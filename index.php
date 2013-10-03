<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Isometric Engine - Iain Hamilton</title>
<style type="text/css">
 body{
 	margin: 0;
 	padding: 0;
 }

</style>
<script type="text/javascript" src="com/canvas_input/canvas_input.js"></script>	
<script type="text/javascript" src="com/xml/XMLPopulate.js" ></script>
<script type="text/javascript" src="com/img/image_loader.js"></script>
<script tppe="text/javascript" src="com/iso/isometric.js"></script>
<script tppe="text/javascript" src="com/url/url.js"></script>
<script type="text/javascript">
var context;
// -- requestAnimFrame: gets the browsers optomized Framerate for drawing loops.
window.requestAnimFrame = (function(){
      return  window.requestAnimationFrame       || 
              window.webkitRequestAnimationFrame || 
              window.mozRequestAnimationFrame    || 
              window.oRequestAnimationFrame      ||  
              window.msRequestAnimationFrame     || 
              function(/* function */ callback, /* DOMElement */ element){
                window.setTimeout(callback, 1000 / 60);
       };
})();

function init(){
	canvas = document.createElement('canvas');
	canvas.width = 706;
	canvas.height = 425;
	canvas.style.border = "#333 2px solid";
	canvas.style.background = "url(img/canvas-background.jpg)"
	context = canvas.getContext( '2d' );
	document.body.appendChild( canvas );
	launch();
}


function launch(){
	//  -- XMLPopulate: takes in XML data and allows manipulation through getContent and getAttribute.
	var XML = new XMLPopulate();
	var image_root_path = 'img';
	var image_path = 'img';


// -- image_loader: returns an associated array from the ground images folder - file name is index.
// -- as tiles are represented by numbers we store the related graphic number in ground_dict.
	XML.loadXML('com/xml/XMLFiles.php?folder=' + image_root_path + '/ground/');
	var ground = new image_loader();
	var ground_dict = XML.getContent('files','file');
	var ground_images = ground.load_array(image_path + '/ground/', ground_dict);

// -- image_loader: returns an associated array from the city images folder - file name is index.
// -- as tiles are represented by numbers we store the related graphic number in city_dict.
	XML.loadXML('com/xml/XMLFiles.php?folder=' + image_root_path + '/city/');
	var city = new image_loader();
	var city_dict = XML.getContent('files','file');
	var city_images = city.load_array(image_path + '/city/', city_dict);
	

// -- image_loader: returns an associated array from the gui images folder - file name is index.
	XML.loadXML('com/xml/XMLFiles.php?folder=' + image_root_path + '/gui/');
	var gui = new image_loader();
	var gui_images = city.load_array(image_path + '/gui/', XML.getContent('files','file'));
	


// -- city-read.php: reads table data and parses output as XML.
	var user = new url();
	XML.loadXML('city-read.php');
	var ground_map = XML.getContent('ground_map','row');
	var city_height = XML.getContent('steps_made','row');
	var city_map = XML.getContent('steps_dates','row');
		
	

// -- loadTimer: required for polling if images are preloaded.
	var loadTimer = setInterval(loadAll,100);
	function loadAll(){
		if(ground.loaded == ground.to_load && city.loaded == city.to_load){
				clearInterval(loadTimer);
				var game = new main();
				game.init(ground_map,city_map,city_height,ground_images,ground_dict,city_images,city_dict,gui_images);
		}
	}
}


function rotateArray(setting,arrayIn){
		if(setting == "left"){
			var tempArray = [];
			var tempLine = [];
			for(i=arrayIn.length-1; i>=0; i--){
					for(j=arrayIn[i].length-1; j>= 0; j--){
						tempLine.push(arrayIn[i][j]);
					}
					tempArray.push(tempLine);
					tempLine = [];
			}
			return tempArray;
		}else{

		}
	}
	

