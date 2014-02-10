<?php
    session_start();   
	//ini_set('memory_limit','512M');
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
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_scg_r_mayor_analitico.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
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

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "mayor_analitico.xls");
		$lo_libro = &new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		require_once("../../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();
		require_once("../../shared/class_folder/class_fecha.php");
		$io_fecha = new class_fecha();
		require_once("../class_funciones_scg.php");
		$io_fun_scg=new class_funciones_scg();
		$ls_tiporeporte="0";
		$ls_bolivares="";
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
	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros para Filtar el Reporte
		$ld_fecdesde=$_GET["fecdes"];
		$ld_fechasta=$_GET["fechas"];
		$ls_cuentadesde_min=$_GET["cuentadesde"];
		$ls_cuentahasta_max=$_GET["cuentahasta"];
		$li_recortar=$_GET["recortar"];
		$li_lenconcepto=$_GET["lenconcepto"];
		
		$datos_reporte['sc_cuenta_d'] = $_GET["cuentadesde"];
		$datos_reporte['sc_cuenta_h'] = $_GET["cuentahasta"];
		$datos_reporte['fecdes'] = $_GET["fecdes"];
		$datos_reporte['fechas'] = $_GET["fechas"];
		$datos_reporte['recortar'] = $_GET["recortar"];
		$datos_reporte['lenconcepto'] = $_GET["lenconcepto"];
		$datos_reporte['orden'] = $_GET["orden"];
		
		if(($ls_cuentadesde=="")&&($ls_cuentahasta==""))
		{
			if($io_report->uf_spg_reporte_select_cuenta($ls_cuentadesde,$ls_cuentahasta))
			{
				//$ls_cuentadesde=$ls_cuentadesde_min;
				//$ls_cuentahasta=$ls_cuentahasta_max;
			} 
		}
	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros del encabezado
		$ldt_fecha="Desde   ".$ld_fecdesde."   al   ".$ld_fechasta."";
		$ls_titulo="Mayor  Analitico";       
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data
	$lb_valido=uf_insert_seguridad("<b>Mayor Analítico en Excel</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		 $cuentas=$io_report->cuentas_mayor($datos_reporte);
    }
	 if($cuentas===false) // Existe algún error ó no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');");
		//print(" close();");
		print("</script>");
	 }
	
	else // Imprimimos el reporte
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
		$lo_hoja->set_column(3,3,30);		
		$lo_hoja->set_column(4,4,20);
		$lo_hoja->set_column(5,5,13);
		$lo_hoja->set_column(6,8,30);
		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 3, $ldt_fecha,$lo_encabezado);
		
		
		$ld_totaldebe=0;
		$ld_totalhaber=0;
		$ld_totalsaldo=0;
        $ld_saldo=0;
		$ldec_mondeb=0;
        $ldec_monhab=0;
		$li_row=2;
		$ls_cuenta_ant="";
		$ld_montototaldebe=0;
		$ld_montototalhaber=0;
		
		$siguiente = 0;	
		$count = 0;
		
		foreach($cuentas as $fila_sc){
					
					//echo 'Cuenta: '.$fila_sc['sc_cuenta'].'<br>';
					$datos = $datos_reporte;
					$datos['sc_cuenta_d'] = $fila_sc['sc_cuenta'];
					$datos['sc_cuenta_h'] = $fila_sc['sc_cuenta'];
					$fila_mayor=$io_report->mayor_analitico($datos);
					
					$total = $io_report->rs_analitico->RecordCount();
					$ld_totaldebe=0;
					$ld_totalhaber=0;								
					$ld_totalsaldo=0;
					$ld_saldo=0;
					$ldec_mondeb=0;
					$ldec_monhab=0;
					//echo 'Total: '.$total.'<br>';
									
					if($total>0){
							
							
							$thisPageNum=$io_pdf->ezPageCount;
							
							$ls_cuenta=trim($io_report->rs_analitico->fields["sc_cuenta"]);							
							$ls_denominacion=$io_report->rs_analitico->fields["denominacion"];
							$ld_saldo_ant=$io_report->rs_analitico->fields["saldo_ant"];
							$contador = 0;	
							
							$li_row=$li_row+1;
							$lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
							$lo_hoja->write($li_row, 1, " ".$ls_cuenta,$lo_datacenter);
							$lo_hoja->write($li_row, 2, $ls_denominacion,$lo_libro->addformat(array('font'=>'Verdana','align'=>'left','size'=>'9')));
							$li_row=$li_row+1;
							$lo_hoja->write($li_row, 0, "Saldo Anterior ".$ls_bolivares,$lo_titulo);
							$lo_hoja->write($li_row, 1, $ld_saldo_ant,$lo_dataright);
							$li_row=$li_row+1;
							$lo_hoja->write($li_row, 0, "Procede",$lo_titulo);
							$lo_hoja->write($li_row, 1, "Comprobante",$lo_titulo);
							$lo_hoja->write($li_row, 2, "Concepto",$lo_titulo);
							$lo_hoja->write($li_row, 3, "Beneficiario",$lo_titulo);
							$lo_hoja->write($li_row, 4, "Documento",$lo_titulo);
							$lo_hoja->write($li_row, 5, "Fecha",$lo_titulo);
							$lo_hoja->write($li_row, 6, "Debe",$lo_titulo);
							$lo_hoja->write($li_row, 7, "Haber",$lo_titulo);
							$lo_hoja->write($li_row, 8, "Saldo Actual",$lo_titulo);
							
							
							foreach($io_report->rs_analitico as $fila_mayor){								
									
									$contador++;					
									$ls_comprobante=$io_report->rs_analitico->fields["comprobante"];
									$ls_codpro=$fila_mayor["cod_pro"];
									$ls_cedbene=$fila_mayor["ced_bene"];
									$ls_nompro=$fila_mayor["nompro"];
									$ls_nombene=$fila_mayor["apebene"].", ".$fila_mayor["nombene"];
									$ls_nombre="";
									$ls_codban=$fila_mayor["codban"];
									$ls_ctaban=$fila_mayor["ctaban"];
									if($ls_codpro!="----------")
									{
										$ls_nombre=$ls_nompro;
									}
									if($ls_cedbene!="----------")
									{
										$ls_nombre=$ls_nombene;
									}
									$ls_documento=$fila_mayor["documento"];
									$ls_procede=$fila_mayor["procede"];
									if ($ls_procede=='SCBBCH')
									{
										$io_report->uf_scg_mayor_analitico_info_cheques($ls_comprobante,$ls_codban,$ls_ctaban);
										$ls_cheque=$io_report->rs_info_cheques->fields["numdoc"];
										$ls_nombanco=$io_report->rs_info_cheques->fields["nomban"];
										$ld_fecmov=$io_report->rs_info_cheques->fields["fecmov"];
						
										if ($ls_cheque=='')
										{
											$ls_cheque='';
										}
										if ($ls_nombanco=='')
										{
											$ls_nombanco='';
										}				
										if ($ld_fecmov=='')
										{
											$ld_fecmov='';
										}	
										$ls_infobanco="   <b>Cheque:</b> $ls_cheque, <b>Fecha:</b> $ld_fecmov, <b>Banco:</b> $ls_nombanco ";				
									}
									else
									{
										$ls_infobanco='';
									}			
									if ($li_recortar==1)
									{
										$ls_concepto=substr($fila_mayor["descripcion"],0,$li_lenconcepto).$ls_infobanco;
									}
									else
									{
										$ls_concepto=$fila_mayor["descripcion"].$ls_infobanco;
									}
									$ldec_monto=$fila_mayor["monto"];
									$fecmov=$fila_mayor["fecha"];
									$ld_fecmov=$io_funciones->uf_convertirfecmostrar($fecmov);
									$ls_debhab=$fila_mayor["debhab"];									
																
									
									if($ls_debhab=='D')
									{
										$ldec_mondeb=$ldec_monto;
										$ldec_monhab=0;
										$ld_totaldebe=$ld_totaldebe+$ldec_mondeb;
						
									}
									elseif($ls_debhab=='H')
									{
										$ldec_monhab=$ldec_monto;
										$ldec_mondeb=0;
										$ld_totalhaber=$ld_totalhaber+$ldec_monhab;
						
									}
									if ($contador==1)
									{
									  $ld_saldo=$ld_saldo_ant+$ldec_mondeb-$ldec_monhab;
									}
									else
									{
										if($ls_debhab=='D')
										{
											$ld_saldo=$ld_saldo+$ldec_monto;
										}
										elseif($ls_debhab=='H')
										{
											$ld_saldo=$ld_saldo-$ldec_monto;
										}
									}					
									
									$pdf_mondeb=number_format(abs($ldec_mondeb),2,",",".");
									$pdf_monhab=number_format(abs($ldec_monhab),2,",",".");
									if($ld_saldo<0)
									{									
									  $ld_saldo_aux=number_format(abs($ld_saldo),2,",",".");
									  $ld_saldo_final="(".$ld_saldo_aux.")";
									}
									else
									{
									  $ld_saldo_aux=number_format($ld_saldo,2,",",".");
									  $ld_saldo_final=$ld_saldo_aux;
									}
									
									$li_row=$li_row+1;
									$lo_hoja->write($li_row, 0, $ls_procede,$lo_datacenter);
									$lo_hoja->write($li_row, 1, $ls_comprobante." ",$lo_datacenter);
									$lo_hoja->write($li_row, 2, $ls_concepto,$lo_dataleft);
									$lo_hoja->write($li_row, 3, $ls_nombre,$lo_dataleft);
									$lo_hoja->write($li_row, 4, $ls_documento." ",$lo_datacenter);
									$lo_hoja->write($li_row, 5, $ld_fecmov,$lo_datacenter);
									$lo_hoja->write($li_row, 6, $ldec_mondeb,$lo_dataright);
									$lo_hoja->write($li_row, 7, $ldec_monhab,$lo_dataright);
									$lo_hoja->write($li_row, 8, $ld_saldo_final,$lo_dataright);									
									
							}//fin del foreach
							
							$ld_saldo_ant_pdf=number_format($ld_saldo_ant,2,",",".");
							
							$li_totfil=0;
							$as_cuenta="";
							for($li=$li_total;$li>1;$li--)
							{
								$li_ant=$ia_niveles_scg[$li-1];
								$li_act=$ia_niveles_scg[$li];
								$li_fila=$li_act-$li_ant;
								$li_len=strlen($ls_cuenta);
								$li_totfil=$li_totfil+$li_fila;
								$li_inicio=$li_len-$li_totfil;
								if($li==$li_total){$as_cuenta=substr($ls_cuenta,$li_inicio,$li_fila);}
								else{$as_cuenta=substr($ls_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;}
							}
							$li_fila=$ia_niveles_scg[1]+1;
							$as_cuenta=substr($ls_cuenta,0,$li_fila)."-".$as_cuenta;
										
							$ld_totalsaldo_final=$ld_saldo_final;
							
							$ldec_mondeb=abs($ldec_mondeb);
							$ldec_monhab=abs($ldec_monhab);
						
							$ld_montototaldebe=$ld_montototaldebe+$ld_totaldebe;
							$ld_montototalhaber=$ld_montototalhaber+$ld_totalhaber;	
							
							$li_row=$li_row+1;
							$lo_hoja->write($li_row, 5, "Total ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
							$lo_hoja->write($li_row, 6, $ld_totaldebe,$lo_dataright);
							$lo_hoja->write($li_row, 7, $ld_totalhaber,$lo_dataright);
							$lo_hoja->write($li_row, 8, $ld_saldo_final,$lo_dataright);

							
					}//fin del if
		
		}//fin del foreach
		
			
		$ld_montototalhaber=number_format($ld_montototalhaber,2,",",".");
		$ld_montototaldebe=number_format($ld_montototaldebe,2,",",".");
		
		$li_row=$li_row+2;
		$lo_hoja->write($li_row, 4, "Total General al ".$ld_fechasta.'  '.$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
		$lo_hoja->write($li_row, 6, $ld_montototaldebe,$lo_dataright);
		$lo_hoja->write($li_row, 7, $ld_montototalhaber,$lo_dataright);

		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"mayor_analitico.xls\"");
		header("Content-Disposition: inline; filename=\"mayor_analitico.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
?> 