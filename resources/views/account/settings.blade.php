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

            {!! Form::open(['url' => 'account/theme']) !!}
            <div class="mb-3 row">
                <label class="col-md-2 col-form-label">Theme</label>
                <div class="col-md-10">
                    {!! Form::select('theme', ['dark' => 'Dark', 'light' => 'Light'], Auth::user()->theme, ['class' => 'form-select']) !!}
                </div>
            </div>
            <div class="text-end">
                {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="card bg-body-secondary mb-4">
        <div class="card-body">
            <h3>Email Address</h3>

            {!! Form::open(['url' => 'account/email']) !!}
            <div class="mb-3 row">
                <label class="col-md-2 col-form-label">Email Address</label>
                <div class="col-md-10">
                    {!! Form::text('email', Auth::user()->email, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="text-end">
                {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}

            @if(Auth::user()->isMod)
                <h3>Admin Notifications</h3>

                {!! Form::open(['url' => 'account/admin-notifs']) !!}
                <div class="mb-3 row">
                    <label class="col-md-2 col-form-label">
                        Receive Admin Notifications {!! add_help('Whether or not you would like to receive an email notification when a new report is submitted.') !!}
                    </label>
                    <div class="col-md-10">
                        {!! Form::checkbox('receive_admin_notifs', 1, Auth::user()->receive_admin_notifs, ['class' => 'form-check mt-3']) !!}
                    </div>
                </div>
                <div class="text-end">
                    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            @endif
        </div>
    </div>

    <div class="card bg-body-secondary mb-4">
        <div class="card-body">
            <h3>Change Password</h3>

            {!! Form::open(['url' => 'account/password']) !!}
            <div class="mb-3 row">
                <label class="col-md-2 col-form-label">Old Password</label>
                <div class="col-md-10">
                    {!! Form::password('old_password', ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-md-2 col-form-label">New Password</label>
                <div class="col-md-10">
                    {!! Form::password('new_password', ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-md-2 col-form-label">Confirm New Password</label>
                <div class="col-md-10">
                    {!! Form::password('new_password_confirmation', ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="text-end">
                {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
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
                {!! Form::open(['url' => 'account/two-factor/enable']) !!}
                <div class="text-end">
                    {!! Form::submit('Enable', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            @elseif(isset(Auth::user()->two_factor_secret))
                <p>Two-factor authentication is currently enabled.</p>

                <h4>Disable Two-Factor Authentication</h4>
                <p>To disable two-factor authentication, you must enter a code from your authenticator app.</p>
                {!! Form::open(['url' => 'account/two-factor/disable']) !!}
                <div class="mb-3 row">
                    <label class="col-md-2 col-form-label">Code</label>
                    <div class="col-md-10">
                        {!! Form::text('code', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="text-end">
                    {!! Form::submit('Disable', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            @endif
        </div>
    </div>
@endsection
