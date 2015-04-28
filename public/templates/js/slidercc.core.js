/*!
 * CC Slider by Chop-chop
 * http://shop.chop-chop.org
 */

(function($){
	'use strict';

	var defaults = {
		// basic
		mode: 'fade',
		autoPlay: 3000,
		pauseOnHover: true,
		animationSpeed: 1000,
		pagination: true,
		arrows: true,
		loop: false,
		keyboard: true,
		mousewheel: true,
		touch: true,
		lockDuringAnimation: false,
		synchronize: null,
		remoteControl: null,
		// advanced
		startAt: 0, // zero-based
		changePaginations: 'before', // before or after
		animationMethod: 'css', // css, velocity or js
		slidesListSelector: 'ul.slides',
		slidesSelector: 'ul.slides > li',
		// technical
		namespace: 'scc',
		syncNamespace: 'scc',
		remoteNamespace: 'scc',
		arrowTexts: {
			prev: 'Previous slide',
			next: 'Next slide'
		},
		limitWidthToParent: true
	};

	if(!console){
		var consoleFix = function() {};
		window.console = {log:consoleFix, info:consoleFix, warn:consoleFix, debug:consoleFix, error:consoleFix};
	}

	if(!$) {
		console.error('[CC Slider] No jQuery detected.');
		return false;
	}

	var Slider = function(elem, opts){
		this.$wrap = $(elem);
		if(!this.$wrap.data('slidercc')){
			if($.isEmptyObject(this.modes)) {
				console.error('[CC Slider] No modules found.');
				return false;
			}

			this.mode = this.modes[opts.mode || defaults.mode];
			if(!this.mode) {
				for(var firstMod in this.modes) {
					break;
				}
				this.mode = this.modes[firstMod];
			}
			this.mode = new this.mode();
			this.mode.slider = this;

			this.opts = $.extend({}, $.fn.slidercc.defaults, this.mode.defaults, opts);
			this.$wrap.data('slidercc', this.opts);
			this.namespace = this.opts.namespace;
			this.$wrap.addClass(this.namespace+'-wrapper');

			this.init();
		}
		else {
			this.$wrap.trigger(this.$wrap.data('slidercc').namespace+'-reset', opts);
		}
	};

	Slider.prototype.modes = {};

	Slider.prototype.init = function(){
		var _this = this;
		this.loadSlides();
		if(this.testSlideLength()){
			this.makeViewport();
			this.$wrap.css('opacity', 0);
			if(this.opts.arrows) this.makeArrows();
			if(this.opts.pagination) this.makePagination();
			this.current = Math.min(this.opts.startAt, this.slideCount-1);
			this.next = this.current;
			this.wrapWidth = this.$wrap.width();
			this.setActiveClasses();
			this.bind();
			this.checkAnimationMethod();
			this.setAnimations();
			if(this.opts.synchronize!=null){
				this.$sync = $(this.opts.synchronize);
				if(!this.$sync.length) this.opts.synchronize = null;
				else {
					this.synchronize();
				}
			}
			if(this.opts.remoteControl!=null){
				this.$remote = $(this.opts.remoteControl);
				if(!this.$remote.length) this.opts.remoteControl = null;
				else {
					this.remoteControl();
				}
			}
			this.mode.init();
			this.$slides.eq(this.current).imagesLoaded(function(){
				_this.animate.fadeIn(_this.$wrap);
				_this.ready = true;
				// console.log('Slider.init imagesLoaded',_this.$wrap.attr('id'));
				_this.$wrap.trigger(_this.namespace+'-ready');
			});

		}
	};

	Slider.prototype.loadSlides = function(){
		this.$slideList = $(this.$wrap).find(this.opts.slidesListSelector);
		this.$slides = $(this.$wrap).find(this.opts.slidesSelector);
		this.slideCount = this.$slides.length;
		this.opts.$slides = this.$slides;
	};

	Slider.prototype.testSlideLength = function(){
		if(!this.slideCount){
			console.warn('[CC Slider] No slides found.');
			return false;
		}
		return true;
	};

	Slider.prototype.bind = function(){
		var _this = this;
		$(window).on('resize', $.proxy(this.resize, this));
		this.$wrap.on(this.namespace+'-reset', $.proxy(this.reset, this));
		this.$wrap.on(this.namespace+'-'+this.opts.changePaginations+' '+this.namespace+'-ready', $.proxy(this.setActiveClasses, this));
		if(this.opts.arrows){
			this.arrows.$prev.on('click.'+this.namespace, function(e){
				e.preventDefault();
				_this.$wrap.trigger(_this.namespace+'-prev');
			});
			this.arrows.$next.on('click.'+this.namespace, function(e){
				e.preventDefault();
				_this.$wrap.trigger(_this.namespace+'-next');
			});
		}
		if(this.opts.pagination){
			this.$pagination.find('a').on('click.'+this.namespace, function(e){
				e.preventDefault();
				_this.$wrap.trigger(_this.namespace+'-slideTo', $(this).attr('href').replace('#', ''));
			});
		}
		if(this.opts.keyboard && !$('body').data('slidercc-keyboard')) this.bindKeyboard();
		if(this.opts.mousewheel && $.event.special.mousewheel) this.bindMousewheel();
		if(this.opts.touch && $.event.special.swipeleft && $.event.special.swiperight) this.bindTouch();

		if(this.opts.autoPlay){
			_this.startAutoplay();
			if(this.opts.pauseOnHover){
				this.$wrap.on('mouseenter.'+this.namespace, function(e){
					// console.log('mouseenter', _this.$wrap.attr('id'));
					e.preventDefault();
					_this.stopAutoplay();
				});
				this.$wrap.on('mouseleave.'+this.namespace, function(e){
					e.preventDefault();
					_this.startAutoplay();
				});

			}
		}

	};

	Slider.prototype.bindKeyboard = function(){
		var _this = this;
		$('body').data('slidercc-keyboard', this.$wrap);
		$(window).on('keyup', function(e){
			var key = e.keyCode;
			if(key==37) _this.$wrap.trigger(_this.namespace+'-prev');
			else if(key==39) _this.$wrap.trigger(_this.namespace+'-next');
		});
	};

	Slider.prototype.bindMousewheel = function(){
		var _this = this;
		this.$wrap.on('mousewheel', function(e){
			e.preventDefault();
			if(e.deltaY<0 || e.deltaY==0 && e.deltaX>0){
				_this.$wrap.trigger(_this.namespace+'-next');
			}
			else {
				_this.$wrap.trigger(_this.namespace+'-prev');
			}
		});
	};

	Slider.prototype.bindTouch = function(){
		var _this = this;
		this.$wrap.on('swiperight', function(e){
			e.preventDefault();
			_this.$wrap.trigger(_this.namespace+'-prev');
			_this.opts.autoPlay = false;
			_this.stopAutoplay();
		});
		this.$wrap.on('swipeleft', function(e){
			console.log(e);
			e.preventDefault();
			_this.$wrap.trigger(_this.namespace+'-next');
			_this.opts.autoPlay = false;
			_this.stopAutoplay();
		});
	};

	Slider.prototype.synchronize = function(){
		this.$wrap.on(this.namespace+'-slideTo', $.proxy(function(e, d, force, squash){
			if(squash) return;
			var target = e instanceof jQuery.Event ? d : e;
			this.$sync.trigger(this.opts.syncNamespace+'-slideTo', [target, force, true]);
		}, this));
	};

	Slider.prototype.remoteControl = function(){
		var _this = this;
		this.$slides.on('click.'+this.namespace, $.proxy(function(e){
			e.preventDefault();
			var target = this.$slides.index(e.currentTarget);
			this.$remote.add(this.$wrap).trigger(this.opts.remoteNamespace+'-slideTo', [target, null, true]);
		}, this));
		this.$wrap.on('mouseenter.'+this.namespace, function(e){
			_this.$remote.trigger('mouseenter');
		});
		this.$wrap.on('mouseleave.'+this.namespace, function(e){
			_this.$remote.trigger('mouseleave');
		});
	};

	Slider.prototype.unbind = function(){
		this.$wrap.off(this.namespace+'-reset');
	};

	Slider.prototype.destroy = function(){
		this.unbind();
		this.$arrows.remove();
		this.$pagination.remove();
		this.$viewport.after(this.$viewport.children()).remove();
		this.removeData('slidercc');
	};

	Slider.prototype.reset = function(){
		$.extend(this.opts, arguments[arguments.length-1]);
		this.mode.reset();
	};

	Slider.prototype.checkAnimationMethod = function(){
		// var _this = this;
		if(this.opts.animationMethod=='css' && window.Modernizr && (Modernizr.csstransforms==false || Modernizr.csstransitions==false)) {
			// if($.fn.velocity) {
			// 	this.opts.animationMethod = 'velocity';
			// }
			// else {
				this.opts.animationMethod = 'js';
			// }
		}
	};

	Slider.prototype.setAnimations = function(){
		var _this = this;
		if(this.opts.animationMethod=='css'){
			this.animate = {
				fadeOut: function(elem){
					elem.css('opacity', 0);
				},
				fadeIn: function(elem){
					elem.css('opacity', 1);
				},
				slide: function(elem, val, force){
					if(force) {
						elem.addClass(_this.namespace+'-no-trans');
						setTimeout(function(){
							elem.removeClass(_this.namespace+'-no-trans');
						}, 10);
					}
					elem.css('transform', 'translate3d('+val+'px,0,0)');
				}
			};
			this.$wrap.css('transition', 'opacity '+this.opts.animationSpeed+'ms');
			if(this.opts.animateHeight){
				this.$viewport.css('transition', 'height '+this.opts.animationSpeed+'ms');
			}
		}
		else if(this.opts.animationMethod=='velocity') {
			this.animate = {
				fadeOut: function(elem){
					elem.velocity('stop').velocity('fadeOut', {duration: _this.opts.animationSpeed, display: 'block'});
				},
				fadeIn: function(elem){
					elem.velocity('stop').velocity('fadeIn', {duration: _this.opts.animationSpeed});
				},
				slide: function(elem, val, force){
					elem.velocity('stop').velocity({translateX: val}, {duration: force ? 0 : _this.opts.animationSpeed});
				}
			};
		}
		else {
			this.animate = {
				fadeOut: function(elem){
					elem.stop(1,0).fadeTo(_this.opts.animationSpeed, 0);
				},
				fadeIn: function(elem){
					elem.stop(1,0).fadeTo(_this.opts.animationSpeed, 1);
				},
				slide: function(elem, val, force){
					elem.stop(1,0).animate({marginLeft: val}, force ? 0 : _this.opts.animationSpeed);
				}
			};
		}
	};

	Slider.prototype.makeViewport = function(){
		this.$viewport = $('<div class="'+this.namespace+'-viewport"></div>');
		this.$wrap.append(this.$viewport);
		this.$viewport.append(this.$wrap.children());
	};

	Slider.prototype.makeArrows = function(){
		this.$arrows = $('<div class="'+this.namespace+'-arrows"></div>');
		this.arrows = {
			$prev: $('<a href="#" class="'+this.namespace+'-prev">'+this.opts.arrowTexts.prev+'</a>'),
			$next: $('<a href="#" class="'+this.namespace+'-next">'+this.opts.arrowTexts.next+'</a>')
		};
		this.$arrows.append(this.arrows.$prev, this.arrows.$next);
		this.$wrap.append(this.$arrows);
	};

	Slider.prototype.makePagination = function(){
		var html = '<div class="'+this.namespace+'-pagination">';
		for(var i=0; i<this.slideCount; i++){
			html += '<a href="#'+i+'">'+(i+1)+'</a>';
		}
		html += '</div>';
		this.$pagination = $(html);
		this.$wrap.append(this.$pagination);
	};

	Slider.prototype.setActiveClasses = function(){
		var current = this.opts.changePaginations=='after' ? this.current : this.next;
		this.$slides.removeClass(this.namespace+'-active');
		this.$slides.eq(current).addClass(this.namespace+'-active');
		if(this.opts.arrows && !this.opts.loop){
			if(current==0){
				this.arrows.$prev.addClass(this.namespace+'-disabled');
			}
			else {
				this.arrows.$prev.removeClass(this.namespace+'-disabled');
			}
			if(current==this.slideCount-1){
				this.arrows.$next.addClass(this.namespace+'-disabled');
			}
			else {
				this.arrows.$next.removeClass(this.namespace+'-disabled');
			}
		}
		if(this.opts.pagination){
			this.$pagination.find('a[href=#'+current+']').addClass(this.namespace+'-active').siblings().removeClass(this.namespace+'-active');
		}
	};

	Slider.prototype.startAutoplay = function(){
		var _this = this;
		if(this.autoTimer) clearInterval(this.autoTimer);
		this.autoTimer = setInterval(function(){
			_this.$wrap.trigger(_this.namespace+'-next', {auto: true});
		}, this.opts.autoPlay);
	};

	Slider.prototype.stopAutoplay = function(){
		if(this.autoTimer) clearInterval(this.autoTimer);
	};

	Slider.prototype.resize = function(){
		if(this.opts.limitWidthToParent){
			this.$wrap.width('');
			if(this.$wrap.width() > this.$wrap.parent().width()){
				this.$wrap.width( this.$wrap.parent().width() );
			}
		}
		this.wrapWidth = this.$wrap.width();
		this.mode.resize();
	};

	$.fn.slidercc = function(args){
		return this.each(function(){
			new Slider(this, args);
		});
	};

	$.fn.slidercc.defaults = defaults;
	$.fn.slidercc.modeDefaults = {};

	$.fn.slidercc.insertMode = function(name, mode){
		var newMode = {};
		newMode[name] = mode;
		// $.extend(Slider.prototype.modes, newMode);
		Slider.prototype.modes[name] = mode;
		$.fn.slidercc.modeDefaults[name] = mode.prototype.defaults;
	};
})(jQuery);


