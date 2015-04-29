<?php
/**
 *
 * id: slidersb-dark
 * title: Slider with Side Box - Dark
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="ccs-wrapper slidersb arrows-xs dark" id="chch-slidercc-<?php echo $this->post_id; ?>">
	    <?php echo $this->get_slides('slidersb');?>
</section>

