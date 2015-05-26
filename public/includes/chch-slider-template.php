<?php
/**
 * Slider CC
 *
 * @package   ChChSliderTemplate
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014
 */

/**
 * @package ChChSliderTemplate
 * @author  Chop-Chop.org <shop@chop-chop.org>
 */
class ChChSliderTemplate {

	private $template,  $post_id = 0;

	function __construct($template = NULL, $post_id = 0) {
		$this->plugin = ChChSlider::get_instance();
		$this->plugin_slug = $this->plugin->get_plugin_slug();
		if($template != NULL){
			$this->template = $template;
			$this->post_id = $post_id;
		} else {
			$this->template = get_post_meta($post_id, '_chch_sf_template',true);
			$this->post_id = $post_id;
		}
	}



	function get_template_options(){
		if(!$options = get_post_meta($this->post_id, '_'.$this->template.'_template_data',true)){
			if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/'.$this->template.'/defaults.php'))
			{
				$options = (include(CHCH_SF_PLUGIN_DIR . 'public/templates/'.$this->template.'/defaults.php'));
			}
		}

		return $options;
	}

	function get_template_option($base, $option){

		$all_options = $this->get_template_options();

		if(isset($all_options[$base][$option])){

			return $all_options[$base][$option];

		} elseif(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/'.$this->template.'/defaults.php')) {

			$default_options = (include(CHCH_SF_PLUGIN_DIR . 'public/templates/'.$this->template.'/defaults.php'));

			if(isset($default_options[$base][$option])){
				return $default_options[$base][$option];
			}
		}

		return '';
	}


	function get_template(){
		$template_options = $this->get_template_options();
		$id = $this->post_id;
		include(CHCH_SF_PLUGIN_DIR . 'public/templates/'.$this->template.'/template.php' );
	}

	function get_preview_template(){
		$template_options = $this->get_template_options();
		$id = $this->post_id;
		include(CHCH_SF_PLUGIN_DIR . 'public/templates/'.$this->template.'/preview.php' );
	}

	private function get_slides($template){

		$slides = get_post_meta($this->post_id, '_chch_slides',true);

		if($slides):

			$slides_order = get_post_meta($this->post_id, '_chch_slider_order',true);
			if($slides_order == 'random') {
				shuffle($slides);
			}
		?>
      <ul class="slides">
      	<?php foreach($slides as $slide):?>
        <li>
        	<?php $this->get_slide_content($template,$slide);?>
        </li>
        <?php endforeach;?>
      </ul>
    <?php
		endif;
	}


	/**
	 * ChChSliderTemplate::get_slide_content()
	 *
	 * @param string $template
	 * @return string - slide content html
	 */
	private function get_slide_content($template, $slide){

    switch($template){
      case 'slider':
      case 'carousel':
        if($slide_url = $slide['url']):?>
          <a href="<?php echo $slide_url; ?>" <?php echo $slide['blank'] ? 'target="_blank"' : ''; ?>>
        <?php endif;?>
          <img src="<?php echo $slide['image']; ?>" alt="slide">
        <?php if($slide_url = $slide['url']):?>
          </a>
        <?php endif;?>
          <div class="slidercc-title">
            <h2><?php echo $slide['title']; ?></h2>
          </div>
          <?php if($desc = $slide['caption']):?>
          <div class="slidercc-desc">
            <p><?php echo $desc; ?></p>
          </div>
          <?php endif;
      break;

      case 'sliderwt':
      case 'slidersb':
         if($slide_url = $slide['url']):?>
          	<a href="<?php echo $slide_url; ?>" <?php echo $slide['blank'] ? 'target="_blank"' : ''; ?>>
          <?php endif;?>
          <img src="<?php echo $slide['image']; ?>" alt="slide">
          <?php if($slide_url = $slide['url']):?>
          	</a>
          <?php endif;?>
          <div class="slidercc-title">
          	<?php if($title = $slide['title']):?>
            <h2><?php echo $slide['title']; ?></h2>
         	<?php endif;?>
          <?php if($desc = $slide['caption']):?>
          <div class="slidercc-desc">
            <p><?php echo $desc; ?></p>
          </div>
          <?php endif;?>
           </div>
          <?php
      break;

      case 'thumbs': ?>
          <img src="<?php echo $slide['image']; ?>" alt="slide">
          <?php
      break;
    }
	}


	function build_css(){
		$options = $this->get_template_options();
		$prefix = '';
    
    $css = '<style>'; 
    
    $slider_position = 'margin:0 auto !important;';  
		
		switch($this->template):
			case 'slider-dark':
			case 'slider-light':
			case 'slidersb-light':
			case 'slidersb-dark':
				$prefix = '#chch-slidercc-'.$this->post_id.' '; 
			break;

			case 'sliderwt-dark':
			case 'sliderwt-light': 
				$prefix = '#chch-sliderwt1-'.$this->post_id.' ';  
        $css .= '#sliderwt-wrapper-'.$this->post_id.'  {
    			max-width: '. get_post_meta($this->post_id, '_chch_slider_width',true).'px; 
          '.$slider_position.' 
    		}';  
			break;

