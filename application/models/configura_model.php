<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Configura_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	function _guardar()
	{
		$data = array('titulo_pagina'=>$this->input->post('titulo_pagina'),'nom_empresa'=>$this->input->post('nom_empresa'),'direccion'=>$this->input->post('direccion'),'poblacion'=>$this->input->post('poblacion'),'provincia'=>$this->input->post('provincia'),'cod_postal'=>$this->input->post('cod_postal'),'pais'=>$this->input->post('pais'),'telefono'=>$this->input->post('telefono'),'cif'=>$this->input->post('cif'));
		$this->db->where('id','1');
		if($this->db->update('settings',$data)){
			return TRUE;
		}else{
			return FALSE;
		}
	}

}

/* End of file configura_model.php */
/* Location: ./application/models/configura_model.php */