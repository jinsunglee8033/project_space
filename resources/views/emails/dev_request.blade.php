@component('mail::message')

# Hi {{ $details['who'] }},
Your request has been received by the Dev team. #{{ $details['d_id'] }}
@component('mail::panel')
{{ $details['task_name'] }}
@endcomponent

@component('mail::table')
| TYPE          | STATUS        | Task ID  |
| ------------- |:-------------:| ---------:|
| {{ $details['task_type'] }}   | {{ $details['task_status'] }} | {{ $details['d_id'] }}|
@endcomponent

@component('mail::button', ['url' => url($details['url']),'color' => 'error'])
Go to Task
@endcomponent

Thanks,<br>
KDO Project Manager

<small><i>This email address will not receive replies. If you have any questions, please contact Mo Tuhin or Vincent "Vinny" Cerone.</i></small>
@endcomponent
