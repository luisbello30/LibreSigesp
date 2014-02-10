<?php
	session_start();
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "location.href='../sigesp_inicio_sesion.php'";
		print "</script>";		
	}
$ls_logusr=$_SESSION["la_logusr"];
require_once("class_funciones_banco.php");
$io_fun_banco = new class_funciones_banco();
$io_fun_banco->uf_load_seguridad("SCB","sigesp_scb_p_procesar_no_contabilizables.php",$ls_permisos,$la_seguridad,$la_permisos);
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
<title>Procesar Documentos No Contabilizables</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/general.css"  rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css"   rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="js/stm31.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/number_format.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/sigesp_cat_ordenar.js"></script>
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
</style>
</head>
<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" alt="Encabezado" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="12" bgcolor="#E7E7E7"><table width="778" border="0" align="center" cellpadding="0" cellspacing="0">
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Caja y Banco</td>
            <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
      <tr>
        <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
        <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td class="toolbar">&nbsp;</td>
    <td class="toolbar">&nbsp;</td>
    <td class="toolbar">&nbsp;</td>
    <td colspan="2" class="toolbar">&nbsp;</td>
    <td width="3" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td class="toolbar" width="21"><a href="javascript:uf_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" title="Nuevo" width="20" height="20" border="0"></a></td>
    <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_cerrar();"></a><a href="javascript:uf_procesar_documentos();"><img src="../shared/imagebank/tools20/ejecutar.gif" alt="Procesar" title="Procesar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="29"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" title="Ayuda" width="20" height="20"></td>
    <td class="toolbar" width="676">&nbsp;</td>
  </tr>
</table>
<?php
if (array_key_exists("operacion",$_POST))
   {
     $ls_numdoc    = $_POST["txtnumdoc"];
	 $ls_codban    = $_POST["txtcodban"];
	 $ls_denban    = $_POST["txtdenban"];
	 $ls_ctaban    = $_POST["txtcuenta"];
     $ld_fecmov    = $_POST["txtfecmov"];
     $ls_codope    = $_POST["cmbcodope"];
	 $ls_denctaban = $_POST["txtdenominacion"];
	 $ls_accion    = $_POST["cmbaccion"];
	 $ls_operacion = $_POST["operacion"];
	 $ls_orden     = $_POST["hidorden"];
	 $li_totrows   = $_POST["hidtotrows"];
	 $li_totrowche = $_POST["hidtotrowschecked"];
   }
else
   {
     $ls_numdoc    = "";
	 $ls_codban    = "";
	 $ls_denban    = "";
	 $ls_ctaban    = "";
     $ld_fecmov    = "";
	 $ls_codope    = "";
	 $ls_denctaban = "";
	 $ls_accion    = '-';
	 $li_totrowche = 0;
	 $li_totrows   = 1;
	 $ls_operacion = "NUEVO";
	 $ls_orden     = "scb_movbco.numdoc ASC";
   }

$ls_selpro = '';
$ls_selrev = '';
if ($ls_accion=='P')
   {
     $ls_selpro = 'selected';
   }
elseif($ls_accion=='R')
   {
	 $ls_selrev = 'selected';
   }

$ls_selnd = '';
$ls_selnc = '';
$ls_selch = '';
$ls_seldp = '';
$ls_selre = '';
if ($ls_codope=='ND')
   {
     $ls_selnd = 'selected';
   }
if ($ls_codope=='NC')
   {
     $ls_selnc = 'selected';
   }
if ($ls_codope=='DP')
   {
     $ls_seldp = 'selected';
   }
if ($ls_codope=='CH')
   {
     $ls_selch = 'selected';
   }
if ($ls_codope=='RE')
   {
     $ls_selre = 'selected';
   }
require_once("class_funciones_banco.php");
require_once("../shared/class_folder/grid_param.php");
require_once("../shared/class_folder/class_mensajes.php");
require_once("sigesp_scb_c_procesar_no_contabilizables.php");

$io_msg    = new class_mensajes();
$io_grid   = new grid_param();
$io_banco  = new class_funciones_banco();
$io_noncon = new sigesp_scb_c_procesar_no_contabilizables();
$ls_codemp = $_SESSION["la_empresa"]["codemp"];

$la_title[1] = "<input name=chkall type=checkbox id=chkall value=1 class=sin-borde style=width:15px;height:15px onClick=javascript:uf_select_all();>";
$la_title[2] = "<a href=javascript:ordenar_detalle('scb_movbco.numdoc');><font color=#FFFFFF>Nro. Documento</font></a>";
$la_title[3] = "<a href=javascript:ordenar_detalle('scb_movbco.nomproben');><font color=#FFFFFF>Proveedor/Beneficiario</font></a>";
$la_title[4] = "<a href=javascript:ordenar_detalle('scb_movbco.fecmov');><font color=#FFFFFF>Fecha</font></a>";
$la_title[5] = "<a href=javascript:ordenar_detalle('scb_movbco.conmov');><font color=#FFFFFF>Concepto</font></a>";
$la_title[6] = "<a href=javascript:ordenar_detalle('scb_movbco.monto');><font color=#FFFFFF>Monto</font></a>";
$ls_grid     = "grid";

switch ($ls_operacion){
  case 'NUEVO':
    uf_nuevo($object,$li_totrows);
  break; 
  case 'BUSCAR':
    $io_noncon->uf_cargar_documentos($ls_codemp,$ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope,$ld_fecmov,$object,$li_totrows,$ls_orden,$ls_accion);
  break;
  case 'PROCESAR':
    $lb_valido    = true;
	$li_totrowpro = 0;
	$io_noncon->io_sql->begin_transaction();
	for ($li_i=1;($li_i<=$li_totrows && $li_totrowpro<$li_totrowche);$li_i++)
	    {
		  $li_totrowpro++;
		  $ls_numdocgrid = trim($_POST["txtnumdoc".$li_i]);
		  $ls_codbangrid = trim($_POST["hidcodban".$li_i]);
		  $ls_ctabangrid = trim($_POST["hidctaban".$li_i]);
		  $ls_codopegrid = trim($_POST["hidcodope".$li_i]);
		  if (array_key_exists("chk".$li_i,$_POST))
		     {
			   $lb_valido = $io_noncon->uf_procesar_documentos($ls_codemp,$ls_numdocgrid,$ls_codbangrid,$ls_ctabangrid,$ls_codopegrid,$ls_accion,$la_seguridad);		
			   if (!$lb_valido)
				  {
				    $io_noncon->io_sql->rollback();
				    $io_msg->message("Error en Procesamiento de Documentos !!!");
				    break;
				  }
			 } 
		}
	if ($lb_valido)
	   {
	     $io_noncon->io_sql->commit();
	     $io_msg->message("Documentos Procesados con Éxito !!!");
	   }
	$io_noncon->uf_cargar_documentos($ls_codemp,$ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope,$ld_fecmov,$object,$li_totrows,$ls_orden,$ls_accion);  
  break;
}

