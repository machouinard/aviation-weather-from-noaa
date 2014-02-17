=== Aviation Weather from NOAA ===
Contributors:		machouinard
Tags: 				weather, noaa, aviation, metar
Requires at least: 	3.8.1
Tested up to:      	3.8.1
Stable tag:        	0.3.0
License:           	GPLv2 or later
License URI:       	http://www.gnu.org/licenses/gpl-2.0.html

Aviation weather data from NOAA's Aviation Digital Data Service (ADDS)

== Description ==
* Display METAR & TAF info from NOAA's Aviation Digital Data Service.
* Display up to 6 hours before now.
* PIREPs up to 200sm

= Shortcode Usage: ( shown with defaults ) =
    [adds_weather apts='KSMF' hours=2 show_taf=1 show_pireps=1 radial_dist=30 title='']

Data is cached for 15 minutes using the WordPress Transients API.

Code available at [GitHub](https://github.com/machouinard/aviation-weather-from-noaa "GitHub Repo")
== Installation ==

= Manual Installation =

1. Upload the entire `/aviation-weather-from-noaa` directory to the `/wp-content/plugins/` directory.
2. Activate Aviation Weather from NOAA through the 'Plugins' menu in WordPress.


== Changelog ==

= 0.3.0 =
* Added ability to use cURL if allow_url_fopen() is disabled

= 0.2.7 =
* Fixed readme errors
* Added GitHub link

= 0.2.6 =
* Added transients API so we don't hit NOAA with every page load

= 0.2.4 =
* Added custom title to widget + shortcode

= 0.2.0 =
* Added ability to hide or display TAF
* Added shortcode

= 0.1.0 =
* First release

== Upgrade Notice ==

= 0.3.0 =
Added ability to use cURL if allow_url_fopen() is disabled