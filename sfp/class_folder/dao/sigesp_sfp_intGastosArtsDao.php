<?php
require_once("../class_folder/sigesp_conexion_dao.php");
require_once("sigesp_sfp_intGastosFuenDao.php");
class intArtsDao extends ADOdb_Active_Record
{
	var $_table='spe_int_arts';
	public function Incluir()
	{
		try
		{
			global $db;
	//		$db->debug=1;
			$res =$this->save();
			if($res)
			{
				return "1";
			}
			else
			{
				return "0";
			}
		}
		catch (Exception $e) 
		{
    		return "0";
		}


	}
	
	public function Eliminar()
	{
		try
		{
			global $db;
	//		$db->debug=1;
			$sql="delete from {$this->_table} where codemp='{$this->codemp}' and codinte='{$this->codinte}' 
					and sig_cuenta_gas='{$this->sig_cuenta_gas}' 
					and ano_presupuesto='{$this->ano_presupuesto}'";
			$res =$db->Execute($sql);		
			if($res)
			{
				
				
				return "1";
			}
			else
			{
				return "0";
			}
		}
		catch (Exception $e) 
		{
    		return "0";
		}


	}
	
	public function leerIngresosGastos()
	{
		global $db;
		//$db->debug=1;
		$sql="select sig_cuenta_ing,montoasig from {$this->_table} where codemp='{$this->codemp}' and codinte='{$this->codinte}' 
			  and sig_cuenta_gas='{$this->sig_cuenta_gas}' 
			  and ano_presupuesto='{$this->ano_presupuesto}'";
		$rs=$db->Execute($sql);	
		return $rs;	
	}
}
?>