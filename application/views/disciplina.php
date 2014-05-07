<section id="disciplina" class="<?= $disciplina['puertaCss'] ?>">
	<h1><?= $disciplina['puerta'] ?>: <?= $disciplina['title'] ?></h1>
	<div class="top">
		<img src="<?= $disciplina['imagen'] ?>" />
		<div class="text">
			<?= $disciplina['textoDestacado'] ?>
		</div>
		<div class="clear"></div>
	</div>
	<div class="contenido">
		<div class="left">
			<?= $disciplina['texto'] ?>
		</div>
		<div class="right">
			<?php if ( count($disciplina['horarios']) ) : ?>
				<h2>Horarios</h2>
				<?php foreach( $disciplina['horarios'] as $frecuencia => $horariosRegulares ) : ?>
					
					<h3>Frecuencia <?= $frecuencia ?></h3>
					
					<ul class="horarios regulares" rel="<?= $frecuencia ?>">
						<?php foreach( $horariosRegulares as $horario ) : ?>
						<li>
							<p><?= $horario['dias'] ?> de <?= $horario['horaDesde'] ?> hs hasta <?= $horario['horaHasta'] ?> hs.</p>

							<?php if ( count($horario['docentes']) ) : ?>
							<p class="docente">
								Docente<?= count($horario['docentes']) > 1 ? 's' : '' ?>: 
								<?php foreach ( $horario['docentes'] as $key => $docente ) : ?>
								<a href="<?= $docente['url'] ?>"><?= $docente['title'] ?></a><?= $key != count($horario['docentes']) - 1 ? ', ' : '' ?>
								<?php endforeach; ?>
							</p>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>
					
					<?php magico_setData($horariosRegulares, 'Actividad', "section#disciplina ul.horarios.regulares[rel=\"$frecuencia\"] li") ?>
				
				<?php endforeach; ?>
			
			<?php endif; ?>
			
			<?php if ( strip_tags( $disciplina['requisitos'] ) ) : ?>
			<h2>Requisitos</h2>
			<div class="requisitos">
				<?= $disciplina['requisitos'] ?>
			</div>
			<?php endif; ?>
			<?php if ( count($disciplina['horarios']) ) : ?>
				<a href="javascript:void(0)" class="inscribirse precios">Arancel y descuentos</a>
				<div class="preciosInfo">
					<?php if ( $disciplina['arancelYDescuentos'] ) : ?>
						<div class="arancelYDescuentosWrapper" <?php if ( $disciplina['gratis'] ) : ?>style="margin-bottom: 0"<?php endif;?>>
							<?= $disciplina['arancelYDescuentos'] ?>
						</div>

						<?php magico_setEditable($disciplina['id'], 'Disciplina', 'arancelYDescuentos', 'section#disciplina .arancelYDescuentosWrapper') ?>
					<?php endif;?>
                    
                    <?php if ( !$disciplina['gratis'] ) : ?>
                        <?php foreach( $disciplina['precios'] as $nombreFrecuencia => $frecuencia ) : ?>
                        <h4>Frecuencia <?= $nombreFrecuencia ?>:</h4>
                        <ul>
                            <?php foreach( $frecuencia as $precio ) : ?>
                            <li><div class="desc"><?= $precio['desc'] ?></div>  <div class="precio"><?= $precio['precio'] ?></div></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endforeach; ?>

                        <div class="clear"></div>

                        <div class="descuento">
                            Obtenés un 20% de descuento en TODAS nuestras actividades, si sos:
                            <ul>
                                <li>Estudiante menor de 25 años</li>
                                <li>Jubilado / pensionado</li>
                                <li>Segundo integrante del núcleo familiar que realiza la misma actividad en la misma fecha (Padre-esposo, Madre-esposa, hija/o-hermana/o)</li>
                            </ul>
                            <small>Excepto los siguientes cursos: La Senda del Chamán, Sanación Energética,  
                                Formación Vortex Healing, Meditación de la Conciencia P.U.R.A. Este descuento no es combinable con ningún otro.</small>
                        </div>
                    <?php endif; ?>
					
					<!--<a href="#" class="descarga" target="blank">
						<img src="images/pdf_download.jpg" />
						<div>
							Descargar PDF con <br />
							descuentos y promociones
						</div>
					</a>-->
				</div>
				
				<?php $url = $this->uri->segment(2) . '/' . $this->uri->segment(3) ?>
			
				<?php if ( !$this->siteuser->isLogged() ) : ?>
					<a href="<?= current_url() ?>#" rel="inscripciones/<?= $url ?>" class="inscribirse nolog">Reservá tu lugar</a>
				<?php else : ?>		
					<a href="inscripciones/<?= $url ?>" class="inscribirse">Reservá tu lugar</a>
				<?php endif; ?>
			<?php endif; ?>
					
			<?php if ( count($disciplina['actividades_especiales']) ) : ?>
			<h2 class="actividades_especiales">Cursos/Talleres Mensuales</h2>
			<ul class="horarios especiales">
				<?php foreach( $disciplina['actividades_especiales'] as $horario ) : ?>
				<li>
					<p class="title"><a href="<?= $horario['url'] ?>"><?= $horario['title'] ?></a></p>
					<p><?= $horario['fecha'] ?></p>
					<?php if ( count($horario['docentes']) ) : ?>
						<p class="docente">
							Docente<?= count($horario['docentes']) > 1 ? 's' : '' ?>: 
							<?php foreach ( $horario['docentes'] as $key => $docente ) : ?>
							<a href="<?= $docente['url'] ?>"><?= $docente['title'] ?></a><?= $key != count($horario['docentes']) - 1 ? ', ' : '' ?>
							<?php endforeach; ?>
						</p>
					<?php endif; ?>
						
					<?php if ( $horario['puedeInscribirse'] ) : ?>	
						<?php $url = substr( $horario['url'], strrpos($horario['url'], '/') + 1 ); ?>
						
						<a href="agenda/<?= $url ?>" class="inscribirse inscribirseEspecial">Arancel y descuentos</a>
						
						<?php if ( !$this->siteuser->isLogged() ) : ?>
							<a href="#" class="inscribirse inscribirseEspecial nolog" rel="inscripciones/<?= $url ?>">INSCRIBIRME Y SEÑAR/PAGAR</a>
						<?php else : ?>		
							<a href="inscripciones/<?= $url ?>" class="inscribirse inscribirseEspecial">INSCRIBIRME Y SEÑAR/PAGAR</a>
						<?php endif; ?>
					<?php endif; ?>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
			
			<div class="share">
				<h2>Compartí esta actividad</h2>
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_default_style addthis_20x20_style">
					<a class="addthis_button_email"></a>
					<a class="addthis_button_facebook"></a>
					<a class="addthis_button_twitter"></a>
					<!--<a class="addthis_button_google_plusone_badge"  g:plusone:size="smallbadge" ></a>-->
					<a class="addthis_counter addthis_bubble_style"></a>
				</div>
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-515c571113912377"></script>
				<!-- AddThis Button END -->
			</div>
		</div>
	</div>
	<?php magico_setEditable($disciplina['id'], 'Disciplina', 'textoDestacado', 'section#disciplina .top .text');  ?>
	<?php magico_setEditable($disciplina['id'], 'Disciplina', 'texto', 'section#disciplina .contenido .left');  ?>
	<?php magico_setEditable($disciplina['id'], 'Disciplina', 'requisitos', 'section#disciplina .contenido .right .requisitos');  ?>
	<?php magico_setMainData($disciplina['id'], 'Disciplina')  ?>
	<?php magico_setData($disciplina['actividades_especiales'], 'ActividadEspecial', 'section#disciplina ul.horarios.especiales li') ?>
</section>