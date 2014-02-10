<?php
	session_start();
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";
		print "</script>";		
	}
   //--------------------------------------------------------------
   function uf_print($as_codger, $as_denger, $as_tipo)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codpro  // Código de Profesión
		//				   as_despro  // Descripción de la profesión
		//				   as_tipo  // Verifica de donde se está llamando el catálogo
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$io_fun_nomina->uf_loadmodalidad(&$li_len1,&$li_len2,&$li_len3,&$li_len4,&$li_len5,&$ls_titulo);
		require_once("../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../shared/class_folder/class_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../shared/class_folder/class_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td>Código </td>";
		print "<td>Denominación</td>";
		print "</tr>";
		
		$ls_sql="SELECT srh_gerencia.* FROM srh_gerencia ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codger like '".$as_codger."' ".
				"   AND denger like '".$as_denger."' ".
			    " ORDER BY codger";
		$rs_data=$io_sql->select($ls_sql);
		
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$codigo=$row["codger"];
				$ls_descrip=$row["denger"];
				switch($as_tipo)
				{
					case "": // Se hace el llamado desde sigesp_snorh_d_uni_adm.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptar('$codigo','$ls_descrip');\">".$codigo."</a></td>";
						print "<td>".$ls_descrip."</td>";
						print "</tr>";			
						break;			

					case "replisperdes": // Se hace el llamado desde sigesp_snorh_r_unidadadministrativa.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisperdes('$codigo','$ls_descrip');\">".$codigo."</a></td>";
						print "<td>".$ls_descrip."</td>";
						print "</tr>";			
						break;		

					case "replisperhas": // Se hace el llamado desde sigesp_snorh_r_unidadadministrativa.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisperhas('$codigo','$ls_descrip');\">".$codigo."</a></td>";
						print "<td>".$ls_descrip."</td>";
						print "</tr>";			
						break;			
				}
			}
			$io_sql->free_result($rs_data);
		}
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
		unset($io_unidadadmin);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Gerencias</title>
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
<style type="text/css">
<!--
.Estilo1 {font-size: 11px}
-->
</style>
</head>

<body>
<form name="form1" method="post" action="">
    <input name="operacion" type="hidden" id="operacion">
  	 <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    	<tr>
     	 	<td width="500" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Unidades Administrativas  </td>
    	</tr>
  </table>
	 <br>
	 <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="111" height="22"><div align="right">Codigo</div></td>
        <td width="451"><div align="left">
          <input name="codigo" type="text" id="codigo" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Denominacion</div></td>
        <td><div align="left">
          <input name="denominacion" type="text" id="denominacion" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
	<br>
    <?php
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	if($ls_operacion=="BUSCAR")
	{
		$ls_codigo=$_POST["codigo"];
		$ls_denominacion="%".$_POST["denominacion"]."%";
		uf_print($ls_codigo, $ls_denominacion, $ls_tipo);
	}
	else
	{
		$ls_codigo="%%";
		$ls_denominacion="%%";
		uf_print($ls_codigo, $ls_denominacion, $ls_tipo);
	}	
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
  function aceptar(codigo,deno,codest1,codest2,codest3,codest4,codest5,denestpro1,denestpro2,denestpro3,denestpro4,denestpro5,estcla)
  {
    opener.document.form1.txtcodigo.value=codigo;
	opener.document.form1.txtcodigo.readOnly=true;
    opener.document.form1.txtdenominacion.value=deno;
	opener.document.form1.txtcodestpro1.value=codest1;
	opener.document.form1.txtcodestpro2.value=codest2;
	opener.document.form1.txtcodestpro3.value=codest3;
	opener.document.form1.txtcodestpro4.value=codest4;
	opener.document.form1.txtcodestpro5.value=codest5;
	opener.document.form1.txtdenestpro1.value=denestpro1;
	opener.document.form1.txtdenestpro2.value=denestpro2;
	opener.document.form1.txtdenestpro3.value=denestpro3;
	opener.document.form1.txtdenestpro4.value=denestpro4;
	opener.document.form1.txtdenestpro5.value=denestpro5;
	opener.document.form1.txtestcla1.value=estcla;
	opener.document.form1.txtestcla2.value=estcla;
	opener.document.form1.txtestcla3.value=estcla;
	opener.document.form1.txtestcla4.value=estcla;
	opener.document.form1.txtestcla5.value=estcla;
    opener.document.form1.existe.value="TRUE";
	close();
  }

  function aceptarreplisperdes(codigo,denominacion)
  {
    opener.document.form1.txtcodgerdes.value=codigo;
    opener.document.form1.txtcodgerdes.readOnly=true;
	close();
  }

  function aceptarreplisperhas(codigo,denominacion)
  {
	opener.document.form1.txtcodgerhas.value=codigo;
	opener.document.form1.txtcodgerhas.readOnly=true;
	close();
  }

function ue_mostrar(myfield,e)
{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	if (keycode == 13)
	{
		ue_search();
		return false;
	}
	else
		return true
}

  function ue_search()
  {
	  f=document.form1;
	  f.operacion.value="BUSCAR";
	  f.action="sigesp_snorh_cat_gerencia.php?tipo=<?php print $ls_tipo;?>";
	  f.submit();
  }
</script>
</html>
