=== Citizen Space ===
Contributors: delib
Tags: citizen space, consultation, online democracy, delib
Requires at least: 3.3.1
Tested up to: 3.3.1
Stable tag: 1.0

A plugin to talk to Citizen Space via its API.


== Description ==

This plugin is designed to be used with any Citizen Space site running Citizen Space version 1.6.5 or
greater.  This may be:

* A Citizen Space site that you or your organisation administers.
* A Citizen Space site that you don't administer, but which contains interesting content you'd like to
syndicate on your own site.

With this plugin, you can use shortcodes to embed parts of the Citizen Space frontend into
your own pages and posts.  The following shortcodes are supported:

* __[citizenspace_basic_search]__ - The short 'Find consultations' keyword search form.
* __[citizenspace_advanced_search]__ - The Advanced consultation search form.
* __[citizenspace_search_results]__ - A list of consultations matching a predefined search, or based
on the on the form fields above.
* __[citizenspace_consultation url="http://full_consultation_url"]__ - An individual consultation record.

To use a shortcode, simply copy and paste the code (including its square brackets) into any page or post.
When the page or post is displayed, Wordpress will contact Citizen Space, request the appropriate
information and display it in place of the shortcode.

= Example =

To turn a page in your Wordpress site into a fully-functional consultation search page, just copy and
paste the following three lines:

[citizenspace_advanced_search]
Search results:
[citizenspace_search_results]


= Shortcode builder =

If you want to include a list of consultations based on a predefined search (rather than allowing
the visitor to choose their search parameters using the form) you can pass an additional query
parameter to the [citizenspace_search_results] shortcode, so it looks something like:

[citizenspace_search_results query="hide_batch_nav=1&st=open&au=Community+groups&ar=.site.Horfield&b_size=0"]

Obviously a query like this is fiddly to write by hand, so we've included a form under
_Tools->Citizen Space_ to make this simpler.  Just set up your search like you would do in the Citizen Space
frontend, and it will generate a shortcode for you.  You can then embed this code in your page or post
and you'll get an up-to-the-minute listing of all consultations matching your query.

= Link options =

By default, the consultations in the search results link directly to the consultation overview on
the Citizen Space site.  If you would prefer to embed the overview pages in your own site, so that visitors
don't have to leave your site to read them, you can change this in _Settings->Citizen Space_.

== Installation ==

1. Upload the citizen-space directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. In _Settings->Citizen Space_ enter the full URL of the Citizen Space instance you want to talk to.

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
Initial release

