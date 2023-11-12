@extends('admin.layout')

@section('admin-title')
    User Ranks
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'User Ranks' => 'admin/ranks']) !!}

    <h1>User Ranks</h1>

    <p>This site uses a simple rank system to handle basic permisisons. You can edit the name and description of the site's
        ranks here. Permissions afforded to each rank are as such:</p>
    <ul>
        <li><strong>Admin</strong>/Rank 1: Can view and edit basic site settings and manage, including ban, users. Also has access to the reports queue.</li>
        <li><strong>Moderator</strong>/Rank 2: Can view the reports queue, but cannot access other admin panel functions.</li>
        <li><strong>Member</strong>/Rank 3: A regular user.</li>
    </ul>
    <p>Users are given the basic/member rank by default. To assign a rank to a user, find their admin page from the <a href="{{ url('admin/users') }}">User Index</a> and change their rank there.</p>

    <table class="table table-sm ranks-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ranks as $rank)
                <tr>
                    <td>{!! $rank->name !!}</td>
                    <td>{!! $rank->description !!}</td>
                    <td>
                        <a href="#" class="btn btn-primary edit-rank-button float-end" data-id="{{ $rank->id }}" data-bs-toggle="modal" data-bs-target="#modal">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
@endsection

@section('scripts')
    @parent
    <script type="module">
        $(document).ready(function() {
            $('.edit-rank-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/ranks/edit') }}" + '/' + $(this).data('id'), 'Edit Rank');
            });
        });
    </script>
@endsection
