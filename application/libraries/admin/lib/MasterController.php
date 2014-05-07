<?php 
/*
 Mâgico
 http://www.parleboo.com
 Copyright 2012 Leandro Garber <leandrogarber@gmail.com>
 Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
*/

/**
********* CONTROLLERS *********
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
* @property CI_Benchmark $benchmark
* @property CI_Calendar $calendar
* @property CI_Cart $cart
* @property CI_Config $config
* @property CI_Controller $controller
* @property CI_Email $email
* @property CI_Encrypt $encrypt
* @property CI_Exceptions $exceptions
* @property CI_Form_validation $form_validation
* @property CI_Ftp $ftp
* @property CI_Hooks $hooks
* @property CI_Image_lib $image_lib
* @property CI_Input $input
* @property CI_Language $language
* @property CI_Loader $load
* @property CI_Log $log
* @property CI_Model $model
* @property CI_Output $output
* @property CI_Pagination $pagination
* @property CI_Parser $parser
* @property CI_Profiler $profiler
* @property CI_Router $router
* @property CI_Session $session
* @property CI_Security $security
* @property CI_Sha1 $sha1
* @property CI_Table $table
* @property CI_Template $template
* @property CI_Trackback $trackback
* @property CI_Typography $typography
* @property CI_Unit_test $unit_test
* @property CI_Upload $upload
* @property CI_URI $uri
* @property CI_User_agent $agent
* @property CI_Validation $validation
* @property CI_Xmlrpc $xmlrpc
* @property CI_Xmlrpcs $xmlrpcs
* @property CI_Zip $zip
* @property Image_Upload $image_upload
* @property Lang_Detect $lang_detect
* @property SiteUser $siteuser

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *  Esta clase no debería ser modificada. Los features específicos deberían estar en la clase hija.
 */
class MasterController extends CI_Controller {

	private $_additionalCss = array();
	private $_additionalJs = array();
	
	protected $_masterPageName = 'master_page'; //por si hay mas de una, esto se establece en '' si se especifica la masterPage en los métodos de la page
	protected $_siteName; //esto hay que llenarlo desde la clase hija
	
	protected $_pageTitle; //El título de la página
	
	// Para facebook
	public $og_image;
	public $og_description;
	
	public function __construct()
	{
		parent::__construct();
		
		include_once('application/libraries/admin/lib/fields/Field.php');
		spl_autoload_register(array($this,'_autoIncludeFields'));
		
		$this->_loadCommon();
	}
	
	
	protected function _autoIncludeFields($name)
	{
		@include_once("application/libraries/admin/lib/fields/$name.php");
	}
	
	protected function _loadCommon()
	{
		if ( $this->adminuser->cookieCheck() == AdminUser::LOGGED_IN )
		{
			redirect( uri_string() );
			exit;
		}
		
		if ( $this->_masterPageName )
			$this->setMasterPage($this->_masterPageName);
	}
	
	public function magico()
	{
		$this->masterpage->addContentPage('admin/admin_login', 'Adminnav');
		$this->index();
	}
	
	public function magico_login()
	{
		$loginResult = $this->adminuser->login($_POST['user'], $_POST['password'], $_POST['remember']);

		if ( $loginResult == AdminUser::NOT_LOGGED_IN )
		{
			echo json_encode(array('error' => true));
		}
		elseif ( $loginResult == AdminUser::LOGGED_IN )
		{
			echo json_encode(array('success' => true));
		}
		
		return $loginResult;
	}
	
	public function magico_logout()
	{
		$this->adminuser->logout();
		redirect(base_url());
	}
	
	protected function setTitle($title)
	{
		$this->_pageTitle = $title;
	}

