 <?php echo $this->session->flashdata('message'); ?>
	<section id="ad-page-title">
        
        <div class="container">

        	<div class="span9 first"> 
        		<h2><?php echo $a_title;?></h2> 

        	</div>

        	<div class="span3"> <span class="ad-page-price"><h2>$ <?php  echo $a_price; ?></h2></span> </div>

        </div>

    </section>

    <section id="ad-page-header">
        
        <div class="container">

        	<div class="span12">

        			<script type='text/javascript'>
	  				jQuery(function() {
						jQuery('.flexslider').flexslider();
					});
				</script>

				<div class="flexslider">
					
					<ul class="slides">
						<?php 
						$images = explode("|",$a_image);
						foreach ($images as $img){

						?>
						<li><img class='flexslider-image' height="560" width="950" src="<?php echo base_url("uploads/ads/".$img); ?>"/></li>
						<?php } ?>
					</ul>
							
				</div>

        	</div>

        </div>

    </section>

    <section class="ads-main-page">

    	<div class="container">

	    	<div class="span9 first">




				<table class="ad-detail-half-box">
					<tr>
						<td class="centered-ad-details" style="text-align: center;">
							<span class="author-avatar">
				    			<?php 

				

								if(!empty($c_logo)) {

					

									echo "<img class='author-avatar' src='" .base_url("uploads/logo/".$c_logo). "' alt='' />";

								} else { 

							?>

								
								<img class="author-avatar" src="<?php echo base_url("uploads/logo/noavatar.png"); ?>" alt="" />

							<?php } ?>
				    		</span>
				    		
				    		
							<span class="ad-details-title"><h3><?php echo $c_title; ?></h3></span>

				    	
			

				    		

							<span class="ad-detail-info">
								<span class="author-profile-ad-details"><a href="<?php echo base_url('site/supplier/'.$c_username); ?>" class="button-ag large green"><span class="button-inner"><i class="fa fa-user"></i>Author Profile</span></a></span>
							</span>

						</td>
					</tr>
					<tr>
						<td>
							<span class="ad-detail-info">Category <span class="ad-detail">
				    			<?php 
								
								?></span>
							</span>

				

							<span class="ad-detail-info">Added <span class="ad-detail">
						    	<?php echo $a_published; ?></span>
							</span>

							

							<span class="ad-detail-info">Location <span class="ad-detail">
						    	<?php echo $a_location; ?></span>
							</span>

							

							<span class="ad-detail-info">Views <span class="ad-detail">
				    			<?php echo $a_views; ?></span>
							</span>


							
						</td>
					</tr>
				</table>

	    		<ul class="links">

					<li class="service-links-pinterest-button">
						<a href="//www.pinterest.com/pin/create/button/?url=<?php echo base_url("classified/ad/".$a_id); ?>&amp;media=&amp;description=<?php echo $a_title; ?>" data-pin-do="buttonPin" data-pin-config="beside"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_20.png" /></a>
						<script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js"></script>
					</li>

					<li class="service-links-facebook-share">
						<div id="fb-root"></div>
						<script>(function(d, s, id) {
							var js, fjs = d.getElementsByTagName(s)[0];
							if (d.getElementById(id)) return;
							js = d.createElement(s); js.id = id;
							js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=247363645312964";
							fjs.parentNode.insertBefore(js, fjs);
							}(document, 'script', 'facebook-jssdk'));</script>
						<div class="fb-share-button" data-href="<?php echo base_url("classified/ad/".$a_id); ?>" data-type="button_count"></div>
					</li>

					<li class="service-links-google-plus-one last">
						<!-- Place this tag where you want the share button to render. -->
						<div class="g-plus" data-action="share" data-annotation="bubble"></div>

						<!-- Place this tag after the last share tag. -->
						<script type="text/javascript">
							(function() {
								var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
								po.src = 'https://apis.google.com/js/platform.js';
								var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
							})();
						</script>
					</li>

					<li class="service-links-twitter-widget first">
						<iframe id="twitter-widget-0" scrolling="no" frameborder="0" allowtransparency="true" src="http://platform.twitter.com/widgets/tweet_button.1384205748.html#_=1384949257081&amp;count=horizontal&amp;counturl=<?php echo base_url("classified/ad/".$a_id) ?>&amp;id=twitter-widget-0&amp;lang=en&amp;original_referer=<?php echo base_url("classified/ad/".$a_id) ?>&amp;size=m&amp;text=<?php echo $a_title ?>&amp;url=<?php echo $a_title; ?>&amp;via=drupads" class="twitter-share-button service-links-twitter-widget twitter-tweet-button twitter-count-horizontal" title="Twitter Tweet Button" data-twttr-rendered="true" style="width: 107px; height: 20px;"></iframe>
					</li>
				</ul>

				<div class="ad-detail-content">

	    			<?php echo $a_description;?>

	    			

	    		</div>

		
