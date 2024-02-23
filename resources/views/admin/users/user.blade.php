@extends('admin.layout')

@section('admin-title') User Index @stop

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'User Index' => 'admin/users',
        $user->name => 'admin/users/' . $user->name . '/edit',
    ]) !!}

    <h1>User: {!! $user->displayName !!}</h1>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="{{ $user->adminUrl }}">Account</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/users/' . $user->name . '/updates') }}">Account Updates</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/users/' . $user->name . '/ban') }}">Ban</a>
        </li>
    </ul>

    <h3>Basic Info</h3>
    {{ html()->modelForm($user, 'POST', 'basic')->class('mb-4')->open() }}
    <div class="mb-3 row">
        {{ html()->label('Username', 'name')->class('col-md-2 col-form-label') }}
        <div class="col-md-10">
            {{ html()->text('name')->class('form-control') }}
        </div>
    </div>
    <div class="mb-3 row">
        {{ html()->label('Rank ' . ($user->isAdmin ? add_help('The rank of the admin user cannot be edited.') : ''), 'rank_id')->class('col-md-2 col-form-label') }}
        <div class="col-md-10">
            {{ html()->select('rank_id', $ranks)->class('form-select')->disabled($user->isAdmin) }}
        </div>
    </div>
    <div class="text-end">
        {{ html()->submit('Edit')->class('btn btn-primary') }}
    </div>
    {{ html()->closeModelForm() }}

    {{ html()->modelForm($user, 'POST', 'account')->open() }}
    <div class="mb-3 row">
        {{ html()->label('Email Address', 'email')->class('col-md-2 col-form-label') }}
        <div class="col-md-10">
            {{ html()->text('email')->class('form-control')->disabled() }}
        </div>
    </div>
    <div class="mb-3 row">
        {{ html()->label('Join Date', 'created_at')->class('col-md-2 col-form-label') }}
        <div class="col-md-10">
            {{ html()->text('created_at', format_date($user->created_at, false))->class('form-control')->disabled() }}
        </div>
    </div>
    <div class="text-end">
        {{ html()->submit('Edit')->class('btn btn-primary') }}
    </div>
    {{ html()->closeModelForm() }}
@endsection
