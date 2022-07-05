<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use SimpleXMLElement;
use App\Component\Utility;
use Illuminate\Support\Arr;

use Yajra\DataTables\DataTables;

class LiveAnalyticsStatController extends Controller
{
    public function index()
    {
        return view('live_analytics_stat');
    }

    public function get_live_data()
    {
        libxml_use_internal_errors(true);
        try {
            $xml_stat_rtmp_raw = new SimpleXMLElement(config('app.url') . ':' . config('component.nginx_port_rtmp') . '/status/stat', 0, true);
            $xml_stat_rtmp = json_decode(json_encode($xml_stat_rtmp_raw), TRUE);
            $fetchconnection = [];
            $fetchdata = [];
            if (!array_key_exists('application', $xml_stat_rtmp['server'])) {
                $fetchdata[] = [
                    'name_address' => '<span class="bi bi-x-circle me-1"></span>No Data Available',
                    'status' => '<span class="bi bi-x-circle me-1"></span>No Data Available',
                    'clients' => '<span class="bi bi-x-circle me-1"></span>No Data Available',
                    'metadata' => '<span class="bi bi-x-circle me-1"></span>No Data Available',
                    'bandwidth' => '<span class="bi bi-x-circle me-1"></span>No Data Available'
                ];
            } else {
                if (count(array_filter($xml_stat_rtmp['server']['application'], 'is_array')) > 1) {
                    foreach ($xml_stat_rtmp['server']['application'] as $data) {
                        if ((array_key_exists('stream', $data['live']) && count(array_filter($data['live']['stream'], 'is_array')) > 1) && !Arr::isAssoc($data['live']['stream'])) {
                            foreach ($data['live']['stream'] as $key) {
                                $fetchconnection[] = "<li>" . $key['name'] . " [In: " . Utility::getreadableBit($key['bw_in']) . " / Out: " . Utility::getreadableBit($key['bw_out']) . "]</li>";
                            }
                        }
                        $fetchdata[] = [
                            'name_address' => (!array_key_exists('stream', $data['live']) ? $data['name'] : (((count(array_filter($data['live']['stream'], 'is_array')) > 1) && !Arr::isAssoc($data['live']['stream'])) ?
                                '<div>' . $data['name'] . '</div>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target=".live-address-id-' . $data['name'] . '" aria-expanded="false" aria-controls="live-address-id-' . $data['name'] . '"><span class="bi bi-reception-4 me-1"></span>Show Stream Connection</button>
                                <div class="collapse live-address-id-' . $data['name'] . '  my-2">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="text-center">
                                                <tr>
                                                    <th><span class="bi bi-reception-4 me-1"></span>Stream Connection</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <ol>
                                                            ' . implode('', $fetchconnection)  . '
                                                        </ol>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                ' :
                                '<div>' . $data['name'] . '</div>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target=".live-address-id-' . $data['name'] . '" aria-expanded="false" aria-controls="live-address-id-' . $data['name'] . '"><span class="bi bi-reception-4 me-1"></span>Show Stream Connection</button>
                            <div class="collapse live-address-id-' . $data['name'] . '  my-2">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="text-center">
                                            <tr>
                                                <th><span class="bi bi-reception-4 me-1"></span>Stream Connection</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <ol>
                                                        <li>' . $data['live']['stream']['name'] . ' [In: ' . Utility::getreadableBit($data['live']['stream']['bw_in']) . ' / Out: ' . Utility::getreadableBit($data['live']['stream']['bw_out']) . ']</li>
                                                    </ol>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            ')),
                            'clients' => '<span class="bi bi-diagram-3 me-1"></span>' . $data['live']['nclients'],
                            'metadata' => (!array_key_exists('stream', $data['live']) ? '<span class="bi bi-x-circle me-1"></span>No stream available' : (((count(array_filter($data['live']['stream'], 'is_array')) > 1) && !Arr::isAssoc($data['live']['stream'])) ? '<span class="bi bi-exclamation-circle me-1"></span>Disable Due Have Another Stream In Same Input' : '
                                <div class="table-responsive">
                                    <table class="table">
                                    <thead>
                                        <tr>
                                        <th><span class="bi bi-film me-1"></span>Video</th>
                                        <th><span class="bi bi-speaker me-1"></span>Audio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        <td>
                                            <ul>
                                                <li><span class="bi bi-aspect-ratio me-1"></span>Resolution: ' . $data['live']['stream']['meta']['video']['width'] . 'x' . $data['live']['stream']['meta']['video']['height'] . '</li>
                                                <li><span class="bi bi-collection me-1"></span>FPS: ' . $data['live']['stream']['meta']['video']['frame_rate'] . '</li>
                                                <li><span class="bi bi-cpu me-1"></span>Codec: ' . $data['live']['stream']['meta']['video']['codec'] . '</li>
                                            </ul>
                                        </td>
                                        <td>
                                            <ul>
                                                <li><span class="bi bi-bounding-box me-1"></span>Channels: ' . $data['live']['stream']['meta']['audio']['channels'] . '</li>
                                                <li><span class="bi bi-soundwave me-1"></span>Sample Rate: ' . $data['live']['stream']['meta']['audio']['sample_rate'] . '</li>
                                                <li><span class="bi bi-cpu me-1"></span>Codec: ' . $data['live']['stream']['meta']['audio']['codec'] . '</li>
                                            </ul>
                                        </td>
                                        </tr>
                                    </table>
                                </div>
                        ')),
                            'bandwidth' => (!array_key_exists('stream', $data['live']) ? '<span class="bi bi-x-circle me-1"></span>No stream available' : (((count(array_filter($data['live']['stream'], 'is_array')) > 1) && !Arr::isAssoc($data['live']['stream'])) ? '<span class="bi bi-exclamation-circle me-1"></span>Disable Due Have Another Stream In Same Input' : ('<span class="bi bi-speedometer2 me-1"></span>' . 'In: ' . Utility::getreadableBit($data['live']['stream']['bw_in']) . ' / Out: ' . Utility::getreadableBit($data['live']['stream']['bw_out']))))
                        ];
                    }
                } else {
                    if ((array_key_exists('stream', $xml_stat_rtmp['server']['application']['live']) && count(array_filter($xml_stat_rtmp['server']['application']['live']['stream'], 'is_array')) > 1) && !Arr::isAssoc($xml_stat_rtmp['server']['application']['live']['stream'])) {
                        foreach ($xml_stat_rtmp['server']['application']['live']['stream'] as $key) {
                            $fetchconnection[] = "<li>" . $key['name'] . " [In: " . Utility::getreadableBit($key['bw_in']) . " / Out: " . Utility::getreadableBit($key['bw_out']) . "]</li>";
                        }
                    }
                    $fetchdata[] = [
                        'name_address' => (!array_key_exists('stream', $xml_stat_rtmp['server']['application']['live']) ? $xml_stat_rtmp['server']['application']['name'] : (((count(array_filter($xml_stat_rtmp['server']['application']['live']['stream'], 'is_array')) > 1) && !Arr::isAssoc($xml_stat_rtmp['server']['application']['live']['stream'])) ?
                            '<div>' . $xml_stat_rtmp['server']['application']['name'] . '</div>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target=".live-address-id-' . $xml_stat_rtmp['server']['application']['name'] . '" aria-expanded="false" aria-controls="live-address-id-' . $xml_stat_rtmp['server']['application']['name'] . '"><span class="bi bi-reception-4 me-1"></span>Show Stream Connection</button>
                        <div class="collapse live-address-id-' . $xml_stat_rtmp['server']['application']['name'] . '  my-2">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="text-center">
                                        <tr>
                                            <th><span class="bi bi-reception-4 me-1"></span>Stream Connection</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <ol>
                                                    ' . implode('', $fetchconnection)  . '
                                                </ol>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        ' :
                            '<div>' . $xml_stat_rtmp['server']['application']['name'] . '</div>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target=".live-address-id-' . $xml_stat_rtmp['server']['application']['name'] . '" aria-expanded="false" aria-controls="live-address-id-' . $xml_stat_rtmp['server']['application']['name'] . '"><span class="bi bi-reception-4 me-1"></span>Show Stream Connection</button>
                            <div class="collapse live-address-id-' . $xml_stat_rtmp['server']['application']['name'] . '  my-2">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="text-center">
                                            <tr>
                                                <th><span class="bi bi-reception-4 me-1"></span>Stream Connection</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <ol>
                                                        <li>' . $xml_stat_rtmp['server']['application']['live']['stream']['name'] . ' [In: ' . Utility::getreadableBit($xml_stat_rtmp['server']['application']['live']['stream']['bw_in']) . ' / Out: ' . Utility::getreadableBit($xml_stat_rtmp['server']['application']['live']['stream']['bw_out']) . ']</li>
                                                    </ol>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            ')),
                        'clients' => '<span class="bi bi-diagram-3 me-1"></span>' . $xml_stat_rtmp['server']['application']['live']['nclients'],
                        'metadata' => (!array_key_exists('stream', $xml_stat_rtmp['server']['application']['live']) ? '<span class="bi bi-x-circle me-1"></span>No stream available' : (((count(array_filter($xml_stat_rtmp['server']['application']['live']['stream'], 'is_array')) > 1) && !Arr::isAssoc($xml_stat_rtmp['server']['application']['live']['stream'])) ? '<span class="bi bi-exclamation-circle me-1"></span>Disable Due Have Another Stream In Same Input' : '
                            <div class="table-responsive">
                                <table class="table">
                                <thead>
                                    <tr>
                                    <th><span class="bi bi-film me-1"></span>Video</th>
                                    <th><span class="bi bi-speaker me-1"></span>Audio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td>
                                        <ul>
                                            <li><span class="bi bi-aspect-ratio me-1"></span>Resolution: ' . $xml_stat_rtmp['server']['application']['live']['stream']['meta']['video']['width'] . 'x' . $xml_stat_rtmp['server']['application']['live']['stream']['meta']['video']['height'] . '</li>
                                            <li><span class="bi bi-collection me-1"></span>FPS: ' . $xml_stat_rtmp['server']['application']['live']['stream']['meta']['video']['frame_rate'] . '</li>
                                            <li><span class="bi bi-cpu me-1"></span>Codec: ' . $xml_stat_rtmp['server']['application']['live']['stream']['meta']['video']['codec'] . '</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <ul>
                                            <li><span class="bi bi-bounding-box me-1"></span>Channels: ' . $xml_stat_rtmp['server']['application']['live']['stream']['meta']['audio']['channels'] . '</li>
                                            <li><span class="bi bi-soundwave me-1"></span>Sample Rate: ' . $xml_stat_rtmp['server']['application']['live']['stream']['meta']['audio']['sample_rate'] . '</li>
                                            <li><span class="bi bi-cpu me-1"></span>Codec: ' . $xml_stat_rtmp['server']['application']['live']['stream']['meta']['audio']['codec'] . '</li>
                                        </ul>
                                    </td>
                                    </tr>
                                </table>
                            </div>
                        ')),
                        'bandwidth' => (!array_key_exists('stream', $xml_stat_rtmp['server']['application']['live']) ? '<span class="bi bi-x-circle me-1"></span>No stream available' : (((count(array_filter($xml_stat_rtmp['server']['application']['live']['stream'], 'is_array')) > 1) && !Arr::isAssoc($xml_stat_rtmp['server']['application']['live']['stream'])) ? '<span class="bi bi-x-circle me-1"></span>Disable Due Have Another Stream In Same Input' : ('<span class="bi bi-speedometer2 me-1"></span>' . 'In: ' . Utility::getreadableBit($xml_stat_rtmp['server']['application']['live']['stream']['bw_in']) . ' / Out: ' . Utility::getreadableBit($xml_stat_rtmp['server']['application']['live']['stream']['bw_out']))))
                    ];
                }
            }
        } catch (\Exception $e) {
            $fetchdata[] = [
                'name_address' => '<div><span class="bi bi-x-circle me-1"></span> No Data Available</div>
                <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target=".error-show-data" aria-expanded="false" aria-controls="error-show-data"><span class="bi bi-exclamation-circle me-1"></span>Show Error Code</button>
                <div class="collapse error-show-data my-2">
                    <code>' . $e->getMessage() . '</code>
                </div>
                ',
                'status' => '<span class="bi bi-x-circle me-1"></span>No Data Available',
                'clients' => '<span class="bi bi-x-circle me-1"></span>No Data Available',
                'metadata' => '<span class="bi bi-x-circle me-1"></span>No Data Available',
                'bandwidth' => '<span class="bi bi-x-circle me-1"></span>No Data Available'
            ];
        }
        return DataTables::of($fetchdata)
            ->addIndexColumn()
            ->rawColumns(['name_address', 'clients', 'metadata', 'bandwidth'])
            ->make(true);
    }
}
