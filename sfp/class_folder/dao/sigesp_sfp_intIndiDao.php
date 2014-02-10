<?php
require_once("../class_folder/sigesp_conexion_dao.php");
class intIndiDao extends ADOdb_Active_Record
{
	var $_table='spe_relacion_estindi';
	
	public function Modificar()
	{
		global $db;
		$db->StartTrans();
		$this->Replace();
		$db->CompleteTrans();
		return "1";
		
	}
	
	public function Eliminar()
	{
		global $db;
		//$db->debug=true;
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
	
	
	public function Incluir()
	{
		try
		{
			//var_dump($this);
			//die();
			global $db;
			//$db->debug=true;
			$db->StartTrans();
			$this->save();
			if($db->CompleteTrans())
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
	
	public function BuscarIndicadores($integracion,$codemp)
	{
		global $db;
	   
	   $sql = "select {$this->_table}.ano_presupuesto, {$this->_table}.codinte, {$this->_table}.cod_ind, {$this->_table}.codemp,
				COALESCE({$this->_table}.enero+{$this->_table}.febrero+{$this->_table}.marzo+{$this->_table}.abril+{$this->_table}.mayo+{$this->_table}.junio+
				{$this->_table}.julio+{$this->_table}.agosto+{$this->_table}.septiembre+{$this->_table}.octubre+{$this->_table}.noviembre+
				{$this->_table}.diciembre,0.00) as montototal,{$this->_table}.monto,
				{$this->_table}.enero,{$this->_table}.febrero,{$this->_table}.marzo,{$this->_table}.abril,{$this->_table}.mayo,{$this->_table}.junio,{$this->_table}.julio,
				{$this->_table}.agosto,{$this->_table}.septiembre,{$this->_table}.octubre,{$this->_table}.noviembre,{$this->_table}.diciembre, {$this->_table}.formula, sig_indicador.denominacion from {$this->_table} 
		        inner join sig_indicador on {$this->_table}.cod_ind=sig_indicador.cod_ind and spe_relacion_estindi.codemp=sig_indicador.codemp
				inner join spe_relacion_es on {$this->_table}.codinte=spe_relacion_es.codinte and {$this->_table}.codemp=spe_relacion_es.codemp where {$this->_table}.codinte ={$integracion} and {$this->_table}.codemp = '{$codemp}'";
	   
		/*$sql = "select {$this->_table}.*, sig_indicador.denominacion from {$this->_table} 
		        inner join sig_indicador on {$this->_table}.cod_ind=sig_indicador.cod_ind and spe_relacion_estindi.codemp=sig_indicador.codemp
				inner join spe_relacion_es on {$this->_table}.codinte=spe_relacion_es.codinte and {$this->_table}.codemp=spe_relacion_es.codemp where {$this->_table}.codinte ={$integracion} and {$this->_table}.codemp = {$codemp}";*/
		
		//ver($sql);
		$Rs = $db->Execute($sql); 
		return $Rs;
	}

}

?>