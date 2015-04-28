<?php
/**
 *
 * id: sliderwt-light
 * title: Slider with Thumbnails - Light
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="sliderwt-wrapper ccs-wrapper sliderwt arrows-md light" style="max-width: <?php echo $width = get_post_meta($this->post_id, '_chch_slider_width',true) ? get_post_meta($this->post_id, '_chch_slider_width',true)  : '600'; ?>px">
  <div id="chch-sliderwt1-<?php echo $this->post_id; ?>"  class="scc-wrapper" style="max-width: <?php echo $width = get_post_meta($this->post_id, '_chch_slider_width',true) ? get_post_meta($this->post_id, '_chch_slider_width',true)  : '600'; ?>px">
    <?php echo $this->get_slides('sliderwt');?>
  </div>
  <div id="chch-sliderccth1-<?php echo $this->post_id; ?>" class="scc-wrapper sliderccth light">
    <?php echo $this->get_slides('thumbs');?>
  </div>
</section>
