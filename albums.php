<?php
//echo 'Facebook PLUGIN:<br>';
//echo 'FACEBOOK ALBUMS FOR '.get_option('fbas_page');
//echo "Option: ".var_dump(get_option( 'rewrite_rules' ));
?>
<div id="fbalbumsync" class="container">

</div>
<script type="text/javascript">
					var ALbumsCount = 1;
					var rowItemscnt = 1;
					var fbpagename = "<?php echo get_option('fbas_page') ?>";
					var prettypermalinkon = "<?php echo $prettypermalinkon; ?>"; 
					var curhtml ="";
					var loadingImage = "<br /><img id=\"fbloader\"src=\"<?php echo plugins_url();?>/facebook-album-sync/images/fbloader.gif\" /><br />" ;
					addhtml(loadingImage);			
					getAlbumPage("https://graph.facebook.com/"+fbpagename+"/albums/");
					

					function addhtml(html){
						/* Write the text */
						//console.log("do print: "+html);
						document.getElementById('fbalbumsync').innerHTML += html;
					}
					function getAlbumPage(fbGraphUrl)
					{
						jQuery.ajax({
							dataType: 'jsonp',
							url: fbGraphUrl,
							type: 'GET',
							success: function getpages(data){
								getAlbums(data,0);
							},
							error: function( data ) {
								console.log("Error: Facebook\'s Graph API might be down."+data);
							}
						});
					}
					// Note to developer:
					// There something wrong with this code
					// it calls the facbook to get the cover photo url 
					// is there a way to get the url by just using standard url 
					// structure? like just plug it in http://facebook.com/photo/".id."/
					function getAlbums(data, i)
					{
					// album by album get the links and then process the albums 
					// one by one
							try{ // test if album as a cover photo
									var coverPhotograph = 'https://graph.facebook.com/'+data.data[i].cover_photo;
							
								}catch(err){
									addhtml(curhtml);
									curhtml ="";
									console.log(err);
								}
								
							jQuery.ajax({
								dataType: 'jsonp',
								url: coverPhotograph,
								type: 'GET',
								success:function(coverPhotoData){
								    //determine if the data has a cover photo field 
								    // if not increment the counter and check the next photo
								    try{					    										
										var imgsrc =  coverPhotoData.images[5].source;
										var albumname = data.data[i].name;
										//console.log(albumname+"=>"+imgsrc);
										var photoslink = "";
								        //start the row if the item count less than 1
								        if (rowItemscnt == 1)
										{
											curhtml += "<div class=\"row \">";
											
										}
										
										if(prettypermalinkon==1){
											photoslink = document.URL+"?fbasid="+data.data[i].id;
										}else{
											photoslink = document.URL+"&fbasid="+data.data[i].id;
											
										}

										//print output 
										if(rowItemscnt==4){
													curhtml +="<div class=\"threecol "+rowItemscnt +" last\"><a class=\"albumlink\" href=\""+photoslink+ "\" ><div class=\"albumthumb\" style=\"background-image: url("+ imgsrc +") \" /></div></a><br/><a class=\"albumlinktitle\"  href=\""+photoslink+ "\">"+ albumname +"</a></div> <!-- last col -->";
													curhtml += " </div> <!-- End Row Loop-->" ;
													// output row
													addhtml(curhtml);
													 curhtml ="";
													rowItemscnt = 0;
										}else{
													curhtml +="<div class=\"threecol "+rowItemscnt +" \"><a class=\"albumlink\"  href=\""+photoslink+ "\" ><div class=\"albumthumb\" style=\"background-image: url("+ imgsrc +") \" /></div></a><br/><a class=\"albumlinktitle\"  href=\""+photoslink+ "\">"+ albumname +"</a></div>";
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
													if( !jQuery.isEmptyObject(data.paging.next))
													{
														getAlbumPage(data.paging.next);
														
													}else{
														jQuery('#fbloader').remove();
													}
													jQuery('#fbloader').remove();
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
													jQuery('#fbloader').remove();
											  }
	
										}
								    }catch(err){ // End of 25 Albums catch
								    		i++;
								    		//console.log("parent catch: "+err);
											getAlbums(data, i );
								    }								    

							
									},
								error: function( data ) {
									jQuery('#fbloader').remove();
									console.log("Error: Facebook\'s Graph API might be down."+data);
								}
							}); 
						
						//process the data from facebook
							
					}
					
</script>
