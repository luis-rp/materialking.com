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
	if($function=='company' || $function=='networkconnections')
	{
		$menu = 'networkconnections';
	}
	if($function=='company' || $function=='invoicecycle')
	{
		$menu = 'invoicecycle';
	}
	if($function=='invoice')
	{
		$menu = 'invoice';
	}
	
	if($function=='invoices')
	{
		$menu = 'invoices';
	}
	if($function=='performance')
	{
		$menu = 'performance';
	}
	if($function=='ads' || $function == 'updatead' || $function == 'addAd')
	{
		$menu = 'ads';
	}
	if($function == 'createformfields' || $function == 'createformdata' || $function == 'formview' || $function == 'deleteformdata' || $function == 'deleteallformdata' || $function == 'formsubmission')
	{
		$menu = 'createformfields';
	}
	if($function=='mailinglist' || $function == 'newtemplate' || $function=="listsubscribers" || $function=="listtemplates" || $function=="edittemplate" || $function=="formsubscriptionsview" || $function=="listpretemplates" || $function=="createformsubscriptions")
	{
		$menu = 'mailinglist';
	}
	
	if($function=='event')
	{
		$menu = 'events';
	}
	
	if($function=='designbook')
	{
		$menu = 'designbook';
	}
	if($function=='forthcomings')
	{
		$menu = 'forthcomings';
	}
	
?>
<!DOCTYPE html>
<html>
<head>
 <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/jquery.js"></script>
<link type="text/css" href="<?php echo base_url(); ?>cometchat/cometchatcss.php" rel="stylesheet" charset="utf-8">
		<script type="text/javascript" src="<?php echo base_url(); ?>cometchat/cometchatjs.php" charset="utf-8"></script>
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
<link href="<?php echo base_url();?>templates/admin/css/bootstrap-tour.min.css" rel="stylesheet"></link>
<script src="<?php echo base_url();?>templates/admin/js/jquery.js"></script>
<script src="<?php echo base_url();?>templates/admin/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>templates/admin/js/bootstrap-tour.min.js"></script>
<script type="text/javascript">

  <?php  if(isset($pagetour->pagetour)) {
					if($pagetour->pagetour==2) { ?>
	 $(document).ready(function(){
		tourCompany = new Tour({
			  steps: [
			  {
				element: ".tour1D",
				title: "Welcome",
				content: "Welcome to your Company Dashboard, This Welcome tour will guide you through a few steps to get your company set up on our website.",
				placement: "left"
			  },
			  {
				element: ".tour2D",
				title: "",
				content: "First, you will want to set up your companies profile, click the Profile & Settings menu option."
			  },
			  {
				element: ".tour3D",
				path: "/company/profile"
				//title: "",
				//content: "First, you will want to set up your companies profile, click the Profile & Settings menu option."
			  }
			]
			});

			$("#tourcontrols").remove();
			tourCompany.restart();
			// Initialize the tour
			tourCompany.init();
			start();
			
	 });
	 
	 <?php } } ?>
	//$('#canceltour').live('click',endTour);   // Commented as Calendar was not working on Date field
	 function start(){
		  
			// Start the tour
				tourCompany.start();
			 }
	 function endTour(){
          
		 $("#tourcontrols").remove();
		 tourCompany.end();
			}
			
	function userquickoptions(){
		
		$("#quickoptions").addClass("open");
	}
			
 </script>
