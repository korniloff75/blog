//version 1.3
function openwindow(id, w, h, html){
	if(document.getElementById('bgwindow_'+id) == null){
		var bgobj = document.createElement('div');
		document.body.appendChild(bgobj);
		bgobj.style.opacity = '0';
		bgobj.style.transition = 'opacity 0.3s ease 0s';
		bgobj.id = 'bgwindow_'+id;
		bgobj.style.zIndex = '1000';
		bgobj.style.position = 'fixed';
		bgobj.style.backgroundColor = '#000';
		
			bgobj.style.filter = 'alpha(opacity=80)';// IE LOL
		bgobj.style.top = '0px';
		bgobj.style.bottom = '0px';
		bgobj.style.left = '0px';
		bgobj.style.right = '0px';
		bgobj.style.width = '100%';
		bgobj.style.height = '100%';
		bgobj.style.margin = '0px';
		bgobj.style.padding = '0px';
		
		bgobj.onclick = function(){
			closewindow(id);
		}
		
		setTimeout(function(){
						bgobj.style.opacity = '0.8';
		}, 100);
	}
	if(document.getElementById(id) == null){
		var obj = document.createElement('div');
		document.body.appendChild(obj);
		obj.style.opacity = '0';
		obj.style.transform = 'scaleY(0.7)';//
		obj.style.transition = 'transform 0.2s ease 0s, opacity 0.2s';//
		obj.innerHTML = html;
		obj.id = id;
		obj.className = 'window';
		obj.style.zIndex = '1001';
		obj.style.position = 'fixed';
		obj.style.top = '50%';
		obj.style.left = '50%';
		obj.style.width = w + 'px';
		if(h == 'auto'){h = obj.offsetHeight;}else{obj.style.height = h + 'px';}
		var mleft = Math.round(w / 2);
		var mtop = Math.round(h / 1.8);
		obj.style.margin = '-'+ mtop +'px auto  auto -'+ mleft +'px';
		setTimeout(function(){
						obj.style.opacity = '1';
						obj.style.transform = 'scale(1)';//
		}, 100);
	}
}
function closewindow(id){
	if(document.getElementById('bgwindow_'+id)){
		var bgobj = document.getElementById('bgwindow_'+id);
		bgobj.style.opacity = '0';
		setTimeout(function(){
						bgobj.parentNode.removeChild(bgobj);
		}, 500);
	}
	if(document.getElementById(id)){
		var obj = document.getElementById(id);
		obj.parentNode.removeChild(obj);
	}
}