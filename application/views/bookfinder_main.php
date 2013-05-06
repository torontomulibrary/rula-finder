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
<!doctype html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>RULA Finder</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <link rel="shortcut icon" href="favicon.ico.jpg">
  <link rel="apple-touch-icon" href="apple-touch-icon.png">


  <!-- CSS: implied media="all" -->
  <link rel="stylesheet" href="css/style.css?v=2">
 <link rel="stylesheet" href="<?= base_url(); ?>js/libs/fancybox/css/fancybox/jquery.fancybox.css">
 
	
	<!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->
	<script src="<?= base_url(); ?>js/libs/modernizr-1.7.min.js"></script>
	<script src='<?= base_url(); ?>js/libs/jquery-1.7.2.min.js'></script>

	<script src="<?= base_url(); ?>js/libs/fancybox/js/jquery.fancybox.js"></script>
  
	

</head>

<body>

  <div id="container">
    <header>

    </header>
	<div id="sidebar">
		<ul>
			<li id="logo"><a href=""><img src="<?= base_url(); ?>img/RULA-logo.png" alt="logo"/></a></li>
			<li id="search">
			
				<form action="<?= base_url() ?>search" method="POST">
					
					<input id="s" type="search" name="s" results="5" autosave="call_no_search" placeholder="Search... " speech x-webkit-speech style="float:left;"/>
					<input type="image" src="img/magnifying-glass-brushblack.png" alt="Submit" id="img_submit">
					<!--<input type="submit" value="Search" id="search_btn" id="" style="">-->
					
				</form>
				
				<div id="askus_mobile">
					<span class="help_text">Need<br />Help?</span>
				</div>
				
			</li>
			<li id="floors">
				<ul>
					<?php foreach($floors->result() as $floor): ?>
						<li data-fid="<?= $floor->floor_id ?>"><div class="floor_name"><?= $floor->floor_name ?></div>
							<span class="which-floor">Your book is on this floor</span>
							<span class="alt-floor">Also available on this floor</span>
						</li>
					<?php endforeach; ?>
				</ul>
				
					
			</li>
		</ul>
		
				
		<div id="askus">
			<span class="help_text">Need Help?</span>
		</div>
		<div id="email">
			<span class="email_text">Email Book Location</span>
		</div>
		
		
		<p class="ytlinks">
			<a class="fancybox.iframe" href="http://www.youtube.com/embed/fe3D5jvQ7FA?rel=0">Learn about Call Numbers</a> <br /><br />
			<a class="fancybox.iframe" href="http://www.youtube.com/embed/RTRUXTBTaA8?rel=0">Learn how to Place a Hold</a> <br /><br />
			<a class="fancybox.iframe" href="http://www.youtube.com/embed/z8AWC8nuqqg?rel=0">Learn about Self Checkout</a><br /><br />
		</p>

	</div>
	
			
		
	<div id="stretcher">
		<div id="map">
			<img id="floor_img" src="" alt="" />
			<div id="stack1" class="stack" style="display:none;">
				<div id="pop-container" class="remove-pop" style="position:absolute;padding:0px;display:inline-block;">
					<div class="ipad-pop">
						<span>
							<b></b>
							<img class="pop-img" src="" alt="" width="66" height="100" />
							<p class="pop-name"></p>
							<p class="pop-callno"></p>
							<p class="pop-other secondary"></p>
							<p class="pop-title"><a href=""></a></p>
							<p class="pop-desc"></p>
							
							
							
						</span>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>
			<div id="status-2">
			</div>
			<div id="status-3">
				<h2>Unable to locate your item</h2>
				<p>Please visit our Circulation Desk or Research Help Desk on the 2nd Floor if you require assistance locating this item</p>
			</div>
			<div id="status-empty">
					<!---
					<div class="ipad-pop" data-tip="l" style="position:absolute;top:85px; left:15px;">
						<span>
							<b></b>
							<p class="pop-title">Enter your call number here</p>
							<p class="pop-desc">We'll search for the resource and help you find it.</p>
						</span>
						<div style="clear:both"></div>
					</div>
					--->
			</div>
		</div>
	</div>
    <footer>

    </footer>
  </div> <!--! end of #container -->


  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if necessary -->
 

   <script type="text/javascript">
	//Global var to be read by JS files
	var base_url = '<?= base_url() ?>';
	
  </script>	
  
  <!-- scripts concatenated and minified via ant build script-->
  <script src="<?= base_url(); ?>js/plugins.js"></script>
  <script src="<?= base_url(); ?>js/script.js"></script>
  
  
  <!-- end scripts-->


  <!--[if lt IE 7 ]>
    <script src="<?= base_url(); ?>js/libs/dd_belatedpng.js"></script>
    <script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
  <![endif]-->
	
	<div id="help_content" style="display:none">
		<h2>If you need assistance, please:</h2><br />

		<ul>
		<li>Find a Staff Member,</li>
		<li>Visit 2nd floor Circulation Desk, iDesk, or</li>
		<li>Call us: <a href="tel:4169795055,2">416-979-5055 opt. 2</a></li>
		</ul>
		<br />
		<h2>If you are on the 5th floor, please visit the <br />Serials Help Desk for assistance.</h2>
		<br />
		
		
	</div>
	
	<div id="email_content" style="display:none">
		
		<div id="email_form">
		
			<h2>Email Book Location</h2><br />
		
			<form method="post" onsubmit="return false;" action="" id="email_fields">
				<label for="target_email">Send to Email Address</label>
				<input type="text" id="target_email" name="target_email">

				
					<br /><br />
					
				<input type="button" class="sharing_send" value="Send Email">
			</form>
		
		</div>
		
		
	</div>
	
	
	<script type="text/javascript">
		$('#askus, #askus_mobile').click(function(){
			
			var content = $('#help_content').html();
			  $.fancybox({
				  'content': content,
				  'padding' : 20,
				  'height' : '30px'
			  });
		});
		
		$('#email').click(function(){
			
			var content = $('#email_content').html();
			  $.fancybox({
				  'content': content,
				  'padding' : 20,
				  'height' : '30px'
			  });
		});
		
		$('.sharing_send').live('click',function(){
			//Important to keep fancybox in selector as it clones html (allowing wrong form to be selected)
			var data = $('.fancybox-inner #email_fields').serialize();
			
			//Append more data to this post (page url, title?, etc...)
			data += '&page_url='+escape(window.location); //Page URL
			data += '&title='+escape($('.pop-title').html()); //Title
			data += '&callno='+escape($('.pop-callno').html()); //Call Number
			data += '&shelf='+escape($('.pop-name').html()); //Shelf Number
			data += '&author='+escape($('.pop-desc').html()); //Author
			data += '&floor='+escape($('.selected .floor_name').html()); //Author
			
			
			$.ajax({
				type: "POST",
				url: '<?= base_url() ?>api/email',
				data: data,
				success: function(){
					
				},
				
			});
			$.fancybox.close();
		});
		
		
	
	</script>
	
</body>
</html>