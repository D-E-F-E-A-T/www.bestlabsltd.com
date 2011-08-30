#!/bin/bash

# source paths
curr=`pwd`
self=`pwd $0`
root="$self/.."
jqui="$self/jquery-ui"
uiui="$jqui/ui"

echo -en "\nCleaning..."
rm -f `find $root/pub -maxdepth 1 -type f | grep -i jquery-ui | xargs echo`
echo -en "done.\n\n"

echo -n "Updating Repo..."
cd $jqui
git fetch
git reset -q --hard HEAD
cd $curr
echo -en "done.\n\n"

echo -n "Merging source..."

# obtain version
ver=`cat $jqui/version.txt`

# destination paths
tip="$root/pub/jquery-ui-$ver-head.js"
min="$root/pub/jquery-ui-$ver-head.min.js"

# add effects if specified on parameters.
if [ "$1" == "--fx" ]; then
	shift # remove param
	list=`find $uiui -maxdepth 1 | grep -i "\.effects" | grep -viP "core" | xargs echo`
	effects=`cat $uiui/jquery.effects.core.js`" "`cat $list`
else
	effects=''
fi

# obtaining widgets
core=("core" "widget" "mouse" "position")
for i in "${core[@]}"; do
	widgets=${widgets}" "`cat $uiui/jquery.ui.$i.js`
done

# Core elements will be ignored
ignore=`echo ${core[@]} | sed 's/ /\|/g'`

# if no arguments specified get all widgets.
if [[ -z "${@}" ]]; then
	list=`find $uiui -maxdepth 1 -type f | grep -Pvi "$ignore" | grep -i "\.ui\." | xargs echo`
else
	# each argument is a widget to include. 
	for i in "${@}"; do
		ucase=`echo $i| tr '[:lower:]' '[:upper:]'`
		# must not be in the ignore list.
		if [ ! -z `echo $i | grep -Pi $ignore` ]; then
			echo -e "\n $ucase is part of Core, aborting.\n"
			exit 1
		fi
		# must exist
		if [ ! -r "$uiui/jquery.ui.$i.js" ]; then
			echo -e "\n $ucase is not a valid widget. \n"
			exit 1
		fi
		# if datepicker, append regional 
		if [ "$i" == "datepicker" ]; then
			list="${list} $uiui/jquery.ui.$i.js "`find $uiui/i18n | grep -i "es\.js" | xargs echo`
		else
			list="${list} $uiui/jquery.ui.$i.js"
		fi
	done
fi
widgets=${widgets}" "`cat $list`

# merge in order
echo "$widgets $effects" > $tip
echo -en "done.\n\n"

# minify
echo -n "Minifying..."
/usr/bin/java -jar $self/closure-compiler/compiler.jar --compilation_level ADVANCED_OPTIMIZATIONS --js $tip --js_output_file $min
echo -en "done.\n\n"

echo -e "Results:\n"
# show sizes
cd $root/pub
ls -lh jquery-ui* | awk '{print "\t"$5" "$9}' | grep -viP theme
cd $curr
echo -e "\n"