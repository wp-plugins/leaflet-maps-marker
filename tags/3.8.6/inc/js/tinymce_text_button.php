<?php
header('Content-Type: text/javascript; charset=UTF-8'); //info: to prevent console warning on chrome
$leaflet_plugin_url = isset($_GET['leafletpluginurl']) ? base64_decode($_GET['leafletpluginurl']) : '';
$admin_url = isset($_GET['adminurl']) ? base64_decode($_GET['adminurl']) : '';
$text_add = isset($_GET['textadd']) ? base64_decode($_GET['textadd']) : '';
$text_insert = isset($_GET['textinsert']) ? base64_decode($_GET['textinsert']) : '';

echo "
(function($) {
	$('#map_button, #modal-content').remove();
	var visual_active = true;
	if($('#wp-content-wrap').is('.tmce-active')){
		tinymce.create('tinymce.plugins.mm_shortcode', {
			init : function(ed, url) {
				function open_map() {
					ed.windowManager.open({
						title : '" . $text_add . "',
						file : '" . $admin_url . "admin-ajax.php?action=get_mm_list',
						width : 450 + parseInt(ed.getLang('example.delta_width', 0)),
						height : 407 + parseInt(ed.getLang('example.delta_height', 0)),
						inline: 1
					});
				}
				$(document).on('click', '#map_button', function(){
					open_map();
					return false;
				});
				ed.addCommand('mm_shortcode', function(){
				open_map();
				});
			},
			createControl : function(n, cm) {
				return null;
			}
		});
		tinymce.PluginManager.add('mm_shortcode', tinymce.plugins.mm_shortcode);
		$('#wp-content-media-buttons').append('<a style=\'margin-left:5px;\' class=\'button\' title=\'" . $text_add . "\' id=\'map_button\' href=\'#\'><div style=\'float:left;\'><img src=\'" . $leaflet_plugin_url . "inc/img/icon-tinymce.png#\' style=\'padding:0 5px 3px 0;\'></div><div style=\'float:right;padding-top:0px;\'>" . $text_add . "</div></a>');
	}
})(jQuery);
";
?>