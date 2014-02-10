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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo,$ai_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Generó el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_relacionvacaciones.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hrelacionvacaciones.php",$ls_descripcion,$ls_codnom);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_desnom,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$as_titulo1=$_SESSION["la_empresa"]["nombre"]." ( ".$_SESSION["la_empresa"]["titulo"]." )";
		$tm=40;
		$io_pdf->addText($tm,755,9,'<b>'.$as_titulo1.'</b>'); // Agregar el título
		$as_titulo1="GERENCIA DE RECURSOS HUMANOS - DIVISIÓN DE REGISTRO Y CONTROL ";
		$io_pdf->addText($tm,745,9,'<b>'.$as_titulo1.'</b>'); // Agregar el título
		$as_titulo1="SISTEMA DE VACACIONES  - ".$as_desnom;
		$io_pdf->addText($tm,735,9,'<b>'.$as_titulo1.'</b>'); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(14,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,710,14,$as_titulo); // Agregar el título
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_sueint,$as_cedper,$ai_aniovacacion,$as_periodovaca,$as_nomper,$as_desded,$as_descar,$as_desuniadm,
							   $ad_fecingper,$as_codvac,$ai_anoservpreper,$ai_diavac,$ai_diaadivac,$as_lapsovaca,$ad_fecreivac,
							   $as_obsvac,$ai_quinquenio,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_cedper // Cédula del personal 
		//	   			   as_nomcon // Nombre del personal
		//	    		   ad_fecingper // fecha de ingreso del personal
		//	    		   as_desuniadm // Descripción de la unidad adinistrativa
		//	    		   ai_sueintvac // sueldo integral de vacaciones
		//	    		   ad_fecdisvac // fecha de disfrute de las vacaciones
		//	    		   ad_fecreivac // fecha de reintegro de las vacaciones
		//	    		   ai_diavac // días hábiles de vacaciones
		//	    		   as_codvac // código de vacaciones
		//	    		   as_descar // descripción del cargo
		//	    		   ai_sueintdia // Sueldo integral diario
		//                 as_sueint // denominación de sueldo integral
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($as_sueint=="")
		{
			$titulo1="Sueldo Integral de Vacaciones";
			$titulo2="Sueldo Diario Integral";
		}
		else
		{
			$titulo1=$as_sueint." de Vacaciones";
			$titulo2=$as_sueint." Diario";
		}
		$la_data[1]=array('campo1'=>'<b>Cédula de Identidad</b>','campo2'=>'<b>Años de Vacaciones</b>','campo3'=>'<b>Período de Vacaciones a Disfrutar</b>',
						  'campo4'=>'<b>Elaborado Por</b>','campo5'=>'<b>Fecha de Elaboración</b>');
		$la_data[2]=array('campo1'=>$as_cedper,'campo2'=>$ai_aniovacacion,'campo3'=>$as_periodovaca,
						  'campo4'=>$_SESSION['la_apeusu'].' '.$_SESSION['la_nomusu'],'campo5'=>date("d/m/Y"));
		$la_columnas=array('campo1'=>'','campo2'=>'','campo3'=>'','campo4'=>'','campo5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'center','width'=>80),
									   'campo3'=>array('justification'=>'center','width'=>100),
									   'campo4'=>array('justification'=>'center','width'=>210),
									   'campo5'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('campo1'=>'<b>Apellidos y Nombres</b>','campo2'=>'<b>Tipo de Funcionario</b>');
		$la_data[2]=array('campo1'=>$as_nomper,'campo2'=>$as_desded);
		$la_columnas=array('campo1'=>'','campo2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados,
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'left','width'=>400), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'left','width'=>150))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('campo1'=>'<b>Denominación del Cargo</b>','campo2'=>'<b>Ubicación Administrativa</b>');
		$la_data[2]=array('campo1'=>$as_descar,'campo2'=>$as_desuniadm);
		$la_columnas=array('campo1'=>'','campo2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados,
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'left','width'=>275), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'left','width'=>275))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('campo1'=>'<b>Fecha de Ingreso</b>','campo2'=>'<b>Antiguedad de Servicio</b>','campo3'=>'<b>Años Previos Adm. Pública</b>',
						  'campo4'=>'<b>Antiguedad de Servicio + Años Previos Adm. Pública</b>','campo5'=>'<b>Quinquenio</b>');
		$la_data[2]=array('campo1'=>$ad_fecingper,'campo2'=>$as_codvac." Años",'campo3'=>$ai_anoservpreper." Años",
						  'campo4'=>($as_codvac+$ai_anoservpreper)." Años",'campo5'=>$ai_quinquenio);
		$la_columnas=array('campo1'=>'','campo2'=>'','campo3'=>'','campo4'=>'','campo5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'center','width'=>80),
									   'campo3'=>array('justification'=>'center','width'=>100),
									   'campo4'=>array('justification'=>'center','width'=>210),
									   'campo5'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('campo1'=>'<b>Cant. Días Vacaciones</b>','campo2'=>'<b>Días Adicionales</b>','campo3'=>'<b>Total Días Hábiles</b>',
						  'campo4'=>'<b>Lapso de Vacaciones</b>','campo5'=>'<b>Fecha de Reingreso</b>');
		$la_data[2]=array('campo1'=>$ai_diavac." Hábiles",'campo2'=>$ai_diaadivac." Hábiles",'campo3'=>($ai_diavac+$ai_diaadivac)." Hábiles",
						  'campo4'=>$as_lapsovaca,'campo5'=>$ad_fecreivac);
		$la_columnas=array('campo1'=>'','campo2'=>'','campo3'=>'','campo4'=>'','campo5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'center','width'=>80),
									   'campo3'=>array('justification'=>'center','width'=>100),
									   'campo4'=>array('justification'=>'center','width'=>210),
									   'campo5'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('campo1'=>'<b>Observaciones</b>');
		$la_data[2]=array('campo1'=>$as_obsvac);
		$la_columnas=array('campo1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados,
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-10);
		$la_data[1]=array('campo1'=>'<b>Beneficiario</b>','campo2'=>'<b>Elaborado y Revisado por</b>','campo3'=>'<b>Procesado por</b>');
		$la_data[2]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[3]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[4]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[5]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[6]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[7]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[8]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_columnas=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'center','width'=>183), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'center','width'=>183),
									   'campo3'=>array('justification'=>'center','width'=>184))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('campo1'=>'<b>'.$as_nomper.'</b>','campo2'=>'','campo3'=>'<b>DEPARTAMENTO DE NÓMINA</b>');
		$la_columnas=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'center','width'=>183), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'center','width'=>183),
									   'campo3'=>array('justification'=>'center','width'=>184))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('campo1'=>'<b>Aprobado por</b>','campo2'=>'<b>Autorizado por</b>','campo3'=>'<b>Aprobado por</b>');
		$la_data[2]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[3]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[4]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[5]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[6]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[7]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_data[8]=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_columnas=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'center','width'=>183), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'center','width'=>183),
									   'campo3'=>array('justification'=>'center','width'=>184))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('campo1'=>'<b>ASISTENTE / ANALISTA / W.G </b>','campo2'=>'<b>GERENTE DE RECURSOS HUMANOS</b>','campo3'=>'<b>PRESIDENTE / GERENTE / DIRECTOR</b>');
		$la_columnas=array('campo1'=>'','campo2'=>'','campo3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'center','width'=>183), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'center','width'=>183),
									   'campo3'=>array('justification'=>'center','width'=>184))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('campo1'=>'Original: Funcionario','campo2'=>'LAS VACACIONES NO SON ACUMULATIVAS Y DEBEN DISFRUTARSE EN LA ');
		$la_data[2]=array('campo1'=>'C.C.: Expediente Personal','campo2'=>'FECHA QUE APARECEN EN ESTA AUTORIZACIÓN POR EL LAPSO AQUI');
		$la_data[3]=array('campo1'=>'C.C.: Gerencia ó Dirección','campo2'=>'ESTABLECIDO');
		$la_data[4]=array('campo1'=>'C.C.: División','campo2'=>'');
		$la_columnas=array('campo1'=>'','campo2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'left','width'=>150), // Justificación y ancho de la columna
									   'campo2'=>array('justification'=>'center','width'=>400))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('campo1'=>'*                                                                                                  SE LES AGRADECE DEVOLVER UNA DE LA COPIA SELLADA Y FIRMADA POR EL BENEFICIARIO ');
		$la_columnas=array('campo1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('campo1'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	$ls_tiporeporte="0";
	$ls_bolivares="";
	if (array_key_exists("tiporeporte",$_GET))
	{
		$ls_tiporeporte=$_GET["tiporeporte"];
	}
	switch($ls_tiporeporte)
	{
		case "0":
			if($_SESSION["la_nomina"]["tiponomina"]=="NORMAL")
			{
				require_once("sigesp_sno_class_report.php");
				$io_report=new sigesp_sno_class_report();
				$li_tipo=1;
			}
			if($_SESSION["la_nomina"]["tiponomina"]=="HISTORICA")
			{
				require_once("sigesp_sno_class_report_historico.php");
				$io_report=new sigesp_sno_class_report_historico();
				$li_tipo=2;
			}	
			$ls_bolivares ="Bs.";
			break;

		case "1":
			if($_SESSION["la_nomina"]["tiponomina"]=="NORMAL")
			{
				require_once("sigesp_sno_class_reportbsf.php");
				$io_report=new sigesp_sno_class_reportbsf();
				$li_tipo=1;
			}
			if($_SESSION["la_nomina"]["tiponomina"]=="HISTORICA")
			{
				require_once("sigesp_sno_class_report_historicobsf.php");
				$io_report=new sigesp_sno_class_report_historicobsf();
				$li_tipo=2;
			}	
			$ls_bolivares ="Bs.F.";
			break;
	}
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
	$ld_fechasper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
	$ls_titulo="<b><i>AUTORIZACIÓN DE VACACIONES</i></b>";
	$ls_periodo="<b>Período Nro ".$ls_peractnom.", ".$ld_fecdesper." - ".$ld_fechasper."</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codper=$io_fun_nomina->uf_obtenervalor_get("codper","");
	$ls_codvac=$io_fun_nomina->uf_obtenervalor_get("codvac","");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_tituloconcepto=$io_fun_nomina->uf_obtenervalor_get("tituloconcepto","");
	$ls_sueint=$io_fun_nomina->uf_obtenervalor_get("sueint","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_relacionvacacion_personal($ls_codper,$ls_codvac,$ls_conceptocero,$rs_data); // Cargar el DS con los datos de la cabecera del reporte
	}
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		error_reporting(E_ALL);
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.1,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_desnom,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		
		while((!$rs_data->EOF)&&($lb_valido))
		{
			$ls_codper=$rs_data->fields["codper"];
			$ls_cedper=$rs_data->fields["cedper"];
			$li_aniovacacion=substr($rs_data->fields["fecvenvac"],0,4);
			$ls_periodovaca=($li_aniovacacion-1)." - ".substr($rs_data->fields["fecvenvac"],0,4);
			$ls_nomper=$rs_data->fields["apeper"].", ".$rs_data->fields["nomper"];
			$ls_desded=$rs_data->fields["desded"];
			$ls_desuniadm=$rs_data->fields["desuniadm"];
			$ls_descar=$rs_data->fields["descar"];			
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecingper"]);
			$ls_codvac=$rs_data->fields["codvac"];
			$li_anoservpreper=$rs_data->fields["anoservpreper"];
			$ld_fecdisvac=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecdisvac"]);
			$ld_fechasvac=(substr($rs_data->fields["fecreivac"],8,2)-1);
			$ld_fechasvac=substr($rs_data->fields["fecreivac"],0,8).$ld_fechasvac;
			$ld_fechasvac=$io_funciones->uf_convertirfecmostrar($ld_fechasvac);
			$ls_lapsovaca="Desde: ".$ld_fecdisvac." Hasta: ".$ld_fechasvac;
			$ld_fecreivac=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecreivac"]);
			$li_diavac=$rs_data->fields["diavac"];
			$li_diaadivac=$rs_data->fields["diaadivac"];
			$ls_obsvac=substr($rs_data->fields["obsvac"],0,1500);
			$ls_obsvac=str_pad($ls_obsvac,1500," ");
			$li_quinquenio=(($ls_codvac-1)/5)+1;
			uf_print_cabecera($ls_sueint,$ls_cedper,$li_aniovacacion,$ls_periodovaca,$ls_nomper,$ls_desded,$ls_descar,$ls_desuniadm,
							  $ld_fecingper,$ls_codvac,$li_anoservpreper,$li_diavac,$li_diaadivac,$ls_lapsovaca,$ld_fecreivac,$ls_obsvac,
							  $li_quinquenio,$io_pdf); 
			$rs_data->MoveNext();
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