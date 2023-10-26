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
                    {!! Form::label('web_url', 'For Web: (WebP)', ['class' => 'form-label']) !!}
                    <div class="input-group">
                        {!! Form::text('web_url', $image->imageUrl, ['class' => 'form-control bg-body', 'disabled']) !!}
                        <span class="input-group-text"><i data-toggle="tooltip" title="Click to Copy" onclick="copyUrl($(this), '{{ $image->imageUrl }}');" class="far fa-copy fs-5 my-auto copy-url"></i></span>
                    </div>
                </div>
                <div class="mb-3">
                    {!! Form::label('share_url', 'For Sharing: (PNG)', ['class' => 'form-label']) !!}
                    <div class="input-group">
                        {!! Form::text('share_url', url('images/converted/'.$image->slug), ['class' => 'form-control bg-body', 'disabled']) !!}
                        <span class="input-group-text"><i data-toggle="tooltip" title="Click to Copy" onclick="copyUrl($(this), '{{ url('images/converted/'.$image->slug) }}');" class="far fa-copy fs-5 my-auto"></i></span>
                    </div>
                </div>
                <hr/>
                Uploaded {!! $image->created_at->format('d F Y') !!}
                <div id="deleteImage" class="d-none mt-3">
                    {!! Form::open(['url' => 'images/delete/' . $image->slug]) !!}

                    You are about to delete this image. This is not reversible.<br/>
                    Are you sure you want to delete this image?

                    <div class="text-end mt-2">
                        <a href="#" onclick="$(this).parent().parent().parent().addClass('d-none'); $('#deleteImageBtn').removeClass('d-none');" class="btn btn-sm btn-info me-2">Cancel</a>
                        {!! Form::submit('Confirm Delete Image', ['class' => 'btn btn-sm btn-danger']) !!}
                    </div>

                    {!! Form::close() !!}
                </div>
                <div id="deleteImageBtn" class="text-end align-bottom mt-3">
                    <a href="#" onclick="$(this).parent().addClass('d-none'); $('#deleteImage').removeClass('d-none');" class="btn btn-sm btn-danger">Delete Image</a>
                </div>
            </div>
        </div>
    </div>
</div>
