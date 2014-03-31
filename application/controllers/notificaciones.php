<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');
include_once('application/libraries/admin/content_types/Alumno.php');
include_once('application/libraries/admin/content_types/ActividadEspecial.php');

class Notificaciones extends MasterControllerColumbia
{	
	const VACANTE_INACTIVO_MINIMO = 1;
	
	function index()
	{	
		redirect('/');
	}
	
	function checkActividadesInactivas()
	{
		$logRegulares = $this->checkActividadesRegulares();
		$logEspeciales = $this->checkActividadesEspeciales();
		
		$cuerpo = "
			<b>Regulares</b><br>
			Desactivadas: $logRegulares[desactivadas] <br />
			Archivadas: $logRegulares[archivadas] <br /><br />
		
			<b>Especiales</b><br>
			Desactivadas: $logEspeciales[desactivadas] <br />
			Archivadas: $logEspeciales[archivadas]
		";
		
		echo $cuerpo;
		
		//magico_sendmail('leandrogarber@gmail.com', 'Fundacion Columbia - checkActividadesInactivas()', $cuerpo, 'localhost@parleboo.com');
	}
	
	function checkActividadesEspeciales()
	{
		$conf = magico_getGlobalConfig();
		$cantInscripcionesDesactivadas = $cantInscripcionesArchivadas = 0;
		$diasVacanteInactivo = intval($conf['diasVacanteInactivo']) > self::VACANTE_INACTIVO_MINIMO ? $conf['diasVacanteInactivo'] : self::VACANTE_INACTIVO_MINIMO ;
		
		$sqlAVencer = "
			SELECT
				aae.id,
				aae.`idAlumno`,
				aae.`idDisciplinaActividadEspecial`,
				aae.`fechaHasta`,
				aae.`activo`,
				aae.`pagoVacante`,
				dae.`fecha` AS actividadEspecialFechaDesde,
				dae.`fechaHasta` AS actividadEspecialFechaHasta,
				dae.`modalidadPago`,
				DATEDIFF( aae.fechaHasta, NOW() ) AS diasVencidos,
				IF ( DATEDIFF( dae.fechaHasta, NOW() ) < 0, 1, 0 ) AS yaPaso,
				IF ( DATEDIFF( dae.fecha, NOW() ) < 0, 1, 0 ) AS yaEmpezo
			FROM
				alumnos_actividades_especiales aae
			INNER JOIN
				disciplinas_actividades_especiales dae ON
				dae.`id` = aae.`idDisciplinaActividadEspecial`
				
		";
		
		/*

			Cuando archivar:

			(1) Cuando ya paso actividadEspecialFechaHasta
			(2) Cuando pasaron mas de diez dias de fechaHasta, es mensual
		    (3) Cuando nunca pagó y hoy es > a actividadEspecialFechaDesde
			
			Cuando Desactivar y mandar mail
			
		 * (4) Cuando pasaron menos de diez dias de fechaHasta, es mensual

		*/
		
		
		$arrAVencer = $this->db->query($sqlAVencer)->result_array();
		
		foreach ( $arrAVencer as &$aVencer )
		{
			if ( !$aVencer['yaPaso'] )
			{
				if ( $aVencer['diasVencidos'] !== null )
				{
					if ( $aVencer['modalidadPago'] == ActividadEspecial::MODALIDAD_MONTO_MENSUAL )
					{
						if ( $aVencer['diasVencidos'] < -$diasVacanteInactivo ) //(2)
						{
							Alumno::archivarInscripcionEspecial( $aVencer['idAlumno'], $aVencer['idDisciplinaActividadEspecial'] );
							$cantInscripcionesArchivadas++;
						}
						elseif ( $aVencer['diasVencidos'] < 0 ) //(4)
						{
							Alumno::desactivarInscripcionEspecial($aVencer['idAlumno'], $aVencer['idDisciplinaActividadEspecial']);
							$cantInscripcionesDesactivadas++;
						}
							
					}
				}
				else //nunca pagó
				{
					if ( $aVencer['yaEmpezo'] && !$aVencer['pagoVacante'] ) //(3)
					{
						Alumno::archivarInscripcionEspecial( $aVencer['idAlumno'], $aVencer['idDisciplinaActividadEspecial'] );
						$cantInscripcionesArchivadas++;
					}
					
				}
			}
			else //(1)
			{
				Alumno::archivarInscripcionEspecial($aVencer['idAlumno'], $aVencer['idDisciplinaActividadEspecial']);
				$cantInscripcionesArchivadas++;
			}
		}
		
		return array( 'archivadas' => $cantInscripcionesArchivadas, 'desactivadas' => $cantInscripcionesDesactivadas ); 
	}
	
