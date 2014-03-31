<section id="inscripcion">
	<h1>Inscripci&oacute;n</h1>
	<p>
		<strong><?= $this->siteuser->getUserData('nombre') . ' ' . $this->siteuser->getUserData('apellido') ?></strong>
		para inscribirse a la actividad es necesario que complete el siguiente formulario:
	</p>
	<form>
		<table class="inscripcion_especial <?= !$actividad['hayVacante'] || $actividad['yaAnotado'] ? 'noVacante' : '' ?>" >
			<tr class="pad">
				<th></th>
				<td>
					<?php if ( !$actividad['hayVacante'] ) : ?>
					<p>Lo sentimos, no hay vacantes disponibles para <?= $actividad['title'] ?></p>
					<?php endif; ?>
					<?php if ( $actividad['yaAnotado'] ) : ?>
					<p>Ya está anotado en esta actividad</p>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th>Actividad:</th>
				<td class="actividad"><a href="<?= magico_urlclean('disciplinas_actividades_especiales', $actividad['id']) ?>" target="_blank"><?= $actividad['title'] ?></a></td>
			</tr>
			<?php if ( count($actividad['docentes']) ) : ?>
			<tr>
				<th>Docente<?= count($actividad['docentes']) > 1 ? 's' : '' ?>:</th>
				<td>
					<?php foreach( $actividad['docentes'] as $key => $docente ) : ?>
					<a href="<?= $docente['url'] ?>" target="_blank"><?= $docente['title'] ?></a><?= $key != count($actividad['docentes']) - 1 ? ', ' : '' ?>
					<?php endforeach; ?>
				</td>
			</tr>	
			<?php endif; ?>
			<tr>
				<th>Fecha de inicio:</th>
				<td><?= $actividad['fecha'] ?></td>
			</tr>
			<?php if ( $actividad['hayDescuento'] ) : ?>
			<tr>
				<th>Descuentos especiales:</th>
				<td class="descuentosEspeciales">
					<ul>
						<li>
							<input type="radio" name="descuentoPoblacional" checked="checked" value="0" />
							<label>Ninguno</label>
						</li>
						<li>
							<input type="radio" name="descuentoPoblacional" value="1" />
							<label>Estudiante menor de 25 años</label>
						</li>
						<li>
							<input type="radio" name="descuentoPoblacional" value="2" />
							<label>Jubilado o pensionado</label>
						</li>
						<li>
							<input type="radio" name="descuentoPoblacional" value="3" />
							<label>
								Segundo integrante del núcleo familiar que realiza la misma actividad en la misma fecha
								(Padre-esposo, Madre-esposa, hija/o-hermana/o)
							</label>
						</li>
					</ul>
					
					<span class="noCombinable"><?= $configuracion['descuentoPoblacional'] ?>% de descuento no combinable con ningún otro.</span>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th>Monto <?= $actividad['modalidadPago'] == 'Monto fijo' ? 'total' : 'mensual' ?> a pagar:</th>
				<td class="plata">
						$<span id="plata" rel="<?= $actividad['precio'] ?>"><?= $actividad['precio'] ?></span>
						
						<?php if ( $actividad['precioDescuento1'] || $actividad['precioDescuento2'] ) : ?>
							<div class="beneficios">
								Beneficios:
								<br />
								<?php if ( $actividad['precioDescuento1'] ) : ?>
									<span class="<?= !$actividad['aplicaDescuento1'] ? 'no' : '' ?>">
										<strong>$<?= $actividad['precioDescuento1'] ?></strong> 
										pagando antes del <?= $actividad['fechaDescuento1'] ?>
									</span>
									<?php if ( $actividad['precioDescuento2'] ) : ?>
									<br />
									<span class="<?= !$actividad['aplicaDescuento2'] ? 'no' : '' ?>">
										<strong>$<?= $actividad['precioDescuento2'] ?></strong> 
										pagando antes del <?= $actividad['fechaDescuento2'] ?>
									</span>
									<?php endif; ?>
									<br />
									<span class="<?= $actividad['aplicaDescuento2'] || $actividad['aplicaDescuento1'] ? 'no' : '' ?>">
										<strong id="precioReal">$<?= $actividad['precioReal'] ?></strong> 
										pagando después del <?= $actividad['precioDescuento2'] ? $actividad['fechaDescuento2'] : $actividad['fechaDescuento1'] ?>
									</span>
									<br /><br />
								<?php endif; ?>
							</div>
						<?php endif; ?>
				</td>
			</tr>
			<?php if ( $actividad['precioReserva'] ) : ?>
				<tr>
					<th>Valor de la seña <br/> para reservar vacante:</th>
					<td class="plata"><br/>$<?= $actividad['precioReserva'] ?></td>
				</tr>
			<?php endif; ?>
			<!--<tr>
				<th>Condiciones de uso:</th>
				<td>
					De esta manera usted está reservando su vacante en la actividad seleccionada. 
					La misma se hará efectiva el día que concurra a la sede de la Fundación Columbia previo a comenzar la actividad, 
					donde abone la suma estipulada. Por cualquier consulta administrativa comuníquese al 4341-4311. 
				</td>
			</tr>-->
			
			<?php if ( $actividad['hayVacante'] && !$actividad['yaAnotado'] && intval($actividad['precio']) > 0 ) : ?>
				<?php if ( $actividad['modalidadPago'] == 'Monto fijo' ) : ?>
					<?php if ( $actividad['mercadopago'] ) : ?>
						<tr>
							<th></th>
							<td>	
								<button type="button" class="pagar" id="btnInscribirsePagar">Inscribirse y pagar ahora</button>
								<img src="images/procesando_pago.gif" class="btnInscribirseLoading" />
							</td>
						</tr>
						<?php if ( $actividad['precioReserva'] ) : ?>
						<tr>
							<th></th>
							<td>
								<button type="button" class="pagar" id="btnVacante">Reservar y dejar una seña</button>
								<img src="images/procesando_pago.gif" class="btnInscribirseLoading" />
								<div class="costoServicio">Se le recargará un <?= $configuracion['comisionMercadoPago'] ?>% en concepto de costo del servicio. Pagos online disponibles hasta dos días antes del evento.</div>
							</td>
						</tr>
						<?php endif; ?>	
					<?php else : ?>
						<tr>
							<th></th>
							<td style="color: #912497;">	
								48hs antes del evento no podrás realizar más pagos online. <br /><br />
								Todavía tenés la posibilidad de escribirnos a <a href="mailto:info@fundacioncolumbia.org">info@fundacioncolumbia.org</a> y consultar por cupo y realizar el pago presencialmente.
								Muchas gracias.
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
			<?php elseif ( $actividad['hayVacante'] && !$actividad['yaAnotado'] && intval($actividad['precio']) == 0 ) : ?>
			<tr>
				<th></th>
				<td>
					<button type="button" id="btnInscribirse">Inscribirse</button>
					<img src="images/procesando_pago.gif" class="btnInscribirseLoading" />
					<!--<div class="costoServicio">
						De esta manera usted está demostrando especial interés en la actividad seleccionada. 
						La reserva sólo se hará efectiva el día que concurra a la sede de la Fundación Columbia previo a comenzar la actividad, 
						donde abone la reserva y/o la suma estipulada. Por cualquier consulta administrativa comuníquese al 4775-2172 y 4776-4462. 
					</div>-->
				</td>
			</tr>
			<?php endif; ?>
			
			
			<tr class="padBottom">
				<th></th>
				<td> </td>
			</tr>
		</table>
		
		<input type="hidden" name="id" value="<?= $actividad['id'] ?>" />
	</form>
