<?php
/*
 Mâgico
 http://www.parleboo.com
 Copyright 2012 Leandro Garber <leandrogarber@gmail.com>
 Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * De esta clase heredan los distintos tipos de contenido de del sitio
 */
class ContentType {
	
	const OPERATION_CREATE = 'create';
	const OPERATION_EDIT = 'edit';
	
	const FIELD_LISTABLE_ALL = 'all';
	const CLEAN_URLS_TABLE = 'clean_urls';
	
    const FOREIGNKEY_TYPE_ONE_TO_MANY = 1;
    const FOREIGNKEY_TYPE_MANY_TO_MANY = 2;
    
	//Objeto de codeigniter
	protected $ci;
	//Array de objetos que extienden de field. Se establece en la clase hija
	public $fields = array();
	/**
     * Si es foreignKey un array con los content fields que lo usan, al eliminar el contenido, eliminará todo lo asociado. 
     * 
     * Caso FOREIGNKEY_TYPE_ONE_TO_MANY El campo foreign debe llamarse id<nombre_de_contentType>
     * El formato es array( 'nombreContentField', 'nombreContentField2', etc )
     * 
     * Caso FOREIGNKEY_TYPE_MANY_TO_MANY El campo foreign debe llamarse id<nombre_de_contentType>
     * El formato es array( 'nombreTablaManyToMany', 'nombreTablaManyToMany2', etc )
     */
	public $isForeignKey = false;
    public $foreignKeyType = self::FOREIGNKEY_TYPE_ONE_TO_MANY;
	//Esta es la tabla a la que se refiere este content type (si es más de una se deberá overridear el método save)
	public $table;
	//Este es el nombre que se mostrará para este tipo de contenido
	public $name;
	//Si está editando
	public $id = null;
	//Donde volverá cuando se agregue ej: document/{id}/{title} (sin barra ni al comienzo ni al final)
	public $returnURL = '';
	//Si hay, el listado mostrará una acción extra para ver la página de ese contenido
	public $hayPaginaIndividual = true;
	//Cuantos items por página cuando se lista el contenido desde el backend /TODO
	public $itemsPorPaginaList = 10;
	//Para establecer un mensaje de borrado especial para este content
	public $mensajeBorrado = null;
	//Si la tabla tiene weight (orden), cuando se agrega un nuevo elemento le establece bien el weight
	public $hasWeight = false;
	//Si este contenido está en distintos idiomas. Si lo está, un array con los campos que no cambian entre idioma o TRUE si ningún campo se mantiene (las imágenes siempre se mantienen)
	public $i18n = false;
	//Si el contenido no esta disponible en el idioma pero existe el mismo contenido en otro idioma (uso interno)
	protected $translating = false;
	/*
	 *  array('title','image','link')
	 */
	public $arrCustomListButtons = array();
	
	function __construct($id = null)
	{
		$this->ci =& get_instance();
		
		$this->setFieldsParent();
        
		if ( $id )
		{
			$this->id = $id;
			$this->setFieldsValues();
		}
	}
	
	/**
	 * A veces es necesario saber el padre del field, asi que lo establezclo automaticamente aqui
	 * 
	 * Es recursiva para darle el parent a los fields de los fields si es que hay
	 */
	protected function setFieldsParent( &$context = null)
	{
		if ( $context == null )
			$context =& $this;
		
		foreach ($context->fields as &$field)
		{
			$field->setParent($context);
			
			if ( $field->fields )
				$this->setFieldsParent( $field );
		}
	}
	
	function setFieldsValues()
	{
		$arrContent = $this->ci->db->get_where($this->table, array('id' => $this->id))->result_array();
		
		if ( $this->i18n )
			$lang = $this->ci->uri->segment(1);
		
		if ( !$lang )
			$row = $arrContent[0];
		else
		{
			$row = null;
			
			foreach ( $arrContent as $content )
			{
				if ( $content['language'] == $lang )
				{
					$row = $content;
					break;
				}
			}
			
			if ( $row == null )
			{
				$row = $arrContent[0];
				$this->translating = true;
			}
		}
		
		foreach ( $row as $name => $field)
		{
			if ( isset($this->fields[$name]) )
			{
				$this->fields[$name]->value = $field;
			}
		}
		
		foreach ( $this->fields as $field )
		{
			if ( $field->autoSave )
					$field->setFieldValue($this->table, $this->id, $row);
		}
	}
	
