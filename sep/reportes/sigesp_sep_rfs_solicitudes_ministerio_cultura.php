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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_sep;
		
		$ls_descripcion="Gener&aacute; el Reporte ".$as_titulo;
		$lb_valido=$io_fun_sep->uf_load_seguridad_reporte("SEP","sigesp_sep_p_solicitud.php",$ls_descripcion);
		return $lb_valido;
	}

	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_sep.php");
	$io_fun_sep=new class_funciones_sep();
	$ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
	if($ls_estmodest==1)
	{
		$ls_titcuentas="Estructura Presupuestaria";
	}
	else
	{
		$ls_titcuentas="Estructura Programatica";
	}
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	 $ls_numsol=$io_fun_sep->uf_obtenervalor_get("numsol","");
	 $ls_tipoformato=$io_fun_sep->uf_obtenervalor_get("tipoformato",0);
	//--------------------------------------------------------------------------------------------------------------------------------
	 global $ls_tipoformato;
	 if($ls_tipoformato==1)
	 {
		require_once("sigesp_sep_class_reportbsf.php");
		$io_report=new sigesp_sep_class_reportbsf();
	 }
	 else
	 {
		require_once("sigesp_sep_class_report.php");
		$io_report=new sigesp_sep_class_report();
  	 }	
	 //Instancio a la clase de conversi�n de numeros a letras.
	 include("../../shared/class_folder/class_numero_a_letra.php");
	 $numalet= new class_numero_a_letra();
	 //imprime numero con los valore por defecto
	 //cambia a minusculas
	 $numalet->setMayusculas(1);
	 //cambia a femenino
	 $numalet->setGenero(1);
	 //cambia moneda
	 if($ls_tipoformato==1)
	 {
		 $numalet->setMoneda("Bolivares Fuerte");
	     $ls_moneda="EN Bs.F.";
	 }
	 else
	 {
		 $numalet->setMoneda("Bolivares");
	     $ls_moneda="EN Bs.";
  	 }
		
	 //cambia prefijo
	 $numalet->setPrefijo("***");
	 //cambia sufijo
	 $numalet->setSufijo("***");
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	/***********************************************************************/
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_select_solicitud($ls_numsol); // Cargar el DS con los datos del reporte
		if($lb_valido==false) // Existe alg�n error � no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{
		//	global $ls_tipoformato;
		if($ls_tipoformato==1)
		{
		   $ls_titsub="Bs.F.";
		   $ls_titcar="Bs.F.";
		   $ls_tittot="Bs.F.";
		}
		else
		{
		   $ls_titsub="Bs.";
		   $ls_titcar="Bs.";
		   $ls_tittot="Bs.";
		}
			$datos_reporte = Array();
			$sub_total = 0; $cargos = 0;
			$li_totrow=$io_report->DS->getRowCount("numsol");
			
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numsol=$io_report->DS->data["numsol"][$li_i];
				$ls_dentipsol=$io_report->DS->data["dentipsol"][$li_i];
				$ls_denuniadm=$io_report->DS->data["denuniadm"][$li_i];
				$ls_denfuefin=$io_report->DS->data["denfuefin"][$li_i];
				$ls_codpro=$io_report->DS->data["cod_pro"][$li_i];
                                $ls_rifbenepro=$io_report->DS->data["rif"][$li_i];
				$ls_cedbene=$io_report->DS->data["cedula_benepro"][$li_i];
				$ls_nombre=$io_report->DS->data["nombre"][$li_i];
				$ld_fecregsol=$io_report->DS->data["fecregsol"][$li_i];
				$ls_consol=$io_report->DS->data["consol"][$li_i];
				$li_monto=$io_report->DS->data["monto"][$li_i];
				$li_monbasimptot=$io_report->DS->data["monbasinm"][$li_i];
				$li_montotcar=$io_report->DS->data["montotcar"][$li_i];
				$li_estsol=$io_report->DS->data["estsol"][$li_i];
				$li_estapro=$io_report->DS->data["estapro"][$li_i];
				switch ($li_estsol)
				{
					case "R":
						$ls_estatus="REGISTRO";
						break;
						
					case "E":
						if($li_estapro==0)
						{
							$ls_estatus="EMITIDA";
						}
						else
						{
							$ls_estatus="EMITIDA (APROBADA)";
						}
						break;
						
					case "A":
						$ls_estatus="ANULADA";
						break;
						
					case "C":
						$ls_estatus="CONTABILIZADA";
						break;
						
					case "P":
						$ls_estatus="PROCESADA";
						break;
						
					case "D":
						$ls_estatus="DESPACHADA";
						break;
					
					case "L":
						$ls_estatus="DESPACHADA PARCIALMENTE";
						break;
				}
				$numalet->setNumero($li_monto);
				$ls_monto= $numalet->letra();
				$li_monto=number_format($li_monto,2,",",".");
				$li_monbasimptot=number_format($li_monbasimptot,2,",",".");
				$li_montotcar=number_format($li_montotcar,2,",",".");
				$ld_fecregsol=$io_funciones->uf_convertirfecmostrar($ld_fecregsol);
				if($ls_codpro!="----------")
				{
					$ls_codigo1= $ls_rifbenepro;
				}
				else
				{
					$ls_codigo1=$ls_cedbene;
				}						
				//uf_print_encabezado_pagina($ls_titulo,$ls_numsol,$ld_fecregsol,&$io_pdf);
				//uf_print_cabecera($ls_numsol,$ls_dentipsol,$ls_denuniadm,$ls_denfuefin,$ls_codigo,$ls_nombre,$ls_consol,&$io_pdf);
				$io_report->ds_detalle->reset_ds();
				$lb_valido=$io_report->uf_select_dt_solicitud($ls_numsol); // Cargar el DS con los datos del reporte
				
				if($lb_valido)
				{
					$li_totrowdet=$io_report->ds_detalle->getRowCount("codigo");
					$la_data="";
					for($li_s=1;$li_s<=$li_totrowdet;$li_s++)
					{
						$ls_codigo=$io_report->ds_detalle->data["codigo"][$li_s];
						$ls_tipo=$io_report->ds_detalle->data["tipo"][$li_s];
						$ls_denominacion=$io_report->ds_detalle->data["denominacion"][$li_s];
						$ls_unidad=$io_report->ds_detalle->data["unidad"][$li_s];
						$li_cantidad=$io_report->ds_detalle->data["cantidad"][$li_s];
						$li_cosuni=$io_report->ds_detalle->data["monpre"][$li_s];
						$li_basimp=$li_cosuni*$li_cantidad;
						$li_monart=$io_report->ds_detalle->data["monto"][$li_s];
						
						if(($ls_tipo=="B")&&($ls_unidad=="M"))
						{
							$li_unidad=$io_report->uf_select_dt_unidad($ls_codigo);
							$li_basimp=$li_cosuni*($li_cantidad*$li_unidad);
						}
						$sub_total += $li_basimp; 
						$li_monart=number_format($li_monart,2,".","");
						$li_basimp=number_format($li_basimp,2,".","");
						$li_cargos=($li_monart-$li_basimp);
						$cargos += $li_cargos;
						if($ls_unidad=="M")
						{
							$ls_unidad="MAYOR";
						}
						else
						{
							$ls_unidad="DETAL";
						}
						
						$li_cosuni=number_format($li_cosuni,2,",",".");
						$li_basimp=number_format($li_basimp,2,",",".");
						$li_monart=number_format($li_monart,2,",",".");
						$li_cargos=number_format($li_cargos,2,",",".");
						$la_data[$li_s]=array('codigo'=>$ls_codigo,'denominacion'=>$ls_denominacion,'cantidad'=>$li_cantidad,
											  'unidad'=>$ls_unidad,'cosuni'=>$li_cosuni,'baseimp'=>$li_basimp,'cargo'=>$li_cargos,'montot'=>$li_monart);
					}
					$datos_reporte['detalle']=$la_data;
					/*echo "<pre>";
					print_r($datos_reporte['detalle']);
					echo "</pre>";die();*/
					//uf_print_detalle($la_data,&$io_pdf);
					unset($la_data);
					$lb_valido=$io_report->uf_select_dt_cargos($ls_numsol); // Cargar el DS con los datos del reporte
					if($lb_valido)
					{
						$li_totrowcargos=$io_report->ds_cargos->getRowCount("codigo");
						$la_data="";
						for($li_s=1;$li_s<=$li_totrowcargos;$li_s++)
						{
							$ls_codigo=$io_report->ds_cargos->data["codcar"][$li_s];
							$ls_dencar=$io_report->ds_cargos->data["dencar"][$li_s];
							$li_monbasimp=$io_report->ds_cargos->data["monbasimp"][$li_s];
							$li_monimp=$io_report->ds_cargos->data["monimp"][$li_s];
							$li_montocar=$io_report->ds_cargos->data["monto"][$li_s];
							$li_monbasimp=number_format($li_monbasimp,2,",",".");
							$li_monimp=number_format($li_monimp,2,",",".");
							$li_montocar=number_format($li_montocar,2,",",".");
							$la_data[$li_s]=array('codigo'=>$ls_codigo,'dencar'=>$ls_dencar,'monbasimp'=>$li_monbasimp,
												  'monimp'=>$li_monimp,'monto'=>$li_montocar);
						}
						/*echo "<pre>";
						print_r($la_data);
						echo "</pre>";die();*/
						//uf_print_detalle_cargos($la_data,&$io_pdf);
						unset($la_data);
						$lb_valido=$io_report->uf_select_dt_spgcuentas($ls_numsol); // Cargar el DS con los datos del reporte
						if($lb_valido)
						{
							$li_totrowcuentas=$io_report->ds_cuentas->getRowCount("codestpro1");
							$la_data="";
							
							for($li_s=1;$li_s<=$li_totrowcuentas;$li_s++)
							{
								$ls_codestpro1=trim($io_report->ds_cuentas->data["codestpro1"][$li_s]);
								$ls_codestpro2=trim($io_report->ds_cuentas->data["codestpro2"][$li_s]);
								$ls_codestpro3=trim($io_report->ds_cuentas->data["codestpro3"][$li_s]);
								$ls_codestpro4=trim($io_report->ds_cuentas->data["codestpro4"][$li_s]);
								$ls_codestpro5=trim($io_report->ds_cuentas->data["codestpro5"][$li_s]);
								$ls_spgcuenta=$io_report->ds_cuentas->data["spg_cuenta"][$li_s];
								if($ls_estmodest==1)
								{
									$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3;
								}
								else
								{
									$ls_codestpro=$ls_codestpro1." - ".$ls_codestpro2." - ".$ls_codestpro3." - ".$ls_codestpro4." - ".$ls_codestpro5;
								}
								
								$li_montocta=$io_report->ds_cuentas->data["monto"][$li_s];
								$li_montocta=number_format($li_montocta,2,",",".");
								$la_data[$li_s]=array('codestpro'=>$ls_codestpro,'cuenta'=>$ls_spgcuenta,'monto'=>$li_montocta);
							}
							$datos_reporte['presupuesto']=$la_data;
							/*echo "<pre>";
							print_r($la_data);
							echo "</pre>";die();
							/*detalle del presupuesto

							*/
							//uf_print_detalle_cuentas($la_data,$ls_estmodest,&$io_pdf);
							unset($la_data);
						}//if
					}//if
				}//if
			}//for
		}//else
		/*uf_print_piecabecera($li_monbasimptot,$li_montotcar,$li_monto,$ls_monto,&$io_pdf);
		if($lb_valido) // Si no ocurrio ning�n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else // Si hubo alg�n error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			//print(" close();");
			print("</script>");		
		}*/
		
	}


	/******************************************************************************/
