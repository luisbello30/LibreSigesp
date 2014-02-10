<?php 
    session_start();   
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}
	ini_set('memory_limit','2048M');
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
		function calcular_anos_servicioas($fecha_ingreso,$fecha_egreso)
	  {  
		  $c = date("Y",$fecha_ingreso);	   
		  $b = date("m",$fecha_ingreso);	  
		  $a = date("d",$fecha_ingreso); 	  
		  $anos = date("Y",$fecha_egreso)-$c; 
	   
			  if(date("m",$fecha_egreso)-$b > 0){
		  
			  }elseif(date("m",$fecha_egreso)-$b == 0){
		 
			  if(date("d",$fecha_egreso)-$a <= 0)
			  {		  
			     $anos = $anos-1;	  
			  }
		  
			  }else{		  
			         $anos = $anos-1;		  
			       }  
		  return $anos;	 
      } //FIN DE calcular_anos_servicioas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	$ruta = '../../';
	require_once("sigesp_scg_reporte.php");
	$io_report  = new sigesp_scg_reporte();
	require_once("../class_funciones_scg.php");
	$io_fun_scg=new class_funciones_scg();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$_GET['year'] = substr($_SESSION["la_empresa"]["periodo"],0,4);
	//$_GET["sc_cuenta"] = '111010105000000';
	
	if(!$_GET['year'] or !$_GET['sc_cuenta']){		
		print "<script language=JavaScript>";
		print "alert('Faltan parametros para generar el reporte');";
		print "close();";			
		print "</script>";	
		exit();
	}
	
	$year = $_GET['year'];
	$sc_cuenta=$_GET["cuentadesde"];
	
	$datos_reporte['sc_cuenta'] = $_GET["sc_cuenta"];
	$datos_reporte['year'] = $_GET["year"];
	
	$resul = $io_report->datos_mensual_mayor_analitico($datos_reporte);
	
	$datos_reporte['fecha_max'] = $io_report->rs_datos_mensual->fields['periodo_maximo'];
	$datos_reporte['denominacion'] = $io_report->rs_datos_mensual->fields['denominacion'];
	$mes = (substr($datos_reporte['fecha_max'],5,2));
	$mes = (integer)$mes;
	$datos_reporte['mes_max'] = $mes;
	
	if(!$datos_reporte['mes_max'] or $resul===false or !$datos_reporte['denominacion']){		
		print "<script language=JavaScript>";
		print "alert('No se ha podido obtener datos correspondientes al año o la cuenta');";
		print "close();";			
		print "</script>";	
		exit();
	}
	
	$ls_titulo="<b>Resumen Mensual</b>";
	
	$lb_valido=uf_insert_seguridad("Resumen Mensual"); // Seguridad de Reporte
	
		require_once('../../shared/tcpdf/config/lang/ita.php');
		require_once('../../shared/tcpdf/tcpdf.php');  
		//error_reporting(E_ALL);
		//set_time_limit(18000);
		
		$pdf = new TCPDF('PORTRAIT', PDF_UNIT, 'LETTER', true, 'UTF-8', true); 
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->setPrintHeader(false);
		//$pdf->setTextoFooter(utf8_encode(''));
		
		$pdf->AddPage();
		
		$margenes = $pdf->getMargins();
		$pdf->Image('../../shared/imagebank/'.$_SESSION["ls_logo"],$margenes['left'],$margenes['top'], 15, 15);
		$pdf->SetFont('helvetica', '', 8);
		$tit_rep = '<p  style="text-align:center;"><b> '.$ls_titulo.'</b></p>';
		$pdf->writeHTML(utf8_encode($tit_rep), true, false, false, false, '');
		
		$periodo = '<p  style="text-align:center;"><b> Cuenta:</b> '.$_GET['sc_cuenta'].'</p>';
		$pdf->writeHTML(utf8_encode($periodo), true, false, false, false, '');	
		
		$nomina = '<p  style="text-align:center;"><b>Denominación:</b> '.$datos_reporte['denominacion'].'</p>';
		$pdf->writeHTML($nomina, true, false, false, false, '');
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		
		$pdf->SetFont('helvetica', '', 6);
		
		$pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(200, 200, 200))); 

		$ancho[1] = 22;
		$ancho[2] = 40;
		$ancho[3] = 40;
		$ancho[4] = 40;
		$ancho[5] = 40;		
		$ancho_total = array_sum($ancho);
		$margen_tabla=1;
		
		
		function encabezado_tit($opciones=array()){		
														
							global $pdf,$margen_tabla,$ancho_total;
							$pdf->SetFillColor(255,255,255);
							$pdf->Cell($margen_tabla, 3,"", 0,0,'C',1);
							$pdf->SetFont('helvetica', 'B', 8);
							$pdf->SetTextColor(0,0,150);
							$pdf->SetFillColor(255, 255, 150);
							//Cell( $w, $h, $txt, $border,$ln,$align, $fill,$link,$stretch,$ignore_min_height)
							$pdf->Cell($ancho_total, 3,'CUENTA: '.$opciones['sc_cuenta'], 1,0,'C',1);
							$pdf->Ln();
							$pdf->SetFillColor(255, 255, 255);
							$pdf->SetTextColor(0);
							$pdf->SetFont('helvetica', '', 5);
		}
		function encabezado($opciones=array()){		
														
							global $pdf,$margen_tabla,$ancho;
							$pdf->SetFillColor(255,255,255);
							$pdf->Cell($margen_tabla, 3,"", 0,0,'C',1);
							$pdf->SetFont('helvetica', 'B', 8);
							$pdf->SetTextColor(255,255,255);
							$pdf->SetFillColor(0, 0, 150);
							
							//Cell( $w, $h, $txt, $border,$ln,$align, $fill,$link,$stretch,$ignore_min_height)							
							$pdf->Cell($ancho[1], 3,"MES", 1,0,'C',1);
							$pdf->Cell($ancho[2], 3,'SALDO ANT.', 1,0,'C',1);							
							$pdf->Cell($ancho[3], 3,"DEBE", 1,0,'C',1);
							$pdf->Cell($ancho[4], 3,'HABER', 1,0,'C',1);	
							$pdf->Cell($ancho[5], 3,'SALDO ACTUAL', 1,0,'C',1);																	
							$pdf->Ln();
							
							$pdf->SetFillColor(255, 255, 255);
							$pdf->SetTextColor(0);
							$pdf->SetFont('helvetica', '', 5);
		}
		
		function fila($datos=array()){		
														
							global $pdf,$io_fun_nomina,$margen_tabla,$ancho;
							$pdf->SetFillColor(255,255,255);
							$pdf->Cell($margen_tabla, 3,"", 0,0,'C',1);
							$pdf->SetFont('helvetica', 'B', 8);
							$pdf->SetFillColor(255, 255, 255);
							$pdf->SetTextColor(0);
							
							$pdf->Cell($ancho[1], 3,$datos['nommes'], 1,0,'C',1); 
							$pdf->Cell($ancho[2], 3,number_format($datos['saldo_ant'],2,",","."), 1,0,'R',1);
							$pdf->Cell($ancho[3], 3,number_format($datos['debe'],2,",","."), 1,0,'R',1);					
							$pdf->Cell($ancho[4], 3,number_format($datos['haber'],2,",","."), 1,0,'R',1);
							$saldo_actual = $datos['saldo_ant'] + ($datos['debe'] - $datos['haber']);
							$pdf->Cell($ancho[5], 3,number_format($saldo_actual,2,",","."), 1,0,'R',1);											
							$pdf->Ln();
							
		}
		
		function totales($datos=array()){		
														
							global $pdf,$margen_tabla,$ancho_total,$ancho;
							$pdf->SetFillColor(255,255,255);
							$pdf->Cell($margen_tabla, 3,"", 0,0,'C',1);
							$pdf->SetFont('helvetica', 'B', 8);
							$pdf->SetTextColor(0,0,150);
							$pdf->SetFillColor(255, 255, 150);
							
							$pdf->Cell($ancho[1] + $ancho[2], 3,'Totales: ', 1,0,'R',1);						
							$pdf->Cell($ancho[3], 3,number_format($datos['tot_debe'],2,",","."), 1,0,'R',1);					
							$pdf->Cell($ancho[4], 3,number_format($datos['tot_haber'],2,",","."), 1,0,'R',1);							
							$pdf->Cell($ancho[5], 3,number_format($datos['tot_saldo'],2,",","."), 1,0,'R',1);											
							$pdf->Ln();
							
		}
		
		
		encabezado_tit($datos_reporte);
		encabezado();
		$tot_debe = 0;
		$tot_haber = 0;
				
		for($mes=1; $mes<=$datos_reporte['mes_max']; $mes++){		
				switch($mes){
					case 1:
						$datos_reporte['mes'] = '01';
						$datos_reporte['nommes'] = 'enero';
					break;
					
					case 2:
						$datos_reporte['mes'] = '02';
						$datos_reporte['nommes'] = 'febrero';
					break;
					
					case 3:
						$datos_reporte['mes'] = '03';
						$datos_reporte['nommes'] = 'marzo';
					break;
					
					case 4:
						$datos_reporte['mes'] = '04';
						$datos_reporte['nommes'] = 'abril';
					break;
					
					case 5:
						$datos_reporte['mes'] = '05';
						$datos_reporte['nommes'] = 'mayo';
					break;
					
					case 6:
						$datos_reporte['mes'] = '06';
						$datos_reporte['nommes'] = 'junio';
					break;
					
					case 7:
						$datos_reporte['mes'] = '07';
						$datos_reporte['nommes'] = 'julio';
					break;
					
					case 8:
						$datos_reporte['mes'] = '08';
						$datos_reporte['nommes'] = 'agosto';
					break;
					
					case 9:
						$datos_reporte['mes'] = '09';
						$datos_reporte['nommes'] = 'septiembre';
					break;
					
					case 10:
						$datos_reporte['mes'] = '10';
						$datos_reporte['nommes'] = 'octubre';
					break;
					
					case 11:
						$datos_reporte['mes'] = '11';
						$datos_reporte['nommes'] = 'noviembre';
					break;
					
					case 12:
						$datos_reporte['mes'] = '12';
						$datos_reporte['nommes'] = 'diciembre';
					break;
				}
				
				
				$io_report->uf_resumen_mensual_mayor_analitico($datos_reporte);
										
				$datos=array();
				$datos=$io_report->rs_resumen->fields;
				$datos['mes'] = $datos_reporte['mes'];
				$datos['nommes'] = $datos_reporte['nommes'];
				
				$tot_debe += $datos['debe'];
				$tot_haber += $datos['haber'];
				$tot_saldo = $datos['saldo_ant'] + ($datos['debe'] - $datos['haber']);
				
				fila($datos);						
		}
		
		$datos_tot['tot_debe'] = $tot_debe;
		$datos_tot['tot_haber'] = $tot_haber;
		$datos_tot['tot_saldo'] = $tot_saldo;
		totales($datos_tot);
		
		$pdf->Ln();
		$pdf->Ln();		
		
		$pdf->Output('resumen_mensual.pdf', 'I');

?>