	/**
	 * Se pasan los nombres de los fields como array y los marca como listables (asi es más rápido).
	 * Si se manda un field que es foreign key el listado mostrara el title de la tabla
	 * Tambien se peude mandar la constante FIELD_LISTABLE_ALL para todos
	 */
	function setListableFields($arrFields)
	{
		if (is_array($arrFields) )
		{
			foreach ($arrFields as $field)
			{
				$this->fields[$field]->setListable();
			}
		}
		elseif ( $arrFields == self::FIELD_LISTABLE_ALL )
		{
			foreach ( $this->fields as $key => $field )
			{
				$this->fields[$key]->setListable();
			}
		}
	}
	
	/*
	 * Devuelve los fields que son listables. $onlyNames devuelve sólo el nombre.
	 */
	function getListableFields( $onlyNames = false )
	{
		$arrFields = array();
		
		foreach ( $this->fields as $field )
		{
			if ( $field->isListable() )
			{
				if ($onlyNames)
					$arrFields[] = $field->name;
				else
					$arrFields[] = $field;
			}
		}
		
		return $arrFields;
	}
	
	/**
	 * Devuelve array con la lista paginada (o null para sin paginar) /TODO
	 * 
	 * @param array $where
	 * @param int $page
	 * @return array
	 */
	function getList($where = null, $page = null)
	{
		$arrFields = $this->getListableFields(true);
		$arrFields[] = 'id';
		
		//checkeo por foreign keys y agrego nombre de tabla a los fields. El foreign key debe tener un field title.
		foreach ( $arrFields as $key => $field )
		{
			if ( $this->fields[$field]->isForeignKey )
			{
				$arrFields[$key] = "{$this->fields[$field]->isForeignKey}.title AS `{$this->fields[$field]->label}`";
				
				if ( ( $this->fields[$field]->content_type && !$this->fields[$field]->content_type->i18n ) || !$this->fields[$field]->content_type )
					$joinOn = "{$this->fields[$field]->isForeignKey}.id = {$this->table}.$field";
				else
					$joinOn = "{$this->fields[$field]->isForeignKey}.id = {$this->table}.$field AND {$this->fields[$field]->isForeignKey}.language = '{$this->ci->uri->segment(1)}'";
				
				$this->ci->db->join($this->fields[$field]->isForeignKey, $joinOn, 'left');
			}
			else
			{
				$arrFields[$key] = "{$this->table}.$field";
			}
		}
		
		$this->ci->db->select($arrFields);
		$this->ci->db->order_by('id ASC');
		
		if ( !$this->i18n )
			return $this->ci->db->get_where($this->table, $where)->result_array();
		else
		{
			$where = array_merge(array("{$this->table}.language" => $this->ci->uri->segment(1)), $where ? $where : array());
			return $this->ci->db->get_where($this->table, $where)->result_array();
		}
			
	}
	
	function getListJSON($page = null)
	{
		echo json_encode($this->getList($page));
	}
	
	/**
	 * Devuelve el tipo de operación, create o edit
	 */
	function getOperation()
	{
		if ( !$this->id )
			return self::OPERATION_CREATE;
		else
			return self::OPERATION_EDIT;
	}
	
