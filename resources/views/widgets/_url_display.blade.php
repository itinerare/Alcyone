<div class="input-group">
    {!! Form::text($urlName ?? 'url', $url, ['class' => 'form-control ' . $formClass ?? '', 'disabled']) !!}
    <span class="input-group-text"><i data-toggle="tooltip" title="Click to Copy" onclick="copyUrl($(this), '{{ $url }}');" class="far fa-copy fs-5 my-auto"></i></span>
</div>
