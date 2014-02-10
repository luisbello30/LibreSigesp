
var url= "../../php/sigesp_srh_a_jubilado.php";
var metodo='get';
var img="<img src=../../../public/imagenes/progress.gif> ";

var mygrid;
var timeoutHandler;//update will be sent automatically to server if row editting was stoped;
var rowUpdater;//async. Calls doUpdateRow function when got data from server
var rowEraser;//async. Calls doDeleteRow function when got confirmation about row deletion
var authorsLoader;//sync. Loads list of available authors from server to populate dropdown (co)
var mandFields = [0,1,1,0,0];

function ue_mostrar(myfield,e)
{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	if (keycode == 13)
	{
		Buscar();
		return false;
	}
	else
		return true
}
		
	//initialise (from xml) and populate (from xml) grid
function doOnLoad()
{
	hidtipo=document.form1.hidtipo.value;
	mygrid = new dhtmlXGridObject('gridbox');
	mygrid.setImagePath("../../../public/imagenes/"); 
	//set columns properties
	mygrid.setHeader("Código,Cedula,Apellido, Nombre");
	mygrid.setInitWidths("90,90,150,170");
	mygrid.setColAlign("center,center,center,center");
	mygrid.setColTypes("link,ro,ro,ro");
	mygrid.setColSorting("str,str,str,str");//nuevo  ordenacion
	mygrid.setColumnColor("#FFFFFF,#FFFFFF,#FFFFFF,#FFFFFF");
	//mygrid.loadXML("../../php/sigesp_srh_a_personal.php?valor=createXML"+"&hidtipo="+hidtipo);
	mygrid.setSkin("xp");
	mygrid.init();
}
		
function terminar_buscar()
{ 
   divResultado = document.getElementById('mostrar');
   divResultado.innerHTML= '';   
}		
		
function Buscar()
{
	codper=document.form1.txtcodper.value;
	cedper=document.form1.txtcedper.value;
	nomper=document.form1.txtnomper.value;
	apeper=document.form1.txtapeper.value;
	hidtipo=document.form1.hidtipo.value;

	mygrid.clearAll();
	divResultado = document.getElementById('mostrar');
	divResultado.innerHTML= img;
	mygrid.loadXML("../../php/sigesp_srh_a_jubilado.php?valor=buscar"+"&txtcodper="+codper+"&txtcedper="+cedper+"&txtnomper="+nomper+"&txtapeper="+apeper+"&hidtipo="+hidtipo);
	setTimeout (terminar_buscar,3000);
}

function Limpiar_busqueda () 
{
	$('txtcodper').value="";
	$('txtcedper').value="";
	$('txtnomper').value="";
	$('txtapeper').value="";	
}

//FUNCIONES PARA INICIALIZAR LOS COMBOS DE ESTADO MUNICIPIO Y PARROQUIA AL TRAER LOS DATOS DEL CATALOGO

function LimpiarCambioPais()
{
	removeAllOptions(opener.document.form1.cmbcodest);
	opener.document.form1.cmbcodest.selectedIndex = 0;
	LimpiarCambioEstado();
}

function LimpiarCambioEstado()
{
	removeAllOptions(opener.document.form1.cmbcodmun);	
	opener.document.form1.cmbcodmun.selectedIndex = 0;
	LimpiarCambioMunicipio();
}

function LimpiarCambioMunicipio()
{
	removeAllOptions(opener.document.form1.cmbcodpar);
	opener.document.form1.cmbcodpar.selectedIndex = 0;
}

function LimpiarCambioPaisNac()
{
	removeAllOptions(opener.document.form1.cmbcodestnac);
	opener.document.form1.cmbcodestnac.selectedIndex = 0;
}

function ue_cambiopais(ls_codpai, ls_codest, ls_codmun)
{
	LimpiarCambioPais();
	if (ue_valida_null(opener.document.form1.cmbcodpai))
	{
		function onCambioPais(respuesta)
		{
			var estados=JSON.parse(respuesta.responseText);
			
			for (j=0; j<estados.codest.length; j++)
			{
				opener.document.form1.cmbcodest.options[opener.document.form1.cmbcodest.options.length] = new Option(estados.desest[j],estados.codest[j]);
			}
			//El siguiente if es usado cuando viene del catalogo	  
			if (trim(opener.document.form1.hidcodest.value) != "")
			{
				opener.document.form1.cmbcodest.value = opener.document.form1.hidcodest.value;
				opener.document.form1.hidcodest.value = "";
				ue_cambioestado (ls_codpai, ls_codest, ls_codmun);
			}
		}
		params = "operacion=ue_inicializarestado&codpai="+ls_codpai;
		new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onCambioPais});
	}  
}

