@if ($rank)
    {!! Form::open(['url' => 'admin/ranks/edit/' . $rank->id]) !!}

    <div class="mb-3">
        {!! Form::label('Rank Name') !!}
        {!! Form::text('name', $rank->name, ['class' => 'form-control']) !!}
    </div>

    <div class="mb-3">
        {!! Form::label('Description (optional)') !!}
        {!! Form::textarea('description', $rank->description, ['class' => 'form-control']) !!}
    </div>

    <div class="text-end">
        {!! Form::submit($rank->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid rank selected.
@endif
