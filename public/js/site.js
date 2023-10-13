function loadModal(url, title) {
    $('#modal').find('.modal-body').html('');
    $('#modal').find('.modal-title').html(title);
    $('#modal').find('.modal-body').load(url, function( response, status, xhr ) {
        if ( status == "error" ) {
            var msg = "Error: ";
            $( "#modal" ).find('.modal-body').html( msg + xhr.status + " " + xhr.statusText );
        }
        else {
            $('#modal [data-bs-toggle=tooltip]').map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }
    });
}

async function copyUrl(target, url) {
    await window.navigator.clipboard.writeText(url);
    target.removeClass('toCopy');
    target.addClass('toCheck');
    setTimeout(() => {
        target.removeClass('toCheck');
        target.addClass('toCopy');
    }, 2000);
}
