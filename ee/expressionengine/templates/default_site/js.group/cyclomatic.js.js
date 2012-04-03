/* network cyclomatic
 * complete with unnessesary inheritence!
 */

 
displayNoneId = function(objId) {
    document.getElementById(objId).style.display = "none";
};


function Cyclo(fader) {
		this.IMAGE_ID_PREFIX        = "lom-lead";
		this.IMAGE_CAPTION_PREFIX   = null;
		this.CONTROLS               = null;
		this.THUMB_ID_PREFIX        = "lom-thumb";
		this.BACKGROUND_CONTAINER   = "lead-image";
		this.THUMBS_CONTAINER       = "image-thumbs";
		this.AREA_CONTAINER         = "cyclomatic";
		this.NEXT_ID                = "lomnext";
		this.PREV_ID                = "lomprev";
		this.IMAGE_MAX              = 0;
		this.imagePos               = 0;
		this.currentTimeout         = 0;
		this.timeoutMutex           = 0;
		this.toFade                 = 0;
		this.toDisplay              = 0;
		this.toHighlight            = 0;
		this.toUnHighlight          = 0;
		this.toCaption              = 0;
		this.toUnCaption            = 0;
		this.prevImageSrc           = 0;
		this.isLocked               = false;
		this.cycloIndex             = null; 
		
		// get image count and set id's
		
		root = document.getElementById(this.BACKGROUND_CONTAINER);
		var i = 0;
		for(var i = 0; i != root.childNodes.length; i++) { 
			if(root.childNodes[i].nodeName === "IMG") {
				root.childNodes[i].style.position = "absolute";
				root.childNodes[i].style.display = "none";
				root.childNodes[i].id = this.IMAGE_ID_PREFIX + this.IMAGE_MAX;
				++(this.IMAGE_MAX);
			}
		}
		--(this.IMAGE_MAX);
		document.getElementById(this.IMAGE_ID_PREFIX+"0").style.display = "block";
		if(this.THUMBS_CONTAINER != null){
			root = document.getElementById(this.THUMBS_CONTAINER);
			var n = 0;
			for(var i = 0; i != root.childNodes.length; i++) { 
				if(root.childNodes[i].nodeName === "IMG") {
					root.childNodes[i].id = this.THUMB_ID_PREFIX + n;
					root.childNodes[i].style.cursor = "pointer";
					var relayOwner = this;
					root.childNodes[i].onclick = function() {
						// next 2 lines required to create a pupup when the user clicks on the thumbnails
						var t_number = parseInt(this.id.substring(9))+1;
                                                var car_id = document.getElementById("car_id").value;
						gallery_pop('{path="/search/gallery-pop-up"}' + car_id + '/' + t_number, 850,750);
						relayOwner.jump(parseInt(this.id.replace(relayOwner.THUMB_ID_PREFIX, "")), true);
					}
					++n;
				}
			}
		}
}


Cyclo.prototype.loop = function() {
    // mutex
    if(this.timeoutMutex == 0) {
        clearTimeout(this.currentTimeout);
        this.next(false);
        var relayOwner = this;
        function timerRelay() {
            relayOwner.loop();
        }
        this.currentTimeout = window.setTimeout(timerRelay, 6000);
    }
    return(true); 
};

Cyclo.prototype.start = function(unlock) {
    if(unlock) {
        this.isLocked = false;
    }
    
    if(!this.isLocked) {
        this.timeoutMutex = 0;
        clearTimeout(this.currentTimeout);
        var relayOwner = this;
        function timerRelay() {
            relayOwner.loop();
        }
        this.currentTimeout = window.setTimeout(timerRelay, 6000);
    }
    return(true);
};


Cyclo.prototype.stop = function(lock) {
    this.timeoutMutex = 1;
    clearTimeout(this.currentTimeout);
    
    if(lock) {
        this.isLocked = true;
    }
};


setOpacity = function(obj, opacity) {
    opacity = (opacity == 100)?99.999:opacity;
    // IE/Win
    obj.style.filter = "alpha(opacity:"+opacity+")";
    // Safari<1.2, Konqueror
    obj.style.KHTMLOpacity = opacity/100;
    // Older Mozilla and Firefox
    obj.style.MozOpacity = opacity/100;
    // Safari 1.2, newer Firefox and Mozilla, CSS3
    obj.style.opacity = opacity/100;
};


