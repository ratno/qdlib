<?php

// Special Print Functions / Shortcuts
// NOTE: These are simply meant to be shortcuts to actual QCubed functional
// calls to make your templates a little easier to read.  By no means do you have to
// use them.  Your templates can just as easily make the fully-named method/function calls.
/**
 * Standard Print function.  To aid with possible cross-scripting vulnerabilities,
 * this will automatically perform QApplication::HtmlEntities() unless otherwise specified.
 *
 * @param string $strString string value to print
 * @param boolean $blnHtmlEntities perform HTML escaping on the string first
 */
function _p($strString, $blnHtmlEntities = true)
{
    // Standard Print
    if ($blnHtmlEntities && (gettype($strString) != 'object')) {
        print(QApplication::HtmlEntities($strString));
    } else {
        print($strString);
    }
}

/**
 * Standard Print as Block function.  To aid with possible cross-scripting vulnerabilities,
 * this will automatically perform QApplication::HtmlEntities() unless otherwise specified.
 *
 * Difference between _b() and _p() is that _b() will convert any linebreaks to <br/> tags.
 * This allows _b() to print any "block" of text that will have linebreaks in standard HTML.
 *
 * @param string $strString
 * @param boolean $blnHtmlEntities
 */
function _b($strString, $blnHtmlEntities = true)
{
    // Text Block Print
    if ($blnHtmlEntities && (gettype($strString) != 'object')) {
        print(nl2br(QApplication::HtmlEntities($strString)));
    } else {
        print(nl2br($strString));
    }
}

/**
 * Standard Print-Translated function.  Note: Because translation typically
 * occurs on coded text strings, NO HTML ESCAPING will be performed on the string.
 *
 * Uses QApplication::Translate() to perform the translation (if applicable)
 *
 * @param string $strString string value to print via translation
 */
function _t($strString)
{
    // Print, via Translation (if applicable)
    print(QApplication::Translate($strString));
}

function _i($intNumber)
{
    // Not Yet Implemented
    // Print Integer with Localized Formatting
}

function _f($intNumber)
{
    // Not Yet Implemented
    // Print Float with Localized Formatting
}

function _c($strString)
{
    return number_format($strString, 2, ",", ".");
}

function _pr($var, $die = false, $comment = "[info]")
{
    echo '<pre>';
    print_r($var);
    if ($die) {
        die($comment . " [die at " . date('D-M-Y@h:i:s') . "]\n");
    }
    echo '</pre>';
}

function _env($key)
{
    $val = null;
    if (isset($_SERVER[$key])) {
        $val = $_SERVER[$key];
    } elseif (isset($_ENV[$key])) {
        $val = $_ENV[$key];
    } elseif (getenv($key) !== false) {
        $val = getenv($key);
    }

    return $val;
}

function ___($str)
{
    return str_replace(" ", "_", $str);
}

//////////////////////////////////////

function qd_url($controller, $action = "list", $params = null, $base = __WEB_URL__)
{
    $urls = array();
    if ($controller != '#') {
        if (__MOD_REWRITE__) {
            $urls[] = $base;
        } else {
            $urls[] = $base . "/index.php";
        }
    }

    if ($controller) {
        if(preg_match("/\-/",$controller)) {
            // cek jika mengandung - ganti ke _
            $controller = str_replace("-","_",$controller);
        }
        if(preg_match("/\_/",$controller)) {
            // cek jika mengandung _ maka ubah dulu ke camelcase
            $controller = \QCubed\QString::camelCaseFromUnderscore($controller);
        }

        // ubah ke underscore
        $controller = \QCubed\QString::underscoreFromCamelCase($controller);

        // kembalikan _ ke -
        $urls[] = str_replace("_", "-", $controller);
    }

    if ($action) {
        $urls[] = $action;
    }
//  _pr($params,1);
    if (is_null($params)) {
        // nothing
    } else {
        if (is_array($params) && count($params) > 0) {
            if (array_key_exists("id", $params)) {
                $urls = $params['id'];
                unset($params['id']);
            }

            foreach ($params as $key => $val) {
                if (is_string($key)) {
                    $urls[] = "$key:" . rawurlencode($val);
                } else {
                    if (preg_match("/^(<?)/", $val)) {
                        $urls[] = $val;
                    } else {
                        $urls[] = rawurlencode($val);
                    }
                }
            }
        } else {
            if (preg_match("/^(<?)/", $params)) {
                $urls[] = $params;
            } else {
                $urls[] = rawurlencode($params);
            }
        }
    }

    return implode("/", $urls);
}

