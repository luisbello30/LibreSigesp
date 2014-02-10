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



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

           //////////  F U N C I O N E S    P A R A    E L    A C U M U L A D O    P O R    C U E N T A S  //////////
		   //////////                       E N    F O R M A T O    H T M L   							   //////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function uf_print_cabecera_acumulado_html(&$tbl)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_cabecera_acumulado_html
	//		    Acess: private
	//    Description: funci�n que imprime el detalle
	//	   Creado Por: Victor Mendoza
	// Fecha Creaci�n: 10/08/09
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$tbl = <<<EOD
<table border="" cellpadding="1" cellspacing="1" style="font-size:8pt">
	<thead border="1" cellpadding="1" cellspacing="1">
	 <tr>
	 	<td width="1620" align="left"></td>
	 </tr>
	 <tr>
		  <td width="80" align="center"><b>Cuenta</b></td>
		  <td width="100" align="center"><b>Denominacion</b></td>
		  <td width="70" align="center"><b>Asignado</b></td>
		  <td width="70" align="center"><b>Aumento</b></td>
		  <td width="70" align="center"><b>Disminucion</b></td>
		  <td width="70" align="center"><b>Monto Actualizado</b></td>
		  <td width="70" align="center"><b>Pre Comprometido</b></td>
		  <td width="70" align="center"><b>Comprometido</b></td>
		  <td width="70" align="center"><b>Saldo por Comprometer</b></td>
		  <td width="70" align="center"><b>Causado</b></td>
		  <td width="70" align="center"><b>Pagado</b></td>
		  <td width="70" align="center"><b>Por Pagar</b></td>
	 </tr>
	 <tr>
	 	<td width="1620" align="left">____________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________</td>
	 </tr>
	</thead>

EOD;

}

//------------------------------------------------------------------------------------------------------------------------------

	function uf_print_detalle_acumulado2_html($la_data,&$tbl)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle_acumulado2_html
	//		    Acess: private
	//	    Arguments: la_data // arreglo de informaci�n
	//    Description: funci�n que imprime el detalle
	//	   Creado Por: Victor Mendoza
	// Fecha Creaci�n: 10/08/2009
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	foreach($la_data as $row)
	{
		$tbl .= '
		   <tr>
				<td width="80" align="center"> '.$row[0].' </td>'.
				'<td width="100">'.$row[1].'</td>'.
				'<td width="70" align="right">'.$row[2].'</td>'.
				'<td width="70" align="right">'.$row[3].'</td>'.
				'<td width="70" align="right">'.$row[4].'</td>'.
				'<td width="70" align="right">'.$row[5].'</td>'.
				'<td width="70" align="right">'.$row[6].'</td>'.
				'<td width="70" align="right">'.$row[7].'</td>'.
				'<td width="70" align="right">'.$row[8].'</td>'.
				'<td width="70" align="right">'.$row[9].'</td>'.
				'<td width="70" align="right">'.$row[10].'</td>'.
				'<td width="70" align="right">'.$row[11].'</td>'.
		   ' </tr>';

	}
}

//------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total_acumulado_html($la_data,&$tbl)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_total_acumulado
	//		    Acess: private
	//	    Arguments: la_data // arreglo de informaci�n
	//    Description: funci�n que imprime el detalle
	//	   Creado Por: Victor Mendoza
	// Fecha Creaci�n: 10/08/2009
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		foreach($la_data as $row)
		{
			$tbl .= '
			   <tr style="font-style=bold;">'.
			   		'<td width="80" align="center"> . </td>'.
					'<td width="100" align="center"> <b> '.$row[0].' </b> </td>'.
					'<td width="70" align="right"> <b> '.$row[1].' </b> </td>'.
					'<td width="70" align="right"> <b> '.$row[2].' </b> </td>'.
					'<td width="70" align="right"> <b> '.$row[3].' </b> </td>'.
					'<td width="70" align="right"> <b> '.$row[4].' </b> </td>'.
					'<td width="70" align="right"> <b> '.$row[5].' </b> </td>'.
					'<td width="70" align="right"> <b> '.$row[6].' </b> </td>'.
					'<td width="70" align="right"> <b> '.$row[7].' </b> </td>'.
					'<td width="70" align="right"> <b> '.$row[8].' </b> </td>'.
					'<td width="70" align="right"> <b> '.$row[9].' </b> </td>'.
					'<td width="70" align="right"> <b> '.$row[10].' </b> </td>'.
			   '</tr>';
		}
	}

