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
Es wird mit 3 Verzeichnissen und zwei Batchfiles gearbeitet.

**Verzeichnisse**:
1. Das Vorlageverzeichnis auf dem COSA-Rechner. 
   Hier sind die Sourcedateien aus [src](https://github.com/Koseng/LaIVE-System/tree/master/src) abzulegen.
2. Das eingestellte Ausgabeverzeichnis in COSA WIN unter `Extra->Drucker-Steuerungen/spezieller Datenauschtauch->Autom. HTML-Ergebnisausgabe für Ergebnis-Live-Ticker-> Ausgabe aktivieren`. 
3. Das Zielverzeichnis auf dem Webserver.

Hier im Beispiel:
- Vorlageverzeichnis: c:\vorlageVerzeichnis
- Ausgabeverzeichnis: c:\COSAWIN\transfer
- Serververzeichnis:  /websites/html

**Skripte**:
1. LoeschenUndNeuEinrichtenLiveDaten.bat
2. UebertrageLiveDaten.bat

**Verwendung von COSA im Netzwerk mit mehreren Rechnern**
Es kann ein gemeinsames zentrales Ausgabeverzeichnis im Netzwerk verwendet werden. Dieses muss auf allen Rechnern in COSA eingestellt werden. Es genügt dann auch ein zentraler Rechner auf dem WinSCP installiert ist und auf dem die Skripte ausgeführt werden.

### Einrichten eines neuen Wettkampfes
Oder neu Aufsetzen eines laufenden Wettkampfes bei Problemen. Achtung: Es sind dann allerdings alle Ergebnislisten gelöscht und müssen in COSA WIN jeweils über "Leistungen->Erfassen Leistungen" und Speichern wieder erzeugt werden.

Grundsätzlich sollte man sich zunächst manuell über die Bedienoberfläche von WinSCP verbinden und damit überprüfen ob die Zugangsdaten korrekt sind und die Verbindung einwandfrei funktioniert.

#### Skript ausführen
Das Einrichten erfolgt über das folgende Skript `LoeschenUndNeuEinrichtenLiveDaten.bat`. Angepasst werden müssen die Verzeichnisse und das 'open sftp...'-Kommando. 

Das 'open sftp...'-Kommando erzeugt man sich in WinSCP und kopiert es in das Batch-File. 
1. Regulär auf den Webserver mit WinSCP-Bedienoberfläche per SFTP-Sitzung einloggen.
2. `Sitzung -> Generiere Sitzungs-URL/code -> Script`

[**LoeschenUndNeuEinrichtenLiveDaten.bat**](https://github.com/Koseng/LaIVE-System/blob/master/scripts/LoeschenUndNeuEinrichtenLiveDaten.bat)
```Batchfile
@echo off 
set winscp="C:\Program Files (x86)\WinSCP\winscp.com"
set transferVerzeichnis="C:\COSAWIN\transfer"
set vorlageVerzeichnis="C:\vorlageVerzeichnis"
set serverVerzeichnis=/websites/html

echo ============================================================
echo Dateien in Transferverzeichnis loeschen und neu vorbereiten
echo ============================================================
del /Q %transferVerzeichnis%\*.*
copy %vorlageVerzeichnis%\*.* %transferVerzeichnis%\

echo.
echo ===================================
echo Dateien zu Server synchronisieren
echo ===================================
%winscp% /ini=nul /command ^
    "open sftp://myUser:myPassword@myServer.de:22/ -hostkey=""ssh-ed25519 256 xxxxxxxxxxxxxxxxMyKexxxxxxxxxxxxxxxxxxxxxxx"" -rawsettings FSProtocol=2" ^
    "synchronize remote -delete %transferVerzeichnis% %serverVerzeichnis%" ^
    "exit"

PAUSE
```

#### COSA WIN Basisdaten kopieren
In COSA WIN die Einstellungen unter `Extra->Drucker-Steuerungen/spezieller Datenauschtauch->Autom. HTML-Ergebnisausgabe für Ergebnis-Live-Ticker` kontrollieren und **Basisdaten kopieren** anklicken.

![Bild COSAWIN](https://github.com/Koseng/LaIVE-System/blob/master/pictures/cosawintransfer.JPG)

### Datenübertragung während des Wettkampfes
Hierfür dient das Skript `UebertrageLiveDaten.bat`. Angepasst werden müssen die Verzeichnisse und das 'open sftp...'-Kommando. In der Vorlage ist ein timeout von 60 Sekunden eingestellt. Das heißt so lange das Skript läuft wird alle 60 Sekunden das Ausgabeverzeichnis mit dem Webserver synchronisiert.

[**UebertrageLiveDaten.bat**](https://github.com/Koseng/LaIVE-System/blob/master/scripts/UebertrageLiveDaten.bat)
```Batchfile
@echo off
set winscp="C:\Program Files (x86)\WinSCP\winscp.com"
set transferVerzeichnis=C:\COSAWIN\transfer
set serverVerzeichnis=/websites/html

:loop
%winscp% /ini=nul /command ^
    "open sftp://myUser:myPassword@myServer.de:22/ -hostkey=""ssh-ed25519 256 xxxxxxxxxxxxxxxxMyKexxxxxxxxxxxxxxxxxxxxxxx"" -rawsettings FSProtocol=2" ^
    "synchronize remote %transferVerzeichnis% %serverVerzeichnis%" ^
    "exit"
timeout /t 60
goto loop
```
