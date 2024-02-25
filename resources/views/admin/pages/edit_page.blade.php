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
            {{ html()->label('Title', 'title')->class('form-label') }}
            {{ html()->text('name', $page->title)->class('form-control')->disabled() }}
        </div>
        <div class="col-md-6 mb-3">
            {{ html()->label('Key', 'key')->class('form-label') }}
            {{ html()->text('key', $page->key)->class('form-control')->disabled() }}
        </div>
    </div>

    {{ html()->modelForm($page)->open() }}

    <div class="mb-3">
        {{ html()->label('Content', 'text')->class('form-label') }}
        {{ html()->textarea('text')->class('form-control wysiwyg') }}
    </div>

    <div class="text-end">
        {{ html()->submit('Edit')->class('btn btn-primary') }}
    </div>

    {{ html()->closeModelForm() }}
@endsection
