<?php
/*ini_set('error_reporting', E_ALL);
        ini_set('display_errors' , 'On');
        ini_set('display_startup_errors', 'On');*/
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
		// Fecha Creaci�n: 15/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;

		$ls_descripcion="Genero el Reporte formato gobierno distrito capital".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesmunicipales.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------



	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo= "COMPROBANTE DE RETENCION DE IMPUESTO DE TIMBRE FISCAL";
        $ls_agente=$_SESSION["la_empresa"]["nombre"];
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_mes=$io_fun_cxp->uf_obtenervalor_get("mes","");
	$ls_anio=$io_fun_cxp->uf_obtenervalor_get("anio","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	$ls_licagenteret=$_SESSION["la_empresa"]["numlicemp"];
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
                        $html2='';
			error_reporting(E_ALL);
			set_time_limit(1800);
			$lb_valido=true;
			$ls_numcomant = "";
                        $total_base =0;
                        $total_iva  =0;
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				
				$ls_numcom=$la_datos[$li_z];
				$lb_valido=$io_report->uf_retencionesunoxmil_proveedor($ls_numcom,$ls_mes,$ls_anio);
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
						$ls_rif=$io_report->DS->data["rif"][$li_i];
						$ls_dirsujret=$io_report->DS->data["dirsujret"][$li_i];
						$li_estcmpret=$io_report->DS->data["estcmpret"][$li_i];
						$ls_numlic=$io_report->DS->data["numlic"][$li_i];
					}
					$lb_valido=$io_report->uf_retencionesunoxmil_detalles($ls_numcom);
					if($lb_valido)
					{
						$li_totalbaseimp=0;
						$li_totalmontoimp=0;
						$li_totmontoiva=0;
						$li_totmontotdoc=0;
						$li_total=$io_report->ds_detalle->getRowCount("numfac");
                                                //echo $li_total."<hr>";
						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							//$li_montotdoc=$io_report->uf_retencionesmunicipales_monfact($ls_numcon);
							$ls_numsop=$io_report->ds_detalle->data["numsop"][$li_i];
							//$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);
							//$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];
							//$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];
							$li_iva_ret=$io_report->ds_detalle->data["iva_ret"][$li_i];
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];
							$li_totimp=$io_report->ds_detalle->data["totimp"][$li_i];

							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;
							$li_totmontotdoc=$li_totmontotdoc+$li_montotdoc;
							$li_totmontoiva += $li_iva_ret;

                                                        $total_base +=$li_baseimp;
                                                        $total_iva  +=$li_iva_ret;
						  }
                                                  $html2.='<TR>';
                                                            $html2.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:80px;height:15px;border:1px solid;text-align:center;vertical-align:middle;" >';
                                                                    $html2.=$ls_fecrep;
                                                            $html2.='</TD>';
                                                            $html2.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:80px;height:15px;border:1px solid;text-align:center;vertical-align:middle;" >';
                                                                    $html2.=$ls_numsop;
                                                            $html2.='</TD>';
                                                            $html2.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:150px;height:15px;border:1px solid;text-align:center;vertical-align:middle;" >';
                                                                    $html2.=$ls_nomsujret;
                                                            $html2.='</TD>';
                                                            $html2.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:80px;height:15px;border:1px solid;text-align:center;vertical-align:middle;" >';
                                                                    $html2.=$ls_rif;
                                                            $html2.='</TD>';
                                                            $html2.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:80px;height:15px;border:1px solid;text-align:center;vertical-align:middle;" >';
                                                                    $html2.=number_format($li_totalbaseimp,2,",",".");
                                                            $html2.='</TD>';
                                                            $html2.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:110px;height:15px;border:1px solid;text-align:center;vertical-align:middle;" >';
                                                                    $html2.=number_format($li_totmontoiva,2,",",".");
                                                            $html2.='</TD>';
                                                            $html2.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:110px;height:15px;border:1px solid;text-align:center;vertical-align:middle;" >';
                                                                    $html2.='';
                                                            $html2.='</TD>';
                                                  $html2.='</TR>';
					}
				}
				
			}
			
		}
	}
