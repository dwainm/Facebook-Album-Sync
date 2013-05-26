<?php
global $post;
$previouspageURL  = $_SERVER['HTTP_REFERER'];
$shortcode_used = true; // used to test if the album page is called directly via shortcode

 // test if the album page is called via short code  
//  or if the page is called when the user clicks on album link

if ($album_id==""){
	$album_id = get_query_var('fbasid');
	$shortcode_used = false;
?>

	<p><a href="<?PHP echo $previouspageURL?>"> Back to Albums</a></p>

<?php 	

}

?>

<div id="fbalbumsync">

</div>
<script type="text/javascript">

					//initialize variables
					var photosCount = 0;
					var albumId = "<?php echo $album_id ?>";
					var rowItemscnt = 1;
					var curhtml = "";
					var shortcode_used = "<?php echo $shortcode_used ?>";
					var loadingImage = "<br /><img id=\"fbloader\"src=\"<?php echo plugins_url();?>/facebook-album-sync/images/fbloader.gif\" /><br />" ;
					addhtml(loadingImage);					
					function addhtml(html){
						/* Write the text */
						document.getElementById('fbalbumsync').innerHTML += html;
					}
					getAlbumPhotos("https://graph.facebook.com/"+ albumId +"/photos" );
					
					// functions start

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
							
							if(!shortcode_used){
								addhtml("<a href=\"javascript:javascript:history.go(-1)\"> Back to Facebook Albums</a> <br /> ");
							}
							
						}
					jQuery('#fbloader').remove();
						
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