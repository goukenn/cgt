
(function(){
	var o_win=0;
	function __show_tips(e){
			//alert("you click "+this.getMap());
			var m = this.getMap();
			var d = igk.createNode('div');
			d.addClass("offertips");
			//var c = this.inf.host.select('^div').first();
			var t = this.inf.host.select('^.offre').first();
			d.add('div').setHtml(t.getHtml());
			//d.add('div').setHtml( c ? c.getHtml(): "");

			if (o_win){
					o_win.close();
			}
			var wnd = new google.maps.InfoWindow({content:d.getHtml()});
			wnd.open(m, this);
			o_win = wnd;

	}


var c = igk.dom.xslt.initTransform.lastTransform();
var p = 0;
var pq = $igk(igk.getParentScript());
if (c){
	c.initResult(function(q){
p = $igk(q).select('a.nav').getCount();
pq.select('a.nav').reg_event("click", function(e){
	//reload the xsl view with the
	var i = parseInt(this.getAttribute("value"))+1;
	var d = (c.options['max'] - c.options['min']);
	var f = {min: ((i-1) * d), max: d * i, imguri:c.options['imguri']};
	c.reload(f);
	e.preventDefault();
});
var coords=[];

pq.select('input.geo').each_all(function(){
	var cp = igk.JSON.parse(this.o.value);
	cp.host = this;
	coords.push(cp);

});


var gm =igk.google && igk.google.maps;
if (!gm)
	return;
igk.google.maps.setMarker(0, coords, {click:__show_tips});

	});
}
})();
$igk(igk.getCurrentScript()).remove();