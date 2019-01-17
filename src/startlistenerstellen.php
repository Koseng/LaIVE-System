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
ignore_user_abort(1);
set_time_limit(120);
### LaIVE - Modul Zeitplan (zeitplan.php) /LaIVE - Module timetable (startlistenerstellen.php)
### Erstellt von / Created by Kilian Wenzel
### Zuletzt geändert: / Last change: 0.12.3.2014-03-17

# Datei der letzten Erzeugung anlegen

if($StartlistenerstellenAutomatischAn == 1) {
	$dateiStartlistenZeit = fopen("./laive_startlisten.txt", 'w');
	fwrite($dateiStartlistenZeit, time());
	fclose($dateiStartlistenZeit);
	}



########################################################################################

# DSB Mode - Startklassen einlesen / IPC Mode - Read start classes from file
if($IPCModeON == 1) {
	$DBSTextskl_startlist = IPCClassesArray();
}

if($StartlistenerstellenAutomatischAn != 1) {

?>

<table class="body" cellspacing="0">
			<tr><td class="KopfZ1"><?php echo $Kopfzeile1; ?></td></tr>
			<tr><td class="KopfZ11"><?php echo $Kopfzeile2; ?></td></tr>
			<tr><td class="KopfZ12"><?php echo $Kopfzeile3; ?></td></tr>
			</table>



		<table class="body" cellspacing="0">
		<tr><td class="KopfZ21">Startlisten erstellen</td></tr>
		</table>
		<br>
		<a>gestartet ...</a>
		<br>


<?php
}

# Verein.c01
if(file_exists($dat_verein)) {

	#$VereinInhaltArray = file($dat_verein);
	$VereinInhalt = file_get_contents($dat_verein);
	$VereinLaenge = strlen($VereinInhalt);
	$VereinLaengeDatensatz = 292;
	$VereinAnzahlDatensaetze = $VereinLaenge / $VereinLaengeDatensatz;
	
	$VereinDatensatzzaehler = 0;
	$VereinAbsolutePosition = 1;
	
	while($VereinDatensatzzaehler < $VereinAnzahlDatensaetze) {
	
		$VereinDatensatzzaehler++;
		
		$Verein[trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3))."-".trim(substr($VereinInhalt, $VereinAbsolutePosition + 2, 5))] = array(	'LV'		=>	trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3)),
																																						'VereinNr'	=> trim(substr($VereinInhalt, $VereinAbsolutePosition + 2, 5)),
																																						'VereinBez'	=> trim(substr($VereinInhalt, $VereinAbsolutePosition + 7, 30))
																																						);
		$VereinAbsolutePosition = $VereinAbsolutePosition + $VereinLaengeDatensatz;
	
	}


}

# Stamm.c01
if(file_exists($dat_stamm)) {

	#$StammInhaltArray = file($dat_stamm);
	$StammInhalt = file_get_contents($dat_stamm);
	$StammLaenge = strlen($StammInhalt);
	$StammLaengeDatensatz = 132;
	$StammAnzahlDatensaetze = $StammLaenge / $StammLaengeDatensatz;
	
	$StammDatensatzzaehler = 0;
	$StammAbsolutePosition = 1;
	
	while($StammDatensatzzaehler < $StammAnzahlDatensaetze) {
	
		$StammDatensatzzaehler++;
		
		# DBS Startklasse ermitteln / Set IPC Class
				if($IPCModeON == 1) {
					$SLTmpIPCClass = $DBSTextskl_startlist[trim(substr($StammInhalt, $StammAbsolutePosition + 66, 2)) * 1]['IPCClassName'];
				}
		
		$Stamm[trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5))] = array(	'StartNr'		=>	trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5)),
																					'Nachname'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 4, 22)),
																					'Vorname'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 27, 16)),
																					'JG'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 44, 4)),
																					'Geschlecht'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 48, 1)),
																					'LV'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 50, 3)),
																					'Verein'	=> $Verein[trim(substr($StammInhalt, $StammAbsolutePosition + 50, 3))."-".trim(substr($StammInhalt, $StammAbsolutePosition + 53, 5))]['VereinBez'],
																					'IPCClassName'			=> $SLTmpIPCClass
																					);
		$StammAbsolutePosition = $StammAbsolutePosition + $StammLaengeDatensatz;
	
	}


}


# Wettbewerb-Datei Wettbew.c01 verwenden
if(file_exists($dat_wettbew)) {

	#$WettbewInhaltArray = file($dat_wettbew);
	
	$WettbewInhalt = file_get_contents($dat_wettbew);
	
	$WettbewLaenge = strlen($WettbewInhalt);
	
	$WettbewLaengeDatensatz = 389;
	
	$WettbewAnzahlDatensaetze = $WettbewLaenge / $WettbewLaengeDatensatz;
	
	
	$WettbewDatensatzzaehler = 0;
	$WettbewAbsolutePositionDS = 1;
	
	while($WettbewDatensatzzaehler < $WettbewAnzahlDatensaetze) {
	
		$WettbewDatensatzzaehler++;
		
		# Sprunghöhen ermitteln
		
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 221, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 221, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 224, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 224, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 227, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 227, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 230, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 230, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 233, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 233, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 236, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 236, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 239, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 239, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 242, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 242, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 245, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 245, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 248, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 248, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 251, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 251, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 254, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 254, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 257, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 257, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 260, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 260, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 263, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 263, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 266, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 266, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 269, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 269, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 272, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 272, 3));}
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 275, 3)) != "") {$Sprunghoehen[] = trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 275, 3));}
		
		
		
		$Wettbew[trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3))] = array(	'WettbewNr'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3)),
							'WettbewBez'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 2, 32)),
							'StellplatzMin'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 71, 3)),
							'StellplatzZeit'	=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 74, 5)),
							'VorlaufZeit'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 79, 5)),
							'VorlaufTag'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 84, 1)),
							'ZwischenlaufZeit'	=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 85, 5)),
							'ZwischenlaufTag'	=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 90, 1)),
							'FinaleZeit'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 91, 5)),
							'FinaleTag'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 96, 1)),
							'COSANrAK'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 36, 2)),
							'COSANrDIS'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 38, 3)),
							'DISBez'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 296, 32)),
							'AKBez'				=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 331, 24)),
							'WettbewTyp'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 41, 1)),
							'QualiVorlaufAnzahlLaeufe'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 103, 2)),
							'QualiVorlaufPlatz'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 105, 2)),
							'QualiVorlaufZeit'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 107, 2)),
							'QualiVorlaufAnzahlZwischenlaeufe'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 109, 2)),
							'QualiVorlaufAnzahlFinals'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 111, 2)),
							'QualiZwischenlaufAnzahlLaeufe'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 113, 2)),
							'QualiZwischenlaufPlatz'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 115, 2)),
							'QualiZwischenlaufZeit'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 117, 2)),
							'QualiFinalsungleichberechtigt'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 120, 1)),
							'QualiFreitext1'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 121, 50)),
							'QualiFreitext2'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 171, 50)),
							'Sprunghoehen'			=> 	$Sprunghoehen						
		);			
		unset($Sprunghoehen);
		$WettbewAbsolutePositionDS = $WettbewAbsolutePositionDS + $WettbewLaengeDatensatz;
	
	}
	
	
	

}


# WbTeil.c01
if(file_exists($dat_wbteiln)) {

	#$WbTeilnInhaltArray = file($dat_wbteiln);
	$WbTeilnInhalt = file_get_contents($dat_wbteiln);
	$WbTeilnLaenge = strlen($WbTeilnInhalt);
	$WbTeilnLaengeDatensatz = 100;
	$WbTeilnAnzahlDatensaetze = $WbTeilnLaenge / $WbTeilnLaengeDatensatz;
	$WbTeilnDatensatzzaehler = 0;
	$WbTeilnAbsolutePositionDS = 1;
	
	while($WbTeilnDatensatzzaehler < $WbTeilnAnzahlDatensaetze) {
	
	$WbTeilnDatensatzzaehler++;
	
	
	
	$WbTeiln3[trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 9, 3)) ."-".trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS - 1, 5))] = array ( 	'StNr' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS - 1, 5)),
							'WettbewNr' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 4, 3)) * 1,
							'COSANr' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 7, 5)),
							'aW' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 22, 1)),
							'Meldeleistung' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 13, 8)),
							'AK' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 90, 3)),
							'LeistungVorlauf' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 52, 10)),
							'QualiVorlauf' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 62, 1)),
							'LeistungZwischenlauf' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 65, 10)),
							'QualiZwischenlauf' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 75, 1))
						);
	
	
	
	$WbTeilnAbsolutePositionDS = $WbTeilnAbsolutePositionDS + $WbTeilnLaengeDatensatz;

	}	

}




# WkList.c01
if(file_exists($dat_wklist)) {

	#$WkListInhaltArray = file($dat_wklist);
	$WkListInhalt = file_get_contents($dat_wklist);
	$WkListLaenge = strlen($WkListInhalt);
	$WkListLaengeDatensatz = 539;
	$WkListAnzahlDatensaetze = $WkListLaenge / $WkListLaengeDatensatz;
	
	$WkListDatensatzzaehler = 0;
	$WkListAbsolutePosition = 1;
	
	while($WkListDatensatzzaehler < $WkListAnzahlDatensaetze) {
	
		$WkListDatensatzzaehler++;
		
		if(is_numeric(trim(substr($WkListInhalt, $WkListAbsolutePosition + 10, 2)))) {
			$TmpRiege = trim(substr($WkListInhalt, $WkListAbsolutePosition + 10, 2)) * 1;
		}
		elseif(trim(substr($WkListInhalt, $WkListAbsolutePosition + 10, 1)) == "p") {
			$TmpRiege = ord(trim(substr($WkListInhalt, $WkListAbsolutePosition + 11, 1)));
		}
		else {
			$TmpRiege = 0;
		}
		
		#Gelöscht
		if(trim(substr($WkListInhalt, $WkListAbsolutePosition + 29, 2)) == "**") {
			$SLTmpGeloescht = 1;
			
		}
		else {
			$SLTmpGeloescht = 0;
		}
		
		if ($SLTmpGeloescht != 1) {
		
		
		
		
		# Unterscheidung ob Einzel oder Staffel
		switch($Wettbew[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1]['WettbewTyp']) {
		
		
		case "l": # Einzel
		case "h":
		case "t":
		case "w":
		case "m": # mehrkampf neu
		$WkListSL[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1 . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)) . $TmpRiege . trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1))] = array(	'WettbewNr'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1,
							'COSANr'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)),
							'Riege'			=>	$TmpRiege,
							'RundeTyp'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1)),
							'WettbewTyp'	=>	$Wettbew[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1]['WettbewTyp'],
							'Startdatum'	=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 13, 10)),
							'Startzeit'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 23, 5)),
							'Gemischt'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 10, 2)),
							'Sprunghoehen'		=>	$Wettbew[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1]['Sprunghoehen']
												);
						
		$StartpositionImDatensatz = $WkListAbsolutePosition + 40;
		$InternerZaehler = 0;
		
		while($InternerZaehler < 62) {
		
		$InternerZaehler++;
		

		
		
		
		if(trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5)) != "") {										
		$WkList2SL[] = array(	'WettbewNr'			=>	trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1,
							'COSANr'			=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)),
							'Riege'				=>	$TmpRiege,
							'RundeTyp'			=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1)),
							'LaufGruppe'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 8, 2)),
							'Pos'				=>	trim(substr($WkListInhalt, $StartpositionImDatensatz - 1, 3)),
							'StNr'				=>	trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5)),
							'Nachname'			=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5))]['Nachname'],
							'Vorname'			=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5))]['Vorname'],
							'IPCClassName'		=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5))]['IPCClassName'],
							'JG'				=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5))]['JG'],
							'Geschlecht'		=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5))]['Geschlecht'],
							'LV'				=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5))]['LV'],
							'Verein'			=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5))]['Verein'],
							'aW'				=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5)) * 1]['aW'],
							'Meldeleistung'		=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5)) * 1]['Meldeleistung'],
							'AK'				=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5)) * 1]['AK'],
							'AKMK'				=>	$WbTeiln3[$Wettbew[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3))*1]['COSANrDIS']."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5)) * 1]['AK'],
							'LeistungVorlauf'	=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5)) * 1]['LeistungVorlauf'],
							'QualiVorlauf'		=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5))* 1]['QualiVorlauf'],
							'LeistungZwischenlauf'=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5))* 1]['LeistungZwichenlauf'],
							'QualiZwischenlauf'	=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3))  ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5)) * 1]['QualiZwischenlauf']
							
												);										
		

			$AKGemischt[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1 ."-".trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5))."-".$TmpRiege][] = $WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5)) * 1]['AK'];

		
		}			
		
		$StartpositionImDatensatz = $StartpositionImDatensatz + 8;
		
		
		}									
		
		break;
		
		case "s": #Staffel
		
		$WkListSL[] = array(	'WettbewNr'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1,
							'COSANr'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)),
							'Riege'			=>	$TmpRiege,
							'RundeTyp'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1)),
							'WettbewTyp'	=>	$Wettbew[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1]['WettbewTyp'],
							'Startdatum'	=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 13, 10)),
							'Startzeit'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 23, 5)),
							'Gemischt'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 10, 2))
												);
						
		$StartpositionImDatensatz = $WkListAbsolutePosition + 40;
		$InternerZaehler = 0;
		
		while($InternerZaehler < 13) {
		
		$InternerZaehler++;
						
		if(trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5)) != "") {

		#Staffelteilnehmer
		if(trim(substr($WkListInhalt, $StartpositionImDatensatz + 6, 5)) != "") {
		$TmpStaffelTeiln[] = array( 'Pos'		=>	1,
									'StNr'		=>	trim(substr($WkListInhalt, $StartpositionImDatensatz + 6, 5)),
									'Nachname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 6, 5))]['Nachname'],
									'Vorname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 6, 5))]['Vorname'],
									'JG'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 6, 5))]['JG']
									);
		}
		if(trim(substr($WkListInhalt, $StartpositionImDatensatz + 11, 5)) != "") {
		$TmpStaffelTeiln[] = array( 'Pos'		=>	2,
									'StNr'		=>	trim(substr($WkListInhalt, $StartpositionImDatensatz + 11, 5)),
									'Nachname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 11, 5))]['Nachname'],
									'Vorname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 11, 5))]['Vorname'],
									'JG'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 11, 5))]['JG']
									);
		}
		if(trim(substr($WkListInhalt, $StartpositionImDatensatz + 16, 5)) != "") {
		$TmpStaffelTeiln[] = array( 'Pos'		=>	3,
									'StNr'		=>	trim(substr($WkListInhalt, $StartpositionImDatensatz + 16, 5)),
									'Nachname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 16, 5))]['Nachname'],
									'Vorname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 16, 5))]['Vorname'],
									'JG'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 16, 5))]['JG']
									);
		}							
		if(trim(substr($WkListInhalt, $StartpositionImDatensatz + 21, 5)) != "") {
		$TmpStaffelTeiln[] = array( 'Pos'		=>	4,
									'StNr'		=>	trim(substr($WkListInhalt, $StartpositionImDatensatz + 21, 5)),
									'Nachname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 21, 5))]['Nachname'],
									'Vorname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 21, 5))]['Vorname'],
									'JG'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 21, 5))]['JG']
									);
		}
		if(trim(substr($WkListInhalt, $StartpositionImDatensatz + 26, 5)) != "") {
		$TmpStaffelTeiln[] = array( 'Pos'		=>	5,
									'StNr'		=>	trim(substr($WkListInhalt, $StartpositionImDatensatz + 26, 5)),
									'Nachname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 26, 5))]['Nachname'],
									'Vorname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 26, 5))]['Vorname'],
									'JG'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 26, 5))]['JG']
									);
		}
		if(trim(substr($WkListInhalt, $StartpositionImDatensatz + 31, 5)) != "") {
		$TmpStaffelTeiln[] = array( 'Pos'		=>	6,
									'StNr'		=>	trim(substr($WkListInhalt, $StartpositionImDatensatz + 31, 5)),
									'Nachname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 31, 5))]['Nachname'],
									'Vorname'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 31, 5))]['Vorname'],
									'JG'	=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 31, 5))]['JG']
									);
		}
		
		$WkList2SL[] = array(	'WettbewNr'			=>	trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1,
							'COSANr'			=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)),
							'Riege'				=>	$TmpRiege,
							'RundeTyp'			=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1)),
							'LaufGruppe'		=>	trim(substr($WkListInhalt, $WkListAbsolutePosition + 8, 2)),
							'Pos'				=>	trim(substr($WkListInhalt, $StartpositionImDatensatz - 1, 3)),
							'StNr'				=>	trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5)),
							'Nachname'			=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5))]['Nachname'],
							'Vorname'			=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5))]['Vorname'],
							'IPCClassName'		=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 2, 5))]['IPCClassName'],
							'JG'				=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5))]['JG'],
							'Geschlecht'		=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5))]['Geschlecht'],
							'LV'				=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5))]['LV'],
							'Verein'			=>	$Stamm[trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5))]['Verein'],
							'aW'				=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5)) * 1]['aW'],
							'Meldeleistung'		=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5)) * 1]['Meldeleistung'],
							'AK'				=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5)) * 1]['AK'],
							'LeistungVorlauf'	=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5)) * 1]['LeistungVorlauf'],
							'QualiVorlauf'		=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5))* 1]['QualiVorlauf'],
							'LeistungZwischenlauf'=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5))* 1]['LeistungZwichenlauf'],
							'QualiZwischenlauf'	=>	$WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3))  ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5)) * 1]['QualiZwischenlauf'],
							'Mannschaftsteilnehmer' => $TmpStaffelTeiln
												);										
					$AKGemischt[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1 ."-".trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5))."-".$TmpRiege][] = $WbTeiln3[trim(substr($WkListInhalt, $WkListAbsolutePosition + 4, 3)) ."-".trim(substr($WkListInhalt, $StartpositionImDatensatz + 1, 5)) * 1]['AK'];
									
		}			
		unset($TmpStaffelTeiln);
		$StartpositionImDatensatz = $StartpositionImDatensatz + 38;
		
		
		}
		
		
		
		
		
		break;
		}
		
		
		} # Gelöscht
		else {
		if($IPCModeON != 1) {
			switch($Wettbew[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1]['WettbewTyp']) {
				default:
					if(file_exists("s" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)) . trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1)) . ".htm")) { # Falls Startliste schon auf Server vorhanden
						unlink("s" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)) . trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1)) . ".htm");
					}
					break;
				case "m":
					if(file_exists("s-" . $Wettbew[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1]['WettbewTyp'] . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1 . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)) . "-" . $TmpRiege . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1)) . ".htm")) { # Falls Startliste schon auf Server vorhanden
						unlink("s-" . $Wettbew[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1]['WettbewTyp'] . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1 . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)) . "-" . $TmpRiege . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1)) . ".htm");
					}
			}
		}
		else {
			if(file_exists("s-" . $Wettbew[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1]['WettbewTyp'] . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1 . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)) . "-" . $TmpRiege . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1)) . ".htm")) { # Falls Startliste schon auf Server vorhanden
						unlink("s-" . $Wettbew[trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1]['WettbewTyp'] . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition - 1, 3)) * 1 . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 2, 5)) . "-" . $TmpRiege . "-" . trim(substr($WkListInhalt, $WkListAbsolutePosition + 7, 1)) . ".htm");
					}
		}
		}


		
												
		$WkListAbsolutePosition = $WkListAbsolutePosition + $WkListLaengeDatensatz;
		
		
		
		
		
		
	
	}


}













