<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    REPORTE: Formato de salida  de la Orden de Compra
//  ORGANISMO: Ninguno en particular
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
	function uf_print_encabezado_pagina($as_estcondat,$as_numordcom,$ad_fecordcom,$as_coduniadm,$as_denuniadm, $as_codfuefin,
	                                   $as_denfuefin,$as_codigo,$as_nombre,$as_conordcom,$as_rifpro,$as_diaplacom,$as_dirpro,
									   $ls_forpagcom,$ls_estcom,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_estcondat  ---> tipo de la orden de compra
		//	    		   as_numordcom ---> numero de la orden de compra
		//	    		   ad_fecordcom ---> fecha de registro de la orden de compra
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(15,40,585,40);
		$io_pdf->line(480,700,480,760);
		$io_pdf->line(480,730,585,730);

                $io_pdf->Rectangle(15,700,570,60);
                //$io_pdf->Rectangle(15,700,570,50);

		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		switch ($ls_estcom)
				{
					case "0": // Deberian ir en letras(R) como estan en la sep y en cxp 
						$ls_estatus="REGISTRO";
					break;
						
					case "1":  //   Deberia ir  E
						if($ls_estapro==1)
						{
							$ls_estatus="EMITIDA (APROBADA)";
						}
						else
						{
							$ls_estatus="EMITIDA";
						}
					break;
						
					case "2": // DEBERIA IR P
						$ls_estatus="COMPROMETIDA(PROCESADA)";
					break;
						
					case "3": //DEBERIA IR A
						$ls_estatus="ANULADA";
					break;
						
					case "4": //DEBERIA IR ????
						$ls_estatus="ENTRADA COMPRA";
					break;
						
					case "5": //DEBERIA IR ????
						$ls_estatus="PRE-COMPROMETIDA";
					break;
					
					case "6": //DEBERIA IR ????
						$ls_estatus="PRE-COMPROMETIDA ANULADA";
					break;
					
					case "7": //DEBERIA IR ????
						$ls_estatus="SERVICIO RECIBIDO";
					break;

				}
                if($as_estcondat=="B") 
                {
                         $ls_titulo="Orden de Compra ";
                         
                         
			 $ls_titulo_grid="Bienes";
                }
                else
                {
                         $ls_titulo="Orden de Servicio";
                         //$ls_titulo=$ls_estcom;
			 $ls_titulo_grid="Servicios";
                         
                }
		
		$li_tm=$io_pdf->getTextWidth(14,$ls_titulo);
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,730,14,$ls_titulo); // Agregar el t�tulo
                $io_pdf->addText($tm-40,710,9," <b>ESTATUS: </b>".$ls_estatus); // Agregar el t�tulo
		$io_pdf->addText(485,740,9," <b>No. </b>".$as_numordcom); // Agregar el t�tulo
		$io_pdf->addText(485,710,9,"<b>Fecha </b>".$ad_fecordcom); // Agregar el t�tulo
               	$io_pdf->addText(540,770,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(546,764,6,date("h:i a")); // Agregar la Hora
		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
          

	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	//function uf_print_detalle($la_data,&$io_pdf)
        function uf_print_detalle($la_data,&$io_pdf , $la_data2 )
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data ---> arreglo de informaci�n
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle 
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                /*************** Encabezado  *************/
                //$la_data_=array(); 
                $as_nombre = $la_data2["as_nombre"];
                $as_rifpro = $la_data2["as_rifpro"];     
                $as_dirpro = $la_data2["as_dirpro"]; 
                $la_data_[0]=array('columna1'=>'<b>Proveedor</b>  '.$as_nombre.'
<b>Rif</b> '.$as_rifpro,
                                 'columna2'=>'<b>Direccion</b> '.$as_dirpro.'');
                $la_columna=array('columna1'=>'','columna2'=>'');
                $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                                 'fontSize' => 8, // Tama�o de Letras
                                                 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
                                                 'showLines'=>1, // Mostrar L�neas
                                                 'shaded'=>0, // Sombra entre l�neas
                                                 'width'=>570, // Ancho de la tabla
                                                 'maxWidth'=>570, // Ancho M�ximo de la tabla
                                                 'xOrientation'=>'center', // Orientaci�n de la tabla
                                                 'cols'=>array('columna1'=>array('justification'=>'left','width'=>250), // Justificaci�n y ancho de la columna
                                                                           'columna2'=>array('justification'=>'left','width'=>320))); // Justificaci�n y ancho de la columna
                $io_pdf->ezTable($la_data_,$la_columna,'',$la_config);
                unset($la_data_);
                unset($la_columna);
                unset($la_config);


                $as_coduniadm = $la_data2["as_coduniadm"];
                $as_denuniadm = $la_data2["as_denuniadm"];
                //$ls_uniadm    = $la_data2["ls_uniadm"];
                $ls_forpagcom = $la_data2["ls_forpagcom"];

		 if($ls_forpagcom=="CARCRE"){$ls_forpagcom='CARTA DE CREDITO';} elseif ($ls_forpagcom=="ABOCUE") {$ls_forpagcom='ABONO EN CUENTA';}
      
                $ls_uniadm=$as_coduniadm."  -  ".$as_denuniadm;
                $la_data_[1]=array('columna1'=>'<b>Unidad Ejecutora</b>    '.$ls_uniadm,'columna2'=>'<b>Forma de Pago</b>    '.$ls_forpagcom);
                $la_columnas=array('columna1'=>'','columna2'=>'');
                $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                                 'fontSize' => 7, // Tama�o de Letras
                                                 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
                                                 'showLines'=>1, // Mostrar L�neas
                                                 'shaded'=>0, // Sombra entre l�neas
                                                 'width'=>570, // Ancho de la tabla
                                                 'maxWidth'=>570, // Ancho M�ximo de la tabla
                                                 'xOrientation'=>'center', // Orientaci�n de la tabla
                                                 'cols'=>array('columna1'=>array('justification'=>'left','width'=>300), // Justificaci�n y ancho de la columna
                                                                           'columna2'=>array('justification'=>'left','width'=>270))); // Justificaci�n y ancho de la columna
                $io_pdf->ezTable($la_data_,$la_columnas,'',$la_config);
                unset($la_data_);
                unset($la_columnas);
                unset($la_config);


                $as_codfuefin    = $la_data2["as_codfuefin"];
                $as_denfuefin    = $la_data2["as_denfuefin"];
                //$ls_fuefin       = $la_data2["ls_fuefin"];
                $as_diaplacom    = $la_data2["as_diaplacom"];


                $ls_fuefin=$as_codfuefin."  -  ".$as_denfuefin;
                $la_data_[1]=array('columna1'=>'<b>Fuente Financiamiento</b>   '.$ls_fuefin,'columna2'=>'<b> Plazo de Entrega</b>    '.$as_diaplacom);
                $la_columnas=array('columna1'=>'','columna2'=>'');
                $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                                 'fontSize' => 7, // Tama�o de Letras
                                                 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
                                                 'showLines'=>1, // Mostrar L�neas
                                                 'shaded'=>0, // Sombra entre l�neas
                                                 'width'=>570, // Ancho de la tabla
                                                 'maxWidth'=>570, // Ancho M�ximo de la tabla
                                                 'xOrientation'=>'center', // Orientaci�n de la tabla
                                                 'cols'=>array('columna1'=>array('justification'=>'left','width'=>300), // Justificaci�n y ancho de la columna
                                                                           'columna2'=>array('justification'=>'left','width'=>270))); // Justificaci�n y ancho de la columna
                $io_pdf->ezTable($la_data_,$la_columnas,'',$la_config);
                unset($la_data_);
                unset($la_columnas);
                unset($la_config);

                $as_conordcom    = $la_data2["as_conordcom"];


                $la_data_[1]=array('columna1'=>'<b>Concepto</b>         '.$as_conordcom);
                $la_columnas=array('columna1'=>'');
                $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                                 'fontSize' => 7, // Tama�o de Letras
                                                 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
                                                 'showLines'=>1, // Mostrar L�neas
                                                 'shaded'=>0, // Sombra entre l�neas
                                                 'width'=>570, // Ancho de la tabla
                                                 'maxWidth'=>570, // Ancho M�ximo de la tabla
                                                 'xOrientation'=>'center', // Orientaci�n de la tabla
                                                 'cols'=>array('columna1'=>array('justification'=>'left','width'=>570))); // Justificaci�n y ancho de la columna
                $io_pdf->ezTable($la_data_,$la_columnas,'',$la_config);
                unset($la_data_);
                unset($la_columnas);
                unset($la_config);
                /*************** Encabezado  *************/
		global $ls_estmodest, $ls_bolivares;
		if($la_data2['as_estcondat']=="B") 
                {
			$ls_titulo_grid="Bienes";
		}
		else
		{
			$ls_titulo_grid="Servicios";
		}//echo $ls_titulo_grid;die();
		$io_pdf->ezSetDy(-10);
		$la_datatitulo[1]=array('columna1'=>'<b> Detalle de '.$ls_titulo_grid.'</b>');
		$la_columnas=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>570))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_datatitulo,$la_columnas,'',$la_config);
		unset($la_datatitulo);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-2);
		$la_columnas=array('codigo'=>'<b>C�digo</b>',
						   'denominacion'=>'<b>Denominacion</b>',
						   'cantidad'=>'<b>Cant.</b>',
						   'unidad'=>'<b>Unidad</b>',
						   'cosuni'=>'<b>Costo/Uni '.$ls_bolivares.'</b>',
						   'baseimp'=>'<b>Sub-Total '.$ls_bolivares.'</b>',
						   'cargo'=>'<b>IVA '.$ls_bolivares.'</b>',
						   'montot'=>'<b>Total '.$ls_bolivares.'</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>100), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>130), // Justificaci�n y ancho de la columna
						 			   'cantidad'=>array('justification'=>'left','width'=>40), // Justificaci�n y ancho de la columna
						 			   'unidad'=>array('justification'=>'center','width'=>45), // Justificaci�n y ancho de la columna
						 			   'cosuni'=>array('justification'=>'right','width'=>60), // Justificaci�n y ancho de la columna
						 			   'baseimp'=>array('justification'=>'right','width'=>65), // Justificaci�n y ancho de la columna
						 			   'cargo'=>array('justification'=>'right','width'=>60), // Justificaci�n y ancho de la columna
						 			   'montot'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_cuentas($la_data,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_cuentas
		//		   Access: private 
		//	    Arguments: la_data ---> arreglo de informaci�n
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-5);
		global $ls_estmodest, $ls_bolivares;
		if($ls_estmodest==1)
		{
			$ls_titulo="Estructura Presupuestaria";
		}
		else
		{
			$ls_titulo="Estructura Programatica";
		}
		$la_datatit[1]=array('titulo'=>'<b> Detalle de Presupuesto </b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
		unset($la_datatit);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-2);

		$la_columnas=array('codestpro'=>'<b>'.$ls_titulo.'</b>',
						   'cuenta'=>'<b>Cuenta</b>',
						   'denominacion'=>'<b>Denominacion</b>',
						   'monto'=>'<b>Total '.$ls_bolivares.'</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('codestpro'=>array('justification'=>'center','width'=>170), // Justificaci�n y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>100), // Justificaci�n y ancho de la columna
						 			   'denominacio'=>array('justification'=>'center','width'=>200), // Justificaci�n y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna

                
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($li_subtot,$li_totcar,$li_montot,$ls_monlet,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		    Acess: private 
		//	    Arguments: li_subtot ---> Subtotal del articulo
		//	    		   li_totcar -->  Total cargos
		//	    		   li_montot  --> Monto total
		//	    		   ls_monlet   //Monto en letras
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: funci�n que imprime los totales
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		
		$la_data[1]=array('titulo'=>'<b>Sub Total '.$ls_bolivares.'</b>','contenido'=>$li_subtot,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci�n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Cargos '.$ls_bolivares.'</b>','contenido'=>$li_totcar,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci�n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Total '.$ls_bolivares.'</b>','contenido'=>$li_montot,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci�n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-5);
		$la_data[1]=array('titulo'=>'<b> Son: '.$ls_monlet.'</b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>1, // Sombra entre l�neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-10);



$la_columnas=array('col1'=>'', 'col2'=>'','col3'=>'', 'col4'=>'','col5'=>'');

$la_columnas2=array('col1'=>'', 'col2'=>'',  'col3'=>'');

$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>1, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('col1'=>array('justification'=>'center','width'=>119),'col2'=>array('justification'=>'center','width'=>100),
								'col3'=>array('justification'=>'center','width'=>115),'col4'=>array('justification'=>'center','width'=>105),
								'col5'=>array('justification'=>'center','width'=>131))); // Justificaci�n y ancho de la columna

$la_config1=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>1, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('col1'=>array('justification'=>'center','width'=>90),'col2'=>array('justification'=>'center','width'=>90),
								'col3'=>array('justification'=>'center','width'=>105),'col4'=>array('justification'=>'center','width'=>144),
								'col5'=>array('justification'=>'center','width'=>141))); // Justificaci�n y ancho de la columna

$la_config2=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>1, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						  'rowGap'=>15,
						 'cols'=>array('col1'=>array('justification'=>'center','width'=>90),'col2'=>array('justification'=>'center','width'=>90),
								'col3'=>array('justification'=>'center','width'=>105),'col4'=>array('justification'=>'center','width'=>144),
								'col5'=>array('justification'=>'center','width'=>141))); // Justificaci�n y ancho de la columna

$la_config3=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>1, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570,// Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('col1'=>array('justification'=>'center','width'=>200),'col2'=>array('justification'=>'center','width'=>170),
								'col3'=>array('justification'=>'center','width'=>200))); // Justificaci�n y ancho de la columna

