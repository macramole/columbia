<?php if ( count($arrActividades) > 0 ) : ?>
	<?php foreach( $arrActividades as $actividad ) : ?>
	<li>
		<strong><?= $actividad['dias'] ?></strong> | <?= $actividad['horaDesde'] ?> hs. | <?= $actividad['sala'] ?><!--<br />
		A cargo de 
		
		<?php //foreach ( $actividad['docentes'] as $key => $docente ) : ?>
		<a href="<?= $docente['url'] ?>"><?= $docente['title'] ?></a><?= $key != count($actividad['docentes']) - 1 ? ', ' : '' ?>
		<?php //endforeach; ?>-->
	</li>
	<?php endforeach; ?>
<?php else : ?>
	<li>No hay actividades</li>
<?php endif; ?>
