<?php
ob_start("ob_gzhandler");
header('Content-Type: text/html; charset=ISO-8859-1'); 
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', './logfile.log');
ini_set('allow_url_fopen', '1');?>
<?php /*
             LaIVE – Athletics live results
    Copyright (C) 2013  Kilian Wenzel / laive@kwenzel.net

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version. 

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details. 

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.

The licence can be found in the file "Licence.txt".

An inofficial german translation of that licence can be found at
http://www.gnu.de/documents/gpl-3.0.de.html.

This program contains (as at 27.06.2013) the following files,
written by myself:

-	index.php
-	uebersicht.php
-	zeitplan.php
-	gesamtteilnehmer.php
-	startlistenerstellen.php
-	stellplatzzeitplan.php

The file „sorttable.js“ (http://www.kryogenix.org/code/browser/sorttable/)
by Stuart Langridge is used in that project under the X11 licence
(http://www.kryogenix.org/code/browser/licence.html).
The file "archive.php" is used under the GLP Licence.
(http://blog.oncode.info/2007/10/17/zipdateien-on-the-fly-erstellen-mit-php/)

--------------------------------------------------------------------------

              LaIVE – Leichtathletik-Live-Ergebnisse
       Copyright (C) 2013  Kilian Wenzel / laive@kwenzel.net

Dieses Programm ist freie Software. Sie können es unter den Bedingungen der
GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß Version 3
der Lizenz oder (nach Ihrer Option) jeder späteren Version. 

Die Veröffentlichung dieses Programms erfolgt in der Hoffnung, daß es Ihnen
von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE, sogar ohne die implizite
Garantie der MARKTREIFE oder der VERWENDBARKEIT FÜR EINEN BESTIMMTEN ZWECK.
Details finden Sie in der GNU General Public License. 

Sie sollten ein Exemplar der GNU General Public License zusammen mit diesem
Programm erhalten haben. Falls nicht, siehe <http://www.gnu.org/licenses/>.

Die Lizenz befindet sich in der Datei "Licence.txt".

Eine inoffizielle deutsche Übersetzung der Lizenz können Sie unter 
http://www.gnu.de/documents/gpl-3.0.de.html abrufen. 

Das Programm beinhaltet derzeit (27.06.2013) folgende eigenständig
programmierte Dateien:

-	index.php
-	uebersicht.php
-	zeitplan.php
-	gesamtteilnehmer.php
-	startlistenerstellen.php
-	stellplatzzeitplan.php

Sowie die von Stuart Langridge erstellte Datei „sorttable.js“
(http://www.kryogenix.org/code/browser/sorttable/), die unter der Lizenz
„X11 licence“ (http://www.kryogenix.org/code/browser/licence.html) verwendet wird.
Sowie die Datei "archive.php" unter der GLP Lizenz.
(http://blog.oncode.info/2007/10/17/zipdateien-on-the-fly-erstellen-mit-php/)

---------------------------------------------------------------------------------
*/ ?>  
<?php
	# Prüfen auf Servereinstellungen
	if(isset($_GET['check']) && $_GET['check'] == 1) {
		phpinfo();
		echo "<hr>";
		echo "Prüfen auf Schreibbarkeit des Verzeichnisses:<br>";
		if(is_writable("./")) {
			echo "schreibbar";
		}
		else {
			echo "nicht schreibbar";
		}
		echo "<hr>";
		echo "Informationen zu den Dateien im Verzeichnis:<br>";
		$AllFilesCheck		= scandir(".");
		echo "<table>";
		echo "<tr><td>Datei</td><td>schreibbar (1=ja / 0=nein)</td><td>ausführbar (1=ja / 0=nein)</td><td>Dateirechte</td><tr>";
		
		foreach($AllFilesCheck as $AllFilesCheckFile) {
			echo "<tr><td>".$AllFilesCheckFile."</td><td>".is_writable($AllFilesCheckFile)."</td><td>". is_executable($AllFilesCheckFile)."</td><td>".substr(sprintf('%o', fileperms($AllFilesCheckFile)), -4)."</td><tr>";
		}
		echo "</table>";
		
		# Links zum Speichern
		echo "<br><hr><br>";
		echo "<a href='index.php?save=1'>Speichere Datendateien aus Verzeichnis als ZIP-Datei</a>";
		echo "<br>";
		echo "<a href='index.php?save=2'>Speichere alle Dateien aus Verzeichnis als ZIP-Datei</a>";
		
		ob_end_flush();
		exit();
	}

	# Sichern der Daten auf dem Webserver in ZIP-Datei
	if(isset($_GET['save']) && $_GET['save'] > 0) {
	 ob_end_clean();
		# Für Namen ZIP

		$arrPath = explode("/", __DIR__);
		$intLastEntry = count($arrPath)-1;
		$strForZipFile = $arrPath[$intLastEntry];
		$strTime = date("Y-m-d_H-i-s",time());

		#echo __DIR__;
		// Archivklasse einbinden:
		require_once ('archive.php');

		// Objekt erzeugen. Das Argument bezeichnet den Dateinamen
		$zipfile = new zip_file('LaIVE-Sicherung_'.$strForZipFile.'_'.$strTime.'.zip');


		// Die Optionen
		$zipfile->set_options(array(
			'basedir' => __DIR__, // Das Basisverzeichnis. Sonst wird der ganze Pfad von / an im Zip gespeichert.
			'followlinks' => 1, // Symlinks sollen berücksichtigt werden
			'inmemory' => 1, // Die Datei nur im Speicher erstellen
			'level' => 6, // Level 1 = schnell, Level 9 = gut
			'recurse' => 1, // In Unterverzeichnisse wechseln
			// Wenn zu grosse dateien verarbeitet werden, kannes zu einem php memory error kommen
			// Man sollte nicht über das halbe memory_limit (php.ini) hinausgehen
			'maxsize' => 12 * 1024 * 1024 // Nur Dateien die <= 12 MB gross sind zippen
			));

		// Alle Dateien im Verzeichnis /home/me/toZip/Stuff hinzufügen
		// Alle ".doc" Dateien und alle Ordner im Verzeichnis /home/me/toZip/Letters hinzufügen
		$zipfile->add_files(array("."));

		// Alle ".tmp" dateien in Stuff ausschliessen
		if($_GET['save'] == 1) {
			$zipfile->exclude_files(array("*.php", "*.js", "*.zip"));
		}
		
		// Alle Dateien in ".svn" und "CVS" Verzeichnissen ausschliessen (Regular Expressions)
		#$zipfile->exclude_regexp_files('.*/CVS|.*/CVS/.*|.*/\.svn|.*/\.svn.*');

		// Archiv erstellen
		$zipfile->create_archive();

		// Archiv zum Download anbieten
		$zipfile->download_file();

		// Oder speichern (vielen Dank an PHPler!!!)
		#$zipfile->save_file('myzip.zip', $path = '.');
	
		
		exit();
	}


$startzeitdauer = microtime(true);

### INFO ###
### LaIVE - Hauptseite (index.php) / LaIVE - Main page (index.php)
### Erstellt von / Created by Kilian Wenzel
### Zuletzt geändert: / Last change: 0.12.3.2014-02-17


# Result-Ticker auf Basis der COSAWIN-HTML-Dateien /Result-Ticker based on COSAWIN HTML Files
$ResultTickerVersion = "0.12.3.2014-03-17";
$ResultTickerErsteller = "Kilian Wenzel";
$ResultTickerCOSAWINVersion = "2.5.0";

# ------------------------------------------------------------------------
# Puffer und Dateien umbenennen um Ausfälle während der Uploadzeit (vor allem bei größeren Dateien oder schlechter Verbindung zu vermeiden)

$ZeitZwischenDenDateiumwandlungen = 20; # in Sekunden

$DateienCOSA[] = "vandat.c01";
$DateienCOSA[] = "Wettbew.c01";
$DateienCOSA[] = "WbTeiln.c01";
$DateienCOSA[] = "Endli.c01";
$DateienCOSA[] = "WkList.c01";
$DateienCOSA[] = "Stamm.c01";
$DateienCOSA[] = "Verein.c01";
$DateienCOSA[] = "DBSTextSKL.ct1"; # DBS-Startklassen

$DateienVorzeichen = "laive_";

foreach($DateienCOSA as $DateienCOSAZeile) {
if(file_exists("./".$DateienCOSAZeile)) {
	if(file_exists("./".$DateienVorzeichen.$DateienCOSAZeile) == FALSE) {
		copy("./".$DateienCOSAZeile, "./".$DateienVorzeichen.$DateienCOSAZeile);
		$Geloescht = unlink("./".$DateienCOSAZeile);
		if($DateienCOSAZeile == "WkList.c01" && $Geloescht == True) {
			if(file_exists("./laive_startlisten.txt")) {
				unlink("./laive_startlisten.txt");
			}
		}
	}
	else {
		if(filemtime("./".$DateienCOSAZeile) + $ZeitZwischenDenDateiumwandlungen < time()) {
			
			copy("./".$DateienCOSAZeile, "./".$DateienVorzeichen.$DateienCOSAZeile);
			$Geloescht = unlink("./".$DateienCOSAZeile);
			if($DateienCOSAZeile == "WkList.c01" && $Geloescht == True) {
				if(file_exists("./laive_startlisten.txt")) {
					unlink("./laive_startlisten.txt");
				}
			}
		}
	}
}
}
unset($DateienCOSA);

#-------------------------------------------------------------------------
# Variablen deklinieren --------------------------------------------------
$IPCMode = "";


# ---- FUNKTIONEN --------------------------------------------------------
# Uhrzeiten umwandeln in Format: hh:mm / Convert Times to hh:mm
			
function uhrzeitformat($cosauhrzeit) {
	$neueuhrzeit = "";
	$laengeuhrzeit = strlen($cosauhrzeit);
		switch ($laengeuhrzeit) {
			case 4:
				$neueuhrzeit = "0".substr($cosauhrzeit, 0, 1).":".substr($cosauhrzeit, 2, 2);
				break;
			case 5:
				$neueuhrzeit = substr($cosauhrzeit, 0, 2).":".substr($cosauhrzeit, 3, 2);
				break;
		}
		return($neueuhrzeit);
}

# IPCClasses -------------------------------------------------------------
				
function IPCClassesArray() {
	if(file_exists("./laive_DBSTextSKL.ct1")) {
		$DBSTextsklContent 					= file_get_contents("./laive_DBSTextSKL.ct1");
		$DBSTextsklLength 					= strlen($DBSTextsklContent);
		$DBSTextsklLengthDataset 			= 38;
		$DBSTextsklSumDatasets 				= $DBSTextsklLength / $DBSTextsklLengthDataset;
		$DBSTextsklCounterDatasets 			= 0;
		$DBSTextsklAbsolutePositionDataset 	= 1;
		
		
		$DBSTextskl[0] = array(	'IPCClassID'		=>	0,
									'IPCClassName'	=>	"",
									'IPCClassNote'	=>	""
								);
		
		while($DBSTextsklCounterDatasets < $DBSTextsklSumDatasets) {
			$DBSTextsklCounterDatasets++;
			
			$DBSTextskl[trim(substr($DBSTextsklContent, $DBSTextsklAbsolutePositionDataset - 1, 3))] = array(	'IPCClassID'		=>	trim(substr($DBSTextsklContent, $DBSTextsklAbsolutePositionDataset - 1, 3)),
									'IPCClassName'	=>	trim(substr($DBSTextsklContent, $DBSTextsklAbsolutePositionDataset + 2, 15)),
									'IPCClassNote'	=>	trim(substr($DBSTextsklContent, $DBSTextsklAbsolutePositionDataset + 17, 20))
								);
								$DBSTextsklAbsolutePositionDataset = $DBSTextsklAbsolutePositionDataset + $DBSTextsklLengthDataset;
		}
		return($DBSTextskl);
	}				
}
# Events -----------------------------------------------------------------
function EventsArray() {
	if(file_exists("./laive_Wettbew.c01")) {
		$WettbewContent 				= file_get_contents("./laive_Wettbew.c01");
		$WettbewLength 					= strlen($WettbewContent);
		$WettbewLengthDataset 			= 389;
		$WettbewSumDatasets				= $WettbewLength / $WettbewLengthDataset;
		$WettbewCounterDatasets			= 0;
		$WettbewAbsolutePositionDataset = 1;
		
		while($WettbewCounterDatasets < $WettbewSumDatasets	) {
			$WettbewCounterDatasets++;
			
			$Events[trim(substr($WettbewContent, $WettbewAbsolutePositionDataset - 1, 3))] = array(
							'EventID'					=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset - 1, 3)),
							'EventNameAndClass'			=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 2, 32)),
							'FinalConfirmationMin'		=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 71, 3)),
							'FinalConfirmationTime'		=>	uhrzeitformat(trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 74, 5))),
							'Round1Time'				=>	uhrzeitformat(trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 79, 5))),
							'Round1Day'					=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 84, 1)),
							'SemiFinalsTime'			=>	uhrzeitformat(trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 85, 5))),
							'SemiFinalsDay'				=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 90, 1)),
							'FinalTime'					=>	uhrzeitformat(trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 91, 5))),
							'FinalDay'					=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 96, 1)),
							'COSAIDClass'				=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 36, 2)),
							'COSAIDEvent'				=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 38, 3)),
							'COSAID'					=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 36, 5)),
							'EventName'					=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 296, 32)),
							'ClassName'					=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 331, 24)),
							'EventType'					=>	trim(substr($WettbewContent, $WettbewAbsolutePositionDataset + 41, 1))					
							);
							
							$WettbewAbsolutePositionDataset = $WettbewAbsolutePositionDataset + $WettbewLengthDataset;
		}

		

		return($Events);
	}
}

############ Funktion Mehrdimensionales Array durchsuchen
function array_multi_search($mSearch, $aArray, $sKey = "")
{
    $aResult = array();
   
    foreach( (array) $aArray as $aValues)
    {
        if($sKey === "" && in_array($mSearch, $aValues)) $aResult[] = $aValues;
        else
        if(isset($aValues[$sKey]) && $aValues[$sKey] == $mSearch) $aResult[] = $aValues;
    }
   
    return $aResult;
}

# ---- FUNKTIONEN ----------------------ENDE------------------------------

##########################################################################
## Basisdaten / Basicdata
##########################################################################

# --- Einstellungen / Settings -----------------------------------------

$Standardsprache = "de";			# Legt die Standardsprache fest / "de" = deutsch DLV, "en" = english IAAF

$competitionsubON = 0;			# Veranstaltungshomepagelink zeigen
$identifizierung_teilnehmer = "t";

# Links von Modulen in Kopf
$GesamtteilnehmerlisteAn = 0;				# 1= An, 0= Aus
$TeilnehmerlisteNachWettbewerbenAn = 1;		# 1= An, 0= Aus
$TeilnehmerlisteNachVereinen = 0;			# 1= An, 0= Aus
$AllStartlistsInOneFile = 1;				# 1= On, 0= Off

# Teilnehmerliste nach Vereinen auf Zeitplanseite zeigen, nur VOR der Veranstaltung
$TeilnehmerlisteNachVereinenAufZeitplanseiteAn = 1; 		# 1= An, 0= Aus

# Zeiten, bis Webseite aktualisiert wird (in Sekunden)
$AktualisierenLaiveZeit = 300;		
$AktualisierenLaiveAdminZeit = 300;	

#Zeitplanverzug in Minuten:
$zeitplanverzug = 0;

# Ab wann wird ein Wettbewerb als aktuell angezeigt und bis wann?
$startzeitaktuellminus = 360; #in Sekunden
$startzeitaktuellplus= 360; #in Sekunden

# Unterlegung aktuelle Wettbewerbe an?
$aktuellerWettbewerbAn = 1;   		# 1= An, 0= Aus

# Bestimmte Wettbewerbe/Wertungen oberhalb des Zeitplans anzeigen?
$WettbewerbeOberhalbZeitplanAn = 0;	# 1= An, 0= Aus

# Startnummern anzeigen? (Derzeit nur bei Gesamtteilnehmerliste)
$StartnummernAn = 1;				# 1= An, 0= Aus

# Standardsortierung bei Teilnehmerliste nach Wettbewerben
$TeilnNachWettbewStandardSort = 	3;		# 1= St.-Nr, 2= Namen, 3= Meldeleistung

# Startlistenerstellung automatisch ausführen
$StartlistenerstellenAutomatischAn = 1;				# 1= An, 0= Aus
$StartlistenerstellenAlleSekunden = 300;	# in Sekunden

# Teilnehmerlistenerstellung automatisch ausführen
$CreateEntyListsOn = 1;
$CreateEntyListsEverySecounds = 300;

# Stellplatzzeitplan anzeigen?
$StellplatzzeitplanAn = 0;			# 1= An, 0= Aus

# DBS Modus / ICP Mode - Settings
$IPCResultListFileExtention = "htm";		# htm or pdf

# Einstellungen für Fenster/Target Links Startlisten, Teilnehmerlisten, Ergebnislisten / Settings for Window/target start lists, result lists, entry lists
# _blank or _self

$LinksTargets[1] = "_self"; # Ergebnisliste / Result list
$LinksTargets[2] = "_self"; # Teilnehmerliste / entry list
$LinksTargets[3] = "_self"; # Zwischenerg. / intermediate results
$LinksTargets[4] = "_self"; # Startliste / start list
$LinksTargets[5] = "_self"; # 
$LinksTargets[6] = "_self"; # Qualif Vorl. / Qualif. by round 1
$LinksTargets[7] = "_self"; # Qualif. Zwischenl. / Qualif. by semi-finals
$LinksTargets[8] = "_self"; # Disz. beendet MK / Event finished combinded event

# Header- und Footer-Bild einfügen / include picture for header und footer
$LinkToHeaderImage = "./header.jpg";
$LinkToFooterImage = "./footer.jpg";
$EnableHeaderImage = 1;		# 1 = an / active; 2 = aus / off
$EnableFooterImage = 1;		# 1 = an / active; 2 = aus / off
$WeblinkHeaderImage = "";
$WeblinkFooterImage = "";

# Athletennummer anzeigen / Show Athletes Licence ID
$AthletesLicenceIDOn = 1; 		# 1= An/active, 0= Aus/off

# Final Confirmation On
$FinalConfirmationOn = 0; 		# 1= An/active, 0= Aus/off

# Use of Flags (instead of Nation-Abbrev.) On
$FlagsOn = 0;					# 1= An/active, 0= Aus/off
$PathToFlags = "http://laive.de/images/flags/16/";
$FileFormatFlags = ".png";


##########################################################################
## Spezialoptionen / Special options
##########################################################################

# ----- Zeitplan für Anzeigetafel ausgeben / Create Timetable file for Score board

$TTBoardON					= 1;													# 1=yes, 0=no

$TTBoardRows 				= 32;													# Rows by line
$TTBoardLines				= 10;													# Lines on 1 page

$TTBoardRowsTime			= 5;													# Length Time
$TTBoardRowsClass			= 6;													# Length Class
$TTBoardRowsEvent			= 8;													# Length Event
$TTBoardRowsRound			= 3;													# Length Round
$TTBoardRowsParticipants 	= 2;													# Length Participants

$TTBoardSeperatorLength[1]	= 2;													# Length of Seperator 1
$TTBoardSeperatorLength[2]	= 2;													# Length of Seperator 2
$TTBoardSeperatorLength[3]	= 2;													# Length of Seperator 3
$TTBoardSeperatorLength[4]	= 2;													# Length of Seperator 4

$TTBoardSeperator[1]		= "  ";													# Content Seperator 1 (between Time and Class)
$TTBoardSeperator[2]		= "  ";													# Content Seperator 2 (between Class and Event)
$TTBoardSeperator[3]		= "  ";													# Content Seperator 3 (between Event and Round)
$TTBoardSeperator[4]		= "  ";													# Content Seperator 4 (between Round and Participants)


$TTBoardOutputParticipants 	= 1;													# Output of no. of Participants;	 	1=yes, 0=no



$TTBoardHeadlineOnEachPage 	= 1;													# Headlines on each page;				1=yes, 0=no

$TTBoardHeadline[1]			= "{TIMETABLE} {DATE} ";								# Template for headline 1
$TTBoardHeadline[2]			= " {PAGE} {PAGENO}{PAGESEPERATOR}{PAGENOTOTAL}";		# Template for headline 2

$TTBoardHeadlineOrientation[1]	= STR_PAD_RIGHT;												# Orientation Headline 1; STR_PAD_LEFT, STR_PAD_BOTH,  STR_PAD_RIGHT 		
$TTBoardHeadlineOrientation[2]	= STR_PAD_LEFT;												# Orientation headline 2; STR_PAD_LEFT, STR_PAD_BOTH,  STR_PAD_RIGHT 

$TTBoardHeadlineFilling[1]	= " ";													# Filling for empty Headline 1
$TTBoardHeadlineFilling[2]	= "-";													# Filling for empty Headline 2


$TTBoardTemplate[1]			= "{TIMETABLE}";										# Template-Array
$TTBoardTemplate[2]			= "{DATE}";
$TTBoardTemplate[3]			= "{PAGE}";
$TTBoardTemplate[4]			= "{PAGENO}";
$TTBoardTemplate[5]			= "{PAGENOTOTAL}";
$TTBoardTemplate[6]			= "{PAGESEPERATOR}";


$TTBoardTemplateContent[1]	= "Zeitplan";											# Content for Template-Array
$TTBoardTemplateContent[2]	= "TT.MM.YYYY";
$TTBoardTemplateContent[3]	= "Seite";
$TTBoardTemplateContent[4]	= "0";
$TTBoardTemplateContent[5]	= "##";
$TTBoardTemplateContent[6]	= "/";

$TTBoardCharsetFile			="cp850";												# Charset for Output file 



# ----- CSV File for Results/Certificate Combined Cup scoring / CSV-Datei für Ergebnisse/Urkunden kombinierte Pokalwertung ausgeben

$CCCSVFileON				= 1;															# 1=yes, 0=no
$CCCSVFile_Seperator		= ";";															# Seperator for CSV File
$CCCSVFile_Extention		= "txt";														# Extention of CSV File
$CCCSVFile_TxtIdentifier	= '"';															# Text Identifier


########################## Sprachspezifsiche Einstellungen ############# ###################################################

# Falls Sprachparameter nicht übergeben, Standard setzen
if(isset($_GET['lang']) && $_GET['lang'] == "" || !isset($_GET['lang'])) {
	$_GET['lang'] = $Standardsprache;
}

