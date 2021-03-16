Hello, {{ $user->name }}!

Thank you for creating an account!

Please verify your account using the <a href="{{ route('verify', $user->verification_token) }}">clicking here</a>.

If the link doesn't work copy & paste this link in your browser: {{ route('verify', $user->verification_token) }}