ob_start();
require_once('../../html2pdf/html2pdf.class.php');
$html2pdf = new HTML2PDF('P','Letter','es');
$html ='';
$html .='<page backtop="30mm" backleft="7mm"  backbottom="30mm" >';
//PIE DE PAGINA
$size ='font-size:9px;';
$fonfamily_css  ="font-family:Arial,Verdana,Bitstream Vera Sans,Sans,Sans-serif;";

//$_SESSION["ls_width"],$_SESSION["ls_height"]
$html.='<page_header>';
	$html.='<TABLE style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
	$html.='<TR>';
		$html.='<TD style="'.$fonfamily_css.'color:black; width:120px;text-align:center;height:50px;border:1px solid;" rowspan="2"  >
 				<IMG src="../../shared/imagebank/'.$_SESSION["ls_logo"].'" width="'.$_SESSION["ls_width"].'" height="'.$_SESSION["ls_height"].'" align="left" border="0">
			</TD>';
		
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;color:black; width:500px;text-align:center;height:50px;border:1px solid;"  rowspan="2">
 				SOLICITUD DE EJECUCION PRESUPUESTARIA
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;color:black;font-size:10px; width:115px;text-align:center;height:50px;border:1px solid;"  >
 				No. '.$ls_numsol.'
			</TD>';
	$html.='</TR>';
	$html.='<TR>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;color:black;font-size:10px; width:115px;text-align:center;height:15px;border:1px solid;"  >
 				Fecha. '.$ld_fecregsol.'
			</TD>';
	$html.='</TR>';
	$html.='<TR>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;color:black;font-size:10px; width:615px;text-align:center;height:15px;border:0px solid;" colspan="2" >
 				&nbsp;&nbsp;
			</TD>';
	$html.='</TR>';

	$html.='</table>';
	$html.='</page_header>';

