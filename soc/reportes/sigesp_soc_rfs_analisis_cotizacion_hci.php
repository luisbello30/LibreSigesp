<?PHP
    session_start();
	ini_set('memory_limit','1024M');
 	ini_set('max_execution_time ','0');
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "opener.document.form1.submit();"	;
		print "close();";
		print "</script>";
	}
	//---------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 25/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_compra;
		$ls_descripcion="Generó el Reporte Análisis de Cotización";
		$lb_valido=$io_fun_compra->uf_load_seguridad_reporte("SOC","sigesp_soc_p_analisis_cotizacion.php",$ls_descripcion);
		return $lb_valido;
	}
	//------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_numanacot,$ad_fecha,$ds_contcol,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		    Acess: private
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime el banner del reporte
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 17/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $io_encabezado=$io_pdf->openObject();
        $io_pdf->saveState();
        $io_pdf->setStrokeColor(0,0,0);
        
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],38,530,80,40); // Agregar Logo
        $li_tm=$io_pdf->getTextWidth(14,"<b>Análisis de Cotizaciones</b>");
        $tm=396-($li_tm/2);
        $io_pdf->addText($tm-35,542,14,"<b>"."<b>ANÁLISIS DE COTIZACIONES</b>"."</b>");
        $io_pdf->addText(550,558,9,"<b>"."<b>NUMERO</b>"."</b>");
        $io_pdf->addText(550,547,9,"<b>"."<b>FECHA</b>"."</b>");
        $io_pdf->addText(550,537,9,"<b>"."<b>LICITACION N°</b>"."</b>");
        
        $io_pdf->addText(650,558,10,"<b>"."<b>$as_numanacot</b>"."</b>");
        $io_pdf->addText(650,547,10,"<b>"."<b>$ad_fecha</b>"."</b>");

		$io_pdf->Rectangle(34,529,510,42);
		$io_pdf->Rectangle(544,529,240,42);


		$io_pdf->ezSetDy(-4);
		$la_data[0]["1"]="";
		$la_data[0]["2"]="";		//$as_numanacot;
		$la_anchos_col = array(27,173);
		$la_justificaciones = array("left","left");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 9,
							   "lineas"        =>1,
							   "margen_horizontal"=>6,
							   "alineacion_col"=>$la_justificaciones);
		$io_pdf->ezSetDy(-58);
		$io_pdf->add_tabla(40,$la_data,$la_opciones);
        $io_pdf->ezSetY(520); 
        
        unset($la_data);
        unset($la_columnas);
        unset($la_config);
        
        $la_data[1]=array('columna1'=>'<b>DATOS DEL PROVEEDOR</b>',
                          'columna2'=>'<b>COTIZACION 1</b>',
                          'columna3'=>'<b>COTIZACION 2</b>',
                          'columna4'=>'<b>COTIZACION 3</b>');
        $la_columnas=array('columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'');
        $la_config=array('showHeadings'=>0, // Mostrar encabezados
                         'fontSize' => 6, // Tamaño de Letras
                         'titleFontSize' => 6,  // Tamaño de Letras de los títulos
                         'showLines'=>1, // Mostrar Líneas
                         'shaded'=>0,   // Sombra entre líneas
                         'width'=>570, // Ancho de la tabla
                         'maxWidth'=>570, // Ancho Máximo de la tabla
                         'xOrientation'=>'right', // Orientación de la tabla
                         'xPos'=>37,
                         'cols'=>array('columna1'=>array('justification'=>'center','width'=>198),
                                       'columna2'=>array('justification'=>'center','width'=>184),
                                       'columna3'=>array('justification'=>'center','width'=>184),
                                       'columna4'=>array('justification'=>'center','width'=>184))); // Justificación y ancho de la columna
        $io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
        unset($la_columnas);
        unset($la_config);
        
        $io_pdf->ezSetY(507);  
        $la_data[1]=array('columna1'=>'NOMBRE',
                          'columna2'=>$ds_contcol[0]["nombre1"],
                          'columna3'=>$ds_contcol[1]["nombre2"],
                          'columna4'=>$ds_contcol[2]["nombre3"]);
        $la_columnas=array('columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'');
        $la_config=array('showHeadings'=>0, // Mostrar encabezados
                         'fontSize' => 6, // Tamaño de Letras
                         'titleFontSize' => 6,  // Tamaño de Letras de los títulos
                         'showLines'=>1, // Mostrar Líneas
                         'shaded'=>0, // Sombra entre líneas
                         'width'=>570, // Ancho de la tabla
                         'maxWidth'=>570, // Ancho Máximo de la tabla
                         'xOrientation'=>'right', // Orientación de la tabla
                         'xPos'=>37,
                         'cols'=>array('columna1'=>array('justification'=>'left','width'=>198),
                                       'columna2'=>array('justification'=>'center','width'=>184),
                                       'columna3'=>array('justification'=>'center','width'=>184),
                                       'columna4'=>array('justification'=>'center','width'=>184))); // Justificación y ancho de la columna
        $io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
        unset($la_columnas);
        unset($la_config);
                        
        $la_data[1]=array('columna1'=>'CODIGO DE PROVEEDOR:',
                          'columna2'=>$ds_contcol[0]["rif1"],
                          'columna3'=>$ds_contcol[1]["rif2"],
                          'columna4'=>$ds_contcol[2]["rif3"]);
        $la_columnas=array('columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'');
        $la_config=array('showHeadings'=>0, // Mostrar encabezados
                         'fontSize' => 6, // Tamaño de Letras
                         'titleFontSize' => 6,  // Tamaño de Letras de los títulos
                         'showLines'=>1, // Mostrar Líneas
                         'shaded'=>0, // Sombra entre líneas
                         'width'=>570, // Ancho de la tabla
                         'maxWidth'=>570, // Ancho Máximo de la tabla
                         'xOrientation'=>'right', // Orientación de la tabla
                         'xPos'=>37,
                         'cols'=>array('columna1'=>array('justification'=>'left','width'=>198),
                                       'columna2'=>array('justification'=>'center','width'=>184),
                                       'columna3'=>array('justification'=>'center','width'=>184),
                                       'columna4'=>array('justification'=>'center','width'=>184))); // Justificación y ancho de la columna
        $io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
        unset($la_columnas);
        unset($la_config);
        
        $la_data[1]=array('columna1'=>'FECHA Y REFERENCIA DE COTIZACION:',
                          'columna2'=>$ds_contcol[0]["feccot1"],
                          'columna3'=>$ds_contcol[1]["feccot2"],
                          'columna4'=>$ds_contcol[2]["feccot3"]);
        $la_columnas=array('columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'');
        $la_config=array('showHeadings'=>0, // Mostrar encabezados
                         'fontSize' => 6, // Tamaño de Letras
                         'titleFontSize' => 6,  // Tamaño de Letras de los títulos
                         'showLines'=>1, // Mostrar Líneas
                         'shaded'=>0, // Sombra entre líneas
                         'width'=>570, // Ancho de la tabla
                         'maxWidth'=>570, // Ancho Máximo de la tabla
                         'xOrientation'=>'right', // Orientación de la tabla
                         'xPos'=>37,
                         'cols'=>array('columna1'=>array('justification'=>'left','width'=>198),
                                       'columna2'=>array('justification'=>'center','width'=>184),
                                       'columna3'=>array('justification'=>'center','width'=>184),
                                       'columna4'=>array('justification'=>'center','width'=>184))); // Justificación y ancho de la columna
        $io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
        unset($la_columnas);
        unset($la_config);
                       
        $la_data[1]=array('columna1'=>'DOMICILIO FISCAL:',
                          'columna2'=>$ds_contcol[0]["dirpro1"],
                          'columna3'=>$ds_contcol[1]["dirpro2"],
                          'columna4'=>$ds_contcol[2]["dirpro3"]);
        $la_columnas=array('columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'');
        $la_config=array('showHeadings'=>0, // Mostrar encabezados
                         'fontSize' => 6, // Tamaño de Letras
                         'titleFontSize' => 6,  // Tamaño de Letras de los títulos
                         'showLines'=>1, // Mostrar Líneas
                         'shaded'=>0, // Sombra entre líneas
                         'width'=>570, // Ancho de la tabla
                         'maxWidth'=>570, // Ancho Máximo de la tabla
                         'xOrientation'=>'right', // Orientación de la tabla
                         'xPos'=>37,
                         'cols'=>array('columna1'=>array('justification'=>'left','width'=>198),
                                       'columna2'=>array('justification'=>'center','width'=>184),
                                       'columna3'=>array('justification'=>'center','width'=>184),
                                       'columna4'=>array('justification'=>'center','width'=>184))); // Justificación y ancho de la columna
        $io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
        unset($la_columnas);
        unset($la_config);
        
        $io_pdf->ezSetDy(-5);        
        $la_data[1]=array('columna1'=>'<b>Item</b>',
                          'columna2'=>'<b>Descripción</b>',
                          'columna3'=>'<b>CANTIDAD</b>',
                          'columna4'=>'<b>PRECIO UNITARIO</b>',
                          'columna5'=>'<b>TOTAL</b>',
                          'columna6'=>'<b>CANTIDAD</b>',
                          'columna7'=>'<b>PRECIO UNITARIO</b>',
                          'columna8'=>'<b>TOTAL</b>',
                          'columna9'=>'<b>CANTIDAD</b>',
                          'columna10'=>'<b>PRECIO UNITARIO</b>',
                          'columna11'=>'<b>TOTAL</b>');
        $la_columnas=array('columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'','columna5'=>'',
                           'columna6'=>'','columna7'=>'','columna8'=>'','columna9'=>'','columna10'=>'','columna11'=>'');
        $la_config=array('showHeadings'=>0, // Mostrar encabezados
                         'fontSize' => 5, // Tamaño de Letras
                         'titleFontSize' => 5,  // Tamaño de Letras de los títulos
                         'showLines'=>1, // Mostrar Líneas
                         'shaded'=>0, // Sombra entre líneas
                         'width'=>570, // Ancho de la tabla
                         'maxWidth'=>570, // Ancho Máximo de la tabla
                         'xOrientation'=>'right', // Orientación de la tabla
                         'xPos'=>37,
                         'cols'=>array('columna1'=>array('justification'=>'left','width'=>28),
                                       'columna2'=>array('justification'=>'center','width'=>170),
                                       'columna3'=>array('justification'=>'center','width'=>60),
                                       'columna4'=>array('justification'=>'center','width'=>60),
                                       'columna5'=>array('justification'=>'center','width'=>64),
                                       'columna6'=>array('justification'=>'center','width'=>60),
                                       'columna7'=>array('justification'=>'center','width'=>60),
                                       'columna8'=>array('justification'=>'center','width'=>64),
                                       'columna9'=>array('justification'=>'center','width'=>60),
                                       'columna10'=>array('justification'=>'center','width'=>60),
                                       'columna11'=>array('justification'=>'center','width'=>64))); // Justificación y ancho de la columna
        $io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
        unset($la_columnas);
        unset($la_config);
        
        $io_pdf->restoreState();
        $io_pdf->closeObject();
        $io_pdf->addObject($io_encabezado,'all');        
	}// end function uf_print_encabezado_pagina
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_proveedores($la_cotizaciones,$io_ds_detalle,$io_ds_detallepro,$la_countcot,$ds_contcol,$li_calculado,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_proveedores
		//		    Acess: private
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime el el listado de  proveedores participantes
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 18/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_class_report;
		global $io_funciones, $ls_bolivares;
		$io_ds_detalle1=new class_datastore();
		$io_ds_detallepro1=new class_datastore();
		$li_totalcotizaciones=count($la_cotizaciones);
		$io_ds_detalle1->data=$io_ds_detalle->data;
		$io_ds_detallepro1->data=$io_ds_detallepro->data;
		$li_totalproveedores=$io_ds_detallepro1->getRowCount($io_ds_detallepro);

		$li_a=0;
		$li_b=0;
        $ds_linea_tot1=array("subtot1"=>0,"subtot2"=>0,"subtot3"=>0);
        $ds_linea_tot2=array("iva1"=>0,"iva2"=>0,"iva3"=>0);
        $ds_linea_tot3=array("tot1"=>0,"tot2"=>0,"tot3"=>0);
        $ds_linea_tot4=array("garantia1"=>'',"garantia2"=>'',"garantia3"=>'');
        $ds_linea_tot5=array("condp1"=>'',"condp2"=>'',"condp3"=>'');
        $ds_linea_tot6=array("dfecent1"=>'',"dfecent2"=>'',"dfecent3"=>'');
        $ds_linea_tot7=array("cumple1"=>'',"cumple2"=>'',"cumple3"=>'');
        $ds_linea_tot8=array("garantia1"=>'',"garantia2"=>'',"garantia3"=>'');
        
        $io_pdf->ezSetDy(-5);
        $li_totrow=count($la_cotizaciones);
        
        //----------- Impresion de denominaciones de Items
        //$li_set=438;
        //$io_pdf->ezSetY($li_set);                               
        
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
		    if (!empty($la_data))
		    {
                $la_data=array();
		    }

			$ls_codigo=$la_cotizaciones[$li_i]["codigo"];

            $li_a++;
			$la_data[0]["1"]=$li_i;
			$la_data[0]["2"]=$la_cotizaciones[$li_i]["denominacion"];

            $li_canart_1    = $la_cotizaciones[$li_i]["canart1"];
            $li_preuniart_1 = $la_cotizaciones[$li_i]["preuniart1"];
            $li_monsubart_1 = $la_cotizaciones[$li_i]["monsubart1"];
            $li_canart_2    = (!empty($la_cotizaciones[$li_i]["canart2"]) ? $la_cotizaciones[$li_i]["canart2"] : ""); 
            $li_preuniart_2 = (!empty($la_cotizaciones[$li_i]["preuniart2"]) ? $la_cotizaciones[$li_i]["preuniart2"] : ""); 
            $li_monsubart_2 = (!empty($la_cotizaciones[$li_i]["monsubart2"]) ? $la_cotizaciones[$li_i]["monsubart2"] : ""); 
            $li_canart_3    = (!empty($la_cotizaciones[$li_i]["canart3"]) ? $la_cotizaciones[$li_i]["canart3"] : "");
            $li_preuniart_3 = (!empty($la_cotizaciones[$li_i]["preuniart3"]) ? $la_cotizaciones[$li_i]["preuniart3"] : ""); 
            $li_monsubart_3 = (!empty($la_cotizaciones[$li_i]["monsubart3"]) ? $la_cotizaciones[$li_i]["monsubart3"] : ""); 

            $la_data[0]["3"]=$li_canart_1;
            $la_data[0]["4"]=number_format($li_preuniart_1,2,",",".");
            $la_data[0]["5"]=number_format($li_monsubart_1,2,",",".");     //number_format($li_totalsubtotal,2,",",".")
            $la_data[0]["6"]=$li_canart_2;
            $la_data[0]["7"]=number_format($li_preuniart_2,2,",",".");
            $la_data[0]["8"]=number_format($li_monsubart_2,2,",",".");     
            $la_data[0]["9"]=$li_canart_3;
            $la_data[0]["10"]=number_format($li_preuniart_3,2,",",".");
            $la_data[0]["11"]=number_format($li_monsubart_3,2,",",".");                       
                                   
			$la_justificaciones=array();
			$la_justificaciones = array("center","left","center","center","center","center","center","center","center","center","center");
			$la_opciones = array("color_texto"     => array(0,0,0),
								   "anchos_col"    => array(10,60,21,21,23,21,21,23,21,21,23),
								   "tamano_texto"  => 5,
								   "lineas"        => 2,
								   "alineacion_col"=> $la_justificaciones,
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
			$io_pdf->add_tabla(10,$la_data,$la_opciones);

		}//for

		//IMPRIMIENDO LOS DETALLES POR PROVEEDOR
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$li_pos=80;
		$li_count=1;

		$li_colcount=0;
		for($li_i=0;$li_i<$la_countcot;$li_i++)
		{
			//$io_pdf->ezSetY(455.7);
			if (!empty($la_data))
			   {
				 $la_data=array();
			   }

				$li_z=0;
				$li_sumcolprecio=0;
				$li_sumcoliva=0;
				$li_sumcoltotal=0;

				if ($li_colcount==0)
				{
					$ds_linea_tot1[$li_colcount]["subtot1"]=$li_sumcolprecio;
					$ds_linea_tot2[$li_colcount]["iva1"]=$li_sumcoliva;
					$ds_linea_tot3[$li_colcount]["tot1"]=$li_sumcoltotal;
				}
				if ($li_colcount==1)
				{
					$ds_linea_tot1[$li_colcount]["subtot2"]=$li_sumcolprecio;
					$ds_linea_tot2[$li_colcount]["iva2"]=$li_sumcoliva;
					$ds_linea_tot3[$li_colcount]["tot2"]=$li_sumcoltotal;
				}
				if ($li_colcount==2)
				{
					$ds_linea_tot1[$li_colcount]["subtot3"]=$li_sumcolprecio;
					$ds_linea_tot2[$li_colcount]["iva3"]=$li_sumcoliva;
					$ds_linea_tot3[$li_colcount]["tot3"]=$li_sumcoltotal;
				}
				$li_colcount++;
				$li_count++;
				$li_a=$li_a+$li_z;
				$li_pos+=65;
		}
	}//fin de uf_print_proveedores
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_proveedores2($la_cotizaciones,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_proveedores
		//		    Acess: private
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime el el listado de  proveedores participantes
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 18/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_class_report;
		global $io_funciones, $ls_bolivares;
		$li_totalcotizaciones=count($la_cotizaciones);

		//Imprimiendo primer titulo
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Cotizaciones</b>";
		$la_anchos_col = array(335);
		$la_justificaciones = array("center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 1,
							   "alineacion_col"=> $la_justificaciones,
							   "color_fondo"=>array(200,200,200),
							   "grosor_lineas_externas"=>0.5,
							   "grosor_lineas_internas"=>0.5);
		$io_pdf->ezSetDy(-10);
		$io_pdf->add_tabla(10,$la_data,$la_opciones);	//primera fila del item, color gris



		//Imprimiendo titulos columnas
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		for($li_i=0;$li_i<$li_totalcotizaciones;$li_i++)
		{
			$la_data[$li_i]["1"]="<b>Cantidad</b>";
			$la_data[$li_i]["2"]="<b>Unidad Medida</b>";
			$la_data[$li_i]["3"]="<b>Denominacion</b>";
			$la_data[$li_i]["4"]="<b>Precio/Unid. ".$ls_bolivares."</b>";
			$la_data[$li_i]["5"]="<b>Sub-Total</b>";
			$la_data[$li_i]["6"]="<b>I.V.A.</b>";
			$la_data[$li_i]["7"]="<b>Monto Total ".$ls_bolivares."</b>";
		}


			$la_anchos_col = array(15,25,40,25,25,15,30);
			$la_justificaciones = array("center","center","center","center","center","center","center");
			$la_opciones = array("color_texto"     => array(0,0,0),
								   "anchos_col"    => $la_anchos_col,
								   "tamano_texto"  => 10,
								   "lineas"        => 2,
								   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(232,232,232),
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
			$io_pdf->add_tabla(10,$la_data,$la_opciones);


		//Imprimiendo columnas
		$la_data=array();
		for($li_i=0;$li_i<$li_totalcotizaciones;$li_i++)
		{
			$la_data[$li_i]["1"]=number_format($la_cotizaciones[$li_i+1]["canart"],2,",",".");
			$la_data[$li_i]["2"]=$la_cotizaciones[$li_i+1]["denunimed"];
			$la_data[$li_i]["3"]=$la_cotizaciones[$li_i+1]["denominacion"];
			$la_data[$li_i]["4"]=number_format($la_cotizaciones[$li_i+1]["preuniart"],2,",",".");
			$la_data[$li_i]["5"]=$la_cotizaciones[$li_i+1]["monsubart"];
			$la_data[$li_i]["6"]=number_format($la_cotizaciones[$li_i+1]["moniva"],2,",",".");
			$la_data[$li_i]["7"]=number_format($la_cotizaciones[$li_i+1]["montotart"],2,",",".");

		}

			$la_justificaciones=array();
			$la_justificaciones = array("center","center","left","center","right","right","center");
			$la_opciones = array("color_texto"     => array(0,0,0),
								   "anchos_col"    => $la_anchos_col,
								   "tamano_texto"  => 10,
								   "lineas"        => 2,
								   "alineacion_col"=> $la_justificaciones,
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
			$io_pdf->add_tabla(10,$la_data,$la_opciones);
	}//fin de uf_print_proveedores
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_items($as_tipsolcot,$la_items,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_items
		//		    Acess: private
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime los items del analisis de cotizacion y su respectivo proveedor
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 17/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_class_report;
		global $io_funciones, $ls_bolivares;
		$li_totalitems=count($la_items);
		
		if($as_tipsolcot=="B")
			$ls_item="Bienes";
		else
			$ls_item="Servicios";

		//Imprimiendo primer titulo
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>$ls_item</b>";
		$la_anchos_col = array(335);
		$la_justificaciones = array("center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 10,
							 "lineas"        => 1,
							 "alineacion_col"=> $la_justificaciones,
							 "color_fondo"=>array(200,200,200),
							 "grosor_lineas_externas"=>0.5,
							 "grosor_lineas_internas"=>0.5);
		$io_pdf->ezSetDy(-5);
		$io_pdf->add_tabla(10,$la_data,$la_opciones);	//primera fila del item, color gris

		//Imprimiendo titulos columnas
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Código</b>";
		$la_data[0]["2"]="<b>Descripcion</b>";
		$la_data[0]["3"]="<b>Proveedor</b>";
		$la_data[0]["4"]="<b>Cant.</b>";
		$la_data[0]["5"]="<b>Precio/Unid. ".$ls_bolivares."</b>";
		$la_data[0]["6"]="<b>I.V.A. ".$ls_bolivares."</b>";
		$la_data[0]["7"]="<b>Monto Total ".$ls_bolivares."</b>";
		$la_data[0]["8"]="<b>Observación</b>";

		$la_anchos_col = array(25,35,75,30,35,35,35,65);
		$la_justificaciones = array("center","center","center","center","center","center","center","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 10,
							 "lineas"        => 2,
							 "alineacion_col"=> $la_justificaciones,
							 "color_fondo"=>array(232,232,232),
							 "grosor_lineas_externas"=>0.5,
							 "grosor_lineas_internas"=>0.5);
		$io_pdf->add_tabla(10,$la_data,$la_opciones);

		//Imprimiendo columnas
		$la_data=array();
		$li_totalprecio=0;
		$li_totaliva=0;
		$li_totalmonto=0;
		for($li_i=0;$li_i<$li_totalitems;$li_i++)
		{
			$la_data[$li_i]["1"]=trim($la_items[$li_i+1]["codigo"]);
			$la_data[$li_i]["2"]=$la_items[$li_i+1]["denominacion"];
			$la_data[$li_i]["3"]=$la_items[$li_i+1]["nompro"];
			$la_data[$li_i]["4"]=number_format($la_items[$li_i+1]["cantidad"],2,",",".");
			$la_data[$li_i]["5"]=number_format($la_items[$li_i+1]["precio"],2,",",".");
			$la_data[$li_i]["6"]=number_format($la_items[$li_i+1]["moniva"],2,",",".");
			$la_data[$li_i]["7"]=number_format($la_items[$li_i+1]["monto"],2,",",".");
			$la_data[$li_i]["8"]=$la_items[$li_i+1]["obsanacot"];
			$li_totalprecio+=$la_items[$li_i+1]["precio"];
			$li_totaliva+=$la_items[$li_i+1]["moniva"];
			$li_totalmonto+=$la_items[$li_i+1]["monto"];
		}

			$la_justificaciones=array();
			$la_justificaciones = array("center","left","left","right","right","right","right","left");
			$la_opciones = array("color_texto"     => array(0,0,0),
								   "anchos_col"    => $la_anchos_col,
								   "tamano_texto"  => 9,
								   "lineas"        => 2,
								   "alineacion_col"=> $la_justificaciones,
									   "grosor_lineas_externas"=>0.5,
									   "grosor_lineas_internas"=>0.5);
			$io_pdf->add_tabla(10,$la_data,$la_opciones);

		//imprimiendo totales
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Totales ".$ls_bolivares."</b>";
		$la_data[0]["2"]="<b>".number_format($li_totalprecio,2,",",".")."</b>";
		$la_data[0]["3"]="<b>".number_format($li_totaliva,2,",",".")."</b>";
		$la_data[0]["4"]="<b>".number_format($li_totalmonto,2,",",".")."</b>";

		$la_anchos_col = array(30,35,35,35);
		$la_justificaciones = array("center","right","right","right");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 10,
							 "lineas"        => 2,
							 "alineacion_col"=> $la_justificaciones,
							 "color_fondo"=>array(232,232,232));
		$io_pdf->add_tabla(145,$la_data,$la_opciones);
	}//fin de uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_subtotales($ds_contcol,$la_cotizaciones,$la_countcot,$io_ds_detalle,$as_numanacot,$as_tipsolcot,$aa_ganadores,$ds_linea_tot,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_items
		//		    Acess: private
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime los ganadores del analisis de cotizacion
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 26/08/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_class_report;
		global $io_funciones, $ls_bolivares;
		$li_totalcotizaciones=count($la_cotizaciones);
		$li_a=0;
		$ds_linea_tot1=array();
		$ds_linea_tot2=array();
		$ds_linea_tot3=array();

		$ls_codp1=$ds_contcol[0]["codpro1"];
		$ls_codp2=$ds_contcol[1]["codpro2"];
		$ls_codp3=$ds_contcol[2]["codpro3"];
		
		$ls_diaentcom1=$ds_contcol[0]["diaentcom1"];
		$ls_diaentcom2=$ds_contcol[1]["diaentcom2"];
		$ls_diaentcom3=$ds_contcol[2]["diaentcom3"];
		
		$ls_forpagcom1=$ds_contcol[0]["forpagcom1"];
		$ls_forpagcom2=$ds_contcol[1]["forpagcom2"];
		$ls_forpagcom3=$ds_contcol[2]["forpagcom3"];				

		$ls_estasitec1=$ds_contcol[0]["estasitec1"];
		$ls_estasitec2=$ds_contcol[1]["estasitec2"];
		$ls_estasitec3=$ds_contcol[2]["estasitec3"];				

		$ls_estesp1=$ds_contcol[0]["estesp1"];
		$ls_estesp2=$ds_contcol[1]["estesp2"];
		$ls_estesp3=$ds_contcol[2]["estesp3"];				

		$ls_garanacot1=$ds_contcol[0]["garanacot1"];
		$ls_garanacot2=$ds_contcol[1]["garanacot2"];
		$ls_garanacot3=$ds_contcol[2]["garanacot3"];				
		
		$subt1=0;
		$subt2=0;
		$subt3=0;
		$subiva1=0;
		$subiva2=0;
		$subiva3=0;
		$subtot1=0;
		$subtot2=0;                  
		$subtot3=0;
        $li_totrow=count($la_cotizaciones);
        //var_dump($la_cotizaciones);  print "<br>";
        //print "registror $li_totrow     p1: $ls_codp1 , p2:$ls_codp2,    p3:$ls_codp3  <br>";
		for($li_i=0;$li_i<$li_totrow;$li_i++)
		{
			if ($la_cotizaciones[$li_i+1]["cod_pro_1"]==$ls_codp1)
			{                
				$subt1=$subt1+$la_cotizaciones[$li_i+1]["monsubart1"];
				$subiva1=$subiva1+$la_cotizaciones[$li_i+1]["moniva1"];
				$subtot1=$subtot1+$la_cotizaciones[$li_i+1]["montotart1"];
			}
			if ($la_cotizaciones[$li_i+1]["cod_pro_2"]==$ls_codp2)
			{
				$subt2=$subt2+$la_cotizaciones[$li_i+1]["monsubart2"];
				$subiva2=$subiva2+$la_cotizaciones[$li_i+1]["moniva2"];
				$subtot2=$subtot2+$la_cotizaciones[$li_i+1]["montotart2"];
                //print "$subt2   ,   $subiva2  ,  $subtot2  <br>";
			}
			if ($la_cotizaciones[$li_i+1]["cod_pro_3"]==$ls_codp3)
			{
				$subt3=$subt3+$la_cotizaciones[$li_i+1]["monsubart3"];
				$subiva3=$subiva3+$la_cotizaciones[$li_i+1]["moniva3"];
				$subtot3=$subtot3+$la_cotizaciones[$li_i+1]["montotart3"];
			}
            //print "$ls_codp2:   $subt2   ,   $subiva2  ,  $subtot2  <br>";
		}

		$ds_linea_tot1=array("subtot1"=>$subt1,"subtot2"=>$subt2,"subtot3"=>$subt3);
		$ds_linea_tot2=array("iva1"=>$subiva1,"iva2"=>$subiva2,"iva3"=>$subiva3);
		$ds_linea_tot3=array("tot1"=>$subtot1,"tot2"=>$subtot2,"tot3"=>$subtot3);
		$ds_linea_tot4=array("garantia1"=>'',"garantia2"=>'',"garantia3"=>'');
		$ds_linea_tot5=array("condp1"=>'',"condp2"=>'',"condp3"=>'');
		$ds_linea_tot6=array("dfecent1"=>'',"dfecent2"=>'',"dfecent3"=>'');
		$ds_linea_tot7=array("cumple1"=>'',"cumple2"=>'',"cumple3"=>'');
		$ds_linea_tot8=array("asist1"=>'',"asist2"=>'',"asist3"=>'');

		$ds_linea_tot=array($ds_linea_tot1,$ds_linea_tot2,$ds_linea_tot3,$ds_linea_tot4,$ds_linea_tot5,$ds_linea_tot6,$ds_linea_tot7,$ds_linea_tot8);

		//$io_pdf->set_margenes(90,55,0,0);
        $li_y=$io_pdf->y;
        $io_pdf->ezSetDy(-5);
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Sub Total</b>";
		$la_data[0]["2"]=number_format($ds_linea_tot[0]["subtot1"],2,",",".");
		$la_data[0]["3"]="<b>Sub Total</b>";
		$la_data[0]["4"]=number_format($ds_linea_tot[0]["subtot2"],2,",",".");
		$la_data[0]["5"]="<b>Sub Total</b>";
		$la_data[0]["6"]=number_format($ds_linea_tot[0]["subtot3"],2,",",".");
		$la_anchos_col = array(42,23,42,23,42,23);
		$la_justificaciones = array("left","center","left","center","left","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 7,
							 "lineas"        => 1,
							 "alineacion_col"=> $la_justificaciones,
							 "grosor_lineas_externas"=>0.5,
							 "grosor_lineas_internas"=>0.5);

		$io_pdf->add_tabla(80,$la_data,$la_opciones);

		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>IVA</b>";
		$la_data[0]["2"]=number_format($ds_linea_tot[1]["iva1"],2,",",".");
		$la_data[0]["3"]="<b>IVA</b>";
		$la_data[0]["4"]=number_format($ds_linea_tot[1]["iva2"],2,",",".");
		$la_data[0]["5"]="<b>IVA</b>";
		$la_data[0]["6"]=number_format($ds_linea_tot[1]["iva3"],2,",",".");
		$la_anchos_col = array(42,23,42,23,42,23);
		$la_justificaciones = array("left","center","left","center","left","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 7,
							 "lineas"        => 1,
							 "alineacion_col"=> $la_justificaciones,
							 "grosor_lineas_externas"=>0.5,
							 "grosor_lineas_internas"=>0.5);

		$io_pdf->add_tabla(80,$la_data,$la_opciones);

		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>TOTAL</b>";
		$la_data[0]["2"]=number_format($ds_linea_tot[2]["tot1"],2,",",".");
		$la_data[0]["3"]="<b>TOTAL</b>";
		$la_data[0]["4"]=number_format($ds_linea_tot[2]["tot2"],2,",",".");
		$la_data[0]["5"]="<b>TOTAL</b>";
		$la_data[0]["6"]=number_format($ds_linea_tot[2]["tot3"],2,",",".");
		$la_anchos_col = array(42,23,42,23,42,23);
		$la_justificaciones = array("left","center","left","center","left","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 7,
							 "lineas"        => 1,
							 "alineacion_col"=> $la_justificaciones,
							 "grosor_lineas_externas"=>0.5,
							 "grosor_lineas_internas"=>0.5);

		$io_pdf->add_tabla(80,$la_data,$la_opciones);

		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["0"]="GARANTIAS";
		$la_data[0]["1"]=$ls_garanacot1;
		$la_data[0]["2"]=$ls_garanacot2;
		$la_data[0]["3"]=$ls_garanacot3;
		$la_anchos_col = array(69,65,65,65);
		$la_justificaciones = array("left","center","center","center","center","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 7,
							 "lineas"        => 1,
							 "alineacion_col"=> $la_justificaciones,
							 "grosor_lineas_externas"=>0.5,
							 "grosor_lineas_internas"=>0.5);

		$io_pdf->add_tabla(11,$la_data,$la_opciones);

		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["0"]="CONDICIONES DE PAGO";
		$la_data[0]["1"]=$ls_forpagcom1;
		$la_data[0]["2"]=$ls_forpagcom2;
		$la_data[0]["3"]=$ls_forpagcom3;
		$la_anchos_col = array(69,65,65,65);
		$la_justificaciones = array("left","center","center","center","left","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 7,
							 "lineas"        => 1,
							 "alineacion_col"=> $la_justificaciones,
							 "grosor_lineas_externas"=>0.5,
							 "grosor_lineas_internas"=>0.5);

		$io_pdf->add_tabla(11,$la_data,$la_opciones);

		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["0"]="FECHA DE ENTREGA (DIAS)";
		$la_data[0]["1"]=$ls_diaentcom1;
		$la_data[0]["2"]=$ls_diaentcom2;
		$la_data[0]["3"]=$ls_diaentcom3;
		$la_anchos_col = array(69,65,65,65);
		$la_justificaciones = array("left","center","center","center","left","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 7,
							 "lineas"        => 1,
							 "alineacion_col"=> $la_justificaciones,
							 "grosor_lineas_externas"=>0.5,
							 "grosor_lineas_internas"=>0.5);

		$io_pdf->add_tabla(11,$la_data,$la_opciones);

		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["0"]="CUMPLE CON ESPECIFICACIONES";
		$la_data[0]["1"]=$ls_estesp1;
		$la_data[0]["2"]=$ls_estesp2;
		$la_data[0]["3"]=$ls_estesp3;
		$la_anchos_col = array(69,65,65,65);
		$la_justificaciones = array("left","center","center","center","center","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 7,
							 "lineas"        => 1,
							 "alineacion_col"=> $la_justificaciones,
							 "grosor_lineas_externas"=>0.5,
							 "grosor_lineas_internas"=>0.5);

		$io_pdf->add_tabla(11,$la_data,$la_opciones);

		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["0"]="ASISTENCIA TECNICA";
		$la_data[0]["1"]=$ls_estasitec1;
		$la_data[0]["2"]=$ls_estasitec2;
		$la_data[0]["3"]=$ls_estasitec3;
		$la_anchos_col = array(69,65,65,65);
		$la_justificaciones = array("left","center","center","center","center","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							 "anchos_col"    => $la_anchos_col,
							 "tamano_texto"  => 7,
							 "lineas"        => 1,
							 "alineacion_col"=> $la_justificaciones,
							 "grosor_lineas_externas"=>0.5,
							 "grosor_lineas_internas"=>0.5);

		$io_pdf->add_tabla(11,$la_data,$la_opciones);
	}//fin de uf_print_subtotales
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------

	function uf_print_ganadores($as_numanacot,$as_tipsolcot,$aa_ganadores,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_items
		//		    Acess: private
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime los ganadores del analisis de cotizacion
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 26/08/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_class_report;
		global $io_funciones, $ls_bolivares;

		//Imprimiendo primer titulo
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Resumen de Proveedores Ganadores</b>";
		$la_anchos_col = array(260);
		$la_justificaciones = array("center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 1,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(200,200,200),
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
		$io_pdf->ezSetDy(-10);
		$io_pdf->add_tabla(10,$la_data,$la_opciones);	//primera fila del item, color gris

		//Imprimiendo titulos columnas
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Código</b>";
		$la_data[0]["2"]="<b>Nombre</b>";
		$la_data[0]["3"]="<b>Subtotal ".$ls_bolivares."</b>";
		$la_data[0]["4"]="<b>Total Cargos ".$ls_bolivares."</b>";
		$la_data[0]["5"]="<b>Monto Total ".$ls_bolivares."</b>";

		$la_anchos_col = array(25,80,52,51,52);
		$la_justificaciones = array("center","center","center","center","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 2,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(232,232,232),
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
		$io_pdf->add_tabla(10,$la_data,$la_opciones);

		$la_data=array();
		$li_totalganadores=count($aa_ganadores);
		$li_totalsubtotal=0;
		$li_totaliva=0;
		$li_totalmonto=0;
		for($li_i=0;$li_i<$li_totalganadores;$li_i++)
		{
			$ls_proveedor		= $aa_ganadores[$li_i]["cod_pro"];
			$ls_cotizacion		= $aa_ganadores[$li_i]["numcot"];
			$ls_tipo_proveedor	= $aa_ganadores[$li_i]["tipconpro"];
			$io_class_report->uf_select_items_proveedor($ls_cotizacion,$ls_proveedor,$as_numanacot,$as_tipsolcot,$la_items,$li_totrow);
			$io_class_report->uf_calcular_montos($li_totrow,$la_items,$la_totales,$ls_tipo_proveedor);
			$la_data[$li_i]["1"]=$ls_proveedor;
			$la_data[$li_i]["2"]=$aa_ganadores[$li_i]["nompro"];
			$la_data[$li_i]["4"]=number_format($la_totales["subtotal"],2,",",".");
			$la_data[$li_i]["5"]=number_format($la_totales["totaliva"],2,",",".");
			$la_data[$li_i]["6"]=number_format($la_totales["total"],2,",",".");
			$li_totalsubtotal+=$la_totales["subtotal"];
			$li_totaliva+=$la_totales["totaliva"];
			$li_totalmonto+=$la_totales["total"];
		}

		//Imprimiendo columnas
		$la_justificaciones=array();
		$la_justificaciones = array("center","left","right","right","right");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 9,
							   "lineas"        => 2,
							   "alineacion_col"=> $la_justificaciones,
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
		$io_pdf->add_tabla(10,$la_data,$la_opciones);

		//imprimiendo totales
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Totales ".$ls_bolivares."</b>";
		$la_data[0]["2"]="<b>".number_format($li_totalsubtotal,2,",",".")."</b>";
		$la_data[0]["3"]="<b>".number_format($li_totaliva,2,",",".")."</b>";
		$la_data[0]["4"]="<b>".number_format($li_totalmonto,2,",",".")."</b>";

		$la_anchos_col = array(17,52,51,52);
		$la_justificaciones = array("center","right","right","right");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 2,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(232,232,232));
		$io_pdf->add_tabla(98,$la_data,$la_opciones);
	}//fin de uf_print_detalle

    //------------------------------------------------------------------------------------------------------------------------------------
    function uf_print_observaciones($observacion,&$io_pdf)
    {
        $la_data=array();
        $la_anchos=array();
        $la_justificaciones=array();
        $la_data[0]["1"]="<b>OBSERVACIONES:</b>";
        $la_data[0]["2"]=$observacion;        
        $la_anchos_col = array(69,195);
        $la_justificaciones = array("left","left");
        $la_opciones = array("color_texto"     => array(0,0,0),
                               "anchos_col"    => $la_anchos_col,
                               "tamano_texto"  => 10,
                               "lineas"        => 2,
                               "alineacion_col"=> $la_justificaciones);
        $io_pdf->add_tabla(11,$la_data,$la_opciones);
        
    }
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_pagina($observacion,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_pagina
		//		    Acess: private
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime el pie del reporte
		//	   Creado Por: Ing. Laura Cabré                  Modificado Por: Ing. Gloriely Fréitez
		// Fecha Creación: 17/06/2007                 Fecha de Modificación: 01/04/2008
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->Rectangle(30,100,950,70); // primer rectángulo de firmas
		$io_pdf->addText(40,160,8,"<b>OBSERVACIONES</b>");
		$io_pdf->Rectangle(30,30,950,70);
		$io_pdf->addText(40,90,8,"<b>ELABORADO Y ANALIZADO POR:</b>");
		$io_pdf->addText(40,32,8,"<b>FECHA:</b>");
		$io_pdf->line(250,30,250,100);	//VERTICAL
		$io_pdf->addText(263,90,8,"<b>REVISADO POR:</b>");
		$io_pdf->addText(263,32,8,"<b>FECHA:</b>");
		$io_pdf->line(500,30,500,100);	//VERTICAL
		$io_pdf->addText(513,90,8,"<b>AREA DE COMPRAS</b>");
		$io_pdf->addText(513,32,8,"<b>FECHA:</b>");
		$io_pdf->line(740,30,740,100);	//VERTICAL
		$io_pdf->addText(743,90,8,"<b>GCIA. ADMINISTRACION</b>");
		$io_pdf->addText(743,32,8,"<b>FECHA:</b>");
	}// end function uf_print_pie_pagina
    
    function uf_agrupa_cotizaciones($ls_countcot,$la_cotizaciones,&$la_cotizaciones_ag,$ds_contcol,$io_ds_detalle)
    {
        $io_ds_items = array();
        $la_cotizaciones_ag = array();
        
        $items=$io_ds_detalle->getRowCount("codigo");
        $li_citem = 0;
        //---> agrupa los items en un datastore sin los espacios en blanco
        for($li_i=1;$li_i<=$items;$li_i++)
        {
            $coditem = $io_ds_detalle->getValue('codigo',$li_i);
            if (!empty($coditem))
            {
                $li_citem++;
                $io_ds_items[$li_citem]["codigo"]=$coditem;
            }
        }

        $codpro1=$ds_contcol[0]["codpro1"];    
        $codpro2=$ds_contcol[1]["codpro2"];    
        $codpro3=$ds_contcol[2]["codpro3"];            

        $row_items=count($io_ds_items);
        $li_totrow=count($la_cotizaciones);

        for($li_item=1;$li_item<=$row_items;$li_item++)
        {
            for($li_p=1;$li_p<=3;$li_p++)
            {
                if($li_p==1)
                {
                    $codpro1=$ds_contcol[0]["codpro1"];
                    $codprob=$codpro1;
                }
                elseif ($li_p==2)
                {
                    $codpro2=$ds_contcol[1]["codpro2"];    
                    $codprob=$codpro2;
                }
                elseif ($li_p==3)
                {
                    $codpro3=$ds_contcol[2]["codpro3"];
                    $codprob=$codpro3;
                }                                                
                $coditem = $io_ds_items[$li_item]["codigo"];
                for($li_i=1;$li_i<=$li_totrow;$li_i++)
                {                    
                    $ls_codigo      = $la_cotizaciones[$li_i]["codigo"];
                    $ls_codp        = $la_cotizaciones[$li_i]["cod_pro"];
                    $numsolcot      = $la_cotizaciones[$li_i]["numsolcot"];
                    $estec2         = $la_cotizaciones[$li_i]["estec2"];
                    $estep2         = $la_cotizaciones[$li_i]["estep2"];
                    $garcot2        = $la_cotizaciones[$li_i]["garcot2"];
                    $numcot         = $la_cotizaciones[$li_i]["numcot"];
                    $poriva         = $la_cotizaciones[$li_i]["poriva"];
                    $feccot         = $la_cotizaciones[$li_i]["feccot"];
                    $forpagcom      = $la_cotizaciones[$li_i]["forpagcom"];
                    $diaentcom      = $la_cotizaciones[$li_i]["diaentcom"];
                    $garanacot      = $la_cotizaciones[$li_i]["garanacot"];
                    $estasitec      = $la_cotizaciones[$li_i]["estasitec"];
                    $estesp         = $la_cotizaciones[$li_i]["estesp"];
                    $denominacion   = $la_cotizaciones[$li_i]["denominacion"];
                    $denunimed      = $la_cotizaciones[$li_i]["denunimed"];
                    $nompro         = $la_cotizaciones[$li_i]["nompro"];
                    $cod_pro        = $la_cotizaciones[$li_i]["cod_pro"];
                    $rifpro         = $la_cotizaciones[$li_i]["rifpro"];
                    $dirpro         = $la_cotizaciones[$li_i]["dirpro"];
                    $montotart      = $la_cotizaciones[$li_i]["montotart"];
                    $moniva         = $la_cotizaciones[$li_i]["moniva"];
                    $canart         = $la_cotizaciones[$li_i]["canart"];
                    $preuniart      = $la_cotizaciones[$li_i]["preuniart"];
                    $monsubart      = $la_cotizaciones[$li_i]["monsubart"]; 
                    
                    //---> se icluye todos en un nuevo array mas ordenado (se incluyen varias descripciones repetidas)
                    if (($ls_codigo==$coditem)&&($ls_codp==$codprob))
                    {
                        if($li_p==1)
                        {                            
                            $la_cotizaciones_ag[$li_item]["codigo_1"]       = $ls_codigo;
                            $la_cotizaciones_ag[$li_item]["numsolcot_1"]    = $numsolcot;
                            $la_cotizaciones_ag[$li_item]["estec2_1"]       = $estec2;
                            $la_cotizaciones_ag[$li_item]["estep2_1"]       = $estep2;
                            $la_cotizaciones_ag[$li_item]["garcot2_1"]      = $garcot2;
                            $la_cotizaciones_ag[$li_item]["numcot1_1"]      = $numcot;
                            $la_cotizaciones_ag[$li_item]["poriva_1"]       = $poriva;
                            $la_cotizaciones_ag[$li_item]["feccot_1"]       = $feccot;
                            $la_cotizaciones_ag[$li_item]["forpagcom_1"]    = $forpagcom;
                            $la_cotizaciones_ag[$li_item]["diaentcom_1"]    = $diaentcom;
                            $la_cotizaciones_ag[$li_item]["garanacot_1"]    = $garanacot;
                            $la_cotizaciones_ag[$li_item]["estasitec_1"]    = $estasitec;
                            $la_cotizaciones_ag[$li_item]["estesp_1"]       = $estesp;
                            $la_cotizaciones_ag[$li_item]["denominacion_1"] = $denominacion;
                            $la_cotizaciones_ag[$li_item]["denunimed_1"]    = $denunimed;
                            $la_cotizaciones_ag[$li_item]["nompro_1"]       = $nompro;
                            $la_cotizaciones_ag[$li_item]["cod_pro_1"]      = $cod_pro;
                            $la_cotizaciones_ag[$li_item]["rifpro_1"]       = $rifpro;
                            $la_cotizaciones_ag[$li_item]["dirpro_1"]       = $dirpro;
                            $la_cotizaciones_ag[$li_item]["montotart_1"]    = $montotart;
                            $la_cotizaciones_ag[$li_item]["moniva_1"]       = $moniva;
                            $la_cotizaciones_ag[$li_item]["canart_1"]       = $canart;
                            $la_cotizaciones_ag[$li_item]["preuniart_1"]    = $preuniart;
                            $la_cotizaciones_ag[$li_item]["monsubart_1"]    = $monsubart;  
                        }//if
                        elseif($li_p==2)
                        {
                            $la_cotizaciones_ag[$li_item]["codigo_2"]       = $ls_codigo;
                            $la_cotizaciones_ag[$li_item]["numsolcot_2"]    = $numsolcot;
                            $la_cotizaciones_ag[$li_item]["estec2_2"]       = $estec2;
                            $la_cotizaciones_ag[$li_item]["estep2_2"]       = $estep2;
                            $la_cotizaciones_ag[$li_item]["garcot2_2"]      = $garcot2;
                            $la_cotizaciones_ag[$li_item]["numcot1_2"]      = $numcot;
                            $la_cotizaciones_ag[$li_item]["poriva_2"]       = $poriva;
                            $la_cotizaciones_ag[$li_item]["feccot_2"]       = $feccot;
                            $la_cotizaciones_ag[$li_item]["forpagcom_2"]    = $forpagcom;
                            $la_cotizaciones_ag[$li_item]["diaentcom_2"]    = $diaentcom;
                            $la_cotizaciones_ag[$li_item]["garanacot_2"]    = $garanacot;
                            $la_cotizaciones_ag[$li_item]["estasitec_2"]    = $estasitec;
                            $la_cotizaciones_ag[$li_item]["estesp_2"]       = $estesp;
                            $la_cotizaciones_ag[$li_item]["denominacion_2"] = $denominacion;
                            $la_cotizaciones_ag[$li_item]["denunimed_2"]    = $denunimed;
                            $la_cotizaciones_ag[$li_item]["nompro_2"]       = $nompro;
                            $la_cotizaciones_ag[$li_item]["cod_pro_2"]      = $cod_pro;
                            $la_cotizaciones_ag[$li_item]["rifpro_2"]       = $rifpro;
                            $la_cotizaciones_ag[$li_item]["dirpro_2"]       = $dirpro;
                            $la_cotizaciones_ag[$li_item]["montotart_2"]    = $montotart;
                            $la_cotizaciones_ag[$li_item]["moniva_2"]       = $moniva;
                            $la_cotizaciones_ag[$li_item]["canart_2"]       = $canart;
                            $la_cotizaciones_ag[$li_item]["preuniart_2"]    = $preuniart;
                            $la_cotizaciones_ag[$li_item]["monsubart_2"]    = $monsubart;                         
                        }//if
                        elseif($li_p==3)
                        {
                            $la_cotizaciones_ag[$li_item]["codigo_3"]       = $ls_codigo;
                            $la_cotizaciones_ag[$li_item]["numsolcot_3"]    = $numsolcot;
                            $la_cotizaciones_ag[$li_item]["estec2_3"]       = $estec2;
                            $la_cotizaciones_ag[$li_item]["estep2_3"]       = $estep2;
                            $la_cotizaciones_ag[$li_item]["garcot2_3"]      = $garcot2;
                            $la_cotizaciones_ag[$li_item]["numcot1_3"]      = $numcot;
                            $la_cotizaciones_ag[$li_item]["poriva_3"]       = $poriva;
                            $la_cotizaciones_ag[$li_item]["feccot_3"]       = $feccot;
                            $la_cotizaciones_ag[$li_item]["forpagcom_3"]    = $forpagcom;
                            $la_cotizaciones_ag[$li_item]["diaentcom_3"]    = $diaentcom;
                            $la_cotizaciones_ag[$li_item]["garanacot_3"]    = $garanacot;
                            $la_cotizaciones_ag[$li_item]["estasitec_3"]    = $estasitec;
                            $la_cotizaciones_ag[$li_item]["estesp_3"]       = $estesp;
                            $la_cotizaciones_ag[$li_item]["denominacion_3"] = $denominacion;
                            $la_cotizaciones_ag[$li_item]["denunimed_3"]    = $denunimed;
                            $la_cotizaciones_ag[$li_item]["nompro_3"]       = $nompro;
                            $la_cotizaciones_ag[$li_item]["cod_pro_3"]      = $cod_pro;
                            $la_cotizaciones_ag[$li_item]["rifpro_3"]       = $rifpro;
                            $la_cotizaciones_ag[$li_item]["dirpro_3"]       = $dirpro;
                            $la_cotizaciones_ag[$li_item]["montotart_3"]    = $montotart;
                            $la_cotizaciones_ag[$li_item]["moniva_3"]       = $moniva;
                            $la_cotizaciones_ag[$li_item]["canart_3"]       = $canart;
                            $la_cotizaciones_ag[$li_item]["preuniart_3"]    = $preuniart;
                            $la_cotizaciones_ag[$li_item]["monsubart_3"]    = $monsubart;                         
                        }//if 
                    }//if
                } //for  de $la_cotizaciones
            }//for proveedores (maximo 3)            
        }//for de la cantidad de items
    }
    //--------------------------------------------------------------------------------------------------------------------------------
    function evalua_campos($ai_tipo,$as_valor_e)
    {
        $as_valor_r = '';
        if ($ai_tipo==1)    //estasitec
        {
            if (empty($as_valor_e)) 
            {
               $as_valor_r = '';
            }
            if($as_valor_e=="1")
            {
                $as_valor_r = "Sí";
            }
            else
            {
                $as_valor_r = "No";
            } 
        }
        if ($ai_tipo==2)    //estesp        
        {
            if (empty($as_valor_e)) 
            {
               $as_valor_r = '';
            }
            if($as_valor_e=="1")
            {
                $as_valor_r = "Sí Cumple";
            }
            else
            {
                $as_valor_r = "No Cumple";
            }
        }
        return $as_valor_r;        
    }

	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------

	require_once("sigesp_soc_class_report.php");
	require_once('../../shared/class_folder/class_pdf.php');
	require_once("../class_folder/class_funciones_soc.php");
	require_once("../../shared/class_folder/class_funciones.php");
	require_once("../../shared/class_folder/class_datastore.php");
	$io_class_report = new sigesp_soc_class_report();
	$io_funciones    = new class_funciones();
	$io_fun_compra   = new class_funciones_soc();
	$ls_tiporeporte=$io_fun_compra->uf_obtenervalor_get("tiporeporte",1);
	$ls_bolivares="Bs.";
	$io_ds_detalle=new class_datastore();
	$io_ds_detallecot=new class_datastore();
	$io_ds_detallepro=new class_datastore();
	$io_ds_grupodetallepro=new class_datastore();

	$ds_linea_tot=array();

	$li_calculado = 0;

	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_class_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
	}
	error_reporting(E_ALL);
	set_time_limit(3000);
	$io_pdf=new class_pdf('LETTER','landscape');                    // Instancia de la clase PDF
	$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm');  // Seleccionamos el tipo de letra
	$io_pdf->numerar_paginas(7);
	$io_pdf->set_margenes(62,12,3,3);
	$ls_tipsolcot=$_GET["tipsolcot"];
    
	$ls_numanacot=$_GET["numanacot"];
	$ld_fecha=$_GET["fecha"];
	$ls_observacion=$_GET["observacion"];
	$lb_valido=uf_insert_seguridad();
	if($lb_valido)
	{
		//uf_print_encabezado_pagina($ls_numanacot,$ld_fecha,$ds_contcol,$io_pdf);
        $ls_codpro1 = '';
        $ls_codpro2 = '';
        $ls_codpro3 = '';
        $lb_valido=$io_class_report->uf_cargar_cotizaciones_v2($ls_numanacot,$la_cotizaciones,$ls_tipsolcot,&$la_proveedor,&$li_cotizaciones,&$ls_codpro1,&$ls_codpro2,&$ls_codpro3);
		if($lb_valido)
		{
			$li_totcot=$li_cotizaciones;
			
			$io_ds_detalle->insertRow("codigo",$la_cotizaciones[1]["codigo"]);

            if ($li_totcot==1)
            {
                $io_ds_detallepro->insertRow("cod_pro_1",$ls_codpro1);
                $io_ds_detallepro->insertRow("cod_pro_2",'');
                $io_ds_detallepro->insertRow("cod_pro_3",'');
                
                $io_ds_detallepro->insertRow("nompro_1",$la_proveedor[1]["nompro"]);
                $io_ds_detallepro->insertRow("nompro_2",'');
                $io_ds_detallepro->insertRow("nompro_3",'');                
                
                $io_ds_detallepro->insertRow("dirpro_1",$la_proveedor[1]["dirpro"]);
                $io_ds_detallepro->insertRow("dirpro_2",'');
                $io_ds_detallepro->insertRow("dirpro_3",'');
                
                $io_ds_detallepro->insertRow("rifpro_1",$la_proveedor[1]["rifpro"]);
                $io_ds_detallepro->insertRow("rifpro_2",'');
                $io_ds_detallepro->insertRow("rifpro_3",''); 
                               
                $io_ds_detallepro->insertRow("feccot_1",$la_proveedor[1]["feccot"]);
                $io_ds_detallepro->insertRow("feccot_2",'');
                $io_ds_detallepro->insertRow("feccot_3",'');
                
                $io_ds_detallepro->insertRow("diaentcom_1",$la_proveedor[1]["diaentcom"]);
                $io_ds_detallepro->insertRow("diaentcom_2",'');
                $io_ds_detallepro->insertRow("diaentcom_3",'');
                
                $io_ds_detallepro->insertRow("forpagcom_1",$la_proveedor[1]["forpagcom"]);
                $io_ds_detallepro->insertRow("forpagcom_2",'');
                $io_ds_detallepro->insertRow("forpagcom_3",''); 
                               
                $io_ds_detallepro->insertRow("estasitec_1",$la_proveedor[1]["estasitec"]);
                $io_ds_detallepro->insertRow("estasitec_2",'');
                $io_ds_detallepro->insertRow("estasitec_3",'');
                
                $io_ds_detallepro->insertRow("estesp_1",$la_proveedor[1]["estesp"]);
                $io_ds_detallepro->insertRow("estesp_2",'');
                $io_ds_detallepro->insertRow("estesp_3",'');
                
                $io_ds_detallepro->insertRow("garanacot_1",$la_proveedor[1]["garanacot"]);
                $io_ds_detallepro->insertRow("garanacot_2",'');
                $io_ds_detallepro->insertRow("garanacot_3",''); 

            } 
                       
            if ($li_totcot==2)
            {
                $io_ds_detallepro->insertRow("cod_pro_1",$ls_codpro1);
                $io_ds_detallepro->insertRow("cod_pro_2",$ls_codpro2);
                $io_ds_detallepro->insertRow("cod_pro_3",'');
                
                $io_ds_detallepro->insertRow("nompro_1",$la_proveedor[1]["nompro"]);
                $io_ds_detallepro->insertRow("nompro_2",$la_proveedor[2]["nompro"]);
                $io_ds_detallepro->insertRow("nompro_3",'');                
                
                $io_ds_detallepro->insertRow("dirpro_1",$la_proveedor[1]["dirpro"]);
                $io_ds_detallepro->insertRow("dirpro_2",$la_proveedor[2]["dirpro"]);
                $io_ds_detallepro->insertRow("dirpro_3",'');
                
                $io_ds_detallepro->insertRow("rifpro_1",$la_proveedor[1]["rifpro"]);
                $io_ds_detallepro->insertRow("rifpro_2",$la_proveedor[2]["rifpro"]);
                $io_ds_detallepro->insertRow("rifpro_3",'');
                               
                $io_ds_detallepro->insertRow("feccot_1",$la_proveedor[1]["feccot"]);
                $io_ds_detallepro->insertRow("feccot_2",$la_proveedor[2]["feccot"]);
                $io_ds_detallepro->insertRow("feccot_3",'');
                
                $io_ds_detallepro->insertRow("diaentcom_1",$la_proveedor[1]["diaentcom"]);
                $io_ds_detallepro->insertRow("diaentcom_2",$la_proveedor[2]["diaentcom"]);
                $io_ds_detallepro->insertRow("diaentcom_3",'');
                
                $io_ds_detallepro->insertRow("forpagcom_1",$la_proveedor[1]["forpagcom"]);
                $io_ds_detallepro->insertRow("forpagcom_2",$la_proveedor[2]["forpagcom"]);
                $io_ds_detallepro->insertRow("forpagcom_3",''); 
                               
                $io_ds_detallepro->insertRow("estasitec_1",$la_proveedor[1]["estasitec"]);
                $io_ds_detallepro->insertRow("estasitec_2",$la_proveedor[2]["estasitec"]);
                $io_ds_detallepro->insertRow("estasitec_3",'');
                
                $io_ds_detallepro->insertRow("estesp_1",$la_proveedor[1]["estesp"]);
                $io_ds_detallepro->insertRow("estesp_2",$la_proveedor[2]["estesp"]);
                $io_ds_detallepro->insertRow("estesp_3",'');
                
                $io_ds_detallepro->insertRow("garanacot_1",$la_proveedor[1]["garanacot"]);
                $io_ds_detallepro->insertRow("garanacot_2",$la_proveedor[2]["garanacot"]);
                $io_ds_detallepro->insertRow("garanacot_3",'');
            }

            if ($li_totcot==3)
            {
                $io_ds_detallepro->insertRow("cod_pro_1",$ls_codpro1);
                $io_ds_detallepro->insertRow("cod_pro_2",$ls_codpro2);
                $io_ds_detallepro->insertRow("cod_pro_3",$ls_codpro3);
                
                $io_ds_detallepro->insertRow("nompro_1",$la_proveedor[1]["nompro"]);
                $io_ds_detallepro->insertRow("nompro_2",$la_proveedor[2]["nompro"]);
                $io_ds_detallepro->insertRow("nompro_3",$la_proveedor[3]["nompro"]);                
                
                $io_ds_detallepro->insertRow("dirpro_1",$la_proveedor[1]["dirpro"]);
                $io_ds_detallepro->insertRow("dirpro_2",$la_proveedor[2]["dirpro"]);
                $io_ds_detallepro->insertRow("dirpro_3",$la_proveedor[3]["dirpro"]);
                
                $io_ds_detallepro->insertRow("rifpro_1",$la_proveedor[1]["rifpro"]);
                $io_ds_detallepro->insertRow("rifpro_2",$la_proveedor[2]["rifpro"]);
                $io_ds_detallepro->insertRow("rifpro_3",$la_proveedor[3]["rifpro"]);
                               
                $io_ds_detallepro->insertRow("feccot_1",$la_proveedor[1]["feccot"]);
                $io_ds_detallepro->insertRow("feccot_2",$la_proveedor[2]["feccot"]);
                $io_ds_detallepro->insertRow("feccot_3",$la_proveedor[3]["feccot"]);
                
                $io_ds_detallepro->insertRow("diaentcom_1",$la_proveedor[1]["diaentcom"]);
                $io_ds_detallepro->insertRow("diaentcom_2",$la_proveedor[2]["diaentcom"]);
                $io_ds_detallepro->insertRow("diaentcom_3",$la_proveedor[3]["diaentcom"]);
                
                $io_ds_detallepro->insertRow("forpagcom_1",$la_proveedor[1]["forpagcom"]);
                $io_ds_detallepro->insertRow("forpagcom_2",$la_proveedor[2]["forpagcom"]);
                $io_ds_detallepro->insertRow("forpagcom_3",$la_proveedor[3]["forpagcom"]); 
                               
                $io_ds_detallepro->insertRow("estasitec_1",$la_proveedor[1]["estasitec"]);
                $io_ds_detallepro->insertRow("estasitec_2",$la_proveedor[2]["estasitec"]);
                $io_ds_detallepro->insertRow("estasitec_3",$la_proveedor[3]["estasitec"]);
                
                $io_ds_detallepro->insertRow("estesp_1",$la_proveedor[1]["estesp"]);
                $io_ds_detallepro->insertRow("estesp_2",$la_proveedor[2]["estesp"]);
                $io_ds_detallepro->insertRow("estesp_3",$la_proveedor[3]["estesp"]);
                
                $io_ds_detallepro->insertRow("garanacot_1",$la_proveedor[1]["garanacot"]);
                $io_ds_detallepro->insertRow("garanacot_2",$la_proveedor[2]["garanacot"]);
                $io_ds_detallepro->insertRow("garanacot_3",$la_proveedor[3]["garanacot"]);
            }
            
			$ds_col1=array();
			$ds_col2=array();
			$ds_col3=array();

			$li_cant_pro=$io_ds_detallepro->getRowCount('cod_pro');
			$cCol=1;
                
                $codpro1        = $io_ds_detallepro->getValue('cod_pro_1',1 );
                $codpro2        = $io_ds_detallepro->getValue('cod_pro_2',1 );
                $codpro3        = $io_ds_detallepro->getValue('cod_pro_3',1 ); 
                //print "codigos 1 $codpro1   2 $codpro2    3 $codpro3  <br>";
                $nompro1        = $io_ds_detallepro->getValue('nompro_1',1 );
                $nompro2        = $io_ds_detallepro->getValue('nompro_2',1 );
                $nompro3        = $io_ds_detallepro->getValue('nompro_3',1 );
                $rifpro1        = $io_ds_detallepro->getValue('rifpro_1',1 );
                $rifpro2        = $io_ds_detallepro->getValue('rifpro_2',1 );
                $rifpro3        = $io_ds_detallepro->getValue('rifpro_3',1 );
                $dirpro1        = $io_ds_detallepro->getValue('dirpro_1',1 );
                $dirpro2        = $io_ds_detallepro->getValue('dirpro_2',1 );
                $dirpro3        = $io_ds_detallepro->getValue('dirpro_3',1 );
                $feccot1        = $io_ds_detallepro->getValue('feccot_1',1 );
                $feccot2        = $io_ds_detallepro->getValue('feccot_2',1 );
                $feccot3        = $io_ds_detallepro->getValue('feccot_3',1 );
                $diaentcom1     = $io_ds_detallepro->getValue('diaentcom_1',1 );
                $diaentcom2     = $io_ds_detallepro->getValue('diaentcom_2',1 );
                $diaentcom3     = $io_ds_detallepro->getValue('diaentcom_3',1 );
                $forpagcom1     = $io_ds_detallepro->getValue('forpagcom_1',1 );
                $forpagcom2     = $io_ds_detallepro->getValue('forpagcom_2',1 );
                $forpagcom3     = $io_ds_detallepro->getValue('forpagcom_3',1 );
                $garanacot1     = $io_ds_detallepro->getValue('garanacot_1',1 );
                $garanacot2     = $io_ds_detallepro->getValue('garanacot_2',1 ); 
                $garanacot3     = $io_ds_detallepro->getValue('garanacot_3',1 );
                  
                $estasitec1     = evalua_campos(1,$io_ds_detallepro->getValue('estasitec_1',1 ));
                $estasitec2     = evalua_campos(1,$io_ds_detallepro->getValue('estasitec_2',1 ));
                $estasitec3     = evalua_campos(1,$io_ds_detallepro->getValue('estasitec_3',1 ));
                $estesp1        = evalua_campos(2,$io_ds_detallepro->getValue('estesp_1',1 ));
                $estesp2        = evalua_campos(2,$io_ds_detallepro->getValue('estesp_2',1 ));
                $estesp3        = evalua_campos(2,$io_ds_detallepro->getValue('estesp_3',1 ));

				$ds_col1 = array("nombre1"=>$nompro1,"rif1"=>$rifpro1,"dirpro1"=>$dirpro1,"feccot1"=>$feccot1,"codpro1"=>$codpro1,"diaentcom1"=>$diaentcom1,"forpagcom1"=>$forpagcom1,"estasitec1"=>$estasitec1,"estesp1"=>$estesp1,"garanacot1"=>$garanacot1); //
				$ds_col2 = array("nombre2"=>$nompro2,"rif2"=>$rifpro2,"dirpro2"=>$dirpro2,"feccot2"=>$feccot2,"codpro2"=>$codpro2,"diaentcom2"=>$diaentcom2,"forpagcom2"=>$forpagcom2,"estasitec2"=>$estasitec2,"estesp2"=>$estesp2,"garanacot2"=>$garanacot2); //
				$ds_col3 = array("nombre3"=>$nompro3,"rif3"=>$rifpro3,"dirpro3"=>$dirpro3,"feccot3"=>$feccot3,"codpro3"=>$codpro3,"diaentcom3"=>$diaentcom3,"forpagcom3"=>$forpagcom3,"estasitec3"=>$estasitec3,"estesp3"=>$estesp3,"garanacot3"=>$garanacot3); //

				$io_ds_grupodetallepro->insertRow("nompro1",$nompro1);
				$io_ds_grupodetallepro->insertRow("rifpro1",$rifpro1);
				$io_ds_grupodetallepro->insertRow("feccot1",$feccot1);
				$io_ds_grupodetallepro->insertRow("dirpro1",$dirpro1);
                
                $io_ds_grupodetallepro->insertRow("nompro2",$nompro2);
                $io_ds_grupodetallepro->insertRow("rifpro2",$rifpro2);
                $io_ds_grupodetallepro->insertRow("feccot2",$feccot2);
                $io_ds_grupodetallepro->insertRow("dirpro2",$dirpro2);
                
                $io_ds_grupodetallepro->insertRow("nompro3",$nompro3);
                $io_ds_grupodetallepro->insertRow("rifpro3",$rifpro3);
                $io_ds_grupodetallepro->insertRow("feccot3",$feccot3);
                $io_ds_grupodetallepro->insertRow("dirpro3",$dirpro3);

			$lb_valido=$io_class_report->uf_count_cotizaciones($ls_numanacot,$ls_countcot,$ls_tipsolcot);
			$ls_countcot=count($ls_countcot);

			$ds_contcol=array($ds_col1,$ds_col2,$ds_col3);   
                     
			uf_print_encabezado_pagina($ls_numanacot,$ld_fecha,$ds_contcol,$io_pdf);
            if($lb_valido)
			{
                //imprime los items
				uf_print_proveedores($la_cotizaciones,$io_ds_detalle,$io_ds_detallepro,$ls_countcot,$ds_contcol,$li_calculado,$io_pdf);			
            }
			$lb_valido=$io_class_report->uf_select_items($ls_numanacot,$ls_tipsolcot,$la_items);
			if($lb_valido)
			{
				
				$la_ganadores=$io_class_report->uf_select_cotizacion_analisis($ls_numanacot,$ls_tipsolcot);
				uf_print_subtotales($ds_contcol,$la_cotizaciones,$ls_countcot,$io_ds_detalle,$ls_numanacot,$ls_tipsolcot,$la_ganadores,$ds_linea_tot,$io_pdf);
                uf_print_observaciones($ls_observacion,&$io_pdf)  ;
				uf_print_ganadores($ls_numanacot,$ls_tipsolcot,$la_ganadores,$io_pdf);
				//uf_print_pie_pagina($ls_observacion,$io_pdf);
				$io_pdf->ezStream();
				unset($io_pdf);
			}
		}
	}
	if(!$lb_valido)
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que reportar');");
		//print(" close();");
		print("</script>");
	}
?>
