@extends('layouts.app')

@section('title') New Report @endsection

@section('content')
    <h1>New Report</h1>
    <p>
        If you've encountered an image hosted by this Alycone instance that violates its <a href="{{ url('info/terms') }}">Terms of Service</a>, you may report that image here. You may also (optionally) provide a contact email address if you wish to be notified of actions relating to your report. Reports are limited to one image per; if there are multiple offending images, please report each one individually. Thank you for understanding.
    </p>
    {!! Form::open(['url' => 'reports/new', 'action' => 'reports/new']) !!}

    @honeypot

    <div class="mb-3">
        {!! Form::label('image_url', 'Image URL', ['class' => 'form-label']) !!} {!! add_help('Provide the full URL of the offending image here.') !!}
        {!! Form::text('image_url', null, ['class' => 'form-control']) !!}
    </div>

    <div class="mb-3">
        {!! Form::label('reason', 'Reason for Reporting', ['class' => 'form-label']) !!} {!! add_help('Provide a brief summary of what about this image violates this site\'s ToS.') !!}
        {!! Form::text('reason', null, ['class' => 'form-control']) !!}
    </div>

    <div class="mb-3">
        {!! Form::label('email', 'Email Address (Optional)', ['class' => 'form-label']) !!} {!! add_help('If you wish to be notified of actions regarding your report, please provide your email address.') !!}
        {!! Form::text('email', null, ['class' => 'form-control']) !!}
    </div>

    <div class="mb-3">
        {!! Form::checkbox('agreement', 1, false, ['class' => 'form-check-input']) !!}
        I have read and agree to the <a href="{{ url('info/privacy') }}">Privacy Policy</a>.
    </div>

    <div class="text-end">
        <input onclick="this.disabled=true;this.value='Submiting...';this.form.submit();" class="btn btn-primary" type="submit" value="Submit Report"></input>
    </div>
    {!! Form::close() !!}
@endsection
