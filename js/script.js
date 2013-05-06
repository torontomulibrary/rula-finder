/*This file belongs to the RULA BookFinder Application.
*
*Bookfinder is free software: you can redistribute it and/or modify
*it under the terms of the GNU General Public License as published by
*the Free Software Foundation, either version 3 of the License, or
*(at your option) any later version.
*
*Bookfinder is distributed in the hope that it will be useful,
*but WITHOUT ANY WARRANTY; without even the implied warranty of
*MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*GNU General Public License for more details.
*
*You should have received a copy of the GNU General Public License
*along with RULA Bookfinder.  If not, see <http://www.gnu.org/licenses/>.
*
*/


var jsonData;
var dTime = new Date().getTime();
var alsoOnline = '';

$(document).ready(function() {
	// If there is a search string in the URL load it into the search box.
	var url = document.location.href;
	var search_string = url.lastIndexOf('s=');
	if(search_string > 0){
		search_string = url.substring(search_string+2);
		search_string = search_string.split('&');
		lookup();
	}
	
	$(window).bind('hashchange', function() {
		var url = document.location.href;
		var search_string = url.lastIndexOf('s=');
		if(search_string > 0){
			search_string = url.substring(search_string+2);
			search_string = search_string.split('&');
			
			//$('#s').val( unescape(search_string) );
			lookup();
			
		}
	});
	
	
	$(".ytlinks a").fancybox({
		maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'elastic',
		closeEffect	: 'none'
	});
	
});

var tipObj = {
	map_y: null,
	map_x: null,
	width: null,
	height: null
}

