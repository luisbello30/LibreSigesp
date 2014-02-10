<?php
require_once("../class_folder/sigesp_conexion_dao.php");
class intMetasDao extends ADOdb_Active_Record
{
	var $_table='spe_relacion_estvar';
	
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
	
	public function BuscarMetas($integracion,$codemp)
	{
		global $db;
		$sql = "select {$this->_table}.*, 
				({$this->_table}.enero_masc+{$this->_table}.febrero_masc+{$this->_table}.marzo_masc+
				{$this->_table}.abril_masc+{$this->_table}.mayo_masc+{$this->_table}.junio_masc+
				{$this->_table}.julio_masc+{$this->_table}.agosto_masc+{$this->_table}.septiembre_masc+
				{$this->_table}.octubre_masc+{$this->_table}.noviembre_masc+{$this->_table}.diciembre_masc) as totalm,
				COALESCE(COALESCE(spe_relacion_estvar.enero_masc,0.00)+
                COALESCE(spe_relacion_estvar.febrero_masc,0.00)+
                COALESCE(spe_relacion_estvar.marzo_masc,0.00)+
				        COALESCE(spe_relacion_estvar.abril_masc,0.00)+
                COALESCE(spe_relacion_estvar.mayo_masc,0.00)+
                COALESCE(spe_relacion_estvar.junio_masc,0.00)+
				        COALESCE(spe_relacion_estvar.julio_masc,0.00)+
                COALESCE(spe_relacion_estvar.agosto_masc,0.00)+
                COALESCE(spe_relacion_estvar.septiembre_masc,0.00)+
                COALESCE(spe_relacion_estvar.octubre_masc,0.00)+
                COALESCE(spe_relacion_estvar.noviembre_masc,0.00)+
                COALESCE(spe_relacion_estvar.diciembre_masc,0.00)+
                COALESCE(spe_relacion_estvar.enero_fem,0.00)+
                COALESCE(spe_relacion_estvar.febrero_fem,0.00)+
                COALESCE(spe_relacion_estvar.marzo_fem,0.00)+
				        COALESCE(spe_relacion_estvar.abril_fem,0.00)+
                COALESCE(spe_relacion_estvar.mayo_fem,0.00)+
                COALESCE(spe_relacion_estvar.junio_fem,0.00)+
				        COALESCE(spe_relacion_estvar.julio_fem,0.00)+
                COALESCE(spe_relacion_estvar.agosto_fem,0.00)+
                COALESCE(spe_relacion_estvar.septiembre_fem,0.00)+
				        COALESCE(spe_relacion_estvar.octubre_fem,0.00)+
                COALESCE(spe_relacion_estvar.noviembre_fem,0.00)+
                COALESCE(spe_relacion_estvar.diciembre_fem,0.00)) as totalmeta,
				sig_variables.denominacion as meta from {$this->_table} inner join sig_variables on {$this->_table}.cod_var=sig_variables.cod_var and spe_relacion_estvar.codemp=sig_variables.codemp
				inner join spe_relacion_es on {$this->_table}.codinte=spe_relacion_es.codinte and spe_relacion_estvar.codemp=spe_relacion_es.codemp where {$this->_table}.codinte ={$integracion} and {$this->_table}.codemp ={$codemp}";
		//echo $sql;
		//die();
		$Rs = $db->Execute($sql); 
		return $Rs;
	}

}

?>