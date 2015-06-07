This Plugin is Retired
====
Facebook Album Sync -  WordPress Plugin

 
Load your Facebook galleries on any page by using the plugin shortcode. With this plugin you can:

* List all your Facebook albums with.
* Lists a specific album by specifying the id.

Usage
----
 
###Adding albums to your page
 
 To add the plugin to your page add the short code below to the page where you want your albums to be displayed: 
 
 [fbalbumsync]
 
###Showing a specific album
 
 To add a specific album to a page use the album attribute and include the album id as you see below:
 
 [fbalbumsync album="album id here"]
 
 To get the correct ID go to the specific album page on Facebook . Then look up at the URL of the page:
 
 https://www.facebook.com/media/set/?set=a.10151623813761670.1073741963.6604386669&type=3
 
 The album gallery ID is the first number after the "a." : 10151623813761670
 
 So, in this case, the correct shortcode is:
 
 [fbalbumsync album="10151623813761670"]
 
Code Contributions
----
If you have any code that fixes or enhances this plugin, let me know opening an issue / pull request on this repo.