(function($, undefined){

var ø = {};

ø.$cont = null;
ø.$body = null;
ø.$sect = null;
ø.ui    = null;

/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/04 03:35
 */
ø.init = function(){

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

	// if divive elements found, adjust them.
	if (ø.$cont.find('.divide').length>0) {
		ø.divide();
		ø.resize.fn.divide = ø.divide;
	}

	$(window).resize(ø.resize);
	$('html').removeClass('no-js');

	// enable global modal
	ø.modal = $.ui.enable('modal', $('<div>').appendTo(ø.$body));

	// filter behaviour according to page.
	switch(page){
		case 'agregar_producto'  : ø.agregar.producto.init();  break;
		case 'agregar_categoria' : ø.agregar.categoria.init(); break;
		default: console.info('This has not been developed yet');
	}

};

/**
 * Do stuff when window resized.
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
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
 * @licence http://etor.mx/licence.txt
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
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/05 12:19
 */
ø.divide = function(scroll){
	// adjust if scrollbar present.
	// still don't know why this 2pixels appear.
	var scl = ($(document).height() > $(window).height())? 2 : 0;
	var div = ø.$cont.find('.divide').css({
		'margin-left'  : 0,
		'margin-right' : 0
	});
	var sec = ø.$cont.find('> section').outerWidth()-scl;
	var pad = parseInt(div.css('padding-left'),10);
	var dif = (div.outerWidth()-div.width())*2;
	// apply width
	div.width((sec-dif-pad)/2);
	// if width < 350  no need of dividing.
	if (div.width()<300) div.width(sec-(dif/2));
	else div.filter(':nth-child(odd)').css('margin-right',pad+'px');
	$.ui.textinput.padding(div);
};

/**
 * reduce the number of decimals to Two.
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/05 13:11
 */
ø.divide.twodec = function(dec){
	return Math.round(dec*100+((dec*1000)%10>4?1:0))/100;
}
/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/04 03:45
 */
ø.agregar = {

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/04 03:45
	 */
	producto:{

		/**
		 * @author Hector Menendez <h@cun.mx>
		 * @licence http://etor.mx/licence.txt
		 * @created 2011/SEP/08 15:23
		 */
		init:function(){
			// insert progressbar into modal.
			ø.upbar  = $.ui.enable('progressbar', $('<div>').appendTo(ø.modal.$section).height('30px'));
			var $pu = $('#product-upload');
			var $fu = $pu.find('.ui-fileupload').first();
			var $ph = $pu.find('.placeholder').first();
			// enable uploader
			ø.upload = $.ui.enable('fileupload', $fu,{
				url:'../../consola/test',
				auto:true, // auto starts upload.
				size:3*1024*1024, // maximum size [3Mb]
				change:function(){
					ø.modal.settings.footer = false;
					ø.modal.settings.close  = false;
					ø.modal.title = 'Subiendo Fotografía…';
					ø.modal.show();
				},
				progress:function(percentage){
					ø.upbar.update(percentage)
				},
				complete:function(){ ø.modal.hide(); },
				success :function(){
					ø.candivide = false; // don't call divide while doing this.
					// remove all existing images
					$ph.removeClass('hasimg').find('img').remove();
					var self = this;
					// show new image and adjust its size.
					var fr = new FileReader();
					fr.file = this.$file.get(0).files[0];
					fr.onloadend = function(e){
						var img = new Image();
						img.src = e.target.result;
						$img = $(img).appendTo($ph);
						img.onload = function(){
							$ph.addClass('hasimg');
							$('html, body').animate({ scrollTop : 0 });
							// allow browser to scroll.
							setTimeout(ø.divide,200)
							//ø.divide();
						};
					}
					fr.readAsDataURL(fr.file);
				},
				error:function(e, complete, message){
					// remove all existing images
					$ph.removeClass('hasimg').find('img').remove();
					ø.modal.settings.footer = false;
					ø.modal.settings.close  = true;
					if (!complete) {
						ø.modal.hide();
						ø.modal.title = 'Error';
						message = (message == 'size')?
							'El archivo excede el tamaño ḿáximo permitido.' :
							'Error desconocido, contacte a soporte técnico.';
						ø.modal.$section.html(message);
						ø.modal.show();
						return;
					}
					ø.modal.title = 'La Transferencia Falló';
					ø.modal.$section.html(this.xhr.responseText)
					ø.modal.show();
				}
			});
		}
	},

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/04 16:24
	 */
	categoria:{

		/**
		 * @author Hector Menendez <h@cun.mx>
		 * @licence http://etor.mx/licence.txt
		 * @created 2011/SEP/04 16:24
		 */
		init:function(){

		}
	}
};

$.ui.core.defaults.debug = true;

$(document).ready(ø.init).load(function(){ ø.ui.loader.hide(); });


})(jQuery);
