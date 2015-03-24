<?php $id = $this->uri->segment(3); ?>
<!-- Main bar -->
<div class="mainbar">
  
  <!-- Page heading -->
  <div class="page-head">
    <h2 class="pull-left"><i class="fa fa-tag"></i> Nuevo Idioma</h2>
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
              <div class="pull-left"><i class="fa fa-plus"></i> Añadir Nuevo Idioma
                <span class="pull-right ml15"><a class="btn btn-xs btn-default" href="<?php echo base_url(); ?>idiomas/listar"><i class="fa fa-angle-double-left"></i> Volver a Ver la Gestión de Idiomas</a></span></div>
                <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
                <div class="clearfix"></div>
              </div>
              <div class="widget-content">
                <div class="padd"> <br />
                  <!-- Form starts.  -->
                  
                  <?php echo validation_errors(); ?>
                  <div class="box-info form">
                    <?php if(isset($guardado)){ ?>
                    <div class="alert alert-success">
                      <p><i class="fa fa-info-circle bg"></i> <?php echo $guardado; ?></p>
                      <?php
                      if(isset($error)){
                        foreach($error as $fail){
                          echo '<p>Error: '.$fail.'</p>';
                        }
                      }
                      ?>
                    </div>
                    <?php } ?>
                    <?php $atributos = array('class' => 'form-horizontal', 'id' => 'anadirIdioma', 'role' => 'form');
                      //$oculto = array('id' => $data_menu->id);
                    echo form_open_multipart('idiomas/addidioma', $atributos); ?>
                      <div class="form-group">
                        <div class="col-sm-6">
                          <label for="nombre">TÍTULO</label>
                          <input type="text" name="titulo" id="titulo" required class="form-control" value="">
                        </div>
                        <div class="col-sm-6">
                          <label for="descripcion">DESCRIPCIÓN DE LA CATEGORÍA</label>
                          <input type="text" name="descripcion" id="descripcion" class="form-control" value="">
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-12 pull-right">
                          <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Añadir Categoría</button>
                        </div>
                      </div>
                      <input type="hidden" name="idioma" value="es">
                  </form>
                </div>
              </div>
              <div class="widget-foot">
                <div id="info"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>