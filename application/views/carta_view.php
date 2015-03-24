				<!--Carta
				=============================-->
				<div id="carta" class="item">
					<img src="<?php echo base_url(); ?>assets/img/img_menus/<?php echo $contcarta->menu_imagen; ?>" alt="Volcano Restaurant" class="fullBg">
					<div class="content">
						
						<div class="content_overlay"></div>
						<div class="content_inner">
							<div class="row contentscroll">
								<div class="container">
									<div class="col-md-6 empty">&nbsp;</div>
									<div class="col-md-6 content_text">
										<h1><?php echo $contcarta->titulo; ?></h1>
										<p class="pad_top13"><?php echo $contcarta->contenido; ?></p> <br />
										<div class="home3">
											
											<div class="col-md-12 pad_top20">
												<?php foreach($catsCarta as $categoria){ ?>
													<div class="toggle-container">
														<div class="toggle-header">
															<div class="toggle-link toggle-open"><?php echo $categoria->titulo; ?></div>
														</div>
														<div class="toggle-content">
															<?php if(count(${'itemscarta_'.$categoria->idcat})>0){ ?>
															<div class="desc">
																<?php echo $categoria->descripcion; ?>
															</div>
															<?php foreach(${'itemscarta_'.$categoria->idcat} as $item){ ?>
															<div class="pad_top10 clearfix">
																<div class="specials-content" >
																	<h4><?php echo $item->nombre; ?></h4>
																</div>
															</div>
															<?php } ?>
															<?php }else{ ?>
															<div class="pad_top10 clearfix">
															<div class="specials-content" >
																<h4>Lo siento pero no hay items en esta categoría.</h4>
																<p>(vuelva a comprobarlo más tarde)</p>
															</div>
															</div>
															
															<?php } ?>
														</div>
													</div><!--// .toggle-container end-->
													<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- // Carta
				=============================-->
