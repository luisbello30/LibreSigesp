<?Php
session_start();
if (!array_key_exists("la_logusr",$_SESSION))
   {
	 print "<script language=JavaScript>";
	 print "location.href='sigesp_inicio_sesion.php'";
	 print "</script>";		
   }
$ls_logusr=$_SESSION["la_logusr"];
require_once("class_funciones_banco.php");
$io_fun_banco= new class_funciones_banco();
$io_fun_banco->uf_load_seguridad("SCB","sigesp_scb_r_colocaciones.php",$ls_permisos,$la_seguridad,$la_permisos);
$li_diasem = date('w');
switch ($li_diasem){
  case '0': $ls_diasem='Domingo';
  break; 
  case '1': $ls_diasem='Lunes';
  break;
  case '2': $ls_diasem='Martes';
  break;
  case '3': $ls_diasem='Mi&eacute;rcoles';
  break;
  case '4': $ls_diasem='Jueves';
  break;
  case '5': $ls_diasem='Viernes';
  break;
  case '6': $ls_diasem='S&aacute;bado';
  break;
}  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Estado de Cuenta Colocaciones</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript" language="JavaScript1.2" src="js/stm31.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
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
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr> 
    <td height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
   <td width="778" height="20" colspan="11" bgcolor="#E7E7E7">
    <table width="778" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Caja y Banco</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table>
  </td>
  </tr>
  <tr>
    <td height="20" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_search();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a><a href="javascript:ue_openexcel();"><img src="../shared/imagebank/tools20/excel.jpg" alt="Excel" title="Excel" width="20" height="20" border="0"></a><a href="sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" title="Ayuda" width="20" height="20"></td>
  </tr>
</table>
  <?Php
require_once("../shared/class_folder/grid_param.php");
require_once("../shared/class_folder/sigesp_include.php");

$io_grid	= new grid_param();
$io_include = new sigesp_include();
$ls_conect  = $io_include->uf_conectar();

$la_emp=$_SESSION["la_empresa"];
if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ld_fecha=$_POST["txtfecha"];
	
	$ls_codbandes=$_POST["txtcodbandes"];
	$ls_denbandes=$_POST["txtnombandes"];
	$ls_codbanhas=$_POST["txtcodbanhas"];
	$ls_denbanhas=$_POST["txtnombanhas"];
	
	$ls_cuenta_bancodes=$_POST["txtcuentades"];
	$ls_dencuenta_bancodes=$_POST["txtdenctabandes"];
	$ls_cuenta_bancohas=$_POST["txtcuentahas"];
	$ls_dencuenta_bancohas=$_POST["txtdenctabanhas"];
	
	$ld_fecdesde=$_POST["txtfecdesde"];
	$ld_fechasta=$_POST["txtfechasta"];
}
else
{
	$ls_operacion="";	
	$ld_fecha=date("d/m/Y");
	$ls_codbandes="";
	$ls_denbandes="";
	$ls_codbanhas="";
	$ls_denbanhas="";
	$ls_cuenta_bancodes="";
	$ls_dencuenta_bancodes="";
	$ls_cuenta_bancohas="";
	$ls_dencuenta_bancohas="";
	$ld_fecdesde=$ld_fecha;
	$ld_fechasta=$ld_fecha;
}


	

?>
</div> 
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_banco->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_banco);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<input name="operacion" type="hidden" id="operacion">

<form name="form1" method="post" action="">
  
  <table width="590" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
   
    <tr>
      <td width="81"></td>
    </tr>
    <tr class="titulo-ventana">
      <td height="13" colspan="4" align="center">Estado de Cuenta Colocaciones </td>
    </tr>
    <tr>
      <td height="13" colspan="4" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" align="center"><div align="right">Banco Desde</div></td>
      <td height="22" colspan="3" align="center"><div align="left">
        <input name="txtcodbandes" type="text" id="txtcodbandes"  style="text-align:center" size="10" readonly>
        <a href="javascript:cat_bancosdes(1);"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Bancos"></a>
        <input name="txtdenbandes" type="text" id="txtdenbandes" size="51" class="sin-borde" readonly>
</div></td>
    </tr>
    <tr>
      <td height="22" align="center"><div align="right">Banco Hasta</div></td>
      <td height="22" colspan="3" align="center"><div align="left">
        <input name="txtcodbanhas" type="text" id="txtcodbanhas"  style="text-align:center" size="10" readonly>
        <a href="javascript:cat_bancoshas(2);"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Bancos"></a>
        <input name="txtdenbanhas" type="text" id="txtdenbanhas" size="51" class="sin-borde" readonly>
