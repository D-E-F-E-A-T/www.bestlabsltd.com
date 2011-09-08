(function($, undefined){

var ø = {};

ø.$cont = null;
ø.$body = null;
ø.$sect = null;
ø.ui    = null;
/**
 * @author Hector Menendez <h@cun.mx>
 * @created 2011/SEP/04 03:35
 */
ø.init = function(){

	// show the loader until the page is fully load.
	ø.ui = $.ui().loader.show();

	ø.$cont = $('#cont');
	ø.$body = $('body');
	ø.$sect = ø.$cont.find('> section');

	var page = ø.$cont.attr('class');
	// disable current's menu item.
	var curr = '.ui-menubar .'+page.replace('_',' .');
	curr = $(curr).addClass('ui-menubar-current');

	// enable inset style for title.
	$('h1.ui-inset').ui({low:0.6});
	$('fieldset.ui-inset').ui({low:0.85});

	// enable

	// if divive elements found, adjust them.
	if (ø.$cont.find('.divide').length>0) {
		ø.divide();
		ø.resize.fn.divide = ø.divide;
	}

	$(window).resize(ø.resize);
	$('html').removeClass('no-js');

	// filter behaviour according to page.
	switch(page){
		case 'agregar_producto'  : return ø.agregar.producto.init();
		case 'agregar_categoria' : return ø.agregar.categoria.init();
		default: alert('This has not been developed yet');
	}

};

/**
 * Do stuff when window resized.
 * @author Hector Menendez <h@cun.mx>
 * @created 2011/SEP/05 12:29
 */
ø.resize = function(e){
	if (ø.resize.to) clearTimeout(ø.resize.to);
	ø.resize.to = setTimeout(ø.resize.run, 150);
};
ø.resize.to = null;
ø.resize.fn = {};

/**
 * runs all callbacks "pushed" to resize.
 * @author Hector Menendez <h@cun.mx>
 * @created 2011/SEP/05 13:14
 */
ø.resize.run = function(){
	for (var i in ø.resize.fn)
		if (typeof ø.resize.fn[i] == 'function')
			ø.resize.fn[i]();
}

/**
 * Adjust width and padding of .divide elements.
 * @author Hector Menendez <h@cun.mx>
 * @created 2011/SEP/05 12:19
 */
ø.divide = function(){
	var div = ø.$cont.find('.divide').css({
		'margin-left'  : 0,
		'margin-right' : 0
	});
	var sec = ø.$cont.find('> section').outerWidth();
	// calculate padding size.
	var pad = parseInt(ø.$cont.css('padding-left'),10);
	// apply width
	div.width((sec/2)+2-(pad*4));
	// if width < 350  no need of dividing.
	if (div.width()<350) div.width(sec+2-(pad*4));
	else div.filter(':nth-child(even)').css('margin-right',pad+'px');
};

/**
 * reduce the number of decimals to Two.
 * @author Hector Menendez <h@cun.mx>
 * @created 2011/SEP/05 13:11
 */
ø.divide.twodec = function(dec){
	return Math.round(dec*100+((dec*1000)%10>4?1:0))/100;
}
/**
 * @author Hector Menendez <h@cun.mx>
 * @created 2011/SEP/04 03:45
 */
ø.agregar = {

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @created 2011/SEP/04 03:45
	 */
	producto:{

		/**
		 * @author Hector Menendez <h@cun.mx>
		 * @created 2011/SEP/04 03:46
		 */
		init:function(){
			//var $sel = $('#sel').ui();
		}
	},

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @created 2011/SEP/04 16:24
	 */
	categoria:{

		/**
		 * @author Hector Menendez <h@cun.mx>
		 * @created 2011/SEP/04 16:24
		 */
		init:function(){

		}
	}
};

$(document).ready(ø.init).load(function(){ ø.ui.loader.hide(); });


})(jQuery);
