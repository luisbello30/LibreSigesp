<?php
class sigesp_ins_c_traspaso_concepto_aporte
{
	var $io_sql;
	var $DS;
	var $DS_R;
	var $io_mensajes;
	var $io_funciones;
	var $io_fun_nomina;
	var $io_sno;
	var $ls_codemp;
	var $ls_codnom;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_ins_c_traspaso_concepto_aporte()
	{	
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_ins_c_traspaso_concepto_aporte
		//		   Access: public (sigesp_sno_c_cierre_periodo)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 15/02/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha Última Modificación : 29/05/2006
		//////////////////////////////////////////////////////////////////////////////
		$this->ls_database_source = $_SESSION["ls_database"];
	    $this->ls_database_target = $_SESSION["ls_database_destino"];
		$this->ls_hostname_target = $_SESSION["ls_hostname_destino"];
		$this->ls_login_target    = $_SESSION["ls_login_destino"];
		$this->ls_password_target = $_SESSION["ls_password_destino"];
		$this->ls_gestor_target   = $_SESSION["ls_gestor_destino"];
		require_once("../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../shared/class_folder/class_sql.php");
		////////////////////////////////////////////////////////////////////////////////////////
		$io_conexion_origen       = $io_include->uf_conectar();
		$io_conexion_destino      = $io_include->uf_conectar_otra_bd ($this->ls_hostname_target, $this->ls_login_target, $this->ls_password_target,$this->ls_database_target,$this->ls_gestor_target);
		$this->io_sql_origen      = new class_sql($io_conexion_origen);
	    $this->io_sql_destino     = new class_sql($io_conexion_destino);
		////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);	
		$this->DS=new class_datastore();
		$this->DS_R=new class_datastore();
		require_once("../shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
   		require_once("../shared/class_folder/class_funciones.php");
		$this->io_funciones=new class_funciones();						
		require_once("../sno/class_folder/class_funciones_nomina.php");
		$this->io_fun_nomina=new class_funciones_nomina();
   		require_once("../sno/sigesp_sno.php");
		$this->io_sno=new sigesp_sno();						
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		
	}// end function sigesp_ins_c_traspaso_concepto_aporte
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_sessionnomina($ls_nomina)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_sessionnomina
		//		   Access: public (en toda las pantallas de procesos)
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que crea la sessión de la nómina actual
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/02/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT sno_nomina.codnom, sno_nomina.desnom, sno_nomina.peractnom, sno_nomina.diabonvacnom, sno_nomina.diareivacnom, ".
				"		sno_nomina.adenom, sno_periodo.fecdesper, sno_periodo.fechasper, sno_periodo.cerper, sno_periodo.conper,".
				"		sno_periodo.totper, sno_nomina.anocurnom, sno_nomina.tippernom, sno_nomina.perresnom, sno_nomina.racnom, ".
				"		sno_nomina.subnom, sno_nomina.espnom, sno_nomina.consulnom, sno_nomina.descomnom, sno_nomina.codpronom, ".
				"		sno_nomina.codbennom, sno_nomina.conaponom, sno_nomina.cueconnom, sno_nomina.notdebnom, ".
				"       sno_nomina.numvounom, sno_nomina.recdocnom, sno_nomina.recdocapo, sno_nomina.tipdocnom, sno_nomina.tipdocapo, ".
				"		sno_nomina.tipnom, sno_nomina.fecininom, sno_nomina.numpernom, sno_nomina.conpernom, sno_nomina.conpronom, ".
				"		sno_nomina.titrepnom, sno_nomina.divcon, sno_nomina.subnom, sno_nomina.informa, ".
				"       sno_nomina.recdocpagperche, sno_nomina.tipdocpagperche, sno_nomina.estctaalt, sno_nomina.racobrnom ".
				"  FROM sno_nomina, sno_periodo ".
				" WHERE sno_nomina.codemp='0001' ".
				"   AND sno_nomina.codnom='".$ls_nomina."' ".
				"   AND sno_nomina.codemp=sno_periodo.codemp ".
				"   AND sno_nomina.codnom=sno_periodo.codnom ".
				"   AND sno_nomina.peractnom=sno_periodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SNO MÉTODO->p_concepto_aporte_trans ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				unset($_SESSION["la_nomina"]);
				$_SESSION["la_nomina"]=$row;
				$ld_fecdesper=$this->io_funciones->uf_formatovalidofecha($_SESSION["la_nomina"]["fecdesper"]);
				$ld_fechasper=$this->io_funciones->uf_formatovalidofecha($_SESSION["la_nomina"]["fechasper"]);
				$_SESSION["la_nomina"]["fecdesper"]=$ld_fecdesper;
				$_SESSION["la_nomina"]["fechasper"]=$ld_fechasper;
				$ld_fecdesper=$this->io_funciones->uf_convertirfecmostrar($ld_fecdesper);
				$ld_fechasper=$this->io_funciones->uf_convertirfecmostrar($ld_fechasper);
				$ls_desper=" Año <strong>".$_SESSION["la_nomina"]["anocurnom"]."</strong> Período <strong>".$_SESSION["la_nomina"]["peractnom"]."</strong> ".$ld_fecdesper." - ".$ld_fechasper."";
				$_SESSION["la_nomina"]["descripcionperiodo"]=$ls_desper;
				$_SESSION["la_nomina"]["tiponomina"]="NORMAL";
			}
			else
			{
				unset($_SESSION["la_nomina"]);
				$lb_valido=false;
				$this->io_mensajes->message("Favor verifique los datos de la nómina. No se pueden cargar los datos."); 
				print "<script language=JavaScript>";
				print "location.href='sigespwindow_blank.php'";
				print "</script>";		
			}
			$this->io_sql->free_result($rs_data);
		}
      	return ($lb_valido);  
    }// end function uf_crear_sessionnomina	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reversar_acumular_conceptos($as_codperi_abrir)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_acumular_conceptos 
		//	    Arguments: as_codperi_abrir // codigo del periodo abrir 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga reversar el acumulado de los conceptos al momento de abrir un periodo
	    //     Creado por: Ing. Yozelin Barragán
	    // Fecha Creación: 10/02/2006 
		// Modificado Por: Ing. Yesenia Moreno						Fecha Última Modificación : 29/05/2006
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT tipsal,valsal,codper,codconc ".
				"  FROM sno_salida ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codperi='".$as_codperi_abrir."' ".
				"   AND (tipsal='A ' OR tipsal='D ' OR tipsal='P1' OR tipsal='P2' OR tipsal='R') ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
		  $lb_valido=false;
		  $this->io_mensajes->message("CLASE->Cierre Periodo MÉTODO->uf_reversar_acumular_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_tipsal=$row["tipsal"];
				$ld_valsal=$row["valsal"];
				$ls_codper=$row["codper"];
				$ls_codconc=$row["codconc"];			   
				if($ls_tipsal=="P2")
				{
					$ls_sql="UPDATE sno_conceptopersonal ".
						    "   SET acupat=(acupat - ".$ld_valsal.") ".
						    " WHERE codemp='".$this->ls_codemp."' ".
						    "   AND codnom='".$this->ls_codnom."' ".
						    "   AND codper='".$ls_codper."' ".
						    "   AND codconc='".$ls_codconc."' ";  
				}//if
				else
				{
					$ls_sql="UPDATE sno_conceptopersonal ".
						    "   SET acuemp=(acuemp - ".$ld_valsal.") ".
						    " WHERE codemp='".$this->ls_codemp."' ".
						    "   AND codnom='".$this->ls_codnom."' ".
						    "   AND codper='".$ls_codper."' ".
						    "   AND codconc='".$ls_codconc."' ";
				}
			   $li_row=$this->io_sql->execute($ls_sql);
			   if($li_row===false)
			   {
					$lb_valido=false;
		            $this->io_mensajes->message("CLASE->Cierre Periodo MÉTODO->uf_reversar_acumular_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			   }
			}//while
		}
		return $lb_valido;	
	}// end function uf_reversar_acumular_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reversar_actualizar_periodo($as_codperi_abrir)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_actualizar_periodo 
		//	    Arguments: as_codperi_abrir // codigo del periodo abrir 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se enacarga de reversar el perido, ya cerrado para proceder abrir otro
	    //     Creado por: Ing. Yozelin Barragán
	    // Fecha Creación: 11/02/2006          Fecha última Modificacion : 
		// Modificado Por: Ing. Yesenia Moreno						Fecha Última Modificación : 29/05/2006
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_periodo ".
				"   SET cerper=0, ".
				"       conper=0, ".
				"       apoconper=0, ".
				"       ingconper=0, ".
				"       fidconper=0 ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codperi='".$as_codperi_abrir."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo4 MÉTODO->uf_reversar_actualizar_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ls_sql="UPDATE sno_nomina ".
					"   SET peractnom='".$as_codperi_abrir."' ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Cierre Periodo4 MÉTODO->uf_reversar_actualizar_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
		}    			   
		return $lb_valido;		
	}// end function uf_reversar_actualizar_periodo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_contabilizacion($as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion 
		//	    Arguments: as_codperi_actual  //  codigo del periodo a cerrar
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización del período
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 31/05/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$ld_fecdesper=$this->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
		$ld_fechasper=$this->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
		$ls_codcom=$ls_anocurnom."-".$ls_codnom."-".$ls_codperi."-N"; // Comprobante de Conceptos
		$ls_codcoming=$ls_anocurnom."-".$ls_codnom."-".$ls_codperi."-I"; // Comprobante de Ingresos
		$ls_codcomapo=$ls_anocurnom."-".$ls_codnom."-".$ls_codperi."-A"; // Comprobante de Aportes
		$ls_descripcion=$ls_desnom."- Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper; // Descripción de Conceptos
		$ls_descripcioning=$ls_desnom." INGRESOS - Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper; // Descripción de Conceptos
		$ls_descripcionapo=$ls_desnom." APORTES - Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper; // Descripción de Aportes
		
		$ls_descripcion_int=$ls_desnom."- Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper." Asiento Intercompañia"; // Descripción de Conceptos Asiento intercomapñias
		
		$ls_descripcionapo_int=$ls_desnom." APORTES - Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper." Asiento Intercompañia"; // Descripción de Aportes Asientos intercompañias
		
		$ls_cuentapasivo="";
		$ls_operacionnomina="";
		$ls_operacionaporte="";
		$ls_tipodestino="";
		$ls_codpro="";
		$ls_codben="";
		$li_gennotdeb="";
		$li_genvou="";
		$li_genrecdoc="";
		$li_genrecapo="";
		$li_tipdocnom="";
		$li_tipdocapo="";
		// Obtenemos la configuración de la contabilización de la nómina
		$lb_valido=$this->uf_load_configuracion_contabilizacion($ls_cuentapasivo,$ls_operacionnomina,$ls_operacionaporte,
																$ls_tipodestino,$ls_codpro,$ls_codben,$li_gennotdeb,$li_genvou,
																$li_genrecdoc,$li_genrecapo,$li_tipdocnom,$li_tipdocapo);
		
		/*if($lb_valido)
		{	// eliminamos la contabilización anterior 
			$lb_valido=$this->uf_delete_contabilizacion($ls_codperi);
		}*/														
		if($lb_valido)
		{ // insertamos la contabilización de presupuesto de conceptos
			$lb_valido=$this->uf_contabilizar_conceptos_spg($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
															$ls_tipodestino,$ls_descripcion,$li_genrecdoc,$li_tipdocnom,
															$li_gennotdeb,$li_genvou);
		}
		if($lb_valido)
		{// insertamos la contabilización de contabilidad de conceptos
			if($ls_operacionnomina!="O")// Si es compromete no genero detalles contables
			{
				$lb_valido=$this->uf_contabilizar_conceptos_scg($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
																$ls_tipodestino,$ls_descripcion,$ls_cuentapasivo,$li_genrecdoc,
																$li_tipdocnom,$li_gennotdeb,$li_genvou);
				//para contabilizar cuentas contables de intercompañias
				$lb_valido=$this->uf_contabilizar_conceptos_scg_int($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
				                                                    $ls_tipodestino,$ls_descripcion_int,$ls_cuentapasivo,
																	$li_genrecdoc,$li_tipdocnom,$li_gennotdeb,
																	$li_genvou);
			}
		}
		if($lb_valido)
		{ // insertamos la contabilización de presupuesto de aportes
			$lb_valido=$this->uf_contabilizar_aportes_spg($ls_codcomapo,$ls_operacionaporte,$ls_codpro,$ls_codben,
														  $ls_tipodestino,$ls_descripcionapo,$ls_cuentapasivo,
														  $li_genrecapo,$li_tipdocapo,$li_gennotdeb,$li_genvou);
		}
		if($lb_valido)
		{// insertamos la contabilización de contabilidad de aportes
			if($ls_operacionaporte!="O")// Si es compromete no genero detalles contables
			{
				$lb_valido=$this->uf_contabilizar_aportes_scg($ls_codcomapo,$ls_codpro,$ls_codben,$ls_tipodestino,
				                                              $ls_descripcionapo,$li_genrecapo,$li_tipdocapo,$li_gennotdeb,
															  $li_genvou,$ls_operacionaporte);
				//para contabilizar cuentas contables de intercompañias
				$lb_valido=$this->uf_contabilizar_aportes_scg_int($ls_codcomapo,$ls_codpro,$ls_codben,$ls_tipodestino,
				                                                  $ls_descripcionapo_int,$li_genrecapo,$li_tipdocapo, 
																  $li_gennotdeb,$li_genvou,$ls_operacionaporte);
			}
		}
		if($lb_valido)
		{ // insertamos la contabilización de ingresos
			$lb_valido=$this->uf_contabilizar_ingresos_spi($ls_codcoming,$ls_descripcioning);
		}
		if($lb_valido)
		{ // insertamos la contabilización de ingresos
			$lb_valido=$this->uf_contabilizar_ingresos_scg($ls_codcoming,$ls_descripcioning);
		}
		/*if($lb_valido)
		{
			$lb_valido=$this->uf_verificar_contabilizacion($ls_codcom); // Nómina
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_verificar_contabilizacion($ls_codcomapo); // Aportes
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_verificar_contabilizacion($ls_codcoming); // Ingreso
		}*/
		if(($lb_valido)&&($ls_codnom=='0002'))
		{
			$this->io_sql_destino->commit(); 
			$this->io_mensajes->message("Se traspaso la data con exito.");
		}
		elseif((!$lb_valido)&&($ls_codnom=='0002'))
		{
			$this->io_sql_destino->rollback();
			$this->io_mensajes->message("Ocurrio un error al pasar la data.");
		}
		return  $lb_valido;    
	}// end function uf_procesar_contabilizacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_configuracion_contabilizacion(&$as_cuentapasivo,&$as_modo,&$as_modoaporte,&$as_destino,&$as_codpro,&$as_codben,
												   &$ai_gennotdeb,&$ai_genvou,&$ai_genrecdoc,&$ai_genrecapo,&$ai_tipdocnom,&$ai_tipdocapo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_configuracion_contabilizacion 
		//	    Arguments: as_cuentapasivo  //  cuenta pasivo a la que va la nómina
		//	    		   as_modo  //  modo de contabilización de la nómina
		//	    		   as_modoaporte  //  modo de contabilización de los aportes
		//	    		   as_destino  //  destino de la contabilización
		//	    		   as_codpro  //  código de proveedor
		//	    		   as_codben  // código de beneficiario
		//	    		   ai_gennotdeb  // generar nota de débito
		//	    		   ai_genvou  // generar voucher
		//	    		   ai_genrecdoc  // generar recepción de documento
		//	    		   ai_genrecapo  // generar recepción de documento de aporte
		//	    		   ai_tipdocnom  // Tipo de documento de nómina
		//	    		   ai_tipdocapo  // Tipo de documento de aporte
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que busca los datos de la configuración de la contabilización de la nómina
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 31/05/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$li_parametros=$this->io_sno->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I");
		switch($li_parametros)
		{
			case 0: // La contabilización es global
				$as_cuentapasivo=$this->io_sno->uf_select_config("SNO","CONFIG","CTA.CONTA","-------------------------","C");
				$as_modo=$this->io_sno->uf_select_config("SNO","NOMINA","CONTABILIZACION","OCP","C");
				$as_modoaporte=$this->io_sno->uf_select_config("SNO","NOMINA","CONTABILIZACION APORTES","OCP","C");
				$as_destino=$this->io_sno->uf_select_config("SNO","NOMINA","CONTABILIZACION DESTINO","","C");
				$ai_gennotdeb=$this->io_sno->uf_select_config("SNO","CONFIG","GENERAR NOTA DEBITO","1","I");
				$ai_genvou=$this->io_sno->uf_select_config("SNO","CONFIG","VOUCHER GENERAR","1","I");
				$ai_genrecdoc=str_pad($this->io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO","0","I"),1,"0");
				$ai_genrecapo=str_pad($this->io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO APORTE","0","I"),1,"0");
				$ai_tipdocnom=$this->io_sno->uf_select_config("SNO","CONFIG","TIPO DOCUMENTO NOMINA","","C");
				$ai_tipdocapo=$this->io_sno->uf_select_config("SNO","CONFIG","TIPO DOCUMENTO APORTE","","C");
				switch (substr($as_destino,0,1))
				{
					case "P":
						$as_codpro=substr($as_destino,1,strlen($as_destino)-1);
						$as_destino="P";
						$as_codben="----------";
						break;
						
					case "B":
						$as_codben=substr($as_destino,1,strlen($as_destino)-1);
						$as_codpro="----------";
						$as_destino="B";
						break;
						
					default:
						$ls_con_descon=substr($as_destino,1,strlen($as_destino)-1);
						$as_destino=" ";
						$as_codpro="----------";
						$as_codben="----------";
				}
				break;
				
			case 1: // La contabilización es por nómina
				$as_cuentapasivo=trim($_SESSION["la_nomina"]["cueconnom"]);
				$as_modo=trim($_SESSION["la_nomina"]["consulnom"]);
				$as_modoaporte=trim($_SESSION["la_nomina"]["conaponom"]);
				$as_destino=trim($_SESSION["la_nomina"]["descomnom"]);
				$as_codpro=str_pad(trim($_SESSION["la_nomina"]["codpronom"]),10,"-");
				$as_codben=trim($_SESSION["la_nomina"]["codbennom"]);
				if(trim($as_codben)=="")
				{
					$as_codben=str_pad(trim($_SESSION["la_nomina"]["codbennom"]),10,"-");			
				}
				$ai_gennotdeb=trim($_SESSION["la_nomina"]["notdebnom"]);
				$ai_genvou=str_pad(trim($_SESSION["la_nomina"]["numvounom"]),1,"0");
				$ai_genrecdoc=str_pad(trim($_SESSION["la_nomina"]["recdocnom"]),1,"0");
				$ai_genrecapo=str_pad(trim($_SESSION["la_nomina"]["recdocapo"]),1,"0");
				$ai_tipdocnom=trim($_SESSION["la_nomina"]["tipdocnom"]);
				$ai_tipdocapo=trim($_SESSION["la_nomina"]["tipdocapo"]);
				break;
		}
		if(trim($as_destino)=="")
		{
			$lb_valido=false;
			$this->io_mensajes->message("ERROR-> La nómina debe tener una Destino de Contabilización (Proveedor ó Beneficiario).");
		}
		else
		{
			if($as_destino=="P") // Es un proveedor
			{
				if(trim($as_codpro)=="")
				{
					$lb_valido=false;
					$this->io_mensajes->message("ERROR-> Debe Seleccionar un Proveedor.");
				}
			}
			if($as_destino=="B") // Es un Beneficiario
			{
				if(trim($as_codpro)=="")
				{
					$lb_valido=false;
					$this->io_mensajes->message("ERROR-> Debe Seleccionar un Beneficiario. ");
				}
			}
		}
		if($ai_genrecdoc=="1") // Genera recepción de Documento de la Nómina.
		{
			if(trim($ai_tipdocnom)=="")
			{
				$lb_valido=false;
				$this->io_mensajes->message("ERROR-> Debe Seleccionar un Tipo de Documento,Para la Recepción de Documento de la Nómina. ");
			}
		}
		if($ai_genrecapo=="1") // Genera recepción de Documento de los aportes
		{
			if(trim($ai_tipdocapo)=="")
			{
				$lb_valido=false;
				$this->io_mensajes->message("ERROR-> Debe Seleccionar un Tipo de Documento,Para la Recepción de Documento de los Aportes. ");
			}
		}
		return  $lb_valido;    
	}// end function uf_load_configuracion_contabilizacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_contabilizacion($as_peractnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_contabilizacion
		//	      Returns: lb_valido True si se ejecuto el delete ó False si hubo error en el delete
		//	  Description: Funcion que elimina la contabilización de los conceptos en spg
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 31/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE ".
				"  FROM sno_dt_spg ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codperi='".$as_peractnom."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_delete_conceptos_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		if($lb_valido)
		{
			$ls_sql="DELETE ".
					"  FROM sno_dt_scg ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ".
					"   AND codperi='".$as_peractnom."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_delete_conceptos_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}		
		}
		if($lb_valido)
		{
			$ls_sql="DELETE ".
					"  FROM sno_dt_spi ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ".
					"   AND codperi='".$as_peractnom."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_delete_conceptos_spi ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}		
		}
		if($lb_valido)
		{
			$ls_sql="DELETE ".
					"  FROM sno_dt_scg_int ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ".
					"   AND codperi='".$as_peractnom."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_delete_conceptos_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}		
		}
		return $lb_valido;
    }// end function uf_delete_contabilizacion
	//-----------------------------------------------------------------------------------------------------------------------------------   
	function uf_insert_contabilizacion_spg($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
									 	   $as_programatica,$as_estcla,$as_cueprecon,$ai_monto,$as_tipnom,$as_codconc,
										   $ai_genrecdoc,$ai_tipdoc,
										   $ai_gennotdeb,$ai_genvou,$as_codcomapo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_contabilizacion_spg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_programatica  //  Programática
		//	    		   as_cueprecon  //  cuenta presupuestaria
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdoc  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_codcomapo  //  Código del comprobante de aporte
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el total des las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/06/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
		$ls_codestpro1=substr($as_programatica,0,25);
		$ls_codestpro2=substr($as_programatica,25,25);
		$ls_codestpro3=substr($as_programatica,50,25);
		$ls_codestpro4=substr($as_programatica,75,25);
		$ls_codestpro5=substr($as_programatica,100,25);		
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$as_codcom=substr($as_codcom,2,15);
		$concat="P-";
		if($as_codpro=="")
		{
		 $as_codpro="----------";
		 $as_tipodestino="B";
		}
		$ls_sql="INSERT INTO sno_dt_spg(codemp,codnom,codperi,codcom,tipnom,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,".
				"spg_cuenta,operacion,codconc,cod_pro,ced_bene,tipo_destino,descripcion,monto,estatus,estrd,codtipdoc,estnumvou,".
				"estnotdeb,codcomapo,estcla, codfuefin) VALUES ('0001','".$ls_codnom."','".$ls_codperi."','".$concat.$as_codcom."',".
				"'".$as_tipnom."','".$ls_codestpro1."','".$ls_codestpro2."','".$ls_codestpro3."','".$ls_codestpro4."','".$ls_codestpro5."',".
				"'".$as_cueprecon."','".$as_operacionnomina."','".$as_codconc."','".$as_codpro."','".$as_codben."','".$as_tipodestino."',".
				"'".$as_descripcion."',".$ai_monto.",".$li_estatus.",".$ai_genrecdoc.",'".$ai_tipdoc."',".$ai_genvou.",".$ai_gennotdeb.",".
				"'".$as_codcomapo."','".$as_estcla."','--')";	
		$li_row=$this->io_sql_destino->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_insert_contabilizacion_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_contabilizacion_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_contabilizacion_scg($as_codcom,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,$as_cuenta,$as_operacion,
									 	   $ai_monto,$as_tipnom,$as_codconc,$ai_genrecdoc,$ai_tipdoc,$ai_gennotdeb,$ai_genvou,$as_codcomapo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_contabilizacion_scg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_programatica  //  Programática
		//	    		   as_cueprecon  //  cuenta presupuestaria
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilización es aporte ó de conceptos
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   as_codconc  //  Código de concepto
		//	    		   ai_tipdoc  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_codcomapo  //  Código del comprobante de aporte
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el total des las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/06/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		$as_codcom=substr($as_codcom,2,15);
		$concat="P-";
		$li_estatus=0; // No contabilizado
		$ls_sql="INSERT INTO sno_dt_scg(codemp,codnom,codperi,codcom,tipnom,sc_cuenta,debhab,codconc,cod_pro,ced_bene,tipo_destino,".
				"descripcion,monto,estatus,estrd,codtipdoc,estnumvou,estnotdeb,codcomapo) VALUES ('0001','".$ls_codnom."',".
				"'".$ls_codperi."','".$concat.$as_codcom."','".$as_tipnom."','".$as_cuenta."','".$as_operacion."','".$as_codconc."',".
				"'".$as_codpro."','".$as_codben."','".$as_tipodestino."','".$as_descripcion."',".$ai_monto.",".$li_estatus.",".
				"'".$ai_genrecdoc."','".$ai_tipdoc."','".$ai_genvou."','".$ai_gennotdeb."','".$as_codcomapo."')";
		$li_row=$this->io_sql_destino->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_insert_contabilizacion_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_contabilizacion_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_contabilizacion_spi($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
									 	   $as_spicuenta,$ai_monto,$as_tipnom,$as_codconc,$ai_genrecdoc,$ai_tipdoc,
										   $ai_gennotdeb,$ai_genvou,$as_codcomapo,$as_codestpro1,$as_codestpro2,$as_codestpro3,
										   $as_codestpro4,$as_codestpro5,$as_estcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_contabilizacion_spi
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_spicuenta  //  cuenta de ingresos
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdoc  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_codcomapo  //  Código del comprobante de aporte
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el total des las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/06/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
		$as_codcom=substr($as_codcom,2,15);
		$concat="P-";
		$ls_sql="INSERT INTO sno_dt_spi(codemp,codnom,codperi,codcom,tipnom,spi_cuenta,operacion,codconc,cod_pro,ced_bene,tipo_destino,".
				"descripcion,monto,estatus,estrd,codtipdoc,estnumvou,estnotdeb,codcomapo, ".
				"codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla) VALUES ('0001',".
				"'".$ls_codnom."','".$ls_codperi."','".$concat.$as_codcom."','".$as_tipnom."','".$as_spicuenta."','".$as_operacionnomina."',".
				"'".$as_codconc."','".$as_codpro."','".$as_codben."','".$as_tipodestino."','".$as_descripcion."',".$ai_monto.",".$li_estatus.",".
				"".$ai_genrecdoc.",'".$ai_tipdoc."',".$ai_genvou.",".$ai_gennotdeb.",'".$as_codcomapo."',".
				" '".$as_codestpro1."', '".$as_codestpro2."', '".$as_codestpro3."', '".$as_codestpro4."', '".$as_codestpro5."', ".
				" '".$as_estcla."')";	
		$li_row=$this->io_sql_destino->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_insert_contabilizacion_spi ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_contabilizacion_spi
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_conceptos_spg($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
										   $ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,$ai_genvou)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_conceptos 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdocnom  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 31/05/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		//$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="N"; // tipo de contabilización
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$ls_sql="SELECT sno_concepto.codpro as programatica, spg_cuentas.spg_cuenta as cueprecon, sum(sno_salida.valsal) AS total, sno_concepto.estcla ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='0001' ".
				"   AND sno_salida.codnom='".$ls_codnom."' ".
				"   AND sno_salida.codperi='".$ls_codperi."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"	AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND substr(sno_concepto.codpro,1,25)  = spg_cuentas.codestpro1 ".
				"   AND substr(sno_concepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_concepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_concepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_concepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codpro, spg_cuentas.spg_cuenta ,sno_concepto.estcla ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codprouniadm as programatica, spg_cuentas.spg_cuenta as cueprecon, sum(sno_salida.valsal) as total, sno_unidadadmin.estcla".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='0001' ".
				"   AND sno_salida.codnom='".$ls_codnom."' ".
				"   AND sno_salida.codperi='".$ls_codperi."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND substr(sno_unidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_unidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_unidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_unidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_unidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codprouniadm , spg_cuentas.spg_cuenta,sno_unidadadmin.estcla ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codpro as programatica, spg_cuentas.spg_cuenta as cueprecon, sum(sno_salida.valsal) as total, sno_concepto.estcla ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='0001' ".
				"   AND sno_salida.codnom='".$ls_codnom."' ".
				"   AND sno_salida.codperi='".$ls_codperi."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND substr(sno_concepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_concepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_concepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_concepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_concepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codpro, spg_cuentas.spg_cuenta, sno_concepto.estcla ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codprouniadm as programatica, spg_cuentas.spg_cuenta as cueprecon, sum(sno_salida.valsal) as total, sno_unidadadmin.estcla".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='0001' ".
				"   AND sno_salida.codnom='".$ls_codnom."' ".
				"   AND sno_salida.codperi='".$ls_codperi."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND substr(sno_unidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_unidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_unidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_unidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_unidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codprouniadm,spg_cuentas.spg_cuenta,sno_unidadadmin.estcla ".
				" ORDER BY programatica, cueprecon,estcla";
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_contabilizar_conceptos_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message));
			$lb_valido=false;
		}
		else
		{
				while((!$rs_data->EOF)&&($lb_valido))
				{
					$ls_programatica=$rs_data->fields["programatica"];
					$ls_estcla=$rs_data->fields["estcla"];
					$ls_cueprecon=$rs_data->fields["cueprecon"];
					$li_total=round($rs_data->fields["total"],2);
					$ls_codconc="0000000001";
					$ls_codcomapo=substr($ls_codconc,2,8).$ls_codperi.$ls_codnom;
					$lb_existe=$this->uf_select_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_programatica,$ls_estcla,
					                                                $ls_cueprecon,$as_operacionnomina,$ls_codconc);
					if (!$lb_existe)
					{
					
						$lb_valido=$this->uf_insert_contabilizacion_spg($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,
																	$as_tipodestino,$as_descripcion,$ls_programatica,$ls_estcla,
																	$ls_cueprecon,$li_total,$ls_tipnom,$ls_codconc,$ai_genrecdoc,
																	$ai_tipdocnom,$ai_gennotdeb,$ai_genvou,$ls_codcomapo);
					}
					else
					{
						/*/$lb_valido=$this->uf_update_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_programatica,$ls_estcla,
					                                                $ls_cueprecon,$as_operacionnomina,$ls_codconc,$li_total);*/						
					}
					$rs_data->MoveNext();
				}
				
			}
			$rs_data->Close();
			
		return $lb_valido;    
	}// end function uf_contabilizar_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_conceptos_scg($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
										   $as_cuentapasivo,$ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,$ai_genvou)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_conceptos_scg 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_cuentapasivo  //  cuenta pasivo
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdocnom  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 31/05/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="N";
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		
			    $ls_sql= " SELECT cuenta, operacion, total               ".
				         "   FROM contableconceptos_contable             ".
						 "  WHERE codemp='0001'          ".
						 "	 AND codnom='".$ls_codnom."'           ".
						 "	 AND codperi='".$ls_codperi."'       ".
						 " UNION                                         ".
						 " SELECT cuenta, operacion, total               ".
						 "   FROM contableconceptos_contable_intercom    ".
						  "  WHERE codemp='0001'         ".
						 "	 AND codnom='".$ls_codnom."'           ".
						 "	 AND codperi='".$ls_codperi."'       "; 
		if($as_operacionnomina=="OC") // Si el modo de contabilizar la nómina es Compromete y Causa tomamos la cuenta pasivo de la nómina.
		{
			if($ai_genrecdoc=="0") // No se genera Recepción de Documentos
			{
				// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena="CONVERT('".$as_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "POSTGRES":
						$ls_cadena="CAST('".$as_cuentapasivo."' AS char(25)) as cuenta";
						break;					
					case "INFORMIX":
						$ls_cadena="CAST('".$as_cuentapasivo."' AS char(25)) as cuenta";
						break;					
				}
				$ls_sql=$ls_sql." UNION ".
						"SELECT ".$ls_cadena.", CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
						"  FROM sno_personalnomina, sno_salida, sno_banco, scg_cuentas ".
						" WHERE sno_salida.codemp = '0001' ".
						"   AND sno_salida.codnom = '".$ls_codnom."' ".
						"   AND sno_salida.codperi = '".$ls_codperi."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1) ".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND scg_cuentas.sc_cuenta = '".$as_cuentapasivo."' ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_salida.codemp = sno_banco.codemp ".
						"   AND sno_salida.codnom = sno_banco.codnom ".
						"   AND sno_salida.codperi = sno_banco.codperi ".
						"   AND sno_personalnomina.codemp = sno_banco.codemp ".
						"   AND sno_personalnomina.codban = sno_banco.codban ".
						"   AND scg_cuentas.codemp = sno_banco.codemp ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			else // Se genera Recepción de documentos
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_nomina, rpc_proveedor ".
						" WHERE sno_salida.codemp = '0001' ".
						"   AND sno_salida.codnom = '".$ls_codnom."' ".
						"   AND sno_salida.codperi = '".$ls_codperi."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_nomina.descomnom = 'P'".
						"   AND sno_nomina.codemp = sno_salida.codemp ".
						"   AND sno_nomina.codnom = sno_salida.codnom ".
						"   AND sno_nomina.peractnom = sno_salida.codperi ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_nomina.codemp = rpc_proveedor.codemp ".
						"   AND sno_nomina.codpronom = rpc_proveedor.cod_pro ".
						"   AND rpc_proveedor.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_nomina, rpc_beneficiario ".
						" WHERE sno_salida.codemp = '0001' ".
						"   AND sno_salida.codnom = '".$ls_codnom."' ".
						"   AND sno_salida.codperi = '".$ls_codperi."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_nomina.descomnom = 'B'".
						"   AND sno_nomina.codemp = sno_salida.codemp ".
						"   AND sno_nomina.codnom = sno_salida.codnom ".
						"   AND sno_nomina.peractnom = sno_salida.codperi ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_nomina.codemp = rpc_beneficiario.codemp ".
						"   AND sno_nomina.codbennom = rpc_beneficiario.ced_bene ".
						"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
					"  FROM sno_personalnomina, sno_salida, scg_cuentas ".
					" WHERE sno_salida.codemp = '0001' ".
					"   AND sno_salida.codnom = '".$ls_codnom."' ".
					"   AND sno_salida.codperi = '".$ls_codperi."' ".
					"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
					"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
					"   AND sno_salida.valsal <> 0".
					"   AND sno_personalnomina.pagbanper = 0 ".
					"   AND sno_personalnomina.pagtaqper = 0 ".
					"   AND sno_personalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND scg_cuentas.codemp = sno_personalnomina.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_personalnomina.cueaboper ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_banco, scg_cuentas ".
					" WHERE sno_salida.codemp = '0001' ".
					"   AND sno_salida.codnom = '".$ls_codnom."' ".
					"   AND sno_salida.codperi = '".$ls_codperi."' ".
					"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
					"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
					"   AND sno_salida.valsal <> 0".
					"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1) ".
					"   AND sno_personalnomina.pagefeper = 0 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_banco.codemp ".
					"   AND sno_salida.codnom = sno_banco.codnom ".
					"   AND sno_salida.codperi = sno_banco.codperi ".
					"   AND sno_personalnomina.codemp = sno_banco.codemp ".
					"   AND sno_personalnomina.codban = sno_banco.codban ".
					"   AND scg_cuentas.codemp = sno_banco.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_banco.codcuecon ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
					"  FROM sno_personalnomina, sno_salida, scg_cuentas ".
					" WHERE sno_salida.codemp = '0001' ".
					"   AND sno_salida.codnom = '".$ls_codnom."' ".
					"   AND sno_salida.codperi = '".$ls_codperi."' ".
					"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
					"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
					"   AND sno_salida.valsal <> 0".
					"   AND sno_personalnomina.pagbanper = 0 ".
					"   AND sno_personalnomina.pagtaqper = 0 ".
					"   AND sno_personalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND scg_cuentas.codemp = sno_personalnomina.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_personalnomina.cueaboper ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_contabilizar_conceptos_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message));
			$lb_valido=false;
		}
		else
		{
				while((!$rs_data->EOF)&&($lb_valido))
				{
					$ls_cuenta=$rs_data->fields["cuenta"];
					$ls_operacion=$rs_data->fields["operacion"];
					$li_total=abs($rs_data->fields["total"]);
					$ls_codconc="0000000001";
					$ls_codcomapo=substr($ls_codconc,2,8).$ls_codperi.$ls_codnom;

					$lb_existe=$this->uf_select_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,$ls_codconc);
					if (!$lb_existe)
					{
						$lb_valido=$this->uf_insert_contabilizacion_scg($as_codcom,$as_codpro,$as_codben,$as_tipodestino,
																		$as_descripcion,$ls_cuenta,$ls_operacion,$li_total,
																		$ls_tipnom,$ls_codconc,$ai_genrecdoc,$ai_tipdocnom,
																		$ai_gennotdeb,$ai_genvou,$ls_codcomapo);
					}
					else
					{
						/*$lb_valido=$this->uf_update_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,
																		$ls_codconc,$li_total);						*/
					}
					$rs_data->MoveNext();
				}				
			
			$rs_data->Close();
		}	
		return $lb_valido;	  
	}// end function uf_contabilizar_conceptos_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_aportes_spg($as_codcom,$as_operacionaporte,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
										 $as_cuentapasivo,$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_aportes_spg 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionaporte  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecapo  //  Generar recepción de documento
		//	    		   ai_tipdocapo  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 31/05/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="A"; // tipo de contabilización
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codpro as programatica, spg_cuentas.spg_cuenta as cueprepatcon, sum(sno_salida.valsal) as total, sno_concepto.estcla, ".
				"		sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='0001' ".
				"   AND sno_salida.codnom='".$ls_codnom."' ".
				"   AND sno_salida.codperi='".$ls_codperi."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND substr(sno_concepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_concepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_concepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_concepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_concepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codconc, sno_concepto.codpro,sno_concepto.estcla, spg_cuentas.spg_cuenta,sno_concepto.codprov, sno_concepto.cedben ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codprouniadm as programatica, spg_cuentas.spg_cuenta as cueprepatcon, sum(sno_salida.valsal) as total, sno_unidadadmin.estcla, ".
				"		sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='0001' ".
				"   AND sno_salida.codnom='".$ls_codnom."' ".
				"   AND sno_salida.codperi='".$ls_codperi."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND substr(sno_unidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_unidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_unidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_unidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_unidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codconc, sno_unidadadmin.codprouniadm,  sno_unidadadmin.estcla,spg_cuentas.spg_cuenta, sno_concepto.codprov, sno_concepto.cedben ";
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_contabilizar_aportes_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message));
			$lb_valido=false;
		}
		else
		{
			
				
				while((!$rs_data->EOF)&&($lb_valido))
				{
					$ls_programatica=$rs_data->fields["programatica"];
					$ls_estcla=$rs_data->fields["estcla"];
					$ls_cueprepatcon=$rs_data->fields["cueprepatcon"];
					$li_total=abs(round($rs_data->fields["total"],2));
					$ls_codpro=$rs_data->fields["codprov"];
					$ls_cedben=$rs_data->fields["cedben"];
					$ls_codconc=$rs_data->fields["codconc"];
					if($ls_codpro=="----------")
					{
						$ls_tipodestino="B";
					}
					if($ls_cedben=="----------")
					{
						$ls_tipodestino="P";
					}
					$ls_codcomapo=substr($ls_codconc,2,8).$ls_codperi.$ls_codnom;
					
					
					$lb_existe=$this->uf_select_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_programatica,$ls_estcla,
													$ls_cueprepatcon,$as_operacionaporte,$ls_codconc);
					if (!$lb_existe)
					{
					
						$lb_valido=$this->uf_insert_contabilizacion_spg($as_codcom,$as_operacionaporte,$ls_codpro,$ls_cedben,
																	$ls_tipodestino,$as_descripcion,$ls_programatica,$ls_estcla,
																	$ls_cueprepatcon,$li_total,$ls_tipnom,$ls_codconc,
																	$ai_genrecapo,
																	$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,$ls_codcomapo);
					}
					else
					{
						/*$lb_valido=$this->uf_update_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_programatica,$ls_estcla,
																	$ls_cueprepatcon,$as_operacionaporte,$ls_codconc,$li_total);*/						
					}
					$rs_data->MoveNext();
				}
			
			$rs_data->Close();
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_aportes_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_aportes_scg($as_codcom,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,$ai_genrecapo,$ai_tipdocapo,
										 $ai_gennotdeb,$ai_genvou,$as_operacionaporte)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_aportes_scg  as_tipodestino  ai_genrecapo
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecapo  //  Generar recepción de documento
		//	    		   ai_tipdocapo  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_operacionaporte  //  Operación con que se va a contabilizar los aportes
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 31/05/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		
		$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
				
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="A";
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
		// integran directamente con presupuesto estas van por el debe de contabilidad
		
			$ls_sql=" SELECT  cuenta, operacion, total,codprov, cedben, codconc ".
					"   FROM  cierre_contableaportes_contable                   ".
					"  WHERE codemp='0001'                      ".                
					"   AND codnom='".$ls_codnom."'                       ". 
					"   AND codperi='".$ls_codperi."'                   ".
					" UNION                                                     ".  
					" SELECT  cuenta, operacion, total,codprov, cedben, codconc ".
					"   FROM cierre_contableaportes_contable_int                ".
					"  WHERE codemp='0001'                      ".                
					"   AND codnom='".$ls_codnom."'                       ". 
					"   AND codperi='".$ls_codperi."'                   ";	
		if(($as_operacionaporte=="OC")&&($ai_genrecapo=="1"))
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_salida.valsal)) as total, ".
					"		sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, rpc_proveedor ".
					" WHERE sno_salida.codemp='0001' ".
					"   AND sno_salida.codnom='".$ls_codnom."' ".
					"   AND sno_salida.codperi='".$ls_codperi."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_concepto.codprov <> '----------' ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"	AND sno_concepto.codemp = rpc_proveedor.codemp ".
					"	AND sno_concepto.codprov = rpc_proveedor.cod_pro ".
					"   AND scg_cuentas.codemp = rpc_proveedor.codemp ".
					"   AND scg_cuentas.sc_cuenta = ".$ls_scctaprov." ".
					" GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben  ";
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_salida.valsal)) as total, ".
					"		sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, rpc_beneficiario ".
					" WHERE sno_salida.codemp='0001' ".
					"   AND sno_salida.codnom='".$ls_codnom."' ".
					"   AND sno_salida.codperi='".$ls_codperi."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_concepto.cedben <> '----------' ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"	AND sno_concepto.codemp = rpc_beneficiario.codemp ".
					"	AND sno_concepto.cedben = rpc_beneficiario.ced_bene ".
					"   AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
					"   AND scg_cuentas.sc_cuenta = ".$ls_scctaben." ".
					" GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben  ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_salida.valsal)) as total, ".
					"		sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas ".
					" WHERE sno_salida.codemp='0001' ".
					"   AND sno_salida.codnom='".$ls_codnom."' ".
					"   AND sno_salida.codperi='".$ls_codperi."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND scg_cuentas.codemp = sno_concepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_concepto.cueconpatcon ".
					" GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben "; 
		}
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_contabilizar_aportes_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message));
			$lb_valido=false;
		}
		else
		{				
				while((!$rs_data->EOF)&&($lb_valido))
				{
					$ls_cuenta=$rs_data->fields["cuenta"];
					$ls_operacion=$rs_data->fields["operacion"];
					$li_total=abs(round($rs_data->fields["total"],2));
					$ls_codpro=$rs_data->fields["codprov"];
					$ls_cedben=$rs_data->fields["cedben"];
					$ls_codconc=$rs_data->fields["codconc"];
					$ls_tipodestino="";
					if($ls_codpro=="----------")
					{
						$ls_tipodestino="B";
					}
					if($ls_cedben=="----------")
					{
						$ls_tipodestino="P";
					}
					$ls_codcomapo=substr($ls_codconc,2,8).$ls_codperi.$ls_codnom;
					
					$lb_existe=$this->uf_select_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,$ls_codconc);
					if (!$lb_existe)
					{
						$lb_valido=$this->uf_insert_contabilizacion_scg($as_codcom,$ls_codpro,$ls_cedben,$ls_tipodestino,$as_descripcion,
																	$ls_cuenta,$ls_operacion,$li_total,$ls_tipnom,$ls_codconc,
																	$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,$ls_codcomapo);
					}
					else
					{
						/*$lb_valido=$this->uf_update_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,
																		$ls_codconc,$li_total);	*/					
					}
					
					$rs_data->MoveNext();
				}
				
			
			$rs_data->Close();
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_aportes_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_ingresos_spi($as_codcom,$as_descripcion)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_ingresos_spi 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_descripcion  //  descripción del comprobante
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 25/03/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="I"; // tipo de contabilización
		
		if ($ls_estpreing==0)
		{
			$ls_sql="SELECT spi_cuentas.spi_cuenta AS cuenta, sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas ".
					" WHERE sno_salida.codemp='0001' ".
					"   AND sno_salida.codnom='".$ls_codnom."' ".
					"   AND sno_salida.codperi='".$ls_codperi."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
					"   AND sno_concepto.intingcon = '1'".
					"   AND spi_cuentas.status = 'C' ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND spi_cuentas.codemp = sno_concepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta ".
					" GROUP BY spi_cuentas.spi_cuenta ";
		 }
		 else
		 {
		 	$ls_sql=" SELECT spi_cuentas.spi_cuenta AS cuenta, ".
					"		 MAX(spi_cuentas.denominacion) AS denominacion, ".
					"		 sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total, ".         
					"		 spi_cuentas_estructuras.codestpro1, ".
					"		 spi_cuentas_estructuras.codestpro2, ". 
					"		 spi_cuentas_estructuras.codestpro3, ".
					"		 spi_cuentas_estructuras.codestpro4, ".
					"		 spi_cuentas_estructuras.codestpro5,  ".
					"		 spi_cuentas_estructuras.estcla  ".
				 	"  FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas, ".
					"       spi_cuentas_estructuras, sno_unidadadmin     ".
				    " WHERE sno_salida.codemp='0001'     ".
					"   AND sno_salida.codnom='".$ls_codnom."'     ".
					"   AND sno_salida.codperi='".$ls_codperi."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'D'  ".
					"        OR sno_salida.tipsal = 'V2' ". 
					"        OR sno_salida.tipsal = 'W2' ".
					"        OR sno_salida.tipsal = 'P1' ".
					"        OR sno_salida.tipsal = 'V3' ".
					"        OR sno_salida.tipsal = 'W3') ". 
					"	  AND sno_concepto.intingcon = '1' ".
					"	  AND spi_cuentas.status = 'C' ".
					"	  AND sno_personalnomina.codemp = sno_salida.codemp ".
					"	  AND sno_personalnomina.codnom = sno_salida.codnom ".
					"	  AND sno_personalnomina.codper = sno_salida.codper ".
					"	  AND sno_salida.codemp = sno_concepto.codemp ".
					"	  AND sno_salida.codnom = sno_concepto.codnom ".
					"	  AND sno_salida.codconc = sno_concepto.codconc ".
					"	  AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
					"	  AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
					"	  AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
					"	  AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
					"	  AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
					"	  AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm  ".
					"	  AND spi_cuentas.codemp = sno_concepto.codemp ".
					"	  AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta  ".
					"	  AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"	  AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
					"	  AND substr(sno_concepto.codpro,1,25)  = spi_cuentas_estructuras.codestpro1 ".
					"	  AND substr(sno_concepto.codpro,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"	  AND substr(sno_concepto.codpro,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"	  AND substr(sno_concepto.codpro,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"	  AND substr(sno_concepto.codpro,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"	  AND sno_concepto.estcla = spi_cuentas_estructuras.estcla ".
					"  GROUP BY spi_cuentas.spi_cuenta,spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2, ".
					"           spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4, ".
					"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla  ";
				$ls_sql=$ls_sql."  UNION   ".
				    "   SELECT spi_cuentas.spi_cuenta AS cuenta, ".
					"	       MAX(spi_cuentas.denominacion) AS denominacion, ".
					"  	       sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total,         ".
					"		   spi_cuentas_estructuras.codestpro1, ".
					"  		   spi_cuentas_estructuras.codestpro2, ".
					" 		   spi_cuentas_estructuras.codestpro3, ".
					"		   spi_cuentas_estructuras.codestpro4, ".
					"		   spi_cuentas_estructuras.codestpro5,  ".
					"		   spi_cuentas_estructuras.estcla  ".
					"    FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas, ".
					"         spi_cuentas_estructuras, sno_unidadadmin ".
					"   WHERE sno_salida.codemp='0001' ".
					"     AND sno_salida.codnom='".$ls_codnom."' ".
					"     AND sno_salida.codperi='".$ls_codperi."' ". 
					"	  AND sno_salida.valsal <> 0 ".
					"	  AND (sno_salida.tipsal = 'D' ".
					"         OR sno_salida.tipsal = 'V2' ". 
					"         OR sno_salida.tipsal = 'W2' ".
					"         OR sno_salida.tipsal = 'P1' ".
					"         OR sno_salida.tipsal = 'V3' ".
					"         OR sno_salida.tipsal = 'W3') ".
					"	  AND sno_concepto.intingcon = '1' ".
					"	  AND spi_cuentas.status = 'C' ".
					"	  AND sno_personalnomina.codemp = sno_salida.codemp ".
					"	  AND sno_personalnomina.codnom = sno_salida.codnom ".
					"	  AND sno_personalnomina.codper = sno_salida.codper ".
					"	  AND sno_salida.codemp = sno_concepto.codemp ".
					"	  AND sno_salida.codnom = sno_concepto.codnom ".
					"	  AND sno_salida.codconc = sno_concepto.codconc ".
					"	  AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
					"	  AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
					"	  AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
					"	  AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
					"	  AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
					"	  AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm  ".
					"	  AND spi_cuentas.codemp = sno_concepto.codemp ".
					"	  AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta  ".
					"	  AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"	  AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
					"	  AND substr(sno_unidadadmin.codprouniadm,1,25) =  spi_cuentas_estructuras.codestpro1 ".
					"	  AND substr(sno_unidadadmin.codprouniadm,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"	  AND substr(sno_unidadadmin.codprouniadm,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"	  AND substr(sno_unidadadmin.codprouniadm,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"	  AND substr(sno_unidadadmin.codprouniadm,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"	  AND sno_unidadadmin.estcla = spi_cuentas_estructuras.estcla     ".
					" GROUP BY spi_cuentas.spi_cuenta,spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2, ".
					"		   spi_cuentas_estructuras.codestpro3,  spi_cuentas_estructuras.codestpro4, ".
					"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla   	"; 
		 
		 }
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_contabilizar_ingresos_spi ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message));
			$lb_valido=false;
		}
		else
		{
				while((!$rs_data->EOF)&&($lb_valido))
				{
					$ls_cuenta=$rs_data->fields["cuenta"];
					$li_total=round(abs($rs_data->fields["total"]),2);
					$ls_codconc="0000000001";
					$ls_codcomapo=substr($ls_codconc,2,8).$ls_codperi.$ls_codnom;
					$as_operacionnomina="DC";
					$as_codpro="----------";
					$as_codben="----------";
					$as_tipodestino="-";
					$ai_genrecdoc="0";
					$ai_tipdocnom="";
					$ai_gennotdeb="0";
					$ai_genvou="0";
					if ($ls_estpreing==1)
		            {
						$ls_codestpro1=$rs_data->fields["codestpro1"];
						$ls_codestpro2=$rs_data->fields["codestpro2"];
						$ls_codestpro3=$rs_data->fields["codestpro3"];
						$ls_codestpro4=$rs_data->fields["codestpro4"];
						$ls_codestpro5=$rs_data->fields["codestpro5"];
						$ls_estcla=$rs_data->fields["estcla"];
					}
					else
					{
						$ls_codestpro1='-------------------------';
						$ls_codestpro2='-------------------------';
						$ls_codestpro3='-------------------------';
						$ls_codestpro4='-------------------------';
						$ls_codestpro5='-------------------------';
						$ls_estcla='-';					
					}
				
					$lb_existe=$this->uf_select_contabilizacion_spi($as_codcom,$ls_tipnom,$ls_cuenta,$as_operacionnomina,$ls_codconc,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																	$ls_codestpro5,$ls_estcla);
					if (!$lb_existe)
					{
						$lb_valido=$this->uf_insert_contabilizacion_spi($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,$ls_cuenta,$li_total,$ls_tipnom,$ls_codconc,$ai_genrecdoc,
																	$ai_tipdocnom,$ai_gennotdeb,$ai_genvou,$ls_codcomapo,
																	$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																	$ls_codestpro5,$ls_estcla);
					}
					else
					{
						//$lb_valido=$this->uf_update_contabilizacion_spi($as_codcom,$ls_tipnom,$ls_cuenta,$as_operacionnomina,$ls_codconc,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$li_total);						
					}
					
					
					$rs_data->MoveNext();
				}
			
			$rs_data->Close();
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_ingresos_spi
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_ingresos_scg($as_codcom,$as_descripcion)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_ingresos_scg 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_descripcion  //  descripción del comprobante
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 25/03/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="I"; // tipo de contabilización
		if ($ls_estpreing==0)
		{
			// Buscamos todas aquellas cuentas contables que estan ligadas a las de ingreso de los conceptos que se 
			// integran directamente con presupuesto estas van por el haber de contabilidad
			$ls_sql="SELECT spi_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, 'H' as operacion, ".
					"		sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas, scg_cuentas ".
					" WHERE sno_salida.codemp='0001' ".
					"   AND sno_salida.codnom='".$ls_codnom."' ".
					"   AND sno_salida.codperi='".$ls_codperi."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
					"   AND sno_concepto.intingcon = '1'".
					"   AND spi_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND spi_cuentas.codemp = sno_concepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta ".
					"   AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
					"   GROUP BY spi_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, ".
					"		sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas ".
					" WHERE sno_salida.codemp='0001' ".
					"   AND sno_salida.codnom='".$ls_codnom."' ".
					"   AND sno_salida.codperi='".$ls_codperi."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
					"   AND sno_concepto.intingcon = '1'".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND scg_cuentas.codemp = sno_concepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_concepto.cueconcon  ".
					"   GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			$ls_sql="  SELECT spi_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, ".
					"		  'H' as operacion, sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total ".
					"	  FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas, scg_cuentas, ".
					"          spi_cuentas_estructuras, sno_unidadadmin  ".
					" WHERE sno_salida.codemp='0001' ".
					"   AND sno_salida.codnom='".$ls_codnom."' ".
					"   AND sno_salida.codperi='".$ls_codperi."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'D' ".
					"       OR sno_salida.tipsal = 'V2' ".
					"       OR sno_salida.tipsal = 'W2' ".
					"       OR sno_salida.tipsal = 'P1' ".
					"       OR sno_salida.tipsal = 'V3' ".
					"       OR sno_salida.tipsal = 'W3') ".
					"   AND sno_concepto.intingcon = '1' ".
					"   AND spi_cuentas.status = 'C' ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
					"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
					"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
					"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
					"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
					"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm  ".
					"   AND spi_cuentas.codemp = sno_concepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta ".
					"   AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta ".
					"   AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"   AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
					"   AND substr(sno_concepto.codpro,1,25)  = spi_cuentas_estructuras.codestpro1 ".
					"   AND substr(sno_concepto.codpro,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"   AND substr(sno_concepto.codpro,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"   AND substr(sno_concepto.codpro,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"   AND substr(sno_concepto.codpro,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"   AND sno_concepto.estcla = spi_cuentas_estructuras.estcla ".
					"  GROUP BY spi_cuentas.sc_cuenta ";
		   $ls_sql=$ls_sql."		  UNION    ".
					" SELECT spi_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, ".
					" 	    'H' as operacion, sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total ".
					"   FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas, ".
					"        scg_cuentas, spi_cuentas_estructuras, sno_unidadadmin  ".
				    "  WHERE sno_salida.codemp='0001 ".
					"    AND sno_salida.codnom='".$ls_codnom."' ".
					"    AND sno_salida.codperi='".$ls_codperi."' ".

					"    AND sno_salida.valsal <> 0 ".
					"    AND (sno_salida.tipsal = 'D' ". 
					"         OR sno_salida.tipsal = 'V2' ". 
					"         OR sno_salida.tipsal = 'W2' ".
					"         OR sno_salida.tipsal = 'P1' ".
					"         OR sno_salida.tipsal = 'V3' ".
					"         OR sno_salida.tipsal = 'W3') ".
					"    AND sno_concepto.intingcon = '1' ".
					"	 AND spi_cuentas.status = 'C' ".
					"	 AND sno_personalnomina.codemp = sno_salida.codemp ".
					"	 AND sno_personalnomina.codnom = sno_salida.codnom ".
					"	 AND sno_personalnomina.codper = sno_salida.codper ".
					"	 AND sno_salida.codemp = sno_concepto.codemp ".
					"	 AND sno_salida.codnom = sno_concepto.codnom ".
					"	 AND sno_salida.codconc = sno_concepto.codconc ".
					"	 AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
					"    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
					"	 AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
					"	 AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
					"	 AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
					"	 AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm  ".
					"	 AND spi_cuentas.codemp = sno_concepto.codemp ".
					"	 AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta ".
					"	 AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta ".
					"	 AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"	 AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
					"	 AND substr(sno_unidadadmin.codprouniadm,1,25) =  spi_cuentas_estructuras.codestpro1 ".
					"	 AND substr(sno_unidadadmin.codprouniadm,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"	 AND substr(sno_unidadadmin.codprouniadm,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"	 AND substr(sno_unidadadmin.codprouniadm,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"	 AND substr(sno_unidadadmin.codprouniadm,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"	 AND sno_unidadadmin.estcla = spi_cuentas_estructuras.estcla ".
					"  GROUP BY spi_cuentas.sc_cuenta ";
			 $ls_sql=$ls_sql."		  UNION    ".
					"  SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta,   ".
					"		  'D' as operacion, sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total ".
					"    FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, ".
					"         spi_cuentas, spi_cuentas_estructuras, sno_unidadadmin  ".
				    "   WHERE sno_salida.codemp='0001' ".
					"     AND sno_salida.codnom='".$ls_codnom."' ".
					"     AND sno_salida.codperi='".$ls_codperi."' ".
					" 	  AND sno_salida.valsal <> 0 ".
					"	  AND (sno_salida.tipsal = 'D' ".
					"          OR sno_salida.tipsal = 'V2' ".
					"          OR sno_salida.tipsal = 'W2' ".
					"          OR sno_salida.tipsal = 'P1' ".
					"          OR sno_salida.tipsal = 'V3' ".
					"          OR sno_salida.tipsal = 'W3') ". 
					"    AND sno_concepto.intingcon = '1' ".
					"	 AND scg_cuentas.status = 'C' ".
					"	 AND sno_personalnomina.codemp = sno_salida.codemp ".
					"	 AND sno_personalnomina.codnom = sno_salida.codnom ".
					"	 AND sno_personalnomina.codper = sno_salida.codper ".
					"	 AND sno_personalnomina.codemp = sno_unidadadmin.codemp ". 
					"	 AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
					"	 AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
					"	 AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
					"	 AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
					"	 AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
					"	 AND sno_salida.codemp = sno_concepto.codemp ".
					"	 AND sno_salida.codnom = sno_concepto.codnom ".
					"	 AND sno_salida.codconc = sno_concepto.codconc ".
					"	 AND scg_cuentas.codemp = sno_concepto.codemp ".
					"	 AND scg_cuentas.sc_cuenta = sno_concepto.cueconcon ".
					"	 AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta ". 
					"	 AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".

					"	 AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
					"	 AND substr(sno_concepto.codpro,1,25)  = spi_cuentas_estructuras.codestpro1 ".
					"	 AND substr(sno_concepto.codpro,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"	 AND substr(sno_concepto.codpro,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"	 AND substr(sno_concepto.codpro,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"	 AND substr(sno_concepto.codpro,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"	 AND sno_concepto.estcla = spi_cuentas_estructuras.estcla ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			 $ls_sql=$ls_sql."		  UNION    ".
				    "  SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta,  ".
					"		 'D' as operacion, sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total ".
					"   FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, ".
					"        spi_cuentas, spi_cuentas_estructuras, sno_unidadadmin  ".
				    " WHERE sno_salida.codemp='0001' ".
					"   AND sno_salida.codnom='".$ls_codnom."' ".
					"   AND sno_salida.codperi='".$ls_codperi."' ". 
					"   AND sno_salida.valsal <> 0 ".
					"	AND (sno_salida.tipsal = 'D' ".
					"        OR sno_salida.tipsal = 'V2' ".
					"        OR sno_salida.tipsal = 'W2' ".
					"        OR sno_salida.tipsal = 'P1' ".
					"        OR sno_salida.tipsal = 'V3' ".
					"        OR sno_salida.tipsal = 'W3') ".
					"  AND sno_concepto.intingcon = '1' ".
					"  AND scg_cuentas.status = 'C' ".
					"  AND sno_personalnomina.codemp = sno_salida.codemp ".
					"  AND sno_personalnomina.codnom = sno_salida.codnom ".
					"  AND sno_personalnomina.codper = sno_salida.codper ".
					"  AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
					"  AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
					"  AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
					"  AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
					"  AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
					"  AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
					"  AND sno_salida.codemp = sno_concepto.codemp ".
					"  AND sno_salida.codnom = sno_concepto.codnom ".
					"  AND sno_salida.codconc = sno_concepto.codconc ".
					"  AND scg_cuentas.codemp = sno_concepto.codemp ".
					"  AND scg_cuentas.sc_cuenta = sno_concepto.cueconcon ".
					"  AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"  AND spi_cuentas.codemp = sno_concepto.codemp ".
					"  AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta  ".
					"  AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"  AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
					"  AND substr(sno_unidadadmin.codprouniadm,1,25) =  spi_cuentas_estructuras.codestpro1 ".
					"  AND substr(sno_unidadadmin.codprouniadm,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"  AND substr(sno_unidadadmin.codprouniadm,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"  AND substr(sno_unidadadmin.codprouniadm,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"  AND substr(sno_unidadadmin.codprouniadm,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"  AND sno_unidadadmin.estcla = spi_cuentas_estructuras.estcla ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		
		}
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_contabilizar_ingresos_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message));
			$lb_valido=false;
		}
		else
		{
				while((!$rs_data->EOF)&&($lb_valido))
				{
					$ls_cuenta=$rs_data->fields["cuenta"];
					$ls_operacion=$rs_data->fields["operacion"];
					$li_total=abs(round($rs_data->fields["total"],2));
					$ls_codconc="0000000001";
					$ls_codcomapo=substr($ls_codconc,2,8).$ls_codperi.$ls_codnom;
					$ls_codpro="----------";
					$ls_cedben="----------";
					$ls_tipodestino="-";
					$ai_genrecdoc="0";
					$ai_tipdocnom="";
					$ai_gennotdeb="0";
					$ai_genvou="0";
					
					
					$lb_existe=$this->uf_select_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,$ls_codconc);
					if (!$lb_existe)
					{
						$lb_valido=$this->uf_insert_contabilizacion_scg($as_codcom,$ls_codpro,$ls_cedben,$ls_tipodestino,$as_descripcion,
																	$ls_cuenta,$ls_operacion,$li_total,$ls_tipnom,$ls_codconc,
																	$ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,$ai_genvou,$ls_codcomapo);
					}
					else
					{
						/*$lb_valido=$this->uf_update_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,
																		$ls_codconc,$li_total);*/						
					}
					$rs_data->MoveNext();
				}
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_ingresos_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_contabilizacion($as_codcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_contabilizacion 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de verificar que lo mismo que esta por el debe tambien este por el haber en contabilidad
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 29/06/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_sql="SELECT debhab, sum(monto) as total ".
				"  FROM sno_dt_scg ".
				" WHERE codcom = '".$as_codcom."' ".
				" GROUP BY debhab ";
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_verificar_contabilizacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message));
			$lb_valido=false;
		}
		else
		{
			$li_debe=0;
			$li_haber=0;
			while($row=$this->io_sql_origen->fetch_row($rs_data))
			{
				$li_operacion=$row["debhab"];
				if($li_operacion=="D")
				{
					$li_debe=number_format($row["total"],2,".","");
				}
				else
				{
					$li_haber=number_format($row["total"],2,".","");
				}
			}
			$this->io_sql_origen->free_result($rs_data);
			if($li_debe!=$li_haber)
			{
				$lb_valido=false;
				if(substr($as_codcom,14,1)=="A")
				{
					$ls_texto=" Aportes";
				}
				else
				{
					$ls_texto=" Nómina";
				}
				$this->io_mensajes->message("Los Monto en la Contabilización de ".$ls_texto." no cuadran. Debe=".$this->io_fun_nomina->uf_formatonumerico($li_debe)." Haber ".$this->io_fun_nomina->uf_formatonumerico($li_haber).". Verifique la información ");
			}
		}		
		return  $lb_valido;    
	}// end function uf_verificar_contabilizacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_conceptos_spg_normales()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_conceptos_spg_normales 
		//	    Arguments:
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos que son normales
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 11/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codpro as programatica, spg_cuentas.spg_cuenta as cueprecon, sum(sno_salida.valsal) AS total,sno_concepto.estcla, ".
				" 		SUBSTR(sno_concepto.codpro,1,25) AS proy1, ".
       			"		SUBSTR(sno_concepto.codpro,26,25) AS proy2, ".
       			"		SUBSTR(sno_concepto.codpro,51,25) AS proy3, ".
       			"		SUBSTR(sno_concepto.codpro,76,25) AS proy4, ".
       			"		SUBSTR(sno_concepto.codpro,101,25) AS proy5 ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND substr(sno_concepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_concepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_concepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_concepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_concepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codpro, spg_cuentas.spg_cuenta, sno_concepto.estcla ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codprouniadm as programatica, spg_cuentas.spg_cuenta as cueprecon, sum(sno_salida.valsal) as total,sno_unidadadmin.estcla, ".
				" 		SUBSTR(sno_unidadadmin.codprouniadm,1,25) AS proy1, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,26,25) AS proy2, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,51,25) AS proy3, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,76,25) AS proy4, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,101,25) AS proy5 ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND substr(sno_unidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_unidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_unidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_unidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_unidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".

				" GROUP BY sno_unidadadmin.codprouniadm , spg_cuentas.spg_cuenta, sno_unidadadmin.estcla ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codpro as programatica, spg_cuentas.spg_cuenta as cueprecon, sum(sno_salida.valsal) as total,sno_concepto.estcla, ".
				" 		SUBSTR(sno_concepto.codpro,1,25) AS proy1, ".
       			"		SUBSTR(sno_concepto.codpro,26,25) AS proy2, ".
       			"		SUBSTR(sno_concepto.codpro,51,25) AS proy3, ".
       			"		SUBSTR(sno_concepto.codpro,76,25) AS proy4, ".
       			"		SUBSTR(sno_concepto.codpro,101,25) AS proy5 ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND substr(sno_concepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_concepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_concepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_concepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_concepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codpro, spg_cuentas.spg_cuenta, sno_concepto.estcla  ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codprouniadm as programatica, spg_cuentas.spg_cuenta as cueprecon, sum(sno_salida.valsal) as total,sno_unidadadmin.estcla, ".
				" 		SUBSTR(sno_unidadadmin.codprouniadm,1,25) AS proy1, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,26,25) AS proy2, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,51,25) AS proy3, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,76,25) AS proy4, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,101,25) AS proy5 ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND substr(sno_unidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_unidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_unidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_unidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_unidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codprouniadm,spg_cuentas.spg_cuenta, sno_unidadadmin.estcla ".
				" ORDER BY programatica, cueprecon"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_load_conceptos_spg_normales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}	
		return  $lb_valido;    
	}// end function uf_load_conceptos_spg_normales
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_conceptos_scg_normales($as_operacionnomina,$as_cuentapasivo,$ai_genrecdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_conceptos_scg_normales 
		//	    Arguments: as_operacionnomina // Operación de contabilización 
		//				   as_cuentapasivo // Cuenta de Pasivo hacia donde se contabiliza la nómina
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos normales
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 11/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		
		$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		
				$ls_sql="  SELECT cuenta, operacion, total                              ".
				        "    FROM contableconceptos_contable_proyecto                   ".
						"	WHERE  codemp='".$this->ls_codemp."'                        ". 
						"	   AND codnom='".$this->ls_codnom."'                        ".
						"	   AND codperi='".$this->ls_peractnom."'                    ".
						"	 UNION                                                      ".
						"	SELECT cuenta, operacion, total                             ".
						"     FROM contableconceptos_contable_proyecto_intercom         ".
						"	WHERE  codemp='".$this->ls_codemp."'                        ". 
						"	   AND codnom='".$this->ls_codnom."'                        ".
						"	   AND codperi='".$this->ls_peractnom."'                    ";
		if($as_operacionnomina=="OC") // Si el modo de contabilizar la nómina es Compromete y Causa tomamos la cuenta pasivo de la nómina.
		{
			if($ai_genrecdoc=="0") // No se genera Recepción de Documentos
			{
				// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena="CONVERT('".$as_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "POSTGRES":
						$ls_cadena="CAST('".$as_cuentapasivo."' AS char(25)) as cuenta";
						break;					
					case "INFORMIX":
						$ls_cadena="CAST('".$as_cuentapasivo."' AS char(25)) as cuenta";
						break;					
				}
				$ls_sql=$ls_sql." UNION ".
						"SELECT ".$ls_cadena.", CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
						"  FROM sno_personalnomina, sno_salida, sno_banco, scg_cuentas, sno_concepto ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1  OR sno_personalnomina.pagtaqper = 1) ".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C' ".
						"   AND scg_cuentas.sc_cuenta = '".$as_cuentapasivo."' ".
						"   AND sno_salida.codemp = sno_concepto.codemp ".
						"   AND sno_salida.codnom = sno_concepto.codnom ".
						"   AND sno_salida.codconc = sno_concepto.codconc ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_salida.codemp = sno_banco.codemp ".
						"   AND sno_salida.codnom = sno_banco.codnom ".
						"   AND sno_salida.codperi = sno_banco.codperi ".
						"   AND sno_personalnomina.codemp = sno_banco.codemp ".
						"   AND sno_personalnomina.codban = sno_banco.codban ".
						"   AND scg_cuentas.codemp = sno_banco.codemp ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			else // Se genera Recepción de documentos
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_nomina, rpc_proveedor ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_nomina.descomnom = 'P'".
						"   AND sno_nomina.codemp = sno_salida.codemp ".
						"   AND sno_nomina.codnom = sno_salida.codnom ".
						"   AND sno_nomina.peractnom = sno_salida.codperi ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_nomina.codemp = rpc_proveedor.codemp ".
						"   AND sno_nomina.codpronom = rpc_proveedor.cod_pro ".
						"   AND rpc_proveedor.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_nomina, rpc_beneficiario ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_nomina.descomnom = 'B'".
						"   AND sno_nomina.codemp = sno_salida.codemp ".
						"   AND sno_nomina.codnom = sno_salida.codnom ".
						"   AND sno_nomina.peractnom = sno_salida.codperi ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_nomina.codemp = rpc_beneficiario.codemp ".
						"   AND sno_nomina.codbennom = rpc_beneficiario.ced_bene ".
						"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
					"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_concepto ".
					" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
					"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
					"   AND sno_salida.valsal <> 0".
					"   AND sno_personalnomina.pagbanper = 0 ".
					"   AND sno_personalnomina.pagtaqper = 0 ".
					"   AND sno_personalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND scg_cuentas.codemp = sno_personalnomina.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_personalnomina.cueaboper ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_banco, scg_cuentas, sno_concepto ".
					" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
					"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
					"   AND sno_salida.valsal <> 0".
					"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1) ".
					"   AND sno_personalnomina.pagefeper = 0 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_banco.codemp ".
					"   AND sno_salida.codnom = sno_banco.codnom ".
					"   AND sno_salida.codperi = sno_banco.codperi ".
					"   AND sno_personalnomina.codemp = sno_banco.codemp ".
					"   AND sno_personalnomina.codban = sno_banco.codban ".
					"   AND scg_cuentas.codemp = sno_banco.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_banco.codcuecon ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -sum(sno_salida.valsal) as total ".
					"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_concepto ".
					" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
					"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
					"   AND sno_salida.valsal <> 0".
					"   AND sno_personalnomina.pagbanper = 0 ".
					"   AND sno_personalnomina.pagtaqper = 0 ".
					"   AND sno_personalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND scg_cuentas.codemp = sno_personalnomina.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_personalnomina.cueaboper ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_load_conceptos_scg_normales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_conceptos_scg_normales
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_aportes_spg_normales()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_aportes_spg_normales 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos de aportes normales
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 13/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codpro as programatica, sno_concepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(sno_salida.valsal) as total, ".
				"       SUBSTR(sno_concepto.codpro,1,25) AS proy1, ".
       			"		SUBSTR(sno_concepto.codpro,26,25) AS proy2, ".
				"		SUBSTR(sno_concepto.codpro,51,25) AS proy3, ".
				"		SUBSTR(sno_concepto.codpro,76,25) AS proy4, ".
				"		SUBSTR(sno_concepto.codpro,101,25) AS proy5 ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND substr(sno_concepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_concepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_concepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_concepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_concepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codpro,sno_concepto.estcla, spg_cuentas.spg_cuenta  ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codprouniadm as programatica, sno_unidadadmin.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(sno_salida.valsal) as total, ".
				"		SUBSTR(sno_unidadadmin.codprouniadm,1,25) AS proy1, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,26,25) AS proy2, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,51,25) AS proy3, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,76,25) AS proy4, ".
       			"		SUBSTR(sno_unidadadmin.codprouniadm,101,25) AS proy5 ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '0'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND substr(sno_unidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_unidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_unidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_unidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_unidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codprouniadm,sno_unidadadmin.estcla, spg_cuentas.spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_load_aportes_spg_normales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_aportes_spg_normales
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_aportes_scg_normales($as_operacionaporte)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_aportes_scg_normales 
		//	    Arguments: as_operacionaporte  //  Operación con que se va a contabilizar los aportes
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los aportes
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 13/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		
		$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
		// integran directamente con presupuesto estas van por el debe de contabilidad
		
			$ls_sql="  SELECT cuenta, operacion, total, codprov, cedben, codconc   ".
					"	 FROM  cierre_contableaportes_contable_proy                ".
					"	 WHERE codemp='".$this->ls_codemp."'                       ".
					"	   AND codnom='".$this->ls_codnom."'                       ".
					"	   AND codperi='".$this->ls_peractnom."'                   ". 
					"	UNION                                                      ".
					"	SELECT cuenta, operacion, total, codprov, cedben, codconc  ".
					"	  FROM  cierre_contableaportes_contable_proy_intcom        ".
					"	 WHERE codemp='".$this->ls_codemp."'                       ".
					"	   AND codnom='".$this->ls_codnom."'                       ".
					"	   AND codperi='".$this->ls_peractnom."'                   "; 
		if(($as_operacionaporte=="OC")&&($ai_genrecapo=="1"))
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_salida.valsal)) as total, ".
					"		sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, rpc_proveedor ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_concepto.codprov <> '----------' ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"	AND sno_concepto.codemp = rpc_proveedor.codemp ".
					"	AND sno_concepto.codprov = rpc_proveedor.cod_pro ".
					"   AND scg_cuentas.codemp = rpc_proveedor.codemp ".
					"   AND scg_cuentas.sc_cuenta = ".$ls_scctaprov." ".
					" GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben  ";
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_salida.valsal)) as total, ".
					"		sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, rpc_beneficiario ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_concepto.cedben <> '----------' ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"	AND sno_concepto.codemp = rpc_beneficiario.codemp ".
					"	AND sno_concepto.cedben = rpc_beneficiario.ced_bene ".
					"   AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
					"   AND scg_cuentas.sc_cuenta = ".$ls_scctaben." ".
					" GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben  ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_salida.valsal)) as total, ".
					"		sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND scg_cuentas.codemp = sno_concepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_concepto.cueconpatcon ".
					" GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_load_aportes_scg_normales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_aportes_scg_normales
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_contabilizacion_scg_int($as_codcom,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,$as_cuenta,
	                                            $as_operacion,$ai_monto,$as_tipnom,$as_codconc,$ai_genrecdoc,$ai_tipdoc,
												$ai_gennotdeb,$ai_genvou,$as_codcomapo, $as_codest1_G, $as_estcla_G)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_contabilizacion_scg_int
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_programatica  //  Programática
		//	    		   as_cueprecon  //  cuenta presupuestaria
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilización es aporte ó de conceptos
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   as_codconc  //  Código de concepto
		//	    		   ai_tipdoc  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_codcomapo  //  Código del comprobante de aporte
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el total des las cuentas presupuestarias
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 14/08/2008								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$as_codcom=substr($as_codcom,2,15);
		$concat="P-";
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
		$ls_sql="INSERT INTO sno_dt_scg_int (codemp,codnom,codperi,codcom,tipnom,sc_cuenta,debhab,codconc,cod_pro,ced_bene,tipo_destino,".
				"descripcion,monto,estatus,estrd,codtipdoc,estnumvou,estnotdeb,codcomapo,   ".
				"codestpro1, estcla) VALUES ('0001','".$ls_codnom."',".
				"'".$ls_codperi."','".$concat.$as_codcom."','".$as_tipnom."','".$as_cuenta."','".$as_operacion."','".$as_codconc."',".
				"'".$as_codpro."','".$as_codben."','".$as_tipodestino."','".$as_descripcion."',".$ai_monto.",".$li_estatus.",".
				"'".$ai_genrecdoc."','".$ai_tipdoc."','".$ai_genvou."','".$ai_gennotdeb."','".$as_codcomapo."','".$as_codest1_G."','".$as_estcla_G."')"; 
		$li_row=$this->io_sql_destino->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_insert_contabilizacion_scg_int ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_contabilizacion_scg_int
	//-------------------------------------------------------------------------------------------------------------------------------------
    function uf_concepto_reintegro_int()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_concepto_reintegro_int
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 05/09/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		
		$ls_sql=" SELECT scg_cuentas.sc_cuenta AS cuenta,'H' AS operacion, sum(sno_salida.valsal) AS total, 
						 spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint    
					FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,
					     spg_cuentas, scg_cuentas, spg_ep1
				   WHERE sno_salida.codemp='0001'                           
				  	    AND sno_salida.codnom='".$ls_codnom."'                          
				  	    AND sno_salida.codperi='".$ls_codperi."'   
				        AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' 
					      OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3') 
					AND sno_salida.valsal <> 0
					AND sno_concepto.intprocon = '1'
					AND sno_concepto.sigcon = 'E'
					AND spg_cuentas.status = 'C'
					AND spg_ep1.estint = 1
					AND substr(sno_concepto.codpro, 1, 25) = spg_ep1.codestpro1
					AND spg_ep1.estcla = sno_concepto.estcla	
					AND sno_personalnomina.codemp = sno_salida.codemp 
					AND sno_personalnomina.codnom = sno_salida.codnom 
					AND sno_personalnomina.codper = sno_salida.codper 
					AND sno_salida.codemp = sno_concepto.codemp 
					AND sno_salida.codnom = sno_concepto.codnom 
					AND sno_salida.codconc = sno_concepto.codconc 
					AND sno_personalnomina.codemp = sno_unidadadmin.codemp 
					AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm
					AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm 
					AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm 
					AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm 
					AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm 
					AND spg_cuentas.codemp = sno_concepto.codemp 
					AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon
					AND scg_cuentas.sc_cuenta = spg_cuentas.sc_cuenta         
					AND substr(sno_concepto.codpro, 1, 25) = spg_cuentas.codestpro1
					AND substr(sno_concepto.codpro, 26, 25) = spg_cuentas.codestpro2
					AND substr(sno_concepto.codpro, 51, 25) = spg_cuentas.codestpro3
					AND substr(sno_concepto.codpro, 76, 25) = spg_cuentas.codestpro4
					AND substr(sno_concepto.codpro, 101, 25) = spg_cuentas.codestpro5
					AND sno_concepto.estcla = spg_cuentas.estcla  					
				GROUP BY spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint, scg_cuentas.sc_cuenta";
				$ls_sql=$ls_sql."	UNION ".
				    " SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, sum(sno_salida.valsal) AS total,
					         spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint                              
					    FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,                     
					        spg_cuentas, scg_cuentas, spg_ep1                                                  
					  WHERE sno_salida.codemp='0001'                           
				  	    AND sno_salida.codnom='".$ls_codnom."'                          
				  	    AND sno_salida.codperi='".$ls_codperi."'  
					    AND(sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2'     
					       OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')
					    AND sno_salida.valsal <> 0              
					    AND sno_concepto.intprocon = '0'        
					    AND sno_concepto.sigcon = 'E'           
					    AND spg_cuentas.status = 'C'            
					    AND spg_ep1.estint = 1                  
					    AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_ep1.codestpro1 
					    AND spg_ep1.estcla = sno_unidadadmin.estcla				          
					    AND sno_personalnomina.codemp = sno_salida.codemp                    
					    AND sno_personalnomina.codnom = sno_salida.codnom                    
					    AND sno_personalnomina.codper = sno_salida.codper                    
					    AND sno_salida.codemp = sno_concepto.codemp                          
					    AND sno_salida.codnom = sno_concepto.codnom                          
					    AND sno_salida.codconc = sno_concepto.codconc                        
					    AND sno_personalnomina.codemp = sno_unidadadmin.codemp               
					    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm   
					    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm         
					    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm         
					    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm         
					    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm         
					    AND spg_cuentas.codemp = sno_concepto.codemp                         
					    AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon                  
					    AND scg_cuentas.sc_cuenta = spg_cuentas.sc_cuenta                    
					    AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_cuentas.codestpro1   
					    AND substr(sno_unidadadmin.codprouniadm, 26, 25) = spg_cuentas.codestpro2  
					    AND substr(sno_unidadadmin.codprouniadm, 51, 25) = spg_cuentas.codestpro3  
					    AND substr(sno_unidadadmin.codprouniadm, 76, 25) = spg_cuentas.codestpro4  
					    AND substr(sno_unidadadmin.codprouniadm, 101, 25) = spg_cuentas.codestpro5 
					    AND sno_unidadadmin.estcla = spg_cuentas.estcla                            
				    GROUP BY spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint, scg_cuentas.sc_cuenta";	
		$rs_datos=$this->io_sql_origen->select($ls_sql);
		if($rs_datos===false)
		{
			$this->io_mensajes->message("CLASE->Traspaso Concepto MÉTODO->uf_concepto_reintegro_int ERROR->"
			                            .$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message));
			return $rs_datos="";
		}
		else
		{
			return $rs_datos;
		}
	}// fin uf_concepto_reintegro_int
	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_conceptos_scg_int($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,
	                                           $as_descripcion,$as_cuentapasivo,$ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,
											   $ai_genvou)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_conceptos_scg_int 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_cuentapasivo  //  cuenta pasivo
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdocnom  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 14/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		$rs_datos=array();
		$ls_monto=0;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="N";
		    // se buscan todas las cuentas contables de intercampañias realcioandas con el presupuesto
			// estas van por el DEBE		    
			$rs_datos=$this->uf_concepto_reintegro_int();
			$ls_sql=" SELECT scg_cuentas.sc_cuenta AS cuenta, 'D' AS operacion, sum(sno_salida.valsal) AS total,
								 spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint
						FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,                     
							 spg_cuentas, scg_cuentas, spg_ep1                                                  
					   WHERE sno_salida.codemp='0001'                           
				  	     AND sno_salida.codnom='".$ls_codnom."'                          
				  	     AND sno_salida.codperi='".$ls_codperi."' 
						 AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') 
						 AND sno_salida.valsal <> 0       
						 AND sno_concepto.intprocon = '1' 
						 AND spg_cuentas.status = 'C'     
						 AND spg_ep1.estint = 1           
						 AND substr(sno_concepto.codpro, 1, 25) = spg_ep1.codestpro1 
						 AND spg_ep1.estcla = sno_concepto.estcla          
						 AND spg_ep1.sc_cuenta=spg_cuentas.scgctaint 					  
						 AND spg_cuentas.sc_cuenta= scg_cuentas.sc_cuenta   
						 AND sno_personalnomina.codemp = sno_salida.codemp 
						 AND sno_personalnomina.codnom = sno_salida.codnom 
						 AND sno_personalnomina.codper = sno_salida.codper 
						 AND sno_salida.codemp = sno_concepto.codemp       
						 AND sno_salida.codnom = sno_concepto.codnom       
						 AND sno_salida.codconc = sno_concepto.codconc     
						 AND sno_personalnomina.codemp = sno_unidadadmin.codemp  
						 AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm 
						 AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm       
						 AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm       
						 AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm       
						 AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm       
						 AND spg_cuentas.codemp = sno_concepto.codemp                       
						 AND substr(sno_concepto.codpro, 1, 25) = spg_cuentas.codestpro1    
						 AND substr(sno_concepto.codpro, 26, 25) = spg_cuentas.codestpro2   
						 AND substr(sno_concepto.codpro, 51, 25) = spg_cuentas.codestpro3   
						 AND substr(sno_concepto.codpro, 76, 25) = spg_cuentas.codestpro4   
						 AND substr(sno_concepto.codpro, 101, 25) = spg_cuentas.codestpro5     
						GROUP BY spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint, 
						         scg_cuentas.sc_cuenta
						UNION 						                    
                     SELECT scg_cuentas.sc_cuenta AS cuenta, 'D' AS operacion, sum(sno_salida.valsal) AS total,
							  spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint 
						 FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,   
						      spg_cuentas, scg_cuentas, spg_ep1                                
					    WHERE sno_salida.codemp='0001'                           
				  	      AND sno_salida.codnom='".$ls_codnom."'                          
				  	      AND sno_salida.codperi='".$ls_codperi."' 
						  AND (sno_salida.tipsal = 'A'OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') 
						  AND sno_salida.valsal <> 0         
						  AND sno_concepto.intprocon = '0'   
						  AND spg_cuentas.status = 'C'       
						  AND spg_ep1.estint = 1             
						  AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_ep1.codestpro1 
						  AND spg_ep1.estcla= sno_unidadadmin.estcla        
						  AND spg_ep1.sc_cuenta=spg_cuentas.scgctaint 					  
						  AND spg_cuentas.sc_cuenta= scg_cuentas.sc_cuenta   
						  AND sno_personalnomina.codemp = sno_salida.codemp  
						  AND sno_personalnomina.codnom = sno_salida.codnom 
						  AND sno_personalnomina.codper = sno_salida.codper 
						  AND sno_salida.codemp = sno_concepto.codemp       
						  AND sno_salida.codnom = sno_concepto.codnom       
						  AND sno_salida.codconc = sno_concepto.codconc      
						  AND sno_personalnomina.codemp = sno_unidadadmin.codemp 
						  AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  
						  AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm        
						  AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm        
						  AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm        
						  AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm        
						  AND spg_cuentas.codemp = sno_concepto.codemp                        
						  AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon                 
						  AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_cuentas.codestpro1   
						  AND substr(sno_unidadadmin.codprouniadm, 26, 25) = spg_cuentas.codestpro2  
						  AND substr(sno_unidadadmin.codprouniadm, 51, 25) = spg_cuentas.codestpro3  
						  AND substr(sno_unidadadmin.codprouniadm, 76, 25) = spg_cuentas.codestpro4  
						  AND substr(sno_unidadadmin.codprouniadm, 101, 25) = spg_cuentas.codestpro5  
					   GROUP BY spg_cuentas.codestpro1,spg_cuentas.estcla, spg_cuentas.scgctaint, scg_cuentas.sc_cuenta          
						  UNION
						  SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, sum(sno_salida.valsal) AS total,
								 spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint
						FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,                     
							 spg_cuentas, scg_cuentas, spg_ep1                                                  
					   WHERE sno_salida.codemp='0001'                           
				  	     AND sno_salida.codnom='".$ls_codnom."'                          
				  	     AND sno_salida.codperi='".$ls_codperi."' 
						 AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') 
						 AND sno_salida.valsal <> 0       
						 AND sno_concepto.intprocon = '1' 
						 AND spg_cuentas.status = 'C'     
						 AND spg_ep1.estint = 1           
						 AND substr(sno_concepto.codpro, 1, 25) = spg_ep1.codestpro1 
						 AND spg_ep1.estcla = sno_concepto.estcla          
						 AND spg_ep1.sc_cuenta=spg_cuentas.sc_cuenta       
						 AND spg_cuentas.scgctaint= scg_cuentas.sc_cuenta  
						 AND sno_personalnomina.codemp = sno_salida.codemp 
						 AND sno_personalnomina.codnom = sno_salida.codnom 
						 AND sno_personalnomina.codper = sno_salida.codper 
						 AND sno_salida.codemp = sno_concepto.codemp       
						 AND sno_salida.codnom = sno_concepto.codnom       
						 AND sno_salida.codconc = sno_concepto.codconc     
						 AND sno_personalnomina.codemp = sno_unidadadmin.codemp  
						 AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm 
						 AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm       
						 AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm       
						 AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm       
						 AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm       
						 AND spg_cuentas.codemp = sno_concepto.codemp                       
						 AND substr(sno_concepto.codpro, 1, 25) = spg_cuentas.codestpro1    
						 AND substr(sno_concepto.codpro, 26, 25) = spg_cuentas.codestpro2   
						 AND substr(sno_concepto.codpro, 51, 25) = spg_cuentas.codestpro3   
						 AND substr(sno_concepto.codpro, 76, 25) = spg_cuentas.codestpro4   
						 AND substr(sno_concepto.codpro, 101, 25) = spg_cuentas.codestpro5     
						GROUP BY spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint, 
						         scg_cuentas.sc_cuenta
						  UNION
						 SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, sum(sno_salida.valsal) AS total,
							  spg_cuentas.codestpro1,  spg_cuentas.estcla, spg_cuentas.scgctaint 
						 FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,   
						      spg_cuentas, scg_cuentas, spg_ep1                                
					    WHERE sno_salida.codemp='0001'                           
				  	      AND sno_salida.codnom='".$ls_codnom."'                          
				  	      AND sno_salida.codperi='".$ls_codperi."'  
						  AND (sno_salida.tipsal = 'A'OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') 
						  AND sno_salida.valsal <> 0         
						  AND sno_concepto.intprocon = '0'   
						  AND spg_cuentas.status = 'C'       
						  AND spg_ep1.estint = 1             
						  AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_ep1.codestpro1
						  AND spg_ep1.estcla= sno_unidadadmin.estcla        
						  AND spg_ep1.sc_cuenta=spg_cuentas.scgctaint 					  
						  AND spg_cuentas.sc_cuenta= scg_cuentas.sc_cuenta   
						  AND sno_personalnomina.codemp = sno_salida.codemp  
						  AND sno_personalnomina.codnom = sno_salida.codnom 
						  AND sno_personalnomina.codper = sno_salida.codper 
						  AND sno_salida.codemp = sno_concepto.codemp       
						  AND sno_salida.codnom = sno_concepto.codnom       
						  AND sno_salida.codconc = sno_concepto.codconc      
						  AND sno_personalnomina.codemp = sno_unidadadmin.codemp 
						  AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  
						  AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm        
						  AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm        
						  AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm        
						  AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm        
						  AND spg_cuentas.codemp = sno_concepto.codemp                        
						  AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon                 
						  AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_cuentas.codestpro1   
						  AND substr(sno_unidadadmin.codprouniadm, 26, 25) = spg_cuentas.codestpro2  
						  AND substr(sno_unidadadmin.codprouniadm, 51, 25) = spg_cuentas.codestpro3  
						  AND substr(sno_unidadadmin.codprouniadm, 76, 25) = spg_cuentas.codestpro4  
						  AND substr(sno_unidadadmin.codprouniadm, 101, 25) = spg_cuentas.codestpro5  
					   GROUP BY spg_cuentas.codestpro1,spg_cuentas.estcla, spg_cuentas.scgctaint, scg_cuentas.sc_cuenta
					       ORDER BY codestpro1,total";
										    
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_contabilizar_conceptos_scg_int ERROR->"
			                            .$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql_origen->fetch_row($rs_data))
			{
				if ($rs_datos=="")
				{   
					$this->DS->data=$this->io_sql_origen->obtener_datos($rs_data);
					//$this->DS->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),array('0'=>'cuenta','1'=>'operacion'));
					$li_totrow=$this->DS->getRowCount("cuenta");
					for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
					{
						$ls_cuenta=$this->DS->data["cuenta"][$li_i];
						$ls_operacion=$this->DS->data["operacion"][$li_i];
						$li_total=abs(round($this->DS->data["total"][$li_i],2));
												
						$ls_codest1_G=$this->DS->data["codestpro1"][$li_i];
						$ls_estcla_G=$this->DS->data["estcla"][$li_i];
						$ls_ctaint_G=$this->DS->data["scgctaint"][$li_i];
						
						if ($ls_operacion=="H")
						{
							$ls_cuenta=$ls_ctaint_G;
						}
						$ls_codconc="0000000001";
						$ls_codcomapo=substr($ls_codconc,2,8).$ls_codperi.$ls_codnom;
						$lb_valido=$this->uf_insert_contabilizacion_scg_int($as_codcom,$as_codpro,$as_codben,$as_tipodestino,
																			$as_descripcion,$ls_cuenta,$ls_operacion,$li_total,
																			$ls_tipnom,$ls_codconc,$ai_genrecdoc,$ai_tipdocnom,
																			$ai_gennotdeb,$ai_genvou,$ls_codcomapo,
																			$ls_codest1_G, $ls_estcla_G);
					}
					$this->DS->resetds("cuenta");
				}
				else
				{
				    
					$this->DS->data=$this->io_sql_origen->obtener_datos($rs_data);
					$this->DS->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),array('0'=>'cuenta','1'=>'operacion'));
					$li_totrow=$this->DS->getRowCount("cuenta");/// total de conceptos
					$this->DS_R->data=$this->io_sql_origen->obtener_datos($rs_datos);
					$this->DS_R->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),array('0'=>'cuenta','1'=>'operacion'));
					$ls_total=$this->DS_R->getRowCount("cuenta");// total conceptos de reintegro
					$ls_ctaint="";
					    for($li_j=1;($li_j<=$ls_total);$li_j++)
						{  
					    	$ls_ctaint=$this->DS_R->data["scgctaint"][$li_j];
						    $ls_codest1=$this->DS_R->data["codestpro1"][$li_j];
						    $ls_estcla=$this->DS_R->data["estcla"][$li_j];						
						    $total=abs(round($this->DS_R->data["total"][$li_j],2));
							$ls_cuenta_R=$this->DS_R->data["cuenta"][$li_j];
						}// fin del for($li_j=1;($li_j<=$ls_total);$li_j++)
						
					for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
					{ 	
						
						$ls_cuenta=$this->DS->data["cuenta"][$li_i];
						$ls_ctaint_G=$this->DS->data["scgctaint"][$li_i];						
						$ls_operacion=$this->DS->data["operacion"][$li_i];
						if ($ls_operacion=="H")
						{
							$ls_cuenta2=$ls_ctaint_G;
						}
						else
						{
							$ls_cuenta2=$ls_cuenta;
						}
						$li_total=abs(round($this->DS->data["total"][$li_i],2));						
						$ls_codest1_G=$this->DS->data["codestpro1"][$li_i];
						$ls_estcla_G=$this->DS->data["estcla"][$li_i];						
						
						if (($ls_ctaint==$ls_ctaint_G)&&($ls_codest1==$ls_codest1_G)&&($ls_estcla==$ls_estcla_G)&&($ls_cuenta_R==$ls_cuenta))
						{   
						    $ls_monto=$li_total; 
							$li_total=$li_total-$total;
						}
						elseif (($ls_monto==$li_total)&&($ls_operacion=="H")&&($ls_ctaint==$ls_ctaint_G)&&($ls_codest1==$ls_codest1_G)&&($ls_estcla==$ls_estcla_G))
						{ 
							$li_total=$li_total-$total;
						}
													
						$ls_codconc="0000000001";
						$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
						$lb_valido=$this->uf_insert_contabilizacion_scg_int($as_codcom,$as_codpro,$as_codben,$as_tipodestino,
																			$as_descripcion,$ls_cuenta2,$ls_operacion,$li_total,
																			$ls_tipnom,$ls_codconc,$ai_genrecdoc,$ai_tipdocnom,
																			$ai_gennotdeb,$ai_genvou,$ls_codcomapo,
																			$ls_codest1_G, $ls_estcla_G);							
					}//fin del for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)						
					
					$this->DS->resetds("cuenta");
					$this->DS_R->resetds("cuenta");
				}
			}
			$this->io_sql_origen->free_result($rs_data);
		}	
		return $lb_valido;	  
	}// end function uf_contabilizar_conceptos_scg_int
	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_contabilizar_aportes_scg_int($as_codcom,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,$ai_genrecapo,
	                                         $ai_tipdocapo, $ai_gennotdeb,$ai_genvou,$as_operacionaporte)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_aportes_scg_int
		//      Arguments: as_tipodestino  ai_genrecapo
		//	               as_codcom  //  Código de Comprobante
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecapo  //  Generar recepción de documento
		//	    		   ai_tipdocapo  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_operacionaporte  //  Operación con que se va a contabilizar los aportes
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 14/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="A";
		$ls_sql="";
		    // se buscan todas las cuentas contables de intercampañias realcioandas con el presupuesto
			// estas van por el DEBE						
		$ls_sql="SELECT scg_cuentas.sc_cuenta AS cuenta, 'D' AS operacion, sum(abs(sno_salida.valsal)) AS total, 
					 sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc,
					 spg_cuentas.codestpro1,spg_cuentas.estcla, spg_cuentas.scgctaint
				  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,  
					spg_cuentas, scg_cuentas, spg_ep1                               
				  WHERE sno_salida.codemp='0001'                           
				  	AND sno_salida.codnom='".$ls_codnom."'                          
				  	AND sno_salida.codperi='".$ls_codperi."'  
					AND sno_salida.valsal <> 0   
					AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4') 
					AND sno_concepto.intprocon= '1' 
					AND spg_cuentas.status = 'C'    
					AND spg_ep1.estint = 1          
					AND substr(sno_concepto.codpro, 1, 25) = spg_ep1.codestpro1 
					AND spg_ep1.estcla = sno_concepto.estcla                    
					AND spg_ep1.sc_cuenta=spg_cuentas.scgctaint 					  
					AND spg_cuentas.sc_cuenta= scg_cuentas.sc_cuenta
					AND sno_personalnomina.codemp = sno_salida.codemp           
					AND sno_personalnomina.codnom = sno_salida.codnom           
					AND sno_personalnomina.codper = sno_salida.codper           
					AND sno_salida.codemp = sno_concepto.codemp                 
					AND sno_salida.codnom = sno_concepto.codnom                 
					AND sno_salida.codconc = sno_concepto.codconc               
					AND sno_personalnomina.codemp = sno_unidadadmin.codemp      
					AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  
					AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm        
					AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm        
					AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm        
					AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm        
					AND spg_cuentas.codemp = sno_concepto.codemp                        
					AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon              
					AND substr(sno_concepto.codpro, 1, 25) = spg_cuentas.codestpro1     
					AND substr(sno_concepto.codpro, 26, 25) = spg_cuentas.codestpro2    
					AND substr(sno_concepto.codpro, 51, 25) = spg_cuentas.codestpro3    
					AND substr(sno_concepto.codpro, 76, 25) = spg_cuentas.codestpro4    
					AND substr(sno_concepto.codpro, 101, 25) = spg_cuentas.codestpro5   
					 GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben,
							  spg_cuentas.codestpro1,	spg_cuentas.estcla, spg_cuentas.scgctaint
				  UNION
				  SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, sum(abs(sno_salida.valsal)) AS total, 
						 sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc,
						 spg_cuentas.codestpro1,spg_cuentas.estcla, spg_cuentas.scgctaint
					  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,  
						spg_cuentas, scg_cuentas, spg_ep1                               
					 WHERE sno_salida.codemp='0001'                           
				  	    AND sno_salida.codnom='".$ls_codnom."'                          
				  	    AND sno_salida.codperi='".$ls_codperi."'  
						AND sno_salida.valsal <> 0   
						AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4') 
						AND sno_concepto.intprocon= '1' 
						AND spg_cuentas.status = 'C'    
						AND spg_ep1.estint = 1          
						AND substr(sno_concepto.codpro, 1, 25) = spg_ep1.codestpro1 
						AND spg_ep1.estcla = sno_concepto.estcla                    
						AND spg_ep1.sc_cuenta=spg_cuentas.scgctaint 					  
						AND spg_cuentas.sc_cuenta= scg_cuentas.sc_cuenta
						AND sno_personalnomina.codemp = sno_salida.codemp           
						AND sno_personalnomina.codnom = sno_salida.codnom           
						AND sno_personalnomina.codper = sno_salida.codper           
						AND sno_salida.codemp = sno_concepto.codemp                 
						AND sno_salida.codnom = sno_concepto.codnom                 
						AND sno_salida.codconc = sno_concepto.codconc               
						AND sno_personalnomina.codemp = sno_unidadadmin.codemp      
						AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  
						AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm        
						AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm        
						AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm        
						AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm        
						AND spg_cuentas.codemp = sno_concepto.codemp                        
						AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon              
						AND substr(sno_concepto.codpro, 1, 25) = spg_cuentas.codestpro1     
						AND substr(sno_concepto.codpro, 26, 25) = spg_cuentas.codestpro2    
						AND substr(sno_concepto.codpro, 51, 25) = spg_cuentas.codestpro3    
						AND substr(sno_concepto.codpro, 76, 25) = spg_cuentas.codestpro4    
						AND substr(sno_concepto.codpro, 101, 25) = spg_cuentas.codestpro5   
						 GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben,
								  spg_cuentas.codestpro1,	spg_cuentas.estcla, spg_cuentas.scgctaint
					UNION	
		        SELECT scg_cuentas.sc_cuenta AS cuenta, 'D' AS operacion, sum(abs(sno_salida.valsal)) AS total, 
					sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc, 
					spg_cuentas.codestpro1,	spg_cuentas.estcla, spg_cuentas.scgctaint
				   FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,   
					spg_cuentas, scg_cuentas, spg_ep1                                
				  WHERE sno_salida.codemp='0001'                           
				  	AND sno_salida.codnom='".$ls_codnom."'                          
				  	AND sno_salida.codperi='".$ls_codperi."'  
					AND sno_salida.valsal <> 0                      
					AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4') 
					AND sno_concepto.intprocon = '0' 
					AND spg_cuentas.status = 'C'     
					AND spg_ep1.estint = 1           
					AND spg_ep1.codestpro1 = substr(sno_unidadadmin.codprouniadm, 1, 25) 
					AND spg_ep1.estcla = sno_unidadadmin.estcla 	    
					AND spg_ep1.sc_cuenta=spg_cuentas.scgctaint 					  
					AND spg_cuentas.sc_cuenta= scg_cuentas.sc_cuenta    
					AND sno_personalnomina.codemp = sno_salida.codemp   
					AND sno_personalnomina.codnom = sno_salida.codnom   
					AND sno_personalnomina.codper = sno_salida.codper   
					AND sno_salida.codemp = sno_concepto.codemp         
					AND sno_salida.codnom = sno_concepto.codnom         
					AND sno_salida.codconc = sno_concepto.codconc       
					AND sno_personalnomina.codemp = sno_unidadadmin.codemp 
					AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  
					AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm        
					AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm        
					AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm        
					AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm        
					AND spg_cuentas.codemp = sno_concepto.codemp                        
					AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon	     
					AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_cuentas.codestpro1  
					AND substr(sno_unidadadmin.codprouniadm, 26, 25) = spg_cuentas.codestpro2 
					AND substr(sno_unidadadmin.codprouniadm, 51, 25) = spg_cuentas.codestpro3
					AND substr(sno_unidadadmin.codprouniadm, 76, 25) = spg_cuentas.codestpro4 
					AND substr(sno_unidadadmin.codprouniadm, 101, 25) = spg_cuentas.codestpro5 
				GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben, 
						 spg_cuentas.codestpro1,spg_cuentas.estcla, spg_cuentas.scgctaint
					UNION
				SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, sum(abs(sno_salida.valsal)) AS total, 
						sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc, 
						spg_cuentas.codestpro1,	spg_cuentas.estcla, spg_cuentas.scgctaint
					   FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,   
						spg_cuentas, scg_cuentas, spg_ep1                                
					   WHERE sno_salida.codemp='0001'                           
				  	    AND sno_salida.codnom='".$ls_codnom."'                          
				  	    AND sno_salida.codperi='".$ls_codperi."'  
						AND sno_salida.valsal <> 0                      
						AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4') 
						AND sno_concepto.intprocon = '0' 
						AND spg_cuentas.status = 'C'     
						AND spg_ep1.estint = 1           
						AND spg_ep1.codestpro1 = substr(sno_unidadadmin.codprouniadm, 1, 25) 
						AND spg_ep1.estcla = sno_unidadadmin.estcla 	    
						AND spg_ep1.sc_cuenta=spg_cuentas.scgctaint 					  
						AND spg_cuentas.sc_cuenta= scg_cuentas.sc_cuenta    
						AND sno_personalnomina.codemp = sno_salida.codemp   
						AND sno_personalnomina.codnom = sno_salida.codnom   
						AND sno_personalnomina.codper = sno_salida.codper   
						AND sno_salida.codemp = sno_concepto.codemp         
						AND sno_salida.codnom = sno_concepto.codnom         
						AND sno_salida.codconc = sno_concepto.codconc       
						AND sno_personalnomina.codemp = sno_unidadadmin.codemp 
						AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  
						AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm        
						AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm        
						AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm        
						AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm        
						AND spg_cuentas.codemp = sno_concepto.codemp                        
						AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon	     
						AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_cuentas.codestpro1  
						AND substr(sno_unidadadmin.codprouniadm, 26, 25) = spg_cuentas.codestpro2 
						AND substr(sno_unidadadmin.codprouniadm, 51, 25) = spg_cuentas.codestpro3
						AND substr(sno_unidadadmin.codprouniadm, 76, 25) = spg_cuentas.codestpro4 
						AND substr(sno_unidadadmin.codprouniadm, 101, 25) = spg_cuentas.codestpro5 
					GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben, 
							 spg_cuentas.codestpro1,spg_cuentas.estcla, spg_cuentas.scgctaint";							
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_contabilizar_aportes_scg_int ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql_origen->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql_origen->obtener_datos($rs_data);
				$this->DS->group_by(array('0'=>'codconc','1'=>'cuenta','2'=>'operacion'),array('0'=>'total'),array('0'=>'codconc','1'=>'cuenta','2'=>'operacion'));
				$li_totrow=$this->DS->getRowCount("cuenta");
				for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
				{
					$ls_cuenta=$this->DS->data["cuenta"][$li_i];
					$ls_operacion=$this->DS->data["operacion"][$li_i];
					$li_total=abs(round($this->DS->data["total"][$li_i],2));
					$ls_codpro=$this->DS->data["codprov"][$li_i];
					$ls_cedben=$this->DS->data["cedben"][$li_i];
					$ls_codconc=$this->DS->data["codconc"][$li_i];
					
					$ls_codest1_G=$this->DS->data["codestpro1"][$li_i];
					$ls_estcla_G=$this->DS->data["estcla"][$li_i];
					$ls_ctaint_G=$this->DS->data["scgctaint"][$li_i];
					if ($ls_operacion=="H")
					{
						$ls_cuenta=$ls_ctaint_G;
					}
						
					$ls_tipodestino="";
					if($ls_codpro=="----------")
					{
						$ls_tipodestino="B";
					}
					if($ls_cedben=="----------")
					{
						$ls_tipodestino="P";
					}
					$ls_codcomapo=substr($ls_codconc,2,8).$ls_codperi.$ls_codnom;
					$lb_valido=$this->uf_insert_contabilizacion_scg_int($as_codcom,$ls_codpro,$ls_cedben,$ls_tipodestino,$as_descripcion,
																	$ls_cuenta,$ls_operacion,$li_total,$ls_tipnom,$ls_codconc,
																	$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,
																	$ls_codcomapo,$ls_codest1_G, $ls_estcla_G);
				}
				$this->DS->resetds("cuenta");
			}
			$this->io_sql_origen->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_aportes_scg_int
	//------------------------------------------------------------------------------------------------------------------------------------
    function uf_load_conceptos_scg_normales_int($as_operacionnomina,$as_cuentapasivo,$ai_genrecdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_conceptos_scg_normales_int 
		//	    Arguments: as_operacionnomina // Operación de contabilización 
		//				   as_cuentapasivo // Cuenta de Pasivo hacia donde se contabiliza la nómina
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos normales
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 14/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_sql=" SELECT scg_cuentas.sc_cuenta AS cuenta, 'D' AS operacion, sum(sno_salida.valsal) AS total,
						spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint
						FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,                     
							 spg_cuentas, scg_cuentas, spg_ep1                                                  
					   WHERE sno_salida.codemp='".$this->ls_codemp."'                           
				  	     AND sno_salida.codnom='".$this->ls_codnom."'                          
				  	     AND sno_salida.codperi='".$this->ls_peractnom."' 
						 AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') 
						 AND sno_salida.valsal <> 0       
						 AND sno_concepto.intprocon = '1' 
						 AND spg_cuentas.status = 'C'     
						 AND spg_ep1.estint = 1           
						 AND substr(sno_concepto.codpro, 1, 25) = spg_ep1.codestpro1 
						 AND spg_ep1.estcla = sno_concepto.estcla          
						 AND spg_ep1.sc_cuenta=spg_cuentas.scgctaint 					  
						 AND spg_cuentas.sc_cuenta= scg_cuentas.sc_cuenta   
						 AND sno_personalnomina.codemp = sno_salida.codemp 
						 AND sno_personalnomina.codnom = sno_salida.codnom 
						 AND sno_personalnomina.codper = sno_salida.codper 
						 AND sno_salida.codemp = sno_concepto.codemp       
						 AND sno_salida.codnom = sno_concepto.codnom       
						 AND sno_salida.codconc = sno_concepto.codconc     
						 AND sno_personalnomina.codemp = sno_unidadadmin.codemp  
						 AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm 
						 AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm       
						 AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm       
						 AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm       
						 AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm       
						 AND spg_cuentas.codemp = sno_concepto.codemp                       
						 AND substr(sno_concepto.codpro, 1, 25) = spg_cuentas.codestpro1    
						 AND substr(sno_concepto.codpro, 26, 25) = spg_cuentas.codestpro2   
						 AND substr(sno_concepto.codpro, 51, 25) = spg_cuentas.codestpro3   
						 AND substr(sno_concepto.codpro, 76, 25) = spg_cuentas.codestpro4   
						 AND substr(sno_concepto.codpro, 101, 25) = spg_cuentas.codestpro5     
						GROUP BY spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint, 
						         scg_cuentas.sc_cuenta
						UNION 						                    
                     SELECT scg_cuentas.sc_cuenta AS cuenta, 'D' AS operacion, sum(sno_salida.valsal) AS total,
							  spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint 
						 FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,   
						      spg_cuentas, scg_cuentas, spg_ep1                                
					    WHERE sno_salida.codemp='".$this->ls_codemp."'                           
				  	      AND sno_salida.codnom='".$this->ls_codnom."'                          
				  	      AND sno_salida.codperi='".$this->ls_peractnom."' 
						  AND (sno_salida.tipsal = 'A'OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') 
						  AND sno_salida.valsal <> 0         
						  AND sno_concepto.intprocon = '0'   
						  AND spg_cuentas.status = 'C'       
						  AND spg_ep1.estint = 1             
						  AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_ep1.codestpro1 
						  AND spg_ep1.estcla= sno_unidadadmin.estcla        
						  AND spg_ep1.sc_cuenta=spg_cuentas.scgctaint 					  
						  AND spg_cuentas.sc_cuenta= scg_cuentas.sc_cuenta   
						  AND sno_personalnomina.codemp = sno_salida.codemp  
						  AND sno_personalnomina.codnom = sno_salida.codnom 
						  AND sno_personalnomina.codper = sno_salida.codper 
						  AND sno_salida.codemp = sno_concepto.codemp       
						  AND sno_salida.codnom = sno_concepto.codnom       
						  AND sno_salida.codconc = sno_concepto.codconc      
						  AND sno_personalnomina.codemp = sno_unidadadmin.codemp 
						  AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  
						  AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm        
						  AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm        
						  AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm        
						  AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm        
						  AND spg_cuentas.codemp = sno_concepto.codemp                        
						  AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon                 
						  AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_cuentas.codestpro1   
						  AND substr(sno_unidadadmin.codprouniadm, 26, 25) = spg_cuentas.codestpro2  
						  AND substr(sno_unidadadmin.codprouniadm, 51, 25) = spg_cuentas.codestpro3  
						  AND substr(sno_unidadadmin.codprouniadm, 76, 25) = spg_cuentas.codestpro4  
						  AND substr(sno_unidadadmin.codprouniadm, 101, 25) = spg_cuentas.codestpro5  
					   GROUP BY spg_cuentas.codestpro1,spg_cuentas.estcla, spg_cuentas.scgctaint, scg_cuentas.sc_cuenta          
						  UNION
						  SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, sum(sno_salida.valsal) AS total,
								 spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint
						FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,                     
							 spg_cuentas, scg_cuentas, spg_ep1                                                  
					   WHERE sno_salida.codemp='".$this->ls_codemp."'                           
				  	     AND sno_salida.codnom='".$this->ls_codnom."'                          
				  	     AND sno_salida.codperi='".$this->ls_peractnom."' 
						 AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') 
						 AND sno_salida.valsal <> 0       
						 AND sno_concepto.intprocon = '1' 
						 AND spg_cuentas.status = 'C'     
						 AND spg_ep1.estint = 1           
						 AND substr(sno_concepto.codpro, 1, 25) = spg_ep1.codestpro1 
						 AND spg_ep1.estcla = sno_concepto.estcla          
						 AND spg_ep1.sc_cuenta=spg_cuentas.sc_cuenta       
						 AND spg_cuentas.scgctaint= scg_cuentas.sc_cuenta  
						 AND sno_personalnomina.codemp = sno_salida.codemp 
						 AND sno_personalnomina.codnom = sno_salida.codnom 
						 AND sno_personalnomina.codper = sno_salida.codper 
						 AND sno_salida.codemp = sno_concepto.codemp       
						 AND sno_salida.codnom = sno_concepto.codnom       
						 AND sno_salida.codconc = sno_concepto.codconc     
						 AND sno_personalnomina.codemp = sno_unidadadmin.codemp  
						 AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm 
						 AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm       
						 AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm       
						 AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm       
						 AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm       
						 AND spg_cuentas.codemp = sno_concepto.codemp                       
						 AND substr(sno_concepto.codpro, 1, 25) = spg_cuentas.codestpro1    
						 AND substr(sno_concepto.codpro, 26, 25) = spg_cuentas.codestpro2   
						 AND substr(sno_concepto.codpro, 51, 25) = spg_cuentas.codestpro3   
						 AND substr(sno_concepto.codpro, 76, 25) = spg_cuentas.codestpro4   
						 AND substr(sno_concepto.codpro, 101, 25) = spg_cuentas.codestpro5     
						GROUP BY spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint, 
						         scg_cuentas.sc_cuenta
						  UNION
						 SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, sum(sno_salida.valsal) AS total,
							  spg_cuentas.codestpro1,  spg_cuentas.estcla, spg_cuentas.scgctaint 
						 FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,   
						      spg_cuentas, scg_cuentas, spg_ep1                                
					    WHERE sno_salida.codemp='".$this->ls_codemp."'                           
				  	      AND sno_salida.codnom='".$this->ls_codnom."'                          
				  	      AND sno_salida.codperi='".$this->ls_peractnom."'  
						  AND (sno_salida.tipsal = 'A'OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') 
						  AND sno_salida.valsal <> 0         
						  AND sno_concepto.intprocon = '0'   
						  AND spg_cuentas.status = 'C'       
						  AND spg_ep1.estint = 1             
						  AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_ep1.codestpro1
						  AND spg_ep1.estcla= sno_unidadadmin.estcla        
						  AND spg_ep1.sc_cuenta=spg_cuentas.scgctaint 					  
						  AND spg_cuentas.sc_cuenta= scg_cuentas.sc_cuenta   
						  AND sno_personalnomina.codemp = sno_salida.codemp  
						  AND sno_personalnomina.codnom = sno_salida.codnom 
						  AND sno_personalnomina.codper = sno_salida.codper 
						  AND sno_salida.codemp = sno_concepto.codemp       
						  AND sno_salida.codnom = sno_concepto.codnom       
						  AND sno_salida.codconc = sno_concepto.codconc      
						  AND sno_personalnomina.codemp = sno_unidadadmin.codemp 
						  AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  
						  AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm        
						  AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm        
						  AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm        
						  AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm        
						  AND spg_cuentas.codemp = sno_concepto.codemp                        
						  AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon                 
						  AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_cuentas.codestpro1   
						  AND substr(sno_unidadadmin.codprouniadm, 26, 25) = spg_cuentas.codestpro2  
						  AND substr(sno_unidadadmin.codprouniadm, 51, 25) = spg_cuentas.codestpro3  
						  AND substr(sno_unidadadmin.codprouniadm, 76, 25) = spg_cuentas.codestpro4  
						  AND substr(sno_unidadadmin.codprouniadm, 101, 25) = spg_cuentas.codestpro5  
					   GROUP BY spg_cuentas.codestpro1,spg_cuentas.estcla, spg_cuentas.scgctaint, scg_cuentas.sc_cuenta
					       ORDER BY codestpro1,total";								
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_load_conceptos_scg_normales_int ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_conceptos_scg_normales_int
	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_load_aportes_scg_normales_int($as_operacionaporte)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_aportes_scg_normales 
		//	    Arguments: as_operacionaporte  //  Operación con que se va a contabilizar los aportes
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los aportes
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 14/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
			   $ls_sql=" SELECT scg_cuentas.sc_cuenta AS cuenta, 'D' AS operacion, sum(abs(sno_salida.valsal)) AS total,  ".
					   "		   sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc                        ".
					   "  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas,                ".
					   "       scg_cuentas, spg_ep1                                                                       ".
					   " WHERE sno_salida.codemp='".$this->ls_codemp."'                                                   ". 
				       "   AND sno_salida.codnom='".$this->ls_codnom."'                                                   ".
				       "   AND sno_salida.codperi='".$this->ls_peractnom."'                                               ".
					   "   AND  sno_salida.valsal <> 0                                                                    ".
					   "   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')         ".
					   "   AND sno_concepto.intprocon = '1'                                                               ".
					   "   AND sno_concepto.conprocon = '0'                                                               ".
					   "   AND spg_cuentas.status = 'C'                                                                   ".
					   "   AND spg_ep1.estint = 1                                                                         ".
					   "   AND substr(sno_concepto.codpro, 1, 25) = spg_ep1.codestpro1                                    ".
					   "   AND spg_ep1.estcla = sno_concepto.estcla                                                       ".
					   "   AND sno_personalnomina.codemp = sno_salida.codemp                                              ".
					   "   AND sno_personalnomina.codnom = sno_salida.codnom                                              ".
					   "   AND sno_personalnomina.codper = sno_salida.codper                                              ".
					   "   AND sno_salida.codemp = sno_concepto.codemp                                                    ".
					   "   AND sno_salida.codnom = sno_concepto.codnom                                                    ".
					   "   AND sno_salida.codconc = sno_concepto.codconc                                                  ".
					   "   AND sno_personalnomina.codemp = sno_unidadadmin.codemp                                         ".
					   "   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm                             ".
					   "   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm                                   ".
					   "   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm                                   ".
					   "   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm                                   ".
					   "   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm                                   ".
					   "   AND spg_cuentas.codemp = sno_concepto.codemp                                                   ".
					   "   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon                                         ".
					   "   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta                                              ".
					   "   AND substr(sno_concepto.codpro, 1, 25) = spg_cuentas.codestpro1                                ".
					   "   AND substr(sno_concepto.codpro, 26, 25) = spg_cuentas.codestpro2                               ".
					   "   AND substr(sno_concepto.codpro, 51, 25) = spg_cuentas.codestpro3                               ".
					   "   AND substr(sno_concepto.codpro, 76, 25) = spg_cuentas.codestpro4                               ".
					   "   AND substr(sno_concepto.codpro, 101, 25) = spg_cuentas.codestpro5                              ".
					   "   AND sno_concepto.estcla=spg_cuentas.estcla                                                     ".
					   "  GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben "; 
					   
				$ls_sql=$ls_sql."  UNION  ".
						" SELECT scg_cuentas.sc_cuenta AS cuenta, 'D' AS operacion, sum(abs(sno_salida.valsal)) AS total,  ".
						"		 sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc                           ".
						"   FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas,               ".
						"        scg_cuentas, spg_ep1                                                                      ".
						" WHERE sno_salida.codemp='".$this->ls_codemp."'                                                   ". 
				        "   AND sno_salida.codnom='".$this->ls_codnom."'                                                   ".
				        "   AND sno_salida.codperi='".$this->ls_peractnom."'                                               ".
						"   AND sno_salida.valsal <> 0                                                                     ".
						"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')         ".
						"   AND sno_concepto.intprocon = '0'                                                               ".
						"   AND sno_concepto.conprocon = '0'                                                               ".
						"   AND spg_cuentas.status = 'C'                                                                   ".
						"   AND spg_ep1.estint = 1                                                                         ".
						"   AND spg_ep1.codestpro1 = substr(sno_unidadadmin.codprouniadm, 1, 25)                           ".
						"   AND spg_ep1.estcla = sno_unidadadmin.estcla                                                    ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp                                              ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom                                              ".
						"   AND sno_personalnomina.codper = sno_salida.codper                                              ".
						"   AND sno_salida.codemp = sno_concepto.codemp                                                    ".
						"   AND sno_salida.codnom = sno_concepto.codnom                                                    ".
						"   AND sno_salida.codconc = sno_concepto.codconc                                                  ".
						"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp                                         ".
						"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm                             ".
						"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm                                   ".
						"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm                                   ".
						"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm                                   ".
						"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm                                   ".
						"   AND spg_cuentas.codemp = sno_concepto.codemp                                                   ".
						"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon                                         ".
						"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta                                              ".
						"   AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_cuentas.codestpro1                       ".
						"   AND substr(sno_unidadadmin.codprouniadm, 26, 25) = spg_cuentas.codestpro2                      ".
						"   AND substr(sno_unidadadmin.codprouniadm, 51, 25) = spg_cuentas.codestpro3                      ".
						"   AND substr(sno_unidadadmin.codprouniadm, 76, 25) = spg_cuentas.codestpro4                      ".
						"   AND substr(sno_unidadadmin.codprouniadm, 101, 25) = spg_cuentas.codestpro5                     ".
						"   AND sno_unidadadmin.estcla=spg_cuentas.estcla                                                  ".
						"  GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben "; 
						
				$ls_sql=$ls_sql." UNION".  
				        " SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, sum(abs(sno_salida.valsal)) AS total,  ".
						"		 sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc                           ".
						"   FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,                            ".
						"        spg_cuentas, scg_cuentas, spg_ep1                                                         ".
						"  WHERE sno_salida.codemp='".$this->ls_codemp."'                                                  ". 
				        "    AND sno_salida.codnom='".$this->ls_codnom."'                                                  ".
				        "    AND sno_salida.codperi='".$this->ls_peractnom."'                                              ".
						"    AND  sno_salida.valsal <> 0                                                                   ".
						"    AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')        ".
						"    AND sno_concepto.intprocon = '1'                                                              ".
						"    AND sno_concepto.conprocon = '0'                                                              ".
						"    AND spg_cuentas.status = 'C'                                                                  ".
						"    AND spg_ep1.estint = 1                                                                        ".
						"    AND substr(sno_concepto.codpro, 1, 25) = spg_ep1.codestpro1                                   ".
						"    AND spg_ep1.estcla = sno_concepto.estcla                                                      ".
						"    AND spg_ep1.sc_cuenta = spg_cuentas.scgctaint                                                 ".
						"    AND spg_cuentas.scgctaint = scg_cuentas.sc_cuenta                                             ".
						"    AND sno_personalnomina.codemp = sno_salida.codemp                                             ".
						"    AND sno_personalnomina.codnom = sno_salida.codnom                                             ".
						"    AND sno_personalnomina.codper = sno_salida.codper                                             ".
						"    AND sno_salida.codemp = sno_concepto.codemp                                                   ".
						"    AND sno_salida.codnom = sno_concepto.codnom                                                   ".
						"    AND sno_salida.codconc = sno_concepto.codconc                                                 ".
						"    AND sno_personalnomina.codemp = sno_unidadadmin.codemp                                        ".
						"    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm                            ".
						"    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm                                  ".
						"    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm                                  ".
						"    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm                                  ".
						"    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm                                  ".
						"    AND spg_cuentas.codemp = sno_concepto.codemp                                                  ".
						"    AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon                                        ".
						"    AND substr(sno_concepto.codpro, 1, 25) = spg_cuentas.codestpro1                               ".
						"    AND substr(sno_concepto.codpro, 26, 25) = spg_cuentas.codestpro2                              ".
						"    AND substr(sno_concepto.codpro, 51, 25) = spg_cuentas.codestpro3                              ".
						"    AND substr(sno_concepto.codpro, 76, 25) = spg_cuentas.codestpro4                              ".
						"    AND substr(sno_concepto.codpro, 101, 25) = spg_cuentas.codestpro5                             ".
						"	  GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben";
						
				$ls_sql=$ls_sql." UNION ".
				        " SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, sum(abs(sno_salida.valsal)) AS total,  ".
						"		 sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc                           ".
						"    FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto,                           ".
						"         spg_cuentas, scg_cuentas, spg_ep1                                                        ".
						"  WHERE sno_salida.codemp='".$this->ls_codemp."'                                                  ". 
				        "    AND sno_salida.codnom='".$this->ls_codnom."'                                                  ".
				        "    AND sno_salida.codperi='".$this->ls_peractnom."'                                              ".
						"    AND sno_salida.valsal <> 0                                                                    ".
						"    AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')        ".
						"    AND sno_concepto.intprocon = '0'                                                              ".
						"    AND sno_concepto.conprocon = '0'                                                              ".
						"    AND spg_cuentas.status = 'C'                                                                  ".
						"    AND spg_ep1.estint = 1                                                                        ".
						"    AND spg_ep1.codestpro1 = substr(sno_unidadadmin.codprouniadm, 1, 25)                          ".
						"    AND spg_ep1.estcla = sno_unidadadmin.estcla                                                   ".
						"    AND spg_ep1.sc_cuenta = spg_cuentas.scgctaint                                                 ".
						"    AND spg_cuentas.scgctaint = scg_cuentas.sc_cuenta                                             ".
						"    AND sno_personalnomina.codemp = sno_salida.codemp                                             ".
						"    AND sno_personalnomina.codnom = sno_salida.codnom                                             ".
						"    AND sno_personalnomina.codper = sno_salida.codper                                             ".
						"    AND sno_salida.codemp = sno_concepto.codemp                                                   ".
						"    AND sno_salida.codnom = sno_concepto.codnom                                                   ".
						"    AND sno_salida.codconc = sno_concepto.codconc                                                 ".
						"    AND sno_personalnomina.codemp = sno_unidadadmin.codemp                                        ".
						"    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm                            ".
						"    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm                                  ".
						"    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm                                  ".
						"    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm                                  ".
						"    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm                                  ".
						"    AND spg_cuentas.codemp = sno_concepto.codemp                                                  ".
						"    AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon                                        ".
						"    AND substr(sno_unidadadmin.codprouniadm, 1, 25) = spg_cuentas.codestpro1                      ".
						"    AND substr(sno_unidadadmin.codprouniadm, 26, 25) = spg_cuentas.codestpro2                     ".
						"    AND substr(sno_unidadadmin.codprouniadm, 51, 25) = spg_cuentas.codestpro3                     ".
						"    AND substr(sno_unidadadmin.codprouniadm, 76, 25) = spg_cuentas.codestpro4                     ".
						"    AND substr(sno_unidadadmin.codprouniadm, 101, 25) = spg_cuentas.codestpro5                    ".
						"  GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_load_aportes_scg_normales_int ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_aportes_scg_normales_int
	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_rec_doc_pago_personal_cheque()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_rec_doc_pago_personal_cheque  
		//	    Arguments: 	  
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la informacion para la recepcion de documento del 
		//                 personal que cobra con cheque
	    //     Creado por: Ing. María Beatriz Unda
	    // Fecha Creación: 22/12/2009
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_valido=$this->uf_delete_sno_rd();				
		if ($lb_valido)
		{
			$ls_sql="SELECT sno_resumen.codper, sno_resumen.monnetres, sno_personalnomina.cueaboper ".
					"  FROM sno_resumen, sno_personalnomina ".
					" WHERE sno_resumen.codemp='".$this->ls_codemp."'        ". 
					"   AND sno_resumen.codnom='".$this->ls_codnom."'        ".
					"   AND sno_resumen.codperi='".$this->ls_peractnom."'    ".
					"   AND sno_personalnomina.pagefeper=1                   ".
					"   AND sno_personalnomina.codemp=sno_resumen.codemp     ".
					"   AND sno_personalnomina.codper=sno_resumen.codper     ".
					"   AND sno_personalnomina.codnom=sno_resumen.codnom     ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_procesar_rec_doc_pago_personal_cheque ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				while ((!$rs_data->EOF)&&($lb_valido))
				{		 
					$ls_codper=$rs_data->fields["codper"];
					$ld_monpagper=$rs_data->fields["monnetres"];
					$ls_ctadebe=$rs_data->fields["cueaboper"];
					$lb_valido=$this->uf_chequear_beneficiario($ls_codper,$ls_ctahaber);
					if (($lb_valido)&&($ls_ctahaber==""))
					{
						$this->io_mensajes->message("El personal ".$ls_codper." no tiene asocida cuenta contable como beneficiario. No se puede realizar la recepción de Documento al personal");
						$lb_valido=false;
					}
					elseif ($lb_valido)
					{
						$lb_valido=$this->uf_insert_sno_rd($ls_codper,$ld_monpagper,$ls_ctadebe,'D');					
						if ($lb_valido)
						{
							$lb_valido=$this->uf_insert_sno_rd($ls_codper,$ld_monpagper,$ls_ctahaber,'H');						
						}
					}
					$rs_data->MoveNext();
				}//fin del while
			}	
		}
		return $lb_valido;
	}// end function uf_procesar_rec_doc_pago_personal_cheque
	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_sno_rd()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_sno_rd
		//	    Arguments: $
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que elimina la data de recpeciones de documento de un periodo
	    //     Creado por: Ing. María Beatriz Unda
	    // Fecha Creación: 22/12/2009
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_codemp=$this->ls_codemp;
		$ls_codnom=$this->ls_codnom;
		$ls_codperi=$this->ls_peractnom;
		$ls_codtipdoc=$_SESSION["la_nomina"]["tipdocpagperche"];
		$ls_sql= "DELETE ".
				 " FROM sno_rd ".
				 "WHERE codemp = '".$ls_codemp."' ".
				 "  AND codnom = '".$ls_codnom."' ".
				 "  AND codperi  = '".$ls_codperi."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_delete_sno_rd ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_sno_rd
	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_chequear_beneficiario($as_codper,&$as_ctahaber)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_chequear_beneficiario
		//	    Arguments: $as_codper // código del personal
		//                 $as_ctahaber // cuenta del beneficiario
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de verificar si el personal existe como benficiario y buscar
		//                 su cuenta contable
	    //     Creado por: Ing. María Beatriz Unda
	    // Fecha Creación: 22/12/2009
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
 		$as_ctahaber="";
		$ls_sql="SELECT sc_cuenta ".
		        "  FROM rpc_beneficiario, sno_personal ".
				" WHERE rpc_beneficiario.codemp='".$this->ls_codemp."'  ". 
				"   AND sno_personal.codper='".$as_codper."'            ".
			    "   AND sno_personal.codemp=rpc_beneficiario.codemp     ".
				"   AND sno_personal.cedper=rpc_beneficiario.ced_bene   ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_chequear_beneficiario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if (!$rs_data->EOF)
			{		 
				$as_ctahaber=$rs_data->fields["sc_cuenta"];				
			}
			else
			{
				$this->io_mensajes->message("El personal ".$as_codper." no se encuentra registrado como beneficiario. No se puede realizar la recepción de Documento al personal");
				$lb_valido=false;
			}
		}	
		return $lb_valido;
	}// end function uf_chequear_beneficiario
	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_sno_rd($as_codper,$ad_monpagper,$as_sc_cuenta,$as_debhab)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_sno_rd
		//	    Arguments: $as_codper // código del personal
		//                 $ad_monpagper // monto de la recepción de documento
	    //                 $as_sc_cuenta // cuenta contable para la recepción de documentos
		//                 $as_debhab // tipo de movimiento (debe -  haber)
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de verificar si el personal existe como benficiario y buscar
		//                 su cuenta contable
	    //     Creado por: Ing. María Beatriz Unda
	    // Fecha Creación: 22/12/2009
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_codemp=$this->ls_codemp;
		$ls_codnom=$this->ls_codnom;
		$ls_codperi=$this->ls_peractnom;
		$ls_codtipdoc=$_SESSION["la_nomina"]["tipdocpagperche"];
		
		$ls_sql=" INSERT INTO sno_rd (codemp, codnom, codperi, codper, sc_cuenta, debhab,monpagper,codtipdoc, estcon) ".
		        " VALUES ('".$ls_codemp."', '".$ls_codnom."', '".$ls_codperi."', '".$as_codper."' , ".
				"         '".$as_sc_cuenta."', '".$as_debhab."', '".$ad_monpagper."', '".$ls_codtipdoc."','0' ) ";	
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_insert_sno_rd ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_sno_rd
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_contabilizacion_spg($as_codcom,$as_tipnom,$as_programatica,$as_estcla,$as_cueprecon,
	                                        $as_operacionnomina,$as_codconc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contabilizacion_spg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_programatica  //  Programática
		//	    		   as_cueprecon  //  cuenta presupuestaria
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que busca si el registro  existe en sno_dt_spg
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
		$ls_codestpro1=substr($as_programatica,0,25);
		$ls_codestpro2=substr($as_programatica,25,25);
		$ls_codestpro3=substr($as_programatica,50,25);
		$ls_codestpro4=substr($as_programatica,75,25);
		$ls_codestpro5=substr($as_programatica,100,25);			
		$as_codcom=substr($as_codcom,2,15);
		$concat="P-";
		$ls_sql="SELECT codcom FROM sno_dt_spg ".
		        " WHERE codemp= '0001' ".
				"  AND  codnom= '".$ls_codnom."' ".
				"  AND  codperi='".$ls_codperi."' ".
				"  AND  codcom= '".$concat.$as_codcom."' ".
				"  AND  codestpro1= '".$ls_codestpro1."' ".
				"  AND  codestpro2= '".$ls_codestpro2."' ".
				"  AND  codestpro3= '".$ls_codestpro3."' ".
				"  AND  codestpro4= '".$ls_codestpro4."' ".
				"  AND  codestpro5= '".$ls_codestpro5."' ". 
				"  AND  estcla= '".$as_estcla."' ".
				"  AND  spg_cuenta = '".$as_cueprecon."' ".
				"  AND  operacion= '".$as_operacionnomina."'  ".
				"  AND  codconc= '".$as_codconc."' ";	
		$rs_data=$this->io_sql_destino->select($ls_sql);
		if($rs_data===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->traspaso_concepto_aporte MÉTODO->uf_select_contabilizacion_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
		}
		else
		{
			if ($rs_data->RecordCount()==0)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_contabilizacion_spg
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_contabilizacion_spg($as_codcom,$as_tipnom,$as_programatica,$as_estcla,$as_cueprecon,
	                                        $as_operacionnomina,$as_codconc,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_contabilizacion_spg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_programatica  //  Programática
		//	    		   as_cueprecon  //  cuenta presupuestaria
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualiza el total des las cuentas presupuestarias
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
		$ls_codestpro1=substr($as_programatica,0,25);
		$ls_codestpro2=substr($as_programatica,25,25);
		$ls_codestpro3=substr($as_programatica,50,25);
		$ls_codestpro4=substr($as_programatica,75,25);
		$ls_codestpro5=substr($as_programatica,100,25);			
	
		$ls_sql="UPDATE  sno_dt_spg ".
		        " SET monto = (monto + ".$ai_monto.") ".
		        " WHERE codemp= '".$this->ls_codemp."' ".
				"  AND  codnom= '".$this->ls_codnom."' ".
				"  AND  codperi='".$this->ls_peractnom."' ".
				"  AND  codcom= '".$as_codcom."' ".
				"  AND  codestpro1= '".$ls_codestpro1."' ".
				"  AND  codestpro2= '".$ls_codestpro2."' ".
				"  AND  codestpro3= '".$ls_codestpro3."' ".
				"  AND  codestpro4= '".$ls_codestpro4."' ".
				"  AND  codestpro5= '".$ls_codestpro5."' ". 
				"  AND  estcla= '".$as_estcla."' ".
				"  AND  spg_cuenta = '".$as_cueprecon."' ".
				"  AND  operacion= '".$as_operacionnomina."'  ".
				"  AND  codconc= '".$as_codconc."' ";	
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_update_contabilizacion_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		
		return $lb_valido;
	}// end function uf_update_contabilizacion_spg
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_contabilizacion_scg($as_tipnom,$as_cuenta,$as_operacion,$as_codcom,$as_codconc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contabilizacion_scg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacion  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación		
		//	    		   as_cuenta  //  cuenta contable
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que busca si el registro  existe en sno_dt_scg
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$as_codcom=substr($as_codcom,2,15);
		$concat="P-";
		$ls_sql="SELECT codcom FROM sno_dt_scg ".
		        " WHERE codemp= '0001' ".
				"  AND     codnom= '".$ls_codnom."' ".
				"  AND     codperi='".$ls_codperi."' ".
				"  AND     codcom= '".$concat.$as_codcom."' ".
				"  AND     tipnom= '".$as_tipnom."' ".				
				"  AND     sc_cuenta = '".$as_cuenta."' ".
				"  AND     debhab= '".$as_operacion."'  ".
				"  AND     codconc= '".$as_codconc."' ";	
		$rs_data=$this->io_sql_destino->select($ls_sql);
		if($rs_data===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_select_contabilizacion_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
		}
		else
		{
			if ($rs_data->RecordCount()==0)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_contabilizacion_scg
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_contabilizacion_scg($as_tipnom,$as_cuenta,$as_operacion,$as_codcom,$as_codconc,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_contabilizacion_scg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacion  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación		
		//	    		   as_cuenta  //  cuenta contable
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualiza el total de las cuentas contables
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
			
	
		$ls_sql="UPDATE  sno_dt_scg ".
		        " SET monto = (monto + ".$ai_monto.") ".
		         " WHERE codemp= '".$this->ls_codemp."' ".
				"    AND   codnom= '".$this->ls_codnom."' ".
				"    AND   codperi='".$this->ls_peractnom."' ".
				"    AND   codcom= '".$as_codcom."' ".
				"    AND   tipnom= '".$as_tipnom."' ".				
				"    AND   sc_cuenta = '".$as_cuenta."' ".
				"    AND   debhab= '".$as_operacion."'  ".
				"    AND   codconc= '".$as_codconc."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_update_contabilizacion_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		
		return $lb_valido;
	}// end function uf_update_contabilizacion_scg
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_contabilizacion_spi($as_codcom,$as_tipnom,$as_cuenta,$as_operacionnomina,$as_codconc,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contabilizacion_spi
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacion  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación		
		//	    		   as_cuenta  //  cuenta contable
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que busca si el registro  existe en sno_dt_scg
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$as_codcom=substr($as_codcom,2,15);
		$concat="P-";
		$lb_valido=true;
				
		$ls_sql="SELECT codcom FROM sno_dt_spi ".
		        " WHERE codemp= '0001' ".
				"   AND    codnom= '".$ls_codnom."' ".
				"   AND    codperi='".$ls_codperi."' ".
				"   AND    codcom= '".$concat.$as_codcom."' ".
				"   AND    tipnom= '".$as_tipnom."' ".
				"   AND    codestpro1= '".$as_codestpro1."' ".
				"   AND    codestpro2= '".$as_codestpro2."' ".
				"   AND    codestpro3= '".$as_codestpro3."' ".
				"   AND    codestpro4= '".$as_codestpro4."' ".
				"   AND    codestpro5= '".$as_codestpro5."' ". 
				"   AND    estcla= '".$as_estcla."' ".
				"   AND    spi_cuenta = '".$as_cueprecon."' ".
				"   AND    operacion= '".$as_operacionnomina."'  ".
				"   AND    codconc= '".$as_codconc."' ";	
		$rs_data=$this->io_sql_destino->select($ls_sql);
		if($rs_data===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Traspaso Concepto Aporte MÉTODO->uf_select_contabilizacion_spi ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
		}
		else
		{
			if ($rs_data->RecordCount()==0)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_contabilizacion_spi
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_contabilizacion_spi($as_codcom,$as_tipnom,$as_cuenta,$as_operacionnomina,$as_codconc,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_contabilizacion_spi
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacion  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación		
		//	    		   as_cuenta  //  cuenta contable
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualiza el total de las cuentas contables
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		
		$ls_sql="UPDATE  sno_dt_spi ".
		        " SET monto = (monto + ".$ai_monto.") ".
		        " WHERE codemp= '".$this->ls_codemp."' ".
				"   AND    codnom= '".$this->ls_codnom."' ".
				"   AND    codperi='".$this->ls_peractnom."' ".
				"   AND    codcom= '".$as_codcom."' ".
				"   AND    tipnom= '".$as_tipnom."' ".
				"   AND    codestpro1= '".$as_codestpro1."' ".
				"   AND    codestpro2= '".$as_codestpro2."' ".
				"   AND    codestpro3= '".$as_codestpro3."' ".
				"   AND    codestpro4= '".$as_codestpro4."' ".
				"   AND    codestpro5= '".$as_codestpro5."' ". 
				"   AND    estcla= '".$as_estcla."' ".
				"   AND    spi_cuenta = '".$as_cueprecon."' ".
				"   AND    operacion= '".$as_operacionnomina."'  ".
				"   AND    codconc= '".$as_codconc."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_update_contabilizacion_spi ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		
		return $lb_valido;
	}// end function uf_update_contabilizacion_spi
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>
