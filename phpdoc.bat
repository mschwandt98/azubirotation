@echo off

cd vendor/phpdocumentor/phpdocumentor/bin/
phpdoc.bat -d ../../../.. -t ../../../../docs --ignore "node_modules/,vendor/"
