<?php
$mp = $this->session->userdata('managedprojectdetails');
$menu = $this->uri->segment(2);
$menu = $menu ? $menu : 'dashboard';
$function = $this->uri->segment(3);
//echo $menu;echo $function;
if ($menu == 'quote' && $function == 'invoices') {
    $menu = 'invoices';
}

//print_r($pendingbids);die;
$pendingquotes = array();
if(isset($pendingbids))
{
foreach ($pendingbids as $pending) {
    if (!isset($pendingquotes[$pending->quote])) {
        $pendingquotes[$pending->quote] = array();
        $pendingquotes[$pending->quote]['id'] = $pending->quote;
        $pendingquotes[$pending->quote]['count'] = 1;
        $pendingquotes[$pending->quote]['ponum'] = $pending->quotedetails->ponum;
        $pendingquotes[$pending->quote]['potype'] = $pending->quotedetails->potype;
    } else {
        $pendingquotes[$pending->quote]['count'] ++;
    }
}
}
if ($this->session->userdata('usertype_id') == 3 && $menu == 'quote' && !in_array($function, array('calendar', 'jsonlist'))) {
    redirect('admin/dashboard', 'refresh');
}
?>

<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js"><!--<![endif]-->
    <head>
    <link type="text/css" href="<?php echo base_url(); ?>cometchat/cometchatcss.php" rel="stylesheet" charset="utf-8">
		<script type="text/javascript" src="<?php echo base_url(); ?>cometchat/cometchatjs.php" charset="utf-8"></script>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>EZPZP Purchasing User Administration</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <script src="<?php echo base_url(); ?>templates/admin/js/adminflare-demo-init.min.js" type="text/javascript"></script>
<?php if ($_SERVER['SERVER_NAME'] != 'localhost' || 0) { ?>
            <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700" rel="stylesheet" type="text/css">
<?php } ?>

		
        <link href="<?php echo base_url(); ?>templates/admin/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
         <link href="<?php echo base_url(); ?>templates/admin/css/bootstrap-tour.min.css" media="all" rel="stylesheet" type="text/css" id="boostrap-tour">
        <link href="<?php echo base_url(); ?>templates/admin/css/adminflare.min.css" media="all" rel="stylesheet" type="text/css" id="adminflare-css">
        <link href="<?php echo base_url(); ?>templates/admin/css/bootstrap-tagsinput.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-tagsinput">
        <link href="<?php echo base_url(); ?>templates/admin/css/jquerytour.css" media="all" rel="stylesheet" type="text/css">
          
        <script src="<?php echo base_url(); ?>templates/admin/js/jquery.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>templates/admin/js/modernizr-jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>templates/admin/js/adminflare-demo.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>templates/admin/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>templates/admin/js/adminflare.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tagsinput.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tour.min.js" type="text/javascript"></script>         

        <style type="text/css">

			/*.map-wrapper{
			
				margin:0px;padding:0px;float:left;height:210px;width:100%; position:absolute;  z-index:9999;
            }*/
			
			
			
			img[src="http://www.google.com/intl/en_us/mapfiles/close.gif"] {
                 display: block;
                 margin: 0px;
                 z-index:9999;
            }
            .infobox span.key
            {
            	margin-right: 10px;
			}

            #theme_switcher
            {
                display:none;
            }

            @media (max-width: 767px) {
                #supported-browsers header { text-align: center; margin-bottom: 20px; }
            }

            .status-example { line-height: 0; position:relative; top: 22px }     
            
        </style>
