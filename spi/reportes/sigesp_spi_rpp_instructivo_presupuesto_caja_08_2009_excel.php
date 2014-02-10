<?php 
	session_start();
	ini_set('memory_limit','256M');
	ini_set('max_execution_time ','0');
	
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina(&$li_row,&$lo_titulo,$lo_hoja,$as_titulo,$as_moneda,$as_trimestre)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//                 $as_moneda // Moneda
		//	    		   as_trimestre // Nro. del Trimestre
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$lo_hoja->write($li_row, 1, "Denominacion",$lo_titulo);
		$lo_hoja->write($li_row, 2, "Presupuesto",$lo_titulo);
		$lo_hoja->write($li_row+1, 2, "Aprobado",$lo_titulo);
		$lo_hoja->write($li_row, 3, "Presupuesto",$lo_titulo);
		$lo_hoja->write($li_row+1, 3, "Modificado",$lo_titulo);
		$lo_hoja->write($li_row-1, 4, "TRIMESTRE No: $as_trimestre ",$lo_titulo);
		$lo_hoja->write($li_row, 4, "Programado",$lo_titulo);
		$lo_hoja->write($li_row, 5, "Ejecutado",$lo_titulo);
		$lo_hoja->write($li_row-2, 6, "VARIACION EJECUTADO-",$lo_titulo);
		$lo_hoja->write($li_row-1, 6, "PROGRAMADO TRIMESTRE No. $as_trimestre",$lo_titulo);
		$lo_hoja->write($li_row, 6, "Absoluto",$lo_titulo);
		$lo_hoja->write($li_row, 7, "Porcentual",$lo_titulo);
		$lo_hoja->write($li_row-1, 8, "TOTAL ACUMULADO AL TRIMESTRE",$lo_titulo);
		$lo_hoja->write($li_row, 8, "Programado",$lo_titulo);
		$lo_hoja->write($li_row, 9, "Ejecutado",$lo_titulo);
		++$li_row;
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte(&$li_row,&$lo_titulo,$lo_hoja,$as_programatica,$ai_ano,$as_mes,$as_denestpro,$ls_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads=$_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona   = $_SESSION['la_empresa']['codasiona'];
		$lo_hoja->write($li_row, 3, "$ls_titulo ",$lo_titulo);
		$li_row++;
		$li_row++;
		$lo_hoja->write($li_row, 1, "CODIGO PRESUPUESTARIO DEL ENTE: $ls_codasiona ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 1, "DENOMINACION DEL ENTE:  $ls_nombre ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 1, "ORGANO DE ADSCRIPCION:  ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 1, "PERIODO PRESUPUESTARIO: $ai_ano  ",$lo_titulo);
		$li_row++;

	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo(&$li_row,&$lo_titulo,$lo_hoja,$io_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_titulo
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->saveState();
		$io_pdf->ezSetDy(-100); // para  el rectangulo 
		$la_data=array(array('cuenta'=>'',
							 'denominacion'=>'',
							 'asignado'=>'',
							 'modificado'=>'',
							 'programado'=>'',
							 'ejecutado'=>'',
							 'absoluto'=>'',
							 'porcentual'=>'',
							 'programado_acum'=>'',
							 'ejecutado_acum'=>''));
							 
		$la_columna=array(   'cuenta'=>'',
							 'denominacion'=>'',
							 'asignado'=>'',
							 'modificado'=>'',
							 'programado'=>'',
							 'ejecutado'=>'',
							 'absoluto'=>'',
							 'porcentual'=>'',
							 'programado_acum'=>'',
							 'ejecutado_acum'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 7,       // Tamaño de Letras
						 'titleFontSize' => 7, // Tamaño de Letras de los títulos
						 'showLines'=>1,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'xPos'=>504,
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990,
						 'colGap'=>0,
						 'cols'=>array('cuenta'=>         array('justification'=>'center','width'=>60),
							           'denominacion'=>    array('justification'=>'center','width'=>210),
							           'asignado'=>        array('justification'=>'center','width'=>90),
							           'modificado'=>      array('justification'=>'center','width'=>90),
							           'programado'=>      array('justification'=>'center','width'=>90),
							           'ejecutado'=>      array('justification'=>'center','width'=>90),
							           'absoluto'=>         array('justification'=>'center','width'=>90),
							           'porcentual'=>          array('justification'=>'center','width'=>90),
							           'programado_acum'=> array('justification'=>'center','width'=>90),
							           'ejecutado_acum'=> array('justification'=>'center','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_titulo,'all');

	}// end function uf_print_titulo
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($io_cabecera,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->saveState();
		$la_data=array(array('cuenta'=>'<b>Cuenta</b>','denominacion'=>'<b>Denominación</b>','pres_anual'=>'<b>Presupuesto Anual</b>',
		                     'programado'=>'<b>Trimestre</b>','programado_acum'=>'<b>Acumulado</b>','compromiso'=>'<b>Compromiso</b>','causado'=>'<b>Causado</b>',
							 'pagado'=>'<b>Pagado</b>','porc_comprometer'=>'<b> Compromiso</b>','porc_causado'=>'<b>Causado</b>',
							 'porc_pagado'=>'<b>Pagado</b>','disp_trim_ant'=>'<b>Trimestre Anterior</b>',
							 'disp_fecha'=>'<b>A la Fecha</b>'));
		$la_columna=array('cuenta'=>'','denominacion'=>'','pres_anual'=>'','programado'=>'','programado_acum'=>'','compromiso'=>'','causado'=>'',
		                  'pagado'=>'','porc_comprometer'=>'','porc_causado'=>'','porc_pagado'=>'','disp_trim_ant'=>'','disp_fecha'=>'');
		$la_config=array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 7,       // Tamaño de Letras
						 'titleFontSize' => 7, // Tamaño de Letras de los títulos
						 'showLines'=>2,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990,
						 'colGap'=>0,
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'center','width'=>130), // Justificación y ancho de la columna
						 			   'pres_anual'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'programado'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'programado_acum'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'compromiso'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'causado'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'pagado'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'porc_comprometer'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'porc_causado'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'porc_pagado'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'disp_trim_ant'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
									   'disp_fecha'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_cabecera,'all');

	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$lo_dataleftb,$lo_datarightb,$la_data)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_row++;
		$ls_html = array('<b>','</b>');
		for( $i = 1; $i < count($la_data); $i ++)
		{
			$formato 	= (strstr($la_data[$i]['denominacion'],'<b>')) ? $lo_datarightb : $lo_dataright;
			$formatotx  = (strstr($la_data[$i]['denominacion'],'<b>')) ? $lo_dataleftb : $lo_dataleft;

			$lo_hoja->write($li_row, 0, $la_data[$i]['cuenta'],$lo_datacenter);
			$lo_hoja->write($li_row, 1, str_replace($ls_html,'',$la_data[$i]['denominacion']),$formatotx);
			$lo_hoja->write($li_row, 2, $la_data[$i]['asignado'],$formato);
			$lo_hoja->write($li_row, 3, $la_data[$i]['modificado'],$formato);
			$lo_hoja->write($li_row, 4, $la_data[$i]['programado'],$formato);
			$lo_hoja->write($li_row, 5, $la_data[$i]['ejecutado'],$formato);
			$lo_hoja->write($li_row, 6, $la_data[$i]['absoluto'],$formato);
			$lo_hoja->write($li_row, 7, $la_data[$i]['porcentual'],$formato);
			$lo_hoja->write($li_row, 8, $la_data[$i]['programado_acum'],$formato);
			$lo_hoja->write($li_row, 9, $la_data[$i]['ejecutado_acum'],$formato);

			$li_row++;
		}		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Arnaldo USárez
		// Fecha Creación: 10/06/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


			$lo_hoja->write($li_row, 0, $la_data_tot[1]['cuenta'],$lo_datacenter);
			$lo_hoja->write($li_row, 1, $la_data_tot[1]['denominacion'],$lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_tot[1]['asignado'],$lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_tot[1]['modificado'],$lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_tot[1]['programado'],$lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_tot[1]['ejecutado'],$lo_dataright);
			$lo_hoja->write($li_row, 6, $la_data_tot[1]['absoluto'],$lo_dataright);
			$lo_hoja->write($li_row, 7, $la_data_tot[1]['porcentual'],$lo_dataright);
			$lo_hoja->write($li_row, 8, $la_data_tot[1]['programado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 9, $la_data_tot[1]['ejecutado_acum'],$lo_dataright);

			$li_row++;
		
		
	}// end function uf_print_pie_cabecera
	
	
	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");	
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();	
	require_once("sigesp_spi_funciones_reportes.php");
	$io_function_report=new sigesp_spi_funciones_reportes();	
	require_once("../../shared/class_folder/class_fecha.php");
	$io_fecha = new class_fecha();
	//-----------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------
	//para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "spi_instructivo_presupuesto_caja_inst_08_2009.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	$li_row = 1;
	//---------------------------------------------------------------------------------------------------------------------------

	global $la_data_tot;
	require_once("sigesp_spi_class_reportes_instructivos.php");
	$io_report = new sigesp_spi_class_reportes_instructivos();
	
	$li_candeccon=$_SESSION["la_empresa"]["candeccon"];
	$li_tipconmon=$_SESSION["la_empresa"]["tipconmon"];
	$li_redconmon=$_SESSION["la_empresa"]["redconmon"];
		 
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	$li_ano=substr($ldt_periodo,0,4);
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	$ls_cmbtri=$_GET["trimestre"];
	
	switch($ls_cmbtri)
	{
		case '0103': $ls_trimestre = "01";
		break;
		
		case '0406': $ls_trimestre = "02";
		break;
		
		case '0709': $ls_trimestre = "03";
		break;
		
		case '1012': $ls_trimestre = "04";
		break;
	
	}
	$li_mesdes=substr($ls_cmbtri,0,2);
	$ldt_fecdes=$li_ano."-".$li_mesdes."-01";
	$li_meshas=substr($ls_cmbtri,2,2);
	$ldt_ult_dia=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$fechas=$ldt_ult_dia;
	$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
	$ls_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes);
	$ls_meshas=$io_fecha->uf_load_nombre_mes($li_meshas);
	$ls_formpre=$_SESSION["la_empresa"]["formpre"];
	$ls_formpre=str_replace('-','',$ls_formpre);
	$li_len=strlen($ls_formpre);
	$li_len=$li_len-9;
	$ls_ceros=$io_funciones->uf_cerosderecha("",$li_len);
		
		
//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
		$ls_titulo="PRESUPUESTO DE CAJA";       
//--------------------------------------------------------------------------------------------------------------------------------
   
      $lb_valido=$io_report->uf_spg_reportes_presupuesto_de_caja_inst_08($ldt_fecdes,$ldt_fechas,"",$ls_mesdes,$ls_meshas);
	 //$lb_valido=true;
	 if($lb_valido==false) // Existe algún error ó no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte	 
	 {
	 	//--------------------------------------------------------------------------------------------------
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('left');
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
		//---> formatos bold
		$lo_dataleftb= &$lo_libro->addformat();
		$lo_dataleftb->set_text_wrap();
		$lo_dataleftb->set_font("Verdana");
		$lo_dataleftb->set_align('left');
		$lo_dataleftb->set_size('9');
		$lo_dataleftb->set_bold();
		$lo_datarightb= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_datarightb->set_font("Verdana");
		$lo_datarightb->set_align('right');
		$lo_datarightb->set_size('9');
		$lo_datarightb->set_bold();		
		
		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,70);
		$lo_hoja->set_column(2,2,25);
		$lo_hoja->set_column(3,3,25);
		$lo_hoja->set_column(4,4,25);
		$lo_hoja->set_column(5,5,25);
		$lo_hoja->set_column(6,6,25);
		$lo_hoja->set_column(7,7,25);
		$lo_hoja->set_column(8,8,25);
		$lo_hoja->set_column(9,9,25);
		$lo_hoja->set_column(10,10,25);
		$lo_hoja->set_column(11,11,25);
		$lo_hoja->set_column(12,12,25);

		$li_row = 2;
		//--------------------------------------------------------------------------------------------------		
		$li_row++;
	 	uf_print_titulo_reporte(&$li_row,&$lo_titulo,$lo_hoja,"",$li_ano,$ls_mesdes,"",$ls_titulo);
		
		$li_row = 9;
		uf_print_encabezado_pagina(&$li_row,&$lo_titulo,$lo_hoja,$ls_titulo,'(En Bolivares)',$ls_trimestre); // Imprimimos el encabezado de la página
		
		$li_tot=$io_report->dts_reporte->getRowCount("cuenta");
		
	    $ld_total_asignado=0;
		$ld_total_modificado=0;
		$ld_total_programado=0;
		$ld_total_ejecutado=0;
		$ld_total_absoluto=0;
		$ld_total_porcentual=0;
		$ld_total_programado_acum=0;
		$ld_total_ejecutado_acum=0;
	
		$thisPageNum=$io_pdf->ezPageCount;
		for($z=1;$z<=$li_tot;$z++)
		{		
			$ls_cuenta = "";
			$ls_denominacion = "";
			$ld_asignado=0;
			$ld_modificado=0;
			$ld_programado=0;
			$ld_ejecutado=0;
			$ld_programado_acum=0;
			$ld_ejecutado_acum=0;
			$ld_absoluto=0;
			$ld_porcentual=0;
				  
				$ls_cuenta          =  $io_report->dts_reporte->data["cuenta"][$z];
				$ls_denominacion    =  $io_report->dts_reporte->data["denominacion"][$z];
				$ld_asignado        =  $io_report->dts_reporte->data["asignado"][$z];
				$ld_modificado      =  $io_report->dts_reporte->data["modificado"][$z];
				$ld_programado      =  $io_report->dts_reporte->data["programado"][$z];
				$ld_ejecutado       =  $io_report->dts_reporte->data["ejecutado"][$z];
				$ld_absoluto        =  $io_report->dts_reporte->data["absoluto"][$z];
				$ld_porcentual      =  $io_report->dts_reporte->data["porcentual"][$z];
				$ld_programado_acum =  $io_report->dts_reporte->data["programado_acumulado"][$z];
				$ld_ejecutado_acum  =  $io_report->dts_reporte->data["ejecutado_acumulado"][$z];
		
				  if(($z == 1)||($z == 2))
				  {
				   $ld_total_asignado         = $ld_total_asignado + $ld_asignado;
				   $ld_total_modificado       = $ld_total_modificado + $ld_modificado;
		           $ld_total_programado       = $ld_total_programado + $ld_programado;
		           $ld_total_ejecutado        = $ld_total_ejecutado + $ld_ejecutado;
		           $ld_total_programado_acum  = $ld_total_programado_acum + $ld_programado_acum;
		           $ld_total_ejecutado_acum  =  $ld_total_ejecutado_acum + $ld_ejecutado_acum;
				  }
				  
				  if($ls_cuenta == "400000000".$ls_ceros)
				  {
				   $ld_total_asignado         = $ld_total_asignado -$ld_asignado;
				   $ld_total_modificado       = $ld_total_modificado - $ld_modificado;
		           $ld_total_programado       = $ld_total_programado - $ld_programado;
		           $ld_total_ejecutado        = $ld_total_ejecutado - $ld_ejecutado;
		           $ld_total_programado_acum  = $ld_total_programado_acum - $ld_programado_acum;
		           $ld_total_ejecutado_acum  =  $ld_total_ejecutado_acum - $ld_ejecutado_acum;
				  } 
				  
				  $ld_asignado               = number_format($ld_asignado,2,",",".");
				  $ld_modificado             = number_format($ld_modificado,2,",",".");
				  $ld_programado             = number_format($ld_programado,2,",",".");
				  $ld_ejecutado              = number_format($ld_ejecutado,2,",",".");
				  $ld_absoluto             	 = number_format($ld_absoluto,2,",",".");
				  $ld_porcentual             = number_format($ld_porcentual,2,",",".");
				  $ld_programado_acum        = number_format($ld_programado_acum,2,",",".");
				  $ld_ejecutado_acum         = number_format($ld_ejecutado_acum,2,",",".");
				
				  $la_data[$z]=array('cuenta'=>$io_function_report->uf_formato_cuenta_instructivo(trim($ls_cuenta)),
				                     'denominacion'=>$ls_denominacion,
									 'asignado'=>$ld_asignado,
									 'modificado'=>$ld_modificado,
									 'programado'=>$ld_programado,
									 'ejecutado'=>$ld_ejecutado,
									 'absoluto'=>$ld_absoluto,
									 'porcentual'=>$ld_porcentual,
									 'programado_acum'=>$ld_programado_acum,
									 'ejecutado_acum'=>$ld_ejecutado_acum);
					  							 						   
			}//for
		    $ld_total_absoluto           = abs($ld_total_ejecutado - $ld_total_programado);
			if ($ld_total_programado > 0)
			{
			 $ld_total_porcentual = ($ld_total_ejecutado/$ld_total_programado)*100;
			}
			else
			{
			 $ld_total_porcentual = 0;
			}
			
			$ld_total_asignado           = number_format($ld_total_asignado,2,",",".");
			$ld_total_modificado         = number_format($ld_total_modificado,2,",",".");
		    $ld_total_programado         = number_format($ld_total_programado,2,",",".");
		    $ld_total_ejecutado          = number_format($ld_total_ejecutado,2,",",".");
		    $ld_total_absoluto           = number_format($ld_total_absoluto,2,",",".");
		    $ld_total_porcentual         = number_format($ld_total_porcentual,2,",",".");
		    $ld_total_programado_acum    = number_format($ld_total_programado_acum,2,",",".");
		    $ld_total_ejecutado_acum     = number_format($ld_total_ejecutado_acum,2,",",".");
		  
			
			$la_data_tot[1]=array('cuenta'=>"",
			                      'denominacion'=>" SALDO FINAL",
								  'asignado'=>$ld_total_asignado,
								  'modificado'=>$ld_total_modificado,
								  'programado'=>$ld_total_programado,
								  'ejecutado'=>$ld_total_ejecutado,
								  'absoluto'=>$ld_total_absoluto,
								  'porcentual'=>$ld_total_porcentual,
								  'programado_acum'=>$ld_total_programado_acum,
								  'ejecutado_acum'=>$ld_total_ejecutado_acum);

            
			




			
			uf_print_detalle(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$lo_dataleftb,$lo_datarightb,$la_data); // Imprimimos el detalle 
		    uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot);

			unset($la_data);
			unset($la_data_tot);



		if (isset($d) && $d)
		{
//			$ls_pdfcode = $io_pdf->ezOutput(1);
//			$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
//			echo '<html><body>';
//			echo trim($ls_pdfcode);
//			echo '</body></html>';
		}
//		else
//			
//		{
//			$lo_libro->close();
//			header("Content-Type: application/x-msexcel; name=\"spi_instructivo_presupuesto_caja_inst_07.xls\"");
//			header("Content-Disposition: inline; filename=\"spi_instructivo_presupuesto_caja_inst_07.xls\"");
//			$fh=fopen($lo_archivo, "rb");
//			fpassthru($fh);
//			unlink($lo_archivo);
//		}
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"spi_instructivo_presupuesto_caja_inst_08_2009.xls\"");
			header("Content-Disposition: inline; filename=\"spi_instructivo_presupuesto_caja_inst_08_2009.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
		//unset($io_pdf);
	}//else
	unset($io_report);
	unset($io_funciones);
?> 