ob_start();
$mes['01']="ENERO";
$mes['02']="FEBRERO";
$mes['03']="MARZO";
$mes['04']="ABRIL";
$mes['05']="MAYO";
$mes['06']="JUNIO";
$mes['07']="JULIO";
$mes['08']="AGOSTO";
$mes['09']="SEPTIEMBRE";
$mes['10']="OCTUBRE";
$mes['11']="NOVIEMBRE";
$mes['12']="DICIEMBRE";
$fonfamily_css  ="font-family:Arial,Verdana,Bitstream Vera Sans,Sans,Sans-serif;";
$html ='';
$html .='<page backtop="80mm" backleft="5mm"  backbottom="5mm" >';

$html.='<page_header>';
$html.='<TABLE style="border-spacing:0px; border-collapse:0px; border:0px solid; bordercolor:#cc3300; width=750px;" celspacing="0" celpaddign="1" border="0">';
$html.='<TR>';
        $html.='<TD style="'.$fonfamily_css.'font-weight:bold;color:black; width:140px;border:0px solid;text-align:right;" bgcolor="white"  >
		<IMG src="../../shared/imagenes/gob_dttc.png" width="140" align="right" border="0">
		</TD>';
	$html.='<TD style="'.$fonfamily_css.'font-weight:bold;color:black; width:30px;border:0px solid;text-align:right;" bgcolor="white" >
			&nbsp;&nbsp;&nbsp;
		</TD>';
	$html.='<TD style="'.$fonfamily_css.'font-weight:bold;color:black; width:500px;border:0px solid;" bgcolor="white" >';
			$html.='<TABLE style="border-spacing:0px; border-collapse:0px; border:0px solid; bordercolor:#cc3300; width=500px;" celspacing="0" celpaddign="1" border="0">';
				$html.='<TR>';
        			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black;background:#ffffff; width:500px;height:20px;border:0px solid;text-align:center;vertical-align:bottom;" >';
					$html.=$mes[$ls_mes]." ".$ls_anio;
				$html.='</TD>';
				$html.='</TR>';
				$html.='<TR>';
        			$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:12px;width:500px;color:#000000;height:20px;border:0px solid;text-align:center;vertical-align:middle;" bgcolor="white" >';
					$html.='REP&Uacute;BLICA BOLIVARIANA DE VENEZUELA';
				$html.='</TD>';
				$html.='</TR>';
				$html.='<TR>';
        			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:#000000;width:500px;height:20px;border:0px solid;text-align:center;vertical-align:middle;" bgcolor="white" >';
					$html.='SERVICIO DE ADMINISTRACI&Oacute;N TRIBUTARIA DEL DISTRITO CAPITAL';
				$html.='</TD>';
				$html.='</TR>';
                                $html.='<TR>';
        			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;color:#000000;width:500px;height:20px;border-bottom:1px solid #ff0000;text-align:right;vertical-align:middle;" bgcolor="white" >';
					$html.='Direcci&oacute;n de Recaudaci&oacute;n';
				$html.='</TD>';
				$html.='</TR>';
                                $html.='<TR>';
        			$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:#000000;width:500px;height:10px;border-bottom:0px solid #ff0000;text-align:right;vertical-align:middle;" bgcolor="white" >';
					$html.='RELACI&Oacute;N MENSUAL DEL IMPUESTO 1X1000<br>ORDENES DE PAGO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$html.='</TD>';
				$html.='</TR>';
			$html.='</table>';
		$html.='</TD>';
