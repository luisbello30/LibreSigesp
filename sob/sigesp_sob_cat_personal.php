<?php
session_start();
	//-----------------------------------------------------------------------------------------------------------------------------------
   	// Función que obtiene que tipo de operación se va a ejecutar
   	// NUEVO, GUARDAR, ó ELIMINAR
   	function uf_obteneroperacion()
   	{
		if(array_key_exists("operacion",$_POST))
		{
			$operacion=$_POST["operacion"];
		}
		else
		{
			$operacion="NUEVO";
		}
   		return $operacion; 
   	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
   	// Función que le da formato a los valore numéricos que vienen de la BD
	// parametro de entrada = Valor númerico que se desa formatear
	// parametro de retorno = valor numérico formateado
   	function uf_formatonumerico($as_valor)
   	{
		$li_poscoma = strpos($as_valor, ",");
		$li_contador = 0;
		if ($li_poscoma==0)
		{
			$li_poscoma = strlen($as_valor);
			$as_valor = $as_valor.",00";
		}
		for($li_index=$li_poscoma;$li_index>=0;--$li_index)
		{
			if(($li_contador==3)&&(($li_index-1)>0)) 
			{
				$as_valor = substr($as_valor,0,$li_index).".".substr($as_valor,$li_index);
				$li_contador=1;
			}
			else
			{
				$li_contador=$li_contador + 1;
			}
		}
		return $as_valor;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	// Función que obtiene e imprime los resultados de la busqueda
	function uf_imprimirresultados($as_codper, $as_cedper, $as_nomper, $as_apeper)
   	{
		require_once("../shared/class_folder/sigesp_include.php");
		require_once("../shared/class_folder/class_mensajes.php");
		require_once("../shared/class_folder/class_sql.php");
   		require_once("../shared/class_folder/class_funciones.php");
		
		$in=new sigesp_include();
		$con=$in->uf_conectar();
		$io_msg=new class_mensajes();
		$io_sql=new class_sql($con);
		$ds=new class_datastore();
		$fun=new class_funciones();				
       	$emp=$_SESSION["la_empresa"];
        $ls_codemp=$emp["codemp"];

		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60>Cédula</td>";
		print "<td width=440>Nombre y Apellido</td>";
		print "<td width=440>Cargo</td>";
		print "</tr>";
		$ls_sql="SELECT (CASE sno_nomina.racnom WHEN 1 THEN sno_asignacioncargo.denasicar ELSE sno_cargo.descar END) AS cargo,".
				"       sno_personalnomina.codper,".
				"(SELECT nomper FROM sno_personal".
				"   WHERE sno_personal.codper=sno_personalnomina.codper) as nomper,".
				"(SELECT apeper FROM sno_personal".
				"   WHERE sno_personal.codper=sno_personalnomina.codper) as apeper,".
				"(SELECT cedper FROM sno_personal".
				"   WHERE sno_personal.codper=sno_personalnomina.codper) as cedper".
				"  FROM sno_personalnomina, sno_nomina, sno_cargo, sno_asignacioncargo,sno_personal".
				" WHERE sno_personalnomina.codper LIKE '".$as_codper."'".
				"   AND sno_personal.cedper LIKE '".$as_cedper."'".
				"   AND sno_personal.nomper LIKE '".$as_nomper."'".
				"   AND sno_personal.apeper LIKE '".$as_apeper."'".
				"   AND sno_nomina.espnom=0".
				"   AND sno_personalnomina.codemp = sno_nomina.codemp".
				"   AND sno_personalnomina.codnom = sno_nomina.codnom".
				"   AND sno_personalnomina.codper = sno_personal.codper".
				"   AND sno_personalnomina.codemp = sno_cargo.codemp".
				"   AND sno_personalnomina.codnom = sno_cargo.codnom".
				"   AND sno_personalnomina.codcar = sno_cargo.codcar".
				"   AND sno_personalnomina.codemp = sno_asignacioncargo.codemp".
				"   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom".
				"   AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar".
				" GROUP BY sno_personalnomina.codper,sno_nomina.racnom,sno_asignacioncargo.denasicar,sno_cargo.descar,codclavia".
				" ORDER BY sno_personalnomina.codper,codclavia";
		$rs_per=$io_sql->select($ls_sql);
		$li_index=0;
		while($row=$io_sql->fetch_row($rs_per))
		{
			$li_index=$li_index+1;
			print "<tr class=celdas-blancas>";
			$ls_codper=$row["codper"];
			$ls_cedper=$row["cedper"];
			$ls_nomper=$row["nomper"];
			$ls_apeper=$row["apeper"];				
			$ls_cargo=$row["cargo"];				
			print "<td><a href=\"javascript: aceptar('$ls_codper','$ls_cedper','$ls_nomper','$ls_apeper',".
				  "               '$ls_cargo');\">".$ls_cedper."</a></td>";
			print "<td>".$ls_nomper."  ".$ls_apeper."</td>";
			print "<td>".$ls_cargo."</td>";
			print "</tr>";			
		}
		if($li_index==0)
		{
			$io_msg->message("No hay nada que reportar");
		}

		print "</table>";
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catalogo de Personal</title>
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
    <input name="hidlinea" type="hidden" id="hidlinea" value="<?php print $li_linea?>">
  </p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Personal </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="1" class="formato-blanco" align="center">
      <tr>
        <td width="67"><div align="right">C&oacute;digo</div></td>
        <td width="431" height="22"><div align="left">
          <input name="txtcodper" type="text" id="txtcodper" size="30" maxlength="10">        
        </div></td>
      </tr>
      <tr>
        <td><div align="right">C&eacute;dula</div></td>
        <td height="22"><input name="txtcedper" type="text" id="txtcedper" size="30" maxlength="10"></td>
      </tr>
      <tr>
        <td><div align="right">Nombre</div></td>
        <td height="22"><input name="txtnomper" type="text" id="txtnomper" size="30" maxlength="60"></td>
      </tr>
      <tr>
        <td><div align="right">Apellido</div></td>
        <td height="22"><div align="left">
          <input name="txtapeper" type="text" id="txtapeper" size="30" maxlength="60">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="left"></div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();">
          <img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
  <br>
<?php
	$ls_operacion=uf_obteneroperacion();
	if($ls_operacion=="BUSCAR")
	{
		$ls_codper="%".$_POST["txtcodper"]."%";
		$ls_cedper="%".$_POST["txtcedper"]."%";
		$ls_nomper="%".$_POST["txtnomper"]."%";
		$ls_apeper="%".$_POST["txtapeper"]."%";

		uf_imprimirresultados($ls_codper, $ls_cedper, $ls_nomper, $ls_apeper);
	}
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function aceptar(ls_codper,ls_cedper,ls_nomper,ls_apeper,ls_cargo)
{
	opener.document.form1.txtnominsact.value=ls_nomper+" "+ls_apeper;
	opener.document.form1.txtcedinsact.value=ls_cedper;
	close();

}
function ue_search()
{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_sob_cat_personal.php";
  	f.submit();
}
</script>
</html>
