<?php
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
?>
<?php ob_start();?>
		<script type="text/javascript" src="<?= base_url() ?>static/js/simpla.jquery.configuration.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>static/js/jquery.tipsy.js"></script>
		<link type="text/css" rel="stylesheet" media="all" href="<?= base_url() ?>static/css/tipsy.css" />
		<style type="text/css">
			.ui-dialog-buttonset{
				width: 100%;
				text-align: right;
			}
			
			.ui-dialog{
				font-size: 1em;
			}
			.field_header{
				min-width: 25em;
			}
			.oldBox{
				background-color:rgba(0,0,255,0.3);
				border:1px #0000FF solid;
				cursor:pointer;
			}
			#current_box{
				background-color:rgba(0,255,0,0.3);
				border:1px #00FF00 solid;
			}
			.badBox{
				background-color:rgba(255,0,0,0.3);
				border:1px #FF0000 solid;
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				//Read whether or not the menu is dealing with a stack
				$('#item_type').change(function(){
					var is_stack = $("#item_type option:selected").attr('data-stack');
					
					if(is_stack == 0){
						$('.callno_form').css('display','none');
					}
					else{
						$('.callno_form').css('display','inline');
					}
				});
				
				
				//Create tooptips for existing zones
				$('.oldBox.stack').tipsy({
					title: 'data-callrange',
					gravity: 'sw'
				});
				
				$('.oldBox.location').tipsy({
					title: 'data-locname',
					gravity: 'sw'
				});
				
				
			
				var x1,y1;
				
				//Begin drawing a new zone
				//The box being drawn is given the ID "current_box"
				$('.openTab .img_container').live('mousedown', function(e) {
					//Check to make sure an existing zone was not clicked on
					var classes = e.target.className.split(' ');
					
					if($.inArray('oldBox', classes)){
						e.preventDefault();

						$('#call_start').val('');
						$('#call_end').val('');
						$('#loc_name').val('');
						
						$('#ok_btn .ui-button-text').html('Ok');
						$('#del_btn').hide();
						$('#update_id').remove();
						
						//Only want left click
						if(e.which == 1){
							box = $('<div style="z-index: 99; position:absolute;">');
							$('.openTab .img_container').append(box);
		
							x1 = e.pageX - $('.openTab .img_container').offset().left;
							y1 = e.pageY - $('.openTab .img_container').offset().top;
							
							box.attr({id: 'current_box'}).css({
								top: y1, 
								left: x1 
							});
						}


					}
				});
				
				//Grow/Shrink "current_box" based on the mouse movement
				$('.openTab .img_container').live('mousemove', function(e) {
					var oldTop = parseInt($('#current_box').css('top'));
					var oldLeft = parseInt($('#current_box').css('left'));
					var x_offset = $('.openTab .img_container').offset().left;
					var y_offset = $('.openTab .img_container').offset().top;
						
					var x_cord = e.pageX - x1 - x_offset;
					var y_cord = e.pageY - y1 - y_offset;
					
					if(y_cord < 0 && x_cord < 0){
						$("#current_box").css({
							left: (e.pageX -  x_offset),
							top: (e.pageY- y_offset),
							width:(Math.abs(x_cord)), 
							height:(Math.abs(y_cord))
						});
					}
					else if(x_cord < 0 ){
						$("#current_box").css({
							left: (e.pageX- x_offset),
							width:(Math.abs(x_cord)), 
							height:(y_cord) 
						});
					}
					else if(y_cord < 0 ){
						$("#current_box").css({
							top: (e.pageY - y_offset ),
							width:(x_cord ), 
							height:(Math.abs(y_cord)) 
						});
					}
					else{
						$("#current_box").css({
							width:(x_cord), 
							height:(y_cord) 
						});
					}
				});

				//Process just created box
				$(document).mouseup(function() {
					//Verify a box was being created before prompting user
					if($('#current_box').length > 0){
						if((parseInt($('#current_box').css('width')) * parseInt($('#current_box').css('height'))) > 100){
							$('#del_btn').hide(); //Prevent the delete box from being shown when the dialog is opened
							
							check_is_callno($("#item_type option:selected"));
							
							$('#callno_dialog').dialog("open");
						}
						else{
							$('#current_box').remove();
						}
					}
				});

				//If an existing zone was clicked
				$(".oldBox").live('click', function(){
					//$(this).attr('id', 'current_box');
					
					//Load the old data
					
					//Load stack data (if applicable)
					if($(this).hasClass('stack')){
						//Load old call number values
						var callNums = $(this).attr('data-callrange').split('-');
						if(callNums.length == 2){
							$('#call_start').val(callNums[0]);
							$('#call_end').val(callNums[1]);
						}
						$('#stack_type').val($(this).attr('data-stacktype'));
					}
					
					$('#loc_name').val($(this).attr('data-locname'));
					$('#item_type').val($(this).attr('data-itemtype'));
					
					
					
					//Add the delete button
					$('#del_btn').show(); //This is a previously created item, so ensure the item can be deleted
					$(this).addClass('deletable');
					

					

					//Add the "update" button to the dialog
					$('#ok_btn .ui-button-text').html('Update');

					//Add a hidden field to contain the id
					$('#update_id').remove();
					$('#loc_id').remove();
					
					$('#stack_data').append('<input type="hidden" name="loc_id" id="loc_id" value="' + $(this).attr('data-locid') +'" />');
					

					check_is_callno($("#item_type option:selected"));
					$('#callno_dialog').dialog("open");
				});
			});
			
			function check_is_callno(element){
				var is_stack = element.attr('data-stack');
				
				if(is_stack == 0){
					$('.callno_form').css('display','none');
				}
				else{
					$('.callno_form').css('display','inline');
				}
			}
			
			//Create the dialog to add/edit
			$(function() {
				$('#callno_dialog').dialog({
					autoOpen: false,
					modal: true,
					title: "Selection Data",
					show: "highlight",
					hide: "explode",
					buttons: [{
						text: "Ok",
						id : "ok_btn",
						click : function(){
							$(this).dialog("close");
							
							var data = 	$('#stack_data').serialize()+ 								//Call Numbers
										'&top='+parseInt($("#current_box").css('top')) + 			//Distance from top of image
										'&left=' + parseInt($("#current_box").css('left')) +  		//Distance from left of image
										'&height=' + parseInt($("#current_box").css('height')) +	//Height of box
										'&width=' + parseInt($("#current_box").css('width')) +   	//Width of the box
										'&floor=' + $('.openTab').attr('id');						//The floor_id stack is located on (neccessary?)
							
							var jqElement = $('#current_box');
							
							var loc_id;
							$.post('<?= base_url();?>admin/submit_stack', data, function(e){
								loc_id = e;
							});
							$("#current_box").addClass('oldBox'); //Allow it to be editable
							
							
							 //Populate data fields
							$('#current_box, .deletable').attr({ 'data-itemtype': $('#item_type').val()});
							$('#current_box, .deletable').attr({ 'data-locname': $('#loc_name').val()});
							$('#current_box, .deletable').attr({ 'data-locid': loc_id}); 
							
							//Populate the stack fields (if applicable)
							$('#current_box, .deletable').attr({ 'data-callrange': $('#call_start').val().toUpperCase()+'-'+$('#call_end').val().toUpperCase() });
							$('#current_box, .deletable').attr({ 'data-stacktype': $('#stack_type').val()});
							
							$('.deletable').removeClass('deletable');

							$('#current_box').tipsy({
								title: 'data-callrange',
								gravity: 'sw'
							});

							//Allow for new zones to be created
							$("#current_box").attr({ id: '' });
							
							
							//CLEAR THE LOC ID!
							$('#update_id').remove();
							$('#loc_id').remove();
					
						}
						},{
						text: "Cancel",
						click : function(){  
							//If it was "Cancelled", close the dialog and fade out the zone
							$(this).dialog("close"); 
							$("#current_box").addClass('badBox');
							$("#current_box").attr('id','');
							$('.deletable').removeClass('deletable');
							$(".badBox").fadeOut(1500, function(){$(this).remove();});
							$('#update_id').remove();
							$('#loc_id').remove();
						  }
						},{
							text: "Delete",
							style: "float:left",
							id: "del_btn",
							click: function(){
								//console.log($('#stack_data').serialize());
								//Submit rnage_id to be deleted, and remove it from the screen
								$.post('<?= base_url();?>admin/delete_stack', $('#stack_data').serialize());
								$('.deletable').addClass('badBox');
								$('.deletable').fadeOut(1500, function(){$(this).remove();});
								$(this).dialog("close");
								$('#update_id').remove();
								$('#loc_id').remove();

							}
						}],
					close: 	function(){
								//Fixes bug where box is being drawn with mouse not down
								if($('#current_box').length > 0){
									$('#current_box').remove();
								}
							
								$("#current_box").addClass('badBox');
								$("#current_box").attr('id','');
								$('.deletable').removeClass('deletable');
								$(".badBox").fadeOut(1500, function(){$(this).remove();});
								$('#update_id').remove();
								$('#loc_id').remove();
							}
				});
			}); 
		</script>
