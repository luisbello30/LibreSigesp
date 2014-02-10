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
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_jubilados.php",$ls_permisos,$la_seguridad,$la_permisos);
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   
   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $li_primrem,$li_subtotper,$li_porpenper,$li_monpenper,$li_segrem,$ld_fecvid,$ls_operacion,$ls_existe,$la_tipjub;
		global $io_fun_nomina, $ls_cedula;
		
		$li_primrem="";
		$li_subtotper="";
		$li_porpenper="";
		$li_monpenper="";
		$li_segrem="";
		$ld_fecvid="dd/mm/aaaa";
		$la_tipjub[0]="";
		$la_tipjub[1]="";
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_existe=$io_fun_nomina->uf_obtenerexiste();
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 18/03/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codper,$ls_nomper,$li_primrem,$li_subtotper,$li_porpenper,$li_monpenper,$li_segrem,$ld_fecvid,$ls_operacion,$ls_existe,$ls_tipjub;
		global $io_fun_nomina, $ls_cedula;
		
		$ls_codper=$_POST["txtcodper"];
		$li_primrem=$_POST["txtprimrem"];
		$li_subtotper=$_POST["txtsubtotper"];
		$li_porpenper=$_POST["txtporpenper"];
		$li_monpenper=$_POST["txtmonpenper"];
		$li_segrem=$_POST["txtsegrem"];
		$ld_fecvid=$_POST["txtfecvid"];
		$ls_tipjub=$_POST["cmbtipjub"];
		$ls_nomper=$_POST["txtnomper"];
		$ls_cedula=$_POST["txtcedula"];
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<script language="javascript">
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title >Datos de Jubilación</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EFEBEF;
}

a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}

-->
</style>
<script type="text/javascript" language="JavaScript1.2" src="js/stm31.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="js/funcion_nomina.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/validaciones.js"></script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_snorh_c_jubilados.php");
	$io_jubilados=new sigesp_snorh_c_jubilados();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":
		 	$ls_codper=$_GET["codper"];
			$ls_nomper=$_GET["nomper"];
			break;

		case "GUARDAR":
			uf_load_variables();
			$lb_valido=$io_jubilados->uf_guardar($ls_existe,$ls_codper,$ls_nomper,$li_primrem,$li_subtotper,$li_porpenper,$li_monpenper,
												$li_segrem,$ld_fecvid,$ls_tipjub,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_existe="FALSE";
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
			}
			else
			{
				$io_fun_nomina->uf_seleccionarcombo("R-E",$ls_tipjub,$la_tipjub,2);
			}
		break;

		case "ELIMINAR":
			uf_load_variables();
			$lb_valido=$io_jubilados->uf_delete_jubilados($ls_codper,$ls_nomper,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_existe="FALSE";
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
			}
			else
			{
				$io_fun_nomina->uf_seleccionarcombo("R-E",$ls_tipjub,$la_tipjub,2);
			}
		break;
	}
	$io_jubilados->uf_destructor();
	unset($io_jubilados);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Nómina</td>
			<td width="346" bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
        </table>
	 </td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif"  title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif"  title="Eliminar" alt="Eliminar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_volver();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigesp_snorh_d_personal.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="550" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr>
          <td colspan="2"><input name="txtnomper" type="text" class="sin-borde2" id="txtnomper" value="<?php print $ls_nomper;?>" size="60" readonly>
            <input name="txtcodper" type="hidden" id="txtcodper" value="<?php print $ls_codper;?>"></td>
        </tr>
        <tr class="titulo-ventana">
          <td height="20" colspan="2" class="titulo-ventana">Datos de Jubilaci&oacute;n </td>
        </tr>
        <tr>
          <td width="166" height="22">&nbsp;</td>
          <td width="378">&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Primera Remuneraci&oacute;n</div></td>
          <td><div align="left">
            <input name="txtprimrem" type="text" id="txtprimrem" value="<?php print number_format($li_primrem,2,",",".");?>" size="23" maxlength="20"  onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Sub - Total</div></td>
          <td><input name="txtsubtotper" type="text" id="txtsubtotper" value="<?php print number_format($li_subtotper,2,",",".");?>" size="23" maxlength="20"  onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right">
        </tr>
        <tr>
          <td height="22"><div align="right">Porcentaje de Pensi&oacute;n</div></td>
          <td><div align="left">
            <input name="txtporpenper" type="text" id="txtporpenper" value="<?php print number_format($li_porpenper,2,",",".");?>" size="23" maxlength="20"  onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right">
          %</div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Monto de Pensi&oacute;n</div></td>
          <td><div align="left">
            <input name="txtmonpenper" type="text" id="txtmonpenper" value="<?php print number_format($li_monpenper,2,",",".");?>" size="23" maxlength="20"  onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Ultima Remuneraci&oacute;n</div></td>
          <td><div align="left">
            <input name="txtsegrem" type="text" id="txtsegrem" value="<?php print number_format($li_segrem,2,",",".");?>" size="23" maxlength="20"  onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Fe de Vida </div></td>
          <td><div align="left">
            <input name="txtfecvid" type="text" id="txtfecvid" value="<?php print $ld_fecvid;?>" size="15" maxlength="10" datepicker="true" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tipo de Jubilaci&oacute;n</div></td>
          <td><div align="left">
            <select name="cmbtipjub" id="cmbtipjub">
              <option value="" selected>--Seleccione Uno--</option>
              <option value="R" <?php print $la_tipjub[0];?>>Regular</option>
              <option value="E" <?php print $la_tipjub[1];?>>Especial</option>
            </select>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right"></div></td>
          <td><input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>"> 
            <input name="hidfecvid" type="hidden" id="hidfecvid" value="<?php print $ld_fecvid;?>"></td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
