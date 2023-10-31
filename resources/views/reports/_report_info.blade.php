<div class="row mb-4">
    @if($report->status != 'Accepted')
        <div class="col-md-4 col-xxl-3">
            <div class="container">
                <div class="content align-self-center bg-dark-subtle rounded" style="min-height:100px;">
                    <a class="align-self-center image-link" data-lightbox="entry" data-title="{{ 'Reported Image '.$report->image->slug }}" href="{{ $report->image->imageUrl }}">
                        <div class="content-overlay"></div>
                        <div class="text-center align-self-center my-auto">
                            <img src="{{ $report->image->thumbnailUrl }}"
                                class="p-2 reported-image"
                                style="width: auto; height: {{ config('alcyone.settings.thumbnail_height') }}px"
                                alt="Uploaded image thumbnail" />
                        </div>
                    </a>
                </div>
            </div>
            <p><i>Image is obscured for safety. Hover to reveal its contents.</i></p>
        </div>
    @endif
    <div class="col-md">
        <div class="row">
            <div class="col-md-4">
                <h5>Reported Image</h5>
            </div>
            <div class="col-md">
                {{ $report->image->slug }}
            </div>
        </div>
        @if(isset($isAdmin) && $isAdmin)
            <div class="row">
                <div class="col-md-4">
                    <h5>Uploaded By</h5>
                </div>
                <div class="col-md">
                    {!! $report->image->user->displayName !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h5>Reported By</h5>
                </div>
                <div class="col-md">
                    {!! $report->reporter->displayName !!}
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-4">
                <h5>Reason Given</h5>
            </div>
            <div class="col-md">
                {{ $report->reason }}
            </div>
        </div>
        @if(isset($isAdmin) && $isAdmin)
            <div class="row">
                <div class="col-md-4">
                    <h5>Processed By</h5>
                </div>
                <div class="col-md">
                    {!! $report->staff ? $report->staff->displayName : '' !!}
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-4">
                <h5>Reported</h5>
            </div>
            <div class="col-md">
                {!! pretty_date($report->created_at) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h5>Last Updated</h5>
            </div>
            <div class="col-md">
                {!! pretty_date($report->updated_at) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h5 class="mt-2">Report URL</h5>
            </div>
            <div class="col-md">
                @include('widgets._url_display', ['url' => $report->url, 'urlName' => 'report_url', 'formClass' => ''])
            </div>
        </div>
    </div>
</div>
