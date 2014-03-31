<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH.'libraries/admin/lib/MasterController.php');
include_once(APPPATH.'libraries/admin/lib/ContentType.php');

class MasterControllerColumbia extends MasterController {
	
	protected $_siteName = 'columbia';
	protected $_masterPageName = 'master_page';
	public $og_image = 'images/logo_fundacion_columbia_fb.jpg';
	public $og_description = 'Fundación Columbia es un espacio abierto de difusión de múltiples conocimientos tradicionales y contemporáneos para aquellas personas que quieran comenzar o continuar con su camino de búsqueda espiritual.';
	public $newsNotification = false;
	
	
	protected $_menuTop = array(
		'Quiénes somos' => 'informacion/quienes-somos',
		'Qué hacemos' => 'puertas-de-entrada',
		'Docentes' => 'docentes',
		'Agenda' => 'agenda',
		'Novedades' => 'novedades',
		'Publicaciones y Prensa' => 'publicaciones',
		'Nuestra casa' => 'informacion/nuestra-casa',
	);
	
	public function __construct()
	{
		parent::__construct();
		
		if ( $this->siteuser->cookieCheck() == SiteUser::LOGGED_IN )
		{
			redirect( uri_string() );
			exit;
		}
	}
	
	/**
	 *
	 * @param type $additionalData
	 * @param type $mostrarPuertas Muestra el menu izquierdo de puertas
	 * @param type $full En vez del diseño en dos columnas, una. En principio se usa para las agendas
	 */
	public function show($additionalData = array(), $mostrarPuertas = true, $full = false)
	{
		$arrPuertas = array();
		
		if ( $mostrarPuertas )
		{
			$arrPuertas = $this->getPuertas();
			$arrSponsors = $this->getSponsors();
		}
		
		$newsNotification = false;
		
		if ( $this->newsNotification && !$this->siteuser->isLogged() && !$this->adminuser->isLogged() )
			$newsNotification = true;
		
		$data = array( 
			'arrPuertas' => $arrPuertas, 
			'menuTop' => $this->getMenuTop(), 
			'full' => $full, 
			'newsNotification' => $newsNotification,
			'arrSponsors' => $arrSponsors);
		
		$data = array_merge($data, $additionalData);
		
		parent::show($data);
	}
	
	protected function getMenuTop()
	{
		return $this->_menuTop;
	}
	
	protected function getSponsors()
	{
		return magico_getList('sponsors', 100);
	}
	
	protected function getPuertas()
	{
		$arrDisciplinas = $this->db->query('
			SELECT 
				d.id,
				d.title,
				d.textoDestacado,
				d.textoCorto,
				d.idPuerta,
				p.title AS puerta,
				p.descripcion,
				cu.url
			FROM 
				disciplinas d
			LEFT JOIN
				puertas p
				ON p.id = d.idPuerta
			LEFT JOIN
				clean_urls cu ON
				cu.node_id = d.id AND cu.table = "disciplinas"
			ORDER BY
				d.idPuerta ASC, d.weight ASC')->result_array();
		
		if ( $arrDisciplinas )
		{
			$arrPuertas = array();
			$ultimaPuerta = '';
			$i = 0;
			
			while ( $i < count($arrDisciplinas) )
			{
				$ultimaPuerta = $arrDisciplinas[$i]['puerta'];
				
				while ( $i < count($arrDisciplinas) && $ultimaPuerta == $arrDisciplinas[$i]['puerta'] )
				{
					$key = ContentType::cleanURL($ultimaPuerta);
					
					if ( count($arrPuertas[ $key ]) >= 2 )
						$arrDisciplinas[$i]['hidden'] = true;
						
					
					$arrPuertas[ $key ][] = $arrDisciplinas[$i];
					$i++;
				}
			}
			
			return $arrPuertas;
		}
		else
			return array();
	}
}