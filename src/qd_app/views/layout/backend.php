<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="<?php echo csrf_token() ?>">
    <title><?php echo $page->CustomTitle ?></title>
    <?php if (file_exists(__WEB_PATH__ . "/favicon.ico")) : ?>
        <link href="<?php echo __WEB_URL__ ?>/favicon.ico" type="image/x-icon" rel="icon"/>
        <link href="<?php echo __WEB_URL__ ?>/favicon.ico" type="image/x-icon" rel="shortcut icon"/>
    <?php endif; ?>

    <?php
    echo $page->css(array(
        'bootstrap.min',
        'bootstrap-responsive.min.css',
        'blue.css',
        'BreadCrumb',
        'jquery.qtip.min.css',
        'splashy',
        'style',
//        'index.css', 
        'customreset.css',
        'qd',
        'icon',
        'ui-icon',
        'jquery.tablePagination',
        __QDFJQUERY_CSS__,
        'footable-0.1.css',
        'form.css',
        'hint.css'
    ));
    ?>
    <!--[if lte IE 8]>
    <?php echo $page->css(array("ie.css")); ?>
    <?php echo $page->js(array("ie/html5.min.js", "ie/respond.min.js")); ?>
    <![endif]-->

    <?php
    echo $page->js(array(
        "document.documentElement.className += 'js'",
        "web_url = '" . __WEB_URL__ . "'; js_url = '" . __JS_URL__ . "'; css_url ='" . __CSS_URL__ . "'; images_url = '" . __IMAGES_URL__ . "';",
        __QDFJQUERY_BASE__,
        "$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')}});",
        __QDFJQUERY_EFFECTS__,
        "jquery/jquery.ajaxq-0.0.1.js",
        "head.js",
        __QDF_JS_CORE__,
        "control.js"
    ));
    ?>

</head>
<body>
<div id="loading_layer" style="display:none"><?php echo img("waiticon.gif") ?></div>