#print_r($WkList2);
#echo "<hr>";
#print_r($Stamm);
#echo "<hr>";
#print_r($WkList2);
#echo "<hr>";
#print_r($WbTeiln);

# Sorting WkListSL Array
foreach ($WkListSL as $nr => $inhalt) {

	$SWettbewNr[$nr] = strtolower($inhalt['WettbewNr']);
	$SCOSANr[$nr] = strtolower($inhalt['COSANr']);
	$Riege[$nr] = strtolower($inhalt['Riege']);
	$RundeTyp[$nr] = strtolower($inhalt['RundeTyp']);
	$WettbewTyp[$nr] = strtolower($inhalt['WettbewTyp']);
	$Startdatum[$nr] = strtolower($inhalt['Startdatum']);
	$Startzeit[$nr] = strtolower($inhalt['Startzeit']);
	$Gemischt[$nr] = strtolower($inhalt['Gemischt']);
	
	
}
	
	array_multisort($SCOSANr, SORT_ASC, $WkListSL);


# Age groups for Start lists in one single file
$StartlistsOneFileAgeGroups = 0;
$StartlistsOneFileAgeGroupsBefore = 0;

$StartlistsOneFileLinklist = array();


### Ausgabe

# Startlist all events and rounds in one singe file (extra)
$StartlistsOneFileContent = "<table class='body' cellspacing='0'><tr><td class='KopfZ1'>$Kopfzeile1</td></tr><tr><td class='KopfZ11'>$Kopfzeile2</td></tr><tr><td class='KopfZ12'>$Kopfzeile3</td></tr></table>" . "<table class='body' cellspacing='0'><tr><td class='KopfZ21'>" . $TxtStartlistHeadlineAll ."</td></tr></table>";
$StartlistsOneFileContent = $StartlistsOneFileContent . "{{TemplateStartlistsLinks}}";

foreach($WkListSL as $WkListZeile) {

# Start list one single file
$StartlistsOneFileContent = $StartlistsOneFileContent . "<div class='holdtogether'>";

if($IPCModeON != 1) { # DSB/IPC Mode

switch($WkListZeile['WettbewTyp']) {

case "m": # Mehrkampf ##########################################################################################

$LaufGruppeVorher= "";
$StartlisteGemischtWeitereAKs = array();
$TmpWannNaechsteRunde = "";

$dateiname = "_tmp"."s"."-".$WkListZeile['WettbewTyp']."d-".$WkListZeile['WettbewNr']."-".$WkListZeile['COSANr']."-".$WkListZeile['Riege']."-".$WkListZeile['RundeTyp'].".htm";
$DateienStartlisten[] = $dateiname;

# gemischt
if($WkListZeile['Gemischt'] == "v") {
	$TmpGemischt = $TxtMixedEvent;
}
else {
	$TmpGemischt = "";
}

# Riege Ermitteln
if($WkListZeile['Riege'] > 0) {
	$TmpRiegeU = "- " . $TxtCombinedEventGroup . " ".$WkListZeile['Riege']." ";
}
else {
	$TmpRiegeU = "";
}


$datei = fopen($dateiname, 'w');


fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ1'>$Kopfzeile1</td></tr><tr><td class='KopfZ11'>$Kopfzeile2</td></tr><tr><td class='KopfZ12'>$Kopfzeile3</td></tr></table>");
fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ21'>" . $TxtStartlistHeadline . ": ".$Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Bez']." ".$TmpRiegeU."(".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez'].") ".$TmpGemischt."</td></tr></table>");

$Zeilenwechsler = 0;
$Start = 1;

$LaufGruppeVorher= "";
$TmpLaufGruppeUe = "";
foreach($WkList2SL as $WkList2Zeile) {



	

	if($WkList2Zeile['WettbewNr'] == $WkListZeile['WettbewNr'] && $WkList2Zeile['Riege'] == $WkListZeile['Riege'] && $WkList2Zeile['RundeTyp'] == $WkListZeile['RundeTyp'] && $WkList2Zeile['COSANr'] == $WkListZeile['COSANr'] ) {

	#Ermittlung von Geschlecht und Alter für Akl MK
	
	#Geschlecht
			switch($WkList2Zeile['Geschlecht']) {
				case 0: # männlich
					$tmpgeschlecht = $TxtAbrrevGenderMale;
					$tmpgeschlechtAK = $TxtAbrrevGenderMan;
				break;
				case 1: # weiblich
					$tmpgeschlecht = $TxtAbrrevGenderFemale;
					$tmpgeschlechtAK = $TxtAbrrevGenderWoman;
				break;
			}
		
		# AK
						
							if(is_numeric($WkList2Zeile['AKMK'])) {
								$TmpAKMK = $tmpgeschlechtAK.$WkList2Zeile['AKMK'];
							}
							else {
								$TmpAKMK = "";
							}
						
						
	
	

	
	# Ermittlung der Leistung bei der Ausgabe Vorleistung
	
	switch($WkListZeile['RundeTyp']) {
	
		case "a":
		case "c":
		case "e":
		case "r":
		case "s":
		case "n":
		case "q":
		
		
			$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
			$TmpVorleistungBez = $TxtSeasonBest;
		
		break;
		
		case "b": #Zwischenlauf
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
			else {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
				
			
			}
		
		
		break;
		
		case "d":
		case "k":
		case "l":
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == true && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
			}
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
			
			if(empty($WkList2Zeile['LeistungZwischenlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungZwischenlauf']). " <b>".$WkList2Zeile['QualiZwischenlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
		
			
		
		break;
	
	
	}
	
	
	if($WkList2Zeile['aW'] == 1) { $TmpaW = $TxtAbbrevOutOfRanking;} else {$TmpaW = " ";}
	
	# Bezeichnung Lauf/Gruppe
	switch($Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Typ']) {
	
		case "s":
		case "l":
		case "w":
		
			$TmpLaufGruppeBez = $TxtHeat;
		
		break;
		
		case "t":
		case "h":
		
			$TmpLaufGruppeBez = $TxtGroup;
		
		break;
	
	}
	
	if(is_numeric($WkList2Zeile['LaufGruppe'])) {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'] * 1;
		}
		else {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'];
		}
	
	if($TmpLaufGruppeUe == 0 && $Start == 1) {
	fwrite($datei, "<br>");
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettbMK'>".$Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Bez']." ".$TmpRiegeU."(".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez'].") "."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>&nbsp;</td></tr></table>");
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>");
		fwrite($datei, "<td CLASS='blEJGu'>" . $TxtAbbrevJOB  . "</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . "</td>");
		fwrite($datei, "<td CLASS='blELeistu'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");

		# Startlist one single file
		
		$StartlistsOneFileAgeID = substr($WkListZeile['COSANr'], 0, 2)*1;
		
		$StartlistsOneFileLinklist[$WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']] = array(	'EventID'	=>	$WkListZeile['WettbewNr'],
												'RiegeID'	=>  $WkListZeile['Riege'],
												'RoundTyp'	=>  $WkListZeile['RundeTyp'],
												'COSAID'	=> $WkListZeile['COSANr'] ,
												'Linkname'	=> $Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Bez']." ".$TmpRiegeU."(".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez'].")"
											);
		
		if($StartlistsOneFileAgeGroupsBefore <> $StartlistsOneFileAgeID) {
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='AklZ'><a name=agegroup". $StartlistsOneFileAgeID . ">" . $Klassen[$StartlistsOneFileAgeID]['Bez'] . "</a></td></tr></table>";
			$StartlistsOneFileAgeGroupsBefore = $StartlistsOneFileAgeID;
		}
		
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettbMK'><a name='round" . $WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']  . "'>".$Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Bez']." ".$TmpRiegeU."(".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez'].") "."</a></td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>" . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>&nbsp;</td></tr></table>" . "<table CLASS='body' cellspacing='0' cellpadding='0'>" . "<tr>" . "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>" . "<td CLASS='blEJGu'>" . $TxtAbbrevJOB  . "</td>" . "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>" . "<td CLASS='blEVereinu'>" . $TxtClub . "</td>" . "<td CLASS='blELeistu'>&nbsp;</td>" . "<td CLASS='blEPokPu'></td>" . "</tr>" .  "</table>";
		
		$Start = 0;
		$Zeilenwechsler = 0;
	
	}
	
		if(is_numeric($WkList2Zeile['LaufGruppe'])) {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'] * 1;
		}
		else {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'];
		}
	
	if($TmpLaufGruppeUe != $LaufGruppeVorher) {
	
	
		$Zeilenwechsler = 0;
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
		
		
	
		fwrite($datei, "<br>");
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettbMK'>".$Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Bez']." ".$TmpRiegeU."(".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez'].") "."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>");
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>");
		fwrite($datei, "<td CLASS='blEJGu'>" . $TxtAbbrevJOB . "</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . "</td>");
		fwrite($datei, "<td CLASS='blELeistu'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNr$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>");
		fwrite($datei, "<td CLASS='blELv$farbe'>".$WkList2Zeile['LV'] ."</td>");
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAKMK."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		# Start list one single file:
		 
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<br>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettbMK'>".$Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Bez']." ".$TmpRiegeU."(".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez'].") "."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>";
		
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJGu'>" . $TxtAbbrevJOB . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVereinu'>" . $TxtClub . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeistu'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokPu'></td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
		
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNr$farbe'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'>".$WkList2Zeile['LV'] ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeist$farbe'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokP$farbe'>".$TmpAKMK."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
	
		$Zeilenwechsler = 1;
	
	}
	else {
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
	
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNr$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAKMK ."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		# Start list one single file
		$StartlistsOneFileContent = $StartlistsOneFileContent .  "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent .  "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent .  "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent .  "<td CLASS='blEStNr$farbe'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent .  "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent .  "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>";
		
		if($FlagsOn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent .  "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>";}
		else {$StartlistsOneFileContent = $StartlistsOneFileContent .  "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>";}
		
		$StartlistsOneFileContent = $StartlistsOneFileContent .  "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent .  "<td CLASS='blELeist$farbe'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent .  "<td CLASS='blEPokP$farbe'>".$TmpAKMK ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent .  "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent .  "</table>";
	
	
	
	}

	$LaufGruppeVorher = $TmpLaufGruppeUe;
	}
	
	
	
	
	
	
	
	
} ###






			
			
