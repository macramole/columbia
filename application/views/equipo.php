<section id="equipo" class="listadoFotos">
	<h1>Equipo</h1>
	<div class="texto">
		<?= $texto ?>
	</div>
	<?php magico_setEditable(4, 'Estatica', 'textoDestacado', 'section#equipo > .texto') ?>
	
	<?php if ( count($miembrosPrincipales) ) : ?>
		<ul class="principales">
			<?php foreach( $miembrosPrincipales as $miembro ) : ?>
			<li>
				<a href="<?= $miembro['url'] ?>">
					<img src="<?= $miembro['imagen'] ?>" />
					<?= $miembro['title'] ?> <span>[+]</span> <br />
					<span class="actividad"><?= $miembro['actividad'] ?></span>
				</a>
				
			</li>
			<?php endforeach; ?>
		</ul>
		<div class="clear"></div>
	<?php endif; ?>
	
	<?php if ( count($equipo) ) : ?>
		<ul class="categorias">
			<?php foreach($categorias as $idCategoria => $categoria) : ?>
				<?php if ( count($equipo[$idCategoria]) ) : ?>
					<li>
						
						<h2><?= $categoria['title'] ?></h2>
						<ul class="grande" rel="<?=$idCategoria?>">
						<?php foreach($equipo[$idCategoria] as $key => $miembro ) : ?>
							<?php if ( !is_numeric($key) ) continue; ?>
							<li>
								<a href="<?= $miembro['url'] ?>">
									<img src="<?= $miembro['imagen'] ?>" />
									<div class="info">
										<h3><?= $miembro['title'] ?></h3>
										<div class="actividad"><?= $miembro['actividad'] ?></div>
										<div class="textoCorto"><?= $miembro['textoCorto'] ?></div>
										<?php if ( $miembro['anio'] ) : ?><div class="anio">Año: <?= $miembro['anio'] ?></div><?php endif; ?>
									</div>
								</a>
								
							</li>
						<?php endforeach; ?>
						</ul>
						<div class="clear"></div>
						<?php if ( count($equipo[$idCategoria]['miembrosAnteriores']) )  : ?>
						<ul class="chicos" rel="<?= $idCategoria ?>">
							<?php foreach( $equipo[$idCategoria]['miembrosAnteriores'] as $miembro ) : ?>
							<li>
								<strong><?= $miembro['title'] ?></strong> | <?= $miembro['actividad'] ?><?php if ( $miembro['anio'] ) : ?> | Año <?= $miembro['anio'] ?><?php endif; ?>
								
							</li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
						<div class="clear"></div>
						<?php magico_setData($equipo[$idCategoria]['miembrosAnteriores'], 'Equipo', "section#equipo ul.chicos[rel=\"$idCategoria\"] li", MAGICO_SORTABLE) ?>
						<?php unset($equipo[$idCategoria]['miembrosAnteriores']) ?>
						<?php magico_setData($equipo[$idCategoria], 'Equipo', "section#equipo ul.grande[rel=\"$idCategoria\"] li", MAGICO_SORTABLE) ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	
	<?php magico_setData($miembrosPrincipales, 'Equipo', 'section#equipo ul.principales li', MAGICO_SORTABLE)  ?>
	<?php magico_setData($categorias, 'EquipoCategoria', 'section#equipo ul.categorias > li', MAGICO_SORTABLE)  ?>
</section>