/**
 * Facebook Album sync Settings JS
 *
 */
(function( $, _ , Backbone ){

    /**
     * Submit click event handler
     */
    $('[name=fbas_page]').on('blur', function( e ){

        e.preventDefault();
        var loadingImg = $('#fbas_loading');
        var pageNameField = $('[name=fbas_page]');
        var submit = $( '#submit' );


        // show the loading icon
        submit.attr('disabled', 'disabled');
        loadingImg.removeClass('hidden');

        if( _.isEmpty( pageNameField.val() ) ){
            loadingImg.addClass('hidden');
            pageNameField.addClass('error');
            submit.removeAttr('disabled');
            return false;
        }

        //try to reach the page on fb
        var pageApiUrl = 'https://graph.facebook.com/'+ pageNameField.val();

        $.ajax({
            dataType: 'jsonp',
            url: pageApiUrl,
            type: 'GET'
        }).done( function ( data ){

            if( _.isEmpty( data.error ) ){

                pageNameField.addClass('success');
                pageNameField.removeClass('error');

            }else{
                pageNameField.addClass('error');
                pageNameField.removeClass('success');
            }

            loadingImg.addClass('hidden');
            submit.removeAttr('disabled');
        });


    });

}( jQuery, _ , Backbone ));