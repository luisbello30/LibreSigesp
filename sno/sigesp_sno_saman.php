<?php
class sigesp_sno_saman
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;
	var $ls_codnom;
	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_sno_saman()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_saman
		//		   Access: public
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("sigesp_sno.php");
		$this->io_sno=new sigesp_sno();
		$ls_hostname=$this->io_sno->uf_select_config("SNO","NOMINA","SAMAN_HOSTNAME","C","");
		$ls_port=$this->io_sno->uf_select_config("SNO","NOMINA","SAMAN_PORT","C","");
		$ls_database=$this->io_sno->uf_select_config("SNO","NOMINA","SAMAN_DATABASE","C","");
		$ls_gestor=$this->io_sno->uf_select_config("SNO","NOMINA","SAMAN_GESTOR","C","");
		$ls_login=$this->io_sno->uf_select_config("SNO","NOMINA","SAMAN_LOGIN","C","");
		$ls_password=$this->io_sno->uf_select_config("SNO","NOMINA","SAMAN_PASSWORD","C","");
		require_once("../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar_otra_bd ($ls_hostname.":".$ls_port,$ls_login,$ls_password,$ls_database,$ls_gestor);
		require_once("../shared/class_folder/class_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		require_once("../shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
   		require_once("../shared/class_folder/class_funciones.php");
		$this->io_funciones=new class_funciones();				
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
	}// end function sigesp_sno_saman
	//-----------------------------------------------------------------------------------------------------------------------------------
 
 	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insertar_personal($ds_personal)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insertar_personal
		//		   Access: private
		//	    Arguments:  
		//	      Returns: lb_existe True si no existe ó False si existe
		//	  Description: Funcion que inserta en una base de datos externa
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/04/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nropersona=0;
		$ls_usuario=substr("SIGESP-".$_SESSION["ls_login"],0,16);
		$lb_existe=$this->uf_verificar_personal($ds_personal,&$ls_nropersona);
		if(!$lb_existe)
		{
			$lb_valido=$this->uf_select_correlativo(&$ls_nropersona,$ls_usuario);
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_persona($ds_personal,$ls_nropersona,$ls_usuario);
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_afiliacion_persona($ds_personal,$ls_nropersona,$ls_usuario);
			}
			if($lb_valido)
			{
				$this->io_mensajes->message("El personal ".$ds_personal->getValue("cedper",1)." fue insertado en SAMAN.");
			}
		}
		return $lb_valido;
	}// end function uf_insertar_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

 	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_personal($ds_personal,&$as_nropersona)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_personal
		//		   Access: private
		//	    Arguments: $as_codper // código del personal
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si el personal ya esta registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/04/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT nropersona ".
				"  FROM personas ".
				" WHERE tipnip = '".$ds_personal->getValue("nacper",1)."'". // NACIONALIDAD
				"   AND codnip = '".$ds_personal->getValue("cedper",1)."'"; // CÉDULA
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SAMAN MÉTODO->uf_verificar_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_existe=false;
		}
		else
		{
			if($rs_data->EOF)
			{
				$lb_existe=false;
			}
			else
			{
				$as_nropersona=$rs_data->fields["nropersona"];
			}
		}
		return $lb_existe;
	}// end function uf_verificar_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

 	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_correlativo(&$as_nropersona,$as_usuario)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_correlativo
		//		   Access: private
		//	    Arguments: $as_nropersona // Nro de la persona
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que actualiza el correlativo de las personas y devuelve el nro actual
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/04/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE correlativos_pers ".
				"  SET secuenciaentero = (secuenciaentero + 1),  ".
				"	   _version_hb = (_version_hb + 1), ".
				"      auditfechacambio = '".date("Y/m/d")."', ".
				"      audithoracambio = '".date("H:i:s")."', ".
				"      auditcodusuario = '".$as_usuario."'".
				" WHERE ciaopr = '1'". 
				"   AND tabla = 'PERSONAS'".
				"	AND codigo = 'nropersona' "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SAMAN MÉTODO->uf_select_correlativo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		if($lb_valido)
		{
			$ls_sql="SELECT secuenciaentero ".
					"  FROM correlativos_pers ".
					" WHERE ciaopr = '1'". 
					"   AND tabla = 'PERSONAS'".
					"	AND codigo = 'nropersona' "; 
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->SAMAN MÉTODO->uf_select_correlativo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if(!$rs_data->EOF)
				{
					$as_nropersona=$rs_data->fields["secuenciaentero"];
				}
			}
		}
		return $lb_valido;
	}// end function uf_select_correlativo
	//-----------------------------------------------------------------------------------------------------------------------------------

 	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_persona($ds_personal,$as_nropersona,$as_usuario)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_persona
		//		   Access: private
		//	    Arguments: $ds_personal // Datastored con los datos de la persona
		//	               $as_nropersona // Nro de la persona
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que inserta una nueva persona
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/04/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_pos=strpos($ds_personal->getValue("nomper",1)," ");
		if($li_pos===false)
		{
			$ls_nombre1=$ds_personal->getValue("nomper",1);
			$ls_nombre2="";
		}
		else
		{
			$ls_nombre1=trim(substr($ds_personal->getValue("nomper",1),0,$li_pos));
			$ls_nombre2=trim(substr($ds_personal->getValue("nomper",1),$li_pos+1));
		}
		$li_pos=strpos($ds_personal->getValue("apeper",1)," ");
		if($li_pos===false)
		{
			$ls_apellido1=$ds_personal->getValue("apeper",1);
			$ls_apellido2="";
		}
		else
		{
			$ls_apellido1=trim(substr($ds_personal->getValue("apeper",1),0,$li_pos));
			$ls_apellido2=trim(substr($ds_personal->getValue("apeper",1),$li_pos+1));
		}
		$ls_nombrecompleto=$ds_personal->getValue("nomper",1)." ".$ds_personal->getValue("apeper",1);
		$ls_nombrecorto=$ls_nombre1." ".$ls_apellido1;
		$ld_fecnac=str_replace("-","/",$ds_personal->getValue("fecnacper",1));
		$ld_fecingper=str_replace("-","/",$ds_personal->getValue("fecingper",1));
		$ls_sql="INSERT INTO personas(ciaopr,nropersona,tipnip,codnip,nombreprimero,nombresegundo,apellidoprimero,apellidosegundo,".
				"nombrecompleto,nombrecorto,sexocod,edocivilcod,fechanacimiento,email1,auditfechacreacion,audithoracreacion,auditcodusuario,".
				"_version_hb,nombrecompletoupp,nacionalidadcod)VALUES".
				"('1','".$as_nropersona."','".$ds_personal->getValue("nacper",1)."','".$ds_personal->getValue("cedper",1)."','".$ls_nombre1."',".
				"'".$ls_nombre2."','".$ls_apellido1."','".$ls_apellido2."','".$ls_nombrecompleto."','".$ls_nombrecorto."',".
				"'".$ds_personal->getValue("sexper",1)."','".$ds_personal->getValue("edocivper",1)."','".$ld_fecnac."',".
				"'".$ds_personal->getValue("coreleper",1)."','".$ld_fecingper."','".date("H:i:s")."','".$as_usuario."',0,".
				"'".strtoupper($ls_nombrecompleto)."','VEN')";		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SAMAN MÉTODO->uf_insert_persona ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_insert_persona
	//-----------------------------------------------------------------------------------------------------------------------------------

 	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_afiliacion_persona($ds_personal,$as_nropersona,$as_usuario)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_afiliacion_persona
		//		   Access: private
		//	    Arguments: $ds_personal // Datasotred con los datos de la persona
		//	               $as_nropersona // Nro de la persona
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que inserta la afiliación de las personas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/04/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_fecingper=str_replace("-","/",$ds_personal->getValue("fecingper",1));
		$ls_sql="INSERT INTO pers_dat_afiliac(ciaopr,nropersona,perafilsecuencia,tipafilcod,fchinicafiliacion,estafilcod,nrocarnet,".
				"							  fchemicarnet,fchvctocarnet,auditfechacreacion,audithoracreacion,auditcodusuario,_version_hb,".
				"							  nropersonatitular, fchcambest, razestafilcod, persrelstipcod)".		
				"VALUES ('1','".$as_nropersona."', '".$as_nropersona."', 'AE', '".$ld_fecingper."', 'ACT', '', '', '', '".date("Y/m/d")."', ".
				"		'".date("H:i:s")."', '".$as_usuario."', 0, '".$as_nropersona."', '".$ld_fecingper."', 'INCTI', 'TIT') "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SAMAN MÉTODO->uf_insert_afiliacion_persona ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		if($lb_valido)
		{
			$ls_sql="INSERT INTO pers_relaciones(ciaopr,nropersona,nropersonarel,persrelstipcod,fechainicrelacion,estatusrelacion) ".
					"VALUES ('1','1003217','".$as_nropersona."','JEF','".$ld_fecingper."','S')  ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->SAMAN MÉTODO->uf_insert_afiliacion_persona ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_insert_afiliacion_persona
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>