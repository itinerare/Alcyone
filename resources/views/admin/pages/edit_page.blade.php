@extends('admin.layout')

@section('admin-title')
    Edit Text Page
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Text Pages' => 'admin/pages',
        'Edit Page' => 'admin/pages/edit/' . $page->id,
    ]) !!}

    <h1>
        Edit Text Page
    </h1>

    <div class="row">
        <div class="col-md-6 mb-3">
            {!! Form::label('title', 'Title', ['class' => 'form-label']) !!}
            {!! Form::text('name', $page->title, ['class' => 'form-control', 'disabled']) !!}
        </div>
        <div class="col-md-6 mb-3">
            {!! Form::label('key', 'Key', ['class' => 'form-label']) !!}
            {!! Form::text('key', $page->key, ['class' => 'form-control', 'disabled']) !!}
        </div>
    </div>

    {!! Form::open(['url' => 'admin/pages/edit/' . $page->id]) !!}

    <div class="mb-3">
        {!! Form::label('text', 'Content', ['class' => 'form-label']) !!}
        {!! Form::textarea('text', $page->text, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="text-end">
        {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@endsection
