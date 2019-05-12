# LaIVE-System mit WinSCP
Leichtathletik Live Ergebnisse für Cosa

## Überblick
Das LaIVE-System dient zum Aufsetzen eines PHP basierten Webservers zur Darstellung von Live Ergebnissen und Listen bei Leichtathletik Wettkämpfen unter Verwendung von [COSA WIN](http://www.cosa-software.de). Es wurde von Kilian Wenzel entwickelt. Das ursprüngliche System wird auf dieser [Website](http://laive.de/LaIVE-System) zur Verfügung gestellt und steht unter der GPL V3.

Während des Wettkampfes müssen die sich ändernden Datendateien (.C01) von COSA WIN kontinuierlich in das Verzeichnis der PHP-Dateien auf dem Webserver übertragen werden. Die Datendateien werden auf dem Webserver von den PHP-Skripten ausgewertet und automatisch entsprechende Webseiten erzeugt. Außnahme sind die Ergebnisseiten. Dort erzeugt COSA WIN direkt .htm-Seiten die übertragen werden müssen. Nach der Verarbeitung werden die Datendateien auf dem Server automatisch umbenannt und bekommen den Prefix laive_.

Empfehlung: 
Auf jeden Fall die komplette [Anleitung](https://github.com/Koseng/LaIVE-System/blob/master/doc/LaIVE_Kurzinformation_2013-11-22.pdf) lesen. Im Unterschied zum FTP-Watchdog wird hier WinSCP verwendet. Auch der Anfang des [Changelog](https://github.com/Koseng/LaIVE-System/blob/master/doc/changelog.txt) ist interessant.

## Hinweise zur Verwendung und zum Betrieb
Stand heute sollte zur Übertragung von Dateien möglichst nicht mehr das unverschlüsselte [FTP-Protokoll](https://de.wikipedia.org/wiki/File_Transfer_Protocol) verwendet werden. Eine geeignete Alternative ist das [SFTP-Protokoll](https://de.wikipedia.org/wiki/File_Transfer_Protocol). Bei Verwendung eines eigenen Webservers kann auf die integrierte FTP-Client Funktionalität von COSA WIN verzichtet werden. Zur Synchronisierung der Dateien zum Webserver gibt es gute Alternativen, die relativ einfach eingerichtet werden können.

Empfohlen wird die Verwendung von [WinSCP](https://winscp.net/). WinSCP ist ein mächtiger FTP/SCP/SFTP-Client mit Bedienoberfläche und umfangreichen Automatisierungsschnittstellen. 

![Bild WinSCP](https://github.com/Koseng/LaIVE-System/blob/master/pictures/explorer.png)

## Anleitung zur Verwendung von LaIVE mit COSA WIN und WinSCP

### Vorbereitung
1. [WinSCP](https://winscp.net/) herunterladen und installieren.
2. .NET Assembly Paket von WinSCP herunterladen. Auf der Seite .NET Assembly / Com Library suchen [Download](https://winscp.net/eng/downloads.php)
3. WinSCP.exe + WinSCPnet.dll aus dem .NET Assembly Paket und Skriptdateien aus [scripts](https://github.com/Koseng/LaIVE-System/tree/master/scripts) in ein gemeinsames Verzeichnis kopieren.

![Bild Verzeichnis](https://github.com/Koseng/LaIVE-System/blob/master/pictures/scriptDirectory.JPG)

4. Ein Vorlageverzeichnis auf dem COSA-Rechner anlegen. Hier sind die Sourcedateien aus [src](https://github.com/Koseng/LaIVE-System/tree/master/src) abzulegen. Beispielsweise c:\vorlageVerzeichnis
5. Das Transferverzeichnis anlegen und in COSA WIN einstellen unter `Extra->Drucker-Steuerungen/spezieller Datenauschtauch->Autom. HTML-Ergebnisausgabe für Ergebnis-Live-Ticker-> Ausgabe aktivieren`. Beispielsweise c:\COSAWIN\transfer.
6. Pfade und Zugangsdaten unter 'param' und '$SessionOptions' in deleteAllFilesOnserver.ps1 und uploadService.ps1 anpassen. **Für die Zugangsdaten regulär auf den Webserver mit WinSCP-Bedienoberfläche per SFTP-Sitzung einloggen und mit 'Sitzung -> Erzeuge Sitzungs-URL/code -> .NET assembly code' abrufen**.
![Bild Zugangsdaten](https://github.com/Koseng/LaIVE-System/blob/master/pictures/winscpPowershell.JPG)


Hier im Beispiel:
- Vorlageverzeichnis: c:\vorlageVerzeichnis
- Transferverzeichnis: c:\COSAWIN\transfer
- Serververzeichnis:  /websites/html

### Zum Wettkampfbeginn
1. Alter Inhalt im Transferverzeichnis und auf dem Webserver löschen. Zum Löschen auf dem Webserver kann das Skript deleteAllFilesOnServer.bat verwendet werden. Man kann auch ohne das Skript arbeiten und zum Beispiel die Daten selbst über den WinSCP-Client löschen.
2. uploadLiveData.bat starten. Das Skript prüft alle 60 Sekunden ob es neue Daten zum Übertragen gibt. Das Fenster darf nicht geschlossen werden und muss während des ganzen Wettkampfs aktiv sein.

![Bild Upload](https://github.com/Koseng/LaIVE-System/blob/master/pictures/uploadScript.JPG)

3. Dateien aus dem Vorlageverzeichnis in das Transferverzeichnis kopieren.
4. **COSA WIN Basisdaten kopieren**. In COSA WIN die Einstellungen unter `Extra->Drucker-Steuerungen/spezieller Datenauschtauch->Autom. HTML-Ergebnisausgabe für Ergebnis-Live-Ticker` kontrollieren und **Basisdaten kopieren** anklicken.

![Bild COSAWIN](https://github.com/Koseng/LaIVE-System/blob/master/pictures/cosawintransfer.JPG)


**Verwendung von COSA im Netzwerk mit mehreren Rechnern**
Es kann ein gemeinsames zentrales Ausgabeverzeichnis im Netzwerk verwendet werden. Dieses muss auf allen Rechnern in COSA eingestellt werden. Es genügt dann auch ein zentraler Rechner auf dem WinSCP installiert ist und auf dem die Skripte ausgeführt werden.