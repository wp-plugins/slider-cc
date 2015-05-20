<?php $templates = $this->get_templates(); ?>
<?php $active = get_post_meta(get_the_ID(),'_chch_sf_template', true) ? get_post_meta(get_the_ID(),'_chch_sf_template', true) : 'carousel-dark';?> 
<style> 

	.cc-pu-tab-2,.hide-section {
		display:none;	
	}
	#poststuff .theme-browser .theme {
		cursor:default !important;	
	}
	
	.theme-browser .theme .theme-screenshot img {
		position:relative !important;
		display:block !important;	
	}
	
	.theme-browser .theme .theme-screenshot:after {
		padding:0 !important;
		display:none;	
	}
	
	#poststuff .theme-browser .theme .theme-name {
	    font-size: 15px;
	    font-weight: 600;
	    height: 18px;
	    margin: 0;
	    padding: 15px;
	    -webkit-box-shadow: inset 0 1px 0 rgba(0,0,0,.1);
	    box-shadow: inset 0 1px 0 rgba(0,0,0,.1);
	    overflow: hidden;
	    white-space: nowrap;
	    text-overflow: ellipsis;
	    background: #fff;
	    background: rgba(255,255,255,.65);
	}

	#poststuff .theme-browser .theme.active .theme-name {
	    background: #2f2f2f;
	    color: #fff;
	    padding-right: 110px;
	    font-weight: 300;
	    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.5);
	    box-shadow: inset 0 1px 1px rgba(0,0,0,.5);
	}

	#poststuff .theme-browser .theme .more-details {
		text-decoration: none;
		transition: all .2s ease-in-out;
	}

	#poststuff .theme-browser .theme .more-details:hover {
		background: rgba(46, 162, 204,.9);
		border-color: #0074a2;
		box-shadow: inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);
	}
</style>
<div class="themes-php cc-pu-tab cc-pu-tab-1">
	<div class="wrap"> 
		<h2>Templates:<span class="theme-count title-count"><?php echo count($templates); ?></span></h2>
		 
		<?php if(count($templates)): ?>
			<div class="theme-browser rendered">
				<div class="themes">
					<?php if(isset($templates[$active])): ?>
						<?php $template = $templates[$active]; ?>
						<div class="theme active">
							<div class="theme-screenshot">
								<?php if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/' . $template['id'] . '/screenshot.png')): ?>
									<img src="<?php echo CHCH_SF_PLUGIN_URL . 'public/templates/' .  $template['id'] . '/screenshot.png'; ?>" alt="" />
								<?php else: ?>
									<img src="<?php echo CHCH_SF_PLUGIN_URL . 'public/templates/' . $template['id'] . '/screenshot.png'; ?>" alt="" />
								<?php endif; ?>
							</div>
							<h3 class="theme-name"><span>Active:</span>	<?php echo $template['title']; ?></h3>
							<div class="theme-actions">
								<a  href="#" class="cc-pu-template-edit button button-primary" data-template="<?php echo $template['id']; ?>"  data-postid="<?php echo get_the_ID(); ?>" data-nounce="<?php echo wp_create_nonce('cc-pu-preview-'.$template['id']); ?>">Customize</a>
							</div> 
              <?php 
						 	$preview = new ChChSliderPreview($template['id'],$template['title']); 
							$preview->build_preview();
						?> 
						</div> 
					<?php endif; ?>
					
					<?php foreach($templates as $template): if($template['id'] !== $active): ?>
						<div class="theme" tabindex="0">
							<div class="theme-screenshot">
								<?php if(file_exists(CHCH_SF_PLUGIN_DIR . 'public/templates/' .  $template['id'] . '/screenshot.png')): ?>
									<img src="<?php echo CHCH_SF_PLUGIN_URL . 'public/templates/' .  $template['id'] . '/screenshot.png'; ?>" alt="" />
								<?php endif; ?>
							</div>
							<h3 class="theme-name"><?php echo $template['title']; ?></h3>
							<div class="theme-actions"> 
									<a  href="#" class="cc-pu-template-acivate button button-primary" data-template="<?php echo $template['id']; ?>">Activate</a> 
									<a  href="#" class="cc-pu-template-edit button button-primary" data-template="<?php echo $template['id']; ?>" data-postid="<?php echo get_the_ID(); ?>" data-nounce="<?php echo wp_create_nonce('cc-pu-preview-'.$template['id']); ?>">Customize</a>
							</div> 
               
						<?php 
						 	$preview = new ChChSliderPreview($template['id'],$template['title']); 
							$preview->build_preview();
						?> 
						</div>
						
						 
						
					<?php endif; endforeach; ?> 
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
<input type="hidden" name="_chch_sf_template" id="_chch_sf_template" value="<?php echo $active; ?>"/> 
<?php wp_nonce_field('chch_sf_save_nonce_'.get_the_ID(),'chch_sf_save_nonce'); ?>
 