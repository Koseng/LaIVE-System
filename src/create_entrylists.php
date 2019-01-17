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
*/
### LaIVE - Modul Teilnehmerlisten erstellen (create_entrylists.php) /LaIVE - Module create entry Lists (create_entrylists.php)
### Erstellt von / Created by Kilian Wenzel
### Zuletzt geändert: / Last change: 0.10.0.2013-07-01

if($CreateEntyListsOn == 1) {
	$FileCreateEntryListsTime = fopen("./laive_entrylists.txt", 'w');
	fwrite($FileCreateEntryListsTime, time());
	fclose($FileCreateEntryListsTime);
	
	# DBS Modus - Einlesen der Startklassen / IPC Mode - read start classes from file
if($IPCModeON == 1) {
	$DBSTextskl_entrylists = IPCClassesArray();
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
																																						'VereinBez'	=> trim(substr($VereinInhalt, $VereinAbsolutePosition + 7, 30)),
																																						'Sortierung'	=> trim(substr($VereinInhalt, $VereinAbsolutePosition + 62, 25))
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
	
	if(trim(substr($StammInhalt, $StammAbsolutePosition + 49, 1)) == 1) {
					$TmpStaffel = trim(substr($StammInhalt, $StammAbsolutePosition + 49, 1));
				}
				else {
					$TmpStaffel = 0;
				}
	
	# DBS Startklasse ermitteln / Set IPC Class
				if($IPCModeON == 1) {
					$TmpIPCClass = $DBSTextskl_entrylists[trim(substr($StammInhalt, $StammAbsolutePosition + 66, 2)) * 1]['IPCClassName'];
				}
				
		$StammDatensatzzaehler++;
		
		$Stamm[trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5))] = array(	'StartNr'		=>	trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5)),
																					'Nachname'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 4, 22)),
																					'Vorname'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 27, 16)),
																					'JG'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 44, 4)),
																					'Geschlecht'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 48, 1)),
																					'LV'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 50, 3)),
																					'VereinNr'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 53, 5)),
																					'Staffel'	=> $TmpStaffel ,
																					'Verein'	=> $Verein[trim(substr($StammInhalt, $StammAbsolutePosition + 50, 3))."-".trim(substr($StammInhalt, $StammAbsolutePosition + 53, 5))]['VereinBez'],
																					'Wertungsgruppe1'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 71, 1)),
																					'Wertungsgruppe2'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 72, 1)),
																					'Wertungsgruppe3'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 73, 1)),
																					'Wertungsgruppe4'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 74, 1)),
																					'Wertungsgruppe5'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 75, 1)),
																					'Wertungsgruppe6'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 76, 1)),
																					'Wertungsgruppe7'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 77, 1)),
																					'IPCClassName'			=> $TmpIPCClass
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
		
		
		# Überprüfen, ob Wettbewerb ausgegeben werden soll
		if(strpos(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 2, 32)),"#0") !== false) {
			$TmpWettbewAktiv = 0;
		}
		else {
			$TmpWettbewAktiv = 1;
		}
		
		
		
		$WettbewE[trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3))*1] = array(	'WettbewNr'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3))*1,
							'WettbewBez'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 2, 32)),
							'WettbewKurz'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 58, 7)),
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
							'COSANrAKBez'		=>	$Klassen[trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 36, 2))]['Bez'],
							'COSANrDISBez'		=>	$Disziplinen[trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 38, 3))*1]['Kurz'],
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
							'Aktiv'		=>	$TmpWettbewAktiv
							
		);			
	
		$WettbewAbsolutePositionDS = $WettbewAbsolutePositionDS + $WettbewLaengeDatensatz;
	
	}
	
	
} # Ende Wettbew.c01
	
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
				if(trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 4, 3)) != "***") {
				
				# Final Confirmation
				$TmpAthleteStarts = trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 31, 1));
				if($TmpAthleteStarts != 1) {
					$TmpAthleteStarts = 0;
				}
				switch($TmpAthleteStarts) {
					case 1:
						$TmFinalConfirmation = 0;
					break;
					case 0:
						$TmFinalConfirmation = 1;
					break;
				}
				
				$WbTeiln3E[trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 4, 3)) * 1 ."-".trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS - 1, 5))] = array ( 	'StNr' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS - 1, 5)),
							'WettbewNr' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 4, 3)) * 1,
							'COSANr' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 7, 5)),
							'aW' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 22, 1)),
							'Meldeleistung' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 13, 8)),
							'AK' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 90, 3)),
							'LeistungVorlauf' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 52, 10)),
							'QualiVorlauf' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 62, 1)),
							'LeistungZwischenlauf' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 65, 10)),
							'QualiZwischenlauf' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 75, 1)),
							'Staffel'			=> trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 12, 1)),
							'Nachmeldung'			=> trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 23, 1)),
							'VereinNrStG'			=> trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 32, 5)),
							'Riege'			=> trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 28, 2))*1,
							'FinalConfirmation' => $TmFinalConfirmation
						);
				}
				$WbTeilnAbsolutePositionDS = $WbTeilnAbsolutePositionDS + $WbTeilnLaengeDatensatz;
			}	
		} # Ende WbTeiln.c01
	
		# Aufbereiten der Ausgabedatei
		
		
		foreach($WettbewE as $WettbewZeile) { # Ausgabedatei
		$TmpAnzahlStaffeln = 0;
				foreach($WbTeiln3E as $Teilnehmer2Zeile) { # Teilnehmer zu Wettbewerben
				
					if($Teilnehmer2Zeile['WettbewNr'] == $WettbewZeile['WettbewNr']) { #1
					
					
					
				
				
				
				
				
				
				if($WettbewZeile['Aktiv'] != 0) {
				
						# Vereinsnummern (bei StG)
						if($Teilnehmer2Zeile['VereinNrStG'] != "") {
							$TmpVereinNrTeiln = $Teilnehmer2Zeile['VereinNrStG'];
						}
						else {
							$TmpVereinNrTeiln = $Stamm[$Teilnehmer2Zeile['StNr']]['VereinNr'];
						}
					
						$TeilnehmerZumWettbewerb[] = array (	'StNr'		=>	$Teilnehmer2Zeile['StNr'],
																'Nachname'	=>	$Stamm[$Teilnehmer2Zeile['StNr']]['Nachname'],
																'Vorname'	=>	$Stamm[$Teilnehmer2Zeile['StNr']]['Vorname'],
																'LV'	=>	$Stamm[$Teilnehmer2Zeile['StNr']]['LV'],
																'Verein'	=>	$Stamm[$Teilnehmer2Zeile['StNr']]['Verein'],
																'VereinNr'	=>	$TmpVereinNrTeiln,
																'JG'	=>	$Stamm[$Teilnehmer2Zeile['StNr']]['JG'],
																'Geschlecht'	=>	$Stamm[$Teilnehmer2Zeile['StNr']]['Geschlecht'],
																'Meldeleistung'		=>	$Teilnehmer2Zeile['Meldeleistung'],
																'aW'		=>	$Teilnehmer2Zeile['aW'],
																'AK'		=>	$Teilnehmer2Zeile['AK'],
																'Staffel'		=>	$Teilnehmer2Zeile['Staffel'],
																'Nachmeldung'		=>	$Teilnehmer2Zeile['Nachmeldung'],
																'WettbewTyp'	=>	$WettbewZeile['WettbewTyp'],
																'Riege'		=>	$Teilnehmer2Zeile['Riege'],
																'IPCClassName'	=>	$Stamm[$Teilnehmer2Zeile['StNr']]['IPCClassName'],
																'FinalConfirmation' => $Teilnehmer2Zeile['FinalConfirmation'],
																'EvaluationGroup1' => $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe1'],
																'EvaluationGroup2' => $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe2'],
																'EvaluationGroup3' => $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe3'],
																'EvaluationGroup4' => $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe4'],
																'EvaluationGroup5' => $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe5'],
																'EvaluationGroup6' => $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe6'],
																'EvaluationGroup7' => $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe7']
																);
																
					# Bei Staffeln
					if ($Teilnehmer2Zeile['Staffel'] == 1) {
						$TmpAnzahlStaffeln++;
					}
					
					}
					
					} #1
				
				}

				
				# Teilnehmer sortieren
		if(count($TeilnehmerZumWettbewerb) > 0)		{
				foreach ($TeilnehmerZumWettbewerb as $nr => $inhalt) {

				$T2StNr[$nr] = strtolower($inhalt['StNr']);
				$T2Nachname[$nr] = strtolower($inhalt['Nachname']);
				$T2Vorname[$nr] = strtolower($inhalt['Vorname']);
				$T2LV[$nr] = strtolower($inhalt['LV']);
				$T2Verein[$nr] = strtolower($inhalt['Verein']);
				$T2VereinNr[$nr] = strtolower($inhalt['VereinNr']);
				$T2JG[$nr] = strtolower($inhalt['JG']);
				$T2Meldeleistung[$nr] = strtolower($inhalt['Meldeleistung']);
				$T2aW[$nr] = strtolower($inhalt['aW']);
				$T2AK[$nr] = strtolower($inhalt['AK']);
				$T2Staffel[$nr] = strtolower($inhalt['Staffel']);
				$T2Nachmeldung[$nr] = strtolower($inhalt['Nachmeldung']);
				$T2Riege[$nr] = strtolower($inhalt['Riege']);
				$T2IPCClassName[$nr] = strtolower($inhalt['IPCClassName']);
				$T2FinalConfirmation[$nr] = strtolower($inhalt['FinalConfirmation']);
				$T2EvaluationGroup1[$nr] = strtolower($inhalt['EvaluationGroup1']);
				$T2EvaluationGroup2[$nr] = strtolower($inhalt['EvaluationGroup2']);
				$T2EvaluationGroup3[$nr] = strtolower($inhalt['EvaluationGroup3']);
				$T2EvaluationGroup4[$nr] = strtolower($inhalt['EvaluationGroup4']);
				$T2EvaluationGroup5[$nr] = strtolower($inhalt['EvaluationGroup5']);
				$T2EvaluationGroup6[$nr] = strtolower($inhalt['EvaluationGroup6']);
				$T2EvaluationGroup7[$nr] = strtolower($inhalt['EvaluationGroup7']);
				$T2SortName[$nr] = strtolower($inhalt['Nachname'])." ".strtolower($inhalt['Vorname'])." ".strtolower($inhalt['Verein'])." ".strtolower($inhalt['JG']);
				
				if($inhalt['Meldeleistung'] != "") {
					$T2SortMeldeleistung[$nr] = str_replace(",", ".",strtolower($inhalt['Meldeleistung']));
				}
				else {
					$T2SortMeldeleistung[$nr] = 0;
				}
				
				
				# Meldeleistung Bahnwettbewerbe umrechnen
				
				if($inhalt['Meldeleistung'] == "") {
					$T2SortMeldeleistungZeit[$nr] = 99999999999999999999;
				}
				else {
				$TmpZeitStandard = str_replace(",", ".", $inhalt['Meldeleistung']); # format mit Punkt anstatt des kommas 12:12.2 z. b.
				
				if(strpos($TmpZeitStandard, ":") == FALSE) {
				
					$T2SortMeldeleistungZeit[$nr] = $TmpZeitStandard;
					
				}
				
				else {
					$TmpZeitZerlegt = explode(':', $TmpZeitStandard);
				
					$TmpZeitZerlegtLaenge = count($TmpZeitZerlegt);
				
					switch($TmpZeitZerlegtLaenge) {
					
						case 2: # Minuten und Sekunden
						
							$T2SortMeldeleistungZeit[$nr] = ($TmpZeitZerlegt[0] * 60) + $TmpZeitZerlegt[1];
						
						break;
						
						case 3: # Stunden, Minuten und Sekunden
						
							$T2SortMeldeleistungZeit[$nr] = ($TmpZeitZerlegt[0] * 3600) + ($TmpZeitZerlegt[1] * 60) + $TmpZeitZerlegt[2];
						
						break;
					}
				}
				unset($TmpZeitStandard);
				unset($TmpZeitZerlegt);
				unset($TmpZeitZerlegtLaenge);	
				}
				
				if($WettbewZeile['WettbewTyp'] == "m") {
					$T2SortName[$nr] = $T2Riege[$nr]." ".$T2SortName[$nr];
					$T2StNr[$nr] = $T2Riege[$nr]." ".$T2StNr[$nr];
					$T2SortMeldeleistung[$nr] = 100 - $T2Riege[$nr]." ".$T2SortMeldeleistung[$nr];
					
					}
	}
}
				$_GET['sort'] = 3;
				

				switch($_GET['sort']) {
				
					case 1: # nach St.-Nr.
					if(count($T2StNr)) {
						array_multisort($T2StNr, SORT_ASC, $TeilnehmerZumWettbewerb);
					}
					break;
					
					case 2: # nach Namen
					if(count($T2SortName)) {
						array_multisort($T2SortName, SORT_ASC, $TeilnehmerZumWettbewerb);
					}
					break;
					
					case 3: # nach Meldeleistungen
						
						switch($WettbewZeile['WettbewTyp']) {
						
							case "t":
							case "h":
							if(count($T2SortMeldeleistung)) {
								array_multisort($T2SortMeldeleistung, SORT_DESC, $TeilnehmerZumWettbewerb);
							}
							break;
							
							case "l":
							case "s":
							case "w":
							if(count($T2SortMeldeleistungZeit)) {
								array_multisort($T2SortMeldeleistungZeit, SORT_ASC, $TeilnehmerZumWettbewerb);
							}
							break;
							
							case "m":
							if(count($T2SortMeldeleistung)) {
								array_multisort($T2SortMeldeleistung, SORT_DESC, $TeilnehmerZumWettbewerb);
							}
							break;
						
						}
						
					
					
						
					break;
				
				}
				unset($T2StNr);
				unset($T2Nachname);
				unset($T2Vorname);
				unset($T2LV);
				unset($T2Verein);
				unset($T2VereinNr);
				unset($T2JG);
				unset($T2Meldeleistung);
				unset($T2aW);
				unset($T2AK);
				unset($T2Staffel);
				unset($T2Nachmeldung);
				unset($T2Riege);
				unset($T2IPCClassName);
				unset($T2SortName);
				unset($T2SortMeldeleistung);
				unset($T2SortMeldeleistungZeit);
				unset($nr);
				unset($inhalt);
				
				
				
				#Ausgabe2 Linkliste
				if($WettbewZeile['Aktiv'] != 0) {
				if(count($TeilnehmerZumWettbewerb) > 0 && $WettbewZeile['WettbewTyp'] != "s" || $TmpAnzahlStaffeln > 0 && $WettbewZeile['WettbewTyp'] == "s" ) {
				
				
					# Disziplin-Kurz-Bezeichnung
					if($WettbewZeile['WettbewKurz'] != "") {
						$TmpWettbewBezDis = $WettbewZeile['WettbewKurz'];
					}
					else {
						$TmpWettbewBezDis = $WettbewZeile['COSANrDISBez'];
					}
				
					$Ausgabe2Linkliste[] = array (	'WettbewNr'	=>	$WettbewZeile['WettbewNr'],
													'COSANrAK'	=>	$WettbewZeile['COSANrAK'],
													'COSANrDIS'	=>	$WettbewZeile['COSANrDIS'],
													'COSANrAKBez'	=>	$WettbewZeile['COSANrAKBez'],
													'COSANrDISBez'	=>	$TmpWettbewBezDis,
													'DISBez'	=>	$WettbewZeile['DISBez'],
													'AKBez'	=>	$WettbewZeile['AKBez']);
				
				}
				
				
				# Ausgabedatei
				
				$Ausgabe2[] = array (	'WettbewNr'		=>	$WettbewZeile['WettbewNr'],	
									'WettbewBez'		=>	$WettbewZeile['WettbewBez'],
									'WettbewTyp'		=>	$WettbewZeile['WettbewTyp'],
									'VorlaufZeit'		=>	$WettbewZeile['VorlaufZeit'],
									'VorlaufTag'		=>	$WettbewZeile['VorlaufTag'],	
									'ZwischenlaufZeit'		=>	$WettbewZeile['ZwischenlaufZeit'],
									'ZwischenlaufTag'		=>	$WettbewZeile['ZwischenlaufTag'],
									'FinaleZeit'		=>	$WettbewZeile['FinaleZeit'],
									'FinaleTag'		=>	$WettbewZeile['FinaleTag'],
									'COSANrAK'		=>	$WettbewZeile['COSANrAK'],
									'COSANrDIS'		=>	$WettbewZeile['COSANrDIS'],
									'COSANrAKBez'		=>	$WettbewZeile['COSANrAKBez'],
									'COSANrDISBez'		=>	$WettbewZeile['COSANrDISBez'],
									'DISBez'		=>	$WettbewZeile['DISBez'],
									'AKBez'		=>	$WettbewZeile['AKBez'],
									'Teilnehmer'	=>	$TeilnehmerZumWettbewerb,
									'AnzahlStaffeln'=>	$TmpAnzahlStaffeln
									);
									unset($TeilnehmerZumWettbewerb);
				}
		
		} # Ausgabedatei
	
		# Sortieren
		# Ausgabe2 Linkliste

