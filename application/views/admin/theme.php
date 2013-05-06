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
<?php $controller = $this->router->fetch_class() ?>
<?php $method = $this->router->fetch_method() ?>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		
		<?php if(isset($title)):?><title><?= $title?></title><?php endif;?>
		
		<link rel="stylesheet" href="<?= base_url() ?>static/css/reset.css" type="text/css" media="screen">
		<link rel="stylesheet" href="<?= base_url() ?>static/css/style.css" type="text/css" media="screen">
		
		<link rel="stylesheet" href="<?= base_url() ?>static/css/smoothness/jquery-ui-1.8.13.custom.css">
		
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>static/js/jquery-ui-1.8.13.custom.min.js" /></script>
		
		
		<?php if(isset($headers)) echo $headers; ?>
	</head>
  
	<body>
		<div id="body-wrapper">
		
			<div id="sidebar">
				<div id="sidebar-wrapper">
					<img id="logo" src="<?= base_url() ?>img/RULA-logo.png" width="180" alt="Admin logo" style="padding-left: 2em">
		
					<ul id="main-nav"> 
						<li>
							<a href="<?= base_url() ?>" class="nav-top-item no-submenu">View Site</a>       
						</li>
						
						<li> 
							<a href="#" class="nav-top-item <?= ($controller === "admin")? 'current' : '' ?>">Admin</a>
							<ul style="display: block; ">
								<?php if($this->session->userdata('admin_type') === "Super Admin"): ?>
								<li><a class="<?= ($method === "manage_admin")? 'current' : '' ?>" href="<?= base_url() ?>admin/manage_admin">Manage Administrators</a></li>
								<li><a class="<?= ($method === "manage_buildings")? 'current' : '' ?>" href="<?= base_url() ?>admin/manage_buildings">Manage Buildings</a></li>
								<li><a class="<?= ($method === "manage_floors")? 'current' : '' ?>" href="<?= base_url() ?>admin/manage_floors">Manage Floors</a></li>
								<li><a class="<?= ($method === "manage_item_types")? 'current' : '' ?>" href="<?= base_url() ?>admin/manage_item_types">Manage Item Types</a></li>
								<li><a class="<?= ($method === "manage_stack_types")? 'current' : '' ?>" href="<?= base_url() ?>admin/manage_stack_types">Manage Stack Types</a></li>
								<?php endif; ?>
								<li><a class="<?= ($method === "index")? 'current' : '' ?>" href="<?= base_url() ?>admin/">Modify Stacks</a></li>
								
								<?php if(!USE_LDAP): ?>
								<li><a class="<?= ($method === "myaccount")? 'current' : '' ?>" href="<?= base_url() ?>admin/myaccount">My Account</a></li>
								<?php endif; ?>
								<li><a class="<?= ($method === "usage")? 'current' : '' ?>" href="<?= base_url() ?>admin/usage">Usage Stats</a></li>
								<li><a href="<?= base_url() ?>admin/logout">Logout</a></li>
								
							</ul>
						</li>
					</ul>
				</div>

		
			</div> 
			
			<div id="main-content">
				<?php if(isset($title)):?><h2><?= $title?></h2><?php endif;?>
	
				<?php if(isset($content)) echo $content; ?>
				
				<div class="clear"></div>
				

			</div>
		</div>
  	</body>
</html>