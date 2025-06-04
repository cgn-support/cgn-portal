@component('mail::message')
# Welcome to Our Application!

Thanks for signing up, {{ $notifiable->name }}.

We're excited to have you on board.

@component('mail::button', ['url' => url('/')])
Get Started
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent