<?php
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Cuentas Contable de Provisiones  Acumuladas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
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
<link href="../../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
  </p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" height="15" colspan="2" class="titulo-celda">Cat&aacute;logo de Cuentas Contable de Provisiones  Acumuladas &nbsp;&nbsp;&nbsp;&nbsp; y Reservas T&eacute;cnicas o de &nbsp;Depreciaci&oacute;n y Amortizaci&oacute;n Acumulada</td>
    </tr>
  </table>
<br>
<br>
    <?php
require_once("../../shared/class_folder/sigesp_include.php");
$in=     new sigesp_include();
$con=$in->uf_conectar();
require_once("../../shared/class_folder/class_mensajes.php");
$io_msg= new class_mensajes();
require_once("../../shared/class_folder/class_datastore.php");
$ds=     new class_datastore();
require_once("../../shared/class_folder/class_sql.php");
$io_sql= new class_sql($con);
require_once("../../shared/class_folder/class_funciones.php");
$io_fun= new class_funciones();
$ls_codemp=$_SESSION["la_empresa"]["codemp"];
$ls_cueproacu=$_SESSION["la_empresa"]["cueproacu"];
$ls_cuedepamo=$_SESSION["la_empresa"]["cuedepamo"];

print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td width='150'>Código </td>";
print "<td>Denominación</td>";
print "</tr>";
	$li_i=0;
	$ls_sql="SELECT sc_cuenta,denominacion".
			"  FROM scg_cuentas".
			" WHERE codemp= '".$ls_codemp."'".
			"   AND status='C'".
			"   AND (sc_cuenta LIKE '".$ls_cueproacu."%' OR sc_cuenta LIKE '".$ls_cuedepamo."%')";
            
	$rs_data=$io_sql->select($ls_sql);
	while($row=$io_sql->fetch_row($rs_data))
	{
		$li_i++;
		$ls_sccuenta= $row["sc_cuenta"];
		$ls_denominacion= $row["denominacion"];
		print "<tr class=celdas-blancas>";
		print " <td align='center'><a href=\"javascript: aceptar('$ls_sccuenta','$ls_denominacion');\">".$ls_sccuenta."</a></td>";
		print "<td>".$ls_denominacion."</td>";
		print "</tr>";			
	}
	if($li_i==0)
	{
		$io_msg->message("No hay registros.");
	}
print "</table>";

?>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">   
	function aceptar(ls_sccuenta,ls_denominacion)
	{
		opener.document.form1.txtcueproacu.value=ls_sccuenta;
	//	opener.document.form1.txtdenconcom.value=ls_denconcom;
		close();
	}
  
	function ue_search()
	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_scg_cat_provisionesacumuladas.php";
		f.submit();
	}
  

</script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>