$html.='</TR>';
$html.='<TR>';
$html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:12px;color:#000000;width:500px;height:10px;border-bottom:0px solid #ff0000;text-align:left;vertical-align:middle;" bgcolor="white" colspan="3" >';
            $html.='<TABLE style="border-spacing:0px; border-collapse:0px; border:0px solid; bordercolor:#cc3300; width=500px;" celspacing="0" celpaddign="1" border="0">';
			$html.='<TR>';
        		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black;background:#ffffff; width:100px;height:30px;border:0px solid;text-align:left;vertical-align:bottom;" >';
				$html.='Nombre de la <br>Instituci&oacute;n';
			$html.='</TD>';
                        $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:#AC0011;background:#ffffff; width:250px;height:30px;border-bottom:1px solid #000000;text-align:left;vertical-align:bottom;" >';
				$html.=$ls_agenteret;
			$html.='</TD>';
                        $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black;background:#ffffff; width:100px;height:30px;border:0px solid;text-align:right;vertical-align:bottom;" >';
				$html.='R.I.F:';
			$html.='</TD>';
                        $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:#AC0011;background:#ffffff; width:200px;height:30px;border-bottom:1px solid #000000;text-align:center;vertical-align:bottom;" >';
				$html.=$ls_rifagenteret;
			$html.='</TD>';
			$html.='</TR>';
                        $html.='<TR>';
        		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black;background:#ffffff; width:100px;height:15px;border:0px solid;text-align:left;vertical-align:bottom;" >';
				$html.='Direcci&oacute;n:';
			$html.='</TD>';
                        $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:#AC0011;background:#ffffff; width:550px;height:15px;border-bottom:1px solid #000000;text-align:left;vertical-align:bottom;" COLSPAN="3" >';
				$html.=$ls_diragenteret;
			$html.='</TD>';
			$html.='</TR>';
                        $html.='<TR>';
        		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black;background:#ffffff; width:100px;height:20px;border:0px solid;text-align:left;vertical-align:bottom;" >';
				$html.='Per&iacute;odo:';
			$html.='</TD>';
                        $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:#AC0011;background:#ffffff; width:250px;height:20px;border-bottom:1px solid #000000;text-align:center;vertical-align:bottom;" >';
				$html.=$ls_anio."-".$ls_mes;
			$html.='</TD>';
                        $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black;background:#ffffff; width:100px;height:20px;border:0px solid;text-align:right;vertical-align:bottom;" >';
				$html.='&nbsp;';
			$html.='</TD>';
                        $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:#AC0011;background:#ffffff; width:200px;height:20px;border-bottom:0px solid #000000;text-align:center;vertical-align:bottom;" >';
				$html.='&nbsp;';
			$html.='</TD>';
			$html.='</TR>';
                        $html.='<TR>';
        		$html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black;background:#ffffff; width:100px;height:35px;border:0px solid;text-align:left;vertical-align:bottom;" >';
				$html.='Planilla(s)<br>Bancaria(s)';
			$html.='</TD>';
                        $plani=explode(",",$_GET['planilla']);
                        $planilla="";
                        for($u=0;$u<count($plani);$u++)
                            $planilla .= $plani[$u]."<br>";
                        $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:#AC0011;background:#ffffff; width:250px;height:35px;border-bottom:1px solid #000000;text-align:center;vertical-align:middle;" >';
				$html.=$planilla;
			$html.='</TD>';
                        $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black;background:#ffffff; width:100px;height:20px;border:0px solid;text-align:right;vertical-align:bottom;" >';
				$html.='&nbsp;';
			$html.='</TD>';
                        $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:#AC0011;background:#ffffff; width:200px;height:20px;border-bottom:0px solid #000000;text-align:center;vertical-align:bottom;" >';
				$html.='&nbsp;';
			$html.='</TD>';
			$html.='</TR>';
                       
             $html.='</table>';
$html.='</TD>';
$html.='</TR>';
$html.='</table>';
$html.='</page_header>';


//PIE DE PAGINA
$size ='font-size:9px;';
$html.= '<page_footer>';
$html.='<TABLE style="border-spacing:0px; border-collapse:0px; border:0px solid; bordercolor:#cc3300; width:960px;text-align:center;" celspacing="0" celpaddign="1" border="0">';
	$html.='<TR>';
		$html.='<TD style="'.$fonfamily_css.'font-weight:bold;color:black; width:15px;text-align:center;" bgcolor="white" colspan="0">
 				&nbsp;&nbsp;
			</TD>';
	$html.='</TR>';
	$html.='</table>';
