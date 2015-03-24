<!-- Main bar -->
<div class="mainbar">
  
  <!-- Page heading -->
  <div class="page-head">
    <h2 class="pull-left"><i class="fa fa-trash"></i> Borrado de Menú</h2>
    
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
              <div class="pull-left">Resultado del Borrado<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="<?php echo base_url(); ?>menu"><i class="fa fa-angle-double-left"></i> Volver a Menús</a></span>
              <span class="pull-right ml15"><a class="btn btn-xs btn-default" href="<?php echo base_url(); ?>menu/nuevo"><i class="fa fa-plus"></i> Añadir Nuevo Menú</a></span></div>
              <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
              <div class="clearfix"></div>
            </div>
            <div class="widget-content">
              <div class="padd"> <br />
                <?php echo validation_errors(); ?>
                <div id="result2"></div>
                <div class="alert alert-success">
                  <p><i class="fa fa-info-circle bg"></i> <?php echo $guardado; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="clearfix"></div>
</div>