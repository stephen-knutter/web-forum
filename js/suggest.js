function start(){
	var oTextbox = new suggestControl(document.getElementById("txtState"), new suggestionProvider());
}

function createXmlHttpRequestObject(){
	var xmlHttp;
	try{
		xmlHttp = new XMLHttpRequest();
	} catch(e){
		var xmlHttpVersions = new Array(
			"MSXML2.XMLHTTP.6.0",
			"MSXML2.XMLHTTP.5.0",
			"MSXML2.XMLHTTP.4.0",
			"MSXML2.XMLHTTP.3.0",
			"MSXML2.XMLHTTP",
			"Microsoft.XMLHTTP"
		);
		for(var i=0; i < XmlHttpVersions.length && !xmlHttp; i++){
			try{
				xmlHttp = new ActiveXObject(xmlHttpVersions[i]);
			} catch(e){}
		}
	}
	if(!xmlHttp){
		alert("Error XMLHttpRequest Object could not be created");
	} else {
		return xmlHttp;
	}
}

function suggestControl(oTextbox, oProvider){
	this.cur = -1;
	this.layer = null;
	this.textbox = oTextbox;
	this.userText = oTextbox.value;
	this.provider = oProvider;
	this.timeoutId = null;
	this.init();
}

suggestControl.prototype.createDropDown = function(){
	this.layer = document.createElement("div");
	this.layer.className = "suggestions";
	this.layer.style.visibility = "hidden";
	this.layer.style.width = this.textbox.offsetWidth;
	document.body.appendChild(this.layer);
	
	oThis = this;
	this.layer.onmousedown = 
	this.layer.onmouseup =
	this.layer.onmouseover = function(oEvent){
		oEvent = oEvent || window.event;
		oTarget = oEvent.target || oEvent.srcElement;
		
		if(oEvent.type == "mousedown"){
			window.location = oTarget.href;
		} else if(oEvent.type == "mouseover"){
			oThis.highlightSuggestion(oTarget);
		} else {
			oThis.textbox.focus();
		}
	}
};


suggestControl.prototype.highlightSuggestion = function(oSuggestionNode){
	for(var i=0; i < this.layer.childNodes.length; i++){
		var oNode = this.layer.childNodes[i];
		if(oNode == oSuggestionNode){
			oNode.className = "current";
		} else if(oNode.className == "current"){
			oNode.className = "";
		}
	}
};

suggestControl.prototype.handleKeyUp = function(oEvent){
	var iKeyCode = oEvent.keyCode;
	
	var oThis = this;
	
	this.userText = this.textbox.value;
	
	clearTimeout(this.timeoutId);
	
	if(iKeyCode == 8 || iKeyCode == 46){
		this.timeoutId = setTimeout(function(){
			oThis.provider.requestSuggestions(oThis, false);
		}, 250);
	} else if((iKeyCode != 16 && iKeyCode < 32) || (iKeyCode >= 33 && iKeyCode < 46) || 
	(iKeyCode >= 112 && iKeyCode <= 123)){
		//ignore
	} else {
		this.timeoutId = setTimeout(function(){
			oThis.provider.requestSuggestions(oThis, true);
		}, 250);
	}
};

suggestControl.prototype.xmlToArray = function(aSuggestions){
	var resultsRootXml = aSuggestions.documentElement;
	var resultsNodeXml = resultsRootXml.getElementsByTagName("name");
	
	var resultsArray = new Array();
	for(i=0; i<resultsNodeXml.length; i++){
		resultsArray[i] = resultsNodeXml.item(i).firstChild.data;
	}
	return resultsArray;
}

suggestControl.prototype.xmlToArrayId = function(aSuggestions){
	var resultsRootXml = aSuggestions.documentElement;
	var resultsNodeXml = resultsRootXml.getElementsByTagName("name");
	
	var idArray = new Array();
	for(i=0; i<resultsNodeXml.length; i++){
		idArray[i] = resultsNodeXml[i].getAttribute("postid");
	}
	return idArray;
}

