<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'install-and-updates.php') { die ("Please do not access this file directly. Thanks!<br/><a href='http://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
global $wpdb;
//info: options not managed by Settings API
add_option('leafletmapsmarker_version', 'init');
add_option('leafletmapsmarker_version_before_update', '0');
add_option('leafletmapsmarker_redirect', 'true'); //redirect to marker creation page page after first activation only
if (get_option('leafletmapsmarker_version') == 'init') {
	//info: copy map icons to wp-content/uploads
	WP_Filesystem();
	$target = LEAFLET_PLUGIN_ICONS_DIR;
	if (!is_dir($target)) //info: check for multisite installations not to extract files again if already installed on 1 site
	{
		wp_mkdir_p( $target );
		$source = LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'mapicons';
		copy_dir($source, $target, $skip_list = array() );
		$zipfile = LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . 'mapicons.zip';
		unzip_file( $zipfile, $target );
	}
	//info: create tables for markers & layers
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$sql_create_marker_table = "CREATE TABLE IF NOT EXISTS `" . $table_name_markers . "` (
		`id` int(6) unsigned NOT NULL auto_increment,
		`markername` varchar(255) CHARACTER SET utf8 NOT NULL,
		`basemap` varchar(20) NOT NULL,
		`layer` int(6) unsigned NOT NULL,
		`lat` decimal(9,6) NOT NULL,
		`lon` decimal(9,6) NOT NULL,
		`icon` varchar(255) CHARACTER SET utf8 NOT NULL,
		`popuptext` text CHARACTER SET utf8 NOT NULL,
		`zoom` int(2) NOT NULL,
		`openpopup` tinyint(1) NOT NULL,
		`mapwidth` int(4) NOT NULL,
		`mapwidthunit` varchar(2) NOT NULL,
		`mapheight` int(4) NOT NULL,
		`panel` tinyint(1) NOT NULL,
		`createdby` varchar(30) CHARACTER SET utf8 NOT NULL,
		`createdon` datetime NOT NULL,
		`updatedby` varchar(30) CHARACTER SET utf8 NOT NULL,
		`updatedon` datetime NOT NULL,
		`controlbox` int(1) NOT NULL,
		`overlays_custom` int(1) NOT NULL,
		`overlays_custom2` int(1) NOT NULL,
		`overlays_custom3` int(1) NOT NULL,
		`overlays_custom4` int(1) NOT NULL,
		`wms` int(1) NOT NULL,
		`wms2` int(1) NOT NULL,
		`wms3` int(1) NOT NULL,
		`wms4` int(1) NOT NULL,
		`wms5` int(1) NOT NULL,
		`wms6` int(1) NOT NULL,
		`wms7` int(1) NOT NULL,
		`wms8` int(1) NOT NULL,
		`wms9` int(1) NOT NULL,
		`wms10` int(1) NOT NULL,
		PRIMARY KEY  (`id`)
	)  ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
	$wpdb->query($sql_create_marker_table);
	$sql_create_layer_table = "CREATE TABLE IF NOT EXISTS `".$table_name_layers."` (
		`id` int(6) unsigned NOT NULL auto_increment,
		`name` varchar(255) CHARACTER SET utf8 NOT NULL,
		`basemap` varchar(20) NOT NULL,
		`layerzoom` int(2) NOT NULL,
		`mapwidth` int(4) NOT NULL,
		`mapwidthunit` varchar(2) NOT NULL,
		`mapheight` int(4) NOT NULL,
		`panel` tinyint(1) NOT NULL,
		`layerviewlat` decimal(9,6) NOT NULL,
		`layerviewlon` decimal(9,6) NOT NULL,
		`createdby` varchar(30) CHARACTER SET utf8 NOT NULL,
		`createdon` datetime NOT NULL,
		`updatedby` varchar(30) CHARACTER SET utf8 NOT NULL,
		`updatedon` datetime NOT NULL,
		`controlbox` int(1) NOT NULL,
		`overlays_custom` int(1) NOT NULL,
		`overlays_custom2` int(1) NOT NULL,
		`overlays_custom3` int(1) NOT NULL,
		`overlays_custom4` int(1) NOT NULL,
		`wms` int(1) NOT NULL,
		`wms2` int(1) NOT NULL,
		`wms3` int(1) NOT NULL,
		`wms4` int(1) NOT NULL,
		`wms5` int(1) NOT NULL,
		`wms6` int(1) NOT NULL,
		`wms7` int(1) NOT NULL,
		`wms8` int(1) NOT NULL,
		`wms9` int(1) NOT NULL,
		`wms10` int(1) NOT NULL,
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
	$wpdb->query($sql_create_layer_table);
	//info: insert layer row 0 for markers without assigned layer
	$sql = "SET SESSION sql_mode=NO_AUTO_VALUE_ON_ZERO;";
	$wpdb->query($sql);
	$sql2 = "INSERT INTO `".$table_name_layers."` ( `id`, `name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `layerviewlat`, `layerviewlon` ) VALUES (0, 'markers not assigned to a layer', 'osm_mapnik', '11', '640', 'px', '480', '', '');";
	$wpdb->query($sql2);
	$sql3 = "SET SESSION sql_mode='';";
	$wpdb->query($sql3);
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '0');
	}	
	update_option('leafletmapsmarker_version', '1.0');
} 
if (get_option('leafletmapsmarker_version') == '1.0' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.0');
	}
	update_option('leafletmapsmarker_version', '1.1');
}
if (get_option('leafletmapsmarker_version') == '1.1' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.1');
	}
	update_option('leafletmapsmarker_version', '1.2');
}
if (get_option('leafletmapsmarker_version') == '1.2' ) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.2');
	}
	update_option('leafletmapsmarker_version', '1.2.1');
}
if (get_option('leafletmapsmarker_version') == '1.2.1' ) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.2.1');
	}
	update_option('leafletmapsmarker_version', '1.2.2');
}
if (get_option('leafletmapsmarker_version') == '1.2.2' ) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.2.2');
	}
	update_option('leafletmapsmarker_version', '1.3');
}
if (get_option('leafletmapsmarker_version') == '1.3' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.3');
	}
	update_option('leafletmapsmarker_version', '1.4');
}
if (get_option('leafletmapsmarker_version') == '1.4' ) {
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$update141_1 = "ALTER TABLE `" . $table_name_markers . "` CHANGE `updatedby` `updatedby` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
	$wpdb->query($update141_1);
	$update141_2 = "ALTER TABLE `" . $table_name_markers . "` CHANGE `updatedon` `updatedon` DATETIME NULL;";
	$wpdb->query($update141_2);
	$update141_3 = "ALTER TABLE `" . $table_name_layers . "` CHANGE `updatedby` `updatedby` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
	$wpdb->query($update141_3);
	$update141_4 = "ALTER TABLE `" . $table_name_layers . "` CHANGE `updatedon` `updatedon` DATETIME NULL;";
	$wpdb->query($update141_4);
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.4');
	}
	update_option('leafletmapsmarker_version', '1.4.1');
}
if (get_option('leafletmapsmarker_version') == '1.4.1' ) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.4.1');
	}
	update_option('leafletmapsmarker_version', '1.4.2');
}
if (get_option('leafletmapsmarker_version') == '1.4.2' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.4.2');
	}
	update_option('leafletmapsmarker_version', '1.4.3');
}
if (get_option('leafletmapsmarker_version') == '1.4.3' ) {
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$update15_1 = "ALTER TABLE `" . $table_name_markers . "` CHANGE `wms` `wms` TINYINT( 1 ) NOT NULL, CHANGE `wms2` `wms2` TINYINT( 1 ) NOT NULL, CHANGE `wms3` `wms3` TINYINT( 1 ) NOT NULL, CHANGE `wms4` `wms4` TINYINT( 1 ) NOT NULL, CHANGE `wms5` `wms5` TINYINT( 1 ) NOT NULL, CHANGE `wms6` `wms6` TINYINT( 1 ) NOT NULL, CHANGE `wms7` `wms7` TINYINT( 1 ) NOT NULL, CHANGE `wms8` `wms8` TINYINT( 1 ) NOT NULL, CHANGE `wms9` `wms9` TINYINT( 1 ) NOT NULL, CHANGE `wms10` `wms10` TINYINT( 1 ) NOT NULL;";
	$wpdb->query($update15_1);
	$update15_2 = "ALTER TABLE `" . $table_name_layers . "` CHANGE `panel` `panel` TINYINT( 1 ) NOT NULL, CHANGE `wms` `wms` TINYINT( 1 ) NOT NULL, CHANGE `wms2` `wms2` TINYINT( 1 ) NOT NULL, CHANGE `wms3` `wms3` TINYINT( 1 ) NOT NULL, CHANGE `wms4` `wms4` TINYINT( 1 ) NOT NULL, CHANGE `wms5` `wms5` TINYINT( 1 ) NOT NULL, CHANGE `wms6` `wms6` TINYINT( 1 ) NOT NULL, CHANGE `wms7` `wms7` TINYINT( 1 ) NOT NULL, CHANGE `wms8` `wms8` TINYINT( 1 ) NOT NULL, CHANGE `wms9` `wms9` TINYINT( 1 ) NOT NULL, CHANGE `wms10` `wms10` TINYINT( 1 ) NOT NULL;";
	$wpdb->query($update15_2);
	$update15_3 = "ALTER TABLE `" . $table_name_layers . "` ADD `listmarkers` TINYINT(1) NOT NULL AFTER `wms10`;";
	$wpdb->query($update15_3);
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.4.3');
	}
	update_option('leafletmapsmarker_version', '1.5');
}
if (get_option('leafletmapsmarker_version') == '1.5' ) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.5');
	}
	update_option('leafletmapsmarker_version', '1.5.1');
}
if (get_option('leafletmapsmarker_version') == '1.5.1' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.5.1');
	}
	update_option('leafletmapsmarker_version', '1.6');
}
if (get_option('leafletmapsmarker_version') == '1.6' ) {
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$update17_1 = "ALTER TABLE `" . $table_name_layers . "` ADD `multi_layer_map` TINYINT(1) NOT NULL AFTER `listmarkers`, ADD `multi_layer_map_list` VARCHAR(255) CHARACTER SET utf8 NULL AFTER `multi_layer_map`;";
	$wpdb->query($update17_1);
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.6');
	}
	update_option('leafletmapsmarker_version', '1.7');
}
if (get_option('leafletmapsmarker_version') == '1.7' ) {
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$update18_1 = "ALTER TABLE `" . $table_name_markers . "` ADD `kml_timestamp` DATETIME NULL AFTER `wms10`;";
	$wpdb->query($update18_1);
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.7');
	}
	update_option('leafletmapsmarker_version', '1.8');
}
if (get_option('leafletmapsmarker_version') == '1.8' ) {
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$update19_1 = "UPDATE `" . $table_name_markers . "` SET basemap = 'osm_mapnik' WHERE basemap = 'osm_osmarender';";
	$wpdb->query($update19_1);
	$update19_2 = "UPDATE `" . $table_name_layers . "` SET basemap = 'osm_mapnik' WHERE basemap = 'osm_osmarender';";
	$wpdb->query($update19_2);
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.8');
	}
	update_option('leafletmapsmarker_version', '1.9');
}
if (get_option('leafletmapsmarker_version') == '1.9' ) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '1.9');
	}
	update_option('leafletmapsmarker_version', '2.0');
}
if (get_option('leafletmapsmarker_version') == '2.0' ) {
	add_option('leafletmapsmarker_update_info', 'show');
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.0');
	}
	update_option('leafletmapsmarker_version', '2.1');
}
if (get_option('leafletmapsmarker_version') == '2.1' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.1');
	}
	update_option('leafletmapsmarker_version', '2.2');
}
if (get_option('leafletmapsmarker_version') == '2.2' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.2');
	}
	update_option('leafletmapsmarker_version', '2.3');
}
if (get_option('leafletmapsmarker_version') == '2.3' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.3');
	}
	update_option('leafletmapsmarker_version', '2.4');
}
if (get_option('leafletmapsmarker_version') == '2.4' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.4');
	}
	update_option('leafletmapsmarker_version', '2.5');
}
if (get_option('leafletmapsmarker_version') == '2.5' ) {
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$update26_1 = "ALTER TABLE `" . $table_name_markers . "` CHANGE `basemap` `basemap` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
	$wpdb->query($update26_1);
	$update26_2 = "ALTER TABLE `" . $table_name_layers . "` CHANGE `basemap` `basemap` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
	$wpdb->query($update26_2);
	$update26_3 = "UPDATE `" . $table_name_markers . "` SET basemap = 'googleLayer_satellite' WHERE basemap = 'googleLayer_satellit';";
	$wpdb->query($update26_3);
	$update26_4 = "UPDATE `" . $table_name_layers . "` SET basemap = 'googleLayer_satellite' WHERE basemap = 'googleLayer_satellit';";
	$wpdb->query($update26_4);
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.5');
	}
	update_option('leafletmapsmarker_version', '2.6');
}
if (get_option('leafletmapsmarker_version') == '2.6' ) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.6');
	}
	update_option('leafletmapsmarker_version', '2.6.1');
}
if (get_option('leafletmapsmarker_version') == '2.6.1' ) {
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.6.1');
	}
	update_option('leafletmapsmarker_version', '2.7');
}
if (get_option('leafletmapsmarker_version') == '2.7' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.7');
	}
	update_option('leafletmapsmarker_version', '2.7.1');
}
if (get_option('leafletmapsmarker_version') == '2.7.1' ) {
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.7.1');
	}
	update_option('leafletmapsmarker_version', '2.8');
	update_option('leafletmapsmarker_update_info', 'show');
	//info: redirect to settings page only on first plugin activation, otherwise redirect is also done on bulk plugin activations
	if (get_option('leafletmapsmarker_redirect') == 'true') 
	{
		update_option('leafletmapsmarker_redirect', 'false');
		wp_redirect(LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&display=install_note');
	} else {
		update_option('leafletmapsmarker_update_info', 'show');
	}
}
/* template for plugin updates 
if (get_option('leafletmapsmarker_version') == '2.8' ) {
	//optional: add code for sql ddl updates
	//mandatory if new options in class-leaflet-options.php were added
	$save_defaults_for_new_options = new Class_leaflet_options();
	$save_defaults_for_new_options->save_defaults_for_new_options();
	$version_before_update = get_transient( 'leafletmapsmarker_version_before_update' );
	if ( $version_before_update === FALSE ) {
		set_transient( 'leafletmapsmarker_version_before_update', 'deleted-in-1-hour', 60*60 );
		update_option('leafletmapsmarker_version_before_update', '2.8');
	}
	update_option('leafletmapsmarker_version', '2.9');
	//mandatory: remove update_option('leafletmapsmarker_update_info', 'show'); from last version
	update_option('leafletmapsmarker_update_info', 'show');
	//mandatory: move code for redirect-on-first-activation-check to here
}
*/
?>