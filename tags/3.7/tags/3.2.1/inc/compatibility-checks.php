<?php
/*
    Checks for incompatible plugins and settings - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'compatibility-checks.php') { die ("Please do not access this file directly. Thanks!<br/><a href='http://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . "pluggable.php");
$lmm_options = get_option( 'leafletmapsmarker_options' ); //info: required for bing maps api key check

//info: check if bing maps api key is defined
if (( (($lmm_options['standard_basemap'] == 'bingaerial') || ($lmm_options['standard_basemap'] == 'bingaerialwithlabels') || ($lmm_options['standard_basemap'] == 'bingroad')) 
|| ((isset($lmm_options[ 'controlbox_bingaerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerial' ] == 1 )) 
|| ((isset($lmm_options[ 'controlbox_bingaerialwithlabels' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerialwithlabels' ] == 1 )) 
|| ((isset($lmm_options[ 'controlbox_bingroad' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingroad' ] == 1 )) 
) && ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] == NULL ) 
)) {
	echo '<p><div class="error" style="padding:10px;"><strong>' . __('Warning: you enabled support for bing maps but did not provide an API key. Please visit <a href="http://www.mapsmarker.com/bing-maps" target="_blank">http://www.mapsmarker.com/bing-maps</a> for info on how to get a free bing maps API key!','lmm') . '</strong></div></p>';
}

//info: plugin JavaScript to Footer
if (is_plugin_active('footer-javascript/footer-javascript.php') ) {
	echo '<p><div class="error" style="padding:10px;"><strong>' . __('Warning: you are using the plugin Javascript to Footer which is incompatible with Leaflet Maps Marker and causing maps to break. Please deactivate this plugin in order to be able to use Leaflet Maps Marker.','lmm') . '</strong></div></p>';
}
//info: plugin Lazy Load
if (is_plugin_active('lazy-load/lazy-load.php') ) {
	echo '<p><div class="error" style="padding:10px;"><strong>' . __('Warning: you are using the plugin Lazy Load which is incompatible with Leaflet Maps Marker and causing maps to break. Please deactivate this plugin in order to be able to use Leaflet Maps Marker.','lmm') . '</strong></div></p>';
}
//info: plugin jQuery Colorbox
if (is_plugin_active('jquery-colorbox/jquery-colorbox.php') ) {
	$lmm_jquery_colorbox_options = get_option( 'jquery-colorbox_settings' );
	if ($lmm_jquery_colorbox_options['autoColorbox'] == TRUE) { 
		echo '<p><div class="error" style="padding:10px;">' . __('<strong>Warning: you are using the plugin jQuery Colorbox which is causing maps to break!</strong><br/><br/>Here is how to fix this:<br/>1. click on to "Settings" / "jQuery Colorbox" in your WordPress admin menu<br/>2. Uncheck the setting "Automate jQuery Colorbox for all images in pages, posts and galleries:"<br/>3. check the setting "Automate jQuery Colorbox for images in WordPress galleries only:" instead<br/>4. save changes<br/><br/>This message will disappear automatically when the jQuery Colorbox option was updated.','lmm') . '</div></p>';
	} 
}
//info: plugin cformsII
if (is_plugin_active('cforms/cforms.php') ) {
	$lmm_cforms_options = get_option( 'cforms_settings' );
	if ($lmm_cforms_options['global'][ 'cforms_show_quicktag_js' ] == FALSE) { 
		echo '<p><div class="error" style="padding:10px;">' . __('<strong>Warning: you are using the plugin cformsII which is causing the TinyMCE editor to break when creating new maps!</strong><br/><br/>Here is how to fix this:<br/>1. click on to "cformsII" / "Global Settings" in your WordPress admin menu<br/>2. open the tab "WP Editor Button support"<br/>3. check the option "Fix TinyMCE error"<br/>4. save changes<br/><br/>If you do not see this option in your settings, please upgrade to the latest version first (this has to be done manually - see plugin website http://www.deliciousdays.com/cforms-plugin/ for details)<br/><br/>This message will disappear automatically when the cformsII option "Fix TinyMCE error" is checked.','lmm') . '</div></p>';
	} 
}
//info: plugin WP Google Analytics
if (is_plugin_active('wp-google-analytics/wp-google-analytics.php') ) {
	echo '<p><div class="error" style="padding:10px;"><strong>' . __('Warning: you are using the outdated plugin WP Google Analytics which is incompatible with Leaflet Maps Marker. Please update to a more current Google analytics plugin like http://wordpress.org/extend/plugins/google-analytics-for-wordpress/','lmm') . '</strong></div></p>';
}
//info: plugin Better WordPress Minify
if (is_plugin_active('bwp-minify/bwp-minify.php') ) {
	$lmm_bwpminify_options = get_option( 'bwp_minify_general' );
	if ($lmm_bwpminify_options['enable_min_js'] == 'yes') { 
		if (strpos($lmm_bwpminify_options['input_ignore'], 'leafletmapsmarker') === false)  { 
			echo '<p><div class="error" style="padding:10px;"><strong>' . __('Warning: you are using the plugin "Better WordPress Minify" which can cause Leaflet Maps Marker to break if the option "Minify JS files automatically?" is active. Please disable this option (Settings / BWP Minify) or add <strong>leafletmapsmarker</strong> to the form field "Scripts to be ignored (not minified)"','lmm') . '</strong></div></p>';
		}
	}
}
?>