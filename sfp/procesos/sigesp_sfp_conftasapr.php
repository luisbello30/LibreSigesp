<?php
require_once('../class_folder/dao/sigesp_spe_conftasasDao.php');
require_once('../class_folder/dao/sigesp_sfp_plancuentasDao.php');
require_once('../librerias/php/general/funciones.php');
require_once("../librerias/php/general/class_funciones_seguridad.php");
require_once('../librerias/php/general/CrearReporte.php');
require_once('../librerias/php/general/Json.php');
if ($_POST['ObjSon']) 	
{
	$submit = str_replace("\\","",$_POST['ObjSon']);
	//$submit = utf8_decode($submit);
	$json = new Services_JSON;
	$ArJson = $json->decode($submit);
	
	if($ArJson->movimientos)
	{
		for($j=0;$j<count($ArJson->movimientos);$j++)
		{
			$ArObjetos[$j] = new ConfTasas();
			PasarDatos($ArObjetos[$j],$ArJson->movimientos[$j]);			
		}
	}	
	$Evento = $ArJson->oper;
	switch ($Evento)
	{
		case 'ObtenerSesion':
    		if(!array_key_exists("la_logusr",$_SESSION))
			{
				echo "|nosesion";
				break;	
			}
			$io_fun_activo=new class_funciones_seguridad();
			$io_fun_activo->uf_load_seguridad("SFP",$ArJson->pantalla,$ls_permisos,$la_seguridad,$la_permisos);
			if($ls_permisos===true)
			{
				$jla_seguridad = $json->encode($la_seguridad);
				$jla_permisos = $json->encode($la_permisos);
				echo "{$jla_seguridad}|{$jla_permisos}|{$ls_permisos}";
			}
			else
			{
				echo "0|0|0";
			}
		break;    	
		case 'grabarconf':
			if(count($ArObjetos)>0)
			{
				ConfTasas::IniciarTran();
				for($i=0;$i<count($ArObjetos);$i++)
				{
					$resp=$ArObjetos[$i]->LeerUna();
					if($resp->RecordCount()>0)
					{
						$ArObjetos[$i]->actualizar();
					} 
					else
					{
						$ArObjetos[$i]->incluir();
					}
				}
				$resp=ConfTasas::CompletarTran();
				if($resp==true)
				{
					echo "1";
					
				}
				else
				{
					echo "0";
				}
			}
			break;
		case 'datostasas':
			$ArObjetos = new ConfTasas();
			$ArObjetos->tipotasa="IPC";
			$ArObjetos->cuenta=$ArJson->cuenta;
			$rs1 = $ArObjetos->LeerTodas($ArJson->criterio,$ArJson->cadena);
			$ObjSon1 = GenerarJson2($rs1);
			echo "{$ObjSon1}";	
			break;
		case 'datostasas2':
			$ArObjetos2 = new ConfTasas();
			$ArObjetos2->tipotasa="OTR";
			$ArObjetos2->cuenta=$ArJson->cuenta;
			$rs2 = $ArObjetos2->LeerTodas2();
			$ObjSon2 = GenerarJson2($rs2);
			echo "|{$ObjSon2}";	
			break;	
		case 'plancuentas':
			$ArObjetos2 = new PlancuentasDao();
			$rs2 = $ArObjetos2->LeerPorCadenaTodas($ArJson->criterio,$ArJson->cadena);
			$ObjSon1 = GenerarJson2($rs2);
			echo "{$ObjSon1}";	
			break;	
		case 'plancuentastasa':
			$ArObjetos2 = new PlancuentasDao();
			$rs2 = $ArObjetos2->LeerPorCadenaTasa($ArJson->criterio,$ArJson->cadena);
			$ObjSon1 = GenerarJson2($rs2);
			echo "{$ObjSon1}";	
			break;		
		case 'catalogo':
			$Datos = $ounidad->LeerTodos();					
			$ObjSon = GenerarJson2($Datos);
			echo $ObjSon;	
			break;
		case 'actualizar':
		if($ounidad->Modificar())
		{
			echo "|1";
		}
		else
		{
			echo "|0";
		}
		break;
		case 'eliminar':
		if($ounidad->Eliminar())
		{
			echo "|1";
		}
		else
		{
			echo "|0";
		}
		break;	
		case 'buscarcadena':
			$Datos = $ounidad->LeerPorCadena($GLOBALS["criterio"],$GLOBALS["cadena"]);
			$ObjSon = GenerarJson($Datos);
			echo $ObjSon;
			break;
		case 'Reporte':
			$oReporte = new Reporte();
			$Data = $ounidad->LeerTodos();
			$oReporte->CrearXml('listafuente',$Data);
			$oReporte->NomRep="FuenteFin";
			echo $oReporte->MostrarReporte();
	}
}



function PasarDatos(&$ObjDao,$ObJson)
{
	$ArDao = $ObjDao->getAttributeNames();
	foreach($ObjDao as $IndiceD =>$valorD)
	{
		foreach($ObJson as $Indice =>$valor)
		{
			if($Indice==$IndiceD && $Indice!="ano_presupuesto" && $Indice!="codemp")
			{
				$ObjDao->$Indice = utf8_decode($valor);					
			}
			else
			{
				
				$GLOBALS[$Indice] = $valor;
				
			}
			
			
			
		}
	}
}

function GenerarJson($Datos)
{
	global $ArJson,$json;
	$obj = $Datos[0];
		if(is_object($obj))
		{
			foreach($obj as $Propiedad=>$valor)
			{
				$i=0;
				foreach($Datos as $obj)
				{
		
					if(array_key_exists($Propiedad,$ArJson))	
					{	
						
						$arRegistros[$i][$Propiedad]= $Datos[$i]->$Propiedad;
						$i++;
					}
				
				}
	
			}
			//aqui se pasa el arreglo de arreglos a un objeto json
			$TextJso = array("raiz"=>$arRegistros);
			$TextJson = $json->encode($TextJso);
			return $TextJson;
			
		}
}

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
						if(is_numeric($valor) && $Propiedad!='ano_presupuesto')
						{
							$arRegistros[$i][$Propiedad]= utf8_encode(number_format($valor,2,",","."));
						}
						else
						{
							$arRegistros[$i][$Propiedad]= utf8_encode($valor);
						}
					}		
				}
		
				$i++;		
			}
			//aqui se pasa el arreglo de arreglos a un objeto json
			$TextJso = array("raiz"=>$arRegistros);
			$TextJson = $json->encode($TextJso);
			return $TextJson;
			
		
}

?>