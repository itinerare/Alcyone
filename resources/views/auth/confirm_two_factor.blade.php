@extends('account.layout')

@section('account-title')
    Confirm Two Factor Auth
@endsection

@section('account-content')
    <h1>Confirm Two-Factor Authentication</h1>

    <p>Two factor authentication information has been generated. Please scan the provided QR code with an authenticator app
        on your phone, then confirm with a code from the app to enable two-factor authentication.</p>

    <p>Please also note that these recovery codes will not be shown to you again. Please save them before continuing.</p>

    <div class="row text-center mb-2">
        <div class="col-md mb-2">
            <h4>QR Code:</h4>
            <div class="rounded bg-light py-2">
                {!! $qrCode !!}
            </div>
        </div>

        <div class="col-md">
            <h4>Recovery Codes:</h4>
            @foreach ($recoveryCodes as $recoveryCode)
                {{ $recoveryCode }}<br />
            @endforeach
        </div>
    </div>

    {{ html()->form()->open() }}
    <div class="mb-3">
        {{ html()->label('Confirm 2FA', 'code')->class('form-label') }}
        {{ html()->text('code')->class('form-control') }}
    </div>
    <div class="text-end">
        {{ html()->submit('Confirm')->class('btn btn-primary') }}
    </div>
    {{ html()->form()->close() }}
@endsection
