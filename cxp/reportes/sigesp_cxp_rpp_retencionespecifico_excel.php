<?php
session_start();

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//    REPORTE: Retencion Especifico
	//  ORGANISMO: Ninguno en particular
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 10/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Gener� el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesespecifico.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function iif($condition,$val_if_true,$val_if_false="")
	{
		if ($condition)
			return $val_if_true;
		else
			return $val_if_false;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "retenciones_especifico.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();

	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo="LISTADO DE RETENCIONES";
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codded=$io_fun_cxp->uf_obtenervalor_get("codded","");
	$ls_coddedhas=$io_fun_cxp->uf_obtenervalor_get("coddedhas","");
	//$ls_dended=$io_fun_cxp->uf_obtenervalor_get("dended","");
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=$io_fun_cxp->uf_obtenervalor_get("codprobendes","");
	$ls_codprobenhas=$io_fun_cxp->uf_obtenervalor_get("codprobenhas","");
	$ld_fecdes=$io_fun_cxp->uf_obtenervalor_get("fecdes","");
	$ld_fechas=$io_fun_cxp->uf_obtenervalor_get("fechas","");
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	$ls_tipded=$io_fun_cxp->uf_obtenervalor_get("tipded","");
	$ls_tipper=$io_fun_cxp->uf_obtenervalor_get("tipper","");
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_select_retenciones($ls_codded,"",$ls_tipded);
	}
	if($lb_valido===false)
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else
	{
		//-------formato para el reporte----------------------------------------------------------
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');		
		$lo_titulo2= &$lo_libro->addformat();
		$lo_titulo2->set_text_wrap();
		$lo_titulo2->set_bold();
		$lo_titulo2->set_font("Verdana");
		$lo_titulo2->set_align('left');
		$lo_titulo2->set_size('9');		
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		//ANCHO DE LASCOLUMNAS
		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,50);	
		$lo_hoja->set_column(2,12,25);	

		$lo_hoja->write(0,3,$ls_titulo,$lo_encabezado);
		$li_rowcargos=$io_report->DS->getRowCount("codded");//print"s";
		$lb_existe=false;
		$li_z=2;
		for($li_j=1;$li_j<=$li_rowcargos;$li_j++)
		{
			$li_z++;
			$ls_codded= $io_report->DS->data["codded"][$li_j];
			$ls_dended= $io_report->DS->data["dended"][$li_j];
			$li_islr= $io_report->DS->data["islr"][$li_j];
			$li_iva= $io_report->DS->data["iva"][$li_j];
			$li_estretmun= $io_report->DS->data["estretmun"][$li_j];
			$li_retaposol= $io_report->DS->data["retaposol"][$li_j];
			$li_estretmil= $io_report->DS->data["estretmil"][$li_j];
			$lo_hoja->write($li_z,0, "Retencion:",$lo_titulo2);	
			$lo_hoja->write($li_z,1, $ls_codded."  ".$ls_dended,$lo_titulo2);	
			$li_z++;
			$lo_hoja->write($li_z,0, "CEDULA / RIF",$lo_titulo);
			$lo_hoja->write($li_z,1, "PROVEEDOR/BENEFICIARIO",$lo_titulo);	
			$lo_hoja->write($li_z,2, "SOLICITUD DE PAGO",$lo_titulo);	
			$lo_hoja->write($li_z,3, "FECHA",$lo_titulo);	
			$lo_hoja->write($li_z,4, "COMPROBANTE",$lo_titulo);	
			$lo_hoja->write($li_z,5, "MONTO OBJ. RETENC.",$lo_titulo);	
			$lo_hoja->write($li_z,6, "ALICUOTA RETENC.",$lo_titulo);	
			$lo_hoja->write($li_z,7, "MONTO RETENIDO",$lo_titulo);	
			$lo_hoja->write($li_z,8, "MONTO SOLICITUD",$lo_titulo);	
			$lo_hoja->write($li_z,9, "FACTURA",$lo_titulo);	
			$lo_hoja->write($li_z,10, "NUMERO CONTROL",$lo_titulo);	

			$lb_valido=$io_report->uf_retencionesespecifico($ls_codded,$ls_coddedhas,$ls_tipproben,$ls_codprobenhas,$ls_codprobendes,$ld_fecdes,$ld_fechas,$ls_tipper);
			$li_totbase=0;
			$li_totcargos=0;
			$li_totrow=$io_report->ds_detalle->getRowCount("numsol");
			for ($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_codded= $io_report->ds_detalle->data["codded"][$li_i];
				$ls_numsol= " ".$io_report->ds_detalle->data["numsol"][$li_i];
				$ls_nombre= $io_report->ds_detalle->data["nombre"][$li_i];
				$ls_cedrif= $io_report->ds_detalle->data["cedula_rif"][$li_i];
				$ls_numcomiva= $io_report->ds_detalle->data["numcomiva"][$li_i];
				$ls_numrecdoc= $io_report->ds_detalle->data["numrecdoc"][$li_i];
				$ls_numref= $io_report->ds_detalle->data["numref"][$li_i];
				$ls_numcommun= $io_report->ds_detalle->data["numcommun"][$li_i];
				$ls_numcomapo= $io_report->ds_detalle->data["numcomapo"][$li_i];
				$ls_numcommil= $io_report->ds_detalle->data["numcommil"][$li_i];
				$ls_numcomislr= $io_report->ds_detalle->data["numcomislr"][$li_i];
				$li_porded=($io_report->ds_detalle->data["porded"][$li_i]*100);
				if($li_porded>100)
				{
					$li_porded=number_format($io_report->ds_detalle->data["porded"][$li_i],2,',','.');
				}
				else
				{
					$li_porded=number_format($li_porded,2,',','.');
				}
				$ld_fecemisol= $io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecemisol"][$li_i]);
				$li_mon_obj_ret= number_format($io_report->ds_detalle->data["mon_obj_ret"][$li_i],2,',','.');
				$li_monsol= number_format($io_report->ds_detalle->data["monsol"][$li_i],2,',','.');
				$li_monret= number_format($io_report->ds_detalle->data["monret"][$li_i],2,',','.');
				$li_totbase= $li_totbase+$io_report->ds_detalle->data["mon_obj_ret"][$li_i];
				$li_totcargos= $li_totcargos+$io_report->ds_detalle->data["monret"][$li_i];
				$ls_numcom="";
				if($li_iva==1)
				{$ls_numcom=$ls_numcomiva;}
				if($li_estretmun==1)
				{$ls_numcom=$ls_numcommun;}
				if($li_retaposol==1)
				{$ls_numcom=$ls_numcomapo;}
				if (empty($ls_numcom))
				{
					$ls_numcom = iif(empty($ls_numcomiva),iif(empty($ls_numcommun),iif(empty($ls_numcomapo),'',$ls_numcomapo),$ls_numcommun),$ls_numcomiva);						
				}				
				$li_z++;
				$lo_hoja->write($li_z,0, $ls_cedrif,$lo_dataleft);
				$lo_hoja->write($li_z,1, $ls_nombre,$lo_dataleft);	
				$lo_hoja->write($li_z,2, $ls_numsol,$lo_dataleft);	
				$lo_hoja->write($li_z,3, $ld_fecemisol,$lo_datacenter);	
				$lo_hoja->write($li_z,4, $ls_numcom,$lo_datacenter);	
				$lo_hoja->write($li_z,5, '',$lo_dataright);	
				$lo_hoja->write($li_z,6, '',$lo_datacenter);	
				$lo_hoja->write($li_z,7, '',$lo_dataright);
				$lo_hoja->write($li_z,8, $li_monsol,$lo_dataright);
				$lo_hoja->write($li_z,9, $ls_numrecdoc,$lo_datacenter);
				$lo_hoja->write($li_z,10, $ls_numref,$lo_datacenter);
				$lb_valido=$io_report->uf_retencionesespecifico_detalle($ls_codded,trim($ls_numsol));
				while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
				{
					$ls_numsol= str_pad(trim($io_report->rs_detalle->fields["numrecdoc"]),15);
					$ls_nombre="    N� FACTURA ".$ls_numsol."N� Control ".str_pad(trim($io_report->rs_detalle->fields["numref"]),15);
					$ls_numsol="" ;
					$ld_fecemisol= $io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecregdoc"]);
					$ls_numcom="";
					$li_monsol_det= number_format($io_report->rs_detalle->fields["monobjret"],2,',','.');
					$li_monret_det= number_format($io_report->rs_detalle->fields["monret"],2,',','.');
					$li_porded_det=($io_report->rs_detalle->fields["porded"]*100);
					if($li_porded_det>100)
					{
						$li_porded_det=number_format($io_report->rs_detalle->fields["porded"],2,',','.');
					}
					else
					{
						$li_porded_det=number_format($li_porded,2,',','.');
					}
					$li_z++;
					$lo_hoja->write($li_z,1, $ls_nombre,$lo_dataleft);	
					$lo_hoja->write($li_z,2, $ls_numsol,$lo_dataleft);	
					$lo_hoja->write($li_z,3, $ld_fecemisol,$lo_datacenter);	
					$lo_hoja->write($li_z,4, $ls_numcom,$lo_datacenter);	
					$lo_hoja->write($li_z,5, $li_monsol_det,$lo_dataright);	
					$lo_hoja->write($li_z,6, $li_porded_det,$lo_datacenter);	
					$lo_hoja->write($li_z,7, $li_monret_det,$lo_dataright);
					$io_report->rs_detalle->MoveNext();
				}
				$li_z++;
				$lo_hoja->write($li_z,0, '',$lo_dataleft);
				$lo_hoja->write($li_z,1, 'SUB TOTAL',$lo_titulo);	
				$lo_hoja->write($li_z,2, '',$lo_dataleft);	
				$lo_hoja->write($li_z,3, '',$lo_datacenter);	
				$lo_hoja->write($li_z,4, '',$lo_datacenter);	
				$lo_hoja->write($li_z,5, $li_mon_obj_ret,$lo_dataright);	
				$lo_hoja->write($li_z,6, $li_porded,$lo_datacenter);	
				$lo_hoja->write($li_z,7, $li_monret,$lo_dataright);
				$lo_hoja->write($li_z,8, '',$lo_dataright);
				$lo_hoja->write($li_z,9, '',$lo_datacenter);
				$lo_hoja->write($li_z,10, '',$lo_datacenter);
				$li_z++;
				$li_z++;
			}
			$li_z++;
			$li_totbase=number_format($li_totbase,2,',','.');
			$li_totcargos=number_format($li_totcargos,2,',','.');
			$lo_hoja->write($li_z,0, "TOTAL RET.: ".$li_totrow,$lo_dataleft);	
			$lo_hoja->write($li_z,3, "TOTALES MONTOS: ",$lo_titulo);	
			$lo_hoja->write($li_z,5,$li_totbase,$lo_titulo);	
			$lo_hoja->write($li_z,7, $li_totcargos,$lo_titulo);
			$li_z+=2;	
		}
		$la_manuales[0]="";
		switch($ls_tipded)
		{
			case "T":
				if($ls_codded=="")
				{
					$la_manuales[0]['codret']="";
					$la_manuales[1]['codret']="0000000001";
					$la_manuales[2]['codret']="0000000003";
					$la_manuales[3]['codret']="0000000004";
					$la_manuales[4]['codret']="0000000005";
	
					$la_manuales[0]['denret']="";
					$la_manuales[1]['denret']="IVA";
					$la_manuales[2]['denret']="MUNICIPAL";
					$la_manuales[3]['denret']="APORTE SOCIAL";
					$la_manuales[4]['denret']="1 X 1000";
				}
				else
				{
					if($li_iva==1)
					{
						$la_manuales[0]['codret']="";
						$la_manuales[1]['codret']="0000000001";
						$la_manuales[0]['denret']="";
						$la_manuales[1]['denret']="IVA";
					}
					if($li_estretmun==1)
					{
						$la_manuales[0]['codret']="";
						$la_manuales[1]['codret']="0000000003";
						$la_manuales[0]['denret']="";
						$la_manuales[1]['denret']="MUNICIPAL";
					}
					if($li_retaposol==1)
					{
						$la_manuales[0]['codret']="";
						$la_manuales[1]['codret']="0000000004";
						$la_manuales[0]['denret']="";
						$la_manuales[1]['denret']="APORTE SOCIAL";
					}
					if($li_estretmil==1)
					{
						$la_manuales[0]['codret']="";
						$la_manuales[1]['codret']="0000000005";
						$la_manuales[0]['denret']="";
						$la_manuales[1]['denret']="1 X 1000";
					}
				}
			break;
			case "I":
				$la_manuales[0]['codret']="";
				$la_manuales[1]['codret']="0000000001";
				$la_manuales[0]['denret']="";
				$la_manuales[1]['denret']="IVA";
			break;
			case "M":
				$la_manuales[0]['codret']="";
				$la_manuales[1]['codret']="0000000003";
				$la_manuales[0]['denret']="";
				$la_manuales[1]['denret']="MUNICIPAL";
			break;
			case "A":
				$la_manuales[0]['codret']="";
				$la_manuales[1]['codret']="0000000004";
				$la_manuales[0]['denret']="";
				$la_manuales[1]['denret']="APORTE SOCIAL";
			break;
			case "O":
				$la_manuales[0]['codret']="";
				$la_manuales[1]['codret']="0000000005";
				$la_manuales[0]['denret']="";
				$la_manuales[1]['denret']="1 X 1000";
			break;
		}
		$li_total=count($la_manuales);
		for($li_i=1; $li_i< $li_total; $li_i++)
		{
			$ls_codret=$la_manuales[$li_i]['codret'];
			$ls_denret=$la_manuales[$li_i]['denret'];
			$lb_valido=$io_report->uf_retencionesespecificomanual($ls_codret,$ls_tipproben,$ls_codprobenhas,$ls_codprobendes,$ld_fecdes,$ld_fechas,$ls_tipper);
			$li_totbase=0;
			$li_totcargos=0;
			$li_j=0;
			$li_z++;
			$lo_hoja->write($li_z,0, "Retencion Manual:",$lo_titulo2);	
			$lo_hoja->write($li_z,1, $ls_denret,$lo_titulo2);	
			$li_z++;
			$lo_hoja->write($li_z,0, "CEDULA / RIF",$lo_titulo);
			$lo_hoja->write($li_z,1, "PROVEEDOR/BENEFICIARIO",$lo_titulo);	
			$lo_hoja->write($li_z,2, "FECHA",$lo_titulo);	
			$lo_hoja->write($li_z,3, "COMPROBANTE",$lo_titulo);	
			$lo_hoja->write($li_z,4, "MONTO OBJ. RETENC.",$lo_titulo);	
			$lo_hoja->write($li_z,5, "ALICUOTA RETENC.",$lo_titulo);	
			$lo_hoja->write($li_z,6, "MONTO RETENIDO",$lo_titulo);	
			while (!$io_report->rs_detalle->EOF)
			{
				$ls_rif=" ".$io_report->rs_detalle->fields["rif"];
				$ls_nombre=$io_report->rs_detalle->fields["nomsujret"];
				$ld_fecemisol= $io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecrep"]);
				$ls_numcom=$io_report->rs_detalle->fields["numcom"];
				$li_monsol= number_format($io_report->rs_detalle->fields["basimp"],2,',','.');
				$li_monret= number_format($io_report->rs_detalle->fields["totimp"],2,',','.');
				$li_porded=($io_report->rs_detalle->fields["porimp"]*100);
				$li_totbase=$li_totbase+$io_report->rs_detalle->fields["basimp"];
				$li_totcargos=$li_totcargos+$io_report->rs_detalle->fields["totimp"];
				if($li_porded>100)
				{
					$li_porded=number_format($io_report->rs_detalle->fields["porimp"],2,',','.');
				}
				else
				{
					$li_porded=number_format($li_porded,2,',','.');
				}
				$li_z++;
				$lo_hoja->write($li_z,0, $ls_rif,$lo_datacenter);	
				$lo_hoja->write($li_z,1, $ls_nombre,$lo_dataleft);	
				$lo_hoja->write($li_z,2, $ld_fecemisol,$lo_datacenter);	
				$lo_hoja->write($li_z,3, $ls_numcom,$lo_datacenter);	
				$lo_hoja->write($li_z,4, $li_monsol,$lo_dataright);	
				$lo_hoja->write($li_z,5, $li_porded,$lo_datacenter);	
				$lo_hoja->write($li_z,6, $li_monret,$lo_dataright);
				$li_j++;
				$io_report->rs_detalle->MoveNext();
			}
			$li_z++;
			$li_totbase=number_format($li_totbase,2,',','.');
			$li_totcargos=number_format($li_totcargos,2,',','.');
			$lo_hoja->write($li_z,0, "TOTAL RET.: ".($li_j-1),$lo_dataleft);	
			$lo_hoja->write($li_z,3, "TOTALES MONTOS: ",$lo_titulo);	
			$lo_hoja->write($li_z,4,$li_totbase,$lo_titulo);	
			$lo_hoja->write($li_z,6, $li_totcargos,$lo_titulo);
			$li_z+=2;	
		}
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"retenciones_especifico.xls\"");
		header("Content-Disposition: inline; filename=\"retenciones_especifico.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);		
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}

?>