</body>
<script language="javascript">
function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.existe.value="FALSE";	
		codper=ue_validarvacio(f.txtcodper.value);
		nomper=ue_validarvacio(f.txtnomper.value);	
		f.action="sigesp_snorh_d_jubilados.php?codper="+codper+"&nomper="+nomper+"";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_volver()
{
	f=document.form1;
	f.operacion.value="BUSCAR";
	f.existe.value="TRUE";	
	codper=ue_validarvacio(f.txtcodper.value);
	f.action="sigesp_snorh_d_personal.php?codper="+codper;
	f.submit();
}

function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	lb_existe=f.existe.value;
	if(((lb_existe=="TRUE")&&(li_cambiar==1))||(lb_existe=="FALSE")&&(li_incluir==1))
	{
		valido=true;
		codper = ue_validarvacio(f.txtcodper.value);
		prirem = ue_validarvacio(f.txtprimrem.value);
		subtotal = ue_validarvacio(f.txtsubtotper.value);
		porpen = ue_validarvacio(f.txtporpenper.value);
		monpen = ue_validarvacio(f.txtmonpenper.value);
		f.txtfecvid.value=ue_validarfecha(f.txtfecvid.value);	
		ultrem = ue_validarvacio(f.txtsegrem.value);
		if(valido)
		{
			if ((prirem!="")&&(subtotal!="")&&(porpen!="")&&(monpen!=""))
			{
				f.operacion.value="GUARDAR";
				f.action="sigesp_snorh_d_jubilados.php";
				f.submit();
			}
			else
			{
				alert("Debe llenar todos los datos.");
			}
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
	
}

function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{	
		if(f.existe.value=="TRUE")
		{
			codper = ue_validarvacio(f.txtcodper.value);
			nomper = ue_validarvacio(f.txtnomper.value);
			if ((codper!="")&&(nomper!=""))
			{
				if(confirm("¿Desea eliminar el Registro actual?"))
				{
					f.operacion.value="ELIMINAR";
					f.action="sigesp_snorh_d_jubilados.php";
					f.submit();
				}
			}
			else
			{
				alert("Debe buscar el registro a eliminar.");
			}
		}
		else
		{
			alert("Debe buscar el registro a eliminar.");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	//window.open("../hlp/index.php?sistema=SNO&subsistema=SNR&nomfis=sno/sigesp_hlp_snr_personal.php","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		codper = ue_validarvacio(f.txtcodper.value);
		nomper = ue_validarvacio(f.txtnomper.value);
		window.open("sigesp_snorh_cat_jubilados.php?codper="+codper+"&nomper="+nomper+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_hcm()
{
	f=document.form1;
	if((f.cmbsexfam.value!="F")||(f.cmbnexfam.value!="C"))
	{
		f.chkhcmfam.checked=false;
		alert("La poliza de maternidad es solo para las Conyugues");
	}
}

function ue_checkhijo(tipo)
{
	f=document.form1;
	if(f.cmbnexfam.value!="H")
	{
		
		alert("Esta opicón es solamente para para Hijos");
		if (tipo=='1')
		{
			f.chkhijesp.checked=false;
		}
		else if (tipo=='2')
		{
			f.chkbonjug.checked=false;
		}
	}
}


var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
</script> 
</html>