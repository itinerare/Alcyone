@extends('admin.layout')

@section('admin-title') User Index @stop

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'User Index' => 'admin/users']) !!}

    <h1>User Index</h1>

    <p>Click on a user's name to view/edit their information.</p>

    <div>
        {{ html()->form('GET')->class('d-flex justify-content-end')->open() }}
        <div class="me-2 mb-3">
            {{ html()->text('name', request()->get('name'))->class('form-control') }}
        </div>
        <div class="me-2 mb-3">
            {{ html()->select('rank_id', $ranks, request()->get('rank_id'))->class('form-select') }}
        </div>
        <div class="me-2 mb-3">
            {{ html()->select(
                    'sort',
                    [
                        'alpha' => 'Sort Alphabetically (A-Z)',
                        'alpha-reverse' => 'Sort Alphabetically (Z-A)',
                        'rank' => 'Sort by Rank (Default)',
                        'newest' => 'Newest First',
                        'oldest' => 'Oldest First',
                    ],
                    request()->get('sort') ?? 'category',
                )->class('form-select') }}
        </div>
        <div class="mb-3">
            {{ html()->submit('Submit')->class('btn btn-primary') }}
        </div>
        {{ html()->form()->close() }}
    </div>

    {!! $users->render() !!}
    <div class="row ms-md-2">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-12 col-md-4 font-weight-bold">Username</div>
            <div class="col-4 col-md-2 font-weight-bold">Rank</div>
            <div class="col-4 col-md-3 font-weight-bold">Joined</div>
        </div>
        @foreach ($users as $user)
            <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
                <div class="col-12 col-md-4 "><a href="{{ $user->adminUrl }}">{!! $user->is_banned ? '<strike>' : '' !!}{{ $user->name }}{!! $user->is_banned ? '</strike>' : '' !!}</a>
                </div>
                <div class="col-4 col-md-2">{!! $user->rank->name !!}</div>
                <div class="col-4 col-md-3">{!! pretty_date($user->created_at) !!}</div>
            </div>
        @endforeach
    </div>
    {!! $users->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $count }} user{{ $count == 1 ? '' : 's' }} found.</div>

@endsection
