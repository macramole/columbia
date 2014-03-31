<section id="novedades" class="listado">
	<h1>Novedades</h1>
	<a href="<?= site_url('novedades') ?>" class="back">&lt; volver</a>
	<div class="clear"></div>
	
	<?php if ( count($arrNovedades) ) : ?>
		<ul>
			<?php foreach( $arrNovedades as $novedad ) : ?>
			<li rel="<?= $novedad['id'] ?>">
				<a href="<?= $novedad['url'] ?>" class="imagen">
					<img src="<?= $novedad['imagen'] ?>" alt="<?= $novedad['title'] ?>" />
					<?php if ($novedad['video']) : ?>
					<div class="videoDuration"><?= gmdate("i:s", $novedad['videoDuration']); ?></div>
					<?php endif; ?>
				</a>
				<div class="texto">
					<h2><?= $novedad['title'] ?></h2>
					<div class="resumen">
						<?= $novedad['resumen'] ?>
					</div>
					<a href="<?= $novedad['url'] ?>" class="mas">[ver +]</a>
				</div>
				<div class="clear"></div>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( $arrNovedades[0]['cantidad'] > $this->CANT_PAGINADO ) : ?>
			<div class="cargarMas">
				<a href="#">ver anteriores</a>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<div class="noContent">No hay novedades cargadas.</div>
	<?php endif; ?>
	
		
	<?php magico_setEditables($arrNovedades, 'Novedad', 'resumen', 'section#novedades ul li .resumen');  ?>
	<?php magico_setData($arrNovedades, 'Novedad', 'section#novedades ul li')  ?>
</section>