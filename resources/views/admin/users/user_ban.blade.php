@extends('admin.layout')

@section('admin-title') User: {{ $user->name }} @stop

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'User Index' => 'admin/users',
        $user->name => 'admin/users/' . $user->name . '/edit',
        'Account Updates' => 'admin/users/' . $user->name . '/updates',
    ]) !!}

    <h1>User: {!! $user->displayName !!}</h1>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link" href="{{ $user->adminUrl }}">Account</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/users/' . $user->name . '/updates') }}">Account Updates</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ url('admin/users/' . $user->name . '/ban') }}">Ban</a>
        </li>
    </ul>

    <h3>{{ $user->is_banned ? 'Edit ' : '' }}Ban</h3>
    <p>Banning the user will prevent them from using site features.</p>

    {{ html()->modelForm($user)->id('banForm')->open() }}
    <div class="mb-3">
        {{ html()->label('Reason (Optional; no HTML)', 'ban_reason') }}
        {{ html()->textarea('ban_reason')->class('form-control') }}
    </div>
    <div class="text-end">
        {{ html()->submit($user->is_banned ? 'Edit' : 'Ban')->class('btn btn' . ($user->is_banned ? '' : '-outline') . '-danger ban-button') }}
    </div>
    {{ html()->form()->close() }}

    @if ($user->is_banned)
        <h3>Unban</h3>
        <p>Unbanning the user will grant them access to site features again. However, if they had a rank before being
            banned, it will not be restored.</p>
        <div class="text-end">
            <a href="#" class="btn btn-outline-danger unban-button">Unban</a>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            @if (!$user->is_banned)
                $('.ban-button').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('admin/users/' . $user->name . '/ban-confirm') }}", 'Ban User');
                });
            @else
                $('.unban-button').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('admin/users/' . $user->name . '/unban-confirm') }}", 'Unban User');
                });
            @endif
        });
    </script>
@endsection
