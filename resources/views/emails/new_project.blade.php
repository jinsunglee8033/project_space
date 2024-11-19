@component('mail::message')

# Hi Admin,
New Project #{{ $details['c_id'] }} was created by <br>
<span style="color:#ffffff; font-size: medium;background-color: #933434;border-radius: 6px;">&nbsp;{{ $details['creator'] }}&nbsp;</span> from
<b style="color: #b91d19">{{ $details['team'] }} </b> Team
@component('mail::panel')
{{ $details['task_name'] }}
@endcomponent

@component('mail::button', ['url' => url($details['url']),'color' => 'error'])
Go to Project
@endcomponent

Thanks,<br>
KDO Project Manager

<small><i>This email address will not receive replies. If you have any questions, please contact Mo Tuhin or Vincent "Vinny" Cerone.</i></small>
@endcomponent
