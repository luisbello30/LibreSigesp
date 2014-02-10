<?php 
class sigesp_sss_c_usuarios
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;

	function sigesp_sss_c_usuarios()
	{
		require_once("../shared/class_folder/class_sql.php");
		require_once("../shared/class_folder/sigesp_include.php");
		require_once("../shared/class_folder/class_mensajes.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		require_once("../shared/class_folder/class_funciones.php");
		require_once("class_validacion.php");
		$this->io_validacion = new class_validacion();
		$this->io_msg=new class_mensajes();
		$this->seguridad= new sigesp_c_seguridad;
		$this->dat_emp=$_SESSION["la_empresa"];
		$this->dat_bdorigen=$_SESSION["ls_database"];
		$in=new sigesp_include();
		//$this->con=$in->uf_conectar();
		$conn=$in->uf_conectar();
		$this->io_sql=new class_sql($conn);
		$this->io_funcion = new class_funciones();
	}

	function  uf_sss_select_usuarios($as_codemp,$as_codusu)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_select_grupos
		//         Access: public (sigesp_sss_d_usuarios)
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codusu    // codigo de usuario
		//	      Returns: Retorna un Booleano
		//    Description: Función que se encarga de verificar la existencia de un usuario en la tabla sss_usuarios
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/11/2005									Fecha Última Modificación : 01/11/2005	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT * FROM sss_usuarios  ".
				  " WHERE codemp='".$as_codemp."'".
				  " AND codusu='".$as_codusu."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->usuarios MÉTODO->uf_sss_select_usuarios ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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
	}  // end  function  uf_sss_select_usuarios

	function  uf_sss_insert_usuario($ad_ultingusu,$as_codemp,$as_codusu,$as_nomusu,$as_apeusu,$as_cedusu,
									$as_pwdusu,$as_telusu,$as_nota,$as_fotousu,$aa_seguridad )
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_insert_usuario
		//         Access: public (sigesp_sss_d_usuarios)
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codusu    // codigo de usuario
		//  			   $as_nomusu    // nombre de usuario
		//  			   $as_apeusu    // apellido de usuario
		//  			   $as_cedusu    // cedula de usuario
		//  			   $as_pwdusu    // password encriptado de usuario
		//  			   $as_telusu    // telefono de usuario
		//  			   $as_nota      // observaciones de usuario
		//  			   $as_fotousu   // foto de usuario
		//  			   $ad_ultingusu // fecha de ultimo ingreso del usuario
		//  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Función que se encarga de insertar un usuario en la tabla sss_usuarios
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/11/2005									Fecha Última Modificación : 01/11/2005	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "INSERT INTO sss_usuarios (codemp, codusu, cedusu, nomusu, apeusu, pwdusu, telusu, nota, ultingusu, fotousu ) ".
				  " VALUES('".$as_codemp."','".$as_codusu."','".$as_cedusu."','".$as_nomusu."','".$as_apeusu."','".$as_pwdusu."',".
				  "        '".$as_telusu."','".$as_nota."','".$ad_ultingusu."','".$as_fotousu."')" ;
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->usuarios MÉTODO->uf_sss_insert_usuario ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó el Usuario ".$as_codusu." Asociado a la empresa ".$as_codemp;
			$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			if ($lb_variable)
			{
				$lb_valido=true;
				$this->io_sql->commit();
			}
			else
			{
				$this->io_sql->rollback();
			}	
		}
		return $lb_valido;
	}  // end  function  uf_sss_insert_usuario
	
	function uf_sss_delete_usuario($as_codemp,$as_codusu,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_delete_usuario
		//         Access: public (sigesp_sss_d_usuarios)
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codusu    // codigo de usuario
		//  			   $aa_seguridad // codigo de usuario
		//	      Returns: Retorna un Booleano
		//    Description: Función que se encarga de eliminar un usuario en la tabla sss_usuarios verificando su integridad referencial
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/11/2005									Fecha Última Modificación : 01/11/2005	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$lb_existe=$this->uf_sss_select_eventos($as_codemp,$as_codusu);
		if($lb_existe)
		{
			$this->io_msg->message("El usuario tiene registros de eventos asociados");
			$lb_valido=false;
		}
		else
		{
			$lb_existe=$this->uf_sss_select_permisos($as_codemp,$as_codusu);
			if($lb_existe)
			{
				$this->io_msg->message("El usuario tiene registros de permisos");
				$lb_valido=false;
			}
		}
		if(!$lb_existe)
		{
			$ls_sql = " DELETE FROM sss_usuarios".
					  " WHERE codemp= '".$as_codemp. "'".
					  " AND codusu= '".$as_codusu."'"; 
			$this->io_sql->begin_transaction();	
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->usuarios MÉTODO->uf_sss_delete_usuario ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Eliminó el Usuario ".$as_codusu." Asociado a la empresa ".$as_codemp;
				$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////			
				if ($lb_variable)
				{
					$lb_valido=true;
					$this->io_sql->commit();
				}
				else
				{
					$this->io_sql->rollback();
				}	
			}
		}
		return $lb_valido;
	} // end  function uf_sss_delete_usuario
	
	function uf_sss_update_usuario($as_codemp,$as_codusu,$as_cedusu,$as_nomusu,$as_apeusu,$as_telusu,$as_nota,$as_nomarch,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_update_usuario
		//         Access: public (sigesp_sss_d_usuarios)
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codusu    // codigo de usuario
		//  			   $as_nomusu    // nombre de usuario
		//  			   $as_apeusu    // apellido de usuario
		//  			   $as_cedusu    // cedula de usuario
		//  			   $as_telusu    // telefono de usuario
		//  			   $as_nota      // observaciones de usuario
		//  			   $as_nomarch   // nombre del archivo de la foto
		//  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Función que se encarga de modificar un usuario en la tabla sss_usuarios
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/11/2005									Fecha Última Modificación : 01/11/2005	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=false;
		 $ls_sqlfoto="";
		 if($as_nomarch!="")
		 {
		 	$ls_sqlfoto=", fotousu='". $as_nomarch ."'";
		 }
		 $ls_sql = "UPDATE sss_usuarios SET  cedusu='". $as_cedusu ."',nomusu='". $as_nomusu ."',apeusu='". $as_apeusu ."',".
				   "telusu='". $as_telusu ."', nota='". $as_nota ."'". $ls_sqlfoto ."".
				   " WHERE codemp='" .$as_codemp ."'".
				   " AND codusu='" .$as_codusu ."'";
        $this->io_sql->begin_transaction();
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->usuarios MÉTODO->uf_sss_update_usuario ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó el Usuario ".$as_codusu." Asociado a la empresa ".$as_codemp;
			$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			if ($lb_variable)
			{
				$lb_valido=true;
				$this->io_sql->commit();
			}
			else
			{
				$this->io_sql->rollback();
			}	
		}
	  return $lb_valido;
	}  // end  function uf_sss_update_usuario
	
	function  uf_sss_select_eventos($as_codemp,$as_codusu)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_select_eventos
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codusu    // codigo de usuario
		//	      Returns: Retorna un Booleano
		//    Description: Función que verifica si un usuario tiene registrado algun evento en la tabla sss_registro_eventos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/11/2005									Fecha Última Modificación : 01/11/2005	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT * FROM sss_registro_eventos  ".
				  " WHERE codemp='".$as_codemp."'".
				  " AND codusu='".$as_codusu."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->usuarios MÉTODO->uf_sss_select_eventos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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
	}  // end  function  uf_sss_select_eventos

	function  uf_sss_select_permisos($as_codemp,$as_codusu)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_select_permisos
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codusu    // codigo de usuario
		//	      Returns: Retorna un Booleano
		//    Description: Función que verifica si un usuario tiene registrado algun permiso en la tabla sss_derechos_usuarios
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/11/2005									Fecha Última Modificación : 01/11/2005	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT * FROM sss_derechos_usuarios  ".
				  " WHERE codemp='".$as_codemp."'".
				  " AND codusu='".$as_codusu."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->usuarios MÉTODO->uf_sss_select_permisos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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
	}  // end   function  uf_sss_select_permisos


	function uf_consultar_usuarios($as_codusudes,$as_codusuhas,$as_codusu, $as_cedusu, $as_nomusu, $as_apeusu,&$ai_totrows,&$ao_object)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Funcion       uf_consultar_usuarios
	//	Access        public
	//	Arguments	  ai_totrows  // total de filas del detalle
	//				  ao_object  // objetos del detalle
	//	Returns	      lb_valido. Retorna una variable booleana
	//	Description   Busca registros de usuarios dada una base de datos. 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_cadena="";
		
		if (($as_codusudes!="") && ($as_codusuhas!=""))
		{
		  $ls_cadena=$ls_cadena."AND codusu BETWEEN '".$as_codusudes."' AND '".$as_codusuhas."' ";
		}
		
		if ($as_codusu!="")
		{
		  $ls_cadena=$ls_cadena."AND codusu LIKE '".'%'.$as_codusu.'%'."' ";
		}
		
		if ($as_cedusu!="")
		{
		  $ls_cadena=$ls_cadena."AND cedusu LIKE '".'%'.$as_cedusu.'%'."' ";
		}
		
		if ($as_nomusu!="")
		{
		  $ls_cadena=$ls_cadena."AND nomusu LIKE '".'%'.$as_nomusu.'%'."' ";
		}
		
		if ($as_apeusu!="")
		{
		  $ls_cadena=$ls_cadena."AND apeusu LIKE '".'%'.$as_apeusu.'%'."' ";
		}
		
		$lb_valido=true;
		$ls_sql="SELECT codusu, cedusu, nomusu, apeusu ".
                "   FROM sss_usuarios ".	
				" WHERE codusu <> '----------' ".$ls_cadena.			
				" ORDER BY codusu ";			
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->usuarios MÉTODO->uf_consultar_usuarios ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
		 $num=$this->io_sql->num_rows($rs_data);
           
		  if ($num!=0) {
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				
				$ls_codusu= trim ($row["codusu"]);
				$ls_cedusu= trim ($row["cedusu"]);
				$ls_nomusu= trim (htmlentities($row["nomusu"]));
				$ls_apeusu= trim ($row["apeusu"]);
				$ai_totrows++;
				$ao_object[$ai_totrows][1]="<input name=txtcodusu".$ai_totrows." type=text id=txtcodusu".$ai_totrows." class=sin-borde size=12 maxlength=10 value='".$ls_codusu."' readonly >";
				$ao_object[$ai_totrows][2]="<input name=txtcedusu".$ai_totrows." type=text id=txtcedusu".$ai_totrows." class=sin-borde size=12 maxlength=10 value='".$ls_cedusu."' readonly >";
				$ao_object[$ai_totrows][3]="<input name=txtnomusu".$ai_totrows." type=text id=txtnomusu".$ai_totrows." maxlength=100 class=sin-borde size=60 value='".$ls_nomusu."' readonly>";
				$ao_object[$ai_totrows][4]="<input name=txtapeusu".$ai_totrows." type=text id=txtapeusu".$ai_totrows." class=sin-borde size=60 maxlength=100 value='".$ls_apeusu."'  readonly>";
				$ao_object[$ai_totrows][5]="<input type=checkbox name=selusu".$ai_totrows." id=selusu".$ai_totrows." onChange='javascript: cambiar_valor(".$ai_totrows.");'><input name=txtselusu".$ai_totrows." type=hidden id=txtselusu".$ai_totrows." readonly>";			
			   	
							
			}
			$this->io_sql->free_result($rs_data);
			}
		else 
		 {
		    $this->io_msg->message("No hay usuarios en la base de datos seleccionada.");
	 		$ai_totrows=1;
			$ao_object[$ai_totrows][1]="<input name=txtcodusu".$ai_totrows." type=text id=txtcodusu".$ai_totrows." class=sin-borde size=12 maxlength=10  readonly >";
			$ao_object[$ai_totrows][2]="<input name=txtcedusu".$ai_totrows." type=text id=txtcedusu".$ai_totrows." class=sin-borde size=12 maxlength=10 readonly >";
			$ao_object[$ai_totrows][3]="<input name=txtnomusu".$ai_totrows." type=text id=txtnomusu".$ai_totrows." maxlength=100 class=sin-borde size=60  readonly>";
			$ao_object[$ai_totrows][4]="<input name=txtapeusu".$ai_totrows." type=text id=txtapeusu".$ai_totrows." class=sin-borde size=60 maxlength=100   readonly>";
			$ao_object[$ai_totrows][5]="<input type=checkbox name=selusu".$ai_totrows." id=selusu".$ai_totrows." onChange='javascript: cambiar_valor(".$ai_totrows.");'><input name=txtselusu".$ai_totrows." type=hidden id=txtselusu".$ai_totrows." readonly>";			
			
		
		  }
		  return $lb_valido;
		}
		
	}
	
