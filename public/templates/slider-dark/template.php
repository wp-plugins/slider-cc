<?php
/**
 *
 * id: slider-dark
 * title: Simple Slider - Dark
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="ccs-wrapper slidercc dark arrows-xs" id="chch-slidercc-<?php echo $this->post_id; ?>" style="max-width: <?php echo $width = get_post_meta($this->post_id, '_chch_slider_width',true) ? get_post_meta($this->post_id, '_chch_slider_width',true)  : '600'; ?>px">
	<?php echo $this->get_slides('slider');?>
</section>