function main(){
	self = this;
	var ground_level;
	var city_level;
	var city_details_steps;
	var city_details_dates;
	var gui = [];
	var tile_coordinates = {};
	var mouse_coordinates = {};
// -- canvas_input: contains commonly used input functions.	
	var input = new canvas_input(document);
	input.keyboard(function(pressed){
		switch(pressed){
			case 38:
				self.keyCommand(1);	
			break;
			case 39:
				self.keyCommand(2);	
			break;
			case 40:
				self.keyCommand(3);	
			break;
			case 37:
				self.keyCommand(4);	
			break;
			case 65:
				self.keyCommand(5);	
			break;
			case 83:
				self.keyCommand(6);	
			break;
			case 49:
				self.keyCommand(7);	
			break;
			case 50:
				self.keyCommand(8);	
			break;
			case 81:
				self.keyCommand(9);	
			break;
		}

	});
	
	
	this.keyCommand = function(dir){
		switch(dir){
			case 1:
				ground_level.draw_y += 20;	
				city_level.draw_y += 20;
			break;
			case 2:
				ground_level.draw_x -= 20;	
				city_level.draw_x -= 20;
			break;
			case 3:
				ground_level.draw_y -= 20;	
				city_level.draw_y -= 20;
			break;
			case 4:
				ground_level.draw_x += 20;	
				city_level.draw_x += 20;
			break;
			case 5:
				ground_level.setZoom("out");
				city_level.setZoom("out");
				ground_level.align("h-center",706);
				city_level.align("h-center",706);
				ground_level.align("v-center",425);
				city_level.align("v-center",425);
			break;
			case 6:
				ground_level.setZoom("in");
				city_level.setZoom("in");
				ground_level.align("h-center",706);
				city_level.align("h-center",706);
				ground_level.align("v-center",425);
				city_level.align("v-center",425);
			break;
			case 7:
				city_level.hideGraphics(true,0,6,"7-normal.png");
				city_level.applyObjectShadow(true);
			break;
			case 8:
				city_level.hideGraphics(false);
				city_level.applyObjectShadow(false);
			break;
			case 9:
				city_level.rotate("left");
				ground_level.rotate("left");
				city_details_steps = rotateArray("left",city_details_steps);
				city_details_dates = rotateArray("left",city_details_dates);
			break;
		}
		// -- loop to browser suggested rate of redraw.
		requestAnimFrame(self.draw);
	}
	

	
	this.draw = function(){
		context.clearRect(0,0,706,425);
		ground_level.draw();
		city_level.draw();
		context.fillStyle = "rgb(255,255,255)";
		context.save(); 
		if(tile_coordinates.x < city_details_steps.length && tile_coordinates.x >= 0 && tile_coordinates.y < city_details_steps.length && tile_coordinates.y >= 0){
			if(city_details_dates[tile_coordinates.x][tile_coordinates.y] != 0){
				if(city_details_dates[tile_coordinates.x][tile_coordinates.y]){
					context.drawImage(gui["popup-box.png"],mouse_coordinates.x,mouse_coordinates.y);
					context.font = "8pt Arial";
					context.fillText("Hover box",mouse_coordinates.x+14,mouse_coordinates.y+25);
					if(String(city_details_dates[tile_coordinates.x][tile_coordinates.y]).length == 10){
						context.fillText('Example',mouse_coordinates.x+14,mouse_coordinates.y+45);
					}else{
						context.fillText("Example",mouse_coordinates.x+14,mouse_coordinates.y+45);
					}
				}
			}
		}
		context.restore();
	}
	
	this.init = function(ground_map_layout,city_map_layout,city_height_layout,ground_graphics,ground_dict,city_graphics,city_dict,gui_graphics){
		// -- isometric: contains the functions to output an isometric map.
		// -- takes both the associated index array and the numbered index array in order to look up
		// -- tile number and return corresponding image.
		ground_level = new isometric(context,50,25,ground_map_layout, ground_graphics,ground_dict);
		ground_level.setZoom(1);
		
		city_level = new isometric(context,50,25,city_map_layout,city_graphics,city_dict);
		city_level.setZoom(1);
		city_level.zero_is_blank = 1;
		city_level.alpha_mouse_behind = 1;
		// -- stack_graphics: sets iso map stack_numbers = 1 and passes height map to use, divider makes heights more acceptable.
		city_level.stack_tiles(city_height_layout,1);
		city_level.hideGraphics(true,0,6,"7-normal.png");
		city_level.applyObjectShadow(true);
		
		
		
		city_details_steps = city_height_layout;
		city_details_dates = city_map_layout;
		input.mouse_move(function(coords){
				tile_coordinates = city_level.applyMouse(coords.x,coords.y);
				mouse_coordinates = coords;
				// -- loop to browser suggested rate of redraw.
				requestAnimFrame(self.draw);
		});	
		input.mobile(function(coords){
				tile_coordinates = city_level.applyMouse(coords.x,coords.y);
				mouse_coordinates = coords;
				// -- loop to browser suggested rate of redraw.
				requestAnimFrame(self.draw);
		});		
		ground_level.align("h-center",706);
		city_level.align("h-center",706);
		ground_level.align("v-center",425);
		city_level.align("v-center",425);
		gui = gui_graphics;
		this.draw();
		
	}
	
	
}
</script>
</head>
<body onLoad="init()">	
<p class="instructions">A &amp; S: Zoom In/Out - Arrow Keys: Move Around - 1 &amp; 2: Switch On/Off detail mode - Q: Reverse Map</p> <br />
</body>
</html>