<style>
.boton {
  padding:2px 7px !important;
}
</style>
  <div class="modal-header">
    <button type="button" class="close" onclick="window.parent.jQuery.lightbox().close();">&times;</button>
    <h4>&iquest;Est&aacute; seguro de eliminar a <strong><?php echo $data_usuario->nombre; ?></strong> de la lista de Usuarios?</h4>
  </div>
  <div class="modal-body">
  <?php $atributos = array('class'=>'form-horizontal','role'=>'form','name'=>'borraUsuario','id'=>'borraUsuario');
    echo form_open(base_url('usuarios/accion/borrar'),$atributos) ?>
      <button type="submit" name="borrar" class="btn btn-sm btn-danger" id="borrar"><i class="fa fa-warning"></i> Borrar</button>
      <button type="button" class="btn btn-sm btn-default" onclick="window.parent.jQuery.lightbox().close();" name="cancelar">Cancelar</button>
      <input type="hidden" name="id" value="<?php echo $data_usuario->id; ?>">
    <?php echo form_close(); ?>
  </div>