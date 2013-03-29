<?php
global $post;
$previouspageURL  = $_SERVER['HTTP_REFERER'];

?>
<p><a href="<?PHP echo $previouspageURL?>"> Back to Albums</a></p>
<div id="fbalbumsync">

</div>
<script type="text/javascript">

					//initialize variables
					var photosCount = 0;
					var albumId = "<?php echo get_query_var('fbasid') ?>";	
					var rowItemscnt = 1;
					var curhtml = "";				
					function addhtml(html){
						/* Write the text */
						document.getElementById('fbalbumsync').innerHTML += html;
					}
					getAlbumPhotos("https://graph.facebook.com/"+ albumId +"/photos");
					function getAlbumPhotos(fbGraphUrl)
					{
						jQuery.ajax({
							dataType: 'jsonp',
							url: fbGraphUrl,
							type: 'GET',
							success: function getpages(data){
								//var albumname = data.data[i].name;
								
								getPhotos(data,0);
							},
							error: function( data ) {
								alert('Facebook\'s Graph API might be down.');
							}
						});
					}
					
					function getPhotos(jsonObject, i)
					{

						for(a=0; a < jsonObject.data.length; a++ ){
							//start the row if the item count less than 1
							if (rowItemscnt == 1)
							{
								curhtml += "<div class=\"row \">";
								
							}
							photosCount ++;
							imgsrc_thumb = jsonObject.data[a].images[5].source;
							imgsrc = jsonObject.data[a].source;
							//print output 
							if(rowItemscnt==4){
										curhtml +="<div class=\"threecol "+rowItemscnt +" last\"><a class=\"photolink\" href=\""+imgsrc+"\" rel=\"lightbox[galleryname]\" ><div class=\"photothumblarge\" style=\"background-image: url("+ imgsrc_thumb +") \" /></div> </a></div> <!-- last col -->";
										curhtml += " </div> <!-- End Row Loop-->" ;
										// output row
										addhtml(curhtml);
										curhtml ="";
										rowItemscnt = 1;
							}else{
										curhtml += "<div class=\"threecol "+rowItemscnt +" \"><a class=\"photolink\" href=\""+imgsrc+"\" rel=\"lightbox[galleryname]\" ><div class=\"photothumblarge\" style=\"background-image: url("+ imgsrc_thumb +") \" /></div></a></div>";
										rowItemscnt++;
							}	
						}//end for loop

						
						//check if there are more image
						if( !jQuery.isEmptyObject(jsonObject.paging.next)){
									getAlbumPhotos(jsonObject.paging.next);
						}else{
							if(rowItemscnt<4){ // if curhtml still has photos as row has not ended
						 		addhtml(curhtml);
							}
							// setup light box
							jQuery('#fbalbumsync a').lightBox(); // Select all links that contains lightbox in the attribute rel
							//alert("lightbox bound");
							//addhtml("<hr/> FINISH");
							
							addhtml("<a href=\"javascript:javascript:history.go(-1)\"> Back to Albums</a> <br /> "); 
							}
						
					}// End getPhotos
					function getvar( varname )
						{
						  name = varname.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
						  var regexS = "[\\?&]"+name+"=([^&#]*)";
						  var regex = new RegExp( regexS );
						  var results = regex.exec( window.location.href );
						  if( results == null )
							return "";
						  else
							return results[1];
						}

					
	</script>