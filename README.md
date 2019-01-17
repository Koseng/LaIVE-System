# LaIVE-System
Leichtathletik Live Ergebnisse für Cosa

## Überblick
Das LaIVE-System dient zum Aufsetzen eines PHP basierten Webservers zur Darstellung von Live Ergebnissen und Listen bei Leichtathletik Wettkämpfen unter Verwendung von [COSA WIN](http://www.cosa-software.de). Es wurde von Kilian Wenzel entwickelt und das ursprüngliche System wird auf dieser [Website](http://laive.de/LaIVE-System) zur Verfügung gestellt.

Während des Wettkampfes müssen die sich ändernden Datendateien von COSA WIN kontinuierlich in dasselbe Verzeichnis der PHP-Dateien auf dem Webserver übertragen werden. Die Datendateien werden auf dem Webserver ausgewertet und automatisch entsprechende Webseiten erzeugt.

## Hinweise zur Verwendung und zum Betrieb
Stand heute sollte zur Übertragung von Dateien möglichst nicht mehr das unverschlüsselte [FTP-Protokoll](https://de.wikipedia.org/wiki/File_Transfer_Protocol) verwendet werden. Eine geeignete Alternative ist das [SFTP-Protokoll](https://de.wikipedia.org/wiki/File_Transfer_Protocol). Bei Verwendung eines eigenen Webservers kann auf die integrierte FTP-Client Funktionalität von COSA WIN verzichtet werden. Zur Synchronisierung der Dateien zum Webserver gibt es gute Alternativen die relativ einfach eingerichtet werden können.

Empfohlen wird die Verwendung von [WinSCP](https://winscp.net/). WinSCP ist ein mächtiger FTP/SCP/SFTP-Client mit einer umfangreichen Bedienoberfläche und umfangreichen Automatisierungsschnittstellen. 

![Bild WinSCP](https://github.com/Koseng/LaIVE-System/blob/master/pictures/explorer.png)

## Anleitung zur Verwendung von LaIVE mit COSA WIN und WinSCP

TODO Batchfile 
Skript1
Skript2
TODO


