/**
 * Facebook Album Sync - Photo Model and Collection
 *
 * The model is the foundation of Facebook Album Sync single album page. It
 * will manage the state and ajax functionality of all photos.
 */
(function( $, _ , Backbone ){

    fbas = window.fbas || {};
    var shortcode_used = facbookAlbumsSync.singleAlbumShortcode;

    fbas.PhotoModel = Backbone.Model.extend({
        initialize: function( newData, collection ){
            var images = this.get('images');

            this.largestImage = images[ 0 ];

            this.standardImage = images[ images.length-1 ];
            _.each( images, function( currentImage ){
                if( currentImage.width > 300 && currentImage.width < 500 ){
                    this.standardImage = currentImage;
                }
            } );
        },
    });

    fbas.PhotoCollection = Backbone.Collection.extend({

        initialize: function(){
            this.albumId = this.getUrlAlbumId('fbasid');
            this.isLoading = true;
        },

        /**
         *  The ajax function that wraps jQuery and returns a promise
         */
        ajax : function( url ){

            // return the defered object
            // but ensure that this is bind to to the collection and
            // not the global windoew object
            return 	$.ajax({
                dataType: 'jsonp',
                url: url,
                type: 'GET',
                error: function( data ) {
                    console.log( "Error: Facebook\'s Graph API might be down. \n" + data );
                }
            });

        },

        /**
         * Get the url parameter value
         *
         * @param urlVarName
         * @returns string urlVarValue
         */
        getUrlAlbumId: function ( urlVarName ) {
            name = urlVarName.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
            var regexS = "[\\?&]"+name+"=([^&#]*)";
            var regex = new RegExp( regexS );
            var results = regex.exec( window.location.href );
            if( results == null ) {
                return "";
            }else {
                return results[1];
            }
        },

        /**
         * get all the photos for the
         * current album
         *
         * @param url
         */
        fetchPhotos: function ( url ){

            this.isLoading = true;
            this.trigger('update');
            // set up the url for the next api call
            // if the url is not by the calling function
            // the models url will be used ad the default.
            var apiUrl = this.url;
            if( ! _.isEmpty( url ) ){
                apiUrl = url;
            }

            // reference to this to be used inside ajax done callback
            var thisCollection = this;

            //fetch the data for this request
            this.ajax( apiUrl ).done( function ( apiJson ) {

                // store the album data
                _.each(  apiJson.data ,function( photoJson, index ){

                    var model = new fbas.PhotoModel(photoJson, thisCollection );
                    thisCollection.add( model );

                });

                //thisCollection.removeFromLoadingQueue( thisCollection );
                // Check if the api has more albums avaialbe then fetch them
                if( ! _.isEmpty( apiJson.paging.next) ){
                    thisCollection.fetchPhotos(  apiJson.paging.next );
                }

                thisCollection.isLoading = false;
                thisCollection.trigger('update');
            }); // end ajax call

        }, // end fetchPhotos
    });

}( jQuery, _ , Backbone ));