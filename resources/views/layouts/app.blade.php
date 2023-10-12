<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="{{ Auth::check() && Auth::user() ? Auth::user()->theme : 'dark' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('alcyone.settings.site_name', 'Alcyone') }} -@yield('title')</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ config('alcyone.settings.site_name', 'Alcyone') }} -@yield('title')">
    <meta name="description"
        content="@if (View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('alcyone.settings.site_desc', 'An Alcyone site') }} @endif">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url', 'http://localhost') }}">
    @if (View::hasSection('meta-img'))
        <meta property="og:image"
            content="@yield('meta-img')">
    @endif
    <meta property="og:title" content="{{ config('alcyone.settings.site_name', 'Alcyone') }} -@yield('title')">
    <meta property="og:description"
        content="@if (View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('alcyone.settings.site_desc', 'An Alcyone site') }} @endif">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ config('app.url', 'http://localhost') }}">
    @if (View::hasSection('meta-img'))
        <meta property="twitter:image"
            content="@yield('meta-img')">
    @endif
    <meta property="twitter:title"
        content="{{ config('alcyone.settings.site_name', 'Alcyone') }} -@yield('title')">
    <meta property="twitter:description"
        content="@if (View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('alcyone.settings.site_desc', 'An Alcyone site') }} @endif">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    <!-- Scripts -->
    <script defer src="{{ asset('js/site.js') }}"></script>
    @if (View::hasSection('head-scripts'))
        @yield('head-scripts')
    @endif

    {{-- Font Awesome --}}
    <link defer href="{{ asset('css/all.min.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app" class="bg-body-secondary">
        <main class="container-fluid">
            <div class="row">
                <div class="col-lg-2 col-0 d-none d-sm-block">
                </div>

                <div class="col-lg-8 main-content bg-body 0 my-md-4 my-0 px-0">
                    @include('layouts._nav')
                    <div class="page-content p-4">
                        <div>
                            @include('flash::message')
                            @yield('content')
                        </div>

                        <div class="site-footer mt-4" id="footer">
                            @include('layouts._footer')
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div class="modal fade" id="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="modal-title h5 mb-0"></span>
                        <button type="button" class="btn-close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>

        @yield('scripts')
        <script type="module">
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            $(function() {
                var $mobileMenuButton = $('#mobileMenuButton');
                var $sidebar = $('#sidebar');
                $('#mobileMenuButton').on('click', function(e) {
                    e.preventDefault();
                    $sidebar.toggleClass('active');
                });
            });
        </script>
    </div>
</body>

</html>
