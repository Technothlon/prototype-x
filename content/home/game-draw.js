/*
 * 
 * Environment variables
 */

ENV = {
    debug: true,
    size: 72,
    source_pos: { x:872, y: 100 }, // size * 7 + offset
    offset: {
        x: 00,
        y: 100
    },
    speed: 2000,
    fps: 480
};

Array.prototype.erase = function (item) {
	for (var i = this.length; i--; i) {
		if (this[i] === item) this.splice(i, 1);
	}

	return this;
};

Function.prototype.bind = function (bind) {
	var self = this;
	return function () {
		var args = Array.prototype.slice.call(arguments);
		return self.apply(bind || null, args);
	};
};

merge = function (original, extended) {
	for (var key in extended) {
		var ext = extended[key];
		if (typeof (ext) != 'object' || ext instanceof Class) {
			original[key] = ext;
		} else {
			if (!original[key] || typeof (original[key]) != 'object') {
				original[key] = {};
			}
			merge(original[key], ext);
		}
	}
	return original;
};

function copy(object) {
	if (!object || typeof (object) != 'object' || object instanceof Class) {
		return object;
	} else if (object instanceof Array) {
		var c = [];
		for (var i = 0, l = object.length; i < l; i++) {
			c[i] = copy(object[i]);
		}
		return c;
	} else {
		var c = {};
		for (var i in object) {
			c[i] = copy(object[i]);
		}
		return c;
	}
}

function ksort(obj) {
	if (!obj || typeof (obj) != 'object') {
		return [];
	}

	var keys = [],
		values = [];
	for (var i in obj) {
		keys.push(i);
	}

	keys.sort();
	for (var i = 0; i < keys.length; i++) {
		values.push(obj[keys[i]]);
	}

	return values;
}

// -----------------------------------------------------------------------------
// Class object based on John Resigs code; inspired by base2 and Prototype
// http://ejohn.org/blog/simple-javascript-inheritance/
(function () {
	var initializing = false,
		fnTest = /xyz/.test(function () {
			xyz;
		}) ? /\bparent\b/ : /.*/;

	this.Class = function () {};
	var inject = function (prop) {
		var proto = this.prototype;
		var parent = {};
		for (var name in prop) {
			if (typeof (prop[name]) == "function" && typeof (proto[name]) == "function" && fnTest.test(prop[name])) {
				parent[name] = proto[name]; // save original function
				proto[name] = (function (name, fn) {
					return function () {
						var tmp = this.parent;
						this.parent = parent[name];
						var ret = fn.apply(this, arguments);
						this.parent = tmp;
						return ret;
					};
				})(name, prop[name]);
			} else {
				proto[name] = prop[name];
			}
		}
	};

	this.Class.extend = function (prop) {
		var parent = this.prototype;

		initializing = true;
		var prototype = new this();
		initializing = false;

		for (var name in prop) {
			if (typeof (prop[name]) == "function" && typeof (parent[name]) == "function" && fnTest.test(prop[name])) {
				prototype[name] = (function (name, fn) {
					return function () {
						var tmp = this.parent;
						this.parent = parent[name];
						var ret = fn.apply(this, arguments);
						this.parent = tmp;
						return ret;
					};
				})(name, prop[name]);
			} else {
				prototype[name] = prop[name];
			}
		}

		function Class() {
			if (!initializing) {

				// If this class has a staticInstantiate method, invoke it
				// and check if we got something back. If not, the normal
				// constructor (init) is called.
				if (this.staticInstantiate) {
					var obj = this.staticInstantiate.apply(this, arguments);
					if (obj) {
						return obj;
					}
				}

				for (var p in this) {
					if (typeof (this[p]) == 'object') {
						this[p] = copy(this[p]); // deep copy!
					}
				}

				if (this.init) {
					this.init.apply(this, arguments);
				}
			}

			return this;
		}

		Class.prototype = prototype;
		Class.constructor = Class;
		Class.extend = arguments.callee;
		Class.inject = inject;

		return Class;
	};

})();

newGuid_short = function () {
	var S4 = function () {
		return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
	};
	return (S4()).toString();
};
/*
 * 
 * Little bit maths
 */
