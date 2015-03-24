<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends CI_Controller {

    private $menuHtml = NULL;
    private $itemsModulo = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('menu_model');
		$this->load->model('modulos_model');
		$this->load->model('comunes_model');
		$this->load->library(array('image_lib'));
		$this->load->helper(array('url','form','MY_menu'));
		//$this->output->enable_profiler(TRUE);
    }

	public function index()
	{
		
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$data['titulo'] = 'Volcano Restaurant - Panel de Control';
			$data['modulos'] = $this->comunes_model->_modulos();
			$this->load->view('admin/head_view', $data);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/menuadmin_view', $data);
			$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
        }
	}

	
	public function nuevo()
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$data['titulo'] = 'Volcano Restaurant - ALTA DE MENÚ';
			$data['modulos'] = $this->comunes_model->_modulos();
			$data['ultimoId'] = $this->menu_model->nextid('menu');
			$data['comboMenuSup'] = $this->_pinta_combo(0,'0','','','');
			$this->menuHtml = '';
			$data['comboMenuLat'] = $this->_pinta_combo(0,'1','','','');
			$this->menuHtml = '';
			$data['comboMenuInf'] = $this->_pinta_combo(0,'2','','',''); 

			$this->load->view('admin/head_view', $data, FALSE);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/nuevomenu_view');
			$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
        }
	}

	public function delmenu($id)
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			if($this->menu_model->del_menu($id))
			{
				$data['titulo'] = 'Volcano Restaurant - BORRAR MENÚ';
				$data['modulos'] = $this->comunes_model->_modulos();
				$data['guardado'] = 'El registro ha sido borrado.';
				$this->load->view('admin/head_view', $data);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				$this->load->view('admin/borramenuresult_view',$data);
				$this->load->view('admin/pie_view');
			}else{
				$$data['titulo'] = 'Volcano restaurant - BORRANDO MENU';
				$data['data_menu'] = $this->menu_model->_data_menu($id);
				//$data['data_galeria'] = $this->galerias_model->_data_galeria($id);
				$data['comboMenuSup'] = $this->_pinta_combo(0,'0',$data['data_menu']->subid,'','edit');
				$this->menuHtml = '';
				$data['comboMenuLat'] = $this->_pinta_combo(0,'1',$data['data_menu']->subid,'','edit');
				$this->menuHtml = '';
				$data['comboMenuInf'] = $this->_pinta_combo(0,'2',$data['data_menu']->subid,'','edit'); 

				$this->load->view('admin/head_view', $data);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				$this->load->view('admin/editmenu_view',$data);
				$this->load->view('admin/pie_view');
			}
		}else{
			redirect(base_url().'login');
        }
	}

	public function editar($id)
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$data['titulo'] = 'Volcano restaurant - EDITANDO MENU';
			$data['modulos'] = $this->comunes_model->_modulos();
			$data['data_menu'] = $this->menu_model->_data_menu($id);
			$data['ultimoId'] = $this->menu_model->nextid('menu');
			$idiomas = $this->menu_model->_idiomas();
			foreach($idiomas as $idioma)
			{
				$idRed = $idioma->reducido;
				$data["data_menu_$idioma->reducido"] = $this->menu_model->_data_menu($id,$idRed);
			}
			//$data['data_galeria'] = $this->galerias_model->_data_galeria($id);
			$data['comboMenuSup'] = $this->_pinta_combo(0,'0',$data['data_menu']->subid,'','edit');
			$this->menuHtml = '';
			$data['comboMenuLat'] = $this->_pinta_combo(0,'1',$data['data_menu']->subid,'','edit');
			$this->menuHtml = '';
			$data['comboMenuInf'] = $this->_pinta_combo(0,'2',$data['data_menu']->subid,'','edit'); 
			$data['idiomas'] = $this->menu_model->_idiomas();

			$this->load->view('admin/head_view', $data);
			$this->load->view('admin/cabecera_view',$data);
			$this->load->view('admin/sidebar_view');
			$this->load->view('admin/editmenu_view',$data);
			$this->load->view('admin/pie_view');
		}else{
			redirect(base_url().'login');
        }
	}

	public function guarda_menu($id,$idmenu='',$imgs,$tipo='')
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$this->form_validation->set_rules('menu', 'Menu', 'required|trim|min_length[2]|max_length[150]|xss_clean');
			$this->form_validation->set_rules('titulo', 'Título de Menu', 'required|trim|min_length[2]|max_length[150]|xss_clean');
			$this->form_validation->set_rules('contenido', 'Contenido', 'required|trim|min_length[10]|xss_clean');
			if($tipo === 'editgaleria'){
				$data['titulo'] = 'Volcano restaurant - EDITANDO GALERÍA';
			}else{
				$data['titulo'] = 'Volcano restaurant - EDITANDO MENÚ';
			}
			$data['modulos'] = $this->comunes_model->_modulos();
			$data['id'] = $idmenu;
			if($this->form_validation->run() == FALSE)
	        {
				$data['data_menu'] = $this->menu_model->_data_menu($id);
				$data['comboMenuSup'] = $this->_pinta_combo(0,'0',$data['data_menu']->subid,'','edit');
				$this->menuHtml = '';
				$data['comboMenuLat'] = $this->_pinta_combo(0,'1',$data['data_menu']->subid,'','edit');
				$this->menuHtml = '';
				$data['comboMenuInf'] = $this->_pinta_combo(0,'2',$data['data_menu']->subid,'','edit'); 
				
				$this->load->view('admin/head_view', $data);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				if($tipo === 'editgaleria'){
					$this->load->view('admin/editgaleria_view',$data);
				}else{
					$this->load->view('admin/editmenu_view',$data);
				}
				$this->load->view('admin/pie_view');
			}else{
				if(count($imgs[0]) > 0){
					$images = $imgs;
					foreach($images as $image){
						if(!empty($image)){
							$imagen[] = $image['upload_data']['file_name'];
						}
					} 
				}else{
					$imagen = '';
				}
				if($tipo === 'editgaleria'){
					if($this->galerias_model->_editgaleria($id,$idmenu)){
						$galeria = TRUE;
					}else{
						$data['guardado'] = 'Ha ocurrido un error. Vuelva a intentarlo más tarde';
						exit;
					}
				}
				if($this->menu_model->edita_menu($id,$imagen)){
					if($tipo === 'editgaleria'){
						$data['guardado'] = 'La galería ha sido actualizado correctamente';
					}else{
						$data['guardado'] = 'El menú ha sido actualizado correctamente';
					}
					
					if(count($imgs[0]) > 0){
						
						$data['img_upload'][] = 'Archivos subidos: ';
						foreach($imgs as $img){
							if(!empty($img)){
								$data['img_upload'][] = $img['upload_data']['file_name'].' ';
							}
						}
					}
				}else{
					$data['guardado'] = 'Ha ocurrido un error. Por favor vuelva a intentarlo más tarde';
				}
				$this->load->view('admin/head_view', $data);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				if($tipo === 'editgaleria'){
					$this->load->view('admin/editgaleriaresult_view',$data);
				}else{
					$this->load->view('admin/editmenuresult_view',$data);
				}
				$this->load->view('admin/pie_view');
			}
		}else{
			redirect(base_url().'login');
        }
	}

	// AÑADIR MENÚ Y/O GALERÏA
	public function menu_alta($imgs,$idmenu,$tipo='',$idioma)
	{
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$galeria = FALSE;
			if($tipo === 'galeria'){
				$this->form_validation->set_rules('galeria', 'Galería', 'required|trim|min_length[2]|max_length[150]|xss_clean');
				$this->form_validation->set_rules('descripcion', 'Descripción', 'required|trim|min_length[2]|max_length[150]|xss_clean');
			}
			$this->form_validation->set_rules('menu', 'Menu', 'required|trim|min_length[2]|max_length[150]|xss_clean');
			$this->form_validation->set_rules('titulo', 'Título de Menu', 'required|trim|min_length[2]|max_length[150]|xss_clean');
			$this->form_validation->set_rules('contenido', 'Contenido', 'required|trim|min_length[10]|xss_clean');
			$this->form_validation->set_message('required','<i class="fa fa-info-circle"></i> El %s es obligatorio');
			if($this->form_validation->run() == FALSE)
	        {
				$data['titulo'] = 'Volcano Restaurant - ALTA NUEVO MENÚ';
				$data['modulos'] = $this->comunes_model->_modulos();
				$data['ultimoId'] = $this->menu_model->nextid('menu');
				$data['comboMenuSup'] = $this->_pinta_combo(0,'0','','','');
				$this->menuHtml = '';
				$data['comboMenuLat'] = $this->_pinta_combo(0,'1','','','');
				$this->menuHtml = '';
				$data['comboMenuInf'] = $this->_pinta_combo(0,'2','','',''); 
			
				$this->load->view('admin/head_view', $data);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				if($tipo === 'galeria'){
					$this->load->view('admin/addgaleria_view',$data);
				}else{
					$this->load->view('admin/nuevomenu_view',$data);
				}
				$this->load->view('admin/pie_view');
			}else{
				if(count($imgs[0]) > 0){
					$images = $imgs;
					foreach($images as $image){
						if(!empty($image)){
							$imagen[] = $image['upload_data']['file_name'];
						}
					} 
				}else{
					$imagen = '';
				}
				if($tipo === 'addgaleria'){
					if($this->galerias_model->_addgaleria()){
						$galeria = TRUE;
					}else{
						$data['guardado'] = 'Ha ocurrido un error. Vuelva a intentarlo más tarde';
						exit;
					}
				}
				
				if($this->menu_model->alta_menu($imagen,$idioma)){
					if($galeria){
						$data['guardado'] = 'Galería añadida correctamente';
					}else{
						$data['guardado'] = 'El menú ha sido dado de alta correctamente Idioma: '.$idioma;
					}
					if(count($imgs[0]) > 0){
						foreach($imgs as $img){
							if(!empty($img)){
								$data['img_upload'][] = $img['upload_data']['file_name'].' ';
							}
						}
					}
				}else{
					$data['guardado'] = 'Ha ocurrido un error. Por favor vuelva a intentarlo más tarde';
				}

				$data['idmenu'] = $idmenu;
				$data['titulo'] = 'Volcano Restaurant - EDITANDO MENU';
				$data['modulos'] = $this->comunes_model->_modulos();
				$data['id'] = $this->menu_model->nextid('menu') - 1;
				$this->load->view('admin/head_view', $data, FALSE);
				$this->load->view('admin/cabecera_view',$data);
				$this->load->view('admin/sidebar_view');
				if($tipo === 'addgaleria'){
					$this->load->view('admin/addgaleriaresult_view',$data);
				}else{
					$this->load->view('admin/addmenuresult_view',$data);
				}
				$this->load->view('admin/pie_view');
			}
		}else{
			redirect(base_url().'login');
        }
	}