function ue_cambioestado(ls_codpai, ls_codest, ls_codmun)
{
	LimpiarCambioEstado();
	if (ue_valida_null(opener.document.form1.cmbcodest))
	{
		function onCambioEstado(respuesta)
		{
			var municipios = JSON.parse(respuesta.responseText);
			for (j=0; j<municipios.codmun.length; j++)
			{
				opener.document.form1.cmbcodmun.options[opener.document.form1.cmbcodmun.options.length] = new Option(municipios.denmun[j],municipios.codmun[j]);
			}
			//El siguiente if es usado cuando viene del catalogo	  
			if (trim(opener.document.form1.hidcodmun.value) != "")
			{
				opener.document.form1.cmbcodmun.value = opener.document.form1.hidcodmun.value;
				opener.document.form1.hidcodmun.value = "";
				ue_cambiomunicipio(ls_codpai, ls_codest, ls_codmun);
			}
		}
		params = "operacion=ue_inicializarmunicipio&codpai="+ls_codpai+"&codest="+ls_codest;
		new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onCambioEstado});
	}  
} 

function ue_cambiomunicipio(ls_codpai, ls_codest, ls_codmun)
{
	LimpiarCambioMunicipio();
	if (ue_valida_null(opener.document.form1.cmbcodmun))
	{
		function onCambioMunicipio(respuesta)
		{
			var parroquias = JSON.parse(respuesta.responseText);
			for (i=0; i<parroquias.codpar.length; i++)
			{
				opener.document.form1.cmbcodpar.options[opener.document.form1.cmbcodpar.options.length] = new Option(parroquias.denpar[i],parroquias.codpar[i]);
			}	  
			//El siguiente if es usado cuando viene del catalogo	  
			if (trim(opener.document.form1.hidcodpar.value) != "")
			{
				opener.document.form1.cmbcodpar.value = opener.document.form1.hidcodpar.value;
				opener.document.form1.hidcodpar.value = "";
				ocultar_mensaje("mensaje");
				if (opener.document.form1.hidguardar.value == "modificar")
				{ try{catalogo.close();}catch(e){}}
			}
		}
		params = "operacion=ue_inicializarparroquia&codpai="+ls_codpai+"&codest="+ls_codest+"&codmun="+ls_codmun;
		new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onCambioMunicipio});  
	}  
}

function ue_cambiopaisnac(ls_codpainac)
{
	LimpiarCambioPaisNac();
	if (ue_valida_null(opener.document.form1.cmbcodpainac))
	{
		function onCambioPais(respuesta)
		{
			var estados=JSON.parse(respuesta.responseText);
			for (j=0; j<estados.codest.length; j++)
			{
				opener.document.form1.cmbcodestnac.options[opener.document.form1.cmbcodestnac.options.length] = new Option(estados.desest[j],estados.codest[j]);
			}
			//El siguiente if es usado cuando viene del catalogo	  
			if (trim(opener.document.form1.hidcodestnac.value) != "")
			{
				opener.document.form1.cmbcodestnac.value = opener.document.form1.hidcodestnac.value;
				opener.document.form1.hidcodestnac.value = "";
			}
		}
		params = "operacion=ue_inicializarestado&codpai="+ls_codpainac;
		new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onCambioPais});
	}  
}