//------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------

	function uf_select_usuarios_bd($as_codemp, $as_codusu, $as_hostname, $as_login, $as_password,$as_database,$as_gestor)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Funcion       uf_select_usurioa_bd
	//	Access        public
	//	Arguments	  $as_codemp    // código de la Empresa
	//                $as_codusu    // código del Usuario
	//                $as_hostname  // hostname para conectar con la Base de Datos
	//                $as_login     // login para conectar con la Base de Datos
	//                $as_password  // password o clave para conectac con la Base de Datos
	///               $as_database  // nombre de la Base Datos con la que se quiere conectar
	//                $as_gestor    // nombre del gestor que maneja la Base de Datos
	//	Returns	      lb_valido. Retorna una variable booleana
	//	Description   Busca registros de usuarios dada una base de datos. 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/sigesp_include.php");
		$io_conect=new sigesp_include();
		$io_conexion_destino   = $io_conect->uf_conectar_otra_bd($as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		$io_sql_destino        = new class_sql($io_conexion_destino);
		$lb_valido=true;
		$ls_sql="SELECT codusu ". 
				"  FROM sss_usuarios ".	
				" WHERE codemp = '".$as_codemp."' AND codusu = '".$as_codusu."' ".			
				" ORDER BY codusu ";	
		$rs_data = $io_sql_destino->select($ls_sql);
		if ($rs_data===false)
		   {
			  $this->io_msg->message($this->io_funcion->uf_convertirmsg($io_sql_destino->message));		 
			  $lb_valido=false;
		   }
		else
		   {
			 $li_numrows =  $io_sql_destino->num_rows($rs_data);
			 if ($li_numrows>0)
				{
				 $lb_valido=true;                  
				 $io_sql_destino->free_result($rs_data);	
				}
			else 
				{
					$lb_valido=false;
				}
		   }
	return $lb_valido;
	}
	
	function uf_select_permisos_internos($as_codemp, $as_codusu, $as_codsis, $as_codintper,$as_hostname, $as_login, $as_password,$as_database,$as_gestor)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Funcion       uf_select_permisos_internos
	//	Access        public
	//	Arguments	  $as_codusu   // código del usuario
	//                $as_hostname  // hostname para conectar con la Base de Datos
	//                $as_login     // login para conectar con la Base de Datos
	//                $as_password  // password o clave para conectac con la Base de Datos
	///               $as_database  // nombre de la Base Datos con la que se quiere conectar
	//                $as_gestor    // nombre del gestor que maneja la Base de Datos
	//	Returns	      lb_valido. Retorna una variable booleana
	//	Description   Busca registros de permisos internos de los usuarios dada una base de datos. 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/sigesp_include.php");
		$io_conect=new sigesp_include();
		$io_conexion_destino   = $io_conect->uf_conectar_otra_bd ($as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		$io_sql_destino        = new class_sql($io_conexion_destino);
		$lb_valido=true;
		$ls_sql="SELECT codemp, codusu, codsis, codintper ". 
				"  FROM sss_permisos_internos ".	
				" WHERE codemp = '".$as_codemp."' AND codusu = '".$as_codusu."' ".
				" AND codsis ='".$as_codsis."' AND codintper = '".$as_codintper."'".			
				" ORDER BY codusu ";
			
		$rs_data   = $io_sql_destino->select($ls_sql);
		if ($rs_data===false)
		   {
			  $this->io_msg->message($this->io_funcion->uf_convertirmsg($io_sql_destino->message));		 
			  $lb_valido=false;
		   }
		else
		   {
			 $li_numrows = $io_sql_destino->num_rows($rs_data);
			 if ($li_numrows>0)
				{
				 $lb_valido=true;                  
				 $io_sql_destino->free_result($rs_data);	
				}
			else 
				{
					$lb_valido=false;
				}
		   }
	return $lb_valido;
	}

