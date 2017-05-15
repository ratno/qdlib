<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page->CustomTitle ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <?php if (file_exists(__WEB_PATH__ . "/favicon.ico")) : ?>
        <link href="<?php echo __WEB_URL__ ?>/favicon.ico" type="image/x-icon" rel="icon"/>
        <link href="<?php echo __WEB_URL__ ?>/favicon.ico" type="image/x-icon" rel="shortcut icon"/>
    <?php endif; ?>

    <?php echo $page->css(array('index.css', 'qd', 'icon.min', 'anytimec.css')) ?>
    <?php
    echo $page->js(array(
        "web_url = '" . __WEB_URL__ . "'; js_url = '" . __JS_URL__ . "'; css_url ='" . __CSS_URL__ . "'; images_url = '" . __IMAGES_URL__ . "';",
        __QDFJQUERY_BASE__,
        __QDFJQUERY_EFFECTS__,
        "jquery/jquery.ajaxq-0.0.1.js",
        "head.js",
        __QDF_JS_CORE__,
        "control.js"
    ));
    ?>
</head>
<body>
<div id="topnav_container">
    <div id="topnav">
        <ul>
            <?php echo $strTopNavHtml ?>
        </ul>
        <div class="clear"></div>
    </div>
</div>
<div id="topsubnav_container">
    <div id="topsubnav">
        <ul>
            <li><a href="#">&nbsp;</a></li>
            <?php echo $strSubNavHtml ?>
        </ul>
        <div class="clear"></div>
    </div>
</div>
<div id="wrapper">
    <div class="contentheader">
        <h2><?php echo $page->CustomTitle ?></h2>
        <div id="bc"><?php echo $page->CustomSubTitle ?></div>
    </div>
    <div class="clear"></div>
    <div id="flash"><?php echo $page->flash(); ?></div>
    <div id="cwrapper"><?php echo $PageContent; ?></div>
    <div class="clear"></div>
</div>

<?php echo $page->js(array('tiny_mce/jquery.tinymce.js', 'close.js', 'index.js', 'anytimec.js')) ?>

<div id="footer">
    <div id="copy">
        <?php QApplication::Footer() ?>
        <br/>
        <span style="font-size: 9px">Processed in <?php echo QTimer::Stop(); ?> seconds</span>
    </div>
</div>

</body>
</html>