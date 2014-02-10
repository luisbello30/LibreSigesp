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
	function uf_insert_seguridad($as_titulo,$as_titulo2)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/08/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Gener� el Reporte ".$as_titulo." ".$as_titulo2;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_retencion_islr.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	require_once("../../shared/class_folder/class_fecha.php");
	$io_fecha=new class_fecha();
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>XML de Relaci�n de Retenci�n I.S.L.R.</b>";
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_ano=$io_fun_nomina->uf_obtenervalor_get("ano","");
	$ls_mes=$io_fun_nomina->uf_obtenervalor_get("mes","");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_porcentajecero=$io_fun_nomina->uf_obtenervalor_get("porcentajecero","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_rifempresa=str_replace('-','',$io_fun_nomina->uf_obtenervalor_get("rifempresa","1"));
	$ls_titulo2="<b>A�o</b> ".$ls_ano." <b>Mes</b> ".$io_fecha->uf_load_nombre_mes($ls_mes);
	$ls_tiporeporte=$io_fun_nomina->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_snorh_class_reportbsf.php");
		$io_report=new sigesp_snorh_class_reportbsf();
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_titulo2); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_retencionislr_personal($ls_codnomdes,$ls_codnomhas,$ls_ano,$ls_mes,$ls_conceptocero,$ls_orden,$ls_porcentajecero); // Cargar el DS con los datos del reporte
	}
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		$ls_nombrearchivo="../xml/islr/XML_relacionRetencionesISLR_.xml";
		error_reporting(E_ALL);
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.25,2.5,3,3); // Configuraci�n de los margenes en cent�metros
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n�mero de p�gina
		$li_totrow=$io_report->DS->getRowCount("codper");
		$li_s=0;

			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo existente para crearlo nuevo.
				{
					$lb_valido=false;
				}
				else
				{
					$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
				//escritura del encabezado del archivo XML
					$ls_cadena='';
					$ls_cadena='<?xml version="1.0" encoding="utf-8" ?>'."\r\n";
					$ls_cadena.='<RelacionRetencionesISLR RifAgente="'.$ls_rifempresa.'" Periodo="'.$ls_ano.$ls_mes.'">'."\r\n";

					if ($ls_creararchivo)
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				//escritura del encabezado del archivo XML
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
			$ls_cadena='';
			$ls_cedper=$io_report->DS->data["cedper"][$li_i];
			$ls_nomper=$io_report->DS->data["apeper"][$li_i].", ".$io_report->DS->data["nomper"][$li_i];
			//$li_porisr=$io_fun_nomina->uf_formatonumerico(($io_report->DS->data["porisr"][$li_i]*100));
			$li_porisr=str_replace(',','.',$io_fun_nomina->uf_formatonumerico(($io_report->DS->data["porisr"][$li_i])));
			$li_arc=str_replace(',','.',str_replace('.','',$io_fun_nomina->uf_formatonumerico($io_report->DS->data["arc"][$li_i])));
			$retencion=($io_report->DS->data["arc"][$li_i]*$io_report->DS->data["porisr"][$li_i])/100;
			$retencion=number_format($retencion,2,',','.');
			$li_islr=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS->data["islr"][$li_i]));
			$ls_rifper=str_replace('-','',$io_report->DS->data["rifper"][$li_i]);
			if($ls_conceptocero==1)
			{
				if($li_arc<>"0,00")
				{
					$li_s=$li_s+1;
					$la_data[$li_s]=array('nro'=>$li_i,'cedula'=>$ls_cedper,'nombre'=>$ls_nomper,'monto'=>$li_arc,'porcentaje'=>$li_porisr,'retencion'=>$retencion);
				}
			}
			else
			{
				$li_s=$li_s+1;
				$la_data[$li_s]=array('nro'=>$li_i,'cedula'=>$ls_cedper,'nombre'=>$ls_nomper,'monto'=>$li_arc,'porcentaje'=>$li_porisr,'retencion'=>$retencion);
			}
				$ls_cadena='<DetalleRetencion>'."\r\n";
				$ls_cadena.="\t".'<RifRetenido>'.$ls_rifper.'</RifRetenido>'."\r\n";
				$ls_cadena.="\t".'<NumeroFactura>'.'00000000'.'</NumeroFactura>'."\r\n";
				$ls_cadena.="\t".'<NumeroControl>'.$li_s.'</NumeroControl>'."\r\n";
				$ls_cadena.="\t".'<CodigoConcepto>'.'001'.'</CodigoConcepto>'."\r\n";
				$ls_cadena.="\t".'<MontoOperacion>'.$li_arc.'</MontoOperacion>'."\r\n";
				$ls_cadena.="\t".'<PorcentajeRetencion>'.$li_porisr.'</PorcentajeRetencion>'."\r\n";
				$ls_cadena.='</DetalleRetencion>'."\r\n";
				      //escritura del cuerpo del archivo XML
					if ($ls_creararchivo)
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				      //escritura del cuerpo del archivo XML
		}     
		//escritura del pie del archivo XML
			$ls_cadena='';
			$ls_cadena.='</RelacionRetencionesISLR>';

			if ($ls_creararchivo)
			{
				if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
				{
					$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			else
			{
				$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
				$lb_valido = false;
			}
		//escritura del pie del archivo XML
		if($li_s==0)
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		if ($lb_valido)
		{
			@fclose($ls_creararchivo); //cerramos la conexi�n y liberamos la memoria
                        //echo "<script>alert('sssss')</script>";die();
			print("<script language=JavaScript>");
			print(" alert('El archivo ha sido creado');"); 
			print(" pagina='../sigesp_sno_cat_descarga.php?file=XML_relacionRetencionesISLR_.xml&enlace=xml/islr/';"); 
			print(" window.open(pagina,'catalogo','menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no');");
                        print(" close();");
			print("</script>");
		}
		else
		{
			@fclose($ls_creararchivo); //cerramos la conexi�n y liberamos la memoria
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el archivo');"); 
			print(" close();");
			print("</script>");
		}
	}
?>