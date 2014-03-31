<?php if ( count($arrActividades) > 0 ) : ?>
	<?php foreach( $arrActividades as $actividad ) : ?>
	<li>
		<a href="<?= $actividad['url'] ?>"><?= $actividad['title'] ?></a><br />
		<?= $actividad['fecha'] ?> | <?= $actividad['lugar'] ?>
		
		<?php // foreach ( $actividad['docentes'] as $key => $docente ) : ?>
		<!--<a href="<?= $docente['url'] ?>"><?= $docente['title'] ?></a><?= $key != count($actividad['docentes']) - 1 ? ', ' : '' ?>-->
		<?php // endforeach; ?>
	</li>
	<?php endforeach; ?>
<?php endif; ?>
