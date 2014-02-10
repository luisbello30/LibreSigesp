<?php
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//    REPORTE: 1x1000 bancos entes publicos
	//  ORGANISMO: 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 15/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scb;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scb->uf_load_seguridad_reporte("SCB","sigesp_scb_r_retenciones_pub1x1000.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_mes,$as_anio,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->Rectangle(50,85,690,505);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],55,520,60,50); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(18,$as_titulo);
		$tm=460-($li_tm/2);
		$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
		$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
		$io_pdf->addText($tm,550,12,"<b> ".$as_titulo."</b>"); // Agregar el título
		$io_pdf->addText(260,528,12,"<b>Relación Mensual Impuesto 1x1000-Entes Públicos</b>"); // Agregar el título
		$io_pdf->addText(52,475,12,"<b>Agente de Retención :    </b>".$ls_agenteret); // Agregar el título
		$io_pdf->addText(52,457,12,"<b>Dirección :      </b>".$ls_diragenteret); // Agregar el título
		$io_pdf->addText(52,442,12,"<b>Periodo :      </b>".$as_mes." / ".substr($as_anio,2,4)); // Agregar el título
		$io_pdf->addText(52,427,12,"<b>Nro.(s) Planilla(s) Bancaria(s): __________________________</b>"); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_observacion(&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-100);
		$la_data1[1]=array('fecfac'=>'<b>Fecha Orden Pago</b>',
		                  'numordpag'=>'<b>Nº Orden Pago</b>',
  						  'nomcont'=>'<b>Nombre Contribuyente</b>',		
						  'rif'=>'<b>Rif</b>',
						  'monobr'=>'<b>Monto Obra</b>',
						  'monbru'=>'<b>Monto Bruto     </b>',  
						  'montotimp'=>'<b>Monto del Impuesto</b>',
						  'tippag'=>'<b>Tipo de Pago</b>',
						  'munici'=>'<b>Municipio</b>',
						  'operev'=>'<b>Operaciones Anuladas o Reversadas</b>');
		$la_columna=array('fecfac'=>'<b>Fecha Orden Pago</b>',
		                  'numordpag'=>'<b>Nº Orden Pago</b>',
  						  'nomcont'=>'<b>Nombre Contribuyente</b>',		
						  'rif'=>'<b>Rif</b>',
						  'monobr'=>'<b>Monto Obra</b>',
						  'monbru'=>'<b>Monto Bruto     </b>',  
						  'montotimp'=>'<b>Monto del Impuesto</b>',
						  'tippag'=>'<b>Tipo de Pago</b>',
						  'munici'=>'<b>Municipio</b>',
						  'operev'=>'<b>Operaciones Anuladas o Reversadas</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>690, // Ancho de la tabla
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'colGap'=>1,
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>70),
						 			   'numordpag'=>array('justification'=>'center','width'=>85), // Justificacion y ancho de la columna
						 			   'nomcont'=>array('justification'=>'center','width'=>85), // Justificacion y ancho de la columna
									   'rif'=>array('justification'=>'center','width'=>77), // Justificacion y ancho de la columna
						 			   'monobr'=>array('justification'=>'center','width'=>50),// Justificacion y ancho de la columna
						 			   'monbru'=>array('justification'=>'center','width'=>55),// Justificacion y ancho de la columna
   						 			   'montotimp'=>array('justification'=>'center','width'=>55),// Justificacion y ancho de la columna
									   'tippag'=>array('justification'=>'center','width'=>100),// Justificacion y ancho de la columna
									   'munici'=>array('justification'=>'center','width'=>50),// Justificacion y ancho de la columna
									   'operev'=>array('justification'=>'center','width'=>63))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		$io_pdf->addText(150,350,12,"<b> NO SE EFECTUARON PAGOS SUJETOS A LA RETENCIÓN DE IMPUESTOS 1x1000</b>"); // Agregar el título
		unset($la_data1);
		unset($la_columna);
		unset($la_config);		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numcon,$ad_fecrep,$as_agenteret,$as_rifagenteret,$as_perfiscal,$as_licagenteret,$as_diragenteret,
							   $as_nomsujret,$as_rif,$as_numlic,$ai_estcmpret,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   as_agenteret // agente de Retención
		//	    		   as_rifagenteret // Rif del Agente de Retención
		//	    		   as_perfiscal // Período Fiscal
		//	    		   as_licagenteret // Número de licencia de agente de retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_nomsujret // Nombre del sujeto retenido
		//	    		   as_rif // Rif del sujeto retenido
		//	    		   as_numlic // Número de Licencia del sujeto retenido
		//	    		   ai_estcmpret // Estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-4);
	 	if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,495,180,30);		
			$io_pdf->addText(90,505,15,"<b> ANULADO </b>"); 
		}	
		$la_data[1]=array('name'=>'<b>LEY DE TIMBRE FISCAL</b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 12, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'colGap'=>1,
						 'width'=>690, // Ancho de la tabla						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>690))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('agen_ret'=>'<b>Nº DE R.I.F. Agente de Retencion </b>',
		                  'ubic'=>'<b></b>',
						  'correlativo'=>'');				
		$la_columna=array('agen_ret'=>'',
		                  'ubic'=>'',
						  'correlativo'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla	
						 'colGap'=>1,					 
						 'maxWidth'=>690,
						 'cols'=>array('agen_ret'=>array('justification'=>'center','width'=>150),
						               'ubic'=>array('justification'=>'center','width'=>390),
						               'correlativo'=>array('justification'=>'center','width'=>150)));
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$io_pdf->addText(635,478,9,date("d/m/Y")); // Agregar la Fecha	
		$la_data[1]=array('agen_ret'=>$as_rifagenteret,
		                  'ubic'=>'ARTICULO 03 PROVIDENCIA SAATEL-00004 SOBRE ORDENES DE PAGO EMITIDAS',
						  'correlativo'=>'<b>CORRELATIVO: </b>'.$as_numcon);				
		$la_columna=array('agen_ret'=>'',
		                  'ubic'=>'',
						  'correlativo'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla	
						 'colGap'=>1,					 
						 'maxWidth'=>690,
						 'cols'=>array('agen_ret'=>array('justification'=>'center','width'=>150),
						               'ubic'=>array('justification'=>'left','width'=>390),
						               'correlativo'=>array('justification'=>'center','width'=>150)));
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'<b>DATOS DEL BENEFICIARIO                                                                                                                 </b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>690))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'<b>NOMRE: '.$as_nomsujret.'</b>');
		$la_data[2]=array('name'=>'<b>Nº DE RIF: '.$as_rif.'</b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>690))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);						 								 
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$as_rifagenteret,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//                 ai_totmoniva // Total monto iva
		//	    		   as_rifagenteret // Rif del Agente de Retención
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		//     Modificado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-100);
		$la_data1[1]=array('fecfac'=>'<b>Fecha Orden Pago</b>',
		                  'numordpag'=>'<b>Nº Orden Pago</b>',
  						  'nomcont'=>'<b>Nombre Contribuyente</b>',		
						  'rif'=>'<b>Rif</b>',
						  'monobr'=>'<b>Monto Obra</b>',
						  'monbru'=>'<b>Monto Bruto     </b>',  
						  'montotimp'=>'<b>Monto del Impuesto</b>',
						  'tippag'=>'<b>Tipo de Pago</b>',
						  'munici'=>'<b>Municipio</b>',
						  'operev'=>'<b>Operaciones Anuladas o Reversadas</b>');
		$la_columna=array('fecfac'=>'<b>Fecha Orden Pago</b>',
		                  'numordpag'=>'<b>Nº Orden Pago</b>',
  						  'nomcont'=>'<b>Nombre Contribuyente</b>',		
						  'rif'=>'<b>Rif</b>',
						  'monobr'=>'<b>Monto Obra</b>',
						  'monbru'=>'<b>Monto Bruto     </b>',  
						  'montotimp'=>'<b>Monto del Impuesto</b>',
						  'tippag'=>'<b>Tipo de Pago</b>',
						  'munici'=>'<b>Municipio</b>',
						  'operev'=>'<b>Operaciones Anuladas o Reversadas</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>690, // Ancho de la tabla
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'colGap'=>1,
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>70),
						 			   'numordpag'=>array('justification'=>'center','width'=>85), // Justificacion y ancho de la columna
						 			   'nomcont'=>array('justification'=>'center','width'=>85), // Justificacion y ancho de la columna
									   'rif'=>array('justification'=>'center','width'=>77), // Justificacion y ancho de la columna
						 			   'monobr'=>array('justification'=>'center','width'=>50),// Justificacion y ancho de la columna
						 			   'monbru'=>array('justification'=>'center','width'=>55),// Justificacion y ancho de la columna
   						 			   'montotimp'=>array('justification'=>'center','width'=>55),// Justificacion y ancho de la columna
									   'tippag'=>array('justification'=>'center','width'=>100),// Justificacion y ancho de la columna
									   'munici'=>array('justification'=>'center','width'=>50),// Justificacion y ancho de la columna
									   'operev'=>array('justification'=>'center','width'=>63))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('fecfac'=>'<b>Fecha Orden Pago</b>',
		                  'numordpag'=>'<b>Nº Orden Pago</b>',
  						  'nomcont'=>'<b>Nombre Contribuyente</b>',		
						  'rif'=>'<b>Rif</b>',
						  'monobr'=>'<b>Monto Obra</b>',
						  'monbru'=>'<b>Monto Bruto     </b>',  
						  'montotimp'=>'<b>Monto del Impuesto</b>',
						  'tippag'=>'<b>Tipo de Pago</b>',
						  'munici'=>'<b>Municipio</b>',
						  'operev'=>'<b>Operaciones Anuladas o Reversadas</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>70),
						 			   'numordpag'=>array('justification'=>'center','width'=>85), // Justificacion y ancho de la columna
						 			   'nomcont'=>array('justification'=>'center','width'=>85), // Justificacion y ancho de la columna
									   'rif'=>array('justification'=>'center','width'=>77), // Justificacion y ancho de la columna
						 			   'monobr'=>array('justification'=>'center','width'=>50),// Justificacion y ancho de la columna
						 			   'monbru'=>array('justification'=>'center','width'=>55),// Justificacion y ancho de la columna
   						 			   'montotimp'=>array('justification'=>'center','width'=>55),// Justificacion y ancho de la columna
									   'tippag'=>array('justification'=>'center','width'=>100),// Justificacion y ancho de la columna
									   'munici'=>array('justification'=>'center','width'=>50),// Justificacion y ancho de la columna
									   'operev'=>array('justification'=>'center','width'=>63))); 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
	}// end function uf_print_detalle

	function uf_print_sello(&$io_pdf)
	{
	    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_sello
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Jennifer Rivero
		//     Modificado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 13/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $la_data[1]=array('name1'=>'<b>ELABORADO POR</b>',
	                    'name2'=>'<b>JEFE DE LA UNIDAD</b>',
						'name3'=>'<b>TESORERO </b>');	
        $la_columna=array('name1'=>'','name2'=>'','name3'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 11, // Tamaño de Letras
						  'showLines'=>2, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>690,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>240),						                
										'name2'=>array('justification'=>'center','width'=>200),
										'name3'=>array('justification'=>'center','width'=>250))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config); 		
		 
	    $la_data[1]=array('name1'=>'','name2'=>'','name3'=>'');
		$la_data[2]=array('name1'=>'','name2'=>'','name3'=>'');	
		$la_data[3]=array('name1'=>'','name2'=>'','name3'=>'');	
		$la_data[4]=array('name1'=>'','name2'=>'','name3'=>'');	
		$la_data[5]=array('name1'=>'','name2'=>'','name3'=>'');		
        $la_columna=array('name1'=>'','name2'=>'','name3'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados					  
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>240),						                
										'name2'=>array('justification'=>'center','width'=>200),
										'name3'=>array('justification'=>'center','width'=>250))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config); 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $la_data2[1]=array('name1'=>'<b>RECIBE CONFORME</b>',
	                       'name2'=>'<b>SELLO</b>');	
        $la_columna=array('name1'=>'','name2'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 11, // Tamaño de Letras
						  'showLines'=>2, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>440),						                
										'name2'=>array('justification'=>'center','width'=>250))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data2,$la_columna,'',$la_config); 		
			    
		$la_data3[1]=array('name1'=>'','name2'=>'','name3'=>'');
		$la_data3[2]=array('name1'=>'<b> Nombre y Apellido:                               ________________________________</b>','name2'=>'');	
		$la_data3[3]=array('name1'=>'','name2'=>'');	
		$la_data3[4]=array('name1'=>'<b> Cédula de Identidad:                            ________________________________</b>','name2'=>'');	
		$la_data3[5]=array('name1'=>'','name2'=>'');	
		$la_data3[6]=array('name1'=>'<b> Fecha en se que Recibe Comprobante:                       ___________________</b>','name2'=>'');	
		$la_data3[7]=array('name1'=>'','name2'=>'');
		
        $la_columna=array('name1'=>'','name2'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados					  
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'left','width'=>440),						                
										'name2'=>array('justification'=>'center','width'=>250))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data3,$la_columna,'',$la_config); 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_scb_class_report.php");
	require_once("../../shared/class_folder/sigesp_include.php");
    $sig_inc   = new sigesp_include();
	$con       = $sig_inc->uf_conectar();
	$io_report=new sigesp_scb_class_report($con);
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_scb.php");
	$io_fun_scb=new class_funciones_scb();
	$ls_tiporeporte=$io_fun_scb->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo= "REPUBLICA BOLIVARIANA DE VENEZUELA";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	//$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_mes=$io_fun_scb->uf_obtenervalor_get("mes","");
	$ls_anio=$io_fun_scb->uf_obtenervalor_get("anio","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	$ls_licagenteret=$_SESSION["la_empresa"]["numlicemp"];
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_retencionesunoxmil_proveedor($ls_mes,$ls_anio);
		if ($lb_valido)
		{
			$li_totrow=$io_report->DS->getRowCount("codret");
		}
		else
		{
			$li_totrow=0;
		}
		if($li_totrow<=0)
		{
			error_reporting(E_ALL);
			set_time_limit(1800);
			$io_pdf = new Cezpdf("LETTER","landscape");
			$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(3.5,1.5,3,3);
			$lb_valido=true;
			uf_print_encabezado_pagina($ls_titulo,$ls_mes,$ls_anio,$io_pdf);
			uf_print_observacion($io_pdf);
			$lb_valido=true;
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
		else
		{
			error_reporting(E_ALL);
			set_time_limit(1800);
			$io_pdf = new Cezpdf("LETTER","landscape");
			$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(3.5,1.5,3,3);
			$lb_valido=true;
			uf_print_encabezado_pagina($ls_titulo,$ls_mes,$ls_anio,$io_pdf);
			$lb_valido=$io_report->uf_retencionesunoxmil_proveedor($ls_mes,$ls_anio);
			if($lb_valido)
			{
				$li_total=$io_report->DS->getRowCount("numcom");
				for($li_i=1;$li_i<=$li_total;$li_i++)
				{
					$ls_numcon=$io_report->DS->data["numcom"][$li_i];		 								
					$ls_codret=$io_report->DS->data["codret"][$li_i];			   
					$ls_fecrep=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecrep"][$li_i]);
					$ls_perfiscal=$io_report->DS->data["perfiscal"][$li_i];						
					$ls_codsujret=$io_report->DS->data["codsujret"][$li_i];			     
					$ls_nomsujret=$io_report->DS->data["nomsujret"][$li_i];	
					$ls_rif=$io_report->DS->data["rif"][$li_i];	
					$ls_dirsujret=$io_report->DS->data["dirsujret"][$li_i];		
					$li_estcmpret=$io_report->DS->data["estcmpret"][$li_i];	
					$ls_numlic=$io_report->DS->data["numlic"][$li_i];									
					$ls_numsop=$io_report->DS->data["numsop"][$li_i];					
					$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecfac"][$li_i]);	
					$li_baseimp=$io_report->DS->data["basimp"][$li_i];
					$li_iva_ret=$io_report->DS->data["iva_ret"][$li_i];	
					$ls_codope=$io_report->DS->data["codope"][$li_i];	
					if ($ls_codope=="CH")
					{
						$ls_codope='CHEQUE';
					}
					elseif ($ls_codope=="NC")
					{
						$ls_codope='NOTA DE CREDITO';
					}
					$li_iva_ret=number_format($li_iva_ret,2,",",".");	
					$li_baseimp=number_format($li_baseimp,2,",",".");			
					$ls_munici=" ";
					$ls_operev=" ";
					$la_data[$li_i]=array('fecfac'=>$ld_fecfac,'numordpag'=>$ls_numsop,'nomcont'=>$ls_nomsujret,'rif'=>$ls_rif,
										  'monobr'=>$li_baseimp,'monbru'=>$li_baseimp,'montotimp'=>$li_iva_ret,'tippag'=>$ls_codope,'munici'=>$ls_munici,'operev'=>$ls_operev);														
				}
				uf_print_detalle($la_data,$ls_rifagenteret,&$io_pdf);
			    //uf_print_sello($io_pdf);
			    unset($la_data);											

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
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_scb);
?> 