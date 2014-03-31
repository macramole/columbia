<section id="actividad_especial" class="nota">
	<h1><?= $actividad['title'] ?></h1>
	<a href="<?= $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : site_url('agenda/mensual') ?>" class="back">&lt; volver</a>
	<div class="clear"></div>
	<div class="top">
		<?php if ( $actividad['imagenes'] ) : ?>
			<div id="coin-slider-novedad" class="slide">
				<?php foreach( $actividad['imagenes'] as $imagen ) : ?>
				<a href="javascript:;">
					<img src="<?= $imagen ?>" />
				</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="text">
			<div class="wrapper">
				<div class="fecha">
					<?php if( !is_null($actividad['fechaHasta']) ) : ?>
						<strong><?= $actividad['fecha'] ?></strong> <span>hasta</span>
						<strong><?= $actividad['fechaHasta'] ?></strong><br />
					<?php else : ?>
						<strong><?= $actividad['fecha'] ?></strong><br />
					<?php endif; ?>

					<?= $actividad['lugar'] ?>
				</div>
				<div class="textoDestacado">
					<?= $actividad['textoDestacado'] ?>
				</div>
				<?php if ( count($actividad['docentes']) ) : ?>
				<div class="docentes">
					Docente<?= count($actividad['docentes']) > 1 ? 's' : '' ?>:
					<?php foreach ($actividad['docentes'] as $key => $docente ) : ?>
					<a href="<?= $docente['url'] ?>"><?= $docente['title'] ?></a><?= $key != count($actividad['docentes']) - 1 ? ', ' : '' ?>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php if ( $actividad['frecuencia'] ) : ?>
				<div class="frecuencia">
					<strong>Frecuencia</strong>: <?= $actividad['frecuencia'] ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
		
		<?php if ( $actividad['puedeInscribirse'] ) :  ?>
			<div class="pagar <?= $actividad['hayDescuento'] ? 'full' : '' ?>">
				<div class="left">
					<ul>
						<?php foreach( $actividad['precios'] as $nombre => $precio ) : ?>
						<li><div class="desc"><?= $nombre ?></div>  <div class="precio"><?= $precio ?></div></li>
						<?php endforeach; ?>
					</ul>
					
					<?php 
						$url = $this->uri->segment(2);
						$textoInscripcion = intval($actividad['precio'] > 0) ? 'INSCRIBIRME Y SEÑAR/PAGAR' : 'Inscribirme';
					?>

					<?php if ( !$this->siteuser->isLogged() ) : ?>
						<a href="#" class="inscribirse nolog" rel="inscripciones/<?= $url ?>"><?= $textoInscripcion ?></a>
					<?php else : ?>		
						<a href="inscripciones/<?= $url ?>" class="inscribirse"><?= $textoInscripcion ?></a>
					<?php endif; ?>
				</div>
				<?php if ( $actividad['hayDescuento'] ) : ?>
				<div class="right">
					Obtenés un 20% de descuento en esta actividad, si sos:
					<ul>
						<li>Estudiante menor de 25 años</li>
						<li>Jubilado / pensionado</li>
						<li>Segundo integrante del núcleo familiar que realiza la misma actividad en la misma fecha (Padre-esposo, Madre-esposa, hija/o-hermana/o)</li>
					</ul>
					<em>Este descuento no es combinable con ningún otro.</em>
				</div>
				<?php endif; ?>
				<div class="clear"></div>
			</div>
		<?php endif; ?>
		
		<div class="clear"></div>
	</div>
	<div class="contenido">
		<?= $actividad['cuerpo'] ?>
	</div>
	<?php magico_setEditable($actividad['id'], 'ActividadEspecial', 'textoDestacado', 'section#actividad_especial .top .text .textoDestacado');  ?>
	<?php magico_setEditable($actividad['id'], 'ActividadEspecial', 'cuerpo', 'section#actividad_especial .contenido');  ?>
	<?php magico_setMainData($actividad['id'], 'ActividadEspecial')  ?>
</section>