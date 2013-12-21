/*
 * 
 * Environment variables
 */

ENV = {
    debug: true,
    size: 72,
    offset: {
        x: 400,
        y: 100
    },
    speed: 2000,
    fps: 480
};

/*
 * Object orientation hacks
 */


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

var Canvas = Class.extend({
   canvas: null,
   ctx: null,
   init: function(){}, 
   set: function(c){
       this.canvas = c;
       this.ctx = this.canvas.getContext('2d');
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
 * Level map functions
 */
var Move = Class.extend({
   id: 0,
   _do: 0,
   init: function(ID,_DO){
       this.id = parseInt(ID);
       this._do = _DO;
   }
});
var LevelDictionary = {}, LevelsLoaded = 0;
var Level = Class.extend({
    parsedData: null,
    entity: [],
    completed: false,
    map: {},
    loaded: false,
    id: null,
    time: null,
    name: null,
    playing: false,
    auto: [],
    pushAuto: function(id, _do){
        //console.log(id+" ");
      for(var i =0; i < this.entity.length; i++)
          if(this.entity[i].id == id){
              
              this.auto.push(new Move(i, _do) );
              break;
          }
    },
    automoving: false,
    mouseDownAt: {
        x: 0,
        y: 0
    },
    mouseDown: false,
    activeEntity: 0,
    mouse:{
      x:0,
      y:0
    },
    init: function(uri){
        var parent = this;
        xhrGet(uri, function(response){
           log(ERROR.core, "who is this" ,this);
           parent.load(response.target.responseText); 
        });
    },
    createEntity: function(id,entity){
        //this.entity.push();
        if(entity.type === "h"){
          log( ERROR.info, "JSON parse in createEntity h"+id, entity);
          this.entity.push(new BlockHor(id, entity.length, entity.map));
        } else if(entity.type === "v"){
          log( ERROR.info, "JSON parse in createEntity v"+id, entity);
          this.entity.push(new BlockVer(id, entity.length, entity.map));
        } else if(entity.type === "e"){
          log( ERROR.info, "JSON parse in createEntity v"+id, entity);
          this.entity.push(new BlockExt(id, entity.length, entity.map));
        } else
          log( ERROR.info, "JSON parse in createEntity h"+id, null);
    },
    load: function(levelJSON){        
        var ldata;           
                
                ldata = JSON.parse(levelJSON);
                this.parsedData = ldata;
                this.map = ldata.level;
                this.id = ldata.id;
                this.time = ldata.time;
                this.name = ldata.name;
                for(var index in ldata.entities){                        
                        var entity = ldata.entities[index];                          
                        this.createEntity(index,entity);
                    
                }
                LevelDictionary[this.id] = this;
    },
    draw: function(){
        for(var i = 0; i < this.entity.length; i++ )
            this.entity[i].draw();
    },
    move: function(event){
        if(this.mouseDown === true && !this.completed){
            try{
                    var x = event.layerX - ENV.offset.x;
                    var y = event.layerY - ENV.offset.y;
                    if(x>0 && y>0)
                    this.entity[this.activeEntity].move(x, y, this);
            }catch(e){
               // log(ERROR.info, "Method not found", this);
               // log(ERROR.info, "Cont: Method not found", e);
            }
        }
    },
    down: function(event){
        if(this.mouseDown === true)
               return;
        this.mouseDown = true;
        var x, y;
        this.mouseDownAt.x = x = event.layerX - ENV.offset.x;
        this.mouseDownAt.y = y = event.layerY - ENV.offset.y;
        try{
            this.activeEntity = this.map[Math.floor(y/ENV.size)][Math.floor(x/ENV.size)];
        } catch(e){}
        for( var z = 0; z < this.entity.length; z++ ){
            if(this.entity[z].id == this.activeEntity){
                this.activeEntity = z;
                break;
            }
        }
        
        //$('#mouseClick').html("down"+"<br />");
    },
    up: function(event){
        try{
            this.entity[this.activeEntity].up();
        } catch(e) { }
        this.activeEntity = -1;
        this.mouseDown = false;
        this.mouseDownAt.x = 0;
        this.mouseDownAt.y = 0;
        //$('#mouseClick').html("up");
    },
    doAuto: function(){
        var i = 0, lvl = this,delay = 500;
        var interval = window.setInterval(function(){
            lvl.entity[lvl.auto[i].id].doMove(lvl.auto[i]._do, lvl);
            if(i < (lvl.auto.length-1) ){                
                i++;
            }
            else window.clearInterval(interval);
        }, delay);
    }
});


var Point2D = Class.extend({
    x: 0,
    y: 0,
    init: function(x, y){
        this.x = x;
        this.y = y;
    }
});

var Dimen = Class.extend({
   w: 0,
   h:0,
   init: function(w, h){
       this.w = w;
       this.h = h;
   }
});

var Block = Class.extend({
    id: null,
    start: null, //Point2D
    cur: null,
    end: null, //Point2D
    _dead: false,
    loc: null,
    map: null,
    init: function(){},
    update: function(){}
});

var BlockVer = Block.extend({
    length: 0,
    spriteName: null,
    dimen: null,    
    init: function(id,size,loc){
        this.map = loc;
        this.id = id;
        this.length = size;
        log( ERROR.info, "JSON parse "+id, loc);
        this.dimen = new Dimen(ENV.size, ENV.size * size);
        this.start = new Point2D(loc[0][1] * ENV.size, loc[0][0] * ENV.size);
        this.cur = new Point2D(loc[0][1] * ENV.size, loc[0][0] * ENV.size);
        this.loc = new Point2D(loc[0][1] * ENV.size, loc[0][0] * ENV.size);
        this.end = new Point2D((loc[size -1][1]+1) * ENV.size, (loc[size -1][0]+1) * ENV.size);
        this.spriteName = "block_ver_"+ size +".png";
    },
    update: function(){
        // TODO: override parent function and call it aferwards
    },
    move: function(x,y,parent){
         var disp = y - parent.mouseDownAt.y;
         var blocks_to_move = Math.floor((Math.abs(disp) * 2) / ENV.size);
         if(disp < 0) blocks_to_move *= -1;
         var movable = false;
         var indexX = -1, indexY = -1;
         if(disp >= 0){
             indexY = this.map[this.length -1][0] +1, indexX = this.map[this.length -1][1];
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         }
         if(disp <= 0){
             indexY = this.map[0][0] -1; indexX = this.map[0][1];
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         }
         if(movable === true && Math.abs(disp) < ENV.size){
             this.start.y = this.loc.y + disp; 
         } //else this.start.y = this.loc.y; 
         //$('#mouseDump').html(this.length+" "+parent.map[indexY][indexX]+" "+movable+" "+indexY+" "+indexX+" '"+blocks_to_move+" vertical"+this.start.y+" "+this.cur.y);
         if(blocks_to_move === 1 && movable === true){
             //log(ERROR.info, "LOdalskdfalksgjalksfjas "+indexY+" "+indexX, null);
             parent.mouseDownAt.y += ENV.size;
             this.loc.y +=ENV.size;
             //this.start.y = this.loc.y;
             parent.pushAuto(this.id, 1);
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[0][0])][(this.map[0][1])] = 0;
             for(var i = 0; i < this.length -1; i++){
                 this.map[i][0]=this.map[i+1][0];
                 this.map[i][1]=this.map[i+1][1];
             }
             this.map[this.length-1][0] = indexY;
             this.map[this.length-1][1] = indexX;             
         }else if(blocks_to_move === -1 && movable === true){
             parent.mouseDownAt.y -= ENV.size;
             this.loc.y -=ENV.size;
             //this.start.y = this.loc.y;
             parent.pushAuto(this.id, 1);
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[this.length - 1][0])][(this.map[this.length-1][1])] = 0;
             
             for(var i = this.length-1; i > 0; i--){
                 this.map[i][0]=this.map[i-1][0];
                 this.map[i][1]=this.map[i-1][1];
             }
             this.map[0][0] = indexY;
             this.map[0][1] = indexX;
         }
    },
    up: function(){
        this.start.y = this.loc.y;
    },
    draw: function(){
        var dy = 0.001 * ENV.speed * sgnm(this.start.y - this.cur.y);
        drawSpriteX(this.spriteName, ENV.offset.x+this.start.x, (this.cur.y += dy)+ENV.offset.y, this.dimen.w, this.dimen.h);
    },
    doMove: function(blocks_to_move, parent){
        var movable = false;
         var indexX = -1, indexY = -1;
         if(blocks_to_move === 1){
             indexY = this.map[this.length -1][0] +1, indexX = this.map[this.length -1][1];
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         }
         if(blocks_to_move === -1){
             indexY = this.map[0][0] -1; indexX = this.map[0][1];
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         }
        if(blocks_to_move === 1 && movable === true){
             //log(ERROR.info, "LOdalskdfalksgjalksfjas "+indexY+" "+indexX, null);
            
             this.start.y = this.loc.y +=ENV.size;
             //this.start.y = this.loc.y;
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[0][0])][(this.map[0][1])] = 0;
             for(var i = 0; i < this.length -1; i++){
                 this.map[i][0]=this.map[i+1][0];
                 this.map[i][1]=this.map[i+1][1];
             }
             this.map[this.length-1][0] = indexY;
             this.map[this.length-1][1] = indexX;             
         }else if(blocks_to_move === -1 && movable === true){
             
             this.start.y = this.loc.y -=ENV.size;
             //this.start.y = this.loc.y;
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[this.length - 1][0])][(this.map[this.length-1][1])] = 0;
             
             for(var i = this.length-1; i > 0; i--){
                 this.map[i][0]=this.map[i-1][0];
                 this.map[i][1]=this.map[i-1][1];
             }
             this.map[0][0] = indexY;
             this.map[0][1] = indexX;
         }
    }
});

