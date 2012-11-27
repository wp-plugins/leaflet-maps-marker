<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html"; charset="utf-8" />
<title>Changelog for Leaflet Maps Marker</title>
<style type="text/css">
body {
	font-family: sans-serif;
	padding:0 0 0 5px;
	margin:0px;
	font-size: 12px;
	line-height: 1.4em;
}
table {
	line-height:0.7em;
	font-size:12px;
	font-family:sans-serif;
}
.updated {
	padding:10px;
	background-color: #FFFFE0;
}
a {
	color: #21759B;
	text-decoration: none;
}
a:hover, a:active, a:focus {
	color: #D54E21;
}
hr {
	color: #E6DB55;
}
</style>
</head>
<body>
<?php 
while(!is_file('wp-load.php')){
  if(is_dir('../')) chdir('../');
  else die('Error: Could not construct path to wp-load.php - please check <a href="http://mapsmarker.com/path-error">http://mapsmarker.com/path-error</a> for more details');
}
include( 'wp-load.php' );
if (get_option('leafletmapsmarker_update_info') == 'show') {
	$lmm_version_old = get_option( 'leafletmapsmarker_version_before_update' );
	$lmm_version_new = get_option( 'leafletmapsmarker_version' );
/*2do: change verion numbers and date in first line on each update and add if ( ($lmm_version_old < 'x.x' ) ){ to old changelog
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), 'x.x') . '</strong> - ' . __('released on','lmm') . ' xx.xx.2012 (<a href="http://www.mapsmarker.com/vx.x" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		
		</td></tr>
		</table>'.PHP_EOL;
*/
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '3.0') . '</strong> - ' . __('released on','lmm') . ' 28.11.2012 (<a href="http://www.mapsmarker.com/v3.0" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to switch between simplified and advanced editor
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		address now also gets saved to database and displayed on maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Hungarian translation thanks to István Pintér, <a href="http://www.logicit.hu" target="_blank">http://www.logicit.hu</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		show info on top of Maps Marker pages if plugin update is available
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		layer control box is not opened by default on mobile devices anymore
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized TinyMCE popup (new with links to add new marker and layer maps)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		changed position of delete marker and layer buttons
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Japanese translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higash</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized use of WordPress Transients API (saving less rows to wp_options-table)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized plugin active check for higher performance (use of isset() instead of in_array())
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		set jQuery cache for layers to true again for higher performance	
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		shrinked plugin´s total size by 700kb by moving screenshots to assets-directory on wordpress.org
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		top menu now displays correctly if you are on add new or edit-marker or layer page
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		use of checkboxes instead of radio boxes if only one option is available (yes/no)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated screenshots for settings panel
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized backend pages for iOS devices
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized marker and layer list tables on backend
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		marker count on layers lists was wrong for multi-layer-maps (thanks photocoen!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		warning messages for WordPress 3.5beta3 when debug was enabled
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		layout of the preview of list markers on layer maps in backend was broken
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		some links to the new settings panel from backend were broken	
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		layout of map panel was broken on preview if empty marker/layer name was entered	
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		shortcode form field could not be focused on iOS
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		list of assigned markers to multi-layer-maps was broken when more than 1 layer was checked
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		zooming on layer maps on backend was broken on WordPress < v3.3
		</td></tr>
		</table>'.PHP_EOL;

	if ( ($lmm_version_old < '2.9.2' ) ){
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.9.2') . '</strong> - ' . __('released on','lmm') . ' 11.11.2012 (<a href="http://www.mapsmarker.com/v2.9.2" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		compatibility with 1st WordPress NFC plugin from pingeb.org - <a href="http://www.mapsmarker.com/pingeb" target="_blank">read more</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Dutch translation thanks to Patrick Ruers, <a href="http://www.stationskwartiersittard.nl" target="_blank">http://www.stationskwartiersittard.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org/" target="_blank">http://rodolphe.quiedeville.org/</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Ukrainian translation thanks to Andrexj, <a href="http://all3d.com.ua" target="_blank">http://all3d.com.ua</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		new settings panel was broken when certain translations were loaded
		</td></tr>
		</table>'.PHP_EOL;
	}

	if ( ($lmm_version_old < '2.9.1' ) ){
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.9.1') . '</strong> - ' . __('released on','lmm') . ' 05.11.2012 (<a href="http://www.mapsmarker.com/v2.9.1" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		improved backend usability
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		refreshed backend design
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Catalan translation thanks to Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Dutch translation thanks to Patrick Ruers, <a href="http://www.stationskwartiersittard.nl" target="_blank">http://www.stationskwartiersittard.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated German translation
		</td></tr>
		</table>'.PHP_EOL;
	}
	if ( ($lmm_version_old < '2.9' ) ){
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.9') . '</strong> - ' . __('released on','lmm') . ' 02.11.2012 (<a href="http://www.mapsmarker.com/v2.9" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		new logo and updated <a href="http://www.mapsmarker.com" target="_blank">mapsmarker.com</a> website
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		update to <a href="http://www.leafletjs.com" target="_blank">leaflet.js</a> v0.45 (fixing issues with Internet Explorer 10 and Chrome 23)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		revamped <a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings" target="_top">settings panel</a> for better usability
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		add support for bing map localization (cultures)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		compatibilty check notices are now shown globally on each admin page
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added compatibility check for incompatible plugin <a href="http://wordpress.org/extend/plugins/lazy-load/" target="_blank">Lazy Load</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added fallback for installation on hosts where unzip of default marker icons did not work with default method
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		show link "add new map" in TinyMCE popup if no maps have been created yet
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Slovak translation thanks to Zdenko Podobny
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org/" target="_blank">http://rodolphe.quiedeville.org/</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Japanese translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higash</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Turkish translation thanks to Emre Erkan, <a href="http://www.karalamalar.net" target="_blank">http://www.karalamalar.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Catalan translation thanks to Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized internal code structure (moved some functions to /inc/-directory)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized database install- and update routine (use of dbdelta()-function)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		table for list of markers below layer maps was not as wide as map if map with was set in %
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		Bing tiles failed to load when p.x or p.y was -ve (<a href="https://github.com/shramov/leaflet-plugins/issues/31" target="_blank">bug #31</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		Revert function wrapper for Google Maps (broke deferred loading and compiled version of plugins)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		Compatibility with WordPress 3.5beta2
		</td></tr>
		</table>'.PHP_EOL;
	}
	if ( ($lmm_version_old < '2.8.2' ) ){
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.8.2') . '</strong> - ' . __('released on','lmm') . ' 26.09.2012 (<a href="http://www.mapsmarker.com/v2.8.2" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added media button to TinyMCE editor and support for HTML editing mode
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Catalan translation thanks to Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		database tables &amp; marker icon directory did not get removed on multisite blogs when blog was deleted through network admin
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		KML output was broken if marker or layer name contained &amp;-characters
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		plugin incompatibility with "<a href="http://wordpress.org/extend/plugins/seo-image/" target="_blank">SEO Friendly Images</a>" plugin
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		padding was added to map tiles on some templates
		</td></tr>
		</table>'.PHP_EOL;
	}
	if ( ($lmm_version_old < '2.8.1' ) ){
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.8.1') . '</strong> - ' . __('released on','lmm') . ' 09.09.2012 (<a href="http://www.mapsmarker.com/v2.8.1" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		images and links in layer maps were broken
		</td></tr>
		</table>'.PHP_EOL;
	}
	if ( ($lmm_version_old < '2.8' ) ){
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.8') . '</strong> - ' . __('released on','lmm') . ' 08.09.2012 (<a href="http://www.mapsmarker.com/v2.8" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added dynamic changelog to show all changes since your last plugin update
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added WordPress pointers which show after plugin updates (can be disabled)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added subnavigations in settings for higher usability
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized OGD Vienna selector (basemaps now hidden if location outside Vienna)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		revamped admin dashboard widget (cache RSS feeds, show post text)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized install & update routine (now executed only once a day)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated jQuery-Timepicker-Addon by Trent Richardson to v1.0.1
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		started code refactoring for better readability and extensability
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Slovak translation thanks to Zdenko Podobny
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Catalan translation thanks to Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed global stats to comply with WordPress plugin repository policies
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		AJAX GeoJSON-calls from other (sub)domains were not allowed (same origin policy)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		maximum popup width and popup image width were ignored on TinyMCE editor 
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		invalid geojson output when \ in marker name or popup text (now replaced with /)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		markers and layers with lat = 0 could not be created
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		fixed broken zoom for Google Maps with tilt (<a href="https://github.com/robertharm/Leaflet-Maps-Marker/issues/31" target="_blank">github issue #31</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		autoPanPadding for popups was broken
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		widget width was not 100% of sidebar on some templates
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		Google language localization broke GeoJSON output when debug was enabled
		</td></tr>
		</table>'.PHP_EOL;
	}
	if ( ($lmm_version_old < '2.7.1' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.7.1') . '</strong> - ' . __('released on','lmm') . ' 24.08.2012 (<a href="http://www.mapsmarker.com/v2.7.1" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		upgrade to leaflet.js v0.4.4 (<a href="http://www.leafletjs.com/2012/07/30/leaflet-0-4-released.html" target=_blank">changelog</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to add an unobtrusive scale control to maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		support for Retina displays to display maps in a higher resolution
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		boxzoom option (whether the map can be zoomed to a rectangular area specified by dragging the mouse while pressing shift)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		worldCopyJump option (the map tracks when you pan to another "copy" of the world and moves all overlays like markers and vector layers there)
		</td></tr>
    		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		keyboard navigation support for maps
		</td></tr>
    		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		options to customize marker popups (min/max width, scrollbar...)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		add support for maps that do not reflect the real world (e.g. game, indoor or photo maps)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		zoom level can now also be edited directly on marker/layer maps on backend
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added bing/google/mapbox/cloudmad basemaps to mass actions on tools page
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Ukrainian translation thanks to Andrexj, <a href="http://all3d.com.ua" target="_blank">http://all3d.com.ua</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Slovak translation thanks to Zdenko Podobny
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added config options for marker icons and shadow image in settings (size, offset...)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		show marker icons directory (especially needed for blogs on WordPress Multisite installations)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to show marker name as icon tooltip (enabled by default)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		add css-classes to each marker icon automatically
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added routing provider OSRM (<a href="http://map.project-osrm.org" target="_blank">http://map.project-osrm.org</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to customize Google Maps base domain
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		marker/layer name gets added as &lt;title&gt; on fullscreen maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		list of markers can now also be displayed below multi-layer-maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added option to set opacity for overlays
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		support for TMS services for custom basemaps (inversed Y axis numbering for tiles)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		secure loading of Google API via https instead of http
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		enhanced Google Maps language localization options (for maps, directions and autocomplete)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized usability for forms and marker icon selection on backend
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed translation .po files from plugin to reduce file size
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		merged &amp; compressed google-maps.js, bing.js &amp;  into leaflet.js to save http requests
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		changed default color for panel text to #373737 for new installations
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		moved "General Map settings" from tab "Misc" to "Basemaps"
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		GeoJSON AJAX calls for layer maps are not cached anymore to deliver more current results
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized OGD Vienna selector (considers switch to other default basemaps)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org/" target="_blank">http://rodolphe.quiedeville.org/</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>
		</td></tr>		
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Catalan translation thanks to Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		the selection of shortcodes via tinymce popup on posts/pages editor was broken on iOS devices
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		fixed broken links in multi-layer-maps-list and default state controlbox on layer maps on backend 
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		manual language selection for Chinese and Yiddish was broken
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		overwrite box-shadow attribute from style.css to remove border on some themes
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		linebreak was added to mapquest logo in attribution box on some templates
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		Google API key was not loaded on backend
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		attribution text for Google Maps provider was hidden
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		Marker/layer repositioning via Google address search did not changed basemap to Bing/Google
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		switching basemaps caused attribution text not to clear first
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		<html>-tags in geotags are now stripped as they caused 404 messages
		</td></tr>
			</table>'.PHP_EOL;
	}
	if ( ( $lmm_version_old < '2.7' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.7') . '</strong> - ' . __('released on','lmm') . ' 21.07.2012:</p>
		<table>
		<tr><td>
		 "Special Collectors Edition" :-)
		</td></tr>
		</table>'.PHP_EOL;
	}
	if ( ( $lmm_version_old < '2.6.1' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.6.1') . '</strong> - ' . __('released on','lmm') . ' 20.07.2012 (<a href="http://www.mapsmarker.com/v2.6.1" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		bing maps should now work as designed - thank to Pavel Shramov, <a href="https://github.com/shramov/" target="_blank">https://github.com/shramov/</a>!
		</td></tr>
		</table>'.PHP_EOL;
	}
	if ( ( $lmm_version_old < '2.6' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.6') . '</strong> - ' . __('released on','lmm') . ' 19.07.2012 (<a href="http://www.mapsmarker.com/v2.6" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		support for bing maps as basemaps (<a href="http://www.mapsmarker.com/bing-maps" target="_blank">API key required</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		configure marker attributes to show in marker list below layer maps (icon, marker name, popuptext)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to use Google Maps (Terrain) as basemap
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to add Google Maps API key (required for commercial usage) - see <a href="http://www.mapsmarker.com/google-maps-api-key" target="_blank">http://www.mapsmarker.com/google-maps-api-key</a> for more details
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Hindi translation thanks to Outshine Solutions, <a href="http://outshinesolutions.com" target="_blank">http://outshinesolutions.com</a> and Guntupalli Karunakar, <a href="http://indlinux.org" target="_blank">http://indlinux.org</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Yiddish translation thanks to Raphael Finkel, <a href="http://www.cs.uky.edu/~raphael/yiddish.html" target="_blank">http://www.cs.uky.edu/~raphael/yiddish.html</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Catalan translation thanks to Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Added compatibility check for plugin <a href="http://wordpress.org/extend/plugins/bwp-minify/" target="_blank">WordPress Better Minify</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		increased Google Maps maximal zoom level from 18 to 22
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		changed the way Google Maps API is called in order to prevent errors with unset sensor parameter when using certain proxy servers (thanks <a href="http://EdWeWo.com" target="_blank">Dragan</a>!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Italian translation thanks to <a href="http://twitter.com/okibone" target="_blank">Luca Barbetti</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated French translation thanks to Vincen Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		maps using Google Maps Satellite as basemaps were broken
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		text for popups was not as wide in TinyMCE editor as wide in popups
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		fixed vertical alignment of basemaps in layer control box in backend
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '2.5' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.5') . '</strong> - ' . __('released on','lmm') . ' 06.07.2012 (<a href="http://www.mapsmarker.com/v2.5" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		support for Google Maps as basemaps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		admin dashboard widget showing latest markers and blog posts from mapsmarker.com
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Russian translation thanks to Ekaterina Golubina, supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Bulgarian translation thanks to Andon Ivanov, <a href="http://coffebreak.info" target="_blank">http://coffebreak.info</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Turkish translation thanks to Emre Erkan, <a href="http://www.karalamalar.net" target="_blank">http://www.karalamalar.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Polish translation thanks to Pawel Wyszy&#324;ski, <a href="http://injit.pl" target="_blank">http://injit.pl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		new collaborative translation site <a href="http://translate.mapsmarker.com/projects/lmm" target="_blank">http://translate.mapsmarker.com</a> - contributing new translations is now more easier than ever :-)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Japanese translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higash</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Italian translation thanks to <a href="http://twitter.com/okibone" target="_blank">Luca Barbetti</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated French translation thanks to Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org/" target="_blank">http://rodolphe.quiedeville.org/</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Dutch translation thanks to Marijke <a href="http://www.mergenmetz.nl" target="_blank">http://www.mergenmetz.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		show "no markers created yet" on sidebar widget, if no markers are available
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		added translations strings for plugin update notice
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		v2.4 was broken on Wordpress 3.0-3.1.3
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		WMS layer legend links were broken on marker/layer maps in admin area
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		\" in popup text caused layer maps to break (now " get replaced with &#39;)
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '2.4' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.4') . '</strong> - ' . __('released on','lmm') . ' 07.06.2012 (<a href="http://www.mapsmarker.com/v2.4" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to add widgets showing recent marker entries
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to select plugin default language in settings for backend and frontend
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		fixed several SQL injections and cross site scripting issues based on an external audit of the plugin
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		CSS bugfix for wrong sized leaflet attribution links on several templates
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		direction link on popuptext was not shown if popuptext was empty
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed geo tags from Google (geo) sitemap as they are not supported anymore
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '2.3' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.3') . '</strong> - ' . __('released on','lmm') . ' 26.04.2012 (<a href="http://www.mapsmarker.com/v2.3" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added sort options for marker and layer listing pages in backend
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		localized paypal check out pages for donations :-)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated French translation thanks to Vincen Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Italian translation thanks to <a href="http://twitter.com/okibone" target="_blank">Luca Barbetti</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		TinyMCE button error on certain installations (function redeclaration; different wp-admin-directory)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		list of markers below layer maps was not as wide as the map on some templates
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		changed constant WP_ADMIN_URL to LEAFLET_WP_ADMIN_URL due to problems on some blogs
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '2.2' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.2') . '</strong> - ' . __('released on','lmm') . ' 24.03.2012 (<a href="http://www.mapsmarker.com/v2.2" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		support for new map options (dragging, touchzoom, scrollWheelZoom...)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Italian translation thanks to <a href="http://twitter.com/okibone" target="_blank">Luca Barbetti</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		TinyMCE button did not work when WordPress was installed in custom directory
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '2.1' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.1') . '</strong> - ' . __('released on','lmm') . ' 18.03.2012 (<a href="http://www.mapsmarker.com/v2.1" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added changelog info box after each plugin update
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added support for MapBox basemaps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added option to hide API links on markers list below layer maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added check for incompatible plugins
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Italian translation thanks to <a href="mailto:lucabarbetti@gmail.com">Luca Barbetti</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized search results table for maps (started with TinyMCE button on post/page edit screen)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated French translation thanks to Vincen Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Dutch translation thanks to Marijke, <a href="http://www.mergenmetz.nl" target="_blank">http://www.mergenmetz.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated Japanese translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higashi</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		attribution text is not cleared on backend maps if basemap is changed
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		removed double slashes from image urls in settings
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '2.0' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '2.0') . '</strong> - ' . __('released on','lmm') . ' 13.03.2012 (<a href="http://www.mapsmarker.com/v2.0" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added support for geo sitemaps for all marker and layer maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added mass actions (delete+assign to layer) for selected markers only
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		French translation thanks to Vincen Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		maps didnt show up on French installations on backend
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.9' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.9') . '</strong> - ' . __('released on','lmm') . ' 05.03.2012 (<a href="http://www.mapsmarker.com/v1.9" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added TinyMCE-button for easily searching and inserting maps on post/pages-edit screen
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added French translation thanks to Vincen Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		Dutch translation thanks to <a href="http://www.mergenmetz.nl" target="_blank">Marijke</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		Japanes translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higashi</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed support for OSM Osmarender basemaps (service has been discontinued)
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.8' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.8') . '</strong> - ' . __('released on','lmm') . ' 29.02.2012 (<a href="http://www.mapsmarker.com/v1.8" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added option to add a timestamp for each marker for more precise KML animations
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added option to change the default marker icon for new marker maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to configure output of names for KML (show, hide, put in front of popup-text)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added Dutch translation thanks to <a href="http://www.mergenmetz.nl target="_blank">Marijke</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		reduced load for GeoJSON feeds up to 75% (full list of attributes can be shown by adding &full=yes to URL)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated columns for CSV export file (custom overlay & WMS status, kml timestamp)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		KML links are now opened in the same window (removed target="_blank")
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		UTC offset calculations for KML timestamp was wrong if UTC was < 0
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		markers are not clickable anymore if there is no popup text 
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		styles for each marker icon in KML output are now unique (SELECT DISTINCT...)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		output of multiple markers as KML did not work (leaflet-kml.php?marker/layer=1,2,3)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		output of multiple markers as GeoRSS did not work (leaflet-georss.php?marker/layer=1,2,3)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		output of multiple markers as ARML did not work (leaflet-wikitude.php?marker/layer=1,2,3)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		if single layer was changed into multi layer map, list of markers was still displayed below map
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		button "add to layer" did not work on new layers
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.7' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.7') . '</strong> - ' . __('released on','lmm') . ' 22.02.2012 (<a href="http://www.mapsmarker.com/v1.7" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added multi-layer support allowing you to combine markers from different layer maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Wikitude World Browser now displays custom marker icons instead of standard icon from settings
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to set the maximum number of markers you want to display in the list below layer maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Spanish translation thanks to David Ramirez, <a href="http://www.hiperterminal.com" target="_blank">http://www.hiperterminal.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		added with & height attributes to custom marker-image-tags on marker edit page to speed up page load time
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		default font color in popups to black due to incompabilities with several themes
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.6' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.6') . '</strong> - ' . __('released on','lmm') . ' 14.02.2012 (<a href="http://www.mapsmarker.com/v1.6" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added support for Cloudmade maps with styles as basemaps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		update from leaflet 0.3 beta to 0.3.1 stable - <a href="https://github.com/CloudMade/Leaflet/blob/master/CHANGELOG.md" target="_blank">changelog</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		added updated Japanese translation (thanks to Shu Higashi, @higa4)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		added updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		markers did not show up in Wikitude World Browser due to a bug with different provider name
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		lat/lon values for layer and marker maps were rounded on some installations
		</td></tr>		
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.5.1' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.5.1') . '</strong> - ' . __('released on','lmm') . ' 12.02.2012 (<a href="http://www.mapsmarker.com/v1.5.1" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized javascript variable definitions for wms layers and custom overlays get added to sourcecode only when they are active on the current map
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		layer maps and API links did not work on multisite installations
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		legend link for WMS layer did not work
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		links in panel had a border with some templates
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		removed double slashes from LEAFLET_PLUGIN_URL-links
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		uninstall didnt remove marker-icon-directory on some installations
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		admin pages for map/layer edit screens broken on WordPress 3.0 installations
		</td></tr>		
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.5' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.5') . '</strong> - ' . __('released on','lmm') . ' 09.02.2012 (<a href="http://www.mapsmarker.com/v1.5" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added option to display a list of markers below layer maps (enabled for new layer maps, disabled for existing layer maps)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		included option to add GeoRSS feed for all markers to &lt;head&gt; to allow users subscribing to your markers easily
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		add mass actions for layer maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		database structure for boolean values from tinyint(4) to tinyint(1)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		overlay status for layer maps wasnt displayed in backend preview
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.4.3' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.4.3') . '</strong> - ' . __('released on','lmm') . ' 29.01.2012 (<a href="http://www.mapsmarker.com/v1.4.3" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added WMS support for KML-files via networklink
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		routing link attached to popup text did not work
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		missing KML schema declaration causing KML file not to work with scribblemaps.com
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.4.2' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.4.2') . '</strong> - ' . __('released on','lmm') . ' 25.01.2012 (<a href="http://www.mapsmarker.com/v1.4.2" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		custom marker icons not showing up on maps on certain hosts (using directory separators different to / ) 
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		css styling for <label>-tag in controlbox got overriden by some templates
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.4.1' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.4.1') . '</strong> - ' . __('released on','lmm') . ' 24.01.2012 (<a href="http://www.mapsmarker.com/v1.4.1" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		added updated Japanese translation (thanks to Shu Higashi, @higa4)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		markers & layers could not be added on some hosting providers (changed updatedby & updatedon column on both tables to NULL instead of NOT NULL)
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.4' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.4') . '</strong> - ' . __('released on','lmm') . ' 23.01.2012 (<a href="http://www.mapsmarker.com/v1.4" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added support for routing service from Google Maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added support for routing service from yournavigation.org
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added support for routing service from openrouteservice.org
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		mass-actions for changing default values for existing markers (map size, icon, panel status, zoom, basemap...)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		panel status can now also be selected as column for marker/layer listing page
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		controlbox status column for markers/layers list view now displays text instead of 0/1/2
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		method for adding markers/layers as some users reported that new markers/layers were not saved to database
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		method for plugin active-check as some users reported that API links did not work
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		marker/layer name in fullscreen panel did not support UTF8-characters
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		text width in tinymce editor was not the same as in popup text
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		several German translation text strings
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		markers added directly with shortcode caused error on frontend
		</td></tr>		
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.3' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.3') . '</strong> - ' . __('released on','lmm') . ' 17.01.2012 (<a href="http://www.mapsmarker.com/v1.3" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added mass actions for makers (assign markers to layer, delete markers)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		flattr now embedded as static image as long loadtimes decrease usability because Google Places scripts starts only afterwards
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		marker-/layername for panel in backend now gets refreshed dynamically after entering in form field
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		geo microformat tags are now also added to maps added directly via shortcode
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized div structure and order for maps on frontend
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed global stats for plugin installs, marker/layer edits and deletions
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed featured sponsor in admin header
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed developers comments from css- and js-files
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		map/panel width were not the same due to css inheritance
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		map css partially broken in IE < 9 when viewing backend maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		links in maps were underlined on some templates
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		panel API link images had borders on some templates
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		text in layer controlbox was centered on some templates
		</td></tr>		
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.2.2' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.2.2') . '</strong> - ' . __('released on','lmm') . ' 14.01.2012 (<a href="http://www.mapsmarker.com/v1.2.2" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		custom marker icons were not shown on certain hosts due to different wp-upload-directories
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.2.1' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.2.1') . '</strong> - ' . __('released on','lmm') . ' 13.01.2012 (<a href="http://www.mapsmarker.com/v1.2.1" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		plugin installation failed on certain hosting providers due to path/directory issues
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		(interactive) maps do not get display in RSS feeds (which is not possible), so now a static image with a link to the fullscreen standalone map is displayed
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		removed redundant slashes from paths
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		fullscreen maps did not get loaded if WordPress is installed in subdirectory
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		API images in panel did show a border on some templates
		</td></tr>
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.2' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.2') . '</strong> - ' . __('released on','lmm') . ' 11.01.2012 (<a href="http://www.mapsmarker.com/v1.2" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added <a href="http://www.mapsmarker.com/georss" target="_blank">GeoRSS-feeds for marker- and layer maps</a> (RSS 2.0 & ATOM 1.0)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added microformat geo-markup to maps, to make your maps machine-readable
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		Default custom overlay (OGD Vienna Addresses) is not active anymore by default on new markers/layers (but still gets active when an address through search by Google Places is selected)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		added attribution text for default custom overlay (OGD Vienna Addresses) to see if overlay has accidently been activated
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		added sanitization for wikitude provider name 
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		plugin conflict with Google Analytics for WordPress resulting in maps not showing up
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		plugin did not work on several hosts as path to wp-load.php for API links could not be constructed
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		reset settings to default values did only reset values from v1.0
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		when default custom overlay for new markers/layers got unchecked, the map in backend did not show up anymore
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		fullscreen standalone maps didnt work in Internet Explorer
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		maps did not show up in Internet Explorer 7 at all
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		attribution box on standalone maps did not show up if windows size is too small
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		slashes were not stripped from marker/layer name on frontend maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		quotes were not shown on marker/layer names (note: double quotes are replaced with single quotes automatically due to compatibility reasons)
		</td></tr>		
		</table>'.PHP_EOL;
	}		
	if ( ( $lmm_version_old < '1.1' ) && ( $lmm_version_old > '0' ) ){
		echo '<hr noshade size="1"><p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.1') . '</strong> - ' . __('released on','lmm') . ' 08.01.2012 (<a href="http://www.mapsmarker.com/v1.1" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		<a href="http://www.mapsmarker.com/wp-content/plugins/leaflet-maps-marker/leaflet-fullscreen.php?marker=1" target="_blank">show standalone maps in fullscreen mode</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		<a href="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=http://www.mapsmarker.com/wp-content/plugins/leaflet-maps-marker/leaflet-fullscreen.php?marker=1" target="_blank">create QR code images for standalone maps in fullscreen mode</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		API links (KML, GeoJSON, Fullscreen, QR Code, Wikitude) now only work if plugin is active
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Japanese translation thanks to Shu Higashi (<a href="http://twitter.com/higa4" target="_blank">@higa4</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to show/hide WMS layer legend link
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to disable global statistics
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		added more default marker icons, based on the top 100 icons from the Map Icons Collection
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		added attribution text field in settings for custom overlays
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed settings for Wikitude debug lon/lat -> now marker lat/lon respectively layer center lat/lon are used when Wikitude API links are called without explicit parameters &latitude= and &longitude=
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		default setting fields can now be changed by focusing with mouse click
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		added icons to API links on backend for better usability
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		dynamic preview of marker/layer panel in backend not working as designed
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		language pot-file did not include all text strings for translations
		</td></tr>		
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		active translations made setting tabs unaccessible
		</td></tr>
		</table>'.PHP_EOL;
	}		
	echo '</div>';
}
?>
</body>
</html>