fwrite($datei, "<br>");	

# Start List one single file
#$StartlistsOneFileContent = $StartlistsOneFileContent . "<br>";
		
fclose($datei);
$LaufGruppeVorher = " ";
$TmpLaufGruppeUe = "";
			
break;


default: # Alle Wettbewerbe außer MK #####################################################################################


$LaufGruppeVorher= "";
$StartlisteWeitereAKs = array();
$TmpWannNaechsteRunde = "";
$TmpLaufGruppeUe = "";

$dateiname = "_tmp"."s".$WkListZeile['COSANr'].$WkListZeile['RundeTyp'].".htm";
$DateienStartlisten[] = $dateiname;

# gemischt
if($WkListZeile['Gemischt'] == "v") {
	$TmpGemischt = $TxtMixedEvent;
}
else {
	$TmpGemischt = "";
}

# Rundentypen
			switch($WkListZeile['RundeTyp']) {
			
				case "a": #Vorläufe
					$HTmpRundeBez = $RundeTyp1;
				break;
				case "b": #Zwischenläufe
					$HTmpRundeBez = $RundeTyp2;
				break;
				case "c": #Ausscheidung
					$HTmpRundeBez = $RundeTyp4;
				break;
				case "d": #Zeitfinalläufe
					$HTmpRundeBez = $RundeTyp6;
				break;
				case "e": #Zeit-Vorläufe
					$HTmpRundeBez = $RundeTyp3;
				break;
				case "k": #Finale
					$HTmpRundeBez = $RundeTyp0;
				break;
				case "l": #ABFinale
					$HTmpRundeBez = $RundeTyp7;
				break;
				case "m": #nur Lauf-Nr.
					$HTmpRundeBez = $RundeTyp8;
				break;
				case "n": #Finale Techn./Hoch
					$HTmpRundeBez = $RundeTyp0;
				break;
				case "q": #Finale Techn./Hoch
					$HTmpRundeBez = $RundeTyp0;
				break;
				case "r": #Ausscheidung Techn./Hoch
					$HTmpRundeBez = $RundeTyp4;
				break;
				case "s": #Qualifikation Tech./Hoch
					$HTmpRundeBez = $RundeTyp5;
				break;
			
			}
			
			
	
			

$datei = fopen($dateiname, 'w');


if($WkListZeile['Gemischt'] != "v") {
	fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ1'>$Kopfzeile1</td></tr><tr><td class='KopfZ11'>$Kopfzeile2</td></tr><tr><td class='KopfZ12'>$Kopfzeile3</td></tr></table>");
	fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ21'>" . $TxtStartlistHeadline . ": ".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez." ".$TmpGemischt."</td></tr></table>");
}
else { # Gemischter Wettbewerb


foreach($AKGemischt[$WkListZeile['WettbewNr']."-".$WkListZeile['COSANr']."-".$WkListZeile['Riege']] as $GemischtAKZeile) {

	
	$TmpAKGemischtArray = (array_multi_search($GemischtAKZeile, $Klassen));
	$AKGemischtArray[] = $TmpAKGemischtArray[0]['Bez'];
	sort($AKGemischtArray);
	unset($TmpAKGemischtArray);
	
}

	$AKGemischtArray2 = array_unique($AKGemischtArray);
	
	$ZaehlerAKGemischt = 0;
	foreach($AKGemischtArray2 as $AKGemischtArray2Zeile) {
		$ZaehlerAKGemischt++;
		
		if(count($AKGemischtArray2) > $ZaehlerAKGemischt) {
			$TmpGemischtTrenner = ", ";
		}
		else {
			$TmpGemischtTrenner = "";
		}
		
		$TmpAKUeberschrift = $TmpAKUeberschrift . $AKGemischtArray2Zeile . $TmpGemischtTrenner;
	}
	
	fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ1'>$Kopfzeile1</td></tr><tr><td class='KopfZ11'>$Kopfzeile2</td></tr><tr><td class='KopfZ12'>$Kopfzeile3</td></tr></table>");
	fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ21'>Startliste: ".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez." ".$TmpGemischt."</td></tr></table>");
}
$ZaehlerAKGemischt = 0;

	





		
		
$Zeilenwechsler = 0;
$Start = 1;

