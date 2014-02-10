<?php
class sigesp_soc_class_report
{
	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_soc_class_report()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_soc_class_report
		//		   Access: public
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yozelin Barragan /Ing. Nestor Falcon /Ing. Laura Cabre
		// Fecha Creaci�n: 18/06/2007.
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../shared/class_folder/sigesp_include.php");
		require_once("../../shared/class_folder/class_sql.php");
                
		require_once("../../shared/class_folder/class_mensajes.php");
		require_once("../../shared/class_folder/class_funciones.php");

		$io_include         = new sigesp_include();
		$this->io_conexion  = $io_include->uf_conectar();
		$this->io_sql       = new class_sql($this->io_conexion);
		$this->io_mensajes  = new class_mensajes();
		$this->io_funciones = new class_funciones();
                $this->ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		$this->ls_codusu    = $_SESSION["la_logusr"];
                
	}// end function sigesp_soc_class_report
	//-----------------------------------------------------------------------------------------------------------------------------------

function uf_load_cabecera_formato_solicitud_cotizacion($as_numsolcot,$as_tipsolcot,$as_fecsolcot,$as_tabla,&$lb_valido,$as_codprov)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function:  uf_load_cabecera_formato_solicitud_cotizacion
//		   Access:  public
//		 Argument:
//   $as_numsolcot  //N�mero de la Solicitud de Cotizaci�n.
//   $as_tipsolcot  //Tipo de la Solicitud de Cotizaci�n.
//       $as_tabla  //Nombre de la tabla detalle de la Solicitud de Cotizaci�n.
//      $lb_valido  //Variable booleana que devuelve true si todo se ejecut� con �xito, false de lo contrario.
//	  Description:  Funci�n que busca los datos de la cabecera de la Solicitud de Cotizacion.
//	   Creado Por:  Ing. Nestor Falcon.
// Fecha Creaci�n:  18/06/2007								Fecha �ltima Modificaci�n : 19/06/2007
////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido    = true;
  $ls_fecsolcot = $this->io_funciones->uf_convertirdatetobd($as_fecsolcot);

  $ls_sql = "SELECT $as_tabla.cod_pro,
  					max(soc_sol_cotizacion.fecsol) as fecsol,
					max(soc_sol_cotizacion.obssol) as obssol,
                                        max(soc_sol_cotizacion.consolcot) as consolcot,
					max(soc_sol_cotizacion.cedper) as cedper,
					max(soc_sol_cotizacion.soltel) as soltel,
					max(rpc_proveedor.nompro) as nompro,
					max(rpc_proveedor.dirpro) as dirpro,
					max(rpc_proveedor.telpro) as telpro,
					max(rpc_proveedor.rifpro) as rifpro,
					max(rpc_proveedor.email) as email,
					max(rpc_proveedor.faxpro) as faxpro,
					max(curPersonal.nomper) as nomper,
					max(curPersonal.apeper) as apeper
			   FROM soc_sol_cotizacion,
			   		(SELECT sc.codemp,sc.cedper,sp.nomper,sp.apeper
					   FROM soc_sol_cotizacion sc LEFT OUTER JOIN sno_personal sp
						 ON sc.codemp=sp.codemp
						AND sc.cedper=sp.cedper) as curPersonal,
					$as_tabla, rpc_proveedor
			  WHERE $as_tabla.codemp='".$this->ls_codemp."'
			    AND $as_tabla.numsolcot='".$as_numsolcot."'
			    AND soc_sol_cotizacion.tipsolcot='".$as_tipsolcot."'
				AND soc_sol_cotizacion.fecsol='".$ls_fecsolcot."'
			  	AND	soc_sol_cotizacion.codemp=curPersonal.codemp
				AND soc_sol_cotizacion.codemp=$as_tabla.codemp
				AND soc_sol_cotizacion.numsolcot=$as_tabla.numsolcot
				AND soc_sol_cotizacion.codemp=rpc_proveedor.codemp
				AND $as_tabla.codemp=rpc_proveedor.codemp
				AND $as_tabla.cod_pro=rpc_proveedor.cod_pro";
		  if($as_codprov!='')
			       $ls_sql.=" AND $as_tabla.cod_pro='".$as_codprov."'";
 $ls_sql.="  GROUP BY $as_tabla.cod_pro";



  $rs_data = $this->io_sql->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_mensajes->message("CLASE->sigesp_soc_class_report.php->M�TODO->uf_load_cabecera_formato_solicitud_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
	 }
  return $rs_data;
}//function uf_load_cabecera_formato_solicitud_cotizacion

//-----------------------------------------------------------------------------------------------------------------------------------
function uf_load_dt_solicitud_cotizacion($as_numsolcot,$as_codpro,$as_tabla,$as_table,$as_campo,&$lb_valido)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function:  uf_load_dt_solicitud_cotizacion
//		   Access:  public
//		 Argument:
//  $as_numsolcot  //N�mero de la Solicitud de Cotizaci�n.
//     $as_codpro  //C�digo del Proveedor asociado a esa Solicitud de Cotizaci�n.
//      $as_tabla  //Nombre de la Tabla donde se localizara el detalle de la Solicitud de Cotizaci�n,
//                   soc_dtsc_bienes para solicitus de bienes, soc_dtsc_servicios para servicios.
//      $as_table  //Nombre de la tabla de donde extraeremos la denominacion del Item, siv_articulo para los bienes y
//                   soc_servicios para los servicios.
//      $as_campo  //Campo para el enlace del item con su tabla maestro, codart para bienes y codser para servicios.
//     $lb_valido  //Variable booleana que devuelve true si todo se ejecut� con �xito, false de lo contrario.
//	  Description: Funci�n que busca los detalles una Solicitud de Cotizaci�n para un proveedor en particular.
//	   Creado Por: Ing. Nestor Falcon.
// Fecha Creaci�n: 19/06/2007								Fecha �ltima Modificaci�n : 19/06/2007
////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  $ls_straux = "";
  $ls_newtab="";
  if ($as_tabla=='soc_dtsc_bienes')
     {
	   $ls_straux = " $as_tabla.$as_campo as codite,max($as_table.denart) as denite,sum($as_tabla.canart) as canite, max($as_tabla.unidad) as unidad, max(siv_unidadmedida.denunimed) as denunimed";
	   $ls_newtab = ", siv_unidadmedida";
	   $ls_sqlaux = " AND $as_table.codunimed=siv_unidadmedida.codunimed";
	 }
  elseif($as_tabla=='soc_dtsc_servicios')
     {
	   //$ls_straux = " $as_tabla.$as_campo as codite,max($as_table.denser) as denite,max($as_tabla.canser) as canite, max(soc_servicios.codunimed) as codunimed, max(siv_unidadmedida.denunimed) as denunimed";
	   //$ls_newtab = ", siv_unidadmedida";
	   //$ls_sqlaux = $ls_sqlaux = " AND soc_servicios.codunimed=siv_unidadmedida.codunimed";

	   $ls_straux = " $as_tabla.$as_campo as codite,max($as_table.denser) as denite,max($as_tabla.canser) as canite, '00000' as codunimed, 'SERVICIO' as denunimed ";
	   $ls_newtab = " ";
	   $ls_sqlaux = $ls_sqlaux = "  ";
	 }
  $ls_sql = "SELECT $ls_straux, max($as_tabla.orden) as orden
			   FROM soc_sol_cotizacion, $as_tabla, $as_table $ls_newtab
			  WHERE $as_tabla.codemp='".$this->ls_codemp."'
			    AND $as_tabla.numsolcot='".$as_numsolcot."'
			    AND $as_tabla.cod_pro='".$as_codpro."'
			    AND soc_sol_cotizacion.codemp=$as_tabla.codemp
			    AND soc_sol_cotizacion.numsolcot=$as_tabla.numsolcot
			    AND $as_tabla.$as_campo=$as_table.$as_campo $ls_sqlaux
			  GROUP BY $as_tabla.$as_campo
	 		  ORDER BY $as_tabla.$as_campo";
  $rs_data = $this->io_sql->select($ls_sql);//print $ls_sql;

    if ($rs_data===false)
    {
            print $this->io_sql->message;
            $lb_valido = false;
            $this->io_mensajes->message("CLASE->sigesp_soc_class_report.php->M�TODO->uf_load_dt_solicitud_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
    }
  return $rs_data;
}//function uf_load_dt_solicitud_cotizacion.

//-----------------------------------------------------------------------------------------------------------------------------------
function uf_load_cabecera_formato_registro_cotizacion($as_numcot,$as_tipcot,$as_feccot,$as_codpro,$as_tabla,&$lb_valido)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function:  uf_load_cabecera_formato_registro_cotizacion
//		   Access:  public
//		 Argument:
//   $ls_numsolcot  //N�mero de la Cotizaci�n.
//   $ls_tipsolcot  //Tipo de la Cotizaci�n.
//      $lb_valido  //Variable booleana que devuelve true si todo se ejecut� con �xito, false de lo contrario.
//	  Description:  Funci�n que busca los datos de la cabecera de la Cotizacion.
//	   Creado Por:  Ing. Nestor Falcon.
// Fecha Creaci�n:  20/06/2007								Fecha �ltima Modificaci�n : 20/06/2007
////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  $ls_feccot = $this->io_funciones->uf_convertirdatetobd($as_feccot);

  $ls_sql = "SELECT $as_tabla.cod_pro,
                    max(rpc_proveedor.nompro) as nompro,
                    max(rpc_proveedor.dirpro) as dirpro,
					max(rpc_proveedor.telpro) as telpro,
					max(rpc_proveedor.rifpro) as rifpro,
					max(rpc_proveedor.email) as email,
					max(soc_cotizacion.feccot) as feccot,
					max(soc_cotizacion.obscot) as obscot,
					max(soc_cotizacion.forpagcom) as forpagcom,
					max(soc_cotizacion.numsolcot) as numsolcot,
					max(soc_cotizacion.monsubtot) as monsubtot,
					max(soc_cotizacion.monimpcot) as monimpcot,
					max(soc_cotizacion.montotcot) as montotcot,
					max(soc_cotizacion.diaentcom) as diaentcom,
                    max(soc_cotizacion.diavalofe) as diavalofe 
			   FROM soc_cotizacion, $as_tabla, rpc_proveedor
			  WHERE $as_tabla.codemp='".$this->ls_codemp."'
			    AND $as_tabla.numcot='".$as_numcot."'
                AND soc_cotizacion.cod_pro='".$as_codpro."'
				AND soc_cotizacion.tipcot='".$as_tipcot."'
				AND soc_cotizacion.feccot='".$ls_feccot."'
			    AND soc_cotizacion.codemp=$as_tabla.codemp
			    AND soc_cotizacion.numcot=$as_tabla.numcot
			    AND soc_cotizacion.codemp=rpc_proveedor.codemp
				AND $as_tabla.codemp=rpc_proveedor.codemp
			    AND $as_tabla.cod_pro=rpc_proveedor.cod_pro
			  GROUP BY $as_tabla.cod_pro";
  $rs_data = $this->io_sql->select($ls_sql); //print $ls_sql;
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_mensajes->message("CLASE->sigesp_soc_class_report.php->M�TODO->uf_load_cabecera_formato_solicitud_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
	 }
  return $rs_data;
}//function uf_load_cabecera_formato_registro_cotizacion

