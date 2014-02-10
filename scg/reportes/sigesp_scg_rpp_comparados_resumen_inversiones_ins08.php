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
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scg;
		
		$ls_descripcion="Gener� el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_scg_r_comparados_forma0714.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_periodo_comp // Descripci�n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci�n del per�odo de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_titulo1;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,30,1000,30);
		
		$io_pdf->rectangle(10,480,985,110);
		//$io_pdf->addJpegFromFile('../../shared/imagebank/logo.jpg',10,525,80); // Agregar Logo
		$io_pdf->addText(15,580,11,"<b>OFICINA NACIONAL DE PRESUPUESTO (ONAPRE)</b>"); // Agregar la Fecha
		$io_pdf->addText(15,565,11,"<b>OFICINA DE PLANIFICACI�N DEL SECTOR UNIVERSITARIO (OPSU)</b>"); // Agregar la Fecha
		
		$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,530,16,$as_titulo); // Agregar el t�tulo

		$li_tm=$io_pdf->getTextWidth(12,$ls_titulo1);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,518,12,$ls_titulo1); // Agregar el t�tulo
		
		$io_pdf->addText(900,560,7,$_SESSION["ls_database"]); // Agregar la Base de datos
		$io_pdf->addText(900,550,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(900,540,10,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte($ai_ano,$as_meses_trimestre,$as_etiqueta,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_periodo_comp // Descripci�n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci�n del per�odo de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSetY(520);
		if($as_etiqueta=="Mensual")
		{
		   $ls_etiqueta="Mes";
		}
		if($as_etiqueta=="Bi-Mensual")
		{
		   $ls_etiqueta="Bimestre";
		}
		if($as_etiqueta=="Trimestral")
		{
		   $ls_etiqueta="Trimestre";
		}
		if($as_etiqueta=="Semestral")
		{
		   $ls_etiqueta="Semestre";
		}
		$la_data=array(array('name'=>'<b>Presupuesto   </b> '.'<b>'.$ai_ano.'</b>'),
		               array('name'=>'<b>'.$ls_etiqueta.'   </b>'.'<b>'.$as_meses_trimestre.'</b>'));
		$la_columna=array('name'=>'','name'=>'','name'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 8,       // Tama�o de Letras
						 'titleFontSize' => 8, // Tama�o de Letras de los t�tulos
						 'showLines'=>0,        // Mostrar L�neas
						 'shaded'=>0,           // Sombra entre l�neas
						 'xPos'=>265,//65
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>500),
									   'name'=>array('justification'=>'left','width'=>500)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_venta(&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_periodo_comp // Descripci�n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci�n del per�odo de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSetY(480);
		$la_data=array(array('name'=>'<b> VENTA Y/O DESINCORPORACI�N DE ACTIVOS </b>'));
		$la_columna=array('name'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 8,       // Tama�o de Letras
						 'titleFontSize' => 8, // Tama�o de Letras de los t�tulos
						 'showLines'=>0,        // Mostrar L�neas
						 'shaded'=>0,           // Sombra entre l�neas
						 'xPos'=>265,//65
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>500)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo($as_etiqueta,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_titulo
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci�n que imprime la cabecera de cada p�gina
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
        $io_pdf->ezSetDy(-50);
		if($as_etiqueta=="Mensual")
		{
		   $ls_etiqueta="Mes";
		}
		if($as_etiqueta=="Bi-Mensual")
		{
		   $ls_etiqueta="Bimestre";
		}
		if($as_etiqueta=="Trimestral")
		{
		   $ls_etiqueta="Trimestre";
		}
		if($as_etiqueta=="Semestral")
		{
		   $ls_etiqueta="Semestre";
		}
		$la_data   =array(array('name1'=>'<b></b>',
								'name2'=>'<b>VARIACION '.strtoupper($ls_etiqueta).'</b>',
                                'name3'=>'<b>VARIACION EJECUTADO PROGRAMADO '.strtoupper($ls_etiqueta).'</b>',
                                'name4'=>'<b>TOTAL ACUMULADO AL '.strtoupper($ls_etiqueta).'</b>'));
                                
		$la_columna=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 7,       // Tama�o de Letras
						 'titleFontSize' => 7, // Tama�o de Letras de los t�tulos
						 'showLines'=>1,        // Mostrar L�neas
						 'shaded'=>0,           // Sombra entre l�neas
						 'xPos'=>509,
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900,
						 'cols'=>array('name1'=>array('justification'=>'center','width'=>410),// Justificaci�n y ancho de la columna
						               'name2'=>array('justification'=>'center','width'=>190),// Justificaci�n y ancho de la columna
									   'name3'=>array('justification'=>'center','width'=>190),// Justificaci�n y ancho de la columna
									   'name4'=>array('justification'=>'center','width'=>190))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');

	}// end function uf_print_titulo
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_etiqueta,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci�n que imprime la cabecera de cada p�gina
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		if($as_etiqueta=="Mensual")
		{
		   $ls_etiqueta="Mes";
		}
		if($as_etiqueta=="Bi-Mensual")
		{
		   $ls_etiqueta="Bimestre";
		}
		if($as_etiqueta=="Trimestral")
		{
		   $ls_etiqueta="Trimestre";
		}
		if($as_etiqueta=="Semestral")
		{
		   $ls_etiqueta="Semestre";
		}
		$la_data=array(array('cuenta'=>'<b>Cuenta</b>','denominacion'=>'<b>Denominaci�n</b>',
                             'pres_apr'=>'<b>Presupuesto Aprobado</b>',
		                     'pres_mod'=>'<b>Presupuesto Modificado</b>',
                             'monto_prog'=>'<b>Programado</b>',
                             'monto_eject'=>'<b>Ejecutado</b>',
							 'varia_abs'=>'<b>Absoluto</b>',
                             'varia_porc'=>'<b>Porcentual</b>',
                             'total_acum_pro'=>'<b>Programado</b>',
							 'total_acum_eje'=>'<b>Ejecutado</b>'));
		$la_columna=array('cuenta'=>'','denominacion'=>'',
                             'pres_apr'=>'',
                             'pres_mod'=>'',
                             'monto_prog'=>'',
                             'monto_eject'=>'',
		                     'varia_abs'=>'',
                             'varia_porc'=>'',
                             'total_acum_pro'=>'',
                             'total_acum_eje'=>'');
		$la_config=array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 7,       // Tama�o de Letras
						 'titleFontSize' => 7, // Tama�o de Letras de los t�tulos
						 'showLines'=>2,        // Mostrar L�neas
						 'shaded'=>0,           // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990,
						 'colGap'=>0,
						 'xPos'=>505,
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'center','width'=>150), // Justificaci�n y ancho de la columna
						 			   'pres_apr'=>array('justification'=>'center','width'=>95), // Justificaci�n y ancho de la columna
						 			   'pres_mod'=>array('justification'=>'center','width'=>95), //f Justificaci�n y ancho de la columna
									   'monto_prog'=>array('justification'=>'center','width'=>95), // Justificaci�n y ancho de la columna
									   'monto_eject'=>array('justification'=>'center','width'=>95), // Justificaci�n y ancho de la columna
									   'varia_abs'=>array('justification'=>'center','width'=>95), // Justificaci�n y ancho de la columna
									   'varia_porc'=>array('justification'=>'center','width'=>95), // Justificaci�n y ancho de la columna
									   'total_acum_pro'=>array('justification'=>'center','width'=>95), // Justificaci�n y ancho de la columna
									   'total_acum_eje'=>array('justification'=>'center','width'=>95))); // Justificaci�n y ancho de la columna
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');

	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'xPos'=>505,        
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>150), // Justificaci�n y ancho de la columna
						 			   'presupuesto_aprobado'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
						 			   'presupuesto_modificado'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'pres_anual'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'monto_eject'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'varia_abs_acum'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'varia_porc_acum'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'prog_acum'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'acum_eject'=>array('justification'=>'right','width'=>95))); // Justificaci�n y ancho de la columna
		
		$la_columnas=array('cuenta'=>'<b></b>',
						   'denominacion'=>'<b></b>',
						   'presupuesto_aprobado'=>'<b>Presupuesto Aprobado</b>',
						   'presupuesto_modificado'=>'<b>Presupuesto Aprobado</b>',
						   'pres_anual'=>'<b>Monto Programado</b>',
						   'monto_eject'=>'<b>Monto Ejecutado</b>',
						   'varia_abs_acum'=>'<b>Absoluto</b>',
						   'varia_porc_acum'=>'<b>Porcentual</b>',
						   'prog_acum'=>'<b>Programado</b>',
						   'acum_eject'=>'<b>Ejecutado</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($la_data_tot,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : funci�n que imprime el fin de la cabecera de cada p�gina
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
                         'xPos'=>505, 
						 'cols'=>array('total'=>array('justification'=>'right','width'=>220), // Justificaci�n y ancho de la columna
									   'presupuesto_aprobado'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
						 			   'presupuesto_modificado'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'pres_anual'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'monto_eject'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'varia_abs_acum'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'varia_porc_acum'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'prog_acum'=>array('justification'=>'right','width'=>95), // Justificaci�n y ancho de la columna
									   'acum_eject'=>array('justification'=>'right','width'=>95))); // Justificaci�n y ancho de la columna
		
		$la_columnas=array('total'=>'',
		                   'presupuesto_aprobado'=>'',
						   'presupuesto_modificado'=>'',
						   'pres_anual'=>'',
						   'monto_eject'=>'',
						   'varia_abs_acum'=>'',
						   'varia_porc_acum'=>'',
						   'prog_acum'=>'',
						   'acum_eject'=>'');
		$io_pdf->ezTable($la_data_tot,$la_columnas,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../shared/ezpdf/class.ezpdf.php");
		require_once("sigesp_scg_reporte_comparado_resumen_inversiones_ins08.php");
		$io_report = new sigesp_scg_reporte_comparado_resumen_inversiones_ins08();
		require_once("../../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();			
		require_once("../../shared/class_folder/class_fecha.php");
		$io_fecha = new class_fecha();
		require_once("../class_funciones_scg.php");
		$io_fun_scg=new class_funciones_scg();
		$ls_tiporeporte="0";
		$ls_bolivares="";
		if (array_key_exists("tiporeporte",$_GET))
		{
			$ls_tiporeporte=$_GET["tiporeporte"];
		}
		switch($ls_tiporeporte)
		{
			case "0":
				require_once("sigesp_scg_reporte_comparado_resumen_inversiones_ins08.php");
				$io_report  = new sigesp_scg_reporte_comparado_resumen_inversiones_ins08();
				$ls_bolivares ="Bs.";
				break;
	
			case "1":
				require_once("sigesp_scg_reporte_comparado_resumen_inversiones_ins08.php");
				$io_report  = new sigesp_scg_reporte_comparado_resumen_inversiones_ins08();
				$ls_bolivares ="Bs.F.";
				break;
		}
//--------------------------------------------------  Par�metros para Filtar el Reporte  ---------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		
		$ls_etiqueta=$_GET["txtetiqueta"];
		if($ls_etiqueta=="Mensual")
		{
			$ls_combo=$_GET["combo"];
			$ls_combomes=$_GET["combomes"];
			$li_mesdes=substr($ls_combo,0,2);
			$li_meshas=substr($ls_combomes,0,2); 
			$li_mesdes=intval($li_mesdes);
			$li_meshas=intval($li_meshas); 
			$ls_cant_mes=1;
			$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			$ls_combo=$ls_combo.$ls_combomes;
			
		}
		else
		{
			$ls_combo=$_GET["combo"];
			$li_mesdes=substr($ls_combo,0,2);
			$li_meshas=substr($ls_combo,2,2); 
			$li_mesdes=intval($li_mesdes);
			$li_meshas=intval($li_meshas); 
			if($ls_etiqueta=="Bi-Mensual")
			{
				$ls_cant_mes=2;
				$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			}
			if($ls_etiqueta=="Trimestral")
			{
				$ls_cant_mes=3;
				$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			}
			if($ls_etiqueta=="Semestral")
			{
				$ls_cant_mes=6;
				$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			}
		}
		$ls_mesdes=substr($ls_combo,0,2);
		$ls_meshas=substr($ls_combo,2,2);
		$ls_diades="01";
		$ls_diahas=$io_fecha->uf_last_day($ls_meshas,$li_ano);
		$ldt_fecdes=$ls_diades."/".$ls_mesdes."/".$li_ano;
		$ldt_fechas=$ls_diahas;
//----------------------------------------------------  Par�metros del encabezado  --------------------------------------------
		$ls_titulo=" <b>RESUMEN DE  INVERSIONES</b>";       
		$ls_titulo1=" (Expresado en ".$ls_bolivares.") ";
//------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
	$lb_valido=uf_insert_seguridad("<b>Instructivo 08 Comparado Resumen de Inversiones</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
	    $lb_valido=$io_report->uf_scg_reportes_comparados_resumen_inversiones_ins08($ldt_fecdes,$ldt_fechas,$li_mesdes,$li_meshas,
	                                                                    $ls_cant_mes);
	    //print "$lb_valido :valido retornado al salir <br>";
	    //var_dump($lb_valido);
	}
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		//print(" close();");
		print("</script>");
	}
	 else // Imprimimos el reporte
	{
		error_reporting(E_ALL);
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetDy(0.5);
		$io_pdf->ezSetCmMargins(7.08,3,3,3);            // Configuraci�n de los margenes en cent�metros
		uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la p�gina
		$io_pdf->ezStartPageNumbers(980,40,10,'','',1); // Insertar el n�mero de p�gina
		$ld_total_monto_programado=0;
		$ld_total_monto_programado_acumulado=0;
		$ld_total_monto_ejecutado=0;
		$ld_total_monto_ejecutado_acumulado=0;
		$ld_total_variacion_absoluta=0;
		$ld_total_porcentaje_variacion=0;
		$ld_total_variacion_absoluta_acumulada=0;
		$ld_total_porcentaje_variacion_acumulada=0;
		$ld_total_reprog_proxima=0;
        $ld_total_presupuesto_aprobado = 0;
        $ld_total_presupuesto_modificado = 0;
		$li_total=$io_report->dts_reporte->getRowCount("sc_cuenta");
		//print "total: $li_total <br>";
		for($z=1;$z<=$li_total;$z++)
		{
			
			$thisPageNum=$io_pdf->ezPageCount;
			$ls_sc_cuenta=$io_report->dts_reporte->data["sc_cuenta"][$z];
			$ls_denominacion=$io_report->dts_reporte->data["denominacion"][$z];
			$li_nivel=$io_report->dts_reporte->data["nivel"][$z];
			$ld_monto_programado=$io_report->dts_reporte->data["monto_programado"][$z];
			$ld_monto_programado_acumulado=$io_report->dts_reporte->data["programado_acumulado"][$z];
			$ld_monto_ejecutado=$io_report->dts_reporte->data["monto_ejecutado"][$z];
			$ld_monto_ejecutado_acumulado=$io_report->dts_reporte->data["ejecutado_acumulado"][$z];
			$ld_variacion_absoluta=$io_report->dts_reporte->data["variacion_absoluta"][$z];
			$ld_porcentaje_variacion=$io_report->dts_reporte->data["porcentaje_variacion"][$z];
			$ld_variacion_absoluta_acumulada=$io_report->dts_reporte->data["variacion_absoluta_acumulada"][$z];
			$ld_porcentaje_variacion_acumulada=$io_report->dts_reporte->data["porcentaje_variacion_acumulado"][$z];
			$ld_reprog_proxima=$io_report->dts_reporte->data["reprogr_prox_periodo"][$z];
            $ld_presupuesto_aprobado=$io_report->dts_reporte->data["presupuesto_aprobado"][$z];
            $ld_presupuesto_modificado=$io_report->dts_reporte->data["presupuesto_modificado"][$z];
            
			if($li_nivel==1)
			{
				$ld_total_monto_programado=$ld_total_monto_programado+$ld_monto_programado;
				$ld_total_monto_programado_acumulado=$ld_total_monto_programado_acumulado+$ld_monto_programado_acumulado;
				$ld_total_monto_ejecutado=$ld_total_monto_ejecutado+$ld_monto_ejecutado;
				$ld_total_monto_ejecutado_acumulado=$ld_total_monto_ejecutado_acumulado+$ld_monto_ejecutado_acumulado;
				$ld_total_variacion_absoluta=$ld_total_variacion_absoluta+$ld_variacion_absoluta;
				$ld_total_porcentaje_variacion=$ld_total_porcentaje_variacion+$ld_porcentaje_variacion;
				$ld_total_variacion_absoluta_acumulada=$ld_total_variacion_absoluta_acumulada+$ld_variacion_absoluta_acumulada;
				$ld_total_porcentaje_variacion_acumulada=$ld_total_porcentaje_variacion_acumulada+$ld_porcentaje_variacion_acumulada;
				$ld_total_reprog_proxima=$ld_total_reprog_proxima+$ld_reprog_proxima;
                $ld_total_presupuesto_aprobado = $ld_total_presupuesto_aprobado +  $ld_presupuesto_aprobado;
                $ld_total_presupuesto_modificado = $ld_total_presupuesto_modificado + $ld_presupuesto_modificado;
                
                
			}	
			$ld_monto_programado=number_format($ld_monto_programado,2,",",".");
			$ld_monto_programado_acumulado=number_format($ld_monto_programado_acumulado,2,",",".");
			$ld_monto_ejecutado=number_format($ld_monto_ejecutado,2,",",".");
			$ld_monto_ejecutado_acumulado=number_format($ld_monto_ejecutado_acumulado,2,",",".");
			$ld_variacion_absoluta=number_format(abs($ld_variacion_absoluta),2,",",".");
			$ld_porcentaje_variacion=number_format(abs($ld_porcentaje_variacion),2,",",".");
			$ld_variacion_absoluta_acumulada=number_format(abs($ld_variacion_absoluta_acumulada),2,",",".");
			$ld_porcentaje_variacion_acumulada=number_format(abs($ld_porcentaje_variacion_acumulada),2,",",".");
			$ld_reprog_proxima=number_format($ld_reprog_proxima,2,",",".");
            $ld_presupuesto_aprobado    =number_format($ld_presupuesto_aprobado,2,",",".");
			$ld_presupuesto_modificado  =number_format($ld_presupuesto_modificado,2,",",".");
            
			$la_data[$z]=array('cuenta'=>$ls_sc_cuenta,'denominacion'=>$ls_denominacion,'pres_anual'=>$ld_monto_programado,
							   'prog_acum'=>$ld_monto_programado_acumulado,'monto_eject'=>$ld_monto_ejecutado,
							   'acum_eject'=>$ld_monto_ejecutado_acumulado,'varia_abs'=>$ld_variacion_absoluta,
							   'varia_porc'=>$ld_porcentaje_variacion,'varia_abs_acum'=>$ld_variacion_absoluta_acumulada,
							   'varia_porc_acum'=>$ld_porcentaje_variacion_acumulada,'reprog_prox_mes'=>$ld_reprog_proxima,
                               'presupuesto_aprobado'=>$ld_presupuesto_aprobado,'presupuesto_modificado'=>$ld_presupuesto_modificado);
								                                              
			$ld_monto_programado=str_replace('.','',$ld_monto_programado);
			$ld_monto_programado=str_replace(',','.',$ld_monto_programado);		
			$ld_monto_programado_acumulado=str_replace('.','',$ld_monto_programado_acumulado);
			$ld_monto_programado_acumulado=str_replace(',','.',$ld_monto_programado_acumulado);		
			$ld_monto_ejecutado=str_replace('.','',$ld_monto_ejecutado);
			$ld_monto_ejecutado=str_replace(',','.',$ld_monto_ejecutado);		
			$ld_monto_ejecutado_acumulado=str_replace('.','',$ld_monto_ejecutado_acumulado);
			$ld_monto_ejecutado_acumulado=str_replace(',','.',$ld_monto_ejecutado_acumulado);
			$ld_variacion_absoluta=str_replace('.','',$ld_variacion_absoluta);
			$ld_variacion_absoluta=str_replace(',','.',$ld_variacion_absoluta);
			$ld_porcentaje_variacion=str_replace('.','',$ld_porcentaje_variacion);
			$ld_porcentaje_variacion=str_replace(',','.',$ld_porcentaje_variacion);		
			$ld_variacion_absoluta_acumulada=str_replace('.','',$ld_variacion_absoluta_acumulada);
			$ld_variacion_absoluta_acumulada=str_replace(',','.',$ld_variacion_absoluta_acumulada);	
			$ld_porcentaje_variacion_acumulada=str_replace('.','',$ld_porcentaje_variacion_acumulada);
			$ld_porcentaje_variacion_acumulada=str_replace(',','.',$ld_porcentaje_variacion_acumulada);		
			$ld_reprog_proxima=str_replace('.','',$ld_reprog_proxima);
			$ld_reprog_proxima=str_replace(',','.',$ld_reprog_proxima);		
			
			if($z==$li_total)
			{
				$ld_total_monto_programado=number_format($ld_total_monto_programado,2,",",".");
				$ld_total_monto_programado_acumulado=number_format($ld_total_monto_programado_acumulado,2,",",".");
				$ld_total_monto_ejecutado=number_format($ld_total_monto_ejecutado,2,",",".");
				$ld_total_monto_ejecutado_acumulado=number_format($ld_total_monto_ejecutado_acumulado,2,",",".");
				$ld_total_variacion_absoluta=number_format($ld_total_variacion_absoluta,2,",",".");
				$ld_total_porcentaje_variacion=number_format($ld_total_porcentaje_variacion,2,",",".");
				$ld_total_variacion_absoluta_acumulada=number_format($ld_total_variacion_absoluta_acumulada,2,",",".");
				$ld_total_porcentaje_variacion_acumulada=number_format($ld_total_porcentaje_variacion_acumulada,2,",",".");
				$ld_total_reprog_proxima=number_format($ld_total_reprog_proxima,2,",",".");
                $ld_total_presupuesto_aprobado = number_format($ld_total_presupuesto_aprobado,2,",",".");
                $ld_total_presupuesto_modificado = number_format($ld_total_presupuesto_modificado,2,",",".");
				 
				 $la_data_tot[$z]=array('total'=>'<b>TOTALES</b>','pres_anual'=>$ld_total_monto_programado,'prog_acum'=>$ld_total_monto_programado_acumulado,
										'monto_eject'=>$ld_total_monto_ejecutado,'acum_eject'=>$ld_total_monto_ejecutado_acumulado,
										'varia_abs'=>$ld_total_variacion_absoluta,'varia_porc'=>$ld_total_porcentaje_variacion,
										'varia_abs_acum'=>$ld_total_variacion_absoluta_acumulada,'varia_porc_acum'=>$ld_total_porcentaje_variacion_acumulada,
										'reprog_prox_mes'=>$ld_total_reprog_proxima,
                                        'presupuesto_aprobado'=>$ld_total_presupuesto_aprobado,'presupuesto_modificado'=>$ld_total_presupuesto_modificado);
			}//if
		}//for
		uf_print_titulo_reporte($li_ano,$ls_meses,$ls_etiqueta,$io_pdf);
		uf_print_titulo($ls_etiqueta,$io_pdf);
		uf_print_cabecera($ls_etiqueta,$io_pdf);
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		uf_print_pie_cabecera($la_data_tot,$io_pdf);
		unset($la_data);
		unset($la_data_tot);
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if($z<$li_total)
		{
		 $io_pdf->ezNewPage(); // Insertar una nueva p�gina
		} 
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
		unset($io_pdf);
	}//else
	unset($io_report);
	unset($io_funciones);
?> 