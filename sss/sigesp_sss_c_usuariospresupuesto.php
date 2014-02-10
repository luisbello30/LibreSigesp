<?php 
class sigesp_sss_c_usuariospresupuesto
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;

	function sigesp_sss_c_usuariospresupuesto()
	{
		require_once("../shared/class_folder/class_sql.php");
		require_once("../shared/class_folder/sigesp_include.php");
		require_once("../shared/class_folder/class_mensajes.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		require_once("../shared/class_folder/class_funciones.php");
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
		$this->io_sql=new class_sql($this->con);
		$this->seguridad= new sigesp_c_seguridad;
		$this->io_msg=new class_mensajes();
		$this->io_funcion = new class_funciones();
		$this->ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
	}

	function  uf_sss_load_usuarios($as_codemp,&$aa_usuarios)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_load_usuarios
		//         Access: public  
		//      Argumento: $as_codemp      //codigo de empresa
		//                 $aa_usuarios    //arreglo de usuarios
		//	      Returns: Retorna un Booleano
		//    Description: Función que carga los datos de los usuarios
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 30/10/2006								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT * FROM sss_usuarios".
				" WHERE  codemp ='".$as_codemp."' ".
				" ORDER BY nomusu";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->usuariospresupuesto MÉTODO->uf_sss_load_usuarios ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_pos=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$aa_usuarios[$li_pos]["nomusu"]=$row["nomusu"];  
				$aa_usuarios[$li_pos]["apeusu"]=$row["apeusu"];  
				$aa_usuarios[$li_pos]["codusu"]=$row["codusu"];  
				$li_pos=$li_pos+1;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end  function  uf_sss_load_usuarios

	function  uf_sss_load_estructurasdisponibles($as_codemp,$as_codusu,&$aa_disponibles)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_load_estructurasdisponibles
		//         Access: public  
		//      Argumento: $as_codemp      //codigo de empresa
		//                 $as_codusu      //codigo de usuario
		//                 $aa_disponibles //arreglo de usuarios disponibles
		//	      Returns: Retorna un Booleano
		//    Description: Función que carga los datos de las nominas que estan disponibles para un determinado usuario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 30/10/2006								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_codestpro4="";
		$ls_codestpro5="";
		if($this->ls_estmodest==2)
		{
			$ls_sql=" SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla".
					"   FROM spg_ep5".
					"  WHERE codemp='".$as_codemp."' ";
		}
		else
		{
			$ls_sql=" SELECT codestpro1,codestpro2,codestpro3,estcla".
					"   FROM spg_ep3".
					"  WHERE codemp='".$as_codemp."' ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->usuariospresupuesto MÉTODO->uf_sss_load_estructurasdisponibles ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_pos=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_codestpro1=$row["codestpro1"];
				$ls_codestpro2=$row["codestpro2"];
				$ls_codestpro3=$row["codestpro3"];
				if($this->ls_estmodest==2)
				{
					$ls_codestpro4=$row["codestpro4"];
					$ls_codestpro5=$row["codestpro5"];
				}
				else
				{
				 $ls_codestpro4="0000000000000000000000000";
				 $ls_codestpro5="0000000000000000000000000";
				}
				$ls_estcla=$row["estcla"];
				$ls_estpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla;
				
                $ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
				$ls_incio1=25-$ls_loncodestpro1;
				$ls_codestpro1=substr($ls_codestpro1,$ls_incio1,$ls_loncodestpro1);
				
				$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
				$ls_incio2=25-$ls_loncodestpro2;
				$ls_codestpro2=substr($ls_codestpro2,$ls_incio2,$ls_loncodestpro2);
				
				$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
				$ls_incio3=25-$ls_loncodestpro3;
				$ls_codestpro3=substr($ls_codestpro3,$ls_incio3,$ls_loncodestpro3);
				
				$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
				$ls_incio4=25-$ls_loncodestpro4;
				$ls_codestpro4=substr($ls_codestpro4,$ls_incio4,$ls_loncodestpro4);
				
				$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
				$ls_incio5=25-$ls_loncodestpro5;
				$ls_codestpro5=substr($ls_codestpro5,$ls_incio5,$ls_loncodestpro5);
				
				$ls_progra=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla;
				
				$lb_existe=$this->uf_sss_select_usuario_presupuesto($as_codemp,$ls_estpro,$as_codusu);
				if(!$lb_existe)
				{
					$aa_disponibles["codest"][$li_pos]=$ls_estpro;
					$aa_disponibles["codestmost"][$li_pos]=$ls_progra;  
					$li_pos=$li_pos+1;
				}
			} 
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end  function  uf_sss_load_estructurasdisponibles

	function  uf_sss_select_usuario_presupuesto($as_codemp,$as_codest,$as_codusu)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_select_usuario_presupuesto
		//         Access: public  
		//      Argumento: $as_codemp   // codigo de empresa
		//                 $as_codest   // codigo de estructura programatica
		//                 $as_codusu   // codigo de usuario
		//	      Returns: Retorna un Booleano
		//    Description: Función que se encarga de verificar si una estructura programatica existe para un determinado usuario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 30/10/2006								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT codusu FROM sss_permisos_internos".
				  " WHERE codemp = '".$as_codemp."'".
				  "   AND codintper ='".$as_codest."'".
				  "   AND codusu ='".$as_codusu."'".
				  "   AND codsis ='SPG'" ; //print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->usuariospresupuesto MÉTODO->uf_sss_select_usuario_presupuesto ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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
	}  // end  function  uf_sss_select_usuario_presupuesto

	function  uf_sss_load_estructurasasignadas($as_codemp,$as_codusu,&$aa_asignados)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_load_estructurasasignadas
		//         Access: public  
		//      Argumento: $as_codemp     //codigo de empresa
		//                 $as_codusu     //codigo de usuario
		//                 $aa_asignados  //arreglo de usuarios asignados
		//	      Returns: Retorna un Booleano
		//    Description: Función que carga los datos de las estructuras que estan asignados para un determinado usuario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 30/10/2006								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "SELECT codintper".
				 " FROM sss_permisos_internos".
				 " WHERE codemp= '".$as_codemp."'".
				 "   AND codusu= '".$as_codusu."'".
				 "   AND codintper <> '---------------------------------' ".
				 "   AND codintper <> '' AND codintper <> '------------------------------------------------------------------------------------------------------------------------------' ".  
				 "   AND codsis= 'SPG'"; //print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->usuariospresupuesto MÉTODO->uf_sss_load_estructurasasignadas ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_pos=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
					
				$aa_asignados["codintper"][$li_pos]=$row["codintper"]; 
				$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
				$ls_codestpro1=substr($row["codintper"],0,25);
				$ls_incio1=25-$ls_loncodestpro1;
				$ls_codestpro1=substr($ls_codestpro1,$ls_incio1,$ls_loncodestpro1);
					
				$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
				$ls_codestpro2=substr($row["codintper"],25,25); 
				$ls_incio2=25-$ls_loncodestpro2;
				$ls_codestpro2=substr($ls_codestpro2,$ls_incio2,$ls_loncodestpro2);	 
				
				$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
				$ls_codestpro3=substr($row["codintper"],50,25);
				$ls_incio3=25-$ls_loncodestpro3;
				$ls_codestpro3=substr($ls_codestpro3,$ls_incio3,$ls_loncodestpro3);	
				
				$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
				$ls_codestpro4=substr($row["codintper"],75,25);
				$ls_incio4=25-$ls_loncodestpro4;
				$ls_codestpro4=substr($ls_codestpro4,$ls_incio4,$ls_loncodestpro4);	
				
				$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
				$ls_codestpro5=substr($row["codintper"],100,25);
				$ls_incio5=25-$ls_loncodestpro5;
				$ls_codestpro5=substr($ls_codestpro5,$ls_incio5,$ls_loncodestpro5);	
				
				$ls_estcla = substr($row["codintper"],125,1);
						
			    $ls_estpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla;
				$aa_asignados["codintpermost"][$li_pos]=$ls_estpro;
				$li_pos=$li_pos+1;
			}
			$this->io_sql->free_result($rs_data); 
		}
		return $lb_valido;
	}  // end  function  uf_sss_load_estructurasasignadas

	function  uf_sss_insert_usuario_estructura($as_codemp,$as_codest,$as_codusu,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_insert_usuario_estructura
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//      		   $as_codest    // codigo de estrctura presupuestaria (codigo interno de permisologia)
		//      		   $as_codusu    // codigo de usuario
		//      		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: funcion que inserta un usuario en determinado estructura presupuestaria en la tabla sss_permisos_internos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 30/10/2006									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;  
		if($as_codest!="---------------------------------")
		{ 
			$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
			$ls_codestpro1=substr($as_codest,0,25);
				
			$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
			$ls_codestpro2=substr($as_codest,25,25); 
			
			$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
			$ls_codestpro3=substr($as_codest,50,25);
			
			$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
			$ls_codestpro4=substr($as_codest,75,25);
			
			$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
			$ls_codestpro5=substr($as_codest,100,25);
			
			$ls_estcla=substr($as_codest,125,1);  
			
			$as_codestpro1=str_pad($ls_codestpro1,25,0); 
			$as_codestpro2=str_pad($ls_codestpro2,25,0); 
			$as_codestpro3=str_pad($ls_codestpro3,25,0); 
			$as_codestpro4=str_pad($ls_codestpro4,25,0); 
			$as_codestpro5=str_pad($ls_codestpro5,25,0); 
			
			$as_codest=$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5.$ls_estcla;
		} 
		$ls_sql = "INSERT INTO sss_permisos_internos (codemp, codsis, codusu, codintper) ".
				  "     VALUES('".$as_codemp."','SPG','".$as_codusu."','".$as_codest."')" ; 
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->usuariospresupuesto MÉTODO->uf_sss_insert_usuario_estructura ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Relacionó la Estructura ".$as_codest." al usuario ".$as_codusu." Asociado a la empresa ".$as_codemp;
			$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	} // end  function  uf_sss_insert_usuario_estructura

	function uf_sss_delete_usuario_estructura($as_codemp,$as_codest,$as_codusu,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_delete_usuario_estructura
		//         Access: public  
		//      Argumento: $as_codemp     // codigo de empresa
		//      		   $as_codest     // codigo de estructura presupuestaria
		//      		   $as_codusu     // codigo de usuario
		//      		   $aa_seguridad  // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: funcion que elimina un usuario en determinada estructura en la tabla sss_permisos_internos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 30/10/2006									Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "DELETE FROM sss_permisos_internos".
				 " WHERE codemp= '".$as_codemp. "'".
				 "   AND codintper= '".$as_codest. "'".
				 "   AND codusu= '".$as_codusu."'".
				 "   AND codsis='SPG'";		  		 	 
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->usuariospresupuesto MÉTODO->uf_sss_delete_usuario_estructura ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$ls_evento="DELETE";
			$ls_descripcion ="Eliminó la Estructura ".$as_codest." al usuario ".$as_codusu." Asociado a la empresa ".$as_codemp;
			$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////			
			$lb_valido=true;
		}
		return $lb_valido;
	}  // end  function uf_sss_delete_usuario_nomina

	function  uf_sss_load_permisos($as_codemp,$as_codest,$as_codusu,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_load_permisos
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//                 $as_codest    // codigo de estructura presupuestaria
		//                 $as_codusu    // codigo de usuario
		//                 $aa_seguridad //arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Función que verifica si un usuario tiene definido algun perfil de seguridad en las estructuras presup.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 30/10/2006								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="  SELECT nomven,visible,enabled,leer,incluir,cambiar,eliminar,imprimir,administrativo,anular,ejecutar".
				"    FROM sss_derechos_usuarios".
				"   WHERE codemp= '".$as_codemp."'".
				"     AND codusu= '".$as_codusu."'".
				"     AND codsis='SPG' ".
				"GROUP BY nomven,visible,enabled,leer,incluir,cambiar,eliminar,imprimir,administrativo,anular,ejecutar";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->usuariospresupuesto MÉTODO->uf_sss_load_permisos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_pos=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_nomven=$row["nomven"];  
				$li_visible=$row["visible"];  
				$li_enabled=$row["enabled"];  
				$li_leer=$row["leer"];  
				$li_incluir=$row["incluir"];  
				$li_cambiar=$row["cambiar"];  
				$li_eliminar=$row["eliminar"];  
				$li_imprimir=$row["imprimir"];  
				$li_administrador=$row["administrativo"];  
				$li_anular=$row["anular"];  
				$li_ejecutar=$row["ejecutar"];  
				$lb_valido=$this->uf_sss_insert_derecho_usuario($as_codemp,$as_codusu,'SPG',$ls_nomven,$li_visible,$li_enabled,
									   					 		$li_leer,$li_incluir,$li_cambiar,$li_eliminar,$li_imprimir,
														 		$li_administrador,$li_anular,$li_ejecutar,$as_codest);
				if(!$lb_valido)
				{break;}
				$li_pos=$li_pos+1;
			}
			if(($li_pos>0)&&($lb_valido))
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion="Actualizó el perfil de seguridad en la Estructura ".$as_codest." al usuario ".$as_codusu.
								 " Asociado a la empresa ".$as_codemp;
				$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end  function  uf_sss_load_permisos

	function  uf_sss_insert_derecho_usuario($as_codemp,$as_codusu,$as_codsis,$as_nomven,$ai_visible,$ai_enabled,$ai_leer,
											$ai_incluir,$ai_cambiar,$ai_eliminar,$ai_imprimir,$ai_administrador,$ai_anular,
											$ai_ejecutar,$as_codintper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_insert_derecho_usuario
		//         Access: public  
		//      Argumento: $as_codemp        // codigo de empresa
		//      		   $as_codusu        // codigo de usuario
		//      		   $as_codsis        // codigo de sistema
		//      		   $as_nomven        // nombre de la ventana (fisico)
		//      		   $ai_visible       // indica si puede ver o no la pantalla
		//      		   $ai_enabled       // indica si tiene permiso o no a la pantalla
		//      		   $ai_leer          // indica si tiene permiso o no de lectura
		//      		   $ai_incluir       // indica si tiene permiso o no de incluir
		//      		   $ai_cambiar       // indica si tiene permiso o no demodificar
		//      		   $ai_habilitada    // indica si tiene permiso o no 
		//      		   $ai_imprimir      // indica si tiene permiso o no de imprimir
		//      		   $ai_administrador // indica si tiene permiso o no de administrador
		//      		   $ai_anular        // indica si tiene permiso o no de anular
		//      		   $ai_ejecutar      // indica si tiene permiso o no de ejecutar
		//      		   $as_codintper     // codigo interno de permisos
		//    Description: Función que se encarga de otorgar permisos a un usuario en determinada  pantalla
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 27/10/2006									Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "INSERT INTO sss_derechos_usuarios (codemp,codusu,codsis,nomven,visible,enabled,leer,incluir,cambiar,". 
				  "									  eliminar,imprimir,administrativo,anular,ejecutar,codintper) ".
				  "     VALUES('".$as_codemp."','".$as_codusu."','".$as_codsis."','".$as_nomven."',".$ai_visible.",".
				  " 	        ".$ai_enabled.",".$ai_leer.",".$ai_incluir.",".$ai_cambiar.",".$ai_eliminar.",".$ai_imprimir.",".
				  "             ".$ai_administrador.",".$ai_anular.",".$ai_ejecutar.",'".$as_codintper."')" ;
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{print $this->io_sql->message;
			$this->io_msg->message("CLASE->usuariospresupuesto MÉTODO->uf_sss_insert_derecho_usuario ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	}  // end  function  uf_sss_insert_derecho_usuario

	function  uf_sss_delete_permisos($as_codemp,$as_codest,$as_codusu,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sss_delete_permisos
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//                 $as_codusu    // codigo de usuario
		//                 $as_codest    // codigo de estructura
		//                 $aa_seguridad //arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Función que elimina los permisos de un usuario a alguna nomina en especifico
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 30/10/2006								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="DELETE FROM sss_derechos_usuarios".
			    " WHERE codemp='" .$as_codemp ."'".
			    "   AND codusu='" .$as_codusu ."'".
			    "   AND codsis='SPG'".
			    "   AND codintper='" .$as_codest ."'";			
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->usuariospresupuesto MÉTODO->uf_sss_delete_permisos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion="Eliminó el perfil de seguridad en la Estructura ".$as_codest." al usuario ".$as_codusu.
							 " Asociado a la empresa ".$as_codemp;
			$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$lb_valido=true;
		}
		return $lb_valido;
	}  // end  function  uf_sss_delete_permisos
	
}//  end  class sigesp_sss_c_usuariospresupuesto

?>
