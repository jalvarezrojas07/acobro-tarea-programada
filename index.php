<?php
require_once "Utilidades/Log.php";
require_once "Db/DBConexion.php";
require_once "Models/Gestion.php";

try{
    

    date_default_timezone_set('Chile/Continental');
    $log = new Log();
    //$settings = file_get_contents('C:\laragon\www\panel-tarea\settings.json');
    $settings = file_get_contents('settings.json');
    //$settings = file_get_contents('C:\Users\JALVAREZ\Documents\\tarea\settings.json');
    $settings = json_decode($settings,true);
    $log->registrar("Iniciando tarea...");
    $gestion = new Gestion();
    $campanas = ['FALF', 'FALC', 'FALB', 'FALR'];

    $periodos = [];
    if($argv[1] == 1){
        $mesActual = explode('-', date('Y-m'))[1];
        $anoActual = explode('-', date('Y-m'))[0];
        $periodos[] = [$mesActual, $anoActual];
    }
    if($argv[2] == 1){
        $mesTrim3 = explode('-', date('Y-m',strtotime('-1 months')))[1];
        $anoTrim3 = explode('-', date('Y-m',strtotime('-1 months')))[0];
        $periodos[] = [$mesTrim3, $anoTrim3];
    }
    if($argv[3] == 1){
        $mesTrim2 = explode('-', date('Y-m',strtotime('-2 months')))[1];
        $anoTrim2 = explode('-', date('Y-m',strtotime('-2 months')))[0];
        $periodos[] = [$mesTrim2, $anoTrim2];
    }
    if($argv[4] == 1){
        $mesTrim1 = explode('-', date('Y-m',strtotime('-3 months')))[1];
        $anoTrim1 = explode('-', date('Y-m',strtotime('-3 months')))[0];
        $periodos[] = [$mesTrim1, $anoTrim1];
    }
    if($argv[5] == 1){
        $mesEstacional = explode('-', date('Y-m',strtotime('-1 year')))[1];
        $anoEstacional = explode('-', date('Y-m',strtotime('-1 year')))[0];
        $periodos[] = [$mesEstacional, $anoEstacional];
    }
    
    foreach ($campanas as $campana) {
        $log->registrar("Iniciando campana: ".$campana);
    
        $codigosCD = 0;
        $codigosCI = 0;
        $codigosCR = 0;
        $codigosSC = 0;
        $codigosOtro = array(0,0);
        if($campana == "FALF" || $campana == "FALC"){
            $codigosCD = 983;
            $codigosCI = 984;
            $codigosCR = 986;
            $codigosSC = 989;
            $codigosOtro = array(985, 988);

        }else{
            $codigosCD = 969;
            $codigosCI = 970;
            $codigosCR = 972;
            $codigosSC = 975;
            $codigosOtro = array(971, 974);
        }
        foreach ($periodos as $periodo) {
            $periodoF = $periodo[1]."-".$periodo[0];

            /* Buscar registro en panel */
            $idPanel = $opTotal = $gestion->get_panel($periodoF, $campana);
            if($idPanel == 0){
                $idPanel = $opTotal = $gestion->put_panel($periodoF, $campana);
            }
            /* #1 - N° cuentas asignadas */
            $log->registrar("Iniciando busqueda fila #1 campana: ".$campana." - periodo: ".$periodoF);
            $opTotal = $gestion->get_op_total($periodo[0], $periodo[1], $campana);
            $log->registrar("Finalizada busqueda fila #1 campana: ".$campana." - periodo: ".$periodoF);
            
            $log->registrar("Iniciando registro #1 campana: ".$campana." - periodo: ".$periodoF);
            $opTotal = $gestion->put_op_total($idPanel, $opTotal);
            $log->registrar("Finalizado registro #1 campana: ".$campana." - periodo: ".$periodoF);

            /* #2 - Deuda asignada MM$ */
            $log->registrar("Iniciando busqueda fila #2 campana: ".$campana." - periodo: ".$periodoF);
            $deudaTotal = $gestion->get_deuda_total($periodo[0], $periodo[1], $campana);
            $log->registrar("Finalizada busqueda fila #2 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #2 periodo: ".$periodoF);
            $gestion->put_deuda_total($idPanel, $deudaTotal);
            $log->registrar("Finalizado registro #2 periodo: ".$periodoF);

            /* #3 - MM$ RECUPERO */
            $log->registrar("Iniciando busqueda fila #3 campana: ".$campana." - periodo: ".$periodoF);
            $deudaPagada = $gestion->get_deuda_pagada($periodo[0], $periodo[1], $campana);
            $log->registrar("Finalizada busqueda fila #3 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #3 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_deuda_pagada($idPanel, $deudaPagada);
            $log->registrar("Finalizado registro #3 campana: ".$campana." - periodo: ".$periodoF);

            /* #7 - # TOTAL CD */
            $log->registrar("Iniciando busqueda fila #7 campana: ".$campana." - periodo: ".$periodoF);
            $totalCD = $gestion->get_contactos_directos($periodo[0], $periodo[1], $codigosCD, $campana);
            $log->registrar("Finalizada busqueda fila #7 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #7 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_contactos_directos($idPanel, $totalCD);
            $log->registrar("Finalizado registro #7 campana: ".$campana." - periodo: ".$periodoF);

            /* #8 -TOTAL CI */
            $log->registrar("Iniciando busqueda fila #8 campana: ".$campana." - periodo: ".$periodoF);
            $totalCI = $gestion->get_contactos_indirectos($periodo[0], $periodo[1], $codigosCI, $campana);
            $log->registrar("Finalizada busqueda fila #8 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #7 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_contactos_indirectos($idPanel, $totalCI);
            $log->registrar("Finalizado registro #7 campana: ".$campana." - periodo: ".$periodoF);

            /* #9 -TOTAL CR */
            $log->registrar("Iniciando busqueda fila #9 campana: ".$campana." - periodo: ".$periodoF);
            $totalCR = $gestion->get_canales_remotos($periodo[0], $periodo[1], $codigosCR, $campana);
            $log->registrar("Finalizada busqueda fila #9 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #9 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_contactos_canales_remotos($idPanel, $totalCR);
            $log->registrar("Finalizado registro #9 campana: ".$campana." - periodo: ".$periodoF);

            /* #10 - TOTAL SC */
            $log->registrar("Iniciando busqueda fila #10 campana: ".$campana." - periodo: ".$periodoF);
            $totalSC = $gestion->get_sin_contactos($periodo[0], $periodo[1], $codigosSC, $campana);
            $log->registrar("Finalizada busqueda fila #10 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #10 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_contactos_sin_contactos($idPanel, $totalSC);
            $log->registrar("Finalizado registro #10 campana: ".$campana." - periodo: ".$periodoF);

            /* #11 - TOTAL OTRO */
            $log->registrar("Iniciando busqueda fila #11 campana: ".$campana." - periodo: ".$periodoF);
            $totalOtro = $gestion->get_otros_contactos($periodo[0], $periodo[1], $codigosOtro, $campana);
            $log->registrar("Finalizada busqueda fila #11 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #11 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_contactos_otros_contactos($idPanel, $totalOtro);
            $log->registrar("Finalizado registro #11 campana: ".$campana." - periodo: ".$periodoF);

            /* #13 - # CONTACTO_DIRECTO */
            $log->registrar("Iniciando busqueda fila #13 campana: ".$campana." - periodo: ".$periodoF);
            $totalCDRut = $gestion->get_contactos_directos_rut($periodo[0], $periodo[1], $codigosCD, $campana);
            $log->registrar("Finalizada busqueda fila #13 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #13 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_contactos_directos_rut($idPanel, $totalCDRut);
            $log->registrar("Finalizado registro #13 campana: ".$campana." - periodo: ".$periodoF);

            /* #14 - # CONTACTO_INDIRECTO */
            $log->registrar("Iniciando busqueda fila #14 campana: ".$campana." - periodo: ".$periodoF);
            $totalCIRut = $gestion->get_contactos_indirectos_rut($periodo[0], $periodo[1], $codigosCI, $campana);
            $log->registrar("Finalizada busqueda fila #14 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #14 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_contactos_indirectos_rut($idPanel, $totalCIRut);
            $log->registrar("Finalizado registro #14 campana: ".$campana." - periodo: ".$periodoF);

            /* #15 - # CANAL_REMOTO*/
            $log->registrar("Iniciando busqueda fila #15 campana: ".$campana." - periodo: ".$periodoF);
            $totalCRRut = $gestion->get_canales_remotos_rut($periodo[0], $periodo[1], $codigosCR, $campana);
            $log->registrar("Finalizada busqueda fila #15 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #15 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_canales_remotos_rut($idPanel, $totalCRRut);
            $log->registrar("Finalizado registro #15 campana: ".$campana." - periodo: ".$periodoF);

            /* #16 - # SIN_CONTACTO */
            $log->registrar("Iniciando busqueda fila #16 campana: ".$campana." - periodo: ".$periodoF);
            $totalSCRut = $gestion->get_sin_contactos_rut($periodo[0], $periodo[1], $codigosSC, $campana);
            $log->registrar("Finalizada busqueda fila #16 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #16 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_sin_contactos_rut($idPanel, $totalSCRut);
            $log->registrar("Finalizado registro #16 campana: ".$campana." - periodo: ".$periodoF);

            /* #17 - # OTRO */
            $log->registrar("Iniciando busqueda fila #17 campana: ".$campana." - periodo: ".$periodoF);
            $totalOtroRut = $gestion->get_otros_contactos_rut($periodo[0], $periodo[1], $codigosOtro, $campana);
            $log->registrar("Finalizada busqueda fila #17 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #17 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_otros_con_rut($idPanel, $totalOtroRut);
            $log->registrar("Finalizado registro #17 campana: ".$campana." - periodo: ".$periodoF);

            /* #18 - # SIN_GESTION */
            $log->registrar("Iniciando busqueda fila #18 campana: ".$campana." - periodo: ".$periodoF);
            $log->registrar("Buscando ruts periodo: ".$periodoF);
            $rutsDeuda = $gestion->get_deuda_buscar_rut($periodo[0], $periodo[1], $campana);
            
            $sinGestionRut = 0;
            $log->registrar("Validando ruts en gestiones periodo: ".$periodoF);
            foreach ($rutsDeuda as $rut) {
                $validar = $gestion->get_sin_gestion_validar_rut($periodo[0], $periodo[1], $campana, $rut);
                if($validar > 0){
                    $sinGestionRut = $sinGestionRut + 1;
                }
            }
            $log->registrar("Finalizada busqueda fila #18 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #18 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_sin_gestion_rut($idPanel, $sinGestionRut);
            $log->registrar("Finalizado registro #18 campana: ".$campana." - periodo: ".$periodoF);

            /* #25 - # PROMESA_PAGO */
            $log->registrar("Iniciando busqueda fila #25 campana: ".$campana." - periodo: ".$periodoF);
            $promesaPago = $gestion->get_promesas_rut($periodo[0], $periodo[1], $campana);
            $log->registrar("Finalizada busqueda fila #25 campana: ".$campana." - periodo: ".$periodoF);

            $log->registrar("Iniciando registro #25 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_sin_promesas_rut($idPanel, $promesaPago);
            $log->registrar("Finalizado registro #25 campana: ".$campana." - periodo: ".$periodoF);

            /* #27 - # PROMESA CUMPLIDA */
            $log->registrar("Iniciando busqueda fila #27 campana: ".$campana." - periodo: ".$periodoF);
            $log->registrar("Validando ruts en gestiones periodo: ".$periodoF);
            $promesaCumplida = 0;
            
            foreach ($rutsDeuda as $rut) {
            
                $validarGestion = 0;
                $validarGestion = $gestion->get_gestion_validar_rut($periodo[0], $periodo[1], $campana, $rut);
                if($validarGestion > 0){
                    $validarPromesa = $gestion->get_promesa_validar_rut($periodo[0], $periodo[1], $campana, $rut);
                    if($validarPromesa > 0){
                        $promesaCumplida = $promesaCumplida + 1;
                    }
                }
            }

            
            $log->registrar("Iniciando registro #27 campana: ".$campana." - periodo: ".$periodoF);
            $gestion->put_promesas_cumplidas_rut($idPanel, $promesaCumplida);
            $log->registrar("Finalizado registro #27 campana: ".$campana." - periodo: ".$periodoF);
            
        }
    }
    $log->registrar("Tarea finalizada");
}catch(Exception $e) {
    echo $e->getMessage();
    $log->registrar($e->getMessage());
}
?>