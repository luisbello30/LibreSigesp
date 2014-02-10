
// JavaScript Document

var url    = '../../php/sigesp_srh_a_jubilado.php';
var params = 'operacion';
var metodo = 'get';
var img="<img src=\"../../../public/imagenes/progress.gif\"> ";
var acordion;

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//FUNCIÓN PARA INICIALIZAR

function ue_inicializar()
{
  function onInicializar(respuesta)
  {
	 if (trim(respuesta.responseText) != "")
	 {  
		ue_cancelar();
	     var respuestas = respuesta.responseText.split('&');
	     num_respuesta = -1;
		 //Paises
	     num_respuesta++;
		if (trim(respuestas[num_respuesta]) != "")
		{
			var pais = JSON.parse(respuestas[num_respuesta]);
			for (i=0; i<pais.despai.length; i++)
			{
			  $('cmbcodpai').options[$('cmbcodpai').options.length] = new Option(pais.despai[i],pais.codpai[i]);
			}
			
			
			for (i=0; i<pais.despai.length; i++)
			{
			  $('cmbcodpainac').options[$('cmbcodpainac').options.length] = new Option(pais.despai[i],pais.codpai[i]);
			}
		}
	 }
			
  }	

  params = "operacion=ue_inicializar";
  new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onInicializar});
 }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//FUNCIONES PARA EL MANEJO DE LOS COMBOS

function LimpiarComboPais()
{
  $('cmbcodpai').value="null";	
  $('cmbcodpai').selectedIndex = 0;
  LimpiarComboEstado();
}

function LimpiarComboEstado()
{
  removeAllOptions($('cmbcodest'));	
  $('cmbcodest').selectedIndex = 0;
  LimpiarComboMunicipio();
}

function LimpiarComboEstadoNac()
{
  removeAllOptions($('cmbcodestnac'));	
  $('cmbcodestnac').selectedIndex = 0;
}

function LimpiarComboMunicipio()
{
  removeAllOptions($('cmbcodmun'));	
  $('cmbcodmun').selectedIndex = 0;
  LimpiarComboParroquia();
}

function LimpiarComboParroquia()
{
  removeAllOptions($('cmbcodpar'));
  $('cmbcodpar').selectedIndex = 0;
}

function ue_valida_combopais () 
{
f= document.form1;
if (f.cmbcodpai.value =="null")
  { alert ('Debe seleccionar un Pais');   
  }
}

function ue_valida_combopaisnac () 
{
f= document.form1;
if (f.cmbcodpainac.value =="null")
  { alert ('Debe seleccionar un Pais de Nacimiento');   }
}

function ue_valida_cmbcodmun () {

f= document.form1;
if (f.cmbcodest.value =="null") 
  {alert ('Debe seleccionar un Estado');   }

}

function ue_valida_cmbcodpar () {

f= document.form1;
if (f.cmbcodmun.value =="null")
  { alert ('Debe seleccionar un Municipio');   }
 
}

function ue_CambioPais()
{
  function onInicializar(respuesta)
  {
	if (trim(respuesta.responseText) != "")
	{
	  var estados = JSON.parse(respuesta.responseText);
	  for (i=0; i<estados.desest.length; i++)
	  {$('cmbcodest').options[$('cmbcodest').options.length] = new Option(estados.desest[i],estados.codest[i]);}
	}
  }	
  LimpiarComboEstado();
  params = "operacion=ue_inicializarestado&codpai="+$('cmbcodpai').value;
  new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onInicializar});
}

function ue_CambioPaisNac()
{
  function onInicializar(respuesta)
  {
	if (trim(respuesta.responseText) != "")
	{
	  var estados = JSON.parse(respuesta.responseText);
	  for (i=0; i<estados.desest.length; i++)
	  {$('cmbcodestnac').options[$('cmbcodestnac').options.length] = new Option(estados.desest[i],estados.codest[i]);}
	}
  }	
  LimpiarComboEstadoNac();
  params = "operacion=ue_inicializarestado&codpai="+$('cmbcodpainac').value;
  new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onInicializar});
}

function ue_CambioEstado()
{
  function onInicializar(respuesta)
  {
	if (trim(respuesta.responseText) != "")
	{
	  var municipios = JSON.parse(respuesta.responseText);
	  for (i=0; i<municipios.codmun.length; i++)
	  {$('cmbcodmun').options[$('cmbcodmun').options.length] = new Option(municipios.denmun[i],municipios.codmun[i]);}
	}
  }	
  LimpiarComboMunicipio();
  params = "operacion=ue_inicializarmunicipio&codpai="+$('cmbcodpai').value+"&codest="+$('cmbcodest').value;
  new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onInicializar});
}

