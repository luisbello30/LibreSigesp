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
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scg;	
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_scg_r_estado_resultado.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo1,$as_titulo2,$as_titulo3,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,705,11,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo1);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,690,11,$as_titulo1); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,680,11,$as_titulo2); // Agregar el título

		$li_tm=$io_pdf->getTextWidth(11,$as_titulo3);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,670,11,$as_titulo3); // Agregar el título

		$io_pdf->addText(510,725,7,$_SESSION["ls_database"]); // Agregar la Base de datos
		$io_pdf->addText(510,715,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(510,705,8,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_ingreso(&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('name'=>'<b>INGRESOS</b> '));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
						 'fontSize' => 7, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.7,0.7,0.7), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>500, // Ancho de la tabla
						 'rowGap'=>2,
						 'colGap'=>3,
						 'maxWidth'=>500); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->ezSetDy(-1);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_egreso(&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_egreso
		//		   Access: private
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('name'=>'<b>EGRESOS</b> '));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
						 'fontSize' => 7, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.7,0.7,0.7), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>500, // Ancho de la tabla
						 'rowGap'=>2,
						 'colGap'=>3,
						 'maxWidth'=>500); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->ezSetDy(-1);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_ingreso($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'rowGap'=>2,
						 'colGap'=>3,
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>110), // Justificación y ancho de la columna
						 			   'saldomay'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'saldomen'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
									   'saldo'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'denominacion'=>'<b>Denominación</b>',
						   'saldomay'=>'<b>Saldo</b>',
						   'saldomen'=>'<b></b>',
						   'saldo'=>'<b></b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_egreso($la_data_egr,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'rowGap'=>2,
						 'colGap'=>3,
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>110), // Justificación y ancho de la columna
						 			   'saldomay'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'saldomen'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
									   'saldo'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'denominacion'=>'<b>Denominación</b>',
						   'saldomay'=>'<b>Saldo</b>',
						   'saldomen'=>'<b></b>',
						   'saldo'=>'<b></b>');
		$io_pdf->ezTable($la_data_egr,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera_ingreso($ld_total_ingresos,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_totaldebe // Total debe
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por : Ing. Yozelin Barragan
		// Fecha Creación : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		
		$la_data=array(array('total'=>'<b>Total Ingreso '.$ls_bolivares.'</b>','saldomay'=>$ld_total_ingresos));
		$la_columna=array('total'=>'','saldomay'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho Máximo de la tabla
						 'rowGap'=>2,
						 'colGap'=>3,
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>300), // Justificación y ancho de la columna
						 			   'saldomay'=>array('justification'=>'right','width'=>200))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera_egreso($ld_total_egresos,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera_egreso
		//		    Acess : private
		//	    Arguments : ld_total_egresos // Total debe
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por : Ing. Yozelin Barragan
		// Fecha Creación : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		
		$la_data=array(array('total'=>'<b>Total Egreso '.$ls_bolivares.'</b>','saldomay'=>$ld_total_egresos));
		$la_columna=array('total'=>'','saldomay'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'rowGap'=>2,
						 'colGap'=>3,
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>300), // Justificación y ancho de la columna
						 			   'saldomay'=>array('justification'=>'right','width'=>200))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ld_total,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera_egreso
		//		    Acess : private
		//	    Arguments : ld_total // Total 
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por : Ing. Yozelin Barragan
		// Fecha Creación : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		if($ld_total<0)
		{
			$ls_cadena="DESAHORRO";
		}
		else
		{
			$ls_cadena="AHORRO";
		}
		$la_data=array(array('total'=>'<b>Total ('.$ls_cadena.') '.$ls_bolivares.'</b>','saldomay'=>$ld_total));
		$la_columna=array('total'=>'','saldomay'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'rowGap'=>2, // ancho entre lineas 
						 'colGap'=>3, //ancho entre  columnas
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>300), // Justificación y ancho de la columna
						 			   'saldomay'=>array('justification'=>'right','width'=>200))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_cabecera($io_cabecera,$as_programatica,$as_denestpro,&$io_pdf)
	//function uf_print_cabecera($as_programatica,$as_denestpro,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_programatica // programatica del comprobante
		//	    		   as_denestpro5 // denominacion de la programatica del comprobante
		//	    		   io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 21/04/2006 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->saveState();
		$io_pdf->ezSetY(650);
		
		$ls_codestpro = "";
		$li_estmodest = $_SESSION["la_empresa"]["estmodest"];
		if ($li_estmodest==1)
		{
          	$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	 		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	 		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	 
	 		$la_datatit=array(array('name'=>'<b>ESTRUCTURA PRESUPUESTARIA </b>'));
	 
	 		$la_columnatit=array('name'=>'');
	 
	 		$la_configtit=array('showHeadings'=>0, // Mostrar encabezados
					 			'showLines'=>0, // Mostrar Líneas
							 	'shaded'=>0, // Sombra entre líneas
					 			'fontSize' => 8, // Tamaño de Letras
					 			'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
					 			'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
					 			'xOrientation'=>'center', // Orientación de la tabla
					 			'xPos'=>302, // Orientación de la tabla
					 			'width'=>530, // Ancho de la tabla
					 			'maxWidth'=>530);// Ancho Máximo de la tabla
	 
	 		$io_pdf->ezTable($la_datatit,$la_columnatit,'',$la_configtit);	
	 		unset($la_data);
	 		$la_data=array(array('name'=>substr($as_programatica,0,$ls_loncodestpro1).'</b>','name2'=>$as_denestpro[0]),
                    array('name'=>substr($as_programatica,$ls_loncodestpro1,$ls_loncodestpro2),'name2'=>$as_denestpro[1]),
					array('name'=>substr($as_programatica,$ls_loncodestpro1+$ls_loncodestpro2,$ls_loncodestpro3),'name2'=>$as_denestpro[2]));
					
	 		$la_columna=array('name'=>'','name2'=>'');
	 		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					 		 'showLines'=>0, // Mostrar Líneas
					 		 'shaded'=>0, // Sombra entre líneas
					 		 'fontSize' => 8, // Tamaño de Letras
					 		 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
					  		 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
					 		 'xOrientation'=>'center', // Orientación de la tabla
					 		 'xPos'=>302, // Orientación de la tabla
					 		 'width'=>530, // Ancho de la tabla
					    	 'maxWidth'=>530,// Ancho Máximo de la tabla
					  		 'cols'=>array('name'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 		   'name2'=>array('justification'=>'left','width'=>490))); // Justificación y ancho de la columna
	 		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		/*	$io_pdf->restoreState();
		    $io_pdf->closeObject();
		    $io_pdf->addObject($io_cabecera,'all');
		   */ 
		}
		elseif($li_estmodest==2)
		{
			 $ls_denrep     = "PROGRAMATICA";
			 $la_data=array(array('name'=>'<b>'.$ls_denrep.'</b>  '.$as_programatica.''),
		                    array('name'=>'<b></b> '.$as_denestpro.''));
			 $la_columna=array('name'=>'');
		     $la_config=array('showHeadings'=>0, // Mostrar encabezados
						      'showLines'=>0, // Mostrar Líneas
						      'shaded'=>0, // Sombra entre líneas
						 	  'fontSize' => 8, // Tamaño de Letras
						      'colGap'=>1, // separacion entre tablas
						      'shadeCol'=>array(0.9,0.9,0.9),
						      'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						      'xOrientation'=>'center', // Orientación de la tabla
						      'xPos'=>302, // Orientación de la tabla
						      'width'=>530, // Ancho de la tabla
						      'maxWidth'=>530); // Ancho Máximo de la tabla
		    $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		   
		/*	$io_pdf->restoreState();
			$io_pdf->closeObject();
			$io_pdf->addObject($io_cabecera,'all');*/
		}	
		// $io_pdf->ezSetDy(-50);
		
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este método realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones,$ia_niveles_scg;
		
		$ls_formato=""; $li_posicion=0; $li_indice=0;
		$dat_emp=$_SESSION["la_empresa"];
		//contable
		$ls_formato = trim($dat_emp["formcont"])."-";
		$li_posicion = 1 ;
		$li_indice   = 1 ;
		$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		do
		{
			$ia_niveles_scg[$li_indice] = $li_posicion;
			$li_indice   = $li_indice+1;
			$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		} while ($li_posicion>=0);
	}// end function uf_init_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------

	 require_once("../../shared/ezpdf/class.ezpdf.php");
	 require_once("../../shared/class_folder/class_funciones.php");
	 $io_funciones=new class_funciones();
	 require_once("../../shared/class_folder/class_fecha.php");
	 require_once('../../shared/class_folder/Json2.php');
	 require_once('../../spg/reportes/sigesp_spg_class_report.php');
	 $oGastos= new sigesp_spg_class_report();
	 $io_fecha=new class_fecha();
	require_once("../class_funciones_scg.php");
	$io_fun_scg=new class_funciones_scg();
	$ls_tiporeporte="0";
	$ls_bolivares="";
	$rsEst="";
	if ($_GET['jasonest']) 	
	{
		$submit = str_replace("\\","",$_GET['jasonest']);
		//$submit = utf8_decode($submit);
		$json = new Services_JSON;
		$ArJson = $json->decode($submit);	
		if($ArJson->estructuras[0]->codestpro1=='todas')
		{
			$rsEst = $oGastos->uf_select_todasest();
		}
	}
	
	
	if (array_key_exists("tiporeporte",$_GET))
	{
		$ls_tiporeporte=$_GET["tiporeporte"];
	}
	switch($ls_tiporeporte)
	{
		case "0":
			require_once("sigesp_scg_reporte.php");
			$io_report  = new sigesp_scg_reporte();
			$ls_bolivares ="Bs.";
			break;

		case "1":
			require_once("sigesp_scg_reportebsf.php");
			$io_report  = new sigesp_scg_reportebsf();
			$ls_bolivares ="Bs.F.";
			break;
	}
	 $ia_niveles_scg[0]="";			
	 uf_init_niveles();
	 $li_total=count($ia_niveles_scg)-1;
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	 $ls_hidbot=$_GET["hidbot"];
	 if($ls_hidbot==true)
	 {
	   $ls_cmbmesdes=$_GET["cmbmesdes"];
	   $ls_cmbagnodes=$_GET["cmbagnodes"];
	   if($_SESSION["ls_gestor"]=='INFORMIX')
	   {
	     $fecdes=$ls_cmbagnodes."-".$ls_cmbmesdes."-01";
	     $ldt_fecdes=$ls_cmbagnodes."-".$ls_cmbmesdes."-01";
	   }
	   else 
	   {
	     $fecdes=$ls_cmbagnodes."-".$ls_cmbmesdes."-01"." 00:00:00";
	     $ldt_fecdes=$ls_cmbagnodes."-".$ls_cmbmesdes."-01"." 00:00:00";
	   }
	   $ls_cmbmeshas=$_GET["cmbmeshas"];
	   $ls_cmbagnohas=$_GET["cmbagnohas"];
	   $ls_last_day=$io_fecha->uf_last_day($ls_cmbmeshas,$ls_cmbagnohas);
	   $fechas=$ls_last_day;
	   $ldt_fechas=$io_funciones->uf_convertirdatetobd($ls_last_day);
	 }
	 elseif($ls_hidbot==false)
	 {
		 $fecdes=$_GET["txtfecdes"];
		 $ldt_fecdes=$io_funciones->uf_convertirdatetobd($fecdes);
		 $fechas=$_GET["txtfechas"];
		 $ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
	 }
	 $li_nivel=$_GET["cmbnivel"];
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ld_fecdes=$io_funciones->uf_convertirfecmostrar($fecdes);
		$ld_fechas=$io_funciones->uf_convertirfecmostrar($fechas);
		$ls_titulo="<b> ESTADO DE RESULTADOS</b>";
		$ls_titulo1="<b> ".$ls_nombre." </b>"; 
		$ls_titulo2="<b> al ".$ld_fechas."</b>";
		$ls_titulo3="<b>(Expresado en ".$ls_bolivares.")</b>";  
       // $ls_titulo2=" del  ".$ld_fecdes."  al  ".$ld_fechas." </b>";
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar datastore con los datos del reporte
	    error_reporting(E_ALL);
		//set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(7,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_titulo1,$ls_titulo2,$ls_titulo3,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		$lb_valido=uf_insert_seguridad("<b>Estado de Resultado estructura presupuetaria en PDF</b>"); // Seguridad de Reporte
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
				
		
		
		if(!is_object($rsEst))
		{
			$todas=false;
		}
		else
		{
			$todas=true;
		}	
		$j=0;
	//	for($i=0;$i<count($ArJson->estructuras);$i++)
	//	{ 
