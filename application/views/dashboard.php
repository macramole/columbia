<section id="dashboard">
	<div class="left">
		<h2>Actividades actuales</h2>
		
		<?php if ( count($horarios) || count($actividadesEspeciales) ) : ?>
		<ul class="actuales">
			<?php if ( count($horarios) ) : ?>
				<?php foreach( $horarios as $nombreDisciplina => $disciplina ) : ?>
				<li>
					<h3><?= $nombreDisciplina ?></h3>
					<?php foreach( $disciplina as $dia ) : ?>
						<p class="horarios"><?= $dia['dia'] ?> de <?= $dia['horaDesde'] ?> a <?= $dia['horaHasta'] ?> hs.</p>
						<?php if ( count($dia['docentes']) ) : ?>
							<p class="docente">
								<strong>Docente<?= count($dia['docentes']) > 1 ? 's' : '' ?>:</strong> 
								<?php foreach ( $dia['docentes'] as $key => $docente ) : ?>
								<a href="<?= $docente['url'] ?>"><?= $docente['title'] ?></a><?= $key != count($dia['docentes']) - 1 ? ', ' : '' ?>
								<?php endforeach; ?>
								<br/><em>Frecuencia <?= strtolower($dia['frecuencia']) ?></em>
							</p>
						<?php endif; ?>
					<?php endforeach; ?>
					
					<?php if ( !$disciplina[0]['activo'] ) : ?>
						<div class="pagoPendiente">Pago pendiente</div>
					<?php endif; ?>
						
					<?php if ( $this->adminuser->isLogged() ) : ?>
						<ul class="magico_pagos" rel="<?= $disciplina[0]['idDisciplina'] ?>" >
							<li><a href="<?= $disciplina[0]['disciplinaInscripcionURL'] ?>"><img src="images/backend/edit_24.png" title="Editar" /></a></li>
							<li class="eliminar"><a href="#"><img src="images/backend/delete_24.png" title="Eliminar" /></a></li>
							<li class="pagar">
								<a href="#"><img src="images/backend/pago_24.png" title="Marcar como pago" /></a>
								<form>
									<fieldset>
										<label>Desde:</label>
										<input class="input-text" type="text" name="desde" value="<?= $disciplina[0]['fechaDesde'] ? date('d/m/Y', strtotime($disciplina[0]['fechaDesde'])) : date('d/m/Y') ?>" />
										<label>Hasta:</label>
										<input class="input-text" type="text" name="hasta" value="<?= $disciplina[0]['fechaHasta'] ? date('d/m/Y', strtotime($disciplina[0]['fechaHasta'])) : '' ?>" />
										<input type="hidden" name="idDisciplina" value="<?= $disciplina[0]['idDisciplina'] ?>" />
									</fieldset>
									<button type="button" class="aceptar">Aceptar</button>
								</form>
							</li>
						</ul>
					<?php endif; ?>
						
					<div class="validez <?= !$disciplina[0]['fechaDesde'] ? 'no' : '' ?>">
						<strong>Vencimiento del pago</strong>: <span><?= date('d/m/Y', strtotime($disciplina[0]['fechaHasta'])) ?></span>
					</div>
					
				</li>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php foreach( $actividadesEspeciales as $actividad ) : ?>
			<li>
				<h3><?= $actividad['title'] ?></h3>
				<p class="horarios"><?= $actividad['fecha'] ?></p>
				<?php if ( count($actividad['docentes']) ) : ?>
					<p class="docente">
						<strong>Docente<?= count($actividad['docentes']) > 1 ? 's' : '' ?>:</strong> 
						<?php foreach ( $actividad['docentes'] as $key => $docente ) : ?>
						<a href="<?= $docente['url'] ?>"><?= $docente['title'] ?></a><?= $key != count($actividad['docentes']) - 1 ? ', ' : '' ?>
						<?php endforeach; ?>
					</p>
				<?php endif; ?>
				
				<?php 
					if ( !$actividad['activo'] || $actividad['pagoVacante'] ) 
						$pagoPendienteVisible = true;
				?>
				
				<div class="pagoPendiente <?= !$pagoPendienteVisible ? 'no' : '' ?> ">
					<?php if ( !$actividad['activo'] ) : ?>
						Pago pendiente
					<?php else : ?>
						Vacante reservada
					<?php endif; ?>
				</div>
				
				<?php if ( $this->adminuser->isLogged() ) : ?>
					<ul class="magico_pagos magico_pagos_especiales" rel="<?= $actividad['id'] ?>" >
						<li class="eliminar"><a href="#"><img src="images/backend/delete_24.png" title="Eliminar" /></a></li>
						<li class="pagar">
							<a href="#"><img src="images/backend/pago_24.png" title="Marcar como pago" /></a>
							<form>
								<fieldset>
									<label>Hasta:</label>
									<input class="input-text" type="text" name="hasta" value="<?= $actividad['fechaHastaRaw'] ? date('d/m/Y', strtotime($actividad['fechaHastaRaw'])) : '' ?>" />
									<input class="check" type="checkbox" name="soloVacante" value="1" <?= $actividad['pagoVacante'] ? 'checked="checked"' : '' ?> />
									<label class="checkLabel">Solo vacante</label>
									<input type="hidden" name="idActividad" value="<?= $actividad['id'] ?>" />
									<input type="hidden" name="modalidadPago" value="<?= $actividad['modalidadPago'] ?>" />
								</fieldset>
								<button type="button" class="aceptar">Aceptar</button>
							</form>
						</li>
					</ul>
				<?php endif; ?>
					
				<?php 
					if ( $actividad['activo'] && $actividad['modalidadPago'] == 'Mensual' && !$actividad['pagoVacante'] ) 
						$validezVisible = true;
					else
						$validezVisible = false;
				?>
					
				<div class="validez <?= !$validezVisible ? 'no' : '' ?> " >
					<strong>Vencimiento del pago</strong>: <span><?= date('d/m/Y', strtotime($actividad['pagoFechaHasta'])) ?></span>
				</div>
				
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
		<?php if ( !count($horarios) && !count($actividadesEspeciales) ) : ?>
			<div class="no">No estás inscripto en ninguna actividad</div>
		<?php endif; ?>
		<a href="usuarios/modificarDatos" class="modificarDatos">modificar mis datos</a>
		<!--<h3>Actividades realizadas</h3>
		<ul class="realizadas">
			<li>
				<h4>MeditaciÃ³n Vipassana</h4>
				<p class="horarios">Lunes, miÃ©rcoles y jueves de 10 a 12 hs.</p>
				<p class="docente"><strong>Docente:</strong> <a href="#">Mariano GarcÃ­a</a></p>
			</li>
			<li>
				<h4>MeditaciÃ³n Vipassana</h4>
				<p class="horarios">Lunes, miÃ©rcoles y jueves de 10 a 12 hs.</p>
				<p class="docente"><strong>Docente:</strong> <a href="#">Mariano GarcÃ­a</a></p>
			</li>
			<li>
				<h4>MeditaciÃ³n Vipassana</h4>
				<p class="horarios">Lunes, miÃ©rcoles y jueves de 10 a 12 hs.</p>
				<p class="docente"><strong>Docente:</strong> <a href="#">Mariano GarcÃ­a</a></p>
			</li>
		</ul>-->
	</div>
	<div class="right">
		<h2>Notificaciones</h2>
		<ul>
			<li>
				No tiene ninguna notificación nueva.
			</li>
		</ul>
		<!--<ul>
			<li>
				<h3>Recordatorio de pago</h3>
				<ul>
					<li>
						<p>
							Integer et ante id tortor feugiat pretium id a sapien. Ut sit amet nunc lectus, aliquet gravida sapien. 
							Duis bibendum magna quis odio venenatis venenatis. Aenean gravida magna vitae justo sollicitudin in convallis est aliquam. 
							Vivamus consequat eleifend lorem eget imperdiet.
						</p>
					</li>
				</ul>
			</li>
			<li>
				<h3>TÃ­tulo</h3>
				<ul>
					<li>
						<p>
							Integer et ante id tortor feugiat pretium id a sapien. Ut sit amet nunc lectus, aliquet gravida sapien. 
							Duis bibendum magna quis odio venenatis venenatis. Aenean gravida magna vitae justo sollicitudin in convallis est aliquam. 
							Vivamus consequat eleifend lorem eget imperdiet.
						</p>
					</li>
				</ul>
			</li>
			<li>
				<h3>TÃ­tulo</h3>
				<ul>
					<li>
						<p>
							Integer et ante id tortor feugiat pretium id a sapien. Ut sit amet nunc lectus, aliquet gravida sapien. 
							Duis bibendum magna quis odio venenatis venenatis. Aenean gravida magna vitae justo sollicitudin in convallis est aliquam. 
							Vivamus consequat eleifend lorem eget imperdiet.
						</p>
					</li>
				</ul>
			</li>
		</ul>-->
	</div>
</section>