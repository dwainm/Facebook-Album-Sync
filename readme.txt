=== Plugin Name ===
Contributors: dwainm
Donate link: http://dwainm.wordpress.com/donate/
Tags: Facebook albums,Facebook gallery
Requires at least: 3.X
Tested up to: 3.5.1
Stable tag: 0.3
License: GPLv2 or later


Load your Facebook albums on any page by using short codes.

== Description ==

Load your Facebook galleries on any page by using the plugin shortcode.

Here are the key features:

* List all your Facebook albums with.
* Lists a specific album by specifying the id.

= Installation Instructions =
Go to the [Instalation Instructions](http://wordpress.org/extend/plugins/facebook-album-sync/installation/ "Installation Instructions") 

= Please Contribute =
If you have any code that fixes or enaches this plugin, let me know opening an issue / pull request on Github: [Facebook Albums Sync]("https://github.com/dwainm/Facebook-Album-Sync")



== Installation ==

=Plugin installation=
1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. See the Plugin at the bottom Menu, edit it with no restrictions

=Setup=

1. Go to "Settings > Facebook Albums" in your WordPress admin screen.
2. Enter your Facebook page name as instructed on that page and click on save.

=Adding albums to your page=

To add the plugin to your page add the short code below to the page where you want your albums to be displayed: 

[fbalbumsync]

=Showing a specific album=

To add a specific album to a page use the album attribute and include the album id as you see below:

[fbalbumsync album="album id here"]

To get the correct ID go to the specific album page on Facebook . Then look up at the URL of the page:

https://www.facebook.com/media/set/?set=a.10151623813761670.1073741963.6604386669&type=3

The album gallery ID is the first number after the "a." : 10151623813761670

So, in this case, the correct shortcode is:

[fbalbumsync album="10151623813761670"]


== Frequently Asked Questions ==

= How Does the plugin work =
Go to [Instalation](http://wordpress.org/extend/plugins/facebook-album-sync/installation/ "Installation Instructions")  for instructions.


= Do you offer support =

We do offer support via the plugin forum [click here for support](http://wordpress.org/support/plugin/facebook-album-sync "Support Forum") .

You can also submit issues on github [https://github.com/dwainm/Facebook-Albums-Sync/issues](https://github.com/dwainm/Facebook-Albums-Sync/issues "Gitub Isues") .


== Screenshots ==

1. Will be uploaded.

== Changelog ==
= 0.1 =
* Initial release plugin
* Get the albums from Facebook
* Display all albums with a link to its photos via short code

= 0.2 =
* Updated plugin readme file.
* Added ability to load specific album only
* Bug Fixes:
* * fixed loading images and lightbox issues as mentioned here: http://wordpress.org/support/topic/lightbox-13?replies=21

= 0.3 =
* Fixed lightbox photo not working.
* Fixed photo count to ensure that all album photo's are displayed.
* Change the lightbox script and loading to version 2 and update the links to reflect the new plugin
* Removed styling from the back to album link
* Removed profile, wall and cover albums

== Upgrade Notice ==

= 0.3 =
* Fixed lightbox photo not working.
* Fixed photo count to ensure that all album photo's are displayed.
* Change the lightbox script and loading to version 2 and update the links to reflect the new plugin
* Removed styling from the back to album link
* Removed profile, wall and cover albums

== How to ==

1. To use go to the Settings page then enter your Facebook page there.
2. Copy the short code to your page: [fbalbumsync]
