<section id="agenda_especial" class="agenda" >
	<h1>Agenda mensual</h1>
	<div class="textoAgenda">
		<?= $textoAgenda['textoDestacado'] ?>
	</div>
	<?php magico_setEditable($textoAgenda['id'], 'Estatica', 'textoDestacado', 'section#agenda_especial .textoAgenda') ?>
	<div class="menu">
		<ul class="meses">
			<?php $i = 0; ?>
			<?php foreach ($calendarios as $mes => $calendario) : ?>
			<li class="<?= $i == 0 ? 'active' : '' ?>"><a href="#" rel="<?= $mes ?>"><?= $mes ?></a></li>
			<?php if ( $i < $cantMeses - 1 ) : ?>
			<li class="separador">·</li>
			<?php endif;?>
			<?php $i++; ?>
			<?php endforeach; ?>
		</ul>
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
	<div class="agendaWrapper">
		<div class="agenda">
			<?php foreach ($calendarios as $mes => $calendario) : ?>
			<table rel="<?= $mes ?>" class="stickyHeader">
				<thead>
					<tr>
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
				<tr>
				<?php $i = 1; ?>
				<?php foreach( $calendario as $numeroDia => $dia ) : ?>
					<td>
						<div class="zoomWrapper">
							<div class="wrapper">
								<span class="numero <?= strftime('%B', strtotime($numeroDia)) != $mes ? 'viejo' : '' ?>"><?= date('j', strtotime($numeroDia) ) ?></span>
								<ul rel="<?= $numeroDia ?>">
								<?php foreach( $dia as $actividad ) : ?>
									<li>
										<a href="<?= $actividad['url'] ?>" class="<?= $actividad['cssClass'] ?>" rel="<?= $actividad['idPuerta'] ?>">
											<strong><?= $actividad['horaDesde'] ?> hs.</strong>
											<?= $actividad['title'] ?>
										</a>
									</li>
								<?php endforeach; ?>
								</ul>
								<?php magico_setData($dia, 'ActividadEspecial', "section#agenda_especial .agenda ul[rel=\"$numeroDia\"] li") ?>
							</div>
							<?php if ( count($dia) > 1 ) : ?>
							<a href="#" class="expandir">expandir </a>
							<?php endif ?>
						</div>
					</td>
					<?php
						if ( $i % 7 == 0 )
							echo '</tr><tr>';

						$i++;
					?>
				<?php endforeach ?>
				</tr>
				</tbody>
			</table>
			<?php endforeach; ?>
			<div class="clear"></div>
		</div>
	</div>
	
	
	<a href="agenda/regular" class="otra_agenda">Agenda regular</a>
	
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