//-----------------------------------------------------------------------------------------------------------------------------------
function uf_load_dt_registro_cotizacion($as_numcot,$as_codpro,$as_tabla,$as_table,$as_campo,&$lb_valido)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function:  uf_load_dt_registro_cotizacion
//		   Access:  public
//		 Argument:
//  $as_numsolcot  //N�mero de la Solicitud de Cotizaci�n.
//     $as_codpro  //C�digo del Proveedor asociado a esa Solicitud de Cotizaci�n.
//      $as_tabla  //Nombre de la Tabla donde se localizara el detalle de la Solicitud de Cotizaci�n,
//                   soc_dtsc_bienes para solicitus de bienes, soc_dtsc_servicios para servicios.
//      $as_table  //Nombre de la tabla de donde extraeremos la denominacion del Item, siv_articulo para los bienes y
//                   soc_servicios para los servicios.
//      $as_campo  //Campo para el enlace del item con su tabla maestro, codart para bienes y codser para servicios.
//     $lb_valido  //Variable booleana que devuelve true si todo se ejecut� con �xito, false de lo contrario.
//	  Description: Funci�n que busca los detalles una Solicitud de Cotizaci�n para un proveedor en particular.
//	   Creado Por: Ing. Nestor Falcon.
// Fecha Creaci�n: 19/06/2007								Fecha �ltima Modificaci�n : 19/06/2007
////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  $ls_straux = "";
  if ($as_tabla=='soc_dtcot_bienes')
     {
	   $ls_straux = " $as_tabla.$as_campo as codite,$as_table.denart as denite,$as_tabla.canart as canite,(SELECT siv_unidadmedida.denunimed FROM siv_unidadmedida WHERE $as_table.codunimed=siv_unidadmedida.codunimed) as denunimed,
	                  $as_tabla.preuniart as preite, $as_tabla.monsubart as subite, $as_tabla.montotart as totite";
	 }
  elseif($as_tabla=='soc_dtcot_servicio')
     {
	   $ls_straux = " $as_tabla.$as_campo as codite,$as_table.denser as denite,$as_tabla.canser as canite,(SELECT siv_unidadmedida.denunimed FROM siv_unidadmedida WHERE $as_table.codunimed=siv_unidadmedida.codunimed) as denunimed,
	                  $as_tabla.monuniser as preite, $as_tabla.monsubser as subite, $as_tabla.montotser as totite";
	 }
  $ls_sql = "SELECT $ls_straux
			   FROM soc_cotizacion, $as_tabla, $as_table
			  WHERE $as_tabla.codemp='".$this->ls_codemp."'
			    AND $as_tabla.numcot='".$as_numcot."'
			    AND $as_tabla.cod_pro='".$as_codpro."'
			    AND soc_cotizacion.codemp=$as_tabla.codemp
			    AND soc_cotizacion.numcot=$as_tabla.numcot
			    AND $as_tabla.$as_campo=$as_table.$as_campo
	 		  ORDER BY $as_tabla.orden";

  $rs_data = $this->io_sql->select($ls_sql);//print $ls_sql;
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_mensajes->message("CLASE->sigesp_soc_class_report.php->M�TODO->uf_load_dt_registro_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
	 }
  return $rs_data;
}//function uf_load_dt_registro_cotizacion.

    function uf_select_orden_imprimir($as_numordcom,$as_estcondat,&$lb_valido)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_orden_imprimir
		//         Access: public
		//	    Arguments: as_numordcom    ---> Orden de Compra a imprimir
		//                 $as_estcondat  ---< tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca una orden de compra para imprimir
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql=" SELECT  soc_ordencompra.codemp,	soc_ordencompra.codusuapr, soc_ordencompra.numordcom,soc_ordencompra.estcondat,".
				" soc_ordencompra.cod_pro,soc_ordencompra.codfuefin,soc_ordencompra.fecordcom,".
				" soc_ordencompra.obscom,soc_ordencompra.obsordcom,soc_ordencompra.monsubtot,soc_ordencompra.estcom,soc_ordencompra.estapro,".
				" soc_ordencompra.monimp,soc_ordencompra.montot,soc_ordencompra.coduniadm,".
				" soc_ordencompra.forpagcom,soc_ordencompra.diaplacom,spg_unidadadministrativa.denuniadm,".
				" soc_ordencompra.montotdiv,0 as montotaux,soc_ordencompra.lugentnomdep,".
				" soc_ordencompra.lugentdir,soc_ordencompra.concom,soc_ordencompra.fecent,".
				" soc_ordencompra.estlugcom,soc_ordencompra.codmon,soc_ordencompra.codpai,".
				" soc_ordencompra.codest,soc_ordencompra.codmun,soc_ordencompra.codpar,".
				" soc_ordencompra.estsegcom,soc_ordencompra.porsegcom,soc_ordencompra.monsegcom,".
				" soc_ordencompra.monant,soc_ordencompra.tascamordcom,rpc_proveedor.nompro,".
				" rpc_proveedor.dirpro,rpc_proveedor.rifpro,rpc_proveedor.nitpro,".
				" rpc_proveedor.telpro,rpc_proveedor.nomreppro,rpc_proveedor.nomreppro,".
				" rpc_proveedor.faxpro,soc_ordencompra.codtipmod, soc_ordencompra.fechentdesde,".
				" soc_ordencompra.fechenthasta,soc_ordencompra.numanacot,soc_ordencompra.codusureg,".
				" (SELECT despai FROM sigesp_pais".
				" WHERE soc_ordencompra.codpai=sigesp_pais.codpai)AS despai,".
				" (SELECT desest FROM sigesp_estados".
				" WHERE soc_ordencompra.codpai=sigesp_estados.codpai".
				" AND soc_ordencompra.codest=sigesp_estados.codest)AS desest,".
				"	(SELECT denmun FROM sigesp_municipio".
				"  WHERE soc_ordencompra.codpai=sigesp_municipio.codpai".
				"  AND soc_ordencompra.codest=sigesp_municipio.codest".
				"  AND soc_ordencompra.codmun=sigesp_municipio.codmun) AS denmun,".
				" (SELECT denpar FROM sigesp_parroquia".
				"  WHERE soc_ordencompra.codpai=sigesp_parroquia.codpai".
				"  AND soc_ordencompra.codest=sigesp_parroquia.codest".
				"  AND soc_ordencompra.codmun=sigesp_parroquia.codmun".
				"  AND soc_ordencompra.codpar=sigesp_parroquia.codpar) AS denpar,".
				" soc_ordencompra.codmon, sigesp_moneda.denmon,soc_ordencompra.uniejeaso,".
				" (SELECT coalesce(denfuefin,'') FROM sigesp_fuentefinanciamiento".
				" WHERE sigesp_fuentefinanciamiento.codfuefin<>'--'".
				" AND sigesp_fuentefinanciamiento.codemp=soc_ordencompra.codemp".
				" AND sigesp_fuentefinanciamiento.codfuefin=soc_ordencompra.codfuefin) AS denfuefin,".
				" 	  coalesce(soc_analisicotizacion.numsolcot,'') as numsolcot,soc_sol_cotizacion.fecsol,coalesce(soc_cotizacion.numcot,'') as numcot,soc_cotizacion.feccot,coalesce(sss_usuarios.nomusu,'') as nomusu,coalesce(sss_usuarios.apeusu,'') as apeusu ".
				" FROM 	soc_ordencompra LEFT OUTER JOIN soc_analisicotizacion on soc_ordencompra.numanacot=soc_analisicotizacion.numanacot ".
				" LEFT OUTER JOIN soc_sol_cotizacion ON  soc_analisicotizacion.numsolcot = soc_sol_cotizacion.numsolcot ".
				" LEFT OUTER JOIN soc_cotizacion ON soc_sol_cotizacion.numsolcot=soc_cotizacion.numsolcot ".
				" LEFT OUTER JOIN sss_usuarios ON soc_ordencompra.codusuapr=sss_usuarios.codusu, ".
				" 	   spg_unidadadministrativa, rpc_proveedor,sigesp_moneda ".
				" WHERE 	soc_ordencompra.codemp='".$this->ls_codemp."'".
				" AND 	soc_ordencompra.numordcom='".$as_numordcom."'".
				" AND 	soc_ordencompra.estcondat='".$as_estcondat."'".
				" AND 	soc_ordencompra.codemp=rpc_proveedor.codemp".
				" AND 	soc_ordencompra.cod_pro=rpc_proveedor.cod_pro".
				" AND	soc_ordencompra.codemp=spg_unidadadministrativa.codemp".
				" AND	soc_ordencompra.coduniadm=spg_unidadadministrativa.coduniadm".
				" AND	soc_ordencompra.codmon=sigesp_moneda.codmon";        // print "$ls_sql <br>";die;
		$rs_data=$this->io_sql->select($ls_sql);         
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_orden_imprimir ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_select_ue($as_numordcom,$as_estcondat,&$lb_valido)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_ue
		//		   Access: private
		//	    Arguments: as_numordcom  --->  N�mero de la orden de compra
		//                 $as_estcondat --->  Tipo de Orden
		// 	      Returns: array con las unidades ejecutoras de las sep (si existen)
		//	  Description: Busca las unidades ejecutoras asociadas a una orden de compra con SEP
		//	   Creado Por: Victor Mendoza
		// Fecha Creaci�n: 10/11/2009 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=false;	
		
		$ls_sql="	SELECT coduniadm,(SELECT denuniadm FROM spg_unidadadministrativa WHERE codemp='$this->ls_codemp' 
							AND spg_unidadadministrativa.coduniadm=soc_enlace_sep.coduniadm) AS denuniadm
					FROM soc_enlace_sep
					WHERE codemp='$this->ls_codemp' AND 
							numordcom='$as_numordcom' AND
							estcondat = '$as_estcondat' 
					group by coduniadm,denuniadm		";	
		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_class_report.php;M�TODO->uf_select_ue ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			//---> si hay registros busca las denominaciones de las unidades ejecutoras
			$pos = 1;
			$la_data_ue[$pos]=array('codigoue'=>'<b>Unidad Ejecutora</b>');
			$pos ++;
			while($row=$this->io_sql->fetch_row($rs_data))//Se verifica si la solicitud es de bienes o de servicios
			{
				$la_data_ue[$pos]=array('codigoue'=>$row["coduniadm"].'    '.$row["denuniadm"]);
				$pos ++;
			}
						
			return $la_data_ue;
		}
		
		
		
		
		
	}

    function uf_select_orden_compra($as_numordcom,$as_estcondat,&$lb_valido)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_orden_compra
		//         Access: public
		//	    Arguments: as_numordcom    ---> Orden de Compra a imprimir
		//                 $as_estcondat  ---< tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca una orden de compra para imprimir
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql=" SELECT  soc_ordencompra.codemp,	soc_ordencompra.codusuapr, soc_ordencompra.numordcom,soc_ordencompra.estcondat,".
				" soc_ordencompra.cod_pro,soc_ordencompra.codfuefin,soc_ordencompra.fecordcom,".
				" soc_ordencompra.obscom,soc_ordencompra.obsordcom,soc_ordencompra.monsubtot,soc_ordencompra.estcom,soc_ordencompra.estapro,".
				" soc_ordencompra.monimp,soc_ordencompra.montot,soc_ordencompra.coduniadm,".
				" soc_ordencompra.forpagcom,soc_ordencompra.diaplacom,".
				" soc_ordencompra.montotdiv,0 as montotaux,soc_ordencompra.lugentnomdep,".
				" soc_ordencompra.lugentdir,soc_ordencompra.concom,soc_ordencompra.fecent,".
				" soc_ordencompra.estlugcom,soc_ordencompra.codmon,soc_ordencompra.codpai,".
				" soc_ordencompra.codest,soc_ordencompra.codmun,soc_ordencompra.codpar,".
				" soc_ordencompra.estsegcom,soc_ordencompra.porsegcom,soc_ordencompra.monsegcom,".
				" soc_ordencompra.monant,soc_ordencompra.tascamordcom,rpc_proveedor.nompro,".
				" rpc_proveedor.dirpro,rpc_proveedor.rifpro,rpc_proveedor.nitpro,".
				" rpc_proveedor.telpro,rpc_proveedor.nomreppro,rpc_proveedor.nomreppro,".
				" rpc_proveedor.faxpro,soc_ordencompra.codtipmod, soc_ordencompra.fechentdesde,".
				" soc_ordencompra.fechenthasta,soc_ordencompra.numanacot".
				" FROM 	soc_ordencompra, rpc_proveedor".
				" WHERE 	soc_ordencompra.codemp='".$this->ls_codemp."'".
				" AND 	soc_ordencompra.numordcom='".$as_numordcom."'".
				" AND 	soc_ordencompra.estcondat='".$as_estcondat."'".
				" AND 	soc_ordencompra.codemp=rpc_proveedor.codemp".
				" AND 	soc_ordencompra.cod_pro=rpc_proveedor.cod_pro"; //print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_orden_imprimir ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_orden_compra
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_soc_sep($as_codemp,$as_numordcom,$as_estcondat)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_soc_sep
		//         Access: public
		//	    Arguments: as_numordcom   // Orden de Compra a imprimir
		//                 $as_estcondat  // tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: Funcion que verifica si existe una SEP asociada a la orden de compra
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 18/09/2007									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		$this->ds_soc_sep = new class_datastore();
		$ls_sql="SELECT soc_enlace_sep.numordcom,soc_enlace_sep.numsol,sep_solicitud.coduniadm,".
				"       (SELECT spg_unidadadministrativa.denuniadm".
				"          FROM spg_unidadadministrativa".
				"         WHERE spg_unidadadministrativa.codemp=sep_solicitud.codemp".
				"           AND spg_unidadadministrativa.coduniadm=sep_solicitud.coduniadm) AS denuniadm".
				"  FROM soc_enlace_sep,sep_solicitud".
				" WHERE soc_enlace_sep.codemp='".$this->ls_codemp."'".
				"   AND soc_enlace_sep.numordcom='".$as_numordcom."'".
				"   AND estcondat='".$as_estcondat."'".
				"   AND sep_solicitud.codemp=soc_enlace_sep.codemp".
				"   AND sep_solicitud.numsol=soc_enlace_sep.numsol";
		$rs_data=$this->io_sql->select($ls_sql);
		$li_numrows=$this->io_sql->num_rows($rs_data);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_soc_sep ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_soc_sep->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end 	function uf_select_soc_sep
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_detalle_orden_imprimir($as_numordcom,$as_estcondat,&$lb_valido)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_detalle_orden_imprimir
		//         Access: public
		//	    Arguments: as_numordcom    ---> Orden de Compra a imprimir
		//                 $as_estcondat  ---< tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca los detalles de la  orden de compra para imprimir
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		switch ($as_estcondat)
		{
		   case 'B':
                    /*$ls_sql=" SELECT soc_dt_bienes.numordcom, soc_dt_bienes.codart as codartser, siv_articulo.denart as denartser, ".
                                        "        soc_dt_bienes.canart as cantartser, soc_dt_bienes.unidad, soc_dt_bienes.preuniart as preartser,   ".
                                        "	    soc_dt_bienes.monsubart as montsubartser, soc_dt_bienes.montotart as monttotartser, siv_articulo.spg_cuenta, ".
                                                "        soc_dt_bienes.orden, siv_articulo.codunimed, siv_unidadmedida.denunimed, ".
                                                "        soc_ordencompra.fecordcom, sigesp_cargos.porcar as porimp ".
                                                " FROM   soc_ordencompra , soc_dt_bienes , siv_articulo, siv_unidadmedida, sigesp_cargos, soc_solicitudcargos".
                                                " WHERE  soc_dt_bienes.codemp='".$this->ls_codemp."' AND ".
                                    "        soc_dt_bienes.numordcom='".$as_numordcom."' AND ".
                                                "        soc_dt_bienes.estcondat='".$as_estcondat."' AND ".
                                                "		soc_dt_bienes.codemp=soc_ordencompra.codemp AND ".
                                                "		soc_dt_bienes.codemp=siv_articulo.codemp AND ".
                                                "		siv_articulo.codemp=soc_ordencompra.codemp AND ".
                                                "		soc_dt_bienes.numordcom=soc_ordencompra.numordcom AND ".
                                                "		soc_dt_bienes.estcondat=soc_ordencompra.estcondat AND ".
                                                " 		soc_dt_bienes.codart=siv_articulo.codart  AND ".
                                        "	    siv_unidadmedida.codunimed=siv_articulo.codunimed AND".
                                                "		soc_solicitudcargos.codemp=soc_ordencompra.codemp AND ".
                                                "	    soc_solicitudcargos.numordcom=soc_ordencompra.numordcom AND ".
                                                "		soc_solicitudcargos.estcondat=soc_ordencompra.estcondat AND ".
                                                "		soc_solicitudcargos.codemp=sigesp_cargos.codemp AND ".
                                                "	    soc_solicitudcargos.codcar=sigesp_cargos.codcar ".
                                            "		ORDER BY soc_dt_bienes.orden ASC ";*/
              $ls_sql=" SELECT MAX(soc_dt_bienes.numordcom) as numordcom, MAX(soc_dt_bienes.codart) AS codartser, MAX(siv_articulo.denart) as denartser, ".
       				   "        SUM(soc_dt_bienes.canart) as cantartser, MAX(soc_dt_bienes.unidad) as unidad, MAX(soc_dt_bienes.preuniart) as preartser,   ".
       				   "	    SUM(soc_dt_bienes.monsubart) as montsubartser, SUM(soc_dt_bienes.montotart) as monttotartser, MAX(siv_articulo.spg_cuenta) AS spg_cuenta, ".
					   "        MAX(soc_dt_bienes.orden) as orden, MAX(siv_articulo.codunimed) as codunimed, MAX(siv_unidadmedida.denunimed) as denunimed, ".
					   "        MAX(soc_ordencompra.fecordcom) as fecordcom, ".
					   "        (SELECT coalesce(MAX(sigesp_cargos.porcar),0) ".
					   "         FROM sigesp_cargos, soc_dta_cargos ".
					   "		 WHERE MAX(soc_dt_bienes.codemp)=soc_dta_cargos.codemp ".
					   "           AND MAX(soc_dt_bienes.numordcom)=soc_dta_cargos.numordcom ".
					   "           AND MAX(soc_dt_bienes.estcondat)=soc_dta_cargos.estcondat ".
					   "           AND MAX(soc_dt_bienes.codart)=soc_dta_cargos.codart ".
					   "           AND soc_dta_cargos.codemp=sigesp_cargos.codemp ".
					   "           AND soc_dta_cargos.codcar=sigesp_cargos.codcar) AS porimp ".
					   " FROM   soc_ordencompra , soc_dt_bienes , siv_articulo, siv_unidadmedida ".
					   " WHERE  soc_dt_bienes.codemp='".$this->ls_codemp."' AND ".
           		       "        soc_dt_bienes.numordcom='".$as_numordcom."' AND ".
					   "        soc_dt_bienes.estcondat='".$as_estcondat."' AND ".
					   "		soc_dt_bienes.codemp=soc_ordencompra.codemp AND ".
					   "		soc_dt_bienes.codemp=siv_articulo.codemp AND ".
					   "		siv_articulo.codemp=soc_ordencompra.codemp AND ".
					   "		soc_dt_bienes.numordcom=soc_ordencompra.numordcom AND ".
					   "		soc_dt_bienes.estcondat=soc_ordencompra.estcondat AND ".
					   " 		soc_dt_bienes.codart=siv_articulo.codart  AND ".
       				   "	    siv_unidadmedida.codunimed=siv_articulo.codunimed ".
					   "  GROUP BY soc_dt_bienes.numordcom,soc_dt_bienes.codart".
				       "  ORDER BY MAX(soc_dt_bienes.orden) ASC "; //print $ls_sql."<br>";
		  break;

		  case 'S':
		     /*  $ls_sql=" SELECT soc_dt_servicio.numordcom, soc_dt_servicio.codser as codartser, soc_servicios.denser as denartser , ".
			   		   "        soc_dt_servicio.canser as cantartser, soc_dt_servicio.monuniser as preartser, ".
					   "        soc_dt_servicio.montotser as monttotartser, ".
					   "        soc_dt_servicio.monsubser as montsubartser, soc_servicios.spg_cuenta, soc_dt_servicio.orden, ".
					   "        soc_ordencompra.fecordcom, sigesp_cargos.porcar as porimp ".
					   " FROM   soc_ordencompra , soc_dt_servicio , soc_servicios, sigesp_cargos, soc_solicitudcargos ".
					   " WHERE  soc_dt_servicio.codemp='".$this->ls_codemp."' AND ".
					   "	    soc_dt_servicio.numordcom='".$as_numordcom."' AND ".
					   "	    soc_dt_servicio.estcondat='".$as_estcondat."' AND ".
					   "		soc_dt_servicio.codemp=soc_ordencompra.codemp AND ".
					   "		soc_dt_servicio.codemp=soc_servicios.codemp AND ".
					   "	    soc_servicios.codemp=soc_ordencompra.codemp AND ".
					   "	    soc_dt_servicio.numordcom=soc_ordencompra.numordcom AND ".
					   "		soc_dt_servicio.estcondat=soc_ordencompra.estcondat AND ".
					   "	    soc_dt_servicio.codser=soc_servicios.codser AND ".
					   "		soc_solicitudcargos.codemp=soc_ordencompra.codemp AND ".
					   "	    soc_solicitudcargos.numordcom=soc_ordencompra.numordcom AND ".
					   "		soc_solicitudcargos.estcondat=soc_ordencompra.estcondat AND ".
					   "		soc_solicitudcargos.codemp=sigesp_cargos.codemp AND ".
					   "	    soc_solicitudcargos.codcar=sigesp_cargos.codcar ".
					   " ORDER BY soc_dt_servicio.orden ASC ";*/
		       $ls_sql=" SELECT MAX(soc_dt_servicio.numordcom) as numordcom, MAX(soc_dt_servicio.codser) as codartser, MAX(soc_servicios.denser) as denartser , ".
			   		   "        SUM(soc_dt_servicio.canser) as cantartser, MAX(soc_dt_servicio.monuniser) as preartser, ".
					   "        SUM(soc_dt_servicio.montotser) as monttotartser, ".
					   "        SUM(soc_dt_servicio.monsubser) as montsubartser, MAX(soc_servicios.spg_cuenta) as spg_cuenta, MAX(soc_dt_servicio.orden) as orden, ".
					   "        MAX(soc_ordencompra.fecordcom) as fecordcom, MAX(siv_unidadmedida.denunimed) as denunimed, ".
					   "        (SELECT coalesce(MAX(sigesp_cargos.porcar),0) ".
					   "         FROM sigesp_cargos, soc_dts_cargos ".
					   "		 WHERE MAX(soc_dt_servicio.codemp)=soc_dts_cargos.codemp ".
					   "           AND MAX(soc_dt_servicio.numordcom)=soc_dts_cargos.numordcom ".
					   "           AND MAX(soc_dt_servicio.estcondat)=soc_dts_cargos.estcondat ".
					   "           AND MAX(soc_dt_servicio.codser)=soc_dts_cargos.codser ".
					   "           AND soc_dts_cargos.codemp=sigesp_cargos.codemp ".
					   "           AND soc_dts_cargos.codcar=sigesp_cargos.codcar) AS porimp ".
					   " FROM   soc_ordencompra ".
					   "  join  soc_dt_servicio on (soc_dt_servicio.codemp=soc_ordencompra.codemp".
                       "  and  soc_dt_servicio.numordcom=soc_ordencompra.numordcom ".
                       "  and  soc_dt_servicio.estcondat=soc_ordencompra.estcondat)".
                       "  join  soc_servicios  on ( soc_servicios.codemp=soc_ordencompra.codemp ".
                       "   and  soc_dt_servicio.codser=soc_servicios.codser )".
                       " left join  siv_unidadmedida on (soc_servicios.codunimed=siv_unidadmedida.codunimed) ".
				   	   " WHERE  soc_dt_servicio.codemp='".$this->ls_codemp."' AND ".
					   "	    soc_dt_servicio.numordcom='".$as_numordcom."' AND ".
					   "	    soc_dt_servicio.estcondat='".$as_estcondat."' ".
					   "  GROUP BY soc_dt_servicio.numordcom,soc_dt_servicio.codser".
					   "  ORDER BY MAX(soc_dt_servicio.orden) ASC ";// print $ls_sql;
		  break;
		}
		$rs_data=$this->io_sql->select($ls_sql);    // print $ls_sql;        
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_detalle_orden_imprimir ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;

		}
		return $rs_data;
	}// end function uf_select_detalle_orden_imprimir
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cuenta_gasto($as_numordcom,$as_estcondat,&$lb_valido)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_cuenta_gasto
		//         Access: public
		//	    Arguments: as_numordcom    ---> Orden de Compra a imprimir
		//                 $as_estcondat  ---< tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca las cuenats de gastos de la  orden de compra para imprimir
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql=" SELECT numordcom,codestpro1,codestpro2,codestpro3,".
		 	    "        codestpro4,codestpro5,spg_cuenta,monto     ".
				" FROM   soc_cuentagasto                            ".
				" WHERE  codemp='".$this->ls_codemp."'  AND         ".
				"        numordcom='".$as_numordcom."'  AND         ".
				"        estcondat='".$as_estcondat."'              ";
		$rs_data=$this->io_sql->select($ls_sql);  //print $ls_sql;
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_cuenta_gasto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_cuenta_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------------
	function uf_select_denominacionspg($as_cuenta,&$as_denominacion)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sep_select_denominacion_unidad_medida
		//		   Access: private
		//	    Arguments: as_cuenta //codigo de la cuenta
		//	   			   as_denominacion // denominacion de la cuenta
		//    Description: Function que devuelve la denominacion de la cuenta presupuestaria
		//	   Creado Por: Ing. Yozelin Barragan.
		// Fecha Creaci�n: 10/04/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=false;
		 $ls_sql=" SELECT denominacion ".
				 " FROM   spg_cuentas ".
				 " WHERE  codemp='".$this->ls_codemp."'  AND  spg_cuenta='".$as_cuenta."' ";
		 $rs=$this->io_sql->select($ls_sql);
		 if ($rs===false)
		 {
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_denominacionspg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		 }
		 else
		 {
			 if($row=$this->io_sql->fetch_row($rs))
			 {
				$as_denominacion=$row["denominacion"];
				$lb_valido=true;
			 }
			$this->io_sql->free_result($rs);
		 }
		 return $lb_valido;
	}//fin 	uf_select_denominacionspg
   //---------------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------------------
		function uf_select_items($as_numanacot,$as_tipsolcot,&$la_items)
		{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_select_items
			//		   Access: public
			//		  return : arreglo que contiene los items que participaron en un determinado analisis
			//	  Description: Metodo que  devuelve los items que participaron en un determinado analisis
			//	   Creado Por: Ing. Laura Cabr�
			// 			Fecha: 17/07/2007								Fecha �ltima Modificaci�n :
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$la_items=array();
			$lb_valido=false;
			if($as_tipsolcot=="B")
			{
				$ls_sql="SELECT d.codart as codigo, a.denart as denominacion, p.nompro, dt.canart as cantidad, dt.preuniart as precio, dt.moniva,dt.montotart as monto,
						d.obsanacot, d.numcot, d.cod_pro
						FROM soc_dtac_bienes d,siv_articulo a, rpc_proveedor p,soc_dtcot_bienes dt
						WHERE
						d.codemp='$this->ls_codemp' AND d.numanacot='$as_numanacot' AND d.codemp=a.codemp AND a.codemp=p.codemp AND p.codemp=dt.codemp AND
						d.codart=a.codart AND d.cod_pro=p.cod_pro AND d.numcot=dt.numcot AND d.cod_pro=dt.cod_pro AND d.codart=dt.codart";
			}
			else
			{
					$ls_sql="SELECT d.codser as codigo, a.denser as denominacion, p.nompro, dt.canser as cantidad, dt.monuniser as precio, dt.moniva,dt.montotser as monto,
						d.obsanacot, d.numcot, d.cod_pro
						FROM soc_dtac_servicios d,soc_servicios a, rpc_proveedor p,soc_dtcot_servicio dt
						WHERE
						d.codemp='$this->ls_codemp' AND d.numanacot='$as_numanacot' AND d.codemp=a.codemp AND a.codemp=p.codemp AND p.codemp=dt.codemp AND
						d.codser=a.codser AND d.cod_pro=p.cod_pro AND d.numcot=dt.numcot AND d.cod_pro=dt.cod_pro AND d.codser=dt.codser";
			}

			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$io_mensajes->message("ERROR->uf_select_items".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				$li_i=0;
				while($row=$this->io_sql->fetch_row($rs_data))
				{
					$li_i++;
					$la_items[$li_i]=$row;
					$lb_valido=true;
				}
			}
			return $lb_valido;
		}
	//---------------------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------------------
		function uf_cargar_cotizaciones_esp($as_numanacot, &$aa_proveedores)
		{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_cargar_cotizaciones
			//		   Access: public
			//     Parameters: $as_numanacot--->numero del analisis de cotizacion
			//		  return : arreglo que contiene las cotizaciones que participaron en un determinado analisis
			//	  Description: Metodo que  devuelve las cotizaciones que participaron en un determinado analisis
			//	   Creado Por: Ing. Laura Cabr�
			// 			  Fecha: 18/06/2007								Fecha �ltima Modificaci�n :
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$aa_proveedores=array();
			$lb_valido=false; 
            if ($as_tipsolcot=='B')
            {
                $ls_sql= "SELECT soc_analisicotizacion.numsolcot, soc_cotxanalisis.numcot, soc_dtcot_bienes.montotart,soc_cotizacion.estasitec,soc_cotizacion.estesp,soc_cotizacion.garanacot,".
                           " soc_dtcot_bienes.moniva,soc_cotizacion.poriva, soc_cotizacion.feccot, rpc_proveedor.nompro, siv_articulo.denart as denominacion,".
                           " soc_dtac_bienes.codart as codigo, soc_dtac_bienes.numcot,soc_dtcot_bienes.canart, siv_unidadmedida.denunimed,".
                           " soc_dtcot_bienes.preuniart, soc_dtcot_bienes.moniva, soc_dtcot_bienes.monsubart,rpc_proveedor.cod_pro,rpc_proveedor.rifpro,rpc_proveedor.dirpro,soc_cotizacion.diaentcom, ".
                           " case  soc_cotizacion.forpagcom when 'A50E50' then '50% AL APROBAR 50% A LA ENTREGA' else  soc_cotizacion.forpagcom end as forpagcom ".
                           " FROM   soc_analisicotizacion, soc_cotizacion, rpc_proveedor, soc_cotxanalisis, siv_articulo, soc_dtac_bienes, soc_dtcot_bienes, siv_unidadmedida ".
                           " WHERE  soc_analisicotizacion.codemp   = '$this->ls_codemp'".   
                           " AND    soc_analisicotizacion.numanacot= '$as_numanacot'".
                           " AND    soc_analisicotizacion.codemp   = soc_cotizacion.codemp".
                           " AND    soc_cotxanalisis.cod_pro       = soc_cotizacion.cod_pro".
                           " AND    soc_cotxanalisis.numcot        = soc_cotizacion.numcot".
                           " AND    soc_cotxanalisis.cod_pro       = rpc_proveedor.cod_pro".
                           " AND    soc_analisicotizacion.codemp   = rpc_proveedor.codemp".
                           " AND    soc_analisicotizacion.codemp   = soc_cotxanalisis.codemp".
                           " AND    soc_analisicotizacion.numanacot= soc_cotxanalisis.numanacot".
                           " AND    soc_analisicotizacion.numanacot= soc_dtac_bienes.numanacot".
                           " AND    soc_dtac_bienes.codart         = siv_articulo.codart".
                           " AND    soc_dtcot_bienes.codart        = soc_dtac_bienes.codart".
                           " AND    soc_dtcot_bienes.numcot        = soc_cotizacion.numcot".

                           " AND    siv_unidadmedida.codunimed     = siv_articulo.codunimed
                           ORDER BY soc_cotizacion.cod_pro;";                
            }
            else
            {
                $ls_sql= "SELECT    soc_analisicotizacion.numsolcot, soc_cotxanalisis.numcot, soc_dtcot_servicio.montotser as montotart,soc_cotizacion.estasitec,
                                    soc_cotizacion.estesp,soc_cotizacion.garanacot, soc_dtcot_servicio.moniva,soc_cotizacion.poriva, soc_cotizacion.feccot, 
                                    rpc_proveedor.nompro, soc_servicios.denser as denominacion, soc_dtac_servicios.codser as codigo, soc_dtac_servicios.numcot,
                                    soc_dtcot_servicio.canser as canart, 'Servicio' as denunimed, soc_dtcot_servicio.monuniser as preuniart, soc_dtcot_servicio.moniva, 
                                    soc_dtcot_servicio.monsubser as monsubart,rpc_proveedor.cod_pro,rpc_proveedor.rifpro,rpc_proveedor.dirpro,soc_cotizacion.diaentcom, 
                                    case soc_cotizacion.forpagcom when 'A50E50' then '50% AL APROBAR 50% A LA ENTREGA' else soc_cotizacion.forpagcom end as forpagcom 
                            FROM    soc_analisicotizacion, soc_cotizacion, rpc_proveedor, soc_cotxanalisis, 
                                    soc_servicios,soc_dtac_servicios,soc_dtcot_servicio     
                            WHERE   soc_analisicotizacion.codemp = '$this->ls_codemp' 
                            AND     soc_analisicotizacion.numanacot= '$as_numanacot' 
                            AND     soc_analisicotizacion.codemp = soc_cotizacion.codemp 
                            AND     soc_cotxanalisis.cod_pro = soc_cotizacion.cod_pro 
                            AND     soc_cotxanalisis.numcot = soc_cotizacion.numcot 
                            AND     soc_cotxanalisis.cod_pro = rpc_proveedor.cod_pro 
                            AND     soc_analisicotizacion.codemp = rpc_proveedor.codemp 
                            AND     soc_analisicotizacion.codemp = soc_cotxanalisis.codemp 
                            AND     soc_analisicotizacion.numanacot= soc_cotxanalisis.numanacot 
                            AND     soc_analisicotizacion.numanacot= soc_dtac_servicios.numanacot 
                            AND     soc_servicios.codemp=soc_dtac_servicios.codemp  
                            AND     soc_servicios.codser = soc_dtac_servicios.codser  
                            AND     soc_dtac_servicios.codemp = soc_dtcot_servicio.codemp 
                            AND     soc_dtac_servicios.numcot = soc_dtcot_servicio.numcot
                            ORDER BY soc_cotizacion.cod_pro;";
                       
            }
            
			//print $ls_sql;
				$rs_data=$this->io_sql->select($ls_sql);//print_r($rs_data);
				if($rs_data===false)
				{
					$this->io_mensajes->message("ERROR->uf_cargar_cotizaciones_esp".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					print ($this->io_sql->message);
					$lb_valido=false;
				}
				else
				{
					$li_i=0;
					while($row=$this->io_sql->fetch_row($rs_data))//Se verifica si la solicitud es de bienes o de servicios
					{
						$li_i++;
						$aa_proveedores[$li_i]=$row;
						$lb_valido=true;
					}
				}
			return $lb_valido;
		}//fin de uf_cargar_cotizaciones
	//---------------------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------------------
        function uf_cargar_cotizaciones_v2($as_numanacot, &$aa_proveedores,$as_tipsolcot,&$la_proveedor,&$li_cotizaciones,&$ls_codpro1,&$ls_codpro2,&$ls_codpro3)
        {
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //         Function: uf_cargar_cotizaciones_v2
            //           Access: public
            //     Parameters: $as_numanacot--->numero del analisis de cotizacion
            //          return : arreglo que contiene las cotizaciones que participaron en un determinado analisis
            //      Description: Modificacion a la consulta que  devuelve las cotizaciones que participaron en un determinado analisis
            //
            //       Creado Por: Victor Mendoza
            //               Fecha: 18/08/2009                                Fecha �ltima Modificaci�n :
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            $lb_valido=false;
            $la_proveedor=array();
            
            $ls_sql_sc =   "select a.numanacot,b.cod_pro,b.numcot,
                                   c.feccot, c.obscot, c.monsubtot, c.monimpcot, c.mondes, c.montotcot, c.diaentcom,
                                   c.codusu, c.estcot, c.forpagcom, c.poriva, c.estinciva, c.tipcot, c.diavalofe,
                                   c.estasitec, c.estesp, c.garanacot,d.nompro,d.dirpro,d.rifpro
                            from soc_analisicotizacion a,soc_cotxanalisis b,soc_cotizacion c,rpc_proveedor d
                            where a.numanacot='$as_numanacot' and
                              a.codemp='$this->ls_codemp' and
                              a.codemp=b.codemp and
                              b.numanacot=a.numanacot and
                              a.codemp=c.codemp and
                              b.cod_pro=c.cod_pro and
                              b.numcot = c.numcot and
                              c.cod_pro = d.cod_pro
                            order by b.cod_pro ";
                                       
            $rs_data_sc=$this->io_sql->select($ls_sql_sc);
            
            if($rs_data_sc===false)
            {
                $this->io_mensajes->message("ERROR->uf_cargar_cotizaciones_v2 cargando cotizaciones ".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
                print ($this->io_sql->message);
                $lb_valido=false;
            }
            else
            {
                $li_i=0;
                while($row=$this->io_sql->fetch_row($rs_data_sc))//Se verifica si la solicitud es de bienes o de servicios
                {
                    $li_i++;
                    $la_proveedor[$li_i]=$row;
                    $lb_valido=true;
                }
            }
            //var_dump($la_proveedor);   
            if ($li_i>3)
            {
                $li_i=3;
            }
            
            $li_cotizaciones = $li_i;
            
            $ls_codpro1 = (!empty($la_proveedor[1]["cod_pro"]) ? $la_proveedor[1]["cod_pro"] : ""); 
            $ls_codpro2 = (!empty($la_proveedor[2]["cod_pro"]) ? $la_proveedor[2]["cod_pro"] : ""); 
            $ls_codpro3 = (!empty($la_proveedor[3]["cod_pro"]) ? $la_proveedor[3]["cod_pro"] : ""); 
            
            
            $aa_proveedores=array();
            $lb_valido=false;
            if ($as_tipsolcot=='B')
            {
            $ls_sql=<<<EOD
                select cur1.*,cur2.*,cur3.*
                from
                (SELECT
                  soc_analisicotizacion.numsolcot as numsolcot,
                  soc_analisicotizacion.estasitec as estec2_1 ,
                  soc_analisicotizacion.estesp as estep2_1,
                  soc_analisicotizacion.garanacot  as garcot2_1,
                  soc_cotxanalisis.numcot as numcot_1,
                  soc_cotizacion.poriva as poriva_1,
                  soc_cotizacion.feccot as  feccot_1,
                  soc_cotizacion.forpagcom as forpagcom_1,
                  soc_cotizacion.diaentcom as diaentcom_1,
                  soc_cotizacion.garanacot as garanacot_1,
                  soc_cotizacion.estasitec as estasitec_1,
                  soc_cotizacion.estesp as estesp_1,
                  soc_dtac_bienes.codart as codigo,
                  siv_articulo.denart as denominacion,
                  siv_unidadmedida.denunimed,
                  rpc_proveedor.nompro as nompro_1,
                  rpc_proveedor.cod_pro as cod_pro_1,
                  rpc_proveedor.rifpro as rifpro_1,
                  rpc_proveedor.dirpro as dirpro_1,
                  COALESCE(soc_dtcot_bienes.montotart,0) as montotart1,
                  COALESCE(soc_dtcot_bienes.moniva,0) as moniva1,
                  COALESCE(soc_dtcot_bienes.canart,0) as canart1,
                  COALESCE(soc_dtcot_bienes.preuniart,0) as preuniart1,
                  COALESCE(soc_dtcot_bienes.monsubart,0) as monsubart1
                FROM soc_analisicotizacion, soc_cotizacion,rpc_proveedor,soc_cotxanalisis,siv_articulo ,soc_dtac_bienes,
                       siv_unidadmedida,soc_dtcot_bienes
                WHERE soc_analisicotizacion.codemp = '$this->ls_codemp'             AND
                    soc_analisicotizacion.numanacot= '$as_numanacot'                AND
                    soc_analisicotizacion.codemp = soc_cotizacion.codemp            AND
                    soc_cotxanalisis.cod_pro = soc_cotizacion.cod_pro               AND
                    soc_cotxanalisis.numcot = soc_cotizacion.numcot                 AND
                    soc_cotxanalisis.cod_pro = '$ls_codpro1'                        and
                    soc_cotxanalisis.cod_pro = rpc_proveedor.cod_pro                AND
                    soc_analisicotizacion.codemp = rpc_proveedor.codemp             AND
                    soc_analisicotizacion.codemp = soc_cotxanalisis.codemp          AND
                    soc_analisicotizacion.numanacot= soc_cotxanalisis.numanacot     AND
                    soc_analisicotizacion.numanacot= soc_dtac_bienes.numanacot      AND
                    soc_dtac_bienes.codart = siv_articulo.codart                    AND
                    siv_unidadmedida.codunimed = siv_articulo.codunimed             and
                     soc_dtcot_bienes.codart = soc_dtac_bienes.codart               AND
                      soc_dtcot_bienes.numcot = soc_cotizacion.numcot               AND
                      soc_dtcot_bienes.cod_pro = rpc_proveedor.cod_pro) as cur1
                left outer join
                (SELECT
                  soc_analisicotizacion.numsolcot as numsolcot,
                  soc_analisicotizacion.estasitec as estec2_2,
                  soc_analisicotizacion.estesp as estep2_2 ,
                  soc_analisicotizacion.garanacot  as garcot2_2,
                  soc_cotxanalisis.numcot as numcot_2,
                  soc_cotizacion.poriva as poriva_2,
                  soc_cotizacion.feccot as feccot_2,
                  soc_cotizacion.forpagcom as forpagcom_2,
                  soc_cotizacion.diaentcom as diaentcom_2,
                  soc_cotizacion.garanacot as garanacot_2,
                  soc_cotizacion.estasitec as estasitec_2,
                  soc_cotizacion.estesp as estesp_2,
                  soc_dtac_bienes.codart as codigo,
                  siv_articulo.denart as denominacion,
                  siv_unidadmedida.denunimed,
                  rpc_proveedor.nompro as nompro_2,
                  rpc_proveedor.cod_pro as cod_pro_2,
                  rpc_proveedor.rifpro as rifpro_2,
                  rpc_proveedor.dirpro as dirpro_2,
                  COALESCE(soc_dtcot_bienes.montotart,0) as montotart2,
                  COALESCE(soc_dtcot_bienes.moniva,0) as moniva2,
                  COALESCE(soc_dtcot_bienes.canart,0) as canart2,
                  COALESCE(soc_dtcot_bienes.preuniart,0) as preuniart2,
                  COALESCE(soc_dtcot_bienes.monsubart,0) as monsubart2
                FROM soc_analisicotizacion, soc_cotizacion,rpc_proveedor,soc_cotxanalisis,siv_articulo ,soc_dtac_bienes,
                       siv_unidadmedida,soc_dtcot_bienes
                WHERE soc_analisicotizacion.codemp = '$this->ls_codemp'             AND
                    soc_analisicotizacion.numanacot= '$as_numanacot'                AND
                    soc_analisicotizacion.codemp = soc_cotizacion.codemp            AND
                    soc_cotxanalisis.cod_pro = soc_cotizacion.cod_pro               AND
                    soc_cotxanalisis.numcot = soc_cotizacion.numcot                 AND
                    soc_cotxanalisis.cod_pro = '$ls_codpro2'                        and
                    soc_cotxanalisis.cod_pro = rpc_proveedor.cod_pro                AND
                    soc_analisicotizacion.codemp = rpc_proveedor.codemp             AND
                    soc_analisicotizacion.codemp = soc_cotxanalisis.codemp          AND
                    soc_analisicotizacion.numanacot= soc_cotxanalisis.numanacot     AND
                    soc_analisicotizacion.numanacot= soc_dtac_bienes.numanacot      AND
                    soc_dtac_bienes.codart = siv_articulo.codart                    AND
                    siv_unidadmedida.codunimed = siv_articulo.codunimed             and
                     soc_dtcot_bienes.codart = soc_dtac_bienes.codart               AND
                      soc_dtcot_bienes.numcot = soc_cotizacion.numcot               AND
                      soc_dtcot_bienes.cod_pro = rpc_proveedor.cod_pro) as cur2

                on cur1.numsolcot=cur2.numsolcot and
                  cur1.codigo=cur2.codigo
                 left outer join
                (SELECT
                  soc_analisicotizacion.numsolcot as numsolcot,
                  soc_analisicotizacion.estasitec as estec2_3,
                  soc_analisicotizacion.estesp as estep2_3 ,
                  soc_analisicotizacion.garanacot  as garcot2_3,
                  soc_cotxanalisis.numcot as numcot_3,
                  soc_cotizacion.poriva as poriva_3,
                  soc_cotizacion.feccot as feccot_3,
                  soc_cotizacion.forpagcom as forpagcom_3,
                  soc_cotizacion.diaentcom as diaentcom_3,
                  soc_cotizacion.garanacot as garanacot_3,
                  soc_cotizacion.estasitec as estasitec_3,
                  soc_cotizacion.estesp as estesp_3,
                  soc_dtac_bienes.codart as codigo,
                  siv_articulo.denart as denominacion,
                  siv_unidadmedida.denunimed as denunimed,
                  rpc_proveedor.nompro as nompro_3,
                  rpc_proveedor.cod_pro as cod_pro_3,
                  rpc_proveedor.rifpro as rifpro_3,
                  rpc_proveedor.dirpro as dirpro_3,
                  COALESCE(soc_dtcot_bienes.montotart,0) as montotart3,
                  COALESCE(soc_dtcot_bienes.moniva,0) as moniva3,
                  COALESCE(soc_dtcot_bienes.canart,0) as canart3,
                  COALESCE(soc_dtcot_bienes.preuniart,0) as preuniart3,
                  COALESCE(soc_dtcot_bienes.monsubart,0) as monsubart3
                FROM soc_analisicotizacion, soc_cotizacion,rpc_proveedor,soc_cotxanalisis,siv_articulo ,soc_dtac_bienes,
                       siv_unidadmedida,soc_dtcot_bienes
                WHERE soc_analisicotizacion.codemp = '$this->ls_codemp'             AND
                    soc_analisicotizacion.numanacot= '$as_numanacot'                AND
                    soc_analisicotizacion.codemp = soc_cotizacion.codemp            AND
                    soc_cotxanalisis.cod_pro = soc_cotizacion.cod_pro               AND
                    soc_cotxanalisis.numcot = soc_cotizacion.numcot                 AND
                    soc_cotxanalisis.cod_pro = '$ls_codpro3'                        AND
                    soc_cotxanalisis.cod_pro = rpc_proveedor.cod_pro                AND
                    soc_analisicotizacion.codemp = rpc_proveedor.codemp             AND
                    soc_analisicotizacion.codemp = soc_cotxanalisis.codemp          AND
                    soc_analisicotizacion.numanacot= soc_cotxanalisis.numanacot     AND
                    soc_analisicotizacion.numanacot= soc_dtac_bienes.numanacot      AND
                    soc_dtac_bienes.codart = siv_articulo.codart                    AND
                    siv_unidadmedida.codunimed = siv_articulo.codunimed             AND
                     soc_dtcot_bienes.codart = soc_dtac_bienes.codart               AND
                      soc_dtcot_bienes.numcot = soc_cotizacion.numcot               AND
                      soc_dtcot_bienes.cod_pro = rpc_proveedor.cod_pro) as cur3
                on
                  cur1.numsolcot=cur3.numsolcot and
                  cur1.codigo=cur3.codigo
EOD;
                //la linea anterior debe quedar en la columna 1 !!! OBLIGATORIO !!!
                }
                else
                {
                $ls_sql=<<<EOD
                SELECT cur1.*,cur2.*,cur3.*
                FROM   (SELECT
                  soc_analisicotizacion.numsolcot as numsolcot,
                  soc_analisicotizacion.estasitec as estec2_1 ,
                  soc_analisicotizacion.estesp as estep2_1,
                  soc_analisicotizacion.garanacot  as garcot2_1,
                  soc_cotxanalisis.numcot as numcot_1,
                  soc_cotizacion.poriva as poriva_1,
                  soc_cotizacion.feccot as  feccot_1,
                  soc_cotizacion.forpagcom as forpagcom_1,
                  soc_cotizacion.diaentcom as diaentcom_1,
                  soc_cotizacion.garanacot as garanacot_1,
                  soc_cotizacion.estasitec as estasitec_1,
                  soc_cotizacion.estesp as estesp_1,
                  soc_dtac_servicios.codser as codigo,
                  soc_servicios.denser as denominacion,
                  'Servicio' as denunimed,
                  rpc_proveedor.nompro as nompro_1,
                  rpc_proveedor.cod_pro as cod_pro_1,
                  rpc_proveedor.rifpro as rifpro_1,
                  rpc_proveedor.dirpro as dirpro_1,
                  COALESCE(soc_dtcot_servicio.montotser,0) as montotart1,
                  COALESCE(soc_dtcot_servicio.moniva,0) as moniva1,
                  COALESCE(soc_dtcot_servicio.canser,0) as canart1,
                  COALESCE(soc_dtcot_servicio.monuniser,0) as preuniart1,
                  COALESCE(soc_dtcot_servicio.monsubser,0) as monsubart1
                FROM soc_analisicotizacion, soc_cotizacion,rpc_proveedor,soc_cotxanalisis,soc_servicios,soc_dtac_servicios,
                       soc_dtcot_servicio,siv_unidadmedida
                WHERE soc_analisicotizacion.codemp = '$this->ls_codemp'             AND
                    soc_analisicotizacion.numanacot= '$as_numanacot'                AND
                    soc_analisicotizacion.codemp = soc_cotizacion.codemp            AND
                    soc_cotxanalisis.cod_pro = soc_cotizacion.cod_pro               AND
                    soc_cotxanalisis.numcot = soc_cotizacion.numcot                 AND
                    soc_cotxanalisis.cod_pro = '$ls_codpro1'                        and
                    soc_cotxanalisis.cod_pro = rpc_proveedor.cod_pro                AND
                    soc_analisicotizacion.codemp = rpc_proveedor.codemp             AND
                    soc_analisicotizacion.codemp = soc_cotxanalisis.codemp          AND
                    soc_analisicotizacion.numanacot= soc_cotxanalisis.numanacot     AND
                    soc_analisicotizacion.numanacot= soc_dtac_servicios.numanacot   AND
                    soc_dtac_servicios.codser = soc_servicios.codser                and
                    soc_dtac_servicios.codser =soc_servicios.codser                 AND
                    siv_unidadmedida.codunimed = soc_servicios.codunimed            and
                    soc_dtcot_servicio.codser = soc_dtac_servicios.codser           AND
                    soc_dtcot_servicio.numcot = soc_cotizacion.numcot               AND
                    soc_dtcot_servicio.cod_pro = rpc_proveedor.cod_pro) as cur1
                  LEFT OUTER JOIN
                  (SELECT
                    soc_analisicotizacion.numsolcot as numsolcot,
                    soc_analisicotizacion.estasitec as estec2_2 ,
                    soc_analisicotizacion.estesp as estep2_2,
                    soc_analisicotizacion.garanacot  as garcot2_2,
                    soc_cotxanalisis.numcot as numcot_2,
                    soc_cotizacion.poriva as poriva_2,
                    soc_cotizacion.feccot as  feccot_2,
                    soc_cotizacion.forpagcom as forpagcom_2,
                    soc_cotizacion.diaentcom as diaentcom_2,
                    soc_cotizacion.garanacot as garanacot_2,
                    soc_cotizacion.estasitec as estasitec_2,
                    soc_cotizacion.estesp as estesp_2,
                    soc_dtac_servicios.codser as codigo,
                    soc_servicios.denser as denominacion,
                    'Servicio' as denunimed,
                    rpc_proveedor.nompro as nompro_2,
                    rpc_proveedor.cod_pro as cod_pro_2,
                    rpc_proveedor.rifpro as rifpro_2,
                    rpc_proveedor.dirpro as dirpro_2,
                    COALESCE(soc_dtcot_servicio.montotser,0) as montotart2,
                    COALESCE(soc_dtcot_servicio.moniva,0) as moniva2,
                    COALESCE(soc_dtcot_servicio.canser,0) as canart2,
                    COALESCE(soc_dtcot_servicio.monuniser,0) as preuniart2,
                    COALESCE(soc_dtcot_servicio.monsubser,0) as monsubart2
                FROM soc_analisicotizacion, soc_cotizacion,rpc_proveedor,soc_cotxanalisis,soc_servicios,soc_dtac_servicios,
                    soc_dtcot_servicio,siv_unidadmedida
                WHERE soc_analisicotizacion.codemp = '$this->ls_codemp'             AND
                    soc_analisicotizacion.numanacot= '$as_numanacot'                AND
                    soc_analisicotizacion.codemp = soc_cotizacion.codemp            AND
                    soc_cotxanalisis.cod_pro = soc_cotizacion.cod_pro               AND
                    soc_cotxanalisis.numcot = soc_cotizacion.numcot                 AND
                    soc_cotxanalisis.cod_pro = '$ls_codpro2'                        and
                    soc_cotxanalisis.cod_pro = rpc_proveedor.cod_pro                AND
                    soc_analisicotizacion.codemp = rpc_proveedor.codemp             AND
                    soc_analisicotizacion.codemp = soc_cotxanalisis.codemp          AND
                    soc_analisicotizacion.numanacot= soc_cotxanalisis.numanacot     AND
                    soc_analisicotizacion.numanacot= soc_dtac_servicios.numanacot   AND
                    soc_dtac_servicios.codser = soc_servicios.codser                and
                    soc_dtac_servicios.codser =soc_servicios.codser                 AND
                    siv_unidadmedida.codunimed = soc_servicios.codunimed            and
                    soc_dtcot_servicio.codser = soc_dtac_servicios.codser           AND
                    soc_dtcot_servicio.numcot = soc_cotizacion.numcot               AND
                    soc_dtcot_servicio.cod_pro = rpc_proveedor.cod_pro ) as cur2
                 ON cur1.numsolcot=cur2.numsolcot and
                    cur1.codigo=cur2.codigo
                 LEFT OUTER JOIN
                 (SELECT
                    soc_analisicotizacion.numsolcot as numsolcot,
                    soc_analisicotizacion.estasitec as estec2_3 ,
                    soc_analisicotizacion.estesp as estep2_3,
                    soc_analisicotizacion.garanacot  as garcot2_3,
                    soc_cotxanalisis.numcot as numcot_3,
                    soc_cotizacion.poriva as poriva_3,
                    soc_cotizacion.feccot as  feccot_3,
                    soc_cotizacion.forpagcom as forpagcom_3,
                    soc_cotizacion.diaentcom as diaentcom_3,
                    soc_cotizacion.garanacot as garanacot_3,
                    soc_cotizacion.estasitec as estasitec_3,
                    soc_cotizacion.estesp as estesp_3,
                    soc_dtac_servicios.codser as codigo,
                    soc_servicios.denser as denominacion,
                    'Servicio' as denunimed,
                    rpc_proveedor.nompro as nompro_3,
                    rpc_proveedor.cod_pro as cod_pro_3,
                    rpc_proveedor.rifpro as rifpro_3,
                    rpc_proveedor.dirpro as dirpro_3,
                    COALESCE(soc_dtcot_servicio.montotser,0) as montotart3,
                    COALESCE(soc_dtcot_servicio.moniva,0) as moniva3,
                    COALESCE(soc_dtcot_servicio.canser,0) as canart3,
                    COALESCE(soc_dtcot_servicio.monuniser,0) as preuniart3,
                    COALESCE(soc_dtcot_servicio.monsubser,0) as monsubart3
                FROM soc_analisicotizacion, soc_cotizacion,rpc_proveedor,soc_cotxanalisis,soc_servicios,soc_dtac_servicios,
                    soc_dtcot_servicio,siv_unidadmedida
                WHERE soc_analisicotizacion.codemp = '$this->ls_codemp'             AND
                    soc_analisicotizacion.numanacot= '$as_numanacot'                AND
                    soc_analisicotizacion.codemp = soc_cotizacion.codemp            AND
                    soc_cotxanalisis.cod_pro = soc_cotizacion.cod_pro               AND
                    soc_cotxanalisis.numcot = soc_cotizacion.numcot                 AND
                    soc_cotxanalisis.cod_pro = '$ls_codpro3'                        and
                    soc_cotxanalisis.cod_pro = rpc_proveedor.cod_pro                AND
                    soc_analisicotizacion.codemp = rpc_proveedor.codemp             AND
                    soc_analisicotizacion.codemp = soc_cotxanalisis.codemp          AND
                    soc_analisicotizacion.numanacot= soc_cotxanalisis.numanacot     AND
                    soc_analisicotizacion.numanacot= soc_dtac_servicios.numanacot   AND
                    soc_dtac_servicios.codser = soc_servicios.codser                and
                    soc_dtac_servicios.codser =soc_servicios.codser                 AND
                    siv_unidadmedida.codunimed = soc_servicios.codunimed            and
                    soc_dtcot_servicio.codser = soc_dtac_servicios.codser           AND
                    soc_dtcot_servicio.numcot = soc_cotizacion.numcot               AND
                    soc_dtcot_servicio.cod_pro = rpc_proveedor.cod_pro ) as cur3
                 ON cur1.numsolcot=cur3.numsolcot and
                    cur1.codigo=cur3.codigo                
EOD;
                 //la linea anterior debe quedar en la columna 1 !!! OBLIGATORIO !!!   
                }
                $rs_data=$this->io_sql->select($ls_sql);
                if($rs_data===false)
                {
                    $this->io_mensajes->message("ERROR->uf_cargar_cotizaciones_esp".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
                    print ($this->io_sql->message);
                    $lb_valido=false;
                }
                else
                {
                    $li_i=0;
                    while($row=$this->io_sql->fetch_row($rs_data))//Se verifica si la solicitud es de bienes o de servicios
                    {
                        $li_i++;
                        $aa_proveedores[$li_i]=$row;
                        $lb_valido=true;
                    }
                }
            return $lb_valido;
        }//fin de uf_cargar_cotizaciones_v2
    
	
    //---------------------------------------------------------------------------------------------------------------------------------------
		function uf_count_cotizaciones($as_numanacot, &$aa_proveedores,$as_tipsolcot)
		{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_cargar_cotizaciones
			//		   Access: public
			//     Parameters: $as_numanacot--->numero del analisis de cotizacion
			//		  return : arreglo que contiene las cotizaciones que participaron en un determinado analisis
			//	  Description: Metodo que  devuelve las cotizaciones que participaron en un determinado analisis
			//	   Creado Por: Ing. Laura Cabr�
			// 			  Fecha: 18/06/2007								Fecha �ltima Modificaci�n :
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$aa_proveedores=array();
			$lb_valido=false;
            if ($as_tipsolcot=='B')
            {
                $ls_sql= "SELECT count(soc_cotxanalisis.numcot)".
                         "    FROM soc_analisicotizacion, soc_cotizacion, rpc_proveedor, soc_cotxanalisis, siv_articulo,".
                         "         soc_dtac_bienes, soc_dtcot_bienes, siv_unidadmedida".
                         "    WHERE soc_analisicotizacion.codemp = '$this->ls_codemp'".
                         "    AND soc_analisicotizacion.numanacot= '$as_numanacot'".
                         "    AND soc_analisicotizacion.codemp = soc_cotizacion.codemp".
                         "    AND soc_cotxanalisis.cod_pro = soc_cotizacion.cod_pro".
                         "    AND soc_cotxanalisis.numcot = soc_cotizacion.numcot".
                         "    AND soc_cotxanalisis.cod_pro = rpc_proveedor.cod_pro".
                         "    AND soc_analisicotizacion.codemp = rpc_proveedor.codemp".
                         "    AND soc_analisicotizacion.codemp = soc_cotxanalisis.codemp".
                         "    AND soc_analisicotizacion.numanacot= soc_cotxanalisis.numanacot".
                          "    AND soc_analisicotizacion.numanacot= soc_dtac_bienes.numanacot".
                         "    AND soc_dtac_bienes.codart = siv_articulo.codart".
                         "    AND soc_dtcot_bienes.codart = soc_dtac_bienes.codart".
                         "    AND soc_dtcot_bienes.numcot = soc_cotizacion.numcot".
                         "    AND siv_unidadmedida.codunimed = siv_articulo.codunimed".
                         "    GROUP BY soc_cotxanalisis.numcot;";                
            }
            else
            {
                 $ls_sql="  SELECT count(soc_cotxanalisis.numcot)
                            FROM soc_analisicotizacion, soc_cotizacion, rpc_proveedor, soc_cotxanalisis,
                                  soc_servicios,soc_dtac_servicios,soc_dtcot_servicio
                            WHERE soc_analisicotizacion.codemp = '$this->ls_codemp'
                                 AND soc_analisicotizacion.numanacot= '$as_numanacot'
                                 AND soc_analisicotizacion.codemp = soc_cotizacion.codemp
                                 AND soc_cotxanalisis.cod_pro = soc_cotizacion.cod_pro
                                 AND soc_cotxanalisis.numcot = soc_cotizacion.numcot
                                 AND soc_cotxanalisis.cod_pro = rpc_proveedor.cod_pro
                                 AND soc_analisicotizacion.codemp = rpc_proveedor.codemp
                                 AND soc_analisicotizacion.codemp = soc_cotxanalisis.codemp
                                 AND soc_analisicotizacion.numanacot= soc_cotxanalisis.numanacot
                                 AND soc_analisicotizacion.numanacot= soc_dtac_servicios.numanacot
                                 AND soc_servicios.codemp=soc_dtac_servicios.codemp
                                 AND soc_servicios.codser = soc_dtac_servicios.codser
                                 AND soc_dtac_servicios.codemp = soc_dtcot_servicio.codemp
                                 AND soc_dtac_servicios.numcot = soc_dtcot_servicio.numcot
                            GROUP BY soc_cotxanalisis.numcot;";                
            }
    
			//print $ls_sql;
				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("ERROR->uf_count_cotizaciones".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					print ($this->io_sql->message);
					$lb_valido=false;
				}
				else
				{
					$li_i=0;
					while($row=$this->io_sql->fetch_row($rs_data))//Se verifica si la solicitud es de bienes o de servicios
					{
						$li_i++;
						$aa_proveedores[$li_i]=$row;
						$lb_valido=true;
					}
				}
			return $lb_valido;
		}//fin de uf_cargar_cotizaciones
	//---------------------------------------------------------------------------------------------------------------------------------------




	//---------------------------------------------------------------------------------------------------------------------------------------
		function uf_cargar_cotizaciones($as_numanacot, &$aa_proveedores)
		{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_cargar_cotizaciones
			//		   Access: public
			//     Parameters: $as_numanacot--->numero del analisis de cotizacion
			//		  return : arreglo que contiene las cotizaciones que participaron en un determinado analisis
			//	  Description: Metodo que  devuelve las cotizaciones que participaron en un determinado analisis
			//	   Creado Por: Ing. Laura Cabr�
			// 			  Fecha: 18/06/2007								Fecha �ltima Modificaci�n :
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$aa_proveedores=array();
			$lb_valido=false;
			$ls_sql= "SELECT usu.nomusu,usu.apeusu,a.numsolcot, cxa.numcot, c.feccot, c.montotcot, c.poriva, p.nompro, p.cod_pro, 
							 p.rifpro, p.dirpro,c.monsubtot,c.monimpcot,c.montotcot,c.garanacot,c.forpagcom,c.diaentcom,
							 c.estesp,c.estasitec
					  FROM soc_analisicotizacion a, soc_cotizacion c, rpc_proveedor p, soc_cotxanalisis cxa, sss_usuarios usu
					  WHERE a.codemp='$this->ls_codemp' AND a.numanacot='$as_numanacot'
					  AND a.codemp=c.codemp AND cxa.cod_pro=c.cod_pro AND cxa.numcot=c.numcot
					  AND a.codemp=p.codemp AND cxa.cod_pro=p.cod_pro
					  AND a.codemp=cxa.codemp and a.numanacot=cxa.numanacot AND usu.codusu=a.codusu";

				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("ERROR->uf_cargar_cotizaciones".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					$li_i=0;
					while($row=$this->io_sql->fetch_row($rs_data))//Se verifica si la solicitud es de bienes o de servicios
					{
						$li_i++;
						$aa_proveedores[$li_i]=$row;
						$lb_valido=true;
					}
				}
			return $lb_valido;
		}//fin de uf_cargar_cotizaciones
	//---------------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_analisis_cotizaciones($as_anacotdes,$as_anacothas,$as_codprodes,$as_codprohas,
											$as_fecanades,$as_fecanahas,$as_tipanacot,&$aa_cotizaciones)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_analisis_cotizaciones
		//		   Access: public
		//	   Parametros:
		//		        $as_anacotdes  //Numero de solicitud de Cotizaci�n a partir del cual realizaremos la b�squeda.
		//				$as_anacothas  //Numero de solicitud de Cotizaci�n hasta el cual realizaremos la b�squeda.
		//				$as_codprodes  //C�digo del Proveedor a partir del cual realizaremos la b�squeda.
		//				$as_codprohas  //C�digo del Proveedor hasta el cual realizaremos la b�squeda.
		//				$as_fecanades  //Fecha de la Solicitud de Cotizaci�n a partir del cual realizaremos la b�squeda.
		//				$as_fecanahas  //Fecha de la Solicitud de Cotizaci�n hasta el cual realizaremos la b�squeda.
		//				$as_tipsolcot  //Tipo de la Solicitud de Cotizaci�n B=Bienes, S=Servicios.
		//		  return : arreglo que contiene los analisis de cotizacion filtrados segun parametros de busqueda
		//	  Description: Metodo que  devuelve los analisis de cotizacion filtrados segun parametros de busqueda
		//	   Creado Por: Ing. Laura Cabr�
		// 			Fecha: 23/06/2007								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_cotizaciones=array();
		$lb_valido=false;
		$ls_straux = "";
			if (!empty($as_tipanacot))
			 {
			   $ls_straux = $ls_straux." AND a.tipsolcot='".$as_tipanacot."'";
			 }
		  if (!empty($as_anacotdes))
			 {
			   $ls_straux = $ls_straux. " AND a.numanacot>='".$as_anacotdes."'";
			 }
		  if (!empty($as_anacothas))
			 {
			   $ls_straux = $ls_straux. " AND a.numanacot<='".$as_anacothas."' ";
			 }
		  if (!empty($as_codprodes))
			 {
			   $ls_straux = $ls_straux. " AND a.codemp=c.codemp AND a.numanacot=c.numanacot AND c.cod_pro>='$as_codprodes'";
			 }
		  if (!empty($as_codprohas))
			 {
			   $ls_straux = $ls_straux. " AND a.codemp=c.codemp AND a.numanacot=c.numanacot AND c.cod_pro<='$as_codprohas'";
			 }
		  if (!empty($as_fecanades))
			 {
			   $ls_fecanades = $this->io_funciones->uf_convertirdatetobd($as_fecanades);
			   $ls_straux = $ls_straux. " AND a.fecanacot>='".$ls_fecanades."'";
			 }
		  if (!empty($as_fecanahas))
			 {
			   $ls_fecanahas= $this->io_funciones->uf_convertirdatetobd($as_fecanahas);
			   $ls_straux = $ls_straux. " AND a.fecanacot<='".$ls_fecanahas."' ";
			 }
		$ls_sql= "SELECT DISTINCT a.numanacot, a.fecanacot, a.obsana, a.tipsolcot
				FROM soc_analisicotizacion a, soc_cotxanalisis c
				WHERE a.codemp='$this->ls_codemp'".$ls_straux;

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_analisis_cotizaciones".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))//Se verifica si la solicitud es de bienes o de servicios
			{
				$aa_cotizaciones[$li_i]["numero"]=$row["numanacot"];
				$aa_cotizaciones[$li_i]["fecha"]=$this->io_funciones->uf_convertirfecmostrar($row["fecanacot"]);
				$aa_cotizaciones[$li_i]["observacion"]=$row["obsana"];
				if($row["tipsolcot"]=="B")
					$aa_cotizaciones[$li_i]["tipo"]="Bienes";
				else
					$aa_cotizaciones[$li_i]["tipo"]="Servicios";
				$li_i++;
				$lb_valido=true;
			}
		}
		return $lb_valido;
	}//fin de uf_select_analisis_cotizaciones
