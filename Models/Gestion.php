<?php
//require_once "C:\laragon\www\panel-tarea\Db\DBConexion.php";
//require_once "C:\laragon\www\panel-tarea\Utilidades\Log.php";

//require_once "C:\wamp64\www\panel-tarea\Db\DBConexion.php";
//require_once "C:\wamp64\www\panel-tarea\Utilidades\Log.php";

require_once "Db\DBConexion.php";
require_once "Utilidades\Log.php";

//require_once "C:\Users\JALVAREZ\Documents\\tarea\Db\DBConexion.php";
//require_once "C:\Users\JALVAREZ\Documents\\tarea\Utilidades\Log.php";

class Gestion{
    
    public function get_panel($periodo, $campana){
        $query = "
            SELECT
                id
            FROM
                panel
            WHERE
                periodo = '".$periodo."'
            and campana = '".$campana."'
            order by id desc
            limit 1;
        ";
        try{

            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            $id = 0;
            while ( $row = $data->fetch_assoc() ) {
                if($row['id'] == "" || $row['id'] == null){
                    $id = 0;
                }else{
                    $id = $row['id'];
                }
                
            }
            return $id;
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function put_panel($periodo, $campana){
        $query = "
            INSERT INTO panel (
                periodo,
                campana
            )VALUES(
                '".$periodo."',
                '".$campana."'
            )
        ";
        try{
            $db = DBConexion::conexion_destino();
            
            $data = $db->query($query);
            
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    /* #1 - N° cuentas asignadas */
    public function get_op_total($mesActual, $anoActual, $campana){
        $query = "
                SELECT
                    count(sistema_deuda.nro_doc)
                FROM
                    sistema_deuda
                WHERE
                    campaign_id LIKE '%".$campana."%'
                AND MONTH (sistema_deuda.fecha_carga) = ".$mesActual."
                AND YEAR (sistema_deuda.fecha_carga) = ".$anoActual."
                GROUP BY
                sistema_deuda.nro_doc;
            ";
        try{

            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $contador =0;
            while ( $row = $data->fetch_assoc() ) {
                $contador = $contador+1;
            }
            return $contador;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function put_op_total($idPanel, $opTotal){
    
        $query = "update panel set numero_cuentas=".$opTotal." where id = ".$idPanel;
        
        try{
            $db = DBConexion::conexion_destino();
            
            $data = $db->query($query);
            
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    /* #2 - MM$ DEUDA */
    public function get_deuda_total($mesActual, $anoActual, $campana){
        $query = "
                SELECT
                    SUM(monto) AS monto
                FROM
                    sistema_deuda
                WHERE
                    sistema_deuda.campaign_id LIKE '%".$campana."%'
                AND MONTH (sistema_deuda.fecha_carga) = ".$mesActual."
                AND YEAR (sistema_deuda.fecha_carga) = ".$anoActual."
            ";
        try{

            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $monto = 0;
            while ( $row = $data->fetch_assoc() ) {
                if($row['monto'] == "" || $row['monto'] == null){
                    $monto = 0;
                }else{
                    $monto = $row['monto'];
                }
                
            }
            return $monto;
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function put_deuda_total($idPanel, $deudaTotal){
        $query = "update panel set deuda_total=".$deudaTotal." where id = ".$idPanel;
        try{

            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    /* #3 - MM$ RECUPERO */
    public function get_deuda_pagada($mesActual, $anoActual, $campana){
        $query = "
            SELECT
                sum(monto_pago) AS monto
            FROM
                sistema_deuda
            LEFT JOIN sistema_pagos_general ON sistema_deuda.nro_doc = sistema_pagos_general.nro_documento
            WHERE
                sistema_deuda.campaign_id LIKE '%".$campana."%'
            AND MONTH (sistema_deuda.fecha_carga) = ".$mesActual."
            AND YEAR (sistema_deuda.fecha_carga) = ".$anoActual."
            AND sistema_pagos_general.id_pago IS NOT NULL
        ";
        try{

            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $monto = 0;
            while ( $row = $data->fetch_assoc() ) {
                if($row['monto'] == "" || $row['monto'] == null){
                    $monto = 0;
                }else{
                    $monto = $row['monto'];
                }
                
            }
            return $monto;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function put_deuda_pagada($idPanel, $deudaPagada){
        $query = "update panel set deuda_pagada=".$deudaPagada." where id = ".$idPanel;
        try{

            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function get_contactos_directos($mesActual, $anoActual, $codigosCD, $campana){
        $query = "
            SELECT
                count(
                    sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
                sistema_gestiones.campaign LIKE '%".$campana."%'
            AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
            AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
            AND  sistema_gestiones.cod_contacto IN (".$codigosCD.")
        ";
        try{

            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $cantidad = 0;
            while ( $row = $data->fetch_assoc() ) {
                if($row['cantidad'] == "" || $row['cantidad'] == null){
                    $cantidad = 0;
                }else{
                    $cantidad = $row['cantidad'];
                }
            }
            return $cantidad;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function put_contactos_directos($idPanel, $totalCD){
        $query = "update panel set total_cd=".$totalCD." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function get_contactos_indirectos($mesActual, $anoActual, $codigosCI, $campana){
        $query = "
            SELECT
                count(
                    sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
                sistema_gestiones.campaign LIKE '%".$campana."%'
            AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
            AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
            AND  sistema_gestiones.cod_contacto IN (".$codigosCI.")
        ";
        try{
            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $cantidad = 0;
            while ( $row = $data->fetch_assoc() ) {
                if($row['cantidad'] == "" || $row['cantidad'] == null){
                    $cantidad = 0;
                }else{
                    $cantidad = $row['cantidad'];
                }
            }
            return $cantidad;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function put_contactos_indirectos($idPanel, $totalCI){
        $query = "update panel set total_ci=".$totalCI." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function get_canales_remotos($mesActual, $anoActual, $codigosCR, $campana){
        $query = "
            SELECT
                count(
                    sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
                sistema_gestiones.campaign LIKE '%".$campana."%'
            AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
            AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
            AND sistema_gestiones.cod_contacto IN (".$codigosCR.")
        ";
        try{

            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $cantidad = 0;
            while ( $row = $data->fetch_assoc() ) {
                if($row['cantidad'] == "" || $row['cantidad'] == null){
                    $cantidad = 0;
                }else{
                    $cantidad = $row['cantidad'];
                }
            }
            return $cantidad;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    public function put_contactos_canales_remotos($idPanel, $totalCR){
        $query = "update panel set total_cr=".$totalCR." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function get_sin_contactos($mesActual, $anoActual, $codigosSC, $campana){
        $query = "
            SELECT
                count(
                    sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
                sistema_gestiones.campaign LIKE '%".$campana."%'
            AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
            AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
            AND sistema_gestiones.cod_contacto IN (".$codigosSC.")
        ";
        try{

            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $cantidad = 0;
            while ( $row = $data->fetch_assoc() ) {
                if($row['cantidad'] == "" || $row['cantidad'] == null){
                    $cantidad = 0;
                }else{
                    $cantidad = $row['cantidad'];
                }
            }
            return $cantidad;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function put_contactos_sin_contactos($idPanel, $totalSC){
        $query = "update panel set total_sc=".$totalSC." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function get_otros_contactos($mesActual, $anoActual, $codigosOtro, $campana){
        $query = "
            SELECT
                count(
                    sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
                sistema_gestiones.campaign LIKE '%".$campana."%'
            AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
            AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
            AND sistema_gestiones.cod_contacto != ''
            AND sistema_gestiones.cod_contacto IN (".implode(",", $codigosOtro).")
        ";
        try{
            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $cantidad = 0;
            while ( $row = $data->fetch_assoc() ) {
                if($row['cantidad'] == "" || $row['cantidad'] == null){
                    $cantidad = 0;
                }else{
                    $cantidad = $row['cantidad'];
                }
            }
            return $cantidad;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function put_contactos_otros_contactos($idPanel, $totalOtro){
        try{
            $db = DBConexion::conexion_destino();
            $query = "update panel set total_otro=".$totalOtro." where id = ".$idPanel;
            $data = $db->query($query);
            return $db->insert_id;

        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function get_contactos_directos_rut($mesActual, $anoActual, $codigosCD, $campana){
        $query = "
            SELECT
                count(
                    sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
                sistema_gestiones.campaign LIKE '%".$campana."%'
            AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
            AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
            AND sistema_gestiones.cod_contacto IN (".$codigosCD.")
            group by sistema_gestiones.rut_cliente
        ";
        try{

            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $contador =0;
            while ( $row = $data->fetch_assoc() ) {
                $contador = $contador+1;
            }
            return $contador;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function put_contactos_directos_rut($idPanel, $totalCDRut){
        $query = "update panel set total_cd_rut=".$totalCDRut." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function get_contactos_indirectos_rut($mesActual, $anoActual, $codigosCI, $campana){
        $query = "
            SELECT
                count(
                    sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
                sistema_gestiones.campaign LIKE '%".$campana."%'
            AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
            AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
            AND sistema_gestiones.cod_contacto IN (".$codigosCI.")
            group by sistema_gestiones.rut_cliente
        ";
        try{
            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $contador =0;
            while ( $row = $data->fetch_assoc() ) {
                $contador = $contador+1;
            }
            return $contador;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function put_contactos_indirectos_rut($idPanel, $totalCIRut){
        $query = "update panel set total_ci_rut=".$totalCIRut." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    

    public function get_canales_remotos_rut($mesActual, $anoActual, $codigosCR, $campana){
        $query = "
            SELECT
                count(
                    sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
                sistema_gestiones.campaign LIKE '%".$campana."%'
            AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
            AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
            AND sistema_gestiones.cod_contacto IN (".$codigosCR.")
            group by sistema_gestiones.rut_cliente
        ";
        try{

            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $contador =0;
            while ( $row = $data->fetch_assoc() ) {
                $contador = $contador+1;
            }
            return $contador;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function put_canales_remotos_rut($idPanel, $totalCRRut){
        $query = "update panel set total_cr_rut=".$totalCRRut." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function get_sin_contactos_rut($mesActual, $anoActual, $codigosSC, $campana){
        $query = "
            SELECT
                count(
                    sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
                sistema_gestiones.campaign LIKE '%".$campana."%'
            AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
            AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
            AND sistema_gestiones.cod_contacto IN (".$codigosSC.")
            group by sistema_gestiones.rut_cliente
        ";
        try{
            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $contador =0;
            while ( $row = $data->fetch_assoc() ) {
                $contador = $contador+1;
            }
            return $contador;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function put_sin_contactos_rut($idPanel, $totalSCRut){
        $query = "update panel set total_sc_rut=".$totalSCRut." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function get_otros_contactos_rut($mesActual, $anoActual, $codigosOtro, $campana){
        $query = "
            SELECT
                count(
                    sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
            sistema_gestiones.campaign LIKE '%".$campana."%'
            AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
            AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
            AND sistema_gestiones.cod_contacto != ''
            AND sistema_gestiones.cod_contacto IN (".implode(",", $codigosOtro).")
            group by sistema_gestiones.rut_cliente
        ";
        try{
            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $contador =0;
            while ( $row = $data->fetch_assoc() ) {
                $contador = $contador+1;
            }
            return $contador;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function put_otros_con_rut($idPanel, $totalOtroRut){
        $query = "update panel set total_otro_rut=".$totalOtroRut." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    /* #18 - # SIN_GESTION */   
    public function get_deuda_buscar_rut($mesActual, $anoActual, $campana){
            $query = "
            SELECT
                sistema_deuda.rut
            FROM
                sistema_deuda
            WHERE
                sistema_deuda.campaign_id LIKE '%".$campana."%'
            AND MONTH (sistema_deuda.fecha_carga) = ".$mesActual."
            AND YEAR (sistema_deuda.fecha_carga) = ".$anoActual."
            GROUP BY
                sistema_deuda.rut
        ";
        try{
            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $contador =0;
            $ruts = array();
            while ( $row = $data->fetch_assoc() ) {
               
                $ruts[] =  $row['rut'];
            }
            return $ruts;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function get_sin_gestion_validar_rut($mesActual, $anoActual, $campana, $rut){
        $query = "
            SELECT
                count(sistema_deuda.rut) as validar
            FROM
                sistema_deuda
                INNER JOIN sistema_gestiones ON sistema_gestiones.rut_cliente = sistema_deuda.rut
            WHERE
                sistema_deuda.campaign_id LIKE '%".$campana."%'
                AND MONTH (sistema_deuda.fecha_carga) = ".$mesActual."
                AND YEAR (sistema_deuda.fecha_carga) = ".$anoActual."
                AND sistema_deuda.rut = '".$rut."'
            ";
        try{
            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            
            while ( $row = $data->fetch_assoc() ) {
                if($row['validar'] > 0){
                    return 0;
                }else{
                    return 1;
                }
            }
            return 0;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
   
    public function put_sin_gestion_rut($idPanel, $sinGestionRut){
        $query = "update panel set sin_gestion=".$sinGestionRut." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    /* #25 - # PROMESA_PAGO */
    public function get_promesas_rut($mesActual, $anoActual, $campana){
        $query = "
            SELECT
                count(
                        sistema_gestiones.rut_cliente
                ) AS cantidad
            FROM
                sistema_gestiones
            WHERE
                sistema_gestiones.campaign LIKE '%".$campana."%'
                AND MONTH (sistema_gestiones.fecha) = ".$mesActual."
                AND YEAR (sistema_gestiones.fecha) = ".$anoActual."
                AND cod_gestion = 'PP'
            group by sistema_gestiones.rut_cliente
            ";
        try{

            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            $contador =0;
            while ( $row = $data->fetch_assoc() ) {
                $contador = $contador+1;
            }
            return $contador;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
    
    public function put_sin_promesas_rut($idPanel, $promesaPago){
        $query = "update panel set promesa_pago=".$promesaPago." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    /* #27 - # PROMESA CUMPLIDA */
    public function get_gestion_validar_rut($mesActual, $anoActual, $campana, $rut){
        $query = "
        SELECT
            count(sistema_deuda.rut) as validar
        FROM
            sistema_deuda
            INNER JOIN sistema_gestiones ON sistema_gestiones.rut_cliente = sistema_deuda.rut
        WHERE
            sistema_deuda.campaign_id LIKE '%".$campana."%'
            AND MONTH (sistema_deuda.fecha_carga) = ".$mesActual."
            AND YEAR (sistema_deuda.fecha_carga) = ".$anoActual."
            AND cod_gestion = 'PP'
            AND sistema_deuda.rut = '".$rut."'
        ";
        try{
            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            
            while ( $row = $data->fetch_assoc() ) {
                if($row['validar'] > 0){
                    return 1;
                }else{
                    return 0;
                }
            }
            return 0;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }

    public function get_promesa_validar_rut($mesActual, $anoActual, $campana, $rut){
            $query = "
            SELECT
                count(sistema_deuda.rut) as validar
            FROM
                sistema_deuda
                INNER JOIN sistema_pagos_general ON sistema_pagos_general.rut = sistema_deuda.rut
            WHERE
                sistema_deuda.campaign_id LIKE '%".$campana."%'
                AND MONTH (sistema_pagos_general.fecha_ingreso) = ".$mesActual."
                AND YEAR (sistema_pagos_general.fecha_ingreso) = ".$anoActual."
                AND sistema_deuda.rut = '".$rut."'
            GROUP BY
                sistema_deuda.rut
        ";
        try{
            $db = DBConexion::conexion_origen();
            $data = $db->query($query);
            
            while ( $row = $data->fetch_assoc() ) {
                if($row['validar'] > 0){
                    return 1;
                }else{
                    return 0;
                }
            }
            return 0;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
  
    public function put_promesas_cumplidas_rut($idPanel, $promesaCumplida){
        $query = "update panel set promesa_cumplida=".$promesaCumplida." where id = ".$idPanel;
        try{
            $db = DBConexion::conexion_destino();
            $data = $db->query($query);
            return $db->insert_id;
            
        }catch(mysqli_sql_exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar("Error: ".$e->getMessage());
            $log->registrar("Query: ".$query);
        }
    }
}