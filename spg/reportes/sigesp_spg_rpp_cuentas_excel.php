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
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');
	//--------------------------------------------------------------------------------------------------------------------------------
	
	  // para crear el libro excel
		require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo =  tempnam("/tmp", "Listado_de_Cuentas_Presupuestarias.xls");
		$lo_libro = &new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_encabezado_pagina($as_titulo,$as_periodo,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		    Acess: private
		//	    Arguments: ldec_monto : Monto del cheque
		//	    		   ls_nomproben:  Nombre del proveedor o beneficiario
		//	    		   ls_monto : Monto en letras
		//	    		   ls_fecha : Fecha del cheque
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creaci�n: 25/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_encabezado;
		$io_pdf->stopObject($io_encabezado);
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],30,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el t�tulo

		$io_pdf->addText(20,678,10,$GLOBALS["ls_tit1"]);
		$io_pdf->addText(20,664,10,$GLOBALS["ls_tit2a"]);
		$io_pdf->addText(80,664,10,$GLOBALS["ls_tit2b"]);
		$io_pdf->addText(20,652,10,$GLOBALS["ls_tit3a"]);
		$io_pdf->addText(80,652,10,$GLOBALS["ls_tit3b"]);
		$io_pdf->addText(20,640,10,$GLOBALS["ls_tit4a"]);
		$io_pdf->addText(80,640,10,$GLOBALS["ls_tit4b"]);

		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,718,11,$as_periodo); // Agregar el t�tulo
		$io_pdf->addText(500,740,10,$_SESSION["ls_database"]);// Agrerar el nombre de la base de datos actual
		$io_pdf->addText(500,730,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina

	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_denominacion_estructura2($la_columna,$la_config,$la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creaci�n: 24/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;

		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		//$io_pdf->ezText('                     ',10);//Inserto una linea en blanco
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------


	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_columna,$la_config,$la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creaci�n: 24/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->ezText('                     ',10);//Inserto una linea en blanco
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------


	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("../../shared/class_folder/sigesp_include.php");
	$in=new sigesp_include();
	$con=$in->uf_conectar();
	require_once("../../shared/class_folder/class_sql.php");
	$io_sql=new class_sql($con);
	$io_sql2=new class_sql($con);
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("../../shared/class_folder/class_datastore.php");
	$ds_prog=new class_datastore();
	$ds_ctas=new class_datastore();
	require_once("sigesp_spg_funciones_reportes.php");
	$io_function_report = new sigesp_spg_funciones_reportes();
	require_once("sigesp_spg_reporte.php");
	$io_spg_report=new sigesp_spg_reporte();

	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	$ls_codestpro1_desde=$_GET["codestpro1"];
	$ls_codestpro2_desde=$_GET["codestpro2"];
	$ls_codestpro3_desde=$_GET["codestpro3"];
	$ls_codestpro1_hasta=$_GET["codestpro1h"];
	$ls_codestpro2_hasta=$_GET["codestpro2h"];
	$ls_codestpro3_hasta=$_GET["codestpro3h"];
	$ls_cuenta_desde=$_GET["txtcuentades"];
	$ls_cuenta_hasta=$_GET["txtcuentahas"];
	$ls_estclades = $_GET["estclades"];
	$ls_estclahas = $_GET["estclahas"];
    $ls_codfuefindes=$_GET["txtcodfuefindes"];
    $ls_codfuefinhas=$_GET["txtcodfuefinhas"];
        
    if (($ls_codfuefindes=='')&&($ls_codfuefindes==''))
    {
	  if($io_function_report->uf_spg_select_fuentefinanciamiento(&$ls_minfuefin,&$ls_maxfuefin))
	  {
		 $ls_codfuefindes=$ls_minfuefin;
		 $ls_codfuefinhas=$ls_maxfuefin;
	  }
    }
   /////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codestpro1_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro1_desde,25);
	 $ls_codestpro2_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro2_desde,25);
	 $ls_codestpro3_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro3_desde,25);

	 $ls_codestpro1_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro1_hasta,25);
	 $ls_codestpro2_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro2_hasta,25);
	 $ls_codestpro3_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro3_hasta,25);
	 if($li_estmodest==2)
	 {
		 $ls_codestpro4_desde=$io_funciones->uf_cerosizquierda($_GET["codestpro4"],25);
		 $ls_codestpro5_desde=$io_funciones->uf_cerosizquierda($_GET["codestpro5"],25);
		 $ls_codestpro4_hasta=$io_funciones->uf_cerosizquierda($_GET["codestpro4h"],25);
		 $ls_codestpro5_hasta=$io_funciones->uf_cerosizquierda($_GET["codestpro5h"],25);
	 }
	 else
	 {
		 $ls_codestpro4_desde=$io_funciones->uf_cerosizquierda(0,25);
		 $ls_codestpro5_desde=$io_funciones->uf_cerosizquierda(0,25);
		 $ls_codestpro4_hasta=$io_funciones->uf_cerosizquierda(0,25);
		 $ls_codestpro5_hasta=$io_funciones->uf_cerosizquierda(0,25);
	 }

	$ls_aux_sql="";
	$ls_aux="";
	$ls_gestor = $_SESSION["ls_gestor"];
	$ls_seguridad="";
	$io_function_report->uf_filtro_seguridad_programatica('a',$ls_seguridad);
	if($li_estmodest==1)
	{
		if(strtoupper($ls_gestor)=="MYSQLT")
		{
		   $ls_concat="CONCAT(a.codestpro1,a.codestpro2,a.codestpro3,a.codestpro4,a.codestpro5,a.estcla)";
		}
		else
		{
		   $ls_concat="(a.codestpro1||a.codestpro2||a.codestpro3||a.codestpro4||a.codestpro5||a.estcla)";
		}
		if(($ls_cuenta_desde!="")&&($ls_cuenta_hasta!=""))
		{
			$ls_aux=" AND a.spg_cuenta between '".$ls_cuenta_desde."' AND '".$ls_cuenta_hasta."'";
		}
		$ls_estructura_desde="";
		$ls_estructura_hasta="";
		//if(($ls_codestpro1_desde!="**")&&(!empty($ls_codestpro1_desde)))
		if($ls_codestpro1_desde<>"0000000000000000000000000")
		{
			$ls_codestpro1_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro1_desde,25);
			$ls_estructura_desde= $ls_codestpro1_desde;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_min_codestpro1(&$ls_codestpro1_desde);
			$ls_estructura_desde=$ls_codestpro1_desde;
		}
		//if(($ls_codestpro2_desde!="**")&&(!empty($ls_codestpro2_desde)))
		if($ls_codestpro2_desde<>"0000000000000000000000000")
		{
			$ls_codestpro2_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro2_desde,25);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro2_desde;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_min_codestpro2($ls_codestpro1_desde,&$ls_codestpro2_desde);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro2_desde;
		}
		//if(($ls_codestpro3_desde!="**")&&(!empty($ls_codestpro3_desde)))
		if($ls_codestpro3_desde<>"0000000000000000000000000")
		{
			$ls_codestpro3_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro3_desde,25);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro3_desde;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_min_codestpro3($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro3_desde;
		}
		//if(($ls_codestpro4_desde!="**")&&(!empty($ls_codestpro4_desde)))
		if($ls_codestpro4_desde<>"0000000000000000000000000")
		{
		$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro4_desde;
		}
		else
		{
			$io_spg_report->uf_spg_reporte_select_min_codestpro4($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_codestpro4_desde);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro4_desde;
		}
		//if(($ls_codestpro5_desde!="**")&&(!empty($ls_codestpro5_desde)))
		if($ls_codestpro5_desde<>"0000000000000000000000000")
		{	$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro5_desde;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_min_codestpro5($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_codestpro4_desde,$ls_codestpro5_desde);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro5_desde;
		}

		//if(($ls_codestpro1_hasta!="**")&&(!empty($ls_codestpro1_hasta)))
		if($ls_codestpro1_hasta<>"0000000000000000000000000")
		{
			$ls_codestpro1_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro1_hasta,25);
			$ls_estructura_hasta=$ls_codestpro1_hasta;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_max_codestpro1(&$ls_codestpro1_hasta);
			$ls_estructura_hasta=$ls_codestpro1_hasta;
		}
		//if(($ls_codestpro2_hasta!="**")&&(!empty($ls_codestpro2_hasta)))
		if($ls_codestpro2_hasta<>"0000000000000000000000000")
		{
			$ls_codestpro2_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro2_hasta,25);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro2_hasta;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_max_codestpro2($ls_codestpro1_hasta,$ls_codestpro2_hasta);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro2_hasta;
		}
		//if(($ls_codestpro3_hasta!="**")&&(!empty($ls_codestpro3_hasta)))
		if($ls_codestpro3_hasta<>"0000000000000000000000000")
		{
			$ls_codestpro3_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro3_hasta,25);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro3_hasta;
		}
		else
		{
			$io_spg_report->uf_spg_reporte_select_max_codestpro3($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro3_hasta;
		}
		//if(($ls_codestpro4_hasta!="**")&&(!empty($ls_codestpro4_hasta)))
		if($ls_codestpro4_hasta<>"0000000000000000000000000")
		{
		 $ls_codestpro4_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro4_hasta,25);
		 $ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro4_hasta;
		}
		else
		{
			$io_spg_report->uf_spg_reporte_select_max_codestpro4($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_codestpro4_hasta);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro4_hasta;
		}
		//if(($ls_codestpro5_hasta!="**")&&(!empty($ls_codestpro5_hasta)))
		if($ls_codestpro5_hasta<>"0000000000000000000000000")
		{
		 $ls_codestpro5_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro5_hasta,25);
		 $ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro5_hasta;
		}
		else
		{
			$io_spg_report->uf_spg_reporte_select_max_codestpro5($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_codestpro4_hasta,$ls_codestpro5_hasta);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro5_hasta;
		}

		$ls_aux_sql=" AND ".$ls_concat." between '".$ls_estructura_desde.$ls_estclades."' AND '".$ls_estructura_hasta.$ls_estclahas."'";

		/*if(($ls_codestpro1_desde!="")&&($ls_codestpro2_desde!="")&&($ls_codestpro3_desde!="")&&($ls_codestpro1_hasta!="")&&($ls_codestpro2_hasta!="")&&($ls_codestpro3_hasta!=""))
		{
			$ls_aux_sql=" AND ".$ls_concat." between '".$ls_codestpro1_desde.$ls_codestpro2_desde.$ls_codestpro3_desde.$ls_estclades."' AND '".$ls_codestpro1_hasta.$ls_codestpro2_hasta.$ls_codestpro3_hasta.$ls_estclahas."'";
		}*/

		$ls_sql=" SELECT distinct a.codestpro1 as codestpro1, ".
		        "        a.codestpro2 as codestpro2,a.codestpro3 as codestpro3, ".
				"        b.denestpro1,c.denestpro2, spg_ep3.denestpro3, a.estcla as estcla".
				" FROM   spg_cuentas a,spg_ep1 b,spg_ep2 c, spg_ep3 ".
				" WHERE  a.codemp='".$ls_codemp."' ".
				"   AND  a.codestpro1=b.codestpro1 ".
				"   AND  a.codestpro1=c.codestpro1 ".
				"   AND  a.estcla=c.estcla  ".
                "   AND  a.estcla=b.estcla  ".
				"   AND  a.codestpro2=c.codestpro2 ".
				"   AND  a.codestpro1=spg_ep3.codestpro1 ".
				"   AND  a.codestpro2=spg_ep3.codestpro2  ".
				"   AND  a.codestpro3=spg_ep3.codestpro3  ".
				"   AND  a.estcla=spg_ep3.estcla ".
				"   AND  spg_ep3.codfuefin BETWEEN '".$ls_codfuefindes."' AND '".$ls_codfuefinhas."' ".
				"        ".$ls_aux_sql." ".$ls_aux." ".$ls_seguridad; //print $ls_sql;
	}
	else
	{
		if(strtoupper($ls_gestor)=="MYSQLT")
		{
		   $ls_concat="CONCAT(a.codestpro1,a.codestpro2,a.codestpro3,a.codestpro4,a.codestpro5,a.estcla)";
		}
		else
		{
		   $ls_concat="(a.codestpro1||a.codestpro2||a.codestpro3||a.codestpro4||a.codestpro5||a.estcla)";
		}
		if(($ls_cuenta_desde!="")&&($ls_cuenta_hasta!=""))
		{
			$ls_aux=" AND a.spg_cuenta between '".$ls_cuenta_desde."' AND '".$ls_cuenta_hasta."' ";
		}
		$ls_estructura_desde="";
		$ls_estructura_hasta="";
		//if(($ls_codestpro1_desde!="**")&&(!empty($ls_codestpro1_desde)))
		if(!empty($ls_codestpro1_desde))
		{
			$ls_codestpro1_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro1_desde,25);
			$ls_estructura_desde= $ls_codestpro1_desde;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_min_codestpro1(&$ls_codestpro1_desde);
			$ls_estructura_desde=$ls_codestpro1_desde;
		}
		//if(($ls_codestpro2_desde!="**")&&(!empty($ls_codestpro2_desde)))
		if(!empty($ls_codestpro2_desde))
		{
			$ls_codestpro2_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro2_desde,25);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro2_desde;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_min_codestpro2($ls_codestpro1_desde,&$ls_codestpro2_desde);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro2_desde;
		}
		//if(($ls_codestpro3_desde!="**")&&(!empty($ls_codestpro3_desde)))
		if(!empty($ls_codestpro3_desde))
		{
			$ls_codestpro3_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro3_desde,25);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro3_desde;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_min_codestpro3($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro3_desde;
		}
		//if(($ls_codestpro4_desde!="**")&&(!empty($ls_codestpro4_desde)))
		if(!empty($ls_codestpro4_desde))
		{	$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro4_desde;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_min_codestpro4($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_codestpro4_desde);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro4_desde;
		}
		//if(($ls_codestpro5_desde!="**")&&(!empty($ls_codestpro5_desde)))
		if(!empty($ls_codestpro5_desde))
		{	$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro5_desde;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_min_codestpro5($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_codestpro4_desde,$ls_codestpro5_desde);
			$ls_estructura_desde=$ls_estructura_desde.$ls_codestpro5_desde;
		}

		//if(($ls_codestpro1_hasta!="**")&&(!empty($ls_codestpro1_hasta)))
		if(!empty($ls_codestpro1_hasta))
		{
			$ls_codestpro1_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro1_hasta,25);
			$ls_estructura_hasta=$ls_codestpro1_hasta;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_max_codestpro1(&$ls_codestpro1_hasta);
			$ls_estructura_hasta=$ls_codestpro1_hasta;
		}
		//if(($ls_codestpro2_hasta!="**")&&(!empty($ls_codestpro2_hasta)))
		if(!empty($ls_codestpro2_hasta))
		{
			$ls_codestpro2_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro2_hasta,25);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro2_hasta;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_max_codestpro2($ls_codestpro1_hasta,$ls_codestpro2_hasta);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro2_hasta;
		}
		//if(($ls_codestpro3_hasta!="**")&&(!empty($ls_codestpro3_hasta)))
		if(!empty($ls_codestpro3_hasta))
		{
			$ls_codestpro3_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro3_hasta,25);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro3_hasta;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_max_codestpro3($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro3_hasta;
		}
		//if(($ls_codestpro4_hasta!="**")&&(!empty($ls_codestpro4_hasta)))
		if(!empty($ls_codestpro4_hasta))
		{	$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro4_hasta;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_max_codestpro4($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_codestpro4_hasta);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro4_hasta;
		}
		//if(($ls_codestpro5_hasta!="**")&&(!empty($ls_codestpro5_hasta)))
		if(!empty($ls_codestpro5_hasta))
		{	$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro5_hasta;	}
		else
		{
			$io_spg_report->uf_spg_reporte_select_max_codestpro5($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_codestpro4_hasta,$ls_codestpro5_hasta);
			$ls_estructura_hasta=$ls_estructura_hasta.$ls_codestpro5_hasta;
		}
		$ls_aux_sql=" AND ".$ls_concat." between '".$ls_estructura_desde.$ls_estclades."' AND '".$ls_estructura_hasta.$ls_estclahas."'";
		$ls_codestpro1_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro1_desde,25);
		$ls_codestpro2_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro2_desde,25);
		$ls_codestpro3_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro3_desde,25);
		$ls_codestpro4_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro4_desde,25);
		$ls_codestpro5_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro5_desde,25);
		$ls_codestpro1_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro1_hasta,25);
		$ls_codestpro2_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro2_hasta,25);
		$ls_codestpro3_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro3_hasta,25);
		$ls_codestpro4_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro4_hasta,25);
		$ls_codestpro5_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro5_hasta,25);

		if(($ls_codestpro1_desde <>"0000000000000000000000000")&&($ls_codestpro2_desde="0000000000000000000000000")&&($ls_codestpro3_desde="0000000000000000000000000")&&($ls_codestpro4_desde="0000000000000000000000000")&&($ls_codestpro5_desde="0000000000000000000000000"))
		{
		 $ls_programatica_desde=$ls_codestpro1_desde;
		}
		elseif(($ls_codestpro1_desde <> "0000000000000000000000000")&&($ls_codestpro2_desde<>"0000000000000000000000000")&&($ls_codestpro3_desde="0000000000000000000000000")&&($ls_codestpro4_desde="0000000000000000000000000")&&($ls_codestpro5_desde="0000000000000000000000000"))
		{
		 $ls_programatica_desde=$ls_codestpro1_desde.$ls_codestpro2_desde;
		}
		elseif(($ls_codestpro1_desde <> "0000000000000000000000000")&&($ls_codestpro2_desde<>"0000000000000000000000000")&&($ls_codestpro3_desde<>"0000000000000000000000000")&&($ls_codestpro4_desde="0000000000000000000000000")&&($ls_codestpro5_desde="0000000000000000000000000"))
		{
		 $ls_programatica_desde=$ls_codestpro1_desde.$ls_codestpro2_desde.$ls_codestpro3_desde;
		}
		elseif(($ls_codestpro1_desde <> "0000000000000000000000000")&&($ls_codestpro2_desde<>"0000000000000000000000000")&&($ls_codestpro3_desde<>"0000000000000000000000000")&&($ls_codestpro4_desde<>"0000000000000000000000000")&&($ls_codestpro5_desde="0000000000000000000000000"))
		{
		 $ls_programatica_desde=$ls_codestpro1_desde.$ls_codestpro2_desde.$ls_codestpro3_desde.$ls_codestpro4_desde;
		}
		elseif(($ls_codestpro1_desde <> "0000000000000000000000000")&&($ls_codestpro2_desde<>"0000000000000000000000000")&&($ls_codestpro3_desde<>"0000000000000000000000000")&&($ls_codestpro4_desde<>"0000000000000000000000000")&&($ls_codestpro5_desde<>"0000000000000000000000000"))
		{
		 $ls_programatica_desde=$ls_codestpro1_desde.$ls_codestpro2_desde.$ls_codestpro3_desde.$ls_codestpro4_desde.$ls_codestpro5_desde;
		}


		if(($ls_codestpro1_hasta <>"0000000000000000000000000")&&($ls_codestpro2_hasta="0000000000000000000000000")&&($ls_codestpro3_hasta="0000000000000000000000000")&&($ls_codestpro4_hasta="0000000000000000000000000")&&($ls_codestpro5_hasta="0000000000000000000000000"))
		{
		 $ls_programatica_hasta=$ls_codestpro1_hasta;
		}
		elseif(($ls_codestpro1_hasta <> "0000000000000000000000000")&&($ls_codestpro2_hasta<>"0000000000000000000000000")&&($ls_codestpro3_hasta="0000000000000000000000000")&&($ls_codestpro4_hasta="0000000000000000000000000")&&($ls_codestpro5_hasta="0000000000000000000000000"))
		{
		 $ls_programatica_hasta=$ls_codestpro1_hasta.$ls_codestpro2_hasta;
		}
		elseif(($ls_codestpro1_hasta <> "0000000000000000000000000")&&($ls_codestpro2_hasta<>"0000000000000000000000000")&&($ls_codestpro3_hasta<>"0000000000000000000000000")&&($ls_codestpro4_hasta="0000000000000000000000000")&&($ls_codestpro5_hasta="0000000000000000000000000"))
		{
		 $ls_programatica_hasta=$ls_codestpro1_hasta.$ls_codestpro2_hasta.$ls_codestpro3_hasta;
		}
		elseif(($ls_codestpro1_hasta <> "0000000000000000000000000")&&($ls_codestpro2_hasta<>"0000000000000000000000000")&&($ls_codestpro3_hasta<>"0000000000000000000000000")&&($ls_codestpro4_hasta<>"0000000000000000000000000")&&($ls_codestpro5_hasta="0000000000000000000000000"))
		{
		 $ls_programatica_hasta=$ls_codestpro1_hasta.$ls_codestpro2_hasta.$ls_codestpro3_hasta.$ls_codestpro4_hasta;
		}
		elseif(($ls_codestpro1_hasta <> "0000000000000000000000000")&&($ls_codestpro2_hasta<>"0000000000000000000000000")&&($ls_codestpro3_hasta<>"0000000000000000000000000")&&($ls_codestpro4_hasta<>"0000000000000000000000000")&&($ls_codestpro5_hasta<>"0000000000000000000000000"))
		{
		 $ls_programatica_hasta=$ls_codestpro1_hasta.$ls_codestpro2_hasta.$ls_codestpro3_hasta.$ls_codestpro4_hasta.$ls_codestpro5_hasta;
		}



		 $ls_desc_event="Solicitud de Reporte Listado de Cuentas Presupuestaria Desde la Cuenta ".$ls_cuenta_desde." hasta ".$ls_cuenta_hasta." ,  Desde la Programatica  ".$ls_programatica_desde." hasta ".$ls_programatica_hasta;
		 $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_spg_r_cuentas.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sql=" SELECT distinct a.codestpro1 as codestpro1,a.codestpro2 as codestpro2,a.codestpro3 as codestpro3, ".
		        "                 a.codestpro4 as codestpro4,a.codestpro5 as codestpro5, b.denestpro1, c.denestpro2, ".
				"                 a.estcla as estcla".
				" FROM  spg_cuentas a, spg_ep1 b, spg_ep2 c, spg_ep5".
				" WHERE a.codemp='".$ls_codemp."' ".
				"   AND a.codestpro1=b.codestpro1 ".
				"   AND a.codestpro1=c.codestpro1 ".
				"   AND a.codestpro2=c.codestpro2 ".
				"   AND spg_ep5.codfuefin BETWEEN '".$ls_codfuefindes."' AND '".$ls_codfuefinhas."' ".
				"       ".$ls_aux_sql."  ".$ls_aux." ".$ls_seguridad;

	}

	$rs_data=$io_sql->select($ls_sql);
	if($rs_data===false)
	{

	}
	else
	{
		$ds_prog->data=$io_sql->obtener_datos($rs_data);
	}