//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
function uf_select_precios_cotizacion($as_tipsolcot,$aa_proveedores,&$aa_items)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_items_cotizacion
		//		   Access: public
		//		   return:arreglo con los bienes/servicios de la cotizacion dada
		//	  Description: Metodo que  devuelve los bienes/servicios de una cotizacion
		//	   Creado Por: Ing. Laura Cabr�
		// 			  Fecha: 09/05/2007								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_items=array();
		$lb_valido=true;
		//Tomando los datos del querystring
		$li_totalcotizaciones=count($aa_proveedores);
		for($li_i=1;$li_i<=$li_totalcotizaciones;$li_i++)
		{
			$ls_codpro=$aa_proveedores[$li_i]["cod_pro"];
			$ls_nompro=$aa_proveedores[$li_i]["nompro"];
			$ls_numcot=$aa_proveedores[$li_i]["numcot"];
			if($as_tipsolcot=='B')//Si la solicitud es de Bienes
			{
				$ls_sql= "SELECT a.denart as denominacion, d.montotart as monto
							FROM soc_dtcot_bienes d, siv_articulo a
							WHERE d.codemp='$this->ls_codemp' AND d.numcot='$ls_numcot'
							AND d.cod_pro='$ls_codpro' AND d.codemp=a.codemp AND a.codart=d.codart";
			}
			elseif($as_tipsolcot=='S') //Si la solicitud es de Servicios
			{

				$ls_sql= "SELECT a.denser as denominacion, d.montotser as monto
							FROM soc_dtcot_servicio d, soc_servicios a
							WHERE d.codemp='$this->ls_codemp' AND d.numcot='$ls_numcot'
							AND d.cod_pro='$ls_codpro' AND d.codemp=a.codemp AND a.codser=d.codser";
			}
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("ERROR->uf_select_items_cotizacion".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{

				while($row=$this->io_sql->fetch_row($rs_data))
				{
					$aa_items[$row["denominacion"]][$ls_nompro]=$row["monto"];
				}
			}
		}
		return $lb_valido;
	}  //Fin funcion uf_select_items_cotizacion
