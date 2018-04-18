//author: C.A.D BONDJE 
//date : 20-05-2017
//desctription : balafonjs script page designer


//dsg/mt : design media type cookies: store if we use the media type definition to store current data information

"uses sctrict";
(function(){

function __init(){		
	if(document.body.firstChild != this.o){
		//this.remove();
		document.body.insertBefore(this.o, document.body.firstChild);		
	}
	else{
		console.debug("is first the child");
	}
	$igk(document.body).addClass('igk-dsg-mode');
	var progress;
	
			// progress = igk.createNode("div");
				// progress.addClass("igk-winui-track");
				// progress.add("div").addClass("track").setCss({width:"50%"});
				// document.body.insertBefore(progress.o, document.body.firstChild);
				// progress.init();
				
				
	igk.winui.dragdrop.init(document.body, {
		uri: this.getAttribute('dropuri'),//"!@sys//design/drop",
		supported:'text/html,image/jpeg,image/png,video/mp4', 
		async:true,
		start:function(){
			if (!progress){
				progress = igk.createNode("div");
				progress.addClass("igk-winui-track");
				progress.add("div").addClass("track");
				document.body.insertBefore(progress.o, document.body.firstChild);
				progress.init();
			}else{				
				document.body.insertBefore(progress.o, document.body.firstChild);
			}
			progress.select('.track').setCss({width:"0%"});
		},
		update:null,
		done:function(evt){
			progress.remove();
		},
		progress:function(i){
			progress.select('.track').setCss({width: Math.round((i.loaded/i.total)*100, 2)+"%"});
		},
		drop:function(evt){
			igk.winui.dragdrop.fn.update_file.apply(this, arguments);
		},
		enter:null,
		over:null,
		leave:null		
	});
};
var _NS=0;
var m_prop=0;
igk.system.createNS("igk.winui.designer", {
	inithierarchi:function(q){
		if (!m_prop || typeof(q) =='undefined'){
			console.error("can't init hierachy");
			return;
		}
		igk.ajx.post(m_prop.uri+'getHierachi', null, function(xhr){
			if (this.isReady()){
				var p = $igk(q.parentNode);
				p.setHtml(xhr.responseText);
				p.init();
				p.qselect("ul[role='alignment']").each_all(function(){
					this.qselect('a[role]').each_all(function(){
						//console.debug("lkj");
						var r = this.getAttribute('role');
						this.reg_event("click", function(evt){
							igk.evts.stop(evt);							
							if(_NS.align)
							_NS.align(r);					
						});
					});
				});
				p.qselect("ul[role='font']").each_all(function(){
					this.qselect('a[role]').each_all(function(){
						//console.debug("lkj");
						var r = this.getAttribute('role');
						this.reg_event("click", function(evt){
							igk.evts.stop(evt);
							if (_NS.font)
							_NS.font(r);					
						});
					});
				});
				p.qselect("a[role='store']").each_all(function(){
					this.reg_event('click', function(evt){
						igk.evts.stop(evt);
						igk.ajx.post(this.getAttribute('href'), 
						igk.winui.designer.getDesignerPostData()												
						//'data='+igk.winui.designer.getstyledef()
						,function(){
							
						});
					});
				});
			}
		});
	},
	init:function(q, prop){
		m_prop = prop;
		$igk(q).remove();
	}
});


_NS=igk.winui.designer;

igk.winui.initClassControl("igk-winui-designer",__init);


igk.ready(function(){
	
	//alert($igk(".igk-winui-designer"));
	//console.debug("kjdf---------------");
});

// alert( "the document "+document.readyState);




//control for design

(function(){
	var rects=[];
	var rs_mode = 0;
	var _vdirs=['top','left','right','bottom'];
	var _CSS = {}; //css data to serialize
	
	function __initRect(){
		var H = 0;//global height
		var W = 0; //global width
		var L=0;
		var T=0;
		var n = null;
		var q = this;
		var sc=0;//source capture
		var ack = 0;//actionv
		var sbutton={
			cssDef:{
				left: q.getComputedStyle('left'),
				top: q.getComputedStyle('top'),
				right: q.getComputedStyle('right'),
				bottom: q.getComputedStyle('bottom'),
				fontSize: q.getComputedStyle('fontSize'),
				textAlign: q.getComputedStyle('textAlign'),
				width: q.getComputedStyle('width'),
				height: q.getComputedStyle('height')
			}
		};
		var rdu;
		//IE 11 make some error for size because au margin calculation.
		
		
		function _update(){			
			switch(sbutton.f){
				case 4:
					sbutton.cssDef.height=(H +sbutton.outy)+"px";
				break;
				case 1: //display line :top
				
					if (sbutton.pos=="relative"){
						sbutton.cssDef.top=(T +sbutton.outy)+"px";	
					}
					break;
				case 2: //display line : left
					if (sbutton.pos=="relative"){
						sbutton.cssDef.left=(L +sbutton.outx)+"px";	
					}
				break;
				case 3:
					sbutton.cssDef.width =(W +sbutton.outx)+"px";
				break;
			}
			q.setCss(sbutton.cssDef);
			_setdu(_style_def());
		};
		function _setdu(s){
			if (!rdu && !sbutton.du){
				sbutton.du = $igk("#du").getItemAt(0);
				rdu=1;
			}
			if (sbutton.du){
				sbutton.du.setHtml(s);
			}
					
		};
		function _reset(){
			q.setCss({
				left:'auto',
				top:'auto',
				width:'auto',
				height:'auto'
			});
		};
		function _align(t){
			sbutton.cssDef.textAlign=t;
			q.setCss(sbutton.cssDef);
			// {
				// textAlign:t
			// });
			if (t=='justify'){
				//not supported for chrome or firefox
				q.setCss({"textJustify":'none'});
			}
			_setdu(_style_def());
		};
		function _fonts(t){
			sbutton.cssDef.fontSize = t;
			q.setCss(sbutton.cssDef);
			// {
				// fontSize:t
			// });
			_setdu(_style_def());
		};
		function _style_def(){
			var o = "";
			o=  q.getCssSelector()+"{";
			o+= q.o.style["cssText"];
			o+="}";
			return o;
		}
		function _designerPostData(){
			//get return data to post for store
			var mt = igk.web.getcookies("dsg/mt");
			var m = 'global';			
			q.setCss(sbutton.cssDef);
			if (mt==1){
				m = igk.css.getMediaType();
			}
			//console.debug(" m = "+m);
			if (!(m in _CSS)){
				_CSS[m]={};
			}
			_CSS[m][q.getCssSelector()]=q.o.style["cssText"];
			return "q="+igk.utils.Base64.encode(JSON.stringify(_CSS));
		}
		
		igk.winui.designer.reset = _reset;
		igk.winui.designer.align = _align;
		igk.winui.designer.font = _fonts;
		igk.winui.designer.getstyledef=_style_def;
		igk.winui.designer.getDesignerPostData=_designerPostData;
		
		
		function _ms_down(evt){
			var n = $igk(evt.target);		
			if (igk.winui.mouseButton(evt)==
			igk.winui.mouseButton.Left){
			// console.debug("mouse down "+n.fn.tag);	
			H = igk.getNumber(q.getComputedStyle("height"));
			W = igk.getNumber(q.getComputedStyle("width"));
			T = igk.getNumber(q.getComputedStyle("top"));
			L = igk.getNumber(q.getComputedStyle("left"));
			sc =n;
			igk.winui.mouseCapture.setCapture(sc);
			ack=1;
			//console.debug(W);
			sbutton.f= n.fn.tag;
			sbutton.x = evt.screenX;
			sbutton.y = evt.screenY;
			sbutton.outx=0;
			sbutton.outy=0;
			sbutton.pos = q.getComputedStyle("position");
			evt.preventDefault();
			evt.stopPropagation();
			}
		};
		function _ms_move(evt){
			
			//$igk("#du").getItemAt(0).setHtml('dim :'+Math.round(evt.screenX)+" x "+ Math.round(evt.screenY)+"<br />Target:"+evt.target) ;
			if (ack){
			var n = $igk(evt.target);				
			sbutton.outx = -sbutton.x + evt.screenX;
			sbutton.outy = -sbutton.y + evt.screenY;
			_update();
			evt.preventDefault();
			evt.stopPropagation();
			}
		};
		function _ms_up(evt){
			_update();			
			igk.winui.mouseCapture.releaseCapture(sc);
			ack =0;
			evt.preventDefault();
			evt.stopPropagation();
		};
		
		for(var i =0;i<_vdirs.length;i++){
			n = this.add('div').addClass("snippet "+_vdirs[i])
			.reg_event("mousedown", _ms_down)
			.reg_event("mousemove", _ms_move)
			.reg_event("mouseup", _ms_up)
			;
			n.fn.tag=i+1;	
			
		}
		// igk.winui.reg_event(window,"mousedown", _ms_down);
		// igk.winui.reg_event(window,"mousemove", _ms_move);
		// igk.winui.reg_event(window,"mouseup", _ms_up);
		// var n1 = this.add('div').addClass("snippet top").reg_event("mousedown", _ms_down);
		// n1.fn.tag=1;
		// var n2 = this.add('div').addClass("snippet right").reg_event("mousedown", _ms_down);
		// n2.fn.tag=2;		
		// var n3 = this.add('div').addClass("snippet bottom").reg_event("mousedown", _ms_down);
		// n3.fn.tag=3;
		// var n4 = this.add('div').addClass("snippet left").reg_event("mousedown", _ms_down);
		// n4.fn.tag=4;
	};
	igk.winui.initClassControl("igk-dsg-rect", __initRect);
	
})();


})();