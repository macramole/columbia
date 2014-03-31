<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class Ipn extends MasterControllerColumbia
{	
	function index()
	{	
		
		if ( $_GET['id'] )
		{
			$this->load->library('mp', array('client_id' => MP_CLIENT_ID, 'client_secret' => MP_CLIENT_SECRET) );
			$paymentInfo = $this->mp->get_payment_info ( $_GET['id'] );
			

			if ( $paymentInfo['response']['collection'] )
			{
				$collection = $paymentInfo['response']['collection'];
				
				/** Si ya estaba updateado que no haga nada, es por seguridad **/
				$pago = $this->db->get_where('alumnos_actividades_especiales', array( 'MP_external_reference' => $collection['external_reference'] ) )->row_array();
				
				if ( $pago['MP_status'] !=  $collection['status'])
				{
					/** Sino que primero que nada updatee el pago **/
					$this->db->update('alumnos_actividades_especiales', array(
						//'idMercadoPago' => $collection['id'],
						'MP_status' => $collection['status']), array( 'MP_external_reference' => $collection['external_reference'] ));
				}
				else
				{
					echo 'Error';
					exit;
				}
				
				/**
				 * Si esta aprobado o en vias de... 
				 */
				if ( in_array( $collection['status'], array('approved','pending','in_process') ) )
				{
					/**
					 * CASO REGULARES 
					 */
					if ( substr($collection['external_reference'],0,1) == MP_REFERENCE_REGULAR )
					{
						/**
						* Checkeo si hay inscripciones anteriores.
						 * NUNCA PUEDE ESTAR ACTIVO = 1 Y VACANTE = 0
						 * 
						 * Devuelve también el numero de día 1 ,2 , 3 etc (lunes martes etc) ordenados
						*/
						$sqlDias = "
							SELECT
								aad.*,
								(
									SELECT
										fechaHasta + INTERVAL 1 DAY
									FROM
										`alumnos_actividades_dias`
									WHERE
										MP_external_reference <> ? AND
										`idDisciplinaActividadDia` = aad.`idDisciplinaActividadDia` AND
										`idAlumno` = aad.`idAlumno` AND
										activo = 1 AND
										id < aad.`id`
									ORDER BY
										fechaHasta DESC
									LIMIT 1
								) AS anterior,
								daa.`idDia`
							FROM
								`alumnos_actividades_dias` aad
							INNER JOIN
								`disciplinas_actividades_dias` daa ON
								daa.`id` = aad.`idDisciplinaActividadDia`
							WHERE
								aad.`MP_external_reference` = ?
							ORDER BY
								idDia
						";
						
						$arrDias = $this->db->query($sqlDias, array($collection['external_reference'], $collection['external_reference']))->result_array();
						
						/*
						 * O todos o ninguno tienen anterior
						 * TODO: en inscripciones_regulares no podes elegir uno que ya estés anotado
						 */
						
						/**
						* No hay anteriores, son nuevos
						*/
						if ( !$arrDias[0]['anterior'] )
						{
							$rangoFechas = $this->getRangoFechas($arrDias);
							
							/**
							* Si esta aprobado puede empezar a ir desde hoy
							*/
							if ( $collection['status'] == 'approved' )
							{
								foreach( $arrDias as $dia )
								{
									$sqlUpdate = "
										UPDATE
											alumnos_actividades_dias
										SET
											activo = 1,
											vacante = 1,
											fechaDesde = ?,
											fechaHasta = ?
										WHERE
											id = ?
									";
									
									
									$this->db->query($sqlUpdate, array($rangoFechas[0], $rangoFechas[1], $dia['id']));
								}
							}
							/**
							* Se reserva la vacante pero no puede ir hasta que pague
							* La vacante se reserva hasta que por ipn me avisen que el pago no se realizó o los dias correspondientes
							*/
							else
							{
								foreach( $arrDias as $dia )
								{
									$sqlUpdate = "
										UPDATE
											alumnos_actividades_dias
										SET
											activo = 0,
											vacante = 1,
											fechaDesde = NOW(),
											fechaHasta = NOW() + INTERVAL 30 DAY
										WHERE
											id = ?
									";
									
									$this->db->query($sqlUpdate, array($rangoFechas[0], $rangoFechas[1], $dia['id']));
								}
							}
							//Envio el mail correspondiente
							$this->enviarEmailRegular($collection['status'], $collection['external_reference'], false, $rangoFechas);
						}
						/**
						* Hay anteriores, deben actualizarse
						*/
						else
						{
							$rangoFechas = $this->getRangoFechas($arrDias, strtotime($dia['anterior']));
							
							/**
							* Si esta aprobado se le extiende 30 días desde el último
							*/
							if ( $collection['status'] == 'approved' )
							{
								foreach( $arrDias as $dia )
								{
									$sqlUpdate = "
										UPDATE
											alumnos_actividades_dias
										SET
											activo = 1,
											vacante = 1,
											fechaDesde = ?,
											fechaHasta = ?
										WHERE
											id = ?
									";
									$this->db->query($sqlUpdate, array($rangoFechas[0], $rangoFechas[1], $dia['id']));
								}
							}
							/**
							* Si no, se reserva la vacante pero no puede ir hasta que pague.
							* Cuando paga, si esta activo, se le extiende normalmente. Si no es como si no estuviera renovando
							*/
							else
							{
								foreach( $arrDias as $dia )
								{
									$sqlUpdate = "
										UPDATE
											alumnos_actividades_dias
										SET
											activo = 0,
											vacante = 1,
											fechaDesde = ?,
											fechaHasta = ?
										WHERE
											id = ?
									";
									$this->db->query($sqlUpdate, array($rangoFechas[0], $rangoFechas[1], $dia['id']));
								}
							}
							
							//Envio el mail correspondiente
							$this->enviarEmailRegular($collection['status'], $collection['external_reference'], true, $rangoFechas);
						}
					}
					/**
					* CASO ESPECIALES
					*/
					else
					{
						/**
						 *
						 * Agarro la actividad especial de la base de datos 
						 */
						$sqlActividadEspecial = "
							SELECT
								*
							FROM
								`disciplinas_actividades_especiales` dae
							INNER JOIN
								`alumnos_actividades_especiales` aae ON
								aae.`idDisciplinaActividadEspecial` = dae.`id`
							WHERE
								aae.`MP_external_reference` = ?
						";
						
						$actividadEspecial = $this->db->query($sqlActividadEspecial, array($collection['external_reference']))->row_array();
						
						/**
						 * Caso monto fijo. Es el caso más simple 
						 */
						if ( $actividadEspecial['modalidadPago'] == 'Monto fijo' )
						{
							/**
							 * Paga la actividad completa 
							 */
							if ( !$actividadEspecial['pagoVacante'] )
							{
								/**
								 * Si esta aprobado ya está 
								 */
								if ( $collection['status'] == 'approved' )
								{
									$this->db->update('alumnos_actividades_especiales', array(
										'activo' => '1',
										'vacante' => '1',
										'pagoVacante' => '0',
										'fechaDesde' => $actividadEspecial['fecha'],
										'fechaHasta' => $actividadEspecial['fechaHasta'],
									), array( 'MP_external_reference' => $collection['external_reference'] ));
								}
								else
								{
									$this->db->update('alumnos_actividades_especiales', array(
										'activo' => '0',
										'vacante' => '1',
										'pagoVacante' => '0',
										'fechaDesde' => $actividadEspecial['fecha'],
										'fechaHasta' => $actividadEspecial['fechaHasta'],
									), array( 'MP_external_reference' => $collection['external_reference'] ));
								}
								
							}
							/**
							 * Paga solo la vacante
							 */
							else
							{
								if ( $collection['status'] == 'approved' )
								{
									$this->db->update('alumnos_actividades_especiales', array(
										'activo' => '1',
										'vacante' => '1',
										'pagoVacante' => '1',
										'fechaDesde' => $actividadEspecial['fecha'],
										'fechaHasta' => $actividadEspecial['fechaHasta'],
									), array( 'MP_external_reference' => $collection['external_reference'] ));
								}
								else
								{
									$this->db->update('alumnos_actividades_especiales', array(
										'activo' => '0',
										'vacante' => '1',
										'pagoVacante' => '1',
										'fechaDesde' => $actividadEspecial['fecha'],
										'fechaHasta' => $actividadEspecial['fechaHasta'],
									), array( 'MP_external_reference' => $collection['external_reference'] ));
								}
							}
							
							$this->enviarEmailEspecial($collection['status'], $collection['external_reference'], false, null, null);
						}
						/**
						 * Caso especiales y pago mensual.
						 */
						else
						{
							//Checkeo si hay inscripciones anteriores
							$sqlDia = "
								SELECT
									*,
									(
										SELECT
											fechaHasta + INTERVAL 1 DAY
										FROM
											`alumnos_actividades_especiales`
										WHERE
											MP_external_reference <> ? AND
											`idDisciplinaActividadEspecial` = aae.`idDisciplinaActividadEspecial` AND
											`idAlumno` = aae.`idAlumno` AND
											activo = 1 AND
											id < aae.`id` AND
											pagoVacante = 0
										ORDER BY
											fechaHasta DESC
										LIMIT 1
									) AS anterior
								FROM
									`alumnos_actividades_especiales` aae
								WHERE
									aae.`MP_external_reference` = ?
							";
							
							$dia = $this->db->query($sqlDia, array($collection['external_reference'], $collection['external_reference']))->row_array();
						
							/**
							 * Es nuevo 
							 */
							if ( !$dia['anterior'] )
							{
								/**
								* Paga el mes completo
								*/
								if ( !$actividadEspecial['pagoVacante'] )
								{
									
									if ( $collection['status'] == 'approved' )
									{
										$sqlUpdate = "
											UPDATE
												alumnos_actividades_especiales
											SET
												activo = 1,
												pagoVacante = 0,
												vacante = 1,
												fechaDesde = ?,
												fechaHasta = ? + INTERVAL 30 DAY
											WHERE
												id = ?
										";
									}
									else
									{
										$sqlUpdate = "
											UPDATE
												alumnos_actividades_especiales
											SET
												activo = 0,
												pagoVacante = 0,
												vacante = 1,
												fechaDesde = ?,
												fechaHasta = ? + INTERVAL 30 DAY
											WHERE
												id = ?
										";
									}
									
									$this->enviarEmailEspecial(
										$collection['status'], 
										$collection['external_reference'], 
										false, 
										array($actividadEspecial['fecha'], strtotime('+ 30 days', strtotime($actividadEspecial['fecha']))),
										false);
								}
								/**
								* Paga solo la vacante
								*/
								else
								{
									if ( $collection['status'] == 'approved' )
									{
										$sqlUpdate = "
											UPDATE
												alumnos_actividades_especiales
											SET
												activo = 1,
												pagoVacante = 1,
												vacante = 1,
												fechaDesde = ?,
												fechaHasta = ? + INTERVAL 30 DAY
											WHERE
												id = ?
										";
									}
									else
									{
										$sqlUpdate = "
											UPDATE
												alumnos_actividades_especiales
											SET
												activo = 0,
												pagoVacante = 1,
												vacante = 1
												fechaDesde = ?,
												fechaHasta = ? + INTERVAL 30 DAY
											WHERE
												id = ?
										";
									}
									
									$this->enviarEmailEspecial(
										$collection['status'], 
										$collection['external_reference'], 
										false, 
										array($actividadEspecial['fecha'], strtotime('+ 30 days', strtotime($actividadEspecial['fecha']))),
										true);
									
								}

								$this->db->query($sqlUpdate, array($actividadEspecial['fecha'],  $actividadEspecial['fecha'], $dia['id']));
							}
							/**
							 * Renueva, entonces "solo vacante" no podría ser
							 */
							else
							{
								if ( $collection['status'] == 'approved' )
								{
									$sqlUpdate = "
										UPDATE
											alumnos_actividades_especiales
										SET
											activo = 1,
											pagoVacante = 0,
											vacante = 1,
											fechaDesde = ?,
											fechaHasta = ? + INTERVAL 30 DAY
										WHERE
											id = ?
									";
								}
								else
								{
									$sqlUpdate = "
										UPDATE
											alumnos_actividades_especiales
										SET
											activo = 0,
											pagoVacante = 0,
											vacante = 1
											fechaDesde = ?,
											fechaHasta = ? + INTERVAL 30 DAY
										WHERE
											id = ?
									";
								}
								
								$this->db->query($sqlUpdate, array($dia['anterior'], $dia['anterior'], $dia['id']));
								$this->enviarEmailEspecial(
									$collection['status'], 
									$collection['external_reference'], 
									true, 
									array($dia['anterior'], strtotime('+ 30 days', strtotime($dia['anterior']))),
									false);
							}	
						}
					}
				}
				else //pagos cancelados etc.
				{
					if ( substr($collection['external_reference'],0,1) == MP_REFERENCE_REGULAR )
					{
						
						//ToDo archivar
						
						$this->db->update(
								'alumnos_actividades_dias', 
								array('activo' => 0, 'vacante' => 0),
								array('MP_external_reference' => $collection['external_reference']));
						
						$this->enviarEmailRegular($collection['status'], $collection['external_reference'], false, null);
					}
					else
					{
						/*$this->db->update(
								'alumnos_actividades_especiales', 
								array('activo' => 0, 'vacante' => 0), 
								array('MP_external_reference' => $collection['external_reference']));*/
						
						$this->enviarEmailEspecial($collection['status'], $collection['external_reference'], false, null, null);
						
						include_once('application/libraries/admin/content_types/Alumno.php');
						Alumno::archivarInscripcionEspecial($pago['idAlumno'], $pago['idDisciplinaActividadEspecial']);
					}
					
				}
			}
		}
	}
	/**
	 * Devuelve el rango de fechas en que es válida su inscripción. Se supone que estan ordenados !
	 * 
	 * @param type $arrNumDias 
	 */
	private function getRangoFechas( $arrDias, $timestamp = null )
	{
		$dayNames = array(
			'',
			'Monday', 
			'Tuesday', 
			'Wednesday', 
			'Thursday', 
			'Friday', 
			'Saturday',
			'Sunday'
		);
		
		$arrNumDias = array();
		
		foreach ( $arrDias as $dia )
		{
			$arrNumDias[] = $dia['idDia'];
		}
		
		$primerDia = $ultimoDia = null;
		
		if ( $timestamp == null ) //Desde hoy
		{
			//Si algun día es hoy toma el siguiente
			$keyDiaHoy = null;
			
			foreach ( $arrNumDias as $key => $dia )
			{
				if ( $dia == date('N') )
				{
					$keyDiaHoy = $key + 1;
					break;
				}
			}
			
			if ($keyDiaHoy == null) //si ningun dia es hoy toma el más próximo
			{
				$numDiaHoy = date('N') + 1;
				
				while( ( $keyDiaHoy = array_search($numDiaHoy, $arrNumDias) ) === false )
				{	
					$numDiaHoy++;
					
					if ( $numDiaHoy > 7 )
						$numDiaHoy = 1;
					
				}
			} else if ( !isset($arrNumDias[$keyDiaHoy]) ) // si algun dia es hoy pero ese key no existe es que era el primero
				$keyDiaHoy = 0;
			
			$primerDia = strtotime('next ' . $dayNames[$arrNumDias[$keyDiaHoy]] );
			
			$keyDiaHoy--;
			
			if ( $keyDiaHoy < 0 )
				$keyDiaHoy = count($arrNumDias) - 1;
			
			$ultimoDia = strtotime('next ' . $dayNames[$arrNumDias[$keyDiaHoy]] . ' + 3 weeks' );
		}
		else
		{
			$primerDia = $timestamp;
			
			$numDiaHoy = date('N') - 1;
				
			while( ( $keyDiaHoy = array_search($numDiaHoy, $arrNumDias) ) === false )
			{	
				$numDiaHoy--;

				if ( $numDiaHoy < 1 )
					$numDiaHoy = 7;

			}
			
			$ultimoDia = strtotime('next ' . $dayNames[ $arrNumDias[$keyDiaHoy] ] . ' + 3 weeks', $timestamp );
		}
		
	
		return array( date('Y-m-d',$primerDia), date('Y-m-d',$ultimoDia) . ' 23:00');
	}
	
	private function enviarEmailRegular( $status, $external_reference, $renovando, $rangoFechas )
	{
		if ( count($rangoFechas ) )
		{
			foreach ( $rangoFechas as &$rango )
			{
				$rango = date('d-m-Y',strtotime($rango));
			}
		}
		
		$sqlUser = "
			SELECT DISTINCT
				a.*
			FROM 
				alumnos a
			INNER JOIN
				`alumnos_actividades_dias` aad ON
				aad.`idAlumno` = a.`id`
			WHERE
				aad.`MP_external_reference` = ?
		";

		$user = $this->db->query($sqlUser, array( $external_reference ) )->row_array();

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
				aad.`MP_external_reference` = ?
			GROUP BY
				da.id
			ORDER BY
				dis.title, dad.`idDia`, da.`horaDesde`
		";

		$arrHorarios = $this->db->query($sqlHorarios, array( $external_reference ))->result_array();
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
			'nombre' => $user['nombre'] . ' ' . $user['apellido'],
			'rangoFechas' => $rangoFechas,
			'renovando' => $renovando
		), true);
		
		magico_sendmail($user['email'], 'Fundación Columbia - Aviso de pago', $mail, 'info@fundacioncolumbia.org');
	}
	
	private function enviarEmailEspecial( $status, $external_reference, $renovando = false, $rangoFechas = null, $pagoVacante = null)
	{
		//Agarro el usuario relacionado
		$sqlUser = "
			SELECT DISTINCT
				a.*
			FROM 
				alumnos a
			INNER JOIN
				`alumnos_actividades_especiales` aae ON
				aae.`idAlumno` = a.`id`
			WHERE
				aae.`MP_external_reference` = ?
		";

		$user = $this->db->query($sqlUser, array( $external_reference ) )->row_array();
		
		magico_setLocale('es');
				
		$sqlActividad = "
			SELECT
				dae.title,
				dae.fecha,
				dae.`frecuencia`,
				dae.`horaDesde`,
				dae.`horaHasta`,
				dae.modalidadPago,
				DATE_FORMAT(aae.`fechaDesde`,'%d/%m/%Y') AS rangoDesde,
				DATE_FORMAT(aae.`fechaHasta`,'%d/%m/%Y') AS rangoHasta,
				aae.`pagoVacante`,
				aae.`vacante`,
				aae.`activo`
			FROM 
				`disciplinas_actividades_especiales` dae
			INNER JOIN
				`alumnos_actividades_especiales` aae ON
				aae.`idDisciplinaActividadEspecial` = dae.`id`
			WHERE
				aae.`MP_external_reference` = ?
		";

		$actividad = $this->db->query($sqlActividad, array( $external_reference ))->row_array();

		$time = strtotime($actividad['fecha']);
		$actividad['fecha'] = strftime('%A', $time);
		$actividad['fecha'] = ucfirst( utf8_encode($actividadEspecial['fecha']) );
		$actividad['fecha'] .= strftime(' %#d de %B', $time);
		$actividad['fecha'] .= ', ' . substr($actividad['horaDesde'],0,5) . 'hs';
		
		$mail = $this->load->view('mails/inscripcion_especial_' . $status, array(
			'actividad' => $actividad,
			'nombre' => $user['nombre'] . ' ' . $user['apellido']
		), true);

		$emails = array($user['email'], 'info@fundacioncolumbia.org'); 
		magico_sendmail($emails, 'Fundación Columbia - Aviso de pago', $mail, 'info@fundacioncolumbia.org');
	}
}

