<!--Gallery
=============================-->
<div id="galeria" class="item">
    <div id="slides" class="clearfix">
        <div class="cycle-slideshow"
            data-cycle-fx=fade
            data-cycle-speed=1000
            data-cycle-timeout=3000
            data-cycle-caption-plugin=caption2
            data-cycle-overlay-fx-out="fadeOut"
            data-cycle-overlay-fx-in="fadeIn"
            data-cycle-prev=".prev"
            data-cycle-next=".next"
            >
            <div class="cycle-overlay"></div>
            <?php print_r($fotosGal); ?>
            <?php foreach($fotosGal as $foto){ ?>
            <img src="assets/img/galerias/<?php echo $foto->carpeta."/".$foto->archivo; ?>" data-cycle-desc="<?php echo $foto->descripcion; ?>"  class="fullBg" alt="<?php echo $foto->descripcion; ?>">
            <?php } ?>
        </div>
        <div id="galheading" class="clearfix"></div>
        <div id="button" class="clearfix">
            <div class="prev"><i class="fa fa-angle-left"></i></div>
            <div class="next"><i class="fa fa-angle-right"></i></div>
        </div>
    </div>
</div>
<!-- // Gallery Ends
=============================-->