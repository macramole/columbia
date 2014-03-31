<section id="agenda_regular" class="agenda" >
	<h1>Agenda regular</h1>
	<div class="textoAgenda">
		<?= $textoAgenda['textoDestacado'] ?>
	</div>
	<?php magico_setEditable($textoAgenda['id'], 'Estatica', 'textoDestacado', 'section#agenda_regular .textoAgenda') ?>
	<div class="menu">
		<ul class="filtros">
			<?php foreach ( $puertas as $puerta ) : ?>
			<li class="<?= $puerta['cssClass'] ?>">
				<input type="checkbox" value="<?= $puerta['id'] ?>" checked />
				<?= $puerta['title'] ?>
			</li>
			<?php endforeach; ?>
		</ul>
		<div class="clear"></div>
	</div>
	<table class="stickyHeader">
		<thead>
			<tr>
				<th class="null"></th>
				<th>lunes</th>
				<th>martes</th>
				<th>miércoles</th>
				<th>jueves</th>
				<th>viernes</th>
				<th>sábado</th>
				<th>domingo</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $horarios as $hora => $horario ) : ?>
			<tr>
				<td class="hora"><?= $hora ?></td>
				<?php foreach( $horario as $dia ) : ?>
				<td>
					<?php foreach( $dia as $actividad ) : ?>
					<a href="<?= $actividad['url'] ?>" class="<?= $actividad['cssClass'] ?>" rel="<?= $actividad['idPuerta'] ?>"><?= $actividad['disciplina'] ?></a>
					<?php endforeach; ?>
				</td>
				<?php endforeach; ?>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
	<?php magico_setData($arrHorariosId, 'Actividad', 'section#agenda_regular table td a') ?>
	<a href="agenda/mensual" class="otra_agenda">Agenda mensual</a>
	
	<div class="share">
		<!-- AddThis Button BEGIN -->
		<div class="addthis_toolbox addthis_default_style addthis_20x20_style">
			<a class="addthis_button_print"></a>
			<a class="addthis_button_email"></a>
			<a class="addthis_button_facebook"></a>
			<a class="addthis_button_twitter"></a>
			<!--<a class="addthis_button_google_plusone_badge"  g:plusone:size="smallbadge" ></a>-->
			<a class="addthis_counter addthis_bubble_style"></a>
		</div>
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-515c571113912377"></script>
		<!-- AddThis Button END -->
	</div>
</section>