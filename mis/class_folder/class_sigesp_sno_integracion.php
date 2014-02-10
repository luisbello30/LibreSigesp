<?php
 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 //       Class : class_sigesp_sno_integracion_php                                                     //    
 // Description : Esta clase tiene todos los metodos necesario para el manejo de la rutina integradora //
 //               de las nominas y aportes															   //
 ////////////////////////////////////////////////////////////////////////////////////////////////////////
class class_sigesp_sno_integracion
{
	//Instancia de la clase funciones.
    var $is_msg_error;
	var $dts_empresa; 
	var $dts_nomina;
	var $dts_banco;
	var $dts_nomina_aporte;
	var $obj="";
	var $io_sql;
	var $io_siginc;
	var $io_conect;
	var $io_function;	
    var $io_sigesp_int;
	var $io_sigesp_int_spg;
	var $io_sigesp_int_scg;	
	var $io_fecha;
	var $io_msg;
	var $is_codemp="";
	var $is_procede="";
	var $is_mensaje_spi="";	
	var $is_mensaje_spg="";	
	var $is_comprobante;
	var $idt_fecha;
    var	$is_tiponomina;

	//-----------------------------------------------------------------------------------------------------------------------------------
	function class_sigesp_sno_integracion()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: class_sigesp_sno_integracion
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/class_sql.php");  
		require_once("../shared/class_folder/class_datastore.php");
		require_once("../shared/class_folder/sigesp_include.php");
		require_once("../shared/class_folder/class_funciones.php");
		require_once("../shared/class_folder/class_sigesp_int.php");
		require_once("../shared/class_folder/class_sigesp_int_int.php");
		require_once("../shared/class_folder/class_sigesp_int_spg.php");
		require_once("../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../shared/class_folder/class_sigesp_int_spi.php");
		require_once("../shared/class_folder/class_fecha.php");
		require_once("../shared/class_folder/class_mensajes.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		require_once("class_funciones_mis.php");
	    $this->io_fun_mis=new class_funciones_mis();
	    $this->io_fecha=new class_fecha();
        $this->io_sigesp_int=new class_sigesp_int_int();
		$this->io_function=new class_funciones() ;
		$this->io_siginc=new sigesp_include();
		$this->io_connect=$this->io_siginc->uf_conectar();
		$this->io_sql=new class_sql($this->io_connect);		
		$this->obj=new class_datastore();
		$this->dts_empresa=$_SESSION["la_empresa"];
		$this->is_codemp=$this->dts_empresa["codemp"];		
		$this->ls_codemp=$this->dts_empresa["codemp"];		
		$this->dts_nomina=new class_datastore();
		$this->dts_banco=new class_datastore();
		$this->dts_nomina_aporte=new class_datastore();
		$this->io_msg=new class_mensajes();		
		$this->io_sigesp_int_spg=new class_sigesp_int_spg();
		$this->io_sigesp_int_scg=new class_sigesp_int_scg();		
		$this->io_seguridad=new sigesp_c_seguridad();		
		$this->as_procede="";
		$this->as_comprobante="";
		$this->ad_fecha="";
		$this->as_codban="";
		$this->as_ctaban="";
	}// end function class_sigesp_sno_integracion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destroy_objects()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destroy_objects
		//		   Access: public 
		//	  Description: Destructor de los objectos de la Clase
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if( is_object($this->io_fecha) ) { unset($this->io_fecha);  }
		if( is_object($this->io_sigesp_int) ) { unset($this->io_sigesp_int);  }
		if( is_object($this->io_function) ) { unset($this->io_function);  }
		if( is_object($this->io_siginc) ) { unset($this->io_siginc);  }
		if( is_object($this->io_connect) ) { unset($this->io_connect);  }
		if( is_object($this->io_sql) ) { unset($this->io_sql);  }	   
		if( is_object($this->obj) ) { unset($this->obj);  }	   
		if( is_object($this->dts_empresa) ) { unset($this->dts_empresa);  }	   
		if( is_object($this->dts_nomina) ) { unset($this->dts_nomina);  }	   	   
		if( is_object($this->dts_banco) ) { unset($this->dts_banco);  }	   	   
		if( is_object($this->dts_nomina_aporte) ) { unset($this->dts_nomina_aporte);  }	   
		if( is_object($this->io_msg) ) { unset($this->io_msg);  }	   
		if( is_object($this->io_sigesp_int_spg) ) { unset($this->io_sigesp_int_spg);  }	   
		if( is_object($this->io_sigesp_int_scg) ) { unset($this->io_sigesp_int_scg);  }	   
		if( is_object($this->io_seguridad) ) { unset($this->io_seguridad);  }	   
	}// end function uf_destroy_objects
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci�n a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT value ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->SNO M�TODO->uf_select_config ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["value"];
				$li_i=$li_i+1;
			}
			if($li_i==0)
			{
				$lb_valido=$this->uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
				if ($lb_valido)
				{
					$ls_valor=$this->uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
				}
			}
			$this->io_sql->free_result($rs_data);		
		}
		return rtrim($ls_valor);
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci�n a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $lb_valido True si se ejecuto el insert � False si hubo error en el insert
		//	  Description: Funci�n que inserta la variable de configuraci�n
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="DELETE ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_msg->message("CLASE->SNO M�TODO->uf_insert_config ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			//$this->io_mensajes->message("CLASE->SNO M�TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			switch ($as_tipo)
			{
				case "C"://Caracter
					$valor = $as_valor;
					break;

				case "D"://Double
					$as_valor=str_replace(".","",$as_valor);
					$as_valor=str_replace(",",".",$as_valor);
					$valor = $as_valor;
					break;

				case "B"://Boolean
					$valor = $as_valor;
					break;

				case "I"://Integer
					$valor = intval($as_valor);
					break;
			}
			$ls_sql="INSERT INTO sigesp_config(codemp, codsis, seccion, entry, value, type)VALUES ".
					"('".$this->ls_codemp."','".$as_sistema."','".$as_seccion."','".$as_variable."','".$valor."','".$as_tipo."')";
					
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_msg->message("CLASE->SNO M�TODO->uf_insert_config ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
				//$this->io_mensajes->message("CLASE->SNO M�TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
			else
			{
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	}// end function uf_insert_config	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_contabilizacion_nomina($as_comprobante,$adt_fecha,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_nomina
		//		   Access: public (sigesp_mis_p_contabiliza_sno.php)
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   adt_fecha  // Fecha de contabilizaci�n
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto la contabilizaci�n correctamente
		//	  Description: Funcion que procesa la contabilizaci�n dado un comprobante
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
	    if(!$this->uf_obtener_data_comprobante($as_comprobante))
		{
			return false;
		}
        $li_estred=$this->dts_nomina->getValue("estrd",1);  
		$ls_nomina=$this->dts_nomina->getValue("codnom",1); 
		$ls_periodo=$this->dts_nomina->getValue("codperi",1);  
		$this->is_tiponomina = $this->dts_nomina->getValue("tipnom",1);  
		switch($this->is_tiponomina)
		{
			case "N":
				// Si la contabilizaci�n es para las n�minas
				if($li_estred==0)
				{	// Si la contabilizaci�n es Normal
					$lb_valido=$this->uf_procesar_contabilizacion_tipo_nomina($as_comprobante,$adt_fecha,$aa_seguridad);
				}
				else
				{	// Si se genera una recepci�n de documentos
					$lb_valido = $this->uf_procesar_recepcion_documento_tipo_nomina($as_comprobante,$adt_fecha,$aa_seguridad);
				}
				
			break;
			case "A":
				// Si la contabilizaci�n es para los aportes
				if($li_estred==0)
				{	// Si la contabilizaci�n es Normal
					$lb_valido=$this->uf_procesar_nomina_aportes($as_comprobante,$adt_fecha,$ls_nomina,$ls_periodo,$aa_seguridad);
				}
				else
				{	// Si se genera una recepci�n de documentos
					$lb_valido=$this->uf_procesar_recepcion_documento_tipo_aporte($as_comprobante,$adt_fecha,$ls_nomina,$ls_periodo,$aa_seguridad);
				}
			break;
			case "I":
				// Si la contabilizaci�n es para los ingresos
				$lb_valido=$this->uf_procesar_contabilizacion_ingresos($as_comprobante,$adt_fecha,$aa_seguridad);
			break;
			case "P":
				// Si la contabilizaci�n es para la Prestaci�n Antiguedad
				if($li_estred==0)
				{	// Si la contabilizaci�n es Normal
					$lb_valido=$this->uf_procesar_contabilizacion_tipo_nomina($as_comprobante,$adt_fecha,$aa_seguridad);
				}
				else
				{	// Si se genera una recepci�n de documentos
					$lb_valido = $this->uf_procesar_recepcion_documento_tipo_nomina($as_comprobante,$adt_fecha,$aa_seguridad);
				}
			break;
		}
        return $lb_valido;		
    } // end function uf_procesar_contabilizacion_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_data_comprobante($as_comprobante)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_data_comprobante
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//	      Returns: lb_existe True si existe el comprobante
		//	  Description: Este metodo que obtiene la informaci�n agrupada en un registro con la informaci�n 
		//                  de cabecera de proveedor,beneficiario tipo destino y descripcion 
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_existe = false;		
		$ls_sql="SELECT sno_dt_spg.tipnom, sno_dt_spg. codperi, sno_dt_spg.codnom, sno_dt_spg.cod_pro, sno_dt_spg.ced_bene,".
				"		sno_dt_spg.tipo_destino, sno_dt_spg.descripcion, sno_dt_spg.operacion, sno_dt_spg.estatus, ".
				"		sno_dt_spg.codtipdoc,sno_dt_spg.estrd,sno_dt_spg.estnotdeb, sno_hnomina.cueconnom, sno_hnomina.anocurnom ".
                "  FROM sno_dt_spg, sno_hnomina ".
                " WHERE sno_dt_spg.codemp='".$this->is_codemp."' ".
				"	AND sno_dt_spg.codcom='".$as_comprobante."' ".
				"   AND sno_dt_spg.codemp = sno_hnomina.codemp ".
				"	AND sno_dt_spg.codnom = sno_hnomina.codnom ".
				"	AND sno_dt_spg.codperi = sno_hnomina.peractnom ".
				" UNION ".	
				"SELECT sno_dt_spi.tipnom, sno_dt_spi.codperi, sno_dt_spi.codnom, sno_dt_spi.cod_pro, sno_dt_spi.ced_bene,".
				"		sno_dt_spi.tipo_destino, sno_dt_spi.descripcion, sno_dt_spi.operacion, sno_dt_spi.estatus, ".
				"		sno_dt_spi.codtipdoc,sno_dt_spi.estrd,sno_dt_spi.estnotdeb, sno_hnomina.cueconnom, sno_hnomina.anocurnom ".
                "  FROM sno_dt_spi, sno_hnomina ".
                " WHERE sno_dt_spi.codemp='".$this->is_codemp."' ".
				"	AND sno_dt_spi.codcom='".$as_comprobante."' ".
				"   AND sno_dt_spi.codemp = sno_hnomina.codemp ".
				"	AND sno_dt_spi.codnom = sno_hnomina.codnom ".	
				"	AND sno_dt_spi.codperi = sno_hnomina.peractnom ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
            $this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_obtener_data_comprobante ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=true;			
                $this->dts_nomina->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$ls_sql="SELECT tipnom,codperi,codnom,cod_pro,ced_bene,tipo_destino,descripcion,'' AS operacion,estatus,codtipdoc,estrd,estnotdeb ".
						"  FROM sno_dt_scg ".
						" WHERE codemp='".$this->is_codemp."' ".
						"	AND codcom='".$as_comprobante."' ";				  
				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{ 
					$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_obtener_data_comprobante ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					return false;
				}
				else
				{                 
					if($row=$this->io_sql->fetch_row($rs_data))
					{
						$lb_existe=true;			
						$this->dts_nomina->data=$this->io_sql->obtener_datos($rs_data);
					}
					else
					{
		            	$this->io_msg->message("ERROR-> El Comprobante ".$as_comprobante." no existe.");			
					}
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_existe;
	} // end function uf_obtener_data_comprobante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_contabilizacion_tipo_nomina($as_comprobante,$adt_fecha,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_tipo_nomina
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   adt_fecha  // Fecha de contabilizaci�n
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto la contabilizaci�n correctamente
		//	  Description: Funcion que procesa la contabilizaci�n de una N�mina
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $ls_procede="SNOCNO";
		 $this->is_procede="SNOCNO";
		$ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante(trim($as_comprobante));
		$ls_cod_pro=$this->dts_nomina->getValue("cod_pro",1);	
		$ls_ced_bene=$this->dts_nomina->getValue("ced_bene",1);	
		$ls_descripcion=$this->dts_nomina->getValue("descripcion",1);	
        $ls_tipo_destino=$this->dts_nomina->getValue("tipo_destino",1);			
        $ls_mensaje=$this->dts_nomina->getValue("operacion",1);
		$li_estatus=$this->dts_nomina->getValue("estatus",1);
		$ls_nomina=$this->dts_nomina->getValue("codnom",1); 
		$ls_periodo=$this->dts_nomina->getValue("codperi",1);  
        $ls_tipnom=$this->dts_nomina->getValue("tipnom",1);  
        $li_estnotdeb=$this->dts_nomina->getValue("estnotdeb",1);
        if($ls_tipo_destino=="B")
		{
			$ls_codigo_destino=$ls_ced_bene;
		}
		if($ls_tipo_destino=="P")
		{
			$ls_codigo_destino=$ls_cod_pro;
		}
		if($ls_tipo_destino=="-")
		{
			$ls_codigo_destino="----------";
		}
		if($li_estatus==1) 
		{
		   $this->io_msg->message("La N�mina debe estar en estatus EMITIDA para su contabilizaci�n.");
		   return false;
		}
		$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);
		// Creo la cabecera del Comprobante
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
		$this->as_procede=$ls_procede;
		 $this->as_comprobante=$ls_comprobante;
		$this->ad_fecha=$adt_fecha;
		$this->as_codban=$ls_codban;
		$this->as_ctaban=$ls_ctaban;//raq
		$lb_valido=$this->io_sigesp_int->uf_int_init($this->is_codemp,$ls_procede,$ls_comprobante,$adt_fecha,$ls_descripcion,
													 $ls_tipo_destino,$ls_codigo_destino,false,$ls_codban,$ls_ctaban,$li_tipo_comp);
		
		$this->io_sigesp_int->uf_int_config(false,false);
		if (!$lb_valido)
		{   
		   $this->io_msg->message($this->io_sigesp_int->is_msg_error); 
		   return false;		   		   
		}
		
		// inicia transacci�n SQL
		$this->io_sigesp_int->uf_int_init_transaction_begin();
        if ($li_estnotdeb==1)
		{
			  //echo "nota debito";
			// si se Genera una nota de D�bito al Banco	
			$lb_valido=$this->uf_generar_nota_debito_banco($ls_nomina,$ls_periodo,$as_comprobante,$ls_descripcion,$adt_fecha,
														   1,$ls_tipo_destino,$ls_cod_pro,$ls_ced_bene,$aa_seguridad);
		}
		//echo " no nota debito";
		if($lb_valido)
		{
		
		    //  echo " Se procesan los detalles de presupuesto";
		      // Se procesan los detalles de presupuesto
			$lb_valido=$this->uf_procesar_detalles_gasto($as_comprobante,"");  
		}
		 
		if ($lb_valido)
        {	// Se procesan los detalles de Contabilidad
			$lb_valido = $this->uf_procesar_detalles_contables($as_comprobante,""); 

			
			if ($lb_valido)
			{	// Se inserta el comprobante con sus detalles contables y presupuestarios
				$lb_valido = $this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				 
				if (!$lb_valido) 
				{ 
					if (!empty($this->io_sigesp_int->is_msg_error))
					{
						$this->io_msg->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
					}	
				}
			}
			if($lb_valido)
			{	// Se Actualiza el estatus de la n�mina que est� contabilizada
				$lb_valido=$this->uf_update_estatus_nomina($ls_periodo,$ls_nomina,$as_comprobante,$ls_tipnom,1);
			}
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sno($this->is_codemp,$ls_nomina,$ls_periodo,$ls_comprobante,
															    $adt_fecha,'1900-01-01');
		}
		$lb_pagodirecto=$this->uf_select_config("SNO", "CONFIG", "PAGO_DIRECTO_PERSONAL_CHEQUE", "", "C");
		if($lb_pagodirecto=="1")
		{
			$lb_valido=$this->uf_generar_pago_directo_personal_cheque($ls_nomina,$ls_periodo,$adt_fecha,$aa_seguridad);		
		}
		else
		{
			$li_genrd=$this->uf_select_config_nomina($ls_nomina);
			if(($li_genrd=="1")&&($lb_valido))
			{
				$lb_valido=$this->uf_generar_recepcion_documento_personal_cheque($ls_nomina,$ls_periodo,$adt_fecha,$aa_seguridad);
			}
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Contabiliz� la N�mina <b>".$ls_nomina."</b>, Per�odo <b>".$ls_periodo."</b>, ".
							"Comprobante <b>".$ls_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/// PARA LA CONVERSI�N MONETARIA
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/*if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_sigespcmp($this->as_procede,$this->as_comprobante,$this->ad_fecha,
																 $this->as_codban,$this->as_ctaban,$aa_seguridad);
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_spgdtcmp($this->as_procede,$this->as_comprobante,$this->ad_fecha,
																$this->as_codban,$this->as_ctaban,$aa_seguridad);
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_scgdtcmp($this->as_procede,$this->as_comprobante,$this->ad_fecha,
																$this->as_codban,$this->as_ctaban,$aa_seguridad);
		}*/
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Se Finaliza la transacci�n con Commit � Rollback de acuerdo al $lb_valido
		if($lb_valido)
		{
			$ls_guarderia=trim($this->uf_select_configuracion("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO GUARDERIA","I","0"));				
			if($ls_guarderia==1)
			{
				$lb_valido = $this->uf_procesar_recepcion_documento_guarderias($as_comprobante,$adt_fecha,$aa_seguridad);
			}
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);		
		return  $lb_valido;
	} // end function uf_procesar_contabilizacion_tipo_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generar_nota_debito_banco($as_nomina,$as_periodo,$as_comprobante,$as_descripcion,$adt_fecha,$ai_process,
									      $as_tipo_destino,$as_cod_pro,$as_ced_bene,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generar_nota_debito_banco
		//		   Access: private
		//	    Arguments: as_nomina  // C�digo de la N�mina
		//				   as_periodo  // C�digo del Per�odo
		//				   as_comprobante  // N�mero de Comprobante
		//				   as_descripcion  // Descripci�n de la Contabilizaci�n
		//				   adt_fecha  // Fecha de contabilizaci�n
		//				   ai_process  // Proceso si se va a insertar � a eliminar
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto la contabilizaci�n correctamente
		//	  Description: Funcion que procesa la contabilizaci�n de una N�mina
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
        if(!$this->uf_obtener_data_banco($as_nomina,$as_periodo)) 
		{ 
		   return false; 
		}
		$ls_codban=$this->dts_banco->getValue("codban",1); 
		$ls_ctaban=$this->dts_banco->getValue("codcueban",1); 
	    $ls_sc_cuenta=$this->dts_banco->getValue("codcuecon",1); 	
		if ($ai_process==1) // contabilizar (insert mov. banco)
		{
			$ldec_monto=$this->uf_obtener_suma_banco($as_comprobante,$ls_sc_cuenta);
			if($ldec_monto!=0)
			{
				$lb_valido=$this->uf_insert_movimiento_banco($ls_codban,$ls_ctaban,$as_comprobante,$adt_fecha,$as_descripcion,
															 $ldec_monto,$as_tipo_destino,$as_cod_pro,$as_ced_bene);
				if($lb_valido)
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="INSERT";
					$ls_descripcion="Gener� Nota de D�bito N�mina <b>".$as_nomina."</b>, Per�odo <b>".$as_periodo."</b>, ".
									"Comprobante <b>".$as_comprobante."</b>, Banco <b>".$ls_codban."</b>, ".
									"Cuenta Banco <b>".$ls_ctaban."</b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////
				}
			}
		}
		else // reversar (delete mov. banco)
		{
		    $lb_valido=$this->uf_delete_movimiento_banco($ls_codban,$ls_ctaban,$as_comprobante);
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="DELETE";
				$ls_descripcion="Elimin� Nota de D�bito N�mina <b>".$as_nomina."</b>, Per�odo <b>".$as_periodo."</b>, ".
								"Comprobante <b>".$as_comprobante."</b>, Banco <b>".$ls_codban."</b>, ".
								"Cuenta Banco <b>".$ls_ctaban."</b>";
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
			}
		}
		return $lb_valido;
	} // end function uf_generar_nota_debito_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_data_banco($as_nomina,$as_periodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_data_banco
		//		   Access: private
		//	    Arguments: as_nomina  // C�digo de la N�mina
		//				   as_periodo  // C�digo del Per�odo
		//	      Returns: lb_valido True si se encuentra la informaci�n de banco
		//	  Description: Este metodo obtiene la cuenta, banco y codigo contable
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_existe=false;		
		$ls_sql="SELECT codemp, codnom, codperi, codban, codcueban, codcuecon ".
                "  FROM sno_banco ".
                " WHERE codemp='".$this->is_codemp."' ".
				"   AND codnom='".$as_nomina."' ".
				"   AND codperi='".$as_periodo."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_obtener_data_banco ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=true; // si existe se procedera a registrar en el datastore.				
                $this->dts_banco->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$this->io_msg->message("ERROR->No existe data para generar la Nota de D�bito.");
			}
		}
		$this->io_sql->free_result($rs_data);
		return $lb_existe;
    } // end function uf_obtener_data_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_suma_banco($as_comprobante,$as_cuenta_banco)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_suma_banco
		//		   Access: private
		//	    Arguments: as_comprobante  // N�mero de Comprobante
		//				   as_cuenta_banco  // Cuenta del Banco
		//	      Returns: el monto por el que se va a hacer la n�ta de cr�dito
		//	  Description: Este metodo obtiene la cuenta, banco y codigo contable
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=false;		
		$ldec_monto=0;
		$ls_sql="SELECT SUM(monto) as monto ".
                "  FROM sno_dt_scg ".
                " WHERE codemp='".$this->is_codemp."' ".
				"   AND codcom='".$as_comprobante."' ".
				"   AND debhab='H' ".
				"   AND sc_cuenta='".$as_cuenta_banco."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_obtener_suma_banco ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			while($row=$this->io_sql->fetch_row($rs_data))  
			{ 
				$ldec_monto=round($row["monto"],2); 
			}
		}
		$this->io_sql->free_result($rs_data);
		return $ldec_monto;
	}  // end function uf_obtener_suma_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_suma_proveedor_beneficiario($as_comprobante)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_suma_proveedor_beneficiario
		//		   Access: private
		//	    Arguments: as_comprobante  // N�mero de Comprobante
		//				   as_cuenta_banco  // Cuenta del Banco
		//	      Returns: el monto por el que se va a hacer la n�ta de cr�dito
		//	  Description: Este metodo obtiene la cuenta, banco y codigo contable
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=false;		
		$ldec_monto=0;
		$ls_sql="SELECT SUM(monto) as monto ".
                "  FROM sno_dt_scg, rpc_beneficiario ".
                " WHERE sno_dt_scg.codemp='".$this->is_codemp."' ".
				"   AND sno_dt_scg.codcom='".$as_comprobante."' ".
				"   AND sno_dt_scg.debhab='H' ".
				"   AND sno_dt_scg.tipo_destino='B' ".
				"   AND sno_dt_scg.codemp = rpc_beneficiario.codemp ".
				"   AND sno_dt_scg.ced_bene = rpc_beneficiario.ced_bene ".
				"   AND sno_dt_scg.sc_cuenta = rpc_beneficiario.sc_cuenta ".
				" UNION ".
				"SELECT SUM(monto) as monto ".
                "  FROM sno_dt_scg, rpc_proveedor ".
                " WHERE sno_dt_scg.codemp='".$this->is_codemp."' ".
				"   AND sno_dt_scg.codcom='".$as_comprobante."' ".
				"   AND sno_dt_scg.debhab='H' ".
				"   AND sno_dt_scg.tipo_destino='P' ".
				"   AND sno_dt_scg.codemp = rpc_proveedor.codemp ".
				"   AND sno_dt_scg.cod_pro = rpc_proveedor.cod_pro ".
				"   AND sno_dt_scg.sc_cuenta = rpc_proveedor.sc_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_obtener_suma_proveedor_beneficiario ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			while($row=$this->io_sql->fetch_row($rs_data))  
			{ 
				$ldec_monto=$ldec_monto+round($row["monto"],2); 
			}
		}
		$this->io_sql->free_result($rs_data);
		return $ldec_monto;
	}  // end function uf_obtener_suma_proveedor_beneficiario
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_movimiento_banco($as_codban,$as_ctaban,$as_comprobante,$adt_fecha,$as_descripcion,$adec_monto,
										$as_tipo_destino,$as_cod_pro,$as_ced_bene)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_movimiento_banco
		//		   Access: private
		//	    Arguments: as_codban  // C�digo del Banco
		//				   as_ctaban  // C�digo de la cuenta del banco
		//				   as_comprobante  // N�mero de Comprobante
		//				   adt_fecha  // Fecha de contabilizaci�n
		//				   as_descripcion  // Descripci�n de la Contabilizaci�n
		//				   adec_monto  // Monto de la nota de Cr�dito
		//	      Returns: lb_valido True si se ejecuto la contabilizaci�n correctamente
		//	  Description: Funcion que procesa la contabilizaci�n de una N�mina
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SNOCNO";
		 $ls_sql="INSERT INTO scb_movbco(codemp,codban,ctaban,numdoc,codope,estmov,cod_pro,ced_bene,tipo_destino, codconmov,".
		        "                       fecmov, conmov, nomproben, monto, estbpd, estcon, estcobing, esttra, chevau, estimpche, ".
				"                       monobjret, monret, procede, comprobante, fecha, id_mco, emicheproc, emicheced, emichenom, ".
				"                       emichefec, estmovint, codusu, codopeidb, aliidb, feccon, estreglib, numcarord, numpolcon,".
				"                       coduniadmsig,codbansig,fecordpagsig,tipdocressig,  numdocressig,estmodordpag,codfuefin,".
				"                       forpagsig,medpagsig,codestprosig) ".
				" VALUES ('".$this->is_codemp."','".$as_codban."','".$as_ctaban."','".$as_comprobante."','ND','L','".$as_cod_pro."','".$as_ced_bene."',".
				"         '".$as_tipo_destino."','---','".$adt_fecha."','".$as_descripcion."','Ninguno',".$adec_monto.",".
  				"         'M',0,0,0,' ',0,0,0,' ',' ','1900-01-01',' ',0,' ',' ','1900-01-01',0,'ninguno',".
 				"         ' ',0,'1900-01-01',0,' ',0,' ',' ','1900-01-01',' ',' ',0,' ',' ',' ',' ')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{   
           $this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_movimiento_banco ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
		   return false;
		}
		if($lb_valido)
		{
			$ls_sql="SELECT codemp, codnom, codperi, codcom, tipnom, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, ".
					"		spg_cuenta, operacion, codconc, cod_pro, ced_bene, tipo_destino, descripcion, monto, estatus, estrd, ".
					"		codtipdoc, estnumvou, estnotdeb, codcomapo, estcla ".
					"  FROM sno_dt_spg ".
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND codcom='".$as_comprobante."'";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{   
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_movimiento_banco ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				return false;
			}
			else
			{           
				while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
				{
					$ls_codestpro=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
					$ls_estcla=$row["estcla"];
					$ls_spg_cuenta=$row["spg_cuenta"];
					$ldec_monto=$row["monto"];				
					$ls_operacion=$row["operacion"];				
					$ls_descripcion=$row["descripcion"];				
					$ls_sql="INSERT INTO scb_movbco_spg (codemp, codban, ctaban, numdoc, codope, estmov, codestpro, spg_cuenta, ".
							" documento, operacion, desmov, procede_doc, monto, estcla)  VALUES ('".$this->is_codemp."','".$as_codban."',".
							"'".$as_ctaban."','".$as_comprobante."','ND','L','".$ls_codestpro."','".$ls_spg_cuenta."',".
							"'".$as_comprobante."','".$ls_operacion."','".$ls_descripcion."','".$ls_procede."',".$ldec_monto.",'".$ls_estcla."')";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_movimiento_banco ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
						$lb_valido=false;
						break;
					}
				} // end while
			}
		}
		if($lb_valido)
		{
			$ls_sql="SELECT codemp, codnom, codperi, codcom, tipnom, sc_cuenta, debhab, codconc, cod_pro, ced_bene, ".
					"		tipo_destino, descripcion, monto, estatus, estrd, codtipdoc, estnumvou, estnotdeb, codcomapo ".
					"  FROM sno_dt_scg ".
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND codcom='".$as_comprobante."'";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{   
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_movimiento_banco ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				return false;
			}
			else
			{           
				while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
				{
					$ls_scg_cuenta = $row["sc_cuenta"];
					$ldec_monto = $row["monto"];				
					$ls_debhab = $row["debhab"];				
					$ls_documento = $row["codconc"];								 
					$ls_documento = $this->io_sigesp_int->uf_fill_comprobante(trim($ls_documento));
					$ls_sql="INSERT INTO scb_movbco_scg (codemp, codban, ctaban, numdoc, codope, estmov, scg_cuenta, debhab, codded, ".
							"documento, desmov, procede_doc, monto, monobjret) VALUES ('".$this->is_codemp."','".$as_codban."',".
							"'".$as_ctaban."','".$as_comprobante."','ND','L','".$ls_scg_cuenta."','".$ls_debhab."','00000',".
							"'".$as_comprobante."','".$ls_descripcion."','".$ls_procede."',".$ldec_monto.",0)";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
						$lb_valido=false;
						break;
					}
				} // end while
			}
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/// PARA LA CONVERSI�N MONETARIA
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_seguridad="";
		/*if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_scbmovbco($as_codban,$as_ctaban,$as_comprobante,'ND','L',$aa_seguridad);
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_scbmovbcoscg($as_codban,$as_ctaban,$as_comprobante,'ND','L',$aa_seguridad);
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_scbmovbcospg($as_codban,$as_ctaban,$as_comprobante,'ND','L',$aa_seguridad);
		}*/
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		return $lb_valido;
    } // end function uf_insert_movimiento_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_movimiento_banco($as_codban,$as_ctaban,$as_comprobante)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_movimiento_banco
		//		   Access: private
		//	    Arguments: as_codban  // C�digo del Banco
		//				   as_ctaban  // N�mero de Cuenta
		//				   as_comprobante  // N�mero de Comprobante
		//	      Returns: lb_valido True si se elimin� correctamente
		//	  Description: Este metodo elimina un registro de movimiento de banco asociado a la nota de debito genereado por nomina 
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_sql="DELETE ".
				"  FROM scb_movbco_spg ".
		        " WHERE codemp='".$this->is_codemp."' ".
				"   AND codban='".$as_codban."' ".
				"   AND ctaban='".$as_ctaban."' ".
                "   AND numdoc='".$as_comprobante."' ".
				"   AND codope='ND' ".
				"   AND estmov='L' ".
				"   AND documento='".$as_comprobante."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{   
           $this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_delete_movimiento_banco ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
		   return false;
		}
		if($lb_valido)
		{
			$ls_sql="DELETE ".
					"  FROM scb_movbco_scg ".
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND codban='".$as_codban."' ".
					"   AND ctaban='".$as_ctaban."' ".
					"   AND numdoc='".$as_comprobante."' ".
					"   AND codope='ND' ".
					"   AND estmov='L' ".
					"   AND documento='".$as_comprobante."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{   
			   $this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_delete_movimiento_banco ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			   return false;
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE ".
					"  FROM scb_movbco ".
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND codban='".$as_codban."' ".
					"   AND ctaban='".$as_ctaban."' ".
					"   AND numdoc='".$as_comprobante."' ".
					"   AND codope='ND' ".
					"   AND estmov='L' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{   
			   $this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_delete_movimiento_banco ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			   return false;
			}
		}
		return $lb_valido;
    } // end uf_delete_movimiento_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_detalles_gasto($as_comprobante,$as_codcomapo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_detalles_gasto
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo del comprobante de N�mina
		//				   as_codcomapo  // C�digo del comprobante de Aportes
		//	      Returns: lb_valido True si se insertaron correctamente los detalles en el datastored
		//	  Description: M�todo que recorre la tabla generada por nomina de asientos de gastos para ser
		//                  insertado en el datastore para la integracio�n contable presupuestaria.
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;	
		$ls_criterio="";	
		if ($this->is_tiponomina=="A")
		{
			$ls_criterio=$ls_criterio."	AND codcomapo='".$as_codcomapo."'";
		}		  
		$ls_sql="SELECT codemp, codnom, codperi, codcom, tipnom, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, ".
				"		spg_cuenta, operacion, codconc, cod_pro, ced_bene, tipo_destino, descripcion, monto, estatus, estrd, ".
				"		codtipdoc, estnumvou, estnotdeb, codcomapo, estcla ".
				"  FROM sno_dt_spg ".
				" WHERE codemp='".$this->is_codemp."' ".
				"	AND codcom='".$as_comprobante."' ".
				$ls_criterio;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_procesar_detalles_gasto ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
   	       while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
		   {
				$ls_codestpro1 = $row["codestpro1"];
				$ls_codestpro2 = $row["codestpro2"];
				$ls_codestpro3 = $row["codestpro3"];
				$ls_codestpro4 = $row["codestpro4"];
				$ls_codestpro5 = $row["codestpro5"];
				$ls_estcla = $row["estcla"];
				$ls_spg_cuenta = $row["spg_cuenta"];
				$ls_mensaje = $row["operacion"];
				$ldec_monto = $row["monto"];
				$ls_descripcion = $row["descripcion"];								
				$ls_documento = $row["codconc"];								 
                $ls_documento = $this->io_sigesp_int->uf_fill_comprobante(trim($ls_documento));
				// Insertar el el datastored los detalles de presupuesto
				$lb_valido = $this->io_sigesp_int->uf_spg_insert_datastore($this->is_codemp,$ls_codestpro1,$ls_codestpro2,
																		  $ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
																		  $ls_estcla,$ls_spg_cuenta,$ls_mensaje,$ldec_monto,
																		  $ls_documento,$this->is_procede,$ls_descripcion);
				if (!$lb_valido)
				{  
				   $this->io_msg->message("ERROR->".$this->io_sigesp_int->is_msg_error);
				   break;
				}
		   } // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } //  end function uf_procesar_detalles_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_detalles_ingreso($as_comprobante)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_detalles_ingreso
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo del comprobante de N�mina
		//				   as_codcomapo  // C�digo del comprobante de Aportes
		//	      Returns: lb_valido True si se insertaron correctamente los detalles en el datastored
		//	  Description: M�todo que recorre la tabla generada por nomina de asientos de ingresos para ser
		//                  insertado en el datastore para la integracio�n contable presupuestaria.
		//	   Creado Por: Ing. Yesenia Moreno
		// Modificado Por: Ing. Jennifer Rivero									Fecha �ltima Modificaci�n : 31/10/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;	
		$ls_sql="SELECT spi_cuenta, operacion, descripcion, monto, codconc, ".
		        "       codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla".
				"  FROM sno_dt_spi ".
				" WHERE codemp='".$this->is_codemp."' ".
				"	AND codcom='".$as_comprobante."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_procesar_detalles_ingreso ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
   	       while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
		   {
				$ls_spi_cuenta = $row["spi_cuenta"];
				$ls_mensaje = $row["operacion"];
				$ldec_monto = $row["monto"];
				$ls_descripcion = $row["descripcion"];								
				$ls_documento = $row["codconc"];
				//--------------------------Manejo de Estructuras con el Presuepuesto de Ingresos------------------
				$ls_codestpro1=	$row["codestpro1"];
				$ls_codestpro2=	$row["codestpro2"];
				$ls_codestpro3=	$row["codestpro3"];
				$ls_codestpro4=	$row["codestpro4"];
				$ls_codestpro5=	$row["codestpro5"];
				$ls_estcla    =	$row["estcla"];
				//------------------------------------------------------------------------------------------------							 
                $ls_documento = $this->io_sigesp_int->uf_fill_comprobante(trim($ls_documento));
				// Insertar el el datastored los detalles de ingreso
				$lb_valido=$this->io_sigesp_int->uf_spi_insert_datastore($this->is_codemp,$ls_spi_cuenta,$ls_mensaje,
																		 $ldec_monto,$ls_documento,$this->is_procede,
																		 $ls_descripcion,$ls_codestpro1,$ls_codestpro2,
																		 $ls_codestpro3, $ls_codestpro4, $ls_codestpro5,
																		 $ls_estcla);
				if (!$lb_valido)
				{  
				   $this->io_msg->message("ERROR->".$this->io_sigesp_int->is_msg_error);
				   break;
				}
		   } // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } //  end function uf_procesar_detalles_ingreso
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_detalles_contables($as_comprobante,$as_codcomapo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_detalles_contables
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo del comprobante de N�mina
		//				   as_codcomapo  // C�digo del comprobante de Aportes
		//	      Returns: lb_valido True si se insertaron correctamente los detalles en el datastored
		//	  Description: M�todo que recorre la tabla generada por nomina de asientos contables para ser
		//                  insertado en el datastore para la integracio�n contable.
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_criterio="";	
		if ($this->is_tiponomina=="A")
		{
			$ls_criterio=$ls_criterio."	AND codcomapo='".$as_codcomapo."'";
		}		  
		$ls_sql="SELECT codemp, codnom, codperi, codcom, tipnom, sc_cuenta, debhab, codconc, cod_pro, ced_bene, tipo_destino, ".
				"		descripcion, monto, estatus, estrd, codtipdoc, estnumvou, estnotdeb, codcomapo ".
				"  FROM sno_dt_scg ".
				" WHERE codemp='".$this->is_codemp."' ".
				"	AND codcom='".$as_comprobante."' ".
				$ls_criterio;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_procesar_detalles_contables ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{  $a=0;         
   	       while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
		   {
				$ls_scg_cuenta = $row["sc_cuenta"];
				$ls_mensaje = $row["debhab"];
				$ldec_monto = $row["monto"];				
				$ls_descripcion = $row["descripcion"];				
				$ls_documento = $row["codconc"];								
                $ls_documento = $this->io_sigesp_int->uf_fill_comprobante(trim($ls_documento));
				// Incluimos el detalle de contabilidad en el datastored
				$lb_valido = $this->io_sigesp_int->uf_scg_insert_datastore($this->is_codemp,$ls_scg_cuenta,$ls_mensaje,$ldec_monto,$ls_documento,$this->is_procede,$ls_descripcion);				
				if (!$lb_valido)
				{  
				   $this->io_msg->message("ERROR->".$this->io_sigesp_int->is_msg_error);
				   break;
				}
		   } // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_procesar_detalles_contables
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_nomina($as_periodo,$as_nomina,$as_comprobante,$as_tipo,$ai_estatus)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_nomina
		//		   Access: private
		//	    Arguments: as_periodo  // Per�odo de la N�mina
		//				   as_nomina  // C�digo de la N�mina
		//				   as_comprobante  // C�digo del comprobante 
		//				   as_tipo  // Tipo si es de n�mina � de aportes
		//				   ai_estatus  // estatus si es 0 � 1
		//	      Returns: lb_valido True si se actualiz� correctamente
		//	  Description: M�todo que actualiza el estatus de la nomina en contabilizad o no 
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;	
		$ls_sql="";
		switch($as_tipo)
		{
			case "N":
				$ls_sql="UPDATE sno_periodo ".
						"   SET conper=".$ai_estatus.
						" WHERE codemp='".$this->is_codemp."' ".
						"   AND codnom='".$as_nomina."' ".
						"   AND codperi='".$as_periodo."'";
				break;
			case "A":
				$ls_sql="UPDATE sno_periodo ".
						"   SET apoconper=".$ai_estatus.
						" WHERE codemp='".$this->is_codemp."' ".
						"   AND codnom='".$as_nomina."' ".
						"   AND codperi='".$as_periodo."'";
				break;
			case "I":
				$ls_sql="UPDATE sno_periodo ".
						"   SET ingconper=".$ai_estatus.
						" WHERE codemp='".$this->is_codemp."' ".
						"   AND codnom='".$as_nomina."' ".
						"   AND codperi='".$as_periodo."'";
				break;
			case "P":
				$ls_sql="UPDATE sno_periodo ".
						"   SET fidconper=".$ai_estatus.
						" WHERE codemp='".$this->is_codemp."' ".
						"   AND codnom='".$as_nomina."' ".
						"   AND SUBSTR(CAST(fecdesper as char),6,2)='".substr($as_periodo,1,2)."'";
				break;
        }		
		if($ls_sql!="")
		{			
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_update_estatus_nomina ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		if($lb_valido)
		{
			$ls_sql="UPDATE sno_dt_scg ".
					"   SET estatus=".$ai_estatus.
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND codnom='".$as_nomina."' ".
					"   AND codperi='".$as_periodo."' ".
					"   AND codcom='".$as_comprobante."' ".
					"   AND tipnom='".$this->is_tiponomina."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
	           	$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_update_estatus_nomina ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		if ($lb_valido)
		{
			$ls_sql="UPDATE sno_dt_spg ".
					"   SET estatus=".$ai_estatus.
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND codnom='".$as_nomina."' ".
					"   AND codperi='".$as_periodo."' ".
					"   AND codcom='".$as_comprobante."' ".
					"   AND tipnom='".$this->is_tiponomina."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_update_estatus_nomina ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		if ($lb_valido)
		{
			$ls_sql="UPDATE sno_dt_spi ".
					"   SET estatus=".$ai_estatus.
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND codnom='".$as_nomina."' ".
					"   AND codperi='".$as_periodo."' ".
					"   AND codcom='".$as_comprobante."' ".
					"   AND tipnom='".$this->is_tiponomina."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_update_estatus_nomina ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_update_estatus_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_recepcion_documento_tipo_nomina($as_comprobante,$adt_fecha,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_recepcion_documento_tipo_nomina
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   adt_fecha  // Fecha de contabilizaci�n
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se genero la recepci�n de documento correctamente
		//	  Description: M�todo que registra la contabilizacion solo de nomina en la recepci�n documento
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		$ls_cod_pro = $this->dts_nomina->getValue("cod_pro",1);	
		$ls_ced_bene = $this->dts_nomina->getValue("ced_bene",1);	
        $ls_tipo_destino = $this->dts_nomina->getValue("tipo_destino",1);			
		$ls_nomina = $this->dts_nomina->getValue("codnom",1); 
		$ls_periodo = $this->dts_nomina->getValue("codperi",1);  
        $ls_tipnom = $this->dts_nomina->getValue("tipnom",1);  
		$ls_codtipdoc = $this->dts_nomina->getValue("codtipdoc",1);  
		$ls_descripcion = $this->dts_nomina->getValue("descripcion",1);  
		$adt_fecha = $this->io_function->uf_convertirdatetobd($adt_fecha);
		if($ls_tipnom=="N")
		{
			$ldec_monto=$this->uf_obtener_suma_proveedor_beneficiario($as_comprobante);
			if($ldec_monto<=0)
			{
				$this->io_msg->message("La Cuenta Contable del Proveedor � Beneficiario no esta bien definida.");
				return false;
			}
		}
		else
		{
			$ldec_monto=$this->uf_obtener_total_monto($as_comprobante,"");
		}
		// inicia transacci�n SQL
		$this->io_sigesp_int->uf_int_init_transaction_begin(); 
		// Insertamos la Cabecera
		$ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,".
 		        "montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,fecaprord,usuaprord,".
				"estimpmun,codcla,codrecdoc) VALUES ('".$this->is_codemp."','".$as_comprobante."','".$ls_codtipdoc."','".$ls_ced_bene."',".
				"'".$ls_cod_pro."','".$ls_descripcion."','".$adt_fecha."','".$adt_fecha."','".$adt_fecha."',".$ldec_monto.
				",0,0,'".$ls_tipo_destino."','".$as_comprobante."','R','SNOCNO',0,0,'1900-01-01','',0,'--','".$as_comprobante."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{  
           	$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_procesar_recepcion_documento_tipo_nomina ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		// Insertar los detalles Presupuestarios
		if($lb_valido)
		{	// Insertar los detalles Contables
	        $lb_valido=$this->uf_insert_recepcion_documento_gasto($as_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,"");
		}
		if($lb_valido)
		{	// Insertar los detalles Contables
			$lb_valido=$this->uf_insert_recepcion_documento_contable($as_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,"");
		}
	    if($lb_valido)
		{	// Actualizar el estatus en la n�mina
			$lb_valido=$this->uf_update_estatus_nomina($ls_periodo,$ls_nomina,$as_comprobante,$ls_tipnom,1);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sno($this->is_codemp,$ls_nomina,$ls_periodo,$as_comprobante,
															    $adt_fecha,'1900-01-01');
		}
		$li_genrd=$this->uf_select_config_nomina($ls_nomina);
		if($li_genrd=="1")
		{
			$lb_valido=$this->uf_generar_recepcion_documento_personal_cheque($ls_nomina,$ls_periodo,$adt_fecha,$aa_seguridad);
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Gener� la Recepci�n de Documento N�mina <b>".$ls_nomina."</b>, Per�odo <b>".$ls_periodo."</b>, ".
							"Comprobante <b>".$as_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/// PARA LA CONVERSI�N MONETARIA
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/*if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_cxprd($as_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,$aa_seguridad);
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_cxprdscg($as_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,$aa_seguridad);
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_cxprdspg($as_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,$aa_seguridad);
		}*/
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Fin de la transacci�n hace comit � Rollback de acuerdo al $lb_valido
		if($lb_valido)
		{
			$ls_guarderia=trim($this->uf_select_configuracion("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO GUARDERIA","I","0"));				
			if($ls_guarderia==1)
			{
				$lb_valido = $this->uf_procesar_recepcion_documento_guarderias($as_comprobante,$adt_fecha,$aa_seguridad);
			}
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);		
		return $lb_valido;
	}  // end function uf_procesar_recepcion_documento_tipo_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_documento_gasto($as_comprobante,$as_codtipdoc,$as_ced_bene,$as_cod_pro,$as_codcomapo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_gasto
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   as_codtipdoc  // Tipo de Documento
		//				   as_ced_bene  // C�dula del Beneficiario
		//				   as_cod_pro  // C�digo del Proveedor
		//				   as_codcomapo  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se inserto los detalles presupuestario en la recepci�n de documento correctamente
		//	  Description: M�todo que inserta los movimientos de gasto en la tabla de detalle de gasto de la recepcion de documento
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SNOCNO";
		$ls_criterio="";	
		if ($this->is_tiponomina=="A")
		{
			$ls_criterio=$ls_criterio."	AND codcomapo='".$as_codcomapo."'";
		}		  
		$ls_sql="SELECT codemp, codnom, codperi, codcom, tipnom, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, ".
				"		spg_cuenta, operacion, codconc, cod_pro, ced_bene, tipo_destino, descripcion, monto, estatus, estrd, ".
				"		codtipdoc, estnumvou, estnotdeb, codcomapo, estcla ".
				"  FROM sno_dt_spg ".
				" WHERE codemp='".$this->is_codemp."' ".
				"	AND codcom='".$as_comprobante."' ".
				$ls_criterio;

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_gasto ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
			if ($this->is_tiponomina=="A")
			{
				$as_comprobante = $as_codcomapo;
			}
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
			{
				$ls_codestpro=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$la_estructura[0]=$row["codestpro1"];
				$la_estructura[1]=$row["codestpro2"];
				$la_estructura[2]=$row["codestpro3"];		
				$la_estructura[3]=$row["codestpro4"];
				$la_estructura[4]=$row["codestpro5"];
				$la_estructura[5]=$ls_estcla;
				$ls_spg_cuenta=$row["spg_cuenta"];
				$ldec_monto=$row["monto"];				
				$ls_documento=$row["codconc"];								 
				$ls_documento=$this->io_sigesp_int->uf_fill_comprobante(trim($ls_documento));
				$ls_status="";
				$ls_denominacion="";
				$ls_sc_cuenta="";
				if(!$this->io_sigesp_int_spg->uf_spg_select_cuenta($this->is_codemp,$la_estructura,$ls_spg_cuenta,&$ls_status,&$ls_denominacion,&$ls_sc_cuenta))
				{
					$ls_estructura=$la_estructura[0]."-".$la_estructura[1]."-".$la_estructura[2]."-".$la_estructura[3]."-".$la_estructura[4]."-".$la_estructura[5];
					$this->io_msg->message("La Cuenta Presupuestaria ".$ls_estructura."::".$ls_spg_cuenta." no existe en el plan de cuenta.");			
					$lb_valido=false;
				}
				if($lb_valido)
				{
					$ls_sql="INSERT INTO cxp_rd_spg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,codestpro,".
							"spg_cuenta,monto,estcla)  VALUES ('".$this->is_codemp."','".$as_comprobante."','".$as_codtipdoc."',".
							"'".$as_ced_bene."','".$as_cod_pro."','".$ls_procede."','".$ls_documento."','".$ls_codestpro."',".
							"'".$ls_spg_cuenta."',".$ldec_monto.",'".$ls_estcla."')";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_gasto ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
						$lb_valido=false;
						break;
					}
				}
			} // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_documento_contable($as_comprobante,$as_codtipdoc,$as_ced_bene,$as_cod_pro,$as_codcomapo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_contable
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   as_codtipdoc  // Tipo de Documento
		//				   as_ced_bene  // C�dula del Beneficiario
		//				   as_cod_pro  // C�digo del Proveedor
		//				   as_codcomapo  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se inserto los detalles contables en la recepci�n de documento correctamente
		//	  Description: M�todo que inserta los movimientos contables en la tabla de detalle de contable de la recepcion de documento
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SNOCNO";
		$ls_criterio="";	
		if ($this->is_tiponomina=="A")
		{
			$ls_criterio=$ls_criterio."	AND codcomapo='".$as_codcomapo."'";
		}		  
		$ls_sql="SELECT codemp, codnom, codperi, codcom, tipnom, sc_cuenta, debhab, codconc, cod_pro, ced_bene, tipo_destino, ".
				"		descripcion, monto, estatus, estrd, codtipdoc, estnumvou, estnotdeb, codcomapo ".
				"  FROM sno_dt_scg ".
				" WHERE codemp='".$this->is_codemp."' ".
				"	AND codcom='".$as_comprobante."' ".
				$ls_criterio;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
			if ($this->is_tiponomina=="A")
			{
				$as_comprobante = $as_codcomapo;
			}
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
			{
				$ls_scg_cuenta = $row["sc_cuenta"];
				$ldec_monto = $row["monto"];				
				$ls_debhab = $row["debhab"];				
				$ls_documento = $row["codconc"];								 
				$ls_documento = $this->io_sigesp_int->uf_fill_comprobante(trim($ls_documento));
				$ls_status="";
				$ls_denominacion="";
				if(!$this->io_sigesp_int_scg->uf_scg_select_cuenta($this->is_codemp,$ls_scg_cuenta,$ls_status,$ls_denominacion))
				{
					$this->io_msg->message("La cuenta contable ".trim($ls_sc_cuenta)." no exite en el plan de cuenta.");			
				}
				if($lb_valido)
				{
					$ls_sql="INSERT INTO cxp_rd_scg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,".
							"sc_cuenta,monto) VALUES ('".$this->is_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$as_ced_bene."',".
							"'".$as_cod_pro."','".$ls_procede."','".$ls_documento."','".$ls_debhab."','".$ls_scg_cuenta."',".$ldec_monto.")";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
						$lb_valido=false;
						break;
					}
				}
			} // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_documento_gasto_guarderias($as_comprobante,$as_codtipdoc,$as_ced_bene,$as_cod_pro,$as_codcomapo,
														    $as_numrecdoc,$as_codestpro,$as_estcla,$adec_monto,$as_spgcuenta,&$as_sc_cuenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_gasto_guarderias
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   as_codtipdoc  // Tipo de Documento
		//				   as_ced_bene  // C�dula del Beneficiario
		//				   as_cod_pro  // C�digo del Proveedor
		//				   as_codcomapo  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se inserto los detalles presupuestario en la recepci�n de documento correctamente
		//	  Description: M�todo que inserta los movimientos de gasto en la tabla de detalle de gasto de la recepcion de documento
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SNOCNO";
		$ls_criterio="";	
		$ls_status="";
		$ls_denominacion="";
		$ls_sc_cuenta="";
		$la_estructura[0]=substr($as_codestpro,0,25);
		$la_estructura[1]=substr($as_codestpro,25,25);
		$la_estructura[2]=substr($as_codestpro,50,25);		
		$la_estructura[3]=substr($as_codestpro,75,25);
		$la_estructura[4]=substr($as_codestpro,100,25);
		$la_estructura[5]=$as_estcla;
		if(!$this->io_sigesp_int_spg->uf_spg_select_cuenta($this->is_codemp,$la_estructura,$as_spgcuenta,&$ls_status,&$ls_denominacion,&$as_sc_cuenta))
		{
			$ls_estructura=$la_estructura[0]."-".$la_estructura[1]."-".$la_estructura[2]."-".$la_estructura[3]."-".$la_estructura[4]."-".$la_estructura[5];
			$this->io_msg->message("La Cuenta Presupuestaria ".$ls_estructura."::".$as_spgcuenta." no existe en el plan de cuenta.");			
			$lb_valido=false;
		}
		if($lb_valido)
		{
			$ls_sql="INSERT INTO cxp_rd_spg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,codestpro,".
					"spg_cuenta,monto,estcla)  VALUES ('".$this->is_codemp."','".$as_numrecdoc."','".$as_codtipdoc."',".
					"'".$as_ced_bene."','".$as_cod_pro."','".$ls_procede."','".$as_comprobante."','".$as_codestpro."',".
					"'".$as_spgcuenta."',".$adec_monto.",'".$as_estcla."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_gasto ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
				break;
			}
		}
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_documento_contable_guarderias($as_comprobante,$as_codtipdoc,$as_ced_bene,$as_cod_pro,
															   $as_codcomapo,$as_numrecdoc,$adec_monto,$as_sccuenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_contable
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   as_codtipdoc  // Tipo de Documento
		//				   as_ced_bene  // C�dula del Beneficiario
		//				   as_cod_pro  // C�digo del Proveedor
		//				   as_codcomapo  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se inserto los detalles contables en la recepci�n de documento correctamente
		//	  Description: M�todo que inserta los movimientos contables en la tabla de detalle de contable de la recepcion de documento
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SNOCNO";
		$ls_sql="SELECT sc_cuenta ".
				"  FROM rpc_beneficiario ".
				" WHERE codemp='".$this->is_codemp."' ".
				"	AND ced_bene='".$as_ced_bene."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_scg_cuenta = $row["sc_cuenta"];
				$ls_status="";
				$ls_denominacion="";
				if(!$this->io_sigesp_int_scg->uf_scg_select_cuenta($this->is_codemp,$ls_scg_cuenta,$ls_status,$ls_denominacion))
				{
					$this->io_msg->message("La cuenta contable ".trim($ls_scg_cuenta)." no exite en el plan de cuenta.");			
				}
				if(!$this->io_sigesp_int_scg->uf_scg_select_cuenta($this->is_codemp,$as_sccuenta,$ls_status,$ls_denominacion))
				{
					$this->io_msg->message("La cuenta contable ".trim($as_sccuenta)." no exite en el plan de cuenta.");			
				}
				if($lb_valido)
				{
					$ls_sql="INSERT INTO cxp_rd_scg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,".
							"sc_cuenta,monto) VALUES ('".$this->is_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$as_ced_bene."',".
							"'".$as_cod_pro."','".$ls_procede."','".$as_comprobante."','D','".$as_sccuenta."',".$adec_monto.")"; 
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
						$lb_valido=false;
					}
				}
				if($lb_valido)
				{
					$ls_sql="INSERT INTO cxp_rd_scg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,".
							"sc_cuenta,monto) VALUES ('".$this->is_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$as_ced_bene."',".
							"'".$as_cod_pro."','".$ls_procede."','".$as_comprobante."','H','".$ls_scg_cuenta."',".$adec_monto.")";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
						$lb_valido=false;
					}
				}
			} 
			else
			{
				$this->io_msg->message("El personal ".trim($as_ced_bene)." no exite como beneficiario.");			
				$lb_valido=false;
			}
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_nomina_aportes($as_comprobante,$adt_fecha,$as_nomina,$as_periodo,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_nomina_aportes
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   adt_fecha  // Fecha de contabilizaci�n
		//				   as_nomina  // c�digo de N�mina
		//				   as_periodo  // C�digo de Per�odo 
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto la contabilizaci�n correctamente
		//	  Description: Funcion que procesa la contabilizaci�n de los aportes de una N�mina
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
	    if(!$this->uf_obtener_data_aportes($as_comprobante))
		{
			return false;
		}       
		$this->is_procede="SNOCNO";
		$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);		
		$ll_tot_row=$this->dts_nomina_aporte->getRowCount("codcomapo");
		// inicia transacci�n SQL
		$this->io_sigesp_int->uf_int_init_transaction_begin(); 
		// Recorremos todos los comprobantes de los aportes
	    for ($ll_row=1;($ll_row<=$ll_tot_row)&&($lb_valido); $ll_row++ )
		{
			$lb_valido = false;       
			$ls_comprobante = $this->dts_nomina_aporte->getValue("codcomapo",$ll_row);	
			$ls_comprobante = $this->io_sigesp_int->uf_fill_comprobante(trim($ls_comprobante));
			$as_comprobante = $this->io_sigesp_int->uf_fill_comprobante(trim($as_comprobante));			
			$ls_cod_pro	= $this->dts_nomina_aporte->getValue("cod_pro",$ll_row);	
			$ls_ced_bene = $this->dts_nomina_aporte->getValue("ced_bene",$ll_row);	
			$ls_descripcion = $this->dts_nomina_aporte->getValue("descripcion",$ll_row);	
			$ls_tipo_destino = $this->dts_nomina_aporte->getValue("tipo_destino",$ll_row);			
			$li_estatus = $this->dts_nomina_aporte->getValue("estatus",$ll_row);	
			if($ls_tipo_destino=="B")
			{
				$ls_codigo_destino = $ls_ced_bene;
			}
			if($ls_tipo_destino=="P")
			{
				$ls_codigo_destino = $ls_cod_pro;
			}
			if($ls_tipo_destino=="-")
			{
				$ls_codigo_destino = "----------";
			}
			if ($li_estatus==1) 
			{
			   $this->io_msg->message("ERROR-> La N�mina debe estar en estatus EMITIDA para su contabilizaci�n.");
			   return false ;
			}
			// Creamos la Cabecera del Comprobante
			$ls_codban="---";
			$ls_ctaban="-------------------------";
			$li_tipo_comp=1; // comprobante Normal
			$this->as_procede=$this->is_procede;
			$this->as_comprobante=$ls_comprobante;
			$this->ad_fecha=$adt_fecha;
			$this->as_codban=$ls_codban;
			$this->as_ctaban=$ls_ctaban;
			$lb_valido = $this->io_sigesp_int->uf_int_init($this->is_codemp,$this->is_procede,$ls_comprobante,$adt_fecha,
														   $ls_descripcion,$ls_tipo_destino,$ls_codigo_destino,false,$ls_codban,
														   $ls_ctaban,$li_tipo_comp);
			$this->io_sigesp_int->uf_int_config(false,false);
			if (!$lb_valido)
			{   
			   $this->io_msg->message($this->io_sigesp_int->is_msg_error); 
			   return false;		   		   
			}
			// Agregamos al Datastored los detalles Presupuestarios			
			$lb_valido = $this->uf_procesar_detalles_gasto($as_comprobante,$ls_comprobante);
			if ($lb_valido)
			{
				// Agregamos al Datastored los detalles Presupuestarios			
				$lb_valido = $this->uf_procesar_detalles_contables($as_comprobante,$ls_comprobante);  
				if ($lb_valido) 
				{	// Actualizamos el estatus de contabilizaci�n de los aportes 
					$lb_valido=$this->uf_update_estatus_nomina_aporte($as_periodo,$as_nomina,$as_comprobante,$ls_comprobante,1); 
				}
				if ($lb_valido)
				{	// Insertamos el comprobante en la Base de Datos
					$lb_valido = $this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				}
			}
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="PROCESS";
				$ls_descripcion="Contabiliz� la N�mina Aporte <b>".$as_nomina."</b>, Per�odo <b>".$as_periodo."</b>, ".
								"Comprobante <b>".$as_comprobante."</b>, Comprobante Aporte <b>".$ls_comprobante."</b>";
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
			}
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			/// PARA LA CONVERSI�N MONETARIA
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			/*if($lb_valido)
			{
				$lb_valido=$this->io_fun_mis->uf_convertir_sigespcmp($this->as_procede,$this->as_comprobante,$this->ad_fecha,
																	 $this->as_codban,$this->as_ctaban,$aa_seguridad);
			}
			if($lb_valido)
			{
				$lb_valido=$this->io_fun_mis->uf_convertir_spgdtcmp($this->as_procede,$this->as_comprobante,$this->ad_fecha,
																	$this->as_codban,$this->as_ctaban,$aa_seguridad);
			}
			if($lb_valido)
			{
				$lb_valido=$this->io_fun_mis->uf_convertir_scgdtcmp($this->as_procede,$this->as_comprobante,$this->ad_fecha,
																	$this->as_codban,$this->as_ctaban,$aa_seguridad);
			}*/
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sno($this->is_codemp,$as_nomina,$as_periodo,$as_comprobante,
																$adt_fecha,'1900-01-01');
		}
		if($lb_valido)
		{	// Actualizamos el estatus en la n�mina
			$lb_valido=$this->uf_update_estatus_periodo_nomina($as_periodo,$as_nomina,1);
		}
		// Finalizamos la transacci�n hacemos un commit � rollback de acuerdo $lb_valido
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);		
		return $lb_valido;
	} // end function uf_procesar_nomina_aportes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_data_aportes($as_comprobante)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_data_aportes
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//	      Returns: lb_valido True si se cargo la data correctamente
		//	  Description: Este metodo que obtiene la informaci�n agrupada la informaci�n asociadas a los aportes
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_existe = false;
		$this->io_sql=new class_sql($this->io_connect);		
		$ls_sql="SELECT codcomapo,cod_pro,ced_bene,tipo_destino,descripcion,codtipdoc ".
                "  FROM sno_dt_spg ".
                " WHERE codemp='".$this->is_codemp."' ".
				"   AND codcom='".$as_comprobante."'".
				" UNION ".
				"SELECT codcomapo,cod_pro,ced_bene,tipo_destino,descripcion,codtipdoc ".
                "  FROM sno_dt_scg ".
                " WHERE codemp='".$this->is_codemp."' ".
				"   AND codcom='".$as_comprobante."'".
				" GROUP BY codcomapo,cod_pro,ced_bene,tipo_destino,descripcion,codtipdoc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_obtener_data_aportes ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=true;
                $this->dts_nomina_aporte->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$this->io_msg->message("ERROR-> No hay data para el comprobante de aportes N�".$as_comprobante);			
			}
		}
		$this->io_sql->free_result($rs_data);
		return $lb_existe;
	}  // end function uf_obtener_data_aporte
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_nomina_aporte($as_periodo,$as_nomina,$as_comprobante,$as_codcomapo,$ai_estatus)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_nomina_aporte
		//		   Access: private
		//	    Arguments: as_periodo  // Per�odo de la n�mina
		//				   as_nomina  // c�digo de l an�mina
		//				   as_comprobante  // n�mero de Comprobante
		//				   as_codcomapo  // N�mero de Comprobante de Aportes
		//				   ai_estatus  // Estatus si es Cero � Uno
		//	      Returns: lb_valido True si se ejecuto la contabilizaci�n correctamente
		//	  Description: M�todo que actualiza el estatus de la nomina detalle aportes en contabilizado
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_sql="UPDATE sno_dt_scg ".
				"   SET estatus=".$ai_estatus.
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codnom='".$as_nomina."' ".
				"   AND codperi='".$as_periodo."' ".
				"   AND codcom='".$as_comprobante."' ".
				"   AND codcomapo='".$as_codcomapo."' ".
				"   AND tipnom='".$this->is_tiponomina."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_update_estatus_nomina_aporte ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		if ($lb_valido)
		{
			$ls_sql="UPDATE sno_dt_spg ".
					"   SET estatus=".$ai_estatus.
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND codnom='".$as_nomina."' ".
					"   AND codperi='".$as_periodo."' ".
					"   AND codcom='".$as_comprobante."' ".
					"   AND codcomapo='".$as_codcomapo."' ".
					"   AND tipnom='".$this->is_tiponomina."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_update_estatus_nomina_aporte ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		return $lb_valido;
	} // end function uf_update_estatus_nomina_aporte
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_periodo_nomina($as_periodo,$as_nomina,$ai_estatus)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_periodo_nomina
		//		   Access: private
		//	    Arguments: as_periodo  // Per�odo de la n�mina
		//				   as_nomina  // c�digo de l an�mina
		//				   ai_estatus  // Estatus si es Cero � Uno
		//	      Returns: lb_valido True si se ejecuto la contabilizaci�n correctamente
		//	  Description: M�todo que actualiza el estatus del per�odo de la nomina para los aportes
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido = true;
		$ls_sql="UPDATE sno_periodo ".
				"   SET apoconper=".$ai_estatus.
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codnom='".$as_nomina."' ".
				"   AND codperi='".$as_periodo."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_update_estatus_periodo_nomina ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		return $lb_valido;
	} // end uf_update_estatus_periodo_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_recepcion_documento_tipo_aporte($as_comprobante,$adt_fecha,$as_nomina,$as_periodo,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_recepcion_documento_tipo_aporte
		//		   Access: private
		//	    Arguments: as_comprobante  // n�mero de comprobante
		//				   adt_fecha  // Fecha de Contabilizaci�n
		//				   as_nomina  // C�digo de la N�mina
		//				   as_periodo  // c�digo del per�odo de la N�mina
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se inserto la recepci�n correctamente
		//	  Description: M�todo que genera las recepciones de documento por cada aporte definido en la nomina por periodo calculado
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
	    if(!$this->uf_obtener_data_aportes($as_comprobante))
		{
			return false;
		}       
		$adt_fecha = $this->io_function->uf_convertirdatetobd($adt_fecha);		
		$this->is_procede= "SNOCNO";
		$ll_tot_row = $this->dts_nomina_aporte->getRowCount("codcomapo");
		// inicia transacci�n SQL
		$this->io_sigesp_int->uf_int_init_transaction_begin(); 	
	    for ($ll_row=1;($ll_row<=$ll_tot_row)&&($lb_valido);$ll_row++)
		{
		    $lb_valido = true;       
			$ls_comprobante = $this->dts_nomina_aporte->getValue("codcomapo",$ll_row);	
			$ls_comprobante = $this->io_sigesp_int->uf_fill_comprobante(trim($ls_comprobante));
			$ls_cod_pro	= $this->dts_nomina_aporte->getValue("cod_pro",$ll_row);	
			$ls_ced_bene = $this->dts_nomina_aporte->getValue("ced_bene",$ll_row);	
			$ls_tipo_destino = $this->dts_nomina_aporte->getValue("tipo_destino",$ll_row);			
			$ls_descripcion = $this->dts_nomina_aporte->getValue("descripcion",$ll_row);				
			$ls_codtipdoc = $this->dts_nomina_aporte->getValue("codtipdoc",$ll_row);							
			$ldec_sum_monto	 = $this->uf_obtener_total_monto($as_comprobante,$ls_comprobante);
			// Crear la cabecera de la recepci�n de documento
            $lb_valido = $this->uf_insert_cabecera_recepcion_documento($ls_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,$ls_descripcion,$adt_fecha,$ldec_sum_monto,$ls_tipo_destino);
            if($lb_valido)
			{	// Insertar los detalles de Presupuesto
				$lb_valido=$this->uf_insert_recepcion_documento_gasto($as_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,$ls_comprobante);
			}
		    if($lb_valido)
			{	// Insertar los detalles de Contabilidad
				$lb_valido=$this->uf_insert_recepcion_documento_contable($as_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,$ls_comprobante);
			}
	        if($lb_valido)
			{	// Actualizar el estatus de la n�mina
				$lb_valido=$this->uf_update_estatus_nomina_aporte($as_periodo,$as_nomina,$as_comprobante,$ls_comprobante,1);
			}		
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="PROCESS";
				$ls_descripcion="Gener� la Recepci�n de Documento N�mina Aporte <b>".$as_nomina."</b>, Per�odo <b>".$as_periodo."</b>, ".
								"Comprobante <b>".$as_comprobante."</b>, Comprobante Aporte <b>".$ls_comprobante."</b>";
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
			}
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			/// PARA LA CONVERSI�N MONETARIA
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			/*if($lb_valido)
			{
				$lb_valido=$this->io_fun_mis->uf_convertir_cxprd($ls_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,$aa_seguridad);
			}
			if($lb_valido)
			{
				$lb_valido=$this->io_fun_mis->uf_convertir_cxprdscg($ls_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,$aa_seguridad);
			}
			if($lb_valido)
			{
				$lb_valido=$this->io_fun_mis->uf_convertir_cxprdspg($ls_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,$aa_seguridad);
			}*/
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sno($this->is_codemp,$as_nomina,$as_periodo,$as_comprobante,
																$adt_fecha,'1900-01-01');
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_estatus_periodo_nomina($as_periodo,$as_nomina,1);
		}
		// fin de la transacci�n
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);				
		return $lb_valido;
    }   // end function	uf_procesar_recepcion_documento_tipo_aporte
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_insert_cabecera_recepcion_documento($as_comprobante,$ls_codtipdoc,$ls_ced_bene,$ls_cod_pro,$ls_descripcion,$adt_fecha,$ldec_sum_monto,$ls_tipo_destino)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_cabecera_recepcion_documento
		//		   Access: private
		//	    Arguments: as_comprobante  // n�mero de comprobante
		//				   as_codtipdoc  // Fecha de Contabilizaci�n
		//                 as_ced_bene  // cedula del beneficiario
		//                 as_cod_pro  // codigo del proveedor
		//                 as_descripcion  // descripcion 
		//                 adt_fecha  // fecha de contabilizaci�n
		//                 ldec_sum_monto  // monto suma de los movimientos de recepcion
		//                 ls_tipo_destino  // beneficiario o proveedor		
		//	      Returns: lb_valido True si se inserto la recepci�n correctamente
		//	  Description: M�todo que inserta la informaci�n en la cabecera tabla de la recepcion de documento
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		$ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,".
 		        "montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,fecaprord,usuaprord,".
				"estimpmun,codcla, codrecdoc) VALUES ('".$this->is_codemp."','".$as_comprobante."','".$ls_codtipdoc."','".$ls_ced_bene."',".
				"'".$ls_cod_pro."','".$ls_descripcion."','".$adt_fecha."','".$adt_fecha."','".$adt_fecha."',".$ldec_sum_monto.
				",0,0,'".$ls_tipo_destino."','".$as_comprobante."','R','SNOCNO',0,0,'1900-01-01','',0,'--','".$as_comprobante."')"; 
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{  
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_cabecera_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
	    return $lb_valido;
	} // end function uf_insert_cabecera_recepcion_documento
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reversar_contabilizacion_nomina($as_comprobante,$ad_fechaconta,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_contabilizacion_nomina
		//		   Access: public (sigesp_mis_p_reverso_sno.php)
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   ad_fechaconta  // Fecha en que fue contabilizado el Documento
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el reverso correctamente
		//	  Description: M�todo que reversa la contabilizacion de la nomina
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->dts_nomina->resetds("codcom");		
	    if(!$this->uf_obtener_data_comprobante($as_comprobante))
		{
			return false ;
		}		
                $li_estred=$this->dts_nomina->getValue("estrd",1);
		$this->is_tiponomina=$this->dts_nomina->getValue("tipnom",1);  
		$ls_nomina=$this->dts_nomina->getValue("codnom",1); 
		$ls_periodo=$this->dts_nomina->getValue("codperi",1);  
		$ls_cod_pro=$this->dts_nomina->getValue("cod_pro",1);	
		$ls_ced_bene=$this->dts_nomina->getValue("ced_bene",1);	
		$ls_codtipdoc=$this->dts_nomina->getValue("codtipdoc",1);

		switch($this->is_tiponomina)
		{	
			case "N":
				// Si el reverso es para las N�mina
				if($li_estred==0)
				{	// si es un reverso de una contabilizaci�n Normal
					$lb_valido=$this->uf_reversar_contabilizacion($as_comprobante,$ad_fechaconta,$aa_seguridad);
				}
				else
				{	// Si es un reverso de una recepci�n de documento
					$lb_valido=$this->uf_reversar_recepcion_documento($as_comprobante,1,$ad_fechaconta,$ls_cod_pro,$ls_ced_bene,$ls_codtipdoc,$aa_seguridad);
				}
				if($lb_valido)
				{
					$ls_guarderia=trim($this->uf_select_configuracion("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO GUARDERIA","I","0"));				
					if($ls_guarderia==1)
					{
						$lb_valido=$this->uf_reversar_recepcion_documento_guarderias($as_comprobante,$ad_fechaconta,$aa_seguridad);
					}
				}
			break;
			case "A":
				// Si el reverso es para los aportes
				if($li_estred==0)
				{	// si es un reverso de una contabilizaci�n Normal
					$lb_valido=$this->uf_reversar_contabilizacion_aportes($as_comprobante,$ls_nomina,$ls_periodo,$ad_fechaconta,$aa_seguridad);
				}
				else
				{	// Si es un reverso de una recepci�n de documento
					$lb_valido=$this->uf_reversar_recepcion_documento_tipo_aporte($as_comprobante,$ls_nomina,$ls_periodo,$ad_fechaconta,$aa_seguridad);
				}
			break;
			case "I":
				// si es un reverso de una contabilizaci�n Normal
				$lb_valido=$this->uf_reversar_contabilizacion($as_comprobante,$ad_fechaconta,$aa_seguridad);
			break;
			case "P":
				// Si el reverso es para la Prestaci�n Antiguedad
				if($li_estred==0)
				{	// si es un reverso de una contabilizaci�n Normal
					$lb_valido=$this->uf_reversar_contabilizacion($as_comprobante,$ad_fechaconta,$aa_seguridad);
				}
				else
				{	// Si es un reverso de una recepci�n de documento
					$lb_valido=$this->uf_reversar_recepcion_documento($as_comprobante,1,$ad_fechaconta,$ls_cod_pro,$ls_ced_bene,$ls_codtipdoc,$aa_seguridad);
				}
			break;
		}	
        return $lb_valido;		
    } // end function uf_reversar_contabilizacion_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_reversar_contabilizacion($as_comprobante,$ad_fechaconta,$aa_seguridad)	
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_contabilizacion
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   ad_fechaconta  // Fecha en que fue contabilizado el Documento
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el reverso correctamente
		//	  Description: Este metodo reversa contablemente y presupuestariamente una n�mina contabilizada
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
   	    $ldt_fecha=$ad_fechaconta;
        $ls_codemp=$this->is_codemp;
        $ls_procede="SNOCNO";
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante(trim($as_comprobante));
		$ls_cod_pro=$this->dts_nomina->getValue("cod_pro",1);	
		$ls_ced_bene=$this->dts_nomina->getValue("ced_bene",1);	
        $ls_tipo_destino = $this->dts_nomina->getValue("tipo_destino",1);			
		$ls_nomina = $this->dts_nomina->getValue("codnom",1); 
		$ls_periodo = $this->dts_nomina->getValue("codperi",1);  
        $ls_tipnom = $this->dts_nomina->getValue("tipnom",1);  
        $li_estnotdeb = $this->dts_nomina->getValue("estnotdeb",1); 
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		// Buscamos el comprobante a reversar						
	    $lb_valido = $this->io_sigesp_int->uf_obtener_comprobante($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_codban,
																  $ls_ctaban,$ls_tipo_destino,$ls_ced_bene,$ls_cod_pro);
		if (!$lb_valido) 
		{ 
			$this->io_msg->message("ERROR-> No existe el comprobante N� ".$ls_comprobante."-".$ls_procede.".");
			return false;
		}
		$lb_check_close=false;
		// Creamos la cabecera del comprobante y validamos la informaci�n
		$lb_valido = $this->io_sigesp_int->uf_init_delete($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_tipo_destino,
														  $ls_ced_bene,$ls_cod_pro,$lb_check_close,$ls_codban,$ls_ctaban);
		if(!$lb_valido)	
		{ 
 		   $this->io_msg->message("".$this->io_sigesp_int->is_msg_error);
		   return false; 
		}
		// Iniciamos la transacci�n en la BD
        $this->io_sigesp_int->uf_int_init_transaction_begin();
        if ($li_estnotdeb==1)
		{	// Si se hizo nota de D�bito se Reversa
		   $lb_valido=$this->uf_generar_nota_debito_banco($ls_nomina,$ls_periodo,$as_comprobante,"Ninguno",$ldt_fecha,2,$ls_tipo_destino,
		   												  $ls_cod_pro,$ls_ced_bene,$aa_seguridad);
		}
	    if($lb_valido) 
		{// Si se hizo nota de D�bito se Reversa 
		   $lb_valido=$this->uf_update_estatus_nomina($ls_periodo,$ls_nomina,$as_comprobante,$ls_tipnom,0); 
	    } 
		if($lb_valido)
		{	// Reversamos los detalles y el comprobante
			$lb_valido = $this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
			if(!$lb_valido)
			{
				$this->io_msg->message(" ERROR-> ".$this->io_sigesp_int->is_msg_error);
			}		   
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sno($this->is_codemp,$ls_nomina,$ls_periodo,$ls_comprobante,
																'1900-01-01','1900-01-01');
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_reversar_recepcion_documento_personal_cheque($ls_nomina,$ls_periodo,$aa_seguridad);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_reversar_pagodirecto_personal_cheque($ls_nomina,$ls_periodo,$aa_seguridad);
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso Contabilizaci�n N�mina <b>".$ls_nomina."</b>, Per�odo <b>".$ls_periodo."</b>, ".
							"Comprobante <b>".$ls_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		// Finalizamos la transacci�n en la base de datos
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return  $lb_valido;
	} // end function uf_reversar_contabilizacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reversar_recepcion_documento($as_comprobante,$ai_row,$ad_fechaconta,$as_cod_pro,$as_ced_bene,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_recepcion_documento
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   ad_fechaconta  // Fecha en que fue contabilizado el Documento
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el reverso correctamente
		//	  Description: Este metodo elimina la recepci�n de documento de una n�mina
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_estprodoc="";
		$ls_nomina=$this->dts_nomina->getValue("codnom",$ai_row); 
		$ls_periodo=$this->dts_nomina->getValue("codperi",$ai_row);  
        $ls_tipnom=$this->dts_nomina->getValue("tipnom",$ai_row);  
        $this->io_sigesp_int->uf_int_init_transaction_begin();
		// Eliminamos los Detalles Contables
		$lb_existe=$this->uf_validar_recepcion_documento($as_comprobante,$as_ced_bene,$as_codtipdoc,&$ls_estprodoc);
		if($lb_existe)
		{
			if($ls_estprodoc!="R")
			{
				$this->io_msg->message("La Recepcion de Documentos ".$as_comprobante." debe estar en estatus de Registro");
				return false;
			}
		}
		$ls_sql="DELETE ".
				"  FROM cxp_rd_scg ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND numrecdoc='".$as_comprobante."' ".
				"   AND codtipdoc='".$as_codtipdoc."' ".
				"   AND cod_pro='".$as_cod_pro."' ".
				"   AND ced_bene='".$as_ced_bene."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		if ($lb_valido)
		{
			// Eliminamos los Detalles Presupuestarios
			$ls_sql="DELETE ".
					"  FROM cxp_rd_spg ".
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND numrecdoc='".$as_comprobante."' ".
					"   AND codtipdoc='".$as_codtipdoc."' ".
					"   AND cod_pro='".$as_cod_pro."' ".
					"   AND ced_bene='".$as_ced_bene."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
			if ($lb_valido)
			{
				// Eliminamos los Hist�ricos de La Recepci�n de Documento
				$ls_sql="DELETE ".
						"  FROM cxp_historico_rd ".
						" WHERE codemp='".$this->is_codemp."' ".
						"   AND numrecdoc='".$as_comprobante."' ".
						"   AND codtipdoc='".$as_codtipdoc."' ".
						"   AND cod_pro='".$as_cod_pro."' ".
						"   AND ced_bene='".$as_ced_bene."'";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					$lb_valido=false;
				}
			}
			if ($lb_valido)
			{
				// Eliminamos La Recepci�n de Documento
				$ls_sql="DELETE ".
						"  FROM cxp_rd ".
						" WHERE codemp='".$this->is_codemp."' ".
						"   AND numrecdoc='".$as_comprobante."' ".
						"   AND codtipdoc='".$as_codtipdoc."' ".
						"   AND cod_pro='".$as_cod_pro."' ".
						"   AND ced_bene='".$as_ced_bene."'";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					$lb_valido=false;
				}
			}
		}
	    if($lb_valido) 
		{	// Actualizamos el estatus de la N�mina 
		   $lb_valido=$this->uf_update_estatus_nomina($ls_periodo,$ls_nomina,$as_comprobante,$ls_tipnom,0); 
	    } 
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sno($this->is_codemp,$ls_nomina,$ls_periodo,$as_comprobante,
																'1900-01-01','1900-01-01');
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_reversar_recepcion_documento_personal_cheque($ls_nomina,$ls_periodo,$aa_seguridad);
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso la Recepci�n de Documento N�mina <b>".$ls_nomina."</b>, Per�odo <b>".$ls_periodo."</b> ".
							"Comprobante <b>".$as_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return $lb_valido;
	}  // end function uf_reversar_recepcion_documento
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reversar_contabilizacion_aportes($as_comprobante,$as_nomina,$as_periodo,$ad_fechaconta,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_contabilizacion_aportes
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   as_nomina  // C�digo de N�mina
		//				   as_periodo  // C�digo de Per�odo de la N�mina
		//				   ad_fechaconta  // Fecha en que fue contabilizado el documento
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el reverso correctamente
		//	  Description: M�todo que reversa la contabilizacion de los aportes 
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 26/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
	    if(!$this->uf_obtener_data_aportes($as_comprobante))
		{
			return false;
		}       
   	    $ldt_fecha=$ad_fechaconta;		
		$this->is_procede="SNOCNO";
		$ll_tot_row=$this->dts_nomina_aporte->getRowCount("codcomapo");
		// Inicializaci�n de la transacci�n SQL
		$this->io_sigesp_int->uf_int_init_transaction_begin();
	    for($ll_row=1;($ll_row<=$ll_tot_row)&&($lb_valido); $ll_row++ )
		{      
			$ls_comprobante=$this->dts_nomina_aporte->getValue("codcomapo",$ll_row);	
			$ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante(trim($ls_comprobante));
			$ls_cod_pro=$this->dts_nomina_aporte->getValue("cod_pro",$ll_row);	
			$ls_ced_bene=$this->dts_nomina_aporte->getValue("ced_bene",$ll_row);	
			$ls_tipo_destino=$this->dts_nomina_aporte->getValue("tipo_destino",$ll_row);			
			$ls_codban="---";
			$ls_ctaban="-------------------------";
		    // Obtener La data del Comprobante
			$lb_valido = $this->io_sigesp_int->uf_obtener_comprobante($this->is_codemp,$this->is_procede,$ls_comprobante,
																	  $ldt_fecha,$ls_codban,$ls_ctaban,$ls_tipo_destino,
																	  $ls_ced_bene,$ls_cod_pro);
			if ($lb_valido) 
			{ 
				$lb_check_close=false;
				// Se Inicializa la cabecera del comprobante
				$lb_valido = $this->io_sigesp_int->uf_init_delete($this->is_codemp,$this->is_procede,$ls_comprobante,$ldt_fecha,
																  $ls_tipo_destino,$ls_ced_bene,$ls_cod_pro,$lb_check_close,
																  $ls_codban,$ls_ctaban);
				if(!$lb_valido)	
				{ 
					$this->io_msg->message("".$this->io_sigesp_int->is_msg_error);
				}	
				if($lb_valido)
				{	// Actualiza el estatus de los aportes para que actualice en SPG
					$lb_valido=$this->uf_update_estatus_nomina_aporte($as_periodo,$as_nomina,$as_comprobante,$ls_comprobante,0);
				} 
				if(!$lb_valido)
				{
					return false;
				}
				else
				{	// Elimina en la base de datos los registros 
					$lb_valido = $this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
					if(!$lb_valido)
					{
						$this->io_msg->message("".$this->io_sigesp_int->is_msg_error);
					}		   
				}
				if (!$lb_valido) 
				{	
				  $this->io_msg->message("Error al reversar integraci�n del comprobante concepto N� ".$ls_comprobante);
				}
			}
			else
			{
				$this->io_msg->message("ERROR-> No existe el comprobante N� ".$ls_comprobante."-".$this->is_procede.".");
			  	$lb_valido = false;
			}
		}
		if($lb_valido)
		{	// Actualizar el estatus del per�odo 
			$lb_valido=$this->uf_update_estatus_periodo_nomina($as_periodo,$as_nomina,0);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sno($this->is_codemp,$as_nomina,$as_periodo,$as_comprobante,
																'1900-01-01','1900-01-01');
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso la Contabilizaci�n N�mina Aportes <b>".$as_nomina."</b>, Per�odo <b>".$as_periodo."</b>, ".
							"Comprobante <b>".$as_comprobante."</b>, Comprobante Aporte <b>".$ls_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		// Finalizamos la transacci�n se realiza commit � rollback de acuerdo a $lb_valido
	    $this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return $lb_valido;
	} // end uf_reversar_contabilizacion_aportes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reversar_recepcion_documento_tipo_aporte($as_comprobante,$as_nomina,$as_periodo,$ad_fechaconta,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_recepcion_documento_tipo_aporte
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   as_nomina  // C�digo de N�mina
		//				   as_periodo  // C�digo de Per�odo de la N�mina
		//				   ad_fechaconta  // Fecha en que fue contabilizado el documento
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el reverso correctamente
		//	  Description: M�todo que reversa  las recepciones de documento por cada aporte definido en la nomina por periodo calculado
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 26/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
	    if(!$this->uf_obtener_data_aportes($as_comprobante))
		{
			return false;
		}
		$ll_tot_row = $this->dts_nomina_aporte->getRowCount("codcomapo");
		// inicia transacci�n SQL
		$this->io_sigesp_int->uf_int_init_transaction_begin(); 
	    for($ll_row=1; ($ll_row<=$ll_tot_row)&&($lb_valido);$ll_row++)
		{
			$ls_comprobante=$this->dts_nomina_aporte->getValue("codcomapo",$ll_row);	
			$ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante(trim($ls_comprobante));
			$ls_cod_pro=$this->dts_nomina_aporte->getValue("cod_pro",$ll_row);	
			$ls_ced_bene=$this->dts_nomina_aporte->getValue("ced_bene",$ll_row);	
			$ls_codtipdoc=$this->dts_nomina_aporte->getValue("codtipdoc",$ll_row);		
								
            if($lb_valido)
			{
				$lb_valido=$this->uf_reversar_recepcion_documento($ls_comprobante,$ll_row,$ad_fechaconta,$ls_cod_pro,$ls_ced_bene,$ls_codtipdoc,$aa_seguridad);
			}
	        if($lb_valido)
			{
				$lb_valido=$this->uf_update_estatus_nomina_aporte($as_periodo,$as_nomina,$as_comprobante,$ls_comprobante,0);
			}
		}
		if($lb_valido)
		{	// Actualizar el estatus del per�odo 
			$lb_valido=$this->uf_update_estatus_periodo_nomina($as_periodo,$as_nomina,0);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sno($this->is_codemp,$as_nomina,$as_periodo,$as_comprobante,
																'1900-01-01','1900-01-01');
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso la Recepci�n de Documento N�mina Aportes <b>".$as_nomina."</b>, Per�odo <b>".$as_periodo."</b> ".
							"Comprobante <b>".$as_comprobante."</b>, Comprobante Aporte <b>".$ls_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		// Fin de la transacci�n SQL se hace commit � rolback de acuerdo al $lb_valido
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);				
		return $lb_valido ;
    }   // end function	uf_reversar_recepcion_documento_tipo_aporte
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_total_monto($as_comprobante,$as_codcomapo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_total_monto
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   as_codcomapo  // C�digo comprobante aporte
		//	      Returns: lb_valido True si se ejecuto el reverso correctamente
		//	  Description: Este metodo calcula la sumatoria del monto total de los movimientos de gastos
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 26/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_existe=false;		
		switch($this->is_tiponomina)
		{
			case "A":
				$ls_sql="SELECT SUM(monto) as monto ".
						"  FROM sno_dt_spg ".
						" WHERE codemp='".$this->is_codemp."' ".
						"   AND codcom='".$as_comprobante."' ".
						"   AND codcomapo='".$as_codcomapo."'";
				break;
			default:
				$ls_sql="SELECT SUM(monto) as monto ".
						"  FROM sno_dt_spg ".
						" WHERE codemp='".$this->is_codemp."' ".
						"   AND codcom='".$as_comprobante."'";
				break;
		 }
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_obtener_total_monto ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
            while($row=$this->io_sql->fetch_row($rs_data) )
		    {
				$ldec_monto=$row["monto"];
				break;		
			}	
		}
		$this->io_sql->free_result($rs_data);
		return $ldec_monto;
	} // end uf_obtener_total_monto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_fecha_contabilizado_sno($as_codemp,$as_codnom,$as_codperi,$as_comprobante,$ad_fechaconta,$ad_fechaanula)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_fecha_contabilizado_sno
		//		   Access: private
		//	    Arguments: as_codemp  // C�digo
		//                 as_codnom  // C�digo de N�mina
		//                 as_codperi  // C�digo de Per�odo
		//                 as_comprobante  // Comprobante
		//                 ad_fecha  // Fecha de contabilizaci�n � de Anulaci�n
		//	      Returns: lb_valido True si se ejecuto la contabilizaci�n correctamente
		//	  Description: M�todo que actualiza la solicitud en estatus contabilizado
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 07/11/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_campo1="";
		$ls_campo2="";
		if($ad_fechaconta!="")
		{
			$ls_campo1=" fechaconta='".$ad_fechaconta."' ";
		}
		if($ad_fechaanula!="")
		{
			$ls_campo2=" fechaanula='".$ad_fechaanula."' ";
		}
		if($ls_campo1!="")
		{
			if($ls_campo2!="")
			{
				$ls_campos=$ls_campo1.", ".$ls_campo2;
			}
			else
			{
				$ls_campos=$ls_campo1;
			}
		}
		else
		{
			$ls_campos=$ls_campo2;
		}
		$ls_sql="UPDATE sno_dt_scg ".
		        "   SET ".$ls_campos.
                " WHERE codemp='".$as_codemp."' ".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND codcom='".$as_comprobante."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
            $this->io_msg->message("CLASE->Integraci�n SEP M�TODO->uf_update_fecha_contabilizado_sno ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		if($lb_valido)
		{
			$ls_sql="UPDATE sno_dt_spg ".
					"   SET ".$ls_campos.
					" WHERE codemp='".$as_codemp."' ".
					"   AND codnom='".$as_codnom."'".
					"   AND codperi='".$as_codperi."'".
					"   AND codcom='".$as_comprobante."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SEP M�TODO->uf_update_fecha_contabilizado_sno ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		if($lb_valido)
		{
			$ls_sql="UPDATE sno_dt_spi ".
					"   SET ".$ls_campos.
					" WHERE codemp='".$as_codemp."' ".
					"   AND codnom='".$as_codnom."'".
					"   AND codperi='".$as_codperi."'".
					"   AND codcom='".$as_comprobante."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SEP M�TODO->uf_update_fecha_contabilizado_sno ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_update_fecha_contabilizado_sno
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_contabilizacion_ingresos($as_comprobante,$adt_fecha,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_ingresos
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   adt_fecha  // Fecha de contabilizaci�n
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto la contabilizaci�n correctamente
		//	  Description: Funcion que procesa la contabilizaci�n de ingresos de una N�mina 
		//	   Creado Por: Ing. Yesenia Moreno
		// Modificado Por: 										Fecha �ltima Modificaci�n : 26/03/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $ls_procede="SNOCNO";
		$this->is_procede="SNOCNO";
		$ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante(trim($as_comprobante));
		$ls_cod_pro=$this->dts_nomina->getValue("cod_pro",1);	
		$ls_ced_bene=$this->dts_nomina->getValue("ced_bene",1);	
		$ls_descripcion=$this->dts_nomina->getValue("descripcion",1);	
        $ls_tipo_destino=$this->dts_nomina->getValue("tipo_destino",1);			
        $ls_mensaje=$this->dts_nomina->getValue("operacion",1);
		$li_estatus=$this->dts_nomina->getValue("estatus",1);
		$ls_nomina=$this->dts_nomina->getValue("codnom",1); 
		$ls_periodo=$this->dts_nomina->getValue("codperi",1);  
        $ls_tipnom=$this->dts_nomina->getValue("tipnom",1);  
        $li_estnotdeb=$this->dts_nomina->getValue("estnotdeb",1); 
        if($ls_tipo_destino=="B")
		{
			$ls_codigo_destino=$ls_ced_bene;
		}
		if($ls_tipo_destino=="P")
		{
			$ls_codigo_destino=$ls_cod_pro;
		}
		if($ls_tipo_destino=="-")
		{
			$ls_codigo_destino="----------";
		}
		if($li_estatus==1) 
		{
		   $this->io_msg->message("La N�mina debe estar en estatus EMITIDA para su contabilizaci�n.");
		   return false;
		}
		$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);
		// Creo la cabecera del Comprobante
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
		$this->as_procede=$ls_procede;
		$this->as_comprobante=$ls_comprobante;
		$this->ad_fecha=$adt_fecha;
		$this->as_codban=$ls_codban;
		$this->as_ctaban=$ls_ctaban;
		$lb_valido=$this->io_sigesp_int->uf_int_init($this->is_codemp,$ls_procede,$ls_comprobante,$adt_fecha,$ls_descripcion,
													 $ls_tipo_destino,$ls_codigo_destino,false,$ls_codban,$ls_ctaban,$li_tipo_comp);
		$this->io_sigesp_int->uf_int_config(false,false);
		if (!$lb_valido)
		{   
           $this->io_msg->message($this->io_sigesp_int->is_msg_error); 
		   return false;		   		   
		}
		// inicia transacci�n SQL
		$this->io_sigesp_int->uf_int_init_transaction_begin();
		if($lb_valido)
		{
			// Se procesan los detalles de presupuesto de ingreso
			$lb_valido=$this->uf_procesar_detalles_ingreso($as_comprobante);  
		}
		if ($lb_valido)
        {	// Se procesan los detalles de Contabilidad
			$lb_valido = $this->uf_procesar_detalles_contables($as_comprobante,""); 
			if ($lb_valido)
			{	// Se inserta el comprobante con sus detalles contables y presupuestarios
				$lb_valido = $this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				if (!$lb_valido) 
				{ 
					if (!empty($this->io_sigesp_int->is_msg_error))
					{
						$this->io_msg->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
					}	
				}
			}
			if($lb_valido)
			{	// Se Actualiza el estatus de la n�mina que est� contabilizada
				$lb_valido=$this->uf_update_estatus_nomina($ls_periodo,$ls_nomina,$as_comprobante,$ls_tipnom,1);
			}
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sno($this->is_codemp,$ls_nomina,$ls_periodo,$ls_comprobante,
															    $adt_fecha,'1900-01-01');
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Contabiliz� la N�mina <b>".$ls_nomina."</b>, Per�odo <b>".$ls_periodo."</b>, ".
							"Comprobante <b>".$ls_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		// Se Finaliza la transacci�n con Commit � Rollback de acuerdo al $lb_valido
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);		
		return  $lb_valido;
	} // end function uf_procesar_contabilizacion_ingresos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_configuracion($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_configuracion
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci�n a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 07/05/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT value ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->SNO M�TODO->uf_select_configuracion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			//$this->io_mensajes->message("CLASE->Integraci�n SNO M�TODO->uf_select_configuracion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["value"];
			}
			$this->io_sql->free_result($rs_data);		
		}
		return rtrim($ls_valor);
	}// end function uf_select_configuracion
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_config_nomina($as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_config_nomina
		//		   Access: public
		//	    Arguments: as_codnom  // Codigo de Nomina
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 07/05/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT recdocpagperche ".
				"  FROM sno_nomina ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codnom='".$as_codnom."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_select_config_nomina ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["recdocpagperche"];
			}
			$this->io_sql->free_result($rs_data);		
		}
		return rtrim($ls_valor);
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generar_recepcion_documento_personal_cheque($as_nomina,$as_periodo,$adt_fecha,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generar_recepcion_documento_personal_cheque
		//		   Access: public
		//	    Arguments: as_nomina  // Nomina a contabilizar
		//				   as_periodo  // Periodo actual 
		//				   adt_fecha  // Fecha de movimiento
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que genera las recepciones de documento a las personas que cobran por cheque
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 05/02/2009 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$lb_valido=true;
//		$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);
		//print('******************************************');
		 $ls_sql="SELECT codper,MAX(codtipdoc) as  codtipdoc, MAX(monpagper) as monpagper".
				"  FROM sno_rd ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codnom='".$as_nomina."' ".
				"   AND codperi='".$as_periodo."' ".
				"   AND estcon='0'".
				" GROUP BY codnom,codperi,codper";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_generar_recepcion_documento_personal_cheque ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$lb_valido=false;
				$ls_codper=$row["codper"];
				$ls_codtipdoc=$row["codtipdoc"];
				$li_monpagper=$row["monpagper"];
				$ls_concepto= "PAGO NOMINA ".$as_nomina.", PERIODO ".$as_periodo.". BENEFICIARIO ".$ls_codper;
				
				$ls_numrecdoc="N".substr($ls_codper,1,8).$as_nomina.substr($as_periodo,1,2);
				
				$ls_cedbene=$this->uf_validar_beneficiario($ls_codper);
				if($ls_cedbene!="")
				{
					$lb_valido=$this->uf_insert_recepcion_documento($ls_numrecdoc,$ls_cedbene,$ls_codtipdoc,$ls_concepto,$adt_fecha,$li_monpagper,"--");
				}
				else
				{
					$this->io_msg->message("El personal de C�digo ".$ls_codper." No esta registrado como beneficiario."); 
				}
				if($lb_valido)
				{
					$lb_valido=$this->uf_insert_contable_rd($as_nomina,$as_periodo,$ls_numrecdoc,$ls_codper,$ls_codtipdoc,$ls_cedbene);
				}
				if($lb_valido)
				{
					$lb_valido=$this->uf_update_estatus_rd($as_nomina,$as_periodo,$ls_codper,"1");
				}
				if($lb_valido)
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="PROCESS";
					$ls_descripcion="Genero la R.D. para el personal <b>".$ls_codper."</b>, Nomina <b>".$as_nomina."</b> y Per�odo <b>".$as_periodo."</b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////
				}
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $lb_valido;
	}// end function uf_generar_recepcion_documento_personal_cheque
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generar_pago_directo_personal_cheque($as_nomina,$as_periodo,$adt_fecha,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generar_pago_directo_personal_cheque
		//		   Access: public
		//	    Arguments: as_nomina  // Nomina a contabilizar
		//				   as_periodo  // Periodo actual 
		//				   adt_fecha  // Fecha de movimiento
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que genera las recepciones de documento a las personas que cobran por cheque
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 05/02/2009 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$lb_valido=true;
//		$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);
		$ls_sql="SELECT sno_hpersonalnomina.codper, MAX(sno_hresumen.monnetres) as monnetres, MAX(sno_hpersonalnomina.cueaboper) as cueaboper".
				"  FROM sno_hpersonalnomina,sno_hresumen ".
				" WHERE sno_hpersonalnomina.codemp='".$this->is_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$as_nomina."' ".
				"   AND sno_hpersonalnomina.codperi='".$as_periodo."' ".
				"   AND sno_hpersonalnomina.pagefeper='1'".
				"   AND sno_hpersonalnomina.codemp=sno_hresumen.codemp".
				"   AND sno_hpersonalnomina.codnom=sno_hresumen.codnom".
				"   AND sno_hpersonalnomina.codperi=sno_hresumen.codperi".
				"   AND sno_hpersonalnomina.codper=sno_hresumen.codper".
				" GROUP BY sno_hpersonalnomina.codnom,sno_hpersonalnomina.codperi,sno_hpersonalnomina.codper";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_generar_pago_directo_personal_cheque ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$ls_banco=$this->uf_select_config("SNO", "NOMINA", "BANCO PERSONAL CHEQUE", "", "C");
			$ls_ctabanco=$this->uf_select_config("SNO", "NOMINA", "CTA. PERSONAL CHEQUE", "", "C");
			$ls_cuentabanco=$this->uf_select_cuentacontablebanco($ls_banco,$ls_ctabanco);
			if((trim($ls_banco)!="")&&(trim($ls_ctabanco)!="")&&(trim($ls_cuentabanco)!=""))
			{
				while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
				{
					$ls_codper=$row["codper"];
					$li_monpagper=$row["monnetres"];
					$ls_concepto= "PAGO NOMINA ".$as_nomina.", PERIODO ".$as_periodo.". BENEFICIARIO ".$ls_codper;
					$ls_numrecdoc=substr($ls_codper,1,8).$as_nomina.substr($as_periodo,1,2)."N";
					$ls_cedbene=$this->uf_validar_beneficiario($ls_codper);
					$ls_comprobante=$as_nomina.$as_periodo.substr($ls_codper,1,8);
					$ls_cuentapasivo=$row["cueaboper"];
					$lb_valido=$this->uf_insert_movimiento_pagodirecto($ls_banco,$ls_ctabanco,$ls_comprobante,$adt_fecha,$ls_concepto,
																       $li_monpagper,"B","----------",$ls_cedbene,$ls_cuentabanco,$ls_cuentapasivo);
				}
			}
			else
			{
				$this->io_msg->message("Existe un error en la configuracion del banco a personas que cobran por cheque");
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_generar_pago_directo_personal_cheque
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cuentacontablebanco($as_banco,$as_ctabanco)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cuentacontablebanco
		//		   Access: public
		//	    Arguments: as_codper  // Codigo de Personal
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que obtiene la cedula del beneficiario de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 07/05/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT sc_cuenta ".
				"  FROM scb_ctabanco ".
				" WHERE scb_ctabanco.codemp='".$this->is_codemp."' ".
				"   AND scb_ctabanco.codban='".$as_banco."' ".
				"   AND scb_ctabanco.ctaban='".$as_ctabanco."'"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_select_cuentacontablebanco ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["sc_cuenta"];
			}
			$this->io_sql->free_result($rs_data);		
		}
		return rtrim($ls_valor);
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	

	function uf_generar_voucher()
	{
		require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
		$io_keygen= new sigesp_c_generar_consecutivo();
		$codigo= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","chevau","SCBBCH",25,"","","");
		unset($io_keygen);
		$ls_codigo=$this->io_function->uf_cerosizquierda($codigo,25);
		return $ls_codigo;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_movimiento_pagodirecto($as_codban,$as_ctaban,$as_comprobante,$adt_fecha,$as_descripcion,$adec_monto,
										$as_tipo_destino,$as_cod_pro,$as_ced_bene,$as_cuentabanco,$as_cuentapasivo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_movimiento_pagodirecto
		//		   Access: private
		//	    Arguments: as_codban  // C�digo del Banco
		//				   as_ctaban  // C�digo de la cuenta del banco
		//				   as_comprobante  // N�mero de Comprobante
		//				   adt_fecha  // Fecha de contabilizaci�n
		//				   as_descripcion  // Descripci�n de la Contabilizaci�n
		//				   adec_monto  // Monto de la nota de Cr�dito
		//	      Returns: lb_valido True si se ejecuto la contabilizaci�n correctamente
		//	  Description: Funcion que procesa la contabilizaci�n de una N�mina
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SNOCNO";
		$ls_chevau=$this->uf_generar_voucher();
		$ls_sql="INSERT INTO scb_movbco(codemp,codban,ctaban,numdoc,codope,estmov,cod_pro,ced_bene,tipo_destino, codconmov,".
		        "                       fecmov, conmov, nomproben, monto, estbpd, estcon, estcobing, esttra, chevau, estimpche, ".
				"                       monobjret, monret, procede, comprobante, fecha, id_mco, emicheproc, emicheced, emichenom, ".
				"                       emichefec, estmovint, codusu, codopeidb, aliidb, feccon, estreglib, numcarord, numpolcon,".
				"                       coduniadmsig,codbansig,fecordpagsig,tipdocressig,  numdocressig,estmodordpag,codfuefin,".
				"                       forpagsig,medpagsig,codestprosig) ".
				" VALUES ('".$this->is_codemp."','".$as_codban."','".$as_ctaban."','".$as_comprobante."','CH','N','".$as_cod_pro."','".$as_ced_bene."',".
				"         '".$as_tipo_destino."','---','".$adt_fecha."','".$as_descripcion."','Ninguno',".$adec_monto.",".
  				"         'M',0,0,0,'".$ls_chevau."',0,0,0,' ',' ','1900-01-01',' ',0,' ',' ','1900-01-01',0,'ninguno',".
 				"         ' ',0,'1900-01-01',0,' ',0,' ',' ','1900-01-01',' ',' ',0,' ',' ',' ',' ')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{   
           $this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_movimiento_pagodirecto ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
		   return false;
		}
		if($lb_valido)
		{
			$ls_sql="INSERT INTO scb_movbco_scg (codemp, codban, ctaban, numdoc, codope, estmov, scg_cuenta, debhab, codded, ".
					"documento, desmov, procede_doc, monto, monobjret) VALUES ('".$this->is_codemp."','".$as_codban."',".
					"'".$as_ctaban."','".$as_comprobante."','CH','N','".$as_cuentapasivo."','D','00000',".
					"'".$as_comprobante."','".$as_descripcion."','".$ls_procede."',".$adec_monto.",0)";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		if($lb_valido)
		{
			$ls_sql="INSERT INTO scb_movbco_scg (codemp, codban, ctaban, numdoc, codope, estmov, scg_cuenta, debhab, codded, ".
					"documento, desmov, procede_doc, monto, monobjret) VALUES ('".$this->is_codemp."','".$as_codban."',".
					"'".$as_ctaban."','".$as_comprobante."','CH','N','".$as_cuentabanco."','H','00000',".
					"'".$as_comprobante."','".$as_descripcion."','".$ls_procede."',".$adec_monto.",0)";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		return $lb_valido;
    } // end function uf_insert_movimiento_banco
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_insert_recepcion_documento($as_numrecdoc,$as_cedbene,$as_codtipdoc,$as_concepto,$ad_fecha,$ai_monto,$as_codfuefin)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento
		//		   Access: private
		//	    Arguments: $as_numrecdoc    // Codigo de Comprobante
		//				   $as_cedbene 		// cedula de beneficiario
		//				   $as_codtipdoc	// codigo de tipo de documento
		//				   $as_descripcion	// descripcion del documento
		//				   $ad_fecha  		// Fecha de contabilizaci�n
		//				   $ai_monto  		// Monto de contabilizaci�n
		//                 $as_codfuefin    // C�digo de la fuente de financiamiento
		//	      Returns: $lb_valido True si se genero la recepci�n de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 05/02/2009 								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
        $ls_tipodestino= "B";			
		$ls_codpro= "----------";	
		 $ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,".
 		        "                    montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,".
				"                    fecaprord,usuaprord,estimpmun,codcla,codfuefin,codrecdoc)".
				"     VALUES ('".$this->is_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$as_cedbene."',".
				"             '".$ls_codpro."','".$as_concepto."','".$ad_fecha."','".$ad_fecha."','".$ad_fecha."',
				"               .$ai_monto.",0,0,'".$ls_tipodestino."','".$as_numrecdoc."','R','SNOCNO',0,0,'1900-01-01','',0,'--','".$as_codfuefin."','".$as_numrecdoc."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}  // end function uf_insert_recepcion_documento
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_insert_contable_rd($as_nomina,$as_periodo,$as_numrecdoc,$as_codper,$as_codtipdoc,$as_cedbene)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_contable_rd
		//		   Access: private
		//	    Arguments: $as_comprobante // C�digo de Comprobante
		//				   $as_codtipdoc   // Tipo de Documento
		//				   $as_cedbene     // C�dula del Beneficiario
		//				   $as_codpro      // C�digo del Proveedor
		//				   $ai_monto       // monto del comprobante
		//	      Returns: $lb_valido True si se inserto los detalles contables en la recepci�n de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 07/11/2006 								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SNOCNO";
		$ls_codpro= "----------";	
		$ls_sql="SELECT sc_cuenta,debhab,monpagper".
				"  FROM sno_rd ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codnom='".$as_nomina."' ".
				"   AND codperi='".$as_periodo."'".
				"   AND codper='".$as_codper."' "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_contable_rd_S ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ls_sccuenta= $row["sc_cuenta"];
				$ls_debhab= $row["debhab"];				
				$li_monpagper= $row["monpagper"];								 
				$ls_sql="INSERT INTO cxp_rd_scg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,".
						"						 sc_cuenta,monto)".
						"     VALUES ('".$this->is_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$as_cedbene."',".
						"             '".$ls_codpro."','".$ls_procede."','".$as_numrecdoc."','".$ls_debhab."',".
						"             '".$ls_sccuenta."',".$li_monpagper.")";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_insert_contable_rd_I ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
				    $lb_valido=false;
				    break;
				}
			} // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_contable_rd
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_update_estatus_rd($as_nomina,$as_periodo,$as_codper,$as_estatus)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_rd
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa 
		//                 $as_codsolvia // codigo de solicitud de viaticos
		//                 $ai_monsolvia    // codigo de mision
		//				   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica un maestro de solicitud de viaticos en la tabla scv_solicitudviatico
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 06/11/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 	$lb_valido=true;
		$ls_sql="UPDATE sno_rd".
				"   SET estcon='".$as_estatus."'".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codnom='".$as_nomina."' ".
				"   AND codperi='".$as_periodo."'".
				"   AND codper='".$as_codper."' ";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_update_estatus_rd ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
		}
	    return $lb_valido;
	} // end  function uf_update_estatus_rd
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_beneficiario($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_beneficiario
		//		   Access: public
		//	    Arguments: as_codper  // Codigo de Personal
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que obtiene la cedula del beneficiario de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 07/05/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT rpc_beneficiario.ced_bene ".
				"  FROM rpc_beneficiario,sno_personal ".
				" WHERE sno_personal.codemp='".$this->is_codemp."' ".
				"   AND sno_personal.codper='".$as_codper."' ".
				"   AND sno_personal.codemp=rpc_beneficiario.codemp".
				"   AND sno_personal.cedper=rpc_beneficiario.ced_bene"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_select_config_nomina ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["ced_bene"];
			}
			$this->io_sql->free_result($rs_data);		
		}
		return rtrim($ls_valor);
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reversar_recepcion_documento_personal_cheque($as_nomina,$as_periodo,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_recepcion_documento_personal_cheque
		//		   Access: public
		//	    Arguments: as_nomina  // Nomina a contabilizar
		//				   as_periodo  // Periodo actual 
		//				   adt_fecha  // Fecha de movimiento
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que genera las recepciones de documento a las personas que cobran por cheque
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 05/02/2009 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$lb_valido=true;
//		$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);
		$ls_sql="SELECT codper,MAX(codtipdoc) as  codtipdoc, MAX(monpagper) as monpagper".
				"  FROM sno_rd ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codnom='".$as_nomina."' ".
				"   AND codperi='".$as_periodo."' ".
				"   AND estcon='1'".
				" GROUP BY codnom,codperi,codper";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento_personal_cheque ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$lb_existe=false;
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ls_codper=$row["codper"];
				$ls_codtipdoc=$row["codtipdoc"];
				$li_monpagper=$row["monpagper"];
				//$ls_numrecdoc=$ls_codper."-".$as_periodo."N";
				$ls_numrecdoc=substr($ls_codper,1,8).$as_nomina.substr($as_periodo,1,2)."N";
				$ls_cedbene=$this->uf_validar_beneficiario($ls_codper);
				if($ls_cedbene!="")
				{
					$lb_existe=$this->uf_validar_recepcion_documento($ls_numrecdoc,$ls_cedbene,$ls_codtipdoc,&$ls_estprodoc);
				}
				if($lb_existe)
				{
					if($ls_estprodoc!="R")
					{
						$this->io_msg->message("La Recepcion de Documentos ".$ls_numrecdoc." debe estar en estatus de Registro");
						$lb_valido=false;
					}
					else
					{
						$lb_valido=$this->uf_delete_recepcion_documento_personal_cheque($as_nomina,$as_periodo,$ls_codper,
																						  $ls_numrecdoc,$ls_cedbene,$ls_codtipdoc,
																						  $aa_seguridad);
					}
				}
				else
				{
					$ls_numrecdoc=$ls_codper."-".$as_periodo."N";
					if($ls_cedbene!="")
					{
						$lb_existe=$this->uf_validar_recepcion_documento($ls_numrecdoc,$ls_cedbene,$ls_codtipdoc,&$ls_estprodoc);
					}
					if($lb_existe)
					{
						if($ls_estprodoc!="R")
						{
							$this->io_msg->message("La Recepcion de Documentos ".$ls_numrecdoc." debe estar en estatus de Registro");
							$lb_valido=false;
						}
						else
						{
							$lb_valido=$this->uf_delete_recepcion_documento_personal_cheque($as_nomina,$as_periodo,$ls_codper,
																							  $ls_numrecdoc,$ls_cedbene,$ls_codtipdoc,
																							  $aa_seguridad);
						}
					}
				}
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $lb_valido;
	}// end function uf_reversar_recepcion_documento_personal_cheque
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_recepcion_documento($as_numrecdoc,$as_cedbene,$as_codtipdoc,&$as_estprodoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_recepcion_documento
		//		   Access: public
		//	    Arguments: as_codper  // Codigo de Personal
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que obtiene la cedula del beneficiario de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 07/05/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=false;
		$ls_codpro= "----------";	
		$as_estprodoc="";
		$ls_sql="SELECT numrecdoc,estprodoc ".
				"  FROM cxp_rd ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND numrecdoc='".$as_numrecdoc."' ".
				"   AND cod_pro='".$ls_codpro."'".
				"   AND ced_bene='".$as_cedbene."'".
				"   AND codtipdoc='".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_validar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=true;
				$as_estprodoc=$row["estprodoc"];
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $lb_existe;
	}// end function uf_validar_recepcion_documento
	//-----------------------------------------------------------------------------------------------------------------------------------	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_recepcion_documento_personal_cheque($as_nomina,$as_periodo,$as_codper,$as_numrecdoc,$as_cedbene,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_recepcion_documento_personal_cheque
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   ad_fechaconta  // Fecha en que fue contabilizado el Documento
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el reverso correctamente
		//	  Description: Este metodo elimina la recepci�n de documento de una n�mina
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_cod_pro="----------";
		// Eliminamos los Detalles Contables
		$ls_sql="DELETE ".
				"  FROM cxp_rd_scg ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND numrecdoc='".$as_numrecdoc."' ".
				"   AND codtipdoc='".$as_codtipdoc."' ".
				"   AND cod_pro='".$ls_cod_pro."' ".
				"   AND ced_bene='".$as_cedbene."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		if ($lb_valido)
		{
			// Eliminamos los Detalles Presupuestarios
			$ls_sql="DELETE ".
					"  FROM cxp_rd_spg ".
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND numrecdoc='".$as_numrecdoc."' ".
					"   AND codtipdoc='".$as_codtipdoc."' ".
					"   AND cod_pro='".$ls_cod_pro."' ".
					"   AND ced_bene='".$as_cedbene."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
			if ($lb_valido)
			{
				// Eliminamos los Hist�ricos de La Recepci�n de Documento
				$ls_sql="DELETE ".
						"  FROM cxp_historico_rd ".
						" WHERE codemp='".$this->is_codemp."' ".
						"   AND numrecdoc='".$as_numrecdoc."' ".
						"   AND codtipdoc='".$as_codtipdoc."' ".
						"   AND cod_pro='".$ls_cod_pro."' ".
						"   AND ced_bene='".$as_cedbene."'";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					$lb_valido=false;
				}
			}
			if ($lb_valido)
			{
				// Eliminamos La Recepci�n de Documento
				$ls_sql="DELETE ".
						"  FROM cxp_rd ".
						" WHERE codemp='".$this->is_codemp."' ".
						"   AND numrecdoc='".$as_numrecdoc."' ".
						"   AND codtipdoc='".$as_codtipdoc."' ".
						"   AND cod_pro='".$ls_cod_pro."' ".
						"   AND ced_bene='".$as_cedbene."'";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					$lb_valido=false;
				}
			}
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_estatus_rd($as_nomina,$as_periodo,$as_codper,"0");
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso la Recepci�n de Documento <b>".$as_numrecdoc."</b>, Per�odo <b>".$as_periodo."</b> ".
							"Beneficiario <b>".$as_codper."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		return $lb_valido;
	}  // end function uf_reversar_recepcion_documento
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reversar_pagodirecto_personal_cheque($as_nomina,$as_periodo,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_pagodirecto_personal_cheque
		//		   Access: public
		//	    Arguments: as_nomina  // Nomina a contabilizar
		//				   as_periodo  // Periodo actual 
		//				   adt_fecha  // Fecha de movimiento
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que genera las recepciones de documento a las personas que cobran por cheque
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 05/02/2009 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$lb_valido=true;
		$ls_sql="SELECT sno_hpersonalnomina.codper, MAX(sno_hresumen.monnetres) as monnetres, MAX(sno_hpersonalnomina.cueaboper) as cueaboper".
				"  FROM sno_hpersonalnomina,sno_hresumen ".
				" WHERE sno_hpersonalnomina.codemp='".$this->is_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$as_nomina."' ".
				"   AND sno_hpersonalnomina.codperi='".$as_periodo."' ".
				"   AND sno_hpersonalnomina.pagefeper='1'".
				"   AND sno_hpersonalnomina.codemp=sno_hresumen.codemp".
				"   AND sno_hpersonalnomina.codnom=sno_hresumen.codnom".
				"   AND sno_hpersonalnomina.codperi=sno_hresumen.codperi".
				"   AND sno_hpersonalnomina.codper=sno_hresumen.codper".
				" GROUP BY sno_hpersonalnomina.codnom,sno_hpersonalnomina.codperi,sno_hpersonalnomina.codper";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_pagodirecto_personal_cheque ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$lb_existe=false;
			$ls_banco=$this->uf_select_config("SNO", "NOMINA", "BANCO PERSONAL CHEQUE", "", "C");
			$ls_ctabanco=$this->uf_select_config("SNO", "NOMINA", "CTA. PERSONAL CHEQUE", "", "C");
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ls_codper=$row["codper"];
				$ls_numrecdoc=substr($ls_codper,1,8).$as_nomina.substr($as_periodo,1,2)."N";
				$ls_cedbene=$this->uf_validar_beneficiario($ls_codper);
				$ls_comprobante=$as_nomina.$as_periodo.substr($ls_codper,1,8);
				if($ls_cedbene!="")
				{
					$lb_existe=$this->uf_validar_pagodirecto($ls_banco,$ls_ctabanco,$ls_comprobante,&$ls_estmov);
				}
				if($lb_existe)
				{
					if($ls_estmov!="N")
					{
						$this->io_msg->message("El Movimiento de Banco ".$ls_comprobante." debe estar en estatus de Registro");
						$lb_valido=false;
					}
					else
					{
						$lb_valido=$this->uf_delete_pagodirecto_personal_cheque($as_nomina,$as_periodo,$ls_banco,$ls_ctabanco,$ls_comprobante,
																						  $aa_seguridad);
					}
				}
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $lb_valido;
	}// end function uf_reversar_recepcion_documento_personal_cheque
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_pagodirecto($as_banco,$as_ctabanco,$as_comprobante,&$as_estmov)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_pagodirecto
		//		   Access: public
		//	    Arguments: as_codper  // Codigo de Personal
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que obtiene la cedula del beneficiario de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 07/05/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=false;
		$ls_codpro= "----------";	
		$as_estmov="";
		$ls_sql="SELECT estmov ".
				"  FROM scb_movbco ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codban='".$as_banco."' ".
				"   AND ctaban='".$as_ctabanco."'".
				"   AND numdoc='".$as_comprobante."'".
				"   AND codope='CH'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_validar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=true;
				$as_estmov=$row["estmov"];
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $lb_existe;
	}// end function uf_validar_recepcion_documento
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_pagodirecto_personal_cheque($as_nomina,$as_periodo,$as_banco,$as_ctabanco,$as_comprobante,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_recepcion_documento_personal_cheque
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   ad_fechaconta  // Fecha en que fue contabilizado el Documento
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el reverso correctamente
		//	  Description: Este metodo elimina la recepci�n de documento de una n�mina
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_cod_pro="----------";
		// Eliminamos los Detalles Contables
		$ls_sql="DELETE ".
				"  FROM scb_movbco_scg ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codban='".$as_banco."' ".
				"   AND ctaban='".$as_ctabanco."'".
				"   AND numdoc='".$as_comprobante."'".
				"   AND codope='CH'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		if ($lb_valido)
		{
			// Eliminamos los Detalles Presupuestarios
			$ls_sql="DELETE ".
					"  FROM scb_movbco ".
					" WHERE codemp='".$this->is_codemp."' ".
					"   AND codban='".$as_banco."' ".
					"   AND ctaban='".$as_ctabanco."'".
					"   AND numdoc='".$as_comprobante."'".
					"   AND codope='CH'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso el pago directo <b>".$as_comprobante."</b>, Nomina <b>".$as_periodo."</b>, Per�odo <b>".$as_periodo."</b> ";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		return $lb_valido;
	}  // end function uf_reversar_recepcion_documento
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_recepcion_documento_guarderias($as_comprobante,$adt_fecha,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_recepcion_documento_guarderias
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   adt_fecha  // Fecha de contabilizaci�n
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se genero la recepci�n de documento correctamente
		//	  Description: M�todo que registra la contabilizacion solo de nomina en la recepci�n documento
		//	   Creado Por: Ing. Yesenia Moreno
		// Modificado Por:														Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		$ls_nomina = $this->dts_nomina->getValue("codnom",1); 
		$ls_periodo = $this->dts_nomina->getValue("codperi",1);  
		$ls_anocurnom = $this->dts_nomina->getValue("anocurnom",1);  
        $ls_tipnom = $this->dts_nomina->getValue("tipnom",1);  //PARA SABER SI LA NOMINA ES DE OBREROS � NO
		$ls_codtipdoc = trim($this->uf_select_configuracion("SNO","CONFIG","GUARDERIA","C","")); 
		$ls_cod_pro="----------";
		$ls_tipo_destino="B";
		$ls_descripcion = $this->dts_nomina->getValue("descripcion",1);  
		$adt_fecha = $this->io_function->uf_convertirdatetobd($adt_fecha);
		$ls_spg_cuentaobrero= trim($this->uf_select_configuracion("SNO","NOMINA","DESTINO GUARDERIA OBRERO","C","")); 
		$ls_spg_cuentapersonal= trim($this->uf_select_configuracion("SNO","NOMINA","DESTINO GUARDERIA PERSONAL","C","")); 
		$this->io_sql->begin_transaction();
		$ls_sql="SELECT sno_guarderias.cedbene, sno_guarderias.nombene, sno_hsalida.codper, sno_guarderias.monto as valsal,".
				"       sno_personal.cedper, SUBSTR(sno_guarderias.codguar,7,4) AS codguar,sno_hunidadadmin.codprouniadm, ".
				"       sno_hunidadadmin.estcla,sno_nomina.tippernom".
				"  FROM sno_hconcepto, sno_hsalida, sno_hpersonalnomina, sno_guarderias, sno_personal,sno_hunidadadmin,sno_nomina   ".
				" WHERE sno_hconcepto.codemp='".$this->is_codemp."' ".
				"   AND sno_hconcepto.codnom='".$ls_nomina."' ".
				"   AND sno_hconcepto.anocur='".$ls_anocurnom."' ".
				"   AND sno_hconcepto.codperi='".$ls_periodo."' ".
				"   AND sno_hconcepto.guarrepcon='1'  ".
				"   AND sno_hconcepto.sigcon='R' ".
				"   AND sno_hconcepto.codemp = sno_hsalida.codemp ".
				"   AND sno_hconcepto.codnom = sno_hsalida.codnom ".
				"   AND sno_hconcepto.anocur = sno_hsalida.anocur ".
				"   AND sno_hconcepto.codperi = sno_hsalida.codperi ".
				"   AND sno_hconcepto.codconc = sno_hsalida.codconc ".
				"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_guarderias.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_guarderias.codper".
				"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_personal.codper".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm".
				"   AND sno_hconcepto.codemp = sno_nomina.codemp ".
				"   AND sno_hconcepto.codnom = sno_nomina.codnom ";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_generar_recepcion_documento_personal_cheque ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_codguar=$rs_data->fields["codguar"];
				$ldec_monto=$rs_data->fields["valsal"];
				$ls_codestpro=$rs_data->fields["codprouniadm"];
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_tippernom=$rs_data->fields["tippernom"];
				$ls_numrecdoc=$ls_periodo.$ls_codguar.$ls_cedper;
				if($ls_tippernom=="1")
				{
					$ls_spgcuenta=$ls_spg_cuentapersonal;
				}
				else
				{
					$ls_spgcuenta=$ls_spg_cuentaobrero;
				}
				if($ls_spgcuenta=="")
				{
					$this->io_msg->message("No estan definidas las cuentas presupuestarias para las Guarderias.");
					return false;
				
				}
				$ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,".
						"montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,fecaprord,usuaprord,".
						"estimpmun,codcla) VALUES ('".$this->is_codemp."','".$ls_numrecdoc."','".$ls_codtipdoc."','".$ls_cedper."',".
						"'".$ls_cod_pro."','".$ls_descripcion."','".$adt_fecha."','".$adt_fecha."','".$adt_fecha."',".$ldec_monto.
						",0,0,'".$ls_tipo_destino."','".$as_comprobante."','R','SNOCNO',0,0,'1900-01-01','',0,'--')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_procesar_recepcion_documento_guarderias ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				
				if($lb_valido)
				{	// Insertar los detalles Presupuestarios
					
					$lb_valido=$this->uf_insert_recepcion_documento_gasto_guarderias($as_comprobante,$ls_codtipdoc,$ls_cedper,
																					$ls_cod_pro,"",$ls_numrecdoc,$ls_codestpro,
																					$ls_estcla,$ldec_monto,$ls_spgcuenta,$ls_sccuenta);
				}
				if($lb_valido)
				{	// Insertar los detalles Contables
					$lb_valido=$this->uf_insert_recepcion_documento_contable_guarderias($as_comprobante,$ls_codtipdoc,$ls_cedper,
																						$ls_cod_pro,"",$ls_numrecdoc,$ldec_monto,$ls_sccuenta);
				}
				
				$rs_data->MoveNext();
			}
		}

		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Gener� las Recepciones de Documento de Guarderia para la N�mina <b>".$ls_nomina."</b>, Per�odo <b>".$ls_periodo."</b>, ".
							"Comprobante <b>".$as_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Fin de la transacci�n hace comit � Rollback de acuerdo al $lb_valido
		if($lb_valido)
		{
			$this->io_sql->commit();
		}
		else
		{
			$this->io_sql->rollback();
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);	
		return $lb_valido;
	}  // end function uf_procesar_recepcion_documento_guarderias
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reversar_recepcion_documento_guarderias($as_comprobante,$adt_fecha,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_recepcion_documento
		//		   Access: private
		//	    Arguments: as_comprobante  // C�digo de Comprobante
		//				   ad_fechaconta  // Fecha en que fue contabilizado el Documento
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el reverso correctamente
		//	  Description: Este metodo elimina la recepci�n de documento de una n�mina
		//	   Creado Por: Ing. Wilmer Brice�o
		// Modificado Por: Ing. Yesenia Moreno								Fecha �ltima Modificaci�n : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_nomina=$this->dts_nomina->getValue("codnom",1); 
		$ls_periodo=$this->dts_nomina->getValue("codperi",1);  
        $ls_tipnom=$this->dts_nomina->getValue("tipnom",1);  
		$ls_cod_pro="----------";
		$ls_estprodoc="";
		$ls_codtipdoc = trim($this->uf_select_configuracion("SNO","CONFIG","GUARDERIA","C","")); 
        $this->io_sigesp_int->uf_int_init_transaction_begin();
		$ls_sql="SELECT sno_guarderias.cedbene, sno_guarderias.nombene, sno_hsalida.codper, sno_hsalida.valsal,".
				"       sno_personal.cedper, SUBSTR(sno_guarderias.codguar,7,4) AS codguar,sno_hunidadadmin.codprouniadm, ".
				"       sno_hunidadadmin.estcla,sno_nomina.tippernom".
				"  FROM sno_hconcepto, sno_hsalida, sno_hpersonalnomina, sno_guarderias, sno_personal,sno_hunidadadmin,sno_nomina   ".
				" WHERE sno_hconcepto.codemp='".$this->is_codemp."' ".
				"   AND sno_hconcepto.codnom='".$ls_nomina."' ".
				"   AND sno_hconcepto.codperi='".$ls_periodo."' ".
				"   AND sno_hconcepto.guarrepcon='1'  ".
				"   AND sno_hconcepto.sigcon='R' ".
				"   AND sno_hconcepto.codemp = sno_hsalida.codemp ".
				"   AND sno_hconcepto.codnom = sno_hsalida.codnom ".
				"   AND sno_hconcepto.anocur = sno_hsalida.anocur ".
				"   AND sno_hconcepto.codperi = sno_hsalida.codperi ".
				"   AND sno_hconcepto.codconc = sno_hsalida.codconc ".
				"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_guarderias.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_guarderias.codper".
				"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_personal.codper".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm".
				"   AND sno_hconcepto.codemp = sno_nomina.codemp ".
				"   AND sno_hconcepto.codnom = sno_nomina.codnom ";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento_guarderias ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_codguar=$rs_data->fields["codguar"];
				$ls_numrecdoc=$ls_periodo.$ls_codguar.$ls_cedper;
				// Eliminamos los Detalles Contables
				$lb_existe=$this->uf_validar_recepcion_documento($ls_numrecdoc,$ls_cedper,$ls_codtipdoc,&$ls_estprodoc);
				if($lb_existe)
				{
					if($ls_estprodoc!="R")
					{
						$this->io_msg->message("La Recepcion de Documentos ".$ls_numrecdoc." debe estar en estatus de Registro");
						$lb_valido=false;
						break;
					}
				}
				if($lb_valido)
				{
					$ls_sql="DELETE ".
							"  FROM cxp_rd_scg ".
							" WHERE codemp='".$this->is_codemp."' ".
							"   AND numrecdoc='".$ls_numrecdoc."' ".
							"   AND codtipdoc='".$ls_codtipdoc."' ".
							"   AND cod_pro='".$ls_cod_pro."' ".
							"   AND ced_bene='".$ls_cedper."'";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento_guarderias ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
						$lb_valido=false;
					}
					if ($lb_valido)
					{
						// Eliminamos los Detalles Presupuestarios
						$ls_sql="DELETE ".
								"  FROM cxp_rd_spg ".
								" WHERE codemp='".$this->is_codemp."' ".
								"   AND numrecdoc='".$ls_numrecdoc."' ".
								"   AND codtipdoc='".$ls_codtipdoc."' ".
								"   AND cod_pro='".$ls_cod_pro."' ".
								"   AND ced_bene='".$ls_cedper."'";
						$li_row=$this->io_sql->execute($ls_sql);
						if($li_row===false)
						{
							$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento_guarderias ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
							$lb_valido=false;
						}
						if ($lb_valido)
						{
							// Eliminamos los Hist�ricos de La Recepci�n de Documento
							$ls_sql="DELETE ".
									"  FROM cxp_historico_rd ".
									" WHERE codemp='".$this->is_codemp."' ".
									"   AND numrecdoc='".$ls_numrecdoc."' ".
									"   AND codtipdoc='".$ls_codtipdoc."' ".
									"   AND cod_pro='".$ls_cod_pro."' ".
									"   AND ced_bene='".$ls_cedper."'";
							$li_row=$this->io_sql->execute($ls_sql);
							if($li_row===false)
							{
								$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento_guarderias ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
								$lb_valido=false;
							}
						}
						if ($lb_valido)
						{
							// Eliminamos La Recepci�n de Documento
							$ls_sql="DELETE ".
									"  FROM cxp_rd ".
									" WHERE codemp='".$this->is_codemp."' ".
									"   AND numrecdoc='".$ls_numrecdoc."' ".
									"   AND codtipdoc='".$ls_codtipdoc."' ".
									"   AND cod_pro='".$ls_cod_pro."' ".
									"   AND ced_bene='".$ls_cedper."'";
							$li_row=$this->io_sql->execute($ls_sql);
							if($li_row===false)
							{
								$this->io_msg->message("CLASE->Integraci�n SNO M�TODO->uf_reversar_recepcion_documento_guarderias ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
								$lb_valido=false;
							}
						}
					}
				}
				$rs_data->MoveNext();
			}
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso la Recepci�n de Documento de Guarderia para la N�mina <b>".$ls_nomina."</b>, Per�odo <b>".$ls_periodo."</b> ".
							"Comprobante <b>".$as_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return $lb_valido;
	}  // end function uf_reversar_recepcion_documento
	//-----------------------------------------------------------------------------------------------------------------------------------

}
?>
