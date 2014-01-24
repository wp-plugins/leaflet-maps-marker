<?php
header('Content-Type: text/javascript; charset=UTF-8'); //info: to prevent console warning on chrome
$leaflet_plugin_url = isset($_GET['leafletpluginurl']) ? base64_decode($_GET['leafletpluginurl']) : '';
$admin_url = isset($_GET['adminurl']) ? base64_decode($_GET['adminurl']) : '';
$text_add = isset($_GET['textadd']) ? base64_decode($_GET['textadd']) : '';
$text_insert = isset($_GET['textinsert']) ? base64_decode($_GET['textinsert']) : '';

echo "
jQuery(function($) {
    $(document).ready(function(){
        $('#map_button').remove();
        var html_active = true;
        if($('#wp-content-wrap').is('.html-active')){
	
			$('#wp-content-media-buttons').append('<a style=\'margin-left:5px;\' class=\'button\' title=\'" . $text_add . "\' id=\'map_button\' href=\'#\'><div style=\'float:left;\'><img src=\'" . $leaflet_plugin_url . "inc/img/icon-tinymce.png\' style=\'padding:0 5px 3px 0;\'></div><div style=\'float:right;padding-top:0px;\'>" . $text_add . "</div></a>');
	
			var info = $('<div id=modal-content style=\'overflow: hidden;\' />');
			info.html('<iframe width=\'450\' height=\'407\' scrolling=\'no\' src=\'" . $admin_url . "admin-ajax.php?action=get_mm_list&mode=html\' />')
				info.wpdialog({
					title : '" . $text_insert . "',
					dialogClass: 'wp-dialog',
					width : 450,
					height : 440,
					modal : true,
					autoOpen : false,
					closeOnEscape : true
				});
				$(document).on('click', '#map_button', function(event) {
					info.wpdialog('open');
					return false;
				});
        }
	});
});
";
?>