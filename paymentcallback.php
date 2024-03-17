<?php
error_log('Payment callback called!');
// Disable security.insecure_field_warning.contextual.enabled for http testing
foreach ($_POST as $key => $value) {
    echo ($key . ": " . $value . "<BR>");
    error_log ($key . ": " . $value);
}

if (isset($_POST['STATUS'])) {
    $status = $_POST['STATUS'];
    if($status=='000'){
        $checkout = WC_Checkout::instance();
        $checkout->process_checkout();
        ?>
            <script type="text/javascript">
                window.parent.document.getElementById('place_order').type="submit";
                window.parent.document.getElementById('place_order').onclick='';
                window.parent.document.getElementById('order_comments').value='< ?=$_POST['MERCHANT_OPERATION']?>';
                window.parent.document.getElementById('place_order').click();
            </script>
        <?php
    } else if ($status=='960') {
        ?>
            <script type="text/javascript">
                window.parent.document.getElementById('place_order').type="submit";
                window.parent.document.getElementById('place_order').onclick='';
                window.parent.document.getElementById('order_comments').value='< ?=$_POST['MERCHANT_OPERATION']?>';
                window.parent.document.getElementById('place_order').click();
            </script>
        <?php
    }else{
        ?>
            <script type="text/javascript">
                window.parent.document.getElementById('place_order').style.visibility="visible";
            </script>
        <?php
    }
}
?>