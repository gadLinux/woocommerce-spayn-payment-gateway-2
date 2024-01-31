<?php
    if (isset($_GET['TOKEN']) && isset($_GET['URL'])) {
        $token=$_GET['TOKEN'];
        $url=$_GET['URL'];
        ?>
            <form id="spayn_form_post_<?=$token?>" action="<?= $url?>" method="POST" autocomplete="off">
            <input type="hidden" name="TOKEN" value="<?= $token?>">
            <noscript>
            <h2>JavaScript is currently disabled or is not supported by your browser.<br></h2>
            <h3>Please click Submit to continue the processing of your 3-D Secure transaction.</h3><input type="submit" value="Submit">
            </noscript></form>
            <script type="text/javascript">
                document.forms['spayn_form_post_<?=$token?>'].submit();
            </script>
        <?php
    }else{
        error_log('Operation with tracking code ' . $_GET['TRACKING_CODE'] . ' cannot be processed');
        die;
    }
?>