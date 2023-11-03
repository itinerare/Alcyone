@extends('account.layout')

@section('account-title')
    Notifications
@endsection

@section('account-content')
    {!! breadcrumbs(['Notifications' => 'notifications']) !!}

    <h1>Notifications</h1>

    <div class="text-end mb-3">
        {!! Form::open(['url' => 'notifications/clear']) !!}
        {!! Form::submit('Clear All', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
    {!! $notifications->render() !!}

    @foreach ($notifications->pluck('notification_type_id')->unique() as $type)
        <div class="card bg-dark-subtle mb-4">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <span class="float-end h5 mb-2">
                        {!! Form::open(['url' => 'notifications/clear/' . $type]) !!}
                        <span class="badge badge-primary">
                            {{ $notifications->where('notification_type_id', $type)->count() }}
                        </span>
                        {!! Form::submit('x clear', ['class' => 'badge btn-primary', 'style' => 'display:inline; border: 0;']) !!}
                        {!! Form::close() !!}
                    </span>
                    <a class="card-title h5 collapse-title mb-2" href="#{{ str_replace(' ', '_', config('mundialis.notifications.' . $type . '.name')) }}" data-bs-toggle="collapse">{{ config('mundialis.notifications.' . $type . '.name') }}
                    </a>
                    <div id="{{ str_replace(' ', '_', config('mundialis.notifications.' . $type . '.name')) }}" class="collapse {{ $notifications->where('notification_type_id', $type)->count() < 5 ? 'show' : '' }} mt-2">
                        <table class="table notifications-table">
                            <thead>
                                <tr>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notifications->where('notification_type_id', $type) as $notification)
                                    <tr class="{{ $notification->is_unread ? 'unread' : '' }}">
                                        <td>{!! $notification->message !!}</td>
                                        <td>{!! format_date($notification->created_at) !!}</td>
                                        <td class="text-end"><a href="#" data-id="{{ $notification->id }}" class="clear-notification"><i class="fas fa-times" aria-hidden="true"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </li>
            </ul>
        </div>
    @endforeach
    @if (!count($notifications))
        <div class="text-center">No notifications.</div>
    @endif

    {!! $notifications->render() !!}
@endsection
@section('scripts')
    @parent
    <script type="module">
        $(document).ready(function() {
            $('.clear-notification').on('click', function(e) {
                e.preventDefault();
                var $row = $(this).parent().parent();
                $.get("{{ url('notifications/delete') }}/" + $(this).data('id'), function(data) {
                    console.log($(this));
                    $row.fadeOut(300, function() {
                        $(this).remove();
                    });
                });
            });

        });
    </script>
@endsection
