#!/usr/bin/python2.6

import getopt
import filecmp
import fnmatch
import logging
import os
import shlex
import shutil
import subprocess
import sys

kHome = os.getenv('HOME')
kPdfRipImage_bin = os.path.join(
    kHome, 'www/colinc/apps/pdfripimage_script/pdfripimage')


def FindFiles(ext, dir):
  """Finds files by the extension in the dir.

  Args:
    ext: str, file extension.
    dir: str, directory to look.

  Return:
    a list of files match the given extension.
  """
  pattern = '*.%s' % ext
  return [os.path.join(dir, f) for f in os.listdir(dir) if fnmatch.fnmatch(f, pattern)]


def ExtractPdfCover(f, format_type='jpeg'):
  """Extracts cover page from the pdf file.

  Args:
    f: str, pdf file.
    format_type: format to extract the cover page to. 'jpeg', 'png'.

  Return:
    the directory contains the image.
  """
  options = 'EXTRACT=pages PAGE_START=1 PAGE_END=1 FORMAT=%s' % format_type
  cmd = '%s "%s" %s' % (kPdfRipImage_bin, f, options)
  subprocess.check_call(shlex.split(cmd))
  return '%s.%ss' % (f, format_type)


def ShrinkJpeg(input_dir, dest_file, size='280x320'):
  """Shrings the all jpeg files in the input_file to size to the dest_file.

  Args:
    input_dir: str, source image directory.
    dest_file: str, output filename.
    size: str, size to shrink to.

  Return:
    the output file location.
  """
  cmd = 'convert "%s"/* -resize %s "%s"' % (input_dir, size, dest_file)
  subprocess.check_call(shlex.split(cmd))
  return dest_file


def Cleanup(dir_to_clean):
  """Directory to remove.

  Args:
    dir_to_clean: str, directory to remove.
  """
  shutil.rmtree(dir_to_clean)


def Publish(srcfile_list, dest_dir):
  """Publishes the files to destination directory.

  Args:
    srcfile_list: list[str], the list of files to copy to the output directory.
    dest_dir: str, the output directory.
  """
  for f in srcfile_list:
    try:
      logging.info('Publishing file %s to %s' % (f, dest_dir))
      shutil.move(f, dest_dir)
    except shutil.Error as e:
      if filecmp.cmp(f, os.path.join(dest_dir, os.path.basename(f))):
        os.remove(f)


def Run(data_path, publish_path, extension='pdf'):
  """Starts the conversion process.

  Args:
    data_path: str, pdf directory to process.
    publish_path: str, output directory to store the pdf and cover image files.
    extension: str, 'pdf'.
  """
  f_list = FindFiles(extension, data_path)
  for i in f_list:
    try:
      out_dir = ExtractPdfCover(i)
      out_file = ShrinkJpeg(out_dir, '%s.jpg' % i)
      Publish([i, out_file], publish_path)
    except subprocess.CalledProcessError:
      logging.warn('Error during creating the image file for  %s' % i)
    finally:
      Cleanup(out_dir)


def Usage():
  """Prints usage."""
  print ('Usage: extract_pdf_cover.py\n  -h --help\n  '
         '--ext [extension (default "pdf")]\n  --src_path [src_directory]\n  '
         '--dest_path [publish_directory]\n')


def main(argv):
  options, remainder = getopt.gnu_getopt(
      argv[1:], 'h', ['help', 'ext', 'src_path=', 'dest_path='])

  ext = None
  src_path = None
  dest_path = None

  if not options:
    Usage()
    sys.exit(0)

  for opt, value in options:
    if opt in ('-h', '--help'):
      Usage()
      sys.exit(0)
    elif opt == '--ext':
      ext = value
    elif opt == '--src_path':
      src_path = value
    elif opt == '--dest_path':
      dest_path = value

  if ext is None:
    Run(src_path, dest_path)
  else:
    Run(src_path, dest_path, ext)


if __name__ == '__main__':
  main(sys.argv)
