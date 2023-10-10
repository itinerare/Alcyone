@extends('layouts.app')

@section('title')
    Verify Email
@endsection

@section('content')
    <h1>Verify Email</h1>
    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success" role="alert">
            {{ __('A new verification link has been sent to your email address.') }}
        </div>
    @endif

    {{ __('Before proceeding, please check your email for a verification link.') }}
    {{ __('If you did not receive the email') }},
    <form class="d-inline" method="POST" action="{{ url('email/verification-notification') }}">
        @csrf
        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
            {{ __('click here to request another') }}
        </button>.
    </form>
@endsection
