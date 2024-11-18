@component('mail::message')
# Welcome to Your {{ $packageDetails['name'] }}!

Dear {{ $client->name ?? 'Valued Customer' }},

Thank you for choosing {{ config('app.name') }}! We're excited to have you join our {{ $packageDetails['name'] ?? 'package' }} and can't wait to help you find your perfect match.

## Your Package Includes:

@component('mail::panel')
@if(isset($packageDetails['features']) && is_array($packageDetails['features']))
    @foreach($packageDetails['features'] as $feature)
        * {{ $feature }}
    @endforeach
@else
    * Package features will be displayed here
@endif
@endcomponent

## Next Steps:
1. Complete your profile if you haven't already
2. Select your matching criteria (up to {{ $packageDetails['criteria'] ?? '0' }})
3. Wait for our matchmaker to contact you

@component('mail::button', ['url' => config('app.url') . '/client/dashboard'])
Go to Dashboard
@endcomponent

If you have any questions about your package or need assistance, don't hesitate to reach out to our support team.

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent