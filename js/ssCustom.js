$(document).ready(function (e) {
    $('style').remove();
    $('script[type*="text/javascript"]').remove();
});
$('#ssModelCenter').on('show.bs.modal', function (e) {
    // do something...
    var clickElement=e.relatedTarget;
    var userId=$(clickElement).data('userid');
    $('#ssModelCenterTitle').html($(clickElement).data('name'));
    $('.ss-body .modal-body .ssContent').html('');
    $('.ss-body .modal .ss-loader').addClass('d-flex').css({'display':'flex!important'});
    $.ajax({
        url : ssCustomAjax.ajax_url,
        type : 'post',
        data : {
            action : 'ssFnGetUserPosts',
            user_id : userId
        },
        success : function (response) {
            $('.ss-body .modal .ss-loader').removeClass('d-flex').css({'display':'none'});
            $('.ss-body .modal-body .ssContent').html(response);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('.ss-body .modal .ss-loader').removeClass('d-flex').css({'display':'none'});
            if (xhr.status===500) {
                $('.ss-body .modal-body .ssContent').html(xhr.status+': '+thrownError+'<p>There are some issues with the API from server side, Please try again.</p>');
            }

        }
    });
})