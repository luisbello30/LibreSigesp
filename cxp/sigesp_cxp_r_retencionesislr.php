<?php
	session_start();
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "location.href='../sigesp_inicio_sesion.php'";
		print "</script>";		
	}
	$ls_logusr=$_SESSION["la_logusr"];
        $ls_ano=substr($_SESSION['la_empresa']['periodo'],0,4); //date('Y'); 
	require_once("class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$io_fun_cxp->uf_load_seguridad("CXP","sigesp_cxp_r_retencionesislr.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_reporte=$io_fun_cxp->uf_select_config("CXP","REPORTE","FORMATO_ISLR","sigesp_cxp_rpp_retencionislr.php","C");
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$ls_estretiva=$_SESSION["la_empresa"]["estretiva"];
	if($ls_estretiva!="C")
	{
		print "<script language=JavaScript>";
		print "alert('La impresion de las retenciones de ISLR esta configurada por el modulo de Bancos');";
		print "location.href='sigespwindow_blank.php'";
		print "</script>";		
	
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Reporte de Retenciones I.S.L.R.</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript" language="JavaScript1.2" src="js/stm31.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="js/funcion_cxp.js"></script>

<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<link href="css/cxp.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/validaciones.js"></script>
<script language="javascript" src="js/funcion_cxp.js"></script>
<script language="javascript">
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey))
		{
			window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ return false;} 
		} 
	}
</script>
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:hover {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style></head>
<body>
<?php

	require_once("class_folder/sigesp_cxp_c_cmp_islr.php");
	$io_cxp=new sigesp_cxp_c_cmp_islr("../");
	$ls_operacion=$io_fun_cxp->uf_obteneroperacion();
	switch($ls_operacion)
	{
		case"PROCESAR":
			$ls_comprobantes=$io_fun_cxp->uf_obtenervalor("comprobantes","");
			$ls_procedencias=$io_fun_cxp->uf_obtenervalor("procedencias","");
			$lb_valido=$io_cxp->uf_procesar_comprobantes_islr($ls_comprobantes,$ls_procedencias,$la_seguridad);
			
		break;
	}

?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="806" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			
          <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Cuentas por Pagar </td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	  <tr>
	  	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	    <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </table>    </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td class="toolbar" width="25">
        <div align="center"><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" width="20" height="20" border="0" title="Procesar"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_print();"><img src="../shared/imagebank//////tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0" title="Imprimir"></a></div></td>
    <td class="toolbar" width="20"><div align="center"><a href="javascript: ue_openexcel();">
                <img src="../shared/imagebank/tools20/excel.jpg" alt="excel" title="excel" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="20"><div align="center"><a href="javascript: ue_openxml();"><img src="../shared/imagebank//tools20/xml.png" alt="XML" title="XML" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" title="Ayuda"></a></div></td>
    <td class="toolbar" width="25">&nbsp;</td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
</div> 
<p>&nbsp;	</p>
<form name="formulario" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_cxp->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_cxp);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="573"></td>
    </tr>
    <tr class="titulo-ventana">
      <td height="22" colspan="3" align="center">Reporte de Retenciones de I.S.L.R. </td>
    </tr>
    <tr style="visibility:hidden">
      <td height="22" colspan="3" align="center"><div align="left">Reporte en
          <select name="cmbbsf" id="cmbbsf">
            <option value="0" selected>Bs.</option>
            <option value="1">Bs.F.</option>
          </select>
</div></td>
    </tr>
     <tr>
      <td height="22" colspan="3" align="center">
          <table width="550" border="0" cellspacing="0" class="formato-blanco">

               <tr>
      <td height="22" align="center"><div align="right">Mes</div></td>
      <td width="208" height="22" align="center"><div align="left">
        <label>
        <select name="cmbmes" id="cmbmes">
          <option value="01" <?php if($ls_mes=="01"){ print "selected";} ?>>ENERO</option>
          <option value="02" <?php if($ls_mes=="02"){ print "selected";} ?>>FEBRERO</option>
          <option value="03" <?php if($ls_mes=="03"){ print "selected";} ?>>MARZO</option>
          <option value="04" <?php if($ls_mes=="04"){ print "selected";} ?>>ABRIL</option>
          <option value="05" <?php if($ls_mes=="05"){ print "selected";} ?>>MAYO</option>
          <option value="06" <?php if($ls_mes=="06"){ print "selected";} ?>>JUNIO</option>
          <option value="07" <?php if($ls_mes=="07"){ print "selected";} ?>>JULIO</option>
          <option value="08" <?php if($ls_mes=="08"){ print "selected";} ?>>AGOSTO</option>
          <option value="09" <?php if($ls_mes=="09"){ print "selected";} ?>>SEPTIEMBRE</option>
          <option value="10" <?php if($ls_mes=="10"){ print "selected";} ?>>OCTUBRE</option>
          <option value="11" <?php if($ls_mes=="11"){ print "selected";} ?>>NOVIEMBRE</option>
          <option value="12" <?php if($ls_mes=="12"){ print "selected";} ?>>DICIEMBRE</option>
        </select>
        </label>