function sgnm(a){
        if(a>0) return 1;
        if(a<0) return -1;
        return 0;
}

/*
 * Sprite loading function and dictionary
 */
var SpriteSheetsDictionary = {},SpritesheetLoaded = 0;
function isSpriteSheetsDictionaryReady(){
    var count = 0;
    for(var key in SpriteSheetsDictionary){
        var val = SpriteSheetsDictionary[key];
        if(val !== 'undefined' && val.loaded && val.ready)
            count++;
    }
    if(count === SpritesheetLoaded)
        return true;
    return false;
}
SpriteSheet = Class.extend({
    img: null,
    url: "",
    sprites: [],
    loaded:false,
    ready:false,
    
    init: function(){},
    
    load: function(imgName){
        this.url = imgName;
        var img = new Image();
        var parent = this;
        img.onload = function(){            
            parent.loaded = true;
        };
        img.src = this.url;
        this.img = img;
        SpriteSheetsDictionary[imgName] = this;
    },
    defSprite: function(name, x, y, w, h, cx, cy){
        var spt = {
			"id": name,
			"x": x,
			"y": y,
			"w": w,
			"h": h,
			"cx": cx === null ? 0 : cx,
			"cy": cy === null ? 0 : cy
		};
        this.sprites.push(spt);
    },
    parseAtlasDefinition: function (atlasJSON) {
        var parsed = JSON.parse(atlasJSON);        
        for( var key in parsed.frames){
            try{
                var sprite = parsed.frames[key];            
                var cx = - sprite.sourceSize.w * 0.5,
                    cy = - sprite.sourceSize.h * 0.5;
                if(sprite.trimmed){
                    cx =  sprite.spriteSourceSize.x - ( sprite.sourceSize.w * 0.5 );
                    cy =  sprite.spriteSourceSize.y - ( sprite.sourceSize.h * 0.5 );
                }
                this.defSprite(sprite.filename, sprite.frame.x, sprite.frame.y, sprite.frame.w, sprite.frame.h, cx, cy);
            } catch(e){
                log(ERROR.core, "Parsing JSON Sprite "+key, e);
            }
        }
    },
    getStats: function (name) {
        for(var i = 0; i < this.sprites.length; i++) {
             if(this.sprites[i].id === name) {
                return this.sprites[i];
             }
        }
        return null;
    }
});


/*
 * Drawing funtions
 */
function loadSpriteSheets(a){
    for(var i = 0; i < a.length; i++){
        var sptSheet = new SpriteSheet();
        sptSheet.load(a[i].image);
        xhrGet(a[i].json, function(resp){
            //log(ERROR.info, "Sprite JSON", resp.target.response);
            sptSheet.parseAtlasDefinition(resp.target.response);            
            sptSheet.ready = true;            
        });
        SpritesheetLoaded++;
    }
}

function drawSprite(spriteName, posX, posY){
    for(var key in SpriteSheetsDictionary){
        var sheet = SpriteSheetsDictionary[key];
        var sprite = sheet.getStats(spriteName);
        if(sprite === null) continue;
        __drawSprite(sprite,sheet,posX,posY);
            //log(ERROR.info, "Image drawn "+spriteName, null);
        return;
    }
}

function __drawSprite(sprite,sheet,posX,posY){
    if(sprite === null || sheet === null){
        log(ERROR.core, "__drawSprite", "sprite_ not found");
        return;
    }
    DT.ctx.drawImage(sheet.img,
                    sprite.x, sprite.y, sprite.w, sprite.h,
                    posX, posY, sprite.w, sprite.h);
    //log(ERROR.info, "Image drawn ", sprite);

}

function drawSpriteX(spriteName, posX, posY, w, h){
    for(var key in SpriteSheetsDictionary){
        var sheet = SpriteSheetsDictionary[key];
        var sprite = sheet.getStats(spriteName);
        if(sprite === null) continue;
        __drawSpriteX(sprite,sheet,posX,posY,w,h);
           // log(ERROR.info, "Image drawn "+spriteName, null);
        return;
    }
}