//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
function uf_analisis_cualitativo($aa_proveedores,&$la_arre2)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_analisis_cualitativo
		//		   Access: public
		//		   return:arreglo con los calificadores de un conjunto de proveedores dados
		//	  Description: Metodo que  devuelve los calificadores de un conjunto de proveedores dados
		//	   Creado Por: Ing. Laura Cabr�
		// 			  Fecha: 09/05/2007								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_arre1=array();
		$la_arre2=array();
		$lb_valido=true;
		//Tomando los datos del querystring
		$li_totalcotizaciones=count($aa_proveedores);
		$ls_proveedores="(";
		$ls_parentesis="";
		for($li_i=1;$li_i<=$li_totalcotizaciones;$li_i++)//Construyendo la consulta sql;
		{
			$ls_codpro=$aa_proveedores[$li_i]["cod_pro"];
			$ls_parentesis=$ls_parentesis.")";
			$ls_proveedores=$ls_proveedores."'".$ls_codpro."'";
			if($li_i<$li_totalcotizaciones)
				$ls_proveedores=$ls_proveedores.",";
		}
		$ls_proveedores=$ls_proveedores.")";

		$ls_sql="SELECT DISTINCT codclas FROM rpc_clasifxprov c WHERE cod_pro IN $ls_proveedores
					AND codemp='$this->ls_codemp'  AND status=0 AND codclas IN ";

		for($li_i=1;$li_i<=$li_totalcotizaciones;$li_i++)
		{
			$ls_codpro=$aa_proveedores[$li_i]["cod_pro"];
			$ls_sql=$ls_sql."(SELECT codclas FROM rpc_clasifxprov  WHERE cod_pro='$ls_codpro' ";
			if($li_i<$li_totalcotizaciones)
			 $ls_sql=$ls_sql."AND codclas IN ";
		}
		$ls_sql=$ls_sql.$ls_parentesis;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_i++;
				$la_arre1[$li_i]=$row["codclas"];
			}
		}
		if(($lb_valido) && ($li_totcalificadores=count($la_arre1))>0)//Si existen calificadores en comun y no ocurrio ningun error, se buscan los valores
		{																				//de cada calificador por proveedor
				for($li_i=1;$li_i<=$li_totalcotizaciones;$li_i++)
				{
					$ls_codpro=$aa_proveedores[$li_i]["cod_pro"];
					$ls_nompro=$aa_proveedores[$li_i]["nompro"];
					$la_calificadores=array();
					for($li_j=1; $li_j<=$li_totcalificadores;$li_j++)
					{
							$ls_codclas=$la_arre1[$li_j];
							$ls_sql="SELECT c.denclas, cp.nivstatus
									   FROM rpc_clasifxprov cp, rpc_clasificacion c
									  WHERE c.codemp='$this->ls_codemp'
										AND cp.cod_pro='$ls_codpro'
										AND cp.codclas='$ls_codclas'
										AND c.codemp=cp.codemp
										AND cp.codclas=c.codclas";

							$rs_data=$this->io_sql->select($ls_sql);
							if($rs_data===false)
							{
								$this->io_mensajes->message("ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
								$lb_valido=false;
							}
							else
							{
								while($row=$this->io_sql->fetch_row($rs_data))
								{

									switch($row["nivstatus"])
									{
										case "0":
											$la_calificadores[$row["denclas"]]="Ninguno";
										break;
										case "1":
											$la_calificadores[$row["denclas"]] ="Bueno";
										break;
										case "2":
											$la_calificadores[$row["denclas"]] ="Regular";
										break;
										case "3":
											$la_calificadores[$row["denclas"]]="Malo";
										break;
									}

								}
							}
					}
					$la_arre2[$ls_nompro]=$la_calificadores;
				}
		}
		return $lb_valido;
	}  //Fin funcion uf_analisis_cualitativo
