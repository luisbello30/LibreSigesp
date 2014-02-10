<?php 
ini_set('precision ','20');
session_start(); 
if((!array_key_exists("ls_database",$_SESSION))||(!array_key_exists("ls_hostname",$_SESSION))||(!array_key_exists("ls_gestor",$_SESSION))||(!array_key_exists("ls_login",$_SESSION))||(!array_key_exists("la_logusr",$_SESSION))||(!array_key_exists("la_empresa",$_SESSION)||(!array_key_exists("ls_password",$_SESSION))))
{
	print "<script language=JavaScript>";
	print "location.href='sigesp_inicio_sesion.php'";
	print "</script>";		
}
$ls_tipocontabilidad=$_SESSION["la_empresa"]["esttipcont"];
$ls_usuario=$_SESSION["la_codusu"];
$ls_clave=$_SESSION["la_pasusu"];

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
}
else
{
	$ls_operacion="";
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>M&oacute;dulos SIGESP,C.A. , <?php print $_SESSION["ls_database"] ?> </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<style type="text/css">
<!--
body{color:#666666;font-family:Tahoma, Verdana, Arial;font-size:11px;margin:0px;background-color:#EAEAEA;}
.titulo {
	font-family: Tahoma, Verdana, Arial;
	font-size: 16px;
	font-weight: bold;
	color: #666666;
}
.style1 {font-size: 12px}
.style6 {font-size: 16px}
.style7 {color: #FF0000}
.Estilo1 {
	font-size: 10px;
	color: #898989;
}
-->
</style>
</head>

<body>

<p><br>
</p>
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<input name="hidclave" id="hidclave" type="hidden"   value="<?PHP print $ls_clave; ?>">
<input name="hidusuario" id="hidusuario" type="hidden" value="<?PHP print $ls_usuario; ?>">
<input name="operacion" id="operacion" type="hidden" value="<?PHP print $ls_opreacion; ?>">
<table width="200" border="0" align="center" cellpadding="0" cellspacing="0">

  <tr>
    <td><img src="shared/imagebank/modulos/modulos_azul.jpg" width="645" height="496" border="0" usemap="#Map"></td>
  </tr>
</table>
<map name="MapMap">
  <area shape="rect" coords="224,102,357,144" href="scb/sigespwindow_blank.php">
</map>
<p align="center" class="Estilo1">Software Libre desarrollado por<span class="style7"> SIGESP C.A.</span> <br>
Derecho de Autor bajo el N&ordm; 1950, del 5 de Agosto del a&ntilde;o 1998.<br>
Direcci&oacute;n: Carrera 1 entre Av. Concordia y Calle 3. Quinta N&ordm; 2-13. <br>
Urbanizaci&oacute;n del Este. Barquisimeto - Edo.Lara <br>
Hecho en Venezuela.<br>
Telefonos: (0251) 2547643 - 2552587
<map name="Map">
  <area shape="rect" coords="21,432,135,476" href="vista/apr/sigesp_vis_apr_principal.html" target="_self" alt="Sistema de Apertura">
  <area shape="rect" coords="426,349,601,393" href="sps/pages/html/sigespwindow_blank.php" target="_self" alt="Sistema de Prestaciones Sociales">
<?php if($ls_tipocontabilidad=="2"){ ?>
  <area shape="rect" coords="224,143,383,190" href="scf/sigespwindow_blank.php" target="_self" alt="Sistema de Contabilidad Fiscal">
<?php }
	  if($ls_tipocontabilidad=="1"){
?>
  <area shape="rect" coords="13,144,195,187" href="scg/sigespwindow_blank.php" target="_self" alt="Sistema de Contabilidad Patrimonial">
<?php }
?>
<area shape="rect" coords="466,430,614,484" href="sfp/pantallas/formas/sigesp_windowblank.php" target="_self" alt="Sistema de Formulación de Presupuesto">
  <area shape="rect" coords="12,303,166,346" href="scv/sigespwindow_blank.php" target="_self" alt="Sistema de Vi&aacute;ticos">
  <area shape="rect" coords="229,352,379,398" href="srh/pages/vistas/pantallas/sigespwindow_blank.php" target="_self" alt="Sistema de Recursos Humanos">
  <area shape="rect" coords="222,253,406,297" href="sob/sigespwindow_blank.php" target="_self" alt="Obras">
  <area shape="rect" coords="566,5,589,28" href="javascript: cambio_bd();" target="_self">
  <area shape="rect" coords="13,93,132,134" href="saf/sigespwindow_blank.php" target="_self" alt="Sistema de Activos Fijos">
  <area shape="rect" coords="224,93,357,135" href="scb/sigespwindow_blank.php" target="_self" alt="Sistema de Caja y Bancos">
  <area shape="rect" coords="222,196,411,240" href="spg/sigespwindow_blank.php" target="_self" alt="Sistema de Presupuesto de Gastos">
  <area shape="rect" coords="425,197,623,240" href="spi/sigespwindow_blank.php" target="_self" alt="Sistema de Presupuesto de Ingresos">
  <area shape="rect" coords="429,89,550,135" href="soc/sigespwindow_blank.php" target="_self" alt="Sistema de Compras">
  <area shape="rect" coords="588,5,611,28" href="sigesp_inicio_sesion.php" target="_self">
  <area shape="rect" coords="610,4,633,28" href="sigesp_conexion.php">
  <area shape="rect" coords="225,303,360,348" href="cfg/index.php" target="_self" alt="Sistema de Configuraciï¿½">
  <area shape="rect" coords="11,360,165,399" href="mis/sigespwindow_blank.php">
  <area shape="rect" coords="12,196,200,243" href="sep/sigespwindow_blank.php" target="_self" alt="Sistema de Ejecución Presupuestaria">
  <area shape="rect" coords="13,251,109,293" href="sno/sigespwindow_blank.php" target="_self" alt="Sistema de Nómina">
  <area shape="rect" coords="425,248,629,297" href="rpc/sigespwindow_blank.php" target="_self" alt="Sistema de Proveedores y Beneficiarios">
  <area shape="rect" coords="424,142,583,188" href="cxp/sigespwindow_blank.php" target="_self" alt="Sistema de Cuentas por pagar">
  <area shape="rect" coords="173,437,287,481" href="sss/sigespwindow_blank.php" target="_self" alt="Sistema de Seguridad">
  <area shape="rect" coords="427,303,545,343" href="siv/sigespwindow_blank.php" target="_self" alt="Sistema de Inventario">
  <area shape="rect" coords="346,437,451,480" href="ins/sigespwindow_blank.php" target="_self" alt="Instala">
  <area shape="rect" coords="446,207,464,224" target="_self">
<area shape="rect" coords="545,5,568,29" href="javascript:uf_abrir_help();" >
</map>
</p>


<?php 
	
	switch ($ls_operacion) 
	{
		case "CAMBIO_BD":
		   	
			/// validación del release necesario
			require_once("shared/class_folder/sigesp_release.php");
			$io_release= new sigesp_release();
			
			$lb_valido=$io_release->io_function_db->uf_select_column('sigesp_empresa','estcamemp');
			if($lb_valido==false)
			{
				?>
	           <script language="javascript">
			   alert("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_53 ");
			   close();
			   </script>
	          <?php	
			}
			else
			{
				require_once("shared/class_folder/sigesp_include.php");
				require_once("shared/class_folder/class_mensajes.php");
				require_once("shared/class_folder/class_sql.php");
				$in=new sigesp_include();
				$con=$in->uf_conectar();
				$msg=new class_mensajes();
				$io_sql=new class_sql($con);
				
				$ls_codemp= $_SESSION["la_empresa"]["codemp"];
				$ls_sql  =" SELECT estcamemp ".
						  " FROM sigesp_empresa".
						  " WHERE codemp = '".$ls_codemp."' ";
						 
				$rs_data=$io_sql->select($ls_sql);
				
				if ($rs_data===false) 
				{
				  ?>
				   <script language="javascript">
				   alert("No se puede efectuar la operacion");
				   close();
				   </script>
				  <?php
				  $lb_valido = false;
				} 
				else
				{
				  $li_numrows = $io_sql->num_rows($rs_data);
				  if ($li_numrows>0)
				  {
					   if($row=$io_sql->fetch_row($rs_data))
						{
						 
							  $ls_estcamemp = $row["estcamemp"];
							  if ($ls_estcamemp==0)
							  {
									?>
									<script language="javascript">
										document.form1.action="sigesp_conexion.php";
										document.form1.submit();
									</script>
								   <?php
							  }
							  else
							   {
									?>
									<script language="javascript">
									codusu=document.form1.hidusuario.value;
									codpas=document.form1.hidclave.value
									window.open("sigesp_cambio_db.php?codusu="+codusu+"&codpas="+codpas,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=440,left=50,top=50,location=no,resizable=yes");
									</script>
								   <?php
							  }		
						}
				 }
				 else
				 {?>
				   <script language="javascript">
				   alert("No se puede efectuar la operacion");
				   close();
				   </script>
				  <?php
				 }	
			}
		}// Fin del else que chequea el release
			
		break;
	}
	
	
?>



</form>
</body>
</html>
<script language="javascript">
function uf_abrir_help()
{
	window.open("hlp/index.php","Catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=50,top=50,location=no,resizable=yes");	
}

function cambio_bd()
{
    document.form1.operacion.value="CAMBIO_BD";
    document.form1.action="index_modules.php";
	document.form1.submit();

}

</script>
