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
            $ffmpeg_folder = Utility::defaultBinDirFolder(config('component.ffmpeg_path'));

            //Remove drive letter from path if present in windows system (e.g. C:\)
            $default_font = preg_replace('/^[A-Z]:/i', '', str_replace('\\', '/', storage_path('app/fonts/' . 'VCR_OSD_MONO_1.001.ttf')));
            # check ffmpeg executable path if not found then exit or else continue
            if (!file_exists($ffmpeg_folder . DIRECTORY_SEPARATOR . 'ffmpeg.exe') || !file_exists($ffmpeg_folder . DIRECTORY_SEPARATOR . 'ffprobe.exe')) {
                $this->fail('FFMpeg or FFProbe binaries does not exist');
            } else {
                $runtime_data = [
                    'bitrate' => (!is_null($this->video['bitrate_stream']) ? $this->video['bitrate_stream'] : 1000),
                    'encoder' => (!is_null($this->video['encoder_type']) ? $this->video['encoder_type'] : 'libx264'),
                    'fps' => (!is_null($this->video['fps_type']) ? $this->video['fps_type'] : 30),
                    'font' => (file_exists($default_font) ? "fontfile='" . $default_font . "'" : ''),
                    'duration_limit' => ($this->video['limit_duration'] == TRUE ? "-t 00:05:00" : '')
                ];
                if (Utility::getInstanceRunByPath((Utility::defaultBinDirFolder(config('component.nginx_path')) . DIRECTORY_SEPARATOR . 'nginx.exe'))) {
                    system(`start "Test Stream FFMpeg" /d"{$ffmpeg_folder}" "ffmpeg.exe" -re -f lavfi -i "smptehdbars=rate={$runtime_data['fps']}:size=1920x1080" -f lavfi -i "sine=frequency=1000:sample_rate=48000" -vf "[in]drawtext=:fontsize=48:fontcolor=white:box=1:boxcolor=black:{$runtime_data['font']}:text='Sorry To Interrupt You, This Is A Test Stream':rate={$runtime_data['fps']}:x=(w-tw)/2:y=(h-lh)/2, drawtext=:fontsize=48:fontcolor=white:box=1:boxcolor=black:{$runtime_data['font']}:text='%{localtime\:%X}':rate={$runtime_data['fps']}:x=(w-tw)/2:y=((h)/2)+25, drawtext=fontsize=48:fontcolor=white:box=1:boxcolor=black:{$runtime_data['font']}:text='%{pts\:hms}':rate={$runtime_data['fps']}:x=(w-tw)/2:y=((h)/2)+70[out]" -f flv -flvflags no_duration_filesize -b:v {$runtime_data['bitrate']}k -c:v {$runtime_data['encoder']} -pix_fmt yuv420p -preset ultrafast -tune zerolatency -crf 28 -g 60 -c:a aac {$runtime_data['duration_limit']} "{$this->video['rtmp_output']}"`, $output);
                    if ($output == 0) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                } else {
                    $this->fail('Nginx service is not running');
                }
            }
        }
    }
}
