<!--About us
=============================-->
<div id="contacto" class="item" style="background-color:#999999;">
	<img src="<?php echo base_url(); ?>assets/img/img_menus/<?php echo $contacto->menu_imagen; ?>" class="fullBg">
	<div class="content">
		<div class="content_overlay"></div>
		<div class="content_inner">
			<div class="row contentscroll">
				<div class="container">
					<div class="col-md-6 empty">&nbsp;</div>
					<div class="col-md-6 content_text">
						<h1><?php echo $contacto->menu; ?></h1>
						<div class="pad_top13">
							<div class="col-md-10">
								<?php echo $contacto->contenido; ?>
							</div>
							<div class="col-md-10" id="formContacto">
								<form action="<?php echo base_url(); ?>contacto/enviar" method="post" name="formContact" id="formContact" class="reserve_form pad_top13">
									<h4>Formulario de contacto</h4>
									<div class="clearfix contact_form">
										<input type="text" name="nombre" class="textbox1" required placeholder="* Nombre : " onfocus="this.placeholder = ''" onBlur="this.placeholder = '* Nombre :'" />
										<input type="email" name="email"  class="textbox1" required placeholder="* Email : " onFocus="this.placeholder = ''" onBlur="this.placeholder = '* Email :'" />
										<input type="text" name="telefono" class="textbox1" required placeholder="* Teléfono : " onFocus="this.placeholder = ''" onBlur="this.placeholder = '* Teléfono :'" />
										<textarea name="mensaje" class="messagebox1" required placeholder="* Mensaje : " onFocus="this.placeholder = ''" onBlur="this.placeholder = '* Mensaje :'"></textarea>
										<input id="submitBtn" value="contactar" name="Confirm" type="submit" class="submitBtn">
									</div>
								</form>
							</div>
							<div class="header_icons accura-header-block accura-hidden-2xs">
								<ul class="accura-social-icons accura-stacked accura-jump accura-full-height accura-bordered accura-small accura-colored-bg clearFix">
									<li id="1"><a href="https://www.facebook.com/volcanorest" target="_blank" title="facebook" class="accura-social-icon-facebook circle"><i class="fa fa-facebook" title="facebook"></i></a></li>
									<li id="2"><a href="https://twitter.com/volcanorest" target="_blank" title="twitter" class="accura-social-icon-twitter circle"><i class="fa fa-twitter" title="twitter"></i></a></li>
									<li id="3"><a href="https://instagram.com/volcanorest" target="_blank" title="instagram" class="accura-social-icon-instagram circle"><i class="fa fa-instagram" title="instagram"></i></a></li>
									<li id="4"><a href="https://plus.google.com/117124654740540745684/" target="_blank" title="google+" class="accura-social-icon-gplus circle"><i class="fa fa-plus" title="google+"></i></a></li>
									<li id="5"><a href="https://es.foursquare.com/v/volcano-restaurant/54f46ad8498ee0f30b1c9396" title="foursquare" target="_blank" class="accura-social-icon-foursquare circle" title="foursquare"><i class="fa fa-foursquare"></i></a></li>
									<li id="6"><a href="https://www.youtube.com/channel/UCiiXMzttqshyFH6MzXYDV2w" target="_blank" title="youtube" class="accura-social-icon-youtube circle"><i class="fa fa-youtube" title="youtube"></i></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- // About us
	=============================-->