	/**
	 * Guarda los datos por POST en la base de datos. Se llama desde el controller ABM
	 * 
	 * @param mixed $isCloning False or the id of the model being cloned
	 * 
	 * @return int El id 
	 */
	function save($isCloning = false)
	{
		$saveFields = array();
		
		foreach ( $this->fields as $field )
		{
			if ( !$field->autoSave && !$field->disabled )
			{
				$value = $_POST[$field->name] ? $_POST[$field->name] : '';
				
				if ( $field->safeHtml )
					$saveFields[$field->name] = htmlentities($value, ENT_NOQUOTES , 'UTF-8' );
				else
					$saveFields[$field->name] = $value;
			}
				
		}
		
		//si tiene weight y esta creando que lo agregue Ãºltimo
		if ( $this->hasWeight && $this->getOperation() == self::OPERATION_CREATE )
		{
			$maxWeight = $this->ci->db->query("SELECT MAX(weight) as weight FROM {$this->table}")->row_array();
			
			$saveFields['weight'] = intval($maxWeight['weight']) + 1;
		}
		
		//Guardo los campos compartidos
		if ( $this->i18n )
		{
			$saveFields['language'] = $this->ci->uri->segment(1);
			
			if ( $this->getOperation() == self::OPERATION_EDIT && is_array($this->i18n) )
			{
				$arrI18NContent = $this->ci->db->query("SELECT language FROM {$this->table} WHERE id = ? AND language <> ? ", array( $this->id, $saveFields['language'] ))->result_array();
				
				if ( count($arrI18NContent) )
				{
					$i18nSaveFields = array();
					
					foreach ( $this->i18n as $i18nField )
					{
						$i18nSaveFields[$i18nField] = $saveFields[$i18nField];
					}
					
					$this->ci->db->where('id', $this->id);
					$this->ci->db->update( $this->table, $i18nSaveFields );
				}
			}
			
			if ( $this->isTranslating() )
				$saveFields['id'] = $this->id;
		}
			
		
		//Se guarda todo normalmente
		if ( $this->getOperation() == self::OPERATION_CREATE || $this->isTranslating() )
		{
			$this->ci->db->insert($this->table, $saveFields);
			
			if ( !$this->isTranslating() )
				$this->id = $this->ci->db->insert_id();
		}
		else
		{
			if ( !$this->i18n )
				$this->ci->db->where('id', $this->id);
			else
				$this->ci->db->where( array( 'id' => $this->id, 'language' => $saveFields['language'] ) );

			$this->ci->db->update($this->table, $saveFields);
		}
		
		//Los campos que se guardan solos
		foreach ( $this->fields as $field )
		{
			if ( $field->autoSave && !$field->disabled )
			{
				if ( !$isCloning || !method_exists($field, 'cloneField') )
					$field->save($this->table, $this->id);
				else
					$field->cloneField($this->table, $isCloning, $this->id);
			}
		}
		
		if ( $this->hayPaginaIndividual )
			$this->saveClean();
		
		return $this->id;
	}
	/**
	 * Guarda un field en particular. Se usa para el ALOHA editor. $_POST['data'] tiene la data a modificar.
	 * 
	 * @param string $field Nombre del field
	 */
	public function saveField( $field )
	{
		if ( !$this->i18n )
			$this->ci->db->where('id', $this->id);
		else
			$this->ci->db->where( array( 'id' => $this->id, 'language' => $this->ci->uri->segment(1) ) );
	
		if ( $this->fields[$field]->safeHtml )
			$data = htmlentities($_POST['data'], ENT_NOQUOTES , 'UTF-8' );
		else
			$data = $_POST['data'];
		
		$this->ci->db->update($this->table, array( $field => $data ) );
	}
	
