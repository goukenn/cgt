"use strict";
	
(function(){
	
	// igk.system.createNS("cgt",{
		// filters:{
			
		// },
		// initFilter:function(){
			// var q = igk.getParentScript();
			
		// }
	// });
	
	
	igk.system.createNS("cgt.filters", {
		init:function(o){
			
			//data must be 
			// {id: {
			//	value:..
			//	type:..
			//  offer:..			
			//}}
			// console.debug('init filters');
			
			//initialise the group setting group			
			var b = $igk(igk.getParentScript());			
			var i = 0;
			var tab = o.data;
			var gx = igk.JSON.parse(o.data);
			var v = 0;//test value
			
			// console.debug(o);
			// console.debug(gx);
			

			function _toggle_radio(){
				var q  = $igk(this);
				if (this.checked){
					// console.debug("checked ? "+this.checked + " for "+ q.state);
					if (typeof(q.state) == 'undefined'){
							this.checked = false;
					}else{
						if (!q.state){
							q.state = true;
							this.checked = false; //reset state of the radio button
							
						}else{							
							q.state = false;
						}		
					}
					
				}
			};
				
			//			console.debug("rate init radio");
			//select all box o
			b.select(".box-o").each_all(function(){	
				var d = this.getAttribute("box-o-data");
				var c = 0;
				var sl = 0;
				var q = this;
				sl = this.select("input");
				var rc=0;
				
				function _initialize(){
				//this context of the node
					if (this.o.value in v){
						this.o.checked = true;
						rc ++;
					}
				};
				if (gx){
					if (d in gx){
						//create object of keys
						var ttab = gx[d].value;//.split('|');
						// console.debug("init bo 444 x");						
						// console.debug(ttab);
						v = igk.createObj(ttab,1);
						// console.debug("end ...");
						// console.debug(v);
						
						sl.each_all(_initialize);						
					}
				}
				
				q.fn.on = (rc >= sl.getCount());
				
				q.select("div").first().reg_event('click', function(){
					//console.debug("lick");
					if (!q.fn.on){
						//check all input
						sl.each_all(function(){
							this.o.checked = true;
							this.state = false;
						});
						q.fn.on=1;
					}else{
						//unckeck all input
						sl.each_all(function(){
							this.o.checked = false;
							this.state = true;
						});
						q.fn.on=0;
					}
				});
				
			});
			// console.debug("init radio");
			b.select("input").each_all(function()
			{
				if (this.o.type=="radio"){
					this.reg_event("click", _toggle_radio);
					this.state = this.o.checked;//store state
				}
			});
		
			
		}
	});

	igk.system.createNS("cgt.map",{
		init: function(){
			
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
						// console.debug("d = "+d);
						// console.debug("i = "+i);
						// console.debug(f);
						e.preventDefault();
					});
					var coords=[];
					pq.select('input.geo').each_all(function(){
						var cp = igk.JSON.parse(this.o.value);
						//console.debug(cp);
						cp.host = this;
						coords.push(cp);
					});
					var gm =igk.google && igk.google.maps;
					if (!gm)
					return;
					igk.google.maps.setMarker(0, coords, {click:__show_tips});
				});
				}			
				
				$igk(igk.getCurrentScript()).remove();							
		}
	
	});
})();