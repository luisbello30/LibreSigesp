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
		// Fecha Creación: 07/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Generó el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_pagonominaunidadadmin.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hpagonominaunidadadmin.php",$ls_descripcion,$ls_codnom);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
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
		// Fecha Creación: 07/08/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addText(50,750,9,$_SESSION["la_empresa"]["nombre"]); // Agregar el título
		$io_pdf->addText(50,740,9,"GERENCIA DE RECURSOS HUMANOS"); // Agregar el título
		$io_pdf->addText(50,730,9,"DIVISIÓN DE REGISTRO Y CONTROL"); // Agregar el título
		$io_pdf->addText(50,720,9,"DEPARTAMENTO DE NÓMINA"); // Agregar el título
		$io_pdf->addText(50,700,9,$as_titulo); // Agregar el título
		$io_pdf->addText(50,690,9,$as_periodo); // Agregar el título
		$io_pdf->addText(512,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(518,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_minorguniadm,$as_ofiuniadm,$as_uniuniadm,$as_depuniadm,$as_prouniadm,$as_desuniadm,
							   &$io_cabecera,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_minorguniadm // Código de la unidad
		//	   			   as_ofiuniadm // Código de la unidad
		//	   			   as_uniuniadm // Código de la unidad
		//	   			   as_depuniadm // Código de la unidad
		//	   			   as_prouniadm // Código de la unidad
		//	   			   as_desuniadm // Descripción de la unidad
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->saveState();
       	$io_pdf->addText(50,670,9,'<b>Unidad Administrativa</b> '.$as_minorguniadm.'-'.$as_ofiuniadm.'-'.$as_uniuniadm.'-'.$as_depuniadm.'-'.$as_prouniadm.'  '.$as_desuniadm.''); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_personal($as_codper,$as_cedper,$as_apenomper,$as_descar,$as_desuniadm,$ad_fecingper,$as_codcueban,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_cedper // cédula del personal
		//	    		   as_apenomper // apellidos y nombre del personal
		//	    		   as_descar // descripción del cargo
		//	    		   as_desuniadm // descripción de la unidad administrativa
		//	    		   ad_fecingper // fecha de ingreso
		//	    		   as_codcueban // código de lla cuenta bancaria
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('codigo'=>'<b>CÓDIGO</b>','cargo'=>'<b>DENOMINACIÓN DEL CARGO</b>','nombre'=>'<b>APELLIDOS Y NOMBRE</b>','cedula'=>'<b>CÉDULA</b>','fecha'=>'<b>FECHA DE INGRESO</b>'));		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'cargo'=>array('justification'=>'center','width'=>160), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>160), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50),// Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,'','',$la_config);	

		$la_data=array(array('codigo'=>$as_codper,'cargo'=>$as_descar,'nombre'=>$as_apenomper,'cedula'=>$as_cedper,'fecha'=>$ad_fecingper));		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'cargo'=>array('justification'=>'left','width'=>160), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>160), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50),// Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,'','',$la_config);	
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-2);
		$la_columnas=array('codigo'=>'<b>CÓDIGO</b>',
						   'nombre'=>'<b>               CONCEPTO</b>',
						   'asignacion'=>'<b>ASIGNACIONES    </b>',
						   'deduccion'=>'<b>DEDUCCIONES   </b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xPos' =>339.8,
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>186), // Justificación y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>87), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>87))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($ai_totalasignacion,$ai_totaldeduccion,$ai_total_neto,$ai_priquires,$ai_segquires,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		   Access: private 
		//	    Arguments: ai_totalasignacion // Total Asignación
		//	   			   ai_totaldeduccion // Total Deduccción
		//	   			   ai_totalaporte // Total aporte
		//	   			   ai_total_neto // Total Neto
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		
		$la_data[1]=array('texto1'=>'MONTO PRIMERA QUINCENA','texto2'=>'MONTO SEGUNDA QUINCENA','totales'=>'________________________________','asignacion'=>'__________________________','deduccion'=>'__________________________');
		$la_data[2]=array('texto1'=>'<b>'.$ai_priquires.'</b>','texto2'=>'<b>'.$ai_segquires.'</b>','totales'=>'<b>Totales...</b>                                      ','asignacion'=>'<b>'.$ai_totalasignacion.'</b>      ','deduccion'=>'<b>'.$ai_totaldeduccion.'</b>      ');
		$la_data[3]=array('texto1'=>'','texto2'=>'','totales'=>'','asignacion'=>'','deduccion'=>'');
		$la_data[4]=array('texto1'=>'FIRMA:_______________________','texto2'=>'FIRMA:_______________________','totales'=>'<b>MONTO A PAGAR </b>                     ','asignacion'=>'','deduccion'=>'<b>'.$ai_total_neto.'</b>      ');
		$la_data[5]=array('texto1'=>'','texto2'=>'','totales'=>'','asignacion'=>'','deduccion'=>'');
		$la_columna=array('texto1'=>'','texto2'=>'','totales'=>'','asignacion'=>'','deduccion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'colGap' => 0,
						 'cols'=>array('texto1'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
									   'texto2'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'totales'=>array('justification'=>'right','width'=>126), // Justificación y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>87), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>87))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_piecabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piepagina($ai_totper,$ai_totasi,$ai_totded,$ai_totapo,$ai_totgeneral,$as_desuniadm,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piepagina
		//		   Access: private 
		//	    Arguments: ai_totasi // Total de Asignaciones
		//	   			   ai_totded // Total de Deducciones
		//	   			   ai_totapo // Total de Aportes
		//	   			   ai_totgeneral // Total de Neto a Pagar
		//	   			   as_desuniadm // Descripción Unidad Administrativa
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>500); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data=array(array('titulo'=>'<b>TOTAL '.$as_desuniadm.':</b>','asignacion'=>$ai_totasi,'deduccion'=>$ai_totded,'neto'=>$ai_totgeneral));
		$la_columna=array('titulo'=>'','asignacion'=>'','deduccion'=>'','neto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((224/255),(224/255),(224/255)), // Color de la sombra
						 'shadeCol2'=>array((224/255),(224/255),(224/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>260), // Justificación y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'neto'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		
		$la_data=array(array('titulo'=>'<b>TOTAL DE TRABAJADORES:</b>','totper'=>$ai_totper));
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((224/255),(224/255),(224/255)), // Color de la sombra
						 'shadeCol2'=>array((224/255),(224/255),(224/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>260), // Justificación y ancho de la columna
						 			   'totper'=>array('justification'=>'left','width'=>240))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,'','',$la_config);
		unset ($la_data);
		unset ($la_config);	
	}
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
	$ls_titulo="<b>NÓMINA DE PAGO DEL PERSONAL ".$ls_desnom."</b>";
	$ls_periodo="<b>Período Nro ".$ls_peractnom.", del ".$ld_fecdesper." al ".$ld_fechasper."</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_coduniadmdes=$io_fun_nomina->uf_obtenervalor_get("coduniadmdes","");
	$ls_coduniadmhas=$io_fun_nomina->uf_obtenervalor_get("coduniadmhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_tituloconcepto=$io_fun_nomina->uf_obtenervalor_get("tituloconcepto","");
	$ls_conceptoreporte=$io_fun_nomina->uf_obtenervalor_get("conceptoreporte","");
	$ls_conceptop2=$io_fun_nomina->uf_obtenervalor_get("conceptop2","");
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("subnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("subnomhas","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_pagonominaunidad_unidad($ls_codperdes,$ls_codperhas,$ls_conceptocero,$ls_conceptoreporte,
														  $ls_conceptop2,$ls_coduniadmdes,$ls_coduniadmhas,$ls_subnomdes,$ls_subnomhas); // Cargar el DS con los datos de la cabecera del reporte
	}
	if(($lb_valido==false) || ($io_report->rs_data->RecordCount()==0)) // Existe algún error ó no hay registros
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
		$io_pdf->ezSetCmMargins(4.5,4,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		$io_pdf->FitWindow=true;
		$li_totrowuni=$io_report->rs_data->RecordCount();
		$li_totgenper=0;		
		$li_totgenasi=0;
		$li_totgended=0;
		$li_totgenapo=0;
		$li_totgen=0;
		$li_k=1;
		$ls_vac_codconvac=trim($io_report->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		while((!$io_report->rs_data->EOF)&&($lb_valido))
		{
			$li_totasi=0;
			$li_totded=0;
			$li_totapo=0;
			$li_totgeneral=0;
			$ls_minorguniadm=$io_report->rs_data->fields["minorguniadm"];
			$ls_ofiuniadm=$io_report->rs_data->fields["ofiuniadm"];
			$ls_uniuniadm=$io_report->rs_data->fields["uniuniadm"];
			$ls_depuniadm=$io_report->rs_data->fields["depuniadm"];
			$ls_prouniadm=$io_report->rs_data->fields["prouniadm"];
			$ls_desuniadm=$io_report->rs_data->fields["desuniadm"];
			$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
			uf_print_cabecera($ls_minorguniadm,$ls_ofiuniadm,$ls_uniuniadm,$ls_depuniadm,$ls_prouniadm,$ls_desuniadm,$io_cabecera,$io_pdf); // Imprimimos la cabecera del registro
			$lb_valido=$io_report->uf_pagonominaunidad_personal($ls_codperdes,$ls_codperhas,$ls_conceptocero,$ls_conceptoreporte,
															    $ls_conceptop2,$ls_minorguniadm,$ls_ofiuniadm,$ls_uniuniadm,
																$ls_depuniadm,$ls_prouniadm,$ls_subnomdes,$ls_subnomhas,$ls_orden); // Cargar el DS con los datos de la cabecera del reporte
			$li_totrow=$io_report->rs_data_detalle->RecordCount();
			while((!$io_report->rs_data_detalle->EOF)&&($lb_valido))
			{
				$li_totalasignacion=0;
				$li_totaldeduccion=0;
				$li_totalaporte=0;
				$li_total_neto=0;
				$ls_codper=$io_report->rs_data_detalle->fields["codper"];
				$ls_cedper=$io_report->rs_data_detalle->fields["cedper"];
				$ls_apenomper=$io_report->rs_data_detalle->fields["apeper"].", ".$io_report->rs_data_detalle->fields["nomper"];
				$ls_descar=$io_report->rs_data_detalle->fields["descar"];
				$ls_desuniadm=$io_report->rs_data_detalle->fields["desuniadm"];
				$ld_fecingper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data_detalle->fields["fecingper"]);
				$ls_codcueban=$io_report->rs_data_detalle->fields["codcueban"];
				$li_priquires=0;
				$li_segquires=0;
				$li_montovac=0;
				uf_print_cabecera_personal($ls_codper,$ls_cedper,$ls_apenomper,$ls_descar,$ls_desuniadm,$ld_fecingper,$ls_codcueban,$io_pdf); // Imprimimos la cabecera del registro
				$lb_valido=$io_report->uf_pagonominaunidad_conceptopersonal($ls_codper,$ls_conceptocero,$ls_tituloconcepto,$ls_conceptoreporte,$ls_conceptop2); // Obtenemos el detalle del reporte
				if($lb_valido)
				{
					$li_totrow_res=$io_report->rs_data_detalle2->RecordCount();
					$li_s=1;
					while((!$io_report->rs_data_detalle2->EOF)&&($lb_valido))
					{						
						$ls_codconc=$io_report->rs_data_detalle2->fields["codconc"];
						$ls_nomcon=$io_report->rs_data_detalle2->fields["nomcon"];
						$ls_tipsal=rtrim($io_report->rs_data_detalle2->fields["tipsal"]);
						$li_valsal=abs($io_report->rs_data_detalle2->fields["valsal"]);
						if($ls_codconc==$ls_vac_codconvac)
						{
							$li_montovac=$li_valsal;
							if(substr($ld_fecdesper,3,2)==substr($ld_fecingper,3,2))
							{
								if((intval(substr($ld_fecingper,0,2))>=2)&&(intval(substr($ld_fecingper,0,2))<=16))
								{
									$li_priquires=$li_valsal;
								}
								elseif(intval(substr($ld_fecingper,0,2))>=17)
								{
									$li_segquires=$li_valsal;
								}
							}
							else
							{
								if(intval(substr($ld_fecdesper,3,2)+1)==intval(substr($ld_fecingper,3,2)))
								{
									if(intval(substr($ld_fecingper,0,2))==1)
									{
										$li_segquires=$li_valsal;
									}
								}
							}
						}
						switch($ls_tipsal)
						{
							case "A":
								$li_totalasignacion=$li_totalasignacion + $li_valsal;
								$li_asignacion=number_format($li_valsal,2,",",".");
								$li_deduccion=""; 
								$li_aporte=""; 
								break;
								
							case "V1":
								$li_totalasignacion=$li_totalasignacion + $li_valsal;
								$li_asignacion=number_format($li_valsal,2,",",".");
								$li_deduccion=""; 
								$li_aporte=""; 
								break;
								
							case "W1":
								$li_totalasignacion=$li_totalasignacion + $li_valsal;
								$li_asignacion=number_format($li_valsal,2,",",".");
								$li_deduccion=""; 
								$li_aporte=""; 
								break;
								
							case "D":
								$li_totaldeduccion=$li_totaldeduccion + $li_valsal;
								$li_asignacion=""; 
								$li_deduccion=number_format($li_valsal,2,",",".");
								$li_aporte=""; 
								break;
								
							case "V2":
								$li_totaldeduccion=$li_totaldeduccion + $li_valsal;
								$li_asignacion=""; 
								$li_deduccion=number_format($li_valsal,2,",",".");
								$li_aporte=""; 
								break;
								
							case "W2":
								$li_totaldeduccion=$li_totaldeduccion + $li_valsal;
								$li_asignacion=""; 
								$li_deduccion=number_format($li_valsal,2,",",".");
								$li_aporte=""; 
								break;
	
							case "P1":
								$li_totaldeduccion=$li_totaldeduccion + $li_valsal;
								$li_asignacion=""; 
								$li_deduccion=number_format($li_valsal,2,",",".");
								$li_aporte=""; 
								break;
	
							case "V3":
								$li_totaldeduccion=$li_totaldeduccion + $li_valsal;
								$li_asignacion=""; 
								$li_deduccion=number_format($li_valsal,2,",",".");
								$li_aporte=""; 
								break;
	
							case "W3":
								$li_totaldeduccion=$li_totaldeduccion + $li_valsal;
								$li_asignacion=""; 
								$li_deduccion=number_format($li_valsal,2,",",".");
								$li_aporte=""; 
								break;
	
							case "P2":
								$li_totalaporte=$li_totalaporte + $li_valsal;
								$li_asignacion=""; 
								$li_deduccion=""; 
								$li_aporte=number_format($li_valsal,2,",",".");
								break;
	
							case "V4":
								$li_totalaporte=$li_totalaporte + $li_valsal;
								$li_asignacion=""; 
								$li_deduccion=""; 
								$li_aporte=number_format($li_valsal,2,",",".");
								break;
	
							case "W4":
								$li_totalaporte=$li_totalaporte + $li_valsal;
								$li_asignacion=""; 
								$li_deduccion=""; 
								$li_aporte=number_format($li_valsal,2,",",".");
								break;
	
							case "R":
								$li_asignacion=number_format($li_valsal,2,",",".");
								$li_deduccion=""; 
								$li_aporte="";
								break;
						}
						$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>$li_asignacion,
											  'deduccion'=>$li_deduccion);
						$li_s++;
						$io_report->rs_data_detalle2->MoveNext();				  
					}
					uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle  
					$li_total_neto=$li_totalasignacion-$li_totaldeduccion;
					$li_neto=number_format((($li_total_neto-$li_montovac)/2),2,".","");
					$li_priquires=number_format(($li_priquires+$li_neto),2,",",".");
					$li_segquires=number_format(($li_segquires+$li_neto),2,",",".");
					$li_totasi=$li_totasi+$li_totalasignacion;
					$li_totded=$li_totded+$li_totaldeduccion;
					$li_totapo=$li_totapo+$li_totalaporte;
					$li_totgeneral=$li_totgeneral+$li_total_neto;
					$li_totalasignacion=number_format($li_totalasignacion,2,",",".");
					$li_totaldeduccion=number_format($li_totaldeduccion,2,",",".");
					$li_totalaporte=number_format($li_totalaporte,2,",",".");
					$li_total_neto=number_format($li_total_neto,2,",",".");
					uf_print_piecabecera($li_totalasignacion,$li_totaldeduccion,$li_total_neto,$li_priquires,$li_segquires,$io_pdf); // Imprimimos el pie de la cabecera					
				}
				$io_report->rs_data_detalle->MoveNext();
				unset($la_data);
			}
			$li_totgenper=$li_totgenper+$li_totrow;		
			$li_totgenasi=$li_totgenasi+$li_totasi;
			$li_totgended=$li_totgended+$li_totded;
			$li_totgenapo=$li_totgenapo+$li_totapo;
			$li_totgen=$li_totgen+$li_totgeneral;			
			$li_totasi=number_format($li_totasi,2,",",".");
			$li_totded=number_format($li_totded,2,",",".");
			$li_totapo=number_format($li_totapo,2,",",".");
			$li_totgeneral=number_format($li_totgeneral,2,",",".");
			uf_print_piepagina($li_totrow,$li_totasi,$li_totded,$li_totapo,$li_totgeneral,'UNIDAD ADMINISTRATIVA',$io_pdf);
			$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera
			if($li_k<$li_totrowuni)
			{
				$io_pdf->ezNewPage(); // Insertar una nueva página
			}
			unset($io_cabecera);
			$li_k++;
			$io_report->rs_data->MoveNext();
		}	
		if($lb_valido) // Si no ocurrio ningún error
		{
			$li_totgenasi=number_format($li_totgenasi,2,",",".");
			$li_totgended=number_format($li_totgended,2,",",".");
			$li_totgenapo=number_format($li_totgenapo,2,",",".");
			$li_totgen=number_format($li_totgen,2,",",".");	
			uf_print_piepagina($li_totgenper,$li_totgenasi,$li_totgended,$li_totgenapo,$li_totgen,'GENERAL',$io_pdf);				
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