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
### LaIVE - Modul Pokalwertung (cupscoring.php) /LaIVE - Module Cup scoring (cupscoring.php)
### Erstellt von / Created by Kilian Wenzel
### Zuletzt geändert: / Last change: 0.12.0.2013-12-06

	
	# DBS Modus - Einlesen der Startklassen / IPC Mode - read start classes from file
if($IPCModeON == 1) {
	$DBSTextskl_entrylists = IPCClassesArray();
}


# PokalGrxx.c01
$Filename_PokalGrxx = "./" . "PokalGr" . $_GET['cupID'] . ".c01" ;

switch($_GET["list"]) {

case 1:	############################Normal Cup Scoring

if(file_exists($Filename_PokalGrxx)) {



$Cup_SumOfEvents = 0;

# PokalGrxx.c01 einlesen

	$PokalGrInhalt 		= 	file_get_contents($Filename_PokalGrxx);
	$PokalGrLaenge 		=	strlen($PokalGrInhalt);
	$PokalGrLaengeDS	=	829;
	$PokalGrAnzahlDS	=	($PokalGrLaenge / $PokalGrLaengeDS) - 1;
	
	$PokalGrDSZaehler	=	0;
	$PokalGrAbsolutePos	=	1;
	
	$Cup_ID						=	trim(substr($PokalGrInhalt, 0, 2));
	$Cup_Name					=	trim(substr($PokalGrInhalt, 3, 34));
	$Cup_SharePoints			=	trim(substr($PokalGrInhalt, 38, 1));
	$Cup_NumberScoresAthletes	=	trim(substr($PokalGrInhalt, 40, 1));
	$Cup_NumberPlacesAthletes	=	trim(substr($PokalGrInhalt, 41, 2));
	$Cup_NumberScoresRelays		=	trim(substr($PokalGrInhalt, 44, 1));
	$Cup_NumberPlacesRelays		=	trim(substr($PokalGrInhalt, 45, 2));
	
	
	# Points for singe events
	
	$Cup_Counter_PointsEventSingle = 1;
	$Cup_AbsPos_PointsEventSingle = 80;
	$Cup_PointsEventSingle_Array = array();
	
	while($Cup_Counter_PointsEventSingle < 37) {
	
		$Cup_PointsEventSingle_Array[$Cup_Counter_PointsEventSingle] = trim(substr($PokalGrInhalt, $Cup_AbsPos_PointsEventSingle - 1, 2));
	
		$Cup_AbsPos_PointsEventSingle = $Cup_AbsPos_PointsEventSingle + 3;
		$Cup_Counter_PointsEventSingle = $Cup_Counter_PointsEventSingle + 1;
	
	}
	
	# Points for relay events
	
	$Cup_Counter_PointsEventRelay = 1;
	$Cup_AbsPos_PointsEventRelay = 203;
	$Cup_PointsEventRelay_Array = array();
	
	while($Cup_Counter_PointsEventRelay < 37) {
	
		$Cup_PointsEventRelay_Array[$Cup_Counter_PointsEventRelay] = trim(substr($PokalGrInhalt, $Cup_AbsPos_PointsEventRelay - 1, 2));
	
		$Cup_AbsPos_PointsEventRelay = $Cup_AbsPos_PointsEventRelay + 3;
		$Cup_Counter_PointsEventRelay = $Cup_Counter_PointsEventRelay + 1;
	
	}
	
	
	# Teams in Cup
	
	$Cup_Counter_Teams = 1;
	$Cup_AbsPos_Teams = 380;
	$Cup_Teams_Array = array();
	
	while ($Cup_Counter_Teams < 51) {
		
		$Cup_Teams_Array["'" . trim(substr($PokalGrInhalt, $Cup_AbsPos_Teams - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_AbsPos_Teams + 2, 5)) . "'"] = 0;
	
		$Cup_AbsPos_Teams = $Cup_AbsPos_Teams + 8;
		$Cup_Counter_Teams = $Cup_Counter_Teams + 1;
	
	}
	
	
	
	
	#print($Cup_ID);
	#print($Cup_Name);
	#print_r($Cup_PointsEventSingle_Array);
	#print_r($Cup_PointsEventRelay_Array);
	#print_r($Cup_Teams_Array);
	#print("Anzahl Wettbewerbe: ". $PokalGrAnzahlDS);
	
	
	
	# Read Events out of Cup Scoring
	
	$Cup_Counter_Events = 1;
	$Cup_AbsPos_Events = 830;
	
	$Cup_Events_Array = array();
	
	while ($Cup_Counter_Events <  $PokalGrAnzahlDS + 1)  {
	
		# Events
		$Cup_Events_Array[$Cup_Counter_Events] = trim(substr($PokalGrInhalt, $Cup_AbsPos_Events + 4, 3));
		
		# Team scores by event
		$Cup_TeamscoresEvent_Counter = 1;
		$Cup_TeamscoresEvent_AbsPos = $Cup_AbsPos_Events + 42;
		
		while($Cup_TeamscoresEvent_Counter < 51) {
		
			#print(trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-");
			#print(trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)) . " / ");
			#print(trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 7, 6)) . "<br>");
		
			if (array_key_exists("'" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)) . "'", $Cup_Teams_Array)) {
				
				$Cup_Teams_Array["'" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)) . "'"] = $Cup_Teams_Array["'" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)) . "'"] + trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 7, 6));
			}
			
			if(trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) <> "") {
				$Cup_Array_Matrix[trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)) . "-" . trim(substr($PokalGrInhalt, $Cup_AbsPos_Events + 4, 3))] = array(	'TeamID' => trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)),
											'EventID'=> trim(substr($PokalGrInhalt, $Cup_AbsPos_Events + 4, 3)),
											'Points' => trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 7, 6))
										);
			}
		
			$Cup_TeamscoresEvent_AbsPos = $Cup_TeamscoresEvent_AbsPos + 14;
			$Cup_TeamscoresEvent_Counter++;
		}
		
		
		$Cup_AbsPos_Events = $Cup_AbsPos_Events + $PokalGrLaengeDS;
		$Cup_Counter_Events ++;
	
	}
	
	# Sorting Scores
	arsort ( $Cup_Teams_Array );
	
	#print_r($Cup_Events_Array);
	#print_r($Cup_Teams_Array);
	#print_r($Cup_Array_Matrix);
	

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
				$Verein["'" . trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3))."-".trim(substr($VereinInhalt, $VereinAbsolutePosition + 2, 5)) . "'"] = array(	'LV'		=>	trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3)),
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
		
		if(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 46, 2)) == ($_GET['cupID'] * 1) ) {
		
			$Cup_SumOfEvents = $Cup_SumOfEvents + 1;
			
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

# Create Output for Cup Scoring

	$Cup_Points_Before 	= 999999999999;
	$Cup_Place_Before	= 0;
	$Cup_Place_ContiniousCount = 0;

foreach($Cup_Teams_Array as $Cup_Array_Teams_Array_Key => $Cup_Array_Teams_Array_Value) {

	# Calculating Place
	if($Cup_Array_Teams_Array_Value == 0) {
		$CupTmpPlace = "";
	}
	
	else {
		if($Cup_Array_Teams_Array_Value == $Cup_Points_Before) {
			$CupTmpPlace = $Cup_Place_Before;
			$Cup_Place_ContiniousCount++;
		}
		
		else {
			$CupTmpPlace = $Cup_Place_ContiniousCount + 1;
			$Cup_Place_ContiniousCount++;
			$Cup_Place_Before++;
		}
	
	}
	
	$Cup_Points_Before 	= $Cup_Array_Teams_Array_Value;

	if($Verein[$Cup_Array_Teams_Array_Key]['VereinBez'] <> "") {

		# Output for detailed scorings by events
		$TmpArrayEventPointsToEvent = array();
	
	
		# New output scoring - with events with zero points
		
		
		
		foreach($Cup_Events_Array as $Cup_Events_ArrayKey => $Cup_Events_ArrayItem) {
		
		#if (array_key_exists(str_replace("'", "", $Cup_Array_Teams_Array_Key) . "-" . $Cup_Events_ArrayItem, $Cup_Array_Matrix) == FALSE) {
		#					$TmpArrayEventPointsToEvent[$Cup_Array_MatrixItem['EventID']] =  0;
		#				}
		$TmpArrayEventPointsToEvent[$Cup_Events_ArrayItem] =  0;
			
			foreach($Cup_Array_Matrix as $Cup_Array_MatrixItem) {
				
				if("'" . $Cup_Array_MatrixItem['TeamID'] . "'"  == $Cup_Array_Teams_Array_Key) {
					if($Cup_Array_MatrixItem['EventID'] == $Cup_Events_ArrayItem) {
						$TmpArrayEventPointsToEvent[$Cup_Array_MatrixItem['EventID']] =  $Cup_Array_MatrixItem['Points'];
						#break;
					}
					
						
					
				}
			
			}
			
		
		}
		
	
	
		/*foreach($Cup_Events_Array as $Cup_Events_ArrayItem) {

			foreach($Cup_Array_Matrix as $MatrixItem) {
				if(!array_key_exists($Cup_Events_ArrayItem, $TmpArrayEventPointsToEvent)) {
					if("'" . $MatrixItem['TeamID'] . "'"  == $Cup_Array_Teams_Array_Key && $MatrixItem['EventID'] == $Cup_Events_ArrayItem) {
						$TmpArrayEventPointsToEvent[$MatrixItem['EventID']] =  $MatrixItem['Points'];
					}
				
					else {
						$TmpArrayEventPointsToEvent[$MatrixItem['EventID']] =  0;
					}
				}
			}
		}*/
	
		#print_r($TmpArrayEventPointsToEvent);
		#print("<br>");
		
		$Cup_Scores_Out[] = array(	'Place'	=>	$CupTmpPlace,
								'Team'	=>	$Verein[$Cup_Array_Teams_Array_Key]['VereinBez'],
								'NAT'	=>	$Verein[$Cup_Array_Teams_Array_Key]['LV'],
								'Points'=>	$Cup_Array_Teams_Array_Value,
								'PointsByEvent' => $TmpArrayEventPointsToEvent
								);
	}
	unset($TmpArrayEventPointsToEvent);
}

#print_r($Cup_Scores_Out);


# Check for Files PokalGrxx.c01

$PokalGrFilesCounter = 1;
$PokalGrFileArray = array();

while($PokalGrFilesCounter < 100) {

if ($PokalGrFilesCounter < 10) {

	$PokalGrFilesCounterTmp = "0" . $PokalGrFilesCounter;

}
else {

	$PokalGrFilesCounterTmp = $PokalGrFilesCounter;

}

	if(file_exists("./" . "PokalGr" . $PokalGrFilesCounterTmp . ".c01")) {
	
		$PokalGrFileInhalt 		= 	file_get_contents("./" . "PokalGr" . $PokalGrFilesCounterTmp . ".c01");
		$PokalGrFileArray[$PokalGrFilesCounter] = trim(substr($PokalGrFileInhalt, 3, 34));
	
	}


$PokalGrFilesCounter++;
}

#print_r($PokalGrFileArray);

?>
<table class="laivemenu">
	<tr>
		
		<td class="linkliste">
			<ul class="secoundmenu">
				<?php 
						
						foreach($PokalGrFileArray as $PokalGrFileArrayKey => $PokalGrFileArrayItem) {
						
							
						
							if($PokalGrFileArrayKey < 10) {
								$PokalGrFileArrayKeyTmp = "0" . $PokalGrFileArrayKey;
								
							}
							else {
								$PokalGrFileArrayKeyTmp = $PokalGrFileArrayKey;
							}
						
							echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=cupscoring.php&amp;list=1&amp;cupID=" . $PokalGrFileArrayKeyTmp . "'>" . $PokalGrFileArrayItem . "</a>
							</li>";
						
						}
						
						if(file_exists("laive_combinedcupscoring.txt")) {
									
						$CC_file_content = file("./laive_combinedcupscoring.txt");
							foreach($CC_file_content AS $CC_file_content_Line) {
								$CC_file_content_Line_Explode = explode(";", $CC_file_content_Line);
								echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=cupscoring.php&amp;list=2&amp;ccupID=" . $CC_file_content_Line_Explode[0] . "'>" . $CC_file_content_Line_Explode[1] . "</a>
							</li>";
							}
						}
						
						
						?>

			</ul>
		</td>
		<td class="aktualisiert" align="right"><a class="aktualisiert"><?php echo $TxtSubMenuUpdated . " ".date("d.m.y H:i", filemtime($Filename_PokalGrxx)) ; ?></a></td>
	</tr>
</table>

		<table class="body" cellspacing="0">
			<tr><td class="KopfZ1"><?php echo $Kopfzeile1; ?></td></tr>
			<tr><td class="KopfZ11"><?php echo $Kopfzeile2; ?></td></tr>
			<tr><td class="KopfZ12"><?php echo $Kopfzeile3; ?></td></tr>
			</table>



		<table class="body" cellspacing="0">
		<tr><td class="KopfZ21"><?php echo $txt_headline_cupscoring . ": " . $Cup_Name; ?></td></tr>
		</table>
		
		
<?php
	print("<table class='body'><tr><td class='KopfZ2'>" . $txt_cup_standingafter_1 . " " . $PokalGrAnzahlDS . " " . $txt_cup_standingafter_2 . " " . $Cup_SumOfEvents . " " . $txt_cup_standingafter_3 . ":</td></tr></table>");

	echo "<table CLASS='body' cellspacing='0' cellpadding='0'>";
			
				echo "<tr>";
					echo"<td CLASS='cuptableheadplace'>$cup_tablehead_place</td>";
					echo "<td CLASS='cuptableheadteam'>$cup_tablehead_team</td>";
					echo "<td CLASS='cuptableheadpoints'>$cup_tablehead_points</td>";
				echo "</tr>";
			
	$ColorCupOutput = 1;
	
	foreach($Cup_Scores_Out as $OutItem) {
	
	if($OutItem['Place'] <> "") {$cupTmpPlaceafer =  $cup_afterplace;}
	else {$cupTmpPlaceafer =  "";}
	
		echo "<tr>";
			echo "<td class='cuptableplace" . $ColorCupOutput . "'>" . $OutItem['Place'] . $cupTmpPlaceafer . "</td>";
			
			echo "<td class='cuptableteam" . $ColorCupOutput . "'>";
			if($FlagsOn == 1) {print("<img src='" .  $PathToFlags . $OutItem['NAT'] . $FileFormatFlags . "' alt='".$OutItem['NAT']."' class='imgflags'>");}
			echo $OutItem['Team'] . "</td>";
			
			echo "<td class='cuptablepoints" . $ColorCupOutput . "'>" . $OutItem['Points'] . "</td>";
		echo "</tr>";
		
		switch($ColorCupOutput) {
		
		case 1:
			$ColorCupOutput = 0;
		break;
		
		case 0:
			$ColorCupOutput = 1;
		break;
		
		
		}
	
	}
			
			
			
			
			
			echo "</table>";

	

		print("<table class='body'><tr><td class='KopfZ2'>" . $txt_cup_detailedScoring .  ":</td></tr></table>");
		
		$WidthEvent = 520 / $PokalGrAnzahlDS;
		
		echo "<table CLASS='body' cellspacing='0' cellpadding='0'>";
			
				echo "<tr>";
					echo"<td CLASS='cuptableheaddetailedteam'>$cup_tablehead_team</td>";
					
					$CounterDetailHead = 1;
					while($CounterDetailHead < ($PokalGrAnzahlDS + 1)) {
						echo "<td CLASS='cuptableheaddetailedevent' width='". $WidthEvent ."'>" . "<abbr title='" . $WettbewE[$Cup_Events_Array[$CounterDetailHead]]['WettbewBez'] . "'>" . $CounterDetailHead . ")</abbr></td>";
						$CounterDetailHead++;
					}
					
					echo "<td CLASS='cuptableheaddetailedpoints'>$cup_tablehead_points</td>";
					echo "<td CLASS='cuptableheaddetailedplace'>$cup_tablehead_place</td>";
					
				echo "</tr>";
				
				$ColorCupOutput = 1;
				
				foreach($Cup_Scores_Out as $OutItem2) {
				
				if($OutItem2['Place'] <> "") {$cupTmpPlaceafer2 =  $cup_afterplace;}
					else {$cupTmpPlaceafer2 =  "";}
				
					echo "<tr>";
						echo "<td class='cuptabledetailteam" . $ColorCupOutput . "'>";

						if($FlagsOn == 1) {print("<img src='" .  $PathToFlags . $OutItem2['NAT'] . $FileFormatFlags . "' alt='".$OutItem2['NAT']."' class='imgflags'>");}
						
						echo $OutItem2['Team']  . "</td>";
						
						foreach($OutItem2['PointsByEvent'] as $OutPointsEvent) {
						
							echo "<td class='cuptabledetailevent" . $ColorCupOutput . "' width='". $WidthEvent ."'>" . $OutPointsEvent * 1 . "</td>";
						
						}
						
						echo "<td class='cuptabledetailpoints" . $ColorCupOutput . "'>" . $OutItem2['Points'] . "</td>";
						echo "<td class='cuptabledetailplace" . $ColorCupOutput . "'>" . $OutItem2['Place'] . $cupTmpPlaceafer2 ."</td>";
					echo "</tr>";
					
					switch($ColorCupOutput) {
		
						case 1:
							$ColorCupOutput = 0;
							break;
		
						case 0:
							$ColorCupOutput = 1;
							break;
		
		
					}
				
				}
				
				print("<table class='body'><tr><td class='KopfZ2'>" . $txt_cup_includedevents .  ":</td></tr></table>");
	
	$AnzahlInEventliste = count($Cup_Events_Array);
	$AnzahlEineSpalte = ceil($AnzahlInEventliste / 3);
	$Eventzaehler = 0;
	
		echo "<table class=''body'>";
		
		if($AnzahlInEventliste > 1) {
		
			echo "<tr><td class='blGrundLink'>";
			foreach($Cup_Events_Array as $CupEventKey => $CupEventItem) {
			
				if($Eventzaehler == $AnzahlEineSpalte || $Eventzaehler == $AnzahlEineSpalte * 2) {
				
					echo "</td>";
					echo "<td class='blGrundLink'>";
				}
				
				echo $CupEventKey . ") ";
				echo $WettbewE[$CupEventItem]['WettbewBez'];
				echo "<br>";
				
				if($Eventzaehler == $AnzahlInEventliste) {
				
					echo "</td>";

				}
				
				$Eventzaehler++;
			
			}
			echo "</tr>";
		
		}
		else {
		
			foreach($Cup_Events_Array as $CupEventKey => $CupEventItem) {
	
				echo "<tr><td class='blGrund'>";
				echo $CupEventKey . ") ";
				echo $WettbewE[$CupEventItem]['WettbewBez'];
				echo "</td></tr>";

			}
		}
		
		echo "</table>";
		echo "<br>";
		
		
		
	
		
			
		


}# Ende PokalGrxx existiert

