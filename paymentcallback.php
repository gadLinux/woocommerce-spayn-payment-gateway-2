<?php
foreach ($_POST as $key => $value) {
    echo ($key . ": " . $value . "<BR>");
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
        }else{
            ?>
                <script type="text/javascript">
                    window.parent.location.reload();
                </script>
            <?php
        }
}
?>