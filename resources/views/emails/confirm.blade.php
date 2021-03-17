Hello, {{ $user->name }}!

We've notice that recently you changed the email address for your account.
Threrefore, we need to verify it again. Please access this link: {{ route('verify', $user->verification_token) }}