//---------------------------------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------------------------------
function uf_analisis_cualitativo_items($as_tipsolcot,$aa_proveedores,&$aa_items)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_analisis_cualitativo_items
		//		   Access: public
		//		   return: arreglo con los calificadores de los bienes/servicios por cotizacion
		//	  Description: Metodo que  devuelve los calificadores de los bienes/servicios de una cotizacion
		//	   Creado Por: Ing. Laura Cabr�
		// 			Fecha: 23/05/2007								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_items=array();
		$lb_valido=true;
		//Tomando los datos del querystring
		$li_totalcotizaciones=count($aa_proveedores);
		for($li_i=1;$li_i<=$li_totalcotizaciones;$li_i++)
		{
			$ls_codpro=$aa_proveedores[$li_i]["cod_pro"];
			$ls_nompro=$aa_proveedores[$li_i]["nompro"];
			$ls_numcot=$aa_proveedores[$li_i]["numcot"];
			if($as_tipsolcot=='B')//Si la solicitud es de Bienes
			{
				$ls_sql= "SELECT a.denart as denominacion,d.nivcalart AS calificacion
							FROM soc_dtcot_bienes d, siv_articulo a
							WHERE d.codemp='$this->ls_codemp' AND d.numcot='$ls_numcot'
							AND d.cod_pro='$ls_codpro' AND d.codemp=a.codemp AND a.codart=d.codart";
			}
			elseif($as_tipsolcot=='S') //Si la solicitud es de Servicios
			{

				$ls_sql= "SELECT a.denser as denominacion, d.nivcalser AS calificacion
							FROM soc_dtcot_servicio d, soc_servicios a
							WHERE d.codemp='$this->ls_codemp' AND d.numcot='$ls_numcot'
							AND d.cod_pro='$ls_codpro' AND d.codemp=a.codemp AND a.codser=d.codser";
			}
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("ERROR->uf_analisis_cualitativo_items".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{

				while($row=$this->io_sql->fetch_row($rs_data))
				{
					switch($row["calificacion"])
					{
						case "E":
							$ls_calificacion="Excelente";
						break;
						case "B":
							$ls_calificacion="Bueno";
						break;
						case "R":
							$ls_calificacion="Regular";
						break;
						case "M":
							$ls_calificacion="Malo";
						break;
						case "P":
							$ls_calificacion="Muy Malo";
						break;
					}

					$aa_items[$row["denominacion"]][$ls_nompro]=$ls_calificacion;
				}
			}
		}
		return $lb_valido;
	}  //Fin funcion uf_analisis_cualitativo_items
//---------------------------------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------------------------------
function uf_load_solicitudes_cotizacion($as_solcotdes,$as_solcothas,$as_codprodes,$as_codprohas,
                                        $as_numsepdes,$as_numsephas,$as_fecsoldes,$as_fecsolhas,$as_tipsolcot,$as_estsolcot,&$lb_valido)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function:  uf_load_solicitudes_cotizacion
//		   Access:  public
//		 Argument:
//  $ls_solcotdes  //Numero de solicitud de Cotizaci�n a partir del cual realizaremos la b�squeda.
//  $ls_solcothas  //Numero de solicitud de Cotizaci�n hasta el cual realizaremos la b�squeda.
//  $ls_codprodes  //C�digo del Proveedor a partir del cual realizaremos la b�squeda.
//  $ls_codprohas  //C�digo del Proveedor hasta el cual realizaremos la b�squeda.
//  $ls_numsepdes  //Numero de Solicitud de Ejecucion Presupuestaria a partir del cual realizaremos la b�squeda.
//  $ls_numsephas  //Numero de Solicitud de Ejecucion Presupuestaria hasta el cual realizaremos la b�squeda.
//  $ls_fecsoldes  //Fecha de la Solicitud de Cotizaci�n a partir del cual realizaremos la b�squeda.
//  $ls_fecsolhas  //Fecha de la Solicitud de Cotizaci�n hasta el cual realizaremos la b�squeda.
//  $ls_tipsolcot  //Tipo de la Solicitud de Cotizaci�n B=Bienes, S=Servicios.
//  $ls_estsolcot  //Estatus de la Solicitud de Cotizaci�n.
//     $lb_valido  //Variable booleana que devuelve true si todo se ejecut� con �xito, false de lo contrario.
//	  Description: Funci�n que obtiene las Solicitudes de Cotizaci�n seg�n se especifiquen los parametros de b�squeda.
//	   Creado Por: Ing. Nestor Falcon.
// Fecha Creaci�n: 21/06/2007								Fecha �ltima Modificaci�n : 21/06/2007
////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  $ls_straux = "";
  $ls_tabla = "";
  if (!empty($as_tipsolcot) && ($as_tipsolcot!='-'))
     {
	   $ls_straux = $ls_straux." AND soc_sol_cotizacion.tipsolcot='".$as_tipsolcot."'";
	 }

  if (!empty($as_solcotdes) && !empty($as_solcothas))
     {
	   $ls_straux = $ls_straux." AND soc_sol_cotizacion.numsolcot BETWEEN '".$as_solcotdes."' AND '".$as_solcothas."' ";
	 }
  if (!empty($as_codprodes) && !empty($as_codprohas))
     {
	   if ($as_tipsolcot=='B')
	      {
	        $ls_tabla = ", soc_dtsc_bienes";
			$ls_straux = $ls_straux." AND soc_dtsc_bienes.cod_pro BETWEEN '".$as_codprodes."' AND '".$as_codprohas."'";
		  }
	   elseif($as_tipsolcot=='S')
	      {
	        $ls_tabla  = ", soc_dtsc_servicios";
			$ls_straux = $ls_straux." AND soc_dtsc_servicios.cod_pro BETWEEN '".$as_codprodes."' AND '".$as_codprohas."'";
		  }
	 }
  if (!empty($as_numsepdes) && !empty($as_numsephas))
     {
	   $ls_tabla  = $ls_tabla.", soc_solcotsep";
	   $ls_straux = $ls_straux." AND soc_solcotsep.numsol BETWEEN '".$as_numsepdes."' AND '".$as_numsephas."'";
	 }
  if (!empty($as_fecsoldes) && !empty($as_fecsolhas))
     {
	   $ls_fecsoldes = $this->io_funciones->uf_convertirdatetobd($as_fecsoldes);
	   $ls_fecsolhas = $this->io_funciones->uf_convertirdatetobd($as_fecsolhas);
	   $ls_straux    = $ls_straux." AND soc_sol_cotizacion.fecsol BETWEEN '".$ls_fecsoldes."' AND '".$ls_fecsolhas."' ";
	 }
  if (!empty($as_estsolcot) && $as_estsolcot!='-')
     {
	   if ($as_estsolcot=='R')
	      {
			$ls_straux = $ls_straux." AND soc_sol_cotizacion.numsolcot NOT IN (SELECT numsolcot FROM soc_cotizacion WHERE codemp='".$this->ls_codemp."')";
		  }
	   elseif($as_estsolcot=='P')
	      {
			$ls_straux = $ls_straux." AND soc_sol_cotizacion.numsolcot IN (SELECT numsolcot FROM soc_cotizacion WHERE codemp='".$this->ls_codemp."')";
		  }
	 }

  $ls_sql = "SELECT soc_sol_cotizacion.numsolcot,
                    max(soc_sol_cotizacion.fecsol) as fecsol,
					max(soc_sol_cotizacion.obssol) as obssol,
					max(soc_sol_cotizacion.tipsolcot) as tipsolcot
               FROM soc_sol_cotizacion $ls_tabla
			  WHERE soc_sol_cotizacion.codemp='".$this->ls_codemp."' $ls_straux
			  GROUP BY soc_sol_cotizacion.numsolcot
			  ORDER BY soc_sol_cotizacion.numsolcot ASC";//print $ls_sql;
  $rs_data = $this->io_sql->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_mensajes->message("CLASE->sigesp_soc_class_report.php->M�TODO->uf_load_solicitudes_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
	 }
  return $rs_data;
}

