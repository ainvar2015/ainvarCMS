			<h2><?php echo $datosevento->title; ?></h2>
			<p>Contenido: <?php echo nl2br($datosevento->body); ?></p>
			<p>Desde: <?php echo date('d/m/Y',$datosevento->start/1000).' a las '.date('H:i',$datosevento->start/1000); ?></p>
			<p>Hasta: <?php echo date('d/m/Y',$datosevento->end/1000).' a las '.date('H:i',$datosevento->end/1000); ?></p>
		