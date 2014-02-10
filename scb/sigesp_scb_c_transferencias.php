<?php
class sigesp_scb_c_transferencias
{
	var $io_sql;
	var $fun;
	var $msg;
	var $is_msg_error;	
	var $dat;
	var $io_sql_aux;
	var $la_security;
	function sigesp_scb_c_transferencias($aa_security)
	{
		require_once("../shared/class_folder/class_sql.php");
		require_once("../shared/class_folder/class_funciones.php");
		require_once("../shared/class_folder/class_mensajes.php");
		require_once("../shared/class_folder/sigesp_include.php");
		require_once("sigesp_scb_c_movbanco.php");
		require_once("../shared/class_folder/class_fecha.php");
		$this->io_fecha=new class_fecha();		
		$this->io_class_movbco=new sigesp_scb_c_movbanco($aa_security);
		$sig_inc=new sigesp_include();
		$con=$sig_inc->uf_conectar();
		$this->io_sql=new class_sql($con);
		$this->io_sql_aux=new class_sql($con);
		$this->fun=new class_funciones();
		$this->msg=new class_mensajes();
		$this->dat=$_SESSION["la_empresa"];	
		$this->la_security=$aa_security;
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad=new sigesp_c_seguridad();
		require_once("../shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->rs_data ="";			
	}

	function uf_procesar_transferencia($arr_data,$arr_datadestino,$aa_seguridad)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:	    uf_procesar_tranferencia
	// Access:			public
	//	Returns:		Boolean Retorna si proceso correctamente
	//	Description:	Funcion que se encarga de guardar los detalles de la transferencia
	//////////////////////////////////////////////////////////////////////////////
	
		$ls_codemp=$this->dat["codemp"];
		$li_total=count($arr_data["numtra"]);
//	var_dump($arr_data);die();
		for($li_i=1 ; $li_i<=$li_total ; $li_i++)//for datos de origen
		{
			$ls_numtrans=$arr_data["numtra"][$li_i];
			$ls_codban=$arr_data["Codban"][$li_i];
			$ls_cuenta_banco=$arr_data["Ctaban"][$li_i];
			$ls_numdoc=$arr_data["numdoc"][$li_i];
			$ls_codope=$arr_data["codope"][$li_i];
			$ld_fecha=$arr_data["fecmov"][$li_i];
			$ls_conmov=$arr_data["concepto"][$li_i];
			$ls_cedbene=$arr_data["ced_bene"][$li_i];
			$ls_codpro =$arr_data["cod_prov"][$li_i];
			$ls_debhab =$arr_data["debhab"][$li_i]; 
			$ls_cuenta_scg=$arr_data["scg_cuenta"][$li_i];
			$ls_nomproben=$arr_data["nomproben"][$li_i];
			$ls_estmov=$arr_data["estmov"][$li_i];	
			$ldec_monto=$arr_data["monto"][$li_i];
			$ldec_monobjret=$arr_data["monobjret"][$li_i];
			$ldec_monret=$arr_data["monret"][$li_i];
			$ls_chevau=$arr_data["chevau"][$li_i];
			$ls_estbpd=$arr_data["estbpd"][$li_i];
			$ls_procede=$arr_data["procede_doc"][$li_i];
			$ls_estmovint=$arr_data["estmovint"][$li_i];
			$ls_codded=$arr_data["codded"][$li_i];
			$ld_feccon="1900/01/01";
			if($li_i==1)	
			{
				$lb_existe=$this->uf_select_documento($ls_codemp,$ls_numtrans,$ls_codope);
				if($lb_existe)
				{
					$this->is_msg_error="Numero de Documento ".$ls_numtrans." ya existe, introduzca un nuevo numero";
					return false;
				}
					if ($ls_codope!='CH')
					{
						//Genera Numero
						require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
						$io_keygen= new sigesp_c_generar_consecutivo();
						$ls_numcontint= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","numconint","SCBBRE",15,"valinimovban","","");
						if($ls_numcontint===false)
						{
							 print "<script language=JavaScript>";
							 print "location.href='sigespwindow_blank.php'";
							 print "</script>";  
						}
						unset($io_keygen);
						//Genera Numeros
					}
					$lb_valido=$this->io_class_movbco->uf_guardar_automatico($ls_codban,$ls_cuenta_banco,$ls_numtrans,$ls_codope,$ld_fecha,$ls_conmov,'---',$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,0,1,$ls_estbpd,$ls_procede,' ','N','-','--','','',0,$ls_numcontint);
					if(!$lb_valido)
					{
						return false ;
					}
			}
			if ($lb_valido)
			   {
			     $lb_valido = true;
				 $ls_sql="INSERT INTO scb_movbco_scg(codemp,codban,ctaban,numdoc,codope,estmov,scg_cuenta,debhab,codded,documento,desmov,procede_doc,monto,monobjret)
							   VALUES('".$ls_codemp."','".$ls_codban."','".$ls_cuenta_banco."','".$ls_numtrans."','".$ls_codope."','".$ls_estmov."','".$ls_cuenta_scg."','".$ls_debhab."','".$ls_codded."','".$ls_numdoc."','".$ls_conmov."','SCBTRA',".$ldec_monto.",".$ldec_monobjret.")";		
				 $li_result=$this->io_sql->execute($ls_sql);
				 if (($li_result===false))//3
					{
					  $lb_valido=false;
					  $this->is_msg_error="Error en insert scb_movbco_scg,".$this->fun->uf_convertirmsg($this->io_sql->message);		
					}
			   }
		}
		if($lb_valido)
		{
			$li_total=count($arr_datadestino["numtra"]);
			
			for($li_i=1 ; $li_i<=$li_total ; $li_i++)//for datos destino
			{
				$ls_numtrans=$arr_datadestino["numtra"][$li_i];
				$ls_codban=$arr_datadestino["Codban"][$li_i];
				$ls_cuenta_banco=$arr_datadestino["Ctaban"][$li_i];
				$ls_numdoc=$arr_datadestino["numdoc"][$li_i];
				$ls_codope=$arr_datadestino["codope"][$li_i];
				$ld_fecha=$arr_datadestino["fecmov"][$li_i];
				$ls_conmov=$arr_datadestino["concepto"][$li_i];
				$ls_cedbene=$arr_datadestino["ced_bene"][$li_i];
				$ls_codpro =$arr_datadestino["cod_prov"][$li_i];
				$ls_nomproben=$arr_datadestino["nomproben"][$li_i];
				$ls_estmov=$arr_datadestino["estmov"][$li_i];	
				$ldec_monto=$arr_datadestino["monto"][$li_i];
				$ldec_monobjret=$arr_datadestino["monobjret"][$li_i];
				$ldec_monret=$arr_datadestino["monret"][$li_i];
				$ls_chevau=$arr_datadestino["chevau"][$li_i];
				$ls_estbpd=$arr_datadestino["estbpd"][$li_i];
				$ls_procede=$arr_datadestino["procede_doc"][$li_i];
				$ls_estmovint=$arr_datadestino["estmovint"][$li_i];
				$ls_codded=$arr_datadestino["codded"][$li_i];
				$ld_feccon="1900/01/01";
				$lb_existe=$this->uf_select_documento($ls_codemp,$ls_numtrans,$ls_codope);
				if($lb_existe)
				{
					$this->is_msg_error="Numero de Documento ".$ls_numtrans." ya existe, introduzca un nuevo numero";
					return false;
				}
				//Genera Numero
				require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
				$io_keygen= new sigesp_c_generar_consecutivo();
				$ls_numcontint= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","numconint","SCBBRE",15,"valinimovban","","");
				if($ls_numcontint===false)
				{
					 print "<script language=JavaScript>";
					 print "location.href='sigespwindow_blank.php'";
					 print "</script>";  
				}
				unset($io_keygen);
				//Genera Numeros
				$lb_valido=$this->io_class_movbco->uf_guardar_automatico($ls_codban,$ls_cuenta_banco,$ls_numtrans,$ls_codope,$ld_fecha,$ls_conmov,'---',$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,0,1,$ls_estbpd,$ls_procede,' ','N','-','--','','',0,$ls_numcontint);
				if(!$lb_valido)
				{
					$this->is_msg_error = $this->io_class_movbco->is_msg_error;
					return false;
				}
			
				if(!$lb_valido)
				{
					break;
				}
			}
			
		}
		return $lb_valido;
	
	}//Fin de  uf_procesar_emision_chq
	
