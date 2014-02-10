<?php
session_start();
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "close();";
	print "</script>";		
}
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codart,$ls_denart,$ls_codartpri,$ls_denartpri,$ls_codtipart,$ls_dentipart,$ls_codunimed,$ls_denunimed;
   		global $ls_codcatsig,$ls_dencatsig,$ls_spgcuenta,$li_canart,$li_cosart;
		
		$ls_codart="";
		$ls_denart="";
		$ls_codartpri="";
		$ls_denartpri="";
		$ls_codtipart="";
		$ls_dentipart="";
		$ls_codunimed="";
		$ls_denunimed="";
		$ls_codcatsig="";
		$ls_dencatsig="";
		$ls_spgcuenta="";
		$li_canart=0;
		$li_cosart=0;
   }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Detalle de Art&iacute;culo </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script language="javascript" src="js/funcion_siv.js"></script>
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

<body onLoad="javascript: ue_focus();">
<?php
require_once("sigesp_siv_c_recepcion.php");
$io_siv=  new sigesp_siv_c_recepcion();
require_once("class_funciones_inventario.php");
$io_fun_inventario= new class_funciones_inventario();
$io_fun_inventario->uf_load_seguridad("SIV","sigesp_siv_p_recepcion.php",$ls_permisos,$la_seguridad,$la_permisos);
$li_catalogo=$io_siv->uf_siv_select_catalogo($li_estnum,$li_estcmp);
$ls_codartori="";
	$ls_operacion=$io_fun_inventario->uf_obteneroperacion();
	$li_nuevo="2";
	
	switch($ls_operacion)
	{
		case"NUEVO":
			uf_limpiarvariables();
			$li_totrows=$io_fun_inventario->uf_obtenervalor_get("linea",1);
			$ls_origen=$io_fun_inventario->uf_obtenervalor_get("origen","");
		break;
		case"BUSCAR":
			uf_limpiarvariables();
			$ls_codart=$io_fun_inventario->uf_obtenervalor("txtcodart",1);
			$li_totrows=$io_fun_inventario->uf_obtenervalor("totalfilas","");
			$ls_origen=$io_fun_inventario->uf_obtenervalor("origen","");
			/*if($ls_origen=="orden")
			{
				$ls_codartori=$io_fun_inventario->uf_obtenervalor("codartori","");
			}*/
			$lb_valido=$io_siv->uf_select_articulo($ls_codart,$ls_origen,$ls_codartpri,$ls_denart,$li_unidad);
			if(!$lb_valido)
			{
				$li_nuevo="1";
			}
		break;
		case"GUARDAR":
			$ls_origen=$io_fun_inventario->uf_obtenervalor("origen","");
			$li_totrows=$io_fun_inventario->uf_obtenervalor("totalfilas",1);
			$ls_codart=$io_fun_inventario->uf_obtenervalor("txtcodart","");
			$ls_codartpri=$io_fun_inventario->uf_obtenervalor("txtcodartpri","");
			$ls_denart=$io_fun_inventario->uf_obtenervalor("txtdenart2","");
			$ls_codtipart=$io_fun_inventario->uf_obtenervalor("txtcodtipart","");
			$ls_codunimed=$io_fun_inventario->uf_obtenervalor("txtcodunimed","");
			$ls_codcatsig=$io_fun_inventario->uf_obtenervalor("txtcodcatsig","");
			$ls_spgcuenta=$io_fun_inventario->uf_obtenervalor("txtspg_cuenta","");
			$lb_valido=$io_siv->uf_siv_insert_articulo($ls_codart,$ls_denart,$ls_codtipart,$ls_codunimed,$ls_spgcuenta,$ls_codcatsig,$ls_codartpri,$la_seguridad);
			if($lb_valido)
			{
				print "<script>";
				print "close();";
				print "</script>";
			}
		break;
		case"VALIDAR":
			$ls_origen=$io_fun_inventario->uf_obtenervalor("origen","");
			$li_nuevo="1";
			$li_totrows=$io_fun_inventario->uf_obtenervalor("totalfilas",1);
			$ls_codart=$io_fun_inventario->uf_obtenervalor("txtcodart","");
			$ls_codartpri=$io_fun_inventario->uf_obtenervalor("txtcodartpri","");
			$ls_denartpri=$io_fun_inventario->uf_obtenervalor("txtdenartpri","");
			$ls_denart=$io_fun_inventario->uf_obtenervalor("txtdenart2","");
			$ls_codtipart=$io_fun_inventario->uf_obtenervalor("txtcodtipart","");
			$ls_dentipart=$io_fun_inventario->uf_obtenervalor("txtdentipart","");
			$ls_codunimed=$io_fun_inventario->uf_obtenervalor("txtcodunimed","");
			$ls_denunimed=$io_fun_inventario->uf_obtenervalor("txtdenunimed","");
			$ls_codcatsig=$io_fun_inventario->uf_obtenervalor("txtcodcatsig","");
			$ls_dencatsig=$io_fun_inventario->uf_obtenervalor("txtdencatsig","");
			$ls_spgcuenta=$io_fun_inventario->uf_obtenervalor("txtspg_cuenta","");
			$li_canart=$io_fun_inventario->uf_obtenervalor("txtcanart","");
			$li_canart=$io_fun_inventario->uf_formatocalculo($li_canart);
			$li_cosart=$io_fun_inventario->uf_obtenervalor("txtcosart","");
			$li_cosart=$io_fun_inventario->uf_formatocalculo($li_cosart);
		break;
	}