function href($link, $linkname = "", $params = array())
{
    if (array_key_exists("allowempty", $params)) {
        $linkname = $linkname;
    } else {
        $linkname = ($linkname) ? $linkname : $link;
    }
    $outparams = array();

    if (array_key_exists("class", $params) && $params['class']) {
        $outparams['class'] = "class='{$params['class']}'";
    }

    if (array_key_exists("id", $params) && $params['id']) {
        $outparams['id'] = "id='{$params['id']}'";
    }

    if (array_key_exists("target", $params) && $params['target']) {
        $outparams['target'] = "target='{$params['target']}'";
    }

    $strParams = implode(" ", $outparams);
    if (is_empty("$link")) {
        return null;
    } else {
        return "<a href='$link' $strParams>$linkname</a>";
    }
}

function href_popup($link, $linkname = "", $params = array())
{
    $linkname = ($linkname) ? $linkname : $link;
    $outparams = array();

    if (array_key_exists("class", $params) && $params['class']) {
        $outparams['class'] = "class='{$params['class']}'";
    }

    if (array_key_exists("id", $params) && $params['id']) {
        $outparams['id'] = "id='{$params['id']}'";
    }

    if (array_key_exists("target", $params) && $params['target']) {
        $outparams['target'] = "target='{$params['target']}'";
    }

    $strParams = implode(" ", $outparams);
    if (is_empty("$link")) {
        return null;
    } else {
        return "<a href='#' onclick='window.open(\"" . $link . "\")' $strParams>$linkname</a>";
    }
}

function img($image_file, $params = array())
{
    $base = __IMAGES_URL__;
    $outparams = array();

    if (array_key_exists("title", $params) && $params['title']) {
        $outparams['title'] = "title='{$params['title']}' alt='{$params['title']}'";
    }

    if (array_key_exists("class", $params) && $params['class']) {
        $outparams['class'] = "class='{$params['class']}'";
    }

    if (array_key_exists("base", $params) && $params['base']) {
        $base = $params['base'];
    }

    if (array_key_exists("id", $params) && $params['id']) {
        $outparams['id'] = "id='{$params['id']}'";
    }

    if (array_key_exists("width", $params) && $params['width']) {
        $outparams['width'] = "width='{$params['width']}'";
    }

    if (array_key_exists("height", $params) && $params['height']) {
        $outparams['height'] = "height='{$params['height']}'";
    }

    $strParams = implode(" ", $outparams);

    if (is_empty("$base/$image_file")) {
        return null;
    } else {
        return "<img src='$base/$image_file' $strParams />";
    }
}

function explode_trim($str, $delimiter = ',')
{
    if (is_string($delimiter)) {
        $str = trim(preg_replace('|\\s*(?:' . preg_quote($delimiter) . ')\\s*|', $delimiter, $str));

        return explode($delimiter, $str);
    }

    return $str;
}

function is_empty($data)
{
    $blnToReturn = false;
    if (empty($data)) {
        $blnToReturn = $blnToReturn || true;
    }

    if (is_null($data)) {
        $blnToReturn = $blnToReturn || true;
    }

    if (trim($data) == "") {
        $blnToReturn = $blnToReturn || true;
    }

    return $blnToReturn;
}

function angka($number, $decimals = 0)
{
    return number_format($number, $decimals, ",", ".");
}

function time_ago($tm, $rcs = 0)
{
    $cur_tm = time();
    $dif = $cur_tm - $tm;
    $pds = array('detik', 'menit', 'jam', 'hari', 'minggu', 'bulan', 'tahun', 'dekade');
    $lngh = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);
    for ($v = sizeof($lngh) - 1; ($v >= 0) && (($no = $dif / $lngh[$v]) <= 1); $v--) {
        ;
    }
    if ($v < 0) {
        $v = 0;
    }
    $_tm = $cur_tm - ($dif % $lngh[$v]);

    $no = floor($no);
    if ($no <> 1) {
        $pds[$v] .= '';
    }
    $x = sprintf("%d %s ", $no, $pds[$v]);
    if (($rcs == 1) && ($v >= 1) && (($cur_tm - $_tm) > 0)) {
        $x .= time_ago($_tm);
    }

    return $x;
}

function linkify($str)
{
    return preg_replace("/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/i",
        '<a href="$1" target="blanks">$1</a>', $str);
}

function url_param($param, $url = null)
{
    $param = http_build_query($param);
    if (is_empty($url)) {
        return $param;
    } else {
        return "$url?$param";
    }
}

