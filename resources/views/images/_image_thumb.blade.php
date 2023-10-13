<div class="flex-fill img-fluid align-self-center text-center m-1">
    <div class="container">
        <div class="content align-self-center bg-dark-subtle rounded" style="min-height:100px;">
            <a class="align-self-center image-link" href="{{ url('images/view/'.$image->slug) }}">
                <div class="content-overlay"></div>
                <div class="text-center align-self-center my-auto">
                    <img src="{{ $image->thumbnailUrl }}"
                        class="p-2"
                        style="width: auto; height: {{ config('alcyone.settings.thumbnail_height') }}px"
                        alt="Uploaded image thumbnail" />
                </div>
            </a>
        </div>
    </div>
</div>
