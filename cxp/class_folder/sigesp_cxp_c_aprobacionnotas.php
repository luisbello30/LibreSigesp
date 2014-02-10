<?php
class sigesp_cxp_c_aprobacionnotas
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;

	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_cxp_c_aprobacionnotas($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sep_c_aprobacion
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creacin: 17/03/2007 								Fecha ltima Modificacin : 
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
	}// end function sigesp_sep_c_aprobacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_sep_p_solicitud.php)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creacin: 17/03/2007								Fecha ltima Modificacin : 
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
	function uf_load_solicitudes($as_numsol,$as_tipo,$as_coduniadm,$ad_fecregdes,$ad_fecreghas,$as_tipproben,$as_proben,$as_tipooperacion)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_solicitudes
		//		   Access: public
		//		 Argument: as_numsol        // Numero de la solicitud de ejecucion presupuestaria
		//                 as_tipo          // Indica si es de Bienes o de servicios
		//                 as_coduniadm     // Codigo de la Unidad Ejecutora
		//                 ad_fecregdes     // Fecha (Registro) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Registro) de fin de la Busqueda
		//                 as_tipproben     // tipo proveedor/ beneficiario
		//                 as_proben        // Codigo de proveedor/ beneficiario
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Funcin que busca las solicitudes de ejecucion presupuestaria
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creacin: 03/02/2007								Fecha ltima Modificacin : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="CONCAT(nombene,' ',apebene)";
				break;
			case "POSTGRE":
				$ls_cadena="nombene||' '||apebene";
				break;
		}
		$ls_sql="SELECT sep_solicitud.numsol,spg_unidadadministrativa.denuniadm,sep_solicitud.estsol,sep_solicitud.monto,".
				"       sep_solicitud.estapro,sep_solicitud.fecregsol,".
				"       (CASE WHEN sep_solicitud.tipo_destino='B' THEN (SELECT ".$ls_cadena." ".
				"                                                      FROM rpc_beneficiario".
				"                                                     WHERE sep_solicitud.codemp=rpc_beneficiario.codemp".
				"                                                       AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene)".
				"             WHEN sep_solicitud.tipo_destino='P' THEN (SELECT nompro".
				"                                                         FROM rpc_proveedor".
				"                                                        WHERE sep_solicitud.codemp=rpc_proveedor.codemp".
				"                                                          AND sep_solicitud.cod_pro=rpc_proveedor.cod_pro)".
				"                                                  ELSE 'NINGUNO'".
				"         END) AS nombre".
				"  FROM sep_solicitud,spg_unidadadministrativa,sep_tiposolicitud".
				" WHERE sep_solicitud.codemp = '".$this->ls_codemp."'".
				"   AND sep_solicitud.numsol LIKE '".$as_numsol."' ".
				"   AND sep_solicitud.coduniadm LIKE '".$as_coduniadm."' ".
				"   AND sep_solicitud.fecregsol >= '".$ad_fecregdes."' ".
				"   AND sep_solicitud.fecregsol <= '".$ad_fecreghas."' ".
				"   AND sep_solicitud.estsol='E'".
				"   AND sep_solicitud.estapro='".$as_tipooperacion."'".
				"   AND sep_tiposolicitud.modsep LIKE '".$as_tipo."'".
				"   AND sep_solicitud.codtipsol=sep_tiposolicitud.codtipsol".
				"   AND sep_solicitud.codemp=spg_unidadadministrativa.codemp".
				"   AND sep_solicitud.coduniadm=spg_unidadadministrativa.coduniadm";
		if($as_tipproben=="B")
		{
			$ls_sql= $ls_sql." AND sep_solicitud.ced_bene LIKE '".$as_proben."'";
		}
		else
		{
			$ls_sql= $ls_sql." AND sep_solicitud.cod_pro LIKE'".$as_proben."'";
		}
		$ls_sql= $ls_sql." ORDER BY sep_solicitud.numsol ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MTODO->uf_load_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_cuentas($as_numsol,$as_numrecdoc,$as_numncnd,$as_codope,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_cuentas
		//		   Access: private
		//		 Argument: as_numsol // Nmero de solicitud
		//	  Description: Funcin que busca que las cuentas presupuestarias estn en la programtica seleccionada
		//				   de ser asi puede aprobar la sep de lo contrario no la apruebas
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creacin: 17/03/2007								Fecha ltima Modificacin : 
		//////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/class_sigesp_int.php");
		require_once("../shared/class_folder/class_sigesp_int_int.php");
		require_once("../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../shared/class_folder/class_sigesp_int_spg.php");
		$io_int_spg=new class_sigesp_int_spg();

		$lb_valido=true;
		 $ls_sql="SELECT SUBSTR(cxp_dc_spg.codestpro,1,25) as codestpro1, SUBSTR(cxp_dc_spg.codestpro,26,25) as codestpro2, SUBSTR(cxp_dc_spg.codestpro,51,25) as codestpro3, ".
				"		SUBSTR(cxp_dc_spg.codestpro,76,25) as codestpro4, SUBSTR(cxp_dc_spg.codestpro,101,25) as codestpro5, cxp_dc_spg.estcla, ".
				"		cxp_dc_spg.spg_cuenta, cxp_dc_spg.monto, cxp_sol_dc.fecope, ".
				"		(SELECT COUNT(codemp) ".
				"		   FROM spg_cuentas ".
				"		  WHERE spg_cuentas.codemp = cxp_dc_spg.codemp ".
				"			AND spg_cuentas.codestpro1 = SUBSTR(cxp_dc_spg.codestpro,1,25) ".
				"		    AND spg_cuentas.codestpro2 = SUBSTR(cxp_dc_spg.codestpro,26,25) ".
				"		    AND spg_cuentas.codestpro3 = SUBSTR(cxp_dc_spg.codestpro,51,25) ".
				"		    AND spg_cuentas.codestpro4 = SUBSTR(cxp_dc_spg.codestpro,76,25) ".
				"		    AND spg_cuentas.codestpro5 = SUBSTR(cxp_dc_spg.codestpro,101,25) ".
				"		    AND spg_cuentas.estcla = cxp_dc_spg.estcla ".
				"			AND spg_cuentas.spg_cuenta = cxp_dc_spg.spg_cuenta) AS existe ".		
				"  FROM cxp_dc_spg  ".
				" INNER JOIN cxp_sol_dc ".
				"    ON cxp_dc_spg.codemp='".$this->ls_codemp."' ".
				"   AND cxp_dc_spg.numsol='".$as_numsol."' ".
				"   AND cxp_dc_spg.numrecdoc='".$as_numrecdoc."' ".
				"   AND cxp_dc_spg.codope='".$as_codope."' ".
				"   AND cxp_dc_spg.codtipdoc='".$as_codtipdoc."' ".
				"   AND cxp_dc_spg.numdc='".$as_numncnd."' ".
				"   AND cxp_dc_spg.codemp=cxp_sol_dc.codemp".
				"   AND cxp_dc_spg.numsol=cxp_sol_dc.numsol ".
				"   AND cxp_dc_spg.numrecdoc=cxp_sol_dc.numrecdoc".
				"   AND cxp_dc_spg.codtipdoc=cxp_sol_dc.codtipdoc ".
				"   AND cxp_dc_spg.ced_bene=cxp_sol_dc.ced_bene ".
				"   AND cxp_dc_spg.cod_pro=cxp_sol_dc.cod_pro ".
				"   AND cxp_dc_spg.codope=cxp_sol_dc.codope ".
				"   AND cxp_dc_spg.numdc=cxp_sol_dc.numdc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MTODO->uf_validar_cuentas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 