<div class="full">

					<h2>Contact Owner</h2>

					<div id="contact-ad-owner">

						<div class="contact-ad-owner-arrow"></div>

						<form name="contactForm" action="<?php echo base_url("classified/sendrequest/".$c_id);?>" id="contact-form" method="post" class="contactform" >
															
							<input type="text" onfocus="if(this.value=='Name*')this.value='';" onblur="if(this.value=='')this.value='Name*';" name="contactName" id="contactName" value="Name*" class="input-textarea" />
														 
							<input type="text" onfocus="if(this.value=='Email*')this.value='';" onblur="if(this.value=='')this.value='Email*';" name="email" id="email" value="Email*" class="input-textarea" />

							<input type="text" onfocus="if(this.value=='Subject*')this.value='';" onblur="if(this.value=='')this.value='Subject*';" name="subject" id="subject" value="Subject*" class="input-textarea" />
														 
							<textarea name="comments" id="commentsText" cols="8" rows="5" ></textarea>
															
							<br />

																	
							<input style="margin-bottom: 0;" name="submitted" type="submit" value="Send Message" class="input-submit"/>	
														
						</form>

						

					</div>

				</div>

	    	<div class="related-ads">

	    			<h2>Related Ads</h2>

	    			<div class="full">

	    						<?php $current = -1; foreach($related as $rel){   $current++;?>
		    						<div class="span2 <?php if($current%4 == 0) { echo 'first'; } ?>">
		    							
		    							<span class="field-content">

		    								<div class="ad-image-related">
		    										<a href="<?php echo base_url("classified/ad".$rel['id']); ?>">
		    										<?php 
		    										$dis_img;
		    					$image= explode("|",$rel['image']);
									if(is_array($image))
										$dis_img = $image[0];
									else 
										$dis_img = $image;?>
		    											 <img class='add-box-main-image' src='<?php echo base_url("uploads/ads/".$rel['image']);?>'/>";

													</a>
		    								</div>
		    									
		    								<div class="ad-description">
		    									<span class="title">
		    										<a href="<?php echo base_url("classified/ad".$rel['id']); ?>">
		                    							<span class="title"><?php echo $rel['title']; ?></span>
		    										</a>
		    									</span>
		    									<span class="price"><?php echo $rel['price']?></span>
		    								</div>
		    								
		    							</span> 

		    						</div>
								<?php } ?>
		    			

	    			</div>

	    		</div>

	    		<div id="ad-comments">

	    			

	    		</div>

	    	</div>
			<!-- Sidebar start -->
			<div class="span3">

	    		<div class="cat-widget" style="margin-top: 10px;">

		    		<div class="cat-widget-title"><h4>Most Popular</h4></div>

		    		<div class="cat-widget-content">
		    			<ul>
		    			<?php foreach($popular as $pop){
		    				$dis_img;
		    					$image= explode("|",$pop['image']);
									if(is_array($image))
										$dis_img = $image[0];
									else 
										$dis_img = $image;
		    					?> 
                            <li class="widget-ad-list">
                            <img class="widget-ad-image" src="<?php echo base_url("uploads/ads/".$dis_img);?>">
						    		<span class="widget-ad-list-content">
						    			<span class="widget-ad-list-content-title"><a href="<?php echo base_url("classified/ad/".$pop['id']); ?>"><?php echo $pop['title']; ?></a></span>
						    			
										<span class="add-price"><?php echo $pop['price']; ?></span>
						    		</span>
								</li>
						<?php } ?>
						</ul>

		    		</div>

		    	</div>

	    	</div>
	    	<!-- sidebar end -->
	    	
		

	    </div>

    </section>

  