$LaufGruppeVorher= "";
$TmpLaufGruppeUe = "";
foreach($WkList2SL as $WkList2Zeile) {



if(is_numeric($WkList2Zeile['LaufGruppe'])) {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'] * 1;
		}
		else {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'];
		}



	switch($Wettbew[$WkListZeile['WettbewNr']]['WettbewTyp']) {
	
	case "l":
	case "t":
	case "h":
	case "w":
			

	if($WkList2Zeile['WettbewNr'] == $WkListZeile['WettbewNr'] && $WkList2Zeile['Riege'] == $WkListZeile['Riege'] && $WkList2Zeile['RundeTyp'] == $WkListZeile['RundeTyp']) {

	# Gemischte Wettbewerbe - Bezeichnungen der Aks und doppelte Ausgabe der Startliste

if($WkList2Zeile['AK'] != "" && $WkListZeile['Gemischt'] == "v") {

	$TmpAKArray = (array_multi_search($WkList2Zeile['AK'], $Klassen));
	$TmpAK = "".$TmpAKArray[0]['Bez']."";
	
	
	if($TmpAKArray[0]['Nr'] != substr($WkListZeile['COSANr'], 0, 2)) {
	$StartlisteGemischtWeitereAKs["s" . $TmpAKArray[0]['Nr']. substr($WkListZeile['COSANr'], 2, 3) . $WkListZeile['RundeTyp']] = "_tmp"."s" . $TmpAKArray[0]['Nr']. substr($WkListZeile['COSANr'], 2, 3) . $WkListZeile['RundeTyp'].".htm";
	}
	unset($TmpAKArray);
	
	
	
}
else {
	$TmpAK = "";
}
	
	
	# Ermittlung der Leistung bei der Ausgabe Vorleistung
	
	switch($WkListZeile['RundeTyp']) {
	
		case "a":
		case "c":
		case "e":
		case "r":
		case "s":
		case "n":
		case "q":
		case "m":
		
			$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
			$TmpVorleistungBez = $TxtSeasonBest;
		
		break;
		
		case "b": #Zwischenlauf
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
			else {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
				
			
			}
		
		
		break;
		
		case "d":
		case "k":
		case "l":
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == true && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
			}
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
			
			if(empty($WkList2Zeile['LeistungZwischenlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungZwischenlauf']). " <b>".$WkList2Zeile['QualiZwischenlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
		
			
		
		break;
	
	
	}
	
	
	if($WkList2Zeile['aW'] == 1) { $TmpaW = $TxtAbbrevOutOfRanking;} else {$TmpaW = " ";}
	
	# Bezeichnung Lauf/Gruppe
	switch($Wettbew[$WkListZeile['WettbewNr']]['WettbewTyp']) {
	
		case "s":
		case "l":
		case "w":
		
			$TmpLaufGruppeBez = $TxtHeat;
		
		break;
		
		case "t":
		case "h":
		
			$TmpLaufGruppeBez = $TxtGroup;
		
		break;
	
	}
	
	if($TmpLaufGruppeUe === 0 && $Start == 1) {
	fwrite($datei, "<br>");
	
	# Start List one single file
	$StartlistsOneFileContent = $StartlistsOneFileContent . "<br>";
	
		if($WkListZeile['Gemischt'] != "v") {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez."</td></tr></table>");
			
			# Start list one single file
			
			
			
			$StartlistsOneFileAgeID = substr($WkListZeile['COSANr'], 0, 2)*1;
			
			$StartlistsOneFileLinklist[$WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']] = array(	'EventID'	=>	$WkListZeile['WettbewNr'],
												'RiegeID'	=>  $WkListZeile['Riege'],
												'RoundTyp'	=>  $WkListZeile['RundeTyp'],
												'COSAID'	=> $WkListZeile['COSANr'] ,
												'Linkname'	=> $Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez
											);
			
			if($StartlistsOneFileAgeGroupsBefore <> $StartlistsOneFileAgeID) {
				$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='AklZ'><a name=agegroup". $StartlistsOneFileAgeID . ">" . $Klassen[$StartlistsOneFileAgeID]['Bez'] . "</a></td></tr></table>";
				$StartlistsOneFileAgeGroupsBefore = $StartlistsOneFileAgeID;
			}
			
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'><a name='round" . $WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']  . "'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</a></td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>";
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez."</td></tr></table>";
			
		}
		else {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez."</td></tr></table>");
			
			# Start list one single file
			
			

			$StartlistsOneFileAgeID = substr($WkListZeile['COSANr'], 0, 2)*1;
			
			$StartlistsOneFileLinklist[$WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']] = array(	'EventID'	=>	$WkListZeile['WettbewNr'],
												'RiegeID'	=>  $WkListZeile['Riege'],
												'RoundTyp'	=>  $WkListZeile['RundeTyp'],
												'COSAID'	=> $WkListZeile['COSANr'] ,
												'Linkname'	=> $Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez
											);
			
			
			if($StartlistsOneFileAgeGroupsBefore <> $StartlistsOneFileAgeID) {
				$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='AklZ'><a name=agegroup". $StartlistsOneFileAgeID . ">" . $Klassen[$StartlistsOneFileAgeID]['Bez'] . "</a></td></tr></table>";
				$StartlistsOneFileAgeGroupsBefore = $StartlistsOneFileAgeID;
			}			
			
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'><a name='round" . $WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']  . "'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</a></td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>";
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez."</td></tr></table>";
			
		}
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>");
		fwrite($datei, "<td CLASS='blEJGu'>" . $TxtAbbrevJOB . "</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . "</td>");
		fwrite($datei, "<td CLASS='blELeistu'>$TmpVorleistungBez</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		# Start list one single file
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJGu'>" . $TxtAbbrevJOB . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVereinu'>" . $TxtClub . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeistu'>$TmpVorleistungBez</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokPu'></td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
		
		
		$Start = 0;
		$Zeilenwechsler = 0;
	
	}
	if(is_numeric($WkList2Zeile['LaufGruppe'])) {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'] * 1;
		}
		else {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'];
		}
	
	if($TmpLaufGruppeUe != $LaufGruppeVorher) {
	
	
		$Zeilenwechsler = 0;
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
		
		
		
	
		fwrite($datei, "<br>");
		
		# Start List one single file
	$StartlistsOneFileContent = $StartlistsOneFileContent . "<br>";
	
		if($WkListZeile['Gemischt'] != "v") {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>");
		
			# Start list one single file
			
			

			$StartlistsOneFileAgeID = substr($WkListZeile['COSANr'], 0, 2)*1;
			
			$StartlistsOneFileLinklist[$WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']] = array(	'EventID'	=>	$WkListZeile['WettbewNr'],
												'RiegeID'	=>  $WkListZeile['Riege'],
												'RoundTyp'	=>  $WkListZeile['RundeTyp'],
												'COSAID'	=> $WkListZeile['COSANr'] ,
												'Linkname'	=> $Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez
											);
			
			if($StartlistsOneFileAgeGroupsBefore <> $StartlistsOneFileAgeID) {
				$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='AklZ'><a name=agegroup". $StartlistsOneFileAgeID . ">" . $Klassen[$StartlistsOneFileAgeID]['Bez'] . "</a></td></tr></table>";
				$StartlistsOneFileAgeGroupsBefore = $StartlistsOneFileAgeID;
			}
			
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'><a name='round" . $WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']  . "'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</a></td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>";
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>";
		
		}
		else {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>");
			
			#Start list one single file
			
			

			$StartlistsOneFileAgeID = substr($WkListZeile['COSANr'], 0, 2)*1;
			
			$StartlistsOneFileLinklist[$WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']] = array(	'EventID'	=>	$WkListZeile['WettbewNr'],
												'RiegeID'	=>  $WkListZeile['Riege'],
												'RoundTyp'	=>  $WkListZeile['RundeTyp'],
												'COSAID'	=> $WkListZeile['COSANr'] ,
												'Linkname'	=> $Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez
											);
											
			if($StartlistsOneFileAgeGroupsBefore <> $StartlistsOneFileAgeID) {
				$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='AklZ'><a name=agegroup". $StartlistsOneFileAgeID . ">" . $Klassen[$StartlistsOneFileAgeID]['Bez'] . "</a></td></tr></table>";
				$StartlistsOneFileAgeGroupsBefore = $StartlistsOneFileAgeID;
			}			

			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'><a name='round" . $WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']  . "'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</a></td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>";
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>";
			
		}
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>");
		fwrite($datei, "<td CLASS='blEJGu'>" . $TxtAbbrevJOB . "</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . "</td>");
		fwrite($datei, "<td CLASS='blELeistu'>$TmpVorleistungBez</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNr$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAK ."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		# Start list one single file
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJGu'>" . $TxtAbbrevJOB . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVereinu'>" . $TxtClub . "</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeistu'>$TmpVorleistungBez</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokPu'></td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
		
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNr$farbe'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>";
		
		if($FlagsOn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>";}
		else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>";}
		
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokP$farbe'>".$TmpAK ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
		
	
		$Zeilenwechsler = 1;
	
	}
	else {
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
	
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNr$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAK ."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
	
		# Start list one single file
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNr$farbe'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>";
		
		if($FlagsOn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>";}
		else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>";}
		
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokP$farbe'>".$TmpAK ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
	
	
	}

	$LaufGruppeVorher = $TmpLaufGruppeUe;
	}
	
	break;
	
	case "s": # Staffel
	
	
	
	if($WkList2Zeile['WettbewNr'] == $WkListZeile['WettbewNr'] && $WkList2Zeile['Riege'] == $WkListZeile['Riege'] && $WkList2Zeile['RundeTyp'] == $WkListZeile['RundeTyp']) {
	
	# Gemischte Wettbewerbe - Bezeichnungen der Aks und doppelte Ausgabe der Startliste

if($WkList2Zeile['AK'] != "" && $WkListZeile['Gemischt'] == "v") {

	$TmpAKArray = (array_multi_search($WkList2Zeile['AK'], $Klassen));
	$TmpAK = "".$TmpAKArray[0]['Bez']."";
	
	if($TmpAKArray[0]['Nr'] != substr($WkListZeile['COSANr'], 0, 2)) {
	$StartlisteGemischtWeitereAKs["s" . $TmpAKArray[0]['Nr']. substr($WkListZeile['COSANr'], 2, 3) . $WkListZeile['RundeTyp']] = "_tmp"."s" . $TmpAKArray[0]['Nr']. substr($WkListZeile['COSANr'], 2, 3) . $WkListZeile['RundeTyp'].".htm";
	}
	unset($TmpAKArray);
	
	
	
}
else {
	$TmpAK = "";
}

	# Ermittlung der Leistung bei der Ausgabe Vorleistung
	
	switch($WkListZeile['RundeTyp']) {
	
		case "a":
		case "c":
		case "e":
		case "r":
		case "s":
		case "n":
		case "q":
		case "m":
		
			$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
			$TmpVorleistungBez = $TxtSeasonBest;
		
		break;
		
		case "b": #Zwischenlauf
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
			else {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
			
			}
		
		
		break;
		
		case "d":
		case "k":
		case "l":
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == true && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
				$TmpZeileStaffel = "";
			}
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
				$TmpZeileStaffel = "<br>";
			
			}
			
			if(empty($WkList2Zeile['LeistungZwischenlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungZwischenlauf']). " <b>".$WkList2Zeile['QualiZwischenlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
				$TmpZeileStaffel = "<br>";
			}
		
			
		
		break;
	
	
	}
	
	
	
	if($WkList2Zeile['aW'] == 1) { $TmpaW = "a.W.";} else {$TmpaW = " ";}
	
	# Bezeichnung Lauf/Gruppe
	switch($Wettbew[$WkListZeile['WettbewNr']]['WettbewTyp']) {
	
		case "s":
		case "l":
		case "w":
		
			$TmpLaufGruppeBez = $TxtHeat;
		
		break;
		
		case "t":
		case "h":
		
			$TmpLaufGruppeBez = $TxtGroup;
		
		break;
	
	}
	
	if($TmpLaufGruppeUe === 0 && $Start == 1) {
	fwrite($datei, "<br>");
	
	# Start List one single file
	$StartlistsOneFileContent = $StartlistsOneFileContent . "<br>";
	
		if($WkListZeile['Gemischt'] != "v") {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez."</td></tr></table>");
			
			# Start list one single file
			
			
			
			$StartlistsOneFileAgeID = substr($WkListZeile['COSANr'], 0, 2)*1;
			
			$StartlistsOneFileLinklist[$WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']] = array(	'EventID'	=>	$WkListZeile['WettbewNr'],
												'RiegeID'	=>  $WkListZeile['Riege'],
												'RoundTyp'	=>  $WkListZeile['RundeTyp'],
												'COSAID'	=> $WkListZeile['COSANr'] ,
												'Linkname'	=> $Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez
											);
			
			if($StartlistsOneFileAgeGroupsBefore <> $StartlistsOneFileAgeID) {
				$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='AklZ'><a name=agegroup". $StartlistsOneFileAgeID . ">" . $Klassen[$StartlistsOneFileAgeID]['Bez'] . "</a></td></tr></table>";
				$StartlistsOneFileAgeGroupsBefore = $StartlistsOneFileAgeID;
			}
			
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'><a name='round" . $WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']  . "'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</a></td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>";
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez."</td></tr></table>";
			
		}
		else {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez."</td></tr></table>");
			
			#Start list one single file
			
			

			$StartlistsOneFileAgeID = substr($WkListZeile['COSANr'], 0, 2)*1;
			
			$StartlistsOneFileLinklist[$WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']] = array(	'EventID'	=>	$WkListZeile['WettbewNr'],
												'RiegeID'	=>  $WkListZeile['Riege'],
												'RoundTyp'	=>  $WkListZeile['RundeTyp'],
												'COSAID'	=> $WkListZeile['COSANr'] ,
												'Linkname'	=> $Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez
											);
			
			if($StartlistsOneFileAgeGroupsBefore <> $StartlistsOneFileAgeID) {
				$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='AklZ'><a name=agegroup". $StartlistsOneFileAgeID . ">" . $Klassen[$StartlistsOneFileAgeID]['Bez'] . "</a></td></tr></table>";
				$StartlistsOneFileAgeGroupsBefore = $StartlistsOneFileAgeID;
			}			
			
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'><a name='round" . $WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']  . "'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</a></td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>";
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez."</td></tr></table>";
			
			
		}
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>".$TxtAbbrevOrdner."<br>".$TmpZeileStaffel . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIBRelay . "<br>".$TmpZeileStaffel . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtRelayTeam . "<br>".$TmpZeileStaffel . $TxtRelayMembers ."</td>");
		fwrite($datei, "<td CLASS='blEJGu'>&nbsp;<br>".$TmpZeileStaffel.$TxtAbbrevJOB."</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "<br>".$TmpZeileStaffel."&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . $TmpZeileStaffel."<br>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blELeistu'>".$TmpVorleistungBez."<br>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		# Start list one single file
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERangu'>".$TxtAbbrevOrdner."<br>".$TmpZeileStaffel . $TxtAbbrevOrdner . "</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>" . $TxtAbbrevBIBRelay . "<br>".$TmpZeileStaffel . $TxtAbbrevBIB . "</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameASu'>" . $TxtRelayTeam . "<br>".$TmpZeileStaffel . $TxtRelayMembers ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJGu'>&nbsp;<br>".$TmpZeileStaffel.$TxtAbbrevJOB."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELvu'>" . $TxtAbbrevNation . "<br>".$TmpZeileStaffel."&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVereinu'>" . $TxtClub . $TmpZeileStaffel."<br>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeistu'>".$TmpVorleistungBez."<br>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokPu'></td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
		
		
		
		$Start = 0;
		$Zeilenwechsler = 0;
	
	}
	
			if(is_numeric($WkList2Zeile['LaufGruppe'])) {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'] * 1;
		}
		else {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'];
		}
	
	
	if($TmpLaufGruppeUe != $LaufGruppeVorher) {
	
	
		$Zeilenwechsler = 0;
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
		
	
		
		
	
		fwrite($datei, "<br>");
		
		# Start list one single file
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<br>";
		
		if($WkListZeile['Gemischt'] != "v") {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>");
			
			# Start list one single file
			
			

			$StartlistsOneFileAgeID = substr($WkListZeile['COSANr'], 0, 2)*1;
			
			$StartlistsOneFileLinklist[$WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']] = array(	'EventID'	=>	$WkListZeile['WettbewNr'],
												'RiegeID'	=>  $WkListZeile['Riege'],
												'RoundTyp'	=>  $WkListZeile['RundeTyp'],
												'COSAID'	=> $WkListZeile['COSANr'] ,
												'Linkname'	=> $Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez
											);
											
			if($StartlistsOneFileAgeGroupsBefore <> $StartlistsOneFileAgeID) {
				$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='AklZ'><a name=agegroup". $StartlistsOneFileAgeID . ">" . $Klassen[$StartlistsOneFileAgeID]['Bez'] . "</a></td></tr></table>";
				$StartlistsOneFileAgeGroupsBefore = $StartlistsOneFileAgeID;
			}
			
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'><a name='round" . $WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']  . "'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</a></td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>";
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>";
			
		}
		else {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>");
			
			# Start list one single file
			
			

			$StartlistsOneFileAgeID = substr($WkListZeile['COSANr'], 0, 2)*1;
			
			$StartlistsOneFileLinklist[$WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']] = array(	'EventID'	=>	$WkListZeile['WettbewNr'],
												'RiegeID'	=>  $WkListZeile['Riege'],
												'RoundTyp'	=>  $WkListZeile['RundeTyp'],
												'COSAID'	=> $WkListZeile['COSANr'] ,
												'Linkname'	=> $Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez
											);
			
			if($StartlistsOneFileAgeGroupsBefore <> $StartlistsOneFileAgeID) {
				$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='AklZ'><a name=agegroup". $StartlistsOneFileAgeID . ">" . $Klassen[$StartlistsOneFileAgeID]['Bez'] . "</a></td></tr></table>";
				$StartlistsOneFileAgeGroupsBefore = $StartlistsOneFileAgeID;
			}
			
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'><a name='round" . $WkListZeile['WettbewNr'] . "-" . $WkListZeile['COSANr'] . "-" . $WkListZeile['Riege'] . "-" . $WkListZeile['RundeTyp']  . "'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</a></td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>";
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>";
			
		}
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>".$TxtAbbrevOrdner."<br>".$TmpZeileStaffel . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIBRelay . "<br>".$TmpZeileStaffel . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtRelayTeam . "<br>".$TmpZeileStaffel . $TxtRelayMembers ."</td>");
		fwrite($datei, "<td CLASS='blEJGu'>&nbsp;<br>".$TmpZeileStaffel.$TxtAbbrevJOB."</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "<br>".$TmpZeileStaffel."&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . $TmpZeileStaffel."<br>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blELeistu'>".$TmpVorleistungBez."<br>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangStaffel$farbe'><a CLASS='blERangStaffel$farbe'>".$WkList2Zeile['Pos'] ."</a></td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNrStaffel$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNrStaffel$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASStaffel$farbe'>".$WkList2Zeile['Verein']." ".$Roemisch[$WkList2Zeile['JG']]."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>&nbsp;</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAK ."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		# Start list one single file
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERangu'>".$TxtAbbrevOrdner."<br>".$TmpZeileStaffel . $TxtAbbrevOrdner . "</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>" . $TxtAbbrevBIBRelay . "<br>".$TmpZeileStaffel . $TxtAbbrevBIB . "</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNru'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameASu'>" . $TxtRelayTeam . "<br>".$TmpZeileStaffel . $TxtRelayMembers ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJGu'>&nbsp;<br>".$TmpZeileStaffel.$TxtAbbrevJOB."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELvu'>" . $TxtAbbrevNation . "<br>".$TmpZeileStaffel."&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVereinu'>" . $TxtClub . $TmpZeileStaffel."<br>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeistu'>".$TmpVorleistungBez."<br>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokPu'></td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
		
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERangStaffel$farbe'><a CLASS='blERangStaffel$farbe'>".$WkList2Zeile['Pos'] ."</a></td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNrStaffel$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNrStaffel$farbe'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameASStaffel$farbe'>".$WkList2Zeile['Verein']." ".$Roemisch[$WkList2Zeile['JG']]."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJG$farbe'>&nbsp;</td>";
		
		if($FlagsOn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>";}
		else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>";}
		
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokP$farbe'>".$TmpAK ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
		
		
		
		
		
		foreach($WkList2Zeile['Mannschaftsteilnehmer'] as $MannschaftsTNZeile) {
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangMannschaftsTN$farbe'>".$MannschaftsTNZeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNrMannschaftsTN$farbe'>".$MannschaftsTNZeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNrMannschaftsTN$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASMannschaftsTN$farbe'>".$MannschaftsTNZeile['Nachname'].", ".$MannschaftsTNZeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJGMannschaftsTN$farbe'>".$MannschaftsTNZeile['JG']."</td>");
		fwrite($datei, "<td CLASS='blELv$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEVerein$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>&nbsp;</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		# Start list one single file
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERangMannschaftsTN$farbe'>".$MannschaftsTNZeile['Pos'] ."</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNrMannschaftsTN$farbe'>".$MannschaftsTNZeile['StNr'] ."</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNrMannschaftsTN$farbe'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameASMannschaftsTN$farbe'>".$MannschaftsTNZeile['Nachname'].", ".$MannschaftsTNZeile['Vorname']."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJGMannschaftsTN$farbe'>".$MannschaftsTNZeile['JG']."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVerein$farbe'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeist$farbe'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokP$farbe'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
		
		
		}
		
	
		$Zeilenwechsler = 1;
	
	}
	else {
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
	
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangStaffel$farbe'><a CLASS='blERangStaffel$farbe'>".$WkList2Zeile['Pos'] ."</a></td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNrStaffel$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNrStaffel$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASStaffel$farbe'>".$WkList2Zeile['Verein']." ".$Roemisch[$WkList2Zeile['JG']]."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>&nbsp;</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAK ."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		# Start list one single file
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERangStaffel$farbe'><a CLASS='blERangStaffel$farbe'>".$WkList2Zeile['Pos'] ."</a></td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNrStaffel$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNrStaffel$farbe'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameASStaffel$farbe'>".$WkList2Zeile['Verein']." ".$Roemisch[$WkList2Zeile['JG']]."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJG$farbe'>&nbsp;</td>";
		
		if($FlagsOn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>";}
		else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>";}
		
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokP$farbe'>".$TmpAK ."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
		
		
		
		foreach($WkList2Zeile['Mannschaftsteilnehmer'] as $MannschaftsTNZeile) {
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangMannschaftsTN$farbe'>".$MannschaftsTNZeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNrMannschaftsTN$farbe'>".$MannschaftsTNZeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNrMannschaftsTN$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASMannschaftsTN$farbe'>".$MannschaftsTNZeile['Nachname'].", ".$MannschaftsTNZeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJGMannschaftsTN$farbe'>".$MannschaftsTNZeile['JG']."</td>");
		fwrite($datei, "<td CLASS='blELv$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEVerein$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>&nbsp;</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		# Start list one single file
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<table CLASS='body' cellspacing='0' cellpadding='0'>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blERangMannschaftsTN$farbe'>".$MannschaftsTNZeile['Pos'] ."</td>";
		if($StartnummernAn == 1) {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNrMannschaftsTN$farbe'>".$MannschaftsTNZeile['StNr'] ."</td>";} else {$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEStNrMannschaftsTN$farbe'>&nbsp;</td>";}
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blENameASMannschaftsTN$farbe'>".$MannschaftsTNZeile['Nachname'].", ".$MannschaftsTNZeile['Vorname']."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEJGMannschaftsTN$farbe'>".$MannschaftsTNZeile['JG']."</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELv$farbe'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEVerein$farbe'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blELeist$farbe'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "<td CLASS='blEPokP$farbe'>&nbsp;</td>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr>";
		$StartlistsOneFileContent = $StartlistsOneFileContent . "</table>";
		
		}
		
		
	
	
	}

	$LaufGruppeVorher = $TmpLaufGruppeUe;
	}
	
	
	
	
	
	break;
	}
	
	
	
	
	
	
}






fwrite($datei, "<br>");

# Start List one single file
#	$StartlistsOneFileContent = $StartlistsOneFileContent . "<br>";
	
# Qualifikationsmodus	und Sprunghöhen
	
	
	switch ($WkListZeile['WettbewTyp']) { # Auswahl nach Wettbewerbstyp
	
		case "l": # Lauf Bahn
		case "s": # Staffel Bahn
		
			switch($WkListZeile['RundeTyp']) {
			
				case "a": #Vorlauf
				case "e": #Zeitvorlauf
				
					
				
					if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlLaeufe']) == false || empty($Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext1']) == false) {
						# Wenn Anzahl Vorläufe oder Freitext1 nicht leer ist, dann gibt es einen Qualimodus
						
						if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlLaeufe']) == false){ # Wenn Anzahl Läufe nicht leer, dann Standardmodus verwenden
						
							# Ermitteln, welche Läufe
							switch($WkListZeile['RundeTyp']) {
			
								case "a": #Vorläufe
									
									if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlLaeufe'] == 1) {
										$TmpQualiVorrundeBez = $TxtQualificationHeat;
									}
									else {
										$TmpQualiVorrundeBez = $TxtQualificationHeats;
									}
									
								break;
								case "e": #Zeit-Vorläufe
								
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlLaeufe'] == 1) {
										$TmpQualiVorrundeBez = $TxtQualificationTimedHeat;
									}
									else {
										$TmpQualiVorrundeBez = $TxtQualificationTimedHeats;
									}
								
									
								break;
			
							}
							
							# Quali Platz Bezeichnung
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufPlatz']) == false) {
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufPlatz'] == 1) {
									$TmpQualiPlatz = $TxtQualificationByPlaceTrack1;
								}
								else {
									$TmpQualiPlatz = $TxtQualificationByPlaceTrackMore;
								}
							}
							else {
								$TmpQualiPlatz = "";	
							}
							
							# Quali Zeit Bezeichnung
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufZeit']) == false) {
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufZeit'] == 1) {
									$TmpQualiZeit = $TxtQualificationByTimeTrack1;
								}
								else {
									$TmpQualiZeit = $TxtQualificationByTimeTrackMore;
								}
							}
							else {
								$TmpQualiZeit = "";	
							}
							
							# Bezeichnung Quali für was
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlZwischenlaeufe']) == false) {
							
									
									if($WkListZeile['Startdatum'] == $tage[$Wettbew[$WkListZeile['WettbewNr']]['ZwischenlaufTag']]) {
										$TmpWannNaechsteRunde = " (". $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['ZwischenlaufZeit']." ". $TxtDaytimeUnit.")";
									}
									else {
										$TmpWannNaechsteRunde = "<br> (". $TxtOnThe. " ".$tage[$Wettbew[$WkListZeile['WettbewNr']]['ZwischenlaufTag']]." " . $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['ZwischenlaufZeit']." ". $TxtAt . ")";
									}
							
							
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlZwischenlaeufe'] == 1) {
									$TmpQualiNaechsteRundeBez = $TxtQualificationToSemiFinal;
								}
								else {
									$TmpQualiNaechsteRundeBez = $TxtQualificationToSemiFinals;
								}
							}
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals']) == false) {
							
									if($WkListZeile['Startdatum'] == $tage[$Wettbew[$WkListZeile['WettbewNr']]['FinaleTag']]) {
										$TmpWannNaechsteRunde = " (" . $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['FinaleZeit']." " . $TxtDaytimeUnit . ")";
									}
									else {
										$TmpWannNaechsteRunde = "<br> (" . $TxtOnThe. " ".$tage[$Wettbew[$WkListZeile['WettbewNr']]['FinaleTag']]." " . $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['FinaleZeit']." " . $TxtDaytimeUnit . ")";
									}
							
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiFinalsungleichberechtigt'] == 1) {
							
									if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals'] == 1) {
										$TmpQualiNaechsteRundeBez = $TxtQualificationToFinal;
									}
									else {
										$TmpQualiNaechsteRundeBez = $TxtQualificationToFinalsNotEqual;
									}
								
								}
								else {
								
									if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals'] == 1) {
										$TmpQualiNaechsteRundeBez = $TxtQualificationToFinal;
									}
									else {
										$TmpQualiNaechsteRundeBez = $TxtQualificationToFinalsEqual;
									}
								
								
								}
							}
							
							# Füllwort
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufPlatz']) == false && empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufZeit']) == false) {
								$TmpFuellwort = " " . $TxtQualificationWordsBetweenPlaceAndTime  . " ";
							}
							else {
								$TmpFuellwort = "";
							}
							
							# Ob Zwischenlauf oder Finale
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlZwischenlaeufe']) == false && empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals']) == true) {
								$TmpAnzahlNaechsteRunde = $Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlZwischenlaeufe'];
							}
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlZwischenlaeufe']) == true && empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals']) == false) {
								$TmpAnzahlNaechsteRunde = $Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals'];
							}
							
							
						# Text erzeugen
						
						$TmpQualiText = $TxtQualificationFrom . " " . $Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlLaeufe'] . " " . $TmpQualiVorrundeBez . " " . $TxtQualificationAdvancedTo . " " . $Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufPlatz'] . " " . $TmpQualiPlatz . $TmpFuellwort . $Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufZeit'] . " " . $TmpQualiZeit . " " . $TxtQualificationToWord . " " . $TmpAnzahlNaechsteRunde . " " . $TmpQualiNaechsteRundeBez . $TmpWannNaechsteRunde."." ;
						
						fwrite($datei, "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>");
						fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$TmpQualiText."</td>");
						fwrite($datei, "</tr></table>");
						
						# Start list one single file
						
						$StartlistsOneFileContent = $StartlistsOneFileContent . "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>";
						$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr><td class='qualifikationsmodustext'>".$TmpQualiText."</td>";
						$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr></table>";
						
						
						}
						else { # Wenn kein Standardmodus, dann nur Freitexte verwenden
						
							fwrite($datei, "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>");
							fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext1']."</td></tr>");
							fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext2']."</td>");
							fwrite($datei, "</tr></table>");
							
							# Start list one single file
							$StartlistsOneFileContent = $StartlistsOneFileContent . "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>";
							$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext1']."</td></tr>";
							$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext2']."</td>";
							$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr></table>";
						
						}
					
					
					
					
					
					}
				
				
				
				
				break;
				
			
				
				
				case "b": # Zwischenlauf
				
				if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufAnzahlLaeufe']) == false || empty($Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext1']) == false) {
						# Wenn Anzahl Zwischenläufe oder Freitext1 nicht leer ist, dann gibt es einen Qualimodus
						
						if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufAnzahlLaeufe']) == false){ # Wenn Anzahl Läufe nicht leer, dann Standardmodus verwenden
						
							
									
									if($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufAnzahlLaeufe'] == 1) {
										$TmpQualiVorrundeBez = $TxtQualificationSemiFinal;
									}
									else {
										$TmpQualiVorrundeBez = $TxtQualificationSemiFinals;
									}
									
							
							
							# Quali Platz Bezeichnung
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufPlatz']) == false) {
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufPlatz'] == 1) {
									$TmpQualiPlatz = $TxtQualificationByPlaceTrack1;
								}
								else {
									$TmpQualiPlatz = $TxtQualificationByPlaceTrackMore;
								}
							}
							else {
								$TmpQualiPlatz = "";	
							}
							
							# Quali Zeit Bezeichnung
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufZeit']) == false) {
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufZeit'] == 1) {
									$TmpQualiZeit = $TxtQualificationByTimeTrack1;
								}
								else {
									$TmpQualiZeit = $TxtQualificationByTimeTrackMore;
								}
							}
							else {
								$TmpQualiZeit = "";	
							}
							
							# Bezeichnung Quali für was
								
								if($WkListZeile['Startdatum'] == $tage[$Wettbew[$WkListZeile['WettbewNr']]['FinaleTag']]) {
										$TmpWannNaechsteRunde = " (" . $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['FinaleZeit']." " . $TxtDaytimeUnit . ")";
									}
									else {
										$TmpWannNaechsteRunde = " (" . $TxtOnThe . " ".$tage[$Wettbew[$WkListZeile['WettbewNr']]['FinaleTag']]." " . $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['FinaleZeit']." " . $TxtDaytimeUnit . ")";
									}
							
							
							
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiFinalsungleichberechtigt'] == 1) {
							
									
										$TmpQualiNaechsteRundeBez = $TxtQualificationToFinal2;
									
								
							}
							else {
							
								$TmpQualiNaechsteRundeBez = $TxtQualificationToFinal3;
							
							}
							
							# Füllwort
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufPlatz']) == false && empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufZeit']) == false) {
								$TmpFuellwort = " " . $TxtQualificationWordsBetweenPlaceAndTime . " ";
							}
							else {
								$TmpFuellwort = "";
							}
							
							
								$TmpAnzahlNaechsteRunde = "";
							
							
						# Text erzeugen
						
						$TmpQualiText = $TxtQualificationFrom . " " . $Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufAnzahlLaeufe'] . " " . $TmpQualiVorrundeBez . " " . $TxtQualificationAdvancedTo . " " . $Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufPlatz'] . " " . $TmpQualiPlatz . $TmpFuellwort . $Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufZeit'] . " " . $TmpQualiZeit . " " . $TxtQualificationToWord . " " . $TmpAnzahlNaechsteRunde . " " . $TmpQualiNaechsteRundeBez . $TmpWannNaechsteRunde. "." ;
						
						fwrite($datei, "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>");
						fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$TmpQualiText."</td>");
						fwrite($datei, "</tr></table>");
						
						# Start list one single file
						
						$StartlistsOneFileContent = $StartlistsOneFileContent . "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>";
						$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr><td class='qualifikationsmodustext'>".$TmpQualiText."</td>";
						$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr></table>";
						
						}
						else { # Wenn kein Standardmodus, dann nur Freitexte verwenden
						
							fwrite($datei, "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>");
							fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext1']."</td></tr>");
							fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext2']."</td>");
							fwrite($datei, "</tr></table>");
							
							# Start list one single file
							$StartlistsOneFileContent = $StartlistsOneFileContent . "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>";
							$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext1']."</td></tr>";
							$StartlistsOneFileContent = $StartlistsOneFileContent . "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext2']."</td>";
							$StartlistsOneFileContent = $StartlistsOneFileContent . "</tr></table>";
						
						}
					
					
					
					
					
					}
				
				
				
				
				
				break;
			
			
			}
			
		
		
		break;
		
		case "h": # Hoch
		
		$AnzahlSprunghoehen = count($WkListZeile['Sprunghoehen']);
		$SprunghoehenZaehler = 0;
		if(count($WkListZeile['Sprunghoehen']) > 0){
		
			fwrite($datei, "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtHJHeightsHeadline . ":</td></tr><td class='qualifikationsmodustext'>");
			
			# Start list one single file
			$StartlistsOneFileContent = $StartlistsOneFileContent . "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtHJHeightsHeadline . ":</td></tr><td class='qualifikationsmodustext'>";
			
				$SprunghoehenZaehler++;
				foreach($WkListZeile['Sprunghoehen'] as $SprunghoehenZeile) {
					fwrite($datei, substr($SprunghoehenZeile, 0, 1).$MarkSeperator1.substr($SprunghoehenZeile, 1, 2));
					
					# Start list one single file
					$StartlistsOneFileContent = $StartlistsOneFileContent . substr($SprunghoehenZeile, 0, 1).$MarkSeperator1.substr($SprunghoehenZeile, 1, 2);
					
					if($SprunghoehenZaehler++ < $AnzahlSprunghoehen) {
							fwrite($datei, " - ");
							
							#Start List one single file
							$StartlistsOneFileContent = $StartlistsOneFileContent . " - ";
							
							}
				}
			
			fwrite($datei, "</td></tr></table>");
			
			# Start list one single file
			$StartlistsOneFileContent = $StartlistsOneFileContent . "</td></tr></table>";
		
		}
		
		
						
						
		
		break;
		case "t": # technisch
		
		break;
	
	
	}
