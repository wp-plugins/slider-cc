jQuery(document).ready( function ($) {

	$('#post').prepend(
	 '<h2 class="nav-tab-wrapper" id="cc-pu-tabs"><a class="nav-tab nav-tab-active" href="#" title="Templates" data-target="cc-pu-tab-1"><span class="dashicons dashicons-format-image"></span> Templates</a><a class="nav-tab" href="#" title="Settings" data-target="cc-pu-tab-2"><span class="dashicons dashicons-format-gallery"></span> Slides</a></h2>'
	 );

	 $('#wpbody-content > .wrap').prepend(
	 '<a class="button button-secondary right button-hero" style="margin: 25px 0px 0px 2px; padding: 0px 20px; height: 47px;" href="https://shop.chop-chop.org/contact" target="_blank">Contact Support</a><a class="button button-primary right button-hero" href="http://ch-ch.org/pupro" style="margin: 25px 20px 0 2px;">Get Pro</a>');

	$('#cc-pu-tabs a').on('click', function(e){
		e.preventDefault();
		var target = $(this).attr('data-target');

		if(!$(this).hasClass('nav-tab-active'))
		{
			$('.cc-pu-tab').hide();
			$('#cc-pu-tabs a').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');

			$('.'+target).show();
		}
	});

	$('.cc-pu-template-acivate').on('click', function(e){
		e.preventDefault();
		var template = $(this).attr('data-template');

		$('#poststuff .theme-browser .theme.active').removeClass('active');
		var theme = $(this).closest('.theme');
		theme.addClass('active');


		$('#_chch_sf_template').val(template);
		$('#publish-slides').trigger('click');
	});

	$('.cc-pu-customize-close').on('click', function(e){
		e.preventDefault();
		var template = $(this).attr('data-template');

		$('#cc-pu-customize-form-'+template).hide();
	});

	$('.cc-pu-template-edit').on('click', function(e){
		e.preventDefault();
		var thisEl = $(this);
		template = thisEl.attr('data-template');
		id = thisEl.attr('data-postid');
		nounce = thisEl.attr('data-nounce');

		$.ajax({
			url: chch_sf_ajax_object.ajaxUrl,
			async: true,
			type: "POST",
			data: {
			action: "chch_sf_load_preview_module",
			template: template,
			nounce: nounce,
			id:id
 		},
		success: function(data) {
				
			if(!$('#ch-ch-slider-defaults').length) {
				$('head').append('<link rel="stylesheet" id="ch-ch-slider-defaults"  href="'+chch_sf_ajax_object.chch_slider_url+'public/templates/css/defaults.css" type="text/css" media="all" />');
			}

				
			if(!$('#'+template+'-base').length) {
				$('head').append('<link rel="stylesheet" id="'+template+'-base"  href="'+chch_sf_ajax_object.chch_slider_url+'public/templates/'+template+'/css/base.css" type="text/css" media="all" />');
			}

			if(!$('#'+template+'-style').length) {
				$('head').append('<link rel="stylesheet" id="'+template+'-style"  href="'+chch_sf_ajax_object.chch_slider_url+'public/templates/'+template+'/css/style.css" type="text/css" media="all" />'); 
			} 

			theme = thisEl.closest('.theme');
			previewWrapper = $('#cc-pu-customize-form-'+template);
			
			$('#cc-pu-customize-preview-'+template).html(data);

			$('.theme').removeClass('active');
			theme.addClass('active');

			$('#_chch_sf_template').val(template);

			previewWrapper.find('.cc-pu-option-active .cc-pu-customize-style').trigger('change');
			previewWrapper.find('.class-switcher:checked').trigger('change');
			previewWrapper.show();
		}
		});
	});
  
	/////SLIDES REPEATER
	function count_slides(){
		$('.chch-slides-repeater .chch-slide-counter').each(function(index){
			$(this).html(	index+1);
		});
	}
	
	$( "#chch-add-slide" ).on('click', function(e){
		e.preventDefault();

		slidesIndex = $(this).attr('data-slides-number');
		currentIndex = (parseInt(slidesIndex)+1);
		$(this).attr('data-slides-number',currentIndex);
		wrapper = $('.chch-slides-repeater');

		fields = wrapper.find('.chch-single-slide:first-child').clone(true);
		fields.find('.wp-editor-wrap').remove(); 
		fields.find('img').remove();
		 
		fields_inputs = fields.find('input');
					
		fields_inputs.each(function(){
			$(this).val('');
			field_name = $(this).attr('name');
			field_name = field_name.replace('_chch_slides[0]', '_chch_slides['+currentIndex+']');
			$(this).attr('name',field_name);
		});
		
		fields.appendTo(wrapper);
		
		fields.find('.chch-repeater-desc-field').append('<div id="wp-chch-slide-description-wrap-'+currentIndex+'" class="wp-core-ui wp-editor-wrap tmce-active"><textarea id="chch-wysiwyg-'+currentIndex+'" name="_chch_slides['+currentIndex+'][caption]"></textarea></div>');
		
		tinymce.init({
						selector: 'textarea#chch-wysiwyg-'+currentIndex, 
						preview_styles:"font-family font-size font-weight font-style text-decoration text-transform",  
						editor_class:'chch-repeater-wysiwyg',
						menubar:false,
						wpautop:true,
						indent:false,
						toolbar1:" bold,italic,underline,link,unlink,forecolor,undo,redo",
						toolbar2:"",
						toolbar3:"",
						toolbar4:"", 
		});  
		
		count_slides();
	});

	$( ".chch-delete-slide" ).on('click', function(e){
		e.preventDefault();
		$(this).closest('.chch-single-slide').not('.chch-single-slide:first-child').remove();
		count_slides();
	});



	/////LIVE PREVIEW SCRIPTS
		  $( ".accordion-section-title" ).on('click', function(e){

		  	var el = $(this);
			var target = el.next('.accordion-section-content');
	  	 	if(!$(this).hasClass('open')){
				$( ".accordion-section-title").removeClass('open');
				el.addClass('open');
				target.slideDown('fast');
			}
			else
			{
				el.removeClass('open');
				target.slideUp('fast');
			}
		}
	  );

	 $( '.cc-pu-colorpicker' ).wpColorPicker({
	 	 change: _.throttle(function() {
			var el = $(this);
			var template = el.attr('data-template');
			var target = el.attr('data-customize-target');
			var styleAttr = el.attr('data-attr');
			var elValue = el.val(); 
			console.log(elValue);
			$('#cc-pu-customize-preview-'+template+' '+target).css(styleAttr,elValue);
		})
	 });

	$('.cc-pu-customize-style').on('change', function(e){
		var el = $(this);

		var elId = el.attr('id');
		var elType = el.attr('type');
		var template = el.attr('data-template');
		var target = el.attr('data-customize-target');
		var styleAttr = el.attr('data-attr');
		var elValue = el.val();
		var elUnit = el.attr('data-unit');

		if(typeof elUnit === "undefined"){
			elUnit = '';
		}

		if(styleAttr == 'background-image'){
			$('#cc-pu-customize-preview-'+template+' '+target).css('background-image','url('+elValue+')');

			var n = elId.search("_image");
			if(n > 0) {
				$('#cc-pu-customize-preview-'+template+' '+target).css('background-size','cover');
			}
		}
		else
		{
			$('#cc-pu-customize-preview-'+template+' '+target).css(styleAttr,elValue+elUnit);
		}

	});
 
 
	
	$('.remover-checkbox').on('change', function(){ 
		var target = $(this).attr('data-customize-target');
		
		if($(this).is(':checked')){
			$(target).hide();
		} else {
			$(target).show();	
		}
	});
	  
		$(".class-switcher").on('change',function() {  
			el = $(this);
			template = el.attr('data-template');
			eltarget = el.attr('data-customize-target'); 
			elName = el.attr('name'); 
			
			elOldVal = el.attr('data-old'); 
			elval = el.val();
			
			$('#cc-pu-customize-preview-'+template+' '+eltarget).removeClass(elOldVal);
			$('#cc-pu-customize-preview-'+template+' '+eltarget).addClass(elval);
			
			$('#cc-pu-customize-form-'+template+' input[name='+elName+']').attr('data-old',elval); 
    });
	
	///// WP MEDIA UPLOAD JS
	var custom_uploader;


    $('.chch-slide-image-upload').click(function(e) {

		e.preventDefault();
		target = $(this).closest('.chch-slide-image').find('.chch-slide-image-url');
		preview = $(this).closest('.chch-slide-image').find('.chch-image-preview');
		//If the uploader object has already been created, reopen the dialog
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}

		//Extend the wp.media object
		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});

		//When a file is selected, grab the URL and set it as the text field's value
		custom_uploader.on('select', function() {
			attachment = custom_uploader.state().get('selection').first().toJSON();
			target.val(attachment.url);
			preview.html('<img src="'+attachment.url+'" />');
		});

		//Open the uploader dialog
		custom_uploader.open();

		});

});
