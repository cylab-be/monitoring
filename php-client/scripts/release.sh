#!/bin/sh

DATE=`date '+%Y%m%d.%H%M%S'`
echo $DATE > version
./vendor/bin/box build
echo "@dev" > version
