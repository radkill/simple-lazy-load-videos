/**
 * Send AJAX request
 */
/*
function initAjax() {
  $('.js-form').on('submit', function(e){
    e.preventDefault();
    let form     = $(this);
    let action   = form.data('action');
    let postdata = form.serialize();
    let button   = form.find('input[type=submit], button[type=submit]');

    button.prop('disabled', true);
    form.addClass('g-loading');

    $.ajax({
      type: 'POST',
      url: ncVar.ajax_url,
      data: {
        'postdata' : postdata,
        'action'   : action,
      },
      dataType: 'json',
      success: function(result) {
        if ( result.success == true ) {
          console.log( result.data );
        } else {
          console.warn( result.data.error );
        }

        button.prop('disabled', false);
        form.removeClass('g-loading');
      }
    });
  });
}
*/
