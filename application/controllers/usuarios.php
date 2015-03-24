<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('usuarios_model');
		$this->load->model('comunes_model');
	}

	public function index()
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$configura = $this->comunes_model->_configura();
			$data['titulo'] = $configura->titulo_pagina;
			$data['usuarios'] = $this->usuarios_model->_datosUsuarios();
			
			$this->load->view('admin/head_view',$data);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/usuarios/usuarios_view',$data);
			$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
		}
	}
	public function nuevo()
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$configura = $this->comunes_model->_configura();
			$data['titulo'] = $configura->titulo_pagina;
			$data['ultimoId'] = $this->comunes_model->nextid('usuarios');

			$this->load->view('admin/head_view',$data);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/usuarios/nuevousuario_view',$data);
			$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
		}
	}
	public function accion($modo)
	{
		if($modo === 'alta')
		{
			$this->form_validation->set_rules('login', 'Login del Usuario', 'trim|required|is_unique[usuarios.login]|xss_clean');
			$this->form_validation->set_rules('password', 'Contraseña', 'trim|required|matches[password1]|xss_clean');
			$this->form_validation->set_rules('password1', 'Confirme Contraseña', 'trim|required|xss_clean');
			$this->form_validation->set_rules('nombre', 'Nombre del Usuario', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email del Usuario', 'trim|required|is_unique[usuarios.email]|valid_email|xss_clean');
			$this->form_validation->set_message('required','El campo %s es obligatorio');
			$this->form_validation->set_message('matches','Los campos %s y %s deben de ser iguales');
			$this->form_validation->set_message('is_unique','El %s ya existe en nuestra base de datos');
			$this->form_validation->set_message('valid_email','Debe escribir una dirección de email válida');

			if($this->form_validation->run() === FALSE)
	        {
				$this->nuevo();

			}else{
				$configura = $this->comunes_model->_configura();
				$data['titulo'] = $configura->titulo_pagina;
				$data['tipo'] = 'alta';
				$data['pageHead'] = 'ALTA DE USUARIO';
				$data['widgetHead'] = 'RESULTADO DEL ALTA';
				if($this->usuarios_model->alta_usuario())
				{
					$data['guardado'] = '<div class="alert alert-success"><i class="fa fa-thumbs-up fa-lg"></i> El usuario ha sido dado de alta correctamente.';
				}else{
					$data['guardado'] = '<div class="alert alert-danger"><i class="fa fa-thumbs-down fa-lg"></i> Ha ocurrido un error. Vuelva a intentarlo más tarde.';
				}
				$id = $this->db->insert_id();
				$data['titulores'] = '<i class="fa fa-user"></i> Nuevo Usuario';
				$data['linksHead'] = '<div class="pull-left">Resultado de la Edición<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="'.base_url().'usuarios"><i class="fa fa-angle-double-left"></i> Volver a Ver Usuarios</a></span>
		        <span class="pull-right ml15"><a class="btn btn-xs btn-default" href="'.base_url().'usuarios/editar/'.$id.'"><i class="fa fa-edit"></i> Volver a Editar este Usuario</a></span>
		        <span class="pul-right ml15"><a class="btn btn-xs btn-default" href="'.base_url().'usuarios/nuevo"><i class="fa fa-plus"></i> Añadir Usuario</a></span></div>';

				$data['ctrl'] = 'usuarios';
				$this->load->view('admin/head_view', $data);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				$this->load->view('admin/resultado_view',$data);
				$this->load->view('admin/pie_view');
			}
		}elseif($modo === 'editar'){
			if($this->input->post('login_old') !== $this->input->post('login'))
			{
				$this->form_validation->set_rules('login', 'Login del Usuario', 'trim|required|is_unique[usuarios.login]|xss_clean');
			}else{
				$this->form_validation->set_rules('login', 'Login del Usuario', 'trim|required|xss_clean');
			}
			if($this->input->post('email_old') !== $this->input->post('email'))
			{
				$this->form_validation->set_rules('email', 'Email del Usuario', 'trim|required|is_unique[usuarios.email]|valid_email|xss_clean');
			}else{
				$this->form_validation->set_rules('email', 'Email del Usuario', 'trim|required|valid_email|xss_clean');
			}
			$this->form_validation->set_rules('nombre', 'Nombre del Usuario', 'trim|required|xss_clean');
			$this->form_validation->set_message('required','El campo %s es obligatorio');
			$this->form_validation->set_message('is_unique','El %s ya existe en nuestra base de datos');
			$this->form_validation->set_message('valid_email','Debe escribir una dirección de email válida');

			$idc = $this->input->post('id');

			if($this->form_validation->run() === FALSE)
	        {
				$this->editar($idc);

			}else{
				$configura = $this->comunes_model->_configura();
				$data['titulo'] = $configura->titulo_pagina;
				$data['tipo'] = 'editar';
				$data['pageHead'] = 'EDICCIÓN DE USUARIO';
				$data['widgetHead'] = 'RESULTADO DE LA EDICCIÓN';
				$data['id'] = $idc;
				if($this->usuarios_model->edita_usuario($idc))
				{
					$data['guardado'] = '<div class="alert alert-success"><i class="fa fa-thumbs-up"></i> El usuario ha sido editado corectamente';
				}else{
					$data['guardado'] = '<div class="alert alert-danger"><i class="fa fa-thumbs-down"></i> Ha ocurrido un error. Vuelva a intentarlo más tarde.';
				}
				$data['titulores'] = '<i class="fa fa-cutlery"></i> Nuevo Usuario';
				$data['linksHead'] = '<div class="pull-left">Resultado de la Edición<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="'.base_url().'usuarios"><i class="fa fa-angle-double-left"></i> Volver a Ver Usuarios</a></span>
		        <span class="pull-right ml15"><a class="btn btn-xs btn-default" href="'.base_url().'usuarios/editar/'.$idc.'"><i class="fa fa-edit"></i> Volver a Editar este Usuario</a></span>
		        <span class="pul-right ml15"><a class="btn btn-xs btn-default" href="'.base_url().'usuarios/nuevo"><i class="fa fa-plus"></i> Añadir Usuario</a></span></div>';

				$data['ctrl'] = 'usuarios';
				$this->load->view('admin/head_view', $data);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				$this->load->view('admin/resultado_view',$data);
				$this->load->view('admin/pie_view');
			}
		}elseif($modo === 'borrar'){
			$idc = $this->input->post('id');
			$configura = $this->comunes_model->_configura();
			$data['titulo'] = $configura->titulo_pagina;
			$data['tipo'] = 'borrar';
			$data['pageHead'] = 'BORRADO DE USUARIO';
			$data['widgetHead'] = 'RESULTADO DEL BORRADO';
			if($this->usuarios_model->borra_usuario($idc))
			{
				$data['guardado'] = '<span class="text-success"><i class="fa fa-thumbs-up"></i> El usuario ha sido borrado correctamente</span>';
			}else{
				$data['guardado'] = '<i class="fa fa-thumbs-down"></i> Ha ocurrido un error. Vuelva a intentarlo más tarde.';
			}
			$data['titulores'] = '<i class="fa fa-cutlery"></i> Nuevo Usuario';
			$data['linksHead'] = '<div class="pull-left">Resultado del Borrado<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="'.base_url().'usuarios"><i class="fa fa-angle-double-left"></i> Volver a Ver Usuarios</a></span>
	        <span class="pul-right ml15"><a class="btn btn-xs btn-default" href="'.base_url().'usuarios/nuevo"><i class="fa fa-plus"></i> Añadir Usuario</a></span></div>';

			$data['ctrl'] = 'usuarios';
			$this->load->view('admin/head_view', $data);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/resultado_view',$data);
			$this->load->view('admin/pie_view');
		}elseif($modo === 'password'){
			$this->form_validation->set_rules('passwordold', 'Password Actual', 'trim|required|xss_clean');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[password1]|xss_clean');
			$this->form_validation->set_rules('password1', 'Confirma Password', 'trim|required|xss_clean');

			$this->form_validation->set_message('required', 'El  %s es requerido');
			$this->form_validation->set_message('matches', 'Las contraseñas no coinciden');
			$id = $this->input->post('id');
			if($this->form_validation->run() === FALSE)
	        {
				$this->password($id,'fallo');

			}else{
				$configura = $this->comunes_model->_configura();
				$data['titulo'] = $configura->titulo_pagina;
				$data['tipo'] = 'password';
				$data['pageHead'] = 'CAMBIO DE PASSWORD';
				$data['widgetHead'] = 'RESULTADO DEL CAMBIO';
				$data['id'] = $id;
				if(	$this->usuarios_model->_password($id))
				{
					$data['resultado'] = '<span class="text-success"><i class="fa fa-thumbs-up"></i> El password ha sido modificado corectamente</span>';
				}else{
					$data['resultado'] = '<i class="fa fa-thumbs-down"></i> Ha ocurrido un error. Vuelva a intentarlo más tarde. <span class="error"><i class="fa fa-warning"></i> ERROR: La contraseña actual no es correcta.</<span>';
				}
				$data['ctrl'] = 'usuarios';
				$this->load->view('admin/head_view', $data);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				$this->load->view('admin/result_view',$data);
				$this->load->view('admin/pie_view');
			}
		}
	}

	/*	Funciones para editar clientes */
	public function editar($id)
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$configura = $this->comunes_model->_configura();
			$data['titulo'] = $configura->titulo_pagina;
			$data['data_usuario'] = $this->usuarios_model->_data_usuario($id);

			$this->load->view('admin/head_view', $data);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/usuarios/edit_usuario_view',$data);
			$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
        }
	}

	public function password($id,$error='')
	{
		if($error === '')
		{
			$this->load->view('admin/usuarios/cambiaPassword_view');
		}else{
			$configura = $this->comunes_model->_configura();
			$data['titulo'] = $configura->titulo_pagina;
			$data['tipo'] = 'password';
			$data['pageHead'] = 'CAMBIO DE PASSWORD';
			$data['widgetHead'] = 'RESULTADO DEL CAMBIO';
			$data['id'] = $id;
			$data['resultado'] = '<i class="fa fa-thumbs-down"></i> Ha ocurrido un error. Vuelva a intentarlo más tarde. <span class="error"><i class="fa fa-warning"></i> ERROR: Las contraseñas no coinciden.</span>';
			
			$data['ctrl'] = 'usuarios';
			$this->load->view('admin/head_view', $data);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/result_view',$data);
			$this->load->view('admin/pie_view');
		}
	}

	public function ver($id)
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$data['data_usuario'] = $this->usuarios_model->_data_usuario($id);
			$this->load->view('admin/head_view', $data);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/usuarios/ver_usuario_view',$data);
			$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
		}
	}
	
	/* Función para borrar clientes */
	public function borrar($id)
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$configura = $this->comunes_model->_configura();
			$data['titulo'] = $configura->titulo_pagina;
			$data['data_usuario'] = $this->usuarios_model->_data_usuario($id);

			$this->load->view('admin/usuarios/borra_usuario_view',$data);
		}else{
			redirect(base_url().'login');
		}
	}

	public function checkunicologin($str)
	{
		$usuario = $this->usuarios_model->_datosUsuarios();
		foreach($usuario as $datauser)
		{
			if($datauser['login'] === $str)
			{
				return FALSE;
			}
		}
		$login_usuario = $usuario->login;
		if($str === $login_usuario)
		{
			return TRUE;
		}else{
			return $this->usuarios_model->checklogin($id);
		}
	}
}

/* End of file users.php */
/* Location: ./application/controllers/usuarios.php */