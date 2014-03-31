<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('application/libraries/admin/content_types/Puerta.php');

class Novedad extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Novedad";
		$this->table = "novedades";
		$this->hayPaginaIndividual = true;
		$this->returnURL = 'novedades/{title}';
		
		/*** Fields ***/
		$this->fields['fecha'] = new DatePicker();
		$this->fields['title'] = new Textbox('Título');
		$this->fields['imagenes'] = new FileUpload();
		$this->fields['video'] = new VideoField();
		$this->fields['resumen'] = new TextEditor('Resumen','Se ve en la home y en listado');
		$this->fields['textoDestacado'] = new TextEditor('Texto destacado', 'El texto que acompaña las imagenes');
		$this->fields['cuerpo'] = new TextEditor();
		$this->fields['publicado'] = new Checkbox('Publicado?','',true);
		
		
		/*** Extras ***/	
		$this->setListableFields(array('title'));
		
		$this->fields['resumen']->allowLists = false;
		$this->fields['textoDestacado']->allowLists = false;
		$this->fields['textoDestacado']->extraTags = array('Destacado' => 'h3');
		$this->fields['cuerpo']->extraTags = array('h2');
		$this->fields['imagenes']->isImage();
		$this->fields['imagenes']->dimensionesRecomendadas = '380x285';
		//$this->fields['video']->helptext .= ' Si se le ingresa un video este aparecerá en vez de las imágenes y el texto destacado.';
		
		
		parent::__construct($id);
	}
	
	function validate()
	{
		parent::validate();
		
		$this->ci->form_validation->set_rules('title','','required');
		$this->ci->form_validation->set_rules('cuerpo','','required');
		$this->fields['video']->validate();
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}