			case 'carousel-dark':
			case 'carousel-light':
				$prefix = '#chch-carouselcc-'.$this->post_id.' ';   
			break;
		endswitch;
		
		 
    if($this->template != 'sliderwt-dark' && $this->template != 'sliderwt-light'){ 
        
      $css .= $prefix.'  {
  			max-width:'. get_post_meta($this->post_id, '_chch_slider_width',true).'px;
        '.$slider_position.' 
  		}';   
    }
      
    
		$css .= $prefix.' .slidercc-title  {
			color: '.$options['title']['color'].' !important; 
		}'; 
		
		$css .= $prefix.' .slidercc-desc  {
			color: '.$options['desc']['color'].' !important; 
		}'; 
		
		$css .= $prefix.' .slidercc-desc a  {
			color: '.$options['url']['color'].' !important; 
		}'; 
		  
		$css .= '</style>';
	
		return $css; 
	}

	function build_js(){
		$slider_options = $this->get_template_options();
		$pagination = $slider_options['pagination']['hide'] ? 'false' : 'true';
		$arrows = $slider_options['arrows']['hide'] ? 'false' : 'true';

		$animation_type = get_post_meta($this->post_id, '_chch_slider_animation',true);
		$mode = get_post_meta($this->post_id, '_chch_slider_effect',true);
		$speed = get_post_meta($this->post_id, '_chch_slider_speed',true);
		$interval = get_post_meta($this->post_id, '_chch_slider_interval',true);
		$keyboard = get_post_meta($this->post_id, '_chch_slider_keyboard',true) ? 'true' : 'false';
		$pause = get_post_meta($this->post_id, '_chch_slider_pause',true) ? 'true' : 'false';
		$autoPlay = get_post_meta($this->post_id, '_chch_slider_autoplay',true) ? $speed : 'false';
		$touch = get_post_meta($this->post_id, '_chch_slider_touch',true) ? 'true' : 'false';
		$mousewheel = get_post_meta($this->post_id, '_chch_slider_mousewheel',true) ? 'true' : 'false'; 

		$js = "<script>\n\tjQuery(function($) {\n";

		switch($this->template):
			case 'slider-dark':
			case 'slider-light':
			case 'slidersb-light':
			case 'slidersb-dark':
      
				$js .= sprintf("\t\t$('#chch-slidercc-%s').slidercc({\n",$this->post_id);
				$js .= sprintf("\t\t\tmode: '%s' ,\n",$mode);
        $js .= sprintf("\t\t\tanimationSpeed: %s ,\n",$speed); 
        $js .= sprintf("\t\t\tpagination: %s ,\n",$pagination);  
        $js .= sprintf("\t\t\tarrows: %s ,\n",$arrows); 
        $js .= sprintf("\t\t\tanimationMethod: \"%s\" ,\n",$animation_type); 
        $js .= sprintf("\t\t\tkeyboard: %s ,\n",$keyboard); 
        $js .= sprintf("\t\t\tpauseOnHover: %s ,\n",$pause); 
        $js .= sprintf("\t\t\tautoPlay: %s ,\n",$autoPlay);  
        $js .= sprintf("\t\t\tmousewheel: %s ,\n",$mousewheel); 
        $js .= sprintf("\t\t\ttouch: %s ,\n",$touch);  
				$js .= "\t\t});\n";
        
			break;

			case 'sliderwt-dark':
			case 'sliderwt-light':
      
        $js .= sprintf("\t\t$('#chch-sliderwt1-%s').slidercc({\n",$this->post_id);
        $js .= sprintf("\t\t\tsynchronize: \"#chch-sliderccth1-%s\" ,\n",$this->post_id);
				$js .= sprintf("\t\t\tmode: '%s' ,\n",$mode);
        $js .= sprintf("\t\t\tanimationSpeed: %s ,\n",$speed); 
        $js .= sprintf("\t\t\tpagination: %s ,\n",$pagination);  
        $js .= sprintf("\t\t\tarrows: %s ,\n",$arrows); 
        $js .= sprintf("\t\t\tanimationMethod: \"%s\" ,\n",$animation_type); 
        $js .= sprintf("\t\t\tkeyboard: %s ,\n",$keyboard); 
        $js .= sprintf("\t\t\tpauseOnHover: %s ,\n",$pause); 
        $js .= sprintf("\t\t\tautoPlay: %s ,\n",$autoPlay);  
        $js .= sprintf("\t\t\tmousewheel: %s ,\n",$mousewheel); 
        $js .= sprintf("\t\t\ttouch: %s ,\n",$touch);  
				$js .= "\t\t});\n\n"; 
        
        $slide_width ='80';
        
        if($this->template == 'sliderrt-dark'){
          $slide_width ='60';  
        }
        
        $js .= sprintf("\t\t$('#chch-sliderccth1-%s').slidercc({\n",$this->post_id); 
        $js .= sprintf("\t\t\tmode: '%s' ,\n","carousel"); 
        $js .= sprintf("\t\t\tsynchronize: \"#chch-sliderwt1-%s\" ,\n",$this->post_id); 
        $js .= sprintf("\t\t\tremoteControl: \"#chch-sliderwt1-%s\" ,\n",$this->post_id);  
        $js .= sprintf("\t\t\tslideMargin: %s,\n",'10'); 
        $js .= sprintf("\t\t\tslideWidth: %s ,\n",$slide_width);  
        $js .= sprintf("\t\t\tpagination: %s ,\n",'false');  
        $js .= sprintf("\t\t\tarrows: %s ,\n",'false'); 
		    $js .= "\t\t});\n";  
        
			break; 

			case 'carousel-dark':
			case 'carousel-light':
				$js .= '
			$("#chch-carouselcc-'.$this->post_id.'").slidercc({
				mode: "carousel",
						animationSpeed : '.$speed.', 
						pagination : '.$pagination.',
						arrows : '.$arrows.',
						animationMethod : "'.$animation_type.'",
						keyboard : '.$keyboard.',
						pauseOnHover : '.$pause.',
						autoPlay :'.$autoPlay.',
						mousewheel : '.$mousewheel.',
						touch : '.$touch.',
            slideWidth: "33.3%",
            slideMargin: 10,
			});';
			break;
		endswitch;

		$js .= "\t});\n</script>";

		return $js;

	}

	function enqueue_template_style(){

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/js/vendor/velocity.min.js')){
			wp_enqueue_script( $this->plugin_slug .'-velocity', CHCH_SF_PLUGIN_URL . 'public/templates/js/vendor/velocity.min.js', array('jquery'),ChChSlider::VERSION ,true);
		}

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/js/vendor/modernizr.min.js')){
			wp_enqueue_script( $this->plugin_slug .'-modernizr', CHCH_SF_PLUGIN_URL . 'public/templates/js/vendor/modernizr.min.js', array('jquery'),ChChSlider::VERSION ,true);
		}

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/js/vendor/jquery.mousewheel.min.js')){
			wp_enqueue_script( $this->plugin_slug .'-mousewheel', CHCH_SF_PLUGIN_URL . 'public/templates/js/vendor/jquery.mousewheel.min.js', array('jquery'),ChChSlider::VERSION ,true);
		}

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/js/vendor/jquery.mobile-events.min.js')){
			wp_enqueue_script( $this->plugin_slug .'-mobile-events', CHCH_SF_PLUGIN_URL . 'public/templates/js/vendor/jquery.mobile-events.min.js', array('jquery'),ChChSlider::VERSION ,true);
		}

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/js/slidercc.utils.imagesLoaded.js')){
			wp_enqueue_script( $this->plugin_slug .'-imgload', CHCH_SF_PLUGIN_URL . 'public/templates/js/slidercc.utils.imagesLoaded.js', array('jquery'),ChChSlider::VERSION ,true);
		}

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/js/slidercc.core.js')){
			wp_enqueue_script( $this->plugin_slug .'-slider-core', CHCH_SF_PLUGIN_URL . 'public/templates/js/slidercc.core.js', array('jquery'),ChChSlider::VERSION ,true);
		}

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/js/slidercc.mode.carousel.js')){
			wp_enqueue_script( $this->plugin_slug .'-slider-carousel', CHCH_SF_PLUGIN_URL . 'public/templates/js/slidercc.mode.carousel.js', array('jquery'),ChChSlider::VERSION ,true);
		}

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/js/slidercc.mode.fade.js')){
			wp_enqueue_script( $this->plugin_slug .'-slider-fade', CHCH_SF_PLUGIN_URL . 'public/templates/js/slidercc.mode.fade.js', array('jquery'),ChChSlider::VERSION ,true);
		}

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/css/defaults.css')){
			wp_enqueue_style($this->plugin_slug.'-defaults', CHCH_SF_PLUGIN_URL . 'public/templates/css/defaults.css', null, ChChSlider::VERSION, 'all');

		}

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/'.$this->template.'/css/base.css')){
			wp_enqueue_style($this->plugin_slug.'-'.$this->template.'-base', CHCH_SF_PLUGIN_URL . 'public/templates/'.$this->template.'/css/base.css', null, ChChSlider::VERSION, 'all');
		}

		if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/'.$this->template.'/css/style.css')){
			wp_enqueue_style($this->plugin_slug.'-'.$this->template.'-style', CHCH_SF_PLUGIN_URL . 'public/templates/'.$this->template.'/css/style.css', null, ChChSlider::VERSION, 'all');
		}
	}

}