	function uf_select_ctaauxiliar($ls_cta,$ls_dencta)
	{
		$ls_sql="SELECT TRIM(sc_cuenta) as sc_cuenta, denominacion 
		           FROM scg_cuentas 
				  WHERE sc_cuenta like '1110101%'
				    AND status='C'";
		$rs_cuentas=$this->io_sql->select($ls_sql);
		if(($rs_cuentas===false))
		{
			$this->is_msg_error="Error en consulta.".$this->fun->uf_convertirmsg($this->io_sql->message);
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_cuentas))
			{
				$ls_cta=$row["sc_cuenta"];
				$ls_dencta=$row["denominacion"];
			}
			else
			{
				$ls_cta="";
				$ls_dencta="";
			}
		}		
	}	
	
	function uf_select_documento($ls_codemp,$ls_numdoc,$ls_codope)
	{
		$ls_sql="SELECT numdoc
				 FROM scb_movbco
				 WHERE codemp='".$ls_codemp."' AND numdoc='".$ls_numdoc."' AND codope='".$ls_codope."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if(($rs_data===false))
		{
			return false;	
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=true;
			}
			else
			{
				$lb_existe=false;				
			}	
			$this->io_sql->free_result($rs_data);
		}	
		return $lb_existe;		
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_declaracionxml($as_mesdes,$as_meshas,$as_year,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_declaracionxml
		//         Access: public  
		//	    Arguments: as_quincena // Quincena del cual se van a generar los txt
		//	    		   as_mes      // Mes del cual se van a generar los txt
		//	    		   as_anio     // A�o del cual se van a generar los txt
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: Funci�n que genera los txt de la declaraci�n informativa
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 15/07/2007									Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
        $ls_rifemp=$_SESSION["la_empresa"]["rifemp"];
		$ls_mesdesaux=intval($as_mesdes);
		$ls_meshasaux=intval($as_meshas);
		for($li_i=$ls_mesdesaux;$li_i<=$ls_meshasaux;$li_i++)
		{
			$ls_periodo=str_pad($li_i,2,"0",0);  
			$ld_fechadesde=$as_year."-".$ls_periodo."-01";
			$ld_fechahasta=$as_year."-".$ls_periodo."-".substr($this->io_fecha->uf_last_day($ls_periodo,$as_year),0,2);
			$ls_ruta="declaracion";
			@mkdir($ls_ruta,0755);
			$ls_archivo="declaracion/Declaracion_Salarios_y_otras_R_".$ls_periodo."-".date("Y_m_d_H_i").".xml";
			$ls_archivo2="declaracion/ERROR_Declaracion_Salarios_y_otras_R_".$ls_periodo."-".date("Y_m_d_H_i").".txt";
			$lo_archivo=fopen("$ls_archivo","a+");	
			$lo_archivo2=fopen("$ls_archivo2","a+");	
			$rs_datac=$this->uf_declaracion_xml_cabecera($ld_fechadesde,$ld_fechahasta,$ls_periodo,$as_year);
			$ls_contenido='<?xml version="1.0" encoding="utf-8"?>'; 
			$ls_contenido.='<RelacionRetencionesISLR RifAgente="'.$ls_rifemp.'" Periodo="'.$as_year.$ls_periodo.'">'; 
			$ls_cadena="";			
			while(!$rs_datac->EOF)
			{
				$ls_rifpro=trim($rs_datac->fields["rifpro"]);
				$ls_rifben=trim($rs_datac->fields["rifben"]);
				if($ls_rifpro!="")
				{
					$ls_rif=$ls_rifpro;
				}
				else
				{
					$ls_rif=$ls_rifben;
				}
				$ls_numrecdoc=trim($rs_datac->fields["numrecdoc"]);
				$ls_numrecdoc=substr($ls_numrecdoc,-10);
				$ls_numref=trim($rs_datac->fields["numref"]);
				if($ls_numref=="")
				{
					$ls_numref="NA";
				}
				$li_baseimp=number_format($rs_datac->fields["baseimp"],2,'.','');
				$ls_codconret=trim($rs_datac->fields["codconret"]);
				$ls_codper=trim($rs_datac->fields["codper"]);
				$li_porded=number_format($rs_datac->fields["porded"],2,'.','');
				$ls_procedencia=trim($rs_datac->fields["procedencia"]);
				$correcto=true;
				if ($ls_procedencia=='CXP')
				{
					if ((trim($ls_rif)==""))
					{
						$ls_cadena=$ls_cadena."La factura ".$ls_numrecdoc." no se pudo agregar ya que el proveedor/beneficiario asociado no posee rif. \r\n";
						$correcto=false;
					}
					if ((trim($ls_codconret)==""))
					{
						$ls_cadena=$ls_cadena."La factura ".$ls_numrecdoc." no se pudo agregar ya que la deducci�n no posee Concepto de Retenci�n asociado. \r\n";
						$correcto=false;
					}
				}
				if ($ls_procedencia=='SNO')
				{
					if ((trim($ls_rif)==""))
					{
						$ls_cadena=$ls_cadena."El personal  ".$ls_codper." no se pudo agregar ya que no posee rif. \r\n";
						$correcto=false;
					}
					if ((trim($ls_codconret)==""))
					{
						$ls_cadena=$ls_cadena."El personal  ".$ls_codper." no se pudo agregar ya que la deducci�n no posee Concepto de Retenci�n asociado. \r\n";
						$correcto=false;
					}
					if (($li_porded==0))
					{
						$ls_cadena=$ls_cadena."El personal  ".$ls_codper." no se pudo agregar ya que el porcentaje de deducci�n es cero. \r\n";
						$correcto=false;
					}
				}
				if($correcto)
				{
					$ls_contenido.='<DetalleRetencion>';
					$ls_contenido.='<RifRetenido>'.$ls_rif.'</RifRetenido>';
					$ls_contenido.='<NumeroFactura>'.$ls_numrecdoc.'</NumeroFactura>';
					$ls_contenido.='<NumeroControl>'.$ls_numref.'</NumeroControl>';
					$ls_contenido.='<CodigoConcepto>'.$ls_codconret.'</CodigoConcepto>';
					$ls_contenido.='<MontoOperacion>'.$li_baseimp.'</MontoOperacion>';
					$ls_contenido.='<PorcentajeRetencion>'.$li_porded.'</PorcentajeRetencion>';
					$ls_contenido.='</DetalleRetencion>';
				}
				$rs_datac->MoveNext();
			}
			$ls_contenido.='</RelacionRetencionesISLR>';
			@fwrite($lo_archivo,$ls_contenido);
			@fwrite($lo_archivo2,$ls_cadena);
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="PROCESS";
				$ls_descripcion ="Genero el xml de Declaraci�n de sueldos y otras remuneraciones para el periodo ".
								 $as_mesdes." a ".$as_meshas." del a�o ".$as_year." en el Archivo ".$ls_archivo.
								 " Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////					
			}
		}
		return $lb_valido;
	}// end function uf_declaracioninformativa
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_declaracion_xml_cabecera($as_fecemidocdes,$as_fecemidochas,$as_periodo,$as_year)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_declaracion_xml_cabecera
		//         Access: public 
		//      Argumento: as_fecemidocdes // Parametro de busqueda Fecha Desde
		//				   as_fecemidochas // Parametro de busqueda Fecha Hasta
		//	      Returns: Retorna un Datastored
		//    Description: Funcion que obtiene los datos para la declaracion de salarios y otras remuneraciones
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 05/06/2009									Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_criterio="";
		$rs_data="";
		if($as_fecemidocdes!="")
		{
			$ls_criterio=$ls_criterio." AND cxp_rd.fecemidoc>='".$as_fecemidocdes."'";
		}
		if($as_fecemidochas!="")
		{
			$ls_criterio=$ls_criterio." AND cxp_rd.fecemidoc<='".$as_fecemidochas."'";
		}
		$ls_sql="SELECT '' AS codper, rpc_proveedor.rifpro, rpc_beneficiario.rifben,cxp_rd.numrecdoc, cxp_rd.numref,".
				" 		(cxp_rd.montotdoc-cxp_rd.mondeddoc+cxp_rd.moncardoc) as baseimp,".
				"       sigesp_deducciones.codconret ,sigesp_deducciones.porded,sigesp_deducciones.codded, 'CXP' AS procedencia".
				"  FROM cxp_rd, cxp_rd_deducciones, sigesp_deducciones, rpc_proveedor, rpc_beneficiario ".
				" WHERE cxp_rd.codemp = '".$this->ls_codemp."' ".
				"   AND sigesp_deducciones.islr = 1 ".
				"   AND cxp_rd.estprodoc='C'".
				$ls_criterio.
				"   AND cxp_rd.codemp = cxp_rd_deducciones.codemp ".
				"   AND cxp_rd.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"   AND cxp_rd.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"   AND cxp_rd.ced_bene = cxp_rd_deducciones.ced_bene ".
				"   AND cxp_rd.cod_pro = cxp_rd_deducciones.cod_pro ".
				"   AND cxp_rd_deducciones.codemp = sigesp_deducciones.codemp ".
				"   AND cxp_rd_deducciones.codded = sigesp_deducciones.codded ".
				"   AND cxp_rd.codemp = rpc_proveedor.codemp ".
				"   AND cxp_rd.cod_pro = rpc_proveedor.cod_pro ".
				"   AND cxp_rd.codemp = rpc_beneficiario.codemp ".
				"   AND cxp_rd.ced_bene = rpc_beneficiario.ced_bene ".
				" UNION ".
				"SELECT sno_personal.codper, MAX(sno_personal.rifper) AS rifpro,'' AS rifben,'0' AS numrecdoc,'' AS numref, SUM(sno_hsalida.valsal), ".
				"	   MAX(sno_personalisr.codconret) AS codconret, MAX(sno_personalisr.porisr) AS porded, sno_personalisr.codisr AS codded, 'SNO' AS procedencia ".
				"  FROM sno_hsalida, sno_personalisr, sno_personal, sno_hperiodo,sno_hconcepto ".
				" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
				"   AND SUBSTR(sno_hperiodo.fecdesper,1,4) = '".$as_year."' ".
				"   AND SUBSTR(sno_hperiodo.fecdesper,6,2) = '".$as_periodo."' ".
				"   AND sno_personalisr.codisr = '".$as_periodo."'  ".
				"   AND sno_hconcepto.aplarccon = 1  ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp  ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur  ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi  ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom  ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc  ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp  ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur  ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi  ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom  ".
				"   AND sno_hsalida.codemp = sno_personalisr.codemp  ".
				"   AND sno_hsalida.codper = sno_personalisr.codper  ".
				"   AND sno_personal.codemp = sno_personalisr.codemp  ".
				"   AND sno_personal.codper = sno_personalisr.codper  ".
				" GROUP BY sno_personal.codper, sno_personalisr.codisr ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{		 
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_declaracion_xml_cabecera ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;		
	}// end function uf_arc_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_gennotdebcrexml($ad_fechadesde,$ad_fechahasta,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_declaracionxml
		//         Access: public  
		//	    Arguments: as_quincena // Quincena del cual se van a generar los txt
		//	    		   as_mes      // Mes del cual se van a generar los txt
		//	    		   as_anio     // A�o del cual se van a generar los txt
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: Funci�n que genera los txt de la declaraci�n informativa
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 15/07/2007									Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
        $ls_rifemp=$_SESSION["la_empresa"]["rifemp"];
		$ls_ruta="banco/CREDITO_DEBITO/procesado";
		@mkdir($ls_ruta,0777);
		$ls_archivo="banco/CREDITO_DEBITO/procesado/ISAF001.xml";
		$lo_archivo=fopen("$ls_archivo","a+");	
		$rs_datac=$this->uf_gennotdebcrexml_cabecera($ad_fechadesde,$ad_fechahasta);
		$ls_contenido='<?xml version="1.0" encoding="utf-8"?>'; 
		$ls_contenido.='<COMPROBANTES>'; 
		$ls_cadena="";			
		$li_totrows = $this->io_sql->num_rows($rs_datac);
		if ($li_totrows > 0)
		{
			while(!$rs_datac->EOF)
			{
				$ls_nrocia="00";
				$ls_montot=number_format($rs_datac->fields["monto"],2,'.','');
				$ls_nrocta=trim($rs_datac->fields["ctaban"]);
				$ls_fechamov=trim($rs_datac->fields["fecmov"]);
				$ls_diamov=substr($ls_fechamov,8,2);
				$ls_mesmov=substr($ls_fechamov,5,2);
				$ls_anomov=substr($ls_fechamov,0,4);
				$ls_referz="0000";
				$ls_cr="00";
				$ls_status="0";
				$ls_nada="0";
				$ls_libre="";
				$ls_proxm="0000";
				$ls_coderr="0000";
				$ls_consec="00000000";
				$ls_scodban=trim($rs_datac->fields["codban"]);
				$ls_snumdoc=trim($rs_datac->fields["numdoc"]);
				$ls_scodope=trim($rs_datac->fields["codope"]);
				$lb_boolean=$this->uf_cambio_estatus_estaxmlbs($ls_scodban,$ls_nrocta,$ls_snumdoc,$ls_scodope);
				$correcto=true;
				if($correcto)
				{
					$ls_contenido.='<comprobante>';
					$ls_contenido.='<nrocia>'.$ls_nrocia.'</nrocia>';
					$ls_contenido.='<montot>'.$ls_montot.'</montot>';
					$ls_contenido.='<nrocta>'.$ls_nrocta.'</nrocta>';
					$ls_contenido.='<diamov>'.$ls_diamov.'</diamov>';
					$ls_contenido.='<mesmov>'.$ls_mesmov.'</mesmov>';
					$ls_contenido.='<anomov>'.$ls_anomov.'</anomov>';
					$ls_contenido.='<referz>'.$ls_referz.'</referz>';
					$ls_contenido.='<cr>'.$ls_cr.'</cr>';
					$ls_contenido.='<status>'.$ls_status.'</status>';
					$ls_contenido.='<nada>'.$ls_nada.'</nada>';
					$ls_contenido.='<libre>'.$ls_libre.'</libre>';
					$ls_contenido.='<proxm>'.$ls_proxm.'</proxm>';
					$ls_contenido.='<coderr>'.$ls_coderr.'</coderr>';
					$ls_contenido.='<consec>'.$ls_consec.'</consec>';
					$ls_contenido.='<codban>'.$ls_scodban.'</codban>';
					$ls_contenido.='<ctaban>'.$ls_nrocta.'</ctaban>';
					$ls_contenido.='<numdoc>'.$ls_snumdoc.'</numdoc>';
					$ls_contenido.='<codope>'.$ls_scodope.'</codope>';
					$ls_contenido.='</comprobante>';
				}
				$rs_datac->MoveNext();
			 }
			}
			else
			{
				$this->io_mensajes->message("No hay data para generar el XML!");
				/*if (file_exists ($ls_archivo))
				{
					unlink($ls_archivo);
				}*/	  
				return false;
			}
			$ls_contenido.='</COMPROBANTES>';
			@fwrite($lo_archivo,$ls_contenido);
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="PROCESS";
				$ls_descripcion ="Genero el xml de Notas de debito o credito ISAF001 en las fechas del ".
								$ad_fechadesde." al ".$ad_fechahasta." en el Archivo ".$ls_archivo.
								 " Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////					
			}
		
		return $lb_valido;
	}// end function uf_declaracioninformativa
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_gennotdebcrexml_cabecera($ad_fechadesde,$ad_fechahasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_declaracion_xml_cabecera
		//         Access: public 
		//      Argumento: as_fecemidocdes // Parametro de busqueda Fecha Desde
		//				   as_fecemidochas // Parametro de busqueda Fecha Hasta
		//	      Returns: Retorna un Datastored
		//    Description: Funcion que obtiene los datos para la declaracion de salarios y otras remuneraciones
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 05/06/2009									Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_criterio="";
		$rs_data="";
		if($ad_fechadesde!="")
		{
			$ls_criterio=$ls_criterio." AND scb_movbco.fecmov>='".$ad_fechadesde."'";
		}
		if($ad_fechahasta!="")
		{
			$ls_criterio=$ls_criterio." AND scb_movbco.fecmov<='".$ad_fechahasta."'";
		}
		$ls_sql=" SELECT scb_movbco.codemp,scb_movbco.codban,scb_movbco.ctaban,scb_movbco.numdoc,scb_movbco.numordpagmin, ".
				" scb_movbco.codope,scb_movbco.estmov,scb_movbco.cod_pro,scb_movbco.ced_bene, ".
				" scb_movbco.tipo_destino,scb_movbco.codconmov,scb_movbco.fecmov,scb_movbco.conmov, ".
				" scb_movbco.nomproben,scb_movbco.monto,scb_movbco.estcon,scb_movbco.estcobing, ".
				" scb_movbco.chevau,scb_movbco.estimpche,scb_movbco.monobjret,scb_movbco.monret, ".
				" scb_movbco.procede,scb_movbco.estmovint,scb_movbco.numcarord,scb_banco.nomban, ".
				" scb_ctabanco.dencta,scb_ctabanco.sc_cuenta,scb_cheques.numchequera,scb_movbco.codtipfon,scb_movbco.estmovcob,scb_movbco.numconint, ".
				"	(SELECT MAX(codfuefin) ".
				"	FROM scb_movbco_fuefinanciamiento ".
				"	WHERE codemp = scb_movbco.codemp ".
				"	AND codban = scb_movbco.codban ".
				"	AND ctaban = scb_movbco.ctaban ".
				"	AND numdoc = scb_movbco.numdoc ".
				"	AND codope = scb_movbco.codope ".
				"	AND estmov = scb_movbco.estmov) AS fuentefinanciamiento, ".
				"	(SELECT scb_tipofondo.dentipfon ".
				"   FROM scb_tipofondo ".
				"	WHERE scb_tipofondo.codemp=scb_movbco.codemp ".
				"	AND scb_tipofondo.codtipfon=scb_movbco.codtipfon) as dentipfon ".
				" FROM scb_movbco LEFT JOIN scb_cheques ".
				" ON scb_movbco.codemp=scb_cheques.codemp ".
				" AND scb_movbco.codban=scb_cheques.codban ".
				" AND scb_movbco.ctaban=scb_cheques.ctaban ".
				" AND scb_movbco.numdoc=scb_cheques.numche, scb_banco, scb_ctabanco ".
				" WHERE scb_movbco.codemp='".$this->ls_codemp."' ".
				" AND scb_movbco.codope<>'OP' ".
				" AND scb_movbco.estmov ='N' ".
				" AND (scb_movbco.codope = 'NC' OR scb_movbco.codope = 'ND') ".
				" AND scb_movbco.estxmlibs = '0' ".
				$ls_criterio.
				" AND scb_movbco.codemp=scb_banco.codemp ".
				" AND scb_movbco.codban=scb_banco.codban ".
				" AND scb_movbco.codemp=scb_ctabanco.codemp ".
				" AND scb_movbco.codban=scb_ctabanco.codban ".
				" AND scb_movbco.ctaban=scb_ctabanco.ctaban";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{		 
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_gennotdebcrexml_cabecera ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;		
	}// end function uf_arc_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_gencheqgerxml($ad_fecdes,$ad_fechas,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_declaracionxml
		//         Access: public  
		//	    Arguments: as_quincena // Quincena del cual se van a generar los txt
		//	    		   as_mes      // Mes del cual se van a generar los txt
		//	    		   as_anio     // A�o del cual se van a generar los txt
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: Funci�n que genera los txt de la declaraci�n informativa
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 15/07/2007									Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
        $ls_rifemp=$_SESSION["la_empresa"]["rifemp"];
		$ls_ruta="banco/CHEQUE_GERENCIA/procesado";
		@mkdir($ls_ruta,0777);
		$ls_archivo="banco/CHEQUE_GERENCIA/procesado/IMPOFC.xml";
		$lo_archivo=fopen("$ls_archivo","a+");	
		$rs_datac=$this->uf_gencheqgerxml_cabecera($ad_fecdes,$ad_fechas);
		$ls_contenido='<?xml version="1.0" encoding="utf-8"?>'; 
		$ls_contenido.='<COMPROBANTES>'; 
		$ls_cadena="";			
		$li_totrows = $this->io_sql->num_rows($rs_datac);
		if ($li_totrows > 0)
		{
			while(!$rs_datac->EOF)
			{
				$ls_impbnk="01";
				$ls_codestpro=trim($rs_datac->fields["codestpro"]);
				$ls_codestpro1=substr($ls_codestpro,0,25);
				$ls_codestpro2=substr($ls_codestpro,25,25);
				$ls_codestpro3=substr($ls_codestpro,50,25);
				$ls_codestpro4=substr($ls_codestpro,75,25);
				$ls_codestpro5=substr($ls_codestpro,100,25);
				$ls_impbrn=$this->uf_buscasucursal($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5);
				$ls_impbrn=trim($ls_impbrn);
				$ls_impccy="BS.";
				$ls_impgln=trim($rs_datac->fields["ctacontable"]);
				$ls_impnch=trim($rs_datac->fields["chevau"]);
				$ls_impmch=number_format($rs_datac->fields["monto"],2,'.','');
				$ls_impusr=$_SESSION["la_logusr"];
				$ls_fechamov=trim($rs_datac->fields["fecmov"]);
				$ls_diamov=substr($ls_fechamov,8,2);
				$ls_mesmov=substr($ls_fechamov,5,2);
				$ls_anomov=substr($ls_fechamov,0,4);
				$ls_impsch=trim($rs_datac->fields["estmov"]);
				$ls_impbnf=trim($rs_datac->fields["nomproben"]);
				$ls_impbnf1="LLENAR";
				$ls_impbnf2="LLENAR";
				$ls_impdtp=trim($rs_datac->fields["conmov"]);
				$ls_impdtp1="LLENAR";
				$ls_impdtp2="LLENAR";
				$ls_impfty="NO EXISTE FORMULA";
				$ls_tipodestino=trim($rs_datac->fields["tipo_destino"]);
				if ($ls_tipodestino == 'P')
				{
					$ls_imprif=trim($rs_datac->fields["rifpro"]);
				}
				else
				{
					$ls_imprif=trim($rs_datac->fields["ced_bene"]);
				}
				$ls_impori="5";
				$ls_scodban=trim($rs_datac->fields["codban"]);
				$ls_snumdoc=trim($rs_datac->fields["numdoc"]);
				$ls_sctaban=trim($rs_datac->fields["ctaban"]);
				$ls_scodope="CH";
				$lb_boolean=$this->uf_cambio_estatus_estaxmlbs($ls_scodban,$ls_sctaban,$ls_snumdoc,$ls_scodope);
				$correcto=true;
				if($correcto)
				{
					$ls_contenido.='<comprobante>';
					$ls_contenido.='<impbnk>'.$ls_impbnk.'</impbnk>';
					$ls_contenido.='<impbrn>'.$ls_impbrn.'</impbrn>';
					$ls_contenido.='<impccy>'.$ls_impccy.'</impccy>';
					$ls_contenido.='<impgln>'.$ls_impgln.'</impgln>';
					$ls_contenido.='<impnch>'.$ls_impnch.'</impnch>';
					$ls_contenido.='<impmch>'.$ls_impmch.'</impmch>';
					$ls_contenido.='<impusr>'.$ls_impusr.'</impusr>';
					$ls_contenido.='<impdem>'.$ls_diamov.'</impdem>';
					$ls_contenido.='<impmem>'.$ls_mesmov.'</impmem>';
					$ls_contenido.='<impaem>'.$ls_anomov.'</impaem>';
					$ls_contenido.='<impsch>'.$ls_impsch.'</impsch>';
					$ls_contenido.='<impbnf>'.$ls_impbnf.'</impbnf>';
					$ls_contenido.='<impbn1>'.$ls_impbnf1.'</impbn1>';
					$ls_contenido.='<impbn2>'.$ls_impbnf2.'</impbn2>';
					$ls_contenido.='<impdtp>'.$ls_impdtp.'</impdtp>';
					$ls_contenido.='<impdt1>'.$ls_impdtp1.'</impdt1>';
					$ls_contenido.='<impdt2>'.$ls_impdtp2.'</impdt2>';
					$ls_contenido.='<impfty>'.$ls_impfty.'</impfty>';
					$ls_contenido.='<imppdd>'.$ls_diamov.'</imppdd>';
					$ls_contenido.='<imppdm>'.$ls_mesmov.'</imppdm>';
					$ls_contenido.='<imppdy>'.$ls_anomov.'</imppdy>';
					$ls_contenido.='<imprif>'.$ls_imprif.'</imprif>';
					$ls_contenido.='<impori>'.$ls_impori.'</impori>';
					$ls_contenido.='<codban>'.$ls_scodban.'</codban>';
					$ls_contenido.='<ctaban>'.$ls_sctaban.'</ctaban>';
					$ls_contenido.='<numdoc>'.$ls_snumdoc.'</numdoc>';
					$ls_contenido.='</comprobante>';
				}
				$rs_datac->MoveNext();
			}
		}
		else
		{
			$this->io_mensajes->message("No hay data para generar el XML!");
			/*if (file_exists ($ls_archivo))
			{
				unlink($ls_archivo);
			}*/	  
			return false;
		}
		$ls_contenido.='</COMPROBANTES>';
		@fwrite($lo_archivo,$ls_contenido);
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Genero el xml de Cheques de Gerencia IMPOFC en las fechas del ".
							 $ad_fecdes." al ".$ad_fechas." en el Archivo ".$ls_archivo.

							 " Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////					
		}
		
		return $lb_valido;
	}// end function uf_declaracioninformativa
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_gencheqgerxml_cabecera($ad_fechadesde,$ad_fechahasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_declaracion_xml_cabecera
		//         Access: public 
		//      Argumento: as_fecemidocdes // Parametro de busqueda Fecha Desde
		//				   as_fecemidochas // Parametro de busqueda Fecha Hasta
		//	      Returns: Retorna un Datastored
		//    Description: Funcion que obtiene los datos para la declaracion de salarios y otras remuneraciones
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 05/06/2009									Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_criterio="";
		$rs_data="";
		if($ad_fechadesde!="")
		{
			$ls_criterio=$ls_criterio." AND scb_movbco.fecmov>='".$ad_fechadesde."'";
		}
		if($ad_fechahasta!="")
		{
			$ls_criterio=$ls_criterio." AND scb_movbco.fecmov<='".$ad_fechahasta."'";
		}
		$ls_sql=" SELECT scb_movbco.codemp,scb_movbco.codban,scb_movbco.ctaban,scb_movbco.numdoc,scb_movbco.numordpagmin, ".
				" scb_movbco.codope,scb_movbco.estmov,scb_movbco.cod_pro,scb_movbco.ced_bene, ".
				" scb_movbco.tipo_destino,scb_movbco.codconmov,scb_movbco.fecmov,scb_movbco.conmov, ".
				" scb_movbco.nomproben,scb_movbco.monto,scb_movbco.estcon,scb_movbco.estcobing, ".
				" scb_movbco.chevau,scb_movbco.estimpche,scb_movbco.monobjret,scb_movbco.monret, ".
				" scb_movbco.procede,scb_movbco.estmovint,scb_movbco.numcarord,scb_banco.nomban, ".
				" scb_ctabanco.dencta,scb_ctabanco.sc_cuenta,scb_cheques.numchequera,scb_movbco.codtipfon,scb_movbco.estmovcob,scb_movbco.numconint, ".
				"	(SELECT MAX(rifpro) ".
				"	FROM rpc_proveedor ".
				"	WHERE codemp = scb_movbco.codemp ".
				"	AND cod_pro = scb_movbco.cod_pro) AS rifpro, ".
				"	(SELECT MAX(codestpro) ".
				"	FROM scb_movbco_spg ".
				"	WHERE codemp = scb_movbco.codemp ".
				"	AND codban = scb_movbco.codban ".
				"	AND ctaban = scb_movbco.ctaban ".
				"	AND numdoc = scb_movbco.numdoc ".
				"	AND codope = scb_movbco.codope ".
				"	AND estmov = scb_movbco.estmov) AS codestpro, ".
				"	(SELECT MAX(scg_cuenta) ".
				"	FROM scb_movbco_scg ".
				"	WHERE codemp = scb_movbco.codemp ".
				"	AND codban = scb_movbco.codban ".
				"	AND ctaban = scb_movbco.ctaban ".
				"	AND numdoc = scb_movbco.numdoc ".
				"	AND codope = scb_movbco.codope ".
				"	AND estmov = scb_movbco.estmov) AS ctacontable, ".
				"	(SELECT MAX(codfuefin) ".
				"	FROM scb_movbco_fuefinanciamiento ".
				"	WHERE codemp = scb_movbco.codemp ".
				"	AND codban = scb_movbco.codban ".
				"	AND ctaban = scb_movbco.ctaban ".
				"	AND numdoc = scb_movbco.numdoc ".
				"	AND codope = scb_movbco.codope ".
				"	AND estmov = scb_movbco.estmov) AS fuentefinanciamiento, ".
				"	(SELECT scb_tipofondo.dentipfon ".
				"   FROM scb_tipofondo ".
				"	WHERE scb_tipofondo.codemp=scb_movbco.codemp ".
				"	AND scb_tipofondo.codtipfon=scb_movbco.codtipfon) as dentipfon ".
				" FROM scb_movbco LEFT JOIN scb_cheques ".
				" ON scb_movbco.codemp=scb_cheques.codemp ".
				" AND scb_movbco.codban=scb_cheques.codban ".
				" AND scb_movbco.ctaban=scb_cheques.ctaban ".
				" AND scb_movbco.numdoc=scb_cheques.numche, scb_banco, scb_ctabanco ".
				" WHERE scb_movbco.codemp='".$this->ls_codemp."' ".
				" AND scb_movbco.codope<>'OP' ".
				" AND scb_movbco.estmov ='N' ".
				" AND scb_movbco.codope = 'CH' ".
				" AND scb_movbco.estxmlibs = '0' ".
				$ls_criterio.
				" AND scb_movbco.codemp=scb_banco.codemp ".
				" AND scb_movbco.codban=scb_banco.codban ".
				" AND scb_movbco.codemp=scb_ctabanco.codemp ".
				" AND scb_movbco.codban=scb_ctabanco.codban ".
				" AND scb_movbco.ctaban=scb_ctabanco.ctaban";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{		 
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_gencheqgerxml_cabecera ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;		
	}// end function uf_arc_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_aprobaciondebcre($as_path,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_declaracionxml
		//         Access: public  
		//	    Arguments: as_quincena // Quincena del cual se van a generar los txt
		//	    		   as_mes      // Mes del cual se van a generar los txt
		//	    		   as_anio     // A�o del cual se van a generar los txt
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: Funci�n que genera los txt de la declaraci�n informativa
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creaci�n: 15/07/2007									Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
        require_once("../shared/class_folder/class_funciones_xml.php");
		$this->io_xml=new class_funciones_xml();
		$la_archivos=$this->io_xml->uf_load_archivos($as_path);
		$li_totalarchivos=count($la_archivos["filnam"]);
		if($la_archivos=="")
		{
			$li_totalarchivos=0;
		}
		if ($li_totalarchivos==0)
		{
			$this->io_mensajes->message("No hay XML a leer o estan vacios.");
			$lb_valido=false;
		}
		else
		{
			for($li_i=1;$li_i<=$li_totalarchivos;$li_i++)
			{
				$ls_archivo=$la_archivos["filnam"][$li_i];
				$la_data=$this->io_xml->uf_cargar_ncred_ndeb_aprobados($as_path.$ls_archivo);
				$li_total=count($la_data);
				for($i=1;$i<=$li_total;$i++)
				{
					$ls_codban=rtrim($la_data[$i]["codban"]);
					$ls_ctaban=rtrim($la_data[$i]["ctaban"]);
					$ls_numdoc=rtrim($la_data[$i]["numdoc"]);
					$ls_codope=rtrim($la_data[$i]["codope"]);
					$lb_valido=$this->uf_select_movbco($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope);
					if($lb_valido)
					{
						$lb_valido=$this->uf_cambio_estatus_estapribs($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope);
					}
					
				}
		    }
				/*$dir = "banco/CREDITO_DEBITO/aprobados/";
				$handle = opendir($dir);
				while ($file = readdir($handle))
				{
				   if (is_file($dir.$file))
				   {
					   unlink($dir.$file);
				   }
				}*/
		}
		return $lb_valido;
	}// end function uf_declaracioninformativa
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_aprobacioncheger($as_path,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_declaracionxml
		//         Access: public  
		//	    Arguments: as_quincena // Quincena del cual se van a generar los txt
		//	    		   as_mes      // Mes del cual se van a generar los txt
		//	    		   as_anio     // A�o del cual se van a generar los txt
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: Funci�n que genera los txt de la declaraci�n informativa
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creaci�n: 15/07/2007									Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
        require_once("../shared/class_folder/class_funciones_xml.php");
		$this->io_xml=new class_funciones_xml();
		$la_archivos=$this->io_xml->uf_load_archivos($as_path);
		$li_totalarchivos=count($la_archivos["filnam"]);
		if($la_archivos=="")
		{
			$li_totalarchivos=0;
		}
		if ($li_totalarchivos==0)
		{
			$this->io_mensajes->message("No hay XML a leer o estan vacios.");
			$lb_valido=false;
		}
		else
		{
			for($li_i=1;$li_i<=$li_totalarchivos;$li_i++)
			{
				$ls_archivo=$la_archivos["filnam"][$li_i];
				$la_data=$this->io_xml->uf_cargar_ncred_ndeb_aprobados($as_path.$ls_archivo);
				$li_total=count($la_data);
				for($i=1;$i<=$li_total;$i++)
				{
					$ls_codban=rtrim($la_data[$i]["codban"]);
					$ls_ctaban=rtrim($la_data[$i]["ctaban"]);
					$ls_numdoc=rtrim($la_data[$i]["numdoc"]);
					$ls_codope=rtrim($la_data[$i]["codope"]);
					$lb_valido=$this->uf_select_movbco($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope);
					if($lb_valido)
					{
						$lb_valido=$this->uf_cambio_estatus_estapribs($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope);
					}
					
				}
		    }
				/*$dir = "banco/CHEQUE_GERENCIA/aprobados/";
				$handle = opendir($dir);
				while ($file = readdir($handle))
				{
				   if (is_file($dir.$file))
				   {
					   unlink($dir.$file);
				   }
				}*/
		}
		return $lb_valido;
	}// end function uf_declaracioninformativa
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_movbco($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope)
	{
			////////////////////////////////////////////////////////////////////////////////////////////////
			//
			// -Funcion que verifica que el movimiento bancario no exista
			//
			///////////////////////////////////////////////////////////////////////////////////////////////
			
			$dat=$_SESSION["la_empresa"];
			$ls_codemp=$dat["codemp"];
			
			$ls_sql="SELECT * 
					 FROM   scb_movbco
					 WHERE  codemp='".$ls_codemp."' AND codban ='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
					 AND    numdoc='".$ls_numdoc."' AND codope ='".$ls_codope."' ";
			$rs_mov=$this->io_sql->select($ls_sql);
			if(($rs_mov===false))
			{
				$this->io_mensajes->message("Error al seleccionar el movimiento!");
				return false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_mov))
				{
					return true;
				}
				else
				{
					return false;
				}	
			}
	}
	//----------------------------------------------------------------------------------------------------------------------------------
	function uf_cambio_estatus_estapribs($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que inserta la cabecera del movimiento  bancario
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		
		$ls_sql=" UPDATE scb_movbco SET estapribs='1' ".
				"  WHERE codemp='".$ls_codemp."' ".
				"  AND codban='".$ls_codban."' ".
				"  AND ctaban='".$ls_ctaban."' ".
				"  AND numdoc='".$ls_numdoc."' ".
				"  AND codope='".$ls_codope."'";
		$li_result=$this->io_sql->execute($ls_sql);
		if($li_result===false)
		{
			$this->is_msg_error=" Fallo Actualizacion de movimiento, ".$this->fun->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		else
		{
			return true;
		}
		
	}
	//----------------------------------------------------------------------------------------------------------------------------------
	function uf_cambio_estatus_estaxmlbs($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que inserta la cabecera del movimiento  bancario
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		
		$ls_sql=" UPDATE scb_movbco SET estxmlibs='1' ".
				"  WHERE codemp='".$ls_codemp."' ".
				"  AND codban='".$ls_codban."' ".
				"  AND ctaban='".$ls_ctaban."' ".
				"  AND numdoc='".$ls_numdoc."' ".
				"  AND codope='".$ls_codope."'";
		$li_result=$this->io_sql->execute($ls_sql);
		if($li_result===false)
		{
			$this->is_msg_error=" Fallo Actualizacion de movimiento, ".$this->fun->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		else
		{
			return true;
		}
		
	}
	//----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscasucursal($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_declaracion_xml_cabecera
		//         Access: public 
		//      Argumento: as_fecemidocdes // Parametro de busqueda Fecha Desde
		//				   as_fecemidochas // Parametro de busqueda Fecha Hasta
		//	      Returns: Retorna un Datastored
		//    Description: Funcion que obtiene los datos para la declaracion de salarios y otras remuneraciones
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 05/06/2009									Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$rs_data="";
		$ls_sql=" SELECT codsuc FROM sigesp_sucursales ".
				"  WHERE codestpro1='".$ls_codestpro1."' ".
				"  AND codestpro2='".$ls_codestpro2."' ".
				"  AND codestpro3='".$ls_codestpro3."' ".
				"  AND codestpro4='".$ls_codestpro4."' ".
				"  AND codestpro5='".$ls_codestpro5."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{		 
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_buscasucursal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codsuc=$row["codsuc"];
			}
			else
			{
				$ls_codsuc="";
			}
		}
		return $ls_codsuc;		
	}// end function uf_arc_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------
	
}
?>