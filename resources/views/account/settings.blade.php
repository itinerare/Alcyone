@extends('account.layout')

@section('account-title')
    Settings
@endsection

@section('account-content')
    {!! breadcrumbs(['User Settings' => 'account/settings']) !!}

    <h1>Settings</h1>

    <div class="card bg-body-secondary mb-4">
        <div class="card-body">
            <h3>Theme</h3>

            {{ html()->modelForm(Auth::user(), 'POST', 'theme')->open() }}
            <div class="mb-3 row">
                {{ html()->label('Theme', 'theme')->class('col-md-2 col-form-label') }}
                <div class="col-md-10">
                    {{ html()->select('theme', ['dark' => 'Dark', 'light' => 'Light'])->class('form-select') }}
                </div>
            </div>
            <div class="text-end">
                {{ html()->submit('Edit')->class('btn btn-primary') }}
            </div>
            {{ html()->closeModelForm() }}
        </div>
    </div>

    <div class="card bg-body-secondary mb-4">
        <div class="card-body">
            <h3>Email Address</h3>

            {{ html()->modelForm(Auth::user(), 'POST', 'email')->open() }}
            <div class="mb-3 row">
                {{ html()->label('Email Address', 'email')->class('col-md-2 col-form-label') }}
                <div class="col-md-10">
                    {{ html()->text('email')->class('form-control') }}
                </div>
            </div>
            <div class="text-end">
                {{ html()->submit('Edit')->class('btn btn-primary') }}
            </div>
            {{ html()->closeModelForm() }}

            @if (Auth::user()->isMod)
                <h3>Admin Notifications</h3>

                {{ html()->modelForm(Auth::user(), 'POST', 'admin-notifs')->open() }}
                <div class="mb-3 row">
                    {{ html()->label('Receive Admin Notifications '.add_help('Whether or not you would like to receive an email notification when a new report is submitted.'), 'receive_admin_notifs')->class('col-md-2 col-form-label') }}
                    <div class="col-md-10">
                        {{ html()->checkbox('receive_admin_notifs')->class('form-check mt-3') }}
                    </div>
                </div>
                <div class="text-end">
                    {{ html()->submit('Edit')->class('btn btn-primary') }}
                </div>
                {{ html()->closeModelForm() }}
            @endif
        </div>
    </div>

    <div class="card bg-body-secondary mb-4">
        <div class="card-body">
            <h3>Change Password</h3>

            {{ html()->form('POST', 'password')->open() }}
            <div class="mb-3 row">
                {{ html()->label('Old Password', 'old_password')->class('col-md-2 col-form-label') }}
                <div class="col-md-10">
                    {{ html()->password('old_password')->class('form-control') }}
                </div>
            </div>
            <div class="mb-3 row">
                {{ html()->label('New Password', 'new_password')->class('col-md-2 col-form-label') }}
                <div class="col-md-10">
                    {{ html()->password('new_password')->class('form-control') }}
                </div>
            </div>
            <div class="mb-3 row">
                {{ html()->label('Confirm New Password', 'new_password_confirmation')->class('col-md-2 col-form-label') }}
                <div class="col-md-10">
                    {{ html()->password('new_password_confirmation')->class('form-control') }}
                </div>
            </div>
            <div class="text-end">
                {{ html()->submit('Edit')->class('btn btn-primary') }}
            </div>
            {{ html()->form()->close() }}
        </div>
    </div>

    <div class="card bg-body-secondary mb-4">
        <div class="card-body">
            <h3>Two-Factor Authentication</h3>

            <p>Two-factor authentication acts as a second layer of protection for your account. It uses an app on your
                phone-- such as Google Authenticator-- and information provided by the site to generate a random code that
                changes frequently.</p>

            @if (!isset(Auth::user()->two_factor_secret))
                <p>In order to enable two-factor authentication, you will need to scan a QR code with an authenticator app
                    on your phone. Two-factor authentication will not be enabled until you do so and confirm by entering one
                    of the codes provided by your authentication app.</p>
                {{ html()->form('POST', 'two-factor/enable')->open() }}
                    <div class="text-end">
                        {{ html()->submit('Submit')->class('btn btn-primary') }}
                    </div>
                {{ html()->form()->close() }}
            @elseif(isset(Auth::user()->two_factor_secret))
                <p>Two-factor authentication is currently enabled.</p>

                <h4>Disable Two-Factor Authentication</h4>
                <p>To disable two-factor authentication, you must enter a code from your authenticator app.</p>
                {{ html()->form('POST', 'two-factor/disable')->open() }}
                <div class="mb-3 row">
                    {{ html()->label('Code', 'code')->class('col-md-2 col-form-label') }}
                    <div class="col-md-10">
                        {{ html()->text('code')->class('form-control') }}
                    </div>
                </div>
                <div class="text-end">
                    {{ html()->submit('Submit')->class('btn btn-primary') }}
                </div>
                {{ html()->form()->close() }}
            @endif
        </div>
    </div>
@endsection
