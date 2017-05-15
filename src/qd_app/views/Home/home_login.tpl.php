<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo __APPSNAME__ ?> :: Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td, article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary, time, mark, audio, video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline
        }

        article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {
            display: block
        }

        body {
            line-height: 1
        }

        ol, ul {
            list-style: none
        }

        blockquote, q {
            quotes: none
        }

        blockquote:before, blockquote:after, q:before, q:after {
            content: '';
            content: none
        }

        table {
            border-collapse: collapse;
            border-spacing: 0
        }
    </style>

    <link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900'>
    <link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Montserrat:400,700'>
    <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>

    <style type="text/css">
        /* Form */
        .form {
            position: relative;
            z-index: 1;
            background: #FFFFFF;
            max-width: 300px;
            margin: 0 auto;
            padding: 30px;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            border-bottom-left-radius: 3px;
            border-bottom-right-radius: 3px;
            text-align: center;
        }

        .form .thumbnail {
            background: #EF3B3A;
            width: 150px;
            height: 150px;
            margin: 0 auto 30px;
            padding: 50px 30px;
            border-top-left-radius: 100%;
            border-top-right-radius: 100%;
            border-bottom-left-radius: 100%;
            border-bottom-right-radius: 100%;
            box-sizing: border-box;
        }

        .form .thumbnail img {
            display: block;
            width: 100%;
        }

        .form input {
            outline: 0;
            background: #f2f2f2;
            width: 100%;
            border: 0;
            margin: 0 0 15px;
            padding: 15px;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            border-bottom-left-radius: 3px;
            border-bottom-right-radius: 3px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .form .buttonlogin {
            outline: 0;
            background: rgba(26, 156, 244, 0.8);;
            width: 100%;
            border: 0;
            padding: 15px;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            border-bottom-left-radius: 3px;
            border-bottom-right-radius: 3px;
            color: #FFFFFF;
            font-size: 14px;
            -webkit-transition: all 0.3 ease;
            transition: all 0.3 ease;
            cursor: pointer;
        }

        .form .message {
            margin: 15px 0 0;
            color: #b3b3b3;
            font-size: 12px;
        }

        .form .message a {
            color: #EF3B3A;
            text-decoration: none;
        }

        .form .register-form {
            display: none;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 300px;
            margin: 0 auto;
        }

        .container:before, .container:after {
            content: "";
            display: block;
            clear: both;
        }

        .container .info {
            margin: 50px auto;
            text-align: center;
        }

        .container .info h1 {
            margin: 0 0 15px;
            padding: 0;
            font-size: 36px;
            font-weight: 300;
            color: #1a1a1a;
        }

        .container .info span {
            color: #4d4d4d;
            font-size: 12px;
        }

        .container .info span a {
            color: #000000;
            text-decoration: none;
        }

        .container .info span .fa {
            color: #EF3B3A;
        }

        .container .footer {
            margin: 0px auto;
            text-align: center;
        }

        .container .footer span {
            color: white;
            font-size: 12px;
        }

        /* END Form */
        /* Demo Purposes */
        body {
            background: #ccc;
            font-family: "Roboto", sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body:before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            display: block;
            background: rgba(40, 136, 177, 0.8);
            width: 100%;
            height: 100%;
        }

        #video {
            z-index: -99;
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            -webkit-transform: translateX(-50%) translateY(-50%);
            transform: translateX(-50%) translateY(-50%);
        }

        #logo {
            width: 100px;
            margin-bottom: 5px;
        }

        /* flash messages */
        #flash {
            margin-top: 10px;
            text-align: left;
        }

        .close {
            float: right;
            font-size: 20px;
            font-weight: bold;
            line-height: 18px;
            color: #000000;
            text-shadow: 0 1px 0 #ffffff;
            opacity: 0.2;
            filter: alpha(opacity=20);
        }

        .close:hover {
            color: #000000;
            text-decoration: none;
            opacity: 0.4;
            filter: alpha(opacity=40);
            cursor: pointer;
        }

        .alert {
            padding: 8px 35px 8px 14px;
            margin-bottom: 18px;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
            background-color: #fcf8e3;
            border: 1px solid #fbeed5;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
        }

        .alert, .alert-heading {
            color: #c09853;
        }

        .alert .close {
            position: relative;
            top: -2px;
            right: -21px;
            line-height: 18px;
        }

        .alert-success {
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }

        .alert-success, .alert-success .alert-heading {
            color: #468847;
        }

        .alert-danger, .alert-error {
            background-color: #f2dede;
            border-color: #eed3d7;
        }

        .alert-danger,
        .alert-error,
        .alert-danger .alert-heading,
        .alert-error .alert-heading {
            color: #b94a48;
        }

        .alert-info {
            background-color: #d9edf7;
            border-color: #bce8f1;
        }

        .alert-info, .alert-info .alert-heading {
            color: #3a87ad;
        }

        .alert-block {
            padding-top: 14px;
            padding-bottom: 14px;
        }

        .alert-block > p, .alert-block > ul {
            margin-bottom: 0;
        }

        .alert-block p + p {
            margin-top: 5px;
        }

        /* wait icon ajax */
        .waiticon_block {
            text-align: center;
            cursor: wait;
            z-index: 9999;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
        }

        .waiticon_block:before {
            content: '';
            display: inline-block;
            height: 100%;
            vertical-align: middle;
            margin-right: -0.25em;
        }

        .waiticon_centered {
            display: inline-block;
            vertical-align: middle;
            z-index: 10000;
        }

        .waiticon_transparent {
            cursor: wait;
            z-index: 5000;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-color: white;
            opacity: 0.3;
            filter: alpha(opacity=30);
        }

    </style>
    <?php echo $this->js(array(__QDFJQUERY_BASE__, "head.js", "qcubed.js", "control.js")); ?>
</head>

<body>

<div class="container">
    <div class="info">
        <h1><?php echo __APPSNAME__ ?></h1>
    </div>
</div>
<div class="form">
    <?php echo img("logo.png", array("id" => "logo")) ?>
    <?php $this->RenderBegin() ?>
    <?php $this->DefaultWaitIcon->Render() ?>
    <?php $this->txtUsername->Render(); ?>
    <?php $this->txtPassword->Render(); ?>
    <?php $this->btnLogin->Render('CssClass=buttonlogin'); ?>
    <div id="flash"><?php echo $this->flash(); ?></div>
    <?php $this->RenderEnd() ?>
</div>
<div class="container">
    <div class="footer">
        <span><?php QApplication::Footer() ?></span>
    </div>
</div>

<?php echo $this->js(array("close.js")) ?>
</body>
</html>