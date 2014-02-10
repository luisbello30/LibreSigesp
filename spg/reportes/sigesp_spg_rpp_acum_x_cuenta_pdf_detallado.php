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
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 27/09/2006 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,30,1000,30);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],10,550,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,550,16,$as_titulo); // Agregar el título
		
		$io_pdf->addText(900,550,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(900,540,10,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
    function uf_print_encabezado_pagina2($as_titulo,$as_titulo1,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina2
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página manejando esructuras presupuestarias
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 27/09/2006 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,30,1000,30);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],10,550,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,550,16,$as_titulo); // Agregar el título
	    $li_tm=$io_pdf->getTextWidth(16,$as_titulo1);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,530,16,$as_titulo1); // Agregar el título
		
		$io_pdf->addText(900,550,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(900,540,10,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina

	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($io_cabecera,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 27/09/2006 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->saveState();
		//$io_pdf->ezSetY(480);
		$io_pdf->ezSetY(460);
		$la_data=array(array('cuenta'=>'<b>Cuenta</b>',
		                     'denominacion'=>'<b>Denominación</b>',
							 'asignado'=>'<b>Asignado</b>',
		                     'aumento'=>'<b>Aumento</b>',
							 'disminución'=>'<b>Disminución</b>',
							 'montoactualizado'=>'<b>Monto Actualizado</b>',
							 'precomprometido'=>'<b>Pre-Comprometido</b>',
							 'comprometido'=>'<b>Comprometido</b>',
							 'saldoxcomprometer'=>'<b>Saldo por Comprometer</b>',
							 'causado'=>'<b>Causado</b>',
							 'pagado'=>'<b>Pagado</b>',
							 'porpagar'=>'<b>Por Pagar</b>'));
		
		$la_columna=array('cuenta'=>'',
		                  'denominacion'=>'',
					      'asignado'=>'',
		                  'aumento'=>'',
						  'disminución'=>'',
						  'montoactualizado'=>'',
						  'precomprometido'=>'',
						  'comprometido'=>'',
						  'saldoxcomprometer'=>'',
						  'causado'=>'',
						  'pagado'=>'',
						  'porpagar'=>'');
		$la_config=array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la 
						 			   'denominacion'=>array('justification'=>'center','width'=>160), // Justificación y  
						 			   'asignado'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la 
						 			   'aumento'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la 
									   'disminución'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la 
									   'montoactualizado'=>array('justification'=>'center','width'=>75), // Justificación y ancho de 
									   'precomprometido'=>array('justification'=>'center','width'=>75), // Justificación y ancho de 
									   'comprometido'=>array('justification'=>'center','width'=>75), // Justificación  
									   'saldoxcomprometer'=>array('justification'=>'center','width'=>75), // Justificación y ancho 
									   'causado'=>array('justification'=>'center','width'=>75),
									   'pagado'=>array('justification'=>'center','width'=>75),
									   'porpagar'=>array('justification'=>'center','width'=>75))); // Justificación y ancho de la 
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_cabecera,'all');
	unset($la_data);
	unset($la_columnas);
	unset($la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 11/04/2010
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$la_config=array(//'showHeadings'=>1, // Mostrar encabezados
						 'showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 //'colGap'=>7, // separacion entre tablas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la 
						 			   'denominacion'=>array('justification'=>'left','width'=>160), // Justificación y  
						 			   'asignado'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
						 			   'aumento'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
									   'disminución'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
									   'montoactualizado'=>array('justification'=>'right','width'=>75), // Justificación y ancho de 
									   'precomprometido'=>array('justification'=>'right','width'=>75), // Justificación y ancho de 
									   'comprometido'=>array('justification'=>'right','width'=>75), // Justificación  
									   'saldoxcomprometer'=>array('justification'=>'right','width'=>75), // Justificación y ancho 
									   'causado'=>array('justification'=>'right','width'=>75),
									   'pagado'=>array('justification'=>'right','width'=>75),
									   'porpagar'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la 
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
		                   'denominacion'=>'<b>Denominación</b>',
					       'asignado'=>'<b>Asignado</b>',
		                   'aumento'=>'<b>Aumento</b>',
						   'disminución'=>'<b>Disminución</b>',
						   'montoactualizado'=>'<b>Monto Actualizado</b>',
						   'precomprometido'=>'<b>Pre-Comprometido</b>',
						   'comprometido'=>'<b>Comprometido</b>',
						   'saldoxcomprometer'=>'<b>Saldo por Comprometer</b>',
						   'causado'=>'<b>Causado</b>',
						   'pagado'=>'<b>Pagado</b>',
						   'porpagar'=>'<b>Por Pagar</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		/*unset($la_data);
		unset($la_columnas);
		unset($la_config);*/
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($la_data_tot,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 27/09/2006 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('total'=>array('justification'=>'center','width'=>230), // Justificación y ancho de la 
						 			   'asignado'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
						 			   'aumento'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
									   'disminución'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
									   'montoactualizado'=>array('justification'=>'right','width'=>75), // Justificación y ancho de 
									   'precomprometido'=>array('justification'=>'right','width'=>75), // Justificación y ancho de 
									   'comprometido'=>array('justification'=>'right','width'=>75), // Justificación  
									   'saldoxcomprometer'=>array('justification'=>'right','width'=>75), // Justificación y ancho 
									   'causado'=>array('justification'=>'right','width'=>75),
									   'pagado'=>array('justification'=>'right','width'=>75),
									   'porpagar'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la 
		$la_columnas=array('total'=>'',
					       'asignado'=>'',
		                   'aumento'=>'',
						   'disminución'=>'',
						   'montoactualizado'=>'',
						   'precomprometido'=>'',
						   'comprometido'=>'',
						   'saldoxcomprometer'=>'',
						   'causado'=>'',
						   'pagado'=>'',
						   'porpagar'=>'');
		$io_pdf->ezTable($la_data_tot,$la_columnas,'',$la_config);
		unset($la_data_tot);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_pie_cabecera
//--------------------------------------------------------------------------------------------------------------------------------------
	 function uf_print_cabecera_estructura($io_encabezado,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
		                                    $ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,&$io_pdf)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_cabecera
	//		   Access: private 
	//	    Arguments: as_programatica // programatica del comprobante
	//	    		   as_denestpro5 // denominacion de la programatica del comprobante
	//	    		   io_pdf // Objeto PDF
	//    Description: función que imprime la cabecera de cada página
	//	   Creado Por: Ing. Jennifer Rivero
	// Fecha Creación: 17/11/2008 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$ls_estmodest  = $_SESSION["la_empresa"]["estmodest"];
		$li_nomestpro1 = $_SESSION["la_empresa"]["nomestpro1"];
		$li_nomestpro2 = $_SESSION["la_empresa"]["nomestpro2"];
		$li_nomestpro3 = $_SESSION["la_empresa"]["nomestpro3"];
		$li_nomestpro4 = $_SESSION["la_empresa"]["nomestpro4"];
		$li_nomestpro5 = $_SESSION["la_empresa"]["nomestpro5"];
		$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	    $li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	    $li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	    $li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
	    $li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		
		$ls_codestpro1    = trim(substr($ls_codestpro1,-$li_loncodestpro1));
		$ls_codestpro2    = trim(substr($ls_codestpro2,-$li_loncodestpro2));
		$ls_codestpro3    = trim(substr($ls_codestpro3,-$li_loncodestpro3));
		$ls_codestpro4    = trim(substr($ls_codestpro4,-$li_loncodestpro4));
		$ls_codestpro5    = trim(substr($ls_codestpro5,-$li_loncodestpro5));
		
		$io_pdf->saveState();
		$io_pdf->ezSetY(520);
		
		if ($ls_estmodest==1)
		{
			$ls_datat1[1]=array('nombre'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro'=>$ls_codestpro1,'denom'=>$ls_denestpro1);
			$ls_datat1[2]=array('nombre'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro'=>$ls_codestpro2,'denom'=>$ls_denestpro2);
			$ls_datat1[3]=array('nombre'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro'=>$ls_codestpro3,'denom'=>$ls_denestpro3);			
			
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' =>7, // Tamaño de Letras
							 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
							 'showLines'=>0, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'colGap'=>1, // separacion entre tablas
							 'width'=>990, // Ancho de la tabla
							 'maxWidth'=>990, // Ancho Máximo de la tabla
							 'xOrientation'=>'center', // Orientación de la tabla
							 //'xPos'=>290, // Orientación de la tabla
							 'xPos'=>375, // Orientación de la tabla
							 'cols'=>array('nombre'=>array('justification'=>'left','width'=>150),									  
										   'codestpro'=>array('justification'=>'right','width'=>60),
										   'denom'=>array('justification'=>'left','width'=>500)));
			$la_columna=array('nombre'=>'','codestpro'=>'','denom'=>'');			
			$io_pdf->ezTable($ls_datat1,$la_columna,'',$la_config);
		}
		else
		{
			$ls_datat1[1]=array('nombre'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro'=>$ls_codestpro1,'denom'=>$ls_denestpro1);
			$ls_datat1[2]=array('nombre'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro'=>$ls_codestpro2,'denom'=>$ls_denestpro2);
			$ls_datat1[3]=array('nombre'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro'=>$ls_codestpro3,'denom'=>$ls_denestpro3);
			$ls_datat1[4]=array('nombre'=>'<b>'.$li_nomestpro4.":</b> ",'codestpro'=>$ls_codestpro4,'denom'=>$ls_denestpro4);
			$ls_datat1[5]=array('nombre'=>'<b>'.$li_nomestpro5.":</b> ",'codestpro'=>$ls_codestpro5,'denom'=>$ls_denestpro5);			
			
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' => 6, // Tamaño de Letras
							 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
							 'showLines'=>0, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'colGap'=>1, // separacion entre tablas
							 'width'=>990, // Ancho de la tabla
							 'maxWidth'=>990, // Ancho Máximo de la tabla
							 'xOrientation'=>'center', // Orientación de la tabla
							 //'xPos'=>302, // Orientación de la tabla
							 'xPos'=>375, // Orientación de la tabla
							 'cols'=>array('nombre'=>array('justification'=>'left','width'=>100),									  
										   'codestpro'=>array('justification'=>'right','width'=>60),
										   'denom'=>array('justification'=>'left','width'=>550)));
		   $la_columna=array('nombre'=>'','codestpro'=>'','denom'=>'');			
		   $io_pdf->ezTable($ls_datat1,$la_columna,'',$la_config);	
		}
		unset($ls_datat1);
		unset($la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');			
	}// end function uf_print_cabecera
//--------------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../shared/ezpdf/class.ezpdf.php");
		require_once("sigesp_spg_reporte.php");
		$io_report = new sigesp_spg_reporte();
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report = new sigesp_spg_funciones_reportes();
		require_once("../../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();
		require_once("sigesp_spg_funciones_reportes.php");
		$io_fun_gasto=new sigesp_spg_funciones_reportes();		
		require_once("../../shared/class_folder/class_fecha.php");
		$io_fecha = new class_fecha();
//--------------------------------------------------  Parámetros para Filtar el Reporte  ---------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$ls_cmbmesdes = $_GET["cmbmesdes"];
		$ldt_fecini=$li_ano."-01-01";
		$ldt_fecini_rep="01/01/".$li_ano;
		$ls_cmbmeshas = $_GET["cmbmeshas"];
		$ls_mes=$ls_cmbmeshas;
		$ls_ano=$li_ano;
		$fecfin=$io_fecha->uf_last_day($ls_mes,$ls_ano);
		$ldt_fecfin=$io_funciones->uf_convertirdatetobd($fecfin);
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		$ls_codestpro1_min  = $_GET["codestpro1"];
		$ls_codestpro2_min  = $_GET["codestpro2"];
		$ls_codestpro3_min  = $_GET["codestpro3"];
		$ls_codestpro1h_max = $_GET["codestpro1h"];
		$ls_codestpro2h_max = $_GET["codestpro2h"];
		$ls_codestpro3h_max = $_GET["codestpro3h"];
		$ls_estclades       = $_GET["estclades"];
		$ls_estclahas       = $_GET["estclahas"];
		$ls_loncodestpro1   = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2   = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3   = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4   = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5   = $_SESSION["la_empresa"]["loncodestpro5"];
		$ls_cuentades_min=$_GET["txtcuentades"];
		$ls_cuentahas_max=$_GET["txtcuentahas"];
		
		if($ls_cuentades_min=="")
		{
		   if($io_function_report->uf_spg_reporte_select_min_cuenta($ls_cuentades_min))
		   {
		     $ls_cuentades=$ls_cuentades_min;
		   }
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestraias');");
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentades=$ls_cuentades_min;
		}
		if($ls_cuentahas_max=="")
		{
		   if($io_function_report->uf_spg_reporte_select_max_cuenta($ls_cuentahas_max))
		   {
		     $ls_cuentahas=$ls_cuentahas_max;
		   }
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestraias');");
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentahas=$ls_cuentahas_max;
		}
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
	
			if($ls_modalidad==1)
			{
				$ls_codestpro4_min =  "0000000000000000000000000";
				$ls_codestpro5_min =  "0000000000000000000000000";
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
			elseif($ls_modalidad==2)
			{
				$ls_codestpro4_min  = $_GET["codestpro4"];
				$ls_codestpro5_min  = $_GET["codestpro5"];
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
				}
				else
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
			
			$ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
			$ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h;
			if($ls_modalidad==1)
			{
				if (($ls_codestpro1<>"")&&($ls_codestpro2=="")&&($ls_codestpro3==""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1);
				}
				elseif(($ls_codestpro1<>"")&&($ls_codestpro2<>"")&&($ls_codestpro3==""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2);
				}
				elseif(($ls_codestpro1<>"")&&($ls_codestpro2<>"")&&($ls_codestpro3<>""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2)."-".substr($ls_codestpro3h,-$ls_loncodestpro3);
				}
				else
				{
				 $ls_programatica_desde1="";
				 $ls_programatica_hasta1="";
				}
			}
			else
			{
				$ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5)."-".$ls_estclades;
				$ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2)."-".substr($ls_codestpro3h,-$ls_loncodestpro3)."-".substr($ls_codestpro4h,-$ls_loncodestpro4)."-".substr($ls_codestpro5h,-$ls_loncodestpro5)."-".$ls_estclahas;
			}

		$cmbnivel=$_GET["cmbnivel"];
		if($cmbnivel=="s1")
		{
          $ls_cmbnivel="1";
		}
		else
		{
          $ls_cmbnivel=$cmbnivel;
		}
        $ls_subniv=$_GET["checksubniv"];
		if($ls_subniv==1)
		{
		  $lb_subniv=true;
		}
		else
		{
		  $lb_subniv=false;
		}
		/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
		
		
		
		$ls_desc_event="Solicitud de Reporte Acumulado por Cuentas desde la fecha ".$ldt_fecini_rep." hasta ".$fecfin;
		$io_fun_gasto->uf_load_seguridad_reporte("SPG","sigesp_spg_r_acum_x_cuentas.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////
     //----------------------------------------------------  Parámetros del encabezado  --------------------------------------------
		$ls_titulo=" <b> ACUMULADO POR CUENTAS  DESDE LA FECHA ".$ldt_fecini_rep."  HASTA  ".$fecfin." </b> ";
		$ls_titulo1 = "";
		switch($ls_modalidad)
		{
		 case 2  : $ls_titulo1="<b> DESDE LA PROGRAMATICA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1." </b>";
		 break;
		 default : $ls_titulo1="<b> DESDE LA ESTRUCTURA PRESUPUESTARIA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1." </b>";
		}
	    
		$ls_tiporeporte=$_GET["tiporeporte"];
		global $ls_tiporeporte;
		require_once("../../shared/ezpdf/class.ezpdf.php");
		          
    //--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
	$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
	$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
	$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
	$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
	$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
	$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
		
	$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
	$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
	$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
	$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
	$ls_codestpro5h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
	
	$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	$li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	$li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	$li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
	$li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
	error_reporting(E_ALL);
	set_time_limit(1800);
	$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	//$io_pdf->ezSetCmMargins(3.4,3,3,3); // Configuración de los margenes en centímetros
	$io_pdf->ezSetCmMargins(4.3,3,3,3); // Configuración de los margenes en centímetros
	//uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la página
	uf_print_encabezado_pagina2($ls_titulo,$ls_titulo1,$io_pdf);
	$io_pdf->ezStartPageNumbers(980,40,10,'','',1); // Insertar el número de página
	
	// TOTALES GENERALES
	$ld_totalgen_asignado=0;
	$ld_totalgen_aumento=0;
	$ld_totalgen_disminucion=0;
	$ld_totalgen_monto_actualizado=0;
	$ld_totalgen_precomprometido=0;
	$ld_totalgen_comprometido=0;
	$ld_totalgen_saldoxcomprometer=0;
	$ld_totalgen_causado=0;
	$ld_totalgen_pagado=0;
	$ld_totalgen_por_pagar=0;
	$rs_cuentas = NULL;
	$rs_estructuras = NULL;
	$j=0;
	$total_estructuras = 0;
	//$io_encabezado=$io_pdf->openObject();
	//uf_print_encabezado_pagina2($ls_titulo,$ls_titulo1,$io_pdf); // Imprimimos el encabezado de la página
	
	$lb_valido = $io_report->obtener_estructuras_presupuestarias($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																 $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
																 $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas,$rs_estructuras);
	if($lb_valido)
	{
	 if($rs_estructuras->RecordCount()>0)
	 {
	  $total_estructuras = $rs_estructuras->RecordCount();
	  while(!$rs_estructuras->EOF)
	  {
	    $j++;
		$thisPageNum=$io_pdf->ezPageCount;
		$ls_codestpro1_fil = $rs_estructuras->fields["codestpro1"];
		$ls_denestpro1_fil = $rs_estructuras->fields["denestpro1"];
		$ls_codestpro2_fil = $rs_estructuras->fields["codestpro2"];
		$ls_denestpro2_fil = $rs_estructuras->fields["denestpro2"];
		$ls_codestpro3_fil = $rs_estructuras->fields["codestpro3"];
		$ls_denestpro3_fil = $rs_estructuras->fields["denestpro3"];
		$ls_codestpro4_fil = $rs_estructuras->fields["codestpro4"];
		$ls_denestpro4_fil = $rs_estructuras->fields["denestpro4"];
		$ls_codestpro5_fil = $rs_estructuras->fields["codestpro5"];
		$ls_denestpro5_fil = $rs_estructuras->fields["denestpro5"];
		$ls_estcla_fil     = $rs_estructuras->fields["estcla"];
		$lb_valido=$io_report->obtener_cuentas_presupuestarias($ls_codestpro1_fil,$ls_codestpro2_fil,$ls_codestpro3_fil,$ls_codestpro4_fil,
															   $ls_codestpro5_fil,$ls_cmbnivel,$ls_cuentades_min,$ls_cuentahas_max,
															    $ls_codfuefindes,$ls_codfuefinhas,$ls_estcla_fil,$rs_cuentas);	
		if($lb_valido)
		{
		 if($rs_cuentas->RecordCount()>0)
		 {
		  	 // TOTALES POR ESTRUCTURA
		   $ld_total_asignado=0;
		   $ld_total_aumento=0;
		   $ld_total_disminucion=0;
		   $ld_total_monto_actualizado=0;
		   $ld_total_precomprometido=0;
		   $ld_total_comprometido=0;
		   $ld_total_saldoxcomprometer=0;
		   $ld_total_causado=0;
		   $ld_total_pagado=0;
		   $ld_total_por_pagar=0;
		   $i=0;
		   $total_cuentas = $rs_cuentas->RecordCount();
		   while(!$rs_cuentas->EOF)
		   {
			  $thisPageNum=$io_pdf->ezPageCount;
			  $ld_asignado=0;
		      $ld_aumento=0;
		      $ld_disminucion=0;
		      $ld_monto_actualizado=0;
		      $ld_precomprometido=0;
		      $ld_comprometido=0;
		      $ld_saldoxcomprometer=0;
		      $ld_causado=0;
		      $ld_pagado=0;
		      $ld_por_pagar=0;
			  $ls_status = "";
			  $ls_codestpro1=trim($rs_cuentas->fields["codestpro1"]); 
			  $ls_codestpro2=trim($rs_cuentas->fields["codestpro2"]);
			  $ls_codestpro3=trim($rs_cuentas->fields["codestpro3"]);
			  $ls_codestpro4=trim($rs_cuentas->fields["codestpro4"]);
			  $ls_codestpro5=trim($rs_cuentas->fields["codestpro5"]);
			  $ls_estcla=trim($rs_cuentas->fields["estcla"]);
			  $ls_spg_cuenta = trim($rs_cuentas->fields["spg_cuenta"]);
			  $ls_denominacion = trim($rs_cuentas->fields["denominacion"]);
			  $ld_asignado = $rs_cuentas->fields["asignado"];
			  $ls_status = trim($rs_cuentas->fields["status"]);
			  $li_nivel = $rs_cuentas->fields["nivel"];
			  $lb_valido=$io_report->uf_spg_ejecutado_cuenta($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
								                             $ls_codestpro5,$ls_estcla,$ls_spg_cuenta,$ldt_fecini,$ldt_fecfin,$ld_precomprometido,
								                             $ld_comprometido,$ld_causado,$ld_pagado,$ld_aumento,$ld_disminucion);
			  $ld_monto_actualizado = $ld_asignado + $ld_aumento - $ld_disminucion;
			  $ld_saldoxcomprometer = $ld_monto_actualizado - $ld_comprometido - $ld_precomprometido;
			  $ld_por_pagar = $ld_causado - $ld_pagado;				  
					 
			  if(($ls_cmbnivel==1)||($ls_cmbnivel==2)||($ls_cmbnivel==3))
			  {
				  if($li_nivel == $ls_cmbnivel)
				  {
				   $ld_total_asignado += $ld_asignado;
		           $ld_total_aumento  += $ld_aumento;
		           $ld_total_disminucion += $ld_disminucion;
		           $ld_total_monto_actualizado += $ld_monto_actualizado;
		           $ld_total_precomprometido += $ld_precomprometido;
		           $ld_total_comprometido += $ld_comprometido;
		           $ld_total_saldoxcomprometer += $ld_saldoxcomprometer;
		           $ld_total_causado += $ld_causado;
		           $ld_total_pagado += $ld_pagado;
		           $ld_total_por_pagar += $ld_por_pagar;
				  }
			  }
			  else
			  {
			   	  if($ls_status == "C")
				  {
				   $ld_total_asignado += $ld_asignado;
		           $ld_total_aumento  += $ld_aumento;
		           $ld_total_disminucion += $ld_disminucion;
		           $ld_total_monto_actualizado += $ld_monto_actualizado;
		           $ld_total_precomprometido += $ld_precomprometido;
		           $ld_total_comprometido += $ld_comprometido;
		           $ld_total_saldoxcomprometer += $ld_saldoxcomprometer;
		           $ld_total_causado += $ld_causado;
		           $ld_total_pagado += $ld_pagado;
		           $ld_total_por_pagar += $ld_por_pagar;
				  }
			  } 
			  
			  $ld_asignado=number_format($ld_asignado,2,",",".");
			  $ld_aumento=number_format($ld_aumento,2,",",".");
			  $ld_disminucion=number_format($ld_disminucion,2,",",".");
		      $ld_monto_actualizado=number_format($ld_monto_actualizado,2,",",".");
		      $ld_precomprometido=number_format($ld_precomprometido,2,",",".");
		      $ld_comprometido=number_format($ld_comprometido,2,",",".");
		      $ld_saldoxcomprometer=number_format($ld_saldoxcomprometer,2,",",".");
		      $ld_causado=number_format($ld_causado,2,",",".");
		      $ld_pagado=number_format($ld_pagado,2,",",".");
		      $ld_por_pagar=number_format($ld_por_pagar,2,",",".");
					
			  $la_data1[$i]=array('cuenta'=>$ls_spg_cuenta,
								  'denominacion'=>$ls_denominacion,
								  'asignado'=>$ld_asignado,
								  'aumento'=>$ld_aumento,
								  'disminución'=>$ld_disminucion,
								  'montoactualizado'=>$ld_monto_actualizado,
								  'precomprometido'=>$ld_precomprometido,
								  'comprometido'=>$ld_comprometido,
								  'saldoxcomprometer'=>$ld_saldoxcomprometer,
								  'causado'=>$ld_causado,
								  'pagado'=>$ld_pagado,
								  'porpagar'=>$ld_por_pagar);
			
					
			$i++;	  
			$rs_cuentas->MoveNext();
		 }// fin del while estructura
		
					if($total_cuentas>0)
					{ 
						//$io_pdf->ezSetDy(-10);
						//$io_encabezado=$io_pdf->openObject();
						//uf_print_cabecera_estructura($io_encabezado,$ls_codestpro1_fil,$ls_codestpro2_fil,$ls_codestpro3_fil,$ls_codestpro4_fil,$ls_codestpro5_fil,
						//					   $ls_denestpro1_fil,$ls_denestpro2_fil,$ls_denestpro3_fil,$ls_denestpro4_fil,$ls_denestpro5_fil,$io_pdf);
						//$io_cabecera=$io_pdf->openObject();
				        //uf_print_cabecera($io_cabecera,$io_pdf);
						//$io_pdf->ezSetDy(-10);
						//uf_print_cabecera($io_pdf);
					if($i==$total_cuentas)
					{
						
					  $ld_totalgen_asignado += $ld_total_asignado;
					  $ld_totalgen_aumento += $ld_total_aumento;
					  $ld_totalgen_disminucion += $ld_total_disminucion;
					  $ld_totalgen_monto_actualizado += $ld_total_monto_actualizado;
					  $ld_totalgen_precomprometido += $ld_total_precomprometido;
					  $ld_totalgen_comprometido += $ld_total_comprometido;
					  $ld_totalgen_saldoxcomprometer += $ld_total_saldoxcomprometer;
					  $ld_totalgen_causado += $ld_total_causado;
					  $ld_totalgen_pagado += $ld_total_pagado;
					  $ld_totalgen_por_pagar += $ld_total_por_pagar;
					  
					  $ld_total_asignado=number_format($ld_total_asignado,2,",",".");
					  $ld_total_aumento=number_format($ld_total_aumento,2,",",".");
					  $ld_total_disminucion=number_format($ld_total_disminucion,2,",",".");
					  $ld_total_monto_actualizado=number_format($ld_total_monto_actualizado,2,",",".");
		              $ld_total_precomprometido=number_format($ld_total_precomprometido,2,",",".");
		              $ld_total_comprometido=number_format($ld_total_comprometido,2,",",".");
		              $ld_total_saldoxcomprometer=number_format($ld_total_saldoxcomprometer,2,",",".");
		              $ld_total_causado=number_format($ld_total_causado,2,",",".");
		              $ld_total_pagado=number_format($ld_total_pagado,2,",",".");
		              $ld_total_por_pagar=number_format($ld_total_por_pagar,2,",",".");
					  $ls_estructura = "";
			          if($ls_modalidad == 1)
					  {
					   $ls_estructura = trim(substr($ls_codestpro1_fil,-$li_loncodestpro1))."-".trim(substr($ls_codestpro2_fil,-$li_loncodestpro2))."-".trim(substr($ls_codestpro3_fil,-$li_loncodestpro3));
					  }
					  elseif($ls_modalidad == 2)
					  {
					   $ls_estructura = trim(substr($ls_codestpro1_fil,-$li_loncodestpro1))."-".trim(substr($ls_codestpro2_fil,-$li_loncodestpro2))."-".trim(substr($ls_codestpro3_fil,-$li_loncodestpro3))."-".trim(substr($ls_codestpro4_fil,-$li_loncodestpro4))."-".trim(substr($ls_codestpro5_fil,-$li_loncodestpro5));
					  }
					  $la_data_tot[$i]=array('total'=>'<b>TOTAL '.$ls_estructura.'</b>',
										     'asignado'=>$ld_total_asignado,
										     'aumento'=>$ld_total_aumento,
										     'disminución'=>$ld_total_disminucion,
										     'montoactualizado'=>$ld_total_monto_actualizado,
										     'precomprometido'=>$ld_total_precomprometido,
										     'comprometido'=>$ld_total_comprometido,
										     'saldoxcomprometer'=>$ld_total_saldoxcomprometer,
										     'causado'=>$ld_total_causado,
										     'pagado'=>$ld_total_pagado,
										     'porpagar'=>$ld_total_por_pagar);
					}//if
					}
						$io_encabezado=$io_pdf->openObject();
						uf_print_cabecera_estructura($io_encabezado,$ls_codestpro1_fil,$ls_codestpro2_fil,$ls_codestpro3_fil,$ls_codestpro4_fil,$ls_codestpro5_fil,
											          $ls_denestpro1_fil,$ls_denestpro2_fil,$ls_denestpro3_fil,$ls_denestpro4_fil,$ls_denestpro5_fil,$io_pdf);
					    $io_pdf->ezSetCmMargins(6.25,3,3,3);
						$io_cabecera=$io_pdf->openObject();
				        uf_print_cabecera($io_cabecera,$io_pdf);
						uf_print_detalle($la_data1,$io_pdf); // Imprimimos el detalle 
						uf_print_pie_cabecera($la_data_tot,$io_pdf);
						$io_pdf->stopObject($io_encabezado);
						$io_pdf->stopObject($io_cabecera);
						
						unset($la_data1);
						unset($la_data_tot);		
						if($j<$total_estructuras)
						{
						 $io_pdf->ezNewPage();
						}
		 }
	    }
	   $rs_estructuras->MoveNext();
	  }
		 $ld_totalgen_asignado = number_format($ld_totalgen_asignado,2,",",".");
		 $ld_totalgen_aumento = number_format($ld_totalgen_aumento,2,",",".");
		 $ld_totalgen_disminucion = number_format($ld_totalgen_disminucion,2,",",".");
		 $ld_totalgen_monto_actualizado = number_format($ld_totalgen_monto_actualizado,2,",",".");
		 $ld_totalgen_precomprometido = number_format($ld_totalgen_precomprometido,2,",",".");
		 $ld_totalgen_comprometido = number_format($ld_totalgen_comprometido,2,",",".");
		 $ld_totalgen_saldoxcomprometer = number_format($ld_totalgen_saldoxcomprometer,2,",",".");
		 $ld_totalgen_causado = number_format($ld_totalgen_causado,2,",",".");
		 $ld_totalgen_pagado = number_format($ld_totalgen_pagado,2,",",".");
		 $ld_totalgen_por_pagar = number_format($ld_totalgen_por_pagar,2,",",".");
		 
		 $la_data_tot2[]=array('total'=>'<b>TOTAL GENERAL</b>',
							   'asignado'=>$ld_totalgen_asignado,
							   'aumento'=>$ld_totalgen_aumento,
							   'disminución'=>$ld_totalgen_disminucion,
							   'montoactualizado'=>$ld_totalgen_monto_actualizado,
							   'precomprometido'=>$ld_totalgen_precomprometido,
							   'comprometido'=>$ld_totalgen_comprometido,
							   'saldoxcomprometer'=>$ld_totalgen_saldoxcomprometer,
							   'causado'=>$ld_totalgen_causado,
							   'pagado'=>$ld_totalgen_pagado,
							   'porpagar'=>$ld_totalgen_por_pagar);
		
		uf_print_pie_cabecera($la_data_tot2,$io_pdf);
		$io_pdf->ezStopPageNumbers(1,1);
		if (isset($d) && $d)
		{
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
	 }
	}
		
	unset($io_pdf);
	unset($io_report);
	unset($io_funciones);
?> 