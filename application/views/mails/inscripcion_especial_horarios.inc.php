<div class="big" style="color: #984a9e;margin: 45px 0 45px 40px">
	<div class="disciplina" style="font-size: 23px;font-weight: bold"><?= $actividad['title'] ?></div>
	<ul class="horarios" style="list-style: none;margin: 0;padding: 0;line-height: 30px;">
		<li style="font-size: 23px;margin: 0;padding: 0;"><?= $actividad['fecha'] ?></li>
	</ul>
	<?php if ( $actividad['frecuencia'] ) : ?>
	<div class="frecuencia">Frecuencia: <?= $actividad['frecuencia'] ?></div>
	<?php endif; ?>
</div>