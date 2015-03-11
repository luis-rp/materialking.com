var settings={'force_size':0,'img_width':468,'img_height':60,'refresh_time':5000,'refresh_max':100,'duplicate_banners':0,'location_prefix':'adLocation-','location_class':'swb','window':'_self','default_ad_loc':'default'}
var banners=[new banner('nearanyjobsite','http://www.materialking.com','http://findsupplyhouse.com/uploads/banners/nearanyjobsite.jpg','10/04/2019','top'),new banner('savemoneyonmaterialneeds','http://www.materialking.com','http://findsupplyhouse.com/uploads/banners/savemoneyonmaterialneeds.jpg','30/04/2019','top'),new banner('findsupplyhouses2','http://www.materialking.com','http://findsupplyhouse.com/uploads/banners/findsupplyhouses2.jpg','30/04/2019','top')]
var used=0;var location_counter=0;var refresh_counter=1;var map=new Array();function banner(name,url,image,date,loc)
{this.name=name;this.url=url;this.image=image;this.date=date;this.active=1;this.oid=0;if(loc!='')
{this.loc=loc;}
else
{this.loc=settings.default_ad_loc;}}
function show_banners(banner_location)
{location_counter=location_counter+1;if(banner_location!=''&&banner_location!=undefined)
{map[location_counter]=banner_location;}
else
{map[location_counter]=settings.default_ad_loc;}
var html='<div id="'+settings.location_prefix+location_counter+'" class="'+settings.location_class+'"></div>';document.write(html);display_banners(location_counter);}
function display_banners(location)
{var location_banners=new Array();if(location==''||!location||location<0)
{return;}
var am=banners.length;if((am==used)&&settings.duplicate_banners==0){return;}
for(i=0;i<(banners.length);i++)
{banners[i].oid=i;if((banners[i].loc==map[location])&&(banners[i].active==1))
{location_banners.push(banners[i]);}}
var rand=Math.floor(Math.random()*location_banners.length);var bn=location_banners[rand];var image_size=(settings.force_size==1)?' width="'+settings.img_width+'" height="'+settings.img_height+'"':'';var html='<a href="'+bn.url+'" title="'+bn.name+'" target="'+settings.window+'"><img border="0" src="'+bn.image+'"'+image_size+' alt="'+bn.name+'" /></a>';var now=new Date();var input=bn.date;input=input.split('/',3);var end_date=new Date();end_date.setFullYear(parseInt(input[2]),parseInt(input[1])-1,parseInt(input[0]));if((now<end_date)&&bn.active==1)
{var location_element=document.getElementById(settings.location_prefix+location);if(location_element==null)
{alert('spyka Webmaster banner rotator\nError: adLocation doesn\'t exist!');}
else
{location_element.innerHTML=html;if(settings.duplicate_banners==0)
{banners[bn.oid].active=0;used++;}
return;}}
else
{display_banners(location);}
return;}
function refresh_banners()
{if((refresh_counter==settings.refresh_max)||settings.refresh_time<1)
{clearInterval(banner_refresh);}
used=0;for(j=0;j<(banners.length);j++)
{banners[j].active=1;}
for(j=1;j<(location_counter+1);j++)
{display_banners(j);}
refresh_counter++;}
var banner_refresh=window.setInterval(refresh_banners,settings.refresh_time);