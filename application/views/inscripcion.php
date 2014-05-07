<section id="inscripcion">
	<h1>Inscripción</h1>
	<p>
		<strong><?= $this->siteuser->getUserData('nombre') . ' ' . $this->siteuser->getUserData('apellido') ?></strong>
		para inscribirse a la actividad es necesario que complete el siguiente formulario:
	</p>
	<form>
		<input type="hidden" name='idDisciplina' value="<?= $disciplina['id'] ?>" />
		<table>
			<tr class="pad">
				<th></th>
				<td></td>
			</tr>
			<tr>
				<th>Actividad:</th>
				<td class="actividad"><a href="<?= magico_urlclean('disciplinas', $disciplina['id']) ?>" target="_blank"><?= $disciplina['title'] ?></a></td>
			</tr>
			<tr>
				<th>Días y horarios:</th>
				<td class="horarios">
					<?php $yaAnotado = false; ?>
					<?php foreach ( $disciplina['horarios'] as $frecuencia => $horarios ) : ?>
						<h3>Frecuencia <?= $frecuencia ?></h3>
						<ul>
							<?php foreach ( $horarios as $horario ) : ?>
							<li class="<?= !$horario['hayVacante'] || $horario['yaAnotado'] ? 'noVacante' : '' ?>">
								<?php
									$disabled = '';

									if ( !$this->adminuser->isLogged() )
									{
										if ( !$horario['hayVacante'] || $horario['yaAnotado'] )
											$disabled = 'disabled="disabled"';
									}
									else
									{
										if ( !$horario['hayVacante'] && !$horario['yaAnotado'] )
											$disabled = 'disabled="disabled"';
									}

									$checked = '';

									if (  $horario['yaAnotado'] )
									{
										if ( !$this->adminuser->isLogged() )
											$yaAnotado = true;

										$checked = 'checked="checked"';
									}

								?>

								<input 
									type="checkbox" 
									name="horario[]" 
									value="<?= $horario['idDia'] ?>"
									<?=  $disabled ?> <?=  $checked ?>
									rel="<?= $horario['precio'] ?>" />
								<div class="info">
									<p><?= $horario['dia'] ?> de <?= $horario['horaDesde'] ?> hs hasta <?= $horario['horaHasta'] ?> hs.</p>
									<p>
										Docente<?= count($horario['docentes']) > 1 ? 's' : '' ?>:
										<?php if ( count($horario['docentes']) ) : ?>
											<?php foreach ( $horario['docentes'] as $key => $docente ) : ?>
												<a href="<?= $docente['url'] ?>" target="_blank"><?= $docente['title'] ?></a><?= $key != count($horario['docentes']) - 1 ? ', ' : '' ?>
											<?php endforeach; ?>
										<?php endif; ?>
									</p>
									<?php if ( !$horario['hayVacante'] && !$horario['yaAnotado'] ) : ?>
									<p class="no">
										( no hay vacantes )
									</p>
									<?php endif; ?>
									<?php if ( $horario['yaAnotado'] ) : ?>
									<p class="no">
										( ya está inscripto )
									</p>
									<?php endif; ?>
								</div>
							</li>
							<?php endforeach; ?>
						</ul>
					<?php endforeach; ?>
				</td>
			</tr>
			<?php if (!$disciplina['gratis']) : ?>
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
				<th>Monto mensual a pagar:</th>
				<td class="plata">$<span id="plata">0</span></td>
			</tr>
			<tr>
				<th>Condiciones de uso:</th>
				<td>
					De esta manera usted está reservando su vacante en la actividad seleccionada. 
					La misma se hará efectiva el día que concurra a la sede de la Fundación Columbia previo a comenzar la actividad, 
					donde abone la suma estipulada. Por cualquier consulta administrativa comuníquese al 4341-4311. 
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<?php if (!$yaAnotado ) : ?>
					<button type="button" id="btnInscribirse">Inscribirse</button>
					<img src="images/procesando_pago.gif" class="btnInscribirseLoading" />
					<?php else : ?>
					<br>Ya estás inscripto
					<?php endif; ?>
				</td>
			</tr>
			<tr class="padBottom">
				<th></th>
				<td></td>
			</tr>
		</table>
	</form>
</section>
<script>
	var precios = [ <?= $configuracion['precioActividadesRegulares'] ?>, <?= $configuracion['descuento2dias'] ?>, <?= $configuracion['descuento3dias'] ?>, <?= $configuracion['descuento4dias'] ?> ];
	var numDescuento = 0;
	
	$( function() {
		$('section#inscripcion .horarios input').change(function(){
			precioFinal = 0;
			numDescuento = 0;
			$('section#inscripcion .horarios input').each(function(){
				if ( $(this).is(':checked') )
				{
					if ( !$(this).attr('rel') || $(this).attr('rel') == '0' )
					{
						precioFinal += precios[numDescuento];
						numDescuento++;

						if ( numDescuento == precios.length )
							numDescuento = precios.length - 1;
					}
					else
					{
						precioFinal += parseInt($(this).attr('rel'));
					}
					
				}
					
			});
			
			if ( parseInt( $('section#inscripcion td.descuentosEspeciales input:checked').val() ) > 0 )
				precioFinal = precioFinal * ( 1 - <?= $configuracion['descuentoPoblacional'] ?> / 100 );
			
			$('#plata').text(Math.ceil(precioFinal));
		});
		
		$('section#inscripcion td.descuentosEspeciales input').click( function() {
			$('section#inscripcion .horarios input').change();
		});
		
		$('section#inscripcion #btnInscribirse').click( function() {
			if ( $('section#inscripcion .horarios input:checked').length == 0 )
				alert('Debe seleccionar al menos un día');
			else
			{
				$this = $(this);
				$this.text('Procesando...').attr('disabled', true);
				$('.btnInscribirseLoading').css('display', 'inline');
				
				$.post(
					'inscripciones/inscribirse_regular',
					$('section#inscripcion form').serialize(),
					function(data)
					{
						if ( data.status == 'ok' )
						{
							location.href = data.url;
							
							/*$('<a href="" name="MP-Checkout" mp-mode="modal" id="lnkPagar" onreturn="finPago">p</a>')
								.insertAfter($this)
								.attr('href', data.init_point);
								
							$LAB.script('http://mp-tools.mlstatic.com/buttons/render.js').wait(function(){
								$('section#inscripcion #lnkPagar')[0].click();
							});*/
						}
						else
						{
							alert('Lo sentimos pero ocurrió un error. Por favor recargue la página e inténtelo nuevamente en unos minutos');
							$this.text('Inscribirse').attr('disabled', false);
							$('#btnInscribirseLoading').css('display', 'none');
						}
					},
					'json'
				);
			}
		});
		
		$('section#inscripcion .horarios input').change();
	});
	/*
	function finPago(json)
	{
		$('section#inscripcion #btnInscribirse').text('Redirigiendo...');
		
		if ( json.collection_status == null )
		{
			$.post(
				'inscripciones/inscripcionCancelada',
				{ 'external_reference' : json.external_reference },
				function() { location.reload();	}
			);
		}
		else
			location.href = json.back_url;
	}*/
</script>
 <!--<script type="text/javascript" src="http://mp-tools.mlstatic.com/buttons/render.js"></script>-->
<!--<script type="text/javascript" src="js/LAB.min.js"></script>-->