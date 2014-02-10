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
	require_once ("sigesp_spg_class_tcpdf.php");
  	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');
	set_time_limit(0);
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_periodo_comp,$as_fecha_comp,&$io_pdf)
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
		// Fecha Creación: 21/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,40,578,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo

		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,694,11,$as_titulo); // Agregar el título

		$li_tm=$io_pdf->getTextWidth(11,$as_periodo_comp);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,682,11,$as_periodo_comp); // Agregar el título

		$li_tm=$io_pdf->getTextWidth(11,$as_fecha_comp);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,670,11,$as_fecha_comp); // Agregar el título

		$io_pdf->addText(500,720,9,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(500,710,9,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	
	

	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_procede,$as_comprobante,$as_nomprobene,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private
		//	    Arguments: as_procede // procede
		//	    		   as_comprobante // comprobante
		//                 as_nomprobene   // nombre del proveedor
		//	    		   io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 21/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('name'=>'<b>Comprobante</b>  '.$as_procede.'---'.$as_comprobante.''),
		               array('name'=>'<b>Proveedor</b>  '.$as_nomprobene.''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 8, // Tamaño de Letras
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9),
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xPos'=>299); // Orientación de la tabla 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_programatica($as_programatica,$as_denestpro,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private
		//	    Arguments: as_programatica // programatica del comprobante
		//	    		   as_denestpro5 // denominacion de la programatica del comprobante
		//	    		   io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 21/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($_SESSION["la_empresa"]["estmodest"] == 2)
		{
			$la_data=array(array('name'=>'<b>Programatica</b>  '.$as_programatica.''),
		               array('name'=>'<b></b>'.$as_denestpro.''));
			$la_columna=array('name'=>'');
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'showLines'=>0, // Mostrar Líneas
							 'fontSize' => 9, // Tamaño de Letras
							 'shaded'=>2, // Sombra entre líneas
							 'shadeCol'=>array(0.98,0.98,0.98), // Color de la sombra
							 'shadeCol2'=>array(0.98,0.98,0.98), // Color de la sombra
							 'xOrientation'=>'center', // Orientación de la tabla
							 'width'=>520, // Ancho de la tabla
							 'maxWidth'=>520, // Ancho Máximo de la tabla
							 'xPos'=>299); // Orientación de la tabla 
			$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		}
		else
		{
		 	$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	 		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	 		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	 
	 		$la_datatit=array(array('name'=>'<b>ESTRUCTURA PRESUPUESTARIA </b>'));
	 
	 		$la_columnatit=array('name'=>'');
	 
	 		$la_configtit=array('showHeadings'=>0, // Mostrar encabezados
								 'showLines'=>0, // Mostrar Líneas
								 'shaded'=>2, // Sombra entre líneas
								 'fontSize' => 8, // Tamaño de Letras
								 'shadeCol'=>array(0.98,0.98,0.98), // Color de la sombra
								 'shadeCol2'=>array(0.98,0.98,0.98), // Color de la sombra
								 'xOrientation'=>'center', // Orientación de la tabla
								 'xPos'=>299, // Orientación de la tabla
								 'width'=>520, // Ancho de la tabla
								 'maxWidth'=>520);// Ancho Máximo de la tabla
	 
	 		$io_pdf->ezTable($la_datatit,$la_columnatit,'',$la_configtit);	
	 
			 $la_data=array(array('name'=>substr($as_programatica,0,$ls_loncodestpro1).'</b>','name2'=>$as_denestpro[0]),
							array('name'=>substr($as_programatica,$ls_loncodestpro1,$ls_loncodestpro2),'name2'=>$as_denestpro[1]),
							array('name'=>substr($as_programatica,$ls_loncodestpro1+$ls_loncodestpro2,$ls_loncodestpro3),'name2'=>$as_denestpro[2]));
							
			 $la_columna=array('name'=>'','name2'=>'');
			 $la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'showLines'=>0, // Mostrar Líneas
							 'shaded'=>2, // Sombra entre líneas
							 'fontSize' => 8, // Tamaño de Letras
							 'shadeCol'=>array(0.98,0.98,0.98), // Color de la sombra
							 'shadeCol2'=>array(0.98,0.98,0.98), // Color de la sombra
							 'xOrientation'=>'center', // Orientación de la tabla
							 'xPos'=>299, // Orientación de la tabla
							 'width'=>520, // Ancho de la tabla
							 'maxWidth'=>520,// Ancho Máximo de la tabla
							 'cols'=>array('name'=>array('justification'=>'right','width'=>40), // Justificación y ancho de la columna
										   'name2'=>array('justification'=>'left','width'=>480))); // Justificación y ancho de la columna
			 $io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		}		
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 21/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>299, // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'dencuenta'=>array('justification'=>'left','width'=>115), // Justificación y ancho de la columna
						 			   'descripcion'=>array('justification'=>'left','width'=>115), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'operacion'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'dencuenta'=>'<b>Denominacion Cuenta</b>',
						   'descripcion'=>'<b>Descripción Movimiento</b>',
						   'fecha'=>'<b>Fecha</b>',
						   'operacion'=>'<b>Operacion</b>',
						   'monto'=>'<b>Monto</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total_programatica($ad_totalprogramatica,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_total_programatica
		//		    Acess : private
		//	    Arguments : ad_totalprogramatica // Total Programatica
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($_SESSION["la_empresa"]["estmodest"] == 2)
		{
		 $la_data[1]=array('cuenta'=>' ','dencuenta'=>' ','descripcion'=>' ','fecha'=>' ','operacion'=>'<b>Total Programatica </b>','monto'=>$ad_totalprogramatica);
		}
		else
		{
		 $la_data[1]=array('cuenta'=>' ','dencuenta'=>' ','descripcion'=>' ','fecha'=>' ','operacion'=>'<b>Total Estructura Presupuestaria </b>','monto'=>$ad_totalprogramatica); 
		}
		$la_columnas=array('cuenta'=>'','dencuenta'=>'','descripcion'=>'','fecha'=>'','operacion'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>299, // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'dencuenta'=>array('justification'=>'left','width'=>70), // Justificación y ancho de la columna
						 			   'descripcion'=>array('justification'=>'left','width'=>70), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'operacion'=>array('justification'=>'right','width'=>180), // Justificación y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_total_programatica
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total_comprobante($ad_totalcomprobante,$as_denominacion,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_total_programatica
		//		    Acess : private
		//	    Arguments : ad_totalcomprobante // Total Comprobante
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data[1]=array('cuenta'=>' ','dencuenta'=>' ','descripcion'=>' ','fecha'=>' ','operacion'=>'<b>Total Comprobante '.$as_denominacion.' </b>','monto'=>$ad_totalcomprobante);
		$la_columnas=array('cuenta'=>'','dencuenta'=>'','descripcion'=>'','fecha'=>'','operacion'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>299, // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'dencuenta'=>array('justification'=>'left','width'=>112), // Justificación y ancho de la columna
						 			   'descripcion'=>array('justification'=>'left','width'=>112), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'operacion'=>array('justification'=>'center','width'=>96), // Justificación y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);									   
	}// end function uf_print_total_comprobante
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_total,$as_denominacion,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('name'=>'------------------------------------------------------------------------------------------------------------------------------------------------------------------------'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>520); // Ancho Máximo de la tabla---
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('total'=>'<b>Total '.$as_denominacion.'</b>','monto'=>$ad_total));
		$la_columna=array('total'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 9, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>400), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>120))); // Justificación y ancho de la columna

		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("sigesp_spg_funciones_reportes.php");
	$io_function_report = new sigesp_spg_funciones_reportes();
	$ls_tipoformato=$_GET["tipoformato"];
