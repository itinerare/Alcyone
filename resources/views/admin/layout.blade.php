@extends('layouts.app')

@section('title')
    Admin:@yield('admin-title')
@endsection

@section('head-scripts')
    @vite(['resources/js/tinymce.js', 'resources/js/tinymce_'.Auth::user()->theme.'.js'])
    @yield('admin-head-scripts')
@endsection

@section('content')
    <div class="text-center mb-4">
        <h5>Navigation</h5>
        <h6>
            <a href="{{ url('admin') }}">Admin Home</a> ・
            <a href="{{ url('admin/reports') }}">Reports Queue</a><br />
            @if (Auth::user()->isAdmin)
                <a href="{{ url('admin/invitations') }}">Invitation Keys</a> ・
                <a href="{{ url('admin/ranks') }}">Ranks</a> ・
                <a href="{{ url('admin/users') }}">User Index</a> ・
                <a href="{{ url('admin/pages') }}">Site Pages</a><br />
            @endif
        </h6>
    </div>

    @yield('admin-content')
@endsection

@section('scripts')
    @parent
    <script type="module">
        $(function() {
            tinymce.init({
                selector: '.wysiwyg',
                height: 500,
                menubar: false,
                convert_urls: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image',
                    'charmap', 'anchor', 'searchreplace', 'visualblocks',
                    'code', 'fullscreen', 'media', 'table', 'code', 'wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat | code',
                content_css: [
                    '{{ Vite::asset('resources/css/app.scss') }}',
                    '{{ Vite::asset('resources/js/tinymce_'.Auth::user()->theme.'.css') }}'
                ],
                target_list: false
            });
        });
    </script>
@endsection
