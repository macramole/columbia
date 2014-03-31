<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class Novedades extends MasterControllerColumbia
{	
	var $CANT_PAGINADO = 5;
	
	public function index()
	{
		$arrNovedades = $this->db->query("
			SELECT
				n.*,
				( SELECT filename FROM files f WHERE f.node_id = n.id AND f.table = 'novedades' ORDER BY f.weight ASC, f.id DESC LIMIT 1 ) AS imagen,
				cu.url,
				( SELECT count(*) FROM novedades ) AS cantidad
			FROM
				novedades n
			INNER JOIN
				clean_urls cu ON
				cu.node_id = n.id AND cu.table = 'novedades'
			WHERE
				n.publicado = 1
			ORDER BY
				n.fecha DESC, n.id DESC
			LIMIT {$this->CANT_PAGINADO}
		")->result_array();
		
		foreach ( $arrNovedades as &$novedad )
		{
			$novedad['imagen'] = magico_thumb ($novedad['imagen'], 200, 130);
				
			if ( $novedad['url'] )
				$novedad['url'] = site_url($novedad['url']);
		}
		
		$this->setTitle( 'Novedades' );
		$this->addContentPage('novedades', array('arrNovedades' => $arrNovedades));
		$this->show();
	}
	
	public function ajaxMasNotas($lastId)
	{
		$arrNovedades = $this->db->query("
			SELECT
				n.*,
				( SELECT filename FROM files f WHERE f.node_id = n.id AND f.table = 'novedades' ORDER BY f.weight ASC, f.id DESC LIMIT 1 ) AS imagen,
				cu.url
			FROM
				novedades n
			INNER JOIN
				clean_urls cu ON
				cu.node_id = n.id AND cu.table = 'novedades'
			WHERE
				n.publicado = 1
			ORDER BY
				n.fecha DESC, n.id DESC
		")->result_array();
		
		$countNovedades = -1;
		
		foreach ( $arrNovedades as $key => &$novedad )
		{
			if ( $countNovedades == -1 && $novedad['id'] != $lastId )
			{
				unset($arrNovedades[$key]);
				continue;
			}
			elseif ( $novedad['id'] == $lastId )
			{
				unset($arrNovedades[$key]);
				$countNovedades++;
				continue;
			}
			
			if ($countNovedades >= $this->CANT_PAGINADO)
			{
				unset($arrNovedades[$key]);
				continue;
			}
			
			$novedad['imagen'] = magico_thumb ($novedad['imagen'], 200, 130);
				
			if ( $novedad['url'] )
				$novedad['url'] = site_url($novedad['url']);
			
			$countNovedades++;
		}
		
		$this->load->view('ajax_novedades', array('arrNovedades' => $arrNovedades));
	}
	
	public function nota()
	{
		$novedad = magico_getByUrlClean();
		
		if ( $novedad )
		{
			
			magico_getImagesToRow($novedad, 'novedades', 380, 285);
			
			if ( $novedad['video'] )
			{
				$this->load->library('autoembed');
				$a = $this->autoembed->parseUrl($novedad['video']);
				$novedad['video'] = $this->autoembed->getEmbedCode();
			}
			
			$this->newsNotification = true;
			$this->setTitle( $novedad['title'] );
			$this->setFacebookDescription($novedad['resumen']);
			$this->setFacebookImage($novedad['imagenes'][0]);
			$this->addContentPage('novedad', array('novedad' => $novedad));
			$this->show();
		}
		else
		{
			redirect('novedades');
		}
	}
}
