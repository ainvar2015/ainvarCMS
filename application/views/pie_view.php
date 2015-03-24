			</div>
		</div>
		<!-- // Wrapper =============================-->
		<!--java script-->
		<!-- Preloader Starts -->
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jpreloader.min.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.scrollTo.min.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/bootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.fitvids.js"></script>
		<!-- <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>-->
		<!-- Gallery -->
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.prettyPhoto.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.isotope.min.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.mixitup.min.js"></script>
		<!-- Vegas sliders -->
		<script src="<?echo base_url(); ?>assets/js/jquery.vegas.min.js"></script>
		<!-- Cycle Slider Sldier -->
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.cycle.all.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.cycle2.caption2.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.slicknav.min.js"></script>
		<script src="<?echo base_url(); ?>assets/js/jquery.nicescroll.min.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.mousewheel.min.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.easing.1.3.js"></script>
		<!-- include retina js -->
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/retina-1.1.0.min.js"></script>
		<!-- History.js -->
		<!--[if (gte IE 10) | (!IE)]><!-->
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/jquery.history.js"></script>
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/ajaxify-html5.js"></script>
		<!--<![endif]-->
		<script type="text/javascript" src="<?echo base_url(); ?>assets/js/custom_general.js"></script>
		<script>
			$(document).ready(function(){
			"use strict";	
			/* custom function VEGAS Home Slider */
				function loadVegas(){
					$.vegas('slideshow', {
						  backgrounds:[
							<?php echo $imagenes; ?>
						  ]
						})('overlay', {
						  src:'<?php echo base_url(); ?>assets/img/pat/02.png'
						});
						
						$( "#vegas-next" ).click(function() {
						  $.vegas('next');
						});
						$( "#vegas-prev" ).click(function() {
						  $.vegas('previous');
					});
				}
				
				loadVegas();
				
				
				/* Home sliding_title  */



			$(document).ready(function(){
				"use strict";
			/* function Slider  Title animated :Auto  */
				function loadSlidingTitleAnimated(){
					var myInterval;
					var counter = 1;
					var myFunc = function() {
						var cur = $('.main-title ul li').length;
						if(cur == counter) {; //if conditon for resetting counter
								$('.main-title ul li.sliding_title').removeClass('sliding_title');
								$('.main-title ul li').first().addClass('sliding_title');
								counter = 1;
							} else {
								counter++;
								$('.main-title ul li.sliding_title').removeClass('sliding_title').next().addClass('sliding_title');
							}
					};
					myInterval = setInterval(myFunc, 5000); // Set Animation Time Intervals in Miliseconds
				}
				
				loadSlidingTitleAnimated();
			});
			//MOSTRAR CAMPO PLANTILLA
			$("#tipoplantilla").change(function(){
				valor = $('#tipoplantilla').val();
				if(valor != 0){
					if(valor == 1){
						$('.c_plantilla').slideUp('slow');
						$('span.miplantilla').empty();
						$('.c_plantilla').append('<span class="miplantilla"><label for="plantilla">PLANTILLA</label><input type="text" name="plantilla" id="plantilla" class="form-control"></span>');
					}else if(valor == 2){
						$('.c_plantilla').slideUp('fast');
						$('span.miplantilla').empty();
						$('.c_plantilla').append('<span class="miplantilla"><label for="plantilla">ENLACE EXTERNO</label><input type="text" name="plantilla" id="plantilla" class="form-control"></span>');
					}else if(valor == 3){
						$('.c_plantilla').slideUp('fast');
						$('span.miplantilla').empty();
						$('.c_plantilla').append('<span class="miplantilla"><label for="plantilla">MÓDULO</label><select name="subid" class="form-control">' + <?php echo base_url().menu/listamodulos; ?> + '</select></span>');
					}
					$('.c_plantilla').slideDown('slow');
				}else{
					$('.c_plantilla').slideUp('slow');
				}
			});
		</script>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-59535836-1', 'auto');
			ga('send', 'pageview');

		</script>
	</body>
</html>