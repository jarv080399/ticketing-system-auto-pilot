<x-mail::message>
# How did we do?

Hello {{ $ticket->requester->name }},

Your ticket **#{{ $ticket->ticket_number }}: {{ $ticket->title }}** has been closed. We'd love to hear about your experience with our support team.

Your feedback helps us improve our service!

<x-mail::button :url="$surveyUrl">
Rate My Experience
</x-mail::button>

If the button above doesn't work, you can copy and paste this link into your browser:
[{{ $surveyUrl }}]({{ $surveyUrl }})

Thanks,<br>
{{ config('app.name') }} IT Team
</x-mail::message>
