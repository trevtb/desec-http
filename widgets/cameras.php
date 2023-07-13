<?php
if (!isset($page) || $page != 'dashboard') {
    require("../classes/Config.php");
    require("../classes/PDOConnection.php");
    require("../classes/CamHelper.php");
    require("../loginhandler.php");
    $pdocon = PDOConnection::getPdoConnection();
} else {
    include_once("loginhandler.php");
} //endif

if (!isset($pdocon)) {
    echo "FEHLER: Es existiert keine Datenbankverbindung.";
    exit();
} //endif

$camhelper = new CamHelper();
$cammode = $camhelper->getCamWidgetRefreshMode($_SESSION['user_id']);
$camid = $camhelper->getCamWidgetCamId($_SESSION['user_id']);

$res = $pdocon->prepare("SELECT * FROM `camera`;");
$res->execute();
$cams = $res->fetchAll(PDO::FETCH_ASSOC);
?>
<script type="text/javascript">
    window.camwidgetrefmode = '<?php echo $cammode;?>';
</script>
<div style="position: relative; text-align: center; padding: 10px;" id="camwidget_monitor" class="chart tab-pane">
    <div class="camwidget_fails alert alert-info" id="camwidget-nocamwarn" style="display: none;">
        <h4><i class="fa fa-info"></i> Kamera ausw&auml;hlen</h4>
        Es wurde keine Kamera ausgew&auml;hlt.
    </div>
    <div class="camwidget_fails alert alert-info" id="camwidget-creatingwarn" style="display: none;">
        <h4><i class="fa fa-info"></i> Einstellungen werden &uuml;bernommen</h4>
        Das System f&uuml;hrt gerade Konfigurationsarbeiten f&uuml;r die ausgew&auml;hlte Kamera durch. Aktualisieren Sie dieses Fenster von Zeit zu Zeit.
    </div>
    <div class="camwidget_fails alert alert-danger" id="camwidget-offlinewarn" style="display: none;">
        <h4><i class="fa fa-ban"></i> Kamera offline</h4>
        Die Kamera ist im Netzwerk nicht erreichbar.
    </div>
    <div class="camwidget_fails alert alert-danger" id="camwidget-loginwarn" style="display: none;">
        <h4><i class="fa fa-ban"></i> Falsche Logindaten</h4>
        Der Benutzername oder das Passwort f&uuml;r die ausgew&auml;hlte Kamera wurden falsch eingegeben.
    </div>
    <img id="camwidget-img" data-id="<?php echo $camid; ?>" src="" alt="" style="display: <?php echo ($camid == '-1') ? 'none' : 'block'; ?>;" class="img-responsive" />
</div>
<div style="position: relative; padding: 10px;" id="camwidget_settings" class="chart tab-pane">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-md-9">
            <form role="form">
                <div class="form-group">
                    <label>Kamera:</label>
                    <select class="form-control" id="camwidgetcamselect">
                        <option value="-1"<?php echo ($camid == '-1') ? ' selected' : '';?>>---  Kamera ausw&auml;hlen  ---</option>
                        <?php foreach ($cams as $camera): ?>
                            <option value="<?php echo $camera['id']; ?>"<?php echo ($camera['id'] == $camid) ? ' selected' : '';?>><?php echo $camera['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <form role="form">
                <div class="form-group">
                    <label>
                        Aktualisierungsintervall:
                        <div class="radio">
                            <label>
                                <input id="camwmodeRadio1" type="radio" checked="" value="live" name="camwmode"<?php echo ($cammode == 'live') ? ' checked' : ''; ?>>
                                Live
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input id="camwmodeRadio2" type="radio" value="poll" name="camwmode" <?php echo ($cammode == 'poll') ? ' checked' : ''; ?>>
                                30 Sekunden
                            </label>
                        </div>
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>