</div></td>
      <td width="66" height="22" align="center"><div align="right">A&ntilde;o</div></td>
      <td width="182" align="center"><div align="left">
        <label>
        <input name="txtano" type="text" id="txtano" value="<?php print $ls_ano;?>" size="6" maxlength="4" readonly>
        </label>
</div></td>
    </tr>
    
          </table>


      </td>
     </tr>
    <tr>
      <td height="22" colspan="3" align="center">

          <table width="550" border="0" cellspacing="2" class="formato-blanco">
        <tr>
          <td width="220" height="22"><div align="right">
            <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();" checked>
            Todos</div></td>
          <td width="89" height="22"><div align="center">
                <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();">
            Proveedor</div></td>
          <td width="215" height="22"><div align="left">
            <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();">
          Beneficiario</div></td>
        </tr>
        <tr>
          <td height="22"><div align="left">Desde
            <input name="txtcoddes" type="text" id="txtcoddes" size="20" readonly>
              <a href="javascript: ue_catalogo_proben('REPDES');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">Hasta</div></td>
          <td><input name="txtcodhas" type="text" id="txtcodhas" size="20" readonly>
            <a href="javascript: ue_catalogo_proben('REPHAS');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></td>
        </tr>
        <tr>
          <td height="22"><div align="left">Solicitud
            <input name="txtnumsoldes" type="text" id="txtnumsoldes" size="20">
              <a href="javascript: ue_catalogo_solicitud();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

      </table></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="33" colspan="3" align="center">      <div align="left">
        <table width="511" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td height="22" colspan="5"><strong>Rango de Fecha </strong></td>
            </tr>
          <tr>
            <td width="136"><div align="right">Desde</div></td>
            <td width="101"><input name="txtfecdes" type="text" id="txtfecdes"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" size="15" maxlength="10"  datepicker="true"></td>
            <td width="42"><div align="right">Hasta</div></td>
            <td width="129"><div align="left">
                <input name="txtfechas" type="text" id="txtfechas"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" size="15" maxlength="10"  datepicker="true">
            </div></td>
            <td width="101">&nbsp;</td>
          </tr>
        </table>
      </div></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center"><div align="right"><a href="javascript:ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" width="20" height="20" border="0">Buscar Documentos</a></div></td>
    </tr>
    <tr>
      <td colspan="3" align="center">
  		<div id="resultados" align="center"></div>	</td>
    </tr>
  </table>
	<input name="total" type="hidden" id="total" value="<?php print $totrow;?>">
    <input name="formato"    type="hidden" id="formato"    value="<?php print $ls_reporte; ?>">
    <input name="basdatcmp" type="hidden" id="basdatcmp" value="<?php print $_SESSION["la_empresa"]["basdatcmp"]; ?>">
    <input name="operacion" type="hidden" id="operacion">
    <input name="comprobantes" type="hidden" id="comprobantes">
    <input name="procedencias" type="hidden" id="procedencias">
