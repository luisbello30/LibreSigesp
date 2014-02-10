<?php
    session_start();   
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if (!array_key_exists("la_logusr",$_SESSION))
	{
		 print "<script language=JavaScript>";
		 print "close();";
		 print "</script>";		
	}
    ini_set('memory_limit','512M');
	ini_set('max_execution_time ','0');
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_periodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Ing. Nelson Barraez
		// Fecha Creaci�n: 15/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Gener� el Reporte Libro de Compra para el periodo ".$as_periodo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_librocompra.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$ls_periodo,$ls_mes,$ls_ano,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Ttulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funcin que imprime los encabezados por pgina
		//	   Creado Por: Ing.Ing. Nelson Barraez
		// Fecha Creacin: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_denominacion="<b> en Bs.F.</b>";
		}
		else
		{
			$ls_denominacion="<b> en Bs.</b>";
		}
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		//$ls_periodo="<b>MES :</b>".$ls_mes." "."<b>A�O:</b>".$ls_ano;	
		$io_pdf->line(10,30,1000,30);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,535,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(16,$as_titulo.$ls_denominacion);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,550,16,$as_titulo.$ls_denominacion); // Agregar el ttulo
		$li_tm=$io_pdf->getTextWidth(14,$ls_periodo);
		$tm=510-($li_tm/2);
		$io_pdf->addText($tm,530,12,$ls_periodo); // Agregar el ttulo
		$io_pdf->ezSetY(500);
		$la_data   =array(array('name1'=>'<b>Compras Internas o Importaciones</b>'));
		$la_columna=array('name1'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						  'fontSize' => 6,       // Tamao de Letras
						  'titleFontSize' => 8, // Tamao de Letras de los ttulos
						  'showLines'=>1,        // Mostrar Lneas
						  'shaded'=>0,           // Sombra entre lneas
						  'xPos'=>880,
						  'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						  'xOrientation'=>'center', // Orientacin de la tabla
						  'width'=>150, // Ancho de la tabla
						  'maxWidth'=>150,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>150))); // Justificacin y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		uf_print_cabecera(&$io_pdf);

		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo(&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funcin que imprime la cabecera de cada pgina
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera(&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funcin que imprime la cabecera de cada pgina
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('name20'=>'<b>Nro Cheque</b>','name1'=>'<b>Nro Oper</b>',
							 'name2'=>'<b>Fecha Factura</b>','name3'=>'<b>RIF</b>',
							 'name4'=>'<b>Nombre o Razon Social</b>','name5'=>'<b>Nro Compr.</b>',
							 'name51'=>'<b>Tipo Prov.</b>','name6'=>'<b>Nro Planilla Importacin  (C-80 o c-81)</b>','name7'=>'<b>Nro Expediente Importacin</b>','name8'=>'<b>Nro de Factura</b>','name9'=>'<b>Nro de Control</b>','name10'=>'<b>Nro Nota Dbito</b>','name11'=>'<b>Nro Nota Crdito</b>','name12'=>'<b>Tipo de Transacc.</b>','name13'=>'<b>Nro de Factura Afectada</b>','name14'=>'<b>Total de Compra Incluyendo IVA</b>','name15'=>'<b>Compra sin Derecho a Crdito IVA</b>','name16'=>'<b>Base Imponible</b>','name17'=>'<b>%</b>','name18'=>'<b>Impuesto Iva</b>','name19'=>'<b>IVA Retenido (Vendedor)</b>'));
		$la_columna=array('name20'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'','name5'=>'','name51'=>'','name6'=>'','name7'=>'','name8'=>'','name9'=>'','name10'=>'','name11'=>'','name12'=>'','name13'=>'','name14'=>'','name15'=>'','name16'=>'','name17'=>'','name18'=>'','name19'=>'');
		$la_config=array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 5,       // Tamao de Letras
						 'titleFontSize' => 5, // Tamao de Letras de los ttulos
						 'showLines'=>1,        // Mostrar Lneas
						 'shaded'=>0,           // Sombra entre lneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientacin de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990,
						 'colGap'=>0.5,
						 'cols'=>array('name20'=>array('justification'=>'center','width'=>50),
						 			   'name1'=>array('justification'=>'center','width'=>15), // Justificacin y ancho de la columna Nro de Operacion.
						 			   'name2'=>array('justification'=>'left','width'=>28), // Justificacin y ancho de la columna RIF.
						 			   'name3'=>array('justification'=>'center','width'=>50), // Justificacin y ancho de la columna Nmero de la Fcatura.
						 			   'name4'=>array('justification'=>'left','width'=>120), // Justificacin y ancho de la columna Nombre o Razn Social.
									   'name5'=>array('justification'=>'center','width'=>45), // Justificacin y ancho de la columna Nro de Comprobante.
									   'name51'=>array('justification'=>'center','width'=>20), // Justificacin y ancho de la columna Nro de Comprobante.
						 			   'name6'=>array('justification'=>'center','width'=>35), // Justificacin y ancho de la columna Nro de Planilla de Importacin.
						 			   'name7'=>array('justification'=>'center','width'=>40), // Justificacin y ancho de la columna Nro Expediente de Importacin.
						 			   'name8'=>array('justification'=>'center','width'=>45), // Justificacin y ancho de la columna Nro de Factura.   
									   'name9'=>array('justification'=>'center','width'=>45), // Justificacin y ancho de la columna Nro de Control.
						 			   'name10'=>array('justification'=>'center','width'=>40), // Justificacin y ancho de la columna
						 			   'name11'=>array('justification'=>'center','width'=>45), // Justificacin y ancho de la columna
						 			   'name12'=>array('justification'=>'center','width'=>25), // Justificacin y ancho de la columna
									   'name13'=>array('justification'=>'center','width'=>45), // Justificacin y ancho de la columna
						 			   'name14'=>array('justification'=>'center','width'=>60), // Justificacin y ancho de la columna
						 			   'name15'=>array('justification'=>'center','width'=>70), // Justificacin y ancho de la columna
						 			   'name16'=>array('justification'=>'center','width'=>70), // Justificacin y ancho de la columna
									   'name17'=>array('justification'=>'center','width'=>20), // Justificacin y ancho de la columna
						 			   'name18'=>array('justification'=>'center','width'=>60), // Justificacin y ancho de la columna
						 			   'name19'=>array('justification'=>'center','width'=>35))); // Justificacin y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informacin
		//	   			   io_pdf // Objeto PDF
		//    Description: funcin que imprime el detalle
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 5, // Tamao de Letras
						 'titleFontSize' => 10,  // Tamao de Letras de los ttulos
						 'showLines'=>1, // Mostrar Lneas
						 'shaded'=>0, // Sombra entre lneas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Mximo de la tabla
						 'xOrientation'=>'center', // Orientacin de la tabla
 						 'colGap'=>0.5,
						 'cols'=>array('name20'=>array('justification'=>'center','width'=>50),
						 			   'name1'=>array('justification'=>'center','width'=>15), // Justificacin y ancho de la columna Nro de Operacion.
						 			   'name2'=>array('justification'=>'left','width'=>28), // Justificacin y ancho de la columna RIF.
						 			   'name3'=>array('justification'=>'center','width'=>50), // Justificacin y ancho de la columna Nmero de la Fcatura.
						 			   'name4'=>array('justification'=>'left','width'=>120), // Justificacin y ancho de la columna Nombre o Razn Social.
									   'name5'=>array('justification'=>'center','width'=>45), // Justificacin y ancho de la columna Nro de Comprobante.
									   'name51'=>array('justification'=>'center','width'=>20), // Justificacin y ancho de la columna Nro de Comprobante.
						 			   'name6'=>array('justification'=>'center','width'=>35), // Justificacin y ancho de la columna Nro de Planilla de Importacin.
						 			   'name7'=>array('justification'=>'center','width'=>40), // Justificacin y ancho de la columna Nro Expediente de Importacin.
						 			   'name8'=>array('justification'=>'center','width'=>45), // Justificacin y ancho de la columna Nro de Factura.   
									   'name9'=>array('justification'=>'center','width'=>45), // Justificacin y ancho de la columna Nro de Control.
						 			   'name10'=>array('justification'=>'center','width'=>40), // Justificacin y ancho de la columna
						 			   'name11'=>array('justification'=>'center','width'=>45), // Justificacin y ancho de la columna
						 			   'name12'=>array('justification'=>'center','width'=>25), // Justificacin y ancho de la columna
									   'name13'=>array('justification'=>'center','width'=>45), // Justificacin y ancho de la columna
						 			   'name14'=>array('justification'=>'right','width'=>60), // Justificacin y ancho de la columna
						 			   'name15'=>array('justification'=>'right','width'=>70), // Justificacin y ancho de la columna
						 			   'name16'=>array('justification'=>'right','width'=>70), // Justificacin y ancho de la columna
									   'name17'=>array('justification'=>'right','width'=>20), // Justificacin y ancho de la columna
						 			   'name18'=>array('justification'=>'right','width'=>60), // Justificacin y ancho de la columna
						 			   'name19'=>array('justification'=>'right','width'=>35))); // Justificacin y ancho de la columna
		$la_columna=array('name20'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'','name5'=>'','name51'=>'','name6'=>'',
						  'name7'=>'','name8'=>'','name9'=>'','name10'=>'','name11'=>'','name12'=>'',
						  'name13'=>'','name14'=>'','name15'=>'','name16'=>'','name17'=>'','name18'=>'','name19'=>'');
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_table_default($ld_monto,$ad_totbasimp,$ad_totimpuestos,$ld_sumcom,$ad_totcomsiniva,$ad_totimpred,$ad_totbasimp8,$ad_basimpga,$ad_totgenadi,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function:  uf_print_pie_cabecera
		//		   Access:  private 
		//	    Arguments:  ai_totprenom // Total Prenmina
		//	   			    ai_totant // Total Anterior
		//	    		    io_pdf // Instancia de objeto pdf
		//    Description:  funcin que imprime el fin de la cabecera de cada pgina
		//	   Creado Por:  Ing. Nelson Barraez
		// Fecha Creacin:  27/04/2006    Fecha ltima actualizacin:27/04/2006. 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		

		$ld_monto   = number_format($ld_monto,2,',','.');
		$la_data[0] = array('name1'=>'','name2'=>'','name3'=>'<b>Base Imponible</b>','name4'=>'','name5'=>'<b>Credito Fiscal</b>','name6'=>'<b>Iva Retenido a Terceros</b>','name7'=>'<b>Anticipo IVA</b>');
		$la_data[1] = array('name1'=>'Total: Compras Exentas y/o sin derecho a credito fiscal','name2'=>'30','name3'=>$ad_totcomsiniva,'name4'=>'','name5'=>'','name6'=>'','name7'=>'');
	    $la_data[2] = array('name1'=>'E de las: Compras Importacion Afectadas Alicuota General','name2'=>'31','name3'=>'','name4'=>'32','name5'=>'','name6'=>'','name7'=>'');
	    $la_data[3] = array('name1'=>'E de las: Compras Importacion Afectadas en Alicuota General + Adicional','name2'=>'312','name3'=>'','name4'=>'322','name5'=>'','name6'=>'','name7'=>'');
	    $la_data[4] = array('name1'=>'E de las: Compras Importacion Afectadas en Alicuota Reducida','name2'=>'313','name3'=>'','name4'=>'323','name5'=>'','name6'=>'','name7'=>'');
	    $la_data[5] = array('name1'=>'E de las: Compras Internas Afectadas solo en Alicuota General','name2'=>'33','name3'=>$ad_totbasimp,'name4'=>'34','name5'=>$ad_totimpuestos,'name6'=>'','name7'=>'');
	    $la_data[6] = array('name1'=>'E de las: Compras Internas Afectadas en Alicuota General + Adicional','name2'=>'332','name3'=>"0,00",'name4'=>'342','name5'=>"0,00",'name6'=>'','name7'=>'');
	    $la_data[7] = array('name1'=>'E de las: Compras Internas Afectadas en Alicuota Reducida','name2'=>'333','name3'=>$ad_totbasimp8,'name4'=>'343','name5'=>$ad_totimpred,'name6'=>'','name7'=>'');
	    $la_data[8] = array('name1'=>'','name2'=>'35','name3'=>'','name4'=>'36','name5'=>'','name6'=>'','name7'=>'');
		$la_columna = array('name1'=>'','name2'=>'','name3'=>'','name4'=>'','name5'=>'','name6'=>'','name7'=>'');
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
						    'fontSize' => 8, // Tamao de Letras
						   'titleFontSize' => 10,  // Tamao de Letras de los ttulos
						   'showLines'=>1, // Mostrar Lneas
						   'shaded'=>0, // Sombra entre lneas
						   'width'=>970, // Ancho de la tabla
						   'maxWidth'=>970, // Ancho Mximo de la tabla
						   'xOrientation'=>'center', // Orientacin de la tabla
						   'cols'=>array('name0'=>array('justification'=>'center','width'=>300,'showLines'=>1),
						                 'name1'=>array('justification'=>'left','width'=>300), // Justificacin y ancho de la columna Nro de Operacion.
						 			     'name2'=>array('justification'=>'center','width'=>25), // Justificacin y ancho de la columna RIF.
						 			     'name3'=>array('justification'=>'right','width'=>100), // Justificacin y ancho de la columna Nmero de la Fcatura.
						 			     'name4'=>array('justification'=>'center','width'=>25), // Justificacin y ancho de la columna Nombre o Razn Social.
									     'name5'=>array('justification'=>'right','width'=>110), // Justificacin y ancho de la columna Nro de Comprobante.
						 			     'name6'=>array('justification'=>'right','width'=>110), // Justificacin y ancho de la columna Nro de Planilla de Importacin.
						 			     'name7'=>array('justification'=>'center','width'=>110),
									     'name8'=>array('justification'=>'center','width'=>60))); // Justificacin y ancho de la columna Nro Expediente de Importacin.
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_table_default
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_table_totales($ldec_totcomsiniva,$ldec_totimp8,$ldec_totimp9,$ldec_totimp25,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function:  uf_print_table_totales
		//		   Access:  private 
		//	    Arguments:  ai_totprenom // Total Prenmina
		//	   			    ai_totant // Total Anterior
		//	    		    io_pdf // Instancia de objeto pdf
		//    Description:  funcin que imprime el fin de la cabecera de cada pgina
		//	   Creado Por:  Ing. Nelson Barraez
		// Fecha Creacin:  27/04/2006    Fecha ltima actualizacin:27/04/2006. 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		

		$la_data[0] = array('name1'=>'Compras Exentas:','name2'=>$ldec_totcomsiniva);
		$la_data[1] = array('name1'=>'Total del 8% de IVA Retenido:','name2'=>$ldec_totimp8);
	    $la_data[2] = array('name1'=>'Total del 11% de IVA Retenido','name2'=>$ldec_totimp9);
	    $la_data[3] = array('name1'=>'Total del 25% de IVA Retenido','name2'=>$ldec_totimp25);
		$la_columna = array('name1'=>'','name2'=>'');
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
						    'fontSize' => 8, // Tamao de Letras
						   'titleFontSize' => 10,  // Tamao de Letras de los ttulos
						   'showLines'=>2, // Mostrar Lneas
						   'shaded'=>0, // Sombra entre lneas
						   'width'=>970, // Ancho de la tabla
						   'maxWidth'=>970, // Ancho Mximo de la tabla
						   'xPos'=>'center', // Orientacin de la tabla
						   'cols'=>array('name1'=>array('justification'=>'left','width'=>200), // Justificacin y ancho de la columna Nro de Operacion.
						 			     'name2'=>array('justification'=>'right','width'=>150))); // Justificacin y ancho de la columna Nro Expediente de Importacin.
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_table_totales
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($ldec_totcomsiniva,$ldec_basimpga,$ldec_totcomconiva,$ldec_totimpiva,$ldec_totivaret,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funcin que imprime la cabecera de cada pgina
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('name13'=>'<b>TOTALES</b>','name14'=>$ldec_totcomconiva,'name15'=>$ldec_totcomsiniva,'name16'=>$ldec_basimpga,'name17'=>'','name18'=>$ldec_totimpiva,'name19'=>$ldec_totivaret));
		$la_columna=array('name13'=>'','name14'=>'','name15'=>'','name16'=>'','name17'=>'','name18'=>'','name19'=>'');
		$la_config=array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 5,       // Tamao de Letras
						 'titleFontSize' => 5, // Tamao de Letras de los ttulos
						 'showLines'=>1,        // Mostrar Lneas
						 'shaded'=>0,           // Sombra entre lneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientacin de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990,
						 'colGap'=>0.5,
						 'cols'=>array('name13'=>array('justification'=>'right','width'=>648), // Justificacin y ancho de la columna
						 			   'name14'=>array('justification'=>'right','width'=>60), // Justificacin y ancho de la columna
						 			   'name15'=>array('justification'=>'right','width'=>70), // Justificacin y ancho de la columna
						 			   'name16'=>array('justification'=>'right','width'=>70), // Justificacin y ancho de la columna
									   'name17'=>array('justification'=>'right','width'=>20), // Justificacin y ancho de la columna
						 			   'name18'=>array('justification'=>'right','width'=>60), // Justificacin y ancho de la columna
						 			   'name19'=>array('justification'=>'right','width'=>35))); // Justificacin y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	require_once("../../shared/class_folder/sigesp_include.php");
	require_once("../../shared/class_folder/class_sql.php");
	require_once("../../shared/class_folder/class_fecha.php");
    $io_fecha = new class_fecha();
	$io_in    = new sigesp_include();
	$con      = $io_in->uf_conectar();
    $io_sql   = new class_sql($con);
	$io_report= new sigesp_cxp_class_report("../../");
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();		
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();		
	//----------------------------------------------------  Parmetros del encabezado  -----------------------------------------------
	$ls_mes=$io_fun_cxp->uf_obtenervalor_get("mes","");
	$ls_agno=$io_fun_cxp->uf_obtenervalor_get("agno","");
	$ls_tiprep=$io_fun_cxp->uf_obtenervalor_get("tiprep","M");
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	$ls_titulo     = "<b>Libro de Compras</b>";
	$li_lastday    = $io_fecha->uf_last_day($ls_mes,$ls_agno);
	$li_lastday    = substr($li_lastday,0,2);
	$ls_mesletras        = $io_fecha->uf_load_nombre_mes($ls_mes);
	switch($ls_tiprep)
	{
		case"M":
			$as_fechadesde = $ls_agno.'-'.$ls_mes.'-01';
			$as_fechahasta = $ls_agno.'-'.$ls_mes.'-'.$li_lastday;
			$ls_periodo    = "MENSUAL  MES: ".$ls_mesletras."    A�O".$ls_agno."";
		break;
		case"PQ":
			$as_fechadesde = $ls_agno.'-'.$ls_mes.'-01';
			$as_fechahasta = $ls_agno.'-'.$ls_mes.'-15';
			$ls_periodo    = "PRIMERA QUINCENA   MES: ".$ls_mesletras."    A�O".$ls_agno."";
		break;
		case"SQ":
			$as_fechadesde = $ls_agno.'-'.$ls_mes.'-16';
			$as_fechahasta = $ls_agno.'-'.$ls_mes.'-'.$li_lastday;
			$ls_periodo    = "SEGUNDA QUINCENA MES: ".$ls_mesletras."    A�O".$ls_agno."";
		break;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$ld_monto    = 0;
	$ld_impuesto = 0;
	$ld_sumcom   = 0;
	$ld_baseimp  = 0;
	$arremp      = $_SESSION["la_empresa"];
    $ls_codemp   = $arremp["codemp"];
	error_reporting(E_ALL);
	set_time_limit(1800);
	$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->ezSetCmMargins(5.1,3,2.5,2.5); // Configuracin de los margenes en centmetros
	$io_pdf->ezStartPageNumbers(970,40,10,'','',1); // Insertar el nmero de pgina
	$lb_valido=$io_report->uf_select_report_libcompra($as_fechadesde,$as_fechahasta,&$rs_resultado);
	uf_print_encabezado_pagina($ls_titulo,$ls_periodo,$ls_mesletras,$ls_agno,&$io_pdf);
	$ldec_totimp8      = 0; //Variable de tipo acumulador que almacenara el monto total de los impuestos a 8%.
	$ldec_totimp9      = 0; //Variable de tipo acumulador que almacenara el monto total de los impuestos a 14%.
	$ldec_totimp25     = 0; //Variable de tipo acumulador que almacenara el monto total de los impuestos a 25%.
	$ldec_totimpret8   = 0; //Variable de tipo acumulador que almacenara el monto total retenido de los impuestos a 8%.
	$ldec_totimpret9   = 0; //Variable de tipo acumulador que almacenara el monto total retenido de los impuestos a 14%.
	$ldec_totimpret25  = 0; //Variable de tipo acumulador que almacenara el monto total retenido de los impuestos a 25%.
	$ldec_totbasimp8   = 0;
	$ldec_totbasimp9   = 0;
	$ldec_totbasimp25  = 0;
	$ldec_totcomsiniva = 0;
	$ldec_totcomconiva  = 0; //Variable de tipo acumulador que almacenara el monto total retenido de los impuestos a 25%.
	$ldec_totimpiva   = 0;
	$ldec_totivaret   = 0;
        //var_dump($lb_valido);
	if($lb_valido)
	{
		$li=0;
		$li_aux=0;
		while($row=$io_report->io_sql->fetch_row($rs_resultado))	
		{
			$li++;
			$ls_numnc="";
			$ls_numnd="";
                        $ldec_monret=0;
			$ldec_totbaseimp=0;
			$ldec_totmonimp=0;
			$ls_numrecdoc=$row["numrecdoc"];
			$ls_tipproben=$row["tipproben"];
			$ls_codpro=$row["cod_pro"];
			$ls_cedben=$row["ced_bene"];
			$ldec_montoret=$row["monret"];
			$ldec_montodoc=$row["montotdoc"];
			$ldec_mondeddoc=$row["mondeddoc"];
			$ls_codtipdoc =$row["codtipdoc"];
			$li_total_cargos=0;
			if($ls_tipproben=='P')
			{
				$la_provben=$io_report->uf_select_rowdata($io_sql,"SELECT * FROM rpc_proveedor WHERE cod_pro='".$ls_codpro."'");
				$ls_rif=$la_provben["rifpro"];
				$ls_nombre=$la_provben["nompro"];
			}	
			else
			{
				$la_provben=$io_report->uf_select_rowdata($io_sql,"SELECT * FROM rpc_beneficiario WHERE ced_bene='".$ls_cedben."'");
				$ls_rif=$la_provben["rifben"];
				$ls_nombre=$la_provben["nombene"]." ".$la_provben["apebene"];
			}
			$ls_cheque=$io_report->uf_select_data($io_sql,"SELECT distinct cxp_sol_banco.numdoc AS numdoc".
														  "  FROM cxp_dt_solicitudes, cxp_sol_banco".
														  " WHERE cxp_dt_solicitudes.codemp='".$ls_codemp."'".
														  "   AND cxp_dt_solicitudes.numrecdoc='".$ls_numrecdoc."'".
														  "   AND cxp_dt_solicitudes.cod_pro='".$ls_codpro."'".
														  "   AND cxp_dt_solicitudes.ced_bene='".$ls_cedben."'".
														  "   AND cxp_dt_solicitudes.codemp=cxp_sol_banco.codemp".
														  "   AND cxp_dt_solicitudes.numsol=cxp_sol_banco.numsol","numdoc");
	if($ls_tiporeporte==1)
	{
			$la_cmpret=$io_report->uf_select_rowdata($io_sql,"SELECT DISTINCT max(a.numrecdoc) as numrecdoc,max(a.monobjretaux) as monobjret,SUM(a.monretaux) as monret,max(a.porded) as porded,max(b.codret) as codret,max(b.numcom) as numcom,max(b.iva_retaux) as iva_ret,max(tiptrans) as tiptrans".
															  "   FROM cxp_rd_deducciones a,scb_dt_cmp_ret b                                                                         ".
															  "  WHERE a.codemp=b.codemp AND a.numrecdoc=b.numfac AND a.codemp='".$ls_codemp."' AND a.numrecdoc='".$ls_numrecdoc."' AND ".
															  "        a.cod_pro='".$ls_codpro."' AND a.ced_bene='".$ls_cedben."'                                              ".
															  " GROUP BY a.numrecdoc ");
	}
	else
	{
    		 // Consulta colocada en comentario por Ofimatica de Venezuela el 30-06-2011, por estar mal estructurada y no traer los datos de forma consistente. 
	         //$la_cmpret=$io_report->uf_select_rowdata($io_sql,"SELECT DISTINCT max(a.numrecdoc) as numrecdoc,max(a.monobjretaux) as monobjret,SUM(a.monretaux) as monret,max(a.porded) as porded,max(b.codret) as codret,max(b.numcom) as numcom,max(b.iva_retaux) as iva_ret,max(tiptrans) as tiptrans".
			 //												  "   FROM cxp_rd_deducciones a,scb_dt_cmp_ret b                                                                         ".
			 //												  "  WHERE a.codemp=b.codemp AND a.numrecdoc=b.numfac AND a.codemp='".$ls_codemp."' AND a.numrecdoc='".$ls_numrecdoc."' AND ".
			 //												  "        a.cod_pro='".$ls_codpro."' AND a.ced_bene='".$ls_cedben."'                                              ".
			 //												  " GROUP BY a.numrecdoc ");
	         // Fin de lo colocado en comentario por Ofimatica de Venezuela
	
	        // Consulta cambiada por Ofimatica de Venezuela el 30-06-2011 y ajustada nuavemente el 06-07-2011, ya que la que se estaba utilizando no traia la informacion de forma correcta, ya que fue mal estructurada. 
			$la_cmpret=$io_report->uf_select_rowdata($io_sql,"SELECT cxp_rd_deducciones.numrecdoc as numrecdoc, cxp_rd_deducciones.monobjret as monobjret, SUM(cxp_rd_deducciones.monret) as monret, cxp_rd_deducciones.porded as porded, scb_dt_cmp_ret.codret as codret, ".
                                                             "       scb_dt_cmp_ret.numcom as numcom, scb_dt_cmp_ret.iva_ret as iva_ret, scb_dt_cmp_ret.tiptrans as tiptrans ".
  															 "FROM cxp_rd_deducciones, cxp_rd, cxp_dt_solicitudes, sigesp_deducciones, scb_cmp_ret, scb_dt_cmp_ret ".
															 "WHERE (cxp_rd_deducciones.codemp = '".$ls_codemp."' AND cxp_rd_deducciones.numrecdoc = '".$ls_numrecdoc."' AND cxp_rd_deducciones.codtipdoc = '".$ls_codtipdoc."' AND cxp_rd_deducciones.cod_pro = '".$ls_codpro."' AND cxp_rd_deducciones.ced_bene = '".$ls_cedben."') AND ".
															 "      (cxp_rd_deducciones.codemp = cxp_rd.codemp AND cxp_rd_deducciones.numrecdoc = cxp_rd.numrecdoc AND cxp_rd_deducciones.codtipdoc = cxp_rd.codtipdoc AND cxp_rd_deducciones.cod_pro = cxp_rd.cod_pro AND cxp_rd_deducciones.ced_bene = cxp_rd.ced_bene) AND ".
															 "      (cxp_rd_deducciones.codemp = cxp_dt_solicitudes.codemp AND cxp_rd_deducciones.numrecdoc = cxp_dt_solicitudes.numrecdoc AND cxp_rd_deducciones.codtipdoc = cxp_dt_solicitudes.codtipdoc AND cxp_rd_deducciones.cod_pro = cxp_dt_solicitudes.cod_pro AND cxp_rd_deducciones.ced_bene = cxp_dt_solicitudes.ced_bene) AND ". 
															 "      (cxp_rd_deducciones.codded = sigesp_deducciones.codded AND sigesp_deducciones.iva = 1) AND ".
															 "      (cxp_rd_deducciones.codemp = scb_dt_cmp_ret.codemp AND cxp_rd_deducciones.numrecdoc = scb_dt_cmp_ret.numfac AND substring(scb_dt_cmp_ret.numsop,1,15) = cxp_dt_solicitudes.numsol) AND ". 
															 "       scb_dt_cmp_ret.numcom = scb_cmp_ret.numcom AND scb_cmp_ret.estcmpret = '1' AND scb_dt_cmp_ret.codret = '0000000001' AND".
															 "      (scb_cmp_ret.codsujret = (CASE cxp_rd_deducciones.ced_bene WHEN '----------' THEN cxp_rd_deducciones.cod_pro ELSE cxp_rd_deducciones.ced_bene END) AND cxp_rd_deducciones.codemp = scb_cmp_ret.codemp) AND ".
															 "       substring(scb_cmp_ret.perfiscal,1,4) = substring(CAST(cxp_rd.fecregdoc as text),1,4) AND substring(scb_cmp_ret.perfiscal,5,2) = substring(CAST(cxp_rd.fecregdoc as text),6,2) AND scb_cmp_ret.estcmpret = '1' AND ".
															 "       scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ".
															 "GROUP BY  cxp_rd_deducciones.numrecdoc , cxp_rd_deducciones.monobjret ,  cxp_rd_deducciones.porded , scb_dt_cmp_ret.codret , ".
															 "          scb_dt_cmp_ret.numcom, scb_dt_cmp_ret.iva_ret , scb_dt_cmp_ret.tiptrans ");
		    // Fin de la consulta cambiaba
 	}
			if(count($la_cmpret)>0)
			{
				$ls_codret = $la_cmpret["codret"];
				if ($ls_codret=='0000000001')
				{
					 $ldec_monret    = $la_cmpret["monret"];  
					 $ls_cmpret      = $la_cmpret["numcom"];
					 $ldec_monobjret = $la_cmpret["monobjret"];
					 $ldec_porded    = $la_cmpret["porded"];
					 $ldec_ivaret    = $la_cmpret["iva_ret"];
					 $ls_tiptrans    = $la_cmpret["tiptrans"];
				}
				else
				{
					 $ldec_monret    = 0; 
					 $ls_cmpret      = '';
					 $ldec_monobjret = 0;
					 $ldec_porded    = 0;
					 $ldec_ivaret    = 0;
 					 $ls_tiptrans    = "";
				}													  
			}
		    else
			{
				$ldec_monret    = $ldec_monret+$ldec_montoret;  
				$ls_cmpret      = '';
				$ldec_monobjret = 0;
				$ldec_porded    = 0;
				$ls_tiptrans    = "";
				$ldec_ivaret    = 0;
			}
			//Consultas Modificadas por Ofimatica de Venezuela el 23-05-2011, se elimina el c�digo del cargo y se agrupa y suma por el tipo de iva y se 
			//cambia el metodo uf_select_rowdata por uf_select_arraydata porque pueden aplicar varios ivas distintos.		
			if($ls_tiporeporte==1)
			{
				$la_cargos=$io_report->uf_select_arraydata($io_sql,"SELECT cxp_rd_cargos.monobjretaux as basimp,cxp_rd_cargos.porcar,cxp_rd_cargos.monretaux as impiva, tipo_iva".
																 "  FROM cxp_rd_cargos,sigesp_cargos ".
																 " WHERE cxp_rd_cargos.codemp='".$ls_codemp."'".
																 "   AND cxp_rd_cargos.numrecdoc='".$ls_numrecdoc."'".
																 "   AND cxp_rd_cargos.cod_pro='".$ls_codpro."'".
																 "   AND cxp_rd_cargos.ced_bene='".$ls_cedben."'");
			}
			else
			{
				$la_cargos=$io_report->uf_select_arraydata($io_sql,"SELECT SUM(cxp_rd_cargos.monobjret) as basimp,MAX(cxp_rd_cargos.porcar) AS porcar,SUM(cxp_rd_cargos.monret) as impiva, tipo_iva".
																 "  FROM cxp_rd_cargos,sigesp_cargos ".
																 " WHERE cxp_rd_cargos.codcar=sigesp_cargos.codcar ".
																 "   AND cxp_rd_cargos.codemp='".$ls_codemp."'".
																 "   AND cxp_rd_cargos.numrecdoc='".$ls_numrecdoc."'".
																 "   AND cxp_rd_cargos.cod_pro='".$ls_codpro."'".
																 "   AND cxp_rd_cargos.ced_bene='".$ls_cedben."'".
																 " GROUP BY cxp_rd_cargos.codemp, cxp_rd_cargos.numrecdoc, cxp_rd_cargos.cod_pro, cxp_rd_cargos.ced_bene, cxp_rd_cargos.codtipdoc,tipo_iva ORDER BY porcar ASC");
			}
			if(!empty($la_cargos))
			{
				$li_total_cargos=count($la_cargos["porcar"]);
				if($li_total_cargos>0)
				{
					$ldec_porcar=$la_cargos["porcar"][1];
					$ldec_baseimp=$la_cargos["basimp"][1];
					$ldec_monimp=$la_cargos["impiva"][1];
					$ldec_totbaseimp=$la_cargos["basimp"][1];
					$ldec_totmonimp=$la_cargos["impiva"][1];
					//Modificado por Ofimatica de Venezuela el 23-05-2011, se toma el tipo de iva directo del arreglo resultante de la consulta.	
					$ldec_porcentaje_conf=$la_cargos["tipo_iva"][1];
					//$ldec_cargo=$la_cargos["codcar"]; //C�digo utilizado anteriormente modificado seg�n descripci�n superior
					//$ldec_porcentaje_conf=$io_report->uf_porcentaje_eval($ldec_cargo); //C�digo utilizado anteriormente modificado seg�n descripci�n superior
				}
				else
				{
					$ldec_porcar="";
					$ldec_baseimp=0;
					$ldec_monimp=0;	
					$ldec_porcentaje_conf=0;	
				}
			}
			else
			{
				$ldec_porcar="";
				$ldec_baseimp=0;
				$ldec_monimp=0;	
				$ldec_porcentaje_conf=0;			
			}	
			$la_manualesd=$io_report->uf_select_rowdata($io_sql,"SELECT SUM(monto) AS monto".
															 "  FROM cxp_rd_scg ".
															 " WHERE codemp='".$ls_codemp."'".
															 "   AND numrecdoc='".$ls_numrecdoc."'".
															 "   AND cod_pro='".$ls_codpro."'".
															 "   AND ced_bene='".$ls_cedben."'".
															 "   AND debhab='D'".
															 "   AND estasicon='M'");
			
			$la_manualesh=$io_report->uf_select_rowdata($io_sql,"SELECT SUM(monto) AS monto".
															 "  FROM cxp_rd_scg ".
															 " WHERE codemp='".$ls_codemp."'".
															 "   AND numrecdoc='".$ls_numrecdoc."'".
															 "   AND cod_pro='".$ls_codpro."'".
															 "   AND ced_bene='".$ls_cedben."'".
															 "   AND debhab='H'".
															 "   AND estasicon='M'");
			$li_habman=0;
			$li_debman=0;
			if(count($la_manualesd)>0)
			{
				$li_debman=$la_manualesd["monto"];
			}	
			if(count($la_manualesh)>0)
			{
				$li_habman=$la_manualesh["monto"];
			}	
			$ldec_montodoc     = $ldec_montodoc+$ldec_mondeddoc+$li_habman-$li_debman;			 
			//print $ls_nombre." MONTOTDOC->".$ldec_montodoc."  BASE IMP->".$ldec_baseimp." MONIMP->".$ldec_monimp."<br>";
			//print $ls_nombre." MONTOTDOC->".$ldec_montodoc."  DED->".$ldec_mondeddoc." HAB->".$li_habman." DEB->".$li_debman."<br>";
			$ldec_sinderiva    = $ldec_montodoc-$ldec_baseimp-$ldec_monimp;// Total de la Compra sin Derecho a Cr�dito Iva.
			if(($ldec_sinderiva<0)&&($ldec_sinderiva>-1))
			{
				$ldec_sinderiva=0;
			}
			$ldec_totimpiva   = $ldec_totimpiva+$ldec_monimp;
			$ldec_totivaret   = $ldec_totivaret+$ldec_montoret;
			$ldec_totcomconiva= $ldec_totcomconiva+$ldec_montodoc;
			$la_data[$li]=array('name20'=>$ls_cheque,'name1'=>$li,
							 'name2'=>$io_report->io_funciones->uf_convertirfecmostrar($row["fecemidoc"]),'name3'=>$ls_rif,
							 'name4'=>$ls_nombre,'name5'=>$ls_cmpret,'name51'=>$ls_tipproben,'name6'=>'', 'name7'=>'',
							 'name8'=>$ls_numrecdoc,'name9'=>$row["numref"], 'name10'=>$ls_numnd,'name11'=>$ls_numnc,
							 'name12'=>$ls_tiptrans,'name13'=>'', 'name14'=>number_format($ldec_montodoc,2,",","."),
							 'name15'=>number_format($ldec_sinderiva,2,",","."),'name16'=>number_format($ldec_baseimp,2,",","."),
							 'name17'=>$ldec_porcar,'name18'=>number_format($ldec_monimp,2,",","."),
							 'name19'=>number_format($ldec_montoret,2,",","."));
						 	
			 $li_porcentaje   = intval($ldec_porcentaje_conf);
			 if ($ldec_porcar>0)
			 {
				switch ($li_porcentaje){
				  case '2':
					$ldec_totimp8    = ($ldec_totimp8+$ldec_monimp);
					$ldec_totbasimp8 = ($ldec_totbasimp8+$ldec_baseimp);
					$ldec_totimpret8    = ($ldec_totimpret8+$ldec_montoret);
					break;
				  case '1':
					$ldec_totimp9    = ($ldec_totimp9+$ldec_monimp);  
					$ldec_totbasimp9 = ($ldec_totbasimp9+$ldec_baseimp);
					$ldec_totimpret9    = ($ldec_totimpret9+$ldec_montoret);
					break;
				  case '3':
					$ldec_totimp25    = ($ldec_totimp25+$ldec_monimp);  
					$ldec_totbasimp25 = ($ldec_totbasimp28+$ldec_baseimp);
					$ldec_totimpret25    = ($ldec_totimpret25+$ldec_montoret);
					break;
				  }
			 }
			 
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
			//****************************************************AGREGADO POR OFIMATICA DE VENEZUELA 23-05-2011******************************************************//
			// Bloque correspondiente al recorrido del arreglo de cargos esto se debe a que existen facturas que aplican distintos tipos de iva (Ejemplo: 8% y 12%).
			 $li_aux=$li;
			 if($li_total_cargos>1)		
			 {			 	
				for($i=2;$i<=$li_total_cargos;$i++)
				{					
					$ldec_porcar=$la_cargos["porcar"][$i];
					$ldec_baseimp=$la_cargos["basimp"][$i];
					$ldec_monimp=$la_cargos["impiva"][$i];
					$ldec_totbaseimp=$ldec_totbaseimp+$ldec_baseimp;
					$ldec_totmonimp=$ldec_totmonimp+$ldec_monimp;
					$ldec_porcentaje_conf=$la_cargos["tipo_iva"][$i];
					$li_aux++;
					$la_data[$li_aux]=array('name20'=>'','name1'=>$li_aux,
								 'name2'=>'','name3'=>'',
								 'name4'=>'','name5'=>'','name51'=>'','name6'=>'', 'name7'=>'',
								 'name8'=>'','name9'=>'', 'name10'=>'','name11'=>'',
								 'name12'=>'','name13'=>'', 'name14'=>'',
								 'name15'=>'','name16'=>number_format($ldec_baseimp,2,",","."),
								 'name17'=>$ldec_porcar,'name18'=>number_format($ldec_monimp,2,",","."),
								 'name19'=>'');
					 $li_porcentaje   = intval($ldec_porcentaje_conf);
					 if ($ldec_porcar>0)
					 {
						switch ($li_porcentaje){
						  case '2':
							$ldec_totimp8    = ($ldec_totimp8+$ldec_monimp);
							$ldec_totbasimp8 = ($ldec_totbasimp8+$ldec_baseimp);
							$ldec_totimpret8    = ($ldec_totimpret8+$ldec_montoret);
							break;
						  case '1':
							$ldec_totimp9    = ($ldec_totimp9+$ldec_monimp);  
							$ldec_totbasimp9 = ($ldec_totbasimp9+$ldec_baseimp);
							$ldec_totimpret9    = ($ldec_totimpret9+$ldec_montoret);
							break;
						  case '3':
							$ldec_totimp25    = ($ldec_totimp25+$ldec_monimp);  
							$ldec_totbasimp25 = ($ldec_totbasimp28+$ldec_baseimp);
							$ldec_totimpret25    = ($ldec_totimpret25+$ldec_montoret);
							break;
						  }
					 }				 
				}
			 }
			//Reasignamos el monto exento por si aplicaba varios ivas se calcula en base a la sumatoria de los ivas y las respectivas bases imponibles
			$ldec_sinderiva=round($ldec_montodoc,2) -round($ldec_totbaseimp,2)-round($ldec_totmonimp,2);
			$la_data[$li]['name15']=number_format(abs($ldec_sinderiva),2,",",".");			
			$li=$li_aux;//Reemplazo el valor actual de $li por $li_aux que contiene el incremento de la misma segun el numero de cargos aplicados a la factura
			
			/**********************************************************FIN BLOQUE AGREGADO POR OFIMATICA DE VENEZUELA ************************************************/	
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			$la_notas=$io_report->uf_select_rowdata($io_sql,"SELECT cxp_sol_dc.*,(SELECT porcar FROM cxp_dc_cargos WHERE cxp_sol_dc.codemp=cxp_dc_cargos.codemp AND cxp_sol_dc.numsol=cxp_dc_cargos.numsol AND cxp_sol_dc.numrecdoc=cxp_dc_cargos.numrecdoc AND cxp_sol_dc.codtipdoc=cxp_dc_cargos.codtipdoc AND cxp_sol_dc.cod_pro=cxp_dc_cargos.cod_pro AND cxp_sol_dc.ced_bene=cxp_dc_cargos.ced_bene AND cxp_sol_dc.codope=cxp_dc_cargos.codope AND cxp_sol_dc.numdc=cxp_dc_cargos.numdc) as porcar FROM cxp_sol_dc WHERE cxp_sol_dc.numrecdoc='".$ls_numrecdoc."' AND cxp_sol_dc.codtipdoc='".$ls_codtipdoc."' AND cxp_sol_dc.cod_pro='".$ls_codpro."' AND cxp_sol_dc.ced_bene='".$ls_cedben."' ");
			if(count($la_notas)>0)
			{
				$ls_codope=$la_notas["codope"];
				$ls_numnota=$la_notas["numdc"];
				$ls_monnota=$la_notas["monto"];
				$ls_carnota=$la_notas["moncar"];
				$ldec_porcar=$la_notas["porcar"];
				$ls_basnota=$ls_monnota-$ls_carnota;
				if($ls_codope=='NC')
				{
					$ls_numnc=$ls_numnota;
					$ls_numnd="";
					$ls_monnota=$ls_monnota*(-1);
					$ls_carnota=$ls_carnota*(-1);
					$ls_basnota=$ls_basnota*(-1);
				}
				else
				{
					$ls_numnd=$ls_numnota;
					$ls_numnc="";
				}
				$li++;
		//	$ldec_montodoc     = $ldec_montodoc+$ls_monnota;			 
		//	$ldec_sinderiva    = $ldec_montodoc-$ldec_baseimp-$ldec_monimp;// Total de la Compra sin Derecho a Cr�dito Iva.
			$ldec_totcomconiva= $ldec_totcomconiva+$ls_monnota;
			$ldec_totimpiva   = $ldec_totimpiva+$ls_carnota;
				$la_data[$li]=array('name20'=>$ls_cheque,'name1'=>$li,
								 'name2'=>$io_report->io_funciones->uf_convertirfecmostrar($row["fecemidoc"]),'name3'=>$ls_rif,
								 'name4'=>$ls_nombre,'name5'=>$ls_cmpret,'name51'=>$ls_tipproben,'name6'=>'', 'name7'=>'',
								 'name8'=>$ls_numrecdoc,'name9'=>$row["numref"], 'name10'=>$ls_numnd,'name11'=>$ls_numnc,
								 'name12'=>$ls_tiptrans,'name13'=>$row["numrecdoc"], 'name14'=>number_format($ls_monnota,2,",","."),
								 'name15'=>'0,00','name16'=>number_format($ls_basnota,2,",","."),
								 'name17'=>$ldec_porcar,'name18'=>number_format($ls_carnota,2,",","."),
								 'name19'=>'');	
			 $li_porcentaje   = intval($ldec_porcentaje_conf);
			 if ($ldec_porcar>0)
			 {
				switch ($li_porcentaje){
				  case '2':
					$ldec_totimp8    = ($ldec_totimp8+$ls_carnota);
					$ldec_totbasimp8 = ($ldec_totbasimp8+$ls_basnota);
					//$ldec_totimpret8    = ($ldec_totimpret8+$ldec_montoret);
					break;
				  case '1':
					$ldec_totimp9    = ($ldec_totimp9+$ls_carnota);  
					$ldec_totbasimp9 = ($ldec_totbasimp9+$ls_basnota);
					//$ldec_totimpret9    = ($ldec_totimpret9+$ldec_montoret);
					break;
				  case '3':
					$ldec_totimp25    = ($ldec_totimp25+$ls_carnota);  
					$ldec_totbasimp25 = ($ldec_totbasimp28+$ls_basnota);
				    //$ldec_totimpret25    = ($ldec_totimpret25+$ldec_montoret);
					break;
				  }
			 }	
			}
			 $ldec_totcomsiniva = ($ldec_totcomsiniva+$ldec_sinderiva);	
			 $ldec_basimpga     = ($ldec_totbasimp9+$ldec_totbasimp25);//Total Base Imponible Compras Internas Afectadas en Alicuota General + Adicional(9% y 25%).
			 $ldec_totgenadi    = ($ldec_totimp9+$ldec_totimp25);//Total Compras Internas Afectadas en Alicuota Reducida. Impuestos al 9% y 25%.
		}	
		uf_print_detalle($la_data,&$io_pdf);
//		uf_print_totales(number_format($ldec_totcomsiniva,2,",","."),number_format($ldec_basimpga,2,",","."),number_format($ldec_totcomconiva,2,",","."),number_format($ldec_totimpiva,2,",","."),number_format($ldec_totivaret,2,",","."),&$io_pdf);		
		uf_print_table_default(0,number_format($ldec_totbasimp9,2,",","."),number_format($ldec_totimp9,2,",","."),0,number_format($ldec_totcomsiniva,2,",","."),number_format($ldec_totimp8,2,",","."),number_format($ldec_totbasimp8,2,",","."),number_format($ldec_basimpga,2,",","."),number_format($ldec_totgenadi,2,",","."),&$io_pdf);
		//uf_print_table_totales(number_format($ldec_totcomsiniva,2,",","."),number_format($ldec_totimpret8,2,",","."),number_format($ldec_totimpret9,2,",","."),number_format($ldec_totimpret25,2,",","."),&$io_pdf);
		uf_insert_seguridad($ls_periodo);
	}
	else
	{
		print("<script language=JavaScript>");
		print("alert('No hay Registros para Mostrar');"); 
		print("close();");
		print("</script>");	
	}
	
	
	$io_pdf->stream();
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?>