function __drawSpriteX(sprite,sheet,posX,posY, w, h){
    if(sprite === null || sheet === null){
        log(ERROR.core, "__drawSprite", "sprite_ not found");
        return;
    }
    DT.ctx.drawImage(sheet.img,
                    sprite.x, sprite.y, sprite.w, sprite.h,
                    posX, posY, w, h);
    //log(ERROR.info, "Image drawn ", sprite);

}

function hoverDrawSpriteX(spriteName, posX, posY, w, h){
    for(var key in SpriteSheetsDictionary){
        var sheet = SpriteSheetsDictionary[key];
        var sprite = sheet.getStats(spriteName);
        if(sprite === null) continue;
        __hoverDrawSpriteX(sprite,sheet,posX,posY,w,h);
           // log(ERROR.info, "Image drawn "+spriteName, null);
        return;
    }
}

function __hoverDrawSpriteX(sprite,sheet,posX,posY, w, h){
    if(sprite === null || sheet === null){
        log(ERROR.core, "__drawSprite", "sprite_ not found");
        return;
    }
    DT.hctx.drawImage(sheet.img,
                    sprite.x, sprite.y, sprite.w, sprite.h,
                    posX, posY, w, h);
    //log(ERROR.info, "Image drawn ", sprite);

}

var Canvas = Class.extend({
   canvas: null,
   hover: null,
   ctx: null,
   hctx:null,
   init: function(){}, 
   set: function(c,h){
       this.canvas = c;
       this.hover = h;
       this.hctx = this.hover.getContext('2d');
       this.ctx = this.canvas.getContext('2d');
   },
   clear: function(){
       this.ctx.clearRect(0,0,this.canvas.width,this.canvas.height);       
   },
   clearHover: function(){
       this.hctx.clearRect(0,0,this.hover.width,this.hover.height);
   }
});
var DT = new Canvas();

/*
 * Error logging funtions
 */
var ERROR = {
    info: "[INFO]",
    fatal: "[FATAL]",
    core: "[CORE]",
    net: "[NETWORK]"
};
/*
 * logs an activity
 * @param {string} level
 * @param {function} caller
 * @param {object} object
 * @returns {null}
 */
function log(level, caller ,object){
//    if(ENV.debug){
//        console.log((new Date().toGMTString())+" "+level+" at "+ caller + " Error: ");
//        console.log(object);
//    }
}
function async(callback){
    window.setTimeout(callback(),0);
}


/*
 * xhr Request functions
 */



/*
 * Sends an async XML HTTP Request and calls callback function on request completion
 * @param {string} reqUri
 * @param {function} callback
 * @returns {null}
 */
function xhrGet(reqUri, callback) {
    log(ERROR.info, "[ajax request] fired for  "+reqUri, null);
    
    var xhr = new XMLHttpRequest();
    
    xhr.open("GET", reqUri, true);
    xhr.onload = callback;
    xhr.onerror = function(response){
       log(ERROR.net, xhrGet.caller ,response);
    };
    
    xhr.send();
}


/*
 * 
 *  Script: Drawing a level :)
 * 
 */
var Point = Class.extend({
   x:0,
   y:0,
   init: function(){}
});
var Pickable = Class.extend({
    source_pos: null,
    cur_pos: null,
    type: 0,
    size: 0,
    dimen: null,
    sprite: "",
    init: function(type, size, x,y, sprite){
        this.source_pos = new Point();
        this.cur_pos = new Point();
        this.dimen = new Point();
        this.source_pos.x = this.cur_pos.x = x;
        this.source_pos.y = this.cur_pos.y = y;
        this.sprite = sprite;
        this.size = size;
        switch(type){
            case 1: this.type = "h"; this.dimen.x = ENV.size * 2; this.dimen.y = ENV.size; break;
            case 2: this.type = "h"; this.dimen.x = ENV.size * 3; this.dimen.y = ENV.size; break;
            case 3: this.type = "v"; this.dimen.x = ENV.size; this.dimen.y = ENV.size *2; break;
            case 4: this.type = "v"; this.dimen.x = ENV.size; this.dimen.y = ENV.size *3; break;
        }
    },
    draw: function(){
        drawSpriteX(this.sprite, this.source_pos.x,this.source_pos.y, this.dimen.x, this.dimen.y);
    },
    move: function(){},
    reset: function(){},
    drop: function(event, parent){},
    pick: function(){}
});
var LevelMap = Class.extend({
    map: [
        [0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0],
        [-1, -1, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0]
    ],
    entity: [],
    name: "LEVEL NAME",
    id: 0,
    uid: 0
});

