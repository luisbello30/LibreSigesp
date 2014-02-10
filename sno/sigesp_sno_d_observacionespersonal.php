<?php
    session_start();
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "location.href='../sigesp_inicio_sesion.php'";
		print "</script>";		
	}
	ini_set('max_execution_time ','0');

	$ls_logusr=$_SESSION["la_logusr"];
	$ls_codnom=$_SESSION["la_nomina"]["codnom"];
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$io_fun_nomina->uf_load_seguridad_nomina("SNO","sigesp_sno_d_observacionespersonal.php",$ls_codnom,$ls_permisos,$la_seguridad,$la_permisos);
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
 
   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		global $ls_operacion, $li_totrows, $ls_titletable, $li_widthtable;
		global $ls_nametable, $lo_title, $io_fun_nomina, $ls_desnom,$ls_desper;
		global $li_registros, $li_pagina, $li_inicio, $li_totpag;
		
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$ls_desper=$_SESSION["la_nomina"]["descripcionperiodo"];
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",1);
		$ls_titletable="Personal";
		$li_widthtable=700;
		$ls_nametable="grid";
		$lo_title[1]="Código";
		$lo_title[2]="Nombre";
		$lo_title[3]="Observación";
		$li_registros = 100;
		$li_pagina=$io_fun_nomina->uf_obtenervalor_get("pagina",0);
		if (!$li_pagina) { 
			$li_inicio = 0; 
			$li_pagina = 1; 
		} 
		else { 
			$li_inicio = ($li_pagina - 1) * $li_registros; 
		} 
		$li_totpag=0;
	}
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<script language="javascript">
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title >Definici&oacute;n de Observaciones del Personal</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EFEBEF;
}

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
<script type="text/javascript" language="JavaScript1.2" src="js/stm31.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="js/funcion_nomina.js"></script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_sno_c_personalnomina.php");
	$io_personal=new sigesp_sno_c_personalnomina();
	require_once("../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":
			$lb_valido=$io_personal->uf_load_observacionpersonal($li_inicio,$li_registros,$li_totrows,$lo_object,$li_totpag);
			break;
			
		case "GUARDAR":
			$lb_valido=true;
			$ls_descripcionpersonal="";
			$io_personal->io_sql->begin_transaction();
			for($li_i=1;$li_i<=$li_totrows&&$lb_valido;$li_i++)
			{
				$ls_codper=$_POST["txtcodper".$li_i.""];
				$ls_obsrecper=trim($_POST["txtobsrecper".$li_i.""]);	
				$ls_obsrecperant=trim($_POST["txtobsrecperant".$li_i.""]);	
				if ($ls_obsrecper != $ls_obsrecperant)
				{
					$lb_valido=$io_personal->uf_updateobservacion($ls_codper,$ls_obsrecper);
					$ls_descripcionpersonal=$ls_descripcionpersonal." - personal ".$ls_codper;
				}
			}
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////					
				$ls_evento="UPDATE";
				$ls_descripcion ="Actualizó la Observación para Recibo de ".$ls_descripcionpersonal.", asociado a la nómina ".$ls_codnom;
				$lb_valido= $io_personal->io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
												$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
												$la_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			}		
			if($lb_valido)
			{
				$io_personal->io_sql->commit();
				$io_personal->io_mensajes->message("El Personal fue actualizado.");
			}
			else
			{
				$io_personal->io_sql->rollback();
				$io_personal->io_mensajes->message("Ocurrio un error al actualizar el personal.");
			}
			$lb_valido=$io_personal->uf_load_observacionpersonal($li_inicio,$li_registros,$li_totrows,$lo_object,$li_totpag);
			break;
	}
	$io_personal->uf_destructor();
	unset($io_personal);	
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema"><?php print $ls_desnom;?></td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><?php print $ls_desper;?></span></div></td>
			 <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
	  </table>
	</td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu_nomina.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title='Guardar 'alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title='Salir' alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title='Ayuda' alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigesp_sno_d_concepto.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="762" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td>
      <p>&nbsp;</p>
      <table width="712" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-celda">
          <td colspan="2" class="formato-blanco"><div align="center"></div></td>
        </tr>
        <tr class="titulo-ventana">
          <td height="20" colspan="2" class="titulo-ventana">Observaciones del Personal </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="85"><div align="right">Observaci&oacute;n</div></td>
          <td width=>
            <div align="left">
              <textarea name="txtobservacion" cols="80" rows="3" id="txtobservacion" onKeyUp="javascript: ue_validarcomillas(this);"></textarea>
              Aplicar a Todos
              <input name="chktodos" type="checkbox" class="sin-borde" id="chktodos" value="1" onChange="ue_aplicar();">
              </div></td></tr>
        <tr>
		<?php
			print "<center>";
			if(($li_pagina - 1) > 0) 
			{
				print "<a href='sigesp_sno_d_observacionespersonal.php?pagina=".($li_pagina-1)."'>< Anterior</a> ";
			}
			for ($li_i=1; $li_i<=$li_totpag; $li_i++)
			{ 
				if ($li_pagina == $li_i) 
				{
					print "<b>".$li_pagina."</b> "; 
				}
				else
				{
					print "<a href='sigesp_sno_d_observacionespersonal.php?pagina=".($li_i)."'>$li_i</a> "; 
				}
			}
			if(($li_pagina + 1)<=$li_totpag) 
			{
				print " <a href='sigesp_sno_d_observacionespersonal.php?pagina=".($li_pagina+1)."'>Siguiente ></a>";
			}
			
			print "</center>";
		?>
          </tr>
        <tr>
          <td colspan="2"><div align="center">
			<?php
					$io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
					unset($io_grid);
			?>
            </div></td>
          </tr>
        <tr>
          <td><div align="right"></div></td>
          <td><input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe">
            <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>"></td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
</body>
<script language="javascript">
function ue_guardar()
{
	f=document.form1;
	li_cambiar=f.cambiar.value;
	if(li_cambiar==1)
	{
		f.operacion.value="GUARDAR";
		f.action="sigesp_sno_d_observacionespersonal.php?pagina=<?php print $li_pagina; ?>";
		f.submit();
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_aplicar()
{
	f=document.form1;
	if(f.chktodos.checked==true)
	{
		total=f.totalfilas.value;
		observacion=f.txtobservacion.value;
		for(i=1;i<=total;i++)
		{
			eval("f.txtobsrecper"+i+".value='"+observacion+"';");
		}
	}
	else
	{
		total=f.totalfilas.value;
		for(i=1;i<=total;i++)
		{
			eval("f.txtobsrecper"+i+".value=f.txtobsrecperant"+i+".value;");
		}
	}
}

function ue_cerrar()
{
	location.href = "sigespwindow_blank_nomina.php";
}
</script> 
</html>