break;

	case 2:	# List 2 Combined Scoring ##############################################################################################################################
		$Cup_Combined_Cups = array();
		$CombCup_Cups = array();
	
		if(file_exists("./laive_combinedcupscoring.txt")) {
		
			# laive_combinedcupscoring.txt reading
			$CombCup_file = "./laive_combinedcupscoring.txt";			
			$CombCup_fileContent = file($CombCup_file);
			
			#print_r($CombCup_fileContent);
			
			foreach($CombCup_fileContent AS $CombCup_fileContentLine) {
				#echo $meine_datei."<br>";
				$ExplodeArray = explode(";", $CombCup_fileContentLine);
				
				if($ExplodeArray[0] == $_GET['ccupID']) {
				
					$CombCup_ID = $ExplodeArray[0];
					$CombCup_Name = $ExplodeArray[1];
					$CombCup_Cups = explode(",", $ExplodeArray[2]);
					
					
					
				}
				
				# Create Array with Cups inside
					Foreach($CombCup_Cups as $CombCup_Cups_Value) {
					$Cup_Combined_Cups[$CombCup_Cups_Value] = array	(
																	'CupID' 			=> 	$CombCup_Cups_Value,
																	'Name'				=>	'',
																	'EventsTotal'		=> 	0,
																	'EventsFinished'	=>	0
																);
					}
					#print_r($Cup_Combined_Cups);
				
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
				$Verein["'" . trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3))."-".trim(substr($VereinInhalt, $VereinAbsolutePosition + 2, 5)) . "'"] = array(	'LV'		=>	trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3)),
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
							'Aktiv'		=>	$TmpWettbewAktiv,
							'CupID'		=>	str_pad(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 46, 2)), 2, "0", STR_PAD_LEFT)
							
		);			
	
		$WettbewAbsolutePositionDS = $WettbewAbsolutePositionDS + $WettbewLaengeDatensatz;
	
	}
	
	
} # Ende Wettbew.c01
			
			#print_r($WettbewE);
			
