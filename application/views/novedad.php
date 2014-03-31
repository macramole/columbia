<section id="novedad" class="nota">
	<h1><?= $novedad['title'] ?></h1>
	<a href="<?= $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : site_url('novedades') ?>" class="back">&lt; volver</a>
	<div class="clear"></div>
	<div class="top">
		<?php if ($novedad['video']) : ?>
			<?= $novedad['video'] ?>
		<?php else : ?>
			<?php if ( $novedad['imagenes'] ) : ?>
				<div id="coin-slider-novedad" class="slide">
					<?php foreach( $novedad['imagenes'] as $imagen ) : ?>
					<a href="#">
						<img src="<?= $imagen ?>" />
					</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="text">
				<?= $novedad['textoDestacado'] ?>
			</div>
		<?php endif;?>
		<div class="clear"></div>
	</div>
	<div class="contenido">
		<?= $novedad['cuerpo'] ?>
	</div>
	<?php magico_setEditable($novedad['id'], 'Novedad', 'textoDestacado', 'section#novedad .top .text');  ?>
	<?php magico_setEditable($novedad['id'], 'Novedad', 'cuerpo', 'section#novedad .contenido');  ?>
	<?php magico_setMainData($novedad['id'], 'Novedad')  ?>
</section>