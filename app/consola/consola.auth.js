
$.ui.core.defaults.debug = true;

$(document).ready(function(){

	// users with no javascript wont use the site.
	$('html').removeClass('no-js');

	var $auth = $('#auth');

	$auth.ui({
		auto   : true,  // auto open
		close  : false, // disable closing

		submit : function(){
			var pass = true;
			this.$content.find('input:not([type="hidden"])').each(function(){
				if (!this.value.length) pass = false;
			});
			if (!pass) return $auth.ui('sayno');
			// all is clear, find the form and submit.
			this.$content.find('form').submit();
		}
	});
});
