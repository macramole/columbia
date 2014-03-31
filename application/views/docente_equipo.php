<section id="docente_equipo">
	<h1><?= $tipo ?></h1>
	<a href="<?= $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : site_url( $tipo == 'Docentes' ? 'docentes' : 'informacion/equipo' ) ?>" class="back">&lt; volver</a>
	<div class="clear"></div>
	<div class="left">
		<img class="foto" src="<?= $equipo['imagen'] ?>" />
		<?php if ( $tipo == 'Docentes') : ?>
			
			<?php if ( count($equipo['actividades']) ) : ?>
			<div class="actividades">
				<h3>Actividades regulares:</h3>
				<ul class="regulares">
					<?php foreach ( $equipo['actividades'] as $actividad ) : ?>
					<li>
						<a href="<?= $actividad['url'] ?>" class="title"><?= $actividad['disciplina'] ?></a>
						<p><?= $actividad['sala'] ?> | <?= $actividad['dias'] ?> de <?= $actividad['horaDesde'] ?> hs a <?= $actividad['horaHasta'] ?> hs</p>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php magico_setData($equipo['actividades'], 'Actividad', 'section#docente_equipo .actividades ul.regulares li') ?>
			<?php endif; ?>
			<?php if ( count($equipo['actividades_especiales']) ) : ?>
			<div class="actividades">
				<h3>Actividades especiales:</h3>
				<ul class="especiales">
					<?php foreach ( $equipo['actividades_especiales'] as $actividad ) : ?>
					<li>
						<a href="<?= $actividad['url'] ?>" class="title"><?= $actividad['title'] ?></a>
						<p><?= $actividad['lugar'] ?> | <?= $actividad['fecha'] ?></p>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php magico_setData($equipo['actividades_especiales'], 'ActividadEspecial', 'section#docente_equipo .actividades ul.especiales li') ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="right">
		<h2><?= $equipo['title'] ?></h2>
		<div class="bio">
			<?= $equipo['texto'] ?>
		</div>
	</div>
	<?php magico_setEditable($equipo['id'], $content_type, 'texto', 'section#docente_equipo .bio');  ?>
	<?php magico_setMainData($equipo['id'], $content_type)  ?>
</section>