var Block = Class.extend({
    id: 0,
    size: 0,
    dir: null,
    position: [],
    __class: null,
    // Drawing data
    sprite: null,
    dimen: { x: 0, y: 0},
    init: function(id, size, dir, position, __class, sprite){
        this.id = id;
        this.size = size;
        this.dir = dir;
        this.position = position;
        this.__class = __class;
        this.sprite = sprite;
        if(dir === "h" || dir === "e"){
            this.dimen.x = ENV.size * size;
            this.dimen.y = ENV.size;
        } else {
            this.dimen.y = ENV.size * size;
            this.dimen.x = ENV.size;
        }
    },
    draw: function(){
        drawSpriteX(this.sprite, ENV.offset.x+this.position[0][0]*ENV.size, ENV.offset.y+this.position[0][1]*ENV.size, this.dimen.x, this.dimen.y);
    },
    drag: function(event){
        DT.clearHover();
        hoverDrawSpriteX(this.sprite, event.layerX - this.dimen.x/2, event.layerY - this.dimen.y/2, this.dimen.x, this.dimen.y);
    },
    pick: function(){
        
    }
});

var Level = Class.extend({
    id: 0,
    picked: null,
    map: new LevelMap(),
    team: "TEAM NAME",
    source: null,
    pending: [],
    id_counter: 0,
    init: function(){
        this.source = new Source();
        this.picked = null;
        this.map.entity.push(new Block(-1, 2, "h", [[0,2], [1,2]], "BlockExt", "block_ext_2.png"));
    },
    draw: function(){
        for(var i = 0; i < this.map.entity.length; i++ )
            this.map.entity[i].draw();
       this.source.draw();              
    },
    move: function(event){
        if(this.picked !== null){            
            //this.pending.push(event);
            this.picked.drag(event);
            //$('#mouseDump').html("from Level.move if lX:"+event.layerX+" lY:"+event.layerY+" "+this.picked.sprite);
        } //else
        //$('#mouseDump').html("from Level.move else lX:"+event.layerX+" lY:"+event.layerY+" NO picked "+this.picked);
               // var x = event.layerX - ENV.offset.x - this.picked.dimen.x/2, y = event.layerY - ENV.offset.y - this.picked.dimen.y/2;
                
                 //   x = Math.floor(x / ENV.size); y = Math.floor(y / ENV.size);
                   // $('#mouseDump').html("from Level.move else lX:"+event.layerX+" lY:"+event.layerY+" drop no:"+x+", "+y);
                
    },
    down: function(event){        
        var x = event.layerX - ENV.source_pos.x, y = event.layerY - ENV.source_pos.y;
        //$('#mouseDump').html("from Level.down x:"+x+" y:"+y+" lX:"+event.layerX+" lY:"+event.layerY);
        //$('#mouseDump').html("from Level.down x:"+x+" y:"+y+" lX:"+event.layerX+" lY:"+event.layerY);
        if(x<0|| y<0) {
            x = event.layerX - ENV.offset.x; y = event.layerY - ENV.offset.y;
            if(x>0 && y>0){
                x = Math.floor(x / ENV.size); y = Math.floor(y / ENV.size);
                //$('#mouseDump').html("MAP Level.down x:"+x+" y:"+y+" lX:"+event.layerX+" lY:"+event.layerY);
                if( x < 6 && y < 6){
                    var id = this.map.map[y][x];
                    var pck = null, found = false;
                    for( var i = 0; i < this.map.entity.length; i++){
                        if( found === true ){
                            this.map.entity[i-1] = this.map.entity[i];
                        }
                        if(id === this.map.entity[i].id){
                            pck = this.map.entity[i];
                            found = true;
                        }
                    }
                    if( found === true) this.map.entity.pop();
                    if(pck !== null){
                        for(var i = 0; i < pck.size; i++){
                            this.map.map[pck.position[i][1]][pck.position[i][0]] = 0;
                            //console.log("Clearing "+pck.position[i][0]+" "+pck.position[i][1])
//                            if(pck.id !== -1){
//                                pck.position[i][0] = -1;
//                                pck.position[i][1] = -1;
//                            }
                        }
                        this.picked = pck;
                    }
                }
            }
        }
        else{
            this.source.pick(x,y,++this.id_counter,this.picking, this); 
        }        
    },
    up: function(event){
        if(this.picked === null) return;
        var x = event.layerX - ENV.offset.x - this.picked.dimen.x/2, y = event.layerY - ENV.offset.y - this.picked.dimen.y/2;
        var restore = false, done = false;
        if(this.picked.id === -1) restore = true;
        if(x > 0 && y > 0){
            x = Math.floor(x / ENV.size); y = Math.floor(y / ENV.size);
            if(x < 6 && y < 6){
                var dropable = true;
                if(restore === true && y !== 2) dropable = false;
                if(this.picked.dir === "v"){
                    for(var i = 0; i < this.picked.size; i++){
                        if(this.map.map[y+i][x] !== 0) dropable = false;
                    }
                } else {
                        for(var i = 0; i < this.picked.size; i++){
                            if(this.map.map[y][x+i] !== 0) dropable = false;
                    }
                }
                if(dropable === true){
                    if(this.picked.dir === "v"){
                        for(var i = 0; i < this.picked.size; i++){
                            this.map.map[y+i][x] = this.picked.id;
                            this.picked.position[i][0] = x;
                            this.picked.position[i][1] = y+i;
                        }
                    } else {
                            for(var i = 0; i < this.picked.size; i++){
                                this.map.map[y][x+i] = this.picked.id;                            
                                this.picked.position[i][0] = x+i;
                                this.picked.position[i][1] = y;
                        }
                    }
                    this.map.entity.push(this.picked);
                } else if(restore === true && done === false) {
                    done = true;
                    restore = false;
                          this.map.map[this.picked.position[0][1]][this.picked.position[0][0]] = -1;                            
                          this.map.map[this.picked.position[1][1]][this.picked.position[1][0]] = -1;                    
                          this.map.entity.push(this.picked);
                }
            } else if(restore === true && done === false){
                done = true;
                restore = false;
                          this.map.map[this.picked.position[0][1]][this.picked.position[0][0]] = -1;                            
                          this.map.map[this.picked.position[1][1]][this.picked.position[1][0]] = -1;
                          this.map.entity.push(this.picked);
            }
        } else if(restore === true && done === false){
            done = true;
            restore = false;
                          this.map.map[this.picked.position[0][1]][this.picked.position[0][0]] = -1;                            
                          this.map.map[this.picked.position[1][1]][this.picked.position[1][0]] = -1;
                          this.map.entity.push(this.picked);
        }
        this.picked = null;
        DT.clearHover();
    },
    picking: function(picked, self){        
        self.picked = picked;
        //$('#mouseClick').html(" from picking Picked:"+self.picked.sprite);
    }
});

