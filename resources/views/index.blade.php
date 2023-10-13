@extends('layouts.app')

@section('title') Home @endsection

@section('content')
    <div class="row">
        <div class="col-xxl-4">
            <div class="card bg-body-secondary mb-4">
                <div class="card-body">
                    <h5>Upload an Image</h5>
                    {!! Form::open([ 'url' => 'images/upload', 'id' => 'imageForm', 'files' => true,]) !!}

                    <div class="card bg-body-tertiary mb-2 d-none" id="imageContainer">
                        <div class="card-body text-center">
                            <img src="#" id="image" style="max-width:100%; max-height:25vh;" alt="Uploaded image preview" />
                        </div>
                    </div>

                    <div class="p-2">
                        {!! Form::label('mainImage', 'Upload File') !!}
                        {!! Form::file('image', ['id' => 'mainImage']) !!}<br/>
                        <small>Images may be PNG, GIF, JPEG, or WebP and up to
                            {{ min((int)ini_get('upload_max_filesize'), (int)ini_get('post_max_size'), '17') }}MB in size.</small>
                    </div>

                    <div class="text-end">
                        {!! Form::submit('Upload', ['class' => 'btn btn-primary']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col">
            <div class="d-flex align-content-around flex-wrap flex-md-row mb-2">
                @foreach ($images->split(4) as $group)
                    @foreach ($group as $image)
                        @include('images._image_thumb')
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    @include('images._info_popup_js')
    <script type="module">
        var $image = $('#image');

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $image.attr('src', e.target.result);
                    $('#imageContainer').removeClass('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#mainImage").change(function() {
            readURL(this);
        });
    </script>
@endsection
