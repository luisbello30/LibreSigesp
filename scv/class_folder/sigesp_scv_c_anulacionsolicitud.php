<?php
class sigesp_scv_c_anulacionsolicitud
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;

	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_scv_c_anulacionsolicitud($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_scv_c_anulacionsolicitud
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 13/04/2008 								Fecha �ltima Modificaci�n : 
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
		// Fecha Creaci�n: 02/05/2007								Fecha �ltima Modificaci�n : 
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
		//	  Description: Funci�n que busca las solicitudes  a aanular o reversar anulacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: Ing. Mar�a Beatriz Unda
		// Fecha Creaci�n: 13/04/2008								Fecha �ltima Modificaci�n : 05/02/2009
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		
		$ls_sql="SELECT scv_solicitudviatico.codsolvia, scv_solicitudviatico.fecsolvia,scv_rutas.desrut,scv_misiones.denmis".
				"  FROM scv_solicitudviatico,scv_rutas,scv_misiones".
				" WHERE scv_solicitudviatico.codemp='".$this->ls_codemp."'".
				"   AND (estsolvia='R' OR estsolvia='P')".
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
			$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_load_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_estatus_solicitud($as_codsolvia)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_recepciones
		//		   Access: public
		//		 Argument: as_codsolvia // Numero de Solicitud de Viaticos
		//	  Description: Funci�n que busca las solicitudes  a aanular o reversar anulacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 13/04/2008								Fecha �ltima Modificaci�n : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_existe=false;
		$ls_sql="SELECT codsolvia ".
				"  FROM scv_solicitudviatico".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codsolvia='".$as_codsolvia."'".
				"   AND estsolvia='A'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_validar_estatus_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_existe=true;
			}
		}
		return $ls_existe;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_solicitud($as_codsolvia,$aa_seguridad) 
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_update_estatus_solicitud
		//	          Access:  public
		//	       Arguments:  $as_codsolvia  // C�digo de la Empresa.
		//              	   $aa_seguridad  // Arreglo de Seguridad
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de actualizar el estatus de la solicitud de viaticos
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci�n:  13/04/2008        
		////////////////////////////////////////////////////////////////////////////// 
		$lb_valido=false;
		$this->io_sql->begin_transaction();
		$lb_valido=$this->uf_anular_recepciones($as_codsolvia,$aa_seguridad);
		if($lb_valido)
		{
			$ls_sql="UPDATE scv_solicitudviatico".
					"   SET estsolvia='A'".
					" WHERE codemp='".$this->ls_codemp."'".
					"   AND codsolvia='".$as_codsolvia."'";
			$rs_data = $this->io_sql->execute($ls_sql);
			if ($rs_data===false)
			{
				$this->io_sql->rollback();
				$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_update_estatus_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="UPDATE";
				$ls_descripcion ="Anulo la Solicitud de Viaticos  ".$as_codsolvia." Asociado a la Empresa ".$this->ls_codemp;
				$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
							$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
							$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		     
			}
		}
		if($lb_valido)
		{
				$this->io_sql->commit();
		}
		else
		{
				$this->io_sql->rollback();
		}
		return $lb_valido;
	} // fin de la function uf_update_estatus_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_anular_recepciones($as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_anular_recepciones
		//         Access: public  
		//      Argumento: $ls_codsolvia // codigo de solicitud de viaticos 
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Funci�n que se encarga obtener los datos de la solicitud de viaticos y reversar la recepcion de documentos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 14/08/2009							Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_numrecdoc="SCV-".$this->io_funciones->uf_cerosizquierda($as_codsolvia,11);
		$rs_datapersonal=$this->uf_load_personalviaticos($as_codsolvia);
		while((!$rs_datapersonal->EOF)&& $lb_valido)
		{
			$ls_codper=$rs_datapersonal->fields["codper"];
			$ls_cedula=$rs_datapersonal->fields["cedper"];
			if($ls_cedula=="")
			{
				$ls_cedula=$ls_codper;
			}
			$lb_existe=$this->uf_scv_select_estatus_recepcion($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,&$lb_registro);
			if($lb_existe)
			{
				$lb_anulada=$this->uf_load_solicitudesanuladas($ls_numrecdoc,$ls_cedula,$ls_codtipdoc);
				if($lb_anulada)
				{
					$lb_valido=$this->uf_anular_recepcion($ls_numrecdoc,1,"----------",$ls_cedula,$ls_codtipdoc,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("La Recepcion de Documentos ".$ls_numrecdoc." debe estar asociada a una solicitud de pago Anulada.");
					$lb_valido=false;
					break;
				}
			}
			$rs_datapersonal->MoveNext();
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
		// Fecha Creaci�n: 24/11/2006							Fecha �ltima Modificaci�n :
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
			$this->io_mensajes->message("CLASE->revcalcularviaticos M�TODO->uf_scv_select_estatus_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ab_registro=true;
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
	function uf_load_personalviaticos($as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_datos_solicitud
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $ls_codsolvia // codigo de solicitud de viaticos 
		//	      Returns: Retorna un Booleano
		//	  Description: Funci�n que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 14/08/2009							Fecha �ltima Modificaci�n :
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
			$this->io_mensajes->message("CLASE->Aprobar M�TODO->uf_load_personalviaticos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;
		
				
	}
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
		//	  Description: Funci�n que busca las recepciones  a aprobar o reversar aprobacion
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 05/05/2007								Fecha �ltima Modificaci�n : 
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
				"						      WHERE cxp_dt_solicitudes.numrecdoc like '".$as_numrecdoc."%'".
				"								AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"								AND (cxp_solicitudes.estprosol='A' OR cxp_solicitudes.estprosol='N'))";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_load_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_anular_recepcion($as_numrecdoc,$as_estsol,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_anular_recepcion
		//		   Access: private
		//	    Arguments: as_numrecdoc  //  N�mero de Recepcion de Documentos
		//				   as_estsol     //  Estatus de la Solicitud
		//				   as_codpro     //  Codigo de Proveedor
		//				   as_cedben     //  Codigo de Beneficiario
		//				   as_codtipdoc  //  Codigo de Tipo de Documento
		//				   aa_seguridad  //  Arreglo de Seguridad
		// 	      Returns: lb_existe True si existe � False si no existe
		//	  Description: Funcion que obtiene el numero de recepcion anulado
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 12/05/2007 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_numrecdocnew="";
		$ls_numrecdocnew=$this->uf_load_numero_anulado($as_numrecdoc,$as_estsol,$as_codpro,$as_cedben,$as_codtipdoc);
		if($ls_numrecdocnew!="")
		{
			$lb_valido=$this->uf_insert_recepcion_anulada($as_numrecdoc,$ls_numrecdocnew,$as_codpro,$as_cedben,
														  $as_codtipdoc,$aa_seguridad);
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_dt_spg_anulada($as_numrecdoc,$ls_numrecdocnew,$as_codpro,$as_cedben,
														   $as_codtipdoc,$aa_seguridad);
				if($lb_valido)
				{
					$lb_valido=$this->uf_insert_dt_scg_anulada($as_numrecdoc,$ls_numrecdocnew,$as_codpro,$as_cedben,
															   $as_codtipdoc,$aa_seguridad);
					if($lb_valido)
					{
						$lb_valido=$this->uf_insert_dt_solicitud_anulada($as_numrecdoc,$ls_numrecdocnew,$as_codpro,
																		  $as_cedben,$as_codtipdoc,$aa_seguridad);
						if($lb_valido)
						{
							$lb_valido=$this->uf_delete($as_numrecdoc,$as_codtipdoc,$as_cedben,$as_codpro,$aa_seguridad);
						}
					}
				}
			}
		}
		if($lb_valido)
		{	
			$this->io_mensajes->message("La Recepci�n de Documentos fue Anulada.");
			$this->io_sql->commit();
		}
		else
		{
			$this->io_mensajes->message("Ocurrio un Error al Anular la Recepci�n de Documentos."); 
			$this->io_sql->rollback();
		}
		return $lb_valido;
	}// end function uf_load_numero_anulado
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_numero_anulado($as_numrecdoc,$as_estsol,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_numero_anulado
		//		   Access: private
		//	    Arguments: as_numrecdoc  //  N�mero de Recepcion de Documentos
		//				   as_estsol     //  Estatus de la Solicitud
		//				   as_codpro     //  Codigo de Proveedor
		//				   as_cedben     //  Codigo de Beneficiario
		//				   as_codtipdoc  //  Codigo de Tipo de Documento
		// 	      Returns: lb_existe True si existe � False si no existe
		//	  Description: Funcion que obtiene el numero de recepcion anulado
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 07/05/2007 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$li_i=0;
		$li_lonnumrecdoc = strlen(trim($as_numrecdoc));
		$ls_prenumrec    = substr(trim($as_numrecdoc),0,4);//Prefijo del N�mero de la Recepci�n de Documentos.
		while($lb_existe)
		{
			$li_i=$li_i+1;
			if ($ls_prenumrec=='SCV-' && $li_lonnumrecdoc>=14)//Para el Caso de las Solicitudes de Viaticos SCV-00000000000.
			   {
			     $ls_numrecdoc = substr(trim($as_numrecdoc),0,3).substr(trim($as_numrecdoc),5,15);
				 $ls_numrecdocnew="@".$li_i.$ls_numrecdoc;
			   }
			else
			   {
			     $ls_numrecdocnew="@".$li_i.$as_numrecdoc;
			   }

			$li_lonnewnumrecdoc = strlen(trim($ls_numrecdocnew));
			if($li_lonnewnumrecdoc>15)
			{
				$ls_numrecdocnew=substr($ls_numrecdocnew,0,15);			
			}
			$ls_sql="SELECT numrecdoc ".
					"  FROM cxp_rd ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND numrecdoc='".$ls_numrecdocnew."' ".
					"   AND cod_pro='".$as_codpro."' ".
					"   AND ced_bene='".$as_cedben."' ".
					"   AND codtipdoc='".$as_codtipdoc."' ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_load_numero_anulado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
				if(!$row=$this->io_sql->fetch_row($rs_data))
				{
					$lb_existe=false;
				}
			}
		}
		return $ls_numrecdocnew;
	}// end function uf_load_numero_anulado
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_anulada($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_anulada
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  N�mero de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  N�mero de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe � False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 07/05/2007 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$rs_data=$this->uf_select_recepcion($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion M�TODO->uf_validar_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_numref= $row["numref"];  					
				$ls_tipproben= $row["tipproben"];										
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_codcla= $row["codcla"]; 
				$ls_dencondoc= $row["dencondoc"];    
				$ls_fecemidoc= $this->io_funciones->uf_formatovalidofecha($row["fecemidoc"]);    
				$ls_fecregdoc= $this->io_funciones->uf_formatovalidofecha($row["fecregdoc"]);    
				$ls_fecvendoc= $this->io_funciones->uf_formatovalidofecha($row["fecvendoc"]);    
				$li_montotdoc= $row["montotdoc"]; 
				$li_mondeddoc= $row["mondeddoc"];    
				$li_moncardoc= $row["moncardoc"];    
				$ls_procede= $row["procede"];
				$li_estlibcom= 0;
				$ls_estaprord= 0;								   
				$ls_sql=" INSERT INTO cxp_rd (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, codcla,dencondoc, fecemidoc,  ".
					  	" 					  fecregdoc, fecvendoc, montotdoc, mondeddoc, moncardoc, tipproben, ".
					  	" 					  numref, estprodoc, procede, estlibcom, estaprord, fecaprord, usuaprord) ".
					  	" VALUES ('".$this->ls_codemp."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_cedbene."','".$ls_codpro."', ".
					  	" 		  '".$ls_codcla."','".$ls_dencondoc."','".$ls_fecemidoc."','".$ls_fecregdoc."','".$ls_fecvendoc."', ".
					  	" 		   ".$li_montotdoc.",".$li_mondeddoc.",".$li_moncardoc.",'".$ls_tipproben."','".$ls_numref."', ".
					  	" 		  'A', '".$ls_procede."',".$li_estlibcom.",".$ls_estaprord.",'1900-01-01','')";	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Aprobacion M�TODO->uf_insert_recepcion_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$this->io_sql->rollback();
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Insert� la Recepcion de Documentos Anulada <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$this->io_sql->free_result($rs_data);	
				}
			}
		}

		return $lb_valido;
	}// end function uf_insert_recepcion_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_spg($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_spg
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Funci�n que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 07/05/2007								Fecha �ltima Modificaci�n : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom, codestpro, estcla, spg_cuenta, monto".
				"  FROM cxp_rd_spg".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_select_dt_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_select_dt_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_spg_anulada($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dt_spg_anulada
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  N�mero de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  N�mero de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe � False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 07/05/2007 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_dt_spg($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_insert_dt_spg_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_procededoc= $row["procede_doc"]; 
				$ls_numdoccom= $row["numdoccom"];    
				$ls_codestpro= $row["codestpro"];  					
				$ls_estcla= $row["estcla"];  					
				$ls_spgcuenta= $row["spg_cuenta"];
				$li_monto= $row["monto"];

				$ls_sql=" INSERT INTO cxp_rd_spg (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom,".
						"					 	  codestpro, estcla, spg_cuenta, monto) ".
					  	" VALUES ('".$this->ls_codemp."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_cedbene."','".$ls_codpro."', ".
					  	" 		  '".$ls_procededoc."','".$ls_numdoccom."','".$ls_codestpro."','".$ls_estcla."','".$ls_spgcuenta."',".$li_monto.")";	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_insert_dt_spg_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Insert� la Recepcion de Documentos Anulada <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
			}
			$this->io_sql->free_result($rs_data);	
		}

		return $lb_valido;
	}// end function uf_insert_dt_spg_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_scg($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_scg
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Funci�n que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 07/05/2007								Fecha �ltima Modificaci�n : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom,".
				"		debhab, sc_cuenta, monto, estgenasi".
				"  FROM cxp_rd_scg".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_select_dt_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_select_dt_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_scg_anulada($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dt_scg_anulada
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  N�mero de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  N�mero de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe � False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 07/05/2007 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_dt_scg($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{$this->io_sql->message;
			$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_insert_dt_scg_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{

				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_procededoc= $row["procede_doc"]; 
				$ls_numdoccom= $row["numdoccom"];    
				$ls_debhab= $row["debhab"];  					
				$ls_sccuenta= $row["sc_cuenta"];    
				$li_monto= $row["monto"];
				$ls_estgenasi= 0;

				$ls_sql=" INSERT INTO cxp_rd_scg (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom,".
						"						  debhab, sc_cuenta, monto, estgenasi) ".
					  	" VALUES ('".$this->ls_codemp."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_cedbene."','".$ls_codpro."', ".
					  	" 		  '".$ls_procededoc."','".$ls_numdoccom."','".$ls_debhab."','".$ls_sccuenta."',".$li_monto.",'".$ls_estgenasi."')";	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{ print $this->io_sql->message;
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_insert_dt_scg_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Insert� la Recepcion de Documentos Anulada <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
			}
			$this->io_sql->free_result($rs_data);	
		}

		return $lb_valido;
	}// end function uf_insert_dt_scg_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_solicitud($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_solicitud
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Funci�n que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 07/05/2007								Fecha �ltima Modificaci�n : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numsol, numrecdoc, codtipdoc, ced_bene, cod_pro, monto".
				"  FROM cxp_dt_solicitudes".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_select_dt_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_select_dt_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_solicitud_anulada($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dt_solicitud_anulada
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  N�mero de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  N�mero de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe � False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 07/05/2007 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$rs_data=$this->uf_select_dt_solicitud($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_insert_dt_solicitud_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{

				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_numsol= $row["numsol"]; 
				$li_monto= $row["monto"];

				$ls_sql=" INSERT INTO cxp_dt_solicitudes (codemp, numsol, numrecdoc, codtipdoc, ced_bene, cod_pro, monto) ".
					  	" VALUES ('".$this->ls_codemp."','".$ls_numsol."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_cedbene."', ".
					  	" 		  '".$ls_codpro."',".$li_monto.")";	
			}
		}

		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Anulacion M�TODO->uf_insert_dt_solicitud_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Insert� la Recepcion de Documentos Anulada <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_insert_dt_solicitud_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_recepcion($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_recepciones
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Funci�n que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 07/05/2007								Fecha �ltima Modificaci�n : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, codcla, dencondoc, fecemidoc,".
				"       fecregdoc, fecvendoc, montotdoc, mondeddoc, moncardoc, tipproben, numref, estprodoc,".
				"       procede, estlibcom, estaprord, fecaprord, usuaprord, numpolcon, estimpmun, montot".
				"  FROM cxp_rd".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
			//	print $ls_sql."<br>"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion M�TODO->uf_validar_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_validar_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_estatus($as_numrecdoc,$as_cedbene,$as_codpro,$as_codtipdoc,&$as_estprodoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_estatus
		//		   Access: public
		//		 Argument: as_numrecdoc // N�mero de Recepci�n de Documentos
		//		 		   as_tipodestino // Tipo de Destino (Proveedor � Beneficiario)
		//		 		   as_codprovben // C�digo del Proveedor � Beneficiario
		//		 		   as_codtipdoc // C�digo del Tipo de Documento
		//	  Description: Funci�n que busca en la tabla de la recepcion el estatus de la misma
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 06/05/2007								Fecha �ltima Modificaci�n : 
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		$ls_sql="SELECT estprodoc ".
				"  FROM cxp_rd ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"	AND numrecdoc='".$as_numrecdoc."' ".
				"	AND codtipdoc='".$as_codtipdoc."' ".
				"   AND cod_pro='".$as_codpro."' ".
				"   AND ced_bene='".$as_cedbene."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Recepcion M�TODO->uf_load_estatus ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_estprodoc=$row["estprodoc"];
			}
		}
		return $lb_valido;
	}// end function uf_load_estatus
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_detallesrecepcion($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_detallesrecepcion
		//		   Access: private
		//	    Arguments: as_numrecdoc  // N�mero de recepci�n de documentos
		//				   as_codtipdoc  // Tipo de Documento
		//				   as_cedbene  // C�dula del Beneficiario
		//				   as_codpro  // C�digo de proveedor
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert � False si hubo error en el insert
		//	  Description: Funcion que elimina los detalles de una recepcion
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 17/03/2007 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE FROM cxp_rd_cargos ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"	AND numrecdoc='".$as_numrecdoc."' ".
				"	AND codtipdoc='".$as_codtipdoc."' ".
				"	AND cod_pro='".$as_codpro."' ".
				"	AND ced_bene='".$as_cedbene."'";		  
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Recepcion M�TODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_rd_deducciones ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion M�TODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_rd_scg ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion M�TODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_rd_spg ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion M�TODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_dt_solicitudes ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion M�TODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_historico_rd ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion M�TODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_rd_amortizacion ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion M�TODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_dt_amortizacion ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion M�TODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Elimin� todos los detalles de Recepci�n de Documentos ".$as_numrecdoc." Tipo ".$as_codtipdoc." Beneficiario ".$as_cedbene.
							 "Proveedor ".$as_codpro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_delete_detallesrecepcion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete
		//		   Access: public (sigesp_cxp_p_recepcion.php)
		//	    Arguments: as_numrecdoc  // N�mero de recepci�n de documentos
		//				   as_tipodestino  // Tipo Destino
		//				   as_codprovben  // C�digo de proveedor � beneficiario
		//				   as_codtipdoc  // Tipo de Documento
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar � False si hubo error en el guardar
		//	  Description: Funcion que valida y elimina la recepci�n
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 07/05/2007 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;	
		$as_codtipdoc=substr($as_codtipdoc,0,5);
		$lb_encontrado=$this->uf_select_recepcion($as_numrecdoc,$as_codpro,$as_cedbene,$as_codtipdoc);
		if($lb_encontrado)
		{
			$lb_valido=$this->uf_load_estatus($as_numrecdoc,$as_cedbene,$as_codpro,$as_codtipdoc,$ls_estprodoc);
			if($ls_estprodoc!="R")
			{
				if($ls_estprodoc!="E")
				{
					$this->io_mensajes->message("La Recepci�n de Documentos no se puede eliminar, Tiene Movimientos.");           
					$lb_valido=false;
				}
			}
			if($lb_valido)
			{	
				$lb_valido=$this->uf_delete_detallesrecepcion($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$aa_seguridad);
			}			
			if($lb_valido)
			{	
				$ls_sql="DELETE FROM cxp_rd ".
						" WHERE codemp='".$this->ls_codemp."' ".
						"	AND numrecdoc='".$as_numrecdoc."' ".
						"	AND codtipdoc='".$as_codtipdoc."' ".
						"	AND cod_pro='".$as_codpro."' ".
						"	AND ced_bene='".$as_cedbene."' ";		  
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{print $this->io_sql->message;
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Recepci�n M�TODO->uf_eliminar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="DELETE";
					$ls_descripcion ="Elimino la Recepci�n de Documentos ".$as_numrecdoc." Tipo ".$as_codtipdoc." Beneficiario ".$as_cedbene.
									 "Proveedor ".$as_codpro." Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				}	
			}
		}
		else
		{
			$this->io_mensajes->message("No se encontro la Recepcion de Documentos");
			$lb_valido=false;	
		}
		return $lb_valido;
	}// end function uf_delete
	//-----------------------------------------------------------------------------------------------------------------------------------


}
?>