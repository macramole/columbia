<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class Agenda extends MasterControllerColumbia
{	
	const CANT_MESES = 6;
	const ID_TEXTO_AGENDA_REGULAR = 6;
	const ID_TEXTO_AGENDA_MENSUAL = 7;
	
	public function index()
	{
		redirect('agenda/regular');
	}
	
	public function actividadEspecial()
	{
		$actividadEspecial = magico_getByUrlClean();
		
		if ( $actividadEspecial )
		{
			magico_setLocale('es');
			
			// puede inscribirse ?
			$actividadEspecial['puedeInscribirse'] = false;
			
			if ( 
				$actividadEspecial && 
				//$actividadEspecial['precio'] && 
				$actividadEspecial['vacantes'] > 0 && 
				strtotime($actividadEspecial['fecha'] . ' ' . $actividadEspecial['horaDesde'] ) > time() 
				)
				$actividadEspecial['puedeInscribirse'] = true;		
			
			magico_getImagesToRow($actividadEspecial, 'disciplinas_actividades_especiales', 380, 285);
			
			/** PRECIOS **/
			$precios = array();
			$configuracion = magico_getGlobalConfig();
			$time = strtotime($actividadEspecial['fecha']);
			
			$fechaDescuento1 = strtotime("-$configuracion[descuentoAdelantado1dias] days", $time);
			$fechaDescuento1 = date('d/m/Y', $fechaDescuento1);
			
			$fechaDescuento2 = strtotime("-$configuracion[descuentoAdelantado2dias] days", $time);
			$fechaDescuento2 = date('d/m/Y', $fechaDescuento2);
			
			if ( intval($actividadEspecial['precio']) > 0 )
				$precios['Arancel'] = '$' . $actividadEspecial['precio'];
			else
				$precios['Arancel'] = 'Gratis';
			
			if ( $actividadEspecial['precioDescuento1'] )
				$precios['Antes del ' . $fechaDescuento1] = '$' . $actividadEspecial['precioDescuento1'];
			
			if ( $actividadEspecial['precioDescuento2'] )
				$precios['Antes del ' . $fechaDescuento2] = '$' . $actividadEspecial['precioDescuento2'];
			
			if ( $actividadEspecial['precioReserva'] )
				$precios['Valor de la seña'] = '$' . $actividadEspecial['precioReserva'];
			
			$actividadEspecial['precios'] = $precios;
			
			
			/**Fechas**/
			$time = strtotime($actividadEspecial['fecha']);
			
			$actividadEspecial['fecha'] = strftime('%A', $time);
			$actividadEspecial['fecha'] = ucfirst( utf8_encode($actividadEspecial['fecha']) );
			$actividadEspecial['fecha'] .= strftime(' %#d de %B', $time);
			
			$timeHasta = strtotime($actividadEspecial['fechaHasta']);
			
			if ( $timeHasta != $time )
			{
				$actividadEspecial['fechaHasta'] = strftime('%A', $timeHasta);
				$actividadEspecial['fechaHasta'] = ucfirst( utf8_encode($actividadEspecial['fechaHasta']) );
				$actividadEspecial['fechaHasta'] .= strftime(' %#d de %B', $timeHasta);
			}
			else
				$actividadEspecial['fechaHasta'] = null;
			
			
			/**Horas**/
			
			$arrHoraDesde = explode(':', $actividadEspecial['horaDesde']);
			$arrHoraDesde[0] = intval($arrHoraDesde[0]);
			
			if ( intval($arrHoraDesde[1]) > 0 )
				$actividadEspecial['horaDesde'] = "$arrHoraDesde[0]:$arrHoraDesde[1]";
			else
				$actividadEspecial['horaDesde'] = "$arrHoraDesde[0]";
			
			$arrHoraHasta = explode(':', $actividadEspecial['horaHasta']);
			$arrHoraHasta[0] = intval($arrHoraHasta[0]);
			
			if ( intval($arrHoraHasta[1]) > 0 )
				$actividadEspecial['horaHasta'] = "$arrHoraHasta[0]:$arrHoraHasta[1]";
			else
				$actividadEspecial['horaHasta'] = "$arrHoraHasta[0]";
			
			$actividadEspecial['fecha'] .= ", $actividadEspecial[horaDesde] a $actividadEspecial[horaHasta] hs.";
			
			
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
			
			$this->newsNotification = true;
			$this->setTitle( $actividadEspecial['title'] );
			$this->setFacebookImage($actividadEspecial['imagenes'][0]);
			$this->setFacebookDescription($actividadEspecial['textoDestacado']);
			$this->addContentPage('actividad_especial', array('actividad' => $actividadEspecial));
			$this->show();
		}
		else
		{
			redirect('agenda');
		}
	}
	
	public function regular()
	{
		$arrPuertaCssClasses = array();
		$puertas = $this->db->get('puertas')->result_array();
		
		foreach ( $puertas as &$puerta )
		{
			$puerta['cssClass'] = ContentType::cleanURL($puerta['title']);
			$arrPuertaCssClasses[$puerta['id']] = $puerta['cssClass'];
		}
		
		$sqlHorarios = "
		SELECT
			da.id,
			TIME_FORMAT(da.horaDesde, '%k:%i') AS horaDesde,
			d.title AS disciplina,
			d.idPuerta,
			dad.idDia,
			cu.url
		FROM
			disciplinas_actividades da
		INNER JOIN
			disciplinas d ON
			d.id = da.idDisciplina
		INNER JOIN
			disciplinas_actividades_dias dad ON
			dad.idActividad = da.id
		INNER JOIN
			clean_urls cu ON
			cu.node_id = da.idDisciplina AND cu.table = 'disciplinas'
		ORDER BY
			da.horaDesde, dad.idDia
		";
		
		$arrHorarios = $this->db->query($sqlHorarios)->result_array();
		$horarios = array();
		$arrHorariosId = array();
		$i = 0; $horaActual = ''; $diaActual = 0;
		
		while ( $i < count($arrHorarios) )
		{
			$horaActual = $arrHorarios[$i]['horaDesde'];
			
			for ( $j = 1 ; $j <= 7 ; $j++ )
					$horarios[$horaActual][$j] = array();
			
			while ( $i < count($arrHorarios) && $horaActual == $arrHorarios[$i]['horaDesde'] )
			{
				$diaActual = $arrHorarios[$i]['idDia'];
				
				while ( $i < count($arrHorarios) && $horaActual == $arrHorarios[$i]['horaDesde'] && $diaActual == $arrHorarios[$i]['idDia'] )
				{
					$arrHorarios[$i]['cssClass'] = $arrPuertaCssClasses[ $arrHorarios[$i]['idPuerta'] ];
					$horarios[$horaActual][$diaActual][] = $arrHorarios[$i];
					$arrHorariosId[] = array('id' => $arrHorarios[$i]['id']);
					$i++;
				}
			}
		}
		
		$textoAgenda = $this->db->get_where('estaticas', array('id' => self::ID_TEXTO_AGENDA_REGULAR ))->row_array();
		
		$this->newsNotification = true;
		$this->setTitle( 'Agenda regular' );
		$this->addContentPage('agenda_regular', array( 'puertas' => $puertas, 'horarios' => $horarios, 'arrHorariosId' => $arrHorariosId, 'textoAgenda' => $textoAgenda ));
		$this->show(array(), false, true);
	}
	
	public function especial()
	{
		magico_setLocale('es');
		
		$arrPuertaCssClasses = array();
		$puertas = $this->db->get('puertas')->result_array();
		
		foreach ( $puertas as &$puerta )
		{
			$puerta['cssClass'] = ContentType::cleanURL($puerta['title']);
			$arrPuertaCssClasses[$puerta['id']] = $puerta['cssClass'];
		}
		
		$esteMes = strftime('%B');
		$arrMeses = array($esteMes);
		$calendarios = array();
		
		$mkTimeHoy = mktime( 0, 0, 0, date('n'), 1, date('y') ); //sin esto los dias 31 son un desastre
		
		for ( $i = 1 ; $i < self::CANT_MESES ; $i++ )
		{
			$arrMeses[] = strftime('%B', strtotime("+$i month", $mkTimeHoy));
		}
		
		for ( $i = 0 ; $i < self::CANT_MESES ; $i++ )
		{
			$calendarios[ $arrMeses[$i] ] = $this->_createCalendario(date('m', strtotime("+$i month")), date('Y', strtotime("+$i month")));
		}
		
		//print_r($calendarios);
		
		$sqlHorarios = "
		SELECT
			dae.id,
			dae.title,
			TIME_FORMAT(dae.horaDesde, '%k:%i') AS horaDesde,
			dae.fecha,
			d.idPuerta,
			cu.url
		FROM
			disciplinas_actividades_especiales dae
		LEFT JOIN
			disciplinas d ON
			d.id = dae.idDisciplina
		INNER JOIN
			clean_urls cu ON
			cu.node_id = dae.id AND cu.table = 'disciplinas_actividades_especiales'
		WHERE
			dae.fecha > ? AND dae.fecha < ?
		ORDER BY
			dae.fecha, dae.horaDesde
		";
		
		$ultimoMes = strftime('%B', strtotime("+" . (self::CANT_MESES - 1) . " month"));
		
		reset($calendarios[ $esteMes ]);
		end($calendarios[ $ultimoMes ]);
		
		$arrActividades = $this->db->query($sqlHorarios, array( key($calendarios[$esteMes]), key($calendarios[$ultimoMes]) ))->result_array();

		foreach( $arrActividades as &$actividad )
		{
			$actividad['cssClass'] = $arrPuertaCssClasses[ $actividad['idPuerta'] ];
			
			foreach( $calendarios as &$calendario )
				if ( isset( $calendario[$actividad['fecha']] ) )
					$calendario[$actividad['fecha']][] = $actividad;
				
		}
		
		$textoAgenda = $this->db->get_where('estaticas', array('id' => self::ID_TEXTO_AGENDA_MENSUAL ))->row_array();
		
		
		$this->newsNotification = true;
		$this->setTitle( 'Agenda mensual' );
		$this->addContentPage('agenda_especial', array( 'puertas' => $puertas, 'calendarios' => $calendarios, 'cantMeses' => self::CANT_MESES, 'textoAgenda' => $textoAgenda ));
		$this->show(array(), false, true);
	}
	
	/**
	 * Devuelve un array cuyos keys son el listado de dias como aparecen en un calendario empezando del día lunes
	 * 
	 * @param type $mes
	 * @param type $anio
	 * @return string 
	 */
	private function _createCalendario($mes, $anio)
	{
		$primerDia = strtotime("$anio-$mes-1");
		$primerDiaSemana = date('N', $primerDia);
		$resta = intval($primerDiaSemana) - 1;
		$primerDiaCalendario = strtotime("-$resta days", $primerDia);
		
		$numUltimoDia = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
		
		$ultimoDiaMes = strtotime("$anio-$mes-$numUltimoDia");
		
		if ( date('w', $ultimoDiaMes) != 0 )
			$ultimoDiaCalendario = strtotime('next sunday', $ultimoDiaMes);
		else
			$ultimoDiaCalendario = $ultimoDiaMes;
		
		$currentDia = new DateTime(date('Y-m-d',$primerDiaCalendario));
		$ultimoDia = new DateTime(date('Y-m-d',$ultimoDiaCalendario));
		$arrCalendario = array();
		
		while( $currentDia <= $ultimoDia )
		{
			$arrCalendario[ $currentDia->format('Y-m-d') ] = array();
			$currentDia->modify( '+1 day' );
		}
		
		
		return $arrCalendario;
	}
}