switch($_GET['lang']) {

case "de": # deutsch DLV !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

# --- Textbausteine/Übersetzungen ----- Outputtext/Translations-----------
$txt_laive = "LaIVE";
$TitleHTMLInfo 	= "(Leichtathletik-Live-Ergebnisse)";
$txt_vom = "vom";
$txt_am = "am";
$txt_in = "in";
$txt_uebersicht = "Übersicht";
$txt_zeitplan = "Zeitplan";
$txt_stellplatzzeitplan = "Stellplatz-Zeitplan";
$txt_gesamtteilnehmerliste = "Gesamtteilnehmerliste";
$txt_gesamtteilnehmerlistenachverein = "Teilnehmerliste nach Vereinen";
$txt_gesamtteilnehmerlistenachwettbewerben = "Teilnehmerliste nach Wettbewerben";
$txt_tag = "Tag";
$txt_kopf_zeitplanaktualisiert ="Zeitplan aktualisiert:";
$text_hinweissortierunguebersicht = "Klicken Sie auf den Spaltenkopf um die Tabelle zu sortieren.";

$txt_klasse = "Klasse";
$txt_disziplin = "Disziplin";
$txt_runde = "Runde"; 
$txt_typ = "Typ";
$txt_aktualisiert = "akt.";
$txt_startzeit = "Zeit";
$txt_wettbewerb = "Wettbewerb";
$txt_anzahlrunden = "Runden";
$txt_IPCClassesName = "Startklasse";
$txt_zeit = "späteste<br>Abgabe-Zeit";
$txt_meldungen = "Meldungen";
$txt_wbnr = "Wb.-Nr.";
$txt_headline_abbrev_participansandteams = "T/S";
$txt_headline_explanation_participansandteams = "Anzahl Teilnehmer oder Staffelmannschaften in der jeweiligen Runde des Wettbewerbs";
$txt_headline_abbrev_heatsandgroups = "L/G";
$txt_headline_explanation_heatsandgroups = "Anzahl Läufe oder Gruppen in der jeweiligen Runde des Wettbewerbs";

$txt_startnummer = "St.-Nr.";
$txt_name = "Name";
$txt_geschlecht = "m/w";
$txt_jahrgang = "JG";
$txt_lv = "LV";
$txt_verein = "Verein";
$txt_gemeldetewettbewerbe = "Gemeldete Wettbewerbe";

$txt_stellplatzabgabe = "Die Stellplatzkarten müssen spätestens (!) zur angegebenen »Abgabe-Zeit« abgegeben worden sein!";
$txt_anzahlrunden = "Runden";
$txt_anzahlrunden_keine = "Noch keine Teilnehmer- oder Ergebnislisten vorhanden.";
$txt_anzahlwettbewerbestellplatz = "Wettbewerbe mit Stellplatzkarten-Abgabe";
$txt_zwischenergebnisse = "Zwischenergebnisliste";
$txt_finale_ergebnisse = "Finale (Ergebnisse)";
$txt_zwischenlaeufe_ergebnisse = "Zwischenläufe (Ergebnisse)";
$txtIPCMode = "DBS";
$txt_laivefuss1 = "$txt_laive (Version: $ResultTickerVersion $IPCMode - $ResultTickerErsteller - <a href='http://kwenzel.net/Special_Contact' target='_blank'>laive@kwenzel.net</a>)";
$txt_laivefuss2 = "Datenverarbeitung mit COSA WIN.";

$txt_meta_beschreibung = "Live-Daten mit Ergebnisse von der Leichtathletik-Veranstaltung:";

# Format ----------------------------------------
$MarkSeperator1 = ",";

# Allgemein ----------------------------------------
$TxtMixedEvent = "Gemischter Wettbewerb";
$TxtCombinedEventGroup = "Riege";
$TxtHeat = "Lauf";
$TxtGroup = "Gruppe";
$TxtAbrrevGenderMale = "m";
$TxtAbrrevGenderMan = "M";
$TxtAbrrevGenderFemale = "w";
$TxtAbrrevGenderWoman = "W";
$TxtAbbrevOutOfRanking = "a.W.";
$TxtParticipant = "Teilnehmer";
$TxtParticipants = "Teilnehmer";
$TxtAbbrevParticipant = "Teiln.";
$TxtAbbrevParticipants = "Teiln.";
$TxtAbbrevRelayTeam = "Staff.";
$TxtAbbrevRelayTeams = "Staff.";
$TxtDaytimeUnit = "Uhr";
$txtMenuStartlistsAll = "Alle Startlisten";


# Index.php ----------------------------------------
$LinksSubMenuResultsFinal = "Finale (Ergebnisse)";
$LinksSubMenuResultsSemifinals = "Zwischenläufe (Ergebnisse)";
$LinksSubMenuResultsTimedHeats = "Zeitvorläufe (Ergebnisse)";
$LinksSubMenuResultsHeats = "Vorläufe (Ergebnisse)";
$LinksSubMenuResultsCombinedEventAfter9Events = "nach 9 Disz. (Erg.)";
$LinksSubMenuResultsCombinedEventAfter8Events = "nach 8 Disz. (Erg.)";
$LinksSubMenuResultsCombinedEventAfter7Events = "nach 7 Disz. (Erg.)";
$LinksSubMenuResultsCombinedEventAfter6Events = "nach 6 Disz. (Erg.)";
$LinksSubMenuResultsCombinedEventAfter5Events = "nach 5 Disz. (Erg.)";
$LinksSubMenuResultsCombinedEventAfter4Events = "nach 4 Disz. (Erg.)";
$LinksSubMenuResultsCombinedEventAfter3Events = "nach 3 Disz. (Erg.)";
$LinksSubMenuResultsCombinedEventAfter2Events = "nach 2 Disz. (Erg.)";
$LinksSubMenuResultsCombinedEventAfter1Event = "nach 1 Disz. (Erg.)";
$LinksSubMenuStartlistFinalTrack = "Finale (Startl.)";
$LinksSubMenuStartlistFinalHJ = "Finale (Startl.)";
$LinksSubMenuStartlistFinalField = "Finale (Startl.)";
$LinksSubMenuStartlistFinalTrackAB = "A-/B-Finale (Startl.)";
$LinksSubMenuStartlistTimeRace = "Zeitläufe (Startl.)";
$LinksSubMenuStartlistSemifinals = "Zwischenläufe (Startl.)";
$LinksSubMenuStartlistTimedHeats = "Zeitvorläufe (Startl.)";
$LinksSubMenuStartlistHeats = "Vorläufe (Startl.)";
$LinksSubMenuStartlistEliminationTrack = "Ausscheidungslauf (Startl.)";
$LinksSubMenuStartlistEliminationField = "Ausscheidung (Startl.)";
$LinksSubMenuStartlistQualification = "Qualifikation (Startl.)";
$LinksSubMenuStartlistOnlyHeatNumber = "Nur Lauf-Nr. (Startl.)";
$LinksSubMenuParticipantslist = "Teilnehmerliste";
$TxtSubMenuUpdated = "aktualisiert:";
$TxtFooterSiteLoadedIn = "Seite geladen in";
$TxtFooterSiteLoadedInUnit = "s";
$txtMenuEntriesAll = "Gesamtteiln.";
$txtMenuEntriesByEvent ="Teiln. nach Wettb.";
$txtMenuEntriesByClub ="Teiln. nach Verein";
$LinksSubMenuResultsEliminationTrack = "Ausscheidungslauf (Ergebnisse)";
$LinksSubMenuResultsTimeRace = "Zeitläufe (Ergebnisse)";
$LinksSubMenuResultsFinalTrackAB = "A-/B-Finale (Ergebnisse)";
$LinksSubMenuResultsOnlyHeatNumber = "Nur Lauf-Nr. (Ergebnisse)";
$LinksSubMenuResultsFinalHJ = "Finale (Ergebnisse)";
$LinksSubMenuResultsFinalField = "Finale (Ergebnisse)";
$LinksSubMenuResultsEliminationField = "Ausscheidung (Ergebnisse)";
$LinksSubMenuResultsQualification = "Qualifikation (Ergebnisse)";


# zeitplan.php ----------------------------------------


$TxtHeats = "Läufe";

$TxtGroups = "Gruppen";
$TxtLinkEntriesByClubs = "Teilnehmerliste nach Vereinen (Vereinsmeldungen)";




$TxtEntry = "Meldung";
$TxtEntries = "Meldungen";
$TxtNoEntry = "0";

$TxtHeadKey = "Legende";

$TxtTTBoardPage = "Seite";
$TxtTTBoardDownload = "Herunterladen des Zeitplans für Anzeigetafel";


# startlistenerstellen.php ----------------------------------------
$TxtStartlistHeadline = "Startliste";

$TxtSeasonBest = "Meldeleistung";
$TxtQualificationMark = "Qualifikations-<br>leistung";

$TxtAbbrevOrdner = "Pos.";
$TxtAbbrevBIB = "St.-Nr.";
$TxtAbbrevBIBRelay = "EDV-Nr.";
$TxtAthleteName = "Name";
$TxtAbbrevJOB = "JG";
$TxtAbbrevNation = "LV";
$TxtClub = "Verein";
$TxtRelayTeam = "Mannschaft";
$TxtRelayMembers = "Mannschaftsmitglieder";
$TxtQualificationHeat = "Vorlauf";
$TxtQualificationHeats = "Vorläufen";
$TxtQualificationTimedHeat = "Zeitvorlauf";
$TxtQualificationTimedHeats = "Zeitvorläufen";
$TxtQualificationSemiFinal = "Zwischenlauf";
$TxtQualificationSemiFinals = "Zwischenläufen";
$TxtQualificationByPlaceTrack1 = "Erstplatzierter pro Lauf (Q)";
$TxtQualificationByPlaceTrackMore = "Erstplatzierte pro Lauf (Q)";
$TxtQualificationByTimeTrack1 = "Zeitschnellster (q)";
$TxtQualificationByTimeTrackMore = "Zeitschnellste (q)";
$TxtAt = "um";

$TxtOnThe = "am";
$TxtQualificationToFinal = "Finale";
$TxtQualificationToFinalsNotEqual = "Finale (ungleichberechtigt)";
$TxtQualificationToFinalsEqual = "Finale (gleichberechtigt)";
$TxtQualificationToFinal2 = "das Finale";
$TxtQualificationToFinal3 = "das Finale";
$TxtQualificationToSemiFinal = "Zwischenlauf";
$TxtQualificationToSemiFinals = "Zwischenläufe";
$TxtQualificationWordsBetweenPlaceAndTime = "und";
$TxtQualificationFrom = "Aus";
$TxtQualificationAdvancedTo = "qualifizieren sich";
$TxtQualificationToWord = "für";
$TxtQualificationHeadline = "Qualifikationsmodus";
$TxtHJHeightsHeadline = "Sprunghöhen";

$TxtStartlistHeadlineAll = "Startlisten aller Wettbewerbe und Runden";

# create_entrylists.php ----------------------------------------
$TxtEntrylistHeadline = "Teilnehmerliste";
$TxtFinalConfirmation[0]['Abbrev'] 		= "&#10008;";
$TxtFinalConfirmation[0]['Explanation'] = "Stellplatzkarte -nicht- abgegeben";
$TxtFinalConfirmation[1]['Abbrev'] 		= "&#10004;";
$TxtFinalConfirmation[1]['Explanation'] = "Stellplatzkarte abgegeben";

# gesamtteilnehmer.php ----------------------------------------
$TxtLinkSubMenuEntriesList1 = "Gesamtteilnehmerliste";
$TxtLinkSubMenuEntriesList2 = "Teilnehmerliste nach Wettbewerben";
$TxtLinkSubMenuEntriesList3 = "Teilnehmerliste nach Vereinen";
$TxtEvaluationGroupsHeadline = "Wertungs-Gruppen";
$TxtEvaluationGroupsNoGroup = "Ohne Berücksichtigung Wertungs-Gruppen";
$TxtAbbrevLateEntry = "Nachm.";
$TxtLateEntry = "Nachmeldung";
$TxtAnd = "und";
$TxtRelayTeam = "Staffelmannschaft";
$TxtRelayTeams = "Staffelmannschaften";
$TxtSortedByHeadline = "Sortiert nach";
$TxtSortedByBIB = "Startnummern";
$TxtSortedByName = "Namen";
$TxtSortedBySeasonBest = "Meldeleistung";
$TxtSortedByIPCClass = "Startklassen";
$TxtSummaryOfClasses = "Klassenübersicht";
$TxtEntriesByEventsHeats = "Vorläufe";
$TxtEntriesByEventsSemiFinals = "Zwischenläufe";
$TxtEntriesByEventsFinal = "Finale";
$TxtEntriesByEventsCombinedEventFirstEvent = "Beginn 1. Disz.";
$TxtClubs = "Vereine";
$TxtIPCClass = "Startkl.";
$TxtSDMSID = "SDMS-ID";
$TxtAthletesLicenceIDShowHead = "Athletennummern anzeigen";
$TxtAthletesLicenceIDDontShowHead = "Athletennummern nicht anzeigen";
$TxtAthletesLicenceID = "Athleten-Nr.";
$TxtAthletesOnlineID = "Online-Athleten-Nr.";

# uebersicht.php ----------------------------------------
$TxtLinkJustRL = "Zeige nur Ergebnislisten an";

# cupscoring.php ----------------------------------------
$txt_headline_cupscoring = "Pokalwertung";
$txt_cup_standingafter_1 = "Stand nach";
$txt_cup_standingafter_2 = "von";
$txt_cup_standingafter_3 = "Wettbewerben";
$cup_tablehead_place	= "Platz"; 
$cup_tablehead_team		= "Mannschaft";
$cup_tablehead_points 	= "Punkte";
$cup_afterplace 		= ".";
$txt_cup_includedevents = "Folgende Wettbewerbe sind in der Wertung enthalten";
$txt_cup_detailedScoring = "Punktaufschlüsselung nach Wettbewerb";
$txt_cup_scoringbased  = "Cup scoring is based on the following rules";
$txt_cup_Nb_ScoredAthletes = "Number of scored athletes per team and event";
$txt_cup_Nb_ScoredPlaces = "Number of scored places in event";
$txt_cup_Nb_Points = "Points (starting by 1st Place)";
$txt_cup_Nb_ScoredRelays = "Number of scored relay teams per team and event";
$txt_cup_Nb_ScoredPlacesRelays = "Number of scored places in relay event";
$txt_cup_Nb_PointsRelays = "Points for relays (starting by 1st Place)";

$txt_headline_combined_cupscoring 	= "Kombinierte Pokalwertung";
$txt_combined_cup_detailedScoring 	= "Punkte (und Plätze) nach Pokalwertungen";
$txt_combined_cup_includedcups		= "Folgende Pokalwertungen sind in der kombinierten Wertung enthalten";
$txt_cup_Combined_standingafter_1	= "von";
$txt_cup_Combined_standingafter_2	= "Wettbewerben beendet";

### Noch ergänzen ###


$competitionsubname	="Veranst.-Webseite";
$entryname			="Teiln. nach Wettbew.";					#Teilnehmerliste nach Wettbewerb
$entrybyclubname	="Teiln. nach Verein (PDF)";				#Bezeichnung TN nach Vereinen
$resultname			="Ergebnisse";								#Bezeichnung Ergebnisse
$resultname1		="Tag 1 (Ergebnisse)";						#Bezeichnung Ergebnisse Tag 1
$resultname2		="Tag 2 (Ergebnisse)";						#Bezeichnung Ergebnisse Tag 2

# --- Dateien ------ Files -----------------------------------------------

# Module ---- Modules
$dat_index = "index.php";
$dat_uebersicht = "uebersicht.php";
$dat_zeitplan = "zeitplan.php";
$dat_stellplatzzeitplan = "stellplatzzeitplan.php";



# COSA-Dateien --- COSA Files
$dat_vandat 	= "./laive_vandat.c01";
$dat_wettbew 	= "./laive_Wettbew.c01";
$dat_wbteiln 	= "./laive_WbTeiln.c01";
$dat_endli 		= "./laive_Endli.c01";
$dat_wklist 	= "./laive_WkList.c01";

$dat_stamm 		= "./laive_Stamm.c01";
$dat_verein 	= "./laive_Verein.c01";



# Zusatzdateien --- Extended Files
$entryfile="_teilnehmer.htm";                 						#Name der Teilnehmerdatei
$entrybyclubfile="_teilnehmerverein.pdf";							#Name der Datei - TN nach Vereinen
$resultfile="_ergebnisse.htm";                						#Name der Ergebnisdatei
$resultfile1="_ergebnisse1.htm";                					#Name der Ergebnisdatei Tag 1
$resultfile2="_ergebnisse2.htm";                					#Name der Ergebnisdatei Tag 2

$competitionsublink="http://";	# competitionsub /Veranstaltungsseite



$Klassen[10]['Nr']		=	10;
$Klassen[10]['Bez']		=	"Männer";
$Klassen[10]['Ident']	=	"Mä";
$Klassen[10]['Abbrev']	=	"M";
	
$Klassen[11]['Nr']		=	11;
$Klassen[11]['Bez']		=	"Frauen";
$Klassen[11]['Ident']	=	"Fr";
$Klassen[11]['Abbrev']	=	"W";

$Klassen[12]['Nr']		=	12;
$Klassen[12]['Bez']		=	"M U23";
$Klassen[12]['Ident']	=	"Ju";
$Klassen[12]['Abbrev']	=	"M U23";

$Klassen[13]['Nr']		=	13;
$Klassen[13]['Bez']		=	"W U23";
$Klassen[13]['Ident']	=	"Jui";
$Klassen[13]['Abbrev']	=	"W U23";

$Klassen[20]['Nr']		=	20;
$Klassen[20]['Bez']		=	"MJ U20";
$Klassen[20]['Ident']	=	"MJA";
$Klassen[20]['Abbrev']	=	"MJ U20";

$Klassen[21]['Nr']		=	21;
$Klassen[21]['Bez']		=	"WJ U20";
$Klassen[21]['Ident']	=	"WJA";
$Klassen[21]['Abbrev']	=	"WJ U20";

$Klassen[22]['Nr']		=	22;
$Klassen[22]['Bez']		=	"MJ U18";
$Klassen[22]['Ident']	=	"MJB";
$Klassen[22]['Abbrev']	=	"MJ U18";

$Klassen[23]['Nr']		=	23;
$Klassen[23]['Bez']		=	"WJ U18";
$Klassen[23]['Ident']	=	"WJB";
$Klassen[23]['Abbrev']	=	"WJ U18";

$Klassen[37]['Nr']		=	37;
$Klassen[37]['Bez']		=	"MJ U16";
$Klassen[37]['Ident']	=	"SA";
$Klassen[37]['Abbrev']	=	"MJ U16";

$Klassen[38]['Nr']		=	38;
$Klassen[38]['Bez']		=	"M14";
$Klassen[38]['Ident']	=	"M14";
$Klassen[38]['Abbrev']	=	"M14";

$Klassen[39]['Nr']		=	39;
$Klassen[39]['Bez']		=	"M15";
$Klassen[39]['Ident']	=	"M15";
$Klassen[39]['Abbrev']	=	"M15";

$Klassen[34]['Nr']		=	34;
$Klassen[34]['Bez']		=	"MJ U14";
$Klassen[34]['Ident']	=	"SB";
$Klassen[34]['Abbrev']	=	"MJ U14";

$Klassen[35]['Nr']		=	35;
$Klassen[35]['Bez']		=	"M12";
$Klassen[35]['Ident']	=	"M12";
$Klassen[35]['Abbrev']	=	"M12";

$Klassen[36]['Nr']		=	36;
$Klassen[36]['Bez']		=	"M13";
$Klassen[36]['Ident']	=	"M13";
$Klassen[36]['Abbrev']	=	"M13";

$Klassen[31]['Nr']		=	31;
$Klassen[31]['Bez']		=	"MK U12";
$Klassen[31]['Ident']	=	"SC";
$Klassen[31]['Abbrev']	=	"MK U12";

$Klassen[32]['Nr']		=	32;
$Klassen[32]['Bez']		=	"M10";
$Klassen[32]['Ident']	=	"M10";
$Klassen[32]['Abbrev']	=	"M10";

$Klassen[33]['Nr']		=	33;
$Klassen[33]['Bez']		=	"M11";
$Klassen[33]['Ident']	=	"M11";
$Klassen[33]['Abbrev']	=	"M11";

$Klassen[28]['Nr']		=	28;
$Klassen[28]['Bez']		=	"MK U10";
$Klassen[28]['Ident']	=	"SD";
$Klassen[28]['Abbrev']	=	"MK U10";

$Klassen[29]['Nr']		=	29;
$Klassen[29]['Bez']		=	"M 8";
$Klassen[29]['Ident']	=	"M08";
$Klassen[29]['Abbrev']	=	"M 8";

$Klassen[30]['Nr']		=	30;
$Klassen[30]['Bez']		=	"M 9";
$Klassen[30]['Ident']	=	"M09";
$Klassen[30]['Abbrev']	=	"M 9";

$Klassen[24]['Nr']		=	24;
$Klassen[24]['Bez']		=	"MK U08";
$Klassen[24]['Ident']	=	"SE";
$Klassen[24]['Abbrev']	=	"MK U08";

$Klassen[88]['Nr']		=	88;
$Klassen[88]['Bez']		=	"M 7";
$Klassen[88]['Ident']	=	"M07";
$Klassen[88]['Abbrev']	=	"M 7";

$Klassen[86]['Nr']		=	86;
$Klassen[86]['Bez']		=	"M 6";
$Klassen[86]['Ident']	=	"M06";
$Klassen[86]['Abbrev']	=	"M 6";

$Klassen[84]['Nr']		=	84;
$Klassen[84]['Bez']		=	"M 5";
$Klassen[84]['Ident']	=	"M05";
$Klassen[84]['Abbrev']	=	"M 5";

$Klassen[82]['Nr']		=	82;
$Klassen[82]['Bez']		=	"M 4";
$Klassen[82]['Ident']	=	"M04";
$Klassen[82]['Abbrev']	=	"M 4";

$Klassen[80]['Nr']		=	80;
$Klassen[80]['Bez']		=	"M 3";
$Klassen[80]['Ident']	=	"M03";
$Klassen[80]['Abbrev']	=	"M 3";

$Klassen[47]['Nr']		=	47;
$Klassen[47]['Bez']		=	"WJ U16";
$Klassen[47]['Ident']	=	"SIA";
$Klassen[47]['Abbrev']	=	"WJ U16";

$Klassen[48]['Nr']		=	48;
$Klassen[48]['Bez']		=	"W14";
$Klassen[48]['Ident']	=	"W14";
$Klassen[48]['Abbrev']	=	"W14";

$Klassen[49]['Nr']		=	49;
$Klassen[49]['Bez']		=	"W15";
$Klassen[49]['Ident']	=	"W15";
$Klassen[49]['Abbrev']	=	"W15";

$Klassen[44]['Nr']		=	44;
$Klassen[44]['Bez']		=	"WJ U14";
$Klassen[44]['Ident']	=	"SIB";
$Klassen[44]['Abbrev']	=	"WJ U14";

$Klassen[45]['Nr']		=	45;
$Klassen[45]['Bez']		=	"W12";
$Klassen[45]['Ident']	=	"W12";
$Klassen[45]['Abbrev']	=	"W12";

$Klassen[46]['Nr']		=	46;
$Klassen[46]['Bez']		=	"W13";
$Klassen[46]['Ident']	=	"W13";
$Klassen[46]['Abbrev']	=	"W13";

$Klassen[27]['Nr']		=	27;
$Klassen[27]['Bez']		=	"WK U12";
$Klassen[27]['Ident']	=	"SIC";
$Klassen[27]['Abbrev']	=	"WK U12";

$Klassen[42]['Nr']		=	42;
$Klassen[42]['Bez']		=	"W10";
$Klassen[42]['Ident']	=	"W10";
$Klassen[42]['Abbrev']	=	"W10";

$Klassen[43]['Nr']		=	43;
$Klassen[43]['Bez']		=	"W11";
$Klassen[43]['Ident']	=	"W11";
$Klassen[43]['Abbrev']	=	"W11";

$Klassen[26]['Nr']		=	26;
$Klassen[26]['Bez']		=	"WK U10";
$Klassen[26]['Ident']	=	"SID";
$Klassen[26]['Abbrev']	=	"WK U10";

$Klassen[40]['Nr']		=	40;
$Klassen[40]['Bez']		=	"W 8";
$Klassen[40]['Ident']	=	"W08";
$Klassen[40]['Abbrev']	=	"W 8";

$Klassen[41]['Nr']		=	41;
$Klassen[41]['Bez']		=	"W 9";
$Klassen[41]['Ident']	=	"W09";
$Klassen[41]['Abbrev']	=	"W 9";

$Klassen[25]['Nr']		=	25;
$Klassen[25]['Bez']		=	"WK U08";
$Klassen[25]['Ident']	=	"SIE";
$Klassen[25]['Abbrev']	=	"WK U08";

$Klassen[89]['Nr']		=	89;
$Klassen[89]['Bez']		=	"W 7";
$Klassen[89]['Ident']	=	"W07";
$Klassen[89]['Abbrev']	=	"W 7";

$Klassen[87]['Nr']		=	87;
$Klassen[87]['Bez']		=	"W 6";
$Klassen[87]['Ident']	=	"W06";
$Klassen[87]['Abbrev']	=	"W 6";

$Klassen[85]['Nr']		=	85;
$Klassen[85]['Bez']		=	"W 5";
$Klassen[85]['Ident']	=	"W05";
$Klassen[85]['Abbrev']	=	"W 5";

$Klassen[83]['Nr']		=	83;
$Klassen[83]['Bez']		=	"W 4";
$Klassen[83]['Ident']	=	"W04";
$Klassen[83]['Abbrev']	=	"W 4";

$Klassen[81]['Nr']		=	81;
$Klassen[81]['Bez']		=	"W 3";
$Klassen[81]['Ident']	=	"W03";
$Klassen[81]['Abbrev']	=	"W 3";

$Klassen[50]['Nr']		=	50;
$Klassen[50]['Bez']		=	"M30";
$Klassen[50]['Ident']	=	"M30";
$Klassen[50]['Abbrev']	=	"M30";

$Klassen[51]['Nr']		=	51;
$Klassen[51]['Bez']		=	"W30";
$Klassen[51]['Ident']	=	"W30";
$Klassen[51]['Abbrev']	=	"W30";

$Klassen[52]['Nr']		=	52;
$Klassen[52]['Bez']		=	"M35";
$Klassen[52]['Ident']	=	"M35";
$Klassen[52]['Abbrev']	=	"M35";

$Klassen[53]['Nr']		=	53;
$Klassen[53]['Bez']		=	"W35";
$Klassen[53]['Ident']	=	"W35";
$Klassen[53]['Abbrev']	=	"W35";

$Klassen[54]['Nr']		=	54;
$Klassen[54]['Bez']		=	"M40";
$Klassen[54]['Ident']	=	"M40";
$Klassen[54]['Abbrev']	=	"M40";

$Klassen[55]['Nr']		=	55;
$Klassen[55]['Bez']		=	"W40";
$Klassen[55]['Ident']	=	"W40";
$Klassen[55]['Abbrev']	=	"W40";

$Klassen[56]['Nr']		=	56;
$Klassen[56]['Bez']		=	"M45";
$Klassen[56]['Ident']	=	"M45";
$Klassen[56]['Abbrev']	=	"M45";

$Klassen[57]['Nr']		=	57;
$Klassen[57]['Bez']		=	"W45";
$Klassen[57]['Ident']	=	"W45";
$Klassen[57]['Abbrev']	=	"W45";

$Klassen[58]['Nr']		=	58;
$Klassen[58]['Bez']		=	"M50";
$Klassen[58]['Ident']	=	"M50";
$Klassen[58]['Abbrev']	=	"M50";

$Klassen[59]['Nr']		=	59;
$Klassen[59]['Bez']		=	"W50";
$Klassen[59]['Ident']	=	"W50";
$Klassen[59]['Abbrev']	=	"W50";

$Klassen[60]['Nr']		=	60;
$Klassen[60]['Bez']		=	"M55";
$Klassen[60]['Ident']	=	"M55";
$Klassen[60]['Abbrev']	=	"M55";

$Klassen[61]['Nr']		=	61;
$Klassen[61]['Bez']		=	"W55";
$Klassen[61]['Ident']	=	"W55";
$Klassen[61]['Abbrev']	=	"W55";

$Klassen[62]['Nr']		=	62;
$Klassen[62]['Bez']		=	"M60";
$Klassen[62]['Ident']	=	"M60";
$Klassen[62]['Abbrev']	=	"M60";

$Klassen[63]['Nr']		=	63;
$Klassen[63]['Bez']		=	"W60";
$Klassen[63]['Ident']	=	"W60";
$Klassen[63]['Abbrev']	=	"W60";

$Klassen[64]['Nr']		=	64;
$Klassen[64]['Bez']		=	"M65";
$Klassen[64]['Ident']	=	"M65";
$Klassen[64]['Abbrev']	=	"M65";

$Klassen[65]['Nr']		=	65;
$Klassen[65]['Bez']		=	"W65";
$Klassen[65]['Ident']	=	"W65";
$Klassen[65]['Abbrev']	=	"W65";

$Klassen[66]['Nr']		=	66;
$Klassen[66]['Bez']		=	"M70";
$Klassen[66]['Ident']	=	"M70";
$Klassen[66]['Abbrev']	=	"M70";

$Klassen[67]['Nr']		=	67;
$Klassen[67]['Bez']		=	"W70";
$Klassen[67]['Ident']	=	"W70";
$Klassen[67]['Abbrev']	=	"W70";

$Klassen[68]['Nr']		=	68;
$Klassen[68]['Bez']		=	"M75";
$Klassen[68]['Ident']	=	"M75";
$Klassen[68]['Abbrev']	=	"M75";

$Klassen[69]['Nr']		=	69;
$Klassen[69]['Bez']		=	"W75";
$Klassen[69]['Ident']	=	"W75";
$Klassen[69]['Abbrev']	=	"W75";

$Klassen[70]['Nr']		=	70;
$Klassen[70]['Bez']		=	"M80";
$Klassen[70]['Ident']	=	"M80";
$Klassen[70]['Abbrev']	=	"M80";

$Klassen[71]['Nr']		=	71;
$Klassen[71]['Bez']		=	"W80";
$Klassen[71]['Ident']	=	"W80";
$Klassen[71]['Abbrev']	=	"W80";

$Klassen[72]['Nr']		=	72;
$Klassen[72]['Bez']		=	"M85";
$Klassen[72]['Ident']	=	"M85";
$Klassen[72]['Abbrev']	=	"M85";

$Klassen[73]['Nr']		=	73;
$Klassen[73]['Bez']		=	"W85";
$Klassen[73]['Ident']	=	"W85";
$Klassen[73]['Abbrev']	=	"W85";

$Klassen[99]['Nr']		=	99;
$Klassen[99]['Bez']		=	"alle Klassen";
$Klassen[99]['Ident']	=	"alle Klassen";
$Klassen[99]['Abbrev']	=	"alle";

# --- Disziplinen-Array ------------------------------------------------------

$Disziplinen[6]['Bez']		=	"30 m Lauf";
$Disziplinen[6]['Kurz']		=	"30m";
$Disziplinen[6]['Typ']		=	"l";

$Disziplinen[10]['Bez']		=	"50 m Lauf";
$Disziplinen[10]['Typ']		=	"l";
$Disziplinen[10]['Kurz']	=	"50m";

$Disziplinen[15]['Bez']		=	"60 m Lauf";
$Disziplinen[15]['Kurz']	=	"60m";
$Disziplinen[15]['Typ']		=	"l";

$Disziplinen[20]['Bez']		=	"75 m Lauf";
$Disziplinen[20]['Kurz']	=	"75m";
$Disziplinen[20]['Typ']		=	"l";

$Disziplinen[24]['Bez']		=	"80 m Lauf";
$Disziplinen[24]['Kurz']	=	"80m";
$Disziplinen[24]['Typ']		=	"l";

$Disziplinen[30]['Bez']		=	"100 m Lauf";
$Disziplinen[30]['Kurz']	=	"100m";
$Disziplinen[30]['Typ']		=	"l";

$Disziplinen[31]['Bez']		=	"100 m Rollstuhl"; #
$Disziplinen[31]['Kurz']	=	"100 Roll."; #
$Disziplinen[31]['Typ']		=	"l"; #

$Disziplinen[35]['Bez']		=	"150 m Lauf";
$Disziplinen[35]['Kurz']	=	"150m";
$Disziplinen[35]['Typ']		=	"l";

$Disziplinen[40]['Bez']		=	"200 m Lauf";
$Disziplinen[40]['Kurz']	=	"200m";
$Disziplinen[40]['Typ']		=	"l";

$Disziplinen[41]['Bez']		=	"200 m Rollstuhl"; #
$Disziplinen[41]['Kurz']	=	"200 Roll."; #
$Disziplinen[41]['Typ']		=	"l"; #

$Disziplinen[45]['Bez']		=	"300 m Lauf";
$Disziplinen[45]['Kurz']	=	"300m";
$Disziplinen[45]['Typ']		=	"l";

$Disziplinen[50]['Bez']		=	"400 m Lauf";
$Disziplinen[50]['Kurz']	=	"400m";
$Disziplinen[50]['Typ']		=	"l";

$Disziplinen[51]['Bez']		=	"400 m Rollstuhl"; #
$Disziplinen[51]['Kurz']	=	"400 Roll."; #
$Disziplinen[51]['Typ']		=	"l"; #

$Disziplinen[53]['Bez']		=	"500 m Lauf";
$Disziplinen[53]['Kurz']	=	"500m";
$Disziplinen[53]['Typ']		=	"l";

$Disziplinen[55]['Bez']		=	"600 m Lauf";
$Disziplinen[55]['Kurz']	=	"600m";
$Disziplinen[55]['Typ']		=	"l";

$Disziplinen[60]['Bez']		=	"800 m Lauf";
$Disziplinen[60]['Kurz']	=	"800m";
$Disziplinen[60]['Typ']		=	"l";

$Disziplinen[61]['Bez']		=	"800 m Rollstuhl"; #
$Disziplinen[61]['Kurz']	=	"800 Roll."; #
$Disziplinen[61]['Typ']		=	"l"; #

$Disziplinen[70]['Bez']		=	"1000 m Lauf";
$Disziplinen[70]['Kurz']	=	"1000m";
$Disziplinen[70]['Typ']		=	"l";

$Disziplinen[80]['Bez']		=	"1500 m Lauf";
$Disziplinen[80]['Kurz']	=	"1500m";
$Disziplinen[80]['Typ']		=	"l";

$Disziplinen[81]['Bez']		=	"1500 m Rollstuhl"; #
$Disziplinen[81]['Kurz']	=	"1500 Roll."; #
$Disziplinen[81]['Typ']		=	"l"; #

$Disziplinen[90]['Bez']		=	"1 Meilen-Lauf";
$Disziplinen[90]['Kurz']	=	"1 Meile";
$Disziplinen[90]['Typ']		=	"l";

$Disziplinen[100]['Bez']	=	"2000 m Lauf";
$Disziplinen[100]['Kurz']	=	"2000m";
$Disziplinen[100]['Typ']	=	"l";

$Disziplinen[110]['Bez']	=	"3000 m Lauf";
$Disziplinen[110]['Kurz']	=	"3000m";
$Disziplinen[110]['Typ']	=	"l";

$Disziplinen[111]['Bez']	=	"3000 m Rollstuhl";
$Disziplinen[111]['Kurz']	=	"3000 Roll.";
$Disziplinen[111]['Typ']	=	"l";

$Disziplinen[120]['Bez']	=	"5000 m Lauf";
$Disziplinen[120]['Kurz']	=	"5000m";
$Disziplinen[120]['Typ']	=	"l";

$Disziplinen[121]['Bez']	=	"5000 m Rollstuhl"; #
$Disziplinen[121]['Kurz']	=	"5000 Roll."; #
$Disziplinen[121]['Typ']	=	"l"; #

$Disziplinen[125]['Bez']	=	"10000 m Lauf";
$Disziplinen[125]['Kurz']	=	"10000m";
$Disziplinen[125]['Typ']	=	"l";

$Disziplinen[126]['Bez']	=	"Halbstundenlauf";
$Disziplinen[126]['Kurz']	=	"H-StdLauf";
$Disziplinen[126]['Typ']	=	"l";

$Disziplinen[127]['Bez']	=	"Halbstundenlauf Ma";
$Disziplinen[127]['Kurz']	=	"H-StdLauf Ma.";
$Disziplinen[127]['Typ']	=	"s";

$Disziplinen[128]['Bez']	=	"Stundenlauf";
$Disziplinen[128]['Kurz']	=	"StdLauf";
$Disziplinen[128]['Typ']	=	"l";

$Disziplinen[129]['Bez']	=	"Stundenlauf Ma.";
$Disziplinen[129]['Kurz']	=	"StdLauf Ma.";
$Disziplinen[129]['Typ']	=	"s";

$Disziplinen[131]['Bez']	=	"5 km Str.";
$Disziplinen[131]['Kurz']	=	"5 km";
$Disziplinen[131]['Typ']	=	"w";

$Disziplinen[132]['Bez']	=	"5 km Str. Ma.";
$Disziplinen[132]['Kurz']	=	"5 km Ma.";
$Disziplinen[132]['Typ']	=	"w";

$Disziplinen[133]['Bez']	=	"7,5 km Str.";
$Disziplinen[133]['Kurz']	=	"7,5 km";
$Disziplinen[133]['Typ']	=	"w";

$Disziplinen[134]['Bez']	=	"7,5 km Str. Ma.";
$Disziplinen[134]['Kurz']	=	"7,5 km Ma.";
$Disziplinen[134]['Typ']	=	"w";

$Disziplinen[135]['Bez']	=	"10 km Str.";
$Disziplinen[135]['Kurz']	=	"10 km";
$Disziplinen[135]['Typ']	=	"w";

$Disziplinen[136]['Bez']	=	"10 km Str. Ma.";
$Disziplinen[136]['Kurz']	=	"10 km Ma.";
$Disziplinen[136]['Typ']	=	"w";

$Disziplinen[137]['Bez']	=	"15 km Str.";
$Disziplinen[137]['Kurz']	=	"15 km";
$Disziplinen[137]['Typ']	=	"w";

$Disziplinen[138]['Bez']	=	"15 km Str. Ma.";
$Disziplinen[138]['Kurz']	=	"15 km Ma.";
$Disziplinen[138]['Typ']	=	"w";

$Disziplinen[140]['Bez']	=	"25 km Str.";
$Disziplinen[140]['Kurz']	=	"25 km";
$Disziplinen[140]['Typ']	=	"w";

$Disziplinen[141]['Bez']	=	"25 km Str. Ma.";
$Disziplinen[141]['Kurz']	=	"25 km Ma.";
$Disziplinen[141]['Typ']	=	"w";

$Disziplinen[148]['Bez']	=	"Halbmarathon";
$Disziplinen[148]['Kurz']	=	"Halbm.";
$Disziplinen[148]['Typ']	=	"w";

$Disziplinen[149]['Bez']	=	"Halbmarathon Ma.";
$Disziplinen[149]['Kurz']	=	"Halbm. Ma.";
$Disziplinen[149]['Typ']	=	"w";

$Disziplinen[150]['Bez']	=	"Marathon";
$Disziplinen[150]['Kurz']	=	"Marath.";
$Disziplinen[150]['Typ']	=	"w";

$Disziplinen[151]['Bez']	=	"Marathon Ma.";
$Disziplinen[151]['Kurz']	=	"Marath. Ma.";
$Disziplinen[151]['Typ']	=	"w";

$Disziplinen[153]['Bez']	=	"100 km";
$Disziplinen[153]['Kurz']	=	"100 km";
$Disziplinen[153]['Typ']	=	"w";

$Disziplinen[154]['Bez']	=	"100 km Ma.";
$Disziplinen[154]['Kurz']	=	"100 km Ma.";
$Disziplinen[154]['Typ']	=	"w";

$Disziplinen[156]['Bez']	=	"5 x 10 km Staffel";
$Disziplinen[156]['Kurz']	=	"5x10 km";
$Disziplinen[156]['Typ']	=	"w";

$Disziplinen[159]['Bez']	=	"50 m Hürden";
$Disziplinen[159]['Kurz']	=	"50m H.";
$Disziplinen[159]['Typ']	=	"l";

$Disziplinen[160]['Bez']	=	"60 m Hürden";
$Disziplinen[160]['Kurz']	=	"60m H.";
$Disziplinen[160]['Typ']	=	"l";

$Disziplinen[170]['Bez']	=	"80 m Hürden";
$Disziplinen[170]['Kurz']	=	"80m H.";
$Disziplinen[170]['Typ']	=	"l";

$Disziplinen[180]['Bez']	=	"100 m Hürden";
$Disziplinen[180]['Kurz']	=	"100m H.";
$Disziplinen[180]['Typ']	=	"l";

$Disziplinen[190]['Bez']	=	"110 m Hürden";
$Disziplinen[190]['Kurz']	=	"110m H.";
$Disziplinen[190]['Typ']	=	"l";

$Disziplinen[195]['Bez']	=	"300 m Hürden";
$Disziplinen[195]['Kurz']	=	"300m H.";
$Disziplinen[195]['Typ']	=	"l";

$Disziplinen[200]['Bez']	=	"400 m Hürden";
$Disziplinen[200]['Kurz']	=	"400m H.";
$Disziplinen[200]['Typ']	=	"l";

$Disziplinen[210]['Bez']	=	"1500 m Hindernis";
$Disziplinen[210]['Kurz']	=	"1500mHi";
$Disziplinen[210]['Typ']	=	"l";

$Disziplinen[220]['Bez']	=	"2000 m Hindernis";
$Disziplinen[220]['Kurz']	=	"2000mHi";
$Disziplinen[220]['Typ']	=	"l";

$Disziplinen[230]['Bez']	=	"3000 m Hindernis";
$Disziplinen[230]['Kurz']	=	"3000mHi";
$Disziplinen[230]['Typ']	=	"l";

$Disziplinen[240]['Bez']	=	"4 x 50 m Staffel";
$Disziplinen[240]['Kurz']	=	"4x50m";
$Disziplinen[240]['Typ']	=	"s";

$Disziplinen[250]['Bez']	=	"4 x 75 m Staffel";
$Disziplinen[250]['Kurz']	=	"4x75m";
$Disziplinen[250]['Typ']	=	"s";

$Disziplinen[260]['Bez']	=	"4 x 100 m Staffel";
$Disziplinen[260]['Kurz']	=	"4x100m";
$Disziplinen[260]['Typ']	=	"s";

$Disziplinen[261]['Bez']	=	"4 x 100 m Rollstuhl";
$Disziplinen[261]['Kurz']	=	"4x100 Roll.";
$Disziplinen[261]['Typ']	=	"s";

$Disziplinen[270]['Bez']	=	"4 x 200 m Staffel";
$Disziplinen[270]['Kurz']	=	"4x200m";
$Disziplinen[270]['Typ']	=	"s";

$Disziplinen[280]['Bez']	=	"4 x 400 m Staffel";
$Disziplinen[280]['Kurz']	=	"4x400m";
$Disziplinen[280]['Typ']	=	"s";

$Disziplinen[281]['Bez']	=	"4 x 400 m Rollstuhl";
$Disziplinen[281]['Kurz']	=	"4x400 Roll.";
$Disziplinen[281]['Typ']	=	"s";

$Disziplinen[290]['Bez']	=	"3 x 800 m Staffel";
$Disziplinen[290]['Kurz']	=	"3x800m";
$Disziplinen[290]['Typ']	=	"s";

$Disziplinen[300]['Bez']	=	"4 x 800 m Staffel";
$Disziplinen[300]['Kurz']	=	"4x800m";
$Disziplinen[300]['Typ']	=	"s";

$Disziplinen[310]['Bez']	=	"3 x 1000 m Staffel";
$Disziplinen[310]['Kurz']	=	"3x1000m";
$Disziplinen[310]['Typ']	=	"s";

$Disziplinen[320]['Bez']	=	"4 x 1500 m Staffel";
$Disziplinen[320]['Kurz']	=	"4x1500m";
$Disziplinen[320]['Typ']	=	"s";

$Disziplinen[321]['Bez']	=	"Olympische Staffel";
$Disziplinen[321]['Kurz']	=	"Oly-St.";
$Disziplinen[321]['Typ']	=	"s";

$Disziplinen[322]['Bez']	=	"Schwedenstaffel";
$Disziplinen[322]['Kurz']	=	"Schwed.";
$Disziplinen[322]['Typ']	=	"s";

$Disziplinen[323]['Bez']	=	"Schwellstaffel";
$Disziplinen[323]['Kurz']	=	"Schwell";
$Disziplinen[323]['Typ']	=	"s";

$Disziplinen[324]['Bez']	=	"Pendelstaffel";
$Disziplinen[324]['Kurz']	=	"Pendel";
$Disziplinen[324]['Typ']	=	"s";

$Disziplinen[325]['Bez']	=	"1000 m Bahngehen";
$Disziplinen[325]['Kurz']	=	"1000mBG";
$Disziplinen[325]['Typ']	=	"l";

$Disziplinen[330]['Bez']	=	"2000 m Bahngehen";
$Disziplinen[330]['Kurz']	=	"2000mBG";
$Disziplinen[330]['Typ']	=	"l";

$Disziplinen[340]['Bez']	=	"3000 m Bahngehen";
$Disziplinen[340]['Kurz']	=	"3000mBG";
$Disziplinen[340]['Typ']	=	"l";

$Disziplinen[350]['Bez']	=	"5000 m Bahngehen";
$Disziplinen[350]['Kurz']	=	"5000mBG";
$Disziplinen[350]['Typ']	=	"l";

$Disziplinen[352]['Bez']	=	"10.000 m Bahngehen";
$Disziplinen[352]['Kurz']	=	"10000mBG";
$Disziplinen[352]['Typ']	=	"l";

$Disziplinen[354]['Bez']	=	"20.000 m Bahngehen";
$Disziplinen[354]['Kurz']	=	"20000mBG";
$Disziplinen[354]['Typ']	=	"l";

$Disziplinen[355]['Bez']	=	"1km Str.Gehen";
$Disziplinen[355]['Kurz']	=	"1km G";
$Disziplinen[355]['Typ']	=	"w";

$Disziplinen[356]['Bez']	=	"2km Str.Gehen";
$Disziplinen[356]['Kurz']	=	"2km G";
$Disziplinen[356]['Typ']	=	"w";

$Disziplinen[358]['Bez']	=	"3km Str.Gehen";
$Disziplinen[358]['Kurz']	=	"3km G";
$Disziplinen[358]['Typ']	=	"w";

$Disziplinen[359]['Bez']	=	"3km Str.Gehen Ma.";
$Disziplinen[359]['Kurz']	=	"3km G Ma.";
$Disziplinen[359]['Typ']	=	"w";

$Disziplinen[360]['Bez']	=	"5km Str.Gehen";
$Disziplinen[360]['Kurz']	=	"5km G";
$Disziplinen[360]['Typ']	=	"w";

$Disziplinen[361]['Bez']	=	"5km Str.Gehen Ma.";
$Disziplinen[361]['Kurz']	=	"5km G Ma.";
$Disziplinen[361]['Typ']	=	"w";

$Disziplinen[370]['Bez']	=	"10km Str.Gehen";
$Disziplinen[370]['Kurz']	=	"10km G";
$Disziplinen[370]['Typ']	=	"w";

$Disziplinen[371]['Bez']	=	"10km Str.Gehen Ma.";
$Disziplinen[371]['Kurz']	=	"10km G Ma.";
$Disziplinen[371]['Typ']	=	"w";

$Disziplinen[380]['Bez']	=	"20km Str.Gehen";
$Disziplinen[380]['Kurz']	=	"20km G";
$Disziplinen[380]['Typ']	=	"w";

$Disziplinen[381]['Bez']	=	"20km Str.Gehen Ma.";
$Disziplinen[381]['Kurz']	=	"20km G Ma.";
$Disziplinen[381]['Typ']	=	"w";

$Disziplinen[386]['Bez']	=	"30km Str.Gehen";
$Disziplinen[386]['Kurz']	=	"30km G";
$Disziplinen[386]['Typ']	=	"w";

$Disziplinen[387]['Bez']	=	"30km Str.Gehen Ma.";
$Disziplinen[387]['Kurz']	=	"30km G Ma.";
$Disziplinen[387]['Typ']	=	"w";

$Disziplinen[390]['Bez']	=	"50km Str.Gehen";
$Disziplinen[390]['Kurz']	=	"50km G";
$Disziplinen[390]['Typ']	=	"w";

$Disziplinen[391]['Bez']	=	"50km Str.Gehen Ma.";
$Disziplinen[391]['Kurz']	=	"50km G Ma.";
$Disziplinen[391]['Typ']	=	"w";

$Disziplinen[510]['Bez']	=	"Hochsprung";
$Disziplinen[510]['Typ']	=	"h";
$Disziplinen[510]['Kurz']	=	"Hoch";

$Disziplinen[520]['Bez']	=	"Stabhochsprung";
$Disziplinen[520]['Kurz']	=	"Stab";
$Disziplinen[520]['Typ']	=	"h";

$Disziplinen[530]['Bez']	=	"Weitsprung";
$Disziplinen[530]['Kurz']	=	"Weit";
$Disziplinen[530]['Typ']	=	"t";

$Disziplinen[535]['Bez']	=	"Standweitsprung";
$Disziplinen[535]['Kurz']	=	"StWeit";
$Disziplinen[535]['Typ']	=	"t";

$Disziplinen[540]['Bez']	=	"Dreisprung";
$Disziplinen[540]['Kurz']	=	"Drei";
$Disziplinen[540]['Typ']	=	"t";

$Disziplinen[610]['Bez']	=	"Kugelstoß";
$Disziplinen[610]['Kurz']	=	"Kugel";
$Disziplinen[610]['Typ']	=	"t";

$Disziplinen[611]['Bez']	=	"Stein";
$Disziplinen[611]['Kurz']	=	"Stein";
$Disziplinen[611]['Typ']	=	"t";

$Disziplinen[612]['Bez']	=	"Kugelstoß Rollstuhl"; #
$Disziplinen[612]['Kurz']	=	"Kugel Roll."; #
$Disziplinen[612]['Typ']	=	"t"; #

$Disziplinen[620]['Bez']	=	"Diskuswurf";
$Disziplinen[620]['Kurz']	=	"Diskus";
$Disziplinen[620]['Typ']	=	"t";

$Disziplinen[621]['Bez']	=	"Diskuswurf Rollstuhl"; #
$Disziplinen[621]['Kurz']	=	"Diskus Roll."; #
$Disziplinen[621]['Typ']	=	"t"; #

$Disziplinen[630]['Bez']	=	"Hammerwurf";
$Disziplinen[630]['Kurz']	=	"Hammer";
$Disziplinen[630]['Typ']	=	"t";

$Disziplinen[640]['Bez']	=	"Speerwurf";
$Disziplinen[640]['Kurz']	=	"Speer";
$Disziplinen[640]['Typ']	=	"t";

$Disziplinen[641]['Bez']	=	"Speerwurf Rollstuhl"; #
$Disziplinen[641]['Kurz']	=	"Speer Roll."; #
$Disziplinen[641]['Typ']	=	"t"; #

$Disziplinen[644]['Bez']	=	"Keulenwurf 397g";
$Disziplinen[644]['Kurz']	=	"Keule";
$Disziplinen[644]['Typ']	=	"t";

$Disziplinen[650]['Bez']	=	"Ballwurf 200g";
$Disziplinen[650]['Kurz']	=	"Ball";
$Disziplinen[650]['Typ']	=	"t";

$Disziplinen[660]['Bez']	=	"Schlagball 80g";
$Disziplinen[660]['Kurz']	=	"S.-Ball";
$Disziplinen[660]['Typ']	=	"t";

$Disziplinen[670]['Bez']	=	"Schleuderball";
$Disziplinen[670]['Kurz']	=	"SchleuB";
$Disziplinen[670]['Typ']	=	"t";

$Disziplinen[690]['Bez']	=	"Gewicht";
$Disziplinen[690]['Kurz']	=	"Gewicht";
$Disziplinen[690]['Typ']	=	"t";

$Disziplinen[710]['Bez']	=	"3-Kampf";
$Disziplinen[710]['Kurz']	=	"3-Kampf";
$Disziplinen[710]['Typ']	=	"m";

$Disziplinen[711]['Bez']	=	"3-Kampf Ma.";
$Disziplinen[711]['Kurz']	=	"3-Kampf Ma.";
$Disziplinen[711]['Typ']	=	"b";

$Disziplinen[720]['Bez']	=	"4-Kampf";
$Disziplinen[720]['Kurz']	=	"4-Kampf";
$Disziplinen[720]['Typ']	=	"m";

$Disziplinen[721]['Bez']	=	"4-Kampf Ma.";
$Disziplinen[721]['Kurz']	=	"4-Kampf Ma.";
$Disziplinen[721]['Typ']	=	"b";

$Disziplinen[730]['Bez']	=	"5-Kampf";
$Disziplinen[730]['Kurz']	=	"5-Kampf";
$Disziplinen[730]['Typ']	=	"m";

$Disziplinen[731]['Bez']	=	"5-Kampf Ma.";
$Disziplinen[731]['Kurz']	=	"5-Kampf Ma.";
$Disziplinen[731]['Typ']	=	"b";

$Disziplinen[740]['Bez']	=	"6-Kampf";
$Disziplinen[740]['Kurz']	=	"6-Kampf";
$Disziplinen[740]['Typ']	=	"m";

$Disziplinen[741]['Bez']	=	"6-Kampf Ma.";
$Disziplinen[741]['Kurz']	=	"6-Kampf Ma.";
$Disziplinen[741]['Typ']	=	"b";

$Disziplinen[750]['Bez']	=	"7-Kampf";
$Disziplinen[750]['Kurz']	=	"7-Kampf";
$Disziplinen[750]['Typ']	=	"m";

$Disziplinen[751]['Bez']	=	"7-Kampf Ma.";
$Disziplinen[751]['Kurz']	=	"7-Kampf Ma.";
$Disziplinen[751]['Typ']	=	"b";

$Disziplinen[765]['Bez']	=	"9-Kampf";
$Disziplinen[765]['Kurz']	=	"9-Kampf";
$Disziplinen[765]['Typ']	=	"m";

$Disziplinen[766]['Bez']	=	"9-Kampf Ma.";
$Disziplinen[766]['Kurz']	=	"9-Kampf Ma.";
$Disziplinen[766]['Typ']	=	"b";

$Disziplinen[770]['Bez']	=	"10-Kampf";
$Disziplinen[770]['Kurz']	=	"10-Kampf";
$Disziplinen[770]['Typ']	=	"m";

$Disziplinen[771]['Bez']	=	"10-Kampf Ma.";
$Disziplinen[771]['Kurz']	=	"10-Kampf Ma.";
$Disziplinen[771]['Typ']	=	"b";

$Disziplinen[790]['Bez']	=	"Wurf-Fünfkampf";
$Disziplinen[790]['Kurz']	=	"Wurf-5K";
$Disziplinen[790]['Typ']	=	"m";

$Disziplinen[791]['Bez']	=	"Wurf-Fünfkampf Ma.";
$Disziplinen[791]['Kurz']	=	"Wurf-5K Ma.";
$Disziplinen[791]['Typ']	=	"b";

$Disziplinen[785]['Bez']	=	"Block Basis";
$Disziplinen[785]['Kurz']	=	"Block Basis";
$Disziplinen[785]['Typ']	=	"m";

$Disziplinen[780]['Bez']	=	"Block S/S";
$Disziplinen[780]['Kurz']	=	"Block SS";
$Disziplinen[780]['Typ']	=	"m";

$Disziplinen[782]['Bez']	=	"Block Lauf";
$Disziplinen[782]['Kurz']	=	"Block L";
$Disziplinen[782]['Typ']	=	"m";

$Disziplinen[784]['Bez']	=	"Block Wurf";
$Disziplinen[784]['Kurz']	=	"Block W";
$Disziplinen[784]['Typ']	=	"m";

$Disziplinen[788]['Bez']	=	"Block Mannschaft";
$Disziplinen[788]['Kurz']	=	"Block Ma.";
$Disziplinen[788]['Typ']	=	"m";

$Disziplinen[801]['Bez']	=	"DMM Gruppe 1";
$Disziplinen[801]['Kurz']	=	"DMM G1";
$Disziplinen[801]['Typ']	=	"d";

$Disziplinen[803]['Bez']	=	"DMM Gruppe 2";
$Disziplinen[803]['Kurz']	=	"DMM G2";
$Disziplinen[803]['Typ']	=	"d";

$Disziplinen[805]['Bez']	=	"DMM Gruppe 3";
$Disziplinen[805]['Kurz']	=	"DMM G3";
$Disziplinen[805]['Typ']	=	"d";

$Disziplinen[816]['Bez']	=	"Gruppe 1";
$Disziplinen[816]['Kurz']	=	"Gruppe 1";
$Disziplinen[816]['Typ']	=	"d";

$Disziplinen[817]['Bez']	=	"Gruppe 2";
$Disziplinen[817]['Kurz']	=	"Gruppe 2";
$Disziplinen[817]['Typ']	=	"d";

$Disziplinen[818]['Bez']	=	"Gruppe 3";
$Disziplinen[818]['Kurz']	=	"Gruppe 3";
$Disziplinen[818]['Typ']	=	"d";

$Disziplinen[819]['Bez']	=	"Gruppe 4";
$Disziplinen[819]['Kurz']	=	"Gruppe 4";
$Disziplinen[819]['Typ']	=	"d";

$Disziplinen[824]['Bez']	=	"M70 DAMM";
$Disziplinen[824]['Kurz']	=	"M70 DAMM";
$Disziplinen[824]['Typ']	=	"d";

$Disziplinen[828]['Bez']	=	"W60 DAMM";
$Disziplinen[828]['Kurz']	=	"W60 DAMM";
$Disziplinen[828]['Typ']	=	"d";

$Disziplinen[840]['Bez']	=	"I JtfO";
$Disziplinen[840]['Kurz']	=	"I JtfO";
$Disziplinen[840]['Typ']	=	"j";

$Disziplinen[841]['Bez']	=	"II JtfO";
$Disziplinen[841]['Kurz']	=	"II JtfO";
$Disziplinen[841]['Typ']	=	"j";

$Disziplinen[843]['Bez']	=	"III JtfO";
$Disziplinen[843]['Kurz']	=	"III JtfO";
$Disziplinen[844]['Typ']	=	"j";

$Disziplinen[844]['Bez']	=	"IV JtfO";
$Disziplinen[844]['Kurz']	=	"IV JtfO";
$Disziplinen[844]['Typ']	=	"j";

$Disziplinen[845]['Bez']	=	"IV/1JtfO";
$Disziplinen[845]['Kurz']	=	"IV/1JtfO";
$Disziplinen[845]['Typ']	=	"j";

$Disziplinen[846]['Bez']	=	"IV/2JtfO";
$Disziplinen[846]['Kurz']	=	"IV/2JtfO";
$Disziplinen[846]['Typ']	=	"j";

$Disziplinen[0]['Bez']		=	"Eigener Wettbewerb";
$Disziplinen[0]['Kurz']		=	"Eig. Wb.";
$Disziplinen[0]['Typ']		=	"e";


# --- Rundentypen -------------------Rounds----------------------------

$RundeTyp0 = "Finale";
$RundeTyp1 = "Vorläufe";
$RundeTyp2 = "Zwischenläufe";
$RundeTyp3 = "Zeitvorläufe";

$RundeTyp99 = " ";

$RundeTyp4 = "Ausscheidung";
$RundeTyp5 = "Qualifikation"; 
$RundeTyp6 = "Zeitläufe";
$RundeTyp7 = "A-/B-Finale";

$RundeTyp8 = "nur Lauf-Nr.";
# belegt für MK 9

$RundeTypa = "nach 1 Disz.";
$RundeTypb = "nach 2 Disz.";
$RundeTypc = "nach 3 Disz.";
$RundeTypd = "nach 4 Disz.";
$RundeType = "nach 5 Disz.";
$RundeTypf = "nach 6 Disz.";
$RundeTypg = "nach 7 Disz.";
$RundeTyph = "nach 8 Disz.";
$RundeTypi = "nach 9 Disz.";

# Abbrev for Types
$RoundTypAbbrev[0]	= "F";
$RoundTypAbbrev[1]	= "V";
$RoundTypAbbrev[2]	= "Z";
$RoundTypAbbrev[3]	= "ZV";
$RoundTypAbbrev[4]	= "A";
$RoundTypAbbrev[5]	= "Q";
$RoundTypAbbrev[6]	= "ZF";
$RoundTypAbbrev[7]	= "A/B";
$RoundTypAbbrev[8]	= "EiL";
$RoundTypAbbrev[99]	= "";

# --- Typ-Typen ---------------------Typs---------------------------

$TypTyp1 = "Ergebnisliste";
$TypTyp2 = "Teilnehmerliste";
$TypTyp3 = "Zwischenergebnisliste";
$TypTyp4 = "Startliste";
$TypTyp5 = "";
$TypTyp6 = "Qualif. aus Vorläufen";
$TypTyp7 = "Qualif. aus Zwischenl.";
$TypTyp8 = "Disziplin beendet";

$TypTyp[99] = "Pokalwertung";

# --- Typ-Typen Abkürzungen ---------------------Typs Abbrev ------
$ListTypAbbrev[1] = "&nbsp;E&nbsp;";
$ListTypAbbrev[2] = "&nbsp;T&nbsp;";
$ListTypAbbrev[3] = "&nbsp;Z&nbsp;";
$ListTypAbbrev[4] = "&nbsp;S&nbsp;";
$ListTypAbbrev[5] = "";
$ListTypAbbrev[6] = "";
$ListTypAbbrev[7] = "";
$ListTypAbbrev[8] = "";
$ListTypAbbrev[99] = "&nbsp;P&nbsp;"; # Pokal/Cup


# --- arabische in römische Zahlen--------------------------

$Roemisch[1] = "I";
$Roemisch[2] = "II";
$Roemisch[3] = "III";
$Roemisch[4] = "IV";
$Roemisch[5] = "V";
$Roemisch[6] = "VI";
$Roemisch[7] = "VII";
$Roemisch[8] = "VIII";
$Roemisch[9] = "IX";
$Roemisch[10] = "X";
$Roemisch[11] = "XI";
$Roemisch[12] = "XII";
$Roemisch[13] = "XIII";
$Roemisch[14] = "XIV";
$Roemisch[15] = "XV";
$Roemisch[16] = "XVI";
$Roemisch[17] = "XVII";
$Roemisch[18] = "XVIII";
$Roemisch[19] = "IX";
$Roemisch[20] = "XX";

# --- Wochentage --------------------------
$Wochentage[0] = "Sonntag";
$Wochentage[1] = "Montag";
$Wochentage[2] = "Dienstag";
$Wochentage[3] = "Mittwoch";
$Wochentage[4] = "Donnerstag";
$Wochentage[5] = "Freitag";
$Wochentage[6] = "Samstag";

$LengthForAbbrevDaysOfWeek = 2;

break; 

case "en":	# English Team Thomas !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

$txt_laive = "LaIVE";
$TitleHTMLInfo 	= "(athletics live results)";
$txt_vom = "";
$txt_am = "";
$txt_in = "in";
$txt_uebersicht = "summary";
$txt_zeitplan = "schedule";
$txt_stellplatzzeitplan = "schedule for final confirmation";
$txt_gesamtteilnehmerliste = "all athletes and teams";
$txt_gesamtteilnehmerlistenachverein = "entry list by countries/clubs";
$txt_gesamtteilnehmerlistenachwettbewerben = "entry list by events";
$txt_tag = "day";
$txt_kopf_zeitplanaktualisiert ="schedule updated:";
$text_hinweissortierunguebersicht = "Click column header for sorting.";

$txt_klasse = "Categorie";
$txt_disziplin = "event";
$txt_runde = "round"; 
$txt_typ = "type";
$txt_aktualisiert = "upd.";
$txt_startzeit = "time";
$txt_wettbewerb = "event";
$txt_anzahlrunden = "rounds";
$txt_zeit = "latest<br>confirmation time";
$txt_meldungen = "entries";
$txt_wbnr = "event No.";
$txt_IPCClassesName = "start class";
$txt_headline_abbrev_participansandteams = "P/T";
$txt_headline_explanation_participansandteams = "Number of participants or teams in each round of an event";
$txt_headline_abbrev_heatsandgroups = "H/G";
$txt_headline_explanation_heatsandgroups = "Number of heats or groups in each round of an event";

$txt_startnummer = "BIB";
$txt_name = "Name";
$txt_geschlecht = "m/w";
$txt_jahrgang = "YoB";
$txt_lv = "NAT";
$txt_verein = "Country/Club";
$txt_gemeldetewettbewerbe = "Entries for the following events";

$txt_stellplatzabgabe = "Final confirmation possible until final confirmation time for each event.";
$txt_anzahlrunden = "Rounds";
$txt_anzahlrunden_keine = "No Entry list or Result list available.";
$txt_anzahlwettbewerbestellplatz = "Events with final confirmation";
$txt_zwischenergebnisse = "Intermediate Results";
$txt_finale_ergebnisse = "Final (Result list)";
$txt_zwischenlaeufe_ergebnisse = "Semi-Finals (Result list)";
$txtIPCMode = "IPC";
$txt_laivefuss1 = "$txt_laive (Version: $ResultTickerVersion - $ResultTickerErsteller - <a href='http://kwenzel.net/Special_Contact' target='_blank'>laive@kwenzel.net</a>)";
$txt_laivefuss2 = "Data handling by COSA WIN.";

$txt_meta_beschreibung = "Live data for the following Athletics competition:";

# Format ----------------------------------------
$MarkSeperator1 = ".";

# Allgemein ----------------------------------------
$TxtMixedEvent = "(mixed event)";
$TxtCombinedEventGroup = "group";
$TxtHeat = "heat";
$TxtGroup = "group";
$TxtAbrrevGenderMale = "m";
$TxtAbrrevGenderMan = "M";
$TxtAbrrevGenderFemale = "w";
$TxtAbrrevGenderWoman = "W";
$TxtAbbrevOutOfRanking = "no rank.";
$TxtParticipant = "athlete";
$TxtParticipants = "athletes";
$TxtAbbrevParticipant = "athl.";
$TxtAbbrevParticipants = "athl.";
$TxtAbbrevRelayTeam = "team";
$TxtAbbrevRelayTeams = "teams";
$TxtDaytimeUnit = "";
$txtMenuStartlistsAll = "All start lists";


# Index.php ----------------------------------------
$LinksSubMenuResultsFinal = "final (Result list)";
$LinksSubMenuResultsSemifinals = "semi-finals (Result list)";
$LinksSubMenuResultsTimedHeats = "time heats (Result list)";
$LinksSubMenuResultsHeats = "heats (Result list)";
$LinksSubMenuResultsCombinedEventAfter9Events = "after 9 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter8Events = "after 8 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter7Events = "after 7 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter6Events = "after 6 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter5Events = "after 5 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter4Events = "after 4 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter3Events = "after 3 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter2Events = "after 2 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter1Event = "after 1 event (Res.)";
$LinksSubMenuStartlistFinalTrack = "final (Start list)";
$LinksSubMenuStartlistFinalHJ = "final (Start list)";
$LinksSubMenuStartlistFinalField = "final (Start list)";
$LinksSubMenuStartlistFinalTrackAB = "finals A/B (Start list)";
$LinksSubMenuStartlistTimeRace = "time-races (Start list)";
$LinksSubMenuStartlistSemifinals = "semi-finals (Start list)";
$LinksSubMenuStartlistTimedHeats = "time heats (Start list)";
$LinksSubMenuStartlistHeats = "heats (Start list)";
$LinksSubMenuStartlistEliminationTrack = "qualification heat (Start list)";
$LinksSubMenuStartlistEliminationField = "elimination (Start list)";
$LinksSubMenuStartlistQualification = "qualification (Start list)";
$LinksSubMenuStartlistOnlyHeatNumber = "heat (Start list)";
$LinksSubMenuParticipantslist = "Entry list";
$TxtSubMenuUpdated = "updated:";
$TxtFooterSiteLoadedIn = "Site loaded in";
$TxtFooterSiteLoadedInUnit = "s";
$txtMenuEntriesAll = "All entries";
$txtMenuEntriesByEvent ="Entries by events";
$txtMenuEntriesByClub ="Entries by countries/clubs";

$LinksSubMenuResultsEliminationTrack = "qualification heat (Result list)";
$LinksSubMenuResultsTimeRace = "time-races (Result list)";
$LinksSubMenuResultsFinalTrackAB = "final A/B (Result list)";
$LinksSubMenuResultsOnlyHeatNumber = "heat (Result list)";
$LinksSubMenuResultsFinalHJ = "final (Result list)";
$LinksSubMenuResultsFinalField = "final (Result list)";
$LinksSubMenuResultsEliminationField = "elimination (Result list)";
$LinksSubMenuResultsQualification = "qualification (Result list)";

# zeitplan.php ----------------------------------------


$TxtHeats = "heats";

$TxtGroups = "groups";
$TxtLinkEntriesByClubs = "Entries by countries/clubs";




$TxtEntry = "entry";
$TxtEntries = "entries";
$TxtNoEntry = "0";

$TxtHeadKey = "Key";


$TxtTTBoardPage = "Page";
$TxtTTBoardDownload = "Download Timetable for Score Board";

# startlistenerstellen.php ----------------------------------------
$TxtStartlistHeadline = "Start list";

$TxtSeasonBest = "Personal best";
$TxtQualificationMark = "Qualification<br>mark";

$TxtAbbrevOrdner = "Ord.";
$TxtAbbrevBIB = "BIB";
$TxtAbbrevBIBRelay = "BIB";
$TxtAthleteName = "Name";
$TxtAbbrevJOB = "YOB";
$TxtAbbrevNation = "NAT";
$TxtClub = "Country/Club";
$TxtRelayTeam = "Team";
$TxtRelayMembers = "Relay members";
$TxtQualificationHeat = "heat";
$TxtQualificationHeats = "heats";
$TxtQualificationTimedHeat = "heat";
$TxtQualificationTimedHeats = "heats";
$TxtQualificationSemiFinal = "semi-final";
$TxtQualificationSemiFinals = "semi-finals";
$TxtQualificationByPlaceTrack1 = "first in each heat (Q)";
$TxtQualificationByPlaceTrackMore = "first in each heat (Q)";
$TxtQualificationByTimeTrack1 = "fastest (q)";
$TxtQualificationByTimeTrackMore = "fastest (q)";
$TxtAt = "at";

$TxtOnThe = "on the";
$TxtQualificationToFinal = "final";
$TxtQualificationToFinalsNotEqual = "finals (not equal)";
$TxtQualificationToFinalsEqual = "finals (equal)";
$TxtQualificationToFinal2 = "the final";
$TxtQualificationToFinal3 = "the final";
$TxtQualificationToSemiFinal = "semi-final";
$TxtQualificationToSemiFinals = "semi-finals";
$TxtQualificationWordsBetweenPlaceAndTime = "and";
$TxtQualificationFrom = "From";
$TxtQualificationAdvancedTo = "advanced";
$TxtQualificationToWord = "to";
$TxtQualificationHeadline = "Qualification";
$TxtHJHeightsHeadline = "Heights";

$TxtStartlistHeadlineAll = "Start lists of all events and rounds";

# create_entrylists.php ----------------------------------------
$TxtEntrylistHeadline = "Entry list";
$TxtFinalConfirmation[0]['Abbrev'] 		= "&#10008;";
$TxtFinalConfirmation[0]['Explanation'] = "Participation not confirmed";
$TxtFinalConfirmation[1]['Abbrev'] 		= "&#10004;";
$TxtFinalConfirmation[1]['Explanation'] = "Participation finaly confirmed";

# gesamtteilnehmer.php ----------------------------------------
$TxtLinkSubMenuEntriesList1 = "All athletes and teams";
$TxtLinkSubMenuEntriesList2 = "Entries by events";
$TxtLinkSubMenuEntriesList3 = "Entries by countries/clubs";
$TxtEvaluationGroupsHeadline = "Evaluation groups";
$TxtEvaluationGroupsNoGroup = "No evaluation group selected";
$TxtAbbrevLateEntry = "late entry";
$TxtLateEntry = "late entry";
$TxtAnd = "and";
$TxtRelayTeam = "Relay team";
$TxtRelayTeams = "Relay teams";
$TxtSortedByHeadline = "Sorted by";
$TxtSortedByBIB = "BIB Numbers";
$TxtSortedByName = "Names";
$TxtSortedBySeasonBest = "Personal bests";
$TxtSortedByIPCClass = "Start class";
$TxtSummaryOfClasses = "Summary";
$TxtEntriesByEventsHeats = "Heats";
$TxtEntriesByEventsSemiFinals = "Semi-Finals";
$TxtEntriesByEventsFinal = "Final";
$TxtEntriesByEventsCombinedEventFirstEvent = "Start of 1<sup>st</sup> event";
$TxtClubs = "Countries/Clubs";
$TxtIPCClass = "Start cl.";
$TxtSDMSID = "SDMS-ID";
$TxtAthletesLicenceIDShowHead = "Show athlete's licence ID";
$TxtAthletesLicenceIDDontShowHead = "Do not show athlete's licence ID";
$TxtAthletesLicenceID = "Athlete's licence ID";
$TxtAthletesOnlineID = "Athlete's online ID";

# uebersicht.php ----------------------------------------
$TxtLinkJustRL = "Just show Result lists";

# cupscoring.php ----------------------------------------
$txt_headline_cupscoring = "Cup scoring";
$txt_cup_standingafter_1 = "Standing after";
$txt_cup_standingafter_2 = "of";
$txt_cup_standingafter_3 = "events";
$cup_tablehead_place	= "Place"; 
$cup_tablehead_team		= "Team";
$cup_tablehead_points 	= "Points";
$cup_afterplace 		= ".";
$txt_cup_includedevents = "Following events are included";
$txt_cup_detailedScoring = "Detailed scoring by event";
$txt_cup_scoringbased  = "Cup scoring is based on the following rules";
$txt_cup_Nb_ScoredAthletes = "Number of scored athletes per team and event";
$txt_cup_Nb_ScoredPlaces = "Number of scored places in event";
$txt_cup_Nb_Points = "Points (starting by 1st Place)";
$txt_cup_Nb_ScoredRelays = "Number of scored relay teams per team and event";
$txt_cup_Nb_ScoredPlacesRelays = "Number of scored places in relay event";
$txt_cup_Nb_PointsRelays = "Points for relays (starting by 1st Place)";
$txt_cup_NoCombinedScoring = "No combined cup scoring available.";

$txt_headline_combined_cupscoring 	= "Combined cup scoring";
$txt_combined_cup_detailedScoring 	= "Points (and Places) by cups";
$txt_combined_cup_includedcups		= "Following cups are included";
$txt_cup_Combined_standingafter_1	= "of";
$txt_cup_Combined_standingafter_2	= "events finished";

### Noch ergänzen ###


$competitionsubname	="Competition website";
$resultname			="Result list";								#Bezeichnung Ergebnisse
$resultname1		="Result list day 1";						#Bezeichnung Ergebnisse Tag 1
$resultname2		="Result list day 2";						#Bezeichnung Ergebnisse Tag 2

# --- Dateien ------ Files -----------------------------------------------

# Module ---- Modules
$dat_index = "index.php";
$dat_uebersicht = "uebersicht.php";
$dat_zeitplan = "zeitplan.php";
$dat_stellplatzzeitplan = "stellplatzzeitplan.php";



# COSA-Dateien --- COSA Files
$dat_vandat 	= "./laive_vandat.c01";
$dat_wettbew 	= "./laive_Wettbew.c01";
$dat_wbteiln 	= "./laive_WbTeiln.c01";
$dat_endli 		= "./laive_Endli.c01";
$dat_wklist 	= "./laive_WkList.c01";

$dat_stamm 		= "./laive_Stamm.c01";
$dat_verein 	= "./laive_Verein.c01";



# Zusatzdateien --- Extended Files
$entryfile="_teilnehmer.htm";                 						#Name der Teilnehmerdatei
$entrybyclubfile="_teilnehmerverein.pdf";							#Name der Datei - TN nach Vereinen
$resultfile="_ergebnisse.htm";                						#Name der Ergebnisdatei
$resultfile1="_ergebnisse1.htm";                					#Name der Ergebnisdatei Tag 1
$resultfile2="_ergebnisse2.htm";                					#Name der Ergebnisdatei Tag 2

$competitionsublink="http://";	# competitionsub /Veranstaltungsseite



$Klassen[10]['Nr']		=	10;
$Klassen[10]['Bez']		=	"men";
$Klassen[10]['Ident']	=	"Mä";
$Klassen[10]['Abbrev']	=	"m";

$Klassen[11]['Nr']		=	11;
$Klassen[11]['Bez']		=	"women";
$Klassen[11]['Ident']	=	"Fr";
$Klassen[11]['Abbrev']	=	"w";

$Klassen[12]['Nr']		=	12;
$Klassen[12]['Bez']		=	"men U23";
$Klassen[12]['Ident']	=	"Ju";
$Klassen[12]['Abbrev']	=	"m U23";

$Klassen[13]['Nr']		=	13;
$Klassen[13]['Bez']		=	"women U23";
$Klassen[13]['Ident']	=	"Jui";
$Klassen[13]['Abbrev']	=	"w U23";

$Klassen[20]['Nr']		=	20;
$Klassen[20]['Bez']		=	"men U20";
$Klassen[20]['Ident']	=	"MJA";
$Klassen[20]['Abbrev']	=	"m U20";

$Klassen[21]['Nr']		=	21;
$Klassen[21]['Bez']		=	"women U20";
$Klassen[21]['Ident']	=	"WJA";
$Klassen[21]['Abbrev']	=	"w U20";

$Klassen[22]['Nr']		=	22;
$Klassen[22]['Bez']		=	"men U18";
$Klassen[22]['Ident']	=	"MJB";
$Klassen[22]['Abbrev']	=	"m U18";

$Klassen[23]['Nr']		=	23;
$Klassen[23]['Bez']		=	"women U18";
$Klassen[23]['Ident']	=	"WJB";
$Klassen[23]['Abbrev']	=	"w U18";

$Klassen[37]['Nr']		=	37;
$Klassen[37]['Bez']		=	"men U16";
$Klassen[37]['Ident']	=	"SA";
$Klassen[37]['Abbrev']	=	"m U16";

$Klassen[38]['Nr']		=	38;
$Klassen[38]['Bez']		=	"M14";
$Klassen[38]['Ident']	=	"M14";
$Klassen[38]['Abbrev']	=	"M14";

$Klassen[39]['Nr']		=	39;
$Klassen[39]['Bez']		=	"M15";
$Klassen[39]['Ident']	=	"M15";
$Klassen[39]['Abbrev']	=	"M15";

$Klassen[34]['Nr']		=	34;
$Klassen[34]['Bez']		=	"men U14";
$Klassen[34]['Ident']	=	"SB";
$Klassen[34]['Abbrev']	=	"m U14";

$Klassen[35]['Nr']		=	35;
$Klassen[35]['Bez']		=	"M12";
$Klassen[35]['Ident']	=	"M12";
$Klassen[35]['Abbrev']	=	"M12";

$Klassen[36]['Nr']		=	36;
$Klassen[36]['Bez']		=	"M13";
$Klassen[36]['Ident']	=	"M13";
$Klassen[36]['Abbrev']	=	"M13";

$Klassen[31]['Nr']		=	31;
$Klassen[31]['Bez']		=	"Boys U12";
$Klassen[31]['Ident']	=	"SC";
$Klassen[31]['Abbrev']	=	"B. U12";

$Klassen[32]['Nr']		=	32;
$Klassen[32]['Bez']		=	"M10";
$Klassen[32]['Ident']	=	"M10";
$Klassen[32]['Abbrev']	=	"M10";

$Klassen[33]['Nr']		=	33;
$Klassen[33]['Bez']		=	"M11";
$Klassen[33]['Ident']	=	"M11";
$Klassen[33]['Abbrev']	=	"M11";

$Klassen[28]['Nr']		=	28;
$Klassen[28]['Bez']		=	"Boys U10";
$Klassen[28]['Ident']	=	"SD";
$Klassen[28]['Abbrev']	=	"B. U10";

$Klassen[29]['Nr']		=	29;
$Klassen[29]['Bez']		=	"M 8";
$Klassen[29]['Ident']	=	"M08";
$Klassen[29]['Abbrev']	=	"M 8";

$Klassen[30]['Nr']		=	30;
$Klassen[30]['Bez']		=	"M 9";
$Klassen[30]['Ident']	=	"M09";
$Klassen[30]['Abbrev']	=	"M 9";

$Klassen[24]['Nr']		=	24;
$Klassen[24]['Bez']		=	"Boys U08";
$Klassen[24]['Ident']	=	"SE";
$Klassen[24]['Abbrev']	=	"B. U08";

$Klassen[88]['Nr']		=	88;
$Klassen[88]['Bez']		=	"M 7";
$Klassen[88]['Ident']	=	"M07";
$Klassen[88]['Abbrev']	=	"M 7";

$Klassen[86]['Nr']		=	86;
$Klassen[86]['Bez']		=	"M 6";
$Klassen[86]['Ident']	=	"M06";
$Klassen[86]['Abbrev']	=	"M 6";

$Klassen[84]['Nr']		=	84;
$Klassen[84]['Bez']		=	"M 5";
$Klassen[84]['Ident']	=	"M05";
$Klassen[84]['Abbrev']	=	"M 5";

$Klassen[82]['Nr']		=	82;
$Klassen[82]['Bez']		=	"M 4";
$Klassen[82]['Ident']	=	"M04";
$Klassen[82]['Abbrev']	=	"M 4";

$Klassen[80]['Nr']		=	80;
$Klassen[80]['Bez']		=	"M 3";
$Klassen[80]['Ident']	=	"M03";
$Klassen[80]['Abbrev']	=	"M 3";

$Klassen[47]['Nr']		=	47;
$Klassen[47]['Bez']		=	"women U16";
$Klassen[47]['Ident']	=	"SIA";
$Klassen[47]['Abbrev']	=	"w U16";

$Klassen[48]['Nr']		=	48;
$Klassen[48]['Bez']		=	"W14";
$Klassen[48]['Ident']	=	"W14";
$Klassen[48]['Abbrev']	=	"W14";

$Klassen[49]['Nr']		=	49;
$Klassen[49]['Bez']		=	"W15";
$Klassen[49]['Ident']	=	"W15";
$Klassen[49]['Abbrev']	=	"W15";

$Klassen[44]['Nr']		=	44;
$Klassen[44]['Bez']		=	"women U14";
$Klassen[44]['Ident']	=	"SIB";
$Klassen[44]['Abbrev']	=	"w U14";

$Klassen[45]['Nr']		=	45;
$Klassen[45]['Bez']		=	"W12";
$Klassen[45]['Ident']	=	"W12";
$Klassen[45]['Abbrev']	=	"W12";

$Klassen[46]['Nr']		=	46;
$Klassen[46]['Bez']		=	"W13";
$Klassen[46]['Ident']	=	"W13";
$Klassen[46]['Abbrev']	=	"W13";

$Klassen[27]['Nr']		=	27;
$Klassen[27]['Bez']		=	"Girls U12";
$Klassen[27]['Ident']	=	"SIC";
$Klassen[27]['Abbrev']	=	"G. U12";

$Klassen[42]['Nr']		=	42;
$Klassen[42]['Bez']		=	"W10";
$Klassen[42]['Ident']	=	"W10";
$Klassen[42]['Abbrev']	=	"W10";

$Klassen[43]['Nr']		=	43;
$Klassen[43]['Bez']		=	"W11";
$Klassen[43]['Ident']	=	"W11";
$Klassen[43]['Abbrev']	=	"W11";

$Klassen[26]['Nr']		=	26;
$Klassen[26]['Bez']		=	"Girls U10";
$Klassen[26]['Ident']	=	"SID";
$Klassen[26]['Abbrev']	=	"G. U10";

$Klassen[40]['Nr']		=	40;
$Klassen[40]['Bez']		=	"W 8";
$Klassen[40]['Ident']	=	"W08";
$Klassen[40]['Abbrev']	=	"W 8";

$Klassen[41]['Nr']		=	41;
$Klassen[41]['Bez']		=	"W 9";
$Klassen[41]['Ident']	=	"W09";
$Klassen[41]['Abbrev']	=	"W 9";

$Klassen[25]['Nr']		=	25;
$Klassen[25]['Bez']		=	"Girls U08";
$Klassen[25]['Ident']	=	"SIE";
$Klassen[25]['Abbrev']	=	"G. U08";

$Klassen[89]['Nr']		=	89;
$Klassen[89]['Bez']		=	"W 7";
$Klassen[89]['Ident']	=	"W07";
$Klassen[89]['Abbrev']	=	"W 7";

$Klassen[87]['Nr']		=	87;
$Klassen[87]['Bez']		=	"W 6";
$Klassen[87]['Ident']	=	"W06";
$Klassen[87]['Abbrev']	=	"W 6";

$Klassen[85]['Nr']		=	85;
$Klassen[85]['Bez']		=	"W 5";
$Klassen[85]['Ident']	=	"W05";
$Klassen[85]['Abbrev']	=	"W 5";

$Klassen[83]['Nr']		=	83;
$Klassen[83]['Bez']		=	"W 4";
$Klassen[83]['Ident']	=	"W04";
$Klassen[83]['Abbrev']	=	"W 4";

$Klassen[81]['Nr']		=	81;
$Klassen[81]['Bez']		=	"W 3";
$Klassen[81]['Ident']	=	"W03";
$Klassen[81]['Abbrev']	=	"W 3";

$Klassen[50]['Nr']		=	50;
$Klassen[50]['Bez']		=	"M30";
$Klassen[50]['Ident']	=	"M30";
$Klassen[50]['Abbrev']	=	"M30";

$Klassen[51]['Nr']		=	51;
$Klassen[51]['Bez']		=	"W30";
$Klassen[51]['Ident']	=	"W30";
$Klassen[51]['Abbrev']	=	"W30";

$Klassen[52]['Nr']		=	52;
$Klassen[52]['Bez']		=	"M35";
$Klassen[52]['Ident']	=	"M35";
$Klassen[52]['Abbrev']	=	"M35";

$Klassen[53]['Nr']		=	53;
$Klassen[53]['Bez']		=	"W35";
$Klassen[53]['Ident']	=	"W35";
$Klassen[53]['Abbrev']	=	"W35";

$Klassen[54]['Nr']		=	54;
$Klassen[54]['Bez']		=	"M40";
$Klassen[54]['Ident']	=	"M40";
$Klassen[54]['Abbrev']	=	"M40";

$Klassen[55]['Nr']		=	55;
$Klassen[55]['Bez']		=	"W40";
$Klassen[55]['Ident']	=	"W40";
$Klassen[55]['Abbrev']	=	"W40";

$Klassen[56]['Nr']		=	56;
$Klassen[56]['Bez']		=	"M45";
$Klassen[56]['Ident']	=	"M45";
$Klassen[56]['Abbrev']	=	"M45";

$Klassen[57]['Nr']		=	57;
$Klassen[57]['Bez']		=	"W45";
$Klassen[57]['Ident']	=	"W45";
$Klassen[57]['Abbrev']	=	"W45";

$Klassen[58]['Nr']		=	58;
$Klassen[58]['Bez']		=	"M50";
$Klassen[58]['Ident']	=	"M50";
$Klassen[58]['Abbrev']	=	"M50";

$Klassen[59]['Nr']		=	59;
$Klassen[59]['Bez']		=	"W50";
$Klassen[59]['Ident']	=	"W50";
$Klassen[59]['Abbrev']	=	"W50";

$Klassen[60]['Nr']		=	60;
$Klassen[60]['Bez']		=	"M55";
$Klassen[60]['Ident']	=	"M55";
$Klassen[60]['Abbrev']	=	"M55";

$Klassen[61]['Nr']		=	61;
$Klassen[61]['Bez']		=	"W55";
$Klassen[61]['Ident']	=	"W55";
$Klassen[61]['Abbrev']	=	"W55";

$Klassen[62]['Nr']		=	62;
$Klassen[62]['Bez']		=	"M60";
$Klassen[62]['Ident']	=	"M60";
$Klassen[62]['Abbrev']	=	"M60";

$Klassen[63]['Nr']		=	63;
$Klassen[63]['Bez']		=	"W60";
$Klassen[63]['Ident']	=	"W60";
$Klassen[63]['Abbrev']	=	"W60";

$Klassen[64]['Nr']		=	64;
$Klassen[64]['Bez']		=	"M65";
$Klassen[64]['Ident']	=	"M65";
$Klassen[64]['Abbrev']	=	"M65";

$Klassen[65]['Nr']		=	65;
$Klassen[65]['Bez']		=	"W65";
$Klassen[65]['Ident']	=	"W65";
$Klassen[65]['Abbrev']	=	"W65";

$Klassen[66]['Nr']		=	66;
$Klassen[66]['Bez']		=	"M70";
$Klassen[66]['Ident']	=	"M70";
$Klassen[66]['Abbrev']	=	"M70";

$Klassen[67]['Nr']		=	67;
$Klassen[67]['Bez']		=	"W70";
$Klassen[67]['Ident']	=	"W70";
$Klassen[67]['Abbrev']	=	"W70";

$Klassen[68]['Nr']		=	68;
$Klassen[68]['Bez']		=	"M75";
$Klassen[68]['Ident']	=	"M75";
$Klassen[68]['Abbrev']	=	"M75";

$Klassen[69]['Nr']		=	69;
$Klassen[69]['Bez']		=	"W75";
$Klassen[69]['Ident']	=	"W75";
$Klassen[69]['Abbrev']	=	"W75";

$Klassen[70]['Nr']		=	70;
$Klassen[70]['Bez']		=	"M80";
$Klassen[70]['Ident']	=	"M80";
$Klassen[70]['Abbrev']	=	"M80";

$Klassen[71]['Nr']		=	71;
$Klassen[71]['Bez']		=	"W80";
$Klassen[71]['Ident']	=	"W80";
$Klassen[71]['Abbrev']	=	"W80";

$Klassen[72]['Nr']		=	72;
$Klassen[72]['Bez']		=	"M85";
$Klassen[72]['Ident']	=	"M85";
$Klassen[72]['Abbrev']	=	"M85";

$Klassen[73]['Nr']		=	73;
$Klassen[73]['Bez']		=	"W85";
$Klassen[73]['Ident']	=	"W85";
$Klassen[73]['Abbrev']	=	"W85";

$Klassen[99]['Nr']		=	99;
$Klassen[99]['Bez']		=	"all categories";
$Klassen[99]['Ident']	=	"all categories";
$Klassen[99]['Abbrev']	=	"all";

# --- Disziplinen-Array ------------------------------------------------------

$Disziplinen[6]['Bez']			=	"30m";
$Disziplinen[6]['Kurz']			=	"30m";
$Disziplinen[6]['Typ']			=	"l";

$Disziplinen[10]['Bez']			=	"50 m";
$Disziplinen[10]['Typ']			=	"l";
$Disziplinen[10]['Kurz']		=	"50m";

$Disziplinen[15]['Bez']			=	"60 m";
$Disziplinen[15]['Kurz']		=	"60m";
$Disziplinen[15]['Typ']			=	"l";

$Disziplinen[16]['Bez']			=	"60 m Heat 1";
$Disziplinen[16]['Kurz']		=	"60m H1";
$Disziplinen[16]['Typ']			=	"l";

$Disziplinen[17]['Bez']			=	"60 m Heat 2";
$Disziplinen[17]['Kurz']		=	"60m H2";
$Disziplinen[17]['Typ']			=	"l";

$Disziplinen[20]['Bez']			=	"75 m";
$Disziplinen[20]['Kurz']		=	"75m";
$Disziplinen[20]['Typ']			=	"l";

$Disziplinen[24]['Bez']			=	"80 m";
$Disziplinen[24]['Kurz']		=	"80m";
$Disziplinen[24]['Typ']			=	"l";

$Disziplinen[30]['Bez']			=	"100 m";
$Disziplinen[30]['Kurz']		=	"100m";
$Disziplinen[30]['Typ']			=	"l";

$Disziplinen[31]['Bez']			=	"100 m wheelchair"; #
$Disziplinen[31]['Kurz']		=	"100m wheel."; #
$Disziplinen[31]['Typ']			=	"l"; #

$Disziplinen[35]['Bez']			=	"150 m";
$Disziplinen[35]['Kurz']		=	"150m";
$Disziplinen[35]['Typ']			=	"l";

$Disziplinen[40]['Bez']			=	"200 m";
$Disziplinen[40]['Kurz']		=	"200m";
$Disziplinen[40]['Typ']			=	"l";

$Disziplinen[41]['Bez']			=	"200 m wheelchair"; #
$Disziplinen[41]['Kurz']		=	"200m wheel."; #
$Disziplinen[41]['Typ']			=	"l"; #

$Disziplinen[45]['Bez']			=	"300 m";
$Disziplinen[45]['Kurz']		=	"300m";
$Disziplinen[45]['Typ']			=	"l";

$Disziplinen[50]['Bez']			=	"400 m";
$Disziplinen[50]['Kurz']		=	"400m";
$Disziplinen[50]['Typ']			=	"l";

$Disziplinen[51]['Bez']			=	"400 m wheelchair"; #
$Disziplinen[51]['Kurz']		=	"400m wheel."; #
$Disziplinen[51]['Typ']			=	"l"; #

$Disziplinen[53]['Bez']			=	"500 m";
$Disziplinen[53]['Kurz']		=	"500m";
$Disziplinen[53]['Typ']			=	"l";

$Disziplinen[55]['Bez']			=	"600 m";
$Disziplinen[55]['Kurz']		=	"600m";
$Disziplinen[55]['Typ']			=	"l";
	
$Disziplinen[60]['Bez']			=	"800 m";
$Disziplinen[60]['Kurz']		=	"800m";
$Disziplinen[60]['Typ']			=	"l";

$Disziplinen[61]['Bez']			=	"800 m wheelchair"; #
$Disziplinen[61]['Kurz']		=	"800m wheel."; #
$Disziplinen[61]['Typ']			=	"l"; #

$Disziplinen[70]['Bez']			=	"1000 m";
$Disziplinen[70]['Kurz']		=	"1000m";
$Disziplinen[70]['Typ']			=	"l";

$Disziplinen[80]['Bez']			=	"1500 m";
$Disziplinen[80]['Kurz']		=	"1500m";
$Disziplinen[80]['Typ']			=	"l";

$Disziplinen[81]['Bez']			=	"1500 m wheelchair"; #
$Disziplinen[81]['Kurz']		=	"1500m wheel."; #
$Disziplinen[81]['Typ']			=	"l"; #

$Disziplinen[90]['Bez']			=	"1 Mile";
$Disziplinen[90]['Kurz']		=	"1 Mile";
$Disziplinen[90]['Typ']			=	"l";

$Disziplinen[100]['Bez']		=	"2000 m";
$Disziplinen[100]['Kurz']		=	"2000m";
$Disziplinen[100]['Typ']		=	"l";

$Disziplinen[110]['Bez']		=	"3000 m";
$Disziplinen[110]['Kurz']		=	"3000m";
$Disziplinen[110]['Typ']		=	"l";

$Disziplinen[111]['Bez']		=	"3000 m wheelchair";
$Disziplinen[111]['Kurz']		=	"3000m wheel.";
$Disziplinen[111]['Typ']		=	"l";

$Disziplinen[120]['Bez']		=	"5000 m";
$Disziplinen[120]['Kurz']		=	"5000m";
$Disziplinen[120]['Typ']		=	"l";

$Disziplinen[121]['Bez']		=	"5000 m wheelchair"; #
$Disziplinen[121]['Kurz']		=	"5000m wheel."; #
$Disziplinen[121]['Typ']		=	"l"; #

$Disziplinen[125]['Bez']		=	"10,000 m";
$Disziplinen[125]['Kurz']		=	"10,000m";
$Disziplinen[125]['Typ']		=	"l";

$Disziplinen[126]['Bez']		=	"Half Hour";
$Disziplinen[126]['Kurz']		=	"1/2 h";
$Disziplinen[126]['Typ']		=	"l";

$Disziplinen[127]['Bez']		=	"Half Hour Team";
$Disziplinen[127]['Kurz']		=	"1/2 h Team";
$Disziplinen[127]['Typ']		=	"s";

$Disziplinen[128]['Bez']		=	"1 Hour";
$Disziplinen[128]['Kurz']		=	"1 h";
$Disziplinen[128]['Typ']		=	"l";

$Disziplinen[129]['Bez']		=	"1 Hour Team";
$Disziplinen[129]['Kurz']		=	"1 h Team";
$Disziplinen[129]['Typ']		=	"s";

$Disziplinen[131]['Bez']		=	"5 Kilometres";
$Disziplinen[131]['Kurz']		=	"5km";
$Disziplinen[131]['Typ']		=	"w";

$Disziplinen[132]['Bez']		=	"5 Kilometres Team";
$Disziplinen[132]['Kurz']		=	"5km Team";
$Disziplinen[132]['Typ']		=	"w";

$Disziplinen[133]['Bez']		=	"7,5 Kilometres";
$Disziplinen[133]['Kurz']		=	"7,5km";
$Disziplinen[133]['Typ']		=	"w";

$Disziplinen[134]['Bez']		=	"7,5 Kilometres Team";
$Disziplinen[134]['Kurz']		=	"7,5km Team";
$Disziplinen[134]['Typ']		=	"w";

$Disziplinen[135]['Bez']		=	"10 Kilometres";
$Disziplinen[135]['Kurz']		=	"10km";
$Disziplinen[135]['Typ']		=	"w";

$Disziplinen[136]['Bez']		=	"10 Kilometres Team";
$Disziplinen[136]['Kurz']		=	"10km Team";
$Disziplinen[136]['Typ']		=	"w";

$Disziplinen[137]['Bez']		=	"15 Kilometres";
$Disziplinen[137]['Kurz']		=	"15km";
$Disziplinen[137]['Typ']		=	"w";

$Disziplinen[138]['Bez']		=	"15 Kilometres Team";
$Disziplinen[138]['Kurz']		=	"15km Team";
$Disziplinen[138]['Typ']		=	"w";

$Disziplinen[140]['Bez']		=	"25 Kilometres";
$Disziplinen[140]['Kurz']		=	"25km";
$Disziplinen[140]['Typ']		=	"w";

$Disziplinen[141]['Bez']		=	"25 Kilometres Team";
$Disziplinen[141]['Kurz']		=	"25km Team";
$Disziplinen[141]['Typ']		=	"w";

$Disziplinen[148]['Bez']		=	"Half Marathon";
$Disziplinen[148]['Kurz']		=	"1/2 Marath.";
$Disziplinen[148]['Typ']		=	"w";

$Disziplinen[149]['Bez']		=	"Half Marathon Team";
$Disziplinen[149]['Kurz']		=	"1/2 Marath. Team";
$Disziplinen[149]['Typ']		=	"w";

$Disziplinen[150]['Bez']		=	"Marathon";
$Disziplinen[150]['Kurz']		=	"Marath.";
$Disziplinen[150]['Typ']		=	"w";

$Disziplinen[151]['Bez']		=	"Marathon Team";
$Disziplinen[151]['Kurz']		=	"Marath. Team";
$Disziplinen[151]['Typ']		=	"w";

$Disziplinen[153]['Bez']		=	"100 Kilometres";
$Disziplinen[153]['Kurz']		=	"100km";
$Disziplinen[153]['Typ']		=	"w";

$Disziplinen[154]['Bez']		=	"100 Kilometres Team";
$Disziplinen[154]['Kurz']		=	"100km Team";
$Disziplinen[154]['Typ']		=	"w";

$Disziplinen[156]['Bez']		=	"5 x 10km Relay";
$Disziplinen[156]['Kurz']		=	"5x10km";
$Disziplinen[156]['Typ']		=	"w";

$Disziplinen[159]['Bez']		=	"50 m Hurdles";
$Disziplinen[159]['Kurz']		=	"50m H.";
$Disziplinen[159]['Typ']		=	"l";

$Disziplinen[160]['Bez']		=	"60 m Hurdles";
$Disziplinen[160]['Kurz']		=	"60m H.";
$Disziplinen[160]['Typ']		=	"l";

$Disziplinen[161]['Bez']		=	"60 m Hurdles Heat 1";
$Disziplinen[161]['Kurz']		=	"60m H. H1";
$Disziplinen[161]['Typ']		=	"l";

$Disziplinen[162]['Bez']		=	"60 m Hurdles Heat 2";
$Disziplinen[162]['Kurz']		=	"60m H. H2";
$Disziplinen[162]['Typ']		=	"l";

$Disziplinen[170]['Bez']		=	"80 m Hurdles";
$Disziplinen[170]['Kurz']		=	"80m H.";
$Disziplinen[170]['Typ']		=	"l";

$Disziplinen[180]['Bez']		=	"100 m Hurdles";
$Disziplinen[180]['Kurz']		=	"100m H.";
$Disziplinen[180]['Typ']		=	"l";

$Disziplinen[190]['Bez']		=	"110 m Hurdles";
$Disziplinen[190]['Kurz']		=	"110m H.";
$Disziplinen[190]['Typ']		=	"l";

$Disziplinen[195]['Bez']		=	"300 m Hurdles";
$Disziplinen[195]['Kurz']		=	"300m H.";
$Disziplinen[195]['Typ']		=	"l";

$Disziplinen[200]['Bez']		=	"400 m Hurdles";
$Disziplinen[200]['Kurz']		=	"400m H.";
$Disziplinen[200]['Typ']		=	"l";

$Disziplinen[210]['Bez']		=	"1500 m Steeplechase";
$Disziplinen[210]['Kurz']		=	"1500m SC";
$Disziplinen[210]['Typ']		=	"l";

$Disziplinen[220]['Bez']		=	"2000 m Steeplechase";
$Disziplinen[220]['Kurz']		=	"2000m SC";
$Disziplinen[220]['Typ']		=	"l";

$Disziplinen[230]['Bez']		=	"3000 m Steeplechase";
$Disziplinen[230]['Kurz']		=	"3000m SC";
$Disziplinen[230]['Typ']		=	"l";

$Disziplinen[240]['Bez']		=	"4x50 m Relay";
$Disziplinen[240]['Kurz']		=	"4x50m";
$Disziplinen[240]['Typ']		=	"s";

$Disziplinen[250]['Bez']		=	"4x75 m Relay";
$Disziplinen[250]['Kurz']		=	"4x75m";
$Disziplinen[250]['Typ']		=	"s";

$Disziplinen[260]['Bez']		=	"4x100 m Relay";
$Disziplinen[260]['Kurz']		=	"4x100m";
$Disziplinen[260]['Typ']		=	"s";

$Disziplinen[261]['Bez']		=	"4 x 100 m wheelchair";
$Disziplinen[261]['Kurz']		=	"4x100 wheel.";
$Disziplinen[261]['Typ']		=	"s";

$Disziplinen[270]['Bez']		=	"4x200 m Relay";
$Disziplinen[270]['Kurz']		=	"4x200m";
$Disziplinen[270]['Typ']		=	"s";

$Disziplinen[280]['Bez']		=	"4x400 m Relay";
$Disziplinen[280]['Kurz']		=	"4x400m";
$Disziplinen[280]['Typ']		=	"s";

$Disziplinen[281]['Bez']		=	"4 x 400 m wheelchair";
$Disziplinen[281]['Kurz']		=	"4x400 wheel.";
$Disziplinen[281]['Typ']		=	"s";

$Disziplinen[290]['Bez']		=	"3x800 m Relay";
$Disziplinen[290]['Kurz']		=	"3x800m";
$Disziplinen[290]['Typ']		=	"s";

$Disziplinen[300]['Bez']		=	"4x800 m Relay";
$Disziplinen[300]['Kurz']		=	"4x800m";
$Disziplinen[300]['Typ']		=	"s";

$Disziplinen[310]['Bez']		=	"3x1000 m Relay";
$Disziplinen[310]['Kurz']		=	"3x1000m";
$Disziplinen[310]['Typ']		=	"s";

$Disziplinen[320]['Bez']		=	"4x1500 m Relay";
$Disziplinen[320]['Kurz']		=	"4x1500m";
$Disziplinen[320]['Typ']		=	"s";

$Disziplinen[321]['Bez']		=	"Olympic Relay";
$Disziplinen[321]['Kurz']		=	"OLY Relay";
$Disziplinen[321]['Typ']		=	"s";

$Disziplinen[322]['Bez']		=	"Swedish Relay";
$Disziplinen[322]['Kurz']		=	"SWE Relay";
$Disziplinen[323]['Bez']		=	"Medley Relay";
$Disziplinen[323]['Kurz']		=	"Medl. Relay";
$Disziplinen[323]['Typ']		=	"s";

$Disziplinen[324]['Bez']		=	"Commute Relay";
$Disziplinen[324]['Kurz']		=	"Com. Relay";
$Disziplinen[324]['Typ']		=	"s";

$Disziplinen[325]['Bez']		=	"1000 m Race Walk";
$Disziplinen[325]['Kurz']		=	"1000m RW";
$Disziplinen[325]['Typ']		=	"l";

$Disziplinen[330]['Bez']		=	"2000 m Race Walk";
$Disziplinen[330]['Kurz']		=	"2000m RW";
$Disziplinen[330]['Typ']		=	"l";

$Disziplinen[340]['Bez']		=	"3000 m Race Walk";
$Disziplinen[340]['Kurz']		=	"3000m RW";
$Disziplinen[340]['Typ']		=	"l";

$Disziplinen[350]['Bez']		=	"5000 m Race Walk";
$Disziplinen[350]['Kurz']		=	"5000m RW";
$Disziplinen[350]['Typ']		=	"l";

$Disziplinen[352]['Bez']		=	"10,000 m Race Walk";
$Disziplinen[352]['Kurz']		=	"10,000m RW";
$Disziplinen[352]['Typ']		=	"l";

$Disziplinen[354]['Bez']		=	"20,000 m Race Walk";
$Disziplinen[354]['Kurz']		=	"20,000m RW";
$Disziplinen[354]['Typ']		=	"l";

$Disziplinen[355]['Bez']		=	"1 Kilometre Race Walk";
$Disziplinen[355]['Kurz']		=	"1km RW";
$Disziplinen[355]['Typ']		=	"w";

$Disziplinen[356]['Bez']		=	"2 Kilometres Race Walk";
$Disziplinen[356]['Kurz']		=	"2km RW";
$Disziplinen[356]['Typ']		=	"w";

$Disziplinen[358]['Bez']		=	"3 Kilometres Race Walk";
$Disziplinen[358]['Kurz']		=	"3km RW";
$Disziplinen[358]['Typ']		=	"w";

$Disziplinen[359]['Bez']		=	"3 Kilometres Race Walk Team";
$Disziplinen[359]['Kurz']		=	"3km RW Team";
$Disziplinen[359]['Typ']		=	"w";

$Disziplinen[360]['Bez']		=	"5 Kilometres Race Walk";
$Disziplinen[360]['Kurz']		=	"5km RW";
$Disziplinen[360]['Typ']		=	"w";

$Disziplinen[361]['Bez']		=	"5 Kilometres Race Walk Team";
$Disziplinen[361]['Kurz']		=	"5km RW Team";
$Disziplinen[361]['Typ']		=	"w";

$Disziplinen[370]['Bez']		=	"10 Kilometres Race Walk";
$Disziplinen[370]['Kurz']		=	"10km RW";
$Disziplinen[370]['Typ']		=	"w";

$Disziplinen[371]['Bez']		=	"10km Kilometres Race Walk Team";
$Disziplinen[371]['Kurz']		=	"10km RW Team";
$Disziplinen[371]['Typ']		=	"w";

$Disziplinen[380]['Bez']		=	"20 Kilometres Race Walk";
$Disziplinen[380]['Kurz']		=	"20km RW";
$Disziplinen[380]['Typ']		=	"w";

$Disziplinen[381]['Bez']		=	"20 Kilometres Race Walk Team";
$Disziplinen[381]['Kurz']		=	"20km RW Team";
$Disziplinen[381]['Typ']		=	"w";

$Disziplinen[386]['Bez']		=	"30 Kilometres Race Walk";
$Disziplinen[386]['Kurz']		=	"30km RW";
$Disziplinen[386]['Typ']		=	"w";

$Disziplinen[387]['Bez']		=	"30km Kilometres Race Walk Team";
$Disziplinen[387]['Kurz']		=	"30km RW Team";
$Disziplinen[387]['Typ']		=	"w";

$Disziplinen[390]['Bez']		=	"50 Kilometres Race Walk";
$Disziplinen[390]['Kurz']		=	"50km RW";
$Disziplinen[390]['Typ']		=	"w";

$Disziplinen[391]['Bez']		=	"50km Kilometres Race Walk Team";
$Disziplinen[391]['Kurz']		=	"50km RW Team";
$Disziplinen[391]['Typ']		=	"w";

$Disziplinen[510]['Bez']		=	"heigh jump";
$Disziplinen[510]['Typ']		=	"h";
$Disziplinen[510]['Kurz']		=	"HJ";

$Disziplinen[520]['Bez']		=	"Pole Vault";
$Disziplinen[520]['Kurz']		=	"PV";
$Disziplinen[520]['Typ']		=	"h";

$Disziplinen[530]['Bez']		=	"long jump";
$Disziplinen[530]['Kurz']		=	"LJ";
$Disziplinen[530]['Typ']		=	"t";

$Disziplinen[535]['Bez']		=	"Standing Long Jump";
$Disziplinen[535]['Kurz']		=	"Stand. LJ";
$Disziplinen[535]['Typ']		=	"t";

$Disziplinen[540]['Bez']		=	"triple jump";
$Disziplinen[540]['Kurz']		=	"TJ";
$Disziplinen[540]['Typ']		=	"t";

$Disziplinen[610]['Bez']		=	"shot put";
$Disziplinen[610]['Kurz']		=	"SP";
$Disziplinen[610]['Typ']		=	"t";

$Disziplinen[611]['Bez']		=	"Stone";
$Disziplinen[611]['Kurz']		=	"Stone";
$Disziplinen[611]['Typ']		=	"t";

$Disziplinen[612]['Bez']		=	"shot put wheelchair"; #
$Disziplinen[612]['Kurz']		=	"SP wheel."; #
$Disziplinen[612]['Typ']		=	"t"; #

$Disziplinen[620]['Bez']		=	"discus throw";
$Disziplinen[620]['Kurz']		=	"DT";
$Disziplinen[620]['Typ']		=	"t";

$Disziplinen[621]['Bez']		=	"discus wheelcair"; #
$Disziplinen[621]['Kurz']		=	"DT wheel."; #
$Disziplinen[621]['Typ']		=	"t"; #

$Disziplinen[630]['Bez']		=	"Hammer Throw";
$Disziplinen[630]['Kurz']		=	"HT";
$Disziplinen[630]['Typ']		=	"t";

$Disziplinen[640]['Bez']		=	"javelin throw";
$Disziplinen[640]['Kurz']		=	"JT";
$Disziplinen[640]['Typ']		=	"t";

$Disziplinen[641]['Bez']		=	"javelin wheelchair"; #
$Disziplinen[641]['Kurz']		=	"JT wheel."; #
$Disziplinen[641]['Typ']		=	"t"; #

$Disziplinen[644]['Bez']		=	"club";
$Disziplinen[644]['Kurz']		=	"CT";
$Disziplinen[644]['Typ']		=	"t";

$Disziplinen[650]['Bez']		=	"Ball Throwing 200g";
$Disziplinen[650]['Kurz']		=	"BT 200g";
$Disziplinen[650]['Typ']		=	"t";

$Disziplinen[660]['Bez']		=	"Ball Throwing 80g";
$Disziplinen[660]['Kurz']		=	"BT 80g";
$Disziplinen[660]['Typ']		=	"t";

$Disziplinen[670]['Bez']		=	"Schleuderball";
$Disziplinen[670]['Kurz']		=	"SchleuB";
$Disziplinen[670]['Typ']		=	"t";

$Disziplinen[690]['Bez']		=	"Weight Throw";
$Disziplinen[690]['Kurz']		=	"WT";
$Disziplinen[690]['Typ']		=	"t";

$Disziplinen[710]['Bez']		=	"Triathlon";
$Disziplinen[710]['Kurz']		=	"TRI";
$Disziplinen[710]['Typ']		=	"m";

$Disziplinen[711]['Bez']		=	"Triathlon Team";
$Disziplinen[711]['Kurz']		=	"TRI Team";
$Disziplinen[711]['Typ']		=	"b";

$Disziplinen[720]['Bez']		=	"Tetrathlon";
$Disziplinen[720]['Kurz']		=	"TET";
$Disziplinen[720]['Typ']		=	"m";

$Disziplinen[721]['Bez']		=	"Tetrathlon Team";
$Disziplinen[721]['Kurz']		=	"TET Team";
$Disziplinen[721]['Typ']		=	"b";

$Disziplinen[730]['Bez']		=	"Pentathlon";
$Disziplinen[730]['Kurz']		=	"PEN";
$Disziplinen[730]['Typ']		=	"m";

$Disziplinen[731]['Bez']		=	"Pentathlon Team";
$Disziplinen[731]['Kurz']		=	"PEN Team";
$Disziplinen[731]['Typ']		=	"b";

$Disziplinen[740]['Bez']		=	"Hexathlon";
$Disziplinen[740]['Kurz']		=	"HEX";
$Disziplinen[740]['Typ']		=	"m";

$Disziplinen[741]['Bez']		=	"Hexathlon Team";
$Disziplinen[741]['Kurz']		=	"HEX Team";
$Disziplinen[741]['Typ']		=	"b";

$Disziplinen[750]['Bez']		=	"Heptathlon";
$Disziplinen[750]['Kurz']		=	"HEP";
$Disziplinen[750]['Typ']		=	"m";

$Disziplinen[751]['Bez']		=	"Heptathlon Team";
$Disziplinen[751]['Kurz']		=	"HEP Team";
$Disziplinen[751]['Typ']		=	"b";

$Disziplinen[765]['Bez']		=	"Enneathlon";
$Disziplinen[765]['Kurz']		=	"ENN";
$Disziplinen[765]['Typ']		=	"m";

$Disziplinen[766]['Bez']		=	"Enneathlon Team";
$Disziplinen[766]['Kurz']		=	"ENN Team";
$Disziplinen[766]['Typ']		=	"b";

$Disziplinen[770]['Bez']		=	"Decathlon";
$Disziplinen[770]['Kurz']		=	"DEC";
$Disziplinen[770]['Typ']		=	"m";

$Disziplinen[771]['Bez']		=	"Decathlon Team";
$Disziplinen[771]['Kurz']		=	"DEC Team";
$Disziplinen[771]['Typ']		=	"b";

$Disziplinen[790]['Bez']		=	"Throwing Pentathlon";
$Disziplinen[790]['Kurz']		=	"Throw. PEN";
$Disziplinen[790]['Typ']		=	"m";

$Disziplinen[791]['Bez']		=	"Throwing Pentathlon Team";
$Disziplinen[791]['Kurz']		=	"Throw. PEN Team";
$Disziplinen[791]['Typ']		=	"b";

$Disziplinen[785]['Bez']		=	"German Pentathlon Basic";
$Disziplinen[785]['Kurz']		=	"GER PEN B";
$Disziplinen[785]['Typ']		=	"m";

$Disziplinen[780]['Bez']		=	"German Pentathlon Dash/Jump";
$Disziplinen[780]['Kurz']		=	"GER PEN D/J";
$Disziplinen[780]['Typ']		=	"m";

$Disziplinen[782]['Bez']		=	"German Pentathlon Race";
$Disziplinen[782]['Kurz']		=	"GER PEN R";
$Disziplinen[782]['Typ']		=	"m";

$Disziplinen[784]['Bez']		=	"German Pentathlon Throw";
$Disziplinen[784]['Kurz']		=	"GER PEN T";
$Disziplinen[784]['Typ']		=	"m";

$Disziplinen[788]['Bez']		=	"German Pentathlon Team";
$Disziplinen[788]['Kurz']		=	"GER PEN Team";
$Disziplinen[788]['Typ']		=	"m";

$Disziplinen[801]['Bez']		=	"DMM Gruppe 1";
$Disziplinen[801]['Kurz']		=	"DMM G1";
$Disziplinen[801]['Typ']		=	"d";

$Disziplinen[803]['Bez']		=	"DMM Gruppe 2";
$Disziplinen[803]['Kurz']		=	"DMM G2";
$Disziplinen[803]['Typ']		=	"d";

$Disziplinen[805]['Bez']		=	"DMM Gruppe 3";
$Disziplinen[805]['Kurz']		=	"DMM G3";
$Disziplinen[805]['Typ']		=	"d";

$Disziplinen[816]['Bez']		=	"Gruppe 1";
$Disziplinen[816]['Kurz']		=	"Gruppe 1";
$Disziplinen[816]['Typ']		=	"d";

$Disziplinen[817]['Bez']		=	"Gruppe 2";
$Disziplinen[817]['Kurz']		=	"Gruppe 2";
$Disziplinen[817]['Typ']		=	"d";

$Disziplinen[818]['Bez']		=	"Gruppe 3";
$Disziplinen[818]['Kurz']		=	"Gruppe 3";
$Disziplinen[818]['Typ']		=	"d";

$Disziplinen[819]['Bez']		=	"Gruppe 4";
$Disziplinen[819]['Kurz']		=	"Gruppe 4";
$Disziplinen[819]['Typ']		=	"d";

$Disziplinen[824]['Bez']		=	"M70 DAMM";
$Disziplinen[824]['Kurz']		=	"M70 DAMM";
$Disziplinen[824]['Typ']		=	"d";

$Disziplinen[828]['Bez']		=	"W60 DAMM";
$Disziplinen[828]['Kurz']		=	"W60 DAMM";
$Disziplinen[828]['Typ']		=	"d";

$Disziplinen[840]['Bez']		=	"I JtfO";
$Disziplinen[840]['Kurz']		=	"I JtfO";
$Disziplinen[840]['Typ']		=	"j";

$Disziplinen[841]['Bez']		=	"II JtfO";
$Disziplinen[841]['Kurz']		=	"II JtfO";
$Disziplinen[841]['Typ']		=	"j";

$Disziplinen[843]['Bez']		=	"III JtfO";
$Disziplinen[843]['Kurz']		=	"III JtfO";
$Disziplinen[844]['Typ']		=	"j";

$Disziplinen[844]['Bez']		=	"IV JtfO";
$Disziplinen[844]['Kurz']		=	"IV JtfO";
$Disziplinen[844]['Typ']		=	"j";

$Disziplinen[845]['Bez']		=	"IV/1JtfO";
$Disziplinen[845]['Kurz']		=	"IV/1JtfO";
$Disziplinen[845]['Typ']		=	"j";

$Disziplinen[846]['Bez']		=	"IV/2JtfO";
$Disziplinen[846]['Kurz']		=	"IV/2JtfO";
$Disziplinen[846]['Typ']		=	"j";

$Disziplinen[0]['Bez']			=	"Own Event";
$Disziplinen[0]['Kurz']			=	"Own Evt.";
$Disziplinen[0]['Typ']			=	"e";


# --- Rundentypen -------------------Rounds----------------------------

$RundeTyp0 = "final";
$RundeTyp1 = "heats";
$RundeTyp2 = "semi-finals";
$RundeTyp3 = "time-heats";

$RundeTyp99 = " ";

$RundeTyp4 = "elimination";
$RundeTyp5 = "qualification"; 
$RundeTyp6 = "time-races";
$RundeTyp7 = "finals A/B";

$RundeTyp8 = "heat";
# belegt für MK 9

$RundeTypa = "after 1 event";
$RundeTypb = "after 2 events";
$RundeTypc = "after 3 events";
$RundeTypd = "after 4 events";
$RundeType = "after 5 events";
$RundeTypf = "after 6 events";
$RundeTypg = "after 7 events";
$RundeTyph = "after 8 events";
$RundeTypi = "after 9 events";

# Abbrev for Types
$RoundTypAbbrev[0]	= "F";
$RoundTypAbbrev[1]	= "H";
$RoundTypAbbrev[2]	= "SF";
$RoundTypAbbrev[3]	= "H";
$RoundTypAbbrev[4]	= "E";
$RoundTypAbbrev[5]	= "Q";
$RoundTypAbbrev[6]	= "F";
$RoundTypAbbrev[7]	= "A/B";
$RoundTypAbbrev[8]	= "Inv";
$RoundTypAbbrev[99]	= "";

# --- Typ-Typen ---------------------Typs---------------------------

$TypTyp1 = "Result list";
$TypTyp2 = "Entry list";
$TypTyp3 = "Intermediate Results";
$TypTyp4 = "Start list";
$TypTyp5 = "";
$TypTyp6 = "advanced by heats";
$TypTyp7 = "advanced by semi-finals";
$TypTyp8 = "event finished";

# --- Typ-Typen Abkürzungen ---------------------Typs Abbrev ------
$ListTypAbbrev[1] = "&nbsp;R&nbsp;";
$ListTypAbbrev[2] = "&nbsp;E&nbsp;";
$ListTypAbbrev[3] = "&nbsp;I&nbsp;";
$ListTypAbbrev[4] = "&nbsp;S&nbsp;";
$ListTypAbbrev[5] = "";
$ListTypAbbrev[6] = "";
$ListTypAbbrev[7] = "";
$ListTypAbbrev[8] = "";


# --- arabische in römische Zahlen--------------------------

$Roemisch[1] = "I";
$Roemisch[2] = "II";
$Roemisch[3] = "III";
$Roemisch[4] = "IV";
$Roemisch[5] = "V";
$Roemisch[6] = "VI";
$Roemisch[7] = "VII";
$Roemisch[8] = "VIII";
$Roemisch[9] = "IX";
$Roemisch[10] = "X";
$Roemisch[11] = "XI";
$Roemisch[12] = "XII";
$Roemisch[13] = "XIII";
$Roemisch[14] = "XIV";
$Roemisch[15] = "XV";
$Roemisch[16] = "XVI";
$Roemisch[17] = "XVII";
$Roemisch[18] = "XVIII";
$Roemisch[19] = "IX";
$Roemisch[20] = "XX";

# --- Wochentage --------------------------
$Wochentage[0] = "Sunday";
$Wochentage[1] = "Monday";
$Wochentage[2] = "Tuesday";
$Wochentage[3] = "Wednesday";
$Wochentage[4] = "Thursday";
$Wochentage[5] = "Friday";
$Wochentage[6] = "Saturday";

$LengthForAbbrevDaysOfWeek = 3;


break;

case "en_IAAF":	# English IAAF !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

$txt_laive = "LaIVE";
$TitleHTMLInfo 	= "(athletics live results)";
$txt_vom = "";
$txt_am = "";
$txt_in = "in";
$txt_uebersicht = "Summary";
$txt_zeitplan = "Timetable";
$txt_stellplatzzeitplan = "Timetable for the final confirmation";
$txt_gesamtteilnehmerliste = "All athletes and teams";
$txt_gesamtteilnehmerlistenachverein = "Entry list by countries/clubs";
$txt_gesamtteilnehmerlistenachwettbewerben = "Entry list by events";
$txt_tag = "Day";
$txt_kopf_zeitplanaktualisiert ="Timetable updated:";
$text_hinweissortierunguebersicht = "Click column header for sorting.";

$txt_klasse = "Categorie";
$txt_disziplin = "Event";
$txt_runde = "Round"; 
$txt_typ = "Type";
$txt_aktualisiert = "upd.";
$txt_startzeit = "Time";
$txt_wettbewerb = "Event";
$txt_anzahlrunden = "Rounds";
$txt_zeit = "latest<br>confirmation time";
$txt_meldungen = "Entries";
$txt_wbnr = "Event No.";
$txt_IPCClassesName = "Start class";
$txt_headline_abbrev_participansandteams = "P/T";
$txt_headline_explanation_participansandteams = "Number of participants or teams in each round of an event";
$txt_headline_abbrev_heatsandgroups = "H/G";
$txt_headline_explanation_heatsandgroups = "Number of heats or groups in each round of an event";

$txt_startnummer = "BIB";
$txt_name = "Name";
$txt_geschlecht = "m/w";
$txt_jahrgang = "YOB";
$txt_lv = "NAT";
$txt_verein = "Country/Club";
$txt_gemeldetewettbewerbe = "Entries for the following events";

$txt_stellplatzabgabe = "Final confirmation possible until final confirmation time for each event.";
$txt_anzahlrunden = "Rounds";
$txt_anzahlrunden_keine = "No Entry list or Result list available.";
$txt_anzahlwettbewerbestellplatz = "Events with final confirmation";
$txt_zwischenergebnisse = "Intermediate Results";
$txt_finale_ergebnisse = "Final (Result list)";
$txt_zwischenlaeufe_ergebnisse = "Semi-Finals (Result list)";
$txtIPCMode = "IPC";
$txt_laivefuss1 = "$txt_laive (Version: $ResultTickerVersion - $ResultTickerErsteller - <a href='http://kwenzel.net/Special_Contact' target='_blank'>laive@kwenzel.net</a>)";
$txt_laivefuss2 = "Data handling by COSA WIN.";

$txt_meta_beschreibung = "Live data for the following Athletics competition:";

# Format ----------------------------------------
$MarkSeperator1 = ".";

# Allgemein ----------------------------------------
$TxtMixedEvent = "(mixed event)";
$TxtCombinedEventGroup = "group";
$TxtHeat = "heat";
$TxtGroup = "group";
$TxtAbrrevGenderMale = "m";
$TxtAbrrevGenderMan = "M";
$TxtAbrrevGenderFemale = "w";
$TxtAbrrevGenderWoman = "W";
$TxtAbbrevOutOfRanking = "no rank.";
$TxtParticipant = "athlete";
$TxtParticipants = "athletes";
$TxtAbbrevParticipant = "athl.";
$TxtAbbrevParticipants = "athl.";
$TxtAbbrevRelayTeam = "team";
$TxtAbbrevRelayTeams = "teams";
$TxtDaytimeUnit = "";
$txtMenuStartlistsAll = "All start lists";

# Index.php ----------------------------------------
$LinksSubMenuResultsFinal = "final (Result list)";
$LinksSubMenuResultsSemifinals = "semi-finals (Result list)";
$LinksSubMenuResultsTimedHeats = "time heats (Result list)";
$LinksSubMenuResultsHeats = "heats (Result list)";
$LinksSubMenuResultsCombinedEventAfter9Events = "after 9 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter8Events = "after 8 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter7Events = "after 7 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter6Events = "after 6 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter5Events = "after 5 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter4Events = "after 4 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter3Events = "after 3 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter2Events = "after 2 events (Res.)";
$LinksSubMenuResultsCombinedEventAfter1Event = "after 1 event (Res.)";
$LinksSubMenuStartlistFinalTrack = "final (Start list)";
$LinksSubMenuStartlistFinalHJ = "final (Start list)";
$LinksSubMenuStartlistFinalField = "final (Start list)";
$LinksSubMenuStartlistFinalTrackAB = "finals A/B (Start list)";
$LinksSubMenuStartlistTimeRace = "time-races (Start list)";
$LinksSubMenuStartlistSemifinals = "semi-finals (Start list)";
$LinksSubMenuStartlistTimedHeats = "time heats (Start list)";
$LinksSubMenuStartlistHeats = "heats (Start list)";
$LinksSubMenuStartlistEliminationTrack = "qualification heat (Start list)";
$LinksSubMenuStartlistEliminationField = "elimination (Start list)";
$LinksSubMenuStartlistQualification = "qualification (Start list)";
$LinksSubMenuStartlistOnlyHeatNumber = "heat (Start list)";
$LinksSubMenuParticipantslist = "Entry list";
$TxtSubMenuUpdated = "updated:";
$TxtFooterSiteLoadedIn = "Site loaded in";
$TxtFooterSiteLoadedInUnit = "s";
$txtMenuEntriesAll = "All entries";
$txtMenuEntriesByEvent ="Entries by events";
$txtMenuEntriesByClub ="Entries by countries/clubs";

$LinksSubMenuResultsEliminationTrack = "qualification heat (Result list)";
$LinksSubMenuResultsTimeRace = "time-races (Result list)";
$LinksSubMenuResultsFinalTrackAB = "final A/B (Result list)";
$LinksSubMenuResultsOnlyHeatNumber = "heat (Result list)";
$LinksSubMenuResultsFinalHJ = "final (Result list)";
$LinksSubMenuResultsFinalField = "final (Result list)";
$LinksSubMenuResultsEliminationField = "elimination (Result list)";
$LinksSubMenuResultsQualification = "qualification (Result list)";

# zeitplan.php ----------------------------------------


$TxtHeats = "heats";

$TxtGroups = "groups";
$TxtLinkEntriesByClubs = "Entries by countries/clubs";




$TxtEntry = "entry";
$TxtEntries = "entries";
$TxtNoEntry = "0";

$TxtHeadKey = "Key";

$TxtTTBoardPage = "Page";
$TxtTTBoardDownload = "Download Timetable for Score Board";


# startlistenerstellen.php ----------------------------------------
$TxtStartlistHeadline = "Start list";

$TxtSeasonBest = "Personal best";
$TxtQualificationMark = "Qualification<br>mark";

$TxtAbbrevOrdner = "Ord.";
$TxtAbbrevBIB = "BIB";
$TxtAbbrevBIBRelay = "BIB";
$TxtAthleteName = "Name";
$TxtAbbrevJOB = "YOB";
$TxtAbbrevNation = "NAT";
$TxtClub = "Country/Club";
$TxtRelayTeam = "Team";
$TxtRelayMembers = "Relay members";
$TxtQualificationHeat = "heat";
$TxtQualificationHeats = "heats";
$TxtQualificationTimedHeat = "heat";
$TxtQualificationTimedHeats = "heats";
$TxtQualificationSemiFinal = "semi-final";
$TxtQualificationSemiFinals = "semi-finals";
$TxtQualificationByPlaceTrack1 = "first in each heat (Q)";
$TxtQualificationByPlaceTrackMore = "first in each heat (Q)";
$TxtQualificationByTimeTrack1 = "fastest (q)";
$TxtQualificationByTimeTrackMore = "fastest (q)";
$TxtAt = "at";

$TxtOnThe = "on the";
$TxtQualificationToFinal = "final";
$TxtQualificationToFinalsNotEqual = "finals (not equal)";
$TxtQualificationToFinalsEqual = "finals (equal)";
$TxtQualificationToFinal2 = "the final";
$TxtQualificationToFinal3 = "the final";
$TxtQualificationToSemiFinal = "semi-final";
$TxtQualificationToSemiFinals = "semi-finals";
$TxtQualificationWordsBetweenPlaceAndTime = "and";
$TxtQualificationFrom = "From";
$TxtQualificationAdvancedTo = "advanced";
$TxtQualificationToWord = "to";
$TxtQualificationHeadline = "Qualification";
$TxtHJHeightsHeadline = "Heights";

$TxtStartlistHeadlineAll = "Start lists of all events and rounds";

# create_entrylists.php ----------------------------------------
$TxtEntrylistHeadline = "Entry list";
$TxtFinalConfirmation[0]['Abbrev'] 		= "&#10008;";
$TxtFinalConfirmation[0]['Explanation'] = "Participation not confirmed";
$TxtFinalConfirmation[1]['Abbrev'] 		= "&#10004;";
$TxtFinalConfirmation[1]['Explanation'] = "Participation finaly confirmed";

# gesamtteilnehmer.php ----------------------------------------
$TxtLinkSubMenuEntriesList1 = "All athletes and teams";
$TxtLinkSubMenuEntriesList2 = "Entries by events";
$TxtLinkSubMenuEntriesList3 = "Entries by countries/clubs";
$TxtEvaluationGroupsHeadline = "Evaluation groups";
$TxtEvaluationGroupsNoGroup = "No evaluation group selected";
$TxtAbbrevLateEntry = "late entry";
$TxtLateEntry = "late entry";
$TxtAnd = "and";
$TxtRelayTeam = "Relay team";
$TxtRelayTeams = "Relay teams";
$TxtSortedByHeadline = "Sorted by";
$TxtSortedByBIB = "BIB Numbers";
$TxtSortedByName = "Names";
$TxtSortedBySeasonBest = "Personal bests";
$TxtSortedByIPCClass = "Start class";
$TxtSummaryOfClasses = "Summary";
$TxtEntriesByEventsHeats = "Heats";
$TxtEntriesByEventsSemiFinals = "Semi-Finals";
$TxtEntriesByEventsFinal = "Final";
$TxtEntriesByEventsCombinedEventFirstEvent = "Start of 1<sup>st</sup> event";
$TxtClubs = "Countries/Clubs";
$TxtIPCClass = "Start cl.";
$TxtSDMSID = "SDMS-ID";
$TxtAthletesLicenceIDShowHead = "Show athlete's licence ID";
$TxtAthletesLicenceIDDontShowHead = "Do not show athlete's licence ID";
$TxtAthletesLicenceID = "Athlete's licence ID";
$TxtAthletesOnlineID = "Athlete's online ID";

# uebersicht.php ----------------------------------------
$TxtLinkJustRL = "Just show Result lists";

# cupscoring.php ----------------------------------------
$txt_headline_cupscoring = "Cup scoring";
$txt_cup_standingafter_1 = "Standing after";
$txt_cup_standingafter_2 = "of";
$txt_cup_standingafter_3 = "events";
$cup_tablehead_place	= "Place"; 
$cup_tablehead_team		= "Team";
$cup_tablehead_points 	= "Points";
$cup_afterplace 		= ".";
$txt_cup_includedevents = "Following events are included";
$txt_cup_detailedScoring = "Detailed scoring by event";
$txt_cup_scoringbased  = "Cup scoring is based on the following rules";
$txt_cup_Nb_ScoredAthletes = "Number of scored athletes per team and event";
$txt_cup_Nb_ScoredPlaces = "Number of scored places in event";
$txt_cup_Nb_Points = "Points (starting by 1st Place)";
$txt_cup_Nb_ScoredRelays = "Number of scored relay teams per team and event";
$txt_cup_Nb_ScoredPlacesRelays = "Number of scored places in relay event";
$txt_cup_Nb_PointsRelays = "Points for relays (starting by 1st Place)";
$txt_cup_NoCombinedScoring = "No combined cup scoring available.";

$txt_headline_combined_cupscoring 	= "Combined cup scoring";
$txt_combined_cup_detailedScoring 	= "Points (and Places) by cups";
$txt_combined_cup_includedcups		= "Following cups are included";
$txt_cup_Combined_standingafter_1	= "of";
$txt_cup_Combined_standingafter_2	= "events finished";

### Noch ergänzen ###


$competitionsubname	="Competition website";
$resultname			="Result list";								#Bezeichnung Ergebnisse
$resultname1		="Result list day 1";						#Bezeichnung Ergebnisse Tag 1
$resultname2		="Result list day 2";						#Bezeichnung Ergebnisse Tag 2

# --- Dateien ------ Files -----------------------------------------------

# Module ---- Modules
$dat_index = "index.php";
$dat_uebersicht = "uebersicht.php";
$dat_zeitplan = "zeitplan.php";
$dat_stellplatzzeitplan = "stellplatzzeitplan.php";



# COSA-Dateien --- COSA Files
$dat_vandat 	= "./laive_vandat.c01";
$dat_wettbew 	= "./laive_Wettbew.c01";
$dat_wbteiln 	= "./laive_WbTeiln.c01";
$dat_endli 		= "./laive_Endli.c01";
$dat_wklist 	= "./laive_WkList.c01";

$dat_stamm 		= "./laive_Stamm.c01";
$dat_verein 	= "./laive_Verein.c01";



# Zusatzdateien --- Extended Files
$entryfile="_teilnehmer.htm";                 						#Name der Teilnehmerdatei
$entrybyclubfile="_teilnehmerverein.pdf";							#Name der Datei - TN nach Vereinen
$resultfile="_ergebnisse.htm";                						#Name der Ergebnisdatei
$resultfile1="_ergebnisse1.htm";                					#Name der Ergebnisdatei Tag 1
$resultfile2="_ergebnisse2.htm";                					#Name der Ergebnisdatei Tag 2

$competitionsublink="http://";	# competitionsub /Veranstaltungsseite



$Klassen[10]['Nr']		=	10;
$Klassen[10]['Bez']		=	"Men";
$Klassen[10]['Ident']	=	"Mä";
$Klassen[10]['Abbrev']	=	"M";

$Klassen[11]['Nr']		=	11;
$Klassen[11]['Bez']		=	"Women";
$Klassen[11]['Ident']	=	"Fr";
$Klassen[11]['Abbrev']	=	"W";

$Klassen[12]['Nr']		=	12;
$Klassen[12]['Bez']		=	"Men U23";
$Klassen[12]['Ident']	=	"Ju";
$Klassen[12]['Abbrev']	=	"M U23";

$Klassen[13]['Nr']		=	13;
$Klassen[13]['Bez']		=	"Women U23";
$Klassen[13]['Ident']	=	"Jui";
$Klassen[13]['Abbrev']	=	"W U23";

$Klassen[20]['Nr']		=	20;
$Klassen[20]['Bez']		=	"Junior Men";
$Klassen[20]['Ident']	=	"MJA";
$Klassen[20]['Abbrev']	=	"Jun. M";

$Klassen[20]['Nr']		=	21;
$Klassen[21]['Bez']		=	"Junior Women";
$Klassen[21]['Ident']	=	"WJA";
$Klassen[21]['Abbrev']	=	"Jun. W";

$Klassen[22]['Nr']		=	22;
$Klassen[22]['Bez']		=	"Youth Boys";
$Klassen[22]['Ident']	=	"MJB";
$Klassen[22]['Abbrev']	=	"Boys";

$Klassen[23]['Nr']		=	23;
$Klassen[23]['Bez']		=	"Youth Girls";
$Klassen[23]['Ident']	=	"WJB";
$Klassen[23]['Abbrev']	=	"Girls";

$Klassen[37]['Nr']		=	37;
$Klassen[37]['Bez']		=	"Boys U16";
$Klassen[37]['Ident']	=	"SA";
$Klassen[37]['Abbrev']	=	"B. U16";

$Klassen[38]['Nr']		=	38;
$Klassen[38]['Bez']		=	"M14";
$Klassen[38]['Ident']	=	"M14";
$Klassen[38]['Abbrev']	=	"M14";

$Klassen[39]['Nr']		=	39;
$Klassen[39]['Bez']		=	"M15";
$Klassen[39]['Ident']	=	"M15";
$Klassen[39]['Abbrev']	=	"M15";

$Klassen[34]['Nr']		=	34;
$Klassen[34]['Bez']		=	"Boys U14";
$Klassen[34]['Ident']	=	"SB";
$Klassen[34]['Abbrev']	=	"B. U14";

$Klassen[35]['Nr']		=	35;
$Klassen[35]['Bez']		=	"M12";
$Klassen[35]['Ident']	=	"M12";
$Klassen[35]['Abbrev']	=	"M12";

$Klassen[36]['Nr']		=	36;
$Klassen[36]['Bez']		=	"M13";
$Klassen[36]['Ident']	=	"M13";
$Klassen[36]['Abbrev']	=	"M13";

$Klassen[31]['Nr']		=	31;
$Klassen[31]['Bez']		=	"Boys U12";
$Klassen[31]['Ident']	=	"SC";
$Klassen[31]['Abbrev']	=	"B. U12";

$Klassen[32]['Nr']		=	32;
$Klassen[32]['Bez']		=	"M10";
$Klassen[32]['Ident']	=	"M10";
$Klassen[32]['Abbrev']	=	"M10";

$Klassen[33]['Nr']		=	33;
$Klassen[33]['Bez']		=	"M11";
$Klassen[33]['Ident']	=	"M11";
$Klassen[33]['Abbrev']	=	"M11";

$Klassen[28]['Nr']		=	28;
$Klassen[28]['Bez']		=	"Boys U10";
$Klassen[28]['Ident']	=	"SD";
$Klassen[28]['Abbrev']	=	"B. U10";

$Klassen[29]['Nr']		=	29;
$Klassen[29]['Bez']		=	"M 8";
$Klassen[29]['Ident']	=	"M08";
$Klassen[29]['Abbrev']	=	"M 8";

$Klassen[30]['Nr']		=	30;
$Klassen[30]['Bez']		=	"M 9";
$Klassen[30]['Ident']	=	"M09";
$Klassen[30]['Abbrev']	=	"M 9";

$Klassen[24]['Nr']		=	24;
$Klassen[24]['Bez']		=	"Boys U08";
$Klassen[24]['Ident']	=	"SE";
$Klassen[24]['Abbrev']	=	"B. U08";

$Klassen[88]['Nr']		=	88;
$Klassen[88]['Bez']		=	"M 7";
$Klassen[88]['Ident']	=	"M07";
$Klassen[88]['Abbrev']	=	"M 7";

$Klassen[86]['Nr']		=	86;
$Klassen[86]['Bez']		=	"M 6";
$Klassen[86]['Ident']	=	"M06";
$Klassen[86]['Abbrev']	=	"M 6";

$Klassen[84]['Nr']		=	84;
$Klassen[84]['Bez']		=	"M 5";
$Klassen[84]['Ident']	=	"M05";
$Klassen[84]['Abbrev']	=	"M 5";

$Klassen[82]['Nr']		=	82;
$Klassen[82]['Bez']		=	"M 4";
$Klassen[82]['Ident']	=	"M04";
$Klassen[82]['Abbrev']	=	"M 4";

$Klassen[80]['Nr']		=	80;
$Klassen[80]['Bez']		=	"M 3";
$Klassen[80]['Ident']	=	"M03";
$Klassen[80]['Abbrev']	=	"M 3";

$Klassen[47]['Nr']		=	47;
$Klassen[47]['Bez']		=	"Girls U16";
$Klassen[47]['Ident']	=	"SIA";
$Klassen[47]['Abbrev']	=	"G. U16";

$Klassen[48]['Nr']		=	48;
$Klassen[48]['Bez']		=	"W14";
$Klassen[48]['Ident']	=	"W14";
$Klassen[48]['Abbrev']	=	"W14";

$Klassen[49]['Nr']		=	49;
$Klassen[49]['Bez']		=	"W15";
$Klassen[49]['Ident']	=	"W15";
$Klassen[49]['Abbrev']	=	"W15";

$Klassen[44]['Nr']		=	44;
$Klassen[44]['Bez']		=	"Girls U14";
$Klassen[44]['Ident']	=	"SIB";
$Klassen[44]['Abbrev']	=	"G. U14";

$Klassen[45]['Nr']		=	45;
$Klassen[45]['Bez']		=	"W12";
$Klassen[45]['Ident']	=	"W12";
$Klassen[45]['Abbrev']	=	"W12";

$Klassen[46]['Nr']		=	46;
$Klassen[46]['Bez']		=	"W13";
$Klassen[46]['Ident']	=	"W13";
$Klassen[46]['Abbrev']	=	"W13";

$Klassen[27]['Nr']		=	27;
$Klassen[27]['Bez']		=	"Girls U12";
$Klassen[27]['Ident']	=	"SIC";
$Klassen[27]['Abbrev']	=	"G. U12";

$Klassen[42]['Nr']		=	42;
$Klassen[42]['Bez']		=	"W10";
$Klassen[42]['Ident']	=	"W10";
$Klassen[42]['Abbrev']	=	"W10";

$Klassen[43]['Nr']		=	43;
$Klassen[43]['Bez']		=	"W11";
$Klassen[43]['Ident']	=	"W11";
$Klassen[43]['Abbrev']	=	"W11";

$Klassen[26]['Nr']		=	26;
$Klassen[26]['Bez']		=	"Girls U10";
$Klassen[26]['Ident']	=	"SID";
$Klassen[26]['Abbrev']	=	"G. U10";

$Klassen[40]['Nr']		=	40;
$Klassen[40]['Bez']		=	"W 8";
$Klassen[40]['Ident']	=	"W08";
$Klassen[40]['Abbrev']	=	"W 8";

$Klassen[41]['Nr']		=	41;
$Klassen[41]['Bez']		=	"W 9";
$Klassen[41]['Ident']	=	"W09";
$Klassen[41]['Abbrev']	=	"W 9";

$Klassen[25]['Nr']		=	25;
$Klassen[25]['Bez']		=	"Girls U08";
$Klassen[25]['Ident']	=	"SIE";
$Klassen[25]['Abbrev']	=	"G. U08";

$Klassen[89]['Nr']		=	89;
$Klassen[89]['Bez']		=	"W 7";
$Klassen[89]['Ident']	=	"W07";
$Klassen[89]['Abbrev']	=	"W 7";

$Klassen[87]['Nr']		=	87;
$Klassen[87]['Bez']		=	"W 6";
$Klassen[87]['Ident']	=	"W06";
$Klassen[87]['Abbrev']	=	"W 6";

$Klassen[85]['Nr']		=	85;
$Klassen[85]['Bez']		=	"W 5";
$Klassen[85]['Ident']	=	"W05";
$Klassen[85]['Abbrev']	=	"W 5";

$Klassen[83]['Nr']		=	83;
$Klassen[83]['Bez']		=	"W 4";
$Klassen[83]['Ident']	=	"W04";
$Klassen[83]['Abbrev']	=	"W 4";

$Klassen[81]['Nr']		=	81;
$Klassen[81]['Bez']		=	"W 3";
$Klassen[81]['Ident']	=	"W03";
$Klassen[81]['Abbrev']	=	"W 3";

$Klassen[50]['Nr']		=	50;
$Klassen[50]['Bez']		=	"M30";
$Klassen[50]['Ident']	=	"M30";
$Klassen[50]['Abbrev']	=	"M30";

$Klassen[51]['Nr']		=	51;
$Klassen[51]['Bez']		=	"W30";
$Klassen[51]['Ident']	=	"W30";
$Klassen[51]['Abbrev']	=	"W30";

$Klassen[52]['Nr']		=	52;
$Klassen[52]['Bez']		=	"M35";
$Klassen[52]['Ident']	=	"M35";
$Klassen[52]['Abbrev']	=	"M35";

$Klassen[53]['Nr']		=	53;
$Klassen[53]['Bez']		=	"W35";
$Klassen[53]['Ident']	=	"W35";
$Klassen[53]['Abbrev']	=	"W35";

$Klassen[54]['Nr']		=	54;
$Klassen[54]['Bez']		=	"M40";
$Klassen[54]['Ident']	=	"M40";
$Klassen[54]['Abbrev']	=	"M40";

$Klassen[55]['Nr']		=	55;
$Klassen[55]['Bez']		=	"W40";
$Klassen[55]['Ident']	=	"W40";
$Klassen[55]['Abbrev']	=	"W40";

$Klassen[56]['Nr']		=	56;
$Klassen[56]['Bez']		=	"M45";
$Klassen[56]['Ident']	=	"M45";
$Klassen[56]['Abbrev']	=	"M45";

$Klassen[57]['Nr']		=	57;
$Klassen[57]['Bez']		=	"W45";
$Klassen[57]['Ident']	=	"W45";
$Klassen[57]['Abbrev']	=	"W45";

$Klassen[58]['Nr']		=	58;
$Klassen[58]['Bez']		=	"M50";
$Klassen[58]['Ident']	=	"M50";
$Klassen[58]['Abbrev']	=	"M50";

$Klassen[59]['Nr']		=	59;
$Klassen[59]['Bez']		=	"W50";
$Klassen[59]['Ident']	=	"W50";
$Klassen[59]['Abbrev']	=	"W50";

$Klassen[60]['Nr']		=	60;
$Klassen[60]['Bez']		=	"M55";
$Klassen[60]['Ident']	=	"M55";
$Klassen[60]['Abbrev']	=	"M55";

$Klassen[61]['Nr']		=	61;
$Klassen[61]['Bez']		=	"W55";
$Klassen[61]['Ident']	=	"W55";
$Klassen[61]['Abbrev']	=	"W55";

$Klassen[62]['Nr']		=	62;
$Klassen[62]['Bez']		=	"M60";
$Klassen[62]['Ident']	=	"M60";
$Klassen[62]['Abbrev']	=	"M60";

$Klassen[63]['Nr']		=	63;
$Klassen[63]['Bez']		=	"W60";
$Klassen[63]['Ident']	=	"W60";
$Klassen[63]['Abbrev']	=	"W60";

$Klassen[64]['Nr']		=	64;
$Klassen[64]['Bez']		=	"M65";
$Klassen[64]['Ident']	=	"M65";
$Klassen[64]['Abbrev']	=	"M65";

$Klassen[65]['Nr']		=	65;
$Klassen[65]['Bez']		=	"W65";
$Klassen[65]['Ident']	=	"W65";
$Klassen[65]['Abbrev']	=	"W65";

$Klassen[66]['Nr']		=	66;
$Klassen[66]['Bez']		=	"M70";
$Klassen[66]['Ident']	=	"M70";
$Klassen[66]['Abbrev']	=	"M70";

$Klassen[67]['Nr']		=	67;
$Klassen[67]['Bez']		=	"W70";
$Klassen[67]['Ident']	=	"W70";
$Klassen[67]['Abbrev']	=	"W70";

$Klassen[68]['Nr']		=	68;
$Klassen[68]['Bez']		=	"M75";
$Klassen[68]['Ident']	=	"M75";
$Klassen[68]['Abbrev']	=	"M75";

$Klassen[69]['Nr']		=	69;
$Klassen[69]['Bez']		=	"W75";
$Klassen[69]['Ident']	=	"W75";
$Klassen[69]['Abbrev']	=	"W75";

$Klassen[70]['Nr']		=	70;
$Klassen[70]['Bez']		=	"M80";
$Klassen[70]['Ident']	=	"M80";
$Klassen[70]['Abbrev']	=	"M80";

$Klassen[71]['Nr']		=	71;
$Klassen[71]['Bez']		=	"W80";
$Klassen[71]['Ident']	=	"W80";
$Klassen[71]['Abbrev']	=	"W80";

$Klassen[72]['Nr']		=	72;
$Klassen[72]['Bez']		=	"M85";
$Klassen[72]['Ident']	=	"M85";
$Klassen[72]['Abbrev']	=	"M85";

$Klassen[73]['Nr']		=	73;
$Klassen[73]['Bez']		=	"W85";
$Klassen[73]['Ident']	=	"W85";
$Klassen[73]['Abbrev']	=	"W85";

$Klassen[99]['Nr']		=	99;
$Klassen[99]['Bez']		=	"all categories";
$Klassen[99]['Ident']	=	"all categories";
$Klassen[99]['Abbrev']	=	"all";

# --- Disziplinen-Array ------------------------------------------------------

$Disziplinen[6]['Bez']			=	"30 Metres";
$Disziplinen[6]['Kurz']			=	"30m";
$Disziplinen[6]['Typ']			=	"l";

$Disziplinen[10]['Bez']			=	"50 Metres";
$Disziplinen[10]['Typ']			=	"l";
$Disziplinen[10]['Kurz']		=	"50m";

$Disziplinen[15]['Bez']			=	"60 Metres";
$Disziplinen[15]['Kurz']		=	"60m";
$Disziplinen[15]['Typ']			=	"l";

$Disziplinen[16]['Bez']			=	"60 Metres Heat 1";
$Disziplinen[16]['Kurz']		=	"60m H1";
$Disziplinen[16]['Typ']			=	"l";

$Disziplinen[17]['Bez']			=	"60 Metres Heat 2";
$Disziplinen[17]['Kurz']		=	"60m H2";
$Disziplinen[17]['Typ']			=	"l";

$Disziplinen[20]['Bez']			=	"75 Metres";
$Disziplinen[20]['Kurz']		=	"75m";
$Disziplinen[20]['Typ']			=	"l";

$Disziplinen[24]['Bez']			=	"80 Metres";
$Disziplinen[24]['Kurz']		=	"80m";
$Disziplinen[24]['Typ']			=	"l";

$Disziplinen[30]['Bez']			=	"100 Metres";
$Disziplinen[30]['Kurz']		=	"100m";
$Disziplinen[30]['Typ']			=	"l";

$Disziplinen[31]['Bez']			=	"100 Metres Wheelchair"; #
$Disziplinen[31]['Kurz']		=	"100m wheel."; #
$Disziplinen[31]['Typ']			=	"l"; #

$Disziplinen[35]['Bez']			=	"150 Metres";
$Disziplinen[35]['Kurz']		=	"150m";
$Disziplinen[35]['Typ']			=	"l";

$Disziplinen[40]['Bez']			=	"200 Metres";
$Disziplinen[40]['Kurz']		=	"200m";
$Disziplinen[40]['Typ']			=	"l";

$Disziplinen[41]['Bez']			=	"200 Metres Wheelchair"; #
$Disziplinen[41]['Kurz']		=	"200m wheel."; #
$Disziplinen[41]['Typ']			=	"l"; #

$Disziplinen[45]['Bez']			=	"300 Metres";
$Disziplinen[45]['Kurz']		=	"300m";
$Disziplinen[45]['Typ']			=	"l";

$Disziplinen[50]['Bez']			=	"400 Metres";
$Disziplinen[50]['Kurz']		=	"400m";
$Disziplinen[50]['Typ']			=	"l";

$Disziplinen[51]['Bez']			=	"400 Metres Wheelchair"; #
$Disziplinen[51]['Kurz']		=	"400m wheel."; #
$Disziplinen[51]['Typ']			=	"l"; #

$Disziplinen[53]['Bez']			=	"500 Metres";
$Disziplinen[53]['Kurz']		=	"500m";
$Disziplinen[53]['Typ']			=	"l";

$Disziplinen[55]['Bez']			=	"600 Metres";
$Disziplinen[55]['Kurz']		=	"600m";
$Disziplinen[55]['Typ']			=	"l";

$Disziplinen[60]['Bez']			=	"800 Metres";
$Disziplinen[60]['Kurz']		=	"800m";
$Disziplinen[60]['Typ']			=	"l";

$Disziplinen[61]['Bez']			=	"800 Metres Wheelchair"; #
$Disziplinen[61]['Kurz']		=	"800m wheel."; #
$Disziplinen[61]['Typ']			=	"l"; #

$Disziplinen[70]['Bez']			=	"1000 Metres";
$Disziplinen[70]['Kurz']		=	"1000m";
$Disziplinen[70]['Typ']			=	"l";

$Disziplinen[80]['Bez']			=	"1500 Metres";
$Disziplinen[80]['Kurz']		=	"1500m";
$Disziplinen[80]['Typ']			=	"l";

$Disziplinen[81]['Bez']			=	"1500 Metres Wheelchair"; #
$Disziplinen[81]['Kurz']		=	"1500m wheel."; #
$Disziplinen[81]['Typ']			=	"l"; #

$Disziplinen[90]['Bez']			=	"1 Mile";
$Disziplinen[90]['Kurz']		=	"1 Mile";
$Disziplinen[90]['Typ']			=	"l";

$Disziplinen[100]['Bez']		=	"2000 Metres";
$Disziplinen[100]['Kurz']		=	"2000m";
$Disziplinen[100]['Typ']		=	"l";

$Disziplinen[110]['Bez']		=	"3000 Metres";
$Disziplinen[110]['Kurz']		=	"3000m";
$Disziplinen[110]['Typ']		=	"l";

$Disziplinen[111]['Bez']		=	"3000 Metres Wheelchair";
$Disziplinen[111]['Kurz']		=	"3000m wheel.";
$Disziplinen[111]['Typ']		=	"l";

$Disziplinen[120]['Bez']		=	"5000 Metres";
$Disziplinen[120]['Kurz']		=	"5000m";
$Disziplinen[120]['Typ']		=	"l";

$Disziplinen[121]['Bez']		=	"5000 Metres Wheelchair"; #
$Disziplinen[121]['Kurz']		=	"5000m wheel."; #
$Disziplinen[121]['Typ']		=	"l"; #

$Disziplinen[125]['Bez']		=	"10,000 Metres";
$Disziplinen[125]['Kurz']		=	"10,000m";
$Disziplinen[125]['Typ']		=	"l";

$Disziplinen[126]['Bez']		=	"Half Hour";
$Disziplinen[126]['Kurz']		=	"1/2 h";
$Disziplinen[126]['Typ']		=	"l";

$Disziplinen[127]['Bez']		=	"Half Hour Team";
$Disziplinen[127]['Kurz']		=	"1/2 h Team";
$Disziplinen[127]['Typ']		=	"s";

$Disziplinen[128]['Bez']		=	"1 Hour";
$Disziplinen[128]['Kurz']		=	"1 h";
$Disziplinen[128]['Typ']		=	"l";

$Disziplinen[129]['Bez']		=	"1 Hour Team";
$Disziplinen[129]['Kurz']		=	"1 h Team";
$Disziplinen[129]['Typ']		=	"s";

$Disziplinen[131]['Bez']		=	"5 Kilometres";
$Disziplinen[131]['Kurz']		=	"5km";
$Disziplinen[131]['Typ']		=	"w";

$Disziplinen[132]['Bez']		=	"5 Kilometres Team";
$Disziplinen[132]['Kurz']		=	"5km Team";
$Disziplinen[132]['Typ']		=	"w";

$Disziplinen[133]['Bez']		=	"7,5 Kilometres";
$Disziplinen[133]['Kurz']		=	"7,5km";
$Disziplinen[133]['Typ']		=	"w";

$Disziplinen[134]['Bez']		=	"7,5 Kilometres Team";
$Disziplinen[134]['Kurz']		=	"7,5km Team";
$Disziplinen[134]['Typ']		=	"w";

$Disziplinen[135]['Bez']		=	"10 Kilometres";
$Disziplinen[135]['Kurz']		=	"10km";
$Disziplinen[135]['Typ']		=	"w";

$Disziplinen[136]['Bez']		=	"10 Kilometres Team";
$Disziplinen[136]['Kurz']		=	"10km Team";
$Disziplinen[136]['Typ']		=	"w";

$Disziplinen[137]['Bez']		=	"15 Kilometres";
$Disziplinen[137]['Kurz']		=	"15km";
$Disziplinen[137]['Typ']		=	"w";

$Disziplinen[138]['Bez']		=	"15 Kilometres Team";
$Disziplinen[138]['Kurz']		=	"15km Team";
$Disziplinen[138]['Typ']		=	"w";

$Disziplinen[140]['Bez']		=	"25 Kilometres";
$Disziplinen[140]['Kurz']		=	"25km";
$Disziplinen[140]['Typ']		=	"w";

$Disziplinen[141]['Bez']		=	"25 Kilometres Team";
$Disziplinen[141]['Kurz']		=	"25km Team";
$Disziplinen[141]['Typ']		=	"w";

$Disziplinen[148]['Bez']		=	"Half Marathon";
$Disziplinen[148]['Kurz']		=	"1/2 Marath.";
$Disziplinen[148]['Typ']		=	"w";

$Disziplinen[149]['Bez']		=	"Half Marathon Team";
$Disziplinen[149]['Kurz']		=	"1/2 Marath. Team";
$Disziplinen[149]['Typ']		=	"w";

$Disziplinen[150]['Bez']		=	"Marathon";
$Disziplinen[150]['Kurz']		=	"Marath.";
$Disziplinen[150]['Typ']		=	"w";

$Disziplinen[151]['Bez']		=	"Marathon Team";
$Disziplinen[151]['Kurz']		=	"Marath. Team";
$Disziplinen[151]['Typ']		=	"w";

$Disziplinen[153]['Bez']		=	"100 Kilometres";
$Disziplinen[153]['Kurz']		=	"100km";
$Disziplinen[153]['Typ']		=	"w";

$Disziplinen[154]['Bez']		=	"100 Kilometres Team";
$Disziplinen[154]['Kurz']		=	"100km Team";
$Disziplinen[154]['Typ']		=	"w";

$Disziplinen[156]['Bez']		=	"5 x 10km Relay";
$Disziplinen[156]['Kurz']		=	"5x10km";
$Disziplinen[156]['Typ']		=	"w";

$Disziplinen[159]['Bez']		=	"50 Metres Hurdles";
$Disziplinen[159]['Kurz']		=	"50m H.";
$Disziplinen[159]['Typ']		=	"l";

$Disziplinen[160]['Bez']		=	"60 Metres Hurdles";
$Disziplinen[160]['Kurz']		=	"60m H.";
$Disziplinen[160]['Typ']		=	"l";

$Disziplinen[161]['Bez']		=	"60 Metres Hurdles Heat 1";
$Disziplinen[161]['Kurz']		=	"60m H. H1";
$Disziplinen[161]['Typ']		=	"l";

$Disziplinen[162]['Bez']		=	"60 Metres Hurdles Heat 2";
$Disziplinen[162]['Kurz']		=	"60m H. H2";
$Disziplinen[162]['Typ']		=	"l";

$Disziplinen[170]['Bez']		=	"80 Metres Hurdles";
$Disziplinen[170]['Kurz']		=	"80m H.";
$Disziplinen[170]['Typ']		=	"l";

$Disziplinen[180]['Bez']		=	"100 Metres Hurdles";
$Disziplinen[180]['Kurz']		=	"100m H.";
$Disziplinen[180]['Typ']		=	"l";

$Disziplinen[190]['Bez']		=	"110 Metres Hurdles";
$Disziplinen[190]['Kurz']		=	"110m H.";
$Disziplinen[190]['Typ']		=	"l";

$Disziplinen[195]['Bez']		=	"300 Metres Hurdles";
$Disziplinen[195]['Kurz']		=	"300m H.";
$Disziplinen[195]['Typ']		=	"l";

$Disziplinen[200]['Bez']		=	"400 Metres Hurdles";
$Disziplinen[200]['Kurz']		=	"400m H.";
$Disziplinen[200]['Typ']		=	"l";

$Disziplinen[210]['Bez']		=	"1500 Metres Steeplechase";
$Disziplinen[210]['Kurz']		=	"1500m SC";
$Disziplinen[210]['Typ']		=	"l";

$Disziplinen[220]['Bez']		=	"2000 Metres Steeplechase";
$Disziplinen[220]['Kurz']		=	"2000m SC";
$Disziplinen[220]['Typ']		=	"l";

$Disziplinen[230]['Bez']		=	"3000 Metres Steeplechase";
$Disziplinen[230]['Kurz']		=	"3000m SC";
$Disziplinen[230]['Typ']		=	"l";

$Disziplinen[240]['Bez']		=	"4x50 Metres Relay";
$Disziplinen[240]['Kurz']		=	"4x50m";
$Disziplinen[240]['Typ']		=	"s";

$Disziplinen[250]['Bez']		=	"4x75 Metres Relay";
$Disziplinen[250]['Kurz']		=	"4x75m";
$Disziplinen[250]['Typ']		=	"s";

$Disziplinen[260]['Bez']		=	"4x100 Metres Relay";
$Disziplinen[260]['Kurz']		=	"4x100m";
$Disziplinen[260]['Typ']		=	"s";

$Disziplinen[261]['Bez']		=	"4 x 100 Metres Wheelchair";
$Disziplinen[261]['Kurz']		=	"4x100 wheel.";
$Disziplinen[261]['Typ']		=	"s";

$Disziplinen[270]['Bez']		=	"4x200 Metres Relay";
$Disziplinen[270]['Kurz']		=	"4x200m";
$Disziplinen[270]['Typ']		=	"s";

$Disziplinen[280]['Bez']		=	"4x400 Metres Relay";
$Disziplinen[280]['Kurz']		=	"4x400m";
$Disziplinen[280]['Typ']		=	"s";

$Disziplinen[281]['Bez']		=	"4 x 400 Metres Wheelchair";
$Disziplinen[281]['Kurz']		=	"4x400 wheel.";
$Disziplinen[281]['Typ']		=	"s";

$Disziplinen[290]['Bez']		=	"3x800 Metres Relay";
$Disziplinen[290]['Kurz']		=	"3x800m";
$Disziplinen[290]['Typ']		=	"s";

$Disziplinen[300]['Bez']		=	"4x800 Metres Relay";
$Disziplinen[300]['Kurz']		=	"4x800m";
$Disziplinen[300]['Typ']		=	"s";

$Disziplinen[310]['Bez']		=	"3x1000 Metres Relay";
$Disziplinen[310]['Kurz']		=	"3x1000m";
$Disziplinen[310]['Typ']		=	"s";

$Disziplinen[320]['Bez']		=	"4x1500 Metres Relay";
$Disziplinen[320]['Kurz']		=	"4x1500m";
$Disziplinen[320]['Typ']		=	"s";

$Disziplinen[321]['Bez']		=	"Olympic Relay";
$Disziplinen[321]['Kurz']		=	"OLY Relay";
$Disziplinen[321]['Typ']		=	"s";

$Disziplinen[322]['Bez']		=	"Swedish Relay";
$Disziplinen[322]['Kurz']		=	"SWE Relay";
$Disziplinen[323]['Bez']		=	"Medley Relay";
$Disziplinen[323]['Kurz']		=	"Medl. Relay";
$Disziplinen[323]['Typ']		=	"s";

$Disziplinen[324]['Bez']		=	"Commute Relay";
$Disziplinen[324]['Kurz']		=	"Com. Relay";
$Disziplinen[324]['Typ']		=	"s";

$Disziplinen[325]['Bez']		=	"1000 Metres Race Walk";
$Disziplinen[325]['Kurz']		=	"1000m RW";
$Disziplinen[325]['Typ']		=	"l";

$Disziplinen[330]['Bez']		=	"2000 Metres Race Walk";
$Disziplinen[330]['Kurz']		=	"2000m RW";
$Disziplinen[330]['Typ']		=	"l";

$Disziplinen[340]['Bez']		=	"3000 Metres Race Walk";
$Disziplinen[340]['Kurz']		=	"3000m RW";
$Disziplinen[340]['Typ']		=	"l";

$Disziplinen[350]['Bez']		=	"5000 Metres Race Walk";
$Disziplinen[350]['Kurz']		=	"5000m RW";
$Disziplinen[350]['Typ']		=	"l";

$Disziplinen[352]['Bez']		=	"10,000 Metres Race Walk";
$Disziplinen[352]['Kurz']		=	"10,000m RW";
$Disziplinen[352]['Typ']		=	"l";

$Disziplinen[354]['Bez']		=	"20,000 Metres Race Walk";
$Disziplinen[354]['Kurz']		=	"20,000m RW";
$Disziplinen[354]['Typ']		=	"l";

$Disziplinen[355]['Bez']		=	"1 Kilometre Race Walk";
$Disziplinen[355]['Kurz']		=	"1km RW";
$Disziplinen[355]['Typ']		=	"w";

$Disziplinen[356]['Bez']		=	"2 Kilometres Race Walk";
$Disziplinen[356]['Kurz']		=	"2km RW";
$Disziplinen[356]['Typ']		=	"w";

$Disziplinen[358]['Bez']		=	"3 Kilometres Race Walk";
$Disziplinen[358]['Kurz']		=	"3km RW";
$Disziplinen[358]['Typ']		=	"w";

$Disziplinen[359]['Bez']		=	"3 Kilometres Race Walk Team";
$Disziplinen[359]['Kurz']		=	"3km RW Team";
$Disziplinen[359]['Typ']		=	"w";

$Disziplinen[360]['Bez']		=	"5 Kilometres Race Walk";
$Disziplinen[360]['Kurz']		=	"5km RW";
$Disziplinen[360]['Typ']		=	"w";

$Disziplinen[361]['Bez']		=	"5 Kilometres Race Walk Team";
$Disziplinen[361]['Kurz']		=	"5km RW Team";
$Disziplinen[361]['Typ']		=	"w";

$Disziplinen[370]['Bez']		=	"10 Kilometres Race Walk";
$Disziplinen[370]['Kurz']		=	"10km RW";
$Disziplinen[370]['Typ']		=	"w";

$Disziplinen[371]['Bez']		=	"10km Kilometres Race Walk Team";
$Disziplinen[371]['Kurz']		=	"10km RW Team";
$Disziplinen[371]['Typ']		=	"w";

$Disziplinen[380]['Bez']		=	"20 Kilometres Race Walk";
$Disziplinen[380]['Kurz']		=	"20km RW";
$Disziplinen[380]['Typ']		=	"w";

$Disziplinen[381]['Bez']		=	"20 Kilometres Race Walk Team";
$Disziplinen[381]['Kurz']		=	"20km RW Team";
$Disziplinen[381]['Typ']		=	"w";

$Disziplinen[386]['Bez']		=	"30 Kilometres Race Walk";
$Disziplinen[386]['Kurz']		=	"30km RW";
$Disziplinen[386]['Typ']		=	"w";

$Disziplinen[387]['Bez']		=	"30km Kilometres Race Walk Team";
$Disziplinen[387]['Kurz']		=	"30km RW Team";
$Disziplinen[387]['Typ']		=	"w";

$Disziplinen[390]['Bez']		=	"50 Kilometres Race Walk";
$Disziplinen[390]['Kurz']		=	"50km RW";
$Disziplinen[390]['Typ']		=	"w";

$Disziplinen[391]['Bez']		=	"50km Kilometres Race Walk Team";
$Disziplinen[391]['Kurz']		=	"50km RW Team";
$Disziplinen[391]['Typ']		=	"w";

$Disziplinen[510]['Bez']		=	"Heigh Jump";
$Disziplinen[510]['Typ']		=	"h";
$Disziplinen[510]['Kurz']		=	"HJ";

$Disziplinen[520]['Bez']		=	"Pole Vault";
$Disziplinen[520]['Kurz']		=	"PV";
$Disziplinen[520]['Typ']		=	"h";

$Disziplinen[530]['Bez']		=	"Long Jump";
$Disziplinen[530]['Kurz']		=	"LJ";
$Disziplinen[530]['Typ']		=	"t";

$Disziplinen[535]['Bez']		=	"Standing Long Jump";
$Disziplinen[535]['Kurz']		=	"Stand. LJ";
$Disziplinen[535]['Typ']		=	"t";

$Disziplinen[540]['Bez']		=	"Triple Jump";
$Disziplinen[540]['Kurz']		=	"TJ";
$Disziplinen[540]['Typ']		=	"t";

$Disziplinen[610]['Bez']		=	"Shoot Put";
$Disziplinen[610]['Kurz']		=	"SP";
$Disziplinen[610]['Typ']		=	"t";

$Disziplinen[611]['Bez']		=	"Stone";
$Disziplinen[611]['Kurz']		=	"Stone";
$Disziplinen[611]['Typ']		=	"t";

$Disziplinen[612]['Bez']		=	"Shot Put Wheelchair"; #
$Disziplinen[612]['Kurz']		=	"SP wheel."; #
$Disziplinen[612]['Typ']		=	"t"; #

$Disziplinen[620]['Bez']		=	"Discus Throw";
$Disziplinen[620]['Kurz']		=	"DT";
$Disziplinen[620]['Typ']		=	"t";

$Disziplinen[621]['Bez']		=	"Discus Throw Wheelcair"; #
$Disziplinen[621]['Kurz']		=	"DT wheel."; #
$Disziplinen[621]['Typ']		=	"t"; #

$Disziplinen[630]['Bez']		=	"Hammer Throw";
$Disziplinen[630]['Kurz']		=	"HT";
$Disziplinen[630]['Typ']		=	"t";

$Disziplinen[640]['Bez']		=	"Javelin Throw";
$Disziplinen[640]['Kurz']		=	"JT";
$Disziplinen[640]['Typ']		=	"t";

$Disziplinen[641]['Bez']		=	"Javelin Throw Wheelchair"; #
$Disziplinen[641]['Kurz']		=	"JT wheel."; #
$Disziplinen[641]['Typ']		=	"t"; #

$Disziplinen[644]['Bez']		=	"Club Throw 397g";
$Disziplinen[644]['Kurz']		=	"CT";
$Disziplinen[644]['Typ']		=	"t";

$Disziplinen[650]['Bez']		=	"Ball Throwing 200g";
$Disziplinen[650]['Kurz']		=	"BT 200g";
$Disziplinen[650]['Typ']		=	"t";

$Disziplinen[660]['Bez']		=	"Ball Throwing 80g";
$Disziplinen[660]['Kurz']		=	"BT 80g";
$Disziplinen[660]['Typ']		=	"t";

$Disziplinen[670]['Bez']		=	"Schleuderball";
$Disziplinen[670]['Kurz']		=	"SchleuB";
$Disziplinen[670]['Typ']		=	"t";

$Disziplinen[690]['Bez']		=	"Weight Throw";
$Disziplinen[690]['Kurz']		=	"WT";
$Disziplinen[690]['Typ']		=	"t";

$Disziplinen[710]['Bez']		=	"Triathlon";
$Disziplinen[710]['Kurz']		=	"TRI";
$Disziplinen[710]['Typ']		=	"m";

$Disziplinen[711]['Bez']		=	"Triathlon Team";
$Disziplinen[711]['Kurz']		=	"TRI Team";
$Disziplinen[711]['Typ']		=	"b";

$Disziplinen[720]['Bez']		=	"Tetrathlon";
$Disziplinen[720]['Kurz']		=	"TET";
$Disziplinen[720]['Typ']		=	"m";

$Disziplinen[721]['Bez']		=	"Tetrathlon Team";
$Disziplinen[721]['Kurz']		=	"TET Team";
$Disziplinen[721]['Typ']		=	"b";

$Disziplinen[730]['Bez']		=	"Pentathlon";
$Disziplinen[730]['Kurz']		=	"PEN";
$Disziplinen[730]['Typ']		=	"m";

$Disziplinen[731]['Bez']		=	"Pentathlon Team";
$Disziplinen[731]['Kurz']		=	"PEN Team";
$Disziplinen[731]['Typ']		=	"b";

$Disziplinen[740]['Bez']		=	"Hexathlon";
$Disziplinen[740]['Kurz']		=	"HEX";
$Disziplinen[740]['Typ']		=	"m";

$Disziplinen[741]['Bez']		=	"Hexathlon Team";
$Disziplinen[741]['Kurz']		=	"HEX Team";
$Disziplinen[741]['Typ']		=	"b";

$Disziplinen[750]['Bez']		=	"Heptathlon";
$Disziplinen[750]['Kurz']		=	"HEP";
$Disziplinen[750]['Typ']		=	"m";

$Disziplinen[751]['Bez']		=	"Heptathlon Team";
$Disziplinen[751]['Kurz']		=	"HEP Team";
$Disziplinen[751]['Typ']		=	"b";

$Disziplinen[765]['Bez']		=	"Enneathlon";
$Disziplinen[765]['Kurz']		=	"ENN";
$Disziplinen[765]['Typ']		=	"m";

$Disziplinen[766]['Bez']		=	"Enneathlon Team";
$Disziplinen[766]['Kurz']		=	"ENN Team";
$Disziplinen[766]['Typ']		=	"b";

$Disziplinen[770]['Bez']		=	"Decathlon";
$Disziplinen[770]['Kurz']		=	"DEC";
$Disziplinen[770]['Typ']		=	"m";

$Disziplinen[771]['Bez']		=	"Decathlon Team";
$Disziplinen[771]['Kurz']		=	"DEC Team";
$Disziplinen[771]['Typ']		=	"b";

$Disziplinen[790]['Bez']		=	"Throwing Pentathlon";
$Disziplinen[790]['Kurz']		=	"Throw. PEN";
$Disziplinen[790]['Typ']		=	"m";

$Disziplinen[791]['Bez']		=	"Throwing Pentathlon Team";
$Disziplinen[791]['Kurz']		=	"Throw. PEN Team";
$Disziplinen[791]['Typ']		=	"b";

$Disziplinen[785]['Bez']		=	"German Pentathlon Basic";
$Disziplinen[785]['Kurz']		=	"GER PEN B";
$Disziplinen[785]['Typ']		=	"m";

$Disziplinen[780]['Bez']		=	"German Pentathlon Dash/Jump";
$Disziplinen[780]['Kurz']		=	"GER PEN D/J";
$Disziplinen[780]['Typ']		=	"m";

$Disziplinen[782]['Bez']		=	"German Pentathlon Race";
$Disziplinen[782]['Kurz']		=	"GER PEN R";
$Disziplinen[782]['Typ']		=	"m";

$Disziplinen[784]['Bez']		=	"German Pentathlon Throw";
$Disziplinen[784]['Kurz']		=	"GER PEN T";
$Disziplinen[784]['Typ']		=	"m";

$Disziplinen[788]['Bez']		=	"German Pentathlon Team";
$Disziplinen[788]['Kurz']		=	"GER PEN Team";
$Disziplinen[788]['Typ']		=	"m";

$Disziplinen[801]['Bez']		=	"DMM Gruppe 1";
$Disziplinen[801]['Kurz']		=	"DMM G1";
$Disziplinen[801]['Typ']		=	"d";

$Disziplinen[803]['Bez']		=	"DMM Gruppe 2";
$Disziplinen[803]['Kurz']		=	"DMM G2";
$Disziplinen[803]['Typ']		=	"d";

$Disziplinen[805]['Bez']		=	"DMM Gruppe 3";
$Disziplinen[805]['Kurz']		=	"DMM G3";
$Disziplinen[805]['Typ']		=	"d";

$Disziplinen[816]['Bez']		=	"Gruppe 1";
$Disziplinen[816]['Kurz']		=	"Gruppe 1";
$Disziplinen[816]['Typ']		=	"d";

$Disziplinen[817]['Bez']		=	"Gruppe 2";
$Disziplinen[817]['Kurz']		=	"Gruppe 2";
$Disziplinen[817]['Typ']		=	"d";

$Disziplinen[818]['Bez']		=	"Gruppe 3";
$Disziplinen[818]['Kurz']		=	"Gruppe 3";
$Disziplinen[818]['Typ']		=	"d";

$Disziplinen[819]['Bez']		=	"Gruppe 4";
$Disziplinen[819]['Kurz']		=	"Gruppe 4";
$Disziplinen[819]['Typ']		=	"d";

$Disziplinen[824]['Bez']		=	"M70 DAMM";
$Disziplinen[824]['Kurz']		=	"M70 DAMM";
$Disziplinen[824]['Typ']		=	"d";

$Disziplinen[828]['Bez']		=	"W60 DAMM";
$Disziplinen[828]['Kurz']		=	"W60 DAMM";
$Disziplinen[828]['Typ']		=	"d";

$Disziplinen[840]['Bez']		=	"I JtfO";
$Disziplinen[840]['Kurz']		=	"I JtfO";
$Disziplinen[840]['Typ']		=	"j";

$Disziplinen[841]['Bez']		=	"II JtfO";
$Disziplinen[841]['Kurz']		=	"II JtfO";
$Disziplinen[841]['Typ']		=	"j";

$Disziplinen[843]['Bez']		=	"III JtfO";
$Disziplinen[843]['Kurz']		=	"III JtfO";
$Disziplinen[844]['Typ']		=	"j";

$Disziplinen[844]['Bez']		=	"IV JtfO";
$Disziplinen[844]['Kurz']		=	"IV JtfO";
$Disziplinen[844]['Typ']		=	"j";

$Disziplinen[845]['Bez']		=	"IV/1JtfO";
$Disziplinen[845]['Kurz']		=	"IV/1JtfO";
$Disziplinen[845]['Typ']		=	"j";

$Disziplinen[846]['Bez']		=	"IV/2JtfO";
$Disziplinen[846]['Kurz']		=	"IV/2JtfO";
$Disziplinen[846]['Typ']		=	"j";

$Disziplinen[0]['Bez']		=	"Own Event";
$Disziplinen[0]['Kurz']		=	"Own Evt.";
$Disziplinen[0]['Typ']		=	"e";


# --- Rundentypen -------------------Rounds----------------------------

$RundeTyp0 = "final";
$RundeTyp1 = "heats";
$RundeTyp2 = "semi-finals";
$RundeTyp3 = "time-heats";

$RundeTyp99 = " ";

$RundeTyp4 = "elimination";
$RundeTyp5 = "qualification"; 
$RundeTyp6 = "time-races";
$RundeTyp7 = "finals A/B";

$RundeTyp8 = "heat";
# belegt für MK 9

$RundeTypa = "after 1 event";
$RundeTypb = "after 2 events";
$RundeTypc = "after 3 events";
$RundeTypd = "after 4 events";
$RundeType = "after 5 events";
$RundeTypf = "after 6 events";
$RundeTypg = "after 7 events";
$RundeTyph = "after 8 events";
$RundeTypi = "after 9 events";

# Abbrev for Types
$RoundTypAbbrev[0]	= "F";
$RoundTypAbbrev[1]	= "H";
$RoundTypAbbrev[2]	= "SF";
$RoundTypAbbrev[3]	= "H";
$RoundTypAbbrev[4]	= "E";
$RoundTypAbbrev[5]	= "Q";
$RoundTypAbbrev[6]	= "F";
$RoundTypAbbrev[7]	= "A/B";
$RoundTypAbbrev[8]	= "Inv";
$RoundTypAbbrev[99]	= "";

# --- Typ-Typen ---------------------Typs---------------------------

$TypTyp1 = "Result list";
$TypTyp2 = "Entry list";
$TypTyp3 = "Intermediate Results";
$TypTyp4 = "Start list";
$TypTyp5 = "";
$TypTyp6 = "advanced by heats";
$TypTyp7 = "advanced by semi-finals";
$TypTyp8 = "event finished";

# --- Typ-Typen Abkürzungen ---------------------Typs Abbrev ------
$ListTypAbbrev[1] = "&nbsp;R&nbsp;";
$ListTypAbbrev[2] = "&nbsp;E&nbsp;";
$ListTypAbbrev[3] = "&nbsp;I&nbsp;";
$ListTypAbbrev[4] = "&nbsp;S&nbsp;";
$ListTypAbbrev[5] = "";
$ListTypAbbrev[6] = "";
$ListTypAbbrev[7] = "";
$ListTypAbbrev[8] = "";


# --- arabische in römische Zahlen--------------------------

$Roemisch[1] = "I";
$Roemisch[2] = "II";
$Roemisch[3] = "III";
$Roemisch[4] = "IV";
$Roemisch[5] = "V";
$Roemisch[6] = "VI";
$Roemisch[7] = "VII";
$Roemisch[8] = "VIII";
$Roemisch[9] = "IX";
$Roemisch[10] = "X";
$Roemisch[11] = "XI";
$Roemisch[12] = "XII";
$Roemisch[13] = "XIII";
$Roemisch[14] = "XIV";
$Roemisch[15] = "XV";
$Roemisch[16] = "XVI";
$Roemisch[17] = "XVII";
$Roemisch[18] = "XVIII";
$Roemisch[19] = "IX";
$Roemisch[20] = "XX";

# --- Wochentage --------------------------
$Wochentage[0] = "Sunday";
$Wochentage[1] = "Monday";
$Wochentage[2] = "Tuesday";
$Wochentage[3] = "Wednesday";
$Wochentage[4] = "Thursday";
$Wochentage[5] = "Friday";
$Wochentage[6] = "Saturday";

$LengthForAbbrevDaysOfWeek = 3;


break;


} # Ende switch Sprache

