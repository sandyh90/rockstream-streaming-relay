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
                        $ffmpeg_folder = ((AppInterfaces::getsetting('IS_CUSTOM_FFMPEG_BINARY') == TRUE && !empty(AppInterfaces::getsetting('FFMPEG_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('FFMPEG_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('ffmpeg'));

                        # check ffmpeg executable path if not found then exit or else continue
                        if (!file_exists($ffmpeg_folder . DIRECTORY_SEPARATOR . 'ffmpeg.exe') || !file_exists($ffmpeg_folder . DIRECTORY_SEPARATOR . 'ffprobe.exe')) {
                            $this->fail('FFMpeg or FFProbe binaries does not exist');
                        } else {
                            if (Utility::getInstanceRunByPath((((AppInterfaces::getsetting('IS_CUSTOM_NGINX_BINARY') == TRUE && !empty(AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('nginx')) . DIRECTORY_SEPARATOR . 'nginx.exe'), 'nginx.exe')['found_process']) {

                                $log = new Logger('FFmpeg_Streaming');
                                $log->pushHandler(new StreamHandler(storage_path('logs/ffmpeg') . DIRECTORY_SEPARATOR . 'ffmpeg-streaming.log')); // path to log file

                                $ffprobe = \FFMpeg\FFProbe::create([
                                    'ffprobe.binaries' => $ffmpeg_folder . DIRECTORY_SEPARATOR . 'ffprobe.exe'
                                ], $log);
                                if ($ffprobe->isValid($premiereVideo->video_path)) {
                                    $premiereVideo->update([
                                        'is_premiere' => TRUE
                                    ]);


                                    $ffmpeg = FFMpeg::create([
                                        'ffmpeg.binaries'  => $ffmpeg_folder . DIRECTORY_SEPARATOR . 'ffmpeg.exe',
                                        'ffprobe.binaries' => $ffmpeg_folder . DIRECTORY_SEPARATOR . 'ffprobe.exe',
                                        'timeout'          => 3600, // The timeout for the underlying process
                                        'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
                                        'temporary_directory' => storage_path('logs/ffmpeg'), // The directory where FFMpeg puts its temporary files
                                    ], $log);
                                    $format = new \FFMpeg\Format\Video\X264('aac', 'libx264');
                                    $format->setPasses(1); // Set the number of times that FFMpeg will try to encode the video
                                    $format->setKiloBitrate((!is_null($this->video['bitrate_stream']) ? $this->video['bitrate_stream'] : 1000)); // Set the bitrate of the video

                                    // Get the framerate of the video
                                    $getfps = $ffprobe->streams($premiereVideo->video_path)->videos()->first()->get('r_frame_rate');

                                    // Check if use encoder is set or not and set other parameters
                                    $format->setAdditionalParameters(!is_null($this->video['encoder_type']) ? ['-r', $getfps, '-c:v', $this->video['encoder_type'], '-f', 'flv', '-flvflags', 'no_duration_filesize'] : ['-r', $getfps, '-f', 'flv', '-flvflags', 'no_duration_filesize']);

                                    /**
                                     * Encode video from source to destination via rtmp output.
                                     * and check if use countdown or not and check if countdown video is available
                                     */

                                    if (($this->video['custom_countdown']['use_custom'] == FALSE ? !file_exists(storage_path('app/' . 'Default_Rockstream_Countdown_3_Min.mp4')) : !file_exists($this->video['custom_countdown']['custom_countdown_video_path'])) || $this->video['use_countdown'] == FALSE) {
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
                                        $default_video_countdown = storage_path('app/' . 'Default_Rockstream_Countdown_3_Min.mp4');
                                        $check_custom_countdown = ($this->video['custom_countdown']['use_custom'] == TRUE ? (!is_null($this->video['custom_countdown']['custom_countdown_video_path']) ? $this->video['custom_countdown']['custom_countdown_video_path'] : $default_video_countdown) : $default_video_countdown);
                                        $advancedMedia = $ffmpeg->openAdvanced([$check_custom_countdown, $premiereVideo->video_path]);
                                        $advancedMedia->filters()
                                            // Force the video resolution to be 1920x1080.
                                            ->custom('[0:v]settb=AVTB,fps=' . $getfps . ',scale=' . $customFormatResolutionAdvanced . ':force_original_aspect_ratio=decrease,pad=' . $customFormatResolutionAdvanced . ':(ow-iw)/2:(oh-ih)/2,setsar=1[v0];[1:v]settb=AVTB,fps=' . $getfps . ',scale=' . $customFormatResolutionAdvanced . ':force_original_aspect_ratio=decrease,pad=' . $customFormatResolutionAdvanced . ':(ow-iw)/2:(oh-ih)/2,setsar=1[v1];[v0] [0:a] [v1] [1:a]', 'concat=n=2:v=1:a=1', '[v] [a]');
                                        $advancedMedia
                                            ->map(['[v]', '[a]'], $format, $this->video['rtmp_output'])
                                            ->save();
                                    }

                                    $premiereVideo->update([
                                        'is_premiere' => FALSE
                                    ]);
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
        $premiereVideo = PremiereVideo::where('id', $this->video['id'])->first();
        $premiereVideo->update([
            'is_premiere' => FALSE
        ]);
        $this->fail('Premiere Video Broadcast failed');
    }
}