suggestControl.prototype.autosuggest = function(aSuggestion, bTypeAhead){
	var bSuggestions = this.xmlToArray(aSuggestion);
	var suggestionId = this.xmlToArrayId(aSuggestion);
	
	this.cur = -1;
	
	if(bSuggestions){
	
		this.showSuggestions(bSuggestions, suggestionId);
	} else {
		this.hideSuggestions();
	}
};

suggestControl.prototype.typeAhead = function(){};

suggestControl.prototype.selectRange = function(){};

suggestControl.prototype.getLeft = function(){
	var oNode = this.textbox;
	var iLeft = 0;
	
	while(oNode != document.body){
		iLeft += oNode.offsetLeft;
		oNode = oNode.offsetParent;
	}
	
	return iLeft;
};

suggestControl.prototype.getTop = function(){
	var oNode = this.textbox;
	var iTop = 0;
	
	while(oNode != document.body){
		iTop += oNode.offsetTop;
		oNode = oNode.offsetParent;
	}
	
	return iTop;
};

suggestControl.prototype.showSuggestions = function(bSuggestions, suggestionId){
	this.layer.innerHTML = "";
	
	for(var i=0; i < bSuggestions.length; i++){
		aLink = document.createElement("a");
		aLink.href = 'view_post.php?postid='+suggestionId[i];
		aLink.appendChild(document.createTextNode(bSuggestions[i]));
		this.layer.appendChild(aLink);
	}
	
	this.layer.style.left = this.getLeft() + "px";
	this.layer.style.top = (this.getTop() + this.textbox.offsetHeight) + "px";
	this.layer.style.visibility = "visible";
};

suggestControl.prototype.handleKeyDown = function(oEvent){
	switch(oEvent.keyCode){
		case 38: // up
			this.goToSuggestion(-1);
			break;
		case 40: // down
			this.goToSuggestion(1);
			break;
		case 27: // esc
			this.textbox.value = this.userText;
			this.selectRange(this.userText.length, 0);
		case 13:
			window.location = this.layer.childNodes[this.cur].href;
		break;
	} 
};

suggestControl.prototype.goToSuggestion = function(iDiff){
	var cSuggestionNodes = this.layer.childNodes;
	
	if(cSuggestionNodes.length > 0){
		var oNode = null;
		
		if(iDiff > 0){
			if(this.cur < cSuggestionNodes.length-1){
				oNode = cSuggestionNodes[++this.cur];
			}
		} else {
			if(this.cur > 0){
				oNode = cSuggestionNodes[--this.cur];
			}
		}
		
		if(oNode){
			this.highlightSuggestion(oNode);
			this.textbox.value = oNode.firstChild.nodeValue;
		}
	}
};


suggestControl.prototype.hideSuggestions = function(){

	this.layer.style.visibility = "hidden";

};


suggestControl.prototype.init = function(){
	oThis = this;
	
	this.textbox.onkeyup = function(oEvent){
		if(!oEvent){
			oEvent = window.event;
		}
		
		oThis.handleKeyUp(oEvent);
	}
	
	this.textbox.onkeydown = function(oEvent){
		if(!oEvent){
			oEvent = window.event;
		}
		
		oThis.handleKeyDown(oEvent);
	}
	
	
	this.textbox.onblur = function(){
		oThis.hideSuggestions();
	}
	
	
	this.createDropDown();
}

suggestionProvider = function(){
	this.xhr = createXmlHttpRequestObject();
};

suggestionProvider.prototype.requestSuggestions = function(oAutoSuggestControl, bTypeAhead){
	var oXHR = this.xhr;
	
	var oData = "?keyword=" + oAutoSuggestControl.userText;
	
	oXHR.open("GET", "suggest.php" + oData, true);
	oXHR.onreadystatechange = function(){
		if(oXHR.readyState == 4){
			var aSuggestions = oXHR.responseXML;
			
			oAutoSuggestControl.autosuggest(aSuggestions, bTypeAhead);
		}
	}
	
	oXHR.send(null);
};













