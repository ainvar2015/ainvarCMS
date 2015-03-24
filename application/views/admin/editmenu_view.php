<!-- Modal -->
<div id="borraMenu" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="borraMenuLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">BORRAR MENÚ <strong>"<?php echo strtoupper($data_menu->menu); ?>"</strong></h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo base_url(); ?>menu/delmenu/<?php echo $data_menu->id; ?>" method="post" class="formOculto" name="Elimina_Menu" id="Elimina_Menu">
          <p>¿Está seguro de borrar este registro? Una vez eliminado no se podrá recuperar.</p>
          <button type="submit" class="btn btn-danger"><i class="fa fa-trash-o"></i> Eliminar Registro</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cerrar Ventana</button>
      </div>
    </div>
  </div>
</div>
<!-- FIN MODAL -->

<!-- Main bar -->
<div class="mainbar">
  
  <!-- Page heading -->
  <div class="page-head">
    <h2 class="pull-left"><i class="fa fa-navicon"></i> Menús <small>Editar Menú</small></h2>
    
    <div class="clearfix"></div>
  </div>
  <!-- Page heading ends -->
  
  <!-- Matter -->
  <div class="matter">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="widget wgreen">
            <div class="widget-head">
              <div class="pull-left"><i class="fa fa-edit"></i> Edición de Menús<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="<?php echo base_url(); ?>menu"><i class="fa fa-angle-double-left"></i> Volver a Menús</a></span>
              <?php if($data_menu->tipoplantilla === '3'){
              $modulo = $data_menu->plantilla;
              switch($modulo)
              {
              case 'carta':
              case 'bodega':
              $parametro = 'listar';
              break;
              case 'galerias':
              $parametro = 'vergaleria/'.$data_menu->param;
              break;
              }
              ?>
              <span class="pull-right ml15"><a class="btn btn-xs btn-default" href="<?php echo base_url().$data_menu->plantilla; ?>/<?php echo $parametro; ?>"><i class="fa fa-cubes"></i> Gestionar Módulo</a></span>
              <?php } ?>
            </div>
            <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
            <div class="clearfix"></div>
          </div>
          <div class="widget-content">
            <div class="padd"> <br />
              <ul id="menuTabs" class="nav nav-tabs">
                <?php $idmenu = $data_menu_es->idmenu; 
                $ref = $data_menu_es->ref; 
                foreach($idiomas as $idioma){
                if(${'data_menu_'.$idioma->reducido}){ 
                  ?>
                <li><a href="#<?php echo $idioma->reducido; ?>" data-toggle="tab"><i class="fa fa-edit"></i><?php echo $idioma->idioma; ?></a></li>
                <?php }else{ ?>
                <li><a href="#<?php echo $idioma->reducido; ?>" data-toggle="tab"><i class="fa fa-plus"></i><?php echo $idioma->idioma; ?></a></li>
                <?php }
                } ?>
              </ul>
              <div id="menuContent" class="tab-content">
                <?php foreach($idiomas as $idioma){
                if(${'data_menu_'.$idioma->reducido}){ ?>
                <div class="tab-pane fade in" id="<?php echo $idioma->reducido; ?>">
                  <!-- Form starts.  -->
                  <form action="<?php echo base_url(); ?>menu/guardar/edit/<?php echo ${'data_menu_'.$idioma->reducido}->id; ?>/<?php echo ${'data_menu_'.$idioma->reducido}->idmenu; ?>" method="post" enctype="multipart/form-data" id="add_menu_<?php echo $idioma->reducido; ?>" class="form-horizontal">
                    <h2 class="head">
                    <div class="form-group" style="padding-top:7px;">
                      <div class="col-sm-6">
                        <div class="checkbox">
                          <label class="control-label" for="activar">Activar: <?php echo $idioma->reducido; ?>
                            <input type="checkbox" value="1" name="activar" <?php if(${'data_menu_'.$idioma->reducido}->activar == "1"){ echo "checked"; } ?>>
                          </label>
                        </div>
                      </div>
                      <!-- Additional buttons -->
                      <div class="col-sm-6 text-right">
                        <button type="submit" class="btn btn-primary" rel="tooltip" data-original-title="Guardar los cambios"><i class="fa fa-edit"></i> Editar Menú</button>
                        <a href="#borraMenu" class="btn btn-danger md-trigger" data-toggle="modal" title="Eliminar este registro de la base de datos" data-modal="borraMenu"> <i class="fa fa-trash-o"></i> Borrar Menú</a>
                      </div>
                      <!-- End div .additional-button -->
                    </div>
                    </h2>
                    <?php if($idioma->reducido === 'es'){ ?>
                    <div class="form-group">
                      <div class="col-lg-3 col-md-3">
                        <label for="tipoplantilla">TIPO DE PLANTILLA</label>
                        <select name="tipoplantilla" id="tipoplantilla" class="form-control">
                          <option value="0" <?php if(${'data_menu_'.$idioma->reducido}->tipoplantilla == "0"){ echo "selected"; } ?>>No usa</option>
                          <option value="1" <?php if(${'data_menu_'.$idioma->reducido}->tipoplantilla == "1"){ echo "selected"; } ?>>Plantilla</option>
                          <option value="2" <?php if(${'data_menu_'.$idioma->reducido}->tipoplantilla == "2"){ echo "selected"; } ?>>Enlace externo</option>
                          <option value="3" <?php if(${'data_menu_'.$idioma->reducido}->tipoplantilla == "3"){ echo "selected"; } ?>>M&oacute;dulo</option>
                        </select>
                        <div class="c_plantilla">
                          <label for="plantilla">PLANTILLA</label>
                          <input type="text" name="plantilla" id="plantilla" class="form-control" value="<?php echo ${'data_menu_'.$idioma->reducido}->plantilla; ?>">
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="param">PARÁMETROS</label>
                        <input type="text" name="param" class="form-control" id="param" value="<?php echo ${'data_menu_'.$idioma->reducido}->param; ?>">
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="ubicacion">UBICACIÓN DEL MENÚ</label>
                        <select name="ubicacion" class="form-control">
                          <option value="0" <?php if(${'data_menu_'.$idioma->reducido}->ubicacion == "0"){ echo "selected"; } ?>> Superior</option>
                          <option value="1" <?php if(${'data_menu_'.$idioma->reducido}->ubicacion == "1"){ echo "selected"; } ?>> Lateral</option>
                          <option value="2" <?php if(${'data_menu_'.$idioma->reducido}->ubicacion == "2"){ echo "selected"; } ?>> Inferior</option>
                        </select>
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="subid">MEN&Uacute; DE QUE DEPENDE</label>
                        <select name="subid" class="form-control">
                          <option value="0"> Men&uacute; Principal</option>
                          <optgroup label="Menú Superior">
                            <?php echo $comboMenuSup; ?>
                          </optgroup>
                          <optgroup label="Menú Lateral">
                            <?php echo $comboMenuLat; ?>
                          </optgroup>
                          <optgroup label="Menú Inferior">
                            <?php echo $comboMenuInf; ?>
                          </optgroup>
                        </select>
                      </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                      <div class="col-lg-5 col-md-5">
                        <label for="menu">MENÚ</label>
                        <input name="menu" type="text" class="form-control" required value="<?php echo ${'data_menu_'.$idioma->reducido}->menu; ?>" />
                      </div>
                      <div class="col-lg-5 col-md-5">
                        <label for="titulo">TÍTULO</label>
                        <input name="titulo" type="text" class="form-control" required value="<?php echo ${'data_menu_'.$idioma->reducido}->titulo; ?>" />
                      </div>
                      <?php if($idioma->reducido === 'es'){ ?>
                      <div class="col-lg-2 col-md-2">
                        <label for="ref">REFERENCIA</label>
                        <input name="ref" type="text" class="form-control" required value="<?php echo ${'data_menu_'.$idioma->reducido}->ref; ?>" />
                      </div>
                      <?php } ?>
                    </div>
                    <?php if($idioma->reducido === 'es'){ ?>
                    <div class="form-group">
                      <div class="col-lg-6 col-md-6">
                        <?php if(${'data_menu_'.$idioma->reducido}->menu_imagen != ''){ ?>
                        <label>IMAGEN ACTUAL: <a href="<?php echo base_url(); ?>assets/img/img_menus/<?php echo ${'data_menu_'.$idioma->reducido}->menu_imagen; ?>" title="Imagen" class="lightbox" title="Ver Imagen"><?php echo ${'data_menu_'.$idioma->reducido}->menu_imagen; ?></a></label><br />
                        <?php } ?>
                        <label for="menu_imagen">IMAGEN DEL MENÚ</label>
                        <input name="menu_imagen" type="file" id="menu_imagen" class="btn btn-default btn-xs" data-filename-placement="intside"> <a href="javascript:void();" id="clear">Limpiar</a>
                      </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                      <div class="col-lg-12 col-md-12">
                        <label for="contenido">CONTENIDO</label>
                        <textarea name="contenido" class="editor" style="width:100%"><?php echo ${'data_menu_'.$idioma->reducido}->contenido; ?></textarea>
                      </div>
                    </div>
                    <input type="hidden" name="idmenu" value="<?php echo $idmenu; ?>">
                    <input type="hidden" name="idioma" value="<?php echo ${'data_menu_'.$idioma->reducido}->idioma; ?>">
                  </form>
                </div>
                <?php }else{ ?>
                <div class="tab-pane fade in" id="<?php echo $idioma->reducido; ?>">
                  <form action="<?php echo base_url(); ?>menu/guardar/add/<?php echo $idmenu; ?>" method="post" enctype="multipart/form-data" id="add_menu" class="form-horizontal">
                    <h2 class="head">
                    <div class="form-group" style="padding-top:7px;">
                      <div class="col-sm-4">
                        <div class="checkbox">
                          <label class="control-label" for="activar">Activar:<?php echo $idioma->reducido; ?>
                            <input type="checkbox" value="1" name="activar" checked>
                          </label>
                        </div>
                      </div>
                      <div class="col-sm-4 center">
                        <label class="control-label">Próximo ID: <?php echo $ultimoId; ?></label>
                      </div>
                      <!-- Additional buttons -->
                      <div class="col-sm-4 text-right">
                        <button type="submit" class="btn btn-primary" rel="tooltip" data-original-title="Guardar los cambios"><i class="fa fa-plus"></i> Guardar</button>
                      </div>
                      <!-- End div .additional-button -->
                    </div>
                    </h2>
                    <div class="form-group">
                      <div class="col-lg-3 col-md-3">
                        <label for="tipoplantilla">TIPO DE PLANTILLA</label>
                        <select name="tipoplantilla" id="tipoplantilla" class="form-control">
                          <option value="0">No usa</option>
                          <option value="1">Plantilla</option>
                          <option value="2">Enlace externo</option>
                          <option value="3">M&oacute;dulo</option>
                        </select>
                        <div class="c_plantilla">
                          <label for="plantilla">PLANTILLA</label>
                          <input type="text" name="plantilla" id="plantilla" class="form-control">
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="param">PARÁMETROS</label>
                        <input type="text" name="param" class="form-control" id="param" value="">
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="ubicacion">UBICACIÓN DEL MENÚ</label>
                        <select name="ubicacion" class="form-control">
                          <option value="0"> Superior</option>
                          <option value="1"> Lateral</option>
                          <option value="2"> Inferior</option>
                        </select>
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="subid">MEN&Uacute; DE QUE DEPENDE</label>
                        <select name="subid" class="form-control">
                          <option value="0"> Men&uacute; Principal</option>
                          <optgroup label="Menú Superior">
                            <?php echo $comboMenuSup; ?>
                          </optgroup>
                          <optgroup label="Menú Lateral">
                            <?php echo $comboMenuLat; ?>
                          </optgroup>
                          <optgroup label="Menú Inferior">
                            <?php echo $comboMenuInf; ?>
                          </optgroup>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-lg-5 col-md-5">
                        <label for="menu">MENÚ</label>
                        <input name="menu" type="text" required class="form-control" value="" />
                      </div>
                      <div class="col-lg-5 col-md-5">
                        <label for="titulo">TÍTULO</label>
                        <input name="titulo" required type="text" class="form-control" value="" />
                      </div>
                      <div class="col-lg-2 col-md-2">
                        <label for="ref">REFERENCIA</label>
                        <input name="ref" type="text" class="form-control" required value="<?php echo $data_menu_es->ref; ?>" />
                      </div>

                    </div>
                    <div class="form-group">
                      <div class="col-lg-6 col-md-6">
                        <label for="menu_imagen">IMAGEN DEL MENÚ</label>
                        <input name="menu_imagen" type="file" id="menu_imagen" class="btn btn-default btn-xs"> <a href="javascript:void();" id="clear">Limpiar</a>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-lg-12 col-md-12">
                        <label for="contenido">CONTENIDO</label>
                        <textarea name="contenido" class="editor" style="width:100%"></textarea>
                      </div>
                    </div>
                    <input type="hidden" name="idmenu" value="<?php echo $idmenu; ?>">
                    <input type="hidden" name="idioma" value="<?php echo $idioma->reducido; ?>">
                    <input type="hidden" name="ref" value="<?php echo $ref; ?>">
                  </form>
                </div>
                <?php }
                } ?>
              </div>
            </div>
          </div>
          <div class="widget-foot">
            <?php echo validation_errors(); ?>
            <div id="info"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>