### Veranstaltungsdaten aus COSAWIN (vandat.c01) übernehmen / Read competitiondata from cosawin

#$vandat_datei = file($dat_vandat);
if(file_exists($dat_vandat)) {
$meine_vandat_datei = file_get_contents($dat_vandat);
   
   
   $Veranstaltung = trim(substr($meine_vandat_datei, 288, 90));
   $Veranstalter = trim(substr($meine_vandat_datei, 418, 50));
   $Ort = trim(substr($meine_vandat_datei, 518, 50));
   $Stadion = trim(substr($meine_vandat_datei, 585, 50));
   $Tag1 = trim(substr($meine_vandat_datei, 635, 10));
   $Tag2 = trim(substr($meine_vandat_datei, 645, 10));
   $Tag3 = trim(substr($meine_vandat_datei, 655, 10));
   $Tag4 = trim(substr($meine_vandat_datei, 665, 10));
   
   
   # Date for timetable file for score board
   $DayTTBoard[1] = trim(substr($Tag1, 8, 2)) . trim(substr($Tag1, 3, 2)) . trim(substr($Tag1, 0, 2));
   $DayTTBoard[2] = trim(substr($Tag2, 8, 2)) . trim(substr($Tag2, 3, 2)) . trim(substr($Tag2, 0, 2));
   $DayTTBoard[3] = trim(substr($Tag3, 8, 2)) . trim(substr($Tag3, 3, 2)) . trim(substr($Tag3, 0, 2));
   $DayTTBoard[4] = trim(substr($Tag4, 8, 2)) . trim(substr($Tag4, 3, 2)) . trim(substr($Tag4, 0, 2));
   
   
   
   
   # Cup-Scoring / Pokalwertung
   $CupScoringON = trim(substr($meine_vandat_datei, 136, 1));
   
   
   # DSB-Modus /ICP Mode
   $IPCModeON = trim(substr($meine_vandat_datei, 197, 1));
   
   If($IPCModeON == 1) { 
	$IPCMode = $txtIPCMode;
	$txt_laivefuss1 = "$txt_laive (Version: $ResultTickerVersion $IPCMode - $ResultTickerErsteller - <a href='http://kwenzel.net/Special_Contact' target='_blank'>laive@kwenzel.net</a>)";
   }
   
   
   #Datum im Standardformat YYYY-MM-DD
   $meta_datumstandard = trim(substr($Tag1, 6, 4))."-".trim(substr($Tag1, 3, 2))."-".trim(substr($Tag1, 0, 2));
   
   # Datums-Array
   if ($Tag1 != "") {$tage[1] = $Tag1;}
   if ($Tag2 != "") {$tage[2] = $Tag2;}
   if ($Tag3 != "") {$tage[3] = $Tag3;}
   if ($Tag4 != "") {$tage[4] = $Tag4;}
   
   # UNIX-Zeitstempel zu den Tagen
   if ($Tag1 != "") {$TageUnix[1] = mktime(0,0,0,(int)substr($Tag1, 3, 2),(int)substr($Tag1, 0, 2),(int)substr($Tag1, 6, 4));}
   if ($Tag2 != "") {$TageUnix[2] = mktime(0,0,0,(int)substr($Tag2, 3, 2),(int)substr($Tag2, 0, 2),(int)substr($Tag2, 6, 4));}
   if ($Tag3 != "") {$TageUnix[3] = mktime(0,0,0,(int)substr($Tag3, 3, 2),(int)substr($Tag3, 0, 2),(int)substr($Tag3, 6, 4));}
   if ($Tag4 != "") {$TageUnix[4] = mktime(0, 0,0,(int)substr($Tag4, 3, 2),(int)substr($Tag4, 0, 2),(int)substr($Tag4, 6, 4));}
   
   # Ermitteln, ob gerade einer der Tage ist. / Which day is today
   $serverdatum = date("d.m.Y");
   
   switch($serverdatum) {
   
	case $tage[1]:
		$starttag = 1;
	break;
	case $tage[2]:
		$starttag = 2;
	break;
	case $tage[3]:
		$starttag = 3;
	break;
	case $tage[4]:
		$starttag = 4;
	break;
   default:
	$starttag = 1;
   }
   
	# Datum für die Überschrift / Date for Headline
	if(empty($Tag4) == false) {
		$DatumUeberschrift = $txt_vom." ".$Tag1."-".$Tag4;
	}
	if(empty($Tag4)) {
		$DatumUeberschrift = $txt_vom." ".$Tag1."-".$Tag3;
	}
	
	if(empty($Tag3)) {
		$DatumUeberschrift = $txt_am." ".$Tag1."/".$Tag2;
	}
	
	if(empty($Tag2)) {
		$DatumUeberschrift = $txt_am." ".$Tag1;
	}
   $Kopfzeile1 = $Veranstalter;
   $Kopfzeile2 = $Veranstaltung;
   $Kopfzeile3 = $DatumUeberschrift." ".$txt_in." ".$Ort." - ".$Stadion;  

	# Wertungsgruppen
	$wertungsgruppen[1] = trim(substr($meine_vandat_datei, 1876, 35));
	$wertungsgruppen[2] = trim(substr($meine_vandat_datei, 1911, 35));
	$wertungsgruppen[3] = trim(substr($meine_vandat_datei, 1946, 35));
	$wertungsgruppen[4] = trim(substr($meine_vandat_datei, 1981, 35));
	$wertungsgruppen[5] = trim(substr($meine_vandat_datei, 2016, 35));
	$wertungsgruppen[6] = trim(substr($meine_vandat_datei, 2051, 35));
	$wertungsgruppen[7] = trim(substr($meine_vandat_datei, 2086, 35));
   
	
	# Kennzeichen zur Veranstaltungssteuerung
	$TmpKennzeichenSteuerung = strtolower(trim(substr($meine_vandat_datei, 6675, 404)));
	
	# Startnummern ausstellen
	if(strpos($TmpKennzeichenSteuerung,"#s0")!==false) {$StartnummernAn = 0;}; 
	if(strpos($TmpKennzeichenSteuerung,"#S0")!==false) {$StartnummernAn = 0;};
	
	# Zeitplanverzug anschalten
	 
	if(strpos($TmpKennzeichenSteuerung,"#v")!==false) {$zeitplanverzug = trim(substr($TmpKennzeichenSteuerung, strpos($TmpKennzeichenSteuerung,"#v") + 2, 3))*1;}; 
	if(strpos($TmpKennzeichenSteuerung,"#V")!==false) {$zeitplanverzug = trim(substr($TmpKennzeichenSteuerung, strpos($TmpKennzeichenSteuerung,"#V") + 2, 3))*1;};
	
	# Stellplatzzeitplan anzeigen
	if(strpos($TmpKennzeichenSteuerung,"#spz0")!==false) {$StellplatzzeitplanAn = 0;}; 
	if(strpos($TmpKennzeichenSteuerung,"#SPZ0")!==false) {$StellplatzzeitplanAn = 0;};
	
	if(strpos($TmpKennzeichenSteuerung,"#spz1")!==false) {$StellplatzzeitplanAn = 1;}; 
	if(strpos($TmpKennzeichenSteuerung,"#SPZ1")!==false) {$StellplatzzeitplanAn = 1;};
   
   # Endung für Ergebnisliste DBS
   if(strpos($TmpKennzeichenSteuerung, "#dbse")!==false) {$IPCResultListFileExtention = trim(substr($TmpKennzeichenSteuerung, strpos($TmpKennzeichenSteuerung, "#dbse") + 5, 3));}; 
   
   # Final Confirmation Mode / Stellplatzkarten-Abgabe in Teilnehmerlisten anzeigen
   if(strpos($TmpKennzeichenSteuerung,"#spk0")!==false) {$FinalConfirmationOn = 0;}
   if(strpos($TmpKennzeichenSteuerung,"#spk1")!==false) {$FinalConfirmationOn = 1;}
   
   # Texte
   $txt_hinweis_zeitplanverzug ="Derzeit existiert ein Zeitplanverzug von ca. ".$zeitplanverzug." Minuten zu den hier geplanten Startzeiten.";
  }
   # ------------------------------------------------------------------------
