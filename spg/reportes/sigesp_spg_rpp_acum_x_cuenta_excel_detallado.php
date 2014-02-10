<?php
    session_start();   
	ini_set('memory_limit','256M');
	ini_set('max_execution_time ','0');
   //--------------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
   // para crear el libro excel
		require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo =  tempnam("/tmp", "spg_acumulado_x_cuentas.xls");
		$lo_libro = &new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	
   //------------------------------------------------------------------------------------------------------------------------------
		require_once("sigesp_spg_funciones_reportes.php");
		require_once("sigesp_spg_reporte.php");
		require_once("../../shared/class_folder/class_funciones.php");
		require_once("../../shared/class_folder/class_fecha.php");
		$io_function_report = new sigesp_spg_funciones_reportes();
		$io_report          = new sigesp_spg_reporte();
		$io_funciones       = new class_funciones();			
		$io_fecha           = new class_fecha();
		$ls_tipoformato     = $_GET["tipoformato"];
	    $ls_estclades       = $_GET["estclades"];
	    $ls_estclahas       = $_GET["estclahas"];
//-----------------------------------------------------------------------------------------------------------------------------
		 global $ls_tipoformato;
		 global $la_data_tot_bsf;
		 global $la_data_tot;
		 if($ls_tipoformato==1)
		 {
			require_once("sigesp_spg_reporte_bsf.php");
			$io_report = new sigesp_spg_reporte_bsf();
		 }
		 else
		 {
			require_once("sigesp_spg_reporte.php");
		    $io_report = new sigesp_spg_reporte();
		 }	
		 	
		 require_once("../../shared/class_folder/sigesp_c_reconvertir_monedabsf.php");
		 $io_rcbsf= new sigesp_c_reconvertir_monedabsf();
		 $li_candeccon=$_SESSION["la_empresa"]["candeccon"];
		 $li_tipconmon=$_SESSION["la_empresa"]["tipconmon"];
		 $li_redconmon=$_SESSION["la_empresa"]["redconmon"];
   //--------------------------------------------------  Parámetros para Filtar el Reporte  --------------------------------------
		$ldt_periodo        = $_SESSION["la_empresa"]["periodo"];
		$li_ano             = substr($ldt_periodo,0,4);
		$li_estmodest       = $_SESSION["la_empresa"]["estmodest"];
		$ls_codestpro1_min  = $_GET["codestpro1"];
		$ls_codestpro2_min  = $_GET["codestpro2"];
		$ls_codestpro3_min  = $_GET["codestpro3"];
		$ls_codestpro1h_max = $_GET["codestpro1h"];
		$ls_codestpro2h_max = $_GET["codestpro2h"];
		$ls_codestpro3h_max = $_GET["codestpro3h"];
		
		$ls_loncodestpro1   = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2   = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3   = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4   = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5   = $_SESSION["la_empresa"]["loncodestpro5"];
		
		
		if($li_estmodest==1)
		{
			$ls_codestpro4_min  = "0000000000000000000000000";
			$ls_codestpro5_min  = "0000000000000000000000000";
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
		elseif($li_estmodest==2)
		{
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
			    $ls_codestpro1_min  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,20);
			}
			if(($ls_codestpro2_min=='**') ||($ls_codestpro2_min==''))
			{
				$ls_codestpro2_min='';
			}
			else
			{
				$ls_codestpro2_min  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,6);
			
			}
			if(($ls_codestpro3_min=='**')||($ls_codestpro3_min==''))
			{
				$ls_codestpro3_min='';
			}
			else
			{
			
				$ls_codestpro3_min  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,3);
			}
			if(($ls_codestpro4_min=='**') ||($ls_codestpro4_min==''))
			{
				$ls_codestpro4_min='';
			}
			else
			{
				$ls_codestpro4_min  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,2);
	
			
			}
			if(($ls_codestpro5_min=='**') ||($ls_codestpro5_min==''))
			{
				$ls_codestpro5_min='';
			}else
			{
					$ls_codestpro5_min  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,2);
			}
			
			
			if(($ls_codestpro1h_max=='**')||($ls_codestpro1h_max==''))
			{
				$ls_codestpro1h_max='';
			}
			else
			{
				$ls_codestpro1h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,20);
			}
			if(($ls_codestpro2h_max=='**') ||($ls_codestpro2h_max==''))
			{
				$ls_codestpro2h_max='';
			}else
			{
				$ls_codestpro2h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,6);
			}
			if(($ls_codestpro3h_max=='**') ||($ls_codestpro3h_max==''))
			{
				$ls_codestpro3h_max='';
			}else
			{
				$ls_codestpro3h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,3);
			}
			if(($ls_codestpro4h_max=='**')  ||($ls_codestpro4h_max==''))
			{
				$ls_codestpro4h_max='';
			}else
			{
				$ls_codestpro4h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,2);
			}
			if(($ls_codestpro5h_max=='**')  || ($ls_codestpro5h_max==''))
			{
				$ls_codestpro5h_max='';
			}else
			{
				$ls_codestpro5h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,2);
			}
			
			if(($ls_codestpro1_min=="")||($ls_codestpro2_min=="")||($ls_codestpro3_min=="")||($ls_codestpro4_min=="")||($ls_codestpro5_min==""))
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
		
		$ls_cmbmesdes   = $_GET["cmbmesdes"];
		$ldt_fecini     = $li_ano."-".$ls_cmbmesdes."-01";
		$ldt_fecini_rep = "01/".$ls_cmbmesdes."/".$li_ano;
		$ls_cmbmeshas   = $_GET["cmbmeshas"];
		$ls_mes         = $ls_cmbmeshas;
		$ls_ano         = $li_ano;
		$fecfin         = $io_fecha->uf_last_day($ls_mes,$ls_ano);
		$ldt_fecfin     = $io_funciones->uf_convertirdatetobd($fecfin);
		$cmbnivel       = $_GET["cmbnivel"];
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
	    $ls_cuentades_min=$_GET["txtcuentades"];
	    $ls_cuentahas_max=$_GET["txtcuentahas"];
		if($ls_cuentades_min=="")
		{
		   if($io_function_report->uf_spg_reporte_select_min_cuenta($ls_cuentades_min))
		   {
		     $ls_cuentades=$ls_cuentades_min;
		   } 
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestarias');"); 
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentades=$ls_cuentades_min;
		}
		if($ls_cuentahas_max=="")
		{
		   if($io_function_report->uf_spg_reporte_select_max_cuenta($ls_cuentahas_max))
		   {
		     $ls_cuentahas=$ls_cuentahas_max;
		   } 
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestarias');"); 
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentahas=$ls_cuentahas_max;
		}
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
    //------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
	  $ls_codestpro1  = str_pad($ls_codestpro1_min,25,0,0);
	  $ls_codestpro2  = str_pad($ls_codestpro2_min,25,0,0);
	  $ls_codestpro3  = str_pad($ls_codestpro3_min,25,0,0);
	  $ls_codestpro4  = str_pad($ls_codestpro4_min,25,0,0);
	  $ls_codestpro5  = str_pad($ls_codestpro5_min,25,0,0);
	  $ls_codestpro1h = str_pad($ls_codestpro1h_max,25,0,0);
	  $ls_codestpro2h = str_pad($ls_codestpro2h_max,25,0,0);
	  $ls_codestpro3h = str_pad($ls_codestpro3h_max,25,0,0);
	  $ls_codestpro4h = str_pad($ls_codestpro4h_max,25,0,0);
	  $ls_codestpro5h = str_pad($ls_codestpro5h_max,25,0,0);
	  
	  // TOTALES GENERALES
	  $ld_totalgen_asignado=0;
	  $ld_totalgen_aumento=0;
	  $ld_totalgen_disminucion=0;
	  $ld_totalgen_monto_actualizado=0;
	  $ld_totalgen_precomprometido=0;
	  $ld_totalgen_comprometido=0;
	  $ld_totalgen_saldoxcomprometer=0;
	  $ld_totalgen_causado=0;
	  $ld_totalgen_pagado=0;
	  $ld_totalgen_por_pagar=0;
	  $rs_cuentas = NULL;
	  $rs_estructuras = NULL;
	  $j=0;
	  $total_estructuras = 0;
	  
	  
	  $lb_valido = $io_report->obtener_estructuras_presupuestarias($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																   $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
																   $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas,$rs_estructuras);
																 
	//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
		$ls_programatica_desde=$ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3.'-'.$ls_codestpro4.'-'.$ls_codestpro5;
		$ls_programatica_hasta=$ls_codestpro1h.'-'.$ls_codestpro2h.'-'.$ls_codestpro3h.'-'.$ls_codestpro4h.'-'.$ls_codestpro5h;
		$ls_titulo="ACUMULADO POR CUENTAS DESDE FECHA  ".$ldt_fecini_rep."  HASTA ".$fecfin." ";
		switch($_SESSION["la_empresa"]["estmodest"])
		{
		 case 2  : $ls_titulo1="DESDE LA PROGRAMATICA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1;
		 break;
		 default : $ls_titulo1="DESDE LA ESTRUCTURA PRESUPUESTARIA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1;
		}  
    //------------------------------------------------------------------------------------------------------------------------------
	
	if ($lb_valido==false) // Existe algún error ó no hay registros
	   {
		 print("<script language=JavaScript>");
		 print(" alert('No hay nada que Reportar');"); 
		 print(" close();");
		 print("</script>");
	   }
	else // Imprimimos el reporte
	   {
		/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
		$ls_desc_event="Se genero el Reporte Acumulado por Cuentas desde la fecha ".$ldt_fecini_rep." hasta ".$fecfin." ,Desde la programatica ".$ls_programatica_desde."  hasta ".$ls_programatica_hasta;
		$io_function_report->uf_load_seguridad_reporte("SPG","sigesp_spg_r_acum_x_cuentas.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////
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
		$lo_titulo_left= &$lo_libro->addformat();
		$lo_titulo_left->set_bold();
		$lo_titulo_left->set_font("Verdana");
		$lo_titulo_left->set_align('left');
		$lo_titulo_left->set_size('9');
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
		
		$lo_datacenter_bold= &$lo_libro->addformat();
		$lo_datacenter_bold->set_font("Verdana");
		$lo_datacenter_bold->set_align('center');
		$lo_datacenter_bold->set_size('9');
		$lo_datacenter_bold->set_bold();
		$lo_dataright_bold= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright_bold->set_font("Verdana");
		$lo_dataright_bold->set_align('right');
		$lo_dataright_bold->set_size('9');
		$lo_dataright_bold->set_bold();
		$lo_dataleft_bold= &$lo_libro->addformat();
		$lo_dataleft_bold->set_text_wrap();
		$lo_dataleft_bold->set_font("Verdana");
		$lo_dataleft_bold->set_align('left');
		$lo_dataleft_bold->set_size('9');
		$lo_dataleft_bold->set_bold();
		
		
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,30);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,13);
		$lo_hoja->set_column(5,7,30);
		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 3, $ls_titulo1,$lo_encabezado);
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
		if($lb_valido)
		{
		 if($rs_estructuras->RecordCount()>0)
		 {
		  $total_estructuras = $rs_estructuras->RecordCount();
		  while(!$rs_estructuras->EOF)
		  {
			$thisPageNum=$io_pdf->ezPageCount;
			$ls_codestpro1_fil = $rs_estructuras->fields["codestpro1"];
			$ls_denestpro1_fil = $rs_estructuras->fields["denestpro1"];
			$ls_codestpro2_fil = $rs_estructuras->fields["codestpro2"];
			$ls_denestpro2_fil = $rs_estructuras->fields["denestpro2"];
			$ls_codestpro3_fil = $rs_estructuras->fields["codestpro3"];
			$ls_denestpro3_fil = $rs_estructuras->fields["denestpro3"];
			$ls_codestpro4_fil = $rs_estructuras->fields["codestpro4"];
			$ls_denestpro4_fil = $rs_estructuras->fields["denestpro4"];
			$ls_codestpro5_fil = $rs_estructuras->fields["codestpro5"];
			$ls_denestpro5_fil = $rs_estructuras->fields["denestpro5"];
			$ls_estcla_fil     = $rs_estructuras->fields["estcla"];
			switch($_SESSION["la_empresa"]["estmodest"])
			{
			 case 1: $li_row++;
					 $lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro1"]),$lo_titulo_left);
					 $lo_hoja->write($li_row,1,substr($ls_codestpro1_fil,-$ls_loncodestpro1)." ",$lo_datacenter);
					 $lo_hoja->write($li_row,2,$ls_denestpro1_fil,$lo_dataleft);
					 $li_row++;
					 $lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro2"]),$lo_titulo_left);
					 $lo_hoja->write($li_row,1,substr($ls_codestpro2_fil,-$ls_loncodestpro2)." ",$lo_datacenter);
					 $lo_hoja->write($li_row,2,$ls_denestpro2_fil,$lo_dataleft);
					 $li_row++;
					 $lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro3"]),$lo_titulo_left);
					 $lo_hoja->write($li_row,1,substr($ls_codestpro3_fil,-$ls_loncodestpro3)." ",$lo_datacenter);
					 $lo_hoja->write($li_row,2,$ls_denestpro3_fil,$lo_dataleft);
					 break;
					 
			case 2: $li_row++;
					 $lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro1"]),$lo_titulo_left);
					 $lo_hoja->write($li_row,1,substr($ls_codestpro1_fil,-$ls_loncodestpro1)." ",$lo_datacenter);
					 $lo_hoja->write($li_row,2,$ls_denestpro1_fil,$lo_dataleft);
					 $li_row++;
					 $lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro2"]),$lo_titulo_left);
					 $lo_hoja->write($li_row,1,substr($ls_codestpro2_fil,-$ls_loncodestpro2)." ",$lo_datacenter);
					 $lo_hoja->write($li_row,2,$ls_denestpro2_fil,$lo_dataleft);
					 $li_row++;
					 $lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro3"]),$lo_titulo_left);
					 $lo_hoja->write($li_row,1,substr($ls_codestpro3_fil,-$ls_loncodestpro3)." ",$lo_datacenter);
					 $lo_hoja->write($li_row,2,$ls_denestpro3_fil,$lo_dataleft);
					 $li_row++;
					 $lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro4"]),$lo_titulo_left);
					 $lo_hoja->write($li_row,1,substr($ls_codestpro4_fil,-$ls_loncodestpro4)." ",$lo_datacenter);
					 $lo_hoja->write($li_row,2,$ls_denestpro4_fil,$lo_dataleft);
					 $li_row++;
					 $lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro5"]),$lo_titulo_left);
					 $lo_hoja->write($li_row,1,substr($ls_codestpro5_fil,-$ls_loncodestpro5)." ",$lo_datacenter);
					 $lo_hoja->write($li_row,2,$ls_denestpro5_fil,$lo_dataleft);
					 break;
			}
			
			
			$lb_valido=$io_report->obtener_cuentas_presupuestarias($ls_codestpro1_fil,$ls_codestpro2_fil,$ls_codestpro3_fil,$ls_codestpro4_fil,
																   $ls_codestpro5_fil,$ls_cmbnivel,$ls_cuentades_min,$ls_cuentahas_max,
																	$ls_codfuefindes,$ls_codfuefinhas,$ls_estcla_fil,$rs_cuentas);	
			if($lb_valido)
			{
			 if($rs_cuentas->RecordCount()>0)
			 {
				 // TOTALES POR ESTRUCTURA
			   $ld_total_asignado=0;
			   $ld_total_aumento=0;
			   $ld_total_disminucion=0;
			   $ld_total_monto_actualizado=0;
			   $ld_total_precomprometido=0;
			   $ld_total_comprometido=0;
			   $ld_total_saldoxcomprometer=0;
			   $ld_total_causado=0;
			   $ld_total_pagado=0;
			   $ld_total_por_pagar=0;
			   $i=0;
			   $total_cuentas = $rs_cuentas->RecordCount();
			   $ls_spg_cuenta_ant="";
			   while(!$rs_cuentas->EOF)
			   {
				  $ld_asignado=0;
				  $ld_aumento=0;
				  $ld_disminucion=0;
				  $ld_monto_actualizado=0;
				  $ld_precomprometido=0;
				  $ld_comprometido=0;
				  $ld_saldoxcomprometer=0;
				  $ld_causado=0;
				  $ld_pagado=0;
				  $ld_por_pagar=0;
				  $ls_status = "";
				  $ls_codestpro1=trim($rs_cuentas->fields["codestpro1"]); 
				  $ls_codestpro2=trim($rs_cuentas->fields["codestpro2"]);
				  $ls_codestpro3=trim($rs_cuentas->fields["codestpro3"]);
				  $ls_codestpro4=trim($rs_cuentas->fields["codestpro4"]);
				  $ls_codestpro5=trim($rs_cuentas->fields["codestpro5"]);
				  $ls_estcla=trim($rs_cuentas->fields["estcla"]);
				  $ls_spg_cuenta = trim($rs_cuentas->fields["spg_cuenta"]);
				  $ls_denominacion = trim($rs_cuentas->fields["denominacion"]);
				  $ld_asignado = $rs_cuentas->fields["asignado"];
				  $ls_status = trim($rs_cuentas->fields["status"]);
				  $li_nivel = $rs_cuentas->fields["nivel"];
				  $lb_valido=$io_report->uf_spg_ejecutado_cuenta($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																 $ls_codestpro5,$ls_estcla,$ls_spg_cuenta,$ldt_fecini,$ldt_fecfin,$ld_precomprometido,
																 $ld_comprometido,$ld_causado,$ld_pagado,$ld_aumento,$ld_disminucion);
				  $ld_monto_actualizado = $ld_asignado + $ld_aumento - $ld_disminucion;
				  $ld_saldoxcomprometer = $ld_monto_actualizado - $ld_comprometido - $ld_precomprometido;
				  $ld_por_pagar = $ld_causado - $ld_pagado;				  
						 
				  if(($ls_cmbnivel==1)||($ls_cmbnivel==2)||($ls_cmbnivel==3))
				  {
					  if($li_nivel == $ls_cmbnivel)
					  {
					   $ld_total_asignado += $ld_asignado;
					   $ld_total_aumento  += $ld_aumento;
					   $ld_total_disminucion += $ld_disminucion;
					   $ld_total_monto_actualizado += $ld_monto_actualizado;
					   $ld_total_precomprometido += $ld_precomprometido;
					   $ld_total_comprometido += $ld_comprometido;
					   $ld_total_saldoxcomprometer += $ld_saldoxcomprometer;
					   $ld_total_causado += $ld_causado;
					   $ld_total_pagado += $ld_pagado;
					   $ld_total_por_pagar += $ld_por_pagar;
					  }
				  }
				  else
				  {
					  if($ls_status == "C")
					  {
					   $ld_total_asignado += $ld_asignado;
					   $ld_total_aumento  += $ld_aumento;
					   $ld_total_disminucion += $ld_disminucion;
					   $ld_total_monto_actualizado += $ld_monto_actualizado;
					   $ld_total_precomprometido += $ld_precomprometido;
					   $ld_total_comprometido += $ld_comprometido;
					   $ld_total_saldoxcomprometer += $ld_saldoxcomprometer;
					   $ld_total_causado += $ld_causado;
					   $ld_total_pagado += $ld_pagado;
					   $ld_total_por_pagar += $ld_por_pagar;
					  }
				  } 
						
				  if($ls_spg_cuenta_ant=="")
				  {
					$li_row++;
					$li_row++;
					$ls_spg_cuenta_ant=$ls_spg_cuenta;
					$lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
					$lo_hoja->write($li_row, 1, "Denominacion",$lo_titulo);
					$lo_hoja->write($li_row, 2, "Asignado",$lo_titulo);
					$lo_hoja->write($li_row, 3, "Aumento",$lo_titulo);
					$lo_hoja->write($li_row, 4, "Disminucion",$lo_titulo);
					$lo_hoja->write($li_row, 5, "Monto Actualizado",$lo_titulo);
					$lo_hoja->write($li_row, 6, "Pre-Comprometido",$lo_titulo);
					$lo_hoja->write($li_row, 7, "Comprometido",$lo_titulo);
					$lo_hoja->write($li_row, 8, "Saldo Por Comprometer",$lo_titulo);
					$lo_hoja->write($li_row, 9, "Causado",$lo_titulo);
					$lo_hoja->write($li_row, 10,"Pagado",$lo_titulo);
					$lo_hoja->write($li_row, 11,"Por Pagar",$lo_titulo);
				 }
				 $li_row++;
				 $lo_hoja->write($li_row, 0, $ls_spg_cuenta,$lo_datacenter);
				 $lo_hoja->write($li_row, 1, $ls_denominacion." ",$lo_dataleft);
				 $lo_hoja->write($li_row, 2, $ld_asignado,$lo_dataright);
				 $lo_hoja->write($li_row, 3, $ld_aumento,$lo_dataright);
				 $lo_hoja->write($li_row, 4, $ld_disminucion,$lo_dataright);
				 $lo_hoja->write($li_row, 5, $ld_monto_actualizado,$lo_dataright);
				 $lo_hoja->write($li_row, 6, $ld_precomprometido,$lo_dataright);
				 $lo_hoja->write($li_row, 7, $ld_comprometido,$lo_dataright);
				 $lo_hoja->write($li_row, 8, $ld_saldoxcomprometer,$lo_dataright);
				 $lo_hoja->write($li_row, 9, $ld_causado,$lo_dataright);
				 $lo_hoja->write($li_row, 10,$ld_pagado,$lo_dataright);
				 $lo_hoja->write($li_row, 11,$ld_por_pagar,$lo_dataright);		
				 $i++;	  
				 $ls_spg_cuenta_ant=$ls_spg_cuenta;
				 $rs_cuentas->MoveNext();
			 }// fin del while estructura
			
						if($total_cuentas>0)
						{ 
						 if($i==$total_cuentas)
						 {
							
						  $ld_totalgen_asignado += $ld_total_asignado;
						  $ld_totalgen_aumento += $ld_total_aumento;
						  $ld_totalgen_disminucion += $ld_total_disminucion;
						  $ld_totalgen_monto_actualizado += $ld_total_monto_actualizado;
						  $ld_totalgen_precomprometido += $ld_total_precomprometido;
						  $ld_totalgen_comprometido += $ld_total_comprometido;
						  $ld_totalgen_saldoxcomprometer += $ld_total_saldoxcomprometer;
						  $ld_totalgen_causado += $ld_total_causado;
						  $ld_totalgen_pagado += $ld_total_pagado;
						  $ld_totalgen_por_pagar += $ld_total_por_pagar;
						  
						  $ls_estructura = "";
						  if($_SESSION["la_empresa"]["estmodest"] == 1)
						  {
						   $ls_estructura = trim(substr($ls_codestpro1_fil,-$ls_loncodestpro1))."-".trim(substr($ls_codestpro2_fil,-$ls_loncodestpro2))."-".trim(substr($ls_codestpro3_fil,-$ls_loncodestpro3));
						  }
						  elseif($_SESSION["la_empresa"]["estmodest"] == 2)
						  {
						   $ls_estructura = trim(substr($ls_codestpro1_fil,-$ls_loncodestpro1))."-".trim(substr($ls_codestpro2_fil,-$ls_loncodestpro2))."-".trim(substr($ls_codestpro3_fil,-$ls_loncodestpro3))."-".trim(substr($ls_codestpro4_fil,-$ls_loncodestpro4))."-".trim(substr($ls_codestpro5_fil,-$ls_loncodestpro5));
						  }
						  $li_row++;
						  $lo_hoja->write($li_row, 0, "TOTAL ".$ls_estructura,$lo_datacenter_bold);
						  $lo_hoja->write($li_row, 2, $ld_total_asignado,$lo_dataright_bold);
						  $lo_hoja->write($li_row, 3, $ld_total_aumento,$lo_dataright_bold);
						  $lo_hoja->write($li_row, 4, $ld_total_disminucion,$lo_dataright_bold);
						  $lo_hoja->write($li_row, 5, $ld_total_monto_actualizado,$lo_dataright_bold);
						  $lo_hoja->write($li_row, 6, $ld_total_precomprometido,$lo_dataright_bold);
						  $lo_hoja->write($li_row, 7, $ld_total_comprometido,$lo_dataright_bold);
						  $lo_hoja->write($li_row, 8, $ld_total_saldoxcomprometer,$lo_dataright_bold);
						  $lo_hoja->write($li_row, 9, $ld_total_causado,$lo_dataright_bold);
						  $lo_hoja->write($li_row, 10,$ld_total_pagado,$lo_dataright_bold);
						  $lo_hoja->write($li_row, 11,$ld_total_por_pagar,$lo_dataright_bold);
						  $li_row++;
						 }//if
						}
			 }
			}
		   $rs_estructuras->MoveNext();
		  }
			 
			 $li_row++;
			 $lo_hoja->write($li_row, 0, "TOTAL GENERAL",$lo_datacenter_bold);
			 $lo_hoja->write($li_row, 2, $ld_totalgen_asignado,$lo_dataright_bold);
			 $lo_hoja->write($li_row, 3, $ld_totalgen_aumento,$lo_dataright_bold);
			 $lo_hoja->write($li_row, 4, $ld_totalgen_disminucion,$lo_dataright_bold);
			 $lo_hoja->write($li_row, 5, $ld_totalgen_monto_actualizado,$lo_dataright_bold);
			 $lo_hoja->write($li_row, 6, $ld_totalgen_precomprometido,$lo_dataright_bold);
			 $lo_hoja->write($li_row, 7, $ld_totalgen_comprometido,$lo_dataright_bold);
			 $lo_hoja->write($li_row, 8, $ld_totalgen_saldoxcomprometer,$lo_dataright_bold);
			 $lo_hoja->write($li_row, 9, $ld_totalgen_causado,$lo_dataright_bold);
			 $lo_hoja->write($li_row, 10,$ld_totalgen_pagado,$lo_dataright_bold);
			 $lo_hoja->write($li_row, 11,$ld_totalgen_por_pagar,$lo_dataright_bold);
		 }
	    }
	
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"spg_acumulado_x_cuentas_detallado.xls\"");
		header("Content-Disposition: inline; filename=\"spg_acumulado_x_cuentas_detallado.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
?> 