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

        isLoading: function(){

          if( this.loadingQueue.length > 0  ){
              return true;
          }  else{
              return false;
          }

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

			    // Check if the api has more albums avaialbe then fetch them
			    if( ! _.isEmpty( apiJson.paging.next) ){
			    	thisCollection.fetchAlbums(  apiJson.paging.next );
			    }

	    	}); // end ajax call

	    }, // end fetchAlbums
    
    }); // end collection

}( jQuery, _ , Backbone ));

function getAlbums(data, i)	{

        //skip if album is excluded
        arrayIndex = $.inArray( data.data[i].id  , excludeAlbums );

        if(arrayIndex !== -1 ){

            if (i < data.data.length - 1){
                
                i++;
                getAlbums(data, i );
                return;
                
            }

        }else{

// album by album get the links and then process the albums
// one by one
            try{ // test if album as a cover photo
                var coverPhotograph = 'https://graph.facebook.com/'+data.data[i].cover_photo;

            }catch(err){
                addhtml(curhtml);
                curhtml ="";
                console.log(err);
            }

            $.ajax({
                dataType: 'jsonp',
                url: coverPhotograph,
                type: 'GET',
                success:function(coverPhotoData){
                    //determine if the data has a cover photo field
                    // if not increment the counter and check the next photo

                    if( !(data.data[i].type ==='normal' || data.data[i].type ==='mobile') ){
                        //skipe this album as it is of type cover, wall and profile
                        i++;
                        getAlbums(data, i );
                    }

                    try{

                        var imgsrc =  coverPhotoData.picture;

                        var albumname = data.data[i].name;

                        var photoslink = "";
                        //start the row if the item count less than 1
                        if (rowItemscnt == 1)
                        {
                            curhtml += "<div class=\"row \">";

                        }

                        if( prettypermalinkon ){
                            photoslink = document.URL+"?fbasid="+data.data[i].id;
                        }else{
                            photoslink = document.URL+"&fbasid="+data.data[i].id;

                        }

                        //print output
                        if(rowItemscnt==4){
                            curhtml +="<div class=\"threecol "+rowItemscnt +" last\"><a class=\"albumlink\" href=\""+photoslink+ "\" ><img class=\"albumthumb\" src=\""+ imgsrc +" \" /></a><br/><a class=\"albumlinktitle\"  href=\""+photoslink+ "\">"+ albumname +"</a></div> <!-- last col -->";
                            curhtml += " </div> <!-- End Row Loop-->" ;
                            // output row
                            addhtml(curhtml);
                            curhtml ="";
                            rowItemscnt = 0;
                        }else{
                            curhtml +="<div class=\"threecol "+rowItemscnt +" \"><a class=\"albumlink\"  href=\""+photoslink+ "\" ><img class=\"albumthumb\" src=\""+ imgsrc +" \" /></a><br/><a class=\"albumlinktitle\"  href=\""+photoslink+ "\">"+ albumname +"</a></div>";
                        }

                        // print out current image block
                        //addhtml(curhtml);

                        //increment counters
                        rowItemscnt ++;
                        //End ALbum Image processing

                        //check if the if there are more albums left
                        if (i < data.data.length - 1){
                            i++;
                            //console.log("<< Get Next Album>>");
                            getAlbums(data, i );
                        }else
                        {
                            //check if there is another page
                            try
                            {


                                //get the next page
                                if( !$.isEmptyObject(data.paging.next))
                                {
                                    getAlbumPage(data.paging.next);

                                }else{
                                    $('#fbloader').remove();
                                }
                                $('#fbloader').remove();
                            }
                            catch(err)
                            {
                                if (rowItemscnt<4){
                                    // close the row if there are less than four items at the end of all albums
                                    curhtml += "</div> <!-- End Row try Catch-->";
                                    addhtml(curhtml);
                                    curhtml ="";
                                    rowItemscnt = 1;
                                }
                                //Handle errors here
                                //console.log("child no more catch: "+err);
                                addhtml("<h4>No More Pages</h4>");
                                $('#fbloader').remove();
                            }

                        }
                    }catch(err){ // End of 25 Albums catch
                        i++;
                        console.log(err);
                        if(	!( i in obj) ){
                            $('#fbloader').remove();
                        } else{
                            // get the next batch of albums
                            getAlbums(data, i );
                        }

                    }


                },
                error: function( data ) {
                    $('#fbloader').remove();
                    console.log("Error: Facebook\'s Graph API might be down."+data);
                }
            });

            //process the data from facebook
        }

    }
