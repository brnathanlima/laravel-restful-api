@component('mail::message')
# Hello, {{ $user->name }}!

We've notice that recently you changed the email address for your account.
Threrefore, we need to verify it again. Please click the button bellow:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Re-verify account
@endcomponent

Best regards,<br>
{{ config('app.name') }}
@endcomponent
