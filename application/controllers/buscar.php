<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class Buscar extends MasterControllerColumbia
{	
	function index()
	{	
		if ( trim($_GET['q']) && strlen($_GET['q']) > 3 )
		{
			$arrCriteria = explode(' ',trim($_GET['q']));
			
			foreach ( $arrCriteria as $key => $criteria )
			{
				$htmlCriteria = htmlentities($criteria, ENT_COMPAT, 'UTF-8');
				
				if ( $htmlCriteria != $criteria )
					$arrCriteria[] = $htmlCriteria . '*';
				
				$arrCriteria[$key] .= '*';
			}
			
			$criteria = mysql_real_escape_string( implode(' ', $arrCriteria) );
			
			$sql = "
				SELECT 
					* 
				FROM (
					SELECT 
						title,
						textoDestacado AS texto,
						MATCH(title,texto) AGAINST ('$criteria' IN BOOLEAN MODE) AS relevance,
						'Disciplina' AS tipo,
						cu.url AS url
					FROM 
						disciplinas d
					INNER JOIN
						clean_urls cu ON
						cu.node_id = d.id AND
						cu.table = 'disciplinas'
					WHERE
						MATCH(title,texto) AGAINST ('$criteria' IN BOOLEAN MODE)

					UNION

					SELECT 
						title,
						texto,
						MATCH(title,texto) AGAINST ('$criteria' IN BOOLEAN MODE) - 0.7 AS relevance,
						'Docente' AS tipo,
						cu.url AS url
					FROM 
						docentes doc
					INNER JOIN
						clean_urls cu ON
						cu.node_id = doc.id AND
						cu.table = 'docentes'
					WHERE
						MATCH(title,texto) AGAINST ('$criteria' IN BOOLEAN MODE)

					UNION

					SELECT 
						title,
						textoDestacado as texto,	
						MATCH(title,cuerpo) AGAINST ('$criteria' IN BOOLEAN MODE) AS relevance,
						'Actividad' AS tipo,
						cu.url AS url
					FROM 
						disciplinas_actividades_especiales dae
					INNER JOIN
						clean_urls cu ON
						cu.node_id = dae.id AND
						cu.table = 'disciplinas_actividades_especiales'
					WHERE
						MATCH(title,cuerpo) AGAINST ('$criteria' IN BOOLEAN MODE) AND
						fecha >= NOW()

					UNION

					SELECT 
						title,
						resumen as texto,
						MATCH(title,cuerpo) AGAINST ('$criteria' IN BOOLEAN MODE) - 0.7 AS relevance ,
						'Novedad' AS tipo,
						cu.url AS url
					FROM 
						novedades n
					INNER JOIN
						clean_urls cu ON
						cu.node_id = n.id AND
						cu.table = 'novedades'
					WHERE
						MATCH(title,cuerpo) AGAINST ('$criteria' IN BOOLEAN MODE)

				) a
				ORDER BY
					relevance DESC
				LIMIT 0, 20
			";
			
			$arrBusqueda = $this->db->query($sql)->result_array();
			
			if ( count($arrBusqueda) )
			{
				foreach ( $arrBusqueda as &$result )
				{
					$result['resumen'] = substr( html_entity_decode(strip_tags($result['texto']), ENT_COMPAT, 'UTF-8'), 0, 200 ) . '...';
					
				}
			}
		}
		
		$this->addContentPage('busqueda', array('arrBusqueda' => $arrBusqueda));
		$this->show();
	}
}