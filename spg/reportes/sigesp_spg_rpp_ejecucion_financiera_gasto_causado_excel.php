<?php
    session_start();
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";
	}


	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_detallado($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
										 $as_denestpro1,$as_denestpro2,$as_denestpro3,$as_denestpro4,$as_denestpro5,
										 $ls_desper,$ls_lapso_meses,$ls_text_periodo,&$li_fila,$lo_titulo,$lo_hoja,$lo_libro,$ai_consolidado)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: privates
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Arnaldo Suárez
	    // Fecha Creación: 21/04/2010
		/////////////////////////////////////////////////////////////////////////////////////////////////////////
        if($li_fila == 2)
		{
		 $ls_descripcion = "PERIODO: ".strtoupper($ls_desper)." CORRESPONDIENTE A ".strtoupper($ls_text_periodo);
		 $lo_hoja->write($li_fila, 0, $ls_descripcion,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		} 
		$li_fila++;
		$li_fila++;
		
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	    $ls_codestpro1=substr($as_codestpro1,-$ls_loncodestpro1);

		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	    $ls_codestpro2=substr($as_codestpro2,-$ls_loncodestpro2);

		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	    $ls_codestpro3=substr($as_codestpro3,-$ls_loncodestpro3);
		
		if($_SESSION["la_empresa"]["estmodest"] == 2)
		{
		 $ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
	     $ls_codestpro4=substr($as_codestpro4,-$ls_loncodestpro4);

		 $ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
	     $ls_codestpro5=substr($as_codestpro5,-$ls_loncodestpro5);
		}

		if($as_codestpro2!="")
		{
		  $ls_tituto_2=strtoupper($_SESSION["la_empresa"]["nomestpro2"]);
		}
		else
		{
		  $ls_tituto_2="";
		}
		if($as_codestpro3!="")
		{
		  $ls_tituto_3=strtoupper($_SESSION["la_empresa"]["nomestpro3"]);
		}
		else
		{
		  $ls_tituto_3="";
		}
		if($as_codestpro4!="")
		{
		  $ls_tituto_4=strtoupper($_SESSION["la_empresa"]["nomestpro4"]);
		}
		else
		{
		  $ls_tituto_4="";
		}
		if($as_codestpro5!="")
		{
		  $ls_tituto_5=strtoupper($_SESSION["la_empresa"]["nomestpro5"]);
		}
		else
		{
		  $ls_tituto_5="";
		}
        if($_SESSION["la_empresa"]["estmodest"] == 1)
		{
		 $lo_hoja->write($li_fila, 0, strtoupper($_SESSION["la_empresa"]["nomestpro1"]).': '.$ls_codestpro1.' - '.$as_denestpro1,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_2.': '.$ls_codestpro2.' - '.$as_denestpro2,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_3.': '.$ls_codestpro3.' - '.$as_denestpro3,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		}
		elseif($_SESSION["la_empresa"]["estmodest"] == 2)
		{
		 $lo_hoja->write($li_fila, 0, strtoupper($_SESSION["la_empresa"]["nomestpro1"]).': '.$ls_codestpro1.' - '.$as_denestpro1,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_2.': '.$ls_codestpro2.' - '.$as_denestpro2,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_3.': '.$ls_codestpro3.' - '.$as_denestpro3,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_4.': '.$ls_codestpro4.' - '.$as_denestpro4,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_5.' :'.$ls_codestpro5.' - '.$as_denestpro5,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		}
		 $li_fila++;
		 $li_fila++;

	}// end function uf_print_cabecera_detallado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_consolidado($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
	                                       $as_denestpro1,$as_denestpro2,$as_denestpro3,$as_denestpro4,$as_denestpro5,
										   $as_codestpro1h,$as_codestpro2h,$as_codestpro3h,$as_codestpro4h,$as_codestpro5h,
	                                       $as_denestpro1h,$as_denestpro2h,$as_denestpro3h,$as_denestpro4h,$as_denestpro5h,
							               $ls_desper,$ls_lapso_meses,$ls_text_periodo,&$li_fila,$lo_titulo,$lo_hoja,$lo_libro,$li_consolidado)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_consolidado
		//		   Access: privates
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Arnaldo Suárez
	    // Fecha Creación: 21/04/2010
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		$ls_descripcion = "PERIODO: ".strtoupper($ls_desper)." CORRESPONDIENTE A ".strtoupper($ls_text_periodo);
		$lo_hoja->write($li_fila, 0, $ls_descripcion,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		$li_fila++;
		$li_fila++;
		
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	    $ls_codestpro1=substr($as_codestpro1,-$ls_loncodestpro1);
		$ls_codestpro1h=substr($as_codestpro1h,-$ls_loncodestpro1);

		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	    $ls_codestpro2=substr($as_codestpro2,-$ls_loncodestpro2);
		$ls_codestpro2h=substr($as_codestpro2h,-$ls_loncodestpro2);

		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	    $ls_codestpro3=substr($as_codestpro3,-$ls_loncodestpro3);
		$ls_codestpro3h=substr($as_codestpro3h,-$ls_loncodestpro3);
		
		if($_SESSION["la_empresa"]["estmodest"] == 2)
		{
		 $ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
	     $ls_codestpro4=substr($as_codestpro4,-$ls_loncodestpro4);
		 $ls_codestpro4h=substr($as_codestpro4h,-$ls_loncodestpro4);

		 $ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
	     $ls_codestpro5=substr($as_codestpro5,-$ls_loncodestpro5);
		 $ls_codestpro5h=substr($as_codestpro5h,-$ls_loncodestpro5);
		}

		if($as_codestpro2!="")
		{
		  $ls_tituto_2=strtoupper($_SESSION["la_empresa"]["nomestpro2"]);
		}
		else
		{
		  $ls_tituto_2="";
		}
		if($as_codestpro3!="")
		{
		  $ls_tituto_3=strtoupper($_SESSION["la_empresa"]["nomestpro3"]);
		}
		else
		{
		  $ls_tituto_3="";
		}
		if($as_codestpro4!="")
		{
		  $ls_tituto_4=strtoupper($_SESSION["la_empresa"]["nomestpro4"]);
		}
		else
		{
		  $ls_tituto_4="";
		}
		if($as_codestpro5!="")
		{
		  $ls_tituto_5=strtoupper($_SESSION["la_empresa"]["nomestpro5"]);
		}
		else
		{
		  $ls_tituto_5="";
		}
        if($_SESSION["la_empresa"]["estmodest"] == 1)
		{
		 $lo_hoja->write($li_fila, 0, strtoupper($_SESSION["la_empresa"]["nomestpro1"]).': DESDE: '.$ls_codestpro1.' - '.$as_denestpro1,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $lo_hoja->write($li_fila, 2,'HASTA: '.$ls_codestpro1h.' - '.$as_denestpro1h,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_2.': DESDE:'.$ls_codestpro2.' - '.$as_denestpro2,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $lo_hoja->write($li_fila, 2, 'HASTA: '.$ls_codestpro2h.' - '.$as_denestpro2h,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_3.': DESDE: '.$ls_codestpro3.' - '.$as_denestpro3,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $lo_hoja->write($li_fila, 2, 'HASTA: '.$ls_codestpro3h.' - '.$as_denestpro3h,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		}
		elseif($_SESSION["la_empresa"]["estmodest"] == 2)
		{
		 $lo_hoja->write($li_fila, 0, strtoupper($_SESSION["la_empresa"]["nomestpro1"]).': DESDE: '.$ls_codestpro1.' - '.$as_denestpro1,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $lo_hoja->write($li_fila, 2,'HASTA: '.$ls_codestpro1h.' - '.$as_denestpro1h,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_2.': DESDE:'.$ls_codestpro2.' - '.$as_denestpro2,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $lo_hoja->write($li_fila, 2,'HASTA'.$ls_codestpro2h.' - '.$as_denestpro2h,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_3.': DESDE: '.$ls_codestpro3.' - '.$as_denestpro3,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $lo_hoja->write($li_fila, 2,'HASTA: '.$ls_codestpro3h.' - '.$as_denestpro3h,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_4.': DESDE: '.$ls_codestpro4.' - '.$as_denestpro4,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $lo_hoja->write($li_fila, 0,'HASTA: '.$ls_codestpro4h.' - '.$as_denestpro4h,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		 $lo_hoja->write($li_fila, 0, $ls_tituto_5.': DESDE: '.$ls_codestpro5.' - '.$as_denestpro5,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $lo_hoja->write($li_fila, 0,'HASTA: '.$ls_codestpro5h.' - '.$as_denestpro5h,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'10')));
		 $li_fila++;
		}
		
		 $li_fila++;
		 $li_fila++;

	}// end function uf_print_cabecera_consolidado
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_cabecera_detalle($lo_libro,$lo_hoja,$io_encabezado,$ai_estilo,$as_nomper01,$as_nomper02,$as_nomper03,$ad_fecha,&$li_fila,$ai_consolidado)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barragán
	    // Fecha Creación: 12/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		switch($ai_estilo)
		{
		 case 1:
					$lo_hoja->write($li_fila, 0, 'CODIGO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 1, 'DENOMINACION',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 2, 'DISPONIBILIDAD',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 3, $as_nomper01,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 4, 'TOTAL CAUSADO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 5, 'AJUSTE/COMP',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 6, 'MOD. PRES',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 7, 'COMPROMETIDO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 8, 'LIBER./COMPROMISO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 9, 'DISPONIBLE AL: '.$ad_fecha,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$li_fila++;
		        break;

		 case 2:
					$lo_hoja->write($li_fila, 0, 'CODIGO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 1, 'DENOMINACION',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 2, 'DISPONIBILIDAD',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 3, $as_nomper01,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 4, $as_nomper02,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 5, 'TOTAL CAUSADO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 6, 'AJUSTE/COMP',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 7, 'MOD. PRES',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 8, 'COMPROMETIDO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 9, 'LIBER./COMPROMISO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 10, 'DISPONIBLE AL: '.$ad_fecha,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$li_fila++;

		       	break;

		 case 3:
		       		$lo_hoja->write($li_fila, 0, 'CODIGO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 1, 'DENOMINACION',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 2, 'DISPONIBILIDAD',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 3, $as_nomper01,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 4, $as_nomper02,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 5, $as_nomper03,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 6, 'TOTAL CAUSADO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 7, 'AJUSTE/COMP',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 8, 'MOD. PRES',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 9, 'COMPROMETIDO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 10, 'LIBER./COMPROMISO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$lo_hoja->write($li_fila, 11, 'DISPONIBLE AL: '.$ad_fecha,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
					$li_fila++;
		        break;
		}

	}// end function uf_print_cabecera_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	/*function uf_print_detalle($li_tot,$lo_libro,$lo_hoja,$la_data,$ai_estilo,$as_nomper01,$as_nomper02,$as_nomper03,$as_programatica,&$li_fila,$ai_consolidado)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle
	//		    Acess: private
	//	    Arguments: la_data // arreglo de información
	//	   			   io_pdf // Objeto PDF
	//    Description: función que imprime el detalle
	//	   Creado Por: Ing.Yozelin Barragán
	// Fecha Creación: 12/09/2006
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$li_total = count($la_data);
		
		for ($index = 1; $index <= $li_total ; $index++)
		{
			if($ai_consolidado == 0)
			{
				if ((trim($la_data[$index]["cuenta"])<>'')&&(trim($la_data[$index]["programatica"])==$as_programatica))
				{
					switch($ai_estilo)
					{
						case 1:
							$lo_hoja->write($li_fila, 0, trim($la_data[$index]["cuenta"]),$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
							$lo_hoja->write($li_fila, 1, $la_data[$index]["denominacion"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
							$lo_hoja->write($li_fila, 2, $la_data[$index]["disponact"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 3, $la_data[$index]["periodo01"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 4, $la_data[$index]["totcom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 5, $la_data[$index]["ajucom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 6, $la_data[$index]["modpres"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 7, $la_data[$index]["precom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 8, $la_data[$index]["libprecom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 9, $la_data[$index]["disponible"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							//$lo_hoja->write($li_fila, 9, $li_fila,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$li_fila++;
	
						break;
	
						case 2:
							$lo_hoja->write($li_fila, 0, trim($la_data[$index]["cuenta"]),$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
							$lo_hoja->write($li_fila, 1, $la_data[$index]["denominacion"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
							$lo_hoja->write($li_fila, 2, $la_data[$index]["disponact"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 3, $la_data[$index]["periodo01"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 4, $la_data[$index]["periodo02"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 5, $la_data[$index]["totcom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 6, $la_data[$index]["ajucom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 7, $la_data[$index]["modpres"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 8, $la_data[$index]["precom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 9, $la_data[$index]["libprecom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 10, $la_data[$index]["disponible"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							//$lo_hoja->write($li_fila, 11, $li_fila,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
	
							$li_fila++;
	
						break;
	
						case 3:
							$lo_hoja->write($li_fila, 0, trim($la_data[$index]["cuenta"]),$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
							$lo_hoja->write($li_fila, 1, $la_data[$index]["denominacion"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
							$lo_hoja->write($li_fila, 2, $la_data[$index]["disponact"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 3, $la_data[$index]["periodo01"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 4, $la_data[$index]["periodo02"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 5, $la_data[$index]["periodo03"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 6, $la_data[$index]["totcom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 7, $la_data[$index]["ajucom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 8, $la_data[$index]["modpres"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 9, $la_data[$index]["precom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 10, $la_data[$index]["libprecom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							$lo_hoja->write($li_fila, 11, $la_data[$index]["disponible"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
							//$lo_hoja->write($li_fila, 14, $li_fila,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
	
							$li_fila++;
	
						break;
					}
				}
			}
			elseif ($ai_consolidado == 1)
			{
			 if ((trim($la_data[$index]["cuenta"])<>''))
			 {
				switch($ai_estilo)
				{
					case 1:
						$lo_hoja->write($li_fila, 0, trim($la_data[$index]["cuenta"]),$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 1, $la_data[$index]["denominacion"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 2, $la_data[$index]["disponact"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 3, $la_data[$index]["periodo01"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 4, $la_data[$index]["totcom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 5, $la_data[$index]["ajucom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 6, $la_data[$index]["modpres"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 7, $la_data[$index]["precom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 8, $la_data[$index]["libprecom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 9, $la_data[$index]["disponible"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						//$lo_hoja->write($li_fila, 9, $li_fila,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$li_fila++;

					break;

					case 2:
						$lo_hoja->write($li_fila, 0, trim($la_data[$index]["cuenta"]),$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 1, $la_data[$index]["denominacion"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 2, $la_data[$index]["disponact"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 3, $la_data[$index]["periodo01"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 4, $la_data[$index]["periodo02"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 5, $la_data[$index]["totcom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 6, $la_data[$index]["ajucom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 7, $la_data[$index]["modpres"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 8, $la_data[$index]["precom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 9, $la_data[$index]["libprecom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 10, $la_data[$index]["disponible"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						//$lo_hoja->write($li_fila, 11, $li_fila,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));

						$li_fila++;

					break;

					case 3:
						$lo_hoja->write($li_fila, 0, trim($la_data[$index]["cuenta"]),$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 1, $la_data[$index]["denominacion"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 2, $la_data[$index]["disponact"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 3, $la_data[$index]["periodo01"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 4, $la_data[$index]["periodo02"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 5, $la_data[$index]["periodo03"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 6, $la_data[$index]["totcom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 7, $la_data[$index]["ajucom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 8, $la_data[$index]["modpres"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 9, $la_data[$index]["precom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 10, $la_data[$index]["libprecom"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 11, $la_data[$index]["disponible"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
						//$lo_hoja->write($li_fila, 14, $li_fila,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));

						$li_fila++;

					break;
				}
			 }
			}
		}
	}*/// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($lo_libro,$lo_hoja,$la_data,$ai_estilo,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle
	//		    Acess: private
	//	    Arguments: la_data // arreglo de información
	//    Description: función que imprime el detalle
	//	   Creado Por: Ing.Arnaldo Suárez
	// Fecha Creación: 18/05/2010
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $lo_datacenter= &$lo_libro->addformat();
	$lo_datacenter->set_font("Verdana");
	$lo_datacenter->set_align('center');
	$lo_datacenter->set_size('9');
	$lo_dataleft= &$lo_libro->addformat();
	$lo_dataleft->set_text_wrap();
	$lo_dataleft->set_font("Verdana");
	$lo_dataleft->set_align('left');
	$lo_dataleft->set_size('9');
	$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00;[Red](#,##0.00)'));
	$lo_dataright->set_font("Verdana");
	$lo_dataright->set_align('right');
	$lo_dataright->set_size('9');
	if (trim($la_data[0]["cuenta"])<>'')
	{
		switch($ai_estilo)
		{
			case 1:
				$lo_hoja->write($li_fila, 0, trim($la_data[0]["cuenta"]),$lo_datacenter);
				$lo_hoja->write($li_fila, 1, $la_data[0]["denominacion"],$lo_dataleft);
				$lo_hoja->write($li_fila, 2, $la_data[0]["disponact"],$lo_dataright);
				$lo_hoja->write($li_fila, 3, $la_data[0]["periodo01"],$lo_dataright);
				$lo_hoja->write($li_fila, 4, $la_data[0]["totcom"],$lo_dataright);
				$lo_hoja->write($li_fila, 5, $la_data[0]["ajucom"],$lo_dataright);
				$lo_hoja->write($li_fila, 6, $la_data[0]["modpres"],$lo_dataright);
				$lo_hoja->write($li_fila, 7, $la_data[0]["precom"],$lo_dataright);
				$lo_hoja->write($li_fila, 8, $la_data[0]["libprecom"],$lo_dataright);
				$lo_hoja->write($li_fila, 9, $la_data[0]["disponible"],$lo_dataright);
				$li_fila++;

			break;

			case 2:
				$lo_hoja->write($li_fila, 0, trim($la_data[0]["cuenta"]),$lo_datacenter);
				$lo_hoja->write($li_fila, 1, $la_data[0]["denominacion"],$lo_dataleft);
				$lo_hoja->write($li_fila, 2, $la_data[0]["disponact"],$lo_dataright);
				$lo_hoja->write($li_fila, 3, $la_data[0]["periodo01"],$lo_dataright);
				$lo_hoja->write($li_fila, 4, $la_data[0]["periodo02"],$lo_dataright);
				$lo_hoja->write($li_fila, 5, $la_data[0]["totcom"],$lo_dataright);
				$lo_hoja->write($li_fila, 6, $la_data[0]["ajucom"],$lo_dataright);
				$lo_hoja->write($li_fila, 7, $la_data[0]["modpres"],$lo_dataright);
				$lo_hoja->write($li_fila, 8, $la_data[0]["precom"],$lo_dataright);
				$lo_hoja->write($li_fila, 9, $la_data[0]["libprecom"],$lo_dataright);
				$lo_hoja->write($li_fila, 10, $la_data[0]["disponible"],$lo_dataright);
				$li_fila++;

			break;

			case 3:
				$lo_hoja->write($li_fila, 0, trim($la_data[0]["cuenta"]),$lo_datacenter);
				$lo_hoja->write($li_fila, 1, $la_data[0]["denominacion"],$lo_dataleft);
				$lo_hoja->write($li_fila, 2, $la_data[0]["disponact"],$lo_dataright);
				$lo_hoja->write($li_fila, 3, $la_data[0]["periodo01"],$lo_dataright);
				$lo_hoja->write($li_fila, 4, $la_data[0]["periodo02"],$lo_dataright);
				$lo_hoja->write($li_fila, 5, $la_data[0]["periodo03"],$lo_dataright);
				$lo_hoja->write($li_fila, 6, $la_data[0]["totcom"],$lo_dataright);
				$lo_hoja->write($li_fila, 7, $la_data[0]["ajucom"],$lo_dataright);
				$lo_hoja->write($li_fila, 8, $la_data[0]["modpres"],$lo_dataright);
				$lo_hoja->write($li_fila, 9, $la_data[0]["precom"],$lo_dataright);
				$lo_hoja->write($li_fila, 10, $la_data[0]["libprecom"],$lo_dataright);
				$lo_hoja->write($li_fila, 11, $la_data[0]["disponible"],$lo_dataright);
				$li_fila++;

			break;
		}
	}
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_bold($lo_libro,$lo_hoja,$la_data,$ai_estilo,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle_bold
	//		    Acess: private
	//	    Arguments: la_data // arreglo de información
	//    Description: función que imprime el detalle en negrita
	//	   Creado Por: Ing.Arnaldo Suárez
	// Fecha Creación: 18/05/2010
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $lo_datacenter= &$lo_libro->addformat();
	$lo_datacenter->set_font("Verdana");
	$lo_datacenter->set_align('center');
	$lo_datacenter->set_size('9');
	$lo_datacenter->set_bold();
	$lo_dataleft= &$lo_libro->addformat();
	$lo_dataleft->set_text_wrap();
	$lo_dataleft->set_font("Verdana");
	$lo_dataleft->set_align('left');
	$lo_dataleft->set_size('9');
	$lo_dataleft->set_bold();
	$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00;[Red](#,##0.00)'));
	$lo_dataright->set_font("Verdana");
	$lo_dataright->set_align('right');
	$lo_dataright->set_size('9');
	$lo_dataright->set_bold();
	if (trim($la_data[0]["cuenta"])<>'')
	{
		switch($ai_estilo)
		{
			case 1:
				$lo_hoja->write($li_fila, 0, trim($la_data[0]["cuenta"]),$lo_datacenter);
				$lo_hoja->write($li_fila, 1, $la_data[0]["denominacion"],$lo_dataleft);
				$lo_hoja->write($li_fila, 2, $la_data[0]["disponact"],$lo_dataright);
				$lo_hoja->write($li_fila, 3, $la_data[0]["periodo01"],$lo_dataright);
				$lo_hoja->write($li_fila, 4, $la_data[0]["totcom"],$lo_dataright);
				$lo_hoja->write($li_fila, 5, $la_data[0]["ajucom"],$lo_dataright);
				$lo_hoja->write($li_fila, 6, $la_data[0]["modpres"],$lo_dataright);
				$lo_hoja->write($li_fila, 7, $la_data[0]["precom"],$lo_dataright);
				$lo_hoja->write($li_fila, 8, $la_data[0]["libprecom"],$lo_dataright);
				$lo_hoja->write($li_fila, 9, $la_data[0]["disponible"],$lo_dataright);
				$li_fila++;

			break;

			case 2:
				$lo_hoja->write($li_fila, 0, trim($la_data[0]["cuenta"]),$lo_datacenter);
				$lo_hoja->write($li_fila, 1, $la_data[0]["denominacion"],$lo_dataleft);
				$lo_hoja->write($li_fila, 2, $la_data[0]["disponact"],$lo_dataright);
				$lo_hoja->write($li_fila, 3, $la_data[0]["periodo01"],$lo_dataright);
				$lo_hoja->write($li_fila, 4, $la_data[0]["periodo02"],$lo_dataright);
				$lo_hoja->write($li_fila, 5, $la_data[0]["totcom"],$lo_dataright);
				$lo_hoja->write($li_fila, 6, $la_data[0]["ajucom"],$lo_dataright);
				$lo_hoja->write($li_fila, 7, $la_data[0]["modpres"],$lo_dataright);
				$lo_hoja->write($li_fila, 8, $la_data[0]["precom"],$lo_dataright);
				$lo_hoja->write($li_fila, 9, $la_data[0]["libprecom"],$lo_dataright);
				$lo_hoja->write($li_fila, 10, $la_data[0]["disponible"],$lo_dataright);
				$li_fila++;

			break;

			case 3:
				$lo_hoja->write($li_fila, 0, trim($la_data[0]["cuenta"]),$lo_datacenter);
				$lo_hoja->write($li_fila, 1, $la_data[0]["denominacion"],$lo_dataleft);
				$lo_hoja->write($li_fila, 2, $la_data[0]["disponact"],$lo_dataright);
				$lo_hoja->write($li_fila, 3, $la_data[0]["periodo01"],$lo_dataright);
				$lo_hoja->write($li_fila, 4, $la_data[0]["periodo02"],$lo_dataright);
				$lo_hoja->write($li_fila, 5, $la_data[0]["periodo03"],$lo_dataright);
				$lo_hoja->write($li_fila, 6, $la_data[0]["totcom"],$lo_dataright);
				$lo_hoja->write($li_fila, 7, $la_data[0]["ajucom"],$lo_dataright);
				$lo_hoja->write($li_fila, 8, $la_data[0]["modpres"],$lo_dataright);
				$lo_hoja->write($li_fila, 9, $la_data[0]["precom"],$lo_dataright);
				$lo_hoja->write($li_fila, 10, $la_data[0]["libprecom"],$lo_dataright);
				$lo_hoja->write($li_fila, 11, $la_data[0]["disponible"],$lo_dataright);
				$li_fila++;

			break;
		}
	}
	}// end function uf_print_detalle_bold
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($li_tot,$lo_libro,$lo_hoja,$la_data_tot,$ai_estilo,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila)
	{
				switch($ai_estilo)
				{
					case 1:
						$lo_hoja->write($li_fila, 0,"TOTAL",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 1, "",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 2, $la_data_tot[0]["disponact"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 3, $la_data_tot[0]["periodo01"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 4, $la_data_tot[0]["totcom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 5, $la_data_tot[0]["ajucom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 6, $la_data_tot[0]["modpres"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 7, $la_data_tot[0]["precom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 8, $la_data_tot[0]["libprecom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 9,$la_data_tot[0]["disponible"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$li_fila++;

					break;

					case 2:
						$lo_hoja->write($li_fila, 0,"TOTAL",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 1, "",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 2, $la_data_tot[0]["disponact"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 3, $la_data_tot[0]["periodo01"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 4, $la_data_tot[0]["periodo02"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 5, $la_data_tot[0]["totcom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 6, $la_data_tot[0]["ajucom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 7, $la_data_tot[0]["modpres"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 8, $la_data_tot[0]["precom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 9, $la_data_tot[0]["libprecom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 10,$la_data_tot[0]["disponible"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$li_fila++;

					break;

					case 3:
						$lo_hoja->write($li_fila, 0,"TOTAL",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 1, "",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
						$lo_hoja->write($li_fila, 2, $la_data_tot[0]["disponact"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 3, $la_data_tot[0]["periodo01"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 4, $la_data_tot[0]["periodo02"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 5, $la_data_tot[0]["periodo03"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 6, $la_data_tot[0]["totcom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 7, $la_data_tot[0]["ajucom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 8, $la_data_tot[0]["modpres"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 9, $la_data_tot[0]["precom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 10,$la_data_tot[0]["libprecom"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$lo_hoja->write($li_fila, 11,$la_data_tot[0]["disponible"],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
						$li_fila++;
						$li_fila++;

					break;
				}
	}

	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "ejecucion_financiera_del_presupuesto_causado.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();

//-----------------------------------------------------------------------------------------------------------------------------

	require_once("sigesp_spg_funciones_reportes.php");
	$io_function_report = new sigesp_spg_funciones_reportes();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("../../shared/class_folder/class_fecha.php");
	$io_fecha = new class_fecha();
//-----------------------------------------------------------------------------------------------------------------------------

	require_once("sigesp_spg_reportes_class.php");
	$io_report = new sigesp_spg_reportes_class();
	$li_candeccon=$_SESSION["la_empresa"]["candeccon"];
	$li_tipconmon=$_SESSION["la_empresa"]["tipconmon"];
	$li_redconmon=$_SESSION["la_empresa"]["redconmon"];
//------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------  Parámetros para Filtar el Reporte  ------------------------------------
	$li_estmodest    = $_SESSION["la_empresa"]["estmodest"];
	$ldt_periodo     = $_SESSION["la_empresa"]["periodo"];
	$li_ano          = substr($ldt_periodo,0,4);
	$ls_codestpro1   = $_GET["codestpro1"];
	$ls_codestpro2   = $_GET["codestpro2"];
	$ls_codestpro3   = $_GET["codestpro3"];
	$ls_codestpro1h  = $_GET["codestpro1h"];
	$ls_codestpro2h  = $_GET["codestpro2h"];
	$ls_codestpro3h  = $_GET["codestpro3h"];
    $ls_estclades    = $_GET["estclades"];
	$ls_estclahas    = $_GET["estclahas"];
	$ld_tipper       = $_GET["tipper"];
    $ld_periodo      = $_GET["periodo"];
	$ls_cuentades    = $_GET["txtcuentades"];
	$ls_cuentahas    = $_GET["txtcuentahas"];
	$li_consolidado  = $_GET["consolidado"];
	$ls_text_periodo = $_GET["tperiodo"];

	switch($ld_tipper)
	{
	 case 1:
	       $ld_per01 = intval($ld_periodo);
		   $ld_per02 = "";
		   $ld_per03 = "";
		   $ls_desper = "MENSUAL";
	       $ld_fecfinrep=$io_fecha->uf_last_day($ld_periodo,$li_ano);
	       break;

	 case 2:
	      $ld_per01 = intval(substr($ld_periodo,0,2));
		  $ld_per02 = intval(substr($ld_periodo,2,2));
		  $ls_desper = "BIMESTRAL";
		  $ld_fecfinrep=$io_fecha->uf_last_day(substr($ld_periodo,2,2),$li_ano);
		  $ld_per03 = "";
	      break;

	 case 3:
	      $ld_per01 = intval(substr($ld_periodo,0,2));
		  $ld_per02 = intval(substr($ld_periodo,2,2));
		  $ld_per03 = intval(substr($ld_periodo,4,2));
		  $ls_desper = "TRIMESTRAL";
		  $ld_fecfinrep=$io_fecha->uf_last_day(substr($ld_periodo,4,2),$li_ano);
	      break;
	}
	if($li_estmodest==1)
	{
		$ls_codestpro4  =  "0000000000000000000000000";
		$ls_codestpro5  =  "0000000000000000000000000";
		$ls_codestpro4h =  "0000000000000000000000000";
		$ls_codestpro5h =  "0000000000000000000000000";
	}
	elseif($li_estmodest==2)
	{
		$ls_codestpro4  = $_GET["codestpro4"];
		$ls_codestpro5  = $_GET["codestpro5"];
		$ls_codestpro4h = $_GET["codestpro4h"];
		$ls_codestpro5h = $_GET["codestpro5h"];
    }

	 /////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_desc_event="Solicitud de Reporte Ejecucion Financiera del Presupuesto - Causado";
	 $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_spg_r_resumen_ejecucion_financiera_causado.php",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//----------------------------------------------------  Parámetros del encabezado  ----------------------------------------------
		$ls_titulo="EJECUCION FINANCIERA DEL PRESUPUESTO DE GASTO - CAUSADO ".$ls_desper." AL ".$ld_fecfinrep." ";
//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
	if($li_estmodest==1)
	 {
		$ls_codestpro1_min = $ls_codestpro1;
		$ls_codestpro2_min = $ls_codestpro2;
		$ls_codestpro3_min = $ls_codestpro3;
		$ls_codestpro1h_max = $ls_codestpro1h;
		$ls_codestpro2h_max = $ls_codestpro2h;
		$ls_codestpro3h_max = $ls_codestpro3h;
		$ls_codestpro4_min = "0000000000000000000000000";
		$ls_codestpro5_min = "0000000000000000000000000";
		$ls_codestpro4h_max = "0000000000000000000000000";
		$ls_codestpro5h_max = "0000000000000000000000000";
		if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min==""))
		{
		  if($io_function_report->uf_spg_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,
																		 $ls_codestpro3_min,$ls_codestpro4_min,
																		 $ls_codestpro5_min,$ls_estclades))
		  {
				$ls_codestpro1  = $ls_codestpro1_min;
				$ls_codestpro2  = $ls_codestpro2_min;
				$ls_codestpro3  = $ls_codestpro3_min;
				$ls_codestpro4  = $ls_codestpro4_min;
				$ls_codestpro5  = $ls_codestpro5_min;
		  }
		}
		else
		{
				$ls_codestpro1  = $ls_codestpro1_min;
				$ls_codestpro2  = $ls_codestpro2_min;
				$ls_codestpro3  = $ls_codestpro3_min;
				$ls_codestpro4  = $ls_codestpro4_min;
				$ls_codestpro5  = $ls_codestpro5_min;
		}
		if(($ls_codestpro1h_max=="")&&($ls_codestpro2h_max=="")&&($ls_codestpro3h_max==""))
		{
		  if($io_function_report->uf_spg_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
																		 $ls_codestpro3h_max,$ls_codestpro4h_max,
																		 $ls_codestpro4h_max,$ls_estclahas))
		  {
				$ls_codestpro1h  = $ls_codestpro1h_max;
				$ls_codestpro2h  = $ls_codestpro2h_max;
				$ls_codestpro3h  = $ls_codestpro3h_max;
				$ls_codestpro4h  = $ls_codestpro4h_max;
				$ls_codestpro5h  = $ls_codestpro5h_max;
		  }
		}
		else
		{
				$ls_codestpro1h  = $ls_codestpro1h_max;
				$ls_codestpro2h  = $ls_codestpro2h_max;
				$ls_codestpro3h  = $ls_codestpro3h_max;
				$ls_codestpro4h  = $ls_codestpro4h_max;
				$ls_codestpro5h  = $ls_codestpro5h_max;
		}
	}
	 elseif($li_estmodest==2)
	 {   
		
		$ls_codestpro1_min = $ls_codestpro1;
		$ls_codestpro2_min = $ls_codestpro2;
		$ls_codestpro3_min = $ls_codestpro3;
		$ls_codestpro1h_max = $ls_codestpro1h;
		$ls_codestpro2h_max = $ls_codestpro2h;
		$ls_codestpro3h_max = $ls_codestpro3h;
		$ls_codestpro4_min = $_GET["codestpro4"];
		$ls_codestpro5_min = $_GET["codestpro5"];
		$ls_codestpro4h_max = $_GET["codestpro4h"];
		$ls_codestpro5h_max = $_GET["codestpro5h"];
		
		
		if(($ls_codestpro1_min=='**') ||($ls_codestpro1_min==''))
		{
			$ls_codestpro1_min='';
		}
		else
		{
			$ls_codestpro1_min  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
		}
		if(($ls_codestpro2_min=='**') ||($ls_codestpro2_min==''))
		{
			$ls_codestpro2_min='';
		}
		else
		{
			$ls_codestpro2_min  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
		
		}
		if(($ls_codestpro3_min=='**')||($ls_codestpro3_min==''))
		{
			$ls_codestpro3_min='';
		}
		else
		{
		
			$ls_codestpro3_min  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
		}
		if(($ls_codestpro4_min=='**') ||($ls_codestpro4_min==''))
		{
			$ls_codestpro4_min='';
		}
		else
		{
			$ls_codestpro4_min  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);

		
		}
		if(($ls_codestpro5_min=='**') ||($ls_codestpro5_min==''))
		{
			$ls_codestpro5_min='';
		}else
		{
				$ls_codestpro5_min  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
		}
		
		
		if(($ls_codestpro1h_max=='**')||($ls_codestpro1h_max==''))
		{
			$ls_codestpro1h_max='';
		}
		else
		{
			$ls_codestpro1h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
		}
		if(($ls_codestpro2h_max=='**') ||($ls_codestpro2h_max==''))
		{
			$ls_codestpro2h_max='';
		}else
		{
			$ls_codestpro2h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
		}
		if(($ls_codestpro3h_max=='**') ||($ls_codestpro3h_max==''))
		{
			$ls_codestpro3h_max='';
		}else
		{
			$ls_codestpro3h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
		}
		if(($ls_codestpro4h_max=='**')  ||($ls_codestpro4h_max==''))
		{
			$ls_codestpro4h_max='';
		}else
		{
			$ls_codestpro4h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
		}
		if(($ls_codestpro5h_max=='**')  || ($ls_codestpro5h_max==''))
		{
			$ls_codestpro5h_max='';
		}else
		{
			$ls_codestpro5h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
		}
		
		if(($ls_codestpro1_min=="")||($ls_codestpro2_min=="")||($ls_codestpro3_min=="")||($ls_codestpro4_min=="")||($ls_codestpro5_min==""))
		{
		  if($io_function_report->uf_spg_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,$ls_codestpro3_min,
																		 $ls_codestpro4_min,$ls_codestpro5_min,$ls_estclades))
		  {
				$ls_codestpro1  = $ls_codestpro1_min;
				$ls_codestpro2  = $ls_codestpro2_min;
				$ls_codestpro3  = $ls_codestpro3_min;
				$ls_codestpro4  = $ls_codestpro4_min;
				$ls_codestpro5  = $ls_codestpro5_min;
		  }
		}
		else
		{
				$ls_codestpro1  = $ls_codestpro1_min;
				$ls_codestpro2  = $ls_codestpro2_min;
				$ls_codestpro3  = $ls_codestpro3_min;
				$ls_codestpro4  = $ls_codestpro4_min;
				$ls_codestpro5  = $ls_codestpro5_min;
		}
		if(($ls_codestpro1h_max=="")||($ls_codestpro2h_max=="")||($ls_codestpro3h_max=="")||($ls_codestpro4h_max=="")||($ls_codestpro5h_max==""))
		{
		  if($io_function_report->uf_spg_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
																		 $ls_codestpro3h_max,$ls_codestpro4h_max,
																		 $ls_codestpro5h_max,$ls_estclahas))
		  {
			$ls_codestpro1h  = $ls_codestpro1h_max;
			$ls_codestpro2h  = $ls_codestpro2h_max;
			$ls_codestpro3h  = $ls_codestpro3h_max;
			$ls_codestpro4h  = $ls_codestpro4h_max;
			$ls_codestpro5h  = $ls_codestpro5h_max;
		  }
		}
		else
		{
			$ls_codestpro1h  = $ls_codestpro1h_max;
			$ls_codestpro2h  = $ls_codestpro2h_max;
			$ls_codestpro3h  = $ls_codestpro3h_max;
			$ls_codestpro4h  = $ls_codestpro4h_max;
			$ls_codestpro5h  = $ls_codestpro5h_max;
		}
		}
	
	$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1,25);
	$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2,25);
	$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3,25);
	$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4,25);
	$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5,25);

	$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h,25);
	$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h,25);
	$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h,25);
	$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h,25);
	$ls_codestpro5h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h,25);

    $lb_valido=$io_report->uf_spg_reportes_ejecucion_financiera_presupuesto_causado($ls_codestpro1,$ls_codestpro2,
																					$ls_codestpro3,$ls_codestpro4,
																					$ls_codestpro5,$ls_estclades,
																					$ls_codestpro1h,$ls_codestpro2h,
																					$ls_codestpro3h,$ls_codestpro4h,
																					$ls_codestpro5h,$ls_estclahas,
																					$ld_per01,$ld_per02,$ld_per03,
																					$ls_cuentades, $ls_cuentahas,$li_consolidado);

	 if($lb_valido==false) // Existe algún error ó no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');");
		print(" close();");
		print("</script>");
	 }
	 else // Se Transfiere la data a otro arreglo para incluir subtotales por partida
	 {
	    $lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,30);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,13);
		$lo_hoja->set_column(5,7,30);
		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		//$lo_hoja->write(1, 3, $ldt_fecha,$lo_encabezado);
		$li_fila=2;
	    $li_tot=$io_report->dts_reporte->getRowCount("spg_cuenta");
	  if($li_consolidado == 1)
	  {
		$ld_total_disponible		= 0;
		$ld_total_comprometido 		= 0;
		$ld_total_ajuste 			= 0;
		$ld_total_modificaciones 	= 0;
		$ld_total_precompromiso		= 0;
		$ld_total_libPrecompromiso	= 0;
		$ld_total_per1				= 0;
		$ld_total_per2				= 0;
		$ld_total_per3				= 0;
		
		$ld_totalp_disponible		= 0;
		$ld_totalp_disponible_ant   = 0;
		$ld_totalp_comprometido 	= 0;
		$ld_totalp_ajuste 			= 0;
		$ld_totalp_modificaciones 	= 0;
		$ld_totalp_precompromiso	= 0;
		$ld_totalp_libPrecompromiso	= 0;
		$ld_totalp_per1				= 0;
		$ld_totalp_per2				= 0;
		$ld_totalp_per3				= 0;
		$ls_denestpro1="";
		$ls_denestpro2="";
		$ls_denestpro3="";
		$ls_denestpro4="";
		$ls_denestpro5="";
		$ls_denestpro1h="";
		$ls_denestpro2h="";
		$ls_denestpro3h="";
		$ls_denestpro4h="";
		$ls_denestpro5h="";
		$lb_valido=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estclades);
		if($lb_valido)
		{
		  $ls_denestpro1=trim($ls_denestpro1);
		}
		if($lb_valido)
		{
		  $lb_valido=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estclades);
		  $ls_denestpro2=trim($ls_denestpro2);
		}
		if($lb_valido)
		{
		  $lb_valido=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estclades);
		  $ls_denestpro3=trim($ls_denestpro3);
		}
		if($li_estmodest==2)
		{
			if($lb_valido)
			{
			  $lb_valido=$io_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estclades);
			  $ls_denestpro4=trim($ls_denestpro4);
			}
			if($lb_valido)
			{
			  $lb_valido=$io_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estclades);
			  $ls_denestpro5=trim($ls_denestpro5);
			}
		}
		
		$lb_valido=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1h,$ls_denestpro1h,$ls_estclahas);
		if($lb_valido)
		{
		  $ls_denestpro1h=trim($ls_denestpro1h);
		}
		if($lb_valido)
		{
		  $lb_valido=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1h,$ls_codestpro2h,$ls_denestpro2h,$ls_estclahas);
		  $ls_denestpro2h=trim($ls_denestpro2h);
		}
		if($lb_valido)
		{
		  $lb_valido=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_denestpro3h,$ls_estclahas);
		  $ls_denestpro3h=trim($ls_denestpro3h);
		}
		if($li_estmodest==2)
		{
			if($lb_valido)
			{
			  $lb_valido=$io_report->uf_spg_reporte_select_denestpro4($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$ls_denestpro4h,$ls_estclahas);
			  $ls_denestpro4h=trim($ls_denestpro4h);
			}
			if($lb_valido)
			{
			  $lb_valido=$io_report->uf_spg_reporte_select_denestpro5($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$ls_codestpro5h,$ls_denestpro5h,$ls_estclahas);
			  $ls_denestpro5h=trim($ls_denestpro5h);
			}
		}
				
		uf_print_cabecera_consolidado($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
									  $ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,
									  $ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$ls_codestpro5h,
									  $ls_denestpro1h,$ls_denestpro2h,$ls_denestpro3h,$ls_denestpro4h,$ls_denestpro5h,
									  $ls_desper,$ls_lapso_meses,$ls_text_periodo,&$li_fila,$lo_titulo,$lo_hoja,$lo_libro,$li_consolidado);
									  
		$io_function_report->uf_get_nom_mes($ld_per01,$as_nomper01);
		$io_function_report->uf_get_nom_mes($ld_per02,$as_nomper02);
		$io_function_report->uf_get_nom_mes($ld_per03,$as_nomper03);
		uf_print_cabecera_detalle($lo_libro,$lo_hoja,$io_encabezado,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,$ld_fecfinrep,&$li_fila,$li_consolidado);
		$la_data_cta[] = array();
		$la_data_tot_cta[] = array();
		$la_data_tot[] = array();
		for($z=1;$z<=$li_tot;$z++)
		{
		 		$li_tmp						= ($z+1);
				$ls_spg_cuenta				= trim($io_report->dts_reporte->data["spg_cuenta"][$z]);
				$io_function_report->uf_get_spg_cuenta($ls_spg_cuenta,$ls_partida,$ls_generica,$ls_especifica,$ls_subesp);
				$ls_status                 	= $io_report->dts_reporte->data["status"][$z];
				$ls_denominacion        	= trim($io_report->dts_reporte->data["denominacion"][$z]);
				if ($ld_per01==1)
				{
					$ld_dispact              	= $io_report->dts_reporte->data["asignado"][$z];
				}
				else
				{
					$ld_dispact              	= $io_report->dts_reporte->data["dispant"][$z];
				}
				$ld_disant              	= $io_report->dts_reporte->data["dispant"][$z];
				$ld_periodo01         		= $io_report->dts_reporte->data["periodo01"][$z];
				$ld_periodo02        		= $io_report->dts_reporte->data["periodo02"][$z];
				$ld_periodo03       		= $io_report->dts_reporte->data["periodo03"][$z];
				$ld_modpres              	= $io_report->dts_reporte->data["modpres"][$z];
				$ld_comprometido     	    = $io_report->dts_reporte->data["comprometido"][$z];
				$ld_libprecomprometido  	= $io_report->dts_reporte->data["libprecomprometido"][$z];
				$ld_libcomprometido     	= $io_report->dts_reporte->data["libcomprometido"][$z];
				$ld_causado        			= $ld_periodo01 + $ld_periodo02 + $ld_periodo03;
				$ld_disponible          	= $ld_disant + $ld_modpres - $ld_comprometido;
				if ($z<$li_tot)
			    {
					$ls_partida_next=substr($io_report->dts_reporte->data["spg_cuenta"][$li_tmp],0,3);
			    }
			    elseif($z==$li_tot)
			    {
					$ls_partida_next='no_next';
			    }

				switch($ld_tipper)
		 		{
		  			case 1:
				         $la_data_cta[0]=array('programatica'=>$ls_programatica,
											   'dispact'=>$ld_dispact,
											   'dispant'=>$ld_disant,
											   'precomprometido'=>$ld_comprometido,
											   'libprecomprometido'=>$ld_libprecomprometido,
											   'libcomprometido'=>$ld_libcomprometido,
											   'status'=>$ls_status,
											   'estcla'=>$ls_estcla,
											   'cuenta'=>$ls_spg_cuenta,
										       'denominacion'=>$ls_denominacion,
										       'disponact'=>$ld_dispact,
										       'periodo01'=>$ld_periodo01,
										       'periodo02'=>$ld_periodo02,
										       'periodo03'=>$ld_periodo03,
										       'totcom'=>$ld_causado,
										       'ajucom'=>$ld_libcomprometido,
										       'modpres'=>$ld_modpres,
										       'precom'=>$ld_comprometido,
										       'libprecom'=>$ld_libprecomprometido,
										       'disponible'=>$ld_disponible);
		         	break;

		  			case 2:
		         		$la_data_cta[0]=array('programatica'=>$ls_programatica,
		         							  'dispact'=>$ld_dispact,
											  'dispant'=>$ld_disant,
											  'precomprometido'=>$ld_comprometido,
											  'libprecomprometido'=>$ld_libprecomprometido,
											  'libcomprometido'=>$ld_libcomprometido,
											  'status'=>$ls_status,
											  'estcla'=>$ls_estcla,
											  'cuenta'=>$ls_spg_cuenta,
										      'denominacion'=>$ls_denominacion,
										      'disponact'=>$ld_dispact,
										      'periodo01'=>$ld_periodo01,
										      'periodo02'=>$ld_periodo02,
										      'periodo03'=>$ld_periodo03,
										      'totcom'=>$ld_causado,
										      'ajucom'=>$ld_libcomprometido,
										      'modpres'=>$ld_modpres,
										      'precom'=>$ld_comprometido,
										      'libprecom'=>$ld_libprecomprometido,
										      'disponible'=>$ld_disponible);
		         	break;

		  			case 3:
		         		$la_data_cta[0]=array('programatica'=>$ls_programatica,
		         							  'dispact'=>$ld_dispact,
											  'dispant'=>$ld_disant,
											  'precomprometido'=>$ld_comprometido,
											  'libprecomprometido'=>$ld_libprecomprometido,
											  'libcomprometido'=>$ld_libcomprometido,
											  'status'=>$ls_status,
											  'estcla'=>$ls_estcla,
											  'cuenta'=>$ls_spg_cuenta,
										      'denominacion'=>$ls_denominacion,
										      'disponact'=>$ld_dispact,
										      'periodo01'=>$ld_periodo01,
										      'periodo02'=>$ld_periodo02,
										      'periodo03'=>$ld_periodo03,
										      'totcom'=>$ld_causado,
										      'ajucom'=>$ld_libcomprometido,
										      'modpres'=>$ld_modpres,
										      'precom'=>$ld_comprometido,
										      'libprecom'=>$ld_libprecomprometido,
										      'disponible'=>$ld_disponible);
		         	break;
				}// switch

				if($ls_status=="C")
				{
					$ld_totalp_disponible		= $ld_totalp_disponible + $ld_disponible;
					$ld_totalp_disponible_ant   = $ld_totalp_disponible_ant + $ld_disant;
					$ld_totalp_comprometido 	= $ld_totalp_comprometido + $ld_causado;
					$ld_totalp_ajuste 			= $ld_totalp_ajuste + $ld_libcomprometido;
					$ld_totalp_modificaciones 	= $ld_totalp_modificaciones + $ld_modpres;
					$ld_totalp_precompromiso	= $ld_totalp_precompromiso + $ld_comprometido;
					$ld_totalp_libPrecompromiso	= $ld_totalp_libPrecompromiso + $ld_libprecomprometido;
					$ld_totalp_per1				= $ld_totalp_per1 + $ld_periodo01;
					$ld_totalp_per2				= $ld_totalp_per2 + $ld_periodo02;
					$ld_totalp_per3				= $ld_totalp_per3 + $ld_periodo03;
					
					$ld_total_disponible		+= $ld_disponible;
					$ld_total_disponible_ant    += $ld_disant;
					$ld_total_comprometido 		+= $ld_causado;
					$ld_total_ajuste 			+= $ld_libcomprometido;
					$ld_total_modificaciones 	+= $ld_modpres;
					$ld_total_precompromiso		+= $ld_comprometido;
					$ld_total_libPrecompromiso	+= $ld_libprecomprometido;
					$ld_total_per1				+= $ld_periodo01;
					$ld_total_per2				+= $ld_periodo02;
					$ld_total_per3				+= $ld_periodo03;
				}

				if ($ls_partida!=$ls_partida_next)
				{
					uf_print_detalle($lo_libro,$lo_hoja,$la_data_cta,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
					switch($ld_tipper)
			 		{
			  			case 1:
					         $la_data_tot_cta[0]=array('programatica'=>$ls_programatica,
			         							        'dispact'=>$ld_dispact,
												        'dispant'=>$ld_disant,
												        'precomprometido'=>$ld_comprometido,
												        'libprecomprometido'=>$ld_libprecomprometido,
												        'libcomprometido'=>$ld_libcomprometido,
												        'status'=>$ls_status,
												        'estcla'=>$ls_estcla,
												        'cuenta'=>'Total Partida '.$ls_partida,
											            'denominacion'=>'',
											            'disponact'=>$ld_totalp_disponible_ant,
											            'periodo01'=>$ld_totalp_per1,
											            'periodo02'=>$ld_totalp_per2,
											            'periodo03'=>$ld_totalp_per3,
											            'totcom'=>$ld_totalp_comprometido,
											            'ajucom'=>$ld_totalp_ajuste,
											            'modpres'=>$ld_totalp_modificaciones,
											            'precom'=>$ld_totalp_precompromiso,
											            'libprecom'=>$ld_totalp_libPrecompromiso,
											            'disponible'=>$ld_totalp_disponible);
			         	break;

			  			case 2:
			         		$la_data_tot_cta[0]=array('programatica'=>$ls_programatica,
			         							       'dispact'=>$ld_dispact,
												       'dispant'=>$ld_disant,
												       'precomprometido'=>$ld_comprometido,
												       'libprecomprometido'=>$ld_libprecomprometido,
												       'libcomprometido'=>$ld_libcomprometido,
												       'status'=>$ls_status,
												       'estcla'=>$ls_estcla,
												       'cuenta'=>'Total Partida '.$ls_partida,
											           'denominacion'=>'',
											           'disponact'=>$ld_totalp_disponible_ant,
											           'periodo01'=>$ld_totalp_per1,
											           'periodo02'=>$ld_totalp_per2,
											           'periodo03'=>$ld_totalp_per3,
											           'totcom'=>$ld_totalp_comprometido,
											           'ajucom'=>$ld_totalp_ajuste,
											           'modpres'=>$ld_totalp_modificaciones,
											           'precom'=>$ld_totalp_precompromiso,
											           'libprecom'=>$ld_totalp_libPrecompromiso,
											           'disponible'=>$ld_totalp_disponible);
			         	break;

			  			case 3:
			         		$la_data_tot_cta[0]=array('programatica'=>$ls_programatica,
			         							       'dispact'=>$ld_dispact,
												       'dispant'=>$ld_disant,
												       'precomprometido'=>$ld_comprometido,
												       'libprecomprometido'=>$ld_libprecomprometido,
												       'libcomprometido'=>$ld_libcomprometido,
												       'status'=>$ls_status,
												       'estcla'=>$ls_estcla,
												       'cuenta'=>'Total Partida '.$ls_partida,
											           'denominacion'=>'',
											           'disponact'=>$ld_totalp_disponible_ant,
											           'periodo01'=>$ld_totalp_per1,
											           'periodo02'=>$ld_totalp_per2,
											           'periodo03'=>$ld_totalp_per3,
											           'totcom'=>$ld_totalp_comprometido,
											           'ajucom'=>$ld_totalp_ajuste,
											           'modpres'=>$ld_totalp_modificaciones,
											           'precom'=>$ld_totalp_precompromiso,
											           'libprecom'=>$ld_totalp_libPrecompromiso,
											           'disponible'=>$ld_totalp_disponible);

			         	break;
					}
					$ld_totalp_disponible		= 0;
					$ld_totalp_disponible_ant   = 0;
					$ld_totalp_comprometido 	= 0;
					$ld_totalp_ajuste 			= 0;
					$ld_totalp_modificaciones 	= 0;
					$ld_totalp_precompromiso	= 0;
					$ld_totalp_libPrecompromiso	= 0;
					$ld_totalp_per1				= 0;
					$ld_totalp_per2				= 0;
					$ld_totalp_per3				= 0;
					uf_print_detalle_bold($lo_libro,$lo_hoja,$la_data_tot_cta,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
			}
			else
			{
			 uf_print_detalle($lo_libro,$lo_hoja,$la_data_cta,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
			}
		
		} // End for
		switch($ld_tipper)
	    {
			  			case 1:
					         $la_data_tot[0]=array('programatica'=>$ls_programatica,
			         							        'dispact'=>$ld_dispact,
												        'dispant'=>$ld_disant,
												        'precomprometido'=>$ld_comprometido,
												        'libprecomprometido'=>$ld_libprecomprometido,
												        'libcomprometido'=>$ld_libcomprometido,
												        'status'=>$ls_status,
												        'estcla'=>$ls_estcla,
												        'cuenta'=>'TOTAL',
											            'denominacion'=>'',
											            'disponact'=>$ld_total_disponible_ant,
											            'periodo01'=>$ld_total_per1,
											            'periodo02'=>$ld_total_per2,
											            'periodo03'=>$ld_total_per3,
											            'totcom'=>$ld_total_comprometido,
											            'ajucom'=>$ld_total_ajuste,
											            'modpres'=>$ld_total_modificaciones,
											            'precom'=>$ld_total_precompromiso,
											            'libprecom'=>$ld_total_libPrecompromiso,
											            'disponible'=>$ld_total_disponible);
			         	break;

			  			case 2:
			         		$la_data_tot[0]=array('programatica'=>$ls_programatica,
			         							       'dispact'=>$ld_dispact,
												       'dispant'=>$ld_disant,
												       'precomprometido'=>$ld_comprometido,
												       'libprecomprometido'=>$ld_libprecomprometido,
												       'libcomprometido'=>$ld_libcomprometido,
												       'status'=>$ls_status,
												       'estcla'=>$ls_estcla,
												       'cuenta'=>'TOTAL',
											           'denominacion'=>'',
											           'disponact'=>$ld_total_disponible_ant,
											           'periodo01'=>$ld_total_per1,
											           'periodo02'=>$ld_total_per2,
											           'periodo03'=>$ld_total_per3,
											           'totcom'=>$ld_total_comprometido,
											           'ajucom'=>$ld_total_ajuste,
											           'modpres'=>$ld_total_modificaciones,
											           'precom'=>$ld_total_precompromiso,
											           'libprecom'=>$ld_total_libPrecompromiso,
											           'disponible'=>$ld_total_disponible);
			         	break;

			  			case 3:
			         		$la_data_tot[0]=array('programatica'=>$ls_programatica,
			         							       'dispact'=>$ld_dispact,
												       'dispant'=>$ld_disant,
												       'precomprometido'=>$ld_comprometido,
												       'libprecomprometido'=>$ld_libprecomprometido,
												       'libcomprometido'=>$ld_libcomprometido,
												       'status'=>$ls_status,
												       'estcla'=>$ls_estcla,
												       'cuenta'=>'TOTAL',
											           'denominacion'=>'',
											           'disponact'=>$ld_total_disponible_ant,
											           'periodo01'=>$ld_total_per1,
											           'periodo02'=>$ld_total_per2,
											           'periodo03'=>$ld_total_per3,
											           'totcom'=>$ld_total_comprometido,
											           'ajucom'=>$ld_total_ajuste,
											           'modpres'=>$ld_total_modificaciones,
											           'precom'=>$ld_total_precompromiso,
											           'libprecom'=>$ld_total_libPrecompromiso,
											           'disponible'=>$ld_total_disponible);

			         	break;
		}
		$li_fila++;
		uf_print_detalle_bold($lo_libro,$lo_hoja,$la_data_tot,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
	  }
	  elseif($li_consolidado == 0)
	  {
		//$io_report->dts_reporte->group_noorder("programatica");
		$la_estructuras = array();
		$ld_total_disponible		= 0;
		$ld_total_disponible_ant	= 0;
		$ld_total_comprometido 		= 0;
		$ld_total_ajuste 			= 0;
		$ld_total_modificaciones 	= 0;
		$ld_total_precompromiso		= 0;
		$ld_total_libPrecompromiso	= 0;
		$ld_total_per1				= 0;
		$ld_total_per2				= 0;
		$ld_total_per3				= 0;
		
		$ld_totalp_disponible		= 0;
		$ld_totalp_disponible_ant   = 0;
		$ld_totalp_comprometido 	= 0;
		$ld_totalp_ajuste 			= 0;
		$ld_totalp_modificaciones 	= 0;
		$ld_totalp_precompromiso	= 0;
		$ld_totalp_libPrecompromiso	= 0;
		$ld_totalp_per1				= 0;
		$ld_totalp_per2				= 0;
		$ld_totalp_per3				= 0;
		for($z=1;$z<=$li_tot;$z++)
		{
		 		$li_tmp						= ($z+1);
				$ls_programatica			= trim($io_report->dts_reporte->data["programatica"][$z]);
				$ok = array_search($ls_programatica,$la_estructuras);
				if(is_bool($ok))
				{
				    $li_pos = count($la_estructuras);
				    $la_estructuras[$li_pos] = $ls_programatica;
				    $ls_codestpro1 = substr($ls_programatica,0,25);
					$ls_codestpro2 = substr($ls_programatica,25,25);
					$ls_codestpro3 = substr($ls_programatica,50,25);
					$ls_codestpro4 = substr($ls_programatica,75,25);
					$ls_codestpro5 = substr($ls_programatica,100,25);
					$ls_estcla     = substr($ls_programatica,125,1);
					$ls_denestpro1="";
					$ls_denestpro2="";
					$ls_denestpro3="";
					$ls_denestpro4="";
					$ls_denestpro5="";
					if ($z<$li_tot)
					{
						$ls_partida_next=substr($io_report->dts_reporte->data["spg_cuenta"][$li_tmp],0,3);
						$ls_programatica_next=$io_report->dts_reporte->data["programatica"][$li_tmp];
					}
					elseif($z==$li_tot)
					{
						$ls_partida_next='no_next';
						$ls_programatica_next='no_next';
					}
					
					$ld_totalp_disponible		= 0;
					$ld_totalp_disponible_ant   = 0;
					$ld_totalp_comprometido 	= 0;
					$ld_totalp_ajuste 			= 0;
					$ld_totalp_modificaciones 	= 0;
					$ld_totalp_precompromiso	= 0;
					$ld_totalp_libPrecompromiso	= 0;
					$ld_totalp_per1				= 0;
					$ld_totalp_per2				= 0;
					$ld_totalp_per3				= 0;
					$lb_valido=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
					if($lb_valido)
					{
					  $ls_denestpro1=trim($ls_denestpro1);
					}
					if($lb_valido)
					{
					  $lb_valido=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
					  $ls_denestpro2=trim($ls_denestpro2);
					}
					if($lb_valido)
					{
					  $lb_valido=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
					  $ls_denestpro3=trim($ls_denestpro3);
					}
					if($li_estmodest==2)
					{
						if($lb_valido)
						{
						  $lb_valido=$io_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
						  $ls_denestpro4=trim($ls_denestpro4);
						}
						if($lb_valido)
						{
						  $lb_valido=$io_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
						  $ls_denestpro5=trim($ls_denestpro5);
						}
				    }
				    uf_print_cabecera_detallado($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
											$ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,
											$ls_desper,$ls_lapso_meses,$ls_text_periodo,&$li_fila,$lo_titulo,$lo_hoja,$lo_libro,$li_consolidado);
											
				    uf_print_cabecera_detalle($lo_libro,$lo_hoja,$io_encabezado,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,$ld_fecfinrep,&$li_fila,$li_consolidado); 
					$ls_spg_cuenta				= trim($io_report->dts_reporte->data["spg_cuenta"][$z]);
					$io_function_report->uf_get_spg_cuenta($ls_spg_cuenta,$ls_partida,$ls_generica,$ls_especifica,$ls_subesp);
					$ls_status                 	= $io_report->dts_reporte->data["status"][$z];
					$ls_denominacion        	= trim($io_report->dts_reporte->data["denominacion"][$z]);
					if ($ld_per01==1)
					{
						$ld_dispact              	= $io_report->dts_reporte->data["asignado"][$z];
					}
					else
					{
						$ld_dispact              	= $io_report->dts_reporte->data["dispant"][$z];
					}
					$ld_disant              	= $io_report->dts_reporte->data["dispant"][$z];
					$ld_periodo01         		= $io_report->dts_reporte->data["periodo01"][$z];
					$ld_periodo02        		= $io_report->dts_reporte->data["periodo02"][$z];
					$ld_periodo03       		= $io_report->dts_reporte->data["periodo03"][$z];
					$ld_modpres              	= $io_report->dts_reporte->data["modpres"][$z];
					$ld_comprometido     	    = $io_report->dts_reporte->data["comprometido"][$z];
					$ld_libprecomprometido  	= $io_report->dts_reporte->data["libprecomprometido"][$z];
					$ld_libcomprometido     	= $io_report->dts_reporte->data["libcomprometido"][$z];
					$ld_causado        			= $ld_periodo01 + $ld_periodo02 + $ld_periodo03;
					$ld_disponible          	= $ld_disant + $ld_modpres - $ld_comprometido;
					
					switch($ld_tipper)
					{
						case 1:
							 $la_data_cta[0]=array('programatica'=>$ls_programatica,
												   'dispact'=>$ld_dispact,
												   'dispant'=>$ld_disant,
												   'precomprometido'=>$ld_comprometido,
												   'libprecomprometido'=>$ld_libprecomprometido,
												   'libcomprometido'=>$ld_libcomprometido,
												   'status'=>$ls_status,
												   'estcla'=>$ls_estcla,
												   'cuenta'=>$ls_spg_cuenta,
												   'denominacion'=>$ls_denominacion,
												   'disponact'=>$ld_dispact,
												   'periodo01'=>$ld_periodo01,
												   'periodo02'=>$ld_periodo02,
												   'periodo03'=>$ld_periodo03,
												   'totcom'=>$ld_causado,
												   'ajucom'=>$ld_libcomprometido,
												   'modpres'=>$ld_modpres,
												   'precom'=>$ld_comprometido,
												   'libprecom'=>$ld_libprecomprometido,
												   'disponible'=>$ld_disponible);
						break;
	
						case 2:
							$la_data_cta[0]=array('programatica'=>$ls_programatica,
												  'dispact'=>$ld_dispact,
												  'dispant'=>$ld_disant,
												  'precomprometido'=>$ld_comprometido,
												  'libprecomprometido'=>$ld_libprecomprometido,
												  'libcomprometido'=>$ld_libcomprometido,
												  'status'=>$ls_status,
												  'estcla'=>$ls_estcla,
												  'cuenta'=>$ls_spg_cuenta,
												  'denominacion'=>$ls_denominacion,
												  'disponact'=>$ld_dispact,
												  'periodo01'=>$ld_periodo01,
												  'periodo02'=>$ld_periodo02,
												  'periodo03'=>$ld_periodo03,
												  'totcom'=>$ld_causado,
												  'ajucom'=>$ld_libcomprometido,
												  'modpres'=>$ld_modpres,
												  'precom'=>$ld_comprometido,
												  'libprecom'=>$ld_libprecomprometido,
												  'disponible'=>$ld_disponible);
						break;
	
						case 3:
							$la_data_cta[0]=array('programatica'=>$ls_programatica,
												  'dispact'=>$ld_dispact,
												  'dispant'=>$ld_disant,
												  'precomprometido'=>$ld_comprometido,
												  'libprecomprometido'=>$ld_libprecomprometido,
												  'libcomprometido'=>$ld_libcomprometido,
												  'status'=>$ls_status,
												  'estcla'=>$ls_estcla,
												  'cuenta'=>$ls_spg_cuenta,
												  'denominacion'=>$ls_denominacion,
												  'disponact'=>$ld_dispact,
												  'periodo01'=>$ld_periodo01,
												  'periodo02'=>$ld_periodo02,
												  'periodo03'=>$ld_periodo03,
												  'totcom'=>$ld_causado,
												  'ajucom'=>$ld_libcomprometido,
												  'modpres'=>$ld_modpres,
												  'precom'=>$ld_comprometido,
												  'libprecom'=>$ld_libprecomprometido,
												  'disponible'=>$ld_disponible);
						break;
					}// switch
	
					if($ls_status=="C")
					{
						$ld_totalp_disponible		= $ld_totalp_disponible + $ld_disponible;
						$ld_totalp_disponible_ant   = $ld_totalp_disponible_ant + $ld_disant;
						$ld_totalp_comprometido 	= $ld_totalp_comprometido + $ld_causado;
						$ld_totalp_ajuste 			= $ld_totalp_ajuste + $ld_libcomprometido;
						$ld_totalp_modificaciones 	= $ld_totalp_modificaciones + $ld_modpres;
						$ld_totalp_precompromiso	= $ld_totalp_precompromiso + $ld_comprometido;
						$ld_totalp_libPrecompromiso	= $ld_totalp_libPrecompromiso + $ld_libprecomprometido;
						$ld_totalp_per1				= $ld_totalp_per1 + $ld_periodo01;
						$ld_totalp_per2				= $ld_totalp_per2 + $ld_periodo02;
						$ld_totalp_per3				= $ld_totalp_per3 + $ld_periodo03;
						$ld_total_disponible		+= $ld_disponible;
						$ld_total_disponible_ant    += $ld_disant;
						$ld_total_comprometido 		+= $ld_causado;
						$ld_total_ajuste 			+= $ld_libcomprometido;
						$ld_total_modificaciones 	+= $ld_modpres;
						$ld_total_precompromiso		+= $ld_comprometido;
						$ld_total_libPrecompromiso	+= $ld_libprecomprometido;
						$ld_total_per1				+= $ld_periodo01;
						$ld_total_per2				+= $ld_periodo02;
						$ld_total_per3				+= $ld_periodo03;
					}
	
					if ($ls_partida!=$ls_partida_next)
					{
						uf_print_detalle($lo_libro,$lo_hoja,$la_data_cta,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
						switch($ld_tipper)
						{
							case 1:
								 $la_data_tot_cta[0]=array('programatica'=>$ls_programatica,
															'dispact'=>$ld_dispact,
															'dispant'=>$ld_disant,
															'precomprometido'=>$ld_comprometido,
															'libprecomprometido'=>$ld_libprecomprometido,
															'libcomprometido'=>$ld_libcomprometido,
															'status'=>$ls_status,
															'estcla'=>$ls_estcla,
															'cuenta'=>'Total Partida '.$ls_partida,
															'denominacion'=>'',
															'disponact'=>$ld_totalp_disponible_ant,
															'periodo01'=>$ld_totalp_per1,
															'periodo02'=>$ld_totalp_per2,
															'periodo03'=>$ld_totalp_per3,
															'totcom'=>$ld_totalp_comprometido,
															'ajucom'=>$ld_totalp_ajuste,
															'modpres'=>$ld_totalp_modificaciones,
															'precom'=>$ld_totalp_precompromiso,
															'libprecom'=>$ld_totalp_libPrecompromiso,
															'disponible'=>$ld_totalp_disponible);
							break;
	
							case 2:
								$la_data_tot_cta[0]=array('programatica'=>$ls_programatica,
														   'dispact'=>$ld_dispact,
														   'dispant'=>$ld_disant,
														   'precomprometido'=>$ld_comprometido,
														   'libprecomprometido'=>$ld_libprecomprometido,
														   'libcomprometido'=>$ld_libcomprometido,
														   'status'=>$ls_status,
														   'estcla'=>$ls_estcla,
														   'cuenta'=>'Total Partida '.$ls_partida,
														   'denominacion'=>'',
														   'disponact'=>$ld_totalp_disponible_ant,
														   'periodo01'=>$ld_totalp_per1,
														   'periodo02'=>$ld_totalp_per2,
														   'periodo03'=>$ld_totalp_per3,
														   'totcom'=>$ld_totalp_comprometido,
														   'ajucom'=>$ld_totalp_ajuste,
														   'modpres'=>$ld_totalp_modificaciones,
														   'precom'=>$ld_totalp_precompromiso,
														   'libprecom'=>$ld_totalp_libPrecompromiso,
														   'disponible'=>$ld_totalp_disponible);
							break;
	
							case 3:
								$la_data_tot_cta[0]=array('programatica'=>$ls_programatica,
														   'dispact'=>$ld_dispact,
														   'dispant'=>$ld_disant,
														   'precomprometido'=>$ld_comprometido,
														   'libprecomprometido'=>$ld_libprecomprometido,
														   'libcomprometido'=>$ld_libcomprometido,
														   'status'=>$ls_status,
														   'estcla'=>$ls_estcla,
														   'cuenta'=>'Total Partida '.$ls_partida,
														   'denominacion'=>'',
														   'disponact'=>$ld_totalp_disponible_ant,
														   'periodo01'=>$ld_totalp_per1,
														   'periodo02'=>$ld_totalp_per2,
														   'periodo03'=>$ld_totalp_per3,
														   'totcom'=>$ld_totalp_comprometido,
														   'ajucom'=>$ld_totalp_ajuste,
														   'modpres'=>$ld_totalp_modificaciones,
														   'precom'=>$ld_totalp_precompromiso,
														   'libprecom'=>$ld_totalp_libPrecompromiso,
														   'disponible'=>$ld_totalp_disponible);
	
							break;
						}
						$ld_totalp_disponible		= 0;
						$ld_totalp_disponible_ant   = 0;
						$ld_totalp_comprometido 	= 0;
						$ld_totalp_ajuste 			= 0;
						$ld_totalp_modificaciones 	= 0;
						$ld_totalp_precompromiso	= 0;
						$ld_totalp_libPrecompromiso	= 0;
						$ld_totalp_per1				= 0;
						$ld_totalp_per2				= 0;
						$ld_totalp_per3				= 0;
						uf_print_detalle_bold($lo_libro,$lo_hoja,$la_data_tot_cta,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
				    }
					else
					{
					 uf_print_detalle($lo_libro,$lo_hoja,$la_data_cta,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
					}
				}
				else
				{
					
					$ls_spg_cuenta				= trim($io_report->dts_reporte->data["spg_cuenta"][$z]);
					$io_function_report->uf_get_spg_cuenta($ls_spg_cuenta,$ls_partida,$ls_generica,$ls_especifica,$ls_subesp);
					$ls_status                 	= $io_report->dts_reporte->data["status"][$z];
					$ls_denominacion        	= trim($io_report->dts_reporte->data["denominacion"][$z]);
					if ($ld_per01==1)
					{
						$ld_dispact              	= $io_report->dts_reporte->data["asignado"][$z];
					}
					else
					{
						$ld_dispact              	= $io_report->dts_reporte->data["dispant"][$z];
					}
					$ld_disant              	= $io_report->dts_reporte->data["dispant"][$z];
					$ld_periodo01         		= $io_report->dts_reporte->data["periodo01"][$z];
					$ld_periodo02        		= $io_report->dts_reporte->data["periodo02"][$z];
					$ld_periodo03       		= $io_report->dts_reporte->data["periodo03"][$z];
					$ld_modpres              	= $io_report->dts_reporte->data["modpres"][$z];
					$ld_comprometido     	    = $io_report->dts_reporte->data["comprometido"][$z];
					$ld_libprecomprometido  	= $io_report->dts_reporte->data["libprecomprometido"][$z];
					$ld_libcomprometido     	= $io_report->dts_reporte->data["libcomprometido"][$z];
					$ld_causado        			= $ld_periodo01 + $ld_periodo02 + $ld_periodo03;
					$ld_disponible          	= $ld_disant + $ld_modpres - $ld_comprometido;
					if ($z<$li_tot)
					{
						$ls_partida_next=substr($io_report->dts_reporte->data["spg_cuenta"][$li_tmp],0,3);
						$ls_programatica_next=$io_report->dts_reporte->data["programatica"][$li_tmp];
					}
					elseif($z==$li_tot)
					{
						$ls_partida_next='no_next';
						$ls_programatica_next='no_next';
					}
					
					switch($ld_tipper)
					{
						case 1:
							 $la_data_cta[0]=array('programatica'=>$ls_programatica,
												   'dispact'=>$ld_dispact,
												   'dispant'=>$ld_disant,
												   'precomprometido'=>$ld_comprometido,
												   'libprecomprometido'=>$ld_libprecomprometido,
												   'libcomprometido'=>$ld_libcomprometido,
												   'status'=>$ls_status,
												   'estcla'=>$ls_estcla,
												   'cuenta'=>$ls_spg_cuenta,
												   'denominacion'=>$ls_denominacion,
												   'disponact'=>$ld_dispact,
												   'periodo01'=>$ld_periodo01,
												   'periodo02'=>$ld_periodo02,
												   'periodo03'=>$ld_periodo03,
												   'totcom'=>$ld_causado,
												   'ajucom'=>$ld_libcomprometido,
												   'modpres'=>$ld_modpres,
												   'precom'=>$ld_comprometido,
												   'libprecom'=>$ld_libprecomprometido,
												   'disponible'=>$ld_disponible);
						break;
	
						case 2:
							$la_data_cta[0]=array('programatica'=>$ls_programatica,
												  'dispact'=>$ld_dispact,
												  'dispant'=>$ld_disant,
												  'precomprometido'=>$ld_comprometido,
												  'libprecomprometido'=>$ld_libprecomprometido,
												  'libcomprometido'=>$ld_libcomprometido,
												  'status'=>$ls_status,
												  'estcla'=>$ls_estcla,
												  'cuenta'=>$ls_spg_cuenta,
												  'denominacion'=>$ls_denominacion,
												  'disponact'=>$ld_dispact,
												  'periodo01'=>$ld_periodo01,
												  'periodo02'=>$ld_periodo02,
												  'periodo03'=>$ld_periodo03,
												  'totcom'=>$ld_causado,
												  'ajucom'=>$ld_libcomprometido,
												  'modpres'=>$ld_modpres,
												  'precom'=>$ld_comprometido,
												  'libprecom'=>$ld_libprecomprometido,
												  'disponible'=>$ld_disponible);
						break;
	
						case 3:
							$la_data_cta[0]=array('programatica'=>$ls_programatica,
												  'dispact'=>$ld_dispact,
												  'dispant'=>$ld_disant,
												  'precomprometido'=>$ld_comprometido,
												  'libprecomprometido'=>$ld_libprecomprometido,
												  'libcomprometido'=>$ld_libcomprometido,
												  'status'=>$ls_status,
												  'estcla'=>$ls_estcla,
												  'cuenta'=>$ls_spg_cuenta,
												  'denominacion'=>$ls_denominacion,
												  'disponact'=>$ld_dispact,
												  'periodo01'=>$ld_periodo01,
												  'periodo02'=>$ld_periodo02,
												  'periodo03'=>$ld_periodo03,
												  'totcom'=>$ld_causado,
												  'ajucom'=>$ld_libcomprometido,
												  'modpres'=>$ld_modpres,
												  'precom'=>$ld_comprometido,
												  'libprecom'=>$ld_libprecomprometido,
												  'disponible'=>$ld_disponible);
						break;
					}// switch
	
					if($ls_status=="C")
					{
						$ld_totalp_disponible		= $ld_totalp_disponible + $ld_disponible;
						$ld_totalp_disponible_ant   = $ld_totalp_disponible_ant + $ld_disant;
						$ld_totalp_comprometido 	= $ld_totalp_comprometido + $ld_causado;
						$ld_totalp_ajuste 			= $ld_totalp_ajuste + $ld_libcomprometido;
						$ld_totalp_modificaciones 	= $ld_totalp_modificaciones + $ld_modpres;
						$ld_totalp_precompromiso	= $ld_totalp_precompromiso + $ld_comprometido;
						$ld_totalp_libPrecompromiso	= $ld_totalp_libPrecompromiso + $ld_libprecomprometido;
						$ld_totalp_per1				= $ld_totalp_per1 + $ld_periodo01;
						$ld_totalp_per2				= $ld_totalp_per2 + $ld_periodo02;
						$ld_totalp_per3				= $ld_totalp_per3 + $ld_periodo03;
						
						$ld_total_disponible		+= $ld_disponible;
						$ld_total_disponible_ant    += $ld_disant;
						$ld_total_comprometido 		+= $ld_causado;
						$ld_total_ajuste 			+= $ld_libcomprometido;
						$ld_total_modificaciones 	+= $ld_modpres;
						$ld_total_precompromiso		+= $ld_comprometido;
						$ld_total_libPrecompromiso	+= $ld_libprecomprometido;
						$ld_total_per1				+= $ld_periodo01;
						$ld_total_per2				+= $ld_periodo02;
						$ld_total_per3				+= $ld_periodo03;
					}
	
					if ($ls_partida!=$ls_partida_next)
					{
						uf_print_detalle($lo_libro,$lo_hoja,$la_data_cta,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
						switch($ld_tipper)
						{
							case 1:
								 $la_data_tot_cta[0]=array('programatica'=>$ls_programatica,
															'dispact'=>$ld_dispact,
															'dispant'=>$ld_disant,
															'precomprometido'=>$ld_comprometido,
															'libprecomprometido'=>$ld_libprecomprometido,
															'libcomprometido'=>$ld_libcomprometido,
															'status'=>$ls_status,
															'estcla'=>$ls_estcla,
															'cuenta'=>'Total Partida '.$ls_partida,
															'denominacion'=>'',
															'disponact'=>$ld_totalp_disponible_ant,
															'periodo01'=>$ld_totalp_per1,
															'periodo02'=>$ld_totalp_per2,
															'periodo03'=>$ld_totalp_per3,
															'totcom'=>$ld_totalp_comprometido,
															'ajucom'=>$ld_totalp_ajuste,
															'modpres'=>$ld_totalp_modificaciones,
															'precom'=>$ld_totalp_precompromiso,
															'libprecom'=>$ld_totalp_libPrecompromiso,
															'disponible'=>$ld_totalp_disponible);
							break;
	
							case 2:
								$la_data_tot_cta[0]=array('programatica'=>$ls_programatica,
														   'dispact'=>$ld_dispact,
														   'dispant'=>$ld_disant,
														   'precomprometido'=>$ld_comprometido,
														   'libprecomprometido'=>$ld_libprecomprometido,
														   'libcomprometido'=>$ld_libcomprometido,
														   'status'=>$ls_status,
														   'estcla'=>$ls_estcla,
														   'cuenta'=>'Total Partida '.$ls_partida,
														   'denominacion'=>'',
														   'disponact'=>$ld_totalp_disponible_ant,
														   'periodo01'=>$ld_totalp_per1,
														   'periodo02'=>$ld_totalp_per2,
														   'periodo03'=>$ld_totalp_per3,
														   'totcom'=>$ld_totalp_comprometido,
														   'ajucom'=>$ld_totalp_ajuste,
														   'modpres'=>$ld_totalp_modificaciones,
														   'precom'=>$ld_totalp_precompromiso,
														   'libprecom'=>$ld_totalp_libPrecompromiso,
														   'disponible'=>$ld_totalp_disponible);
							break;
	
							case 3:
								$la_data_tot_cta[0]=array('programatica'=>$ls_programatica,
														   'dispact'=>$ld_dispact,
														   'dispant'=>$ld_disant,
														   'precomprometido'=>$ld_comprometido,
														   'libprecomprometido'=>$ld_libprecomprometido,
														   'libcomprometido'=>$ld_libcomprometido,
														   'status'=>$ls_status,
														   'estcla'=>$ls_estcla,
														   'cuenta'=>'Total Partida '.$ls_partida,
														   'denominacion'=>'',
														   'disponact'=>$ld_totalp_disponible_ant,
														   'periodo01'=>$ld_totalp_per1,
														   'periodo02'=>$ld_totalp_per2,
														   'periodo03'=>$ld_totalp_per3,
														   'totcom'=>$ld_totalp_comprometido,
														   'ajucom'=>$ld_totalp_ajuste,
														   'modpres'=>$ld_totalp_modificaciones,
														   'precom'=>$ld_totalp_precompromiso,
														   'libprecom'=>$ld_totalp_libPrecompromiso,
														   'disponible'=>$ld_totalp_disponible);
	
							break;
						}
						$ld_totalp_disponible		= 0;
						$ld_totalp_disponible_ant   = 0;
						$ld_totalp_comprometido 	= 0;
						$ld_totalp_ajuste 			= 0;
						$ld_totalp_modificaciones 	= 0;
						$ld_totalp_precompromiso	= 0;
						$ld_totalp_libPrecompromiso	= 0;
						$ld_totalp_per1				= 0;
						$ld_totalp_per2				= 0;
						$ld_totalp_per3				= 0;
						uf_print_detalle_bold($lo_libro,$lo_hoja,$la_data_tot_cta,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
				    }
					else
					{
					 uf_print_detalle($lo_libro,$lo_hoja,$la_data_cta,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
					}
				}
				
				if($ls_programatica != $ls_programatica_next)
				{
					 switch($ld_tipper)
					 {
									case 1:
										 $la_data_tot[0]=array('programatica'=>$ls_programatica,
																	'dispact'=>$ld_dispact,
																	'dispant'=>$ld_disant,
																	'precomprometido'=>$ld_comprometido,
																	'libprecomprometido'=>$ld_libprecomprometido,
																	'libcomprometido'=>$ld_libcomprometido,
																	'status'=>$ls_status,
																	'estcla'=>$ls_estcla,
																	'cuenta'=>'TOTAL',
																	'denominacion'=>'',
																	'disponact'=>$ld_total_disponible_ant,
																	'periodo01'=>$ld_total_per1,
																	'periodo02'=>$ld_total_per2,
																	'periodo03'=>$ld_total_per3,
																	'totcom'=>$ld_total_comprometido,
																	'ajucom'=>$ld_total_ajuste,
																	'modpres'=>$ld_total_modificaciones,
																	'precom'=>$ld_total_precompromiso,
																	'libprecom'=>$ld_total_libPrecompromiso,
																	'disponible'=>$ld_total_disponible);
									break;
			
									case 2:
										$la_data_tot[0]=array('programatica'=>$ls_programatica,
																   'dispact'=>$ld_dispact,
																   'dispant'=>$ld_disant,
																   'precomprometido'=>$ld_comprometido,
																   'libprecomprometido'=>$ld_libprecomprometido,
																   'libcomprometido'=>$ld_libcomprometido,
																   'status'=>$ls_status,
																   'estcla'=>$ls_estcla,
																   'cuenta'=>'TOTAL',
																   'denominacion'=>'',
																   'disponact'=>$ld_total_disponible_ant,
																   'periodo01'=>$ld_total_per1,
																   'periodo02'=>$ld_total_per2,
																   'periodo03'=>$ld_total_per3,
																   'totcom'=>$ld_total_comprometido,
																   'ajucom'=>$ld_total_ajuste,
																   'modpres'=>$ld_total_modificaciones,
																   'precom'=>$ld_total_precompromiso,
																   'libprecom'=>$ld_total_libPrecompromiso,
																   'disponible'=>$ld_total_disponible);
									break;
			
									case 3:
										$la_data_tot[0]=array('programatica'=>$ls_programatica,
																   'dispact'=>$ld_dispact,
																   'dispant'=>$ld_disant,
																   'precomprometido'=>$ld_comprometido,
																   'libprecomprometido'=>$ld_libprecomprometido,
																   'libcomprometido'=>$ld_libcomprometido,
																   'status'=>$ls_status,
																   'estcla'=>$ls_estcla,
																   'cuenta'=>'TOTAL',
																   'denominacion'=>'',
																   'disponact'=>$ld_total_disponible_ant,
																   'periodo01'=>$ld_total_per1,
																   'periodo02'=>$ld_total_per2,
																   'periodo03'=>$ld_total_per3,
																   'totcom'=>$ld_total_comprometido,
																   'ajucom'=>$ld_total_ajuste,
																   'modpres'=>$ld_total_modificaciones,
																   'precom'=>$ld_total_precompromiso,
																   'libprecom'=>$ld_total_libPrecompromiso,
																   'disponible'=>$ld_total_disponible);
			
									break;
					 }
					 $li_fila++;
					 uf_print_detalle_bold($lo_libro,$lo_hoja,$la_data_tot,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,&$li_fila);
					 $ld_total_disponible		= 0;
					 $ld_total_disponible_ant	= 0;
					 $ld_total_comprometido 	= 0;
					 $ld_total_ajuste 			= 0;
					 $ld_total_modificaciones 	= 0;
					 $ld_total_precompromiso	= 0;
					 $ld_total_libPrecompromiso	= 0;
					 $ld_total_per1				= 0;
					 $ld_total_per2				= 0;
					 $ld_total_per3				= 0;
					
					}
				
		
		} // End for
	  }
	  if ($li_tot>0)
	  {
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"ejecucion_financiera_del_presupuesto_causado.xls\"");
		header("Content-Disposition: inline; filename=\"ejecucion_financiera_del_presupuesto_causado.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");

	 }
	 else
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');");
		print(" close();");
		print("</script>");
	 }	 
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_function_report);
	unset($io_fecha);



?>