foreach ($Ausgabe2Linkliste as $nr => $inhalt) {

	$SWettbewNr[$nr] = strtolower($inhalt['WettbewNr']);
	$SCOSANrAK[$nr] = strtolower($inhalt['COSANrAK']);
	$SCOSANrDIS[$nr] = strtolower($inhalt['COSANrDIS']);
	$SCOSANrAKBez[$nr] = strtolower($inhalt['COSANrAKBez']);
	$SCOSANrDISBez[$nr] = strtolower($inhalt['COSANrDISBez']);
	$SDISBez[$nr] = strtolower($inhalt['RDISBez']);
	$SAKBez[$nr] = strtolower($inhalt['RAKBez']);
	$SCOSANr[$nr] = strtolower($inhalt['COSANrAK']).strtolower($inhalt['COSANrDIS']);
	
}
	
	array_multisort($SCOSANr, SORT_ASC, $Ausgabe2Linkliste);


	
		# Sortieren
		# Ausgabe2

foreach ($Ausgabe2 as $nr => $inhalt) {

	$RWettbewNr[$nr] = strtolower($inhalt['WettbewNr']);
	$RWettbewBez[$nr] = strtolower($inhalt['WettbewBez']);
	$RWettbewTyp[$nr] = strtolower($inhalt['WettbewTyp']);
	$RVorlaufZeit[$nr] = strtolower($inhalt['VorlaufZeit']);
	$RVorlaufTag[$nr] = strtolower($inhalt['VorlaufTag']);
	$RZwischenlaufZeit[$nr] = strtolower($inhalt['ZwischenlaufZeit']);
	$RZwischenlaufTag[$nr] = strtolower($inhalt['ZwischenlaufTag']);
	$RFinaleZeit[$nr] = strtolower($inhalt['FinaleZeit']);
	$RFinaleTag[$nr] = strtolower($inhalt['RFinaleTag']);
	$RCOSANrAK[$nr] = strtolower($inhalt['COSANrAK']);
	$RCOSANrDIS[$nr] = strtolower($inhalt['COSANrDIS']);
	$RCOSANrAKBez[$nr] = strtolower($inhalt['COSANrAKBez']);
	$RCOSANrDISBez[$nr] = strtolower($inhalt['COSANrDISBez']);
	$RDISBez[$nr] = strtolower($inhalt['RDISBez']);
	$RAKBez[$nr] = strtolower($inhalt['RAKBez']);
	$RTeilnehmer[$nr] = $inhalt['Teilnehmer'];
	$RAnzahlStaffeln[$nr] = strtolower($inhalt['AnzahlStaffeln']);
	$RCOSANr[$nr] = strtolower($inhalt['COSANrAK']).strtolower($inhalt['COSANrDIS']);
	
}
	
	array_multisort($RCOSANr, SORT_ASC, $Ausgabe2);
			
		#print_r($Ausgabe2);
		
		# Ausgeben -------------------------------------------
		#___________________________________________
		#Daten ausgeben


		# Head

		$EntryListsByEventsHead = "<table class='body' cellspacing='0'><tr><td class='KopfZ1'>" . $Kopfzeile1 . "</td></tr><tr><td class='KopfZ11'>" . $Kopfzeile2 . "</td></tr><tr><td class='KopfZ12'>" . $Kopfzeile3 . "</td></tr></table>";

			
			$COSANrAKVorher = "";
			$RiegeVorher = "";
			
			$TmpOutputEntryLists = array();
			
			foreach($Ausgabe2 as $Ausgabe2Zeile) {
			if(count($Ausgabe2Zeile['Teilnehmer']) > 0) {
			$TmpFinalConfirmationCount = 0;
			$TmpTeilnehmer = 0;
			$TmpMannschaften = 0;
			$TmpTeilnehmerRiege = array();
			
			# Dateiname für Teinehmerliste
			$TmpFilenameEntryList = "t" . $Ausgabe2Zeile['COSANrAK'] . $Ausgabe2Zeile['COSANrDIS'] . ".htm";
			
			# Tmp Contents
			$TmpContentEntryList = "";
			$TmpContentEntryList_EvaluationGroup1 = "";
			$TmpContentEntryList_EvaluationGroup2 = "";
			$TmpContentEntryList_EvaluationGroup3 = "";
			$TmpContentEntryList_EvaluationGroup4 = "";
			$TmpContentEntryList_EvaluationGroup5 = "";
			$TmpContentEntryList_EvaluationGroup6 = "";
			$TmpContentEntryList_EvaluationGroup7 = "";
			
			$TmpContentEntryList = $EntryListsByEventsHead;
				
				
				
				if(count($tage) > 1) {
					$UeberschriftVorlaufTag = substr($Wochentage[date("w", $TageUnix[$Ausgabe2Zeile['VorlaufTag']])], 0, $LengthForAbbrevDaysOfWeek) . "., " . substr($tage[$Ausgabe2Zeile['VorlaufTag']], 0, 6). ", ";
					$UeberschriftZwischenlaufTag = substr($Wochentage[date("w", $TageUnix[$Ausgabe2Zeile['ZwischenlaufTag']])], 0, $LengthForAbbrevDaysOfWeek) . "., " . substr($tage[$Ausgabe2Zeile['ZwischenlaufTag']], 0, 6). ", ";
					$UeberschriftFinaleTag = substr($Wochentage[date("w", $TageUnix[$Ausgabe2Zeile['FinaleTag']])], 0, $LengthForAbbrevDaysOfWeek) . "., " . substr($tage[$Ausgabe2Zeile['FinaleTag']], 0, 6). ", ";
					
				}
				else {
					$UeberschriftVorlaufTag = "";
					$UeberschriftZwischenlaufTag = "";
					$UeberschriftFinaleTag = "";
				}
				
				
			
				# Vorläufe
				if($Ausgabe2Zeile['VorlaufZeit'] != "") {
					$TmpVorrunde = $TxtEntriesByEventsHeats . ": ". $UeberschriftVorlaufTag . str_replace(".", ":", $Ausgabe2Zeile['VorlaufZeit']) . " " . $TxtDaytimeUnit;
				}
				else {
					$TmpVorrunde = "&nbsp;";
				}
				# Zwischenläufe
				if($Ausgabe2Zeile['ZwischenlaufZeit'] != "") {
					$TmpZwischenlaeufe = $TxtEntriesByEventsSemiFinals. ": ". $UeberschriftZwischenlaufTag . str_replace(".", ":",$Ausgabe2Zeile['ZwischenlaufZeit']) . " ". $TxtDaytimeUnit;
				}
				else {
					$TmpZwischenlaeufe = "&nbsp;";
				}
				# Finale
				if($Ausgabe2Zeile['WettbewTyp'] != "m") {
					if($Ausgabe2Zeile['FinaleZeit'] != "") {
						$TmpFinale = $TxtEntriesByEventsFinal . ": ". $UeberschriftFinaleTag . str_replace(".", ":",$Ausgabe2Zeile['FinaleZeit']) . " ". $TxtDaytimeUnit;
					}
					else {
						$TmpFinale = "&nbsp;";
					}
				}
				else {
					if($Ausgabe2Zeile['FinaleZeit'] != "") {
						$TmpFinale = $TxtEntriesByEventsCombinedEventFirstEvent . ": " . $UeberschriftFinaleTag . str_replace(".", ":",$Ausgabe2Zeile['FinaleZeit']) . " ". $TxtDaytimeUnit;
					}
					else {
						$TmpFinale = "&nbsp;";
					}
				}
				
				if(count($Ausgabe2Zeile['Teilnehmer']) > 0 && $Ausgabe2Zeile['WettbewTyp'] != "s" || $Ausgabe2Zeile['AnzahlStaffeln'] > 0 && $Ausgabe2Zeile['WettbewTyp'] == "s") {
			
				# Überschrift Teilnehmerliste Wettbewerb
				$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td class='KopfZ21'>" . $TxtEntrylistHeadline . ": " . $Ausgabe2Zeile['WettbewBez'] ."</td></tr></table><br>";
				
				# Startzeiten der Runden
				$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>".$TmpVorrunde."</a></td><td CLASS='blEFreiDisTm'><a CLASS='blEFreiDisT'>".$TmpZwischenlaeufe."</a></td><td CLASS='blEFreiDisTr'><a CLASS='blEFreiDisT'>".$TmpFinale."</a></td></tr></table>";
			
				
			
			

				$Zeilenwechsler = 0;
				$Zeilenwechsler2 = 0;
				$LineAthleteEGOne = 0;
				$LineAthleteEGTwo = 0;
				$LineAthleteEGThree = 0;
				$LineAthleteEGFour = 0;
				$LineAthleteEGFive = 0;
				$LineAthleteEGSix = 0;
				$LineAthleteEGSeven = 0;
				$LineRelayEGOne = 0;
				$LineRelayEGTwo = 0;
				$LineRelayEGThree = 0;
				$LineRelayEGFour = 0;
				$LineRelayEGFive = 0;
				$LineRelayEGSix = 0;
				$LineRelayEGSeven = 0;
				$EvatuationGroup1Count = 0;
				$EvatuationGroup2Count = 0;
				$EvatuationGroup3Count = 0;
				$EvatuationGroup4Count = 0;
				$EvatuationGroup5Count = 0;
				$EvatuationGroup6Count = 0;
				$EvatuationGroup7Count = 0;
				
				unset($TmpStaffelteilnehmer);
				
				$TmpStaffelteilnehmer = $Ausgabe2Zeile['Teilnehmer'];
				
				foreach($Ausgabe2Zeile['Teilnehmer'] as $TeilnehmerZeile) {
				
				
				if($TeilnehmerZeile['Riege'] != $RiegeVorher && $TeilnehmerZeile['Riege'] != 0 &&  $TeilnehmerZeile['Riege'] != "") {
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTlriege'><a CLASS='blEFreiDisTriege'>". $TxtCombinedEventGroup . " ".$TeilnehmerZeile['Riege']."</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td></tr></table>";
					$Zeilenwechsler = 0;
					
					# Evaluation Groups
					if($TeilnehmerZeile['EvaluationGroup1'] == 1) {
						$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTlriege'><a CLASS='blEFreiDisTriege'>". $TxtCombinedEventGroup . " ".$TeilnehmerZeile['Riege']."</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td></tr></table>";
						$LineAthleteEGOne = 0;
					}
					if($TeilnehmerZeile['EvaluationGroup2'] == 1) {
						$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTlriege'><a CLASS='blEFreiDisTriege'>". $TxtCombinedEventGroup . " ".$TeilnehmerZeile['Riege']."</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td></tr></table>";
						$LineAthleteEGTwo = 0;
					}
					if($TeilnehmerZeile['EvaluationGroup3'] == 1) {
						$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTlriege'><a CLASS='blEFreiDisTriege'>". $TxtCombinedEventGroup . " ".$TeilnehmerZeile['Riege']."</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td></tr></table>";
						$LineAthleteEGThree = 0;
					}
					if($TeilnehmerZeile['EvaluationGroup4'] == 1) {
						$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTlriege'><a CLASS='blEFreiDisTriege'>". $TxtCombinedEventGroup . " ".$TeilnehmerZeile['Riege']."</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td></tr></table>";
						$LineAthleteEGFour = 0;
					}
					if($TeilnehmerZeile['EvaluationGroup5'] == 1) {
						$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTlriege'><a CLASS='blEFreiDisTriege'>". $TxtCombinedEventGroup . " ".$TeilnehmerZeile['Riege']."</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td></tr></table>";
						$LineAthleteEGFive = 0;
					}
					if($TeilnehmerZeile['EvaluationGroup6'] == 1) {
						$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTlriege'><a CLASS='blEFreiDisTriege'>". $TxtCombinedEventGroup . " ".$TeilnehmerZeile['Riege']."</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td></tr></table>";
						$LineAthleteEGSix = 0;
					}
					if($TeilnehmerZeile['EvaluationGroup7'] == 1) {
						$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTlriege'><a CLASS='blEFreiDisTriege'>". $TxtCombinedEventGroup . " ".$TeilnehmerZeile['Riege']."</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td></tr></table>";
						$LineAthleteEGSeven = 0;
					}
					
					
				}
				$RiegeVorher = $TeilnehmerZeile['Riege'];
				
		
			
							if($TeilnehmerZeile['aW'] == 1) {
							$TmpaW = $TxtAbbrevOutOfRanking;
						}
						else {
							$TmpaW = "";
						}
						
						
					
						if($TeilnehmerZeile['Nachmeldung'] == 1) {
							$TmpNachmeldung = "<a class='nachmeldung'> " . $TxtAbbrevLateEntry . "</a>";
						}
						else {
							$TmpNachmeldung = "";
						}
						
						#echo $TeilnehmerZeile['Riege'];
						
						if($TeilnehmerZeile['Riege'] != "" && $TeilnehmerZeile['Riege'] != 0) {
							$TmpRiege = $TxtCombinedEventGroup . " ".$TeilnehmerZeile['Riege'];
						}
						else {
							$TmpRiege = "";
						}
			
			#Geschlecht
			switch($TeilnehmerZeile['Geschlecht']) {
				case 0: # männlich
					$tmpgeschlecht = $TxtAbrrevGenderMale;
					$tmpgeschlechtAK = $TxtAbrrevGenderMan;
				break;
				case 1: # weiblich
					$tmpgeschlecht = $TxtAbrrevGenderFemale;
					$tmpgeschlechtAK = $TxtAbrrevGenderWoman;
				break;
			}
			
			
			
			
			
			switch($Ausgabe2Zeile['WettbewTyp']) {
			
			
				case "s":  # Staffel
				
					
				
				
				
					
				
				if($TeilnehmerZeile['Staffel'] == 1) {
				switch($Zeilenwechsler2) {
		
					case 0:
						$farbe2 = "g";
						$Zeilenwechsler2 = 1;
					break;
			
					case 1:
						$farbe2 = "w";
						$Zeilenwechsler2 = 0;
					break;
					}
				$TmpMannschaften++;
				$TmpFinalConfirmationCount = $TmpFinalConfirmationCount + $TeilnehmerZeile['FinalConfirmation'];
				
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
					if($IPCModeON == 1) {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='IPCClass$farbe2'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
					if($StartnummernAn == 1) {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEStNr$farbe2'>".$TmpaW." ".$TeilnehmerZeile['StNr'] ."</td>";} else {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEStNr$farbe2'>$TmpaW &nbsp;</td>";}
					
					if($FinalConfirmationOn == 1) {
					
						$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blENameAS$farbe2'><a class='FinalConfirmationAthlete".$TeilnehmerZeile['FinalConfirmation']."'>".$TeilnehmerZeile['Verein']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</a></td>";
					}
					else {
						$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blENameAS$farbe2'>".$TeilnehmerZeile['Verein']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</td>";
					}
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEJG$farbe2'>"."&nbsp;"."</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blELv$farbe2'>";
					
					if($FlagsOn == 1) {$TmpContentEntryList = $TmpContentEntryList . "<img src='" .  $PathToFlags . $TeilnehmerZeile['LV'] . $FileFormatFlags . "' alt='".$TeilnehmerZeile['LV']."' class='imgflags'>";}
					else {$TmpContentEntryList = $TmpContentEntryList . $TeilnehmerZeile['LV'] ."</td>";}
					
					
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEVerein$farbe2'>".$TeilnehmerZeile['Verein'] ."</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blELeist$farbe2'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEPokP$farbe2'>".$TmpNachmeldung." ".""."</td>";
					
					if($FinalConfirmationOn == 1) {
						$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='FinalConfirmation$farbe2'><a class='FinalConfirmation".$TeilnehmerZeile['FinalConfirmation']."'><abbr title='" . $TxtFinalConfirmation[$TeilnehmerZeile['FinalConfirmation']]['Explanation'] . "'>" . $TxtFinalConfirmation[$TeilnehmerZeile['FinalConfirmation']]['Abbrev'] . "</abbr></a></td>";
					}
					$TmpContentEntryList = $TmpContentEntryList . "</tr></table>";
				
				
				# Evaluation Groups
			if($TeilnehmerZeile['EvaluationGroup1'] == 1) {
				$EvatuationGroup1Count++;
				switch($LineRelayEGOne) {
					case 0:
						$farbeREG1 = "g";
						$LineRelayEGOne = 1;
					break;
					case 1:
						$farbeREG1 = "w";
						$LineRelayEGOne = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<td CLASS='IPCClass$farbeREG1'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<td CLASS='blEStNr$farbeREG1'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeREG1'>".$TeilnehmerZeile['Verein']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</td>" . "<td CLASS='blEJG$farbeREG1'>". "&nbsp;" ."</td>" . "<td CLASS='blELv$farbeREG1'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeREG1'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeREG1'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeREG1'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup2'] == 1) {
				$EvatuationGroup2Count++;
				switch($LineRelayEGTwo) {
					case 0:
						$farbeREG2 = "g";
						$LineRelayEGTwo = 1;
					break;
					case 1:
						$farbeREG2 = "w";
						$LineRelayEGTwo = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<td CLASS='IPCClass$farbeREG2'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<td CLASS='blEStNr$farbeREG2'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeREG2'>".$TeilnehmerZeile['Verein']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</td>" . "<td CLASS='blEJG$farbeREG2'>". "&nbsp;" ."</td>" . "<td CLASS='blELv$farbeREG2'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeREG2'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeREG2'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeREG2'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup3'] == 1) {
				$EvatuationGroup3Count++;
				switch($LineRelayEGThree) {
					case 0:
						$farbeREG3 = "g";
						$LineRelayEGThree = 1;
					break;
					case 1:
						$farbeREG3 = "w";
						$LineRelayEGThree = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<td CLASS='IPCClass$farbeREG3'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<td CLASS='blEStNr$farbeREG3'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeREG3'>".$TeilnehmerZeile['Verein']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</td>" . "<td CLASS='blEJG$farbeREG3'>". "&nbsp;" ."</td>" . "<td CLASS='blELv$farbeREG3'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeREG3'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeREG3'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeREG3'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup4'] == 1) {
				$EvatuationGroup4Count++;
				switch($LineRelayEGFour) {
					case 0:
						$farbeREG4 = "g";
						$LineRelayEGFour = 1;
					break;
					case 1:
						$farbeREG4 = "w";
						$LineRelayEGFour = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4 . "<td CLASS='IPCClass$farbeREG4'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4  . "<td CLASS='blEStNr$farbeREG4'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeREG4'>".$TeilnehmerZeile['Verein']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</td>" . "<td CLASS='blEJG$farbeREG4'>". "&nbsp;" ."</td>" . "<td CLASS='blELv$farbeREG4'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeREG4'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeREG4'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeREG4'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup5'] == 1) {
				$EvatuationGroup5Count++;
				switch($LineRelayEGFive) {
					case 0:
						$farbeREG5 = "g";
						$LineRelayEGFive = 1;
					break;
					case 1:
						$farbeREG5 = "w";
						$LineRelayEGFive = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5 . "<td CLASS='IPCClass$farbeREG5'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5  . "<td CLASS='blEStNr$farbeREG5'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeREG5'>".$TeilnehmerZeile['Verein']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</td>" . "<td CLASS='blEJG$farbeREG5'>". "&nbsp;" ."</td>" . "<td CLASS='blELv$farbeREG5'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeREG5'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeREG5'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeREG5'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup6'] == 1) {
				$EvatuationGroup6Count++;
				switch($LineRelayEGSix) {
					case 0:
						$farbeREG6 = "g";
						$LineRelayEGSix = 1;
					break;
					case 1:
						$farbeREG6 = "w";
						$LineRelayEGSix = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGrou61 = $TmpContentEntryList_EvaluationGroup6 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<td CLASS='IPCClass$farbeREG6'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<td CLASS='blEStNr$farbeREG6'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeREG6'>".$TeilnehmerZeile['Verein']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</td>" . "<td CLASS='blEJG$farbeREG6'>". "&nbsp;" ."</td>" . "<td CLASS='blELv$farbeREG6'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeREG6'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeREG6'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeREG6'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup7'] == 1) {
				$EvatuationGroup7Count++;
				switch($LineRelayEGSeven) {
					case 0:
						$farbeREG7 = "g";
						$LineRelayEGSeven = 1;
					break;
					case 1:
						$farbeREG7 = "w";
						$LineRelayEGSeven = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<td CLASS='IPCClass$farbeREG7'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7  . "<td CLASS='blEStNr$farbeREG7'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeREG7'>".$TeilnehmerZeile['Verein']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</td>" . "<td CLASS='blEJG$farbeREG7'>". "&nbsp;" ."</td>" . "<td CLASS='blELv$farbeREG7'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeREG7'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeREG7'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeREG7'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}

				
				}
				
				
				
				
				
				# Realay Members
				foreach($TmpStaffelteilnehmer as $TmpStaffelteilnehmerZeile) {
				
					if($TmpStaffelteilnehmerZeile['Staffel'] != 1 && $TmpStaffelteilnehmerZeile['LV'].$TmpStaffelteilnehmerZeile['VereinNr'] == $TeilnehmerZeile['LV'].$TeilnehmerZeile['VereinNr'] && $TmpStaffelteilnehmerZeile['Meldeleistung'] == $TeilnehmerZeile['JG']) {
					
					
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEStNr$farbe2'>&nbsp;</td>";
					if($IPCModeON == 1) {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='IPCClass$farbe2'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
					if($StartnummernAn == 1) {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEStNr$farbe2'>".$TmpStaffelteilnehmerZeile['StNr'] ."</td>";} else {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEStNr$farbe2'>&nbsp;</td>";}
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blENameAS$farbe2'>".$TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEJG$farbe2'>".$TmpStaffelteilnehmerZeile['JG']."</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blELv$farbe2'>&nbsp;</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEVerein$farbe2'>&nbsp;</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blELeist$farbe2'>&nbsp;</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEPokP$farbe2'>&nbsp;</td>";
					if($FinalConfirmationOn == 1) {
						$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='FinalConfirmation$farbe2'>&nbsp;</td>";
					}
					$TmpContentEntryList = $TmpContentEntryList . "</tr></table>";
					
					
					# Evaluation Groups
					if($TeilnehmerZeile['EvaluationGroup1'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG1'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<td CLASS='IPCClass$farbeREG1'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<td CLASS='blEStNr$farbeREG1'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG1'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG1'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG1'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG1'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG1'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG1'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup2'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG2'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<td CLASS='IPCClass$farbeREG2'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<td CLASS='blEStNr$farbeREG2'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG2'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG2'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG2'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG2'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG2'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG2'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup3'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG3'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<td CLASS='IPCClass$farbeREG3'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 .  "<td CLASS='blEStNr$farbeREG3'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG3'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG3'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG3'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG3'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG3'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG3'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup4'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG4'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4 . "<td CLASS='IPCClass$farbeREG4'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4  . "<td CLASS='blEStNr$farbeREG4'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG4'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG4'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG4'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG4'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG4'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG4'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup5'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG5'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5 . "<td CLASS='IPCClass$farbeREG5'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5  . "<td CLASS='blEStNr$farbeREG5'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG5'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG5'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG5'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG5'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG5'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG5'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup6'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG6'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<td CLASS='IPCClass$farbeREG6'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<td CLASS='blEStNr$farbeREG6'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG6'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG6'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG6'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG6'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG6'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG6'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup7'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG7'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<td CLASS='IPCClass$farbeREG7'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<td CLASS='blEStNr$farbeREG7'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG7'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG7'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG7'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG7'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG7'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG7'>&nbsp;</td></tr></table>";
					}
					
					
					}
					
					if($TmpStaffelteilnehmerZeile['Staffel'] != 1 && $TmpStaffelteilnehmerZeile['LV'].$TmpStaffelteilnehmerZeile['VereinNr'] == $TeilnehmerZeile['LV'].$TeilnehmerZeile['VereinNr'] && $TmpStaffelteilnehmerZeile['Meldeleistung'] == "" && $TeilnehmerZeile['JG'] == 1) {
					
					
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEStNr$farbe2'>&nbsp;</td>";
					if($IPCModeON == 1) {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='IPCClass$farbe2'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
					if($StartnummernAn == 1) {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEStNr$farbe2'>".$TmpStaffelteilnehmerZeile['StNr'] ."</td>";} else {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEStNr$farbe2'>&nbsp;</td>";}
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blENameAS$farbe2'>".$TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEJG$farbe2'>".$TmpStaffelteilnehmerZeile['JG']."</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blELv$farbe2'>&nbsp;</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEVerein$farbe2'>&nbsp;</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blELeist$farbe2'>&nbsp;</td>";
					$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEPokP$farbe2'>&nbsp;</td>";
					if($FinalConfirmationOn == 1) {
						$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='FinalConfirmation$farbe2'>&nbsp;</td>";
					}
					$TmpContentEntryList = $TmpContentEntryList . "</tr></table>";
					
					
					# Evaluation Groups
					if($TeilnehmerZeile['EvaluationGroup1'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG1'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<td CLASS='IPCClass$farbeREG1'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<td CLASS='blEStNr$farbeREG1'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG1'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG1'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG1'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG1'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG1'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG1'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup2'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG2'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<td CLASS='IPCClass$farbeREG2'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<td CLASS='blEStNr$farbeREG2'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG2'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG2'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG2'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG2'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG2'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG2'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup3'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG3'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<td CLASS='IPCClass$farbeREG3'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 .  "<td CLASS='blEStNr$farbeREG3'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG3'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG3'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG3'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG3'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG3'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG3'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup4'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG4'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4 . "<td CLASS='IPCClass$farbeREG4'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4  . "<td CLASS='blEStNr$farbeREG4'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG4'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG4'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG4'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG4'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG4'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG4'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup5'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG5'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5 . "<td CLASS='IPCClass$farbeREG5'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5  . "<td CLASS='blEStNr$farbeREG5'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG5'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG5'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG5'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG5'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG5'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG5'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup6'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG6'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<td CLASS='IPCClass$farbeREG6'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<td CLASS='blEStNr$farbeREG6'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG6'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG6'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG6'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG6'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG6'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG6'>&nbsp;</td></tr></table>";
					}
					if($TeilnehmerZeile['EvaluationGroup7'] == 1) {
						if($StartnummernAn == 1) {
							$TmpBibNumberEvaluationGroupRelayMember = $TmpStaffelteilnehmerZeile['StNr'];
						}
						else {
							$TmpBibNumberEvaluationGroupRelayMember = "&nbsp;";
						}
						$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>" . "<td CLASS='blEStNr$farbeREG7'>&nbsp;</td>";
						if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<td CLASS='IPCClass$farbeREG7'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
						$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<td CLASS='blEStNr$farbeREG7'>" . $TmpBibNumberEvaluationGroupRelayMember ."</td>" . "<td CLASS='blENameAS$farbeREG7'>" . $TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeREG7'>". $TmpStaffelteilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeREG7'>"."&nbsp;" ."</td>" . "<td CLASS='blEVerein$farbeREG7'>"."&nbsp;" ."</td>" . "<td CLASS='blELeist$farbeREG7'>"."&nbsp;" ."</td>" . "<td CLASS='blEPokP$farbeREG7'>&nbsp;</td></tr></table>";
					}
					
					
					
					
					}
					
					
				
				}
				#unset($TmpStaffelteilnehmer);
				
				
				
				
				break;
				
				default: # alle anderen
				
				$TmpTeilnehmer++;
				$TmpFinalConfirmationCount = $TmpFinalConfirmationCount + $TeilnehmerZeile['FinalConfirmation'];
				if($TeilnehmerZeile['Riege'] != "" && $TeilnehmerZeile['Riege'] != 0) {
							$TmpTeilnehmerRiege[$TeilnehmerZeile['Riege']]++;
						}
				
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
				$GesamtteilnehmerAnzahl++;
	
			
			
			
			if($Ausgabe2Zeile['WettbewTyp'] == "m") {
						
							if(is_numeric($TeilnehmerZeile['AK'])) {
								$TmpAKMK = $tmpgeschlechtAK.$TeilnehmerZeile['AK'];
							}
							else {
								$TmpAKMK = "";
							}
						}
						else {
							$TmpAKMK = "";
						}

		
			
			$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
			if($IPCModeON == 1) {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='IPCClass$farbe'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
			if($StartnummernAn == 1) {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$TeilnehmerZeile['StNr'] ."</td>";} else {$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEStNr$farbe'>$TmpaW &nbsp;</td>";}
			
			if($FinalConfirmationOn == 1) {
				$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blENameAS$farbe'><a class='FinalConfirmationAthlete".$TeilnehmerZeile['FinalConfirmation']."'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</a></td>";
			}
			else {
				$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blENameAS$farbe'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</td>";
			}
			
			$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEJG$farbe'>".$TeilnehmerZeile['JG'] ."</td>";
			
			$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blELv$farbe'>";
			
			if($FlagsOn == 1) {$TmpContentEntryList = $TmpContentEntryList . "<img src='" .  $PathToFlags . $TeilnehmerZeile['LV'] . $FileFormatFlags . "' alt='".$TeilnehmerZeile['LV']."' class='imgflags'>";}
					else {$TmpContentEntryList = $TmpContentEntryList . $TeilnehmerZeile['LV'] ."</td>";}
			
			
			
			$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEVerein$farbe'>".$TeilnehmerZeile['Verein'] ."</td>";
			$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blELeist$farbe'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>";
			$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='blEPokP$farbe'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td>";
			if($FinalConfirmationOn == 1) {
						$TmpContentEntryList = $TmpContentEntryList . "<td CLASS='FinalConfirmation$farbe'><a class='FinalConfirmation".$TeilnehmerZeile['FinalConfirmation']."'><abbr title='" . $TxtFinalConfirmation[$TeilnehmerZeile['FinalConfirmation']]['Explanation'] . "'>" . $TxtFinalConfirmation[$TeilnehmerZeile['FinalConfirmation']]['Abbrev'] . "</abbr></a></td>";
					}
			$TmpContentEntryList = $TmpContentEntryList . "</tr></table>";
			
			
			# Evaluation Groups
			if($TeilnehmerZeile['EvaluationGroup1'] == 1) {
				$EvatuationGroup1Count++;
				switch($LineAthleteEGOne) {
					case 0:
						$farbeAEG1 = "g";
						$LineAthleteEGOne = 1;
					break;
					case 1:
						$farbeAEG1 = "w";
						$LineAthleteEGOne = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<td CLASS='IPCClass$farbeAEG1'>" . $TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup1 = $TmpContentEntryList_EvaluationGroup1 . "<td CLASS='blEStNr$farbeAEG1'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeAEG1'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeAEG1'>".$TeilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeAEG1'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeAEG1'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeAEG1'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeAEG1'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup2'] == 1) {
				$EvatuationGroup2Count++;
				switch($LineAthleteEGTwo) {
					case 0:
						$farbeAEG2 = "g";
						$LineAthleteEGTwo = 1;
					break;
					case 1:
						$farbeAEG2 = "w";
						$LineAthleteEGTwo = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 . "<td CLASS='IPCClass$farbeAEG2'>" . $TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup2 = $TmpContentEntryList_EvaluationGroup2 .  "<td CLASS='blEStNr$farbeAEG2'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeAEG2'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeAEG2'>".$TeilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeAEG2'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeAEG2'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeAEG2'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeAEG2'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup3'] == 1) {
				$EvatuationGroup3Count++;
				switch($LineAthleteEGThree) {
					case 0:
						$farbeAEG3 = "g";
						$LineAthleteEGThree = 1;
					break;
					case 1:
						$farbeAEG3 = "w";
						$LineAthleteEGThree = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<td CLASS='IPCClass$farbeAEG3'>" . $TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup3 = $TmpContentEntryList_EvaluationGroup3 . "<td CLASS='blEStNr$farbeAEG3'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeAEG3'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeAEG3'>".$TeilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeAEG3'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeAEG3'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeAEG3'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeAEG3'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup4'] == 1) {
				$EvatuationGroup4Count++;
				switch($LineAthleteEGFour) {
					case 0:
						$farbeAEG4 = "g";
						$LineAthleteEGFour = 1;
					break;
					case 1:
						$farbeAEG4 = "w";
						$LineAthleteEGFour = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4 . "<td CLASS='IPCClass$farbeAEG4'>" . $TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup4 = $TmpContentEntryList_EvaluationGroup4 .  "<td CLASS='blEStNr$farbeAEG4'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeAEG4'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeAEG4'>".$TeilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeAEG4'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeAEG4'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeAEG4'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeAEG4'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup5'] == 1) {
				$EvatuationGroup5Count++;
				switch($LineAthleteEGFive) {
					case 0:
						$farbeAEG5 = "g";
						$LineAthleteEGFive = 1;
					break;
					case 1:
						$farbeAEG5 = "w";
						$LineAthleteEGFive = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5 . "<td CLASS='IPCClass$farbeAEG5'>" . $TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup5 = $TmpContentEntryList_EvaluationGroup5  . "<td CLASS='blEStNr$farbeAEG5'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeAEG5'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeAEG5'>".$TeilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeAEG5'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeAEG5'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeAEG5'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeAEG5'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup6'] == 1) {
				$EvatuationGroup6Count++;
				switch($LineAthleteEGSix) {
					case 0:
						$farbeAEG6 = "g";
						$LineAthleteEGSix = 1;
					break;
					case 1:
						$farbeAEG6 = "w";
						$LineAthleteEGSix = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6 . "<td CLASS='IPCClass$farbeAEG6'>" . $TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup6 = $TmpContentEntryList_EvaluationGroup6  . "<td CLASS='blEStNr$farbeAEG6'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeAEG6'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeAEG6'>".$TeilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeAEG6'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeAEG6'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeAEG6'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeAEG6'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}
			if($TeilnehmerZeile['EvaluationGroup7'] == 1) {
				$EvatuationGroup7Count++;
				switch($LineAthleteEGSeven) {
					case 0:
						$farbeAEG7 = "g";
						$LineAthleteEGSeven = 1;
					break;
					case 1:
						$farbeAEG7 = "w";
						$LineAthleteEGSeven = 0;
					break;
					}
				if($StartnummernAn == 1) {
					$TmpBibNumberEvaluationGroup = $TeilnehmerZeile['StNr'];
				}
				else {
					$TmpBibNumberEvaluationGroup = "&nbsp;";
				}
				$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				if($IPCModeON == 1) {$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7 . "<td CLASS='IPCClass$farbeAEG7'>" . $TeilnehmerZeile['IPCClassName'] ."</td>";}
				$TmpContentEntryList_EvaluationGroup7 = $TmpContentEntryList_EvaluationGroup7  . "<td CLASS='blEStNr$farbeAEG7'>".$TmpaW." ".$TmpBibNumberEvaluationGroup ."</td>" . "<td CLASS='blENameAS$farbeAEG7'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</td>" . "<td CLASS='blEJG$farbeAEG7'>".$TeilnehmerZeile['JG'] ."</td>" . "<td CLASS='blELv$farbeAEG7'>".$TeilnehmerZeile['LV'] ."</td>" . "<td CLASS='blEVerein$farbeAEG7'>".$TeilnehmerZeile['Verein'] ."</td>" . "<td CLASS='blELeist$farbeAEG7'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>" . "<td CLASS='blEPokP$farbeAEG7'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td></tr></table>";
			}

			
			
			}	# ----
				
				
				
				
				
				
				
				
				
				}
				switch($Ausgabe2Zeile['WettbewTyp']) {
			
			
				case "s":  # Staffel
															switch($TmpMannschaften) {
																case 1:
																	$TmpMannschaftenText = $TmpMannschaften . " ".$TxtRelayTeam;
																break;
																default:
																	$TmpMannschaftenText = $TmpMannschaften . " ".$TxtRelayTeams;
																break;
															}
				
				
					$TmpContentEntryList = $TmpContentEntryList . "<p class='AnzahlRunden'><a class='AnzahlRunden'>" . $TmpMannschaftenText . "</a></p>";
				break;
				case "m": # mehrkampf
					$TmpContentEntryList = $TmpContentEntryList . "<p class='AnzahlRunden'><a class='AnzahlRunden'>$TmpTeilnehmer ". $TxtParticipants . " (";
					$u = 0;
					foreach($TmpTeilnehmerRiege as $key => $value) {
					$u++;
						$TmpContentEntryList = $TmpContentEntryList . $TxtCombinedEventGroup . "  $key: $value " . $TxtAbbrevParticipants;
						if($u < count($TmpTeilnehmerRiege)) {
							$TmpContentEntryList = $TmpContentEntryList . ", ";
						}
					
					}
					
					$TmpContentEntryList = $TmpContentEntryList . ")</a></p>";
				break;
				case "l":
				case "t":
				case "w":
				case "h":
					$TmpContentEntryList = $TmpContentEntryList . "<p class='AnzahlRunden'><a class='AnzahlRunden'>$TmpTeilnehmer " . $TxtParticipants . "</a></p>";
				break;
				}
				#echo "<br>";
				if($FinalConfirmationOn == 1) {
					$TmpContentEntryList = $TmpContentEntryList . "<p class='FinalConfirmationCount'><a class='FinalConfirmationCount'>" . $TxtFinalConfirmation[1]['Explanation'] . ": <b>" . $TmpFinalConfirmationCount ."</b></a></p>";
				}
				
				
				# Evaluation Groups include in Outputfile
				if($TmpContentEntryList_EvaluationGroup1 != "" || $TmpContentEntryList_EvaluationGroup2 != "" || $TmpContentEntryList_EvaluationGroup3 != "" || $TmpContentEntryList_EvaluationGroup4 != "" || $TmpContentEntryList_EvaluationGroup5 != "" || $TmpContentEntryList_EvaluationGroup6 != "" || $TmpContentEntryList_EvaluationGroup7 != "") {
					$TmpContentEntryList = $TmpContentEntryList . "<table class='body' cellspacing='0'><tr><td class='KopfZ2'>" . $TxtEvaluationGroupsHeadline . "</td></tr></table>";
				}
				if($TmpContentEntryList_EvaluationGroup1 != "") {
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td class='KopfZ1'>" . $wertungsgruppen[1] . "</td></tr></table><br>" . $TmpContentEntryList_EvaluationGroup1 . "<p class='AnzahlRunden'><a class='AnzahlRunden'>" . $wertungsgruppen[1] . ": " . $EvatuationGroup1Count . "</a></p>";
				}
				if($TmpContentEntryList_EvaluationGroup2 != "") {
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td class='KopfZ1'>" . $wertungsgruppen[2] . "</td></tr></table><br>" . $TmpContentEntryList_EvaluationGroup2 . "<p class='AnzahlRunden'><a class='AnzahlRunden'>" . $wertungsgruppen[2] . ": " . $EvatuationGroup2Count . "</a></p>";
				}
				if($TmpContentEntryList_EvaluationGroup3 != "") {
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td class='KopfZ1'>" . $wertungsgruppen[3] . "</td></tr></table><br>" . $TmpContentEntryList_EvaluationGroup3 . "<p class='AnzahlRunden'><a class='AnzahlRunden'>" . $wertungsgruppen[3] . ": " . $EvatuationGroup3Count . "</a></p>";
				}
				if($TmpContentEntryList_EvaluationGroup4 != "") {
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td class='KopfZ1'>" . $wertungsgruppen[4] . "</td></tr></table><br>" . $TmpContentEntryList_EvaluationGroup4 . "<p class='AnzahlRunden'><a class='AnzahlRunden'>" . $wertungsgruppen[4] . ": " . $EvatuationGroup4Count . "</a></p>";
				}
				if($TmpContentEntryList_EvaluationGroup5 != "") {
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td class='KopfZ1'>" . $wertungsgruppen[5] . "</td></tr></table><br>" . $TmpContentEntryList_EvaluationGroup5 . "<p class='AnzahlRunden'><a class='AnzahlRunden'>" . $wertungsgruppen[5] . ": " . $EvatuationGroup5Count . "</a></p>";
				}
				if($TmpContentEntryList_EvaluationGroup6 != "") {
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td class='KopfZ1'>" . $wertungsgruppen[6] . "</td></tr></table><br>" . $TmpContentEntryList_EvaluationGroup6 . "<p class='AnzahlRunden'><a class='AnzahlRunden'>" . $wertungsgruppen[6] . ": " . $EvatuationGroup6Count . "</a></p>";
				}
				if($TmpContentEntryList_EvaluationGroup7 != "") {
					$TmpContentEntryList = $TmpContentEntryList . "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td class='KopfZ1'>" . $wertungsgruppen[7] . "</td></tr></table><br>" . $TmpContentEntryList_EvaluationGroup7 . "<p class='AnzahlRunden'><a class='AnzahlRunden'>" . $wertungsgruppen[7] . ": " . $EvatuationGroup7Count . "</a></p>";
				}
				
				
				
				
				
				unset($TmpTeilnehmerRiege);
				$RiegeVorher = "";
				$Zeilenwechsler = 0;
				$Zeilenwechsler2 = 0;
				$ZeilenwechslerAthleteEG1 = 0;
				$ZeilenwechslerAthleteEG2 = 0;
				$ZeilenwechslerAthleteEG3 = 0;
				$ZeilenwechslerAthleteEG4 = 0;
				$ZeilenwechslerAthleteEG5 = 0;
				$ZeilenwechslerAthleteEG6 = 0;
				$ZeilenwechslerAthleteEG7 = 0;
			}
			
			# Daten in das Ausgabearray schreiben
			$TmpOutputEntryLists[] = array(	'Filename'	=>	$TmpFilenameEntryList,
											'Content'	=>	$TmpContentEntryList,
											'MD5Check'	=>	md5($TmpContentEntryList)
											);
			unset($TmpFilenameEntryList);
			unset($TmpContentEntryList);
			
			}
			} # Master
			
			# Ausgabe der Teilnehmerlisten-Dateien
			foreach ($TmpOutputEntryLists as $TmpOutputEntryListsFile) {
				if(file_exists($TmpOutputEntryListsFile['Filename']) == FALSE) {
					$EntyListFileForWrite = fopen($TmpOutputEntryListsFile['Filename'], 'w');
					fwrite($EntyListFileForWrite, $TmpOutputEntryListsFile['Content']);
				}
				else {
					if(md5_file($TmpOutputEntryListsFile['Filename']) != $TmpOutputEntryListsFile['MD5Check']) {
						$EntyListFileForWrite = fopen($TmpOutputEntryListsFile['Filename'], 'w');
						fwrite($EntyListFileForWrite, $TmpOutputEntryListsFile['Content']);
					}
				}
			
			}
			
	
	
}
?>