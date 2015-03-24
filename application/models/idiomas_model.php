<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Idiomas_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	function _lista_idiomas()
	{
		$this->db->where('activo','1');
		$idiomas_items = $this->db->get('idiomas');
		$rsIdiomas = $idiomas_items->result();
		if($idiomas_items->num_rows() > 0){
			return $rsIdiomas;
		}else{
			return FALSE;
		}
	}

	function _todosIdiomas()
	{
		$activos = $this->_lista_idiomas();
		foreach($activos as $activo)
		{
			$this->db->where('id != ',$activo->id);
		}
		$this->db->order_by('original');
		$todos = $this->db->get('idiomasd');
		if($todos->num_rows() > 0){
			return $todos->result();
		}
	}
}

/* End of file idiomas_model.php */
/* Location: ./application/models/idiomas_model.php */