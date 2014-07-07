<!DOCTYPE html>
 
<html lang="en-US">
    <head>
        <title>Registration Using Facebook</title>
        <style>
            body { font: normal 14px Verdana; }
            h1 { font-size: 24px; }
            h2 { font-size: 18px; }
        </style>
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
                        data-redirect-uri="http://local2.materialking.com/welhome">
                     
                </div>
                 
            </section>
                         
        </div>
         
    </body>
</html>