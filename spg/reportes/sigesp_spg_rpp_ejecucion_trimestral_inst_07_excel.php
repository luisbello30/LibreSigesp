<?php
    session_start();   
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}	
	  // para crear el libro excel
		require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo =  tempnam("/tmp", "EJECUCION TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS.xls");
		$lo_libro = &new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_moneda,$as_trimestre,&$io_pdf)
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
		// Fecha Creación: 26/06/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(24,380,24,450);
		$io_pdf->line(39,380,39,450);
		$io_pdf->line(54,380,54,450);
		$io_pdf->line(69,380,69,460);
		$io_pdf->line(10,450,70,450);
		$io_pdf->line(199,380,199,460);
		$io_pdf->line(279,380,279,460);
		$io_pdf->line(349,380,349,460);
		$io_pdf->line(419,380,419,450);
		$io_pdf->line(489,380,489,430);
		$io_pdf->line(559,380,559,430);
		$io_pdf->line(629,380,629,450);
		$io_pdf->line(699,380,699,430);
		$io_pdf->line(769,380,769,430);
		$io_pdf->line(839,380,839,430);
		$io_pdf->line(919,380,919,460);
		$io_pdf->line(349,450,919,450);
		$io_pdf->line(419,430,919,430);
		$io_pdf->addText(22,384,7,"PARTIDA",270);
		$io_pdf->addText(37,384,7,"GENÉRICA",270);
		$io_pdf->addText(52,384,7,"ESPECIFICA",270);
		$io_pdf->addText(67,384,7,"SUB-ESPECÍFICA",270);
		$io_pdf->addText(105,400,7,"DENOMINACION");
		$io_pdf->addText(210,400,7,"PRESUPUESTO");
		$io_pdf->addText(215,390,7,"APROBADO");
		$io_pdf->addText(285,400,7,"PRESUPUESTO");
		$io_pdf->addText(290,390,7,"MODIFICADO");
		$io_pdf->addText(352,410,7,"PROGRAMADO EN ");
		$io_pdf->addText(358,400,7,"EL TRIMESTRE");
		$io_pdf->addText(373,390,7,"No. ".$as_trimestre);
		$io_pdf->addText(457,435,7,"EJECUTADO EN EL TRIMESTRE No. ".$as_trimestre);
		$io_pdf->addText(430,390,7,"COMPROMISO");
		$io_pdf->addText(505,390,7,"CAUSADO");
		$io_pdf->addText(580,390,7,"PAGADO");
		$io_pdf->addText(710,435,7,"ACUMULADO AL TRIMESTRE No. ".$as_trimestre);
		$io_pdf->addText(640,390,7,"PROGRAMADO");
		$io_pdf->addText(710,390,7,"COMPROMISO");
		$io_pdf->addText(785,390,7,"CAUSADO");
		$io_pdf->addText(865,390,7,"PAGADO");
		$io_pdf->addText(935,400,7,"DISPONIBILIDAD");
		$io_pdf->addText(930,390,7,"PRESUPUESTARIA");
		$io_pdf->rectangle(10,460,990,140);
		$io_pdf->rectangle(10,382,990,78);
		
		$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,470,16,$as_titulo); // Agregar el título
		
		/*$li_tm=$io_pdf->getTextWidth(10,'<b>'.$as_moneda.'</b>');
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,480,10,'<b>'.$as_moneda.'</b>'); */// Agregar el título
		
		//$io_pdf->addText(900,550,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte($io_encabezado,$as_programatica,$ai_ano,$as_mes,$as_denestpro,$as_tirmestres,
	                                 $as_nombre,&$io_pdf)
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
		$io_pdf->saveState();
		$io_pdf->ezSetY(600);
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads=$_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona   = $_SESSION['la_empresa']['codasiona'];
		$la_data[1]=array('desde'=>'<b>CODIGO PRESUPUESTARIO DEL ENTE:     </b>'.'<b>'.$ls_codasiona.'</b>','hasta'=>'');
		$la_data[2]=array('desde'=>'<b>DENOMINACION DEL ENTE:    </b>'.'<b>'.$ls_nombre.'</b>','hasta'=>'');
		$la_data[3]=array('desde'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro1"]).' Desde:    </b>'.$as_nombre["D"]["1"],'hasta'=>'<b> - Hasta:</b> '.$as_nombre["H"]["1"]);
		$la_data[4]=array('desde'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro2"]).' Desde:    </b>'.$as_nombre["D"]["2"],'hasta'=>'<b> - Hasta:</b> '.$as_nombre["H"]["2"]);
		$la_data[5]=array('desde'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro3"]).' Desde:    </b>'.$as_nombre["D"]["3"],'hasta'=>'<b> - Hasta:</b> '.$as_nombre["H"]["3"]);
		$la_data[6]=array('desde'=>'<b>PERIODO PRESUPUESTARIO:    </b>'.'<b>'.$as_tirmestres.' TRIMESTRES '.$ai_ano.'</b>','hasta'=>'');
		if($_SESSION["la_empresa"]["estmodest"]==2)
		{
		$la_data[6]=array('desde'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro4"]).' Desde:    </b>'.$as_nombre["D"]["4"],'hasta'=>'<b> - Hasta:</b> '.$as_nombre["H"]["4"]);
		$la_data[7]=array('desde'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro5"]).' Desde:    </b>'.$as_nombre["D"]["5"],'hasta'=>'<b> - Hasta:</b> '.$as_nombre["H"]["5"]);
		$la_data[8]=array('desde'=>'<b>PERIODO PRESUPUESTARIO:    </b>'.'<b>'.$as_tirmestres.' TRIMESTRES '.$ai_ano.'</b>','hasta'=>'');
		
		}		
		$la_columna=array('desde'=>'',
						   'hasta'=>'');
		
/*		$la_data=array(array('name'=>'<b>CODIGO PRESUPUESTARIO DEL ENTE:     </b>'.'<b>'.$ls_codasiona.'</b>'),
		               array('name'=>'<b>DENOMINACION DEL ENTE:    </b>'.'<b>'.$ls_nombre.'</b>'),
					   array('name'=>'<b>ORGANO DE ADSCRIPCION:    </b>'.'<b>'.$ls_nombre.'</b>'),
					   array('name'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro1"]).' Desde:    </b>'.$as_nombre["D"]["1"].'<b> - Hasta:</b> '.$as_nombre["H"]["1"]),
					   array('name'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro2"]).' Desde:    </b>'.$as_nombre["D"]["2"].'<b> - Hasta:</b> '.$as_nombre["H"]["2"]),
					   array('name'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro3"]).' Desde:    </b>'.$as_nombre["D"]["3"].'<b> - Hasta:</b> '.$as_nombre["H"]["3"]),
		               array('name'=>'<b>PERIODO PRESUPUESTARIO:    </b>'.'<b>'.$as_tirmestres.' TRIMESTRES '.$ai_ano.'</b>'),
		               array('name'=>''.'<b>'.trim($as_denestpro).'</b>'));
		$la_columna=array('name'=>'','name'=>'','name'=>'','name'=>'','name'=>'','name'=>'','name'=>'','name'=>'');*/
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 8,       // Tamaño de Letras
						 'titleFontSize' => 8, // Tamaño de Letras de los títulos
						 'showLines'=>0,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'xPos'=>520,//65
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900,
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('desde'=>array('justification'=>'left','width'=>520), // Justificación y ancho de la columna
						 			   'hasta'=>array('justification'=>'left','width'=>470))); // Justificación y ancho de la columna
						 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo($io_titulo,&$io_pdf)
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
		$la_data=array(array('partida'=>'',
		                     'generica'=>'',
		                     'especifica'=>'',  
		                     'subespecifica'=>'',
							 'denominacion'=>'',
							 'asignado'=>'',
							 'modificado'=>'',
							 'programado'=>'',
							 'compromiso'=>'',
							 'causado'=>'',
							 'pagado'=>'',
							 'programado_acum'=>'',
							 'compromiso_acum'=>'',
							 'causado_acum'=>'',
							 'pagado_acum'=>'',
							 'disp_fecha'=>''));
							 
		$la_columna=array(   'partida'=>'',
		                     'generica'=>'',
		                     'especifica'=>'',  
		                     'subespecifica'=>'',
							 'denominacion'=>'',
							 'asignado'=>'',
							 'modificado'=>'',
							 'programado'=>'',
							 'compromiso'=>'',
							 'causado'=>'',
							 'pagado'=>'',
							 'programado_acum'=>'',
							 'compromiso_acum'=>'',
							 'causado_acum'=>'',
							 'pagado_acum'=>'',
							 'disp_fecha'=>'');
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
						 'cols'=>array('partida'=>         array('justification'=>'center','width'=>15),
		                     		   'generica'=>        array('justification'=>'center','width'=>15),
		                               'especifica'=>      array('justification'=>'center','width'=>15),  
		                               'subespecifica'=>   array('justification'=>'center','width'=>15),
							           'denominacion'=>    array('justification'=>'center','width'=>130),
							           'asignado'=>        array('justification'=>'center','width'=>80),
							           'modificado'=>      array('justification'=>'center','width'=>70),
							           'programado'=>      array('justification'=>'center','width'=>70),
							           'compromiso'=>      array('justification'=>'center','width'=>70),
							           'causado'=>         array('justification'=>'center','width'=>70),
							           'pagado'=>          array('justification'=>'center','width'=>70),
							           'programado_acum'=> array('justification'=>'center','width'=>70),
							           'compromiso_acum'=> array('justification'=>'center','width'=>70),
							           'causado_acum'=>    array('justification'=>'center','width'=>70),
							           'pagado_acum'=>     array('justification'=>'center','width'=>80),
							           'disp_fecha'=>      array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
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
	function uf_print_detalle($ls_partida,$ls_generica,$ls_especifica,$ls_subesp,$ls_denominacion,$ld_asignado,
	                          $ld_modificado,$ld_programado,$ld_compromiso,$ld_causado,$ld_pagado,
							  $ld_programado_acum, $ld_compromiso_acum,$ld_causado_acum, $ld_pagado_acum,$ld_disp_fecha,&$io_pdf)
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
		$la_data[1]=array('partida'=>$ls_partida,
				                     'generica'=>$ls_generica,
				                     'especifica'=>$ls_especifica,
				                     'subesp'=>$ls_subesp,
				                     'denominacion'=>$ls_denominacion,
									 'asignado'=>$ld_asignado,
									 'modificado'=>$ld_modificado,
									 'programado'=>$ld_programado,
									 'compromiso'=>$ld_compromiso,
									 'causado'=>$ld_causado,
									 'pagado'=>$ld_pagado,
									 'programado_acum'=>$ld_programado_acum,
									 'compromiso_acum'=>$ld_compromiso_acum,
									 'causado_acum'=>$ld_causado_acum,
									 'pagado_acum'=>$ld_pagado_acum,
									 'disp_fecha'=>$ld_disp_fecha);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('partida'=>array('justification'=>'center','width'=>15),
						 
						               'generica'=>array('justification'=>'center','width'=>15),
									   
									   'especifica'=>array('justification'=>'center','width'=>15),
									   
									   'subesp'=>array('justification'=>'center','width'=>15), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>130), // Justificación y ancho de la columna
						 			   'asignado'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'modificado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
						 			   'programado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'compromiso'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'causado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'pagado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'programado_acum'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'compromiso_acum'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									  'causado_acum'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'pagado_acum'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
									   'disp_fecha'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
		$la_columnas=array('partida'=>'',
						   'generica'=>'',
		                   'especifica'=>'',
		                   'subesp'=>'',
				           'denominacion'=>'',
						   'asignado'=>'',
						   'modificado'=>'',
						   'programado'=>'',
						   'compromiso'=>'',
						   'causado'=>'',
						   'pagado'=>'',
						   'programado_acum'=>'',
						   'compromiso_acum'=>'',
						   'causado_acum'=>'',
						   'pagado_acum'=>'',
						   'disp_fecha'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($la_data_tot,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Arnaldo USárez
		// Fecha Creación: 10/06/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-5);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('totales'=>array('justification'=>'center','width'=>190), // Justificación y ancho de la columna
						 			   'asignado'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'modificado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
						 			   'programado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'compromiso'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'causado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'pagado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'programado_acum'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'compromiso_acum'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									  'causado_acum'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'pagado_acum'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
									   'disp_fecha'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
		$la_columnas=array('totales'=>'',
						   'asignado'=>'',
						   'modificado'=>'',
						   'programado'=>'',
						   'compromiso'=>'',
						   'causado'=>'',
						   'pagado'=>'',
						   'programado_acum'=>'',
						   'compromiso_acum'=>'',
						   'causado_acum'=>'',
						   'pagado_acum'=>'',
						   'disp_fecha'=>'');
		$io_pdf->ezTable($la_data_tot,$la_columnas,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_total_partidas($as_partida_aux, $ad_asignado_partida,$ad_modificado_partida,
	                                 $ad_programado_partida, $ad_compromiso_partida,
									 $ad_causado_partida,$ad_pagado_partida,
									 $ad_programado_acum_partida,$ad_compromiso_acum_partida,
									 $ad_causado_acum_partida,$ad_pagado_acum_partida,$ad_disp_fecha_partida,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/11/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$la_data_tot[1]=array('totales'=>"TOTALES PARTIDA ".$as_partida_aux,
							  'asignado'=>$ad_asignado_partida,
							  'modificado'=>$ad_modificado_partida,
							  'programado'=>$ad_programado_partida,
							  'compromiso'=>$ad_compromiso_partida,
							  'causado'=>$ad_causado_partida,
							  'pagado'=>$ad_pagado_partida,
							  'programado_acum'=>$ad_programado_acum_partida,
							  'compromiso_acum'=>$ad_compromiso_acum_partida,
							  'causado_acum'=>$ad_causado_acum_partida,
							  'pagado_acum'=>$ad_pagado_acum_partida,
							  'disp_fecha'=>$ad_disp_fecha_partida);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('totales'=>array('justification'=>'center','width'=>190), // Justificación y ancho de la columna
						 			   'asignado'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'modificado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
						 			   'programado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'compromiso'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'causado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'pagado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'programado_acum'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'compromiso_acum'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									  'causado_acum'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'pagado_acum'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
									   'disp_fecha'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
		$la_columnas=array('totales'=>'',
						   'asignado'=>'',
						   'modificado'=>'',
						   'programado'=>'',
						   'compromiso'=>'',
						   'causado'=>'',
						   'pagado'=>'',
						   'programado_acum'=>'',
						   'compromiso_acum'=>'',
						   'causado_acum'=>'',
						   'pagado_acum'=>'',
						   'disp_fecha'=>'');
		$io_pdf->ezTable($la_data_tot,$la_columnas,'',$la_config);
		unset($la_data_tot);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_pie_cabecera

///------------------------------------------------------------------------------------------------------------------------------------
		require_once("../../shared/ezpdf/class.ezpdf.php");
		
		require_once("../../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();	
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report=new sigesp_spg_funciones_reportes();	
		require_once("../../shared/class_folder/class_fecha.php");
		$io_fecha = new class_fecha();
//-----------------------------------------------------------------------------------------------------------------------------
		global $la_data_tot;
		require_once("sigesp_spg_class_reportes_instructivos.php");
		$io_report = new sigesp_spg_class_reportes_instructivos();
		
		/* $li_candeccon=$_SESSION["la_empresa"]["candeccon"];
		 $li_tipconmon=$_SESSION["la_empresa"]["tipconmon"];
		 $li_redconmon=$_SESSION["la_empresa"]["redconmon"]; */
		 
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$ls_codestpro1_min  = $_GET["codestpro1"];
		$ls_codestpro1_aux=$ls_codestpro1_min;		
		$ls_codestpro2_min  = $_GET["codestpro2"];
		$ls_codestpro3_min  = $_GET["codestpro3"];
		$ls_codestpro4_min  = $_GET["codestpro4"];
		$ls_codestpro5_min  = $_GET["codestpro5"];
		$ls_codestpro1h_max = $_GET["codestpro1h"];
		$ls_codestpro2h_max = $_GET["codestpro2h"];
		$ls_codestpro3h_max = $_GET["codestpro3h"];
		$ls_codestpro4h_max = $_GET["codestpro4h"];
		$ls_codestpro5h_max = $_GET["codestpro5h"];
		$ls_estclades       = $_GET["estclades"];
	    $ls_estclahas       = $_GET["estclahas"];
		$ls_tipoformato=1;
		if($li_estmodest==1)
		{
			$ls_codestpro4_min = "0000000000000000000000000";
			$ls_codestpro5_min = "0000000000000000000000000";
			$ls_codestpro4h_max = "0000000000000000000000000";
			$ls_codestpro5h_max = "0000000000000000000000000";
			if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min==""))
			{
			  if($io_function_report->uf_spg_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,
			                                                                 $ls_codestpro3_min,$ls_codestpro4_min,
			                                                                 $ls_codestpro5_min,$ls_estclades))
			  {
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			  }
			}
			else
			{
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			}
			if(($ls_codestpro1h_max=="")&&($ls_codestpro2h_max=="")&&($ls_codestpro3h_max==""))
			{
			  if($io_function_report->uf_spg_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
																			 $ls_codestpro3h_max,$ls_codestpro4h_max,
																			 $ls_codestpro4h_max,$ls_estclahas))
			  {
					$ls_codestpro1h  = $ls_codestpro1h_max;
					$ls_codestpro2h  = $ls_codestpro2h_max;
					$ls_codestpro3h  = $ls_codestpro3h_max;
					$ls_codestpro4h  = $ls_codestpro4h_max;
					$ls_codestpro5h  = $ls_codestpro5h_max;
			  }
			}
			else
			{
					$ls_codestpro1h  = $ls_codestpro1h_max;
					$ls_codestpro2h  = $ls_codestpro2h_max;
					$ls_codestpro3h  = $ls_codestpro3h_max;
					$ls_codestpro4h  = $ls_codestpro4h_max;
					$ls_codestpro5h  = $ls_codestpro5h_max;
			}
		}
		elseif($li_estmodest==2)
		{   
		    $ls_codestpro4_min = $_GET["codestpro4"];
			$ls_codestpro5_min = $_GET["codestpro5"];
			$ls_codestpro4h_max = $_GET["codestpro4h"];
			$ls_codestpro5h_max = $_GET["codestpro5h"];
			
			
			if(($ls_codestpro1_min=='**') ||($ls_codestpro1_min==''))
			{
				$ls_codestpro1_min='';
			}
			else
			{
			    $ls_codestpro1_min  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
			}
			if(($ls_codestpro2_min=='**') ||($ls_codestpro2_min==''))
			{
				$ls_codestpro2_min='';
			}
			else
			{
				$ls_codestpro2_min  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
			
			}
			if(($ls_codestpro3_min=='**')||($ls_codestpro3_min==''))
			{
				$ls_codestpro3_min='';
			}
			else
			{
			
				$ls_codestpro3_min  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
			}
			if(($ls_codestpro4_min=='**') ||($ls_codestpro4_min==''))
			{
				$ls_codestpro4_min='';
			}
			else
			{
				$ls_codestpro4_min  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
	
			
			}
			if(($ls_codestpro5_min=='**') ||($ls_codestpro5_min==''))
			{
				$ls_codestpro5_min='';
			}else
			{
					$ls_codestpro5_min  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
			}
			
			
			if(($ls_codestpro1h_max=='**')||($ls_codestpro1h_max==''))
			{
				$ls_codestpro1h_max='';
			}
			else
			{
				$ls_codestpro1h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
			}
			if(($ls_codestpro2h_max=='**') ||($ls_codestpro2h_max==''))
			{
				$ls_codestpro2h_max='';
			}else
			{
				$ls_codestpro2h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
			}
			if(($ls_codestpro3h_max=='**') ||($ls_codestpro3h_max==''))
			{
				$ls_codestpro3h_max='';
			}else
			{
				$ls_codestpro3h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
			}
			if(($ls_codestpro4h_max=='**')  ||($ls_codestpro4h_max==''))
			{
				$ls_codestpro4h_max='';
			}else
			{
				$ls_codestpro4h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
			}
			if(($ls_codestpro5h_max=='**')  || ($ls_codestpro5h_max==''))
			{
				$ls_codestpro5h_max='';
			}else
			{
				$ls_codestpro5h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
			}
			
			
			
			
			if(($ls_codestpro1_min=="")||($ls_codestpro2_min=="")||($ls_codestpro3_min=="")||($ls_codestpro4_min=="")||($ls_codestpro5_min==""))
			{
			  if($io_function_report->uf_spg_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,$ls_codestpro3_min,
			                                                                 $ls_codestpro4_min,$ls_codestpro5_min,$ls_estclades))
			  {
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			  }
			}
			else
			{
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			}
			if(($ls_codestpro1h_max=="")||($ls_codestpro2h_max=="")||($ls_codestpro3h_max=="")||($ls_codestpro4h_max=="")||($ls_codestpro5h_max==""))
			{
			  if($io_function_report->uf_spg_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
																			 $ls_codestpro3h_max,$ls_codestpro4h_max,
																			 $ls_codestpro5h_max,$ls_estclahas))
			  {
				$ls_codestpro1h  = $ls_codestpro1h_max;
				$ls_codestpro2h  = $ls_codestpro2h_max;
				$ls_codestpro3h  = $ls_codestpro3h_max;
				$ls_codestpro4h  = $ls_codestpro4h_max;
				$ls_codestpro5h  = $ls_codestpro5h_max;
			  }
			}
			else
			{
				$ls_codestpro1h  = $ls_codestpro1h_max;
				$ls_codestpro2h  = $ls_codestpro2h_max;
				$ls_codestpro3h  = $ls_codestpro3h_max;
				$ls_codestpro4h  = $ls_codestpro4h_max;
				$ls_codestpro5h  = $ls_codestpro5h_max;
			}
			}	
		$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
		$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
		$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
		$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
		$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
		$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
		$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
		$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
		$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
		$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
		
		$as_nombre["D"]["1"]="";
		$as_nombre["D"]["2"]="";
		$as_nombre["D"]["3"]="";
		$as_nombre["H"]["1"]="";
		$as_nombre["H"]["2"]="";
		$as_nombre["H"]["3"]="";
		$as_denestpro1="";
		$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,&$as_denestpro1,$ls_estclades);
		$as_nombre["D"]["1"]=$as_denestpro1;
		if($ls_codestpro1h!="")
		{
			$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1h,&$as_denestpro1,$ls_estclahas);
			$as_nombre["H"]["1"]=$as_denestpro1;
		}
		if($ls_codestpro2!="")
		{
			$as_denestpro="";
			$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,&$as_denestpro,$ls_estclades);
			$as_nombre["D"]["2"]=$as_denestpro;
		}
		if($ls_codestpro2h!="")
		{
			$as_denestpro="";
			$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1h,$ls_codestpro2h,&$as_denestpro,$ls_estclahas);
			$as_nombre["H"]["2"]=$as_denestpro;
		}
		if($ls_codestpro3!="")
		{
			$as_denestpro="";
			$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,&$as_denestpro,$ls_estclades);
			$as_nombre["D"]["3"]=$as_denestpro;
		}
		if($ls_codestpro3h!="")
		{
			$as_denestpro="";
			$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,&$as_denestpro,$ls_estclahas);
			$as_nombre["H"]["3"]=$as_denestpro;
		}
		if($li_estmodest==2)
		{
			$as_nombre["D"]["4"]="";
			$as_nombre["D"]["5"]="";
			$as_nombre["H"]["4"]="";
			$as_nombre["H"]["5"]="";
			if($ls_codestpro4!="")
			{
				$as_denestpro="";
				$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,&$as_denestpro,$ls_estclades);
				$as_nombre["D"]["4"]=$as_denestpro;
			}
			if($ls_codestpro4h!="")
			{
				$as_denestpro="";
				$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,&$as_denestpro,$ls_estclahas);
				$as_nombre["H"]["4"]=$as_denestpro;
			}
			if($ls_codestpro5!="")
			{
				$as_denestpro="";
				$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,&$as_denestpro,$ls_estclades);
				$as_nombre["D"]["5"]=$as_denestpro;
			}
			if($ls_codestpro5h!="")
			{
				$as_denestpro="";
				$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$ls_codestpro5h,&$as_denestpro,$ls_estclahas);
				$as_nombre["H"]["5"]=$as_denestpro;
			}
		}
		$ls_cmbmes=$_GET["cmbmes"];
		switch($ls_cmbmes)
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
		$li_mesdes=substr($ls_cmbmes,0,2);
		$ldt_fecdes=$li_ano."-".$li_mesdes."-01";
		$li_meshas=substr($ls_cmbmes,2,2);
		$ldt_ult_dia=$io_fecha->uf_last_day($li_meshas,$li_ano);
		$fechas=$ldt_ult_dia;
		$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
		$ls_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes);
		$ls_meshas=$io_fecha->uf_load_nombre_mes($li_meshas);
		$ls_codfuefindes=$_GET["txtcodfuefindes"];
	    $ls_codfuefinhas=$_GET["txtcodfuefinhas"];
	    if (($ls_codfuefindes=='')&&($ls_codfuefindes==''))
	    {
		  if($io_function_report->uf_spg_select_fuentefinanciamiento(&$ls_minfuefin,&$ls_maxfuefin))
		  {
		     $ls_codfuefindes=$ls_minfuefin;
		     $ls_codfuefinhas=$ls_maxfuefin;
		  } 
	    }		
		