fwrite($datei, "<br>");

# Start list one single file
#$StartlistsOneFileContent = $StartlistsOneFileContent . "<br>";


fclose($datei);
$LaufGruppeVorher= " ";
$TmpLaufGruppeUe = "";
$TmpAKUeberschrift = "";
unset($AKGemischtArray);

#Weitere Gemischte Startlisten ausgeben
if(count($StartlisteGemischtWeitereAKs)) {
	foreach($StartlisteGemischtWeitereAKs as $GemischtStartlisteZeile) {

		#echo $GemischtStartlisteZeile. "<br>";
		copy($dateiname, $GemischtStartlisteZeile);
		$DateienStartlisten[] = $GemischtStartlisteZeile;

	}

}
unset($StartlisteGemischtWeitereAKs);

break;
}
} # DSB/IPC Mode

else { # DSB/IPC Mode

switch($WkListZeile['WettbewTyp']) {

case "m": # Mehrkampf ##########################################################################################

$LaufGruppeVorher= "";
$StartlisteGemischtWeitereAKs = array();
$TmpWannNaechsteRunde = "";

$dateiname = "_tmp"."s"."-".$WkListZeile['WettbewTyp']."d-".$WkListZeile['WettbewNr']."-".$WkListZeile['COSANr']."-".$WkListZeile['Riege']."-".$WkListZeile['RundeTyp'].".htm";
$DateienStartlisten[] = $dateiname;

# gemischt
if($WkListZeile['Gemischt'] == "v") {
	$TmpGemischt = $TxtMixedEvent;
}
else {
	$TmpGemischt = "";
}

# Riege Ermitteln
if($WkListZeile['Riege'] > 0) {
	$TmpRiegeU = "- " . $TxtCombinedEventGroup . " ".$WkListZeile['Riege']." ";
}
else {
	$TmpRiegeU = "";
}


$datei = fopen($dateiname, w);


fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ1'>$Kopfzeile1</td></tr><tr><td class='KopfZ11'>$Kopfzeile2</td></tr><tr><td class='KopfZ12'>$Kopfzeile3</td></tr></table>");
fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ21'>" . $TxtStartlistHeadline . ": ".$Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Bez']." ".$TmpRiegeU."(".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez'].") ".$TmpGemischt."</td></tr></table>");

$Zeilenwechsler = 0;
$Start = 1;

$LaufGruppeVorher= "";
$TmpLaufGruppeUe = "";
foreach($WkList2SL as $WkList2Zeile) {



	

	if($WkList2Zeile['WettbewNr'] == $WkListZeile['WettbewNr'] && $WkList2Zeile['Riege'] == $WkListZeile['Riege'] && $WkList2Zeile['RundeTyp'] == $WkListZeile['RundeTyp'] && $WkList2Zeile['COSANr'] == $WkListZeile['COSANr'] ) {

	#Ermittlung von Geschlecht und Alter für Akl MK
	
	#Geschlecht
			switch($WkList2Zeile['Geschlecht']) {
				case 0: # männlich
					$tmpgeschlecht = $TxtAbrrevGenderMale;
					$tmpgeschlechtAK = $TxtAbrrevGenderMan;
				break;
				case 1: # weiblich
					$tmpgeschlecht = $TxtAbrrevGenderFemale;
					$tmpgeschlechtAK = $TxtAbrrevGenderWoman;
				break;
			}
		
		# AK
						
							if(is_numeric($WkList2Zeile['AKMK'])) {
								$TmpAKMK = $tmpgeschlechtAK.$WkList2Zeile['AKMK'];
							}
							else {
								$TmpAKMK = "";
							}
						
						
	
	

	
	# Ermittlung der Leistung bei der Ausgabe Vorleistung
	
	switch($WkListZeile['RundeTyp']) {
	
		case "a":
		case "c":
		case "e":
		case "r":
		case "s":
		case "n":
		case "q":
		
		
			$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
			$TmpVorleistungBez = $TxtSeasonBest;
		
		break;
		
		case "b": #Zwischenlauf
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
			else {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
				
			
			}
		
		
		break;
		
		case "d":
		case "k":
		case "l":
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == true && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
			}
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
			
			if(empty($WkList2Zeile['LeistungZwischenlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungZwischenlauf']). " <b>".$WkList2Zeile['QualiZwischenlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
		
			
		
		break;
	
	
	}
	
	
	if($WkList2Zeile['aW'] == 1) { $TmpaW = $TxtAbbrevOutOfRanking;} else {$TmpaW = " ";}
	
	# Bezeichnung Lauf/Gruppe
	switch($Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Typ']) {
	
		case "s":
		case "l":
		case "w":
		
			$TmpLaufGruppeBez = $TxtHeat;
		
		break;
		
		case "t":
		case "h":
		
			$TmpLaufGruppeBez = $TxtGroup;
		
		break;
	
	}
	
	if(is_numeric($WkList2Zeile['LaufGruppe'])) {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'] * 1;
		}
		else {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'];
		}
	
	if($TmpLaufGruppeUe == 0 && $Start == 1) {
	fwrite($datei, "<br>");
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettbMK'>".$Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Bez']." ".$TmpRiegeU."(".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez'].") "."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>&nbsp;</td></tr></table>");
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>");
		fwrite($datei, "<td CLASS='blEJGu'>" . $TxtAbbrevJOB  . "</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . "</td>");
		fwrite($datei, "<td CLASS='blELeistu'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		$Start = 0;
		$Zeilenwechsler = 0;
	
	}
	
		if(is_numeric($WkList2Zeile['LaufGruppe'])) {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'] * 1;
		}
		else {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'];
		}
	
	if($TmpLaufGruppeUe != $LaufGruppeVorher) {
	
	
		$Zeilenwechsler = 0;
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
		
		
	
		fwrite($datei, "<br>");
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettbMK'>".$Disziplinen[substr($WkListZeile['COSANr'], 2, 3)*1]['Bez']." ".$TmpRiegeU."(".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez'].") "."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe."</td></tr></table>");
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>");
		fwrite($datei, "<td CLASS='blEJGu'>" . $TxtAbbrevJOB . "</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . "</td>");
		fwrite($datei, "<td CLASS='blELeistu'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNr$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAKMK."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
	
		$Zeilenwechsler = 1;
	
	}
	else {
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
	
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNr$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAKMK ."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
	
	
	
	}

	$LaufGruppeVorher = $TmpLaufGruppeUe;
	}
	
	
	
	
	
	
	
	
} ###






			
			
fwrite($datei, "<br>");			
fclose($datei);
$LaufGruppeVorher = " ";
$TmpLaufGruppeUe = "";
			
break;


default: # Alle Wettbewerbe außer MK#####################################################################################


$LaufGruppeVorher= "";
$StartlisteWeitereAKs = array();
$TmpWannNaechsteRunde = "";
$TmpLaufGruppeUe = "";

$dateiname = "_tmp"."s"."-".$WkListZeile['WettbewTyp']."-".$WkListZeile['WettbewNr']."-".$WkListZeile['COSANr']."-".$WkListZeile['Riege']."-".$WkListZeile['RundeTyp'].".htm";
$DateienStartlisten[] = $dateiname;

# gemischt
if($WkListZeile['Gemischt'] == "v") {
	$TmpGemischt = $TxtMixedEvent;
}
else {
	$TmpGemischt = "";
}

# Rundentypen
			switch($WkListZeile['RundeTyp']) {
			
				case "a": #Vorläufe
					$HTmpRundeBez = $RundeTyp1;
				break;
				case "b": #Zwischenläufe
					$HTmpRundeBez = $RundeTyp2;
				break;
				case "c": #Ausscheidung
					$HTmpRundeBez = $RundeTyp4;
				break;
				case "d": #Zeitfinalläufe
					$HTmpRundeBez = $RundeTyp6;
				break;
				case "e": #Zeit-Vorläufe
					$HTmpRundeBez = $RundeTyp3;
				break;
				case "k": #Finale
					$HTmpRundeBez = $RundeTyp0;
				break;
				case "l": #ABFinale
					$HTmpRundeBez = $RundeTyp7;
				break;
				case "m": #nur Lauf-Nr.
					$HTmpRundeBez = $RundeTyp8;
				break;
				case "n": #Finale Techn./Hoch
					$HTmpRundeBez = $RundeTyp0;
				break;
				case "q": #Finale Techn./Hoch
					$HTmpRundeBez = $RundeTyp0;
				break;
				case "r": #Ausscheidung Techn./Hoch
					$HTmpRundeBez = $RundeTyp4;
				break;
				case "s": #Qualifikation Tech./Hoch
					$HTmpRundeBez = $RundeTyp5;
				break;
			
			}
			
			
	
			

$datei = fopen($dateiname, w);


if($WkListZeile['Gemischt'] != "v") {
	fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ1'>$Kopfzeile1</td></tr><tr><td class='KopfZ11'>$Kopfzeile2</td></tr><tr><td class='KopfZ12'>$Kopfzeile3</td></tr></table>");
	fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ21'>" . $TxtStartlistHeadline . ": ".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez." ".$DBSTextskl_startlist[$WkListZeile['Riege']]['IPCClassName']." ".$TmpGemischt."</td></tr></table>");
}
else { # Gemischter Wettbewerb


foreach($AKGemischt[$WkListZeile['WettbewNr']."-".$WkListZeile['COSANr']."-".$WkListZeile['Riege']] as $GemischtAKZeile) {

	
	$TmpAKGemischtArray = (array_multi_search($GemischtAKZeile, $Klassen));
	$AKGemischtArray[] = $TmpAKGemischtArray[0]['Bez'];
	sort($AKGemischtArray);
	unset($TmpAKGemischtArray);
	
}

	$AKGemischtArray2 = array_unique($AKGemischtArray);
	
	$ZaehlerAKGemischt = 0;
	foreach($AKGemischtArray2 as $AKGemischtArray2Zeile) {
		$ZaehlerAKGemischt++;
		
		if(count($AKGemischtArray2) > $ZaehlerAKGemischt) {
			$TmpGemischtTrenner = ", ";
		}
		else {
			$TmpGemischtTrenner = "";
		}
		
		$TmpAKUeberschrift = $TmpAKUeberschrift . $AKGemischtArray2Zeile . $TmpGemischtTrenner;
	}
	
	fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ1'>$Kopfzeile1</td></tr><tr><td class='KopfZ11'>$Kopfzeile2</td></tr><tr><td class='KopfZ12'>$Kopfzeile3</td></tr></table>");
	fwrite($datei, "<table class='body' cellspacing='0'><tr><td class='KopfZ21'>Startliste: ".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez." ".$DBSTextskl_startlist[$WkListZeile['Riege']]['IPCClassName']." ".$TmpGemischt."</td></tr></table>");
}
$ZaehlerAKGemischt = 0;

	





		
		
$Zeilenwechsler = 0;
$Start = 1;

$LaufGruppeVorher= "";
$TmpLaufGruppeUe = "";
foreach($WkList2SL as $WkList2Zeile) {

if(is_numeric($WkList2Zeile['LaufGruppe'])) {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'] * 1;
		}
		else {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'];
		}



	switch($Wettbew[$WkListZeile['WettbewNr']]['WettbewTyp']) {
	
	case "l":
	case "t":
	case "h":
	case "w":
			

	if($WkList2Zeile['WettbewNr'] == $WkListZeile['WettbewNr'] && $WkList2Zeile['Riege'] == $WkListZeile['Riege'] && $WkList2Zeile['RundeTyp'] == $WkListZeile['RundeTyp']) {

	# Gemischte Wettbewerbe - Bezeichnungen der Aks und doppelte Ausgabe der Startliste

if($WkList2Zeile['AK'] != "" && $WkListZeile['Gemischt'] == "v") {

	$TmpAKArray = (array_multi_search($WkList2Zeile['AK'], $Klassen));
	$TmpAK = "".$TmpAKArray[0]['Bez']."";
	
	
	if($TmpAKArray[0]['Nr'] != substr($WkListZeile['COSANr'], 0, 2)) {
	$StartlisteGemischtWeitereAKs["s"."-".$WkListZeile['WettbewTyp']."-".$WkListZeile['WettbewNr']."-".$WkListZeile['COSANr']."-".$WkListZeile['Riege']."-".$WkListZeile['RundeTyp'].".htm"] = "_tmp"."s"."-".$WkListZeile['WettbewTyp']."-".$WkListZeile['WettbewNr']."-".$WkListZeile['COSANr']."-".$WkListZeile['Riege']."-".$WkListZeile['RundeTyp'].".htm";
	}
	unset($TmpAKArray);
	
	
	
}
else {
	$TmpAK = "";
}
	
	
	# Ermittlung der Leistung bei der Ausgabe Vorleistung
	
	switch($WkListZeile['RundeTyp']) {
	
		case "a":
		case "c":
		case "e":
		case "r":
		case "s":
		case "n":
		case "q":
		case "m":
		
			$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
			$TmpVorleistungBez = $TxtSeasonBest;
		
		break;
		
		case "b": #Zwischenlauf
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
			else {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
				
			
			}
		
		
		break;
		
		case "d":
		case "k":
		case "l":
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == true && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
			}
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
			
			if(empty($WkList2Zeile['LeistungZwischenlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungZwischenlauf']). " <b>".$WkList2Zeile['QualiZwischenlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
		
			
		
		break;
	
	
	}
	
	
	if($WkList2Zeile['aW'] == 1) { $TmpaW = $TxtAbbrevOutOfRanking;} else {$TmpaW = " ";}
	
	# Bezeichnung Lauf/Gruppe
	switch($Wettbew[$WkListZeile['WettbewNr']]['WettbewTyp']) {
	
		case "s":
		case "l":
		case "w":
		
			$TmpLaufGruppeBez = $TxtHeat;
		
		break;
		
		case "t":
		case "h":
		
			$TmpLaufGruppeBez = $TxtGroup;
		
		break;
	
	}
	
	if($TmpLaufGruppeUe === 0 && $Start == 1) {
	fwrite($datei, "<br>");
		if($WkListZeile['Gemischt'] != "v") {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez." ".$DBSTextskl_startlist[$WkListZeile['Riege']]['IPCClassName']."</td></tr></table>");
		}
		else {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez." ".$DBSTextskl_startlist[$WkListZeile['Riege']]['IPCClassName']."</td></tr></table>");
		}
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>");
		fwrite($datei, "<td CLASS='blEJGu'>" . $TxtAbbrevJOB . "</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . "</td>");
		fwrite($datei, "<td CLASS='blELeistu'>$TmpVorleistungBez</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		$Start = 0;
		$Zeilenwechsler = 0;
	
	}
	if(is_numeric($WkList2Zeile['LaufGruppe'])) {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'] * 1;
		}
		else {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'];
		}
	
	if($TmpLaufGruppeUe != $LaufGruppeVorher) {
	
	
		$Zeilenwechsler = 0;
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
		
		
		
	
		fwrite($datei, "<br>");
		if($WkListZeile['Gemischt'] != "v") {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe." ". $DBSTextskl_startlist[$WkListZeile['Riege']]['IPCClassName']."</td></tr></table>");
		}
		else {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe." ".$DBSTextskl_startlist[$WkListZeile['Riege']]['IPCClassName']."</td></tr></table>");
		}
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>" . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtAthleteName . "</td>");
		fwrite($datei, "<td CLASS='blEJGu'>" . $TxtAbbrevJOB . "</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . "</td>");
		fwrite($datei, "<td CLASS='blELeistu'>$TmpVorleistungBez</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNr$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAK .$WkList2Zeile['IPCClassName']."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
	
		$Zeilenwechsler = 1;
	
	}
	else {
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
	
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERang$farbe'>".$WkList2Zeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNr$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameAS$farbe'>".$WkList2Zeile['Nachname'].", ".$WkList2Zeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>".$WkList2Zeile['JG'] ."</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAK .$WkList2Zeile['IPCClassName']."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
	
	
	
	}

	$LaufGruppeVorher = $TmpLaufGruppeUe;
	}
	
	break;
	
	case "s": # Staffel
	
	
	
	if($WkList2Zeile['WettbewNr'] == $WkListZeile['WettbewNr'] && $WkList2Zeile['Riege'] == $WkListZeile['Riege'] && $WkList2Zeile['RundeTyp'] == $WkListZeile['RundeTyp']) {
	
	# Gemischte Wettbewerbe - Bezeichnungen der Aks und doppelte Ausgabe der Startliste

if($WkList2Zeile['AK'] != "" && $WkListZeile['Gemischt'] == "v") {

	$TmpAKArray = (array_multi_search($WkList2Zeile['AK'], $Klassen));
	$TmpAK = "".$TmpAKArray[0]['Bez']."";
	
	if($TmpAKArray[0]['Nr'] != substr($WkListZeile['COSANr'], 0, 2)) {
	$StartlisteGemischtWeitereAKs["s"."-".$WkListZeile['WettbewTyp']."-".$WkListZeile['WettbewNr']."-".$WkListZeile['COSANr']."-".$WkListZeile['Riege']."-".$WkListZeile['RundeTyp'].".htm"] = "_tmp"."s"."-".$WkListZeile['WettbewTyp']."-".$WkListZeile['WettbewNr']."-".$WkListZeile['COSANr']."-".$WkListZeile['Riege']."-".$WkListZeile['RundeTyp'].".htm";
	}
	unset($TmpAKArray);
	
	
	
}
else {
	$TmpAK = "";
}

	# Ermittlung der Leistung bei der Ausgabe Vorleistung
	
	switch($WkListZeile['RundeTyp']) {
	
		case "a":
		case "c":
		case "e":
		case "r":
		case "s":
		case "n":
		case "q":
		case "m":
		
			$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
			$TmpVorleistungBez = $TxtSeasonBest;
		
		break;
		
		case "b": #Zwischenlauf
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
			
			}
			else {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
			
			}
		
		
		break;
		
		case "d":
		case "k":
		case "l":
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == true && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['Meldeleistung']);
				$TmpVorleistungBez = $TxtSeasonBest;
				$TmpZeileStaffel = "";
			}
		
			if(empty($WkList2Zeile['LeistungVorlauf']) == false && empty($WkList2Zeile['LeistungZwischenlauf']) == true) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungVorlauf']). " <b>".$WkList2Zeile['QualiVorlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
				$TmpZeileStaffel = "<br>";
			
			}
			
			if(empty($WkList2Zeile['LeistungZwischenlauf']) == false) {
			
				$TmpVorleistung = str_replace(",", $MarkSeperator1, $WkList2Zeile['LeistungZwischenlauf']). " <b>".$WkList2Zeile['QualiZwischenlauf'] . "</b>";
				$TmpVorleistungBez = $TxtQualificationMark;
				$TmpZeileStaffel = "<br>";
			}
		
			
		
		break;
	
	
	}
	
	
	
	if($WkList2Zeile['aW'] == 1) { $TmpaW = "a.W.";} else {$TmpaW = " ";}
	
	# Bezeichnung Lauf/Gruppe
	switch($Wettbew[$WkListZeile['WettbewNr']]['WettbewTyp']) {
	
		case "s":
		case "l":
		case "w":
		
			$TmpLaufGruppeBez = $TxtHeat;
		
		break;
		
		case "t":
		case "h":
		
			$TmpLaufGruppeBez = $TxtGroup;
		
		break;
	
	}
	
	if($TmpLaufGruppeUe === 0 && $Start == 1) {
	fwrite($datei, "<br>");
		if($WkListZeile['Gemischt'] != "v") {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez." ".$DBSTextskl_startlist[$WkListZeile['Riege']]['IPCClassName']."</td></tr></table>");
		}
		else {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$HTmpRundeBez." ".$DBSTextskl_startlist[$WkListZeile['Riege']]['IPCClassName']."</td></tr></table>");
		}
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>".$TxtAbbrevOrdner."<br>".$TmpZeileStaffel . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIBRelay . "<br>".$TmpZeileStaffel . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtRelayTeam . "<br>".$TmpZeileStaffel . $TxtRelayMembers ."</td>");
		fwrite($datei, "<td CLASS='blEJGu'>&nbsp;<br>".$TmpZeileStaffel.$TxtAbbrevJOB."</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "<br>".$TmpZeileStaffel."&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . $TmpZeileStaffel."<br>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blELeistu'>".$TmpVorleistungBez."<br>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		$Start = 0;
		$Zeilenwechsler = 0;
	
	}
	
			if(is_numeric($WkList2Zeile['LaufGruppe'])) {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'] * 1;
		}
		else {
			$TmpLaufGruppeUe = $WkList2Zeile['LaufGruppe'];
		}
	
	
	if($TmpLaufGruppeUe != $LaufGruppeVorher) {
	
	
		$Zeilenwechsler = 0;
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
		
	
		
		
	
		fwrite($datei, "<br>");
		if($WkListZeile['Gemischt'] != "v") {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['WettbewBez']." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe." ".$DBSTextskl_startlist[$WkListZeile['Riege']]['IPCClassName']."</td></tr></table>");
		}
		else {
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>".$Wettbew[$WkListZeile['WettbewNr']]['DISBez']." ".$TmpAKUeberschrift." - ". $HTmpRundeBez."</td><td CLASS='blEWind'></td><td CLASS='blEDatum'>".$WkListZeile['Startdatum']." / ".$WkListZeile['Startzeit']      ."</td></tr></table>");
			fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEFreiDis'></td><td CLASS='blEDis'>".$TmpLaufGruppeBez." ".$TmpLaufGruppeUe." ".$DBSTextskl_startlist[$WkListZeile['Riege']]['IPCClassName']."</td></tr></table>");
		}
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangu'>".$TxtAbbrevOrdner."<br>".$TmpZeileStaffel . $TxtAbbrevOrdner . "</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNru'>" . $TxtAbbrevBIBRelay . "<br>".$TmpZeileStaffel . $TxtAbbrevBIB . "</td>");} else {fwrite($datei, "<td CLASS='blEStNru'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASu'>" . $TxtRelayTeam . "<br>".$TmpZeileStaffel . $TxtRelayMembers ."</td>");
		fwrite($datei, "<td CLASS='blEJGu'>&nbsp;<br>".$TmpZeileStaffel.$TxtAbbrevJOB."</td>");
		fwrite($datei, "<td CLASS='blELvu'>" . $TxtAbbrevNation . "<br>".$TmpZeileStaffel."&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEVereinu'>" . $TxtClub . $TmpZeileStaffel."<br>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blELeistu'>".$TmpVorleistungBez."<br>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokPu'></td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		
		
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangStaffel$farbe'><a CLASS='blERangStaffel$farbe'>".$WkList2Zeile['Pos'] ."</a></td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNrStaffel$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNrStaffel$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASStaffel$farbe'>".$WkList2Zeile['Verein']." ".$Roemisch[$WkList2Zeile['JG']]."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>&nbsp;</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAK .$WkList2Zeile['IPCClassName']."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		foreach($WkList2Zeile['Mannschaftsteilnehmer'] as $MannschaftsTNZeile) {
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangMannschaftsTN$farbe'>".$MannschaftsTNZeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNrMannschaftsTN$farbe'>".$MannschaftsTNZeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNrMannschaftsTN$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASMannschaftsTN$farbe'>".$MannschaftsTNZeile['Nachname'].", ".$MannschaftsTNZeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJGMannschaftsTN$farbe'>".$MannschaftsTNZeile['JG']."</td>");
		fwrite($datei, "<td CLASS='blELv$farbe'>&nbsp;".$WkList2Zeile['IPCClassName']."</td>");
		fwrite($datei, "<td CLASS='blEVerein$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>&nbsp;</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		}
		
	
		$Zeilenwechsler = 1;
	
	}
	else {
	
		switch($Zeilenwechsler) {
		
			case 0:
				$farbe = "g";
				$Zeilenwechsler = 1;
			break;
			
			case 1:
				$farbe = "w";
				$Zeilenwechsler = 0;
			break;
		
		}
	
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangStaffel$farbe'><a CLASS='blERangStaffel$farbe'>".$WkList2Zeile['Pos'] ."</a></td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNrStaffel$farbe'>".$TmpaW." ".$WkList2Zeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNrStaffel$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASStaffel$farbe'>".$WkList2Zeile['Verein']." ".$Roemisch[$WkList2Zeile['JG']]."</td>");
		fwrite($datei, "<td CLASS='blEJG$farbe'>&nbsp;</td>");
		
		if($FlagsOn == 1) {fwrite($datei, "<td CLASS='blELv$farbe'><img src='" .  $PathToFlags . $WkList2Zeile['LV'] . $FileFormatFlags . "' alt='" . $WkList2Zeile['LV'] . "' class='imgflags'></td>");}
		else {fwrite($datei, "<td CLASS='blELv$farbe'>" . $WkList2Zeile['LV'] . "</td>");}
		
		fwrite($datei, "<td CLASS='blEVerein$farbe'>".$WkList2Zeile['Verein'] ."</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>".$TmpVorleistung ."</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>".$TmpAK .$WkList2Zeile['IPCClassName']."</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		foreach($WkList2Zeile['Mannschaftsteilnehmer'] as $MannschaftsTNZeile) {
		fwrite($datei, "<table CLASS='body' cellspacing='0' cellpadding='0'>");
		fwrite($datei, "<tr>");
		fwrite($datei, "<td CLASS='blERangMannschaftsTN$farbe'>".$MannschaftsTNZeile['Pos'] ."</td>");
		if($StartnummernAn == 1) {fwrite($datei, "<td CLASS='blEStNrMannschaftsTN$farbe'>".$MannschaftsTNZeile['StNr'] ."</td>");} else {fwrite($datei, "<td CLASS='blEStNrMannschaftsTN$farbe'>&nbsp;</td>");}
		fwrite($datei, "<td CLASS='blENameASMannschaftsTN$farbe'>".$MannschaftsTNZeile['Nachname'].", ".$MannschaftsTNZeile['Vorname']."</td>");
		fwrite($datei, "<td CLASS='blEJGMannschaftsTN$farbe'>".$MannschaftsTNZeile['JG']."</td>");
		fwrite($datei, "<td CLASS='blELv$farbe'>&nbsp;".$WkList2Zeile['IPCClassName']."</td>");
		fwrite($datei, "<td CLASS='blEVerein$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blELeist$farbe'>&nbsp;</td>");
		fwrite($datei, "<td CLASS='blEPokP$farbe'>&nbsp;</td>");
		fwrite($datei, "</tr>");
		fwrite($datei, "</table>");
		
		}
		
		
	
	
	}

	$LaufGruppeVorher = $TmpLaufGruppeUe;
	}
	
	
	
	
	
	break;
	}
	
	
	
	
	
	
}






fwrite($datei, "<br>");
# Qualifikationsmodus	und Sprunghöhen
	
	
	switch ($WkListZeile['WettbewTyp']) { # Auswahl nach Wettbewerbstyp
	
		case "l": # Lauf Bahn
		case "s": # Staffel Bahn
		
			switch($WkListZeile['RundeTyp']) {
			
				case "a": #Vorlauf
				case "e": #Zeitvorlauf
				
					
				
					if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlLaeufe']) == false || empty($Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext1']) == false) {
						# Wenn Anzahl Vorläufe oder Freitext1 nicht leer ist, dann gibt es einen Qualimodus
						
						if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlLaeufe']) == false){ # Wenn Anzahl Läufe nicht leer, dann Standardmodus verwenden
						
							# Ermitteln, welche Läufe
							switch($WkListZeile['RundeTyp']) {
			
								case "a": #Vorläufe
									
									if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlLaeufe'] == 1) {
										$TmpQualiVorrundeBez = $TxtQualificationHeat;
									}
									else {
										$TmpQualiVorrundeBez = $TxtQualificationHeats;
									}
									
								break;
								case "e": #Zeit-Vorläufe
								
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlLaeufe'] == 1) {
										$TmpQualiVorrundeBez = $TxtQualificationTimedHeat;
									}
									else {
										$TmpQualiVorrundeBez = $TxtQualificationTimedHeats;
									}
								
									
								break;
			
							}
							
							# Quali Platz Bezeichnung
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufPlatz']) == false) {
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufPlatz'] == 1) {
									$TmpQualiPlatz = $TxtQualificationByPlaceTrack1;
								}
								else {
									$TmpQualiPlatz = $TxtQualificationByPlaceTrackMore;
								}
							}
							else {
								$TmpQualiPlatz = "";	
							}
							
							# Quali Zeit Bezeichnung
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufZeit']) == false) {
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufZeit'] == 1) {
									$TmpQualiZeit = $TxtQualificationByTimeTrack1;
								}
								else {
									$TmpQualiZeit = $TxtQualificationByTimeTrackMore;
								}
							}
							else {
								$TmpQualiZeit = "";	
							}
							
							# Bezeichnung Quali für was
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlZwischenlaeufe']) == false) {
							
									
									if($WkListZeile['Startdatum'] == $tage[$Wettbew[$WkListZeile['WettbewNr']]['ZwischenlaufTag']]) {
										$TmpWannNaechsteRunde = " (". $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['ZwischenlaufZeit']." ". $TxtDaytimeUnit.")";
									}
									else {
										$TmpWannNaechsteRunde = "<br> (". $TxtOnThe. " ".$tage[$Wettbew[$WkListZeile['WettbewNr']]['ZwischenlaufTag']]." " . $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['ZwischenlaufZeit']." ". $TxtAt . ")";
									}
							
							
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlZwischenlaeufe'] == 1) {
									$TmpQualiNaechsteRundeBez = $TxtQualificationToSemiFinal;
								}
								else {
									$TmpQualiNaechsteRundeBez = $TxtQualificationToSemiFinals;
								}
							}
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals']) == false) {
							
									if($WkListZeile['Startdatum'] == $tage[$Wettbew[$WkListZeile['WettbewNr']]['FinaleTag']]) {
										$TmpWannNaechsteRunde = " (" . $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['FinaleZeit']." " . $TxtDaytimeUnit . ")";
									}
									else {
										$TmpWannNaechsteRunde = "<br> (" . $TxtOnThe. " ".$tage[$Wettbew[$WkListZeile['WettbewNr']]['FinaleTag']]." " . $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['FinaleZeit']." " . $TxtDaytimeUnit . ")";
									}
							
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiFinalsungleichberechtigt'] == 1) {
							
									if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals'] == 1) {
										$TmpQualiNaechsteRundeBez = $TxtQualificationToFinal;
									}
									else {
										$TmpQualiNaechsteRundeBez = $TxtQualificationToFinalsNotEqual;
									}
								
								}
								else {
								
									if($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals'] == 1) {
										$TmpQualiNaechsteRundeBez = $TxtQualificationToFinal;
									}
									else {
										$TmpQualiNaechsteRundeBez = $TxtQualificationToFinalsEqual;
									}
								
								
								}
							}
							
							# Füllwort
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufPlatz']) == false && empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufZeit']) == false) {
								$TmpFuellwort = " " . $TxtQualificationWordsBetweenPlaceAndTime  . " ";
							}
							else {
								$TmpFuellwort = "";
							}
							
							# Ob Zwischenlauf oder Finale
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlZwischenlaeufe']) == false && empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals']) == true) {
								$TmpAnzahlNaechsteRunde = $Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlZwischenlaeufe'];
							}
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlZwischenlaeufe']) == true && empty($Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals']) == false) {
								$TmpAnzahlNaechsteRunde = $Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlFinals'];
							}
							
							
						# Text erzeugen
						
						$TmpQualiText = $TxtQualificationFrom . " " . $Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufAnzahlLaeufe'] . " " . $TmpQualiVorrundeBez . " " . $TxtQualificationAdvancedTo . " " . $Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufPlatz'] . " " . $TmpQualiPlatz . $TmpFuellwort . $Wettbew[$WkListZeile['WettbewNr']]['QualiVorlaufZeit'] . " " . $TmpQualiZeit . " " . $TxtQualificationToWord . " " . $TmpAnzahlNaechsteRunde . " " . $TmpQualiNaechsteRundeBez . $TmpWannNaechsteRunde."." ;
						
						fwrite($datei, "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>");
						fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$TmpQualiText."</td>");
						fwrite($datei, "</tr></table>");
						
						}
						else { # Wenn kein Standardmodus, dann nur Freitexte verwenden
						
							fwrite($datei, "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>");
							fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext1']."</td></tr>");
							fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext2']."</td>");
							fwrite($datei, "</tr></table>");
						
						}
					
					
					
					
					
					}
				
				
				
				
				break;
				
			
				
				
				case "b": # Zwischenlauf
				
				if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufAnzahlLaeufe']) == false || empty($Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext1']) == false) {
						# Wenn Anzahl Zwischenläufe oder Freitext1 nicht leer ist, dann gibt es einen Qualimodus
						
						if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufAnzahlLaeufe']) == false){ # Wenn Anzahl Läufe nicht leer, dann Standardmodus verwenden
						
							
									
									if($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufAnzahlLaeufe'] == 1) {
										$TmpQualiVorrundeBez = $TxtQualificationSemiFinal;
									}
									else {
										$TmpQualiVorrundeBez = $TxtQualificationSemiFinals;
									}
									
							
							
							# Quali Platz Bezeichnung
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufPlatz']) == false) {
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufPlatz'] == 1) {
									$TmpQualiPlatz = $TxtQualificationByPlaceTrack1;
								}
								else {
									$TmpQualiPlatz = $TxtQualificationByPlaceTrackMore;
								}
							}
							else {
								$TmpQualiPlatz = "";	
							}
							
							# Quali Zeit Bezeichnung
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufZeit']) == false) {
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufZeit'] == 1) {
									$TmpQualiZeit = $TxtQualificationByTimeTrack1;
								}
								else {
									$TmpQualiZeit = $TxtQualificationByTimeTrackMore;
								}
							}
							else {
								$TmpQualiZeit = "";	
							}
							
							# Bezeichnung Quali für was
								
								if($WkListZeile['Startdatum'] == $tage[$Wettbew[$WkListZeile['WettbewNr']]['FinaleTag']]) {
										$TmpWannNaechsteRunde = " (" . $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['FinaleZeit']." " . $TxtDaytimeUnit . ")";
									}
									else {
										$TmpWannNaechsteRunde = " (" . $TxtOnThe . " ".$tage[$Wettbew[$WkListZeile['WettbewNr']]['FinaleTag']]." " . $TxtAt . " ".$Wettbew[$WkListZeile['WettbewNr']]['FinaleZeit']." " . $TxtDaytimeUnit . ")";
									}
							
							
							
								if($Wettbew[$WkListZeile['WettbewNr']]['QualiFinalsungleichberechtigt'] == 1) {
							
									
										$TmpQualiNaechsteRundeBez = $TxtQualificationToFinal2;
									
								
							}
							else {
							
								$TmpQualiNaechsteRundeBez = $TxtQualificationToFinal3;
							
							}
							
							# Füllwort
							
							if(empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufPlatz']) == false && empty($Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufZeit']) == false) {
								$TmpFuellwort = " " . $TxtQualificationWordsBetweenPlaceAndTime . " ";
							}
							else {
								$TmpFuellwort = "";
							}
							
							
								$TmpAnzahlNaechsteRunde = "";
							
							
						# Text erzeugen
						
						$TmpQualiText = $TxtQualificationFrom . " " . $Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufAnzahlLaeufe'] . " " . $TmpQualiVorrundeBez . " " . $TxtQualificationAdvancedTo . " " . $Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufPlatz'] . " " . $TmpQualiPlatz . $TmpFuellwort . $Wettbew[$WkListZeile['WettbewNr']]['QualiZwischenlaufZeit'] . " " . $TmpQualiZeit . " " . $TxtQualificationToWord . " " . $TmpAnzahlNaechsteRunde . " " . $TmpQualiNaechsteRundeBez . $TmpWannNaechsteRunde. "." ;
						
						fwrite($datei, "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>");
						fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$TmpQualiText."</td>");
						fwrite($datei, "</tr></table>");
						
						}
						else { # Wenn kein Standardmodus, dann nur Freitexte verwenden
						
							fwrite($datei, "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtQualificationHeadline . ":</td></tr>");
							fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext1']."</td></tr>");
							fwrite($datei, "<tr><td class='qualifikationsmodustext'>".$Wettbew[$WkListZeile['WettbewNr']]['QualiFreitext2']."</td>");
							fwrite($datei, "</tr></table>");
						
						}
					
					
					
					
					
					}
				
				
				
				
				
				break;
			
			
			}
			
		
		
		break;
		
		case "h": # Hoch
		
		$AnzahlSprunghoehen = count($WkListZeile['Sprunghoehen']);
		$SprunghoehenZaehler = 0;
		if(count($WkListZeile['Sprunghoehen']) > 0){
		
			fwrite($datei, "<table class='qualifikationsmodus' cellspacing='0'><tr><td class='qualifikationsmodusueberschrift'>" . $TxtHJHeightsHeadline . ":</td></tr><td class='qualifikationsmodustext'>");
				$SprunghoehenZaehler++;
				foreach($WkListZeile['Sprunghoehen'] as $SprunghoehenZeile) {
					fwrite($datei, substr($SprunghoehenZeile, 0, 1).$MarkSeperator1.substr($SprunghoehenZeile, 1, 2));
					if($SprunghoehenZaehler++ < $AnzahlSprunghoehen) {fwrite($datei, " - ");}
				}
			
			fwrite($datei, "</td></tr></table>");
		
		}
		
		
						
						
		
		break;
		case "t": # technisch
		
		break;
	
	
	}
