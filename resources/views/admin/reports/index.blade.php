@extends('admin.layout')

@section('admin-title')
    Report Queue
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Report Queue' => 'admin/reports/pending']) !!}

    <h1> Report Queue</h1>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ set_active('admin/reports/pending*') }} {{ set_active('admin/reports') }}" href="{{ url('admin/reports/pending') }}">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ set_active('admin/reports/accepted*') }}" href="{{ url('admin/reports/accepted') }}">Accepted</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ set_active('admin/reports/cancelled*') }}" href="{{ url('admin/reports/cancelled') }}">Cancelled</a>
        </li>
    </ul>

    {!! Form::open(['method' => 'GET', 'class' => 'd-flex justify-content-end']) !!}
    <div class="mb-3">
        {!! Form::select(
            'sort',
            [
                'newest' => 'Newest First',
                'oldest' => 'Oldest First',
            ],
            Request::get('sort') ?: 'oldest',
            ['class' => 'form-select'],
        ) !!}
    </div>
    <div class="ms-2 mb-3">
        {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}

    {!! $reports->render() !!}

    <div class="row ms-md-2">
        <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
            <div class="col-12 col-md-3 font-weight-bold">Image</div>
            <div class="col-6 col-md-2 font-weight-bold">Uploader</div>
            <div class="col-6 col-md-2 font-weight-bold">Reported By</div>
            <div class="col-6 col-md-2 font-weight-bold">Reported</div>
            <div class="col-6 col-md font-weight-bold">Status</div>
        </div>

        @foreach ($reports as $report)
            <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
                <div class="col-12 col-md-3">{{ $report->image->slug }}</div>
                <div class="col-6 col-md-2">{!! $report->image->user->displayName !!}</div>
                <div class="col-3 col-md-2">{!! $report->reporter->displayName !!}</div>
                <div class="col-6 col-md-2">{!! pretty_date($report->created_at) !!}</div>
                <div class="col-3 col-md">
                    <span class="btn btn-{{ $report->status == 'Pending' ? 'primary' : ($report->status == 'Accepted' ? 'warning' : 'danger') }} btn-sm py-0 px-1">{{ $report->status }}</span>
                </div>
                <div class="col-3 col-md-1"><a href="{{ $report->adminUrl }}" class="btn btn-primary btn-sm py-0 px-1">Details</a></div>
            </div>
        @endforeach

    </div>

    {!! $reports->render() !!}
    <div class="text-center mt-4 small text-muted">{{ $reports->total() }}
        result{{ $reports->total() == 1 ? '' : 's' }} found.</div>
@endsection
