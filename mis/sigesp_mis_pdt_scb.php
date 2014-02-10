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
   function uf_imprimirresultados($as_numdoc,$as_codban,$as_ctaban,$as_codope,$as_disponibilidad)
   {
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_imprimirresultados
		//		   Access: private
		//	    Arguments: as_numdoc  // Número de Documento
		//	    		   as_codban  // Código de Banco
		//	    		   as_ctaban  // Cuenta de Banco
		//	    		   as_codope  // Código de Operación
		//	  Description: Función que Imprime los detalles del comprobante
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 31/10/2006 								Fecha Última Modificación : 
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
        $ls_contintmovban=$_SESSION["la_empresa"]["contintmovban"];
		require_once("../shared/class_folder/class_sigesp_int.php");
		require_once("../shared/class_folder/class_sigesp_int_int.php");
		require_once("../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../shared/class_folder/class_sigesp_int_spg.php");
		$io_int_spg=new class_sigesp_int_spg();
		$ls_campo="numdoc";
		if(($ls_contintmovban==1)&&(($as_codope=="DP")||($as_codope=="ND")||($as_codope=="NC")))
		{
			$ls_campo="numconint";
		}
		$ls_sql="SELECT codban,ctaban,numdoc,fecmov,conmov,tipo_destino,cod_pro,ced_bene,codope, ".
				"       (SELECT nomban FROM scb_banco ".
				"		  WHERE codemp = '".$ls_codemp."' ".
				"			AND codban = '".$as_codban."' ) as nomban,  ".
				"		(SELECT nompro FROM rpc_proveedor ".
				"		  WHERE rpc_proveedor.codemp = scb_movbco.codemp ".
				"           AND rpc_proveedor.cod_pro = scb_movbco.cod_pro ) as nompro, ".
				"		(SELECT nombene FROM rpc_beneficiario ".
				"		  WHERE rpc_beneficiario.codemp = scb_movbco.codemp ".
				"           AND rpc_beneficiario.ced_bene = scb_movbco.ced_bene ) as nombene, ".
				"		(SELECT apebene FROM rpc_beneficiario ".
				"		  WHERE rpc_beneficiario.codemp = scb_movbco.codemp ".
				"           AND rpc_beneficiario.ced_bene = scb_movbco.ced_bene ) as apebene ".
                "  FROM scb_movbco ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND ".$ls_campo."='".$as_numdoc."' ".
				"   AND codban='".$as_codban."' ".
				"   AND ctaban='".$as_ctaban."' ".
				"   AND codope='".$as_codope."' ".
				" GROUP BY codemp,".$ls_campo.",numdoc,codban,ctaban,fecmov,conmov,tipo_destino,cod_pro,ced_bene,codope ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codban=$rs_data->fields["codban"];
				$ls_nomban=$rs_data->fields["nomban"];
				$ls_conmov=$rs_data->fields["conmov"];
				$ls_tipo_destino=$rs_data->fields["tipo_destino"];
				$ls_numdoc=$rs_data->fields["numdoc"];
				$ld_fecha=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecmov"]);
				$_SESSION["fechacomprobante"]=$ld_fecha;
				switch($ls_tipo_destino)
				{
					case "P":
						$ls_destino="Proveedor";
						$ls_nombre_destino=$rs_data->fields["cod_pro"]." - ".$rs_data->fields["nompro"];
						break;
	
					case "B":
						$ls_destino="Beneficiario";
						$ls_nombre_destino=$rs_data->fields["ced_bene"]." - ".$rs_data->fields["apebene"].", ".$rs_data->fields["nombene"];
						break;

					case "-":
						$ls_destino="Ninguno";
						$ls_nombre_destino="-";
						break;
				}
				$ls_codope=$rs_data->fields["codope"];
				$ls_tabla="scb_movbco_spg";
				switch($ls_codope)
				{
					case "ND":
						$ls_codope="NOTA DE DÉBITO";
						break;	
					case "NC":
						$ls_codope="NOTA DE CRÉDITO";
						break;
					case "CH":
						$ls_codope="CHEQUE";
						break;
					case "DP":
						$ls_codope="DEPÓSITO";
						break;
					case "RE":
						$ls_codope="RETIRO";
						break;
					case "OP":
						$ls_tabla="scb_movbco_spgop";
						$ls_codope="ORDEN DE PAGO DIRECTA";
						break;
				}

				print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0'>";
				print "	<tr>";
				print "		<td width='450' class='titulo-ventana'>Información del Comprobante</td>";
				print " </tr>";
				print "</table>";
				print "<table width='450' border=0 cellpadding=1 cellspacing=1 align='center' class='formato-blanco'>";
				print "  <tr>";
				print "		<td width='100'><div align='right' class='texto-azul'>Comprobante</div></td>";
				print "		<td width='350'><div align='left'>".$as_numdoc."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>Fecha</div></td>";
				print "		<td><div align='justify'>".$ld_fecha."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>Descripci&oacute;n </div></td>";
				print "		<td><div align='justify'>".$ls_conmov."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>Operaci&oacute;n </div></td>";
				print "		<td><div align='left'>".$ls_codope."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>Banco </div></td>";
				print "		<td><div align='left'>".$ls_codban." - ".$ls_nomban."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>".$ls_destino."</div></td>";
				print "		<td><div align='left'>".$ls_nombre_destino."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'></div></td>";
				print "		<td><div align='left'></div></td>";
				print "  </tr>";
				print "</table>";
				$ls_sql="SELECT codestpro, estcla, spg_cuenta, monto ".
						"  FROM ".$ls_tabla." ".
						" WHERE codemp='".$ls_codemp."' ".
						"   AND numdoc='".$ls_numdoc."' ".
						"   AND codban='".$as_codban."' ".
						"   AND ctaban='".$as_ctaban."' ".
						"   AND codope='".$as_codope."' ";
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
					print "		<td colspan='5' class='titulo-celdanew'>Detalle Presupuestario Gasto</td>";
					print " </tr>";
					print " <tr class=titulo-celdanew>";
					print "		<td width='150'>".$ls_titulo."</td>";
					print "		<td width='50'>Estatus</td>";
					print "		<td width='80'>Cuenta</td>";
					print "		<td width='80'>Monto</td>";
					print "		<td width='90'>Disponibilidad</td>";
					print "	</tr>";
					$li_total=0;
					while(!$rs_data2->EOF)
					{
						$ls_cuenta=$rs_data2->fields["spg_cuenta"];
						$li_total=$li_total+$rs_data2->fields["monto"];
						$li_monto=$in_class_mis->uf_formatonumerico($rs_data2->fields["monto"]);
						$ls_codestpro=$rs_data2->fields["codestpro"];
						$ls_estcla=$rs_data2->fields["estcla"];
						$ls_programatica="";
						$ls_estatus="";
						$ls_imagen='blank.gif';
						if($as_disponibilidad==1)
						{
							$ls_imagen='ok.png';
							$li_disponibilidad=0;
							uf_disponibilidad($ls_codemp,$rs_data2,$io_int_spg,&$ls_imagen,&$li_disponibilidad);
							$li_disponibilidad=$in_class_mis->uf_formatonumerico($li_disponibilidad);
						}
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
						print "<tr class=celdas-blancas>";
						print "<td align=center width='150'>".$ls_programatica."</td>";
						print "<td align=center width='50'>".$ls_estatus."</td>";
						print "<td align=center width='80'>".$ls_cuenta."</td>";
						print "<td align=right width='80'>".$li_monto."  </td>";
						print "<td align=center width='90'><img src='../shared/imagebank/".$ls_imagen."'></td>";
						print "</tr>";			
						$rs_data2->MoveNext();	
					}
					$li_total=$in_class_mis->uf_formatonumerico($li_total);
					print "	<tr class=celdas-blancas>";
					print "		<td colspan='3' align='right' class='texto-azul'>Total</td>";
					print "		<td width='100' align='right' class='texto-azul'>".$li_total."</td>";
					print " </tr>";
					print "</table>";
					$io_sql2->free_result($rs_data2);	
				}
				
				$ls_sql="SELECT spi_cuenta, monto, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5 ".
						"  FROM scb_movbco_spi ".
						" WHERE codemp='".$ls_codemp."' ".
						"   AND numdoc='".$ls_numdoc."' ".
						"   AND codban='".$as_codban."' ".
						"   AND ctaban='".$as_ctaban."' ".
						"   AND codope='".$as_codope."' ";
				$rs_data2=$io_sql2->select($ls_sql);
				if($rs_data2===false)
				{
					$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql2->message)); 
				}
				else
				{
				    $ls_estmodest     = $_SESSION["la_empresa"]["estmodest"];
					$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
					$li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
					$li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
					$li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
					$li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
					$li_estpreing = $_SESSION["la_empresa"]["estpreing"];
					if ($li_estpreing==1)
					{					
						print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
						print "	<tr class='titulo-celdanew' >";
						print "		<td colspan='8'>Detalle Presupuestario de Ingreso</td>";
						print " </tr>";
						print " <tr class=titulo-celdanew>";
						print "		<td colspan='2' width='225'>Estructura Presupuestaria</td>";
						print "		<td colspan='2' width='225'>Cuenta</td>";
						print "		<td width='100'>Monto</td>";
						print "	</tr>";
					}
					else
					{
						print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
						print "	<tr>";
						print "		<td colspan='4' class='titulo-celdanew'>Detalle Presupuestario de Ingreso</td>";
						print " </tr>";
						print " <tr class=titulo-celdanew>";
						print "		<td colspan='2' width='225'>Cuenta</td>";
						print "		<td width='100'>Monto</td>";
						print "	</tr>";
					}
					$li_total=0;					
					while(!$rs_data2->EOF)
					{
						$ls_cuenta=$rs_data2->fields["spi_cuenta"];
						$li_total=$li_total+$rs_data2->fields["monto"];
						$li_monto=$in_class_mis->uf_formatonumerico($rs_data2->fields["monto"]);
						
						$ls_codestpro1    = trim(substr(substr($rs_data2->fields["codestpro1"],0,25),-$li_loncodestpro1));
                        $ls_codestpro2    = trim(substr(substr($rs_data2->fields["codestpro2"],0,25),-$li_loncodestpro2));
						$ls_codestpro3    = trim(substr(substr($rs_data2->fields["codestpro3"],0,25),-$li_loncodestpro3));
						if ($ls_estmodest==2)
						{
						  $ls_denestcla="";
						  $ls_codestpro4   = trim(substr(substr($rs_data2->fields["codestpro4"],0,25),-$li_loncodestpro4));
						  $ls_codestpro5   = trim(substr(substr($rs_data2->fields["codestpro5"],0,25),-$li_loncodestpro5));
						  $ls_programatica = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3."-".$ls_codestpro4."-".$ls_codestpro5;			
						  }
						  else
						  {
						  	$ls_programatica = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3;			
						  }
																						  		
						if ($li_estpreing==1)
					    {
							print "<tr class=celdas-blancas>";
							print "<td colspan='2'  align=center width='225'>".$ls_programatica."</td>";
							print "<td colspan='2'  align=center width='225'>".$ls_cuenta."</td>";
							print "<td align=right width='100'>".$li_monto."  </td>";
							print "</tr>";	  
						}
						else
						{
							print "<tr class=celdas-blancas>";
							print "<td colspan='2'  align=center width='225'>".$ls_cuenta."</td>";
							print "<td align=right width='100'>".$li_monto."  </td>";
							print "</tr>";
						}			
						$rs_data2->MoveNext();	
					}
					if ($li_estpreing==1)
					{
						$li_total=$in_class_mis->uf_formatonumerico($li_total);
						print "	<tr class=celdas-blancas>";
						print "		<td colspan='4' width='450' align='right' class='texto-azul'>Total</td>";
						print "		<td width='100' align='right' class='texto-azul'>".$li_total."</td>";
						print " </tr>";
						print "</table>";
					}
					else
					{
						$li_total=$in_class_mis->uf_formatonumerico($li_total);
						print "	<tr class=celdas-blancas>";
						print "		<td colspan='2' width='225' align='right' class='texto-azul'>Total</td>";
						print "		<td width='100' align='right' class='texto-azul'>".$li_total."</td>";
						print " </tr>";
						print "</table>";

					}
					$io_sql2->free_result($rs_data2);	
				}

				$ls_sql="SELECT scg_cuenta, debhab, monto ".
						"  FROM scb_movbco_scg ".
						" WHERE codemp='".$ls_codemp."' ".
						"   AND numdoc='".$ls_numdoc."' ".
						"   AND codban='".$as_codban."' ".
						"   AND ctaban='".$as_ctaban."' ".
						"   AND codope='".$as_codope."' ".
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
					while(!$rs_data2->EOF)
					{
						$ls_cuenta=$rs_data2->fields["scg_cuenta"];
						$li_monto=$rs_data2->fields["monto"];
						$ls_debhab=$rs_data2->fields["debhab"];
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
						$rs_data2->MoveNext();	
					}
					$li_total_deb=$in_class_mis->uf_formatonumerico($li_total_deb);
					$li_total_hab=$in_class_mis->uf_formatonumerico($li_total_hab);
					print "	<tr>";
					print "		<td align=right class='texto-azul'>Total</td>";
					print "		<td align=right class='texto-azul'>".$li_total_deb."</td>";
					print "		<td align=right class='texto-azul'>".$li_total_hab."</td>";
					print " </tr>";
					print "</table>";
					$io_sql2->free_result($rs_data2);
				}
				print "<br><br>";	
				$rs_data->MoveNext();	
			}
			$io_sql->free_result($rs_data);	
		}
   }
   //----------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_disponibilidad($ls_codemp,$rs_data2,$io_int_spg,&$ls_imagen,&$li_disponibilidad)
    { 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_disponibilidad
		//		   Access: public 
		//       Argument: uf_disponibilidad //  data
		//	  Description: busca la disponibilidad presupuiestaria 
		//	      Returns: mensaje
		//	   Creado Por: Ing. Yesenia Moreno
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_cuenta=$rs_data2->fields["spg_cuenta"];
		$ls_estprog[0]=substr($rs_data2->fields["codestpro"],0,25);
		$ls_estprog[1]=substr($rs_data2->fields["codestpro"],25,25);
		$ls_estprog[2]=substr($rs_data2->fields["codestpro"],50,25);
		$ls_estprog[3]=substr($rs_data2->fields["codestpro"],75,25);
		$ls_estprog[4]=substr($rs_data2->fields["codestpro"],100,25);
		$ls_estprog[5]=$rs_data2->fields["estcla"];
		$la_empresa=$_SESSION["la_empresa"];
		$ls_vali_nivel=$la_empresa["vali_nivel"];
		if($ls_vali_nivel==5)
		{
			$ls_formpre=str_replace("-","",$la_empresa["formpre"]);
			$ls_vali_nivel=$io_int_spg->uf_spg_obtener_nivel($ls_formpre);
		}
		if($_SESSION["la_empresa"]["estvaldis"]==0)
		{
			$ls_vali_nivel=0;
		}
		$lb_valido=true;
		$li_nivel=$io_int_spg->uf_spg_obtener_nivel($ls_cuenta);
		if ($li_nivel <= $ls_vali_nivel)
		{
			$ls_status="";
			$li_asignado=0;
			$li_aumento=0;
			$li_disminucion=0;
			$li_precomprometido=0;
			$li_comprometido=0;
			$li_causado=0;
			$li_pagado=0;
			$io_int_spg->uf_spg_saldo_select($ls_codemp,$ls_estprog,$ls_cuenta,$ls_status,$li_asignado,$li_aumento,$li_disminucion,
											 $li_precomprometido,$li_comprometido,$li_causado,$li_pagado,'ACTUAL');
			$li_disponibilidad=(($li_asignado + $li_aumento) - ( $li_disminucion + $li_comprometido + $li_precomprometido));
			if(round($rs_data2->fields["monto"],2) >= round($li_disponibilidad,2))
			{
				$ls_imagen='failed.png';
				$lb_valido=false;
			}
			if($lb_valido)
			{
				$ls_status="";
				$li_asignado=0;
				$li_aumento=0;
				$li_disminucion=0;
				$li_precomprometido=0;
				$li_comprometido=0;
				$li_causado=0;
				$li_pagado=0;
				$io_int_spg->uf_spg_saldo_select($ls_codemp,$ls_estprog,$ls_cuenta,$ls_status,$li_asignado,$li_aumento,$li_disminucion,
												 $li_precomprometido,$li_comprometido,$li_causado,$li_pagado,'COMPROBANTE');
				$li_disponibilidad=(($li_asignado + $li_aumento) - ( $li_disminucion + $li_comprometido + $li_precomprometido));
				if(round($rs_data2->fields["monto"],2) >= round($li_disponibilidad,2))
				{
					$ls_imagen='failed.png';
					$lb_valido=false;
				}
			}				
		} 	
		return true;
	} // end function uf_show_error_disponible
	//-----------------------------------------------------------------------------------------------------------------------------------
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
	require_once("class_folder/class_funciones_mis.php");
	$in_class_mis=new class_funciones_mis();
	$ls_numdoc=$in_class_mis->uf_obtenervalor_get("numdoc","");
	$ls_codban=$in_class_mis->uf_obtenervalor_get("codban","");
	$ls_ctaban=$in_class_mis->uf_obtenervalor_get("ctaban","");
	$ls_codope=$in_class_mis->uf_obtenervalor_get("codope","");
	$ls_disponibilidad=$in_class_mis->uf_obtenervalor_get("disponibilidad","0");
	uf_imprimirresultados($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope,$ls_disponibilidad);
?>
</div>
</form>
</body>
</html>