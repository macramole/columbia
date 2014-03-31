<section id="puertas_de_entrada">
	<h1>Puertas de entrada</h1>
	<div class="cuerpo">
		<?= $cuerpo ?>
	</div>
	<?php magico_setEditable(2, 'Estatica', 'textoDestacado', 'section#puertas_de_entrada > .cuerpo') ?>
	<?php if ( count($arrPuertas) ) :  ?>
	<div class="puertas">
		<?php foreach( $arrPuertas as $nombrePuerta => $puerta ) : ?>
			<?php if ( count($puerta) ) : ?>
				<div class="puerta <?= $nombrePuerta ?>">
					<h2><?= $puerta[0]['puerta'] ?></h2>
					<ul>	
						<?php foreach( $puerta as $disciplina ) : ?>
							<li class="item" rel="<?= $disciplina['id'] ?>"><a href="#"><?= $disciplina['title'] ?></a><?php magico_drag() ?></li>
						<?php endforeach; ?>
					</ul>
					<div class="textoWrapper">
						<div class="texto">
							<div class="descripcion">
								<?= $puerta[0]['descripcion'] ? $puerta[0]['descripcion'] : '<p>No hay información disponible</p>'?>
							</div>
						</div>
						<?php foreach( $puerta as $disciplina ) : ?>
						<div class="texto" rel="<?= $disciplina['id'] ?>">
							<div class="textoDestacado">
								<?= $disciplina['textoCorto'] ? $disciplina['textoCorto'] : '<p>No hay información disponible</p>'?>
							</div>
							<a class="mas" href="<?= magico_urlclean('disciplinas', $disciplina['id']) ?>">[ver +]</a>
						</div>
						
						<?php endforeach; ?>
					</div>
					<div class="clear"></div>
				</div>
			<?php endif; ?>
			<?php magico_setData($puerta, 'Disciplina', "section#puertas_de_entrada .puertas div.$nombrePuerta li.item", MAGICO_SORTABLE, "{items: 'li.item'}") ?>
			<?php magico_setEditables($puerta, 'Disciplina', 'textoCorto', "section#puertas_de_entrada .puertas div.$nombrePuerta .texto .textoDestacado") ?>
			<?php magico_setEditable($puerta[0]['idPuerta'], 'Puerta', 'descripcion', "section#puertas_de_entrada .puertas div.$nombrePuerta .texto .descripcion") ?>
		<?php endforeach; ?>
		
	</div>
	<div id="actividades" class="actividades">
		<h3>Actividades regulares</h3>
		<ul class="regulares">
			<li class="cargando">Cargando...</li>
		</ul>
		<h3 class="especiales">Actividades especiales</h3>
		<ul class="especiales">
			<li class="cargando">Cargando...</li>
		</ul>
	</div>
	<?php endif; ?>
</section>