$html.= '</page_footer>';
//$html.='<page pageset="old"></page>';
//$html  .= $_SESSION['tabla_actividad_general'];


	


$html.='<TABLE style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
 $html.='<Thead>';
    $html.='<TR>';
    $html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;background:#C4C4C4; width:80px;height:30px;border:1px solid;text-align:center;vertical-align:middle;" >';
            $html.='Fecha de la<br>Orden de pago';
    $html.='</TD>';
    $html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;background:#C4C4C4; width:80px;height:30px;border:1px solid;text-align:center;vertical-align:middle;" >';
            $html.='N&#176; de la Orden<br>de Pago';
    $html.='</TD>';
    $html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;background:#C4C4C4; width:150px;height:30px;border:1px solid;text-align:center;vertical-align:middle;" >';
            $html.='Nombre del<br>Contribuyente';
    $html.='</TD>';
    $html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;background:#C4C4C4; width:80px;height:30px;border:1px solid;text-align:center;vertical-align:middle;" >';
            $html.='C.I o RIF';
    $html.='</TD>';
    $html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:10px;background:#C4C4C4; width:80px;height:30px;border:1px solid;text-align:center;vertical-align:middle;" >';
            $html.='Monto de la<br>Operaci&oacute;n';
    $html.='</TD>';
    $html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:9px;background:#C4C4C4; width:110px;height:30px;border:1px solid;text-align:center;vertical-align:middle;" >';
            $html.='Monto del Impueto<hr>1 X 1000';
    $html.='</TD>';
    $html.='<TD style="'.$fonfamily_css.'font-weight:bold;font-size:9px;background:#C4C4C4; width:110px;height:30px;border:1px solid;text-align:center;vertical-align:middle;" >';
            $html.='Observaciones';
    $html.='</TD>';
    $html.='</TR>';
     $html.='</Thead>';

     $html.='<Tbody>';

     $html.=$html2;
     
     $html.='<TR>';
            $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:80px;height:15px;border-top:1px solid;text-align:center;vertical-align:middle;" >';
                    $html.="&nbsp;";
            $html.='</TD>';
            $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:80px;height:15px;border-top:1px solid;text-align:center;vertical-align:middle;" >';
                    $html.="&nbsp;";
            $html.='</TD>';
            $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:150px;height:15px;border-top:1px solid;text-align:center;vertical-align:middle;" >';
                    $html.="&nbsp;";
            $html.='</TD>';
            $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:80px;height:15px;border-top:1px solid;text-align:center;vertical-align:middle;" >';
                    $html.="&nbsp;";
            $html.='</TD>';
            $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:80px;height:15px;border-top:1px solid;border-right:1px solid;text-align:center;vertical-align:middle;" >';
                    $html.="&nbsp;";
            $html.='</TD>';
            $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:110px;height:15px;border:1px solid;text-align:center;vertical-align:middle;" >';
                    $html.=number_format($total_iva,2,",",".");
            $html.='</TD>';
            $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;background:#ffffff; width:110px;height:15px;border-top:1px solid;border-left:1px solid;text-align:center;vertical-align:middle;" >';
                    $html.='';
            $html.='</TD>';
  $html.='</TR>';


     $html.='</Tbody>';
$html.='</table>';


$html  .='</page>';
echo $html;
//die();
$content = ob_get_clean();
require_once('../../html2pdf/html2pdf.class.php');
$html22pdf = new HTML2PDF('P','Letter','es');
$html22pdf->setTestTdInOnePage(false);
$html22pdf->WriteHTML($content, isset($_GET['vuehtml']));
//$html22pdf->Output('incripcion_certamen.pdf');
$html22pdf->Output('reporte_unoxmil_gobdttc.pdf','D');
//$html22pdf->Output('resumen_mejor_vision_'.date("d-m-Y").'.pdf','D');

?>
