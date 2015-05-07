
/**
 * Facebook Album Sync - Album Model and Collection
 *
 * The model is the foundation of Facebook Album Sync. It will store all the album information.
 */

(function( $, _ , Backbone ){

    fbas = window.fbas || {};

    fbas.AlbumModel = Backbone.Model.extend({

        initialize: function( newData, collection ){
            if( this.attributes.cover_photo ) {

                // notify the collection that this model is loading
                collection.addToLoadingQueue( this );
                var  coverPhotoApiUrl = 'https://graph.facebook.com/' + this.attributes.cover_photo
                $.ajax({
                    dataType: 'jsonp',
                    url: coverPhotoApiUrl,
                    type: 'GET'
                }).done( _.bind( this.processCoverPhoto, this) );

            }
        },
        /**
         * Respond to the Ajax request get the album cover url
         */
        processCoverPhoto: function( ajaxData ){

            this.set( { photoUrl: ajaxData.images[0].source } );
            this.collection.removeFromLoadingQueue( this );

        }
    });
// a collection for all albums
    fbas.AlbumCollection = Backbone.Collection.extend({

        initialize: function(){
            // loading queue that determine
            // if loading can be hidden or shown
            this.loadingQueue = [];
        },
        /**
         * Push model on top of the loading queue and
         * trigger updated event.
         */
        addToLoadingQueue: function( model ){

            this.loadingQueue.push( model.id );
            this.trigger('update');

        },
        /**
         * Pop the model from the loading queue and trigger
         * the update event.
         */
        removeFromLoadingQueue: function( model ){
            this.loadingQueue.pop( model.id );
            this.trigger('update');
        },
        sync: function(){

            // triger sync start
            this.trigger('syncStart');

            // get albums for storage
            this.fetchAlbums();

            // triger sync start
            this.trigger('syncEnd');

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
         * Get all ablums and store it in this collection
         */
        fetchAlbums: function ( url ){


            this.addToLoadingQueue( this );

            // set up the url for the next api call
            // if the url is not by the calling funciton
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
                _.each(  apiJson.data ,function( albumJson, index ){
                    var model = new fbas.AlbumModel(albumJson, thisCollection );
                    thisCollection.add( model );

                });

                thisCollection.removeFromLoadingQueue( thisCollection );
                // Check if the api has more albums avaialbe then fetch them
                if( ! _.isEmpty( apiJson.paging.next) ){
                    thisCollection.fetchAlbums(  apiJson.paging.next );
                }

            }); // end ajax call

        }, // end fetchAlbums

    }); // end collection

}( jQuery, _ , Backbone ));