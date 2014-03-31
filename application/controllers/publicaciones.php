<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class Publicaciones extends MasterControllerColumbia
{	
	var $CANT_PAGINADO = 5;
	
	public function index()
	{
		$arrPublicaciones = $this->db->query("
			SELECT
				p.*,
				( SELECT filename FROM files f WHERE f.node_id = p.id AND f.table = 'publicaciones' AND f.flag = 0 ORDER BY f.weight ASC, f.id DESC LIMIT 1 ) AS imagen,
				( SELECT filename FROM files f WHERE f.node_id = p.id AND f.table = 'publicaciones' AND f.flag = 1 ORDER BY f.weight ASC, f.id DESC LIMIT 1 ) AS pdf
			FROM
				publicaciones p
			ORDER BY
				p.fecha DESC, p.id DESC
		")->result_array();
		
		foreach ( $arrPublicaciones as &$publicacion)
		{
			$publicacion['imagen'] = magico_thumb ($publicacion['imagen'], 200, 130);
			$publicacion['fecha'] = date('d/m/Y',strtotime($publicacion['fecha']));
			
			if ( $publicacion['pdf'] )
				$publicacion['link'] = 'uploads/' . $publicacion['pdf'];
		}
		
		$this->setTitle( 'Publicaciones / Prensa' );
		$this->addContentPage('publicaciones', array('publicaciones' => $arrPublicaciones));
		$this->show();
	}
}
