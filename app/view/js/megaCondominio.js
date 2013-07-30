var messageObj = new DHTML_modalMessage();	// We only create one object of this class
var vRootUrl	= '%ROOT_URL%';

function isNumeric(sText) {
   var ValidChars = "0123456789.";
   var IsNumber=true;
   var Char;

   for (i = 0; i < sText.length && IsNumber == true; i++) { 
      Char = sText.charAt(i); 
      if (ValidChars.indexOf(Char) == -1) {
         IsNumber = false;
      }
   }
   return IsNumber;
}

function isRange(pNum,pMin,pMax) {
	
	if (isNumeric(pNum) == false) return false;
	if (pNum < pMin || pNum > pMax) return false;
	
	return true;
}

function MCValidaDiaMes(pNum) {
	if (!pNum) return true;
	return (isRange(pNum,1,31));
}

function lpad (str,len,pad) {
  pad = pad || ' ';
  while(str.length < len) str = pad + str;
  return str;
}

function rpad (str,len,pad) {
  pad = pad || ' ';
  while(str.length < len) str = str + pad;
  return str;
}

function mostraMensagem() {
	if (vTextMessage != '') {
		messageObj.setShadowOffset(5);	// Large shadow
		messageObj.setSource("%BIN_URL%mostraMensagem.php?mensagem="+vTextMessage);
		messageObj.setCssClassMessageBox(null);
		messageObj.setShadowDivVisible(false);	// Disable shadow for these boxes	
		messageObj.setSize(400,200);
		messageObj.display();
	}
}

function fechaMensagem() {
		messageObj.close();
}

function voltarPagina() {
	history.back();
}

function MCApplyMasks() {
	divs = document.body.getElementsByTagName("div");
	for(var i=0; i< divs.length; i++){
		var node = divs[i];
		if (divs[i].className.indexOf("MCMask") != -1) {
			itens	= divs[i].getElementsByTagName("input");
			for (var j=0; j< itens.length; j++){
				//itens[j].className = "MCObject "+divs[i].className;
				classes = divs[i].className.split(" ");
				for (k = 0; k < classes.length; k++) {
					if (classes[k].substr(0,6) == "MCMask") {
						itens[j].alt	= classes[k];
					}else{
						itens[j].className += " "+classes[k];
					}
				}
				//alert(itens[j].type);
				//itens[j].className = divs[i].className;
				//alert('Nome: '+itens[j].name+' -> '+itens[j].className);
			}
		}
	}
	MCApplyCSS();
}

function MCApplyCSS() {
	inputs = document.body.getElementsByTagName("input");
	for(var i=0; i< inputs.length; i++){
		var node = inputs[i];
		node.className += " MCObject";
	}
}


function MCApplyFormValues(form) {
	form.forEachItem(function(name){
		if (form.getItemType(name) == 'input') {
			form.setItemValue(name, form.getInput(name).value);
		} 
	});
}


function showM(mensagem) {
	var div 	= document.createElement('div');
	div.id 		= 'DMCMID';
	div.style	= "position:relative; width:420px; height: 200px; border: #cecece 1px solid; margin: 12px;";
	document.body.appendChild(div);
	ajaxFunction(div.id,"%BIN_URL%mostraMensagem.php");
	
}

function ajaxFunction(id, url){
	var xmlHttp;
	try {// Firefox, Opera 8.0+, Safari
		xmlHttp = new XMLHttpRequest();		
	} catch (e) {// Internet Explorer
		try {
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	
	var elem = document.getElementById(id);
	if (!elem) {
		alert('The element with the passed ID doesnt exists in your page');
		return;
	}

	xmlHttp.onreadystatechange = function(){
		loadpage(xmlHttp, id);
	};


	xmlHttp.open("GET", url, true);
	xmlHttp.send(null);
}		

function loadpage(page_request, containerid){
	if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1)) {
		document.getElementById(containerid).innerHTML=page_request.responseText;
	}
}



var req;

function loadXMLDoc(url,id) {
	// branch for native XMLHttpRequest object
	if (window.XMLHttpRequest) {
	    req = new XMLHttpRequest();
	    req.onreadystatechange = processReqChange(id);
	    req.open("GET", url);
	    req.send(null);
	    // branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
	    req = new ActiveXObject("Microsoft.XMLHTTP");
	    if (req) {
	        req.onreadystatechange = processReqChange(id);
	        req.open("GET", url);
	        req.send();
	    }
	}
}

function processReqChange(id) {
    // only if req shows "complete"
    if (req.readyState == 4) {
        // only if "OK"
        if (req.status == 200) {
            // process the result
            document.getElementById(id).innerHTML = req.responseText;
        } else {
            alert("There was a problem retrieving the XML data:\n" + req.statusText);
        }
    }
}