/*	if($ls_origen=="orden")
	{
		$ls_disabled="disabled";
	}*/
?>
<form name="form1" method="post" action="">
  <table width="559" border="0" align="center" class="formato-blanco">
    <tr>
      <td height="22" colspan="4" class="titulo-celda">Detalle de Art&iacute;culo </td>
    </tr>
    <tr>
      <td width="110"><div align="right">Articulo</div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtcodart" type="text" id="txtcodart" style="text-align:center " value="<?php print $ls_codart; ?>" onKeyPress="javascript: ue_enviar(event);" onBlur="javascript: ue_verificar_articulo(); ">
        <a href="javascript: ue_cataactivo();"></a>
          <input name="txtdenart" type="text" class="sin-borde" id="txtdenart" value="<?php print $ls_denart; ?>" size="40" readonly>
      </div></td>
    </tr>
        <?php
			if($li_nuevo=="1")
			{
		?>
    <tr>
      <td height="22" align="right"><div align="right">C&oacute;digo Principal </div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtcodartpri" type="text" id="txtcodartpri" value="<?php print $ls_codartpri; ?>" size="25" maxlength="20" style="text-align:center" onChange="javascript: ue_hola();" readonly>
        <a href="javascript: uf_catalogoarticulo();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
        <input name="txtdenartpri" type="text" class="sin-borde" id="txtdenartpri" value="<?php print $ls_denartpri; ?>" size="45" readonly>
      </div></td>
    </tr>
    <tr>
      <td><div align="right">Denominaci&oacute;n</div></td>
      <td height="22" colspan="3">
        <div align="left">
          <input name="txtdenart2" type="text" id="txtdenart2" value="<?php print $ls_denart?>" size="45" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn&ntilde;opqrstuvwxyz ()#!%/[]*-+_.,:;');">
        </div></td>
    </tr>
    <tr>
      <td><div align="right">Tipo de Art&iacute;culo</div></td>
      <td height="22" colspan="3">
        <div align="left">
          <input name="txtcodtipart" type="text" id="txtcodtipart" value="<?php print $ls_codtipart?>" size="6" maxlength="4" readonly>
          <a href="javascript: ue_catatipart();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
          <input name="txtdentipart" type="text" class="sin-borde" id="txtdentipart" value="<?php print $ls_dentipart?>" size="30" readonly>
          <input name="txtobstipart" type="hidden" id="txtobstipart">
        </div></td>
    </tr>
    <tr>
      <td><div align="right">Unidad de Medida</div></td>
      <td height="22" colspan="3">
        <div align="left">
          <input name="txtcodunimed" type="text" id="txtcodunimed" value="<?php print $ls_codunimed?>" size="6" maxlength="4" readonly>
          <a href="javascript: ue_cataunimed();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
          <input name="txtdenunimed" type="text" class="sin-borde" id="txtdenunimed" value="<?php print $ls_denunimed?>" size="30" readonly>
          <input name="txtunidad" type="hidden" id="txtunidad">
          <input name="txtobsunimed" type="hidden" id="txtobsunimed">
        </div></td>
    </tr>
  <?php
	if($li_catalogo==1)
	{?>
    <tr>
      <td><div align="right">SIGECOF</div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtcodcatsig" type="text" id="txtcodcatsig" style="text-align:center" value="<?php print $ls_codcatsig; ?>" size="25" readonly>
        <a href="javascript: ue_sigecof();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
        <input name="txtdencatsig" type="text" class="sin-borde" id="txtdencatsig" value="<?php print $ls_dencatsig; ?>" size="45" readonly>
      </div></td>
    </tr>
  <?php
	}
  ?>
    <tr>
      <td><div align="right">Cuenta Presupestaria</div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtspg_cuenta" type="text" id="txtspg_cuenta" onKeyUp="javascript: ue_validarcomillas(this);" value="<?php print $ls_spgcuenta; ?>" size="25" maxlength="25" readonly style="text-align:center ">
      <a href="javascript: ue_cataspg();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
    </tr>
        <?php
		}
		else
		{
		?>
		<input name="txtcodartpri" type="hidden" id="txtcodartpri" value="<?php print $ls_codartpri; ?>" size="25" maxlength="20" readonly>
        <?php
		}
		?>
    <tr>
      <td><div align="right">Unidad</div></td>
      <td height="22" colspan="3"><div align="left">
        <select name="cmbunidad" id="cmbunidad" disabled="disabled">
          <option value="M">Mayor</option>
          <option value="D" selected>Detal</option>
        </select>
      </div></td>
    </tr>
    <tr>
      <td><div align="right">Cantidad</div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtcanart" type="text" id="txtcanart" style="text-align:right " value="<?php print number_format($li_canart,2,",","."); ?>" size="10"  onKeyPress="return(ue_formatonumero(this,'.',',',event));" onBlur="javascript: ue_validarcantidad();">
      </div></td>
    </tr>
    <tr>
      <td><div align="right">Costo</div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtcosart" type="text" id="txtcosart"  style="text-align:right " value="<?php print number_format($li_cosart,2,",","."); ?>" size="10"  onKeyPress="return(ue_formatonumero(this,'.',',',event));">
      </div></td>
    </tr>
    <tr>
      <td><div align="right"></div></td>
      <td width="221" align="center"><input name="operacion" type="hidden" id="operacion">
      <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
      <input name="hidstatus" type="hidden" id="hidstatus">
      <input name="hidnuevo" type="hidden" id="hidnuevo" value="<?php print $li_nuevo; ?>">
      <input name="hidunidad" type="hidden" id="hidunidad" value="<?php print $li_unidad; ?>">
      <input name="origen" type="hidden" id="origen" value="<?php print $ls_origen; ?>">
      <input name="sigecof" type="hidden" id="sigecof" value="<?php print $li_catalogo; ?>"></td>
      <input name="codartori" type="hidden" id="codartori" value="<?php print $ls_codartori; ?>"></td>
      <input name="canori" type="hidden" id="canori" value="<?php print $ls_canori; ?>"></td>
      <input name="penart" type="hidden" id="penart" value="<?php print $ls_penart; ?>"></td>
      <input name="hidpendiente" type="hidden" id="hidpendiente" value="<?php print $ls_hidpendiente; ?>"></td>
      <td width="110"><a href="javascript: ue_agregar();"><img src="../shared/imagebank/tools15/aprobado.gif" width="15" height="15" class="sin-borde">Agregar Detalle</a> </td>
      <td width="98"><a href="javascript: ue_cancelar();"><img src="../shared/imagebank/tools15/eliminar.gif" width="15" height="15" class="sin-borde">Cancelar</a></td>
    </tr>
  </table>
