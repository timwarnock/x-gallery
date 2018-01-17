#!/bin/bash

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
    t) # Specify subject (title) of gallery
      subject=${OPTARG}
      ;;
    y) # Specify YYYY-MM-DD of gallery
      yyyymmdd=${OPTARG}
      ;;
    m) # Specify description (message) of gallery
      message=${OPTARG}
      ;;
    h | *)
      usage
      exit 0
      ;;
  esac
done

##
## verify that variables are set
##
if [ -z "$directory" -o -z "$subject" -o -z "$yyyymmdd" -o -z "$message" ]; then
  usage
fi


##
## main
## 
if [ ! -d "$directory" ]; then
  usage
else
  echo "Creating image gallery for $directory ..."
  cd $base/$directory
  /bin/ls img_* >/dev/null 2>&1
  if [ "$?" == "0" ]; then
    echo "Directory appears to have already been processed"
    UPDATE="1"
  fi
  if [ -e "INDEX" ]; then
    echo "INDEX file already exists in $directory"
    UPDATE="1"
  fi

  #############################################################################
  ## create the INDEX
  #############################################################################
  if [ "$UPDATE" == "0" ]; then
    dirname=`basename $directory`
    echo "--- $directory/" > INDEX
    echo "subject: $subject" >> INDEX
    echo "date: $yyyymmdd" >> INDEX
    printf "\n$message\n" >> INDEX

    cp -f ../INDEX_ALL ../INDEX_ALL.bak
    echo "--- $directory/" >> ../INDEX_ALL
    echo "subject: $subject" >> ../INDEX_ALL
    echo "date: $yyyymmdd" >> ../INDEX_ALL
    printf "\n$message\n" >> ../INDEX_ALL
  fi

  #############################################################################
  ## create previews and thumbs
  #############################################################################
  for image in `/bin/ls *.JPG *.jpg | grep -v img_`; do
    if [ ! -e "img_$image" -o ! -e "small_img_$image" ]; then
      nice convert -resize 800 $image img_$image
      echo " * $image"
      nice convert -resize 260x260 img_$image small_img_$image
    else
      echo " ** skipping $image"
    fi
  done

fi
