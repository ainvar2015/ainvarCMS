<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Idiomas extends CI_Controller {

	private $idiomasHtml = NULL;
	private $todosIdiomas = NULL;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('idiomas_model');
	}

	public function index()
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			if($this->modulos_model->_lista_idiomas())
			{
				$data['listamodulos'] = $this->lista_idiomas();
			}else{
				$data['listamodulos'] = "Lo siento. No hay ningún idioma disponible.";
			}
				$data['titulo'] = 'Volcano Restaurant - GESTOR DE IDIOMAS';
				$this->load->view('admin/head_view', $data);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				$this->load->view('admin/idiomassadmin_view',$data);
				$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
        }
	}

	public function listar()
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$data['listaidiomas'] = $this->lista_idiomas();
			$data['titulo'] = 'ainvar CMS v.1.0 - Gestión de Idiomas';
			$data['todosIdiomas'] = $this->todosIdiomas();
			$this->load->view('admin/head_view', $data);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/modulos/idiomas/idiomasadmin_view',$data);
			$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
		}
	}

	public function lista_idiomas($idioma="")
	{
		$result = $this->idiomas_model->_lista_idiomas();
		if (!$result) {
			return;
		} else {

			$this->idiomasHtml .= '<ul class="task nav-bar pai" id="menu">';
			foreach($result as $row){
				$this->idiomasHtml .= '<li><a href="'.base_url().'idiomas/editar"><i class="fa fa-flag"></i> '.$row->idioma.'</a></li>';
			}
			$this->idiomasHtml .= '</ul>';
			//$this->idiomasHtml .= '<div class="col-lg-4">'.$this->todosIdiomas().'</div>';
		}
		return $this->idiomasHtml;
	}

	public function addidioma()
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$this->idiomas_model->_addIdioma();
		}else{
			redirect(base_url().'login');
		}
	}

	public function todosIdiomas()
	{
		$todos = $this->idiomas_model->_todosIdiomas();
		$this->todosIdiomas = '<select name="idioma" class="form-control">';
		foreach($todos as $idioma)
		{
			$this->todosIdiomas .= '<option value"'.$idioma->id.'>'.$idioma->original.'</option>';
		}
		$this->todosIdiomas .= '</select>';
		return $this->todosIdiomas;
	}

}

/* End of file idiomas.php */
/* Location: ./application/controllers/idiomas.php */