<?php
  $cachefile = "phpcache/.c_pdf.html";
  $cachetime = 60 * 30; // 1 hour
  if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) {
    include($cachefile);
    echo "<!-- Cached ".date("jS F Y H:i", filemtime($cachefile))."-->";
    exit;
  }
  ob_start();
?>
<html>
<title>C pdf</title>
<head>
  <?php
    $dirname = "../www/pdf";
    $pdf_dir = opendir($dirname);
    $pattern = "/^([0-9a-zA-Z.,\-\ _]+).jpg/";
    $numPerRow = 6;
  ?>
</head>
<body>
  <?php include_once("analytics.php") ?>
  <a href="c_pdf_rss.php">rss</a>
  <table>
    <tr width="100%">
    <?php $columns = 1; $tr = false;
      $file_array = array();
      while (false != ($file = readdir($pdf_dir))) {
        if (($file != ".") and ($file != "..")) {
          array_push($file_array, $file);
        }
      }
      closedir($pdf_dir);
      $file_count = count($file_array);
      foreach ($file_array as $i) {
        if ($columns > 1 && $columns % $numPerRow == 0) {
          $tr = true;
        }
        $matched = preg_match($pattern, array_pop($file_array), $matches);
        if ($matched) {
          $columns++;
    ?>
    <td><a href="pdf/<?= $matches[1] ?>"
    onClick="javascript:_gaq.push(['_trackPageview', 'pdf/<?= $matches[1] ?>']);"><img src="pdf/<?= $matches[0] ?>"
    title="<?= $matches[1] ?>" width="140" height="180" style="border-style: none"/></a></td><td></td>
    <?php
          if ($tr) {
    ?>
     </tr><tr>
    <?php
            $tr = false;
          }
        }
      }
    ?>
    </tr>
  </table>
</body>
</html>
<?php
  $fp = fopen($cachefile, "w");
  fwrite($fp, ob_get_contents());
  fclose($fp);
  ob_end_flush();
?>
