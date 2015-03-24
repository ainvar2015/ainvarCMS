<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Events extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('events_model');
	}
	public function index()
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$data['titulo'] = 'ainvar.net - Añadir Evento';
			$this->load->view('admin/headcal_view', $data);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view("admin/add_event");
			$this->load->view('admin/piecal_view');
		}else{
			redirect(base_url().'login');
        }
	}

	public function save()
	{
		$this->form_validation->set_rules('from', 'Desde', 'trim|required|xss_clean');
        $this->form_validation->set_rules('to', 'Hasta', 'trim|required|xss_clean');
        $this->form_validation->set_rules('title', 'Título', 'trim|required|xss_clean');
        $this->form_validation->set_rules('event', 'Evento', 'trim|required|xss_clean');
        $this->form_validation->set_rules('class', 'Tipo de evento', 'trim|required|xss_clean');

        $this->form_validation->set_message('required', 'El  %s es requerido');

        if($this->form_validation->run() == FALSE)
        {
            $this->index();
        }
        else
        {
        	if($this->events_model->add())
        	{
        		$data['guardado'] = 'El evento ha sido guardado con éxito';
        	}else{
        		$data['guardado'] = 'Ha ocurrido un error y el evento no se ha podido guardar. Por favor inténtelo más tarde.';
        	}
        }
        
	}

	public function view($id)
	{
		if($evento = $this->events_model->ver($id))
		{
			$data['datosevento'] = $evento;
			$this->load->view('admin/vereventos_view', $data, FALSE);
		}else{
			$data['datosevento'] = 'No Funciona';
		}
	}

	public function getAll()
	{
		if($this->input->is_ajax_request())
		{
			$this->load->model('events_model');
			if($events = $this->events_model->getAll())
			{
				$eventos = json_encode(array("success" => 1, "result" => $events));
				echo $eventos;
			}
		}
	}

	public function render($id = 0)
	{
		if($id != 0)
		{
			echo $id;
		}
	}
}


/* End of file events.php */
/* Location: ./application/controllers/events.php */