var Source = Class.extend({
   drag: [],
   init: function(){
       this.drag.push(new Pickable(1, 2,ENV.source_pos.x, ENV.source_pos.y+ENV.size, "block_hor_2.png"));       
       this.drag.push(new Pickable(2, 3,ENV.source_pos.x, ENV.source_pos.y, "block_hor_3.png"));
       this.drag.push(new Pickable(3, 2,ENV.source_pos.x, ENV.source_pos.y+ENV.size*2, "block_ver_2.png"));
       this.drag.push(new Pickable(4, 3,ENV.source_pos.x + 2*ENV.size, ENV.source_pos.y+ENV.size, "block_ver_3.png"));
   },
   draw: function(){
       for(var i = 0; i < this.drag.length; i++){
           this.drag[i].draw();
       }
   },
   pick: function(x,y,id,callback, parent){
       var xIndex = Math.floor(x / ENV.size), yIndex = Math.floor(y / ENV.size);
       //$('#mapEntity').html("From Source x:"+x+" y:"+y+" indX:"+xIndex+" indY:"+yIndex);
       if(yIndex === 0 && xIndex < 3)// 2 is picked
           callback(new Block(id, 3, "h", [[-1,-1], [-1,-1], [-1,-1]], "BlockHor", "block_hor_3.png"), parent);
       else if(yIndex === 1 && xIndex < 2)// 1 is picked
           callback(new Block(id, 2, "h", [[-1,-1], [-1,-1]], "BlockHor", "block_hor_2.png"), parent);
       else if(xIndex === 0 && yIndex > 1 && yIndex < 4 )
           callback(new Block(id, 2, "v", [[-1,-1], [-1,-1]], "BlockVer", "block_ver_2.png"), parent);
       else if(xIndex === 2 && yIndex > 0 && yIndex < 4 )
           callback(new Block(id, 3, "v", [[-1,-1], [-1,-1], [-1,-1]], "BlockVer", "block_ver_3.png"), parent);
   }
});

