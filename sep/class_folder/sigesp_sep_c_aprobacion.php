<?php
class sigesp_sep_c_aprobacion
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;

	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_sep_c_aprobacion($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sep_c_aprobacion
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaciï¿½n: 17/03/2007 								Fecha ï¿½ltima Modificaciï¿½n : 
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
		// Fecha Creaciï¿½n: 17/03/2007								Fecha ï¿½ltima Modificaciï¿½n : 
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
	function uf_load_tiposolicitud($as_seleccionado)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_tiposolicitud
		//		   Access: private
		//		 Argument: as_seleccionado // Valor del campo que va a ser seleccionado
		//	  Description: Funciï¿½n que busca en la tabla de tipo de solicitud los tipos de SEP
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaciï¿½n: 17/03/2007								Fecha ï¿½ltima Modificaciï¿½n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT modsep ".
				"  FROM sep_tiposolicitud ".
				" GROUP BY modsep ".
				" ORDER BY modsep ASC ";	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud Mï¿½TODO->uf_load_tiposolicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			print "<select name='cmbcodtipsol' id='cmbcodtipsol'>";
			print " <option value=''>-- Seleccione Uno --</option>";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_seleccionado="";
				$ls_modsep=trim($row["modsep"]);
				$ls_operacion="";
				switch($ls_modsep)
				{
					case"B":// Bienes
						$ls_dentipsol="Bienes";
						break;
					case"S":// Servicios
						$ls_dentipsol="Servicios";
						break;
					case"O":// Conceptos
						$ls_dentipsol="Conceptos";
						break;
				}
				if($as_seleccionado==$ls_modsep)
				{
					$ls_seleccionado="selected";
				}
				print "<option value='".$ls_modsep."' ".$ls_seleccionado.">".$ls_dentipsol."</option>";
			}
			$this->io_sql->free_result($rs_data);	
			print "</select>";
		}
		return $lb_valido;
	}// end function uf_load_tiposolicitud
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
		//	  Description: Funciï¿½n que busca las solicitudes de ejecucion presupuestaria
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaciï¿½n: 03/02/2007								Fecha ï¿½ltima Modificaciï¿½n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql_seguridad="";
		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="CONCAT(nombene,' ',apebene)";
				$ls_sql_seguridad= " AND CONCAT('".$this->ls_codemp."','SEP','".$_SESSION["la_logusr"]."',spg_unidadadministrativa.coduniadm) IN".
								   " (SELECT CONCAT(codemp,codsis,codusu,codintper) ".
								   "    FROM sss_permisos_internos WHERE codusu = '".$_SESSION["la_logusr"]."' AND codsis = 'SEP')";
				$ls_sql_seguridad = $ls_sql_seguridad." AND CONCAT('".$this->ls_codemp."','SPG','".$_SESSION["la_logusr"]."',sep_solicitud.codestpro1,sep_solicitud.codestpro2,sep_solicitud.codestpro3,sep_solicitud.codestpro4,sep_solicitud.codestpro5,sep_solicitud.estcla)".
													  " IN (SELECT CONCAT(codemp,codsis,codusu,codintper) ".
								   					  "       FROM sss_permisos_internos WHERE codusu = '".$_SESSION["la_logusr"]."' AND codsis = 'SPG')";
				break;
			case "POSTGRES":
				$ls_cadena="nombene||' '||apebene";
				$ls_sql_seguridad= " AND '".$this->ls_codemp."'||'SEP'||'".$_SESSION["la_logusr"]."'||spg_unidadadministrativa.coduniadm IN".
								   " (SELECT codemp||codsis||codusu||codintper".
								   "    FROM sss_permisos_internos WHERE codusu = '".$_SESSION["la_logusr"]."' AND codsis = 'SEP')";
				$ls_sql_seguridad = $ls_sql_seguridad." AND '".$this->ls_codemp."'||'SPG'||'".$_SESSION["la_logusr"]."'||sep_solicitud.codestpro1||sep_solicitud.codestpro2||sep_solicitud.codestpro3||sep_solicitud.codestpro4||sep_solicitud.codestpro5||sep_solicitud.estcla".
													  " IN (SELECT codemp||codsis||codusu||codintper".
								   					  "       FROM sss_permisos_internos WHERE codusu = '".$_SESSION["la_logusr"]."' AND codsis = 'SPG')";
				break;
			case "INFORMIX":
				$ls_cadena="nombene||' '||apebene";
				$ls_sql_seguridad= " AND '".$this->ls_codemp."'||'SEP'||'".$_SESSION["la_logusr"]."'||spg_unidadadministrativa.coduniadm IN".
								   " (SELECT codemp||codsis||codusu||codintper".
								   "    FROM sss_permisos_internos WHERE codusu = '".$_SESSION["la_logusr"]."' AND codsis = 'SEP')";
				$ls_sql_seguridad = $ls_sql_seguridad." AND '".$this->ls_codemp."'||'SPG'||'".$_SESSION["la_logusr"]."'||sep_solicitud.codestpro1||sep_solicitud.codestpro2||sep_solicitud.codestpro3||sep_solicitud.codestpro4||sep_solicitud.codestpro5||sep_solicitud.estcla IN (SELECT codemp||codsis||codusu||codintper
								    FROM sss_permisos_internos WHERE codusu = '".$_SESSION["la_logusr"]."' AND codsis = 'SPG')";
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
				"   AND sep_tiposolicitud.modsep LIKE '".$as_tipo."'".$ls_sql_seguridad.
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
			$this->io_mensajes->message("CLASE->Aprobacion Mï¿½TODO->uf_load_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_cuentas($as_numsol, $ld_fecaprsep)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_cuentas
		//		   Access: private
		//		 Argument: as_numsol // Nï¿½mero de solicitud
		//	  Description: Funciï¿½n que busca que las cuentas presupuestarias estï¿½n en la programï¿½tica seleccionada
		//				   de ser asi puede aprobar la sep de lo contrario no la apruebas
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaciï¿½n: 17/03/2007								Fecha ï¿½ltima Modificaciï¿½n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		require_once("../shared/class_folder/class_sigesp_int.php");
		require_once("../shared/class_folder/class_sigesp_int_int.php");
		require_once("../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../shared/class_folder/class_sigesp_int_spg.php");
		$io_int_spg=new class_sigesp_int_spg();
		$ld_fecaprsep=$this->io_funciones->uf_convertirdatetobd($ld_fecaprsep);
		$ls_sql="SELECT sep_cuentagasto.codestpro1, sep_cuentagasto.codestpro2, sep_cuentagasto.codestpro3, sep_cuentagasto.codestpro4, ".
				"		sep_cuentagasto.codestpro5, sep_cuentagasto.estcla, sep_cuentagasto.spg_cuenta, sep_cuentagasto.monto, sep_solicitud.fecregsol, ".
				"		(SELECT COUNT(codemp) ".
				"		   FROM spg_cuentas ".
				"		  WHERE spg_cuentas.codemp = sep_cuentagasto.codemp ".
				"			AND spg_cuentas.codestpro1 = sep_cuentagasto.codestpro1 ".
				"		    AND spg_cuentas.codestpro2 = sep_cuentagasto.codestpro2 ".
				"		    AND spg_cuentas.codestpro3 = sep_cuentagasto.codestpro3 ".
				"		    AND spg_cuentas.codestpro4 = sep_cuentagasto.codestpro4 ".
				"		    AND spg_cuentas.codestpro5 = sep_cuentagasto.codestpro5 ".
				"		    AND spg_cuentas.estcla = sep_cuentagasto.estcla ".
				"			AND spg_cuentas.spg_cuenta = sep_cuentagasto.spg_cuenta) AS existe ".		
				"  FROM sep_cuentagasto ".
				" INNER JOIN sep_solicitud  ".
				"    ON sep_cuentagasto.codemp='".$this->ls_codemp."' ".
				"   AND sep_cuentagasto.numsol='".$as_numsol."'".
				"   AND sep_cuentagasto.codemp=sep_solicitud.codemp".
				"   AND sep_cuentagasto.numsol=sep_solicitud.numsol";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_cuentas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$ls_estaprsep=$_SESSION["la_empresa"]["estaprsep"];
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
				//$_SESSION["fechacomprobante"]=$rs_data->fields["fecregsol"];//validar con la fecha de registro de la sep
				$_SESSION["fechacomprobante"]=$ld_fecaprsep;// para validar con la fecha de aprobacion de la sep
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
						if(round($rs_data->fields["monto"],2) > round($li_disponibilidad,2))
						{
							$li_monto=number_format($rs_data->fields["monto"],2,",",".");
							$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
							$this->io_mensajes->message("**No hay Disponibilidad en la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." Disponible=[".$li_disponibilidad."] Cuenta=[".$li_monto."]");
							if($ls_estaprsep!="1")
							{
								$lb_valido=false;
							}
						}
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
							if(round($rs_data->fields["monto"],2) > round($li_disponibilidad,2))
							{
								$li_monto=number_format($rs_data->fields["monto"],2,",",".");
								$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
								$this->io_mensajes->message("No hay Disponibilidad en la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." Disponible=[".$li_disponibilidad."] Cuenta=[".$li_monto."]"); 
								if($ls_estaprsep!="1")
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
	function uf_validar_estatus_solicitud($as_numsol,$as_estsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Nï¿½mero de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ï¿½ False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaciï¿½n: 26/02/2007 								Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numsol ".
				"  FROM sep_solicitud ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numsol='".$as_numsol."' ".
				"   AND estapro=".$as_estsol."";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_estatus_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	function uf_update_estatus_solicitud($as_numsol,$as_estsol,$ad_fecaprsep,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol    //  Nï¿½mero de Solicitud
		//                 as_estsol    //  Estatus en que se desea colocar la solicitud
		//                 ad_fecaprsep //  Fecha de aprobacion de la solicitud
		//                 aa_seguridad //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ï¿½ False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaciï¿½n: 17/03/2007 								Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=$this->io_fecha->uf_valida_fecha_periodo($ad_fecaprsep,$this->ls_codemp);
		if (!$lb_valido)
		{
			$this->io_mensajes->message($this->io_fecha->is_msg_error);           
			return false;
		}
		$ls_usuario=$_SESSION["la_logusr"];
		if($as_estsol==0)
		{
			$ad_fecaprsep="1900-01-01";
			$ls_usuario="";
		}
		$ad_fecaprsep=$this->io_funciones->uf_convertirdatetobd($ad_fecaprsep);
		$ls_sql="UPDATE sep_solicitud ".
				"   SET estapro = ".$as_estsol.", ".
				"       fecaprsep = '".$ad_fecaprsep."', ".
				"		codaprusu = '".$ls_usuario."' ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND numsol = '".$as_numsol."' ";
		$this->io_sql->begin_transaction();				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Aprobacion Mï¿½TODO->uf_update_estatus_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			if($as_estsol==1)
			{
				$ls_descripcion ="Aprobï¿½ la Solicitud de Ejecucion <b>".$as_numsol."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			}
			else
			{
				$ls_descripcion ="Reversï¿½ la Aprobacion de la Solicitud de Ejecucion <b>".$as_numsol."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
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
	function uf_validar_solicitudes($as_numsol)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_solicitudes
		//		   Access: public
		//		 Argument: as_numsol        // Numero de la solicitud de ejecucion presupuestaria
		//	  Description: Funciï¿½n que verifica que una solicitud este en estatus de emitida
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaciï¿½n: 17/03/2007								Fecha ï¿½ltima Modificaciï¿½n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numsol".
				"  FROM sep_solicitud".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numsol = '".$as_numsol."'".
				"   AND estsol = 'E' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion Mï¿½TODO->uf_validar_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_validar_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>