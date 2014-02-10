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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_periodo,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2010
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,715,11,$as_periodo); // Agregar el título		
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_nomina($as_desnom, &$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_nomban // Nombre del Banco
		//	    		   io_cabecera // Objeto cabecera
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera por banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2010 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $io_pdf->ezSetDy(-7);
		$la_dato_nomina[1]=array('nombre'=>"<b>PERSONAL DE ".$as_desnom."</b>");
		$la_columna=array('nombre'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_dato_nomina,$la_columna,'',$la_config);		
	}// uf_print_cabecera_nomina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($as_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle por banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_columna=array('unidad'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('unidad'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la 
		$io_pdf->ezTable($as_data,$la_columna,'',$la_config);		
	}// end function uf_print_detalle
	///---------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_personal($as_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_personal
		//		   Access: private 
		//	    Arguments: as_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle por banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_titulos[0]=array('cedula'=>'CÉDULA','nombres'=>'NOMBRES','cargo'=>'CARGO','servicio'=>'TIEMPO SERVICIO','ingreso'=>'FECHA INGRESO');
		$la_columna=array('cedula'=>'','nombres'=>'','cargo'=>'','servicio'=>'','ingreso'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas						 
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('cedula'=>array('justification'=>'center','width'=>50),
									   'nombres'=>array('justification'=>'center','width'=>200),
									   'cargo'=>array('justification'=>'center','width'=>200),
									   'servicio'=>array('justification'=>'center','width'=>50),
									   'ingreso'=>array('justification'=>'center','width'=>50))); // Justificación y ancho de la 
		$io_pdf->ezTable($la_titulos,$la_columna,'',$la_config);		

		$la_columna=array('cedula'=>'','nombres'=>'','cargo'=>'','servicio'=>'','ingreso'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('cedula'=>array('justification'=>'center','width'=>50),
									   'nombres'=>array('justification'=>'left','width'=>200),
									   'cargo'=>array('justification'=>'left','width'=>200),
									   'servicio'=>array('justification'=>'center','width'=>50),
									   'ingreso'=>array('justification'=>'center','width'=>50))); // Justificación y ancho de la 
		$io_pdf->ezTable($as_data,$la_columna,'',$la_config);		
	}// end function uf_print_detalle_personal
	///---------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_conceptos($as_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_conceptos
		//		   Access: private 
		//	    Arguments: as_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle por banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_titulos[0]=array('concepto'=>'CONCEPTOS','monto'=>'MONTO');
		$la_columna=array('concepto'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('concepto'=>array('justification'=>'left','width'=>400),
									   'monto'=>array('justification'=>'center','width'=>50))); // Justificación y ancho de la 
		$io_pdf->ezTable($la_titulos,$la_columna,'',$la_config);		

		$la_columna=array('concepto'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('concepto'=>array('justification'=>'left','width'=>400),
									   'monto'=>array('justification'=>'right','width'=>50))); // Justificación y ancho de la 
		$io_pdf->ezTable($as_data,$la_columna,'',$la_config);		
	}// end function uf_print_detalle_conceptos
	///---------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_totales_unidad($as_unidad,$ai_personal,$ai_monto,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_totales_unidad
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle por banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data_total[1]=array('total'=>'<b>TOTAL POR UNIDAD '.$as_unidad.'</b>',
								'total1'=>'PERSONAL '.$ai_personal,
								'total2'=>'MONTO '.$ai_monto);
		$la_columna=array('total'=>'',
						  'total1'=>'',
						  'total2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('total'=>array('justification'=>'center','width'=>350),
									   'total1'=>array('justification'=>'center','width'=>60),
									   'total2'=>array('justification'=>'right','width'=>140))); // Justificación y ancho de la 
		$io_pdf->ezTable($la_data_total,$la_columna,'',$la_config);		
	}
	//--------------------------------------------------------------------------------------------------------------------------------
     
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_totales($ai_personal,$ai_monto,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_totales_unidad
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle por banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data_total[0]=array('total'=>'',
								'total1'=>'',
								'total2'=>'');
		$la_data_total[1]=array('total'=>'',
								'total1'=>'',
								'total2'=>'');
		$la_data_total[2]=array('total'=>'',
								'total1'=>'',
								'total2'=>'');
		$la_data_total[3]=array('total'=>'<b>TOTAL GENERAL</b>',
								'total1'=>'PERSONAL '.$ai_personal,
								'total2'=>'MONTO '.$ai_monto);
		$la_columna=array('total'=>'',
						  'total1'=>'',
						  'total2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('total'=>array('justification'=>'center','width'=>300),
									   'total1'=>array('justification'=>'center','width'=>110),
									   'total2'=>array('justification'=>'right','width'=>140))); // Justificación y ancho de la 
		$io_pdf->ezTable($la_data_total,$la_columna,'',$la_config);		
	}
	//--------------------------------------------------------------------------------------------------------------------------------

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
	$ls_titulo="<b>Resumen de Pagos por Unidad Administrativa Detallado</b>";
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
		error_reporting(E_ALL);
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página	
		$li_totalpersonal=0;
		$li_totalmonto=0;
		while ((!$io_report->rs_data->EOF)&&($lb_valido))
		{  
			$ls_codnom=$io_report->rs_data->fields["codnom"];
			$ls_desnom=$io_report->rs_data->fields["desnom"];		  
			$ls_racnom=$io_report->rs_data->fields["racnom"];		  
			uf_print_cabecera_nomina($ls_desnom, &$io_pdf);	
			$lb_valido=$io_report->uf_pagos_unidad($ls_codnom, $ls_codperides, $ls_codperhas,$ls_unidaddes,$ls_unidadhas,$ld_aniodesde,$ld_aniohasta,
												   $ls_coddeddes,$ls_coddedhas,$ls_codtipperdes,$ls_codtipperhas,$ls_orden);
			$li_j=0;
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
				$ls_data[$li_j]=array('unidad'=>$ls_unidad);
				$lb_valido=$io_report->uf_pagos_unidad_detallado($ls_codnom,$ls_codperides,$ls_codperhas,$ls_uni1,$ls_uni2,$ls_uni3,$ls_uni4,$ls_uni5,
																 $ls_conceptos,$ld_aniodesde,$ld_aniohasta,$ls_coddeddes,$ls_coddedhas,$ls_codtipperdes,$ls_codtipperhas);				
				$li_personal=0;
				if($li_cantidad>0)
				{
					uf_print_detalle($ls_data,&$io_pdf);
				}
				$li_total_unidad=0;
				$li_cantidad=0;
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
					$ls_data_personal[$li_personal]=array('nombres'=>$ls_nomper,'cedula'=>$ls_cedper,'servicio'=>$li_tiemposervicio,'ingreso'=>$ld_fecingper,'cargo'=>$ls_cargo);
					$lb_valido=$io_report->uf_pagos_unidad_conceptos($ls_codnom,$ls_codperides,$ls_codperhas,$ls_conceptos,$ls_codper,$ld_aniodesde,$ld_aniohasta);				
					$li_conceptos=0;
					$li_total_conceptos=0;
					while ((!$io_report->rs_detalle3->EOF)&&($lb_valido))
					{
						$ls_codconc=$io_report->rs_detalle3->fields["codconc"];
						$ls_nomcon=$io_report->rs_detalle3->fields["nomcon"];
						$li_valsal=number_format($io_report->rs_detalle3->fields["valsal"],2,",",".");
						$li_total_conceptos=$li_total_conceptos+$io_report->rs_detalle3->fields["valsal"];
						$ls_data_conceptos[$li_conceptos]=array('concepto'=>$ls_codconc."  -  ".$ls_nomcon,'monto'=>$li_valsal);
						$li_conceptos++;
						$io_report->rs_detalle3->MoveNext();			      
					}
					if($li_conceptos>0)
					{
						$li_total_unidad=$li_total_unidad+$li_total_conceptos;
						$li_total_conceptos=number_format($li_total_conceptos,2,",",".");
						$ls_data_conceptos[$li_conceptos]=array('concepto'=>'																						TOTAL','monto'=>$li_total_conceptos);
						uf_print_detalle_personal($ls_data_personal,&$io_pdf);
						uf_print_detalle_conceptos($ls_data_conceptos,&$io_pdf);
						unset($ls_data_personal);
						unset($ls_data_conceptos);
					}
					$io_report->rs_detalle2->MoveNext();			      
				}
				if($li_cantidad>0)
				{
					$li_totalpersonal=$li_totalpersonal+$li_cantidad;
					$li_totalmonto=$li_totalmonto+$li_total_unidad;
					$li_total_unidad=number_format($li_total_unidad,2,",",".");
					uf_totales_unidad($ls_unidad,$li_cantidad,$li_total_unidad,$io_pdf);
				}
				unset($ls_data);		      
				$io_report->rs_detalle->MoveNext();	
			}
			$io_report->rs_data->MoveNext();	  
		}
		if($li_totalpersonal>0)
		{
			$li_totalmonto=number_format($li_totalmonto,2,",",".");
			uf_totales($li_totalpersonal,$li_totalmonto,$io_pdf);
		}

		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 