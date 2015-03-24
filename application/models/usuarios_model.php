<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuarios_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}
	function _datosUsuarios()
	{
		$contenido = $this->db->get('usuarios');
		$content = $contenido->result();
		if($contenido->num_rows() > 0){
			return $content;
		}else{
			return FALSE;
		}
	}

	function _data_usuario($id)
	{
		$this->db->where('id',$id);
		$contenido = $this->db->get('usuarios');
		$content = $contenido->row();
		return $content;
	}

	public function alta_usuario()
	{
		$fecha = date("Y-m-d H:i:s");
		$data = array('login'=>$this->input->post('login'),'password'=>sha1($this->input->post('password')),
			'nombre'=>$this->input->post('nombre'),'email'=>$this->input->post('email'),
			'telefono'=>$this->input->post('telefono'),'activar'=>$this->input->post('activar'),
			'nivel'=>$this->input->post('nivel'),'fecha_alta'=>$fecha,'fecha_ultima_modificacion'=>$fecha);
		if($this->db->insert('usuarios',$data)){
			return TRUE;
		}else{
			return FALSE;
		}
	}


	/* Edición de Usuarios */
	public function edita_usuario($id)
	{
		$fecha = date("Y-m-d H:i:s");

		$data = array('login'=>$this->input->post('login'),'nombre'=>$this->input->post('nombre'),
			'email'=>$this->input->post('email'),'telefono'=>$this->input->post('telefono'),
			'activar'=>$this->input->post('activar'),'nivel'=>$this->input->post('nivel'),
			'fecha_ultima_modificacion'=>$fecha);
		$this->db->where('id',$id);
		if($this->db->update('usuarios',$data)){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function _password($id)
	{
		$passDB = $this->_data_usuario($id);
		
		$pass_actual = $passDB->password;

		if(sha1($this->input->post('passwordold')) === $pass_actual)
		{
			if($this->input->post('password') === $this->input->post('password1'))
			{
				$data = array('password'=>sha1($this->input->post('password')));
				$this->db->where('id',$id);
				if($this->db->update('usuarios',$data))
				{
					return TRUE;
				}
			}
		}else{
			return FALSE;
		}
	}

	function _checkstr($str)
	{

	}
	/* Fin funciones para edición de Usuarios */

	function borra_usuario($id)
	{
		$this->db->where('id',$id);
		if($this->db->delete('usuarios')){
			return TRUE;
		}else{
			return FALSE;
		}
	}
}

/* End of file usuarios_model.php */
/* Location: ./application/models/usuarios_model.php */