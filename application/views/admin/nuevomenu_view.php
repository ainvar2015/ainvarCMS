<!-- Main bar -->
<div class="mainbar">
  
  <!-- Page heading -->
  <div class="page-head">
    <h2 class="pull-left"><i class="fa fa-navicon"></i> Nuevo Menú</h2>
    <div class="clearfix"></div>
  </div>
  <!-- Page heading ends -->
  
  <!-- Matter -->
  <div class="matter">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="widget wviolet">
            <div class="widget-head">
              <div class="pull-left"><i class="fa fa-plus"></i> Añadir Nuevo Menú<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="<?php echo base_url(); ?>menu"><i class="fa fa-angle-double-left"></i> Volver a Menús</a></span></div>
              <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
              <div class="clearfix"></div>
            </div>
            <div class="widget-content">
              <div class="padd"> <br />
                <!-- Form starts.  -->
                <form action="<?php echo base_url(); ?>menu/guardar/add" method="post" enctype="multipart/form-data" id="add_menu" class="form-horizontal">
                  <h2 class="head">
                  <div class="form-group">
                    <div class="col-sm-2">
                      <div class="checkbox">
                        <label class="control-label" for="activar">Activar:
                          <input type="checkbox" value="1" name="activar" checked>
                        </label>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <label for="creaidiomas">¿Desea crear todos los idiomas con estos datos?</label>
                      <input type="checkbox" name="creaidiomas" id="creaidiomas" value="1" checked="checked">
                    </div>
                    <div class="col-sm-2 center">
                      <label class="control-label">Próximo ID: <?php echo $ultimoId; ?></label>
                    </div>
                    <!-- Additional buttons -->
                    <div class="col-sm-2 text-right">
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
                    <div class="col-lg-6 col-md-6">
                      <label for="menu">MENÚ</label>
                      <input name="menu" type="text" required class="form-control" value="" />
                    </div>
                    <div class="col-lg-6 col-md-6">
                      <label for="titulo">TÍTULO</label>
                      <input name="titulo" required type="text" class="form-control" value="" />
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
                  <input type="hidden" name="idmenu" value="<?php echo $ultimoId; ?>">
                  <input type="hidden" name="idioma" value="es">
                </form>
              </div>
            </div>
            <div class="widget-foot">
              <div class="error"><?php echo validation_errors(); ?></div>
              <div id="info"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>