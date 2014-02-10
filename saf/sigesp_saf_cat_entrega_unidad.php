<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cat&aacute;logo de Entregas de Unidad</title>
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/general.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css" />
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/valida_tecla.js"></script>
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
<form id="sigesp_saf_cat_entrega_unidad.php" name="form1" method="post" action="">
  <p>&nbsp;</p>
  <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td height="18" colspan="4" class="titulo-celda">Cat&aacute;logo de Entregas de Unidad
          <input name="operacion" type="hidden" id="operacion" /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td height="13">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="18">&nbsp;</td>
      <td width="148" rowspan="3"><div align="center">
        <table width="145" border="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td colspan="2"><div align="center" class="titulo-conect">Rango de Fechas </div></td>
          </tr>
          <tr>
            <td width="36"><div align="right">Desde</div></td>
            <td width="103" height="22"><input name="txtdesde" type="text" id="txtdesde" size="15"  datepicker="true"  onkeypress="ue_separadores(this,'/',patron,true);" style="text-align:center" /></td>
          </tr>
          <tr>
            <td><div align="right">Hasta</div></td>
            <td height="22"><input name="txthasta" type="text" id="txthasta" size="15"  datepicker="true"  onkeypress="ue_separadores(this,'/',patron,true);" style="text-align:center" /></td>
          </tr>
        </table>
      </div></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="120" style="text-align:right">Comprobante</td>
      <td width="162" height="22"><div align="left">
          <input name="txtcmpmov" type="text" id="txtcmpmov" style="text-align:center" onKeyPress="return keyRestrict(event,'1234567890');" onBlur="javascript:rellenar_cadena(this.value,15,this.name);" />
      </div></td>
      <td width="68">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="22">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="3"><div align="right"><a href="javascript: uf_buscar();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0" />Buscar</a></div></td>
    </tr>
  </table>
  <p align="center">
<?php
require_once("../shared/class_folder/class_sql.php");
require_once("../shared/class_folder/sigesp_include.php");
require_once("../shared/class_folder/class_mensajes.php");
require_once("../shared/class_folder/class_funciones.php");

$io_include = new sigesp_include();
$ls_conect  = $io_include->uf_conectar();
$io_msg     = new class_mensajes();
$io_sql     = new class_sql($ls_conect);
$io_funcion = new class_funciones();
$ls_codemp  = $_SESSION["la_empresa"]["codemp"];
if (array_key_exists("operacion",$_POST))
   {
	 $ls_operacion = $_POST["operacion"];
	 $ls_cmpent    = $_POST["txtcmpmov"];
	 $ls_fecdes    = $_POST["txtdesde"];
	 $ls_fechas    = $_POST["txthasta"];
   }
else
   {
	 $ls_operacion="BUSCAR";
	 $ls_cmpent = "";
	 $ls_fecdes = "";
	 $ls_fechas = "";	 
   }

