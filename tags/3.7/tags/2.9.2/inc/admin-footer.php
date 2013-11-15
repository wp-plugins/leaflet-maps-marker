<?php
/*
    Admin footer - Leaflet Maps Marker Plugin
*/
?>

<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'admin-footer.php') { die ("Please do not access this file directly. Thanks!<br/><a href='http://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); } 
?>

<table cellpadding="5" cellspacing="0" style="margin-top:20px;border:1px solid #ccc;width:100%;background:#F9F9F9;">
  <tr>
    <td valign="center"><div style="float:left;"><a href="http://www.harm.co.at/en" target="_blank" title="www.harm.co.at/en"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/donate-robertharm.jpg" width="128" height="128" alt="Robert Harm" style="padding:10px 10px 0 10px;" /></a></div>
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