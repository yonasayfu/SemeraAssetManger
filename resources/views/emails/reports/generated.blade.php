<x-mail::message>
# {{ $reportName }}

Your scheduled report "{{ $reportName }}" has been generated.

@if($reportResults->isNotEmpty())
Here's a summary of the first few results:

<x-mail::table>
| Asset Tag | Description | Status |
| :-------- | :---------- | :----- |
@foreach($reportResults->take(5) as $result)
| {{ $result->asset_tag ?? 'N/A' }} | {{ $result->description ?? 'N/A' }} | {{ $result->status ?? 'N/A' }} |
@endforeach
</x-mail::table>
@else
No data was found for this report based on the current filters.
@endif

You can view the full report by logging into the system.

<x-mail::button :url="url('/reports')">
View Reports
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
