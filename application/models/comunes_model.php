<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comunes_model extends CI_Model {

	function _datos($id='',$tabla)
	{
		if($id != '')
		{
			$this->db->where('id',$id);
		}
        $datos = $this->db->get($tabla);
		$data = $datos->row(); 
        if($datos->num_rows()>0)
        {
            return $data;
        }
	}

	public function actOrden($id, $orden,$tabla,$tipo='id')
	{
		$data = array('orden'=>$orden);
		$this->db->where($tipo,$id);
		return $this->db->update($tabla,$data);
	}
	
	public function nextid($table = null) {
		$qry = "SHOW TABLE STATUS LIKE '$table'";
		$result = $this->db->query($qry);
		$row = $result->row();
		$nextId = $row->Auto_increment;			

		return $nextId;
	}

	function _idiomas()
	{
		$this->db->where('activo','1');
		$idiomas_items = $this->db->get('idiomas');
		if($idiomas_items->num_rows() > 0)
		{
			return $idiomas_items->result();
		}else{
			return FALSE;
		}
	}
	function _modulos()
	{
		$this->db->where('activo','1');
		$modulos_items = $this->db->get('modulos');
		if($modulos_items->num_rows() > 0)
		{
			return $modulos_items->result();
		}else{
			return FALSE;
		}
	}
	function _configura()
	{
		//$this->db->where('id','1');
		$settings_items = $this->db->query('SELECT * FROM settings');
		if($settings_items->num_rows() > 0)
		{
			return $settings_items->row();
		}else{
			return FALSE;
		}
	}

	function Thumbnail($tmb_suffix,$tmb_filename,$tmb_naming="suffix") {
		$tmb_pos = strrpos($tmb_filename, "/");
		if ($tmb_pos == false) {
			$tmb_pos = 0;
		}
		$tmb_name = substr($tmb_filename, $tmb_pos, strrpos($tmb_filename, ".")-$tmb_pos);
		$tmb_extension = substr($tmb_filename, strrpos($tmb_filename, "."));
		if ($tmb_naming = "suffix") {
			return ($tmb_name.$tmb_suffix.$tmb_extension);
		} else {
			return ($tmb_suffix.$tmb_name.$tmb_extension);
		}
	}
}

/* End of file comunes_model.php */
/* Location: ./application/models/comunes_model.php */