fadeIn = function(objId,opacity,amount) {
    if (document.getElementById) {
        // see if we have tweening lib available
        if(typeof(OpacityTween) != "undefined") {
            fadeObj = document.getElementById(objId);
            var opacityTween = new OpacityTween(fadeObj,Tween.regularEaseIn,0,100,1);
            opacityTween.start();
        } else {
            obj = document.getElementById(objId);
            if (opacity <= 99.999) {
                if(opacity < 50) {
                amount = amount*1.2;
                } else {
                    amount = amount/1.2;
                }
                setOpacity(obj, opacity);
                opacity += amount;
                this.faderTimeout = window.setTimeout("fadeIn('"+objId+"',"+opacity+","+amount+")", 8);
            } else {
                setOpacity(obj, 100);
                clearTimeout(this.faderTimeout);
            }
        }
    }
};

Cyclo.prototype.jump = function(position, immediate) {
    // set old image
    this.toFade = this.IMAGE_ID_PREFIX + this.imagePos;
    if(this.IMAGE_CAPTION_PREFIX != null) { 
        this.toUnCaption = this.IMAGE_CAPTION_PREFIX + this.imagePos;
    }
    this.toUnHighlight = this.THUMB_ID_PREFIX + this.imagePos;

    this.prevImageSrc = document.getElementById(this.toFade).getAttribute("src");
    this.backgroundElement.style.backgroundImage = "url("+this.prevImageSrc+")";
    document.getElementById(this.toFade).style.display = "none";

    // set new image
    this.imagePos = position;

    // fade or switch image
    this.toDisplay = this.IMAGE_ID_PREFIX + this.imagePos;
    if(this.IMAGE_CAPTION_PREFIX != null) { 
        this.toCaption = this.IMAGE_CAPTION_PREFIX + this.imagePos;
    }
    this.toHighlight = this.THUMB_ID_PREFIX + this.imagePos;
	
    if(immediate) {
        setOpacity((document.getElementById(this.toDisplay)), 100);
        document.getElementById(this.toDisplay).style.display = "block";
    } else {
        document.getElementById(this.toDisplay).style.display = "block";
        setOpacity((document.getElementById(this.toDisplay)), 0);
        fadeIn(this.toDisplay, 0, 2);
    }

    // switch caption
    if(this.IMAGE_CAPTION_PREFIX != null) {
        document.getElementById(this.toUnCaption).style.display = "none";
        document.getElementById(this.toCaption).style.display = "block";
    }

    if(this.THUMB_ID_PREFIX != "") {
        document.getElementById(this.toUnHighlight).className = document.getElementById(this.toHighlight).className.replace('active','');
        document.getElementById(this.toHighlight).className += ' active';
    }

    // set "image x of n" text
    if(this.cycloIndex != null) {
        this.cycloIndex.innerHTML = "Image "+(this.imagePos+1)+" of "+(this.IMAGE_MAX+1);
    }
};


Cyclo.prototype.next = function(immediate) {
    if(this.imagePos == this.IMAGE_MAX) { 
        this.jump(0, immediate); 
    } else {
        this.jump(this.imagePos+1);
    }
};

Cyclo.prototype.previous = function(immediate) {
    if(this.imagePos == 0) { 
            this.jump(this.IMAGE_MAX, immediate); 
        } else {
            this.jump(this.imagePos-1);
    }
};


Cyclo.prototype.init = function() {
    /*init page controls */
    if(this.CONTROLS != null) {
        document.getElementById(this.CONTROLS).style.display = "block";
    }
    document.getElementById(this.PREV_ID).owner = this;
    document.getElementById(this.PREV_ID).onclick = function() {
        this.owner.stop();
        this.owner.previous(true);
        this.owner.start();
        return false;
    };

    document.getElementById(this.NEXT_ID).owner = this;
    document.getElementById(this.NEXT_ID).onclick = function() {
        this.owner.stop();
        this.owner.next(true);
        this.owner.start();
        return false;
    };
    
    this.backgroundElement = document.getElementById(this.BACKGROUND_CONTAINER);
    this.areaElement = document.getElementById(this.AREA_CONTAINER);
    
    this.areaElement.owner = this;
    
    this.areaElement.onmouseover = function() {
        this.owner.stop();
        this.onkeypress = function(event) {
            if(event.keyCode != null) {
                if(event.keyCode == 37) {
                    this.owner.previous(true);
                } else if(event.keyCode == 39) {
                    this.owner.next(true);
                }
            }
        }   
    };
    
    this.areaElement.onmouseout = function() {
        this.owner.start();
        this.onkeypress = null;
    };
    
    // Finally, start
    this.start();
};

