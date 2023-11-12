<x-mail::message>

A new content report has been submitted for image #{{ $image->id }}.

<x-mail::button :url="$report->url" color="primary">
    View Report
</x-mail::button>

<hr /><br />

@include('mail._footer')

</x-mail::message>
