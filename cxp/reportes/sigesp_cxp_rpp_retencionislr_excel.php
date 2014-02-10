<?php
       /*ini_set('error_reporting', E_ALL);
        ini_set('display_errors' , 'On');
        ini_set('display_startup_errors', 'On');*/
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//    REPORTE: Retencion de ISLR
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
		// Fecha Creación: 14/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;

		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesiva.php",$ls_descripcion);
		return $lb_valido;
	}// end function uf_insert_seguridad
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
        set_time_limit(0);
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("../class_folder/class_funciones_cxp.php");
         $ls_mes='';
	$io_fun_cxp=new class_funciones_cxp();
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
        $ls_mes = $io_fun_cxp->uf_obtenervalor_get("mes","");

        $ls_ano = $io_fun_cxp->uf_obtenervalor_get("anio","");
	global $ls_tiporeporte;

	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------

	$ls_titulo="DATA PARA CREAR ARCHIVO XML DE RETENCION DEL IMPUESTO SOBRE LA RENTA";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
        //--------------------------------------------  Llamada a clases de gneracion de excel  ------------------------------------------
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "retenciones_excel.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();

        //seguridad
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$la_comprobantes=split('-',$ls_comprobantes);
		$la_datos=array_unique($la_comprobantes);
		$li_totrow=count($la_datos);
		sort($la_datos,SORT_STRING);
		if($li_totrow<=0)
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');");
			print(" close();");
			print("</script>");
		}
		else
		{
                        $ls_rif_agente = str_replace("-","",$_SESSION["la_empresa"]['rifemp']);
                        $ls_periodo    = $ls_ano.$ls_mes;
                        

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
                        $lo_hoja->set_column(2,2,50);
                        $lo_hoja->set_column(3,3,20);
                        $lo_hoja->set_column(4,4,30);
                        $lo_hoja->set_column(5,5,30);
                        $lo_hoja->set_column(6,6,30);

                        $lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
                        $lo_hoja->write(2, 0, 'RIF Agente: ' ,$lo_encabezado);
                        $lo_hoja->write(2, 1,  $ls_rif_agente,$lo_encabezado);
                        $lo_hoja->write(3, 0, 'Periodo: ',$lo_encabezado);
                        $lo_hoja->write(3, 1, $ls_periodo,$lo_encabezado);

                         //ENCABEZADO DE LA PAGINA
                        $lo_hoja->write(5, 0, "ID-SEC",$lo_titulo);
                        $lo_hoja->write(5, 1, "RIF RETENIDO", $lo_titulo);
                        $lo_hoja->write(5, 2, utf8_decode("NÚMERO FACTURA"), $lo_titulo);
                        $lo_hoja->write(5, 3, utf8_decode("NÚMERO CONTROL"), $lo_titulo);
                        $lo_hoja->write(5, 4,  utf8_decode("CÓDIGO CONCEPTO"), $lo_titulo);
                        $lo_hoja->write(5, 5,  utf8_decode("MONTO OPERACIÓN"), $lo_titulo);
                        $lo_hoja->write(5, 6, "PORCENTAJE", $lo_titulo);
                        $lo_hoja->write(5, 7, "TOTAL GENERAL", $lo_titulo);


                        
                        $a=1;
                        $fila = 7;
                         $columna=0;
			for ($li_z=0;$li_z<$li_totrow;$li_z++)
			{
				//uf_print_encabezadopagina($ls_titulo,&$io_pdf);
				$ls_numcom=$la_datos[$li_z];

				$lb_valido=$io_report->uf_retencionesislr_proveedor($ls_numcom);
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
						$ls_rif_bene=$io_report->DS->data["rifben"][$li_i];
                                                $ls_rif_prov=$io_report->DS->data["rifpro"][$li_i];
						$ls_dirsujret=$io_report->DS->data["dirsujret"][$li_i];
						$li_estcmpret=$io_report->DS->data["estcmpret"][$li_i];
                                             

                                        }
					$lb_valido=$io_report->uf_retencionesislr_detalle($ls_numcom);
					if($lb_valido)
					{
						$li_total=$io_report->ds_detalle->getRowCount("numfac");

						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];
                                                        $ls_codded=$io_report->ds_detalle->data["codded"][$li_i];
                                                        $ls_numfac = $io_report->ds_detalle->data["numfac"][$li_i];
                                                        $ls_numcomseniaf = $io_report->ds_detalle->data["numcon"][$li_i];
                                                        $ls_total_iva = $io_report->ds_detalle->data["iva_ret"][$li_i];
                                                        $columna=0;
                                                        $lo_hoja->write($fila, $columna, $a ,$lo_dataleft);
                                                        $columna++;
                                                        $lo_hoja->write($fila, $columna, str_replace("-","",$ls_rif_prov),$lo_dataleft);
                                                        $columna++;
                                                        $lo_hoja->write($fila, $columna, $ls_numfac.' ',$lo_dataleft);
                                                        $columna++;
                                                        $lo_hoja->write($fila, $columna, $ls_numcomseniaf.' ',$lo_dataleft);
                                                        $columna++;
                                                        $lo_hoja->write($fila, $columna, $ls_codded.' ',$lo_dataleft);
                                                        $columna++;
                                                        $lo_hoja->write($fila, $columna, $li_baseimp,$lo_dataright);
                                                        $columna++;
                                                        $lo_hoja->write($fila, $columna, $li_porimp,$lo_dataright);
                                                        $columna++;
                                                        $lo_hoja->write($fila, $columna, $ls_total_iva,$lo_dataright);
                                                        $a++;
                                                        $fila++;
						  }
					}
				}
			}
                    
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"retenciones_excel.xls\"");
			header("Content-Disposition: inline; filename=\"retenciones_excel.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
			print("<script language=JavaScript>");
			//print(" close();");
			print("</script>");
		}
	}

?>