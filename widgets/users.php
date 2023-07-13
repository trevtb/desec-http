<?php
    function __autoload($classfile) {
        if ($classfile != "" && is_file("../classes/".$classfile.".php")) {
            require "../classes/".$classfile.".php";
        } //endif
    } //endfunction autoload

    include_once("../loginhandler.php");

    $pdocon = PDOConnection::getPdoConnection();
    $wusql = "SELECT * FROM `user` WHERE `lastonline` >= DATE_SUB(now(), INTERVAL 1 MINUTE);";
    $wures = $pdocon->prepare($wusql);
    $wures->execute();
    $wuret1 = $wures->fetchAll(PDO::FETCH_ASSOC);

    $wusql = "SELECT COUNT(*) AS `id` FROM `user`;";
    $wures = $pdocon->prepare($wusql);
    $wures->execute();
    $wuret2 = $wures->fetchAll(PDO::FETCH_ASSOC);
    $wuret2 = $wuret2[0]['id'];
?>
<div style="max-height: 250px; overflow-y: auto;">
    <?php
    $wuhtml = '';
    $wuhtml .= '<ul class="fa-ul">';
    foreach ($wuret1 as $wuuser) {
        $wuhtml .= '<li><i class="fa-li fa fa-circle" style="color: #00a65a;"></i>'.$wuuser['name'].' '.$wuuser['surname'].'</li>';
    } //endforeach
    $wuhtml .= '</ul>';
    echo $wuhtml;
    ?>
</div>
<?php if (count($wuret1) == 1): ?>
    <p>Es ist 1 von <?php echo $wuret2;?> Benutzern online.</p>
<?php else: ?>
    <p>Es sind <?php echo count($wuret1);?> von <?php echo $wuret2;?> Benutzern online.</p>
<?php endif; ?>