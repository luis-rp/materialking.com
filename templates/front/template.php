<?php
  	$menu = $this->uri->segment (1);
	$menu = $menu?$menu:'dashboard';
	$function = $this->uri->segment (2);
	//echo $menu;echo $function;
	if($function=='backtracks' || $function=='viewbacktrack')
	{
		$menu = 'backtracks';
	}
	if($function=='company' || $function=='tier')
	{
		$menu = 'tier';
	}
	if($function=='invoices')
	{
		$menu = 'invoices';
	}
	if($function=='performance')
	{
		$menu = 'performance';
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta charset="utf-8" />
<title>EZPZP</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta content="" name="description" />
<meta content="" name="author" />

	<?php if($_SERVER['SERVER_NAME'] != 'localhost' || 0){?>
	<style>
		@import url(http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700);
	</style>
	<?php }?>
	
<link href="<?php echo base_url();?>templates/front/assets/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="<?php echo base_url();?>templates/front/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="<?php echo base_url();?>templates/front/assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>templates/front/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url();?>templates/front/assets/plugins/jquery-ricksaw-chart/css/rickshaw.css" type="text/css" media="screen" >
<link rel="stylesheet" href="<?php echo base_url();?>templates/front/assets/plugins/jquery-morris-chart/css/morris.css" type="text/css" media="screen">
<link href="<?php echo base_url();?>templates/front/assets/plugins/jquery-slider/css/jquery.sidr.light.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="<?php echo base_url();?>templates/front/assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="<?php echo base_url();?>templates/front/assets/plugins/jquery-jvectormap/css/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="<?php echo base_url();?>templates/front/assets/plugins/boostrap-checkbox/css/bootstrap-checkbox.css" rel="stylesheet" type="text/css" media="screen"/>


<link href="<?php echo base_url();?>templates/front/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>templates/front/assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>templates/front/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>templates/front/assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>templates/front/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>templates/front/assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>templates/front/assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>


<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url();?>templates/front/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url();?>templates/front/assets/plugins/breakpoints.js" type="text/javascript"></script> 
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<!-- END CORE JS FRAMEWORK --> 


<!-- BEGIN PAGE LEVEL JS --> 
<script src="<?php echo base_url();?>templates/front/assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js" type="text/javascript"></script> 
<script src="<?php echo base_url();?>templates/front/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>  
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-inputmask/jquery.inputmask.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-block-ui/jqueryblockui.js" type="text/javascript"></script> 
<script src="<?php echo base_url();?>templates/front/assets/plugins/bootstrap-select2/select2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-ricksaw-chart/js/raphael-min.js"></script> 
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-ricksaw-chart/js/d3.v2.js"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-ricksaw-chart/js/rickshaw.min.js"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-morris-chart/js/morris.min.js"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-easy-pie-chart/js/jquery.easypiechart.min.js"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-slider/jquery.sidr.min.js" type="text/javascript"></script> 	
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-jvectormap/js/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script> 	
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-jvectormap/js/jquery-jvectormap-us-lcc-en.js" type="text/javascript"></script> 	
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-sparkline/jquery-sparkline.js"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-flot/jquery.flot.min.js"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/jquery-flot/jquery.flot.animator.min.js"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/skycons/skycons.js"></script>

<script src="<?php echo base_url();?>templates/front/assets/plugins/boostrap-checkbox/js/bootstrap-checkbox.js"></script>

<!-- BEGIN CORE TEMPLATE JS --> 
<script src="<?php echo base_url();?>templates/front/assets/js/core.js" type="text/javascript"></script> 
<script src="<?php echo base_url();?>templates/front/assets/js/demo.js" type="text/javascript"></script> 

</head>

<body class="">
<div class="header navbar navbar-inverse"> 
  <!-- BEGIN TOP NAVIGATION BAR -->
  <div class="navbar-inner">
	<div class="header-seperation"> 
		<ul class="nav pull-left notifcation-center" id="main-menu-toggle-wrapper" style="display:none">	
		 <li class="dropdown"> <a id="main-menu-toggle" href="#main-menu" class="" > <div class="iconset top-menu-toggle-white"></div> </a> </li>		 
		</ul>	
	  <?php if(0){?>
      <a href="<?php echo base_url();?>"><img src="<?php echo base_url();?>templates/front/assets/img/logo.png" class="logo" alt=""  data-src="<?php echo base_url();?>templates/front/assets/img/logo.png" data-src-retina="<?php echo base_url();?>templates/front/assets/img/logo2x.png" width="106" height="21"/></a>
      <?php }?>
      
      <ul class="nav pull-right notifcation-center">	
        <li class="dropdown" id="header_task_bar"> <a href="<?php echo base_url();?>" class="dropdown-toggle active" data-toggle=""> <div class="iconset top-home"></div> </a> </li>
        <li class="dropdown" id="portrait-chat-toggler" style="display:none"> <a href="#sidr" class="chat-menu-toggle"> <div class="iconset top-chat-white "></div> </a> </li>        
      </ul>
      </div>
      <div class="header-quick-nav" > 
          
    	  <div class="pull-left"> 
            <ul class="nav quick-section">
              <li class="quicklinks"> <a href="#" class="" id="layout-condensed-toggle" >
                <div class="iconset top-menu-toggle-dark"></div>
                </a> </li>
            </ul>
    	  </div>
    	  
          <div class="pull-right"> 
    		<div class="chat-toggler">	
    				
					<div class="user-details"> 
						<div class="username">
							<?php //if(count($newquotes)){?>
							<form method="post" action="<?php echo site_url('quote');?>">
							<input type="hidden" name="searchstatus" value="New"/>
							<button type="submit" style="background:none;border:none;">
							<span class="badge badge-important"><?php echo count($newquotes);?> invitations</span> 
							</button>
							<span class="bold"><?php echo strtoupper($this->session->userdata('company')->username);?></span>									
						
							</form>
							<?php //}?>
						</div>
					</div> 
    				<?php if(0){?>
    				<div class="iconset top-down-arrow"></div>
    				<div class="profile-pic"> 
    					<img src="<?php echo base_url();?>templates/front/assets/img/profiles/avatar_small.jpg"  alt="" data-src="<?php echo base_url();?>templates/front/assets/img/profiles/avatar_small.jpg" data-src-retina="<?php echo base_url();?>templates/front/assets/img/profiles/avatar_small2x.jpg" width="35" height="35" /> 
    				</div>
    				<?php }?>
    			</div>
    			
    		 <ul class="nav quick-section ">
    		 <li class="quicklinks"> <span class="h-seperate"></span></li> 
    			<li class="quicklinks"> 
    				<a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options">						
    					<div class="iconset top-settings-dark "></div> 	
    				</a>
    				<ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">
                      <li><a href="<?php echo site_url('company/profile');?>"> Edit Profile</a></li>
                      <li><a href="<?php echo site_url('company/password');?>">Change Password</a></li>
                      <li><a href="<?php echo site_url('message');?>"> My Inbox</a></li>
                      <li class="divider"></li>                
                      <li><a href="<?php echo site_url('company/bankaccount');?>">Bank Account Settings</a></li>
                      <li class="divider"></li>                
                      <li><a href="<?php echo site_url('company/tier');?>">Tier Pricing</a></li>
                      <li class="divider"></li>                
                      <li><a href="<?php echo site_url('company/logout');?>"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Log Out</a></li>
                   </ul>
    			</li> 
    			<li class="quicklinks"> <span class="h-seperate"></span></li> 
    		</ul>
          </div>
      </div>
  </div>
</div>

<div class="page-container row"> 
  <div class="page-sidebar" id="main-menu"> 
   <div class="user-info-wrapper">	
    <?php if(0){?>
	<div class="profile-wrapper">
		<img src="<?php echo base_url();?>templates/front/assets/img/profiles/avatar.jpg"  alt="" data-src="<?php echo base_url();?>templates/front/assets/img/profiles/avatar.jpg" data-src-retina="<?php echo base_url();?>templates/front/assets/img/profiles/avatar2x.jpg" width="69" height="69" />
	</div>
	<?php }?>
    <div class="user-info">
      <div class="greeting">Welcome</div>
      <div class="username"><span class="semi-bold"><?php echo $this->session->userdata('company')->username;?></span></div>
      <div class="status"></div>
    </div>
   </div>
   
	<p class="menu-title"><hr style="border:0;color:#999;background:#999;height:1px;"/></p>
	
    <ul>	
      <li class="start <?php if($menu=='dashboard'){echo 'active';}?>"> <a href="<?php echo site_url('dashboard/index');?>"> <i class="fa fa-dashboard"></i> <span class="title">Dashboard</span> <span class="selected"></span></a> </li>
	  <li class="<?php if($menu=='tier'){echo 'active';}?>"> <a href="<?php echo site_url('company/tier');?>"> <i class="fa fa-dollar"></i><span class="title">Tier Pricing</span> </a> </li>      
	  <li class="<?php if($menu=='message'){echo 'active';}?>"> <a href="<?php echo site_url('message');?>"> <i class="fa fa-envelope"></i><span class="title">My Messages</span> </a> </li>      
	  <li class="<?php if($menu=='quote'){echo 'active';}?>"> <a href="<?php echo site_url('quote');?>"> <i class="fa fa-legal"></i><span class="title">My Quoations</span></a></li>  
	  <li class="<?php if($menu=='backtracks'){echo 'active';}?>"> <a href="<?php echo site_url('quote/backtracks');?>"> <i class="fa fa-random"></i><span class="title">Back Order</span></a></li>
	  <li class="<?php if($menu=='order'){echo 'active';}?>"> <a href="<?php echo site_url('order');?>"> <i class="fa fa-dollar"></i><span class="title">Sales Items</span></a></li>
	  <li class="<?php if($menu=='report'){echo 'active';}?>"> <a href="<?php echo site_url('report');?>"> <i class="fa fa-dollar"></i><span class="title">Report</span></a></li>
	  <li class="<?php if($menu=='invoices'){echo 'active';}?>"> <a href="<?php echo site_url('quote/invoices');?>"> <i class="fa fa-dollar"></i><span class="title">Invoices</span></a></li>
	  <li class="<?php if($menu=='inventory'){echo 'active';}?>"> <a href="<?php echo site_url('inventory');?>"> <i class="fa fa-archive"></i><span class="title">Inventory &amp; Price</span></a></li>
	  <li class="<?php if($menu=='performance'){echo 'active';}?>"> <a href="<?php echo site_url('quote/performance');?>"> <i class="fa fa-search"></i><span class="title">View Performance</span></a></li>
	  <li class="<?php if($menu=='company'){echo 'active';}?>"> <a href="<?php echo site_url('company/profile');?>"> <i class="fa fa-male"></i><span class="title">Edit Profile</span></a></li>
    </ul>
    
	<a href="#" class="scrollup">Scroll</a>
	<div class="clearfix"></div>
  </div>
 
  <div class="page-content"> 
    <div class="clearfix"></div>
     <?php echo $content; ?>
	
</div>
</div>


</body>
</html>