$la_config4=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>1, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570,// Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'rowGap'=>15,
						 'cols'=>array('col1'=>array('justification'=>'center','width'=>200),'col2'=>array('justification'=>'center','width'=>170),
								'col3'=>array('justification'=>'center','width'=>200))); // Justificaci�n y ancho de la columna

//titulos
		$la_datatit[1]=array('col1'=>'FIANZA DE FIEL CUMPLIMIENTO',
						   'col2'=>'FIANZA DE ANTICIPO',
						   'col3'=>'FIANZA LABORAL',
						   'col4'=>'CLAUSULA PENAL',
						   'col5'=>'CLAUSULA ESPECIAL');
		$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
		unset($la_datatit);
//titulos

//subtitulos  
		$la_data[1]=array('col1'=>'(   )APLICA  (   )NO APLICA',
						   'col2'=>'(   )APLICA  (   )NO APLICA',
						   'col3'=>'(   )APLICA  (   )NO APLICA',
						   'col4'=>'',
						   'col5'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
//subtitulos


//TEXTO  
		$la_data[1]=array('col1'=>'Para asegurar el cumplimiento de las obligaciones que asume el contratista, al aprobarse esta orden se exigira al beneficiario fianza de fiel cumplimiento 					  equivalente al 15% del monto total de la misma incluido IVA',
				  'col2'=>'Fianza por el 100% del monto acordado de anticipo, emitida por una institucion bancaria, empresa de seguros o sociedad nacional de garantias reciprocas',
				  'col3'=>'Hasta por el 10% del costo de la mano de obra, incluida en la estructura de costos de la oferta, vigente desde el inicio del contrato hasta seis meses despues de la teminacion del servicio',
				  'col4'=>'Queda establecida la clausula penal, segun la cual el proveedor pagara al fisco el 2% sobre el monto del servicio respectivo por cada dia habil de retardo en su prestacion',
				  'col5'=>'El Ministerio del Poder Popular para la Cultura, se reserva el derecho de anular unilateralmente la presente orden de compra sin indemnizacion de conformidad con las revisiones legales que rigen la materia');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_config);
		$io_pdf->ezSetDy(-10);
