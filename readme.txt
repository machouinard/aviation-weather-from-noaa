=== Aviation Weather from NOAA ===

Contributors:		machouinard
Tags: 				weather, noaa, aviation, metar, pireps
Requires at least: 	3.8
Tested up to:      	4.4
Stable tag:        	trunk
License:           	GPLv2 or later
License URI:       	http://www.gnu.org/licenses/gpl-2.0.html

Aviation weather data from NOAA's Aviation Digital Data Service (ADDS)

== Description ==
* Display METAR & TAF info from NOAA's Aviation Digital Data Service
* Display up to 6 hours before now
* PIREPs up to 200sm
* Create multiple instances using either widget or shortcode
* WP-CLI Integration

= Shortcode Usage: ( shown with defaults ) =
    [adds_weather apts='KSMF' hours=2 show_metar=1 show_taf=1 show_pireps=1 show_station_info=1 radial_dist=100 title='']

Data is cached for 30 minutes using the WordPress Transients API.

= Included Filter Hooks: =
* adds_shortcode_title_wrap: Tag used for shortcode title.  Defaults to "h2".
* adds_shortcode_wrapper: Class of the div enclosing shortcode output.
* adds_kses: Array of permitted HTML tags.
* adds_custom_css: URL of a user-supplied stylesheet.  Supplying a stylesheet in theme's directory ( 'css/aviation_weather_from_noaa.css' ) will also override stylesheet.
* adds_widget_wrapper: Class of the div enclosing the widget

Code and support available at [GitHub](https://github.com/machouinard/aviation-weather-from-noaa "GitHub Repo")

== Installation ==

= Manual Installation =

1. Upload the entire `/aviation-weather-from-noaa` directory to the `/wp-content/plugins/` directory.
2. Activate Aviation Weather from NOAA through the 'Plugins' menu in WordPress.

= WP_CLI Installation =
    wp plugin install aviation-weather-from-noaa --activate


== Changelog ==
= 0.4.0 =
* Separate functionality into individual classes
* Improve use of transients and options including deletion on uninstall
* Only display most recent METAR & TAF
* Add decoded METAR
* Show/hide decoded METAR and PIREPS
* WP-CLI Integration

= 0.3.8 =
* Add ability to not display Metar
* Add check for PHP version 3.3

= 0.3.7 =
* Add styling
* Add hooks to filter styling
* Add option for theme supplied stylesheet

= 0.3.6 =
* Add unit tests

= 0.3.5 =
* Cast values as bool instead of using boolval(), which requires PHP >= 5.5

= 0.3.4a =
* Remove local development files from SVN ( basically v0.3.3 without the extra files )

= 0.3.3 =
* Fix code that was preventing TAF from displaying
* Fix transients not being deleted on update
* Add check for USA, Canada and Australia if 3 characters entered for ICAO

= 0.3.1 =
* Refactor per WordPress coding standards
* Replace cURL and allow_url_fopen() with wp_remote_get()
* Add check to limit hours before now to 6 in shortcode
* Add checks for empty/missing values
* Better sanitizing

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