	function checkActividadesRegulares()
	{
		$conf = magico_getGlobalConfig();
		$cantInscripcionesDesactivadas = $cantInscripcionesArchivadas = 0;
		$diasVacanteInactivo = intval($conf['diasVacanteInactivo']) > self::VACANTE_INACTIVO_MINIMO ? $conf['diasVacanteInactivo'] : self::VACANTE_INACTIVO_MINIMO ;
		
		//Estan agrupados por idDisciplina así no mando mail por cada horario
		$sqlAVencer = "
			SELECT 
				IF( aad.fechaHasta IS NOT NULL, aad.fechaHasta , aad.fechaReserva ) AS fechaHasta,
				aad.activo,
				da.`idDisciplina`,
				aad.`idAlumno`,
				a.nombre,
				a.apellido,
				a.email,
				ABS( DATEDIFF( IF( aad.fechaHasta IS NOT NULL, aad.fechaHasta , aad.fechaReserva ) , NOW())) AS diasVencidos
			FROM
				alumnos_actividades_dias aad
			INNER JOIN
				`disciplinas_actividades_dias` dad ON
				dad.`id` = aad.`idDisciplinaActividadDia`
			INNER JOIN
				`disciplinas_actividades` da ON
				da.`id` = dad.`idActividad`
			INNER JOIN
				alumnos a ON
				a.`id` = aad.`idAlumno`
			WHERE
				DATEDIFF( IF( aad.fechaHasta IS NOT NULL, aad.fechaHasta , aad.fechaReserva ) , NOW()) < 0
			GROUP BY
				aad.idAlumno, da.`idDisciplina`
			ORDER BY
				idAlumno
		";

		$arrAVencer = $this->db->query($sqlAVencer)->result_array();

		foreach ( $arrAVencer as &$aVencer )
		{
			if ( $aVencer['diasVencidos'] <= $diasVacanteInactivo )
			{
				if ( $aVencer['activo'] == 1 )
				{
					$alumno = array(
						'id' => $aVencer['idAlumno'], 
						'nombre' => $aVencer['nombre'], 
						'apellido' => $aVencer['apellido'], 
						'email' => $aVencer['email']
					);

					Alumno::desactivarInscripcion($alumno['id'], $aVencer['idDisciplina']);
					$this->enviarAvisoRegular('vencido', $aVencer['idDisciplina'], $alumno);
					$cantInscripcionesDesactivadas++;
				}
			}
			else
			{
				Alumno::archivarInscripcion($aVencer['idAlumno'], $aVencer['idDisciplina']);
				$cantInscripcionesArchivadas++;
				//Enviar mail de que se canceló la suscripción
			}
		}
		
		
		return array( 'archivadas' => $cantInscripcionesArchivadas, 'desactivadas' => $cantInscripcionesDesactivadas ); 
	}
	
	private function enviarAvisoRegular($status, $idDisciplina, $alumno)
	{
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
			GROUP BY
				horaDesde, horaHasta
			ORDER BY
				dis.title, dad.`idDia`, da.`horaDesde`
		";

		$arrHorarios = $this->db->query($sqlHorarios, array( $alumno['id'], $idDisciplina ))->result_array();
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
			'nombre' => $alumno['nombre'] . ' ' . $alumno['apellido']
		), true);
		
		magico_sendmail($alumno['email'], 'Fundación Columbia - Inscripción Vencida', $mail, 'info@fundacioncolumbia.org');
	}
}