<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Errors extends CI_Controller {

	
	private $data = array();

	function __construct()
	{
		parent::__construct();
		$this->load->helper('html');
	}

	function error_404()
	{
		 //llamamos a la vista que muestra el error 404 personalizado
		 $this->load->view('errors/error_404');
	}
}

/* End of file errors.php */
/* Location: ./application/controllers/errors.php */