@component('mail::message')
# Register Camp: {{ $checkout->Camp->title }}

Hi {{ $checkout->User->name }}
<br>
Thank you for register on <b>{{ $checkout->Camp->title }}</b>, please see payment instruction by click the button below.

@component('mail::button', ['url' => route('dashboard.invoice')])
Get Invoice
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
