
(function(){
	if (igk.winui.paginationview)
		return;
	
	// console.debug("currentstate ")
	// console.debug(window.history.state);
	
	
	var popstate=0;
	var current = [];
	igk.system.createNS("igk.winui.paginationview",{
		init:function(){
		
			
			function _page_ready(q, i, t, cl, r){
				return function(xhr){
						if (this.isReady()){
						
							
							if (i.target)
								q.select(i.target).setHtml(xhr.responseText).init();
							else
								igk.ajx.fn.replace_or_append_to_body.apply(this, [xhr]);
							
							if (typeof(r)=='undefined'){
								//console.debug("push :  "+t);
								igk.winui.history.push(t, {title:'change','igk-rol':'pagination','olduri':cl},'history page ');
							}
							else{
								//console.debug("replace : "+t);
								igk.winui.history.replace(t);
							}
							var p = $igk('.location').first();
							if (p){
								p.setHtml(cl);
							}
							
						}
				};
			};
			var q = $igk(igk.getParentScript());
			var i = igk.initObj(igk.JSON.parse(q.getAttribute("igk:data")), {
				baseuri:window.location.href,
				ajx:0,
				history:0,
				refresh:0,
				cookie:0
			});
		
			var uri = encodeURI(i.baseuri).replace("?", '\\?');		
			i.regex = new RegExp("^"+uri+"");
			
			if (!popstate){
				// console.debug("reg event");
				igk.winui.reg_event(window, 'popstate', function(e){
					// console.debug(current.length);
					
					var du = document.location.href;
					var ldu = du + (du.indexOf("?")!=-1 ? "?":"") + "?reset=1";
						if(i.cookie){
							var nc  = 'local_com/'+i.cookie;
							// console.debug("remove cookie  "+nc);//i.cookie);
							igk.web.rmcookies(nc); //'local_com/'+i.cookie);
						}
					if (i.regex.test(document.location.href)){
						// console.debug("match");
						igk.ajx.get(du, null, _page_ready(q,i,du, du, 1));						
						current.pop();
					
					}else{
						current.pop();
						current.push(document.location.href);
						// if ((e.state==null)  && (current.length>0)){
							// current.pop();
							
							// console.debug("not match");
							// console.debug(e);
							// console.debug(current);	
							igk.ajx.get(ldu, null, _page_ready(q,i,du, du, 1));
						// }
					}
					
					e.preventDefault();
					e.stopPropagation();
					return;
				// //	/^+i.baseuri+/.test(document.location.href
					// // if (e.state ==null){
						// // console.debug('state is null'+ document.referrer);
						// // console.debug(current);
						// // if (current.length>0){
							// // var suri  = current.pop();
							// // igk.ajx.get(suri, null, _page_ready(q,i,document.location.href, suri, 1));
						// // }
						// // else{
							// reload the current document page						
							// // igk.ajx.get(document.location.href, null, _page_ready(q,i,document.location.href,current.pop(), 1));
						// // }
						// // return;
					// // }
					
					// // var r=e.state['igk-rol'];
					// // if (r){
						// // console.debug("navigate :"+document.location.href);
						// // console.debug("olduriss :"+e.state.olduri);
						// // igk.ajx.get(document.location.href, null, _page_ready(q,i,document.location.href, document.location.href, 1));
						// // current.pop();
					// // }

				});
				popstate=1;
			}
			
			q.select("span").each_all(function(){
				this.addClass("igk-btn").reg_event("click", function(e){
						e.preventDefault();
						e.stopPropagation();
						var c = this.getAttribute("rol");
						var u = i.baseuri;
						var t = 0;
						if(!c){
							t = u+this.innerHTML;
						}else{
							var s = i.selected;
							switch(c){
								case "next":
									if (s <i.max)
										t = u+(s+1);
								break;
								case "prev":
									if (s >i.min)
										t = u+(s-1);
								break;
							}
						}
						if (t){
							if (i.ajx){
								var cl = document.location.href;
								if (t==cl){
									//refresh the page
									if (i.refresh){
										igk.ajx.get(t, null,null);
									}
								}else{
									igk.ajx.get(t, null,_page_ready(q,i, t, cl));
									current.push(t);
								}
							}
							else{ 
								//console.debug(t);
								document.location=t;
							}
						}
				});
			});
		}
	});
})();