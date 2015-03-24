<!-- Main bar -->
<div class="mainbar">
    
    <!-- Page heading -->
    <div class="page-head">
        <h2 class="pull-left"><i class="fa fa-calendar"></i> Añadir Eventos</h2>
        
        <!-- Breadcrumb -->
        <div class="bread-crumb pull-right"> <a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Inicio</a> </div>
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
                            <div class="pull-left">Formulario de Alta Evento</div>
                            <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="widget-content">
                            <div class="padd">
                                <?php $atributos = array('class'=>'form-horizontal','role'=>'form');
                                echo form_open(base_url('events/save'),$atributos) ?>
                                <div class="form-group">
                                    <div class='col-md-6'>
                                        <label for="from" class="control-label text-left">Fecha de Inicio</label>
                                        <div class='input-group date' id='from'>
                                            <input type='text' name="from" class="form-control" readonly />
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </div>
                                    <div class='col-md-6'>
                                        <label for="to" class="control-label text-left">Fecha de Fin</label>
                                        <div class='input-group date' id='to'>
                                            <input type='text' name="to" class="form-control" readonly />
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span> </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">
                                    <label class="control-label text-left">Tipo de evento</label>
                                        <select class="form-control" name="class">
                                            <option value="event-info">Información</option>
                                            <option value="event-success">Trabajo</option>
                                            <option value="event-inverse">Inverso</option>
                                            <option value="event-important">Importante</option>
                                            <option value="event-warning">Atención</option>
                                            <option value="event-special">Especial</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                    <label for="title" class="col-sm-12 control-label text-left">Título</label>
                                        <input type="text" name="title" class="form-control" id="title" placeholder="Introduce un título">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                    <label for="body" class="control-label text-left">Evento</label>
                                        <textarea id="body" name="event" class="form-control" rows="4"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn-lg btn-block btn-primary" tabindex="19"><i class="fa fa-bell"></i> Dar de Alta Nuevo Evento</button>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close() ?> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="clearfix"></div>
<script type = "text/javascript">
$(function() {
$('#from').datetimepicker({
defaultDate: new Date(),
language: 'es',
minDate: new Date()
});
$('#to').datetimepicker({
defaultDate: new Date(),
language: 'es',
minDate: new Date()
});
});
</script>