//-----------------------------------------------------------------------------------------------------------------------------
		global $ls_tipoformato;
		global $la_data_tot_bsf;
		global $la_data_tot;
		 if($ls_tipoformato==1)
		 {
			require_once("sigesp_spg_reporte_bsf.php");
			$io_report = new sigesp_spg_reporte_bsf();
		 }
		 else
		 {
			require_once("sigesp_spg_reporte.php");
		    $io_report = new sigesp_spg_reporte();
		 }	
		 	
		 require_once("../../shared/class_folder/sigesp_c_reconvertir_monedabsf.php");            
		 $io_rcbsf= new sigesp_c_reconvertir_monedabsf();
		 $li_candeccon=$_SESSION["la_empresa"]["candeccon"];
		 $li_tipconmon=$_SESSION["la_empresa"]["tipconmon"];
		 $li_redconmon=$_SESSION["la_empresa"]["redconmon"];
		 
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	    $ls_cuentades_min=$_GET["txtcuentades"];
	    $ls_cuentahas_max=$_GET["txtcuentahas"];
		if($ls_cuentades_min=="")
		{
		   if($io_report->uf_spg_reporte_select_min_cuenta($ls_cuentades_min))
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
		   if($io_report->uf_spg_reporte_select_max_cuenta($ls_cuentahas_max))
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
	 $fecdes=$_GET["txtfecdes"];
	 if (!empty($fecdes))
	 {
	     $ldt_fecdes=$io_funciones->uf_convertirdatetobd($fecdes);
	 }	else {  $ldt_fecdes=""; }
	 $fechas=$_GET["txtfechas"];
	 if (!empty($fechas))
	 {
  	    $ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
	 }	else {  $ldt_fechas=""; }

	 $ls_orden=$_GET["rborden"];
	 /////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_desc_event="Solicitud de Reporte Comprobante Formato2 desde la fecha ".$fecdes." hasta ".$fechas." , Cuenta desde ".$ls_cuentades." hasta ".$ls_cuentahas;
	 $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_spg_r_comprobante_formato2.php",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////////////////////////////////////////////////
	//----------------------------------------------------  Parámetros del encabezado  -------------------------------------------------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
	    $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$ldt_fecdes_cab=$io_funciones->uf_convertirfecmostrar(substr($ldt_fecdes,0,10));
		$ldt_fechas_cab=$io_funciones->uf_convertirfecmostrar(substr($ldt_fechas,0,10));


	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
	 $lb_valido=$io_report->uf_spg_reporte_select_comprobante_formato2($ls_cuentades,$ls_cuentahas,$ldt_fecdes,$ldt_fechas,$rsDatos1);
 
	 
	
	 
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
	    
	    
	    /*
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(4.5,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_periodo_comp,$ls_fecha_comp,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		
		*/
	    
	    
	    		//$ls_titulo="COMPROBANTE PRESUPUESTARIO";
		$ls_titulo="Comprobantes Presupuestarios desde la Cuenta Nro.  ".trim($ls_cuentades)." al ".trim($ls_cuentahas)."";
		$ls_titulo1="Desde ".$ldt_fecdes_cab." al ".$ldt_fechas_cab."";
	    
	    //$ls_titulo=" ACUMULADO POR CUENTAS DESDE FECHA  ".$ldt_fecini_rep."  HASTA  ".$fecfin;
		//$ls_titulo1=" DESDE LA PROGRAMATICA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1;
		
		set_time_limit(0);
		$io_tcpdf= new sigesp_spg_class_tcpdf ("L", PDF_UNIT, "legal", true);
		$io_tcpdf->AliasNbPages();
		$io_tcpdf->SetFont("helvetica","BI",8);	
		$ls_mensaje = str_repeat(' ',80).$ls_titulo;
		$ls_mensaje2 = str_repeat(' ',130).$ls_titulo1.str_repeat(' ',70).date("d/m/Y").' '.date("h:i a").'-'.$_SESSION["ls_database"];
		$io_tcpdf->SetHeaderData($_SESSION["ls_logo"],20, $ls_mensaje, $ls_mensaje2);
		$io_tcpdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$io_tcpdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$io_tcpdf->SetMargins(2, PDF_MARGIN_TOP,2);
		$io_tcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$io_tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$io_tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$io_tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
		$io_tcpdf->AliasNbPages();
		$io_tcpdf->AddPage();	
		$io_tcpdf->SetFont("helvetica","B",8);
		
	 //	$io_tcpdf->Cell(0,10,$ls_titulo,0,0,'C');
	 	//$io_tcpdf->Cell(0,11,$ls_titulo,0,1,'C');
	 //	$io_tcpdf->Cell(0,11,$ls_periodo_comp,0,1,'C');
	 //	$io_tcpdf->Cell(0,11,$ls_fecha_comp,0,1,'C');
	 	
	 	

	 	
	 	
	 	
	 	
	 	$li_tot=$io_report->dts_cab->getRowCount("comprobante");
		$ld_total=0;
		$ld_totalcomprobante=0;
		$ld_totalprogramatica=0;
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		$li_i=0;
		$li_tot = $rsDatos1->RecordCount();
		while(!$rsDatos1->EOF)
		{	
			$ls_comprobante=$rsDatos1->fields["comprobante"];
			$ls_procede=$rsDatos1->fields["procede"];
			$ls_ced_bene=$rsDatos1->fields["ced_bene"];
			$ls_cod_pro=$rsDatos1->fields["cod_pro"];
			$ls_nompro=$rsDatos1->fields["nompro"];
			$ls_apebene=$rsDatos1->fields["apebene"];
			$ls_nombene=$rsDatos1->fields["nombene"];
			$ls_tipo_destino=$rsDatos1->fields["tipo_destino"];
		    
			if($ls_tipo_destino=="P")
		    {
			    $ls_nomprobene=$ls_nompro;
		    }
			if($ls_tipo_destino=="B")
			{
				$ls_nomprobene=$ls_apebene.", ".$ls_nombene;
			}
			if($ls_tipo_destino=="-" || $ls_tipo_destino=="")
			{
				$ls_nomprobene="";
			}
			
			//uf_print_cabecera($ls_procede,$ls_comprobante,$ls_nomprobene,$io_pdf); // Imprimimos la cabecera del registro
	 	
	 	  $io_tcpdf->uf_print_cabecera_comformato2($ls_procede,$ls_comprobante,$ls_nomprobene); // Imprimimos la cabecera del registro
	
	 	  $lb_valido=$io_report->uf_spg_reporte_comprobante_formato2($ls_cuentades,$ls_cuentahas,$ldt_fecdes,$ldt_fechas,
			                                                           $ls_comprobante,$ls_procede,$rsDatos2);                                
			if($lb_valido)
			{
				$li_s=0;
				$li_totrow_det=$rsDatos2->RecordCount();
				//$li_totrow_det=$io_report->dts_reporte->getRowCount("programatica");
				while(!$rsDatos2->EOF)
				{
					//var_dump($rsDatos2->fields);
					//die();
					$ls_procede=$rsDatos2->fields["procede"];
					$ls_comprobante=$rsDatos2->fields["comprobante"];
					$fecha=$rsDatos2->fields["fecha"];
					$fecha=date("Y-m-d",strtotime($fecha));
					$ldt_fecha=$io_funciones->uf_convertirfecmostrar($fecha);
					$ls_programatica=$rsDatos2->fields["codestpro1"].$rsDatos2->fields["codestpro2"].$rsDatos2->fields["codestpro3"].$rsDatos2->fields["codestpro4"].$rsDatos2->fields["codestpro5"];
		            $ls_codestpro1=substr($ls_programatica,0,25);
					$ls_codestpro2=substr($ls_programatica,25,25);
					$ls_codestpro3=substr($ls_programatica,50,25);

			   $lb_valido = $io_report->uf_spg_reporte_select_denestpro1($rsDatos2->fields["codestpro1"],$ls_denestpro1,$rsDatos2->fields["estcla"]);
			   if($lb_valido)
			   { //print "PASO 01";
			     $ls_denestpro1=$ls_denestpro1;
			   }
			   if($lb_valido)
			   { //print "PASO 02";
			     $lb_valido=$io_report->io_spg_report_funciones->uf_spg_reporte_select_denestpro2($rsDatos2->fields["codestpro1"],$rsDatos2->fields["codestpro2"],$ls_denestpro2,$rsDatos2->fields["estcla"]);
				 $ls_denestpro2=$ls_denestpro2;
			   }
			   if($lb_valido)
			   { //print "PASO 03";
			     $ls_denestpro3="";
			     $lb_valido=$io_report->io_spg_report_funciones->uf_spg_reporte_select_denestpro3($rsDatos2->fields["codestpro1"],$rsDatos2->fields["codestpro2"],$rsDatos2->fields["codestpro3"],$ls_denestpro3,$rsDatos2->fields["estcla"]);
				 $ls_denestpro3=$ls_denestpro3;
			   }
			   if($lb_valido)
			   { //print "PASO 04";
			     $ls_denestpro4="";
			     $lb_valido=$io_report->io_spg_report_funciones->uf_spg_reporte_select_denestpro4($rsDatos2->fields["codestpro1"],$rsDatos2->fields["codestpro2"],$rsDatos2->fields["codestpro3"],$rsDatos2->fields["codestpro4"],$ls_denestpro4,$rsDatos2->fields["estcla"]);
				 $ls_denestpro4=$ls_denestpro4;
			   }
			   if($lb_valido)
			   { //print "PASO 05";
			     $ls_denestpro5="";
			     $lb_valido=$io_report->io_spg_report_funciones->uf_spg_reporte_select_denestpro5($rsDatos2->fields["codestpro1"],$rsDatos2->fields["codestpro2"],$rsDatos2->fields["codestpro3"],$rsDatos2->fields["codestpro4"],$rsDatos2->fields["codestpro5"],$ls_denestpro5,$rsDatos2->fields["estcla"]);
				 $ls_denestpro5=$ls_denestpro5;
			   }
			   					
					$ls_denestpro=$ls_denestpro1." , ".$ls_denestpro2." , ".$ls_denestpro3;
					if($li_estmodest==2)
					{
						$ls_codestpro4=substr($ls_programatica,75,25);
						$ls_codestpro5=substr($ls_programatica,100,25);
						$ls_denestpro=trim($ls_denestpro1)." , ".trim($ls_denestpro2)." , ".trim($ls_denestpro3)." , ".trim($ls_denestpro4)." , ".trim($ls_denestpro5);
			            $ls_programatica=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
					}
					elseif($li_estmodest==1)
					{
						//$ls_denestpro=$ls_denestpro1." , ".$ls_denestpro2." , ".$ls_denestpro3;
						$ls_denestpro = array();
						$ls_denestpro[0]=$ls_denestpro1;
				        $ls_denestpro[1]=$ls_denestpro2;
				        $ls_denestpro[2]=$ls_denestpro3;
						$ls_programatica=substr($ls_codestpro1,-$ls_loncodestpro1).substr($ls_codestpro2,-$ls_loncodestpro2).substr($ls_codestpro3,-$ls_loncodestpro3);
					}
					$ls_spg_cuenta=$rsDatos2->fields["spg_cuenta"];
					$ls_documento=$rsDatos2->fields["documento"];
					$ls_operacion=$rsDatos2->fields["operacion"];
					$ls_descripcion=trim($rsDatos2->fields["descripcion"]);
					$ld_monto=$rsDatos2->fields["monto"];
					$ls_orden=$rsDatos2->fields["orden"];
					$ls_denominacion=trim($rsDatos2->fields["denominacion"]);
					$ls_denoperacion=$rsDatos2->fields["denoperacion"];
					$ls_denestpro5=$rsDatos2->fields["denestpro5"];
					$ls_tipo_destino=$rsDatos2->fields["tipo_destino"];
					$ls_cod_pro=$rsDatos2->fields["cod_pro"];
					$ls_ced_bene=$rsDatos2->fields["ced_bene"];
					$ls_nompro=$rsDatos2->fields["nompro"];
					$ls_apebene=$rsDatos2->fields["apebene"];
					$ls_nombene=$rsDatos2->fields["nombene"];
					
					$ls_ced_bene="";
					$ls_nompro="";
					$ls_apebene="";
					$ls_nombene="";

					$ld_totalprogramatica=$ld_totalprogramatica+$ld_monto;
					$ld_totalcomprobante=$ld_totalcomprobante+$ld_monto;
					$ld_total=$ld_total+$ld_monto;
					
					if($ld_monto<0)
					{
					  $ld_monto_positivo=abs($ld_monto);
					  $ld_monto=number_format($ld_monto_positivo,2,",",".");
					  $ld_monto="(".$ld_monto.")";
					}
					else
					{
					  $ld_monto=number_format($ld_monto,2,",",".");
					}
					
					$la_data[$li_s]=array('cuenta'=>$ls_spg_cuenta,'dencuenta'=>$ls_denominacion,'descripcion'=>$ls_descripcion,'fecha'=>$ldt_fecha,'operacion'=>$ls_denoperacion,'monto'=>$ld_monto);
					$ld_monto=str_replace('.','',$ld_monto);
					$ld_monto=str_replace(',','.',$ld_monto);
					$li_s++;
					$rsDatos2->MoveNext();
				}
				
			    $io_tcpdf->uf_print_cabecera_programatica($ls_programatica,$ls_denestpro); // Imprimimos la cabecera del registro
				$io_tcpdf->uf_print_titulos_comprobante2();//Bs
			    $io_tcpdf->uf_print_detalle_comprobante2($la_data,$par,'',''); // Imprimimos el detalle
				
			    //$io_tcpdf->uf_print_titulos_comprobante2();
			    
			    if($ld_totalprogramatica<0)
				{
				  $ld_monto_positivo=abs($ld_totalprogramatica);
				  $ld_totalprogramatica=number_format($ld_monto_positivo,2,",",".");
				  $ld_totalprogramatica="(".$ld_totalprogramatica.")";
				}
				else
				{
			       $ld_totalprogramatica=number_format($ld_totalprogramatica,2,",",".");
				}
                $ld_totalprogram=$ld_totalprogramatica;
			   // uf_print_total_programatica($ld_totalprogramatica,$io_pdf); // Imprimimos el total programatica//quitar
				if($ld_totalcomprobante<0)
				{
   				  //$ld_totalcomprobante_bsf=$io_rcbsf->uf_convertir_monedabsf($ld_totalcomprobante,$li_candeccon,$li_tipconmon,1000,$li_redconmon);	
				  $ld_monto_positivo=abs($ld_totalcomprobante);
				  $ld_totalcomprobante=number_format($ld_monto_positivo,2,",",".");
				  $ld_totalcomprobante="(".$ld_totalcomprobante.")";
				}
				else
				{
			       //$ld_totalcomprobante_bsf=$io_rcbsf->uf_convertir_monedabsf($ld_totalcomprobante, $li_candeccon,$li_tipconmon,1000,$li_redconmon);	
				   $ld_totalcomprobante=number_format($ld_totalcomprobante,2,",",".");
				}
			    $ld_totalcomprob=$ld_totalcomprobante;
				if($ls_tipoformato==1)
				{
					$io_tcpdf->uf_print_total_formato2($ld_totalcomprobante); // Imprimimos el total comprobante
				}
				else
				{
					$io_tcpdf->uf_print_total_formato2($ld_totalcomprobante); // Imprimimos el total comprobante
				}	
				$ld_totalcomprobante=0;
				$ld_totalprogramatica=0;
			}
	 	  $rsDatos1->MoveNext();
		}
	 	
	 	   
	 	   
	 	   
	 	   
	 	   
	 	   
	 	   
	 	
	 	
		//	$io_tcpdf->uf_print_cabecera_acumulado();
		//	$io_tcpdf->uf_print_detalle_acumulado($la_data); // Imprimimos el detalle
			
			unset($la_data);
			unset($la_data_tot);
			$io_tcpdf->Output("sigesp_spg_rpp_acum_x_comprobante2.pdf", "I");
			unset($io_tcpdf);	 	
	 	
	 	
	 	
	 	
	 	echo "AQUI TERMINO EL ciclo";
		die();
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
	}
	unset($io_report);
	unset($io_funciones);
	die();
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
?> 
