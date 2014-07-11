<!DOCTYPE html>
 
<html lang="en-US">
    <head>
        <title>Registration Using Facebook</title>
        <style>
            body { font: normal 14px Verdana; }
            h1 { font-size: 24px; }
            h2 { font-size: 18px; }
        </style>
        
        <script>
    function validate(form) {
        errors = {};
        if (form.password.length < 8) {
        errors.password = "You password must be of 8 characters or more";
        }
        if(form.password_confirmation!=form.password) {
        	errors.password_confirmation = "You passwords dosen't match";
        }    
        /*if(getObjectSize(errors)>1){
        	form.u_0_4.disabled=true;
        	//alert("hello");
        }else{
        	form.u_0_4.disabled=false;
        }*/
        return errors;
     }
     
    /* function getObjectSize(obj) {
     	var size = 0, key; // get the size data
     	for (key in obj) { // check the okeys in the object
     		if (obj.hasOwnProperty(key)) size++; // increase the size
     	}
     	return size; // return the size of the object
     }*/
 
</script>
        
    </head>
    <body>
         
        <div id="wrap">
             
            <section id="main">
                 
                <div id="fb-root"></div>
                <script type="text/javascript">
                  window.fbAsyncInit = function() {
                    FB.init({
                      appId      : '899376703411658', // App ID
                      channelUrl : 'http://localhost.com/channel.html', // Channel File
                      status     : true, // check login status
                      cookie     : true, // enable cookies to allow the server to access the session
                      oauth      : true, // enable OAuth 2.0
                      xfbml      : true  // parse XFBML
                    });
                  };
                  (function(d){
                     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
                     js = d.createElement('script'); js.id = id; js.async = true;
                     js.src = "//connect.facebook.net/en_US/all.js";
                     d.getElementsByTagName('head')[0].appendChild(js);
                   }(document));
                </script>
                 
                <h1>Registration Using Facebook</h1>
                <div style="float: left;color:red" class='fbRegistrationErrorMessage'>Note: Please keep the password of 8 characters or more</div> <br> <br><br> 
                <div style="float: left; margin-right: 15px;">
                     
                    <div class="fb-registration"
                        data-fields='[{"name":"name"},
                                        {"name":"birthday"},
                                        {"name":"location"},
                                        {"name":"gender"},
                                        {"name":"email"},
                                        {"name":"username","description":"Username","type":"text"},
                                        {"name":"companyname","description":"Company Name","type":"text"},
                                        {"name":"password"}]'
                        data-redirect-uri="<?php echo site_url('admin/register/saveregisterfb');?>" onvalidate="validate">
                     
                </div>
                 
            </section>
                         
        </div>
         
    </body>
</html>