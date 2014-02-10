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
	$lo_archivo = tempnam("/tmp", "listado_personal_rec.xls");
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
	$ld_fecha=date("j/n/Y");
	if (substr($ld_fecha,1,1)=="/")
	{
		$ld_fecha="0".$ld_fecha;
	}
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Listado de Personal REC en Excel</b>"); // Seguridad de Reporte
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
		$lo_hoja->set_column(0,0,12);
		$lo_hoja->set_column(1,1,8);
		$lo_hoja->set_column(2,2,8);
		$lo_hoja->set_column(3,3,12);
		$lo_hoja->set_column(4,4,17);
		$lo_hoja->set_column(5,5,17);
		$lo_hoja->set_column(6,6,8);
		$lo_hoja->set_column(7,7,10);
		$lo_hoja->set_column(8,9,30);
		$lo_hoja->set_column(10,10,15);
		$lo_hoja->set_column(11,11,50);
		$lo_hoja->set_column(12,12,20);
		$lo_hoja->set_column(13,13,20);
		$lo_hoja->set_column(14,14,13);
		$lo_hoja->set_column(15,15,18);
		$lo_hoja->set_column(16,16,10);
		$lo_hoja->set_column(17,17,8);
		$lo_hoja->set_column(18,18,12);
		$lo_hoja->set_column(19,19,15);
		$lo_hoja->set_column(20,20,12);
		$lo_hoja->set_column(21,21,15);
		$lo_hoja->set_column(22,22,15);
		$lo_hoja->set_column(23,23,15);
		$lo_hoja->set_column(24,24,20);
		$lo_hoja->set_column(25,25,20);
		$lo_hoja->set_column(26,26,15);
		$lo_hoja->set_column(27,27,50);
		$lo_hoja->set_column(28,28,20);
		$lo_hoja->set_column(29,29,20);
		$lo_hoja->set_column(30,30,20);
		$lo_hoja->set_column(31,31,60);
		$lo_hoja->set_column(32,32,15);
		$lo_hoja->set_column(33,33,20);
		$lo_hoja->set_column(34,34,20);
		$lo_hoja->set_column(35,35,20);
		$lo_hoja->set_column(36,36,15);
		$lo_hoja->set_column(37,37,20);
		$lo_hoja->set_column(38,38,20);
		$lo_hoja->set_column(39,39,15);
		$lo_hoja->set_column(40,40,15);
		$lo_hoja->set_column(41,41,15);
		$lo_hoja->set_column(42,42,15);
		$lo_hoja->set_column(43,43,15);
		$lo_hoja->set_column(44,44,15);
		$lo_hoja->set_column(45,45,15);
		$lo_hoja->set_column(46,46,15);
		$lo_hoja->set_column(47,47,15);
		$lo_hoja->set_column(48,48,15);
		$lo_hoja->set_column(49,49,15);
		$lo_hoja->set_column(50,50,15);
		$lo_hoja->set_column(51,51,15);
		$lo_hoja->set_column(52,52,15);
		$lo_hoja->set_column(53,53,15);
		$lo_hoja->set_column(54,54,15);
		$lo_hoja->set_column(55,55,15);
		$lo_hoja->set_column(56,56,15);
		$lo_hoja->set_column(57,57,15);
		$lo_hoja->set_column(58,58,15);
		$lo_hoja->set_column(59,59,15);
		$lo_hoja->set_column(60,60,15);
		$lo_hoja->set_column(61,61,15);
		$lo_hoja->set_column(62,62,15);
		$lo_hoja->set_column(63,63,15);
		$lo_hoja->set_column(64,64,15);
		$lo_hoja->set_column(65,65,15);
		$lo_hoja->set_column(66,66,15);
		$lo_hoja->set_column(67,67,15);
		$lo_hoja->set_column(68,68,15);
		$lo_hoja->set_column(69,69,15);
		$lo_hoja->set_column(70,70,15);
		$lo_hoja->set_column(71,71,15);
		$lo_hoja->set_column(72,72,15);
		$lo_hoja->set_column(73,73,15);
		$lo_hoja->set_column(74,74,15);
		$lo_hoja->set_column(75,75,15);
		$lo_hoja->set_column(76,76,15);
		$lo_hoja->set_column(77,77,15);
		$lo_hoja->set_column(78,78,15);
		$lo_hoja->set_column(79,79,15);
		$lo_hoja->set_column(80,80,15);
		$lo_hoja->set_column(81,81,15);
		$lo_hoja->set_column(82,82,15);
		$lo_hoja->set_column(83,83,15);
		$lo_hoja->set_column(84,84,15);
		$lo_hoja->set_column(85,85,15);
		
		$li_row=0;
		$li_totrow=$io_report->DS->getRowCount("codcarnomina");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			
			$ls_fecvigen=$ld_fecha;
			$ls_codorg='1305013';
			$ls_codgrup='01';
			$ls_codper=$io_report->DS->data["codper"][$li_i];
			$ls_codnom=$io_report->DS->data["codnom"][$li_i];
			$ls_codcarnomina=$io_report->DS->data["codcarnomina"][$li_i];
			$ls_minorguniadm=$io_report->DS->data["minorguniadm"][$li_i];
			$ls_ofiuniadm=$io_report->DS->data["ofiuniadm"][$li_i];
			$ls_uniuniadm=$io_report->DS->data["uniuniadm"][$li_i];
			$ls_depuniadm=$io_report->DS->data["depuniadm"][$li_i];
			$ls_prouniadm=$io_report->DS->data["prouniadm"][$li_i];
			$ls_codubiadm=$ls_ofiuniadm.$ls_uniuniadm.$ls_depuniadm.$ls_prouniadm.$ls_minorguniadm;
			$ls_codclase=$io_report->DS->data["codigocar"][$li_i];
			$ls_codgra=$io_report->DS->data["codgra"][$li_i];
			$ls_codgra=str_pad(trim($ls_codgra),2,"0",0);
			$ls_tipocargo=$io_report->DS->data["desded"][$li_i];
			$ls_desclase=$io_report->DS->data["denasicar"][$li_i];
			$ls_jortra='000.00';
			$ls_cedper=$io_report->DS->data["cedper"][$li_i];
			$ls_cedper=str_pad(trim($ls_cedper),8,"0",0);
			$ls_nomper=$io_report->DS->data["nomper"][$li_i];
			$ls_apeper=$io_report->DS->data["apeper"][$li_i];
			$ls_codubifis="010108";
			$ls_coddecsercar='00193';
			$ls_decescsue='04270';
			$ls_accadm='0000000';
			$ls_sitcar=4;
			$ls_presuel='004';
			$li_sueper=$io_report->DS->data["sueper"][$li_i];
			//$li_sueper=str_replace(".","",trim($li_sueper));
			//$li_sueper=str_replace(",",".",trim($li_sueper));
			$li_sueper=number_format(trim($li_sueper),2,".","");
			$li_sueper=str_pad(trim($li_sueper),12,"0",STR_PAD_LEFT);
			$ls_precomp='024';
			$lb_valido=$io_report->uf_buscar_conceptos_personal($ls_codper,$ls_codnom,$ls_anio,$ls_peri,$ld_comp,$ld_pripro,$ld_priant,$ld_prihij,$ld_bonvac,$ld_bonagu,$ld_sso,$ld_parfor,$ld_lph);
			//$ld_comp=str_replace(".","",$ld_comp);
			//$ld_comp=str_replace(",",".",$ld_comp);
			$ld_comp=number_format(trim($ld_comp),2,".","");
			$ld_comp=str_pad(trim($ld_comp),12,"0",STR_PAD_LEFT);
			$ls_relleno1='000000000.00';
			$ls_relleno2='000';
			$ls_espacios='                   ';
			$li_row=$li_row+1;

				$lo_hoja->write($li_row, 0, "01/07/2007", $lo_datacenter);//Tenia $ls_fecvigen
				$lo_hoja->write($li_row, 1, " ".$ls_codorg, $lo_datacenter);
				$lo_hoja->write($li_row, 2, " ".$ls_codgrup, $lo_datacenter);
				$lo_hoja->write($li_row, 3, " "."0000001", $lo_datacenter);//Tenia $ls_codcarnomina
				$lo_hoja->write($li_row, 4, " ".$ls_codubiadm, $lo_datacenter);
				$lo_hoja->write($li_row, 5, " ".$ls_codcarnomina, $lo_datacenter);//Tenia  $ls_codclase
				$lo_hoja->write($li_row, 6, " ".$ls_codgra, $lo_datacenter);
				$lo_hoja->write($li_row, 7, " "."1", $lo_datacenter);//Tenia  $ls_tipocargo
				$lo_hoja->write($li_row, 8, " ".$ls_desclase, $lo_datacenter);
				$lo_hoja->write($li_row, 9, " ".$ls_jortra, $lo_datacenter);
				$lo_hoja->write($li_row, 10, " ".$ls_cedper, $lo_dataright);
				$lo_hoja->write($li_row, 11, " ".$ls_nomper, $lo_datacenter);
				$lo_hoja->write($li_row, 12, " ".$ls_apeper, $lo_datacenter);
				$lo_hoja->write($li_row, 13, " ".$ls_codubifis, $lo_datacenter);
				$lo_hoja->write($li_row, 14, " ".$ls_coddecsercar, $lo_datacenter);
				$lo_hoja->write($li_row, 15, " ".$ls_decescsue, $lo_datacenter);
				$lo_hoja->write($li_row, 16, " ".$ls_accadm, $lo_datacenter);
				$lo_hoja->write($li_row, 17, " ".$ls_sitcar, $lo_datacenter);
				$lo_hoja->write($li_row, 18, " ".$ls_presuel, $lo_datacenter);
				$lo_hoja->write($li_row, 19, " ".$li_sueper, $lo_datacenter);
				$lo_hoja->write($li_row, 20, " ".$ls_precomp, $lo_datacenter);
				$lo_hoja->write($li_row, 21, " ".$ld_comp, $lo_datacenter);
				$lo_hoja->write($li_row, 22, " "."036", $lo_datacenter);//$ls_relleno1
				$lo_hoja->write($li_row, 23, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 24, " "."037", $lo_datacenter);
				$lo_hoja->write($li_row, 25, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 26, " "."030", $lo_datacenter);
				$lo_hoja->write($li_row, 27, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 28, " "."053", $lo_datacenter);
				$lo_hoja->write($li_row, 29, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 30, " "."004", $lo_datacenter);
				$lo_hoja->write($li_row, 31, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 32, " "."004", $lo_datacenter);
				$lo_hoja->write($li_row, 33, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 34, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 35, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 36, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 37, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 38, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 39, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 40, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 41, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 42, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 43, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 44, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 45, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 46, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 47, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 48, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 49, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 50, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 51, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 52, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 53, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 54, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 55, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 56, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 57, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 58, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 59, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 60, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 61, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 62, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 63, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 64, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 65, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 66, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 67, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 68, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 69, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 70, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 71, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 72, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 73, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 74, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 75, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 76, " ".$ls_relleno2, $lo_datacenter);
				$lo_hoja->write($li_row, 77, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 78, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 79, " ".$ls_relleno1, $lo_datacenter);
				$lo_hoja->write($li_row, 80, " "."00/00/0000", $lo_datacenter);
				$lo_hoja->write($li_row, 81, " "."0000000", $lo_datacenter);
				$lo_hoja->write($li_row, 82, " "."00", $lo_datacenter);
				$lo_hoja->write($li_row, 83, " "."0000000", $lo_datacenter);
				$lo_hoja->write($li_row, 84, " "."       ", $lo_datacenter);
				$lo_hoja->write($li_row, 85, " ", $lo_datacenter);
				$lo_hoja->write($li_row, 86, " ", $lo_datacenter);
				
		}
		$io_report->DS->resetds("codcarnomina");
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listado_personal_rec.xls\"");
		header("Content-Disposition: inline; filename=\"listado_personal_rec.xls\"");
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