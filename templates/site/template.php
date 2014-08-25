<?php
$menu = $this->uri->segment(1);
$menu = $menu ? $menu : 'dashboard';
$function = $this->uri->segment(2);
//echo $menu;echo $function;
if (!$function || $function == 'index')
    $function = 'home';
?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
$(function(){
$('.fadein img:gt(0)').hide();
setInterval(function(){$('.fadein :first-child').fadeOut().next('img').fadeIn().end().appendTo('.fadein');}, 3000);
});
</script>
    	<link type="text/css" href="<?php echo base_url(); ?>cometchat/cometchatcss.php" rel="stylesheet" charset="utf-8">
		<script type="text/javascript" src="<?php echo base_url(); ?>cometchat/cometchatjs.php" charset="utf-8"></script>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="EZPZP">
        <meta name="google-site-verification" content="jumUq6TsaD8CvuK9tOgwLo1z9vB80ib8mwzAXwpGsm8" />
		<?php if($_SERVER['SERVER_NAME'] != 'localhost' || 0){?>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
        <?php }?>
        <link rel="shortcut icon" href="<?php echo base_url(); ?>templates/site/assets/img/favicon.png" type="image/png">
        <link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/bootstrap.css" type="text/css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/bootstrap-responsive.css" type="text/css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/libraries/chosen/chosen.css" type="text/css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/libraries/bootstrap-fileupload/bootstrap-fileupload.css" type="text/css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/libraries/jquery-ui-1.10.2.custom/css/ui-lightness/jquery-ui-1.10.2.custom.min.css" type="text/css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/realia-blue.css" type="text/css" id="color-variant-default">
        <link rel="stylesheet" href="#" type="text/css" id="color-variant">
		<style>
            img[src="http://www.google.com/intl/en_us/mapfiles/close.gif"] {
                 display: block;
                 margin: 0px;
                 z-index:9999;
            }
            .infobox span.key
            {
            	margin-right: 10px;
			}
        </style>
		
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3&amp;sensor=true"></script>

        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/jquery.ezmark.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/jquery.currency.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/jquery.cookie.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/retina.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/carousel.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/gmap3.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/gmap3.infobox.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/libraries/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/libraries/chosen/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/libraries/iosslider/_src/jquery.iosslider.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/libraries/bootstrap-fileupload/bootstrap-fileupload.js"></script>
        
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/banners.js"></script>
        
        <?php echo '<script>var loginurl = "' . site_url('network/login/users') . '";</script>' ?>
        <?php echo '<script>var joinurl = "' . site_url('network/join') . '";</script>' ?>
        <script type="text/javascript" src="<?php echo base_url(); ?>/jwplayer-6.9/jwplayer.js"></script>
        
        <script>
            function loginformsubmit()
            {
                var data = $("#loginform").serialize();
                
                $.ajax({
                    type: "post",
                    url: loginurl,
                    data: data
                }).done(function(data) {
                    if (data == 'Success')
                    {
                        $("#loginmodal").modal('hide');
                        window.location = window.location;
                    }
                    else
                    {
                        alert('Incorrect Login.');
                    }
                });
                return false;
            }

            function joinnetwork(id)
            {

                $("#jointoid").val(id);
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url("site/formview"); ?>/'+id,

                }).done(function(data) {
					$("#htmlplace").html(data);
                });

                $("#joinmodal").modal('show');
            }

            function joinformsubmit()
            {
                var data = $("#joinform").serialize();
                
                $.ajax({
                    type: "post",
                    url: joinurl,
                    data: data
                }).done(function(data) {
                    if (data == 'Error')
                    {
                        alert(data);
                    }
                    else if (data == 'Success')
                    {
                        alert('Request sent successfully.');
                        window.location = window.location;
                    }
                });
                return false;
            }

            function filtercategory(id)
            {
                $("#searchcategory").val(id);
                $("#categoryform").submit();
            }
            
            function filtercategorystore(id)
            {
                $("#searchbreadcrumbcategory").val(id);                
                $("#categorysearchform2").submit();
            }
            
        </script>
        <?php if ($this->session->userdata('navigator_lat') && $this->session->userdata('navigator_lng')) {
            
        } else {
            ?>
            <script>
                function success(position) 
                {
                    var s = document.querySelector('#status');
                    location.href = "<?php echo site_url("site/my_location"); ?>/" + position.coords.latitude + "/" + position.coords.longitude;
                }

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(success);
                } else {
                    error('not supported');
                }

            </script>
            <?php
        }
        ?>
        <?php //print_r($this->session->userdata('site_loggedin'));  ?>
        <title>EZPZP</title>
    </head>
    <body>
        <div id="wrapper-outer" >
            <div id="wrapper">
                <div id="wrapper-inner">
                    <!-- BREADCRUMB -->
                    <div class="breadcrumb-wrapper">
                        <div class="container">
                            <div class="row">
                                <div class="span12">
                                    <ul class="breadcrumb pull-left">
                                        <li><a href="<?php echo base_url(); ?>">Home</a></li>
                                    </ul>
                                    <div class="account pull-right">
                                        <ul class="nav nav-pills">
                                            <li><a href="<?php echo base_url('company/login'); ?>">Login as Supplier</a></li>
                                            <?php if (!$this->session->userdata('site_loggedin')) { ?>
                                                <li><a href="javascript:void(0);" onclick="$('#loginmodal').modal('show');">Login as Purchaser</a></li>
                                            <?php } else { ?>
                                                <li><a href="<?php echo site_url('network/logout'); ?>">Logout <?php echo $this->session->userdata('site_loggedin')->username; ?></a></li>
                                                <li><a href="<?php echo site_url('admin'); ?>">Dashboard</a></li>
                                            <?php } ?>
                                            <?php if ($this->session->userdata('pms_site_cart')) { ?>
                                                <li>
                                                    <a href="<?php echo site_url('cart'); ?>">
                                                        Cart
                                                        (<?php echo count($this->session->userdata('pms_site_cart')) ?> items)
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- HEADER -->

                    <div id="header-wrapper">
                        <div id="header">
                            <div id="header-inner">
                                <div class="container">
                                    <div class="navbar">
                                        <div class="navbar-inner">
                                            <div class="row">
                                                <div class="logo-wrapper span3">
                                                    <div class="logo">
                                                        <a href="<?php echo base_url(''); ?>" title="Home">
                                                            <img src="<?php echo base_url(); ?>templates/site/assets/img/logo.png" alt="Home">
                                                        </a>
                                                    </div>
                                                </div>
                                                
                                                 <span class="span7" style="padding-top:2px">
                                               <!-- <script type="text/javascript">
													show_banners('top');
												</script>-->
												<?php  try { ?>
                                                 <a target="_self" href="http://www.materialking.com">
                                                 <div style="width:600px;height:90px; overflow:hidden;" class="fadein">
                                                      <?php foreach ($banner as $item){ ?>
                                                        <img src="<?php echo base_url();?>uploads/banners/<?php echo $item->banner;?>"
                                                            alt="<?php echo $item->banner;?>" style="height:90px; width:600px;">
                                                      <?php } ?>
                                                 </div>
                                                 </a>
                                                 <?php }
                                                 catch(Exception $e) {
	                                             echo 'No images found for this slideshow.<br />';
                                                 }?>
                                                 </span>
                                                 <?php if (!$this->session->userdata('site_loggedin')) { ?>
                                                <a class="btn btn-primary btn-large list-your-property arrow-right" href="javascript:void(0);" onclick="$('#createmodal').modal();">Create Account</a>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NAVIGATION -->

                    <div id="navigation">
                        <div class="container">
                            <div class="navigation-wrapper">
                                <div class="navigation clearfix-normal">
                                    <ul class="nav navtext">
                                        <li class="menuparent">
                                        <li <?php
                                            if ($function == 'home') {
                                                echo 'class="current"';
                                            }
                                            ?>><a href="<?php echo base_url(); ?>">Home</a></li>
                                        <li <?php
                                        if ($function == 'suppliers' || $function == 'supplier') {
                                            echo 'class="current"';
                                        }
                                        ?>><a href="<?php echo base_url('site/suppliers'); ?>">Suppliers</a></li>
                                 
                                        <li <?php
                                            if ($function == 'items') {
                                                echo 'class="current"';
                                            }
                                        ?>>
                                       <a href="<?php echo base_url('site/items'); ?>">Shop</a></li>
                                        
                                         <li <?php
                                            if ($function == 'classified') {
                                                echo 'class="current"';
                                            }
                                        ?>>
                                        <a href="<?php echo base_url('site/classified'); ?>">Classifieds</a>
                                        
                                        </li>
                                        
                                    </ul>

                                    <form method="POST" class="site-search" action="<?php echo base_url('site/search'); ?>" id="search_form">
                                        <div class="input-append">
                                            <input type="hidden" name="search_type" id="search_type">
                                            <select style="width:120px;height:20px;height:34px; margin-right:5px;" name = "searchfor" id="searchfor" >
                                            <option value="itemandtags" <?php if(isset($_POST['searchfor']) && $_POST['searchfor']!="" && $_POST['searchfor']=="itemandtags") echo "selected";  ?>>Items & Tags</option>
                                            <option value="suppliers" <?php if(isset($_POST['searchfor']) && $_POST['searchfor']!="" && $_POST['searchfor']=="suppliers") echo "selected";  ?>>Suppliers</option>
                                            </select>&nbsp;&nbsp;
                                            <input title="Enter the terms you wish to search for." class="search-query span2 form-text" placeholder="Search" type="text" name="keyword" value="<?php echo isset($keyword) ? $keyword : ""; ?>">
                                            <button type="submit" class="btn"><i class="icon-search"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="categoryform" action="<?php echo site_url('site/search')?>" method="post">
                    	<input type="hidden" id="searchcategory" name="category" value="<?php echo @$_POST['category']?>"/>
                    </form>
                    
                    <?php echo $content; ?>

                </div>

                <div id="footer-wrapper">
                    <div id="footer-top">
                        <div id="footer-top-inner" class="container">
                            <div class="row">
                                <div class="widget span4">
                                    <div class="title">
                                        <h2>Contact us</h2>
                                    </div>

                                    <div class="content">
                                        <table class="contact">
                                            <tbody>
                                                <tr>
                                                    <th class="address">Address:</th>
                                                    <td>13839 Weddington Street <br/> Sherman Oaks <br/> CA.</td>
                                                </tr>
                                                <tr>
                                                    <th class="phone">Phone:</th>
                                                    <td>310-466-1956</td>
                                                </tr>
                                                <tr>
                                                    <th class="email">E-mail:</th>
                                                    <td><a href="mailto:info@MaterialKing.com">info@MaterialKing.com</a></td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="widget span4">
                                    <div class="title">
                                        <h2 class="block-title">Useful Links</h2>
                                    </div>

                                    <div class="content">
                                        <ul class="menu nav">
                                            <li class="leaf"><a href="<?php echo base_url('site/about'); ?>">About us</a></li>
                                            <li class="leaf"><a href="<?php echo base_url('site/contact'); ?>">Contact us</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="widget span3">
                                    <div class="title">
                                        <h2 class="block-title">Say Hello!</h2>
                                    </div>

                                    <div class="content">
                                        <form method="post">
                                            <div class="control-group">
                                                <label class="control-label" for="inputName">
                                                    Name
                                                    <span class="form-required" title="This field is required.">*</span>
                                                </label>
                                                <div class="controls">
                                                    <input type="text" id="inputName">
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="inputEmail">
                                                    Email
                                                    <span class="form-required" title="This field is required.">*</span>
                                                </label>
                                                <div class="controls">
                                                    <input type="text" id="inputEmail">
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="inputMessage">
                                                    Message
                                                    <span class="form-required" title="This field is required.">*</span>
                                                </label>

                                                <div class="controls">
                                                    <textarea id="inputMessage"></textarea>
                                                </div>
                                            </div>

                                            <div class="form-actions">
                                                <input type="submit" class="btn btn-primary arrow-right" value="Send">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="footer" class="footer container">
                        <div id="footer-inner">
                            <div class="row">
                                <div class="span6 copyright">
                                    <p>&copy; Copyright 2014 by EZPZ-P. All rights reserved.</p>
                                </div>

                                <div class="span6 share">
                                    <div class="content">
                                        <ul class="menu nav">
                                            <li class="first leaf"><a href="http://www.facebook.com" class="facebook">Facebook</a></li>
                                            <li class="leaf"><a href="http://www.linkedin.com" class="linkedin">LinkedIn</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


   <div class="modal hide fade" id="loginmodal">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Login As Purchasing Admin</h3>
    </div>
    <div class="modal-body">
   
    <form id="loginform" class="nobottommargin nopadding" onsubmit="loginformsubmit();return false;" method="post">
                        <div class="modal-body">
                            <p id="loginerror"></p>
                            Username: <input class="required input-block-level" type="text" name="username"/>
                            <br/>
                            Password: <input class="required input-block-level" type="password" name="password"/>
                        </div>
                        <div class="paddingleft">
                         <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-primary">Login</button>
                          <div class="pull-right"><a href="<?php echo site_url('admin/register/forgot');?>" style="text-decoration:none;">Forgot Username/Password</a>&nbsp;|&nbsp;
                          <a href="<?php echo site_url('admin/register'); ?>" style="text-decoration:none;">Register</a></div>
                       </div> 
                          
                       </div>
                    </form>
    </div>
    </div>
    
      
        <!-- Modal -->
         <div class="modal hide fade" id="joinmodal" style="height:600px;overflow:auto;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title nobottompadding" id="myModalLabel">Request</h3>
                    </div>
                    <form id="joinform" action="<?php echo site_url('network/join'); ?>" method="post" onsubmit="joinformsubmit();return false;">
                        <input type="hidden" name="totype" value="company"/>
                        <input type="hidden" id="jointoid" name="toid" value=""/>
                        
                        <div class="modal-body">
                          <div id="htmlplace"></div>
                        </div>
                        
                        <div class="modal-body">
                            <h4>Account Number</h4>
                            <input type="text" class="required input-block-level" name="accountnumber"/>
                        </div>
                        <div class="modal-body">
                            <h4>Message</h4>
                            <textarea type="text" class="required input-block-level" name="message"></textarea>
                        </div>
                        <div class="modal-body">
                            <h4>Wish to Apply?</h4>
                            <input type="checkbox" name="wishtoapply" value="1"/>
                        </div>
                        <div class="paddingleft">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="joinformsubmit();">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal hide fade" id="createmodal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <input type="button" class="close" data-dismiss="modal" aria-hidden="true" value="&times">
                        <h3 class="modal-title nobottompadding" id="myModalLabel">Create Account</h3>
                    </div>
                     <br/> <br/>
                    &nbsp;&nbsp;&nbsp; <a href="<?php echo site_url('company/register')?>">Create Supplier Account</a>
                    <br/>
                    <br/>
                    &nbsp;&nbsp;&nbsp; <a href="<?php echo site_url('admin/register')?>">Create Purchasing Account</a>
                     <br/>
                    <br/>
                    &nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="$('#loginmodal').modal('show'); $('#createmodal').modal('hide');">Login</a>
                     <br/>
                    <br/>
                     <br/>
                </div>
            </div>
        </div>
        
<link type="text/css" href="<?php echo base_url("/cometchat/cometchatcss.php");?>" rel="stylesheet" charset="utf-8">
<script type="text/javascript" src="<?php echo base_url("/cometchat/cometchatcss.php");?>" charset="utf-8"></script>
    </body>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    
      ga('create', 'UA-50628203-1', 'findsupplyhouse.com');
      ga('send', 'pageview');
    </script>
        
</html>