//------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------
		require_once("../class_folder/tcpdf/tcpdf.php");
		require_once ("sigesp_spg_class_tcpdf.php");
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report = new sigesp_spg_funciones_reportes();
		require_once("../../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();
		require_once("../../shared/class_folder/class_fecha.php");
		$io_fecha = new class_fecha();
		$ls_nombrearchivo="acumulado_por_cuentas.txt";
		$lo_archivo=@fopen("$ls_nombrearchivo","a+");
//--------------------------------------------------  Par�metros para Filtar el Reporte  --------------------------------------
		$ldt_periodo        = $_SESSION["la_empresa"]["periodo"];
		$li_ano             = substr($ldt_periodo,0,4);
		$li_estmodest       = $_SESSION["la_empresa"]["estmodest"];
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
//-----------------------------------------------------------------------------------------------------------------------------
		global $ls_tipoformato;
		global $la_data_tot_bsf;
		global $la_data_tot;
		require_once("sigesp_spg_reporte.php");
		$io_report = new sigesp_spg_reporte();

		$li_candeccon = $_SESSION["la_empresa"]["candeccon"];
		$li_tipconmon = $_SESSION["la_empresa"]["tipconmon"];
		$li_redconmon = $_SESSION["la_empresa"]["redconmon"];
//------------------------------------------------------------------------------------------------------------------------------
		if($li_estmodest==1)
		{
			$ls_codestpro4_min =  "0000000000000000000000000";
			$ls_codestpro5_min =  "0000000000000000000000000";
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

		$ls_cmbmesdes = $_GET["cmbmesdes"];
		$ldt_fecini=$li_ano."-".$ls_cmbmesdes."-01";
		$ldt_fecini_rep="01/".$ls_cmbmesdes."/".$li_ano;
		$ls_cmbmeshas = $_GET["cmbmeshas"];
		$ls_mes=$ls_cmbmeshas;
		$ls_ano=$li_ano;
		$fecfin=$io_fecha->uf_last_day($ls_mes,$ls_ano);
		$ldt_fecfin=$io_funciones->uf_convertirdatetobd($fecfin);

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
		$ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
		$ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h;
		if($li_estmodest==1)
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
				print(" alert('No hay cuentas presupuestraias');");
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
				print(" alert('No hay cuentas presupuestraias');");
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
		/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
		$ls_desc_event="Solicitud de Reporte Acumulado por Cuentas desde la fecha ".$ldt_fecini_rep." hasta ".$fecfin." ,Desde la programatica ".$ls_programatica_desde."  hasta ".$ls_programatica_hasta;
		$io_function_report->uf_load_seguridad_reporte("SPG","sigesp_spg_r_acum_x_cuentas.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////
	//----------------------------------------------------  Par�metros del encabezado  ---------------------------------------------
		$ls_titulo=" ACUMULADO POR CUENTAS DESDE FECHA  ".$ldt_fecini_rep."  HASTA  ".$fecfin;
		$ls_titulo1=" DESDE LA PROGRAMATICA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1;
    //------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
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
	$lb_valido=$io_report->uf_spg_reporte_acumulado_cuentas2($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
	                                                        $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
	                                                        $ls_codestpro4h,$ls_codestpro5h,$ldt_fecini,$ldt_fecfin,$ls_cmbnivel,
															$lb_subniv,$ai_MenorNivel,$ls_cuentades,$ls_cuentahas,
															$ls_codfuefindes,$ls_codfuefinhas,$ls_estclades,$ls_estclahas,$rs_data);
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');");
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		error_reporting(E_ALL);
		set_time_limit(1800);
		error_reporting(E_ALL);
		set_time_limit(3600);
		//$io_tcpdf= new sigesp_spg_class_tcpdf ("L", PDF_UNIT, "legal", true);

		$io_tcpdf= new TCPDF ("L", PDF_UNIT, "legal", true);

		$io_tcpdf->AliasNbPages();

		$ls_mensaje = str_repeat(' ',50).$ls_titulo;
		$ls_mensaje2 = str_repeat(' ',60).$ls_titulo1.str_repeat(' ',70).date("d/m/Y").' '.date("h:i a").'-'.$_SESSION["ls_database"];

		$io_tcpdf->SetHeaderData($_SESSION["ls_logo"],30, $ls_mensaje, $ls_mensaje2);
		$io_tcpdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 9));
		$io_tcpdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$io_tcpdf->SetMargins(3, 15,3);
		$io_tcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		//	$io_tcpdf->setTableHeader(true);
		$io_tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$io_tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$io_tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$io_tcpdf->AliasNbPages();
		$io_tcpdf->AddPage();
		$io_tcpdf->SetFont("helvetica","",8);

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
		$ld_aumento=0;
	    $ld_disminucion=0;
	    $ld_precompromiso=0;
	    $ld_compromiso=0;
	    $ld_causado=0;
	    $ld_pagado=0;
		$ld_monto_actualizado=0;
  	    $ld_saldo_comprometer=0;
		$ld_por_paga=0;
		$lb_valido2=false;
		$li_tot=$rs_data->RecordCount();
		$z=0;
		while(!$rs_data->EOF)
		{
			//var_dump($rs_data->fields);
			//die();
			   $ls_spg_cuenta=$rs_data->fields["spg_cuenta"];
			  $ls_status=$rs_data->fields["status"];
                          $ls_denominacion=utf8_encode(trim($rs_data->fields["denominacion"]));
			  $ls_nivel=$rs_data->fields["nivel"];
			   $ld_asignado=$rs_data->fields["asignado"];
			  $lb_valido2=$io_report->uf_spg_reporte_detalle_acumulado_cuentas($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
			                                                                   $ls_codestpro4,$ls_codestpro5,
																			   $ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
	                                                                           $ls_codestpro4h,$ls_codestpro5h,
																			   $ls_estclades,$ls_estclahas,$ls_spg_cuenta,
															                   $ldt_fecfin,$rs_data2);

			  while((!$rs_data2->EOF)&&($lb_valido2))
			  {
				
                                $ld_aumento=$ld_aumento+$rs_data2->fields["aumento"];
                                  
				  $ld_disminucion=$ld_disminucion+$rs_data2->fields["disminucion"];
				  $ld_precompromiso=$ld_precompromiso+$rs_data2->fields["precompromiso"];
				  $ld_compromiso=$ld_compromiso+$rs_data2->fields["compromiso"];
				  $ld_causado=$ld_causado+$rs_data2->fields["causado"];
				  $ld_pagado=$ld_pagado+$rs_data2->fields["pagado"];
				  $ld_monto_actualizado=$ld_asignado+$ld_aumento-$ld_disminucion;
				  $ld_saldo_comprometer=$ld_asignado+$ld_aumento-$ld_disminucion-$ld_precompromiso-$ld_compromiso;
				  $ld_por_paga=$ld_causado-$ld_pagado;
			  	  $rs_data2->MoveNext();
			  }

			   if($ls_nivel==1)
			   {
				  $ld_total_asignado=$ld_total_asignado+$ld_asignado;
				  $ld_total_aumento=$ld_total_aumento+$ld_aumento;
				  $ld_total_disminucion=$ld_total_disminucion+$ld_disminucion;
				  $ld_total_monto_actualizado=$ld_total_monto_actualizado+$ld_monto_actualizado;
				  $ld_total_precompromiso=$ld_total_precompromiso+$ld_precompromiso;
				  $ld_total_compromiso=$ld_total_compromiso+$ld_compromiso;
				  $ld_total_saldo_comprometer=$ld_total_saldo_comprometer+$ld_saldo_comprometer;
				  $ld_total_causado=$ld_total_causado+$ld_causado;

				  $ld_total_pagado=$ld_total_pagado+$ld_pagado;
				  $ld_total_por_paga=$ld_total_por_paga+$ld_por_paga;
			  }

			  $ls_spg_cuenta=trim($ls_spg_cuenta);
			  $la_data[$z]=array($ls_spg_cuenta,utf8_encode($ls_denominacion),number_format($ld_asignado,2,",","."),
			                     number_format($ld_aumento,2,",","."),number_format($ld_disminucion,2,",","."),
								 number_format($ld_monto_actualizado,2,",","."),number_format($ld_precompromiso,2,",","."),
								 number_format($ld_compromiso,2,",","."),number_format($ld_saldo_comprometer,2,",","."),
								 number_format($ld_causado,2,",","."),number_format($ld_pagado,2,",","."),
							     number_format($ld_por_paga,2,",","."),$ls_status);

			         /*/ for ($i=$z+1;$i<=100;$i++)
					  {
						   $la_data[$i]=array($ls_spg_cuenta,utf8_encode($ls_denominacion),number_format($ld_asignado,2,",","."),
			                     number_format($ld_aumento,2,",","."),number_format($ld_disminucion,2,",","."),
								 number_format($ld_monto_actualizado,2,",","."),number_format($ld_precompromiso,2,",","."),
								 number_format($ld_compromiso,2,",","."),number_format($ld_saldo_comprometer,2,",","."),
								 number_format($ld_causado,2,",","."),number_format($ld_pagado,2,",","."),
							     number_format($ld_por_paga,2,",","."));
					  }/*/

			 $ls_cadena=$ls_spg_cuenta."/".$ls_denominacion."/".number_format($ld_asignado,2,",",".")."/".number_format($ld_aumento,2,",",".")."/".number_format($ld_disminucion,2,",",".")."/".number_format($ld_monto_actualizado,2,",",".")."/".number_format($ld_precompromiso,2,",",".")."/".number_format($ld_compromiso,2,",",".")."/".number_format($ld_saldo_comprometer,2,",",".")."/".number_format($ld_saldo_comprometer,2,",",".")."/".number_format($ld_causado,2,",",".")."/".number_format($ld_pagado,2,",",".")."/".number_format($ld_por_paga,2,",",".")."\r\n";
			 if ($lo_archivo)
			 {
				@fwrite($lo_archivo,$ls_cadena);
			 }
			 $ld_aumento=0;
			 $ld_disminucion=0;
			 $ld_precompromiso=0;
			 $ld_compromiso=0;
			 $ld_causado=0;
			 $ld_pagado=0;
			 $ld_monto_actualizado=0;
			 $ld_saldo_comprometer=0;
			 $ld_por_paga=0;

			if($z==($li_tot-1))
			{
				  $ld_total_asignado=number_format($ld_total_asignado,2,",",".");
				  $ld_total_aumento=number_format($ld_total_aumento,2,",",".");
				  $ld_total_disminucion=number_format($ld_total_disminucion,2,",",".");
				  $ld_total_monto_actualizado=number_format($ld_total_monto_actualizado,2,",",".");
				  $ld_total_precompromiso=number_format($ld_total_precompromiso,2,",",".");
				  $ld_total_compromiso=number_format($ld_total_compromiso,2,",",".");
				  $ld_total_saldo_comprometer=number_format($ld_total_saldo_comprometer,2,",",".");
				  $ld_total_causado=number_format($ld_total_causado,2,",",".");
				  $ld_total_pagado=number_format($ld_total_pagado,2,",",".");
				  $ld_total_por_paga=number_format($ld_total_por_paga,2,",",".");

				  $la_data_tot[$z]=array('TOTAL Bs.',$ld_total_asignado,$ld_total_aumento,$ld_total_disminucion,
				  						$ld_total_monto_actualizado,$ld_total_precompromiso,$ld_total_compromiso,
										$ld_total_saldo_comprometer,$ld_total_causado,$ld_total_pagado,$ld_total_por_paga);
			}//if
			  $rs_data->MoveNext();
			  $z=$z+1;
		}//fin del while
                 //die();
		if($lb_valido2)
		{
			$tbl='';
			uf_print_cabecera_acumulado_html(&$tbl);
			uf_print_detalle_acumulado2_html($la_data,&$tbl); // Imprimimos el detalle
			uf_print_total_acumulado_html($la_data_tot,&$tbl);//Bs
			
			//$io_tcpdf->SetFont("helvetica","B",6); 
			unset($la_data);
			unset($la_data_tot);

			$tbl .= '</table>';
			$io_tcpdf->writeHTML($tbl, true, false, false, false, '');
			$io_tcpdf->Output("sigesp_spg_rpp_acum_x_cuenta_abae.pdf", "I");
			unset($io_tcpdf);
		}
		else
		{
				print("<script language=JavaScript>");
				print(" alert('No hay nada que reportar');");
				print(" close();");
				print("</script>");
		 }
		}


	unset($io_report);
	unset($io_funciones);
	unset($io_function_report);
?>