</form>
        <?php
	switch($ls_operacion)
	{
		case"VALIDAR":
			?>	
			<script language= javascript>
			 valido=false;
			 f=document.form1;
			 li_totrows=f.totalfilas.value;
			 articulo=f.txtcodartpri.value;
			 origen=f.origen.value;
			 if(origen=="orden")
			 {
				 for(li_i=1; li_i<li_totrows;li_i++)
				 {
					ls_codartgrid=eval("opener.document.form1.txtcodart"+li_i+".value");
					if(trim(ls_codartgrid)==trim(articulo))
					{
						valido=true;
						canori=eval("opener.document.form1.txtcanoriart"+li_i+".value");
						f.canori.value=canori;
						penart=eval("opener.document.form1.txtpenart"+li_i+".value");
						f.penart.value=penart;
						hidpendiente=eval("opener.document.form1.hidpendiente"+li_i+".value");
						f.hidpendiente.value=hidpendiente;
					}
				 }
				 if(valido==false)
				 {
					alert('El articulo generico indicado no existe en orden de compra');
					f.txtcodartpri.value="";
					f.txtdenartpri.value="";
				 }
			}
			</script>
			<?php
		break;
		case"BUSCAR":
			if($li_nuevo!="1")
			{
			?>	
			<script language= javascript>
			 valido=false;
			 f=document.form1;
			 li_totrows=f.totalfilas.value;
			 articulo=f.txtcodartpri.value;
			 origen=f.origen.value;
			 if(origen=="orden")
			 {
				 for(li_i=1; li_i<li_totrows;li_i++)
				 {
					ls_codartgrid=eval("opener.document.form1.txtcodart"+li_i+".value");
					if(trim(ls_codartgrid)==trim(articulo))
					{
						valido=true;
						canori=eval("opener.document.form1.txtcanoriart"+li_i+".value");
						f.canori.value=canori;
						penart=eval("opener.document.form1.txtpenart"+li_i+".value");
						f.penart.value=penart;
						hidpendiente=eval("opener.document.form1.hidpendiente"+li_i+".value");
						f.hidpendiente.value=hidpendiente;
					}
				 }
				 if(valido==false)
				 {
					alert('El articulo generico indicado no existe en orden de compra');
					f.txtcodartpri.value="";
					f.txtdenartpri.value="";
				 }
			}
			</script>
			<?php
			}
		break;
	}
		?>