<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

		
<div class="content-box" style="float: left; min-width: 1600px;">
	<div class="content-box-header">
		<h3>Library Building</h3>
		
		<ul class="content-box-tabs">
			<?php $count=0; foreach($floors->result() as $floor):?>
				<li><a href="#tab<?= $floor->floor_id; ?>" class="<?= ($count === 0)? 'default-tab' : ''?>"><?= $floor->floor_name ?></a></li> 
			<?php $count++; endforeach;?>
		</ul>
		
		<div class="clear"></div>
		
	</div>
	
	<div class="content-box-content" >
		
		<?php $count = 0; foreach($floors->result() as $floor):?>
			<div class="tab-content <?= ($count === 0)? 'default-tab openTab' : ''?>" id="tab<?= $floor->floor_id ?>"> 
				<div style="display: inline-block; padding:0;position:relative;" class="img_container">
					<!--- Redraw the existing locations --->
					<?php $existing = $this->model_admin->get_floor_data($floor->floor_id); ?>
					<?php foreach ($existing->result() as $div):?>
						<?php if($div->is_stack == 1): ?>
							<?php $stack_data_query = $this->model_admin->get_stack_data($div->loc_id); ?>
							<?php $stack_data = $stack_data_query->row(); ?>	
							<div class="oldBox stack" data-stacktype="<?= $stack_data->stack_type_id ?>" data-itemtype="<?= $div->type_id ?>" data-locname="<?= $div->loc_name ?>"  data-locid="<?= $div->loc_id ?>" data-callrange="<?= strtoupper($stack_data->call_range_start) . "-" . strtoupper($stack_data->call_range_end) ?>" style="position:absolute; z-index: 99; top:<?=$div->map_y?>px; left:<?= $div->map_x?>px; width:<?= $div->width?>px; height:<?= $div->height?>px"></div>
						<?php else: ?>
							<div class="oldBox location" data-itemtype="<?= $div->type_id ?>" data-locname="<?= $div->loc_name ?>" data-locid="<?= $div->loc_id ?>" style="position:absolute; z-index: 99; top:<?=$div->map_y?>px; left:<?= $div->map_x?>px; width:<?= $div->width?>px; height:<?= $div->height?>px"></div>
						<?php endif; ?>
					<?php endforeach;?>
					<img class="floor_img" style="padding: 0" src="<?= base_url() . MAPS_DIR . '/'. $floor->img_url ?>" />
				</div>
			</div> 
		<?php $count++; endforeach;?>
		
	</div> 
		