var BlockHor = Block.extend({
    length: 0,
    spriteName: null,
    dimen: null,
    init: function(id, size,loc){
        this.map = loc;
        this.id = id;
        this.length = size;
        this.dimen = new Dimen(ENV.size * size, ENV.size);
        this.start = new Point2D(loc[0][1] * ENV.size, loc[0][0] * ENV.size);
        this.cur = new Point2D(loc[0][1] * ENV.size, loc[0][0] * ENV.size);
        this.loc = new Point2D(loc[0][1] * ENV.size, loc[0][0] * ENV.size);
        this.end = new Point2D( (loc[size-1][1]+1) * ENV.size, (loc[size -1][0]+1) * ENV.size);
        this.spriteName = "block_hor_"+ size +".png";
    },
    update: function(){
        // TODO: override parent function and call it aferwards
    },
    move: function(x,y,parent){
         var disp = x - parent.mouseDownAt.x;
         var blocks_to_move = Math.floor((Math.abs(disp) * 2) / ENV.size);
         if(disp < 0) blocks_to_move *= -1;
         var movable = false;
         var indexX = -1, indexY = -1;
         if(disp >= 0){
             indexY = this.map[this.length -1][0], indexX = this.map[this.length -1][1]+1;
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         } else if(disp < 0){
             indexY = this.map[0][0]; indexX = this.map[0][1] -1;
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         }
         if(movable === true && Math.abs(disp) < ENV.size){
             this.start.x = this.loc.x + disp; 
         } //else this.start.y = this.loc.y; 
         //$('#mouseDump').html(parent.map[indexY][indexX]+" "+movable+" "+indexY+" "+indexX+" '"+blocks_to_move);
         if(blocks_to_move === 1 && movable === true){
             //log(ERROR.info, "LOdalskdfalksgjalksfjas "+indexY+" "+indexX, null);
             parent.pushAuto(this.id, 1);
             parent.mouseDownAt.x += ENV.size;
             this.loc.x +=ENV.size;
             //this.start.y = this.loc.y;
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[0][0])][(this.map[0][1])] = 0;
             for(var i = 0; i < this.length -1; i++){
                 this.map[i][0]=this.map[i+1][0];
                 this.map[i][1]=this.map[i+1][1];
             }
             this.map[this.length-1][0] = indexY;
             this.map[this.length-1][1] = indexX;             
         }else if(blocks_to_move === -1 && movable === true){
             parent.mouseDownAt.x -= ENV.size;
             parent.pushAuto(this.id, 1);
             this.loc.x -=ENV.size;
             //this.start.y = this.loc.y;
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[this.length - 1][0])][(this.map[this.length-1][1])] = 0;
             for(var i = this.length-1; i > 0; i--){
                 this.map[i][0]=this.map[i-1][0];
                 this.map[i][1]=this.map[i-1][1];
             }
             this.map[0][0] = indexY;
             this.map[0][1] = indexX;
         }
    },
    up: function(){
        this.start.x = this.loc.x;
    },
    draw: function(){
        var dx = 0.001 * ENV.speed * sgnm(this.start.x - this.cur.x);
        drawSpriteX(this.spriteName, (this.cur.x += dx)+ENV.offset.x, ENV.offset.y+this.start.y, this.dimen.w, this.dimen.h);
    },
    doMove: function(blocks_to_move, parent){
        var movable = false;
         var indexX = -1, indexY = -1;
         if(blocks_to_move === 1){
             indexY = this.map[this.length -1][0], indexX = this.map[this.length -1][1]+1;
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         } else if(blocks_to_move === -1){
             indexY = this.map[0][0]; indexX = this.map[0][1] -1;
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         }
         if(blocks_to_move === 1 && movable === true){
             //log(ERROR.info, "LOdalskdfalksgjalksfjas "+indexY+" "+indexX, null);
             
             this.start.x = this.loc.x +=ENV.size;
             //this.start.y = this.loc.y;
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[0][0])][(this.map[0][1])] = 0;
             for(var i = 0; i < this.length -1; i++){
                 this.map[i][0]=this.map[i+1][0];
                 this.map[i][1]=this.map[i+1][1];
             }
             this.map[this.length-1][0] = indexY;
             this.map[this.length-1][1] = indexX;             
         }else if(blocks_to_move === -1 && movable === true){
             
             this.start.x = this.loc.x -=ENV.size;
             //this.start.y = this.loc.y;
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[this.length - 1][0])][(this.map[this.length-1][1])] = 0;
             for(var i = this.length-1; i > 0; i--){
                 this.map[i][0]=this.map[i-1][0];
                 this.map[i][1]=this.map[i-1][1];
             }
             this.map[0][0] = indexY;
             this.map[0][1] = indexX;
         }
    }
});

