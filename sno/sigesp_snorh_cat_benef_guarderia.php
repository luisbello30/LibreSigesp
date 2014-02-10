<?php
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catalogo de Beneficiarios</title>
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
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
</p>
  	 <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    	<tr>
     	 	<td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Beneficiarios </td>
    	</tr>
	 </table>
	 <br>
	 <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67"><div align="right">Codigo</div></td>
        <td width="431" height="22"><div align="left">
          <input name="codigo" type="text" id="codigo">        
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Nombre</div></td>
        <td height="22"><div align="left">
          <input name="nombre" type="text" id="nombre">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
    </table>
	<br>
<?php
require_once("../shared/class_folder/sigesp_include.php");
require_once("../shared/class_folder/class_sql.php");
require_once("../shared/class_folder/class_mensajes.php");
require_once("class_folder/class_funciones_nomina.php");
$io_fun_nomina=new class_funciones_nomina();
$in=new sigesp_include();
$con=$in->uf_conectar();
$msg=new class_mensajes();
$io_sql=new class_sql($con);
$ds=new class_datastore();
$ls_tipo=$io_fun_nomina->uf_obtenertipo();

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codigo="%".$_POST["codigo"]."%";
	$ls_nombre="%".$_POST["nombre"]."%";
}
else
{
	$ls_operacion="";
	$ls_nombre="";
	$ls_codigo="";
}
print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td>Código </td>";
print "<td>Nombre del Beneficiario</td>";
print "</tr>";
$ls_nombre=strtoupper($ls_nombre);
if($ls_operacion=="BUSCAR")
{
	$ls_sql="SELECT * FROM rpc_beneficiario".
			" WHERE ced_bene like '".$ls_codigo."'".
			" AND nombene like '".$ls_nombre."' ".
			" order by ced_bene";////Modificado el di 04/12/2007
	$rs_data=$io_sql->select($ls_sql);	
	while($row=$io_sql->fetch_row($rs_data))
	{
		$ls_codpro=$row["ced_bene"];
		switch ($ls_tipo)
		{
			case "":
				if($ls_codpro!="----------")
				{
					print "<tr class=celdas-blancas>";
					$ls_codpro=$row["ced_bene"];
					$ls_nompro=$row["nombene"];
					$ls_apebene=$row["apebene"];
					$ls_nomcomp=$ls_apebene."   ".$ls_nompro;			
					print "<td><a href=\"javascript: aceptar('$ls_codpro','$ls_nomcomp');\">".$ls_codpro."</a></td>";
					print "<td>".$ls_nomcomp."</td>";
					print "</tr>";
				}
			break;
			
			case "srhguard":
				if($ls_codpro!="----------")
				{
					print "<tr class=celdas-blancas>";
					$ls_codpro=$row["ced_bene"];
					$ls_nompro=$row["nombene"];
					$ls_apebene=$row["apebene"];
					$ls_nomcomp=$ls_apebene."   ".$ls_nompro;			
					print "<td><a href=\"javascript: aceptarsrh('$ls_codpro','$ls_nomcomp');\">".$ls_codpro."</a></td>";
					print "<td>".$ls_nomcomp."</td>";
					print "</tr>";
				}
			break;
		}		
	}
}
print "</table>";
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
  function aceptar(ls_codpro,ls_nomcomp)
  {
    opener.document.form1.txtbeneficiario.value=ls_codpro;
    opener.document.form1.txtdenbene.value=ls_nomcomp;
	//opener.buscar();
	close();
  }
  function aceptarsrh(ls_codpro,ls_nomcomp)
  {
    opener.document.form1.txtguarbene.value=ls_codpro;
    opener.document.form1.txtnomguarben.value=ls_nomcomp;
	//opener.buscar();
	close();
  }

  function ue_search()
  {
  f=document.form1;
  f.operacion.value="BUSCAR";
  f.action="sigesp_snorh_cat_benef_guarderia.php?tipo=<?php print $ls_tipo;?>";
  f.submit();
  }
</script>
</html>