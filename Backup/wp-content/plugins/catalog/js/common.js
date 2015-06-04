	var resQ=1;
	
function SpiderCatHttpReq()
{
	var httpRequest = createHttpRequest();
	var resultId = '';

	function createHttpRequest()
	{
		var httpRequest;
		var browser = navigator.appName;

		if (browser == "Microsoft Internet Explorer")
		{
			httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
		}
		else
		{
			httpRequest = new XMLHttpRequest();
		}

		return httpRequest;
	}

	this.sendRequest=function(file, _resultId, txt)
	{
		resultId = _resultId;

	httpRequest.open('post', file, true);
		
		httpRequest.onreadystatechange = getRequest;

	httpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf8");

		httpRequest.send(txt);
	}

	function getRequest()
	{
		if (httpRequest.readyState == 4)
		{
		 if(resultId!='')
			document.getElementById(resultId).innerHTML = httpRequest.responseText;
		resQ=1;
		}
	}
}

var SCHR=new SpiderCatHttpReq();

function prod_change_picture(url,prod_id,obj,width,height)
{
		
	document.getElementById("prod_main_picture_a_"+prod_id).href=obj.parentNode.href;	
	document.getElementById("prod_main_picture_"+prod_id).src=url;
	
}


function vote(vote_value,prid,div_id,rated_text,home___)
{  
	if(resQ)   
		{  
			resQ=0;
			
		paramslist=home___+'&product_id='+prid+'&vote_value='+vote_value+'&format=row';  
		  
		SCHR.sendRequest(home___, div_id ,paramslist);	
		
		document.getElementById(div_id).innerHTML='<div style="text-align:center;">'+rated_text+'</div>';  
		
		}  		
			
		
}


function submit_reveiw(text1,text2,text3)
{  
if(document.getElementById("full_name_"+prod_id).value=='')      
{              
alert(text1);  	 	      
document.getElementById("full_name_"+prod_id).focus();  	 	    
}        
else  
if(document.getElementById("message_text_"+prod_id).value=='')      
{              
alert(text2);   	 	      
document.getElementById("message_text_"+prod_id).focus();
}       
else  
{
	if(resQ) 
		{
			resQ=0;
  	  		SCHR.sendRequest(document.getElementById("wd_captcha_img_"+prod_id).src.split("&")[0]+'&checkcap=1&cap_code='+document.getElementById("review_capcode_"+prod_id).value, "caphid_"+prod_id,'');
			resNumberOfTry=0;
			submitReveiwInner(text3);
		}

} 

}

function submitReveiwInner(text)
{
	if(resQ) 
		{
			if(document.getElementById("caphid_"+prod_id).innerHTML=="1")
				document.forms["review_"+prod_id].submit();   
			else
				{
					alert(text);
					refreshCaptcha(prod_id);
				}
		}   
else if(resNumberOfTry<100) setTimeout("submitReveiwInner('"+text+"');",200); resNumberOfTry++;

}

function refreshCaptcha(prod_id)
{
	
document.getElementById("wd_captcha_img_"+prod_id).src=document.getElementById("wd_captcha_img_"+prod_id).src.split("&")[0]+'&r='+Math.floor(Math.random()*100);
document.getElementById("review_capcode_"+prod_id).value='';
}

function cat_form_resett(form,type,idd)
{
var pr_name='prod_name_'+type+'_'+idd;
var cat_idd='cat_id_'+type+'_'+idd;
document.getElementById(pr_name).value="";

if(typeof(form.cat_idd)!=='undefined') form.cat_idd.value='0';

form.submit();
}