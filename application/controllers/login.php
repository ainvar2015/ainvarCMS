<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->library(array('session','form_validation','email'));
        $this->load->helper(array('url','form','cookie'));
        $this->load->database('default');
    }
    
    public function index()
    {
		$galleta = get_cookie('recuerda');
		$galleta = explode('|',$galleta);
        if(count($galleta) > 1){
			$data = array(
                'is_logued_in'	=>		TRUE,
                'id_usuario'	=>		$galleta['1'],
                'nivel'			=>		$galleta['2'],
                'nombre'		=>		$galleta['3']
            );        
            $this->session->set_userdata($data);
		}
			
		$data['is_logued_in'] = $this->session->userdata('is_logued_in');
		switch ($this->session->userdata('nivel')) {
            case '':
				$data['token'] = $this->token();
                $data['titulo'] = 'ainvar.net - Login';
                $this->load->view('admin/login_view',$data);
                break;
            case 'adm':
                redirect(base_url().'admin');
                break;
            case 'usu':
                redirect(base_url().'anuncio');
                break;    
            case 'otr':
                redirect(base_url().'otros');
                break;
            default:        
                $data['titulo'] = 'Log In';
                $this->load->view('admin/login_view',$data);
                break;        
        }
    }
	public function new_user()
    {
        if($this->input->post('token') && $this->input->post('token') == $this->session->userdata('token'))
        {
            $this->form_validation->set_rules('login', 'nombre de usuario', 'required|trim|min_length[2]|max_length[150]|xss_clean');
            $this->form_validation->set_rules('password', 'password', 'required|trim|min_length[5]|max_length[150]|xss_clean');
            //lanzamos mensajes de error si es que los hay
            
            if($this->form_validation->run() == FALSE)
            {
                $this->index();
            }else{
                $login = $this->input->post('login');
                $password = sha1($this->input->post('password'));
				//echo $email.' p: '.$password;
                $check_user = $this->login_model->login_user($login,$password);
				print_r($check_user); 
                if($check_user == TRUE)
                {
                    $data = array(
                    'is_logued_in'	=>	TRUE,
                    'id_usuario'	=>	$check_user->id,
                    'nivel'			=>	$check_user->nivel,
                    'nombre'		=>	$check_user->nombre,
                    'lang'			=>  'esp'
                    );    
                    $this->session->set_userdata($data);
					if($this->input->post('recordar') == 'SI')
					{
						$iduser = $check_user->id;
						$nivel = $check_user->nivel;
                    	$nombre = $check_user->nombre;
						$cookie = array(
							'name'   => 'recuerda',
							'value'  => 'SI'.'|'.$iduser.'|'.$nivel.'|'.$nombre,
							'expire' => '1296000',
							'domain' => '.localhost',
							'path'   => '/',
						); 
						set_cookie($cookie);
					}	

                    $this->index();
                }
            }
        }else{
            redirect(base_url().'login');
        }
    }
	public function registro()
	{
		$this->form_validation->set_rules('email', 'Email', 'required|is_unique[usuarios.email]|valid_email');
        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('direccion', 'Direccion', 'required');
        $this->form_validation->set_rules('poblacion', 'Población', 'required');
        $this->form_validation->set_rules('provincia', 'Provincia', 'required');
        $this->form_validation->set_rules('codpostal', 'Código Postal', 'required');
		$this->form_validation->set_rules('acepta', 'Acepta', 'required');
		$this->form_validation->set_message('is_unique', 'El email ya existe. Vuelva a intentarlo por favor');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>'); 
		
		if ($this->form_validation->run() == FALSE)
		{
			//return form_error();
			$this->index();
		}else{
			$data['resultado'] = $this->login_model->nuevo_registro();
			if($data['resultado'])
			{
				
				$this->load->view('cabecera',TRUE);
				$this->load->model('ciudades_model');
				$data['provincias'] = $this->ciudades_model->provincias();
				$this->load->view('formBusqueda',$data);
				$this->load->view('registro_correcto_view',$data);
				$this->load->view('pie',TRUE);
				$email = $this->input->post('email');
				$passwd = $data['resultado']['password'];
				$claveValida = $data['resultado']['validKey'];
				
				$this->envia_email($email,$passwd,$claveValida);

			}else{
				echo 'Ha ocurrido un error';
			}
        }
	}

	function envia_email($email,$passwd,$claveValida)
    {
		$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\">
		<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
		<style>
		html, body{
			font:normal 14px Arial, Helvetica, sans-serif;
			background-color:#eee;
			color:#4878a8;
		}
		h1{
			font-size:16px;
		}
		h2{
			font-size:15px;
		}
		table {
			background-color:#fff;
			color:#4878a8;
			border:#4878a8 5px solid;
		}
		td {
			border:#4878a8 1px solid;
			padding:5px 7px;
		}
		a {
			font-weight:bold;
			color:#4878a8;
			text-decoration:none;
		}
		a:hover {
			text-decoration:underline;
		}
		.importante {
			color:#CC0000;
			font:bold 16px Arial, Helvetica, sans-serif;
		}
		.info{
			font-size:12px;
		}
		#protDatos{
			border:1px solid #4878a8;
			padding:5px;
		}
		</style>
		<title>Nuevo Registro</title>
		</head>
		
		<body>
		<p>Ud. recibe este email porque se ha registrado en la web buscarpiso.com, si no es as&iacute;, por favor no haga caso de este email.</p>
		<p>Para completar su registro pulse en el siguiente enlace y siga las instrucciones en la pantalla.</p>
		<p><a href=\"http://buscarpisoci.ainvar.net/validaregistro/valida/$claveValida\">http://buscarpisoci.ainvar.net/validaregistro/valida/$claveValida</a></p>
		<p class=\"importante\">&iexcl;IMPORTANTE!</p>
		<p>Para acceder a su anuncio debe utilizar los siguientes datos:</p>
		<p><strong>Usuario: $email<br />
		Password: $passwd</strong></p>
		<p>Gu&aacute;rdelos en lugar seguro, pues las contrase&ntilde;as son cifradas y no se pueden recuperar, aunque si podr&aacute; solicitar una nueva.</p>
		<p>Muchas gracias por su confianza y esperamos le sirva de ayuda nuestro servicio.</p>
		<p>No responda a esta direcci&oacute;n de email, pues no es supervisada, solo se utiliza para fines administrativos.</p>
		<p>Un cordial saludo</p>
		<p>El equipo de buscarpiso.com</p>
		</body>
		</html>";
		
		$config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $config['wordwrap'] = TRUE;
		$this->email->initialize($config);
		$this->email->from('admin@buscarpiso.com', 'Administración buscarpiso.com');
		$this->email->to($email);
		$this->email->subject('Registro en buscarpiso.com');
		$this->email->message($html);
		$this->email->send();
    }

	public function token()
    {
        $token = md5(uniqid(rand(),true));
        $this->session->set_userdata('token',$token);
        return $token;
    }
    
    public function logout()
    {
        $this->session->sess_destroy();
        delete_cookie('recuerda');
		redirect(base_url()); 
    }
	
}
/* End of file login.php */
/* Location: ./application/controllers/login.php */