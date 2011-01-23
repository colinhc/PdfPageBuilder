#!/usr/bin/python2.6

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
  pattern = '*.%s' % ext
  return [os.path.join(dir, f) for f in os.listdir(dir) if fnmatch.fnmatch(f, pattern)]


def ExtractPdfCover(f, format_type):
  options = 'EXTRACT=pages PAGE_START=1 PAGE_END=1 FORMAT=%s' % format_type
  cmd = '%s "%s" %s' % (kPdfRipImage_bin, f, options)
  subprocess.check_call(shlex.split(cmd))
  return '%s.%ss' % (f, format_type)


def ShrinkJpeg(input_dir, dest_file, size='280x320'):
  cmd = 'convert "%s"/* -resize %s "%s"' % (input_dir, size, dest_file)
  subprocess.check_call(shlex.split(cmd))
  return dest_file


def Cleanup(dir_to_clean):
  shutil.rmtree(dir_to_clean)


def Publish(srcfile_list, dest_dir):
  for f in srcfile_list:
    try:
      logging.info('Publishing file %s to %s' % (f, dest_dir))
      shutil.move(f, dest_dir)
    except shutil.Error as e:
      if filecmp.cmp(f, os.path.join(dest_dir, os.path.basename(f))):
        os.remove(f)


def Run(extension, data_path, publish_path):
  f_list = FindFiles(extension, data_path)
  for i in f_list:
    try:
      out_dir = ExtractPdfCover(i, 'jpeg')
      out_file = ShrinkJpeg(out_dir, '%s.jpg' % i)
      Publish([i, out_file], publish_path)
    except subprocess.CalledProcessError:
      logging.warn('Error during creating the image file for  %s' % i)
    finally:
      Cleanup(out_dir)


def main(argv):
  Run(argv[1], argv[2], argv[3])


if __name__ == '__main__':
  main(sys.argv)
