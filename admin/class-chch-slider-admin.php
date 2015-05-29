<?php
/**
 * CC Slider CC
 *
 * @package   ChChSliderAdmin
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014
 */

if ( ! class_exists( 'ChChSliderPreview' ) )
    require_once( dirname( __FILE__ ) . '/includes/chch-slider-preview.php' );

if ( ! class_exists( 'ChChSliderTemplate' ) )
    require_once( CHCH_SF_PLUGIN_DIR . 'public/includes/chch-slider-template.php' );
/**
 * @package ChChSliderAdmin
 * @author 	Chop-Chop.org <shop@chop-chop.org>
 */


class ChChSliderAdmin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	function __construct() {
		$this->plugin = ChChSlider::get_instance();
		$this->plugin_slug = $this->plugin->get_plugin_slug();

		// Register Post Type
		add_action( 'init', array( $this, 'chch_sf_register_post_type' ) );

		// Register Post Type Messages
		add_filter( 'post_updated_messages',  array( $this, 'chch_sf_post_type_messages') );

		// Register Post Type Meta Boxes and fields
		add_action( 'init', array( $this, 'chch_sf_initialize_cmb_meta_boxes'), 9999 );
		add_filter( 'cmb_meta_boxes', array( $this, 'chch_sf_cmb_metaboxes') );
    add_action( 'cmb_render_position_select', array( $this, 'chch_sf_render_position_select'), 10, 5  ); 
		add_action( 'add_meta_boxes_chch-slider', array( $this, 'chch_sf_metabox' ));

		// remove help tabs
		add_filter( 'contextual_help', array($this,'chch_sf_remove_help_tabs'), 999, 3 );
		add_filter( 'screen_options_show_screen', '__return_false');

		// Templates view
		add_action( 'edit_form_after_title',array( $this, 'chch_sf_templates_view' ));

		// Save Post Data
		add_action( 'save_post', array( $this, 'chch_sf_save_pop_up_meta'), 10, 3 );

		 

		// Customize the columns in the popup list.
		add_filter('manage_chch-slider_posts_columns',array( $this, 'chch_sf_custom_columns') );
		// Returns the content for the custom columns.
		add_action('manage_chch-slider_posts_custom_column',array( $this, 'chch_sf_manage_custom_columns' ),10, 2);
		add_action( 'admin_print_scripts', array( $this, 'chch_sf_enqueue_admin_scripts' ));
		add_action( 'admin_head', array( $this, 'chch_sf_admin_head_scripts') );
		add_action( 'wp_ajax_chch_sf_load_preview_module', array( $this, 'chch_sf_load_preview_module'  ));
    
