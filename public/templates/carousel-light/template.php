<?php
/**
 *
 * id: carousel-light
 * title: Simple Carousel - Light
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="carouselcc light arrows-xs ccs-wrapper scc-wrapper" id="chch-carouselcc-<?php echo $this->post_id; ?>"  style="max-width: <?php echo $width = get_post_meta($this->post_id, '_chch_slider_width',true) ? get_post_meta($this->post_id, '_chch_slider_width',true)  : '600'; ?>px">
 
    <?php echo $this->get_slides('carousel');?>
 
</section>
