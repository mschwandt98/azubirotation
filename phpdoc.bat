@echo off

rmdir /s /q docs
timeout 2 > NUL

cd vendor/phpdocumentor/phpdocumentor/bin/
phpdoc.bat -d ../../../.. -t ../../../../docs --ignore "build/, node_modules/,vendor/"
