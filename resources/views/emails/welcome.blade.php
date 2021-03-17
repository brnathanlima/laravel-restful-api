@component('mail::message')
# Hello, {{ $user->name }}!

Thank you for creating an account!

Please verify your account clicking the button bellow

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verify account
@endcomponent

Best regards,<br>
{{ config('app.name') }}
@endcomponent

