<?php
  $cachefile = "phpcache/.c_pdf.html";
  $cachetime = 60 * 30; // 1 hour
  if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) {
    include($cachefile);
    echo "<!-- Cached ".date('jS F Y H:i', filemtime($cachefile))."-->";
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
    $pattern = '/^([0-9a-zA-Z.,\-\ _]+).jpg/'
  ?>
</head>
<body>
  <?php include_once("analytics.php") ?>
  <table>
    <tr width="100%">
    <?php $columns = 1; $tr = false;
      while (false != ($file = readdir($pdf_dir))) {
        if (($file != ".") and ($file != "..")) {
          if ($columns > 1 && $columns % 5 == 0) {
            $tr = true;
          }
          $matched = preg_match($pattern, $file, $matches);
          if ($matched) {
            echo "<td><a href=\"pdf/".$matches[1]
                 ."\" onClick=\"javascript: _gaq.push(['_trackPageview', 'pdf/".$matches[1]."']);\">"
                 ."<img src=\"pdf/".$matches[0]."\" title=\"".$matches[1]
                 ."\" width=\"140\" height=\"180\" style=\"border-style: none\"/></a></td><td></td>";
            $columns++;
            if ($tr) {
              echo "</tr><tr>";
              $tr = false;
            }
          }
        }
      }
      closedir($pdf_dir);
    ?>
    </tr>
  </table>
</body>
</html>
<?php
  $fp = fopen($cachefile, 'w');
  fwrite($fp, ob_get_contents());
  fclose($fp);
  ob_end_flush();
?>
