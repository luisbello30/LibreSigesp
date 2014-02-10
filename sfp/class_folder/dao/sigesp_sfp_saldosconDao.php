<?php
require_once("../class_folder/sigesp_conexion_dao.php");
class SaldosCont extends ADOdb_Active_Record
{
	var $_table='sigesp_sfp_saldoscon';
	
	public function LeerPorCadena($cr,$cad)
	{
		global $db;
		$Rs = $db->Execute("select * from {$this->_table} where  {$cr} like  '{$cad}%'");
		return $Rs;
		
	}
	
	public function LeerTodas($codemp="0001")
	{
		global $db;
		$digactivo="1";
		$digpasivo="2";
		$digresultado="3";
		$digcapital="3";
		//$db->debug=true;
		/*$sql="select * from sigesp_plan_unico where 
			  sigesp_plan_unico.sc_cuenta like 
			 '1%' or sigesp_plan_unico.sc_cuenta like '2%' 
			 or sigesp_plan_unico.sc_cuenta like '3%'
			 order by sc_cuenta asc"; */
		$resultado = $this->leerDigitoCuentasBalanceGeneral($codemp);
		if($resultado)
		{
		 $digactivo=$resultado->fields["activo"];
		 $digpasivo=$resultado->fields["pasivo"];
		 $digresultado=$resultado->fields["resultado"];
		 $digcapital=$resultado->fields["capital"];
		}
		
		$sql="select * from sigesp_plan_unico where 
			  sigesp_plan_unico.sc_cuenta like 
			 '".$digactivo."%' or sigesp_plan_unico.sc_cuenta like '".$digpasivo."%' 
			 or sigesp_plan_unico.sc_cuenta like '".$digresultado."%' or sigesp_plan_unico.sc_cuenta like '".$digcapital."%'
			 order by sc_cuenta asc";
		
		$Rs = $db->Execute($sql);
		$i=0;
		while(!$Rs->EOF)
		{
			$AuxCuenta = trim(uf_spg_cuenta_sin_cero($Rs->fields["sc_cuenta"]));
			
			$sql="select coalesce(sum(sigesp_sfp_saldoscon.monto_anreal),000) as anreal,
				  coalesce(sum(sigesp_sfp_saldoscon.monto_anest),000) as anest
				  from sigesp_sfp_saldoscon where sc_cuenta like '{$AuxCuenta}%' and codemp='{$this->codemp}' and ano_presupuesto='{$this->ano_presupuesto}'";
			$Rs2 = $db->Execute($sql);
			$arcuentas[$i] = array("sc_cuenta"=>utf8_encode($Rs->fields["sc_cuenta"]),"denominacion"=>utf8_encode($Rs->fields["denominacion"]),"monto_anreal"=>number_format($Rs2->fields["anreal"],2,",","."),"monto_anest"=>number_format($Rs2->fields["anest"],2,",","."));
			$i++;
			$Rs->MoveNext();
		}			
		return $arcuentas;
	}
	
	public function incluir()
	{
		global $db;
		$db->StartTrans();
		$this->save();
		$db->CompleteTrans();
		return "1";	
	}
	
	public function actualizar()
	{
		global $db;
		$db->StartTrans();
		$this->replace();
		$db->CompleteTrans();
		return "1";
	}
	
	public function buscarCuenta()	
	{
		global $db;
		$Rs = $db->Execute("select * from sigesp_sfp_saldoscon where sc_cuenta=$this->sc_cuenta and codemp='{$this->codemp}' and ano_presupuesto='{$this->ano_presupuesto}'");
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
	
	public function leerDigitoCuentasBalanceGeneral($codemp="0001")
	{
	 	global $db;
		$ls_sql = "SELECT activo, pasivo, resultado, capital FROM sigesp_empresa WHERE codemp = '".$codemp."'";
		$resulset = $db->Execute($ls_sql);
		if ($resulset->RecordCount()>0)
		{
			return $resulset;			
		}
		else
		{
			return false;	
		}	
	}
	
}
?>