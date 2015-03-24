<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Configura extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('configura_model');
		$this->load->model('comunes_model');
        $this->load->library(array('image_lib'));
        $this->load->helper(array('MY_menu_helper'));
	}

	public function index()
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$configura = $this->comunes_model->_configura();
			$data['data_config'] = $this->comunes_model->_configura();
			$data['titulo'] = $configura->titulo_pagina;
			$this->load->view('admin/head_view', $data, FALSE);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/settings_view');
			$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
        }
	}

	public function guardar()
	{
		$data['data_config'] = $this->comunes_model->_configura();
		if($this->configura_model->_guardar())
		{
			$data['titulo'] = $data['data_config']->titulo_pagina;
			$data['pageHead'] = 'MODIFICAR CONFIGURACIÓN';
			$data['widgetHead'] = 'RESULTADO DE LA EDICIÓN';
			$data['guardado'] = '<div class="alert alert-success"><i class="fa fa-thumbs-up fa-lg"></i> La configuración ha sido guardada correctamente.';
			$data['titulores'] = '<i class="fa fa-cogs"></i> Editar Configuración';
			$data['linksHead'] = '<div class="pull-left">Resultado de la Edición<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="'.base_url().'configura"><i class="fa fa-angle-double-left"></i> Volver a Ver la Configuración</a></span></div>';

			$this->load->view('admin/head_view', $data, FALSE);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/resultado_view');
			$this->load->view('admin/pie_view');
		}else{
			$data['titulo'] = $data['data_config']->titulo_pagina;
			$data['pageHead'] = 'MODIFICAR CONFIGURACIÓN';
			$data['widgetHead'] = 'RESULTADO DE LA EDICIÓN';
			$data['titulores'] = '';
			$data['linksHead'] = '<div class="pull-left">Resultado de la Edición<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="'.base_url().'configura"><i class="fa fa-angle-double-left"></i> Volver a Ver la Configuración</a></span>';
			$data['guardado'] = '<div class="alert alert-danger"><i class="fa fa-thumbs-down fa-lg"></i> Ha ocurrido un error. Vuelva a intentarlo más tarde.';
		}
	}

}

/* End of file configura.php */
/* Location: ./application/controllers/configura.php */