var renderLoopVariable, level = new Level();
function renderLoop(){
    renderLoopVariable = window.setInterval(function(){
                  DT.clear();
//                  DT.ctx.fillStyle = "#ffff00";
//                  DT.ctx.fillRect(0+ENV.offset.x,0+ENV.offset.y,6*ENV.size,6*ENV.size);
                  drawSpriteX('back.png', ENV.offset.x, ENV.offset.y+2, ENV.size*6, ENV.size*6);
                  level.draw();                  
                  //DT.ctx.clearRect(6*ENV.size+ENV.offset.x,2*ENV.size+ENV.offset.y,2*ENV.size,ENV.size);
                  //$('#map').html(level.map.map.toSource().replace(/],/g, "]<br />").replace(/,/g, " ").replace(/-1/g, "*").replace(/10/g, "x").replace(/\[\[/g, "[").replace(/\]\]/g, "]"));
                  //var html = "";
                  //for( var i =0; i < level.map.entity.length; i++)
                    //  html+=level.map.entity[i].id+" "+level.map.entity[i].position.toSource() + "<br />";
                  //$('#mapEntity').html(html);
    },1000/ENV.fps);
                  
}

function playLoop(){
    DT.hover.addEventListener('mousemove',function(event){
        level.move(event);
    });
    DT.hover.addEventListener('mousedown',function(event){
        level.down(event);
    });
    DT.hover.addEventListener('mouseup',function(event){
        level.up(event);
    });
    DT.hover.addEventListener('mouseout',function(event){
        level.up(event);
    });
}

function submit(){
    var map = level.map.map;
    var entities = {},
        temp = level.map.entity;
    for(var i = 0; i < temp.length; i++){
        entities[temp[i].id] = {
            'type': temp[i].dir,
            'length': temp[i].size,
            'class': temp[i].__class,
            'map': (temp[i].position)
        }
        if (temp[i].dir !== "h") {
            entities[temp[i].id]['row'] = temp[i].position[0][0];
        } else {
            entities[temp[i].id]['col'] = temp[i].position[0][1];
        }
    }
    var imageData = DT.ctx.getImageData(ENV.offset.x, ENV.offset.y+2, ENV.size*6, ENV.size*6);
    var cn = document.createElement('canvas');
    cn.width = ENV.size*6;
    cn.height = ENV.size*6;
    cn.getContext("2d").putImageData(imageData, 0, 0);
    var str = JSON.stringify({'level': map, 'entities': entities});
    $.ajax({
        method: 'post',
        data: {
            'data': str,
            'image': cn.toDataURL("image/png")
        },
        async: true,
        url: window.home_url + 'home/form/level'
    }).done(function(text){
            $('body').html(text);
        });
}
function swap(pos){
    for(var i = 0; i < pos.length; ++i){
        var tem = pos[i][0];
        pos[i][0] = pos[i][1];
        pos[i][1] = tem;
    }
    return tem;
}