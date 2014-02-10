<?php
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//  ESTE FORMATO SE IMPRIME EN Bs Y EN BsF. SEGUN LO SELECCIONADO POR EL USUARIO
	//  MODIFICADO POR: ING.YOZELIN BARRAGAN         FECHA DE MODIFICACION : 03/09/2007
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
	ini_set('memory_limit','24M');
	
		require_once("../../shared/ezpdf/class.ezpdf.php");
		require_once("../../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();
		require_once("../class_funciones_activos.php");
		$io_fun_activo=new class_funciones_activos();
		$ls_tipoformato=$io_fun_activo->uf_obtenervalor_get("tipoformato",0);
		global $ls_tipoformato;
		if($ls_tipoformato==1)
		{
			require_once("sigesp_saf_class_reportbsf.php");
			$io_report=new sigesp_saf_class_reportbsf();
			$ls_titulo_report="Bs.F.";
		}
		else
		{
			require_once("sigesp_saf_class_report.php");
			$io_report=new sigesp_saf_class_report();
			$ls_titulo_report="Bs.";
		}	
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_nomemp=$arre["nombre"];
	$ls_cmpmov=$io_fun_activo->uf_obtenervalor_get("cmpmov","");
	//----------------------------------------------------  Par�metros del encabezado  --------------------------------------------
	$ls_titulo="<b>ACTA DE INCORPORACI�N</b>";   
	$ls_coduniadm=$io_fun_activo->uf_obtenervalor_get("coduniadm","");
	$ls_codres=$io_fun_activo->uf_obtenervalor_get("codres","");
        $ls_fecha_acta=$io_fun_activo->uf_obtenervalor_get("txtdesde","");
        $ls_cedsupbienesnac=$io_fun_activo->uf_obtenervalor_get("txtcodres","");
        $ls_nomsupbienesnac=$io_fun_activo->uf_obtenervalor_get("nomres","");
        $explode_fecha = explode('/',$ls_fecha_acta);
        
        switch($explode_fecha[1])
        {
            case '01';
                $mes = 'Enero';
            break;
            case '02';
                $mes = 'Febrero';
            break;
            case '03';
                $mes = 'Marzo';
            break;
            case '04';
                $mes = 'Abril';
            break;
            case '05';
                $mes = 'Mayo';
            break;
            case '06';
                $mes = 'Junio';
            break;
            case '07';
                $mes = 'Julio';
            break;
            case '08';
                $mes = 'Agosto';
            break;
            case '09';
                $mes = 'Septiembre';
            break;
            case '10';
                $mes = 'Octubre';
            break;
            case '11';
                $mes = 'Noviembre';
            break;
            case '12';
                $mes = 'Diciembre';
            break;

        }
        $ld_cargoresp=$io_fun_activo->uf_obtenervalor_get("cargorespri","");
        
	//--------------------------------------------------------------------------------------------------------------------------------
	
        $lb_valido=$io_report->uf_select_datos_movimiento($ls_codemp,$ls_cmpmov,$ls_codres);
        

	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	 else // Imprimimos el reporte
	 {
		/////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
		$ls_desc_event="Se Genero el Reporte Acta de Recepcion de Bienes con orden de compra ".$ls_numordcom." ";
		$io_fun_activo->uf_load_seguridad_reporte("SIV","sigesp_siv_r_acta_recepcion_bienes.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               //////////////////////////////////////////////////////

                /******************************************************************************/
               

                $li_aux1=0;
		$li_totrow_det1=$io_report->ds_detalle->getRowCount("cmpmov");
                 for($li_s1=1;$li_s1<=$li_totrow_det1;$li_s1++)
                {
                     $ls_codmov             = $io_report->ds_detalle->data["cmpmov"][$li_s1];
                     $ls_respprimario       = $io_report->ds_detalle->data["nomrespri"][$li_s1];
                     $ls_cedrespprimario    = $io_report->ds_detalle->data["cedpercodrespri"][$li_s1];
                     $ls_nomresuso          =$io_report->ds_detalle->data["nomresuso"][$li_s1];
                     $ls_cedresuso          =$io_report->ds_detalle->data["cedpercodresuso"][$li_s1];
                     $ls_asigcargouso          =$io_report->ds_detalle->data["asig"][$li_s1];
                     $ls_cargouso          =$io_report->ds_detalle->data["cargo"][$li_s1];

                 }if ($ls_cargouso=='Sin Cargo'){
                     $ls_caruso=$ls_asigcargouso;
                 }else {$ls_caruso=$ls_cargouso;}
                    
                ob_start();
                require_once('../../html2pdf/html2pdf.class.php');
                $html2pdf = new HTML2PDF('P','Letter','es');
                $html ='';
                $html .='<page backtop="30mm" backleft="7mm" backbottom="60mm" >';
                //ENCABEZADO
                $size ='font-size:14px;';
                $fonfamily_css  ="font-family:Arial,Verdana,Bitstream Vera Sans,Sans,Sans-serif;";

                $html.='<page_header>';
                    $html.='<TABLE align="left"  style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
                    $html.='<TR>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:600px;text-align:left;border:0px solid;align:left"   >
                                            <IMG src="../../shared/imagebank/'.$_SESSION["ls_logo"].'" width="120"  align="left" border="0">
                                    </TD>';
                    $html.='</TR>';
                    $html.='</table>';
                $html.='</page_header>';

                //PIE DE PAGINA
                $html.= '<page_footer>';
                        $size ='font-size:14px;';
                  $html.='<TABLE  width="650px" style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
                    $html.='<TR>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:325px;text-align:center;border:0px solid;'.$size.'"   >
                                           _______________________________
                                          <br>'.utf8_encode($ld_cargoresp).' <br>
                                              '.utf8_encode($ls_respprimario).' <br>
                                              '.$ls_cedrespprimario.' <br>
                                             
                                          
                                    </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:325px;text-align:center;border:0px solid;'.$size.'"   >
                                            _______________________________
                                           <br>'.utf8_encode($ls_caruso).' <br>
                                              '.utf8_encode($ls_nomresuso).' <br>
                                              '.$ls_cedresuso.' <br>
                                           
                                    </TD>';
                    $html.='</TR>';
                  $html.='</TABLE><br><br>';
                  $html.='<TABLE  width="650px" align="center" style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
                    $html.='<TR>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:650px;text-align:center;border:0px solid;'.$size.'"   >
                                           _______________________________
                                           <br>'.utf8_encode($ls_nomsupbienesnac).' <br>
                                              '.$ls_cedsupbienesnac.' <br>
                                           
                                    </TD>';
                    $html.='</TR>';
                  $html.='</TABLE><br><br>';

                  $html.='<table width="650px" style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
                            $html.='<tr>';
                                    $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;color:black; width:650px;text-align:left;height:13px;border:0px solid;">
                                                    OD/ov.
                                            </TD>';
                            $html.='</tr>';
                            
                    $html.='</table>';
                     $html.=' <table   style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
                                $html.='<tr>';
                                    $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:10px;color:black; width:700px;text-align:justify;border:0px solid;">
                                                <p align="justify"> <b> Se hace entrega los bienes en excelentes condiciones. Se recuerda que en caso de movilización, extravío, robo o hurto participar por escrito a la Dirección de Bienes y Servicios del MPPPC y hacer la respectiva denuncia ante el CICPC (en los casos de robo o hurto).</b></p>
                                            </TD>';
                            $html.='</tr>';
                    $html.='</table> <br>';

                     $html.=' <table width="650px" align="center" style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
                                $html.='<tr>';
                                    $html.='<TD style="'.$fonfamily_css.'font-weight:none;font-size:9px;color:black; width:650px;text-align:center;height:13px;border:0px solid;">
                                                Av. Panteón, Foro Libertador, Edificio Archivo General de la Nación, PB. Código Postal 1010. Caracas-Venezuela, Ministerio de la Cultura
                                                <br>
                                                Telfs: (0058-212) 564.22.07 / 3789 / 0106 / 9383 / 8023 / 2939 / 4750 / 6695 Fax: 564.44.71
                                            </TD>';
                            $html.='</tr>';
                    $html.='</table>
                </page_footer>';



                $html.='<TABLE  align="center" style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
                    $html.='<TR>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:300px;text-align:center;border:0px solid;'.$size.'"   >
                                           <b>Acta de Incorporación N° '.$ls_codmov.'</b>
                                    </TD>';
                    $html.='</TR>';
                $html.='</table>';
                $html.='<br/>';
                $html.='<TABLE  border="0" width="750px" >';
                $html.='<TR>';
                        $html.='<TD style="'.$fonfamily_css.'color:black;width:700px;text-align:justify;border:0px solid;line-height:150%;'.$size.'"  >
                                      En Caracas, a los '.$explode_fecha[0].' del mes de '.$mes.' del año '.$explode_fecha[2].', reunidos en la
                                           sede del Ministerio del Poder Popular
                                           para la Cultura, los funcionarios, <b>'.utf8_encode($ls_respprimario).'</b>, Titular de la cédula de identidad <b>N° '.$ls_cedrespprimario.',</b>
                                           '.utf8_encode($ld_cargoresp).', quien entrega los bienes como responsable primario,
                                          <b>'.utf8_encode($ls_nomresuso).'</b>, titular de la cédula de identidad
                                           <b>N° '.$ls_cedresuso.'</b> de Cargo <b> '.$ls_caruso.'</b> como responsable de uso y <b>'.utf8_encode($ls_nomsupbienesnac).',</b>
                                           titular de la cédula de identidad <b>N° '.$ls_cedsupbienesnac.'</b>, de la Unidad de
                                           Bienes Nacionales, en calidad de supervisor todos con el fin de hacer formal la entrega de los
                                           bienes descritos a continuación:
                                </TD>';
                $html.='</TR>';
                $html.='</table><br/><br/><br/>';
                
               $size ='font-size:9px;';
                   $html.='<TABLE  width="650px" style="border-spacing:0px; border-collapse:0px; border:1px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
                    $html.='<TR>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:40px;text-align:center;border:1px solid;'.$size.'"   >
                                           <b>Cantidad</b>
                                    </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:50px;text-align:center;border:1px solid;'.$size.'"   >
                                           <b>Código del Catálogo</b>
                                    </TD>';
                             $html.='<TD style="'.$fonfamily_css.'color:black; width:99px;text-align:center;border:1px solid;'.$size.'"   >
                                           <b>Serial del Articulo</b>
                                    </TD>';
                             $html.='<TD valign="middle" style="'.$fonfamily_css.'color:black; width:101px;text-align:center;border:1px solid;'.$size.'""   >
                                           <b>Número de Inventario</b>
                                    </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:150px;text-align:center;border:1px solid;'.$size.'""   >
                                    <b>Descripción</b>
                                </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:79px;text-align:center;border:1px solid;'.$size.'""   >
                                    <b>Incorporación</b>
                                </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:65px;text-align:center;border:1px solid;'.$size.'""   >
                                    <b>Valor Unitario</b>
                                </TD>';
                             $html.='<TD style="'.$fonfamily_css.'color:black; width:65px;text-align:center;border:1px solid;'.$size.'""   >
                                    <b>Valor Total</b>
                                </TD>';
                    $html.='</TR>';
               $lb_valido=$io_report->uf_saf_load_dt_compmovimiento($ls_codemp,$ls_cmpmov,$ls_codres); // Cargar el DS con los datos de la cabecera del reporte
                 $li_aux=0;
		$li_totrow_det=$io_report->ds_detalle->getRowCount("codact");
                
                for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
                {
                        $ls_auxcoduniadm= $io_report->ds_detalle->data["coduniadm"][$li_s];
                        $ls_codart=       $io_report->ds_detalle->data["codact"][$li_s];
                        $ls_denart=       $io_report->ds_detalle->data["denact"][$li_s];
                        $ls_catalogo=     $io_report->ds_detalle->data["catalogo"][$li_s];
                        $li_ideact=       $io_report->ds_detalle->data["ideact"][$li_s];
                        $ls_codcau=       $io_report->ds_detalle->data["codcau"][$li_s];
                        $ls_dencau=       $io_report->ds_detalle->data["dencau"][$li_s];
                        $li_costo=        $io_report->ds_detalle->data["costo"][$li_s];
                        $li_cantidad=     $io_report->ds_detalle->data["cantidad"][$li_s];
                        $li_seract=     $io_report->ds_detalle->data["seract"][$li_s];
                        $li_total=($li_costo * $li_cantidad);
                        $li_cantidad=$io_fun_activo->uf_formatonumerico($li_cantidad);
                        $li_costo=$io_fun_activo->uf_formatonumerico($li_costo);
                        $li_total=$io_fun_activo->uf_formatonumerico($li_total);
                      if($ls_auxcoduniadm==$ls_coduniadm)
                        {
                                $li_aux=$li_aux + 1;
                               /* $la_data[$li_aux]=array('cantidad'=>$li_cantidad,'catalogo'=>$ls_catalogo,'codact'=>$ls_codart,'denact'=>$ls_denart,
                                                                          'codcau'=>$ls_codcau." ".$ls_dencau,'costo'=>$li_costo,'total'=>$li_total);*/
                                $ls_codcau = $ls_codcau." ".$ls_dencau;
                        }
                         $html.='<TR>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:40px;text-align:center;border:1px solid;'.$size.'"   >
                                           '.$li_cantidad.'
                                    </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:50px;text-align:center;border:1px solid;'.$size.'"   >
                                           '.$ls_catalogo.'
                                    </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:101px;text-align:center;border:1px solid;'.$size.'"   >
                                           '.$li_seract.'
                                    </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:99px;text-align:center;border:1px solid;'.$size.'""   >
                                           '.$ls_codart.'
                                    </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:150px;text-align:center;border:1px solid;'.$size.'""   >
                                    '.$ls_denart.'
                                </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:79px;text-align:center;border:1px solid;'.$size.'""   >
                                    '.    $ls_codcau.'
                                </TD>';
                            $html.='<TD style="'.$fonfamily_css.'color:black; width:65px;text-align:right;border:1px solid;'.$size.'""   >
                                   '.$li_costo.'
                                </TD>';
                             $html.='<TD style="'.$fonfamily_css.'color:black; width:65px;text-align:right;border:1px solid;'.$size.'""   >
                                    '.$li_total.'
                                </TD>';
                    $html.='</TR>';
                        
                }
                
                 $html.='</table><br><br>';
                 $size ='font-size:14px;';
                 $html.='<TABLE align="justify" width="700px" style="border-spacing:0px; border-collapse:0px; border:0px solid;border-collapse:collapse;" bgcolor="white" celspacing="0" celpaddign="0"  bgcolor="white">';
                $html.='<TR>';
                        $html.='<TD align="justify" style="'.$fonfamily_css.'color:black;width:700px;text-align:justify;border:0px solid;line-height:150%;'.$size.'"  >
                                     Se levanta la presente Acta por triplicado a un solo tenor y efecto la cual es leída y firmada en señal de conformidad.
                                </TD>';
                $html.='</TR>';
                $html.='</table>';
                $html.='</page>';

                echo $html;
             
                $content = ob_get_clean();
                $html2pdf->setTestTdInOnePage(false);
                $html2pdf->WriteHTML($content, isset($_GET['vuehtml']));
                $html2pdf->Output('acta_incorporacion.pdf','D');
		
	}
	
?> 