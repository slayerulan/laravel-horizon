@component('mail::message')


PLEASE CLICK ON THE BELOW BUTTON TO RESET YOUR PASSWORD

@component('mail::button', ['url' => $url])
Click here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
