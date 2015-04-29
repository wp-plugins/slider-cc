<?php
/**
 *
 * id: slidersb-light
 * title: Slider with Side Box - Light
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="ccs-wrapper slidersb arrows-xs light" id="chch-slidercc-<?php echo $this->post_id; ?>">
	<?php echo $this->get_slides('slidersb');?>
</section>