function ue_CambioMunicipio()
{
  function onInicializar(respuesta)
  {
	if (trim(respuesta.responseText) != "")
	{
	  var parroquias = JSON.parse(respuesta.responseText);
	  for (i=0; i<parroquias.codpar.length; i++)
	  {$('cmbcodpar').options[$('cmbcodpar').options.length] = new Option(parroquias.denpar[i],parroquias.codpar[i]);}
	}
  }	
  LimpiarComboParroquia();
  params = "operacion=ue_inicializarparroquia&codpai="+$('cmbcodpai').value+"&codest="+$('cmbcodest').value+"&codmun="+$('cmbcodmun').value;
  new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onInicializar});
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// FUNCIONES PARA GUARDAR REGISTROS 

function ue_guardar()
{
	lb_valido = true;
	la_objetos =new Array ("txtcodper","txtfecfevid","txtprirem","txtultrem","txtporpen","txtmonpen");
	la_mensajes=new Array ("el Codigo","la Fecha de Fe de Vida","La Primera Remuneracion","La ultima remuneracion","el porcentaje de pension","el monto de pension");
	lb_valido = valida_datos_llenos(la_objetos,la_mensajes);
	if(lb_valido)
	{	
		divResultado = document.getElementById('mostrar');
		divResultado.innerHTML= img;
		function onGuardar(respuesta)
		{
			alert(respuesta.responseText);   
			divResultado = document.getElementById('mostrar');
			divResultado.innerHTML= "";
			ue_cancelar();
		}
		var personal = 
		{
			"codper"   : $F('txtcodper'),
			"nomper"   : $F('txtnomper')+ " " +$F('txtapeper'),
			"fecfevid" : $F('txtfecfevid'),
			"prirem"   : $F('txtprirem'),
			"ultrem"   : $F('txtultrem'),
			"porpen"   : $F('txtporpen'),
			"monpen"   : $F('txtmonpen'),
			"tipjub"   : $F('cmbtipjub')
		};
		var objeto = JSON.stringify(personal);	 
		params = "operacion=ue_guardar&objeto="+objeto+"&insmod="+$F('hidguardar');
		new Ajax.Request(url,{method:'post',parameters:params,onComplete:onGuardar});
	}
}

function ue_cancelar()
{
     ue_nuevo();
    scrollTo(0,0);
   
}
 