var BlockExt = Block.extend({
    length: 0,
    spriteName: null,
    dimen: null,
    ext: null,
    init: function(id, size,loc){
        this.map = loc;
        this.id = id;
        this.length = size;
        this.ext = [2,5];
        this.dimen = new Dimen(ENV.size * size, ENV.size);
        this.start = new Point2D(loc[0][1] * ENV.size, loc[0][0] * ENV.size);
        this.cur = new Point2D(loc[0][1] * ENV.size, loc[0][0] * ENV.size);
        this.loc = new Point2D(loc[0][1] * ENV.size, loc[0][0] * ENV.size);
        this.end = new Point2D( (loc[size-1][1]+1) * ENV.size, (loc[size -1][0]+1) * ENV.size);
        this.spriteName = "block_ext_2.png";
    },
    update: function(){
        // TODO: override parent function and call it aferwards
    },
    move: function(x,y,parent){
         var disp = x - parent.mouseDownAt.x;
         var blocks_to_move = Math.floor((Math.abs(disp) * 2) / ENV.size);
         if(disp < 0) blocks_to_move *= -1;
         var movable = false;
         var indexX = -1, indexY = -1;
         if(disp >= 0){
             indexY = this.map[this.length -1][0], indexX = this.map[this.length -1][1]+1;
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         }else if(disp < 0){
             indexY = this.map[0][0]; indexX = this.map[0][1] -1;
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         }
         if(movable === true && Math.abs(disp) < ENV.size){
             this.start.x = this.loc.x + disp; 
         } //else this.start.y = this.loc.y; 
         //$('#mouseDump').html(parent.map[indexY][indexX]+" "+movable+" "+indexY+" "+indexX+" '"+blocks_to_move);
         if(blocks_to_move === 1 && movable === true){
             if(this.ext[0] === indexY && this.ext[1] === indexX){
                 this.loc.x += 2*ENV.size; 
                         parent.complete = true;
                     
             }
             //log(ERROR.info, "LOdalskdfalksgjalksfjas "+indexY+" "+indexX, null);
             parent.mouseDownAt.x += ENV.size;
             this.loc.x +=ENV.size;
             //this.start.y = this.loc.y;
             parent.pushAuto(this.id, 1);
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[0][0])][(this.map[0][1])] = 0;
             for(var i = 0; i < this.length -1; i++){
                 this.map[i][0]=this.map[i+1][0];
                 this.map[i][1]=this.map[i+1][1];
             }
             this.map[this.length-1][0] = indexY;
             this.map[this.length-1][1] = indexX;            
         }else if(blocks_to_move === -1 && movable === true){
             parent.mouseDownAt.x -= ENV.size;
             this.loc.x -=ENV.size;
             //this.start.y = this.loc.y;
             parent.pushAuto(this.id, 1);
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[this.length - 1][0])][(this.map[this.length-1][1])] = 0;
             for(var i = this.length-1; i > 0; i--){
                 this.map[i][0]=this.map[i-1][0];
                 this.map[i][1]=this.map[i-1][1];
             }
             this.map[0][0] = indexY;
             this.map[0][1] = indexX;
         }
    },
    up: function(){
        this.start.x = this.loc.x;
    },
    draw: function(){
        var dx = 0.001 * ENV.speed * sgnm(this.start.x - this.cur.x);
        drawSpriteX(this.spriteName, (this.cur.x += dx)+ENV.offset.x, ENV.offset.y+this.start.y, this.dimen.w, this.dimen.h);
    },
    doMove: function(blocks_to_move, parent){
        var movable = false;
         var indexX = -1, indexY = -1;
         if(blocks_to_move === 1){
             indexY = this.map[this.length -1][0], indexX = this.map[this.length -1][1]+1;
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         } else if(blocks_to_move === -1){
             indexY = this.map[0][0]; indexX = this.map[0][1] -1;
             try{
                 if(parent.map[indexY][indexX]===0)
                     movable = true;
             } catch(e){
                // log(ERROR.info, "Index out of bound", e);
             }
         }
         if(blocks_to_move === 1 && movable === true){
             if(this.ext[0] === indexY && this.ext[1] === indexX){
                 this.loc.x += 2*ENV.size;
                 parent.complete = true;
                 //parent.up(null);
             }
             //log(ERROR.info, "LOdalskdfalksgjalksfjas "+indexY+" "+indexX, null);
             
             this.start.x = this.loc.x +=ENV.size;
             //this.start.y = this.loc.y;
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[0][0])][(this.map[0][1])] = 0;
             for(var i = 0; i < this.length -1; i++){
                 this.map[i][0]=this.map[i+1][0];
                 this.map[i][1]=this.map[i+1][1];
             }
             this.map[this.length-1][0] = indexY;
             this.map[this.length-1][1] = indexX;             
         }else if(blocks_to_move === -1 && movable === true){
             
             this.start.x = this.loc.x -=ENV.size;
             //this.start.y = this.loc.y;
             parent.map[indexY][indexX] = parseInt(this.id);
             parent.map[(this.map[this.length - 1][0])][(this.map[this.length-1][1])] = 0;
             for(var i = this.length-1; i > 0; i--){
                 this.map[i][0]=this.map[i-1][0];
                 this.map[i][1]=this.map[i-1][1];
             }
             this.map[0][0] = indexY;
             this.map[0][1] = indexX;
         }
    }
});

