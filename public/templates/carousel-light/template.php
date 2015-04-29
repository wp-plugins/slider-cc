<?php
/**
 *
 * id: carousel-light
 * title: Simple Carousel - Light
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="carouselcc light arrows-xs ccs-wrapper scc-wrapper" id="chch-carouselcc-<?php echo $this->post_id; ?>">
 
    <?php echo $this->get_slides('carousel');?>
 
</section>