</div></td>
    </tr>
	<tr>
      <td height="28" align="center"><div align="right">Cuenta Desde</div></td>
      <td height="28" colspan="3" align="center"><div align="left">
        <input name="txtcuentades" type="text" id="txtcuentades" style="text-align:center" size="30" maxlength="25" readonly>
          <a href="javascript:catalogo_cuentabancodes(1);"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Cuentas Bancarias"></a>
          <input name="txtdenominaciondes" type="text" class="sin-borde" id="txtdenominaciondes" style="text-align:left" size="48" maxlength="254" readonly>
      </div></td>
    </tr>
    <tr>
      <td height="26" align="center"><div align="right">Cuenta Hasta</div></td>
      <td height="26" colspan="3" align="center"><div align="left">
        <input name="txtcuentahas" type="text" id="txtcuentahas" style="text-align:center" size="30" maxlength="25" readonly>
          <a href="javascript:catalogo_cuentabancohas(2);"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Cuentas Bancarias"></a>
          <input name="txtdenominacionhas" type="text" class="sin-borde" id="txtdenominacionhas" style="text-align:left" size="48" maxlength="254" readonly>
      </div></td>
    </tr>
	
	<tr>
      <td height="22" align="center"><div align="right">Desde</div></td>
      <td width="127" height="22" align="center"><div align="left">
        <input name="txtfecdesde" type="text" id="txtfecdesde"  style="text-align:center" onKeyPress="currencyDate(this);" value="<?php print $ld_fecdesde?>" size="24" maxlength="10"  datepicker="true">
      </div></td>
      <td width="80" align="center"><div align="right">Hasta</div></td>
      <td width="300" align="center"><div align="left">
        <input name="txtfechasta" type="text" id="txtfechasta" style="text-align:center" onKeyPress="currencyDate(this);" value="<?php print $ld_fechasta?>"  datepicker="true">
      </div></td>
    </tr>
    <tr>
      <td height="22" align="center"><div align="right">Ordenar </div>        <div align="left"></div></td>
      <td align="center"><div align="left">
        <select name="orden">
          <option value="D">Documento</option>
          <option value="F">Fecha</option>
          <option value="O">Operacion</option>
		  <option value="P">Proyecto</option>
		  <option value="FE">Fecha Emisión</option>
          <option value="FV">Fecha Vencimiento</option>
          <option value="C">Cuenta Cedente</option>
		  
        </select>
</div></td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" colspan="4" align="center"><div align="right">     <span class="Estilo1">
        <input name="txttipocuenta" type="hidden" id="txttipocuenta">
        <input name="txtdisponible" type="hidden" id="txtdisponible" style="text-align:right" size="24" readonly>
        <input name="txtcuenta_scg" type="hidden" id="txtcuenta_scg" style="text-align:center" value="<?php print $ls_cuenta_scg;?>" size="24" readonly>
        <input name="txtdentipocuenta" type="hidden" id="txtdentipocuenta">
        <input name="operacion"   type="hidden"   id="operacion"   value="<?php print $ls_operacion;?>">
      </span></div></td>
    </tr>
    <tr>
      <td colspan="4" align="center">
        <p>&nbsp;</p>        </td>

    </tr>
  </table>
 
</table>
</p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function ue_search()
{
  f=document.form1;
  ld_fecdesde= f.txtfecdesde.value;
  ld_fechasta= f.txtfechasta.value;
  ls_codbandes  = f.txtcodbandes.value;
  ls_codbanhas  = f.txtcodbanhas.value;
  ls_ctabandes  = f.txtcuentades.value;
  ls_ctabanhas  = f.txtcuentahas.value;
  ls_orden=f.orden.value;
  if((ld_fecdesde!="")&&(ld_fechasta!=""))
  {
	   pagina="reportes/sigesp_scb_rpp_colocaciones_pdf.php?fecdes="+ld_fecdesde+"&fechas="+ld_fechasta+"&codbandes="+ls_codbandes+"&ctabandes="+ls_ctabandes+"&codbanhas="+ls_codbanhas+"&ctabanhas="+ls_ctabanhas+"&orden="+ls_orden;
	   window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
   }
   else
   {
   	   alert("Seleccione los parametros de busqueda");
   }
}

