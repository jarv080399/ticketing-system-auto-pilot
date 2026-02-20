<x-mail::message>
# Weekly SLA Compliance Report

Summary for the period: **{{ $slaData['period'] }}**

Your team achieved an SLA compliance rate of:
# {{ $slaData['rate'] }}%

<x-mail::button :url="config('app.url') . '/admin'">
Investigate Breaches
</x-mail::button>

Best regards,<br>
{{ config('app.name') }} Automated Reporting
</x-mail::message>
