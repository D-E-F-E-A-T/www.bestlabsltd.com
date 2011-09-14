/**
 * SAYNO make an element "say no"
 *
 * v1 2011/MAY/01 Hector Menendez <h@cun.mx>
 *
 * @note not tested in uncontrolled environment.[css]
 * @note only tested in FF4+ Chrome12
**/
(function($){ jQuery.fn.sayno = function(settings){
	settings = $.extend({},{ times: 3, distance:10, speed:100 }, settings);
	return this.each(function(i,elem){
		$this = $(this);
		var ml = parseInt($this.css('margin-left'),10);
		var x = [ml-settings.distance, ml+settings.distance];
		var fn = function(x){ $this.animate({marginLeft:x},settings.speed); };
		for (i=0; i<((settings.times*2)-1); i++) fn(x[i%2]);
		fn(ml);
	});
};})(jQuery);

$.ui.core.defaults.debug = true;

$(document).ready(function(){

	// users with no javascript wont use the site.
	$('html').removeClass('no-js');

	var $auth = $('#auth').ui({auto:true,close:false},function(){
		var self = this;
		self.$submit.click(function(){
			// both user and password must have values before considering sending.
			var all = true;
			self.$section.find('input:not([type="hidden"])').each(function(){
				if (!this.value.length) all = false;
			});
			if (!all) return self.element.sayno();
			self.$section.find('form').submit();
		});
	});
});