//TEXTO  

//TITULO
		$la_data[1]=array('col1'=>'CONTABILIDAD',
						   'col2'=>'COMPRAS',
						   'col3'=>'JEFE DE PROYECTO',
						   'col4'=>'DIRECCI�N DE BIENES Y SERVICIOS',
						   'col5'=>'DIRECCI�N DE ADMINISTRACI�N');		
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config1);
		unset($la_data);
//TITULO

//ESPACIO
		$la_data[1]=array('col1'=>'','col2'=>'','col3'=>'','col4'=>'','col5'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config2);
		unset($la_data);
		unset($la_config2);
//ESPACIO

//FIRMAS
		$la_data[1]=array('col1'=>'FIRMA Y SELLO',
						   'col2'=>'FIRMA Y SELLO',
						   'col3'=>'FIRMA Y SELLO',
						   'col4'=>'FIRMA Y SELLO',
						   'col5'=>'FIRMA Y SELLO');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config1);
		unset($la_data);
		unset($la_config1);
		unset($la_columnas);
		$io_pdf->ezSetDy(-15);
//FIRMAS

//titulo recepcion
		$la_data[1]=array('col1'=>'APELLIDOS Y NOMBRES',
						   'col2'=>'CEDULA DE IDENTIDAD',
						   'col3'=>'FIRMA Y SELLO RECIBIDO CONFORME');
		
		$io_pdf->ezTable($la_data,$la_columnas2,'RECEPCION DE LA ORDEN POR EL PROVEEDOR',$la_config3);
		unset($la_data);
		unset($la_config3);