	public static function cleanURL( $string )
	{
		setlocale(LC_ALL, 'en_US.UTF8');
        
        $clean = html_entity_decode($string);
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $clean);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);

        return $clean;
	}
	
	/**
	 * Guarda en clean_urls la url del content_type. Si ya existe el titulo le agrega un numero.
	 */
	function saveClean()
	{	
		$arrDelete = array('table' => $this->table, 'node_id' => $this->id);
		
		if ( $this->i18n )
			$arrDelete['language'] = $this->ci->uri->segment(1);
		
		$this->ci->db->delete(self::CLEAN_URLS_TABLE, $arrDelete);
		$urlClean = $this->buildReturnURL(false);
		$num = 0; //si ya existe agregarle un número
		
		$rowClean = $this->ci->db->get_where(self::CLEAN_URLS_TABLE, array('url' => $urlClean))->row_array();
		$urlCleanNum = '';
		
		while ( $rowClean && $rowClean['node_id'] != $this->id )
		{
			$urlCleanNum = $urlClean . '-' . $num;
			$rowClean = $this->ci->db->get_where(self::CLEAN_URLS_TABLE, array('url' => $urlCleanNum))->row_array();
			$num++;
		}
		
		if ( $urlCleanNum == '')
			$urlToAdd = $urlClean;
		else
			$urlToAdd = $urlCleanNum;
		
		$arrInsert = array('table' => $this->table, 'node_id' => $this->id, 'url' => $urlToAdd);
		
		if ( $this->i18n )
			$arrInsert['language'] = $this->ci->uri->segment(1);
		
		$this->ci->db->insert(self::CLEAN_URLS_TABLE, $arrInsert);
	}
	
	function getCleanUrl()
	{
		
		$arrGet = array('table' => $this->table, 'node_id' => $this->id);
		
		if ( $this->i18n )
			$arrGet['language'] = $this->ci->uri->segment(1);
		
		$row = $this->ci->db->get_where(self::CLEAN_URLS_TABLE, $arrGet)->row_array();
		
		if ( $row )
			return site_url($row['url']);
	}
	
	function getReturnURL()
	{
		if ($this->hayPaginaIndividual)
			return $this->getCleanUrl();
		else
			return $this->buildReturnURL();
	}
	
	/**
	 * Devuelve la URL del contenido
	 * 
	 * @param boolean $absolute Si devuelve la url absoluta o relativa
	 * @return String La url 
	 */
	function buildReturnURL($absolute = true)
	{
		$returnUrl = $this->returnURL;
		
		foreach ( $this->fields as $field )
		{
			if ( is_array($_POST[$field->name]) ) //sino tira error el urlencode
                continue;
            
			/*
			 * Si tiene foreignKey se fija el clean de ese contenido. Esto sirve para categorias
			 */
			if ( !$field->isForeignKey )
				$returnUrl = str_replace('{' . $field->name . '}', $_POST[$field->name] ? $this->cleanURL($_POST[$field->name]) : $this->cleanURL($field->value), $returnUrl);
			else
			{
				if ( !$field->content_type->i18n )
					$foreignCleanUrl = magico_urlclean($field->isForeignKey, $_POST[$field->name], null, false);
				else
					$foreignCleanUrl = magico_urlclean($field->isForeignKey, $_POST[$field->name], $this->ci->uri->segment(1), false);
				
				$returnUrl = str_replace('{' . $field->name . '}', $foreignCleanUrl, $returnUrl);
			}
		}
		
		$returnUrl = str_replace('{id}', $this->id, $returnUrl);
		
		if ( $absolute )
			return site_url($returnUrl);
		else
			return $returnUrl;
	}
	
	function delete()
	{
		if (!$this->id)
			return;
		else
		{
			
			if ( !$this->i18n )
			{
				//Primero los campos que se eliminan solos
				foreach ( $this->fields as $field )
				{
					if ( $field->autoSave )
						$field->delete($this->table, $this->id);
				}

				$this->ci->db->delete($this->table, array( 'id' => $this->id) );
			}
			else
			{
				$lang = $this->ci->uri->segment(1);
				
				$arrCount = $this->ci->db->query("SELECT COUNT(*) AS cantidad FROM {$this->table} WHERE id = ?", array( 'id' => $this->id ) )->row_array();
				
				if ( intval($arrCount['cantidad']) == 1 )
				{
					//Si queda solo este borrar los campos que se autograban (estos no son traducibles de momento)
					foreach ( $this->fields as $field )
					{
						if ( $field->autoSave )
							$field->delete($this->table, $this->id);
					}
				}
				
				$this->ci->db->delete($this->table, array( 'id' => $this->id, 'language' => $lang) );
			}
				
			//Si tiene clean url elimino la entrada (guarda que esto debe borrar todas las i18n)
			if ( $this->hayPaginaIndividual )
				$this->ci->db->delete('clean_urls', array('node_id' => $this->id, 'table' => $this->table));
		}
	}
	
	function getRow()
	{
		if (!$this->id)
			return;
		else
		{
			return $this->ci->db->get_where($this->table, array( 'id' => $this->id ))->row_array();
		}
	}
	
	/**
	 * Tendrá los datos por post, debe devolver un array ( 'field_name' => 'error' ) o null si no hay error
	 */
	function validate()
	{
		$this->ci->load->library('form_validation');
	}
	
	/**
	 * Devuelve si el contenido está siendo traducido
	 * 
	 * @return type 
	 */
	function isTranslating()
	{
		return $this->translating;
	}
	
	/**
	 * Agrega un botón personalizado al listado.
	 * 
	 * La image tiene que estar en images/backend/
	 * 
	 * El link debe ser interno y al final se le agregará automáticamente el id correspondiente
	 * 
	 * @param type $arrButton 
	 */
	function addCustomListButton($title, $image, $link)
	{
		$this->arrCustomListButtons[] = array(
			'title' => $title,
			'image' => $image,
			'link' => $link
		);
	}
	
	function getCustomButtons()
	{
		return $this->arrCustomListButtons;
	}
}

?>
