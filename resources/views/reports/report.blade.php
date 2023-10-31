@extends('layouts.app')

@section('title') Report {{ $report->key }} @endsection

@section('content')
    <h1>
        Report {{ $report->key }}
        <div class="float-end badge
            {{ $report->status == 'Pending' ? 'bg-primary' : '' }}
            {{ $report->status == 'Accepted' ? 'bg-warning' : '' }}
            {{ $report->status == 'Cancelled' ? 'bg-danger' : '' }}
        ">
                {{ $report->status }}
        </div>
    </h1>

    @include('reports._report_info')

    <p>
        This is the record of your report. You can return to this page at any time to view the current state of the report and any staff comments. Please save this URL if you wish to view the report's status at a later date, especially if you have not provided an email address for later notification.
    </p>

    <p>
        @switch($report->status)
            @case('Accepted')
                This report has been accepted and the reported image removed.
                @break
            @case('Cancelled')
                The report was reviewed and the reported image found not to violate the <a href="{{ url('info/terms') }}">Terms of Service.</a>
                @break
            @case('Pending')
                This report is pending review by staff.
                @break
            @default
        @endswitch
    </p>

    <div class="card bg-body-secondary">
        <div class="card-body">
            <h3>Staff Comments</h3>
            @isset($report->staff_comments)
                {!! $report->staff_comments !!}
            @else
                <i>No staff comments provided.</i>
            @endisset
        </div>
    </div>
@endsection