# Add Events to Cups
foreach($WettbewE as $WettbewE_Value) {
	
		If(array_key_exists($WettbewE_Value['CupID'], $Cup_Combined_Cups)) {
			$Cup_Combined_Cups[$WettbewE_Value['CupID']]['EventsTotal'] = $Cup_Combined_Cups[$WettbewE_Value['CupID']]['EventsTotal'] + 1;
		}
}	
#print_r($Cup_Combined_Cups);		
			

			#print($CombCup_ID);
			#print($CombCup_Name);
			#print_r($CombCup_Cups);
			
			
			###### Read PokalGrxx.c01 ######
			
			$CombCup_Out = array();
			
			
			foreach($CombCup_Cups as $CombCup_CupsItem) { # each cup scoring file
			
			
				if(file_exists("./PokalGr" . $CombCup_CupsItem . ".c01")) { # file exists
				
					$Cup_SumOfEvents = 0;

					# PokalGrxx.c01 einlesen
					$PokalGrInhalt 		= 	file_get_contents("./PokalGr" . $CombCup_CupsItem . ".c01");
					$PokalGrLaenge 		=	strlen($PokalGrInhalt);
					$PokalGrLaengeDS	=	829;
					$PokalGrAnzahlDS	=	($PokalGrLaenge / $PokalGrLaengeDS) - 1;
	
					$PokalGrDSZaehler	=	0;
					$PokalGrAbsolutePos	=	1;
	
					$Cup_ID						=	trim(substr($PokalGrInhalt, 0, 2));
					$Cup_Name					=	trim(substr($PokalGrInhalt, 3, 34));
					$Cup_SharePoints			=	trim(substr($PokalGrInhalt, 38, 1));
					$Cup_NumberScoresAthletes	=	trim(substr($PokalGrInhalt, 40, 1));
					$Cup_NumberPlacesAthletes	=	trim(substr($PokalGrInhalt, 41, 2));
					$Cup_NumberScoresRelays		=	trim(substr($PokalGrInhalt, 44, 1));
					$Cup_NumberPlacesRelays		=	trim(substr($PokalGrInhalt, 45, 2));
	
					# Add Name to Cups Array
					$Cup_Combined_Cups[$Cup_ID]['Name']	= $Cup_Name;
	
					# Points for singe events
					$Cup_Counter_PointsEventSingle = 1;
					$Cup_AbsPos_PointsEventSingle = 80;
					$Cup_PointsEventSingle_Array = array();
	
					while($Cup_Counter_PointsEventSingle < 37) {
						$Cup_PointsEventSingle_Array[$Cup_Counter_PointsEventSingle] = trim(substr($PokalGrInhalt, $Cup_AbsPos_PointsEventSingle - 1, 2));
						$Cup_AbsPos_PointsEventSingle = $Cup_AbsPos_PointsEventSingle + 3;
						$Cup_Counter_PointsEventSingle = $Cup_Counter_PointsEventSingle + 1;
					}
	
					# Points for relay events
					$Cup_Counter_PointsEventRelay = 1;
					$Cup_AbsPos_PointsEventRelay = 203;
					$Cup_PointsEventRelay_Array = array();
	
					while($Cup_Counter_PointsEventRelay < 37) {
						$Cup_PointsEventRelay_Array[$Cup_Counter_PointsEventRelay] = trim(substr($PokalGrInhalt, $Cup_AbsPos_PointsEventRelay - 1, 2));
						$Cup_AbsPos_PointsEventRelay = $Cup_AbsPos_PointsEventRelay + 3;
						$Cup_Counter_PointsEventRelay = $Cup_Counter_PointsEventRelay + 1;
					}
	
					# Teams in Cup
	
					$Cup_Counter_Teams = 1;
					$Cup_AbsPos_Teams = 380;
					$Cup_Teams_Array = array();
	
					while ($Cup_Counter_Teams < 51) {
						$Cup_Teams_Array["'" . trim(substr($PokalGrInhalt, $Cup_AbsPos_Teams - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_AbsPos_Teams + 2, 5)) . "'"] = 0;
						$Cup_AbsPos_Teams = $Cup_AbsPos_Teams + 8;
						$Cup_Counter_Teams = $Cup_Counter_Teams + 1;
					}
		
					# Read Events out of Cup Scoring
					$Cup_Counter_Events = 1;
					$Cup_AbsPos_Events = 830;
					$Cup_Events_Array = array();
	
					while ($Cup_Counter_Events <  $PokalGrAnzahlDS + 1)  {
	
						# Events
						$Cup_Events_Array[$Cup_Counter_Events] = trim(substr($PokalGrInhalt, $Cup_AbsPos_Events + 4, 3));
		
						# Team scores by event
						$Cup_TeamscoresEvent_Counter = 1;
						$Cup_TeamscoresEvent_AbsPos = $Cup_AbsPos_Events + 42;
		
							while($Cup_TeamscoresEvent_Counter < 51) {
		
								if (array_key_exists("'" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)) . "'", $Cup_Teams_Array)) {
									$Cup_Teams_Array["'" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)) . "'"] = $Cup_Teams_Array["'" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)) . "'"] + trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 7, 6));
								}
			
								if(trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) <> "") {
									$Cup_Array_Matrix[trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)) . "-" . trim(substr($PokalGrInhalt, $Cup_AbsPos_Events + 4, 3))] = array(	'TeamID' => trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos - 1, 3)) . "-" . trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 2, 5)),
											'EventID'=> trim(substr($PokalGrInhalt, $Cup_AbsPos_Events + 4, 3)),
											'Points' => trim(substr($PokalGrInhalt, $Cup_TeamscoresEvent_AbsPos + 7, 6))
										);
								}
								$Cup_TeamscoresEvent_AbsPos = $Cup_TeamscoresEvent_AbsPos + 14;
								$Cup_TeamscoresEvent_Counter++;
							}
		
						$Cup_AbsPos_Events = $Cup_AbsPos_Events + $PokalGrLaengeDS;
						$Cup_Counter_Events ++;
					}
	
					# Sorting Scores
					arsort ( $Cup_Teams_Array );
				} # file exists end
			
	
			# Create Output for Cup Scoring

			$Cup_Points_Before 	= 999999999999;
			$Cup_Place_Before	= 0;
			$Cup_Place_ContiniousCount = 0;

			foreach($Cup_Teams_Array as $Cup_Array_Teams_Array_Key => $Cup_Array_Teams_Array_Value) {
				
				if($Cup_Array_Teams_Array_Value == 0) {
					$CupTmpPlace = "";
				}
				else {
					if($Cup_Array_Teams_Array_Value == $Cup_Points_Before) {
						$CupTmpPlace = $Cup_Place_Before;
						$Cup_Place_ContiniousCount++;
					}
					else {
						$CupTmpPlace = $Cup_Place_ContiniousCount + 1;
						$Cup_Place_ContiniousCount++;
						$Cup_Place_Before++;
					}
				}
	
				$Cup_Points_Before 	= $Cup_Array_Teams_Array_Value;

				if($Verein[$Cup_Array_Teams_Array_Key]['VereinBez'] <> "") {

					# Output for detailed scorings by events
					$TmpArrayEventPointsToEvent = array();
	
					# New output scoring - with events with zero points
					foreach($Cup_Events_Array as $Cup_Events_ArrayKey => $Cup_Events_ArrayItem) {
		
						$TmpArrayEventPointsToEvent[$Cup_Events_ArrayItem] =  0;
			
						foreach($Cup_Array_Matrix as $Cup_Array_MatrixItem) {
				
							if("'" . $Cup_Array_MatrixItem['TeamID'] . "'"  == $Cup_Array_Teams_Array_Key) {
								if($Cup_Array_MatrixItem['EventID'] == $Cup_Events_ArrayItem) {
									$TmpArrayEventPointsToEvent[$Cup_Array_MatrixItem['EventID']] =  $Cup_Array_MatrixItem['Points'];
								}
							}
						}
					}
		
					$Cup_Scores_Out_Combined[str_replace("'", "", $Cup_Array_Teams_Array_Key) . "-" .$CombCup_CupsItem] = array(	'Place'	=>	$CupTmpPlace,
								'Team'	=>	$Verein[$Cup_Array_Teams_Array_Key]['VereinBez'],
								'NAT'	=>	$Verein[$Cup_Array_Teams_Array_Key]['LV'],
								'Points'=>	$Cup_Array_Teams_Array_Value,
								'PointsByEvent' => $TmpArrayEventPointsToEvent,
								'TeamID'	=> str_replace("'", "", $Cup_Array_Teams_Array_Key),
								'CupID'		=> $CombCup_CupsItem
								);
								
					$Cup_Combined_Cups[$CombCup_CupsItem]['EventsFinished'] = count($TmpArrayEventPointsToEvent);
								
				}
				unset($TmpArrayEventPointsToEvent);
			}
			
			

			# Check for Files PokalGrxx.c01

			$PokalGrFilesCounter = 1;
			$PokalGrFileArray = array();

			while($PokalGrFilesCounter < 100) {

				if ($PokalGrFilesCounter < 10) {

					$PokalGrFilesCounterTmp = "0" . $PokalGrFilesCounter;

				}
				else {

					$PokalGrFilesCounterTmp = $PokalGrFilesCounter;

				}

				if(file_exists("./" . "PokalGr" . $PokalGrFilesCounterTmp . ".c01")) {
	
					$PokalGrFileInhalt 		= 	file_get_contents("./" . "PokalGr" . $PokalGrFilesCounterTmp . ".c01");
					$PokalGrFileArray[$PokalGrFilesCounter] = trim(substr($PokalGrFileInhalt, 3, 34));
	
				}


				$PokalGrFilesCounter++;
			}


		
} # end each cup scoring file
#print_r($Cup_Scores_Out_Combined);

