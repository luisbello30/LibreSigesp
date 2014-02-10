// stm_aix("p1i2","p1i0",[0,"Opci�n 2    ","","",-1,-1,0,""]);
// stm_aix("p1i0","p0i0",[0,"Opci�n 1    ","","",-1,-1,0,"tablas.htm","_self","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);

//-----------------------//
// L�nea de separaci�n
// Para inlcuir l�neas de separaci�n entre las opciones, incoporar la siguiente instrucci�n, entre las opciones a separar
// stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);

//-----------------------//
// Men�es de Tercer Nivel
// Para hacer submen�es, incluir las siguientes l�neas de c�digo
// stm_bpx("pn","p1",[1,4,0,0,2,3,6,7]);   debajo de la l�nea de c�digo de la opci�n principal stm_aix("p0in","p0i0",[0," Opci�n Men� "]);
// luego, buscar la opci�n del men� bajo la cual se abrir� el submen� y agregar al final de esa l�nea de c�digo, los siguientes atributos:
// ,"","",-1,-1,0,"","_self","","","","",6,0,0,"imagebank/arrow.gif","imagebank/arrow.gif",7,7]);
// y justo debajo de esa l�nea agregar las siguientes l�neas de c�digo.
// stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edici�n - Opciones de Tercer Nivel
// stm_aix("p3i0","p1i0",[0,"  Menu Item 1  ","","",-1,-1,0,"","_self","","","","",0]);
// stm_aix("p3i1","p3i0",[0,"  Menu Item 2  "]);
// stm_aix("p3i2","p3i0",[0,"  Menu Item 3  "]);
// stm_aix("p3i3","p3i0",[0,"  Menu Item 4  "]);
// stm_aix("p3i4","p3i0",[0,"  Menu Item 5  "]);
// stm_ep();
// Luego cambiar las opciones "Menu Item 5", por el nombre de la opci�n que corresponda en cada caso.

//-----------------------//
// Hiperv�nculos
// Para incluir los enlaces correspondientes a cada opci�n del men�, se procede de la siguiente manera:
// En aquellas intrucciones, cuyo c�digo es similare a esto:
// stm_aix("p1i0","p0i0",[0,"Opci�n 1    ","","",-1,-1,0,"","_self","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
// agregar el enlace dentro de las comillas, justo delante de "_self"
// En aquellas intrucciones, cuyo c�digo es similare a esto:
// stm_aix("p3i1","p3i0",[0,"  Menu Item 2  "]);
// agregar al final de esta l�nea de c�digo, los siguientes par�metros:
// ,"","",-1,-1,0,"","_self","","","","",0]);
// y luego incorporar el enlace en las comillas que est� justo antes de "_self"

