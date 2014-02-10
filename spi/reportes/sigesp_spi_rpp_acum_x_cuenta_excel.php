<?php
    session_start();   
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');
	//--------------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "spi_mayor_analitico.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	require_once("sigesp_spi_reporte.php");
	$io_report = new sigesp_spi_reporte();
	require_once("sigesp_spi_funciones_reportes.php");
	$io_function_report = new sigesp_spi_funciones_reportes();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("../class_funciones_ingreso.php");
	$io_fun_ingreso=new class_funciones_ingreso();			
	require_once("../../shared/class_folder/class_fecha.php");
	$io_fecha = new class_fecha();
		
			
		
//--------------------------------------------------  Parámetros para Filtar el Reporte  ---------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$ls_cmbmesdes = $_GET["cmbmesdes"];
		$ldt_fecini=$li_ano."-01-01";
		$ldt_fecini_rep="01/01/".$li_ano;
		$ls_cmbmeshas = $_GET["cmbmeshas"];
		$ls_mes=$ls_cmbmeshas;
		$ls_ano=$li_ano;
		$fecfin=$io_fecha->uf_last_day($ls_mes,$ls_ano);
		$ldt_fecfin=$io_funciones->uf_convertirdatetobd($fecfin);
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
	    $ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
	    
	     
	    
		if($ls_estpreing==1)
		{
			$ls_codestpro1_min  = $_GET["codestpro1"];
			$ls_codestpro2_min  = $_GET["codestpro2"];
			$ls_codestpro3_min  = $_GET["codestpro3"];
			$ls_codestpro1h_max = $_GET["codestpro1h"];
			$ls_codestpro2h_max = $_GET["codestpro2h"];
			$ls_codestpro3h_max = $_GET["codestpro3h"];
			$ls_estclades       = $_GET["estclades"];
			$ls_estclahas       = $_GET["estclahas"];
			$ls_loncodestpro1   = $_SESSION["la_empresa"]["loncodestpro1"];
			$ls_loncodestpro2   = $_SESSION["la_empresa"]["loncodestpro2"];
			$ls_loncodestpro3   = $_SESSION["la_empresa"]["loncodestpro3"];
			$ls_loncodestpro4   = $_SESSION["la_empresa"]["loncodestpro4"];
			$ls_loncodestpro5   = $_SESSION["la_empresa"]["loncodestpro5"];
	
			if($ls_modalidad==1)
			{
				$ls_codestpro4_min =  "0000000000000000000000000";
				$ls_codestpro5_min =  "0000000000000000000000000";
				$ls_codestpro4h_max = "0000000000000000000000000";
				$ls_codestpro5h_max = "0000000000000000000000000";
				if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min==""))
				{
				  if($io_function_report->uf_spi_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,
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
				  if($io_function_report->uf_spi_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
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
			elseif($ls_modalidad==2)
			{
				$ls_codestpro4_min  = $_GET["codestpro4"];
				$ls_codestpro5_min  = $_GET["codestpro5"];
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
				}
				else
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
				  if($io_function_report->uf_spi_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,
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
				if(($ls_codestpro1h_max=="")||($ls_codestpro2h_max=="")||($ls_codestpro3h_max=="")||($ls_codestpro4h_max=="")||($ls_codestpro5h_max==""))
				{
				  if($io_function_report->uf_spi_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
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
			
			$ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
			$ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h;
			if($ls_modalidad==1)
			{
				if (($ls_codestpro1<>"")&&($ls_codestpro2=="")&&($ls_codestpro3==""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1);
				}
				elseif(($ls_codestpro1<>"")&&($ls_codestpro2<>"")&&($ls_codestpro3==""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2);
				}
				elseif(($ls_codestpro1<>"")&&($ls_codestpro2<>"")&&($ls_codestpro3<>""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2)."-".substr($ls_codestpro3h,-$ls_loncodestpro3);
				}
				else
				{
				 $ls_programatica_desde1="";
				 $ls_programatica_hasta1="";
				}
			}
			else
			{
				$ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5)."-".$ls_estclades;
				$ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2)."-".substr($ls_codestpro3h,-$ls_loncodestpro3)."-".substr($ls_codestpro4h,-$ls_loncodestpro4)."-".substr($ls_codestpro5h,-$ls_loncodestpro5)."-".$ls_estclahas;
			}
		}
		
		
		
		
		$cmbnivel=$_GET["cmbnivel"];
		if($cmbnivel=="s1")
		{
          $ls_cmbnivel="1";
		}
		else
		{
          $ls_cmbnivel=$cmbnivel;
		}
        $ls_subniv=$_GET["checksubniv"];
		if($ls_subniv==1)
		{
		  $lb_subniv=true;
		}
		else
		{
		  $lb_subniv=false;
		}
		/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
		
		
	  
		
		$ls_desc_event="Solicitud de Reporte Acumulado por Cuentas desde la fecha ".$ldt_fecini_rep." hasta ".$fecfin;
		$io_fun_ingreso->uf_load_seguridad_reporte("SPI","sigesp_spi_r_acum_x_cuentas.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////
     //----------------------------------------------------  Parámetros del encabezado  --------------------------------------------
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		$ls_titulo=" ACUMULADO POR CUENTAS  DESDE LA FECHA ".$ldt_fecini_rep."  HASTA  ".$fecfin."  ";
		if($ls_estpreing==1)
		{
	    	$ls_titulo1="<b> DESDE LA PROGRAMATICA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1." </b>"; 
		}
		$ls_tiporeporte=$_GET["tiporeporte"];
		global $ls_tiporeporte;		
		if($ls_tiporeporte==1)
		{
			require_once("sigesp_spi_reportebsf.php");
			$io_report=new sigesp_spi_reportebsf();
		}              
    //--------------------------------------------------------------------------------------------------------------------------------

    
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
	$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
	$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
	if($ls_estpreing==1)
	{
		$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
		$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
		$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
		$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
		$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
			
		$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
		$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
		$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
		$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
		$ls_codestpro5h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
		
		$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
	}
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
	$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
	$lo_dataright->set_font("Verdana");
	$lo_dataright->set_align('right');
	$lo_dataright->set_size('9');
	$lo_hoja->set_column(0,0,15);
	$lo_hoja->set_column(1,1,20);
	$lo_hoja->set_column(2,2,30);
	$lo_hoja->set_column(3,3,20);
	$lo_hoja->set_column(4,4,13);
	$lo_hoja->set_column(5,7,30);
	$lo_hoja->write(0, 3,$ls_titulo,$lo_titulo);
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
//	$li_tot=$rs_data->RecordCount();
	$z=0;
	$ld_total_previsto=0;
	$ld_total_aumento=0;
	$ld_total_disminucion=0;
	$ld_total_devengado=0;
	$ld_total_cobrado=0;
	$ld_total_cobrado_anticipado=0;
	$ld_total_monto_actualizado=0;
	$ld_total_por_cobrar=0;
	$ld_totalgen_previsto=0;
	$ld_totalgen_aumento=0;
	$ld_totalgen_disminucion=0;
	$ld_totalgen_devengado=0;
	$ld_totalgen_cobrado=0;
	$ld_totalgen_cobrado_anticipado=0;
	$ld_totalgen_monto_actualizado=0;
	$ld_totalgen_por_cobrar=0;
	$contlineas=0;
	$cuentamin = $_GET["cuentadesde"];
	$cuentamax = $_GET["cuentahasta"];
	if(empty($cuentamin))
	{
	 $io_function_report->uf_spi_reporte_select_max_cuenta($cuentamax);
	}
	if(empty($cuentamax))
	{
	 $io_function_report->uf_spi_reporte_select_min_cuenta($cuentamin);
	}
    if ($ls_estpreing==1)
	{
	    $lb_valido=$io_report->select_estructuras_spi($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
											        $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
											        $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas);
		$li_totfila=$io_report->data_est->getRowCount("programatica");
	
		for($j=1;(($j<=$li_totfila)&&($lb_valido));$j++)
		{
			  $ls_codestpro1=trim($io_report->data_est->data["codestpro1"][$j]); 
			  $ls_codestpro2=trim($io_report->data_est->data["codestpro2"][$j]);
			  $ls_codestpro3=trim($io_report->data_est->data["codestpro3"][$j]);
			  $ls_codestpro4=trim($io_report->data_est->data["codestpro4"][$j]);
			  $ls_codestpro5=trim($io_report->data_est->data["codestpro5"][$j]);
			  $ls_estcla=trim($io_report->data_est->data["estcla"][$j]);
			  $ls_estclades=trim($io_report->data_est->data["estcla"][$j]);
			  $ld_total_previsto=0;
			  $ld_total_aumento=0;
			  $ld_total_disminucion=0;
			  $ld_total_devengado=0;
			  $ld_total_cobrado=0;
			  $ld_total_cobrado_anticipado=0;
			  $ld_total_monto_actualizado=0;
			  $ld_total_por_cobrar=0;
			  $ls_codestpro1h=trim($io_report->data_est->data["codestpro1"][$j]);
			  $ls_codestpro2h=trim($io_report->data_est->data["codestpro2"][$j]);
			  $ls_codestpro3h=trim($io_report->data_est->data["codestpro3"][$j]);
			  $ls_codestpro4h=trim($io_report->data_est->data["codestpro4"][$j]);
			  $ls_codestpro5h=trim($io_report->data_est->data["codestpro5"][$j]);
			  $ls_estclahas=trim($io_report->data_est->data["estcla"][$j]); 
			  $ls_programatica=$io_report->data_est->data["programatica"][$j];
			  
		      $lb_valido=$io_report->uf_spi_reporte_acumulado_cuentas2($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
	                                                        $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
	                                                        $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas,$ldt_fecini,$ldt_fecfin,
															$ls_cmbnivel,$lb_subniv,$ai_MenorNivel,$ls_modalidad,$cuentamin,$cuentamax);
			 $li_tot=0;
			 $li_tot=$io_report->dts_reporte->getRowCount("spi_cuenta");
			 

			 	$lb_valido=$io_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);			
				if($lb_valido)
				{
					$ls_denestpro1=$ls_denestpro1; 
				}			
				if($lb_valido)
				{
					  $ls_denestpro2="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,
					                                                          $ls_denestpro2,$ls_estcla);
					  $ls_denestpro2=$ls_denestpro2;
				}
				if($lb_valido)
				{
					  $ls_denestpro3="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
																			  $ls_denestpro3,$ls_estcla);
					  $ls_denestpro3=$ls_denestpro3;
					  $ls_denestpro4="";
					  $ls_denestpro5="";
				} 
				if($ls_modalidad==2)
				{
					$ls_codestpro4=substr($ls_programatica,75,25);
					if($lb_valido)
					{
					  $ls_denestpro4="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
																			  $ls_codestpro4,$ls_denestpro4,$ls_estcla);
					  $ls_denestpro4=$ls_denestpro4;
					}
					$ls_codestpro5=substr($ls_programatica,100,25);
					if($lb_valido)
					{
					  $ls_denestpro5="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
																			  $ls_codestpro4,$ls_codestpro5,$ls_denestpro5,
																			  $ls_estcla);
					  $ls_denestpro5=$ls_denestpro5;
					}			
				}
	
			 
			 
		 	$ls_tit1='';
			$ls_tit2='';
			$ls_tit3='';
			$ls_tit4='';
			$ls_tit2a= substr($ls_codestpro1,-$ls_loncodestpro1);
			$ls_tit2b= $ls_denestpro1;
			$ls_tit3a= substr($ls_codestpro2,-$ls_loncodestpro2);
			$ls_tit3b= $ls_denestpro2;
			$ls_tit4a= substr($ls_codestpro3,-$ls_loncodestpro3);
			$ls_tit4b= $ls_denestpro3;
			if($ls_modalidad==2)
			{
			$ls_tit5a= substr($ls_codestpro4,-$ls_loncodestpro4);
			$ls_tit5b= $ls_denestpro4;
			$ls_tit6a= substr($ls_codestpro5,-$ls_loncodestpro5);
			$ls_tit6b= $ls_denestpro5;
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
            if($ls_modalidad==2)
			{
				$lo_hoja->write($contlineas, 0," ".$ls_tit5a, $lo_dataleft);
				$lo_hoja->write($contlineas, 1, $ls_tit5b,$lo_dataleft);
				$contlineas++;
				$lo_hoja->write($contlineas, 0," ".$ls_tit6a, $lo_dataleft);
				$lo_hoja->write($contlineas, 1, $ls_tit6b,$lo_dataleft);
				$contlineas++;
			}
			$contlineas++;
			
			
			
			
			
			$lo_hoja->write($contlineas, 0,"Cuenta",$lo_datacenter);
			$lo_hoja->write($contlineas, 1,"Denominación Cta Presupuestaria", $lo_datacenter);
			$lo_hoja->write($contlineas, 2, "Previsto",$lo_datacenter);
			$lo_hoja->write($contlineas, 3,"Aumento", $lo_datacenter);
			$lo_hoja->write($contlineas, 4, "Disminución",$lo_datacenter);
			$lo_hoja->write($contlineas, 5,"Devengado", $lo_datacenter);
			$lo_hoja->write($contlineas, 6, "Cobrado",$lo_datacenter);
			$lo_hoja->write($contlineas, 7,"Cobrado Anticipado", $lo_datacenter);
			$lo_hoja->write($contlineas, 8,"Monto Actualizado", $lo_datacenter);
			$lo_hoja->write($contlineas, 9,"Por Cobrar", $lo_datacenter);
			
			$contlineas++;
			
			 			 
			 
			 for($z=1;$z<=$li_tot;$z++)
			 {
				  $ls_spi_cuenta=$io_report->dts_reporte->data["spi_cuenta"][$z];
				  $ls_denominacion=$io_report->dts_reporte->data["denominacion"][$z];
				  $ls_nivel=$io_report->dts_reporte->data["nivel"][$z];
				  $ld_previsto=$io_report->dts_reporte->data["previsto"][$z];
				  $ld_aumento=$io_report->dts_reporte->data["aumento"][$z];
				  $ld_disminucion=$io_report->dts_reporte->data["disminucion"][$z];
				  $ld_devengado=$io_report->dts_reporte->data["devengado"][$z];
				  $ld_cobrado=$io_report->dts_reporte->data["cobrado"][$z];
				  $ld_cobrado_anticipado=$io_report->dts_reporte->data["cobrado_anticipado"][$z];
				  $ls_status=$io_report->dts_reporte->data["status"][$z];
				  $ld_monto_actualizado=$ld_previsto+$ld_aumento-$ld_disminucion-$ld_devengado;
				  $ld_por_cobrar=$ld_devengado-$ld_cobrado;
				 
				  if($ls_nivel==1)
				  {
					  $ld_total_previsto=$ld_total_previsto+$ld_previsto;
					  $ld_total_aumento=$ld_total_aumento+$ld_aumento;
					  $ld_total_disminucion=$ld_total_disminucion+$ld_disminucion;
					  $ld_total_devengado=$ld_total_devengado+$ld_devengado;
					  $ld_total_cobrado=$ld_total_cobrado+$ld_cobrado;
					  $ld_total_cobrado_anticipado=$ld_total_cobrado_anticipado+$ld_cobrado_anticipado;
					  $ld_total_monto_actualizado=$ld_total_monto_actualizado+$ld_monto_actualizado;
					  $ld_total_por_cobrar=$ld_total_por_cobrar+$ld_por_cobrar;
				  } 
				  $ld_previsto=number_format($ld_previsto,2,",",".");
				  $ld_aumento=number_format($ld_aumento,2,",",".");
				  $ld_disminucion=number_format($ld_disminucion,2,",",".");
				  $ld_devengado=number_format($ld_devengado,2,",",".");
				  $ld_cobrado=number_format($ld_cobrado,2,",",".");
				  $ld_cobrado_anticipado=number_format($ld_cobrado_anticipado,2,",",".");
				  $ld_monto_actualizado=number_format($ld_monto_actualizado,2,",",".");
				  $ld_por_cobrar=number_format($ld_por_cobrar,2,",",".");
				
				  $la_data1[$z]=array('cuenta'=>$ls_spi_cuenta,'denominacion'=>$ls_denominacion,'previsto'=>$ld_previsto,
									  'aumento'=>$ld_aumento,'disminución'=>$ld_disminucion,'devengado'=>$ld_devengado,
									  'cobrado'=>$ld_cobrado,'cobrado_anticipado'=>$ld_cobrado_anticipado,
									  'montoactualizado'=>$ld_monto_actualizado,'porcobrar'=>$ld_por_cobrar);
				  
				 
				$lo_hoja->write($contlineas, 0," ".$ls_spi_cuenta,$lo_dataleft);
				$lo_hoja->write($contlineas, 1," ".$ls_denominacion, $lo_dataleft);
				$lo_hoja->write($contlineas, 2, $ld_previsto,$lo_dataright);
				$lo_hoja->write($contlineas, 3,$ld_aumento, $lo_dataright);
				$lo_hoja->write($contlineas, 4, $ld_disminucion,$lo_dataright);
				$lo_hoja->write($contlineas, 5,$ld_devengado, $lo_dataright);
				$lo_hoja->write($contlineas, 6, $ld_cobrado,$lo_dataright);
				$lo_hoja->write($contlineas, 7,$ld_cobrado_anticipado, $lo_dataright);
				$lo_hoja->write($contlineas, 8,$ld_monto_actualizado, $lo_dataright);
				$lo_hoja->write($contlineas, 9,$ld_por_cobrar, $lo_dataright);
				$contlineas++;
				  
				  
				  
				  
				  
				 $ld_previsto=str_replace('.','',$ld_previsto);
				 $ld_previsto=str_replace(',','.',$ld_previsto);		
				 $ld_aumento=str_replace('.','',$ld_aumento);
				 $ld_aumento=str_replace(',','.',$ld_aumento);		
				 $ld_disminucion=str_replace('.','',$ld_disminucion);
				 $ld_disminucion=str_replace(',','.',$ld_disminucion);		
				 $ld_monto_actualizado=str_replace('.','',$ld_monto_actualizado);
				 $ld_monto_actualizado=str_replace(',','.',$ld_monto_actualizado);
				 $ld_devengado=str_replace('.','',$ld_devengado);
				 $ld_devengado=str_replace(',','.',$ld_devengado);		
				 $ld_cobrado=str_replace('.','',$ld_cobrado);
				 $ld_cobrado=str_replace(',','.',$ld_cobrado);		
				 $ld_cobrado_anticipado=str_replace('.','',$ld_cobrado_anticipado);
				 $ld_cobrado_anticipado=str_replace(',','.',$ld_cobrado_anticipado);		
				 $ld_por_cobrar=str_replace('.','',$ld_por_cobrar);
				 $ld_por_cobrar=str_replace(',','.',$ld_por_cobrar);
		
				if($z==$li_tot)
				{
				  $ld_totalgen_previsto+=$ld_total_previsto;
				  $ld_totalgen_aumento+=$ld_total_aumento;
				  $ld_totalgen_disminucion+=$ld_total_disminucion;
				  $ld_totalgen_devengado+=$ld_total_devengado;
				  $ld_totalgen_cobrado+=$ld_total_cobrado;
				  $ld_totalgen_cobrado_anticipado+=$ld_total_cobrado_anticipado;
				  $ld_totalgen_monto_actualizado+=$ld_total_monto_actualizado;
				  $ld_totalgen_por_cobrar+=$ld_total_por_cobrar;
				  
				  $ld_total_previsto=number_format($ld_total_previsto,2,",",".");
				  $ld_total_aumento=number_format($ld_total_aumento,2,",",".");
				  $ld_total_disminucion=number_format($ld_total_disminucion,2,",",".");
				  $ld_total_devengado=number_format($ld_total_devengado,2,",",".");
				  $ld_total_cobrado=number_format($ld_total_cobrado,2,",",".");
				  $ld_total_cobrado_anticipado=number_format($ld_total_cobrado_anticipado,2,",",".");
				  $ld_total_monto_actualizado=number_format($ld_total_monto_actualizado,2,",",".");
				  $ld_total_por_cobrar=number_format($ld_total_por_cobrar,2,",",".");
		 		  
				
				$lo_hoja->write($contlineas, 0," ",$lo_dataleft);
				$lo_hoja->write($contlineas, 1,"Total ", $lo_dataleft);
				$lo_hoja->write($contlineas, 2, $ld_total_previsto,$lo_dataright);
				$lo_hoja->write($contlineas, 3,$ld_total_aumento, $lo_dataright);
				$lo_hoja->write($contlineas, 4, $ld_total_disminucion,$lo_dataright);
				$lo_hoja->write($contlineas, 5,$ld_total_devengado, $lo_dataright);
				$lo_hoja->write($contlineas, 6, $ld_total_cobrado,$lo_dataright);
				$lo_hoja->write($contlineas, 7,$ld_total_cobrado_anticipado, $lo_dataright);
				$lo_hoja->write($contlineas, 8,$ld_total_monto_actualizado, $lo_dataright);
				$lo_hoja->write($contlineas, 9,$ld_total_por_cobrar, $lo_dataright);
				$contlineas++;
				  
				  
				
				  
				  $la_data_tot[$z]=array('total'=>'<b>TOTAL</b>','previsto'=>$ld_total_previsto,'aumento'=>$ld_total_aumento,
										 'disminución'=>$ld_total_disminucion,
										 'devengado'=>$ld_total_devengado,'cobrado'=>$ld_total_cobrado,
										 'cobrado_anticipado'=>$ld_total_cobrado_anticipado,
										 'montoactualizado'=>$ld_total_monto_actualizado,
										 'porcobrar'=>$ld_total_por_cobrar);
				}//if
			}//for
			  $lb_valido=$io_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);			
				if($lb_valido)
				{
					$ls_denestpro1=$ls_denestpro1; 
				}			
				if($lb_valido)
				{
					  $ls_denestpro2="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,
					                                                          $ls_denestpro2,$ls_estcla);
					  $ls_denestpro2=$ls_denestpro2;
				}
				if($lb_valido)
				{
					  $ls_denestpro3="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
																			  $ls_denestpro3,$ls_estcla);
					  $ls_denestpro3=$ls_denestpro3;
					  $ls_denestpro4="";
					  $ls_denestpro5="";
				} 
				if($ls_modalidad==2)
				{
					$ls_codestpro4=substr($ls_programatica,75,25);
					if($lb_valido)
					{
					  $ls_denestpro4="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
																			  $ls_codestpro4,$ls_denestpro4,$ls_estcla);
					  $ls_denestpro4=$ls_denestpro4;
					}
					$ls_codestpro5=substr($ls_programatica,100,25);
					if($lb_valido)
					{
					  $ls_denestpro5="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
																			  $ls_codestpro4,$ls_codestpro5,$ls_denestpro5,
																			  $ls_estcla);
					  $ls_denestpro5=$ls_denestpro5;
					}			
				}
				if($li_tot>0)
				{ 
					unset($la_data1);
				    unset($la_data_tot);
			    }		
				unset($la_data1);
				unset($la_data_tot);			
		 }// fin del for
		
		 
		 $ld_totalgen_previsto=number_format($ld_totalgen_previsto,2,",",".");
		 $ld_totalgen_aumento=number_format($ld_totalgen_aumento,2,",",".");
		 $ld_totalgen_disminucion=number_format($ld_totalgen_disminucion,2,",",".");
		 $ld_totalgen_devengado=number_format($ld_totalgen_devengado,2,",",".");
		 $ld_totalgen_cobrado=number_format($ld_totalgen_cobrado,2,",",".");
		 $ld_totalgen_cobrado_anticipado=number_format($ld_totalgen_cobrado_anticipado,2,",",".");
		 $ld_totalgen_monto_actualizado=number_format($ld_totalgen_monto_actualizado,2,",",".");
		 $ld_totalgen_por_cobrar=number_format($ld_totalgen_por_cobrar,2,",",".");
		 		
 		$lo_hoja->write($contlineas, 0," ",$lo_dataleft);
		$lo_hoja->write($contlineas, 1,"Total General ", $lo_dataleft);
		$lo_hoja->write($contlineas, 2, $ld_totalgen_previsto,$lo_dataright);
		$lo_hoja->write($contlineas, 3,$ld_totalgen_aumento, $lo_dataright);
		$lo_hoja->write($contlineas, 4, $ld_totalgen_disminucion,$lo_dataright);
		$lo_hoja->write($contlineas, 5,$ld_totalgen_devengado, $lo_dataright);
		$lo_hoja->write($contlineas, 6, $ld_totalgen_cobrado,$lo_dataright);
		$lo_hoja->write($contlineas, 7,$ld_totalgen_cobrado_anticipado, $lo_dataright);
		$lo_hoja->write($contlineas, 8,$ld_totalgen_monto_actualizado, $lo_dataright);
		$lo_hoja->write($contlineas, 9,$ld_totalgen_por_cobrar, $lo_dataright);
		$contlineas++;
	 
 
		
	 }
	 else // Imprimimos el reporte
	 {
   	    $lb_valido=$io_report->uf_spi_reporte_acumulado_cuentas($ldt_fecini,$ldt_fecfin,$ls_cmbnivel,$lb_subniv,$ai_MenorNivel,$cuentamin,$cuentamax);
		$li_tot=$io_report->dts_reporte->getRowCount("spi_cuenta");// print $li_tot;
		
		$contlineas++;
		$lo_hoja->write($contlineas, 0,"Cuenta",$lo_datacenter);
		$lo_hoja->write($contlineas, 1,"Denominación Cta Presupuestaria", $lo_datacenter);
		$lo_hoja->write($contlineas, 2, "Previsto",$lo_datacenter);
		$lo_hoja->write($contlineas, 3,"Aumento", $lo_datacenter);
		$lo_hoja->write($contlineas, 4, "Disminución",$lo_datacenter);
		$lo_hoja->write($contlineas, 5,"Devengado", $lo_datacenter);
		$lo_hoja->write($contlineas, 6, "Cobrado",$lo_datacenter);
		$lo_hoja->write($contlineas, 7,"Cobrado Anticipado", $lo_datacenter);
		$lo_hoja->write($contlineas, 8,"Monto Actualizado", $lo_datacenter);
		$lo_hoja->write($contlineas, 9,"Por Cobrar", $lo_datacenter);
		$contlineas++;
		
		for($z=1;$z<=$li_tot;$z++)
		{
			 
			  $thisPageNum=$io_pdf->ezPageCount;
		      $ls_spi_cuenta=$io_report->dts_reporte->data["spi_cuenta"][$z];
		      $ls_denominacion=$io_report->dts_reporte->data["denominacion"][$z];
			  $ls_nivel=$io_report->dts_reporte->data["nivel"][$z];
			  $ld_previsto=$io_report->dts_reporte->data["previsto"][$z]; 
			  $ld_aumento=$io_report->dts_reporte->data["aumento"][$z];
			  $ld_disminucion=$io_report->dts_reporte->data["disminucion"][$z];
			  $ld_devengado=$io_report->dts_reporte->data["devengado"][$z];
			  $ld_cobrado=$io_report->dts_reporte->data["cobrado"][$z];
			  $ld_cobrado_anticipado=$io_report->dts_reporte->data["cobrado_anticipado"][$z];
			  $ls_status=$io_report->dts_reporte->data["status"][$z];
			  $ld_monto_actualizado=$ld_previsto+$ld_aumento-$ld_disminucion-$ld_devengado;
			  $ld_por_cobrar=$ld_devengado-$ld_cobrado;
			 
			  if($ls_nivel==1)
			  {
			  	  $ld_total_previsto=$ld_total_previsto+$ld_previsto;
				  $ld_total_aumento=$ld_total_aumento+$ld_aumento;
				  $ld_total_disminucion=$ld_total_disminucion+$ld_disminucion;
				  $ld_total_devengado=$ld_total_devengado+$ld_devengado;
				  $ld_total_cobrado=$ld_total_cobrado+$ld_cobrado;
				  $ld_total_cobrado_anticipado=$ld_total_cobrado_anticipado+$ld_cobrado_anticipado;
				  $ld_total_monto_actualizado=$ld_total_monto_actualizado+$ld_monto_actualizado;
				  $ld_total_por_cobrar=$ld_total_por_cobrar+$ld_por_cobrar;
			  } 
			  $ld_previsto=number_format($ld_previsto,2,",",".");
			  $ld_aumento=number_format($ld_aumento,2,",",".");
			  $ld_disminucion=number_format($ld_disminucion,2,",",".");
			  $ld_devengado=number_format($ld_devengado,2,",",".");
			  $ld_cobrado=number_format($ld_cobrado,2,",",".");
			  $ld_cobrado_anticipado=number_format($ld_cobrado_anticipado,2,",",".");
			  $ld_monto_actualizado=number_format($ld_monto_actualizado,2,",",".");
			  $ld_por_cobrar=number_format($ld_por_cobrar,2,",",".");
			
			  
			$lo_hoja->write($contlineas, 0," ".$ls_spi_cuenta,$lo_dataleft);
			$lo_hoja->write($contlineas, 1," ".$ls_denominacion, $lo_dataleft);
			$lo_hoja->write($contlineas, 2, $ld_previsto,$lo_dataright);
			$lo_hoja->write($contlineas, 3,$ld_aumento, $lo_datarigth);
			$lo_hoja->write($contlineas, 4, $ld_disminucion,$lo_dataright);
			$lo_hoja->write($contlineas, 5,$ld_devengado, $lo_dataright);
			$lo_hoja->write($contlineas, 6, $ld_cobrado,$lo_dataright);
			$lo_hoja->write($contlineas, 7,$ld_cobrado_anticipado, $lo_dataright);
			$lo_hoja->write($contlineas, 8,$ld_monto_actualizado, $lo_dataright);
			$lo_hoja->write($contlineas, 9,$ld_por_cobrar, $lo_dataright);
			$contlineas++;
			 
			  
			  $la_data[$z]=array('cuenta'=>$ls_spi_cuenta,'denominacion'=>$ls_denominacion,'previsto'=>$ld_previsto,
			                      'aumento'=>$ld_aumento,'disminución'=>$ld_disminucion,'devengado'=>$ld_devengado,
								  'cobrado'=>$ld_cobrado,'cobrado_anticipado'=>$ld_cobrado_anticipado,
								  'montoactualizado'=>$ld_monto_actualizado,'porcobrar'=>$ld_por_cobrar);
			
			 $ld_previsto=str_replace('.','',$ld_previsto);
			 $ld_previsto=str_replace(',','.',$ld_previsto);		
			 $ld_aumento=str_replace('.','',$ld_aumento);
			 $ld_aumento=str_replace(',','.',$ld_aumento);		
			 $ld_disminucion=str_replace('.','',$ld_disminucion);
			 $ld_disminucion=str_replace(',','.',$ld_disminucion);		
			 $ld_monto_actualizado=str_replace('.','',$ld_monto_actualizado);
			 $ld_monto_actualizado=str_replace(',','.',$ld_monto_actualizado);
			 $ld_devengado=str_replace('.','',$ld_devengado);
			 $ld_devengado=str_replace(',','.',$ld_devengado);		
			 $ld_cobrado=str_replace('.','',$ld_cobrado);
			 $ld_cobrado=str_replace(',','.',$ld_cobrado);		
			 $ld_cobrado_anticipado=str_replace('.','',$ld_cobrado_anticipado);
			 $ld_cobrado_anticipado=str_replace(',','.',$ld_cobrado_anticipado);		
			 $ld_por_cobrar=str_replace('.','',$ld_por_cobrar);
			 $ld_por_cobrar=str_replace(',','.',$ld_por_cobrar);

			if($z==$li_tot)
			{
			  
			  $ld_total_previsto=number_format($ld_total_previsto,2,",",".");
			  $ld_total_aumento=number_format($ld_total_aumento,2,",",".");
			  $ld_total_disminucion=number_format($ld_total_disminucion,2,",",".");
			  $ld_total_devengado=number_format($ld_total_devengado,2,",",".");
			  $ld_total_cobrado=number_format($ld_total_cobrado,2,",",".");
			  $ld_total_cobrado_anticipado=number_format($ld_total_cobrado_anticipado,2,",",".");
			  $ld_total_monto_actualizado=number_format($ld_total_monto_actualizado,2,",",".");
			  $ld_total_por_cobrar=number_format($ld_total_por_cobrar,2,",",".");
			
			  	$lo_hoja->write($contlineas, 0," ",$lo_dataleft);
				$lo_hoja->write($contlineas, 1,"Total", $lo_dataleft);
				$lo_hoja->write($contlineas, 2, $ld_total_previsto,$lo_dataright);
				$lo_hoja->write($contlineas, 3,$ld_total_aumento, $lo_datarigth);
				$lo_hoja->write($contlineas, 4, $ld_total_disminucion,$lo_dataright);
				$lo_hoja->write($contlineas, 5,$ld_total_devengado, $lo_dataright);
				$lo_hoja->write($contlineas, 6, $ld_total_cobrado,$lo_dataright);
				$lo_hoja->write($contlineas, 7,$ld_total_cobrado_anticipado, $lo_dataright);
				$lo_hoja->write($contlineas, 8,$ld_total_monto_actualizado, $lo_dataright);
				$lo_hoja->write($contlineas, 9,$ld_total_por_cobrar, $lo_dataright);
				$contlineas++;
				  
			  
			  $la_data_tot[$z]=array('total'=>'<b>TOTAL</b>','previsto'=>$ld_total_previsto,'aumento'=>$ld_total_aumento,
									 'disminución'=>$ld_total_disminucion,
									 'devengado'=>$ld_total_devengado,'cobrado'=>$ld_total_cobrado,
									 'cobrado_anticipado'=>$ld_total_cobrado_anticipado,
									 'montoactualizado'=>$ld_total_monto_actualizado,
									 'porcobrar'=>$ld_total_por_cobrar);
			}//if
		}//for
		unset($la_data);
		unset($la_data_tot);			
	
	}
	
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"Acumulado_por_Cuentas.xls\"");
		header("Content-Disposition: inline; filename=\"Acumulado_por_Cuentas.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	
		unset($class_report);
		unset($io_funciones);
	
		unset($io_report);
		unset($io_funciones);
?> 