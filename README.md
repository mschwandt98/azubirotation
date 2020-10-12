= Kurzdokumentation für die Weiterentwicklung =

== Vorwort ==
Bei Fragen zu Funktionalitäten und Aufbau ist die zum Projekt dazugehörige
Facharbeit zu lesen. Code-spezifische Fragen sind den Kommentaren des Codes zu
entnehmen. Sofern weitere Fragen offen sind, kann sich beim Autor des Projekts
gemeldet werden.

Zur Weiterentwicklung des Projekts werden npm und composer benötigt.
npm Installation: https://www.npmjs.com/get-npm
composer Installation: https://getcomposer.org/download/


== PHING ==
Mit Phing wird das Projekt in den /dist Ordner gebaut. In der build.xml ist
definiert welche Dateien gebaut werden.

=== Installation ===
Siehe https://www.phing.info/#install


== Projekt bauen ==
npm node_module werden installiert mit:
$ npm run restore

Nachdem die benötigten Module installiert und zuvor PHING installiert wurden,
kann mit
$ npm run build
das Projekt gebaut werden. Beim Bauen werden die CSS-Dateien und JS-Dateien
minimiert gebaut.

=== CSS ===
Die CSS-Dateien werden mit Hilfe von SASS aus SCSS-Dateien gebaut.
Es werden zwei Versionen an CSS-Dateien gebaut: public-styles.css und styles.css
Die public-styles.css wird ausgeliefert, wenn der Benutzer nicht angemeldet ist.
Die styles.css hingegen wird für angemeldete Benutzer ausgeliefert. Der Grund
weshalb zwei verschiedene Dateien ausgeliefert werden ist einfach, dass nicht
angemeldete Benutzer weniger Daten übertragen bekommen.

=== JS ===
Die JS-Dateien werden mit Hilfe von Terser gebaut.
Es werden zwei verschiedene Versionen gebaut: public-script.js und script.js.
Die public-script.js enthält Funktionalitäten, welche jeder Benutzer nutzen
darf. Die script.js enthält alle Funktionalitäten des Frontends und ist wird nur
für angemeldete Benutzer ausgeliefert. Der Grund für diese Unterscheidung ist
einerseits, dass nicht angemeldete Benutzer weniger Daten transferiert bekommen
und andererseits, dass die Anwendung sicherer ist.


== PHPDOC ==
PHPDOC wird verwendet, um eine strukturierte Übersicht über die Codedoku zu
haben.

=== Installation ===
https://docs.phpdoc.org/latest/getting-started/index.html

=== Vorbereitung ===
Falls PHP mit composer installiert wurde, muss die Datei
vendor/phpdocumentor/phpdocumentor/bin/phpdoc.bat bearbeitet werden (Fehler
seitens PHPDOC, Stand: 12.10.2020). Den Code der Datei mit den folgenden Zeilen
ersetzen:

@echo off
if "%PHPBIN%" == "" set PHPBIN=php.exe
if not exist "%PHPBIN%" if "%PHP_PEAR_PHP_BIN%" neq "" goto USE_PEAR_PATH
GOTO RUN
:USE_PEAR_PATH
set PHPBIN=%PHP_PEAR_PHP_BIN%
:RUN
"%PHPBIN%" "phpdoc" %*

=== Ausführung ===
Mit
$ npm run phpdoc
wird phpdoc zur Erstellung einer PHP Dokumentation aus dem Code ausgeführt.

Die erstellte PHP Dokumentation ist unter /docs/index.html zu finden.
