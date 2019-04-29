#!/bin/bash
#
# redo thumbnails
#
# to do in bulk:
# find . -maxdepth 1 -type d -name "2017*" -exec ./NEW_THUMBS.sh -d {} \;

base=`/bin/pwd`
UPDATE="0"
export PATH=/bin:/usr/X11R6/bin:$PATH

##
## gather variables from getopts
##
usage() { echo "$0 usage:" && grep " .)\ #" $0; exit 0; }
[ $# -eq 0 ] && usage
while getopts ":hd:t:y:m:" arg; do
  case $arg in
    d) # Specify directory
      directory=${OPTARG}
      ;;
    h | *)
      usage
      exit 0
      ;;
  esac
done


##
## main
## 
if [ ! -d "$directory" ]; then
  usage
else

  #############################################################################
  ## recreate thumbs
  #############################################################################
  cd $directory
  for image in `/bin/ls *.JPG *.jpg | grep -v img_`; do
    if [ ! -e "img_$image" -o ! -e "small_img_$image" ]; then
      nice convert -resize 800 $image img_$image
      echo " * $image"
      nice convert -resize 260x260 img_$image small_img_$image
    else
      echo " ** skipping $image"
    fi
  done

  ## clear cache
  rm -f ../cache/INDEX.json >/dev/null 2>&1
  rm -f ../cache/${directory#/}".json" >/dev/null 2>&1

fi
