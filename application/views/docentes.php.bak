<section id="docentes" class="listadoFotos">
	<h1>Docentes</h1>
	<?php if ( count($arrDocentes) ) : ?>
		<ul>
			<?php foreach( $arrDocentes as $docente ) : ?>
			<li>
				<a href="<?= $docente['url'] ?>">
					<img src="<?= $docente['imagen'] ?>" />
					<?= $docente['title'] ?> <span>[+]</span>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<div class="noContent">No hay docentes cargados.</div>
	<?php endif; ?>
	
	<?php magico_setData($arrDocentes, 'Docente', 'section#docentes ul li')  ?>
</section>