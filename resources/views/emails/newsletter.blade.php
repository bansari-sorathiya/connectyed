@component('mail::message')
# Latest Updates

{!! $content !!}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
