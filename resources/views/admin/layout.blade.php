@extends('layouts.app')

@section('title')
    Admin:@yield('admin-title')
@endsection

@section('content')
    <div class="text-center mb-4">
        <h5>Navigation</h5>
        <h6>
            <a href="{{ url('admin') }}">Admin Home</a> ・
            <a href="{{ url('admin/reports') }}">Reports Queue</a><br />
            @if(Auth::user()->isAdmin)
                <a href="{{ url('admin/invitations') }}">Invitation Keys</a> ・
                <a href="{{ url('admin/ranks') }}">Ranks</a> ・
                <a href="{{ url('admin/users') }}">User Index</a> ・
                <a href="{{ url('admin/pages') }}">Site Pages</a><br />
            @endif
        </h6>
    </div>

    @yield('admin-content')
@endsection
