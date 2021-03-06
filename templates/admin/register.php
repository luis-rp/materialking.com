<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>EZPZP Purchasing User Administration</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
	<script src="<?php echo base_url(); ?>templates/admin/js/adminflare-demo-init.min.js" type="text/javascript"></script>
	<?php if ($_SERVER['SERVER_NAME'] != 'localhost' || 0) { ?>
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700" rel="stylesheet" type="text/css">
	<?php }?>
	<link href="<?php echo base_url(); ?>templates/admin/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
	<link href="<?php echo base_url(); ?>templates/admin/css/adminflare.min.css" media="all" rel="stylesheet" type="text/css" id="adminflare-css">
	
	<script src="<?php echo base_url(); ?>templates/admin/js/modernizr-jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>templates/admin/js/adminflare-demo.min.js" type="text/javascript"></script>

	<!--[if lte IE 9]>
		<script src="<?php echo base_url(); ?>/templates/admin/js/jquery.placeholder.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function () {
				$('input, textarea').placeholder();
			});
		</script>
	<![endif]-->

	<style type="text/css">
		body {
			padding: 0;
			background: #292929 url("<?php echo base_url(); ?>templates/admin/images/left-menu-bg.png");
		}

		a, input, button {
			outline: 0 !important;
		}

		.signin-container {
			margin: 0 auto;
			width: 260px;
		}

		.signin-container form .btn, .signin-container input, .signin-container label, .social p {
			font-size: 14px;
		}

		.signin-container input, .social p, .signin-container form {
			box-sizing:border-box;
			-moz-box-sizing:border-box;
			-webkit-box-sizing:border-box;
		}

		.signin-container .btn {
			border: none;
		}

		a.header, .social p {
			-webkit-text-shadow: rgba(0, 0, 0, 0.8) 0 -1px 0;
			-moz-text-shadow: rgba(0, 0, 0, 0.8) 0 -1px 0;
			-o-text-shadow: rgba(0, 0, 0, 0.8) 0 -1px 0;
			text-shadow: rgba(0, 0, 0, 0.8) 0 -1px 0;
		}

		.signin-container form,
		.signin-container input,
		.signin-container,
		a.header span,
		a.header img,
		.social a {
			-webkit-transition: all 0.2s;
			-moz-transition: all 0.2s;
			-o-transition: all 0.2s;
			transition: all 0.2s;
		}


		/* ======================================================================= */
		/* Logo */

		a.header {
			display: block;
			margin: 0 auto 40px auto;
			font-size: 16px;
			line-height: 22px;
			text-decoration: none;
			width: 325px;
		}

		a.header span, a.header strong {
			margin-left: -1px;
			color: #fff;
		}

		a.header img, a.header span {
		}


		a.header strong {
			font-size: 18px;
		}

		a.header img {
			display: block;
			float: left;
			margin: -10px 10px 0 0;
			position: relative;
		}

		/* ======================================================================= */
		/* Form */

		.signin-container form {
			width: 100%;
			margin: 0;
		}

		.fields {
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
			border: 1px solid rgba(0, 0, 0, 1);
			-webkit-box-shadow: rgba(255, 255, 255, 0.2) 0 1px 0;
			-moz-box-shadow: rgba(255, 255, 255, 0.2) 0 1px 0;
			box-shadow: rgba(255, 255, 255, 0.2) 0 1px 0;
		}

		a.forgot-password {
			background: rgba(0, 0, 0, 0.05);
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
			color: #888;
			display: block;
			float: right;
			font-size: 11px;
			height: 22px;
			line-height: 22px;
			margin: -32px 10px 0 0;
			padding: 0 6px;
			position: relative;
			z-index: 10;
		}

		.signin-container form .btn {
			line-height: 26px;
			margin-top: 15px;
		}

		.signin-container input {
			background: rgba(255, 255, 255, 1);
			border: solid #dedede;
			border-width:  0 0 1px 0;
			-webkit-border-radius: 0;
			-moz-border-radius: 0;
			border-radius: 0;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none;
			height: 40px;
			margin: 0;
			padding: 0 15px;
			width: 100%;
		}

		.signin-container input[type=password] {
			padding-right: 70px;
		}

		.signin-container input:nth-child(1) {
	/*		-webkit-border-radius: 3px 3px 0 0;
			-moz-border-radius: 3px 3px 0 0;
			border-radius: 3px 3px 0 0;*/
		}

		.signin-container input:nth-child(2) {
/*			border: none;
			-webkit-border-radius: 0 0 3px 3px;
			-moz-border-radius: 0 0 3px 3px;
			border-radius: 0 0 3px 3px;*/
		}

		.signin-container input:focus {
			border-color: #dedede;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none;
		}

		/* ======================================================================= */
		/* Signup with */

		.social {
			text-align: center;
			width: 100%;
		}

		.social p {
			color: #777;
			display: block;
			height: 20px;
			margin: 30px 0 20px 0;
		}
		
		.social p:before,
		.social p:after {
			background: rgba(0, 0, 0, 0.3);
			-webkit-box-shadow: rgba(255, 255, 255, 0.07) 0 1px 0;
			-moz-box-shadow: rgba(255, 255, 255, 0.07) 0 1px 0;
			box-shadow: rgba(255, 255, 255, 0.07) 0 1px 0;
			content: "";
			display: block;
			height: 1px;
			margin-top: 10px;
			position: absolute;
			width: 60px;
		}

		.social p:after {
			margin-left: 200px;
			margin-top: -10px;
		}

		.social a {
			background: rgba(0, 0, 0, 0.2);
			-webkit-border-radius: 999px;
			-moz-border-radius: 999px;
			border-radius: 999px;
			-webkit-box-shadow: rgba(255, 255, 255, 0.1) 0 -1px 0 inset;
			-moz-box-shadow: rgba(255, 255, 255, 0.1) 0 -1px 0 inset;
			box-shadow: rgba(255, 255, 255, 0.1) 0 -1px 0 inset;
			color: rgba(255, 255, 255, 0.3);
			display: inline-block;
			font-size: 22px;
			height: 50px;
			line-height:50px;
			margin-right: 10px;
			text-decoration: none;
			width: 50px;
		}

		.social a:hover {
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none;
			color: #fff;
		}

		.social a.twitter:hover {
			background: #38a1c4;
		}

		.social a.facebook:hover {
			background: #4f6faa;
		}

		.social a.google:hover {
			background: #ce5147;
		}

		.social a:last-child {
			margin: 0;
		}
		
		#theme_switcher
		{
		display:none;				
		}	

</style>
	
	<script type="text/javascript">
		$(document).ready(function() {
			var updateBoxPosition = function() {
				$('.signin-container').css({
					'margin-top': ($(window).height() - $('.signin-container').height()) / 2
				});
			};
			$(window).resize(updateBoxPosition);
			setTimeout(updateBoxPosition, 50);
		});
	</script>
</head>


<body>
	<?php echo $content;?>
</body>
</html>