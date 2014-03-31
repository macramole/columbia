<section id="publicaciones" class="listado">
	<h1>Publicaciones / Prensa</h1>
	
	<?php if ( count($publicaciones) ) : ?>
		<ul>
			<?php foreach( $publicaciones as $publicacion ) : ?>
			<li>
				<a class="imagen" href="<?= $publicacion['link'] ?>" target="_blank">
					<img src="<?= $publicacion['imagen'] ?>" alt="<?= $publicacion['title'] ?>" />
				</a>
				
				<div class="texto">
					<h2><?= $publicacion['medio'] ?></h2>
					<h3><?= $publicacion['title'] ?></h3>
					<h4><?= $publicacion['fecha'] ?></h4>
					<a href="<?= $publicacion['link'] ?>" class="mas" target="_blank">[ver +]</a>
				</div>
				<div class="clear"></div>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<div class="noContent">No hay publicaciones cargadas.</div>
	<?php endif; ?>
	
		
	<?php magico_setData($publicaciones, 'Publicacion', 'section#publicaciones ul li')  ?>
</section>