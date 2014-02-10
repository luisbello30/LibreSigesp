<?php
class sigesp_snorh_c_metodo_aporte
{
	var $io_mensajes;
	var $io_sno;
	var $io_fecha;
	var $io_metbanco;

	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_snorh_c_metodo_aporte()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_c_metodo_aporte
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 28/08/2006 								
		// Modificado Por: 										Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../shared/class_folder/class_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once("../shared/class_folder/class_datastore.php");
		$this->DS=new class_datastore();
		require_once("../shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
		require_once("../shared/class_folder/class_funciones.php");
		$this->io_funciones=new class_funciones();
		require_once("../shared/class_folder/class_fecha.php");
		$this->io_fecha=new class_fecha();
		require_once("sigesp_sno.php");
		$this->io_sno=new sigesp_sno();
		require_once("sigesp_snorh_c_metodobanco.php");
		$this->io_metbanco=new sigesp_snorh_c_metodobanco();
   		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->ls_nomemp=$_SESSION["la_empresa"]["nombre"];
		$this->ls_rifemp=$_SESSION["la_empresa"]["rifemp"];
		require_once ("../shared/writeexcel/class.writeexcel_workbook.inc.php");
		require_once ("../shared/writeexcel/class.writeexcel_worksheet.inc.php");	
	}// end function sigesp_sno_c_metodo_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_disgrega_rif(&$as_riflet,&$as_rifnum,&$as_rifdig)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_disgrega_rif
		//		   Access: private
		//	    Arguments: as_riflet // Letra del Rif
		//	    		   as_rifnum // Nï¿½mero de Rif
		//	    		   as_rifdig // Digitos del Rif
		//	      Returns: lb_valido 
		//    Description: function que separa letra, numero y digito del rif original
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 31/08/2006 								Fecha ï¿½ltima Modificaciï¿½n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_rif=$this->ls_rifemp;
		$as_riflet="J";
		$as_rifnum="XXXXXXXX";
		$as_rifdig="0";
		$li_pos1=strpos($ls_rif, "-");
		if($li_pos1>=1)
		{
			$as_riflet=substr($ls_rif,0,$li_pos1-1);
			$li_pos2=strpos($ls_rif,"-",$li_pos1);
			if($li_pos2>=1)
			{
				$as_rifnum=substr($ls_rif,$li_pos1+1,$li_pos2-$li_pos1-1);
				$as_rifdig=substr($ls_rif,$li_pos2+1);
			}
		}
	}// end function uf_disgrega_rif
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listado_gendisk($aa_codconc,$as_codnomdes,$as_codnomhas,$as_ano,$as_perdes,$as_perhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listado_gendisk
		//		   Access: public (desde la clase sigesp_sno_r_aportepatronal)  
		//	    Arguments: aa_codconc // Arreglo de conceptos se desea busca el personal
		//	    		   as_codnomdes // Cï¿½digo Nï¿½mina Desde
		//	    		   as_codnomhas // Cï¿½digo Nï¿½mina Hasta
		//	    		   as_ano // Aï¿½o en curso
		//	    		   as_perdes // Perï¿½odo Desde
		//	    		   as_perhas // Perï¿½odo Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ï¿½ False si no se creo
		//    Description: funciï¿½n que busca la informaciï¿½n de las personas que tienen asociado el concepto	de tipo aporte patronal 
		//				   y se calculï¿½ en la nï¿½mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 28/08/2006 								Fecha ï¿½ltima Modificaciï¿½n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_concbanavih=rtrim($this->io_sno->uf_select_config("SNO","NOMINA","CONCEPTOS_BANAVIH","","C"));
		$ls_concbanavih=str_replace("-","','",$ls_concbanavih);
		$ls_concbanavih="'".$ls_concbanavih."'";
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_ano))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.anocur='".$as_ano."' ";
		}
		if(!empty($as_perdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codperi>='".$as_perdes."' ";
		}
		if(!empty($as_perhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codperi<='".$as_perhas."' ";
		}
		$ls_sueldointegralbanavih=" 0  as sueintbanavih, ";
		if($ls_concbanavih<>"")
		{
			$ls_sueldointegralbanavih="       (SELECT SUM(valsal) ".
									  " 		 FROM sno_hsalida ".
					 				  "     	WHERE sno_hsalida.codconc IN (".$ls_concbanavih.") ".
									  $ls_criterio.
									  "           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
									  "   		  AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
									  "   		  AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
									  "   		  AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
									  "   		  AND sno_hpersonalnomina.codper = sno_hsalida.codper) as sueintbanavih, ";
		}
		$li_total=count($aa_codconc);
		for($li_i=0;$li_i<$li_total;$li_i++)
		{
			$ls_codconc=str_pad($aa_codconc[$li_i],10,"0",0);
			if($li_i==0)
			{
				$ls_criterio=$ls_criterio." AND (sno_hsalida.codconc='".$ls_codconc."' ";
			}
			else
			{
				$ls_criterio=$ls_criterio."    OR sno_hsalida.codconc='".$ls_codconc."' ";
			}
		}
		if($li_total>0)
		{
			$ls_criterio=$ls_criterio.")";
		}
		$ls_criterio = $ls_criterio." AND sno_hsalida.valsal<>0 ";
		$ls_sql="SELECT sno_hpersonalnomina.codper,sno_personal.cedper,  sno_personal.apeper, sno_personal.fecingper as fechainst,sno_personal.nomper, sno_hpersonalnomina.sueper,sno_hpersonalnomina.sueintper, ".
				"       sno_personal.nacper, sno_personal.fecnacper, sno_personal.sexper, sno_hpersonalnomina.fecingper, ".
				"		sno_personal.fecegrper, sno_hpersonalnomina.fecegrper AS fecegrnom, sno_personal.estper, ".
				"		sno_personal.cuecajahoper, sno_personal.edocivper, sno_hpersonalnomina.minorguniadm,sno_hpersonalnomina.ofiuniadm, ".
				"       sno_hpersonalnomina.uniuniadm,sno_hpersonalnomina.depuniadm,sno_hpersonalnomina.prouniadm, sno_hpersonalnomina.anocur, sno_hpersonalnomina.codperi,".
				$ls_sueldointegralbanavih.
				"       (SELECT tipnom ".
				"		   FROM sno_hnomina ".
				"   	  WHERE sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
				"   		AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom) as tipnom, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR sno_hsalida.tipsal='Q1') ".
				$ls_criterio.
				"           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper) as personal, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR sno_hsalida.tipsal='Q2') ".
				$ls_criterio.
				"           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper) as patron, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR sno_hsalida.tipsal='Q1') ".
				$ls_criterio.
				"           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi) as totalpersonal, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR sno_hsalida.tipsal='Q2') ".
				$ls_criterio.
				"           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi) as totalpatron, ".
				"       (SELECT DISTINCT acuemp ".
				"		   FROM sno_hconceptopersonal, sno_hsalida ".
				"   	  WHERE sno_hsalida.codemp = sno_hconceptopersonal.codemp ".
				"   		AND sno_hsalida.codnom = sno_hconceptopersonal.codnom ".
				"   		AND sno_hsalida.anocur = sno_hconceptopersonal.anocur ".
				"   		AND sno_hsalida.codperi = sno_hconceptopersonal.codperi ".
				"   		AND sno_hsalida.codconc = sno_hconceptopersonal.codconc ".
				"   		AND sno_hsalida.codper = sno_hconceptopersonal.codper ".
				"   	    AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				$ls_criterio.
				"   	 GROUP BY sno_hpersonalnomina.codper, acuemp) as acumulado, ".
				"       (SELECT DISTINCT acuiniemp ".
				"		   FROM sno_hconceptopersonal, sno_hsalida ".
				"   	  WHERE sno_hsalida.codemp = sno_hconceptopersonal.codemp ".
				"   		AND sno_hsalida.codnom = sno_hconceptopersonal.codnom ".
				"   		AND sno_hsalida.anocur = sno_hconceptopersonal.anocur ".
				"   		AND sno_hsalida.codperi = sno_hconceptopersonal.codperi ".
				"   		AND sno_hsalida.codconc = sno_hconceptopersonal.codconc ".
				"   		AND sno_hsalida.codper = sno_hconceptopersonal.codper ".
				"   	    AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				$ls_criterio.
				"   	  GROUP BY sno_hpersonalnomina.codper, acuiniemp) as acumuladoinicial ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"   AND sno_hpersonalnomina.anocur='".$as_ano."' ".
				"   AND sno_hpersonalnomina.codperi>='".$as_perdes."' ".
				"   AND sno_hpersonalnomina.codperi<='".$as_perhas."' ".
				"   AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2') ".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"	AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				" GROUP BY sno_hpersonalnomina.codemp, sno_personal.fecingper, sno_hpersonalnomina.anocur, sno_hpersonalnomina.codnom, ".
				"		   sno_hpersonalnomina.codperi, sno_hpersonalnomina.codper, sno_personal.cedper, sno_personal.apeper, ".
				"		   sno_personal.nomper, sno_hpersonalnomina.sueper, sno_personal.nacper, sno_personal.fecnacper, ".
				"		   sno_personal.sexper, sno_hpersonalnomina.fecingper, sno_personal.fecegrper, sno_personal.estper, ".
				"		   sno_personal.cuecajahoper, sno_hpersonalnomina.fecegrper,sno_personal.edocivper,sno_hpersonalnomina.minorguniadm, ".
				"          sno_hpersonalnomina.ofiuniadm, sno_hpersonalnomina.uniuniadm,sno_hpersonalnomina.depuniadm,sno_hpersonalnomina.prouniadm,sno_hpersonalnomina.sueintper ".
				" ORDER BY sno_hpersonalnomina.codper  ";

            //echo $ls_sql."<hr>";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("hhCLASE->Metodo LPH Mï¿½TODO->uf_listado_gendisk ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$datos=$this->io_sql->obtener_datos($rs_data);
				$this->DS->data=$datos;	
				$this->DS->group_by(array('0'=>'codper'),array('0'=>'personal','1'=>'patron','2'=>'sueintbanavih',3=>'sueintper'),'personal');							
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listado_gendisk
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listado_gendisk_nomina($aa_codconc,$as_codnomdes,$as_codnomhas,$as_ano,$as_perdes,$as_perhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listado_gendisk_nomina
		//		   Access: public (desde la clase sigesp_sno_r_aportepatronal)  
		//	    Arguments: aa_codconc // Arreglo de conceptos se desea busca el personal
		//	    		   as_codnomdes // Cï¿½digo Nï¿½mina Desde
		//	    		   as_codnomhas // Cï¿½digo Nï¿½mina Hasta
		//	    		   as_ano // Aï¿½o en curso
		//	    		   as_perdes // Perï¿½odo Desde
		//	    		   as_perhas // Perï¿½odo Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ï¿½ False si no se creo
		//    Description: funciï¿½n que busca la informaciï¿½n de las personas que tienen asociado el concepto	de tipo aporte patronal 
		//				   y se calculï¿½ en la nï¿½mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 02/05/2007 								Fecha ï¿½ltima Modificaciï¿½n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_concbanavih=rtrim($this->io_sno->uf_select_config("SNO","NOMINA","CONCEPTOS_BANAVIH","","C"));
		$ls_concbanavih=str_replace("-","','",$ls_concbanavih);
		$ls_concbanavih="'".$ls_concbanavih."'";
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_salida.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_salida.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_perdes))
		{
			$ls_criterio = $ls_criterio." AND sno_salida.codperi>='".$as_perdes."' ";
		}
		if(!empty($as_perhas))
		{
			$ls_criterio = $ls_criterio." AND sno_salida.codperi<='".$as_perhas."' ";
		}
		$ls_sueldointegralbanavih=" 0  as sueintbanavih, ";
		if($ls_concbanavih<>"")
		{
			$ls_sueldointegralbanavih="       (SELECT SUM(valsal) ".
									  " 		 FROM sno_salida ".
					 				  "     	WHERE sno_salida.codconc IN (".$ls_concbanavih.") ".
									  $ls_criterio.
									  "           AND sno_personalnomina.codemp = sno_salida.codemp ".
									  "   		  AND sno_personalnomina.codnom = sno_salida.codnom ".
									//  "   		  AND sno_personalnomina.anocur = sno_salida.anocur ".
									 // "   		  AND sno_personalnomina.codperi = sno_salida.codperi ".
									  "   		  AND sno_personalnomina.codper = sno_salida.codper) as sueintbanavih, ";
		}
               // echo $ls_sueldointegralbanavih;die();
		$li_total=count($aa_codconc);
		for($li_i=0;$li_i<$li_total;$li_i++)
		{
			if($li_i==0)
			{
				$ls_criterio=$ls_criterio." AND (sno_salida.codconc='".$aa_codconc[$li_i]."' ";
			}
			else
			{
				$ls_criterio=$ls_criterio."    OR sno_salida.codconc='".$aa_codconc[$li_i]."' ";
			}
		}
		if($li_total>0)
		{
			$ls_criterio=$ls_criterio.")";
		}
		$ls_criterio = $ls_criterio." AND sno_salida.valsal<>0 ";
		$ls_sql="SELECT sno_personalnomina.codper,sno_personal.cedper,  sno_personal.apeper, sno_personal.nomper, sno_personalnomina.sueper, ".
				"       sno_personal.nacper, sno_personal.fecnacper, sno_personal.sexper, sno_personalnomina.fecingper, ".
				"		sno_personal.fecegrper, sno_personalnomina.fecegrper AS fecegrnom, sno_personal.estper, ".
				"		sno_personal.cuecajahoper, sno_personal.edocivper, ".
				$ls_sueldointegralbanavih.
				"       (SELECT tipnom ".
				"		   FROM sno_nomina ".
				"   	  WHERE sno_personalnomina.codemp = sno_nomina.codemp ".
				"   		AND sno_personalnomina.codnom = sno_nomina.codnom) as tipnom, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_salida ".
				"   	  WHERE (sno_salida.tipsal='P1' OR sno_salida.tipsal='V3' OR sno_salida.tipsal='W3' OR sno_salida.tipsal='Q1') ".
				$ls_criterio.
				"           AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   		AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   		AND sno_personalnomina.codper = sno_salida.codper) as personal, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_salida ".
				"   	  WHERE (sno_salida.tipsal='P2' OR sno_salida.tipsal='V4' OR sno_salida.tipsal='W4' OR sno_salida.tipsal='Q2') ".
				$ls_criterio.
				"           AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   		AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   		AND sno_personalnomina.codper = sno_salida.codper) as patron, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_salida ".
				"   	  WHERE (sno_salida.tipsal='P1' OR sno_salida.tipsal='V3' OR sno_salida.tipsal='W3' OR sno_salida.tipsal='Q1') ".
				$ls_criterio.
				"           AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   		AND sno_personalnomina.codnom = sno_salida.codnom) as totalpersonal, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_salida ".
				"   	  WHERE (sno_salida.tipsal='P2' OR sno_salida.tipsal='V4' OR sno_salida.tipsal='W4' OR sno_salida.tipsal='Q2') ".
				$ls_criterio.
				"           AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   		AND sno_personalnomina.codnom = sno_salida.codnom) as totalpatron, ".
				"       (SELECT DISTINCT acuemp ".
				"		   FROM sno_conceptopersonal, sno_salida ".
				"   	  WHERE sno_salida.codemp = sno_conceptopersonal.codemp ".
				"   		AND sno_salida.codnom = sno_conceptopersonal.codnom ".
				"   		AND sno_salida.codconc = sno_conceptopersonal.codconc ".
				"   		AND sno_salida.codper = sno_conceptopersonal.codper ".
				"   	    AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   		AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   		AND sno_personalnomina.codper = sno_salida.codper ".
				$ls_criterio.
				"   	 GROUP BY sno_personalnomina.codper, acuemp) as acumulado, ".
				"       (SELECT DISTINCT acuiniemp ".
				"		   FROM sno_conceptopersonal, sno_salida ".
				"   	  WHERE sno_salida.codemp = sno_conceptopersonal.codemp ".
				"   		AND sno_salida.codnom = sno_conceptopersonal.codnom ".
				"   		AND sno_salida.codconc = sno_conceptopersonal.codconc ".
				"   		AND sno_salida.codper = sno_conceptopersonal.codper ".
				"   	    AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   		AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   		AND sno_personalnomina.codper = sno_salida.codper ".
				$ls_criterio.
				"   	  GROUP BY sno_personalnomina.codper, acuiniemp) as acumuladoinicial ".
				"  FROM sno_personal, sno_personalnomina, sno_salida ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_personalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_personalnomina.codnom<='".$as_codnomhas."' ".
				"   AND (sno_personalnomina.staper='1' OR sno_personalnomina.staper='2') ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"	AND sno_personal.codemp = sno_personalnomina.codemp ".
				"   AND sno_personal.codper = sno_personalnomina.codper ".
				" GROUP BY sno_personalnomina.codemp,sno_personalnomina.codnom, sno_personalnomina.codper, sno_personal.cedper, ".
				"		   sno_personal.apeper, sno_personal.nomper, sno_personalnomina.sueper, sno_personal.nacper, ".
				"		   sno_personal.fecnacper, sno_personal.sexper, sno_personalnomina.fecingper, sno_personal.fecegrper, ".
				"		   sno_personal.estper, sno_personal.cuecajahoper, sno_personalnomina.fecegrper,sno_personal.edocivper ".
				"   ".$ls_orden;

             //  echo $ls_sql; die();
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Metodo LPH Mï¿½TODO->uf_listado_gendisk ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$datos=$this->io_sql->obtener_datos($rs_data);
				$this->DS->data=$datos;	
				$this->DS->group_by(array('0'=>'codper'),array('0'=>'personal','1'=>'patron'),'codper');							
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listado_gendisk_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_fpj($as_ruta,$as_metodo,$as_organismo,$aa_ds_banco,$ad_fecproc,$as_codconc,
						   $as_codnomdes,$as_codnomhas,$as_anocur,$as_perdes,$as_perhas,$aa_seguridad)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fpj
		//		   Access: public 
		//	    Arguments: as_ruta // Ruta donde se guardan los archivos txt 
		//	    		   as_metodo // Mï¿½todo de fpj
		//	    		   as_organismo // Cï¿½digo de Organismo
		//	    		   aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fecpro // Fecha de la Nï¿½mina
		//	    		   as_codconc // Cï¿½digo del concepto del que se desea busca el personal
		//	    		   as_codnomdes // Cï¿½digo Nï¿½mina Desde
		//	    		   as_codnomhas // Cï¿½digo Nï¿½mina Hasta
		//	    		   as_ano // Aï¿½o en curso
		//	    		   as_perdes // Perï¿½odo Desde
		//	    		   as_perhas // Perï¿½odo Hasta
		//	  Description: genera el archivo txt a disco para el Fondo de Pensiï¿½n de Jubilaciones
		//	   Creado Por: Ing. Yesenia Moreno	
		// Fecha Creaciï¿½n: 28/08/2006 								
		// Modificado Por: 								Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_dia=substr($ad_fecproc,0,2);
		$li_mes=substr($ad_fecproc,3,2);
		$li_ano=substr($ad_fecproc,8,2);
		$ls_nombrearchivo=$as_ruta."/"."O".$li_dia.$li_mes.$li_ano."0.txt";
		$ls_nombrearchivo_e=$as_ruta."/"."O".$li_dia.$li_mes.$li_ano."0-e.txt";
		$ls_nombrearchivo_p=$as_ruta."/"."O".$li_dia.$li_mes.$li_ano."0-p.txt";
		$li_count=$aa_ds_banco->getRowCount("cedper");
		if ($li_count>0)
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				//if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				//{
				//	$lb_valido = false;
				//}
				//else
				//{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				//}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo_e"))
			{
				//if(@unlink("$ls_nombrearchivo_e")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				//{
				//	$lb_valido = false;
				//}
				//else
				//{
					$ls_creararchivo_e = @fopen("$ls_nombrearchivo_e","a+");
				//}
			}
			else
			{
				$ls_creararchivo_e = @fopen("$ls_nombrearchivo_e","a+"); //creamos y abrimos el archivo para escritura
			}
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo_p"))
			{
				//if(@unlink("$ls_nombrearchivo_p")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				//{
				//	$lb_valido = false;
				//}
				//else
				//{
					$ls_creararchivo_p = @fopen("$ls_nombrearchivo_p","a+");
				//}
			}
			else
			{
				$ls_creararchivo_p = @fopen("$ls_nombrearchivo_p","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;(($li_i<=$li_count)&&($lb_valido));$li_i++)
			{
				$ls_cedper=str_replace(".","",$aa_ds_banco->data["cedper"][$li_i]);
				$ls_cedper=substr($ls_cedper,0,8);
				$ls_cedper=str_replace("0","",$ls_cedper); //Agregado por Carlos Zambrano
				$li_dia=substr($ad_fecproc,0,2);
				$li_mes=substr($ad_fecproc,3,2);
				$li_ano=substr($ad_fecproc,6,4);
				$ls_nomper=trim($aa_ds_banco->data["nomper"][$li_i]);
				$ls_apeper=trim($aa_ds_banco->data["apeper"][$li_i]);
				$ls_personal=$ls_apeper.", ".$ls_nomper;				
				$li_sueper=$aa_ds_banco->data["sueper"][$li_i];				
				$li_sueper=number_format($li_sueper,2,",","");
				$li_personal=abs($aa_ds_banco->data["personal"][$li_i]);				
				$li_personal=number_format($li_personal,2,",","");
				$li_patron=abs($aa_ds_banco->data["patron"][$li_i]);				
				$li_patron=number_format($li_patron,2,",","");
                                //$aa_ds_banco
				$ls_cadena=$ls_cedper."|".$li_dia.$li_mes.$li_ano."|".$ls_personal."|".$as_organismo."|".$li_sueper."|".$li_personal."|".$li_patron."\r\n";
				if ($ls_creararchivo)
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido=false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido=false;
				}	
				$ls_cadena=$ls_cedper."|".$li_dia.$li_mes.$li_ano."|".$ls_personal."|".$as_organismo."|".$li_sueper."|".$li_personal."|0000000,00\r\n";
				if ($ls_creararchivo_e)
				{
					if (@fwrite($ls_creararchivo_e,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo_e);
						$lb_valido=false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo_e);
					$lb_valido=false;
				}	
				$ls_cadena=$ls_cedper."|".$li_dia.$li_mes.$li_ano."|".$ls_personal."|".$as_organismo."|".$li_sueper."|0000000,00|".$li_patron."\r\n";
				if ($ls_creararchivo_p)
				{
					if (@fwrite($ls_creararchivo_p,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo_p);
						$lb_valido=false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo_p);
					$lb_valido=false;
				}	
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="PROCESS";
				$ls_descripcion ="Generï¿½ el disco de FPJ.Concepto ".$as_codconc." Nï¿½mina Desde ".$as_codnomdes." Nï¿½mina Hasta ".$as_codnomhas."<br>".
								 " Aï¿½o ".$as_anocur." Periodo Desde ".$as_perdes." Periodo Hasta ".$as_perhas;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fpj
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph($as_ruta,$as_metodo,$aa_ds_lph,$ad_fecproc,$as_codconc,$as_codnomdes,$as_codnomhas,$as_anocur,
						   $as_perdes,$as_perhas,$aa_seguridad)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph	
		//	    Arguments: as_ruta   // ruta donde se va aguardar el archivo
		//	    		   as_metodo   // Cï¿½digo del metodo a banco
		//                 aa_ds_lph // arreglo (datastore) datos lph      
		//	    		   ad_fecpro // Fecha de la Nï¿½mina
		//	    		   as_codconc // Cï¿½digo del concepto del que se desea busca el personal
		//	    		   as_codnomdes // Cï¿½digo Nï¿½mina Desde
		//	    		   as_codnomhas // Cï¿½digo Nï¿½mina Hasta
		//	    		   as_ano // Aï¿½o en curso
		//	    		   as_perdes // Perï¿½odo Desde
		//	    		   as_perhas // Perï¿½odo Hasta
		//				   aa_seguridad // arreglo de seguridad
		//	      Returns: lb_valido True 
		//	  Description: Funcion que segun el banco, genera un archivo txt a disco para cancelaciï¿½n de ley de polï¿½tica
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 30/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
                //echo $as_metodo; die();
	  	switch ($as_metodo)
		{
			case "VENEZUELA":
				$lb_valido=$this->uf_metodo_lph_venezuela($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;

			case "MERCANTIL":
				$lb_valido=$this->uf_metodo_lph_mercantil($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;

			case "BANESCO":
				$lb_valido=$this->uf_metodo_lph_banesco($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
				
			case "CAJA FAMILIA":
				$lb_valido=$this->uf_metodo_lph_caja_familia($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
			
			case "CANARIAS":
				$lb_valido=$this->uf_metodo_lph_canarias($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
			
			case "CASA PROPIA":
				$lb_valido=$this->uf_metodo_lph_casapropia($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
			
			case "CENTRAL":
				$lb_valido=$this->uf_metodo_lph_central($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
			
			case "DELSUR":
				$lb_valido=$this->uf_metodo_lph_delsur($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
				
			case "FONDO MUTUAL HABITACIONAL":
				$lb_valido=$this->uf_metodo_lph_f_m_h($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
			
			case "MERENAP":
				$lb_valido=$this->uf_metodo_lph_merenap($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
			
			case "MIRANDA":
				$lb_valido=$this->uf_metodo_lph_miranda($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
				
			case "MI CASA EAP":
				$lb_valido=$this->uf_metodo_lph_mi_casa_eap($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
			
			case "VIVIENDA":
				$lb_valido=$this->uf_metodo_lph_vivienda($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;

			case "FONDO_COMUN_EAP":
				$lb_valido=$this->uf_metodo_lph_fondocomun_eap($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;

			case "FONDO_COMUN_MRE":
				$lb_valido=$this->uf_metodo_lph_fondocomun_mre($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;

			case "BOD":
				$lb_valido=$this->uf_metodo_lph_bod($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;

			case "BANAVIH":
				$lb_valido=$this->uf_metodo_lph_banavih($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;
			
			case "BANAVIH2":
				$lb_valido=$this->uf_metodo_lph_banavih2($as_ruta,$ad_fecproc,$aa_ds_lph);
				break;

			default:
				$this->io_mensajes->message("El mï¿½todo seleccionado no esta disponible.");
				break;
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Generï¿½ el disco de LPH.Concepto ".$as_codconc." Nï¿½mina Desde ".$as_codnomdes." Nï¿½mina Hasta ".$as_codnomhas."<br>".
							 " Aï¿½o ".$as_anocur." Periodo Desde ".$as_perdes." Periodo Hasta ".$as_perhas;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
	}// end function uf_metodo_lph
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_lph_venezuela($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_venezuela
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecproc // fecha de procesamiento 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: genera el archivo txt a disco para  el banco BOD para pago de nomina
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ld_desde=$ad_fecproc;
		$lb_valido=true;
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/ingresos.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				//if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				//{
				//	$lb_valido = false;
				//}
				//else
				//{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				//}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ld_fecingper=$aa_ds_lph->data["fechainst"][$li_i];
				$ld_mesingper=substr($ld_fecingper,5,2);
				$ld_anoingper=substr($ld_fecingper,0,4);
				$ld_mesfecdes=substr($ld_desde,3,2);
				$ld_anofecdes=substr($ld_desde,6,4);
				if(($ld_mesingper==$ld_mesfecdes)&&($ld_anoingper==$ld_anofecdes))
				{
					$ld_fecnacper=$this->io_funciones->uf_trim($aa_ds_lph->data["fecnacper"][$li_i]);
					$ld_fecnacper=substr($ld_fecnacper,8,2).substr($ld_fecnacper,5,2).substr($ld_fecnacper,0,4); //DDMMAAAA
					$ld_fecingper=substr($ld_fecingper,8,2).substr($ld_fecingper,5,2).substr($ld_fecingper,0,4); //DDMMAAAA
					$ls_nacper= $this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
					$ls_cedper= $this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
					$ls_cedper= $this->io_funciones->uf_rellenar_der($ls_cedper,"0",10);
					$ls_nomper= $this->io_funciones->uf_trim($aa_ds_lph->data["nomper"][$li_i]); //nombres
					$ls_apeper= $this->io_funciones->uf_trim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
					$ls_nombre= $this->io_funciones->uf_rellenar_der($ls_apeper.", ".$ls_nomper, " ", 50);
					$ls_sexper= $this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]); //sexo del empleado M o F
					$ls_cadena = $ls_nacper.$ls_cedper.$ls_nombre.$ld_fecnacper.$ls_sexper.$ld_fecingper."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}//fin del for
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
			$ls_nombrearchivo=$as_ruta."/egresos.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				//if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				//{
				//	$lb_valido = false;
				//}
				//else
				//{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				//}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{//echo "ff";die();
				$ld_fecha="";
				$ld_fecnacper=$this->io_funciones->uf_trim($aa_ds_lph->data["fecnacper"][$li_i]);
				$ld_fecnacper=substr($ld_fecnacper,8,2).substr($ld_fecnacper,5,2).substr($ld_fecnacper,0,4); //DDMMAAAA
				$ld_fecha=$ld_fecha.$ld_fecnacper;
				$ld_fecingper=$aa_ds_lph->data["fecingper"][$li_i]; 
				$ld_fecingper=substr($ld_fecingper,8,2).substr($ld_fecingper,5,2).substr($ld_fecingper,0,4); //DDMMAAAA
				$ld_fecha=$ld_fecha.$ld_fecingper;
				$ld_fecegrper=$aa_ds_lph->data["fecegrper"][$li_i]; 
				if ((is_null($ld_fecegrper))||($ld_fecegrper==""))
				{
					$ld_fecegrper=$aa_ds_lph->data["fecegrnom"][$li_i]; 
				}
				$ld_fecegrper=substr($ld_fecegrper,8,2).substr($ld_fecegrper,5,2).substr($ld_fecegrper,0,4); //DDMMAAAA
				$ld_fecha=$ld_fecha.$ld_fecegrper;
				$ls_nacper= $this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper= $this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper= $this->io_funciones->uf_rellenar_der($ls_cedper,"0",10);
				$ls_nomper= $this->io_funciones->uf_trim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$ls_apeper= $this->io_funciones->uf_trim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$ls_nombre= $this->io_funciones->uf_rellenar_der($ls_apeper.", ".$ls_nomper, " ", 50);
				$ld_fecingper=$aa_ds_lph->data["fecingper"][$li_i]; 
				$ld_mesegrper=substr($ld_fecegrper,5,2);
				$ld_anoegrper=substr($ld_fecegrper,0,4);
				$ld_mesfecdes=substr($ld_desde,3,2);
				$ld_anofecdes=substr($ld_desde,6,4);
				if(($ld_mesegrper==$ld_mesfecdes)&&($ld_anoegrper==$ld_anofecdes))
				{
					$ls_cadena = $ls_nacper.$ls_cedper.$ls_nombre.$ld_fecha."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}//fin del for
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}
			$ls_nombrearchivo=$as_ruta."/aportes.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				//if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				//{
				//	$lb_valido = false;
				//}
				//else
				//{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				//}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_periodo=substr($ld_desde,3,2).substr($ld_desde,6,4);//Periodo retencion  Mes/Aï¿½o del Aporte
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper= $this->io_funciones->uf_rellenar_izq($ls_cedper,"0",10);
				$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100);   //Monto aporte-porcion empleado 				
				$ldec_monper=$this->io_funciones->uf_cerosizquierda($ldec_monper,7);
				$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100);   //Monto aporte-porcion patronal 
				$ldec_monpat=$this->io_funciones->uf_cerosizquierda($ldec_monpat,7);
				$ls_cadena=$ls_cedper.$ldec_monper.$ldec_monpat.$ls_periodo."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}//fin del for
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_venezuela
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_mercantil($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_mercantil
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: genera el archivo txt a disco para  el banco BOD para pago de nomina
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 31/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=$ad_fecproc;
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/bmahm000.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_debcuelph="";
			$ls_numplalph="";
			$ls_numconlph="";
			$ls_suclph="";
			$ls_cuelph="";
			$ls_grulph="";
			$ls_subgrulph="";
			$ls_conlph="";
			$ls_numactlph="";
			$ls_codagelph="";
			$ls_apaposlph="";
			$lb_valido=$this->io_metbanco->uf_load_metodobanco_lph("MERCANTIL","1",$ls_debcuelph,$ls_numplalph,$ls_numconlph,$ls_suclph,
																   $ls_cuelph,$ls_grulph,$ls_subgrulph,$ls_conlph,$ls_numactlph,$ls_codagelph,$ls_apaposlph);
			$ls_numactlph=$ls_numactlph+1;
			$lb_valido=$this->io_metbanco->uf_update_campo_lph("MERCANTIL","1","numactlph",$ls_numactlph);
			$ls_riflet="";
			$ls_rifnum="";
			$ls_rifdig="";
			$ls_rif=$this->ls_rifemp; 
			$ls_letra=substr($ls_rif,0,1)."0";
			$ls_rif=str_replace("-","",substr($ls_rif,1,12));
			$ls_rif=$ls_letra.$ls_rif;						
			if ($ls_debcuelph==1)
			{
				$ls_modpago="660";
			}
			else
			{
				$ls_modpago="666";
				$ls_cuelph="00000000000000000000";
				$ls_grulph="000000";
				$ls_subgrulph="00";
			}
			$ls_numactlph=$this->io_funciones->uf_cerosizquierda($ls_numactlph,12);
			$ldec_totpatron=(abs($aa_ds_lph->data["totalpatron"][1])*100);
			$ldec_totpersonal=(abs($aa_ds_lph->data["totalpersonal"][1])*100);
			$ls_cuelph=$this->io_funciones->uf_cerosizquierda($ls_cuelph,20); //Numero de Cuenta a debitar o ceros en caso de Deposito
			$ls_grulph=$this->io_funciones->uf_cerosizquierda($ls_grulph,6); //Codigo de Grupo para Clientes con Cuenta Mercantil (Solo en Dï¿½bito), Ceros para empresas Comerciales y en caso de Depï¿½sito
			$ls_codsubgrupo=$this->io_funciones->uf_cerosizquierda($ls_subgrulph,2);
			$ldec_monaporte=$this->io_funciones->uf_cerosizquierda($ldec_totpatron+$ldec_totpersonal,13); // Monto Total Movimiento     
			$ls_numconlph=$this->io_funciones->uf_cerosizquierda($ls_numconlph,9);
			$ld_fecaporte=str_replace("/","",$ld_desde);
			$ld_fecaporte=substr($ld_fecaporte,4,4).substr($ld_fecaporte,2,2); //Fecha de Aporte (AAAAMM)
			$ls_cadena=$ls_modpago.$ls_cuelph."855".$ls_grulph.$ls_codsubgrupo.$ldec_monaporte.str_repeat("0",17)."0"."2".
					   str_repeat("0",21).$ls_numconlph.str_repeat("0",4).$ls_rif.$ld_fecaporte.
					   str_repeat("0",40).$ls_numactlph."\r\n";
			if ($ls_creararchivo)  //Chequea que el archivo este abierto				
			{
				if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
				{
					$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			else
			{
				$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
				$lb_valido = false;
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   
				$ld_fecingper=$aa_ds_lph->data["fechainst"][$li_i];
				$ld_mesingper=substr($ld_fecingper,5,2);
				$ld_anoingper=substr($ld_fecingper,0,4);
				$ld_mesfecdes=substr($ld_desde,3,2);
				$ld_anofecdes=substr($ld_desde,6,4);
				if(($ld_mesingper==$ld_mesfecdes)&&($ld_anoingper==$ld_anofecdes))
				{
					$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
					$ls_cedper=$this->io_funciones->uf_trim(str_pad($aa_ds_lph->data["cedper"][$li_i],10,"0",0)); //cedula
					$ls_nomper=$this->io_funciones->uf_trim($aa_ds_lph->data["nomper"][$li_i]); //nombres
					$ls_apeper=$this->io_funciones->uf_trim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
					$ls_nombre=str_pad(substr($ls_apeper." ".$ls_nomper,0,35),35," ");
					$ld_fecnacper=$this->io_funciones->uf_trim(str_replace("-","",$aa_ds_lph->data["fecnacper"][$li_i]));	//Fecha de nacimiento del empleado AAAAMMDD
					$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo del empleado M o F
					$ls_constante1="0000000000000000000000000000000000";
					$ls_constante2="0000000000000000000000000000000000000000000000000000";
					$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100);       //Monto aporte-porcion empleado 
					$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100);       //Monto aporte-porcion patronal 
					$ldec_monto=$this->io_funciones->uf_cerosizquierda(round($ldec_monper+$ldec_monpat,2),13);
					$ls_cadena="322".$ls_constante1.$ls_nacper.$ls_cedper.$ls_constante2.$ls_nombre.$ld_fecnacper.$ls_sexper.$ldec_monto."00000000"."0"."000"."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   					
				$ld_fecingper=$aa_ds_lph->data["fecingper"][$li_i]; 
				$ld_mesingper=substr($ld_fecingper,5,2);
				$ld_anoingper=substr($ld_fecingper,0,4);
				$ld_mesfecdes=substr($ld_desde,3,2);
				$ld_anofecdes=substr($ld_desde,6,4);
				if(((intval($ld_anoingper)===intval($ld_anofecdes))&&(intval($ld_mesingper)<intval($ld_mesfecdes)))||
				   ((intval($ld_anoingper)<intval($ld_anofecdes))))
				{
					$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]);     //nacionalidad
					$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]);     //cedula
					$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);			
					$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100);       //Monto aporte-porcion empleado 
					$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100);       //Monto aporte-porcion patronal 
					$ldec_monto=$this->io_funciones->uf_cerosizquierda(round($ldec_monper+$ldec_monpat,2),13);
					$ls_cadena="323".str_repeat("0",34).$ls_nacper.$ls_cedper.str_repeat("0",96).$ldec_monto.str_repeat("0",12)."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_mercantil
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_banesco($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_banesco
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco BANESCO para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 31/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_rif=trim($this->ls_rifemp);
		$ls_rif=str_replace("-","",$ls_rif);
		$ls_rif=substr($ls_rif,0,10);
		$ld_desde=substr($ad_fecproc,0,10);
		$ld_desde=str_replace("/","",$ld_desde);
		$ls_periodo=str_replace("/","",$ad_fecproc);
		$ls_periodo=substr($ls_periodo,2,6);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/lph-".substr($ls_periodo,0,2).substr($ls_periodo,4,2).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_debcuelph="";
			$ls_numplalph="";
			$ls_numconlph="";
			$ls_suclph="";
			$ls_cuelph="";
			$ls_grulph="";
			$ls_subgrulph="";
			$ls_conlph="";
			$ls_numactlph="";
			$ls_codagelph="";
			$ls_apaposlph="";
			$lb_valido=$this->io_metbanco->uf_load_metodobanco_lph("BANESCO","1",$ls_debcuelph,$ls_numplalph,$ls_numconlph,$ls_suclph,
																   $ls_cuelph,$ls_grulph,$ls_subgrulph,$ls_conlph,$ls_numactlph,$ls_codagelph,$ls_apaposlph);
			$ls_numconlph=$this->io_funciones->uf_rellenar_izq($ls_numconlph," ",9);
			$ls_suclph=$this->io_funciones->uf_rellenar_izq($ls_suclph," ",4);
			$li_numero="0000001";
			$ldec_totpatron=abs($aa_ds_lph->data["totalpatron"][1]); 
			$ldec_totpersonal=abs($aa_ds_lph->data["totalpersonal"][1]);
			$ldec_totpatron=($ldec_totpatron*100);
			$ldec_totpersonal=($ldec_totpersonal*100);
			$li_numero=$this->io_funciones->uf_rellenar_izq($li_count,"0",7);
			$ldec_totpatron=$this->io_funciones->uf_rellenar_izq($ldec_totpatron,"0",13);
			$ldec_totpersonal=$this->io_funciones->uf_rellenar_izq($ldec_totpersonal,"0",13);
			$ls_cadena="00".$ls_numconlph.$ls_suclph.$ls_rif.substr($ls_periodo,2,4).substr($ls_periodo,0,2).$li_numero.$ldec_totpatron.$ldec_totpersonal."P"."                                  "."\r\n";
			if ($ls_creararchivo)  //Chequea que el archivo este abierto				
			{
				if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
				{
					$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			else
			{
				$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
				$lb_valido = false;
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad				
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,8);
				$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100); //Monto aporte-porcion empleado (incluyendo las 2 decimales)
				$ldec_monper=$this->io_funciones->uf_rellenar_izq(round($ldec_monper,2),"0",9);
				$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100); //Monto aporte-porcion patronal (incluyendo las 2 decimales)
				$ldec_monpat=$this->io_funciones->uf_rellenar_izq(round($ldec_monpat,2),"0",9);
				$ls_apeper=$this->io_funciones->uf_rellenar_der(substr($aa_ds_lph->data["apeper"][$li_i],0,20)," ",20); //apellidos
				$ls_nomper=$this->io_funciones->uf_rellenar_der(substr($aa_ds_lph->data["nomper"][$li_i],0,20), " ",20); //nombres
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);
				$ld_fecnacper=substr($aa_ds_lph->data["fecnacper"][$li_i],0,10); //fecnacper debe estar formato (AAAAMMDD)
				$ld_fecnacper=$this->io_funciones->uf_trim(str_replace("-","",$ld_fecnacper)); //fecnacper debe estar formato (AAAAMMDD)
				$ls_cadena ="01".$ls_nacper.$ls_cedper.$ldec_monpat.$ldec_monper." ".$ls_apeper.$ls_nomper.$ls_sexper.$ld_fecnacper.
							substr($ld_desde,4,4).substr($ld_desde,2,2).substr($ld_desde,0,2)."        "."    "."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_banesco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_caja_familia($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_caja_familia
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco CAJA FAMILIA para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 31/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ld_desde=substr($ad_fecproc,0,10);
		$ld_desde=str_replace("/","",$ld_desde);
		$ls_periodo=str_replace("/","",$ad_fecproc);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/lphm".substr($ls_periodo,2,2).substr($ls_periodo,6,2).".txt"; 
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_riflet="";
			$ls_rifnum="";
			$ls_rifdig="";
			$this->uf_disgrega_rif($ls_riflet,$ls_rifnum,$ls_rifdig);	
			$ls_debcuelph="";
			$ls_numplalph="";
			$ls_numconlph="";
			$ls_suclph="";
			$ls_cuelph="";
			$ls_grulph="";
			$ls_subgrulph="";
			$ls_conlph="";
			$ls_codagelph="";
			$ls_apaposlph="";
			$ls_numactlph="";
			$lb_valido=$this->io_metbanco->uf_load_metodobanco_lph("CAJA FAMILIA","1",$ls_debcuelph,$ls_numplalph,$ls_numconlph,$ls_suclph,
																   $ls_cuelph,$ls_grulph,$ls_subgrulph,$ls_conlph,$ls_numactlph,$ls_codagelph,$ls_apaposlph);
			$ls_numconlph=$this->io_funciones->uf_rellenar_izq($ls_numconlph," ",9);
			$ls_suclph=$this->io_funciones->uf_rellenar_izq($ls_suclph," ",4);			
			$li_numero="";
			$ldec_totpatron=abs($aa_ds_lph->data["totalpatron"][1]); 
			$ldec_totpersonal=abs($aa_ds_lph->data["totalpersonal"][1]);
			$ldec_totpatron=($ldec_totpatron*100);
			$ldec_totpersonal=($ldec_totpersonal*100);
			$ld_anoaporte=substr($ld_desde,2,4);  //Aï¿½O DEL APORTE DE ACUERDO A LA NOMINA (AAAAMM)
			$ld_mesaporte=substr($ld_desde,0,2); //MES DEL APORTE DE ACUERDO A LA NOMINA (AAAAMM)
			$li_numero=$this->io_funciones->uf_rellenar_izq($li_numero," ",7);
			$ldec_totpatron=$this->io_funciones->uf_rellenar_izq($ldec_totpatron," ",13);
			$ldec_totpersonal=$this->io_funciones->uf_rellenar_izq($ldec_totpersonal," ",13);
			$ls_cadena="00".$ls_numconlph.$ls_suclph.$ls_riflet.$ls_rifnum.$ls_rifdig.$ld_anoaporte.$ld_mesaporte.$li_numero.
			                   $ldec_totpatron.$ldec_totpersonal."P"."                                  "."\r\n";
			if ($ls_creararchivo)  //Chequea que el archivo este abierto				
			{
				if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
				{
					$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			else
			{
				$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
				$lb_valido = false;
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100); //Monto aporte-porcion empleado (incluyendo las 2 decimales)
				$ldec_monper=$this->io_funciones->uf_rellenar_izq(round($ldec_monper,2)," ",9);
				$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100); //Monto aporte-porcion patronal (incluyendo las 2 decimales)
				$ldec_monpat=$this->io_funciones->uf_rellenar_izq(round($ldec_monpat,2)," ",9);
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,8);
				$ls_apeper=$this->io_funciones->uf_rellenar_der($aa_ds_lph->data["apeper"][$li_i], " ", 20); //apellidos
				$ls_nomper=$this->io_funciones->uf_rellenar_der($aa_ds_lph->data["nomper"][$li_i], " ", 20); //nombres
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);
				$ld_fecnacper=substr($aa_ds_lph->data["fecnacper"][$li_i],0,10); //fecnacper debe estar formato (AAAAMMDD)
				$ld_fecnacper=$this->io_funciones->uf_trim(str_replace("-","",$ld_fecnacper)); //fecnacper debe estar formato (AAAAMMDD)
				$ls_cadena = "01".$ls_nacper.$ls_cedper.$ldec_monpat.$ldec_monper." ".$ls_apeper.$ls_nomper.$ls_sexper.$ld_fecnacper.$ld_desde."        "."    "."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_caja_familia
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_canarias($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_canarias
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco CANARIAS para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 31/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ld_desde=substr($ad_fecproc,0,10);
		$ls_periodo=str_replace("/","",$ad_fecproc);
		$ls_periodo=substr($ls_periodo,2,6);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_debcuelph="";
			$ls_numplalph="";
			$ls_numconlph="";
			$ls_suclph="";
			$ls_cuelph="";
			$ls_grulph="";
			$ls_subgrulph="";
			$ls_conlph="";
			$ls_numactlph="";
			$ls_codagelph="";
			$ls_apaposlph="";
			$lb_valido=$this->io_metbanco->uf_load_metodobanco_lph("CANARIAS","1",$ls_debcuelph,$ls_numplalph,$ls_numconlph,$ls_suclph,
																   $ls_cuelph,$ls_grulph,$ls_subgrulph,$ls_conlph,$ls_numactlph,$ls_codagelph,$ls_apaposlph);
			$ls_nombrearchivo=$as_ruta."/".$ls_numconlph.".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_riflet="";
			$ls_rifnum="";
			$ls_rifdig="";
			$this->uf_disgrega_rif($ls_riflet,$ls_rifnum,$ls_rifdig);	
			$ls_rif=$this->ls_rifemp; 
			$ls_rif=str_replace("-","",$ls_rif);			
			$ls_rif=$this->io_funciones->uf_rellenar_der(substr($ls_rif,0,10)," ",9);
			$ls_cuelph=$this->io_funciones->uf_rellenar_izq(substr($ls_cuelph,0,12)," ",12);
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   				
				$ld_fecingper=$aa_ds_lph->data["fechainst"][$li_i];
				$ld_mesingper=substr($ld_fecingper,5,2);
				$ld_anoingper=substr($ld_fecingper,0,4);
				$ld_mesfecdes=substr($ld_desde,3,2);
				$ld_anofecdes=substr($ld_desde,6,4);
				if(($ld_mesingper==$ld_mesfecdes)&&($ld_anoingper==$ld_anofecdes))
				{
					$ls_apertura="3";
				}
				else
				{
					$ls_apertura="1";
				}
				$ld_fecnacper=$this->io_funciones->uf_trim($aa_ds_lph->data["fecnacper"][$li_i]);  // fecha de nacimiento
				$li_dianacper=substr($ld_fecnacper,8,2);
				$li_mesnacper=substr($ld_fecnacper,5,2);
				$li_anonacper=substr($ld_fecnacper,0,4);
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); // nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); // cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_nomper=$this->io_funciones->uf_rellenar_der($aa_ds_lph->data["nomper"][$li_i], " ",30); // nombres
				$ls_apeper=$this->io_funciones->uf_rellenar_der($aa_ds_lph->data["apeper"][$li_i], " ",30); // apellidos
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);
				$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100); // Monto aporte-porcion empleado (incluyendo las 2 decimales)
				$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100); // Monto aporte-porcion patronal (incluyendo las 2 decimales)
				$ldec_monto=$this->io_funciones->uf_rellenar_izq(round($ldec_monper+$ldec_monpat,2),"0",12);
				$ls_cadena=$ls_rif.$ls_cuelph.$ls_nacper.$ls_cedper.$ls_nomper.$ls_apeper.$ls_sexper.$li_dianacper.
				           $li_mesnacper.$li_anonacper.$ls_apertura."3".$ldec_monto.$ls_periodo."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_canarias
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_casapropia($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_casapropia
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco CASA PROPIA para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 31/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=substr($ad_fecproc,0,10);
		$ls_periodo=str_replace("/","",$ad_fecproc);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo1=$as_ruta."/lph".substr($ld_desde,3,2).substr($ld_desde,8,2).".txt";
			$ls_nombrearchivo2=$as_ruta."/lph".substr($ld_desde,3,2).substr($ld_desde,8,2)."-p.txt";
			$ls_nombrearchivo3=$as_ruta."/lph".substr($ld_desde,3,2).substr($ld_desde,8,2)."-t.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo1"))
			{
				if(@unlink("$ls_nombrearchivo1")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo1 = @fopen("$ls_nombrearchivo1","a+");
				}
			}
			else
			{
				$ls_creararchivo1 = @fopen("$ls_nombrearchivo1","a+"); //creamos y abrimos el archivo para escritura
			}
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo2"))
			{
				if(@unlink("$ls_nombrearchivo2")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo2 = @fopen("$ls_nombrearchivo2","a+");
				}
			}
			else
			{
				$ls_creararchivo2 = @fopen("$ls_nombrearchivo2","a+"); //creamos y abrimos el archivo para escritura
			}
			
			if (file_exists("$ls_nombrearchivo3"))
			{
				if(@unlink("$ls_nombrearchivo3")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo3 = @fopen("$ls_nombrearchivo3","a+");
				}
			}
			else
			{
				$ls_creararchivo3 = @fopen("$ls_nombrearchivo3","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($ls_cedper,0,10),10);
				$ls_nomper=$this->io_funciones->uf_trim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$ls_apeper=$this->io_funciones->uf_trim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$ls_nombre=substr($ls_apeper. " ".$ls_nomper,0, 30);
				$ls_nombre=$this->io_funciones->uf_rellenar_der($ls_nombre, " ", 30);
				$ld_fecnacper=$this->io_funciones->uf_trim(substr($aa_ds_lph->data["fecnacper"][$li_i],0,10));  //fecha de nacimiento			
				$ld_fecnacper=str_replace("-","",$ld_fecnacper);  //fecha de nacimiento			
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo
				if ($ls_sexper=="M")
				{
					$ls_sexper = "1";
				}
				else
				{
					$ls_sexper = "2";
				}
				$li_tipnom=$this->io_funciones->uf_trim($aa_ds_lph->data["tipnom"][$li_i]);
				if (($li_tipnom == 1)||($li_tipnom == 3)||($li_tipnom == 5))
				{
					$ls_movimiento = "0";
				}
				else
				{
					$ls_movimiento = "2";
				}
				$li_staper=$this->io_funciones->uf_trim($aa_ds_lph->data["estper"][$li_i]); //status de personal
				if ($li_staper == 3)  //status de egresado
				{
					$ls_movimiento = "1";
				}		
				$ldec_monper=$this->io_funciones->uf_cerosizquierda((abs($aa_ds_lph->data["personal"][$li_i])*100),8);
				$ldec_monpat=$this->io_funciones->uf_cerosizquierda((abs($aa_ds_lph->data["patron"][$li_i])*100),8);
				$ldec_montperpat=$this->io_funciones->uf_cerosizquierda((abs($aa_ds_lph->data["personal"][$li_i]+$aa_ds_lph->data["patron"][$li_i])*100),8);
				$ld_fecret=substr($ld_desde,3,2).substr($ld_desde,8,2);
				$ls_cadena=str_replace("-","",$this->ls_rifemp)."00".$ls_nacper.$ls_cedper.$ls_nombre.$ld_fecnacper.$ls_sexper.
						   $ls_movimiento.$ldec_monper.$ld_fecret."3410"."          "."                                      "." "."\r\n";
				if ($ls_creararchivo1)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo1,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo1);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo1);
					$lb_valido = false;
				}

				//APORTES PATRONAL
				$ls_cadena=str_replace("-","",$this->ls_rifemp)."00".$ls_nacper.$ls_cedper.$ls_nombre.$ld_fecnacper.$ls_sexper.
						   $ls_movimiento.$ldec_monpat.$ld_fecret."3410"."          "."                                      "." "."\r\n";
				if ($ls_creararchivo2)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo2,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo2);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo2);
					$lb_valido = false;
				}
				//APORTES PATRONAL + APORTE PERSONAL
				$ls_cadena=str_replace("-","",$this->ls_rifemp)."00".$ls_nacper.$ls_cedper.$ls_nombre.$ld_fecnacper.$ls_sexper.
						   $ls_movimiento.$ldec_montperpat.$ld_fecret."3410"."          "."                                      "." "."\r\n";
				if ($ls_creararchivo3)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo3,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo3);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo3);
					$lb_valido = false;
				}
				
			}// fin del for	
			if ($lb_valido)
			{
				@fclose($ls_creararchivo1); //cerramos la conexiï¿½n y liberamos la memoria
				@fclose($ls_creararchivo2); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo1." fue creado.");
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo2." fue creado.");
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo3." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo1); //cerramos la conexiï¿½n y liberamos la memoria
				@fclose($ls_creararchivo2); //cerramos la conexiï¿½n y liberamos la memoria
				@fclose($ls_creararchivo3); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_casapropia
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_central($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_central
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco CENTRAL para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 01/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=substr($ad_fecproc,0,10);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/lphmovpr.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_rif=str_replace("-","",$this->ls_rifemp);
			$ls_rif=rtrim($ls_rif);
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   				
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
				$ls_nomper=rtrim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$ls_apeper=rtrim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$ls_nombre=substr($ls_apeper.", ".$ls_nomper,0, 30);
				$ls_nombre=$this->io_funciones->uf_rellenar_der($ls_nombre, " ", 30);
				$ld_fecnacper=$this->io_funciones->uf_trim(substr($aa_ds_lph->data["fecnacper"][$li_i],0,10));  //fecha de nacimiento			
				$ld_fecnacper=str_replace("-","",$ld_fecnacper);  //fecha de nacimiento			
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo
				$li_tipnom=$aa_ds_lph->data["tipnom"][$li_i];
				if(($li_tipnom==1)||($li_tipnom==3)||($li_tipnom==5)) 
				{
					$ls_movimiento="0";
				}
				else
				{
					$ls_movimiento="2";
				}
				$li_staper=$this->io_funciones->uf_trim($aa_ds_lph->data["estper"][$li_i]); //status de personal
				if($li_staper==3)  //status de egresado
				{
					$ls_movimiento="1";
				}			
				$ldec_monper=abs($aa_ds_lph->data["personal"][$li_i]*100);
				$ldec_monpat=abs($aa_ds_lph->data["patron"][$li_i]*100);
				$ldec_monto=number_format($ldec_monper+$ldec_monpat,0,".","");
				$ldec_monto=$this->io_funciones->uf_cerosizquierda($ldec_monto,8);
				$ld_fecret=substr($ld_desde,3,2).substr($ld_desde,8,2);
				$ls_cadena=$ls_rif."00".$ls_nacper.$ls_cedper.$ls_nombre.$ld_fecnacper.$ls_sexper.$ls_movimiento.
						   $ldec_monto.$ld_fecret."                                                     "."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_casapropia
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_delsur($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_delsur
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco Del Sur para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 01/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=substr($ad_fecproc,0,10);
		$ld_desde=str_replace("/","",$ld_desde);
		$ls_periodo=substr($ad_fecproc,0,10);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/lph-delsur-".substr($ls_periodo,8,2).substr($ls_periodo,3,2).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_debcuelph="";
			$ls_numplalph="";
			$ls_numconlph="";
			$ls_suclph="";
			$ls_cuelph="";
			$ls_grulph="";
			$ls_subgrulph="";
			$ls_conlph="";
			$ls_numactlph="";
			$ls_codagelph="";
			$ls_apaposlph="";			
			$lb_valido=$this->io_metbanco->uf_load_metodobanco_lph("DELSUR","1",$ls_debcuelph,$ls_numplalph,$ls_numconlph,$ls_suclph,
																   $ls_cuelph,$ls_grulph,$ls_subgrulph,$ls_conlph,$ls_numactlph,$ls_codagelph,$ls_apaposlph);
			$ls_riflet="";
			$ls_rifnum="";
			$ls_rifdig="";
			$this->uf_disgrega_rif($ls_riflet,$ls_rifnum,$ls_rifdig);	
			$ls_rif=$ls_riflet.$this->io_funciones->uf_cerosizquierda($ls_rifnum.$ls_rifdig,13);
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,12);			
				$ld_fecapo=substr($ld_desde,6,2).substr($ld_desde,2,2).substr($ld_desde,0,2);     //fecha de aporte yymmdd			
				$ls_nomper=$this->io_funciones->uf_trim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$ls_apeper=$this->io_funciones->uf_trim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$ls_nombre=$this->io_funciones->uf_rellenar_der($ls_apeper.", ".$ls_nomper, " ",75);			
				$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100);   //Monto aporte-porcion empleado (incluyendo las 2 decimales)
				$ldec_monper=$this->io_funciones->uf_rellenar_izq(round($ldec_monper,2),"0",10);
				$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100);   //Monto aporte-porcion patronal (incluyendo las 2 decimales)
				$ldec_monpat=$this->io_funciones->uf_rellenar_izq(round($ldec_monpat,2),"0",11);
				$ld_fecnacper=str_replace("-","",substr($aa_ds_lph->data["fecnacper"][$li_i],2,8));  //Fecha de nacimiento del empleado: aï¿½o, mes y dia
				$ld_fecnacper=$this->io_funciones->uf_trim($ld_fecnacper);  //Fecha de nacimiento del empleado: aï¿½o, mes y dia
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo del empleado M o F			
				$ls_cadena=$ls_rif.$ls_codagelph.$ls_nacper.$ls_cedper.$ld_fecapo.$ls_nombre.$ldec_monper.$ldec_monpat.
						   $ld_fecnacper.$ls_sexper.$ls_apaposlph."00000000"."1"."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_delsur
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_f_m_h($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_f_m_h
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco de FMH para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 01/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_periodo=substr($ad_fecproc,0,10);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/fmh_".substr($ls_periodo,3,2).substr($ls_periodo,0,2).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_periodo="0".substr($ls_periodo,8,2).substr($ls_periodo,3,2);
			$ls_codemp="002541220";
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   
				$ld_fecingper=substr($aa_ds_lph->data["fechainst"][$li_i],0,10);
			    $ld_mesingper=substr($ld_fecingper,5,2);
				$ld_anoingper=substr($ld_fecingper,0,4);
				$ld_mesfecdes=substr($ls_periodo,5,2);
				$ld_anofecdes=substr($ls_periodo,0,4);
				if(($ld_mesingper==$ld_mesfecdes)&&($ld_anoingper==$ld_anofecdes))
				{
					$ls_entrada = "2";
				}
				else
				{
					$ls_entrada = "0";
				}
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]);     //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]);     //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);			
				$ls_nomper=$this->io_funciones->uf_trim($aa_ds_lph->data["nomper"][$li_i]);     //nombres
				$ls_apeper=$this->io_funciones->uf_trim($aa_ds_lph->data["apeper"][$li_i]);     //apellidos
				$ls_nombre=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,40)," ",40);			
				$ld_fecnacper=$this->io_funciones->uf_trim(substr($aa_ds_lph->data["fecnacper"][$li_i],0,10));  //Fecha de nacimiento del empleado: aï¿½o, mes y dia
				$ld_fecnacper=substr(str_replace("-","",$ld_fecnacper),2,6);                                       //YYMMDD
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);     //sexo del empleado M o F
				$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100);       //Monto aporte-porcion empleado 
				$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100);       //Monto aporte-porcion patronal 
				$ldec_monto=$this->io_funciones->uf_rellenar_izq(round($ldec_monper+$ldec_monpat,2),"0",13);
				$ls_cadena=$ls_codemp.$ls_periodo.$ls_entrada.$ls_nacper.$ls_cedper.$ls_nombre."0".$ld_fecnacper.$ls_sexper.$ldec_monto."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_f_m_h
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_merenap($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_merenap
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco MERENAP para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 01/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_riflet="";
		$ls_rifnum="";
		$ls_rifdig="";
		$this->uf_disgrega_rif($ls_riflet,$ls_rifnum,$ls_rifdig);	
		$ls_rif=$ls_riflet.$this->io_funciones->uf_cerosizquierda($ls_rifnum.$ls_rifdig,12);
		$ls_rif=$this->io_funciones->uf_rellenar_der($ls_rif," ",13);
		$ls_periodo=substr($ad_fecproc,0,10);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/aporteah.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_debcuelph="";
			$ls_numplalph="";
			$ls_numconlph="";
			$ls_suclph="";
			$ls_cuelph="";
			$ls_grulph="";
			$ls_subgrulph="";
			$ls_conlph="";
			$ls_numactlph="";
			$ls_codagelph="";
			$ls_apaposlph="";			
			$lb_valido=$this->io_metbanco->uf_load_metodobanco_lph("MERENAP","1",$ls_debcuelph,$ls_numplalph,$ls_numconlph,$ls_suclph,
																   $ls_cuelph,$ls_grulph,$ls_subgrulph,$ls_conlph,$ls_numactlph,$ls_codagelph,$ls_apaposlph);
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   
				$ls_coduni="00000000";
				$ls_staper=$aa_ds_lph->data["estper"][$li_i];
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,12);
				$ld_fecapo=str_replace("/","",$ls_periodo);                     //fecha de aporte yymmdd					
				$ld_fecapo=substr($ld_fecapo,8,2).substr($ld_fecapo,2,2).substr($ld_fecapo,0,2);                     //fecha de aporte yymmdd					
				$ls_nomper=$this->io_funciones->uf_trim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$ls_apeper=$this->io_funciones->uf_trim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$ls_nombre=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,75)," ",75);			
				$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100);   //Monto aporte-porcion empleado (incluyendo las 2 decimales)
				$ldec_monper=$this->io_funciones->uf_rellenar_izq(round($ldec_monper,2),"0",10);
				$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100);   //Monto aporte-porcion patronal (incluyendo las 2 decimales)
				$ldec_monpat=$this->io_funciones->uf_rellenar_izq(round($ldec_monpat,2),"0",11);
				$ld_fecnacper=$this->io_funciones->uf_trim(substr($aa_ds_lph->data["fecnacper"][$li_i],0,10));  //Fecha de nacimiento del empleado: aï¿½o, mes y dia
				$ld_fecnacper=substr(str_replace("-","",$ld_fecnacper),2,6);
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo del empleado M o F			
				$ls_cadena=$ls_rif.$ls_codagelph.$ls_nacper.$ls_cedper.$ld_fecapo.$ls_nombre.$ldec_monper.$ldec_monpat.$ld_fecnacper.$ls_sexper.$ls_apaposlph.$ls_coduni.$ls_staper."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_merenap
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_miranda($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_merenap
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco MIRANDA para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 04/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=$ad_fecproc;
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/phab".substr($ld_desde,3,2).substr($ld_desde,0,2).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_riflet="";
			$ls_rifnum="";
			$ls_rifdig="";
			$this->uf_disgrega_rif($ls_riflet,$ls_rifnum,$ls_rifdig);	
			$ls_rif=$this->io_funciones->uf_rellenar_der($ls_rifnum.$ls_rifdig, " ", 9); //Numero de RIF Empresa
			$ls_periodo=substr($ld_desde,3,2).substr($ld_desde,0,2);
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   
				$ld_fecingper=$aa_ds_lph->data["fecingper"][$li_i]; 
				$ld_mesingper=substr($ld_fecingper,3,2);
				$ld_anoingper=substr($ld_fecingper,6,4);
				$ld_mesfecdes=substr($ld_desde,3,2);
				$ld_anofecdes=substr($ld_desde,6,4);
				if(($ld_mesingper==$ld_mesfecdes)&&($ld_anoingper==$ld_anofecdes))
				{
					$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
					$ls_cedper=$this->io_funciones->uf_trim(str_pad($aa_ds_lph->data["cedper"][$li_i],8,"0",0)); //cedula
					$ls_nomper=$this->io_funciones->uf_trim($aa_ds_lph->data["nomper"][$li_i]); //nombres
					$ls_apeper=$this->io_funciones->uf_trim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
					$ls_nombre=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,30), " ", 30);
					$ld_fecnacper=$this->io_funciones->uf_trim(str_replace("-","",$aa_ds_lph->data["fecnacper"][$li_i]));	//Fecha de nacimiento del empleado AAAAMMDD
					$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo del empleado M o F
					$ls_tipmov="2";              //Tipo de Movimiento 2=Ingreso 0=Aporte
					$ls_space7="       ";        //Localidad
					$ls_space8="        ";       //Monto aporte mensual 
					$ls_space48="                                                "; //uso futuro
					$ls_cadena=$ls_riflet.$ls_rif."  ".$ls_nacper.$ls_cedper.$ls_nombre.$ld_fecnacper.$ls_sexper.$ls_tipmov.$ls_space8.$ls_periodo.$ls_space7.$ls_space48."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim(str_pad($aa_ds_lph->data["cedper"][$li_i],8,"0",0)); //cedula
				$ls_space30="                              ";
				$ls_space8="        ";
				$ls_space1=" ";
				$ls_tipmov="0";                              //Tipo de Movimiento 2=Ingreso 0=Aporte
				$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100);   //Monto aporte-porcion empleado (incluyendo las 2 decimales)
				$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100);   //Monto aporte-porcion patronal (incluyendo las 2 decimales)
				$ldec_monto=$this->io_funciones->uf_cerosizquierda(round($ldec_monpat+$ldec_monper,2),8);
				$ls_space7="       ";        //Localidad
				$ls_space48="                                                "; //uso futuro
				$ls_cadena=$ls_riflet.$ls_rif."  ".$ls_nacper.$ls_cedper.$ls_space30.$ls_space8.$ls_space1.$ls_tipmov.$ldec_monto.$ls_periodo.$ls_space7.$ls_space48."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_miranda
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_mi_casa_eap($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_mi_casa_eap
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco MI CASA EAP para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 04/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=$ad_fecproc;
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/phab".substr($ld_desde,3,2).substr($ld_desde,0,2).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_riflet="";
			$ls_rifnum="";
			$ls_rifdig="";
			$this->uf_disgrega_rif($ls_riflet,$ls_rifnum,$ls_rifdig);	
			$ls_rif=$this->io_funciones->uf_rellenar_der($ls_rifnum.$ls_rifdig, " ", 9); //Numero de RIF Empresa
			$li_anofec=substr($ld_desde,0,4); // aï¿½o 4 digitos
			$li_anomes=substr($ld_desde,5,2); // aï¿½o y mes 
			if (intval($li_anofec)>=2000)
			{
				$ls_ano="1";
			}
			else
			{
				$ls_ano="0";
			}
			$ls_periodo=$ls_ano.substr(str_replace("/","",$ld_desde),6,2).substr(str_replace("/","",$ld_desde),2,2); //Periodo retencion  Mes/Aï¿½o del Aporte (OOOJJJOOO orden de la fecha yyyymm)
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   
				$ld_fecingper=$aa_ds_lph->data["fechainst"][$li_i];
				$ld_mesingper=substr($ld_fecingper,5,2);
				$ld_anoingper=substr($ld_fecingper,0,4);
				$ld_mesfecdes=substr($ld_desde,3,2);
				$ld_anofecdes=substr($ld_desde,6,4);
				if(($ld_mesingper==$ld_mesfecdes)&&($ld_anoingper==$ld_anofecdes))
				{
					$ls_apertura = "2";
				}
				else
				{
					$ls_apertura = "0";
				}
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_nomper=$this->io_funciones->uf_trim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$ls_apeper=$this->io_funciones->uf_trim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$ls_nombre=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,40), " ", 40);		
				$ld_fecnacper=$this->io_funciones->uf_trim($aa_ds_lph->data["fecnacper"][$li_i]);	
				$li_anofecnac=substr($ld_fecnacper,0,4);
				if(intval($li_anofecnac)>=2000)
				{
					$ls_anofecnac="1";
				}
				else
				{
					$ls_anofecnac="0";
				}
				$ls_fecnac=$ls_anofecnac.substr(str_replace("-","",$ld_fecnacper), 2, 6); //  yymmdd
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo del empleado M o F
				$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100);   //Monto aporte-porcion empleado 				
				$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100);   //Monto aporte-porcion patronal 
				$ldec_monto=$this->io_funciones->uf_rellenar_izq(round($ldec_monper+$ldec_monpat,2),"0",13);			
				$ls_cadena=$ls_rif.$ls_periodo.$ls_apertura.$ls_nacper.$ls_cedper.$ls_nombre.$ls_fecnac.$ls_sexper.$ldec_monto."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_mi_casa_eap
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_vivienda($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_vivienda
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco VIVIENDA para los aportes de LPH.
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 04/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=$ad_fecproc;
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/habita.txt";
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_riflet="";
			$ls_rifnum="";
			$ls_rifdig="";
			$this->uf_disgrega_rif($ls_riflet,$ls_rifnum,$ls_rifdig);	
			$ls_rif=$ls_riflet.$ls_rifnum.$ls_rifdig; //Numero de RIF Empresa 				
			$ldec_totpatron=$this->io_funciones->uf_trim(abs($aa_ds_lph->data["totalpatron"][1]));    //suma de aporte patron ultima fila del ds
			$ldec_totpersonal=$this->io_funciones->uf_trim(abs($aa_ds_lph->data["totalpersonal"][1]));  //suma de aporte personal ultima fila del ds
			$ldec_totaporte=round(($ldec_totpatron + $ldec_totpersonal),2);
			$ldec_totaporte=$this->io_funciones->uf_cerosizquierda(($ldec_totaporte*100),11); 
			$ld_fecefectiva=substr($ld_desde,3,2).substr($ld_desde,8,2);
			$ls_space         = "                                                                                                   "; //99 espacios
			$ls_cadena="C".$ls_rif."0"."1"."2".$ldec_totaporte.$ld_fecefectiva.$ls_space."\r\n";
			if ($ls_creararchivo)  //Chequea que el archivo este abierto				
			{
				if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
				{
					$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			else
			{
				$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
				$lb_valido = false;
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$li_acumulado=$aa_ds_lph->data["acumulado"][$li_i];
				$li_acumuladoinicial=$aa_ds_lph->data["acumuladoinicial"][$li_i];
				$li_total=abs($li_acumulado+$li_acumuladoinicial);
				if($li_total==0)
				{
					$ld_fecnacper=$this->io_funciones->uf_trim(str_replace("-","",$aa_ds_lph->data["fecnacper"][$li_i]));  //AAAAMMDD
					$ld_fecnacper=substr($ld_fecnacper,6,2).substr($ld_fecnacper,4,2).substr($ld_fecnacper,2,2); //DDMMAA
					$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
					$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
					$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
					$ls_nomper=$this->io_funciones->uf_trim($aa_ds_lph->data["nomper"][$li_i]); //nombres
					$ls_apeper=$this->io_funciones->uf_trim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
					$ls_nombre=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,30), " ", 30);
					$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo del empleado M o F
					$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100);   //Monto aporte-porcion empleado 				
					$ldec_monper=$this->io_funciones->uf_cerosizquierda($ldec_monper,8);
					$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100);   //Monto aporte-porcion patronal 
					$ldec_monpat=$this->io_funciones->uf_cerosizquierda($ldec_monpat, 7);
					$ls_space43="                                           "; //43
					$ls_space10="          "; //Cuenta L.P.H. (reservado para ser llenado por el banco)
					$ls_cadena="D".$ls_cedper.$ls_nacper.$ls_nombre.$ld_fecnacper.$ls_sexper.$ls_space10.$ldec_monpat.$ldec_monper.$ls_space43.$ls_space10."1"."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			} 
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   
				$li_acumulado=$aa_ds_lph->data["acumulado"][$li_i];
				$li_acumuladoinicial=$aa_ds_lph->data["acumuladoinicial"][$li_i];
				$li_total=abs($li_acumulado+$li_acumuladoinicial);
				if($li_total>0)
				{
					$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
					$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
					$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
					$ldec_monper=(abs($aa_ds_lph->data["personal"][$li_i])*100);   //Monto aporte-porcion empleado 				
					$ldec_monper=$this->io_funciones->uf_cerosizquierda($ldec_monper,8);
					$ldec_monpat=(abs($aa_ds_lph->data["patron"][$li_i])*100);   //Monto aporte-porcion patronal 
					$ldec_monpat=$this->io_funciones->uf_cerosizquierda($ldec_monpat, 7);
					$ls_space53="                                                     "; //53
					$ls_space10="          "; //Localidad
					$ls_space37="                                     "; //Espacio libre
					$ls_cadena="D".$ls_cedper.$ls_nacper.$ls_space37.$ls_space10.$ldec_monpat.$ldec_monper.$ls_space53."2"."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_vivienda
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_fondocomun_eap($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_fondocomun_eap
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecproc // Fecha de Procesamiento
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco CENTRAL para los aportes de LPH.
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 14/03/2007 								
		// Modificado Por: 													Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=substr($ad_fecproc,0,10);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/nomi".substr($ld_desde,3,2).substr($ld_desde,8,2).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_rif=str_replace("-","",$this->ls_rifemp);
			$ls_rif=rtrim($ls_rif);
			$ls_letrarif=substr($ls_rif,0,1);
			$ls_numrif=str_pad(substr($ls_rif,1,9),9,"0",0);
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   				
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
				$ls_nomper=rtrim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$ls_apeper=rtrim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$ls_nombre=substr($ls_apeper." ".$ls_nomper,0, 30);
				$ls_nombre=$this->io_funciones->uf_rellenar_der($ls_nombre, " ", 30);
				$ld_fecnacper=substr($aa_ds_lph->data["fecnacper"][$li_i],0,10);  //fecha de nacimiento			
				$ld_fecnacper=str_replace("-","",$ld_fecnacper);  //fecha de nacimiento			
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo
				if ($ls_sexper=="F")
				{
					$ls_sexper="2";
				}
				else
				{
					$ls_sexper="1";
				}
				$ls_movimiento="0";
				$ldec_monper=number_format(abs($aa_ds_lph->data["personal"][$li_i]*100),2,".","");
				$ldec_monpat=number_format(abs($aa_ds_lph->data["patron"][$li_i]*100),2,".","");
				$ldec_monto=$this->io_funciones->uf_cerosizquierda($ldec_monper+$ldec_monpat,10);
				$ld_fecret=substr($ld_desde,3,2).substr($ld_desde,8,2);
				$ls_cadena=$ls_letrarif.$ls_numrif."00".$ls_nacper.$ls_cedper.$ls_nombre.$ld_fecnacper.$ls_sexper.$ls_movimiento.$ldec_monto.$ld_fecret."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_fondocomun_eap
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_fondocomun_mre($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_fondocomun
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecproc // Fecha de Procesamiento
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco CENTRAL para los aportes de LPH.
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 18/03/2009 								
		// Modificado Por: 													Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=substr($ad_fecproc,0,10);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/nomi".substr($ld_desde,3,2).substr($ld_desde,8,2).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_rif=str_replace("-","",$this->ls_rifemp);
			$ls_rif=rtrim($ls_rif);
			$ls_letrarif=substr($ls_rif,0,1);
			$ls_numrif=str_pad(substr($ls_rif,1,9),9,"0",0);
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   				
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
				$ls_nomper=rtrim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$ls_apeper=rtrim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$ls_nombre=substr($ls_apeper." ".$ls_nomper,0, 30);
				$ls_nombre=$this->io_funciones->uf_rellenar_der($ls_nombre, " ", 30);
				$ld_fecnacper=substr($aa_ds_lph->data["fecnacper"][$li_i],0,10);  //fecha de nacimiento			
				$ld_fecnacper=str_replace("-","",$ld_fecnacper);  //fecha de nacimiento			
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo
				if ($ls_sexper=="F")
				{
					$ls_sexper="2";
				}
				else
				{
					$ls_sexper="1";
				}
				$ls_movimiento="0";
				$ldec_monper=number_format(abs($aa_ds_lph->data["personal"][$li_i]),2,".","");
				$ldec_monpat=number_format(abs($aa_ds_lph->data["patron"][$li_i]),2,".","");
				$ldec_monto=($ldec_monper+$ldec_monpat);
				$ldec_monto=number_format(($ldec_monto),2,"","");
				$ldec_monto=substr($ldec_monto,0,4);
				$ldec_monto=str_pad($ldec_monto,4,"0",0);
				$ld_fecret=substr($ld_desde,3,2).substr($ld_desde,8,2);
				$ls_cadena=$ls_letrarif.$ls_numrif."00".$ls_nacper.$ls_cedper.$ls_nombre."19700101100000".$ldec_monto.$ld_fecret."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_fondocomun
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_bod($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_bod
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecproc // Fecha de Procesamiento
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco BOD para los aportes de LPH.
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 09/04/2007 								
		// Modificado Por: 													Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=substr($ad_fecproc,0,10);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/habpriv.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_rif=str_replace("-","",$this->ls_rifemp);
			$ls_rif=rtrim($ls_rif);
			$ls_letrarif=substr($ls_rif,0,1);
			$ls_numrif=str_pad(substr($ls_rif,1,9),9,"0",0);
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   				
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($ls_cedper,0,10),10);
				$ls_nomper=rtrim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$ls_apeper=rtrim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$ls_nombre=substr($ls_apeper." ".$ls_nomper,0,30);
				$ls_nombre=$this->io_funciones->uf_rellenar_der($ls_nombre," ",30);
				$ld_fecnacper=substr($aa_ds_lph->data["fecnacper"][$li_i],0,10);  //fecha de nacimiento			
				$ld_fecnacper=str_replace("-","",$ld_fecnacper);  //fecha de nacimiento			
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo
				$ls_movimiento="0";
				$ldec_monper=abs($aa_ds_lph->data["personal"][$li_i]*100);
				$ldec_monpat=abs($aa_ds_lph->data["patron"][$li_i]*100);
				$ldec_monto=$this->io_funciones->uf_cerosizquierda($ldec_monper+$ldec_monper,15);
				$ld_fecret=substr($ld_desde,3,2).substr($ld_desde,8,2);
				$ls_blanco="                                               ";
				$ls_cadena=$ls_letrarif.$ls_numrif."00".$ls_nacper.$ls_cedper.$ls_nombre.$ld_fecnacper.$ls_sexper.$ls_movimiento.$ldec_monto.$ld_fecret.$ls_blanco."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_bod
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_banavih($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_banavih
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecproc // Fecha de Procesamiento
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco BOD para los aportes de LPH.
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 09/04/2007 								
		// Modificado Por: 													Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_desde=substr($ad_fecproc,0,10);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/lph-banavih.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_rif=str_replace("-","",$this->ls_rifemp);
			$ls_rif=rtrim($ls_rif);
			$ls_letrarif=substr($ls_rif,0,1);
			$ls_numrif=str_pad(substr($ls_rif,1,9),9,"0",0);
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   				
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($ls_cedper,0,10),10);
				$ls_statusper=$this->io_funciones->uf_trim($aa_ds_lph->data["estper"][$li_i]); //estatus
				$ls_minorguniadm=$this->io_funciones->uf_trim($aa_ds_lph->data["minorguniadm"][$li_i]); //estatus
				$ls_minorguniadm=substr($ls_minorguniadm,0,2);
				$ls_ofiuniadm=$this->io_funciones->uf_trim($aa_ds_lph->data["ofiuniadm"][$li_i]); //estatus
				$ls_uniuniadm=$this->io_funciones->uf_trim($aa_ds_lph->data["uniuniadm"][$li_i]); //estatus
				$ls_depuniadm=$this->io_funciones->uf_trim($aa_ds_lph->data["depuniadm"][$li_i]); //estatus
				$ls_prouniadm=$this->io_funciones->uf_trim($aa_ds_lph->data["prouniadm"][$li_i]); //estatus
				$ls_localidad=$ls_minorguniadm.$ls_ofiuniadm.$ls_uniuniadm.$ls_depuniadm.$ls_prouniadm;
				$ls_nomper=rtrim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$ls_apeper=rtrim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$ls_nombre=substr($ls_apeper." ".$ls_nomper,0,30);
				$ls_nombre=$this->io_funciones->uf_rellenar_der($ls_nombre," ",30);
				$ld_fecnacper=substr($aa_ds_lph->data["fecnacper"][$li_i],0,10);  //fecha de nacimiento			
				$ld_fecnacper=str_replace("-","",$ld_fecnacper);  //fecha de nacimiento			
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo
				if ($ls_sexper=='M')
				{
					$ls_sexper='1';
				}
				else
				{
					$ls_sexper='2';
				}
				$ls_movimiento="0";
				$ldec_monper=abs($aa_ds_lph->data["personal"][$li_i]*100);
				$ldec_monpat=abs($aa_ds_lph->data["patron"][$li_i]*100);
				$li_monto=$ldec_monper+$ldec_monpat;
				$li_montoesp=number_format($li_monto,2,".","");
				$li_montoesp=str_replace(".","",$li_montoesp);
				$ldec_monto=$this->io_funciones->uf_cerosizquierda($li_montoesp,10);
				$ld_fecret=substr($ld_desde,3,2).substr($ld_desde,8,2);
				$ls_cadena=$ls_letrarif.$ls_numrif."00".$ls_nacper.$ls_cedper.$ls_nombre.$ld_fecnacper.$ls_sexper.$ls_statusper.$ldec_monto.$ld_fecret.$ls_localidad."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_bod
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_lph_banavih2($as_ruta,$ad_fecproc,$aa_ds_lph)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_lph_banavih2
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecproc // Fecha de Procesamiento
		//                 aa_ds_lph // arreglo (datastore) datos LPH   
		//	  Description: Metodo que genera el archivo txt a disco para el banco BOD para los aportes de LPH.
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 09/04/2007 								
		// Modificado Por: 													Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("sigesp_sno.php");
		$io_sno=new sigesp_sno();
		$lb_valido=true;
		$ld_desde=substr($ad_fecproc,0,10);
		$li_count=$aa_ds_lph->getRowCount("cedper");
		if($li_count>0)
		{	
			
			$ls_mes_txt=substr($ad_fecproc,3,2);
			$ls_ano_txt=substr($ad_fecproc,6,4);
			$ls_ctanomina=$io_sno->uf_select_config("SNO","NOMINA","CTA. BANAVIH 2.0","NORMAL","C");
			$ls_nombrearchivo=$as_ruta."/N".str_replace("-","",$ls_ctanomina).$ls_mes_txt.$ls_ano_txt.".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_rif=str_replace("-","",$this->ls_rifemp);
			$ls_rif=rtrim($ls_rif);
			$ls_letrarif=substr($ls_rif,0,1);
			$ls_numrif=str_pad(substr($ls_rif,1,9),9,"0",0);
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{   				
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_lph->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_lph->data["cedper"][$li_i]); //cedula
				$ls_statusper=$this->io_funciones->uf_trim($aa_ds_lph->data["estper"][$li_i]); //estatus
				$ls_minorguniadm=$this->io_funciones->uf_trim($aa_ds_lph->data["minorguniadm"][$li_i]); //estatus
				$ls_minorguniadm=substr($ls_minorguniadm,0,2);
				$ls_ofiuniadm=$this->io_funciones->uf_trim($aa_ds_lph->data["ofiuniadm"][$li_i]); //estatus
				$ls_uniuniadm=$this->io_funciones->uf_trim($aa_ds_lph->data["uniuniadm"][$li_i]); //estatus
				$ls_depuniadm=$this->io_funciones->uf_trim($aa_ds_lph->data["depuniadm"][$li_i]); //estatus
				$ls_prouniadm=$this->io_funciones->uf_trim($aa_ds_lph->data["prouniadm"][$li_i]); //estatus
				$ls_localidad=$ls_minorguniadm.$ls_ofiuniadm.$ls_uniuniadm.$ls_depuniadm.$ls_prouniadm;
				$ls_nomper=rtrim($aa_ds_lph->data["nomper"][$li_i]); //nombres
				$pos1 =strpos(rtrim($ls_nomper)," ",0);
				$ls_nomper1=substr(rtrim($ls_nomper),0,$pos1);  //Nombre de Persona 1
				$ls_nomper2=substr(ltrim(rtrim($ls_nomper)),$pos1+1,60);  //Nombre de Persona 2
				if($pos1==0)
				{
					$ls_nomper1=$ls_nomper;
					$ls_nomper2="";
				}
				$ls_apeper=rtrim($aa_ds_lph->data["apeper"][$li_i]); //apellidos
				$pos2 =strpos(rtrim($ls_apeper),' ',0);
				$ls_apeper1=substr(rtrim($ls_apeper),0,$pos2);  //Apellido de Persona 1
				$ls_apeper2=substr(ltrim(rtrim($ls_apeper)),$pos2+1,60);  //Apellido de Persona 2
				if($pos2==0)
				{
					$ls_apeper1=$ls_apeper;
					$ls_apeper2="";
				}
				$ld_fecnacper=substr($aa_ds_lph->data["fecnacper"][$li_i],0,10);  //fecha de nacimiento			
				$ld_fecnacper=str_replace("-","",$ld_fecnacper);  //fecha de nacimiento			
				$ld_fecingper=substr($aa_ds_lph->data["fechainst"][$li_i],0,10);  //fecha de nacimiento
				$ld_fecingper=str_replace("-","",$ld_fecingper);  //fecha de nacimiento	
				$ld_fecingper=substr($ld_fecingper,6,2).substr($ld_fecingper,4,2).substr($ld_fecingper,0,4);
				$ld_fecegrper=substr($aa_ds_lph->data["fecegrper"][$li_i],0,10);  //fecha de nacimiento			
				$ld_fecegrper=str_replace("-","",$ld_fecegrper);  //fecha de nacimiento	
				$ld_fecegrper=substr($ld_fecegrper,6,2).substr($ld_fecegrper,4,2).substr($ld_fecegrper,0,4);
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_lph->data["sexper"][$li_i]);  //sexo
				if ($ls_sexper=='M')
				{
					$ls_sexper='1';
				}
				else
				{
					$ls_sexper='2';
				}
				$ls_movimiento="0";
				$ldec_monper=abs($aa_ds_lph->data["personal"][$li_i]*100);
				$ldec_monpat=abs($aa_ds_lph->data["patron"][$li_i]*100);
				$li_monto=$ldec_monper+$ldec_monpat;
				$li_montoesp=number_format($li_monto,2,".","");
				$ac="00";
				$li_sueper=$aa_ds_lph->data["sueintper"][$li_i];
				$li_banavih=$aa_ds_lph->data["sueintbanavih"][$li_i];
				$li_sueper=number_format($li_sueper,2,".","");
				$li_sueper=str_replace(".","",$li_sueper);
				$li_banavih=number_format($li_banavih,2,".","");
				$li_banavih=str_replace(".","",$li_banavih);

                              //  $sueldo_int = number_format($li_sueper,0,".","");
                               // $sueldo_int = str_replace(".","",$sueldo_int);

				$li_montoesp=str_replace(".","",$li_montoesp);
				$ldec_monto=$this->io_funciones->uf_cerosizquierda($li_montoesp,10);
				$ld_fecret=substr($ld_desde,3,2).substr($ld_desde,8,2);
				if($ld_fecegrper=="01011900")
				{
					$ld_fecegrper="";
				}
				if($ls_nomper2=="")
				{
					$ls_nomper2=" ";
				}
				if($ls_apeper2=="")
				{
					$ls_apeper2=" ";
				}
                                //  ANTES MOSTRABA EL SUELDO BANAVIH $li_banavih, PERO ES EL SUELDO INTEGRAL QUE DEBE TOMAR
				$ls_cadena=$ls_nacper.",".$ls_cedper.",".$ls_nomper1.",".$ls_nomper2.",".$ls_apeper1.",".$ls_apeper2.",".$li_sueper.",".$ld_fecingper.",".$ld_fecegrper."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_lph_banavih2
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_fpa($as_ruta,$as_metodo,$aa_ds_fpa,$ad_fecproc,$as_codconc,$as_codnomdes,$as_codnomhas,$as_anocur,
						   $as_perdes,$as_perhas,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fpa	
		//	    Arguments: as_ruta   // ruta donde se va aguardar el archivo
		//	    		   as_metodo   // Cï¿½digo del metodo a banco
		//                 aa_ds_fpa // arreglo (datastore) datos fpa      
		//	    		   ad_fecpro // Fecha de la Nï¿½mina
		//	    		   as_codconc // Cï¿½digo del concepto del que se desea busca el personal
		//	    		   as_codnomdes // Cï¿½digo Nï¿½mina Desde
		//	    		   as_codnomhas // Cï¿½digo Nï¿½mina Hasta
		//	    		   as_ano // Aï¿½o en curso
		//	    		   as_perdes // Perï¿½odo Desde
		//	    		   as_perhas // Perï¿½odo Hasta
		//				   aa_seguridad // arreglo de seguridad
		//	      Returns: lb_valido True 
		//	  Description: Funcion que segun el banco, genera un archivo txt a disco para cancelaciï¿½n de ley de polï¿½tica
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 30/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
              //echo $as_metodo;  die();
	  	switch ($as_metodo)
		{
			case "VENEZUELA":
				$lb_valido=$this->uf_metodo_fpa_venezuela($as_ruta,$aa_ds_fpa);
				break;

			case "MERCANTIL":
				$lb_valido=$this->uf_metodo_fpa_mercantil($as_ruta,$aa_ds_fpa);
				break;
			
			case "CENTRAL":
				$lb_valido=$this->uf_metodo_fpa_central($as_ruta,$aa_ds_fpa);
				break;

                        case "METODO MINCULTURA":
				$lb_valido=$this->uf_metodo_fpa_mincultura($as_ruta,$aa_ds_fpa);
				break;

			default:
				$this->io_mensajes->message("El mï¿½todo seleccionado no esta disponible.");
				break;
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Generï¿½ el disco de LPH.Concepto ".$as_codconc." Nï¿½mina Desde ".$as_codnomdes." Nï¿½mina Hasta ".$as_codnomhas."<br>".
							 " Aï¿½o ".$as_anocur." Periodo Desde ".$as_perdes." Periodo Hasta ".$as_perhas;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
	}// end function uf_metodo_fpa
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_fpa_venezuela($as_ruta,$aa_ds_fpa)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fpa_venezuela
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_fpa // arreglo (datastore) datos LPH   
		//	  Description: genera el archivo txt a disco para  el banco BOD para pago de nomina
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_count=$aa_ds_fpa->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/bvzla.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_numplan="00001";
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fpa->data["cedper"][$li_i]); //cedula
				$ls_cedper=str_pad(substr($ls_cedper,0,9),9,"0",0); //cedula
				$ls_nacper= $this->io_funciones->uf_trim($aa_ds_fpa->data["nacper"][$li_i]); //nacionalidad
				$ls_cuecajahoper=substr($this->io_funciones->uf_trim($aa_ds_fpa->data["cuecajahoper"][$li_i]),0,10); //nacionalidad
				$ls_cuecajahoper=str_pad($ls_cuecajahoper,10,"0",0); //nacionalidad
				$ldec_monper=(abs($aa_ds_fpa->data["personal"][$li_i])*100);   //Monto aporte-porcion empleado 				
				$ldec_monpat=(abs($aa_ds_fpa->data["patron"][$li_i])*100);   //Monto aporte-porcion patronal 
				$ld_total=$this->io_funciones->uf_rellenar_izq($ldec_monper+$ldec_monpat,"0",13);
				$ls_cadena=$ls_numplan.$ls_nacper.$ls_cedper."1"."00"." ".$ld_total."N"."1".$ls_cuecajahoper."000000"." "."000"."0000000000000"."000"."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fpa_venezuela
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_fpa_mercantil($as_ruta,$aa_ds_fpa)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fpa_mercantil
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_fpa // arreglo (datastore) datos LPH   
		//	  Description: genera el archivo txt a disco para  el banco BOD para pago de nomina
		//	   Creado Por: Ing. Marï¿½a Roa
		// Fecha Creaciï¿½n: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ï¿½ltima Modificaciï¿½n : 20/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_count=$aa_ds_fpa->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/aporte.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ld_fecha=date("dmY");
				$ls_numplan="12";
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fpa->data["cedper"][$li_i]); //cedula
				$ls_cedper=str_pad(substr($ls_cedper,0,9),9,"0",0); //cedula
				$ls_nacper= $this->io_funciones->uf_trim($aa_ds_fpa->data["nacper"][$li_i]); //nacionalidad
				$ls_cuecajahoper=substr($this->io_funciones->uf_trim($aa_ds_fpa->data["cuecajahoper"][$li_i]),0,6); // cuenta caja de ahorro
				$ls_cuecajahoper=str_pad($ls_cuecajahoper,6,"0",0); //nacionalidad
				$ldec_monper=(abs($aa_ds_fpa->data["personal"][$li_i])*100);   //Monto aporte-porcion empleado 				
				$ldec_monpat=(abs($aa_ds_fpa->data["patron"][$li_i])*100);   //Monto aporte-porcion patronal 
				$ld_total=$this->io_funciones->uf_rellenar_izq($ldec_monper+$ldec_monpat,"0",13);
				$ls_cadena=$ls_numplan.$ld_fecha."1".$ls_cuecajahoper.$ls_nacper.$ls_cedper."0".$ld_total."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}//fin del for
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fpa_mercantil
	//-----------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------

    function uf_metodo_fpa_central($as_ruta,$aa_ds_fpa)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fpa_central
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 aa_ds_fpa // arreglo (datastore) datos LPH   
		//	  Description: genera el archivo txt a disco para  el banco Central Banco universal
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaciï¿½n: 20/08/2008	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_count=$aa_ds_fpa->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/aporte.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fpa->data["cedper"][$li_i]); //cedula
				$ls_cedper=str_pad($ls_cedper,9," ",'LEFT'); //cedula				
				$ldec_monper=(abs($aa_ds_fpa->data["personal"][$li_i])*100);   //Monto aporte-porcion empleado 				
				$ldec_monpat=(abs($aa_ds_fpa->data["patron"][$li_i])*100);   //Monto aporte-porcion patronal 
				$ld_total=$ldec_monper+$ldec_monpat;
				$ld_total=str_pad($ld_total,10," ",'LEFT');
				$ls_nomper=$aa_ds_fpa->data["nomper"][$li_i];			
				$ls_apeper=$aa_ds_fpa->data["apeper"][$li_i];
				$nomape=$ls_apeper.", ".$ls_nomper;
				$nomape=str_pad($nomape,40," ");
			    $nomape=substr($nomape,0,40);
				$ls_cadena=$ls_cedper.$ld_total."  ".$nomape."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}//fin del for
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fpa_central
//------------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------

    function uf_metodo_fpa_mincultura($as_ruta,$aa_ds_fpa)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fpa_central
		//		   Access: private
		//	    Arguments: as_ruta  // ruta
		//                 aa_ds_fpa // arreglo (datastore) datos LPH
		//	  Description: genera el archivo txt a disco segun un metodo especifico definido por el ministerio de cultura.
                //	  solo para el aporte de caja de ahorro
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaciï¿½n: 20/08/2008	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_count=$aa_ds_fpa->getRowCount("cedper");
		if($li_count>0)
		{//sno/sigesp_snorh_p_seleccionarhnomina.php
			$ls_nombrearchivo=$as_ruta."/aporte.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
                               
                                $ls_mes_aporte =  $_SESSION["mes_aporte"]; 
                                $ls_anio_aporte = $aa_ds_fpa->data["anocur"][$li_i];
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fpa->data["cedper"][$li_i]); //cedula
				$ls_cedper=str_pad($ls_cedper,9," ",'LEFT'); //cedula

				$ldec_monper=(abs($aa_ds_fpa->data["personal"][$li_i]));   //Monto aporte-porcion empleado
				$ldec_monpat=(abs($aa_ds_fpa->data["patron"][$li_i]));   //Monto aporte-porcion patronal

                                $ldec_monper =number_format($ldec_monper,2,'.','');
                                $ldec_monpat =number_format($ldec_monpat,2,'.','');
                                $ld_total=$ldec_monper+$ldec_monpat;
                                $ld_total=str_pad($ld_total,10," ",'LEFT');
                                $ls_nomper=$aa_ds_fpa->data["nomper"][$li_i];
                                $ls_apeper=$aa_ds_fpa->data["apeper"][$li_i];
                                $nomape=$ls_apeper.", ".$ls_nomper;
                                $nomape=str_pad($nomape,40," ");
                                $nomape=substr($nomape,0,40);

                                $ls_cadena=$ls_cedper.","."0,".$ls_anio_aporte.",".$ls_mes_aporte.",".$ldec_monper.",".$ldec_monpat."\n";

                                 
				
                              
				$ls_cadena=trim($ls_cedper).","."0,".$ls_anio_aporte.",".$ls_mes_aporte.",".$ldec_monper.",".$ldec_monpat."\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}//fin del for
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fpa_central
//------------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listado_prestacionantiguedad($as_codnomdes,$as_codnomhas,$as_anocurper,$as_mescurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listado_prestacionantiguedad
		//         Access: public (desde la clase sigesp_snorh_r_prestacionantiguedad)  
		//	    Arguments: as_codnom // cï¿½digo de Nï¿½mina
		//	    		   as_anocurper // Aï¿½o en curso
		//	  			   as_mescurper // Mes en curso		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ï¿½ False si no se creo
		//    Description: funciï¿½n que busca la informaciï¿½n del fideicomiso del  personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 03/07/2006 								Fecha ï¿½ltima Modificaciï¿½n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		
		
		
		$li_mes=str_pad($as_mescurper,2,"0",0);
		$ls_sql="SELECT sno_personal.cedper,sno_personal.nomper,sno_personal.apeper, sno_fideiperiodo.sueintper, ".
				"		sno_fideicomiso.fecingfid, sno_fideicomiso.codfid, sno_personal.edocivper, sno_personal.nacper, ".
				"		sno_personal.fecingadmpubper, sno_fideicomiso.cuefid, sno_fideicomiso.capfid, sno_fideicomiso.ubifid, ".
				"		sno_fideiperiodo.bonvacper, sno_fideiperiodo.bonfinper, sno_fideiperiodo.apoper, sno_personal.cuefidper, ".
				"		sno_fideicomiso.ficfid, sno_personal.dirper, sno_personal.telhabper, sno_personal.telmovper, ".
				"       sno_fideicomiso.porintcap, minorguniadm,ofiuniadm,uniuniadm,depuniadm,prouniadm, ".
				"		sno_personal.sexper, sno_personal.fecingper, sno_personal.coreleper, sno_personalnomina.codcueban, ".
				"       (SELECT SUM(apoper) FROM sno_fideiperiodo ".
				"         WHERE sno_fideiperiodo.codemp = '".$this->ls_codemp."' AND sno_fideiperiodo.codnom = '".$as_codnom."' ".
				"           AND sno_fideiperiodo.anocurper = '".$as_anocurper."' AND sno_fideiperiodo.mescurper = '".$li_mes."') AS montototal ".
				"  FROM sno_personal, sno_fideiperiodo, sno_fideicomiso,sno_personalnomina ".
				" WHERE sno_fideiperiodo.codemp = '".$this->ls_codemp."' ".
				"   AND sno_fideiperiodo.codnom >= '".$as_codnomdes."' ".
				"   AND sno_fideiperiodo.codnom <= '".$as_codnomhas."' ".
				"   AND sno_fideiperiodo.anocurper = '".$as_anocurper."' ".
				"   AND sno_fideiperiodo.mescurper = '".$li_mes."' ".
				"   AND sno_personal.codemp = sno_fideiperiodo.codemp ".
				"	AND sno_personal.codper = sno_fideiperiodo.codper ".
				"   AND sno_personal.codemp = sno_fideicomiso.codemp ".
				"	AND sno_personal.codper = sno_fideicomiso.codper ".
				"   AND sno_personalnomina.codemp= sno_personal.codemp ".
				"   AND sno_personalnomina.codper= sno_personal.codper ".
				"   AND sno_personalnomina.codnom= sno_fideiperiodo.codnom ".
				" ORDER BY sno_personal.cedper ";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql;die();
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Metodo Aporte Mï¿½TODO->uf_listado_prestacionantiguedad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listado_prestacionantiguedad
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_fps($as_ruta,$as_metodo,$aa_ds_fps,$as_codnom,$as_anocurper,$as_mescurper,$ad_fecha,$as_tiptra,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps	
		//	    Arguments: as_ruta   // ruta donde se va aguardar el archivo
		//	    		   as_metodo   // Cï¿½digo del metodo a banco
		//                 aa_ds_fps // arreglo (datastore) datos fps      
		//	    		   as_codnom // Cï¿½digo Nï¿½mina 
		//	    		   as_anocurper // Aï¿½o en curso
		//	    		   as_mescurper // Mes
		//	    		   ad_fecha // Fecha de Procesamiento
		//	    		   as_tiptra // Tipo de Transacciï¿½n
		//				   aa_seguridad // arreglo de seguridad
		//	      Returns: lb_valido True 
		//	  Description: Funcion que segun el banco, genera un archivo txt a disco para cancelaciï¿½n de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaciï¿½n: 05/09/2006 								
		// Modificado Por:											Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
                    //  echo   $as_metodo; die();
	  	switch ($as_metodo)
		{
			case "VENEZUELA":
				$lb_valido=$this->uf_metodo_fps_venezuela($as_ruta,$ad_fecha,$aa_ds_fps);
				break;
			
			case "CASA PROPIA":
				$lb_valido=$this->uf_metodo_fps_casapropia($as_ruta,$ad_fecha,$aa_ds_fps,$as_anocurper,$as_mescurper);
				break;
			
			case "BANCO DE VENEZUELA":
				$lb_valido=$this->uf_metodo_fps_bancovenezuela($as_ruta,$ad_fecha,$aa_ds_fps);
				break;
			
			case "MERCANTIL":
				$lb_valido=$this->uf_metodo_fps_mercantil($as_ruta,$ad_fecha,$aa_ds_fps);
				break;

			case "BANCO PROVINCIAL":
				$lb_valido=$this->uf_metodo_fps_provincial($as_ruta,$ad_fecha,$aa_ds_fps);
				break;
			
			case "UNION":
				$lb_valido=$this->uf_metodo_fps_union($as_ruta,$ad_fecha,$aa_ds_fps);
				break;

			case "VENEZOLANO DE CREDITO":
				$lb_valido=$this->uf_metodo_fps_venezolanocredito($as_ruta,$ad_fecha,$as_tiptra,$aa_ds_fps);
				break;
			
			case "CARIBE":
				$lb_valido=$this->uf_metodo_fps_caribe($as_ruta,$ad_fecha,$aa_ds_fps);
				break;
			
			case "BANESCO":
				$lb_valido=$this->uf_metodo_fps_banesco($as_ruta,$ad_fecha,$aa_ds_fps);
				break;
			
			case "CENTRAL BANCO UNIVERSAL":
				$lb_valido=$this->uf_metodo_fps_central($as_ruta,$ad_fecha,$aa_ds_fps);
				break;
			
			case "DEL SUR":
				$lb_valido=$this->uf_metodo_fps_delsur($as_ruta,$ad_fecha,$aa_ds_fps);
				break;
				
			case "BANCO INDUSTRIAL":
				$lb_valido=$this->uf_metodo_fps_banco_industrial($as_ruta,$ad_fecha,$aa_ds_fps);
				break;

			default:
				$this->io_mensajes->message("El método seleccionado no esta disponible.");
				break;
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Generó el disco de FPS  Nómina ".$as_codnom." Año ".$as_anocurper." Mes ".$as_mescurper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
	}// end function uf_metodo_fps
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_fps_venezuela($as_ruta,$ad_fecha,$aa_ds_fps)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_venezuela
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco VENEZUELA para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Yesenia Moreno	
		// Fecha Creaciï¿½n: 05/09/2006 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_count=$aa_ds_fps->getRowCount("cedper");
       
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/fps_venezuela.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_codfid=substr($this->io_funciones->uf_trim($aa_ds_fps->data["codfid"][$li_i]),0,5);
				$ls_codfid=$this->io_funciones->uf_rellenar_izq($ls_codfid,"0",5);
				$ls_nomper=$aa_ds_fps->data["nomper"][$li_i];
				$li_pos=strpos($ls_nomper," ");
				if($li_pos===false)
				{
					$li_pos=strlen($ls_nomper);
				}
				$ls_primernombre=substr(substr($ls_nomper,0,$li_pos),0,15);
				$ls_primernombre=$this->io_funciones->uf_rellenar_der($ls_primernombre," ",15);
				$ls_segundonombre=substr(substr($ls_nomper,$li_pos+1,strlen($ls_nomper)-$li_pos),0,15);
				$ls_segundonombre=$this->io_funciones->uf_rellenar_der($ls_segundonombre," ",15);
				$ls_apeper=$aa_ds_fps->data["apeper"][$li_i];
				$li_pos=strpos($ls_apeper," ");
				if($li_pos===false)
				{
					$li_pos=strlen($ls_apeper);
				}
				$ls_primerapellido=substr(substr($ls_apeper,0,$li_pos),0,15);
				$ls_primerapellido=$this->io_funciones->uf_rellenar_der($ls_primerapellido," ",15);
				$ls_segundoapellido=substr(substr($ls_apeper,$li_pos+1,strlen($ls_apeper)-$li_pos),0,15);
				$ls_segundoapellido=$this->io_funciones->uf_rellenar_der($ls_segundoapellido," ",15);
				$ls_nacper= $this->io_funciones->uf_trim($aa_ds_fps->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
				$ls_cedper=str_pad(substr($ls_cedper,0,8),8,"0",0); //cedula
				$ls_edocivper=$this->io_funciones->uf_trim($aa_ds_fps->data["edocivper"][$li_i]);
				$ld_apoper=(abs($aa_ds_fps->data["apoper"][$li_i])*100);   //Monto aporte 				
				$ld_apoper=$this->io_funciones->uf_rellenar_izq($ld_apoper,"0",11);
				$ls_cadena=$ls_codfid.$ls_nacper.$ls_cedper.$ls_primernombre.$ls_segundonombre.$ls_primerapellido.$ls_segundoapellido.$ls_edocivper.$ld_apoper."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fps_venezuela
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_fps_casapropia($as_ruta,$ad_fecha,$aa_ds_fps,$as_anocurper,$as_mescurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_casapropia
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco CASA PROPIA para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Carlos Zambrano	
		// Fecha Creaciï¿½n: 28/10/2009 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_origen=$as_ruta."/casapropia.xls";
		$ls_destino=$as_ruta."/fps_casapropia.xls";
		copy($ls_origen,$ls_destino);
		chmod($ls_destino,0777);
		$fname = fopen ($ls_destino,"r+");
		$workbook = &new writeexcel_workbook($fname);
		$worksheet = &$workbook->addworksheet();
		$li_count=$aa_ds_fps->getRowCount("cedper");
		if($li_count>0)
		{	
			$li_fila=0;
			$li_i=0;
			$li_total=0;
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_nacper= $this->io_funciones->uf_trim($aa_ds_fps->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
				$ls_cedper=str_pad(substr($ls_cedper,0,10),10,"0",0); //cedula
				$ls_apeper=$aa_ds_fps->data["apeper"][$li_i];
				$ls_apeper=trim($ls_apeper);
				$ls_nomper=$aa_ds_fps->data["nomper"][$li_i];
				$ls_nomper=trim($ls_nomper);
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_fps->data["sexper"][$li_i]);
				$ls_numcta=$this->io_funciones->uf_trim($aa_ds_fps->data["codcueban"][$li_i]);
				$ls_anopersonal=substr($aa_ds_fps->data["fecingper"][$li_i],0,4);
				$ls_mespersonal=substr($aa_ds_fps->data["fecingper"][$li_i],5,2);
				$ls_ano=substr($ad_fecha,6,4);
				$ls_mes=substr($ad_fecha,3,2);
				$ls_mes=$ls_mes-4;
				$ls_mes=str_pad($ls_mes,2,"0",0);
				if(!(($ls_anopersonal==$ls_ano)&&($ls_mespersonal==$ls_mes)))
				{
					$ls_transacc=2;
				}
				else
				{
					$ls_transacc=1;
				}
				$ld_apoper=$aa_ds_fps->data["apoper"][$li_i];   //Monto aporte 
				$ld_apoper=substr($ld_apoper,0,-2);
				$ld_apoper=number_format($ld_apoper,2,",","");
				$ls_senal='S';
				$ls_dirper=substr($aa_ds_fps->data["dirper"][$li_i],0,60);
				$ls_senres='S';
				if($as_mescurper==01)
				{$ls_mescur='ENE-';}
				elseif ($as_mescurper==02)
				{$ls_mescur='FEB-';}
				elseif ($as_mescurper==03)
				{$ls_mescur='MAR-';}
				elseif ($as_mescurper==04)
				{$ls_mescur='ABR-';}
				elseif ($as_mescurper==05)
				{$ls_mescur='MAY-';}
				elseif ($as_mescurper==06)
				{$ls_mescur='JUN-';}
				elseif ($as_mescurper==07)
				{$ls_mescur='JUL-';}
				elseif ($as_mescurper==08)
				{$ls_mescur='AGO-';}
				elseif ($as_mescurper==09)
				{$ls_mescur='SEP-';}
				elseif ($as_mescurper==10)
				{$ls_mescur='OCT-';}
				elseif ($as_mescurper==11)
				{$ls_mescur='NOV-';}
				else
				{$ls_mescur='DIC-';}
				$ls_perdef=$ls_mescur.$as_anocurper;
				$worksheet->write($li_fila,0," ".$ls_nacper,'');
				$worksheet->write($li_fila,1," ".$ls_cedper,'');
				$worksheet->write($li_fila,2," ".$ls_apeper,'');
				$worksheet->write($li_fila,3," ".$ls_nomper,'');
				$worksheet->write($li_fila,4," ".$ls_sexper,'');
				$worksheet->write($li_fila,5," ".$ls_numcta,'');
				$worksheet->write($li_fila,6," ".$ls_transacc,'');
				$worksheet->write($li_fila,7," ".$ld_apoper,'');
				$worksheet->write($li_fila,8," ".$ls_senal,'');
				$worksheet->write($li_fila,9," ".$ls_dirper,'');
				$worksheet->write($li_fila,10," ".$ls_senres,'');
				$worksheet->write($li_fila,11," ".$ls_perdef,'');
				$li_fila=$li_fila+1;

			}
			if ($lb_valido)
			{
				$this->io_mensajes->message("El archivo ".$ls_destino." fue creado.");
				$workbook->close();
			}
			else
			{
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
				unset($worksheet);
				unset($workbook);
				unset($fname);
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fps_casapropia
	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_fps_bancovenezuela($as_ruta,$ad_fecha,$aa_ds_fps)
	{//var_dump($aa_ds_fps);
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_bancovenezuela
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco VENEZUELA para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Yesenia Moreno	
		// Fecha Creaciï¿½n: 05/09/2006 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_count=$aa_ds_fps->getRowCount("cedper");
		$ls_numofifps="";
		$ls_numfonfps="";
		$ls_confps="";
		$ls_nroplafps="";
		$lb_valido=$this->io_metbanco->uf_load_metodobanco_fps("BANCO DE VENEZUELA","2",$ls_numofifps,$ls_numfonfps,$ls_confps,$ls_nroplafps);
		$ls_codfid=substr(trim($ls_nroplafps),0,5);
		$ls_codfid=$this->io_funciones->uf_rellenar_izq($ls_codfid," ",5);
                         /*   echo "<pre>";

                print_r($aa_ds_fps);
                echo "</pre>";die();*/
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/incremento_fps.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_anopersonal=substr($aa_ds_fps->data["fecingper"][$li_i],0,4);
				$ls_mespersonal=substr($aa_ds_fps->data["fecingper"][$li_i],5,2);
				$ls_ano=substr($ad_fecha,6,4);
				$ls_mes=substr($ad_fecha,3,2);
				if(((12*($ls_ano-$ls_anopersonal))+($ls_mes-$ls_mespersonal)) > 3)
				{
					//$ls_codfid=substr($this->io_funciones->uf_trim($aa_ds_fps->data["codfid"][$li_i]),0,5);
					//$ls_codfid=$this->io_funciones->uf_rellenar_izq($ls_codfid," ",5);
					$ls_nacper= $this->io_funciones->uf_trim($aa_ds_fps->data["nacper"][$li_i]); //nacionalidad
					$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
					$ls_cedper=str_pad(substr($ls_cedper,0,9),9,"0",0); //cedula
					$ld_apoper=(abs($aa_ds_fps->data["apoper"][$li_i])*100);   //Monto aporte 				
					$ld_apoper=$this->io_funciones->uf_rellenar_izq($ld_apoper,"0",13);
					$ls_tipo="N";
					$ls_espacio11="00000000000";
					$ls_interes="000000";
					$ls_cobint=" ";
					$ls_cuotas="000";
					$ls_montocancelar="0000000000000";
					$ls_cutasanuales="000";
					$ls_cadena=$ls_codfid.$ls_nacper.$ls_cedper."100"." ".$ld_apoper.$ls_tipo.$ls_espacio11.$ls_interes.$ls_cobint.$ls_cuotas.$ls_montocancelar.$ls_cutasanuales."\r\n";

					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
			$ls_nombrearchivo=$as_ruta."/apertura_fps.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}

			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_anopersonal=substr($aa_ds_fps->data["fecingper"][$li_i],0,4);
				$ls_mespersonal=substr($aa_ds_fps->data["fecingper"][$li_i],5,2);
				$ls_ano=substr($ad_fecha,6,4);
				$ls_mes=substr($ad_fecha,3,2);
				if(((12*($ls_ano-$ls_anopersonal))+($ls_mes-$ls_mespersonal))==3)
				{
					//$ls_codfid=substr($this->io_funciones->uf_trim($aa_ds_fps->data["codfid"][$li_i]),0,5);
					$ls_nacper= $this->io_funciones->uf_trim($aa_ds_fps->data["nacper"][$li_i]); //nacionalidad
					$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
					$ls_cedper=str_pad(substr($ls_cedper,0,9),9,"0",0); //cedula
					$ls_edocivper=$this->io_funciones->uf_trim($aa_ds_fps->data["edocivper"][$li_i]); //cedula
					$ls_nomper=$aa_ds_fps->data["nomper"][$li_i];
					$nombres=explode(' ',$ls_nomper);
					$ls_nomper=$this->io_funciones->uf_rellenar_der(substr($nombres[0],0,30)," ",30);
					
					if($nombres[1])
					  $ls_nomper.=$this->io_funciones->uf_rellenar_der(substr($nombres[1],0,30)," ",30);
					else
					  $ls_nomper=$this->io_funciones->uf_rellenar_der(substr($nombres[0],0,30)," ",60);
					$ls_apeper=$aa_ds_fps->data["apeper"][$li_i];
					$apellidos=explode(' ',$ls_apeper);
					$ls_apeper=$this->io_funciones->uf_rellenar_der(substr($apellidos[0],0,30)," ",30);

					if($apellidos[1])
					  $ls_apeper.=$this->io_funciones->uf_rellenar_der(substr($apellidos[1],0,30)," ",30);
					else
					  $ls_apeper=$this->io_funciones->uf_rellenar_der(substr($apellidos[0],0,30)," ",60);
//$ld_apoper=($aa_ds_fps->data["apoper"][$li_i]);   //Monto aporte 

					$ld_apoper=(abs($aa_ds_fps->data["apoper"][$li_i])*100);   //Monto aporte 	
//echo $ld_apoper." ".$ls_nomper."<hr>";
					$ld_apoper=$this->io_funciones->uf_rellenar_izq($ld_apoper,"0",13);

					$ls_cuefid= $this->io_funciones->uf_trim($aa_ds_fps->data["cuefid"][$li_i]);//esta es la cuenta fideicomiso
					if(!$ls_cuefid)
					    $ls_cuefid= $this->io_funciones->uf_trim($aa_ds_fps->data["codcueban"][$li_i]);//esta es la cuenta nomina
					$ls_capfid= $this->io_funciones->uf_trim($aa_ds_fps->data["capfid"][$li_i]);
					if($ls_capfid=="S")
					{
						$ls_capfid="1";
					}
					else
					{
						$ls_capfid="0";
					}
					$ls_ubifid= $this->io_funciones->uf_trim($aa_ds_fps->data["ubifid"][$li_i]);
					$ls_cadena=$ls_codfid.$ls_nacper.$ls_cedper.$ls_nomper.$ls_apeper.$ls_edocivper."0000000000000"."0000000000000".$ld_apoper.
							   $ls_capfid.$ls_cuefid.$ls_ubifid."\r\n";

					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}//die();
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fps_bancovenezuela
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_fps_mercantil($as_ruta,$ad_fecha,$aa_ds_fps)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_mercantil
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco Mercantil para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Yesenia Moreno	
		// Fecha Creaciï¿½n: 05/09/2006 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_count=$aa_ds_fps->getRowCount("cedper");		
		$ls_confps="";
		$lb_valido=$this->io_metbanco->uf_load_metodobanco_fps("MERCANTIL","2",$ls_numofifps,$ls_numfonfps,$ls_confps,$ls_nroplafps);
		$ls_confps=$this->io_funciones->uf_rellenar_izq(substr(trim($ls_confps),0,6),"0",6);
		
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/aporte.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ld_fecha=date("dmY");
				//$ls_cuefidper=substr($this->io_funciones->uf_trim($aa_ds_fps->data["cuefidper"][$li_i]),0,6);
				//$ls_cuefidper=$this->io_funciones->uf_rellenar_izq($ls_cuefidper,"0",6);
				$ls_nacper= $this->io_funciones->uf_trim($aa_ds_fps->data["nacper"][$li_i]); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
				$ls_cedper=str_pad(substr($ls_cedper,0,9),9,"0",0); //cedula
				$ls_edocivper=$this->io_funciones->uf_trim($aa_ds_fps->data["edocivper"][$li_i]);
				$ld_apoper=(abs($aa_ds_fps->data["apoper"][$li_i])*100);   //Monto aporte 				
				$ld_apoper=$this->io_funciones->uf_rellenar_izq($ld_apoper,"0",13);
				$ls_cadena="01".$ld_fecha."1".$ls_confps.$ls_nacper.$ls_cedper."0".$ld_apoper."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fps_mercantil
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_fps_provincial($as_ruta,$ad_fecha,$aa_ds_fps)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_provincial
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco PROVINCIAL para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Yesenia Moreno	
		// Fecha Creaciï¿½n: 21/03/2007 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_numofifps="";
		$ls_numfonfps="";
		$ls_confps="";
		$ls_nroplafps="";
		$lb_valido=$this->io_metbanco->uf_load_metodobanco_fps("BANCO PROVINCIAL","2",$ls_numofifps,$ls_numfonfps,$ls_confps,$ls_nroplafps);
		$ls_confps=$this->io_funciones->uf_rellenar_izq(substr(trim($ls_confps),0,4),"0",4);
		$li_count=$aa_ds_fps->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/f".$ls_confps.substr($ad_fecha,3,2)."1.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			// Insertar cabecera
			$ls_fecha=str_replace("/","",$ad_fecha);
			$li_total=str_pad($li_count,5,"0",0);
			$li_montot=number_format($aa_ds_fps->data["montototal"][1],2,".","");
			$li_montot=str_pad(number_format($li_montot*100,0,".",""),14,"0",0);
			$ls_relleno=str_pad(" ",84," ",0);
			$ls_cadena="01"."00".$ls_confps.$ls_fecha.$li_total.$li_montot."108".$ls_relleno."\r\n";
			if ($ls_creararchivo)  //Chequea que el archivo este abierto				
			{
				if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
				{
					$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			else
			{
				$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
				$lb_valido = false;
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_nacper=$this->io_funciones->uf_trim(substr(trim($aa_ds_fps->data["nacper"][$li_i]),0,1)); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
				$ls_cedper=str_replace(".","",$ls_cedper); //cedula
				$ls_cedper=str_pad(substr($ls_cedper,0,10),10,"0",0); //cedula
				$ls_nomper=$aa_ds_fps->data["nomper"][$li_i]; // Nombre
				$ls_apeper=$aa_ds_fps->data["apeper"][$li_i]; // Apellidos
				$ls_nombre=str_pad(substr(strtoupper($ls_apeper." ".$ls_nomper),0,30),30," ");
				$ls_ficfid=$this->io_funciones->uf_trim($aa_ds_fps->data["ficfid"][$li_i]); // Ficha del Fideicomiso
				$ls_ficfid=$this->io_funciones->uf_rellenar_izq(substr($ls_ficfid,0,6),"0",6);
				$li_apoper=(abs(number_format($aa_ds_fps->data["apoper"][$li_i],2,".","")));   //Monto aporte 				
				$li_apoper=number_format($li_apoper*100,0,".","");   //Monto aporte 				
				$li_apoper=$this->io_funciones->uf_rellenar_izq($li_apoper,"0",14);
				$ld_fecingper=$this->io_funciones->uf_trim(substr($aa_ds_fps->data["fecingper"][$li_i],0,10)); // Fecha de Ingreso
				$ld_fecha=substr($ld_fecingper,8,2).substr($ld_fecingper,5,2).substr($ld_fecingper,0,4); // Fecha de Ingreso
				$ls_cuenta=trim($aa_ds_fps->data["cuefidper"][$li_i]); // Cuenta de Fideicomiso
				$ls_cuenta=str_replace("-","",$ls_cuenta);
				$ls_cuenta=str_pad(substr($ls_cuenta,0,20),20,"0",0);

				$ls_cadena="02".$ls_nacper.$ls_cedper.$ls_nombre."00000000X".$ls_ficfid."00"."00"."000"."000"."000"."02".$li_apoper.$ld_fecha."     ".$ls_cuenta."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fps_provincial
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_fps_union($as_ruta,$ad_fecha,$aa_ds_fps)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_union
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco UNION para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Yesenia Moreno	
		// Fecha Creaciï¿½n: 05/09/2006 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_count=$aa_ds_fps->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/fonz03.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_fecingfid=substr($this->io_funciones->uf_trim($aa_ds_fps->data["fecingfid"][$li_i]),0,10);
				if($this->io_fecha->uf_comparar_fecha($ls_fecingfid,$ad_fecha))
				{
					$ls_codfid=substr($this->io_funciones->uf_trim($aa_ds_fps->data["codfid"][$li_i]),0,10);
					$ls_codfid=$this->io_funciones->uf_rellenar_izq($ls_codfid," ",10);
					$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
					$ls_cedper=str_pad(substr($ls_cedper,0,9),9,"0",0); //cedula
					$ld_apoper=(abs(round($aa_ds_fps->data["apoper"][$li_i],2))*100);   //Monto aporte 				
					$ld_apoper=$this->io_funciones->uf_rellenar_izq($ld_apoper,"0",18);
					$ld_valor="000000000000000000";
					$ls_cadena=$ls_codfid.$ls_cedper."000"."0000000000".$ld_apoper.$ld_valor.$ld_valor.$ld_valor.$ld_valor."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
			$ls_nombrearchivo=$as_ruta."/fonz04.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_anopersonal=substr($aa_ds_fps->data["fecingfid"][$li_i],0,4);
				$ls_mespersonal=substr($aa_ds_fps->data["fecingfid"][$li_i],5,2);
				$ls_ano=substr($ad_fecha,6,4);
				$ls_mes=substr($ad_fecha,3,2);
				if(($ls_anopersonal==$ls_ano)&&($ls_mespersonal==$ls_mes))
				{
					$ls_codfid=substr($this->io_funciones->uf_trim($aa_ds_fps->data["codfid"][$li_i]),0,10);
					$ls_codfid=$this->io_funciones->uf_rellenar_izq($ls_codfid," ",10);
					$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
					$ls_cedper=str_pad(substr($ls_cedper,0,9),9,"0",0); //cedula
					$ls_nomper=$this->io_funciones->uf_trim($aa_ds_fps->data["nomper"][$li_i]);
					$ls_apeper=$this->io_funciones->uf_trim($aa_ds_fps->data["apeper"][$li_i]);
					$ls_nombre=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,40)," ",40);
					$ls_ficfid=$this->io_funciones->uf_trim($aa_ds_fps->data["ficfid"][$li_i]);
					$ls_ficfid=$this->io_funciones->uf_rellenar_der(substr($ls_ficfid,0,10),"0",10);
					$ls_ubifid=$this->io_funciones->uf_trim($aa_ds_fps->data["ubifid"][$li_i]);
					$ls_ubifid=$this->io_funciones->uf_rellenar_der(substr($ls_ubifid,0,10),"0",10);
					$ls_cuefid=$this->io_funciones->uf_trim($aa_ds_fps->data["cuefid"][$li_i]);
					$ls_cuefid=$this->io_funciones->uf_rellenar_der(substr($ls_cuefid,0,25)," ",25);
					$ls_capfid=$this->io_funciones->uf_trim($aa_ds_fps->data["capfid"][$li_i]);
					$ls_fecingfid=substr($aa_ds_fps->data["fecingfid"][$li_i],8,2)."/".substr($aa_ds_fps->data["fecingfid"][$li_i],5,2)."/".substr($aa_ds_fps->data["fecingfid"][$li_i],0,4);
					$ls_dirper=str_pad(substr($aa_ds_fps->data["dirper"][$li_i],0,100),100," ");
					$ls_telhabper=$this->io_funciones->uf_trim($aa_ds_fps->data["telhabper"][$li_i]);
					$ls_telhabper=str_pad(substr($ls_telhabper,0,10),10," ");
					$ls_telmovper=$this->io_funciones->uf_trim($aa_ds_fps->data["telmovper"][$li_i]);
					$ls_telmovper=str_pad(substr($ls_telmovper,0,10),10," ");
					$ls_cadena=$ls_codfid.$ls_cedper."000".$ls_nombre.$ls_ficfid.$ls_ubifid.$ls_cuefid.$ls_fecingfid.
							   $ls_capfid.$ls_dirper.$ls_telhabper.$ls_telmovper."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fps_union
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_fps_venezolanocredito($as_ruta,$ad_fecha,$as_tiptra,$aa_ds_fps)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_venezolanocredito
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco VENEZOLANO DE CREDITO para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Yesenia Moreno	
		// Fecha Creaciï¿½n: 06/09/2006 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_count=$aa_ds_fps->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/aporte_cotizaciones.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_ficfid=substr($this->io_funciones->uf_trim($aa_ds_fps->data["ficfid"][$li_i]),3,7);
				$ls_ficfid=$this->io_funciones->uf_rellenar_izq($ls_ficfid,"0",7);
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]);
				$ls_cedper=str_replace("-","",$ls_cedper);
				$ls_cedper=str_replace(".","",$ls_cedper);
				$ls_cedper=str_pad(substr($ls_cedper,0,8),8,"0",0);
				$ls_nomper=$this->io_funciones->uf_trim($aa_ds_fps->data["nomper"][$li_i]);
				$ls_apeper=$this->io_funciones->uf_trim($aa_ds_fps->data["apeper"][$li_i]);
				$ls_nombre=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,30)," ",30);
				$ld_apoper=(abs($aa_ds_fps->data["apoper"][$li_i])*100);   //Monto aporte 				
				$ld_apoper=$this->io_funciones->uf_rellenar_izq($ld_apoper,"0",11);
				$ls_ubifid=substr($this->io_funciones->uf_trim($aa_ds_fps->data["ubifid"][$li_i]),5,5);
				$ls_ubifid=$this->io_funciones->uf_rellenar_izq($ls_ubifid,"0",5);
				$ls_cadena=$ls_ficfid.$ls_cedper.$ls_nombre.$as_tiptra.$ld_apoper.$ls_ubifid."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fps_venezolanocredito
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_fps_caribe($as_ruta,$ad_fecha,$aa_ds_fps)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_caribe
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco CARIBE para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Yesenia Moreno	
		// Fecha Creaciï¿½n: 06/09/2006 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_numofifps="";
		$ls_numfonfps="";
		$ls_confps="";
		$ls_nroplafps="";
		$lb_valido=$this->io_metbanco->uf_load_metodobanco_fps("CARIBE","2",$ls_numofifps,$ls_numfonfps,$ls_confps,$ls_nroplafps);
		$ls_numofifps=$this->io_funciones->uf_rellenar_izq($ls_numofifps,"X",3);
		$ls_numfonfps=$this->io_funciones->uf_rellenar_izq($ls_numfonfps,"X",6);
		$ls_confps=$this->io_funciones->uf_rellenar_izq($ls_confps,"X",6);
		$li_count=$aa_ds_fps->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/f".$ls_numfonfps.substr($ad_fecha,3,2)."2.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			$ls_dia=substr($ad_fecha,0,2);
			$ls_mes=substr($ad_fecha,3,2);
			$ls_ano=substr($ad_fecha,8,2);
			$li_registros=$this->io_funciones->uf_rellenar_izq($li_count,"0",5);
			$li_total=(abs($aa_ds_fps->data["montototal"][1])*100); 				
			$li_total=$this->io_funciones->uf_rellenar_izq($li_total,"0",14);
			$ls_relleno=str_pad("",64,"0");
			$ls_cadena="01".$ls_confps.$ls_dia.$ls_mes.$ls_ano.$li_registros.$li_total.$ls_numofifps.$ls_relleno."\r\n";
			if ($ls_creararchivo)  //Chequea que el archivo este abierto				
			{
				if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
				{
					$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			else
			{
				$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
				$lb_valido = false;
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_fps->data["nacper"][$li_i]);
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]);
				$ls_cedper=str_replace("-","",$ls_cedper);
				$ls_cedper=str_replace(".","",$ls_cedper);
				$ls_cedper=str_pad(substr($ls_cedper,0,10),10,"0",0);
				$ls_nomper=$this->io_funciones->uf_trim($aa_ds_fps->data["nomper"][$li_i]);
				$ls_apeper=$this->io_funciones->uf_trim($aa_ds_fps->data["apeper"][$li_i]);
				$ls_nombre=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,40)," ",40);
				$ls_sexper=$this->io_funciones->uf_trim($aa_ds_fps->data["sexper"][$li_i]);
				$ls_cuefidper=$this->io_funciones->uf_trim($aa_ds_fps->data["cuefidper"][$li_i]);
				$ls_cuefidper=$this->io_funciones->uf_rellenar_izq(substr($ls_cuefidper,0,20),"0",20);
				$ld_apoper=(abs($aa_ds_fps->data["apoper"][$li_i])*100);   //Monto aporte 				
				$ld_apoper=$this->io_funciones->uf_rellenar_izq($ld_apoper,"0",14);
				$ls_cadena="02".$ls_nacper.$ls_cedper.$ls_nombre.$ls_numofifps.$ls_sexper.$ls_cuefidper."02".$ld_apoper."0001"."100"."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fps_caribe
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_metodo_fps_banesco($as_ruta,$ad_fecha,$aa_ds_fps)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_banesco
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para BANESCO para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Yesenia Moreno	
		// Fecha Creaciï¿½n: 20/03/2007 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_numofifps="";
		$ls_numfonfps="";
		$ls_confps="";
		$ls_nroplafps="";
		$lb_valido=$this->io_metbanco->uf_load_metodobanco_fps("BANESCO","2",$ls_numofifps,$ls_numfonfps,$ls_confps,$ls_nroplafps);
		$ls_nroplafps=$this->io_funciones->uf_rellenar_izq(substr($ls_nroplafps,0,8),"0",8);
		$li_count=$aa_ds_fps->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/fonz04-".substr($ad_fecha,3,2).substr($ad_fecha,6,4).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
				$ld_fecingper=$aa_ds_fps->data["fecingfid"][$li_i]; 
				$ld_mesingper=substr($ld_fecingper,5,2);
				$ld_anoingper=substr($ld_fecingper,0,4);				
				$ld_anoingper2digitos=substr($ld_fecingper,0,2);
				$ld_mesfecdes=substr($ad_fecha,3,2);
				$ld_anofecdes=substr($ad_fecha,6,4);
				
				$fecingpertxt=$aa_ds_fps->data["fecingper"][$li_i];
				$ld_mesingpertxt=substr($fecingpertxt,5,2);
				$ld_anoingpertxt=substr($fecingpertxt,2,2);
				$ld_diaingpertxt=substr($fecingpertxt,8,2); 
				$ld_fecingpertxt=$ld_diaingpertxt.'/'.$ld_mesingpertxt.'/'.$ld_anoingpertxt;
				if(($ld_mesingper==$ld_mesfecdes)&&($ld_anoingper==$ld_anofecdes))
				{
					$ld_fecnacper=$this->io_funciones->uf_trim($aa_ds_fps->data["fecnacper"][$li_i]);
					$ld_fecnacper=substr($ld_fecnacper,8,2).substr($ld_fecnacper,5,2).substr($ld_fecnacper,0,4); //DDMMAAAA
					$ld_fecingper=$this->io_funciones->uf_convertirfecmostrar($ld_fecingper);
					$ls_nacper=$this->io_funciones->uf_trim($aa_ds_fps->data["nacper"][$li_i]); //nacionalidad
					$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
					$ls_cedper=str_replace("-","",$ls_cedper);
					$ls_cedper=str_replace(".","",$ls_cedper);
					$ls_cedper=str_pad(substr($ls_cedper,0,9),9,"0",0);
					$ls_tipafi="001"; // Tipo de Afiliado
					$ls_nomper=$this->io_funciones->uf_trim($aa_ds_fps->data["nomper"][$li_i]); //nombres
					$ls_apeper=$this->io_funciones->uf_trim($aa_ds_fps->data["apeper"][$li_i]); //apellidos
					$ls_nombre=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,40), " ", 40);
					$ls_codigo="0000000000"; // Cï¿½digo 1
					$ls_unidad=$aa_ds_fps->data["minorguniadm"][$li_i].$aa_ds_fps->data["ofiuniadm"][$li_i].$aa_ds_fps->data["uniuniadm"][$li_i].$aa_ds_fps->data["depuniadm"][$li_i].$aa_ds_fps->data["prouniadm"][$li_i]; // Unidad Administrativa
					$ls_unidad=$this->io_funciones->uf_rellenar_der($ls_unidad, " ", 40);					
					$ls_cuenta=str_pad(substr($aa_ds_fps->data["cuefidper"][$li_i],0,20),20," ");  // Cuenta Fideicomiso
					$ls_capfid=$aa_ds_fps->data["capfid"][$li_i];  // Capitaliza el Fideicomiso
					$ls_diremp=str_pad(substr($_SESSION["la_empresa"]["direccion"],0,100),100," ");
					$ls_dirper=str_pad(substr($aa_ds_fps->data["dirper"][$li_i],0,100),100," ");
					$ls_telemp=str_pad(substr($_SESSION["la_empresa"]["telemp"],0,14),14," ");
					$ls_telper=str_pad(substr($aa_ds_fps->data["telhabper"][$li_i],0,14),14," ");
					$ls_coreleper=str_pad(substr($aa_ds_fps->data["coreleper"][$li_i],0,40),40," ");
					if(!trim($ls_cuenta)){$ls_cuenta='01340000000000000000';}
					$ls_cadena=$ls_nroplafps.$ls_nacper.$ls_cedper.$ls_tipafi.$ls_nombre.$ls_codigo.$ls_unidad.$ls_cuenta.$ld_fecingpertxt.
					           $ls_capfid.$ls_diremp.$ls_dirper.$ls_telemp.$ls_telper.$ls_coreleper."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			}//fin del for
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
			$lb_valido=true;
			$ls_nombrearchivo=$as_ruta."/FONZ03-".substr($ad_fecha,3,2).substr($ad_fecha,6,4).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ld_fecingper=$aa_ds_fps->data["fecingfid"][$li_i]; 
				$ld_mesingper=substr($ld_fecingper,5,2);
				$ld_anoingper=substr($ld_fecingper,0,4);
				$ld_mesfecdes=substr($ad_fecha,3,2);
				$ld_anofecdes=substr($ad_fecha,6,4);
				
					$ls_nacper=$this->io_funciones->uf_trim($aa_ds_fps->data["nacper"][$li_i]);
					$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]);
					$ls_cedper=str_replace("-","",$ls_cedper);
					$ls_cedper=str_replace(".","",$ls_cedper);
					$ls_cedper=str_pad(substr($ls_cedper,0,9),9,"0",0);
					$ls_tipafi="001"; // Tipo de Afiliado
					$ls_codtra="APO002AO"; // Cï¿½digo de Transacciï¿½n de aporte
					$ld_apoper=(abs(round($aa_ds_fps->data["apoper"][$li_i],2))*100);   //Monto aporte 				
					$ld_apoper=$this->io_funciones->uf_rellenar_izq($ld_apoper,"0",19);
					$ld_monto2=$this->io_funciones->uf_rellenar_izq(0,"0",19);
					$ld_monto3=$this->io_funciones->uf_rellenar_izq(0,"0",19);
					$ld_monto4=$this->io_funciones->uf_rellenar_izq(0,"0",19);
					$ld_monto5=$this->io_funciones->uf_rellenar_izq(0,"0",19);
					$ls_cadena=$ls_nroplafps.$ls_nacper.$ls_cedper.$ls_tipafi.$ls_codtra.$ld_apoper.$ld_monto2.$ld_monto3.$ld_monto4.$ld_monto5."\r\n";
					if ($ls_creararchivo)  //Chequea que el archivo este abierto				
					{
						if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
						{
							$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
							$lb_valido = false;
						}
					}
					else
					{
						$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_fps_banesco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_fps_central($as_ruta,$ad_fecha,$aa_ds_fps)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_central
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco CENTRAL para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaciï¿½n: 22/07/2008 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_numfonfps="";		
		$lb_valido=$this->io_metbanco->uf_load_metodobanco_fps("CENTRAL BANCO UNIVERSAL","2",$ls_numofifps,$ls_numfonfps,$ls_confps,$ls_nroplafps);		
		$ls_numfonfps=$this->io_funciones->uf_rellenar_izq($ls_numfonfps,"X",6);		
		$li_count=$aa_ds_fps->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/f".$ls_numfonfps.substr($ad_fecha,3,2).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}			
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); 
				$ls_cedper=str_replace("-","",$ls_cedper);
				$ls_cedper=str_replace(".","",$ls_cedper);
				$ls_cedper=str_pad($ls_cedper,9," ",'LEFT');				
				$ls_nomper=$this->io_funciones->uf_trim($aa_ds_fps->data["nomper"][$li_i]);
				$ls_apeper=$this->io_funciones->uf_trim($aa_ds_fps->data["apeper"][$li_i]);
				$ls_nombre=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,40)," ",40);				
				$ld_apoper=(abs(number_format($aa_ds_fps->data["apoper"][$li_i],2,"",".")));  //Monto aporte  
				$ld_apoper=str_pad($ld_apoper,10," ",'LEFT');				
				$ls_cadena=$ls_cedper.$ld_apoper."  ".$ls_nombre."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end uf_metodo_fps_central	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_fps_delsur($as_ruta,$ad_fecha,$aa_ds_fps)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_fps_delsur
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco DEL SUR para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Yesenia Moreno de Lang
		// Fecha Creaciï¿½n: 07/08/2008 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_numfonfps="";
		$ls_confps="";
		$ls_nroplafps="";
		$lb_valido=$this->io_metbanco->uf_load_metodobanco_fps("DEL SUR","2",$ls_numofifps,$ls_numfonfps,$ls_confps,$ls_nroplafps);		
		$ls_nroplafps=$this->io_funciones->uf_rellenar_izq($ls_nroplafps,"X",4);		
		$ls_confps=$this->io_funciones->uf_rellenar_izq($ls_confps,"X",4);		
		$li_count=$aa_ds_fps->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/fidedicomiso_delsur_".substr($ad_fecha,3,2).".txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}			
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_nacper=$this->io_funciones->uf_trim($aa_ds_fps->data["nacper"][$li_i]); 
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); 
				$ls_cedper=str_replace("-","",$ls_cedper);
				$ls_cedper=str_replace(".","",$ls_cedper);
				$ls_cedper=str_pad($ls_cedper,10,"0",0);				
				$ls_cuefidper=$this->io_funciones->uf_trim($aa_ds_fps->data["cuefidper"][$li_i]);
				$ls_cuefidper=str_pad(substr($ls_cuefidper,0,10),10,"0",0);
				$ld_apoper=number_format($aa_ds_fps->data["apoper"][$li_i],2,".","");  //Monto aporte  
				$ld_apoper=str_pad($ld_apoper*100,10,"0",0);				
				$ls_cadena=$ls_confps.$ls_nroplafps.$ls_nacper.$ls_cedper."   ".$ls_cuefidper.$ld_apoper."0001\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end uf_metodo_fps_delsur
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_metodo_fps_banco_industrial($as_ruta,$ad_fecha,$aa_ds_fps)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: banco_industrial
		//		   Access: private 
		//	    Arguments: as_ruta  // ruta 
		//                 ad_fecha // Fecha 
		//                 aa_ds_fps // arreglo (datastore) datos FPS   
		//	  Description: genera el archivo txt a disco para  el banco DEL SUR para pago de Prestaciï¿½n Antiguedad
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaciï¿½n: 23/01/2009 								
		// Modificado Por: 									Fecha ï¿½ltima Modificaciï¿½n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_numofifps="";
		$ls_numfonfps="";
		$ls_confps="";
		$ls_nroplafps="";
		$lb_valido=$this->io_metbanco->uf_load_metodobanco_fps("BANCO INDUSTRIAL","2",$ls_numofifps,$ls_numfonfps,
		                                                       $ls_confps,$ls_nroplafps);
		$ls_confps=$this->io_funciones->uf_rellenar_der(substr(trim($ls_confps),0,5),"0",6);// numero de contrato
		$ls_nroplafps=$this->io_funciones->uf_rellenar_der($ls_nroplafps,"0",3);// numero de la sucursal o agencia
		$li_count=$aa_ds_fps->getRowCount("cedper");
		if($li_count>0)
		{	
			$ls_nombrearchivo=$as_ruta."/disco_banco_industrial.txt";
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			// Insertar cabecera
			$ls_fecha=str_replace("/","",$ad_fecha);
			$lsddmm=substr($ls_fecha,0,4);
			$lsano=substr($ls_fecha,6,8);
			$ls_fecha2=$lsddmm.$lsano;
			$li_total=$this->io_funciones->uf_rellenar_der($li_count,"0",5);
			$li_montot=number_format($aa_ds_fps->data["montototal"][1],2,".","");
			$li_montot=number_format($li_montot*100,0,".","");	
			$li_montot=$this->io_funciones->uf_rellenar_der($li_montot,"0",14);		
			$ls_cadena="01".$ls_confps.$ls_fecha2.$li_total.$li_montot."\r\n"; // cabecera del disco
			
			if ($ls_creararchivo)  //Chequea que el archivo este abierto				
			{
				if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
				{
					$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			else
			{
				$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
				$lb_valido = false;
			}
			for($li_i=1;$li_i<=$li_count;$li_i++)
			{
				$ls_nacper=$this->io_funciones->uf_trim(substr(trim($aa_ds_fps->data["nacper"][$li_i]),0,1)); //nacionalidad
				$ls_cedper=$this->io_funciones->uf_trim($aa_ds_fps->data["cedper"][$li_i]); //cedula
				$ls_cedper=str_replace(".","",$ls_cedper); //cedula
				$ls_cedper=str_pad($ls_nacper.substr($ls_cedper,0,10),11," ",0); //cedula
				$ls_nomper=str_pad($aa_ds_fps->data["nomper"][$li_i],30," ",0); // Nombre
				$ls_apeper=str_pad($aa_ds_fps->data["apeper"][$li_i],30," ",0); // Apellidos
				$ls_nombre=$ls_nomper.$ls_apeper;
				$ls_sexo=$aa_ds_fps->data["sexper"][$li_i];							
				$li_apoper=(abs(number_format($aa_ds_fps->data["apoper"][$li_i],2,".","")));   //Monto aporte 				
				$li_apoper=number_format($li_apoper*100,0,".","");   //Monto aporte 				
				$li_apoper=$this->io_funciones->uf_rellenar_der($li_apoper,"0",14);				
				$ls_cuenta=trim($aa_ds_fps->data["cuefidper"][$li_i]); // Cuenta de Fideicomiso 				
				$ls_cuenta=str_replace("-","",$ls_cuenta);
				$ls_cuenta=str_pad(substr($ls_cuenta,0,20),20," ",0);
				$ls_capfid= $this->io_funciones->uf_trim($aa_ds_fps->data["capfid"][$li_i]);// capitaliza
				$ls_dirper= str_pad(substr($aa_ds_fps->data["dirper"][$li_i],0,60),60," ");// direccion del personal
				$ls_porcentaje=(abs(number_format($aa_ds_fps->data["porintcap"][$li_i],3,".","")));   //Monto aporte			
				$ls_porcentaje=number_format($ls_porcentaje*100,0,".","");   //porcentaje
				$ls_porcentaje=$this->io_funciones->uf_rellenar_der($ls_porcentaje,"0",8);	 
				$ls_cadena="02".$ls_cedper.$ls_nombre.$ls_nroplafps.$ls_sexo.$ls_cuenta."002000".$li_apoper.$ls_capfid.$ls_porcentaje.$ls_dirper."S"."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;

	}// end uf_metodo_fps_delsur
///------------------------------------------------------------------------------------------------------------------------------------
}
?>