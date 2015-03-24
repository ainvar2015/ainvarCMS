/* JS */


/* Navigation */

$(document).ready(function() {

	$(window).resize(function() {
		if ($(window).width() >= 765) {
			$(".sidebar #nav").slideDown(350);
		} else {
			$(".sidebar #nav").slideUp(350);
		}
	});

	$(".has_sub > a").click(function(e) {
		e.preventDefault();
		var menu_li = $(this).parent("li");
		var menu_ul = $(this).next("ul");

		if (menu_li.hasClass("open")) {
			menu_ul.slideUp(350);
			menu_li.removeClass("open");
		} else {
			$("#nav > li > ul").slideUp(350);
			$("#nav > li").removeClass("open");
			menu_ul.slideDown(350);
			menu_li.addClass("open");
		}
	});
});

$(document).ready(function() {
	$(".sidebar-dropdown a").on('click', function(e) {
		e.preventDefault();

		if (!$(this).hasClass("open")) {
			// hide any open menus and remove all other classes
			$(".sidebar #nav").slideUp(350);
			$(".sidebar-dropdown a").removeClass("open");

			// open our new menu and add the open class
			$(".sidebar #nav").slideDown(350);
			$(this).addClass("open");
		} else if ($(this).hasClass("open")) {
			$(this).removeClass("open");
			$(".sidebar #nav").slideUp(350);
		}
	});

});

/* Widget close */

$('.wclose').click(function(e) {
	e.preventDefault();
	var $wbox = $(this).parent().parent().parent();
	$wbox.hide(100);
});

/* Widget minimize */

$('.wminimize').click(function(e) {
	e.preventDefault();
	var $wcontent = $(this).parent().parent().next('.widget-content');
	if ($wcontent.is(':visible')) {
		$(this).children('i').removeClass('fa fa-chevron-up');
		$(this).children('i').addClass('fa fa-chevron-down');
	} else {
		$(this).children('i').removeClass('fa fa-chevron-down');
		$(this).children('i').addClass('fa fa-chevron-up');
	}
	$wcontent.toggle(500);
});

/* Scroll to Top */

$(".totop").hide();

$(function() {
	$(window).scroll(function() {
		if ($(this).scrollTop() > 300) {
			$('.totop').fadeIn();
		} else {
			$('.totop').fadeOut();
		}
	});

	$('.totop a').click(function(e) {
		e.preventDefault();
		$('body,html').animate({
			scrollTop: 0
		}, 500);
	});

});

/* On Off pllugin */

$(document).ready(function() {
	$('.toggleBtn').onoff();
});

/* Tabs */
$(document).ready(function() {
  $( "ul#menuTabs li:first-child" ).addClass( "active" );
  $( "div.tab-content div:first-child" ).addClass( "active" );
});

/* Modal */

$(document).ready(function() {
	$(".lightbox").lightbox();

	$(".lightboxBorrar").lightbox({
		'onClose' : function() { location.reload(); } 
	});
});

/*function reset_html(id) {
    $('#'+id).html($('#'+id).html());
}

$(document).ready(function() {

    var file_input_index = 0;
    $('input[type=file]').each(function() {
        file_input_index++;
        $(this).wrap('<span id="file_input_container_'+file_input_index+'"></span>');
        $(this).after('<input type="button" value="Limpiar Imagen" onclick="reset_html(\'file_input_container_'+file_input_index+'\')" />');
    });
   
});*/
	// Referneces
var clearBn = $("#clear");
	// Setup the clear functionality
	clearBn.on("click", function(){
	    var control = $("#menu_imagen");
	    var span = $(".file-input-name");
	    control.replaceWith( control.val(''));
	    span.text('');
	});

//MOSTRAR CAMPO PLANTILLA
	$("#tipoplantilla").change(function(){
		valor = $('#tipoplantilla').val();
		if(valor != 0){
			if(valor == 1){
				$('.c_plantilla').slideUp('slow');
				$('.c_plantilla').empty();
				$('.c_plantilla').append('<label for="plantilla">PLANTILLA</label><input type="text" name="plantilla" id="plantilla" class="form-control">');
			}else if(valor == 2){
				$('.c_plantilla').slideUp('slow');
				$('.c_plantilla').empty();
				$('.c_plantilla').append('<label for="plantilla">ENLACE EXTERNO</label><input type="text" name="plantilla" id="plantilla" class="form-control">');
			}else if(valor == 3){
				$('.c_plantilla').slideUp('slow');
				$('.c_plantilla').empty();
				$('.c_plantilla').load('http://localhost/ainvarCMS1.6/menu/listamodulos');
			}
			$('.c_plantilla').slideDown('slow');
		}else{
			$('.c_plantilla').slideUp('slow');
		}
	});

$(document).ready(function($) {
//FILE INPUT
$('input[type=file]').bootstrapFileInput();

//ICHECK
$('input').iCheck({
	checkboxClass: 'icheckbox_minimal-blue',
	radioClass: 'iradio_minimal-blue',
	increaseArea: '20%' // optional
});

$('input#checktodos').on('ifChecked ifUnchecked', function(event){
		estado = event.type.replace('if', '').toLowerCase();
		$('input[type=checkbox]').each( function() {		
			if(estado == 'checked'){
				$('input').iCheck('check');
				$(".lavel").html("Deseleccionar todos");
			} else {
				$('input').iCheck('uncheck');
				$(".lavel").html("Seleccionar todos");
			}
		});
	});
});

