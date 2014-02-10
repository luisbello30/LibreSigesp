<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cat&aacute;logo de Cambio de Responsables</title>
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
<form id="sigesp_saf_cat_cambio_responsable.php" name="form1" method="post" action="">
  <p>&nbsp;</p>
  <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td height="18" colspan="4" class="titulo-celda">Cat&aacute;logo de Cambio de Responsables 
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
	 $ls_cmpmov    = $_POST["txtcmpmov"];
	 $ls_fecdes    = $_POST["txtdesde"];
	 $ls_fechas    = $_POST["txthasta"];
   }
else
   {
	 $ls_operacion="BUSCAR";
	 $ls_cmpmov = "";
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
		  $ls_sqlaux = " AND feccam BETWEEN '".$ls_fecdes."' AND '".$ls_fechas."'";
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
	 
	 $ls_sql = "SELECT cmpmov, feccam, obstra, codusureg, codres, codresnew, codact, idact,
					   (SELECT $ls_strsql
						  FROM sno_personal
						 WHERE sno_personal.codemp=saf_cambioresponsable.codemp
						   AND sno_personal.codper=saf_cambioresponsable.codres) AS nomres1,
					   (SELECT $ls_strsql
						  FROM sno_personal
						 WHERE sno_personal.codemp=saf_cambioresponsable.codemp
						   AND sno_personal.codper=saf_cambioresponsable.codresnew) AS nomresnew1,
					   (SELECT $ls_straux
						  FROM rpc_beneficiario
						 WHERE rpc_beneficiario.codemp=saf_cambioresponsable.codemp
						   AND rpc_beneficiario.ced_bene=saf_cambioresponsable.codres) AS nomres2,
					   (SELECT $ls_straux
						  FROM rpc_beneficiario
						 WHERE rpc_beneficiario.codemp=saf_cambioresponsable.codemp
						   AND rpc_beneficiario.ced_bene=saf_cambioresponsable.codresnew) AS nomresnew2,
					   (SELECT denact 
					      FROM saf_activo 
						 WHERE saf_cambioresponsable.codemp=saf_activo.codemp
						   AND saf_cambioresponsable.codact=saf_activo.codact) as denact,
					   (SELECT seract 
					      FROM saf_dta 
						 WHERE saf_cambioresponsable.codemp=saf_dta.codemp
						   AND saf_cambioresponsable.codact=saf_dta.codact
						   AND saf_cambioresponsable.idact=saf_dta.ideact) as seract
			      FROM saf_cambioresponsable 
				 WHERE codemp = '".$ls_codemp."' 
			       AND cmpmov like '%".$ls_cmpmov."%' $ls_sqlaux
			     ORDER BY cmpmov ASC"; 

	  $rs_data = $io_sql->select($ls_sql);//echo $ls_sql.'<br>';
	  if ($rs_data===false)
	     {
		   $io_msg->message("Error en Consulta, Contacte al Administrador del Sistema !!!");
		 }
      else
	     {
		   $li_totrows = $io_sql->num_rows($rs_data);
		   if ($li_totrows>0)
		      {
			    while(!$rs_data->EOF)
				     {
					   echo "<tr class=celdas-blancas>";
			           $ls_cmpmov 	 = $rs_data->fields["cmpmov"];
			           $ls_feccmp 	 = $io_funcion->uf_convertirfecmostrar($rs_data->fields["feccam"]);
			           $ls_obstra 	 = $rs_data->fields["obstra"];
			           $ls_codres 	 = $rs_data->fields["codres"];
			           $ls_codact 	 = $rs_data->fields["codact"];
			           $ls_ideact 	 = $rs_data->fields["idact"];
					   $ls_seract 	 = $rs_data->fields["seract"];
			           $ls_denact 	 = $rs_data->fields["denact"];
					   $ls_codusureg = $rs_data->fields["codusureg"];
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
				       echo "<td style=text-align:center width=100><a href=\"javascript: uf_aceptar('$ls_cmpmov','$ls_feccmp','$ls_obstra','$ls_codusureg','$ls_codres','$ls_nomres','$ls_codresnew','$ls_nomresnew','$ls_codact','$ls_denact','$ls_ideact','$ls_seract');\">".$ls_cmpmov."</a></td>";
				       echo "<td style=text-align:left title='".$ls_obstra."' width=350>".$ls_obstra."</td>";
					   echo "<td style=text-align:left width=50>".$ls_feccmp."</td>";
				       echo "</tr>";
                       $rs_data->MoveNext();
					 }
			  }
		   else
		      {
				$io_msg->message("No se han efectuado Cambios de Responsables !!!");
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
function uf_aceptar(as_cmpmov,as_feccam,as_obstra,as_codusureg,as_codres,as_nomres,as_codresnew,as_nomresnew,as_codact,as_denact,as_ideact,as_seract)
{
  fop = opener.document.form1;
  fop.txtcmpmov.value = as_cmpmov;
  fop.txtcodact.value = as_codact;
  fop.txtdenact.value = as_denact;
  fop.txtideact.value = as_ideact;
  fop.txtseract.value = as_seract;
  fop.txtcodres.value = as_codres;
  fop.txtcodresnew.value = as_codresnew;
  fop.txtnomres.value = as_nomres;
  fop.txtnomresnew.value = as_nomresnew;
  fop.txtobstra.value = as_obstra;
  fop.txtfeccam.value = as_feccam;
  close();
}

function uf_buscar()
{
  f.action = "sigesp_saf_cat_cambio_responsable.php";
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
