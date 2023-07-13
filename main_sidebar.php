<?php include_once("loginhandler.php"); ?>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <a href="/profile">
        <div class="user-panel<?php echo ($page == 'profile') ? ' active' : ''; ?>">
            <div class="pull-left image">
                <img src="/dist/img/user.png" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p><?php echo $_SESSION['user_name']." ".$_SESSION['user_surname'];?></p>
                <!-- Status -->
                <span style="font-size: 11px;"><?php echo $usertype; ?></span>
            </div>
        </div>
        </a>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">HAUPTMEN&Uuml;</li>
            <!-- Optionally, you can add icons to the links -->
            <li<?php echo ($page == 'dashboard') ? ' class="active"' : ''; ?>><a href="/home"><i class='fa fa-dashboard'></i> <span>Dashboard</span></a></li>
            <li class="treeview<?php echo ($page == 'alarmoverview') ? ' active' : ''; ?>">
                <a href=""><i class='fa fa-bell'></i> <span>Alarm</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu" id="alarmtreeview">
                    <li<?php echo ($page == 'alarmoverview') ? ' class="active"' : ''; ?>><a href="/alarmoverview"><i class="fa fa-circle-o"></i> &Uuml;bersicht</a></li>
                </ul>
            </li>
            <?php if (isset($_SESSION['user_admin']) && $_SESSION['user_admin'] != '0'): ?>
                <li<?php echo ($page == 'usermanager') ? ' class="active"' : ''; ?>><a href="/usermanager"><i class='fa fa-users'></i> <span>Benutzerverwaltung</span></a></li>
            <?php endif; ?>
            <li class="treeview<?php echo ($page == 'cammanager' || $page == 'cammonitor' || $page == 'camimgmanager') ? ' active' : ''; ?>">
                <a href=""><i class='fa fa-video-camera'></i> <span>Kameras</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu" id="camtreeview">
                    <li<?php echo ($page == 'camimgmanager') ? ' class="active"' : ''; ?>><a href="/camimgmanager"><i class="fa fa-circle-o"></i> Bilderverwaltung</a></li>
                    <li<?php echo ($page == 'cammonitor') ? ' class="active"' : ''; ?>><a href="" id="sidebar-monitor-link"><i class="fa fa-circle-o"></i> Live Monitor</a></li>
                    <li<?php echo ($page == 'cammanager') ? ' class="active"' : ''; ?>><a href="/cammanager"><i class="fa fa-circle-o"></i> Kameraverwaltung</a></li>
                </ul>
            </li>
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>