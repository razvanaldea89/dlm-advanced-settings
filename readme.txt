=== DLM - Advanced Settings ===
Contributors: raldea89
Tags: download monitor, filters, hooks, advanced settings
Requires at least: 5.4
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv3
Text Domain: dlm-advanced-settings
Requires PHP: 7.0

Download Monitor is a plugin for uploading and managing downloads, tracking downloads and displaying links.

== Description ==

This plugin is used to enhance the control of the Download Monitor plugin by tapping into its hooks and offering a way to manipulate them via the admin panel.

= Features =

* hide the meta that consists of Download Monitor plugin version
* disable the Reports
* disable XHR downloading
* add custom 404 redirect
* add custom Download placeholder image URL
* modify the Reports server limits ( if you have any problems with the Reports not being displayed a possible problem might be your server's lack of resources. This way you can control how much data is retrieved in one request )
* upon deleting a Download get possibility to also delete its files
* Allow Proxy IP Override ( original a setting from Download Monitor, this has been removed starting with version 4.9.1 )
* Enable X-Accel-Redirect / X-Sendfile ( original a setting from Download Monitor, this has been removed starting with version 4.9.1 )
* Enable Hotlink protection ( original a setting from Download Monitor, this has been removed starting with version 4.9.1 )
* get possibility to remove the timestamp from the download link ( the tmstv parameter ). Disabling this might have some unwanted effect on sites that use cached links.
* add or remove the Download's meta value ( manual download count ) in the download count number.
* supply extra restricted file types. Defaults are: php, html, htm & tmp.
* set if the XHR should do the progress loading icon and define a custom icon URL.

= Support =

Use the WordPress.org forums for community support. If you spot a bug, you can of course log it on [Github](https://github.com/raldea89/dlm-advanced-settings/issues/new) instead where we can act upon it more efficiently.

== Installation ==

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't even need to leave your web browser. To do an automatic install, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.

In the search field type "Download Monitor Advanced Settings" and click Search Plugins. Once you've found the plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by clicking _Install Now_.

= Manual installation =

The manual installation method involves downloading the plugin and uploading it to your webserver via your favourite FTP application.

* Download the plugin file to your computer and unzip it
* Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation's `wp-content/plugins/` directory.
* Activate the plugin from the Plugins menu within the WordPress admin.

== Changelog ==

= 1.0.0 =
* Plugin release.

== Upgrade Notice ==
