<div class="card bg-body-secondary rounded" style="width:90%; margin-left:5%;">
    <div class="row">
        <div class="col-md card-header bg-body-tertiary rounded">
            <div class="text-center m-auto">
                <img src="{{ $image->imageUrl }}" class="mw-100 mb-2" style="max-height: 85vh;" />
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col">
            <div class="card-body ps-md-0">
                <div class="mb-3">
                    {{ html()->label(!$image->is_gif ? 'For Web: (WebP)' : 'URL: (GIF)', 'web_url')->class('form-label') }}
                    @include('widgets._url_display', ['url' => $image->imageUrl, 'urlName' => 'web_url', 'formClass' => 'bg-body'])
                </div>
                @if (!$image->is_gif)
                    <div class="mb-3">
                        {{ html()->label('For Sharing: (PNG)', 'share_url')->class('form-label') }}
                        @include('widgets._url_display', ['url' => url('images/converted/' . $image->slug), 'urlName' => 'share_url', 'formClass' => 'bg-body'])
                    </div>
                @endif
                <hr />
                Uploaded {!! $image->created_at->format('d F Y') !!}
                <div id="deleteImage" class="d-none mt-3">
                    {{ html()->form('POST', 'images/delete/'.$image->slug)->open() }}

                    You are about to delete this image. This is not reversible.<br />
                    Are you sure you want to delete this image?

                    <div class="text-end mt-2">
                        <a href="#" onclick="$(this).parent().parent().parent().addClass('d-none'); $('#deleteImageBtn').removeClass('d-none');" class="btn btn-sm btn-info me-2">Cancel</a>
                        {{ html()->submit('Confirm Delete Image')->class('btn btn-sm btn-danger') }}
                    </div>

                    {{ html()->form()->close() }}
                </div>
                <div id="deleteImageBtn" class="text-end align-bottom mt-3">
                    <a href="#" onclick="$(this).parent().addClass('d-none'); $('#deleteImage').removeClass('d-none');" class="btn btn-sm btn-danger">Delete Image</a>
                </div>
            </div>
        </div>
    </div>
</div>
