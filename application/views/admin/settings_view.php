<!-- Main bar -->
<div class="mainbar">
  
  <!-- Page heading -->
  <div class="page-head">
    <h2 class="pull-left"><i class="fa fa-cogs"></i> Modificar Configuración</h2>
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
              <div class="pull-left">Formulario de Configuración de la Aplicación<span class="pull-right ml15"></div>
              <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
              <div class="clearfix"></div>
              <div class="widget-content">
                <div class="padd"> <br />
                  <!-- Form starts.  -->
                  <?php $atributos = array('class'=>'form-horizontal','role'=>'form','name'=>'formEditConf','id'=>'formEditConf');
                  echo form_open(base_url('configura/guardar'),$atributos) ?>
                  <fieldset>
                    <legend>Datos de la Página</legend>
                    <div class="form-group">
                      <label for="login" class="col-lg-3 col-md-3 control-label text-right">Título de la página</label>
                      <div class="col-lg-9 col-md-9">
                        <input type="text" name="titulo_pagina" id="titulo_pagina" class="form-control" required placeholder="* Título" value="<?php echo $data_config->titulo_pagina; ?>" tabindex="1">
                        <?php echo form_error('titulo_pagina'); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="nombre" class="col-lg-3 col-md-3 control-label text-right">Base URL</label>
                      <div class="col-lg-9 col-md-9">
                        <p><?php echo $data_config->base_url; ?></p>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="nombre_bd" class="col-lg-3 col-md-3 control-label text-right">Nombre Base de Datos(Mysql)</label>
                      <div class="col-lg-9 col-md-9">
                        <p><?php echo $data_config->nombre_bd; ?></p>
                      </div>
                    </div>
                  </fieldset>
                  <fieldset>
                    <legend>Datos para la Empresa</legend>
                    <div class="form-group">
                      <label for="nom_empresa" class="col-lg-3 col-md-3 control-label text-right">Nombre de la Empresa</label>
                      <div class="col-lg-9 col-md-9">
                        <input type="text" name="nom_empresa" id="nom_empresa" class="form-control" placeholder="Escriba el Nombre de la Empresa" value="<?php echo $data_config->nom_empresa; ?>" tabindex="4">
                        <?php echo form_error('titulo'); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="direccion" class="col-lg-3 col-md-3 control-label text-right">Dirección de la Empresa</label>
                      <div class="col-lg-9 col-md-9">
                        <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Escriba la Dirección de la Empresa" value="<?php echo $data_config->direccion; ?>" tabindex="5">
                        <?php echo form_error('direccion'); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="poblacion" class="col-lg-3 col-md-3 control-label text-right">Población</label>
                      <div class="col-lg-9 col-md-9">
                        <input type="text" name="poblacion" id="poblacion" class="form-control" placeholder="Escriba la Población" value="<?php echo $data_config->poblacion; ?>" tabindex="6">
                        <?php echo form_error('poblacion'); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="provincia" class="col-lg-3 col-md-3 control-label text-right">Provincia</label>
                      <div class="col-lg-9 col-md-9">
                        <input type="text" name="provincia" id="provincia" class="form-control" placeholder="Escriba la Provincia" value="<?php echo $data_config->provincia; ?>" tabindex="7">
                        <?php echo form_error('provincia'); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="cod_postal" class="col-lg-3 col-md-3 control-label text-right">Código Postal</label>
                      <div class="col-lg-9 col-md-9">
                        <input type="text" name="cod_postal" id="cod_postal" class="form-control" placeholder="Escriba el Código Postal" value="<?php echo $data_config->cod_postal; ?>" tabindex="8">
                        <?php echo form_error('cod_postal'); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="pais" class="col-lg-3 col-md-3 control-label text-right">Pais</label>
                      <div class="col-lg-9 col-md-9">
                        <input type="text" name="pais" id="pais" class="form-control" placeholder="Seleccione el País" value="<?php echo $data_config->pais; ?>" tabindex="9">
                        <?php echo form_error('pais'); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="telefono" class="col-lg-3 col-md-3 control-label text-right">Teléfono</label>
                      <div class="col-lg-9 col-md-9">
                        <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Escriba el Teléfono" value="<?php echo $data_config->telefono; ?>" tabindex="10">
                        <?php echo form_error('telefono'); ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="cif" class="col-lg-3 col-md-3 control-label text-right">CIF</label>
                      <div class="col-lg-9 col-md-9">
                        <input type="text" name="cif" id="cif" class="form-control" placeholder="Escriba el CIF" value="<?php echo $data_config->cif; ?>" tabindex="11">
                        <?php echo form_error('cif'); ?>
                      </div>
                    </div>
                  </fieldset>
                  <div class="form-group">
                    <div class="col-sm-12 col-md-12 text-right">
                      <button type="submit" class="btn-sm btn-primary" tabindex="12"><i class="fa fa-cogs"></i> <i class="fa fa-edit"></i> Guardar Configuración</button>
                    </div>
                  </div>
                  <?php echo form_close(); ?>
                </div>
              </div>
              <div class="widget-foot">
                <!-- Footer goes here -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Matter Ends -->
  </div>
  <!-- Mainbar ends -->
  <div class="clearfix"></div>