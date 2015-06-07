/**
 * Facebook Album sync Settings JS
 *
 */
(function( $, _ , Backbone ){


    // global DOM elements
    var pageNameField = $('[name=fbas_page]');
    var albumsHolder = $('#fbas-albums');
    var refreshButton = $('#fbas-refresh');

    var SettingsModel = Backbone.Model.extend({

        defaults: {
            "validPageName":  Boolean(facbookAlbumsSync.validPageName)
        }

    });
    var settingsModel = new SettingsModel();


    var albumsCollection = new  fbas.AlbumCollection();

    /**
     * Listen to the fetch complete event
     * @returns {boolean}
     */
    var fetchComplete = function(){

            // make sure there are models
            if( ! albumsCollection.length > 0 ){
                return false;
            }

            var data = {
                action: 'save_settings',
                albums: albumsCollection.toJSON()
            };

            //ajax call to save the models json
            $.post( ajaxurl, data );

    };
    // storing the data model within WordPress for easy access
    albumsCollection.on('fetchCompleted', fetchComplete );

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

                // save setting

                var settings =  settingsModel.toJSON();
                settings.action = 'save_settings';

                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data:   settings,
                    success: function( data ){
                        console.log(data);
                    }
                });

            }else{
                settingsModel.set('validPageName', false );
                pageNameField.addClass('error');
                pageNameField.removeClass('success');
            }

            loadingImg.addClass('hidden');
            submit.removeAttr('disabled');
        });


    });

    /**
     * Check if there is valid page name
     * Notify the user of this error
     */
    var hasValidPageName = function(){

        if( false == settingsModel.get('validPageName') ){

            pageNameField.addClass('error');
            pageNameField.focus();
            $(this).removeAttr('checked');
            return false;

        }else{
            return true;
        }

    };

    /**
     * Refresh the list of list item checkboxes
     */
    var refreshList = function ( e ) {

        albumsCollection.reset();
        albumsHolder.find( '.fbas-albums-list' ).empty();
        var pageAlbumsApiUrl = 'https://graph.facebook.com/'+ pageNameField.val()+"/albums/";
        albumsCollection.fetchAlbums( pageAlbumsApiUrl );

    };

    $('#fbas_exclude_albums').change( function( e ){

        if( 'fbas_exclude_albums' != e.target.id ){

            return;

        }

        var checkbox = $(e.target);
        // exit if not checked
        if( ! checkbox.is(':checked') ){

            albumsHolder.addClass('hidden');
            refreshButton.addClass('hidden');

        }else{

            if( ! hasValidPageName() ){
                return false;
            }

            refreshButton.removeClass('hidden');
            albumsHolder.removeClass('hidden');

            // update the list
            refreshList();

        }
    });

    $( '#fbas-refresh' ).on( 'click', function( e  ){

        if( ! hasValidPageName() ){
            return false;
        }

        // update the list
        refreshList();
        return true;

    });



}( jQuery, _ , Backbone ));