function uf_load_registro_cotizaciones($as_numcotdes,$as_numcothas,$as_codprodes,$as_codprohas,$as_numsolcotdes,$as_numsolcothas,
	                                   $as_feccotdes,$as_feccothas,$as_tipcot,$as_estcot,&$lb_valido)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function:  uf_load_registro_cotizaciones
//		   Access:  public
//		 Argument:
//     $ls_numcotdes  //Numero de Cotizaci�n a partir del cual realizaremos la b�squeda.
//     $ls_numcothas  //Numero de Cotizaci�n hasta el cual realizaremos la b�squeda.
//     $ls_codprodes  //C�digo del Proveedor a partir del cual realizaremos la b�squeda.
//     $ls_codprohas  //C�digo del Proveedor hasta el cual realizaremos la b�squeda.
//  $ls_numsolcotdes  //Numero de Solicitud de Cotizacion a partir del cual realizaremos la b�squeda.
//  $ls_numsolcothas  //Numero de Solicitud de Cotizacion hasta el cual realizaremos la b�squeda.
//     $ls_fecsoldes  //Fecha de la Solicitud de Cotizaci�n a partir del cual realizaremos la b�squeda.
//     $ls_fecsolhas  //Fecha de la Solicitud de Cotizaci�n hasta el cual realizaremos la b�squeda.
//        $ls_tipcot  //Tipo de la Cotizaci�n B=Bienes, S=Servicios.
//        $ls_estcot  //Estatus de Cotizaci�n R=Registro, P=Procesada.
//        $lb_valido  //Variable booleana que devuelve true si todo se ejecut� con �xito, false de lo contrario.
//	    Description:  Funci�n que obtiene las Solicitudes de Cotizaci�n seg�n se especifiquen los parametros de b�squeda.
//	     Creado Por:  Ing. Nestor Falcon.
//   Fecha Creaci�n:  15/07/2007								Fecha �ltima Modificaci�n : 15/07/2007
////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  $ls_straux = "";
  $ls_tabla = "";
  if (!empty($as_tipcot) && ($as_tipcot!='-'))
     {
	   $ls_straux = $ls_straux." AND soc_cotizacion.tipcot='".$as_tipcot."'";
	 }

  if (!empty($as_numcotdes) && !empty($as_numcothas))
     {
	   $ls_straux = $ls_straux." AND soc_cotizacion.numcot BETWEEN '".$as_numcotdes."' AND '".$as_numcothas."' ";
	 }
  if (!empty($as_codprodes) && !empty($as_codprohas))
     {
	   if ($as_tipcot=='B')
	      {
	        $ls_tabla = ", soc_dtcot_bienes";
			$ls_straux = $ls_straux." AND soc_dtcot_bienes.cod_pro BETWEEN '".$as_codprodes."' AND '".$as_codprohas."'";
		  }
	   elseif($as_tipcot=='S')
	      {
	        $ls_tabla  = ", soc_dtcot_servicio";
			$ls_straux = $ls_straux." AND soc_dtcot_servicio.cod_pro BETWEEN '".$as_codprodes."' AND '".$as_codprohas."'";
		  }
	 }
  if (!empty($as_numsolcotdes) && !empty($as_numsolcothas))
     {
	   $ls_tabla  = $ls_tabla.", soc_sol_cotizacion";
	   $ls_straux = $ls_straux." AND soc_sol_cotizacion.numsol BETWEEN '".$as_numsolcotdes."' AND '".$as_numsolcothas."'";
	 }
  if (!empty($as_feccotdes) && !empty($as_feccothas))
     {
	   $ls_feccotdes = $this->io_funciones->uf_convertirdatetobd($as_feccotdes);
	   $ls_feccothas = $this->io_funciones->uf_convertirdatetobd($as_feccothas);
	   $ls_straux    = $ls_straux." AND soc_cotizacion.feccot BETWEEN '".$ls_feccotdes."' AND '".$ls_feccothas."' ";
	 }
  if (!empty($as_estcot) && $as_estcot!='-')
     {
	   if ($as_estcot=='R')
	      {
			$ls_straux = $ls_straux." AND soc_cotizacion.numcot NOT IN (SELECT numcot FROM soc_cotxanalisis WHERE codemp='".$this->ls_codemp."')";
		  }
	   elseif($as_estcot=='P')
	      {
			$ls_straux = $ls_straux." AND soc_cotizacion.numcot IN (SELECT numcot FROM soc_cotxanalisis WHERE codemp='".$this->ls_codemp."')";
		  }
	 }

  $ls_sql = "SELECT soc_cotizacion.numcot,
  					max(soc_cotizacion.feccot) as feccot,
					max(soc_cotizacion.obscot) as obscot,
					max(soc_cotizacion.tipcot) as tipcot,
					max(rpc_proveedor.nompro) as nompro
               FROM soc_cotizacion, rpc_proveedor $ls_tabla
			  WHERE soc_cotizacion.codemp='".$this->ls_codemp."' $ls_straux
			    AND soc_cotizacion.codemp = rpc_proveedor.codemp
		 	    AND soc_cotizacion.cod_pro = rpc_proveedor.cod_pro
			  GROUP BY soc_cotizacion.numcot
			  ORDER BY soc_cotizacion.numcot ASC";//print $ls_sql;

  $rs_data = $this->io_sql->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_mensajes->message("CLASE->sigesp_soc_class_report.php->M�TODO->uf_load_registro_cotizaciones.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
	 }
  return $rs_data;
}

