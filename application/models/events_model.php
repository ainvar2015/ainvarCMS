<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Events_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("Europe/Madrid"); 
	}

	/**
	* @desc - añade un nuevo evento
	* @access public
	* @author Ainvar
	* @return bool
	*/
	public function add()
	{
		$id = $this->nextid('eventos');
		$url = base_url()."events/view/".$id;
		$this->db->set("start", $this->_formatDate($this->input->post("from")));
		$this->db->set("end", $this->_formatDate($this->input->post("to")));
		$this->db->set("url", $url);
		$this->db->set("title", $this->input->post("title"));
		$this->db->set("body", $this->input->post("event"));
		$this->db->set("class", $this->input->post("class"));
		if($this->db->insert("eventos"))
		{
			return TRUE;
		}
		return FALSE;
	}

	/**
	* @desc - obtiene todos los registros de events
	* @access public
	* @author Ainvar
	* @return object
	*/
	public function getAll()
	{
		$query = $this->db->get('eventos');
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		return object();
	}

	/**
	* @desc - Obtiene los datos del evento
	* @access private
	* @author Ainvar
	* @return data
	*/
	public function ver($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('eventos');
		if($query->num_rows() > 0)
		{
			return $query->row();
		}
		//return FALSE;
	}
	
	/**
	* @desc - formatea una fecha a microtime para añadir al evento tipo 1401517498985
	* @access private
	* @author Ainvar
	* @return strtotime
	*/
	private function _formatDate($date)
	{
		return strtotime(substr($date, 6, 4)."-".substr($date, 3, 2)."-".substr($date, 0, 2)." " .substr($date, 10, 6)) * 1000;
	}
	
	public function nextid($table = null) {
		$qry = "SHOW TABLE STATUS LIKE '$table'";
		$result = $this->db->query($qry);
		$row = $result->row();
		$nextId = $row->Auto_increment;			

		return $nextId;
	}

}

/* End of file events_model.php */
/* Location: ./application/models/events_model.php */