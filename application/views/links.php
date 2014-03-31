<section id="links" class="listadoFotos">
	<h1>Sitios amigos</h1>
	
	<?php if ( count($links) ) : ?>
		<div class="textoWrapper">
			<?= $texto['textoDestacado'] ?>
		</div>
	
		<ul>
			<?php foreach( $links as $link ) : ?>
			<li>
				<a href="<?= $link['link'] ?>" target="_blank">
					<img src="<?= $link['imagen'] ?>" />
					<?= $link['title'] ?>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<div class="noContent">No hay sitios amigos cargados.</div>
	<?php endif; ?>
	
	<?php magico_setData($links, 'Link', 'section#links ul li', MAGICO_SORTABLE)  ?>
	<?php magico_setEditable($texto['id'], 'Estatica', 'textoDestacado', 'section#links .textoWrapper') ?>
</section>