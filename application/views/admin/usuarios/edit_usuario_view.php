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
                <div class="padd"> <br />
                  <!-- Form starts.  -->
                  <?php $atributos = array('class'=>'form-horizontal','role'=>'form','name'=>'formEditUser','id'=>'formEditUser');
                  echo form_open(base_url('usuarios/accion/editar'),$atributos) ?>
                  <div class="form-group">
                    <label for="login" class="col-lg-2 col-md-2 control-label text-right">Login Usuario</label>
                    <div class="col-lg-4 col-md-4">
                      <input type="text" name="login" id="login" class="form-control" placeholder="Login" value="<?php echo $data_usuario->login; ?>" tabindex="1">
                      <input type="hidden" name="login_old" value="<?php echo $data_usuario->login; ?>">
                      <?php echo form_error('login'); ?>
                    </div>
                    <label for="nombre" class="col-lg-2 col-md-2 control-label text-right">Nombre Usuario</label>
                    <div class="col-lg-4 col-md-4">
                      <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre del Usuario" value="<?php echo $data_usuario->nombre; ?>" tabindex="2">
                      <?php echo form_error('nombre'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="email" class="col-lg-2 col-md-2 control-label text-right">Email</label>
                    <div class="col-lg-4 col-md-4">
                      <input type="text" name="email" id="email" class="form-control" placeholder="Email del Usuario" value="<?php echo $data_usuario->email; ?>" tabindex="11">
                      <input type="hidden" name="email_old" value="<?php echo $data_usuario->email; ?>">
                      <?php echo form_error('email'); ?>
                    </div>
                    <label for="telefono" class="col-lg-2 col-md-2 control-label text-right">Teléfono</label>
                    <div class="col-lg-4 col-md-4">
                      <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Teléfono del Usuario" value="<?php echo $data_usuario->telefono; ?>" tabindex="3">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="nivel" class="col-lg-2 col-md-2 control-label text-right">Nivel de Acceso</label>
                    <div class="col-lg-4 col-md-4">
                    <?php if($this->session->userdata('nivel') === 'adm'){ ?>
                      <select name="nivel" id="nivel" class="form-control" tabindex="15">
                        <option value="adm"<?php if($data_usuario->nivel == 'adm'){ echo ' selected'; } ?>>Administrador</option>
                        <option value="usu"<?php if($data_usuario->nivel == 'usu'){ echo ' selected'; } ?>>Usuario Web</option>
                        <option value="otr"<?php if($data_usuario->nivel == 'otr'){ echo ' selected'; } ?>>Otros</option>
                      </select>
                    <?php }else{ 
                    $nivel = $this->session->userdata('nivel');
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
                    }
                    ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="activo" class="col-lg-2 col-md-2 control-label text-right"><br />Activo</label>
                    <div class="col-lg-4 col-md-4">
                      <div class="checbox">
                        <br /><input type="checkbox" name="activar" id="activar" value="1" <?php if($data_usuario->activar == 1){ ?> checked="checked" <?php } ?>>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-12 col-md-4">
                      <button type="submit" class="btn-lg btn-block btn-primary" tabindex="19"><i class="fa fa-user"></i> <i class="fa fa-edit"></i> Modificar Usuario</button>
                    </div>
                  </div>
                  <input type="hidden" name="id" value="<?php echo $data_usuario->id; ?>">
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