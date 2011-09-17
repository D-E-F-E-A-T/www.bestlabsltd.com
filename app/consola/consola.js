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

	ø.error = function(e){
		var ert = $(e.responseText);
		$.ui.loader.hide();
		ø.modal.settings.close  = true;
		ø.modal.settings.submit = null;
		ø.modal.settings.cancel = null;
		ø.modal.title   = ert.filter('h1').text();
		ø.modal.content = ert.filter('h2').text();
		ø.modal.show();
	};

	ø.success = function(data){
		$.ui.loader.hide();
		ø.modal.settings.close  = false;
		ø.modal.settings.cancel = null;
		ø.modal.settings.submit = function(){
			ø.modal.hide();
			$.ui.loader.show();
			window.location.reload();
		};
		ø.modal.title   = 'La página será recargada.';
		ø.modal.content =  data;
		ø.modal.show();
	};

	if (page == 'agregar_producto')  return ø.agregar.producto();
	if (page == 'agregar_categoria') return ø.agregar.categoria();
	if (page.indexOf('ver_') !== -1){

		// en las acciones "ver"
		// add action rows.
		var $action = $('#action');
		var timeout = null;
		var lastrow = null;
		var type = $.trim(page.replace('ver_',''));
		// add class hover to action so it doesn't hide when hovering.
		$action
			.hover(
				function(){
					$action.add(lastrow).addClass('hover'); 
				},
				function(){
					$action.add(lastrow).removeClass('hover');
					$action.hide();
				}
			)
			.find('li').click(function(){
				var target = $.trim(lastrow.attr('class').replace(/\s*hover\s*/,''));
				var action = $(this).attr('class');
				if (action == 'delete'){
					ø.modal.settings.close = false;
					ø.modal.settings.cancel = function(){ ø.modal.hide(); }
					ø.modal.settings.submit = function(){ return false; }
					ø.modal.title   = "Se necesita confirmación";
					ø.modal.content = 
						"<p>¿Seguro que desea borrar <b>" + target + "</b>?</p>" +
						"<p>Ésta acción es irreversible.</p>";
					ø.modal.show();
					return false;
				}
				// update
				window.location.href = '../../consola/editar/' + type + '/' + target;
			})
		;
		$('table tbody tr').hover(
			// mouseover
			function(e){
				if (timeout) clearTimeout(timeout);
				var $this = $(this);
				lastrow = $this;
				var pos = $this.offset();
				pos.left += $this.outerWidth()-1;

				$action.show().offset(pos);
			},
			// mouseout
			function(){
				timeout = setTimeout(function(){
					if (!$action.hasClass('hover')) $action.hide();
				},50);
			}
		);

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

ø.agregar = {};

ø.agregar.classcheck = function(){
	var $id = $('#class');
	var to;
	$id.$parent = $id.parent();
	$id.keypress(function(e){
		if (to) clearTimeout(to);
		// wait a second before checking value.
		to = setTimeout(function(){
			$.post('',{action:'prodclass', value:$id.val(), token: TOKEN_PUBLIC },
				function(data){
					if (data == 'found') {
						$id.$parent.addClass('error');
						$id.ui('sayno',{distance:5});
					}
					else $id.$parent.removeClass('error');
				}).error(ø.error);
		},333);
	});	
}

/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/08 15:23
 */
ø.agregar.producto = function(){
	// insert progressbar into modal.
	ø.upbar  = $.ui.enable('progressbar', $('<div>').appendTo(ø.modal.$section).height('30px'));
	var $pu = $('#product-upload');
	var $fu = $pu.find('.ui-fileupload').first();
	var $ph = $pu.find('.placeholder').first();
	// enable image uploader
	ø.upload = $.ui.enable('fileupload', $fu,{
		url  : '',
		auto : true,                                    // auto starts upload.
		size : 3*1024*1024,                             // maximum size [3Mb]
		type : ['image/jpg','image/jpeg','image/png'],	// allowed mimetypes
		change:function(){
			ø.modal.settings.footer = false;
			ø.modal.settings.close  = false;
			ø.modal.title = 'Subiendo Fotografía…';
			ø.modal.show();
		},
		progress:function(percentage){
			ø.upbar.value(percentage)
		},
		complete:function(){ ø.modal.hide(); },
		success :function(e){
			// update the name of the image.
			this.name = $.parseJSON(this.xhr.responseText).image;
			this.element.parentsUntil('section').last().removeClass('error');
			ø.candivide = false; // don't call divide while doing this.
			// remove all existing images
			$ph.removeClass('hasimg').find('img').remove();
			var self = this;
			// show new image and adjust its size.
			$.ui.loader.show();
			var img = new Image();
			img.src = '../../pub/consola/upload/' + this.name;
			img.onload = function(){
				$(img).appendTo($ph);
				$ph.addClass('hasimg');
				$('html, body').animate({ scrollTop : 0 });
				$.ui.loader.hide();
			};

			/*
			Loading the image from memory was using so much resources
			I'm gonna use the good ol' load from url. Leaving for future reference.
			
			var fr = new FileReader();
			fr.file = this.$file.get(0).files[0];
			fr.onloadend = function(e){}
			fr.readAsDataURL(fr.file);
			*/
		},
		error:function(e, complete, message){
			// remove all existing images
			$ph.removeClass('hasimg').find('img').remove();
			ø.modal.settings.footer = false;
			ø.modal.settings.close  = true;
			if (!complete) {
				ø.modal.hide();
				ø.modal.title = 'Error';
				switch(message){
					case 'size':
						message = 'El archivo excede el tamaño ḿáximo permitido.';
						break;
					case 'type':
						message = 'El tipo de archivo no está permitido.';
						break;
					default:
						message = 'Error desconocido, contacte a soporte técnico.';	
				}
				ø.modal.content = message;
				ø.modal.show();
				return;
			}
			var html = $(this.xhr.responseText);
			ø.modal.title   = html.filter('h1').text();
			ø.modal.content = html.filter('h2').text();
			ø.modal.show();
		}
	});
	var removerr = function(){
		$(this).parent().removeClass('error');
	};
	$('select').change(removerr);
	$('input,textarea').keypress(removerr);

	// enable class checker
	ø.agregar.classcheck();
	// validate form
	var $button = $('.submit button');
	$button.click(function(){
		var data = { token: TOKEN_PUBLIC };
		var pass = true;
		var $this, val, isfile;
		$('input,textarea,select').each(function(){
			$this = $(this);
			$this.$parent = $this.parent();
			isfile = $this.is('[type=file]');
			if (isfile)
				$this.$parent = $this.parentsUntil('section').last();
			val = $this.val();
			if (!val.length || $this.$parent.hasClass('error')) {
				$this.$parent.addClass('error');
				return pass = false; // breaks
			}
			$this.$parent.removeClass('error');
			if (isfile) data['file'] = ø.upload.name;
			else data[$this.attr('id')] = val;
		});
		if (!pass) return $button.ui('sayno');
		// post data to server.
		$.ui.loader.show();
		$.post('', data, ø.success).error(ø.error);
	});
}


/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/04 16:24
 */
ø.agregar.categoria = function(){
	// validate category identifier
	ø.agregar.classcheck();
	// validate form.
	var $button = $('.submit button');
	$button.click(function(){
		var pass = true;
		var data = { token: TOKEN_PUBLIC };
		var val, $this;
		$('input').each(function(){
			$this = $(this);
			val = $this.val();
			if (!val.length || $this.parent().hasClass('error')) return pass = false; // breaks
			data[$this.attr('id')] = val;
		});
		if (!pass) return $button.ui('sayno');
		// both inputs are filled, check if their values are valid first.
		$.ui.loader.show();
		$.post('', data, ø.success).error(ø.error);
	});
}

$.ui.core.defaults.debug = true;
$(document).ready(ø.init).load(function(){ ø.ui.loader.hide(); });

})(jQuery);
