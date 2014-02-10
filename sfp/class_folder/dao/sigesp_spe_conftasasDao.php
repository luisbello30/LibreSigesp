<?php
require_once("../class_folder/sigesp_conexion_dao.php");
class ConfTasas extends ADOdb_Active_Record
{
	var $_table='spe_conf_tasas';
	
	public function LeerPorCadena($cr,$cad)
	{
		global $db;
		$Rs = $db->Execute("select * from {$this->_table} where  {$cr} like  '{$cad}%'");
		return $Rs;
	}
	
	public function LeerTodas($cr,$cad)
	{
		global $db;
		$sql="select {$this->_table}.*,sigesp_sfp_plancuentas.sig_cuenta as codigo,
			sigesp_sfp_plancuentas.denominacion from {$this->_table}
			inner join sigesp_sfp_plancuentas on 
			{$this->_table}.codemp=sigesp_sfp_plancuentas.codemp
			and {$this->_table}.ano_presupuesto=sigesp_sfp_plancuentas.ano_presupuesto
			and {$this->_table}.cuenta=sigesp_sfp_plancuentas.sig_cuenta
			where {$this->_table}.codemp='{$this->codemp}'
			and {$this->_table}.ano_presupuesto={$this->ano_presupuesto}
			and {$this->_table}.tipotasa='{$this->tipotasa}'
			and sigesp_sfp_plancuentas.{$cr} like '{$cad}%'
			";
		$rs=$db->Execute($sql);
		return $rs;	
	}
	
	
	public function LeerTodas2()
	{
		global $db;
		$sql="select {$this->_table}.* from {$this->_table}
			where {$this->_table}.codemp='{$this->codemp}'
			and {$this->_table}.ano_presupuesto={$this->ano_presupuesto}
			and {$this->_table}.tipotasa='{$this->tipotasa}'
			and {$this->_table}.cuenta = '{$this->cuenta}'";
		$rs=$db->Execute($sql);
		return $rs;	
	}
	
	
	public function LeerUna()
	{
		global $db;
		$sql="select * from {$this->_table}
			where codemp='{$this->codemp}'
			and ano_presupuesto={$this->ano_presupuesto}
			and tipotasa='{$this->tipotasa}'
			and cuenta='{$this->cuenta}'";
		$rs=$db->Execute($sql);
		return $rs;	
	}
	
	
	
	
	
	public function IniciarTran()
	{
		global $db;
		//$db->debug=true;
		$db->StartTrans();
	} 
	public function CompletarTran()
	{
		global $db;
		if($db->CompleteTrans())
		{
			return "1";
		}	
		else
		{
			return "0";
		}
	}	
	
	public function incluir()
	{
		if($this->save())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	public function actualizar()
	{
		if($this->replace())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function buscarCuenta()	
	{
		global $db;
		$Rs = $db->Execute("select * from sigesp_sfp_saldoscon where sc_cuenta=$this->sc_cuenta");
		if ($Rs->RecordCount()>0)
		{
			return true;			
		}
		else
		{
			return false;	
		}	
	}
	public function LeerSaldoInicial()	
	{
		global $db;
		//$db->debug=true;
		$Rs = $db->Execute("select sum(monto_anreal) as saldoinicialpasin, sum(monto_anest) as saldoinicialtri1  from sigesp_sfp_saldoscon where sc_cuenta like '{$this->sc_cuenta}%' and ano_presupuesto={$this->ano_presupuesto} and codemp='{$this->codemp}'");
		if ($Rs->RecordCount()>0)
		{
			return $Rs;			
		}
		else
		{
			return false;	
		}	
	}
	
}
?>