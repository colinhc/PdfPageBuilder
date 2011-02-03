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
    $matched = preg_match($pattern, array_pop($file_array), $matches);
    if ($matched) {
?>
<item> 
<title><?= $matches[1] ?></title> 
<link><?= $domain."/pdf/".$matches[1] ?></link> 
<description><?= $matches[1] ?></description> 
<pubDate><?= date("Y-m-d G:i:s", filemtime(join("/", array($dirname, $matches[1])))) ?></pubDate> 
<image><?= $domain."/pdf/".$matches[1].".jpg" ?></image>
</item><?php } } ?> 
</channel> 
</rss>