function lookup(){
	
	$('#s').attr('disabled', 'disabled');
	
	var url = document.location.hash;
	var search_string = url.lastIndexOf('s=');
	if(search_string > 0){
		search_string = url.substring(search_string+2);
		search_string = search_string.split('&');
	}
	
	var search = search_string;
	
	getResponse( 'call_no/'+search, function(response){
		
		var index = 0;
		jsonData = response; //Save data to global var
		
		//Make sure 
		if(typeof response.data != 'undefined'){
			//If there is more then 1 result, load the first one which has location
			if(response.data.length > 1){
				for(i=0; i < response.data.length; i++){
					if(response.data[i].hasLocation == true){
						index = i;
						break;
					}
				}
			}
			var responseJSON = response.data[index];
		}
		
		loadData(responseJSON);
	});
}
		
		
function loadData(responseJSON){
	
	$('#status-3').hide();
	$('#status-2').hide();
	$('#status-empty').hide();
	$('#stack1').hide();
	$('#floors .selected').removeClass('selected');

	if(typeof responseJSON != 'undefined'){
		if(typeof responseJSON.title == 'undefined' ){
			responseJSON.title = '';
		}
		if(typeof responseJSON.author == 'undefined' ){
			responseJSON.author = '';
		}
		if(typeof responseJSON.location == "undefined"){
			responseJSON.location = '';
		}
		if(typeof responseJSON.cover_url == "undefined"){
			responseJSON.cover_url = '';
		}
		if(typeof responseJSON.isbn == "undefined"){
			responseJSON.isbn = '';
		}
		
		if(typeof responseJSON.bibno == "undefined"){
			responseJSON.bibno = '';
		}
		
		if(responseJSON.isOnline){
			alsoOnline = ' (This resource is also available online)';
		}
		
		//Modify some of the data before it is shown
		if(responseJSON.title.length > 40){
			responseJSON.title = (responseJSON.title.substring(0,40) + "...");
		}
	
	}
	
	if(jsonData.status == "1"){
	//status 1 (book found & data)
		$('#stack1 .pop-img'). attr('src', responseJSON.cover_url);
		$("<img/>") // Make in memory copy of image to avoid css issues
			.load(function() {
				if(this.width > 2){ //1px x 1px is returned if cover is not found, so make sure its bigger then this
					$('#stack1 .pop-img').show();
				} else{
					$('#stack1 .pop-img').hide();
				}
			}).attr("src", responseJSON.cover_url+'&real='+dTime); //The 'real' variable prevents caching (needed?)
		
		$('#stack1 .pop-title a').attr('href', 'http://catalogue.library.ryerson.ca/record='+responseJSON.bibno+'&SUBMIT=Search').text(responseJSON.title);
		$('#stack1 .pop-desc').text(responseJSON.author);
		$('#stack1 .pop-other').text(responseJSON.availability+alsoOnline);
		$('#stack1 .pop-callno').text('Call #: '+responseJSON.callNumber);
		
		
		if(responseJSON.availability.toUpperCase() == "AVAILABLE"){
			$('#stack1 .pop-other').removeClass('not_avail').addClass('avail');
		}
		else{
			$('#stack1 .pop-other').removeClass('avail').addClass('not_avail');
		}
		
		
		if(responseJSON.loc_name.length > 0)
			$('#stack1 .pop-name').text("Shelf " + responseJSON.loc_name);
		else
			$('#stack1 .pop-name').text("");
		
		tipObj.map_y = responseJSON.map_y;
		tipObj.map_x = responseJSON.map_x;
		tipObj.width = responseJSON.width;
		tipObj.height = responseJSON.height;
		rePosition();
		$('#stack1').show().css('display', 'block');
		
		//Load the floor map
		$('#floor_img').show().attr('src', '').attr('src', responseJSON.img_url+'?=t'+dTime).load(function(){
			rePosition();
		});
		
		
		$('#floors .selected').removeClass('selected'); //Remove the selected floor
		$('#floors .alternate').removeClass('alternate'); //Remove the selected floor
		 

		//Select the current floor item is located on
		$('#floors li[data-fid="'+responseJSON.floor_id+'"]').addClass('selected');
		
		//Highlight other floors item may be located on
		$('#floors li').not('.selected').each(function() {
			for(i=0; i < jsonData.data.length; i++){
				if(jsonData.data[i].floor_id == $(this).attr('data-fid')){
					$(this).addClass('alternate');
					
				}
			}
		});
		
	} else if(jsonData.status == "2"){
	//status 2 (found & no location)
	// a). Book that doesn't have a location and: b). An Ebook
		$('#floor_img').hide();
		$('#stack1').hide(); //Make sure previous results stay hidden
		
		if(responseJSON.availability == "ONLINE"){
			$('#status-2').html(
				'<h2>This is an Online Resource</h2>'+
				'<div class="ipad-pop">'+
					'<span>'+
						'<b></b>'+
						'<img class="pop-img" src="'+responseJSON.cover_url+'" alt="" width="66" height="100" />'+
						'<p class="pop-callno">Call #: '+responseJSON.callNumber+'</p>'+
						'<p class="pop-other secondary">'+responseJSON.availability+alsoOnline+'</p>'+
						'<p class="pop-title"><a href="http://catalogue.library.ryerson.ca/record='+responseJSON.bibno+'&SUBMIT=Search">'+responseJSON.title+'</a></p>'+
						'<p class="pop-desc">'+responseJSON.author+'</p>'+
					'</span>'+
					'<div style="clear:both"></div>'+
				'</div>'

	
				
			);
		} else{
			$('#stack1').hide(); //Make sure previous results stay hidden
			$('#status-2').html(
				'<h2>'+responseJSON.location+'<br />This location is not yet mapped!</h2>'+
				//'<p style="margin-bottom:10px;">We have not mapped this resource yet.</p>'+
				'<p style="margin-bottom:10px;"><a href="mailto:steven.marsden@ryerson.ca">Report</a> this page if you beleive it was an error</p>'+
				
				'<div class="ipad-pop">'+
					'<span>'+
						'<b></b>'+
						'<img class="pop-img" src="'+responseJSON.cover_url+'" alt="" width="66" height="100" />'+
						'<p class="pop-callno">Call #: '+responseJSON.callNumber+'</p>'+
						'<p class="pop-other secondary">'+responseJSON.availability+alsoOnline+'</p>'+
						'<p class="pop-title"><a href="http://catalogue.library.ryerson.ca/record='+responseJSON.bibno+'&SUBMIT=Search">'+responseJSON.title+'</a></p>'+
						'<p class="pop-desc">'+responseJSON.author+'</p>'+
						
					'</span>'+
					'<div style="clear:both"></div>'+
				'</div>'
			);
			

			
			
			$('#floors .selected').removeClass('selected');
			$('#floors li').each(function(index, value){
				if( responseJSON.location.indexOf($(this).text().substr(0,8)) > -1 ){
					
					$(this).addClass('selected');
				}
			});
			
		if(responseJSON.availability.toUpperCase() == "AVAILABLE"){
			$('#status-2 .pop-other').removeClass('not_avail').addClass('avail');
		}
		else{
			$('#status-2 .pop-other').removeClass('avail').addClass('not_avail');
		}
		
		
		}
		$("<img/>") // Make in memory copy of image to avoid css issues
			.load(function() {
			if(this.width > 2){
					$('.pop-img').show();
				} else{
					$('.pop-img').hide();
				}
			}).attr("src", responseJSON.cover_url+'&real='+dTime);
		$('#status-2').show();
	} else if(jsonData.status == "3"){
	//status 3 (not found)
		$('#floor_img').hide();
		$('#status-3').show();
	} else{
		$('#floor_img').hide();
		$('#status-empty').show();
	}
	$('#s').removeAttr('disabled');
	if(typeof responseJSON != 'undefined'){
		if(typeof responseJSON.cover_url == "undefined" || responseJSON.cover_url == ''){
			$('.pop-img').hide();
		}
	}
}