	protected function setMasterPage($view)
	{
		$this->_masterPageName = $view;
		
		$this->masterpage->setMasterPage($this->_masterPageName);
		$this->masterpage->addContentPage('admin/admin_nav', 'Adminnav');
		
		if ( $this->adminuser->isLogged() )
		{
			$this->_additionalCss[] = 'magico.css';
			$this->_additionalCss[] = 'prettyPhoto.css';
			$this->_additionalCss[] = 'jquery.jgrowl.css';
			$this->_additionalCss[] = 'jquery-ui-1.8.14.custom.css';
			$this->_additionalCss[] = 'chosen.css';
			//$this->_additionalCss[] = '../js/aloha/css/aloha.css';
			
			$this->_additionalJs[] = 'jquery.prettyPhoto.js';
			$this->_additionalJs[] = 'magico.js';
			$this->_additionalJs[] = "magico_{$this->_siteName}.js";
			$this->_additionalJs[] = 'ckeditor/ckeditor.js';
			$this->_additionalJs[] = 'ckeditor/adapters/jquery.js';
			$this->_additionalJs[] = 'jquery.jgrowl.js';
			$this->_additionalJs[] = "fileuploader-{$this->config->item('admin_language')}.js";
			$this->_additionalJs[] = 'fileuploader.js';
			$this->_additionalJs[] = 'jquery.form.js';
			$this->_additionalJs[] = 'jquery.cookie.js';
			$this->_additionalJs[] = 'jquery.animate-shadow-min.js';
			$this->_additionalJs[] = 'jquery.ui.datepicker-es.js';
			$this->_additionalJs[] = 'chosen.jquery.min.js';
			//$this->_additionalJs[] = 'jquery.ui.touch-punch.min.js';
			//$this->_additionalJs[] = 'modernizr.min.js';
			//$this->_additionalJs[] = 'https://maps.googleapis.com/maps/api/js?sensor=false';
		}
	}
	
	public function setFacebookImage($image)
	{
		$this->og_image = $image;
	}
	
	public function setFacebookDescription($desc)
	{
		$this->og_description = strip_tags($desc);
	}
	
	public function addJs($file)
	{
		$this->_additionalJs[] = $file;
	}
	
	public function addCss($file)
	{
		$this->_additionalCss[] = $file;
	}
	
	public function addMessage($message)
	{
		$_SESSION['messages'][] = $message; 
	}

	public function responsiveImage($filename, $width)
	{
		$arrMime = array( 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/x-png', 'bmp' => 'image/x-ms-bmp' );
		
		$file = magico_thumb(urldecode($filename), $width, 0, ZEBRA_IMAGE_CROP_CENTER, false);
		
		if ( $file )
		{
			$fileExtension = substr( $file, strrpos($file, '.') + 1 );
		
			header("Content-Type: {$arrMime[$fileExtension]}");
			readfile($file);
		}
		
	}
	
	/**
	 * Esto va en el head del master_page para que funcione mâgico 
	 */
	private function magico_load()
	{
		$head = '';
		
		if ( $this->adminuser->isLogged() )
		{
			foreach ($this->_additionalCss as $css )
				$head .= "<link rel='stylesheet' type='text/css' href='css/$css' />\n\r";
			
			foreach ($this->_additionalJs as $js )
				$head .= "<script type='text/javascript' src='js/$js'></script>\n\r";
		}
		
		return $head;
	}
	
	/**
	 * Wrapper del masterpage para agregar contenido a los tags definidos en el master_page
	 */
	public function addContentPage($viewName, $data = array(), $tagName = 'Content')
	{
		$this->masterpage->addContentPage($viewName, $tagName, $data);
	}
	
	public function show($additionalData = array())
	{
		$messages = $_SESSION['messages'] ? $_SESSION['messages'] : array();
		
		$data = array( 'head' => $this->magico_load(), 'messages' => $messages, 'title' => $this->_pageTitle, 'og_image' => site_url($this->og_image), 'og_description' => $this->og_description );
		$data = array_merge($data, $additionalData);
		
		$this->masterpage->show($data);
		unset($_SESSION['messages']);
	}
}
