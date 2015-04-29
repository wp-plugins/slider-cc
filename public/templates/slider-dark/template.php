<?php
/**
 *
 * id: slider-dark
 * title: Simple Slider - Dark
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="ccs-wrapper slidercc dark arrows-xs" id="chch-slidercc-<?php echo $this->post_id; ?>">
	<?php echo $this->get_slides('slider');?>
</section>

