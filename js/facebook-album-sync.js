/**
* Facebook Album sync main 
*
*/ 
(function( $, _ , Backbone ){

	// declare the global fbas object
	window.fbas = {}; 

	// setup the root url for all albums
	rootUrl = "https://graph.facebook.com/" + facbookAlbumsSync.facebookPageName + "/albums/";

	fbas.getUrlVars = function ( varKey ){

	    var vars = [], hash;
	    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	    for(var i = 0; i < hashes.length; i++)
	    {
	        hash = hashes[i].split('=');
	        vars.push(hash[0]);
	        vars[hash[0]] = hash[1];
	    }

	    // return the the value of a specic query variable
	    if( ! _.isEmpty( varKey ) && varKey in vars  ){
	    	return vars[ varKey ];
	    }

	    // return all vars
	    return vars;

	} // end get getUrlVars

}( jQuery, _ , Backbone ));