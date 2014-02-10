<?php
require_once("../class_folder/sigesp_conexion_dao.php");
class ub3Dao extends ADODB_Active_Record
{
var $_table='sigesp_ub3';				
public function FiltrarEst($Cond)
{
	global $db;
	//$Rs = $db->Execute("select * from {$this->_table} where {$Cond} "); 
	$Rs = $db->Execute("select * from {$this->_table} where {$Cond} and codemp= '{$this->codemp}'");
	return $Rs;
}
}
?>