//titulo recepcion

//ESPACIO recepcion
		$la_data[1]=array('col1'=>'','col2'=>'','col3'=>'');
		$io_pdf->ezTable($la_data,$la_columnas2,'',$la_config4);
 		unset($la_data);
 		unset($la_columnas2);
 		unset($la_config4);
//ESPACIO recepcion

	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabeceramonto_bsf($li_montotaux,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		    Acess: private 
		//	    Arguments: li_montotaux ---> Total de la Orden Bs.F.
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: Funci�n que imprime el total de la Orden de Compra en Bolivares Fuertes.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 25/09/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /*
		$la_data[1]=array('titulo'=>'<b>Monto Bs.F.</b>','contenido'=>$li_montotaux,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci�n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
                */
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/class_folder/sigesp_include.php");
	require_once("../../shared/class_folder/class_sql.php");	
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("../../shared/class_folder/class_funciones.php");
	require_once("sigesp_soc_class_report.php");	
	require_once("../class_folder/class_funciones_soc.php");
	$in           = new sigesp_include();
	$con          = $in->uf_conectar();
	$io_sql       = new class_sql($con);	
	$io_funciones = new class_funciones();	
	$io_fun_soc   = new class_funciones_soc();
	$io_report    = new sigesp_soc_class_report($con);
	$ls_estmodest = $_SESSION["la_empresa"]["estmodest"];

	//Instancio a la clase de conversi�n de numeros a letras.
	include("../../shared/class_folder/class_numero_a_letra.php");
	$numalet= new class_numero_a_letra();
	//imprime numero con los valore por defecto
	//cambia a minusculas
	$numalet->setMayusculas(1);
	//cambia a femenino
	$numalet->setGenero(1);
	//cambia moneda
	$numalet->setMoneda("Bolivares");
	//cambia prefijo
	$numalet->setPrefijo("***");
	//cambia sufijo
	$numalet->setSufijo("***");
	$ls_tiporeporte=$io_fun_soc->uf_obtenervalor_get("tiporeporte",1);
	$ls_bolivares="Bs.";
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
		$numalet->setMoneda("Bolivares Fuerte");
	}
		
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_numordcom=$io_fun_soc->uf_obtenervalor_get("numordcom","");
	$ls_estcondat=$io_fun_soc->uf_obtenervalor_get("tipord","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$rs_data= $io_report->uf_select_orden_imprimir($ls_numordcom,$ls_estcondat,&$lb_valido); // Cargar los datos del reporte
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		$ls_descripcion="Gener� el Reporte de Orden de Compra";
		$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_p_registro_orden_compra.php",$ls_descripcion);
		if($lb_valido)	
		{
			error_reporting(E_ALL);
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3.6,3,3,3); // Configuraci�n de los margenes en cent�metros
			$io_pdf->ezStartPageNumbers(570,47,8,'','',1); // Insertar el n�mero de p�gina
                         
			if ($row=$io_sql->fetch_row($rs_data))
			{
				$ls_numordcom=$row["numordcom"];
				$ls_estcondat=$row["estcondat"];
				$ls_coduniadm=$row["coduniadm"];
				$ls_denuniadm=$row["denuniadm"];
				$ls_codfuefin=$row["codfuefin"];
				$ls_denfuefin=$row["denfuefin"];
				$ls_diaplacom=$row["diaplacom"];
				$ls_forpagcom=$row["forpagcom"];
				$ls_codpro=$row["cod_pro"];
				$ls_nompro=$row["nompro"];
				$ls_rifpro=$row["rifpro"];
				$ls_dirpro=$row["dirpro"];
				$ld_fecordcom=$row["fecordcom"];
				$ls_obscom=$row["obscom"];
				$ld_monsubtot=$row["monsubtot"];
				$ld_monimp=$row["monimp"];
				$ld_montot=$row["montot"];
                                $ls_estcom=$row["estcom"];
				if($ls_tiporeporte==0)
				{
					$ld_montotaux=$row["montotaux"];
					$ld_montotaux=number_format($ld_montotaux,2,",",".");
				}
				$numalet->setNumero($ld_montot);
				$ls_monto= $numalet->letra();
				$ld_montot=number_format($ld_montot,2,",",".");
				$ld_monsubtot=number_format($ld_monsubtot,2,",",".");
				$ld_monimp=number_format($ld_monimp,2,",",".");
				$ld_fecordcom=$io_funciones->uf_convertirfecmostrar($ld_fecordcom);





                                $la_data2=array();
                                $la_data2["as_estcondat"]=$ls_estcondat;
                                $la_data2["as_numordcom"]=$ls_numordcom;
                                $la_data2["ad_fecordcom"]=$ld_fecordcom;
                                $la_data2["as_coduniadm"]=$ls_coduniadm;
                                $la_data2["as_denuniadm"]=$ls_denuniadm;
                                $la_data2["as_codfuefin"]=$ls_codfuefin;
                                $la_data2["as_denfuefin"]=$ls_denfuefin;
                                $la_data2["as_codpro"]   =$ls_codpro;
                                $la_data2["as_nombre"]   =$ls_nompro;
                                $la_data2["as_conordcom"]=$ls_obscom;
                                $la_data2["as_rifpro"]   =$ls_rifpro;
                                $la_data2["as_diaplacom"]=$ls_diaplacom;
                                $la_data2["as_dirpro"]   =$ls_dirpro;
                                $la_data2["ls_forpagcom"]=$ls_forpagcom;
                                $la_data2["ls_estcom"]=$ls_estcom;
                               
				    
                                uf_print_encabezado_pagina($ls_estcondat,$ls_numordcom,$ld_fecordcom,$ls_coduniadm,$ls_denuniadm,
				                           $ls_codfuefin,$ls_denfuefin,$ls_codpro,$ls_nompro,$ls_obscom,$ls_rifpro,
										   $ls_diaplacom,$ls_dirpro,$ls_forpagcom,$ls_estcom,&$io_pdf);
                                 
                                

				/////DETALLE  DE  LA ORDEN DE COMPRA
			       $rs_datos = $io_report->uf_select_detalle_orden_imprimir($ls_numordcom,$ls_estcondat,&$lb_valido);
			       if ($lb_valido)
			       {
		     	           $li_totrows = $io_sql->num_rows($rs_datos);
				   if ($li_totrows>0)
				   {
				        $li_i = 0;
				        while($row=$io_sql->fetch_row($rs_datos))
					{
						$li_i=$li_i+1;
						$ls_codartser=$row["codartser"];
						$ls_denartser=$row["denartser"];
						if($ls_estcondat=="B")
						{
							$ls_unidad=$row["unidad"];
						}
						else
						{
							$ls_unidad="";
						}
						if($ls_unidad=="D")
						{
						   $ls_unidad="Detal";
						}
						elseif($ls_unidad=="M")
						{
						   $ls_unidad="Mayor";
						}
						$li_cantartser=$row["cantartser"];
						$ld_preartser=$row["preartser"];
						//$ld_subtotartser=$ld_preartser*$li_cantartser;
                                                $ld_subtotartser=$row["montsubartser"];
						$ld_totartser=$row["monttotartser"];
						$ld_carartser=$ld_totartser-$ld_subtotartser;
						
						
						$ld_preartser=number_format($ld_preartser,2,",",".");
						$ld_subtotartser=number_format($ld_subtotartser,2,",",".");
						$ld_totartser=number_format($ld_totartser,2,",",".");
						$ld_carartser=number_format($ld_carartser,2,",",".");
						$la_data[$li_i]=array('codigo'=>$ls_codartser,'denominacion'=>$ls_denartser,'cantidad'=>$li_cantartser,
											  'unidad'=>$ls_unidad,'cosuni'=>$ld_preartser,'baseimp'=>$ld_subtotartser,
											  'cargo'=>$ld_carartser,'montot'=>$ld_totartser);
					}
					//uf_print_detalle($la_data,&$io_pdf);
                                        uf_print_detalle($la_data,&$io_pdf, $la_data2 );
					unset($la_data);

				         /////DETALLE  DE  LAS  CUENTAS DE GASTOS DE LA ORDEN DE COMPRA
					$rs_datos_cuenta=$io_report->uf_select_cuenta_gasto($ls_numordcom,$ls_estcondat,&$lb_valido); 
					if($lb_valido)
					{
						 $li_totrows = $io_sql->num_rows($rs_datos_cuenta);
						 if ($li_totrows>0)
						 {
							$li_s = 0;
							while($row=$io_sql->fetch_row($rs_datos_cuenta))
							{
								$li_s=$li_s+1;
								$ls_codestpro1=trim($row["codestpro1"]);
								$ls_codestpro2=trim($row["codestpro2"]);
								$ls_codestpro3=trim($row["codestpro3"]);
								$ls_codestpro4=trim($row["codestpro4"]);
								$ls_codestpro5=trim($row["codestpro5"]);
								$ls_spg_cuenta=$row["spg_cuenta"];
								$ld_monto=$row["monto"];
								$ld_monto=number_format($ld_monto,2,",",".");
								$ls_dencuenta="";
								$lb_valido = $io_report->uf_select_denominacionspg($ls_spg_cuenta,$ls_dencuenta);																																						
								if($ls_estmodest==1)
								{
									$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3;
								}
								else
								{
									$ls_codestpro=substr($ls_codestpro1,-2)."-".substr($ls_codestpro2,-2)."-".substr($ls_codestpro3,-2)."-".substr($ls_codestpro4,-2)."-".substr($ls_codestpro5,-2);
								}
								$la_data[$li_s]=array('codestpro'=>$ls_codestpro,'denominacion'=>$ls_dencuenta,
													  'cuenta'=>$ls_spg_cuenta,'monto'=>$ld_monto);
							}	
							uf_print_detalle_cuentas($la_data,&$io_pdf);
							unset($la_data);
						}
				     }
			      }
		       }
	     	}
		}
		uf_print_piecabecera($ld_monsubtot,$ld_monimp,$ld_montot,$ls_monto,&$io_pdf);
		if($ls_tiporeporte==0)
		{
			uf_print_piecabeceramonto_bsf($ld_montotaux,&$io_pdf);
		}
		 
	} 	  	 
	if($lb_valido) // Si no ocurrio ning�n error
	{
		$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
		$io_pdf->ezStream(); // Mostramos el reporte
	}
	else // Si hubo alg�n error
	{
		print("<script language=JavaScript>");
		print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
		print(" close();");
		print("</script>");		
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_soc);
?>
