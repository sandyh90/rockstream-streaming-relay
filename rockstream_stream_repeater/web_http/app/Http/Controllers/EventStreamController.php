<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\StreamInput;

class EventStreamController extends Controller
{
    public function on_publish(Request $request)
    {
        $stream_db = StreamInput::where(['key_input_stream' => $request->name, 'active_input_stream' => TRUE]);
        if ($stream_db->exists()) {
            $stream_db->update(['is_live' => TRUE]);
            return response('Good', 200)->header('Content-Type', 'text/plain');
        } else {
            return response('No', 400)->header('Content-Type', 'text/plain');
        }
        // file_put_contents(dirname(base_path()) . DIRECTORY_SEPARATOR . 'nginxgetrtmp.txt', json_encode($request->all()));
        // return response('Good', 200)->header('Content-Type', 'text/plain');
    }

    public function on_publish_done(Request $request)
    {
        $stream_db = StreamInput::where(['key_input_stream' => $request->name, 'active_input_stream' => TRUE]);
        if ($stream_db->exists()) {
            $stream_db->update(['is_live' => FALSE]);
        }
        return response('Ended', 200)->header('Content-Type', 'text/plain');
    }
}
