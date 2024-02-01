<?php
    if (isset($_GET['MESSAGE']) && isset($_GET['TRACE_ID'])) {
        $message=$_GET['MESSAGE'];
        $trace_id=$_GET['TRACE_ID'];
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if($lang == 'es') {
        ?>
        <h3>No se pudo procesar el pago.<br>
        Indique este código cuando lo reporte: <?= $trace_id?>.<br></h3>
        Recargue la página para reintentar el pago.
        <?php
        } else { 
        ?>
        <h3>Cannot process payment.<br>
        Please indicate this code when reporting: <?= $trace_id?>.<br></h3>
        Reload the page to retry payment.
        <?php
        }
    }else{die;}
?>