<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pagina extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('pagina_model');
		$this->load->model('comunes_model');
    }

    /*	HOME   */
    /***********/
	public function index()
	{
		if($this->input->cookie('idioma') != ''){
			$lang = get_cookie('idioma');
		}else{
			$lang = 'es';
			$cookie = array(
				'name'   => 'idioma',
				'value'  => 'es',
				'expire' => '1296000',
				'domain' => '.volcanorestaurant.es',
				//'domain' => '.localhost',
				'path'   => '/'
			); 
			set_cookie($cookie);
		}
		$data['titulo'] = 'Volcano Restaurant';
		$data['imagenes'] = $this->_getimages('1','portada');
		$data['lineas'] = $this->_getlineas('1');
		$data['itemsmenu'] = $this->_getmenu($lang);
		$data['idiomas'] = $this->idiomas();
		$data['catsCarta'] = $this->_getCats('categoriascarta');
		$cuantosC = count($data['catsCarta']);
		$idcatC = NULL;
		$i=0;
		for($i;$i<$cuantosC;$i++)
		{
			$idcatC[] .= $data['catsCarta'][$i]->idcat;
		}
		foreach($idcatC as $catC)
		{
			$data['itemscarta_'.$catC] = $this->_datacarta($catC,$lang);
		}
		$data['contcarta'] = $this->_getalldatamenu('3',$lang);
		$data['contquienes'] = $this->_getalldatamenu('2',$lang);
		$data['catsBodega'] = $this->_getCats('categoriasbodega');
		$cuantosB = count($data['catsBodega']);
		$idcatB = NULL;
		$i=0;
		for($i;$i<$cuantosB;$i++)
		{
			$idcatB[] .= $data['catsBodega'][$i]->idcat;
		}
		foreach($idcatB as $catB)
		{
			$data['itemsbodega_'.$catB] = $this->_databodega($catB,$lang);
		}
		$data['contBodega'] = $this->_getalldatamenu('8',$lang);
		$data['contGaleria'] = $this->_getalldatamenu('7',$lang);
		$data['fotosGal'] = $this->_getimages('2','platos');
		$data['contComollegar'] = $this->_getalldatamenu('6',$lang);
		$data['reservas'] = $this->_getalldatamenu('5',$lang);
		$data['contacto'] = $this->_getalldatamenu('4',$lang);
		$this->load->view('head_view', $data, FALSE);
		$this->load->view('cabecera_view', $data, FALSE);
		$this->load->view('home_view', $data, FALSE);
		$this->load->view('quienes_view', $data, FALSE);
		$this->load->view('carta_view', $data, FALSE);
		$this->load->view('bodega_view', $data, FALSE);
		$this->load->view('galeria_view',$data);
		$this->load->view('comollegar_view',$data);
		$this->load->view('reservas_view',$data);
		$this->load->view('contacto_view',$data);
		$this->load->view('pie_view', $data, FALSE);
	}

	public function idiomas()
	{
		if(get_cookie('idioma') != '')
		{
			$lg = get_cookie('idioma');
		}else{
			$lg = 'es';
		}
		$divIdiomas = '';
		//$divIdiomas .= "<ul id=\"idioma\"class=\"accura-social-icons accura-stacked accura-jump accura-full-height accura-bordered accura-small accura-colored-bg clearFix\">\n";
		$idiomas = $this->pagina_model->_idiomas();
		
		foreach($idiomas as $idioma)
		{
			$divIdiomas .= "<li>";
			if($idioma->reducido !== $lg)
			{ 		
				$divIdiomas .= "<a href=\"".base_url()."pagina/lang/".$idioma->reducido."\">";
				$divIdiomas .= "<img class=\"tra\" src=\"".base_url()."assets/img/banderas/".$idioma->reducido.".png\" ";
				$divIdiomas .= "border=\"0\" alt=\"".$idioma->idioma."\" ";
				$divIdiomas .= "title=\"".$idioma->idioma."\" />";
				$divIdiomas .= "</a>";
			} else {
				$divIdiomas .= "<span><img src=\"".base_url()."assets/img/banderas/".$idioma->reducido.".png\" ";
				$divIdiomas .= "border=\"0\" alt=\"".$idioma->idioma."\" ";
				$divIdiomas .= "title=\"".$idioma->idioma."\" /></span>";
			} 
			$divIdiomas .= "</li>\n";		
		}
		//$divIdiomas .= "</ul>\n";

		return $divIdiomas;
	}

	public function lang($idioma)
	{
		//echo $idioma;
		$cookie = array(
			'name'   => 'idioma',
			'value'  => $idioma,
			'expire' => '1296000',
			'domain' => '.volcanorestaurant.es',
			//'domain' => '.localhost',
			'path'   => '/'
		); 
		set_cookie($cookie);
		redirect(base_url(),'refresh');
		//$this->index();
	}

	function _getimages($gal,$tipo)
	{
		$imagenes = $this->pagina_model->_getdata('fotosgaleria',$gal);
		$imgs = NULL;
		$conta = count($imagenes);
		$i=0;
		if($tipo === 'portada'){
			foreach($imagenes as $imagen)
			{
				$i++;
				if($i < $conta)
				{
					$imgs .= "{ src:'".base_url()."assets/img/galerias/".$imagen->carpeta."/".$imagen->archivo."',fade:1000},\n";
				}else{
					$imgs .= "{ src:'".base_url()."assets/img/galerias/".$imagen->carpeta."/".$imagen->archivo."',fade:1000}";
				}
			}
		}else{
			/*foreach($imagenes as $imagen)
			{
				$i++;
				if($i < $conta)
				{
					$imgs .= $imagen->carpeta."/".$imagen->archivo.',';
				}else{
					$imgs .= $imagen->carpeta."/".$imagen->archivo;
				}
			}*/
			$imgs = $imagenes;
		}
		return $imgs;
	}

	function _getlineas($gal)
	{
		$lineas = $this->pagina_model->_getdata('fotosgaleria',$gal);
		$elemento = NULL;
		$i=0;
		foreach($lineas as $linea)
		{
			if($i == 0)
			{
				$elemento .= '<li class="sliding_title">'.$linea->titulo.'</li>';
			}else{
				$elemento .= '<li>'.$linea->titulo.'</li>';
			}
			$i++;
		}
		return $elemento;
	}

	function _getmenu($idioma='es')
	{
		$datamenu = $this->pagina_model->_getdata('menu','',$idioma);
		$limenu = NULL;
		foreach($datamenu as $item)
		{
			$limenu .= "<li><a href=\"#".$item->ref."\" class=\"nav-link\">".$item->menu."</a></li>\n";
		}
		return $limenu;
	}

	function _getalldatamenu($id,$idioma='es')
	{
		$item = $this->pagina_model->_getalldata('menu',$id,$idioma);
		return $item;
	}

	function _getcarta()
	{
		$datacarta = $this->pagina_model->_getdata('carta');
		$itemcarta = NULL;
		foreach($datacarta as $item)
		{
			$itemcarta .= "	<div class=\"row\">
								<div class=\"menu_content clearfix\">
									<div class=\"col-md-3 menu_big\"><div class=\"row\"><img src=\"".base_url()."assets/img/carta/".$item->imagen."\"  class=\"img-responsive img_border\" alt=\"\" /></div></div>
									<div class=\"col-md-9 text-left\">
										<div class=\"title-splider\">
											<h4 class=\"clearfix\"><span class=\"left border_bottom\">".$item->nombre."</span><span class=\"right\">".$item->precio."&euro;</span></h4>
										</div>
											<p>".$item->descripcion."</p>
									</div>
								</div>
							</div>";
		}
		return $itemcarta;
	}
	
	function _getCats($tabla)
	{
		if($this->input->cookie('idioma') != ''){
			$lang = get_cookie('idioma');
		}else{
			$lang = 'es';
		}
		$dataCats = $this->pagina_model->_getcats($tabla,$lang);
		return $dataCats;
	}
	
	function _databodega($idcat,$idioma='es')
	{
		$data = $this->pagina_model->_getdatabodega($idcat,$idioma);
		return $data;
	}

	function _datacarta($idcat,$idioma='es')
	{
		$data = $this->pagina_model->_getdatacarta($idcat,$idioma);
		return $data;
	}

}
/********************************************************************************************************************/
/*	FIN PAGINA                                                                                                        */
/********************************************************************************************************************/
/* End of file pagina.php */
/* Location: ./application/controllers/pagina.php */