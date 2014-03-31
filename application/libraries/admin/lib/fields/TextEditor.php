<?php
/*
 Mâgico
 http://www.parleboo.com
 Copyright 2012 Leandro Garber <leandrogarber@gmail.com>
 Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//include('js/ckeditor/ckeditor_php5.php');

class TextEditor extends Field {
	
	private $styles = array(); //USO INTERNO, usar $extraTags
	/**
	 * Array de estilos extra, se puede usar la key para darle nombre. Los h* se establecen automáticamente
	 * B, I, U y P son siempre aceptados
	 * 
	 * @var array
	 */
	public $extraTags = array();
	/**
	 * Permitir links
	 * 
	 * @var boolean 
	 */
	public $allowLinks = true;
	/**
	 * Permitir listas ul, ol
	 * 
	 * @var boolean 
	 */
	public $allowLists = true;
	
	function __construct($label = null, $helptext = '', $defaultValue = '') {
		parent::__construct($label, $helptext, $defaultValue);
		
		$this->safeHtml = false;
	}
	
	/**
	 * Devuelve array con los tags permitidos. Para usar con ALOHA por ejemplo.
	 *  
	 */
	public function getAllowedTags()
	{
		$config = $this->populateConfig();
		return array_keys( $config['whitelist_elements'] );
	}
	
	/*
	 *  Devuelve styles para ckeditor. Llamar a populate config primero
	 */
	public function getStyles()
	{
		return $this->styles;
	}
	
	public function populateConfig()
	{
		$toolbar1 = $toolbar2 = $toolbar3 = array();
		
		//Whitelist, para que no pueda pegar tags que no esten permitidos
		$config['whitelist_elements'] = array('i' => 1, 'u' => 1, 'p' => 1, 'strong' => 1, 'br' => 1);
		
		// Extra tags
		if ( count($this->extraTags) )
		{
			$config['stylesSet'] = $this->name;
			
			foreach ( $this->extraTags as $key => $tag )
			{
				$tagName = $tag;
				
				if ( !is_numeric($key) )
					$tagName = $key;
				else
				{
					if ( $tag == 'h1' )
						$tagName = 'Título';
					elseif ( $tag == 'h2' || $tag == 'h3' || $tag == 'h4' )
						$tagName = 'Subtítulo';
				}
				
				$this->styles[] = array( 'name' => $tagName, 'element' => $tag );
				$config['whitelist_elements'][$tag] = 1;
			}
			
			$toolbar1[] = 'Styles';
		}
		
		$toolbar1[] = 'Bold';
		$toolbar1[] = 'Italic';
		$toolbar1[] = 'Underline';
		
		if ( $this->allowLinks )
		{
			$toolbar2 = array( 'Link', 'Unlink' );
			$config['whitelist_elements']['a'] = array( 'attributes' => array( 'href' => 1, 'target' => 1 ) );
		}
		
		if ( $this->allowLists )
		{
			$toolbar3 = array('NumberedList', 'BulletedList');
			$config['whitelist_elements']['ul'] = 1;
			$config['whitelist_elements']['ol'] = 1;
			$config['whitelist_elements']['li'] = 1;
		}
		
		$config['toolbar'] = array(
			  $toolbar1,
			  $toolbar2,
			  $toolbar3,
			  array( 'PasteFromWord', 'PasteText' )
		);
		
		$ci =& get_instance();
		
		// Idioma
		
		if ( $ci->config->item('language') == 'spanish' )	
			$config['language'] = 'es';
		else
			$config['language'] = 'en';
		
		return $config;
	}
	
	function render()
	{
		$ci =& get_instance();
		$data = array();
		
		$data['name'] = $this->name;
		$data['value'] = $this->value;
		$data['helptext'] = $this->helptext;
		$data['config'] = json_encode($this->populateConfig());
		$data['styles'] = json_encode($this->styles);
		
		$ci->load->view('admin/fields/texteditor.php', $data);
		
		parent::render();
	}
}
