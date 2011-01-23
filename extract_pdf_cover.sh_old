#!/bin/sh

#------------------------------------------------------------------------------
# @2011
# This scrips extracts and generates the first page of PDFs as an image.
# 
# Binary dependencies:
#  1. pdfripimage
#   1.1 xpdf
#  2. pnmtojpeg
#
# @author colin.nyc@gmail.com (Colin Chow)
#------------------------------------------------------------------------------

_PDFRIPIMAGE_BIN="${HOME}/colinc/apps/pdfripimage_script/pdfripimage"
_PNMTOJPEG="/usr/bin/pnmtojpeg"
PATH=$PATH:"${HOME}/colinc/apps/xpdf-3.02pl5-linux/"

FindFiles() {
 local _pattern=$1
 local _dir=$2
 local _fileArray=`find "${_dir}" -type f -name "${_pattern}" -print`
 echo "${_fileArray[@]}"
}

ExtractPdfCover() {
  local _fileToConvert=$1
  local _formatType=$2
  local _options="EXTRACT=pages PAGE_START=1 PAGE_END=1 FORMAT=${_formatType}"
  ${_PDFRIPIMAGE_BIN} ${_fileToConvert} ${_options} > /dev/null 2>&1
  echo "${_fileToConvert}.${_formatType}s"
}

ConvertPpmToJpg() {
  local _ppmDir=$1
  local _destFile=$2
  local _quality="-quality=5"
  printf "Writing out image: ${_destFile} \n"
  cat "${_ppmDir}"/* | ${_PNMTOJPEG} ${_quality} > "${_destFile}"
  convert "${_destFile}" -resize 240x320 "${_destFile}"
}

CleanUp() {
  local _dirToClean=$1
  `rm -rf "${_dirToClean}"`
}

Publish() {
  local _sourceDir=$1
  local _publishDir=$2
  `cp "${_sourceDir}"/*.* "${_publishDir}/"`
}

Usage() {
  printf "Usage: extract_pdf_cover.sh [input file extension] [input file directory]"
  printf " [output image type] [output dir]\n"
  printf "Example:\n  extract_pdf_cover.sh pdf /tmp/inputdir jpg /tmp/outputdir\n"
}

Run() {
  local _filePattern=$1
  local _dir=$2
  local _outputFormat=$3
  local _publishDir=$4
  local _files=(`FindFiles "${_filePattern}" "${_dir}"`)
  for i in "${_files[@]}"
  do
    if [[ "${i}" =~ ( |\') ]]; then
      continue
    fi
    local _outputDir=`ExtractPdfCover "${i}" "${_outputFormat}"`
    if [ "jpg" == "${_outputFormat}" ]; then
      ConvertPpmToJpg "${_outputDir}" "${i}.jpg"
    else
      `mv "${_outputDir}"/*."${_outputFormat}" "${i}.png"`
    fi
    CleanUp "${_outputDir}" || exit 1
  done
  Publish "${_dir}" "${_publishDir}" || exit 1
  return 0
}

if [ "$1" == "--help" ] || [ $# -eq 0 ]; then
  Usage
fi
Run "*.pdf" "$HOME/colinc/uploads" "jpg" "$HOME/www/pdf"
# Run "*.${1}" "${2}" "${3}" "${4}"
exit 0
