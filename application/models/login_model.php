<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*/
class Login_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
		$this->load->helper('date');
    }
    function mkPasswd() {
		$consts='bcdgklmnprstuvxyz';
		$vowels='aeiou';
		$nums='123456789';
		$simbol = '@#$%\&';
	
		for ($x=0; $x < 6; $x++) {
			mt_srand ((double) microtime() * 1000000);
			$const[$x] = substr($consts,mt_rand(0,strlen($consts)-1),1);
			$vow[$x] = substr($vowels,mt_rand(0,strlen($vowels)-1),1);
			$num[$x] = substr($nums,mt_rand(0,strlen($nums)-1),1);
			$simbol[$x] = substr($simbol,mt_rand(0,strlen($simbol)-1),1);
		}
		return $const[0] . $vow[0] .$num[2] . $const[1] . $vow[1] . $const[3] . $simbol[5] . $num[3] . $const[4];
	
	}
	function crearKey(){
		$fecha = round (date("U")/1000);
		srand ($fecha);
		$validkey = rand (1,100000000);
		$validkey = md5 ($validkey);
		return $validkey;
	}

    public function login_user($login,$password)
    {
        $this->db->where('login',$login);
        $this->db->where('password',$password);
        $query = $this->db->get('usuarios');
        if($query->num_rows() == 1)
        {
           	return $query->row();
        }else{
            $this->session->set_flashdata('usuario_incorrecto','<p class="error center"><i class="fa fa-warning"></i>Los datos introducidos son incorrectos</p>');
            redirect(base_url().'login','refresh');
        }
    }
	public function nuevo_registro()
	{
		$data = $this->input->post();
		$data['validkey'] = $this->crearKey();
		$passw_sin = $this->mkPasswd();
		$data['password'] = sha1($passw_sin);
		$data['login'] = $this->input->post('email');
		$data['empresa'] = 'NO';
		$data['fecha_alta'] = now();
		$data['activo'] = 'NO';
		$data['promotora'] = 'NO';
		$data['nivel'] = 'usu';
		$data['anuncios'] = 0;
		$this->db->insert('usuarios', $data);
		$datos = array('password' => $passw_sin,'validKey' => $data['validkey']);
		return $datos;
	}
}