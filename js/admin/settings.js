/**
 * Facebook Album sync Settings JS
 *
 */
(function( $, _ , Backbone ){


    var SettingsModel = Backbone.Model.extend({

        defaults: {
            "validPageName":  false
        }

    });
    var settingsModel = new SettingsModel();


    var albumsCollection = new  fbas.AlbumCollection();

    /**
     * Add albums to list
     */
    var albumList = $('#fbas-albums .fbas-albums-list');
    albumsCollection.on( 'add', function(model){

        var checkboxName = 'fbas_excluded_ids['+ model.get('id') +']';
        var li = '<li> <input type="checkbox" name="'+ checkboxName +'" id="'+ model.get('id') +'"  />'
            + '<label for="' + model.get('id') + '" >'+ model.get('name') + '</label>'
            +'</li>';

        albumList.append( li );

    });

    /**
     * Ensure that the album list spans 2 columns
     */

    /**
     * Submit click event handler
     */
    $('[name=fbas_page]').on('blur', function( e ){

        e.preventDefault();
        var loadingImg = $('#fbas_page_name_loading');
        var pageNameField = $('[name=fbas_page]');
        var submit = $( '#submit' );
        var pageApiUrl = 'https://graph.facebook.com/'+ pageNameField.val();

        // show the loading icon
        submit.attr('disabled', 'disabled');
        loadingImg.removeClass('hidden');

        if( _.isEmpty( pageNameField.val() ) ){
            loadingImg.addClass ('hidden');
            pageNameField.addClass('error');
            submit.removeAttr('disabled');
            return false;
        }

        //try to reach the page on fb
        $.ajax({
            dataType: 'jsonp',
            url: pageApiUrl,
            type: 'GET'
        }).done( function ( data ){

            if( _.isEmpty( data.error ) ){
                settingsModel.set('validPageName', true);
                pageNameField.addClass('success');
                pageNameField.removeClass('error');

            }else{
                settingsModel.set('validPageName', false );
                pageNameField.addClass('error');
                pageNameField.removeClass('success');
            }

            loadingImg.addClass('hidden');
            submit.removeAttr('disabled');
        });


    });

    $('#fbas_exclude_albums').change(function ( e ) {

        var albumsHolder = $('#fbas-albums');
        // exit if not checked
        if(! $(this).is(':checked')){
            albumsHolder.addClass('hidden');
            return false;
        }else{

            var pageNameField = $('[name=fbas_page]');
            if( false == settingsModel.get('validPageName') ){
                pageNameField.addClass('error');
                pageNameField.focus();
                $(this).removeAttr('checked');
                return false;
            }

            albumsHolder.removeClass('hidden');
            var pageAlbumsApiUrl = 'https://graph.facebook.com/'+ pageNameField.val()+"/albums/";
            albumsCollection.fetchAlbums( pageAlbumsApiUrl );

        }

    });

}( jQuery, _ , Backbone ));