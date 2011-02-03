<?php header('Content-Type: text/xml'); ?>
<<??>?xml version="1.0" encoding="UTF-8"?>
<?php
  $dirname = "../www/pdf";
  $pdf_dir = opendir($dirname);
  $pattern = '/^([0-9a-zA-Z.,\-\ _]+).jpg/';

  $domain = "http://".getenv("HTTP_HOST");
?>
<rss version="2.0"> 
<channel> 
<title>C pdf Feed</title> 
<link><?= $domain."/c_pdf_rss.php" ?></link> 
<description>C Pdf</description> 
<?php
  $file_array = array();
  while (false != ($file = readdir($pdf_dir))) {
    if (($file != ".") and ($file != "..")) {
      array_push($file_array, $file);
    }
  }
  closedir($pdf_dir);
  foreach ($file_array as $value) {
    $file = array_pop($file_array);
    $path_parts = pathinfo($file);
    if ($path_parts ["extension"] != "jpg") {
?>
<item> 
<title><?= $file ?></title> 
<link><?= $domain."/pdf/".$file ?></link> 
<description><?= $file ?></description> 
<pubDate><?= date("Y-m-d G:i:s", filemtime(join("/", array($dirname, $file)))) ?></pubDate> 
<image><?= $domain."/pdf/".$file.".jpg" ?></image>
</item><?php } } ?> 
</channel> 
</rss>