$border="1";
$html.= '<page_footer>
<table style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
	$html.='<tr>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;color:black; width:340px;text-align:center;height:13px;border:'.$border.'px solid;vertical-align:middle;" colspan="2"  >
				UNIDAD USUARIA
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;color:black; width:170px;text-align:center;height:13px;border:'.$border.'px solid;vertical-align:middle;"  >
				DIRECCI&Oacute;N DE ADMINISTRACI&Oacute;N
			</TD>';
	$html.='</tr>';
	$html.='<tr>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black; width:230px;text-align:center;height:40px;border:'.$border.'px solid;vertical-align:middle;"  >
				&nbsp;&nbsp;
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black; width:230px;text-align:center;height:40px;border:'.$border.'px solid;vertical-align:middle;"  >
				&nbsp;&nbsp;
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black; width:280px;text-align:center;height:40px;border:'.$border.'px solid;vertical-align:middle;"  >
				&nbsp;&nbsp;
			</TD>';
	$html.='</tr>';
	$html.='<tr>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:8px;color:black; width:170px;text-align:center;height:10px;border:'.$border.'px solid;vertical-align:middle;"  >
				ELABORA
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:8px;color:black; width:170px;text-align:center;height:10px;border:'.$border.'px solid;vertical-align:middle;"  >
				APRUEBA
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:8px;color:black; width:170px;text-align:center;height:10px;border:'.$border.'px solid;vertical-align:middle;"  >
				FIRMA, SELLO Y FECHA
			</TD>';
	$html.='</tr>';;
