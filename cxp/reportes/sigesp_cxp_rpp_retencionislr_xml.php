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
	
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("../class_folder/class_funciones_cxp.php");
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

	$ls_titulo="XML DE RETENCION DEL IMPUESTO SOBRE LA RENTA";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	//--------------------------------------------------------------------------------------------------------------------------------
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
                        $ls_nombrearchivo="../xml/XML_relacionRetencionesISLR_Prov.xml";
                        //escritura del encabezado del archivo XML
                        $ls_cadena='';
                        $ls_cadena.='<?xml version="1.0" encoding="utf-8" ?>'."\r\n";
                        $ls_cadena.='<RelacionRetencionesISLR RifAgente="'.str_replace("-","",$_SESSION["la_empresa"]['rifemp']).'" Periodo="'.$ls_ano.$ls_mes.'">'."\r\n";
                        $a=1;
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
                                       // echo $lb_valido."<hr>";
					if($lb_valido)
					{
						$li_total=$io_report->ds_detalle->getRowCount("numfac");
						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];
                                                        $ls_codded=$io_report->ds_detalle->data["codded"][$li_i];
                                                        $numfac = $io_report->ds_detalle->data["numfac"][$li_i];
                                                        $ls_cadena.='<DetalleRetencion>'."\r\n";
                                                        $ls_cadena.="\t".'<RifRetenido>'.str_replace("-","",$ls_rif_prov).'</RifRetenido>'."\r\n";
                                                        $ls_cadena.="\t".'<NumeroFactura>'.str_replace("-","",$numfac).'</NumeroFactura>'."\r\n";
                                                        $ls_cadena.="\t".'<NumeroControl>'.$a.'</NumeroControl>'."\r\n";
                                                        $ls_cadena.="\t".'<CodigoConcepto>'.$ls_codded.'</CodigoConcepto>'."\r\n";
                                                        $ls_cadena.="\t".'<MontoOperacion>'.$li_baseimp.'</MontoOperacion>'."\r\n";
                                                        $ls_cadena.="\t".'<PorcentajeRetencion>'.$li_porimp.'</PorcentajeRetencion>'."\r\n";
                                                        $ls_cadena.='</DetalleRetencion>'."\r\n";
                                                        $a++;
						  }
					}
				}
			}
                        $ls_cadena.='</RelacionRetencionesISLR>';
                        $fp = fopen($ls_nombrearchivo,"w+");
                        fwrite($fp, $ls_cadena);
                        fclose($fp);
                        print("<script language=JavaScript>");
			print(" alert('El archivo ha sido creado');");
			print(" pagina='../sigesp_cxp_cat_descarga.php?file=XML_relacionRetencionesISLR_Prov.xml&enlace=xml/';");
			print(" window.open(pagina,'catalogo','menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no');");
                        print(" close();");
			print("</script>");
		}
	}
	
?>