print $this->io_sql->message;			$lb_valido=false;
		}
		else
		{
			$ls_estaprcxp=$_SESSION["la_empresa"]["estaprcxp"];
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$li_existe=$rs_data->fields["existe"];
				$ls_cuenta=$rs_data->fields["spg_cuenta"];
				$ls_codestpro1=substr($rs_data->fields["codestpro1"],(25-$_SESSION["la_empresa"]["loncodestpro1"]),$_SESSION["la_empresa"]["loncodestpro1"]);
				$ls_codestpro2=substr($rs_data->fields["codestpro2"],(25-$_SESSION["la_empresa"]["loncodestpro2"]),$_SESSION["la_empresa"]["loncodestpro2"]);
				$ls_codestpro3=substr($rs_data->fields["codestpro3"],(25-$_SESSION["la_empresa"]["loncodestpro3"]),$_SESSION["la_empresa"]["loncodestpro3"]);
				$ls_codestpro4=substr($rs_data->fields["codestpro4"],(25-$_SESSION["la_empresa"]["loncodestpro4"]),$_SESSION["la_empresa"]["loncodestpro4"]);
				$ls_codestpro5=substr($rs_data->fields["codestpro5"],(25-$_SESSION["la_empresa"]["loncodestpro5"]),$_SESSION["la_empresa"]["loncodestpro5"]);
				$ls_estcla=$rs_data->fields["estcla"];
				$_SESSION["fechacomprobante"]=$rs_data->fields["fecope"];
				if($li_existe>0)
				{
					$ls_estprog[0]=$rs_data->fields["codestpro1"];
					$ls_estprog[1]=$rs_data->fields["codestpro2"];
					$ls_estprog[2]=$rs_data->fields["codestpro3"];
					$ls_estprog[3]=$rs_data->fields["codestpro4"];
					$ls_estprog[4]=$rs_data->fields["codestpro5"];
					$ls_estprog[5]=$rs_data->fields["estcla"];
					$ls_vali_nivel=$_SESSION["la_empresa"]["vali_nivel"];
					if($ls_vali_nivel==5)
					{
						$ls_formpre=str_replace("-","",$_SESSION["la_empresa"]["formpre"]);
						$ls_vali_nivel=$io_int_spg->uf_spg_obtener_nivel($ls_formpre);
					}
					if($_SESSION["la_empresa"]["estvaldis"]==0)
					{
						$ls_vali_nivel=0;
					}
                                       // var_dump($ls_vali_nivel);
					$li_nivel=$io_int_spg->uf_spg_obtener_nivel($ls_cuenta);
					if ($li_nivel <= $ls_vali_nivel)
					{
						$ls_status="";
						$li_asignado=0;
						$li_aumento=0;
						$li_disminucion=0;
						$li_precomprometido=0;
						$li_comprometido=0;
						$li_causado=0;
						$li_pagado=0;
						$io_int_spg->uf_spg_saldo_select($this->ls_codemp,$ls_estprog,$ls_cuenta,$ls_status,$li_asignado,$li_aumento,$li_disminucion,
														 $li_precomprometido,$li_comprometido,$li_causado,$li_pagado,'ACTUAL');
						$li_disponibilidad=(($li_asignado + $li_aumento) - ( $li_disminucion + $li_comprometido + $li_precomprometido));
                                                print($li_disponibilidad); echo "<hr>";
                                            //   $li_disponibilidad=$li_disponibilidad+$rs_data->fields["monto"];
                                                             
						if(round($rs_data->fields["monto"],2) >= round($li_disponibilidad,2))
						{
                                                       
							$li_monto=number_format($rs_data->fields["monto"],2,",",".");
							$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
                                                       //   echo "fff";
							$this->io_mensajes->message("***No hay Disponibilidad en la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." Disponible=[".$li_disponibilidad."] Cuenta=[".$li_monto."]");
							if($ls_estaprcxp!="1")
							{
								$lb_valido=false;
							}
						}
                                                   // var_dump($lb_valido);
						if($lb_valido)
						{
							$ls_status="";
							$li_asignado=0;
							$li_aumento=0;
							$li_disminucion=0;
							$li_precomprometido=0;
							$li_comprometido=0;
							$li_causado=0;
							$li_pagado=0;
							$io_int_spg->uf_spg_saldo_select($this->ls_codemp,$ls_estprog,$ls_cuenta,$ls_status,$li_asignado,$li_aumento,$li_disminucion,
															 $li_precomprometido,$li_comprometido,$li_causado,$li_pagado,'COMPROBANTE');
							$li_disponibilidad=(($li_asignado + $li_aumento) - ( $li_disminucion + $li_comprometido + $li_precomprometido));
                                                          //$li_disponibilidad=$li_disponibilidad+$rs_data->fields["monto"];
							if(round($rs_data->fields["monto"],2) >= round($li_disponibilidad,2))
							{

                                                               
								$li_monto=number_format($rs_data->fields["monto"],2,",",".");
								$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
                                                                  
								$this->io_mensajes->message("No hay Disponibilidad en la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." Disponible=[".$li_disponibilidad."] Cuenta=[".$li_monto."]"); 
								if($ls_estaprcxp!="1")
								{
									$lb_valido=false;
								}
							}
						}				
					} 	
				}

				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("La cuenta ".$ls_cuenta." No Existe en la Estructura ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla.""); 
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_validar_cuentas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_estatus_nota($as_numsol,$as_numrecdoc,$as_numncnd,$as_codope,$as_codtipdoc,$as_estapr)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_nota
		//		   Access: private
		//	    Arguments: as_numsol  //  Nmero de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe  False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la nota
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creacin: 26/02/2007 								Fecha ltima Modificacin : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numsol ".
				"  FROM cxp_sol_dc ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numsol='".$as_numsol."' ".
				"   AND numrecdoc='".$as_numrecdoc."' ".
				"   AND numdc='".$as_numncnd."' ".
				"   AND codope='".$as_codope."' ".
				"   AND codtipdoc='".$as_codtipdoc."' ".
				"   AND estapr=".$as_estapr."";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MTODO->uf_validar_estatus_nota ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if(!$row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_existe;
	}// end function uf_validar_estatus_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_nota($as_numsol,$as_numrecdoc,$as_numncnd,$as_codope,$as_codtipdoc,$as_estapr,$ls_estnota,$ad_fecapr,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_nota
		//		   Access: private
		//	    Arguments: as_numsol    //  Nmero de Solicitud
		//				   as_numrecdoc //  Nmero de la Recepcion
		//				   as_numncnd   //  Nmero de la Nota		
		//                 as_estsol    //  Estatus en que se desea colocar la solicitud
		//                 ad_fecapr    //  Fecha de aprobacion de la nota
		//                 aa_seguridad //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe  False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la nota
		//	   Creado Por: Ing. Nelson Barraez
		//  Fecha Creacin: 14/06/2007 								Fecha ltima Modificacin : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=$this->io_fecha->uf_valida_fecha_periodo($ad_fecapr,$this->ls_codemp);
		if (!$lb_valido)
		{
			$this->io_mensajes->message($this->io_fecha->is_msg_error);           
			return false;
		}
		$ls_usuario=$_SESSION["la_logusr"];
		if($as_estapr==0)
		{
			$ad_fecapr="1900-01-01";
			$ls_usuario="";
		}
		$ad_fecapr=$this->io_funciones->uf_convertirdatetobd($ad_fecapr);
		$ls_sql="UPDATE cxp_sol_dc ".
				"   SET estapr = ".$as_estapr.", ".
				"       fecaprnc  = '".$ad_fecapr."', ".
				"		codusuapr = '".$ls_usuario."', ".
				"       estnotadc = '".$ls_estnota."' ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND numrecdoc = '".$as_numrecdoc."' ".
				"   AND codope='".$as_codope."' ".
				"   AND codtipdoc='".$as_codtipdoc."' ".
				"	AND numdc = '".$as_numncnd."' ".
				"	AND numsol = '".$as_numsol."' ";
		$this->io_sql->begin_transaction();				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Aprobacion MTODO->uf_update_estatus_nota ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			if($as_estapr==1)
			{
				$ls_descripcion ="Aprobo la Nota  <b>".$as_numncnd."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			}
			else
			{
				$ls_descripcion ="Reverso la Aprobacion de la Nota <b>".$as_numncnd."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			}
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			if($lb_valido)
			{
				$this->io_sql->commit();
			}
			else
			{
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_update_estatus_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_nota($as_numsol,$as_numrecdoc,$as_numncnd,$as_codope,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_nota
		//		   Access: public
		//		 Argument: as_numsol        // Numero de la solicitud de ejecucion presupuestaria
		//	  Description: Funcin que verifica que una nota este en estatus de registrada
		//	   Creado Por: Ing. Nelson Barraez
		//  Fecha Creacin: 14/06/2007								Fecha ltima Modificacin : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT * ".
				"  FROM cxp_sol_dc".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND trim(numrecdoc) = '".trim($as_numrecdoc)."' ".
				"   AND codope='".$as_codope."' ".
				"   AND codtipdoc='".$as_codtipdoc."' ".
				"	AND trim(numdc) = '".trim($as_numncnd)."' ".
				"	AND numsol = '".$as_numsol."' ".
				"   AND estnotadc = 'E' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MTODO->uf_validar_nota ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if(!$row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_existe;
	}// end function uf_validar_nota
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>