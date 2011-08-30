/**
 * SAYNO make an element "say no"
 * 
 * v1 2011/MAY/01 Héctor Menéndez <h@cun.mx>
 *
 * @note not tested in uncontrolled environment.[css]
 * @note only tested in FF4+ Chrome12
**/
(function($){ jQuery.fn.sayno = function(settings){
	settings = $.extend({},{ times: 3, distance:10, speed:100 }, settings);
	return this.each(function(i,elem){
		$this = $(this);
		var ml = parseInt($this.css('margin-left'));
		var x = [ml-settings.distance, ml+settings.distance];
		var fn = function(x){ $this.animate({marginLeft:x},settings.speed); };
		for (i=0; i<((settings.times*2)-1); i++) fn(x[i%2]);
		fn(ml);
	});

}})(jQuery);

var ø = {
	init:function(){
		$('html').removeClass('no-js');
		ø.$auth = $('#auth');
		ø.$form = ø.$auth.find('form');
		ø.$form.find('input[type=submit]').remove();
		ø.$auth.dialog({
			draggable     : false,
			resizable     : false,
			closeOnEscape : false,
			autoOpen      : true,
			modal         : true,
			buttons       : { 'Accesar' : ø.submit }
		});
	},
	submit:function(){
		 var len = ø.$form.find('div input').filter(function() { return $(this).val() == ""; }).length;
		 ø.$form.submit();
	} 
}

$(document).ready(ø.init);