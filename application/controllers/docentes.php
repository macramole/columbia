<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class Docentes extends MasterControllerColumbia
{	
	public function index()
	{
        $arrDocentes = magico_getList('docentes', 162, 108, null, 't.title ASC');
        $this->db->order_by('weight ASC');
        $arrCategorias = $this->db->get('docentes_categorias')->result_array();
        $docentes = array();
        $categorias = array();
        
        foreach( $arrDocentes as &$docente )
            $docentes[$docente['idDocenteCategoria']][] = $docente;
        
        foreach( $arrCategorias as $categoria )
            $categorias[$categoria['id']] = $categoria;
		
		$this->setTitle( 'Docentes' );
		$this->addContentPage('docentes', array('docentes' => $docentes, 'categorias' => $categorias));
		$this->show();
	}

	public function docente()
	{
		$docente = magico_getByUrlClean();
		
		if ( $docente )
		{
			magico_getImageToRow($docente, 'docentes', 340, 220);
			
			$sqlActividades = "
				SELECT
					h.id,
					TIME_FORMAT(h.horaDesde, '%H:%i') AS horaDesde,
					TIME_FORMAT(h.horaHasta, '%H:%i') AS horaHasta,
					( SELECT GROUP_CONCAT(dias.title ORDER BY dias.id ASC SEPARATOR ', ') FROM disciplinas_actividades_dias dad INNER JOIN dias ON dias.id = dad.idDia WHERE dad.idActividad = h.id GROUP BY dad.idActividad  ) dias,
					s.title AS sala,
					cu.url,
					d.title AS disciplina
				FROM
					disciplinas_actividades	h
				INNER JOIN
					clean_urls cu ON
					cu.node_id = h.idDisciplina AND cu.table = 'disciplinas'
				INNER JOIN
					salas s ON
					s.id = h.idSala
				INNER JOIN
					disciplinas d ON
					d.id = h.idDisciplina
				INNER JOIN
					`disciplinas_actividades_docentes` dad ON
					dad.`idActividad` = h.id
				WHERE
					dad.`idDocente` = ?
			";
			
			$docente['actividades'] = $this->db->query($sqlActividades, array( $docente['id'] ))->result_array();
			
			foreach( $docente['actividades'] as &$horario )
			{
				if ( $horario['dias'] &&  strrpos($horario['dias'], ',') )
					$horario['dias'] = substr_replace($horario['dias'], ' y ', strrpos($horario['dias'], ','), 1 );
			}
			
			$sqlActividadesEspeciales = "
				SELECT
					dae.*,
					cu.url
				FROM
					`disciplinas_actividades_especiales_docentes` daed
				INNER JOIN
					`disciplinas_actividades_especiales` dae ON
					daed.`idActividadEspecial` = dae.`id`
				INNER JOIN
					clean_urls cu ON
					cu.node_id = dae.`id` AND cu.table = 'disciplinas_actividades_especiales'
				WHERE
					daed.`idDocente` = ? AND
					dae.fecha > NOW()
				ORDER BY
					dae.`fecha` ASC, dae.`horaDesde` ASC
			";
			
			$docente['actividades_especiales'] = $this->db->query($sqlActividadesEspeciales, array( $docente['id'] ))->result_array();
			
			magico_setLocale('es');
			
			foreach( $docente['actividades_especiales'] as &$actividadEspecial )
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
			}
			
			$this->setTitle( $docente['title'] );
			$this->setFacebookImage($docente['imagen']);
			$this->addContentPage('docente_equipo', array('equipo' => $docente, 'content_type' => 'Docente', 'tipo' => 'Docentes'));
			$this->show();
		}
		else
		{
			redirect('docentes');
		}
	}
}
