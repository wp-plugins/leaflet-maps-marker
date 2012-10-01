<?php
/*
Plugin Name: Leaflet Maps Marker &reg;
Plugin URI: http://www.mapsmarker.com
Description: Pin, organize & show your favorite places through OpenStreetMap, Google Maps, Google Earth (KML), Bing Maps, GeoRSS or Augmented-Reality browsers
Tags: map, maps, Leaflet, OpenStreetMap, geoJSON, json, jsonp, OSM, travelblog, opendata, open data, opengov, open government, ogdwien, WMTS, geoRSS, location, geo, geo-mashup, geocoding, geolocation, travel, mapnick, osmarender, cloudmade, mapquest, geotag, geocaching, gpx, OpenLayers, mapping, bikemap, coordinates, geocode, geocoding, geotagging, latitude, longitude, position, route, tracks, google maps, googlemaps, gmaps, google map, google map short code, google map widget, google maps v3, google earth, gmaps, ar, augmented-reality, wikitude, wms, web map service, geocache, geocaching, qr, qr code, fullscreen, marker, marker icons, layer, multiple markers, karte, blogmap, geocms, geographic, routes, tracks, directions, navigation, routing, location plan, YOURS, yournavigation, ORS, openrouteservice, widget, bing, bing maps, microsoft, map short code, map widget, kml, cross-browser, fully documented, traffic, bike lanes, map short code, custom marker text, custom marker icons and text
Version: 2.8.2
Author: Robert Harm
Author URI: http://www.harm.co.at
Donate link: http://www.mapsmarker.com/donations
Requires at least: 3.0
Tested up to: 3.5beta1
Requires at least PHP 5.2
Copyright 2011-2012 - @RobertHarm - All rights reserved
MapsMarker &reg; - registration pending
Parts of this plugin were originally based on the Leaflet Plugin by Hind (Copyright 2011)
	
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License v2 as published by
the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You have received a copy of the full GNU General Public License
along with this program (see file licence-gpl20.txt)
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-maps-marker.php') { die ("Please do not access this file directly. Thanks!<br/><a href='http://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
//info: Compatibility checks
global $wp_version;
if (version_compare($wp_version,"3.0","<")){
  exit('[Leaflet Maps Marker Plugin - installation failed!]: WordPress Version 3.0 or higher is needed for this plugin (you are using version '.$wp_version.') - please upgrade your WordPress installation!');
}
if (version_compare(phpversion(),"5.2","<")){
  exit('[Leaflet Maps Marker Plugin - installation failed]: PHP 5.2 is needed for this plugin (you are using PHP '.phpversion().'; note: support for PHP 4 has been officially discontinued since 2007-12-31!) - please upgrade your PHP installation!');
}
//info: define necessary paths and urls
if ( ! defined( 'LEAFLET_WP_ADMIN_URL' ) )
	define( 'LEAFLET_WP_ADMIN_URL', get_admin_url() );
if ( ! defined( 'LEAFLET_PLUGIN_URL' ) )
define ("LEAFLET_PLUGIN_URL", plugin_dir_url(__FILE__));
if ( ! defined( 'LEAFLET_PLUGIN_DIR' ) )
define ("LEAFLET_PLUGIN_DIR", plugin_dir_path(__FILE__));
$lmm_upload_dir = wp_upload_dir();
if ( ! defined( 'LEAFLET_PLUGIN_ICONS_URL' ) )
	define ("LEAFLET_PLUGIN_ICONS_URL", $lmm_upload_dir['baseurl'] . "/leaflet-maps-marker-icons");
if ( ! defined( 'LEAFLET_PLUGIN_ICONS_DIR' ) )
	define ("LEAFLET_PLUGIN_ICONS_DIR", $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons");
//info: not in class Leafletmapsmarker as otherwise warnings on resetting defaults options
require_once( plugin_dir_path( __FILE__ ) . 'inc' . DIRECTORY_SEPARATOR . 'class-leaflet-options.php' );
class Leafletmapsmarker
{
function __construct() {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	add_action('init', array(&$this, 'lmm_load_translation_files'),1);
	add_action('admin_init', array(&$this, 'lmm_install_and_updates'),2); //info: register_action_hook not used as otherwise Wordpress Network installs break
	add_action('wp_enqueue_scripts', array(&$this, 'lmm_frontend_enqueue_scripts'),3);
	add_action('wp_print_styles', array(&$this, 'lmm_frontend_enqueue_stylesheets'),4);
	add_action('admin_menu', array(&$this, 'lmm_admin_menu'),5);
	add_action('admin_init', array(&$this, 'lmm_plugin_meta_links'),6);
	add_action('wp_head', array(&$this, 'lmm_image_css_override'),1000);
	add_action('admin_bar_menu', array(&$this, 'lmm_add_admin_bar_menu'),149);
	add_shortcode($lmm_options['shortcode'], array(&$this, 'lmm_showmap'));
	if ($lmm_options['misc_add_georss_to_head'] == 'enabled') {
		add_action( 'wp_head', array( &$this, 'lmm_add_georss_to_head' ) );
	}
	if ( isset($lmm_options['misc_tinymce_button']) && ($lmm_options['misc_tinymce_button'] == 'enabled') ) {
		require_once( plugin_dir_path( __FILE__ ) . 'inc' . DIRECTORY_SEPARATOR . 'tinymce-plugin.php' );
	}
	if ( isset($lmm_options['misc_plugin_language']) && ($lmm_options['misc_plugin_language'] != 'automatic') ){
		add_filter('plugin_locale', array(&$this,'lmm_set_plugin_locale'), 'lmm');
	}
	add_action('widgets_init', create_function('', 'return register_widget("Class_leaflet_recent_marker_widget");'));
	if ( isset($lmm_options['misc_admin_dashboard_widget']) && ($lmm_options['misc_admin_dashboard_widget'] == 'enabled') ){
		if ( !is_multisite() ) {
			add_action('wp_dashboard_setup', array( &$this,'lmm_register_widgets' ));
		} else {
			add_action('wp_network_dashboard_setup', array( &$this,'lmm_register_widgets' ));
			add_action('wp_dashboard_setup', array( &$this,'lmm_register_widgets' ));
		}
	}
	if ( isset($lmm_options['misc_pointers'] ) && ($lmm_options['misc_pointers'] == 'enabled') ) {
		add_action( 'admin_enqueue_scripts', array( $this, 'lmm_pointer_admin_scripts' ),1001);
	}
	if ( is_multisite() ) {
		add_action('delete_blog', array( &$this,'lmm_delete_multisite_blog' ));
	} 
  }
  function lmm_delete_multisite_blog($blog_id) {
	switch_to_blog($blog_id);
	/* Remove tables */
	$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_layers`");
	$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_markers`");
	/*remove map icons directory for subsite*/
	$lmm_upload_dir = wp_upload_dir();
	$icons_directory = $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons" . DIRECTORY_SEPARATOR;
	if (is_dir($icons_directory)) {
		foreach(glob($icons_directory.'*.*') as $v) {
			unlink($v);
		}
		rmdir($icons_directory);
	}
  }
  function lmm_pointer_admin_scripts() {
	$lmm_version_new = get_option( 'leafletmapsmarker_version' );
	$seen_it = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
	$do_add_script = false;
	$version_without_dots = "lmmv" . str_replace('.', '', $lmm_version_new);
	if ( ! in_array( $version_without_dots, $seen_it ) ) {
		$do_add_script = true;
		add_action( 'admin_print_footer_scripts', array( $this, 'lmm_pointer_footer_script' ) );
	}
	if ( $do_add_script ) {
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_style( 'wp-pointer' );
	}
  }
  function lmm_pointer_footer_script() {
	$lmm_version_new = get_option( 'leafletmapsmarker_version' );
	$version_without_dots = "lmmv" . str_replace('.', '', $lmm_version_new);
	$install_note = (isset($_GET['display']) ? $_GET['display'] : '');
	if ( $install_note == NULL) {
		$pointer_content = '<h3>' . sprintf(__('Leaflet Maps Marker plugin update to v%1s was successful','lmm'), $lmm_version_new) . '</h3>'; 
		$changelog_url = '<a href="' . admin_url('/admin.php?page=leafletmapsmarker_markers') .'" style="text-decoration:none;">' . __('changelog','lmm') . '</a>';
	} else {
		$pointer_content = '<h3>' . __('Leaflet Maps Marker plugin was successfully installed','lmm') . '</h3>'; 
		$changelog_url = '<a href="http://www.mapsmarker.com/changelog" target="_blank" style="text-decoration:none;">' . __('changelog','lmm') . '</a>';
	}
	$blogpost_url = '<a href="http://www.mapsmarker.com/v' . $lmm_version_new . '" target="_blank" style="text-decoration:none;">mapsmarker.com</a>';
	$pointer_content .= '<p>' . sprintf(__('Please see the %1s for new features or the blog post on %2s for more details','lmm'), $changelog_url, $blogpost_url) . '</p>';
  ?>
	<script type="text/javascript">// <![CDATA[
	jQuery(document).ready(function($) {
	    if(typeof(jQuery().pointer) != 'undefined') {
	        $('#toplevel_page_leafletmapsmarker_markers').pointer({
        	    content: '<?php echo $pointer_content; ?>',
	            position: {
        	        edge: 'left',
	                align: 'center'
        	    },
	            close: function() {
        	        $.post( ajaxurl, {
                	    pointer: '<?php echo $version_without_dots; ?>',
	                    action: 'dismiss-wp-pointer'
        	        });
	            }
	        }).pointer('open');
	    }
	});
	// ]]></script>
  <?php
  }
  function lmm_register_widgets(){
	wp_add_dashboard_widget( 'lmm-admin-dashboard-widget', __('Leaflet Maps Marker - recent markers','lmm'), array( &$this,'lmm_dashboard_widget'), array( &$this,'lmm_dashboard_widget_control'));
  }
  function lmm_dashboard_widget(){
	global $wpdb;
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$widgets = get_option( 'dashboard_widget_options' );
	$widget_id = 'lmm-admin-dashboard-widget'; 
	$number_of_markers =  isset( $widgets[$widget_id] ) && isset( $widgets[$widget_id]['items'] ) ? absint( $widgets[$widget_id]['items'] ) : 4;
	$result = $wpdb->get_results($wpdb->prepare("SELECT ID,markername,icon,createdon,createdby FROM $table_name_markers ORDER BY createdon desc LIMIT $number_of_markers"), ARRAY_A);
	if ($result != NULL) {
		echo '<table style="margin-bottom:5px;"><tr>';
		foreach ($result as $row ) {
			$icon = ($row['icon'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' : LEAFLET_PLUGIN_ICONS_URL.'/' . $row['icon'];
			echo '<td><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['ID'] . '" title="' . esc_attr__('edit marker','lmm') . '"><img src="' . $icon . '" style="width:80%;"></a>';
			echo '<td style="vertical-align:top;line-height:1.2em;">';
			echo '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['ID'] . '" title="' . esc_attr__('edit marker','lmm') . '">'.htmlspecialchars(stripslashes($row['markername'])).'</a><br/>' . __('created on','lmm') . ' ' . date("Y-m-d - h:m", strtotime($row['createdon'])) . ', ' . __('created by','lmm') . ' ' . $row['createdby'];
			echo '</td></tr>';
		}
		echo '</table>';	
	} else {
		echo '<p style="margin-bottom:5px;">' . __('No marker created yet','lmm') . '</p>';
	}  
	if  ( !isset($widgets[$widget_id]['blogposts']) ) {
		$show_rss = 1;
	} else if ( isset($widgets[$widget_id]['blogposts']) && ($widgets[$widget_id]['blogposts'] == 1) ) {
		$show_rss = 0;
	} else {
		$show_rss = 1;
	}
	if ($show_rss == 1)
	{
			require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'class-simplepie.php');  
			$feed = new SimplePie();
			if ( file_exists(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . 'information.png') ) {
				$feed->enable_cache(true);
				$feed->set_cache_location($location = LEAFLET_PLUGIN_ICONS_DIR); 
				$feed->set_cache_duration(86400);
			} else {
				$feed->enable_cache(false);
			}
			$feed->set_feed_url('http://feeds.feedburner.com/MapsMarker');
			$feed->set_stupidly_fast(true);
			$feed->enable_order_by_date(true);
			$feed->init();
			$feed->handle_content_type();
			echo '<hr style="border:0;height:1px;background-color:#d8d8d8;"><strong><p>' . __('Latest blog posts from www.mapsmarker.com','lmm') . '</p></strong>';
			if ($feed->get_items() == NULL) { 
				$blogpost_url = '<a href="http://www.mapsmarker.com/news" target="_blank">http://www.mapsmarker.com/news</a>';
				echo sprintf(__('Feed could not be retrieved, please try again later or read the latest blog posts at %s','lmm'),$blogpost_url); 
			}
			foreach ($feed->get_items(0,3) as $item) {
				echo '<p>' . $item->get_date('j F Y') . ': <strong><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></strong><br/>' . $item->get_description() . '</p>'.PHP_EOL;
			}
			echo '<p><a href="http://feeds.feedburner.com/MapsMarker" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-rss.png" width="16" height="16" alt="rss"> RSS</a>&nbsp;&nbsp;&nbsp;<a href="http://feedburner.google.com/fb/a/mailverify?uri=MapsMarker" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-rss-email.png" width="16" height="16" alt="rss-email"> ' . __('E-Mail','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a href="http://twitter.com/mapsmarker" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-twitter.png" width="16" height="16" alt="twitter"> Twitter</a>&nbsp;&nbsp;&nbsp;<a href="http://facebook.com/mapsmarker" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-facebook.png" width="16" height="16" alt="facebook"> Facebook</a>&nbsp;&nbsp;&nbsp;<a href="https://github.com/robertharm/Leaflet-Maps-Marker" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-github.png" width="16" height="16" alt="github"> github</a>&nbsp;&nbsp;&nbsp;<a href="http://translate.mapsmarker.com/projects/lmm" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-translations.png" width="16" height="16" alt="translations"> ' . __('translations','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a href="http://www.mapsmarker.com/donations" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-donations.png" width="16" height="16" alt="donations"> ' . __('donations','lmm') . '</a></p>';		
	}
  }
  function lmm_dashboard_widget_control(){
	$widget_id = 'lmm-admin-dashboard-widget';
	$form_id = 'lmm-admin-dashboard-widget-control';
	$update = false;
    if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
      $widget_options = array(); 
    if ( !isset($widget_options[$widget_id]) ) {
	//info: set default value
      $widget_options[$widget_id] = array(
			'blogposts' => 0,
			'items' => 5
	  ); 
	  $update = true;
	}
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST[$form_id]) ) {
	  $number = ($_POST[$form_id]['items'] == NULL) ? '3' : absint( $_POST[$form_id]['items'] );
      //$number = absint( $_POST[$form_id]['items'] );
	  $blogposts = isset($_POST[$form_id]['blogposts']) ? '1' : '0';
      $widget_options[$widget_id]['items'] = $number; 
      $widget_options[$widget_id]['blogposts'] = $blogposts; 
      $update = true;
    }
	if($update) update_option( 'dashboard_widget_options', $widget_options ); 
    $number = isset( $widget_options[$widget_id]['items'] ) ? (int) $widget_options[$widget_id]['items'] : '';
    echo '<p><label for="lmm-admin-dashboard-widget-number">' . __('Number of markers to show:') . ' </label>';
    echo '<input id="lmm-admin-dashboard-widget-number" name="'.$form_id.'[items]" type="text" value="' . $number . '" size="2" /></p>';
    echo '<p><label for="lmm-admin-dashboard-widget-blogposts">' . __('Hide blog posts and link section:') . ' </label>';
    echo '<input id="lmm-admin-dashboard-widget-blogposts" name="'.$form_id.'[blogposts]" type="checkbox" ' . checked($widget_options[$widget_id]['blogposts'],1,false) . '/></p>';
  }
  function lmm_load_translation_files() {
	load_plugin_textdomain('lmm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
  }
  function lmm_set_plugin_locale( $lang ) {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	if ($lmm_options['misc_plugin_language_area'] == 'backend') {
		return is_admin() ? $lmm_options['misc_plugin_language'] : $locale;
	} else if ($lmm_options['misc_plugin_language_area'] == 'frontend') {
		return is_admin() ? $locale : $lmm_options['misc_plugin_language'];
	} else if ($lmm_options['misc_plugin_language_area'] == 'both') {
		return $lmm_options['misc_plugin_language'];
	} else {
		return $locale;
	}
  }
  function lmm_help()
  {
    include('leaflet-help-credits.php');
  }
  function lmm_settings() {
    $lmm_options = new Class_leaflet_options();
    $lmm_options->display_page();
  }
  function lmm_list_layers()
  {
    include('leaflet-list-layers.php');
  }
  function lmm_list_markers()
  {
     include('leaflet-list-markers.php');
  }
  function lmm_layer()
  {
    include('leaflet-layer.php');
  }
  function lmm_marker()
  {
    include('leaflet-marker.php');
  }
  function lmm_tools()
  {
    include('leaflet-tools.php');
  }
  function lmm_add_georss_to_head() {
	$georss_to_head = '<link rel="alternate" type="application/rss+xml" title="' . get_bloginfo('name') . ' GeoRSS-Feed" href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=all" />'.PHP_EOL;
	echo $georss_to_head;
  }
  function lmm_showmap($atts) {
	//info: remove filter from SEO Friendly Images for compatibility reasons
	include_once( ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php' );
	if (is_plugin_active('seo-image/seo-friendly-images.php') ) {
		if ( function_exists( 'seo_friendly_images_install' ) ) {
			remove_filter('the_content', 'seo_friendly_images', 100);
		}
	}
    global $wpdb;
    $lmm_options = get_option( 'leafletmapsmarker_options' );
    $uid = substr(md5(''.rand()), 0, 8);
    extract(shortcode_atts(array(
		'lat' => '', 'lon' => '',
		'mlat' => '', 'mlon' => '',
		'basemap' => $lmm_options[ 'defaults_marker_shortcode_basemap'],
		'mpopuptext' => '',
		'micon' => '',
		'zoom' => intval($lmm_options[ 'defaults_marker_shortcode_zoom' ]),
		'openpopup' => '',
		'geojson' => '',
		'geojsonurl' => '',
		'layer' => '',
		'marker' => '',
		'markername' => '',
		'panel' => '0',
		'mapwidth' => intval($lmm_options[ 'defaults_marker_shortcode_mapwidth' ]),
		'mapwidthunit' => $lmm_options[ 'defaults_marker_shortcode_mapwidthunit' ],
		'mapheight' => intval($lmm_options[ 'defaults_marker_shortcode_mapheight' ]),
		'mapname' => 'lmm_map_'.$uid
    ), $atts));
    $pname = 'pa'.$uid;
	//info: prepare layers
	if (!empty($layer)) {
		$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
		$row = $wpdb->get_row('SELECT id,name,basemap,mapwidth,mapheight,mapwidthunit,panel,layerzoom,layerviewlat,layerviewlon,controlbox,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10,listmarkers,multi_layer_map,multi_layer_map_list FROM '.$table_name_layers.' WHERE id='.$layer, ARRAY_A);
		$id = $row['id'];
		$basemap = $row['basemap'];
		$lat = $row['layerviewlat'];
		$lon = $row['layerviewlon'];
		$zoom = $row['layerzoom'];
		$mapwidth = $row['mapwidth'];
		$mapheight = $row['mapheight'];
		$mapwidthunit = $row['mapwidthunit'];
		$panel = $row['panel'];
		$paneltext = ($row['name'] == NULL) ? '&nbsp;' : htmlspecialchars($row['name']);
		$controlbox = $row['controlbox'];
		$overlays_custom = $row['overlays_custom'];
		$overlays_custom2 = $row['overlays_custom2'];
		$overlays_custom3 = $row['overlays_custom3'];
		$overlays_custom4 = $row['overlays_custom4'];
		$wms = $row['wms'];
		$wms2 = $row['wms2'];
		$wms3 = $row['wms3'];
		$wms4 = $row['wms4'];
		$wms5 = $row['wms5'];
		$wms6 = $row['wms6'];
		$wms7 = $row['wms7'];
		$wms8 = $row['wms8'];
		$wms9 = $row['wms9'];
		$wms10 = $row['wms10'];
		$listmarkers = $row['listmarkers'];
    	$multi_layer_map = $row['multi_layer_map'];
		$multi_layer_map_list = $row['multi_layer_map_list'];
    	$multi_layer_map_list_exploded = explode(",", $multi_layer_map_list);
	}
	//info: prepare markers
    if (!empty($marker))  {
		    $table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
				$row = $wpdb->get_row('SELECT id,markername,basemap,layer,lat,lon,icon,popuptext,zoom,openpopup,mapwidth,mapwidthunit,mapheight,panel,controlbox,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10 FROM '.$table_name_markers.' WHERE id='.$marker, ARRAY_A);
				if(!empty($row)) {
					$id = $row['id'];
					$markername = $row['markername'];
					$basemap = $row['basemap'];
					$lon = $row['lon'];
					$lat = $row['lat'];
					$coords = $lat.', '.$lon;
					$icon = $row['icon'];
					$popuptext = $row['popuptext'];
					$zoom = $row['zoom'];
					$openpopup = ($row['openpopup'] == 1) ? '.openPopup()' : '';
					$mopenpopup = $openpopup;
					$layer = $row['layer'];
					$mlat = $lat;
					$mlon = $lon;
					$mpopuptext = $popuptext;
					$micon = $icon;
					$mapwidth = $row['mapwidth'];
					$mapwidthunit = $row['mapwidthunit'];
					$mapheight = $row['mapheight'];
					$panel = $row['panel'];
					$paneltext = ($row['markername'] == NULL) ? '&nbsp;' : htmlspecialchars($row['markername']);
					$controlbox = $row['controlbox'];
					$overlays_custom = $row['overlays_custom'];
					$overlays_custom2 = $row['overlays_custom2'];
					$overlays_custom3 = $row['overlays_custom3'];
					$overlays_custom4 = $row['overlays_custom4'];
					$wms = $row['wms'];
					$wms2 = $row['wms2'];
					$wms3 = $row['wms3'];
					$wms4 = $row['wms4'];
					$wms5 = $row['wms5'];
					$wms6 = $row['wms6'];
					$wms7 = $row['wms7'];
					$wms8 = $row['wms8'];
					$wms9 = $row['wms9'];
					$wms10 = $row['wms10'];
				}
    }
	//info: prepare markers only added by shortcode and not defined in backend
	if (empty($layer) and empty($marker)) {
		$lat = $mlat;
		$lon = $mlon;
		$controlbox = $lmm_options[ 'defaults_marker_shortcode_controlbox' ];
		$overlays_custom = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom_active' ] == 1 ) ? '1' : '0';
		$overlays_custom2 = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom2_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom2_active' ] == 1 ) ? '1' : '0';
		$overlays_custom3 = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom3_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom3_active' ] == 1 ) ? '1' : '0';
		$overlays_custom4 = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom4_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom4_active' ] == 1 ) ? '1' : '0';
		$wms = isset($lmm_options[ 'defaults_marker_shortcode_wms_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms_active' ] == 1 ) ? '1' : '0';
		$wms2 = isset($lmm_options[ 'defaults_marker_shortcode_wms2_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms2_active' ] == 1 ) ? '1' : '0';
		$wms3 = isset($lmm_options[ 'defaults_marker_shortcode_wms3_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms3_active' ] == 1 ) ? '1' : '0';
		$wms4 = isset($lmm_options[ 'defaults_marker_shortcode_wms4_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms4_active' ] == 1 ) ? '1' : '0';
		$wms5 = isset($lmm_options[ 'defaults_marker_shortcode_wms5_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms5_active' ] == 1 ) ? '1' : '0';
		$wms6 = isset($lmm_options[ 'defaults_marker_shortcode_wms6_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms6_active' ] == 1 ) ? '1' : '0';
		$wms7 = isset($lmm_options[ 'defaults_marker_shortcode_wms7_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms7_active' ] == 1 ) ? '1' : '0';
		$wms8 = isset($lmm_options[ 'defaults_marker_shortcode_wms8_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms8_active' ] == 1 ) ? '1' : '0';
		$wms9 = isset($lmm_options[ 'defaults_marker_shortcode_wms9_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms9_active' ] == 1 ) ? '1' : '0';
		$wms10 = isset($lmm_options[ 'defaults_marker_shortcode_wms10_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms10_active' ] == 1 ) ? '1' : '0';
		$mopenpopup = '';
	}
	
	//info: show static image with link in feeds
	if (is_feed()) {
		if ($lat != NULL) { //info: marker exists?
			if (empty($layer)) {
			$lmm_out = '<p>' . $paneltext . '<br/><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/map-rss-feed.png"/><br/>' . __('Show embedded map in full-screen mode','lmm') . '</a></p>';		
			}
			if (empty($marker)) {
			$lmm_out = '<p>' . $paneltext . '<br/><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/map-rss-feed.png"/><br/>' . __('Show embedded map in full-screen mode','lmm') . '</a></p>';		
			}
			return $lmm_out;
		}
	} else {
		
	//info: check if layer/marker ID exists
	if ($lat == NULL) {
	$error_layer_not_exists = sprintf( esc_attr__('Error: a layer with the ID %1$s does not exist!','lmm'), $layer); 
	$error_marker_not_exists = sprintf( esc_attr__('Error: a marker with the ID %1$s does not exist!','lmm'), $marker); 
	$lmm_out = '<div id="lmm_error" style="margin:10px 0;">'.PHP_EOL;
		if (empty($layer)) {
			$lmm_out .= $error_marker_not_exists . '<br/>';
		}
		if (empty($marker)) {
			$lmm_out .= $error_layer_not_exists . '<br/>';
		}
	$lmm_out .= '<a href="http://www.mapsmarker.com" target="_blank" title="' . esc_attr__('Go to plugin website','lmm') . '"><img style="border:1px solid #ccc;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/map-deleted-image.png"></a></div>';
	} else {	
	//info: starting output on frontend
	$lmm_out = ''; 
	$lmm_out .= '<div id="lmm_'.$uid.'" style="width:' . $mapwidth.$mapwidthunit . ';">'.PHP_EOL;
	//info: panel for layer/marker name and API URLs
	if ($panel == 1) {
		$lmm_out .= '<div id="lmm_panel_'.$uid.'" class="lmm-panel" style="background: ' . ((!empty($marker)) ? addslashes($lmm_options[ 'defaults_marker_panel_background_color' ]) : (!empty($layer)) ? addslashes($lmm_options[ 'defaults_layer_panel_background_color' ]) : '') . ';">'.PHP_EOL;
		if (!empty($marker)) 
		{
			$lmm_out .= '<div id="lmm_panel_api_'.$uid.'" class="lmm-panel-api">';
			if ( (isset($lmm_options[ 'defaults_marker_panel_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_directions' ] == 1 ) ) {
					if ($lmm_options['directions_provider'] == 'googlemaps') {
						if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $yours_transport_type_icon = 'icon-walk.png'; } else { $yours_transport_type_icon = 'icon-car.png'; }
						$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
						$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
						$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
						$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
						//info: Google language localization (directions)
						if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
							$google_language = '';
						} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
							if ( defined('WPLANG') ) { $google_language = '&hl=' . substr(WPLANG, 0, 2); } else { $google_language =  '&hl=en'; }
						} else {
							$google_language = '&hl=' . $lmm_options['google_maps_language_localization'];
						}
						$lmm_out .= '<a href="http://maps.google.com/maps?daddr=' . $lat . ',' . $lon . '&t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
					} else if ($lmm_options['directions_provider'] == 'yours') {
						if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $yours_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $yours_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $yours_transport_type_icon = 'icon-walk.png'; }
						$lmm_out .= '<a href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
					} else if ($lmm_options['directions_provider'] == 'osrm') {
						$lmm_out .= '<a href="http://map.project-osrm.org/?hl=' . $lmm_options[ 'directions_osrm_language' ] . '&loc=' . $lat . ',' . $lon . '&df=' . $lmm_options[ 'directions_osrm_units' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" /></a>';
					} else if ($lmm_options['directions_provider'] == 'ors') {
						if ($lmm_options[ 'directions_ors_route_preferences' ] == 'Pedestrian') { $yours_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_route_preferences' ] == 'Bicycle') { $yours_transport_type_icon = 'icon-bicycle.png'; } else { $yours_transport_type_icon = 'icon-car.png'; }
						$lmm_out .= '<a href="http://openrouteservice.org/index.php?end=' . $lon . ',' . $lat . '&pref=' . $lmm_options[ 'directions_ors_route_preferences' ] . '&lang=' . $lmm_options[ 'directions_ors_language' ] . '&noMotorways=' . $lmm_options[ 'directions_ors_no_motorways' ] . '&noTollways=' . $lmm_options[ 'directions_ors_no_tollways' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
					}
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_kml' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $id . '&name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_fullscreen' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_qr_code' ] == 1 ) ) {
				$lmm_out .= '<a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_geojson' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $id . '&callback=jsonp&full=yes" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_georss' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_wikitude' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
			}
		$lmm_out .= '</div><div id="lmm_panel_text_'.$uid.'" class="lmm-panel-text" style="padding-left:5px;' . addslashes($lmm_options[ 'defaults_marker_panel_paneltext_css' ]) . '">' . stripslashes($paneltext) . '</div>';
		}
		
		if (!empty($layer) && empty($marker)) //info: check if problems get reported - fix for marker name shown twice when layer+marker map on 1 page
		{
			$lmm_out .= '<div id="lmm_panel_api_'.$uid.'" class="lmm-panel-api">';
			if ( (isset($lmm_options[ 'defaults_layer_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_kml' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $id . '&name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_fullscreen' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_qr_code' ] == 1 ) ) {
				$lmm_out .= '<a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_geojson' ] == 1 ) && ($multi_layer_map == 0 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $id . '&callback=jsonp&full=yes" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_georss' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_wikitude' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
			}
		$lmm_out .= '</div><div id="lmm_panel_text_'.$uid.'" class="lmm-panel-text" style="padding-left:5px;' . addslashes($lmm_options[ 'defaults_layer_panel_paneltext_css' ]) . '">' . stripslashes($paneltext) . '</div>'.PHP_EOL;			
		}
	$lmm_out .= '</div>'.PHP_EOL; //info: <!--end lmm-panel-->
	}
	$lmm_out .= '<div id="'.$mapname.'"  data-marker="'.$marker.'" style="height:'.$mapheight.'px; overflow:hidden;padding:0;"></div>'. PHP_EOL;	
	//info: add geo microformats for layer maps
	if (!empty($layer) && empty($marker))
	{
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$layer_mark_list_microformats = $wpdb->get_results('SELECT l.id as lid,l.name as lname, m.lon as mlon, m.lat as mlat, m.markername as markername,m.id as markerid FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$layer, ARRAY_A);
		if (count($layer_mark_list_microformats) < 1) {
			$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">' . $paneltext . ': <span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span></div>'.PHP_EOL;
		} else {
			foreach ($layer_mark_list_microformats as $row){
				$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">' . htmlspecialchars($row['markername']) . ': <span class="latitude">' . $row['mlat'] . '</span>, <span class="longitude">' . $row['mlon'] . '</span></div>'.PHP_EOL;
			}
		}
	}
	//info: add geo microformats for marker maps
	if (!empty($marker)) 
	{
	//info: add geo microformats
	$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">'.PHP_EOL;
	$lmm_out .= '<span class="paneltext">' . $paneltext . '</span>'.PHP_EOL;
	$lmm_out .= '<span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span>'.PHP_EOL;
	$lmm_out .= '<span class="popuptext">' . strip_tags($popuptext) .'</span>'.PHP_EOL;
	$lmm_out .= '</div>'.PHP_EOL;
	}
	//info: add geo microformats for marker maps added directly via shortcode
	if (empty($layer) && empty($marker)) 
	{
	//info: add geo microformats
	$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">'.PHP_EOL;
	$lmm_out .= '<span class="latitude">' . $mlat . '</span>, <span class="longitude">' . $mlon . '</span>'.PHP_EOL;
	$lmm_out .= '</div>'.PHP_EOL;
	}
	//info: display a list of markers under the map
	if ( !empty($layer) && empty($marker) && ($listmarkers == 1) )
	{
	//info: sqls for singe and multi-layer-maps
    if ($multi_layer_map == 0) {
		$layer_marker_list = $wpdb->get_results('SELECT l.id as lid, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid, m.createdon as mcreatedon, m.updatedon as mupdatedon FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$id.' ORDER BY ' . $lmm_options[ 'defaults_layer_listmarkers_order_by' ] . ' ' . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . ' LIMIT ' . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]), ARRAY_A);
    } else if ($multi_layer_map == 1) {
	if ( (count($multi_layer_map_list_exploded) == 1) && ($multi_layer_map_list != 'all') ) {
		$mlm_query = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon FROM " . $table_name_layers . " as l INNER JOIN " . $table_name_markers . " AS m ON l.id=m.layer WHERE l.id='" . $multi_layer_map_list . "' ORDER BY " . $lmm_options[ 'defaults_layer_listmarkers_order_by' ] . " " . $lmm_options[ 'defaults_layer_listmarkers_sort_order' ] . " LIMIT " . intval($lmm_options[ 'defaults_layer_listmarkers_limit' ]) . ")";
		$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
	} //info: end (count($multi_layer_map_list_exploded) == 1) && ($multi_layer_map_list != 'all')
	else if ( (count($multi_layer_map_list_exploded) > 1 ) && ($multi_layer_map_list != 'all') ) {
		$first_mlm_id = $multi_layer_map_list_exploded[0];
		$other_mlm_ids = array_slice($multi_layer_map_list_exploded,1); 
		$mlm_query = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon FROM " . $table_name_layers . " as l INNER JOIN " . $table_name_markers . " AS m ON l.id=m.layer WHERE l.id='" . $first_mlm_id . "')";
		foreach ($other_mlm_ids as $row) {		
			$mlm_query .= " UNION (SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon FROM " . $table_name_layers . " as l INNER JOIN " . $table_name_markers . " AS m ON l.id=m.layer WHERE l.id='" . $row . "')";
		}
		$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
	} //info: end else if ( (count($multi_layer_map_list_exploded) > 1 ) && ($multi_layer_map_list != 'all'
	else if ($multi_layer_map_list == 'all') {
		$first_mlm_id = '0'; 
		$mlm_all_layers = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM $table_name_layers" ), ARRAY_A );
		$other_mlm_ids = array_slice($mlm_all_layers,1);  
		$mlm_query = "(SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon FROM " . $table_name_layers . " as l INNER JOIN " . $table_name_markers . " AS m ON l.id=m.layer WHERE l.id='" . $first_mlm_id . "')";
		foreach ($other_mlm_ids as $row) {		
			$mlm_query .= " UNION (SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon FROM " . $table_name_layers . " as l INNER JOIN " . $table_name_markers . " AS m ON l.id=m.layer WHERE l.id='" . $row['id'] . "')";
		}
		$layer_marker_list = $wpdb->get_results($mlm_query, ARRAY_A);
	} //info: end else if ($multi_layer_map_list == 'all')
    } //info: end main - else if ($multi_layer_map == 1)
	
	$lmm_out .= '<div id="lmm-listmarkers-'.$uid.'" class="lmm-listmarkers" style="width:' . $mapwidth.$mapwidthunit . ';">'.PHP_EOL;
	$lmm_out .= '<table width="' . $mapwidth.$mapwidthunit . '">';
	foreach ($layer_marker_list as $row){
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_icon' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_icon' ] == 1 ) ) {
			$lmm_out .= '<tr><td style="width:35px;vertical-align:top;text-align:center;">';
			if ($row['micon'] != null) { 
				$lmm_out .= '<img src="' . LEAFLET_PLUGIN_ICONS_URL . '/'.$row['micon'].'" title="' . stripslashes($row['markername']) . '" />'; 
			} else { 
				$lmm_out .= '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" title="' . stripslashes($row['markername']) . '" />';
			};
		} else {
			$lmm_out .= '<tr><td>';			
		};
		$lmm_out .= '</td><td><div class="lmm-listmarkers-panel-icons">';
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_directions' ] == 1 ) ) {
			if ($lmm_options['directions_provider'] == 'googlemaps') {
				if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $yours_transport_type_icon = 'icon-walk.png'; } else { $yours_transport_type_icon = 'icon-car.png'; }
				$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
				$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
				$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
				$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
				//info: Google language localization (directions)
				if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
					$google_language = '';
				} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
					if ( defined('WPLANG') ) { $google_language = '&hl=' . substr(WPLANG, 0, 2); } else { $google_language =  '&hl=en'; }
				} else {
					$google_language = '&hl=' . $lmm_options['google_maps_language_localization'];
				}
				$lmm_out .= '<a href="http://maps.google.com/maps?daddr=' . $row['mlat'] . ',' . $row['mlon'] . '&t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
			} else if ($lmm_options['directions_provider'] == 'yours') {
				if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $yours_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $yours_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $yours_transport_type_icon = 'icon-walk.png'; }
				$lmm_out .= '<a href="http://www.yournavigation.org/?tlat=' . $row['mlat'] . '&tlon=' . $row['mlon'] . '&v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
			} else if ($lmm_options['directions_provider'] == 'osrm') {
				$lmm_out .= '<a href="http://map.project-osrm.org/?hl=' . $lmm_options[ 'directions_osrm_language' ] . '&loc=' . $row['mlat'] . ',' . $row['mlon'] . '&df=' . $lmm_options[ 'directions_osrm_units' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
			} else if ($lmm_options['directions_provider'] == 'ors') {
				if ($lmm_options[ 'directions_ors_route_preferences' ] == 'Pedestrian') { $yours_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_route_preferences' ] == 'Bicycle') { $yours_transport_type_icon = 'icon-bicycle.png'; } else { $yours_transport_type_icon = 'icon-car.png'; }
				$lmm_out .= '<a href="http://openrouteservice.org/index.php?end=' . $row['mlon'] . ',' . $row['mlat'] . '&pref=' . $lmm_options[ 'directions_ors_route_preferences' ] . '&lang=' . $lmm_options[ 'directions_ors_language' ] . '&noMotorways=' . $lmm_options[ 'directions_ors_no_motorways' ] . '&noTollways=' . $lmm_options[ 'directions_ors_no_tollways' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
			}
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_fullscreen' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $row['markerid'] . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_kml' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $row['markerid'] . '&name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_qr_code' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $row['markerid'] . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_geojson' ] == 1 ) && ($multi_layer_map == 0 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $row['markerid'] . '&callback=jsonp&full=yes" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_georss' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $row['markerid'] . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_api_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_listmarkers_api_wikitude' ] == 1 ) ) {
			$lmm_out .= '&nbsp;<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $row['markerid'] . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
		}
		$lmm_out .= '</div>';
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_markername' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_markername' ] == 1 ) ) {
			$lmm_out .= '<strong>' . stripslashes(htmlspecialchars($row['markername'])) . '</strong>';
		}
		if ( (isset($lmm_options[ 'defaults_layer_listmarkers_show_popuptext' ]) == TRUE ) && ($lmm_options[ 'defaults_layer_listmarkers_show_popuptext' ] == 1 ) ) {
			$lmm_out .= '<br/>' . stripslashes($row['mpopuptext']);
		}
		$lmm_out .= '</td></tr>';
	} //info: end foreach
	$lmm_out .= '</table></div>';
	}
	$plugin_version = get_option('leafletmapsmarker_version');
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= '/* Maps created with MapsMarker.com WordPress plugin - version '.$plugin_version.' */'.PHP_EOL;
	$lmm_out .= 'var layers = {};'.PHP_EOL;
	$lmm_out .= 'var markers = {};'.PHP_EOL;
	$lmm_out .= 'var lmm_map_'.$uid.' = {};'.PHP_EOL;
	//info: define attribution links as variables to allow dynamic change through layer control box
	$attrib_prefix = '<a href=\"http://mapsmarker.com/go\" target=\"_blank\" title=\"powered by \'Leaflet Maps Marker\'-Plugin for WordPress\">MapsMarker.com</a> (<a href=\"http://leaflet.cloudmade.com\" target=\"_blank\" title=\"\'Leaflet Maps Marker\' uses the JavaScript library \'Leaflet\' for interactive maps by CloudMade\">Leaflet</a>, <a href=\"http://mapicons.nicolasmollet.com\" target=\"_blank\" title=\"\'Leaflet Maps Marker\' uses icons from the \'Maps Icons Collection\'\">Icons</a>)'; 
	$attrib_osm_mapnik = __("Map",'lmm').': &copy; ' . date("Y") . ' <a href=\"http://www.openstreetmap.org\" target=\"_blank\">OpenStreetMap contributors</a>, <a href=\"http://creativecommons.org/licenses/by-sa/2.0/\" target=\"_blank\">CC-BY-SA</a>';
	$attrib_mapquest_osm = __("Map",'lmm').': Tiles Courtesy of <a href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"' . LEAFLET_PLUGIN_URL . 'inc/img/logo-mapquest.png\" style=\"display:inline;\" /> (<a href=\"http://www.openstreetmap.org\" target=\"_blank\">OpenStreetMap</a>, <a href=\"http://creativecommons.org/licenses/by-sa/2.0/\" target=\"_blank\">CC-BY-SA</a>)';
	$attrib_mapquest_aerial = __("Map",'lmm').': <a href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"' . LEAFLET_PLUGIN_URL . 'inc/img/logo-mapquest.png\" style=\"display:inline;\" />, Portions Courtesy NASA/JPL-Caltech and U.S. Depart. of Agriculture, Farm Service Agency';
	$attrib_ogdwien_basemap = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\" style=\"\">data.wien.gv.at</a>)';
	$attrib_ogdwien_satellite = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\">data.wien.gv.at</a>)';
	$attrib_cloudmade = __("Map",'lmm').': &copy; ' . date("Y") . ' <a href=\"http://www.openstreetmap.org\" target=\"_blank\" style=\"\">OpenStreetMap contributors</a>, <a href=\"http://creativecommons.org/licenses/by-sa/2.0/\" target=\"_blank\">CC-BY-SA</a>, Imagery &copy; <a href=\"http://cloudmade.com\" target=\"_blank\">CloudMade</a>';
	$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap_attribution' ]);
	$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap2_attribution' ]);
	$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap3_attribution' ]);
	$lmm_out .= '(function($) {'.PHP_EOL;
	$lmm_out .= $mapname.' = new L.Map("'.$mapname.'", { dragging: ' . $lmm_options['misc_map_dragging'] . ', touchZoom: ' . $lmm_options['misc_map_touchzoom'] . ', scrollWheelZoom: ' . $lmm_options['misc_map_scrollwheelzoom'] . ', doubleClickZoom: ' . $lmm_options['misc_map_doubleclickzoom'] . ', boxzoom: ' . $lmm_options['map_interaction_options_boxzoom'] . ', trackResize: ' . $lmm_options['misc_map_trackresize'] . ', worldCopyJump: ' . $lmm_options['map_interaction_options_worldcopyjump'] . ', closePopupOnClick: ' . $lmm_options['misc_map_closepopuponclick'] . ', keyboard: ' . $lmm_options['map_keyboard_navigation_options_keyboard'] . ', keyboardPanOffset: ' . intval($lmm_options['map_keyboard_navigation_options_keyboardpanoffset']) . ', keyboardZoomOffset: ' . intval($lmm_options['map_keyboard_navigation_options_keyboardzoomoffset']) . ', inertia: ' . $lmm_options['map_panning_inertia_options_inertia'] . ', inertiaDeceleration: ' . intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) . ', inertiaMaxSpeed: ' . intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) . ', zoomControl: ' . $lmm_options['misc_map_zoomcontrol'] . ', crs: ' . $lmm_options['misc_projections'] . ' });'.PHP_EOL;
	$lmm_out .= $mapname.'.attributionControl.setPrefix("' . $attrib_prefix . '");'.PHP_EOL;
	//info: define basemaps
	$lmm_out .= 'var osm_mapnik = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {maxZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_osm_mapnik . '", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var mapquest_osm = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png", {maxZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_mapquest_osm . '", subdomains: ["otile1","otile2","otile3","otile4"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var mapquest_aerial = new L.TileLayer("http://{s}.mqcdn.com/naip/{z}/{x}/{y}.png", {maxZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_mapquest_aerial . '", subdomains: ["oatile1","oatile2","oatile3","oatile4"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var googleLayer_roadmap = new L.Google("ROADMAP", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var googleLayer_satellite = new L.Google("SATELLITE", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var googleLayer_hybrid = new L.Google("HYBRID", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var googleLayer_terrain = new L.Google("TERRAIN", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) { 
		$lmm_out .= 'var bingaerial = new L.BingLayer("' . $lmm_options[ 'bingmaps_api_key' ] . '", {type: "Aerial", maxZoom: 21, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var bingaerialwithlabels = new L.BingLayer("' . $lmm_options[ 'bingmaps_api_key' ] . '", {type: "AerialWithLabels", maxZoom: 21, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
		$lmm_out .= 'var bingroad = new L.BingLayer("' . $lmm_options[ 'bingmaps_api_key' ] . '", {type: "Road", maxZoom: 21, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	};
	$lmm_out .= 'var ogdwien_basemap = new L.TileLayer("http://{s}.wien.gv.at/wmts/fmzk/pastell/google3857/{z}/{y}/{x}.jpeg", {maxZoom: 19, minZoom: 11, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_ogdwien_basemap . '", subdomains: ["maps","maps1", "maps2", "maps3"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var ogdwien_satellite = new L.TileLayer("http://{s}.wien.gv.at/wmts/lb/farbe/google3857/{z}/{y}/{x}.jpeg", {maxZoom: 19, minZoom: 11, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_ogdwien_satellite . '", subdomains: ["maps","maps1", "maps2", "maps3"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	//info: create Cloudmade TileURLs
	$cloudmade_double_resolution = ($lmm_options[ 'cloudmade_double_resolution' ] == 'enabled') ? "@2x" : "";
	$cloudmade2_double_resolution = ($lmm_options[ 'cloudmade2_double_resolution' ] == 'enabled') ? "@2x" : "";
	$cloudmade3_double_resolution = ($lmm_options[ 'cloudmade3_double_resolution' ] == 'enabled') ? "@2x" : "";
	$cloudmade_tileurl = "http://{s}.tile.cloudmade.com/" . $lmm_options[ 'cloudmade_api_key' ] . "/" . $lmm_options[ 'cloudmade_styleid' ] . $cloudmade_double_resolution . "/256/{z}/{x}/{y}.png"; 
	$cloudmade2_tileurl = "http://{s}.tile.cloudmade.com/" . $lmm_options[ 'cloudmade2_api_key' ] . "/" . $lmm_options[ 'cloudmade2_styleid' ] . $cloudmade2_double_resolution . "/256/{z}/{x}/{y}.png"; 
	$cloudmade3_tileurl = "http://{s}.tile.cloudmade.com/" . $lmm_options[ 'cloudmade3_api_key' ] . "/" . $lmm_options[ 'cloudmade3_styleid' ] . $cloudmade3_double_resolution . "/256/{z}/{x}/{y}.png"; 
	$lmm_out .= 'var cloudmade = new L.TileLayer("' . $cloudmade_tileurl . '", {maxZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_cloudmade . '", subdomains: ["a","b","c"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var cloudmade2 = new L.TileLayer("' . $cloudmade2_tileurl . '", {maxZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_cloudmade . '", subdomains: ["a","b","c"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var cloudmade3 = new L.TileLayer("' . $cloudmade3_tileurl . '", {maxZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_cloudmade . '", subdomains: ["a","b","c"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	//info: MapBox basemaps
	$lmm_out .= 'var mapbox = new L.TileLayer("http://{s}.tiles.mapbox.com/v3/' . $lmm_options[ 'mapbox_user' ] . '.' . $lmm_options[ 'mapbox_map' ] . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox_minzoom' ]) . ', maxZoom: ' . intval($lmm_options[ 'mapbox_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'mapbox_attribution' ]) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var mapbox2 = new L.TileLayer("http://{s}.tiles.mapbox.com/v3/' . $lmm_options[ 'mapbox2_user' ] . '.' . $lmm_options[ 'mapbox2_map' ] . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox2_minzoom' ]) . ', maxZoom: ' . intval($lmm_options[ 'mapbox2_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'mapbox2_attribution' ]) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var mapbox3 = new L.TileLayer("http://{s}.tiles.mapbox.com/v3/' . $lmm_options[ 'mapbox3_user' ] . '.' . $lmm_options[ 'mapbox3_map' ] . '/{z}/{x}/{y}.png", {minZoom: ' . intval($lmm_options[ 'mapbox3_minzoom' ]) . ', maxZoom: ' . intval($lmm_options[ 'mapbox3_maxzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'mapbox3_attribution' ]) . '", subdomains: ["a","b","c","d"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	//info: check if subdomains are set for custom basemaps
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	//info: define custom basemaps
	$lmm_out .= 'var custom_basemap = new L.TileLayer("' . $lmm_options[ 'custom_basemap_tileurl' ] . '", {maxZoom: ' . intval($lmm_options[ 'custom_basemap_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap_tms' ] . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_custom_basemap . '"' . $custom_basemap_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap_nowrap_enabled' ] . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var custom_basemap2 = new L.TileLayer("' . $lmm_options[ 'custom_basemap2_tileurl' ] . '", {maxZoom: ' . intval($lmm_options[ 'custom_basemap2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap2_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap2_tms' ] . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_custom_basemap2 . '"' . $custom_basemap2_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap2_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap2_nowrap_enabled' ] . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	$lmm_out .= 'var custom_basemap3 = new L.TileLayer("' . $lmm_options[ 'custom_basemap3_tileurl' ] . '", {maxZoom: ' . intval($lmm_options[ 'custom_basemap3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap3_minzoom' ]) . ', tms: ' . $lmm_options[ 'custom_basemap3_tms' ] . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_custom_basemap3 . '"' . $custom_basemap3_subdomains . ', continuousWorld: ' . $lmm_options[ 'custom_basemap3_continuousworld_enabled' ] . ', noWrap: ' . $lmm_options[ 'custom_basemap3_nowrap_enabled' ] . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	//info: check if subdomains are set for custom overlays
	$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom4_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	
	//info: define overlays
	if ( $overlays_custom == 1 ) {
	$lmm_out .= 'var overlays_custom = new L.TileLayer("' . $lmm_options[ 'overlays_custom_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom_attribution' ]) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom_opacity' ]) . ', maxZoom: ' . intval($lmm_options[ 'overlays_custom_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ( $overlays_custom2 == 1 ) {
	$lmm_out .= 'var overlays_custom2 = new L.TileLayer("' . $lmm_options[ 'overlays_custom2_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom2_attribution' ]) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom2_opacity' ]) . ', maxZoom: ' . intval($lmm_options[ 'overlays_custom2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom2_minzoom' ]) . $overlays_custom2_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ( $overlays_custom3 == 1 ) {
	$lmm_out .= 'var overlays_custom3 = new L.TileLayer("' . $lmm_options[ 'overlays_custom3_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom3_attribution' ]) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom3_opacity' ]) . ', maxZoom: ' . intval($lmm_options[ 'overlays_custom3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom3_minzoom' ]) . $overlays_custom3_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ( $overlays_custom4 == 1 ) {
	$lmm_out .= 'var overlays_custom4 = new L.TileLayer("' . $lmm_options[ 'overlays_custom4_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom4_attribution' ]) . '", opacity: ' . floatval($lmm_options[ 'overlays_custom4_opacity' ]) . ', maxZoom: ' . intval($lmm_options[ 'overlays_custom4_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom4_minzoom' ]) . $overlays_custom_subdomains . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	//info: check if subdomains are set for wms layers
	$wms_subdomains = ((isset($lmm_options[ 'wms_wms_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms2_subdomains = ((isset($lmm_options[ 'wms_wms2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms3_subdomains = ((isset($lmm_options[ 'wms_wms3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms4_subdomains = ((isset($lmm_options[ 'wms_wms4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms4_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms5_subdomains = ((isset($lmm_options[ 'wms_wms5_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms5_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms5_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms6_subdomains = ((isset($lmm_options[ 'wms_wms6_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms6_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms6_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms7_subdomains = ((isset($lmm_options[ 'wms_wms7_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms7_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms7_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms8_subdomains = ((isset($lmm_options[ 'wms_wms8_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms8_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms8_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms9_subdomains = ((isset($lmm_options[ 'wms_wms9_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms9_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms9_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms10_subdomains = ((isset($lmm_options[ 'wms_wms10_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms10_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms10_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	//info: define wms legends
	$wms_attribution = addslashes($lmm_options[ 'wms_wms_attribution' ]) . ( ($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) ? " (<a href=" . $lmm_options[ 'wms_wms_legend' ] . " target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms2_attribution = addslashes($lmm_options[ 'wms_wms2_attribution' ]) . ( ($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) ? " (<a href=" . $lmm_options[ 'wms_wms2_legend' ] . " target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms3_attribution = addslashes($lmm_options[ 'wms_wms3_attribution' ]) . ( ($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) ? " (<a href=" . $lmm_options[ 'wms_wms3_legend' ] . " target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms4_attribution = addslashes($lmm_options[ 'wms_wms4_attribution' ]) . ( ($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) ? " (<a href=" . $lmm_options[ 'wms_wms4_legend' ] . " target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms5_attribution = addslashes($lmm_options[ 'wms_wms5_attribution' ]) . ( ($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) ? " (<a href=" . $lmm_options[ 'wms_wms5_legend' ] . " target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms6_attribution = addslashes($lmm_options[ 'wms_wms6_attribution' ]) . ( ($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) ? " (<a href=" . $lmm_options[ 'wms_wms6_legend' ] . " target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms7_attribution = addslashes($lmm_options[ 'wms_wms7_attribution' ]) . ( ($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) ? " (<a href=" . $lmm_options[ 'wms_wms7_legend' ] . " target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms8_attribution = addslashes($lmm_options[ 'wms_wms8_attribution' ]) . ( ($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) ? " (<a href=" . $lmm_options[ 'wms_wms8_legend' ] . " target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms9_attribution = addslashes($lmm_options[ 'wms_wms9_attribution' ]) . ( ($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) ? " (<a href=" . $lmm_options[ 'wms_wms9_legend' ] . " target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms10_attribution = addslashes($lmm_options[ 'wms_wms10_attribution' ]) . ( ($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) ? " (<a href=" . $lmm_options[ 'wms_wms10_legend' ] . " target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	//info: define wms layers
	if ($wms == 1) {
	$lmm_out .= 'var wms = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms_baseurl' ] . '", {wmsid: "wms", layers: "' . addslashes($lmm_options[ 'wms_wms_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms_format' ]) . '", attribution: "' . $wms_attribution . '", transparent: "' . $lmm_options[ 'wms_wms_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms_version' ]) . '"' . $wms_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms2 == 1) {
	$lmm_out .= 'var wms2 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms2_baseurl' ] . '", {wmsid: "wms2", layers: "' . addslashes($lmm_options[ 'wms_wms2_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms2_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms2_format' ]) . '", attribution: "' . $wms2_attribution . '", transparent: "' . $lmm_options[ 'wms_wms2_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms2_version' ]) . '"' . $wms2_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms3 == 1) {
	$lmm_out .= 'var wms3 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms3_baseurl' ] . '", {wmsid: "wms3", layers: "' . addslashes($lmm_options[ 'wms_wms3_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms3_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms3_format' ]) . '", attribution: "' . $wms3_attribution . '", transparent: "' . $lmm_options[ 'wms_wms3_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms3_version' ]) . '"' . $wms3_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms4 == 1) {
	$lmm_out .= 'var wms4 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms4_baseurl' ] . '", {wmsid: "wms4", layers: "' . addslashes($lmm_options[ 'wms_wms4_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms4_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms4_format' ]) . '", attribution: "' . $wms4_attribution . '", transparent: "' . $lmm_options[ 'wms_wms4_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms4_version' ]) . '"' . $wms4_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms5 == 1) {
	$lmm_out .= 'var wms5 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms5_baseurl' ] . '", {wmsid: "wms5", layers: "' . addslashes($lmm_options[ 'wms_wms5_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms5_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms5_format' ]) . '", attribution: "' . $wms5_attribution . '", transparent: "' . $lmm_options[ 'wms_wms5_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms5_version' ]) . '"' . $wms5_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms6 == 1) {
	$lmm_out .= 'var wms6 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms6_baseurl' ] . '", {wmsid: "wms6", layers: "' . addslashes($lmm_options[ 'wms_wms6_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms6_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms6_format' ]) . '", attribution: "' . $wms6_attribution . '", transparent: "' . $lmm_options[ 'wms_wms6_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms6_version' ]) . '"' . $wms6_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms7 == 1) {
	$lmm_out .= 'var wms7 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms7_baseurl' ] . '", {wmsid: "wms7", layers: "' . addslashes($lmm_options[ 'wms_wms7_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms7_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms7_format' ]) . '", attribution: "' . $wms7_attribution . '", transparent: "' . $lmm_options[ 'wms_wms7_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms7_version' ]) . '"' . $wms7_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms8 == 1) {
	$lmm_out .= 'var wms8 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms8_baseurl' ] . '", {wmsid: "wms8", layers: "' . addslashes($lmm_options[ 'wms_wms8_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms8_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms8_format' ]) . '", attribution: "' . $wms8_attribution . '", transparent: "' . $lmm_options[ 'wms_wms8_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms8_version' ]) . '"' . $wms8_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms9 == 1) {
	$lmm_out .= 'var wms9 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms9_baseurl' ] . '", {wmsid: "wms9", layers: "' . addslashes($lmm_options[ 'wms_wms9_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms9_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms9_format' ]) . '", attribution: "' . $wms9_attribution . '", transparent: "' . $lmm_options[ 'wms_wms9_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms9_version' ]) . '"' . $wms9_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	if ($wms10 == 1) {
	$lmm_out .= 'var wms10 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms10_baseurl' ] . '", {wmsid: "wms10", layers: "' . addslashes($lmm_options[ 'wms_wms10_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms10_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms10_format' ]) . '", attribution: "' . $wms10_attribution . '", transparent: "' . $lmm_options[ 'wms_wms10_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'inc/img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms10_version' ]) . '"' . $wms10_subdomains  . ', detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
	}
	//info: controlbox - basemaps
	$lmm_out .= 'var layersControl = new L.Control.Layers('.PHP_EOL;
	$lmm_out .= '{';
	$basemaps_available = '';
	if ( (isset($lmm_options[ 'controlbox_osm_mapnik' ]) == TRUE ) && ($lmm_options[ 'controlbox_osm_mapnik' ] == 1 ) )
		$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_osm_mapnik' ]) . "': osm_mapnik,";
	if ( (isset($lmm_options[ 'controlbox_mapquest_osm' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_osm' ] == 1 ) )
		$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_mapquest_osm' ]) . "': mapquest_osm,";
	if ( (isset($lmm_options[ 'controlbox_mapquest_aerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_aerial' ] == 1 ) )
		$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_mapquest_aerial' ]) . "': mapquest_aerial,";
	if ( (isset($lmm_options[ 'controlbox_googleLayer_roadmap' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_roadmap' ] == 1 ) )
		$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_googleLayer_roadmap' ]) . "': googleLayer_roadmap,";
	if ( (isset($lmm_options[ 'controlbox_googleLayer_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_satellite' ] == 1 ) )
		$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_googleLayer_satellite' ]) . "': googleLayer_satellite,";
	if ( (isset($lmm_options[ 'controlbox_googleLayer_hybrid' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_hybrid' ] == 1 ) )
		$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_googleLayer_hybrid' ]) . "': googleLayer_hybrid,";
	if ( (isset($lmm_options[ 'controlbox_googleLayer_terrain' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_terrain' ] == 1 ) )
		$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_googleLayer_terrain' ]) . "': googleLayer_terrain,";
	if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) { 
		if ( (isset($lmm_options[ 'controlbox_bingaerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerial' ] == 1 ) )
			$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_bingaerial' ]) . "': bingaerial,";
		if ( (isset($lmm_options[ 'controlbox_bingaerialwithlabels' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerialwithlabels' ] == 1 ) )
			$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_bingaerialwithlabels' ]) . "': bingaerialwithlabels,";
		if ( (isset($lmm_options[ 'controlbox_bingroad' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingroad' ] == 1 ) )
			$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_bingroad' ]) . "': bingroad,";
	};
	if (((isset($lmm_options[ 'controlbox_ogdwien_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_basemap' ] == 1 )) && ((($lat <= '48.326583')  && ($lat >= '48.114308')) && (($lon <= '16.55056')  && ($lon >= '16.187325')) ))
		$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_ogdwien_basemap' ]) . "': ogdwien_basemap,";
	if (((isset($lmm_options[ 'controlbox_ogdwien_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_satellite' ] == 1 )) && ((($lat <= '48.326583')  && ($lat >= '48.114308')) && (($lon <= '16.55056')  && ($lon >= '16.187325')) ))
		$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_ogdwien_satellite' ]) . "': ogdwien_satellite,";
	if ( (isset($lmm_options[ 'controlbox_cloudmade' ]) == TRUE ) && ($lmm_options[ 'controlbox_cloudmade' ] == 1 ) )
		$basemaps_available .= "'".addslashes($lmm_options[ 'cloudmade_name' ])."': cloudmade,";
	if ( (isset($lmm_options[ 'controlbox_cloudmade2' ]) == TRUE ) && ($lmm_options[ 'controlbox_cloudmade2' ] == 1 ) )
		$basemaps_available .= "'".addslashes($lmm_options[ 'cloudmade2_name' ])."': cloudmade2,";
	if ( (isset($lmm_options[ 'controlbox_cloudmade3' ]) == TRUE ) && ($lmm_options[ 'controlbox_cloudmade3' ] == 1 ) )
		$basemaps_available .= "'".addslashes($lmm_options[ 'cloudmade3_name' ])."': cloudmade3,";
	if ( (isset($lmm_options[ 'controlbox_mapbox' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox' ] == 1 ) )
		$basemaps_available .= "'".addslashes($lmm_options[ 'mapbox_name' ])."': mapbox,";
	if ( (isset($lmm_options[ 'controlbox_mapbox2' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox2' ] == 1 ) )
		$basemaps_available .= "'".addslashes($lmm_options[ 'mapbox2_name' ])."': mapbox2,";
	if ( (isset($lmm_options[ 'controlbox_mapbox3' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox3' ] == 1 ) )
		$basemaps_available .= "'".addslashes($lmm_options[ 'mapbox3_name' ])."': mapbox3,";
	if ( (isset($lmm_options[ 'controlbox_custom_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap' ] == 1 ) )
		$basemaps_available .= "'".addslashes($lmm_options[ 'custom_basemap_name' ])."': custom_basemap,";
	if ( (isset($lmm_options[ 'controlbox_custom_basemap2' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap2' ] == 1 ) )
		$basemaps_available .= "'".addslashes($lmm_options[ 'custom_basemap2_name' ])."': custom_basemap2,";
	if ( (isset($lmm_options[ 'controlbox_custom_basemap3' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap3' ] == 1 ) )
		$basemaps_available .= "'".addslashes($lmm_options[ 'custom_basemap3_name' ])."': custom_basemap3,";
	//info: needed for IE7 compatibility
	$lmm_out .= substr($basemaps_available, 0, -1);
	$lmm_out .= '},'.PHP_EOL;
	
	//info: controlbox - add available overlays
	$lmm_out .= '{';
	$overlays_custom_available = '';
	if ( (isset($lmm_options[ 'overlays_custom' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom' ] == 1 ) && ( $overlays_custom == 1 ) )
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom_name' ])."': overlays_custom,";
	if ( (isset($lmm_options[ 'overlays_custom2' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom2' ] == 1 ) && ( $overlays_custom2 == 1 ) )
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom2_name' ])."': overlays_custom2,";
	if ( (isset($lmm_options[ 'overlays_custom3' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom3' ] == 1 ) && ( $overlays_custom3 == 1 ) )
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom3_name' ])."': overlays_custom3,";
	if ( (isset($lmm_options[ 'overlays_custom4' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom4' ] == 1 ) && ( $overlays_custom4 == 1 ) ) 
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom4_name' ])."': overlays_custom4,"; 
	//info: needed for IE7 compatibility
	$lmm_out .= substr($overlays_custom_available, 0, -1);
	$lmm_out .= '},'.PHP_EOL;
	
	//info: controlbox - hidden / collapsed / expanded status
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 0 ) )
		$lmm_out .= '{ } );'.PHP_EOL;
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 1 ) )
		$lmm_out .= '{ collapsed: !L.Browser.touch } );'.PHP_EOL;
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 2 ) )
		$lmm_out .= '{ collapsed: false } );'.PHP_EOL;
	$lmm_out .= $mapname.'.setView(new L.LatLng('.$lat.', '.$lon.'), '.$zoom.');'.PHP_EOL;
	$lmm_out .= $mapname.'.addLayer(' . $basemap . ')';
	//info: controlbox - check active overlays on marker/layer level
	if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
		$lmm_out .= ".addLayer(overlays_custom)";
	if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
		$lmm_out .= ".addLayer(overlays_custom2)";
	if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
		$lmm_out .= ".addLayer(overlays_custom3)";
	if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
		$lmm_out .= ".addLayer(overlays_custom4)";
	//info: controlbox - add active overlays on marker level
	if ( $wms == 1 )
		$lmm_out .= ".addLayer(wms)";
	if ( $wms2 == 1 )
		$lmm_out .= ".addLayer(wms2)";
	if ( $wms3 == 1 )
		$lmm_out .= ".addLayer(wms3)";
	if ( $wms4 == 1 )
		$lmm_out .= ".addLayer(wms4)";
	if ( $wms5 == 1 )
		$lmm_out .= ".addLayer(wms5)";
	if ( $wms6 == 1 )
		$lmm_out .= ".addLayer(wms6)";
	if ( $wms7 == 1 )
		$lmm_out .= ".addLayer(wms7)";
	if ( $wms8 == 1 )
		$lmm_out .= ".addLayer(wms8)";
	if ( $wms9 == 1 )
		$lmm_out .= ".addLayer(wms9)";
	if ( $wms10 == 1 )
		$lmm_out .= ".addLayer(wms10)";
	$lmm_out .= ( (isset($controlbox) == TRUE) && ($controlbox != 0) ) ? ".addControl(layersControl);" : ";".PHP_EOL;
	//info: add scale control
	if ( $lmm_options['map_scale_control'] == 'enabled' ) {
	$lmm_out .= "L.control.scale({position:'" . $lmm_options['map_scale_control_position'] . "', maxWidth: " . intval($lmm_options['map_scale_control_maxwidth']) . ", metric: " . $lmm_options['map_scale_control_metric'] . ", imperial: " . $lmm_options['map_scale_control_imperial'] . ", updateWhenIdle: " . $lmm_options['map_scale_control_updatewhenidle'] . "}).addTo(" . $mapname . ");".PHP_EOL;
	}
	if (!(empty($mlat) or empty($mlon)) ) {
	if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') { $defaults_marker_icon_title = "title: '" . htmlspecialchars($markername) . "', "; } else { $defaults_marker_icon_title = ""; };
	$lmm_out .= 'var marker = new L.Marker(new L.LatLng('.$mlat.', '.$mlon.'),{ ' . $defaults_marker_icon_title . ' opacity: ' . floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) . '});'.PHP_EOL;
 	if ($micon == NULL) { 
  		$lmm_out .= "marker.options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . htmlspecialchars($lmm_options[ 'defaults_marker_icon_shadow_url' ]) . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_default'});".PHP_EOL;
  	} else {
  		$lmm_out .= "marker.options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_ICONS_URL . "/" . $icon . "',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . htmlspecialchars($lmm_options[ 'defaults_marker_icon_shadow_url' ]) . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_" . substr($icon, 0, -4) . "'});".PHP_EOL;
	};
	if ( (empty($mpopuptext)) && ($lmm_options['directions_popuptext_panel'] == 'no') ) $lmm_out .= 'marker.options.clickable = false;'.PHP_EOL;
	$lmm_out .= $mapname.'.addLayer(marker);'.PHP_EOL;
	
	if ( ($lmm_options['directions_popuptext_panel'] == 'yes') && ($lmm_options['directions_provider'] == 'googlemaps') ) { 
	$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
	$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
	$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
	$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
	$mpopuptext_css = ($mpopuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;" : "";
	//info: Google language localization (directions)
	if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
		$google_language = '';
	} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
		if ( defined('WPLANG') ) { $google_language = '&hl=' . substr(WPLANG, 0, 2); } else { $google_language =  '&hl=en'; }
	} else {
		$google_language = '&hl=' . $lmm_options['google_maps_language_localization'];
	}
	$mpopuptext = $mpopuptext . "<div style='" . $mpopuptext_css . "'><a href=http://maps.google.com/maps?daddr=" . $lat . "," . $lon . "&t=" . $lmm_options[ 'directions_googlemaps_map_type' ] . "&layer=" . $lmm_options[ 'directions_googlemaps_traffic' ] . "&doflg=" . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . "&om=" . $lmm_options[ 'directions_googlemaps_overview_map' ] . " target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a></div>";
	} else if ( ($lmm_options['directions_popuptext_panel'] == 'yes') && ($lmm_options['directions_provider'] == 'yours') ) {
	$mpopuptext_css = ($mpopuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;" : "";
	$mpopuptext = $mpopuptext . "<div style='" . $mpopuptext_css . "'><a href=http://www.yournavigation.org/?tlat=" . $lat . "&tlon=" . $lon . "&v=" . $lmm_options[ 'directions_yours_type_of_transport' ] . "&fast=" . $lmm_options[ 'directions_yours_route_type' ] . "&layer=" . $lmm_options[ 'directions_yours_layer' ] . " target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a></div>"; 
	} else if ( ($lmm_options['directions_popuptext_panel'] == 'yes') && ($lmm_options['directions_provider'] == 'osrm') ) {
	$mpopuptext_css = ($mpopuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;" : "";
	$mpopuptext = $mpopuptext . "<div style='" . $mpopuptext_css . "'><a href=http://map.project-osrm.org/?hl=" . $lmm_options[ 'directions_osrm_language' ] . "&loc=" . $lat . "," . $lon . "&df=" . $lmm_options[ 'directions_osrm_units' ] . " target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a></div>"; 
	} else if ( ($lmm_options['directions_popuptext_panel'] == 'yes') && ($lmm_options['directions_provider'] == 'ors') ) {
	$mpopuptext_css = ($mpopuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;" : "";
	$mpopuptext = $mpopuptext . "<div style='" . $mpopuptext_css . "'><a href=http://openrouteservice.org/index.php?end=" . $lon . "," . $lat . "&pref=" . $lmm_options[ 'directions_ors_route_preferences' ] . "&lang=" . $lmm_options[ 'directions_ors_language' ] . "&noMotorways=" . $lmm_options[ 'directions_ors_no_motorways' ] . "&noTollways=" . $lmm_options[ 'directions_ors_no_tollways' ] . " target='_blank' title='" . esc_attr__('Get directions','lmm') . "'>" . __('Directions','lmm') . "</a></div>"; 
	}
	if (!empty($mpopuptext)) $lmm_out .= 'marker.bindPopup("' . preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$mpopuptext) . '", {maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', autoPan: ' . $lmm_options['defaults_marker_popups_autopan'] . ', closeButton: ' . $lmm_options['defaults_marker_popups_closebutton'] . ', autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')})'.$mopenpopup.';'.PHP_EOL;
	} else if (!empty($geojson) or !empty($geojsonurl) or !empty($layer) ) {
		$lmm_out .= 'var geojsonObj, mapIcon, marker_clickable, marker_title;'.PHP_EOL;
		//info: added for next versions
		if (!empty($geojson)) {
		$lmm_out .= 'geojsonObj = eval("'.$geojson.'");'.PHP_EOL;
		}
		if (!empty($geojsonurl)) {
		$lmm_out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "'.$geojsonurl.'", async: false, cache: false}).responseText + ")");'.PHP_EOL;
		}
		//2do: check if loading marker via GeoJSON has advantages 
		/*
		if ( !empty($marker) ) {
		$lmm_out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker='.$marker.'", async: false, cache: false}).responseText + ")");'.PHP_EOL;
		} 
		*/		
		//info: load GeoJSON for layer maps
		if (!empty($layer) && ($multi_layer_map == 0) ) {
			$lmm_out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $id . '", async: false, cache: false}).responseText + ")");'.PHP_EOL;
		} else if (!empty($layer) && ($multi_layer_map == 1) ) {
			$lmm_out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $multi_layer_map_list . '", async: false, cache: false}).responseText + ")");'.PHP_EOL;
		}
		$lmm_out .= 'L.geoJson(geojsonObj, {'.PHP_EOL;
		$lmm_out .= '		onEachFeature: function(feature, marker) {'.PHP_EOL;
		$lmm_out .= "			if (feature.properties.text != '') {".PHP_EOL;
		$lmm_out .= '			marker.bindPopup(feature.properties.text, {'.PHP_EOL;
		$lmm_out .= '			maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', '.PHP_EOL;
		$lmm_out .= '			minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', '.PHP_EOL;
		$lmm_out .= '			maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', '.PHP_EOL;
		$lmm_out .= '			autoPan: ' . $lmm_options['defaults_marker_popups_autopan'] . ', '.PHP_EOL;
		$lmm_out .= '			closeButton: ' . $lmm_options['defaults_marker_popups_closebutton'] . ', '.PHP_EOL;
		$lmm_out .= '			autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')'.PHP_EOL;
		$lmm_out .= '			});'.PHP_EOL;
		$lmm_out .= '			}'.PHP_EOL;
		$lmm_out .= '		},'.PHP_EOL;
		$lmm_out .= 'pointToLayer: function (feature, latlng) {'.PHP_EOL;
		$lmm_out .= '	mapIcon = L.icon({ '.PHP_EOL;
		$lmm_out .= "		iconUrl: (feature.properties.icon != '') ? '" . LEAFLET_PLUGIN_ICONS_URL . "/' + feature.properties.icon : '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png" . "',".PHP_EOL;
		$lmm_out .= '		iconSize: [' . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . '],'.PHP_EOL;
		$lmm_out .= '		iconAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . '],'.PHP_EOL;
		$lmm_out .= '		popupAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . '],'.PHP_EOL;
		$lmm_out .= "		shadowUrl: '" . htmlspecialchars($lmm_options[ 'defaults_marker_icon_shadow_url' ]) . "',".PHP_EOL;
		$lmm_out .= '		shadowSize: [' . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . '],'.PHP_EOL;
		$lmm_out .= '		shadowAnchor: [' . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ', ' . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . '],'.PHP_EOL;
		$lmm_out .= "		className: (feature.properties.icon == '') ? 'lmm_marker_icon_default' : 'lmm_marker_icon_'+ feature.properties.icon.slice(0,-4)".PHP_EOL;
		$lmm_out .= '	});'.PHP_EOL;
		$lmm_out .= "if (feature.properties.text == '') { marker_clickable = false } else { marker_clickable = true };".PHP_EOL;
		if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') {
		$lmm_out .= "if (feature.properties.markername == '') { marker_title = '' } else { marker_title = feature.properties.markername };".PHP_EOL;
		}
		$lmm_out .= 'return L.marker(latlng, {icon: mapIcon, clickable: marker_clickable, title: marker_title, opacity: ' . floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) . '});'.PHP_EOL;
		$lmm_out .= '}'.PHP_EOL;
		$lmm_out .= '}).addTo(' . $mapname . ');'.PHP_EOL;
    }
  $lmm_out .= '})(jQuery);'.PHP_EOL;
  $lmm_out .= '/* ]] > */'.PHP_EOL;
  $lmm_out .= '</script>'.PHP_EOL;
  $lmm_out .= '</div>'; //info: end leaflet_maps_marker_$uid
  } //info: end (!is_feed())
  return $lmm_out;
	} //info: end check if marker/layer exists
  } //info: end lmm_showmap()
  function lmm_admin_menu() {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$page = add_object_page('Leaflet Maps Marker', 'Leaflet Maps Marker', $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_markers', array(&$this, 'lmm_list_markers'), LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-page.png' );
	$page2 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('List all markers', 'lmm'), __('List all markers', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_markers', array(&$this, 'lmm_list_markers') );
	$page3 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('add/edit marker', 'lmm'), __('Add new marker', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_marker', array(&$this, 'lmm_marker') );
	$page4 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('List all layers', 'lmm'), __('List all layers', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_layers', array(&$this, 'lmm_list_layers') );
	$page5 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('add/edit layer', 'lmm'), __('Add new layer', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_layer', array(&$this, 'lmm_layer') );
	$page6 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('Tools', 'lmm'), __('Tools', 'lmm'), 'activate_plugins','leafletmapsmarker_tools', array(&$this, 'lmm_tools') );
	$page7 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('Settings', 'lmm'), __('Settings', 'lmm'), 'activate_plugins','leafletmapsmarker_settings', array(&$this, 'lmm_settings') );
	$page8 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('Help & Credits', 'lmm'), __('Help & Credits', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_help', array(&$this, 'lmm_help') );
	//info: add javascript - leaflet.js - for admin area
	add_action('admin_print_scripts-'.$page3, array(&$this, 'lmm_admin_enqueue_scripts'),7);
	add_action('admin_print_scripts-'.$page5, array(&$this, 'lmm_admin_enqueue_scripts'),8);
	add_action('admin_print_scripts-'.$page7, array(&$this, 'lmm_admin_jquery_ui'),9); 
	//info: add css styles for admin area
	add_action('admin_print_styles-'.$page, array(&$this, 'lmm_admin_enqueue_stylesheets'),17);
	add_action('admin_print_styles-'.$page2, array(&$this, 'lmm_admin_enqueue_stylesheets'),18);
	add_action('admin_print_styles-'.$page3, array(&$this, 'lmm_admin_enqueue_stylesheets'),19);
	add_action('admin_print_styles-'.$page4, array(&$this, 'lmm_admin_enqueue_stylesheets'),20);
	add_action('admin_print_styles-'.$page5, array(&$this, 'lmm_admin_enqueue_stylesheets'),21);
	add_action('admin_print_styles-'.$page6, array(&$this, 'lmm_admin_enqueue_stylesheets'),22);
	add_action('admin_print_styles-'.$page7, array(&$this, 'lmm_admin_enqueue_stylesheets'),23);
	add_action('admin_print_styles-'.$page8, array(&$this, 'lmm_admin_enqueue_stylesheets'),23);	
	//info: add css styles for datepicker	
	add_action('admin_print_styles-'.$page3, array(&$this, 'lmm_admin_enqueue_stylesheets_datepicker'),24);
	//info: add contextual help on all pages
	add_action('admin_print_scripts-'.$page, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page2, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page3, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page4, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page5, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page6, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page7, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page8, array(&$this, 'lmm_add_contextual_help'));	
	//info: add jquery datepicker on marker page
	add_action('admin_print_scripts-'.$page3, array(&$this, 'lmm_admin_enqueue_scripts_jquerydatepicker'));	
	//info: add image css override for marker+layer edit page
	add_action( 'admin_head-'. $page3, array(&$this, 'lmm_image_css_override'),1000);
	add_action( 'admin_head-'. $page5, array(&$this, 'lmm_image_css_override'),1000);
  }
  function lmm_add_admin_bar_menu() {
	global $wp_version;
	if ( version_compare( $wp_version, '3.1', '>=' ) ) 
	{
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		if ( $lmm_options[ 'admin_bar_integration' ] == 'enabled' && current_user_can($lmm_options[ 'capabilities_edit' ]) ) 
		{
		global $wp_admin_bar;
			$menu_items = array(
				array(
					'id' => 'lmm',
					'title' => '<img style="float:left;margin:5px 5px 0 0;" src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-page.png"/></span> Leaflet Maps Marker',
					'href' => admin_url('admin.php?page=leafletmapsmarker_markers'),
					'meta' => array( 'title' => 'Wordpress-Plugin ' . __('by','lmm') . ' www.mapsmarker.com' )
				),
				array(
					'id' => 'lmm-markers',
					'parent' => 'lmm',
					'title' => __('List all markers','lmm'),
					'href' => admin_url('admin.php?page=leafletmapsmarker_markers')
				),
				array(
					'id' => 'lmm-add-marker',
					'parent' => 'lmm',
					'title' => __('Add new marker','lmm'),
					'href' => admin_url('admin.php?page=leafletmapsmarker_marker')
				),
				array(
					'id' => 'lmm-layers',
					'parent' => 'lmm',
					'title' => __('List all layers','lmm'),
					'href' => admin_url('admin.php?page=leafletmapsmarker_layers')
				),
				array(
					'id' => 'lmm-add-layers',
					'parent' => 'lmm',
					'title' => __('Add new layer','lmm'),
					'href' => admin_url('admin.php?page=leafletmapsmarker_layer')
				)			
			);
			if ( current_user_can( 'activate_plugins' ) ) {
				$menu_items = array_merge($menu_items, array(
					array(
						'id' => 'lmm-tools',
						'parent' => 'lmm',
						'title' => __('Tools','lmm'),
						'href' => admin_url('admin.php?page=leafletmapsmarker_tools')
					),
					array(
						'id' => 'lmm-settings',
						'parent' => 'lmm',
						'title' => __('Settings','lmm'),
						'href' => admin_url('admin.php?page=leafletmapsmarker_settings')
					)
				));
			}
			$menu_items = array_merge($menu_items, array(
					array(
						'id' => 'lmm-help-credits',
						'parent' => 'lmm',
						'title' => __('Help & Credits','lmm'),
						'href' => admin_url('admin.php?page=leafletmapsmarker_help')
					),
					array(
						'id' => 'lmm-plugin-website',
						'parent' => 'lmm',
						'title' => 'mapsmarker.com',
						'href' => 'http://www.mapsmarker.com',
						'meta' => array( 'target' => '_blank', 'title' => __('Open plugin website','lmm') )
					)	
				));
			
			foreach ($menu_items as $menu_item) {
				$wp_admin_bar->add_menu($menu_item);
			}
		}
	}
  }
  function lmm_add_contextual_help() {
	global $wp_version;
	$helptext = '<h4>' . __('Do you have questions or issues with Leaflet Maps Marker? Please use the following support channels appropriately.','lmm') . '</h4>';
	$helptext .= '<ul>';
	$helptext .= '<li><a href="http://www.mapsmarker.com/faq/" target="_blank">' . __('FAQ','lmm') . '</a> (' . __('frequently asked questions','lmm') . ')</li>';
	$helptext .= '<li><a href="http://www.mapsmarker.com/docs/" target="_blank">' . __('Documentation','lmm') . '</a></li>';
	$helptext .= '<li><a href="http://www.mapsmarker.com/ideas/" target="_blank">' . __('Ideas','lmm') . '</a> (' . __('feature requests','lmm') . ')</li>';
	$helptext .= '<li><a href="http://wordpress.org/support/plugin/leaflet-maps-marker" target="_blank">WordPress Support Forum</a> (' . __('free community support','lmm') . ')</li>';
	$helptext .= '<li><a href="http://wpquestions.com/affiliates/register/name/robertharm" target="_blank">WP Questions</a> (' . __('paid community support','lmm') . ')</li>';
	$helptext .= '<li><a href="http://wphelpcenter.com/" target="_blank">WordPress HelpCenter</a> (' . __('paid professional support','lmm') . ')</li>';
	$helptext .= '</ul>';
	$helptext .= '<p>' . __('More information on support','lmm') . ': <a href="http://www.mapsmarker.com/support/" target="_blank">http://www.mapsmarker.com/support</a></p>';
	if ( version_compare( $wp_version, '3.3', '<' ) ) 
	{
		global $current_screen;
		add_contextual_help( $current_screen, $helptext );
	} 
	else if ( version_compare( $wp_version, '3.3', '>=' ) ) 
	{
        $screen = get_current_screen();
		$screen->add_help_tab( array( 'id' => 'lmm_help_tab', 'title' => __('Help & Support','lmm'), 'content' => $helptext ));
	}	
  }
  function lmm_admin_jquery_ui() {
    wp_enqueue_script( array ( 'jquery', 'jquery-ui-tabs' ) );
  }
  function lmm_frontend_enqueue_scripts() {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$plugin_version = get_option('leafletmapsmarker_version');
	if ( is_admin() ) { $gmaps_libraries = '&libraries=places'; } else { $gmaps_libraries =  ''; }
	//info: Google language localization (JSON API)
	if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
		$google_language = '';
	} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
		if ( defined('WPLANG') ) { $google_language = "&language=" . substr(WPLANG, 0, 2); } else { $google_language =  '&language=en'; }
	} else {
		$google_language = "&language=" . $lmm_options['google_maps_language_localization'];
	}
	if ($lmm_options['google_maps_base_domain_custom'] == '') {
		$gmaps_base_domain = "&base_domain=" . $lmm_options['google_maps_base_domain'];
	} else {
		$gmaps_base_domain = "&base_domain=" . $lmm_options['google_maps_base_domain_custom'];
	}
	wp_enqueue_script( array ( 'jquery' ) );
    //info: Google API key
    if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = $lmm_options['google_maps_api_key']; } else { $google_maps_api_key = ''; }
    wp_enqueue_script( 'leafletmapsmarker-googlemaps-loader', 'https://www.google.com/jsapi?key='.$google_maps_api_key, array(), NULL);
	//info: load leaflet.js + plugins
	wp_enqueue_script( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js', array('leafletmapsmarker-googlemaps-loader'), $plugin_version); 
	wp_localize_script('leafletmapsmarker', 'leafletmapsmarker_L10n', array(
		'lmm_zoom_in' => __( 'Zoom in', 'lmm' ),
		'lmm_zoom_out' => __( 'Zoom out', 'lmm' ),
		'lmm_googlemaps_language' => $google_language,
		'lmm_googlemaps_libraries' => $gmaps_libraries,
		'lmm_googlemaps_base_domain' => $gmaps_base_domain
		) );
  }
  function lmm_admin_enqueue_scripts() {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$plugin_version = get_option('leafletmapsmarker_version');
	if ( is_admin() ) { $gmaps_libraries = '&libraries=places'; } else { $gmaps_libraries =  ''; }	
	if ( defined('WPLANG') ) { $lang = substr(WPLANG, 0, 2); } else { $lang =  'en'; }
	//info: Google language localization (JSON API)
	if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
		$google_language = '';
	} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
		if ( defined('WPLANG') ) { $google_language = "&language=" . substr(WPLANG, 0, 2); } else { $google_language =  '&language=en'; }
	} else {
		$google_language = "&language=" . $lmm_options['google_maps_language_localization'];
	}
	if ($lmm_options['google_maps_base_domain_custom'] != '') {
		$gmaps_base_domain = "&base_domain=" . $lmm_options['google_maps_base_domain'];
	} else {
		$gmaps_base_domain = "&base_domain=" . $lmm_options['google_maps_base_domain_custom'];
	}
	wp_enqueue_script( array ( 'jquery' ) );
    //info: Google API key
	if ( isset($lmm_options['google_maps_api_key']) && ($lmm_options['google_maps_api_key'] != NULL) ) { $google_maps_api_key = $lmm_options['google_maps_api_key']; } else { $google_maps_api_key = ''; }
	wp_enqueue_script( 'leafletmapsmarker-googlemaps-loader', 'https://www.google.com/jsapi?key='.$google_maps_api_key, array(), NULL);
	//info: load leaflet.js + plugins
	wp_enqueue_script( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js', array('leafletmapsmarker-googlemaps-loader'), $plugin_version); 
	wp_localize_script('leafletmapsmarker', 'leafletmapsmarker_L10n', array(
		'lmm_zoom_in' => __( 'Zoom in', 'lmm' ),
		'lmm_zoom_out' => __( 'Zoom out', 'lmm' ),
		'lmm_googlemaps_language' => $google_language,
		'lmm_googlemaps_libraries' => $gmaps_libraries,
		'lmm_googlemaps_base_domain' => $gmaps_base_domain
		) );
  }
  function lmm_image_css_override() {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	echo '<style type="text/css" id="leafletmapsmarker-image-css-override">.leaflet-popup-content img { max-width:' . intval($lmm_options['defaults_marker_popups_image_max_width']) . 'px !important; height:auto; margin: 0px !important; padding: 0px !important; box-shadow:none !important; }</style>';
  }
  function lmm_admin_enqueue_scripts_jquerydatepicker() {
	$plugin_version = get_option('leafletmapsmarker_version');
	wp_enqueue_script( array ( 'jquery', 'jquery-ui-tabs','jquery-ui-datepicker','jquery-ui-slider' ) );
	wp_enqueue_script( 'jquery-ui-timepicker-addon', LEAFLET_PLUGIN_URL . 'inc/js/jquery-ui-timepicker-addon.js', array('jquery', 'jquery-ui-tabs','jquery-ui-datepicker'), $plugin_version); 
  }
  function lmm_frontend_enqueue_stylesheets() {
	global $wp_styles;
	$plugin_version = get_option('leafletmapsmarker_version');
	wp_register_style('leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.css', array(), $plugin_version);
	wp_enqueue_style('leafletmapsmarker');
	wp_register_style('leafletmapsmarker-ie-only', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.ie.css', array(), $plugin_version);
	wp_enqueue_style('leafletmapsmarker-ie-only');
	$wp_styles->add_data('leafletmapsmarker-ie-only', 'conditional', 'lt IE 9');
  }
  function lmm_admin_enqueue_stylesheets() {
	global $wp_styles;
	$plugin_version = get_option('leafletmapsmarker_version');
	wp_register_style( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.css', array(), $plugin_version);
	wp_enqueue_style( 'leafletmapsmarker' );
	wp_register_style( 'leafletmapsmarker-admin', LEAFLET_PLUGIN_URL . 'inc/css/leafletmapsmarker-admin.css', array(), $plugin_version);
	wp_enqueue_style('leafletmapsmarker-admin' );
	wp_register_style('leafletmapsmarker-ie-only', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.ie.css', array(), $plugin_version);
	wp_enqueue_style('leafletmapsmarker-ie-only');
	$wp_styles->add_data('leafletmapsmarker-ie-only', 'conditional', 'lt IE 9');
   }
  function lmm_admin_enqueue_stylesheets_datepicker() {
	$plugin_version = get_option('leafletmapsmarker_version');
	wp_register_style( 'jquery-ui-all', LEAFLET_PLUGIN_URL . 'inc/css/jquery-datepicker-theme/jquery-ui-1.8.16.custom.css', array(), $plugin_version);
	wp_enqueue_style( 'jquery-ui-all' );
	wp_register_style( 'jquery-ui-timepicker-addon', LEAFLET_PLUGIN_URL . 'inc/css/jquery-datepicker-theme/jquery-ui-timepicker-addon.css', array('jquery-ui-all'), NULL );
	wp_enqueue_style( 'jquery-ui-timepicker-addon' );	
   }   
  function lmm_install_and_updates() {
	//info: set transient to execute install & update-routine only once a day
	$current_version = "v282"; //2do - mandatory: change on each update!
	$schedule_transient = 'leafletmapsmarker_install_update_cache_' . $current_version . '_' . date('d');
	$install_update_schedule = get_transient( $schedule_transient );
	if ( $install_update_schedule === FALSE ) {
		$schedule_transient = 'leafletmapsmarker_install_update_cache_' . $current_version . '_' . date('d');
		set_transient( $schedule_transient, 'execute install and update-routine only once a day', 60*60*24 );
		include('inc' . DIRECTORY_SEPARATOR . 'install-and-updates.php');
	}
  }
  function lmm_plugin_meta_links() {
	define( 'FB_BASENAME', plugin_basename( __FILE__ ) ); 
	define( 'FB_BASEFOLDER', plugin_basename( dirname( __FILE__ ) ) );
	define( 'FB_FILENAME', str_replace( FB_BASEFOLDER.'/', '', plugin_basename(__FILE__) ) );
	function leafletmapsmarker_filter_plugin_meta($links, $file) {
    	if ( $file == FB_BASENAME ) {
        	array_unshift(
	            $links,
    	        '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers">'.__('Markers','lmm').'</a>',
        	    '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers">'.__('Layers','lmm').'</a>' ,
            	'<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings">'.__('Settings','lmm').'</a>'
	        );
    	}
	    return $links;
	}
	add_filter( 'plugin_action_links', 'leafletmapsmarker_filter_plugin_meta', 10, 2 );
  } //info: end plugin_meta_links()
}  //info: end class
$run_leafletmapsmarker = new Leafletmapsmarker();
//info: include widget class
require_once( plugin_dir_path( __FILE__ ) . 'inc' . DIRECTORY_SEPARATOR . 'class-leaflet-recent-marker-widget.php' );
unset($run_leafletmapsmarker);
?>