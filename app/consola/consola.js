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

	$.ui.loader.show();

	ø.$cont = $('#cont');
	ø.$body = $('body');
	ø.$sect = ø.$cont.find('> section');

	var page = ø.$cont.attr('class');
	// disable current's menu item.
	var curr = '.ui-menubar .'+page.replace('_',' .');
	curr = $(curr).addClass('ui-menubar-current');

	$(window).resize(ø.resize);
	$('html').removeClass('no-js');

	// enable global modal
	ø.modal = $.ui.enable('modal', $('<div>').appendTo(ø.$body));


	// enable changepassword modal.
	$('.ui-menubar .admin .password').click(ø.admin.password);

	// enable inset style for title.
	$('h1.ui-inset').ui({low:0.6});
	$('fieldset.ui-inset').ui({low:0.85});

	// if divive elements found, adjust them.
	if (ø.$cont.find('.divide').length>0) {
		ø.divide();
		ø.resize.fn.divide = ø.divide;
	}

	// force a listener for resize events on textarea.
	$('textarea').each(function(){
		var $t = $(this);
		$t.data('oW', $t.outerWidth());
		$t.data('oH', $t.outerHeight());
		$t.mouseup(function(){
			 var $this = $(this);
			 if (
			 	$this.outerWidth()  != $this.data('oW') ||
			 	$this.outerHeight() != $this.data('oH')
			 )  $this.trigger('resize');
	 		$this.data('oW', $this.outerWidth());
			$this.data('oH', $this.outerHeight());
		});
	}).resize(function(){});


	if (page == 'agregar_producto')  return ø.agregar.producto();
	if (page == 'agregar_categoria') return ø.agregar.categoria();
	if (page == 'editar_categoria')  return ø.editar.categoria();
	if (page == 'editar_producto')    return ø.editar.producto();
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
				var gonnadelete = type=='producto'?
					'<p>Toda la mercancía relacionada, también será eliminada.</p>' :
					'<p>Todos los productos relacionados, también serán eliminados.</p>';
				if (action == 'delete'){
					ø.modal.settings.close = false;
					ø.modal.settings.cancel = function(){ ø.modal.hide(); }
					ø.modal.settings.submit = function(){ 
						window.location.href = '../../consola/borrar/' + type + '/' + target;
					}
					ø.modal.title   = "Se necesita confirmación";
					ø.modal.content = 
						"<p>¿Seguro que desea borrar <b>" + target + "</b>?</p>" +
						gonnadelete +
						"<p>Ésta acción es irreversible.</p>";
					ø.modal.show();
					return false;
				}
				// update
				window.location.href = APP_URL + 'editar/' + type + '/' + target;
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


/**
 * Do stuff when window resized.
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/05 12:29
 */
ø.resize = function(e){
	if (ø.resize.to) {
		clearTimeout(ø.resize.to);
		//ø.resize.to = null;
	}
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
	console.info('run');
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
	var div = ø.$cont.find('.divide').css({
		'margin-left'  : 0,
		'margin-right' : 0
	});
	var sec = ø.$cont.find('> section').outerWidth();
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
 * @created 2011/SEP/15 22:06
 */
ø.classcheck = function(){
	var $id = $('#class');
	var to;
	var val;
	$id.$parent = $id.parent();
	var classcheck = function(e){
		if (to) clearTimeout(to);
		// wait a second before checking value.
		to = setTimeout(function(){
			val = $id.val();
			$.post('',{action:'prodclass', value:val, token: TOKEN_PUBLIC },
				function(data){
					if (data == 'found') {
						$id.$parent.addClass('error');
						$id.ui('sayno',{distance:5});
					}
					else $id.$parent.removeClass('error');
				}).error(ø.error);
		},333);
	};
	$id.keypress(classcheck);
	$id.keydown(function(e){
		if (e.keyCode != 8) return;
		classcheck.call(this, e);
	})
}


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

/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/08 15:23
 */
ø.agregar.producto = function(){
	// insert progressbar into modal.
	ø.upbar  = $.ui.enable('progressbar', $('<div>').appendTo(ø.modal.$content).height('30px'));
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
			try {
				this.name = $.parseJSON(this.xhr.responseText).image;
			} catch (e){
				$('section').html(this.xhr.responseText);
			}
			this.element.parentsUntil('section').last().removeClass('error');
			ø.candivide = false; // don't call divide while doing this.
			// remove all existing images
			$ph.removeClass('hasimg').find('img').remove();
			var self = this;
			// show new image and adjust its size.
			$.ui.loader.show();
			var img = new Image();
			img.src = PUB_URL + 'consola/tmp/' + this.name;
			img.onload = function(){
				$(img).appendTo($ph);
				$ph.addClass('hasimg');
				$('html, body').animate({ scrollTop : 0 });
				ø.divide();
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
	$ph.click( function(){ ø.upload.$file.click(); } );
	var removerr = function(){
		$(this).parent().removeClass('error');
	};
	$('select').change(removerr);
	$('input,textarea').keypress(removerr);

	// enable class checker
	ø.classcheck();
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
				// ignore file while in edit mode.
				if (!(isfile && $this.$parent.find('.isedit').length)){
					$this.$parent.addClass('error');
					$('html,body').animate({opacity:1}, 500, function(){
						$(this).animate({ scrollTop: $this.$parent.scrollTop()});
					});
					return pass = false; // breaks
				}
			}
			$this.$parent.removeClass('error');
			if (isfile) data['file'] = ø.upload.name;
			else data[$this.attr('id')] = val;
		});
		if (!pass) return $button.ui('sayno');
		// while in edit mode, undefined means unchanged. ;)
		if (data.file === undefined && ø.upload.element.hasClass('isedit'))
			data.file = '__same__'+ORIGINALUPFILE;
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
	// validate form.
	var $button = $('.submit button');
	var removerr = function(){
		$(this).parent().removeClass('error');
	};
	$('input,textarea').keypress(removerr);

	// validate category identifier
	ø.classcheck();

	$button.click(function(){
		var pass = true;
		var data = { token: TOKEN_PUBLIC };
		var val, $this;
		$('input,textarea').each(function(){
			$this = $(this);
			$this.$parent = $this.parent();
			val = $this.val();
			if (!val.length || $this.parent().hasClass('error')) {
				$this.$parent.addClass('error');
				return pass = false; // breaks
			}
			$this.$parent.removeClass('error');
			data[$this.attr('id')] = val;
		});
		if (!pass) return $button.ui('sayno');
		$.ui.loader.show();
		$.post('', data, ø.success).error(ø.error);
	});
}

