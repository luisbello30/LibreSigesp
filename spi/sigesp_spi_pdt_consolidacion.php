<?php
	session_start();
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";
		print "</script>";		
	}
   //----------------------------------------------------------------------------------------------------------------------------
   function uf_formato_cuenta($as_cuenta,$as_caracter="-")
   {
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_formato_cuenta_instructivo
	 //         Access :	private
	 //     Argumentos :    $as_cuenta // cuenta de ingreso
     //	       Returns :	Retorna cuenta con el formato para el instructivo
	 //	   Description :	devuelve la cuenta de ingreso con el formato mostrado en los instructivos
	 //     Creado por :    Ing. Arnaldo Suárez
	 // Fecha Creación :    25/09/2009         Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_cuenta="";
	 $as_cuenta = trim($as_cuenta);
	 if(!empty($as_cuenta))
	 {
	  $arreglo = str_split(substr($as_cuenta,1,strlen($as_cuenta)-1),2);
	  $total = count($arreglo);
	 
	  for($i=0;$i<$total;$i++)
	  {
	   $ls_cuenta .=$as_caracter.$arreglo[$i];
	  }
	 
	  $ls_cuenta = substr($as_cuenta,0,1).$ls_cuenta;
	 }
	
	return $ls_cuenta;
   }
   
   function uf_imprimirresultados($as_codcom,$as_procede)
   {
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_imprimirresultados
		//		   Access: private
		//	    Arguments: as_codcom  // Comprobante
		//	    		   as_procede  // Procede del Documento
		//	  Description: Función que Imprime los detalles del comprobante
		//	   Creado Por: Ing. Yesenia Moreno
		//     Modificado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 31/10/2006 								Fecha Última Modificación : 05/10/2009
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $in_class_mis;
		
		require_once("../shared/class_folder/sigesp_include.php");
		$in=new sigesp_include();
		$con=$in->uf_conectar();
		require_once("../shared/class_folder/class_mensajes.php");
		$io_mensajes=new class_mensajes();
		require_once("../shared/class_folder/class_sql.php");
		$io_sql=new class_sql($con);
		require_once("../shared/class_folder/class_sql.php");
		$io_sql2=new class_sql($con);
		require_once("../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql="SELECT comprobante,fecha,procede,descripcion ". 
				"  FROM sigesp_cmp".
				" WHERE codemp = '".$ls_codemp."' ".
				"   AND procede = '".$as_procede."' ".
				"   AND comprobante = '".$as_codcom."' ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_comprobante=$row["comprobante"];
				$ld_fecha=$io_funciones->uf_convertirfecmostrar($row["fecha"]);
				$ls_procede=$row["procede"];
				$ls_descripcion=$row["descripcion"];
				print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0'>";
				print "	<tr>";
				print "		<td width='450' class='titulo-ventana'>Información del Comprobante de Consolidación</td>";
				print " </tr>";
				print "</table>";
				print "<table width='450' border=0 cellpadding=1 cellspacing=1 align='center' class='formato-blanco'>";
				print "  <tr>";
				print "		<td width='150'><div align='right' class='texto-azul'>Nro. de Comprobante</div></td>";
				print "		<td width='350'><div align='left'>".$ls_comprobante."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>Fecha </div></td>";
				print "		<td><div align='justify'>".$ld_fecha."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>Procede</div></td>";
				print "		<td><div align='left'>".$ls_procede."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>Concepto</div></td>";
				print "		<td><div align='left'>".$ls_descripcion."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'></div></td>";
				print "		<td><div align='left'></div></td>";
				print "  </tr>";
				print "</table>";
				$ls_sql=" SELECT  ".
						"   spi_dt_cmp.codestpro1, ".
						"   spi_dt_cmp.codestpro2, ".
						"   spi_dt_cmp.codestpro3, ".
						"   spi_dt_cmp.codestpro4, ".
						"   spi_dt_cmp.codestpro5, ".
						"   spi_dt_cmp.estcla,     ".
						"   spi_dt_cmp.spi_cuenta, ".
						"   spi_dt_cmp.operacion, ".
						"   spi_dt_cmp.monto, ".
						"   spi_operaciones.denominacion as desoperacion ".
						" FROM ".
						"   spi_dt_cmp, ".
						"   spi_operaciones".
						" WHERE ".
						"      spi_dt_cmp.codemp = '".$ls_codemp."'".
						"  AND spi_dt_cmp.procede = '".$as_procede."' ".
						"  AND spi_dt_cmp.comprobante = '".$as_codcom."' ".
						"  AND spi_dt_cmp.operacion = spi_operaciones.operacion";
				/*$ls_sql="SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spi_cuenta, operacion, monto ".
						"  FROM spi_dt_cmp ".
						" WHERE codemp = '".$ls_codemp."' ".
						"   AND procede = '".$as_procede."' ".
						"   AND comprobante = '".$as_codcom."' ";*/
				$rs_data2=$io_sql2->select($ls_sql);
				if($rs_data2===false)
				{
					$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql2->message)); 
				}
				else
				{
					$ls_titulo="";
					$li_len1=0;
					$li_len2=0;
					$li_len3=0;
					$li_len4=0;
					$li_len5=0;
					$in_class_mis->uf_loadmodalidad(&$li_len1,&$li_len2,&$li_len3,&$li_len4,&$li_len5,&$ls_titulo);
					print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
					print "	<tr>";
					if($_SESSION["la_empresa"]["estpreing"]==1)
				    {
					 print "		<td colspan='5' class='titulo-celdanew'>Detalle Presupuestario de Ingreso</td>";
					}
					else
					{
					 print "		<td colspan='3' class='titulo-celdanew'>Detalle Presupuestario de Ingreso</td>";
					} 
					print " </tr>";
					print " <tr class=titulo-celdanew>";
					if($_SESSION["la_empresa"]["estpreing"]==1)
				    {
					 print "		<td width='150'>".$ls_titulo."</td>";
					 print "		<td width='100'>Estatus</td>";
					 print "		<td width='100'>Cuenta</td>";
					 print "		<td width='100'>Operacion</td>";
					 print "		<td width='100'>Monto</td>";
					 print "	</tr>";
					}
					else
					{
					 print "		<td width='200'>Cuenta</td>";
					 print "		<td width='200'>Operacion</td>";
					 print "		<td width='150'>Monto</td>";
					 print "	</tr>";
					}
					$li_total=0;
					while($row=$io_sql2->fetch_row($rs_data2))
					{
						$ls_cuenta=$row["spi_cuenta"];
						$li_total=$li_total+$row["monto"];
						$ls_codestpro=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
						$ls_estcla=$row["estcla"];
						$ls_operacion=$row["operacion"];
						$ls_desoperacion=$row["desoperacion"];
						$ls_programatica="";
						$ls_estatus="";
						$in_class_mis->uf_formatoprogramatica($ls_codestpro,&$ls_programatica);
						switch($ls_estcla)
						{
							case "A":
								$ls_estatus="Acción";
								break;
							case "P":
								$ls_estatus="Proyecto";
								break;
						}
						$li_monto=$in_class_mis->uf_formatonumerico($row["monto"]);
						print "<tr class=celdas-blancas>";
						if($_SESSION["la_empresa"]["estpreing"]==1)
						{
						 print "<td align=center width='150'>".$ls_programatica."</td>";
						 print "<td align=center width='100'>".$ls_estatus."</td>";
						 print "<td align=center width='100'>".uf_formato_cuenta($ls_cuenta,".")."</td>";
						 print "<td align=center width='100'>".strtoupper($ls_desoperacion)."</td>";
						 print "<td align=right width='100'>".$li_monto."  </td>";
						 print "</tr>";
						}
						else
						{
						 print "<td align=center width='200'>".uf_formato_cuenta($ls_cuenta,".")."</td>";
						 print "<td align=center width='200'>".strtoupper($ls_desoperacion)."</td>";
						 print "<td align=right width='150'>".$li_monto."  </td>";
						 print "</tr>";
						}
									
					}
					$li_total=$in_class_mis->uf_formatonumerico($li_total);
					print "	<tr class=celdas-blancas>";
					if($_SESSION["la_empresa"]["estpreing"]==1)
				    {
					 print "		<td colspan='4' align='right' class='texto-azul'>Total</td>";
					}
					else
					{
					  print "		<td colspan='2' align='right' class='texto-azul'>Total</td>";
					}
					print "		<td width='150' align='right' class='texto-azul'>".$li_total."</td>";
					print " </tr>";
					print "</table>";
				}
				/*$io_sql2->free_result($rs_data2);
				$ls_sql="SELECT sc_cuenta, debhab, monto ".
						"  FROM scg_dt_cmp ".
						" WHERE codemp = '".$ls_codemp."' ".
						"   AND procede = '".$as_procede."' ".
						"   AND comprobante = '".$as_codcom."' ".
						" ORDER BY  debhab ";
				$rs_data2=$io_sql2->select($ls_sql);
				if($rs_data2===false)
				{
					$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql2->message)); 
				}
				else
				{
					$li_total_deb=0;
					$li_total_hab=0;
					print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
					print "	<tr>";
					print "		<td colspan='3' class='titulo-celdanew'>Detalle Contable</td>";
					print " </tr>";
					print " <tr class=titulo-celdanew>";
					print "		<td width='100'>Cuenta</td>";
					print "		<td width='100'>Debe</td>";
					print "		<td width='100'>Haber</td>";
					print "	</tr>";
					while($row=$io_sql2->fetch_row($rs_data2))
					{
						$ls_cuenta=$row["sc_cuenta"];
						$li_monto=$row["monto"];
						$ls_debhab=$row["debhab"];
						switch($ls_debhab)
						{
							case "D":
								$li_debe=$li_monto;
								$li_debe=$in_class_mis->uf_formatonumerico($li_debe);
								$li_haber="0,00";
								$li_total_deb=$li_total_deb+$li_monto;
								break;
							case "H":
								$li_debe="0,00";
								$li_haber=$li_monto;
								$li_haber=$in_class_mis->uf_formatonumerico($li_haber);
								$li_total_hab=$li_total_hab+$li_monto;
								break;
						}
						print "<tr class=celdas-blancas>";
						print "<td align=center width='100'>".$ls_cuenta."</td>";
						print "<td align=right width='100'>".$li_debe."</td>";
						print "<td align=right width='100'>".$li_haber."</td>";
						print "</tr>";			
					}
					$li_total_deb=$in_class_mis->uf_formatonumerico($li_total_deb);
					$li_total_hab=$in_class_mis->uf_formatonumerico($li_total_hab);
					print "	<tr>";
					print "		<td align=right class='texto-azul'>Total</td>";
					print "		<td align=right class='texto-azul'>".$li_total_deb."</td>";
					print "		<td align=right class='texto-azul'>".$li_total_hab."</td>";
					print " </tr>";
					print "</table>";
				}
				$io_sql2->free_result($rs_data2);*/
				print "<br><br>";	
			}
		}
		$io_sql->free_result($rs_data);	
   }
   //----------------------------------------------------------------------------------------------------------------------------
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
<title>Detalle Comprobante</title>
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
<?php
	require_once("../mis/class_folder/class_funciones_mis.php");
	$in_class_mis=new class_funciones_mis();
	$ls_codcom=$in_class_mis->uf_obtenervalor_get("codcom","");
	$ls_codcom = str_replace("___"," ",$ls_codcom);
	$ls_procede=$in_class_mis->uf_obtenervalor_get("procede","");
	uf_imprimirresultados($ls_codcom,$ls_procede);
?>
</div>
</form>
</body>
</html>