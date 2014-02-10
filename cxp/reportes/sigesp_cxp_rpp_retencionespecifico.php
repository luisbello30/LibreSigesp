<?php
session_start();

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//    REPORTE: Retencion Especifico
	//  ORGANISMO: Ninguno en particular
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 10/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Gener� el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesespecifico.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_fecdes,$as_fechas,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 08/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,510,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,530,11,$as_titulo); // Agregar el t�tulo
		$ls_periodo = "<b>Del :</b>".$as_fecdes."   "."<b>Al :</b>".$as_fechas;	
		$li_tm=$io_pdf->getTextWidth(11,$ls_periodo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,515,11,$ls_periodo); // Agregar el t�tulo
		$io_pdf->addText(700,550,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(706,543,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_codded,$as_dended,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_codded // C�digo de Deduccion
		//	    		   as_dended // Deenominaci�n de Deduccion
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 10/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo=" Bs.F.";
		}
		else
		{
			$ls_titulo=" Bs.";
		}
		$la_data   =array(array('retencion'=>'<b><i>Retenci�n:<i></b>','codigo'=>$as_codded,'denominacion'=>$as_dended));
		$la_columna=array('retencion'=>'','codigo'=>'','denominacion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'titleFontSize' =>10,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>2,
						 'shadeCol2'=>array(0.86,0.86,0.86),
						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('retencion'=>array('justification'=>'left','width'=>60),
						               'codigo'=>array('justification'=>'left','width'=>50), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>590))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);			
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('beneficiario'=>'<b>Proveedor/Beneficiario</b>','solicitud'=>'<b>Solicitud de Pago</b>','fecha'=>'<b>Fecha</b>','numcom'=>'<b>Comprobante','monto'=>'<b>Monto Objeto Retencion','porded'=>'<b>Alicuota de Retencion (%) </b>','retencion'=>'<b>Monto Retenido</b>'));
		$la_columna=array('beneficiario'=>'','solicitud'=>'','fecha'=>'','numcom'=>'','monto'=>'','porded'=>'','retencion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'titleFontSize' =>10,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0,
						 'shadeCol2'=>array(0.86,0.86,0.86),
						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('beneficiario'=>array('justification'=>'left','width'=>190),
						               'solicitud'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'numcom'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la columna
						 			   'porded'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la columna
						 			   'retencion'=>array('justification'=>'center','width'=>90))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_manual($as_dended,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_manual
		//		   Access: private 
		//	    Arguments: as_dended // Deenominaci�n de Deduccion
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 17/02/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo=" Bs.F.";
		}
		else
		{
			$ls_titulo=" Bs.";
		}
		$la_data   =array(array('retencion'=>'<b><i>Retenci�n Manual:<i></b>','denominacion'=>$as_dended));
		$la_columna=array('retencion'=>'','denominacion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'titleFontSize' =>10,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>2,
						 'shadeCol2'=>array(0.86,0.86,0.86),
						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('retencion'=>array('justification'=>'left','width'=>100),
						 			   'denominacion'=>array('justification'=>'left','width'=>600))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);			
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('beneficiario'=>'<b>Proveedor/Beneficiario</b>','fecha'=>'<b>Fecha</b>','numcom'=>'<b>Comprobante','monto'=>'<b>Monto Objeto Retencion','porded'=>'<b>Alicuota de Retencion (%) </b>','retencion'=>'<b>Monto Retenido</b>'));
		$la_columna=array('beneficiario'=>'','fecha'=>'','numcom'=>'','monto'=>'','porded'=>'','retencion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'titleFontSize' =>10,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0,
						 'shadeCol2'=>array(0.86,0.86,0.86),
						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('beneficiario'=>array('justification'=>'left','width'=>280),
						 			   'fecha'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'numcom'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la columna
						 			   'porded'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la columna
						 			   'retencion'=>array('justification'=>'center','width'=>90))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_cabecera_manual
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo con todos los datos 
		//				   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 10/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//var_dump($la_data);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' =>8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(1,1,1), // Color de la sombra
 						 'colGap'=>0,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('beneficiario'=>array('justification'=>'left','width'=>190),
						               'solicitud'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'numcom'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la columna
						 			   'porded'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la columna
						 			   'retencion'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la columna
		$la_columna=array('beneficiario'=>'','solicitud'=>'','fecha'=>'','numcom'=>'','monto'=>'','porded'=>'','retencion'=>'');
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_manual($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_manual
		//		   Access: private 
		//	    Arguments: la_data // Arreglo con todos los datos 
		//				   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 17/02/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//var_dump($la_data);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' =>8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(1,1,1), // Color de la sombra
 						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('beneficiario'=>array('justification'=>'left','width'=>280),
						 			   'fecha'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'numcom'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la columna
						 			   'porded'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la columna
						 			   'retencion'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la columna
		$la_columna=array('beneficiario'=>'','fecha'=>'','numcom'=>'','monto'=>'','porded'=>'','retencion'=>'');
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle_manual
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	 function uf_print_totales($ai_filas,$ai_total,$ai_totbase,&$io_pdf)
	 {
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: ai_filas // Total de Filas
		//				   ai_total // Monto total retenido
		//				   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los totales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 10/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo=" Bs.F.";
		}
		else
		{
			$ls_titulo=" Bs.";
		}
	    $la_data[1]=array('name'=>'_________________________________________________________________________________________________________________________');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'xPos'=>412, // Orientaci�n de la tabla
						 'width'=>700); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('cantidad'=>'<b>Total de Retenciones :</b>','filas'=>$ai_filas,'totales'=>'<b>Total Montos </b>','base'=>$ai_totbase,'vacio'=>'','monto'=>$ai_total);
		$la_data[2]=array('cantidad'=>'','filas'=>'','totales'=>'','base'=>'','vacio'=>'','monto'=>'');
		$la_data[3]=array('cantidad'=>'','filas'=>'','totales'=>'','base'=>'','vacio'=>'','monto'=>'');
	    $la_columna=array('cantidad'=>'','filas'=>'','totales'=>'','base'=>'','vacio'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' =>8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(1,1,1), // Color de la sombra
 						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cantidad'=>array('justification'=>'right','width'=>90),
						               'filas'=>array('justification'=>'left','width'=>20),
									   'totales'=>array('justification'=>'right','width'=>320),
									   'base'=>array('justification'=>'right','width'=>90),
									   'vacio'=>array('justification'=>'right','width'=>90),
									   'monto'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la columna
	    $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	 }// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	 function uf_print_totales_manual($ai_filas,$ai_total,$ai_totbase,&$io_pdf)
	 {
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales_manual
		//		   Access: private 
		//	    Arguments: ai_filas // Total de Filas
		//				   ai_total // Monto total retenido
		//				   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los totales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 10/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo=" Bs.F.";
		}
		else
		{
			$ls_titulo=" Bs.";
		}
	    $la_data[1]=array('name'=>'_________________________________________________________________________________________________________________________');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'xPos'=>412, // Orientaci�n de la tabla
						 'width'=>700); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('cantidad'=>'<b>Total de Retenciones :</b>','filas'=>$ai_filas,'totales'=>'<b>Total Montos </b>','base'=>$ai_totbase,'vacio'=>'','monto'=>$ai_total);
		$la_data[2]=array('cantidad'=>'','filas'=>'','totales'=>'','base'=>'','vacio'=>'','monto'=>'');
		$la_data[3]=array('cantidad'=>'','filas'=>'','totales'=>'','base'=>'','vacio'=>'','monto'=>'');
	    $la_columna=array('cantidad'=>'','filas'=>'','totales'=>'','base'=>'','vacio'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' =>8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(1,1,1), // Color de la sombra
 						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cantidad'=>array('justification'=>'right','width'=>90),
						               'filas'=>array('justification'=>'left','width'=>20),
									   'totales'=>array('justification'=>'right','width'=>320),
									   'base'=>array('justification'=>'right','width'=>90),
									   'vacio'=>array('justification'=>'right','width'=>90),
									   'monto'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la columna
	    $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	 }// end function uf_print_totales_manual
	//--------------------------------------------------------------------------------------------------------------------------------

	function iif($condition,$val_if_true,$val_if_false="")
	{
		if ($condition)
			return $val_if_true;
		else
			return $val_if_false;
	}
	//--------------------------------------------------------------------------------------------------------------------------------


	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>LISTADO DE RETENCIONES</b>";
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codded=$io_fun_cxp->uf_obtenervalor_get("codded","");//die;
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=$io_fun_cxp->uf_obtenervalor_get("codprobendes","");
	$ls_codprobenhas=$io_fun_cxp->uf_obtenervalor_get("codprobenhas","");
	$ld_fecdes=$io_fun_cxp->uf_obtenervalor_get("fecdes","");
	$ld_fechas=$io_fun_cxp->uf_obtenervalor_get("fechas","");
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	$ls_tipded=$io_fun_cxp->uf_obtenervalor_get("tipded","");
	$ls_tipper=$io_fun_cxp->uf_obtenervalor_get("tipper","");
	if($ls_tipper=="T")
		$ls_tipper="";
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_select_retenciones($ls_codded,"",$ls_tipded);
                
	}
	if($lb_valido===false)
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else
	{
		error_reporting(E_ALL);
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','landscape');
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm');
		$io_pdf->ezSetCmMargins(4.4,3,3,3);                          
		uf_print_encabezado_pagina($ls_titulo,$ld_fecdes,$ld_fechas,&$io_pdf);
		$li_rowcargos=$io_report->DS->getRowCount("codded");//print"s";
		$io_report->DS->sortData("codded");
		$lb_existe=false;
		for($li_j=1;$li_j<=$li_rowcargos;$li_j++)
		{
			$ls_codded= $io_report->DS->data["codded"][$li_j];
			$ls_dended= $io_report->DS->data["dended"][$li_j];
			$li_islr= $io_report->DS->data["islr"][$li_j];
			$li_iva= $io_report->DS->data["iva"][$li_j];
			$li_estretmun= $io_report->DS->data["estretmun"][$li_j];
			$li_retaposol= $io_report->DS->data["retaposol"][$li_j];
			$li_estretmil= $io_report->DS->data["estretmil"][$li_j];
			$lb_valido=$io_report->uf_retencionesespecifico($ls_codded,"",$ls_tipproben,$ls_codprobenhas,$ls_codprobendes,$ld_fecdes,$ld_fechas,$ls_tipper);
			$li_totbase=0;
			$li_totcargos=0;
			$li_totrow=$io_report->ds_detalle->getRowCount("numsol");
			$li_proveedor=0;
			for ($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_codded= $io_report->ds_detalle->data["codded"][$li_i];
				$ls_numsol= $io_report->ds_detalle->data["numsol"][$li_i];
				$ls_nombre= $io_report->ds_detalle->data["nombre"][$li_i];
				$ls_numcomiva= $io_report->ds_detalle->data["numcomiva"][$li_i];
				$ls_numcommun= $io_report->ds_detalle->data["numcommun"][$li_i];
				$ls_numcomapo= $io_report->ds_detalle->data["numcomapo"][$li_i];
				$ls_numcommil= $io_report->ds_detalle->data["numcommil"][$li_i];
				$ls_numcomislr= $io_report->ds_detalle->data["numcomislr"][$li_i];
				$li_porded=($io_report->ds_detalle->data["porded"][$li_i]*100);
				if($li_porded>100)
				{
					$li_porded=number_format($io_report->ds_detalle->data["porded"][$li_i],2,',','.');
				}
				else
				{
					$li_porded=number_format($li_porded,2,',','.');
				}
				$ld_fecemisol= $io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecemisol"][$li_i]);
				$li_monsol= number_format($io_report->ds_detalle->data["mon_obj_ret"][$li_i],2,',','.');
				$li_monret= number_format($io_report->ds_detalle->data["monret"][$li_i],2,',','.');
				$li_totbase= $li_totbase+$io_report->ds_detalle->data["mon_obj_ret"][$li_i];
				$li_totcargos= $li_totcargos+$io_report->ds_detalle->data["monret"][$li_i];
				$ls_numcom="";
//				print $ls_numcomislr."<br>";
				if($li_iva==1)
				{$ls_numcom=$ls_numcomiva;}
				if($li_estretmun==1)
				{$ls_numcom=$ls_numcommun;}
				if($li_retaposol==1)
				{$ls_numcom=$ls_numcomapo;}
				if($li_estretmil==1)
				{$ls_numcom=$ls_numcommil;}
				if($li_islr==1)
				{$ls_numcom=$ls_numcomislr;}
//				if (!empty($ls_numcom))
//				{print "ENTRO";
				//	$ls_numcom = iif(empty($ls_numcomiva),iif(empty($ls_numcommun),iif(empty($ls_numcomapo),'',$ls_numcomapo),$ls_numcommun),$ls_numcomiva);						
					$la_data[$li_proveedor]=array('beneficiario'=>$ls_nombre,'solicitud'=>$ls_numsol,'fecha'=>$ld_fecemisol,'numcom'=>$ls_numcom,'monto'=>'',
										  'porded'=>'','retencion'=>'');
					$li_proveedor++;
					$lb_valido=$io_report->uf_retencionesespecifico_detalle($ls_codded,$ls_numsol);
					while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
					{
						$ls_numsol= trim($io_report->rs_detalle->fields["numrecdoc"]);
						$ls_nombre="    N� FACTURA ".$ls_numsol."     N� Control ".trim($io_report->rs_detalle->fields["numref"]);
						$ls_numsol="" ;
						$ld_fecemisol= $io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecregdoc"]);
						$ls_numcom="";
						$li_monsol_det= number_format($io_report->rs_detalle->fields["monobjret"],2,',','.');
						$li_monret_det= number_format($io_report->rs_detalle->fields["monret"],2,',','.');
						$li_porded_det=($io_report->rs_detalle->fields["porded"]*100);
						if($li_porded_det>100)
						{
							$li_porded_det=number_format($io_report->rs_detalle->fields["porded"],2,',','.');
						}
						else
						{
							$li_porded_det=number_format($li_porded,2,',','.');
						}					
						$la_data[$li_proveedor]=array('beneficiario'=>$ls_nombre,'solicitud'=>$ls_numsol,'fecha'=>$ld_fecemisol,'numcom'=>$ls_numcom,'monto'=>$li_monsol_det,
											  'porded'=>$li_porded_det,'retencion'=>$li_monret_det);
						$li_proveedor++;
						$io_report->rs_detalle->MoveNext();
					}
					$la_data[$li_proveedor]=array('beneficiario'=>'','solicitud'=>'','fecha'=>'','numcom'=>'','monto'=>'____________________','porded'=>'____________________','retencion'=>'____________________');
					$li_proveedor++;
					$la_data[$li_proveedor]=array('beneficiario'=>'								<b>SUB TOTAL</b>','solicitud'=>'','fecha'=>'','numcom'=>'','monto'=>$li_monsol,
										  'porded'=>$li_porded,'retencion'=>$li_monret);
					$li_proveedor++;
					$la_data[$li_proveedor]=array('beneficiario'=>'','solicitud'=>'','fecha'=>'','numcom'=>'','monto'=>'','porded'=>'','retencion'=>'');
					$li_proveedor++;
//				}
			}
			if($li_proveedor>0)
			{  
				$lb_existe=true;
				uf_print_cabecera($ls_codded,$ls_dended,&$io_pdf);
				uf_print_detalle($la_data,&$io_pdf); // Imprimimos el detalle  
				$li_totbase=number_format($li_totbase,2,',','.');
				$li_totcargos=number_format($li_totcargos,2,',','.');
				uf_print_totales($li_totrow,$li_totcargos,$li_totbase,$io_pdf);
				unset($la_data);
			}
		}
		$la_manuales[0]="";
		switch($ls_tipded)
		{
			case "T":
				if($ls_codded=="")
				{
					$la_manuales[0]['codret']="";
					$la_manuales[1]['codret']="0000000001";
					$la_manuales[2]['codret']="0000000003";
					$la_manuales[3]['codret']="0000000004";
					$la_manuales[4]['codret']="0000000005";
	
					$la_manuales[0]['denret']="";
					$la_manuales[1]['denret']="IVA";
					$la_manuales[2]['denret']="MUNICIPAL";
					$la_manuales[3]['denret']="APORTE SOCIAL";
					$la_manuales[4]['denret']="1 X 1000";
				}
				else
				{
					if($li_iva==1)
					{
						$la_manuales[0]['codret']="";
						$la_manuales[1]['codret']="0000000001";
						$la_manuales[0]['denret']="";
						$la_manuales[1]['denret']="IVA";
					}
					if($li_estretmun==1)
					{
						$la_manuales[0]['codret']="";
						$la_manuales[1]['codret']="0000000003";
						$la_manuales[0]['denret']="";
						$la_manuales[1]['denret']="MUNICIPAL";
					}
					if($li_retaposol==1)
					{
						$la_manuales[0]['codret']="";
						$la_manuales[1]['codret']="0000000004";
						$la_manuales[0]['denret']="";
						$la_manuales[1]['denret']="APORTE SOCIAL";
					}
					if($li_estretmil==1)
					{
						$la_manuales[0]['codret']="";
						$la_manuales[1]['codret']="0000000005";
						$la_manuales[0]['denret']="";
						$la_manuales[1]['denret']="1 X 1000";
					}
				}
			break;
			case "I":
				$la_manuales[0]['codret']="";
				$la_manuales[1]['codret']="0000000001";
				$la_manuales[0]['denret']="";
				$la_manuales[1]['denret']="IVA";
			break;
			case "M":
				$la_manuales[0]['codret']="";
				$la_manuales[1]['codret']="0000000003";
				$la_manuales[0]['denret']="";
				$la_manuales[1]['denret']="MUNICIPAL";
			break;
			case "A":
				$la_manuales[0]['codret']="";
				$la_manuales[1]['codret']="0000000004";
				$la_manuales[0]['denret']="";
				$la_manuales[1]['denret']="APORTE SOCIAL";
			break;
			case "1":
				$la_manuales[0]['codret']="";
				$la_manuales[1]['codret']="0000000005";
				$la_manuales[0]['denret']="";
				$la_manuales[1]['denret']="1 X 1000";
			break;
		}
		$li_total=count($la_manuales);
		for($li_i=1; $li_i< $li_total; $li_i++)
		{
			$ls_codret=$la_manuales[$li_i]['codret'];
			$ls_denret=$la_manuales[$li_i]['denret'];
			$lb_valido=$io_report->uf_retencionesespecificomanual($ls_codret,$ls_tipproben,$ls_codprobenhas,$ls_codprobendes,$ld_fecdes,$ld_fechas,$ls_tipper);
			$li_totbase=0;
			$li_totcargos=0;
			$li_j=0;
			while (!$io_report->rs_detalle->EOF)
			{
				$ls_nombre=$io_report->rs_detalle->fields["nomsujret"];
				$ld_fecemisol= $io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecrep"]);
				$ls_numcom=$io_report->rs_detalle->fields["numcom"];
				$li_monsol= number_format($io_report->rs_detalle->fields["basimp"],2,',','.');
				$li_monret= number_format($io_report->rs_detalle->fields["totimp"],2,',','.');
				$li_porded=($io_report->rs_detalle->fields["porimp"]*100);
				$li_totbase=$li_totbase+$io_report->rs_detalle->fields["basimp"];
				$li_totcargos=$li_totcargos+$io_report->rs_detalle->fields["totimp"];
				if($li_porded>100)
				{
					$li_porded=number_format($io_report->rs_detalle->fields["porimp"],2,',','.');
				}
				else
				{
					$li_porded=number_format($li_porded,2,',','.');
				}
				$la_data[$li_j]=array('beneficiario'=>$ls_nombre,'fecha'=>$ld_fecemisol,'numcom'=>$ls_numcom,'monto'=>$li_monsol,'porded'=>$li_porded,'retencion'=>$li_monret);
				$li_j++;
				$io_report->rs_detalle->MoveNext();
			}
			if($li_j>0)
			{  
				$lb_existe=true;
				uf_print_cabecera_manual($ls_denret,&$io_pdf);
				uf_print_detalle_manual($la_data,&$io_pdf); // Imprimimos el detalle  
				$li_totbase=number_format($li_totbase,2,',','.');
				$li_totcargos=number_format($li_totcargos,2,',','.');
				uf_print_totales_manual(($li_j-1),$li_totcargos,$li_totbase,$io_pdf);
				unset($la_data);
			}
		}
		if(!$lb_existe)
		{
			$lb_valido=false;
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
		//	print(" close();");
			print("</script>");		
		}
		
		if($lb_valido) // Si no ocurrio ning�n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo alg�n error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
		//	print(" close();");
			print("</script>");		
		}
	//	unset($io_pdf);
	}
//	unset($io_report);
//	unset($io_funciones);
//	unset($io_fun_cxp);

?>