foreach($Cup_Combined_Cups as $Cup_Combined_Cups_Key => $Cup_Combined_Cups_Value) {
		$TmpArrayPointsPlacesToCups[$Cup_Combined_Cups_Key]['CupPlace'] =  ""; #### beobachten
		$TmpArrayPointsPlacesToCups[$Cup_Combined_Cups_Key]['CupPoints'] =  0; #### beobachten
	}

# Create Output Array
foreach($Cup_Scores_Out_Combined as $Cup_Scores_Out_Combined_Item) {

	

	# create Item in array
	if(!array_key_exists($Cup_Scores_Out_Combined_Item['TeamID'] , $Cup_Combined_Out)) {
		$Cup_Combined_Out[$Cup_Scores_Out_Combined_Item['TeamID']] = array (
																				'Place' 		=>	0,
																				'Team'			=>	$Cup_Scores_Out_Combined_Item['Team'],
																				'NAT'			=>	$Cup_Scores_Out_Combined_Item['NAT'],
																				'Points'		=>	0,
																				'PointsByCups'	=>	$TmpArrayPointsPlacesToCups #### beobachten
																			);
	}
	
	# Points and Places by Cups
	$TmpPointsPlacesByCups[$Cup_Scores_Out_Combined_Item['CupID']] = array	(
																				'CupPlace'		=>	$Cup_Scores_Out_Combined_Item['Place'],
																				'CupPoints'		=>	$Cup_Scores_Out_Combined_Item['Points']
																			);
	$Cup_Combined_Out[$Cup_Scores_Out_Combined_Item['TeamID']]['PointsByCups'][$Cup_Scores_Out_Combined_Item['CupID']] = $TmpPointsPlacesByCups[$Cup_Scores_Out_Combined_Item['CupID']];
	unset($TmpPointsPlacesByCups);
	
	#Calculate Points
	$Cup_Combined_Out[$Cup_Scores_Out_Combined_Item['TeamID']]['Points'] = $Cup_Combined_Out[$Cup_Scores_Out_Combined_Item['TeamID']]['Points'] + $Cup_Scores_Out_Combined_Item['Points'];

}

