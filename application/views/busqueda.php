<section id="buscar" class="listado">
	<h1>Resultado de búsqueda</h1>
	
	<div class="clear"></div>
	
	<?php if ( count($arrBusqueda) ) : ?>
		<ul>
			<?php foreach( $arrBusqueda as $resultado ) : ?>
			<li>
				<div class="texto">
					<h2><a href="<?= $resultado['url'] ?>"><?= $resultado['title'] ?></a></h2>
					<div class="resumen">
						<?= $resultado['resumen'] ?> <a href="<?= $resultado['url'] ?>" class="mas">[ver +]</a>
					</div>
					<div class="tag"><?= $resultado['tipo'] ?></div>
				</div>
				<div class="clear"></div>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<div class="noContent">No se encontraron resultados.</div>
	<?php endif; ?>	
</section>