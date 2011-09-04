var o = {

	init:function(){
		$('html').removeClass('no-js');
		// disable current's menu item.
		var curr = '.ui-menubar .'+ $('#cont').attr('class').replace('_',' .');
		curr = $(curr).addClass('ui-menubar-current');
	}
};

$(document).ready(o.init);