function uf_nuevo(&$object,&$li_totrows)
{
  $li_totrows = 1;
  $object[$li_totrows][1] = "<input name=chk".$li_totrows." 	     type=checkbox id=chk".$li_totrows."          value=1  class=sin-borde onClick=javascript:uf_select_documentos($li_totrows);><input  name=hidcodban".$li_totrows." type=hidden id=hidcodban".$li_totrows." value=''>";
  $object[$li_totrows][2] = "<input name=txtnumdoc".$li_totrows."    type=text     id=txtnumdoc".$li_totrows."    value='' class=sin-borde style=text-align:center size=15 maxlength=15  readonly><input name=hidctaban".$li_totrows." type=hidden id=hidctaban".$li_totrows." value=''>";
  $object[$li_totrows][3] = "<input name=txtcodproben".$li_totrows." type=text     id=txtcodproben".$li_totrows." value='' class=sin-borde style=text-align:center size=33 maxlength=254 readonly><input name=hidcodope".$li_totrows." type=hidden id=hidcodope".$li_totrows." value=''>";
  $object[$li_totrows][4] = "<input name=txtfecmov".$li_totrows."    type=text     id=txtfecmov".$li_totrows."    value='' class=sin-borde style=text-align:center size=8  maxlength=254 readonly>";
  $object[$li_totrows][5] = "<input name=txtconmov".$li_totrows."    type=text     id=txtconmov".$li_totrows."    value='' class=sin-borde style=text-align:left   size=45 maxlength=254 readonly>";
  $object[$li_totrows][6] = "<input name=txtmonmov".$li_totrows."    type=text     id=txtmonmov".$li_totrows."    value='' class=sin-borde style=text-align:right  size=8  maxlength=254 readonly>";
}
?>
<p>&nbsp;</p>
<form name="form1" method="post" action="" id="sigesp_scb_p_procesar_no_contabilizables.php">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_banco->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_banco);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <table width="496" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr class="titulo-ventana">
      <td height="22" colspan="4"><input type="hidden" name="hidorden" id="hidorden" value="<?php echo $ls_orden ?>"/>
        Procesar Documentos No Contabilizables 
      <input name="operacion" type="hidden" id="operacion" value="<?php echo $ls_operacion ?>"></td>
    </tr>
    <tr>
      <td width="89" height="13">&nbsp;</td>
      <td width="158" height="13">&nbsp;</td>
      <td width="79" height="13">&nbsp;</td>
      <td width="168" height="13">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Acci&oacute;n</td>
      <td height="22" colspan="3"><label>
        <select name="cmbaccion" id="cmbaccion" style="width:144px">
          <option value="-">---seleccione---</option>
          <option value="P" <?php echo $ls_selpro ?>>Procesar Documento</option>
          <option value="R" <?php echo $ls_selrev ?>>Reverso de Procesamiento</option>
        </select>
      </label></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Nro Documento</td>
      <td height="22"><label>
        <input name="txtnumdoc" type="text" id="txtnumdoc" style="text-align:center" value="<?php echo $ls_numdoc ?>" size="25" maxlength="15">
      </label></td>
      <td height="22" style="text-align:right">Fecha</td>
      <td height="22"><label>
        <input name="txtfecmov" type="text" id="txtfecmov" value="<?php echo $ld_fecmov ?>" size="15" maxlength="10" datepicker="true" style="text-align:center">
      </label></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Operaci&oacute;n</td>
      <td height="22"><label>
        <select name="cmbcodope" id="cmbcodope" style="width:144px">
          <option value="-">---seleccione---</option>
          <option value="ND" <?php echo $ls_selnd ?>>Nota de D&eacute;bito</option>
          <option value="NC" <?php echo $ls_selnc ?>>Nota de Cr&eacute;dito</option>
          <option value="CH" <?php echo $ls_selch ?>>Cheque</option>
          <option value="DP" <?php echo $ls_seldp ?>>Dep&oacute;sito</option>
          <option value="RE" <?php echo $ls_selre ?>>Retiro</option>
        </select>
      </label></td>
      <td height="22" colspan="2"><span style="text-align:left">
      <input name="txttipocuenta"     type="hidden" id="txttipocuenta">
      <input name="txtdentipocuenta"  type="hidden" id="txtdentipocuenta">
      <input name="txtdisponible"     type="hidden" id="txtdisponible">
      <input name="txtcuenta_scg"     type="hidden" id="txtcuenta_scg">
      <input name="hidtotrows"        type="hidden" id="hidtotrows" value="<?php echo $li_totrows ?>">
      <input name="hidtotrowschecked" type="hidden" id="hidtotrowschecked" value="<?php echo $li_totrowche ?>">
      </span></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Banco</td>
      <td height="22" colspan="3"><label>
        <input name="txtcodban" type="text" id="txtcodban" value="<?php echo $ls_codban ?>" size="6" maxlength="3" readonly style="text-align:center">
        <a href="javascript:uf_catalogo_banco();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar Banco..." border="0"></a> 
      <input name="txtdenban" type="text" class="sin-borde" id="txtdenban" value="<?php echo $ls_denban ?>" size="55" readonly style="text-align:left">
      </label></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Cuenta</td>
      <td height="22" colspan="3"><label>
        <input name="txtcuenta" type="text" id="txtcuenta" style="text-align:center" value="<?php echo $ls_ctaban ?>" size="30" maxlength="25" readonly>
        <a href="javascript:uf_catalogo_cuenta_banco();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar Cuenta Bancaria..." width="15" height="15" border="0"></a> 
        <input name="txtdenominacion" type="text" class="sin-borde" id="txtdenominacion" style="text-align:left" value="<?php echo $ls_denctaban ?>" readonly>
      </label></td>
    </tr>
    <tr>
      <td height="13">&nbsp;</td>
      <td height="13"><label></label></td>
      <td height="13">&nbsp;</td>
      <td height="13">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" colspan="4"><div align="right"><a href="javascript:ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar Documentos..." width="20" height="20" border="0">Buscar Documentos</a></div></td>
    </tr>
    <tr>
      <td height="13" colspan="4">&nbsp;</td>
    </tr>
  </table>
  <p align="center">
    <?php $io_grid->make_gridScroll($li_totrows,$la_title,$object,762,'Documentos No Contabilizables',$ls_grid,165);?>
  </p>
