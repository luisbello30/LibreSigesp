<?php
class spgcuentasDao extends ADODB_Active_Record
{		
	var $_table="spg_cuentas";
	public function IniciarTran($db)
	{
		//$db->debug=true;
		$db->StartTrans();
	}
	
	public function CompletarTran($db)
	{
		if($db->CompleteTrans())
		{
			return "1";
		}	
		else
		{
			return "0";
		}
	}
	
	public function Incluir($db)
	{
		try
		{
			//$db->debug=true;
			if($this->save())
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
			//mandar a un archivo de logs con los eventos fallidos fallidos	
    		return "0";
		}
	}	
	public function leerEstructuras($db,$cuenta)
	{
		$sql="select codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla, 
			COALESCE(sum(enero+febrero+marzo
			+abril+mayo+junio+julio+agosto+septiembre
			+octubre+noviembre+diciembre),000) 
			as asignado,sum(enero),sum(febrero),sum(marzo),
			sum(abril),sum(mayo),sum(junio),sum(julio),sum(agosto),sum(septiembre)
			,sum(octubre),sum(noviembre),sum(diciembre),sum(precomprometido),sum(comprometido),
			sum(causado),sum(pagado),sum(distribuir) 
			from spg_cuentas
			where codestpro1='{$rs->fields["codestpro1"]}'
			and codestpro2='{$rs->fields["codestpro2"]}'
			and codestpro3='{$rs->fields["codestpro3"]}'
			and codestpro4='{$rs->fields["codestpro4"]}'
			and codestpro5='{$rs->fields["codestpro5"]}'
			and estcla='{$rs->fields["estcla"]}'
			and spg_cuenta like '{$cuenta}%'
			group by codestpro1,codestpro2,
			codestpro3,codestpro4,codestpro5,estcla";
		$rs = $db->Execute($sql);
		if($rs!=false)
		{
			return $rs;
		}	
		else
		{
			return false;
		}
	}
	
}
?>