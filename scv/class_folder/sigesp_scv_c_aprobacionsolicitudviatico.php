<?php
class sigesp_scv_c_aprobacionsolicitudviatico
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;

	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_scv_c_aprobacionsolicitudviatico($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_scv_c_aprobacionsolicitudviatico
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 13/04/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($as_path."shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once($as_path."shared/class_folder/class_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once($as_path."shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once($as_path."shared/class_folder/class_funciones.php");
		$this->io_funciones=new class_funciones();		
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
	    require_once($as_path."shared/class_folder/class_fecha.php");		
		$this->io_fecha= new class_fecha();		
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		require_once($as_path."shared/class_folder/class_sigesp_int.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_int.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_spg.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_scg.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_spi.php");
        $this->io_sigesp_int=new class_sigesp_int_int();
		$this->io_sigesp_int_spg=new class_sigesp_int_spg();
		$this->io_sigesp_int_scg=new class_sigesp_int_scg();		
		require_once($as_path."shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
	}// end function sigesp_scv_c_anulacionsolicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_sep_p_solicitud.php)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fecha);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_solicitudes($as_codsolvia,$ad_fecregdes,$ad_fecreghas,$as_tipooperacion)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_recepciones
		//		   Access: public
		//		 Argument: as_codsolvia     // Numero de Solicitud de Viaticos
		//                 ad_fecregdes     // Fecha (Emision) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las solicitudes  a aanular o reversar anulacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: Ing. María Beatriz Unda
		// Fecha Creación: 13/04/2008								Fecha Última Modificación : 05/02/2009
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		
		$ls_sql="SELECT scv_solicitudviatico.codsolvia, scv_solicitudviatico.fecsolvia,scv_rutas.desrut,scv_misiones.denmis".
				"  FROM scv_solicitudviatico,scv_rutas,scv_misiones".
				" WHERE scv_solicitudviatico.codemp='".$this->ls_codemp."'".
				"   AND estsolvia='".$as_tipooperacion."'".
				"   AND fecsolvia>='".$ad_fecregdes."'".
				"   AND fecsolvia<='".$ad_fecreghas."'".
				"   AND codsolvia like '".$as_codsolvia."'".
				"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
				"   AND scv_solicitudviatico.codmis=scv_misiones.codmis".
				"   AND scv_solicitudviatico.codemp=scv_rutas.codemp".
				"   AND scv_solicitudviatico.codrut=scv_rutas.codrut".
				"  GROUP BY scv_solicitudviatico.codsolvia,scv_solicitudviatico.fecsolvia,scv_rutas.desrut,scv_misiones.denmis ".
				"  ORDER BY scv_solicitudviatico.codsolvia";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_load_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_procesar_recepcion_documento_viatico($as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_procesar_recepcion_documento_viatico
		//         Access: public  
		//      Argumento: $ls_codsolvia // codigo de solicitud de viaticos 
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos y generar la recepcion de documentos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_datos_solicitud($as_codsolvia);
		$ls_descripcion="Calculo de Viaticos de la solicitud ".$as_codsolvia;
		$ls_codcom="SCV-".$this->io_funciones->uf_cerosizquierda($as_codsolvia,11);
		
		while((!$rs_data->EOF)&& $lb_valido)
		{
			$ls_fecregvia=$rs_data->fields["fecsolvia"];
			$li_monsolvia=$rs_data->fields["monsolvia"];
			$ls_codfuefin=$rs_data->fields["codfuefin"];
			$ls_codtipdoc=$rs_data->fields["codtipdoc"];
			$ls_obssolvia=$rs_data->fields["obssolvia"];
			$ls_descripcion=$ls_descripcion.". ".$ls_obssolvia;
			$rs_datapersonal=$this->uf_load_personalviaticos($as_codsolvia);
			while((!$rs_datapersonal->EOF)&& $lb_valido)
			{
				
				$li_monpervia=$rs_datapersonal->fields["monpervia"];
				$ls_codper=$rs_datapersonal->fields["codper"];
				$ls_cedula=$rs_datapersonal->fields["cedper"];
				if($ls_cedula=="")
				{
					$ls_cedula=$ls_codper;
				}
				$ls_codrecdoc=$this->io_keygen->uf_generar_numero_nuevo("CXP","cxp_rd","codrecdoc","CXPRCD",15,"","","");
				$lb_valido=$this->uf_scv_validar_recepcion_documentos($ls_codcom,$ls_cedula,$ls_codtipdoc);
				if($lb_valido)
				{
					$lb_valido=$this->uf_scv_procesar_recepcion_documento($as_codsolvia,$ls_codcom,$ls_cedula,$ls_codtipdoc,
																			  $ls_descripcion,$ls_fecregvia,$li_monpervia,
																			  $ls_codfuefin,$ls_codrecdoc,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("La Recepcion de Documentos ".$ls_codcom." ya esta Registrada.");
				}
				$rs_datapersonal->MoveNext();
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_recepcion_documento_gasto($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
				if($lb_valido)
				{
					$lb_valido=$this->uf_insert_recepcion_documento_contable($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
				}
			}
			$rs_data->MoveNext();
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_scv_update_solivitud_viaticos($as_codsolvia,"P",$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_datos_solicitud($as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_datos_solicitud
		//         Access: public  
		//      Argumento: $ls_codsolvia // codigo de solicitud de viaticos 
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codsolvia, fecsolvia, monsolvia, codfuefin, codtipdoc, obssolvia".
				"  FROM scv_solicitudviatico".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codsolvia='".$as_codsolvia."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobar MÉTODO->uf_select_datos_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;
		
				
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_personalviaticos($as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_datos_solicitud
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $ls_codsolvia // codigo de solicitud de viaticos 
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codper, monpervia,".
				"       (SELECT cedper FROM sno_personal".
				"         WHERE scv_dt_personal.codemp=sno_personal.codemp".
				"           AND scv_dt_personal.codper=sno_personal.codper) AS cedper".
				"  FROM scv_dt_personal".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codsolvia='".$as_codsolvia."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobar MÉTODO->uf_load_personalviaticos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;
		
				
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_validar_recepcion_documentos($as_codcom,$as_cedula,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_validar_recepcion_documentos
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $ls_codsolvia // codigo de solicitud de viaticos 
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT numrecdoc".
				"  FROM cxp_rd".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND numrecdoc='".$as_codcom."'".
				"   AND codtipdoc='".$as_codtipdoc."'".
				"   AND ced_bene='".$as_cedula."'".
				"   AND cod_pro='----------'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobar MÉTODO->uf_scv_validar_recepcion_documentos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=false;			
			}
		}
		return $lb_valido;
				
	}
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_procesar_recepcion_documento($as_codsolvia,$as_comprobante,$as_cedbene,$as_codtipdoc,
														 $as_descripcion,$ad_fecha,$ai_monto,$as_codfuefin,$as_codrecdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_procesar_recepcion_documento
		//		   Access: private
		//	    Arguments: $as_codsolvia    // codigo de solicitud de viaticos
		//                 $as_comprobante  // Codigo de Comprobante
		//				   $as_cedbene 		// cedula de beneficiario
		//				   $as_codtipdoc	// codigo de tipo de documento
		//				   $as_descripcion	// descripcion del documento
		//				   $ad_fecha  		// Fecha de contabilización
		//				   $ad_fecha  		// Fecha de contabilización
		//                 $as_codfuefin    // Código de la fuente de financiamiento
		//				   $aa_seguridad    // Arreglo de las variables de seguridad
		//	      Returns: $lb_valido True si se genero la recepción de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
        $ls_tipodestino= "B";			
		$ls_codpro= "----------";	
		$ad_fecha= $this->io_funciones->uf_convertirdatetobd($ad_fecha);
		$ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,".
 		        "                    montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,".
				"                    fecaprord,usuaprord,estimpmun,codcla,codfuefin,codrecdoc)".
				"     VALUES ('".$this->ls_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$as_cedbene."',".
				"             '".$ls_codpro."','".$as_descripcion."','".$ad_fecha."','".$ad_fecha."','".$ad_fecha."',
				"               .$ai_monto.",0,0,'".$ls_tipodestino."','".$as_comprobante."','R','SCVSOV',0,0,'1900-01-01','',0,'--','".$as_codfuefin."','".$as_codrecdoc."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{  
			$this->io_mensajes->message("CLASE->sigesp_scv_c_calcularviaticos MÉTODO->uf_scv_procesar_recepcion_documento_viatico ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Generó la Recepción de Documento Solicitud de Viáticos <b>".$as_codsolvia."</b>, ".
							"Comprobante <b>".$as_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											  $aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											  $aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$li_mondeddoc=0;
			$li_moncardoc=0;
			
		}
		return $lb_valido;
	}  // end function uf_scv_procesar_recepcion_documento_viatico
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_documento_gasto($as_comprobante,$as_codtipdoc,$as_cedbene,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_gasto
		//		   Access: private
		//	    Arguments: $as_comprobante // Código de Comprobante
		//				   $as_codtipdoc   // Tipo de Documento
		//				   $as_cedbene     // Cédula del Beneficiario
		//				   $ai_monto       // monto del comprobante
		//	      Returns: $lb_valido True si se inserto los detalles presupuestario en la recepción de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SCVSOV";
		
		$ls_sql="SELECT codemp, codsolvia, codcom, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, ".
		        " spg_cuenta, operacion, cod_pro, ced_bene, tipo_destino, descripcion, monto, estatus,codfuefin ".
				"  FROM scv_dt_spg ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codcom='".$as_comprobante."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_mensajes->message("CLASE->sigesp_scv_c_calcularviaticos MÉTODO->uf_insert_recepcion_documento_gasto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
			{
				$ls_codestpro=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$ls_spg_cuenta= $row["spg_cuenta"];
				$ls_documento=  $row["codcom"];								 
				$ls_cedbene=    $row["ced_bene"];								 
				$ls_codpro=     $row["cod_pro"];
				$ls_codfuefin=  $row["codfuefin"];								 
				$ls_monto=  $row["monto"];								 
				$ls_documento=$this->io_sigesp_int->uf_fill_comprobante($ls_documento);
				$ls_sql="INSERT INTO cxp_rd_spg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,codestpro,".
						"						 spg_cuenta,monto,estcla,codfuefin)".
						"     VALUES ('".$this->ls_codemp."','".$as_comprobante."','".$as_codtipdoc."',".
						"             '".$ls_cedbene."','".$ls_codpro."','".$ls_procede."','".$ls_documento."','".$ls_codestpro."',".
						"             '".$ls_spg_cuenta."',".$ls_monto.",'".$ls_estcla."','".$ls_codfuefin."')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
           			$this->io_mensajes->message("CLASE->sigesp_scv_c_calcularviaticos MÉTODO->uf_insert_recepcion_documento_gasto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
				   	$lb_valido=false;
				   	break;
				}
				
			} // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_documento_contable($as_comprobante,$as_codtipdoc,$as_cedbene,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_contable
		//		   Access: private
		//	    Arguments: $as_comprobante // Código de Comprobante
		//				   $as_codtipdoc   // Tipo de Documento
		//				   $as_cedbene     // Cédula del Beneficiario
		//				   $ai_monto       // monto del comprobante
		//	      Returns: $lb_valido True si se inserto los detalles contables en la recepción de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SCVSOV";
		$ls_sql="SELECT codemp, codsolvia, codcom, sc_cuenta, debhab, cod_pro, ced_bene, tipo_destino, descripcion, monto, estatus".
				"  FROM scv_dt_scg ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codcom='".$as_comprobante."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_mensajes->message("CLASE->sigesp_scv_c_calcularviaticos MÉTODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
			{
				$ls_sccuenta= $row["sc_cuenta"];
				$ls_debhab=     $row["debhab"];				
				$ls_documento=  $row["codcom"];								 
				$ls_cedbene=    $row["ced_bene"];								 
				$ls_codpro=     $row["cod_pro"];								 
				$ls_monto=  $row["monto"];								 
				$ls_documento= $this->io_sigesp_int->uf_fill_comprobante($ls_documento);
				$ls_sql="INSERT INTO cxp_rd_scg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,".
						"						 sc_cuenta,monto)".
						"     VALUES ('".$this->ls_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$ls_cedbene."',".
						"             '".$ls_codpro."','".$ls_procede."','".$ls_documento."','".$ls_debhab."',".
						"             '".$ls_sccuenta."',".$ls_monto.")";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
		           	$this->io_mensajes->message("CLASE->sigesp_scv_c_calcularviaticos MÉTODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
				    $lb_valido=false;
				    break;
				}
				
			} // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_contable
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_update_solivitud_viaticos($as_codsolvia,$as_estsolvia,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_update_solivitud_viaticos
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $ls_codsolvia // codigo de solicitud de viaticos 
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga de poner en estado de registrada a una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql=" UPDATE scv_solicitudviatico".
				"    SET estsolvia='".$as_estsolvia."'".
				"  WHERE codemp='".$this->ls_codemp."'".
				"    AND codsolvia='".$as_codsolvia."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if ($li_row===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos METODO->uf_scv_update_solivitud_viaticos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion= "Reversó la solicitud de viaticos ".$as_codsolvia." Asociada a la empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               ///////////////////////////
			$lb_valido=true;
		}
		return $lb_valido;
	} // fin function uf_scv_update_solivitud_viaticos
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_procesar_reverso_recepcion_documento_viatico($as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_procesar_reverso_recepcion_documento_viatico
		//         Access: public  
		//      Argumento: $ls_codsolvia // codigo de solicitud de viaticos 
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos y reversar la recepcion de documentos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_datos_solicitud($as_codsolvia);
		$ls_descripcion="Calculo de Viaticos de la solicitud ".$as_codsolvia;
		$ls_numrecdoc="SCV-".$this->io_funciones->uf_cerosizquierda($as_codsolvia,11);
		while((!$rs_data->EOF)&& $lb_valido)
		{
			$ls_codtipdoc=$rs_data->fields["codtipdoc"];
			$rs_datapersonal=$this->uf_load_personalviaticos($as_codsolvia);
			while((!$rs_datapersonal->EOF)&& $lb_valido)
			{
				
				$ls_codper=$rs_datapersonal->fields["codper"];
				$ls_cedula=$rs_datapersonal->fields["cedper"];
				if($ls_cedula=="")
				{
					$ls_cedula=$ls_codper;
				}

				$lb_valido=$this->uf_scv_select_estatus_recepcion($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,&$lb_registro);
				if($lb_valido)
				{
					$lb_anulada=$this->uf_load_solicitudesanuladas($ls_numrecdoc,$ls_cedula,$ls_codtipdoc);
					if($lb_anulada)
					{
						$this->io_mensajes->message("La Recepcion de Documentos ".$ls_numrecdoc." esta asociada a una solicitud de pago Anulada.");
						$lb_valido=false;
						break;
					}
					else
					{
						if($lb_registro)
						{
								$lb_valido=$this->uf_scv_delete_dt_rd_scg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																		  $aa_seguridad);
								if($lb_valido)
								{
									$lb_valido=$this->uf_scv_delete_dt_rd_spg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																		  $aa_seguridad);
									if($lb_valido)
									{
										$lb_valido=$this->uf_scv_delete_rd($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																		  $aa_seguridad);
									}
								}
						}
						else
						{
							$this->io_mensajes->message("Las Recepciones de Documentos asociadas deben estar en estatus de Registro - No Aprobada");
							break;
						}
					}
				}
				else
				{
					$this->io_mensajes->message("No existe Recepcion de Documentos asociada");
					$lb_valido=false;
					break;
				}
				$rs_datapersonal->MoveNext();
			}
			$rs_data->MoveNext();
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_scv_update_solivitud_viaticos($as_codsolvia,"C",$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_select_estatus_recepcion($as_recepcion,$as_cedula,&$as_codtipdoc,&$ab_registro)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_estatus_recepcion
		//         Access: public  
		//      Argumento: $as_numrecdoc // Numero de recepcion de documentos
		//  			   $as_cedula    //  Cedula de  baneficiario
		//  			   $as_codtipdoc // codigo de tipo de documento
		//  			   $ab_registro  // indica si alguna de las recepciones de documentos ha sido pasada a otro estatus
		//  			   $as_numrecdoc // numeto de la recepcion de documento
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica el estatus que se encuentra la recepcion de documentos generada desde viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sqlaux="";
		if(trim($as_codtipdoc)!="")
		{
			$ls_sqlaux="   AND codtipdoc='".$as_codtipdoc."'";
		}
		$ls_sql = "SELECT estprodoc,estaprord,codtipdoc".
		          "  FROM cxp_rd  ".
				  " WHERE codemp='".$this->ls_codemp."'".
				  "   AND numrecdoc='".$as_recepcion."'".
				  "   AND ced_bene='".$as_cedula."'".
				  $ls_sqlaux.
				  "   AND procede='SCVSOV'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos MÉTODO->uf_scv_select_estatus_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ab_registro=true;
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_estaprord=$row["estaprord"];
				$ls_estprodoc=$row["estprodoc"];
				$as_codtipdoc=$row["codtipdoc"];
				if(($ls_estprodoc!="R")||($ls_estaprord!=0))
				{
					$ab_registro=false;
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end  function uf_scv_select_estatus_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_delete_dt_rd_scg($as_numrecdoc,$as_cedula,$as_codtipdoc,$as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_delete_dt_rd_scg
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $as_numconrec // numero concecutivo de recepción
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina un detalle contable de una recepcion de documentos generada por una solicitud de 
		//                 viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sqlaux="";
		if(trim($as_codtipdoc)!="")
		{
			$ls_sqlaux="   AND codtipdoc='".$as_codtipdoc."'";
		}
		$ls_sql="DELETE FROM cxp_rd_scg".
				" WHERE codemp='". $this->ls_codemp ."'".
				"   AND numrecdoc='". $as_numrecdoc ."'".
				  $ls_sqlaux.
				"   AND ced_bene='". $as_cedula ."'".
				"   AND cod_pro='----------'".
				"   AND procede_doc='SCVSOV'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos MÉTODO->uf_scv_delete_dt_rd_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Reversó el detalle contable de la recepcion de documento ".$as_numrecdoc." mediante el reverso de".
			                 " la solicitud de viaticos".$as_codsolvia." asociada a la Empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion); 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	} // end  function uf_scv_delete_dt_rd_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_delete_dt_rd_spg($as_numrecdoc,$as_cedula,$as_codtipdoc,$as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_delete_dt_rd_spg
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $as_numconrec // numero concecutivo de recepción
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina un detalle contable de una recepcion de documentos generada por una solicitud de 
		//                 viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sqlaux="";
		if(trim($as_codtipdoc)!="")
		{
			$ls_sqlaux="   AND codtipdoc='".$as_codtipdoc."'";
		}
		$ls_sql="DELETE FROM cxp_rd_spg".
				" WHERE codemp='". $this->ls_codemp ."'".
				"   AND numrecdoc='". $as_numrecdoc ."'".
				  $ls_sqlaux.
				"   AND ced_bene='". $as_cedula ."'".
				"   AND cod_pro='----------'".
				"   AND procede_doc='SCVSOV'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos MÉTODO->uf_scv_delete_dt_rd_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Reversó el detalle presupuestario de la recepcion de documento ".$as_numrecdoc." mediante el reverso".
			                 " de la solicitud de viaticos".$as_codsolvia." asociada a la Empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion); 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	} // end  function uf_scv_delete_dt_rd_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_delete_rd($as_numrecdoc,$as_cedula,$as_codtipdoc,$as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_delete_rd
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numrecdoc // numero de recepcion de documentos
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina las recepciones de documentos originadas de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sqlaux="";
		if(trim($as_codtipdoc)!="")
		{
			$ls_sqlaux="   AND codtipdoc='".$as_codtipdoc."'";
		}
		$ls_sql="DELETE FROM cxp_rd".
				" WHERE codemp='". $this->ls_codemp ."'".
				"   AND numrecdoc='". $as_numrecdoc ."'".
				  $ls_sqlaux.
				"   AND ced_bene='". $as_cedula ."'".
				"   AND cod_pro='----------'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos MÉTODO->uf_scv_delete_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Reversó  la recepcion de documento ".$as_numrecdoc." mediante el reverso".
			                 " de la solicitud de viaticos".$as_codsolvia." asociada a la Empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion); 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$lb_valido=true;
		}
		return $lb_valido;
	}  // end  function uf_scv_delete_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_solicitudesanuladas($as_numrecdoc,$as_cedula,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_solicitudesanuladas
		//		   Access: public
		//		 Argument: as_numrecdoc     // Numero de Recepcion de Documentos
		//                 as_cedula     // Fecha (Emision) de inicio de la Busqueda
		//                 as_codtipdoc     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipproben     // tipo proveedor/ beneficiario
		//                 as_proben        // Codigo de proveedor/ beneficiario
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las recepciones  a aprobar o reversar aprobacion
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 05/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ab_anulada=false;
		$ls_sql="SELECT cxp_rd.numrecdoc ".
				"  FROM cxp_rd".
				" WHERE cxp_rd.codemp = '".$this->ls_codemp."'".
				"   AND cxp_rd.numrecdoc = '".$as_numrecdoc."' ".
				"   AND cxp_rd.codtipdoc = '".$as_codtipdoc."' ".
				"   AND cxp_rd.ced_bene = '".$as_cedula."' ".
				"   AND cxp_rd.cod_pro = '----------' ".
				"   AND (cxp_rd.estprodoc='R' OR cxp_rd.estprodoc='E')".
				"   AND cxp_rd.numrecdoc IN (SELECT cxp_dt_solicitudes.numrecdoc".
				"						       FROM cxp_solicitudes,cxp_dt_solicitudes".
				"						      WHERE cxp_dt_solicitudes.numrecdoc like '".$as_numrecdoc."'".
				"								AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"								AND (cxp_solicitudes.estprosol='A' OR cxp_solicitudes.estprosol='N'))";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_load_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ab_anulada=true;
			}
		}
		return $ab_anulada;
	}// end function uf_load_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

}
?>