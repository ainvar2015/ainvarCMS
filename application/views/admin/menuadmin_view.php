<!-- Main bar -->
<div class="mainbar">
  <!-- Page heading -->
  <div class="page-head">
    <h2 class="pull-left"><i class="fa fa-navicon"></i> Menús</h2>
    <div class="clearfix"></div>
  </div>
  <!-- Page heading ends -->

  <!-- Matter -->
  <div class="matter">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="widget">
            <div class="widget-head">
              <div class="pull-left"><i class="fa fa-navicon"></i> Listado de Menús<span class="pull-right ml15"><a class="btn btn-xs btn-default" href="<?php echo base_url(); ?>menu/nuevo"><i class="fa fa-plus"></i> Nuevo Menú</a></span></div>
              <div class="widget-icons pull-right">
                <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
                <a href="#" class="wclose"><i class="fa fa-times"></i></a>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="widget-content">
              <div class="padd">
                
                <!-- Table Page -->
                <div class="page-tables">
                  <!-- Table -->
                  <div class="table-responsive">
                <div id="menuinf"> <?php echo pinta_menu(0,'0','sup'); ?> </div>
                    <div class="clearfix"></div>
                  </div>
                </div>
              </div>
              
            </div>
            <div class="widget-foot">
              <!-- Footer goes here -->
              <div id="info"></div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>
<!-- Mainbar ends -->
<div class="clearfix"></div>
<!-- Content ends -->