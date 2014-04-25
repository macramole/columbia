    <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class PuertasDeEntrada extends MasterControllerColumbia
{	
	public function index()
	{
		$puertaEstatica = $this->db->get_where('estaticas', array('id' => 2))->row_array();
		
		$this->setTitle( 'Puertas de entrada' );
		$this->setFacebookDescription($puertaEstatica['textoDestacado']);
		$this->addContentPage('puertas_de_entrada', array('arrPuertas' => self::getPuertas(), 'cuerpo' => $puertaEstatica['textoDestacado']));
		$this->show(array(), false);
	}
		
	public function getActividades() //ajax
	{
		$sqlHorarios = "
			SELECT
				h.id,
				TIME_FORMAT(h.horaDesde, '%H:%i') AS horaDesde,
				( SELECT GROUP_CONCAT(dias.title ORDER BY dias.id ASC SEPARATOR ', ') FROM disciplinas_actividades_dias dad INNER JOIN dias ON dias.id = dad.idDia WHERE dad.idActividad = h.id GROUP BY dad.idActividad ) dias,
				( SELECT dias.id FROM disciplinas_actividades_dias dad INNER JOIN dias ON dias.id = dad.idDia WHERE dad.idActividad = h.id ORDER BY dias.id ASC LIMIT 1 ) primerDia,
				s.title AS sala
			FROM
				disciplinas_actividades	h
			INNER JOIN
				salas s ON
				s.id = h.idSala
			WHERE
				h.idDisciplina = ?
			ORDER BY
				primerDia ASC, horaDesde ASC
		";
		$horarios = $this->db->query($sqlHorarios, array( $_POST['idDisciplina'] ))->result_array();
		
		foreach( $horarios as &$horario )
		{
			if ( $horario['dias'] &&  strrpos($horario['dias'], ',') )
				$horario['dias'] = substr_replace($horario['dias'], ' y ', strrpos($horario['dias'], ','), 1 );
			
			
			$arrHoraDesde = explode(':', $horario['horaDesde']);
			
			$arrHoraDesde[0] = intval($arrHoraDesde[0]);
			
			if ( intval($arrHoraDesde[1]) > 0 )
				$horario['horaDesde'] = "$arrHoraDesde[0]:$arrHoraDesde[1]";
			else
				$horario['horaDesde'] = "$arrHoraDesde[0]";
			
			$disciplina['horarios'][$horario['id']] = $horario;
		}
		
//		$sqlDocentes = "
//			SELECT
//				dad.`idActividad` ,
//				d.`title`,
//				cu.`url`
//			FROM
//				`disciplinas_actividades_docentes` dad
//			INNER JOIN
//				`docentes` d ON
//				d.id = dad.`idDocente`
//			INNER JOIN
//				`disciplinas_actividades` da ON
//				da.`id` = dad.`idActividad` AND da.`idDisciplina` = ?
//			INNER JOIN
//				`clean_urls` cu ON
//				cu.`node_id` = d.`id` AND cu.`table` = 'docentes'
//		";
//		
//		$docentes = $this->db->query($sqlDocentes, array( $_POST['idDisciplina']))->result_array();
//		
//		foreach ( $docentes as &$docente )
//		{
//			$disciplina['horarios'][ $docente['idActividad'] ]['docentes'][] = $docente;
//		}
		
		$arrReturn['regulares'] = $this->load->view('puertas_de_entrada_actividades', array( 'arrActividades' => $disciplina['horarios'] ), true);
		
		/** ESPECIALES **/
		
		$sqlActividadesEspeciales = "
			SELECT
				dae.*,
				cu.url
			FROM 
				`disciplinas_actividades_especiales` dae
			INNER JOIN
				clean_urls cu ON
				cu.node_id = dae.`id` AND cu.table = 'disciplinas_actividades_especiales'
			WHERE
				dae.`idDisciplina` = ? AND dae.`fecha` >= NOW()
			ORDER BY
				dae.`fecha` ASC, dae.`horaDesde` ASC
		";
		
		$arrActividadesEspeciales = $this->db->query($sqlActividadesEspeciales, array('idDisciplina' => $_POST['idDisciplina'] ))->result_array();
		
		magico_setLocale('es');
		
		foreach ( $arrActividadesEspeciales as &$actividadEspecial )
		{
			$time = strtotime($actividadEspecial['fecha']);
			$actividadEspecial['fecha'] = strftime('%A', $time);
			$actividadEspecial['fecha'] = ucfirst( utf8_encode($actividadEspecial['fecha']) );
			$actividadEspecial['fecha'] .= strftime(' %#d de %B', $time);

			$arrHoraDesde = explode(':', $actividadEspecial['horaDesde']);

			$arrHoraDesde[0] = intval($arrHoraDesde[0]);

			if ( intval($arrHoraDesde[1]) > 0 )
				$actividadEspecial['horaDesde'] = "$arrHoraDesde[0]:$arrHoraDesde[1]";
			else
				$actividadEspecial['horaDesde'] = "$arrHoraDesde[0]";

			$actividadEspecial['fecha'] .= ', ' . $actividadEspecial['horaDesde'] . ' hs.';
			
			$disciplina['actividades_especiales'][ $actividadEspecial['id'] ] = $actividadEspecial;
		}
		
		$arrReturn['especiales'] = $this->load->view('puertas_de_entrada_actividades_especiales', array( 'arrActividades' => $disciplina['actividades_especiales'] ), true);
		
//		$sqlDocentesActividadesEspeciales = "
//			SELECT
//				dad.`idActividadEspecial` ,
//				d.`title`,
//				cu.`url`
//			FROM
//				`disciplinas_actividades_especiales_docentes` dad
//			INNER JOIN
//				`docentes` d ON
//				d.id = dad.`idDocente`
//			INNER JOIN
//				`disciplinas_actividades_especiales` da ON
//				da.`id` = dad.`idActividadEspecial` AND da.`idDisciplina` = ?
//			INNER JOIN
//				`clean_urls` cu ON
//				cu.`node_id` = d.`id` AND cu.`table` = 'docentes'
//			WHERE
//				da.`fecha` >= NOW()
//		";
//		
//		$docentesActividadesEspeciales = $this->db->query($sqlDocentesActividadesEspeciales, array($disciplina['id']))->result_array();
//		
//		foreach ( $docentesActividadesEspeciales as &$docente )
//		{
//			$disciplina['actividades_especiales'][ $docente['idActividadEspecial'] ]['docentes'][] = $docente;
//		}
		
		echo json_encode($arrReturn);
	}
	
	public function disciplina()
	{
		$disciplina = magico_getByUrlClean();
		
		if ( !$disciplina )
		{
			redirect();
			exit;
		}
		
		magico_getImageToRow($disciplina, 'disciplinas', 340, 225);
		
		$puerta = $this->db->get_where('puertas', array('id' => $disciplina['idPuerta']))->row_array();
		$disciplina['puerta'] = $puerta['title'];
		$disciplina['puertaCss'] = ContentType::cleanURL($puerta['title']);
		
		/**
		 * Actividades regulares 
		 */
		
		$sqlHorarios = "
			SELECT
				h.id,
				TIME_FORMAT(h.horaDesde, '%H:%i') AS horaDesde,
				TIME_FORMAT(h.horaHasta, '%H:%i') AS horaHasta,
				f.title as frecuencia,
				( SELECT GROUP_CONCAT(dias.title ORDER BY dias.id ASC SEPARATOR ', ') FROM disciplinas_actividades_dias dad INNER JOIN dias ON dias.id = dad.idDia WHERE dad.idActividad = h.id GROUP BY dad.idActividad ) dias,
				( SELECT dias.id FROM disciplinas_actividades_dias dad INNER JOIN dias ON dias.id = dad.idDia WHERE dad.idActividad = h.id ORDER BY dias.id ASC LIMIT 1 ) primerDia,
				s.title AS sala,
				h.precio
			FROM
				disciplinas_actividades	h
			INNER JOIN
				salas s ON
				s.id = h.idSala
			INNER JOIN
				frecuencias f ON
				f.id = h.idFrecuencia
			WHERE
				h.idDisciplina = ?
			ORDER BY
				primerDia ASC, horaDesde ASC 
		";
		$horarios = $this->db->query($sqlHorarios, array( $disciplina['id'] ))->result_array();
		
		if ( count($horarios) )
		{
			$arrHorariosRegulares = array();
			
			foreach( $horarios as &$horario )
			{
				if ( $horario['dias'] &&  strrpos($horario['dias'], ',') )
					$horario['dias'] = substr_replace($horario['dias'], ' y ', strrpos($horario['dias'], ','), 1 );

				//$disciplina['horarios'][$horario['id']] = $horario;
				$arrHorariosRegulares[$horario['id']] = $horario;
			}

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
				$arrHorariosRegulares[ $docente['idActividad'] ]['docentes'][] = $docente;
			}
			
			//los ordeno por frecuencia
			  
			foreach ( $arrHorariosRegulares as &$horarioRegular )
			{
				$disciplina['horarios'][$horarioRegular['frecuencia']][] = $horarioRegular;
			}
			
			/** PRECIOS **/
			$configuracion = magico_getGlobalConfig();
			
			foreach ( $disciplina['horarios'] as $frecuencia => $horario )
			{
				$disciplina['precios'][$frecuencia] = array();
				
				$strFrecuencia = '';
					
				switch( $frecuencia )
				{
					case 'Semanal':
						$strFrecuencia = 'semana';
						break;
					case 'Quincenal':
						$strFrecuencia = 'quincena';
						break;
					case 'Mensual':
						$strFrecuencia = 'mes';
						break;
				}

				
				if ( intval($horario[0]['precio']) ) //Si la actividad en particular tiene precio uso ese (no es comun)
				{
					$disciplina['precios'][$frecuencia][0]['desc'] = 'Precio general';
					$disciplina['precios'][$frecuencia][0]['precio'] = "$" . $horario[0]['precio'];
				}
				elseif ( intval($disciplina['precio']) ) //Si la disciplina tiene precio uso esos (tampoco es tan comun)
				{
					
					for ( $dias = 1 ; $dias <= 4 ; $dias++ )
					{
						$vez = $dias > 1 ? 'veces' : 'vez';
						$precio = intval($disciplina['precio']);
						
						if ( $dias == 1 || intval($disciplina["descuento{$dias}dias"]) > 0 )
						{
							for ( $precios = 2 ; $precios <= $dias ; $precios++ )
								$precio += intval($disciplina["descuento{$precios}dias"]);

							$disciplina['precios'][$frecuencia][$dias]['desc'] = "$dias $vez por $strFrecuencia";
							$disciplina['precios'][$frecuencia][$dias]['precio'] = "$$precio";
						}
						
					}
					
				}
				else //Uso la configuración general (caso comun)
				{
					for ( $dias = 1 ; $dias <= 4 ; $dias++ )
					{
						$vez = $dias > 1 ? 'veces' : 'vez';
						$precio = intval($configuracion['precioActividadesRegulares']);
						
						for ( $precios = 2 ; $precios <= $dias ; $precios++ )
							$precio += intval($configuracion["descuento{$precios}dias"]);
						
						$disciplina['precios'][$frecuencia][$dias]['desc'] = "$dias $vez por $strFrecuencia";
						$disciplina['precios'][$frecuencia][$dias]['precio'] = "$$precio";
					}	
				}
			}
		}
		/**
		 * Actividades especiales 
		 */
		
		$sqlActividadesEspeciales = "
			SELECT
				dae.*,
				cu.url
			FROM 
				`disciplinas_actividades_especiales` dae
			INNER JOIN
				clean_urls cu ON
				cu.node_id = dae.`id` AND cu.table = 'disciplinas_actividades_especiales'
			WHERE
				dae.`idDisciplina` = ? AND DATEDIFF(dae.`fecha`, NOW()) >= 0
			ORDER BY
				dae.`fecha` ASC, dae.`horaDesde` ASC
		";
		
		$arrActividadesEspeciales = $this->db->query($sqlActividadesEspeciales, array('idDisciplina' => $disciplina['id']))->result_array();
		
		magico_setLocale('es');
		
		foreach ( $arrActividadesEspeciales as &$actividadEspecial )
		{
			$actividadEspecial['puedeInscribirse'] = false;
			
			if ( 
				$actividadEspecial && 
				//$actividadEspecial['precio'] && 
				$actividadEspecial['vacantes'] > 0 && 
				strtotime($actividadEspecial['fecha'] . ' ' . $actividadEspecial['horaDesde'] ) > time() 
				)
			$actividadEspecial['puedeInscribirse'] = true;	
			
			
			$time = strtotime($actividadEspecial['fecha']);
			$actividadEspecial['fecha'] = strftime('%A', $time);
			$actividadEspecial['fecha'] = ucfirst( utf8_encode($actividadEspecial['fecha']) );
			$actividadEspecial['fecha'] .= strftime(' %#d de %B', $time);

			$arrHoraDesde = explode(':', $actividadEspecial['horaDesde']);

			$arrHoraDesde[0] = intval($arrHoraDesde[0]);

			if ( intval($arrHoraDesde[1]) > 0 )
				$actividadEspecial['horaDesde'] = "$arrHoraDesde[0]:$arrHoraDesde[1]";
			else
				$actividadEspecial['horaDesde'] = "$arrHoraDesde[0]";

			$actividadEspecial['fecha'] .= ', ' . $actividadEspecial['horaDesde'] . ' hs.';
			
			$disciplina['actividades_especiales'][ $actividadEspecial['id'] ] = $actividadEspecial;
		}
		
		$sqlDocentesActividadesEspeciales = "
			SELECT
				dad.`idActividadEspecial` ,
				d.`title`,
				cu.`url`
			FROM
				`disciplinas_actividades_especiales_docentes` dad
			INNER JOIN
				`docentes` d ON
				d.id = dad.`idDocente`
			INNER JOIN
				`disciplinas_actividades_especiales` da ON
				da.`id` = dad.`idActividadEspecial` AND da.`idDisciplina` = ?
			INNER JOIN
				`clean_urls` cu ON
				cu.`node_id` = d.`id` AND cu.`table` = 'docentes'
			WHERE
				da.`fecha` >= NOW()
		";
		
		$docentesActividadesEspeciales = $this->db->query($sqlDocentesActividadesEspeciales, array($disciplina['id']))->result_array();
		
		foreach ( $docentesActividadesEspeciales as &$docente )
		{
			$disciplina['actividades_especiales'][ $docente['idActividadEspecial'] ]['docentes'][] = $docente;
		}
		
		$this->newsNotification = true;
		$this->setTitle( $disciplina['title'] );
		$this->setFacebookImage($disciplina['imagen']);
		$this->setFacebookDescription($disciplina['textoDestacado']);
		$this->addContentPage('disciplina', array('disciplina' => $disciplina));
		$this->show();
	}
}
