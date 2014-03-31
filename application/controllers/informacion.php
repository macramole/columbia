<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class Informacion extends MasterControllerColumbia
{	
	public function index()
	{
		redirect();
	}
	
	public function estatica()
	{
		$estatica = magico_getByUrlClean();
		
		if ( $estatica )
		{
			$imagen = magico_getFile('estaticas', $estatica['id']);
			if ( $imagen )
				$estatica['imagen'] = magico_thumb ($imagen['filename'], 340, 220);
			
			$this->setTitle( $estatica['title'] );
			$this->addContentPage('institucional', array('estatica' => $estatica));
			$this->show();
		}
		else
		{
			redirect();
		}
	}
	
	public function equipo()
	{
		$textoEstatico = $this->db->get_where('estaticas', array('id' => 4))->row_array();
		
		$sqlEquipo = "
			SELECT
				e.*,
				( SELECT filename FROM files f WHERE f.node_id = e.id AND f.table = 'equipo' ORDER BY f.weight ASC, f.id DESC LIMIT 1 ) AS imagen,
				cu.url
			FROM 
				equipo e
			INNER JOIN
				clean_urls cu ON
				cu.node_id = e.id AND cu.table = 'equipo'
			ORDER BY
				idEquipoCategoria ASC,
				weight ASC
		";
		
		$arrEquipo = $this->db->query($sqlEquipo)->result_array();
		
		$this->db->order_by('weight ASC');
		$arrCategorias = $this->db->get('equipo_categorias')->result_array();
		
		foreach( $arrEquipo as $miembro )
		{
			if ( $miembro['idEquipoCategoria'] == 0 )
			{
				$miembro['imagen'] = magico_thumb($miembro['imagen'], 162, 108);
				$miembrosPrincipales[] = $miembro;
			}
			else
			{
				if ( !$miembro['miembroAnterior'] )
				{
					$miembro['imagen'] = magico_thumb($miembro['imagen'], 82, 56);
					$equipo[$miembro['idEquipoCategoria']][] = $miembro;
				}
				else
				{
					$equipo[$miembro['idEquipoCategoria']]['miembrosAnteriores'][] = $miembro;
				}
			}
		}
		
		foreach ( $arrCategorias as $categoria )
		{
			$categorias[$categoria['id']]['title'] = $categoria['title'];
			$categorias[$categoria['id']]['id'] = $categoria['id'];
		}
		
		$this->setTitle( 'Equipo' );
		$this->addContentPage('equipo', array(
			'miembrosPrincipales' => $miembrosPrincipales, 
			'equipo' => $equipo, 
			'texto' => $textoEstatico['textoDestacado'], 
			'categorias' => $categorias));
		
		$this->show();
	}
	
	public function miembroEquipo()
	{
		$equipo = magico_getByUrlClean();
		
		if ( $equipo )
		{
			magico_getImageToRow($equipo, 'equipo', 340, 220);
			
			$this->setTitle( $equipo['title'] );
			$this->setFacebookImage($equipo['imagen']);
			$this->addContentPage('docente_equipo', array('equipo' => $equipo, 'content_type' => 'Equipo', 'tipo' => 'Equipo'));
			$this->show();
		}
		else
		{
			redirect('equipo');
		}
	}
	
	public function nuestraCasa()
	{
		$nuestraCasa = magico_getByUrlClean();
		if ( !magico_getImagesToRow($nuestraCasa, 'estaticas', 340, 220) )
			unset($nuestraCasa['imagenes']);
		
		
		$this->setTitle( 'Nuestra casa' );
		$this->addContentPage('nuestra_casa', array('nuestraCasa' => $nuestraCasa));
		$this->show();
	}
}
