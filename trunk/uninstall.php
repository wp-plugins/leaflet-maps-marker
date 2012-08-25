<?php
/*die if uninstall not called from Wordpress exit*/
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();
/* Remove settings */
if (is_multisite()) 
{
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
	$lmm_pro_readme = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'leaflet-maps-marker-pro' . DIRECTORY_SEPARATOR . 'readme.txt';
		if (!file_exists($lmm_pro_readme))
			{
		/*remove map icons directory for main site */
		$lmm_upload_dir = wp_upload_dir();
		$icons_directory = $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons" . DIRECTORY_SEPARATOR;
		if (is_dir($icons_directory)) 
		{
		foreach(glob($icons_directory.'*.*') as $v){
		unlink($v);
		}
		rmdir($icons_directory);
		}
	}
	if ($blogs) 
		{
		foreach($blogs as $blog) 
			{
			switch_to_blog($blog['blog_id']);
			delete_option('leafletmapsmarker_version');
			delete_option('leafletmapsmarker_options');
			delete_option('leafletmapsmarker_redirect');
			delete_option('leafletmapsmarker_update_info');
			if (!file_exists($lmm_pro_readme))
				{
				/* Remove and clean tables */
				$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_layers`");
				$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_markers`");
				$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`");
				}
			}
		restore_current_blog();
		if (!file_exists($lmm_pro_readme))
			{
				/*remove map icons directory for subsites*/
				$lmm_upload_dir = wp_upload_dir();
				$icons_directory = $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons" . DIRECTORY_SEPARATOR;
				if (is_dir($icons_directory)) 
				{
				foreach(glob($icons_directory.'*.*') as $v){
				unlink($v);
				}
				rmdir($icons_directory);
				}
			}
		}
} 
else
{
	delete_option('leafletmapsmarker_version');
	delete_option('leafletmapsmarker_options');
	delete_option('leafletmapsmarker_redirect');
	delete_option('leafletmapsmarker_update_info');
	$lmm_pro_readme = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'leaflet-maps-marker-pro' . DIRECTORY_SEPARATOR . 'readme.txt';
	if (!file_exists($lmm_pro_readme))
	{
		/* Remove and clean tables */
		$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_layers`");
		$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_markers`");
		$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`");
		/*remove map icons directory*/
		$lmm_upload_dir = wp_upload_dir();
		$icons_directory = $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons" . DIRECTORY_SEPARATOR;
		if (is_dir($icons_directory)) 
		{
		foreach(glob($icons_directory.'*.*') as $v){
		unlink($v);
		}
		rmdir($icons_directory);
		}			
	}
}
?>