ø.editar = {};

ø.editar.categoria = ø.agregar.categoria;
ø.editar.producto = ø.agregar.producto;

ø.admin = {};

/**
 * @created 2011/SEP/18 20:55
 */
ø.admin.password = function(){
	var to;
	var keydown = function(e){
		// only do this when new and confirmation have values.
		var $old = ø.modal.$content.find('#pass_orig');
		var $new = ø.modal.$content.find('#pass_new');
		var $cnf = ø.modal.$content.find('#pass_conf');
		if (to) {
			clearTimeout(to);
			to = undefined;
		}
		to = setTimeout(function(){
			$new.val = $new.val();
			$cnf.val = $cnf.val();
			$old.val = $old.val();
			// new pwd is the same as old.
			if ($new.val === $old.val){
				$new.parent().addClass('error');
				if ($cnf.length) $cnf.parent().addClass('error');
				return;
			} else $new.add($cnf).parent().removeClass('error');
			// confirmation differs from new.
			if (!$new.val.length || !$cnf.val.length) return;
			if ($new.val === $cnf.val) $cnf.parent().removeClass('error');
			else $cnf.parent().addClass('error');			
		},50);
	};
	ø.modal.title = "Cambiar contraseña"
	ø.modal.content = $('#admin-password').html();
	ø.modal.settings.close = false;
	ø.modal.settings.cancel = function(){
		ø.modal.$content.unbind('keydown', keydown);
		ø.modal.hide();
	};
	ø.modal.settings.submit = function(){
		var pass = true;
		var data = { token: TOKEN_PUBLIC };
		ø.modal.$content.find('input').each(function(){
			$this = $(this);
			if (!$this.val().length || $this.parent().hasClass('error'))
				return pass = false;
			data[$this.attr('id')] = $this.val();
			
		});
		if (!pass) {
			ø.modal.element.ui('sayno');
			return false;
		}
		$.post(APP_URL+'admin/password', data, function(data){
			ø.modal.hide();
			ø.modal.title   = 'Se cerrará la sesión';
			ø.modal.content = 'Contraseña cambiada.';
			ø.modal.settings.cancel = null;
			ø.modal.settings.close  = false;
			ø.modal.settings.submit = function(){
				ø.modal.hide();
				$.ui.loader.show();
				window.location.href = APP_URL + 'logout';
				return false;
			};
			ø.modal.show();
		}).error(function(e){
			ø.modal.$content.find('#admin-password-message').html(e.responseText).show();
			ø.modal.element.ui('sayno');
		});
		return false;	
	};
	ø.modal.show();

	ø.modal.$content.bind('keydown',keydown);
	return false;
	
};

////////////////////////////////////////////////////////////////////////////////////////////////////

$.ui.core.defaults.debug = false;
$(document).ready(ø.init);
$(window).load(function(){ 
	$.ui.loader.hide(); 
	// if divive elements found, adjust them.
	if (ø.$cont.find('.divide').length>0)  ø.divide();
	$('html, body').animate({ scrollTop : 0 },'fast');
});

})(jQuery);
