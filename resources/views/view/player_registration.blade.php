@component('mail::message')

YOUR USERNAME IS {{ $username }}
YOUR PASSWORD IS {{ $password }}

@if($url)
PLEASE CLICK ON THE BELOW BUTTON TO ACTIVATE YOUR ACCOUNT

@component('mail::button', ['url' => $url])
Click here
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
