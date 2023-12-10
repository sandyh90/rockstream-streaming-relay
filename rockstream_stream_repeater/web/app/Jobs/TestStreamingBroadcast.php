<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Component\Utility;
use App\Component\Facades\Facade\AppInterfacesFacade as AppInterfaces;

class TestStreamingBroadcast implements ShouldQueue, ShouldBeUnique, ShouldBeUniqueUntilProcessing
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
        if (($this->video['rtmp_output'] == NULL)) {
            $this->fail('Please provide a valid RTMP Output for premiere video broadcast');
        } else {
            $binaryProc = [
                'nginxBinName' => 'nginx.exe',
                'ffmpegBinName' => 'ffmpeg.exe',
                'ffprobeBinName' => 'ffprobe.exe',
                'nginxPath' => ((AppInterfaces::getsetting('IS_CUSTOM_NGINX_BINARY') == TRUE && !empty(AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('nginx')),
                'ffmpegPath' => ((AppInterfaces::getSetting('IS_CUSTOM_FFMPEG_BINARY') == TRUE && !empty(AppInterfaces::getSetting('FFMPEG_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('FFMPEG_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('ffmpeg')),
                'ffprobePath' => ((AppInterfaces::getSetting('IS_CUSTOM_FFPROBE_BINARY') == TRUE && !empty(AppInterfaces::getSetting('FFPROBE_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('FFPROBE_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('ffmpeg'))
            ];

            // Check what os is using
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                //Remove drive letter from path if present in windows system (e.g. C:\)
                $default_font = preg_replace('/^[A-Z]:/i', '', str_replace('\\', '/', storage_path('app/fonts/' . 'VCR_OSD_MONO_1.001.ttf')));
            } else {
                $default_font = storage_path('app/fonts/' . 'VCR_OSD_MONO_1.001.ttf');
            }

            echo ("\n\nCheck Font Path: {$default_font}\n\n");
            # check ffmpeg executable path if not found then exit or else continue
            if (!file_exists($binaryProc['ffmpegPath'] . DIRECTORY_SEPARATOR . $binaryProc['ffmpegBinName']) || !file_exists($binaryProc['ffprobePath'] . DIRECTORY_SEPARATOR . $binaryProc['ffprobeBinName'])) {
                $this->fail('FFMpeg or FFProbe binaries does not exist');
            } else {
                $runtime_data = [
                    'bitrate' => (!is_null($this->video['bitrate_stream']) ? $this->video['bitrate_stream'] : 1000),
                    'encoder' => (!is_null($this->video['encoder_type']) ? $this->video['encoder_type'] : 'libx264'),
                    'fps' => (!is_null($this->video['fps_type']) ? $this->video['fps_type'] : 30),
                    'font' => (file_exists($default_font) ? "fontfile='" . $default_font . "'" : ''),
                    'duration_limit' => ($this->video['limit_duration'] == TRUE ? "-t 00:05:00" : '')
                ];

                //Check nginx process is running or not
                if (Utility::getInstanceRunByPath($binaryProc['nginxPath'] . DIRECTORY_SEPARATOR . $binaryProc['nginxBinName'], $binaryProc['nginxBinName'])['found_process']) {

                    // Check what os is using
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        system(`start "Test Stream FFMpeg" /d"{$binaryProc['ffmpegPath']}" "{$binaryProc['ffmpegBinName']}" -re -f lavfi -i "smptehdbars=rate={$runtime_data['fps']}:size=1920x1080" -f lavfi -i "sine=frequency=1000:sample_rate=48000" -vf "[in]drawtext=:fontsize=48:fontcolor=white:box=1:boxcolor=black:{$runtime_data['font']}:text='Sorry To Interrupt You, This Is A Test Stream':rate={$runtime_data['fps']}:x=(w-tw)/2:y=(h-lh)/2, drawtext=:fontsize=48:fontcolor=white:box=1:boxcolor=black:{$runtime_data['font']}:text='%{localtime\:%X}':rate={$runtime_data['fps']}:x=(w-tw)/2:y=((h)/2)+25, drawtext=fontsize=48:fontcolor=white:box=1:boxcolor=black:{$runtime_data['font']}:text='%{pts\:hms}':rate={$runtime_data['fps']}:x=(w-tw)/2:y=((h)/2)+70[out]" -f flv -flvflags no_duration_filesize -b:v {$runtime_data['bitrate']}k -c:v {$runtime_data['encoder']} -pix_fmt yuv420p -crf 28 -g 60 -c:a aac {$runtime_data['duration_limit']} "{$this->video['rtmp_output']}"`, $result);
                    } else {
                        system(`{$binaryProc['ffmpegPath']}/{$binaryProc['ffmpegBinName']} -re -f lavfi -i 'smptehdbars=rate={$runtime_data['fps']}:size=1920x1080' -f lavfi -i 'sine=frequency=1000:sample_rate=48000' -vf \"[in]drawtext=:fontsize=48:fontcolor=white:box=1:boxcolor=black:{$runtime_data['font']}:text='Sorry To Interrupt You, This Is A Test Stream':rate={$runtime_data['fps']}:x=(w-tw)/2:y=(h-lh)/2, drawtext=:fontsize=48:fontcolor=white:box=1:boxcolor=black:{$runtime_data['font']}:text='%{localtime\\:%X}':rate={$runtime_data['fps']}:x=(w-tw)/2:y=((h)/2)+25, drawtext=fontsize=48:fontcolor=white:box=1:boxcolor=black:{$runtime_data['font']}:text='%{pts\\:hms}':rate={$runtime_data['fps']}:x=(w-tw)/2:y=((h)/2)+70[out]\" -f flv -flvflags no_duration_filesize -b:v {$runtime_data['bitrate']}k -c:v {$runtime_data['encoder']} -pix_fmt yuv420p -crf 28 -g 60 -c:a aac {$runtime_data['duration_limit']} "{$this->video['rtmp_output']}"`, $result);
                    }
                } else {
                    $this->fail('Nginx service is not running');
                }
            }
        }
    }
}
