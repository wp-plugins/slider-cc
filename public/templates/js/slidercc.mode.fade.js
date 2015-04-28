(function($){
	'use strict';

	if(!console){
		var consoleFix = function() {};
		window.console = {log:consoleFix, info:consoleFix, warn:consoleFix, debug:consoleFix, error:consoleFix};
	}

	if(typeof($)=='undefined') {
		console.error('[CC Slider] No jQuery detected.');
		return false;
	}
	else if(typeof($.fn.slidercc)=='undefined') {
		console.error('[CC Slider] No core found.');
		return false;
	}

	var modeName = 'fade';

	var mode = function(){
		this.name = modeName;
	};

	mode.prototype.defaults = {
		animateHeight: true
	};

	mode.prototype.init = function(){
		this.namespace = this.slider.opts.namespace;
		this.animate = this.slider.animate;
		this.opts = this.slider.opts;
		this.setAnimations();
		this.bind();
		this.slider.$slides.eq(this.slider.current).addClass(this.namespace+'-active');
	};

	mode.prototype.setAnimations = function(){
		// var _this = this;
		if(this.opts.animationMethod=='css'){
			this.slider.$slides.css('transition', 'opacity '+this.opts.animationSpeed+'ms');
		}
		else if($.fn.velocity) {
		}
		else {
		}
	};


	mode.prototype.reset = function(){

	};

	mode.prototype.bind = function(){
		var _this = this;
		this.slider.$wrap.on(this.namespace+'-prev', $.proxy(_this.prev, _this));
		this.slider.$wrap.on(this.namespace+'-next', $.proxy(_this.next, _this));
		this.slider.$wrap.on(this.namespace+'-slideTo', $.proxy(_this.slideTo, _this));
		this.slider.$wrap.on(this.namespace+'-ready', $.proxy(_this.start, _this));
	};

	mode.prototype.start = function(){
		var _this = this;
		var $current = this.slider.$slides.eq(this.slider.current);
		$current.css('zIndex', 2);
		this.maxHeight = $current.outerHeight();
		this.slider.$viewport.height(this.maxHeight);
		this.slider.$wrap.addClass(this.namespace+'-mode-'+this.name);
		this.slider.$slides.not(':eq('+this.slider.current+')')
		.css({
			opacity: 0,
			display: 'block'
		})
		.imagesLoaded(function(){
			_this.setHeights();
		});
	};

	mode.prototype.prev = function(){
		if(this.slider.next > 0){
			this.slider.$wrap.trigger(this.namespace+'-slideTo', this.slider.next - 1);
		}
		else if(this.opts.loop){
			this.slider.$wrap.trigger(this.namespace+'-slideTo',  this.slider.slideCount - 1 );
		}
	};

	mode.prototype.next = function(){
		if(this.slider.next < this.slider.slideCount - 1){
			this.slider.$wrap.trigger(this.namespace+'-slideTo', this.slider.next * 1 + 1);
		}
		else if(this.opts.loop){
			this.slider.$wrap.trigger(this.namespace+'-slideTo',  0 );
		}
	};

	mode.prototype.slideTo = function(e){
		var _this = this;
		var target = e instanceof jQuery.Event ? arguments[1] : arguments[0];
		if(this.slider.animating || target==this.slider.current) return;
		if(this.opts.autoPlay){
			_this.slider.startAutoplay();
		}
		var $next = this.slider.$slides.eq(target).css('zIndex', 2).addClass(this.namespace+'-in');
		var $current = this.slider.$slides.eq(this.slider.current).css('zIndex', '').addClass(this.namespace+'-out');
		if(!$next.length || !$current.length) return;
		if(this.opts.lockDuringAnimation) this.slider.animating = true;
		this.slider.next = target;
		this.slider.$wrap.trigger(this.namespace+'-before');
		this.animate.fadeOut(this.slider.$slides.filter(':visible').not($next));
		this.animate.fadeIn($next);
		if(this.opts.animateHeight) this.slider.$viewport.height($next[0].slideHeight);
		setTimeout(function(){
			_this.slider.current = target;
			$current.removeClass(_this.namespace+'-out');
			$next.removeClass(_this.namespace+'-in');
			_this.slider.$wrap.trigger(_this.namespace+'-after');
			_this.slider.animating = false;
		}, this.opts.animationSpeed);
	};

	mode.prototype.setHeights = function(){
		this.slider.$slides.each(function(){
			$(this).css('minHeight', 0);
			this.slideHeight = $(this).outerHeight();
			$(this).css('minHeight', '');
			if(this.slideHeight >this.maxHeight) {
				this.maxHeight = this.slideHeight;
			}
		});
		if(this.opts.animateHeight) this.slider.$viewport.height( this.slider.$slides[this.slider.current].slideHeight );
		else this.slider.$viewport.height(this.maxHeight);
		if(this.opts.arrows) this.slider.$arrows.children().height( this.slider.$viewport.height() );
	};

	mode.prototype.resize = function(){
		// console.log('ok');
		this.setHeights();
	};

	$.fn.slidercc.insertMode(modeName, mode);

})(jQuery);
