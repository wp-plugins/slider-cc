<?php
/**
 *
 * id: slider-light
 * title: Simple Slider - Light
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="ccs-wrapper slidercc light arrows-xs" id="chch-slidercc-<?php echo $this->post_id; ?>">
	<?php echo $this->get_slides('slider');?>
</section>

