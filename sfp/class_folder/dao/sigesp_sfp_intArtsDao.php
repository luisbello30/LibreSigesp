<?php
require_once("../class_folder/sigesp_conexion_dao.php");
class intArtsdDao extends ADOdb_Active_Record
{
	var $_table='spe_int_arts';
	public function Incluir()
	{
		try
		{
			global $db;
			$this->save();
			//return "1";
		}
		catch (Exception $e) 
		{
			//mandar a un archivo de logs con los eventos fallidos fallidos	
    		return "0";
		}
	}
	
	public function Eliminar()
	{
		global $db;
	//	$db->debug = true;
		$db->StartTrans();
		$this->delete();
		if($db->CompleteTrans())
		{
			return "1";
		}
		else
		{
			return "0";	
		}
	}
	
	
	public function BuscarUnidades($integracion)
	{
		global $db;
		//$db->debug=true;
		$sql = "select spe_inte_unadmin.coduac,sfp_estructura_ad.denuac,sfp_estructura_ad.nivel 
				from spe_inte_unadmin inner join sfp_estructura_ad on spe_inte_unadmin.coduac=sfp_estructura_ad.coduac 
				and spe_inte_unadmin.nivel=sfp_estructura_ad.nivel and spe_inte_unadmin.codemp=sfp_estructura_ad.codemp 
				where spe_inte_unadmin.codinte ={$integracion} and {$this->_table}.codemp='{$this->codemp}'";
		$Rs = $db->Execute($sql); 
		return $Rs;
	}
	
	public function LeerTodos()
	{
		global $db;
		$Rs = $this->Find("cod_fuenfin<>''");
		return $Rs;
		
	}
	
	public function Leerdtunidades()
	{
		global $db;
		$sql = "select spe_inte_unadmin.coduac as coduniadm,spe_inte_unadmin.codemp,
				codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla
				from spe_inte_unadmin inner join spe_relacion_es on 
				spe_inte_unadmin.codinte=spe_relacion_es.codinte";
		
		$Rs = $db->Execute($sql); 
		if($Rs!=false)
		{
			return $Rs;
		}
		else
		{
			return false;			
		}
	}
	
	public function LeerPorCadena($cr,$cad)
	{
		global $db;
		$Rs = $this->Find("{$cr} like  '%{$cad}%' ");
		return $Rs;
	}
	
}

?>