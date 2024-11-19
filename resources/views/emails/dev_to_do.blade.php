@component('mail::message')

# Hi {{ $details['who'] }},
Please do action for Task #{{ $details['d_id'] }}
@component('mail::panel')
{{ $details['title'] }}
@endcomponent

@component('mail::table')
| TYPE          | STATUS        |
| ------------- |:-------------:|
| {{ $details['task_type'] }}   | {{ $details['task_status'] }} |
@endcomponent

@component('mail::button', ['url' => url($details['url']),'color' => 'error'])
Go to Asset
@endcomponent

Thanks,<br>
KDO Project Manager

<small><i>This email address will not receive replies. If you have any questions, please contact Mo Tuhin or Vincent "Vinny" Cerone.</i></small>
@endcomponent
