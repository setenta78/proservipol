<?php
function listaTotalRequerimientoDerivadas($Unidad, $nombreBucar, $escalafon, $grado, $NombreCampo, $TipoOrden, $funcionarios)
{
    $FechaHoy = date("Y-m-d");
    $sql = "SELECT  
               `SOLICITUD`.`SOL_CODIGO`,
               `SOLICITUD`.`UNI_CODIGO`,
               `SOLICITUD`.`SOL_FECHA`,
               `PROBLEMA`.`PROB_DESCRIPCION`,
               `SUBPROBLEMA`.`SUBP_DESCRIPCION`,
               `TIPO_MOVIMIENTO`.`TMOV_DESCRIPCION`,
               UNIDAD.UNI_DESCRIPCION,
               CONCAT_WS(' ', UCASE(`SOLICITUD`.VALOR_IDENTI1), UCASE(`SOLICITUD`.VALOR_IDENTI2)) AS IDENTIFICADORES,
               DATEDIFF(NOW(),FECHA) AS DIF_DIAS,
               CONCAT_WS(' ',  `TIPO_MOVIMIENTO`.`TMOV_DESCRIPCION`,'POR:',GRADO.GRA_DESCRIPCION, FUNCIONARIO.FUN_APELLIDOPATERNO, FUNCIONARIO.FUN_APELLIDOMATERNO, FUNCIONARIO.FUN_NOMBRE) AS DATO_OPER,
               MOVIMIENTO.MOV_CODIGO    
              FROM
               `SOLICITUD`
                INNER JOIN `MOVIMIENTO` ON (`SOLICITUD`.`SOL_CODIGO` = `MOVIMIENTO`.`SOL_CODIGO`)
                INNER JOIN `SUBPROBLEMA` ON (`SOLICITUD`.`PROB_CODIGO` = `SUBPROBLEMA`.`PROB_CODIGO`)
                AND (`SOLICITUD`.`SUBP_CODIGO` = `SUBPROBLEMA`.`SUBP_CODIGO`)
                INNER JOIN `PROBLEMA` ON (`SUBPROBLEMA`.`PROB_CODIGO` = `PROBLEMA`.`PROB_CODIGO`)
                INNER JOIN `TIPO_MOVIMIENTO` ON (`MOVIMIENTO`.`TMOV_CODIGO` = `TIPO_MOVIMIENTO`.`TMOV_CODIGO`)
                INNER JOIN UNIDAD ON(SOLICITUD.UNI_CODIGO = UNIDAD.UNI_CODIGO)
                INNER JOIN FUNCIONARIO ON (MOVIMIENTO.FUNCIONARIO_IMPLICADO = FUNCIONARIO.FUN_CODIGO)
            INNER JOIN GRADO ON (FUNCIONARIO.ESC_CODIGO = GRADO.ESC_CODIGO)
             AND (FUNCIONARIO.GRA_CODIGO = GRADO.GRA_CODIGO)
                WHERE 
             TIPO_MOVIMIENTO.TMOV_CODIGO IN(70,80)
              AND SOLICITUD.SOL_FECHA >= '20240301'
                AND FECHA_TERMINO IS NULL  AND TMOV_DESCRIPCION <> 'CIERRE: RESUELTO FAVORABLEMENTE' AND TMOV_DESCRIPCION <> 'CIERRE: RESUELTO DESFAVORABLEMENTE' 
                AND TMOV_DESCRIPCION <> 'CIERRE: INADMISIBLE'
                 ORDER BY   
          SOLICITUD.SOL_FECHA DESC
           ";
    $i = 0;
    $result = $this->execstmt($this->Conecta(), $sql);
    mysql_close();
    while ($myrow = mysql_fetch_array($result)) {
        $dioscar = new lSolicitud;
        $dioscar->setCodigoSolicitud($myrow["SOL_CODIGO"]);
        $dioscar->setUnidad($myrow["UNI_CODIGO"]);
        $dioscar->setFechaSolicitud($myrow["SOL_FECHA"]);
        $dioscar->setProblema(STRTOUPPER($myrow["PROB_DESCRIPCION"]));
        $dioscar->setSubProblema(STRTOUPPER($myrow["SUBP_DESCRIPCION"]));
        $dioscar->setTipoMovimiento(STRTOUPPER($myrow["TMOV_DESCRIPCION"]));
        $dioscar->setUnidadOrigen(STRTOUPPER($myrow["UNI_DESCRIPCION"]));
        $dioscar->setIdentificadores(STRTOUPPER($myrow["IDENTIFICADORES"]));
        $dioscar->setDiferenciaDias(STRTOUPPER($myrow["DIF_DIAS"]));
        $dioscar->setImplicado(STRTOUPPER($myrow["DATO_OPER"]));
        $dioscar->setCorrelativoMov(STRTOUPPER($myrow["MOV_CODIGO"]));
        $funcionarios[$i] = $dioscar;
        $i++;
    }
}