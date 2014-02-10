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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/05/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_personal_rac_rec.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_codorgvipladin,$as_grupovipladin,$as_vigenciavipladin,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/05/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,755,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,540,11,$as_titulo); // Agregar el título
		$io_pdf->addText(50,510,11,"Fecha de Vigencia:"); // Agregar el título
		$io_pdf->addText(150,510,11,$as_vigenciavipladin); // Agregar el título
		$io_pdf->addText(50,495,11,"Organismo:"); // Agregar el título
		$io_pdf->addText(150,495,11,$as_codorgvipladin); // Agregar el título
		$io_pdf->addText(50,480,11,"Grupo:"); // Agregar el título
		$io_pdf->addText(150,480,11,$as_grupovipladin); // Agregar el título
		$io_pdf->addText(712,560,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(718,553,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_minorguniadm,$as_ofiuniadm,$as_uniuniadm,$as_depuniadm,$as_prouniadm,$as_desuniadm,
								  $as_distritovipladin,$as_municipiovipladin,&$io_cabecera,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/05/2010 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->saveState();
		$io_pdf->addText(50,460,11,"Ub. Adminis:"); // Agregar el título
		$io_pdf->addText(150,460,11,$as_minorguniadm.$as_ofiuniadm.$as_uniuniadm.$as_depuniadm.$as_prouniadm); // Agregar el título
		$io_pdf->addText(50,445,11,"Descripción:"); // Agregar el título
		$io_pdf->addText(150,445,11,$as_desuniadm); // Agregar el título
		$io_pdf->addText(500,460,11,"Municipio:"); // Agregar el título
		$io_pdf->addText(600,460,11,$as_municipiovipladin); // Agregar el título
		$io_pdf->addText(500,445,11,"Distrito:"); // Agregar el título
		$io_pdf->addText(600,445,11,$as_distritovipladin); // Agregar el título
		$io_pdf->ezSety(440);
        $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(50,425,700,$io_pdf->getFontHeight(10));
        $io_pdf->setColor(0,0,0);
		$la_data[1]=array('nomina'=>'<b>Nómina</b>',
						  'cedula'=>'<b>Cédula</b>',
						  'nombre'=>'<b>Apellidos y Nombres</b>',
						  'cargo'=>'<b>Denominación del Cargo</b>',
						  'grado'=>'<b>Grado</b>',
						  'clase'=>'<b>Clase</b>',
						  'tipo'=>'<b>Tipo de Cargo</b>',
						  'basico'=>'<b>Básico</b>',
						  'compensacion'=>'<b>Compensación</b>',
						  'total'=>'<b>Total</b>');
		$la_columna=array('nomina'=>'',
						  'cedula'=>'',
						  'nombre'=>'',
						  'cargo'=>'',
						  'grado'=>'',
						  'clase'=>'',
						  'tipo'=>'',
						  'basico'=>'',
						  'compensacion'=>'',
						  'total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'xPos' => 405,
						 'cols'=>array('nomina'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>120), // Justificación y ancho de la columna
						 			   'cargo'=>array('justification'=>'center','width'=>120), // Justificación y ancho de la columna
						 			   'grado'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'clase'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'tipo'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'basico'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'compensacion'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'total'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/05/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_columna=array('nomina'=>'',
						  'cedula'=>'',
						  'nombre'=>'',
						  'cargo'=>'',
						  'grado'=>'',
						  'clase'=>'',
						  'tipo'=>'',
						  'basico'=>'',
						  'compensacion'=>'',
						  'total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'xPos' => 405,
						 'cols'=>array('nomina'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>120), // Justificación y ancho de la columna
						 			   'cargo'=>array('justification'=>'left','width'=>120), // Justificación y ancho de la columna
						 			   'grado'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'clase'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'tipo'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'basico'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
						 			   'compensacion'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
						 			   'total'=>array('justification'=>'right','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($ai_totalbasico,$ai_totalcompensacion,$ai_totalgeneral,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		   Access: private 
		//	    Arguments: ai_tottra // Total de Trabajadores
		//	   			   ai_montot // Monto total por concepto
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera por conceptos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data[1]=array('total1'=>'',
						  'total2'=>'',
						  'total3'=>'');
		$la_data[2]=array('total1'=>'',
						  'total2'=>'',
						  'total3'=>'');
		$la_columna=array('total1'=>'','total2'=>'','total3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('total1'=>array('justification'=>'right','width'=>200), // Justificación y ancho de la columna
						 			   'total2'=>array('justification'=>'right','width'=>200), // Justificación y ancho de la columna
						 			   'total3'=>array('justification'=>'right','width'=>200))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		$la_data[1]=array('total1'=>'<b>Total Básico:</b>'.'               '.$ai_totalbasico.'',
						  'total2'=>'<b>Total Compensación:</b>'.'               '.$ai_totalcompensacion.'',
						  'total3'=>'<b>Total General:</b>'.'               '.$ai_totalgeneral.'');
		$la_columna=array('total1'=>'','total2'=>'','total3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('total1'=>array('justification'=>'right','width'=>200), // Justificación y ancho de la columna
						 			   'total2'=>array('justification'=>'right','width'=>200), // Justificación y ancho de la columna
						 			   'total3'=>array('justification'=>'right','width'=>200))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>REGISTRO DE ASIGNACIÓN DE CARGOS (RAC)</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_compensacion=$io_fun_nomina->uf_obtenervalor_get("compensacion","");
	$ls_anio=$io_fun_nomina->uf_obtenervalor_get("anio","");	
	$ls_mes=$io_fun_nomina->uf_obtenervalor_get("mes","");	
	$ls_codorgvipladin=trim($io_report->uf_select_config("SNO","CONFIG","COD_ORGANISMO_VIPLADIN","","C"));
	$ls_grupovipladin=trim($io_report->uf_select_config("SNO","CONFIG","GRUPO_VIPLADIN","","C"));
	$ls_codubivipladin=trim($io_report->uf_select_config("SNO","CONFIG","COD_UBICACION_VIPLADIN","","C"));
	$ls_distritovipladin=trim($io_report->uf_select_config("SNO","CONFIG","DISTRITO_VIPLADIN","","C"));
	$ls_municipiovipladin=trim($io_report->uf_select_config("SNO","CONFIG","MUNICIPIO_VIPLADIN","","C"));
	$ls_vigenciavipladin=trim($io_report->uf_select_config("SNO","CONFIG","VIGENCIA_VIPLADIN","","C"));
	$ls_codorgvipladin=$ls_codorgvipladin."  ".$_SESSION["la_empresa"]["nombre"];
	$ls_compensacion="'".$ls_compensacion."'";
	$ls_compensacion=str_replace("-","','",$ls_compensacion);
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_listadopersonal_rac($ls_codnomdes,$ls_codnomhas,$ls_codperdes,$ls_codperhas,$ls_anio,$ls_mes,'UNIDAD'); // Obtenemos el detalle del reporte
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
		$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(6.55,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_codorgvipladin,$ls_grupovipladin,$ls_vigenciavipladin,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(750,50,10,'','',1); // Insertar el número de página
		$ls_minorguniadm_ant="";
		$ls_ofiuniadm_ant="";
		$ls_uniuniadm_ant="";
		$ls_depuniadm_ant="";
		$ls_prouniadm_ant="";
		$li_i=0;
		$li_totalbasico=0;
		$li_totalcompensacion=0;
		$li_totalgeneral=0;
		while((!$io_report->rs_data->EOF)&&($lb_valido))
		{
			$ls_codnom=$io_report->rs_data->fields["codnom"];
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_minorguniadm=$io_report->rs_data->fields["minorguniadm"];
			$ls_ofiuniadm=$io_report->rs_data->fields["ofiuniadm"];
			$ls_uniuniadm=$io_report->rs_data->fields["uniuniadm"];
			$ls_depuniadm=$io_report->rs_data->fields["depuniadm"];
			$ls_prouniadm=$io_report->rs_data->fields["prouniadm"];
			$ls_desuniadm=$io_report->rs_data->fields["desuniadm"];
			$ls_codunirac=$io_report->rs_data->fields["codunirac"];
			$ls_cedula=$io_report->rs_data->fields["cedula"];
			$ls_nombre=$io_report->rs_data->fields["nombre"];
			$ls_denasicar=$io_report->rs_data->fields["denasicar"];
			$ls_codgra=$io_report->rs_data->fields["codgra"];
			$ls_claasicar=$io_report->rs_data->fields["claasicar"];
			$ls_codtipper=$io_report->rs_data->fields["codtipper"];
			$li_monsalgra=$io_report->rs_data->fields["monsalgra"];
			$li_moncomgra=$io_report->rs_data->fields["moncomgra"];
			if($li_moncomgra<=0)
			{
				$lb_valido=$io_report->uf_buscar_valor_concepto_personal($ls_codnom,$ls_codper,$ls_anio,$ls_mes,$ls_compensacion,&$li_moncomgra);
			}
			$li_total=$li_monsalgra+$li_moncomgra;
			$li_totalbasico=$li_totalbasico+$li_monsalgra;
			$li_totalcompensacion=$li_totalcompensacion+$li_moncomgra;
			$li_totalgeneral=$li_totalgeneral+$li_total;
			$li_monsalgra=number_format($li_monsalgra,2,",",".");
			$li_moncomgra=number_format($li_moncomgra,2,",",".");
			$li_total=number_format($li_total,2,",",".");
			switch(substr($ls_codtipper,2,2))
			{
				case "02":
					$ls_codtipper='PROFESIONAL';
				break;
				case "03":
					$ls_codtipper='ADMINISTRATIVO';
				break;
			}
			if($ls_minorguniadm_ant.$ls_ofiuniadm_ant.$ls_uniuniadm_ant.$ls_depuniadm_ant.$ls_prouniadm_ant!=$ls_minorguniadm.$ls_ofiuniadm.$ls_uniuniadm.$ls_depuniadm.$ls_prouniadm)
			{
				$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
				uf_print_cabecera($ls_minorguniadm,$ls_ofiuniadm,$ls_uniuniadm,$ls_depuniadm,$ls_prouniadm,$ls_desuniadm,
								  $ls_distritovipladin,$ls_municipiovipladin,$io_cabecera,$io_pdf); // Imprimimos la cabecera del registro
				$ls_minorguniadm_ant=$ls_minorguniadm;
				$ls_ofiuniadm_ant=$ls_ofiuniadm;
				$ls_uniuniadm_ant=$ls_uniuniadm;
				$ls_depuniadm_ant=$ls_depuniadm;
				$ls_prouniadm_ant=$ls_prouniadm;
			}
			$li_i++;
			$la_data[$li_i]=array('nomina'=>$ls_codunirac,'cedula'=>$ls_cedula,'nombre'=>$ls_nombre,'cargo'=>$ls_denasicar,'grado'=>$ls_codgra,
						  		  'clase'=>$ls_claasicar,'tipo'=>$ls_codtipper,'basico'=>$li_monsalgra,'compensacion'=>$li_moncomgra,'total'=>$li_total);
			$io_report->rs_data->MoveNext();
			$ls_minorguniadm=$io_report->rs_data->fields["minorguniadm"];
			$ls_ofiuniadm=$io_report->rs_data->fields["ofiuniadm"];
			$ls_uniuniadm=$io_report->rs_data->fields["uniuniadm"];
			$ls_depuniadm=$io_report->rs_data->fields["depuniadm"];
			$ls_prouniadm=$io_report->rs_data->fields["prouniadm"];
			$ls_desuniadm=$io_report->rs_data->fields["desuniadm"];
			if($ls_minorguniadm_ant.$ls_ofiuniadm_ant.$ls_uniuniadm_ant.$ls_depuniadm_ant.$ls_prouniadm_ant!=$ls_minorguniadm.$ls_ofiuniadm.$ls_uniuniadm.$ls_depuniadm.$ls_prouniadm)
			{
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				$li_totalbasico=number_format($li_totalbasico,2,",",".");
				$li_totalcompensacion=number_format($li_totalcompensacion,2,",",".");
				$li_totalgeneral=number_format($li_totalgeneral,2,",",".");
 				uf_print_piecabecera($li_totalbasico,$li_totalcompensacion,$li_totalgeneral,&$io_pdf);
				$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera
				if($ls_minorguniadm!="")
				{
					$io_pdf->ezNewPage(); // Insertar una nueva página
				}
				unset($io_cabecera);
				unset($la_data);
				$li_i=0;
				$li_totalbasico=0;
				$li_totalcompensacion=0;
				$li_totalgeneral=0;
			}			
		}
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 