function ue_nuevo()
{
	LimpiarComboPais();
	$('txtcodper').readOnly=false;
	$('txtcedper').readOnly=false;
	$('txtcodper').value="";
	$('txtcedper').value="";
	$('txtnomper').value="";
	$('txtapeper').value="";
	$('txtdirper').value="";
	$('txtcaract').value="";
	$('txtuniadm').value="";	 
	$('txtfecnacper').value="";
	$('txttelhabper').value="";
	$('txttelmovper').value="";
	$('txtcodtippersss').value="";
	$('txtdestippersss').value="";
	$('cmbcodpainac').value="null";
	$('cmbcodestnac').value="null";
	$('txtfecingadmpub').value="";
	$('txtanoservpreper').value="0";
	$('txtfecingper').value="";
	$('txtfecjubper').value="";
	$('txtfecfevid').value="";
	$('txtdia').value="0";
	$('txtmes').value="0";
	$('txtano').value="0";
	$('hidguardar').value="incluir";
	$('txtprirem').value="0";
	$('txtultrem').value="0";
	$('txtporpen').value="0";
	$('txtmonpen').value="0";
	$('cmbtipjub').value="";
	$('cmbsituacion').value="";

	foto=document.getElementById('foto');
	foto.src="";
	foto.src="../../../fotos/silueta.jpg";
	divResultado = document.getElementById('mostrar');
	divResultado.innerHTML= "";
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//FUNCIONES PARA BUSCAR EN CÁTALOGOS
function ue_buscar()
{
	window.open("../catalogos/sigesp_srh_cat_jubilado.php?valor_cat=1&tipo=1","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	ue_limpiar_beneficiario ();
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);


function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	//window.open("../hlp/index.php?sistema=SNO&subsistema=SNR&nomfis=sno/sigesp_hlp_snr_personal.php","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//FUNCIONES PARA EL MANEJO DE LOS BENEFICIARIOS

function ue_buscar_beneficiarios()
{
	
	if ($('txtcodper').value=='') 
	{ 
		alert ('Debe seleccionar un personal del catalogo');		
	}
	else 
	{ 
		codper= $('txtcodper').value;
		window.open("../catalogos/sigesp_srh_cat_beneficiarios.php?codper="+codper,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
}

function ue_guardar_beneficiarios()
{
	lb_valido = true; 
	if ($('txtcodper').value =='')
	{ 
		alert ('El código de personal no puede estar vacío. Seleccione un personal del catalogo.');
		lb_valido= false;
	}
	if(lb_valido)
	{
		$('cmbtipben').disabled="";
		la_objetos =new Array ("txtcedben", "txtnomben", "txtapeben", "cmbnacben",  "cmbtipben" ,"cmbforpagben");
		la_mensajes=new Array ("la cedula del beneficiario","el nombre del beneficiario","el apellido del beneficiario","la nacionalidad del beneficiario", "el tipo de beneficiario","la forma de pago");
		lb_valido = valida_datos_llenos(la_objetos,la_mensajes);
		if (lb_valido)
		{
			divResultado = document.getElementById('mostrar');
			divResultado.innerHTML= img;
			function onGuardar(respuesta)
			{
				alert(respuesta.responseText); 
				divResultado = document.getElementById('mostrar');
				divResultado.innerHTML= "";
				ue_limpiar_beneficiario ();
				ue_nuevo_beneficiario();
			}	
			var beneficiario =
				{
					"codper"   	: $F('txtcodper'),
					"codben"   	: $F('txtcodben'),
					"cedben"		: $F('txtcedben'),
					"nomben"		: $F('txtnomben'),
					"apeben"		: $F('txtapeben'),
					"nacben"		: $F('cmbnacben'),
					"dirben"		: $F('txtdirben'),
					"telben"		: $F('txttelben'),
					"tipben"   	: $F('cmbtipben'),
					"porpagben"	: $F('txtporpagben'),
					"monpagben"	: $F('txtmonpagben'),
					"forpagben"	: $F('cmbforpagben'),
					"nomcheben"   : $F('txtnomcheben'),
					"codban"		: $F('txtcodban'),
					"ctaban"		: $F('txtctaban'),
					"cedaut"		: $F('txtcedaut'),
					"nexben"		: $F('cmbnexben'),
					"tipcueben"	: $F('cmbtipcueben'),
					"numexpben"   : $F('txtnumexpben'),
				}
			var objeto4 = JSON.stringify(beneficiario);
			$aux = "insertar";	
			params = "operacion=ue_guardar_beneficiario&objeto4="+objeto4+"&insmod="+$F('hidguardar_ben')
			new Ajax.Request(url,{method:'post',parameters:params,onComplete:onGuardar});	  
		}
	}
}

function ue_limpiar_beneficiario ()
{
	$('txtcodben').value="";
	$('txtcedben').value="";
	$('txtnomben').value="";
	$('txtapeben').value="";
	$('cmbnacben').value="null";
	$('txtdirben').value="";
	$('txttelben').value="";
	$('cmbtipben').value="null";
	$('txtporpagben').value="";
	$('txtmonpagben').value="";
	$('cmbforpagben').value="null";
	$('txtnomcheben').value="";
	$('txtcodban').value="";
	$('txtnomban').value="";
	$('txtctaban').value="";
	$('txtcedaut').value="";
	$('cmbnexben').value="-";
	$('cmbtipcueben').value="null";
	$('txtnumexpben').value="";
	$('cmbtipben').disabled="";
	$('hidguardar_ben').value="insertar";
	scrollTo(0,0);   
}

function ue_nuevo_beneficiario()
{
	function onNuevo(respuesta)
	{
		$('txtcodben').value  = trim(respuesta.responseText);
		$('txtcodben').focus();
	}
	var codper = $F('txtcodper');
	params = "operacion=ue_nuevo_beneficiario&codper="+codper;
	new Ajax.Request(url,{method:'get',parameters:params,onComplete:onNuevo});
}  

function ue_buscarbanco()
{
	window.open("../../../../sno/sigesp_snorh_cat_banco.php?tipo=beneficiario","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_limpiar(tipo)
{
	f=document.form1;
	if(tipo=="0")
	{
		if(parseFloat(f.txtporpagben.value)>0)
		{
			f.txtmonpagben.value="0,00";
		}
	}
	if(tipo=="1")
	{
		if(parseFloat(f.txtmonpagben.value)>0)
		{
			f.txtporpagben.value="0,00";
		}
	}
}

//FUNCIONES PARA EL CALENDARIO

// Esta es la funcion que detecta cuando el usuario hace click en el calendario, necesaria
function selected(cal, date) {
  cal.sel.value = date; // just update the date in the input field.
                           
  if (cal.dateClicked )
      cal.callCloseHandler();
}

function closeHandler(cal) {
  cal.hide();                        // hide the calendar

  _dynarch_popupCalendar = null;
}

function showCalendar(id, format, showsTime, showsOtherMonths) {
  var el = document.getElementById(id);
  if (_dynarch_popupCalendar != null) {
    // we already have some calendar created
    _dynarch_popupCalendar.hide();                 // so we hide it first.
  } else {
    // first-time call, create the calendar.

    var cal = new Calendar(1, null, selected, closeHandler);
    if (typeof showsTime == "string") {
      cal.showsTime = true;
      cal.time24 = (showsTime == "24");
    }
    if (showsOtherMonths) {
      cal.showsOtherMonths = true;
    }
    _dynarch_popupCalendar = cal;                  // remember it in the global var
    cal.setRange(1900, 2070);        // min/max year allowed.
    cal.create();
  }
  _dynarch_popupCalendar.setDateFormat(format);    // set the specified date format
  _dynarch_popupCalendar.parseDate(el.value);      // try to parse the text in field
  _dynarch_popupCalendar.sel = el;                 // inform it what input field we use
 _dynarch_popupCalendar.showAtElement(el, "T");        // show the calendar

  return false;
}
