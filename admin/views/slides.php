<?php $slides = get_post_meta(get_the_ID(),'_chch_slides', true);?>

<div class="wrap cc-pu-tab-2 cc-pu-tab">

		<?php if($slides): $i = 0;?>
			<table class="chch-slides-repeater">
			<?php foreach($slides as $slide): ?>
				<tr class="chch-single-slide">
					<td class="chch-slide-counter chch-repeater-field"><?php echo $i+1; ?></td>
					<td class="chch-slide-image  chch-repeater-field">
						<div class="chch-image-preview">
							<?php if($slide['image']):?>
							<img src="<?php echo $slide['image']; ?>" />
							<?php endif;?>
						</div>
						<input type="hidden" name="_chch_slides[<?php echo $i; ?>][image]" class="chch-slide-image-url" value="<?php echo $slide['image']; ?>">
						<a class="chch-slide-image-upload button" type="button">Upload Image</a>
					</td>
					<td class="chch-slide-content chch-repeater-field">
						<div class="chch-repeater-text-field">
							<label>Title:</label>
							<input type="text" name="_chch_slides[<?php echo $i; ?>][title]" value="<?php echo $slide['title']; ?>" />
						</div>

						<div class="chch-repeater-text-field chch-repeater-desc-field">
							<label>Description:</label>   
              <?php 
								$editor_params = array(
									'textarea_name' =>'_chch_slides['.$i.'][caption]',
									'editor_class' => 'chch-repeater-wysiwyg',
									'media_buttons' => false,
									'quicktags' => false, 
									'tinymce' => array(
										'toolbar1'=> ', bold,italic,underline,link,unlink,forecolor,undo,redo',
										'toolbar2'=> '',
										'toolbar3'=> ''
									) 
								);
								wp_editor(  $slide['caption'], 'chch-slide-description-'.$i, $editor_params );
							?> 
						</div>

						<div class="chch-repeater-text-field">
							<label>URL:</label>
							<input type="text" name="_chch_slides[<?php echo $i; ?>][url]" value="<?php echo $slide['url']; ?>" />
						</div>
            
            <div class="chch-repeater-text-field target-blank">
              <label>Open in new window</label>
							<input type="checkbox" class="chch-repeater-checkbox" name="_chch_slides[<?php echo $i; ?>][blank]" <?php echo $slide['blank'] ? 'checked' : ''; ?> />
						</div>
					</td>
					<td class="chch-slide-control chch-repeater-field">
						<a class="chch-delete-slide dashicons-before">Delete</a>
					</td>
				</tr> <!--chch-single-slide-->
				<?php $i++;?>
			<?php endforeach; ?>

			</table> <!--chch-slides-repeater-->
      <p class="shortcode-field">Use the following shortcode to display this slider inside a post, page or text widget: <?php echo '[slidercc id="'.$post->ID.'"]'; ?></p>
			<a class="button button-primary button-large right" href="#" id="chch-add-slide" data-slides-number="<?php echo $i; ?>">Add New Slide</a>
      <input name="publish" id="publish-slides" class="button button-secondary button-large" value="Save" accesskey="p" type="submit">
		<?php else: ?>
		<table class="chch-slides-repeater">
			<tr class="chch-single-slide">
					<td class="chch-slide-counter chch-repeater-field">1</td>
					<td class="chch-slide-image  chch-repeater-field">
						<div class="chch-image-preview">
						</div>
						<input type="hidden" name="_chch_slides[0][image]" class="chch-slide-image-url" >
						<a class="chch-slide-image-upload button" type="button">Upload Image</a>
					</td>
					<td class="chch-slide-content chch-repeater-field">
						<div class="chch-repeater-text-field">
							<label>Title:</label>
							<input type="text" name="_chch_slides[0][title]" />
						</div>

						<div class="chch-repeater-text-field chch-repeater-desc-field">
							<label>Description:</label>
              
							<?php 
								$editor_params = array(
									'textarea_name' =>'_chch_slides[0][caption]',
									'editor_class' => 'chch-repeater-wysiwyg',
									'media_buttons' => false,
									'quicktags' => false, 
									'tinymce' => array(
										'toolbar1'=> ', bold,italic,underline,link,unlink,forecolor,undo,redo',
										'toolbar2'=> '',
										'toolbar3'=> ''
									) 
								);
								wp_editor( '', 'chch-slide-description', $editor_params );
							?>
						</div>

						<div class="chch-repeater-text-field">
							<label>URL:</label>
							<input type="text" name="_chch_slides[0][url]" />
						</div>
            
             <div class="chch-repeater-text-field target-blank">
              <label>Open in new window</label>
							<input type="checkbox" class="chch-repeater-checkbox" name="_chch_slides[0][blank]" />
						</div>
					</td>
					<td class="chch-slide-control chch-repeater-field">
						<a class="chch-delete-slide dashicons-before">Delete</a>
					</td>
				</tr> <!--chch-single-slide-->
		</table> <!--chch-slides-repeater-->
		<p class="shortcode-field">Use the following shortcode to display this slider inside a post, page or text widget: <?php echo '[slidercc id="'.$post->ID.'"]'; ?></p>
		<a class="button button-primary button-large right" href="#" id="chch-add-slide" data-slides-number="0">Add New Slide</a>
		<input name="publish" id="publish-slides" class="button button-secondary button-large" value="Save" accesskey="p" type="submit">
		<?php endif; ?>
</div><!--cc-pu-tab-->