window.once = true;
var renderLoopVariable;
window.loadedLevel = "1";
function renderLoop(){
    renderLoopVariable = window.setInterval(function(){
                    //var html = "";
                  //for( var i =0; i < LevelDictionary["1"].entity.length; i++)
                      //html+=LevelDictionary["1"].entity[i].id+" "+LevelDictionary["1"].entity[i].map.toSource() + "<br />";
                  //$('#mapEntity').html(html);
                  DT.ctx.clearRect(0,0,DT.canvas.width,DT.canvas.height);
                  //DT.ctx.fillStyle = "#aaaaaa";
                  //DT.ctx.fillRect(-2+ENV.offset.x,-2+ENV.offset.y,6*ENV.size+4,6*ENV.size+4);
                  drawSpriteX('back-play.png', ENV.offset.x -2, ENV.offset.y-2, ENV.size*6+4, ENV.size*6+4);
                  LevelDictionary[window.loadedLevel].draw();
                  DT.ctx.clearRect(6*ENV.size+ENV.offset.x+2,2*ENV.size+ENV.offset.y,3*ENV.size,ENV.size);
                  //$('#map').html(LevelDictionary["1"].map.toSource().replace(/],/g, "]<br />").replace(/,/g, " ").replace(/-1/g, "*").replace(/10/g, "x").replace(/\[\[/g, "[").replace(/\]\]/g, "]"));
                  if(LevelDictionary[window.loadedLevel].complete === true&& once){
                      // draw level complete animation
                      // LevelDictionary["1"].draw(); 
                      once = false;
                      showAlert('Prototype X', 'Level finished!!!<br> Submit this level?', function(){
                          $('#draft_'+LevelDictionary[loadedLevel]['id']).prop('checked', true);
                          $('#draft').submit();
                      }, 'ok', 'cancel', 1000);
                  }
    },1000/ENV.fps);
                  
}

function playLoop(){
    DT.canvas.addEventListener('mousemove',function(event){
        LevelDictionary[window.loadedLevel].move(event);
    });
    DT.canvas.addEventListener('mousedown',function(event){
        LevelDictionary[window.loadedLevel].down(event);
    });
    DT.canvas.addEventListener('mouseup',function(event){
        LevelDictionary[window.loadedLevel].up(event);
    });
    DT.canvas.addEventListener('mouseout',function(event){
        LevelDictionary[window.loadedLevel].up(event);
    });
}