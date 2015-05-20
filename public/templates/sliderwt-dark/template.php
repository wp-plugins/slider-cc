<?php
/**
 *
 * id: sliderwt-dark
 * title: Slider with Thumbnails - Dark
 *
 */

$slider_options = $this->get_template_options();
?>

<section class="sliderwt-wrapper ccs-wrapper sliderwt arrows-md dark" id="sliderwt-wrapper-<?php echo $this->post_id; ?>">
  <div id="chch-sliderwt1-<?php echo $this->post_id; ?>"  class="scc-wrapper">
    <?php echo $this->get_slides('sliderwt');?>
  </div>
  <div id="chch-sliderccth1-<?php echo $this->post_id; ?>" class="scc-wrapper sliderccth dark">
    <?php echo $this->get_slides('thumbs');?>
  </div>
</section>
