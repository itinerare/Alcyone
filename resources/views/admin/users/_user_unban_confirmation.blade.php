@if ($user->is_banned)
    <p>This will unban the user, allowing them to use the site features again. Are you sure you want to do this?</p>
    {{ html()->form()->open() }}
    <div class="text-end">
        {{ html()->submit('Unban')->class('btn btn-danger') }}
    </div>
    {{ html()->form()->close() }}
@else
    <p>This user is not banned.</p>
@endif
