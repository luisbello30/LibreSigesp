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
   		global $ls_codcatsig,$ls_dencatsig,$ls_spg_cuenta,$li_canart,$li_cosart,$ls_dentipart,$ls_codunimed,$ls_denunimed;
		
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
		$ls_spg_cuenta="";
		$li_canart=0;
		$li_cosart=0;
   }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Detalle de Activo </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
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
require_once("sigesp_siv_c_transferencia.php");
$io_siv=  new sigesp_siv_c_transferencia();
require_once("class_funciones_inventario.php");
require_once("../shared/class_folder/class_mensajes.php");
$io_msg= new class_mensajes();
$io_fun_inventario= new class_funciones_inventario();
$io_fun_inventario->uf_load_seguridad("SIV","sigesp_siv_p_transferencia.php",$ls_permisos,$la_seguridad,$la_permisos);

	$ls_operacion=$io_fun_inventario->uf_obteneroperacion();
	
	switch($ls_operacion)
	{
		case"NUEVO":
			uf_limpiarvariables();
			$li_totrows=$io_fun_inventario->uf_obtenervalor_get("linea",1);
			$ls_codalm=$io_fun_inventario->uf_obtenervalor_get("almacen","");
		break;
		case"BUSCAR":
			uf_limpiarvariables();
			$ls_codart=$io_fun_inventario->uf_obtenervalor("txtcodart","");
			$li_totrows=$io_fun_inventario->uf_obtenervalor("totalfilas",1);
			$ls_codalm=$io_fun_inventario->uf_obtenervalor("hidalmacen","");
			$lb_valido=$io_siv->uf_select_articulo($ls_codart,$ls_codalm,&$ls_denart,&$li_unidad,&$li_cosart,&$li_existencia);
			if(!$lb_valido)
			{
				$io_msg->message("El artículo no tiene existencia en el almacén seleccionado");
			}
		break;
	}
/*
$arre=$_SESSION["la_empresa"];
$ls_codemp=$arre["codemp"];

	if (array_key_exists("totrow",$_GET))
	{
		$li_totrows=$_GET["totrow"];
	}
	else
	{
		$li_totrows="";
	}


	switch ($ls_operacion) 
	{
		case "BUSCARACTIVO":
			$li_totrow=$_POST["totalfilas"];
			$ls_codact= $_POST["txtcodact"];
			$ls_seract= $_POST["txtseract"];
			$ls_ideact= $_POST["txtideact"];
			$ls_denact= $_POST["txtdenact"];
			$li_monact="";
			
			$lb_valido=$io_saf->uf_saf_select_activo($ls_codemp,$ls_codact,$li_monact);
		break;
	}
*/?>
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
    <tr>
      <td><div align="right">Unidad</div></td>
      <td height="22" colspan="3"><div align="left">
        <select name="cmbunidad" id="cmbunidad">
          <option value="M">Mayor</option>
          <option value="D">Detal</option>
        </select>
      </div></td>
    </tr>
    <tr>
      <td><div align="right">Cantidad</div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtcanart" type="text" id="txtcanart" style="text-align:right " value="<?php print number_format($li_canart,2,",","."); ?>" size="10"  onKeyPress="return(ue_formatonumero(this,'.',',',event));">
      </div></td>
    </tr>
    <tr>
      <td><div align="right"></div></td>
      <td width="221" align="center"><input name="operacion" type="hidden" id="operacion">
      <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
      <input name="hidcosto" type="hidden" id="hidcosto"  value="<?php print number_format($li_cosart,2,",","."); ?>">
      <input name="hidexistencia" type="hidden" id="hidexistencia" value="<?php print $li_existencia; ?>">
      <input name="hidunidad" type="hidden" id="hidunidad" value="<?php print $li_unidad; ?>"></td>
      <input name="hidalmacen" type="hidden" id="hidalmacen" value="<?php print $ls_codalm; ?>"></td>
      <td width="110"><a href="javascript: ue_agregar();"><img src="../shared/imagebank/tools15/aprobado.gif" width="15" height="15" class="sin-borde">Agregar Detalle</a> </td>
      <td width="98"><a href="javascript: ue_cancelar();"><img src="../shared/imagebank/tools15/eliminar.gif" width="15" height="15" class="sin-borde">Cancelar</a></td>
    </tr>
  </table>
</form>
</body>
<script language="javascript">

	function ue_verificar_articulo()
	{
		f=document.form1;
		articulo=f.txtcodart.value;
		if(articulo!="")
		{
			f.operacion.value="BUSCAR";
			f.action="sigesp_siv_pdt_transferencia.php";
			f.submit();
		}
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
	li_cosart=f.hidcosto.value;
	li_unidad=f.hidunidad.value;
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
	}
	if(lb_valido)
	{
		if((ls_codart=="")||(li_canart=="0,00")||(li_cosart==""))
		{
			alert("Debe completar todos los datos");
		}
		else
		{
			obj=eval("opener.document.form1.txtdenart"+li_totrows+"");
			obj.value=ls_denart;
			obj=eval("opener.document.form1.txtcodart"+li_totrows+"");
			obj.value=ls_codart;
			obj=eval("opener.document.form1.cmbunidad"+li_totrows+"");
			obj.value=ls_unidad;
			obj=eval("opener.document.form1.txtcantidad"+li_totrows+"");
			obj.value=li_canart;
			obj=eval("opener.document.form1.txtcosuni"+li_totrows+"");
			obj.value=li_cosart;
			obj=eval("opener.document.form1.hidunidad"+li_totrows+"");
			obj.value=li_unidad;
			opener.ue_montosfactura(li_totrows)
			opener.document.form1.operacion.value="AGREGARDETALLE";
			opener.document.form1.submit();
			close();
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

</script> 
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
<script language="javascript" src="js/funciones.js"></script>
</html>