</div> 

<div id="mouse_pos"></div>

<div id="callno_dialog" style="font-size:1em">
	<form id="stack_data">
		<strong>Item type:</strong><br />
		<select name="item_type" id="item_type">
			<?php foreach($item_types->result() as $i_type): ?>
				<option data-stack="<?= $i_type->is_stack ?>" value="<?= $i_type->type_id ?>"><?= $i_type->type_name ?></option>
			<?php endforeach; ?>
		</select>
		
		<br /><br />
		
		
		<div style="float: left">
			<strong>Name:</strong>
			<br />
			<input type="text" name="loc_name" id="loc_name" size="15" value="" />
		</div>
			
		<div id="form_container" class="callno_form">
			
			<div style="float: right">
				<strong>Stack Type:</strong>
				<br />
				<select name="stack_type" id="stack_type">
					<?php foreach($stack_types->result() as $s_type): ?>
					<option value="<?= $s_type->stack_type_id ?>"><?= $s_type->stack_type_name ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<div style="clear:both"></div>
			<br />
			
			<div style="float: left">
				<strong>Call Number Start:</strong>
				<br />
				<input type="text" name="call_start" id="call_start" size="15" value="" />
			</div>
			
			<div style="float: right">
				<strong>Call Number End:</strong>
				<br />
				<input type="text" name="call_end" id="call_end" size="15" value="" />
			</div>
			
			
			</div>
		
		
		 <br />
	</form>
</div>




		
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>
		