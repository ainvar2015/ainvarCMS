<!-- Main bar -->
<div class="mainbar">
  
  <!-- Page heading -->
  <div class="page-head">
    <h2 class="pull-left"><i class="fa fa-table"></i> Editar Usuario</h2>
    
    <!-- Breadcrumb -->
    <div class="bread-crumb pull-right"> <a href="index.html"><i class="fa fa-home"></i> Inicio</a>
    <!-- Divider -->
    <span class="divider">/</span> <a href="#" class="bread-current">Editar Usuario</a> </div>
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
              <div class="pull-left">Formulario de Edición Usuario<span class="pull-right ml15">
                <a class="btn btn-xs btn-default" href="<?php echo base_url(); ?>usuarios"><i class="fa fa-angle-double-left"></i> Volver a Usuarios</a></span><span class="pull-right ml15">
                <a class="btn btn-xs btn-default lightbox" href="<?php echo base_url(); ?>usuarios/password/<?php echo $data_usuario->id; ?>?lightbox[width]=35p&amp;lightbox[height]=310"><i class="fa fa-lock"></i> Cambiar Contraseña</a></span></div>
                <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
                <div class="clearfix"></div>
              </div>
              <div class="widget-content">
                <div class="padd">
                    <!-- Form starts.  -->
                    <?php $atributos = array('class'=>'form-horizontal','role'=>'form','name'=>'formEditUser','id'=>'formEditUser');
                      echo form_open(base_url('usuarios/accion/editar'),$atributos) ?>
                  <div class="form-group txt16px">
                    <div class="col-lg-12 col-md-12">
                      <div class="col-lg-2 col-md-2 text-right">Login Usuario</div>
                      <div class="col-lg-4 col-md-4">
                        <?php echo $data_usuario->login; ?>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                      <div class="col-lg-2 col-md-2 text-right">Nombre Usuario</div>
                      <div class="col-lg-4 col-md-4">
                        <?php echo $data_usuario->nombre; ?>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                      <div class="col-lg-2 col-md-2 text-right">Email</div>
                      <div class="col-lg-4 col-md-4">
                        <?php echo $data_usuario->email; ?>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                      <div class="col-lg-2 col-md-2 text-right">Teléfono</div>
                      <div class="col-lg-4 col-md-4">
                        <?php echo $data_usuario->telefono; ?>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                      <div class="col-lg-2 col-md-2 text-right">Nivel de Acceso</div>
                      <div class="col-lg-4 col-md-4">
                        <?php $nivel = $this->session->userdata('nivel');
                        switch ($nivel) {
                        case 'adm':
                        $elnivel = 'Administrador';
                        break;
                        case 'usu':
                        $elnivel = 'Usuario web';
                        break;
                        case 'otr':
                        $elnivel = 'Otros';
                        break;
                        }
                        echo $elnivel;
                        ?>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                      <div class="col-lg-2 col-md-2 text-right">Activo</div>
                      <div class="col-lg-4 col-md-4">
                        <?php if($data_usuario->activar === '1'){ echo 'Usuario Activo'; }else{ echo 'Usuario Inactivo'; } ?>
                      </div>
                    </div>
                </div>
                  </form>
                </div>
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