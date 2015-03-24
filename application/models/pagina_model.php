<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pagina_model extends CI_Model {

	function _getdata($tabla,$id='',$idioma='es')
	{
		if($tabla === 'fotosgaleria')
		{
			$this->db->where('id_galeria',$id);
		}
		$tablas = array('menu','carta','bodega','categoriascarta','categoriasbodega');
		if(in_array($tabla, $tablas)){
			$this->db->where('idioma',$idioma);
		}
		if($tabla === 'menu'){
			$this->db->where('activar','1');
		}
		
		$this->db->order_by('orden','ASC');
		$data = $this->db->get($tabla);
		
		$datos = $data->result();
		
		if($data->num_rows() > 0)
		{
			return $datos;
		}else{
			return FALSE;
		}
	}
	function _getalldata($tabla, $id,$idioma='es')
	{
		$this->db->where('idmenu', $id);
		$this->db->where('idioma', $idioma);
		$data = $this->db->get($tabla);
		if($data->num_rows>0)
		{
			return $data->row();
		}
	}
	function _getcats($tabla,$idioma)
	{
		$this->db->where('idioma',$idioma);
		$this->db->order_by('orden');
		$data = $this->db->get($tabla);
		if($data->num_rows>0)
		{
			return $data->result();
		}
	}
	function _getdatabodega($idcat,$idioma='es')
	{
		$this->db->where('idcat',$idcat);
		$this->db->where('idioma',$idioma);
		$this->db->order_by('orden','ASC');
		$data = $this->db->get('bodega');
		if($data->num_rows>0)
		{
			return $data->result();
		}
	}
	function _getdatacarta($idcat,$idioma='es')
	{
		$this->db->where('id_cat',$idcat);
		$this->db->where('idioma',$idioma);
		$this->db->order_by('orden','ASC');
		$data = $this->db->get('carta');
		if($data->num_rows>0)
		{
			return $data->result();
		}
	}
	function _idiomas()
	{
		$this->db->where('activo','1');
		$data = $this->db->get('idiomas');
		if($data->num_rows>0)
		{
			$datos = $data->result();
		}
		return $datos;
	}

}

/* End of file pagina_model.php */
/* Location: ./application/models/pagina_model.php */