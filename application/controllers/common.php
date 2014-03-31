<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class Common extends MasterControllerColumbia
{	
	public function index()
	{
		$home = $this->db->get('home')->row_array();
		//magico_getImagesToRow($home, 'home', 750, 290);
		
		$files = magico_getFiles('home', $home['id']);

		if ( count($files) )
		{
			foreach( $files as $imagen )
			{
				$tmpImagen = array();
				$tmpImagen['url'] = magico_thumb($imagen['filename'], 750, 290);
				$tmpImagen['description'] = $imagen['description'];
				$home['imagenes'][] = $tmpImagen;
			}
		}
		else
		{
			$tmpImagen = array();
			$tmpImagen['url'] = magico_thumb('', 750, 290);
			$tmpImagen['description'] = '';
			$home['imagenes'][] = $tmpImagen;
		}
		
		/** NOVEDADES **/
		$arrNovedades = $this->db->query('SELECT * FROM novedades ORDER BY fecha DESC, id DESC LIMIT 2')->result_array();
		magico_getImageToArray($arrNovedades, 'novedades', 130, 75);
		
		/** NUESTRA CASA **/
		$nuestraCasa = $this->db->get_where('estaticas', array('id' => 3))->row_array();
		$nuestraCasa = $nuestraCasa['textoIz'];
		
		$this->addContentPage('home', array('home' => $home, 'arrNovedades' => $arrNovedades, 'nuestraCasa' => $nuestraCasa));
		$this->show();
	}
	
	public function subscribeNews()
	{
		$this->load->library('mcapi');
		$a = $this->mcapi->listSubscribe('f42c45d1d3', $_POST['email'], null, 'html', false, true);
		
		if ( $a )
			echo 'cool';
		else
			echo 'not cool';
	}
	
	public function error404()
	{
		$this->addContentPage('error404');
		$this->show();
	}
}
