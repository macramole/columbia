<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('application/libraries/admin/content_types/Puerta.php');

class Estatica extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Estatica";
		$this->table = "estaticas";
		$this->hayPaginaIndividual = true;
		$this->returnURL = 'informacion/{title}';
		
		/*** Fields ***/
		$this->fields['title'] = new Textbox('Título');
		$this->fields['imagen'] = new FileUpload();
		$this->fields['textoDestacado'] = new TextEditor('Texto destacado', 'El texto que acompaña la imagen');
		$this->fields['textoIz'] = new TextEditor('Texto izquierdo', 'Texto principal que aparece del lado izquierdo');
		$this->fields['textoDe'] = new TextEditor('Texto derecho', 'Texto principal que aparece del lado derecho');
		
		
		/*** Extras ***/	
		$this->setListableFields(array('title'));
		$this->fields['textoDestacado']->allowLists = false;
		$this->fields['textoIz']->extraTags = array('h2');
		$this->fields['textoDe']->extraTags = array('h2');
		$this->fields['imagen']->isImage();
		$this->fields['imagen']->maxFilesAllowed = 1;
		$this->fields['imagen']->dimensionesRecomendadas = '340x220';
		
		parent::__construct($id);
	}
	
	function validate()
	{
		parent::validate();
		
		$this->ci->form_validation->set_rules('title','','required');
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}