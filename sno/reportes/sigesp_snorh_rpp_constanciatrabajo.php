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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private
		//	    Arguments: as_titulo // Tï¿½tulo del Reporte
		//    Description: funciï¿½n que guarda la seguridad de quien generï¿½ el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 06/07/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$lb_valido=true;
		$ls_descripcion="Generï¿½ el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_constanciatrabajo.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private
		//	    Arguments: as_titulo // Tï¿½tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funciï¿½n que imprime los encabezados por pï¿½gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 06/07/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(80,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,680,13,$as_titulo); // Agregar el tï¿½tulo
		if($as_fecha=="1")
		{
			$io_pdf->addText(512,750,8,date("d/m/Y")); // Agregar la Fecha
			$io_pdf->addText(518,743,7,date("h:i a")); // Agregar la Hora
		}
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	include("../../shared/class_folder/class_numero_a_letra.php");
	$io_numero_letra= new class_numero_a_letra();
	//imprime numero con los valore por defecto
	//cambia a minusculas
	$io_numero_letra->setMayusculas(1);
	//cambia a femenino
	$io_numero_letra->setGenero(1);
	//cambia moneda
	$io_numero_letra->setMoneda("Bolivares");
	//cambia prefijo
	$io_numero_letra->setPrefijo("");
	//cambia sufijo
	$io_numero_letra->setSufijo("");
	//imprime numero con los cambios
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	require_once("../../shared/class_folder/class_fecha.php");
	$io_fecha=new class_fecha();
	//----------------------------------------------------  Parï¿½metros del encabezado  -----------------------------------------------
	$ls_titulo="<i>CONSTANCIA</i>";
	//--------------------------------------------------  Parï¿½metros para Filtar el Reporte  -----------------------------------------
	$ls_codcont=$io_fun_nomina->uf_obtenervalor_get("codcont","");
	$ls_codnom=$io_fun_nomina->uf_obtenervalor_get("codnom","");
	$li_rac=$io_fun_nomina->uf_obtenervalor_get("rac","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_fecha=$io_fun_nomina->uf_obtenervalor_get("fecha","");
	$ls_mesactual=$io_fun_nomina->uf_obtenervalor_get("mesactual","");
	$ls_anocurnom=$io_fun_nomina->uf_obtenervalor_get("anocurnom","");
	$ls_parametros=$io_fun_nomina->uf_obtenervalor_get("parametro","");
	$arr_codper=split("-",$ls_parametros);
	$li_totcodper=count($arr_codper);
	$li_mesanterior=(intval($ls_mesactual)-1);
	if($li_mesanterior==0)
	{
		$li_mesanterior=12;
		$ls_anocurnom=(intval($ls_anocurnom)-1);
	}
	$ls_mesanterior=str_pad($li_mesanterior,2,"0",0);
	global $ls_tiporeporte;

	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if ($li_totcodper==1)
	{
		if($lb_valido)
		{
			$lb_valido=$io_report->uf_constanciatrabajo_constancia($ls_codcont,$ls_codnom,$ls_codperdes,$ls_codperhas); // Obtenemos el detalle del reporte
		}
		if($lb_valido==false) // Existe algï¿½n error ï¿½ no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');");
			print(" close();");
			print("</script>");
		}
		else // Imprimimos el reporte
		{
			//error_reporting(E_ALL);
			//set_time_limit(1800);
			//$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			//$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra

			while ((!$io_report->rs_data->EOF)&&($lb_valido))
			{
				$ls_concont=$io_report->rs_data->fields["concont"];
				$li_tamletcont=$io_report->rs_data->fields["tamletcont"];
				$li_tamletpiecont=$io_report->rs_data->fields["tamletpiecont"];
				if($li_tamletpiecont=="")
				{
					$li_tamletpiecont=$li_tamletcont;
				}
				$li_intlincont=$io_report->rs_data->fields["intlincont"];
                                switch($li_intlincont)
                                {
                                    case "1":
                                        $li_intlincont = '100%';
                                    case "2";
                                        $li_intlincont = '150%';
                                    case "2";
                                        $li_intlincont = '200%';
                                }
				$li_marinfcont=$io_report->rs_data->fields["marinfcont"];
				$li_marsupcont=$io_report->rs_data->fields["marsupcont"];
				$ls_titcont=$io_report->rs_data->fields["titcont"];
				$ls_piepagcont=$io_report->rs_data->fields["piepagcont"];
				$ls_ente=$_SESSION["la_empresa"]["nombre"];
				$ld_fecha=date("d/m/Y");
				$ls_dia_act=substr($ld_fecha,0,2);
				$ls_mes_act=$io_fecha->uf_load_nombre_mes(substr($ld_fecha,3,2));
				$ls_ano_act=substr($ld_fecha,6,4);
//				$io_pdf->ezSetCmMargins($li_marsupcont,$li_marinfcont,3,3); // Configuraciï¿½n de los margenes en centï¿½metros
				//uf_print_encabezado_pagina($ls_titcont,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la pï¿½gina
                                  ob_start();
                                $fonfamily_css  ="font-family:Arial,Verdana,Sans,Sans-serif;";
                                //CABECERA
                                $html ='<page backtop="25mm" backbottom='.$li_marinfcont.'"mm" pageset="new" backleft="3mm" backright="3mm">';
                                $html.='<page_header>';
                                $html.='<TABLE style="border-spacing:0px; border-collapse:0px; border:0px solid; bordercolor:#cc3300; width=750px;" celspacing="0" celpaddign="1" border="0">';
                                $html.='<TR>';
                                        $html.='<TD style="'.$fonfamily_css.'font-weight:bold;color:black;align:right;text-align:right;width=750px;" bgcolor="white">
                                                        <IMG src="../../shared/imagebank/'.$_SESSION["ls_logo"].'" width="150"  align="right" border="0">
                                                </TD>';
                                $html.='</TR>';
                                $html.='</table>';
                                $html.='<br>';
                                $html.='<br>';
                                $html.='</page_header>';
                                //PIE DE PAGINA,
                                $size ='font-size:9px;';
                                $fecha = date("d-m-Y");
                                $hora = date("h:i:s");
                                $html.='<page_footer>
                                        <table style="width: 100%;">
                                                <tr>
                                                        <td align="center" ><IMG src="../../shared/imagebank/originalconstancia.jpg" width="750"></td>

                                                </tr>
                                        </table>
                                        </page_footer>';
                                $color='#FFFFFF';
                                $html.='<TABLE style="border-spacing:0px; border-collapse:0px; border:0px solid; width:750px;height:15px;" celspacing="0" celpaddign="0"  bgcolor="white">';
                                $html.='<TR>';
                                        $html.='<TD style="'.$fonfamily_css.'font-size:16px;text-align:center; border:0px solid; font-weight:bold; color:black;text-decoration:none; height:18px; width:750px;" >'.$ls_titcont.'</TD>';
                                $html.='</TR>';
                                $html.='</TABLE><br>';
				$lb_valido=$io_report->uf_constanciatrabajo_personal($ls_codnom,$li_rac,$ls_codperdes,$ls_codperhas); // Obtenemos el detalle del reporte
				if($lb_valido)
				{

					while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
					{
						$ls_contenido="";
						$ls_contenido=$ls_concont;
                                              //  echo $ls_contenido; die();
						$ls_codper=$io_report->rs_detalle->fields["codper"];
						$ls_cedper=$io_report->rs_detalle->fields["cedper"];
						$ls_apeper=$io_report->rs_detalle->fields["apeper"];
						$ls_nomper=$io_report->rs_detalle->fields["nomper"];
						$ls_descar=$io_report->rs_detalle->fields["descar"];
						$ld_fecingper=$io_report->rs_detalle->fields["fecingper"];
                                                $ls_fechaingreso=$io_funciones->uf_convertirfecmostrar($ld_fecingper);
						//$ls_mes=$io_fecha->uf_load_nombre_mes(substr($ld_fecingper,5,2));
						//$ls_fechaingreso="el ".substr($ld_fecingper,8,2)." de ".$ls_mes." de ".substr($ld_fecingper,0,4);
						$ld_fecegrper=$io_report->rs_detalle->fields["fecegrper"];
						$ls_mes=$io_fecha->uf_load_nombre_mes(substr($ld_fecegrper,5,2));
						$ls_fechaegreso="el ".substr($ld_fecegrper,8,2)." de ".$ls_mes." de ".substr($ld_fecegrper,0,4);
						$ls_dirper=$io_report->rs_detalle->fields["dirper"];
						$ld_fecnacper=$io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecnacper"]);
						$ls_edocivper=$io_report->rs_detalle->fields["edocivper"];
						switch($ls_edocivper)
						{
							case "S": // Soltero
								$ls_edocivper="Soltero";
								break;
							case "C": // Casado
								$ls_edocivper="Casado";
								break;
							case "D": // Divociado
								$ls_edocivper="Divociado";
								break;
							case "V": // Viudo
								$ls_edocivper="Viudo";
								break;
						}
						$ls_nacper=$io_report->rs_detalle->fields["nacper"];
						/*switch($ls_nacper)
						{
							case "V": // Venezolano
								$ls_nacper="Venezolano";
								break;
							case "E": // Extranjero
								$ls_nacper="Extranjero";
								break;
						}*/
						$ls_tipnom=$io_report->rs_detalle->fields["tipnom"];
						switch($ls_tipnom)
						{
							case "1": // Empleado Fijo
								$ls_tipnom="Empleado Fijo";
								break;
							case "2": // Empleado Contratado
								$ls_tipnom="Empleado Contratado";
								break;
							case "3": // Obrero Fijo
								$ls_tipnom="Obrero Fijo";
								break;
							case "4": // Obrero Contratado
								$ls_tipnom="Obrero Contratado";
								break;
							case "5": // Docente Fijo
								$ls_tipnom="Docente Fijo";
								break;
							case "6": // Docente Contratado
								$ls_tipnom="Docente Contratado";
								break;
							case "7": // Jubilado
								$ls_tipnom="Jubilado";
								break;
							case "8": // Comision de Servicios
								$ls_tipnom="Comision de Servicios";
								break;
							case "9": // Libre Nombramiento
								$ls_tipnom="Libre Nombramiento";
								break;
						}
						if($ls_tiporeporte==1)
						{
							$ls_prefijo="Bs.F.";
						}
						else
						{
							$ls_prefijo="Bs.";
						}
						$ls_telhabper=$io_report->rs_detalle->fields["telhabper"];
						$ls_telmovper=$io_report->rs_detalle->fields["telmovper"];
						$ls_desuniadm=$io_report->rs_detalle->fields["desuniadm"];
						$li_horper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["horper"]);
						$li_sueper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueper"]);
						$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueper"]);
						$ls_sueper=$io_numero_letra->letra();
						$ls_sueper=$ls_sueper." (".$ls_prefijo." ".$li_sueper.")";
                                                $io_report->rs_detalle->fields["sueintper"]=$io_report->rs_detalle->fields["sueintper"]*2;
						$li_sueintper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueintper"]);
						$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueintper"]);
						$ls_sueintper=$io_numero_letra->letra();
						$ls_sueintper=$ls_sueintper." (".$ls_prefijo." ".$li_sueintper.")";
						$li_salnorper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["salnorper"]);
						$io_numero_letra->setNumero($io_report->rs_detalle->fields["salnorper"]);
						$ls_salnorper=$io_numero_letra->letra();
						$ls_salnorper=$ls_salnorper." (".$ls_prefijo." ".$li_salnorper.")";
						$li_sueproper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueproper"]);
						$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueproper"]);
						$ls_sueproper=$io_numero_letra->letra();
						$ls_sueproper=$ls_sueproper." (".$ls_prefijo." ".$li_sueproper.")";
						$ls_desded=$io_report->rs_detalle->fields["desded"];
						$ls_destipper=$io_report->rs_detalle->fields["destipper"];
						$ls_fecjub=$io_report->rs_detalle->fields["fecjubper"];
						$ls_mes2=$io_fecha->uf_load_nombre_mes(substr($ls_fecjub,5,2));
						$ls_fecjub="el ".substr($ls_fecjub,8,2)." de ".$ls_mes2." de ".substr($ls_fecjub,0,4);
						$li_sueintper_mensual=0;
						$li_sueproper_mensual=0;

						//$ls_gerencia=$io_report->rs_detalle->fields["denger"];
						$lb_valido=$io_report->uf_constanciatrabajo_integralpromedio_mensual($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom,$li_sueintper_mensual, $li_sueproper_mensual); // Obtenemos el detalle del reporte

						$io_numero_letra->setNumero($li_sueintper_mensual);
						$ls_sueintper_mensual=$io_numero_letra->letra();
                                                $ls_sueintper_mensual=str_replace('BOLIVARES',' ',$ls_sueintper_mensual);
						$li_sueintper_mensual=$io_fun_nomina->uf_formatonumerico($li_sueintper_mensual);
						$ls_sueintper_mensual=$ls_sueintper_mensual." (".$ls_prefijo." ".$li_sueintper_mensual.")";
						$io_numero_letra->setNumero($li_sueproper_mensual);
						$ls_sueproper_mensual=$io_numero_letra->letra();
                                                $ls_sueproper_mensual=str_replace('BOLIVARES',' ',$ls_sueproper_mensual);
						$li_sueproper_mensual=$io_fun_nomina->uf_formatonumerico($li_sueproper_mensual);
						$ls_sueproper_mensual=$ls_sueproper_mensual." (".$ls_prefijo." ".$li_sueproper_mensual.")";
						//$ls_contenido="\n\n\t\t".$ls_contenido;


						$ls_contenido=str_replace("$ls_ente",$ls_ente,$ls_contenido);
                                                $io_numero_letra->setNumero($ls_dia_act);
						$ls_dia_letra=$io_numero_letra->letra();
                                                $ls_dia_letra=str_replace(' CON 00/100 BOLIVARES','',$ls_dia_letra);
                                                if ($ls_dia_letra == 'UNO')
                                                        $ls_dia_letra='UN';

						$ls_contenido=str_replace("\$ls_dia",strtolower($ls_dia_letra).'('.$ls_dia_act.')',$ls_contenido);
						$ls_contenido=str_replace("\$ls_mes",strtolower($ls_mes_act),$ls_contenido);

                                                $io_numero_letra->setNumero($ls_ano_act);
						$ls_anio_letra=$io_numero_letra->letra();

                                                $ls_anio_letra=str_replace(' CON 00/100 BOLIVARES','',$ls_anio_letra);
						$ls_contenido=str_replace('$ls_ano',strtolower($ls_anio_letra).'('.$ls_ano_act.')',$ls_contenido);
                                                $nombre_separado = explode(' ',$ls_nomper);
                                                $ls_contenido=str_replace('$ls_apellidos',"<b>".$ls_apeper."</b>",$ls_contenido);
						$ls_contenido=str_replace('$ls_nombres',"<b>".$nombre_separado[0].' '.$nombre_separado[1]."</b>",$ls_contenido);
                                                $ls_contenido=str_replace('$ls_nacionalidad','<b>'.$ls_nacper.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ls_cedula','<b>'.number_format($ls_cedper,0,'','.').'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ld_fecha_ingreso','<b>'.$ls_fechaingreso.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ls_unidad_administrativa','<b>'.$ls_desuniadm.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ls_cargo','<b>'.$ls_descar.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$li_sueldo','<b>'.$ls_sueper.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ld_fecha_egreso','<b>'.$ls_fechaegreso.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ls_direccion',$ls_dirper,$ls_contenido);
                                                $ls_contenido=str_replace('$ld_fecha_nacimiento','<b>'.$ld_fecnacper.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ls_edo_civil','<b>'.$ls_edocivper.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ls_telefono_hab',$ls_telhabper,$ls_contenido);
                                                $ls_contenido=str_replace('$ls_telefono_mov',$ls_telmovper,$ls_contenido);
                                                $ls_contenido=str_replace('$li_horas_lab',$li_horper,$ls_contenido);
                                                $ls_contenido=str_replace('$li_inte_sueldo','<b>'.$ls_sueintper.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$li_salario_normal','<b>'.$ls_salnorper.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$li_prom_sueldo','<b>'.$ls_sueproper.'<b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ls_dedicacion','<b>'.$ls_desded.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ls_tipo_personal',$ls_destipper,$ls_contenido);
                                                $ls_contenido=str_replace('$ls_tipo_nomina',$ls_tipnom,$ls_contenido);
                                                $ls_contenido=str_replace('$li_mensual_inte_sueldo','<b>'.$ls_sueintper_mensual.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$li_mensual_prom_sueldo','<b>'.$ls_sueproper_mensual.'</b>',$ls_contenido);
                                                $ls_contenido=str_replace('$ls_fecjub',$ls_fecjub,$ls_contenido);

						//$ls_contenido=str_replace("\$ls_gerencia",$ls_gerencia,$ls_contenido);

						//$li_texto=$io_pdf->addTextWrap(50,500,500,$li_tamletcont,$ls_contenido,'full');


						/*$io_pdf->ezText($ls_contenido,$li_tamletcont,array('justification' =>'full','spacing' =>$li_intlincont));
						$li_pos=($li_marinfcont*10)*(72/25.4);

						$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$ls_piepagcont,'center');
						$li_pos=$li_pos-$li_tamletpiecont;
						$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');
						$li_pos=$li_pos-$li_tamletpiecont;
						$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');*/

                                                $ls_contenido=chop($ls_contenido);
                                                $ls_contenido = preg_replace("/ +/"," ",$ls_contenido);
                                                $ls_contenido=preg_replace('/\s\s+/',' ',$ls_contenido);

                                                   // echo $ls_contenido;die();
                                                $html.='<TABLE style="border:0px solid;" cellspacing="0" cellpadding="0" >';
                                                $html.='<TR>';
                                                        $html.='<TD style="'.$fonfamily_css.'font-size:'.
                                                        $li_tamletcont.';border:0px solid; color:black;line-height:'.$li_intlincont.';text-align:justify;height:auto" >
                                                          '.utf8_encode($ls_contenido).'
                                                       </TD>';
                                                $html.='</TR>';
                                                $html.='</TABLE>';
                                                $html.='<br>';
                                                $html.='<br>';
                                                $html.='<TABLE style="border:0px solid; width:730px;height:15px;" celspacing="0" celpaddign="0" >';
                                                $html.='<TR>';

                                                        $html.='<TD style="'.$fonfamily_css.'font-size:'.$li_tamletpiecont.';width:730px;border:0px solid; color:black;text-decoration:none;text-align:justify;" >'.$ls_piepagcont.'</TD>';
                                                $html.='</TR>';
                                                $html.='</TABLE>';



						$io_report->rs_detalle->MoveNext();
						/*if(!$io_report->rs_detalle->EOF)
						{
							$io_pdf->ezNewPage(); // Insertar una nueva pï¿½gina
						}*/

					}

				}
				$io_report->rs_data->MoveNext();
			}
			if($lb_valido) // Si no ocurrio ningï¿½n error
			{

				//$io_pdf->ezStream(); // Mostramos el reporte
                                $html.='</page>';
                                echo $html;
                                $content = ob_get_clean();
                                require_once('../../html2pdf/html2pdf.class.php');
                                $html2pdf = new HTML2PDF('P','Letter','es');
                                $html2pdf->setTestTdInOnePage(false);
                                $html2pdf->WriteHTML($content, isset($_GET['vuehtml']));
                                $html2pdf->Output('constancia.pdf');
			}
			else  // Si hubo algï¿½n error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');");
				print(" close();");
				print("</script>");
			}
			//unset($io_pdf);
		}
		unset($io_report);
		unset($io_funciones);
		unset($io_fun_nomina);
	}
	else
	{
                $li_total=count($arr_codper);
		$codperdes1="";
		$codperhas1="";

		//error_reporting(E_ALL);
		//set_time_limit(1800);
		//$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
                //$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
                ob_start();
                $fonfamily_css  ="font-family:Arial,Verdana,Sans,Sans-serif;";
                //CABECERA
                $html ='<page backtop="25mm" backbottom="3mm"  backleft="3mm" backright="3mm" pagesetter="old">';
                $html.='<page_header>';
                $html.='<TABLE style="border-spacing:0px; border-collapse:0px; border:0px solid; bordercolor:#cc3300; width=750px;" celspacing="0" celpaddign="1" border="0">';
                $html.='<TR>';
                        $html.='<TD style="'.$fonfamily_css.'font-weight:bold;color:black;align:right;text-align:right;width=750px;" bgcolor="white">
                                        <IMG src="../../shared/imagebank/'.$_SESSION["ls_logo"].'" width="150"  align="right" border="0">
                                </TD>';
                $html.='</TR>';
                $html.='</table>';
                $html.='<br>';
                $html.='<br>';
                $html.='</page_header>';
		for ($i=1;$i<$li_total;$i++)
		{
                        $codperdes1=$arr_codper[$i];
			$codperhas1=$arr_codper[$i];
			$lb_valido=$io_report->uf_constanciatrabajo_constancia($ls_codcont,$ls_codnom,$codperdes1,$codperhas1);
			if($lb_valido==false) // Existe algï¿½n error ï¿½ no hay registros
			{
				print("<script language=JavaScript>");
				print(" alert('No hay nada que Reportar');");
				print(" close();");
				print("</script>");
			}
			else
			{
				while ((!$io_report->rs_data->EOF)&&($lb_valido))
				{
					$ls_concont=$io_report->rs_data->fields["concont"];
					$li_tamletcont=$io_report->rs_data->fields["tamletcont"];
					$li_tamletpiecont=$io_report->rs_data->fields["tamletpiecont"];
					if($li_tamletpiecont=="")
					{
						$li_tamletpiecont=$li_tamletcont;
					}
					$li_intlincont=$io_report->rs_data->fields["intlincont"];
                                         switch($li_intlincont)
                                        {
                                            case "1":
                                                $li_intlincont = '100%';
                                            case "2";
                                                $li_intlincont = '150%';
                                            case "2";
                                                $li_intlincont = '200%';
                                        }
                                        $li_marinfcont=$io_report->rs_data->fields["marinfcont"];
                                        $li_marsupcont=$io_report->rs_data->fields["marsupcont"];
                                        $ls_titcont=$io_report->rs_data->fields["titcont"];
                                        $ls_piepagcont=$io_report->rs_data->fields["piepagcont"];
                                        $ls_ente=$_SESSION["la_empresa"]["nombre"];
                                        $ld_fecha=date("d/m/Y");
                                        $ls_dia_act=substr($ld_fecha,0,2);
                                        $ls_mes_act=$io_fecha->uf_load_nombre_mes(substr($ld_fecha,3,2));
                                        $ls_ano_act=substr($ld_fecha,6,4);
        //				$io_pdf->ezSetCmMargins($li_marsupcont,$li_marinfcont,3,3); // Configuraciï¿½n de los margenes en centï¿½metros
                                        //uf_print_encabezado_pagina($ls_titcont,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la pï¿½gina

                                        //PIE DE PAGINA,
                                        $size ='font-size:9px;';
                                        $fecha = date("d-m-Y");
                                        $hora = date("h:i:s");
                                        $html.='<page_footer>
                                                <table style="width: 100%;">
                                                        <tr>
                                                                <td align="center" ><IMG src="../../shared/imagebank/originalconstancia.jpg" width="750"></td>

                                                        </tr>
                                                </table>
                                                </page_footer>';
                                        $color='#FFFFFF';
                                        $html.='<TABLE style="border-spacing:0px; border-collapse:0px; border:0px solid; width:750px;height:15px;" celspacing="0" celpaddign="0"  bgcolor="white">';
                                        $html.='<TR>';
                                                $html.='<TD style="'.$fonfamily_css.'font-size:16px;text-align:center; border:0px solid; font-weight:bold; color:black;text-decoration:none; height:18px; width:750px;" >'.$ls_titcont.'</TD>';
                                        $html.='</TR>';
                                        $html.='</TABLE><br>';
					$li_marinfcont=$io_report->rs_data->fields["marinfcont"];
					$li_marsupcont=$io_report->rs_data->fields["marsupcont"];
					$ls_titcont=$io_report->rs_data->fields["titcont"];
					$ls_piepagcont=$io_report->rs_data->fields["piepagcont"];
					$ls_ente=$_SESSION["la_empresa"]["nombre"];
					$ld_fecha=date("d/m/Y");
					$ls_dia_act=substr($ld_fecha,0,2);
					$ls_mes_act=$io_fecha->uf_load_nombre_mes(substr($ld_fecha,3,2));
					$ls_ano_act=substr($ld_fecha,6,4);
					//$io_pdf->ezSetCmMargins($li_marsupcont,$li_marinfcont,3,3); // Configuraciï¿½n de los margenes en centï¿½metros
					//uf_print_encabezado_pagina($ls_titcont,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la pï¿½gina
					$lb_valido=$io_report->uf_constanciatrabajo_personal($ls_codnom,$li_rac,$codperdes1,$codperhas1); // Obtenemos el detalle del reporte
					if($lb_valido)
					{
						while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
						{
							$ls_contenido="";
							$ls_contenido=$ls_concont;
							$ls_codper=$io_report->rs_detalle->fields["codper"];
							$ls_cedper=$io_report->rs_detalle->fields["cedper"];
							$ls_apeper=$io_report->rs_detalle->fields["apeper"];
							$ls_nomper=$io_report->rs_detalle->fields["nomper"];
							$ls_descar=$io_report->rs_detalle->fields["descar"];
							$ld_fecingper=$io_report->rs_detalle->fields["fecingper"];
                                                        $ls_fechaingreso=$io_funciones->uf_convertirfecmostrar($ld_fecingper);
							//$ls_mes=$io_fecha->uf_load_nombre_mes(substr($ld_fecingper,5,2));
							//$ls_fechaingreso="el ".substr($ld_fecingper,8,2)." de ".$ls_mes." de ".substr($ld_fecingper,0,4);
							$ld_fecegrper=$io_report->rs_detalle->fields["fecegrper"];
							$ls_mes=$io_fecha->uf_load_nombre_mes(substr($ld_fecegrper,5,2));
							$ls_fechaegreso="el ".substr($ld_fecegrper,8,2)." de ".$ls_mes." de ".substr($ld_fecegrper,0,4);
							$ls_dirper=$io_report->rs_detalle->fields["dirper"];
							$ld_fecnacper=$io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecnacper"]);
							$ls_edocivper=$io_report->rs_detalle->fields["edocivper"];
							switch($ls_edocivper)
							{
								case "S": // Soltero
									$ls_edocivper="Soltero";
									break;
								case "C": // Casado
									$ls_edocivper="Casado";
									break;
								case "D": // Divociado
									$ls_edocivper="Divociado";
									break;
								case "V": // Viudo
									$ls_edocivper="Viudo";
									break;
							}
							$ls_nacper=$io_report->rs_detalle->fields["nacper"];
							/*switch($ls_nacper)
							{
								case "V": // Venezolano
									$ls_nacper="Venezolano";
									break;
								case "E": // Extranjero
									$ls_nacper="Extranjero";
									break;
							}*/
							$ls_tipnom=$io_report->rs_detalle->fields["tipnom"];
							switch($ls_tipnom)
							{
								case "1": // Empleado Fijo
									$ls_tipnom="Empleado Fijo";
									break;
								case "2": // Empleado Contratado
									$ls_tipnom="Empleado Contratado";
									break;
								case "3": // Obrero Fijo
									$ls_tipnom="Obrero Fijo";
									break;
								case "4": // Obrero Contratado
									$ls_tipnom="Obrero Contratado";
									break;
								case "5": // Docente Fijo
									$ls_tipnom="Docente Fijo";
									break;
								case "6": // Docente Contratado
									$ls_tipnom="Docente Contratado";
									break;
								case "7": // Jubilado
									$ls_tipnom="Jubilado";
									break;
								case "8": // Comision de Servicios
									$ls_tipnom="Comision de Servicios";
									break;
								case "9": // Libre Nombramiento
									$ls_tipnom="Libre Nombramiento";
									break;
							}
							if($ls_tiporeporte==1)
							{
								$ls_prefijo="Bs.F.";
							}
							else
							{
								$ls_prefijo="Bs.";
							}
							$ls_telhabper=$io_report->rs_detalle->fields["telhabper"];
							$ls_telmovper=$io_report->rs_detalle->fields["telmovper"];
							$ls_desuniadm=$io_report->rs_detalle->fields["desuniadm"];
							$li_horper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["horper"]);
							$li_sueper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueper"]);
							$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueper"]);
							$ls_sueper=$io_numero_letra->letra();
							$ls_sueper=$ls_sueper." (".$ls_prefijo." ".$li_sueper.")";
                                                        $io_report->rs_detalle->fields["sueintper"]=$io_report->rs_detalle->fields["sueintper"]*2;
							$li_sueintper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueintper"]);
							$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueintper"]);
							$ls_sueintper=$io_numero_letra->letra();
							$ls_sueintper=$ls_sueintper." (".$ls_prefijo." ".$li_sueintper.")";
							$li_salnorper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["salnorper"]);
							$io_numero_letra->setNumero($io_report->rs_detalle->fields["salnorper"]);
							$ls_salnorper=$io_numero_letra->letra();
							$ls_salnorper=$ls_salnorper." (".$ls_prefijo." ".$li_salnorper.")";
							$li_sueproper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueproper"]);
							$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueproper"]);
							$ls_sueproper=$io_numero_letra->letra();
							$ls_sueproper=$ls_sueproper." (".$ls_prefijo." ".$li_sueproper.")";
							$ls_desded=$io_report->rs_detalle->fields["desded"];
							$ls_destipper=$io_report->rs_detalle->fields["destipper"];
							$ls_fecjub=$io_report->rs_detalle->fields["fecjubper"];
							$ls_mes2=$io_fecha->uf_load_nombre_mes(substr($ls_fecjub,5,2));
							$ls_fecjub="el ".substr($ls_fecjub,8,2)." de ".$ls_mes2." de ".substr($ls_fecjub,0,4);
							$li_sueintper_mensual=0;
							$li_sueproper_mensual=0;
							//$ls_gerencia=$io_report->rs_detalle->fields["denger"];
							$lb_valido=$io_report->uf_constanciatrabajo_integralpromedio_mensual($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom,$li_sueintper_mensual,

																								 $li_sueproper_mensual); // Obtenemos el detalle del reporte

							$io_numero_letra->setNumero($li_sueintper_mensual);
							$ls_sueintper_mensual=$io_numero_letra->letra();
                                                         $ls_sueintper_mensual=str_replace('BOLIVARES',' ',$ls_sueintper_mensual);
							$li_sueintper_mensual=$io_fun_nomina->uf_formatonumerico($li_sueintper_mensual);
							$ls_sueintper_mensual=$ls_sueintper_mensual." (".$ls_prefijo." ".$li_sueintper_mensual.")";

							$io_numero_letra->setNumero($li_sueproper_mensual);
							$ls_sueproper_mensual=$io_numero_letra->letra();
							$li_sueproper_mensual=$io_fun_nomina->uf_formatonumerico($li_sueproper_mensual);
							$ls_sueproper_mensual=$ls_sueproper_mensual." (".$ls_prefijo." ".$li_sueproper_mensual.")";
							$ls_contenido=str_replace("$ls_ente",$ls_ente,$ls_contenido);
                                                        $io_numero_letra->setNumero($ls_dia_act);
                                                        $ls_dia_letra=$io_numero_letra->letra();
                                                        $ls_dia_letra=str_replace(' CON 00/100 BOLIVARES','',$ls_dia_letra);
                                                        if ($ls_dia_letra == 'UNO')
                                                                $ls_dia_letra='UN';

                                                        $ls_contenido=str_replace('$ls_dia',strtolower($ls_dia_letra).'('.$ls_dia_act.')',$ls_contenido);
                                                        $ls_contenido=str_replace('$ls_mes',strtolower($ls_mes_act),$ls_contenido);
                                                        $io_numero_letra->setNumero($ls_ano_act);
                                                        $ls_anio_letra=$io_numero_letra->letra();
                                                        $ls_anio_letra=str_replace(' CON 00/100 BOLIVARES','',$ls_anio_letra);
                                                        $ls_contenido=str_replace('$ls_ano',strtolower($ls_anio_letra).'('.$ls_ano_act.')',$ls_contenido);
							$ls_contenido=str_replace('$ls_nombres','<b>'.$ls_nomper.'</b>',$ls_contenido);
                                                        $ls_contenido=str_replace('$ls_apellidos','<b>'.$ls_apeper.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$ls_cedula','<b>'.number_format($ls_cedper,0,'','.').'</b>',$ls_contenido);
							$ls_contenido=str_replace('$ls_cargo','<b>'.$ls_descar.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$li_sueldo','<b>'.$ls_sueper.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$ld_fecha_ingreso','<b>'.$ls_fechaingreso.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$ld_fecha_egreso','<b>'.$ls_fechaegreso.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$ls_direccion',$ls_dirper,$ls_contenido);
							$ls_contenido=str_replace('$ld_fecha_nacimiento','<b>'.$ld_fecnacper.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$ls_edo_civil','<b>'.$ls_edocivper.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$ls_nacionalidad','<b>'.$ls_nacper.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$ls_telefono_hab',$ls_telhabper,$ls_contenido);
							$ls_contenido=str_replace('$ls_telefono_mov',$ls_telmovper,$ls_contenido);
							$ls_contenido=str_replace('$ls_unidad_administrativa','<b>'.$ls_desuniadm.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$li_horas_lab',$li_horper,$ls_contenido);
							$ls_contenido=str_replace('$li_inte_sueldo','<b>'.$ls_sueintper.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$li_salario_normal','<b>'.$ls_salnorper.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$li_prom_sueldo','<b>'.$ls_sueproper.'<b>',$ls_contenido);
							$ls_contenido=str_replace('$ls_dedicacion','<b>'.$ls_desded.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$ls_tipo_personal',$ls_destipper,$ls_contenido);
							$ls_contenido=str_replace('$ls_tipo_nomina',$ls_tipnom,$ls_contenido);
							$ls_contenido=str_replace('$li_mensual_inte_sueldo','<b>'.$ls_sueintper_mensual.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$li_mensual_prom_sueldo','<b>'.$ls_sueproper_mensual.'</b>',$ls_contenido);
							$ls_contenido=str_replace('$ls_fecjub',$ls_fecjub,$ls_contenido);
							//$ls_contenido=str_replace("\$ls_gerencia",$ls_gerencia,$ls_contenido);

							/*$io_pdf->ezText($ls_contenido,$li_tamletcont,array('justification' =>'full','spacing' =>$li_intlincont));
							$li_pos=($li_marinfcont*10)*(72/25.4);

							$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$ls_piepagcont,'center');
							$li_pos=$li_pos-$li_tamletpiecont;
							$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');
							$li_pos=$li_pos-$li_tamletpiecont;
							$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');*/
                                                        /*$ls_contenido=chop($ls_contenido);
                                                        $ls_contenido = preg_replace("/ +/"," ",$ls_contenido);
                                                        $ls_contenido=preg_replace('/\s\s+/',' ',$ls_contenido);*/

                                                        $html.='<TABLE style="border:0px solid;width:730px;" >';
                                                        $html.='<TR>';
                                                                $html.='<TD style="'.$fonfamily_css.'font-size:'.
                                                                $li_tamletcont.';border:0px solid; color:black;line-height:'.$li_intlincont.';text-align:justify" >
                                                             '.utf8_encode($ls_contenido).'</TD>';
                                                        $html.='</TR>';
                                                        $html.='</TABLE>';
                                                        $html.='<br>';
                                                        $html.='<br>';
                                                        $html.='<TABLE style="border:0px solid;height:15px;" celspacing="0" celpaddign="0" >';
                                                        $html.='<TR>';

                                                                $html.='<TD style="'.$fonfamily_css.'font-size:'.$li_tamletpiecont.';width:730px;border:0px solid; color:black;text-decoration:none;text-align:justify;" >'.$ls_piepagcont.'</TD>';
                                                        $html.='</TR>';
                                                        $html.='</TABLE>';


							$io_report->rs_detalle->MoveNext();
                                                     //   echo $htlm; die();
                                                       if(!$io_report->rs_detalle->EOF)
                                                        {
                                                            $html.='<page pageset="old"></page>';
                                                        }
						}
					}
					$io_report->rs_data->MoveNext();
				}
			}// fin del else
			if (($i+1)<$li_total)
			{
				//$io_pdf->ezNewPage(); // Insertar una nueva pï¿½gina

                                $html.='<page  pagesetter="old"></page>';
			}

		}// fin del for($i=1;$i<=$li_total;$i++)

		if($lb_valido) // Si no ocurrio ningï¿½n error
		{

			//$io_pdf->ezStream(); // Mostramos el reporte
                        $html.='</page>';
                        echo $html;
                        $content = ob_get_clean();
                        require_once('../../html2pdf/html2pdf.class.php');
                        $html2pdf = new HTML2PDF('P','Letter','es');
                        $html2pdf->setTestTdInOnePage(false);
                        $html2pdf->WriteHTML($content, isset($_GET['vuehtml']));
                        $html2pdf->Output('constancia.pdf');
		}
		else  // Si hubo algï¿½n error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');");
			print(" close();");
			print("</script>");
		}
		unset($io_pdf);
	}//fin del else
?> 