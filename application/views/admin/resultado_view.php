<!-- Main bar -->

<div class="mainbar"> 
  
  <!-- Page heading -->
  <div class="page-head">
    <h2 class="pull-left"><?php echo $titulores; ?></h2>
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
              <?php echo $linksHead; ?>
              <div class="widget-icons pull-right"> <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> <a href="#" class="wclose"><i class="fa fa-times"></i></a> </div>
              <div class="clearfix"></div>
            </div>
            <div class="widget-content">
              <div class="padd"> <br />
                <?php echo validation_errors(); ?>
                <div id="result2"></div>
                <div class="box-info form">
                    <?php echo $guardado; ?>
                      <?php 
                      if(isset($img_upload)){
                    	  foreach($img_upload as $imagen){
                    		  echo $imagen;
                    	  }
                      }
                      ?>
                    </p>
                      <?php 
                      if(isset($error)){
                    	  foreach($error as $fail){
                    		  echo '<p>Error: '.$fail.'</p>';
                    	  }
                      }
                      ?>
                  </div>
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
