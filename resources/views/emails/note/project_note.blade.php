@component('mail::message')

# You got a new message from {{ $details['who'] }}
<b style="font: bold;">Project : {{ $details['p_title'] }}</b>

@component('mail::panel')
{{ $details['message'] }}
@endcomponent

@component('mail::button', ['url' => url($details['url']),'color' => 'error'])
Go to Project
@endcomponent

Thanks,<br>
Project Space

<small><i>This email address will not receive replies.</i></small>
@endcomponent