function uf_load_orden_servicio($as_numordcom,&$lb_valido)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////
//	       Function:  uf_load_orden_servicio
//		     Access:  public
//		   Argument:
//    $as_numordcom:  Numero de la Orden de Compra tipo servicio que ser� impresa.
//       $lb_valido:  Variable booleana que devuelve true si todo se ejecut� con �xito, false de lo contrario.
//	    Description:  Funci�n que obtiene los datos de la Orden de Compra.
//	     Creado Por:  Ing. Nestor Falcon.
//   Fecha Creaci�n:  22/07/2007								Fecha �ltima Modificaci�n : 22/07/2007
////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  $ls_sql    = "SELECT soc_ordencompra.fecordcom, soc_ordencompra.montot, soc_ordencompra.estcom,soc_dt_servicio.codser, soc_servicios.denser,
                       soc_dt_servicio.canser, soc_dt_servicio.monuniser,rpc_proveedor.nompro
                  FROM soc_ordencompra, soc_dt_servicio, rpc_proveedor, soc_servicios
                 WHERE soc_ordencompra.codemp='".$this->ls_codemp."'
                   AND soc_ordencompra.numordcom='".$as_numordcom."'
				   AND soc_ordencompra.estcondat='S'
				   AND soc_ordencompra.codemp=soc_dt_servicio.codemp
				   AND soc_ordencompra.numordcom=soc_dt_servicio.numordcom
				   AND soc_ordencompra.estcondat=soc_dt_servicio.estcondat
				   AND soc_ordencompra.codemp=rpc_proveedor.codemp
				   AND soc_ordencompra.cod_pro=rpc_proveedor.cod_pro
				   AND soc_dt_servicio.codemp=soc_servicios.codemp
				   AND soc_dt_servicio.codser=soc_servicios.codser";
  $rs_data = $this->io_sql->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_mensajes->message("CLASE->sigesp_soc_class_report.php->M�TODO->uf_load_orden_servicio.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
	 }
  return $rs_data;
}
//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
function uf_select_imputacion_spg_orden_compra($as_numordcomdes,$as_numordcomhas,$as_fecordcomdes,$as_fecordcomhas,
						$as_tipord,$as_tipo,&$lb_valido)
{
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_select_imputacion_spg_orden_compra
	//         Access: public
	//	Arguments: 	$as_numordcom   ---> Orden de Compra a imprimir
	//                	$as_tipord  	---> Tipo de la orden de compra, bienes o servicios
	//	  Returns: lb_valido True si se creo el Data store correctamente � False si no se creo
	//    Description: funci�n que busca los detalles de la imputacion presupuestaria de las ordenes de compra para imprimir
	//     Creado Por: Victor Mendoza
	// Fecha Creaci�n: 20/08/2009					Fecha �ltima Modificaci�n :
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido = true;
	
        $ls_criterio_a ="";
        if(  (($as_numordcomdes!="") && ($as_numordcomhas=="")) || (($as_numordcomdes=="") && ($as_numordcomhas!=""))  )
	{
	   $lb_valido = false;
	   $this->io_msg->message("Debe Completar el Rango de Busqueda por N�mero !!!");
	}
	else
	{
		if( ($as_numordcomdes!="") && ($as_numordcomhas!="") )
		{
		   $ls_criterio_a = " soc_ordencompra.numordcom >='".$as_numordcomdes."'  AND  soc_ordencompra.numordcom <='".$as_numordcomhas."'  AND  ";
		}
		else
		{
		   $ls_criterio_a ="";
		}
	}
	if(  (($as_fecordcomdes!="") && ($as_fecordcomhas=="")) || (($as_fecordcomdes=="") && ($as_fecordcomhas!=""))  )
	{
	   $lb_valido = false;
	   $this->io_msg->message("Debe Completar el Rango de Busqueda por Fechas !!!");
	}
	else
	{
           if (($as_fecordcomdes != "") && ($as_fecordcomhas != ""))
           {
                $ls_fecha = $this->io_funciones->uf_convertirdatetobd($as_fecordcomdes);
                $as_fecordcomdes = $ls_fecha;
                
                $ls_fechas = $this->io_funciones->uf_convertirdatetobd($as_fecordcomhas);
                $as_fecordcomhas = $ls_fechas;
                
                //   if($ls_criterio_b=="")
                //   {
                //		 $CB_AND="";  //CB = Criterio B
                //   }
                //   else
                //   {
                //		 $CB_AND="  AND  ";
                //   }
                
                $ls_criterio_c = " soc_ordencompra.fecordcom >='".$as_fecordcomdes."'  AND  soc_ordencompra.fecordcom <='".$as_fecordcomhas."'  AND ";
           }
           else
           {
               $ls_criterio_c = "";
           }
	}
	//if( ($as_tipord=="A")  ||  ($as_tipord=="") )
	//{
	//	 $ls_criterio_h = $ls_criterio_g;
	//}
	//else
	//{
	//	 if(empty($ls_criterio_g))
	//	 {
	//		 $CG_AND=""; //CC = Criterio C
	//	 }
	//	 else
	//	 {
	//		$CG_AND="   AND   ";
	//	 }
	//	 if($as_tipord=="B")
	//	 {
	//		 $ls_criterio_h = $ls_criterio_g.$CG_AND." estcondat='B' ";
	//	 }
	//	 else
	//	 {
	//		 if($as_tipord=="S")
	//		 {
	//			 $ls_criterio_h = $ls_criterio_g.$CG_AND." estcondat='S' ";
	//		 }
	//	 }
	//}

	switch ($as_tipord)
	{
	   case 'B':
		$ls_sql=" SELECT MAX(soc_dt_bienes.numordcom) as numordcom, MAX(soc_dt_bienes.codart) AS codartser, MAX(siv_articulo.denart) as denartser, ".
				     "      SUM(soc_dt_bienes.canart) as cantartser, MAX(soc_dt_bienes.unidad) as unidad, MAX(soc_dt_bienes.preuniart) as preartser,   ".
				     "	    SUM(soc_dt_bienes.monsubart) as montsubartser, SUM(soc_dt_bienes.montotart) as monttotartser, MAX(siv_articulo.spg_cuenta) AS spg_cuenta, ".
				     "      MAX(soc_dt_bienes.orden) as orden, MAX(siv_articulo.codunimed) as codunimed, MAX(siv_unidadmedida.denunimed) as denunimed, ".
				     "      MAX(soc_ordencompra.fecordcom) as fecordcom, ".
				     "      (SELECT MAX(sigesp_cargos.porcar) ".
				     "      FROM sigesp_cargos, soc_dta_cargos ".
				     "	    WHERE MAX(soc_dt_bienes.codemp)=soc_dta_cargos.codemp ".
				     "            AND MAX(soc_dt_bienes.numordcom)=soc_dta_cargos.numordcom ".
				     "            AND MAX(soc_dt_bienes.estcondat)=soc_dta_cargos.estcondat ".
				     "            AND MAX(soc_dt_bienes.codart)=soc_dta_cargos.codart ".
				     "            AND soc_dta_cargos.codemp=sigesp_cargos.codemp ".
				     "            AND soc_dta_cargos.codcar=sigesp_cargos.codcar) AS porimp ".
				     " FROM   	soc_ordencompra , soc_dt_bienes , siv_articulo, siv_unidadmedida ".
				     " WHERE  	soc_dt_bienes.codemp='".$this->ls_codemp."' AND ".$ls_criterio_c.
				     $ls_criterio_a."   ".
				     "        	soc_dt_bienes.estcondat='".$as_tipord."' AND ".
				     "		soc_dt_bienes.codemp=soc_ordencompra.codemp AND ".
				     "		soc_dt_bienes.codemp=siv_articulo.codemp AND ".
				     "		siv_articulo.codemp=soc_ordencompra.codemp AND ".
				     "		soc_dt_bienes.numordcom=soc_ordencompra.numordcom AND ".
				     "		soc_dt_bienes.estcondat=soc_ordencompra.estcondat AND ".
				     " 		soc_dt_bienes.codart=siv_articulo.codart  AND ".
				     "	    	siv_unidadmedida.codunimed=siv_articulo.codunimed ".
				     "  GROUP BY soc_dt_bienes.numordcom,soc_dt_bienes.codart".
				     "  ORDER BY MAX(soc_dt_bienes.numordcom),MAX(soc_dt_bienes.orden) ASC "; //print $ls_sql."<br>";
	  break;

	  case 'S':
	       $ls_sql=" SELECT MAX(soc_dt_servicio.numordcom) as numordcom, MAX(soc_dt_servicio.codser) as codartser, MAX(soc_servicios.denser) as denartser , ".
				   "        SUM(soc_dt_servicio.canser) as cantartser, MAX(soc_dt_servicio.monuniser) as preartser, ".
				   "        SUM(soc_dt_servicio.montotser) as monttotartser, ".
				   "        SUM(soc_dt_servicio.monsubser) as montsubartser, MAX(soc_servicios.spg_cuenta) as spg_cuenta, MAX(soc_dt_servicio.orden) as orden, ".
				   "        MAX(soc_ordencompra.fecordcom) as fecordcom, MAX(siv_unidadmedida.denunimed) as denunimed, ".
				   "        (SELECT MAX(sigesp_cargos.porcar) ".
				   "        FROM sigesp_cargos, soc_dts_cargos ".
				   "	    WHERE MAX(soc_dt_servicio.codemp)=soc_dts_cargos.codemp ".
				   "           	AND MAX(soc_dt_servicio.numordcom)=soc_dts_cargos.numordcom ".
				   "           	AND MAX(soc_dt_servicio.estcondat)=soc_dts_cargos.estcondat ".
				   "           	AND MAX(soc_dt_servicio.codser)=soc_dts_cargos.codser ".
				   "           	AND soc_dts_cargos.codemp=sigesp_cargos.codemp ".
				   "           	AND soc_dts_cargos.codcar=sigesp_cargos.codcar) AS porimp ".
				   " FROM   soc_ordencompra ".
				   "  	JOIN soc_dt_servicio on (soc_dt_servicio.codemp=soc_ordencompra.codemp".
				   "  	AND  soc_dt_servicio.numordcom=soc_ordencompra.numordcom ".
				   "  	AND  soc_dt_servicio.estcondat=soc_ordencompra.estcondat)".
				   "  	JOIN soc_servicios  on ( soc_servicios.codemp=soc_ordencompra.codemp ".
				   "   	AND  soc_dt_servicio.codser=soc_servicios.codser )".
				   " 	LEFT JOIN  siv_unidadmedida on (soc_servicios.codunimed=siv_unidadmedida.codunimed) ".
				   " WHERE  soc_dt_servicio.codemp='".$this->ls_codemp."' AND ".$ls_criterio_c.
				   $ls_criterio_a."   ".
				   "	    soc_dt_servicio.estcondat='".$as_tipord."' ".
				   "  GROUP BY soc_dt_servicio.numordcom,soc_dt_servicio.codser".
				   "  ORDER BY MAX(soc_dt_servicio.numordcom),MAX(soc_dt_servicio.orden) ASC ";// print $ls_sql;
	  break;
	}
	//print $ls_sql;	
	$rs_data=$this->io_sql->select($ls_sql);
	
	if($rs_data===false)
	{
		$this->io_mensajes->message("ERROR->uf_select_imputacion_spg_orden_compra".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		$lb_valido=false;
	}
	
        return $rs_data;	
	
}	//end function uf_select_imputacion_spg_orden_compra

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
function uf_select_imputacion_cuentas_spg_orden_compra($as_numordcomdes,$as_numordcomhas,$as_fecordcomdes,$as_fecordcomhas,
						$as_tipord,$as_tipo,&$lb_valido)
{
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_select_imputacion_cuentas_spg_orden_compra
	//         Access: public
	//	Arguments: 	$as_numordcom   ---> Orden de Compra a imprimir
	//                	$as_tipord  	---> Tipo de la orden de compra, bienes o servicios
	//	  Returns: lb_valido True si se creo el Data store correctamente � False si no se creo
	//    Description: funci�n que busca la estrcutura presupuestaria y las cuentas de la tabla de imputacion presupuestaria de las ordenes de compra
	//     Creado Por: Victor Mendoza
	// Fecha Creaci�n: 20/08/2009					Fecha �ltima Modificaci�n :
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido = true;
        if( ($as_numordcomdes!="") && ($as_numordcomhas!="") )
        {
           $ls_criterio_a = "   numordcom >='".$as_numordcomdes."'  AND  numordcom <='".$as_numordcomhas."' AND   ";
        }
        else
        {
           $ls_criterio_a ="";
        }	
	$ls_sql=" ".
		"SELECT   * ".
		"FROM 	  soc_cuentagasto ".
		"WHERE 	  $ls_criterio_a ".
		"	  soc_cuentagasto.estcondat= '".$as_tipord."'  ".
		"ORDER BY soc_cuentagasto.numordcom,soc_cuentagasto.codestpro1,soc_cuentagasto.codestpro2,soc_cuentagasto.codestpro3,soc_cuentagasto.codestpro4,soc_cuentagasto.codestpro5,soc_cuentagasto.spg_cuenta ";	
	$rs_data_spg=$this->io_sql->select($ls_sql);
	
	if($rs_data_spg===false)
	{
		$this->io_mensajes->message("ERROR->uf_select_imputacion_cuentas_spg_orden_compra".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		$lb_valido=false;
	}
	return $rs_data_spg;
}	//end function uf_select_imputacion_cuentas_spg_orden_compra

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_listado_orden_compra($as_numordcomdes,$as_numordcomhas,$as_codprodes,$as_codprohas,
                                            $as_fecordcomdes,$as_fecordcomhas,$as_coduniadmdes,
                                            $as_coduniadmhas,$as_rdanucom,$as_rdemi,$as_rdpre,$as_rdcon,
                                            $as_rdanu,$as_rdinv,$as_artdes,$as_arthas,$as_serdes,$as_serhas,
								            $as_tipord,$as_tipo,&$lb_valido)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_listado_orden_compra
		//         Access: public
		//	    Arguments: as_numordcom   ---> Orden de Compra a imprimir
		//                 $as_tipord  ---> tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca los detalles de la  orden de compra para imprimir
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 16/07/2007									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		//$ab_valido = true;
		$lb_valido = true;
		$ls_criterio_a = "";
		$ls_criterio_b = "";
		$ls_criterio_c = "";
		$ls_criterio_d = "";
		$ls_criterio_e = "";
		$ls_criterio_f = "";
		$ls_criterio_g = "";
		$ls_criterio_h = "";
		$ls_cad        = "";
		$ls_cadena     = "";
		$ls_sql        = "";
		$ls_parentesis = "";
		if(  (($as_numordcomdes!="") && ($as_numordcomhas=="")) || (($as_numordcomdes=="") && ($as_numordcomhas!=""))  )
		{
		   $lb_valido = false;
		   $this->io_msg->message("Debe Completar el Rango de Busqueda por N�mero !!!");
		}
		else
		{
			if( ($as_numordcomdes!="") && ($as_numordcomhas!="") )
			{
			   $ls_criterio_a = "   numordcom >='".$as_numordcomdes."'  AND  numordcom <='".$as_numordcomhas."'    ";
			}
			else
			{
			   $ls_criterio_a ="";
			}
		}

		if(  (($as_codprodes!="") && ($as_codprohas=="")) || (($as_codprodes=="") && ($as_codprohas!=""))  )
		{
		   $lb_valido = false;
		   $this->io_msg->message("Debe Completar el Rango de Busqueda por Proveedor !!!");
		}
		else
		{
			if( ($as_codprodes!="") && ($as_codprohas!="") )
			{
			   if($ls_criterio_a=="")
			   {
					 $CA_AND="";   //CA = Criterio A
			   }
			   else
			   {
					 $CA_AND="  AND  ";
			   }
			   $ls_criterio_b  =  $ls_criterio_a.$CA_AND."  cod_pro   >='".$as_codprodes."'  AND  cod_pro   <='".$as_codprohas."'  ";
			}
			else
			{
			   $ls_criterio_b = $ls_criterio_a;
			}
		}


		if(  (($as_fecordcomdes!="") && ($as_fecordcomhas=="")) || (($as_fecordcomdes=="") && ($as_fecordcomhas!=""))  )
		{
		   $lb_valido = false;
		   $this->io_msg->message("Debe Completar el Rango de Busqueda por Fechas !!!");
		}
		else
		{
		   if( ($as_fecordcomdes!="") && ($as_fecordcomhas!="") )
		   {
			   $ls_fecha  = $this->io_funciones->uf_convertirdatetobd($as_fecordcomdes);
			   $as_fecordcomdes = $ls_fecha;

			   $ls_fechas  = $this->io_funciones->uf_convertirdatetobd($as_fecordcomhas);
			   $as_fecordcomhas  = $ls_fechas;

			   if($ls_criterio_b=="")
			   {
					 $CB_AND="";  //CB = Criterio B
			   }
			   else
			   {
					 $CB_AND="  AND  ";
			   }
			   $ls_criterio_c = $ls_criterio_b.$CB_AND."  fecordcom >='".$as_fecordcomdes."'  AND  fecordcom <='".$as_fecordcomhas."'  ";
			 }
		   else
		   {
				$ls_criterio_c = $ls_criterio_b;
		   }
		}

		if( ($as_rdanucom==0) && ($as_rdemi==0) && ($as_rdpre==0) && ($as_rdcon==0) && ($as_rdanu==0) && ($as_rdinv==0))
		{
			$ls_criterio_d = $ls_criterio_c;
		}
		else
		{
		   if($as_rdanucom!=0)
		   {
			  $ls_cadena=" (  estcom = 6 ";
		   }
		   else
		   {
			 $ls_cadena="";
		   }

		   if($as_rdemi!=0)
		   {
		   	  if($as_rdpre!=0)
			  {
				  if($ls_cadena!="")
				  {
					 $ls_cad=" OR   estcom = 1  ";
					 $ls_cadena=$ls_cadena.$ls_cad;
				  }
				  else
				  {
					  $ls_cadena=" (  estcom = 1 ";
				  }
			  }
			  else
			  {
				  if($ls_cadena!="")
				  {
					 $ls_cad=" OR   (estcom = 1 AND estapro=0) ";
					 $ls_cadena=$ls_cadena.$ls_cad;
				  }
				  else
				  {
					  $ls_cadena=" (  (estcom = 1 AND  estapro=0) ";
				  }
			  }
		   }
		   else
		   {
			 $ls_cadena=$ls_cadena;
		   }

		   if($as_rdpre!=0)
		   {
			  if($ls_cadena!="")
			  {
				 $ls_cad=" OR   (estcom = 1 AND estapro=1) ";
				 $ls_cadena=$ls_cadena.$ls_cad;
			  }
			  else
			  {
				 $ls_cadena=" (  (estcom = 1 AND  estapro=1)";
			  }
		   }
		   else
		   {
			 $ls_cadena=$ls_cadena;
		   }

		   if($as_rdcon!=0)
		   {
			  if($ls_cadena!="")
			  {
				 $ls_cad=" OR   estcom = 2  ";
				 $ls_cadena=$ls_cadena.$ls_cad;
			  }
			  else
			  {
				 $ls_cadena=" (  estcom = 2 ";
			  }
		   }
		   else
		   {
			 $ls_cadena=$ls_cadena;
		   }

		   if($as_rdanu!=0)
		   {
			  if($ls_cadena!="")
			  {
				 $ls_cad=" OR   estcom = 3  ";
				 $ls_cadena=$ls_cadena.$ls_cad;
			  }
			  else
			  {
				 $ls_cadena=" (  estcom = 3 ";
			  }
		   }
		   else
		   {
			 $ls_cadena=$ls_cadena;
		   }

		   if($as_rdinv!=0)
		   {
			  if($ls_cadena!="")
			  {
				 $ls_cad=" OR   estcom = 4  ";
				 $ls_cadena=$ls_cadena.$ls_cad;
			  }
			  else
			  {
				 $ls_cadena=" (  estcom = 4 ";
			  }
		   }
		   else
		   {
			   $ls_cadena=$ls_cadena;
		   }

		   $ls_parentesis="   )   ";

		   if(empty($ls_criterio_c))
		   {
			  $CC_AND=""; //CC = Criterio C
		   }
		   else
		   {
			  $CC_AND="   AND   ";
		   }
		   $ls_criterio_d=$ls_criterio_c.$CC_AND.$ls_cadena.$ls_parentesis;
	   }

		if(  (($as_coduniadmdes!="") && ($as_coduniadmhas=="")) || (($as_coduniadmdes=="") && ($as_coduniadmhas!=""))  )
		{
		   $lb_valido = false;
		   $this->io_msg->message("Debe Completar el Rango de Busqueda por Departamento !!!");
		}
		else
		{
			if(empty($ls_criterio_d))
			 {
				$CD_AND="";  //CD = Criterio D
			 }
			else
			 {
				$CD_AND="  AND  ";
			 }

			if( (($as_coduniadmdes!="") && ($as_coduniadmhas!="")) && (($as_numordcomdes!="") && ($as_numordcomhas!="")) )
			{
			   /*$ls_criterio_e  =  $ls_criterio_d.$CD_AND."  numordcom in (SELECT numordcom FROM soc_enlace_sep   ".
														 "                WHERE  numordcom >='".$as_numordcomdes."' AND numordcom<='".$as_numordcomhas."' AND ".
														 "                numordcom in (SELECT S.numsol FROM sep_solicitud S               ".
														 "                              WHERE  S.coduniadm >='".$as_coduniadmdes."'  AND  S.coduniadm <='".$as_coduniadmhas."' ".
														 "                              ) ".
														 "               )                ";
			*/
				$ls_criterio_e  =  $ls_criterio_d.$CD_AND." soc_ordencompra.coduniadm >='".$as_coduniadmdes."'  AND  soc_ordencompra.coduniadm <='".$as_coduniadmhas."'";
			}
			else
			{
			   if( (($as_coduniadmdes!="") && ($as_coduniadmhas!="")) && (($as_numordcomdes=="") && ($as_numordcomhas=="")) )
			   {
				 /* $ls_criterio_e  =  $ls_criterio_d.$CD_AND."  numordcom in (SELECT numordcom FROM soc_enlace_sep ".
															"                WHERE  numordcom in (SELECT S.numsol FROM sep_solicitud S  ".
															"                                     WHERE  S.coduniadm >='".$as_coduniadmdes."' AND S.coduniadm <='".$as_coduniadmhas."'".
															"                                    ) ".
															"               )                      ";*/
				$ls_criterio_e  =  $ls_criterio_d.$CD_AND." soc_ordencompra.coduniadm >='".$as_coduniadmdes."'  AND  soc_ordencompra.coduniadm <='".$as_coduniadmhas."'";
			   }
			   else
			   {
					if( ($as_coduniadmdes=="") && ($as_coduniadmhas=="") )
					{
						$ls_criterio_e = $ls_criterio_d;
					}
			   }
			}
		}

		if( ($as_tipo=="T") || ($as_tipo=="A") )
		{
			   //************************        Busqueda por Art�culo  ******************************
			   if(  (($as_artdes!="") && ($as_arthas=="")) || (($as_artdes=="") && ($as_arthas!=""))  )
				{
				   $lb_valido = false;
				   $this->io_msg->message("Debe Completar el Rango de Busqueda por Art�culo !!!");
				}
				else
				{
					if(empty($ls_criterio_e))
					 {
						$CE_AND="";  //CD = Criterio D
					 }
					else
					 {
						$CE_AND="  AND  ";
					 }
					 if(  ($as_artdes!="") && ($as_arthas!="")  )
					 {
						 $ls_criterio_f = $ls_criterio_e.$CE_AND."  numordcom in (SELECT numordcom                                             ".
																 "                FROM soc_dt_bienes                                           ".
																 "                WHERE codart >='".$as_artdes."' AND codart<='".$as_arthas."' ".
																 "                )                                                            ";
					 }
					 else
					 {
						 $ls_criterio_f = $ls_criterio_e;
					 }
				}
		}
		else
		{
		  $ls_criterio_f = $ls_criterio_e;
		}

		if( ($as_tipo=="T") || ($as_tipo=="S") )
		{
			   //************************        Busqueda por Servicios  ******************************
			   if(  (($as_serdes!="") && ($as_serhas=="")) || (($as_serdes=="") && ($as_serhas!=""))  )
				{
				   $lb_valido = false;
				   $this->io_msg->message("Debe Completar el Rango de Busqueda por Servicios !!!");
				}
				else
				{
					if(empty($ls_criterio_f))
					 {
						$CF_AND="";  //CD = Criterio D
					 }
					else
					 {
						$CF_AND="  AND  ";
					 }
					 if(  ($as_serdes!="") && ($as_serhas!="")  )
					 {
						 $ls_criterio_g = $ls_criterio_f.$CF_AND."  numordcom in (SELECT numordcom                                             ".
																 "                FROM soc_dt_servicio                                           ".
																 "                WHERE codser >='".$as_serdes."' AND codser<='".$as_serhas."' ".
																 "                )                                                            ";
					}
					else
					{
						$ls_criterio_g = $ls_criterio_f;
					}
				}
		}
		else
		{
		   $ls_criterio_g = $ls_criterio_f;
		}
		if( ($as_tipord=="A")  ||  ($as_tipord=="") )
		{
			 $ls_criterio_h = $ls_criterio_g;
		}
		else
		{
			 if(empty($ls_criterio_g))
			 {
				 $CG_AND=""; //CC = Criterio C
			 }
			 else
			 {
				$CG_AND="   AND   ";
			 }
			 if($as_tipord=="B")
			 {
				 $ls_criterio_h = $ls_criterio_g.$CG_AND." estcondat='B' ";
			 }
			 else
			 {
				 if($as_tipord=="S")
				 {
					 $ls_criterio_h = $ls_criterio_g.$CG_AND." estcondat='S' ";
				 }
			 }
		}
		if($ls_criterio_h!="")
		{
		   $ls_sql=" SELECT * FROM soc_ordencompra ".
				   " WHERE codemp='".$this->ls_codemp."'  AND ".$ls_criterio_h." ".
				   " ORDER BY numordcom ASC";
		}
		else
		{
		   $ls_sql=" SELECT * FROM soc_ordencompra ".
				   " WHERE codemp='".$this->ls_codemp."' ".
				   " ORDER BY numordcom ASC";
		}
		//print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_listado_orden_compra".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
        return $rs_data;
    }//fin de uf_select_listado_orden_compra
   //---------------------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------------
	function uf_select_nombre_proveedor($as_codpro)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_nombre_proveedor
		//		   Access: private
		//	    Arguments: as_codpro //codigo del proveedor
		//    Description: Function que devuelve la denominacion de la cuenta presupuestaria
		//	   Creado Por: Ing. Yozelin Barragan.
		// Fecha Creaci�n: 10/04/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=false;
		 $ls_sql=" SELECT   nompro ".
				 " FROM     rpc_proveedor ".
				 " WHERE    codemp='".$this->ls_codemp."'  AND  cod_pro ='".$as_codpro."' ";
		 $rs=$this->io_sql->select($ls_sql);
		 if ($rs===false)
		 {
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_nombre_proveedor ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		 }
		 else
		 {
			 if($row=$this->io_sql->fetch_row($rs))
			 {
				$as_nompro=$row["nompro"];
				$lb_valido=true;
			 }
			$this->io_sql->free_result($rs);
		 }
		 return $as_nompro;
	}//fin 	uf_select_nombre_proveedor
   //---------------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cotizacion_analisis($as_numanacot, $ls_tipanacot)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cotizacion_analisis
		//		   Access: public
		//		  return :	arreglo que contiene las cotizaciones que participaron en un determinado analisis
		//	  Description: Metodo que  devuelve las cotizaciones que participaron en un determinado analisis
		//	   Creado Por: Ing. Laura Cabr�
		// 			  Fecha: 14/06/2007								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_proveedores=array();
		$lb_valido=false;
		if($ls_tipanacot == "B")
			$ls_tabla = "soc_dtac_bienes";
		elseif($ls_tipanacot == "S")
			$ls_tabla = "soc_dtac_servicios";
		$ls_sql= "SELECT cxa.numcot, cxa.cod_pro,p.nompro,p.tipconpro
				  FROM soc_cotxanalisis cxa, rpc_proveedor p
				  WHERE cxa.codemp='$this->ls_codemp' AND cxa.numanacot='$as_numanacot'
				  AND cxa.codemp=p.codemp AND  cxa.cod_pro = p.cod_pro
				  AND cxa.numcot IN
				  (SELECT numcot FROM $ls_tabla WHERE codemp='$this->ls_codemp')";
		//print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_cotizacion_analisis".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))//Se verifica si la solicitud es de bienes o de servicios
			{
				$aa_proveedores[$li_i]=$row;
				$li_i++;
			}
		}
		return $aa_proveedores;
	}//fin de uf_select_cotizacion_analisis
	//---------------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_analisis($as_numcot,$as_codpro,&$as_forpagcom,&$as_diaentcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_analisis
		//		   Access: public
		//		  return :	arreglo que contiene las cotizaciones que participaron en un determinado analisis
		//	  Description: Metodo que  devuelve las cotizaciones que participaron en un determinado analisis
		//	   Creado Por: Ing. Laura Cabr�
		// 			  Fecha: 14/06/2007								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_forpagcom="";
		$as_diaentcom="";
		$ls_sql="SELECT forpagcom, diaentcom".
				"  FROM soc_cotizacion".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND numcot='".$as_numcot."' ".
				"   AND cod_pro='".$as_codpro."'";
		//print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_cotizacion_analisis".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$li_i=0;
			if($row=$this->io_sql->fetch_row($rs_data))//Se verifica si la solicitud es de bienes o de servicios
			{
				$as_forpagcom=$row["forpagcom"];
				$as_diaentcom=$row["diaentcom"];
			}
		}
		return $lb_valido;
	}//fin de uf_select_cotizacion_analisis
	//---------------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_calcular_montos($ai_totrow,$aa_items,&$aa_totales,$as_tipo_proveedor)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_calcular_montos
		//		   Access: public
		//		  return :	arreglo  montos totalizados
		//	  Description: Metodo que  devuelve arreglo  montos totalizados
		//	   Creado Por: Ing. Laura Cabr�
		// 			  Fecha: 09/08/2007								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$li_subtotal=0;
		 	$li_totaliva=0;
		 	$li_total=0;
		 	$aa_totales=array();
			for($li_j=1;$li_j<=$ai_totrow;$li_j++)
		 	{
				$li_subtotal+=(($aa_items[$li_j]["precio"]) * ($aa_items[$li_j]["cantidad"]));
				if($as_tipo_proveedor != "F") //En caso de que el roveedor sea formal no se le calculan los cargos
					$li_totaliva+=$aa_items[$li_j]["moniva"];
			}
			$li_total=$li_totaliva+$li_subtotal;
			$aa_totales["subtotal"]=$li_subtotal;
			$aa_totales["totaliva"]=$li_totaliva;
			$aa_totales["total"]=$li_total;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_items_proveedor($as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,&$aa_items,&$li_i)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_items
		//		   Access: public
		//		  return :	arreglo que contiene los items que participaron en un determinado analisis
		//	  Description: Metodo que  devuelve los items que participaron en un determinado analisis
		//	   Creado Por: Ing. Laura Cabr�
		// 			  Fecha: 10/06/2007								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$aa_items=array();
		$lb_valido=false;
		if($as_tipsolcot=="B")
		{
			$ls_sql="SELECT d.codart as codigo, a.denart as denominacion, p.nompro, dt.canart as cantidad, dt.preuniart as precio, dt.moniva,dt.montotart as monto,
					d.obsanacot, d.numcot, d.cod_pro
					FROM soc_dtac_bienes d,siv_articulo a, rpc_proveedor p,soc_dtcot_bienes dt
					WHERE
					d.codemp='$this->ls_codemp' AND d.numanacot='$as_numanacot' AND dt.cod_pro='$as_codpro' AND dt.numcot='$as_numcot'
					AND d.codemp=a.codemp AND a.codemp=p.codemp AND p.codemp=dt.codemp AND
					d.codart=a.codart AND d.cod_pro=p.cod_pro AND d.numcot=dt.numcot AND d.cod_pro=dt.cod_pro AND d.codart=dt.codart";
		}
		else
		{
				$ls_sql="SELECT d.codser as codigo, a.denser as denominacion, p.nompro, dt.canser as cantidad, dt.monuniser as precio, dt.moniva,dt.montotser as monto,
					d.obsanacot, d.numcot, d.cod_pro
					FROM soc_dtac_servicios d,soc_servicios a, rpc_proveedor p,soc_dtcot_servicio dt
					WHERE
					d.codemp='$this->ls_codemp' AND d.numanacot='$as_numanacot' AND d.codemp=a.codemp AND dt.cod_pro='$as_codpro' AND dt.numcot='$as_numcot'
					AND a.codemp=p.codemp AND p.codemp=dt.codemp AND
					d.codser=a.codser AND d.cod_pro=p.cod_pro AND d.numcot=dt.numcot AND d.cod_pro=dt.cod_pro AND d.codser=dt.codser";
		}
