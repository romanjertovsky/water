<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?=URL_CSS?>bootstrap.min.css">
    <link rel="stylesheet" href="<?=URL_CSS?>main.min.css" />


    <title><?=$this->sTitle?></title>
</head>
<body>
<?php
if(Core::getUser()->isLoggedIn())
    include 'menu.php';
?>



<div class="m-3">

    <?php
    $aAlerts = Core::getView()->getAlerts();
    if(!empty($aAlerts)) {
        foreach ($aAlerts as $val) {

            switch ($val[1]) {
                case 0:
                    print "\n<div class=\"mx-auto alert alert-primary\">$val[0]</div>\n";
                    break;
                case 1: // green
                    print "\n<div class=\"mx-auto alert alert-success\">$val[0]</div>\n";
                    break;

                case 2: // yellow
                    print "\n<div class=\"mx-auto alert alert-warning\">$val[0]</div>\n";
                    break;

                case 3: // red
                    print "\n<div class=\"mx-auto alert alert-danger\">$val[0]</div>\n";
                    break;
            }


        }
    }
    ?>

    <h4><?=$this->sTitle?></h4>
    <br>

    <?=$this->sModContent?>


    <br><br>
    <?php
    $aAdvises = Core::getView()->getAdvices();
    if(!empty($aAdvises)) {
        foreach ($aAdvises as $val) {

            print "\n<div class=\"col-sm-6 alert alert-dark  \">$val</div>\n";

        }
    }
    ?>



</div>

<?php

$aModals = Core::getView()->getSModals();

if(!empty($aModals)) {
    foreach ($aModals as $val) {
        print "$val";
    }
}

?>


<br /><br /><br />
<br /><br /><br />
<div class="text-center">
    <small><?php echo 'Gen. time: ' . round(microtime(true) - TIME_START, 4) . ' sec.'; ?></small>
</div>

</body>

<script src="<?=URL_JS?>jquery-3.5.1.min.js"></script>
<script src="<?=URL_JS?>bootstrap.bundle.min.js"></script>
<script src="<?=URL_JS?>common.min.js"></script>
<?php

$aScripts = Core::getView()->getSScripts();

if(!empty($aScripts)) {
    foreach ($aScripts as $val) {
        print "<script>\n";
        print "$val\n";
        print "</script>\n\n";
    }
}

?>

</html>