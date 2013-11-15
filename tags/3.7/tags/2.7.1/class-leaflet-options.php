<?php
/**
 * Leaflet Maps Marker Plugin - settings class
 * based on class by Alison Barrett, http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
 */
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'class-leaflet-options.php') { die ("Please do not access this file directly. Thanks!<br/><a href='http://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
class Leafletmapsmarker_options {
	private $sections;
	private $checkboxes;
	private $settings;
	
	 /**
	 *
	 * Construct
	 *
	 */
	public function __construct() {
		//info:  This will keep track of the checkbox options for the validate_settings function.
		$this->checkboxes = array();
		$this->settings = array();
		$this->get_settings();
		$this->sections['basemaps']      = 'Basemaps';
		$this->sections['overlays']      = 'Overlays';
		$this->sections['wms']      = 'WMS';
		$this->sections['defaults_marker']   = 'Marker/Icon/Popup defaults';
		$this->sections['defaults_layer']   = 'Layer defaults';
		$this->sections['google']   = 'Google';
		$this->sections['directions']   = 'Directions';
		$this->sections['ar']   = 'Augmented-Reality';
		$this->sections['misc']   = 'Misc';
		$this->sections['reset']        = 'Reset';
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		if ( ! get_option( 'leafletmapsmarker_options' ) )
			$this->initialize_settings();
	}
	
	/**
	 * Create settings field
	 *
	 * @since 1.0
	 */
	public function create_setting( $args = array() ) {
		
		$defaults = array(
			'id'      => 'default_field',
			'version' => '',
			'title'   => __( 'Default Field','lmm' ),
			'desc'    => __( 'This is a default description.','lmm' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'basemaps',
			'choices' => array(),
			'class'   => ''
		);
			
		extract( wp_parse_args( $args, $defaults ) );
		
		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);
		
		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;
		
		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'leafletmapsmarker_settings', $section, $field_args );
	}
	
	/**
	 * Display options page
	 *
	 * @since 1.0
	 */
	public function display_page() {
		echo '<div style="float:left;" class="icon32" id="icon-options-general"></div><h3>'.__('Settings','lmm').'</h3><div class="wrap lmmsettings">';
		if ( isset( $_GET['settings-updated'] ) )
			echo '<div class="updated fade"><p>' . __( 'Plugin options updated.','lmm' ) . '</p></div>';
		include('leaflet-admin-header.php');
		echo '<form action="options.php" method="post">';
		settings_fields( 'leafletmapsmarker_options' );
		echo '<div class="ui-tabs">
			<ul class="ui-tabs-nav">';
		
		foreach ( $this->sections as $section_slug => $section )
			echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';
		
		echo '</ul>';
		do_settings_sections( $_GET['page'] );
		
		echo '</div>
		<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes','lmm' ) . '" /></p>
		
	</form>';
	
	echo '<script type="text/javascript">
		jQuery(document).ready(function($) {
			var sections = [];';
			
			foreach ( $this->sections as $section_slug => $section )
				echo "sections['$section'] = '$section_slug';";
			echo 'var wrapped = $(".wrap h3").wrap("<div class=\"ui-tabs-panel\">");
			wrapped.each(function() {
				$(this).parent().append($(this).parent().nextUntil("div.ui-tabs-panel"));
			});
			$(".ui-tabs-panel").each(function(index) {
				$(this).attr("id", sections[$(this).children("h3").text()]);
				if (index > 0)
					$(this).addClass("ui-tabs-hide");
			});
			$(".ui-tabs").tabs({
				fx: { opacity: "toggle", duration: "fast" }
			});
			
			$("input[type=text], textarea").each(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "")
					$(this).css("color", "#999");
			});
			
			$("input[type=text], textarea").focus(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "") {
					//$(this).val("");
					$(this).css("color", "#000");
				}
			}).blur(function() {
				if ($(this).val() == "" || $(this).val() == $(this).attr("placeholder")) {
					$(this).val($(this).attr("placeholder"));
					$(this).css("color", "#999");
				}
			});
			
			$(".lmmsettings h3, .lmmsettings table, .leafletmapsmarker-listings").show();
			
			//info:  This will make the "warning" checkbox class really stand out when checked.
			$(".warning").change(function() {
				if ($(this).is(":checked"))
					$(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
				else
					$(this).parent().css("background", "none").css("color", "inherit").css("fontWeight", "normal");
			});
			
			//info:  Browser compatibility
			if ($.browser.mozilla) 
			         $("form").attr("autocomplete", "off");
		});
	</script>