<div id="maincontainer" class="clearfix">
    <!-- header -->
    <header>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="brand" href="<?php echo qd_url("home", "cpanel") ?>"><?php echo __APPSNAME__ ?></a>
                    <ul class="nav user_menu pull-right">
                        <li class="hidden-phone hidden-tablet">
                            <div class="nb_boxes clearfix">
                                <?php if (__ENABLE_NOTIFICATION__) : ?>
                                    <a data-toggle="modal" href="#myNotif" class="label ttip_b" title="Notifikasi Baru"><span
                                                id="myNotifCnt">0</span> <i class="splashy-document_letter_warning"></i></a>
                                <?php endif ?>
                                <!--                    <a data-toggle="modal" data-backdrop="static" href="#myMail" class="label ttip_b" title="New messages">25 <i class="splashy-mail_light"></i></a>-->
                                <!--                    <a data-toggle="modal" data-backdrop="static" href="#myTasks" class="label ttip_b" title="New tasks">10 <i class="splashy-calendar_week"></i></a>-->
                            </div>
                        </li>
                        <li class="divider-vertical hidden-phone hidden-tablet"></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?php echo __OTHERS_URL__ . $page->User->ProfilePictureFile ?>" height="24"/> <?php echo $page->User->Name; ?>
                                <?php if (QApplication::CheckTask("home", "myaccount") || QApplication::CheckTask("home", "preferences")) : ?>
                                    <b class="caret"></b>
                                <?php endif; ?>
                            </a>
                            <?php if (QApplication::CheckTask("home", "myaccount") || QApplication::CheckTask("home", "preferences")) : ?>
                                <ul class="dropdown-menu">
                                    <?php if (QApplication::CheckTask("home", "myaccount")): ?>
                                        <li><a href="<?php echo qd_url("home", "myaccount") ?>"><i
                                                        class="icon-user"></i> My Account</a></li>
                                    <?php endif; ?>
                                    <?php if (QApplication::CheckTask("home", "preferences")): ?>
                                        <li><a href="<?php echo qd_url("home", "preferences") ?>"><i
                                                        class="icon-cog"></i> Change Password</a></li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                        <li><a href="<?php echo qd_url("home", "logout") ?>"><i class="icon-off icon-white"></i> Sign
                                Out</a></li>
                    </ul>
                    <?php if (!is_empty($strScrollNavHtml)) : ?>
                        <a data-target=".nav-collapse" data-toggle="collapse" class="btn_menu">
                            <span class="icon-align-justify icon-white"></span>
                        </a>
                        <nav>
                            <div class="nav-collapse">
                                <ul class="nav">
                                    <?php echo $strScrollNavHtml ?>
                                </ul>
                            </div>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if (__ENABLE_NOTIFICATION__) : ?>
            <div class="modal hide fade" id="myNotif">
                <div class="modal-header">
                    <h3>Notifikasi Baru</h3>
                </div>
                <div class="modal-body">
                    <table class="table table-condensed table-striped table-bordered table-hover" data-rowlink="a"
                           id="myNotifTable">
                        <thead>
                        <tr>
                            <th>Notifikasi</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo qd_url("notification", "list") ?>" class="btn">Lihat Semua Notifikasi</a>
                </div>
            </div>
        <?php endif ?>
        <?php if (false) : ?>
            <div class="modal hide fade" id="myMail">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal">×</button>
                    <h3>New messages</h3>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">In this table jquery plugin turns a table row into a clickable link.
                    </div>
                    <table class="table table-condensed table-striped" data-rowlink="a">
                        <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Size</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Declan Pamphlett</td>
                            <td><a href="javascript:void(0)">Lorem ipsum dolor sit amet</a></td>
                            <td>23/05/2012</td>
                            <td>25KB</td>
                        </tr>
                        <tr>
                            <td>Erin Church</td>
                            <td><a href="javascript:void(0)">Lorem ipsum dolor sit amet</a></td>
                            <td>24/05/2012</td>
                            <td>15KB</td>
                        </tr>
                        <tr>
                            <td>Koby Auld</td>
                            <td><a href="javascript:void(0)">Lorem ipsum dolor sit amet</a></td>
                            <td>25/05/2012</td>
                            <td>28KB</td>
                        </tr>
                        <tr>
                            <td>Anthony Pound</td>
                            <td><a href="javascript:void(0)">Lorem ipsum dolor sit amet</a></td>
                            <td>25/05/2012</td>
                            <td>33KB</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" class="btn">Go to mailbox</a>
                </div>
            </div>
            <div class="modal hide fade" id="myTasks">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal">×</button>
                    <h3>New Tasks</h3>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">In this table jquery plugin turns a table row into a clickable link.
                    </div>
                    <table class="table table-condensed table-striped" data-rowlink="a">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Summary</th>
                            <th>Updated</th>
                            <th>Priority</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>P-23</td>
                            <td><a href="javascript:void(0)">Admin should not break if URL&hellip;</a></td>
                            <td>23/05/2012</td>
                            <td class="tac"><span class="label label-important">High</span></td>
                            <td>Open</td>
                        </tr>
                        <tr>
                            <td>P-18</td>
                            <td><a href="javascript:void(0)">Displaying submenus in custom&hellip;</a></td>
                            <td>22/05/2012</td>
                            <td class="tac"><span class="label label-warning">Medium</span></td>
                            <td>Reopen</td>
                        </tr>
                        <tr>
                            <td>P-25</td>
                            <td><a href="javascript:void(0)">Featured image on post types&hellip;</a></td>
                            <td>22/05/2012</td>
                            <td class="tac"><span class="label label-success">Low</span></td>
                            <td>Updated</td>
                        </tr>
                        <tr>
                            <td>P-10</td>
                            <td><a href="javascript:void(0)">Multiple feed fixes and&hellip;</a></td>
                            <td>17/05/2012</td>
                            <td class="tac"><span class="label label-warning">Medium</span></td>
                            <td>Open</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" class="btn">Go to task manager</a>
                </div>
            </div>
        <?php endif ?>
    </header>

    <!-- main content -->
    <div id="contentwrapper">
        <div class="main_content">
            <?php echo $page->CustomSubTitle ?>
            <div class="row-fluid">
                <div id="flash"><?php echo $page->flash(); ?></div>
            </div>
            <?php echo $PageContent; ?>
        </div>
    </div>

    <!-- sidebar -->
    <a href="javascript:void(0)" class="sidebar_switch on_switch ttip_r" title="Hide Sidebar">Sidebar switch</a>
    <div class="sidebar">
        <div class="antiScroll">
            <div class="antiscroll-inner">
                <div class="antiscroll-content">
                    <div class="sidebar_inner" style="background-color: #f1f1f1;">
                        <h3>Main Menu</h3>
                        <div id="side_accordion" class="accordion">
                            <?php echo $strTopNavHtml ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    <?php ob_start(); ?>
    $(document).ready(function () {
        $('table.footable').footable();
        var tablePaginationOptions = {
            rowsPerPage: 10,
            firstArrow: (new Image()).src = images_url + "/firstBlue.gif",
            prevArrow: (new Image()).src = images_url + "/prevBlue.gif",
            lastArrow: (new Image()).src = images_url + "/lastBlue.gif",
            nextArrow: (new Image()).src = images_url + "/nextBlue.gif",
            topNav: true
        };
        <?php if(__ENABLE_NOTIFICATION__) : ?>
        function update_notif_cnt() {
            $.ajax({
                type: 'GET',
                url: '<?php echo qd_url("home", "cpanel/notif_count") ?>',
                timeout: 60000,
                success: function (data) {
                    $("#myNotifCnt").html(data.total);
                    window.setTimeout(update_notif_cnt, 10000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $("#myNotifCnt").html('error');
                    window.setTimeout(update_notif_cnt, 60000);
                }
            });
        }

        update_notif_cnt();

        $("#myNotif").on('shown', function () {
            $("#myNotif").children(".modal-body").html("<?php echo img("wait16trans.gif") ?>");
            $.ajax({
                type: 'GET',
                url: '<?php echo qd_url("home", "cpanel/notif") ?>',
                timeout: 60000,
                success: function (data) {
                    if (data.status == "ok") {
                        $("#myNotif").children(".modal-body").html(data.data);
                        $("#myNotifTable").tablePagination(tablePaginationOptions);
                        $("#myNotifCnt").html(data.total);
                    } else {
                        $("#myNotif").children(".modal-body").html(data.status);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $("#myNotif").children(".modal-body").html('Timeout contacting server..');
                }
            });
        });
        <?php endif ?>
    });
    <?php $endscript = ob_get_contents(); ob_end_clean(); ?>
</script>

<?php
echo $page->js(array(
    'jquery.debouncedresize.min.js',
    'jquery.actual.min.js',
    'jquery.cookie.min.js',
    'bootstrap.min.js',
    'jquery.qtip.min.js',
    'jquery.jBreadCrumb.1.1.min.js',
    'ios-orientationchange-fix.js',
    'antiscroll.js',
    'jquery-mousewheel.js',
    'qd_common.js',
    'forms/jquery.ui.touch-punch.min.js',
    'tiny_mce/jquery.tinymce.js',
    'index.js',
    'jquery.tablePagination.0.5.js',
    'jquery.treeTable.min.js',
    "jQuery(document).ready(function() {setTimeout('jQuery(\"html\").removeClass(\"js\")',100);});",
    "footable.js",
    $endscript
));
?>
</body>
</html>