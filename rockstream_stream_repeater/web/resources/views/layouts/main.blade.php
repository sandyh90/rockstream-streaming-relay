<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - RockStream</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    @yield('head-content')

    <!-- Addons CSS Module -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/datatables/dataTables.bootstrap5.min.css') }}">
    <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Addons Javascript Module [First Start] -->
    <script src="{{ asset('assets/js/toggle-color-mode.js')}}"></script>
    <script defer src="{{ asset('assets/vendor/alpine.js/cdn.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>

</head>

<body class="d-flex flex-column min-vh-100">
    <div class="wrapper flex-grow-1">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <button class="btn btn-outline" type="button" data-bs-toggle="offcanvas"
                    data-bs-target=".sidebar-toggle">
                    <span class="bi bi-list fs-3"></span>
                </button>
                <a class="navbar-brand" href="{{ route('home') }}"><img
                        src="{{ asset('assets/img/rockstream-brand.png') }}" height="35" width="205"
                        alt="RockStream Brand Logo"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown mx-1">
                            <button class="btn text-reset dropdown-toggle indicator-rtmp" data-bs-toggle="dropdown"
                                aria-expanded="false" type="button">
                                Status Streaming: <span class="status-stream badge bg-secondary"><span
                                        class="bi bi-arrow-down-up me-1"></span>
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end text-small">
                                <div class="dropdown-item-text indicator-rtmp"><span
                                        class="bi bi-speedometer2 me-1"></span>Bandwidth:
                                    <span class="bandwidth">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </span>
                                </div>
                                <div class="dropdown-item-text indicator-rtmp"><span
                                        class="bi bi-arrow-down-up me-1"></span>Bytes:
                                    <span class="bytes-in-out">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </span>
                                </div>
                                <div class="dropdown-item-text indicator-rtmp"><span
                                        class="bi bi-clock me-1"></span>Uptime:
                                    <span class="uptime">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="nav-item dropdown mx-1">
                            <button class="btn text-reset dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false" role="button">
                                <span class="bi bi-sun theme-icon-active" data-theme-icon-active="bi-sun"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="dropdown-item" role="button" data-bs-theme-value="light"><span
                                        class="bi bi-sun me-1" data-theme-icon="bi-sun"></span>Light</li>
                                <li class="dropdown-item" role="button" data-bs-theme-value="dark"><span
                                        class="bi bi-moon me-1" data-theme-icon="bi-moon"></span>Dark</li>
                                <li class="dropdown-item" role="button" data-bs-theme-value="auto">
                                    <span class="bi bi-circle-half me-1" data-theme-icon="bi-circle-half"></span>Auto
                                </li>
                            </ul>
                        </div>
                        <div class="nav-item dropdown mx-1">
                            <button class="btn text-reset dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false" type="button">Hello,<span class="ms-1">{{
                                    \Str::of(Auth::user()->name)->limit(13); }}</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end text-small">
                                <div class="dropdown-item-text text-center small">Logged in
                                    {{\Carbon\Carbon::createFromTimeStamp(strtotime(Auth::user()->last_login))->diffForHumans()}}
                                </div>
                                <div class="dropdown-item text-reset" role="button" data-bs-toggle="modal"
                                    data-bs-target=".modal-about-display">
                                    <span class="bi bi-info-circle me-1"></span>About
                                </div>
                                <hr class="dropdown-divider">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><span
                                            class="bi bi-box-arrow-right me-1"></span>Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="offcanvas offcanvas-start sidebar-toggle sidebar" tabindex="-1" data-bs-backdrop="true">
            <div class="offcanvas-header">
                <div class="brand">
                    <img src="{{ asset('assets/img/rockstream-brand.png') }}" height="50%" width="50%"
                        alt="RockStream Brand Logo">
                </div>
                <button type="button" class="btn btn-outline sidebar-btn" data-bs-dismiss="offcanvas">
                    <span class="bi bi-x fs-3"></span>
                </button>
            </div>
            <div class="offcanvas-body">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link text-reset" aria-current="page">
                            <span class="bi bi-house fs-4 me-1"></span>Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('stream.home') }}" class="nav-link text-reset">
                            <span class="bi bi-hdmi fs-4 me-1"></span>Input Stream
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('premiere.home') }}" class="nav-link text-reset">
                            <span class="bi bi-play-btn fs-4 me-1"></span>Premiere Video
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.settings') }}" class="nav-link text-reset">
                            <span class="bi bi-person-gear fs-4 me-1"></span>Account Settings
                        </a>
                    </li>
                    @if(Auth::user()->is_operator == TRUE)
                    <hr>
                    <div class="fs-5 fw-light"><span class="bi bi-wrench-adjustable-circle me-1"></span>Administrator
                    </div>
                    <li>
                        <a href="{{ route('analytics.home') }}" class="nav-link text-reset">
                            <span class="bi bi-activity fs-4 me-1"></span>Livestream Analytics
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.home') }}" class="nav-link text-reset">
                            <span class="bi bi-people fs-4 me-1"></span>Management Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('interfaces.home') }}" class="nav-link text-reset">
                            <span class="bi bi-tools fs-4 me-1"></span>Interface Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('diagnostic.home') }}" class="nav-link text-reset">
                            <span class="bi bi-wrench-adjustable-circle fs-4 me-1"></span>Diagnostic
                        </a>
                    </li>
                    @endif
                </ul>
                <hr>
                <div class="small d-flex justify-content-around flex-wrap">
                    <a class="nav-link text-reset" href="https://github.com/sandyh90/rockstream-streaming-relay"
                        target="_blank"><span class="bi bi-github me-1"></span>Github Source</a>
                    <div class="support-author-dropdown dropdown">
                        <div type="button" class="dropdown-toggle nav-link text-reset" data-bs-toggle="dropdown"
                            aria-expanded="false" role="button">
                            <span class="bi bi-heart-fill text-danger me-1"></span>Support Me
                        </div>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://trakteer.id/pickedianz/tip" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="me-1"
                                        viewBox="0 0 500 500" version="1.1">
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 223.56 6.23 C 250.57 0.57 278.97 20.06 284.85 46.76 C 253.53 48.72 230.30 81.90 238.11 112.10 C 206.84 116.22 176.62 87.65 179.37 56.17 C 180.11 31.96 199.49 9.73 223.56 6.23 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 268.55 64.51 C 288.19 50.06 319.01 55.04 332.62 75.42 C 343.59 90.18 344.05 111.12 334.67 126.78 C 308.25 127.22 281.78 127.23 255.36 126.77 C 243.19 106.54 248.55 77.64 268.55 64.51 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 169.39 138.50 C 229.24 138.19 289.10 138.46 348.95 138.36 C 359.97 137.76 370.34 147.92 369.31 159.03 C 369.04 169.43 359.50 178.45 349.02 177.71 C 288.25 177.36 227.42 178.21 166.69 177.28 C 144.97 174.32 147.39 138.58 169.39 138.50 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 147.10 186.98 C 221.64 186.79 296.20 186.60 370.74 187.08 C 371.36 193.60 369.04 201.23 371.91 207.05 C 395.19 217.01 411.77 241.40 410.69 266.96 C 410.63 322.79 410.92 378.64 410.55 434.46 C 409.70 466.73 380.55 493.79 348.70 494.53 C 290.24 494.69 231.76 494.69 173.30 494.53 C 141.50 493.79 112.28 466.82 111.43 434.60 C 111.17 378.07 111.35 321.54 111.34 265.01 C 110.84 241.41 125.91 218.99 146.85 208.72 C 147.38 201.48 147.00 194.23 147.10 186.98 M 175.24 215.25 C 175.49 220.02 175.39 224.79 174.91 229.54 C 150.75 230.94 134.93 254.98 139.36 278.00 C 139.37 328.98 139.34 379.97 139.37 430.95 C 139.30 448.62 154.46 464.97 172.24 465.59 C 172.65 466.22 173.06 466.85 173.47 467.50 C 231.23 467.86 289.00 467.30 346.75 467.78 L 346.76 467.23 C 289.11 467.60 231.45 467.34 173.80 467.35 L 173.81 466.11 C 231.55 465.89 289.30 466.13 347.04 466.00 C 365.64 466.06 382.92 449.76 382.60 430.85 C 382.59 375.10 382.82 319.33 382.48 263.59 C 381.63 243.09 361.81 229.46 342.51 229.32 C 342.46 224.62 342.24 219.93 342.16 215.23 C 286.52 215.33 230.88 215.29 175.24 215.25 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 196.41 293.43 C 214.67 276.65 244.34 282.02 260.13 299.57 C 276.09 283.14 303.86 276.83 322.54 292.48 C 336.37 303.69 342.13 323.91 335.43 340.56 C 329.47 356.55 315.41 367.06 304.06 379.05 C 289.33 393.33 275.45 408.53 260.20 422.24 C 238.31 400.35 216.11 378.72 194.66 356.41 C 178.61 339.14 177.66 309.11 196.41 293.43 Z" />
                                    </svg>
                                    Trakteer
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="https://ko-fi.com/sandyh90" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="me-1"
                                        viewBox="0 0 500 500" version="1.1">
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 80.19 170.94 C 79.50 161.05 87.73 150.85 98.06 151.25 C 177.06 151.15 256.06 151.17 335.05 151.24 C 359.92 151.18 384.90 161.85 401.06 180.93 C 423.57 208.68 427.23 251.39 407.72 281.70 C 391.07 308.08 358.95 321.75 328.45 321.39 C 327.61 338.85 320.47 357.69 304.20 366.24 C 291.64 374.49 276.16 371.91 261.99 372.34 C 217.34 372.94 172.67 372.55 128.00 372.73 C 106.29 375.10 82.53 360.04 81.05 337.06 C 79.77 281.72 79.93 226.31 80.19 170.94 M 97.32 164.39 C 94.89 164.99 92.92 167.35 93.23 169.93 C 92.91 224.63 92.76 279.35 93.83 334.04 C 94.02 348.87 107.88 360.04 122.21 359.67 C 176.82 359.70 231.46 359.90 286.06 359.07 C 305.22 356.86 316.38 336.89 315.35 318.92 C 314.41 314.15 317.16 307.42 322.93 308.32 C 348.56 309.79 376.02 300.96 392.50 280.48 C 412.88 255.90 411.66 217.91 393.56 192.44 C 379.78 174.22 356.79 163.72 334.05 164.21 C 255.14 164.28 176.20 163.92 97.32 164.39 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 321.30 191.23 C 340.65 188.47 363.78 192.22 374.26 210.76 C 386.85 232.86 379.88 266.53 355.52 277.41 C 345.90 282.83 334.54 281.14 323.99 281.40 C 320.11 281.87 316.27 278.82 316.39 274.81 C 316.28 249.24 316.38 223.66 316.34 198.08 C 316.05 194.93 318.16 191.87 321.30 191.23 M 329.33 204.21 C 329.34 225.71 329.34 247.21 329.34 268.70 C 354.78 273.61 374.39 246.60 365.74 223.26 C 361.09 208.18 344.06 201.03 329.33 204.21 Z" />
                                        <path fill="#010101" opacity="1.00"
                                            d=" M 142.90 212.91 C 159.85 194.64 190.59 200.02 204.99 218.77 C 216.81 202.77 239.92 197.34 257.51 206.48 C 280.12 219.89 278.11 252.89 260.97 269.95 C 243.05 288.82 224.64 307.35 204.97 324.36 C 204.50 324.02 203.56 323.33 203.09 322.98 C 188.16 306.89 171.73 292.11 154.99 278.00 C 137.08 263.00 126.32 232.56 142.90 212.91 Z" />
                                        <path fill="#000000" opacity="0.22"
                                            d=" M 74.33 215.49 C 77.59 220.16 71.73 221.97 74.33 215.49 Z" />
                                        <path fill="#000000" opacity="0.24"
                                            d=" M 383.13 309.03 C 387.78 308.05 382.11 313.64 383.13 309.03 Z" />
                                    </svg>
                                    ko-fi
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="https://saweria.co/pickedianz" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="me-1"
                                        viewBox="0 0 500 500" version="1.1">
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 83.47 0.00 L 92.42 0.00 C 108.72 1.13 123.27 12.71 127.27 28.67 C 135.79 55.68 127.99 87.53 145.62 111.46 C 150.32 116.47 156.42 123.47 164.09 121.11 C 218.38 105.71 276.73 106.88 331.16 120.99 C 340.72 124.20 347.88 114.48 352.93 107.84 C 368.80 80.49 358.27 46.04 373.37 18.38 C 379.24 7.32 391.51 1.51 403.50 0.00 L 408.65 0.00 C 431.38 6.92 447.27 29.36 447.19 52.98 C 448.15 97.02 419.72 143.72 375.60 155.06 C 411.26 202.97 448.38 250.63 474.07 304.89 C 480.64 318.33 486.26 335.48 476.90 349.04 C 470.67 349.41 464.43 349.45 458.19 349.32 C 463.47 369.18 476.26 389.75 468.66 410.66 C 456.31 429.18 436.68 442.05 416.21 450.14 C 387.26 455.48 374.35 489.32 344.85 494.06 C 308.89 497.64 280.55 467.09 245.01 467.68 C 238.21 467.90 230.53 468.28 225.48 473.49 C 208.15 490.19 184.17 498.68 160.41 500.00 L 151.54 500.00 C 125.86 495.94 99.51 480.22 92.13 453.88 C 67.62 448.73 46.45 433.32 29.70 415.31 C 17.05 394.65 32.19 371.68 36.94 350.98 C 28.23 352.01 18.51 348.69 15.05 340.00 C 10.00 329.53 16.16 318.76 20.20 309.16 C 44.41 252.12 84.76 204.39 121.05 154.88 C 72.26 140.81 39.08 86.04 52.22 36.41 C 54.15 19.52 65.74 2.63 83.47 0.00 M 71.55 19.50 C 42.55 63.99 72.97 131.74 124.25 142.50 C 129.35 143.01 137.27 142.94 137.55 149.78 C 97.11 210.08 43.03 264.63 24.71 336.95 C 32.91 336.37 47.83 331.40 50.08 343.03 C 49.85 364.07 32.77 382.87 37.56 404.37 C 48.37 423.69 69.30 435.33 90.05 441.13 C 89.97 416.88 105.18 391.63 128.87 384.04 C 141.61 380.25 154.25 386.91 162.62 396.24 C 175.60 382.94 200.72 389.23 203.98 408.14 C 229.69 405.54 249.18 436.87 236.29 459.00 C 244.88 458.43 253.49 458.41 262.09 458.15 C 247.47 436.73 263.54 398.47 292.14 404.23 C 294.88 384.68 320.93 376.59 333.85 391.77 C 341.71 382.01 360.31 371.13 369.68 384.32 C 385.42 400.36 400.77 418.62 405.26 441.29 C 422.05 437.70 436.95 427.81 449.54 416.42 C 454.06 411.89 459.98 407.12 459.42 400.00 C 460.89 377.11 445.36 357.84 443.55 335.56 C 453.14 335.79 462.75 340.28 472.27 337.27 C 454.32 262.85 394.49 209.97 359.20 144.31 C 414.78 142.52 452.87 73.59 428.23 24.70 C 418.47 1.56 383.07 11.00 378.00 32.84 C 371.93 60.56 376.74 91.36 361.71 116.66 C 354.79 127.26 340.98 137.90 327.89 132.13 C 273.71 116.13 215.09 117.69 161.00 133.18 C 143.37 132.28 130.24 116.38 127.09 99.84 C 121.10 77.49 122.76 53.93 117.40 31.47 C 111.86 13.24 84.47 2.95 71.55 19.50 M 353.68 389.67 C 343.88 393.67 344.24 407.67 336.17 412.61 C 331.86 405.00 325.68 395.01 315.15 396.30 C 303.73 397.32 299.64 410.34 301.05 420.15 C 293.17 413.53 280.63 413.48 274.07 421.99 C 263.47 433.26 267.25 451.12 279.19 459.81 C 310.04 492.97 370.37 486.07 392.58 446.61 C 393.03 426.61 378.71 410.15 365.65 396.47 C 362.28 393.45 359.06 386.79 353.68 389.67 M 112.87 407.98 C 92.68 428.57 101.83 466.63 126.61 479.45 C 160.54 498.50 214.22 486.90 227.12 447.20 C 234.12 429.36 209.05 410.82 195.28 425.87 C 193.65 417.18 191.91 408.46 188.72 400.18 C 176.39 396.13 167.76 407.06 161.79 416.02 C 159.70 415.83 157.61 415.64 155.53 415.45 C 153.56 408.01 151.25 398.93 143.14 396.04 C 131.71 392.43 120.29 399.79 112.87 407.98 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 293.83 142.89 C 312.61 140.21 329.30 152.00 342.35 164.11 C 340.71 166.73 338.87 169.23 336.90 171.61 C 324.07 162.57 310.25 152.99 293.86 153.00 C 293.66 149.64 293.65 146.26 293.83 142.89 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 145.86 175.08 C 144.95 154.89 168.90 147.93 184.98 145.83 C 191.04 143.63 192.84 149.96 194.00 154.44 C 176.04 155.90 161.57 167.57 145.86 175.08 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 263.04 169.90 C 267.29 172.81 270.47 177.09 270.45 182.44 C 270.71 190.91 278.79 198.69 273.98 207.07 C 259.31 204.63 256.59 181.22 263.04 169.90 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 243.88 170.75 C 247.02 170.90 250.14 171.29 253.21 171.96 C 253.47 183.91 253.58 195.89 252.86 207.83 C 249.34 207.83 245.83 207.78 242.31 207.70 C 242.43 195.38 241.38 182.91 243.88 170.75 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 326.45 172.66 C 384.64 162.74 419.09 248.75 379.55 287.44 C 365.98 299.81 345.94 305.37 328.17 299.97 C 286.39 285.83 269.12 226.25 295.22 191.22 C 302.79 181.38 314.38 175.06 326.45 172.66 M 303.76 197.51 C 311.89 193.17 323.43 190.94 330.17 198.89 C 341.93 212.45 342.43 232.34 338.92 249.11 C 336.75 265.65 319.14 276.87 303.85 268.15 C 316.29 288.16 345.75 296.86 366.21 284.18 C 377.39 277.49 381.77 264.45 385.68 252.78 C 392.25 221.64 368.05 186.54 336.04 183.29 C 323.71 182.20 311.91 188.67 303.76 197.51 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 227.09 173.04 C 230.12 172.87 233.16 172.78 236.20 172.78 C 237.50 184.39 234.04 195.91 228.82 206.17 C 226.11 206.30 223.40 206.38 220.69 206.39 C 219.92 194.78 226.65 184.51 227.09 173.04 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 152.34 175.22 C 170.24 171.93 189.73 180.16 199.02 195.97 C 217.10 225.19 212.92 267.80 187.15 291.23 C 164.43 312.06 122.79 308.73 107.33 280.68 C 83.62 242.56 103.55 179.23 152.34 175.22 M 124.12 203.07 C 103.30 225.53 99.91 266.95 126.70 286.26 C 149.58 303.46 182.03 287.16 194.07 264.32 C 186.99 268.81 177.70 273.05 169.66 268.29 C 158.41 261.13 154.88 246.59 154.11 234.08 C 152.75 216.46 159.96 193.53 179.95 190.39 C 161.41 179.87 136.99 186.80 124.12 203.07 Z" />
                                        <path fill="#000000" opacity="1.00"
                                            d=" M 205.57 276.49 C 231.73 257.99 275.69 255.97 296.24 283.96 C 291.17 292.84 281.79 297.43 273.41 302.35 C 270.21 310.28 264.29 316.42 256.66 320.26 C 267.61 330.87 282.03 337.45 296.75 340.92 C 299.03 335.49 300.89 329.88 302.28 324.15 C 296.43 320.68 289.52 315.85 291.87 307.92 C 303.02 310.79 313.68 315.30 324.87 318.07 C 325.07 321.08 325.18 324.10 325.21 327.12 C 321.28 327.27 317.35 327.39 313.42 327.48 C 310.82 340.83 298.33 358.36 283.07 349.45 C 273.49 361.80 264.40 374.52 255.32 387.24 C 252.38 392.33 245.81 389.03 241.74 387.27 C 227.46 380.39 222.43 364.19 218.04 350.20 C 203.89 355.66 194.65 341.37 189.83 330.38 C 185.84 330.35 181.84 330.26 177.85 330.12 C 173.17 316.19 194.96 316.67 202.07 307.69 C 208.09 312.30 205.63 319.97 199.69 323.26 C 201.73 329.69 205.37 335.43 209.34 340.83 C 221.43 335.52 233.19 329.07 243.47 320.73 C 234.75 317.21 226.80 311.66 223.49 302.48 C 213.99 298.14 196.39 289.05 205.57 276.49 M 228.59 344.36 C 232.05 357.01 236.43 370.97 247.00 379.61 C 255.78 368.38 263.55 356.38 272.02 344.90 C 264.63 339.93 257.30 334.91 250.10 329.67 C 242.95 334.58 235.27 338.84 228.59 344.36 Z" />
                                    </svg>
                                    Saweria
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-3">
            @yield('content')
        </div>
    </div>

    <footer class="p-4 border-top">
        <div class="container-fluid text-muted">
            <div class="d-flex justify-content-between flex-wrap">
                <div class="small">
                    Copyright &copy; {{ date('Y') }}<a href="{{ url('/') }}" class="ms-1">RockStream</a>
                    <div class="d-inline">Powered By<a href="https://github.com/sandyh90" target="_blank"
                            class="ms-1">Pickedianz</a>
                    </div>
                </div>
                <div class="fw-light">
                    Page rendered in <strong>{{ round(microtime(true) - LARAVEL_START, 4) }}</strong> seconds.
                </div>
            </div>
    </footer>

    <!-- Bootstrap Bundle with Popper Important -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Addons Javascript Module -->
    <script src="{{ asset('assets/vendor/clipboard.js-2.0.8/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.min.js') }}"></script>

    <!-- Custom Javascript Module -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    @yield('js-content')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            get_rtmp_stat()

        });
    </script>
    <script>
        function get_rtmp_stat() {
            $.ajax({
                type: "GET",
                url: "{{ route('panel.data_rtmp_stat') }}",
                async: true,
                success: function (data) {
                    if(data.success == true){
                        $(".indicator-rtmp .uptime").html(data.xml.uptime);
                        $(".indicator-rtmp .bytes-in-out").html(data.xml.bytes_in_out);
                        $(".indicator-rtmp .bandwidth").html(data.xml.bandwidth);
                        if(data.xml.status == true){
                            $(".indicator-rtmp .status-stream").html('<span class="bi bi-cloud-check me-1"></span>Online').removeClass('bg-danger').addClass('bg-success');
                        }else{
                            $(".indicator-rtmp .status-stream").html('<span class="bi bi-cloud-slash me-1"></span>Offline').removeClass('bg-success').addClass('bg-danger');
                        }
                        setTimeout('get_rtmp_stat()', 2000);
                    }else{
                        $(".indicator-rtmp .uptime").html('NaN');
                        $(".indicator-rtmp .bytes-in-out").html('NaN');
                        $(".indicator-rtmp .bandwidth").html('NaN');
                        $(".indicator-rtmp .status-stream").html('<span class="bi bi-cloud-minus me-1"></span>NaN').removeClass('bg-success bg-danger').addClass('bg-secondary');
                        setTimeout('get_rtmp_stat()', 10000);
                    }
                },
                error: function (data) {
                    $(".indicator-rtmp .uptime").html('NaN');
                    $(".indicator-rtmp .bytes-in-out").html('NaN');
                    $(".indicator-rtmp .bandwidth").html('NaN');
                    $(".indicator-rtmp .status-stream").html('<span class="bi bi-cloud-minus me-1"></span>NaN').removeClass('bg-success bg-danger').addClass('bg-secondary');
                    setTimeout('get_rtmp_stat()', 10000);
                }
            });
            
        }
    </script>
</body>

<div class="modal fade custom-modal-display" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="custom-modal-content">
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-about-display bg-secondary" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content rounded-6 shadow">
            <div class="modal-body p-5">
                @include('layouts.info_layouts.about_modal')
                <button type="button" class="btn btn-lg btn-primary btn-dismiss-about-modal mt-5 w-100"
                    data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@yield('modal-content')

</html>