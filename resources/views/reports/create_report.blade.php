@extends('layouts.app')

@section('title')
    New Report
@endsection

@section('content')
    <h1>New Report</h1>
    <p>
        If you've encountered an image hosted by this site that violates its <a href="{{ url('info/terms') }}">Terms of Service</a>, you may report that image here. You may also (optionally) provide a contact email address if you wish to be notified of
        actions relating to your report. Reports are limited to one image per; if there are multiple offending images, please report each one individually. Thank you for understanding.
    </p>

    {{ html()->form()->open() }}
    @honeypot

    <div class="mb-3">
        {{ html()->label('Image URL', 'image_url')->class('form-label') }} {!! add_help('Provide the full URL of the offending image here.') !!}
        {{ html()->text('image_url', old('image_url'))->class('form-control') }}
    </div>

    <div class="mb-3">
        {{ html()->label('Reason for Reporting', 'reason')->class('form-label') }} {!! add_help('Provide a brief summary of what about this image violates this site\'s ToS.') !!}
        {{ html()->text('reason', old('reason'))->class('form-control') }}
    </div>

    <div class="mb-3">
        {{ html()->label('Email Address (Optional)', 'email')->class('form-label') }} {!! add_help('If you wish to be notified of actions regarding your report, please provide your email address.') !!}
        {{ html()->text('email', old('email'))->class('form-control') }}
    </div>

    <div class="mb-3">
        {{ html()->checkbox('agreement', false)->class('form-check-input') }}
        {{ html()->label('I have read and agree to the <a href="' . url('info/terms') . '">Terms of Service</a> and <a href="' . url('info/privacy') . '">Privacy Policy</a>.')->class('form-check-label') }}
    </div>

    <div class="text-end">
        <input onclick="this.disabled=true;this.value='Submiting...';this.form.submit();" class="btn btn-primary" type="submit" value="Submit Report"></input>
    </div>
    {{ html()->form()->close() }}
@endsection
