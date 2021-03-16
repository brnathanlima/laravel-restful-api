Hello, {{ $user->name }}!

Thank you for creating an account!

Please verify your account using the link {{ route('verify', $user->verification_token) }}

