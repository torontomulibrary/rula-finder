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
?><!DOCTYPE html>

<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/hmtl; charset=utf-8">
		<link rel="stylesheet" href="<?php echo base_url(); ?>static/css/login_style.css" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
		
		<!-- FIX FOR HTML5 PLACEHOLDER -->
		<script type="text/javascript">
			$(document).ready(function() {
				$('[placeholder]').focus(function() {
					var input = $(this);
					if (input.val() == input.attr('placeholder')) {
						input.val('');
						input.removeClass('placeholder');
					}
				}).blur(function() {
					var input = $(this);
					if (input.val() == '' || input.val() == input.attr('placeholder')) {
						input.addClass('placeholder');
						input.val(input.attr('placeholder'));
					}
				}).blur().parents('form').submit(function() {
					$(this).find('[placeholder]').each(function() {
						var input = $(this);
						if (input.val() == input.attr('placeholder')) {
							input.val('');
						}
					})
				})
			});
		</script>
		<style type="text/css">
			.placeholder { color: #aaa; }
		</style>
	</head>
	<body>
		<div id="login_form">
			
			<img id="logo" style="display:block; margin: 0 auto;" width="250" src="<?= base_url() ?>img/RULA-logo.png" alt="Admin Panel" />
			<?php
				echo form_open('login/validate_credentials');
			?>
				<input type="text" name="username" placeholder="Username" />
				<input type="password" name="password" placeholder="Password" />
				<input type="submit" name="submit" value="Login" />	
				
			
			<?php
				$msg = $this->session->flashdata('message');
				if($msg != ""):
			?>
			<br><br><br>
			
			<div class="err_msg">
				<?php echo $this->session->flashdata('message')?>
			</div>
			<?php endif?>	
		</div>
		
	</body>
</html>