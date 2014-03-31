<?php
/*
 Mâgico
 http://www.parleboo.com
 Copyright 2012 Leandro Garber <leandrogarber@gmail.com>
 Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

global $CFG;

$magico_nav = $CFG->item('magico_nav');
$enableFacebook = $CFG->item('magico_enable_facebook');
$magico_customList = $CFG->item('magico_customList');
$magico_has_config = $CFG->item('magico_has_config');
?>
<?php if ( AdminUser::isLogged() ) : ?>
	<div id="adminNavWrapper">
		<div id="adminNav">
			<div class="adminDrag"></div>
			<ul>
				<li><img src="<?= base_url() ?>images/backend/add.png" rel="add" title="<?= lang('magico_nav_new') ?>" /></li>
				<li class="edit"><img src="<?= base_url() ?>images/backend/edit.png" title="<?= lang('magico_nav_edit') ?>" /></li>
				<li class="delete"><img src="<?= base_url() ?>images/backend/trash.png" title="<?= lang('magico_nav_delete') ?>" /></li>
				<li><img src="<?= base_url() ?>images/backend/process.png" rel="settings" title="<?= lang('magico_nav_settings') ?>" /></li>
				<li class="logout"><img src="<?= base_url() ?>images/backend/logout.png" title="<?= lang('magico_nav_logout') ?>" /></li>
			</ul>
			<div class="invisible" title="Hide"></div>
		</div>
		<div id="adminNavItems">
			<ul class="add">
				<li class="title"><?= lang('magico_nav_new') ?></li>
				<?php foreach ( $magico_nav as $content_type_name => $item ) : ?>
					<?php if ( !$item['noAdd'] && $this->adminuser->tienePermiso($content_type_name) ) : ?>
						<li class="item">
							<a href="<?= site_url('abm/create/' . $content_type_name) ?>" title=""><?= $item['title'] ? $item['title'] : $content_type_name ?></a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<ul class="settings">
				<li class="title"><?= lang('magico_nav_settings') ?></li>
				
				<?php if ( $magico_has_config ) : ?>
				<li class="item">
					<a href="<?= site_url('abm/edit/Configuracion/1') ?>" title="">Configuración general</a>
				</li>
				<?php endif; ?>
				
				<li class="item">
					<a href="<?= site_url('abm/edit/Admin/' . $this->adminuser->getId() ) ?>" title="">Modificar mi usuario</a>
				</li>
				
				<?php if ( $this->adminuser->tienePermiso('Admin') ) : ?>
				<li class="item">
					<a href="<?= site_url('abm/listContent/Admin') ?>" title="">Administradores</a>
				</li>
				<?php endif; ?>
				
				<li class="title"><?= lang('magico_nav_list') ?></li>
				<?php foreach ( $magico_customList as $key => $listado ) : ?>
				<li class="item">
					<a href="<?= site_url("abm/customList/$key") ?>" title=""><?= $listado['title'] ?></a>
				</li>
				<?php endforeach; ?>
				<?php foreach ( $magico_nav as $content_type_name => $item ) : ?>
					<?php if ( $this->adminuser->tienePermiso($content_type_name) ) : ?>
					<li class="item">
						<a href="<?= site_url('abm/listContent/' . $content_type_name) ?>" title=""><?= $item['title'] ? $item['title'] : $content_type_name ?></a>
					</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php if ($enableFacebook) : ?>
		<div id="fb-root"></div>
		<script>
		window.fbAsyncInit = function() {
			FB.init({
			appId      : '<?= $enableFacebook ?>', // App ID
			status     : true, 
			cookie     : true, 
			xfbml      : true
			});
		};

		// Load the SDK Asynchronously
		(function(d){
			var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement('script'); js.id = id; js.async = true;
			js.src = "//connect.facebook.net/en_US/all.js";
			ref.parentNode.insertBefore(js, ref);
		}(document));
		</script>
	<?php endif; ?>
<?php endif; ?>
