<!-- Main bar -->
<div class="mainbar">
  
  <!-- Page heading -->
  <div class="page-head">
    <h2 class="pull-left"><i class="fa fa-users"></i> Usuarios</h2>
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
              <div class="pull-left"><i class="fa fa-navicon"></i> Listado de Usuarios<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="<?php echo base_url(); ?>usuarios/nuevo"><i class="fa fa-plus"></i> Nuevo Usuario</a></span></div>
              <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
              <div class="clearfix"></div>
            </div>
            <div class="widget-content medias">
                <table id="usuarios" class="table table-striped table-bordered table-hover">
                  <thead>
                  <tr>
                    <th class="text-center">Foto</th>
                    <th class="text-center">Activo</th>
                    <th class="text-center">Nombre</td>
                    <th class="text-center">Teléfono</td>
                    <th class="text-center">Email</td>
                    <th class="text-center">Login</td>
                    <th class="text-center">Nivel</td>
                    <th class="text-center">Fecha de Alta</td>
                    <th class="text-center">Última Modificación</th>
                    <th class="text-center">Control</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($usuarios as $user) { 
                    $nivel = $user->nivel;
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
                    $activo = $user->activar;
                    switch ($activo) {
                      case '0':
                        $estado = 'Inactivo';
                        break;

                    case '1':
                        $estado = 'Activo';
                        break; 
                    }
                    $fecha = human_to_unix($user->fecha_alta);
                    $fechaU = human_to_unix($user->fecha_ultima_modificacion);
                  ?>
                  <tr>
                    <td><?php echo $user->foto; ?></td>
                    <td><?php echo $estado; ?></td>
                    <td><?php echo $user->nombre; ?></td>
                    <td><?php echo $user->telefono; ?></td>
                    <td><?php echo $user->email; ?></td>
                    <td><?php echo $user->login; ?></td>
                    <td><?php echo $elnivel; ?></td>
                    <td class="text-center"><?php echo date('d/m/Y \a \l\a\s h:i a',$fecha); ?></td>
                    <td class="text-center"><?php echo date('d/m/Y \a \l\a\s h:i a',$fechaU); ?></td>
                    <td class="text-center"><a class="ico" href="<?php echo base_url(); ?>usuarios/ver/<?php echo $user->id; ?>"><i class="fa fa-eye"></i></a> <a class="ico" href="<?php echo base_url(); ?>usuarios/editar/<?php echo $user->id; ?>"><i class="fa fa-edit"></i></a> <a class="lightboxBorrar ico" href="<?php echo base_url(); ?>usuarios/borrar/<?php echo $user->id; ?>?lightbox[width]=40p&amp;lightbox[height]=120"><i class="fa fa-trash"></i></a></td>
                  </tr>
                  <?php } ?>
                  </tbody>
                </table>
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