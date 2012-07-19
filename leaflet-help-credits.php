<?php
/*
    Help and credits page - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-help-credits.php') { die ("Please do not access this file directly. Thanks!<br/><a href='http://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
?>
<div class="wrap">
	<?php $lmm_options = get_option( 'leafletmapsmarker_options' ); ?>
	<?php include('leaflet-admin-header.php'); ?>
	<p>
	<h3><?php _e('Help','lmm') ?></h3>
	<p>
		<?php _e('Do you have questions or issues with Leaflet Maps Marker? Please use the following support channels appropriately.','lmm') ?>
	</p>
	<ul>
		<li>- <a href="http://www.mapsmarker.com/faq/" target="_blank"><?php _e('FAQ','lmm') ?></a>	<?php _e('(frequently asked questions)','lmm') ?></li>
		<li>- <a href="http://www.mapsmarker.com/docs/" target="_blank"><?php _e('Documentation','lmm') ?></a></li>
		<li>- <a href="http://www.mapsmarker.com/docs/changelog/" target="_blank"><?php _e('Changelog','lmm') ?></a></li>
		<li>- <a href="http://www.mapsmarker.com/ideas/" target="_blank"><?php _e('Ideas','lmm') ?></a> <?php _e('(feature requests)','lmm') ?></li>
		<li>- <a href="http://wordpress.org/support/plugin/leaflet-maps-marker" target="_blank">WordPress Support Forum</a> <?php _e('(free community support)','lmm') ?></li>
		<li>- <a href="http://wpquestions.com/affiliates/register/name/robertharm" target="_blank">WP Questions</a>	<?php _e('(paid community support)','lmm') ?></li>
		<li>- <a href="http://wphelpcenter.com/" target="_blank">WordPress HelpCenter</a> <?php _e('(paid professional support)','lmm') ?></li>
	</ul>
	<p>
		<?php _e('More information on support','lmm') ?>: <a href="http://www.mapsmarker.com/support/" target="_blank">http://www.mapsmarker.com/support</a></p>
	<h3><?php _e('Licence','lmm') ?></h3>
	<p>
		<?php _e('Good news, this plugin is free for everyone! Since it is released under the GPL2, you can use it free of charge on your personal or commercial blog.<br/>But if you enjoy this plugin, you can thank me and leave a small donation for the time I have spent writing and supporting this plugin.<br/>Please see <a href="http://www.mapsmarker.com/donations" target="_blank">http://www.mapsmarker.com/donations</a> for details.','lmm') ?>
	</p>
	<h3><?php _e('Licenses for used libraries, services and images','lmm') ?></h3>
	<ul>
		<li>- Leaflet by Cloudmade, <a href="http://leaflet.cloudmade.com" target="_blank">http://leaflet.cloudmade.com</a>, Copyright (c) 2010-2012, CloudMade, Vladimir Agafonkin</li>
		<li>- OpenStreetMap: <a href="http://wiki.openstreetmap.org/wiki/OpenStreetMap_License" target="_blank">OpenStreetMap License</a></li>
		<li>- Datasource OGD Vienna maps: Stadt Wien - <a href="http://data.wien.gv.at" target="_blank">http://data.wien.gv.at</a></li>
		<li>- Address autocompletion powered by <a href="http://code.google.com/intl/de-AT/apis/maps/documentation/places/autocomplete.html" target="_blank">Google Places API</a></li>
		<li>- Jquery TimePicker by Trent Richardson, <a href="http://trentrichardson.com/examples/timepicker/" target="_blank">http://trentrichardson.com/examples/timepicker/</a>, licence: GPL</li>
		<li>- <a href="http://mapicons.nicolasmollet.com" target="_blank">Map Icons Collection</a> by Nicolas Mollet</li>
		<li>- Map center icon by <a href="http://glyphish.com/" target="_blank">Joseph Wain</a>, licence: Creative Commons Attribution (by)</li>
		<li>- Plus, json, layer &amp; csv-export icon by <a href="http://www.pinvoke.com/" target="_blank">Yusuke Kamiyamane</a>, licence: Creative Commons Attribution (by)</li>
		<li>- Question Mark Icon by <a href="http://www.randomjabber.com/" target="_blank">RandomJabber</a></li>
		<li>- Images for changelog from <a href="http://www.mozilla.org/en-US/firefox/11.0/releasenotes/">Firefox release notes</a>, licence: Creative Commons Attribution ShareAlike (CC BY-SA 3.0)</li> 
		<li>- Plus-, json-, language- &amp; csv-export-icon from <a href="http://p.yusukekamiyamane.com/" target="_blank">Yusuke Kamiyamane</a>, licence: Creative Commons Attribution (by)</li>
	</ul>
	<h3><?php _e('Translations','lmm') ?></h3>
	<p>
	<?php 
	$translation_website = '<a href="http://translate.mapsmarker.com/projects/lmm" target="_blank">http://translate.mapsmarker.com/projects/lmm</a>';
	$translation_output = sprintf(__('Adding a new translation or updating an existing one is quite easy - please visit %s for more information!','lmm'),$translation_website); 
	echo $translation_output;
	?>	
	</p>
	<ul>
		<li>- Bulgarian (bg_BG) thanks to Andon Ivanov, <a href="http://coffebreak.info" target="_blank">http://coffebreak.info</a></li>
		<li>- Catalan (ca) by Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a></li>
		<li>- Chinese (zn_CH) by John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a></li>
		<li>- Dutch (nl_NL) by Marijke Metz, <a href="http://www.mergenmetz.nl" target="_blank">http://www.mergenmetz.nl</a></li>
		<li>- English (en_US) by <a href="http://twitter.com/robertharm" target="_blank">@RobertHarm</a></li>
		<li>- French (fr_FR) by Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a></li>
		<li>- German (de_DE) by <a href="http://twitter.com/robertharm" target="_blank">@RobertHarm</a></li>
		<li>- Hindi (hi_IN) by Outshine Solutions, <a href="http://outshinesolutions.com" target="_blank">http://outshinesolutions.com</a> and Guntupalli Karunakar, <a href="http://indlinux.org" target="_blank">http://indlinux.org</a></li>
		<li>- Italian (it_IT) by <a href="mailto:lucabarbetti@gmail.com">Luca Barbetti</a></li>
		<li>- Japanese (ja) by Shu Higashi, <a href="http://twitter.com/higa4" target="_blank">@higa4</a></li>
		<li>- Polish translation thanks to Pawel Wyszy&#324;ski, <a href="http://injit.pl" target="_blank">http://injit.pl</a></li>
		<li>- Russian (ru_RU) thanks to Ekaterina Golubina, , supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a></li>
		<li>- Spanish (es_ES) by David Ramírez, <a href="http://www.hiperterminal.com" target="_blank">http://www.hiperterminal.com</a> &amp; Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a></li>
		<li>- Turkish (tr_TR) thanks to Emre Erkan, <a href="http://www.karalamalar.net" target="_blank">http://www.karalamalar.net</a></li>
		<li>- Yiddish (yi) thanks to Raphael Finkel, <a href="http://www.cs.uky.edu/~raphael/yiddish.html" target="_blank">http://www.cs.uky.edu/~raphael/yiddish.html</a></li>
	</ul>
	<h3><?php _e('Credits & special thanks','lmm') ?></h3>
	<ul>
		<li>- Sindre Wimberger (<a href="http://www.sindre.at" target="_blank">http://www.sindre.at</a>) - bugfixing &amp; geo-consulting</li>
		<li>- Susanne Mandl (<a href="http://www.greenflamingomedia.com" target="_blank">http://www.greenflamingomedia.com</a>) - plugin logo</li>
		<li>- <a href="http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/" target="_blank">WordPress-Settings-API-Class</a> by Aliso the geek</li>
		<li>- <a href="http://www.nanodesu.ru" target="_blank">Hind</a> who originally release a basic Leaflet plugin (not available anymore) which I used partly as a basis for Leaflet Maps Marker plugin</li>
		<li>- <a href="http://psha.org.ru/b/leaflet-plugins.html" target="_blank">shramov</a> for bing and google maps plugins for leaflet</li>
	</ul>
	</p>
</div>