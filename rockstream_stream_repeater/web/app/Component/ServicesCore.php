<?php

namespace App\Component;

class ServicesCore
{
    public static function fetchDataServer($url_fetch)
    {
        try {
            return [
                'success' => TRUE,
                'content' => json_decode(file_get_contents($url_fetch), true),
            ];
        } catch (\Exception $e) {
            return [
                'success' => FALSE,
                'content' => 'Cannot fetch data from server.',
            ];
        }
    }

    public static function getServices()
    {
        $fetchserver = [
            'twitch' => self::fetchDataServer('https://ingest.twitch.tv/ingests')
        ];
        return [
            'custom' => [
                'id' => 1,
                'name' => 'Custom',
                'code_data' => 'custom',
                'desc' => 'Custom Service',
                'is_multi_server' => FALSE,
                'is_manual_input' => TRUE,
                'server_list' => []
            ],
            'youtube' => [
                'id' => 2,
                'name' => 'YouTube',
                'code_data' => 'youtube',
                'desc' => 'Youtube Service',
                'is_multi_server' => TRUE,
                'is_manual_input' => FALSE,
                'server_list' => [
                    "Primary Server (RTMP)" => "rtmp://a.rtmp.youtube.com/live2",
                    "Secondary Server [Backup] (RTMP)" => "rtmp://b.rtmp.youtube.com/live2",
                    "Primary Server (RTMPS)" => "rtmps://a.rtmps.youtube.com/live2",
                    "Secondary Server [Backup] (RTMPS)" => "rtmps://b.rtmps.youtube.com/live2?backup=1"
                ]
            ],
            'twitch' => [
                'id' => 3,
                'name' => 'Twitch',
                'code_data' => 'twitch',
                'desc' => 'Twitch Service',
                'is_multi_server' => TRUE,
                'is_manual_input' => ($fetchserver['twitch']['success'] == TRUE ? FALSE : TRUE),
                'server_list' => str_replace("/{stream_key}", "", ($fetchserver['twitch']['success'] == TRUE ? array_combine(array_column($fetchserver['twitch']['content']['ingests'], 'name'), array_column($fetchserver['twitch']['content']['ingests'], 'url_template')) :
                    [
                        "Asia: Indonesia, Jakarta (2)" => "rtmp://jkt02.contribute.live-video.net/app/{stream_key}",
                        "Asia: Indonesia, Cikarang Barat (1)" => "rtmp://jkt01.contribute.live-video.net/app/{stream_key}",
                        "Asia: Singapore (1)" => "rtmp://sin01.contribute.live-video.net/app/{stream_key}",
                        "Asia: Singapore (4)" => "rtmp://sin04.contribute.live-video.net/app/{stream_key}",
                        "Asia: Manila, Philippines (1)" => "rtmp://mnl01.contribute.live-video.net/app/{stream_key}",
                        "Asia: Thailand, Bangkok" => "rtmp://bkk.contribute.live-video.net/app/{stream_key}",
                        "Asia: China, Hong Kong (6)" => "rtmp://hkg06.contribute.live-video.net/app/{stream_key}",
                        "Asia: Taiwan, Taipei (1)" => "rtmp://tpe01.contribute.live-video.net/app/{stream_key}",
                        "Asia: Taiwan, Taipei (3)" => "rtmp://tpe03.contribute.live-video.net/app/{stream_key}",
                        "Asia: India, Chennai" => "rtmp://maa01.contribute.live-video.net/app/{stream_key}",
                        "Asia: India, Bangalore (1)" => "rtmp://blr01.contribute.live-video.net/app/{stream_key}",
                        "Oceania: Australia, Sydney (2)" => "rtmp://syd02.contribute.live-video.net/app/{stream_key}",
                        "Oceania: Australia, Sydney (3)" => "rtmp://syd03.contribute.live-video.net/app/{stream_key}",
                        "Asia: South Korea, Seoul (1)" => "rtmp://sel01.contribute.live-video.net/app/{stream_key}",
                        "Asia: South Korea, Seoul (3)" => "rtmp://sel03.contribute.live-video.net/app/{stream_key}",
                        "Asia: South Korea, Seoul (4)" => "rtmp://sel04.contribute.live-video.net/app/{stream_key}",
                        "Asia: India, Mumbai" => "rtmp://bom01.contribute.live-video.net/app/{stream_key}",
                        "Asia: India, Hyderabad (1)" => "rtmp://hyd01.contribute.live-video.net/app/{stream_key}",
                        "Asia: Japan, Tokyo (5)" => "rtmp://tyo05.contribute.live-video.net/app/{stream_key}",
                        "Asia: Japan, Tokyo (3)" => "rtmp://tyo03.contribute.live-video.net/app/{stream_key}",
                        "NA: Canada, Quebec" => "rtmp://ymq03.contribute.live-video.net/app/{stream_key}",
                        "Europe: Czech Republic, Prague" => "rtmp://prg03.contribute.live-video.net/app/{stream_key}",
                        "Europe: Spain, Madrid" => "rtmp://mad02.contribute.live-video.net/app/{stream_key}",
                        "Europe : Finland, Helsinki (3)" => "rtmp://hel03.contribute.live-video.net/app/{stream_key}",
                        "Asia: India, New Delhi" => "rtmp://del01.contribute.live-video.net/app/{stream_key}",
                        "Europe: Poland, Warsaw" => "rtmp://waw.contribute.live-video.net/app/{stream_key}",
                        "Europe : Sweden, Stockholm (4)" => "rtmp://arn04.contribute.live-video.net/app/{stream_key}",
                        "Europe: Sweden, Stockholm (3)" => "rtmp://arn03.contribute.live-video.net/app/{stream_key}",
                        "Europe: Austria, Vienna (2)" => "rtmp://vie02.contribute.live-video.net/app/{stream_key}",
                        "Europe: Germany, Berlin" => "rtmp://ber.contribute.live-video.net/app/{stream_key}",
                        "Europe: Denmark, Copenhagen" => "rtmp://cph.contribute.live-video.net/app/{stream_key}",
                        "Europe: Norway, Oslo" => "rtmp://osl.contribute.live-video.net/app/{stream_key}",
                        "Europe: Germany, Munich (1)" => "rtmp://muc01.contribute.live-video.net/app/{stream_key}",
                        "Europe: Italy, Milan (2)" => "rtmp://mil02.contribute.live-video.net/app/{stream_key}",
                        "Europe: Germany, Dusseldorf (1)" => "rtmp://dus01.contribute.live-video.net/app/{stream_key}",
                        "Europe: Netherlands, Amsterdam (3)" => "rtmp://ams03.contribute.live-video.net/app/{stream_key}",
                        "Europe: Netherlands, Amsterdam (2)" => "rtmp://ams02.contribute.live-video.net/app/{stream_key}",
                        "Europe: France, Marseille (2)" => "rtmp://mrs02.contribute.live-video.net/app/{stream_key}",
                        "Europe: France, Marseille" => "rtmp://mrs.contribute.live-video.net/app/{stream_key}",
                        "Europe: France, Paris (10)" => "rtmp://cdg10.contribute.live-video.net/app/{stream_key}",
                        "Europe: France, Paris (2)" => "rtmp://cdg02.contribute.live-video.net/app/{stream_key}",
                        "Europe: UK, London (4)" => "rtmp://lhr04.contribute.live-video.net/app/{stream_key}",
                        "Europe: UK, London (3)" => "rtmp://lhr03.contribute.live-video.net/app/{stream_key}",
                        "South America: Brazil, Sao Paulo" => "rtmp://sao03.contribute.live-video.net/app/{stream_key}",
                        "Europe: Spain, Madrid (1)" => "rtmp://mad01.contribute.live-video.net/app/{stream_key}",
                        "Europe: Germany, Frankfurt (5)" => "rtmp://fra05.contribute.live-video.net/app/{stream_key}",
                        "Europe: Germany, Frankfurt (2)" => "rtmp://fra02.contribute.live-video.net/app/{stream_key}",
                        "US West: Seattle, WA" => "rtmp://sea.contribute.live-video.net/app/{stream_key}",
                        "US West: Portland, OR" => "rtmp://pdx.contribute.live-video.net/app/{stream_key}",
                        "US West: San Francisco, CA" => "rtmp://sfo.contribute.live-video.net/app/{stream_key}",
                        "US West: San Jose, CA (5)" => "rtmp://sjc05.contribute.live-video.net/app/{stream_key}",
                        "US West: San Jose, CA (2)" => "rtmp://sjc02.contribute.live-video.net/app/{stream_key}",
                        "US West: Los Angeles, CA" => "rtmp://lax.contribute.live-video.net/app/{stream_key}",
                        "US West: Salt Lake City, UT" => "rtmp://slc.contribute.live-video.net/app/{stream_key}",
                        "US West: Phoenix, AZ" => "rtmp://phx.contribute.live-video.net/app/{stream_key}",
                        "US Central: Denver, CO" => "rtmp://den.contribute.live-video.net/app/{stream_key}",
                        "South America: Buenos Aires, Argentina (1)" => "rtmp://bue01.contribute.live-video.net/app/{stream_key}",
                        "South America : chile, Santiago (1)" => "rtmp://scl01.contribute.live-video.net/app/{stream_key}",
                        "US East: Chicago, IL (3)" => "rtmp://ord03.contribute.live-video.net/app/{stream_key}",
                        "US East: Chicago, IL (2)" => "rtmp://ord02.contribute.live-video.net/app/{stream_key}",
                        "South America: Brazil, Rio de Janeiro" => "rtmp://rio.contribute.live-video.net/app/{stream_key}",
                        "NA: Canada, Toronto" => "rtmp://yto.contribute.live-video.net/app/{stream_key}",
                        "US Central: Dallas, TX" => "rtmp://dfw.contribute.live-video.net/app/{stream_key}",
                        "South America: Brazil, Sao Paulo (1)" => "rtmp://sao01.contribute.live-video.net/app/{stream_key}",
                        "US Central: Houston, TX" => "rtmp://hou.contribute.live-video.net/app/{stream_key}",
                        "NA: Mexico, Queretaro (2)" => "rtmp://qro02.contribute.live-video.net/app/{stream_key}",
                        "NA: Mexico, Queretaro (3)" => "rtmp://qro03.contribute.live-video.net/app/{stream_key}",
                        "US East: New York, NY" => "rtmp://jfk.contribute.live-video.net/app/{stream_key}",
                        "US East: Ashburn, VA (5)" => "rtmp://iad05.contribute.live-video.net/app/{stream_key}",
                        "US East: Ashburn, VA (3)" => "rtmp://iad03.contribute.live-video.net/app/{stream_key}",
                        "US East: Atlanta, GA" => "rtmp://atl.contribute.live-video.net/app/{stream_key}",
                        "South America: Brazil, Fortaleza (1)" => "rtmp://for01.contribute.live-video.net/app/{stream_key}",
                        "US East: Miami, FL (5)" => "rtmp://mia05.contribute.live-video.net/app/{stream_key}",
                    ]))
            ],
            'vimeo' => [
                'id' => 4,
                'name' => 'Vimeo',
                'code_data' => 'vimeo',
                'desc' => 'Vimeo Service',
                'is_multi_server' => FALSE,
                'is_manual_input' => FALSE,
                'server_list' => ["Default Vimeo (RTMP)" => "rtmp://rtmp.cloud.vimeo.com/live"]
            ],
            'twitter' => [
                'id' => 5,
                'name' => 'Twitter / Periscope',
                'code_data' => 'twitter',
                'desc' => 'Twitter Service',
                'is_multi_server' => TRUE,
                'is_manual_input' => FALSE,
                'server_list' => [
                    "US West: California" => "rtmp://ca.pscp.tv:80/x",
                    "US West: Oregon" => "rtmp://or.pscp.tv:80/x",
                    "US East: Virginia" => "rtmp://va.pscp.tv:80/x",
                    "South America: Brazil" => "rtmp://br.pscp.tv:80/x",
                    "EU West: France" => "rtmp://fr.pscp.tv:80/x",
                    "EU West: Ireland" => "rtmp://ie.pscp.tv:80/x",
                    "EU Central: Germany" => "rtmp://de.pscp.tv:80/x",
                    "Asia/Pacific: Australia" => "rtmp://au.pscp.tv:80/x",
                    "Asia/Pacific: India" => "rtmp://in.pscp.tv:80/x",
                    "Asia/Pacific: Japan" => "rtmp://jp.pscp.tv:80/x",
                    "Asia/Pacific: Korea" => "rtmp://kr.pscp.tv:80/x",
                    "Asia/Pacific: Singapore" => "rtmp://sg.pscp.tv:80/x"
                ]
            ],
            'steam' => [
                'id' => 6,
                'name' => 'Steam',
                'code_data' => 'steam',
                'desc' => 'Steam Service',
                'is_multi_server' => FALSE,
                'is_manual_input' => FALSE,
                'server_list' => ["Default Steam (RTMP)" => "rtmp://ingest-rtmp.broadcast.steamcontent.com/app"]

            ],
            'telegram' => [
                'id' => 7,
                'name' => 'Telegram',
                'code_data' => 'telegram',
                'desc' => 'Telegram Service',
                'is_multi_server' => FALSE,
                'is_manual_input' => TRUE,
                'server_list' => []

            ],
            'linkedin' => [
                'id' => 8,
                'name' => 'LinkedIn',
                'code_data' => 'linkedin',
                'desc' => 'LinkedIn Service',
                'is_multi_server' => FALSE,
                'is_manual_input' => TRUE,
                'server_list' => []

            ],
            'facebook' => [
                'id' => 9,
                'name' => 'Facebook',
                'code_data' => 'facebook',
                'desc' => 'Facebook Service',
                'is_multi_server' => FALSE,
                'is_manual_input' => FALSE,
                'server_list' => ["Default Facebook (RTMPS)" => "rtmps://rtmp-api.facebook.com:443/rtmp/"]

            ],
        ];
    }

