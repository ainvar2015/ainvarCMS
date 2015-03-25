
<!-- Footer starts -->
<footer>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
            <!-- Copyright info -->
            <p class="copy">Copyright &copy; 2014 | <a href="#">ainvar.net</a> </p>
      </div>
    </div>
  </div>
</footer> 	

<!-- Footer ends -->

<!-- Scroll to top -->
<span class="totop"><a href="#"><i class="fa fa-chevron-up"></i></a></span> 

<!-- JS -->
<script src="<?php echo base_url(); ?>assets/admin/js/jquery.js"></script> <!-- jQuery -->
<script src="//getbootstrap.com/2.3.2/assets/js/google-code-prettify/prettify.js"></script> <!-- Prettify -->
<script src="<?php echo base_url(); ?>assets/admin/js/bootstrap.min.js"></script> <!-- Bootstrap -->
<script src="<?php echo base_url(); ?>assets/admin/js/jquery-ui.min.js"></script> <!-- jQuery UI -->
<script src="<?php echo base_url(); ?>assets/admin/js/jquery.lightbox.min.js"></script> <!-- jQuery Lightbox Evolution -->
<script src="<?php echo base_url() ?>assets/admin/js/language/es-ES.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/underscore-min.js"></script>
<script src="<?php echo base_url() ?>assets/admin/js/calendar.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/js/bootstrap.file-input.js"></script><!-- Bootstrap File Input -->
<script src="<?php echo base_url(); ?>assets/admin/js/icheck.min.js"></script> <!-- Icheck -->

<script src="<?php echo base_url(); ?>assets/admin/js/sparklines.js"></script> <!-- Sparklines -->
<script src="<?php echo base_url(); ?>assets/admin/js/jquery.onoff.min.js"></script> <!-- Bootstrap Toggle -->
<script src="<?php echo base_url(); ?>assets/admin/js/filter.js"></script> <!-- Filter for support page -->
<script src="<?php echo base_url(); ?>assets/admin/js/custom.js"></script> <!-- Custom codes -->
<script type="text/javascript" src="<?php echo base_url(); ?>tinymce/js/tinymce/tinymce.min.js"></script>

<script>
tinymce.init({
      selector: "textarea",
      plugins: [
          "advlist autolink lists link image charmap print preview anchor",
          "searchreplace visualblocks code fullscreen",
          "insertdatetime code media table contextmenu paste responsivefilemanager"
      ],
      image_advtab: true,
      toolbar: "undo redo | styleselect | bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image responsivefilemanager | code",
      menubar: false,
      resize:false,
      height: 350,
      language:'es',
      external_filemanager_path:"/Volcano/filemanager/",
      filemanager_title:"Responsive Filemanager" ,
      external_plugins: { "filemanager" : "/Volcano/filemanager/plugin.min.js"}
  });
//ORDENAR MENÚS
$(document).ready(function($) {
  $("#menus").sortable({
      update : function () {
      var order = $('#menus').sortable('serialize');
        $("#info").load("<?php echo base_url(); ?>menu/actualizaOrden?"+order+"&tabla=menu");
      }
  });
  $("#imagenes").sortable({
      update : function () {
      var order = $('#imagenes').sortable('serialize');
      $("#info").load("<?php echo base_url(); ?>galerias/actualizaOrden?"+order+"&tabla=fotosgaleria");
      }
  });
  $("#cats").sortable({
      update : function () {
      var order = $('#cats').sortable('serialize');
        $("#info").load("<?php echo base_url(); ?>carta/actualizaOrden?"+order+"&tabla=categoriascarta");
      }
  });
  $("#catsB").sortable({
      update : function () {
      var order = $('#catsB').sortable('serialize');
        $("#info").load("<?php echo base_url(); ?>carta/actualizaOrden?"+order+"&tabla=categoriasbodega");
      }
  });
  <?php if(isset($cats)){
  foreach($cats as $catid){ ?>
  $("#orden_<?php echo $catid->id; ?>").sortable({
    update : function () {
      var order = $('#platos_<?php echo $catid->id; ?>').sortable('serialize');
      $("#info").load("<?php echo base_url(); ?>carta/actualizaOrden?"+order+"&tabla=carta");
    }
  });
  <?php } 
}?>
});
</script>
</body>
</html>