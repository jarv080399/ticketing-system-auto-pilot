<x-mail::message>
# Daily Support Performance Metrics

Here is the performance summary for yesterday:

- **Total Tickets:** {{ $metrics['total_tickets'] }}
- **Resolved:** {{ $metrics['resolved_tickets'] }}
- **Avg Resolution Time:** {{ $metrics['avg_resolution_time'] }} mins
- **SLA Compliance:** {{ $metrics['sla_compliance_rate'] }}%
- **Avg CSAT Score:** {{ $metrics['avg_csat_score'] }} / 5

<x-mail::button :url="config('app.url') . '/admin'">
View Full Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
