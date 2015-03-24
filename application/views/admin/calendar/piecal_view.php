<!-- Footer starts -->
<footer>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<!-- Copyright info -->
				<p class="copy">Copyright &copy; 2014 | <a href="<?php echo base_url(); ?>">ainvar.net</a> </p>
			</div>
		</div>
	</div>
</footer>
<!-- Footer ends -->

<!-- Scroll to top -->
<span class="totop"><a href="#"><i class="fa fa-chevron-up"></i></a></span>
<script src="<?php echo base_url() ?>assets/admin/bower_components/underscore/underscore-min.js"></script>
<script src="<?php echo base_url() ?>assets/admin/bower_components/bootstrap-calendar/js/calendar.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/js/custom.js"></script> <!-- Custom codes -->
<script type="text/javascript">
(function($) {
	//creamos la fecha actual
	var date = new Date();
	var yyyy = date.getFullYear().toString();
	var mm = (date.getMonth() + 1).toString().length == 1 ? "0" + (date.getMonth() + 1).toString() : (date.getMonth() + 1).toString();
	var dd = date.getDate().toString().length == 1 ? "0" + date.getDate().toString() : date.getDate().toString();
	//establecemos los valores del calendario
	var options = {
		events_source: '<?php echo base_url() ?>events/getAll',
		view: 'month',
		language: 'es-ES',
		tmpl_path: '<?php echo base_url() ?>assets/admin/bower_components/bootstrap-calendar/tmpls/',
		tmpl_cache: false,
		day: yyyy + "-" + mm + "-" + dd,
		time_start: '10:00',
		time_end: '20:00',
		time_split: '30',
		width: '90%',
		modal: '#events-modal',
		modal_type: 'ajax',
		onAfterEventsLoad: function(events) {
			if (!events) {
				return;
			}
			var list = $('#eventlist');
			list.html('');
			$.each(events, function(key, val) {
				$(document.createElement('li'))
					.html('<a href="' + val.url + '">' + val.title + '</a>')
					.appendTo(list);
			});
		},
		onAfterViewLoad: function(view) {
			$('.page-header h3').text(this.getTitle());
			$('.btn-group button').removeClass('active');
			$('button[data-calendar-view="' + view + '"]').addClass('active');
		},
		classes: {
			months: {
				general: 'label'
			}
		}
	};
	var calendar = $('#calendar').calendar(options);
	$('.btn-group button[data-calendar-nav]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.navigate($this.data('calendar-nav'));
		});
	});
	$('.btn-group button[data-calendar-view]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.view($this.data('calendar-view'));
		});
	});
	$('#first_day').change(function() {
		var value = $(this).val();
		value = value.length ? parseInt(value) : null;
		calendar.setOptions({
			first_day: value
		});
		calendar.view();
	});
	
}(jQuery));

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
</script>