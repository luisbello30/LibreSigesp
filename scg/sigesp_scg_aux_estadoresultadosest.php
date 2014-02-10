<?php
session_start();
function uf_estructuras_ipsfa()
{	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_acumulado_operacion_asignacion( -> proviene de uf_calcular_acumulado_operaciones_por_cuenta
     //	    Returns :	Retorna monto asignado
	 //	Description :	Método que consulta y suma lo asignado por cuenta
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   	    $lb_valido = true;
   	    global $io_sql;
		$ldec_monto=0;
		$lonco1 =  $_SESSION["la_empresa"]["loncodestpro1"];
		$lonco2 =  $_SESSION["la_empresa"]["loncodestpro2"];
		$lonco3 =  $_SESSION["la_empresa"]["loncodestpro3"];
		$pos1=(25-$lonco1)+1;
		$pos2=(25-$lonco2)+1;
		$pos3=(25-$lonco3)+1;
		$ls_sql  =" select substr(spg_ep1.codestpro1,{$pos1},{$lonco1}) 
					as codestpro1,substr(spg_ep2.codestpro2,{$pos2},{$lonco2}) as 
					codestpro2,substr(spg_ep3.codestpro3,{$pos3},{$lonco3}) 
					as codestpro3,spg_ep1.denestpro1,spg_ep2.denestpro2,
					spg_ep3.denestpro3,spg_ep1.estcla from spg_ep3 
					inner join spg_ep2 
					on spg_ep3.codestpro1=spg_ep2.codestpro1 
					and spg_ep3.codestpro2=spg_ep2.codestpro2 
					and spg_ep3.estcla=spg_ep2.estcla 
					inner join spg_ep1 on
					spg_ep2.codestpro1=spg_ep1.codestpro1 
					and spg_ep2.estcla=spg_ep1.estcla ";				 
		// var_dump($ls_sql);
		// die();
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
			echo "Error en metodo uf_estructuras_ipsfa"."cosulta de estructuras presupuestarias del ipsfa";
			return false;
		}
		else
		{
			return $rs_data;
		}
} // fin function uf_calcular_acumulado_operacion_asignacion
	
function GenerarJson2($Datos)
{
			global $json;
			$i=0;
			while($Datos2=$Datos->FetchRow())
			{
			
				foreach($Datos2 as $Propiedad=>$valor)
				{
					if(!is_numeric($Propiedad))
					{
						$Propiedad = strtolower($Propiedad);
						$arRegistros[$i][$Propiedad]= utf8_encode($valor);
					}		
				}
		
				$i++;		
			}
			//aqui se pasa el arreglo de arreglos a un objeto json
			$TextJso = array("raiz"=>$arRegistros);
			$TextJson = $json->encode($TextJso);
			return $TextJson;		
}	

require_once("../shared/class_folder/sigesp_include.php");
$io_in=new sigesp_include();
$con=$io_in->uf_conectar();
require_once("../shared/class_folder/class_sql.php");
$io_sql=new class_sql($con);
require_once('../shared/class_folder/Json2.php');
if ($_POST['ObjSon']) 	
{
	$submit = str_replace("\\","",$_POST['ObjSon']);
	//$submit = utf8_decode($submit);
	$json = new Services_JSON;
	$ArJson = $json->decode($submit);	
	$rsDatos = uf_estructuras_ipsfa();
	if($rsDatos!=false)
	{				
		$json = GenerarJson2($rsDatos);
		echo $json;		
	}
	
}

?>