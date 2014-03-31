<?php foreach( $arrNovedades as $novedad ) : ?>
<li rel="<?= $novedad['id'] ?>" id="<?= "Novedad_$novedad[id]" ?>">
	<a href="<?= $novedad['url'] ?>" class="imagen"><img src="<?= $novedad['imagen'] ?>" alt="<?= $novedad['title'] ?>" /></a>
	<div class="texto">
		<h2><?= $novedad['title'] ?></h2>
		<div class="resumen">
			<?= $novedad['resumen'] ?>
			<a href="<?= $novedad['url'] ?>" class="mas">[ver +]</a>
		</div>

	</div>
	<div class="clear"></div>
</li>
<?php endforeach; ?>
<?php magico_setData($arrNovedades, 'Novedad')  ?>
