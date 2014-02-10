<?php
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Sucursales </title>
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
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celdanew">Cat&aacute;logo de Sucursales </td>
    </tr>
</table>
  <br>
<?php
require_once("../shared/class_folder/sigesp_include.php");
$io_conect=new sigesp_include();
$con=$io_conect->uf_conectar();
require_once("../shared/class_folder/class_datastore.php");
$io_dsmoneda=new class_datastore();
require_once("../shared/class_folder/class_sql.php");
$io_sql=new class_sql($con);
$la_emp=$_SESSION["la_empresa"];
	if (array_key_exists("operacion",$_POST))
	   {
		 $ls_operacion=$_POST["operacion"];
		 $ls_codigo="%".$_POST["txtcodigo"]."%";
	   }
	else
	   {
		 $ls_operacion="";
	   }
?>
<form name="form1" method="post" action="">
<table width="498" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="88" height="15" align="right">&nbsp;</td>
        <td width="149">&nbsp;        </td>
        <td width="159" align="right">&nbsp;</td>
      </tr>
      <tr>
        <td height="18" align="right">C&oacute;digo</td>
        <td><input name="txtcodigo" type="text" id="txtcodigo" style="text-align:center"  maxlength="6">
        <input name="operacion" type="hidden" id="operacion"></td>
        <td align="right"><div align="left"><a href="javascript: ue_search();"></a></div></td>
      <tr>
        <td height="18" align="right">Nombre</td>
        <td colspan="2"><input name="txtnombre" type="text" id="txtnombre" size="40"></td>
      <tr>
        <td height="18" align="right">&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0" onClick="ue_search()">Buscar</a></td>
  </table> 