echo "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
echo "<tr class=titulo-celda>";
echo "<td style=text-align:center width=100>Comprobante</td>";
echo "<td style=text-align:center width=350>Observación</td>";
echo "<td style=text-align:center width=50>Fecha</td>";
echo "</tr>";
if ($ls_operacion=="BUSCAR")
   {
     $ls_sqlaux = "";
	 if (!empty($ls_fecdes) && !empty($ls_fechas))
	    {
		  $ls_fecdes = $io_funcion->uf_convertirdatetobd($ls_fecdes);
		  $ls_fechas = $io_funcion->uf_convertirdatetobd($ls_fechas);
		  $ls_sqlaux = " AND fecentuni BETWEEN '".$ls_fecdes."' AND '".$ls_fechas."'";
		}
	 
	 switch($_SESSION["ls_gestor"]){
	   case "MYSQLT":
	     $ls_strsql = "CONCAT(apeper,',',nomper)";
		 $ls_straux = "CONCAT(apebene,',',nombene)";
	   break;
	   case "POSTGRES":
	     $ls_strsql = "apeper||','||nomper";
		 $ls_straux = "apebene||','||nombene";
	   break;
	 }
	 
	 $ls_sql = "SELECT cmpent, fecentuni, codusureg, coduniadm, codres, codresnew, obsentuni,
					   (SELECT $ls_strsql
						  FROM sno_personal
						 WHERE sno_personal.codemp=saf_entregauniadm.codemp
						   AND sno_personal.codper=saf_entregauniadm.codres) AS nomres1,
					   (SELECT $ls_strsql
						  FROM sno_personal
						 WHERE sno_personal.codemp=saf_entregauniadm.codemp
						   AND sno_personal.codper=saf_entregauniadm.codresnew) AS nomresnew1,
					   (SELECT $ls_straux
						  FROM rpc_beneficiario
						 WHERE rpc_beneficiario.codemp=saf_entregauniadm.codemp
						   AND rpc_beneficiario.ced_bene=saf_entregauniadm.codres) AS nomres2,
					   (SELECT $ls_straux
						  FROM rpc_beneficiario
						 WHERE rpc_beneficiario.codemp=saf_entregauniadm.codemp
						   AND rpc_beneficiario.ced_bene=saf_entregauniadm.codresnew) AS nomresnew2,
					   (SELECT denuniadm 
					      FROM spg_unidadadministrativa 
						 WHERE spg_unidadadministrativa.codemp=saf_entregauniadm.codemp
						   AND spg_unidadadministrativa.coduniadm=saf_entregauniadm.coduniadm) as denuniadm
			      FROM saf_entregauniadm 
				 WHERE codemp = '".$ls_codemp."' 
			       AND cmpent like '%".$ls_cmpent."%' $ls_sqlaux
			     ORDER BY cmpent ASC"; 

	  $rs_data = $io_sql->select($ls_sql);//echo $ls_sql.'<br>';
	  if ($rs_data===false)
	     {
		   $io_msg->message("Error en Consulta, Contacte al Administrador del Sistema !!!");print $io_sql->message;
		 }
      else
	     {
		   $li_totrows = $io_sql->num_rows($rs_data);
		   if ($li_totrows>0)
		      {
			    while(!$rs_data->EOF)
				     {
					   echo "<tr class=celdas-blancas>";
			           $ls_cmpent 	 = $rs_data->fields["cmpent"];
			           $ls_fecentuni = $io_funcion->uf_convertirfecmostrar($rs_data->fields["fecentuni"]);
			           $ls_codusureg = $rs_data->fields["codusureg"];
					   $ls_coduniadm = $rs_data->fields["coduniadm"];
					   $ls_denuniadm = $rs_data->fields["denuniadm"];
					   $ls_codres 	 = $rs_data->fields["codres"];
					   $ls_obsentuni = $rs_data->fields["obsentuni"];
			           $ls_codresnew = $rs_data->fields["codresnew"];
					   $ls_nomres    = $rs_data->fields["nomres1"];
					   if (empty($ls_nomres))
					      {
						    $ls_nomres = $rs_data->fields["nomres2"];
						  }
					   $ls_nomresnew = $rs_data->fields["nomresnew1"];
					   if (empty($ls_nomresnew))
					      {
						    $ls_nomresnew = $rs_data->fields["nomresnew2"];
						  }
				       echo "<td style=text-align:center width=100><a href=\"javascript: uf_aceptar('$ls_cmpent','$ls_fecentuni','$ls_obsentuni','$ls_codusureg','$ls_codres','$ls_nomres','$ls_codresnew','$ls_nomresnew','$ls_coduniadm','$ls_denuniadm');\">".$ls_cmpent."</a></td>";
				       echo "<td style=text-align:left title='".$ls_obsentuni."' width=350>".$ls_obsentuni."</td>";
					   echo "<td style=text-align:left width=50>".$ls_fecentuni."</td>";
				       echo "</tr>";
                       $rs_data->MoveNext();
					 }
			  }
		   else
		      {
				$io_msg->message("No se han efectuado Entregas de Unidad !!!");
			  }
		 }  		 
   }
echo "</table>";
?>
  </p>
</form>
</body>
<script language="javascript">
f = document.form1;
function uf_aceptar(as_cmpent,as_fecentuni,as_obsentuni,as_codusureg,as_codres,as_nomres,as_codresnew,as_nomresnew,as_coduniadm,as_denuniadm)
{
  fop = opener.document.form1;
  fop.txtcmpent.value    = as_cmpent;
  fop.txtcoduniadm.value = as_coduniadm;
  fop.txtdenuniadm.value = as_denuniadm;
  fop.txtcodresact.value = as_codres;
  fop.txtcodresnew.value = as_codresnew;
  fop.txtnomresact.value = as_nomres;
  fop.txtnomresnew.value = as_nomresnew;
  fop.txtobsentuni.value = as_obsentuni;
  fop.txtfecentuni.value = as_fecentuni;
  close();
}

function uf_buscar()
{
  f.action = "sigesp_saf_cat_entrega_unidad.php";
  f.operacion.value = "BUSCAR";
  f.submit();
}

function rellenar_cadena(cadena,longitud,objeto)
{
var mystring = new String(cadena);
cadena_ceros = "";
lencad       = mystring.length;
total        = longitud-lencad;
if (cadena!='')
   {
	 for (i=1;i<=total;i++)
		 {
		  cadena_ceros=cadena_ceros+"0";
		 }
	 cadena = cadena_ceros+cadena;   
	 if (objeto=='txtcmpmov')
	    {
		  document.form1.txtcmpmov.value=cadena;
		}
   }
}
</script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>