</form>
<p>&nbsp;</p>
</body>
<script language="javascript" type="text/javascript">
f = document.form1;

function uf_nuevo()
{
  f.txtnumdoc.value = '';
  f.txtcodban.value = '';
  f.txtdenban.value = '';
  f.txtcuenta.value = '';
  f.cmbaccion[0].selected = true;
  f.txtfecmov.value = '';
  f.cmbcodope[0].selected = true;
  f.txtdenominacion.value = '';
  f.operacion.value = "NUEVO";
  f.action          = "sigesp_scb_p_procesar_no_contabilizables.php";
  f.submit();
}

function uf_catalogo_banco()
{
  pagina="sigesp_cat_bancos.php";
  window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=525,height=400,resizable=yes,location=no");
}

function uf_catalogo_cuenta_banco()
{
  ls_codban = f.txtcodban.value;
  ls_denban = f.txtdenban.value;
  if (ls_codban!="")
	 {
	   pagina="sigesp_cat_ctabanco.php?codigo="+ls_codban+"&hidnomban="+ls_denban;
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=720,height=400,resizable=yes,location=no");
	 }
  else
	 {
	   alert("Seleccione el Banco !!!");   
	 }
}	

function ue_search()
{
  li_leer = f.leer.value;
  if (li_leer==1)
     {
	   ls_accion = f.cmbaccion.value;
	   if (ls_accion!='-')
		  {
		    f.operacion.value = 'BUSCAR';
		    f.action          = "sigesp_scb_p_procesar_no_contabilizables.php";
		    f.submit();
		  }
	   else
		  {
		    alert("Por favor seleccione la Acción a realizar !!!");
		  }
	 }
  else
     {
	   alert("No tiene permiso para realizar esta Operación !!!");
	 }
} 

function ordenar_detalle(ls_parametro)
{
  li_totrows = f.hidtotrows.value;
  if (li_totrows>2)
     {
	   ls_numdoc = eval("f.txtnumdoc1.value");
	   if (ls_numdoc!='')
		  {
		    f.operacion.value = "BUSCAR";
		    ue_ordenar(ls_parametro);
		  }
	 }
}

function uf_select_all()
{
  li_totrows = f.hidtotrows.value;
  if (eval("f.txtnumdoc1.value!=''"))
     {
	   if (f.chkall.checked==true)
		  {
		    for (li_i=1;li_i<=li_totrows;li_i++)	
			    {
				  eval("f.chk"+li_i+".checked=true");			 
			    }
		    f.hidtotrowschecked.value = li_totrows;
		  }		
	   else
		  {
		    for (li_i=1;li_i<=li_totrows;li_i++)	
			    {
				  eval("f.chk"+li_i+".checked=false");
			    }
		    f.hidtotrowschecked.value = 0;
		  }
	 }
  else
     {
	   eval("f.chkall.checked=false");
	   eval("f.chk1.checked=false");
	 }
}

function uf_procesar_documentos()
{
  lb_checked  = false;
  li_totrows  = f.hidtotrows.value;
  li_procesar = f.ejecutar.value;
  if (li_procesar==1)
     {
	   for (li_i=1;li_i<=li_totrows;li_i++) 
		   {
			 if (eval("f.chk"+li_i+".checked==true"))
			    {
				  lb_checked = true;
				  break;
			    }
		   }
	   if (lb_checked)
		  {
		    f.operacion.value = 'PROCESAR';
		    f.action          = "sigesp_scb_p_procesar_no_contabilizables.php";
		    f.submit();
		  }
	   else
		  {
		    alert("Debe seleccionar al menos un Documento !!!");	 
		  }
	 }
  else
     {
	   alert("No tiene permiso para realizar esta Operación !!!");
	 }
}

function uf_select_documentos(li_row)
{
  li_totrowche = f.hidtotrowschecked.value;
  if (eval("f.txtnumdoc"+li_row+".value!=''"))
     {
	   if (eval("f.chk"+li_row+".checked==false"))
		  {
		    li_totrowche--;
			eval("f.chkall.checked=false");
		  }
	   else
		  {
		    li_totrowche++;
		  }
	   f.hidtotrowschecked.value = li_totrowche;
	 }
  else
     {
	   eval("f.chk"+li_row+".checked=false");
	 }
}
</script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>