$html.='</table>
</page_footer>';

$html.='<table style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
$border ="0";
	//$html.='<thead>';
	$html.='<TR>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:100px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;"  >
				Estatus
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;color:black;font-size:10px; width:550px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;" >
 				'.$ls_estatus.'
			</TD>';
	$html.='</TR>';
	$html.='<TR>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:100px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;"  >
				Tipo
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;color:black;font-size:10px; width:550px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;" >
 				'.$ls_dentipsol.'
			</TD>';
	$html.='</TR>';
	$html.='<TR>';
		
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:100px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;"  >
				Unidad Ejecutora
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;color:black;font-size:10px; width:550px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;" >
 				'.$ls_denuniadm.'
			</TD>';
	$html.='</TR>';
	$html.='<TR>';
		
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:120px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;"  >
				Fuente Financiamiento
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;color:black;font-size:10px; width:550px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;" >
 				'.$ls_denfuefin.'
			</TD>';
	$html.='</TR>';
	$html.='<TR>';
		
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:120px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;"  >
				Proveedor / Beneficiario
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;color:black;font-size:10px; width:550px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;">
 				'.$ls_nombre.' '.$ls_codigo1.'
			</TD>';
	$html.='</TR>';
	$html.='<TR>';
		
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:120px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;"  >
				Concepto
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;color:black;font-size:10px; width:550px;text-align:left;height:15px;border:'.$border.'px solid;vertical-align:middle;" >
 				'.$ls_consol.'
			</TD>';
	$html.='</TR>';
	//$html.='</thead>';
	$html.='</table>';