/*
Array
(
    [status] => 200
    [response] => Array
        (
            [collection] => Array
                (
                    [id] => 484817034
                    [site_id] => MLA
                    [date_created] => 2013-02-25T11:44:26.000-04:00
                    [date_approved] => 2013-02-25T11:44:25.000-04:00
                    [money_release_date] => 2013-03-09T11:44:25.000-04:00
                    [last_modified] => 2013-02-25T11:46:27.000-04:00
                    [payer] => Array
                        (
                            [id] => 57604525
                            [first_name] => Leandro
                            [last_name] => Garber
                            [phone] => Array
                                (
                                    [area_code] => 
                                    [number] => 011-48423238
                                    [extension] => 
                                )

                            [identification] => Array
                                (
                                    [type] => 
                                    [number] => 32961549
                                )

                            [email] => leandrogarber@gmail.com
                            [nickname] => SIRLECHUCK
                        )

                    [order_id] => 
                    [external_reference] => 
                    [reason] => Biodanza - 2 veces por semana.
                    [transaction_amount] => 1
                    [currency_id] => ARS
                    [net_received_amount] => 0.94
                    [total_paid_amount] => 1
                    [shipping_cost] => 0
                    [status] => approved
                    [status_detail] => accredited
                    [payment_type] => credit_card
                    [marketplace] => NONE
                    [operation_type] => regular_payment
                    [marketplace_fee] => 0
                    [collector] => Array
                        (
                            [id] => 131481825
                            [first_name] => FundaciÃ³n Columbia
                            [last_name] => Banco Columbia S.A.
                            [phone] => Array
                                (
                                    [area_code] =>  
                                    [number] => (11)43414311
                                    [extension] => 
                                )

                            [email] => candelaria@fundacioncolumbia.org
                            [nickname] => FUNDACINCOLUMBIABANCOCOLUMB
                        )

                )

        )

)
*/