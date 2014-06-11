<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class Inscripciones extends MasterControllerColumbia
{	
	const INSCRIPCION_TIPO_PAGAR = 0;
	const INSCRIPCION_TIPO_RESERVAR = 1;
	const INSCRIPCION_TIPO_NO_PAGAR = 2;
	
	public function index()
	{
		redirect('puertas-de-entrada');
	}
	
	private function _checkLogged()
	{
		if ( !$this->siteuser->isLogged() )
		{
			redirect('puertas-de-entrada');
			exit;
		}
			
	}
	
	public function aprobado()
	{
		$this->setTitle( 'Inscripción exitosa' );
		$this->addContentPage('inscripcion_approved');
		$this->show();
	}
	
	public function pago_aprobado()
	{
		$this->setTitle( 'Inscripción exitosa' );
		$this->addContentPage('inscripcion_pago_approved');
		$this->show();
	}
	
	public function pago_pendiente()
	{
		$this->setTitle( 'Inscripción: Pago pendiente' );
		$this->addContentPage('inscripcion_pago_pending');
		$this->show();
	}
	/*public function revisado()
	{
		$this->setTitle( 'Inscripción: Pago en proceso' );
		$this->addContentPage('inscripcion_in_process');
		$this->show();
	}*/
	public function pago_rechazado()
	{
		$this->setTitle( 'Inscripción: Pago rechazado' );
		$this->addContentPage('inscripcion_pago_rejected');
		$this->show();
	}
	
	public function inscripcion($puerta, $disciplina = null)
	{
		$this->_checkLogged();
		
		if ( $puerta && $disciplina )
			$this->inscripcion_regular($puerta, $disciplina);
		else
			$this->inscripcion_especial($puerta);
	}
	
	private function inscripcion_regular($puerta, $disciplina)
	{
		$disciplina = magico_getByUrlClean("puertas-de-entrada/$puerta/$disciplina");
		
		if ( $disciplina )
		{
			$sqlHorarios = "
				SELECT
					da.id AS idActividad,	
					dad.id AS idDia,
					TIME_FORMAT(da.`horaDesde`,'%H:%i') AS horaDesde, 
					TIME_FORMAT(da.`horaHasta`,'%H:%i') AS horaHasta, 
					d.title AS dia,
					f.title as frecuencia,
					da.precio as precio,
					IF ( 
						da.vacantes - 
						( 
							SELECT 
								COUNT(*) 
							FROM 
								`alumnos_actividades_dias` aad 
							WHERE 
								aad.idDisciplinaActividadDia = dad.id AND 
								aad.vacante = 1
						) > 0, 1, 0) AS hayVacante,
					( 
						SELECT 
							id
						FROM 
							`alumnos_actividades_dias` aad 
						WHERE 
							aad.idDisciplinaActividadDia = dad.id AND 
							aad.vacante = 1 AND aad.idAlumno = ?
						LIMIT 1
					) IS NOT NULL AS yaAnotado
				FROM
					`disciplinas_actividades` da
				INNER JOIN
					`disciplinas_actividades_dias` dad ON
					dad.`idActividad` = da.`id`
				INNER JOIN
					dias d ON
					d.`id` = dad.`idDia`
				INNER JOIN
					frecuencias f ON
					f.id = da.idFrecuencia
				WHERE
					da.`idDisciplina` = ?
				ORDER BY
					dad.`idDia`, da.`horaDesde`
			";
			
			$arrHorarios = $this->db->query($sqlHorarios, array($this->siteuser->getUserData('id'),$disciplina['id']))->result_array();
			
			if ( count($arrHorarios) )
			{
				$sqlDocentes = "
					SELECT
						dad.`idActividad` ,
						d.`title`,
						cu.`url`
					FROM
						`disciplinas_actividades_docentes` dad
					INNER JOIN
						`docentes` d ON
						d.id = dad.`idDocente`
					INNER JOIN
						`disciplinas_actividades` da ON
						da.`id` = dad.`idActividad` AND da.`idDisciplina` = ?
					INNER JOIN
						`clean_urls` cu ON
						cu.`node_id` = d.`id` AND cu.`table` = 'docentes'
				";

				$docentes = $this->db->query($sqlDocentes, array($disciplina['id']))->result_array();

				foreach ( $docentes as &$docente )
				{
					foreach ( $arrHorarios as &$horario )
					{
						if ( $horario['idActividad'] == $docente['idActividad'] )
							$horario['docentes'][] = $docente;
					}
				}
				
				//los ordeno por frecuencia
				
				foreach ( $arrHorarios as &$horario )
				{
					if ( $disciplina['gratis'] )
                        $horario['precio'] = 0;
                    
                    $disciplina['horarios'][$horario['frecuencia']][] = $horario;
				}
					
				$configuracion = magico_getGlobalConfig();
                
                if ( !$disciplina['gratis'] )
                {
                    if ( intval($disciplina['precio']) )
                    {
                        $configuracion['precioActividadesRegulares'] = $disciplina['precio'];
                        $configuracion['descuento2dias'] = $disciplina['descuento2dias'];
                        $configuracion['descuento3dias'] = $disciplina['descuento3dias'];
                        $configuracion['descuento4dias'] = $disciplina['descuento4dias'];
                    }
                }
                else
                {
                    $configuracion['precioActividadesRegulares'] = 
                    $configuracion['descuento2dias'] = 
                    $configuracion['descuento3dias'] = 
                    $configuracion['descuento4dias'] = 0;
                }
					
				$this->setTitle( 'Inscripción : ' . $disciplina['title'] );
				$this->addContentPage('inscripcion', array('disciplina' => $disciplina, 'configuracion' => $configuracion));
				$this->show();
			}
			else
				redirect('puertas-de-entrada');
			
		}
		else
		{
			$this->index();
		}
	}
	
	private function inscripcion_especial($actividadEspecial)
	{
		$actividadEspecial = magico_getByUrlClean("agenda/$actividadEspecial");
		
		if ( 
			$actividadEspecial && 
			$actividadEspecial['vacantes'] > 0 && 
			strtotime($actividadEspecial['fecha'] . ' ' . $actividadEspecial['horaDesde'] ) > time()  )
		{
			$sqlDocentes = "
				SELECT
					d.`title`,
					cu.`url`
				FROM
					`disciplinas_actividades_especiales_docentes` dad
				INNER JOIN
					`docentes` d ON
					d.id = dad.`idDocente`
				INNER JOIN
					`disciplinas_actividades_especiales` da ON
					da.`id` = dad.`idActividadEspecial`
				INNER JOIN
					`clean_urls` cu ON
					cu.`node_id` = d.`id` AND cu.`table` = 'docentes'
				WHERE
					da.id = ?
			";
			
			$actividadEspecial['docentes'] = $this->db->query($sqlDocentes, array($actividadEspecial['id']))->result_array();
			
			$sqlCantInscriptos = "
				SELECT
					COUNT(*) AS cantInscriptos
				FROM
					`alumnos_actividades_especiales` aae
				WHERE
					aae.`idDisciplinaActividadEspecial` = ? AND
					aae.activo = 1
			";
			
			$cantInscriptos = $this->db->query($sqlCantInscriptos, array($actividadEspecial['id']))->row_array();
			$cantInscriptos = $cantInscriptos['cantInscriptos'];
			
			$actividadEspecial['hayVacante'] = $cantInscriptos < $actividadEspecial['vacantes'];
			
			$sqlYaAnotado = "
				SELECT
					COUNT(*) AS yaAnotado
				FROM
					`alumnos_actividades_especiales` aae
				WHERE
					aae.`idDisciplinaActividadEspecial` = ? AND
					aae.idAlumno = ?
			";
			
			$actividadEspecial['yaAnotado'] = $this->db->query($sqlYaAnotado, array($actividadEspecial['id'], $this->siteuser->getUserData('id')))->row_array();
			
			$actividadEspecial['yaAnotado'] = $actividadEspecial['yaAnotado']['yaAnotado'];
			
			
			
			/* DESCUENTOS */
			$configuracion = magico_getGlobalConfig();
			$time = strtotime($actividadEspecial['fecha']);
			$actividadEspecial['fechaDescuento1'] = strtotime("-$configuracion[descuentoAdelantado1dias] days", $time);
			$actividadEspecial['fechaDescuento2'] = strtotime("-$configuracion[descuentoAdelantado2dias] days", $time);
			
			$actividadEspecial['aplicaDescuento1'] = $actividadEspecial['aplicaDescuento2'] = $actividadEspecial['aplicaDescuento'] = false;
			$actividadEspecial['precioReal'] = $actividadEspecial['precio'];
			
			if ( $actividadEspecial['precioDescuento1'] && time() <= $actividadEspecial['fechaDescuento1'] )
			{
				$actividadEspecial['aplicaDescuento1'] = true;
				$actividadEspecial['aplicaDescuento'] = true;
				$actividadEspecial['precio'] = $actividadEspecial['precioDescuento1'];
			}
			elseif ( $actividadEspecial['precioDescuento2'] && time() <= $actividadEspecial['fechaDescuento2'] )
			{
				$actividadEspecial['aplicaDescuento2'] = true;
				$actividadEspecial['aplicaDescuento'] = true;
				$actividadEspecial['precio'] = $actividadEspecial['precioDescuento2'];
			}
			
			$actividadEspecial['fechaDescuento1'] = date('d/m/Y', $actividadEspecial['fechaDescuento1']);
			$actividadEspecial['fechaDescuento2'] = date('d/m/Y', $actividadEspecial['fechaDescuento2']);
			
			//Dos dias antes se desactiva
			if ( time() >= strtotime('-2 days', $time)  )
				$actividadEspecial['mercadopago'] = false;
			else
				$actividadEspecial['mercadopago'] = true;
			
			/** Ajusto fecha */
			magico_setLocale('es');
			$actividadEspecial['fecha'] = strftime('%A', $time);
			$actividadEspecial['fecha'] = ucfirst( utf8_encode($actividadEspecial['fecha']) );
			$actividadEspecial['fecha'] .= strftime(' %#d de %B', $time);
			$actividadEspecial['fecha'] .= ', ' . substr($actividadEspecial['horaDesde'],0,5) . ' hs';
			/** **/
			
			$this->setTitle( 'Inscripción : ' . $actividadEspecial['title'] );
			$this->addContentPage('inscripcion_especial', array('actividad' => $actividadEspecial, 'configuracion' => $configuracion));
			$this->show();
		}
		else
			$this->index();
	}
	
	public function inscribirse_especial($tipoInscripcion) //ajax
	{	
		$this->load->library('mp', array('client_id' => MP_CLIENT_ID, 'client_secret' => MP_CLIENT_SECRET) );
		
		if ($_POST['id'] )
		{
			$external_reference = null;
			
			if ( $tipoInscripcion != self::INSCRIPCION_TIPO_NO_PAGAR )
				$external_reference = MP_REFERENCE_ESPECIAL . md5( time() . $this->siteuser->getUserData('email') );
			
			$sqlActividad = "
				SELECT
					dae.*,
					IF ( dae.vacantes - ( 
						SELECT
							COUNT(*) AS cantInscriptos
						FROM
							`alumnos_actividades_especiales` aae
						WHERE
							aae.`idDisciplinaActividadEspecial` = dae.id AND
							aae.activo = 1
					) > 0, 1, 0) AS hayVacante
				FROM
					disciplinas_actividades_especiales dae
				WHERE
					id = ?
			";
			
			$actividad = $this->db->query($sqlActividad, array('id' => $_POST['id']))->row_array();
			
			
			if ( $actividad && $actividad['hayVacante'] )
			{
				if ( $tipoInscripcion != self::INSCRIPCION_TIPO_NO_PAGAR )
				{
					$configuracion = magico_getGlobalConfig();
					
					if ( $tipoInscripcion == self::INSCRIPCION_TIPO_PAGAR )
					{
						/* DESCUENTOS */
						if ( intval($_POST['descuentoPoblacional']) > 0 )
						{
							$actividad['precio'] *= 1 - ( floatval($configuracion['descuentoPoblacional']) / 100 );
						}
						else
						{
							$time = strtotime($actividad['fecha']);
							$actividad['fechaDescuento1'] = strtotime("-$configuracion[descuentoAdelantado1dias] days", $time);
							$actividad['fechaDescuento2'] = strtotime("-$configuracion[descuentoAdelantado2dias] days", $time);

							$actividad['aplicaDescuento'] = false;

							if ( $actividad['precioDescuento1'] && time() <= $actividad['fechaDescuento1'] )
							{
								$actividad['aplicaDescuento'] = true;
								$actividad['precio'] = $actividad['precioDescuento1'];
							}
							elseif ( $actividad['precioDescuento2'] && time() <= $actividad['fechaDescuento2'] )
							{
								$actividad['aplicaDescuento'] = true;
								$actividad['precio'] = $actividad['precioDescuento2'];
							}
						}

						$precioFinal = $actividad['precio'];
					}
					else
						$precioFinal = $actividad['precioReserva'];
					
					$precioFinal *= 1 + (floatval($configuracion['comisionMercadoPago']) / 100);
				}
				
				$activo = 0;
				
				if ( $precioFinal == 0 )
					$activo = 1;
				
				$this->db->insert('alumnos_actividades_especiales', array(
					'idAlumno' => $this->siteuser->getUserData('id'),
					'idDisciplinaActividadEspecial' => $actividad['id'],
					'MP_external_reference' => $external_reference,
					'activo' => $activo,
					'pagoVacante' => $tipoInscripcion == self::INSCRIPCION_TIPO_RESERVAR ? 1 : 0,
					'vacante' => 1
				));
				
				
				if ( $tipoInscripcion != self::INSCRIPCION_TIPO_NO_PAGAR )
				{
					$title = html_entity_decode($actividad['title'], ENT_COMPAT, 'UTF-8');
					
					if ( $tipoInscripcion == self::INSCRIPCION_TIPO_RESERVAR )
						$title = 'RESERVA ' . $title;
					
					if ( $actividad['aplicaDescuento'] )
						$title .= ' CON DESCUENTO ';
					
					
					
					//$precioFinal = floatval(1);
					
					
					
					$mp_preference = array(
						'items' => array(
							array(
								'title' => $title,
								'quantity' => 1,
								'currency_id' => 'ARS',
								'unit_price' => $precioFinal
							)
						),
						'payer' => array(
							'name' => $this->siteuser->getUserData('nombre'),
							'surname' => $this->siteuser->getUserData('apellido'),
							'email' => $this->siteuser->getUserData('email'),
						),
						'back_urls' => array(
							'success' => base_url('inscripciones/pago_aprobado'),
							'failure' => base_url('inscripciones/pago_rechazado'),
							'pending' => base_url('inscripciones/pago_pendiente')
						),
						'external_reference' => $external_reference
					);

					$mp_result = $this->mp->create_preference($mp_preference);
					
					echo json_encode( array( 'status' => 'ok', 'init_point' => $mp_result['response']['init_point'] ) );
				}
				else
				{
					$url = '';
					
					if ( !$this->adminuser->isLogged() )
					{
						$url = site_url('inscripciones/aprobado');
						$this->enviarEmailEspecial('inscripto', $actividad['id']);
					}
					else
					{
						$url = site_url('usuarios/panel');
					}

					echo json_encode( array( 'status' => 'ok', 'url' => $url ) );
				}
			}
			else
				echo json_encode(array('status' => 'error'));
		}
		else
			echo json_encode(array('status' => 'error'));
	}
	
	public function inscribirse_regular() //ajax
	{
		//$this->load->library('mp', array('client_id' => MP_CLIENT_ID, 'client_secret' => MP_CLIENT_SECRET) );
		
		if ( count($_POST['horario']) && $_POST['idDisciplina'] )
		{
			
			/**
			 * Si es el admin que esta modificando o anotando a alguien
			 * agarro la inscripción actual y la pongo en el archivo
			 *  
			 */
			$inscripcionActual = null;	
			if ( $this->adminuser->isLogged() )
			{
				$sqlInscripcionActual = "
					SELECT
						fechaDesde,
						fechaHasta,
						activo
					FROM
						alumnos_actividades_dias
					WHERE
						idAlumno = ? AND 
						`idDisciplinaActividadDia` IN ( 
							SELECT
								dad.id
							FROM
								`disciplinas_actividades_dias` dad
							INNER JOIN
								`disciplinas_actividades` da ON
								da.`id` = dad.`idActividad`
							WHERE
								da.`idDisciplina` = ?
						)
					ORDER BY
						activo DESC, fechaDesde DESC
					LIMIT 1

				";

				$inscripcionActual = $this->db->query($sqlInscripcionActual, array($this->siteuser->getUserData('id'), $_POST['idDisciplina']))->row_array();

				if ( $inscripcionActual )
				{
					//Elimino todos y los pongo en el archivo
					$sqlArchivo = "
						INSERT INTO
							`alumnos_actividades_dias_archivo` ( `idDisciplinaActividadDia`, `idAlumno`, `fechaDesde`, `fechaHasta`, `MP_external_reference` )
						( SELECT
							`idDisciplinaActividadDia`,
							idAlumno,
							fechaDesde,
							fechaHasta,
							MP_external_reference
						FROM
							alumnos_actividades_dias aad
						WHERE
							aad.`idDisciplinaActividadDia` IN
								(
									SELECT
										dad.id
									FROM
										`disciplinas_actividades_dias` dad
									INNER JOIN
										`disciplinas_actividades` da ON
										da.`id` = dad.`idActividad`
									WHERE
										da.`idDisciplina` = ?
								)
							AND
							aad.vacante = 1 AND aad.idAlumno = ? )
					";

					$this->db->query($sqlArchivo, array($_POST['idDisciplina'], $this->siteuser->getUserData('id')));

					$sqlDelete = "
						DELETE FROM
							alumnos_actividades_dias
						WHERE
							`idDisciplinaActividadDia` IN
								(
									SELECT
										dad.id
									FROM
										`disciplinas_actividades_dias` dad
									INNER JOIN
										`disciplinas_actividades` da ON
										da.`id` = dad.`idActividad`
									WHERE
										da.`idDisciplina` = ?
								)
							AND
							vacante = 1 AND idAlumno = ?
					";

					$this->db->query($sqlDelete, array($_POST['idDisciplina'], $this->siteuser->getUserData('id')));
				}
			}
			
			$arrIn = implode(',', $_POST['horario']);
			//$external_reference = MP_REFERENCE_REGULAR . md5( time() . $this->siteuser->getUserData('email') );
			
			$sqlHorarios = "
				SELECT
					d.`title` AS dia,
					TIME_FORMAT(da.`horaDesde`,'%H:%i') AS horaDesde,
					TIME_FORMAT(da.`horaHasta`,'%H:%i') AS horaHasta,
					dis.title AS disciplina,
					IF ( 
						da.vacantes - 
						( 
							SELECT 
								COUNT(*) 
							FROM 
								`alumnos_actividades_dias` aad 
							WHERE 
								aad.idDisciplinaActividadDia = dad.id AND 
								aad.activo = 1
						) > 0, 1, 0) AS hayVacante
				FROM
					`disciplinas_actividades_dias` dad
				INNER JOIN
					`disciplinas_actividades` da ON
					da.`id` = dad.`idActividad`
				INNER JOIN
					dias d ON
					d.`id` = dad.`idDia`
				INNER JOIN
					disciplinas dis ON
					dis.`id` = da.`idDisciplina`
				WHERE
					dad.`id` IN ($arrIn)
			";
			
			$arrHorarios = $this->db->query($sqlHorarios)->result_array();
			
			if ( count($arrHorarios) )
			{
				//Checkeo que las vacantes sigan bien por las dudas
				
				$hayVacante = true;
				foreach( $arrHorarios as $horario )
				{
					if ( !$horario['hayVacante'] ) 
					{
						$hayVacante = false;
						break;
					}
				}
				
				if ( $hayVacante )
				{
					$precioFinal = 0;
					$configuracion = magico_getGlobalConfig();
					$precios = array( 
						$configuracion['precioActividadesRegulares'], 
						$configuracion['descuento2dias'], 
						$configuracion['descuento3dias'], 
						$configuracion['descuento4dias']);
					$numDescuento = 0;

					//registro pago
					/*$this->db->insert('alumnos_pagos', array(
						'idAlumno' => $this->siteuser->getUserData('id'),
						'MP_external_reference' => $external_reference
					));*/
					
					//registro dias asociados
					
					if ( !$inscripcionActual ) {
						$inscripcionActual = array('fechaDesde' => null, 'fechaHasta' => null, 'activo' => 0);
                    }
                    
                    //Checkeo gratis
                    $disciplina = $this->db->get_where('disciplinas', array('id' => $_POST['idDisciplina']))->row_array();
                    if ( $disciplina['gratis'] )
                        $inscripcionActual['activo'] = 1;
					
					$insert_items = array();
					
					foreach ( $_POST['horario'] as $horario )
					{
						$precioFinal += $precios[$numDescuento];

						if ( $numDescuento <= count($precios) - 1 )
							$numDescuento++;

						$insert_items[] = array(
							'idDisciplinaActividadDia' => $horario,
							'idAlumno' => $this->siteuser->getUserData('id'),
							/*'MP_external_reference' => $external_reference,*/
							'activo' => $inscripcionActual['activo'],
							'fechaDesde' => $inscripcionActual['fechaDesde'],
							'fechaHasta' => $inscripcionActual['fechaHasta'],
							'vacante' => 1
						);
					}

					$this->db->insert_batch('alumnos_actividades_dias', $insert_items);
					
					
					

//					$descripcion = '';
//
//					foreach( $arrHorarios as $horario )
//					{
//						$descripcion .= "$horario[dia] de $horario[horaDesde] hasta $horario[horaHasta]. ";
//					}
//
//					$mp_preference = array(
//						'items' => array(
//							array(
//								'title' => html_entity_decode($arrHorarios[0]['disciplina'], ENT_COMPAT, 'UTF-8') . ' - ' . count($arrHorarios) . ' veces por semana.',
//								'quantity' => 1,
//								'currency_id' => 'ARS',
//								'unit_price' => floatval(1), //$precioFinal
//								'description' => $descripcion
//							)
//						),
//						'payer' => array(
//							'name' => $this->siteuser->getUserData('nombre'),
//							'surname' => $this->siteuser->getUserData('apellido'),
//							'email' => $this->siteuser->getUserData('email'),
//						),
//						'back_urls' => array(
//							'success' => base_url('inscripciones/aprobado'),
//							'failure' => base_url('inscripciones/rechazado'),
//							'pending' => base_url('inscripciones/pendiente')
//						),
//						'external_reference' => $external_reference
//					);
//
//					$mp_result =  $this->mp->create_preference($mp_preference);
//					echo json_encode( array( 'status' => 'ok', 'init_point' => $mp_result['response']['init_point'] ) );
					
					$url = '';
					
					if ( !$this->adminuser->isLogged() )
					{
						$url = site_url('inscripciones/aprobado');
						$this->enviarEmailRegular('inscripto', $_POST['idDisciplina']);
					}
					else
					{
						if ( $inscripcionActual['activo'] )
							$this->enviarEmailRegular('modificado', $_POST['idDisciplina']);
						
						$url = site_url('usuarios/panel');
					}
					
					echo json_encode( array( 'status' => 'ok', 'url' => $url ) );
				}
				else
					echo json_encode(array('status' => 'error'));
			}
			else
				echo json_encode(array('status' => 'error'));
		}
		else
		{
			echo json_encode(array('status' => 'error'));
		}
	}
	
	public function inscripcionCancelada() //ajax $_POST['external_reference']
	{
		if ( $_POST['external_reference'] )
		{
			//$this->db->delete('alumnos_actividades_dias', array('idAlumno' => $this->siteuser->getUserData('id'), 'MP_external_reference' => $_POST['external_reference']));
			$this->db->delete('alumnos_actividades_especiales', array('idAlumno' => $this->siteuser->getUserData('id'), 'MP_external_reference' => $_POST['external_reference']));
			//$this->db->delete('alumnos_pagos', array('idAlumno' => $this->siteuser->getUserData('id'), 'MP_external_reference' => $_POST['external_reference']));
		}
	}

	
	private function enviarEmailRegular( $status, $idDisciplina )
	{
		$user = $this->siteuser->getUserData();

		$sqlHorarios = "
			SELECT
				dis.title AS disciplina,
				TIME_FORMAT(da.`horaDesde`,'%H:%i') AS horaDesde, 
				TIME_FORMAT(da.`horaHasta`,'%H:%i') AS horaHasta, 
				GROUP_CONCAT(d.title ORDER BY d.id SEPARATOR ', ') AS dia
			FROM
				`disciplinas_actividades` da
			INNER JOIN
				`disciplinas_actividades_dias` dad ON
				dad.`idActividad` = da.`id`
			INNER JOIN
				dias d ON
				d.`id` = dad.`idDia`
			INNER JOIN
				`alumnos_actividades_dias` aad ON
				aad.`idDisciplinaActividadDia` = dad.`id`
			INNER JOIN
				`disciplinas` dis ON
				dis.id = da.`idDisciplina`
			WHERE
				aad.`idAlumno` = ? AND dis.id = ?
			ORDER BY
				dis.title, dad.`idDia`, da.`horaDesde`
		";

		$arrHorarios = $this->db->query($sqlHorarios, array( $user['id'], $idDisciplina ))->result_array();
		$horarios = array();

		foreach( $arrHorarios as $horario )
		{
			if ( $horario['dia'] &&  strrpos($horario['dia'], ', ') )
				$horario['dia'] = substr_replace($horario['dia'], ' y ', strrpos($horario['dia'], ', '), 1 );

			$horarios[] = "$horario[dia] de $horario[horaDesde] a $horario[horaHasta] hs";
		}

		$mail = $this->load->view('mails/inscripcion_regular_' . $status, array(
			'horarios' => $horarios,
			'disciplina' => $arrHorarios[0]['disciplina'],
			'nombre' => $user['nombre'] . ' ' . $user['apellido']
		), true);
		
		magico_sendmail($user['email'], 'Fundación Columbia - Aviso de inscripción', $mail, 'info@fundacioncolumbia.org');
	}
	
	private function enviarEmailEspecial( $status, $idActividadEspecial )
	{
		$user = $this->siteuser->getUserData();
		magico_setLocale('es');
				
		$sqlActividad = "
			SELECT
				*
			FROM 
				`disciplinas_actividades_especiales`
			WHERE
				`id` = ?
		";

		$actividad = $this->db->query($sqlActividad, array( $idActividadEspecial ))->row_array();

		$time = strtotime($actividad['fecha']);
		$actividad['fecha'] = strftime('%A', $time);
		$actividad['fecha'] = ucfirst( utf8_encode($actividad['fecha']) );
		$actividad['fecha'] .= strftime(' %#d de %B', $time);
		$actividad['fecha'] .= ', ' . substr($actividad['horaDesde'],0,5) . 'hs';
		
		$mail = $this->load->view('mails/inscripcion_especial_' . $status, array(
			'actividad' => $actividad,
			'nombre' => $user['nombre'] . ' ' . $user['apellido']
		), true);

		
		$emails = array($user['email'], 'info@fundacioncolumbia.org'); 
		magico_sendmail($emails, 'Fundación Columbia - Aviso de inscripción', $mail, 'info@fundacioncolumbia.org');
	}
}