stm_bm(["menu08dd",430,"","../shared/imagebank/blank.gif",0,"","",0,0,0,0,1000,1,0,0,"","100%",0],this);
stm_bp("p0",[0,4,0,0,1,3,0,0,100,"",-2,"",-2,90,0,0,"#000000","#e6e6e6","",3,0,0,"#000000"]);
// Men� Principal- Cotizaciones
stm_ai("p0i0",[0,"   Cotizaciones   ","","",-1,-1,0,"","_self","","","","",0,0,0,"","",0,0,0,0,1,"#F7F7F7",0,"#f4f4f4",0,"","",3,3,0,0,"#fffff7","#000000","#909090","#909090","8pt 'Tahoma','Arial'","8pt 'Tahoma','Arial'",0,0]);
stm_bp("p1",[1,4,0,0,2,3,6,0,100,"progid:DXImageTransform.Microsoft.Fade(overlap=.5,enabled=0,Duration=0.10)",-2,"",-2,100,2,3,"#999999","#ffffff","",3,1,1,"#F7F7F7"]);
stm_aix("p1i0","p0i0",[0," Solicitud de Cotizaci&oacute;n    ","","",-1,-1,0,"sigesp_soc_p_solicitud_cotizacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0," Registro de Cotizaci&oacute;n     ","","",-1,-1,0,"sigesp_soc_p_registro_cotizacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0," An�lisis de Cotizaciones     ","","",-1,-1,0,"sigesp_soc_p_analisis_cotizacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0," Aprobaci�n de An�lisis de Cotizaciones  ","","",-1,-1,0,"sigesp_soc_p_aprobacion_analisis_cotizacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0," Generaci�n de Ordenes de Compra    ","","",-1,-1,0,"sigesp_soc_p_generar_orden_analisis.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0," Anulacion de An�lisis de Cotizaciones    ","","",-1,-1,0,"sigesp_soc_p_anulacion_analisis_cotizacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0," Anulacion de Registro de Cotizacion    ","","",-1,-1,0,"sigesp_soc_p_anulacion_registro_cotizacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0," Anulacion de Solicitud de Cotizacion    ","","",-1,-1,0,"sigesp_soc_p_anulacion_solicitud_cotizacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();
// Men� Principal- Orden de Compra

stm_aix("p1i0","p0i0",[0," Orden de Compra     ","","",-1,-1,0,"","","","","","",6,0,0,"","",0,0,0,0,1,"#F7F7F7"]);
stm_bp("p1",[1,4,0,0,2,3,6,0,100,"progid:DXImageTransform.Microsoft.Fade(overlap=.5,enabled=0,Duration=0.10)",-2,"",-2,100,2,3,"#999999","#ffffff","",3,1,1,"#F7F7F7"]);
// Archivo - Opciones de Segundo Nivel
stm_aix("p1i0","p0i0",[0,"    Registro de Ordenes de Compra ","","",-1,-1,0,"sigesp_soc_p_registro_orden_compra.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"    Aprobaci&oacute;n de Ordenes de Compra ","","",-1,-1,0,"sigesp_soc_p_aprobacion_orden_compra.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"    Anulaci&oacute;n de Ordenes de Compra  ","","",-1,-1,0,"sigesp_soc_p_anulacion_orden_compra.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"    Aceptaci&oacute;n/Reverso de Servicios   ","","",-1,-1,0,"sigesp_soc_p_aceptacion_servicio.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();
// Men� Principal - Reportes
stm_aix("p0i4","p0i0",[0,"   Reportes   "]);
stm_bpx("p6","p1",[1,4,0,0,2,3,6,7]);
stm_aix("p1i0","p0i0",[0,"  Orden de Compra   ","","",-1,-1,0,"sigesp_soc_r_orden_compra.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"  Solicitud de Cotizaciones   ","","",-1,-1,0,"sigesp_soc_r_solicitud_cotizacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"  Registro  de Cotizaciones   ","","",-1,-1,0,"sigesp_soc_r_registro_cotizacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"  An&aacute;lisis  de Cotizaciones   ","","",-1,-1,0,"sigesp_soc_r_analisis_cotizacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"  Acta de Aceptaci&oacute;n de Servicios   ","","",-1,-1,0,"sigesp_soc_r_aceptacion_servicios.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"  Ubicacion de Orden de Compra   ","","",-1,-1,0,"sigesp_soc_r_orden_ubicacioncompra.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);//stm_ep();
stm_aix("p1i0","p0i0",[0,"  Imputaci&oacute;n Presupuestaria de Ordenes de Compra   ","","",-1,-1,0,"sigesp_soc_r_imputacion_spg_orden_compra.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"  Relaci&oacute;n Mensual de Ordenes de Compra    ","","",-1,-1,0,"sigesp_soc_r_orden_relacionmensual.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();

// Men� Principal - Ayuda
stm_aix("p0i8","p0i0",[0,"   Ayuda   "]);
stm_bpx("p10","p1",[]);
stm_ep();

stm_aix("p1i0","p0i0",[0," Ir a M&oacute;dulos  ","","",-1,-1,0,"../index_modules.php","","","","","",6,0,0,"","",0,0,0,0,1,"#F7F7F7"]);
stm_bpx("p10","p1",[]);
stm_ep();

stm_em();