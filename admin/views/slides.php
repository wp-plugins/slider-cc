<?php $slides = get_post_meta(get_the_ID(),'_chch_slides', true);?>

<div class="wrap cc-pu-tab-2 cc-pu-tab">

		<?php if($slides): $i = 0;?>
			<table class="chch-slides-repeater">
      <tbody>
			<?php foreach($slides as $slide): ?>
      
				<tr class="chch-single-slide">
					<td class="chch-slide-counter chch-repeater-field"><?php echo $i+1; ?></td>
					<td class="chch-slide-image  chch-repeater-field">
						<div class="chch-image-preview">
							<?php if($slide['image']):?>
							<img src="<?php echo $slide['image']; ?>" />
							<?php endif;?>
						</div>
						<input type="hidden" name="_chch_slides[<?php echo $i; ?>][image]" class="chch-slide-image-url chch-repeater-input" value="<?php echo $slide['image']; ?>">
						<a class="chch-slide-image-upload button" type="button">Upload Image</a>
					</td>
					<td class="chch-slide-content chch-repeater-field">
            <div class="chch-repeater-text-field">
							<label>Page/Post:</label>
							<input type="checkbox" name="_chch_slides[<?php echo $i; ?>][page]" value="on" class="chch-repeater-page chch-repeater-input" <?php echo isset($slide['page']) ? 'checked' : ''; ?>/>

              <?php
                $pages = $this->chch_sf_get_pages();

                $selected_page = $slide['page_id'];
              ?>
              <select  name="_chch_slides[<?php echo $i; ?>][page_id]" class=" chch-repeater-input">
                <?php
                  foreach($pages as $id => $title){

                    $selected = '';
                    if($id == $selected_page){
                      $selected = 'selected';
                    }

                    printf('<option value="%s" %s disabled>%s (AVAILABLE IN PRO)</option>',$id,$selected,$title);
                  }
                ?>
              </select>
              <p class="chch-repeater-field-desc <?php echo isset($slide['page']) ? '' : 'hide-section'; ?>"><a href="http://ch-ch.org/sliderpro" target="_blank">AVAILABLE IN PRO</a></p>
						</div>
						<div class="chch-repeater-text-field">
							<label>Title:</label>
							<input type="text" name="_chch_slides[<?php echo $i; ?>][title]" value="<?php echo $slide['title']; ?>" class="chch-repeater-input"/>
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
							<input type="text" name="_chch_slides[<?php echo $i; ?>][url]" value="<?php echo $slide['url']; ?>" class="chch-repeater-input"/>
						</div>
            
            <div class="chch-repeater-text-field target-blank">
              <label>Open in new window</label>
							<input type="checkbox" class="chch-repeater-checkbox chch-repeater-input" name="_chch_slides[<?php echo $i; ?>][blank]" <?php echo $slide['blank'] ? 'checked' : ''; ?> />
						</div>
					</td>
					<td class="chch-slide-control chch-repeater-field">
						<a class="chch-delete-slide dashicons-before">Delete</a>
					</td>
				</tr> <!--chch-single-slide-->
				<?php $i++;?>
			<?php endforeach; ?>
      </tbody>
			</table> <!--chch-slides-repeater-->
      <p class="shortcode-field">Use the following shortcode to display this slider inside a post, page or text widget: <?php echo '[slidercc id="'.$post->ID.'"]'; ?></p>
			<a class="button button-primary button-large right" href="#" id="chch-add-slide" data-slides-number="<?php echo $i; ?>">Add New Slide</a>
      <input name="publish" id="publish-slides" class="button button-secondary button-large" value="Save" accesskey="p" type="submit" />
		<?php else: ?>
		<table class="chch-slides-repeater">
    <tbody>
			<tr class="chch-single-slide">
					<td class="chch-slide-counter chch-repeater-field">1</td>
					<td class="chch-slide-image  chch-repeater-field">
						<div class="chch-image-preview">
						</div>
						<input type="hidden" name="_chch_slides[0][image]" class="chch-slide-image-url chch-repeater-input" />
						<a class="chch-slide-image-upload button" type="button">Upload Image</a>
					</td>
					<td class="chch-slide-content chch-repeater-field">
            <div class="chch-repeater-text-field">
							<label>Page/Post:</label>
							<input type="checkbox" name="_chch_slides[0][page]" value="on" class="chch-repeater-page chch-repeater-input" />

              <?php
                $pages = $this->chch_sf_get_pages();

                $selected_page = $slide['page_id'];
              ?>
              <select  name="_chch_slides[0][page_id]" class=" chch-repeater-input">
                <?php
                  foreach($pages as $id => $title){

                    $selected = '';
                    if($id == $selected_page){
                      $selected = 'selected';
                    }

                    printf('<option value="%s" %s disabled>%s</option>',$id,$selected,$title);
                  }
                ?>
              </select>
              <p class="chch-repeater-field-desc hide-section"><a href="http://ch-ch.org/sliderpro" target="_blank">AVAILABLE IN PRO</a></p>
						</div>
						<div class="chch-repeater-text-field">
							<label>Title:</label>
							<input type="text" name="_chch_slides[0][title]" class="chch-repeater-input"/>
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
							<input type="text" name="_chch_slides[0][url]"  class="chch-repeater-input"/>
						</div>
            
             <div class="chch-repeater-text-field target-blank">
              <label>Open in new window</label>
							<input type="checkbox" class="chch-repeater-checkbox chch-repeater-input" name="_chch_slides[0][blank]"  />
						</div>
					</td>
					<td class="chch-slide-control chch-repeater-field">
						<a class="chch-delete-slide dashicons-before">Delete</a>
					</td>
				</tr> <!--chch-single-slide-->
        </tbody>
		</table> <!--chch-slides-repeater-->
		<p class="shortcode-field">Use the following shortcode to display this slider inside a post, page or text widget: <?php echo '[slidercc id="'.$post->ID.'"]'; ?></p>
		<a class="button button-primary button-large right" href="#" id="chch-add-slide" data-slides-number="0">Add New Slide</a>
		<input name="publish" id="publish-slides" class="button button-secondary button-large" value="Save" accesskey="p" type="submit">
		<?php endif; ?>
</div><!--cc-pu-tab-->

