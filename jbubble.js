(function ($) {    
    $.fn.extend({
	pixbubble: function(options) {
	    options = options || {};
	    options.top = options.top || -100;
	    options.left = options.left || -33;
	    options.action = options.action || 'mouseover';


	    return this.each(function() {
		var distance = 10;
		var time = 250;
		var hideDelay = 500;
		var hideDelayTimer = null;
		var beingShown = false;
		var shown = false;
		var trigger = $('.trigger', this);
		var info = $('.popup', this).css('opacity', 0);
		var close = $('.close', this);

		if ('mouseover' == options.action) {
		$([trigger.get(0), info.get(0)]).mouseover(function () {
		    if (hideDelayTimer) clearTimeout(hideDelayTimer);
		    if (beingShown || shown) {
		    } else {
			beingShown = true;

			info.css({
			    top: options.top,
			    left: options.left,
			    display: 'block'
			}).animate({
			    top: '-=' + distance + 'px',
			    opacity: 1
			}, time, 'swing', function() {
			    beingShown = false;
			    shown = true;
			});
		    }
		}).mouseout(function () {
		    if (hideDelayTimer) clearTimeout(hideDelayTimer);
		    hideDelayTimer = setTimeout(function () {
			hideDelayTimer = null;
			info.animate({
			    top: '-=' + distance + 'px',
			    opacity: 0
			}, time, 'swing', function () {
			    shown = false;
			    info.css('display', 'none');
			});
		    }, hideDelay);
		});
		} else {
		$([trigger.get(0)]).click(function () {
		    if (beingShown || shown) {
		    } else {
			beingShown = true;

			info.css({
			    top: options.top,
			    left: options.left,
			    display: 'block'
			}).animate({
			    top: '-=' + distance + 'px',
			    opacity: 1
			}, time, 'swing', function() {
			    beingShown = false;
			    shown = true;
			});
		    }
		});
		if (close.length) {
		    $([close.get(0)]).click(function () {
			info.animate({
			    top: '-=' + distance + 'px',
			    opacity: 0
			}, time, 'swing', function () {
			    shown = false;
			    info.css('display', 'none');
			});
		    });
		}
		}
	    });
	}
    });
})(jQuery);