</form>      
<div align="center">
<?php
print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td>Código</td>";
print "<td>Nombre</td>";
print "</tr>";
$li_len1=$_SESSION["la_empresa"]["loncodestpro1"];
$li_len2=$_SESSION["la_empresa"]["loncodestpro2"];
$li_len3=$_SESSION["la_empresa"]["loncodestpro3"];
$li_len4=$_SESSION["la_empresa"]["loncodestpro4"];
$li_len5=$_SESSION["la_empresa"]["loncodestpro5"];
if ($ls_operacion=="BUSCAR")
   {
		$ls_codigo="%".$_POST["txtcodigo"]."%";
		$ls_nombre="%".$_POST["txtnombre"]."%";
		
		$ls_sql= " SELECT sigesp_sucursales.codsuc,sigesp_sucursales.nomsuc,sigesp_sucursales.codestpro1,".
				 "        sigesp_sucursales.codestpro2,sigesp_sucursales.codestpro3,sigesp_sucursales.codestpro4,".
				 "        sigesp_sucursales.codestpro5,sigesp_sucursales.estcla, spg_ep1.denestpro1, spg_ep2.denestpro2,".
				 "        spg_ep3.denestpro3, spg_ep4.denestpro4, spg_ep5.denestpro5".
		         " FROM sigesp_sucursales,spg_ep1,spg_ep2,spg_ep3,spg_ep4,spg_ep5 ".
				 " WHERE codsuc like '".$ls_codigo."' ".
				 "   AND nomsuc like '".$ls_nombre."' ".
				 "   AND sigesp_sucursales.codemp=spg_ep1.codemp".
				 "   AND sigesp_sucursales.codestpro1=spg_ep1.codestpro1".
				 "   AND sigesp_sucursales.codemp=spg_ep2.codemp".
				 "   AND sigesp_sucursales.codestpro1=spg_ep2.codestpro1".
				 "   AND sigesp_sucursales.codestpro2=spg_ep2.codestpro2".
				 "   AND sigesp_sucursales.codemp=spg_ep3.codemp".
				 "   AND sigesp_sucursales.codestpro1=spg_ep3.codestpro1".
				 "   AND sigesp_sucursales.codestpro2=spg_ep3.codestpro2".
				 "   AND sigesp_sucursales.codestpro3=spg_ep3.codestpro3".
				 "   AND sigesp_sucursales.codemp=spg_ep4.codemp".
				 "   AND sigesp_sucursales.codestpro1=spg_ep4.codestpro1".
				 "   AND sigesp_sucursales.codestpro2=spg_ep4.codestpro2".
				 "   AND sigesp_sucursales.codestpro3=spg_ep4.codestpro3".
				 "   AND sigesp_sucursales.codestpro4=spg_ep4.codestpro4".
				 "   AND sigesp_sucursales.codemp=spg_ep5.codemp".
				 "   AND sigesp_sucursales.codestpro1=spg_ep5.codestpro1".
				 "   AND sigesp_sucursales.codestpro2=spg_ep5.codestpro2".
				 "   AND sigesp_sucursales.codestpro3=spg_ep5.codestpro3".
				 "   AND sigesp_sucursales.codestpro4=spg_ep5.codestpro4".
				 "   AND sigesp_sucursales.codestpro5=spg_ep5.codestpro5".
				 " ORDER BY codsuc";
		$rs_data=$io_sql->select($ls_sql);
		while($row=$io_sql->fetch_row($rs_data))
		{
	  	   print "<tr class=celdas-blancas>";
		   $ls_codsuc=$row["codsuc"];			  
		   $ls_nomsuc=$row["nomsuc"];
		   $ls_codestpro1=substr($row["codestpro1"],(strlen($row["codestpro1"])-$li_len1),$li_len1);
		   $ls_codestpro2=substr($row["codestpro2"],(strlen($row["codestpro2"])-$li_len2),$li_len2);
		   $ls_codestpro3=substr($row["codestpro3"],(strlen($row["codestpro3"])-$li_len3),$li_len3);
		   $ls_codestpro4=substr($row["codestpro4"],(strlen($row["codestpro4"])-$li_len4),$li_len4);
		   $ls_codestpro5=substr($row["codestpro5"],(strlen($row["codestpro5"])-$li_len4),$li_len4);
		   $ls_estcla=$row["estcla"];
		   $ls_denestpro1=$row["denestpro1"];
		   $ls_denestpro2=$row["denestpro2"];
		   $ls_denestpro3=$row["denestpro3"];
		   $ls_denestpro4=$row["denestpro4"];
		   $ls_denestpro5=$row["denestpro5"];
		   print "<td><a href=\"javascript: aceptar('$ls_codsuc','$ls_nomsuc','$ls_codestpro1','$ls_codestpro2',".
				 "'$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$ls_denestpro1','$ls_denestpro2',".
				 "'$ls_denestpro3','$ls_denestpro4','$ls_denestpro5');\">".$ls_codsuc."</a></td>";
		   print "<td  align=center>".$ls_nomsuc."</td>";
		   print "</tr>";			
		}
	   print "</table>";
 /*  	if ($row=$io_sql->fetch_row($rs_data))
		   {
			 $data=$io_sql->obtener_datos($rs_data);
			 $arrcols=array_keys($data);
			 $totcol=count($arrcols);
			 $io_ds->data=$data;
			
			 $totrow=$io_ds->getRowCount("codmon");
			 for ($z=1;$z<=$totrow;$z++)
				 {
			     }
		   //$io_sql->free_result($data);
		   }
		else
		 { ?>
		   <script  language="javascript">
		   alert("No se han creado Sucursales !!!");
		   </script>
		 <?php
		 }  */
}
?>
</div>
</body>
<script language="JavaScript">
  function aceptar(ls_codsuc,ls_nomsuc,ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,
  				   ls_denestpro1,ls_denestpro2,ls_denestpro3,ls_denestpro4,ls_denestpro5)
  {
    opener.document.formulario.txtcodsuc.value=ls_codsuc;
	opener.document.formulario.txtcodsuc.readOnly=true;
	opener.document.formulario.txtnomsuc.value=ls_nomsuc;
	opener.document.formulario.txtcodestpro1.value=ls_codestpro1;
	opener.document.formulario.txtcodestpro2.value=ls_codestpro2;
	opener.document.formulario.txtcodestpro3.value=ls_codestpro3;
	opener.document.formulario.txtcodestpro4.value=ls_codestpro4;
	opener.document.formulario.txtcodestpro5.value=ls_codestpro5;
	opener.document.formulario.hidestcla.value=ls_estcla;
	opener.document.formulario.txtdenestpro1.value=ls_denestpro1;
	opener.document.formulario.txtdenestpro2.value=ls_denestpro2;
	opener.document.formulario.txtdenestpro3.value=ls_denestpro3;
	opener.document.formulario.txtdenestpro4.value=ls_denestpro4;
	opener.document.formulario.txtdenestpro5.value=ls_denestpro5;
	opener.document.formulario.status.value='C';
	
	close();
  }
  
  function ue_search()
  {
    f=document.form1;
    f.operacion.value="BUSCAR";
    f.action="sigesp_cfg_cat_sucursales.php";
    f.submit();
  }
</script>
</html>