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

// Men� Principal- Recepci�n de Documentos
stm_ai("p0i0",[0,"   Procesos    ","","",-1,-1,0,"","_self","","","","",0,0,0,"","",0,0,0,0,1,"#F7F7F7",0,"#f4f4f4",0,"","",3,3,0,0,"#fffff7","#000000","#909090","#909090","8pt 'Tahoma','Arial'","8pt 'Tahoma','Arial'",0,0]);
stm_bp("p1",[1,4,0,0,2,3,6,1,100,"progid:DXImageTransform.Microsoft.Fade(overlap=.5,enabled=0,Duration=0.10)",-2,"",-2,100,2,3,"#999999","#ffffff","",3,1,1,"#F7F7F7"]);
// Archivo - Opciones de Segundo Nivel
stm_aix("p1i0","p0i0",[0,"Apertura ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,7]);
// Edici�n - Opciones de Tercer Nivel
stm_aix("p1i5","p1i0",[0," Mensual  ","","",-1,-1,0,"sigesp_spi_p_apertura.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i5","p1i0",[0," Trimestral  ","","",-1,-1,0,"sigesp_spi_p_apertura_trimestral.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();

stm_aix("p1i2","p1i0",[0,"Comprobantes ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edici�n - Opciones de Tercer Nivel
stm_aix("p1i5","p1i0",[0," Ejecuci&oacute;n Financiera  ","","",-1,-1,0,"sigesp_spi_p_comprobante.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();
stm_aix("p1i4","p1i0",[0,"Modificaciones Presupuestarias ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edici�n - Opciones de Tercer Nivel
stm_aix("p1i5","p1i0",[0," Aumentos  ","","",-1,-1,0,"sigesp_spi_p_aumento.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i5","p1i0",[0," Disminuciones  ","","",-1,-1,0,"sigesp_spi_p_disminucion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i5","p1i0",[0,"Reverso/Cierre de Presupuesto  ","","",-1,-1,0,"sigesp_spi_p_cerrarpre.php","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_ep();
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i5","p1i0",[0,"Eliminar Comprobantes  ","","",-1,-1,0,"sigesp_spi_p_eliminar_comprobante.php","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_ep();
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i5","p1i0",[0,"Programaci&oacute;n de reportes  ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edici�n - Opciones de Tercer Nivel
stm_aix("p1i5","p1i0",[0," Mensual  ","","",-1,-1,0,"sigesp_spi_p_progrep.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i5","p1i0",[0," Trimestral  ","","",-1,-1,0,"sigesp_spi_p_progrep_trimestral.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();
stm_aix("p1i5","p1i0",[0,"Consolidaci&oacute;n Presupuestaria","","",-1,-1,0,"sigesp_spi_p_consolidacion_empresas.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();
stm_ep();
stm_ep();
// Men� Principal - Reportes
stm_aix("p0i4","p0i0",[0," Reportes "]);
stm_bpx("p6","p1",[1,4,0,0,2,3,6,7]);
stm_aix("p4i0","p1i0",[0," Acumulado por Cuentas    ","","",-1,-1,0,"sigesp_spi_r_acum_x_cuentas.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Mayor Analitico ","","",-1,-1,0,"sigesp_spi_r_mayor_analitico.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Listado Apertura ","","",-1,-1,0,"sigesp_spi_r_listado_apertura.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Comprobante ","","",-1,-1,0,"sigesp_spi_r_comprobante_formato1.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Modificaciones Presupuestarias Aprobadas ","","",-1,-1,0,"sigesp_spi_r_modificaciones_presupuestarias_aprobadas.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Modificaciones Presupuestarias No Aprobadas ","","",-1,-1,0,"sigesp_spi_r_modificaciones_presupuestarias_no_aprobadas.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Listado de  Cuentas Presupuestarias  ","","",-1,-1,0,"sigesp_spi_r_cuentas.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Ejecucion Presupuestaria Mensual de Ingreso  ","","",-1,-1,0,"sigesp_spi_r_ejecucion_financiera_mensual.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0," Comparados","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i0","p1i0",[0," Instructivo 07 - 2008","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_aix("p4i0","p1i0",[0," Ejecucion Trimestral de Presupuesto de Ingreso y Fuentes Financieras ","","",-1,-1,0,"sigesp_spi_r_ejecucion_trimestral.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i5","p1i0",[0," Consolidado de Ejecuci&oacute;n Trimestral de Ingresos Financieros ","","",-1,-1,0,"sigesp_spi_r_instructivo_consolidado_ejecucion_trimestral.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i5","p1i0",[0," Presupuesto de Caja ","","",-1,-1,0,"sigesp_spi_r_instructivo_presupuesto_caja.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();
stm_aix("p1i5","p1i0",[0," Instructivo 08 - 2009 ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i5","p1i0",[0," Ejecucion Trimestral de Presupuesto de Ingreso y Fuentes Financieras ","","",-1,-1,0,"sigesp_spi_r_ejecucion_trimestral_instructivo_8_2009.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i5","p1i0",[0," Consolidado de Ejecuci&oacute;n Trimestral de Ingresos Financieros ","","",-1,-1,0,"sigesp_spi_r_instructivo_consolidado_ejecucion_trimestral_inst_08_2009.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i5","p1i0",[0," Presupuesto de Caja ","","",-1,-1,0,"sigesp_spi_r_instructivo_presupuesto_caja_inst_08_2009.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);

/*
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i0","p1i0",[0," Instructivo 08 - 2009","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_aix("p4i0","p1i0",[0," Ejecuci�n Trimestral de Ingreso y Fuentes Financieras ","","",-1,-1,0,"sigesp_spi_r_ejecucion_trimestral.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i5","p1i0",[0," Consolidado de Ejecuci&oacute;n Trimestral de Ingresos Financieros ","","",-1,-1,0,"sigesp_spi_r_instructivo_consolidado_ejecucion_trimestral.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i5","p1i0",[0," Presupuesto de Caja ","","",-1,-1,0,"sigesp_spi_r_instructivo_presupuesto_caja.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
*/

stm_ep();
stm_ep();
stm_ep();

stm_aix("p4i0","p1i0",[0," Ir a M&oacute;dulos  ","","",-1,-1,0,"../index_modules.php","","","","","",6,0,0,"","",0,0,0,0,1,"#F7F7F7"]);
stm_bpx("p10","p1",[]);
stm_ep();
stm_em();
