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
  ## copy img_* to main image if missing
  #############################################################################
  cd $directory
  for image in `/bin/ls img_*.JPG img_*.jpg 2>/dev/null`; do
    nice convert -resize 260x260 $image small_$image
    echo " * small_$image"
    #
    bigimg=${image:4}
    if [ ! -e "$bigimg" ]; then
      echo " .. writing $bigimg"
      nice cp $image $bigimg
    fi
  done

fi
