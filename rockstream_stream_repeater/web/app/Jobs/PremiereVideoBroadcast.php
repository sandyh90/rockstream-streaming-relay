<?php

namespace App\Jobs;

use App\Models\PremiereVideo;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use FFMpeg\FFMpeg;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Carbon\Carbon;

use App\Component\Utility;
use App\Component\Facades\Facade\AppInterfacesFacade as AppInterfaces;

class PremiereVideoBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $video;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data = NULL)
    {
        $this->video = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (($this->video['id_video'] == NULL) || ($this->video['rtmp_output'] == NULL)) {
            $this->fail('Please provide a valid ID or RTMP Output for premiere video broadcast');
        } else {
            $premiere_db = PremiereVideo::where(['id' => $this->video['id_video'], 'active_premiere_video' => TRUE]);
            $premiereVideo = $premiere_db->first();

            if ($premiere_db->exists()) {
                # check if premiere video is valid
                if (!file_exists($premiereVideo->video_path)) {
                    $this->fail('Video file does not exist');
                } else {
                    # check if video file is active or not
                    if ($premiereVideo->active_premiere_video != TRUE) {
                        $this->delete();
                    } else {
                        $binaryProc = [
                            'nginxBinName' => 'nginx.exe',
                            'ffmpegBinName' => 'ffmpeg.exe',
                            'ffprobeBinName' => 'ffprobe.exe',
                            'nginxPath' => ((AppInterfaces::getsetting('IS_CUSTOM_NGINX_BINARY') == TRUE && !empty(AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('nginx')),
                            'ffmpegPath' => ((AppInterfaces::getSetting('IS_CUSTOM_FFMPEG_BINARY') == TRUE && !empty(AppInterfaces::getSetting('FFMPEG_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('FFMPEG_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('ffmpeg')),
                            'ffprobePath' => ((AppInterfaces::getSetting('IS_CUSTOM_FFPROBE_BINARY') == TRUE && !empty(AppInterfaces::getSetting('FFPROBE_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('FFPROBE_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('ffmpeg'))
                        ];

                        # check ffmpeg executable path if not found then exit or else continue
                        if (!file_exists($binaryProc['ffmpegPath'] . DIRECTORY_SEPARATOR . $binaryProc['ffmpegBinName']) || !file_exists($binaryProc['ffprobePath'] . DIRECTORY_SEPARATOR . $binaryProc['ffprobeBinName'])) {
                            $this->fail('FFMpeg or FFProbe binaries does not exist');
                        } else {
                            //Check nginx process is running or not
                            if (Utility::getInstanceRunByPath($binaryProc['nginxPath'] . DIRECTORY_SEPARATOR . $binaryProc['nginxBinName'], $binaryProc['nginxBinName'])['found_process']) {

                                $log = new Logger('FFmpeg_Streaming');
                                $log->pushHandler(new StreamHandler(storage_path('logs/ffmpeg') . DIRECTORY_SEPARATOR . 'ffmpeg-streaming.log')); // path to log file

                                $ffprobe = \FFMpeg\FFProbe::create([
                                    'ffprobe.binaries' => $binaryProc['ffprobePath'] . DIRECTORY_SEPARATOR . $binaryProc['ffprobeBinName']
                                ], $log);
                                if ($ffprobe->isValid($premiereVideo->video_path)) {
                                    $premiereVideo->update([
                                        'is_premiere' => TRUE
                                    ]);


                                    $ffmpeg = FFMpeg::create([
                                        'ffmpeg.binaries'  => $binaryProc['ffmpegPath'] . DIRECTORY_SEPARATOR . $binaryProc['ffmpegBinName'],
                                        'ffprobe.binaries' => $binaryProc['ffprobePath'] . DIRECTORY_SEPARATOR . $binaryProc['ffprobeBinName'],
                                        'timeout'          => 3600, // The timeout for the underlying process
                                        'ffmpeg.threads'   => 5,   // The number of threads that FFMpeg should use
                                        'temporary_directory' => storage_path('logs/ffmpeg'), // The directory where FFMpeg puts its temporary files
                                    ], $log);
                                    $format = new \FFMpeg\Format\Video\X264('aac', 'libx264');
                                    $format->setPasses(1); // Set the number of times that FFMpeg will try to encode the video
                                    $format->setKiloBitrate((!is_null($this->video['bitrate_stream']) ? $this->video['bitrate_stream'] : 1000)); // Set the bitrate of the video

                                    // Get the framerate of the video
                                    $getfps = $ffprobe->streams($premiereVideo->video_path)->videos()->first()->get('r_frame_rate');

                                    // Check if use encoder is set or not and set other parameters
                                    $format->setAdditionalParameters(!is_null($this->video['encoder_type']) ? ['-refs', '0', '-r', $getfps, '-c:v', $this->video['encoder_type'], '-f', 'flv', '-flvflags', 'no_duration_filesize'] : ['-refs', '0', '-r', $getfps, '-f', 'flv', '-flvflags', 'no_duration_filesize']);

                                    // Show progressbar for transcoding video
                                    $format->on('progress', function ($video, $format, $percentage, $duration) {
                                        $humanDuration = Carbon::now()->addSeconds($duration)->diffForHumans(0, true, false, 2);
                                        echo sprintf("\rTranscoding Video (%s%%) - Estimated Left: %s", $percentage, $humanDuration);
                                    });

                                    /**
                                     * Encode video from source to destination via rtmp output.
                                     * and check if use countdown or not and check if countdown video is available
                                     */

                                    if (!file_exists($this->video['countdown_video_path']) || $this->video['use_countdown'] == FALSE) {
                                        // Check resolution video type
                                        if (!is_null($this->video['type_resolution_size'])) {
                                            if ($this->video['type_resolution_size'] == 'locked_resolution') {
                                                $customFormatResolutionSingle = ['width' => 1920, 'height' => 1080];
                                            } elseif ($this->video['type_resolution_size'] == 'follow_resolution') {
                                                $customFormatResolutionSingle = ['width' => $ffprobe->streams($premiereVideo->video_path)->videos()->first()->get('width'), 'height' => $ffprobe->streams($premiereVideo->video_path)->videos()->first()->get('height')];
                                            } elseif ($this->video['type_resolution_size'] == 'custom_resolution') {
                                                $customFormatResolutionSingle = ['width' => $this->video['custom_resolution']['width'], 'height' => $this->video['custom_resolution']['height']];
                                            } else {
                                                $customFormatResolutionSingle = ['width' => 1920, 'height' => 1080];
                                            }
                                        } else {
                                            $customFormatResolutionSingle = ['width' => 1920, 'height' => 1080];
                                        }

                                        // Process single video media
                                        $singleMedia = $ffmpeg->open($premiereVideo->video_path);
                                        $singleMedia->filters()->resize(new \FFMpeg\Coordinate\Dimension($customFormatResolutionSingle['width'], $customFormatResolutionSingle['height']))->pad(new \FFMpeg\Coordinate\Dimension($customFormatResolutionSingle['width'], $customFormatResolutionSingle['height']));
                                        $singleMedia->save($format, $this->video['rtmp_output']);
                                    } else {
                                        if (!is_null($this->video['type_resolution_size'])) {
                                            if ($this->video['type_resolution_size'] == 'locked_resolution') {
                                                $customFormatResolutionAdvanced = '1920:1080';
                                            } elseif ($this->video['type_resolution_size'] == 'follow_resolution') {
                                                $customFormatResolutionAdvanced = ($ffprobe->streams($premiereVideo->video_path)->videos()->first()->get('width') . ':' . $ffprobe->streams($premiereVideo->video_path)->videos()->first()->get('height'));
                                            } elseif ($this->video['type_resolution_size'] == 'custom_resolution') {
                                                $customFormatResolutionAdvanced = ($this->video['custom_resolution']['width'] . ':' . $this->video['custom_resolution']['height']);
                                            } else {
                                                $customFormatResolutionAdvanced = '1920:1080';
                                            }
                                        } else {
                                            $customFormatResolutionAdvanced = '1920:1080';
                                        }

                                        // Process with custom filter and countdown video
                                        $file_countdown = $this->video['countdown_video_path'];
                                        $advancedMedia = $ffmpeg->openAdvanced([$file_countdown, $premiereVideo->video_path]);
                                        $advancedMedia->filters()
                                            // Force the video resolution to be custom resolution selector.
                                            ->custom('[0:v]settb=AVTB,fps=' . $getfps . ',scale=' . $customFormatResolutionAdvanced . ':force_original_aspect_ratio=decrease,pad=' . $customFormatResolutionAdvanced . ':(ow-iw)/2:(oh-ih)/2,setsar=1[v0];[1:v]settb=AVTB,fps=' . $getfps . ',scale=' . $customFormatResolutionAdvanced . ':force_original_aspect_ratio=decrease,pad=' . $customFormatResolutionAdvanced . ':(ow-iw)/2:(oh-ih)/2,setsar=1[v1];[v0] [0:a] [v1] [1:a]', 'concat=n=2:v=1:a=1', '[v] [a]');
                                        $advancedMedia
                                            ->map(['[v]', '[a]'], $format, $this->video['rtmp_output'])
                                            ->save();
                                    }

                                    $premiereVideo->update([
                                        'is_premiere' => FALSE
                                    ]);

                                    echo "\n\nPremiering video finished\n\n";
                                } else {
                                    $this->fail('Video file is not valid');
                                }
                            } else {
                                $this->fail('Nginx service is not running');
                            }
                        }
                    }
                }
            } else {
                $this->fail('Premiere video does not exist');
            }
        }
    }


    public function failed(\Exception $e)
    {
        PremiereVideo::where('id', $this->video['id_video'])->update([
            'is_premiere' => FALSE
        ]);
        $this->fail('Premiere Video Broadcast failed');
    }
}