function strip_url($url, $folder = "/web", $blnLower = false)
{
    if (__MOD_REWRITE__) {
        $ret = str_replace(array("/index.php", __HOSTNAME__, __VIRTUAL_DIRECTORY__, __SUBDIRECTORY__ . $folder), "",
            $url);
    } else {
        $ret = str_replace(array(__HOSTNAME__, __VIRTUAL_DIRECTORY__, __SUBDIRECTORY__ . $folder), "", $url);
    }
    $ret = ($ret == "/") ? "" : $ret;
    if ($blnLower) {
        return strtolower($ret);
    } else {
        return $ret;
    }
}

function complete_url($url, $blnLower = false)
{
    if (preg_match("/(sample)/", $url)) {
        $web_url = str_replace("/web", "/sample", __WEB_URL__);
        $url = str_replace("/web", "/sample", $url);
        $folder = "sample";
    } else {
        $web_url = __WEB_URL__;
        if (WEB_AS_DOCROOT) {
            $folder = "";
        } else {
            $folder = "/web";
        }
    }
    $host = str_replace("/", "\/", $web_url);
    if (preg_match("/($host)/", $url) || preg_match("/($host)/", __HOSTNAME__ . $url)) {
        $ret = $web_url . "/" . strip_url($url, $folder);
    } else {
        if (preg_match('/(http|https)/', $url)) {
            $ret = $url;
        } else {
            $ret = $web_url . "/" . strip_url($url, $folder);
        }
    }
    $ret = $web_url . str_replace("//", "/", substr($ret, strlen($web_url)));
    if ($blnLower) {
        return strtolower($ret);
    } else {
        return $ret;
    }
}

function json($arrData, $data = null)
{
    if (is_array($arrData)) {
        if (is_null($data)) {
            $out = $arrData;
        } else {
            $out = array($arrData => $data);
        }
    } else {
        if (is_null($data)) {
            $out = array("data" => $arrData);
        } else {
            $out = array($arrData => $data);
        }
    }

    header("content-type: application/json");
    die(json_encode($out));
}

function array_depth(array $arrData)
{
    // mari menghitung kedalaman array, untuk mendapatkan rows terdalam
    $max_indentation = 1;
    // PHP_EOL in case we're running on Windows --> ga valid juga di windows
    $lines = explode("\n", print_r($arrData, true));
    foreach ($lines as $line) {
        $indentation = (strlen($line) - strlen(ltrim($line))) / 4;
        $max_indentation = max($max_indentation, $indentation);
    }

    return ceil(($max_indentation - 1) / 2) + 1;
}

function xls($html, $filename = "laporan")
{
    header("Content-Type: application/vnd.ms-excel");
    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
    echo $html;
    die();
}

function pdf($html, $filename = "laporan", $kertas = "a4", $orientasi = "portrait")
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    include_once(__BASEPATH__ . "/vendor/autoload.php");
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->set_paper($kertas, $orientasi);
    $dompdf->render();
    $dompdf->stream("$filename.pdf", array("Attachment" => false));
    exit(0);
}

function performance($tipe, $name, $subname = null, $tambahan = null)
{
    if (__PERFORMANCE_MEASURE__) {
        $time = number_format(QTimer::GetTime(), 7);
        $tipe_out = ($tipe == 1) ? "start" : "end";
        $text_out = (($subname) ? "$name::$subname" : "$name") . (($tambahan) ? "::" . $tambahan : "");
        QFirebug::info("$time => [$tipe_out] $text_out");
    }
}

function trace($debug_backtrace, $return = false)
{
    $strOut = "<table class='table table-bordered table-striped' cellpadding='2' cellspacing='0' width='100%'>";
    $strOut .= "<tr>";
    $strOut .= "<th>#</th>";
    $strOut .= "<th>FUNCTION</th>";
    $strOut .= "<th width='80'>LINE</th>";
    $strOut .= "<th>FILE</th>";
    $strOut .= "</tr>";
    $nomor = 1;
    foreach ($debug_backtrace as $itemtrace) {
        $trace_func = $itemtrace['function'];
        $trace_line = (array_key_exists("line", $itemtrace)) ? $itemtrace['line'] : "";
        $trace_file = (array_key_exists("file", $itemtrace)) ? $itemtrace['file'] : "";
        $strOut .= "<tr>";
        $strOut .= "<td class='trace_num'>" . $nomor++ . "</td>";
        $strOut .= "<td class='trace_func'>{$trace_func}</td>";
        $strOut .= "<td class='trace_line'>{$trace_line}</td>";
        $strOut .= "<td class='trace_file'>{$trace_file}</td>";
        $strOut .= "</tr>";
    }
    $strOut .= "</table>";
    if ($return) {
        return $strOut;
    } else {
        echo "<pre>";
        echo $strOut;
        echo "</pre>";
    }
}

