<?php
/**
 *
 * id: carousel-dark
 * title: Simple Carousel - Dark
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="carouselcc dark arrows-xs ccs-wrapper scc-wrapper dark" id="chch-carouselcc-<?php echo $this->post_id; ?>">

    <?php echo $this->get_slides('carousel');?>

</section>
