  	<?php 
    $clasadmin = '';
    $clasmenu = '';
    $clasusr = '';
    $clasmod = '';
    $classgal = '';
    $classcarta = '';
    $classbodega = '';
    $classidiomas = '';
    $contrl = $this->uri->segment(1);
    switch ($contrl) {
      case 'admin':
        $clasadmin = ' class="open"';
        break;
      
      case 'menu':
        $clasmenu = ' class="open"';
        break;

      case 'modulos':
        $clasmod = 'open';
        break;
      case 'galerias':
        $clasmod = 'open';
        $classgal = ' class="open"';
        break;
      case 'carta':
        $clasmod = 'open';
        $classcarta = ' class="open"';
        break;
      case 'bodega':
        $clasmod = 'open';
        $classbodega = ' class="open"';
        break;
      case 'usuarios':
        $clasusr = ' class="open"';
        break;
      case 'idiomas':
        $classidiomas = ' class="open"';
        break;

      default:
        $clsadm = ' class="open"';
        break;
    }
    ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-dropdown"><a href="#">Navigation</a></div>

        <!--- Sidebar navigation -->
        <!-- If the main navigation has sub navigation, then add the class "has_sub" to "li" of main navigation. -->
        <ul id="nav">
          <!-- Main menu with font awesome icon -->
          <li <?php echo $clasadmin; ?>><a href="<?php echo base_url(); ?>admin"><i class="fa fa-home"></i> Inicio</a></li>
          <li <?php echo $clasmenu; ?>><a href="<?php echo base_url(); ?>menu"><i class="fa fa-navicon"></i> Menús</a></li>  
          <?php if($modulos){ ?>
          <li class="has_sub <?php echo $clasmod; ?>"><a href="#"><i class="fa fa-cubes"></i> Modulos</a>
            <ul>
            <?php foreach($modulos as $modulo){ ?>
              <li<?php echo $classgal; ?>><a href="<?php echo base_url().$modulo->valor; ?>/listar"><i class="fa fa-picture-o"></i> <?php echo $modulo->nombre; ?></a></li>
            <?php } ?>
            </ul>
          </li>  
          <?php } ?>
          <li<?php echo $clasusr; ?>><a href="<?php echo base_url(); ?>usuarios"><i class="fa fa-users"></i> Usuarios</a></li> 
		      <li><a href="<?php echo base_url(); ?>login/logout"><i class="fa fa-sign-out"></i> LogOut</a></li> 
        </ul>
    </div>

    <!-- Sidebar ends -->
