<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once('application/libraries/admin/content_types/Puerta.php');
include_once('application/libraries/admin/content_types/Docente.php');
include_once('application/libraries/admin/content_types/Sala.php');

class Disciplina extends ContentType {
	
	public static $arrDias = array('Lunes' => 'Lunes', 'Martes' => 'Martes', 'Miércoles' => 'Miércoles', 'Jueves' => 'Jueves', 'Viernes' => 'Viernes', 'Sábado' => 'Sábado', 'Domingo' => 'Domingo');
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Disciplina";
		$this->table = "disciplinas";
		$this->hayPaginaIndividual = true;
		$this->returnURL = '{idPuerta}/{title}';
		
		/*** Fields ***/
		$this->fields['idPuerta'] = new DatabaseSelect( new Puerta() );
		$this->fields['imagen'] = new FileUpload();
		$this->fields['title'] = new Textbox('Nombre');
		$this->fields['textoDestacado'] = new TextEditor('Texto destacado');
		$this->fields['textoCorto'] = new TextEditor('Texto corto', 'Este texto aparecerá en la página de puertas');
		$this->fields['texto'] = new TextEditor('Texto descriptivo');
		$this->fields['requisitos'] = new TextEditor();
		$this->fields['arancelYDescuentos'] = new TextEditor('Arancel y descuentos');
		
        $this->fields['gratis'] = new Checkbox();
		$this->fields['precio'] = new Textbox('Precio', 'Se usará este precio en vez del de la configuración general');
		$this->fields['descuento2dias'] = new Textbox('Monto por dos días', 'El monto que se le suma al precio por dos días de esta actividad');
		$this->fields['descuento3dias'] = new Textbox('Monto por tres días', 'El monto que se le suma al total por tres días de esta actividad');
		$this->fields['descuento4dias'] = new Textbox('Monto por cuatro días', 'El monto que se le suma al total por cuatro días de esta actividad. En el caso que un alumno quiera hacer más días se le sumará este monto también');
		
		/*** Extras ***/	
		$this->fields['imagen']->isImage();
		$this->fields['imagen']->dimensionesRecomendadas = '340x225';
		$this->fields['idPuerta']->addNew = false;		
		$this->fields['texto']->extraTags = array('h2');
		$this->fields['textoDestacado']->allowLists = false;
		$this->fields['requisitos']->allowLists = false;
		$this->fields['arancelYDescuentos']->allowLists = false;
		$this->fields['arancelYDescuentos']->allowLinks = false;
		$this->setListableFields(array('title', 'idPuerta'));
		
		parent::__construct($id);
	}
	
	function validate()
	{
		parent::validate();
		
		$this->ci->form_validation->set_rules('title','','required');
		
		if ( 
			intval($_POST['precio']) ||
			intval($_POST['descuento2dias']) ||
			intval($_POST['descuento3dias']) ||
			intval($_POST['descuento4dias']) )
		{
			$this->ci->form_validation->set_rules('precio','','required');
			$this->ci->form_validation->set_rules('descuento2dias','','required');
			$this->ci->form_validation->set_rules('descuento3dias','','required');
			$this->ci->form_validation->set_rules('descuento4dias','','required');
		}
			
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}