//	set_time_limit(1800);
	//print $ls_sql;
	$li_totrow=$ds_prog->getRowCount("codestpro1");
	if($li_totrow<=0)
	{
		?>
		<script language=javascript>
			 alert('No hay datos a reportar!!!');
//			 close();
		</script>
		<?php
	}
	else
	{
		
		
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];

		//---> carga valores del primer registro
		$ls_denestpro1   = trim($ds_prog->getValue("denestpro1",1));
		$ls_denestpro2   = trim($ds_prog->getValue("denestpro2",1));
		$ls_denestpro3   = trim($ds_prog->getValue("denestpro3",1));
		$ls_codestpro1=$ds_prog->getValue("codestpro1",1);
		$ls_codestpro2=$ds_prog->getValue("codestpro2",1);
		$ls_codestpro3=$ds_prog->getValue("codestpro3",1);
		$ls_estcla=$ds_prog->getValue("estcla",1);
		if ($ls_estcla=="P")
		{
			$ls_tipoE="PROYECTO";
		}
		else
		{
			$ls_tipoE="ACCI�N";
		}

		$ls_tit1="<b>ESTRUCTURA PRESUPUESTARIA  (TIPO: ".$ls_tipoE.")</b>";
		$ls_tit2a= substr($ls_codestpro1,-$ls_loncodestpro1);
		$ls_tit2b= $ls_denestpro1;
		$ls_tit3a= substr($ls_codestpro2,-$ls_loncodestpro2);
		$ls_tit3b= $ls_denestpro2;
		$ls_tit4a= substr($ls_codestpro3,-$ls_loncodestpro3);
		$ls_tit4b= $ls_denestpro3;


		//---> fin carga valores primer registro

		$ls_encabezado="Listado de Cuentas Presupuestarias"; // Imprimimos el encabezado de la p�gina
		
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		
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
		$lo_hoja->write(0, 3,$ls_encabezado,$lo_titulo);
	    $ls_spg_cuenta_ant="";
		$ld_total_asignado=0;
		$ld_total_aumento=0;
		$ld_total_disminucion=0;
		$ld_total_monto_actualizado=0;
		$ld_total_compromiso=0;
		$ld_total_precompromiso=0;
		$ld_total_compromiso=0;
		$ld_total_saldo_comprometer=0;
		$ld_total_causado=0;
		$ld_total_pagado=0;
		$ld_total_por_paga=0;
		$li_row=2;
		$li_tot=$rs_data->RecordCount();
		$z=0;
		
		//---> for para la estructura presupuestaria
		$contlineas=0;
		for($li_i=0;$li_i<$li_totrow;$li_i++)
		{
			$li_totprenom=0;
			$ldec_mondeb=0;
			$ldec_monhab=0;
			$li_totant=0;
			unset($la_data);
			unset($la_data_ctas);
			$ls_denestpro1   = trim($ds_prog->getValue("denestpro1",$li_i));
			$ls_denestpro2   = trim($ds_prog->getValue("denestpro2",$li_i));
			$ls_denestpro3   = trim($ds_prog->getValue("denestpro3",$li_i));
			$ls_codestpro1=$ds_prog->getValue("codestpro1",$li_i);
			$ls_codestpro2=$ds_prog->getValue("codestpro2",$li_i);
			$ls_codestpro3=$ds_prog->getValue("codestpro3",$li_i);
			$ls_estcla=$ds_prog->getValue("estcla",$li_i);
			if ($ls_estcla=="P")
			{
				$ls_tipoE="PROYECTO";
			}
			else
			{
				$ls_tipoE="ACCI�N";
			}
			$ls_estpro = substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3);

			if($li_estmodest==2)
			{
				$ls_codestpro4=$ds_prog->getValue("codestpro4",$li_i);
				$ls_codestpro5=$ds_prog->getValue("codestpro5",$li_i);
				$ls_estpro    = substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
				
				
				$la_data[1] = array('estpro'=>"<b>Programatica</b> :".$ls_estpro,'cuenta_scg'=>'');
				$la_data[2] = array('estpro'=>"<b>Proyecto / A.C.:</b>".$ls_denestpro1,'cuenta_scg'=>'<b>Acci�n Especifica:</b>'.$ls_denestpro2);
				$la_config    = array('showHeadings'=>0, // Mostrar encabezados
							 	    'showLines'=>0, // Mostrar L�neas
							 		'shaded'=>0, // Sombra entre l�neas
	 						 		'shadeCol'=>array(0.95,0.95,0.95), // Color de la sombra
							 		'shadeCol2'=>array(1.5,1.5,1.5), // Color de la sombra
							 		'xOrientation'=>'center', // Orientaci�n de la tabla
									'width'=>550, // Ancho de la tabla
							 		'maxWidth'=>550,
							 		'cols'=>array('estpro'=>array('justification'=>'left','width'=>300),'cuenta_scg'=>array('justification'=>'left','width'=>250))); // Ancho M�ximo de la tabla
			}
			else
			{

				$la_config = array('showHeadings'=>0, // Mostrar encabezados
							 	    'showLines'=>0, // Mostrar L�neas
							 		'shaded'=>0, // Sombra entre l�neas
	 						 		'shadeCol'=>array(0.95,0.95,0.95), // Color de la sombra
							 		'shadeCol2'=>array(1.5,1.5,1.5), // Color de la sombra
							 		'xOrientation'=>'center', // Orientaci�n de la tabla
									'width'=>550, // Ancho de la tabla
							 		'maxWidth'=>550,
							 		'cols'=>array('estpro'=>array('justification'=>'right','width'=>60),'cuenta_scg'=>array('justification'=>'left','width'=>490))); // Ancho M�ximo de la tabla

			}
			
			
			
			if ($li_i<=$li_totrow)
			{
				//---> carga valores a mostrar en encabezados
				$ls_denestpro1   = trim($ds_prog->getValue("denestpro1",$li_i+1));
				$ls_denestpro2   = trim($ds_prog->getValue("denestpro2",$li_i+1));
				$ls_denestpro3   = trim($ds_prog->getValue("denestpro3",$li_i+1));
				$ls_codestpro1=$ds_prog->getValue("codestpro1",$li_i+1);
				$ls_codestpro2=$ds_prog->getValue("codestpro2",$li_i+1);
				$ls_codestpro3=$ds_prog->getValue("codestpro3",$li_i+1);
				$ls_estcla=$ds_prog->getValue("estcla",$li_i+1);
				if ($ls_estcla=="P")
				{
					$ls_tipoE="PROYECTO";
				}
				else
				{
					$ls_tipoE="ACCI�N";
				}
				$ls_tit1='';
				$ls_tit2='';
				$ls_tit3='';
				$ls_tit4='';
				$ls_tit1="ESTRUCTURA PRESUPUESTARIA  (TIPO: ".$ls_tipoE.")";
				$ls_tit2a= substr($ls_codestpro1,-$ls_loncodestpro1);
				$ls_tit2b= $ls_denestpro1;
				$ls_tit3a= substr($ls_codestpro2,-$ls_loncodestpro2);
				$ls_tit3b= $ls_denestpro2;
				$ls_tit4a= substr($ls_codestpro3,-$ls_loncodestpro3);
				$ls_tit4b= $ls_denestpro3;
			}			
			
			$contlineas++;
			$lo_hoja->write($contlineas, 0,$ls_tit1,$lo_titulo);
			$contlineas++;
			$lo_hoja->write($contlineas, 0," ".$ls_tit2a, $lo_dataleft);
			$lo_hoja->write($contlineas, 1, $ls_tit2b,$lo_dataleft);
			$contlineas++;
			$lo_hoja->write($contlineas, 0," ".$ls_tit3a, $lo_dataleft);
			$lo_hoja->write($contlineas, 1, $ls_tit3b,$lo_dataleft);
			$contlineas++;
			$lo_hoja->write($contlineas, 0," ".$ls_tit4a, $lo_dataleft);
			$lo_hoja->write($contlineas, 1, $ls_tit4b,$lo_dataleft);
			$contlineas++;
			
			$lo_hoja->write($contlineas, 0,"Cuenta",$lo_datacenter);
			$lo_hoja->write($contlineas, 1,"Denominaci�n Cta Presupuestaria", $lo_datacenter);
			$lo_hoja->write($contlineas, 2, "Cuenta Contable",$lo_datacenter);
			$lo_hoja->write($contlineas, 3,"Denominaci�n Cta Contable", $lo_datacenter);
			$contlineas++;
			
			if($li_estmodest==1)
			{
				if(strtoupper($ls_gestor)=="MYSQLT")
				{
				   $ls_concat="CONCAT(a.codestpro1,a.codestpro2,a.codestpro3,a.estcla)";
				}
				else
				{
				   $ls_concat="(a.codestpro1||a.codestpro2||a.codestpro3||a.estcla)";
				}
				//		$ls_concat="CONCAT(a.codestpro1,a.codestpro2,a.codestpro3,a.estcla)";
				$ls_sql=" SELECT a.spg_cuenta as spg_cuenta,a.denominacion as denspg,a.sc_cuenta as sc_cuenta,a.status as status , ".
						"        b.denominacion as denscg ".
						" FROM   spg_cuentas a,scg_cuentas b ".
						" WHERE  a.codemp='".$ls_codemp."'  AND a.codemp=b.codemp AND a.sc_cuenta=b.sc_cuenta AND  ".
						"        ".$ls_concat."='".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_estcla."' ".$ls_aux.
						" ORDER BY a.spg_cuenta";
			}
			else
			{
				if(strtoupper($ls_gestor)=="MYSQLT")
				{
				   $ls_concat="CONCAT(a.codestpro1,a.codestpro2,a.codestpro3,a.codestpro4,a.codestpro5,a.estcla)";
				}
				else
				{
				   $ls_concat="(a.codestpro1||a.codestpro2||a.codestpro3||a.codestpro4||a.codestpro5||a.estcla)";
				}

				$ls_sql=" SELECT a.spg_cuenta as spg_cuenta,a.denominacion as denspg,a.sc_cuenta as sc_cuenta,a.status as status , ".
						"        b.denominacion as denscg ".
						" FROM   spg_cuentas a,scg_cuentas b ".
						" WHERE  a.codemp='".$ls_codemp."'  AND a.codemp=b.codemp AND a.sc_cuenta=b.sc_cuenta AND  ".
						"        ".$ls_concat."='".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla."' ".
						"        ".$ls_aux."  ORDER BY a.spg_cuenta";
			}
			$rs_data2=$io_sql2->select($ls_sql);
			if($rs_data2===false)
			{

			}
			else
			{
				$ds_ctas->data=$io_sql2->obtener_datos($rs_data2);
				$li_totspg=$ds_ctas->getRowCount("spg_cuenta");				
				for($li_a=1;$li_a<=$li_totspg;$li_a++)
			    {
					 $ls_cuenta      = trim($ds_ctas->getValue("spg_cuenta",$li_a));
					 $ls_denominacion= trim($ds_ctas->getValue("denspg",$li_a));
					 $ls_cuenta_scg  = trim($ds_ctas->getValue("sc_cuenta",$li_a));
					 $ls_status      = trim($ds_ctas->getValue("status",$li_a));
					 $ls_denscg      = trim($ds_ctas->getValue("denscg",$li_a));
					 if($ls_status=='C')
					 {
						$la_data_ctas[$li_a] = array('cuenta'=>'<b>'.$ls_cuenta.'</b>','denominacion'=>'<b>'.$ls_denominacion.'</b>','cuenta_scg'=>'<b>'.$ls_cuenta_scg.'</b>','denscg'=>'<b>'.$ls_denscg.'</b>');
					 }
					 else
					 {
						$la_data_ctas[$li_a] = array('cuenta'=>$ls_cuenta,'denominacion'=>$ls_denominacion,'cuenta_scg'=>' ','denscg'=>' ');
					 }
					 if($li_estmodest<>2)
					 {
					 	//IMPRIME denominaciones de cuentas
						//uf_print_denominacion_estructura($la_columna,$la_config,$la_data,$io_pdf);
						//uf_print_denominacion_estructura($la_columnatit_aux1,$la_configtit_aux1,$la_datatit_aux1,$io_pdf);
					 }
	
					 
					 
					$lo_hoja->write($contlineas, 0,$ls_cuenta,$lo_dataleft);
					$lo_hoja->write($contlineas, 1,$ls_denominacion, $lo_dataleft);
					$lo_hoja->write($contlineas, 2," ".$ls_cuenta_scg,$lo_dataleft);
					$lo_hoja->write($contlineas, 3,$ls_denscg, $lo_dataleft);
					$contlineas++;
			    }
			}
			$ls_tit1='';
			unset($GLOBALS['ls_tit2']);
			$ls_tit3='';
			$ls_tit4='';

		}
		
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"Listado_de_Cuentas_Presupuestarias.xls\"");
		header("Content-Disposition: inline; filename=\"Listado_de_Cuentas_Presupuestarias.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		unset($class_report);
		unset($io_funciones);
	}
?>