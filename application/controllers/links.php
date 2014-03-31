<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('MasterControllerColumbia.php');

class Links extends MasterControllerColumbia
{	
	function index()
	{	
		$links = magico_getList('links', 160, 100, null, 'weight ASC');
		$texto = $this->db->get_where('estaticas', array('id' => 5))->row_array();
		
		$this->setTitle( 'Links' );
		$this->addContentPage('links', array('links' => $links, 'texto' => $texto ));
		$this->show();
	}
}