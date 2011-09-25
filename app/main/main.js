var ø = function(){
	
	this.$body   = $('#body');
	// main vars
	var tmp = this.$body.get(0).className.split(' ');
	this.language = tmp[0];
	this.current  = tmp[1];
	if (typeof this[this.current] == 'function') {
		this[this.current].prototype.parent = this;
		new this[this.current]();
	}
};
ø.fn = {
	overlay:function(message){
		var self = this;

		var $overlay = $('#overlay');
		var $div = $overlay.find('div').first();
		$overlay.css({
			'position'   : 'fixed',
			opacity : 0
		}).show();
		$div.html(message);
		setTimeout(function(){
			var height = $div.height();
			var width  = $div.width();
			$div.css({
				'margin-top' : ((height/2)+40)*-1,
				'margin-left': ((width/2))*-1
			});
			self.timeout = null;
			self.hide = function(){
				if (self.timeout){
					clearTimeout(self.timeout);
					self.timeout = null;
				}
				$(window)
					.unbind('keydown', self.hide)
					.unbind('click', self.hide);
				$overlay.animate({opacity:0},'fast', function(){
					if (typeof ø.fn.overlay.callback == 'function') 
						ø.fn.overlay.callback.call(this);
					$overlay.hide();
				})	
			};
			$overlay.animate({opacity:.95},'fast', function(){
				$(window)
					.bind('click', self.hide)
					.bind('keydown', self.hide)
				//self.timeout = setTimeout(self.hide, 9999);
			})			
		}, 25);
	}
};

ø.prototype.authentic = function(){
	var self = this;
	var $form = $('form');
	var rx = /^[a-zA-Z0-9]{16}$/;
	$form.find('input[type=submit]').click(function(){
		var token = $('input[type=hidden]').val();
		var data = {'token':token}
		$form.find('input[type=text]').each(function(){
			$this = $(this);
			if (!$.trim($this.val()).match(rx)) return data = false;
			data[$this.attr('name')] = this.value;
		});
		if (!data || data.external == data.internal) {
			ø.fn.overlay(self.parent.language == 'es' ? 'Código Inválido' : 'Invalid Code');
			return false;
		}
		$.post('', data, function(e){
			var response = $.parseJSON(e);
			if (self.parent.language == 'es'){
				ø.fn.overlay(
					'<p>El código corresponde a:<p>'                                +
					'<h3>' + response.name + '</h3>'                                +
					'<div><img src="' + response.urli +'" width="250"></div>'       +
					'<h4>¡Gracias por su preferencia!</h4>'                         +
					'<p>Este producto ha sido validado y <br/>futuras verificaciones serán ignoradas.</p>'
				);				
			} else {
				ø.fn.overlay(
					'<p>The code corresponds to :<p>'                               +
					'<h3>' + response.name + '</h3>'                                +
					'<div><img src="' + response.urli +'" width="250"></div>'       +
					'<h4>Thanks for your preference!</h4>'                          +
					'<p>This product has been validated and <br/>future verifications will be ignored.</p>'
				);
			}

			ø.fn.overlay.callback = function(){
				window.location.href = '<?=URL?>' + self.parent.language;
			};
		}).error(function(e){
			ø.fn.overlay.callback = null;
			if (self.parent.language == 'es'){
				ø.fn.overlay('<p>El código no corresponde a ningún producto original.<p>');
			} else {
				ø.fn.overlay('<p>The code does not correspond to an original product.<p>');
			}
		});
		return false;
	});
	return false;
};

ø.prototype['contact-us'] = function(){
	var self = this;
	$aside = $('aside');
	$aside.height($aside.parent().height());
	$('input[type=submit]').click(function(){
		var rx = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if (!$('form').find('input[name=email]').val().match(rx)){
			ø.fn.overlay(self.parent.language == 'es'? 'Correo Inválido.' : 'Invalid Email.');
			return false;
		}
		var data = {}
		$('form').find('input,textarea,select').not('[type=submit]').each(function(){
			$this = $(this);
		 	data[$this.attr('name')] = $this.val();
		});
		$.post('', data, function(response){
			var msg = self.parent.language == 'es'?
			'¡Envío exitoso!<br/><small>Gracias por su preferencia.</small>' :
			'Success!<br/><small>Thanks for your preference.</small>';
			ø.fn.overlay.callback = function(){
				window.location.href = '<?=URL?>' + self.parent.language;
			};
			ø.fn.overlay(msg);
		}).error(function(e){
			ø.fn.overlay.callback = null;
			ø.fn.overlay(e.responseText);
		})
		return false;
	});
};

// Main Page.
ø.prototype.index = function(){
	this.$banner = $('#banner');
	this.$banner.$img = this.$banner.find('img');
	// enable billboard
	var bbH = this.$banner.$img.height() || 123; // webkit needs more time to get this.
	var bbW = this.$banner.$img.width();
	$('#banner').uBillboard({
		height            : bbH,
		width             : bbW,
		square_resolution : Math.ceil(bbH/2),
		delay             : 3333,
		loader_image      : this.$banner.$img.first().attr('src')
	})
}


$(document).ready(function(){ new ø(); });