# Sorting Output
foreach ($Cup_Combined_Out as $nr => $inhalt) {

	$CCPlace[$nr] = strtolower($inhalt['Place']);
	$CCTeam[$nr] = strtolower($inhalt['Team']);
	$CCNAT[$nr] = strtolower($inhalt['NAT']);
	$CCPoints[$nr] = strtolower($inhalt['Points']);
	$CCPointsByCups[$nr] = strtolower($inhalt['PointsByCups']);
	
}
array_multisort($CCPoints, SORT_DESC, $Cup_Combined_Out);

# Calculation Places
$CCup_Points_Before 		= 999999999999;
$CCup_Place_Before			= 0;
$CCup_Place_ContiniousCount = 0;

foreach($Cup_Combined_Out as $Cup_Combined_Out_Key => $Cup_Combined_Out_Value) {
				
	if($Cup_Combined_Out_Value['Points'] == 0) {
		$CCupTmpPlace = "";
	}
	else {
		if($Cup_Combined_Out_Value['Points'] == $CCup_Points_Before) {
			$CCupTmpPlace = $CCup_Place_Before;
			$CCup_Place_ContiniousCount++;
		}
		else {
			$CCupTmpPlace = $CCup_Place_ContiniousCount + 1;
			$CCup_Place_ContiniousCount++;
			$CCup_Place_Before++;
		}
	}
	
	$Cup_Combined_Out[$Cup_Combined_Out_Key]['Place'] = $CCupTmpPlace;
	
	$CCup_Points_Before 	= $Cup_Combined_Out_Value['Points'];

}

