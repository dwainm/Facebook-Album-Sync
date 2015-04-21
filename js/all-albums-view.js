/**
 * The javascript fetches all albums and their artwork and displays thme in a Grid
 *
 * The albums all link to a page with their specific potos
 */
( function( $ , _ , Backbone ){
   
    //facbookAlbumsSync is localized through WordPress
    var ALbumsCount = 1;
    var rowItemscnt = 1;
    var excludeAlbums = facbookAlbumsSync.excludeAlbums;

    for (albumid in excludeAlbums){
        excludeAlbums[albumid] = excludeAlbums[albumid].trim();
    }

    var fbpagename = facbookAlbumsSync.facebookPageName;

    var prettypermalinkon = facbookAlbumsSync.prettyPermalinks ;
    var curhtml ="";
    var loadingImage = "<div id='fbloader'></div>" ;
    
    addhtml(loadingImage);
    
    fbGraphUrl = "https://graph.facebook.com/"+fbpagename+"/albums/"

    getAlbumPage( fbGraphUrl )
  
    function addhtml(html){
        /* Write the text */
        document.getElementById('fbalbumsync').innerHTML += html;
    }

    function getAlbumPage( apiUrl )
    {
          $.ajax({
        dataType: 'jsonp',
        url: apiUrl,
        type: 'GET',
        success: function getpages(data){
            getAlbums(data,0);
        },
        error: function( data ) {
            console.log( "Error: Facebook\'s Graph API might be down. \n" + data );
        }
    });
      
    }
// Note to developer:
// There something wrong with this code
// it calls the facbook to get the cover photo url
// is there a way to get the url by just using standard url
// structure? like just plug it in http://facebook.com/photo/".id."/
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

} ( jQuery , _ , Backbone) ) ;