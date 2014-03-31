<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Configuracion extends ContentType {
	
	function __construct($id = null)
	{
		/*** Datos básicos ***/
		$this->name = "Configuracion";
		$this->table = "configuracion";
		$this->hayPaginaIndividual = false;
		
		/*** Fields ***/
		$this->fields['precioActividadesRegulares'] = new Textbox('Precio actividades regulares','El precio por un día de actividad regular');
		$this->fields['descuento2dias'] = new Textbox('Monto por dos días','El monto que se le suma al precio por dos días de actividades regulares');
		$this->fields['descuento3dias'] = new Textbox('Monto por tres días','El monto que se le suma al total por tres días de actividades regulares');
		$this->fields['descuento4dias'] = new Textbox('Monto por cuatro días','El monto que se le suma al total por cuatro días de actividades regulares. En el caso que un alumno quiera hacer más días se le sumará este monto también');
		$this->fields['descuentoAdelantado1dias'] = new Textbox('Días primer descuento actividades especiales', 'Cuantos días antes de la fecha establecida se aplica el primer descuento (ej. 20)');
		$this->fields['descuentoAdelantado2dias'] = new Textbox('Días segundo descuento actividades especiales', 'Cuantos días antes de la fecha establecida se aplica el segundo descuento (ej. 10)');
		$this->fields['diasVacanteInactivo'] = new Textbox('Días de vencimiento de vacante inactiva', 'Cuantos días puede un alumno ocupar una vacante sin haber pagado (ej. 10)');
		
		/*** Extras ***/	
		
		$this->setListableFields(array('precioActividadesRegulares'));
		
		parent::__construct($id);
	}
	
	function delete()
	{
		
	}
	
	function validate()
	{
		parent::validate();
		
		$this->ci->form_validation->set_rules('precioActividadesRegulares','','required');
		$this->ci->form_validation->set_rules('descuento2dias','','required');
		$this->ci->form_validation->set_rules('descuento3dias','','required');
		$this->ci->form_validation->set_rules('descuento4dias','','required');
		$this->ci->form_validation->set_rules('descuentoAdelantado1dias','','required');
		$this->ci->form_validation->set_rules('descuentoAdelantado2dias','','required');
		
		if ($this->ci->form_validation->run() == true)
			return null;
		else
		{
			return $this->ci->form_validation->get_error_array();
		}
	}
}