# Count Events from all cups
$CC_Count_AllEvents = 0;
$CC_Count_FinishedEvents = 0;
foreach($Cup_Combined_Cups as $Cup_Combined_Cups_Value) {
	$CC_Count_AllEvents = $CC_Count_AllEvents + $Cup_Combined_Cups_Value['EventsTotal'];
	$CC_Count_FinishedEvents = $CC_Count_FinishedEvents + $Cup_Combined_Cups_Value['EventsFinished'];
}

# Output Combined Cup


?>
<table class="laivemenu">
	<tr>
		
		<td class="linkliste">
			<ul class="secoundmenu">
				<?php 
						$CC_Filetimes = array();
						foreach($PokalGrFileArray as $PokalGrFileArrayKey => $PokalGrFileArrayItem) {
						
							
						
							if($PokalGrFileArrayKey < 10) {
								$PokalGrFileArrayKeyTmp = "0" . $PokalGrFileArrayKey;
								
							}
							else {
								$PokalGrFileArrayKeyTmp = $PokalGrFileArrayKey;
							}
						
							echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=cupscoring.php&amp;list=1&amp;cupID=" . $PokalGrFileArrayKeyTmp . "'>" . $PokalGrFileArrayItem . "</a>
							</li>";
							$CC_Filetimes[] = filemtime("./" . "PokalGr" . $PokalGrFileArrayKeyTmp . ".c01");
						
						}
						
						if(file_exists("laive_combinedcupscoring.txt")) {
									
						$CC_file_content = file("./laive_combinedcupscoring.txt");
							foreach($CC_file_content AS $CC_file_content_Line) {
								$CC_file_content_Line_Explode = explode(";", $CC_file_content_Line);
								echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=cupscoring.php&amp;list=2&amp;ccupID=" . $CC_file_content_Line_Explode[0] . "'>" . $CC_file_content_Line_Explode[1] . "</a>
							</li>";
							$CC_Filetimes[] = filemtime("./laive_combinedcupscoring.txt");
							}
						}
						
						
						
						
						?>

			</ul>
		</td>
		<td class="aktualisiert" align="right"><a class="aktualisiert"><?php echo $TxtSubMenuUpdated . " ".date("d.m.y H:i", max($CC_Filetimes)) ; ?></a></td>
	</tr>