//------------------------------------------------------------------------------------------------------------------------------


	function uf_select_derechos_usuario($as_codemp, $as_codusu, $as_codsis, $as_nomven, $as_codintper, $as_hostname, $as_login, $as_password,$as_database,$as_gestor)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Funcion       uf_procesar_derechos_usuario
	//	Access        public
	//	Arguments	  $as_codemp    // código de la Empresa
	//                $as_codusu    // Codigo del Usuario 
	//                $as_codsis    // Codigo del Sistema
	//                $as_nomven    // Nombre de la Ventana
	//                $as_codintper  // Codigo interno del permiso
	//                $as_hostname  // hostname para conectar con la Base de Datos
	//                $as_login     // login para conectar con la Base de Datos
	//                $as_password  // password o clave para conectac con la Base de Datos
	///               $as_database  // nombre de la Base Datos con la que se quiere conectar
	//                $as_gestor    // nombre del gestor que maneja la Base de Datos
	//	Returns	      lb_valido. Retorna una variable booleana
	//	Description   Busca registros de derechos de los usuarios dada una base de datos. 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/sigesp_include.php");
		$io_conect=new sigesp_include();
		$io_conexion_destino   = $io_conect->uf_conectar_otra_bd ($as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		$io_sql_destino        = new class_sql($io_conexion_destino);
		
		$lb_valido=true;
		$ls_sql="SELECT codemp, codusu, codsis, codintper ". 
				"  FROM sss_derechos_usuarios ".	
				" WHERE codemp = '".$as_codemp."' AND codusu = '".$as_codusu."' ".
				" AND codsis = '".$as_codsis."' AND nomven = '".$as_nomven."' AND codintper = '".$as_codintper."' ".			
				" ORDER BY codusu ";
			
		$rs_data   = $io_sql_destino->select($ls_sql);
		if ($rs_data===false)
		   {
			  $this->io_msg->message($this->io_funcion->uf_convertirmsg($io_sql_destino->message));		 
			  $lb_valido=false;
		   }
		else
		   {
			 $li_numrows = $io_sql_destino->num_rows($rs_data);
			 if ($li_numrows>0)
				{
				 $lb_valido=true;                  
				 $io_sql_destino->free_result($rs_data);	
				}
			else 
				{
					$lb_valido=false;
				}
		   }
	return $lb_valido;
	}