if(empty($_GET["sub"])) {

	if(file_exists($dat_zeitplan) && file_exists($dat_wettbew)) {
	
		$_GET["sub"] = $dat_zeitplan;
		$_GET["tag"] = $starttag;
	}
	else {
		$_GET["sub"] = $dat_uebersicht;
	}
}


   
   
   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php echo $txt_laive . " - ".$Kopfzeile2." ".$DatumUeberschrift ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		
<?php
		# Refresh- und Expire-Zeiten festlegen
		switch($_GET["sub"]) {
			default:
			
			break;
			case "zeitplan.php":
			case "uebersicht.php":
				echo '<meta http-equiv="refresh" content="'.$AktualisierenLaiveZeit.'">';
				echo '<meta http-equiv="expires" content="'.$AktualisierenLaiveZeit.'">';
			break;
			
			case "startlistenerstellen.php":
				echo '<meta http-equiv="refresh" content="'.$AktualisierenLaiveAdminZeit.'">';
				echo '<meta http-equiv="expires" content="'.$AktualisierenLaiveAdminZeit.'">';
			break;	
		}
			
?>
		<meta name="robots" content="all">
		
		<meta http-equiv="Content-Style-Type" content="text/css">
		<meta name="description" content="<?php echo $txt_meta_beschreibung." ".$Veranstaltung." ".$Kopfzeile3;?>">
		<meta name="author" content="<?php echo $Veranstalter; ?>">
		<meta name="author" content="<?php echo $ResultTickerErsteller; ?>">
		<meta name="keywords" content="Leichtathletik, Live, Ergebnisse, LaIVE, Results, Athletics, Track and Field, Sports, <?php echo $Veranstalter.", ".$Veranstaltung.", ".$Ort.", ".$DatumUeberschrift;?>">
		<meta name="date" content="<?php echo $meta_datumstandard;?>T00:00:01+02:00">
		<style type="text/css">

