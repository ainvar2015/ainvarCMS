<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modulos extends CI_Controller {

	private $modulosHtml = NULL;
	private $galeriasHtml = NULL;
	public function __construct()
    {
        parent::__construct();
		//error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
        $this->load->model('login_model');
		$this->load->model('modulos_model');
		$this->load->model('galerias_model');
        $this->load->library(array('session','form_validation','email'));
        $this->load->helper(array('url','form','MY_menu_helper'));
        $this->load->database('default');
    }

	public function index()
	{
		
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			if($this->modulos_model->_lista_modulos())
			{
				$data['listamodulos'] = $this->lista_modulos();
			}else{
				$data['listamodulos'] = "Lo siento. No hay ningún módulo disponible.";
			}
				$data['titulo'] = 'Volcano Restaurant - GESTOR DE MÓDULOS';
				$this->load->view('admin/head_view', $data);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				$this->load->view('admin/modulosadmin_view',$data);
				$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
        }
	}
	
	/* General */
	public function listar()
	{
		$data['listamodulos'] = $this->lista_modulos();
		$data['titulo'] = 'ainvar CMS v.1.0 - Gestión de Módulos';
		$this->load->view('admin/cabeceraadmin_view',$data);
		$this->load->view('admin/modulosadmin_view',$data);
		$this->load->view('admin/pieadmin_view');
	}

	public function lista_modulos($modulo="")
	{
		$result = $this->modulos_model->_lista_modulos();
		if (!$result) {
			return;
		} else {
			$this->modulosHtml .= '<ul class="task nav-bar pai" id="menu">';
			foreach($result as $row){
				$this->modulosHtml .= '<li><a href="'.base_url().$row->valor.'/listar"><i class="fa fa-'.$row->icono.'"></i> '.$row->nombre.'</a></li>';
			}
			$this->modulosHtml .= '</ul>';
		}
		return $this->modulosHtml;
	}
	/* Fin de general */
}

/* End of file modulos.php */
/* Location: ./application/controllers/modulos.php */
