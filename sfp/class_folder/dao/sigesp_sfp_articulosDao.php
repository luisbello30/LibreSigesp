<?php
require_once ("../class_folder/sigesp_conexion_dao.php");
class articulos extends ADOdb_Active_Record
{
    var $_table = 'siv_articulo';
    public function Incluir()
    {
        global $db;
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
    
    public function LeerPorCadenaCuenta($cr, $cad)
    {
       global $db;
      // $db->debug = true;
       $sql = "select siv_articulo.*,siv_articulo.codart as codigo,
       		  siv_articulo.denart as denominacion,
       		  siv_articulo.prearta as precio from siv_articulo
			  inner join siv_unidadmedida on
			  siv_articulo.codunimed=siv_unidadmedida.codunimed	
       		  where upper({$cr}) like upper('%{$cad}%')
       		  and spg_cuenta=trim('{$this->cuenta}')";
       $Rs = $db->Execute($sql);
       return $Rs;
    }
    
    public function LeerCargos()
    {
       global $db;
      // $db->debug = true;
       $sql = "select max(siv_cargosarticulo.codcar),sigesp_cargos.porcar
       		  from siv_cargosarticulo inner join sigesp_cargos 
       		  on siv_cargosarticulo.codcar=sigesp_cargos.codcar 
       		  where codart='{$this->codart}'
       		  group by sigesp_cargos.porcar";
       $Rs = $db->Execute($sql);
       return $Rs;
    }
}

?>