a:link,a:visited{color:#000;text-decoration:underline}
hr{background-color:#000;border:0;color:#000;height:1px}
hr.HRPrint{background-color:#000;border:0;color:#000;height:1px;display:none}
br.BRPrint{background-color:#000;border:0;color:#000;height:1px;display:none}
table.sortable{border:0;border-bottom:2px solid #000;border-collapse:collapse;width:770px}
table.qualifikationsmodus{border-bottom:1px solid #000;border-top:1px solid #000;width:770px}
table.zeitplan{border:2px solid #000;border-collapse:collapse;width:770px;background-color:#FFFFFF}
table.zeitplanOben{border:2px solid #000;border-collapse:collapse;margin-bottom:5px;padding:0;width:770px}
table.stellplatz{border:#000 solid 2px;border-collapse:collapse;width:770px}
table.laivemenu{border-bottom:1px solid #000;border-top:1px solid #000;height:8px;margin:0;padding:0;width:770px}

table.laivemenuPrint{border-bottom:1px solid #000;border-top:1px solid #000;height:8px;margin:0;padding:0;width:770px; display:none}
tr.laivemenuPrint, tr.KopfPrint{display:none}

table.zeitplantyplink{border:#FFF solid 2px;height:15px;width:100%}
table.zeitplantyplinkOben{border:0;height:100%;margin:0;padding:0;width:100%}
ul.topmenu{background-color:#FFF;border-bottom-color:#000;border-bottom-style:solid;border-bottom-width:2px;border-top-color:#000;border-top-style:solid;border-top-width:2px;margin:0;padding:5px 0}
ul.secoundmenu{background-color:#FFF;margin:0;padding:0}
li.topmenu{display:inline;margin-bottom:0;margin-right:6px;margin-top:0}
p.AnzahlRunden, p.ParticipantsTeamsByClub{margin-top:2px;padding-left:0;padding-top:2px;text-align:left}
p.LinkStellplatz{text-align:right}
a.AnzahlRunden{font-size:12px;padding-left:46px}
a.LinkStellplatz{color:black;font-size:12px}
a.linkbold{color:black;font-weight:700}
a.linknormal{color:black}
a.linklistezwischenergebnisse{color:#FF8C00;font-size:14px;font-weight:700}
a:hover{color:#000;text-decoration:none}
th{border-bottom:2px solid #000;border-top:2px solid #000;font-size:13px;font-weight:700;height:30px;text-align:left}

td.typempty{padding:3px; text-align: center }

td.typ1{background-color:#90EE90;padding:3px; text-align: center; border-right: 2px solid #FFF; border-bottom: 2px solid #FFF}
td.typoben1{background-color:#90EE90;border-bottom:2px solid #FFF}
td.typ2{background-color:#FFB6C1;padding:3px; text-align: center; border-right: 2px solid #FFF; border-bottom: 2px solid #FFF}
td.typoben2{background-color:#FFB6C1;border-bottom:2px solid #FFF}
td.typ3{background-color:#FF7F50;padding:3px; text-align: center; border-right: 2px solid #FFF; border-bottom: 2px solid #FFF}
td.typoben3{background-color:#FF7F50;border-bottom:2px solid #FFF}
td.typ4{background-color:#FFFF83;padding:3px; text-align: center; border-right: 2px solid #FFF; border-bottom: 2px solid #FFF }
td.typoben4{background-color:#FFFF83;border-bottom:2px solid #FFF}
a.typ8{color:grey;font-size:11px;padding-left:3px;text-align:left; text-align: center; }
a.meldungen{color:#000;font-size:11px;padding-left:5px;text-align:right}
td.meldungen{background-color:#FFF;border-top:1px solid #000;height:25px;width:112px}
td.meldungenOben{background-color:#FFF;border-bottom:2px solid #FFF;width:112px}
td.zeitplanzeit{background-color:#FFF;border-top:1px solid #000;height:25px;width:45px}
td.zeitplanzeitOben{background-color:#FFF;border-bottom:2px solid #FFF;width:45px}
td.zeitplanzeitvorhanden{background-color:#FFF;border-top:0 solid #000}
td.zeitplanzeitaktuell{background-color:#ADD8E6;border-top:1px solid #000}
td.zeitplanzeitvorhandenaktuell{background-color:#ADD8E6;border-top:1px solid #ADD8E6}

td.zeitplanspalteklasse{background-color:#FFF;border-top:1px solid #000;height:25px;text-align:left;width:152px}

td.zeitplanspalteevent0{background-color:#FFF;border-top:1px solid #000;height:25px;text-align:left;width:250px}
td.zeitplanspalteevent1{background-color:#ADD8E6;border-top:1px solid #000;height:25px;text-align:left;width:250px}

td.timetableRowLists{background-color:#FFF;border-top:1px solid #000;height:25px;text-align:left;width:205px}
td.timetableRowParticipansAndTeams{background-color:#FFF;border-top:1px solid #000;height:25px;text-align:right;width:30px;font-size:13px;padding-right:2px;}
td.timetableRowHeatsAndGroups{background-color:#FFF;border-top:1px solid #000;height:25px;text-align:right;width:30px;font-size:13px;padding-right:7px;}


td.zeitplanspalteklasseOben{background-color:#FFF;border-bottom:2px solid #FFF;text-align:left;width:152px}
td.zeitplanspaltedisziplin{background-color:#FFF;border-top:1px solid #000;height:25px;text-align:left;width:145px}
td.zeitplanspaltedisziplinOben{background-color:#FFF;border-bottom:2px solid #FFF;text-align:left;width:145px}
a.zeitplanspaltedisziplin{color:#000;font-size:14px;font-weight:700}
td.zeitplanspalterunde{background-color:#FFF;border-top:1px solid #000;height:25px;text-align:left;width:150px}
td.zeitplanspalterunde0{background-color:#FFF;border-top:1px solid #000;height:25px;text-align:left;width:150px}
td.zeitplanspalterunde1{background-color:#ADD8E6;border-top:1px solid #000;height:25px;text-align:left;width:150px}
td.zeitplanspalterundeOben{background-color:#FFF;border-bottom:2px solid #FFF;text-align:left;width:118px}
td.zeitplanspaltetyp{background-color:#FFF;border-top:1px solid #000;height:25px;text-align:left;width:155px}
td.zeitplanspaltetypOben{background-color:#FFF;border-bottom:2px solid #FFF;text-align:left;width:155px}
a.zeitplanspalterundemk{color:grey;font-size:11px;font-weight:700}
td.zeitplanspalteaktuell{background-color:#FFF;border-top:1px solid #000;height:25px;text-align:right;width:30px}
td.zeitplanspalteaktuellOben{background-color:#FFF;border-bottom:2px solid #FFF;text-align:right;width:30px}

td.seperator, th.seperator{border-top:2px solid #FFF; border-bottom: 2px solid #FFF; border-left:2px solid #000; border-right:2px solid #000;height:25px;width:10px}
th.timetableHeadRight{text-align:right;border-bottom:2px solid #000;border-top:2px solid #000;font-size:13px;font-weight:700;height:30px}
th.timetableHeadCenter{text-align:center;border-bottom:2px solid #000;border-top:2px solid #000;font-size:13px;font-weight:700;height:30px}

a.zeitplanspalteaktuell{color:#000;font-size:10px}
a.aktualisiert{color:red;font-size:12px;padding-left:0;text-align:right}
td.aktualisiert{background-color:#FFF;border-top:0;text-align:right;width:200px}

a.aktualisiertPrint{color:red;font-size:12px;padding-left:0;text-align:right;display:none}
td.aktualisiertPrint{background-color:#FFF;border-top:0;text-align:right;width:200px;display:none}

a.linkliste, a.linkliste_type1, a.linkliste_type2, a.linkliste_type3, a.linkliste_type4{font-size:12px;padding-left:2px;padding-right:2px;}
a.linkliste2 {font-size:14px;padding-left:2px;padding-right:2px;}

td.linkliste{border-top:0;text-align:left;width:570px}

td.linklistePrint{border-top:0;text-align:left;width:570px; display:none}

a.linkliste_type1{background-color:#90EE90}
a.linkliste_type2{background-color:#FFB6C1}
a.linkliste_type3{background-color:#FF7F50}
a.linkliste_type4{background-color:#FFFF83}
a.stellplatz{color:#000;font-size:18px;padding-left:0;text-align:left}
td.stellplatzzeit{background-color:#FFF;border-left:2px solid #000;border-right:2px solid #000;border-top:2px solid #000}
td.stellplatzzeitvorhanden{background-color:#FFF;border-left:2px solid #000;border-right:2px solid #000}
a.stellplatztn{color:#000;font-size:18px;padding-right:10px;text-align:right}
td.stellplatztn{background-color:#FFF;border:#000 solid 2px;text-align:right}
a.stellplatzwbnr{color:#000;font-size:14px;padding-right:2px;text-align:right}
td.stellplatzwbnr{background-color:#FFF;border:#000 solid 2px;font-style:italic;text-align:right}
a.stellplatztag{color:#FFF;font-size:20px;font-weight:700;padding-left:0;text-align:left}
td.stellplatztag{background-color:#000;border:#000 solid 2px;padding-right:2px}
p.txtstellplatz{margin-bottom:0;margin-top:0;text-align:center}
a.txtstellplatz{font-size:16px;font-weight:700}
td.KopfZ1{border-bottom:0;border-top:1px solid #000;color:#000;font-size:18px;font-weight:700;padding:0;text-align:center}
td.KopfZ11{border-bottom:0;border-top:0;color:#000;font-size:18px;font-weight:700;padding:0;text-align:center}
td.KopfZ12{border-bottom:1px solid #000;border-top:0;color:#000;font-size:18px;font-weight:700;padding:0;text-align:center}
td.KopfZ2{border-bottom:0 solid #000;border-top:1px solid #000;color:#000;font-size:14px;font-weight:700;padding-bottom:7px;padding-top:7px;text-align:left;width:600px}
td.KopfZ21{border-bottom:0 solid #000;border-top:0 solid #000;color:#000;font-size:18px;font-weight:700;padding-bottom:0;padding-top:12px;text-align:left;width:600px}
td.Stand{border-bottom:1px solid #000;border-top:1px solid #000;color:#000;font-size:15px;font-weight:700;padding-bottom:0;padding-top:0;text-align:right;width:150px}

td.KopfZ1Print{border-bottom:0;border-top:1px solid #000;color:#000;font-size:18px;font-weight:700;padding:0;text-align:center;display:none}
td.KopfZ11Print{border-bottom:0;border-top:0;color:#000;font-size:18px;font-weight:700;padding:0;text-align:center;display:none}
td.KopfZ12Print{border-bottom:1px solid #000;border-top:0;color:#000;font-size:18px;font-weight:700;padding:0;text-align:center;display:none}
td.KopfZ2Print{border-bottom:0 solid #000;border-top:1px solid #000;color:#000;font-size:14px;font-weight:700;padding-bottom:7px;padding-top:7px;text-align:left;width:600px;display:none}
td.KopfZ21Print{border-bottom:0 solid #000;border-top:0 solid #000;color:#000;font-size:18px;font-weight:700;padding-bottom:0;padding-top:12px;text-align:left;width:600px;display:none}


td.FussZ{border-bottom:1px solid #000;border-top:1px solid #000;font-size:13px;font-weight:700;padding-top:5px;text-align:center;width:770px}
td.FussZLaive{border-bottom:1px solid #000;border-top:1px solid #000;font-size:10px;font-weight:700;padding-top:5px;text-align:center;width:770px}
td.qualifikationsmodusueberschrift{font-size:13px}
td.qualifikationsmodustext{font-size:13px;font-weight:700}
td.blEWettbMK{color:#000;font-size:15px;font-weight:bold;padding-bottom:0;padding-left:17px;padding-top:0;text-align:left;width:700px}
td.AklZ{border-bottom:0 solid #000;border-top:1px solid #000;color:#000;font-size:18px;font-weight:700;height:10px;padding-bottom:8px;padding-top:5px;text-align:left}
p.EWettb{color:#000;font-size:14px;font-weight:700;padding-bottom:0;padding-top:0;text-align:left;width:230px}
.blEWettb{color:#000;font-size:15px;font-weight:700;padding-bottom:0;padding-left:17px;padding-top:0;text-align:left;width:650px}
.blEWind{color:#000;font-size:15px;padding-bottom:0;padding-top:0;text-align:left;width:100px}
td.blEDatum{color:#000;font-size:15px;padding-bottom:0;padding-top:0;text-align:right;width:150px}
.blEFreiDis{color:#000;font-size:14px;padding-bottom:0;padding-top:0;text-align:left;width:33px}
.blEDis{color:#000;font-size:14px;padding-bottom:0;padding-top:0;text-align:left;width:737px}
.blEPokWtr1w{font-size:12px;padding-bottom:2px;padding-left:33px;padding-top:12px;text-align:left;width:107px}
.blEPokBez1w{font-size:12px;padding-bottom:2px;padding-top:12px;text-align:left;width:625px}
.blEPokWtrw{font-size:12px;padding-left:33px;text-align:left;width:107px}
.blEPokBezw{font-size:12px;padding:0;text-align:left;width:625px}
.blEPokPktw{font-size:12px;padding:0;text-align:right;width:70px}
.blEPokFr1w{font-size:12px;padding:0;text-align:left;width:312px}
.blEPokNamew{font-size:12px;padding-left:6px;text-align:left;width:222px}
.blEStMaTn1w{font-size:12px;padding-left:105px;padding-top:7px;text-align:left;width:770px}
.blEHochNw{font-size:12px;padding-left:3px;text-align:left;width:110px}
.blEHochHw{font-size:12px;padding:0;text-align:left;width:25px}
.blEHochRw{font-size:12px;padding:0;text-align:left;width:30px}
.blEStNrVw{font-size:12px;text-align:right;vertical-align:top;width:46px}
.blENameASVw{font-size:14px;padding-left:6px;text-align:left;vertical-align:top;width:218px}
.blEJGVw{font-size:14px;padding-left:0;text-align:right;vertical-align:top;width:35px}
.blELvVw{font-size:14px;padding-left:6px;text-align:left;vertical-align:top;width:30px}
.blEStNrVg{background-color:#DDD;font-size:12px;text-align:right;vertical-align:top;width:46px}
.blENameASVg{background-color:#DDD;font-size:14px;padding-left:6px;text-align:left;vertical-align:top;width:218px}
.blEJGVg{background-color:#DDD;font-size:14px;padding-left:0;text-align:right;vertical-align:top;width:35px}
.blELvVg{background-color:#DDD;font-size:14px;padding-left:6px;text-align:left;vertical-align:top;width:30px}
td.blGrundLink{background:#FFF;color:#000;font-size:13px;padding-left:0;vertical-align:top;width:256px}
td.blGrundLinkAK{background:#FFF;color:#000;font-size:13px;padding-left:0;vertical-align:top;width:150px}
td.blGrundLinkDIS{background:#FFF;color:#000;font-size:13px;padding-left:0;vertical-align:top;width:620px}
td.blEFreiDisTl{background:#FFF;color:#000;font-size:13px;padding-left:0;text-align:left;width:241px}
td.blEFreiDisTm{background:#FFF;color:#000;font-size:13px;padding-left:0;text-align:center;width:241px}
td.blEFreiDisTr{background:#FFF;color:#000;font-size:13px;padding-left:0;text-align:right;width:241px}
a.blEFreiDisTriege{background:#FFF;color:#000;font-size:13px;font-weight:700;padding-left:0}
td.blEFreiDisTlriege{background:#FFF;color:#000;font-size:13px;font-weight:700;padding-left:0;text-align:left;width:241px}
.blEgemwettbeww{font-size:13px;padding-left:0;text-align:left;width:441px}
.blEgemwettbewg{background-color:#DDD;font-size:13px;padding-left:0;text-align:left;width:441px}
td.blGrundSortierung{background:#FFF;color:#000;font-size:13px;padding-left:0;width:192px}
.blERangu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:14px;padding:0;text-align:right;width:30px}
.blERangBu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:14px;text-align:right;width:40px}
.blEStNru{border-bottom:1px solid #000;border-top:1px solid #000;font-size:12px;text-align:right;width:46px}

.cuptableheadplace{border-bottom:1px solid #000;border-top:1px solid #000;font-size:12px;text-align:right;width:100px; padding-right: 20px;}
.cuptableheadteam{border-bottom:1px solid #000;border-top:1px solid #000;font-size:12px;text-align:left;width:500px}
.cuptableheadpoints{border-bottom:1px solid #000;border-top:1px solid #000;font-size:12px;text-align:right;width:170px}

.cuptableplace1{border-bottom:0px solid #000;border-top:0px solid #000;font-size:16px;text-align:right;width:100px; padding-right: 20px;background-color:#DDD;line-height:30px}
.cuptableteam1{border-bottom:0px solid #000;border-top:0px solid #000;font-size:16px;text-align:left;width:500px;background-color:#DDD;line-height:30px}
.cuptablepoints1{border-bottom:0px solid #000;border-top:0px solid #000;font-size:16px;text-align:right;width:170px;background-color:#DDD;line-height:30px}

.cuptableplace0{border-bottom:0px solid #000;border-top:0px solid #000;font-size:16px;text-align:right;width:100px; padding-right: 20px;line-height:30px}
.cuptableteam0{border-bottom:0px solid #000;border-top:0px solid #000;font-size:16px;text-align:left;width:500px;line-height:30px}
.cuptablepoints0{border-bottom:0px solid #000;border-top:0px solid #000;font-size:16px;text-align:right;width:170px;line-height:30px}

.cuptableheaddetailedteam{border-bottom:1px solid #000;border-top:1px solid #000;font-size:12px;text-align:left;width:150px}
.cuptableheaddetailedevent{border-bottom:1px solid #000;border-top:1px solid #000;font-size:12px;text-align:right}
.cuptableheaddetailedpoints{border-bottom:1px solid #000;border-top:1px solid #000;font-size:12px;text-align:right;width:50px}
.cuptableheaddetailedplace{border-bottom:1px solid #000;border-top:1px solid #000;font-size:12px;text-align:right;width:50px}

.cuptabledetailteam1{border-bottom:0px solid #000;border-top:0px solid #000;font-size:14px;text-align:left;width:150px;background-color:#DDD;}
.cuptabledetailevent1{border-bottom:0px solid #000;border-top:0px solid #000;font-size:14px;text-align:right;background-color:#DDD;}
.cuptabledetailpoints1{border-bottom:0px solid #000;border-top:0px solid #000;font-size:14px;text-align:right;width:50px;background-color:#DDD;}
.cuptabledetailplace1{border-bottom:0px solid #000;border-top:0px solid #000;font-size:14px;text-align:right;width:50px;background-color:#DDD;}

.cuptabledetailteam0{border-bottom:0px solid #000;border-top:0px solid #000;font-size:14px;text-align:left;width:150px;}
.cuptabledetailevent0{border-bottom:0px solid #000;border-top:0px solid #000;font-size:14px;text-align:right;}
.cuptabledetailpoints0{border-bottom:0px solid #000;border-top:0px solid #000;font-size:14px;text-align:right;width:50px;}
.cuptabledetailplace0{border-bottom:0px solid #000;border-top:0px solid #000;font-size:14px;text-align:right;width:50px;}

.imgflags{margin-left:3px; margin-right:3px; border: 0px solid #000;width:16px;}

.IPCClassu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:12px;text-align:left;width:46px}
.blENameu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:14px;padding-left:3px;text-align:left;width:276px}
.blENameASu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:14px;padding-left:6px;text-align:left;width:218px}
.blEJGu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:14px;padding-left:0;text-align:right;width:35px}
.blELvu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:14px;padding-left:6px;text-align:left;width:30px}
.blEVereinu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:14px;padding-left:5px;text-align:left;width:214px}
.blELeistu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:14px;padding-left:0;text-align:right;width:85px}
td.blEgemwettbewu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:13px;padding-left:0;text-align:left;width:441px}
.blEQualiu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:14px;padding-left:0;text-align:center;width:10px}
.blEPokPu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:12px;padding-left:0;text-align:right;width:48px}
.blEElemu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:13px;padding-left:0;text-align:right;width:80px}
.blERest1u{border-bottom:1px solid #000;border-top:1px solid #000;font-size:13px;padding-left:0;text-align:left;width:260px}
.blELBezu{border-bottom:1px solid #000;border-top:1px solid #000;font-size:13px;padding-left:4px;text-align:left;width:28px}
td.blEMeldeleistung{font-size:13px;text-align:right;width:110px}
td.blERiege{font-size:12px;text-align:right;width:70px}
td.blEWettbew{font-size:13px;text-align:left;width:261px}

td.blEWertungsgruppenVw{font-size:10px;padding-left:15px;text-align:left;width:724px}
td.blEWertungsgruppenVg{background-color:#DDD;font-size:10px;padding-left:15px;text-align:left;width:724px}

td.blEIPCCodeVw{font-size:10px;padding-left:15px;text-align:left;width:200px}
td.blEIPCCodeVg{background-color:#DDD;font-size:10px;padding-left:15px;text-align:left;width:200px}

td.blEIPCStartClassw{font-size:12px;padding-left:15px;text-align:right;width:40px}
td.blEIPCStartClassg{background-color:#DDD;font-size:12px;padding-left:15px;text-align:right;width:40px}

td.blEIPCemptyw{font-size:10px;padding-left:15px;text-align:left;width:395px}
td.blEIPCemptyg{background-color:#DDD;font-size:10px;padding-left:15px;text-align:left;width:395px}

a.IPCClassEvent{font-size:10px;text-align:right}


.blERangStaffelw{font-size:15px;font-weight:700;padding-right:0;text-align:right;width:30px}
.blERangStaffelg{background-color:#DDD;font-size:15px;font-weight:700;padding-right:0;text-align:right;width:30px}
.blEStNrStaffelw{font-size:13px;font-weight:700;text-align:right;width:46px}
.blEStNrStaffelg{background-color:#DDD;font-size:13px;font-weight:700;text-align:right;width:46px}
.blENameASStaffelw{font-size:15px;font-weight:700;padding-left:6px;text-align:left;width:218px}
.blENameASStaffelg{background-color:#DDD;font-size:15px;font-weight:700;padding-left:6px;text-align:left;width:218px}
a.blERangStaffelw{font-size:15px;font-weight:700;padding-right:12px;text-align:right;width:30px}
a.blERangStaffelg{background-color:#DDD;font-size:15px;font-weight:700;padding-right:12px;text-align:right;width:30px}
.blERangMannschaftsTNw{font-size:13px;padding-right:0;text-align:right;width:30px}
.blERangMannschaftsTNg{background-color:#DDD;font-size:13px;padding-right:0;text-align:right;width:30px}
.blEStNrMannschaftsTNw{font-size:11px;text-align:right;width:46px}
.blEStNrMannschaftsTNg{background-color:#DDD;font-size:11px;text-align:right;width:46px}
.blENameASMannschaftsTNw{font-size:13px;padding-left:6px;text-align:left;width:218px}
.blENameASMannschaftsTNg{background-color:#DDD;font-size:13px;padding-left:6px;text-align:left;width:218px}
.blEJGMannschaftsTNw{font-size:13px;padding-left:0;text-align:right;width:35px}
.blEJGMannschaftsTNg{background-color:#DDD;font-size:13px;padding-left:0;text-align:right;width:35px}
td.LinkTnVereine{background-color:#FFF;border-bottom:0px solid #000;border-top:1px solid #000;font-size:15px;font-weight:700;height:25px;text-align:right;width:770px}
.blEAusserw{font-size:12px;padding-bottom:6px;padding-left:82px;padding-top:6px;text-align:left;width:737px}
.blEMaFrei1g{font-size:14px;padding-left:5px;text-align:left;width:740px}
td.links{border-left-style:solid;border-left-width:1px}
td.linksZentriert{border-left-style:solid;border-left-width:1px;text-align:center}
td.linksOben{border-left-style:solid;border-left-width:1px;border-top-style:solid;border-top-width:1px}
td.linksObenZentriert{border-left-style:solid;border-left-width:1px;border-top-style:solid;border-top-width:1px;text-align:center}
td.linksObenRechts{border-left-style:solid;border-left-width:1px;border-right-style:solid;border-right-width:1px;border-top-style:solid;border-top-width:1px}
td.linksObenRechtsRechts{border-left-style:solid;border-left-width:1px;border-right-style:solid;border-right-width:1px;border-top-style:solid;border-top-width:1px;text-align:right}
td.rechts{border-right-style:solid;border-right-width:1px}
td.rechtsZentriert{border-right-style:solid;border-right-width:1px;text-align:center}
td.linksRechts{border-left-style:solid;border-left-width:1px;border-right-style:solid;border-right-width:1px}
td.linksRechtsRechts{border-left-style:solid;border-left-width:1px;border-right-style:solid;border-right-width:1px;text-align:right}
td.Oben{border-top-style:solid;border-top-width:1px}
td.TeilnStNr{background-color:#FFF;border-top:1px solid #000;font-size:12px;padding-top:1px;text-align:right;vertical-align:top}
td.TeilnIPCClass{background-color:#FFF;border-top:1px solid #000;font-size:12px;padding-top:1px;text-align:left;vertical-align:top}
td.TeilnName{background-color:#FFF;border-top:1px solid #000;font-size:14px;font-weight:700;text-align:left;vertical-align:top}
td.TeilnGemWettbew{background-color:#FFF;border-top:1px solid #000;font-size:12px;text-align:left;vertical-align:top}
a.nachmeldung{color:#8B0000}
body,#seitenbereich{font-family:Arial, Helvetica, sans-serif;font-style:normal;width:770px}
table.body,table.bodynoprint{border:0;width:770px}

table.bodyPrint {border:0;width:770px;display:none}

a.typ3, a.typ5{color:#000;font-size:14px;padding-left:3px;text-align:left;text-decoration:none}

a.typ1{font-size:14px;padding:3px;text-align:left;font-weight:bold;text-decoration:none; }
a.typ2{font-size:14px;padding-left:3px;text-align:left;text-decoration:none}
a.typ3{font-size:14px;padding-left:3px;text-align:left;font-weight:bold;text-decoration:none}
a.typ4{font-size:14px;padding-left:3px;text-align:left;text-decoration:none;font-style:italic;font-weight:bold; color:#424242}

a.typ1:hover, a.typ2:hover, a.typ3:hover, a.typ4:hover{color:#000;text-decoration:underline}


td.typ5,td.typ6,td.typ7,td.typ8{background-color:#FFF;padding:3px}
a.typ6,a.typ7{color:#000;font-size:13px;padding-left:3px;text-align:left}
td.typoben5,td.typoben6,td.typoben7,td.typoben8{background-color:#FFF;border-bottom:2px solid #FFF}
a.zeitplanzeit,a.zeitplanzeitvorhanden,a.zeitplanzeitaktuell,a.zeitplanzeitvorhandenaktuell{color:#000;font-size:14px;font-weight:700;padding-left:3px;text-align:left}
a.zeitplanspalteklasse,a.zeitplanspalterunde{color:#000;font-size:14px}

a.timetable_row_type2, a.timetable_row_type5, a.timetable_row_type6, a.timetable_row_type7, a.timetable_row_type8 {color:#000;font-size:14px}
a.timetable_row_type1, a.timetable_row_type3, a.timetable_row_type4 {font-weight:700;color:#000;font-size:14px}
a.zeitplanspalteklassemk,a.zeitplanspaltedisziplinmk{color:grey;font-size:12px}
td.stellplatz,th.stellplatz{background-color:#FFF;border:#000 solid 2px}
a.stellplatzzeit,a.stellplatzzeitvorhanden{color:#000;font-size:20px;font-weight:700;padding-left:0;text-align:left}
a.zeitplanverzug,p.zeitplanverzug{color:#000;font-size:14px;font-style:italic;padding-left:0;text-align:center}
td.blGrund,a.blEFreiDisT{background:#FFF;color:#000;font-size:13px;padding-left:0}
.blEPokRangw,.blEHochRangw{font-size:12px;padding:0;text-align:right;width:30px}
.blEStMaTnw,.blEStMaTng{font-size:12px;padding-left:105px;text-align:left;width:770px}
td.blEStMaTng,td.blERangfLWg,td.blELeistWg,td.blERangg,td.blERangBg,td.blEStNrg,td.blENameg,td.blENameASg,td.blEJGg,td.blELvg,td.blEVereing,td.blELeistg,td.blEQualig,td.blEPokPg,td.blEElemg,td.blERest1g,td.blELBezg,td.blEMaFrei1g,td.blVereinPg, td.IPCClassg{background-color:#DDD}
.blERangfLWw,.blERangfLWg{font-size:12px;padding-left:0;text-align:right;width:30px}
.blELeistWw,.blELeistWg{font-size:14px;padding-left:0;text-align:right;width:55px}
.blERangw,.blERangg{font-size:14px;padding:0;text-align:right;width:30px}
.blERangBw,.blERangBg{font-size:14px;text-align:right;width:40px}
.blEStNrw,.blEStNrg{font-size:12px;text-align:right;width:46px}
.IPCClassw,.IPCClassg{font-size:12px;text-align:left;width:46px}
.blENamew,.blENameg{font-size:14px;padding-left:3px;text-align:left;width:276px}
.blENameASw,.blENameASg{font-size:14px;padding-left:6px;text-align:left;width:218px}
.blEJGw,.blEJGg{font-size:14px;padding-left:0;text-align:right;width:35px}
.blELvw,.blELvg{font-size:14px;padding-left:6px;text-align:left;width:30px}
.blEVereinw,.blEVereing{font-size:14px;padding-left:5px;text-align:left;width:214px}
.blELeistw,.blELeistg{font-size:14px;padding-left:0;text-align:right;width:85px}
.blEQualiw,.blEQualig{font-size:14px;padding-left:0;text-align:center;width:10px}
.blEPokPw,.blEPokPg{font-size:12px;padding-left:0;text-align:right;width:48px}
.blEElemw,.blEElemg{font-size:13px;padding-left:0;text-align:right;width:80px}
.blERest1w,.blERest1g{font-size:13px;padding-left:0;text-align:left;width:260px}
.blELBezw,.blELBezg{font-size:13px;padding-left:4px;text-align:left;width:28px}
a.blEWettbew,a.blEgemwettbewu{padding-left:10px}
.blVereinPw,.blVereinPg{font-size:12px;padding-left:395px;text-align:left;width:770px}
td.TeilnGeschlecht,td.TeilnJG,td.TeilnLV,td.TeilnVerein{background-color:#FFF;border-top:1px solid #000;font-size:14px;text-align:left;vertical-align:top}
td.FinalConfirmationg{font-size:16px;text-align:center;width:15px; background-color:#DDD;font-weight:700}
td.FinalConfirmationw{font-size:16px;text-align:center;width:15px; background-color:#FFF;font-weight:700}
a.FinalConfirmation0{color: #ff0000}
a.FinalConfirmation1{color: #009966}
a.FinalConfirmationAthlete0{}
a.FinalConfirmationAthlete1{font-weight:700}
a.FinalConfirmationCount, p.FinalConfirmationCount{font-size:12px;text-align:right; margin-top: -30px;}

div.holdtogether{page-break-inside:avoid;}

td.left{text-align:left; padding-left:10px;font-size:12px;}

p.entrylistnotes{text-align:left; padding: 5px; margin-left: 10px; margin-right: 10px; font-size:14px; font-weight:700; background-color:#BB1319; color: #FFFFFF;}
p.entrylistnotesPrint{text-align:left; padding: 5px; margin-left: 10px; margin-right: 10px; font-size:14px; font-weight:700; background-color:#BB1319; color: #FFFFFF;display:none;}
a.entrylistnotes:hover{color: #FFFFFF;}

a.blGrundLinkDIS{display: inline;white-space: nowrap;}

a.tooltip:hover
{    position: relative;                      
     background: transparent;          
}  

a.tooltip span  
{    position: absolute;                     
     visibility: hidden;                        
     width: 20em;                               
     top: 2em; left: 1em;  
	background: #ffffdd;
      border: 1px solid #aaaaaa;
      padding: 7px;
	  font-weight:normal;
}

a.tooltip:hover span  
{    visibility: visible;   }

a.tooltip span b
{     display: block;
      font-weight: bold;
      border-bottom: 1px solid #888888;
      margin-bottom: 5px;
}                    

table.keytable{width:19em}

img.info{border: none;}

#header_image {width: 770px; margin-bottom:3px;}
#footer_image {width: 770px; marigin-top:2px; margin-bottom:2px; border-top:1px solid #000;}
div.header { display: none }

@media print {
a:link{text-decoration:none}
ul.topmenu,a.linkliste,a.linkliste_type1,a.linkliste_type2,a.linkliste_type3,a.linkliste_type4,p.LinkStellplatz,table.bodynoprint,.noprint{display:none}
p.AnzahlRunden{page-break-before:avoid}
p.ParticipantsTeamsByClub{page-break-after:always}
td.blEWettb,td.blGrundLinkAK,td.blEFreiDisT,td.blEFreiDisTlriege{page-break-after:avoid}

table.bodyPrint {border:0;width:770px;display:table}
table.laivemenuPrint{border-bottom:1px solid #000;border-top:1px solid #000;height:8px;margin:0;padding:0;width:770px;display:table}

tr.laivemenuPrint, tr.KopfPrint{display:table-row}

td.linklistePrint{border-top:0;text-align:left;width:570px;display:table-cell}
td.aktualisiertPrint{background-color:#FFF;border-top:0;text-align:right;width:200px;display:table-cell}
td.KopfZ1Print{border-bottom:0;border-top:1px solid #000;color:#000;font-size:18px;font-weight:700;padding:0;text-align:center;display:table-cell}
td.KopfZ11Print{border-bottom:0;border-top:0;color:#000;font-size:18px;font-weight:700;padding:0;text-align:center;display:table-cell}
td.KopfZ12Print{border-bottom:1px solid #000;border-top:0;color:#000;font-size:18px;font-weight:700;padding:0;text-align:center;display:table-cell}
td.KopfZ2Print{border-bottom:0 solid #000;border-top:1px solid #000;color:#000;font-size:14px;font-weight:700;padding-bottom:7px;padding-top:7px;text-align:left;width:600px;display:table-cell}
td.KopfZ21Print{border-bottom:0 solid #000;border-top:0 solid #000;color:#000;font-size:18px;font-weight:700;padding-bottom:0;padding-top:12px;text-align:left;width:600px;display:table-cell}

a.aktualisiertPrint{color:red;font-size:12px;padding-left:0;text-align:right;display:block}
hr.HRPrint{background-color:#000;border:0;color:#000;height:1px;display:block}
br.BRPrint{background-color:#000;border:0;color:#000;height:1px;display:block}
p.entrylistnotesPrint{text-align:left; padding: 5px; margin-left: 10px; margin-right: 10px; font-size:14px; font-weight:700; background-color:#FFFFFF; color: #000000;display:block; border:1px black solid;}

<!-- div#header {display: block; position: running(header);} -->

}
  
@page { @top-center { content: element(header) }}


		</style>
	</head>
	<body>
		<div id="seitenbereich">

<?php

# Header
if($EnableHeaderImage == 1 && file_exists($LinkToHeaderImage)) {
	if($WeblinkHeaderImage != "") {
		echo "<a href='".$WeblinkHeaderImage."' target='_blank'>". "<img id='header_image' src='" . $LinkToHeaderImage . "'/>". "</a>";
	}
	else {
		echo "<img id='header_image' src='" . $LinkToHeaderImage . "'/>";
	}
}


### Menu ###
echo "<ul class='topmenu'>";
if(file_exists($dat_uebersicht)) {echo "<li class='topmenu'><a class='linkbold' href='?sub=$dat_uebersicht'>".$txt_uebersicht."</a></li>";}
if(file_exists($dat_zeitplan) && file_exists($dat_wettbew)) {echo "<li class='topmenu'><a class='linkbold' href='?sub=$dat_zeitplan&amp;tag=".$starttag."#aktuell'>".$txt_zeitplan."</a></li>";}
if($competitionsubON == 1) {echo "<li class='topmenu'><a class='linknormal' href='".$competitionsublink." 'target='_blank'>".$competitionsubname."</a></li>";}
if($GesamtteilnehmerlisteAn == 1 && file_exists("gesamtteilnehmer.php") && file_exists($dat_wbteiln) && file_exists($dat_stamm) && file_exists($dat_verein)) {echo "<li class='topmenu'><a class='linknormal' href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=0'>".$txtMenuEntriesAll."</a></li>";}
if($TeilnehmerlisteNachWettbewerbenAn == 1 && file_exists("gesamtteilnehmer.php") && file_exists($dat_wbteiln) && file_exists($dat_stamm) && file_exists($dat_verein)) {echo "<li class='topmenu'><a class='linknormal' href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=0&amp;sort=3'>".$txtMenuEntriesByEvent."</a></li>";}
if($TeilnehmerlisteNachVereinen == 1 && file_exists("gesamtteilnehmer.php") && file_exists($dat_wbteiln) && file_exists($dat_stamm) && file_exists($dat_verein)) {echo "<li class='topmenu'><a class='linknormal' href='?sub=gesamtteilnehmer.php&amp;list=3'>".$txtMenuEntriesByClub."</a></li>";}
if($AllStartlistsInOneFile == 1 && file_exists("_startlists_all.htm")) {echo "<li class='topmenu'><a class='linknormal' href='?sub=_startlists_all.htm'>".$txtMenuStartlistsAll."</a></li>";}



if(file_exists($entryfile)) {echo "<li class='topmenu'><a class='linknormal' href='?sub=".$entryfile."'>".$entryname."</a></li>";}
if(file_exists($entrybyclubfile)) {echo "<li class='topmenu'><a class='linknormal' href='".$entrybyclubfile." 'target='_blank'>".$entrybyclubname."</a></li>";}
if(file_exists($resultfile1)) {echo "<li class='topmenu'><a class='linknormal' href='?sub=".$resultfile1."'>".$resultname1."</a></li>";}
if(file_exists($resultfile2)) {echo "<li class='topmenu'><a clss='linknormal' href='?sub=".$resultfile2."'>".$resultname2."</a></li>";}
if(file_exists($resultfile)) {echo "<li class='topmenu'><a class='linknormal' href='?sub=".$resultfile."'>".$resultname."</a></li>";}
echo "</ul>";
### Menu Ende ###

if(empty($_GET["sub"])) {

	if(file_exists($dat_zeitplan) && file_exists($dat_wettbew)) {
	
		$_GET["sub"] = $dat_zeitplan;
		$_GET["tag"] = $starttag;
	}
	else {
		$_GET["sub"] = $dat_uebersicht;
	}
}

if(file_exists($_GET["sub"]) == false ) {
	$_GET["sub"] = $dat_zeitplan;
}


if($_GET["sub"] != $dat_uebersicht) {

	if($_GET["sub"] != $dat_zeitplan) {
	
		if($_GET["sub"] != $dat_stellplatzzeitplan) {
		
			if($_GET["sub"] != "gesamtteilnehmer.php") {
			
				if($_GET["sub"] != "cupscoring.php") {

	$dateiname = $_GET["sub"];
	$dateinamegeteilt = explode(".", $dateiname);
	$dateinameohneendung = $dateinamegeteilt[0];
	$dateinameersteszeichen = substr($dateinameohneendung, 0, 1);
	
	if($dateinameersteszeichen == "t") {
	
		$dateinameohneendung = substr($dateinameohneendung, 1);
		
		
		$dateiname_finale 			= $dateinameohneendung.".htm";
		$dateiname_zwischenlauf 	= $dateinameohneendung."2.htm";
		$dateiname_zeitvorlauf		= $dateinameohneendung."3.htm";
		$dateiname_vorlauf		 	= $dateinameohneendung."1.htm";
		$dateiname_teilnehner		= $dateiname;
		$dateiname_disziplin1		= $dateinameohneendung."a.htm";
		$dateiname_disziplin2		= $dateinameohneendung."b.htm";
		$dateiname_disziplin3		= $dateinameohneendung."c.htm";
		$dateiname_disziplin4		= $dateinameohneendung."d.htm";
		$dateiname_disziplin5		= $dateinameohneendung."e.htm";
		$dateiname_disziplin6		= $dateinameohneendung."f.htm";
		$dateiname_disziplin7		= $dateinameohneendung."g.htm";
		$dateiname_disziplin8		= $dateinameohneendung."h.htm";
		$dateiname_disziplin9		= $dateinameohneendung."i.htm";
		$dateiname_startlistefinale 			= "s".$dateinameohneendung."k.htm";
		$dateiname_startlistezwischenlauf 	= "s".$dateinameohneendung."b.htm";
		$dateiname_startlistezeitvorlauf		= "s".$dateinameohneendung."e.htm";
		$dateiname_startlistevorlauf		 	= "s".$dateinameohneendung."a.htm";
					$dateiname_startlistezeitfinale		 	= "s".$dateinameohneendung."d.htm";
			$dateiname_startlisteabfinale		 	= "s".$dateinameohneendung."l.htm";
			$dateiname_startlistenurlaufnr		 	= "s".$dateinameohneendung."m.htm";
			$dateiname_startlisteausscheidung		 	= "s".$dateinameohneendung."r.htm";
			$dateiname_startlistequalifikation		 	= "s".$dateinameohneendung."s.htm";
			$dateiname_startlistefinalehoch		 	= "s".$dateinameohneendung."n.htm";
			$dateiname_startlistefinaletechnisch		 	= "s".$dateinameohneendung."q.htm";
			$dateiname_startlisteausscheidungslauf		 	= "s".$dateinameohneendung."c.htm";
	
	
	}
	else {
	
		$dateinamelaenge = strlen($dateinameohneendung);
		
		if($dateinamelaenge == 5) {
		
			$dateiname_finale 			= $dateiname;
			$dateiname_zwischenlauf 	= $dateinameohneendung."2.htm";
			$dateiname_zeitvorlauf		= $dateinameohneendung."3.htm";
			$dateiname_vorlauf		 	= $dateinameohneendung."1.htm";
			$dateiname_teilnehner		= "t".$dateinameohneendung.".htm";
			$dateiname_disziplin1		= $dateinameohneendung."a.htm";
			$dateiname_disziplin2		= $dateinameohneendung."b.htm";
			$dateiname_disziplin3		= $dateinameohneendung."c.htm";
			$dateiname_disziplin4		= $dateinameohneendung."d.htm";
			$dateiname_disziplin5		= $dateinameohneendung."e.htm";
			$dateiname_disziplin6		= $dateinameohneendung."f.htm";
			$dateiname_disziplin7		= $dateinameohneendung."g.htm";
			$dateiname_disziplin8		= $dateinameohneendung."h.htm";
			$dateiname_disziplin9		= $dateinameohneendung."i.htm";
			$dateiname_startlistefinale 			= "s".$dateinameohneendung."k.htm";
			$dateiname_startlistezwischenlauf 	= "s".$dateinameohneendung."b.htm";
			$dateiname_startlistezeitvorlauf		= "s".$dateinameohneendung."e.htm";
			$dateiname_startlistevorlauf		 	= "s".$dateinameohneendung."a.htm";
			$dateiname_startlistezeitfinale		 	= "s".$dateinameohneendung."d.htm";
			$dateiname_startlisteabfinale		 	= "s".$dateinameohneendung."l.htm";
			$dateiname_startlistenurlaufnr		 	= "s".$dateinameohneendung."m.htm";
			$dateiname_startlisteausscheidung		 	= "s".$dateinameohneendung."r.htm";
			$dateiname_startlistequalifikation		 	= "s".$dateinameohneendung."s.htm";
			$dateiname_startlistefinalehoch		 	= "s".$dateinameohneendung."n.htm";
			$dateiname_startlistefinaletechnisch		 	= "s".$dateinameohneendung."q.htm";
			$dateiname_startlisteausscheidungslauf		 	= "s".$dateinameohneendung."c.htm";
		
		}
		else {
		
			$dateiname_finale 			= substr($dateinameohneendung, 0, 5).".htm";
			$dateiname_zwischenlauf 	= substr($dateinameohneendung, 0, 5)."2.htm";
			$dateiname_zeitvorlauf		= substr($dateinameohneendung, 0, 5)."3.htm";
			$dateiname_vorlauf		 	= substr($dateinameohneendung, 0, 5)."1.htm";
			$dateiname_teilnehner		= "t".substr($dateinameohneendung, 0, 5).".htm";
			$dateiname_disziplin1		= substr($dateinameohneendung, 0, 5)."a.htm";
			$dateiname_disziplin2		= substr($dateinameohneendung, 0, 5)."b.htm";
			$dateiname_disziplin3		= substr($dateinameohneendung, 0, 5)."c.htm";
			$dateiname_disziplin4		= substr($dateinameohneendung, 0, 5)."d.htm";
			$dateiname_disziplin5		= substr($dateinameohneendung, 0, 5)."e.htm";
			$dateiname_disziplin6		= substr($dateinameohneendung, 0, 5)."f.htm";
			$dateiname_disziplin7		= substr($dateinameohneendung, 0, 5)."g.htm";
			$dateiname_disziplin8		= substr($dateinameohneendung, 0, 5)."h.htm";
			$dateiname_disziplin9		= substr($dateinameohneendung, 0, 5)."i.htm";
			$dateiname_startlistefinale 			= "s".substr($dateinameohneendung, 0, 5)."k.htm";
			$dateiname_startlistezwischenlauf 	= "s".substr($dateinameohneendung, 0, 5)."b.htm";
			$dateiname_startlistezeitvorlauf		= "s".substr($dateinameohneendung, 0, 5)."e.htm";
			$dateiname_startlistevorlauf		 	= "s".substr($dateinameohneendung, 0, 5)."a.htm";
			$dateiname_startlisteausscheidungslauf		 	= "s".substr($dateinameohneendung, 0, 5)."c.htm";
			$dateiname_startlistezeitfinale		 	= "s".substr($dateinameohneendung, 0, 5)."d.htm";
			$dateiname_startlisteabfinale		 	= "s".substr($dateinameohneendung, 0, 5)."l.htm";
			$dateiname_startlistenurlaufnr		 	= "s".substr($dateinameohneendung, 0, 5)."m.htm";
			$dateiname_startlisteausscheidung		 	= "s".substr($dateinameohneendung, 0, 5)."r.htm";
			$dateiname_startlistequalifikation		 	= "s".substr($dateinameohneendung, 0, 5)."s.htm";
			$dateiname_startlistefinalehoch		 	= "s".substr($dateinameohneendung, 0, 5)."n.htm";
			$dateiname_startlistefinaletechnisch		 	= "s".substr($dateinameohneendung, 0, 5)."q.htm";
		}
	}
	if($dateinameersteszeichen == "s") {
	
		$dateinameohneendung = substr($dateinameohneendung, 1);
		
		if($dateinamelaenge == 6) {
		
		
		$dateiname_finale 			= $dateinameohneendung.".htm";
		$dateiname_zwischenlauf 	= $dateinameohneendung."2.htm";
		$dateiname_zeitvorlauf		= $dateinameohneendung."3.htm";
		$dateiname_vorlauf		 	= $dateinameohneendung."1.htm";
		$dateiname_teilnehner		= "t".$dateinameohneendung.".htm";
		$dateiname_disziplin1		= $dateinameohneendung."a.htm";
		$dateiname_disziplin2		= $dateinameohneendung."b.htm";
		$dateiname_disziplin3		= $dateinameohneendung."c.htm";
		$dateiname_disziplin4		= $dateinameohneendung."d.htm";
		$dateiname_disziplin5		= $dateinameohneendung."e.htm";
		$dateiname_disziplin6		= $dateinameohneendung."f.htm";
		$dateiname_disziplin7		= $dateinameohneendung."g.htm";
		$dateiname_disziplin8		= $dateinameohneendung."h.htm";
		$dateiname_disziplin9		= $dateinameohneendung."i.htm";
		$dateiname_startlistefinale 			= "s".$dateinameohneendung."k.htm";
		$dateiname_startlistezwischenlauf 	= "s".$dateinameohneendung."b.htm";
		$dateiname_startlistezeitvorlauf		= "s".$dateinameohneendung."e.htm";
		$dateiname_startlistevorlauf		 	= "s".$dateinameohneendung."a.htm";
		$dateiname_startlisteausscheidungslauf		 	= "s".$dateinameohneendung."c.htm";
			$dateiname_startlistezeitfinale		 	= "s".$dateinameohneendung."d.htm";
			$dateiname_startlisteabfinale		 	= "s".$dateinameohneendung."l.htm";
			$dateiname_startlistenurlaufnr		 	= "s".$dateinameohneendung."m.htm";
			$dateiname_startlisteausscheidung		 	= "s".$dateinameohneendung."r.htm";
			$dateiname_startlistequalifikation		 	= "s".$dateinameohneendung."s.htm";
			$dateiname_startlistefinalehoch		 	= "s".$dateinameohneendung."n.htm";
			$dateiname_startlistefinaletechnisch		 	= "s".$dateinameohneendung."q.htm";
	
		}
		else {
		
			$dateiname_finale 			= substr($dateinameohneendung, 0, 5).".htm";
			$dateiname_zwischenlauf 	= substr($dateinameohneendung, 0, 5)."2.htm";
			$dateiname_zeitvorlauf		= substr($dateinameohneendung, 0, 5)."3.htm";
			$dateiname_vorlauf		 	= substr($dateinameohneendung, 0, 5)."1.htm";
			$dateiname_teilnehner		= "t".substr($dateinameohneendung, 0, 5).".htm";
			$dateiname_disziplin1		= substr($dateinameohneendung, 0, 5)."a.htm";
			$dateiname_disziplin2		= substr($dateinameohneendung, 0, 5)."b.htm";
			$dateiname_disziplin3		= substr($dateinameohneendung, 0, 5)."c.htm";
			$dateiname_disziplin4		= substr($dateinameohneendung, 0, 5)."d.htm";
			$dateiname_disziplin5		= substr($dateinameohneendung, 0, 5)."e.htm";
			$dateiname_disziplin6		= substr($dateinameohneendung, 0, 5)."f.htm";
			$dateiname_disziplin7		= substr($dateinameohneendung, 0, 5)."g.htm";
			$dateiname_disziplin8		= substr($dateinameohneendung, 0, 5)."h.htm";
			$dateiname_disziplin9		= substr($dateinameohneendung, 0, 5)."i.htm";
			$dateiname_startlistefinale 			= "s".substr($dateinameohneendung, 0, 5)."k.htm";
			$dateiname_startlistezwischenlauf 	= "s".substr($dateinameohneendung, 0, 5)."b.htm";
			$dateiname_startlistezeitvorlauf		= "s".substr($dateinameohneendung, 0, 5)."e.htm";
			$dateiname_startlistevorlauf		 	= "s".substr($dateinameohneendung, 0, 5)."a.htm";
			$dateiname_startlisteausscheidungslauf		 	= "s".substr($dateinameohneendung, 0, 5)."c.htm";
			$dateiname_startlistezeitfinale		 	= "s".substr($dateinameohneendung, 0, 5)."d.htm";
			$dateiname_startlisteabfinale		 	= "s".substr($dateinameohneendung, 0, 5)."l.htm";
			$dateiname_startlistenurlaufnr		 	= "s".substr($dateinameohneendung, 0, 5)."m.htm";
			$dateiname_startlisteausscheidung		 	= "s".substr($dateinameohneendung, 0, 5)."r.htm";
			$dateiname_startlistequalifikation		 	= "s".substr($dateinameohneendung, 0, 5)."s.htm";
			$dateiname_startlistefinalehoch		 	= "s".substr($dateinameohneendung, 0, 5)."n.htm";
			$dateiname_startlistefinaletechnisch		 	= "s".substr($dateinameohneendung, 0, 5)."q.htm";
		
		
		
		
		}
	
	}

?>
<table class="laivemenu">
	<tr>
		
		<td class="linkliste">
			<ul class="secoundmenu">
				<?php 
						if($IPCModeON != 1) {
						
						if(substr($dateinameohneendung, -1) == "z") {
					
						echo "<li class='topmenu'>
								<a class='linklistezwischenergebnisse'>".$txt_zwischenergebnisse."</a>
							</li>";   
						}
				
				
						if(file_exists($dateiname_finale)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type1' href='?sub=".$dateiname_finale."'>". $LinksSubMenuResultsFinal . "</a>
							</li>";   
						}
						
						if(file_exists($dateiname_zwischenlauf)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type1' href='?sub=".$dateiname_zwischenlauf."'>" . $LinksSubMenuResultsSemifinals . "</a>
							</li>";   
						}
						
						if(file_exists($dateiname_zeitvorlauf)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type1' href='?sub=".$dateiname_zeitvorlauf."'>" . $LinksSubMenuResultsTimedHeats . "</a>
							</li>";   
						}
						
						if(file_exists($dateiname_vorlauf)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type1' href='?sub=".$dateiname_vorlauf."'>" . $LinksSubMenuResultsHeats . "</a>
							</li>";   
						}
						
						if(file_exists($dateiname_disziplin9)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type3' href='?sub=".$dateiname_disziplin9."'>" . $LinksSubMenuResultsCombinedEventAfter9Events . "<a>
							</li>";   
						}
						
						
						if(file_exists($dateiname_disziplin8)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type3' href='?sub=".$dateiname_disziplin8."'>" . $LinksSubMenuResultsCombinedEventAfter8Events . "</a>
							</li>";   
						}
						
						
						
						if(file_exists($dateiname_disziplin7)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type3' href='?sub=".$dateiname_disziplin7."'>" . $LinksSubMenuResultsCombinedEventAfter7Events . "</a>
							</li>";   
						}
						
						
						
						if(file_exists($dateiname_disziplin6)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type3' href='?sub=".$dateiname_disziplin6."'>" . $LinksSubMenuResultsCombinedEventAfter6Events . "</a>
							</li>";   
						}
						
						
						if(file_exists($dateiname_disziplin5)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type3' href='?sub=".$dateiname_disziplin5."'>" . $LinksSubMenuResultsCombinedEventAfter5Events . "</a>
							</li>";   
						}
						
						
						if(file_exists($dateiname_disziplin4)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type3' href='?sub=".$dateiname_disziplin4."'>" . $LinksSubMenuResultsCombinedEventAfter4Events . "</a>
							</li>";   
						}
						
						
						
						if(file_exists($dateiname_disziplin3)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type3' href='?sub=".$dateiname_disziplin3."'>" . $LinksSubMenuResultsCombinedEventAfter3Events . "</a>
							</li>";   
						}
						
						
						if(file_exists($dateiname_disziplin2)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type3' href='?sub=".$dateiname_disziplin2."'>" . $LinksSubMenuResultsCombinedEventAfter2Events . "</a>
							</li>";   
						}
						
						if(file_exists($dateiname_disziplin1)) {
					
						echo "<li class='topmenu'>
								<a class='linkliste_type3' href='?sub=".$dateiname_disziplin1."'>" . $LinksSubMenuResultsCombinedEventAfter1Event . "</a>
							</li>";   
						}
						
						if(file_exists($dateiname_startlistefinale)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlistefinale."'>" . $LinksSubMenuStartlistFinalTrack . "<a></li>";   
						}
						if(file_exists($dateiname_startlistefinalehoch)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlistefinalehoch."'>" . $LinksSubMenuStartlistFinalHJ . "</a></li>";   
						}
						if(file_exists($dateiname_startlistefinaletechnisch)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlistefinaletechnisch."'>" . $LinksSubMenuStartlistFinalField . "</a></li>";   
						}
						if(file_exists($dateiname_startlisteabfinale)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlisteabfinale."'>" . $LinksSubMenuStartlistFinalTrackAB . "</a></li>";   
						}
						if(file_exists($dateiname_startlistezeitfinale)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlistezeitfinale."'>" . $LinksSubMenuStartlistTimeRace . "</a></li>";   
						}
						if(file_exists($dateiname_startlistezwischenlauf)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlistezwischenlauf."'>" . $LinksSubMenuStartlistSemifinals . "</a></li>";   
						}
						if(file_exists($dateiname_startlistezeitvorlauf)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlistezeitvorlauf."'>" . $LinksSubMenuStartlistTimedHeats . "</a></li>";   
						}
						if(file_exists($dateiname_startlistevorlauf)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlistevorlauf."'>" . $LinksSubMenuStartlistHeats . "</a></li>";   
						}
						if(file_exists($dateiname_startlisteausscheidungslauf)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlisteausscheidungslauf."'>" . $LinksSubMenuStartlistEliminationTrack . "</a></li>";   
						}
						if(file_exists($dateiname_startlisteausscheidung)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlisteausscheidung."'>" . $LinksSubMenuStartlistEliminationField . "</a></li>";   
						}
						if(file_exists($dateiname_startlistequalifikation)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlistequalifikation."'>" . $LinksSubMenuStartlistQualification . "</a></li>";   
						}
						if(file_exists($dateiname_startlistenurlaufnr)) {
							echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$dateiname_startlistenurlaufnr."'>" . $LinksSubMenuStartlistOnlyHeatNumber . "</a></li>";   
						}
						if(file_exists($dateiname_teilnehner)) {
							echo "<li class='topmenu'><a class='linkliste_type2' href='?sub=".$dateiname_teilnehner."'>" . $LinksSubMenuParticipantslist . "</a></li>";   
						}
						}
						else { # IPC Mode on
							$Events_SubMenuIPC 		= EventsArray();
							$IPCClass_SubMenuIPC	= IPCClassesArray();
							$TmpSubMenuLinkTitle 	= ""; 
							$CurrentFileListType	= ""; 
							$CurrentFileCOSAID		= ""; 
							$CurrentFileEventID		= ""; 
							$AllFilesFileListType	= "";
							$AllFilesFileCOSAID 	= "";
							$AllFilesFileEventID	= "";	
							
							
							$CurrentFile 	= $_GET['sub'];
							
							switch(substr($CurrentFile, 0, 1)) {
								case "t": # Teilnehmerliste / Entry list
									$CurrentFileListType		= substr($CurrentFile, 0, 1);
									$CurrentFileCOSAID 			= substr($CurrentFile, 1, 5);
									$TmpCurrentFileEventIDArray	= array_multi_search($CurrentFileCOSAID, $Events_SubMenuIPC, "COSAID");
									$CurrentFileEventID			= $TmpCurrentFileEventIDArray[0]['EventID'];
									unset($TmpCurrentFileEventIDArray);
									break;
								
								case "s": # Startliste / start list
									list($CurrentFileName, $CurrentFileExtention) = explode(".", $CurrentFile);
									list($CurrentFileListType, $CurrentFileEventType, $CurrentFileEventID, $CurrentFileCOSAID, $CurrentFileIPCClassID, $CurrentFileRoundType) = explode("-", $CurrentFileName);
									break;
								
								case "e": # Ergebnisliste / result list
									list($CurrentFileName, $CurrentFileExtention) = explode(".", $CurrentFile);
									list($CurrentFileListType, $CurrentFileEventID, $CurrentFileIPCClassID, $CurrentFileRoundType) = explode("-", $CurrentFileName);
									break;
							}
							
							$AllFiles		= scandir(".");
							
							foreach($AllFiles as $AllFilesFile) {
								if(substr($AllFilesFile, 0, 1) == "t" || substr($AllFilesFile, 0, 1) == "s" || substr($AllFilesFile, 0, 1) == "e") {
								switch(substr($AllFilesFile, 0, 1)) {
									case "t": # Teilnehmerliste / Entry list
										$AllFilesFileListType			= substr($AllFilesFile, 0, 1);	
										$AllFilesFileCOSAID 			= substr($AllFilesFile, 1, 5);
										$TmpAllFilesFileEventIDArray	= array_multi_search($AllFilesFileCOSAID, $Events_SubMenuIPC, "COSAID");
										$AllFilesFileEventID			= $TmpAllFilesFileEventIDArray[0]['EventID'];
										unset($TmpAllFilesFileEventIDArray);
										break;
								
									case "s": # Startliste / start list
										list($AllFilesFileName, $AllFilesFileExtention) = explode(".", $AllFilesFile);
										list($AllFilesFileListType, $AllFilesFileEventType, $AllFilesFileEventID, $AllFilesFileCOSAID, $AllFilesFileIPCClassID, $AllFilesFileRoundType) = explode("-", $AllFilesFileName);
										break;
								
									case "e": # Ergebnisliste / result list
										list($AllFilesFileName, $AllFilesFileExtention) = explode(".", $AllFilesFile);
										list($AllFilesFileListType, $AllFilesFileEventID, $AllFilesFileIPCClassID, $AllFilesFileRoundType) = explode("-", $AllFilesFileName);
										break;
									default:
										break;
								
								}
								
								if($AllFilesFileEventID == $CurrentFileEventID && $AllFilesFileEventID != "") {
									switch($AllFilesFileListType) {
										case "t": # Teilnehmerliste / Entry list
											echo "<li class='topmenu'><a class='linkliste_type2' href='?sub=".$AllFilesFile."'>" . $LinksSubMenuParticipantslist . "</a></li>";
											break;
											
										case "s": # Startliste / start list
											switch($AllFilesFileRoundType) {
												case "a":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistHeats;
													break;
												case "b":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistSemifinals;
													break;
												case "c":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistEliminationTrack;
													break;
												case "d":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistTimeRace;
													break;
												case "e":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistTimedHeats;
													break;
												case "k":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistFinalTrack;
													break;
												case "l":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistFinalTrackAB;
													break;
												case "m":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistOnlyHeatNumber;
													break;
												case "n":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistFinalHJ;
													break;
												case "q":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistFinalField;
													break;
												case "r":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistEliminationField;
													break;
												case "s":
													$TmpSubMenuLinkTitle = $LinksSubMenuStartlistQualification;
													break;	
											}
											echo "<li class='topmenu'><a class='linkliste_type4' href='?sub=".$AllFilesFile."'>" . $TmpSubMenuLinkTitle . " " . $IPCClass_SubMenuIPC[$AllFilesFileIPCClassID]['IPCClassName'] . "</a></li>";
											break;
											
										case "e": # Ergebnisliste / result list
										switch($AllFilesFileRoundType) {
												case "a":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsHeats;
													break;
												case "b":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsSemifinals;
													break;
												case "c":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsEliminationTrack;
													break;
												case "d":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsTimeRace;
													break;
												case "e":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsTimedHeats;
													break;
												case "k":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsFinal;
													break;
												case "l":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsFinalTrackAB;
													break;
												case "m":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsOnlyHeatNumber;
													break;
												case "n":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsFinalHJ;
													break;
												case "q":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsFinalField;
													break;
												case "r":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsEliminationField;
													break;
												case "s":
													$TmpSubMenuLinkTitle = $LinksSubMenuResultsQualification;
													break;	
											}
											echo "<li class='topmenu'><a class='linkliste_type1' href='?sub=".$AllFilesFile."'>" . $TmpSubMenuLinkTitle . " " . $IPCClass_SubMenuIPC[$AllFilesFileIPCClassID]['IPCClassName'] . "</a></li>";
											break;
										default:
											break;
									}
								}
							}
							}
						}
						
						
						?>				
			</ul>
		</td>
		<td class="aktualisiert"><?php echo "<a class='aktualisiert'>" . $TxtSubMenuUpdated . " ".date("d.m.y H:i", filemtime($_GET["sub"]))."</a>"; ?></td>
	</tr>
</table>

<?php

}
}
}
}
}
ob_flush();
flush();
if($IPCResultListFileExtention == "pdf" && strpos(strtolower($_GET["sub"]), ".pdf") > 1 ) {
	echo "<iframe src='".$_GET["sub"]."' name='pdflist' width='99%' height='800'></iframe>";

	echo "<p class='LinkStellplatz'><a class='LinkStellplatz' href='".$_GET["sub"]."' target='_blank' type='application/octet-stream'>Download</a></p>";
}
else {
	include_once ("./". $_GET["sub"]);
}


# Footer
if($EnableFooterImage == 1 && file_exists($LinkToFooterImage)) {
	if($WeblinkFooterImage != "") {
		echo "<a href='".$WeblinkFooterImage."' target='_blank'>". "<img id='footer_image' src='" . $LinkToFooterImage . "'/>". "</a>";
	}
	else {
		echo "<img id='footer_image' src='" . $LinkToFooterImage . "'/>";
	}
}


?>

		<table CLASS="body" cellspacing="1">
			<tr>
				<td CLASS="FussZLaive"><?php echo $TxtFooterSiteLoadedIn . " ".sprintf('%.3f', microtime(true) - $startzeitdauer). " ". $TxtFooterSiteLoadedInUnit .". - "; echo $txt_laivefuss1; ?><BR>
				<?php echo $txt_laivefuss2; ?>
				</td>
			</tr>
		</table>
</div>
<?php
		if($_GET["sub"] != "uebersicht.php" && $_GET["sub"] != "gesamtteilnehmer.php" ) {
		}
		else {
		echo '<script type="text/javascript" src="sorttable.js"></script>';
		}
?>
</body>
</html>
<?php
ob_flush();
flush();
	# Startlistenerstellen ein
	if($StartlistenerstellenAutomatischAn == 1 && $_GET["sub"] == $dat_zeitplan) {
		if(file_exists("./laive_startlisten.txt")) {
			#$LetzteDurchfuehrung = file_get_contents("laive_startlisten.txt");
			if((filemtime("./laive_startlisten.txt") + $StartlistenerstellenAlleSekunden) < time()) {
				
				if($_SERVER['SERVER_NAME'] != "" && $_SERVER['REQUEST_URI']!= "" && $_SERVER['QUERY_STRING']!= "" && $_SERVER['SCRIPT_FILENAME']!= "") {
			
					$OrdnerReplacesArray = array($_SERVER['QUERY_STRING'], basename($_SERVER['SCRIPT_FILENAME']));
					$Ordnernamen = str_replace($OrdnerReplacesArray, "", $_SERVER['REQUEST_URI']);
					
					if(substr($Ordnernamen,  - 1) == "?") {
						$Ordnernamen = substr($Ordnernamen, 0, -1);
					}
					if(substr($Ordnernamen,  - 1) == "/") {
						$Ordnernamen = substr($Ordnernamen, 0, -1);
					}
					#echo $Ordnernamen;
			
					$startlistenerstellenAusfuehren = fopen('http://' . $_SERVER['SERVER_NAME'] . "/" . $Ordnernamen . '/index.php?sub=startlistenerstellen.php', 'r');
					fclose($startlistenerstellenAusfuehren);
				}
				else {
					include_once("./startlistenerstellen.php");
				}
			}
		}
		else {
			if($_SERVER['SERVER_NAME'] != "" && $_SERVER['REQUEST_URI']!= "" && $_SERVER['QUERY_STRING']!= "" && $_SERVER['SCRIPT_FILENAME']!= "") {
			
					$OrdnerReplacesArray = array($_SERVER['QUERY_STRING'], basename($_SERVER['SCRIPT_FILENAME']));
					$Ordnernamen = str_replace($OrdnerReplacesArray, "", $_SERVER['REQUEST_URI']);
					
					if(substr($Ordnernamen,  - 1) == "?") {
						$Ordnernamen = substr($Ordnernamen, 0, -1);
					}
					if(substr($Ordnernamen,  - 1) == "/") {
						$Ordnernamen = substr($Ordnernamen, 0, -1);
					}
					#echo $Ordnernamen;
			
					$startlistenerstellenAusfuehren = fopen('http://' . $_SERVER['SERVER_NAME'] . "/" . $Ordnernamen . '/index.php?sub=startlistenerstellen.php', 'r');
					fclose($startlistenerstellenAusfuehren);
				}
				else {
					include_once("./startlistenerstellen.php");
				}
		}
	
	}
	# Create EntryLists on
	if($CreateEntyListsOn == 1 && $_GET["sub"] == $dat_zeitplan) {
		if(file_exists("./laive_entrylists.txt")) {
			#$LetzteDurchfuehrung = file_get_contents("laive_startlisten.txt");
			if((filemtime("./laive_entrylists.txt") + $CreateEntyListsEverySecounds) < time()) {
				
				if($_SERVER['SERVER_NAME'] != "" && $_SERVER['REQUEST_URI']!= "" && $_SERVER['QUERY_STRING']!= "" && $_SERVER['SCRIPT_FILENAME']!= "") {
			
					$OrdnerReplacesArrayE = array($_SERVER['QUERY_STRING'], basename($_SERVER['SCRIPT_FILENAME']));
					$OrdnernamenE = str_replace($OrdnerReplacesArrayE, "", $_SERVER['REQUEST_URI']);
					
					if(substr($OrdnernamenE,  - 1) == "?") {
						$OrdnernamenE = substr($OrdnernamenE, 0, -1);
					}
					if(substr($OrdnernamenE,  - 1) == "/") {
						$OrdnernamenE = substr($OrdnernamenE, 0, -1);
					}
					#echo $Ordnernamen;
			
					$CreateEntylistsDo = fopen('http://' . $_SERVER['SERVER_NAME'] . "/" . $OrdnernamenE . '/index.php?sub=create_entrylists.php', 'r');
					fclose($CreateEntylistsDo);
				}
				else {
					include_once("./create_entrylists.php");
				}
			}
		}
		else {
			if($_SERVER['SERVER_NAME'] != "" && $_SERVER['REQUEST_URI']!= "" && $_SERVER['QUERY_STRING']!= "" && $_SERVER['SCRIPT_FILENAME']!= "") {
			
					$OrdnerReplacesArrayE = array($_SERVER['QUERY_STRING'], basename($_SERVER['SCRIPT_FILENAME']));
					$OrdnernamenE = str_replace($OrdnerReplacesArrayE, "", $_SERVER['REQUEST_URI']);
					
					if(substr($OrdnernamenE,  - 1) == "?") {
						$OrdnernamenE = substr($OrdnernamenE, 0, -1);
					}
					if(substr($OrdnernamenE,  - 1) == "/") {
						$OrdnernamenE = substr($OrdnernamenE, 0, -1);
					}
					#echo $Ordnernamen;
			
					$CreateEntylistsDo = fopen('http://' . $_SERVER['SERVER_NAME'] . "/" . $OrdnernamenE . '/index.php?sub=create_entrylists.php', 'r');
					fclose($CreateEntylistsDo);
				}
				else {
					include_once("./create_entrylists.php");
				}
		}
	
	}


ob_end_flush();
flush();
?>
