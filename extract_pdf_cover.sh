#!/bin/sh

PATH=/home5/colinati/colinc/apps/xpdf-3.02pl5-linux:$PATH
$HOME/colinc/apps/extract_pdf_cover.py pdf "$HOME/www/colinc/uploads" "$HOME/www/pdf" > $HOME/tmp/extract.log 2>&1