<?php if(!$this->session->userdata('managedprojectdetails')){ ?>
        <script type="text/javascript">
        var tourMain;
        var helpTour;
            $(document).ready(function() {


                $('.myLink').click(function(e) {
                    e.preventDefault();
                    $("#searchfrm").attr("action", this.href);
                    $("#searchfrm").submit();
                });
                $('a[rel=tooltip]').tooltip();
                $('.comment-remove').click(function() {
                    bootbox.confirm("Are you sure?", function(result) {
                        alert("Confirm result: " + result);
                    });
                    return false;
                });
                $('#tab-users a').tooltip();
                $(".daterequested").datepicker();
				// commented code as call to tour was giving error while fetching graph
				//Boostratp tour only available on these pages :dashboard,projects,project,costcodelist,costcode
				<?php
				//if(isset($viewname) && ($viewname=="dashboard" || $viewname=="projects" || $viewname=="project" || $viewname=="costcodelist" || $viewname=="costcode" )){
				/*if($this->session->userdata('usertype_id') != 3){

				}else{
               */

                	//if(($this->session->userdata('usertype_id') != 3)  && ($this->session->userdata('tour') == "unfinished")){
                ?>

                <?php  if(isset($pagetour)) {
					if($pagetour==1) { ?>

                $(document).ready(function($){

					var field;
					tourMain = new Tour({
		  		    	  steps: [
		  		    	  {
		  		    	    element: "#step1",
		  		    	    title: "Step 1",
		  		    	    content: "Welcome to your Account, before your job gets <b>a whole lot easier</b>, this tour will help you complete a few simple but necessary steps and get you up and running	quickly."
		  		    	  },
		  		    	  {
		  		    	    element: "#step2",
		  		    	    title: "Step 2",
		  		    	    content: "All of your Quotes, Purchase Orders, Invoices and Items you buy are assigned to a Project and a Cost Code for easy tracking, budgeting and reporting. Let’s add your first Project and Cost Code.",

		  		    	  },
		  		    	  {
		  			    	    element: "#step3",
		  			    	    title: "Step 3",
		  			    	    content: "The Manage tab will help you manage many things, including Projects and Cost Codes. Please Click the Manage tab and let’s get going.",
		  			    	    reflex:true,
		  			    		  onNext:function(tour){
		  							$("#step3 a").click();
		    	  		    	    }
		  			      },
		  			      {
		    			    	    element: "#step4",
		    			    	    title: "Step 4",
		    			    	    reflex:true,
		    			    	    content: "Please Click on the Projects tab.",

		    			   },
		    			   {
		        			   	path:"/admin/project",
					    	    element: "#step5",
					    	    title: "Step 5",
					    	    content: "Please Click the Add Project button.",
					    	    reflex:true,

		    			   },
		    			   {
		        			   	  path:"/admin/project/add",
		    				      element: "#step6",
		    				      title: "Step 6",
		    				      content: "Fill out the form and click Save Project.",
		    				      reflex:true,

		    				      onNext: function(tour){

		    				    	  $("#form-add-prj").submit();
		        				      }

					  		 },
					  		 {
					  			 path:"/admin/project",
						    	    element: "#step7",
						    	    title: "Step 7",

						    	    content: "Congratulations - You have created your first project. Now let's create a Cost Code.",

							 },
					  		 {

						    	    element: "#step3",
						    	    title: "Step 8",
						    	    content: "Click the Manage tab.",
						    	    reflex:true,

						    		  onNext:function(tour){
				  							$("#step3 a").click();
				    	  		    	    }

							 },
					  		 {

						    	    element: "#step9",
						    	    title: "Step 9",
						    	    content: "Click the Cost Code option.",
						    	    reflex:true,


							 },
					  		 {
						  		    path:"/admin/costcode",
						    	    element: "#step10",
						    	    title: "Step 10",
						    	    content: "Click Add Cost Code.",
						    	    reflex:true,


							 },
							 {
								 path:"/admin/costcode/add",
						    	    element: "#step11",
						    	    title: "Step 11",
						    	    content: "Fill out the form and click Update Cost Code.",
						    	    reflex:true,


							 },
							 {

						    	    element: "#step12",
						    	    title: "Step 12",
						    	    prev:-1,
						    	    content: "Bravo - You have just set up your first Project and created a Cost Code to track and monitor your spending.",

							 },


		  		    	],
						onEnd:function(tour){
								$.ajax({
									url:"<?php echo base_url("/admin/admin/finish_user_tour");?>",
									});
							}
		  		    	});

	                <?php  if(isset($viewname) && $viewname=="dashboard"){?>
	                tourMain.restart();
	               <?php } ?>
	  		    	// Initialize the tour
	  		    	tourMain.init();

	  		    	// Start the tour
	  		    	tourMain.start();


	  		  /*  	$("#step6").click(function(e){
						if(!$("#title").val()){
							alert("Please fill out Title Field");
							return false;
						}
	  	  		    });*/

	  		     $("#pages-dropdown","#step3").click(function(e){

	  	  		    	//alert(tour.getCurrentStep());

	  	  		    	if(e.hasOwnProperty('originalEvent') && (tourMain.getCurrentStep()==2 || tourMain.getCurrentStep()==7)){
	  	  		    	tourMain.next();
	  	  		    	}
	  	  		    });

                });


  	  		    <?php } } ?>


				//	}

            });
        </script>
<?php } ?>
        <script>
            var StaticDataSource = function(a)
            {
                this._formatter = a.formatter;
                this._columns = a.columns;
                this._delay = a.delay || 0;
                this._data = a.data
            };
            StaticDataSource.prototype = {
                columns: function()
                {
                    return this._columns
                },
                data: function(b, c)
                {
                    var a = this;
                    setTimeout(function()
                    {
                        var i = $.extend(true, [], a._data);
                        var f = i.length;
                        if (b.search) {
                            i = a.filter(i, b, function(l) {
                                if (l.hasOwnProperty('itemcode')) {
                                    if (~l['itemcode'].toString().toLowerCase().indexOf(b.search.toLowerCase())) {
                                        return true
                                    }
                                }
                                if (l.hasOwnProperty('itemname')) {
                                    if (~l['itemname'].toString().toLowerCase().indexOf(b.search.toLowerCase())) {
                                        return true
                                    }
                                }
                                if (l.hasOwnProperty('catname')) {
                                    if (~l['catname'].toString().toLowerCase().indexOf(b.search.toLowerCase())) {
                                        return true
                                    }
                                }
                                return false
                            })
                        }
                        if (b.sortProperty)
                        {
                            i = a.sortBy(i, b.sortProperty);
                            if (b.sortDirection === "desc")
                            {
                                i.reverse()
                            }
                        }
                        var j = b.pageIndex * b.pageSize;
                        var h = j + b.pageSize;
                        var e = (h > f) ? f : h;
                        var d = Math.ceil(f / b.pageSize);
                        var g = b.pageIndex + 1;
                        var k = j + 1;
                        i = i.slice(j, h);
                        if (a._formatter)
                        {
                            a._formatter(i)
                        }
                        c(
                                {
                                    data: i,
                                    start: k,
                                    end: e,
                                    count: f,
                                    pages: d,
                                    page: g
                                })
                    }, this._delay)
                },
                filter: function(e, a, d)
                {
                    results = [];
                    if (e == null)
                    {
                        return results
                    }
                    for (var f = e.length, b = 0; b < f; b++)
                    {
                        if (d(e[b]) === true)
                        {
                            results[results.length] = e[b]
                        }
                    }
                    return results
                },
                sortBy: function(b, a)
                {
                    return b.sort(function(d, c)
                    {
                        /* for numbers */

                        var s1 = d[a].replace('$ ', '');
                        var s2 = c[a].replace('$ ', '');

                        if (!isNaN(parseInt(s1)))
                            s1 = parseInt(s1);
                        if (!isNaN(parseInt(s2)))
                            s2 = parseInt(s2);

                        /* for date */
                        if (d[a].indexOf('/') > 0 && c[a].indexOf('/') > 0)
                        {
                            var d1 = new Date(d[a]);
                            var d2 = new Date(c[a]);
                            if (!isNaN(d1.getMonth() + 1) && !isNaN(d2.getMonth() + 1))
                            {
                                if (d1 < d2)
                                    return -1;
                                if (d1 > d2)
                                    return 1;
                                return 0;
                            }
                        }
                        if (s1 < s2)
                        {
                            return -1
                        }
                        if (s1 > s2)
                        {
                            return 1
                        }
                        return 0
                    })
                }
            };
            function sortdate(b, a)
            {
                return b.sort(function(d, c)
                {
                    alert('date');
                });
            }
        </script>

    </head>

    <body>

        <header class="navbar navbar-fixed-top" id="main-navbar">
            <div class="navbar-inner">

                <div class="nav-collapse collapse" >
                    <a href="#" class="logo"><img src="<?php echo base_url(); ?>templates/admin/images/smalllogo.png" alt="EZPZP"></a>
                    <a class="btn nav-button collapsed" data-toggle="collapse" data-target=".nav-collapse" href="<?php echo site_url('site/items'); ?>">
                        <span class="icon-reorder"></span>
                    </a>

                     <?php if (@$mp->id) { ?>
                    <ul class="nav">
                    <li class="divider-vertical"></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Quick Order <i class=" icon-caret-down"></i></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo site_url('admin/quote/add/' . $this->session->userdata('managedproject'));?>">Add Quote</a></li>
								<li><a href="<?php echo site_url('admin/quote/add/' . $this->session->userdata('managedproject'));?>/Direct">Add Purchase Order</a></li>
							</ul>
						</li>
					</ul>
					<?php } ?>



                    <div style="float: right;padding-top:0px;padding-right:2px;">
                        <ul class="messages">
                            <li>
                                <a href="<?php echo site_url('admin/quote/calendar'); ?>">
                                    <i class="icon-calendar"></i>PO Calendar
                                </a>
                            </li>

                            <?php if ($this->session->userdata('usertype_id') > 1) { ?>
                            <li>
                            	<a href="<?php echo base_url(); ?>admin/event"><span class="icon-calendar"></span>Events</a>
                            </li>
                            <?php }?>
                                <?php
                                if ($mp) {
                                ?>
                                <li class="separator"></li>
                                <li>
                                    <a><?php echo $mp->title; ?></a>
                                </li>

                                <?php
                                }
                                ?>
                                <?php if ($pendingquotes && $this->session->userdata('usertype_id') != 3) { ?>
                                <li class="separator"></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <span>Pending Bids&nbsp;&nbsp;<?php echo count($pendingbids); ?></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                    <?php foreach ($pendingquotes as $pq) { ?>
                                            <li>
                                            	<?php if ($pq['potype'] == 'Contract') { ?>
                                            	<a href="<?php echo base_url(); ?>admin/quote/conbids/<?php echo $pq['id'] ?>">
                                            	<?php } else { ?>
                                                <a href="<?php echo base_url(); ?>admin/quote/bids/<?php echo $pq['id'] ?>">
                                                <?php } ?>
                                                    <?php echo $pq['ponum']; ?> - <?php echo $pq['count']; ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                                            <?php } ?>
                            <li class="separator"></li>
                            <li>
                                <a href="#"><i class="icon-time"></i><?php $time =  null;  if(isset($timezone)) { $date = new DateTime($time, new DateTimeZone('America/Los_Angeles')); $date->setTimezone(new DateTimeZone($timezone)); echo $time= $date->format('D, d M Y h:i A'); } else { echo date('D, d M Y h:i A'); } ?><span class="responsive-text"> </span></a>
                            </li>

                            <li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<img alt="User" src="<?php echo base_url(); ?>templates/admin/images/avatar.png" height="23px" width="23px">
							&nbsp;
							<?php echo $this->session->userdata('fullname').' - '.$this->session->userdata('position'); ?> <i class=" icon-caret-down"></i></a>
							<ul class="dropdown-menu">
								 <li><a href="<?php echo base_url(); ?>admin/admin/changepwd"><span class="icon-key1"></span> &nbsp; Change Password</a></li>
                                    <li><a href="<?php echo base_url(); ?>admin/admin/profile"><span class="icon-edit1"></span> &nbsp; Edit Profile</a></li>
                                    <li class="divider"></li>
                                    <li><a href="<?php echo base_url(); ?>admin/login/logout"><span class="icon-off"></span> &nbsp; Logout</a>
                                    </li>
							</ul>
						</li>
                        </ul>
                    </div>

                    <div class="input-append">
                        <form id="searchfrm" class="form-inline" name="search" action="" method="post" style="margin-bottom: 0px;">
                            <input type="text" style="width:80px;"  class="span1" id="globalsearch" name="globalsearch" value="<?php echo @$_POST['globalsearch'] ?>"/>
                            <input type="hidden" id="searchponum" name="searchponum" value="<?php echo @$_POST['globalsearch'] ?>"/>
                            <input type="hidden" id="searchitemname" name="searchitemname" value="<?php echo @$_POST['globalsearch'] ?>"/>
                            <input type="hidden" id="searchinvoicenum" name="searchinvoicenum" value="<?php echo @$_POST['globalsearch'] ?>"/>
                            <div class="btn-group">
                                <button class="btn dropdown-toggle btn btn-primary" data-toggle="dropdown" style="font-size:10px;">Action<span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <?php if (@$mp->id) { ?>
                                    <li><a class="myLink" onClick="$('#searchponum').val($('#globalsearch').val());" href="<?php echo base_url(); ?>admin/quote/index/<?php echo $mp->id; ?>">P.O. #</a></li>
                                    <?php } ?>
                                    <li><a class="myLink" onClick="$('#searchinvoicenum').val($('#globalsearch').val());" href="<?php echo base_url(); ?>admin/quote/invoices">Invoice</a></li>
                                    <li><a class="myLink" onClick="$('#searchitemname').val($('#globalsearch').val());" href="<?php echo base_url(); ?>admin/itemcode">Item</a></li>
                                </ul>
                            </div>
                        </form>

                    </div>

                    <div class="dropdown" style="float: left">

                    </div>
                </div>
            </div>
        </header>

        <div style="clear: both"></div>
        <nav id="left-panel" >
            <div id="left-panel-content" style="padding-top: 0px;">
                <ul>
                   <li  <?php if ($menu == 'dashboard') { ?>class="active"<?php } ?>>
                   		<a href="<?php echo base_url(); ?>admin/dashboard"><span class="icon-dashboard"></span>Dashboard</a>
                   </li>

                        <?php if ($this->session->userdata('usertype_id') < 3) {
						 $menu = trim($menu);  ?>						
						  <?php if ($mp) { ?>
    <li id="step3"   class=" powertour-tooltip lp-dropdown <?php if ($menu == 'message' || $menu == 'project' ||  $menu == 'catcode' || $menu == 'contractcatcode' || $menu == 'receive' || $menu == 'billings' || $menu == 'admin' || $menu == 'costcode' || $menu == 'event' || $menu == 'itemcode' || $menu == 'company' || $menu== 'contractor_profile') { echo 'active'; } ?>"  >
						    <a href="#" class="lp-dropdown-toggle" id="pages-dropdown"><span class="icon-edit"></span>Manage</a>
                               <ul class="lp-dropdown-menu simple" data-dropdown-owner="pages-dropdown">
                                
                               
                                <li <?php if ($menu == 'admin' && $function == 'set_bank_purchaser') { ?>class="active"<?php } ?>>
                  <a tabindex="-1" href="<?php echo base_url(); ?>admin/admin/set_bank_purchaser"><i class="icon-picture"></i>&nbsp;&nbsp;Bank Account</a>
                                </li>
                                
                                <?php if ($this->session->userdata('usertype_id') == 1) { ?>                               
                                  <li <?php if ($menu == 'banner') { ?>class="active"<?php } ?>>
	                                    <a tabindex="-1" href="<?php echo base_url(); ?>admin/banner"><i class="icon-picture"></i>&nbsp;&nbsp;Banner</a>
	                                </li>
	                            <?php } ?>    
                                
                                <?php if ($this->session->userdata('usertype_id') == 1) { ?>
                                 	 <li <?php if ($menu == 'company') { ?>class="active"<?php } ?>>
	                                    <a tabindex="-1" href="<?php echo base_url(); ?>admin/company"><i class="icon-ok"></i>&nbsp;&nbsp;Companies</a>
	                                </li>
	                             <?php } ?>

	                             <?php if ($this->session->userdata('usertype_id') == 1) { ?>	                                
	                                 <li <?php if ($menu == 'contractcatcode') { ?>class="active"<?php } ?>>
	             <a tabindex="-1" href="<?php echo base_url(); ?>admin/contractcatcode"><i class="icon-folder-open"></i>&nbsp;&nbsp;Contract Category</a>
	                                </li>
	                            <?php } ?>
	                            
	                             <li <?php if ($menu == 'costcode') { ?>class="active"<?php } ?> id="step9">
                                    <a tabindex="-1" href="<?php echo base_url(); ?>admin/costcode"><i class="icon-money"></i>&nbsp;&nbsp;Cost Codes</a>
                                </li>
	                                
	                             <?php if ($this->session->userdata('usertype_id') == 1) { ?>	                                
	                                 <li <?php if ($menu == 'catcode') { ?>class="active"<?php } ?>>
	                              <a tabindex="-1" href="<?php echo base_url(); ?>admin/catcode"><i class="icon-folder-open"></i>&nbsp;&nbsp;Category</a>
	                                </li>
	                           <?php } ?>
	                           
	                           <li <?php if ($menu == 'event') { ?>class="active"<?php } ?>>
                                	<a href="<?php echo base_url(); ?>admin/event">
                                		<i class="icon-user"></i>
                                		&nbsp;&nbsp;Events
                                	</a>
                            	</li>
	                                
	                           <?php if ($this->session->userdata('usertype_id') == 1) { ?>	                                
	                                 <li <?php if ($menu == 'type') { ?>class="active"<?php } ?>>
	                           <a tabindex="-1" href="<?php echo base_url(); ?>admin/type"><i class="icon-check"></i>&nbsp;&nbsp;Industry/Manufacturer</a>
	                                </li>	                                                            
                               <?php } ?>
                               
                               
                                <li <?php if ($menu == 'inventorymanagement') { ?>class="active"<?php } ?>>
             <a tabindex="-1" href="<?php echo base_url(); ?>admin/inventorymanagement"><i class="icon-picture"></i>&nbsp;&nbsp;Inventory Management</a>
                                </li>
                                
                                  <li <?php if ($menu == 'itemcode') { ?>class="active"<?php } ?>>
	                                    <a tabindex="-1" href="<?php echo base_url(); ?>admin/itemcode"><i class="icon-check"></i>&nbsp;&nbsp;Items</a>
	                              </li>
	                              
	                           <li <?php if ($menu == 'admin' && $function == 'index') { ?>class="active"<?php } ?>>
                                	<a href="<?php echo base_url(); ?>admin/admin/index">
                                		<i class="icon-user"></i>
                                		&nbsp;&nbsp;Manage Users
                                	</a>
                            	</li>   
	                              
	                           <?php if ($this->session->userdata('usertype_id') == 2) { ?>
                                    <li <?php if ($menu == 'message') { ?>class="active"<?php } ?>>
                                        <a href="<?php echo site_url('admin/message/messages/') ?>">
                                            <i class="icon-envelope"></i>&nbsp;&nbsp;Messages
                                        </a>
                                    </li>
                                <?php } ?>
                                
                                 <?php if ($this->session->userdata('usertype_id') != 1) { ?>	                                
	                                 <li <?php if ($menu == 'contractor_profile') { ?>class="active"<?php } ?>>
	                             <a tabindex="-1" href="<?php echo base_url(); ?>admin/contractor_profile"><i class="icon-ok"></i>&nbsp;&nbsp;Profile</a>
	                                </li>
	                                
                                <?php } ?>                               
                                                              	                                
	                            <li <?php if ($menu == 'project') { ?>class="active"<?php } ?> id="step4">
                                	<a href="<?php echo base_url(); ?>admin/project" ><i class="icon-check"></i>&nbsp;&nbsp;Projects</a>
                            	</li>
                                
                                 <li <?php if ($menu == 'serviceandlaboritems') { ?>class="active"<?php } ?>>
	      <a tabindex="-1" href="<?php echo base_url(); ?>admin/serviceandlaboritems"><i class="icon-briefcase"></i>&nbsp;&nbsp;Service & Labor Items</a>
	                              </li>	                                                               
                            </ul>
                        </li>

                        
                        
                        
                            <li <?php if ($menu == 'quote' && $function == 'index') { ?>class="active"<?php } ?>>
                                <a href="<?php echo site_url('admin/quote/index/' . ($this->session->userdata('managedproject') ? $this->session->userdata('managedproject') : '')) ?>">
                                    <span class="icon-legal"></span>QUOTE/PO MANAGEMENT
                                </a>
                            </li>
                            <li <?php if ($menu == 'backtrack') { ?>class="active"<?php } ?>>                   
                               		<a href="<?php echo base_url(); ?>admin/backtrack">
									<span style="color:red;font-size:10px;padding-left:25px;font-weight:bold;margin-top: -36px;"><?php if(@$this->session->userdata('qtyDue') && $this->session->userdata('qtyDue') != '') echo $this->session->userdata('qtyDue'); else echo '0';?> </span>
                               		<span class="icon-random"></span>Back orders</a>
                             </li>
                            <li <?php if ($menu == 'invoices') { ?>class="active"<?php } ?>><a href="<?php echo base_url(); ?>admin/quote/invoices"><span class="icon-list"></span>Invoices</a></li>
                            <li <?php if ($menu == 'report') { ?>class="active"<?php } ?>><a href="<?php echo base_url(); ?>admin/report"><span class="icon-file"></span>Report</a></li>

                        <?php if ($this->session->userdata('usertype_id') == 2) { ?>
                            <li <?php if ($menu == 'order') { ?>class="active"<?php } ?>>
                                <a href="<?php echo site_url('admin/order/') ?>">
                                    <span class="icon-reorder"></span>Store Purchases
                                </a>
                            </li>
                            
                            <li <?php if ($menu == 'quote' && $function == 'receive') { ?>class="active"<?php } ?>>
                                <a href="<?php echo site_url('admin/quote/receive/' . ($this->session->userdata('managedproject') ? $this->session->userdata('managedproject') : '')) ?>">
                                <span style="color:red;font-size:10px;padding-left:25px;font-weight:bold;margin-top: -36px;"><?php if(@$this->session->userdata('receiveqty') && $this->session->userdata('receiveqty') != '') echo $this->session->userdata('receiveqty'); else echo '0';?> </span>
                                    <span class="icon-cog"></span>RECEIVE
                                </a>
                        </li>
                        
                        <li <?php if ($menu == 'quote' && $function == 'billings') { ?>class="active"<?php } ?>>
                                <a href="<?php echo site_url('admin/quote/billings/' . ($this->session->userdata('managedproject') ? $this->session->userdata('managedproject') : '')) ?>">
                                    <span class="icon-hand-down"></span>BILLING
                                </a>
                        </li>
                            
                        <?php } else { ?>



                            <li <?php if ($menu == 'order') { ?>class="active"<?php } ?>>
                                <a href="<?php echo site_url('admin/order/allorders') ?>">
                                    <span class="icon-reorder"></span>PURCHASE REPORT
                                </a>
                            </li>
                        <?php } ?>
                          <?php //} ?>
                        <li <?php if ($menu == 'settings') { ?>class="active"<?php } ?>>
                        	<a href="<?php echo base_url(); ?>admin/settings"><span class="icon-cog"></span>App Settings</a>
                        </li>
                        
                        <!--<li <?php if ($menu == 'contractbids') { ?>class="active"<?php } ?>>
                        	<a href="<?php echo base_url(); ?>admin/quote/contractbids"><span class="icon-cog"></span>Contract Bids</a>
                        </li>  -->            
                        
                          <?php } } else { ?>
                            <?php if ($mp) { ?>
                            <li <?php if ($menu == 'purchaseuser' && $function == 'quotes') { ?>class="active"<?php } ?>>
                                <a href="<?php echo site_url('admin/purchaseuser/quotes/') ?>">
                                    <span class="icon-reorder"></span>QUOTE/PO MANAGEMENT
                                </a>
                            </li>
                            <li <?php if ($menu == 'purchaseuser' && $function == 'messages') { ?>class="active"<?php } ?>>
                                <a href="<?php echo site_url('admin/purchaseuser/messages/') ?>">
                                    <span class="icon-reorder"></span>MESSAGES
                                </a>
                            </li>
                            <li <?php if ($menu == 'backtrack') { ?>class="active"<?php } ?>>
                                <a href="<?php echo site_url('admin/backtrack/') ?>">
                                    <span class="icon-random"></span>BACKORDER
                                </a>
                            </li>                                                
                            <?php } ?>
                        <?php } ?>
                </ul>
            </div>

            <div class="icon-caret-down"></div>
            <div class="icon-caret-up"></div>
        </nav>

        <section class="">
            <?php echo $content; ?>

            <footer id="main-footer">

                <a href="#" class="pull-right" id="on-top-link">On Top&nbsp;<i class="icon-chevron-up"></i>
                </a>
            </footer>
        </section>
        <!-- <link href="<?php echo base_url(); ?>templates/admin/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" id="adminflare-css">
        <script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tour.min.js" type="text/javascript"></script>-->




    </body>
</html>