function aceptar_persona1(ls_codper,ls_codperdestino,ls_cedper,ls_cedperdestino,ls_nomper,ls_nomperdestino,ls_apeper,ls_apeperdestino,
						  ls_codtippersss,ls_codtippersssdestino,ls_dentippersss,ls_dentippersssdestino,ls_cargo,ls_caractdestino,
						  ls_desuniadm,ls_uniadmdestino,ls_fecingadmpubper,ls_fecingadmpubperdestino,ls_anoservpreper,ls_anoservpreperdestino,
						  ls_fecingper,ls_fecingperdestino,ls_fecjubper,ls_fecjubperdestino,ls_fecfevid,ls_fecfeviddestino,dias,ls_diadestino,
						  meses,ls_mesdestino,anos,ls_anodestino,ls_fecnacper,ls_fecnacperdestino,ls_codpai,ls_codpaidestino,ls_codest,
						  ls_codestdestino,ls_codmun,ls_codmundestino,ls_codpar,ls_codpardestino,ls_dirper,ls_dirperdestino,ls_telhabper,
						  ls_telhabperdestino,ls_telmovper,ls_telmovperdestino,ls_codpainac,ls_codpainacdestino,ls_codestnac,
						  ls_codestnacdestino,li_prirem,ls_priremdestino,li_ultrem,ls_ultremdestino,li_porpen,ls_porpendestino,li_monpen,
						  ls_monpendestino,ls_tipjub,ls_tipjubdestino,ls_situacion,ls_situaciondestino,ls_fotper)
{
	opener.document.form1.txtcodigo.value=ls_codper;
	opener.document.form1.txtdescripcion.value=ls_dentippersss;
	obj0=eval("opener.document.form1."+ls_codperdestino);
	obj0.value=ls_codper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_cedperdestino);
	obj0.value=ls_cedper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_nomperdestino);
	obj0.value=ls_nomper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_apeperdestino);
	obj0.value=ls_apeper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_codtippersssdestino);
	obj0.value=ls_codtippersss;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_dentippersssdestino);
	obj0.value=ls_dentippersss;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_caractdestino);
	obj0.value=ls_cargo;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_uniadmdestino);
	obj0.value=ls_desuniadm;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_fecingadmpubperdestino);
	obj0.value=ls_fecingadmpubper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_anoservpreperdestino);
	obj0.value=ls_anoservpreper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_fecingperdestino);
	obj0.value=ls_fecingper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_fecjubperdestino);
	obj0.value=ls_fecjubper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_fecfeviddestino);
	obj0.value=ls_fecfevid;		
	obj0=eval("opener.document.form1."+ls_diadestino);
	obj0.value=dias;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_mesdestino);
	obj0.value=meses;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_anodestino);
	obj0.value=anos;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_fecnacperdestino);
	obj0.value=ls_fecnacper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_codpaidestino);
	obj0.value=ls_codpai;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_dirperdestino);
	obj0.value=ls_dirper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_telhabperdestino);
	obj0.value=ls_telhabper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_telmovperdestino);
	obj0.value=ls_telmovper;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_codpainacdestino);
	obj0.value=ls_codpainac;		
	obj0.readOnly=true;		
	obj0=eval("opener.document.form1."+ls_priremdestino);
	obj0.value=li_prirem;		
	obj0=eval("opener.document.form1."+ls_ultremdestino);
	obj0.value=li_ultrem;		
	obj0=eval("opener.document.form1."+ls_porpendestino);
	obj0.value=li_porpen;		
	obj0=eval("opener.document.form1."+ls_monpendestino);
	obj0.value=li_monpen;		
	obj0=eval("opener.document.form1."+ls_tipjubdestino);
	obj0.value=ls_tipjub;	
	obj0=eval("opener.document.form1."+ls_situaciondestino);
	obj0.value=ls_situacion;		
	obj0.readOnly=true;		
	foto=opener.document.getElementById('foto');
	foto.src="";
	if ((ls_fotper=="")||(ls_fotper=="blanco.jpg"))
	{
		foto.src="../../../fotos/silueta.jpg";
	}
	else
	{
		foto.src="../../../../sno/fotospersonal/"+ls_fotper;	
	}
    ls_ejecucion = document.form1.hidstatus.value;
    if(ls_ejecucion=="1")
	{
		opener.document.form1.hidguardar.value = "modificar";
		opener.document.form1.hidstatus.value="C";
	}
	else
	{
		opener.document.form1.hidguardar.value = "insertar";	
		opener.document.form1.hidstatus.value="";
	}
	opener.document.form1.hidcodest.value=ls_codest;
	opener.document.form1.hidcodpar.value=ls_codpar;
	opener.document.form1.hidcodmun.value=ls_codmun;
	opener.document.form1.hidcodestnac.value=ls_codestnac;
	ue_cambiopais (ls_codpai, ls_codest, ls_codmun);	
	ue_cambiopaisnac (ls_codpainac);
	setTimeout(close,7500);
}

function nextPAge(val)
{
	grid.clearAll(); //clear existing data
	grid.loadXML("some_url.php?page="+val);
}