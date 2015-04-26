console.log('inside all albums');
console.log(facbookAlbumsSync);

//initialize variables
var photosCount = 0;
var albumId = facbookAlbumsSync.albumId ;
var rowItemscnt = 1;
var curhtml = "";
var curImage = "";
var shortcode_used = facbookAlbumsSync.singleAlbumShortcode;
var loadingImage = "<div id='fbloader'></div>" ;
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

		//console.log(jsonObject.data[a]);

		try {
			imgsrc_thumb = jsonObject.data[a].images[5].source;
		} catch (err) {
			imgsrc_thumb = jsonObject.data[a].source;
		}
      

		imgsrc = jsonObject.data[a].source;

		curImage = "<div class=\"threecol "+rowItemscnt +" \"><a class=\"photolink\" href=\""+imgsrc+"\" data-lightbox=\"fbgallery\" ><div class=\"photothumblarge\" style=\"background-size: cover; background-image: url("+ imgsrc_thumb +") \" /></div></a></div>";

		if(rowItemscnt==4){
			
					curImage = jQuery(curImage).addClass('last')[0].outerHTML;
					curImage += "<!-- End Row Loop-->" ;
					curhtml += curImage;

					// output row
					addhtml(curhtml);

					curhtml ="";
					rowItemscnt = 1;

		}else{
					console.log(a);
					curhtml += curImage;
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
		console.log(shortcode_used);
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