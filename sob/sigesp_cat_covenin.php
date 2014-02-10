<?
session_start();
if((!array_key_exists("ls_database",$_SESSION))||(!array_key_exists("ls_hostname",$_SESSION))||(!array_key_exists("ls_gestor",$_SESSION))||(!array_key_exists("ls_login",$_SESSION)))
{
	print "<script language=JavaScript>";
	print "opener.location.href='../sigesp_conexion.php';";
	print "close();";
	print "</script>";		
} 
$la_datemp=$_SESSION["la_empresa"];
if(!array_key_exists("campo",$_POST))
{
	$ls_campo="u.coduni";
	$ls_orden="ASC";
}
else
{
	$ls_campo=$_POST["campo"];
	$ls_orden=$_POST["orden"];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo Covenin</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" language="JavaScript1.2" src="js/number_format.js"></script>
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="js/validaciones.js"></script>
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
	color: #006699#006699;
}
-->
</style></head>

<body>
<form name="form1" method="post" action="">
<input type="hidden" name="campo" id="campo" value="<? print $ls_campo;?>" >
<input type="hidden" name="orden" id="orden" value="<? print $ls_orden;?>">
<?

require_once("../shared/class_folder/sigesp_include.php");
require_once("../shared/class_folder/class_mensajes.php");
require_once("../shared/class_folder/class_sql.php");
require_once("../shared/class_folder/class_funciones.php");
require_once("class_folder/sigesp_sob_c_unidad.php");
$io_include=new sigesp_include();
$io_connect=$io_include->uf_conectar();
$io_msg=new class_mensajes();
$io_sql=new class_sql($io_connect);
$io_data=new class_datastore();
$io_funcion=new class_funciones();
$io_cunid=new sigesp_sob_c_unidad();
$ls_codemp=$la_datemp["codemp"];

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codcov=$_POST["txtcodcov"];
	$ls_descov=$_POST["txtdescov"];

}
else
{
	$ls_operacion="";
	$ls_codcov="";
	$ls_descov="";
}

?>
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
</p>
  	 <table width="600" border="0" align="center" cellpadding="1" cellspacing="1">
    	<tr>
     	 	<td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo Covenin</td>
    	</tr>
	 </table>
	 <br>
	 <table width="600" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="28"><div align="right">Tipo Unidad </div></td>
        <td width="431"><div align="left">
          <input name="txtcodcov" type="text" value="<?php print $ls_codcov; ?>">
        </div></td>
      </tr>
      <tr>
        <td height="26"><div align="right">Nombre</div></td>
        <td><div align="left">
          <input name="txtdescov" type="text" id="devcov" value="<?php print $ls_descov;?>" size="60">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
    </table>
	<br>
    <?


if($ls_operacion=="BUSCAR")
{

 $ls_cadena=" SELECT codcoven,descov ".
			"  FROM sob_covenin".
			" WHERE codemp='".$ls_codemp."'".
			"   AND codcoven like '%".$ls_codcov."%'".
			"   AND descov like '%".$ls_descov."%'";
//print $ls_cadena;			
			$rs_datauni=$io_sql->select($ls_cadena);
			if($rs_datauni==false&&($io_sql->message!=""))
			{
				$io_msg->message("No hay registros");
			}
			else
			{
				
				if($row=$io_sql->fetch_row($rs_datauni))
				{
					print "<table width=600 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
					print "<tr class=titulo-celda>";
					print "<td>Código</td>";
					print "<td>Descripción</td>";
					while(!$rs_datauni->EOF)
					{
						print "<tr class=celdas-blancas>";
						$codcove=$rs_datauni->fields["codcoven"];
						$descov=$rs_datauni->fields["descov"];
						print "<td><a href=\"javascript: aceptar('$codcove','$descov');\">".$codcove."</a></td>";
						print "<td>".$descov."</td>";
					    print "</tr>";	
						$rs_datauni->MoveNext();		
					}
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
	function aceptar(codcov,descov)
	{
		opener.document.form1.txtcodcovpar.value=codcov;
		opener.document.form1.txtdescov.value=descov;
		close();
	}
  function ue_search()
  {
  f=document.form1;
  f.operacion.value="BUSCAR";
  f.action="sigesp_cat_covenin.php";
  f.submit();
  }
</script>
</html>