</div>';
		
	}
	
	/**
	 * Description for section
	 */
	public function display_section() {
		//Standard - nothing to add
	}
	
	/**
	 * Listing for basemaps section
	 */
	public function display_basemaps_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>' . __('Index','lmm') . '</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li><a href="#basemaps1">' . __('Default basemap for new markers/layers','lmm') . '</a></li>
			<li><a href="#basemaps2">' . __('Names for default basemaps','lmm') . '</a></li>
			<li><a href="#basemaps3">' . __('Available basemaps in control box','lmm') . '</a></li>
			<li><a href="#basemaps4">' . __('Interaction options','lmm') . '</a></li>
			<li><a href="#basemaps5">' . __('Keyboard navigation options','lmm') . '</a></li>
			<li><a href="#basemaps6">' . __('Panning inertia options','lmm') . '</a></li>
			<li><a href="#basemaps7">' . __('Control options','lmm') . '</a></li>
			<li><a href="#basemaps8">' . __('Scale control','lmm') . '</a></li>
			<li><a href="#basemaps9">' . __('Retina display detection','lmm') . '</a></li>
			<li><a href="#basemaps10">' . __('Bing Maps API key','lmm') . '</a></li>
			<li><a href="#basemaps11">' . __('OGD Vienna Selector','lmm') . '</a></li>
			<li><a href="#basemaps12">' . __('Cloudmade 1 settings','lmm') . '</a></li>
			<li><a href="#basemaps13">' . __('Cloudmade 2 settings','lmm') . '</a></li>
			<li><a href="#basemaps14">' . __('Cloudmade 3 settings','lmm') . '</a></li>
			<li><a href="#basemaps15">' . __('MapBox 1 settings','lmm') . '</a></li>
			<li><a href="#basemaps16">' . __('MapBox 2 settings','lmm') . '</a></li>
			<li><a href="#basemaps17">' . __('MapBox 3 settings','lmm') . '</a></li>
			<li><a href="#basemaps18">' . __('Custom basemap 1 settings','lmm') . '</a></li>
			<li><a href="#basemaps19">' . __('Custom basemap 2 settings','lmm') . '</a></li>
			<li><a href="#basemaps20">' . __('Custom basemap 3 settings','lmm') . '</a></li></ul></span>';
	}
	/**
	 * Listing for overlays section
	 */
	public function display_overlays_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>' . __('Index','lmm') . '</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li><a href="#overlays1">' . __('Available overlays for new markers/layers','lmm') . '</li>
			<li><a href="#overlays2">' . __('Custom overlay settings','lmm') . '</a></li>
			<li><a href="#overlays3">' . __('Custom overlay 2 settings','lmm') . '</a></li>			
			<li><a href="#overlays4">' . __('Custom overlay 3 settings','lmm') . '</a></li>
			<li><a href="#overlays5">' . __('Custom overlay 4 settings','lmm') . '</a></li></ul></span>';
	}	
	/**
	 * Listing for wms section
	 */
	public function display_wms_section() {
		
		echo '<span class="leafletmapsmarker-listings"><p>' . __( 'WMS stands for <a href="http://www.opengeospatial.org/standards/wms" target="_blank">Web Map Service</a> and is a standard protocol for serving georeferenced map images over the Internet that are generated by a map server using data from a GIS database.<br/>With Leaflet Maps Marker you can configure up to 10 WMS layers which can be enabled for each map. As default, 10 WMS layers from <a href="http://data.wien.gv.at" target="_blank">OGD Vienna</a> and from the <a href="http://www.eea.europa.eu/code/gis" target="_blank">European Environment Agency</a> have been predefined for you.<br/>A selection of further possible WMS layers can be found at <a href="http://www.mapsmarker.com/wms" target="_blank">http://www.mapsmarker.com/wms</a>', 'lmm') . '</p><p><strong>' . __('Index','lmm') . '</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li><a href="#wms1">' . __('Available WMS layers for new markers/layers','lmm') . '</a></li>
			<li><a href="#wms2">' . __('WMS layer 1 settings','lmm') . '</a></li>
			<li><a href="#wms3">' . __('WMS layer 2 settings','lmm') . '</a></li>
			<li><a href="#wms4">' . __('WMS layer 3 settings','lmm') . '</a></li>
			<li><a href="#wms5">' . __('WMS layer 4 settings','lmm') . '</a></li>
			<li><a href="#wms6">' . __('WMS layer 5 settings','lmm') . '</a></li>
			<li><a href="#wms7">' . __('WMS layer 6 settings','lmm') . '</a></li>
			<li><a href="#wms8">' . __('WMS layer 7 settings','lmm') . '</a></li>
			<li><a href="#wms9">' . __('WMS layer 8 settings','lmm') . '</a></li>
			<li><a href="#wms10">' . __('WMS layer 9 settings','lmm') . '</a></li>
			<li><a href="#wms11">' . __('WMS layer 10 settings','lmm') . '</a></li></ul></span>';
	}
	/**
	 * Listing for marker defaults section
	 */
	public function display_defaults_marker_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>' . __('Index','lmm') . '</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li><a href="#defaults_marker1">' . __('Default values for new marker maps','lmm') . '</a></li>
			<li><a href="#defaults_marker2">' . __('Default values for marker icons','lmm') . '</a></li>			
			<li><a href="#defaults_marker3">' . __('Default values for marker popups','lmm') . '</a></li>			
			<li><a href="#defaults_marker4">' . __('Default values for markers added directly','lmm') . '</a></li></ul></span>';
	}	
	/**
	 * Listing for layer defaults section
	 */
	public function display_defaults_layer_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>' . __('Index','lmm') . '</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li><a href="#defaults_layer1">' . __('Default values for new layer maps','lmm') . '</a></li>
			<li><a href="#defaults_layer2">' . __('List marker settings','lmm') . '</a></li></ul></span>';
	}	
	/**
	 * Listing for google section
	 */
	public function display_google_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>' . __('Index','lmm') . '</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li><a href="#google1">' . __('Google Maps API key','lmm') . '</a></li>
			<li><a href="#google2">' . __('Google language localization','lmm') . '</a></li>
			<li><a href="#google3">' . __('Google Maps base domain','lmm') . '</a></li>
			<li><a href="#google4">' . __('Google Places bounds','lmm') . '</a></li>
			<li><a href="#google5">' . __('Google Places search prefix','lmm') . '</a></li></ul></span>';
	}	
	/**
	 * Listing for directions section
	 */
	public function display_directions_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>' . __('Index','lmm') . '</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li><a href="#directions1">' . __('General settings','lmm') . '</a></li>
			<li><a href="#directions2">Google Maps</a></li>
			<li><a href="#directions3">yournavigation.org</a></li>
			<li><a href="#directions4">map.project-osrm.org</a></li>
			<li><a href="#directions5">openrouteservice.org</a></li></ul></span>';
	}		
	/**
	 * Listing for misc section
	 */
	public function display_misc_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>' . __('Index','lmm') . '</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li><a href="#misc1">' . __('General settings','lmm') . '</a></li>
			<li><a href="#misc2">' . __('Language settings','lmm') . '</a></li>
			<li><a href="#misc3">' . __('KML settings','lmm') . '</a></li>
			<li><a href="#misc4">' . __('Available columns for marker listing page','lmm') . '</a></li>
			<li><a href="#misc5">' . __('Sort order for marker listing page','lmm') . '</a></li>
			<li><a href="#misc6">' . __('Available columns for layer listing page','lmm') . '</a></li>
			<li><a href="#misc7">' . __('Sort order for layer listing page','lmm') . '</a></li></ul></span>';
	}	
	/**
	 * HTML output for text field
	 */
	public function display_setting( $args = array() ) {
		
		extract( $args );
		
		$options = get_option( 'leafletmapsmarker_options' );
		
		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;
		
		switch ( $type ) {
			
			case 'heading':
				echo '</td></tr><tr valign="top"><td colspan="2" rowspan="2"><h4>' . $desc . '</h4>';
				break;
			case 'helptext':
				echo '</td></tr><tr valign="top"><td colspan="2">' . $desc . '';
				break;
			case 'checkbox':
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';
				break;
			case 'checkbox-readonly':
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' disabled="disabled" /> <label for="' . $id . '">' . $desc . '</label>';
				break;
			case 'select':
				echo '<select class="select' . $field_class . '" name="leafletmapsmarker_options[' . $id . ']">';
				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';
				echo '</select>';
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;
			case 'radio':
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="leafletmapsmarker_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span>';
				break;
			
			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;
			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;
			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" style="width:30em;" type="text" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
		 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		break;
			case 'text-readonly':
			default:
		 		echo '<input readonly="readonly" class="regular-text' . $field_class . '" style="width:60em;" type="text" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
	 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		break;
			case 'text-deletable':
			default:
		 		echo '<input class="regular-text' . $field_class . '" style="width:60em;" type="text" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
	 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		break;
		}
	}
	
	/**
	 * Settings and defaults
	 */
	public function get_settings() {
		
		/*===========================================
		*
		*
		* section basemaps
		*
		*
		===========================================*/
		/*
		* Default basemap for new markers/layers
		*/
		$this->settings['default_basemap_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps1" class="lmm-index-links"></a>' . __( 'Default basemap for new markers/layers', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['default_basemap_helptext1'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __( 'Please select the basemap which should be pre-selected as default for new markers and layers. Can be changed afterwards on each marker/layer.', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['standard_basemap'] = array(
			'version' => '1.9',
			'section' => 'basemaps',
			'title'   => __('Default basemap','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ogdwien_basemap',
			'choices' => array(
				'osm_mapnik' => __('OpenStreetMap (Mapnik, max zoom 18)','lmm'),
				'mapquest_osm' => __('MapQuest (OSM, max zoom 18)','lmm'),
				'mapquest_aerial' => __('MapQuest (Aerial, max zoom 12 globally, 12+ in the United States)','lmm'),
				'googleLayer_roadmap' => __('Google Maps (Roadmap)','lmm')  . ' - <strong>' . __('API key required for commercial usage!','lmm') . '</strong> <a href="http://www.mapsmarker.com/google-maps-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_satellite' => __('Google Maps (Satellite)','lmm')  . ' - <strong>' . __('API key required for commercial usage!','lmm') . '</strong> <a href="http://www.mapsmarker.com/google-maps-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_hybrid' => __('Google Maps (Hybrid)','lmm')  . ' - <strong>' . __('API key required for commercial usage!','lmm') . '</strong> <a href="http://www.mapsmarker.com/google-maps-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_terrain' => __('Google Maps (Terrain)','lmm')  . ' - <strong>' . __('API key required for commercial usage!','lmm') . '</strong> <a href="http://www.mapsmarker.com/google-maps-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingaerial' => __('Bing Maps (Aerial)','lmm') . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="http://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingaerialwithlabels' => __('Bing Maps (Aerial+Labels)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="http://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingroad' => __('Bing Maps (Road)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="http://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'ogdwien_basemap' => __('OGD Vienna basemap (max zoom 19)','lmm'),
				'ogdwien_satellite' => __('OGD Vienna satellite (max zoom 19)','lmm'),
				'cloudmade' => 'Cloudmade',
				'cloudmade2' => 'Cloudmade 2',
				'cloudmade3' => 'Cloudmade 3',
				'mapbox' => 'MapBox 1',
				'mapbox2' => 'MapBox 2',
				'mapbox3' => 'MapBox 3',
				'custom_basemap' => __('Custom basemap','lmm'),
				'custom_basemap2' => __('Custom basemap 2','lmm'),
				'custom_basemap3' => __('Custom basemap 3','lmm')
			)
		);
		/*
		* Names for default basemaps
		*/
		$this->settings['default_basemap_heading2'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps2" class="lmm-index-links"></a>' . __( 'Names for default basemaps', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['default_basemap_helptext2'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Optionally you can also change the name of the predefined basemaps in the controlbox.', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-names.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['default_basemap_name_osm_mapnik'] = array(
			'version' => '1.0',
			'title'   => 'OpenStreetMap (Mapnik)',
			'desc'    => '',
			'std'     => 'OSM Mapnik',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_mapquest_osm'] = array(
			'version' => '1.0',
			'title'   => 'Mapquest',
			'desc'    => '',
			'std'     => 'Mapquest OSM',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_mapquest_aerial'] = array(
			'version' => '1.0',
			'title'   => 'Mapquest (Aerial)',
			'desc'    => '',
			'std'     => 'Mapquest (Aerial)',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_googleLayer_roadmap'] = array(
			'version' => '2.5',
			'title'   => __('Google Maps (Roadmap)','lmm'),
			'desc'    => '',
			'std'   => __('Google Maps (Roadmap)','lmm'),
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_googleLayer_satellite'] = array(
			'version' => '2.5',
			'title'   => __('Google Maps (Satellite)','lmm'),
			'desc'    => '',
			'std'   => __('Google Maps (Satellite)','lmm'),
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_googleLayer_hybrid'] = array(
			'version' => '2.5',
			'title'   => __('Google Maps (Hybrid)','lmm'),
			'desc'    => '',
			'std'   => __('Google Maps (Hybrid)','lmm'),
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_googleLayer_terrain'] = array(
			'version' => '2.6',
			'title'   => __('Google Maps (Terrain)','lmm'),
			'desc'    => '',
			'std'   => __('Google Maps (Terrain)','lmm'),
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_bingaerial'] = array(
			'version' => '2.6',
			'title'   => __('Bing Maps (Aerial)','lmm'),
			'desc'    => '',
			'std'   => __('Bing Maps (Aerial)','lmm'),
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_bingaerialwithlabels'] = array(
			'version' => '2.6',
			'title'   => __('Bing Maps (Aerial+Labels)','lmm'),
			'desc'    => '',
			'std'   => __('Bing Maps (Aerial+Labels)','lmm'),
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_bingroad'] = array(
			'version' => '2.6',
			'title'   => __('Bing Maps (Road)','lmm'),
			'desc'    => '',
			'std'   => __('Bing Maps (Road)','lmm'),
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_ogdwien_basemap'] = array(
			'version' => '1.0',
			'title'   => 'OGD Vienna basemap',
			'desc'    => '',
			'std'     => 'OGD Vienna basemap',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_ogdwien_satellite'] = array(
			'version' => '1.0',
			'title'   => 'OGD Vienna satellite',
			'desc'    => '',
			'std'     => 'OGD Vienna satellite',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['cloudmade_name'] = array(
			'version' => '1.6',
			'title'   => 'Cloudmade',
			'desc'    => '',
			'std'     => 'Cloudmade',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['cloudmade2_name'] = array(
			'version' => '1.6',
			'title'   => 'Cloudmade 2',
			'desc'    => '',
			'std'     => 'Cloudmade 2',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['cloudmade3_name'] = array(
			'version' => '1.6',
			'title'   => 'Cloudmade 3',
			'desc'    => '',
			'std'     => 'Cloudmade 3',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['mapbox_name'] = array(
			'version' => '2.1',
			'title'   => 'MapBox',
			'desc'    => '',
			'std'     => 'Blue Marble Topography',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['mapbox2_name'] = array(
			'version' => '2.1',
			'title'   => 'MapBox 2',
			'desc'    => '',
			'std'     => 'Geography Class',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['mapbox3_name'] = array(
			'version' => '2.1',
			'title'   => 'MapBox 3',
			'desc'    => '',
			'std'     => 'MapBox Streets',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['custom_basemap_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Custom Basemap', 'lmm' ),
			'desc'    => '',
			'std'     => 'Custom1',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['custom_basemap2_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Custom Basemap 2', 'lmm' ),
			'desc'    => '',
			'std'     => 'Custom2',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['custom_basemap3_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Custom Basemap 3', 'lmm' ),
			'desc'    => '',
			'std'     => 'Custom3',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		
		/*
		* Available basemaps in control box
		*/
		$this->settings['layer_controlbox_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps3" class="lmm-index-links"></a>' . __( 'Available basemaps in control box', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['default_basemap_helptext3'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'    => '',
			'desc'    => __( 'Please select the basemaps which should be available in the control box.', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-available-basemaps.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['controlbox_osm_mapnik'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __( 'Basemaps available in control box', 'lmm' ),
			'desc'    => __( 'OpenStreetMap (Mapnik)', 'lmm' ),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_mapquest_osm'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('MapQuest (OSM)','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_mapquest_aerial'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('MapQuest (Aerial)','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_googleLayer_roadmap'] = array(
			'version' => '2.5',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Google Maps (Roadmap)','lmm')  . ' - <strong>' . __('API key required for commercial usage!','lmm') . '</strong> <a href="http://www.mapsmarker.com/google-maps-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_googleLayer_satellite'] = array(
			'version' => '2.5',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Google Maps (Satellite)','lmm')  . ' - <strong>' . __('API key required for commercial usage!','lmm') . '</strong> <a href="http://www.mapsmarker.com/google-maps-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_googleLayer_hybrid'] = array(
			'version' => '2.5',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Google Maps (Hybrid)','lmm')  . ' - <strong>' . __('API key required for commercial usage!','lmm') . '</strong> <a href="http://www.mapsmarker.com/google-maps-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_googleLayer_terrain'] = array(
			'version' => '2.6',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Google Maps (Terrain)','lmm')  . ' - <strong>' . __('API key required for commercial usage!','lmm') . '</strong> <a href="http://www.mapsmarker.com/google-maps-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_bingaerial'] = array(
			'version' => '2.6',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Bing Maps (Aerial)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="http://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['controlbox_bingaerialwithlabels'] = array(
			'version' => '2.6',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Bing Maps (Aerial+Labels)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="http://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['controlbox_bingroad'] = array(
			'version' => '2.6',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Bing Maps (Road)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="http://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['controlbox_ogdwien_basemap'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('OGD Vienna basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_ogdwien_satellite'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('OGD Vienna satellite','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_cloudmade'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => 'Cloudmade',
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['controlbox_cloudmade2'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => 'Cloudmade 2',
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['controlbox_cloudmade3'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => 'Cloudmade 3',
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['controlbox_mapbox'] = array(
			'version' => '2.1',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => 'MapBox',
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['controlbox_mapbox2'] = array(
			'version' => '2.1',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => 'MapBox 2',
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['controlbox_mapbox3'] = array(
			'version' => '2.1',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => 'MapBox 3',
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['controlbox_custom_basemap'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Custom basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['controlbox_custom_basemap2'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Custom basemap 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['controlbox_custom_basemap3'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Custom basemap 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		/*
		* Interaction options 
		* formerly "General map settings" and moved to "Basemaps" from "Misc" tab
		*/
		$this->settings['map_interaction_options_heading'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps4" class="lmm-index-links"></a>' . __( 'Interaction options', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['map_interaction_options_helptext'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'The following settings will be used for all marker and layer maps', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['misc_map_dragging'] = array(
			'version' => '2.2',
			'section' => 'basemaps',
			'title'   => 'dragging',
			'desc'    => __('Whether the map be draggable with mouse/touch or not.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);			
		$this->settings['misc_map_touchzoom'] = array(
			'version' => '2.2',
			'section' => 'basemaps',
			'title'   => 'touchZoom',
			'desc'    => __('Whether the map can be zoomed by touch-dragging with two fingers.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);			
		$this->settings['misc_map_scrollwheelzoom'] = array(
			'version' => '2.2',
			'section' => 'basemaps',
			'title'   => 'scrollWheelZoom',
			'desc'    => __('Whether the map can be zoomed by using the mouse wheel.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);	
		$this->settings['misc_map_doubleclickzoom'] = array(
			'version' => '2.2',
			'section' => 'basemaps',
			'title'   => 'doubleClickZoom',
			'desc'    => __('Whether the map can be zoomed in by double clicking on it.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);					
		$this->settings['map_interaction_options_boxzoom'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'boxzoom',
			'desc'    => __('Whether the map can be zoomed to a rectangular area specified by dragging the mouse while pressing shift.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);		
		$this->settings['misc_map_trackresize'] = array(
			'version' => '2.2',
			'section' => 'basemaps',
			'title'   => 'trackResize',
			'desc'    => __('Whether the map automatically handles browser window resize to update itself.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);	
		$this->settings['map_interaction_options_worldcopyjump'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'worldCopyJump',
			'desc'    => __('With this option enabled, the map tracks when you pan to another "copy" of the world and moves all overlays like markers and vector layers there.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);		
		$this->settings['misc_map_closepopuponclick'] = array(
			'version' => '2.2',
			'section' => 'basemaps',
			'title'   => 'closePopupOnClick',
			'desc'    => __('Set it to false if you do not want popups to close when user clicks the map.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);					
		/*
		* Keyboard navigation options 
		*/
		$this->settings['map_keyboard_navigation_options_heading'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps5" class="lmm-index-links"></a>' . __( 'Keyboard navigation options', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['map_keyboard_navigation_options_helptext'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'The following settings will be used for all marker and layer maps', 'lmm'),
			'type'    => 'helptext'
		);		
		$this->settings['map_keyboard_navigation_options_keyboard'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'keyboard',
			'desc'    => __('Makes the map focusable and allows users to navigate the map with keyboard arrows and +/- keys','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);			
		$this->settings['map_keyboard_navigation_options_keyboardpanoffset'] = array(
			'version' => '2.7.1',
			'title'   => 'keyboardPanOffset',
			'desc'    => __('Amount of pixels to pan when pressing an arrow key','lmm'),
			'std'     => '80',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['map_keyboard_navigation_options_keyboardzoomoffset'] = array(
			'version' => '2.7.1',
			'title'   => 'keyboardZoomOffset',
			'desc'    => __( 'Number of zoom levels to change when pressing + or - key.', 'lmm' ),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		/*
		* Panning inertia options
		*/
		$this->settings['map_panning_inertia_options_heading'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps6" class="lmm-index-links"></a>' . __( 'Panning inertia options', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['map_panning_inertia_options_helptext'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'The following settings will be used for all marker and layer maps', 'lmm'),
			'type'    => 'helptext'
		);		
		$this->settings['map_panning_inertia_options_inertia'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'inertia',
			'desc'    => __('If enabled, panning of the map will have an inertia effect where the map builds momentum while dragging and continues moving in the same direction for some time. Feels especially nice on touch devices.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);			
		$this->settings['map_panning_inertia_options_inertiadeceleration'] = array(
			'version' => '2.7.1',
			'title'   => 'inertiaDeceleration',
			'desc'    => __('The rate with which the inertial movement slows down, in pixels/second','lmm'),
			'std'     => '3000',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['map_panning_inertia_options_inertiamaxspeed'] = array(
			'version' => '2.7.1',
			'title'   => 'inertiaMaxSpeed',
			'desc'    => __('Max speed of the inertial movement, in pixels/second.','lmm'),
			'std'     => '1500',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		/*
		* Control options
		*/
		$this->settings['map_control_options_heading'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps7" class="lmm-index-links"></a>' . __( 'Control options', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['map_control_options_helptext'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'The following settings will be used for all marker and layer maps', 'lmm'),
			'type'    => 'helptext'
		);	
		$this->settings['misc_map_zoomcontrol'] = array(
			'version' => '2.2',
			'section' => 'basemaps',
			'title'   => 'zoomControl',
			'desc'    => __('Whether the zoom control is added to the map by default.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		/*
		* Scale control options
		*/
		$this->settings['map_scale_control_heading'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps8" class="lmm-index-links"></a>' . __( 'Scale control options', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['map_scale_control_helptext'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'A simple scale control that shows the scale of the current center of screen in metric (m/km) and/or imperial (mi/ft) systems. The following settings will be used for all marker and layer maps.', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-basemap-scale-control.jpg" />',
			'type'    => 'helptext'
		);	
		$this->settings['map_scale_control'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => __('Scale Control','lmm'),
			'desc'    => __('Whether the scale control is added to the map by default.','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->settings['map_scale_control_position'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => __('Position','lmm'),
			'desc'    => __('The position of the control (one of the map corners).','lmm'),
			'type'    => 'radio',
			'std'     => 'bottomleft',
			'choices' => array(
				'bottomleft' => __('Bottom left of the map','lmm'),
				'bottomright' => __('Bottom right of the map','lmm'),
				'topright' => __('Top right of the map','lmm'),
				'topleft' => __('Top left of the map','lmm')
			)
		);
		$this->settings['map_scale_control_maxwidth'] = array(
			'version' => '2.7.1',
			'title'   => 'maxWidth',
			'desc'    => __('Maximum width of the control in pixels. The width is set dynamically to show round values (e.g. 100, 200, 500).','lmm'),
			'std'     => '100',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['map_scale_control_metric'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'metric',
			'desc'    => __('Whether to show the metric scale line (m/km).','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);		
		$this->settings['map_scale_control_imperial'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'imperial',
			'desc'    => __('Whether to show the imperial scale line (mi/ft).','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);			
		$this->settings['map_scale_control_updatewhenidle'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'updateWhenIdle',
			'desc'    => __('If true, the control is updated on moveend, otherwise it is always up-to-date (updated on move).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);	
		/*
		* Retina display detection
		*/
		$this->settings['map_retina_detection_heading'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps9" class="lmm-index-links"></a>' . __( 'Retina display detection', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['map_retina_detection_helptext'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'The following settings will be used for all marker and layer maps', 'lmm'),
			'type'    => 'helptext'
		);	
		$this->settings['map_retina_detection'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'detectRetina',
			'desc'    => __('If true and user is on a retina display (= iPhone 4/4S/5, iPad 3, MacBook Pro 3rd Generation), it will request four tiles of half the specified size and a bigger zoom level in place of one to utilize the high resolution.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);										
		/*
		* Bing Maps API Key
		*/
		$this->settings['bingmaps_api_key_heading'] = array(
			'version' => '2.6',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps10" class="lmm-index-links"></a>' . __( 'Bing Maps API Key', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['bingmaps_api_key_helptext'] = array(
			'version' => '2.6',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'An API key is required if you want to use Bing Maps as basemap for marker or layer maps. Please click on the question mark for more info on how to get your API key.', 'lmm') . ' <a href="http://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'helptext'
		);		
		$this->settings['bingmaps_api_key'] = array(
			'version' => '2.6',
			'title'   => __( 'Bing Maps API key', 'lmm' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		/*
		* OGD Vienna Selector
		*/
		$this->settings['ogdvienna_selector_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps11" class="lmm-index-links"></a>' . __( 'OGD Vienna Selector', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['ogdvienna_selector_helptext'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'If coordinates within boundaries of Vienna/Austria are selected for a marker or layer, the basemap automatically switches to OGD Vienna basemap and the overlay OGD Vienna addresses gets checked.', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-ogdvienna-selector.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['ogdvienna_selector'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __('OGD Vienna Selector','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ogdwien_basemap',
			'choices' => array(
				'ogdwien_basemap' => __('enabled (use OGD Vienna basemap)','lmm'),
				'ogdwien_satellite' => __('enabled (use OGD Vienna satellite)','lmm'),
				'disabled' => __('disabled (use default basemap)','lmm')
			)
		);
		$this->settings['ogdvienna_selector_addresses'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __( 'enable OGD Vienna addresses overlay', 'lmm' ),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		/*
		* Cloudmade settings
		*/
		$this->settings['cloudmade_heading'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps12" class="lmm-index-links"></a>' . __( 'Cloudmade settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['cloudmade_helptext'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Tutorial for Cloudmade configuration:', 'lmm').'<a href="http://mapsmarker.com/cloudmade" target="_blank">http://mapsmarker.com/cloudmade</a><br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-cloudmade.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['cloudmade_api_key'] = array(
			'version' => '1.6',
			'title'   => __( 'API key', 'lmm' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['cloudmade_styleid'] = array(
			'version' => '1.6',
			'title'   => 'styleID',
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['cloudmade_double_resolution'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'title'   => __('Double resolution','lmm'),
			'desc'    => __('This will improve map look for iPhone 4, Motorola Milestone, etc.','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);		
		/*
		* Cloudmade 2 settings
		*/
		$this->settings['cloudmade2_heading'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps13" class="lmm-index-links"></a>' . __( 'Cloudmade 2 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['cloudmade2_helptext'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Tutorial for Cloudmade configuration:', 'lmm').'<a href="http://mapsmarker.com/cloudmade" target="_blank">http://mapsmarker.com/cloudmade</a><br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-cloudmade.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['cloudmade2_api_key'] = array(
			'version' => '1.6',
			'title'   => __( 'API key', 'lmm' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['cloudmade2_styleid'] = array(
			'version' => '1.6',
			'title'   => 'styleID',
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['cloudmade2_double_resolution'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'title'   => __('Double resolution','lmm'),
			'desc'    => __('This will improve map look for iPhone 4, Motorola Milestone, etc.','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);				
		/*
		* Cloudmade 3 settings
		*/
		$this->settings['cloudmade3_heading'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps14" class="lmm-index-links"></a>' . __( 'Cloudmade 3 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['cloudmade3_helptext'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Tutorial for Cloudmade configuration:', 'lmm').'<a href="http://mapsmarker.com/cloudmade" target="_blank">http://mapsmarker.com/cloudmade</a><br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-cloudmade.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['cloudmade3_api_key'] = array(
			'version' => '1.6',
			'title'   => __( 'API key', 'lmm' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['cloudmade3_styleid'] = array(
			'version' => '1.6',
			'title'   => 'styleID',
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['cloudmade3_double_resolution'] = array(
			'version' => '1.6',
			'section' => 'basemaps',
			'title'   => __('Double resolution','lmm'),
			'desc'    => __('This will improve map look for iPhone 4, Motorola Milestone, etc.','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);	
		/*
		* MapBox settings
		*/
		$this->settings['mapbox_heading'] = array(
			'version' => '2.1',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps15" class="lmm-index-links"></a>' . __( 'MapBox settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['mapbox_helptext'] = array(
			'version' => '2.1',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-mapbox.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['mapbox_user'] = array(
			'version' => '2.1',
			'title'   => __( 'User', 'lmm' ),
			'desc'    => __('e.g.','lmm') . 'http://tiles.mapbox.com/<strong>mapbox</strong>/map/blue-marble-topo-jul',
			'std'     => 'mapbox',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['mapbox_map'] = array(
			'version' => '2.1',
			'title'   => __('map','lmm'),
			'desc'    => __('e.g.','lmm') . 'http://tiles.mapbox.com/mapbox/map/<strong>blue-marble-topo-jul</strong>',
			'std'     => 'blue-marble-topo-jul',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['mapbox_minzoom'] = array(
			'version' => '2.1',
			'title'   => __('Minimum zoom level','lmm'),
			'desc'    => '',
			'std'     => '0',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['mapbox_maxzoom'] = array(
			'version' => '2.1',
			'title'   => __('Maximum zoom level','lmm'),
			'desc'    => '',
			'std'     => '8',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['mapbox_attribution'] = array(
			'version' => '2.1',
			'title'   => __('Attribution','lmm'),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider X&lt;/a&gt;",
			'std'     => "MapBox/NASA, <a href=&quot;http://www.mapbox.com&quot; target=&quot;_blank&quot;>http://www.mapbox.com</a>",
			'type'    => 'text',
			'section' => 'basemaps'
		);
		/*
		* MapBox 2 settings
		*/
		$this->settings['mapbox2_heading'] = array(
			'version' => '2.1',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps16" class="lmm-index-links"></a>' . __( 'MapBox 2 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['mapbox2_helptext'] = array(
			'version' => '2.1',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-mapbox.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['mapbox2_user'] = array(
			'version' => '2.1',
			'title'   => __( 'User', 'lmm' ),
			'desc'    => __('e.g.','lmm') . 'http://tiles.mapbox.com/<strong>mapbox</strong>/map/geography-class',
			'std'     => 'mapbox',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['mapbox2_map'] = array(
			'version' => '2.1',
			'title'   => __('map','lmm'),
			'desc'    => __('e.g.','lmm') . 'http://tiles.mapbox.com/mapbox/map/<strong>geography-class</strong>',
			'std'     => 'geography-class',
			'type'    => 'text',
			'section' => 'basemaps'
		);	
		$this->settings['mapbox2_minzoom'] = array(
			'version' => '2.1',
			'title'   => __('Minimum zoom level','lmm'),
			'desc'    => '',
			'std'     => '0',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['mapbox2_maxzoom'] = array(
			'version' => '2.1',
			'title'   => __('Maximum zoom level','lmm'),
			'desc'    => '',
			'std'     => '8',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['mapbox2_attribution'] = array(
			'version' => '2.1',
			'title'   => __('Attribution','lmm'),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider X&lt;/a&gt;",
			'std'     => "MapBox, <a href=&quot;http://www.mapbox.com&quot; target=&quot;_blank&quot;>http://www.mapbox.com</a>",
			'type'    => 'text',
			'section' => 'basemaps'
		);
		/*
		* MapBox 3 settings
		*/
		$this->settings['mapbox3_heading'] = array(
			'version' => '2.1',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps17" class="lmm-index-links"></a>' . __( 'MapBox 3 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['mapbox3_helptext'] = array(
			'version' => '2.1',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-mapbox.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['mapbox3_user'] = array(
			'version' => '2.1',
			'title'   => __( 'User', 'lmm' ),
			'desc'    => __('e.g.','lmm') . 'http://tiles.mapbox.com/<strong>mapbox</strong>/map/mapbox-streets',
			'std'     => 'mapbox',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['mapbox3_map'] = array(
			'version' => '2.1',
			'title'   => __('map','lmm'),
			'desc'    => __('e.g.','lmm') . 'http://tiles.mapbox.com/mapbox/map/<strong>mapbox-streets</strong>',
			'std'     => 'mapbox-streets',
			'type'    => 'text',
			'section' => 'basemaps'
		);					
		$this->settings['mapbox3_minzoom'] = array(
			'version' => '2.1',
			'title'   => __('Minimum zoom level','lmm'),
			'desc'    => '',
			'std'     => '0',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['mapbox3_maxzoom'] = array(
			'version' => '2.1',
			'title'   => __('Maximum zoom level','lmm'),
			'desc'    => '',
			'std'     => '17',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['mapbox3_attribution'] = array(
			'version' => '2.1',
			'title'   => __('Attribution','lmm'),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider X&lt;/a&gt;",
			'std'     => "MapBox, <a href=&quot;http://www.mapbox.com&quot; target=&quot;_blank&quot;>http://www.mapbox.com</a>",
			'type'    => 'text',
			'section' => 'basemaps'
		);
		/*
		* Custom basemap 1 settings
		*/
		$this->settings['custom_basemap_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps18" class="lmm-index-links"></a>' . __( 'Custom basemap 1 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['custom_basemap_helptext'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom basemap', 'lmm').' (custom 1):<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-custom-basemaps.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['custom_basemap_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png",
			'std'     => 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap_attribution'] = array(
			'version' => '1.0',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider X&lt;/a&gt;",
			'std'     => "Copyright ".date('Y')." <a href=&quot;http://xy.com&quot;>Provider X</a>",
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['custom_basemap_tms'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'tms',
			'desc'    => __('If true, inverses Y axis numbering for tiles (turn this on for TMS services).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);	
		$this->settings['custom_basemap_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['custom_basemap_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;",
			'std'     => '&quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['custom_basemap_continuousworld_enabled'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => __('Enable continuousWorld?','lmm'),
			'desc'    => __('If set to true, the tile coordinates will not be wrapped by world width (-180 to 180 longitude) or clamped to lie within world height (-90 to 90). Use this if you use Leaflet for maps that do not reflect the real world (e.g. game, indoor or photo maps).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->settings['custom_basemap_nowrap_enabled'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => __('Enable nowrap?','lmm'),
			'desc'    => __('If set to true, the tiles just will not load outside the world width (-180 to 180 longitude) instead of repeating.','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		/*
		* Custom basemap 2 settings
		*/
		$this->settings['custom_basemap2_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps19" class="lmm-index-links"></a>' . __( 'Custom basemap 2 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['custom_basemap2_helptext'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom basemap', 'lmm').' (custom 2):<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-custom-basemaps.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['custom_basemap2_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png",
			'std'     => 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap2_attribution'] = array(
			'version' => '1.0',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider X&lt;/a&gt;",
			'std'     => "Copyright ".date('Y')." <a href=&quot;http://xy.com&quot;>Provider Y</a>",
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap2_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap2_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['custom_basemap2_tms'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'tms',
			'desc'    => __('If true, inverses Y axis numbering for tiles (turn this on for TMS services).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);			
		$this->settings['custom_basemap2_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['custom_basemap2_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;",
			'std'     => '&quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap2_continuousworld_enabled'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => __('Enable continuousWorld?','lmm'),
			'desc'    => __('If set to true, the tile coordinates will not be wrapped by world width (-180 to 180 longitude) or clamped to lie within world height (-90 to 90). Use this if you use Leaflet for maps that do not reflect the real world (e.g. game, indoor or photo maps).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->settings['custom_basemap2_nowrap_enabled'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => __('Enable nowrap?','lmm'),
			'desc'    => __('If set to true, the tiles just will not load outside the world width (-180 to 180 longitude) instead of repeating.','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		/*
		* Custom basemap 3 settings
		*/
		$this->settings['custom_basemap3_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => '<a name="basemaps20" class="lmm-index-links"></a>' . __( 'Custom basemap 3 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['custom_basemap3_helptext'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom basemap', 'lmm').' (custom 3):<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-default-basemap-custom-basemaps.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['custom_basemap3_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png",
			'std'     => 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap3_attribution'] = array(
			'version' => '1.0',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider XY&lt;/a&gt;",
			'std'     => "Copyright ".date('Y')." <a href=&quot;http://xy.com&quot;>Provider Z</a>",
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap3_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap3_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['custom_basemap3_tms'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => 'tms',
			'desc'    => __('If true, inverses Y axis numbering for tiles (turn this on for TMS services).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);			
		$this->settings['custom_basemap3_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['custom_basemap3_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;",
			'std'     => '&quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap3_continuousworld_enabled'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => __('Enable continuousWorld?','lmm'),
			'desc'    => __('If set to true, the tile coordinates will not be wrapped by world width (-180 to 180 longitude) or clamped to lie within world height (-90 to 90). Use this if you use Leaflet for maps that do not reflect the real world (e.g. game, indoor or photo maps).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->settings['custom_basemap3_nowrap_enabled'] = array(
			'version' => '2.7.1',
			'section' => 'basemaps',
			'title'   => __('Enable nowrap?','lmm'),
			'desc'    => __('If set to true, the tiles just will not load outside the world width (-180 to 180 longitude) instead of repeating.','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);		
		/*===========================================
		*
		*
		* section overlays
		*
		*
		===========================================*/
		/*
		* Available overlays for new markers/layers
		*/
		$this->settings['overlays_available_heading'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '', 
			'desc'    => '<a name="overlays1" class="lmm-index-links"></a>' . __( 'Available overlays for new markers/layers', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['overlays_available_helptext'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select the overlays which should be available in the control box.', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-custom-overlays-available-overlays.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['overlays_custom'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'    => __('Available overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['overlays_custom2'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		$this->settings['overlays_custom3'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		$this->settings['overlays_custom4'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		/*
		* Custom overlay settings
		*/
		$this->settings['overlays_heading_custom'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '', 
			'desc'    => '<a name="overlays2" class="lmm-index-links"></a>' . __( 'Custom overlay settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['overlays_custom_helptext'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom overlay', 'lmm').':<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-overlays-custom.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['overlays_custom_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => __('OGD Vienna addresses','lmm'),
			'type'    => 'text',
			'section' => 'overlays'
		);		
		
		$this->settings['overlays_custom_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __('For example','lmm'). ": http://{s}.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png",
			'std'     => 'http://{s}.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom_attribution'] = array(
			'version' => '1.1',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Addresses: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>data.wien.gv.at</a>)',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '19',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom_opacity'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Opacity', 'lmm' ),
			'desc'    => __('The opacity of the tile layer.','lmm'),
			'std'     => '1.0',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['overlays_custom_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;",
			'std'     => '&quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;',
			'type'    => 'text',
			'section' => 'overlays'
		);
		/*
		* Custom overlay 2 settings
		*/
		$this->settings['overlays_heading_custom2'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '', 
			'desc'    => '<a name="overlays3" class="lmm-index-links"></a>' . __( 'Custom overlay 2 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['overlays_custom2_helptext'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom overlay', 'lmm').' 2:<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-overlays-custom2.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['overlays_custom2_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Custom2',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		
		$this->settings['overlays_custom2_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __('For example','lmm'). ": http://{s}.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png",
			'std'     => 'http://{s}.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom2_attribution'] = array(
			'version' => '1.1',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Addresses: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>data.wien.gv.at</a>)',
			'type'    => 'text',
			'section' => 'overlays'
		);	
		$this->settings['overlays_custom2_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom2_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '17',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom2_opacity'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Opacity', 'lmm' ),
			'desc'    => __('The opacity of the tile layer.','lmm'),
			'std'     => '1.0',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom2_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['overlays_custom2_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;",
			'std'     => '&quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;',
			'type'    => 'text',
			'section' => 'overlays'
		);
		/*
		* Custom overlay 3 settings
		*/
		$this->settings['overlays_heading_custom3'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '', 
			'desc'    => '<a name="overlays4" class="lmm-index-links"></a>' . __( 'Custom overlay 3 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['overlays_custom3_helptext'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom overlay', 'lmm').' 3:<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-overlays-custom3.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['overlays_custom3_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Custom3',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom3_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://maps.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png",
			'std'     => 'http://maps.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom3_attribution'] = array(
			'version' => '1.1',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Addresses: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>data.wien.gv.at</a>)',
			'type'    => 'text',
			'section' => 'overlays'
		);	
		$this->settings['overlays_custom3_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom3_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '17',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom3_opacity'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Opacity', 'lmm' ),
			'desc'    => __('The opacity of the tile layer.','lmm'),
			'std'     => '1.0',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom3_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['overlays_custom3_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;",
			'std'     => '&quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;',
			'type'    => 'text',
			'section' => 'overlays'
		);
		/*
		* Custom overlay 4 settings
		*/
		$this->settings['overlays_heading_custom4'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '', 
			'desc'    => '<a name="overlays5" class="lmm-index-links"></a>' . __( 'Custom overlay 4 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['overlays_custom4_helptext'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom overlay', 'lmm').' 4:<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-overlays-custom4.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['overlays_custom4_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Custom4',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		
		$this->settings['overlays_custom4_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://maps.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png",
			'std'     => 'http://maps.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom4_attribution'] = array(
			'version' => '1.1',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Addresses: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>data.wien.gv.at</a>)',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom4_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom4_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '17',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom4_opacity'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Opacity', 'lmm' ),
			'desc'    => __('The opacity of the tile layer.','lmm'),
			'std'     => '1.0',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom4_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['overlays_custom4_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;",
			'std'     => '&quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;',
			'type'    => 'text',
			'section' => 'overlays'
		);
		
		/*===========================================
		*
		*
		* section wms
		*
		*
		===========================================*/
		/*
		* Available WMS layers for new markers/layers
		*/
		$this->settings['wms_available_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms1" class="lmm-index-links"></a>' . __( 'Available WMS layers for new markers/layers', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_available_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select the WMS layers which should be available when creating new markers/layers', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-wms-available-wms-layers.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['wms_wms_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Available WMS layers','lmm'),
			'desc'    => 'WMS 1',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms2_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 2',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms3_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 3',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms4_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 4',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms5_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 5',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms6_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 6',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms7_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 7',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms8_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 8',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms9_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 9',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms10_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 10',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		/*
		* WMS layer settings
		*/
		$this->settings['wms_wms_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms2" class="lmm-index-links"></a>' . __( 'WMS layer 1 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_wms_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://data.wien.gv.at/katalog/wc-anlagen.html&quot; target=&quot;_blank&quot;>OGD Vienna - Public Toilets</a>' 
		);
		$this->settings['wms_wms_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://data.wien.gv.at/daten/wms' 
		);
		$this->settings['wms_wms_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => 'OEFFWCOGD' 
		);
		$this->settings['wms_wms_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/gif' 
		);		
		$this->settings['wms_wms_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->settings['wms_wms_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.1.1' 
		);
		$this->settings['wms_wms_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>http://data.wien.gv.at</a>)' 
		);		
		$this->settings['wms_wms_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);		
		$this->settings['wms_wms_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		$this->settings['wms_wms_kml_helptext'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '<strong>' . __('KML settings','lmm') . '</strong>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['wms_wms_kml_support'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);	
		$this->settings['wms_wms_kml_href'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://data.wien.gv.at/daten/geoserver/ows?version=1.3.0&service=WMS&request=GetMap&crs=EPSG:4326&bbox=48.10,16.16,48.34,16.59&width=1&height=1&layers=ogdwien:OEFFWCOGD&styles=&format=application/vnd.google-earth.kml+xml' 
		);	
		$this->settings['wms_wms_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);	
		$this->settings['wms_wms_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30' 
		);		
		$this->settings['wms_wms_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);			
		$this->settings['wms_wms_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1' 
		);
		
		/*
		* WMS layer 2 settings
		*/
		$this->settings['wms_wms2_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms3" class="lmm-index-links"></a>' . __( 'WMS layer 2 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_wms2_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms2_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://data.wien.gv.at/katalog/aufzuege.html&quot; target=&quot;_blank&quot;>OGD Vienna - Elevators at stations</a>' 
		);
		$this->settings['wms_wms2_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://data.wien.gv.at/daten/wms' 
		);
		$this->settings['wms_wms2_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => 'AUFZUGOGD' 
		);
		$this->settings['wms_wms2_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms2_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/gif' 
		);		
		$this->settings['wms_wms2_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->settings['wms_wms2_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.1.1' 
		);	
		$this->settings['wms_wms2_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>http://data.wien.gv.at</a>)' 
		);		
		$this->settings['wms_wms2_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms2_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);		
		$this->settings['wms_wms2_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms2_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		$this->settings['wms_wms2_kml_helptext'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '<strong>' . __('KML settings','lmm') . '</strong>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['wms_wms2_kml_support'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);	
		$this->settings['wms_wms2_kml_href'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://data.wien.gv.at/daten/geoserver/ows?version=1.3.0&service=WMS&request=GetMap&crs=EPSG:4326&bbox=48.10,16.16,48.34,16.59&width=1&height=1&layers=ogdwien:AUFZUGOGD&styles=&format=application/vnd.google-earth.kml+xml' 
		);	
		$this->settings['wms_wms2_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);	
		$this->settings['wms_wms2_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30' 
		);		
		$this->settings['wms_wms2_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);			
		$this->settings['wms_wms2_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1' 
		);		
		/*
		* WMS layer 3 settings
		*/
		$this->settings['wms_wms3_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms4" class="lmm-index-links"></a>' . __( 'WMS layer 3 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_wms3_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms3_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/7&quot; target=&quot;_blank&quot;>EEA - CO emissions from road transport</a>' 
		);
		$this->settings['wms_wms3_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/WMSServer' 
		);
		$this->settings['wms_wms3_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '24' 
		);
		$this->settings['wms_wms3_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms3_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms3_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->settings['wms_wms3_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms3_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms3_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=1' 
		);		
		$this->settings['wms_wms3_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);		
		$this->settings['wms_wms3_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms3_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		$this->settings['wms_wms3_kml_helptext'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '<strong>' . __('KML settings','lmm') . '</strong>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['wms_wms3_kml_support'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);	
		$this->settings['wms_wms3_kml_href'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/generatekml?docName=&l%3A7=on&layers=7&layerOptions=nonComposite' 
		);	
		$this->settings['wms_wms3_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);	
		$this->settings['wms_wms3_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30' 
		);		
		$this->settings['wms_wms3_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);			
		$this->settings['wms_wms3_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1' 
		);	
		/*
		* WMS layer 4 settings
		*/
		$this->settings['wms_wms4_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms5" class="lmm-index-links"></a>' . __( 'WMS layer 4 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_wms4_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms4_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Land/CLC2006_Dyna_WM/MapServer&quot; target=&quot;_blank&quot;>EEA - Agricultural areas</a>' 
		);
		$this->settings['wms_wms4_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2006_Dyna_WM/MapServer/WMSServer' 
		);
		$this->settings['wms_wms4_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '10' 
		);
		$this->settings['wms_wms4_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms4_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms4_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->settings['wms_wms4_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);
		$this->settings['wms_wms4_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms4_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms4_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2000_Cach_WM/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=11'			
		);		
		$this->settings['wms_wms4_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms4_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		$this->settings['wms_wms4_kml_helptext'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '<strong>' . __('KML settings','lmm') . '</strong>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['wms_wms4_kml_support'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);	
		$this->settings['wms_wms4_kml_href'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Land/CLC2006_Dyna_WM/MapServer/generatekml?docName=&l%3A5=on&layers=5&layerOptions=nonComposite' 
		);	
		$this->settings['wms_wms4_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);	
		$this->settings['wms_wms4_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30' 
		);		
		$this->settings['wms_wms4_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);			
		$this->settings['wms_wms4_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1' 
		);	
		/*
		* WMS layer 5 settings
		*/
		$this->settings['wms_wms5_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms6" class="lmm-index-links"></a>' . __( 'WMS layer 5 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_wms5_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms5_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Noise/Noise_Dyna_LAEA/MapServer/460&quot; target=&quot;_blank&quot;>EEA - Airport Annual Traffic</a>' 
		);
		$this->settings['wms_wms5_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Noise/Noise_Dyna_LAEA/MapServer/WMSServer' 
		);
		$this->settings['wms_wms5_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '8' 
		);
		$this->settings['wms_wms5_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms5_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms5_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->settings['wms_wms5_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms5_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms5_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms5_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Noise/Noise_Dyna_LAEA/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=8'			
		);		
		$this->settings['wms_wms5_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms5_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		$this->settings['wms_wms5_kml_helptext'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '<strong>' . __('KML settings','lmm') . '</strong>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['wms_wms5_kml_support'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);	
		$this->settings['wms_wms5_kml_href'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Noise/Noise_Dyna_LAEA/MapServer/generatekml?docName=&l%3A460=on&layers=460&layerOptions=nonComposite' 
		);	
		$this->settings['wms_wms5_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);	
		$this->settings['wms_wms5_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30' 
		);		
		$this->settings['wms_wms5_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);			
		$this->settings['wms_wms5_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1' 
		);				
		/*
		* WMS layer 6 settings
		*/
		$this->settings['wms_wms6_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms7" class="lmm-index-links"></a>' . __( 'WMS layer 6 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_wms6_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms6_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Land/CLC2006_Dyna_WM/MapServer&quot; target=&quot;_blank&quot;>EEA - WaterBodies</a>' 
		);
		$this->settings['wms_wms6_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2006_Dyna_WM/MapServer/WMSServer' 
		);
		$this->settings['wms_wms6_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '2' 
		);
		$this->settings['wms_wms6_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms6_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms6_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->settings['wms_wms6_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms6_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms6_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms6_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2006_Dyna_WM/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=2'			
		);		
		$this->settings['wms_wms6_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms6_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		$this->settings['wms_wms6_kml_helptext'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '<strong>' . __('KML settings','lmm') . '</strong>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['wms_wms6_kml_support'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);	
		$this->settings['wms_wms6_kml_href'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Land/CLC2006_Dyna_WM/MapServer/generatekml?docName=&l%3A14=on&layers=14&layerOptions=nonComposite' 
		);	
		$this->settings['wms_wms6_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);	
		$this->settings['wms_wms6_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30' 
		);		
		$this->settings['wms_wms6_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);			
		$this->settings['wms_wms6_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1' 
		);		
		/*
		* WMS layer 7 settings
		*/
		$this->settings['wms_wms7_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms8" class="lmm-index-links"></a>' . __( 'WMS layer 7 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_wms7_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms7_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Water/RiverAndLakes_Dyna_WM/MapServer&quot; target=&quot;_blank&quot;>EEA - Mean annual nitrates in rivers 2008</a>' 
		);
		$this->settings['wms_wms7_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Water/RiverAndLakes_Dyna_WM/MapServer/WMSServer' 
		);
		$this->settings['wms_wms7_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '14' 
		);
		$this->settings['wms_wms7_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms7_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms7_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->settings['wms_wms7_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms7_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms7_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms7_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Water/RiverAndLakes_Dyna_WM/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=14'			
		);		
		$this->settings['wms_wms7_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms7_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		$this->settings['wms_wms7_kml_helptext'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '<strong>' . __('KML settings','lmm') . '</strong>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['wms_wms7_kml_support'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);	
		$this->settings['wms_wms7_kml_href'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Water/RiverAndLakes_Dyna_WM/MapServer/generatekml?docName=&l%3A9=on&layers=9&layerOptions=nonComposite' 
		);	
		$this->settings['wms_wms7_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);	
		$this->settings['wms_wms7_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30' 
		);		
		$this->settings['wms_wms7_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);			
		$this->settings['wms_wms7_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1' 
		);
		/*
		* WMS layer 8 settings
		*/
		$this->settings['wms_wms8_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms9" class="lmm-index-links"></a>' . __( 'WMS layer 8 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_wms8_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms8_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Reports2010/Reports2008_Dyna_WGS84/MapServer&quot; target=&quot;_blank&quot;>EEA - Temperature Change</a>' 
		);
		$this->settings['wms_wms8_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Reports2010/Reports2008_Dyna_WGS84/MapServer/WMSServer' 
		);
		$this->settings['wms_wms8_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '5' 
		);
		$this->settings['wms_wms8_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms8_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms8_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->settings['wms_wms8_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms8_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms8_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms8_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://cow6/ArcGIS/services/Reports2010/Reports2008_Dyna_WGS84/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=5'			
		);		
		$this->settings['wms_wms8_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms8_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		$this->settings['wms_wms8_kml_helptext'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '<strong>' . __('KML settings','lmm') . '</strong>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['wms_wms8_kml_support'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);	
		$this->settings['wms_wms8_kml_href'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Reports2010/Reports2008_Dyna_WGS84/MapServer/generatekml?docName=&l%3A26=on&layers=26&layerOptions=nonComposite' 
		);	
		$this->settings['wms_wms8_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);	
		$this->settings['wms_wms8_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30' 
		);		
		$this->settings['wms_wms8_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);			
		$this->settings['wms_wms8_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1' 
		);
		/*
		* WMS layer 9 settings
		*/
		$this->settings['wms_wms9_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms10" class="lmm-index-links"></a>' . __( 'WMS layer 9 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_wms9_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms9_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Bio/CDDA_Dyna_WGS84/MapServer&quot; target=&quot;_blank&quot;>EEA - Common Database on Designated Areas</a>' 
		);
		$this->settings['wms_wms9_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Bio/CDDA_Dyna_WGS84/MapServer/WMSServer' 
		);
		$this->settings['wms_wms9_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '0' 
		);
		$this->settings['wms_wms9_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms9_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms9_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->settings['wms_wms9_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms9_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms9_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms9_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Bio/CDDA_Dyna_WGS84/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=0'			
		);		
		$this->settings['wms_wms9_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms9_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		$this->settings['wms_wms9_kml_helptext'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '<strong>' . __('KML settings','lmm') . '</strong>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['wms_wms9_kml_support'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);	
		$this->settings['wms_wms9_kml_href'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Bio/CDDA_Dyna_WGS84/MapServer/generatekml?docName=&l%3A2=on&layers=2&layerOptions=nonComposite' 
		);	
		$this->settings['wms_wms9_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);	
		$this->settings['wms_wms9_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30' 
		);		
		$this->settings['wms_wms9_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);			
		$this->settings['wms_wms9_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1' 
		);
		/*
		* WMS layer 10 settings
		*/
		$this->settings['wms_wms10_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => '<a name="wms11" class="lmm-index-links"></a>' . __( 'WMS layer 10 settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['wms_wms10_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms10_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Noise/Noise_Dyna_LAEA/MapServer&quot; target=&quot;_blank&quot;>EEA - Road noise Austria</a>' 
		);
		$this->settings['wms_wms10_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Noise/Noise_Dyna_LAEA/MapServer/WMSServer' 
		);
		$this->settings['wms_wms10_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '247' 
		);
		$this->settings['wms_wms10_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms10_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms10_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->settings['wms_wms10_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms10_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms10_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms10_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Noise/Noise_Dyna_LAEA/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=247'			
		);		
		$this->settings['wms_wms10_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms10_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);											
		$this->settings['wms_wms10_kml_helptext'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '<strong>' . __('KML settings','lmm') . '</strong>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['wms_wms10_kml_support'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);	
		$this->settings['wms_wms10_kml_href'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Noise/Noise_Dyna_LAEA/MapServer/generatekml?docName=&l%3A222=on&layers=222&layerOptions=nonComposite' 
		);	
		$this->settings['wms_wms10_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);	
		$this->settings['wms_wms10_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30' 
		);		
		$this->settings['wms_wms10_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);			
		$this->settings['wms_wms10_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'section' => 'wms',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1' 
		);
		/*===========================================
		*
		*
		* section marker defaults
		*
		*
		===========================================*/	
		/*
		* Default values for new marker maps
		*/
		$this->settings['defaults_marker_heading'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '', 
			'desc'    =>  '<a name="defaults_marker1" class="lmm-index-links"></a>' . __( 'Default values for new marker maps', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['defaults_marker_helptext1'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Will be used when creating a new marker. All values can be changed afterwards on each marker.', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-marker-defaults.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['defaults_marker_lat'] = array(
			'version' => '1.0',
			'title'   => __( 'Latitude', 'lmm' ),
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '48.216038',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_lon'] = array(
			'version' => '1.0',
			'title'   => __( 'Longitude', 'lmm' ),
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '16.378984',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_zoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Zoom', 'lmm' ),
			'desc'    => '',
			'std'     => '11',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_mapwidth'] = array(
			'version' => '1.0',
			'title'   => __( 'Map width', 'lmm' ),
			'desc'    => '',
			'std'     => '640',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_mapwidthunit'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Map width unit','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'px',
			'choices' => array(
				'px' => 'px',
				'%' => '%'
			)
		);
		$this->settings['defaults_marker_mapheight'] = array(
			'version' => '1.0',
			'title'   => __( 'Map height', 'lmm' ) . ' (px)',
			'desc'    => '',
			'std'     => '480',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_openpopup'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Open popup by default','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array(
				'0' => __('disabled','lmm'),
				'1' => __('enabled','lmm')
			)
		);
		$this->settings['defaults_marker_controlbox'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Basemap/layer controlbox on frontend','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('collapsed (except on mobiles)','lmm'),
				'2' => __('expanded','lmm')
			)
		);		
		// defaults_marker - which overlays are active by default?
		$this->settings['defaults_marker_overlays_custom_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'    => __('Checked overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_marker_overlays_custom2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		$this->settings['defaults_marker_overlays_custom3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_marker_overlays_custom4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_marker_panel'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Panel for displaying marker name and API URLs on top of map','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'1' => __('show','lmm'),
				'0' => __('hide','lmm'),
			)
		);	
		// defaults_marker - active API links in panel
		$this->settings['defaults_marker_panel_directions'] = array(
			'version' => '1.4',
			'section' => 'defaults_marker',
			'title'    => __('Visible API links in panel','lmm'),
			'desc'    => __('Directions','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-car.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_marker_panel_kml'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'    => '',
			'desc'    => 'KML <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_marker_panel_fullscreen'] = array(
			'version' => '1.1',
			'section' => 'defaults_marker',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_marker_panel_qr_code'] = array(
			'version' => '1.1',
			'section' => 'defaults_marker',
			'title'    => '',
			'desc'    => __('QR code','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_marker_panel_geojson'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => 'GeoJSON <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_marker_panel_georss'] = array(
			'version' => '1.2',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => 'GeoRSS <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_marker_panel_wikitude'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => 'Wikitude <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_marker_panel_background_color'] = array(
			'version' => '1.0',
			'title'   => __( 'Panel background color', 'lmm' ),
			'desc'    => 'Please use hexadecimal color values',
			'std'     => '#efefef',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);		
		$this->settings['defaults_marker_panel_paneltext_css'] = array(
			'version' => '1.0',
			'title'   => __( 'Panel text css', 'lmm' ),
			'desc'    => '',
			'std'     => 'font-weight:bold;color:#373737;',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		// defaults_marker - which WMS layers are active by default?
		$this->settings['defaults_marker_wms_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'    => __('Checked WMS layers','lmm'),
			'desc'    => __('WMS 1','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_marker_wms2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_marker_wms3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms5_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 5','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms6_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 6','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms7_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 7','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms8_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 8','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms9_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 9','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms10_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 10','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		/*
		* Default values for marker icons
		*/
		$this->settings['defaults_marker_icon_heading'] = array(
			'version' => '2.7.1',
			'section' => 'defaults_marker',
			'title'   => '', 
			'desc'    => '<a name="defaults_marker2" class="lmm-index-links"></a>' . __( 'Default values for marker icons', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['defaults_marker_icon_helptext1'] = array(
			'version' => '2.7.1',
			'section' => 'defaults_marker',
			'std'     => '', 
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->settings['defaults_marker_icon_url'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Icons URL', 'lmm' ),
			'desc'    => __( 'Icons copied to this directory will automatically be available when creating or editing marker maps (cannot be changed)', 'lmm' ),
			'std'     => LEAFLET_PLUGIN_ICONS_URL,
			'type'    => 'text-readonly',
			'section' => 'defaults_marker'
		);		
		$this->settings['defaults_marker_icon_dir'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Icons directory', 'lmm' ),
			'desc'    => __( 'Directory on server where icons are stored - needed especially for WordPress Multisite blogs (cannot be changed)', 'lmm' ),
			'std'     => LEAFLET_PLUGIN_ICONS_DIR,
			'type'    => 'text-readonly',
			'section' => 'defaults_marker'
		);	
		$this->settings['defaults_marker_icon'] = array(
			'version' => '1.8',
			'title'   => __( 'Default icon', 'lmm' ),
			'desc'    => sprintf(__( 'If you want to use another icon than the blue pin (<img src="%sleaflet-dist/images/marker.png">), please enter the file name of the icon (located in the directory %s) in the form field - e.g. smiley_happy.png', 'lmm' ),LEAFLET_PLUGIN_URL,LEAFLET_PLUGIN_ICONS_URL),
			'std'     => '',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_title'] = array(
			'version' => '2.7.1',
			'section' => 'defaults_marker',
			'title'   => 'title' . '<br/><img src="' . LEAFLET_PLUGIN_URL . 'img/help-marker-title.jpg">',
			'desc'    => __('Show marker name for the browser tooltip that appear on marker hover (tooltip is always hidden if marker name is empty).','lmm'),
			'type'    => 'radio',
			'std'     => 'show',
			'choices' => array(
				'show' => __('show','lmm'),
				'hide' => __('hide','lmm')
			)
		);
		$this->settings['defaults_marker_icon_opacity'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Opacity', 'lmm' ),
			'desc'    => __( 'The opacity of the markers.', 'lmm' ),
			'std'     => '1.0',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_helptext2'] = array(
			'version' => '2.7.1',
			'section' => 'defaults_marker',
			'std'     => '', 
			'title'   => '', 
			'desc'    => '<strong>' . __('Only change the values below if you are not using marker or shadow icons from the <a href="http://mapicons.nicolasmollet.com" target="_blank">Map Icons Collection</a>!','lmm') . '</strong>',
			'type'    => 'helptext'
		);
		$this->settings['defaults_marker_icon_iconsize_x'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Icon size', 'lmm' ) . ' (x)',
			'desc'    => __( 'Width of the icons in pixel', 'lmm' ),
			'std'     => '32',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_iconsize_y'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Icon size', 'lmm' ) . ' (y)',
			'desc'    => __( 'Height of the icons in pixel', 'lmm' ),
			'std'     => '37',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_iconanchor_x'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Icon anchor', 'lmm' ) . ' (x)',
			'desc'    => __( 'The x-coordinates of the "tip" of the icons (relative to its top left corner).', 'lmm' ),
			'std'     => '17',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_iconanchor_y'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Icon anchor', 'lmm' ) . ' (y)',
			'desc'    => __( 'The y-coordinates of the "tip" of the icons (relative to its top left corner).', 'lmm' ),
			'std'     => '36',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_popupanchor_x'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Popup anchor', 'lmm' ) . ' (x)',
			'desc'    => __( 'The x-coordinates of the popup anchor (relative to its top left corner)', 'lmm' ),
			'std'     => '-1',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_popupanchor_y'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Popup anchor', 'lmm' ) . ' (y)',
			'desc'    => __( 'The y-coordinates of the popup anchor (relative to its top left corner)', 'lmm' ),
			'std'     => '-32',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);		
		$this->settings['defaults_marker_icon_shadow_url'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Shadow URL', 'lmm' ),
			'desc'    => __( 'The URL to the icons shadow image. If not specified, no shadow image will be created. Default shadow icon:', 'lmm' ) . '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker-shadow.png">',
			'std'     => LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker-shadow.png',
			'type'    => 'text-deletable',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_shadowsize_x'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Shadow size', 'lmm' ) . ' (x)',
			'desc'    => __( 'Width of the shadow icon in pixel', 'lmm' ),
			'std'     => '41',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_shadowsize_y'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Shadow size', 'lmm' ) . ' (y)',
			'desc'    => __( 'Height of the shadow icon in pixel', 'lmm' ),
			'std'     => '41',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_shadowanchor_x'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Shadow anchor', 'lmm' ) . ' (x)',
			'desc'    => __( 'The x-coordinates of the "tip" of the shadow icon (relative to its top left corner)', 'lmm' ),
			'std'     => '16',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_icon_shadowanchor_y'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Shadow anchor', 'lmm' ) . ' (y)',
			'desc'    => __( 'The y-coordinates of the "tip" of the shadow icon (relative to its top left corner)', 'lmm' ),
			'std'     => '43',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		/*
		* Default values for marker popups
		*/
		$this->settings['defaults_marker_popups_heading'] = array(
			'version' => '2.7.1',
			'section' => 'defaults_marker',
			'title'   => '', 
			'desc'    => '<a name="defaults_marker3" class="lmm-index-links"></a>' . __( 'Default values for marker popups', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['defaults_marker_popups_helptext1'] = array(
			'version' => '2.7.1',
			'section' => 'defaults_marker',
			'std'     => '', 
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->settings['defaults_marker_popups_maxwidth'] = array(
			'version' => '2.7.1',
			'title'   => 'maxWidth (px)',
			'desc'    => __( 'Maximum width of popups in pixel', 'lmm' ),
			'std'     => '300',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_popups_minwidth'] = array(
			'version' => '2.7.1',
			'title'   => 'minWidth (px)',
			'desc'    => __( 'Minimum width of popups in pixel', 'lmm' ),
			'std'     => '250',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_popups_maxheight'] = array(
			'version' => '2.7.1',
			'title'   => 'maxHeight (px)',
			'desc'    => __( 'If set, creates a scrollable container of the given height in pixel inside popups if its content exceeds it.', 'lmm' ),
			'std'     => '160',
			'type'    => 'text-deletable',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_popups_image_max_width'] = array(
			'version' => '2.7.1',
			'title'   => __('maximum image width (px)','lmm'),
			'desc'    => __( 'Reduce image width in popups automatically to the given value in pixel (only if is wider). The height of the images gets reduced by the according ratio automatically (this feature only works if your theme supports the wp_head()-hook).', 'lmm' ),
			'std'     => '230',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_popups_autopan'] = array(
			'version' => '2.7.1',
			'section' => 'defaults_marker',
			'title'   => 'autoPan',
			'desc'    => __('Set it to false if you do not want the map to do panning animation to fit the opened popup.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->settings['defaults_marker_popups_closebutton'] = array(
			'version' => '2.7.1',
			'section' => 'defaults_marker',
			'title'   => 'closeButton',
			'desc'    => __('Controls the presence of a close button in popups.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->settings['defaults_marker_popups_autopanpadding_x'] = array(
			'version' => '2.7.1',
			'title'   => 'autoPanPadding (x)',
			'desc'    => __( 'The x-coordinates of the margin between popups and the edges of the map view after autopanning was performed.', 'lmm' ),
			'std'     => '5',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_popups_autopanpadding_y'] = array(
			'version' => '2.7.1',
			'title'   => 'autoPanPadding (y)',
			'desc'    => __( 'The y-coordinates of the margin between popups and the edges of the map view after autopanning was performed.', 'lmm' ),
			'std'     => '5',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);		
		/*
		* Default values for markers added directly
		*/
		$this->settings['defaults_marker_shortcode_heading'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '', 
			'desc'    => '<a name="defaults_marker4" class="lmm-index-links"></a>' . __( 'Default values for markers added directly', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['defaults_marker_shortcode_helptext'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'You can also add markers directly to posts or pages without having to save them to your database previously. You just have to use the shortcode with the attributes mlat and mlon (e.g. <strong>[mapsmarker mlat="48.216038" mlon="16.378984"]</strong>).', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-marker-direct.jpg" /><br/><br/>' . __('Defaults values for markers added directly:','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['defaults_marker_shortcode_basemap'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Default basemap','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'osm_mapnik',
			'choices' => array(
				'osm_mapnik' => __('OpenStreetMap (Mapnik, max zoom 18)','lmm'),
				'mapquest_osm' => __('MapQuest (OSM, max zoom 18)','lmm'),
				'mapquest_aerial' => __('MapQuest (Aerial, max zoom 12 globally, 12+ in the United States)','lmm'),
				'googleLayer_roadmap' => __('Google Maps (Roadmap)','lmm'),
				'googleLayer_satellite' => __('Google Maps (Satellite)','lmm'),
				'googleLayer_hybrid' => __('Google Maps (Hybrid)','lmm'),
				'googleLayer_terrain' => __('Google Maps (Terrain)','lmm'),
				'bingaerial' => __('Bing Maps (Aerial)','lmm') . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="http://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingaerialwithlabels' => __('Bing Maps (Aerial+Labels)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="http://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingroad' => __('Bing Maps (Road)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="http://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'ogdwien_basemap' => __('OGD Vienna basemap (max zoom 19)','lmm'),
				'ogdwien_satellite' => __('OGD Vienna satellite (max zoom 19)','lmm'),
				'cloudmade' => 'Cloudmade',
				'cloudmade2' => 'Cloudmade 2',
				'cloudmade3' => 'Cloudmade 3',
				'custom_basemap' => __('Custom basemap','lmm'),
				'custom_basemap2' => __('Custom basemap 2','lmm'),
				'custom_basemap3' => __('Custom basemap 3','lmm')
			)
		);
		$this->settings['defaults_marker_shortcode_zoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Zoom', 'lmm' ),
			'desc'    => '',
			'std'     => '11',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_shortcode_mapwidth'] = array(
			'version' => '1.0',
			'title'   => __( 'Map width', 'lmm' ),
			'desc'    => '',
			'std'     => '640',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_shortcode_mapwidthunit'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Map width unit','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'px',
			'choices' => array(
				'px' => 'px',
				'%' => '%'
			)
		);
		$this->settings['defaults_marker_shortcode_mapheight'] = array(
			'version' => '1.0',
			'title'   => __( 'Map height', 'lmm' ) . ' (px)',
			'desc'    => '',
			'std'     => '480',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_shortcode_controlbox'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Basemap/layer controlbox on frontend','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('collapsed (except on mobiles)','lmm'),
				'2' => __('expanded','lmm')
			)
		);		
		// defaults_marker - which overlays are active by default?
		$this->settings['defaults_marker_shortcode_overlays_custom_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'    => __('Checked overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_marker_shortcode_overlays_custom2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		$this->settings['defaults_marker_shortcode_overlays_custom3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		$this->settings['defaults_marker_shortcode_overlays_custom4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		// defaults_marker shortcode - which WMS layers are active by default?
		$this->settings['defaults_marker_shortcode_wms_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'    => __('Checked WMS layers','lmm'),
			'desc'    => __('WMS 1','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_marker_shortcode_wms2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_marker_shortcode_wms3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms5_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 5','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms6_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 6','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms7_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 7','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms8_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 8','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms9_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 9','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms10_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 10','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		
		/*===========================================
		*
		*
		* section layer defaults
		*
		*
		===========================================*/		
		/*
		* Default values for new layer maps
		*/
		$this->settings['defaults_layer_heading'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '', 
			'desc'    => '<a name="defaults_layer1" class="lmm-index-links"></a>' . __( 'Default values for new layer maps', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['defaults_layer_helptext1'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Will be used when creating a new layer. All values can be changed afterwards on each layer.', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'img/help-layer-defaults.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['defaults_layer_lat'] = array(
			'version' => '1.0',
			'title'   => __( 'Latitude', 'lmm' ),
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '48.216038',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		$this->settings['defaults_layer_lon'] = array(
			'version' => '1.0',
			'title'   => __( 'Longitude', 'lmm' ),
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '16.378984',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		$this->settings['defaults_layer_zoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Zoom', 'lmm' ),
			'desc'    => '',
			'std'     => '11',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		$this->settings['defaults_layer_mapwidth'] = array(
			'version' => '1.0',
			'title'   => __( 'Map width', 'lmm' ),
			'desc'    => '',
			'std'     => '640',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		$this->settings['defaults_layer_mapwidthunit'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => __('Map width unit','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'px',
			'choices' => array(
				'px' => 'px',
				'%' => '%'
			)
		);
		$this->settings['defaults_layer_mapheight'] = array(
			'version' => '1.0',
			'title'   => __( 'Map height', 'lmm' ) . ' (px)',
			'desc'    => '',
			'std'     => '480',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		$this->settings['defaults_layer_controlbox'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => __('Basemap/layer controlbox on frontend','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('collapsed (except on mobiles)','lmm'),
				'2' => __('expanded','lmm')
			)
		);		
		// defaults_layer - which overlays are active by default?
		$this->settings['defaults_layer_overlays_custom_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'    => __('Checked overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_layer_overlays_custom2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_layer_overlays_custom3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_layer_overlays_custom4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_layer_panel'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => __('Panel for displaying layer name and API URLs on top of map','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'1' => __('show','lmm'),
				'0' => __('hide','lmm'),
			)
		);	
		// defaults_layer - active API links in panel
		$this->settings['defaults_layer_panel_kml'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'    => __('Visible API links in panel','lmm'),
			'desc'    => 'KML <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_panel_fullscreen'] = array(
			'version' => '1.1',
			'section' => 'defaults_layer',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_layer_panel_qr_code'] = array(
			'version' => '1.1',
			'section' => 'defaults_layer',
			'title'    => '',
			'desc'    => __('QR code','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_layer_panel_geojson'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => 'GeoJSON <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png"> (' . __('not available on multi layer maps','lmm') . ')',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_panel_georss'] = array(
			'version' => '1.2',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => 'GeoRSS <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_panel_wikitude'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => 'Wikitude <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_layer_panel_background_color'] = array(
			'version' => '1.0',
			'title'   => __( 'Panel background color', 'lmm' ),
			'desc'    => 'Please use hexadecimal color values',
			'std'     => '#efefef',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);		
		$this->settings['defaults_layer_panel_paneltext_css'] = array(
			'version' => '1.0',
			'title'   => __( 'Panel text css', 'lmm' ),
			'desc'    => '',
			'std'     => 'font-weight:bold;color:#373737;',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);		
		// defaults_layer - which WMS layers are active by default?
		$this->settings['defaults_layer_wms_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'    => __('Checked WMS layers','lmm'),
			'desc'    => __('WMS 1','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_layer_wms2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_layer_wms3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms5_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 5','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms6_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 6','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms7_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 7','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms8_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 8','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms9_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 9','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms10_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 10','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		/*
		* List marker settings
		*/
		$this->settings['defaults_layer_listmarkers_helptext'] = array(
			'version' => '1.5',
			'section' => 'defaults_layer',
			'std'     => '', 
			'title'   => '<a name="defaults_layer2" class="lmm-index-links"></a>' . '<strong>' . __('List marker settings','lmm') . '</strong>' . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->settings['defaults_layer_listmarkers'] = array(
			'version' => '1.5',
			'section' => 'defaults_layer',
			'title'   => __('Display a list of markers under the map','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('no','lmm'),
				'1' => __('yes','lmm')
			)
		);			
		$this->settings['defaults_layer_listmarkers_show_icon'] = array(
			'version' => '2.6',
			'section' => 'defaults_layer',
			'title'    => __('Marker attributes to display in list','lmm'),
			'desc'    => __('Icon','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_listmarkers_show_markername'] = array(
			'version' => '2.6',
			'section' => 'defaults_layer',
			'title'    => '',
			'desc'    => __('Marker name','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_listmarkers_show_popuptext'] = array(
			'version' => '2.6',
			'section' => 'defaults_layer',
			'title'    => '',
			'desc'    => __('Popup text','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_listmarkers_order_by'] = array(
			'version' => '1.5',
			'section' => 'defaults_layer',
			'title'   => __('Order list of markers by','lmm'),
			'desc'    =>  __('(not available on multi layer maps)','lmm'),
			'type'    => 'radio',
			'std'     => 'm.id',
			'choices' => array(
				'm.id' => 'ID',
				'm.markername' => __('marker name','lmm'),
				'm.createdon' => __('created on','lmm'),
				'm.updatedon' => __('updated on','lmm')
			)
		);
		$this->settings['defaults_layer_listmarkers_sort_order'] = array(
			'version' => '1.5',
			'section' => 'defaults_layer',
			'title'   => __('Sort order','lmm'),
			'desc'    =>  __('(not available on multi layer maps)','lmm'),
			'type'    => 'radio',
			'std'     => 'ASC',
			'choices' => array(
				'ASC' => __('ascending','lmm'),
				'DESC' => __('descending','lmm')
			)
		);
		$this->settings['defaults_layer_listmarkers_limit'] = array(
			'version' => '1.7',
			'title'   => __( 'Limit', 'lmm' ),
			'desc'    => __( 'maximum number of markers to display in the list', 'lmm' ) . ' ' .  __('(not available on multi layer maps)','lmm'),
			'std'     => '100',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		// defaults_layer - active API links in markers list
		$this->settings['defaults_layer_listmarkers_api_directions'] = array(
			'version' => '2.1',
			'section' => 'defaults_layer',
			'title'    => __('Visible API links for each marker','lmm'),
			'desc'    => __('Directions','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-car.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_listmarkers_api_kml'] = array(
			'version' => '2.1',
			'section' => 'defaults_layer',
			'title'    => '',
			'desc'    => 'KML <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_listmarkers_api_fullscreen'] = array(
			'version' => '2.1',
			'section' => 'defaults_layer',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png">',
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_layer_listmarkers_api_qr_code'] = array(
			'version' => '2.1',
			'section' => 'defaults_layer',
			'title'    => '',
			'desc'    => __('QR code','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png">',
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_layer_listmarkers_api_geojson'] = array(
			'version' => '2.1',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => 'GeoJSON <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png">',
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_layer_listmarkers_api_georss'] = array(
			'version' => '2.1',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => 'GeoRSS <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png">',
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_layer_listmarkers_api_wikitude'] = array(
			'version' => '2.1',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => 'Wikitude <img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png">',
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		/*===========================================
		*
		*
		* section Google
		*
		*
		===========================================*/	
		/*
		* Google Maps API Key
		*/
		$this->settings['google_maps_api_key_heading'] = array(
			'version' => '2.6',
			'section' => 'google',
			'title'   => '', 
			'desc'    => '<a name="google1" class="lmm-index-links"></a>' . __( 'Google Maps API key', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['google_maps_api_key_helptext'] = array(
			'version' => '2.6',
			'section' => 'google',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'The usage of Google Maps is free for non-commercial users. Since 01/2012, commercial users have a current usage limit of 25.000 free requests a day - with additional usage cost of 0.5$/1000 requests. In order to comply with the <a href="https://developers.google.com/maps/faq" target="_blank">Google Maps terms of services</a>, commercial users have to <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key">register for a free API key</a>. This API key can also be used by non-commercial users in order to monitor their Google Maps API usage.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['google_maps_api_key'] = array(
			'version' => '2.6',
			'title'   => __( 'Google Maps API key', 'lmm'),
			'desc'    => __( 'Please enter your Google Maps API key here', 'lmm' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'google'
		);
		/*
		* Google language localization
		* https://spreadsheets.google.com/spreadsheet/pub?key=0Ah0xU81penP1cDlwZHdzYWkyaERNc0xrWHNvTTA1S1E&gid=1
		*/
		$this->settings['google_maps_language_localization_heading'] = array(
			'version' => '2.7.1',
			'section' => 'google',
			'title'   => '', 
			'desc'    => '<a name="google2" class="lmm-index-links"></a>' . __( 'Google language localization', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['google_maps_language_localization_helptext'] = array(
			'version' => '2.7.1',
			'section' => 'google',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Language used when displaying textual information such as the names for controls, copyright notices, driving directions and labels on Google maps, direction links and autocomplete for address search.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['google_maps_language_localization'] = array(
			'version' => '2.7.1',
			'section' => 'google',
			'title'   => __('Default language','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'browser_setting',
			'choices' => array(
				'browser_setting' => __('automatic 1 (distinct language for each user - detects the users browser language setting, preferred method by Google)','lmm'),
				'wordpress_setting' => sprintf(__('automatic 2 (same language for each user - uses the first two letters from the constant WPLANG defined in wp-config.php = %s)','lmm'),substr(WPLANG,0,2)),
				'ar' => __('Arabic','lmm') . ' (' . __('language code','lmm') . ': ar)',
				'bg' => __('Bulgarian','lmm') . ' (' . __('language code','lmm') . ': bg)',
				'ca' => __('Catalan','lmm') . ' (' . __('language code','lmm') . ': ca)',
				'cs' => __('Czech','lmm') . ' (' . __('language code','lmm') . ': cs)',
				'da' => __('Danish','lmm') . ' (' . __('language code','lmm') . ': da)',
				'de' => __('German','lmm') . ' (' . __('language code','lmm') . ': de)',
				'el' => __('Greek','lmm') . ' (' . __('language code','lmm') . ': el)',
				'en' => __('English','lmm') . ' (' . __('language code','lmm') . ': en)',
				'en-AU' => __('English (Australian)','lmm') . ' (' . __('language code','lmm') . ': en-AU)',
				'en-GB' => __('English (Great Britain)','lmm') . ' (' . __('language code','lmm') . ': en-GB)',
				'es' => __('Spanish','lmm') . ' (' . __('language code','lmm') . ': es)',
				'eu' => __('Basque','lmm') . ' (' . __('language code','lmm') . ': eu)',
				'fa' => __('Farsi','lmm') . ' (' . __('language code','lmm') . ': fa)',
				'fi' => __('Finnish','lmm') . ' (' . __('language code','lmm') . ': fi)',
				'fil' => __('Filipino','lmm') . ' (' . __('language code','lmm') . ': fil)',
				'fr' => __('French','lmm') . ' (' . __('language code','lmm') . ': fr)',
				'gl' => __('Galician','lmm') . ' (' . __('language code','lmm') . ': gl)',
				'gu' => __('Gujarati','lmm') . ' (' . __('language code','lmm') . ': gu)',
				'hi' => __('Hindi','lmm') . ' (' . __('language code','lmm') . ': hi)',
				'hr' => __('Croatian','lmm') . ' (' . __('language code','lmm') . ': hr)',
				'hu' => __('Hungarian','lmm') . ' (' . __('language code','lmm') . ': hu)',
				'id' => __('Indonesian','lmm') . ' (' . __('language code','lmm') . ': id)',
				'it' => __('Italian','lmm') . ' (' . __('language code','lmm') . ': it)',
				'iw' => __('Hebrew','lmm') . ' (' . __('language code','lmm') . ': iw)',
				'ja' => __('Japanese','lmm') . ' (' . __('language code','lmm') . ': ja)',
				'kn' => __('Kannada','lmm') . ' (' . __('language code','lmm') . ': kn)',
				'ko' => __('Korean','lmm') . ' (' . __('language code','lmm') . ': ko)',
				'lt' => __('Lithuanian','lmm') . ' (' . __('language code','lmm') . ': lt)',
				'lv' => __('Latvian','lmm') . ' (' . __('language code','lmm') . ': lv)',
				'ml' => __('Malayalam','lmm') . ' (' . __('language code','lmm') . ': ml)',
				'mr' => __('Marathi','lmm') . ' (' . __('language code','lmm') . ': mr)',
				'nl' => __('Dutch','lmm') . ' (' . __('language code','lmm') . ': nl)',
				'no' => __('Norwegian','lmm') . ' (' . __('language code','lmm') . ': no)',
				'pl' => __('Polish','lmm') . ' (' . __('language code','lmm') . ': pl)',
				'pt' => __('Portuguese','lmm') . ' (' . __('language code','lmm') . ': pt)',
				'pt-BR' => __('Portuguese (Brazil)','lmm') . ' (' . __('language code','lmm') . ': pt-BR)',
				'pt-PT' => __('Portuguese (Portugal)','lmm') . ' (' . __('language code','lmm') . ': pt-PT)',
				'ro' => __('Romanian','lmm') . ' (' . __('language code','lmm') . ': ro)',
				'ru' => __('Russian','lmm') . ' (' . __('language code','lmm') . ': ru)',
				'sk' => __('Slovak','lmm') . ' (' . __('language code','lmm') . ': sk)',
				'sl' => __('Slovenian','lmm') . ' (' . __('language code','lmm') . ': sl)',
				'sr' => __('Serbian','lmm') . ' (' . __('language code','lmm') . ': sr)',
				'sv' => __('Swedish','lmm') . ' (' . __('language code','lmm') . ': sv)',
				'tl' => __('Tagalog','lmm') . ' (' . __('language code','lmm') . ': tl)',
				'ta' => __('Tamil','lmm') . ' (' . __('language code','lmm') . ': ta)',
				'te' => __('Telugu','lmm') . ' (' . __('language code','lmm') . ': te)',
				'th' => __('Thai','lmm') . ' (' . __('language code','lmm') . ': th)',
				'uk' => __('Ukrainian','lmm') . ' (' . __('language code','lmm') . ': uk)',
				'vi' => __('Vietnamese','lmm') . ' (' . __('language code','lmm') . ': vi)',
				'zh-CN' => __('Chinese (simplified)','lmm') . ' (' . __('language code','lmm') . ': zh-CN)',
				'zh-TW' => __('Chinese (traditional)','lmm') . ' (' . __('language code','lmm') . ': zh-TW)',
			)
		);
		/*
		* Google Maps base domain
		*/
		$this->settings['google_maps_base_domain_heading'] = array(
			'version' => '2.7.1',
			'section' => 'google',
			'title'   => '', 
			'desc'    => '<a name="google3" class="lmm-index-links"></a>' . __( 'Google Maps base domain', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['google_maps_base_domain_helptext'] = array(
			'version' => '2.7.1',
			'section' => 'google',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'The base domain from which to load the Google Maps API (used for geocoding for example). If you want to change the language of the Google Maps interface (buttons etc) only, please change the option "Google language localization" above.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['google_maps_base_domain'] = array(
			'version' => '2.7.1',
			'section' => 'google',
			'title'   => __('Google Maps base domain','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'maps.google.com',
			'choices' => array(
				'maps.google.com' => 'maps.google.com',
				'maps.google.at' => 'maps.google.at',
				'maps.google.com.au' => 'maps.google.com.au',
				'maps.google.com.ba' => 'maps.google.com.ba',
				'maps.google.be' => 'maps.google.be',
				'maps.google.bg' => 'maps.google.bg',
				'maps.google.com.br' => 'maps.google.com.br',
				'maps.google.ca' => 'maps.google.ca',
				'maps.google.ch' => 'maps.google.ch',
				'maps.google.cm' => 'maps.google.cm',
				'ditu.google.cn' => 'ditu.google.cn',
				'maps.google.cz' => 'maps.google.cz',
				'maps.google.de' => 'maps.google.de',
				'maps.google.dk' => 'maps.google.dk',
				'maps.google.es' => 'maps.google.es',
				'maps.google.fi' => 'maps.google.fi',
				'maps.google.fr' => 'maps.google.fr',
				'maps.google.it' => 'maps.google.it',
				'maps.google.lk' => 'maps.google.lk',
				'maps.google.jp' => 'maps.google.jp',
				'maps.google.nl' => 'maps.google.nl',
				'maps.google.no' => 'maps.google.no',
				'maps.google.co.nz' => 'maps.google.co.nz',
				'maps.google.pl' => 'maps.google.pl',
				'maps.google.ru' => 'maps.google.ru',
				'maps.google.se' => 'maps.google.se',
				'maps.google.tw' => 'maps.google.tw',
				'maps.google.co.uk' => 'maps.google.co.uk',
				'maps.google.co.ve' => 'maps.google.co.ve',
			)
		);
		$this->settings['google_maps_base_domain_custom'] = array(
			'version' => '2.7.1',
			'title'   => __( 'Custom base domain', 'lmm'),
			'desc'    => __( 'If your localized Google Maps basedomain is not available in the list above, please enter the domain name here (without http://, for example maps.google.com). If a domain name is entered, the setting "Google Maps base domain" from above gets overwritten.', 'lmm' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'google'
		);		
		/*
		* Google Places Bounds
		*/
		$this->settings['google_places_bounds_heading'] = array(
			'version' => '1.0',
			'section' => 'google',
			'title'   => '', 
			'desc'    => '<a name="google4" class="lmm-index-links"></a>' . __( 'Google Places bounds', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['google_places_bounds_helptext2'] = array(
			'version' => '1.0',
			'section' => 'google',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Leaflet Maps Marker uses the <a href="http://code.google.com/intl/de-AT/apis/maps/documentation/places/autocomplete.html" target="_blank">Google Places Autocomplete API</a> to easily find coordinates for places or addresses. This feature is enabled by default. Preview:', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'/img/help-google-places-preview.png" /><br/>' . __( 'You can get better search results if you enable the bounds feature. This allows you to specify the area in which to primarily search for places or addresses. Please note: the results are biased towards, but not restricted to places or addresses contained within these bounds.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['google_places_bounds_status'] = array(
			'version' => '1.0',
			'section' => 'google',
			'title'   => __('Google Places bounds','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'disabled',
			'choices' => array(
				'disabled' => __('disabled','lmm'),
				'enabled' => __('enabled','lmm')
			)
		);
		$this->settings['google_places_bounds_helptext3'] = array(
			'version' => '1.0',
			'section' => 'google',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'If enabled, please enter longitude and latitude values below for the corner points of the prefered search area. Below you find an example for Vienna/Austria:', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'/img/help-google-places-bounds.jpg" />',
			'type'    => 'helptext'
		);
		$this->settings['google_places_bounds_lat1'] = array(
			'version' => '1.0',
			'title'   => __( 'Latitude', 'lmm' ) . ' 1',
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '48.326583',
			'type'    => 'text',
			'section' => 'google'
		);
		$this->settings['google_places_bounds_lon1'] = array(
			'version' => '1.0',
			'title'   => __( 'Longitude', 'lmm' ) . ' 1',
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '16.55056',
			'type'    => 'text',
			'section' => 'google'
		);
		$this->settings['google_places_bounds_lat2'] = array(
			'version' => '1.0',
			'title'   => __( 'Latitude', 'lmm' ) . ' 2',
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '48.114308',
			'type'    => 'text',
			'section' => 'google'
		);
		$this->settings['google_places_bounds_lon2'] = array(
			'version' => '1.0',
			'title'   => __( 'Longitude', 'lmm' ) . ' 2',
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '16.187325',
			'type'    => 'text',
			'section' => 'google'
		);		
		/*
		* Google Places Search Prefix
		*/
		$this->settings['google_places_search_prefix_heading'] = array(
			'version' => '1.0',
			'section' => 'google',
			'title'   => '', 
			'desc'    => '<a name="google5" class="lmm-index-links"></a>' . __( 'Google Places search prefix', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['google_places_search_prefix_helptext1'] = array(
			'version' => '1.0',
			'section' => 'google',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'You can also select a search prefix, which automatically gets added to search form when creating a new marker or layer.', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'/img/help-google-places-prefix.png" />',
			'type'    => 'helptext'
		);
		$this->settings['google_places_search_prefix_status'] = array(
			'version' => '1.0',
			'section' => 'google',
			'title'   => __('Google Places search prefix','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'disabled',
			'choices' => array(
				'disabled' => __('disabled','lmm'),
				'enabled' => __('enabled','lmm')
			)
		);
		$this->settings['google_places_search_prefix'] = array(
			'version' => '1.0',
			'title'   => __( 'Prefix to use', 'lmm' ),
			'desc'    => '',
			'std'     => 'Wien, ',
			'type'    => 'text',
			'section' => 'google'
		);	
		/*===========================================
		*
		*
		* section Directions
		*
		*
		===========================================*/	
		/*
		* Directions General
		*/
		$this->settings['directions_general_heading'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => '', 
			'desc'    => '<a name="directions1" class="lmm-index-links"></a>' . __( 'General settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);		
		$this->settings['directions_general_helptext1'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select your prefered directions provider. This setting will be used for the directions link in the panel on top of marker maps and for the action panel which gets attached to the popup text on each marker if enabled.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['directions_provider'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Use the following directions provider','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'googlemaps',
			'choices' => array(
				'googlemaps' => __('Google Maps (worldwide)','lmm') . ' - <a href="http://maps.google.com/maps?saddr=Vienna&daddr=Linz&hl=de&sll=37.0625,-95.677068&sspn=59.986788,135.263672&geocode=FS6Z3wIdO9j5ACmfyjZRngdtRzFGW6JRiuXC_Q%3BFfwa4QIdBvzZAClNhZn6lZVzRzHEdXlXLClTfA&vpsrc=0&mra=ls&t=m&z=9&layer=t" style="text-decoration:none;" target="_blank">Demo</a>',
				'yours' => __('yournavigation.org (based on OpenStreetMap, worldwide)','lmm') . ' - <a href="http://www.yournavigation.org/?flat=52.215636&flon=6.963946&tlat=52.2573&tlon=6.1799&v=motorcar&fast=1&layer=mapnik" style="text-decoration:none;" target="_blank">Demo</a>',
				'osrm' => __('map.project-osrm.org (based on OpenStreetMap, worldwide)','lmm') . ' - <a href="http://map.project-osrm.org/?hl=en&loc=48.242330,16.433030&loc=48.219069,16.380959" style="text-decoration:none;" target="_blank">Demo</a>',
				'ors' => __('openrouteservice.org (based on OpenStreetMap, Europe only)','lmm') . ' - <a href="http://openrouteservice.org/index.php?start=7.0892567,50.7265543&end=7.0986258,50.7323634&lat=50.72905&lon=7.09574&zoom=15&pref=Fastest&lang=de" style="text-decoration:none;" target="_blank">Demo</a>'
			)
		);	
		$this->settings['directions_popuptext_panel'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Attach directions panel to popup text on each marker','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')			)
		);			
		$this->settings['directions_general_helptext2'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'std'     => '', 
			'title'   => '',	
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'/img/help-directions-popuptext-panel.jpg" />',
			'type'    => 'helptext'
		);
		/*
		* Google Maps
		*/
		$this->settings['directions_googlemaps_heading'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => '', 
			'desc'    => '<a name="directions2" class="lmm-index-links"></a>' . __( 'Google Maps directions settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['directions_googlemaps_helptext1'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'std'     => '', 
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);			
		$this->settings['directions_googlemaps_map_type'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Map type','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'm',
			'choices' => array(
				'm' => __('Map','lmm'),
				'k' => __('Satellite','lmm'),
				'h' => __('Hybrid','lmm'),
				'p' => __('Terrain','lmm')							
			)
		);	
		$this->settings['directions_googlemaps_traffic'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Show traffic layer?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'1' => __('yes','lmm'),
				'0' => __('no','lmm')
			)
		);	
		$this->settings['directions_googlemaps_distance_units'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Distance units','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ptk',
			'choices' => array(
				'ptk' => __('metric (km)','lmm'),
				'ptm' => __('imperial (miles)','lmm')							
			)
		);		
		$this->settings['directions_googlemaps_route_type_highways'] = array(
			'version' => '1.0',
			'section' => 'directions',
			'title'    => __('Route type','lmm'),
			'desc'    => __('Avoid highways','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);				
		$this->settings['directions_googlemaps_route_type_tolls'] = array(
			'version' => '1.0',
			'section' => 'directions',
			'title'    => '',
			'desc'    => __('Avoid tolls','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['directions_googlemaps_route_type_public_transport'] = array(
			'version' => '1.0',
			'section' => 'directions',
			'title'    => '',
			'desc'    => __('Public transport (works only in some areas)','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['directions_googlemaps_route_type_walking'] = array(
			'version' => '1.0',
			'section' => 'directions',
			'title'    => '',
			'desc'    => __('Walking directions','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);					
		$this->settings['directions_googlemaps_overview_map'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Overview map','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('visible','lmm')
			)
		);			
		
		/*
		* yournavigation.org
		*/
		$this->settings['directions_yours_heading'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => '', 
			'desc'    => '<a name="directions3" class="lmm-index-links"></a>' . __( 'yournavigation.org settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['directions_yours_helptext1'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'std'     => '', 
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);		
		$this->settings['directions_yours_type_of_transport'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Type of transport','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'motorcar',
			'choices' => array(
				'motorcar' => __('Motorcar','lmm'),
				'bicycle' => __('Bicycle','lmm'),
				'foot' => __('Foot','lmm')
			)
		);		
		$this->settings['directions_yours_route_type'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Route type','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('fastest route','lmm'),
				'1' => __('shortest route','lmm')
			)
		);		
		$this->settings['directions_yours_layer'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Gosmore instance to calculate the route','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'mapnik',
			'choices' => array(
				'mapnik' => __('mapnik (for normal routing using car, bicycle or foot)','lmm'),
				'cn' => __('cn (for using bicycle routing using cycle route networks only)','lmm')
			)
		);		
		/*
		* map.project-osrm.org
		*/
		$this->settings['directions_osrm_heading'] = array(
			'version' => '2.7.1',
			'section' => 'directions',
			'title'   => '', 
			'desc'    => '<a name="directions4" class="lmm-index-links"></a>' . __( 'map.project-osrm.org settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['directions_osrm_helptext1'] = array(
			'version' => '2.7.1',
			'section' => 'directions',
			'std'     => '', 
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);			
		$this->settings['directions_osrm_language'] = array(
			'version' => '2.7.1',
			'section' => 'directions',
			'title'   => __('Language of route instructions','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'en',
			'choices' => array(
				'en' => __('English','lmm'),
				'de' => __('German','lmm'),
				'dk' => __('Danish','lmm'),
				'es' => __('Spanish','lmm'),
				'fi' => __('Finnish','lmm'),
				'fr' => __('French','lmm'),
				'it' => __('Italian','lmm'),
				'lv' => __('Latvian','lmm'),
				'pl' => __('Polish','lmm'),
				'ru' => __('Russian','lmm')								
			)
		);	
		$this->settings['directions_osrm_units'] = array(
			'version' => '2.7.1',
			'section' => 'directions',
			'title'   => __('Units','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array(
				'0' => __('metric (kilometer)','lmm'),
				'1' => __('imperial (miles)','lmm')
			)
		);
		/*
		* openrouteservice.org
		*/
		$this->settings['directions_ors_heading'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => '', 
			'desc'    => '<a name="directions5" class="lmm-index-links"></a>' . __( 'openrouteservice.org settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['directions_ors_helptext1'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'std'     => '', 
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);			
		$this->settings['directions_ors_route_preferences'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Route preferences','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'Shortest',
			'choices' => array(
				'Fastest' => __('fastest route','lmm'),
				'Shortest' => __('shortest route','lmm'),
				'Pedestrian' => __('route for pedestrians','lmm'),
				'Bicycle' => __('route for bicycles','lmm')								
			)
		);	
		$this->settings['directions_ors_language'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('Language of route instructions','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'en',
			'choices' => array(
				'en' => __('English','lmm'),
				'de' => __('German','lmm'),
				'it' => __('Italian','lmm'),
				'fr' => __('French','lmm'),
				'es' => __('Spanish','lmm')
			)
		);	
		$this->settings['directions_ors_no_motorways'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('No motorways?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);	
		$this->settings['directions_ors_no_tollways'] = array(
			'version' => '1.4',
			'section' => 'directions',
			'title'   => __('No tollways?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')							
			)
		);		
		/*===========================================
		*
		*
		* section Augmented-Reality
		*
		*
		===========================================*/	
		/*
		* AR General
		*/
		$this->settings['ar_general_helptext1'] = array(
			'version' => '1.0',
			'section' => 'ar',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Markers created with Leaflet Maps Marker can also be displayed via <a href="http://en.wikipedia.org/wiki/Augmented_reality" target="_blank">Augmented-Reality technology</a> on mobile devices. As a first steps, an API to <a href="http://www.wikitude.com" target="_blank">Wikitude</a> has been implemented. APIs to other Augmented-Reality-Providers (like <a href="http://www.layar.com" target="_blank">Layar</a> or <a href="http://www.junaio.de" target="_blank">Junaio</a>) will probably follow in one of the next versions. Sample screenshots:', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'/screenshot-6.jpg" />',
			'type'    => 'helptext'
		);
		/*
		* AR Wikitude
		*/
		$this->settings['ar_wikitude_heading'] = array(
			'version' => '1.0',
			'section' => 'ar',
			'title'   => '', 
			'desc'    => '<a name="ar1" class="lmm-index-links"></a>' . __( 'Wikitude settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['ar_wikitude_helptext'] = array(
			'version' => '1.0',
			'section' => 'ar',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please visit <a href="http://www.mapsmarker.com/wikitude" target="_blank">http://www.mapsmarker.com/wikitude</a> for instructions how to submit your marker or layer maps to Wikitude.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['ar_wikitude_provider_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Provider name', 'lmm' ),
			'desc'    => '<strong>' . __( 'Identifies the content provider or content channel, no spaces/special characters', 'lmm' ) . '</strong>',
			'std'     => 'www_mapsmarker_com',
			'type'    => 'text',
			'section' => 'ar'
		);
		$this->settings['ar_wikitude_provider_url'] = array(
			'version' => '1.0',
			'title'   => __( 'Provider URL', 'lmm' ),
			'desc'    => __( 'Link to content provider', 'lmm' ),
			'std'     => 'http://www.mapsmarker.com',
			'type'    => 'text',
			'section' => 'ar'
		);
		$this->settings['ar_wikitude_logo'] = array(
			'version' => '1.0',
			'title'   => __( 'Logo', 'lmm' ),
			'desc'    => __( 'The logo is displayed on the left bottom corner on Wikitude when an icon is selected - 96x96 pixel, transparent PNG', 'lmm' ),
			'std'     => LEAFLET_PLUGIN_URL . 'img/wikitude-logo-96x96.png',
			'type'    => 'text',
			'section' => 'ar'
		);
		$this->settings['ar_wikitude_icon'] = array(
			'version' => '1.0',
			'title'   => __( 'Icon', 'lmm' ),
			'desc'    => __( 'The icon is displayed in the cam view of Wikitude to indicate a marker - 32x32 pixel, transparent PNG', 'lmm' ),
			'std'     => LEAFLET_PLUGIN_URL . 'img/wikitude-icon-32x32.png',
			'type'    => 'text',
			'section' => 'ar'
		);		
		$this->settings['ar_wikitude_email'] = array(
			'version' => '1.0',
			'title'   => __( 'E-Mail', 'lmm' ),
			'desc'    => __( 'Optional: displayed on each marker; used for sending an email directly from Wikitude', 'lmm' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'ar'
		);		
		$this->settings['ar_wikitude_phone'] = array(
			'version' => '1.0',
			'title'   => __( 'Phone', 'lmm' ),
			'desc'    => __( 'Optional: example: +4312345 - when a phone number is given, Wikitude displays a "call me" button in the bubble; used for every marker.', 'lmm' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'ar'
		);		
		$this->settings['ar_wikitude_attachment'] = array(
			'version' => '1.0',
			'title'   => __( 'Attachment', 'lmm' ),
			'desc'    => __( 'Optional: displayed on each marker; can be a link to a resource (image, PDF file...). You could use this to issue coupons or vouchers for potential clients that found you via Wikitude.', 'lmm' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'ar'
		);		
		$this->settings['ar_wikitude_radius'] = array(
			'version' => '1.0',
			'title'   => __( 'Search radius (in meter)', 'lmm' ),
			'desc'    => __( 'Retrieve POIs (Points of Interests) from database within this search radius in meters from the current location of the Wikitude user', 'lmm' ),
			'std'     => '100000',
			'type'    => 'text',
			'section' => 'ar'
		);		
		$this->settings['ar_wikitude_maxnumberpois'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum number of POIs', 'lmm' ),
			'desc'    => __( 'Used if Wikitude does not pass the variable maxNumberofPois - 50 is the maximum recommended', 'lmm' ),
			'std'     => '50',
			'type'    => 'text',
			'section' => 'ar'
		);		
		
		/*===========================================
		*
		*
		* section miscellaneous
		*
		*
		===========================================*/
		$this->settings['misc_general_heading'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => '<a name="misc1" class="lmm-index-links"></a>' . __( 'General settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['misc_general_helptext'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['capabilities_edit'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => __( 'User role needed for adding and editing markers/layers', 'lmm' ),
			'desc'    => __( 'Note: the settings and tools pages are always visible to admins only.', 'lmm' ),
			'type'    => 'radio',
			'std'     => 'edit_posts',
			'choices' => array(
				'activate_plugins' => __('Administrator (Capability activate_plugins)', 'lmm'),
				'moderate_comments' => __('Editor (Capability moderate_comments)', 'lmm'),
				'edit_published_posts' => __('Author (Capability edit_published_posts)', 'lmm'),
				'edit_posts' => __('Contributor (Capability edit_posts)', 'lmm'),
				'read' => __('Subscriber (Capability read)', 'lmm')				
			)
		);
		$this->settings['capabilities_delete'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => __( 'User role needed for deleting markers/layers', 'lmm' ),
			'desc'    => __( 'Note: the settings and tool pages are always visible to admins only.', 'lmm' ),
			'type'    => 'radio',
			'std'     => 'edit_posts',
			'choices' => array(
				'activate_plugins' => __('Administrator (Capability activate_plugins)', 'lmm'),
				'moderate_comments' => __('Editor (Capability moderate_comments)', 'lmm'),
				'edit_published_posts' => __('Author (Capability edit_published_posts)', 'lmm'),
				'edit_posts' => __('Contributor (Capability edit_posts)', 'lmm'),
				'read' => __('Subscriber (Capability read)', 'lmm')		
			)
		);
		$this->settings['markers_per_page'] = array(
			'version' => '1.0',
			'title'   => __( 'Markers per page', 'lmm' ),
			'desc'    => __( 'How many markers should be listed on one page at the page "list all markers"?', 'lmm' ),
			'std'     => '30',
			'type'    => 'text',
			'section' => 'misc'
		);
		$this->settings['shortcode'] = array(
			'version' => '1.0',
			'title'   => __( 'Shortcode', 'lmm' ),
			'desc'    => __( 'Shortcode to add markers or layers into articles or pages  - Example: [mapsmarker marker="1"].<br/> Attention: if you change the shortcode after having embedded shortcodes into posts/Pages, the shortcode on these specific articles/pages has to be changed also manually - otherwise these markers/layers will not be show on frontend!', 'lmm' ),
			'std'     => 'mapsmarker',
			'type'    => 'text',
			'section' => 'misc'
		);
		$this->settings['misc_tinymce_button'] = array(
			'version' => '1.9',
			'section' => 'misc',
			'title'   => __('TinyMCE button','lmm'),
			'desc'    => __('if enabled, a button on post/page edit screen gets added for easily searching and inserting maps','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->settings['misc_add_georss_to_head'] = array(
			'version' => '1.5',
			'section' => 'misc',
			'title'   => __('Add GeoRSS feed to &lt;head&gt;','lmm'),
			'desc'    => __('if enabled, a GeoRSS feed for all markers will be added to the &lt;head&gt;-section of the website, allowing users to subscribe to your markers','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->settings['admin_bar_integration'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => __('WordPress Admin Bar integration','lmm'),
			'desc'    => __('show or hide drop down menu for Leaflet Maps Marker in Wordpress Admin Bar','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->settings['misc_admin_dashboard_widget'] = array(
			'version' => '2.5',
			'section' => 'misc',
			'title'   => __('WordPress admin dashboard widget','lmm'),
			'desc'    => __('shows a widget on the admin dashboard which displays latest markers and blog posts from mapsmarker.com','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->settings['misc_global_stats'] = array(
			'version' => '1.1',
			'section' => 'misc',
			'title'   => __('Global statistics','lmm'),
			'desc'    => __('Anonymous marker/layer hit counter','lmm') . ' <a href="http://www.mapsmarker.com/legal#global-stats" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);		
		$this->settings['misc_qrcode_size'] = array(
			'version' => '1.1',
			'title'   => __( 'QR code image size', 'lmm' ),
			'desc'    => __( 'Width and height in pixel of QR code image for marker/layer standalone fullscreen map links', 'lmm' ),
			'std'     => '150',
			'type'    => 'text',
			'section' => 'misc'
		);
		$this->settings['misc_projections'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => __( 'Coordinate Reference System', 'lmm' ),
			'desc'    => __( 'Used for created maps - do not change this if you are not sure what it means!', 'lmm'),
			'type'    => 'radio',
			'std'     => 'L.CRS.EPSG3857',
			'choices' => array(
				'L.CRS.EPSG3857' => __('EPSG:3857 (Spherical Mercator), used by most of commercial map providers (CloudMade, Google, Yahoo, Bing, etc.)', 'lmm'),
				'L.CRS.EPSG4326' => __('EPSG:4326 (Plate Carree), very popular among GIS enthusiasts', 'lmm'),
				'L.CRS.EPSG3395' => __('EPSG:4326 (Mercator), used by some map providers.', 'lmm')
			)
		);
		/*
		* Language Settings
		*/
		$this->settings['misc_language_heading'] = array(
			'version' => '2.4',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => '<a name="misc2" class="lmm-index-links"></a>' . __( 'Language settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['misc_language_helptext'] = array(
			'version' => '2.4',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => __('The language used on plugin pages on backend and/or on maps on frontend. Please note that the language for Google Services can be set seperately via Settings / tab "Google", section "Google language localization".','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['misc_plugin_language'] = array(
			'version' => '2.4',
			'section' => 'misc',
			'title'   => __('Default language','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'automatic',
			'choices' => array(
				'automatic' => __('automatic (use WordPress default)','lmm'),
				'bg_BG' => __('Bulgarian','lmm') . ' (bg_BG)',
				'ca' => __('Catalan','lmm') . ' (ca)',
				'zh_CN' => __('Chinese','lmm') . ' (zh_CN)',
				'nl_NL' => __('Dutch','lmm') . ' (nl_NL)',
				'en_US' => __('English','lmm') . ' (en_US)',
				'fr_FR' => __('French','lmm') . ' (fr_FR)',
				'de_DE' => __('German','lmm') . ' (de_DE)',
				'hi_IN' => __('Hindi','lmm') . ' (hi_IN)',
				'it_IT' => __('Italian','lmm') . ' (it_IT)',
				'ja' => __('Japanese','lmm') . ' (ja)',
				'pl_PL' => __('Polish','lmm') . ' (pl_PL)',
				'ru_RU' => __('Russian','lmm') . ' (ru_RU)',
				'sk_SK' => __('Slovak','lmm') . ' (sk_SK)',
				'es_ES' => __('Spanish','lmm') . ' (es_ES)',
				'tr_TR' => __('Turkish','lmm') . ' (tr_TR)',
				'uk_UK' => __('Ukrainian','lmm') . ' (uk_UK)',
				'yi' => __('Yiddish','lmm') . ' (yi)'
			)
		);
		$this->settings['misc_plugin_language_area'] = array(
			'version' => '2.4',
			'section' => 'misc',
			'title'   => __('Where to change the default language','lmm'),
			'desc'    => __('This setting will only be used when the plugin language is not selected automatically','lmm'),
			'type'    => 'radio',
			'std'     => 'backend',
			'choices' => array(
				'backend' => __('WordPress admin area only','lmm'),
				'frontend' => __('WordPress frontend only','lmm'),
				'both' => __('WordPress admin area and frontend','lmm')
			)
		);		
		/*
		* KML Settings
		*/
		$this->settings['misc_kml_heading'] = array(
			'version' => '1.8',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => '<a name="misc3" class="lmm-index-links"></a>' . __( 'KML settings', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['misc_kml_helptext'] = array(
			'version' => '1.8',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Choose how marker names should be displayed in KML files', 'lmm') . ' <a href="http://www.mapsmarker.com/kml-names" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'helptext'
		);
		$this->settings['misc_kml'] = array(
			'version' => '1.8',
			'section' => 'misc',
			'title'   => __( 'Marker names in KML', 'lmm' ),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'show',
			'choices' => array(
				'show' => __('show', 'lmm'),
				'hide' => __('hide', 'lmm'),
				'popup' => __('put in front of popup-text', 'lmm')
			)
		);
		/*
		* Available columns for marker listing page
		*/
		$this->settings['misc_marker_listing_columns_heading'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => '<a name="misc4" class="lmm-index-links"></a>' . __( 'Available columns for marker listing page', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['misc_marker_listing_columns_helptext'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select the columns which should be available on the page "List all markers"', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['misc_marker_listing_columns_id'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => __('Columns to show','lmm'),
			'desc'    => 'ID',
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);
		$this->settings['misc_marker_listing_columns_icon'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Icon','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);
		$this->settings['misc_marker_listing_columns_markername'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Marker name','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);
		$this->settings['misc_marker_listing_columns_popuptext'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Popup text','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_layername'] = array(
			'version' => '2.7.1',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Layer name','lmm') . ' ' . __('(for marker listings below multi-layer maps only)','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);			
		$this->settings['misc_marker_listing_columns_basemap'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_layer'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Layer','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_coordinates'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Coordinates','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_zoom'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Zoom','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_openpopup'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Popup status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_panelstatus'] = array(
			'version' => '1.4',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Panel status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_mapsize'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Map size','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_createdby'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Created by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_createdon'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Created on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_updatedby'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Updated by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_updatedon'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Updated on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_controlbox'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Controlbox status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['misc_marker_listing_columns_shortcode'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Shortcode','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_kml'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'KML',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_fullscreen'] = array(
			'version' => '1.1',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_qr_code'] = array(
			'version' => '1.1',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('QR code','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_geojson'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'GeoJSON',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_georss'] = array(
			'version' => '1.2',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'GeoRSS',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_wikitude'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'Wikitude',
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		/*
		* Sort order for marker listing page
		*/
		$this->settings['misc_marker_listing_sort_heading'] = array(
			'version' => '2.3',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => '<a name="misc5" class="lmm-index-links"></a>' . __( 'Sort order for marker listing page', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['misc_marker_listing_sort_helptext'] = array(
			'version' => '2.3',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select order by and sort order for "List all markers" page', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['misc_marker_listing_sort_order_by'] = array(
			'version' => '2.3',
			'section' => 'misc',
			'title'   => __('Order list of markers by','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'm.id',
			'choices' => array(
				'm.id' => 'ID',
				'm.markername' => __('marker name','lmm'),
				'm.layer' => __('assigned layer','lmm') . '(ID)',
				'm.createdon' => __('created on','lmm'),
				'm.createdby' => __('created by','lmm'),
				'm.updatedon' => __('updated on','lmm'),
				'm.updatedby' => __('updated by','lmm')
			)
		);
		$this->settings['misc_marker_listing_sort_sort_order'] = array(
			'version' => '2.3',
			'section' => 'misc',
			'title'   => __('Sort order','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ASC',
			'choices' => array(
				'ASC' => __('ascending','lmm'),
				'DESC' => __('descending','lmm')
			)
		);
		/*
		* Available columns for layer listing page
		*/
		$this->settings['misc_layer_listing_columns_heading'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => '<a name="misc6" class="lmm-index-links"></a>' . __( 'Available columns for layer listing page', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['misc_layer_listing_columns_helptext'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select the columns which should be available on the page "List all layers"', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['misc_layer_listing_columns_id'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => __('Columns to show','lmm'),
			'desc'    => 'ID',
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);
		$this->settings['misc_layer_listing_columns_type'] = array(
			'version' => '1.7',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Type','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);		
		$this->settings['misc_layer_listing_columns_layername'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Layer name','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);
		$this->settings['misc_layer_listing_columns_markercount'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Number of markers','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);		
		$this->settings['misc_layer_listing_columns_basemap'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_layercenter'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Layer center','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_zoom'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Zoom','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_mapsize'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Map size','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_panelstatus'] = array(
			'version' => '1.4',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Panel status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_createdby'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Created by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_createdon'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Created on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_updatedby'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Updated by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_updatedon'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Updated on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_controlbox'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Controlbox status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['misc_layer_listing_columns_shortcode'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Shortcode','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_kml'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'KML',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_fullscreen'] = array(
			'version' => '1.1',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_qr_code'] = array(
			'version' => '1.1',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('QR code','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_geojson'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'GeoJSON',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_georss'] = array(
			'version' => '1.2',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'GeoRSS',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_wikitude'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'Wikitude',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		/*
		* Sort order for layer listing page
		*/
		$this->settings['misc_layer_listing_sort_heading'] = array(
			'version' => '2.3',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => '<a name="misc7" class="lmm-index-links"></a>' . __( 'Sort order for layer listing page', 'lmm') . ' <a href="#" title="' . esc_attr__('Scroll to top','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'img/icon-scroll-to-top.png" /></a>',
			'type'    => 'heading'
		);
		$this->settings['misc_layer_listing_sort_helptext'] = array(
			'version' => '2.3',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select order by and sort order for "List all layers" page', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['misc_layer_listing_sort_order_by'] = array(
			'version' => '2.3',
			'section' => 'misc',
			'title'   => __('Order list of markers by','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'id',
			'choices' => array(
				'id' => 'ID',
				'name' => __('layer name','lmm'),
				'createdon' => __('created on','lmm'),
				'createdby' => __('created by','lmm'),
				'updatedon' => __('updated on','lmm'),
				'updatedby' => __('updated by','lmm')
			)
		);
		$this->settings['misc_layer_listing_sort_sort_order'] = array(
			'version' => '2.3',
			'section' => 'misc',
			'title'   => __('Sort order','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ASC',
			'choices' => array(
				'ASC' => __('ascending','lmm'),
				'DESC' => __('descending','lmm')
			)
		);		
		/*===========================================
		*
		*
		* section reset
		*
		*
		===========================================*/
		$this->settings['reset_settings'] = array(
			'version' => '1.0',
			'section' => 'reset',
			'title'   => __( 'Reset Settings','lmm' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset plugin options to their defaults.','lmm' )
		);
	}
	
	/**
	 * Initialize settings to their default values
	 */ 
	public function initialize_settings() {
		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' ) {
				$default_settings[$id] = $setting['std'];
				}
		}
		update_option( 'leafletmapsmarker_options', $default_settings );
	}
	/**
	* Register settings
	*/
	public function register_settings() {
		
		register_setting( 'leafletmapsmarker_options', 'leafletmapsmarker_options', array ( &$this, 'validate_settings' ) );
		
		foreach ( $this->sections as $slug => $title ) {
			if ( $slug == 'basemaps' )
				add_settings_section( $slug, $title, array( &$this, 'display_basemaps_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'overlays' )
				add_settings_section( $slug, $title, array( &$this, 'display_overlays_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'wms' )
				add_settings_section( $slug, $title, array( &$this, 'display_wms_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'defaults_marker' )
				add_settings_section( $slug, $title, array( &$this, 'display_defaults_marker_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'defaults_layer' )
				add_settings_section( $slug, $title, array( &$this, 'display_defaults_layer_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'google' )
				add_settings_section( $slug, $title, array( &$this, 'display_google_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'directions' )
				add_settings_section( $slug, $title, array( &$this, 'display_directions_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'misc' )
				add_settings_section( $slug, $title, array( &$this, 'display_misc_section' ), 'leafletmapsmarker_settings' );
			else
				add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'leafletmapsmarker_settings' );
		}
		
		$this->get_settings();
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}
	}
	/**
	 * save defaults for new options after plugin updates but keep values of old settings
	 */
	public function save_defaults_for_new_options() {
		//info:  set defaults for options introduced in v1.1
		if (get_option('leafletmapsmarker_version') == '1.0' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.2
		if (get_option('leafletmapsmarker_version') == '1.1' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.2')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.4
		if (get_option('leafletmapsmarker_version') == '1.3' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.4')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.4.3		
		if (get_option('leafletmapsmarker_version') == '1.4.2' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.4.3')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.5
		if (get_option('leafletmapsmarker_version') == '1.4.3' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.5')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.6
		if (get_option('leafletmapsmarker_version') == '1.5.1' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.6')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.7
		if (get_option('leafletmapsmarker_version') == '1.6' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.7')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.8
		if (get_option('leafletmapsmarker_version') == '1.7' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.8')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.9
		if (get_option('leafletmapsmarker_version') == '1.8' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.9')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.1
		if (get_option('leafletmapsmarker_version') == '2.0' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '2.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.2
		if (get_option('leafletmapsmarker_version') == '2.1' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '2.2')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.3
		if (get_option('leafletmapsmarker_version') == '2.2' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '2.3')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.4
		if (get_option('leafletmapsmarker_version') == '2.3' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '2.4')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.5
		if (get_option('leafletmapsmarker_version') == '2.4' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '2.5')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.6
		if (get_option('leafletmapsmarker_version') == '2.5' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '2.6')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.7.1
		if (get_option('leafletmapsmarker_version') == '2.7' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '2.7.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		/* template for plugin updates 
		//info:  set defaults for options introduced in v2.8
		if (get_option('leafletmapsmarker_version') == '2.7.1' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '2.8')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		*/
	}
	
	/**
	* Validate settings
	*/
	public function validate_settings( $input ) {
		
		if ( ! isset( $input['reset_settings'] ) ) {
			$options = get_option( 'leafletmapsmarker_options' );
			
			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}
			
			return $input;
		}
		return false;
	}
}
$leafletmapsmarker_options = new Leafletmapsmarker_options();
function lmm_option( $option ) {
	$options = get_option( 'leafletmapsmarker_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}
?>