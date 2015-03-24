<style type="text/css">
    .page-header{
        margin:10px 0 10px;
    }
</style>
<!-- Main bar -->
<div class="mainbar">
  <!-- Page heading -->
  <div class="page-head">
    <!-- Page heading -->
    <h2 class="pull-left"><i class="fa fa-home"></i> Inicio</h2>
    
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
              <div class="pull-left">Calendario de Eventos</div>
              <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
              <div class="clearfix"></div>
            </div>
            <div class="widget-content">
              <div class="padd">
                <div class="row" style="width:96%;margin:0 auto;">
                  <div class="page-header">
                    <div class="pull-right form-inline">
                      <div class="btn-group">
                        <a class="btn btn-default" href="<?php echo base_url() ?>events"><i class="fa fa-plus"></i> Añadir evento</a>
                      </div>
                      <div class="btn-group">
                        <button class="btn btn-primary" data-calendar-nav="prev"><< Anterior</button>
                        <button class="btn" data-calendar-nav="today">Hoy</button>
                        <button class="btn btn-primary" data-calendar-nav="next">Siguiente >></button>
                      </div>
                      <div class="btn-group">
                        <button class="btn btn-default" data-calendar-view="year">Año</button>
                        <button class="btn btn-default active" data-calendar-view="month">Mes</button>
                        <button class="btn btn-default" data-calendar-view="week">Semana</button>
                        <button class="btn btn-default" data-calendar-view="day">Día</button>
                      </div>
                    </div>
                    <h3></h3>
                  </div>
                </div><hr />
                <div class="row">
                  <div id="calendar" style="margin:0 auto;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="events-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>Información del Evento</h3>
            </div>
            <div class="modal-body" style="height: 400px">
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" class="btn btn-default">Cerrar</a>
            </div>
        </div>
    </div>
</div>