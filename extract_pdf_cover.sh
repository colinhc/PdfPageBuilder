#!/bin/sh

XPDF_PATH=/path/to/xpdf-3.02pl5-linux
PATH=$XPDF_PATH:$PATH
/path/to/extract_pdf_cover.py pdf "/path/to/pdf/dir" "/path/to/publish/dir" > /tmp/extract.log 2>&1