//			unset($la_data);
			

		/*
			$auxEst="0";
			$ls_denestpro = array();
			$arestructuras[0]=str_pad($ArJson->estructuras[$i]->codestpro1,25,"0","STR_PAD_LEFT");
			$arestructuras[1]=str_pad($ArJson->estructuras[$i]->codestpro2,25,"0","STR_PAD_LEFT");
			$arestructuras[2]=str_pad($ArJson->estructuras[$i]->codestpro3,25,"0","STR_PAD_LEFT");
			$arestructuras[3]=str_pad($auxEst,25,"0","STR_PAD_LEFT");
			$arestructuras[4]=str_pad($auxEst,25,"0","STR_PAD_LEFT");	
			$arestructuras[5]=$ArJson->estructuras[$i]->estcla;
		/*	$ls_denestpro[0]=$ls_denestpro1;
			$ls_denestpro[1]=$ls_denestpro2;
			$ls_denestpro[2]=$ls_denestpro3;
		*/
				
		//	$ls_programatica=$ArJson->estructuras[$i]->codestpro1.$ArJson->estructuras[$i]->codestpro2.$ArJson->estructuras[$i]->codestpro3;
			
		//	$oGastos->uf_spg_reporte_select_denestpro1($arestructuras[0],$ls_denestpro[0]);
		//	$oGastos->uf_spg_reporte_select_denestpro2($arestructuras[0],$arestructuras[1],$ls_denestpro[1]);
		//	$oGastos->uf_spg_reporte_select_denestpro3($arestructuras[0],$arestructuras[1],$arestructuras[2],$ls_denestpro[2]);
		
			
		$lb_valido=true;	
		
		if($lb_valido)
		{
			$lb_valido_ing=$io_report->uf_scg_reporte_estado_de_resultado_est_ingreso_consolidado($ldt_fecdes,$ldt_fechas,$li_nivel,$ArJson,$todas);
		
			$lb_valido_egr=$io_report->uf_scg_reporte_estado_de_resultado_est_egreso_consolidado($ldt_fecdes,$ldt_fechas,$li_nivel,$ArJson,$todas);
			
		}
		
			
		if((($lb_valido_ing==false)&&($lb_valido_egr==false))||($lb_valido==false)) // Existe algún error ó no hay registros
	    {
	    	
	    	//var_dump($lb_valido_ing);
	    	//var_dump($lb_valido_egr);
	    	//var_dump($lb_valido);
	    	//die();
	    	continue;
			/*
	    	print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');");
			print(" close();");
			print("</script>");
			*/
	    }
		else// Imprimimos el reporte
		{
			
			if ($j>0)
			{
			//	$io_pdf->ezNewPage(); 					  
			}	
			$j++;
			//$io_pdf->stopObject($io_cabecera);			
		  //  $io_cabecera=$io_pdf->openObject();
			//uf_print_cabecera($io_cabecera,$ls_programatica,$ls_denestpro,$io_pdf); // Imprimimos la cabecera del registro
			//$io_pdf->restoreState();
		 //   $io_pdf->closeObject();
		 //   $io_pdf->addObject($io_cabecera,'all');
			
		 if($lb_valido_ing)
		 {
			$li_tot=$io_report->dts_reporte->getRowCount("sc_cuenta");
			unset($la_data);
			$ld_total_ingresos=0;
			for($li_i=1;$li_i<=$li_tot;$li_i++)
			{
				$io_pdf->transaction('start'); // Iniciamos la transacción
				$thisPageNum=$io_pdf->ezPageCount;
				$ls_sc_cuenta=trim($io_report->dts_reporte->data["sc_cuenta"][$li_i]);
				$li_totfil=0;
				$as_cuenta="";
				for($li=$li_total;$li>1;$li--)
				{
					$li_ant=$ia_niveles_scg[$li-1];
					$li_act=$ia_niveles_scg[$li];
					$li_fila=$li_act-$li_ant;
					$li_len=strlen($ls_sc_cuenta);						
					$li_totfil=$li_totfil+$li_fila;     
					$li_inicio=$li_len-$li_totfil;      
					if($li==$li_total)
					{
						$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
					}
					else
					{
						$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
					}
				}
				$li_fila=$ia_niveles_scg[1]+1; 
				$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
				$ls_status=$io_report->dts_reporte->data["status"][$li_i];
				$ls_denominacion=$io_report->dts_reporte->data["denominacion"][$li_i];
				$ld_saldo=$io_report->dts_reporte->data["saldo"][$li_i];
				
				$ld_total_ingresos+=$ld_saldo;
				$ls_nivel=$io_report->dts_reporte->data["nivel"][$li_i];
				if($ls_nivel>3)
				{
                     $ld_saldo=abs($ld_saldo);
					 $ld_saldomay=number_format($ld_saldo,2,",",".");
					 $ld_saldomen="";  
					 $ld_saldo="";
				}
				if($ls_nivel==3)
				{
                     $ld_saldo=abs($ld_saldo);					
					 $ld_saldomay="";
					 $ld_saldomen=number_format($ld_saldo,2,",",".");  
					 $ld_saldo="";
				}
				if(($ls_nivel==1)||($ls_nivel==2))
				{
                     $ld_saldo=abs($ld_saldo);					
					 $ld_saldomay="";
					 $ld_saldomen="";  
					 $ld_saldo=number_format($ld_saldo,2,",",".");
				}
				
				$la_data[$li_i]=array('cuenta'=>$as_cuenta,'denominacion'=>$ls_denominacion,'saldomay'=>$ld_saldomay,'saldomen'=>$ld_saldomen,'saldo'=>$ld_saldo);
		
			}//for
			
			
		 	//var_dump($ld_total_ingresos);
			//	die();
			
			
			uf_print_cabecera_ingreso($io_pdf);
			uf_print_detalle_ingreso($la_data,$io_pdf); // Imprimimos el detalle 
			
			$ld_total_ingresos=abs($ld_total_ingresos);
			$ld_total_ingresos=number_format($ld_total_ingresos,2,",",".");
			uf_print_pie_cabecera_ingreso($ld_total_ingresos,$io_pdf); // Imprimimos pie de la cabecera
		}//if($lb_valido_ing)
		if($lb_valido_egr)
	    {
				$li_tot=$io_report->dts_egresos->getRowCount("sc_cuenta"); 
				unset($la_data_egr);
			    /*
				if($i==2)
				{
					var_dump($li_tot);
					echo $lb_valido;
					die();
				}
				*/
				$ld_total_egresos=0;
				for($li_i=1;$li_i<=$li_tot;$li_i++)
				{
					//$io_pdf->transaction('start'); // Iniciamos la transacción
					$thisPageNum=$io_pdf->ezPageCount;
					$ls_sc_cuenta=trim($io_report->dts_egresos->data["sc_cuenta"][$li_i]);
					$li_totfil=0;
					$as_cuenta="";
					
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);	
						$li_totfil=$li_totfil+$li_fila;     
						$li_inicio=$li_len-$li_totfil;      
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1; 
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ls_status=$io_report->dts_egresos->data["status"][$li_i];
					$ls_denominacion=$io_report->dts_egresos->data["denominacion"][$li_i];
					$ld_saldo=$io_report->dts_egresos->data["saldo"][$li_i];
					$ld_total_egresos+=$ld_saldo;
					$ls_nivel=$io_report->dts_egresos->data["nivel"][$li_i];
					if($ls_nivel>3)
					{
						 //$ld_saldo=abs($ld_saldo);
						 $ld_saldo=$ld_saldo*(-1);
						 
						 $ld_saldomay=number_format($ld_saldo,2,",",".");
						 if ($ld_saldomay < 0)
						 {
						 	$ld_saldomay='('.$ld_saldomay.')';
						    $ld_saldomay=str_replace('-',"",$ld_saldomay);
						 }
						 $ld_saldomen="";  
						 $ld_saldo="";
					}
					if($ls_nivel==3)
					{
						 //$ld_saldo=abs($ld_saldo);
						 $ld_saldo=$ld_saldo*(-1);
						 $ld_saldomay="";
						 $ld_saldomen=number_format($ld_saldo,2,",",".");  
						 $ld_saldo="";
					}
					if(($ls_nivel==1)||($ls_nivel==2))
					{
						 //$ld_saldo=abs($ld_saldo);
						 $ld_saldo=$ld_saldo*(-1);
						 $ld_saldomay="";
						 $ld_saldomen="";  
						 $ld_saldo=number_format($ld_saldo,2,",",".");
					}
					
					$la_data_egr[$li_i]=array('cuenta'=>$as_cuenta,'denominacion'=>$ls_denominacion,'saldomay'=>$ld_saldomay,'saldomen'=>$ld_saldomen,'saldo'=>$ld_saldo);
				}//for	
				
	    	
				
			uf_print_cabecera_egreso($io_pdf);
			uf_print_detalle_egreso($la_data_egr,$io_pdf); // Imprimimos el detalle
			if($lb_valido_ing)
			{ 
				$ld_total_ingresos=str_replace('.','',$ld_total_ingresos);
				$ld_total_ingresos=str_replace(',','.',$ld_total_ingresos);	
			}
			else
			{
			   $ld_total_ingresos=0;
			}
			$ld_total_egresos=abs($ld_total_egresos);
		    $ld_total=trim($ld_total_ingresos)-($ld_total_egresos);
			$ld_total_egresos=number_format($ld_total_egresos,2,",",".");
			uf_print_pie_cabecera_egreso($ld_total_egresos,$io_pdf); // Imprimimos pie de la cabecera	  
			$ld_total=number_format($ld_total,2,",",".");
            uf_print_pie_cabecera($ld_total,$io_pdf);
            
		}//if
	//	$io_pdf->stopObject($io_cabecera);
		unset($la_data);		
		unset($la_data_egr);
		unset($io_cabecera);
		unset($ls_programatica);
		unset($ls_denestpro);
	 }//else
	 
	
	
	//$io_cabecera,$ls_programatica,$ls_denestpro 
	 
 
 		

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
    unset($io_report);
	unset($io_funciones);			
?> 