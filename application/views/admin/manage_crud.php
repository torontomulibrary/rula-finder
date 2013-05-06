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
<? //HEADER DATA ?>
<?php ob_start();?>

<? foreach($crud->css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<?php foreach($crud->js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<? //BODY DATA ?>
<?php ob_start();?>

<?= $crud->output ?>
		
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>
		