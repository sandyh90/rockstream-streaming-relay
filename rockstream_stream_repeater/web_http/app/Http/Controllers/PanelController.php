<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

use App\Models\StreamInput;

use SimpleXMLElement;
use Carbon\CarbonInterval;

class PanelController extends Controller
{
    public function index()
    {
        return view('panel');
    }

    public function get_stat_rtmp()
    {
        function readableBytes($bytes)
        {
            if ($bytes < 1024) {
                return $bytes . ' b/s';
            } else {
                $i = floor(log($bytes) / log(1024));
                $sizes = array('b/s', 'Kb/s', 'Mb/s', 'Gb/s', 'Tb/s', 'Pb/s', 'Eb/s', 'Zb/s', 'Yb/s');

                return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
            }
        }

        libxml_use_internal_errors(true);
        try {
            libxml_use_internal_errors(true);
            $xml_stat_rtmp_raw = new SimpleXMLElement(config('app.url') . ':' . config('component.nginx_stat_port_rtmp') . '/stat', 0, true);
            $xml_stat_rtmp = json_decode(json_encode($xml_stat_rtmp_raw), TRUE);
            $data = [
                'bandwidth' => readableBytes($xml_stat_rtmp['bw_in']),
                'status' => (!array_key_exists('application', $xml_stat_rtmp['server']) ? FALSE : (count(array_filter($xml_stat_rtmp['server']['application'], 'is_array')) > 1 ? (array_filter(array_column(array_column($xml_stat_rtmp['server']['application'], 'live'), 'nclients'), function ($value) {
                    # if value is not 0 set boolean to true
                    return ($value > 0);
                }) ? TRUE : FALSE) : (array_filter(array_column($xml_stat_rtmp['server']['application']['live'], 'nclients'), function ($value) {
                    # if value is not 0 set boolean to true
                    return ($value != 0);
                }) ? TRUE : FALSE))),
                'uptime' => CarbonInterval::seconds($xml_stat_rtmp['uptime'])->cascade()->forHumans(),

            ];
            return response()->json(['success' => TRUE, 'xml' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => FALSE, 'msg' => 'Get rtmp stat failed.'], 404);
        }
    }

    public function check_stream_preview(Request $request)
    {
        $stream_db = StreamInput::where(['identifier_stream' => $request->input_stream, 'is_live' => TRUE])->first();
        if (!empty($stream_db)) {
            $hls_preview_path = config('app.url') . ':' . config('component.nginx_hls_port_rtmp') . "/{$stream_db->key_input_stream}/index.m3u8";
            try {
                file_get_contents($hls_preview_path);
                return response()->json(['success' => TRUE, 'is_live' => TRUE, 'stream_url' => $hls_preview_path], 200);
            } catch (\Exception $e) {
                return response()->json(['success' => FALSE, 'msg' => 'Check stream preview failed.']);
            }
        } else {
            return response()->json(['success' => TRUE, 'is_live' => FALSE, 'msg' => 'No live stream available.']);
        }
    }

    public function get_stream_key(Request $request)
    {
        $stream_db = StreamInput::where(['identifier_stream' => $request->input_stream])->first();
        if (!empty($stream_db)) {
            $data = [
                'stream_url' => str_replace('http', 'rtmp', config('app.url')) . "/{$stream_db->name_input_stream}",
                'stream_key' => $stream_db->key_input_stream,
            ];
            return response()->json(['success' => TRUE, 'input_stream' => $data], 200);
        } else {
            return response()->json(['success' => FALSE, 'msg' => 'No live stream available.']);
        }
    }

    public function fetch_stream_input(Request $request)
    {
        $responses = [];
        $search_data = $request->search;
        $fetch_stream = StreamInput::where(['active_input_stream' => TRUE]);
        $stream_data = ($search_data == NULL ? $fetch_stream->get(['name_input', 'identifier_stream']) : $fetch_stream->where('name_input', 'like', '%' . $search_data . '%')->get(['name_input', 'identifier_stream']));
        foreach ($stream_data as $data) {
            $responses[] = [
                'id' => $data->identifier_stream,
                'text' => $data->name_input
            ];
        }
        return response()->json($responses);
    }

    public function control_server_panel(Request $request)
    {
        $action = $request->input('action_fetch');
        if ($action == 'power') {
            Artisan::call('nginxrtmp:power');
            $console = Artisan::output();

            return response()->json(['csrftoken' => csrf_token(), 'success' => TRUE, 'msg' => '<div class="alert alert-success"><span class="material-icons me-1">check_circle</span>Switch power Nginx successfuly, ' . $console . '</div>']);
        } elseif ($action == 'disable') {
            $stream_check = StreamInput::where(['active_input_stream' => TRUE]);
            if ($stream_check) {
                # disable stream input in database and restart stream input service to disable stream input on server side
                $stream_check->update(['active_input_stream' => FALSE]);

                # regenerate nginx config file to disable stream input on server side and restart nginx service
                Artisan::call('nginxrtmp:regenconfig');
                $console = Artisan::output();

                return response()->json(['csrftoken' => csrf_token(), 'success' => TRUE, 'msg' => '<div class="alert alert-success"><span class="material-icons me-1">check_circle</span>Disable RTMP input stream successfuly, ' . $console . '</div>']);
            } else {
                return response()->json(['csrftoken' => csrf_token(), 'success' => FALSE, 'msg' => '<div class="alert alert-danger"><span class="material-icons me-1">cancel</span>Disable RTMP input stream failed</div>']);
            }
        } elseif ($action == 'restart') {
            Artisan::call('nginxrtmp:restart');
            $console = Artisan::output();

            return response()->json(['csrftoken' => csrf_token(), 'success' => TRUE, 'msg' => '<div class="alert alert-success"><span class="material-icons me-1">check_circle</span>Restart RTMP input stream successfuly, ' . $console . '</div>']);
        } elseif ($action == 'regenerate') {
            Artisan::call('nginxrtmp:regenconfig');
            $console = Artisan::output();

            return response()->json(['csrftoken' => csrf_token(), 'success' => TRUE, 'msg' => '<div class="alert alert-success"><span class="material-icons me-1">check_circle</span>Regenerate RTMP input stream successfuly, ' . $console . '</div>']);
        } else {
            return response()->json(['csrftoken' => csrf_token(), 'success' => FALSE, 'msg' => '<div class="alert alert-danger"><span class="material-icons me-1">block</span>This action that you select not available</div>']);
        }
    }
}