<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Menu Helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Manuel Ruiz-Falcó
 */

// ------------------------------------------------------------------------

if ( ! function_exists('pinta_menu'))
{
	function pinta_menu($subid,$ubi,$idul,$i=0) {
		$ci = &get_instance();
		$ci->load->model("menu_model");
		$ci->db->where('idioma','es');
		$result = $ci->menu_model->_menu($subid,$ubi);
		$clase_a = '';
		$valor = '';
		$cl_activar = '';
		$txt_activar = '';
		if (!$result) {
			return;
		} else {
			if($subid === 0){
				echo '<ul class="task nav-bar pai" id="menus">';
			}else{
				echo '<ul class="task collapse fillo" id="menu_'.($subid).'">';
			}
			$i = 0;
			foreach($result as $row){
				$i++;
				$id = $row->id;
				if($subid !== 0){
					$clase_a = 'child';
				}
				$tipoplantilla = $row->tipoplantilla;
				switch ($tipoplantilla){
					case 0:
					$tipoplantilla_txt = 'No usa';
					break;
					case 1:
					$tipoplantilla_txt = 'Plantilla';
					break;
					case 2:
					$tipoplantilla_txt = 'Enlace externo';
					break;
					case 3:
					$tipoplantilla_txt = 'Módulo '.$row->plantilla;
					break;
				}
				if($row->activar != 1){
					$cl_activar = ' class="alert alert-info"';
					$cl_li_activar = ' class="disable '.$i.'"';
					$txt_activar = ' [Inactivo]';
				}else{
					$cl_activar = '';
					$cl_li_activar = '';
					$txt_activar = '';
				}
				if(get_nodos($id,$ubi)){
					$i_menu = '<i class="fa fa-folder-o" data-toggle="tooltip" title="Ver submenús"></i>';
					$a_menu = ' href="#" data-toggle="collapse" data-target="#menu_'.$row->id.'" title="Ver submenús"';
					$s_menu = ' data-toggle="tooltip" title="Ver submenús"';
				}else{
					$i_menu = '<i class="fa fa-file-o"></i>';
					$a_menu = '';
					$s_menu = '';
				}
				echo '<li id="orden_'.$row->id.'"'.$cl_li_activar.' data-toggle="tooltip" title="Arrastrar para cambiar orden"><a class="col-lg-11"'.$a_menu.$cl_activar.'>'.$i_menu.$row->menu;
				if($row->usaplantilla !== 'NO'){
					echo ' - [ '.$tipoplantilla_txt.' ]';
				}
				echo $txt_activar;
				echo '</a>';
				//echo '<div class="additional-btn lista">';
				echo '<span class="text-right"><a class="additional-icon" href="#fakelink"><i class="fa fa-eye" data-toggle="tooltip" title="Ver el contenido del menú"></i></a>';
				echo '<a class="additional-icon" href="'.base_url().'menu/editar/'.$row->idmenu.'"><i class="fa fa-edit" data-toggle="tooltip" title="Editar menú &quot;'.$row->menu.'&quot;"></i></a></span>';
				//echo '</div>';
				$tmpsubid = $row->id;
				$i++;
				pinta_menu($tmpsubid,$ubi,$idul,$i);
				echo '</li>';
			}
			echo '</ul>';
		}
	}
}

/* Funcion para saber si el padre tiene hijos */ 
if ( ! function_exists('get_nodos'))
{
	function get_nodos($id,$ubi) 
	{
		$ci = &get_instance();
		$ci->load->model("menu_model");
		$espadre = $ci->menu_model->_tiene_hijos($id,$ubi);
		if($espadre){
			return TRUE;
		}else{
			return FALSE;
		}
	}
}

/* End of file MY_menu_helper.php */
/* Location: ./application/helpers/MY_menu_helper.php */