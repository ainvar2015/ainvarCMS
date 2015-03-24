<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Modulos_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	function _get_enlaces()
	{
		//$this->db->order_by('orden','ASC');
		$this->db->where('mostrar',1);
		$enlaces_items = $this->db->get('enlaces');
		$rsEnlaces = $enlaces_items->result();
		if($enlaces_items->num_rows() > 0){
			return $rsEnlaces;
		}else{
			return FALSE;
		}
	}
	function _lista_modulos()
	{
		$modulos_items = $this->db->get('modulos');
		$rsModulos = $modulos_items->result();
		if($modulos_items->num_rows() > 0){
			return $rsModulos;
		}else{
			return FALSE;
		}
	}
}