<x-mail::message>

Thank you for submitting a report. Your report has been reviewed and the reported image found not to violate the <a href="{{ url('info/terms') }}">Terms of Service</a>. Please see the report for any further information.

<x-mail::button :url="$report->url" color="primary">
    View Report
</x-mail::button>

<hr /><br />

@include('mail._footer')

</x-mail::message>
