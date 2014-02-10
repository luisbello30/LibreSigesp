<?php
session_start();
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "close();";
	print "opener.document.form1.submit();";
	print "</script>";		
}
require_once("../shared/class_folder/sigesp_include.php");
require_once("../shared/class_folder/class_sql.php");
$in     = new sigesp_include();
$con    = $in->uf_conectar();
$io_sql = new class_sql($con);
$arr    = $_SESSION["la_empresa"];

if (array_key_exists("operacion",$_POST))
   {
	 $ls_operacion = $_POST["operacion"];
	 $ls_codigo    = $_POST["banco"];
	 //$ls_obj       = $_POST["obj"]; 
	 $ls_ctabanco  = $_POST["codigo"];
	 $ls_numdoc    = $_POST["codigodoc"];
   }
else
   {
	 $ls_operacion = "BUSCAR";
	 //$ls_obj       = $_GET["obj"];
	 $ls_codigo    = $_GET["banco"];
	 $ls_ctabanco  = $_GET["codigo"];
	 $ls_numdoc    = $_GET["codigodoc"];
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catalogo de Documentos</title>
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
  	 <br>
	 <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td height="22" colspan="2" class="titulo-celda">Cat&aacute;logo de Documentos</td>
       </tr>
      <tr>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
      </tr>
      <tr>
        <td width="77" height="22"><div align="center">N&uacute;mero de Documento</div></td>
        <td width="441" height="22"><div align="left">
          <input name="codigodoc" type="text" id="codigodoc" style="text-align:center" size="40" maxlength="15">        
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td height="22">
          <div align="right">
            <input name="banco" type="hidden" id="obj" value="<?php print $ls_codigo;?>">
			<input name="codigo" type="hidden" id="codcon" value="<? print $ls_ctabanco;?>">
            <a href="javascript: ue_search();"> <img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a> </div></td></tr>
    </table>
	 <div align="center"><br>
         <?php

print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td width=150 style=text-align:center>Número de Documento</td>";
print "<td width=250 style=text-align:center>Descripción del Movimiento</td>";
print "<td width=150 style=text-align:center>Cta Bancaria</td>";
print "</tr>";
if ($ls_operacion=="BUSCAR")
   {
     $ls_sql = " SELECT numdoc,conmov,ctaban
	               FROM scb_movbco
			      WHERE codemp='".$arr["codemp"]."'
			        AND codban='".$ls_codigo."'
					AND ctaban like '".$ls_ctabanco."'
					AND numdoc like '%".$ls_numdoc."%'
			      ORDER BY numdoc ASC";
	 $rs_data    = $io_sql->select($ls_sql);
     $li_numrows = $io_sql->num_rows($rs_data);
     if ($li_numrows>0)
	    {
          while ($row=$io_sql->fetch_row($rs_data))
	            {
				  print "<tr class=celdas-blancas>";
			      $ls_numdoc = $row["numdoc"];
			      $ls_conmov = $row["conmov"];
				  $ls_ctaban = $row["ctaban"];
		 	      print "<td width=100 style=text-align:center><a href=\"javascript: aceptar('$ls_numdoc','$ls_conmov');\">".$ls_numdoc."</a></td>";
			      print "<td width=300 style=text-align:left>".$ls_conmov."</td>";
				  print "<td width=100 style=text-align:left>".$ls_ctaban."</td>";
			      print "</tr>";			
				}		
		}
     else
	    { ?>
	   <script language="javascript">
		    alert("No se encontraron documentos con esa cuenta !!!");
			close();
		  </script> 
	   <?php
		}
   }
print "</table>";
?>
  </div>
     </div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function aceptar(ls_numdoc,ls_conmov)
{
  f=opener.document.form1;
  f.txtdocumento.value=ls_numdoc;
  close();
}
  
function ue_search()
{
	f=document.form1;
	f.operacion.value="BUSCAR";
	f.action="sigesp_cat_numdoc.php";
	f.submit();
}
</script>
</html>