function all_error_out()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(-1);
}

function remove_illegal_string($string)
{
    return preg_replace('/[^(\x20-\x7F)]*/', '', $string);
}

function exec_debug(Exception $objExc)
{
    $strOut = "Message: " . $objExc->getMessage() . "\n";
    $strOut .= "File: " . $objExc->getFile() . " (" . $objExc->getLine() . ")\n";
    if ($objExc->getCode()) {
        $strOut .= "\n[------------Start Code------------]\n";
        $strOut .= $objExc->getCode();
        $strOut .= "\n[------------End Of Code------------]\n";
    }
    $strOut .= "\n[------------Start Trace------------]\n";
    $strOut .= $objExc->getTraceAsString();
    $strOut .= "\n[------------End of Trace------------]\n";
    QFirebug::log($strOut);
    header("content-type: text/plain");
    die($strOut);
}

function hashid()
{
    return sha1(\Ramsey\Uuid\Uuid::uuid4() . uniqid('', true));
}

function rec_clause($objInput, &$objOutput)
{
    if (is_array($objInput)) {
        foreach ($objInput as $objItem) {
            if (is_array($objItem)) {
                rec_clause($objItem, $objOutput);
            } else {
                $objItem->UpdateQueryBuilder($objOutput);
            }
        }
    } else {
        $objInput->UpdateQueryBuilder($objOutput);
    }
}

function isSecure()
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
}

function comma_to_point_parse($data, $reverse = false)
{
    $decimal_separator = "";
    $decimal_position = false;
    $panjang_data = strlen($data);

    if ($panjang_data - strrpos($data, ",") <= 3) {
        $decimal_separator = ",";
        $decimal_position = strrpos($data, $decimal_separator);
    }

    if ($panjang_data - strrpos($data, ".") <= 3) {
        $decimal_separator = ".";
        $decimal_position = strrpos($data, $decimal_separator);
    }

    if ($decimal_position) {
        $fractal = substr($data, $decimal_position + 1);
        $integral = substr($data, 0, $decimal_position);
    } else {
        $fractal = 0;
        $integral = $data;
    }

    $fractal = QType::Cast(str_pad($fractal, 2, "0", STR_PAD_RIGHT), QType::Integer);
    $integral = QType::Cast(str_replace(array(",", "."), "", $integral), QType::Integer);
    if ($fractal > 0) {
        if ($reverse) {
            $value = "$integral,$fractal";
        } else {
            $value = "$integral.$fractal";
        }
    } else {
        $value = $integral;
    }

    return $value;
}

function revert_from_comma($value)
{
    $value = str_replace(array(".", "_"), "", $value);
    $value = str_replace(",", ".", $value);

    return $value;
}

