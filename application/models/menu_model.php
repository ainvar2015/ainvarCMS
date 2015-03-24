<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Menu_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


	function _menu($subid,$ubi="")
	{
		$this->db->order_by('orden','ASC');
		$this->db->order_by('ubicacion','ASC');
		$this->db->where('subid',$subid); 
		//$this->db->where('activar', 1); 
		$this->db->where('idioma','es');
		if($ubi != ''){
			$this->db->like('ubicacion',$ubi);
		}
		//$this->db->select('id,idmenu,subid,menu,ubicacion,titulo');
		$menu_items = $this->db->get('menu');
		$rsMenu = $menu_items->result();
		if($menu_items->num_rows() > 0){
			return $rsMenu;
		}else{
			return FALSE;
		}
	}
	function _espadre($subid)
	{
		$this->db->where('id', $subid);
		$this->db->select('id');
		$padre = $this->db->get('menu');
		if($padre->num_rows() > 0){
			$padreid = $padre->row();
			return $padreid;
		}else{
			return FALSE;
		}
	}

	function _tiene_hijos($id,$ubi)
	{
		$this->db->where('subid', $id);
		$this->db->where('ubicacion', $ubi);
		$padre = $this->db->get('menu');
		if($padre->num_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function _idiomas()
	{
		$this->db->where('activo','1');
		$idiomas_items = $this->db->get('idiomas');
		if($idiomas_items->num_rows() > 0)
		{
			return $idiomas_items->result();
		}
	}

	function _listarmodulos()
	{
		$modulos_items = $this->db->get('modulos');
		if($modulos_items->num_rows() > 0)
		{
			return $modulos_items->result();
		}
	}
		
	function _content_menu($id)
	{
		$this->db->where('id',$id);
		$this->db->select('id,titulo,contenido,usaplantilla,tipoplantilla,plantilla');
		$contenido = $this->db->get('menu');
		$content = $contenido->row();
		return $content;
	}
	function _data_menu($id,$idioma='es')
	{
		$this->db->where('idmenu',$id);
		$this->db->where('idioma',$idioma);
		$contenido = $this->db->get('menu');
		$content = $contenido->row();
		return $content;
	}
	public function edita_menu($id,$files)
	{
		$menu_imagen = '';
		$titulo_imagen = '';
		$data = array('activar'=>$this->input->post('activar'),'ref'=>$this->input->post('ref'),'tipoplantilla'=>$this->input->post('tipoplantilla'),'param'=>$this->input->post('param'),'ubicacion'=>$this->input->post('ubicacion'),'subid'=>$this->input->post('subid'),
			'menu'=>$this->input->post('menu'),'titulo'=>$this->input->post('titulo'),'contenido'=>$this->input->post('contenido'));
		if($files !== ''){
			if(isset($files[0])){
				$menu_imagen = $files[0];
			}
			if(isset($files[1])){
				$titulo_imagen = $files[1];
			}
		}
		if($menu_imagen != ''){
			$this->db->set('menu_imagen',$menu_imagen);
		}elseif($this->input->post('borra_MI') === '1'){
			$this->db->set('menu_imagen', '');
		}
		if($titulo_imagen != ''){
			$this->db->set('titulo_imagen',$titulo_imagen);
		}elseif($this->input->post('borra_TI') === '1'){
			$this->db->set('titulo_imagen', '');
		}
		
		$tipoplantilla = $this->input->post('tipoplantilla');
		if($tipoplantilla != 0){
			$usaplantilla = 'SI';
			$plantilla = $this->input->post('plantilla');
		}else{
			$usaplantilla = 'NO';
			$plantilla = ''; 
		}
		$this->db->set('usaplantilla',$usaplantilla);
		$this->db->set('plantilla',$plantilla);
		$this->db->where('id',$id);
		if($this->db->update('menu',$data)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	public function alta_menu($files,$idioma='es')
	{
		$creado = FALSE;
		$menu_imagen = '';
		$titulo_imagen = '';
		$creaidiomas = $this->input->post('creaidiomas');
		$data = array('idmenu'=>$this->input->post('idmenu'),'activar'=>$this->input->post('activar'),'ref'=>$this->input->post('ref'),'tipoplantilla'=>$this->input->post('tipoplantilla'),'plantilla'=>$this->input->post('plantilla'),'param'=>$this->input->post('param'),'ubicacion'=>$this->input->post('ubicacion'),'subid'=>$this->input->post('subid'),'menu'=>$this->input->post('menu'),'titulo'=>$this->input->post('titulo'),'contenido'=>$this->input->post('contenido'),'idioma'=>$idioma);
		if($files !== ''){
			if(isset($files[0])){
				$menu_imagen = $files[0];
			}
			if(isset($files[1])){
				$titulo_imagen = $files[1];
			}
		}
		if($menu_imagen != ''){
			$arrayMenuImg = array('menu_imagen'=>$menu_imagen);
			$data = array_merge($data,$arrayMenuImg);
		}elseif($this->input->post('borra_MI') === '1'){
			$arrayMenuImg = array('menu_imagen'=>'');
			$data = array_merge($data,$arrayMenuImg);
		}
		if($this->input->post('tipoplantilla') !== '0'){
			$usaplantilla = 'SI';
		}else{
			$usaplantilla = 'NO';
		}
		$arrayUsaPlantilla = array('usaplantilla' => $usaplantilla);
		$data = array_merge($data,$arrayUsaPlantilla);
//		$this->db->set('usaplantilla',$usaplantilla);
		$idmenu = $this->nextid('menu');
		$this->db->set('idmenu',$idmenu);
		if($creaidiomas === '1'){
        	$creado = FALSE;
        	$idiomas = $this->_idiomas();
        	foreach($idiomas as $idioma){
	        	$idiomaN = array('idioma' => $idioma->reducido);
	        	$data = array_replace($data,$idiomaN);
	        	if($this->db->insert('menu', $data)){
		        	$creado = TRUE;
		        }
        	}
        }else{
            if($this->db->insert('menu', $data)){
            	$creado = TRUE;
            }
        }
		return $creado;
	}
	public function del_menu($id)
	{
		$this->db->where('id',$id);
		if($this->db->delete('menu')){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	public function actOrden($id, $orden)
	{
		$data = array('orden'=>$orden);
		$this->db->where('idmenu',$id);
		return $this->db->update('menu',$data);
	}

	
	public function nextid($table = null) {
		$qry = "SHOW TABLE STATUS LIKE '$table'";
		$result = $this->db->query($qry);
		$row = $result->row();
		$nextId = $row->Auto_increment;			

		return $nextId;
	}
}
