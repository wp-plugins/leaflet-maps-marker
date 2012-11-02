<?php
/*
    Admin Header - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'admin-header.php') { die ("Please do not access this file directly. Thanks!<br/><a href='http://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . "pluggable.php");
$lmm_options = get_option( 'leafletmapsmarker_options' ); //info: required for bing maps api key check
$admin_quicklink_settings_buttons = ( current_user_can( "activate_plugins" ) ) ? "<a class='button-secondary' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_settings'>".__('Settings','lmm')."</a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" : "";
$admin_quicklink_tools_buttons = ( current_user_can( "activate_plugins" ) ) ? "<a class='button-secondary' href='" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_tools'>".__('Tools','lmm')."</a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" : "";
//info: display info upon first activation
$install_note = (isset($_GET['display']) ? $_GET['display'] : '');
if ( $install_note != NULL ) {
	$install_success_message = sprintf( __('You just successfully installed the "Leaflet Maps Marker" plugin. You can now add your first marker below or optionally <a href="%1$sadmin.php?page=leafletmapsmarker_settings">change the default settings</a>.<br/>For tutorials and help, please check the <a href="%1$sadmin.php?page=leafletmapsmarker_help">Help &amp; Credits page</a>!','lmm'), LEAFLET_WP_ADMIN_URL); 
	echo '<div class="updated" style="padding:10px;"><p>' . $install_success_message . '</p></div>';
	//info: check if custom icons could be unzipped
	if ( ! file_exists(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . 'information.png') ) {
		echo '<div class="error" style="padding:10px;">'.__('Warning: the custom map icon directory at <code>/wp-contents/uploads/leaflet-maps-marker-icons</code> could not be created due to file permission settings on your webserver. Leaflet Maps Marker will work as designed, but only with one map icon available.<br/>You can add the included map icons manually by following the steps at <a href="http://www.mapsmarker.com/incomplete-installation" target="_blank">http://www.mapsmarker.com/incomplete-installation</a>', 'lmm').'</div>';
	}
	update_option('leafletmapsmarker_update_info', 'hide');
} ?>
<div style="float:right;">
  <div style="text-align:center;"><small><a href="http://www.mapsmarker.com" target="_blank" style="text-decoration:none;">MapsMarker.com</a> supports</small></div>
  <a href="http://www.open3.at" target="_blank" title="open3.at - network for the promotion of Open Society, OpenGov and OpenData in Austria"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/logo-open3-small.png" width="143" height="30" border="0"/></a></div>
  <div style="font-size:1.5em;margin-bottom:5px;padding:10px 0 0 0;"><span style="font-weight:bold;">Maps Marker<sup style="font-size:75%;">&reg;</sup> v<?php echo get_option("leafletmapsmarker_version") ?> - <?php _e('Free Edition','lmm'); ?></span></div>
  <p style="margin:1.5em 0;">
  <a class="button-secondary" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_markers"><?php _e("List all markers", "lmm") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <a class="button-secondary" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_marker"><?php _e("Add new marker", "lmm") ?></a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
  <a class="button-secondary" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layers"><?php _e("List all layers", "lmm") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <a class="button-secondary" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layer"><?php _e("Add new layer", "lmm") ?></a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
  <?php echo $admin_quicklink_tools_buttons ?>
  <?php echo $admin_quicklink_settings_buttons ?>
  <a class="button-secondary" href="<?php echo LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_help"><?php _e("Help & Credits", "lmm") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  </p>
<?php
//info: display update info with current release notes
$update_info_action = isset($_POST['update_info_action']) ? $_POST['update_info_action'] : ''; 
//info: dont display on new installs
$new_install = (isset($_GET['display']) ? 'true' : 'false'); 
if ( ($update_info_action == 'hide') && ($new_install == 'false') ) {
	update_option('leafletmapsmarker_update_info', 'hide');
}
if (get_option('leafletmapsmarker_update_info') == 'show') {
	$lmm_version_old = get_option( 'leafletmapsmarker_version_before_update' );
	$lmm_version_new = get_option( 'leafletmapsmarker_version' );
	$lmm_changelog_new_version = '<a href="http://www.mapsmarker.com/v' . $lmm_version_new . '" target="_blank">http://www.mapsmarker.com/v' . $lmm_version_new . '</a>';
	$lmm_full_changelog = '<a href="http://www.mapsmarker.com/changelog" target="_blank">http://www.mapsmarker.com/changelog</a>';
	echo '<div class="updated">
		<p><span style="font-weight:bold;font-size:125%;">' . sprintf(__('Leaflet Maps Marker has been successfully updated from version %1s to %2s!','lmm'), $lmm_version_old, $lmm_version_new) . '</span></p>
		<p>' . sprintf(__('For more details about version %1s, please visit %2s','lmm'), $lmm_version_new, $lmm_changelog_new_version) . '</p>'.PHP_EOL;
	echo '<iframe name="changelog" src="' . LEAFLET_PLUGIN_URL . 'inc/changelog.php" width="98%" height="285" marginwidth="0" marginheight="0" style="border:thin dashed #E6DB55;"></iframe>'.PHP_EOL;
	echo '<p>' . __('If you like using the plugin, please consider <a href="http://www.mapsmarker.com/donations" target="_blank">making a donation</a> and <a href="http://wordpress.org/support/view/plugin-reviews/leaflet-maps-marker" target="_blank">rate the plugin on wordpress.org</a> - thanks!','lmm') . '</p>'.PHP_EOL;
	echo '<form method="post" style="padding:2px 0 6px 0;">
		<input type="hidden" name="update_info_action" value="hide" />
		<input class="button-secondary" type="submit" value="' . __('remove message', 'lmm') . '"/></form></div>';
}
?>
<table cellpadding="5" cellspacing="0" style="border:1px solid #ccc;width:98%;background:#efefef;">
  <tr>
    <td valign="center"><div style="float:left;"><a href="http://www.mapsmarker.com" target="_blank" title="www.MapsMarker.com"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/logo-mapsmarker.png" width="128" height="128" alt="Leaflet Maps Marker Plugin Logo by Julia Loew, www.weiderand.net" style="padding:10px 10px 0 10px;" /></a></div>
<div style="float:right;"> 
        <!--Begin support table-->
        <table cellspacing="5">
          <tr>
           <td style="background:#fff;text-align:center;">
<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ZKVA3VKMEU2TA">
<?php if ( (defined('WPLANG')) && (strtoupper(substr(WPLANG, 0, 2)) == 'EN') ) { 
		echo '<input type="hidden" name="LC" value="EN">';
	} else if ( (defined('WPLANG')) && (strtoupper(substr(WPLANG, 0, 2)) == 'FR') ) {
		echo '<input type="hidden" name="LC" value="FR">';
	} else if ( (defined('WPLANG')) && (strtoupper(substr(WPLANG, 0, 2)) == 'CN') ) {
		echo '<input type="hidden" name="LC" value="CN">';
	} else if ( (defined('WPLANG')) && (strtoupper(substr(WPLANG, 0, 2)) == 'DE') ) {
		echo '<input type="hidden" name="LC" value="DE">';
	} else if ( (defined('WPLANG')) && (strtoupper(substr(WPLANG, 0, 2)) == 'IT') ) {
		echo '<input type="hidden" name="LC" value="IT">';
	} else if ( (defined('WPLANG')) && (strtoupper(substr(WPLANG, 0, 2)) == 'JP') ) {
		echo '<input type="hidden" name="LC" value="JP">';
	} else if ( (defined('WPLANG')) && (strtoupper(substr(WPLANG, 0, 2)) == 'ES') ) {
		echo '<input type="hidden" name="LC" value="ES">';
	} else { 
		echo '<input type="hidden" name="LC" value="EN">';
	} ?>
<table>
<tr><td><input type="hidden" name="on0" value="Sponsorship Level">
	<select name="os0" style="width:210px;">
	<option value="Supporter 2">Please select sponsorship level</option>
	<option value="Contributor">Contributor €1,00 EUR</option>
	<option value="Contributor 2">Contributor €5,00 EUR</option>
	<option value="Supporter">Supporter €10,00 EUR</option>
	<option value="Supporter 2">Supporter €25,00 EUR</option>
	<option value="Donor">Donor €50,00 EUR</option>
	<option value="Sponsor">Sponsor €100,00 EUR</option>
	<option value="Benefactor">Benefactor €250,00 EUR</option>
	<option value="Patron">Patron €500,00 EUR</option>
	<option value="Open Source Angel">Open Source Angel €1.000,00 EUR</option>
	<option value="Corporate Angel">Corporate Angel €2.500,00 EUR</option>
</select> </td></tr>
<tr><td colspan="2"><input type="hidden" name="on1" value="Message"><?php _e('Message','lmm') ?> <input type="text" name="os1" maxlength="200"></td></tr>
</table>
<input type="hidden" name="currency_code" value="EUR">
<input type="image" src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/donate-paypal.jpg" width="130" height="89" border="0" name="submit" alt="" title="<?php esc_attr_e('If you like to donate a certain amount of money to show your support, you can also use Paypal. If you don´t have a Paypal account, you can use your credit card or bank account (where available). Please click on the paypal image to proceed to the donation form.','lmm') ?>">
</form>
            </td>
            <td style="background:#fff;width:115px;text-align:center;"><a href="http://www.amazon.com/exec/obidos/redirect-home?tag=leafletmapsmarker-21&site=home" target="_blank" title="<?php esc_attr_e('The easiest way to support this plugin is to buy something from Amazon by using this referrer link. Note: this doesn´t cost you anything as your purchase volume won´t be increased, but I will receive 6 per cent of your purchase volume as a referral fee.','lmm') ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/donate-amazon-partnernet.jpg" style="border:1px solid #ccc;padding:4px;" width="84" height="40" border="0"/></a>
		<br/><a href="http://www.amazon.de/registry/wishlist/3P6LQRP11V1AF" target="_blank" title="<?php esc_attr_e('Another way to show your support for this plugin is to buy something from my Amazon wishlist, respectively sending me a greeting card worth from 15 to 500 $ with a personal note, which I would very much appreciate.','lmm') ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/donate-amazon.jpg" width="100" height="50" border="0"/></a><br/><a href="http://flattr.com/thing/447395/MapsMarker-com" target="_blank">
<img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/donate-flattr.png" alt="Flattr this" title="Flattr this" border="0" /></a></td>
          </tr>
        </table>
        <!--End support table-->
      </div>
	<p><strong><?php _e('A message from the plugin´s author','lmm') ?> <a href="http://www.harm.co.at" target="_blank" title="<?php esc_attr_e('Show website of plugin author','lmm') ?>" style="text-decoration:none;">Robert Harm</a>:</strong><br/>
			<?php _e('It is hard to continue development and support for Leaflet Maps Marker-plugin without contributions from users like you.','lmm') ?> <?php _e('If you enjoy using the plugin - <strong>particularly within a commercial context</strong> - please consider making a donation.','lmm') ?> <?php _e('Your donation help keeping the plugin free for everyone and allow me to spend more time on developing, maintaining and support.','lmm') ?> <?php _e('I´d be happy to accept your donation! Thanks!','lmm') ?> <?php _e('For more information on how to donate, please visit','lmm') ?>  
			<a style="text-decoration:none;" href="http://mapsmarker.com/donations" target="_blank">http://mapsmarker.com/donations</a>
			<br/><br/>
			<a style="text-decoration:none;" href="http://www.mapsmarker.com" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-website-home.png" width="16" height="16" alt="mapsmarker.com"> MapsMarker.com</a>&nbsp;&nbsp;&nbsp;
			<a style="text-decoration:none;" href="http://www.mapsmarker.com/donations" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-donations.png" width="16" height="16" alt="donations"> <?php echo __('donations','lmm'); ?></a>&nbsp;&nbsp;&nbsp;
			<a style="text-decoration:none;" href="http://wordpress.org/support/view/plugin-reviews/leaflet-maps-marker" target="_blank" title="<?php esc_attr_e('please review this plugin on wordpress.org','lmm') ?>"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-star.png" width="16" height="16" alt="ratings"> <?php _e('Rate plugin','lmm') ?></a>&nbsp;&nbsp;&nbsp;
			<a style="text-decoration:none;" href="http://translate.mapsmarker.com/projects/lmm" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-translations.png" width="16" height="16" alt="translations"> <?php echo __('translations','lmm'); ?></a>&nbsp;&nbsp;&nbsp;
			<a style="text-decoration:none;" href="https://github.com/robertharm/Leaflet-Maps-Marker" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-github.png" width="16" height="16" alt="github"> github</a>&nbsp;&nbsp;&nbsp;
			<a style="text-decoration:none;" href="http://twitter.com/mapsmarker" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-twitter.png" width="16" height="16" alt="twitter"> Twitter</a>&nbsp;&nbsp;&nbsp;
			<a style="text-decoration:none;" href="http://facebook.com/mapsmarker" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-facebook.png" width="16" height="16" alt="facebook"> Facebook</a>&nbsp;&nbsp;&nbsp;
			<a style="text-decoration:none;" href="http://www.mapsmarker.com/changelog" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-changelog-header.png" width="16" height="16" alt="changelog"> <?php _e('Changelog','lmm') ?></a>&nbsp;&nbsp;&nbsp;
			<a style="text-decoration:none;" href="http://feeds.feedburner.com/MapsMarker" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-rss.png" width="16" height="16" alt="rss"> RSS</a>&nbsp;&nbsp;&nbsp;
			<a style="text-decoration:none;" href="http://feedburner.google.com/fb/a/mailverify?uri=MapsMarker" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-rss-email.png" width="16" height="16" alt="rss-email"> <?php echo __('RSS via E-Mail','lmm'); ?></a>&nbsp;&nbsp;&nbsp;
			</p></td>
  </tr>
</table>