</section>
<script>
	
	$( function() {	
		$('section#inscripcion #btnInscribirse, section#inscripcion #btnVacante, section#inscripcion #btnInscribirsePagar').click( function() {
			
			$this = $(this);
			$this.text('Procesando...').attr('disabled', true);
			$loading = $this.next();
			$loading.css('display', 'inline');
			$('section#inscripcion #btnInscribirse, section#inscripcion #btnVacante, section#inscripcion #btnInscribirsePagar').not($this).css('display','none');
			
			tipoInscripcion = 0;
			
			switch( $this.attr('id') )
			{
				case 'btnInscribirsePagar':
					tipoInscripcion = 0;
					break;
				case 'btnVacante':
					tipoInscripcion = 1;
					break;
				case 'btnInscribirse':
					tipoInscripcion = 2;
					break;
			}
			
			$.post(
				'inscripciones/inscribirse_especial/' + tipoInscripcion,
				$('section#inscripcion form').serializeArray(),
				function(data)
				{
					if ( data.status == 'ok' )
					{
						if ( !data.init_point )
							location.href = data.url;
						else
						{
							$('<a href="" name="MP-Checkout" mp-mode="modal" id="lnkPagar" onreturn="finPago">p</a>')
								.insertAfter($this)
								.attr('href', data.init_point);

							$LAB.script('http://mp-tools.mlstatic.com/buttons/render.js').wait(function(){
								$('section#inscripcion #lnkPagar')[0].click();
							});
						}
					}
					else
					{
						alert('Lo sentimos pero ocurrió un error. Por favor recargue la página e inténtelo nuevamente en unos minutos');
						$this.text('Inscribirse').attr('disabled', false);
						$loading.css('display', 'none');
					}
				},
				'json'
			);
			
		});
		
		
		$('section#inscripcion td.descuentosEspeciales input').click( function() {
			$this = $(this);
			precioReal = parseInt( $('section#inscripcion #precioReal').text().substr(1) );
			plata = precioReal > 0 ? precioReal : parseInt( $('#plata').attr('rel') );
			
			if (  parseInt($this.val()) > 0 )
			{
				plata = plata * ( 1 - <?= $configuracion['descuentoPoblacional'] ?> / 100 );
				$('section#inscripcion #plata').text( plata );
				$('section#inscripcion td.plata .beneficios').addClass('no');
			}
			else
			{	
				$('#plata').text( $('#plata').attr('rel') );
				$('section#inscripcion td.plata .beneficios').removeClass('no');
			}
		});
	});
	
	function finPago(json)
	{
		$('section#inscripcion #btnInscribirse, section#inscripcion #btnVacante').text('Redirigiendo...');
		
		if ( json.collection_status == null )
		{
			$.post(
				'inscripciones/inscripcionCancelada',
				{ 'external_reference' : json.external_reference },
				function() { 
					location.reload();	
				}
			);
		}
		else
			location.href = json.back_url;
	}
</script>
 <!--<script type="text/javascript" src="http://mp-tools.mlstatic.com/buttons/render.js"></script>-->
<!--<script type="text/javascript" src="js/LAB.min.js"></script>-->