</form>      
</body>
<script language="JavaScript">
var patron = new Array(2,2,4)
var patron2 = new Array(1,3,3,3,3)
function ue_limpiarproben()
{
	f=document.formulario;
	f.txtcoddes.value="";
	f.txtcodhas.value="";
}
function ue_catalogo_solicitud()
{
	tipo="REPDES";
	window.open("sigesp_cxp_cat_solicitudpago.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=630,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_catalogo_proben(ls_tipo)
{
	f=document.formulario;
	valido=true;		
	if(f.rdproben[0].checked)
	{
		valido=false;
	}
	if(f.rdproben[1].checked)
	{
		ls_catalogo="sigesp_cxp_cat_proveedor.php?tipo="+ls_tipo+"";
	}
	if(f.rdproben[2].checked)
	{
		ls_catalogo="sigesp_cxp_cat_beneficiario.php?tipo="+ls_tipo+"";
	}
	if(valido)
	{		
		window.open(ls_catalogo,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("Debe indicar si es Proveedor � Beneficiario");
	}
}
function ue_procesar()
{
	f=document.formulario;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{
		basdatcmp=f.basdatcmp.value;
		if(basdatcmp!="")
		{
			ls_comprobantes="";
			ls_procedencias="";
			totrow=ue_calcular_total_fila_local("txtnumero");
			f.total.value=totrow;
			ls_tiporeporte=f.cmbbsf.value;
			for(li_i=1;li_i<=totrow;li_i++)
			{
				if(eval("f.checkcmp"+li_i+".checked==true"))
				{
					ls_documento=eval("f.txtnumero"+li_i+".value");
					ls_procede=eval("f.txtprocede"+li_i+".value");
					if(ls_comprobantes.length>0)
					{
						ls_comprobantes = ls_comprobantes+"<<<"+ls_documento;
					}
					else
					{
						ls_comprobantes = ls_documento;
					}
					if(ls_procedencias.length>0)
					{
						ls_procedencias = ls_procedencias+"<<<"+ls_procede;
					}
					else
					{
						ls_procedencias = ls_procede;
					}
				}
			}
			if(ls_comprobantes!="")
			{
				f.operacion.value="PROCESAR";
				f.comprobantes.value=ls_comprobantes;
				f.procedencias.value=ls_procedencias;
				f.action="sigesp_cxp_r_retencionesislr.php";
				f.submit();
			}
			else
			{
				alert("Debe seleccionar al menos un N�mero de Documento.");	   
			}	  
		}
		else
		{
			alert("Ya ud. se encuentra en una Base de Datos consolidadora");
		}
		
	}
	else
	{
		alert("No tiene permiso para realizar esta operaci�n");
	}
}	
function ue_print()
{
	f=document.formulario;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{
		ls_comprobantes="";
		totrow=ue_calcular_total_fila_local("txtnumcom");
		f.total.value=totrow;
		ls_tiporeporte=f.cmbbsf.value;
		for(li_i=1;li_i<=totrow;li_i++)
		{
			if(eval("f.checkcmp"+li_i+".checked==true"))
			{
				ls_documento=eval("f.txtnumcom"+li_i+".value");
				if(ls_comprobantes.length>0)
				{
					ls_comprobantes = ls_comprobantes+"-"+ls_documento;
				}
				else
				{
					ls_comprobantes = ls_documento;
				}
			}
		}
		if(ls_comprobantes!="")
		{
			tiporeporte=f.cmbbsf.value;
			formato=f.formato.value;
			pagina="reportes/"+formato+"?comprobantes="+ls_comprobantes+"&tiporeporte="+tiporeporte;
			window.open(pagina,"reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no,left=0,top=0");
		}
		else
		{
			alert("Debe seleccionar al menos un N�mero de Documento.");	   
		}	  
	}
	else
	{
		alert("No tiene permiso para realizar esta operaci�n");
	}
}

function ue_opencsv()
{
	f=document.formulario;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{
		ls_comprobantes="";
		totrow=ue_calcular_total_fila_local("txtnumcom");
		f.total.value=totrow;
		ls_tiporeporte=f.cmbbsf.value;
		for(li_i=1;li_i<=totrow;li_i++)
		{
			if(eval("f.checkcmp"+li_i+".checked==true"))
			{
				ls_documento=eval("f.txtnumcom"+li_i+".value");
				if(ls_comprobantes.length>0)
				{
					ls_comprobantes = ls_comprobantes+"-"+ls_documento;
				}
				else
				{
					ls_comprobantes = ls_documento;
				}
			}
		}
		if(ls_comprobantes!="")
		{
			tiporeporte=f.cmbbsf.value;
			formato="sigesp_cxp_rpp_retencionislr_csv.php";
			pagina="reportes/"+formato+"?comprobantes="+ls_comprobantes+"&tiporeporte="+tiporeporte;
			window.open(pagina,"reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no,left=0,top=0");
		}
		else
		{
			alert("Debe seleccionar al menos un N�mero de Documento.");	   
		}	  
	}
	else
	{
		alert("No tiene permiso para realizar esta operaci�n");
	}
}

function ue_openxml()
{
        f=document.formulario;
	li_imprimir=f.imprimir.value;
        ls_comprobantes="";
        totrow=ue_calcular_total_fila_local("txtnumcom");
        f.total.value=totrow;
        ls_tiporeporte=f.cmbbsf.value;
        for(li_i=1;li_i<=totrow;li_i++)
        {
                if(eval("f.checkcmp"+li_i+".checked==true"))
                {
                        ls_documento=eval("f.txtnumcom"+li_i+".value");
                        if(ls_comprobantes.length>0)
                        {
                                ls_comprobantes = ls_comprobantes+"-"+ls_documento;
                        }
                        else
                        {
                                ls_comprobantes = ls_documento;
                        }
                }
        }
        if(ls_comprobantes!="")
        {
                anio = f.txtano.value;
                mes = f.cmbmes.value;
                tiporeporte=f.cmbbsf.value;
                formato="sigesp_cxp_rpp_retencionislr_xml.php";
                pagina="reportes/"+formato+"?comprobantes="+ls_comprobantes+"&tiporeporte="+tiporeporte+"&anio="+anio+"&mes="+mes;
                window.open(pagina,"reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no,left=0,top=0");
        }
        else
        {
                alert("Debe seleccionar al menos un N�mero de Documento.");
        }
}


function ue_openexcel()
{
        f=document.formulario;
	li_imprimir=f.imprimir.value;
        ls_comprobantes="";
        totrow=ue_calcular_total_fila_local("txtnumcom");
        f.total.value=totrow;
        ls_tiporeporte=f.cmbbsf.value;
        for(li_i=1;li_i<=totrow;li_i++)
        {
                if(eval("f.checkcmp"+li_i+".checked==true"))
                {
                        ls_documento=eval("f.txtnumcom"+li_i+".value");
                        if(ls_comprobantes.length>0)
                        {
                                ls_comprobantes = ls_comprobantes+"-"+ls_documento;
                        }
                        else
                        {
                                ls_comprobantes = ls_documento;
                        }
                }
        }
        if(ls_comprobantes!="")
        {
                anio = f.txtano.value;
                mes = f.cmbmes.value;
                tiporeporte=f.cmbbsf.value;
                formato="sigesp_cxp_rpp_retencionislr_excel.php";
                pagina="reportes/"+formato+"?comprobantes="+ls_comprobantes+"&tiporeporte="+tiporeporte+"&anio="+anio+"&mes="+mes;
                window.open(pagina,"reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no,left=0,top=0");
        }
        else
        {
                alert("Debe seleccionar al menos un N�mero de Documento.");
        }
}

function ue_search()
{
	f=document.formulario;
	// Cargamos las variables para pasarlas al AJAX
	if(f.rdproben[0].checked)
	{
		tipproben="";
	}
	else
	{
		if(f.rdproben[1].checked)
		{
			tipproben="P";
		}
		else
		{
			tipproben="B";
		}
	}
	codprobendes=f.txtcoddes.value;
	codprobenhas=f.txtcodhas.value;
	fecdes=f.txtfecdes.value;
	fechas=f.txtfechas.value;
	basdatcmp=f.basdatcmp.value;
	numsol=f.txtnumsoldes.value;
        anio = f.txtano.value;
        mes = f.cmbmes.value;
        
	// Div donde se van a cargar los resultados
	divgrid = document.getElementById('resultados');
	// Instancia del Objeto AJAX
	ajax=objetoAjax();
	// Pagina donde est�n los m�todos para buscar y pintar los resultados
	ajax.open("POST","class_folder/sigesp_cxp_c_catalogo_ajax.php",true);
	ajax.onreadystatechange=function(){
		if(ajax.readyState==1)
		{
			divgrid.innerHTML = "<img src='imagenes/loading.gif' width='350' height='200'>";//<-- aqui iria la precarga en AJAX 
		}
		else
		{
			if(ajax.readyState==4)
			{
				if(ajax.status==200)
				{//mostramos los datos dentro del contenedor
					divgrid.innerHTML = ajax.responseText
				}
				else
				{
					if(ajax.status==404)
					{
						divgrid.innerHTML = "La p�gina no existe";
					}
					else
					{//mostramos el posible error     
						divgrid.innerHTML = "Error:".ajax.status;
					}
				}
				
			}
		}
	}	
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	// Enviar todos los campos a la pagina para que haga el procesamiento
	ajax.send("catalogo=RETENCIONESISLR&tipproben="+tipproben+"&codprobendes="+codprobendes+"&codprobenhas="+codprobenhas+
			  "&fecdes="+fecdes+"&fechas="+fechas+"&basdatcmp="+basdatcmp+"&numsol="+numsol+"&anio="+anio+"&mes="+mes);
}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

function uf_checkall()
{
	f=document.formulario;
	totrow=ue_calcular_total_fila_local("txtnumcom");
	f.total.value=totrow;
	if(f.checkall.checked==true)
	{
		for(li_i=1;li_i<=totrow;li_i++)
		{
			eval("f.checkcmp"+li_i+".checked=true");
		}
	}
	else
	{
		for(li_i=1;li_i<=totrow;li_i++)
		{
			eval("f.checkcmp"+li_i+".checked=false");
		}
	}
}
</script>
</html>