</body>
<script language="javascript">

	function ue_verificar_articulo()
	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		origen=f.origen.value;
		articulo=f.txtcodart.value;
		li_totrows=f.totalfilas.value;
		if(articulo!="")
		{
			for(li_i=1; li_i<=li_totrows;li_i++)
			{
				ls_codartgrid=eval("opener.document.form1.txtcodart"+li_i+".value");
				if(ls_codartgrid==articulo)
				{
					f.codartori.value=ls_codartgrid;
				}
			}
/*			if(origen=="orden")
			{
				fila=f.totalfilas.value;
				ls_codartgrid=eval("opener.document.form1.txtcodart"+fila+".value");
				f.codartori.value=ls_codartgrid;
			}*/
			f.action="sigesp_siv_pdt_articulos.php";
			f.submit();
		}
	}
	function uf_catalogoarticulo()
	{
		f=document.form1;
		codart="txtcodartpri";
		denart="txtdenartpri";
		tipo="recepcion";
		window.open("sigesp_catdinamic_articulom.php?coddestino="+codart+"&dendestino="+denart+"&tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=120,top=70,location=no,resizable=yes");
	}
	function ue_catatipart()
	{
		window.open("sigesp_catdinamic_tipoarticulo.php?tipo=articulo","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	
	function ue_cataunimed()
	{
		window.open("sigesp_catdinamic_unidadmedida.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	
	function ue_sigecof()
	{
		window.open("sigesp_siv_cat_sigecof.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	
	function ue_cataspg()
	{
		window.open("sigesp_siv_cat_ctasspg.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	
function ue_agregar()
{
	f=document.form1;
	lb_valido=true;
	li_totrows=f.totalfilas.value;
	ls_codart=f.txtcodart.value;
	ls_denart=f.txtdenart.value;
	ls_unidad=f.cmbunidad.value;
	li_canart=f.txtcanart.value;
	li_cosart=f.txtcosart.value;
	li_unidad=f.hidunidad.value;
	ls_origen=f.origen.value;
	li_nuevo=f.hidnuevo.value;
	li_canori=f.canori.value;
	li_penart=f.penart.value;
	li_hidpendiente=f.hidpendiente.value;
	ls_codartpri=f.txtcodartpri.value;
	totalgrid=0;
	if(li_nuevo=="1")
	{
		ls_denart=f.txtdenart2.value;
		ls_codtipart=f.txtcodtipart.value;
		ls_spgcuenta=f.txtspg_cuenta.value;
		li_unidad=f.txtunidad.value;
		//li_unidad=ue_formato_operaciones(li_unidad);
		f.hidunidad.value=li_unidad;
		sigecof=f.sigecof.value;
		ls_codcatsig="";
		if(sigecof=="1")
		{
			ls_codcatsig=f.txtcodcatsig.value;
		}
		if((ls_denart=="")||(ls_codtipart=="")||((ls_codcatsig=="")&&(sigecof=="1"))||(ls_spgcuenta==""))
		{
			alert("Debe completar todos los datos");
			lb_valido=false;
		}
	}
	else
	{
		if(ls_denart=="")
		{
			alert("Existe un problema con el código de artículo, por favor verifique");
			f.txtcodart.focus();
			lb_valido=false;
		}
	}
	for(li_i=1; li_i<=li_totrows;li_i++)
	{
		ls_codartgrid=eval("opener.document.form1.txtcodart"+li_i+".value");
		if(ls_codartgrid==ls_codart)
		{
			alert("El Articulo ya esta en el movimiento");
			f.txtcodart.focus();
			lb_valido=false;
			break;
		}
		ls_codartprigrid=eval("opener.document.form1.txtcodartpri"+li_i+".value");
		if(trim(ls_codartprigrid)==trim(ls_codartpri))
		{
			ls_canart=eval("opener.document.form1.txtcanart"+li_i+".value");
			ls_canart=ue_formato_calculo(ls_canart);
			totalgrid=(parseFloat(totalgrid) + parseFloat(ls_canart));
		}
		if(trim(ls_codartgrid)==trim(ls_codartpri))
		{
			ls_canart=eval("opener.document.form1.txtcanart"+li_i+".value");
			if(ls_canart!="")
			{
				ls_canart=ue_formato_calculo(ls_canart);
				totalgrid=(parseFloat(totalgrid) + parseFloat(ls_canart));
			}
		}
	}
	if(li_hidpendiente=="")
	{
		li_hidpendiente=(parseFloat(li_canori) - parseFloat(totalgrid));
	}
	else
	{
		li_hidpendiente=(parseFloat(li_hidpendiente) - parseFloat(totalgrid));
	}
	if(lb_valido)
	{
		if((ls_codart=="")||(li_canart=="0,00")||(li_cosart=="0,00"))
		{
			alert("Debe completar todos los datos");
		}
		else
		{
			if(ls_origen=='orden')
			{
				obj=eval("opener.document.form1.txtdenart"+li_totrows+"");
				obj.value=ls_denart;
				obj=eval("opener.document.form1.txtcodart"+li_totrows+"");
				obj.value=ls_codart;
				obj=eval("opener.document.form1.txtcodartpri"+li_totrows+"");
				obj.value=ls_codartpri;
				obj=eval("opener.document.form1.txtcanart"+li_totrows+"");
				obj.value=li_canart;
				obj=eval("opener.document.form1.txtcanart"+li_totrows+"");
				obj.value=li_canart;
				obj=eval("opener.document.form1.txtpreuniart"+li_totrows+"");
				obj.value=li_cosart;
				obj=eval("opener.document.form1.txtcanoriart"+li_totrows+"");
				obj.value=li_canori;
				obj=eval("opener.document.form1.hidpendiente"+li_totrows+"");
				obj.value=li_hidpendiente;
				obj=eval("opener.document.form1.txtpenart"+li_totrows+"");
				obj.value=li_penart;
				obj=eval("opener.document.form1.txtunidad"+li_totrows+"");
				obj.value="Detal";
				opener.ue_calcularpendiente(li_totrows);
				opener.document.form1.operacion.value="AGREGARDETALLEORDEN";
				opener.document.form1.submit();
			}
			else
			{
				obj=eval("opener.document.form1.txtdenart"+li_totrows+"");
				obj.value=ls_denart;
				obj=eval("opener.document.form1.txtcodart"+li_totrows+"");
				obj.value=ls_codart;
				obj=eval("opener.document.form1.cmbunidad"+li_totrows+"");
				obj.value=ls_unidad;
				obj=eval("opener.document.form1.txtcanoriart"+li_totrows+"");
				obj.value=li_canart;
				obj=eval("opener.document.form1.txtcanart"+li_totrows+"");
				obj.value=li_canart;
				obj=eval("opener.document.form1.txtpenart"+li_totrows+"");
				obj.value="0,00";
				obj=eval("opener.document.form1.txtpreuniart"+li_totrows+"");
				obj.value=li_cosart;
				obj=eval("opener.document.form1.hidunidad"+li_totrows+"");
				obj.value=li_unidad;
				opener.ue_montosfactura(li_totrows)
				opener.document.form1.operacion.value="AGREGARDETALLE";
				opener.document.form1.submit();
			}
			if(li_nuevo=="1")
			{
				f.operacion.value="GUARDAR";
				f.action="sigesp_siv_pdt_articulos.php";
				f.submit();
			}
			else
			{
				close();
			}
		}
	}
}
function ue_enviar(e)
{
    var whichCode = (window.Event) ? e.which : e.keyCode; 
	if (whichCode == 13) // Enter 
	{
		ue_verificar_articulo();
	}
}

function ue_cancelar()
{
	close();
}

function ue_focus()
{
	f=document.form1;
	if(f.txtcodart.value=="")
	{
		f.txtcodart.focus();
	}
}

function ue_validarcantidad()
{
	f=document.form1;
	li_totrows=f.totalfilas.value;
	ls_codartpri=f.txtcodartpri.value;
	li_canart=f.txtcanart.value;
	li_canart=ue_formato_calculo(li_canart);
	ls_origen=f.origen.value;
	totalgrid=0;
	if(ls_origen=="orden")
	{
		if(ls_codartpri!="")
		{
			for(li_i=1; li_i<=li_totrows;li_i++)
			{
				ls_codartgrid=eval("opener.document.form1.txtcodart"+li_i+".value");
				if(trim(ls_codartgrid)==trim(ls_codartpri))
				{
					ls_canoriart=eval("opener.document.form1.txtcanoriart"+li_i+".value");
					ls_hidpendiente=eval("opener.document.form1.hidpendiente"+li_i+".value");
					if(ls_hidpendiente>0)
					{
						referencia=ls_hidpendiente;
					}
					else
					{
						referencia=ls_canoriart;
					}
					referencia=ue_formato_calculo(referencia);
				}
			}
			for(li_i=1; li_i<=li_totrows;li_i++)
			{
				ls_codartprigrid=eval("opener.document.form1.txtcodartpri"+li_i+".value");
				ls_codartgrid=eval("opener.document.form1.txtcodart"+li_i+".value");
				if(trim(ls_codartprigrid)==trim(ls_codartpri))
				{
					ls_canart=eval("opener.document.form1.txtcanart"+li_i+".value");
					ls_canart=ue_formato_calculo(ls_canart);
					totalgrid=(parseFloat(totalgrid) + parseFloat(ls_canart));
				}
				if(trim(ls_codartgrid)==trim(ls_codartpri))
				{
					ls_canart=eval("opener.document.form1.txtcanart"+li_i+".value");
					if(ls_canart!="")
					{
						ls_canart=ue_formato_calculo(ls_canart);
						totalgrid=(parseFloat(totalgrid) + parseFloat(ls_canart));
					}
				}
			}
			totalmovimiento=(parseFloat(totalgrid) + parseFloat(li_canart));
			if(referencia<totalmovimiento)
			{
				alert("La cantidad de articulos exede la cantidad original/pendiente de la orden");
			}
		}
		else
		{
			alert("Debe ingresar los datos anteriores correctamente.");
			f.txtcanart.value="0,00";
			
		}
	}
}
</script> 
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<script language="javascript" src="js/funcion_siv.js"></script>
</html>