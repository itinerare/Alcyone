@extends('layouts.app')

@section('title')
    Forgot Password
@endsection

@section('content')
    <h1>Forgot Password</h1>

    <p>Please enter the email address associated with your account. An email will be sent to this address to reset your
        password.</p>

    {!! Form::open(['url' => 'forgot-password']) !!}
    <div class="mb-3 row">
        {!! Form::label('Email', null, ['class' => 'col-md-3 col-form-label text-md-end']) !!}
        <div class="col-md-7">
            {!! Form::text('email', null, ['class' => 'mb-3']) !!}
        </div>
    </div>
    <div class="text-end">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
@endsection
