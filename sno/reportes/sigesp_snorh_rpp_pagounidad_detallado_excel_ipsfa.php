<?php
    session_start();   
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
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//	    		   as_desnom // descripción de la nómina
		//	    		   as_periodo // período actual de la nómina
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2010 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_pagosunidadadmin.php",$ls_descripcion);
		return $lb_valido;
	}		
	//--------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "pagonomina.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
     
	//-----------------------------------------------------  Instancia de las clases ------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");	
	$ls_bolivares="";
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();					
    $ls_bolivares ="Bs.";
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="Resumen de Pagos por Unidad Administrativa Detallado";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperides=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_unidaddes=$io_fun_nomina->uf_obtenervalor_get("codunides","");
	$ls_unidadhas=$io_fun_nomina->uf_obtenervalor_get("codunihas","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","");
	$ls_conceptos=$io_fun_nomina->uf_obtenervalor_get("conceptos","");
	$ld_aniodesde=substr($io_fun_nomina->uf_obtenervalor_get("fecdesper",""),6,4);
	$ld_aniohasta=substr($io_fun_nomina->uf_obtenervalor_get("fechasper",""),6,4);
	$ld_fecdesper=$io_fun_nomina->uf_obtenervalor_get("fecdesper","");
	$ld_fechasper=$io_fun_nomina->uf_obtenervalor_get("fechasper","");
	$ld_fechahasta=$io_funciones->uf_convertirdatetobd($io_fun_nomina->uf_obtenervalor_get("fechasper",""));
	$ls_coddeddes=$io_fun_nomina->uf_obtenervalor_get("coddeddes","");
	$ls_coddedhas=$io_fun_nomina->uf_obtenervalor_get("coddedhas","");
	$ls_codtipperdes=$io_fun_nomina->uf_obtenervalor_get("codtipperdes","");
	$ls_codtipperhas=$io_fun_nomina->uf_obtenervalor_get("codtipperhas","");
	$ls_conceptos="'".$ls_conceptos."'";
	$ls_conceptos=str_replace("-","','",$ls_conceptos);
	$ls_periodo= "Periodo Desde: ".$ld_fecdesper." - Período Hasta: ".$ld_fechasper;
	
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_rango,$ls_periodo); // Seguridad de Reporte
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_seleccionar_nominaunidad($ls_codnomdes,$ls_codnomhas,$ls_codperides,$ls_codperhas,$ls_orden,$ld_aniodesde,
														   $ld_aniohasta,$ls_coddeddes,$ls_coddedhas,$ls_codtipperdes,$ls_codtipperhas); 
	}
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
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
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array("num_format"=> "#,##0.00"));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->set_column(0,0,60);
		$lo_hoja->set_column(1,1,15);
		$lo_hoja->set_column(2,2,60);
		$lo_hoja->set_column(3,4,20);
		$lo_hoja->write(0,2,$ls_titulo,$lo_encabezado);
		$lo_hoja->write(1,2,$ls_periodo,$lo_encabezado);
		$li_fila=0;
		$li_totalpersonal=0;
		while ((!$io_report->rs_data->EOF)&&($lb_valido))
		{  
			$ls_codnom=$io_report->rs_data->fields["codnom"];
			$ls_desnom=$io_report->rs_data->fields["desnom"];		  
			$ls_racnom=$io_report->rs_data->fields["racnom"];		  
			$li_fila++;	  
			$li_fila++;	  
			$li_fila++;	  
			$lo_hoja->write($li_fila,0,$ls_codnom."  -  ".$ls_desnom,$lo_titulo);
			$lb_valido=$io_report->uf_pagos_unidad($ls_codnom, $ls_codperides, $ls_codperhas,$ls_unidaddes,$ls_unidadhas,$ld_aniodesde,$ld_aniohasta,
												   $ls_coddeddes,$ls_coddedhas,$ls_codtipperdes,$ls_codtipperhas,$ls_orden);
			while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
			{  
				$ls_desuniadm=$io_report->rs_detalle->fields["desuniadm"];			  
				$ls_monto=$io_report->rs_detalle->fields["monnetres"];			  
				$ls_uni1=$io_report->rs_detalle->fields["minorguniadm"];	
				$ls_uni2=$io_report->rs_detalle->fields["ofiuniadm"];	
				$ls_uni3=$io_report->rs_detalle->fields["uniuniadm"];	
				$ls_uni4=$io_report->rs_detalle->fields["depuniadm"];	
				$ls_uni5=$io_report->rs_detalle->fields["prouniadm"];	
				$ls_unidad=$ls_uni1."-".$ls_uni2."-".$ls_uni3."-".$ls_uni4."-".$ls_uni5."      ".$ls_desuniadm;
				$li_cantidad=$io_report->rs_detalle->fields["totalpersonal"];
				$io_report->DS_detalle->reset_ds();
				if($li_cantidad>0)
				{
					$li_fila++;	  
					$li_fila++;	  
					$lo_hoja->write($li_fila,0,$ls_unidad,$lo_titulo);
					$li_fila++;	  
					$lo_hoja->write($li_fila,0,"APELLIDOS Y NOMBRES",$lo_titulo);
					$lo_hoja->write($li_fila,1,"CÉDULA",$lo_titulo);
					$lb_valido=$io_report->uf_pagos_unidad_concepto_excel($ls_codnom,$ls_codperides,$ls_codperhas,$ls_conceptos,$ld_aniodesde,$ld_aniohasta);				
					$li_col=1;
					while(!$io_report->rs_detalle3->EOF)
					{
						$ls_codconc=$io_report->rs_detalle3->fields["codconc"];
						$ls_nomcon=$io_report->rs_detalle3->fields["nomcon"];
						$li_col++;
						$lo_hoja->set_column($li_col,$li_col,20);
						$lo_hoja->write($li_fila, $li_col, $ls_nomcon,$lo_titulo);
						$io_report->DS_detalle->insertRow("codconc",$ls_codconc);
						$io_report->DS_detalle->insertRow("columna",$li_col);
						$io_report->DS_detalle->insertRow("acumulado",0);
						$io_report->rs_detalle3->MoveNext();
					}
					$li_col++;
					$lo_hoja->set_column($li_col,$li_col,20);
					$lo_hoja->write($li_fila, $li_col, "TOTAL",$lo_titulo);
					$io_report->DS_detalle->insertRow("codconc","TOTAL****");
					$io_report->DS_detalle->insertRow("columna",$li_col);
					$io_report->DS_detalle->insertRow("acumulado",0);
					$li_col++;
					$lo_hoja->set_column($li_col,$li_col,20);
					$lo_hoja->write($li_fila,$li_col,"TIEMPO DE SERVICIO",$lo_titulo);
					$li_col++;
					$lo_hoja->set_column($li_col,$li_col,20);
					$lo_hoja->write($li_fila,$li_col,"FECHA DE INGRESO",$lo_titulo);
					$li_colfinal=$li_col;
				}
				$lb_valido=$io_report->uf_pagos_unidad_detallado($ls_codnom,$ls_codperides,$ls_codperhas,$ls_uni1,$ls_uni2,$ls_uni3,$ls_uni4,$ls_uni5,
																 $ls_conceptos,$ld_aniodesde,$ld_aniohasta,$ls_coddeddes,$ls_coddedhas,$ls_codtipperdes,$ls_codtipperhas);				
				$li_cantidad=0;
				$li_total_unidad=0;
				while ((!$io_report->rs_detalle2->EOF)&&($lb_valido))
				{
					$li_cantidad++;
					$ls_codper=$io_report->rs_detalle2->fields["codper"];
					$ls_cedper=$io_report->rs_detalle2->fields["cedper"];
					$ls_nomper=$io_report->rs_detalle2->fields["apeper"].", ".$io_report->rs_detalle2->fields["nomper"];
					$ld_fecingper=$io_report->rs_detalle2->fields["fecingper"];
					$ls_denasicar=$io_report->rs_detalle2->fields["denasicar"];
					$ls_descar=$io_report->rs_detalle2->fields["descar"];
					$ls_descasicar=trim($io_report->rs_detalle2->fields["descasicar"]);
					$ls_cargo="";
					if ($ls_descasicar != "")
					{
						$ls_cargo=$ls_descasicar;
					}
					else
					{
						switch($ls_racnom)
						{
							case "0";
								$ls_cargo=$ls_descar;
							break;
							case "1";
								$ls_cargo=$ls_denasicar;
							break;
						}
					}
					
					$ld_fechasper=substr($ld_fechahasta,0,4);
					$ld_fecing=substr($ld_fecingper,0,4);
					$li_tiemposervicio=$ld_fechasper-$ld_fecing;
					$ld_fechasper=$ld_fechahasta;
					if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
					{
						$li_tiemposervicio=$li_tiemposervicio-1;
					}
					else
					{
						if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
						{
							if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
							{
								$li_tiemposervicio=$li_tiemposervicio-1;
							}
						}
					}
					$ld_fecingper=$io_funciones->uf_convertirfecmostrar($ld_fecingper);
					$li_fila++;	  
					$lo_hoja->write($li_fila,0,$ls_nomper,$lo_dataleft);
					$lo_hoja->write($li_fila,1,$ls_cedper,$lo_datacenter);
					$lo_hoja->write($li_fila,($li_colfinal-1),$li_tiemposervicio,$lo_datacenter);
					$lo_hoja->write($li_fila,$li_colfinal,$ld_fecingper,$lo_datacenter);
					$lb_valido=$io_report->uf_pagos_unidad_conceptos($ls_codnom,$ls_codperides,$ls_codperhas,$ls_conceptos,$ls_codper,$ld_aniodesde,$ld_aniohasta);				
					$li_conceptos=0;
					$li_total_conceptos=0;
					while ((!$io_report->rs_detalle3->EOF)&&($lb_valido))
					{
						$ls_codconc=$io_report->rs_detalle3->fields["codconc"];
						$ls_nomcon=$io_report->rs_detalle3->fields["nomcon"];
						$li_valsal=$io_report->rs_detalle3->fields["valsal"];
						$li_total_conceptos=$li_total_conceptos+$li_valsal;
						$li_find=$io_report->DS_detalle->find("codconc",$ls_codconc);
						$li_col=$io_report->DS_detalle->getValue("columna",$li_find);
						$li_acumulado=$io_report->DS_detalle->getValue("acumulado",$li_find);
						$li_acumulado= $li_acumulado+$li_valsal;
						$io_report->DS_detalle->updateRow("acumulado",$li_acumulado,$li_find);	
						$li_valsal=number_format($li_valsal,2,",",".");
						$lo_hoja->write($li_fila, $li_col, $li_valsal, $lo_dataright);
						$io_report->rs_detalle3->MoveNext();			      
					}
					if($li_total_conceptos>0)
					{
						$li_total_unidad=$li_total_unidad+$li_total_conceptos;
						$li_find=$io_report->DS_detalle->find("codconc","TOTAL****");
						$li_col=$io_report->DS_detalle->getValue("columna",$li_find);
						$li_acumulado=$io_report->DS_detalle->getValue("acumulado",$li_find);
						$li_acumulado= $li_acumulado+$li_total_conceptos;
						$io_report->DS_detalle->updateRow("acumulado",$li_acumulado,$li_find);	
						$li_total_conceptos=number_format($li_total_conceptos,2,",",".");
						$lo_hoja->write($li_fila, $li_col, $li_total_conceptos, $lo_dataright);
					}
					$li_fila++;	  
					$lo_hoja->write($li_fila,0,"CARGO: ".$ls_cargo,$lo_dataleft);
					$io_report->rs_detalle2->MoveNext();			      
				}
				if($li_cantidad>0)
				{
					$li_totalpersonal=$li_totalpersonal+$li_cantidad;
					$li_total_unidad=number_format($li_total_unidad,2,",",".");
					$li_fila++;	  
					$lo_hoja->write($li_fila,0,"TOTAL POR UBICACION --> ",$lo_titulo);
					$lo_hoja->write($li_fila,1,$li_cantidad,$lo_titulo);
					$li_i=$io_report->DS_detalle->getRowCount("codconc");
					for($ll_row=1;($ll_row<=$li_i);$ll_row++)
					{
						$ls_codconc=$io_report->DS_detalle->getValue("codconc",$ll_row);
						$li_col=$io_report->DS_detalle->getValue("columna",$ll_row);
						$li_acumulado=$io_report->DS_detalle->getValue("acumulado",$ll_row);
						$li_find=$io_report->DS->find("codconc",$ls_codconc);
						if($li_find>0)
						{  
							$li_total=$io_report->DS->getValue("total",$li_find);
							$li_total= $li_total+$li_acumulado;
							$io_report->DS->updateRow("total",$li_total,$li_find);	
						}
						else
						{
							$io_report->DS->insertRow("codconc",$ls_codconc);
							$io_report->DS->insertRow("columna",$li_col);
							$io_report->DS->insertRow("total",$li_acumulado);
						}
						$li_acumulado=number_format($li_acumulado,2,",",".");
						$lo_hoja->write($li_fila, $li_col, $li_acumulado, $lo_dataright);
					}
				}
				
				$io_report->rs_detalle->MoveNext();	
			}
			$io_report->rs_data->MoveNext();	  
		}
		if($li_totalpersonal>0)
		{
			$li_totalmonto=number_format($li_totalmonto,2,",",".");
			$li_fila++;	  
			$lo_hoja->write($li_fila,0,"TOTAL GENERAL ",$lo_titulo);
			$lo_hoja->write($li_fila,1,$li_totalpersonal,$lo_titulo);
			$li_i=$io_report->DS->getRowCount("codconc");
			for($ll_row=1;($ll_row<=$li_i);$ll_row++)
			{
				$li_col=$io_report->DS->getValue("columna",$ll_row);
				$li_totalacumulado=$io_report->DS->getValue("total",$ll_row);
				$li_totalacumulado=number_format($li_totalacumulado,2,",",".");
				$lo_hoja->write($li_fila, $li_col, $li_totalacumulado, $lo_dataright);
			}
		}
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"pagounidaddetallado.xls\"");
		header("Content-Disposition: inline; filename=\"pagounidaddetallado.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 