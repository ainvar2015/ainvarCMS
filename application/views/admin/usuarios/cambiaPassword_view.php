<style>
.boton {
  padding:2px 7px !important;
}
</style>
  <div class="modal-header">
    <button type="button" class="close" onclick="window.parent.jQuery.lightbox().close();">&times;</button>
    <h4>Formulario de Cambio de Contraseña</h4>
  </div>
  <div class="modal-body">
  <?php $atributos = array('class'=>'form-horizontal','role'=>'form','name'=>'borraUsuario','id'=>'borraUsuario','autocomplete'=>'off');
  $id = $this->uri->segment(3);
  $oculto = array('id' => $id );
    echo form_open(base_url("usuarios/accion/password/"),$atributos, $oculto) ?>
    <div class="form-group">
      <label for="passwordold" class="col-lg-2 col-md-2 control-label text-right"> Contraseña Actual</label>
      <div class="col-lg-6 cooldl-md-6">
      <input type="password" required class="form-control" name="passwordold" id="passwordold" value="" placeholder="Escriba la contraseña actual">
      </div>
    </div>
    <div class="form-group">
      <label for="password" class="col-lg-2 col-md-2 control-label text-right"> Nueva Contraseña</label>
      <div class="col-lg-6 col-md-6">
      <input type="password" required class="form-control" name="password" id="password" value="" placeholder="Escriba la nueva contraseña">
      </div>
    </div>
    <div class="form-group">
      <label for="password1" class="col-lg-2 col-md-2 control-label text-right"> Confirme Contraseña</label>
      <div class="col-lg-6 col-md-6">
      <input type="password" required class="form-control" name="password1" id="password1" value="" placeholder="Confirme la nueva contraseña">
      </div>
    </div>
    <hr />
    <button type="submit" name="cambiaPwd" class="btn btn-sm btn-primary" id="borrar"><i class="fa fa-lock"></i> Actualizar Contraseña</button>
    <?php echo form_close(); ?>
  </div>