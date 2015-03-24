<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <!-- Title and other stuffs -->
  <title><?php echo $titulo; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="author" content="">

  <!-- Stylesheets -->
  <link href="<?php echo base_url(); ?>assets/admin/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/css/font-awesome.min.css">
  <link href="<?php echo base_url(); ?>assets/admin/css/style.css" rel="stylesheet">
  
  <script src="<?php echo base_url(); ?>assets/admin/js/respond.min.js"></script>
  <!--[if lt IE 9]>
  <script src="<?php echo base_url(); ?>assets/admin/js/html5shiv.js"></script>
  <![endif]-->

  <!-- Favicon -->
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/admin/img/favicon/favicon.png">
</head>

<body>

<!-- Form area -->
<div class="admin-form">
  <div class="container">

    <div class="row">
      <div class="col-md-12">
        <!-- Widget starts -->
            <div class="widget worange">
              <!-- Widget head -->
              <div class="widget-head">
                <i class="fa fa-lock"></i> Login 
              </div>

              <div class="widget-content">
                <div class="padd">
                  <!-- Login form -->
                  <form role="form" action="<?php echo base_url(); ?>login/new_user" id="formLogin" method="post" class="form-horizontal">
                    <!-- Email -->
                    <div class="form-group">
                      <?php echo $this->session->flashdata('usuario_incorrecto'); ?>
                      <label class="control-label col-lg-3" for="login">Usuario</label>
                      <div class="col-lg-9">
                        <input type="text" name="login" class="form-control" id="login" placeholder="Usuario">
                      </div>
                    </div>
                    <!-- Password -->
                    <div class="form-group">
                      <label class="control-label col-lg-3" for="password">Password</label>
                      <div class="col-lg-9">
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                      </div>
                    </div>
                    <!-- Remember me checkbox and sign in button -->
                    <div class="form-group">
					<div class="col-lg-9 col-lg-offset-3">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" name="recordar" value="SI"> Recordarme durante 15 días
                        </label>
						</div>
					</div>
					</div>
                        <div class="col-lg-9 col-lg-offset-3">
							<button type="submit" class="btn btn-info btn-sm pull-right">Entrar</button>
						</div>
                    <br />
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                  </form>
				  
				</div>
                </div>
              </div>  
      </div>
    </div>
  </div> 
</div>
	
		

<!-- JS -->
<script src="<?php echo base_url(); ?>assets/admin/js/jquery.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/js/bootstrap.min.js"></script>
</body>
</html>