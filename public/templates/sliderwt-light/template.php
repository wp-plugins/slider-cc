<?php
/**
 *
 * id: sliderwt-light
 * title: Slider with Thumbnails - Light
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="sliderwt-wrapper ccs-wrapper sliderwt arrows-md light" id="sliderwt-wrapper-<?php echo $this->post_id; ?>">
  <div id="chch-sliderwt1-<?php echo $this->post_id; ?>"  class="scc-wrapper">
    <?php echo $this->get_slides('sliderwt');?>
  </div>
  <div id="chch-sliderccth1-<?php echo $this->post_id; ?>" class="scc-wrapper sliderccth light">
    <?php echo $this->get_slides('thumbs');?>
  </div>
</section>