</table>

		<table class="body" cellspacing="0">
			<tr><td class="KopfZ1"><?php echo $Kopfzeile1; ?></td></tr>
			<tr><td class="KopfZ11"><?php echo $Kopfzeile2; ?></td></tr>
			<tr><td class="KopfZ12"><?php echo $Kopfzeile3; ?></td></tr>
			</table>



		<table class="body" cellspacing="0">
		<tr><td class="KopfZ21"><?php echo $txt_headline_combined_cupscoring . ": " . $CombCup_Name; ?></td></tr>
		</table>
		
		
<?php

print("<table class='body'><tr><td class='KopfZ2'>" . $txt_cup_standingafter_1 . " " . $CC_Count_FinishedEvents . " " . $txt_cup_standingafter_2 . " " . $CC_Count_AllEvents . " " . $txt_cup_standingafter_3 . ":</td></tr></table>");

echo "<table CLASS='body' cellspacing='0' cellpadding='0'>";
			
	echo "<tr>";
		echo"<td CLASS='cuptableheadplace'>$cup_tablehead_place</td>";
		echo "<td CLASS='cuptableheadteam'>$cup_tablehead_team</td>";
		echo "<td CLASS='cuptableheadpoints'>$cup_tablehead_points</td>";
	echo "</tr>";
			
$ColorCupOutput = 1;
	
foreach($Cup_Combined_Out as $OutItem) {
	
	if($OutItem['Place'] <> "") {
		$cupTmpPlaceafer =  $cup_afterplace;
	}
	else {
		$cupTmpPlaceafer =  "";
	}
	
	echo "<tr>";
		echo "<td class='cuptableplace" . $ColorCupOutput . "'>" . $OutItem['Place'] . $cupTmpPlaceafer . "</td>";
		echo "<td class='cuptableteam" . $ColorCupOutput . "'>";
		if($FlagsOn == 1) {
			print("<img src='" .  $PathToFlags . $OutItem['NAT'] . $FileFormatFlags . "' alt='".$OutItem['NAT']."' class='imgflags'>");
		}
		echo $OutItem['Team'] . "</td>";
		echo "<td class='cuptablepoints" . $ColorCupOutput . "'>" . $OutItem['Points'] . "</td>";
		echo "</tr>";
		
		switch($ColorCupOutput) {
		
			case 1:
				$ColorCupOutput = 0;
			break;
		
			case 0:
				$ColorCupOutput = 1;
			break;
		}
	}
echo "</table>";

print("<table class='body'><tr><td class='KopfZ2'>" . $txt_combined_cup_detailedScoring .  ":</td></tr></table>");
		
$WidthEvent = 520 / count($Cup_Combined_Cups);
		
echo "<table CLASS='body' cellspacing='0' cellpadding='0'>";
	echo "<tr>";
		echo"<td CLASS='cuptableheaddetailedteam'>$cup_tablehead_team</td>";
		$CounterDetailHead = 1;
		foreach($Cup_Combined_Cups as $Cup_Combined_Cups_Value) {
			echo "<td CLASS='cuptableheaddetailedevent' width='". $WidthEvent ."'>" . "<abbr title='" . $Cup_Combined_Cups_Value['Name'] . "'>" . $CounterDetailHead . ")";
				if(count($Cup_Combined_Cups) < 4){
					echo " " . $Cup_Combined_Cups_Value['Name'];
				}
				echo" </abbr></td>";
			$CounterDetailHead++;
		}
		echo "<td CLASS='cuptableheaddetailedpoints'>$cup_tablehead_points</td>";
		echo "<td CLASS='cuptableheaddetailedplace'>$cup_tablehead_place</td>";
	echo "</tr>";
	$ColorCupOutput = 1;
	
	foreach($Cup_Combined_Out as $OutItem2) {
		if($OutItem2['Place'] <> "") {
			$cupTmpPlaceafer2 =  $cup_afterplace;
		}
		else {
			$cupTmpPlaceafer2 =  "";
		}
		echo "<tr>";
			echo "<td class='cuptabledetailteam" . $ColorCupOutput . "'>";
			if($FlagsOn == 1) {
				print("<img src='" .  $PathToFlags . $OutItem2['NAT'] . $FileFormatFlags . "' alt='".$OutItem2['NAT']."' class='imgflags'>");
			}
			echo $OutItem2['Team']  . "</td>";
			
			foreach($OutItem2['PointsByCups'] as $OutPointsByCups) {
				echo "<td class='cuptabledetailevent" . $ColorCupOutput . "' width='". $WidthEvent ."'>" . $OutPointsByCups['CupPoints'] * 1;
				if($OutPointsByCups['CupPlace'] !== "") {
					echo " (" . $OutPointsByCups['CupPlace'] * 1 . $cup_afterplace . ")";
				}
				echo "</td>";
			}
			echo "<td class='cuptabledetailpoints" . $ColorCupOutput . "'>" . $OutItem2['Points'] . "</td>";
			echo "<td class='cuptabledetailplace" . $ColorCupOutput . "'>" . $OutItem2['Place'] . $cupTmpPlaceafer2 ."</td>";
		echo "</tr>";
					
		switch($ColorCupOutput) {
		
			case 1:
				$ColorCupOutput = 0;
				break;		
			case 0:
				$ColorCupOutput = 1;
				break;
		}		
	}

