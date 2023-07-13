<?php include_once("loginhandler.php"); ?>

<?php
    $nosettings = true;
?>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <div style="padding: 10px;">
        <?php if ($page == 'cammonitor'): ?>
        <?php $nosettings = false; ?>
        <form method="post">
            <h3 class="control-sidebar-heading"><i class="fa fa-video-camera"></i>  Einstellungen</h3>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    Aktualisierungsintervall
                    <div class="radio">
                        <label>
                            <input id="camrefmodeRadio1" type="radio" value="live" name="camrefmode"<?php echo ($pmode == 'live') ? ' checked' : ''; ?>>
                            Live
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input id="camrefmodeRadio2" type="radio" value="poll" name="camrefmode" <?php echo ($pmode == 'poll') ? ' checked' : ''; ?>>
                            30 Sekunden
                        </label>
                    </div>
                </label>
                <p>
                    Regelt das Aktualisierungsintervall f&uuml;r die Vorschaubilder.
                </p>
            </div>
        </form>
        <?php endif; ?>
        <?php if ($page == 'dashboard'): ?>
        <?php $nosettings = false; ?>
        <form method="post">
            <h3 class="control-sidebar-heading"><i class="fa fa-dashboard"></i>  Einstellungen</h3>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    Widgets anzeigen:
                    <div class="checkbox">
                        <label>
                            <input class="csbar_dashboard_cbox" data-name="users" type="checkbox" value="1"<?php echo ($userwidget) ? ' checked' : ''; ?>>
                            Benutzer online
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input class="csbar_dashboard_cbox" data-name="cameras" type="checkbox" value="1"<?php echo ($camwidget) ? ' checked' : ''; ?>>
                            Kamera&uuml;bersicht
                        </label>
                    </div>
                </label>
                <p>
                    Blendet Widgets ein und aus.
                </p>
            </div>
        </form>
        <?php endif; ?>
        <?php if ($nosettings): ?>
            <p>F&uuml;r diese Ansicht sind keine Einstellungen verf&uuml;gbar.</p>
        <?php endif; ?>
    </div>
</aside>
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class='control-sidebar-bg'></div>