$border="1";
$html.='<table style="border-spacing:0px; border-collapse:0px; border:1px solid #000000;border-collapse:collapse;" celspacing="0" celpaddign="0"  bgcolor="white">';
$html.='<thead>';
		$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:black;width:670px;text-align:center;height:2px;border-bottom:1px solid #000000;border-top:0px solid;vertical-align:middle;"  colspan="7" ></TD>';
		$html.='</tr>';
		$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:black;background:#B2B2B2; width:670px;text-align:center;height:17px;border:'.$border.'px solid #000000;vertical-align:middle;"  colspan="7" >
					DETALLE DE '.$ls_dentipsol.' 
				</TD>';
		$html.='</tr>';
		$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:90px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;"  >
					C&oacute;digo 
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:150px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;"  >
					Denominaci&oacute;n
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:40px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;"  >
					Cant
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:85px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;"  >
					Costo
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:85px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;"  >
					Sub-total
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:85px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;"  >
					Cargo
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:85px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;"  >
					Total
				</TD>';
		$html.='</tr>';
$html.='</thead>';
/*$html.='<tfoot>';
		$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:black;width:670px;text-align:center;height:2px;border-top:1px solid;vertical-align:middle;"  colspan="7" ></TD>';
		$html.='</tr>';
$html.='</tfoot>';*/
$html.='<tbody>';
		for($i=1;$i<=count($datos_reporte['detalle']);$i++)
		{
			$border_bott = "";
			if($i==count($datos_reporte['detalle']))
				$border_bott = "border-bottom:1px solid;";
			else if ($y + $h>=$html2pdf->pdf->getH() - $html2pdf->pdf->getbMargin()) 
			{
				if (!$html2pdf->_isInOverflow && !$html2pdf->_isInFooter) 
				{
					
					$border_bott = "border-bottom:1px solid;";
				}
			}		
	

			$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black; width:90px;text-align:center;height:17px;border-left:1px solid;border-right:1px solid;vertical-align:middle;'.$border_bott.'"  >
					'.$datos_reporte['detalle'][$i]['codigo'].'
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black;padding-left:5px; width:175px;text-align:left;height:17px;border-right:1px solid;px solid;vertical-align:middle;'.$border_bott.'"  >
					'.$datos_reporte['detalle'][$i]['denominacion'].'
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black;padding-left:5px; width:40px;text-align:left;height:17px;border-right:1px solid;vertical-align:middle;'.$border_bott.'"  >
					'.$datos_reporte['detalle'][$i]['cantidad'].'
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black;padding-right:5px; width:85px;text-align:right;height:17px;border-right:1px solid;vertical-align:middle;'.$border_bott.'"  >
					'.$datos_reporte['detalle'][$i]['cosuni'].'
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black;padding-right:5px; width:85px;text-align:right;height:17px;border-right:1px solid;vertical-align:middle;'.$border_bott.'"  >
					'.$datos_reporte['detalle'][$i]['baseimp'].'&nbsp;
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black;padding-right:5px; width:85px;text-align:right;height:17px;border-right:1px solid;vertical-align:middle;'.$border_bott.'"  >
					'.$datos_reporte['detalle'][$i]['cargo'].'&nbsp;
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black;padding-right:5px; width:85px;text-align:right;height:17px;border-right:1px solid;vertical-align:middle;'.$border_bott.'"  >
					'.$datos_reporte['detalle'][$i]['montot'].'&nbsp;
				</TD>';
			$html.='</tr>';
			//$sub_total += $datos_reporte['detalle'][$i]['baseimp'];
			//$cargos    += $datos_reporte['detalle'][$i]['cargo'];
		}

