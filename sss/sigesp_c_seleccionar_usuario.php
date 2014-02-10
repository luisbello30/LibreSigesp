<?php
session_start();
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "location.href='../sigesp_inicio_sesion.php'";
	print "</script>";		
}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_usuarios($as_seleccionado,$as_codemp)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_usuarios
		//		   Access: private
		//		 Argument: $as_seleccionado // Valor del campo que va a ser seleccionado
		//                 $as_codemp      // Codigo de empresa
		//	  Description: Función que busca en los usuarios existentes en el sistema
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 21/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		global $io_sql;
		$ls_sql="SELECT codusu ".
				"  FROM sss_usuarios ".
				" WHERE codemp='".$as_codemp."'".
				" ORDER BY codusu ";	
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
		}
		else
		{
			print "<select name='cmbusuarios' id='cmbusuarios' onChange=ue_aceptar();>";
			print " <option value='-'>-- Seleccione Uno --</option>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_seleccionado="";
				$ls_codusu=$row["codusu"];
				if($as_seleccionado==$ls_codusu)
				{
					$ls_seleccionado="selected";
				}
				print "<option value='".$ls_codusu."'>".$ls_codusu."</option>";
			}
			$io_sql->free_result($rs_data);	
			print "</select>";
		}
		return $lb_valido;
	}// end function uf_load_usuarios
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_grupos($as_seleccionado,$as_codemp)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_grupos
		//		   Access: private
		//		 Argument: $as_seleccionado // Valor del campo que va a ser seleccionado
		//                 $as_codemp      // Codigo de empresa
		//	  Description: Función que busca en los grupos de usuarios existentes en el sistema
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 21/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		global $io_sql;
		$ls_sql="SELECT nomgru ".
				"  FROM sss_grupos ".
				" WHERE codemp='".$as_codemp."'".
				" ORDER BY nomgru ";	
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
		}
		else
		{
			print "<select name='cmbgrupos' id='cmbgrupos'  onChange=ue_aceptargrupos()>";
			print " <option value='-'>-- Seleccione Uno --</option>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_seleccionado="";
				$ls_nomgru=$row["nomgru"];
				if($as_seleccionado==$ls_nomgru)
				{
					$ls_seleccionado="selected";
				}
				print "<option value='".$ls_nomgru."'>".$ls_nomgru."</option>";
			}
			$io_sql->free_result($rs_data);	
			print "</select>";
		}
		return $lb_valido;
	}// end function uf_load_grupos
	//-----------------------------------------------------------------------------------------------------------------------------------

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Seleccionar Perfil a Actualizar</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<!--<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<script language="javascript">
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey))
		{
			window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ return false;} 
		} 
	}
</script>
--><style type="text/css">
<!--
.Estilo1 {
	color: #FFFFFF;
	font-weight: bold;
}
-->
</style>
</head>
<?php
	require_once("../shared/class_folder/sigesp_include.php");
	$in=     new sigesp_include();
	$con= $in->uf_conectar();
	$io_sql=new class_sql($con);
	require_once("class_funciones_seguridad.php");
	$io_fun_seguridad=new class_funciones_seguridad();
	$ls_seleccionado="";
	
	$arr=array_keys($_SESSION);	
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$li_count=count($arr);
	
	if (array_key_exists("sist",$_GET))
	{	
		$ls_sistema=$_GET["sist"];
	}
	else
	{
		$ls_sistema=$io_fun_seguridad->uf_obtenervalor("hidsistema","");
	}
	$ls_operacion=$io_fun_seguridad->uf_obteneroperacion();
	switch ($ls_operacion)
	{
		case "USUARIOS":
			$ls_codusu=$io_fun_seguridad->uf_obtenervalor("cmbusuarios","");
			$_SESSION["la_ususeg"]=$ls_codusu;
			$_SESSION["la_sistema"]["sistema"]=$ls_sistema;
			print("<script language=JavaScript>");
			print("opener.parent.location.href='sigesp_sss_p_derechousuario.php'");
			print("</script>");
			?>
				<script language="JavaScript">
					close();
				</script>
			<?php
		break;
		case "GRUPOS":
			$ls_codgru=$io_fun_seguridad->uf_obtenervalor("cmbgrupos","");
			$_SESSION["la_gruseg"]=$ls_codgru;
			$_SESSION["la_sistema"]["sistema"]=$ls_sistema;
			print("<script language=JavaScript>");
			print("opener.parent.location.href='sigesp_sss_p_derechogrupo.php'");
			print("</script>");
			?>
				<script language="JavaScript">
					close();
				</script>
			<?php
		break;
	}
?>
<body>
<form name="form1" method="post" action="">
  <div align="center"><br>
  </div>
  <div align="center">
    <table width="41%" height="123" border="0" cellpadding="0" cellspacing="0"  class="formato-blanco">
      <tr >
        <td height="22"  class="titulo-celdanew">Seleccionar Perfil a Actualizar </td>
      </tr>
      <tr>
        <td height="13">&nbsp;</td>
      </tr>
      <tr>
        <td height="20"><table width="391" height="52" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="100"><div align="right">Usuario</div></td>
            <td width="291" height="22"><div align="left">
				<?php uf_load_usuarios($ls_seleccionado,$ls_codemp); ?>
                <input name="hidsistema" type="hidden" id="hidsistema" value="<?php print $ls_sistema?>" size="6">
            </div></td>
          </tr>
          <tr>
            <td height="13"><div align="right">Grupos</div></td>
            <td height="22"><?php  uf_load_grupos($ls_seleccionado,$ls_codemp);?>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td height="13"><input name="operacion" type="hidden" id="operacion"></td>
      </tr>
    </table>
  </div>
  <div align="center"></div>
  <p>&nbsp;</p>

</form>
</body>
<script language="JavaScript">
function ue_aceptar()
{
	f=document.form1;
	f.operacion.value="USUARIOS";
	f.action="sigesp_c_seleccionar_usuario.php";
	f.submit();
}

function ue_aceptargrupos()
{
	f=document.form1;
	f.operacion.value="GRUPOS";
	f.action="sigesp_c_seleccionar_usuario.php";
	f.submit();
}

</script>

</html>
