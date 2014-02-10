<?php
    session_start();   
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_personal_rac_rec.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listado_personal_rac.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="Listado de Personal";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_anio=$io_fun_nomina->uf_obtenervalor_get("anio","");	
	$ls_mes=$io_fun_nomina->uf_obtenervalor_get("mes","");	
	$ls_peri=$io_fun_nomina->uf_obtenervalor_get("codperi","");	
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","");	
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Listado de Personal RAC en Excel </b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_listadopersonal_personal_rac_rec($ls_codnomdes,$ls_codnomhas,$ls_codperdes,$ls_codperhas,
														   		   $ls_anio,$ls_mes,$ls_peri,$ls_orden); // Obtenemos el detalle del reporte
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
		$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->set_column(0,0,10);
		$lo_hoja->set_column(1,1,15);
		$lo_hoja->set_column(2,2,8);
		$lo_hoja->set_column(3,3,13);
		$lo_hoja->set_column(4,4,40);
		$lo_hoja->set_column(5,5,8);
		$lo_hoja->set_column(6,6,15);
		$lo_hoja->set_column(7,7,15);
		$lo_hoja->set_column(8,8,8);
		$lo_hoja->set_column(9,9,15);
		$lo_hoja->set_column(10,10,50);
		$lo_hoja->set_column(11,11,5);
		$lo_hoja->set_column(12,12,9);
		$lo_hoja->set_column(13,13,20);
		
		$li_row=0;
		$li_totrow=$io_report->DS->getRowCount("codcarnomina");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_codper=$io_report->DS->data["codper"][$li_i];
			$ls_codnom=$io_report->DS->data["codnom"][$li_i];
			$ls_codcarnomina=$io_report->DS->data["codcarnomina"][$li_i];
			$ls_minorguniadm=$io_report->DS->data["minorguniadm"][$li_i];
			$ls_ofiuniadm=$io_report->DS->data["ofiuniadm"][$li_i];
			$ls_uniuniadm=$io_report->DS->data["uniuniadm"][$li_i];
			$ls_depuniadm=$io_report->DS->data["depuniadm"][$li_i];
			$ls_prouniadm=$io_report->DS->data["prouniadm"][$li_i];
			$ls_codubiadm=$ls_minorguniadm.$ls_ofiuniadm.$ls_uniuniadm.$ls_depuniadm.$ls_prouniadm;
			$ls_coddecsercar='00193';
			$ls_codclase=$io_report->DS->data["codigocar"][$li_i];
			$ls_desclase=$io_report->DS->data["denasicar"][$li_i];
			$ls_decescsue='04270';
			
			$li_sueper=$io_report->DS->data["sueper"][$li_i];
			//$li_sueper=str_replace(".","",trim($li_sueper));
			//$li_sueper=str_replace(",",".",trim($li_sueper));
			$li_sueper=number_format(trim($li_sueper),2,".","");
			$li_sueper=str_pad(trim($li_sueper),12,"0",STR_PAD_LEFT);
			$li_sueperdef=$li_sueper;
			
			$lb_valido=$io_report->uf_buscar_conceptos_personal($ls_codper,$ls_codnom,$ls_anio,$ls_peri,$ld_comp,$ld_pripro,$ld_priant,$ld_prihij,$ld_bonvac,$ld_bonagu,$ld_sso,$ld_parfor,$ld_lph);
			
			//$ld_comp=str_replace(".","",$ld_comp);
			//$ld_comp=str_replace(",",".",$ld_comp);
			$ld_comp=number_format(trim($ld_comp),2,".","");
			$ld_comp=str_pad(trim($ld_comp),12,"0",STR_PAD_LEFT);
			$ld_compdef=$ld_comp;
			
			$ls_jortra='000.00';
			$ls_cedper=$io_report->DS->data["cedper"][$li_i];
			$ls_cedper=str_pad(trim($ls_cedper),8,"0",0);
			$ls_nomper=$io_report->DS->data["apeper"][$li_i].", ".$io_report->DS->data["nomper"][$li_i];
			$ls_sitcar=4;
			$ls_accadm='0000000';
			$ls_espacios='                                                          ';

				$li_row=$li_row+1;

				$lo_hoja->write($li_row, 0, " ".$ls_codcarnomina, $lo_datacenter);
				$lo_hoja->write($li_row, 1, " ".$ls_codubiadm, $lo_datacenter);
				$lo_hoja->write($li_row, 2, " ".$ls_coddecsercar, $lo_datacenter);
				$lo_hoja->write($li_row, 3, " ".$ls_codclase, $lo_datacenter);
				$lo_hoja->write($li_row, 4, $ls_desclase, $lo_dataleft);
				$lo_hoja->write($li_row, 5, " ".$ls_decescsue, $lo_datacenter);
				$lo_hoja->write($li_row, 6, " ".$li_sueperdef, $lo_datacenter);
				$lo_hoja->write($li_row, 7, " ".$ld_compdef, $lo_datacenter);
				$lo_hoja->write($li_row, 8, " ".$ls_jortra, $lo_datacenter);
				$lo_hoja->write($li_row, 9, " ".$ls_cedper, $lo_datacenter);
				$lo_hoja->write($li_row, 10, $ls_nomper, $lo_dataleft);
				$lo_hoja->write($li_row, 11, $ls_sitcar, $lo_datacenter);
				$lo_hoja->write($li_row, 12, " ".$ls_accadm, $lo_datacenter);
				$lo_hoja->write($li_row, 13, " ".$ls_espacios, $lo_datacenter);
		}
		$io_report->DS->resetds("codcarnomina");
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listado_personal_rac.xls\"");
		header("Content-Disposition: inline; filename=\"listado_personal_rac.xls\"");
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