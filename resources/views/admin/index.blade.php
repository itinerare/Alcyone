@extends('admin.layout')

@section('admin-content')
    <div class="col-sm mb-3">
        <div class="card bg-body-secondary h-100">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-body-secondary">
                        <span class="float-end"><a href="{{ url('admin/reports') }}">View Queue
                                <span class="fas fa-caret-end ms-1"></span></a></span>
                        <h5>Reports @if ($reportsCount)
                                <span class="badge badge-primary text-light ms-2" style="font-size: 1em;">{{ $reportsCount }}</span>
                            @endif
                        </h5>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