window.onresize = function(event) {
    rePosition();
}


//Invert all the selected/alternate
$('.alternate').live('click', function() {
	var newFID = $(this).attr('data-fid');
	
	//Loop through JSON to find FID
	for(i=0; i < jsonData.data.length; i++){
		if(jsonData.data[i].floor_id == newFID){
			loadData(jsonData.data[i]);
			break;
		}
	}
});




function rePosition(){
	
	var 	d = new Date(),
			dTime = d.getTime(),
			display = $('#stack1').css('display'),
			shiftLeft = (parseInt( $('#floor_img').css("border-left-width")  ) + parseInt( $('#map').css("padding-left")  ) ),
			shiftTop = (parseInt( $('#floor_img').css("border-top-width")  ) + parseInt( $('#map').css("padding-top")  ) );
			
	$("<img/>") // Make in memory copy of image to avoid css issues
		.load(function() {
			var pH =  $('#floor_img').height() / this.height;
			var pW = $('#floor_img').width() / this.width;
			var styleAttr = 'top:'+Math.round(tipObj.map_y*pH+shiftTop)+'px; left:'+Math.round(tipObj.map_x*pW+shiftLeft)+'px; width:'+Math.round(tipObj.width*pW)+'px; height:'+Math.round(tipObj.height *pH)+'px;';
			$('#stack1').attr('style', styleAttr);
			positionPop();
		}).attr("src", $('#floor_img').attr('src')+'?t='+dTime);

}

function positionPop(){

	//Positions the Tipsy thing
	var offset = 15,
		padding = 0,
		containX = $('#map').width(),
		topX = $('#stack1').position().left,
		width = $('#stack1').width(),
		height = $('#stack1').height(),
		topY = $('#stack1').position().top,
		placeX, placeY, tipDir;

	if (width < height && $('#floor_img').width() > 500) { //If tall stack (rather then wide)
		placeX = width + offset - padding; //Right of stack, factoring in padding/offset
		placeY = (height / 2) - ($('#pop-container').height() / 2) - padding; //Center vertically
		tipDir = 'l';
		
		if(placeY < 0){ //Do not center vertically if there is no room ( y = top of stack )
			placeY = 0; tipDir = "";
		}
		
		//If offscreen on right side, move box to left of stack
		if(((topX + width)+(placeX + $('#pop-container').width())) > containX){
			
			placeX =0;
			placeX  -=$('#pop-container').width() + offset;
			console.log(placeX);
			
			tipDir = 'r';
			
			$('#pop-container').css('top', placeY).css('left', placeX);
			redrawTip('#pop-container .ipad-pop', tipDir);
		}
		else{
			$('#pop-container').css('top', placeY).css('left', placeX);
			redrawTip('#pop-container .ipad-pop', tipDir);
		}
		
		
		
		
	} 
	else { //If wide stack (or small map [eg. mobile device])
		placeX = (width / 2) - ($('#pop-container').width() / 2) - padding; //Center horizantally
		placeY = height + offset - padding; //Beneath stack
		tipDir = 't';
		
		//If x goes offscreen left(within 5px)
		if((topX+placeX) < 5){
			placeX += Math.abs(topX+placeX)+5
			tipDir = '';
		}
		//If x goes offscreen right (PROBLEM: if beneath, new pos is wrong)
		if((topX+width + (($('#pop-container').width()-width)/2)) > containX){ 
			//placeY = 0;
			//placeX -= $('#pop-container').width();
			//tipDir = 'r';
			
			placeX -= 30;
			tipDir = '';
			
		}
		
		
		
		
		$('#pop-container').css('top', placeY).css('left', placeX);
		$('#pop-container b').css('margin-left', -10); // ???
		redrawTip('#pop-container .ipad-pop', tipDir);
		
		
		
		
		// var doubleBack = containX - ( topX + padding + ($('#pop-container').width() / 2) + (width/2));
		// if( doubleBack < 1){
			// $('#pop-container').css('left', ( placeX + doubleBack - 1 ));
			// $('#pop-container b').css('margin-left', (-10 - (doubleBack)));
		// }
	}
}

function redrawTip(el, tipDir) {
	$(el).attr('data-tip', tipDir).removeClass('.ipad-pop').addClass('.ipad-pop');
}

function getResponse(apiCall, callback){
	var json = {}, d = new Date(), dTime = d.getTime();
	
	// Get data from the API b/c it's not stored
	$.ajax({
	  url: base_url+'api/'+apiCall,
	  dataType: 'jsonp',
	  success: function(data){
		data.time = dTime;
		callback(data);
	  }
	}).error(function(jqXHR, textStatus, errorThrown){
		var data = {
			time: 0,
			jqXHR: jqXHR, 
			textStatus: textStatus, 
			errorThrown: errorThrown
		}
		callback(data);
	});
	
}