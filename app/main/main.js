var ø = function(){
	
	this.$body   = $('#body');
	// main vars
	var tmp = this.$body.attr('class').split('_');
	this.language = tmp[0];
	this.current  = tmp[1];
	if (typeof this[this.current] == 'function') {
		this[this.current].prototype.parent = this;
		new this[this.current]();
	}
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