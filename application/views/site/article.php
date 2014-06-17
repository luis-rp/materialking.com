
<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span9">
                	<table width="100%">
                    	<tr>
                        	<td align="left"> <h2 class="page-header"><?php echo $article->title;?></h2></td>
                        	<td align="right">
                            	<!-- AddThis Button BEGIN -->
                            	<div class="addthis_toolbox addthis_default_style ">
                            	<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                            	<a class="addthis_button_tweet"></a>
                            	<a class="addthis_counter addthis_pill_style"></a>
                            	</div>
                            	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-536087a3159911fb"></script>
                            	<!-- AddThis Button END -->
                            	
                        	</td>
                    	</tr>
                    </table>
                    <div class="property-detail">
                        <?php echo $article->content;?>
                    </div>
                </div>

                <div class="sidebar span3">

                    <?php if(@$links){?>
                    <div class="widget contact">
                        <div class="content">
                            <form>
                                <div class="control-group">
                                	<label class="control-label" for="radirange">
                                    	<h5><?php echo $article->linkhead;?></h5>
                                    </label>
                                    <div class="controls">
                                    	<table>
                                    	<?php foreach($links as $link){?>
                                    		<tr>
                                    			<td>
                                    				<?php if($link->filename){?>
                                    				<img width="40" src="<?php echo site_url('uploads/item/thumbs/'.$link->filename);?>"/>
                                    				<?php }?>
                                    			</td>
                                    			<td>
                                    				<a href="http://<?php echo $link->link;?>" target="_blank"><?php echo $link->title?></a>
                                    			</td>
                                    		</tr>
                                    	<?php }?>
                                    	</table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php }?>

                    <?php if(@$articleitems){?>
                    <div class="widget contact">
                        <div class="content">
                            <form>
                                <div class="control-group">
                                	<label class="control-label" for="radirange">
                                    	<h5><?php echo $article->itemhead;?></h5>
                                    </label>
                                    <div class="controls">
                                    	<table>
                                    	<?php foreach($articleitems as $ai){?>
                                    		<tr>
                                    			<td>
                                    				<?php if($ai->item_img){?>
                                    				<img width="40" src="<?php echo site_url('uploads/item/'.$ai->item_img);?>"/>
                                    				<?php }?>
                                    			</td>
                                    			<td>
                                    				<a href="<?php echo site_url('site/item/'.$ai->url);?>" target="_blank"><?php echo $ai->itemname?></a>
                                    			</td>
                                    		</tr>
                                    	<?php }?>
                                    	</table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php }?>
                    
                    
                </div>
            </div>




        </div>
    </div>
</div>
</div>