print("<table class='body'><tr><td class='KopfZ2'>" . $txt_combined_cup_includedcups .  ":</td></tr></table>");
	
$AnzahlInEventliste = count($Cup_Combined_Cups);
$AnzahlEineSpalte = ceil($AnzahlInEventliste / 2);
$Eventzaehler = 0;
	
echo "<table class=''body'>";
	if($AnzahlInEventliste > 1) {
		echo "<tr><td class='blGrundLink'>";
			foreach($Cup_Combined_Cups as $CupEventKey => $CupEventItem) {
				if($Eventzaehler == $AnzahlEineSpalte || $Eventzaehler == $AnzahlEineSpalte * 2) {
					echo "</td>";
					echo "<td class='blGrundLink'>";
				}
				
				if(file_exists("./PokalGr" . $CupEventKey . ".c01")) {
					echo "<a href='?sub=cupscoring.php&amp;list=1&amp;cupID=" . $CupEventKey . "'>";
				}
				
				echo $Eventzaehler + 1 . ") ";
				echo $CupEventItem['Name'];
				
				if(file_exists("./PokalGr" . $CupEventKey . ".c01")) {
					echo "</a>";
				}
				
				echo "<br> (" .  $CupEventItem['EventsFinished'] . " " . $txt_cup_Combined_standingafter_1 . " " . $CupEventItem['EventsTotal'] . " " . $txt_cup_Combined_standingafter_2 . ")"; 
				echo "<br>";
				if($Eventzaehler == $AnzahlInEventliste) {
					echo "</td>";
				}
				$Eventzaehler++;
			}
			echo "</tr>";
		}
		else {
			foreach($Cup_Combined_Cups as $CupEventKey => $CupEventItem) {
				echo "<tr><td class='blGrund'>";
				if(file_exists("./PokalGr" . $CupEventKey . ".c01")) {
					echo "<a href='?sub=cupscoring.php&amp;list=1&amp;cupID=" . $CupEventKey . "'>";
				}
				
				echo $Eventzaehler + 1 . ") ";
				echo $CupEventItem['Name'];
				
				if(file_exists("./PokalGr" . $CupEventKey . ".c01")) {
					echo "</a>";
				}
				echo "<br> (" .  $CupEventItem['EventsFinished'] . " " . $txt_cup_Combined_standingafter_1 . " " . $CupEventItem['EventsTotal'] . " " . $txt_cup_Combined_standingafter_2 . ")"; 
				echo "</td></tr>";
				$Eventzaehler++;
			}
		}
		echo "</table>";
		echo "<br>";

		unset($Cup_Combined_Cups);
		
		# Create CSV File
		if($CCCSVFileON == 1 && $_GET['cccsv'] == 1) {
		
			$CCCSVFileName	= "Results_CombCup_" . $_GET['ccupID'] . "." . $CCCSVFile_Extention;
			$CCCSVFileHandler = fopen($CCCSVFileName,"w");
			fwrite($CCCSVFileHandler, $CCCSVFile_TxtIdentifier . 'Place' . $CCCSVFile_TxtIdentifier . $CCCSVFile_Seperator . $CCCSVFile_TxtIdentifier . 'Team' . $CCCSVFile_TxtIdentifier . $CCCSVFile_Seperator . $CCCSVFile_TxtIdentifier . 'NAT' . $CCCSVFile_TxtIdentifier . $CCCSVFile_Seperator . $CCCSVFile_TxtIdentifier . 'Points' . $CCCSVFile_TxtIdentifier . $CCCSVFile_Seperator . $CCCSVFile_TxtIdentifier . 'Combined Cup Name' . $CCCSVFile_TxtIdentifier . "\r\n");
			
			foreach($Cup_Combined_Out as $Cup_Combined_Out_Value) {
				fwrite($CCCSVFileHandler, $CCCSVFile_TxtIdentifier . $Cup_Combined_Out_Value['Place'] . $CCCSVFile_TxtIdentifier . $CCCSVFile_Seperator . $CCCSVFile_TxtIdentifier . $Cup_Combined_Out_Value['Team'] . $CCCSVFile_TxtIdentifier . $CCCSVFile_Seperator . $CCCSVFile_TxtIdentifier . $Cup_Combined_Out_Value['NAT'] . $CCCSVFile_TxtIdentifier . $CCCSVFile_Seperator . $CCCSVFile_TxtIdentifier . $Cup_Combined_Out_Value['Points'] . $CCCSVFile_TxtIdentifier . $CCCSVFile_Seperator . $CCCSVFile_TxtIdentifier . $CombCup_Name . $CCCSVFile_TxtIdentifier . "\r\n");
			}
			fclose($CCCSVFileHandler);
			if(file_exists($CCCSVFileName)) {
				echo "<p><a href='".$CCCSVFileName."' type='application/octet-stream'>" . "Download"  . " (" . $CCCSVFileName . ")"."</a></p>"; 
			}
		}
		
		
		}	# End laive_combinedcupscoring.txt exists
		
		
		
		else {
			echo "<p>" . $txt_cup_NoCombinedScoring . "</p>";
		}
	
	
	
	
	break;



} 
	

?>