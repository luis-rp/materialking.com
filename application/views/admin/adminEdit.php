
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#about').wysihtml5();
	
});
//-->



</script>


<script language = "Javascript">
/**
 * DHTML phone number validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */

// Declaring required variables
var digits = "0123456789";
// non-digit characters which are allowed in phone numbers
var phoneNumberDelimiters = "()- ";
// characters which are allowed in international phone numbers
// (a leading + is OK)
var validWorldPhoneChars = phoneNumberDelimiters + "+";
// Minimum no of digits in an international phone no.
var minDigitsInIPhoneNumber = 10;
// Maximum no of digits in an america phone no.
var maxDigitsInIPhoneNumber = 13;
//US Area Code
var AreaCode=new Array(205,251,659,256,334,907,403,780,264,268,520,928,480,602,623,501,479,870,242,246,441,250,604,778,284,341,442,628,657,669,747,752,764,951,209,559,408,831,510,213,310,424,323,562,707,369,627,530,714,949,626,909,916,760,619,858,935,818,415,925,661,805,650,600,809,345,670,211,720,970,303,719,203,475,860,959,302,411,202,767,911,239,386,689,754,941,954,561,407,727,352,904,850,786,863,305,321,813,470,478,770,678,404,706,912,229,710,473,671,808,208,312,773,630,847,708,815,224,331,464,872,217,618,309,260,317,219,765,812,563,641,515,319,712,876,620,785,913,316,270,859,606,502,225,337,985,504,318,318,204,227,240,443,667,410,301,339,351,774,781,857,978,508,617,413,231,269,989,734,517,313,810,248,278,586,679,947,906,616,320,612,763,952,218,507,651,228,601,557,573,636,660,975,314,816,417,664 ,406,402,308,775,702,506,603,551,848,862,732,908,201,973,609,856,505,575,585,845,917,516,212,646,315,518,347 ,718,607,914,631,716,709,252,336,828,910,980,984,919,704,701,283,380,567,216,614,937,330,234,440,419,740,513 ,580,918,405,905,289,647,705,807,613,519,416,503,541,971,445,610,835,878,484,717,570,412,215,267,814,724,902,787,939,438,450,819,418,514,401,306,803,843,864,605,869,758,784,731,865,931,423,615,901,325,361,430,432,469,682,737,979,214,972,254,940,713,281,832,956,817,806,903,210,830,409,936,512,915,868,649,340,385,435,801,802,276,434,540,571,757,703,804,509,206,425,253,360,564,304,262,920,414,715,608,307,867)

function isInteger(s)
{   var i;
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag)
{   var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character isn't whitespace.
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}
function trim(s)
{   var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not a whitespace, append to returnString.
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character isn't whitespace.
        var c = s.charAt(i);
        if (c != " ") returnString += c;
    }
    return returnString;
}
function checkInternationalPhone(strPhone){
strPhone=trim(strPhone)
if(strPhone.indexOf("00")==0) strPhone=strPhone.substring(2)
if(strPhone.indexOf("+")>1) return false
if(strPhone.indexOf("+")==0) strPhone=strPhone.substring(1)
if(strPhone.indexOf("(")==-1 && strPhone.indexOf(")")!=-1)return false
if(strPhone.indexOf("(")!=-1 && strPhone.indexOf(")")==-1)return false
s=stripCharsInBag(strPhone,validWorldPhoneChars);
if(strPhone.length>10){var CCode=s.substring(0,s.length-10);}
else{CCode="";}
if(strPhone.length>7){var NPA=s.substring(s.length-10,s.length-7);}
else{NPA=""}
var NEC=s.substring(s.length-7,s.length-4)
if(CCode!="" && CCode!=null){
	if(CCode!="1" && CCode!="011" && CCode!="001") return false
	}
if(NPA!=""){
  if(checkAreaCode(NPA)==false){ //Checking area code is vaid or not
  	return false
	}
}
else{return false}
return (isInteger(s) && s.length >= minDigitsInIPhoneNumber  &&  s.length <= maxDigitsInIPhoneNumber );
}
//Checking area code is vaid or not
function checkAreaCode(val){
	var res=false;
	for (var i=0; i<AreaCode.length;i++){
		if(AreaCode[i]==val) res=true;
	}
	return res
}

function ValidateForm(){
	var Phone=document.frmSample.phone
	
	if ((Phone.value==null)||(Phone.value=="")){
		alert("Please Enter your Phone Number")
		Phone.focus()
		return false
	}
	if (checkInternationalPhone(Phone.value)==false){
		alert("Please Enter a Valid Phone Number")
		Phone.value=""
		Phone.focus()
		return false
	}
	return true
 }
</script>