    // Add custom shortcode button to TineMCE
		add_action( 'media_buttons_context', array( $this, 'chch_sf_shortcocde_button' ) ); 
    add_action( 'admin_footer',  array( $this, 'chch_sf_shortcode_modal'));

	}



	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
 


	/**
	 * Return a pages_select field for CMB
	 *
	 * @since     1.0.0
	 *
	 */
	function chch_sf_custom_columns($defaults) {
		$defaults['chch_sf_shortcode'] = __('Shortcode',$this->plugin_slug);
		$defaults['chch_sf_slides'] = __('Number of Slides',$this->plugin_slug);
		return $defaults;
	}


	/**
	 * Create columns in Pop-ups list
	 *
	 * @since     1.0.0
	 */
	function chch_sf_manage_custom_columns($column, $post_id) {
		global $post;
		if ($column === 'chch_sf_shortcode') {
			echo '[slidercc id="'.$post_id.'"]';
		}

		if ($column === 'chch_sf_slides') {
			if( $slides = get_post_meta($post_id,'_chch_slides', true)){
					$slide_count = count($slides);
			} else {
					$slide_count = 0;
			}
			echo $slide_count;
		}
	}


	/**
	 * Register Custom Post Type
	 *
	 * @since    1.0.0
	 */
	public function chch_sf_register_post_type() {

		$domain = $this->plugin_slug;

		$labels = array(
			'name'                => _x( 'Slider CC', 'Post Type General Name', $domain),
			'singular_name'       => _x( 'Slider CC', 'Post Type Singular Name', $domain),
			'menu_name'           => __( 'Slider CC', $domain),
			'parent_item_colon'   => __( 'Parent Item:', $domain),
			'all_items'           => __( 'Sliders CC', $domain),
			'view_item'           => __( 'View Item', $domain),
			'add_new_item'        => __( 'Add New Slider CC', $domain),
			'add_new'             => __( 'Add New Slider CC', $domain),
			'edit_item'           => __( 'Edit Slider CC', $domain),
			'update_item'         => __( 'Update Slider CC', $domain),
			'search_items'        => __( 'Search Slider CC', $domain),
			'not_found'           => __( 'Not found', $domain),
			'not_found_in_trash'  => __( 'No Slider CC found in Trash', $domain),
		);


		$args = array(
			'label'               => __( 'Slider CC', $domain),
			'description'         => __( '', $domain),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => false,
			'menu_position'       => 65,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false
		);
		register_post_type( 'chch-slider', $args );
	}



	/**
	 * Slider CC update messages.
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with new CPT update messages.
	 */
	function chch_sf_post_type_messages( $messages ) {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages['chch-slider'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Slider CC updated.', $this->plugin_slug ),
			2  => __( 'Custom field updated.', $this->plugin_slug),
			3  => __( 'Custom field deleted.',$this->plugin_slug),
			4  => __( 'Slider CC updated.', $this->plugin_slug ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Slider CC restored to revision from %s', $this->plugin_slug ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Slider CC published.', $this->plugin_slug ),
			7  => __( 'Slider CC saved.', $this->plugin_slug ),
			8  => __( 'Slider CC submitted.', $this->plugin_slug ),
			9  => sprintf(
				__( 'Slider CC scheduled for: <strong>%1$s</strong>.', $this->plugin_slug ),
				date_i18n( __( 'M j, Y @ G:i', $this->plugin_slug ), strtotime( $post->post_date ) )
			),
			10 => __( 'Slider CC draft updated.', $this->plugin_slug )
		);

		if ( $post_type_object->publicly_queryable ) {
			$permalink = get_permalink( $post->ID );

			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Slider CC',  $this->plugin_slug ) );
			$messages[ $post_type ][1] .= $view_link;
			$messages[ $post_type ][6] .= $view_link;
			$messages[ $post_type ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview Slider CC',  $this->plugin_slug ) );
			$messages[ $post_type ][8]  .= $preview_link;
			$messages[ $post_type ][10] .= $preview_link;
		}

		return $messages;
	}

	/**
	 * Initialize Custom Metaboxes Class
	 *
	 * @since  1.0.0
	 */
	function chch_sf_initialize_cmb_meta_boxes() {
 		if ( ! class_exists( 'cmb_Meta_Box' ) )
			require_once( dirname( __FILE__ ) . '/includes/Custom-Metaboxes-and-Fields-for-WordPress-master/init.php' );
	}

	/**
	 * Register custom metaboxes with CMB
	 *
	 * @since  1.0.0
	 */
	function chch_sf_cmb_metaboxes( array $meta_boxes ) {

		$domain = $this->plugin_slug;
		$prefix = '_chch_slider_';

		/**
		 * Slider Settings
		 */
		$meta_boxes['chch-metabox-settings'] = array(
			'id'         => 'chch-metabox-settings',
			'title'      => __( '<span class="dashicons dashicons-admin-generic"></span> Slider Settings', $domain ),
			'pages'      => array( 'chch-slider', $domain ),
			'context'    => 'side',
			'priority'   => 'high',
			'show_names' => true,
			'fields'     => array(
				array(
					'name'    => __( 'Width (px):', $domain ),
					'id'      => $prefix . 'width',
					'type'    => 'text',
					'attributes' => array(
						'class' => 'settings-box input-box width-box'
						),
						'default' => '600'
				), 
        array(
					'name'    => __( 'Position:', $domain ),
					'id'      => $prefix . 'position',
					'type'    => 'position_select',
					'attributes' => array(
						'class' => 'settings-box select-box position-box'
						),  
				), 
				array(
					'name'    => __( 'Effect:', $domain ),
					'id'      => $prefix . 'effect',
					'type'    => 'select',
					'attributes' => array(
						'class' => 'settings-box select-box effect-box'
						),
					'options' => array(
						'slide' => __( 'Slide', $domain  ),
						'fade' => __( 'Fade', $domain  ),
					),
					'default' => 'slide'
				), 
				array(
					'name' => __( 'Pause on hover', $domain  ),
					'id'   => $prefix . 'pause',
					'type' => 'checkbox',
					'attributes' => array(
						'class' => 'settings-box checkbox-box pause-box'
					),
				),
				array(
					'name' => __( 'Autoplay', $domain  ),
					'id'   => $prefix . 'autoplay',
					'type' => 'checkbox',
					'attributes' => array(
						'class' => 'settings-box checkbox-box autoplay-box'
						),
				),
				array(
					'name'    => __( 'Speed:', $domain ),
					'id'      => $prefix . 'speed',
					'type'    => 'text',
					'attributes' => array(
						'class' => 'settings-box input-box speed-box'
					),
					'default' => '2000'
				), 
			),
		);
 
		
		$meta_boxes['chch-metabox-advanced'] = array(
			'id'         => 'chch-metabox-advanced',
			'title'      => __( '<span class="dashicons dashicons-admin-tools"></span> Advanced Settings', $domain ),
			'pages'      => array( 'chch-slider', ),
			'context'    => 'side',
			'priority'   => 'high',
			'show_names' => true,
			'fields'     => array(
				array(
					'name' => __( 'Animation:', $domain  ),
					'id'   => $prefix . 'animation',
					'type' => 'radio',
					'options' => array(
						'css' => __( 'CSS animate', $domain  ),
						'js' => __( 'jQuery.animate', $domain  ),
						'velocity' => __( 'velocity.js', $domain  ),
					),
					'default' => 'css'
				),
				array(
					'name' => __( 'Keyboard navigation', $domain  ),
					'id'   => $prefix . 'keyboard',
					'type' => 'checkbox',
					'attributes' => array(
						'class' => 'settings-box checkbox-box keyboard-box'
					), 
				),
				array(
					'name' => __( 'Touch drag', $domain  ),
					'id'   => $prefix . 'touch',
					'type' => 'checkbox',
					'attributes' => array(
						'class' => 'settings-box checkbox-box touchdrag-box'
						),
				),
				array(
					'name' => __( 'Mousewheel', $domain  ),
					'id'   => $prefix . 'mousewheel',
					'type' => 'checkbox',
					'attributes' => array(
						'class' => 'settings-box checkbox-box mousewheel-box'
						),
				),
			),
		);
 
		return $meta_boxes;
	}
  
  
  /**
	 * Return a position_select field for CMB
	 *
	 * @since     1.0.0
	 * 
	 */
	function chch_sf_render_position_select( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$cookie_expire = array(
			'center' => 'Center',
			'left' => 'Left (Available in Pro)',
			'right' => 'Right (Available in Pro)', 
		);
		?>
		
		<select class="cmb_select settings-box select-box effect-box" name="<?php echo $field_args['_name']; ?>" id="<?php echo $field_args['_id']; ?>">	
		
		<?php
			foreach($cookie_expire as $value => $title):
				$selected = '';
				$disable = '';
				
				if(!empty($escaped_value)){
					if($value == $escaped_value) {
						$selected = 'selected';	
					} 
				}
				
				if($value != 'center') {
					$disable = 'disabled';	
				}
				
			 	echo '<option value="'.$value.'" '.$selected .' '.$disable.'>'.$title.'</option>';
			endforeach
		 ?>
		 
		</select> 
				 
		<?php    
	}


	/**
	 * Register custom metaboxes
	 *
	 * @since  0.1.0
	 */
	public function chch_sf_metabox( $post ) {
		remove_meta_box( 'slugdiv', 'chch-slider', 'normal' );
	 	remove_meta_box( 'submitdiv', 'chch-slider', 'normal' );
		
		$post_boxes = array(
			'chch-metabox-settings',
			'chch-metabox-advanced',
		);

		foreach($post_boxes as $post_box)
		{
			add_filter( 'postbox_classes_chch-slider_'.$post_box,array( $this, 'chch_sf_add_metabox_classes') );
		}
	}

	/**
	 * Add metabox class for tabs
	 *
	 * @since  0.1.0
	 */
	function chch_sf_add_metabox_classes( $classes ) {
 		array_push( $classes, "cc-pu-tab-2 cc-pu-tab" );
		return $classes;
	}


	/**
	 * Remove help tabs from post view.
	 *
	 * @since     1.0.7
	 *
	 */
	function chch_sf_remove_help_tabs($old_help, $screen_id, $screen){
		if ( 'post' == $screen->base && 'chch-slider' === $screen->post_type) {
			$screen->remove_help_tabs();
			return $old_help;
		}
	}

	/**
	 * Return list of templates
	 *
	 * @since     1.0.0
	 *
	 * @return    array - template list
	 */
	public function get_templates() {
		if ( ! class_exists( 'PluginMetaData' ) )
			require_once( CHCH_SF_PLUGIN_DIR . 'admin/includes/PluginMetaData.php' );
		$pmd = new PluginMetaData;
		$pmd->scan(CHCH_SF_PLUGIN_DIR . 'public/templates');
		return $pmd->plugin;
	}


	/**
	 * Add Templates View
	 *
	 * @since  0.1.0
	 */
	public function chch_sf_templates_view( $post ) {

		$screen = get_current_screen();
		if ( 'post' == $screen->base && 'chch-slider' === $screen->post_type) {

			include(CHCH_SF_PLUGIN_DIR . '/admin/views/templates.php' );
			include(CHCH_SF_PLUGIN_DIR . '/admin/views/slides.php' );

		}
	}


	/**
	 * Save Post Type Meta
	 *
	 * @since  0.1.0
	 */
	function chch_sf_save_pop_up_meta( $post_id, $post, $update ) {
		
		if ( !isset($_POST['chch_sf_save_nonce']) || ! wp_verify_nonce($_POST['chch_sf_save_nonce'],'chch_sf_save_nonce_'.$post_id) ) {
			return;
		}

		if(defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) return;

		if ( $post->post_type != 'chch-slider' ) {
			return;
		}

		$template =  $_POST['_chch_sf_template'];

		if(!empty($template)) { 
			update_post_meta( $post_id, '_chch_sf_template', sanitize_text_field( $template) );
			update_post_meta( $post_id, '_chch_slides', $_POST['_chch_slides']);
			
			$template_data = array();
			
			$template_data['theme']= array(
				'type' => sanitize_text_field($_POST['_'.$template.'_theme_type'])  
			); 
			
			$template_data['arrows']= array(
				'hide' => sanitize_text_field($_POST['_'.$template.'_arrows_hide'])  
			); 
			
			$template_data['pagination']= array(
				'hide' => sanitize_text_field($_POST['_'.$template.'_pagination_hide'])  
			);
			
			$template_data['title']= array(
				'color' => sanitize_text_field($_POST['_'.$template.'_title_color'])  
			);
			
			$template_data['desc']= array(
				'color' => sanitize_text_field($_POST['_'.$template.'_desc_color'])  
			);
			
			$template_data['url']= array(
				'color' => sanitize_text_field($_POST['_'.$template.'_url_color'])  
			);
			
			update_post_meta($post_id, '_'.$template.'_template_data', $template_data);	
		}
	}


	/**
	 * Include google fonts
	 *
	 * @since  0.1.0
	 */
	public function chch_sf_admin_head_scripts() {
	 	$screen = get_current_screen();
		if ( 'post' == $screen->base && 'chch-slider' === $screen->post_type) {

			$js ="<link href='http://fonts.googleapis.com/css?family=Playfair+Display:400,700,900|Lora:400,700|Open+Sans:400,300,700|Oswald:700,300|Roboto:400,700,300|Signika:400,700,300' rel='stylesheet' type='text/css'>";
			echo $js;
		}
	 }

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 */
	public function chch_sf_enqueue_admin_scripts() {

		$screen = get_current_screen();
		if ( 'post' == $screen->base && 'chch-slider' === $screen->post_type) {
			wp_enqueue_style('wp-color-picker' );
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-slider');

			wp_enqueue_media();

			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), ChChSlider::VERSION );

			wp_enqueue_script( $this->plugin_slug .'-admin-scripts', plugins_url( 'assets/js/chch-admin.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), ChChSlider::VERSION );
			wp_localize_script( $this->plugin_slug .'-admin-scripts', 'chch_sf_ajax_object', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'chch_slider_url' => CHCH_SF_PLUGIN_URL) );

			wp_enqueue_style( $this->plugin_slug .'-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css', null, ChChSlider::VERSION,'all' );

			if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/css/defaults.css'))
			{
				wp_enqueue_style($this->plugin_slug .'_template_defaults', CHCH_SF_PLUGIN_URL . 'public/templates/css/defaults.css', null, ChChSlider::VERSION, 'all');
			}

			if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/css/fonts.css'))
			{
				wp_enqueue_style($this->plugin_slug .'_template_fonts', CHCH_SF_PLUGIN_URL . 'public/templates/css/fonts.css', null, ChChSlider::VERSION, 'all');
			}
		}

	}


	/**
	 * Load preview by ajax
	 *
	 */
	function chch_sf_load_preview_module() {

		$template = $_POST['template'];
		$popup = $_POST['id'];

		$template = new ChChSliderTemplate($template, $popup);
		$template->get_preview_template();
		die();
	}
  
  /**
   * ChChSliderProAdmin::chch_sp_register_buttons()
   * 
   * @param array $buttons
   * @return array $buttons
   */
  function chch_sf_shortcocde_button( $buttons ) {
      $button_icon = CHCH_SF_PLUGIN_URL.'admin/assets/img/icon.png';
      echo '<a href="#TB_inline?width=600&height=550&inlineId=chch-sf-shortcode-list" class="button thickbox"><span class="wp-media-buttons-icon" style="background: url('.$button_icon.');background-repeat: no-repeat; background-position: left bottom;"></span>Add slider</a>';
  }
  
  function chch_sf_shortcode_modal(){
    
    $sliders = $this->plugin->get_sliders();
    $modal = '<div id="chch-sf-shortcode-list" style="display:none;"><p>
          <select id="chch-sf-sliders-select">';
    foreach($sliders as $slider){
      $slider_title = get_the_title($slider) ? get_the_title($slider) : $slider;
      $modal .= '<option value="'.$slider.'">'.$slider_title.'</option>';  
    }
    
    $modal .= '</select> <a class="button" href="#" id="chch-sf-insert-shortcode">Insert shortcode</a></p> </div>';
    $modal .= ' <script type="text/javascript">
				jQuery(document).ready(function($) {
				  $("#chch-sf-insert-shortcode").on("click", function() { 
				  slider_id = $("#chch-sf-sliders-select option:selected").val();
					if(window.parent.tinyMCE && window.parent.tinyMCE.activeEditor)
					{
						window.parent.send_to_editor("[slidercc id="+slider_id+"]");
					}
					tb_remove();
				  })
				});
</script>';
    echo $modal;
  }
  
  /** 
   *
   * Function returns array with all pages/posts form all post types
   *
   * @return array $all_posts - list of all pages witch page id - [id] and page title - [title]
   */
  private function chch_sf_get_pages() {

    $args = array( 'public' => true, '_builtin' => true);

    $post_types = get_post_types( $args);

    $args = array(
      'post_type' => $post_types,
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC');

    $post_list = get_posts( $args);

    $all_posts = array();

    if ( $post_list):
      foreach ( $post_list as $post):
        $all_posts[$post->ID] = get_the_title( $post->ID);
      endforeach;
    endif;

    return $all_posts;
  }
}
