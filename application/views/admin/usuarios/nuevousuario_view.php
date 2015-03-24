<!-- Main bar -->
<div class="mainbar">
  
  <!-- Page heading -->
  <div class="page-head">
    <h2 class="pull-left"><i class="fa fa-table"></i> Nuevo Usuario</h2>
    
    <!-- Breadcrumb -->
    <div class="bread-crumb pull-right"> <a href="index.html"><i class="fa fa-home"></i> Inicio</a>
    <!-- Divider -->
    <span class="divider">/</span> <a href="#" class="bread-current">Nuevo Cliente</a> </div>
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
              <div class="pull-left">Formulario de Alta Usuario<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="<?php echo base_url(); ?>usuarios"><i class="fa fa-angle-double-left"></i> Volver a Usuarios</a></span></div>
              <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
              <div class="clearfix"></div>
            </div>
            <div class="widget-content">
              <div class="padd"> <br />
                <!-- Form starts.  -->
                <?php $atributos = array('class'=>'form-horizontal','role'=>'form','name'=>'formAltaUser','id'=>'formAltaUser');
                echo form_open(base_url('usuarios/accion/alta'),$atributos) ?>
                  <div class="form-group">
                    <label for="login" class="col-lg-2 col-md-2 control-label text-right">Login Usuario</label>
                    <div class="col-lg-4 col-md-4">
                      <input type="text" required name="login" id="login" class="form-control" placeholder="Login" tabindex="1">
                      <?php echo form_error('login'); ?>
                    </div>
                    <label for="password" class="col-lg-2 col-md-2 control-label text-right">Contraseña</label>
                    <div class="col-lg-4 col-md-4">
                      <input type="password" required name="password" id="password" class="form-control" placeholder="Contraseña" tabindex="4">
                      <?php echo form_error('password'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="nombre" class="col-lg-2 col-md-2 control-label text-right">Nombre Usuario</label>
                    <div class="col-lg-4 col-md-4">
                      <input type="text" required name="nombre" id="nombre" class="form-control" placeholder="Nombre del Usuario" tabindex="2">
                      <?php echo form_error('nombre'); ?>
                    </div>
                    <label for="password1" class="col-lg-2 col-md-2 control-label text-right">Confirme</label>
                    <div class="col-lg-4 col-md-4">
                      <input type="password" required name="password1" id="password1" class="form-control" placeholder="Confirme la Contraseña" tabindex="5">
                      <?php echo form_error('password1'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="telefono" class="col-lg-2 col-md-2 control-label text-right">Teléfono</label>
                    <div class="col-lg-4 col-md-4">
                      <input type="tel" name="telefono" id="telefono" pattern="[0-9]{9}" class="form-control" placeholder="Teléfono del Usuario Formato: 999999999" tabindex="3">
                    </div>
                    <label for="email" class="col-lg-2 col-md-2 control-label text-right">Email</label>
                    <div class="col-lg-4 col-md-4">
                      <input type="email" required name="email" id="email" class="form-control" placeholder="Email del Usuario. Formato: direccion@server.com" tabindex="6">
                      <?php echo form_error('email'); ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="activo" class="col-lg-2 col-md-2 control-label text-right"><br />Activo</label>
                    <div class="col-lg-4 col-md-4">
                      <div class="checbox">
                        <br /><input type="checkbox" name="activar" id="activar" value="1" checked="checked" tabindex="7">
                      </div>
                    </div>
                    <label for="nivel" class="col-lg-2 col-md-2 control-label text-right">Nivel de Acceso</label>
                    <div class="col-lg-4 col-md-4">
                      <select name="nivel" id="nivel" class="form-control" tabindex="8">
                        <option value="adm">Administrador</option>
                        <option value="usu">Usuario Web</option>
                        <option value="otr">Otros</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-12 col-md-4">
                      <button type="submit" class="btn-lg btn-block btn-primary" tabindex="9"><i class="fa fa-user"></i> <i class="fa fa-plus"></i> Dar de Alta Nuevo Usuario</button>
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
</div>
<!-- Matter ends -->
</div>
<!-- Mainbar ends -->
<div class="clearfix"></div>
</div>
<!-- Content ends -->