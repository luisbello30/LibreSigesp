<?php
    session_start();
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "libro_banco.xls");
		$lo_libro = &new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		function uf_convertir($ls_numero)
	{
		$ls_numero=str_replace(".","",$ls_numero);
		$ls_numero=str_replace(",",".",$ls_numero);
		return $ls_numero;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("scb_report_conciliacion.php");
	require_once('../../shared/class_folder/class_pdf.php');
	require_once('../../shared/class_folder/class_fecha.php');
	require_once("../../shared/class_folder/class_funciones.php");
	require_once("../../shared/class_folder/sigesp_include.php");
	require_once("../../shared/class_folder/class_sql.php");
	require_once("../../shared/class_folder/class_mensajes.php");
	require_once("../../shared/class_folder/class_datastore.php");

	require_once("../../shared/class_folder/sigesp_c_reconvertir_monedabsf.php");
	$io_monedabsf=new sigesp_c_reconvertir_monedabsf();

	$in			  = new sigesp_include();
	$con		  =	$in->uf_conectar();
	$io_sql		  = new class_sql($con);
	$io_report	  = new scb_report_conciliacion($con);
	$io_funciones = new class_funciones();
	$io_fecha     = new class_fecha();
	$ds_concil	  = new class_datastore();
	$io_fecha	  = new class_fecha();


	//---------------------------------------------------------------------------------------------------------------------------
	//Par�metros para Filtar el Reporte
	$ls_codemp      = $_SESSION["la_empresa"]["codemp"];
	$ls_codban      = $_GET["codban"];
	$ls_nomban      = $_GET["nomban"];
	$ls_ctaban      = $_GET["ctaban"];
	$ls_mesano      = $_GET["mesano"];
	$ls_tipbol      = 'Bs.';
	$ls_tiporeporte = 0;
	$ls_tiporeporte = $_GET["tiporeporte"];
	global $ls_tiporeporte;
	if ($ls_tiporeporte==1)
	   {
		 require_once("scb_report_conciliacionbsf.php");
		 $io_report = new scb_report_conciliacionbsf($con);
		 $ls_tipbol = 'Bs.F.';
		$ldec_salseglib = $_GET["salseglib"];
		$ldec_salsegbco = $_GET["salsegbco"];
		$ldec_salseglib=$io_monedabsf->uf_convertir_monedabsf($ldec_salseglib,$_SESSION["la_empresa"]["candeccon"],$_SESSION["la_empresa"]["tipconmon"],1000,$_SESSION["la_empresa"]["redconmon"]);
		$ldec_salsegbco=$io_monedabsf->uf_convertir_monedabsf($ldec_salsegbco,$_SESSION["la_empresa"]["candeccon"],$_SESSION["la_empresa"]["tipconmon"],1000,$_SESSION["la_empresa"]["redconmon"]);
	   }
	 else
	 {
		$ldec_salseglib = $_GET["salseglib"];
		$ldec_salsegbco = $_GET["salsegbco"];
	 }
	
	//---------------------------------------------------------------------------------------------------------------------------
	//Par�metros del encabezado
		$ldt_fecha="Desde  ".$ld_fecdesde."  al ".$ld_fechasta."";
		$ls_titulo="LIBRO BANCO $ls_tipbol";
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data
	$data=$io_report->uf_obtener_mov_conciliacion($ls_mesano,$ls_codban,$ls_ctaban,$ldec_salseglib,&$ldec_salsegbco);
	$ls_tipo_cuenta=$io_report->uf_tipo_cuenta($ls_codban,$ls_ctaban);
	$ds_concil->data=$data;
	error_reporting(E_ALL);
	set_time_limit(1800);
	$li_totrow=$ds_concil->getRowCount("numdoc");

	//---------------------------------------------------------------------------------------------------------------------------
  	// Impresi�n de la informaci�n encontrada en caso de que exista
	if(($data===false))
	{
		?>
		<script language="javascript">
			alert("Error al buscar datos de la conciliaci�n");
			close();
		</script>
		<?php
	}
	else // Imprimimos el reporte
	{


                $ld_fechasta=$io_fecha->uf_last_day(substr($ls_mesano,0,2),substr($ls_mesano,2,4));
                $ls_mes=$io_fecha->uf_load_nombre_mes(substr($ls_mesano,0,2));
                $ls_anio=substr($ls_mesano,2,4);
                //uf_print_encabezado_pagina($ls_nomban,$ls_mes,$ls_anio,&$io_pdf); //Se imprime la tabla de la cabecera
                $li_temp=1;
              //  uf_print_cabecera($ls_nomban,$ls_ctaban,$ls_tipo_cuenta,'',$io_pdf); // Se imprime la cuenta del reporte
               // uf_print_saldo_libro ($ld_fechasta,number_format($ldec_salseglib,2,",","."),&$io_pdf);// Se imprime el saldo segun libro

                $la_data=array();
                $la_data_cheque_mas      = array();
                $la_data_cheque_menos    = array();
                $la_data_nota_deb_mas    = array();
                $la_data_nota_deb_menos  = array();
                $la_data_nota_cred_mas   = array();
                $la_data_nota_cred_menos = array();
                $la_data_retiro_mas      = array();
                $la_data_retiro_menos    = array();
                $la_data_deposito_mas    = array();
                $la_data_deposito_menos  = array();
                $la_data_trans_no_regist_nd_mas = array();
                $la_data_trans_no_regist_nd_menos = array();
                $la_data_trans_no_regist_nc_mas = array();
                $la_data_trans_no_regist_nc_menos = array();
                $la_data_trans_no_regist_dp_mas = array();
                $la_data_trans_no_regist_dp_menos = array();
                $li_temp_cheque_mas      = 0;
                $li_temp_cheque_menos    = 0;
                $li_temp_nota_deb_mas    = 0;
                $li_temp_nota_deb_menos  = 0;
                $li_temp_nota_cred_mas   = 0;
                $li_temp_nota_cred_menos = 0;
                $li_temp_retiro_mas      = 0;
                $li_temp_retiro_menos    = 0;
                $li_temp_deposito_mas    = 0;
                $li_temp_deposito_menos  = 0;
                $li_temp_trans_no_regist_nd_mas = 0;
                $li_temp_trans_no_regist_nd_menos = 0;
                $li_temp_trans_no_regist_nc_mas = 0;
                $li_temp_trans_no_regist_nc_menos = 0;
                $li_temp_trans_no_regist_dp_mas = 0;
                $li_temp_trans_no_regist_dp_menos = 0;

		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');

		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');

		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
                
                $lo_dataleft1= &$lo_libro->addformat();
		$lo_dataleft1->set_text_wrap();
		$lo_dataleft1->set_font("Verdana");
		$lo_dataleft1->set_align('left');
		$lo_dataleft1->set_size('9');
                $lo_dataleft1->set_bold();


		$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');

                $lo_dataright1= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright1->set_font("Verdana");
		$lo_dataright1->set_align('right');
		$lo_dataright1->set_size('9');
                $lo_dataright1->set_bold();

		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,15);
		$lo_hoja->set_column(2,5,20);
		$lo_hoja->set_column(3,7,30);
                $lo_hoja->set_column(5,0,40);
                $lo_hoja->set_column(7,0,40);
                $lo_hoja->set_column(11,1,30);
                $lo_hoja->set_column(11,2,40);
                $lo_hoja->set_column(11,3,30);

		$lo_hoja->write(0, 2, utf8_decode("CONCILIACIÓN BANCARIA "),$lo_encabezado);
		$lo_hoja->write(1, 2, $ls_nomban,$lo_encabezado);
                $lo_hoja->write(2, 2, " MES DE ".strtoupper($ls_mes)." ".$ls_anio,$lo_encabezado);

               
		$lo_hoja->write(4, 0, strtoupper($ls_tipo_cuenta)." No ",$lo_dataleft1);
                $lo_hoja->write(5, 0, $ls_ctaban,$lo_dataleft1);
		$lo_hoja->write(7, 0, "SALDO SEGUN LIBRO AL ".$ld_fechasta, $lo_dataleft1);
                $lo_hoja->write(7, 3, $ldec_salseglib, $lo_dataright1);

             


                for($li_i=1;$li_i<=$li_totrow;$li_i++)
                {
                            $li_temp=$li_temp+1;

                            $li_totprenom = 0;
                            $ldec_mondeb  = 0;
                            $ldec_monhab  = 0;
                            $li_totant    = 0;
                            $ls_tipo      = $ds_concil->getValue("tipo",$li_i);
                            $ls_suma      = $ds_concil->getValue("suma",$li_i);
                            $ls_codope    = $ds_concil->getValue("codope",$li_i);
                            $ls_numdoc    = $ds_concil->getValue("numdoc",$li_i);
                            $ls_nomproben = $ds_concil->getValue("nomproben",$li_i);
                            $ld_fecmov    = $ds_concil->getValue("fecmov",$li_i);
                            $ldec_monto   = $ds_concil->getValue("monto",$li_i);
                            $ls_estreglib = $ds_concil->getValue("estreglib",$li_i);
                            $ld_fecmov    = $io_funciones->uf_convertirfecmostrar($ld_fecmov);
                            $ls_item      = $ls_numdoc."  ".$ls_nomproben."   ".$ld_fecmov;
                            if($ls_suma=='+')//En caso que sean mas
                            {
                                    switch($ls_codope)
                                    {
                                            case "CH":
                                                    $la_data_cheque_mas[$li_temp_cheque_mas]["fecha"]=$ld_fecmov;
                                                    $la_data_cheque_mas[$li_temp_cheque_mas]["numdoc"]=$ls_numdoc;
                                                    $la_data_cheque_mas[$li_temp_cheque_mas]["nombre"]=strtoupper($ls_nomproben);
                                                    $la_data_cheque_mas[$li_temp_cheque_mas]["monto"]=number_format($ldec_monto,2,",",".");
                                                    $li_temp_cheque_mas++;
                                            break;
                                            case "ND":
                                                    if ($ls_estreglib=='A')
                                                    {
                                                            $la_data_trans_no_regist_nd_mas[$li_temp_trans_no_regist_nd_mas]["fecha"]=$ld_fecmov;
                                                            $la_data_trans_no_regist_nd_mas[$li_temp_trans_no_regist_nd_mas]["numdoc"]=$ls_numdoc;
                                                            $la_data_trans_no_regist_nd_mas[$li_temp_trans_no_regist_nd_mas]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_trans_no_regist_nd_mas[$li_temp_trans_no_regist_nd_mas]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_trans_no_regist_nd_mas++;
                                                    }
                                                    else
                                                    {
                                                            $la_data_nota_deb_mas[$li_temp_nota_deb_mas]["fecha"]=$ld_fecmov;
                                                            $la_data_nota_deb_mas[$li_temp_nota_deb_mas]["numdoc"]=$ls_numdoc;
                                                            $la_data_nota_deb_mas[$li_temp_nota_deb_mas]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_nota_deb_mas[$li_temp_nota_deb_mas]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_nota_deb_mas++;
                                                    }
                                            break;
                                            case "NC":
                                                    if ($ls_estreglib=='A')
                                                    {
                                                            $la_data_trans_no_regist_nc_mas[$li_temp_trans_no_regist_nc_mas]["fecha"]=$ld_fecmov;
                                                            $la_data_trans_no_regist_nc_mas[$li_temp_trans_no_regist_nc_mas]["numdoc"]=$ls_numdoc;
                                                            $la_data_trans_no_regist_nc_mas[$li_temp_trans_no_regist_nc_mas]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_trans_no_regist_nc_mas[$li_temp_trans_no_regist_nc_mas]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_trans_no_regist_nc_mas++;
                                                    }
                                                    else
                                                    {
                                                            $la_data_nota_cred_mas[$li_temp_nota_cred_mas]["fecha"]=$ld_fecmov;
                                                            $la_data_nota_cred_mas[$li_temp_nota_cred_mas]["numdoc"]=$ls_numdoc;
                                                            $la_data_nota_cred_mas[$li_temp_nota_cred_mas]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_nota_cred_mas[$li_temp_nota_cred_mas]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_nota_cred_mas++;
                                                    }
                                            break;
                                            case "RE":
                                                    $la_data_retiro_mas[$li_temp_retiro_mas]["fecha"]=$ld_fecmov;
                                                    $la_data_retiro_mas[$li_temp_retiro_mas]["numdoc"]=$ls_numdoc;
                                                    $la_data_retiro_mas[$li_temp_retiro_mas]["nombre"]=strtoupper($ls_nomproben);
                                                    $la_data_retiro_mas[$li_temp_retiro_mas]["monto"]=number_format($ldec_monto,2,",",".");
                                                    $li_temp_retiro_mas++;
                                            break;
                                            case "DP":
                                                    if ($ls_estreglib=='A')
                                                    {
                                                            $la_data_trans_no_regist_dp_mas[$li_temp_trans_no_regist_dp_mas]["fecha"]=$ld_fecmov;
                                                            $la_data_trans_no_regist_dp_mas[$li_temp_trans_no_regist_dp_mas]["numdoc"]=$ls_numdoc;
                                                            $la_data_trans_no_regist_dp_mas[$li_temp_trans_no_regist_dp_mas]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_trans_no_regist_dp_mas[$li_temp_trans_no_regist_dp_mas]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_trans_no_regist_dp_mas++;
                                                    }
                                                    else
                                                    {
                                                            $la_data_deposito_mas[$li_temp_deposito_mas]["fecha"]=$ld_fecmov;
                                                            $la_data_deposito_mas[$li_temp_deposito_mas]["numdoc"]=$ls_numdoc;
                                                            $la_data_deposito_mas[$li_temp_deposito_mas]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_deposito_mas[$li_temp_deposito_mas]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_deposito_mas++;
                                                    }
                                            break;
                                    }
                            }
                            else//en caso de que sean menos
                            {
                                            switch($ls_codope)
                                    {
                                            case "CH":
                                                    $la_data_cheque_menos[$li_temp_cheque_menos]["fecha"]=$ld_fecmov;
                                                    $la_data_cheque_menos[$li_temp_cheque_menos]["numdoc"]=$ls_numdoc;
                                                    $la_data_cheque_menos[$li_temp_cheque_menos]["nombre"]=strtoupper($ls_nomproben);
                                                    $la_data_cheque_menos[$li_temp_cheque_menos]["monto"]=number_format($ldec_monto,2,",",".");
                                                    $li_temp_cheque_menos++;
                                            break;
                                            case "ND":
                                                    if ($ls_estreglib=='A')
                                                    {
                                                            $la_data_trans_no_regist_nd_menos[$li_temp_trans_no_regist_nd_menos]["fecha"]=$ld_fecmov;
                                                            $la_data_trans_no_regist_nd_menos[$li_temp_trans_no_regist_nd_menos]["numdoc"]=$ls_numdoc;
                                                            $la_data_trans_no_regist_nd_menos[$li_temp_trans_no_regist_nd_menos]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_trans_no_regist_nd_menos[$li_temp_trans_no_regist_nd_menos]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_trans_no_regist_nd_menos++;
                                                    }
                                                    else
                                                    {
                                                            $la_data_nota_deb_menos[$li_temp_nota_deb_menos]["fecha"]=$ld_fecmov;
                                                            $la_data_nota_deb_menos[$li_temp_nota_deb_menos]["numdoc"]=$ls_numdoc;
                                                            $la_data_nota_deb_menos[$li_temp_nota_deb_menos]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_nota_deb_menos[$li_temp_nota_deb_menos]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_nota_deb_menos++;
                                                    }
                                            break;
                                            case "NC":
                                                    if ($ls_estreglib=='A')
                                                    {
                                                            $la_data_trans_no_regist_nc_menos[$li_temp_trans_no_regist_nc_menos]["fecha"]=$ld_fecmov;
                                                            $la_data_trans_no_regist_nc_menos[$li_temp_trans_no_regist_nc_menos]["numdoc"]=$ls_numdoc;
                                                            $la_data_trans_no_regist_nc_menos[$li_temp_trans_no_regist_nc_menos]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_trans_no_regist_nc_menos[$li_temp_trans_no_regist_nc_menos]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_trans_no_regist_nc_menos++;
                                                    }
                                                    else
                                                    {
                                                            $la_data_nota_cred_menos[$li_temp_nota_cred_menos]["fecha"]=$ld_fecmov;
                                                            $la_data_nota_cred_menos[$li_temp_nota_cred_menos]["numdoc"]=$ls_numdoc;
                                                            $la_data_nota_cred_menos[$li_temp_nota_cred_menos]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_nota_cred_menos[$li_temp_nota_cred_menos]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_nota_cred_menos++;
                                                    }
                                            break;
                                            case "RE":
                                                    $la_data_retiro_menos[$li_temp_retiro_menos]["fecha"]=$ld_fecmov;
                                                    $la_data_retiro_menos[$li_temp_retiro_menos]["numdoc"]=$ls_numdoc;
                                                    $la_data_retiro_menos[$li_temp_retiro_menos]["nombre"]=strtoupper($ls_nomproben);
                                                    $la_data_retiro_menos[$li_temp_retiro_menos]["monto"]=number_format($ldec_monto,2,",",".");
                                                    $li_temp_retiro_menos++;
                                            break;
                                            case "DP":
                                                    if ($ls_estreglib=='A')
                                                    {
                                                            $la_data_trans_no_regist_dp_menos[$li_temp_trans_no_regist_dp_menos]["fecha"]=$ld_fecmov;
                                                            $la_data_trans_no_regist_dp_menos[$li_temp_trans_no_regist_dp_menos]["numdoc"]=$ls_numdoc;
                                                            $la_data_trans_no_regist_dp_menos[$li_temp_trans_no_regist_dp_menos]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_trans_no_regist_dp_menos[$li_temp_trans_no_regist_dp_menos]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_trans_no_regist_dp_menos++;
                                                    }
                                                    else
                                                    {
                                                            $la_data_deposito_menos[$li_temp_deposito_menos]["fecha"]=$ld_fecmov;
                                                            $la_data_deposito_menos[$li_temp_deposito_menos]["numdoc"]=$ls_numdoc;
                                                            $la_data_deposito_menos[$li_temp_deposito_menos]["nombre"]=strtoupper($ls_nomproben);
                                                            $la_data_deposito_menos[$li_temp_deposito_menos]["monto"]=number_format($ldec_monto,2,",",".");
                                                            $li_temp_deposito_menos++;
                                                    }
                                            break;
                                    }
                            }
                    }
                    $la_data=array();
                    $la_data=array(array('nombre'=>'CHEQUES','tipo'=>'MAS','data'=>$la_data_cheque_mas),
                                               array('nombre'=>'CHEQUES','tipo'=>'MENOS','data'=>$la_data_cheque_menos),
                                               array('nombre'=>'NOTAS DE DEBITO','tipo'=>'MAS','data'=>$la_data_nota_deb_mas),
                                               array('nombre'=>'NOTAS DE DEBITO','tipo'=>'MENOS','data'=>$la_data_nota_deb_menos),
                                               array('nombre'=>'NOTAS DE CREDITO','tipo'=>'MAS','data'=>$la_data_nota_cred_mas),
                                               array('nombre'=>'NOTAS DE CREDITO','tipo'=>'MENOS','data'=>$la_data_nota_cred_menos),
                                               array('nombre'=>'RETIROS','tipo'=>'MAS','data'=>$la_data_retiro_mas),
                                               array('nombre'=>'RETIROS','tipo'=>'MENOS','data'=>$la_data_retiro_menos),
                                               array('nombre'=>'DEPOSITOS','tipo'=>'MAS','data'=>$la_data_deposito_mas),
                                               array('nombre'=>'DEPOSITOS','tipo'=>'MENOS','data'=>$la_data_deposito_menos),
                                               array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO ND','tipo'=>'MAS','data'=>$la_data_trans_no_regist_nd_mas),
                                               array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO ND','tipo'=>'MENOS','data'=>$la_data_trans_no_regist_nd_menos),
                                               array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO NC','tipo'=>'MAS','data'=>$la_data_trans_no_regist_nc_mas),
                                               array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO NC','tipo'=>'MENOS','data'=>$la_data_trans_no_regist_nc_menos),
                                               array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO DP','tipo'=>'MAS','data'=>$la_data_trans_no_regist_dp_mas),
                                               array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO DP','tipo'=>'MENOS','data'=>$la_data_trans_no_regist_dp_menos));


                   $fila=10;
                   $columna=0;
                   
                   /*echo '<pre>';
                    print_r($la_data);
                   echo '</pre>';*///die();
                   for($li_i=0;$li_i<count($la_data);$li_i++)
                   {

                       
		 	$la_data_aux=array();
                        $ls_nombre='';
                        $ls_tipo='';
			$la_data_aux=$la_data[$li_i]["data"];
			$ls_nombre=$la_data[$li_i]["nombre"];
                        $ls_tipo=$la_data[$li_i]["tipo"];
                        
			if(count($la_data_aux)>0)
			{

                               
                                $lo_hoja->write($fila, 0, $ls_tipo."     ".$ls_nombre,$lo_dataleft1);
                                $fila++;
                                $lo_hoja->write($fila, 0, "FECHA",$lo_titulo);
                                $lo_hoja->write($fila, 1, "DOCUMENTO",$lo_titulo);
                                $lo_hoja->write($fila, 2, "PROVEEDOR/BENEFICIARIO",$lo_titulo);
                                $lo_hoja->write($fila, 3, "MONTO",$lo_titulo);
                                $fila++;
                               
                                $li_total=0;
                                 for ($l=0; $l < count($la_data_aux); $l++)
                                {
                                        
                                        $lo_hoja->write($fila, $columna, $la_data_aux[$l]['fecha'],$lo_dataleft);
                                        $columna=$columna+1;
                                        
                                        
                                        $lo_hoja->write($fila, $columna, $la_data_aux[$l]['numdoc'].' ',$lo_dataleft);
                                        $columna=$columna+1;
                                        
                                        $lo_hoja->write($fila, $columna, $la_data_aux[$l]['nombre'],$lo_dataleft);
                                        $columna=$columna+1;
                                        
                                        $lo_hoja->write($fila, $columna, $la_data_aux[$l]['monto'],$lo_dataright);
                                        $columna=0;
                                        
                                        $fila++;
                                        
                                        $li_total=$li_total+uf_convertir($la_data_aux[$l]["monto"]);

                                }
                                $fila++;
                                //------Imprimiendo el total----------------
				if($ls_tipo=="MAS")
					$la_data_monto[0]["1"]=number_format($li_total,2,",",".");
				else
					$la_data_monto[0]["1"]=number_format($li_total,2,",",".");

                                $lo_hoja->write($fila, 2, "TOTAL $ls_nombre EN TRANSITO",$lo_titulo);
                                $lo_hoja->write($fila, 3, $la_data_monto[0]["1"],$lo_dataright1);
                                $fila=$fila+2;



                        }
                       

                   }
                    $lo_hoja->write($fila, 2, utf8_decode("SALDO SEGÚN BANCO AL ").$ld_fechasta,$lo_titulo);
                    $lo_hoja->write($fila, 3, $ldec_salsegbco,$lo_dataright1);


		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"conciliacion_bancaria.xls\"");
		header("Content-Disposition: inline; filename=\"conciliacion_bancaria.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
    }
?>