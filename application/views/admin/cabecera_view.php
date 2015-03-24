<body>

<div class="navbar navbar-fixed-top bs-docs-nav" role="banner">
  
    <div class="conjtainer">
      <!-- Menu button for smallar screens -->
      <div class="navbar-header">
		  <button class="navbar-toggle btn-navbar" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
			<span>Menu</span>
		  </button>
		  <!-- Site name for smallar screens -->
		  <a href="<?php echo base_url(); ?>" class="navbar-brand hidden-lg">ainvar.net</a>
		</div>
      
      

      <!-- Navigation starts -->
      <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">         


        <!-- Search form -->
        <form class="navbar-form navbar-left" role="search">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Search">
        <?php echo $this->session->userdata('id_usuario'); ?>
			</div>
		</form>
        <!-- Links -->
        <ul class="nav navbar-nav pull-right">
          <li class="dropdown pull-right">            
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
              <i class="fa fa-user"></i> Admin <b class="caret"></b>              
            </a>
            
            <!-- Dropdown menu -->
            <ul class="dropdown-menu">
              <li><a href="<?php echo base_url(); ?>usuarios/ver/<?php echo $this->session->userdata('id_usuario'); ?>"><i class="fa fa-user"></i> Perfil</a></li>
              <li><a href="<?php echo base_url(); ?>configura"><i class="fa fa-cogs"></i> Configuración</a></li>
              <li><a href="<?php echo base_url(); ?>modulos"><i class="fa fa-cubes"></i> Gestión Módulos</a></li>
              <li><a href="<?php echo base_url(); ?>login/logout"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
          </li>
          
        </ul>
      </nav>

    </div>
  </div>


<!-- Header starts -->
  <header>
    <div class="container">
      <div class="row">

        <!-- Logo section -->
        <div class="col-md-4">
          <!-- Logo. -->
          <div class="logo">
            <h1><a href="<?php echo base_url(); ?>"><?php echo $titulo; ?></a></h1>
            <p class="meta">PANEL DE CONTROL</p>
          </div>
          <!-- Logo ends -->
        </div>

        <!-- Button section -->
        

        <!-- Data section -->

        

      </div>
    </div>
  </header>

<!-- Header ends -->
