parameters0=new Array();
var srt_images='';

function Add(sel)
{
	if(sel=='sel_img')
		{
			if(document.getElementById("sel_img_"+(par_images.length-1)).value!='') 
				{
					fillArray(sel);
					par_images[par_images.length]='';
					fill(sel);
				}
			else
				{
					fillArray(sel);
					fill(sel);
				}
		}
	else
		{
			if(document.getElementById("inp_"+sel+"_"+(parameters0[sel].length-1)).value!='') 
			{
				fillArray(sel);
				parameters0[sel][parameters0[sel].length]='';
				fill(sel);
			}
			else
			{
				fillArray(sel);
				fill(sel);
			}
		}
}

function fillArray(sel)
{
if(sel=='sel_img')
	{
		for(i=0;i<par_images.length;i++) 
			{
				par_images[i]=document.getElementById("sel_img_"+i).value;
			}
	}
else
	{
		for(i=0;i<parameters0[sel].length;i++) 
			{
			  
				parameters0[sel][i]=document.getElementById("inp_"+sel+"_"+i).value;
			}
	}
}

function fill(sel)
{
document.getElementById(sel).innerHTML='';

if(sel=='sel_img')
	{
		selInnerHTML_str='';
		document.getElementById("image_url").value='';
		
		for(i=0;i<par_images.length;i++) 
			{
				selInnerHTML_str+='<input style="width:200px;" id="'+sel+"_"+i+'" onChange="Add(\'sel_img\')" value="'+par_images[i]+'" /> <input type="button" value="X" onClick="Remove('+i+',\'sel_img\');" /><br />';
				document.getElementById("image_url").value+=par_images[i]+";";
			}
		document.getElementById(sel).innerHTML=selInnerHTML_str;
	}
else
	{
		document.getElementById("hid_"+sel).value='';

		for(i=0;i<parameters0[sel].length;i++) 
			{
				var inpElement = document.createElement('input');
				inpElement.setAttribute('type','text');
				inpElement.setAttribute('style','width:200px;');
				inpElement.setAttribute('id','inp_'+sel+'_'+i);
				inpElement.setAttribute('value',parameters0[sel][i]);
				inpElement.setAttribute('onchange','Add(\''+sel+'\')');
				
				var btnElement = document.createElement('input');
				btnElement.setAttribute('type','button');
				btnElement.setAttribute('value','X');
				btnElement.setAttribute('onclick','Remove('+i+',\''+sel+'\')');
						
				document.getElementById(sel).appendChild(inpElement);
				document.getElementById(sel).appendChild(btnElement);
				document.getElementById(sel).appendChild(document.createElement('br'));
				
				document.getElementById("hid_"+sel).value+=parameters0[sel][i]+"	";
				
				 document.getElementById('inp_'+sel+'_'+i).focus();
			}

		if(document.getElementById("all_par_hid")!=null) 
			{
				document.getElementById("all_par_hid").value="";
				for (keyVar in parameters0) 
					{
						if(document.getElementById("hid_"+keyVar)!=null)
						document.getElementById("all_par_hid").value+=keyVar+"@@:@@"+document.getElementById("hid_"+keyVar).value;
					}
			all_par_hid_temp_str=document.getElementById("all_par_hid").value;
			document.getElementById("all_par_hid").value=all_par_hid_temp_str.replace(/</g,"");
			}
	}
}

function loadHids()
{
if(document.getElementById("all_par_hid")!=null) 
	{
		document.getElementById("all_par_hid").value="";
		for (keyVar in parameters0) 
			{
				if(document.getElementById("hid_"+keyVar)!=null)
					{
						document.getElementById("hid_"+keyVar).value='';
						for(i=0;i<parameters0[keyVar].length;i++) 
							{
								document.getElementById("hid_"+keyVar).value+=parameters0[keyVar][i]+"	";
							}
						document.getElementById("all_par_hid").value+=keyVar+"@@:@@"+document.getElementById("hid_"+keyVar).value;
					}
			}
			all_par_hid_temp_str=document.getElementById("all_par_hid").value;
			document.getElementById("all_par_hid").value=all_par_hid_temp_str.replace(/</g,"");

	}

}

function Remove(i,sel)
{
if(sel=='sel_img')
	{
		if(par_images.length!=1)
			{
				fillArray(sel);
				par_images.splice(i,1);
				fill(sel);
				Add(sel);
			}
	}
else
	{
		if(parameters0[sel].length!=1)
			{
				fillArray(sel);
				parameters0[sel].splice(i,1);
				fill(sel);
				Add(sel);
			}
	}

}


function getImage(rootUrl)
{
	tinyMCE.editors.tempimage.onChange.dispatch=function()
		{
			if(tinyMCE.editors.tempimage.contentDocument.getElementsByTagName("img").length>0)
			{
				ImageSrc=tinyMCE.editors.tempimage.contentDocument.getElementsByTagName("img")[0].src;
				
				
				if(rootUrl.substr((rootUrl.length-1),1)=='/')
				ImageSrc=ImageSrc.substr(ImageSrc.indexOf(rootUrl)+rootUrl.length);
				else
				ImageSrc=ImageSrc.substr(ImageSrc.indexOf(rootUrl)+rootUrl.length+1);
				
				document.getElementById("sel_img_"+(par_images.length-1)).value=ImageSrc;
				Add('sel_img');
				tinyMCE.editors.tempimage.contentDocument.body.innerHTML='';
			}
		}
}

function deletevote()
{
		document.getElementById('adminForm').del_sel_votes.value=1;
		submitbutton('apply');
}

function checkedAll (checked) 
{
	 for (var i = 0; i < document.getElementById('adminForm').elements.length; i++) {
		 if(document.getElementById('adminForm').elements[i].name!=undefined)
		 if( document.getElementById('adminForm').elements[i].name.indexOf("delete_vote")==0)
	  document.getElementById('adminForm').elements[i].checked = checked;
	}
}



function save_order()
{
	document.forms["adminForm"].submit();
}