//		print $ls_sql."<br><br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->message("ERROR->".$this->io_funciones->uf_convertirmsg($io_sql->message));
			print $io_mensajes->message("ERROR->".$this->io_funciones->uf_convertirmsg($io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))//Se verifica si la solicitud es de bienes o de servicios
			{
				$li_i++;
				$aa_items[$li_i]=$row;
			}
		}
		return $aa_items;
	}

//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_items_solicitud($as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,&$aa_items,&$li_i)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_items_solicitud
		//		   Access: public
		//		  return :	arreglo que contiene los items que participaron en un determinado analisis
		//	  Description: Metodo que  devuelve los items que participaron en un determinado analisis
		//	   Creado Por: Ing. Laura Cabr�
		// 			  Fecha: 10/06/2007								Fecha �ltima Modificaci�n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($as_tipsolcot=="B")
		{
			$ls_sql="SELECT soc_dtcot_bienes.codart as codigo, siv_articulo.denart as denominacion,soc_dtcot_bienes.canart as cantidad, ".
					"       soc_dtcot_bienes.preuniart as precio,soc_dtcot_bienes.moniva,soc_dtcot_bienes.montotart as monto".
					"  FROM siv_articulo, rpc_proveedor,soc_dtcot_bienes".
					" WHERE	soc_dtcot_bienes.codemp='".$this->ls_codemp."'".
					"   AND soc_dtcot_bienes.cod_pro='".$as_codpro."'".
					"   AND soc_dtcot_bienes.numcot='".$as_numcot."'".
					"   AND soc_dtcot_bienes.codemp=siv_articulo.codemp".
					"   AND soc_dtcot_bienes.codart=siv_articulo.codart".
					"   AND soc_dtcot_bienes.codemp=rpc_proveedor.codemp".
					"   AND soc_dtcot_bienes.cod_pro=rpc_proveedor.cod_pro";
		}
		else
		{
				$ls_sql="SELECT d.codser as codigo, a.denser as denominacion, p.nompro, dt.canser as cantidad, dt.monuniser as precio, dt.moniva,dt.montotser as monto,
					d.obsanacot, d.numcot, d.cod_pro
					FROM soc_dtac_servicios d,soc_servicios a, rpc_proveedor p,soc_dtcot_servicio dt
					WHERE
					d.codemp='$this->ls_codemp' AND d.numanacot='$as_numanacot' AND d.codemp=a.codemp AND dt.cod_pro='$as_codpro' AND dt.numcot='$as_numcot'
					AND a.codemp=p.codemp AND p.codemp=dt.codemp AND
					d.codser=a.codser AND d.cod_pro=p.cod_pro AND d.numcot=dt.numcot AND d.cod_pro=dt.cod_pro AND d.codser=dt.codser";
		}
		//print $ls_sql."<br><br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->message("ERROR->".$this->io_funciones->uf_convertirmsg($io_sql->message));
			print $io_mensajes->message("ERROR->".$this->io_funciones->uf_convertirmsg($io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$aa_items->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}

    function uf_select_denominacion($as_tabla,$as_campo,$as_where)
	{
	 //////////////////////////////////////////////////////////////////////////////
	 //	Funcion      uf_select_denominacion
	 //	Access       public
	 //	Arguments    $as_codmon
	 //	Returns      $rs (Resulset)
	 //	Description  Variable string con la denominacion de la moneda
	 //////////////////////////////////////////////////////////////////////////////
		$ls_denrow="";
		 $ls_sql  ="SELECT $as_campo as denrow FROM $as_tabla $as_where";
	 	 $rs_data = $this->io_sql->select($ls_sql);
		 if ($rs_data===false)
		    {print $this->io_sql->message;
			  $lb_valido=false;
			  $this->io_mensajes->message("ERROR: CLASS=sigesp_soc_class_report.php; Metodo=uf_select_denominacion;".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		    }
		 else
		    {
		      if ($row=$this->io_sql->fetch_row($rs_data))
			     {
			       $ls_denrow = $row["denrow"];
			     }
		    }
	     return $ls_denrow;
	}

	function uf_load_nombre_usuario()
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function:  uf_load_nombre_usuario
		//		   Access:  public
		//		 Argument:
		//	  Description:  Funci�n que obtiene el nombre completo del usuario que imprime el documento
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creaci�n:  22/10/2007								Fecha �ltima Modificaci�n:
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_nombre="";
		$ls_sql="SELECT nomusu,apeusu".
			  	"  FROM sss_usuarios".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codusu='".$this->ls_codusu."'";
		$rs_data= $this->io_sql->select($ls_sql);//print $ls_sql;
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_class_report.php->M�TODO->uf_load_nombre_usuario.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_nombre= $row["nomusu"]." ".$row["apeusu"];
			}
		}
	  return $ls_nombre;
	}// end function uf_load_nombre_usuario
//--------------------------------------------------------------------------------------------------------------------
  function uf_select_unidadtributaria(&$ls_valoruni)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_unidadtributaria
		//         Access: public
		//	    Arguments: as_numordcom    ---> Orden de Compra a imprimir
		//                 $as_estcondat  ---< tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True � False.
		//    Description: funci�n que busca el valor actual de la uniadad tributaria
		//	   Creado Por: Ing. Gloriely Fr�itez
		// Fecha Creaci�n: 21/07/2008									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql=" SELECT sigesp_empresa.periodo,sigesp_unidad_tributaria.codunitri,sigesp_unidad_tributaria.fecentvig , ".
                " sigesp_unidad_tributaria.gacofi,sigesp_unidad_tributaria.fecpubgac,sigesp_unidad_tributaria.decnro,  ".
                " sigesp_unidad_tributaria.fecdec,sigesp_unidad_tributaria.valunitri ".
				" FROM sigesp_unidad_tributaria,sigesp_empresa ".
				" WHERE sigesp_empresa.codemp='".$this->ls_codemp."' ".
				"  AND sigesp_unidad_tributaria.fecentvig>=sigesp_empresa.periodo";
		$rs_datos_uni=$this->io_sql->select($ls_sql);
		if($rs_datos_uni===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_unidadtributaria ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		 else
		 {
			 if($row=$this->io_sql->fetch_row($rs_datos_uni))
			 {
				$ls_codartser=$row["fecentvig"];
				$ls_valoruni=$row["valunitri"];
				$lb_valido=true;
			 }
			$this->io_sql->free_result($rs_datos_uni);
		 }
		 return $lb_valido;
	}// end function uf_select_unidadtributaria
//--------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------
	function uf_load_modalidades_clausulas($as_codtipmod,&$aa_clausulas)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function:  uf_load_nombre_usuario
		//		   Access:  public
		//		 Argument:
		//	  Description:  Funci�n que obtiene el nombre completo del usuario que imprime el documento
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creaci�n:  22/10/2007								Fecha �ltima Modificaci�n:
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_nombre="";
		$ls_sql="SELECT soc_clausulas.dencla".
			  	"  FROM soc_modalidadclausulas,soc_clausulas,soc_dtm_clausulas".
				" WHERE soc_modalidadclausulas.codemp='".$this->ls_codemp."'".
				"   AND soc_modalidadclausulas.codtipmod='".$as_codtipmod."'".
				"   AND soc_modalidadclausulas.codemp=soc_dtm_clausulas.codemp".
				"	AND soc_modalidadclausulas.codtipmod=soc_dtm_clausulas.codtipmod".
				"   AND soc_dtm_clausulas.codemp=soc_clausulas.codemp".
				"   AND soc_dtm_clausulas.codcla=soc_clausulas.codcla";
		$rs_data= $this->io_sql->select($ls_sql);//print $ls_sql;
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_class_report.php->M�TODO->uf_load_nombre_usuario.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_i=$li_i+1;
				$aa_clausulas[$li_i]= $row["dencla"];
			}
		}
	  return $ls_nombre;
	}// end function uf_load_nombre_usuario
//--------------------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_descuentos($as_numordcom,$as_estcondat)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_cuenta_gasto
		//         Access: public
		//	    Arguments: as_numordcom    ---> Orden de Compra a imprimir
		//                 $as_estcondat  ---< tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca las cuenats de gastos de la  orden de compra para imprimir
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 21/06/2007									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ds_soc_desc = new class_datastore();
		$ls_sql=" SELECT monto										".
				" FROM   soc_oc_deducciones							".
				" WHERE  codemp='".$this->ls_codemp."'  AND         ".
				"        numordcom='".$as_numordcom."'  AND         ".
				"        estcondat='".$as_estcondat."'              ";
		$rs_data=$this->io_sql->select($ls_sql);
		$li_numrows=$this->io_sql->num_rows($rs_data);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_descuentos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_soc_desc->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_cuenta_gasto
//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_oc($as_numordcomdes,$as_numordcomhas,$as_codprodes,$as_codprohas,$as_estcondat,&$lb_valido)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_oc
		//         Access: public
		//	    Arguments: as_numordcom    ---> Orden de Compra a imprimir
		//                 $as_estcondat  ---< tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca las cuenats de gastos de la  orden de compra para imprimir
		//	   Creado Por:
		// Fecha Creaci�n:          									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if($as_estcondat=="A")
		{
			$as_estcondat="";
		}
		if($as_numordcomdes!="")
		{
			$ls_criterio=$ls_criterio." AND soc_ordencompra.numordcom>='".$as_numordcomdes."'";
		}
		if($as_numordcomhas!="")
		{
			$ls_criterio=$ls_criterio." AND soc_ordencompra.numordcom<='".$as_numordcomhas."'";
		}
		if($as_codprodes!="")
		{
			$ls_criterio=$ls_criterio." AND soc_ordencompra.cod_pro>='".$as_codprodes."'";
		}
		if($as_codprohas!="")
		{
			$ls_criterio=$ls_criterio." AND soc_ordencompra.cod_pro<='".$as_codprohas."'";
		}
		$ls_sql=" SELECT soc_ordencompra.numordcom,soc_ordencompra.estcondat,soc_ordencompra.estcom,soc_ordencompra.fecordcom,soc_ordencompra.cod_pro,".
                "        (SELECT rpc_proveedor.nompro ".
                "         FROM   rpc_proveedor ".
                "         WHERE  rpc_proveedor.codemp=soc_ordencompra.codemp AND ".
                "                rpc_proveedor.cod_pro=soc_ordencompra.cod_pro) AS nompro ".
				"  FROM soc_ordencompra".
				" WHERE  codemp='".$this->ls_codemp."'".
				$ls_criterio.
				"   AND estcondat like'%".$as_estcondat."%'".
				"   AND numordcom<>'000000000000000'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_oc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_cuenta_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_ubicacion($as_numordcom,$as_codpro,$as_procede,&$lb_valido)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_ubicacion
		//         Access: public
		//	    Arguments: as_numordcom    ---> Orden de Compra a imprimir
		//                 $as_estcondat  ---> tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca las cuenats de gastos de la  orden de compra para imprimir
		//	   Creado Por:
		// Fecha Creaci�n:          									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql=" SELECT cxp_rd_spg.numrecdoc AS documento,cxp_rd.estprodoc AS estatus, 'RD' AS origen  ".
				"  FROM cxp_rd_spg, cxp_rd ".
				" WHERE cxp_rd_spg.codemp='".$this->ls_codemp."'".
				"   AND cxp_rd_spg.numdoccom='".$as_numordcom."'".
				"   AND cxp_rd_spg.cod_pro='".$as_codpro."'".
				"   AND cxp_rd_spg.procede_doc='SOCCOC'".
				"   AND cxp_rd_spg.codemp= cxp_rd.codemp".
				"   AND cxp_rd_spg.numrecdoc= cxp_rd.numrecdoc".
				"   AND cxp_rd_spg.codtipdoc= cxp_rd.codtipdoc".
				"   AND cxp_rd_spg.cod_pro= cxp_rd.cod_pro".
				"   AND cxp_rd_spg.ced_bene= cxp_rd.ced_bene".
				" GROUP BY cxp_rd_spg.numrecdoc,cxp_rd.estprodoc ".
				"UNION ".
				"SELECT numconrec AS documento, 'RECIBIDA' AS estatus, 'IN' AS origen".
				"  FROM siv_recepcion".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND numordcom='".$as_numordcom."'".
				"   AND cod_pro='".$as_codpro."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_load_ubicacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_cuenta_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//---------------------------------------------------------------------------------------------------------------------------------------     
    function uf_unidades_administrativas($as_numord) 
    { 
         
        $lb_valido=false; 
        $unidades = ''; 
        $ls_sql=" SELECT DISTINCT(ua.denuniadm) ". 
                "   FROM soc_enlace_sep es ". 
                "  INNER JOIN spg_unidadadministrativa ua ". 
                "     ON es.coduniadm = ua.coduniadm ". 
                "    AND es.codemp = ua.codemp ".                  
                "  WHERE  es.numordcom='".$as_numord."' ";  
        $rs_data=$this->io_sql->select($ls_sql); 
        if ($rs_data===false) 
        { 
            $lb_valido=false; 
            $this->io_mensajes->message("CLASE->Report M�TODO->uf_unidades_administrativas ERROR->".$this->io_sql->message); 
        }         
        else 
        { 
            $li_i=0; 
            while(!$rs_data->EOF) 
            { 
                $unidades = $rs_data->fields["denuniadm"].'  '.$unidades;                     
                $rs_data->MoveNext(); 
            }      
            $this->io_sql->free_result($rs_data); 
        }  
        return $unidades;     
    }//fin     uf_unidades_administrativas 
    //--------------------------------------------------------------------------------------------------------------------------------- 
//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_partidas($as_numordcomdes,$as_numordcomhas,$as_fecdes,$as_fechas,$as_estcondat,&$lb_valido)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_oc
		//         Access: public
		//	    Arguments: as_numordcom    ---> Orden de Compra a imprimir
		//                 $as_estcondat  ---< tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca las cuenats de gastos de la  orden de compra para imprimir
		//	   Creado Por:
		// Fecha Creaci�n:          									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$as_fecdes = $this->io_funciones->uf_convertirdatetobd($as_fecdes);
		$as_fechas = $this->io_funciones->uf_convertirdatetobd($as_fechas);
		$lb_valido=true;
		$ls_criterio="";
		if($as_estcondat=="A")
		{
			$as_estcondat="";
		}
		if($as_numordcomdes!="")
		{
			$ls_criterio=$ls_criterio." AND soc_ordencompra.numordcom>='".$as_numordcomdes."'";
		}
		if($as_numordcomhas!="")
		{
			$ls_criterio=$ls_criterio." AND soc_ordencompra.numordcom<='".$as_numordcomhas."'";
		}
		if($as_fecdes!="")
		{
			$ls_criterio=$ls_criterio." AND soc_ordencompra.fecordcom>='".$as_fecdes."'";
		}
		if($as_fechas!="")
		{
			$ls_criterio=$ls_criterio." AND soc_ordencompra.fecordcom<='".$as_fechas."'";
		}
		if($as_estcondat!="")
		{
			$ls_criterio=$ls_criterio." AND soc_ordencompra.estcondat='".$as_estcondat."'";
		}
		$ls_sql="SELECT MAX(soc_cuentagasto.numordcom) AS numordcom,SUBSTR(spg_cuenta,1,3) AS partida, SUM(soc_cuentagasto.monto) AS monto,".
				"       MAX(soc_ordencompra.fecordcom) AS fecordcom,MAX(soc_ordencompra.estcondat) AS estcondat,".
				"       MAX(soc_ordencompra.obscom) AS obscom,MAX(soc_ordencompra.cod_pro) AS cod_pro".
				"  FROM soc_ordencompra,soc_cuentagasto".
				" WHERE soc_ordencompra.codemp='".$this->ls_codemp."'".
				"   AND soc_ordencompra.numordcom<>'000000000000000'".
				$ls_criterio.
				"   AND soc_ordencompra.codemp=soc_cuentagasto.codemp".
				"   AND soc_ordencompra.numordcom=soc_cuentagasto.numordcom".
				"   AND soc_ordencompra.estcondat=soc_cuentagasto.estcondat".
				" GROUP BY soc_cuentagasto.numordcom,partida,soc_ordencompra.estcondat".
				" ORDER BY soc_ordencompra.estcondat,soc_cuentagasto.numordcom,partida";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_oc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_cuenta_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_facturas($as_numordcom,$as_codpro,&$lb_valido)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_ubicacion
		//         Access: public
		//	    Arguments: as_numordcom    ---> Orden de Compra a imprimir
		//                 $as_estcondat  ---> tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca las cuenats de gastos de la  orden de compra para imprimir
		//	   Creado Por:
		// Fecha Creaci�n:          									Fecha �ltima Modificaci�n :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql=" SELECT cxp_rd_spg.numrecdoc AS documento,cxp_rd.estprodoc AS estatus,MAX(cxp_rd.fecemidoc) AS fecha".
				"  FROM cxp_rd_spg, cxp_rd ".
				" WHERE cxp_rd_spg.codemp='".$this->ls_codemp."'".
				"   AND cxp_rd_spg.numdoccom='".$as_numordcom."'".
				"   AND cxp_rd_spg.cod_pro='".$as_codpro."'".
				"   AND cxp_rd_spg.procede_doc='SOCCOC'".
				"   AND cxp_rd_spg.codemp= cxp_rd.codemp".
				"   AND cxp_rd_spg.numrecdoc= cxp_rd.numrecdoc".
				"   AND cxp_rd_spg.codtipdoc= cxp_rd.codtipdoc".
				"   AND cxp_rd_spg.cod_pro= cxp_rd.cod_pro".
				"   AND cxp_rd_spg.ced_bene= cxp_rd.ced_bene".
				" GROUP BY cxp_rd_spg.numrecdoc,cxp_rd.estprodoc";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_load_ubicacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_cuenta_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------
	


}//FIN DE LA CLASE.
?>