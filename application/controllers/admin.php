<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('comunes_model');
    }

	public function index()
	{
		
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$configura = $this->comunes_model->_configura();
			$data['titulo'] = $configura->titulo_pagina;
			$data['modulos'] = $this->comunes_model->_modulos();
			$this->load->view('admin/calendar/headcal_view', $data, FALSE);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/calendar/calendar_view');
			$this->load->view('admin/calendar/piecal_view');
		}else{
			redirect(base_url().'login');
        }
	}

	public function actualizaOrden()
	{
		$Getorden = $this->input->get('orden');
		$tabla = $this->input->get('tabla');
		foreach ($Getorden as $orden => $item){
			//echo $Getorden; 
			//var_dump($Getorden); 
			
			if($this->comunes_model->actOrden($item,$orden,$tabla))
			{
				$ordenado = '<i class="fa fa-info"></i> Orden guardado correctamente.';
			}else{
				$ordenado = '<i class="fa fa-info"></i> ERROR!!!.';
			}
		}

		return $ordenado;
	}

}

/* End of file admin.php */
/* Location: ./application/controllers/admin/admin.php */