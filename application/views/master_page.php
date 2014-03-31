<!DOCTYPE html>
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="Fundación Columbia de conciencia y energía" />

		<meta property="og:title" content="Fundación Columbia <?= $title ? "| $title" : '' ?>"/>
		<meta property="og:url" content="<?= current_url() ?>"/>
		<meta property="og:description" content="<?= $og_description ?>"/>
		<meta property="og:image" content="<?= $og_image?>" />
		
		<title>Fundación Columbia <?= $title ? "| $title" : '' ?> </title>

		<base href="<?= base_url() ?>" />
		
		<link rel="stylesheet" href="css/core.css" type="text/css" charset="utf-8" />
		<link rel="stylesheet" href="css/columbia.css?v=3" type="text/css" charset="utf-8" />
		<link rel="stylesheet" href="css/coin-slider-styles.css" type="text/css" charset="utf-8" />
		<link rel="stylesheet" href="css/bjqs.css" type="text/css" charset="utf-8" />

		<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,400italic' rel='stylesheet' type='text/css'>
		
		<link rel="icon" type="image/png" href="http://wwww.fundacioncolumbia.org/favicon.ico">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/modernizr.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/coin-slider.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.ba-throttle-debounce.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.scrollTo-1.4.3.1-min.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/stickyheader.jquery.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/main.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/login.js?2" type="text/javascript" charset="utf-8"></script>
		<script src="js/agendas.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/puertas.js?1" type="text/javascript" charset="utf-8"></script>
		<script src="js/bjqs-1.3.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/checkCookie.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/buscar.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/LAB.min.js" type="text/javascript" charset="utf-8"></script>
		
		<?= $head ?>
    </head>
    <body>
		<div id="mainWrapper">
			<header>
				<a href="<?= base_url() ?>" class="logo"><img src="images/logo_fundacion_columbia.png" alt="Logo Fundación Columbia" /></a>
				<ul class="right">
					<li class="redes_sociales <?= $this->siteuser->isLogged() ? 'logeado' : '' ?>">
						<a href="http://www.facebook.com/fundacioncolumbia" target="_blank"><img src="images/redes_sociales/facebook.jpg" /></a>
						<a href="http://twitter.com/fundcolumbia" target="_blank"><img src="images/redes_sociales/twitter.jpg" /></a>
						<a href="https://plus.google.com/u/0/115334068230956059031/posts?hl=es" target="_blank"><img src="images/redes_sociales/google.jpg" /></a>
						<a href="http://www.youtube.com/user/fundacioncolumbia?feature=watch" target="_blank"><img src="images/redes_sociales/youtube.jpg" /></a>
					</li>
					<?php if ( !$this->siteuser->isLogged() ) : ?>
					<li class="login">
						<a class="ingresar" href="#" rel="ingresar" >Ingresar a mi cuenta</a>
					</li>
					<?php else : ?>
					<li class="registrado">
						<div class="links"><a href="usuarios/panel">panel</a> | <a href="usuarios/logout">salir</a></div>
						Bienvenid@ 
						<a href="usuarios/panel"><?= $this->siteuser->getUserData('nombre') . ' ' . $this->siteuser->getUserData('apellido') ?></a> 
					</li>
					<?php endif; ?>
					<li class="buscar">
						<form id="frmBuscar" action="buscar">
							<input type="text" name="q" value="<?= $_GET['q'] ? $_GET['q'] : 'Buscar...' ?>" id="txtBuscar" />
							<button type="submit" id="btnBuscar"></button>
						</form>
					</li>
				</ul>
				<div class="clear"></div>
			</header>
			
			<nav>
				<ul>
					<?php end($menuTop); $lastMenuTopKey = key($menuTop) ?>
					<?php foreach ($menuTop as $menuItem => $url) : ?>
						<li class="<?= uri_string() == $url ? 'active' : '' ?> menu<?= $menuItem ?>" >
								<a href="<?= base_url($url) ?>"><?= $menuItem ?></a>
								<?php if ( $menuItem == 'Agenda' ) : ?>
								<?php
									if ( $this->uri->segment(1) == 'agenda' && ( $this->uri->segment(2) == 'regular' || $this->uri->segment(2) == 'mensual' ) )
										$oculto = false;
									else
										$oculto = true;
								?>
								<ul class="subAgenda <?= $oculto ? 'hidden' : '' ?>">
									<li class="<?= $this->uri->segment(2) == 'regular' ? 'active' : '' ?>" ><a href="agenda/regular">Agenda regular</a></li>
									<li class="<?= $this->uri->segment(2) == 'mensual' ? 'active' : '' ?>" ><a href="agenda/mensual">Agenda mensual</a></li>
								</ul>
								<?php endif; ?>
						</li>
						<?php if ($menuItem != $lastMenuTopKey ) : ?>
							<li class="separador">·</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<div class="clear"></div>
			</nav>
			
			<?php if ( !$this->siteuser->isLogged() ) : ?>
			<div id="formularios">
				<div id="ingresar" class="formulario">
					<p>
						Para reservar tu lugar en los cursos tenés que registrarte. <br /><strong>¿Todavía no tenés cuenta?</strong> <a href="usuarios/registrarse">Hacé click aquí</a>
					</p>
					<form id="frmIngresar">
						<fieldset>
							<input type="text" name="email" class="email" />
							<label>E.mail:</label>
							<input type="password" name="pass" />
							<label>Contrase&ntilde;a:</label>
							<div class="recordar">
								<input type="checkbox" name="recordar" id="recordar" value="1" />
								<label for="recordar">Recordarme</label>
							</div> 
							<div class="clear"></div>
							<span class="hidden"></span>
							<button type="submit">ingresar</button>
						</fieldset>
					</form>
				</div>
			</div>
			<?php endif; ?>
			
			<div id="mainContent">
				<?php if (!$full) : ?>
				<div class="left">
					<?php if ( count($arrPuertas) ) :  ?>
					<!--<h3>Puertas de Entrada</h3>-->
					<ul class="puertas">
						<?php foreach( $arrPuertas as $nombrePuerta => $puerta ) : ?>
							<?php if ( count($puerta) ) : ?>
								<ul class="puerta <?= $nombrePuerta ?>">
									<li class="title"><?= $puerta[0]['puerta'] ?></li>
									<?php foreach( $puerta as $disciplina ) : ?>
									<li class="item <?= $disciplina['hidden'] ? 'hidden' : '' ?>"><a href="<?=$disciplina['url']?>"><?= $disciplina['title'] ?></a></li>
									<?php endforeach; ?>
									<?php if (count($puerta) > 2) : ?>
									<li class="verMas"><a href="#">ver +</a></li>
									<?php endif; ?>
								</ul>
							<?php endif; ?>
							<?php magico_setData($puerta, 'Disciplina', "#mainContent .puertas ul.$nombrePuerta li.item", MAGICO_SORTABLE, "{items: 'li.item'}") ?>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
					<ul class="agendas">
						<li class="regular"><a href="agenda/regular">Agenda regular</a></li>
						<li class="especial"><a href="agenda/mensual">Agenda mensual</a></li>
					</ul>
					<?php if ( count($arrSponsors) ) : ?>
						<div class="sponsors">
							<h3>Contamos con el apoyo de:</h3>
							<div class="bjqsWrapper">
								<ul class="bjqs">
								<?php foreach( $arrSponsors as $sponsor ) : ?>
									<li><a href="<?= $sponsor['link'] ?>" target="_blank"><img src="<?= $sponsor['imagen'] ?>" title="<?= $sponsor['title'] ?>" /></a></li>
								<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<div class="<?= !$full ? 'right' : 'full' ?>">
					<mp:Content />
				</div>
				<div class="clear"></div>
				<footer>
					<img src="images/footer_logo.png" class="logo" />
					<ul>
						<li><a href="informacion/quienes-somos">Quiénes somos</a></li>
						<li class="separador">·</li>
						<li><a href="puertas-de-entrada">Qué hacemos</a></li>
						<li class="separador">·</li>
						<li><a href="docentes">Docentes</a></li>
						<li class="separador">·</li>
						<li><a href="agenda/regular">Agenda regular</a></li>
						<li class="separador">·</li>
						<li><a href="agenda/mensual">Agenda mensual</a></li>
						<li class="separador">·</li>
						<li><a href="publicaciones">Publicaciones y prensa</a></li>
						<li class="separador">·</li>
						<li><a href="links">Sitios amigos</a></li>
						<!--<li class="separador">·</li>
						<li><a href="#">Descargas</a></li>-->
						<li class="separador">·</li>
						<li><a href="informacion/nuestra-casa">Contacto</a></li>
						<!--<li class="separador">·</li>
						<li><a href="#">¿Querés ser parte?</a></li>-->
					</ul>
					<div class="clear"></div>
					<div class="firmas">
						Dirección creativa: <a href="http://www.taoweb.com.ar/" target="_blank">Tao</a> -
						Diseño: <a href="http://www.lagrannaranja.com.ar" target="_blank">lagrannaranja</a> -
						Desarrollo: <a href="http://www.parleboo.com" target="_blank">pârleboo</a>
					</div>
				</footer>
				
				<?php if ( $newsNotification ) : ?>
				<script src="js/newsNotification.js"></script>
				<div class="newsletter newsletter_notification">
					<a href="#">cerrar x</a>
					<h3>¡Boletín de novedades!</h3>
					<form id="frmNewsletter">
						<fieldset>
							<div class="wrapper">
								<p>Recibí todas las novedades en tu correo:</p>
								<input type="text" name="email" value="Dejanos tu correo electrónico" /><button type="button"></button>
							</div>
							<span class="message">Un momento por favor...</span>
						</fieldset>
					</form>
				</div>
				<?php endif; ?>
			</div>		
		</div>
		
		<mp:Adminnav />
		
		<?php foreach ( $messages as $message ) : ?>
		<div class="jGrowlMessage" style="display: none">
			<?= $message ?>
		</div>
		<?php endforeach; ?>
		
		<?php if ( $_SERVER['HTTP_HOST'] != 'localhost' && !$this->adminuser->isLogged() ) : ?>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-39903440-1', 'fundacioncolumbia.org');
			ga('send', 'pageview');

		</script>
		<script type="text/javascript">
			/* <![CDATA[ */
			var google_conversion_id = 995294263;
			var google_custom_params = window.google_tag_params;
			var google_remarketing_only = true;
			/* ]]> */
		</script>
			
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
		<noscript>
			<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/995294263/?value=0&amp;guid=ON&amp;script=0"/>
			</div>
		</noscript>
		<?php endif; ?>
    </body>
</html>