    public static function getServiceIcons($icon_name)
    {
        $iconList = [
            'youtube' => [
                'class' => 'bi bi-youtube',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.007 2.007 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.007 2.007 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31.4 31.4 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.007 2.007 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A99.788 99.788 0 0 1 7.858 2h.193zM6.4 5.209v4.818l4.157-2.408L6.4 5.209z"/>
              </svg>'
            ],
            'twitch' => [
                'class' => 'bi bi-twitch',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M3.857 0 1 2.857v10.286h3.429V16l2.857-2.857H9.57L14.714 8V0H3.857zm9.714 7.429-2.285 2.285H9l-2 2v-2H4.429V1.143h9.142v6.286z"/>
                <path d="M11.857 3.143h-1.143V6.57h1.143V3.143zm-3.143 0H7.571V6.57h1.143V3.143z"/>
              </svg>'
            ],
            'linkedin' => [
                'class' => 'bi bi-linkedin',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>
              </svg>'
            ],
            'vimeo' => [
                'class' => 'bi bi-vimeo',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M15.992 4.204c-.071 1.556-1.158 3.687-3.262 6.393-2.175 2.829-4.016 4.243-5.522 4.243-.933 0-1.722-.861-2.367-2.583L3.55 7.523C3.07 5.8 2.556 4.94 2.007 4.94c-.118 0-.537.253-1.254.754L0 4.724a209.56 209.56 0 0 0 2.334-2.081c1.054-.91 1.845-1.388 2.373-1.437 1.243-.123 2.01.728 2.298 2.553.31 1.968.526 3.19.646 3.666.36 1.631.756 2.446 1.186 2.445.334 0 .836-.53 1.508-1.587.671-1.058 1.03-1.863 1.077-2.415.096-.913-.263-1.37-1.077-1.37a3.022 3.022 0 0 0-1.185.261c.789-2.573 2.291-3.825 4.508-3.756 1.644.05 2.419 1.117 2.324 3.2z"/>
              </svg>'
            ],
            'twitter' => [
                'class' => 'bi bi-twitter',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
              </svg>'
            ],
            'facebook' => [
                'class' => 'bi bi-facebook',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
              </svg>'
            ],
            'steam' => [
                'class' => 'bi bi-steam',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M.329 10.333A8.01 8.01 0 0 0 7.99 16C12.414 16 16 12.418 16 8s-3.586-8-8.009-8A8.006 8.006 0 0 0 0 7.468l.003.006 4.304 1.769A2.198 2.198 0 0 1 5.62 8.88l1.96-2.844-.001-.04a3.046 3.046 0 0 1 3.042-3.043 3.046 3.046 0 0 1 3.042 3.043 3.047 3.047 0 0 1-3.111 3.044l-2.804 2a2.223 2.223 0 0 1-3.075 2.11 2.217 2.217 0 0 1-1.312-1.568L.33 10.333Z"/>
                <path d="M4.868 12.683a1.715 1.715 0 0 0 1.318-3.165 1.705 1.705 0 0 0-1.263-.02l1.023.424a1.261 1.261 0 1 1-.97 2.33l-.99-.41a1.7 1.7 0 0 0 .882.84Zm3.726-6.687a2.03 2.03 0 0 0 2.027 2.029 2.03 2.03 0 0 0 2.027-2.029 2.03 2.03 0 0 0-2.027-2.027 2.03 2.03 0 0 0-2.027 2.027Zm2.03-1.527a1.524 1.524 0 1 1-.002 3.048 1.524 1.524 0 0 1 .002-3.048Z"/>
              </svg>'
            ],
            'telegram' => [
                'class' => 'bi bi-telegram',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.287 5.906c-.778.324-2.334.994-4.666 2.01-.378.15-.577.298-.595.442-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294.26.006.549-.1.868-.32 2.179-1.471 3.304-2.214 3.374-2.23.05-.012.12-.026.166.016.047.041.042.12.037.141-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8.154 8.154 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629.093.06.183.125.27.187.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.426 1.426 0 0 0-.013-.315.337.337 0 0 0-.114-.217.526.526 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09z"/>
              </svg>'
            ],
            'custom' => [
                'class' => 'bi bi-diagram-3-fill',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H14a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 2 7h5.5V6A1.5 1.5 0 0 1 6 4.5v-1zm-6 8A1.5 1.5 0 0 1 1.5 10h1A1.5 1.5 0 0 1 4 11.5v1A1.5 1.5 0 0 1 2.5 14h-1A1.5 1.5 0 0 1 0 12.5v-1zm6 0A1.5 1.5 0 0 1 7.5 10h1a1.5 1.5 0 0 1 1.5 1.5v1A1.5 1.5 0 0 1 8.5 14h-1A1.5 1.5 0 0 1 6 12.5v-1zm6 0a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1a1.5 1.5 0 0 1-1.5-1.5v-1z"/>
              </svg>'
            ],
            'unknown' => [
                'class' => 'bi bi-question-circle',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
              </svg>'
            ]
        ];

        if (isset($iconList[$icon_name]) && in_array($icon_name, array_keys($iconList))) {
            return $iconList[$icon_name]['svg'] . ucfirst($icon_name);
        } else {
            return $iconList['unknown']['svg'] . 'Unknown';
        }
    }
}
