<?php
require_once ("../class_folder/sigesp_conexion_dao.php");
class servicios extends ADOdb_Active_Record
{
    var $_table = 'soc_servicios';
    public function LeerPorCadenaCuenta($cr, $cad)
    {
       global $db;
       //$db->debug = true;
       $sql = "select soc_servicios.*,soc_servicios.codser
       		 as codigo,soc_servicios.denser
       		 as denominacion,soc_servicios.preser
       		 as precio from soc_servicios
			   inner join siv_unidadmedida on
			   soc_servicios.codunimed=siv_unidadmedida.codunimed	
       		  where upper({$cr}) like upper('%{$cad}%')
       		  and spg_cuenta=trim('{$this->cuenta}')";
       $Rs = $db->Execute($sql);
       return $Rs;
    }    
    public function LeerCargos()
    {
       global $db;
      // $db->debug = true;
       $sql = "select max(soc_serviciocargo.codser),sigesp_cargos.porcar
       		  from soc_serviciocargo inner join sigesp_cargos 
       		  on soc_serviciocargo.codcar=sigesp_cargos.codcar 
       		  where codser='{$this->codser}'
       		  group by sigesp_cargos.porcar";
       $Rs = $db->Execute($sql);
       return $Rs;
    }
}

?>