fwrite($datei, "<br>");
fclose($datei);
$LaufGruppeVorher= " ";
$TmpLaufGruppeUe = "";
$TmpAKUeberschrift = "";
unset($AKGemischtArray);

#Weitere Gemischte Startlisten ausgeben
if(count($StartlisteGemischtWeitereAKs) > 0) {
foreach($StartlisteGemischtWeitereAKs as $GemischtStartlisteZeile) {

#echo $GemischtStartlisteZeile. "<br>";
copy($dateiname, $GemischtStartlisteZeile);
$DateienStartlisten[] = $GemischtStartlisteZeile;

}
}
unset($StartlisteGemischtWeitereAKs);


break;
}



} # DSB/IPC Mode

# Start list one single file
$StartlistsOneFileContent = $StartlistsOneFileContent . "</div>";

}





# _tmp-Datein in richtige Startlistdatein umwandeln
foreach($DateienStartlisten as $DateiStartliste) {

$TmpNameRichtigeStartliste = substr($DateiStartliste, 4);

if(file_exists($TmpNameRichtigeStartliste)) { # wenn richtige Startliste bereits existiert
if(file_exists($DateiStartliste)) {
	if(md5_file($DateiStartliste) != md5_file($TmpNameRichtigeStartliste)) { # Falls MD5 übereinstimmt
	
	#echo "Startliste aktualisiert: ". $TmpNameRichtigeStartliste . "<br>";
	copy($DateiStartliste, $TmpNameRichtigeStartliste);
	unlink($DateiStartliste);
	
	} # Falls MD5 übereinstimmt
	
	else {
	
	unlink($DateiStartliste);
	
	}


}
}# wenn richtige Startliste bereits existiert
else { # Existiert noch nicht

	rename($DateiStartliste, $TmpNameRichtigeStartliste);
	if($StartlistenerstellenAutomatischAn != 1) {
	echo "Startliste neu erstellt: ". $TmpNameRichtigeStartliste. "<br>";
	}

} # existiert noch nicht


	$TmpNameRichtigeStartliste = "";
}