$html.='</tbody>';
$html.='</table>';
$html.='<table style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
		$html.='<thead>';
		$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:black;width:670px;text-align:center;height:2px;border-bottom:1px solid;border-top:1px solid;vertical-align:middle;"  colspan="3" ></TD>';
		$html.='</tr>';
		$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:black;background:#B2B2B2; width:670px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;"  colspan="3" >
					Detalle de Presupuesto
				</TD>';
		$html.='</tr>';
		$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:280px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;"  >
					'.$ls_titcuentas.'
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:280px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;" >
					Cuenta
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:black; width:150px;text-align:center;height:17px;border:'.$border.'px solid;vertical-align:middle;"  >
					Total
				</TD>';
		$html.='</tr>';
		$html.='</thead>';
		$html.='<tbody>';
		for($i=1;$i<=count($datos_reporte['presupuesto']);$i++)
		{
			$border_bott = "";
			if($i==count($datos_reporte['presupuesto']))
				$border_bott = "border-bottom:1px solid;";
			$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black; width:250px;text-align:center;height:17px;border-left:1px solid;border-right:1px solid;vertical-align:middle;'.$border_bott.'"  >
					'.$datos_reporte['presupuesto'][$i]['codestpro'].'
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black; width:250px;text-align:center;height:17px;border-right:1px solid;px solid;vertical-align:middle;'.$border_bott.'"  >
					'.$datos_reporte['presupuesto'][$i]['cuenta'].'
				</TD>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black;padding-right:5px; width:150px;text-align:right;height:17px;border-right:1px solid;vertical-align:middle;'.$border_bott.'"  >
					'.$datos_reporte['presupuesto'][$i]['monto'].'
				</TD>';
			$html.='</tr>';
		}
		
		$html.='<tr>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:black;padding-right:15px;width:500px;text-align:right;height:17px;border-top:1px solid;vertical-align:middle;" colspan="2" >
				Sub Total '.$ls_titsub.'
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black;padding-right:5px; width:80px;text-align:right;height:17px;border-top:1px solid;vertical-align:middle;"  >
				'.number_format($sub_total,2,",",".").'
			</TD>';
		$html.='</tr>';

		$html.='<tr>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:black;padding-right:15px;width:500px;text-align:right;height:17px;vertical-align:middle;" colspan="2" >
				Cargos '.$ls_titsub.'
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black;padding-right:5px; width:80px;text-align:right;height:17px;vertical-align:middle;"  >
				'.number_format($cargos,2,",",".").'
			</TD>';
		$html.='</tr>';

		$html.='<tr>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:black;padding-right:15px;width:500px;text-align:right;height:17px;vertical-align:middle;" colspan="2" >
				Total '.$ls_titsub.'
			</TD>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;color:black;padding-right:5px; width:80px;text-align:right;height:17px;vertical-align:middle;"  >
				'.number_format(($sub_total+$cargos),2,",",".").'
			</TD>';
		$html.='</tr>';
		$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:black; width:670px;text-align:center;height:5px;border:0px solid;vertical-align:middle;"  colspan="3" ></TD>';
		$html.='</tr>';
		$html.='<tr>';
			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:black;background:#B2B2B2; width:670px;text-align:center;height:15px;border:0px solid;vertical-align:middle;"  colspan="3" >
				Son: '.$ls_monto.'
			</TD>';
		$html.='</tr>';

$html.='</tbody>';
$html.='</table>';



$html.='</page>';

echo $html;
//die();
//echo dirname(__FILE__);die();

$content = ob_get_clean();
$html2pdf->setTestTdInOnePage(false);
$html2pdf->WriteHTML($content, isset($_GET['vuehtml']));
$html2pdf->Output('solicitud_presupuestaria.pdf','D');
//$html2pdf->Output('solicitud_presupuestaria.pdf');

?>