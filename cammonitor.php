<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

include_once("loginhandler.php");
$page = 'cammonitor';

$dbcon = PDOConnection::getPdoConnection();
$res = $dbcon->prepare("SELECT * FROM `camera`;");
$res->execute();
$cams = $res->fetchAll(PDO::FETCH_ASSOC);

$camhelper = new CamHelper();
$pmode = $camhelper->getCamRefreshMode($_SESSION['user_id']);

$res = $dbcon->prepare("SELECT * FROM `group`;");
$res->execute();
$groups = $res->fetchAll(PDO::FETCH_ASSOC);

$res = $dbcon->prepare("SELECT * FROM `livemonitor_element` WHERE `type`=?;");
$res->execute(array('camera'));
$selcams = $res->fetchAll(PDO::FETCH_ASSOC);

$res = $dbcon->prepare("SELECT * FROM `livemonitor_element` WHERE `type`=?;");
$res->execute(array('group'));
$selgroups = $res->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT
          `camera`.`id`,
          `camera`.`name`,
          `camera`.`host`,
          `camera`.`port`,
          `camera`.`path`,
          `camera`.`ssl`,
          `camera`.`auth`,
          `camera`.`user`,
          `camera`.`password`,
          `camera`.`resolution`,
          `camera`.`type`,
          `camera`.`created`
        FROM `camera`
          INNER JOIN `group_map`
            ON `camera`.`id` = `group_map`.`camid`
          INNER JOIN `livemonitor_element`
            ON `livemonitor_element`.`eid` = `group_map`.`groupid`
        WHERE
          `livemonitor_element`.`uid`=? AND `livemonitor_element`.`type`=?
        UNION DISTINCT
        SELECT
          `camera`.`id`,
          `camera`.`name`,
          `camera`.`host`,
          `camera`.`port`,
          `camera`.`path`,
          `camera`.`ssl`,
          `camera`.`auth`,
          `camera`.`user`,
          `camera`.`password`,
          `camera`.`resolution`,
          `camera`.`type`,
          `camera`.`created`
        FROM `camera`
          INNER JOIN `livemonitor_element`
            ON `camera`.`id` = `livemonitor_element`.`eid`
        WHERE
          `livemonitor_element`.`uid`=? AND `livemonitor_element`.`type`=?;";
$res = $dbcon->prepare($sql);
$res->execute(array($_SESSION['user_id'], 'group', $_SESSION['user_id'], 'camera'));
$selres = $res->fetchAll(PDO::FETCH_ASSOC);

$camselex = false;
$groupselex = false;
if (isset($_GET['addaction']) && ($_GET['addaction'] == 'camera' || $_GET['addaction'] == 'group')) {
    switch ($_GET['addaction']) {
        case 'camera':
            $camselex = true;
            break;
        case 'group':
            $groupselex = true;
            break;
    } //endswitch
} //endif
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DESEC core | Kamera Live Monitor</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome icons -->
    <link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="/plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE skin -->
    <link href="/dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/theme.css" rel="stylesheet" type="text/css" />
    <!-- S-Gallery -->
    <link rel="stylesheet" href="/plugins/s-gallery/css/styles.css" />
    <!-- Page CSS -->
    <link href="/dist/css/pages/cammonitor.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/plugins/html5shiv/html5shiv.min.js"></script>
    <script src="/plugins/respond/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-blue sidebar-mini">

<script type="text/javascript">
    window.refmode = '<?php echo $pmode; ?>';
</script>

