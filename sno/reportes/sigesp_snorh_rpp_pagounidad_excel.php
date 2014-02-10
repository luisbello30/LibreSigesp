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
		// Fecha Creación: 27/04/2006 
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
	$ls_titulo="Resumen de Pagos por Unidad Administrativa";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperides=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_unidaddes=$io_fun_nomina->uf_obtenervalor_get("codunides","");
	$ls_unidadhas=$io_fun_nomina->uf_obtenervalor_get("codunihas","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","");
	$ld_aniodesde=substr($io_fun_nomina->uf_obtenervalor_get("fecdesper",""),6,4);
	$ld_aniohasta=substr($io_fun_nomina->uf_obtenervalor_get("fechasper",""),6,4);
	$ld_fecdesper=$io_fun_nomina->uf_obtenervalor_get("fecdesper","");
	$ld_fechasper=$io_fun_nomina->uf_obtenervalor_get("fechasper","");
	$ls_coddeddes=$io_fun_nomina->uf_obtenervalor_get("coddeddes","");
	$ls_coddedhas=$io_fun_nomina->uf_obtenervalor_get("coddedhas","");
	$ls_codtipperdes=$io_fun_nomina->uf_obtenervalor_get("codtipperdes","");
	$ls_codtipperhas=$io_fun_nomina->uf_obtenervalor_get("codtipperhas","");
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
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,20);
		$lo_hoja->write(0,2,$ls_titulo,$lo_encabezado);
		$lo_hoja->write(1,2,$ls_periodo,$lo_encabezado);
		$li_fila=0;
		while ((!$io_report->rs_data->EOF)&&($lb_valido))
		{  
			$ls_codnom=$io_report->rs_data->fields["codnom"];
			$ls_desnom=$io_report->rs_data->fields["desnom"];	
			$li_fila++;	  
			$li_fila++;	  
			$li_fila++;	  
			$lo_hoja->write($li_fila,0,$ls_codnom."  -  ".$ls_desnom,$lo_titulo);
			$li_fila++;	  
			$lo_hoja->write($li_fila,0,"UNIDAD",$lo_titulo);
			$lo_hoja->write($li_fila,1,"CANTIDAD",$lo_titulo);
			$lo_hoja->write($li_fila,2,"MONTO",$lo_titulo);
			$lb_valido=$io_report->uf_pagos_unidad($ls_codnom,$ls_codperides,$ls_codperhas,$ls_unidaddes,$ls_unidadhas,$ld_aniodesde,$ld_aniohasta,
												   $ls_coddeddes,$ls_coddedhas,$ls_codtipperdes,$ls_codtipperhas,$ls_orden);
			$li_j=0;
			$total_monto=0;
			$total_cantidad=0;
			while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
			{  
				$ls_desuniadm=$io_report->rs_detalle->fields["desuniadm"];			  
				$ls_monto=$io_report->rs_detalle->fields["monnetres"];			  
				$total_monto=$total_monto+$ls_monto;
				$ls_uni1=$io_report->rs_detalle->fields["minorguniadm"];	
				$ls_uni2=$io_report->rs_detalle->fields["ofiuniadm"];	
				$ls_uni3=$io_report->rs_detalle->fields["uniuniadm"];	
				$ls_uni4=$io_report->rs_detalle->fields["depuniadm"];	
				$ls_uni5=$io_report->rs_detalle->fields["prouniadm"];	
				$ls_cantidad=$io_report->rs_detalle->fields["totalpersonal"];
				$total_cantidad=$total_cantidad+$ls_cantidad;		  
				$li_fila++;	  
				$lo_hoja->write($li_fila,0,$ls_desuniadm,$lo_dataleft);
				$lo_hoja->write($li_fila,1,$ls_cantidad,$lo_datacenter);
				$lo_hoja->write($li_fila,2,$io_fun_nomina->uf_formatonumerico($ls_monto),$lo_dataright);
				$io_report->rs_detalle->MoveNext();			      
			}
			if ($total_monto>0)
			{
				$li_fila++;	  
				$lo_hoja->write($li_fila,0,"TOTAL POR NÓMINA",$lo_datacenter);
				$lo_hoja->write($li_fila,1,$total_cantidad,$lo_datacenter);
				$lo_hoja->write($li_fila,2,$io_fun_nomina->uf_formatonumerico($total_monto),$lo_dataright);
			}
			$io_report->rs_data->MoveNext();	  
		}
		$li_fila++;	  
		$li_fila++;	  
		$li_fila++;	  
		$lo_hoja->write($li_fila,0,"RESUMEN POR UNIDAD",$lo_titulo);
		$li_fila++;	  
		$lo_hoja->write($li_fila,0,"UNIDAD",$lo_titulo);
		$lo_hoja->write($li_fila,1,"CANTIDAD",$lo_titulo);
		$lo_hoja->write($li_fila,2,"MONTO",$lo_titulo);
		$lb_valido=$io_report->uf_pagos_unidad_totales($ls_codnomdes,$ls_codnomhas,$ls_codperides,$ls_codperhas,$ls_unidaddes,$ls_unidadhas,
													   $ld_aniodesde,$ld_aniohasta,$ls_coddeddes,$ls_coddedhas,$ls_codtipperdes,$ls_codtipperhas,
													   $ls_orden);						   
		$total_monto=0;
		$total_cantidad=0;		  
		while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
		{
			$ls_desuniadm=$io_report->rs_detalle->fields["desuniadm"];			  
			$ls_monto=$io_report->rs_detalle->fields["monnetres"];			  
			$ls_uni1=$io_report->rs_detalle->fields["minorguniadm"];	
			$ls_uni2=$io_report->rs_detalle->fields["ofiuniadm"];	
			$ls_uni3=$io_report->rs_detalle->fields["uniuniadm"];	
			$ls_uni4=$io_report->rs_detalle->fields["depuniadm"];	
			$ls_uni5=$io_report->rs_detalle->fields["prouniadm"];
			$ls_cantidad=$io_report->rs_detalle->fields["totalpersonal"];
			$total_monto=$total_monto+$ls_monto;
			$total_cantidad=$total_cantidad+$ls_cantidad;		  
			$li_fila++;	  
			$lo_hoja->write($li_fila,0,$ls_desuniadm,$lo_dataleft);
			$lo_hoja->write($li_fila,1,$ls_cantidad,$lo_datacenter);
			$lo_hoja->write($li_fila,2,$io_fun_nomina->uf_formatonumerico($ls_monto),$lo_dataright);
			$io_report->rs_detalle->MoveNext();	  
		}
		if ($total_monto>0)
		{
			$li_fila++;	  
			$lo_hoja->write($li_fila,0,"TOTAL POR UNIDAD",$lo_datacenter);
			$lo_hoja->write($li_fila,1,$total_cantidad,$lo_datacenter);
			$lo_hoja->write($li_fila,2,$io_fun_nomina->uf_formatonumerico($total_monto),$lo_dataright);
		}
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"pagounidad.xls\"");
		header("Content-Disposition: inline; filename=\"pagounidad.xls\"");
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