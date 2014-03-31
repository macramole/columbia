<section id="home">
	<?php if ( $home['imagenes'] ) : ?>
	<div id="coin-slider" class="slide">
		<?php foreach( $home['imagenes'] as $imagen ) : ?>
		<a href="<?= $imagen['description'] ?>">
			<img src="<?= $imagen['url'] ?>" />
		</a>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<div class="content">
		<div class="left">
			<div class="mensajes">
				<?= $home['texto'] ?>
			</div>
			
			<?php magico_setEditable(1, 'Home', 'texto', 'section#home .content .left .mensajes') ?>
			<?php magico_setMainData(1, 'Home') ?>
		</div>
		<div class="right">
			<?php if ( count($arrNovedades) ) : ?>
			<div class="novedades">
				<h2>Novedades</h2>
				<ul>
					<?php foreach( $arrNovedades as $novedad ) : ?>
					<li>
						<a href="<?= magico_urlclean('novedades', $novedad['id']) ?>">
							<img src="<?= $novedad['imagen'] ?>" />
							<?php if ($novedad['video']) : ?>
							<div class="videoDuration"><?= gmdate("i:s", $novedad['videoDuration']); ?>&nbsp;</div>
							<?php endif; ?>
						</a>
						<div class="novedad">
							<h3><?= $novedad['title'] ?></h3>
							<div class="cuerpo">
								<?= $novedad['resumen'] ?>
							</div>
							<a class="mas" href="<?= magico_urlclean('novedades', $novedad['id']) ?>">[leer +]</a>
						</div>
						<div class="clear"></div>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php magico_setData($arrNovedades, 'Novedad', 'section#home .content .right .novedades ul li') ?>
				<?php magico_setEditables($arrNovedades, 'Novedad', 'resumen', 'section#home .content .right .novedades ul li .cuerpo') ?>
			</div>
			<?php endif; ?>
		</div>
		<div class="clear"></div>
		<div class="bottom">
			<div class="newsletter">
				<h3>Recibí el boletín de novedades</h3>
				<form id="frmNewsletter">
					<fieldset>
						<div class="wrapper">
							<!--<p>Recibí todas las novedades en tu correo:</p>-->
							<input type="text" name="email" value="Dejanos tu correo electrónico" /><button type="button"></button>
						</div>
						<span class="message">Un momento por favor...</span>
					</fieldset>
				</form>
			</div>
			
			<div class="nuestra_casa">
				<h3>Nuestra casa</h3>
				<fieldset>
					<p>
						Jorge Luis Borges 2020. <br/>
						Palermo, CABA. <br />
						<strong>Tel.:</strong> +54 (11) 4775-2172 y 4776-4462<br /><br />
						<a href="informacion/nuestra-casa">ver mapa [+]</a>
					</p>
					<a href="informacion/nuestra-casa" class="mapa"><img src="images/mapa_contacto.jpg" /></a>
				</fieldset>
			</div>
			
			<div class="apoyo"><?= $home['apoyo'] ?></div>
			<?php magico_setEditable(1, 'Home', 'apoyo', 'section#home .content .bottom .apoyo') ?>
		</div>
	</div>
</section>