<?php
class sigesp_siv_c_aprobacionrecepcion
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;

	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_siv_c_aprobacionrecepcion($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_siv_c_aprobacionrecepcion
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
	function uf_load_recepciones($as_numrec,$ad_fecregdes,$ad_fecreghas,$as_numordcom,$as_tipooperacion)
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
		// Fecha Creación: 13/04/2008								Fecha Última Modificación : 05/02/2009
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		$ls_sql="SELECT numordcom, numconrec, cod_pro, codalm, fecrec, estpro,".
				"       (SELECT nomfisalm FROM siv_almacen".
				"         WHERE siv_recepcion.codemp=siv_almacen.codemp".
				"           AND siv_recepcion.codalm=siv_almacen.codalm) AS nomalm".
				"  FROM siv_recepcion".
				" WHERE siv_recepcion.codemp='".$this->ls_codemp."'".
				"   AND numconrec like '".$as_numrec."'".
				"   AND numordcom like '".$as_numordcom."'".
				"   AND fecrec>='".$ad_fecregdes."'".
				"   AND fecrec<='".$ad_fecreghas."'".
				"   AND estapr='".$as_tipooperacion."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_load_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_movimiento(&$as_nummov,$ad_fecmov,$as_nomsol,$as_codusu,$aa_seguridad)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_movimiento
		//         Access: public 
		//      Argumento: $as_nummov    // numero de movimiento
		//                 $as_fecmov    // fecha de movimiento
		//                 $as_nomsol    // nombre del solicitante
		//                 $as_codusu    // codigo del usuario
		//                 $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un maestro de movimiento en la tabla de  siv_movimiento
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_nummov=$this->io_keygen->uf_generar_numero_nuevo("SIV","siv_movimiento","nummov","SIVRCP",15,"","","");

		$ls_sql="INSERT INTO siv_movimiento ( nummov, fecmov, nomsol, codusu)".
				" VALUES ('".$as_nummov."','".$ad_fecmov."','".$as_nomsol."','".$as_codusu."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Aprobacion MÉTODO->uf_siv_insert_movimiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	} // end  function uf_siv_insert_movimiento
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_aprobacion($as_numordcom,$as_numconrec,$ad_fecrec,$as_codalm,$as_estpro,$aa_seguridad)
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
		$as_nomsol="Recepcion";
		if($as_estpro==0)
		{
			$ls_codprodoc="ORD";
		}
		else
		{
			$ls_codprodoc="FAC";
		}
		$ad_fecrec= $this->io_funciones->uf_convertirdatetobd($ad_fecrec);
		$lb_valido=$this->uf_siv_insert_movimiento(&$as_nummov,$ad_fecrec,$as_nomsol,$aa_seguridad["logusr"],$aa_seguridad);
		if($lb_valido)
		{
			$rs_data=$this->uf_select_detalle_recepcion($as_numordcom,$as_numconrec);
			
			while((!$rs_data->EOF)&& $lb_valido)
			{
				$ls_codart=$rs_data->fields["codart"];
				$ls_unidad=$rs_data->fields["unidad"];
				$li_canart=$rs_data->fields["canart"];
				$li_penart=$rs_data->fields["penart"];
				$li_preuniart=$rs_data->fields["preuniart"];
				$li_canoriart=$rs_data->fields["canoriart"];
				$li_monsubart=$rs_data->fields["monsubart"];
				$li_montotart=$rs_data->fields["montotart"];
				$ls_opeinv="ENT";
				$ls_promov="RPC";
				$lb_valido=$this->uf_siv_insert_dt_movimiento($as_nummov,$ad_fecrec,$ls_codart,$as_codalm,$ls_opeinv,
																$ls_codprodoc,$as_numordcom,$li_canart,
																$li_preuniart,$ls_promov,$as_numconrec,
																$li_canart,$aa_seguridad);
				if($lb_valido)
				{
					$lb_valido=$this->uf_siv_aumentar_articuloxalmacen($ls_codart,$as_codalm,$li_canart,$aa_seguridad);
					if($lb_valido)
					{
						$lb_valido=$this->uf_siv_actualizar_cantidad_articulos($ls_codart,$aa_seguridad);
					}
				}
																
				$rs_data->MoveNext();
			}
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_recepcion($as_numordcom,$as_numconrec,$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_detalle_recepcion($as_numordcom,$as_numconrec)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_detalle_recepcion
		//		   Access: public
		//		 Argument: as_codsolvia     // Numero de Solicitud de Viaticos
		//                 ad_fecregdes     // Fecha (Emision) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las solicitudes  a aanular o reversar anulacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 13/04/2008								Fecha Última Modificación : 05/02/2009
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		$ls_sql="SELECT codart, unidad, canart, penart, preuniart, canoriart, monsubart, montotart".
				"  FROM siv_dt_recepcion".
				" WHERE siv_dt_recepcion.codemp='".$this->ls_codemp."'".
				"   AND siv_dt_recepcion.numconrec='".$as_numconrec."'".
				"   AND siv_dt_recepcion.numordcom='".$as_numordcom."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_select_detalle_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_dt_movimiento($as_nummov,$ad_fecmov,$as_codart,$as_codalm,$as_opeinv,$as_codprodoc,$as_numdoc,
										 $ai_canart,$ai_cosart,$as_promov,$as_numdocori,$ai_candesart,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_movimiento
		//         Access: public 
		//      Argumento: $ai_canart    // cantidad de articulos
		//                 $as_nummov    // numero de movimiento				$ai_cosart    // costo del articulo
		//                 $ad_fecmov    // fecha de movimiento					$as_promov    // procedencia del documento
		//                 $as_codart    // codigo de articulo					$as_numdocori // numero de documento original
		//                 $as_codalm    // codigo de almacen					$as_numdoc    // numero de documento
		//                 $as_opeinv    // codigo de operacion de inventario	$ad_fecdesart // fecha de el ultimo despacho del articulo
		//                 $as_codprodoc // codigo de procedencia del documento	$aa_seguridad // arreglo de registro de seguridad	
		//                 $ai_candesart // cantidad de articulos que restan por despachar
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un detalle de movimiento generado en cualquiera de los procesos de inventario,
		//				   en la tabla de  siv_dt_movimiento
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_dt_movimiento (codemp,nummov,fecmov,codart,codalm,opeinv,codprodoc,numdoc,canart,cosart,promov,".
				"                               numdocori,candesart,fecdesart)".
				" VALUES ('".$this->ls_codemp."','".$as_nummov."','".$ad_fecmov."','".$as_codart."','".$as_codalm."','".$as_opeinv."',".
				"         '".$as_codprodoc."','".$as_numdoc."','".$ai_canart."','".$ai_cosart."','".$as_promov."','".$as_numdocori."',".
				"         '".$ai_candesart."','".$ad_fecmov."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_siv_insert_dt_movimiento ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		return $lb_valido;
	} // end function uf_siv_insert_dt_movimiento
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_aumentar_articuloxalmacen($as_codart,$as_codalm,$ai_cantidad,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_aumentar_articuloxalmacen
		//         Access: public 
		//      Argumento: $as_codemp      //codigo de empresa 
		//                 $as_codart      // codigo de articulo
		//                 $as_codalm      //codigo de almacen
		//                 $ai_cantidad    // cantidad de articulos
		//                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que deacuerdo a los resultados de una busqueda (select), inserta o actualiza cierta cantidad de
		//				    articulos en un almacen determinado en la tabla de  siv_articuloalmacen
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if(!($this->uf_siv_select_articuloxalmacen($as_codart,$as_codalm)))
		{
			$lb_valido=$this->uf_siv_insert_articuloxalmacen($as_codart,$as_codalm,$ai_cantidad,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_sumar_articuloxalmacen($as_codart,$as_codalm,$ai_cantidad,$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_select_articuloxalmacen($as_codart,$as_codalm)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_articuloxalmacen
		//         Access: public 
		//      Argumento: $as_codart // codigo de articulo
		//                 $as_codalm //codigo de almacen
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que verifica si existe un articulo en un determinado almacen en la tabla de  siv_articuloalmacen
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT codart FROM siv_articuloalmacen  ".
				  " WHERE codemp='".$this->ls_codemp."' ".
				  " AND codart='".$as_codart."' ".
				  " AND codalm='".$as_codalm."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articuloxalmacen MÉTODO->uf_siv_select_articuloxalmacen ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	} // end  function uf_siv_select_articuloxalmacen
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_articuloxalmacen($as_codart,$as_codalm,$as_existencia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_articuloxalmacen
		//         Access: public 
		//      Argumento: $as_codart      // codigo de articulo
		//                 $as_codalm      //codigo de almacen
		//                 $as_existencia  // codigo del usuario
		//                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que registra cierta cantidad de un articulo en determinado almacen en la tabla siv_articuloalmacen
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_articuloalmacen (codemp, codart, codalm, existencia)".
				" VALUES ('".$this->ls_codemp."','".$as_codart."','".$as_codalm."','".$as_existencia."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->articuloxalmacen MÉTODO->uf_siv_insert_articuloxalmacen ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	} // end function uf_siv_insert_articuloxalmacen
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_sumar_articuloxalmacen($as_codart,$as_codalm,$ai_cantidad,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_sumar_articuloxalmacen
		//         Access: public 
		//      Argumento: $as_codart      // codigo de articulo
		//                 $as_codalm      //codigo de almacen
		//                 $ai_cantidad    // cantidad de articulos
		//                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que aumenta la cantidad de un articulo en un almacen determinado
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql= "UPDATE siv_articuloalmacen ".
		 		  "   SET existencia= (existencia + '".$ai_cantidad."') ".
				  " WHERE codemp='".$this->ls_codemp."' ".
				  "   AND codart='".$as_codart."' ".
				  "   AND codalm='".$as_codalm."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->articuloxalmacen MÉTODO->uf_siv_sumar_articuloxalmacen ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();

		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Aumentó ".$ai_cantidad." Articulos ".$as_codart." del Almacén ".$as_codalm." de la Empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
	    return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_actualizar_cantidad_articulos($as_codart,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_actualizar_cantidad_articulos
		//         Access: public 
		//      Argumento: $as_codemp     //codigo de empresa 
		//                 $as_codart     // codigo de articulo
		//                 $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que calcula la cantidad total de un articulo entre todos los almacenes para luego actualizar dicha cantidad
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_exec=-1;
		$li_totart=0;
		$ls_sql = "SELECT existencia FROM siv_articuloalmacen  ".
				  " WHERE codemp='".$this->ls_codemp."'".
				  "   AND codart='".$as_codart."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		while($row=$this->io_sql->fetch_row($rs_data))
		{
			$li_cantalm=$row["existencia"];
			$li_totart=$li_totart + $li_cantalm;
		}
		$lb_valido=$this->uf_siv_update_total_articulo($as_codart,$li_totart,$aa_seguridad);		
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_total_articulo($as_codart,$ai_cantidad,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_total_articulo
		//         Access: public 
		//      Argumento: $as_codart     // codigo de articulo
		//                 $as_codalm     // codigo de almacen
		//                 $as_cantidad   // cantidad de articulos
		//                 $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que actualiza la cantidad de un articulo en un almacen determinado
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe=$this->uf_siv_select_articulo($as_codart);
		if($lb_existe)
		{
			 $ls_sql= "UPDATE siv_articulo ".
			 		  "   SET exiart='".$ai_cantidad."' ".
					  " WHERE codemp='".$this->ls_codemp."' ".
					  "   AND codart='".$as_codart."'";
				$li_row = $this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$this->io_msg->message("CLASE->articuloxalmacen MÉTODO->uf_siv_update_total_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					$lb_valido=true;
				}
		} 
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_select_articulo($as_codart)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_articulo
		//         Access: public 
		//      Argumento: $as_codart // codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que busca un articulo en la tabla de  siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT codart FROM siv_articulo  ".
				  " WHERE codemp='".$this->ls_codemp."'".
				  "   AND codart='".$as_codart."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articuloxalmacen MÉTODO->uf_siv_select_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_recepcion($as_numordcom,$as_numconrec,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_recepcion
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
		$ls_sql=" UPDATE siv_recepcion".
				"    SET estapr='1'".
				"  WHERE codemp='".$this->ls_codemp."'".
				"    AND numordcom='".$as_numordcom."'".
				"    AND numconrec='".$as_numconrec."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if ($li_row===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos METODO->uf_scv_update_solivitud_viaticos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion= "Aprobó la Recepcion de Suministros ".$as_numconrec." Asociada a la empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               ///////////////////////////
			$lb_valido=true;
		}
		return $lb_valido;
	} // fin function uf_scv_update_rutas
	//-----------------------------------------------------------------------------------------------------------------------------------

}
?>