<?php echo $this->session->flashdata('message'); ?>
<section class="row-fluid">
	<h3 class="box-header"><?php echo $title; ?></h3>
	<div class="box">
	<div class="span9">
	
	
  <div class="pull-left" style="width:70%;">
   <form class="form-horizontal" name="frmSample" method="post" action="<?php echo $action; ?>" onSubmit="return ValidateForm()">
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    <?php //if($this->session->userdata('usertype_id') == 1){?>
    <div class="control-group">
    <label class="control-label" for="usertype_id">User Type</label>
    <div class="controls">
    <select name="usertype_id">
    
    <?php if($this->session->userdata('usertype_id') != 1) {  $userarrays=array_slice($userarrays,1,2);;  } 
			foreach($userarrays as $userarray) {
			echo '<option value="'.$userarray['id'].'" ';
			echo $this->validation->usertype_id == $userarray['id'] ? ' selected="selected"' : '';
			echo '>'.$userarray['userType'].'</option>';
			}
		?>
	<?php echo $this->validation->usertype_id_error; ?>
	</select>
    </div>
    </div>
    <?php //}
    //elseif($this->session->userdata('usertype_id') == 2){
    	if($this->session->userdata('usertype_id') == 2){?>
    <input type="hidden" name="purchasingadmin" value="<?php echo $this->session->userdata('id');?>"/>
    <?php }?>
    
    
   <!-- <div class="control-group">
    <label class="control-label" for="category" required>Category</label>
    <div class="controls">
    <select name="category">
	 <?php foreach($categories as $cat){?>
	  <option value='<?php echo $cat->id;?>' <?php if($this->validation->category==$cat->id){ echo 'SELECTED'; }?>><?php echo $cat->catname;?></option>
     <?php }?>
	</select>
    </div>
    </div>-->
   
   
    <div class="control-group">
    <label class="control-label" for="position">Position</label>
    <div class="controls"> 		
	<input type="text" name="position" class="span4" class="text"  value="<?php echo $this->validation->position; ?>"/>
	<?php echo $this->validation->position_error; ?>
    </div>
    </div>
        
    <div class="control-group">
    <label class="control-label" for="username">User Name *</label>
    <div class="controls">
    <input type="text" name="username" class="span4" class="text" required value="<?php echo $this->validation->username; ?>"/>
	<?php echo $this->validation->username_error; ?>
    </div>
    </div>
  
    
    <div class="control-group">
    <label class="control-label" for="fullname">Full Name *</label>
    <div class="controls"> 		
	<input type="text" name="fullname" class="span4" class="text" required value="<?php echo $this->validation->fullname; ?>"/>
	<?php echo $this->validation->fullname_error; ?>
    </div>
    </div>
  
    <div class="control-group">
    <label class="control-label" for="email">Email Address*</label>
    <div class="controls">
   <input type="email" name="email" required class="text span4" value="<?php echo $this->validation->email; ?>"/>
	<?php echo $this->validation->email_error; ?>
    </div>
    </div>
    
    <div class="control-group">
    	<label class="control-label" for="phone">Phone Number</label>
    		<div class="controls">
   				<input type="text" name="phone" id="phone" class="text span4" value="<?php echo $this->validation->phone; ?>"/>
				<?php echo $this->validation->phone_error; ?>
    		</div>
   </div>
    
    
    <?php if(!$this->validation->id){?>
    <div class="control-group">
    <label class="control-label">Password *</label>
    <div class="controls">
    <input type="password" name="password" id="password" class="span4" class="text" value="<?php echo $this->validation->password; ?>" required/>
	<?php echo $this->validation->password_error; ?>
    </div>
    </div>
    <?php }?>

    <?php if($this->session->userdata('usertype_id') < 2){?>
    <div class="control-group">
    <label class="control-label">Status</label>
    <div class="controls">
  	<input type="radio" name="status" value="1" <?php echo $this->validation->set_radio('status', '1'); ?>/> Active <br/><br/>
	<input type="radio" name="status" value="0" <?php echo $this->validation->set_radio('status', '0'); ?>/> Deactive
	<?php echo $this->validation->status_error; ?>
    </div>
    </div>
    <?php }else{?>
    <input type="hidden" name="status" value="<?php $this->validation->status;?>"/>
    <?php } ?>
    
    <?php if($this->session->userdata('usertype_id') < 2){?>
    <div class="control-group">
    <label class="control-label">Profile</label>
    <div class="controls">
  	<input type="radio" name="profile" value="1" <?php echo $this->validation->set_radio('profile', '1'); ?>/> On <br/><br/>
	<input type="radio" name="profile" value="0" <?php echo $this->validation->set_radio('profile', '0'); ?>/> Off
	<?php echo $this->validation->profile_error; ?>
    </div>
    </div>
    <?php }else{?>
    <input type="hidden" name="profile" value="<?php $this->validation->profile;?>"/>
    <?php } ?>
    
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input type="submit" class="btn btn-primary" value="Save"/>
    </div>
    </div>
    
  </form>
  
         </div><!-- End of Pull left -->
   <?php if(isset($adminusers) && count($adminusers) > 0) { ?>
	   <div class="pull-right" style="width:26%;">
		   <div class="table-responsive">
			   <h3>Existing Users</h3>
				  <table class="table table-hover">
				  <tr><th>User Name</th></tr>
				    <?php foreach ($adminusers as $adminuser) { ?>
				  		<tr><td><?php echo $adminuser->fullname; ?></td></tr>
				     <?php } ?>
				  </table>
			</div>
	   </div><!-- End of Pull right -->
   <?php } ?>
	   
	   <div style="clear:both;"></div>
    
    </div>
    </div>
</section>