<div class="wrapper">
    <?php
    include_once("header.php");
    include_once("main_sidebar.php");
    ?>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                <i class="fa fa-video-camera"></i> Kamera Live Monitor
            </h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-video-camera"></i> Home</a></li>
                <li class="active">Kamera Live Monitor</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                    <div id="gallery-container" class="box box-danger">
                        <div id="noitems-info" style="display: <?php echo (empty($selres)) ? 'block' : 'none';?>;">
                            <p class="lead">Es wurden keine Ger&auml;te oder Gruppen ausgew&auml;hlt.</p>
                        </div>
                        <ul class="items--small">
                            <?php foreach($selres as $cam): ?>
                                <?php
                                    $imgsrc = '<img src="/dist/img/blank_320.jpg" alt="" class="galprevimg" id="lm-imgsrc-'.$cam['id'].'" data-id="'.$cam['id'].'" />';
                                ?>
                                <li class="item" style="border: 1px solid #d2d6de; position: relative; min-height: 24%;" id="galsm-item-<?php echo $cam['id']; ?>">
                                    <a href="" title="<?php echo $cam['name'];?>">
                                        <?php echo $imgsrc;?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <ul class="items--big">
                            <?php foreach($selres as $cam): ?>
                                <?php
                                    $res = $cam['resolution'];
                                    $res = explode('x', $res);
                                    $res = $res[0];
                                ?>
                                <li class="item--big" id="galxl-item-<?php echo $cam['id']; ?>">
                                    <a href="">
                                        <figure>
                                            <img src="/dist/img/blank_<?php echo $res;?>.jpg" alt="" id="fwimg-<?php echo $cam['id']; ?>" data-id="<?php echo $cam['id']; ?>" />
                                            <figcaption class="img-caption">
                                                <?php echo $cam['name']; ?>
                                            </figcaption>
                                        </figure>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="controls">
                            <span class="control icon-arrow-left" data-direction="previous"></span>
                            <span class="control icon-arrow-right" data-direction="next"></span>
                            <span class="grid icon-grid"></span>
                            <span class="fs-toggle icon-fullscreen"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row" id="monitorrow">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="box box-primary<?php echo ($camselex) ? '' : ' collapsed-box';?>" id="camselbox">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-video-camera"></i> Kameraauswahl</h3>
                                    <div class="box-tools pull-right">
                                        <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-<?php echo ($camselex) ? 'minus' : 'plus';?>"></i></button>
                                    </div>
                                </div>
                                <div class="box-body"<?php echo ($camselex) ? '' : ' style="display: none;"';?>>
                                    <?php foreach ($cams as $camelem): ?>
                                        <?php
                                        $sccheck = false;
                                        foreach ($selcams as $sc) {
                                            if ($sc['eid'] == $camelem['id']) {
                                                $sccheck = true;
                                            } //endif
                                        } //endforeach
                                        ?>
                                        <div>
                                            <label class="selcelab">
                                                <input id="selce-<?php echo $camelem['id'];?>" class="selceitems" type="checkbox" value="<?php echo $camelem['id'];?>" name="selceitems[]"<?php echo ($sccheck) ? ' checked' : '';?>>
                                                <?php echo $camelem['name'];?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="box box-primary<?php echo ($groupselex) ? '' : ' collapsed-box';?>" id="groupselbox">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-th"></i> Gruppenauswahl</h3>
                                    <div class="box-tools pull-right">
                                        <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-<?php echo ($groupselex) ? 'minus' : 'plus';?>"></i></button>
                                    </div>
                                </div>
                                <div class="box-body"<?php echo ($groupselex) ? '' : ' style="display: none;"';?>>
                                    <?php foreach ($groups as $groupelem): ?>
                                        <?php
                                        $sgcheck = false;
                                        foreach ($selgroups as $sg) {
                                            if ($sg['eid'] == $groupelem['id']) {
                                                $sgcheck = true;
                                            } //endif
                                        } //endforeach
                                        ?>
                                        <div>
                                            <label>
                                                <input id="selge-<?php echo $groupelem['id'];?>" class="selgeitems" type="checkbox" value="<?php echo $groupelem['id'];?>" name="selgeitems[]"<?php echo ($sgcheck) ? ' checked' : '';?>>
                                                <?php echo $groupelem['name'];?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php
    include_once("footer.php");
    include_once("control_sidebar.php");
    ?>
</div>

<!-- jQuery 2.1.4 -->
<script src="/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- SlimScroll -->
<script src="/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- FastClick -->
<script src='/plugins/fastclick/fastclick.min.js'></script>
<!-- AdminLTE App -->
<script src="/dist/js/app.min.js" type="text/javascript"></script>
<!-- S Gallery -->
<script src="/plugins/s-gallery/js/plugins.js"></script>
<script src="/plugins/s-gallery/js/scripts.js"></script>
<!-- spin.js -->
<script src="/plugins/spinjs/spin.min.js"></script>

<!-- Sidebar JS -->
<script src="/dist/js/sidebar.js" type="text/javascript"></script>
<!-- Page JS, includes polling!!! -->
<script src="/dist/js/pages/cammonitor.js" type="text/javascript"></script>
</body>
</html>