//FUNCIÓN PARA SUBIR LA IMAGEN Y VALIDAR EL TÍTULO
    public function guardar() {
		if($this->session->userdata('is_logued_in') === TRUE)
		{
			$id = $this->uri->segment(4);
			$modo = $this->uri->segment(3);
			$idmenu = $this->input->post('idmenu');
			$idioma = $this->input->post('idioma');
			$data[] = array();
	        $config['upload_path'] = './assets/img/img_menus/';
	        $config['allowed_types'] = 'gif|jpg|png';
	        $config['max_size'] = '9000';
	        $config['max_width'] = '4320';
	        $config['max_height'] = '4320';
	 		//var_dump($_FILES);
			
	        $this->load->library('upload', $config);
	        //SI LA IMAGEN FALLA AL SUBIR MOSTRAMOS EL ERROR EN LA VISTA UPLOAD_VIEW
	        if($_FILES){
				foreach($_FILES as $file => $key){
					if( ! empty($key['name'])){
						//echo 'imagen: '.$menu_imagen . ' - ' . $titulo_imagen;
						if (!$this->upload->do_upload($file)) {
							$error['error'] = array('error' => $this->upload->display_errors());
							$error['titulo'] = 'Volcano Restaurant - EDITANDO MENU';
							$error['modulos'] = $this->comunes_model->_modulos();
							$error['id'] = $id;
							$error['guardado'] = 'Ha ocurrido un error. Por favor vuelva a intentarlo más tarde';
							//echo 'aca está: ';
							$this->load->view('admin/head_view', $error);
							$this->load->view('admin/cabecera_view',$error);
							$this->load->view('admin/sidebar_view');
							$this->load->view('admin/addmenuresult_view', $error);
							$this->load->view('admin/pie_view');
						} else {
							$file_info = $this->upload->data();
							//USAMOS LA FUNCIÓN create_thumbnail Y LE PASAMOS EL NOMBRE DE LA IMAGEN,
							//ASÍ YA TENEMOS LA IMAGEN REDIMENSIONADA
							$nombre_file[] = $file_info['file_name'];
							$this->_resize_image($file_info['file_name']);
							$this->_create_thumbnail($file_info['file_name']);
							$imgs[] = array('upload_data' => $this->upload->data());		
						}
					}else{
						$imgs[] = array();
					}
				}
			}
			if($modo === 'edit'){
				$this->guarda_menu($id,$idmenu,$imgs,'');       
			}elseif($modo === 'add'){
				$this->menu_alta($imgs,$idmenu,'',$idioma);       
			}elseif($modo === 'editgaleria'){
				$this->guarda_menu($id,$idmenu,$imgs,$modo);
			}elseif($modo === 'addgaleria'){
				$this->menu_alta($imgs,$idmenu,$modo,$idioma);
			}
		}else{
			redirect(base_url().'login');
        }
    }
    //FUNCIÓN PARA CREAR LA MINIATURA A LA MEDIDA QUE LE DIGAMOS
    function _create_thumbnail($filename){
        $config['image_library'] = 'gd2';
        //CARPETA EN LA QUE ESTÁ LA IMAGEN A REDIMENSIONAR
        $config['source_image'] = './assets/img/img_menus/'.$filename;
        $config['create_thumb'] = TRUE;
		$config['thumb_marker'] = '_thumb';
        $config['maintain_ratio'] = TRUE;
        //CARPETA EN LA QUE GUARDAMOS LA MINIATURA
        $config['new_image']='./assets/img/img_menus/';
        $config['width'] = 130;
        $config['height'] = 150;
        $this->image_lib->initialize($config);
        $this->image_lib->resize();
		$this->image_lib->clear();
    }
    function _resize_image($filename){
        $config1['image_library'] = 'gd2';
        //CARPETA EN LA QUE ESTÁ LA IMAGEN A REDIMENSIONAR
        $config1['source_image'] = './assets/img/img_menus/'.$filename;
        $config1['maintain_ratio'] = TRUE;
        //CARPETA EN LA QUE GUARDAMOS LA MINIATURA
        $config1['width'] = 1170;
        $config1['height'] = 660;
        $this->image_lib->initialize($config1);
        $this->image_lib->resize();
		$this->image_lib->clear();
    }
	
	function listamodulos(){
		$items = $this->menu_model->_listarmodulos();
		$this->itemsModulo .= '<label for="plantilla">MÓDULO</label><select name="subid" class="form-control">';
		$this->itemsModulo .= '<option value="" >- Seleccione un módulo -</<option>';

		foreach($items as $modulo){
			$this->itemsModulo .= '<option value="'.$modulo->valor.'" >'.$modulo->nombre.'</<option>';
		}
		$this->itemsModulo .= '</select>';
		echo $this->itemsModulo;
	}

	function _pinta_combo($subid,$ubi,$idmenu,$espacios="",$tipo="")
	{
		$espadre = "";
		/* recupero los datos de la tabla menu */
		$result = $this->menu_model->_menu($subid,$ubi);

		if (!$result) {
			return;
		} else {
			foreach($result as $row){
				if($tipo == 'edit'){
					$espadre = $this->menu_model->_espadre($idmenu);
				}
				/* empiezo a pintar el menu */
				$this->menuHtml .= '<option value="'.$row->id.'"';
				if($tipo == 'edit' && $espadre != ''){
					if($espadre->id == $row->id){ $this->menuHtml .= ' selected'; }
				}
				$this->menuHtml .= ' ubicacion="'.$ubi.'">'.$espacios.'&raquo;<i class="icon-double-angle-right"> '.$row->menu.'</i></option>';
				$tmpparentid = $row->id;
				/* Vuelvo a ejecutar con el id actual */
				$this->_pinta_combo($tmpparentid, $ubi, $idmenu, $espacios.'&nbsp;&nbsp;&nbsp;', $tipo);
			}
		}
		return $this->menuHtml;
	}

	public function actualizaOrden()
	{
		$Getorden = $this->input->get('orden');
		foreach ($Getorden as $orden => $item){
			//echo $Getorden; 
			//var_dump($Getorden); 
			$this->menu_model->actOrden($item,$orden);
		}

		return 'Orden guardado correctamente.';
	}
}

/* End of file pagina.php */
/* Location: ./application/controllers/pagina.php */