function ue_openexcel()
{
  f=document.form1;
  ld_fecdesde= f.txtfecdesde.value;
  ld_fechasta= f.txtfechasta.value;
  ls_codbandes  = f.txtcodbandes.value;
  ls_codbanhas  = f.txtcodbanhas.value;
  ls_ctabandes  = f.txtcuentades.value;
  ls_ctabanhas  = f.txtcuentahas.value;
  ls_orden=f.orden.value;
	  if((ld_fecdesde!="")&&(ld_fechasta!=""))
  		{
	        pagina="reportes/sigesp_scb_rpp_colocaciones_excel.php?fecdes="+ld_fecdesde+"&fechas="+ld_fechasta+"&codbandes="+ls_codbandes+"&ctabandes="+ls_ctabandes+"&codbanhas="+ls_codbanhas+"&ctabanhas="+ls_ctabanhas+"&orden="+ls_orden;
	        window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
        }
      else
        {
   	      alert("Seleccione los parámetros de búsqueda !!!");
        }
}

function rellenar_cad(cadena,longitud,objeto)
{
	var mystring=new String(cadena);
	cadena_ceros="";
	lencad=mystring.length;

	total=longitud-lencad;
	if (cadena!="")
	   {
		for (i=1;i<=total;i++)
			{
			  cadena_ceros=cadena_ceros+"0";
			}
		cadena=cadena_ceros+cadena;
		if (objeto=="txtcodprov1")
		   {
			 document.form1.txtcodprov1.value=cadena;
		   }
		 else
		   {
			 document.form1.txtcodprov2.value=cadena;
		   }  
        }
}

 function currencyDate(date)
  { 
	ls_date=date.value;
	li_long=ls_date.length;
	f=document.form1;
			 
		if(li_long==2)
		{
			ls_date=ls_date+"/";
			ls_string=ls_date.substr(0,2);
			li_string=parseInt(ls_string,10);

			if((li_string>=1)&&(li_string<=31))
			{
				date.value=ls_date;
			}
			else
			{
				date.value="";
			}
			
		}
		if(li_long==5)
		{
			ls_date=ls_date+"/";
			ls_string=ls_date.substr(3,2);
			li_string=parseInt(ls_string,10);
			if((li_string>=1)&&(li_string<=12))
			{
				date.value=ls_date;
			}
			else
			{
				date.value=ls_date.substr(0,3);
			}
		}
		if(li_long==10)
		{
			ls_string=ls_date.substr(6,4);
			li_string=parseInt(ls_string,10);
			if((li_string>=1900)&&(li_string<=2090))
			{
				date.value=ls_date;
			}
			else
			{
				date.value=ls_date.substr(0,6);
			}
		}
			//alert(ls_long);


  //  return false; 
   }
//Catalogo de cuentas contables
	 function catalogo_cuentabancodes(tipo)
	 {
	   f=document.form1;
	   ls_codbandes=f.txtcodbandes.value;
	   ls_denbandes=f.txtdenbandes.value;
	   ls_codbanhas=f.txtcodbanhas.value;
	   ls_denbanhas=f.txtdenbanhas.value;
	  	   if((ls_codbandes!="")&&(ls_codbanhas!=""))
		   {
			   pagina="sigesp_cat_ctabanco.php?bandes="+ls_codbandes+"&hidnombandes="+ls_denbandes+"&banhas="+ls_codbanhas+"&hidnombanhas="+ls_denbanhas+"&tipo="+tipo;
			   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
		   }
		   else
		   {
				alert("Seleccione el Banco Desde y Hasta");   
		   }
	  
	 }
	 
	 function catalogo_cuentabancohas(tipo)
	 {
	   f=document.form1;
	   ls_codbandes=f.txtcodbandes.value;
	   ls_denbandes=f.txtdenbandes.value;
	   ls_codbanhas=f.txtcodbanhas.value;
	   ls_denbanhas=f.txtdenbanhas.value;
	   ls_cta_des=f.txtcuentades.value;
       if (ls_cta_des!="")
	   {	  
	  	   if((ls_codbandes!="")&&(ls_codbanhas!=""))
		   {
			   pagina="sigesp_cat_ctabanco.php?bandes="+ls_codbandes+"&hidnombandes="+ls_denbandes+"&banhas="+ls_codbanhas+"&hidnombanhas="+ls_denbanhas+"&tipo="+tipo;
			   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
		   }
		   else
		   {
				alert("Seleccione el Banco Desde y Hasta");   
		   }
	   }
	   else
	   {
	   		alert("Debe Seleccionar una Cuenta Desde");
	   }
		
	 }	
	 	 
	 function cat_bancosdes(tipo)
	 {
	   f=document.form1;
	   pagina="sigesp_cat_bancos.php?tipo="+tipo;
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
	 }

	 function cat_bancoshas(tipo)
	 {
	   f=document.form1;
	   ls_bandes=f.txtdenbandes.value;
	   if (ls_bandes!="")
	   {
		   pagina="sigesp_cat_bancos.php?tipo="+tipo;
		   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
	   }
	   else
	   {
	   		alert("Debe seleccionar un banco desde!");
	   }
	 }

</script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>
