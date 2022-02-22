<?php

namespace App\Jobs;

use App\Models\PremiereVideo;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PremiereVideoBroadcast implements ShouldQueue, ShouldBeUnique, ShouldBeUniqueUntilProcessing
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
        if (($this->video['id'] == NULL) || ($this->video['rtmp_output'] == NULL)) {
            $this->fail('Please provide a valid ID or RTMP Output for premiere video broadcast');
        } else {
            $premiereVideo = PremiereVideo::where('id', $this->video['id'])->first();

            # check if premiere video is valid
            if (!file_exists($premiereVideo->video_path)) {
                $this->fail('Video file does not exist');
            } else {
                # check if video file is active or not
                if ($premiereVideo->active_premiere_video != TRUE) {
                    $this->delete();
                } else {
                    $ffmpeg_folder = (dirname(base_path()) . DIRECTORY_SEPARATOR . config('component.ffmpeg_path'));

                    # check ffmpeg executable path if not found then exit or else continue
                    if (!file_exists($ffmpeg_folder . DIRECTORY_SEPARATOR . 'ffmpeg.exe') || !file_exists($ffmpeg_folder . DIRECTORY_SEPARATOR . 'ffprobe.exe')) {
                        $this->fail('FFMpeg or FFProbe binaries does not exist');
                    } else {
                        $premiereVideo->update([
                            'is_premiere' => TRUE
                        ]);

                        system('start "Premiere Video Stream FFMpeg" /d"' . $ffmpeg_folder . '" "ffmpeg.exe" -y -i "' . $premiereVideo->video_path . '" -threads 12 -vcodec libx264 -acodec aac -b:v ' . $this->video['bitrate_stream'] . 'k -refs 6 -coder 1 -b:a 128k -f flv -flvflags no_duration_filesize "' . $this->video['rtmp_output'] . '"', $output);

                        $premiereVideo->update([
                            'is_premiere' => FALSE
                        ]);
                    }
                }
            }
        }
    }


    public function failed(\Exception $e)
    {
        info('Premiere Video Broadcast failed');
        $premiereVideo = PremiereVideo::where('id', $this->video['id'])->first();
        $premiereVideo->update([
            'is_premiere' => FALSE
        ]);
    }
}
