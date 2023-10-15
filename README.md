# Download Monitor Advanced Settings

## What does it do?
This plugin is used to enhance the control of the Download Monitor plugin by tapping into its hooks. 

## What can be achieved?

- hide the meta that consists of Download Monitor plugin version
- disable the Reports
- disable XHR downloading
- add custom 404 redirect
- add custom Download placeholder image URL
- modify the Reports server limits ( if you have any problems with the Reports not being displayed a possible problem might be your server's lack of resources. This way you can control how much data is retrieved in one request )
- upon deleting a Download get possibility to also delete its files
- Allow Proxy IP Override ( original a setting from Download Monitor, this has been removed starting with version 4.9.1 )
- Enable X-Accel-Redirect / X-Sendfile ( original a setting from Download Monitor, this has been removed starting with version 4.9.1 )
- Enable Hotlink protection ( original a setting from Download Monitor, this has been removed starting with version 4.9.1 )
- get possibility to remove the timestamp from the download link ( the tmstv parameter ). Disabling this might have some unwanted effect on sites that use cached links.
- add or remove the Download's meta value ( manual download count ) in the download count number.
- supply extra restricted file types. Defaults are: php, html, htm & tmp.
- set if the XHR should do the progress loading icon and define a custom icon URL.