</head>
<?php 
if(@$this->session->userdata('company')->username!=@$pagetour->username)
{
	$this->session->userdata('company')->username=@$pagetour->username;
}
?>
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
    	  
          <div class="pull-right tour1D"> 
    		<div class="chat-toggler">	
    				
					<div class="user-details"> 
						<div class="username">
							<?php //if(count($newquotes)){?>
							<form method="post" action="<?php echo site_url('quote');?>">
							<input type="hidden" name="searchstatus" value="New"/>
							<button type="submit" style="background:none;border:none;">
							<span class="badge badge-important"><?php echo count(@$newquotes);?> invitations</span> 
							</button>
							<span class="bold"><?php echo strtoupper(@$this->session->userdata('company')->username);?></span>									
						
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
    			<li id="quickoptions" onclick="userquickoptions();" class="quicklinks"> 
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
                      <li><a href="<?php echo site_url('company/tier');?>">Tier Price Settings</a></li>
                      <li class="divider"></li>  
                      <li><a href="<?php echo site_url('company/networkconnections');?>">Network Connections</a></li>
                      <li class="divider"></li>  
                      <li><a href="<?php echo site_url('company/invoicecycle');?>">Invoice Cycle</a></li>
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
  <div class="page-sidebar <?php echo (@$menuhide==1)?'mini':'';?>" id="main-menu"> 
   <div class="user-info-wrapper">	
    <?php if(0){?>
	<div class="profile-wrapper">
		<img src="<?php echo base_url();?>templates/front/assets/img/profiles/avatar.jpg"  alt="" data-src="<?php echo base_url();?>templates/front/assets/img/profiles/avatar.jpg" data-src-retina="<?php echo base_url();?>templates/front/assets/img/profiles/avatar2x.jpg" width="69" height="69" />
	</div>
	<?php }?>
    <div class="user-info">
      <div class="greeting">Welcome</div>
      <div class="username" style="width:200px"><span class="semi-bold" style="word-wrap:break-word;"><?php echo @$this->session->userdata('company')->username;?></span></div>
      <div class="status"></div>
    </div>
   </div>
   
	<p class="menu-title"><hr style="border:0;color:#999;background:#999;height:1px;"/></p>
	
    <ul>	
    	<li class="start <?php if($menu=='dashboard'){echo 'active';}?>">
    		<a href="<?php echo site_url('dashboard/index');?>"> <i class="fa fa-dashboard1"></i> 
    			<span class="title">Dashboard</span> <span class="selected"></span>
    		</a> 
    	</li>
      
        <?php if(isset($pagetour)) { 
        	if($pagetour->address!="") {?>
      
	    <li class="<?php if($menu=='tier'){echo 'active';}?>"> 
	    	<a href="<?php echo site_url('company/tier');?>"> <i class="fa fa-dollar1"></i>
	    		<span class="title">Tier Price Settings</span> 
	    	</a> 
	    </li> 
	     
	  	<li class="<?php if($menu=='networkconnections'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('company/networkconnections');?>"><span class="glyphicon glyphicon-globe" aria-hidden="true"></span>&nbsp;&nbsp;
	  			<span class="title">Network Connections</span> 
	  		</a> 
	  	</li>    
	  
	  	<li class="<?php if($menu=='invoicecycle'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('company/invoicecycle');?>"><span class="glyphicon glyphicon-euro" aria-hidden="true"></span>&nbsp;&nbsp;
	  			<span class="title">Invoice Cycle</span> 
	  		</a> 
	  	</li>    
	  
	  	<li class="<?php if($menu=='message'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('message');?>"><i class="fa fa-formmailing"></i>
	  			<span class="title">My Messages</span> 
	  		</a> 
	  	</li>  
	  	    
	  	<li class="<?php if($menu=='quote'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('quote');?>"> <i class="fa fa-legal1"></i>
	  			<span class="title">Quotes, P.O's & Tracking</span>
	  		</a>
	  	</li>
	  	  
	  	<li class="<?php if($menu=='backtracks'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('quote/backtracks');?>"> <i class="fa fa-random1"></i>
	  			<span class="title">Manage Back Orders &nbsp;  <font color="Red">(<?php echo @$this->session->userdata('backorderQtyDue');?>)</font></span>
	  		</a>
	  	</li>
	  
	 	<li class="<?php if($menu=='forthcomings'){echo 'active';}?>"> 
	 		<a href="<?php echo site_url('quote/forthcomings');?>"><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span>&nbsp;&nbsp;
	 			<span class="title">Forthcoming PO's &nbsp;  <font color="Red">(<?php echo @$this->session->userdata('forthcomingQtyDue');?>)</font></span>
	 		</a>
	 	</li>
	  
	  <?php if($this->session->userdata('company')->company_type!='3') { ?>
	  	<li class="<?php if($menu=='order'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('order');?>"> <i class="fa fa-dollar02"></i>
	  			<span class="title">Shopping/Store Sales</span>
	  		</a>
	  	</li>
	  <?php } ?>
	  
	  	<li class="<?php if($menu=='report'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('report');?>"> <i class="fa fa-dollar1N"></i>
	  			<span class="title">Run Reports</span>
	  		</a>
	  	</li>
	  	
	  	<li class="<?php if($menu=='invoices' || $menu=='invoice'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('quote/invoices');?>"> <i class="fa fa-dollar2N"></i>
	  			<span class="title">Manage Invoices</span>
	  		</a>
	  	</li>
	  	
	  	<li class="<?php if($menu=='inventory'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('inventory');?>"> <i class="fa fa-archive1"></i>
	  			<span class="title">Inventory, Deal Feeds, Pricing & Store Settings</span>
	  		</a>
	  	</li>
	  
	   <?php if($this->session->userdata('company')->company_type!='3') { ?>
	  	<li class="<?php if($menu=='performance'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('quote/performance');?>"> <i class="fa fa-search1"></i>
	  			<span class="title">Sales Analytics & Performance</span>
	  		</a>
	  	</li>
	  		   
	  	<li class="<?php if($menu=='ads'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('company/ads');?>"> <i class="fa fa-flag1"></i>
	  			<span class="title">Classifieds</span>
	  		</a>
	  	</li>
	   <?php } ?>
	   
	  	<li class="<?php if($menu=='company'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('company/profile');?>"> <i class="fa fa-male1"></i>
	  			<span class="title tour2D">Profile & Settings</span>
	  		</a>
	  	</li>
	  
	   <?php if($this->session->userdata('company')->company_type!='3') { ?>
	  	<li class="<?php if($menu=='designbook'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('company/designbook');?>"> <span class="glyphicon glyphicon-picture" aria-hidden="true"></span>&nbsp;&nbsp;
	  			<span class="title">Design Book</span>
	  		</a>
	  	</li>
	  	
	  	<li class="<?php if($menu=='createformfields'){echo 'active';}?>">	
	  		<a href="<?php echo site_url('company/createformfields');?>"> <i class="fa fa-formbuilder"></i>
	  			<span class="title">Form Builder</span>
	  		</a>
	  	</li>
	  	
	  	<li class="<?php if($menu=='mailinglist'){echo 'active';}?>">
	  		<a href="<?php echo site_url('company/mailinglist');?>"><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span>&nbsp;&nbsp; 
	  			<span class="title">Mailing List</span>
	  		</a>
	  	</li>
	  	
	  	<li class="<?php if($menu=='events'){echo 'active';}?>"> 
	  		<a href="<?php echo site_url('event');?>"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>&nbsp;&nbsp; 
	  			<span class="title">Manage Events</span>
	  		</a>
	  	</li>
	    <?php } } } ?>
	     
    </ul>
    
	<a href="#" class="scrollup">Scroll</a>
	<div class="clearfix"></div>
  </div>
 
  <div class="page-content <?php echo (@$menuhide==1)?'condensed':'';?>"> 
    <div class="clearfix"></div>
     <?php echo $content; ?>
	
</div>
</div>



</body>
</html>