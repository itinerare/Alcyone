@extends('admin.layout')

@section('admin-content')
    <div class="col-sm mb-3">
        <div class="card h-100">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <span class="float-right"><a href="{{ url('admin/reports') }}">View Queue
                                <span class="fas fa-caret-right ml-1"></span></a></span>
                        <h5>Reports @if ($reportsCount)
                                <span class="badge badge-primary text-light ml-2" style="font-size: 1em;">{{ $reportsCount }}</span>
                            @endif
                        </h5>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