//------------------------------------------------------------------------------------------------------------------------------

	function uf_select_ventana_sistema($as_codsis, $as_nomven, $as_hostname, $as_login, $as_password,$as_database,$as_gestor)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Funcion       uf_procesar_ventana_sistema
	//	Access        public
	//	Arguments	  $as_codsis    // Codigo del Sistema
	//                $as_nomven    // Nombre de la Ventana
	//                $as_hostname  // hostname para conectar con la Base de Datos
	//                $as_login     // login para conectar con la Base de Datos
	//                $as_password  // password o clave para conectac con la Base de Datos
	///               $as_database  // nombre de la Base Datos con la que se quiere conectar
	//                $as_gestor    // nombre del gestor que maneja la Base de Datos
	//	Returns	      lb_valido. Retorna una variable booleana
	//	Description   Busca una ventana asociada a un sistema 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/sigesp_include.php");
		$io_conect=new sigesp_include();
		$io_conexion_destino   = $io_conect->uf_conectar_otra_bd ($as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		$io_sql_destino        = new class_sql($io_conexion_destino);
		$lb_valido=true;
		$ls_sql="SELECT * ".
				"  FROM sss_sistemas_ventanas ".	
				" WHERE codsis = '".$as_codsis."' AND nomven = '".$as_nomven."' ";		
		$rs_data   = $io_sql_destino->select($ls_sql);
		if ($rs_data===false)
		   {
			  $this->io_msg->message($this->io_funcion->uf_convertirmsg($io_sql_destino->message));		 
			  $lb_valido=false;
		   }
		else
		   {
			 $li_numrows = $io_sql_destino->num_rows($rs_data);
			 if ($li_numrows>0)
				{
				 $lb_valido=true;                  
				 $io_sql_destino->free_result($rs_data);	
				}
			else 
				{
					$ls_sql="SELECT titven, desven ".
  							"	FROM sss_sistemas_ventanas ".
							"  WHERE codsis = '".$as_codsis."' AND nomven = '".$as_nomven."' ";		
					$io_recordset=$this->io_sql->select($ls_sql);
					if($io_recordset===false)
					{
						$lb_valido=false;
						$this->io_msg->message(" Error en la Seleccion de Datos de la Ventana ".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
					}
					else
					{   
					 while($row=$this->io_sql->fetch_row($io_recordset))
					 {
					  $ls_titven= $this->io_validacion->uf_valida_texto($row["titven"],0,80,"");
					  $ls_desven= $this->io_validacion->uf_valida_texto($row["desven"],0,254,"");
					 }
					 if($ls_titven!="")
					 {
								
						$ls_sql="INSERT INTO sss_sistemas_ventanas(codsis, nomven, titven, desven) ".
    							" VALUES ('".$as_codsis."','".$as_nomven."','".$ls_titven."','".$ls_desven."')";						
						$li_row=$io_sql_destino->execute($ls_sql);
						if($li_row===false)
						{
							$lb_valido=false;
							$this->io_msg->message("Error al insertar Ventana  ".$as_nomven);
							$io_sql_destino->rollback();
						}
						else
						{ 
						 $lb_valido = true;
						 $io_sql_destino->commit();
						}
					}
					else
					{
					 $lb_valido = false;
					}
				  }
				}
		   }
	return $lb_valido;
	}

//------------------------------------------------------------------------------------------------------------------------------

	function uf_obtener_codempresa_bd($as_hostname, $as_login, $as_password,$as_database,$as_gestor,&$as_codempdes)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Funcion       uf_obtener_codempresa_bd
	//	Access        public
	//	Arguments	  $as_hostname  // hostname para conectar con la Base de Datos
	//                $as_login     // login para conectar con la Base de Datos
	//                $as_password  // password o clave para conectac con la Base de Datos
	//                $as_database  // nombre de la Base Datos con la que se quiere conectar
	//                $as_gestor    // nombre del gestor que maneja la Base de Datos
	//                $as_codempdes // Código de la Empresa destino
	//	Returns	      lb_valido. Retorna una variable booleana
	//	Description   Devuelve el Código de Empresa de la Base de Datos referenciada
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/sigesp_include.php");
		$io_conect=new sigesp_include();
		$io_conexion_destino   = $io_conect->uf_conectar_otra_bd ($as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		$io_sql_destino        = new class_sql($io_conexion_destino);
		$lb_valido=true;
		$ls_sql="SELECT codemp ". 
				"  FROM sigesp_empresa ";
		$rs_data   = $io_sql_destino->select($ls_sql);
		if ($rs_data===false)
		   {
			  $this->io_msg->message($this->io_funcion->uf_convertirmsg($io_sql_destino->message));		 
			  $lb_valido=false;
		   }
		else
		   {
			 $li_numrows = $io_sql_destino->num_rows($rs_data);
			 if ($li_numrows>0)
				{
				 $lb_valido=true;
				 if ($row=$io_sql_destino->fetch_row($rs_data))
				 {
				  $as_codempdes = $row["codemp"];
				 }                  
				 $io_sql_destino->free_result($rs_data);	
				}
			else 
				{
					$lb_valido=false;
				}
		   }
	return $lb_valido;
	}


//------------------------------------------------------------------------------------------------------------------------------

	function uf_transferir_usuarios($codemp, $codusu, $as_hostname, $as_login, $as_password,$as_database,$as_gestor)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Funcion       uf_transferir_usuarios
	//	Access        public
	//	Arguments	  ai_totrows  // total de filas del detalle
	//				  ao_object  // objetos del detalle
	//	Returns	      lb_valido. Retorna una variable booleana
	//	Description   Busca registros de usuarios dada una base de datos. 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe=false;
		require_once("../shared/class_folder/sigesp_include.php");
		$io_conect=new sigesp_include();
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
		$this->io_sql=new class_sql($this->con);
		$io_conexion_destino   = $io_conect->uf_conectar_otra_bd ($as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		$io_sql_destino = new class_sql($io_conexion_destino);
		$lb_existe=$this->uf_select_usuarios_bd($codemp, $codusu, $as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		if (!$lb_existe)
		{
			$lb_valido= true;
			$li_total_select= 0;
			$li_total_insert= 0;
			$this->io_sql->begin_transaction();
			$ls_sql="SELECT cedusu, nomusu, apeusu, pwdusu, telusu, nota, ". 
       				"       actusu, blkusu, admusu, ultingusu, fotousu ".
      				"	FROM sss_usuarios".
					"  WHERE codemp = '".$codemp."' AND codusu = '".$codusu."' ";	
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$lb_valido=false;
				$this->io_msg->message(" Error en la Seleccion de Usuario ".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			}
			else
			{   
			 $li_total_select = $this->io_sql->num_rows($rs_data);
			 while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			 {
				$ls_cedusu= $this->io_validacion->uf_valida_texto($row["cedusu"],0,8,"");
				$ls_nomusu= $this->io_validacion->uf_valida_texto($row["nomusu"],0,50,"");
				$ls_apeusu= $this->io_validacion->uf_valida_texto($row["apeusu"],0,50,"");
				$ls_pwdusu= $this->io_validacion->uf_valida_texto($row["pwdusu"],0,50,"");
				$ls_telusu= $this->io_validacion->uf_valida_texto($row["telusu"],0,20,"");
				$ls_nota  = $this->io_validacion->uf_valida_texto($row["nota"],0,5000,"");
				$li_actusu= $this->io_validacion->uf_valida_monto($row["actusu"],0);
				$li_blkusu= $this->io_validacion->uf_valida_monto($row["blkusu"],0);
				$li_admusu= $this->io_validacion->uf_valida_monto($row["admusu"],0);
				$ld_ultingusu=$this->io_validacion->uf_valida_fecha($row["ultingusu"],"1900-01-01");
				$ls_fotousu= $this->io_validacion->uf_valida_texto($row["fotousu"],0,254,"");
				
				if(($codemp!="")&&($codusu!=""))
				{
								
					$ls_sql="INSERT INTO sss_usuarios(codemp, codusu, cedusu, nomusu, apeusu, pwdusu, telusu, nota, ".
                  			"						  actusu, blkusu, admusu, ultingusu, fotousu) ".
    						"     VALUES ('".$codemp."','".$codusu."','".$ls_cedusu."','".$ls_nomusu."','".$ls_apeusu."',".
							"             '".$ls_pwdusu."','".$ls_telusu."','".$ls_nota."',".$li_actusu.",".$li_blkusu.",".
							"             ".$li_admusu.",'".$ld_ultingusu."','".$ls_fotousu."')";					
					$li_row=$io_sql_destino->execute($ls_sql);
					if($li_row===false)
					{
						$lb_valido=false;
						$this->io_msg->message("Error al insertar el Usuario  ".$codusu);
					}
					else
					{ 
					  $lb_valido = $this->uf_procesar_permisos_internos($codemp, $codusu, $as_hostname, $as_login, $as_password,$as_database,$as_gestor);
					  if ($lb_valido)
					  {
					    $lb_valido = $this->uf_procesar_derechos_usuario($codemp, $codusu, $as_hostname, $as_login, $as_password,$as_database,$as_gestor);
						
						if ($lb_valido)
						{
						 $ls_descripcion = "Usuario Nuevo Transferido: ".$codusu." - ".$ls_nomusu." ".$ls_apeusu.", con los permisos asociados ";
						 $lb_result = $this->uf_insertar_resultado($this->dat_bdorigen,$as_database,$ls_descripcion);
						}
					  }
						$li_total_insert++;
					 }
				}
				else
				{
					   $lb_valido = false;
					   $this->io_msg->message("Hay data inconsistente en los Usuarios");
				}
			}
			}	
		}
		else // Si existe el Usuario
		{
		 $lb_valido = $this->uf_procesar_permisos_internos($codemp, $codusu, $as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		 if ($lb_valido)
		 {
		  $lb_valido = $this->uf_procesar_derechos_usuario($codemp, $codusu, $as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		  
		  if ($lb_valido)
		  {
		   $ls_descripcion = "El Usuario ".$codusu." no fue transferido, ya se encuentra en la base de datos destino, se actualizaron sus permisos ";
		   $lb_result = $this->uf_insertar_resultado($this->dat_bdorigen,$as_database,$ls_descripcion);
		  }
		 }
		}	
		
		if ($lb_valido )
		{
		 $io_sql_destino->commit();
		 $this->io_sql->close();
		}
		else
		{
		 $io_sql_destino->rollback();
		 $this->io_sql->close();
		}
		
		return $lb_valido;
	}	

//------------------------------------------------------------------------------------------------------------------------------

	function uf_procesar_permisos_internos($codemp, $codusu, $as_hostname, $as_login, $as_password,$as_database,$as_gestor)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Funcion       uf_procesar_permisos_internos
	//	Access        public
	//	Arguments	  ai_totrows  // total de filas del detalle
	//				  ao_object  // objetos del detalle
	//	Returns	      lb_valido. Retorna una variable booleana
	//	Description   Registra y Procesa los permisos internos asociados a un usuario 
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe_permiso=false;
		require_once("../shared/class_folder/sigesp_include.php");
		$io_conect=new sigesp_include();
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$io_conexion_destino   = $io_conect->uf_conectar_otra_bd($as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		$io_sql_destino = new class_sql($io_conexion_destino);
		$this->io_sql->begin_transaction();
		$ls_sql="SELECT codsis, codintper ". 
				"  FROM sss_permisos_internos ".	
				" WHERE codemp = '".$ls_codemp."' AND codusu = '".$codusu."' ".			
				" ORDER BY codsis ";		
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
		 $lb_valido=false;
		 $this->io_msg->message("Error al Seleccionar Permisos Internos del Usuario ".$codusu." de la Base de Datos.");
		}
		else
		{   
		 $li_total_select = $this->io_sql->num_rows($rs_data);
		 while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
		 {
		  $ls_codsis = $this->io_validacion->uf_valida_texto($row["codsis"],0,3,"");
		  $ls_codintper = $this->io_validacion->uf_valida_texto($row["codintper"],0,126,"------------------------------------------------------------------------------------------------------------------------------");
		  $lb_existe_permiso=$this->uf_select_permisos_internos($codemp, $codusu, $ls_codsis, $ls_codintper,$as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		  if (!$lb_existe_permiso)
		  {
		   $ls_sql="INSERT INTO sss_permisos_internos(codemp, codusu, codsis, codintper) ".
				   "    VALUES ('".$codemp."','".$codusu."','".$ls_codsis."','".$ls_codintper."')";   		
		   $li_row=$io_sql_destino->execute($ls_sql);
		   if($li_row===false)
		   {
			 $lb_valido=false;
			 $this->io_msg->message("Error al insertar el Permiso Interno del Usuario  ".$codusu);
		   }
		   else
		   { 
			 $lb_valido = true; 
		   }
		  }
		 } // while
		}  // else
	 return $lb_valido;
	}
//------------------------------------------------------------------------------------------------------------------------------


	function uf_procesar_derechos_usuario($codemp, $codusu, $as_hostname, $as_login, $as_password,$as_database,$as_gestor)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Funcion       uf_procesar_derechos_usuario
	//	Access        public
	//	Arguments	  ai_totrows  // total de filas del detalle
	//				  ao_object  // objetos del detalle
	//	Returns	      lb_valido. Retorna una variable booleana
	//	Description   Registra y Procesa los derechos asociados a un usuario 
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe_derecho=false;
		require_once("../shared/class_folder/sigesp_include.php");
		$io_conect=new sigesp_include();
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$io_conexion_destino   = $io_conect->uf_conectar_otra_bd ($as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		$io_sql_destino = new class_sql($io_conexion_destino);
		$this->io_sql->begin_transaction();
		$ls_sql="SELECT codsis, nomven, codintper, visible, enabled, leer, incluir, cambiar, eliminar, imprimir, ".
		        "		administrativo, anular, ejecutar ". 
				"  FROM sss_derechos_usuarios ".	
				" WHERE codemp = '".$ls_codemp."' AND codusu = '".$codusu."' ".			
				" ORDER BY codsis ";	
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
		 $lb_valido=false;
		 $this->io_msg->message("Error al seleccionar Derechos del Usuario ".$codusu." de la Base de Datos.".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{   
		 $li_total_select = $this->io_sql->num_rows($rs_data);
		 while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
		 {
		  $ls_codsis     	 = $this->io_validacion->uf_valida_texto($row["codsis"],0,3,"");
		  $ls_nomven    	 = $this->io_validacion->uf_valida_texto($row["nomven"],0,80,"");
		  $ls_codintper 	 = $this->io_validacion->uf_valida_texto($row["codintper"],0,126,"------------------------------------------------------------------------------------------------------------------------------");
		  $li_visible   	 = $this->io_validacion->uf_valida_monto($row["visible"],0);
		  $li_enabled   	 = $this->io_validacion->uf_valida_monto($row["enabled"],0);
		  $li_leer   		 = $this->io_validacion->uf_valida_monto($row["leer"],0);
		  $li_incluir   	 = $this->io_validacion->uf_valida_monto($row["incluir"],0);
		  $li_cambiar   	 = $this->io_validacion->uf_valida_monto($row["cambiar"],0);
		  $li_eliminar   	 = $this->io_validacion->uf_valida_monto($row["eliminar"],0);
		  $li_imprimir   	 = $this->io_validacion->uf_valida_monto($row["imprimir"],0);
		  $li_administrativo = $this->io_validacion->uf_valida_monto($row["administrativo"],0);
		  $li_anular         = $this->io_validacion->uf_valida_monto($row["anular"],0);
		  $li_ejecutar       = $this->io_validacion->uf_valida_monto($row["ejecutar"],0);
		  $lb_existe_derecho=$this->uf_select_derechos_usuario($codemp, $codusu, $ls_codsis, $ls_nomven, $ls_codintper, $as_hostname, $as_login, $as_password,$as_database,$as_gestor);
		  if (!$lb_existe_derecho)
		  { 
		    $lb_valido = $this->uf_select_ventana_sistema($ls_codsis, $ls_nomven, $as_hostname, $as_login, $as_password,$as_database,$as_gestor);
			if($lb_valido)
			{   
			   $ls_sql="INSERT INTO sss_derechos_usuarios(codemp, codusu, codsis, nomven, codintper, visible, enabled, ".
					   "                                  leer, incluir, cambiar, eliminar, imprimir, administrativo, anular, ejecutar) ".
					   "			VALUES ('".$codemp."','".$codusu."','".$ls_codsis."','".$ls_nomven."','".$ls_codintper."',".$li_visible.",".$li_enabled.",".
					   "                    ".$li_leer.",".$li_incluir.",". $li_cambiar.",".$li_eliminar.",".$li_imprimir.",".$li_administrativo.",".
					   "					".$li_anular.",".$li_ejecutar.")";		   	   			
			   $li_row=$io_sql_destino->execute($ls_sql);
			   if($li_row===false)
			   {
				 $lb_valido=false;
				 $this->io_msg->message("Error al Insertar el Derecho del Usuario  ".$codusu." ".$this->io_funcion->uf_convertirmsg($io_sql_destino->message));
			   }
			   else
			   { 
				$lb_valido = true;
			   }
			 }  
		  }
		 } // while
		}  // else
	 return $lb_valido;
	}
//------------------------------------------------------------------------------------------------------------------------------


	function uf_insertar_resultado($as_database_origen,$as_database_destino,$as_descripcion)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	Funcion       uf_insertar_resultado
	//	Access        public
	//	Arguments	  ai_totrows  // total de filas del detalle
	//				  ao_object  // objetos del detalle
	//	Returns	      lb_valido. Retorna una variable booleana
	//	Description   Registra el resultado de 	el proceso de transferencia de usuarios 
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_codproc = "SSSTUS";
		$ls_codsis =  "SSS";
		$li_codres = "";
		$this->io_sql->begin_transaction();
		$ls_sql="SELECT MAX(codres) as codigo FROM sigesp_dt_proc_cons";
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
		 $lb_valido=false;
		 $this->io_msg->message("Error al seleccionar Resultado".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{   
		 while($row=$this->io_sql->fetch_row($rs_data))
		 {
		   $li_codres=$row["codigo"];
		 }			
		}  // else
		if($lb_valido)  
		{
			$li_codres++;
			$ls_codres=str_pad($li_codres,10,"0",0);
			$ls_sql="INSERT INTO sigesp_dt_proc_cons (codres, codproc, codsis, fecha, bdorigen, bddestino, descripcion) ".
					"VALUES('".$ls_codres."','".$ls_codproc."', '".$ls_codsis."', '".date("Y-m-d H:i")."', ".
					"'".$as_database_origen."', '".$as_database_destino."','".$as_descripcion."') ";			
			$li_row=$this->io_sql->Execute($ls_sql);
			if($li_row===false)
			{
				$this->io_sql->rollback();
				$this->io_msg->message("Error al Insertar Resultado".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido = false;
			}
			else
			{
			  $this->io_sql->commit();
			}
	    }		
	 return $lb_valido;
	}
//------------------------------------------------------------------------------------------------------------------------------
	
}//end  class sigesp_sss_c_usuarios

?>