if($StartlistenerstellenAutomatischAn != 1) {
echo "<br><a>... beendet</a>";
}



# Start list all Events and Rounds in one single file


# - Add link list



$StartlistsOneFileLinklistContent = "";

$StartlistsOneFileLinklistContent = $StartlistsOneFileLinklistContent . "<table class='bodynoprint' cellspacing='0'><tr><td class='KopfZ2'>" . $TxtSummaryOfClasses . "</td></tr></table>";
			
			$StartlistsOneFileLinklistContent = $StartlistsOneFileLinklistContent . "<table class='bodynoprint' cellspacing='0'>";
			
			$LinklistCOSAIDBefore = 0;
			
			foreach($StartlistsOneFileLinklist as $StartlistsOneFileLinklistLine) {
			
				if(substr($StartlistsOneFileLinklistLine['COSAID'],0,2) * 1 != $LinklistCOSAIDBefore) {
					
					$StartlistsOneFileLinklistContent = $StartlistsOneFileLinklistContent . "<tr>";
					$StartlistsOneFileLinklistContent = $StartlistsOneFileLinklistContent . "<td class='blGrundLinkAK'>";
					$StartlistsOneFileLinklistContent = $StartlistsOneFileLinklistContent . "<a href='#agegroup" . substr($StartlistsOneFileLinklistLine['COSAID'],0,2) * 1 . "'>" . $Klassen[substr($StartlistsOneFileLinklistLine['COSAID'],0,2) * 1]['Bez'] . "</a>:";
					$StartlistsOneFileLinklistContent = $StartlistsOneFileLinklistContent . "</td>";
					$StartlistsOneFileLinklistContent = $StartlistsOneFileLinklistContent . "<td class='blGrundLinkDIS'>";
				
				}
					$StartlistsOneFileLinklistContent = $StartlistsOneFileLinklistContent . "<a class='blGrundLinkDIS' href='#round" . $StartlistsOneFileLinklistLine['EventID'] . "-" . $StartlistsOneFileLinklistLine['COSAID'] . "-" . $StartlistsOneFileLinklistLine['RiegeID'] . "-" . $StartlistsOneFileLinklistLine['RoundTyp'] . "'>".$StartlistsOneFileLinklistLine['Linkname']."</a>&emsp;";
				
				
				
				$LinklistCOSAIDBefore = substr($StartlistsOneFileLinklistLine['COSAID'],0,2) * 1;
			}
			
			$StartlistsOneFileLinklistContent = $StartlistsOneFileLinklistContent . "</table>";
			
			#$StartlistsOneFileLinklistContent = $StartlistsOneFileLinklistContent . "<br class='noprint'>";
			
$bodytag = str_replace("{{TemplateStartlistsLinks}}", $StartlistsOneFileLinklistContent, $StartlistsOneFileContent);			

if(file_exists("_startlists_all.htm")) {
	if(md5_file("_startlists_all.htm") <> md5($bodytag)) {
		$StartlistsOneFileFile = fopen("_startlists_all.htm", "w");
		fwrite($StartlistsOneFileFile, $bodytag);
		fclose($StartlistsOneFileFile);
	}
}
else {
	$StartlistsOneFileFile = fopen("_startlists_all.htm", "w");
	fwrite($StartlistsOneFileFile, $bodytag);
	fclose($StartlistsOneFileFile);
}
unset($StartlistsOneFileContent);
?>
