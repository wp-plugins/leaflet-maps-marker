<?php 
header('Content-Type: text/javascript; charset=UTF-8');
$adminurl = isset($_GET['adminurl']) ? $_GET['adminurl'] : ''; 
$LEAFLET_PLUGIN_URL = isset($_GET['leafletpluginurl']) ? $_GET['leafletpluginurl'] : ''; 
echo "
(function($) {
    tinymce.create('tinymce.plugins.mm_shortcode', {
        init : function(ed, url) {
			ed.addCommand('mm_shortcode', function() {
                                ed.windowManager.open({
                                        title : 'Insert map',
					file : '".$adminurl."admin-ajax.php?action=get_mm_list',
					width : 450 + parseInt(ed.getLang('example.delta_width', 0)),
					height : 440 + parseInt(ed.getLang('example.delta_height', 0)),
                                        inline: 1
				})
			});
			ed.addButton('mm_shortcode', {title : 'Insert Map', cmd : 'mm_shortcode', image: '".$LEAFLET_PLUGIN_URL."inc/img/icon-menu-page.png' });
        },
        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('mm_shortcode', tinymce.plugins.mm_shortcode);
})(jQuery);
"; 
?>