function is_numeric_decimal($value)
{
    if (is_numeric($value)) {
        if (strrpos($value, ".")) {
            return strrpos($value, ".") ? ((strlen($value) - strrpos($value, ".")) - 1) : 0;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * get nilai riil dengan informasi maskingnya
 * akan terjadi bug jika input decimal, masking non decimal
 * @param $strText
 * @param $strMask
 * @return mixed|string
 */
function getRealValueWithMasking($strText, $strMask)
{
    $blnDalamMasking = true;
    $intDecimal = 0;
    // cek decimal pada masking
    $posisi_decimal = strrpos($strMask, ",");
    // jika ada posisi decimal
    if ($posisi_decimal !== false) {
        $intDecimal = strlen(substr($strMask, $posisi_decimal + 1));
        $separator_decimal = substr($strText, 0 - ($intDecimal + 1), 1);
        // cek apakah format masukan dalam masking
        if ($separator_decimal == ",") {
            // kondisi data dalam masking, berarti harus di clear
            $blnDalamMasking = true;
        } else {
            // separator adalah titik (.) namun apakah titik ini sebagai nilai riil atau masking?
            // dengan inputMask bisa dipastikan hasil yang masuk selalu menyertakan multiple titik
            // cek apakah titik tersebut muncul berulangkali
            if (substr_count($strText, ".") > 1) {
                // kondisi dalam masking
                $blnDalamMasking = true;
            } else {
                // kondisi nilai sebenarnya
                $blnDalamMasking = false;
            }
        }
    } else {
        // jika tidak ditemukan desimal dalam mask maka cari tau dari banyaknya titik
        // jika titik terdapat hanya satu dan angka yang masuk adalah floating point, akan terjadi problem
        $intDecimal = 0;
        if (substr_count($strText, ".") == substr_count($strMask, ".") || substr_count($strText, "_") > 0) {
            // kondisi dalam masking
            $blnDalamMasking = true;
        } else {
            // kondisi nilai sebenarnya
            $blnDalamMasking = false;
        }
    }

    if ($blnDalamMasking) {
        $nilai_riil = str_replace(array("_", "."), "", $strText);
        $nilai_riil = str_replace(",", ".", $nilai_riil);
    } else {
        $nilai_riil = $strText;
    }

    if ($intDecimal) {
        $nilai_riil = sprintf("%01.{$intDecimal}f", $nilai_riil);
    }

    return $nilai_riil;
}

function assignArrayByPath(&$arr, $path, $value, $delimiter = "|")
{
    $keys = explode($delimiter, $path);

    while ($key = array_shift($keys)) {
        $arr = &$arr[$key];
    }

    $arr = trim($value);
}

function renderColumnChart($container_id, $title, $subtitle, $x_category, $y_title, $series)
{
    $title = ucwords($title);
    $subtitle = "Berdasarkan " . ucwords(str_replace("_", " ", $subtitle));
    $y_title = ucwords(str_replace("_", " ", $y_title));

    return "
  $(function () {
    $('#$container_id').highcharts({
      chart: {type: 'column'},
      title: {text: '$title'},
      subtitle: {text: '$subtitle'},
      xAxis: {
        categories: " . json_encode($x_category) . ",
        crosshair: true
      },
      yAxis: {
        min: 0,
        title: {
          text: '$y_title'
        }
      },
      tooltip: {
        headerFormat: '<span style=\"font-size:10px\">{point.key}</span><table>',
        pointFormat: '<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td>' +
        '<td style=\"padding:0\"><b>{point.y:f} orang</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
      },
      plotOptions: {
        column: {
          pointPadding: 0.2,
          borderWidth: 0
        }
      },
      series: " . json_encode($series) . "
    });
  });
  ";
}

function renderLineChart($container_id, $title, $subtitle, $x_categories, $y_title, $series)
{
    $out = "
  $(function () {
      $('#$container_id').highcharts({
          chart: {
              type: 'line'
          },
          credits: {
              enabled: false
          },
          title: {
              text: '$title',
              x: -20 //center
          },
          subtitle: {
              text: '$subtitle',
              x: -20
          },
          xAxis: {
              categories: " . json_encode($x_categories) . "
          },
          yAxis: {
              title: {
                  text: '$y_title'
              },
              plotLines: [{
                  value: 0,
                  width: 1,
                  color: '#808080'
              }]
          },
          tooltip: {
              valueSuffix: ''
          },
          legend: {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle',
              borderWidth: 0
          },
          series: " . json_encode($series) . "
      });
  });
  ";

    return $out;
}

function send_gcm($gcmids, $message, $extra_data = array())
{
    $post_fields = array();

    if (is_array($gcmids)) {
        $regIds = $gcmids;
    } else {
        $regIds = array($gcmids);
    }
    $post_fields['registration_ids'] = $regIds;

    if (is_array($extra_data) && count($extra_data) > 0) {
        $extra_data['message'] = $message;
        $post_fields['data'] = $extra_data;
    } else {
        $post_fields['data']['message'] = $message;
    }

    $headers = array(
        'Authorization: key=' . __GCM_API_KEY__,
        'Content-Type: application/json'
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, __GCM_URL__);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));

    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
}

function buat_list($strData, $numbered = true)
{
    $array = explode("\n", $strData);
    $out = "";
    if (!is_empty($strData) && is_array($array) && count($array)) {
        if ($numbered) {
            $out .= "<ol>";
        } else {
            $out .= "<ul>";
        }

        foreach ($array as $item) {
            $out .= "<li>" . $item . "</li>";
        }

        if ($numbered) {
            $out .= "</ol>";
        } else {
            $out .= "</ul>";
        }
    }

    return $out;
}

function html_box($judul, $html)
{
    $out = '
<div class="row-fluid">
  <div class="w-box">
    <div class="w-box-header">
      ' . $judul . '
    </div>
    <div class="w-box-content cnt_a">
      ' . $html . '
    </div>
  </div>
</div>';

    return $out;
}

function doUnassociateAllMethods($obj)
{
    $methods = get_class_methods(get_class($obj));
    foreach ($methods as $method) {
        if (preg_match("/(UnassociateAll)/", $method)) {
            $obj->$method();
        }
    }
}

function doDeleteAllMethods($obj)
{
    $methods = get_class_methods(get_class($obj));
    foreach ($methods as $method) {
        if (preg_match("/(DeleteAll)/", $method)) {
            if ($method == "DeleteAll") {
                continue;
            } else {
                $obj->$method();
            }
        }
    }
}