//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
		$ls_titulo=" EJECUCION TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS";       
//--------------------------------------------------------------------------------------------------------------------------------
      //$lb_valido=true;
	  $li_numnivcuenta = count(explode("-",$_SESSION["la_empresa"]["formpre"]));
	  if($li_numnivcuenta == 5)
	  {
	   $lb_valido=$io_report->uf_spg_reportes_ejecucion_trimestral_excel($ls_codestpro1,$ls_codestpro2,
																		 $ls_codestpro3,$ls_codestpro4,
																		 $ls_codestpro5,$ls_codestpro1h,
																		 $ls_codestpro2h,$ls_codestpro3h,
																		 $ls_codestpro4h,$ls_codestpro5h,
																		 $ldt_fecdes,$ldt_fechas,
																		 $ls_codfuefindes,$ls_codfuefinhas,
																		 $ls_estclades,$ls_estclahas);
	  }
	  else
	  {
       $lb_valido=$io_report->uf_spg_reportes_ejecucion_trimestral($ls_codestpro1,$ls_codestpro2,
	                                                             $ls_codestpro3,$ls_codestpro4,
																 $ls_codestpro5,$ls_codestpro1h,
																 $ls_codestpro2h,$ls_codestpro3h,
															     $ls_codestpro4h,$ls_codestpro5h,
																 $ldt_fecdes,$ldt_fechas,
																 $ls_codfuefindes,$ls_codfuefinhas,
																 $ls_estclades,$ls_estclahas);
	  }
	 if($lb_valido==false) // Existe algún error ó no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		//print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
	 	
		/*
	    error_reporting(E_ALL);
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		uf_print_encabezado_pagina($ls_titulo,'(En Bolivares Fuertes)',$ls_trimestre,$io_pdf); // Imprimimos el encabezado de la 
 	    $io_pdf->ezStartPageNumbers(980,10,10,'','',1); // Insertar el número de página
		$io_pdf->transaction('start'); // Iniciamos la transacción
		$thisPageNum=$io_pdf->ezPageCount;
		*/
		
		$li_tot=$io_report->dts_reporte->getRowCount("spg_cuenta");
		
		
		
				
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		
		$lo_dataright_bold= &$lo_libro->addformat();
		$lo_dataright_bold->set_bold();
		$lo_dataright_bold->set_font("Verdana");
		$lo_dataright_bold->set_align('right');
		$lo_dataright_bold->set_size('9');
		
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
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,30);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,13);
		$lo_hoja->set_column(5,7,30);
		$lo_hoja->write(0, 3,$ls_titulo,$lo_titulo);
	    $ls_spg_cuenta_ant="";
		$ld_total_asignado=0;
		$ld_total_aumento=0;
		$ld_total_disminucion=0;
		$ld_total_monto_actualizado=0;
		$ld_total_compromiso=0;
		$ld_total_precompromiso=0;
		$ld_total_compromiso=0;
		$ld_total_saldo_comprometer=0;
		$ld_total_causado=0;
		$ld_total_pagado=0;
		$ld_total_por_paga=0;
		$li_row=2;
		$contlineas = 2;
	//	$li_tot=$rs_data->RecordCount();
		$z=0;
		$ld_total_asignado=0;
		$ld_total_modificado=0;
		$ld_total_programado=0;
		$ld_total_compromiso=0;
		$ld_total_causado=0;
		$ld_total_pagado=0;
		$ld_total_programado_acum=0;
		$ld_total_compromiso_acum=0;
		$ld_total_causado_acum=0;
		$ld_total_pagado_acum=0;
		$ld_total_disp_fecha=0;	
		$ld_asignado_partida=0;
		$ld_modificado_partida=0;
		$ld_programado_partida=0;
		$ld_compromiso_partida=0;
		$ld_causado_partida=0;
		$ld_pagado_partida=0;
		$ld_programado_acum_partida=0;
		$ld_compromiso_acum_partida=0;
		$ld_causado_acum_partida=0;
		$ld_pagado_acum_partida=0;
		$ld_disp_fecha_partida=0;
		$ls_partida_aux="";	
		
		$lo_hoja->write(0, 0,"Código Presupuestaro del Ente",$lo_dataleft);
		$lo_hoja->write(0, 1," ".$_SESSION["la_empresa"]["codemp"],$lo_dataleft);
		$lo_hoja->write(1, 0,"Denominación del Ente",$lo_dataleft);
		$lo_hoja->write(1, 1,$_SESSION["la_empresa"]["nombre"],$lo_dataleft);
		$lo_hoja->write(2, 0,"Proyecto y/o Acciones Centralizadas desde",$lo_dataleft);
		$lo_hoja->write(2, 1,$as_nombre["D"]["1"],$lo_dataleft);
		$lo_hoja->write(2, 4,"Hasta",$lo_dataleft);
		$lo_hoja->write(2, 5,$as_nombre["H"]["1"],$lo_dataleft);
		$lo_hoja->write(3, 0,"Acciones Específicas desde",$lo_dataleft);
		$lo_hoja->write(3, 1,$as_nombre["D"]["2"],$lo_dataleft);
		$lo_hoja->write(3, 4,"Hasta",$lo_dataleft);
		$lo_hoja->write(3, 5,$as_nombre["H"]["2"],$lo_dataleft);
		$lo_hoja->write(4, 0,"Unidad Ejecutora desde",$lo_dataleft);
		$lo_hoja->write(4, 1,$as_nombre["D"]["3"],$lo_dataleft);
		$lo_hoja->write(4, 4,"Hasta",$lo_dataleft);
		$lo_hoja->write(4, 5,$as_nombre["H"]["3"],$lo_dataleft);
		$lo_hoja->write(6, 0,"PERIODO PRESUPUESTARIO",$lo_dataleft);
		$lo_hoja->write(6, 1,$ls_trimestre.' TRIMESTRE '.$li_ano,$lo_dataleft);		
		$lo_hoja->write(7, 5,$ls_titulo,$lo_encabezado);
		$li_row=8;
		$lo_hoja->write($li_row, 8, "Ejecutado en el Trimestre Nº ".$ls_trimestre,$lo_titulo);
		$lo_hoja->write($li_row, 9, "",$lo_titulo);
		$lo_hoja->write($li_row, 10, "",$lo_titulo);
		$lo_hoja->write($li_row, 14, "Acumulado en el Trimestre Nº ".$ls_trimestre,$lo_titulo);
		$lo_hoja->write($li_row, 15, "",$lo_titulo);
		$lo_hoja->write($li_row, 16, "",$lo_titulo);
	




		$li_row=$li_row+1;
		$lo_hoja->write($li_row, 0, "PART",$lo_titulo);
		$lo_hoja->write($li_row, 1, "GEN",$lo_titulo);
		$lo_hoja->write($li_row, 2, "ESP",$lo_titulo);
		$lo_hoja->write($li_row, 3, "SUB-ESP",$lo_titulo);
		$lo_hoja->write($li_row, 5, "DENOMINACIÓN",$lo_titulo);
		$lo_hoja->write($li_row, 6, "PRESUPUESTO APROBADO",$lo_titulo);
		$lo_hoja->write($li_row, 7, "PRESUPUESTO MODIFICADO",$lo_titulo);
		$lo_hoja->write($li_row, 8, "PROGRAMADO EN EL TRIMESTRE Nº ",$lo_titulo);
		$lo_hoja->write($li_row, 9, "COMPROMISO",$lo_titulo);
		$lo_hoja->write($li_row, 10, "CAUSADO",$lo_titulo);
		$lo_hoja->write($li_row, 11, "PAGADO",$lo_titulo);
		$lo_hoja->write($li_row, 12, "PROGRAMADO",$lo_titulo);
		$lo_hoja->write($li_row, 13, "COMPROMISO",$lo_titulo);
		$lo_hoja->write($li_row, 14, "CAUSADO",$lo_titulo);
		$lo_hoja->write($li_row, 15, "PAGADO",$lo_titulo);
		$lo_hoja->write($li_row, 16, "DISPONIBILIDAD PRESUPUESTARIA",$lo_titulo);
		$li_row++;
		
		for($z=1;$z<=$li_tot;$z++)
		{		
					$ld_asignado=0;
					$ld_modificado=0;
					$ld_programado=0;
					$ld_compromiso=0;
					$ld_causado=0;
					$ld_pagado=0;
					$ld_programado_acum=0;
					$ld_compromiso_acum=0;
					$ld_causado_acum=0;
					$ld_pagado_acum=0;
					$ld_disp_fecha=0;
					$ls_partida="";
					$ls_generica="";
					$ls_especifica="";
					$ls_subesp="";
				  $ls_status="";
				  $ls_spg_cuenta             = trim($io_report->dts_reporte->data["spg_cuenta"][$z]);
				  $io_function_report->uf_get_spg_cuenta($ls_spg_cuenta,$ls_partida,$ls_generica,$ls_especifica,$ls_subesp,$as_spg_int);
				  $ls_denominacion           = trim($io_report->dts_reporte->data["denominacion"][$z]);
				  $ld_asignado               = $io_report->dts_reporte->data["asignado"][$z];
				  $ld_modificado             = $io_report->dts_reporte->data["modificado"][$z];
				  $ld_programado             = $io_report->dts_reporte->data["programado"][$z];
				  $ld_compromiso             = $io_report->dts_reporte->data["compromiso"][$z];
				  $ld_causado                = $io_report->dts_reporte->data["causado"][$z];
				  $ld_pagado                 = $io_report->dts_reporte->data["pagado"][$z];
				  $ld_programado_acum        = $io_report->dts_reporte->data["programado_acum"][$z];
				  $ld_compromiso_acum        = $io_report->dts_reporte->data["compromiso_acum"][$z];
				  $ld_causado_acum           = $io_report->dts_reporte->data["causado_acum"][$z];
				  $ld_pagado_acum            = $io_report->dts_reporte->data["pagado_acum"][$z];
				  $ld_disp_fecha             = $io_report->dts_reporte->data["disponible_fecha"][$z];
				  $ls_status                 = $io_report->dts_reporte->data["status"][$z];
				  
				  if($ls_status == "C")
				  {
				   $ld_total_asignado         = $ld_total_asignado + $ld_asignado;
				   $ld_total_modificado       = $ld_total_modificado + $ld_modificado;
		           $ld_total_programado       = $ld_total_programado + $ld_programado;
		           $ld_total_compromiso       = $ld_total_compromiso + $ld_compromiso;
		           $ld_total_causado          = $ld_total_causado + $ld_causado;
		           $ld_total_pagado           = $ld_total_pagado + $ld_pagado;
		           $ld_total_programado_acum  = $ld_total_programado_acum + $ld_programado_acum;
		           $ld_total_compromiso_acum  = $ld_total_compromiso_acum + $ld_compromiso_acum;
		           $ld_total_causado_acum     = $ld_total_causado_acum + $ld_causado_acum;
		           $ld_total_pagado_acum      = $ld_total_pagado_acum + $ld_pagado_acum;
		           $ld_total_disp_fecha       = $ld_total_disp_fecha + $ld_disp_fecha;
				  } 
				  
				  ///-------------------agrupar por partida---------------------------------------
				  if ($ls_partida_aux=="")
				  {
				 		$ls_partida_aux=$ls_partida; 
				  }
				  elseif ($ls_partida_aux==$ls_partida)
				  {
				  	
					 if($ls_status=="C")
					 {
						 $ls_partida_aux=$ls_partida;
						 $ld_asignado_partida=$ld_asignado_partida+$ld_asignado;
						 $ld_modificado_partida=$ld_modificado_partida+$ld_modificado;
						 $ld_programado_partida=$ld_programado_partida+$ld_programado;
						 $ld_compromiso_partida=$ld_compromiso_partida+$ld_compromiso;
						 $ld_causado_partida=$ld_causado_partida+$ld_causado;
						 $ld_pagado_partida=$ld_pagado_partida+$ld_pagado;
						 $ld_programado_acum_partida=$ld_programado_acum_partida+$ld_programado_acum;
						 $ld_compromiso_acum_partida=$ld_compromiso_acum_partida+$ld_compromiso_acum;
						 $ld_causado_acum_partida=$ld_causado_acum_partida+$ld_causado_acum;
						 $ld_pagado_acum_partida=$ld_pagado_acum_partida+$ld_pagado_acum;
						 $ld_disp_fecha_partida=$ld_disp_fecha_partida+$ld_disp_fecha;	
					}			 
				  }
				  else
				  {
				  	 $ld_asignado_partida       = number_format($ld_asignado_partida,2,",",".");
				     $ld_modificado_partida     = number_format($ld_modificado_partida,2,",",".");
				     $ld_programado_partida     = number_format($ld_programado_partida,2,",",".");
				     $ld_compromiso_partida     = number_format($ld_compromiso_partida,2,",",".");
				     $ld_causado_partida        = number_format($ld_causado_partida,2,",",".");
				     $ld_pagado_partida         = number_format($ld_pagado_partida,2,",",".");
				     $ld_programado_acum_partida  = number_format($ld_programado_acum_partida,2,",",".");
				     $ld_compromiso_acum_partida  = number_format($ld_compromiso_acum_partida,2,",",".");
				     $ld_causado_acum_partida     = number_format($ld_causado_acum_partida,2,",",".");
				     $ld_pagado_acum_partida      = number_format($ld_pagado_acum_partida,2,",",".");
				     $ld_disp_fecha_partida       = number_format($ld_disp_fecha_partida,2,",",".");

/*					 
					 uf_print_total_partidas($ls_partida_aux,$ld_asignado_partida,$ld_modificado_partida,
	                                        $ld_programado_partida, $ld_compromiso_partida,
									        $ld_causado_partida,$ld_pagado_partida,
									        $ld_programado_acum_partida,$ld_compromiso_acum_partida,
									        $ld_causado_acum_partida,$ld_pagado_acum_partida,
											$ld_disp_fecha_partida,&$io_pdf);
											
*/											
						if($ld_asignado_partida == $ld_modificado_partida)
						{
						 $ld_modificado_partida = "";
						}
						$li_row=$li_row+1;
						$lo_hoja->write($li_row, 0, "",$lo_dataright);
						$lo_hoja->write($li_row, 1, "",$lo_dataright);
						$lo_hoja->write($li_row, 2, "",$lo_dataright);
						$lo_hoja->write($li_row, 3, "",$lo_dataright);
						$lo_hoja->write($li_row, 4, "TOTALES PARTIDA ".$ls_partida_aux,$lo_titulo);
						$lo_hoja->write($li_row, 6, $ld_asignado_partida,$lo_dataright_bold);
						$lo_hoja->write($li_row, 7, $ld_modificado_partida,$lo_dataright_bold);
						$lo_hoja->write($li_row, 8, $ld_programado_partida,$lo_dataright_bold);
						$lo_hoja->write($li_row, 9, $ld_compromiso_partida,$lo_dataright_bold);
						$lo_hoja->write($li_row, 10, $ld_causado_partida,$lo_dataright_bold);
						$lo_hoja->write($li_row, 11, $ld_pagado_partida,$lo_dataright_bold);
						$lo_hoja->write($li_row, 12,$ld_programado_acum_partida,$lo_dataright_bold);
						$lo_hoja->write($li_row, 13, $ld_compromiso_acum_partida,$lo_dataright_bold);
						$lo_hoja->write($li_row, 14, $ld_causado_acum_partida,$lo_dataright_bold);
						$lo_hoja->write($li_row, 15, $ld_pagado_acum_partida,$lo_dataright_bold);
						$lo_hoja->write($li_row, 16,$ld_disp_fecha_partida,$lo_dataright_bold);
						$li_row++;
						$li_row++;					
						$ld_asignado_partida=0;
						$ld_modificado_partida=0;
						$ld_programado_partida=0;
						$ld_compromiso_partida=0;
						$ld_causado_partida=0;
						$ld_pagado_partida=0;
						$ld_programado_acum_partida=0;
						$ld_compromiso_acum_partida=0;
						$ld_causado_acum_partida=0;
						$ld_pagado_acum_partida=0;
						$ld_disp_fecha_partida=0;		
						$ls_partida_aux=$ls_partida;

				  }
				 
				  //------------------------------------------------------------------------------
				  $ld_asignado               = number_format($ld_asignado,2,",",".");
				  $ld_modificado             = number_format($ld_modificado,2,",",".");
				  $ld_programado             = number_format($ld_programado,2,",",".");
				  $ld_compromiso             = number_format($ld_compromiso,2,",",".");
				  $ld_causado                = number_format($ld_causado,2,",",".");
				  $ld_pagado                 = number_format($ld_pagado,2,",",".");
				  $ld_programado_acum        = number_format($ld_programado_acum,2,",",".");
				  $ld_compromiso_acum        = number_format($ld_compromiso_acum,2,",",".");
				  $ld_causado_acum           = number_format($ld_causado_acum,2,",",".");
				  $ld_pagado_acum            = number_format($ld_pagado_acum,2,",",".");
				  $ld_disp_fecha             = number_format($ld_disp_fecha,2,",",".");
				
	/*			  
				  uf_print_detalle($ls_partida,$ls_generica,$ls_especifica,$ls_subesp,$ls_denominacion,$ld_asignado,
	                               $ld_modificado,$ld_programado,$ld_compromiso,$ld_causado,$ld_pagado,
							       $ld_programado_acum, $ld_compromiso_acum,$ld_causado_acum, $ld_pagado_acum,
								   $ld_disp_fecha,&$io_pdf); // Imprimimos el detalle
								   
	*/							   
								   
				$lo_hoja->write($li_row, 0, $ls_partida,$lo_dataleft);
				$lo_hoja->write($li_row, 1, " ".$ls_generica,$lo_dataleft);
				$lo_hoja->write($li_row, 2, " ".$ls_especifica,$lo_dataleft);
				$lo_hoja->write($li_row, 3, " ".$ls_subesp,$lo_dataleft);
				
				if($as_spg_int!='')
				{
					$lo_hoja->write($li_row,4," ".$as_spg_int,$lo_datacenter);
					$lo_hoja->write(9,4,"INT",$lo_titulo);
				}
				if($ld_asignado == $ld_modificado)
				{
				 $ld_modificado = "";
				}
				$lo_hoja->write($li_row, 5, $ls_denominacion,$lo_dataleft);
				$lo_hoja->write($li_row, 6, $ld_asignado,$lo_dataright);
				$lo_hoja->write($li_row, 7, $ld_modificado,$lo_dataright);
				$lo_hoja->write($li_row, 8, $ld_programado,$lo_dataright);
				$lo_hoja->write($li_row, 9, $ld_compromiso,$lo_dataright);
				$lo_hoja->write($li_row, 10, $ld_causado,$lo_dataright);
				$lo_hoja->write($li_row, 11, $ld_pagado,$lo_dataright);
				$lo_hoja->write($li_row, 12, $ld_programado_acum,$lo_dataright);
				$lo_hoja->write($li_row, 13, $ld_compromiso_acum,$lo_dataright);
				$lo_hoja->write($li_row, 14, $ld_causado_acum,$lo_dataright);
				$lo_hoja->write($li_row, 15, $ld_pagado_acum,$lo_dataright);
				$lo_hoja->write($li_row, 16,$ld_disp_fecha,$lo_dataright);				  
				$li_row++;	  							 						   
			}//for
		
		
		///-----------------------totales por partidas-------------------------------------------------
		
		             $ld_asignado_partida       = number_format($ld_asignado_partida,2,",",".");
				     $ld_modificado_partida     = number_format($ld_modificado_partida,2,",",".");
				     $ld_programado_partida     = number_format($ld_programado_partida,2,",",".");
				     $ld_compromiso_partida     = number_format($ld_compromiso_partida,2,",",".");
				     $ld_causado_partida        = number_format($ld_causado_partida,2,",",".");
				     $ld_pagado_partida         = number_format($ld_pagado_partida,2,",",".");
				     $ld_programado_acum_partida  = number_format($ld_programado_acum_partida,2,",",".");
				     $ld_compromiso_acum_partida  = number_format($ld_compromiso_acum_partida,2,",",".");
				     $ld_causado_acum_partida     = number_format($ld_causado_acum_partida,2,",",".");
				     $ld_pagado_acum_partida      = number_format($ld_pagado_acum_partida,2,",",".");
				     $ld_disp_fecha_partida       = number_format($ld_disp_fecha_partida,2,",",".");
					 
					/*
					 uf_print_total_partidas($ls_partida_aux,$ld_asignado_partida,$ld_modificado_partida,
	                                        $ld_programado_partida, $ld_compromiso_partida,
									        $ld_causado_partida,$ld_pagado_partida,
									        $ld_programado_acum_partida,$ld_compromiso_acum_partida,
									        $ld_causado_acum_partida,$ld_pagado_acum_partida,
											$ld_disp_fecha_partida,&$io_pdf);
											
					*/						
					$li_row=$li_row+1;
					if($ld_asignado_partida == $ld_modificado_partida)
					{
					 $ld_modificado_partida = "";
					}
					
					$lo_hoja->write($li_row, 0, "",$lo_titulo);
					$lo_hoja->write($li_row, 1, "",$lo_titulo);
					$lo_hoja->write($li_row, 2, "",$lo_titulo);
					$lo_hoja->write($li_row, 3, "",$lo_titulo);
					$lo_hoja->write($li_row, 4, "TOTALES PARTIDA ".$ls_partida_aux,$lo_titulo);
					$lo_hoja->write($li_row, 6, $ld_asignado_partida,$lo_dataright_bold);
					$lo_hoja->write($li_row, 7, $ld_modificado_partida,$lo_dataright_bold);
					$lo_hoja->write($li_row, 8, $ld_programado_partida,$lo_dataright_bold);
					$lo_hoja->write($li_row, 9, $ld_compromiso_partida,$lo_dataright_bold);
					$lo_hoja->write($li_row, 10, $ld_causado_partida,$lo_dataright_bold);
					$lo_hoja->write($li_row, 11, $ld_pagado_partida,$lo_dataright_bold);
					$lo_hoja->write($li_row, 12,$ld_programado_acum_partida,$lo_dataright_bold);
					$lo_hoja->write($li_row, 13, $ld_compromiso_acum_partida,$lo_dataright_bold);
					$lo_hoja->write($li_row, 14, $ld_causado_acum_partida,$lo_dataright_bold);
					$lo_hoja->write($li_row, 15, $ld_pagado_acum_partida,$lo_dataright_bold);
					$lo_hoja->write($li_row, 16,$ld_disp_fecha_partida,$lo_dataright_bold);
						
											
		//----------------------------------------------------------------------------------------------	
		
		$ld_total_asignado         = number_format($ld_total_asignado,2,",",".");
		$ld_total_modificado       = number_format($ld_total_modificado,2,",",".");
		$ld_total_programado       = number_format($ld_total_programado,2,",",".");
		$ld_total_compromiso       = number_format($ld_total_compromiso,2,",",".");
		$ld_total_causado          = number_format($ld_total_causado,2,",",".");
	    $ld_total_pagado           = number_format($ld_total_pagado,2,",",".");
		$ld_total_programado_acum  = number_format($ld_total_programado_acum,2,",",".");
		$ld_total_compromiso_acum  = number_format($ld_total_compromiso_acum,2,",",".");
		$ld_total_causado_acum     = number_format($ld_total_causado_acum,2,",",".");
		$ld_total_pagado_acum      = number_format($ld_total_pagado_acum,2,",",".");
		$ld_total_disp_fecha       = number_format($ld_total_disp_fecha,2,",",".");
			
			
			/*
		$la_data_tot[1]=array('totales'=>"TOTALES",
							  'asignado'=>$ld_total_asignado,
							  'modificado'=>$ld_total_modificado,
							  'programado'=>$ld_total_programado,
							  'compromiso'=>$ld_total_compromiso,
							  'causado'=>$ld_total_causado,
							  'pagado'=>$ld_total_pagado,
							  'programado_acum'=>$ld_total_programado_acum,
							  'compromiso_acum'=>$ld_total_compromiso_acum,
							  'causado_acum'=>$ld_total_causado_acum,
							  'pagado_acum'=>$ld_total_pagado_acum,
							  'disp_fecha'=>$ld_total_disp_fecha);							              	
		    uf_print_pie_cabecera($la_data_tot,$io_pdf);
			*/
			
				$li_row=$li_row+1;
				$li_row++;
				if($ld_total_asignado == $ld_total_modificado)
				{
				 $ld_total_modificado = "";
				}
				$lo_hoja->write($li_row, 0, "",$lo_titulo);
				$lo_hoja->write($li_row, 1, "",$lo_titulo);
				$lo_hoja->write($li_row, 2, "",$lo_titulo);
				$lo_hoja->write($li_row, 3, "",$lo_titulo);
				$lo_hoja->write($li_row, 4, "TOTALES",$lo_titulo);
				$lo_hoja->write($li_row, 6, $ld_total_asignado,$lo_dataright_bold);
				$lo_hoja->write($li_row, 7, $ld_total_modificado,$lo_dataright_bold);
				$lo_hoja->write($li_row, 8, $ld_total_programado,$lo_dataright_bold);
				$lo_hoja->write($li_row, 9, $ld_total_compromiso,$lo_dataright_bold);
				$lo_hoja->write($li_row, 10, $ld_total_causado,$lo_dataright_bold);
				$lo_hoja->write($li_row, 11, $ld_total_pagado,$lo_dataright_bold);
				$lo_hoja->write($li_row, 12, $ld_total_programado_acum,$lo_dataright_bold);
				$lo_hoja->write($li_row, 13, $ld_total_compromiso_acum,$lo_dataright_bold);
				$lo_hoja->write($li_row, 14, $ld_total_causado_acum,$lo_dataright_bold);
				$lo_hoja->write($li_row, 15, $ld_total_pagado_acum,$lo_dataright_bold);
				$lo_hoja->write($li_row, 16,$ld_total_disp_fecha,$lo_dataright_bold);
				
				unset($la_data);
				unset($la_data_tot);
				$lo_libro->close();
				header("Content-Type: application/x-msexcel; name=\"EJECUCION TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS.xls\"");
				header("Content-Disposition: inline; filename=\"EJECUCION TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS.xls\"");
				$fh=fopen($lo_archivo, "rb");
				fpassthru($fh);
				unlink($lo_archivo);
				print("<script language=JavaScript>");
				print(" close();");
				print("</script>");			
			/*	
		$io_pdf->ezStopPageNumbers(1,1);
		if (isset($d) && $d)
		{
			//echo "al final";
			//die();
			$ls_pdfcode = $io_pdf->ezOutput(1);
			$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
			echo '<html><body>';
			echo trim($ls_pdfcode);
			echo '</body></html>';
		}
		else
		{
			$io_pdf->ezStream();
		}
		unset($io_pdf);
		
		*/
		
	}//else
	unset($io_report);
	unset($io_funciones);
?> 