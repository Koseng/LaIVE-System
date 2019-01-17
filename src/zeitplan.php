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

### LaIVE - Modul Zeitplan (zeitplan.php) /LaIVE - Module timetable (zeitplan.php)
### Erstellt von / Created by Kilian Wenzel
### Zuletzt geändert: / Last change: 0.12.0.2013-12-06

/**
 * array_unique über ein Element eines Array
 * @param     Array<Key => Array<Node>>   Ein mehrstufiger Array
 * @param     String
 * @param     Integer
 * @return    Array<Key => Array<Node>>  
 */
function array_unique_by_subitem($array, $key, $sort_flags  = SORT_STRING){
    $items = array();
    // Die Subeitems auslesen
    foreach($array as $index => $item) $items[$index] = $item[$key];
    //Die Subitems mit array_unique bearbeiten
    $uniqueItems = array_unique($items, $sort_flags);
    //Der eigentliche Array über den Key mit den selektierten Subitems abgleichen
    return array_intersect_key($array, $uniqueItems);
}




# DSB Mode - Startklassen einlesen / IPC Mode - Read start classes from file
if($IPCModeON == 1) {
	$DBSTextskl_timetable = IPCClassesArray();
}
#print_r($DBSTextskl_timetable);
				
$zeitplandatei = $dat_wettbew;				
				
				
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
		
		
		
		$Wettbew[trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3))] = array(	'WettbewNr'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3)),
							'WettbewBez'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 2, 32)),
							'StellplatzMin'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 71, 3)),
							'StellplatzZeit'	=>	uhrzeitformat(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 74, 5))),
							'VorlaufZeit'		=>	uhrzeitformat(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 79, 5))),
							'VorlaufTag'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 84, 1)),
							'ZwischenlaufZeit'	=>	uhrzeitformat(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 85, 5))),
							'ZwischenlaufTag'	=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 90, 1)),
							'FinaleZeit'		=>	uhrzeitformat(trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 91, 5))),
							'FinaleTag'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 96, 1)),
							'COSANrAK'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 36, 2)),
							'COSANrDIS'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 38, 3)),
							'COSANr'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 36, 5)),
							'DISBez'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 296, 32)),
							'AKBez'				=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 331, 24)),
							'WettbewTyp'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 41, 1))
							
		);			
	
		$WettbewAbsolutePositionDS = $WettbewAbsolutePositionDS + $WettbewLaengeDatensatz;
	
	}

		# Create File for original timetable (timetable changed)
if(file_exists("./laive_timetable_original.txt") == FALSE) {
	$file_tt_original = fopen("./laive_timetable_original.txt", "w");
	foreach($Wettbew as $EventsLine) {
		fwrite($file_tt_original, $EventsLine['WettbewNr'] . ";" . $EventsLine['VorlaufTag'] . ";" . $EventsLine['VorlaufZeit'] . ";" . $EventsLine['ZwischenlaufTag'] . ";" . $EventsLine['ZwischenlaufZeit'] . ";" . $EventsLine['FinaleTag'] . ";" . $EventsLine['FinaleZeit'] . "\n");
	}
	$fclose($file_tt_original);
}
	
	
	

}



# Endli.c01 einlesen und  verwenden
if(file_exists($dat_endli)) {

	#$EndliInhaltArray = file($dat_endli);
	
	$EndliInhalt = file_get_contents($dat_endli);
	
	#echo  $EndliInhalt;
	
	$EndliInhaltGesamtLaenge = strlen($EndliInhalt);
	
	#echo  $EndliInhaltGesamtLaenge;
	
	$EndliEinfacheLaenge = 234;
	
	$EndliMindestLaenge = 2 * $EndliEinfacheLaenge;
	
	$EndliInhaltZeichenZaehler = 1;
	
	while($EndliInhaltZeichenZaehler < $EndliInhaltGesamtLaenge) {
	
		$AnzahlLaengen = substr($EndliInhalt, $EndliInhaltZeichenZaehler + 29, 3);
		
		$ETmpWettbewNr = substr($EndliInhalt, $EndliInhaltZeichenZaehler -1, 3) * 1;
		$ETmpRundeTyp = substr($EndliInhalt, $EndliInhaltZeichenZaehler + 7, 1);
		$ETmpLaufGruppe = substr($EndliInhalt, $EndliInhaltZeichenZaehler + 8, 2);
		if(trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 10, 2)) == "") {
			$ETmpRiege = 0;
		}
		elseif(trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 10, 1)) == "p") {
			$ETmpRiege = ord(trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 11, 1)));
		}
		else {
			$ETmpRiege = trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 10, 2))*1;
		}
		
		$ETmpRundeBez = trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 34, 30));
		$ETmpMannschaftTN = substr($EndliInhalt, $EndliInhaltZeichenZaehler + 33, 1);
		$ETmpTeilnehmer = trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 28, 3)) - 1;
		if ($IPCModeON != 1) {
			$ETmpGemischt = substr($EndliInhalt, $EndliInhaltZeichenZaehler + 88, 1);
		}
		else {
			$ETmpGemischt = "";
		}
		$ETmpGemischtHinweis = trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 91, 3));
		$ETmpStartzeit = uhrzeitformat(trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 22, 5)));
		$ETmpStartdatum = trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 12, 10));
		$ETmpCOSANr = trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 2, 5));
		$ETmpWettbewTyp = $Wettbew[$ETmpWettbewNr]['WettbewTyp'];
		$ETmpErstelltDatumZeit = trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 71, 4)).trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 68, 2)).trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 65, 2)).trim(substr($EndliInhalt, $EndliInhaltZeichenZaehler + 75, 5));
		
		
	
		
							
							
		#echo $ETmpStartdatum."<br>";
		#echo $ETmpWettbewNr." / ".$ETmpRundeTyp." / ".$ETmpLaufGruppe." / "." / ".$ETmpRiege." / ".$ETmpRundeBez." / ".$ETmpMannschaftTN." / ".$ETmpTeilnehmer." / ".$ETmpGemischt." / ".$ETmpGemischtHinweis." / ".$ETmpStartzeit." / ".$ETmpCOSANr." / ".$ETmpWettbewTyp."<br>";
		
		
		
		
		#In Array und dabei die gemischten Wettbewerbe ausschließen
		if(($ETmpGemischt == "v" && empty($ETmpGemischtHinweis) == false) || ($ETmpGemischt !== "v" )) {
		
		if($ETmpRundeTyp !== "f" && $ETmpRundeTyp !== "h" && $ETmpRundeTyp !== "j") {
		
			$TmpEndli[] = array (	'WettbewNr'		=>	$ETmpWettbewNr,
									'RundeTyp'		=>	$ETmpRundeTyp,
									'LaufGruppe'	=>	$ETmpLaufGruppe,
									'Riege'			=>	$ETmpRiege,
									'RundeBez'		=>	$ETmpRundeBez,
									'MannschaftTN'	=>	$ETmpMannschaftTN,
									'Teilnehmer'	=>	$ETmpTeilnehmer,
									'Startzeit'		=>	$ETmpStartzeit,
									'Startdatum'	=>	$ETmpStartdatum,
									'COSANr'		=>	$ETmpCOSANr,
									'WettbewTyp'	=>	$ETmpWettbewTyp,
									'Gemischt'	=>	$ETmpGemischt,
									'ErstelltDatumZeit'	=>	$ETmpErstelltDatumZeit,
									'DatensatzID'	=>  $ETmpWettbewNr."-".$ETmpCOSANr."-".$ETmpRundeTyp."-".$ETmpRiege."-".$ETmpLaufGruppe,
									'DatensatzIDmitZeit'	=>  $ETmpWettbewNr."-".$ETmpCOSANr."-".$ETmpRundeTyp."-".$ETmpRiege."-".$ETmpLaufGruppe."-".$ETmpErstelltDatumZeit
			
			);
			
		}
		}
		
		#Wettbewerbsnummernarray
		$EndliWettbewNr[] = $ETmpWettbewNr;
		
	$EndliInhaltZeichenZaehler = $EndliInhaltZeichenZaehler + ($AnzahlLaengen * $EndliEinfacheLaenge);
	}
	
	
	foreach ($TmpEndli as $nr => $inhalt) {

	$CWettbewerbNr[$nr] = strtolower($inhalt['WettbewNr']);
	$CRundeTyp[$nr] = strtolower($inhalt['RundeTyp']);
	$CLaufGruppe[$nr] = strtolower($inhalt['LaufGruppe']);
	$CRiege[$nr] = strtolower($inhalt['Riege']);
	$CRundeBez[$nr] = strtolower($inhalt['RundeBez']);
	$CMannschaftTN[$nr] = strtolower($inhalt['MannschaftTN']);
	$CTeilnehmer[$nr] = strtolower($inhalt['Teilnehmer']);
	$CStartzeit[$nr] = strtolower($inhalt['Startzeit']);
	$CStartdatum[$nr] = strtolower($inhalt['Startdatum']);
	$CCOSANr[$nr] = strtolower($inhalt['COSANr']);
	$CWettbewTyp[$nr] = strtolower($inhalt['WettbewTyp']);
	$CGemischt[$nr] = strtolower($inhalt['Gemischt']);
	$CErstelltDatumZeit[$nr] = strtolower($inhalt['ErstelltDatumZeit']);
	$CDatensatzID[$nr] = strtolower($inhalt['DatensatzID']);
	$CDatensatzIDmitZeit[$nr] = strtolower($inhalt['DatensatzIDmitZeit']);

}

array_multisort($CDatensatzIDmitZeit, SORT_DESC, $TmpEndli);

	
	
	
	# Doppelte Datensätze entfernen in Array
	$Endli = array_unique_by_subitem($TmpEndli, 'DatensatzID');
	
	
	
	
	foreach ($Endli as $nr => $inhalt) {

	$C2WettbewerbNr[$nr] = strtolower($inhalt['WettbewNr']);
	$C2RundeTyp[$nr] = strtolower($inhalt['RundeTyp']);
	$C2LaufGruppe[$nr] = strtolower($inhalt['LaufGruppe']);
	$C2Riege[$nr] = strtolower($inhalt['Riege']);
	$C2RundeBez[$nr] = strtolower($inhalt['RundeBez']);
	$C2MannschaftTN[$nr] = strtolower($inhalt['MannschaftTN']);
	$C2Teilnehmer[$nr] = strtolower($inhalt['Teilnehmer']);
	$C2Startzeit[$nr] = strtolower($inhalt['Startzeit']);
	$C2Startdatum[$nr] = strtolower($inhalt['Startdatum']);
	$C2COSANr[$nr] = strtolower($inhalt['COSANr']);
	$C2WettbewTyp[$nr] = strtolower($inhalt['WettbewTyp']);
	$C2Gemischt[$nr] = strtolower($inhalt['Gemischt']);
	$C2ErstelltDatumZeit[$nr] = strtolower($inhalt['ErstelltDatumZeit']);
	$C2DatensatzID[$nr] = strtolower($inhalt['DatensatzID']);
	$C2DatensatzIDmitZeit[$nr] = strtolower($inhalt['DatensatzIDmitZeit']);

}

array_multisort($C2DatensatzID, SORT_ASC, $Endli);

	unset($C2WettbewerbNr);
	unset($C2RundeTyp);
	unset($C2LaufGruppe);
	unset($C2Riege);
	unset($C2RundeBez);
	unset($C2MannschaftTN);
	unset($C2Teilnehmer);
	unset($C2Startzeit);
	unset($C2Startdatum);
	unset($C2COSANr);
	unset($C2WettbewTyp);
	unset($C2Gemischt);
	unset($C2ErstelltDatumZeit);
	unset($C2DatensatzID);
	unset($C2DatensatzIDmitZeit);

# Zaehlen von Läufen/Gruppen
	foreach($Endli as $BEndliZeile) {
	#Anzahl Läufe/Gruppen Array
	$TmpEndliAnzahlLaeufeGruppen[] = $BEndliZeile['WettbewNr']."-".$BEndliZeile['Riege']."-".$BEndliZeile['RundeTyp'];

	}
	$EndliAnzahlLaeufeGruppen = array_count_values($TmpEndliAnzahlLaeufeGruppen);
	
	$TmpWettbewNrVorher = "";
	$TmpRundeTypVorher = "";
	$TmpRiegeVorher = "";
	$TmpZaehlerTeilnehmerD = 0;
	$TmpTxtRundeMKVorher = "";
	$TmpTxtRundeNurLaufNrVorher = "";
	$TmpCOSANrVorher = "";
	
	#Endli reduzieren auf die notwendigen Datensätze (einen pro Runde)
	foreach($Endli as $DEndliZeile) {
		$NurLaufNrBezeichnung = "";
	
		
	
		if($DEndliZeile['WettbewNr'] == $TmpWettbewNrVorher && $DEndliZeile['RundeTyp'] == $TmpRundeTypVorher && $DEndliZeile['Riege'] == $TmpRiegeVorher && $DEndliZeile['COSANr'] == $TmpCOSANrVorher) {
			$DTeilnehmer = $TmpZaehlerTeilnehmerD + $DEndliZeile['Teilnehmer'];
			
			if($DEndliZeile['WettbewTyp']  == "m") {
			
				$MKBezeichnung = $TmpTxtRundeMKVorher."<br>".$DEndliZeile['RundeBez'];
			
			}
			# Nur Lauf-Nr.
			if($DEndliZeile['RundeTyp']  == "m") {
			
				if($DEndliZeile['RundeBez'] != $TmpTxtRundeNurLaufNrVorher) {
					$NurLaufNrBezeichnung = $TmpTxtRundeNurLaufNrVorher."<br>".$DEndliZeile['RundeBez'];
				}
				else {
					$NurLaufNrBezeichnung = $DEndliZeile['RundeBez'];
				}
			
			}
		
		
		}
		else {
			$DTeilnehmer = $DEndliZeile['Teilnehmer'];
			$TmpZaehlerTeilnehmerD = 0;
			
			if($DEndliZeile['WettbewTyp']  == "m") {
			
				$MKBezeichnung = $DEndliZeile['RundeBez'];
				$TmpTxtRundeMKVorher = "";
			}
			# Nur Lauf-Nr.
			if($DEndliZeile['RundeTyp']  == "m") {
			
				if($DEndliZeile['RundeBez'] != $TmpTxtRundeNurLaufNrVorher) {
					$NurLaufNrBezeichnung = $DEndliZeile['RundeBez'];
					$TmpTxtRundeNurLaufNrVorher = "";
				}
			}
			
			
		}
			
			if($DEndliZeile['WettbewTyp']  == "m") {
			
				$Endli2[$DEndliZeile['WettbewNr']."-".$DEndliZeile['Riege']."-".$DEndliZeile['RundeTyp']."-".$DEndliZeile['COSANr']] = array (		'WettbewNr'		=>	$DEndliZeile['WettbewNr'],
									'RundeTyp'		=>	$DEndliZeile['RundeTyp'],
									'Riege'			=>	$DEndliZeile['Riege'],
									'RundeBez'		=>	$MKBezeichnung,
									'MannschaftTN'	=>	$DEndliZeile['MannschaftTN'],
									'Startzeit'		=>	$DEndliZeile['Startzeit'],
									'Startdatum'		=>	$DEndliZeile['Startdatum'],
									'COSANr'		=>	$DEndliZeile['COSANr'],
									'WettbewTyp'	=>	$DEndliZeile['WettbewTyp']."d",
									'Teilnehmer'	=>	$DTeilnehmer,
									'AnzahlLaeufeGruppen' => $EndliAnzahlLaeufeGruppen[$DEndliZeile['WettbewNr']."-".$DEndliZeile['Riege']."-".$DEndliZeile['RundeTyp']]
									
			
			);
			}
			elseif ($DEndliZeile['RundeTyp']  == "m") {
			
			$Endli2[$DEndliZeile['WettbewNr']."-".$DEndliZeile['Riege']."-".$DEndliZeile['RundeTyp']."-".$DEndliZeile['COSANr']] = array (		'WettbewNr'		=>	$DEndliZeile['WettbewNr'],
									'RundeTyp'		=>	$DEndliZeile['RundeTyp'],
									'Riege'			=>	$DEndliZeile['Riege'],
									'RundeBez'		=>	$NurLaufNrBezeichnung,
									'MannschaftTN'	=>	$DEndliZeile['MannschaftTN'],
									'Startzeit'		=>	$DEndliZeile['Startzeit'],
									'Startdatum'		=>	$DEndliZeile['Startdatum'],
									'COSANr'		=>	$DEndliZeile['COSANr'],
									'WettbewTyp'	=>	$DEndliZeile['WettbewTyp'],
									'Teilnehmer'	=>	$DTeilnehmer,
									'AnzahlLaeufeGruppen' => $EndliAnzahlLaeufeGruppen[$DEndliZeile['WettbewNr']."-".$DEndliZeile['Riege']."-".$DEndliZeile['RundeTyp']]
									
			
			);
			
			}
			
		
			else {
			
		
			$Endli2[$DEndliZeile['WettbewNr']."-".$DEndliZeile['Riege']."-".$DEndliZeile['RundeTyp']] = array (		'WettbewNr'		=>	$DEndliZeile['WettbewNr'],
									'RundeTyp'		=>	$DEndliZeile['RundeTyp'],
									'Riege'			=>	$DEndliZeile['Riege'],
									'RundeBez'		=>	$DEndliZeile['RundeBez'],
									'MannschaftTN'	=>	$DEndliZeile['MannschaftTN'],
									'Startzeit'		=>	$DEndliZeile['Startzeit'],
									'Startdatum'		=>	$DEndliZeile['Startdatum'],
									'COSANr'		=>	$DEndliZeile['COSANr'],
									'WettbewTyp'	=>	$DEndliZeile['WettbewTyp'],
									'Gemischt'	=>	$DEndliZeile['Gemischt'],
									'Teilnehmer'	=>	$DTeilnehmer,
									'AnzahlLaeufeGruppen' => $EndliAnzahlLaeufeGruppen[$DEndliZeile['WettbewNr']."-".$DEndliZeile['Riege']."-".$DEndliZeile['RundeTyp']]
									
			
			);
			
		}
		
			$TmpWettbewNrVorher = $DEndliZeile['WettbewNr'];
			$TmpRundeTypVorher = $DEndliZeile['RundeTyp'];
			$TmpRiegeVorher = $DEndliZeile['Riege'];
			$TmpCOSANrVorher = $DEndliZeile['COSANr'];
			#$TmpZaehlerTeilnehmerD = $TmpZaehlerTeilnehmerD + $DTeilnehmer;
			$TmpZaehlerTeilnehmerD = $DTeilnehmer;
			$TmpTxtRundeMKVorher = $MKBezeichnung;
			$TmpTxtRundeNurLaufNrVorher = $NurLaufNrBezeichnung;
			
			
			
		
	}

	
	
	
	# Doppelte Werte aus Wettbewerbsnummerarray enfernen
	$EndliWettbewNr = array_unique($EndliWettbewNr);
	
	
	
	
	#print_r($EndliAnzahlLaeufeGruppen);
	#echo "<hr>";
	#print_r($Endli);
	#echo "<hr>";
	
	
	

	
	}
	
# Wettkampflisten-Datei WkList.c01 verwenden
if(file_exists($dat_wklist)) {

	#$WkListInhaltArray = file($dat_wklist);
	
	$WkListInhalt = file_get_contents($dat_wklist);
	
	$WkListLaenge = strlen($WkListInhalt);
	
	$WkListLaengeDatensatz = 539;
	
	$WkListAnzahlDatensaetze = $WkListLaenge / $WkListLaengeDatensatz;
	
	$WkListAnzahlDatensaetze."<br>";
	
	$WkListDatensatzzaehler = 0;
	$WkListAbsolutePositionDS = 1;
	
	while($WkListDatensatzzaehler < $WkListAnzahlDatensaetze) {
	
		$WkListDatensatzzaehler++;
		
		$WkListDatensatzzaehler;
		
		
		 $HTmpWettbewNr = trim(substr($WkListInhalt, $WkListAbsolutePositionDS - 1, 3)) * 1;
		
		 $HTmpCOSANr = trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 2, 5));
		 
		 $HTmpRundeTyp = trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 7, 1));
		 
		 $HTmpLaufGruppe = trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 8, 2));
		  
		 $HTmpStartdatum = trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 13, 10));
	
		 $HTmpStartzeit = uhrzeitformat(trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 23, 5)));
		
		 $HTmpTeilnehmer = trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 36, 2));
		
		 $HTmpWettbewTyp = $Wettbew[$HTmpWettbewNr]['WettbewTyp'];
		
		
		#Gemischt oder Riegen
		if(is_numeric(trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 10, 2)))) {
			$HTmpRiege = trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 10, 2)) * 1;
			
		}
		if(trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 10, 1)) == "p") {
			$HTmpRiege = ord(trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 11, 1)));
		}
		
		if ($IPCModeON != 1) {		
			$HTmpGemischt = trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 10, 2));	
		}
		else {
			$HTmpGemischt = "";
		}
		
		if(trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 10, 2)) == "") {
			$HTmpRiege = 0;
		}
		if(trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 10, 2)) == "v") {
			$HTmpRiege = 0;
		}
		
		
		
		#Gelöscht
		if(trim(substr($WkListInhalt, $WkListAbsolutePositionDS + 29, 2)) == "**") {
			$HTmpGeloescht = 1;
			
		}
		else {
			$HTmpGeloescht = 0;
		}
		
		
		if($IPCModeON != 1) {
		#RundeBez festlegen
		if($HTmpWettbewTyp == "m") {
		
			# Mehrkampf
			
			$HTmpRundeBezMKDis = $Disziplinen[trim(substr($HTmpCOSANr, 2, 3))*1]['Kurz'];
			
			if($HTmpRiege > 0) {
				$HTmpRundeBezMKRiege = " " . $TxtCombinedEventGroup . " ".$HTmpRiege." ";	# Riegenbezeichnung
			}
			else {
				$HTmpRundeBezMKRiege = "";
			}
			
			if($HTmpLaufGruppe > 0) {
				
				switch($Disziplinen[trim(substr($HTmpCOSANr, 2, 3))*1]['Typ']) {
				
					case "l":
					case "s":
						$HTmpRundeBezMKLaufGruppe = " " . $TxtHeat . " ".$HTmpLaufGruppe * 1;	# Lauf
					break;
					
					case "h":
					case "t":
						$HTmpRundeBezMKLaufGruppe = " " . $TxtGroup . " ".$HTmpLaufGruppe * 1;	# Gruppe

					break;
				
				
				}
				
				
			
				
			}
			else {
				$HTmpRundeBezMKLaufGruppe = "";
			}
			
			$HTmpRundeBez = $HTmpRundeBezMKDis.$HTmpRundeBezMKRiege.$HTmpRundeBezMKLaufGruppe;
			
		
		}

		else {
		
			# Alle anderen Wettbewerbe
			
			switch($HTmpRundeTyp) {
			
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
			
		}
		}
		else {
				
		
		#echo $HTmpRundeTyp."<br>";
		
		$HTmpRundeBez1 = "";

		switch($HTmpRundeTyp) {
			
				case "a": #Vorläufe
					$HTmpRundeBez1 = $RundeTyp1;
				break;
				case "b": #Zwischenläufe
					$HTmpRundeBez1 = $RundeTyp2;
				break;
				case "c": #Ausscheidung
					$HTmpRundeBez1 = $RundeTyp4;
				break;
				case "d": #Zeitfinalläufe
					$HTmpRundeBez1 = $RundeTyp6;
				break;
				case "e": #Zeit-Vorläufe
					$HTmpRundeBez1 = $RundeTyp3;
				break;
				case "k": #Finale
					$HTmpRundeBez1 = $RundeTyp0;
				break;
				case "l": #ABFinale
					$HTmpRundeBez1 = $RundeTyp7;
				break;
				case "m": #nur Lauf-Nr.
					$HTmpRundeBez1 = $RundeTyp8;
				break;
				case "n": #Finale Techn./Hoch
					$HTmpRundeBez1 = $RundeTyp0;
				break;
				case "q": #Finale Techn./Hoch
					$HTmpRundeBez1 = $RundeTyp0;
				break;
				case "r": #Ausscheidung Techn./Hoch
					$HTmpRundeBez1 = $RundeTyp4;
				break;
				case "s": #Qualifikation Tech./Hoch
					$HTmpRundeBez1 = $RundeTyp5;
				break;
			
			}
			
			if((strlen($HTmpRundeBez1) + strlen($DBSTextskl_timetable[$HTmpRiege]['IPCClassName'])) > 16) {
				$HTmpSeperator1 = "<br>";
			}
			else {
				$HTmpSeperator1 = " ";
			}
			
			$HTmpRundeBez = $HTmpRundeBez1 . $HTmpSeperator1 . $DBSTextskl_timetable[$HTmpRiege]['IPCClassName'];
			
		
		}
		
		
		
		# In Array schreiben
		if($HTmpGeloescht != 1) {
		
			$TmpWkList[] = array(	'WettbewNr'		=>	$HTmpWettbewNr,
									'RundeTyp'		=>	$HTmpRundeTyp,
									'LaufGruppe'	=>	$HTmpLaufGruppe,
									'Riege'			=>	$HTmpRiege,
									'RundeBez'		=>	$HTmpRundeBez,
									'Teilnehmer'	=>	$HTmpTeilnehmer,
									'Startzeit'		=>	$HTmpStartzeit,
									'Startdatum'	=>	$HTmpStartdatum,
									'COSANr'		=>	$HTmpCOSANr,
									'WettbewTyp'	=>	$HTmpWettbewTyp,
									'Gemischt'		=>	$HTmpGemischt,
									'DatensatzID'	=>	$HTmpWettbewNr."-".$HTmpCOSANr."-".$HTmpRundeTyp."-".$HTmpRiege."-".$HTmpLaufGruppe
									);
		}

		$WkListAbsolutePositionDS = $WkListAbsolutePositionDS + $WkListLaengeDatensatz;
	
	} #Datensatz durchgehen
	
	
	# Sortieren und doppelte Datensätze entfernen
	
	foreach ($TmpWkList as $nr => $inhalt) {

	$IWettbewerbNr[$nr] = strtolower($inhalt['WettbewNr']);
	$IRundeTyp[$nr] = strtolower($inhalt['RundeTyp']);
	$ILaufGruppe[$nr] = strtolower($inhalt['LaufGruppe']);
	$IRiege[$nr] = strtolower($inhalt['Riege']);
	$IRundeBez[$nr] = strtolower($inhalt['RundeBez']);
	$ITeilnehmer[$nr] = strtolower($inhalt['Teilnehmer']);
	$IStartzeit[$nr] = strtolower($inhalt['Startzeit']);
	$IStartdatum[$nr] = strtolower($inhalt['Startdatum']);
	$ICOSANr[$nr] = strtolower($inhalt['COSANr']);
	$IWettbewTyp[$nr] = strtolower($inhalt['WettbewTyp']);
	$IGemischt[$nr] = strtolower($inhalt['Gemischt']);
	$IDatensatzID[$nr] = strtolower($inhalt['DatensatzID']);

	}

	array_multisort($IDatensatzID, SORT_ASC, $TmpWkList);
	
	#echo "Hier : ";
	#print_r($TmpWkList);
	
	# Doppelte Datensätze entfernen in Array
	$WkList = array_unique_by_subitem($TmpWkList, 'DatensatzID');
	
	
	# Zaehlen von Läufen/Gruppen
	foreach($WkList as $JWkListZeile) {
	
		#Anzahl Läufe/Gruppen Array
		
		$TmpWkListAnzahlLaeufeGruppen[] = $JWkListZeile['WettbewNr']."-".$JWkListZeile['Riege']."-".$JWkListZeile['RundeTyp'];
	
	}
	$WkListAnzahlLaeufeGruppen = array_count_values($TmpWkListAnzahlLaeufeGruppen);
	
	
	$KTmpWettbewNrVorher = "";
	$KTmpRundeTypVorher = "";
	$KTmpRiegeVorher = "";
	$KTmpZaehlerTeilnehmerD = 0;
	$KTmpTxtRundeMKVorher = "";
	$KTmpCOSANrVorher = "";
	
	
	#WkList auf die notwendigen Datensätze reduzieren (1 pro Runde)
	foreach($WkList as $KWkListZeile) {
	
	$KMKBezeichnung = "";
	
		if($KWkListZeile['WettbewNr'] == $KTmpWettbewNrVorher && $KWkListZeile['RundeTyp'] == $KTmpRundeTypVorher && $KWkListZeile['Riege'] == $KTmpRiegeVorher && $KWkListZeile['COSANr'] == $KTmpCOSANrVorher) {
			$KTeilnehmer = $KTmpZaehlerTeilnehmerD + $KWkListZeile['Teilnehmer'];
			
			if($KWkListZeile['WettbewTyp']  == "m") {
			
				$KMKBezeichnung = $KTmpTxtRundeMKVorher."<br>".$KWkListZeile['RundeBez'];
			
			}
		
		
		}
		else {
			$KTeilnehmer = $KWkListZeile['Teilnehmer'];
			$KTmpZaehlerTeilnehmerD = 0;
			
			if($KWkListZeile['WettbewTyp']  == "m") {
			
				$KMKBezeichnung = $KWkListZeile['RundeBez'];
				$KTmpTxtRundeMKVorher = "";
			}
			
			
		}
	
		if($KWkListZeile['WettbewTyp']  == "m") {
			
				$WkList2[$KWkListZeile['WettbewNr']."-".$KWkListZeile['Riege']."-".$KWkListZeile['RundeTyp']."-".$KWkListZeile['COSANr']] = array (		'WettbewNr'		=>	$KWkListZeile['WettbewNr'],
									'RundeTyp'		=>	$KWkListZeile['RundeTyp'],
									'Riege'			=>	$KWkListZeile['Riege'],
									'RundeBez'		=>	$KMKBezeichnung,
									'Startzeit'		=>	$KWkListZeile['Startzeit'],
									'Startdatum'		=>	$KWkListZeile['Startdatum'],
									'COSANr'		=>	$KWkListZeile['COSANr'],
									'WettbewTyp'	=>	$KWkListZeile['WettbewTyp']."d",
									'Teilnehmer'	=>	$KTeilnehmer,
									'AnzahlLaeufeGruppen' => $WkListAnzahlLaeufeGruppen[$KWkListZeile['WettbewNr']."-".$KWkListZeile['Riege']."-".$KWkListZeile['RundeTyp']]
									
			
			);
			}
		
			else {
			
		
			$WkList2[$KWkListZeile['WettbewNr']."-".$KWkListZeile['Riege']."-".$KWkListZeile['RundeTyp']] = array (		'WettbewNr'		=>	$KWkListZeile['WettbewNr'],
									'RundeTyp'		=>	$KWkListZeile['RundeTyp'],
									'Riege'			=>	$KWkListZeile['Riege'],
									'RundeBez'		=>	$KWkListZeile['RundeBez'],
									'Startzeit'		=>	$KWkListZeile['Startzeit'],
									'Startdatum'		=>	$KWkListZeile['Startdatum'],
									'COSANr'		=>	$KWkListZeile['COSANr'],
									'WettbewTyp'	=>	$KWkListZeile['WettbewTyp'],
									'Gemischt'	=>	$KWkListZeile['Gemischt'],
									'Teilnehmer'	=>	$KTeilnehmer,
									'AnzahlLaeufeGruppen' => $WkListAnzahlLaeufeGruppen[$KWkListZeile['WettbewNr']."-".$KWkListZeile['Riege']."-".$KWkListZeile['RundeTyp']]
									
			
			);
			
		}
		
			$KTmpWettbewNrVorher = $KWkListZeile['WettbewNr'];
			$KTmpRundeTypVorher = $KWkListZeile['RundeTyp'];
			$KTmpRiegeVorher = $KWkListZeile['Riege'];
			$KTmpCOSANrVorher = $KWkListZeile['COSANr'];
			$KTmpZaehlerTeilnehmerD = $KTeilnehmer;
			$KTmpTxtRundeMKVorher = $KMKBezeichnung;

	}
	

} #WkList.c01 existiert	
	
	
	

# Datei "WbTeil.c01" verwenden um die Teilnehmerzahlen zu ermitteln
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
	
	$WbTeiln[] = trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 4, 3));
	
	
	
	$WbTeilnAbsolutePositionDS = $WbTeilnAbsolutePositionDS + $WbTeilnLaengeDatensatz;
	
	
	
	}
	#print_r($WbTeiln);
	
	
	$TeilnehmeranzahlWb = array_count_values($WbTeiln);
	
	#print_r($TeilnehmeranzahlWb);
	
	

}

# Datei "WbTeil.c01" verwenden um die Teilnehmerzahlen zu ermitteln --- Staffeln
if(file_exists($dat_wbteiln)) {

	$StWbTeiln = array();

	#$StWbTeilnInhaltArray = file($dat_wbteiln);
	$StWbTeilnInhalt = file_get_contents($dat_wbteiln);
	$StWbTeilnLaenge = strlen($StWbTeilnInhalt);
	$StWbTeilnLaengeDatensatz = 100;
	$StWbTeilnAnzahlDatensaetze = $StWbTeilnLaenge / $StWbTeilnLaengeDatensatz;
	$StWbTeilnDatensatzzaehler = 0;
	$StWbTeilnAbsolutePositionDS = 1;
	
	while($StWbTeilnDatensatzzaehler < $StWbTeilnAnzahlDatensaetze) {
	
	$StWbTeilnDatensatzzaehler++;
	$tmpstaffel = trim(substr($StWbTeilnInhalt, $StWbTeilnAbsolutePositionDS + 12, 1));
	
	if($tmpstaffel == 1) {
	
		$StWbTeiln[] = trim(substr($StWbTeilnInhalt, $StWbTeilnAbsolutePositionDS + 4, 3));
	}
	
	
	$StWbTeilnAbsolutePositionDS = $StWbTeilnAbsolutePositionDS + $StWbTeilnLaengeDatensatz;
	
	
	
	}	
	$StTeilnehmeranzahlWb = array_count_values($StWbTeiln);

}


### Wettbewerbsarray durchlaufen und Ermitteln, ob für welche Runden Daten vorliegen
foreach($Wettbew as $FWettbewZeile) {

	# 1. Vorrunden / -läufe
		if (empty($FWettbewZeile['VorlaufZeit']) == false) {
			$ZeitplanRunden[$FWettbewZeile['WettbewNr']."v"] = 0;
		}
	
	# 2. Zwischenläufe
		if (empty($FWettbewZeile['ZwischenlaufZeit']) == false) {
			$ZeitplanRunden[$FWettbewZeile['WettbewNr']."z"] = 0;
		}
	
	# 3. Finale
		if (empty($FWettbewZeile['FinaleZeit']) == false) {
			$ZeitplanRunden[$FWettbewZeile['WettbewNr']."f"] = 0;
		}

	
	

}





	#print_r($ZeitplanRunden);
	#echo "<hr>";

#######################################################################################
## Endli2-Array  ausgeben um den Zeitplan zu erstellen ################################
#######################################################################################	

foreach($Endli2 as $GEndli2Zeile) {  # Master Endli2

	$GTmpDatei = "";
	
	if($IPCModeON != 1) {

	switch($GEndli2Zeile['WettbewTyp']) { # Trennen nach Wettbewerstypen (w - Lauf noch nicht erfasst)
	
		case "l": # Läufe Bahn
		case "s": # Staffeln Bahn
		
			switch($GEndli2Zeile['RundeTyp']) { # Trennung nach Rundentypen
			
				case "a": # Vorläufe
				
				if(file_exists($GEndli2Zeile['COSANr']."1.htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						
							$GTmpDatei = $GEndli2Zeile['COSANr']."1.htm";
							$GTmpRundeBez = $RundeTyp1;
						
						
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  1,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
					
					}
				
				
				
				break;
				
				
				case "c": # Ausscheidungslauf
				
				if(file_exists($GEndli2Zeile['COSANr'].".htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $GEndli2Zeile['COSANr'].".htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  4,
											'RundeBez'				=> 	$RundeTyp4,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
				
				
				
				break;
				
				case "e": # Zeit-Vorläufe
				
				if(file_exists($GEndli2Zeile['COSANr']."3.htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."3.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  3,
											'RundeBez'				=> 	$RundeTyp3,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
				
				
				
				break;
				
				case "b": # Zwischenläufe
				
				if(file_exists($GEndli2Zeile['COSANr']."2.htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."2.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."z"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  2,
											'RundeBez'				=> 	$RundeTyp2,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."z"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
				
				
				
				break;
				
				case "k": # Finale
				
				
				
				
				if(file_exists($GEndli2Zeile['COSANr'].".htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $GEndli2Zeile['COSANr'].".htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$RundeTyp0,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$RundeTyp0,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
						
					
					}
					
					
				
				
				
				break;
				
				case "d": # Zeitläufe
				
				
				
				if(file_exists($GEndli2Zeile['COSANr'].".htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $GEndli2Zeile['COSANr'].".htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  6,
											'RundeBez'				=> 	$RundeTyp6,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  6,
											'RundeBez'				=> 	$RundeTyp6,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
						
					
					}
					
					
				
				
				
				break;
				
				case "l": # A-/B-Finale
				
				
				
				if(file_exists($GEndli2Zeile['COSANr'].".htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $GEndli2Zeile['COSANr'].".htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  7,
											'RundeBez'				=> 	$RundeTyp7,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  7,
											'RundeBez'				=> 	$RundeTyp7,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
						
					
					}
					
					
				
				
				
				break;
				
				case "m": # nur Lauf Nr.
				
				
				
				if(file_exists($GEndli2Zeile['COSANr'].".htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $GEndli2Zeile['COSANr'].".htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."nur"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  8,
											'RundeBez'				=> 	$GEndli2Zeile['RundeBez'],
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						#$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  7,
											'RundeBez'				=> 	$RundeTyp7,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						#$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
						
					
					}
					
					
				
				
				
				break;
			
			}
		
		
		break;
		
		case "h": # hoch
		case "t": # technisch
		
			switch($GEndli2Zeile['RundeTyp']) { # Trennung nach Rundentypen
			
				case "r": # Ausscheidung
				
				# Typ / aktualisiert / Datei ermitteln ermitteln UND Schreiben der ZeitplanRunden zur Kontrolle
					if(file_exists($GEndli2Zeile['COSANr'].".htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $GEndli2Zeile['COSANr'].".htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  4,
											'RundeBez'				=> 	$RundeTyp4,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
					
					
				
				
				
				break;
				
				
				case "s": #Qualifikation
				
				# Typ / aktualisiert / Datei ermitteln ermitteln UND Schreiben der ZeitplanRunden zur Kontrolle
				if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  4,
											'RundeBez'				=> 	$RundeTyp4,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
					if(file_exists($GEndli2Zeile['COSANr'].".htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $GEndli2Zeile['COSANr'].".htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  5,
											'RundeBez'				=> 	$RundeTyp5,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						
						
					
				
					
					if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  5,
											'RundeBez'				=> 	$RundeTyp5,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
					}
				
				
				
				
				break;
				
				
				case "n": # finale hoch
				case "q": # finale technisch
				
				
					# Typ / aktualisiert / Datei ermitteln ermitteln UND Schreiben der ZeitplanRunden zur Kontrolle
					
					if(file_exists($GEndli2Zeile['COSANr'].".htm")) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $GEndli2Zeile['COSANr'].".htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$RundeTyp0,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
						
					
					
					
					if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$RundeTyp0,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
					}
				
				break;
			
			}
		
		
		break;
		
		
		case "md": # mehrkampf
			
			if($GEndli2Zeile['COSANr'] !== $Wettbew['COSANr'] && empty($GEndli2Zeile['Startzeit']) == false) {
			
						$GTmpTypNr 	= 8;
						$GTmpTypBez = $TypTyp8;
						
						$GTmpDatei = "";
						$GTmpaktualisiert = filemtime($dat_endli);
						
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Starttag aus Startdatum ermitteln
						switch ($GEndli2Zeile['Startdatum']) {
						
							case $tage[1]:
								$ETmpStarttag = 1;
							break;
							case $tage[2]:
								$ETmpStarttag = 2;
							break;
							case $tage[3]:
								$ETmpStarttag = 3;
							break;
							case $tage[4]:
								$ETmpStarttag = 4;
							break;
							default:
							$ETmpStarttag = 1;
							break;
						}
						
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											#'StartTag'				=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['FinaleTag'],
											'StartTag'				=>	$ETmpStarttag,
											'RundeNr'				=>  9,
											'RundeBez'				=> 	$GEndli2Zeile['RundeBez'],
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
				
			
			}
		
		break;
	
	}
	
	
	} # no dbs /ipc
	
	else { # dbs /ipc
	
	$IPCResutsFile1 = "e-" . $GEndli2Zeile['WettbewNr'] . "-" . $GEndli2Zeile['Riege'] . "-";
	
	switch($GEndli2Zeile['WettbewTyp']) { # Trennen nach Wettbewerstypen (w - Lauf noch nicht erfasst)
	
		case "l": # Läufe Bahn
		case "s": # Staffeln Bahn
		
			switch($GEndli2Zeile['RundeTyp']) { # Trennung nach Rundentypen
			
				case "a": # Vorläufe
				
				if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
							$TmpRundeTypIPC = $RundeTyp1;
						
							$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
							
							if($GEndli2Zeile['AnzahlLaeufeGruppen'] < 2) {
								$GTmpRundeBez = $GEndli2Zeile['RundeBez'];
							}
							else{
								if((strlen($TmpRundeTypIPC) + strlen($DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'])) > 16) {
									$GTmpSeperator1 = "<br>";
								}
								else {
									$GTmpSeperator1 = " ";
								}
								
								
								$GTmpRundeBez = $TmpRundeTypIPC . $GTmpSeperator1 . $DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'];
							}
						
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  1,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
					
					}
				
				
				
				break;
				
				
				case "c": # Ausscheidungslauf
				
				if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$TmpRundeTypIPC = $RundeTyp4;
						
						if($GEndli2Zeile['AnzahlLaeufeGruppen'] < 2) {
								$GTmpRundeBez = $GEndli2Zeile['RundeBez'];
							}
							else{
								if((strlen($TmpRundeTypIPC) + strlen($DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'])) > 16) {
									$GTmpSeperator1 = "<br>";
								}
								else {
									$GTmpSeperator1 = " ";
								}
								
								
								$GTmpRundeBez = $TmpRundeTypIPC . $GTmpSeperator1 . $DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'];
							}
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  4,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
				
				
				
				break;
				
				case "e": # Zeit-Vorläufe
				
				if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$TmpRundeTypIPC = $RundeTyp3;
						
						if($GEndli2Zeile['AnzahlLaeufeGruppen'] < 2) {
								$GTmpRundeBez = $GEndli2Zeile['RundeBez'];
							}
							else{
								if((strlen($TmpRundeTypIPC) + strlen($DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'])) > 16) {
									$GTmpSeperator1 = "<br>";
								}
								else {
									$GTmpSeperator1 = " ";
								}
								
								
								$GTmpRundeBez = $TmpRundeTypIPC . $GTmpSeperator1 . $DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'];
							}
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  3,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
				
				
				
				break;
				
				case "b": # Zwischenläufe
				
				if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$TmpRundeTypIPC = $RundeTyp2;
						
						if($GEndli2Zeile['AnzahlLaeufeGruppen'] < 2) {
								$GTmpRundeBez = $GEndli2Zeile['RundeBez'];
							}
							else{
								if((strlen($TmpRundeTypIPC) + strlen($DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'])) > 16) {
									$GTmpSeperator1 = "<br>";
								}
								else {
									$GTmpSeperator1 = " ";
								}
								
								
								$GTmpRundeBez = $TmpRundeTypIPC . $GTmpSeperator1 . $DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'];
							}
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."z"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  2,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."z"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
				
				
				
				break;
				
				case "k": # Finale
				
				
				
				
				if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$TmpRundeTypIPC = $RundeTyp0;
						
						if($GEndli2Zeile['AnzahlLaeufeGruppen'] < 2) {
								$GTmpRundeBez = $GEndli2Zeile['RundeBez'];
							}
							else{
								if((strlen($TmpRundeTypIPC) + strlen($DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'])) > 16) {
									$GTmpSeperator1 = "<br>";
								}
								else {
									$GTmpSeperator1 = " ";
								}
								
								
								$GTmpRundeBez = $TmpRundeTypIPC . $GTmpSeperator1 . $DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'];
							}
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					# NICHT BEARBEITET
						if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$RundeTyp0,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
						
					
					}
					
					
				
				
				
				break;
				
				case "d": # Zeitläufe
				
				
				
				if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$TmpRundeTypIPC = $RundeTyp6;
						
						if($GEndli2Zeile['AnzahlLaeufeGruppen'] < 2) {
								$GTmpRundeBez = $GEndli2Zeile['RundeBez'];
							}
							else{
								if((strlen($TmpRundeTypIPC) + strlen($DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'])) > 16) {
									$GTmpSeperator1 = "<br>";
								}
								else {
									$GTmpSeperator1 = " ";
								}
								
								
								$GTmpRundeBez = $TmpRundeTypIPC . $GTmpSeperator1 . $DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'];
							}
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  6,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					# NICHT BEARBEITET
						if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  6,
											'RundeBez'				=> 	$RundeTyp6,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
						
					
					}
					
					
				
				
				
				break;
				
				case "l": # A-/B-Finale
				
				
				
				if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$TmpRundeTypIPC = $RundeTyp7;
						
						if($GEndli2Zeile['AnzahlLaeufeGruppen'] < 2) {
								$GTmpRundeBez = $GEndli2Zeile['RundeBez'];
							}
							else{
								if((strlen($TmpRundeTypIPC) + strlen($DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'])) > 16) {
									$GTmpSeperator1 = "<br>";
								}
								else {
									$GTmpSeperator1 = " ";
								}
								
								
								$GTmpRundeBez = $TmpRundeTypIPC . $GTmpSeperator1 . $DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'];
							}
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  7,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					# NICHT BEARBEITET
						if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  7,
											'RundeBez'				=> 	$RundeTyp7,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
						
					
					}
					
					
				
				
				
				break;
				
				case "m": # nur Lauf Nr. --- nicht geändert für DBS, müsste Daten ja direkt entnehmen.
				
				
				
				if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."nur"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  8,
											'RundeBez'				=> 	$GEndli2Zeile['RundeBez'],
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					# NICHT BEARBEITET
						if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						#$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  7,
											'RundeBez'				=> 	$RundeTyp7,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						#$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
						
					
					}
					
					
				
				
				
				break;
			
			}
		
		
		break;
		
		case "h": # hoch
		case "t": # technisch
		
			switch($GEndli2Zeile['RundeTyp']) { # Trennung nach Rundentypen
			
				case "r": # Ausscheidung
				
				# Typ / aktualisiert / Datei ermitteln ermitteln UND Schreiben der ZeitplanRunden zur Kontrolle
					if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$TmpRundeTypIPC = $RundeTyp4;
						
						if($GEndli2Zeile['AnzahlLaeufeGruppen'] < 2) {
								$GTmpRundeBez = $GEndli2Zeile['RundeBez'];
							}
							else{
								if((strlen($TmpRundeTypIPC) + strlen($DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'])) > 16) {
									$GTmpSeperator1 = "<br>";
								}
								else {
									$GTmpSeperator1 = " ";
								}
								
								
								$GTmpRundeBez = $TmpRundeTypIPC . $GTmpSeperator1 . $DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'];
							}
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  4,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
					
					
				
				
				
				break;
				
				
				case "s": #Qualifikation
				
				# Typ / aktualisiert / Datei ermitteln ermitteln UND Schreiben der ZeitplanRunden zur Kontrolle
				
				# NIChT BEACHTEN
				if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  4,
											'RundeBez'				=> 	$RundeTyp4,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
					if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$TmpRundeTypIPC = $RundeTyp5;
						
						if($GEndli2Zeile['AnzahlLaeufeGruppen'] < 2) {
								$GTmpRundeBez = $GEndli2Zeile['RundeBez'];
							}
							else{
								if((strlen($TmpRundeTypIPC) + strlen($DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'])) > 16) {
									$GTmpSeperator1 = "<br>";
								}
								else {
									$GTmpSeperator1 = " ";
								}
								
								
								$GTmpRundeBez = $TmpRundeTypIPC . $GTmpSeperator1 . $DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'];
							}
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  5,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
						
					}
					else {
					
						
						
					
				
					
					# NICHT BEARBEITET
					if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  5,
											'RundeBez'				=> 	$RundeTyp5,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."v"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
					}
				
				
				
				
				break;
				
				
				case "n": # finale hoch
				case "q": # finale technisch
				
				
					# Typ / aktualisiert / Datei ermitteln ermitteln UND Schreiben der ZeitplanRunden zur Kontrolle
					
					if(file_exists($IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention)) {
						$GTmpTypNr 	= 1;
						$GTmpTypBez = $TypTyp1;
						
						$GTmpDatei = $IPCResutsFile1 . $GEndli2Zeile['RundeTyp'] . "." . $IPCResultListFileExtention;
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						$TmpRundeTypIPC = $RundeTyp0;
						
						if($GEndli2Zeile['AnzahlLaeufeGruppen'] < 2) {
								$GTmpRundeBez = $GEndli2Zeile['RundeBez'];
							}
							else{
								if((strlen($TmpRundeTypIPC) + strlen($DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'])) > 16) {
									$GTmpSeperator1 = "<br>";
								}
								else {
									$GTmpSeperator1 = " ";
								}
								
								
								$GTmpRundeBez = $TmpRundeTypIPC . $GTmpSeperator1 . $DBSTextskl_timetable[$GEndli2Zeile['Riege']]['IPCClassName'];
							}
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$GTmpRundeBez,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
							
						
					}
					else {
						
					
					
					# NICHT BEARBEITET
					if(file_exists($GEndli2Zeile['COSANr']."z.htm") && file_exists($GEndli2Zeile['COSANr'].".htm") == false) {
						$GTmpTypNr 	= 3;
						$GTmpTypBez = $TypTyp3;
						
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 1;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						
						$GTmpDatei = $GEndli2Zeile['COSANr']."z.htm";
						$GTmpaktualisiert = filemtime($GTmpDatei);
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	array_search($GEndli2Zeile['Startdatum'], $tage),
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$RundeTyp0,
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
						
					}
					else {
					
						$ZeitplanRunden[$GEndli2Zeile['WettbewNr']."f"] = 0;
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 0;
						
					
					}
					}
				
				break;
			
			}
		
		
		break;
		
		
		case "md": # mehrkampf
		
			if($GEndli2Zeile['COSANr'] !== $Wettbew['COSANr'] && empty($GEndli2Zeile['Startzeit']) == false) {
			
						$GTmpTypNr 	= 8;
						$GTmpTypBez = $TypTyp8;
						
						#$GTmpDatei = $GEndli2Zeile['COSANr'].".htm";
						$GTmpaktualisiert = filemtime($dat_endli);
						
						$ZeitplanRundenEndli[$GEndli2Zeile['WettbewNr']."-".$GEndli2Zeile['COSANr']."-".$GEndli2Zeile['RundeTyp']."-".$GEndli2Zeile['Riege']] = 1;
						
						# Starttag aus Startdatum ermitteln
						switch ($GEndli2Zeile['Startdatum']) {
						
							case $tage[1]:
								$ETmpStarttag = 1;
							break;
							case $tage[2]:
								$ETmpStarttag = 2;
							break;
							case $tage[3]:
								$ETmpStarttag = 3;
							break;
							case $tage[4]:
								$ETmpStarttag = 4;
							break;
							default:
							$ETmpStarttag = 1;
							break;
						}
						
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$GEndli2Zeile['WettbewNr'],
											'WettbewerbTyp'			=>	$GEndli2Zeile['WettbewTyp'],
											'DISBez'				=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['DISBez'],
											'AKBez'					=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$GEndli2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $GEndli2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $GEndli2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $GEndli2Zeile['COSANr'],
											'StartZeit'				=>  $GEndli2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											#'StartTag'				=>	$Wettbew[$GEndli2Zeile['WettbewNr']]['FinaleTag'],
											'StartTag'				=>	$ETmpStarttag,
											'RundeNr'				=>  9,
											'RundeBez'				=> 	$GEndli2Zeile['RundeBez'],
											'TypNr'					=>	$GTmpTypNr,
											'TypBez'				=> 	$GTmpTypBez,
											'Gemischt'				=> 	$GEndli2Zeile['Gemischt'],
											'aktualisiert'			=> 	$GTmpaktualisiert,
											'RoundTypeLetter'		=> 	$GEndli2Zeile['RundeTyp'],
											'Riege'					=>	$GEndli2Zeile['Riege'],
											'Datei'					=>  $GTmpDatei);
				
			
			}
		
		break;
	
	}
	
	} # end dbs / ipc

	


} # Master Endli2	

	#echo "Zeitplanarray: ";
	#print_r($ZeitplanRundenEndli);
	
#######################################################################################
## WkList2-Array  ausgeben um den Zeitplan zu erstellen ###############################
#######################################################################################	

foreach($WkList2 as $LWkList2Zeile) { # Master WkList2

	


	if($ZeitplanRundenEndli[$LWkList2Zeile['WettbewNr']."-".$LWkList2Zeile['COSANr']."-".$LWkList2Zeile['RundeTyp']."-".$LWkList2Zeile['Riege']] != 1) {
	
		
	
		# Falls nicht schon durch Endli erzeugt
		
		if($IPCModeON != 1) {
	
		switch($LWkList2Zeile['WettbewTyp']) { # Trennung nach WettbewertsTyp
		
			case "l": # lauf Bahn
			case "s": # Staffel Bahn
		
				switch($LWkList2Zeile['RundeTyp']) { # Trennung nach Rundentyp
				
					case "a": #Vorläufe
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."a.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."a.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."a.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."a.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."v"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  1,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
						
					
					
					
					break;
				
					case "c": #Ausscheidungslauf
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."c.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."c.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."c.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."c.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."v"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  4,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;
						
						case "e": #Zeitvorlauf
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."e.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."e.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."e.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."e.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."v"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  3,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;

						case "b": #Zwischenlauf
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."b.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."b.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."b.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."b.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."z"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  2,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;							

						case "k": #Finale
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."k.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."k.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."k.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."k.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Vorlauf aber keine Zwischenläufe
							
								$LTmpTypNr 	= 6;
								$LTmpTypBez = $TypTyp6;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == false) { # Falls Vorlauf und Zwischenlauf
								$LTmpTypNr 	= 7;
								$LTmpTypBez = $TypTyp7;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == true && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Weder Vorlauf noch Zwischenlauf
							
								$LTmpTypNr 	= 5;
								$LTmpTypBez = $TypTyp5;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
	
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;	

						case "d": #Zeitläufe
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."d.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."d.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."d.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."d.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Vorlauf aber keine Zwischenläufe
							
								$LTmpTypNr 	= 6;
								$LTmpTypBez = $TypTyp6;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == false) { # Falls Vorlauf und Zwischenlauf
								$LTmpTypNr 	= 7;
								$LTmpTypBez = $TypTyp7;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == true && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Weder Vorlauf noch Zwischenlauf
							
								$LTmpTypNr 	= 5;
								$LTmpTypBez = $TypTyp5;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
	
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  6,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;												

						case "l": #ABFinale
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."l.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."l.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."l.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."l.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Vorlauf aber keine Zwischenläufe
							
								$LTmpTypNr 	= 6;
								$LTmpTypBez = $TypTyp6;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == false) { # Falls Vorlauf und Zwischenlauf
								$LTmpTypNr 	= 7;
								$LTmpTypBez = $TypTyp7;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == true && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Weder Vorlauf noch Zwischenlauf
							
								$LTmpTypNr 	= 5;
								$LTmpTypBez = $TypTyp5;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
	
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  7,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;	

							case "m": #nur lauf nr.
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."m.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."m.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."m.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."m.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Vorlauf aber keine Zwischenläufe
							
								$LTmpTypNr 	= 6;
								$LTmpTypBez = $TypTyp6;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == false) { # Falls Vorlauf und Zwischenlauf
								$LTmpTypNr 	= 7;
								$LTmpTypBez = $TypTyp7;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == true && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Weder Vorlauf noch Zwischenlauf
							
								$LTmpTypNr 	= 5;
								$LTmpTypBez = $TypTyp5;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
	
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."nur"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  8,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;
							
				
				} # Trennung nach Rundentyp
		
		
		
			break;
			
			case "h": # hoch
			case "t": # technisch
			
			switch($LWkList2Zeile['RundeTyp']) { # Trennung nach Rundentyp
			
				case "r": # Ausscheidung
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."r.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."r.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."r.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."r.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."v"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  4,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;
				
				
				case "s": # Ausscheidung
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."s.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."s.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."s.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."s.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."v"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  5,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;
							
				case "n": # finale hoch
				
				if(file_exists("s".$LWkList2Zeile['COSANr']."n.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."n.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."n.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."n.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
				
				
				break;
				case "q": # finale technisch
					
						if(file_exists("s".$LWkList2Zeile['COSANr']."q.htm")) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s".$LWkList2Zeile['COSANr']."q.htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists("s".$LWkList2Zeile['COSANr']."q.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists("s".$LWkList2Zeile['COSANr']."q.htm") == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
		
							break;
				break;

			}

			break;
			
			case "md": # mehrkampf
			
			if($LWkList2Zeile['COSANr'] !== $Wettbew['COSANr'] && empty($LWkList2Zeile['Startzeit']) == false) {
			
			#echo "s-".$LWkList2Zeile['WettbewTyp']."-".$LWkList2Zeile['WettbewNr']."-".$LWkList2Zeile['COSANr']."-".$LWkList2Zeile['Riege']."-".$LWkList2Zeile['RundeTyp'].".htm";
			
						if(file_exists("s-".$LWkList2Zeile['WettbewTyp']."-".$LWkList2Zeile['WettbewNr']."-".$LWkList2Zeile['COSANr']."-".$LWkList2Zeile['Riege']."-".$LWkList2Zeile['RundeTyp'].".htm")) {
							
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s-".$LWkList2Zeile['WettbewTyp']."-".$LWkList2Zeile['WettbewNr']."-".$LWkList2Zeile['COSANr']."-".$LWkList2Zeile['Riege']."-".$LWkList2Zeile['RundeTyp'].".htm";
							$LTmpaktualisiert = filemtime("s-".$LWkList2Zeile['WettbewTyp']."-".$LWkList2Zeile['WettbewNr']."-".$LWkList2Zeile['COSANr']."-".$LWkList2Zeile['Riege']."-".$LWkList2Zeile['RundeTyp'].".htm");
							
						}
						else {
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						#$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  9,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
		
							
			}
			break;
		
		
		
		
		} # Trennung nach WettbewertsTyp
		
		} # DSB/IPC Mode nicht
		
		else { # DSB / IPC Mode
		
		# Dateinamen
		$TmpIPCFilenameStartList1 = "s-" . $LWkList2Zeile['WettbewTyp'] . "-" .$LWkList2Zeile['WettbewNr'] . "-" . $LWkList2Zeile['COSANr'] . "-" . $LWkList2Zeile['Riege'] . "-";
		$TmpIPCFilenameStartList2 = ".htm";
		
		switch($LWkList2Zeile['WettbewTyp']) { # Trennung nach WettbewertsTyp
		
			case "l": # lauf Bahn
			case "s": # Staffel Bahn
		
				switch($LWkList2Zeile['RundeTyp']) { # Trennung nach Rundentyp
				
					case "a": #Vorläufe
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."v"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  1,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
						
					
					
					
					break;
				
					case "c": #Ausscheidungslauf
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."v"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  4,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;
						
						case "e": #Zeitvorlauf
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."v"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  3,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;

						case "b": #Zwischenlauf
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."z"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  2,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;							

						case "k": #Finale
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Vorlauf aber keine Zwischenläufe
							
								$LTmpTypNr 	= 6;
								$LTmpTypBez = $TypTyp6;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == false) { # Falls Vorlauf und Zwischenlauf
								$LTmpTypNr 	= 7;
								$LTmpTypBez = $TypTyp7;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == true && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Weder Vorlauf noch Zwischenlauf
							
								$LTmpTypNr 	= 5;
								$LTmpTypBez = $TypTyp5;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
	
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;	

						case "d": #Zeitläufe
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Vorlauf aber keine Zwischenläufe
							
								$LTmpTypNr 	= 6;
								$LTmpTypBez = $TypTyp6;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == false) { # Falls Vorlauf und Zwischenlauf
								$LTmpTypNr 	= 7;
								$LTmpTypBez = $TypTyp7;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == true && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Weder Vorlauf noch Zwischenlauf
							
								$LTmpTypNr 	= 5;
								$LTmpTypBez = $TypTyp5;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
	
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  6,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;												

						case "l": #ABFinale
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Vorlauf aber keine Zwischenläufe
							
								$LTmpTypNr 	= 6;
								$LTmpTypBez = $TypTyp6;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == false) { # Falls Vorlauf und Zwischenlauf
								$LTmpTypNr 	= 7;
								$LTmpTypBez = $TypTyp7;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == true && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Weder Vorlauf noch Zwischenlauf
							
								$LTmpTypNr 	= 5;
								$LTmpTypBez = $TypTyp5;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
	
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  7,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;	

							case "m": #nur lauf nr.
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Vorlauf aber keine Zwischenläufe
							
								$LTmpTypNr 	= 6;
								$LTmpTypBez = $TypTyp6;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == false && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == false) { # Falls Vorlauf und Zwischenlauf
								$LTmpTypNr 	= 7;
								$LTmpTypBez = $TypTyp7;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
							if(empty($Wettbew[$LWkList2Zeile['WettbewNr']]['VorlaufZeit']) == true && empty($Wettbew[$LWkList2Zeile['WettbewNr']]['ZwischenlaufZeit']) == true) { # Falls Weder Vorlauf noch Zwischenlauf
							
								$LTmpTypNr 	= 5;
								$LTmpTypBez = $TypTyp5;
								$LTmpDatei = "";
								$LTmpaktualisiert = filemtime($dat_wklist);
							
							}
	
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."nur"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  8,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;
							
				
				} # Trennung nach Rundentyp
		
		
		
			break;
			
			case "h": # hoch
			case "t": # technisch
			
			switch($LWkList2Zeile['RundeTyp']) { # Trennung nach Rundentyp
			
				case "r": # Ausscheidung
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."v"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  4,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;
				
				
				case "s": # Ausscheidung
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."v"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  5,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
											
											
							break;
							
				case "n": # finale hoch
				
				if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
				
				
				break;
				case "q": # finale technisch
					
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2)) { # Startliste existiert
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = $TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2;
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm")) { # Teilnehmerliste existiert
							$LTmpTypNr 	= 2;
							$LTmpTypBez = $TypTyp2;
							$LTmpDatei = "t".$LWkList2Zeile['COSANr'].".htm";
							$LTmpaktualisiert = filemtime($LTmpDatei);
						}
						if(file_exists($TmpIPCFilenameStartList1 . $LWkList2Zeile['RundeTyp'] . $TmpIPCFilenameStartList2) == false && file_exists("t".$LWkList2Zeile['COSANr'].".htm") == false) { # Startliste und Teilnehmerliste existieren NICHT
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
					
					
						$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  0,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
		
							break;
				break;

			}

			break;
			
			case "md": # mehrkampf
			
			if($LWkList2Zeile['COSANr'] !== $Wettbew['COSANr'] && empty($LWkList2Zeile['Startzeit']) == false) {
			
			#echo "s-".$LWkList2Zeile['WettbewTyp']."-".$LWkList2Zeile['WettbewNr']."-".$LWkList2Zeile['COSANr']."-".$LWkList2Zeile['Riege']."-".$LWkList2Zeile['RundeTyp'].".htm";
			
						if(file_exists("s-".$LWkList2Zeile['WettbewTyp']."-".$LWkList2Zeile['WettbewNr']."-".$LWkList2Zeile['COSANr']."-".$LWkList2Zeile['Riege']."-".$LWkList2Zeile['RundeTyp'].".htm")) {
							
							$LTmpTypNr 	= 4;
							$LTmpTypBez = $TypTyp4;
							$LTmpDatei = "s-".$LWkList2Zeile['WettbewTyp']."-".$LWkList2Zeile['WettbewNr']."-".$LWkList2Zeile['COSANr']."-".$LWkList2Zeile['Riege']."-".$LWkList2Zeile['RundeTyp'].".htm";
							$LTmpaktualisiert = filemtime("s-".$LWkList2Zeile['WettbewTyp']."-".$LWkList2Zeile['WettbewNr']."-".$LWkList2Zeile['COSANr']."-".$LWkList2Zeile['Riege']."-".$LWkList2Zeile['RundeTyp'].".htm");
							
						}
						else {
							$LTmpTypNr 	= 5;
							$LTmpTypBez = $TypTyp5;
							$LTmpDatei = "";
							$LTmpaktualisiert = filemtime($dat_wklist);
						}
						
						# Starttag aus Startdatum ermitteln
						switch ($LWkList2Zeile['Startdatum']) {
						
							case $tage[1]:
								$LTmpStarttag = 1;
							break;
							case $tage[2]:
								$LTmpStarttag = 2;
							break;
							case $tage[3]:
								$LTmpStarttag = 3;
							break;
							case $tage[4]:
								$LTmpStarttag = 4;
							break;
							default:
								$LTmpStarttag = 1;
							break;
						}
						
						
					
						#$ZeitplanRunden[$LWkList2Zeile['WettbewNr']."f"] = 1;
						
						# Zeitplanarray schreiben
						$Zeitplan[] = array(	'WettbewerbNr'			=>	$LWkList2Zeile['WettbewNr'],
											'DISBez'				=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['DISBez'],
											'WettbewerbTyp'			=>	$LWkList2Zeile['WettbewTyp'],
											'AKBez'					=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['AKBez'],
											'WettbewerbBez'			=>  $Wettbew[$LWkList2Zeile['WettbewNr']]['WettbewBez'],
											'TeilnStaffeln'			=>  $LWkList2Zeile['Teilnehmer'],
											'LaeufeGruppen'			=>  $LWkList2Zeile['AnzahlLaeufeGruppen'],
											'MindTeilnStSiegertext'	=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['MindTeilnStSiegertext'],
											'StellplatzMin'			=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzMin'],
											'StellplatzZeit'		=>	$Wettbew[$LWkList2Zeile['WettbewNr']]['StellplatzZeit'],
											'COSANr'				=>  $LWkList2Zeile['COSANr'],
											'StartZeit'				=>  $LWkList2Zeile['Startzeit'], # Noch überprüfüfungg einbauen
											'StartTag'				=>	$LTmpStarttag,
											'RundeNr'				=>  9,
											'RundeBez'				=> 	$LWkList2Zeile['RundeBez'],
											'TypNr'					=>	$LTmpTypNr,
											'TypBez'				=> 	$LTmpTypBez,
											'aktualisiert'			=> 	$LTmpaktualisiert,
											'Gemischt'				=> 	$LWkList2Zeile['Gemischt'],
											'Datei'					=>  $LTmpDatei);
		
							
			}
			break;
		
		
		
		
		} # Trennung nach WettbewertsTyp
		
		
		} # DSB / IPC Mode Ende
	
	
	
	
	} # Ende Falls nicht schon durch Endli erzeugt




} # Master Ende WkList2	
	
	
	#print_r($Zeitplan);
	
	
#######################################################################################
## Wettbew-Array ausgeben um den Zeitplan zu erstellen ################################
#######################################################################################
foreach($Wettbew as $WettbewZeile) {

# Daten in Variablen
				$WettbewerbNr 			= trim($WettbewZeile['WettbewNr']);
				$WettbewerbBez 			= $WettbewZeile['WettbewBez'];
				
				if($WettbewZeile['WettbewTyp'] == "s") {
				$TeilnStaffeln 			= $StTeilnehmeranzahlWb[$WettbewerbNr];
				}
				else {
				$TeilnStaffeln 			= $TeilnehmeranzahlWb[$WettbewerbNr];
				}
				$MindTeilnStSiegertext 	= "";
				$StellplatzMin			= $WettbewZeile['StellplatzMin'];
				$StellplatzZeit			= $WettbewZeile['StellplatzZeit'];
				$VorlaufZeit			= $WettbewZeile['VorlaufZeit'];
				$VorlaufTag				= $WettbewZeile['VorlaufTag'];
				$ZwischenlaufZeit		= $WettbewZeile['ZwischenlaufZeit'];
				$ZwischenlaufTag		= $WettbewZeile['ZwischenlaufTag'];
				$FinaleZeit				= $WettbewZeile['FinaleZeit'];
				$FinaleTag				= $WettbewZeile['FinaleTag'];
				$COSANrAK				= $WettbewZeile['COSANrAK'];
				$COSANrDIS				= $WettbewZeile['COSANrDIS'];
				$COSANr					= $COSANrAK.$COSANrDIS;
				$DISBez					= $WettbewZeile['DISBez'];
				$AKBez					= $WettbewZeile['AKBez'];
				$WettbewerbTyp			= $WettbewZeile['WettbewTyp'];

				
				
				
				
				
				if(empty($WettbewerbBez) == false) {
				
					if(empty($VorlaufZeit) == false) {
					
					
						
						#Vorrunden
						$StartZeit = $VorlaufZeit;
						$StartTag = $VorlaufTag;
						
							# Auf Vorhandensein von bestimmten Dateien prüfen:
							$datei_teilnehmer 	= "t".$COSANr.".htm";
							$datei_vorlauf 		= $COSANr."1.htm";
							$datei_zeitvorlauf 	= $COSANr."3.htm";
							$datei_startliste_vorlauf 		= "s".$COSANr."a.htm";
							$datei_startliste_zeitvorlauf 	= "s".$COSANr."e.htm";
							$datei_startliste_ausscheidungslauf 	= "s".$COSANr."c.htm";
							$datei_startliste_nurlaufnr 	= "s".$COSANr."m.htm";
							
							
							if(file_exists($datei_vorlauf)) {
							
								#Vorlauf
								$RundeNr 	= 1;
								$RundeBez 	= $RundeTyp1;
								
								$TypNr		= 1;
								$TypBez		= $TypTyp1;
								
								$aktualisiert = filemtime($datei_vorlauf);
								
								# Ausgabe in Array --- Vorlauf
								if($ZeitplanRunden[$WettbewerbNr."v"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_vorlauf);
								}	
							}
							
							if(file_exists($datei_zeitvorlauf)) {
							
								#Zeitvorlauf
								$RundeNr 	= 3;
								$RundeBez 	= $RundeTyp3;
								
								$TypNr		= 1;
								$TypBez		= $TypTyp1;
								
								$aktualisiert = filemtime($datei_zeitvorlauf);
								
								# Ausgabe in Array --- Zeitvorlauf
								if($ZeitplanRunden[$WettbewerbNr."v"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_zeitvorlauf);
								}
							}
							
							
							if(file_exists($datei_zeitvorlauf)) {
							
								#Zeitvorlauf
								$RundeNr 	= 3;
								$RundeBez 	= $RundeTyp3;
								
								$TypNr		= 1;
								$TypBez		= $TypTyp1;
								
								$aktualisiert = filemtime($datei_zeitvorlauf);
								
								# Ausgabe in Array --- Zeitvorlauf
								if($ZeitplanRunden[$WettbewerbNr."v"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_zeitvorlauf);
								}
							}
							if(file_exists($datei_startliste_vorlauf) && file_exists($datei_vorlauf) == FALSE && file_exists($datei_zeitvorlauf) == FALSE) {
							
								#Startliste Vorlauf
								$RundeNr 	= 1;
								$RundeBez 	= $RundeTyp1;
								
								$TypNr		= 4;
								$TypBez		= $TypTyp4;
								
								$aktualisiert = filemtime($datei_startliste_vorlauf);
								
								# Ausgabe in Array --- Vorlauf
								if($ZeitplanRunden[$WettbewerbNr."v"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_vorlauf);
								}	
							}
							if(file_exists($datei_startliste_zeitvorlauf) && file_exists($datei_vorlauf) == FALSE && file_exists($datei_zeitvorlauf) == FALSE) {
							
								#Startliste Zeit-Vorlauf
								$RundeNr 	= 3;
								$RundeBez 	= $RundeTyp3;
								
								$TypNr		= 4;
								$TypBez		= $TypTyp4;
								
								$aktualisiert = filemtime($datei_startliste_zeitvorlauf);
								
								# Ausgabe in Array --- zeitVorlauf
								if($ZeitplanRunden[$WettbewerbNr."v"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_zeitvorlauf);
								}	
							}
							
							if(file_exists($datei_startliste_ausscheidungslauf) && file_exists($datei_vorlauf) == FALSE && file_exists($datei_zeitvorlauf) == FALSE) {
							
								#Startliste Vorlauf
								$RundeNr 	= 4;
								$RundeBez 	= $RundeTyp4;
								
								$TypNr		= 4;
								$TypBez		= $TypTyp4;
								
								$aktualisiert = filemtime($datei_startliste_ausscheidungslauf);
								
								# Ausgabe in Array --- Vorlauf
								if($ZeitplanRunden[$WettbewerbNr."v"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_ausscheidungslauf);
								}	
							}
							
							if(file_exists($datei_startliste_nurlaufnr)) {
							
								#Startliste nur Lauf-Nr.
								$RundeNr 	= 8;
								$RundeBez 	= $RundeTyp8;
								
								$TypNr		= 4;
								$TypBez		= $TypTyp4;
								
								$aktualisiert = filemtime($datei_startliste_nurlaufnr);
								
								# Ausgabe in Array --- Vorlauf
								if($ZeitplanRunden[$WettbewerbNr."nur"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_nurlaufnr);
								}	
							}
							
							
							
							if(file_exists($datei_teilnehmer) && file_exists($datei_vorlauf) == FALSE && file_exists($datei_zeitvorlauf) == FALSE && file_exists($datei_startliste_vorlauf) == FALSE && file_exists($datei_startliste_zeitvorlauf) == FALSE && file_exists($datei_startliste_ausscheidungslauf) == FALSE) {
							
								#Teilnehmer
								$RundeNr 	= 1;
								$RundeBez 	= $RundeTyp1;
								
								$TypNr		= 2;
								$TypBez		= $TypTyp2;
								
								$aktualisiert = filemtime($datei_teilnehmer);
								
								# Ausgabe in Array --- Vorlauf
								if($ZeitplanRunden[$WettbewerbNr."v"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_teilnehmer);
								}	
							}
							if(file_exists($datei_teilnehmer) == FALSE && file_exists($datei_vorlauf) == FALSE && file_exists($datei_zeitvorlauf) == FALSE && file_exists($datei_startliste_vorlauf) == FALSE && file_exists($datei_startliste_zeitvorlauf) == FALSE && file_exists($datei_startliste_ausscheidungslauf) == FALSE) {
							
								#keine Datei vorhanden
								$RundeNr 	= 1;
								$RundeBez 	= $RundeTyp1;
								
								$TypNr		= 5;
								$TypBez		= $TypTyp5;
								
								$aktualisiert = filemtime($zeitplandatei);
								
								# Ausgabe in Array --- Zeitvorlauf
								if($ZeitplanRunden[$WettbewerbNr."v"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	"");
								}
							}					

					
					}
					
					if(empty($ZwischenlaufZeit) == false) {
						
						#Zwischenläufe
						$StartZeit = $ZwischenlaufZeit;
						$StartTag = $ZwischenlaufTag;
						
							# Auf Vorhandensein von bestimmten Dateien prüfen:
							$datei_teilnehmer 	= "t".$COSANr.".htm";
							$datei_vorlauf 		= $COSANr."1.htm";
							$datei_zeitvorlauf 	= $COSANr."3.htm";
							$datei_zwischenlauf 	= $COSANr."2.htm";
							$datei_startliste_zwischenlauf = "s".$COSANr."b.htm";
					
					
						if(file_exists($datei_zwischenlauf)) {
							
								#Zwischenlauf
								$RundeNr 	= 2;
								$RundeBez 	= $RundeTyp2;
								
								$TypNr		= 1;
								$TypBez		= $TypTyp1;
								
								$aktualisiert = filemtime($datei_zwischenlauf);
								
								if(empty($VorlaufZeit) == false) {
								$TmpStellplatzMin = "";
								$TmpStellplatzZeit = "";
								$TmpTeilnStaffeln = "";
								}
								
								# Ausgabe in Array --- Zwischenlauf
								if($ZeitplanRunden[$WettbewerbNr."z"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TmpTeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$TmpStellplatzMin,
														'StellplatzZeit'		=>	$TmpStellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_zwischenlauf);
								}	
							}
							else {
							if(file_exists($datei_startliste_zwischenlauf)) {
							
								#Zwischenlauf
								$RundeNr 	= 2;
								$RundeBez 	= $RundeTyp2;
								
								$TypNr		= 4;
								$TypBez		= $TypTyp4;
								
								$aktualisiert = filemtime($datei_startliste_zwischenlauf);
								
								if(empty($VorlaufZeit) == false) {
								$TmpStellplatzMin = "";
								$TmpStellplatzZeit = "";
								$TmpTeilnStaffeln = "";
								}
								
								# Ausgabe in Array --- Zwischenlauf
								if($ZeitplanRunden[$WettbewerbNr."z"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TmpTeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$TmpStellplatzMin,
														'StellplatzZeit'		=>	$TmpStellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_zwischenlauf);
								}	
							}
							
							
							
							
							
							else {
							# Wenn noch keine Ergebnisse vorhanden
							#keine Datei vorhanden
								$RundeNr 	= 2;
								$RundeBez 	= $RundeTyp2;
								
								$TypNr		= 6;
								$TypBez		= $TypTyp6;
								
								$aktualisiert = filemtime($zeitplandatei);
								
								# Ausgabe in Array --- Zwischenlauf
								if($ZeitplanRunden[$WettbewerbNr."z"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TmpTeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$TmpStellplatzMin,
														'StellplatzZeit'		=>	$TmpStellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	"");
								}
							}
							}
					
					
					
					}
					
					
					if(empty($FinaleZeit) == false) {
					# Finale
					
							# Auf Vorhandensein von bestimmten Dateien prüfen:
							$datei_teilnehmer 		= "t".$COSANr.".htm";
							$datei_vorlauf 			= $COSANr."1.htm";
							$datei_zeitvorlauf 		= $COSANr."3.htm";
							$datei_zwischenlauf 	= $COSANr."2.htm";
							$datei_finale		 	= $COSANr.".htm";
							$datei_finale_zwischen	= $COSANr."z.htm";
							$datei_disziplina		= $COSANr."a.htm";
							$datei_disziplinb		= $COSANr."b.htm";
							$datei_disziplinc		= $COSANr."c.htm";
							$datei_disziplind		= $COSANr."d.htm";
							$datei_diszipline		= $COSANr."e.htm";
							$datei_disziplinf		= $COSANr."f.htm";
							$datei_diszipling		= $COSANr."g.htm";
							$datei_disziplinh		= $COSANr."h.htm";
							$datei_disziplini		= $COSANr."i.htm";
							$datei_startliste_finale = "s".$COSANr."k.htm";
							$datei_startliste_zeitfinale = "s".$COSANr."d.htm";
							$datei_startliste_abfinale = "s".$COSANr."l.htm";
							$datei_startliste_finalehoch = "s".$COSANr."n.htm";
							$datei_startliste_finaletechnisch = "s".$COSANr."q.htm";
							
							#Startzeiten
							$StartZeit = $FinaleZeit;
							$StartTag = $FinaleTag;
							
							#Runden
							$RundeNr 	= 0;
							$RundeBez 	= $RundeTyp0;
							
					
						if(empty($VorlaufZeit) && empty($ZwischenlaufZeit)) {
						
							# Finale als Einzige Runde
							

							if(file_exists($datei_finale) || file_exists($datei_finale_zwischen) || file_exists($datei_disziplina) || file_exists($datei_disziplinb) || file_exists($datei_disziplinc) || file_exists($datei_disziplind) || file_exists($datei_diszipline) || file_exists($datei_disziplinf) || file_exists($datei_diszipling) || file_exists($datei_disziplinh) || file_exists($datei_disziplini)) {
							
								# Überprüfen, ob irgendwelche Dateien vorhanden sind
								
								if(file_exists($datei_finale)) {
								
									# Überprüfen, ob Ergebnisdatei Finale vorhanden ist
									
									$TypNr		= 1;
									$TypBez		= $TypTyp1;
								
									$aktualisiert = filemtime($datei_finale);
								
									# Ausgabe in Array --- Finale
									if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
									$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_finale);
	
									
									# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_finale);
									}
										
									}
								} # Überprüfen, ob Ergebnisdatei Finale vorhanden ist
								
								else  {
									# Falls Ergebnisdatei Finale nicht vorhanden ist, aber Zwischenergebnisse oder Disziplinen 
								
									if(file_exists($datei_finale_zwischen)) {
										# Überprüfen, ob Zwischenergebnis vorhanden ist
										
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
								
										$aktualisiert = filemtime($datei_finale_zwischen);
								
										# Ausgabe in Array --- Zwischenergebnis
										if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_finale_zwischen);
										
										# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_finale_zwischen);
									}
										}
									}
									
									if(file_exists($datei_disziplina) && file_exists($datei_disziplinb) == false && file_exists($datei_disziplinc) == false && file_exists($datei_disziplind) == false  && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "a" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "a";
										$RundeBez	= $RundeTypa;
								
										$aktualisiert = filemtime($datei_disziplina);
								
										# Ausgabe in Array --- Disziplina
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplina);
									
										# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_disziplina);
									}
									}
									
									if(file_exists($datei_disziplinb) && file_exists($datei_disziplinc) == false && file_exists($datei_disziplind) == false  && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "b" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "b";
										$RundeBez	= $RundeTypb;
								
										$aktualisiert = filemtime($datei_disziplinb);
								
										# Ausgabe in Array --- Disziplinb
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinb);
									
										# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_disziplinb);
									}
									}
									
									if(file_exists($datei_disziplinc) && file_exists($datei_disziplind) == false  && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "c" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "c";
										$RundeBez	= $RundeTypc;
								
										$aktualisiert = filemtime($datei_disziplinc);
								
										# Ausgabe in Array --- Disziplinc
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinc);
										# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_disziplinc);
									}
									}
									
									if(file_exists($datei_disziplind) && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "d" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "d";
										$RundeBez	= $RundeTypd;
								
										$aktualisiert = filemtime($datei_disziplind);
								
										# Ausgabe in Array --- Disziplind
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplind);
										# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_disziplind);
									}
									}
									
									if(file_exists($datei_diszipline) && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "e" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "e";
										$RundeBez	= $RundeType;
								
										$aktualisiert = filemtime($datei_diszipline);
								
										# Ausgabe in Array --- Diszipline
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_diszipline);
									
										# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_diszipline);
									}
									}
									
									if(file_exists($datei_disziplinf) && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "f" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "f";
										$RundeBez	= $RundeTypf;
								
										$aktualisiert = filemtime($datei_disziplinf);
								
										# Ausgabe in Array --- Disziplinf
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinf);
									
										# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_disziplinf);
									}
									}
									
									if(file_exists($datei_diszipling) && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "g" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "g";
										$RundeBez	= $RundeTypg;
								
										$aktualisiert = filemtime($datei_diszipling);
								
										# Ausgabe in Array --- Diszipling
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_diszipling);
									
										# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_diszipling);
									}
									}
									
									if(file_exists($datei_disziplinh) && file_exists($datei_disziplini) == false) {
										# Disziplin "h" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "h";
										$RundeBez	= $RundeTyph;
								
										$aktualisiert = filemtime($datei_disziplinh);
								
										# Ausgabe in Array --- Disziplinh
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinh);
									
										# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_disziplinh);
									}
									}
									
									if(file_exists($datei_disziplini)) {
										# Disziplin "i" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "i";
										$RundeBez	= $RundeTypi;
								
										$aktualisiert = filemtime($datei_disziplini);
								
										# Ausgabe in Array --- Disziplini
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplini);
									
										# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_disziplini);
									}
									}

								} # Falls Ergebnisdatei Finale nicht vorhanden ist, aber Zwischenergebnisse oder Disziplinen

							} # Dateien existieren
							
							else {
							
								if(file_exists($datei_startliste_finale) || file_exists($datei_startliste_zeitfinale) || file_exists($datei_startliste_abfinale) || file_exists($datei_startliste_finaletechnisch) || file_exists($datei_startliste_finalehoch)){
								
									if(file_exists($datei_startliste_finale)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
								
								$aktualisiert = filemtime($datei_startliste_finale);
								
								# Ausgabe in Array --- Finale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_finale);
							
							
								}
								}
								if(file_exists($datei_startliste_zeitfinale)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 6;
									$RundeBez = $RundeTyp6;
								
								$aktualisiert = filemtime($datei_startliste_zeitfinale);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_zeitfinale);
							
							
								}
								}
								if(file_exists($datei_startliste_abfinale)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 7;
									$RundeBez = $RundeTyp7;
								
								$aktualisiert = filemtime($datei_startliste_abfinale);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_abfinale);
							
							
								}
								}
								
								if(file_exists($datei_startliste_finalehoch)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
								
								$aktualisiert = filemtime($datei_startliste_finalehoch);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_finalehoch);
							
							
								}
								}
								if(file_exists($datei_startliste_finaletechnisch)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
								
								$aktualisiert = filemtime($datei_startliste_finaletechnisch);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_finaletechnisch);
							
							
								}
								}
								
								
								}
								
							
								else {
								if(file_exists($datei_teilnehmer) && file_exists($datei_startliste_finale) == FALSE) {
							
										# Teilnehmer vorhanden
							
									$TypNr		= 2;
									$TypBez		= $TypTyp2;
								
								$aktualisiert = filemtime($datei_teilnehmer);
								
								# Ausgabe in Array --- Finale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_teilnehmer);
							
									# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	$datei_teilnehmer);
									}
								}
							
								}
								else {
						
								
								# Falls keine Datei vorhanden ist
								#keine Datei vorhanden
								
								
								$TypNr		= 5;
								$TypBez		= $TypTyp5;
								
								$aktualisiert = filemtime($zeitplandatei);
								
								# Ausgabe in Array --- Finale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	"");
									# Für Besondere Ausgabe oberhalt des Zeitplans
									if($WettbewZeile['WettbewTyp'] == "m" && $WettbewerbeOberhalbZeitplanAn == 1) {
										$WettbewerbeOberhalbZeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
															'DISBez'				=>	$DISBez,
															'AKBez'					=>	$AKBez,
															'WettbewerbBez'			=>	$WettbewerbBez,
															'WettbewerbTyp'			=>	$WettbewerbTyp,
															'TeilnStaffeln'			=>	$TeilnStaffeln,
															'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
															'StellplatzMin'			=>	$StellplatzMin,
															'StellplatzZeit'		=>	$StellplatzZeit,
															'COSANr'				=>	$COSANr,
															'StartZeit'				=>	$StartZeit,
															'StartTag'				=>	$StartTag,
															'RundeNr'				=>	$RundeNr,
															'RundeBez'				=>	$RundeBez,
															'TypNr'					=>	$TypNr,
															'TypBez'				=>	$TypBez,
															'aktualisiert'			=>	$aktualisiert,
															'Datei'					=>	"");
									}
							
								}
							
							} # Falls keine Datei vorhanden ist (else)
							
							}
						
							}
						
						
						}		# Finale Einzige Runde				
						else {
						
							# Final als letzte Runde aus vorherigen Vor- und/oder Zwischenläufen
							
							if(empty($ZwischenlaufZeit) == false) {
								# Falls Zwischenlaufzeit nicht leer ist
								
									if(file_exists($datei_finale) || file_exists($datei_finale_zwischen) || file_exists($datei_disziplina) || file_exists($datei_disziplinb) || file_exists($datei_disziplinc) || file_exists($datei_disziplind) || file_exists($datei_diszipline) || file_exists($datei_disziplinf) || file_exists($datei_diszipling) || file_exists($datei_disziplinh) || file_exists($datei_disziplini)) {
										# Falls eine der Ergebnisdateien existiert
										
										if(file_exists($datei_finale)) {
										# Überprüfen, ob Ergebnis vorhanden ist
										
										$TypNr		= 1;
										$TypBez		= $TypTyp1;
								
										$aktualisiert = filemtime($datei_finale);
								
										# Ausgabe in Array --- Finale
										if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_finale);
										
									
										}
									}
										
										if(file_exists($datei_finale_zwischen) && file_exists($datei_finale) == false) {
										# Überprüfen, ob Zwischenergebnis vorhanden ist
										
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
								
										$aktualisiert = filemtime($datei_finale_zwischen);
								
										# Ausgabe in Array --- Zwischenergebnis
										if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_finale_zwischen);
										
									
										}
									}
									
									if(file_exists($datei_disziplina) && file_exists($datei_disziplinb) == false && file_exists($datei_disziplinc) == false && file_exists($datei_disziplind) == false  && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "a" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "a";
										$RundeBez	= $RundeTypa;
								
										$aktualisiert = filemtime($datei_disziplina);
								
										# Ausgabe in Array --- Disziplina
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplina);
									
									}
									
									if(file_exists($datei_disziplinb) && file_exists($datei_disziplinc) == false && file_exists($datei_disziplind) == false  && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "b" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "b";
										$RundeBez	= $RundeTypb;
								
										$aktualisiert = filemtime($datei_disziplinb);
								
										# Ausgabe in Array --- Disziplinb
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinb);
									
									}
									
									if(file_exists($datei_disziplinc) && file_exists($datei_disziplind) == false  && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "c" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "c";
										$RundeBez	= $RundeTypc;
								
										$aktualisiert = filemtime($datei_disziplinc);
								
										# Ausgabe in Array --- Disziplinc
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinc);
									
									}
									
									if(file_exists($datei_disziplind) && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "d" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "d";
										$RundeBez	= $RundeTypd;
								
										$aktualisiert = filemtime($datei_disziplind);
								
										# Ausgabe in Array --- Disziplind
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplind);
									
									}
									
									if(file_exists($datei_diszipline) && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "e" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "e";
										$RundeBez	= $RundeType;
								
										$aktualisiert = filemtime($datei_diszipline);
								
										# Ausgabe in Array --- Diszipline
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_diszipline);
									
									}
									
									if(file_exists($datei_disziplinf) && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "f" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "f";
										$RundeBez	= $RundeTypf;
								
										$aktualisiert = filemtime($datei_disziplinf);
								
										# Ausgabe in Array --- Disziplinf
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinf);
									
									}
									
									if(file_exists($datei_diszipling) && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "g" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "g";
										$RundeBez	= $RundeTypg;
								
										$aktualisiert = filemtime($datei_diszipling);
								
										# Ausgabe in Array --- Diszipling
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_diszipling);
									
									}
									
									if(file_exists($datei_disziplinh) && file_exists($datei_disziplini) == false) {
										# Disziplin "h" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "h";
										$RundeBez	= $RundeTyph;
								
										$aktualisiert = filemtime($datei_disziplinh);
								
										# Ausgabe in Array --- Disziplinh
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinh);
									
									}
									
									if(file_exists($datei_disziplini)) {
										# Disziplin "i" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "i";
										$RundeBez	= $RundeTypi;
								
										$aktualisiert = filemtime($datei_disziplini);
								
										# Ausgabe in Array --- Disziplini
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplini);
									
									}

									} # Falls eine der Ergebnisdateien existiert
								
								
									else { # Startlisten
									
									if(file_exists($datei_startliste_finale) || file_exists($datei_startliste_zeitfinale) || file_exists($datei_startliste_abfinale) || file_exists($datei_startliste_finaletechnisch) || file_exists($datei_startliste_finalehoch)){
									
									if(file_exists($datei_startliste_finale)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
								
								$aktualisiert = filemtime($datei_startliste_finale);
								
								# Ausgabe in Array --- Finale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_finale);
							
								}
							
								}
								if(file_exists($datei_startliste_zeitfinale)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 6;
									$RundeBez = $RundeTyp6;
								
								$aktualisiert = filemtime($datei_startliste_zeitfinale);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_zeitfinale);
								}
							
							
								}
								if(file_exists($datei_startliste_abfinale)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 7;
									$RundeBez = $RundeTyp7;
								
								$aktualisiert = filemtime($datei_startliste_abfinale);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_abfinale);
								}
							
								
								}
								
								if(file_exists($datei_startliste_finalehoch)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
								
								$aktualisiert = filemtime($datei_startliste_finalehoch);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_finalehoch);
							
								}
								
								}
								if(file_exists($datei_startliste_finaletechnisch)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
								
								$aktualisiert = filemtime($datei_startliste_finaletechnisch);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_finaletechnisch);
								}
							
								
								}
										
									
									
									}
								
					
									else { # Ergebnisdatei existiert nicht
									
											$TypNr		= 7;
											$TypBez		= $TypTyp7;
								
											$aktualisiert = filemtime($zeitplandatei);
								
											# Ausgabe in Array --- Finale
											if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
											$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																	'DISBez'				=>	$DISBez,
																	'AKBez'					=>	$AKBez,
																	'WettbewerbBez'			=>	$WettbewerbBez,
																	'WettbewerbTyp'			=>	$WettbewerbTyp,
																	'TeilnStaffeln'			=>	$TeilnStaffeln,
																	'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																	'StellplatzMin'			=>	$StellplatzMin,
																	'StellplatzZeit'		=>	$StellplatzZeit,
																	'COSANr'				=>	$COSANr,
																	'StartZeit'				=>	$StartZeit,
																	'StartTag'				=>	$StartTag,
																	'RundeNr'				=>	$RundeNr,
																	'RundeBez'				=>	$RundeBez,
																	'TypNr'					=>	$TypNr,
																	'TypBez'				=>	$TypBez,
																	'aktualisiert'			=>	$aktualisiert,
																	'Datei'					=>	"");
										}
									
									} # Ergebnisdatei existiert nicht
									}

							} # Falls Zwischenlaufzeit nicht leer ist
							
							
							else { # Falls zwischenlaufzeit leer ist --> Vorlauf
							
							if(file_exists($datei_finale) || file_exists($datei_finale_zwischen) || file_exists($datei_disziplina) || file_exists($datei_disziplinb) || file_exists($datei_disziplinc) || file_exists($datei_disziplind) || file_exists($datei_diszipline) || file_exists($datei_disziplinf) || file_exists($datei_diszipling) || file_exists($datei_disziplinh) || file_exists($datei_disziplini)) {
										# Falls eine der Ergebnisdateien existiert
										
										
										if(file_exists($datei_finale)) {
										# Überprüfen, ob Ergebnis vorhanden ist
										
										$TypNr		= 1;
										$TypBez		= $TypTyp1;
								
										$aktualisiert = filemtime($datei_finale);
								
										# Ausgabe in Array --- Finale
										if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_finale);
										
										}
									
									}
										
										
										if(file_exists($datei_finale_zwischen) && file_exists($datei_finale) == false) {
										# Überprüfen, ob Zwischenergebnis vorhanden ist
										
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
								
										$aktualisiert = filemtime($datei_finale_zwischen);
								
										# Ausgabe in Array --- Zwischenergebnis
										if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_finale_zwischen);
										
										}
									
									}
									
									if(file_exists($datei_disziplina) && file_exists($datei_disziplinb) == false && file_exists($datei_disziplinc) == false && file_exists($datei_disziplind) == false  && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "a" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "a";
										$RundeBez	= $RundeTypa;
								
										$aktualisiert = filemtime($datei_disziplina);
								
										# Ausgabe in Array --- Disziplina
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplina);
									
									}
									
									if(file_exists($datei_disziplinb) && file_exists($datei_disziplinc) == false && file_exists($datei_disziplind) == false  && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "b" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "b";
										$RundeBez	= $RundeTypb;
								
										$aktualisiert = filemtime($datei_disziplinb);
								
										# Ausgabe in Array --- Disziplinb
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinb);
									
									}
									
									if(file_exists($datei_disziplinc) && file_exists($datei_disziplind) == false  && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "c" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "c";
										$RundeBez	= $RundeTypc;
								
										$aktualisiert = filemtime($datei_disziplinc);
								
										# Ausgabe in Array --- Disziplinc
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinc);
									
									}
									
									if(file_exists($datei_disziplind) && file_exists($datei_diszipline) == false && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "d" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "d";
										$RundeBez	= $RundeTypd;
								
										$aktualisiert = filemtime($datei_disziplind);
								
										# Ausgabe in Array --- Disziplind
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplind);
									
									}
									
									if(file_exists($datei_diszipline) && file_exists($datei_disziplinf) == false && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "e" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "e";
										$RundeBez	= $RundeType;
								
										$aktualisiert = filemtime($datei_diszipline);
								
										# Ausgabe in Array --- Diszipline
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_diszipline);
									
									}
									
									if(file_exists($datei_disziplinf) && file_exists($datei_diszipling) == false && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "f" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "f";
										$RundeBez	= $RundeTypf;
								
										$aktualisiert = filemtime($datei_disziplinf);
								
										# Ausgabe in Array --- Disziplinf
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinf);
									
									}
									
									if(file_exists($datei_diszipling) && file_exists($datei_disziplinh) == false && file_exists($datei_disziplini) == false) {
										# Disziplin "g" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "g";
										$RundeBez	= $RundeTypg;
								
										$aktualisiert = filemtime($datei_diszipling);
								
										# Ausgabe in Array --- Diszipling
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_diszipling);
									
									}
									
									if(file_exists($datei_disziplinh) && file_exists($datei_disziplini) == false) {
										# Disziplin "h" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "h";
										$RundeBez	= $RundeTyph;
								
										$aktualisiert = filemtime($datei_disziplinh);
								
										# Ausgabe in Array --- Disziplinh
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplinh);
									
									}
									
									if(file_exists($datei_disziplini)) {
										# Disziplin "i" existiert, aber die anderen nicht
									
										$TypNr		= 3;
										$TypBez		= $TypTyp3;
										
										$RundeNr 	= "i";
										$RundeBez	= $RundeTypi;
								
										$aktualisiert = filemtime($datei_disziplini);
								
										# Ausgabe in Array --- Disziplini
										$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																'DISBez'				=>	$DISBez,
																'AKBez'					=>	$AKBez,
																'WettbewerbBez'			=>	$WettbewerbBez,
																'WettbewerbTyp'			=>	$WettbewerbTyp,
																'TeilnStaffeln'			=>	$TeilnStaffeln,
																'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																'StellplatzMin'			=>	$StellplatzMin,
																'StellplatzZeit'		=>	$StellplatzZeit,
																'COSANr'				=>	$COSANr,
																'StartZeit'				=>	$StartZeit,
																'StartTag'				=>	$StartTag,
																'RundeNr'				=>	$RundeNr,
																'RundeBez'				=>	$RundeBez,
																'TypNr'					=>	$TypNr,
																'TypBez'				=>	$TypBez,
																'aktualisiert'			=>	$aktualisiert,
																'Datei'					=>	$datei_disziplini);
									
									}
	
									
									} # Falls eine der Ergebnisdateien existiert
								
									else { # Startlisten
									if(file_exists($datei_startliste_finale) || file_exists($datei_startliste_zeitfinale) || file_exists($datei_startliste_abfinale) || file_exists($datei_startliste_finaletechnisch) || file_exists($datei_startliste_finalehoch)){
									
									if(file_exists($datei_startliste_finale)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
								
								$aktualisiert = filemtime($datei_startliste_finale);
								
								# Ausgabe in Array --- Finale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_finale);
							
								}
							
								}
								if(file_exists($datei_startliste_zeitfinale)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 6;
									$RundeBez = $RundeTyp6;
								
								$aktualisiert = filemtime($datei_startliste_zeitfinale);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_zeitfinale);
								}
							
							
								}
								if(file_exists($datei_startliste_abfinale)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 7;
									$RundeBez = $RundeTyp7;
								
								$aktualisiert = filemtime($datei_startliste_abfinale);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_abfinale);
								}
							
								
								}
								
								if(file_exists($datei_startliste_finalehoch)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
								
								$aktualisiert = filemtime($datei_startliste_finalehoch);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_finalehoch);
								}
							
								
								}
								if(file_exists($datei_startliste_finaletechnisch)) {
								
										# Startliste vorhanden
							
									$TypNr		= 4;
									$TypBez		= $TypTyp4;
									
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
								
								$aktualisiert = filemtime($datei_startliste_finaletechnisch);
								
								# Ausgabe in Array --- zeitFinale
								if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
								$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
														'DISBez'				=>	$DISBez,
														'AKBez'					=>	$AKBez,
														'WettbewerbBez'			=>	$WettbewerbBez,
														'WettbewerbTyp'			=>	$WettbewerbTyp,
														'TeilnStaffeln'			=>	$TeilnStaffeln,
														'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
														'StellplatzMin'			=>	$StellplatzMin,
														'StellplatzZeit'		=>	$StellplatzZeit,
														'COSANr'				=>	$COSANr,
														'StartZeit'				=>	$StartZeit,
														'StartTag'				=>	$StartTag,
														'RundeNr'				=>	$RundeNr,
														'RundeBez'				=>	$RundeBez,
														'TypNr'					=>	$TypNr,
														'TypBez'				=>	$TypBez,
														'aktualisiert'			=>	$aktualisiert,
														'Datei'					=>	$datei_startliste_finaletechnisch);
								}
							
								
								}
										
									
									
									}
								
								
									else { # Ergebnisdatei existiert nicht
									
											$TypNr		= 6;
											$TypBez		= $TypTyp6;
								
											$aktualisiert = filemtime($zeitplandatei);
								
											# Ausgabe in Array --- Finale
											if($ZeitplanRunden[$WettbewerbNr."f"] == 0) {
											$Zeitplan[] = array(	'WettbewerbNr'			=>	$WettbewerbNr,
																	'DISBez'				=>	$DISBez,
																	'AKBez'					=>	$AKBez,
																	'WettbewerbBez'			=>	$WettbewerbBez,
																	'WettbewerbTyp'			=>	$WettbewerbTyp,
																	'TeilnStaffeln'			=>	$TeilnStaffeln,
																	'MindTeilnStSiegertext'	=>	$MindTeilnStSiegertext,
																	'StellplatzMin'			=>	$StellplatzMin,
																	'StellplatzZeit'		=>	$StellplatzZeit,
																	'COSANr'				=>	$COSANr,
																	'StartZeit'				=>	$StartZeit,
																	'StartTag'				=>	$StartTag,
																	'RundeNr'				=>	$RundeNr,
																	'RundeBez'				=>	$RundeBez,
																	'TypNr'					=>	$TypNr,
																	'TypBez'				=>	$TypBez,
																	'aktualisiert'			=>	$aktualisiert,
																	'Datei'					=>	"");
									
										}
									} # Ergebnisdatei existiert nicht
									}

							} # Falls zwischenlaufzeit leer ist --> Vorlauf

						} # Final als letzte Runde aus vorherigen Vor- und/oder Zwischenläufen

					} # Finale
					
				} # WettbewerbBez nicht leer
							

} # Master

# Festlegen, welcher Tag ausgegeben werden soll
if($_GET["tag"] != "") {$starttag = $_GET["tag"];}


# Array (Zeitplan) sortieren nach Startzeit

foreach ($Zeitplan as $nr => $inhalt) {

	$TWettbewerbNr[$nr] = strtolower($inhalt['WettbewerbNr']);
	$TWettbewerbBez[$nr] = strtolower($inhalt['WettbewerbBez']);
	$TTeilnStaffeln[$nr] = strtolower($inhalt['TeilnStaffeln']);
	$TMindTeilnStSiegertext[$nr] = strtolower($inhalt['MindTeilnStSiegertext']);
	$TStellplatzMin[$nr] = strtolower($inhalt['StellplatzMin']);
	$TStellplatzZeit[$nr] = strtolower($inhalt['StellplatzZeit']);
	$TCOSANr[$nr] = strtolower($inhalt['COSANr']);
	$TStartZeit[$nr] = strtolower($inhalt['StartZeit'])."-".strtolower($inhalt['COSANr']);
	$TStartTag[$nr] = strtolower($inhalt['StartTag']);
	$TRundeNr[$nr] = strtolower($inhalt['RundeNr']);
	$TRundeBez[$nr] = strtolower($inhalt['RundeBez']);
	$TTypNr[$nr] = strtolower($inhalt['TypNr']);
	$TTypBez[$nr] = strtolower($inhalt['TypBez']);
	$TDatei[$nr] = strtolower($inhalt['Datei']);
	$TRoundTypeLetter[$nr] = strtolower($inhalt['RoundTypeLetter']);
	$Taktualisiert[$nr] = strtolower($inhalt['aktualisiert']);
}

array_multisort($TStartZeit, SORT_ASC, $Zeitplan);




#print_r($Zeitplan);



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
		$PokalGrFileArray[] = array (
															'Name'	=>	trim(substr($PokalGrFileInhalt, 3, 34)),
															'Typ'	=>	1,
															'ID'	=>	$PokalGrFilesCounter
														);
	
	}


$PokalGrFilesCounter++;
}
# Combined Cups
if(file_exists("laive_combinedcupscoring.txt")) {
									
						$CC_file_content = file("./laive_combinedcupscoring.txt");
							foreach($CC_file_content AS $CC_file_content_Line) {
								$CC_file_content_Line_Explode = explode(";", $CC_file_content_Line);
								$PokalGrFileArray[] = array (
															'Name'	=>	$CC_file_content_Line_Explode[1],
															'Typ'	=>	2,
															'ID'	=>	$CC_file_content_Line_Explode[0]
														);
							
							}
						}







?>

	<table class="laivemenu">
	<tr>
		
		<td class="linkliste">
			<ul class="secoundmenu">
				<?php 
						
					if(count($tage) > 1) {
					
						if(empty($tage[1]) == false) {
					
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=$dat_zeitplan&amp;tag=1#aktuell'>$tage[1] ($txt_tag 1)</a>
							</li>";   
						}
						if(empty($tage[2]) == false) {
					
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=$dat_zeitplan&amp;tag=2#aktuell'>$tage[2] ($txt_tag 2)</a>
							</li>";   
						}
						if(empty($tage[3]) == false) {
					
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=$dat_zeitplan&amp;tag=3#aktuell'>$tage[3] ($txt_tag 3)</a>
							</li>";   
						}
						if(empty($tage[4]) == false) {
					
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=$dat_zeitplan&amp;tag=4#aktuell'>$tage[4] ($txt_tag 4)</a>
							</li>";   
						}
						
					}
						
						?>

			</ul>
		</td>
		<td class="aktualisiert" align="right"><a class="aktualisiert"><?php echo $txt_kopf_zeitplanaktualisiert." ".date("d.m.y H:i", filemtime($dat_wettbew)); ?></a></td>
	</tr>
</table>

		<table class="body" cellspacing="0">
			<tr><td class="KopfZ1"><?php echo $Kopfzeile1; ?></td></tr>
			<tr><td class="KopfZ11"><?php echo $Kopfzeile2; ?></td></tr>
			<tr><td class="KopfZ12"><?php echo $Kopfzeile3; ?></td></tr>
			</table>



		<table class="body" cellspacing="0">
		<tr><td class="KopfZ21">
		
<?php
										if (count($tage) > 1) {
											echo $txt_zeitplan . " - " . $Wochentage[date("w", $TageUnix[$starttag])] .", ". $tage[$starttag]." (". $txt_tag . " " . $starttag . ")";
										}
										else {
											echo $txt_zeitplan . " - " . $Wochentage[date("w", $TageUnix[$starttag])] .", ". $tage[$starttag];
										}
?>
		</td></tr>
		</table>
		<br>
		<?php 
		
		# Show Cup Scorings
		
		
				$AnzahlInEventliste = count($PokalGrFileArray);
				$AnzahlEineSpalte = ceil($AnzahlInEventliste / 3);
				$Eventzaehler = 0;
				
				
			if($CupScoringON == 1 && $AnzahlInEventliste > 0) {
				print("<table class='body'><tr><td class='KopfZ2'>" . $txt_headline_cupscoring .  "</td></tr></table>");

				echo "<table class=''body'>";
		
				if($AnzahlInEventliste > 1) {
		
					echo "<tr><td class='blGrundLink'>";
			
					foreach($PokalGrFileArray as $PokalGrFileArrayKey => $PokalGrFileArrayItem) {
					
						switch($PokalGrFileArrayItem['Typ']) {
						
						case 1:
						
			
						if($Eventzaehler == $AnzahlEineSpalte || $Eventzaehler == $AnzahlEineSpalte * 2) {
				
							echo "</td>";
							echo "<td class='blGrundLink'>";
						}
				
				
						echo "<a href='?sub=cupscoring.php&amp;list=1&amp;cupID=" . str_pad($PokalGrFileArrayItem['ID'], 2, "0", STR_PAD_LEFT) . "'>" . $PokalGrFileArrayItem['Name'] . "</a><br>";
				
				
						if($Eventzaehler == $AnzahlInEventliste) {
				
							echo "</td>";

						}
				
						
			
						break;
			
						case 2: # Combined Cup
						
						if($Eventzaehler == $AnzahlEineSpalte || $Eventzaehler == $AnzahlEineSpalte * 2) {
				
							echo "</td>";
							echo "<td class='blGrundLink'>";
						}
				
				
						echo "<a href='?sub=cupscoring.php&amp;list=2&amp;ccupID=" . $PokalGrFileArrayItem['ID'] . "'>" . $PokalGrFileArrayItem['Name'] . "</a><br>";
				
				
						if($Eventzaehler == $AnzahlInEventliste) {
				
							echo "</td>";

						}
						
						
						break;
			
						} # switch
						$Eventzaehler++;
					}
					echo "</tr>";
		
				}
				else {
		
					foreach($PokalGrFileArray as $PokalGrFileArrayKey => $PokalGrFileArrayItem) {
					
					
					if($PokalGrFileArrayKey < 10) {
							$PokalGrFileArrayKeyTmp = "0" . $PokalGrFileArrayKey;
						}
						else {
							$PokalGrFileArrayKeyTmp = $PokalGrFileArrayKey;
						}
			
						if($Eventzaehler == $AnzahlEineSpalte || $Eventzaehler == $AnzahlEineSpalte * 2) {
				
							echo "</td>";
							echo "<td class='blGrundLink'>";
						}
	
						echo "<tr><td class='blGrund'>";
						echo "<a href='?sub=cupscoring.php&amp;list=1&amp;cupID=" . $PokalGrFileArrayKeyTmp . "'>" . $PokalGrFileArrayItem['Name'] . "</a><br>";
						echo "</td></tr>";

					}
				}
		
				echo "</table>";
			
			
				
			
			#echo "<hr>";
			}
		
		
		
			# Hinweis auf Teilnehmer nach Vereinen
			
				$ErsterTagZeitStunde = 07;
				$ErsterTagZeitMinute = 00;
				$ErsterTagZeitTag = substr($tage[1], 0, 2);
				$ErsterTagZeitMonat = substr($tage[1], 3, 2);
				$ErsterTagZeitJahr = substr($tage[1], 6, 4);
				$ErsterTagUnixtimestamp = mktime($ErsterTagZeitStunde, $ErsterTagZeitMinute, 00, $ErsterTagZeitMonat, $ErsterTagZeitTag, $ErsterTagZeitJahr);
			
			
			if($TeilnehmerlisteNachVereinenAufZeitplanseiteAn == 1 && file_exists($dat_stamm) && file_exists($dat_verein) && file_exists($dat_wbteiln) && file_exists("gesamtteilnehmer.php") && $ErsterTagUnixtimestamp > time()) {
				echo "<table class='bodynoprint' cellspacing='0'><tr><td class ='LinkTnVereine'><a href='?sub=gesamtteilnehmer.php&amp;list=3'>" . $TxtLinkEntriesByClubs . "</a></td></tr></table>";
			}
		
		
			
		
		
		
		
		
		
		
			
			# Besondere Wettbewerbe und Wertungen
			/*$v = 0;
			if(count($WettbewerbeOberhalbZeitplan) > 0) {
				
				echo "<table class='zeitplanOben'>";
				
				foreach($WettbewerbeOberhalbZeitplan as $WettbewerbeOberhalbZeitplanZeile) { #
				
				if($WettbewerbeOberhalbZeitplanZeile['StartTag'] < $starttag) {
				$v++;
					$classtd2 = "typoben".$WettbewerbeOberhalbZeitplanZeile['TypNr'];
					$classtd3 = "typ".$WettbewerbeOberhalbZeitplanZeile['TypNr'];
				
					echo "<tr>";
					
							echo "<td class ='zeitplanzeitOben'><a class='zeitplanzeit'></a></td>";
							
							echo "<td class ='zeitplanspalteklasseOben'><a class='zeitplanspalteklasse'";
							echo ">".$WettbewerbeOberhalbZeitplanZeile['AKBez']."</a></td>";
							
							echo "<td class ='zeitplanspaltedisziplinOben'><a class='timetable_row_type".$WettbewerbeOberhalbZeitplanZeile['TypNr']."'";
							echo ">".$WettbewerbeOberhalbZeitplanZeile['DISBez']."</a></td>";
							
							echo "<td class ='zeitplanspalterundeOben'><a class='timetable_row_type".$WettbewerbeOberhalbZeitplanZeile['TypNr']."'";
							echo ">".$WettbewerbeOberhalbZeitplanZeile['RundeBez']."</a></td>";
							
							echo "<td class ='".$classtd2."'><a class='".$classtd3."'";
							if($WettbewerbeOberhalbZeitplanZeile['Datei'] !== "" && file_exists($WettbewerbeOberhalbZeitplanZeile['Datei'])) { echo "href='?sub=".$WettbewerbeOberhalbZeitplanZeile['Datei']."'";}
							echo ">";
							echo $WettbewerbeOberhalbZeitplanZeile['TypBez']."</a>";
							echo"</td>";
							
							echo "<td class='meldungenOben'>";
							echo "</td>";
					
					
							echo "<td class ='zeitplanspalteaktuellOben'><a class='zeitplanspalteaktuell'>"."</a></td>";
					
							echo "</tr>";
					
				}
				}#
				
				
				echo "</table>";
				#if($v > 0) {echo "<br>";}
			
			
			}*/
		
		
		
			#Hinweis auf Zeitplanverzug. / Information about delay
			if($zeitplanverzug != 0) {echo "<p class='zeitplanverzug'><a class='zeitplanverzug'>$txt_hinweis_zeitplanverzug</a></p>";}
		
		?>
		<table class= "zeitplan">
			<thead>
					<tr>
						<th><? echo $txt_startzeit; ?></th>
						<th><? echo $txt_wettbewerb; ?></th>
						
						<th><? echo $txt_runde; ?></th>
						<th class="seperator"></th>
						<th class="timetableHeadCenter">
							<a href="#" class="tooltip"><img class="info" src="http://laive.de/images/info.png" alt="Information">
								<span>
									<b><? echo $TxtHeadKey ?></b>
									<table class ="keytable">
										<tr>
											<td class="typ2"><a class="typ2"><? echo $ListTypAbbrev[2] ?></a></td>
											<td class="left"><a class="typ2"><? echo $TypTyp2 ?></a></td>
										<tr>
										<tr>
											<td class="typ4"><a class="typ4"><? echo $ListTypAbbrev[4] ?></a></td>
											<td class="left"><a class="typ4"><? echo $TypTyp4 ?></a></td>
										<tr>
										<tr>
											<td class="typ3"><a class="typ3"><? echo $ListTypAbbrev[3] ?></a></td>
											<td class="left"><a class="typ3"><? echo $TypTyp3 ?></a></td>
										<tr>
										<tr>
											<td class="typ1"><a class="typ1"><? echo $ListTypAbbrev[1] ?></a></td>
											<td class="left"><a class="typ1"><? echo $TypTyp1 ?></a></td>
										<tr>
									</table>
								</span>
							</a>
						</th>
						<th class="seperator"></th>
						<th class="timetableHeadRight"><abbr title="<? echo $txt_headline_explanation_participansandteams; ?>"><? echo $txt_headline_abbrev_participansandteams; ?></abbr></th>
						<th class="timetableHeadRight"><abbr title="<? echo $txt_headline_explanation_heatsandgroups; ?>"><? echo $txt_headline_abbrev_heatsandgroups; ?></abbr></th>
						<th class="seperator"></th>
						<th><? echo $txt_aktualisiert; ?></th>
					</tr>
			</thead>
		
		
<?php
			$vorherigeUhrzeit = "";
			
			# Ausgabe jeder einzelnen Wettbewerbszeile
			$ZaehlerRunden = 0;
			foreach ($Zeitplan as $zeile) {
			
			if($zeile['StartTag'] == $starttag) {
			
			
					$classtd = "typ".$zeile['TypNr'];
			
				
				
				$ZaehlerRunden = $ZaehlerRunden + 1;
				
				#Aktuelle Wettbewerbe markieren
				$StartZeitStunde = substr($zeile['StartZeit'], 0, 2);
				$StartZeitMinute = substr($zeile['StartZeit'], 3, 2);
				$StartZeitTag = substr($tage[$zeile['StartTag']], 0, 2);
				$StartZeitMonat = substr($tage[$zeile['StartTag']], 3, 2);
				$StartZeitJahr = substr($tage[$zeile['StartTag']], 6, 4);
				$startzeitUnixtimestamp = mktime($StartZeitStunde, $StartZeitMinute, 00, $StartZeitMonat, $StartZeitTag, $StartZeitJahr);
				$startzeitUnixtimestamp = $startzeitUnixtimestamp + ($zeitplanverzug * 60);
				
				$serverzeitakt = time();
				$aktuellbeginn = $serverzeitakt - $startzeitaktuellminus;
				$aktuellende = $serverzeitakt + $startzeitaktuellplus;
				$IDForLiveEvent = 0;
				
				#Bestimmung, ab wann ein Wettbewerb nicht mehr als live angezeigt werden soll, obwohl noch keine Ergebnisliste vorliegt
				if($zeile['WettbewerbTyp'] == "m") {
					$DauerLiveEnde = 18000; # 5 Stunden in Sekunden
				}
				else {
					$DauerLiveEnde = 18000; # 5 Stunden in Sekunden
				}
			
				
			
				echo "<tr>";
				
					if($StartlistenerstellenAutomatischAn !== 1) {
				
					if($zeile['StartZeit'] != $vorherigeUhrzeit) {
					
						
			
						if(($startzeitUnixtimestamp  >= $aktuellbeginn AND $startzeitUnixtimestamp  <= $aktuellende) OR (file_exists($dat_wbteiln) == true AND empty($zeile['TeilnStaffeln']) == false AND $startzeitUnixtimestamp  <= $aktuellbeginn AND $zeile['TypNr'] !== 1 AND $startzeitUnixtimestamp + $DauerLiveEnde > $serverzeitakt)) {
						
						if($aktuellerWettbewerbAn == 1 && $zeile['WettbewerbTyp'] !== "m") {	
						
							echo "<td class ='zeitplanzeitaktuell'><a name='aktuell' class='zeitplanzeitaktuell'>".$zeile['StartZeit']."</a></td>";
							$IDForLiveEvent = 1;
							
						}
						else {
						
							if($zeile['TypNr'] == 4) {
								echo "<td class ='zeitplanzeitaktuell'><a name='aktuell' class='zeitplanzeitaktuell'>".$zeile['StartZeit']."</a></td>";
								$IDForLiveEvent = 1;
							}
							else {
								echo "<td class ='zeitplanzeit'><a class='zeitplanzeit'>".$zeile['StartZeit']."</a></td>";
								$IDForLiveEvent = 0;
							}
						}
						}
						else {
						
							echo "<td class ='zeitplanzeit'><a class='zeitplanzeit'>".$zeile['StartZeit']."</a></td>";
							$IDForLiveEvent = 0;
							
						
						}
						
			
			
						
					}
					else {
					
					if(($startzeitUnixtimestamp  >= $aktuellbeginn AND $startzeitUnixtimestamp  <= $aktuellende) OR (file_exists($dat_wbteiln) == true AND empty($zeile['TeilnStaffeln']) == false AND $startzeitUnixtimestamp  <= $aktuellbeginn AND $zeile['TypNr'] !== 1 AND $startzeitUnixtimestamp + $DauerLiveEnde > $serverzeitakt)) {
						
						if($aktuellerWettbewerbAn == 1 && $zeile['WettbewerbTyp'] !== "m") {	
						
							echo "<td class ='zeitplanzeitvorhandenaktuell'><a name='aktuell' class='zeitplanzeitvorhanden'> </a></td>";
							$IDForLiveEvent = 1;
							
						}
						else {
							if($zeile['TypNr'] == 4) {
								echo "<td class ='zeitplanzeitvorhandenaktuell'><a name='aktuell' class='zeitplanzeitvorhanden'> </a></td>";
								$IDForLiveEvent = 1;
							}
							else {
								echo "<td class ='zeitplanzeitvorhanden'><a class='zeitplanzeitvorhanden'> </a></td>";
								$IDForLiveEvent = 0;
							}
						}
						}
						else {
						
							echo "<td class ='zeitplanzeitvorhanden'><a class='zeitplanzeitvorhanden'> </a></td>";
							$IDForLiveEvent = 0;
							
						
						}
					
						}
					}
					else {
					
					if($zeile['StartZeit'] != $vorherigeUhrzeit) {
					
						
			
						if(($startzeitUnixtimestamp  >= $aktuellbeginn AND $startzeitUnixtimestamp  <= $aktuellende) OR (file_exists($dat_wbteiln) == true AND empty($zeile['TeilnStaffeln']) == false AND $startzeitUnixtimestamp  <= $aktuellbeginn AND $zeile['TypNr'] === 4 AND $startzeitUnixtimestamp + $DauerLiveEnde > $serverzeitakt) OR (file_exists($dat_wbteiln) == true AND empty($zeile['TeilnStaffeln']) == false AND $startzeitUnixtimestamp  <= $aktuellbeginn AND $zeile['TypNr'] === 3 AND $startzeitUnixtimestamp + $DauerLiveEnde > $serverzeitakt)) {
						
						if($aktuellerWettbewerbAn == 1 && $zeile['WettbewerbTyp'] !== "m") {	
						
							echo "<td class ='zeitplanzeitaktuell'><a name='aktuell' class='zeitplanzeitaktuell'>".$zeile['StartZeit']."</a></td>";
							$IDForLiveEvent = 1;
							
						}
						else {
						
							if($zeile['TypNr'] == 4) {
								echo "<td class ='zeitplanzeitaktuell'><a name='aktuell' class='zeitplanzeitaktuell'>".$zeile['StartZeit']."</a></td>";
								$IDForLiveEvent = 1;
							}
							else {
								echo "<td class ='zeitplanzeit'><a class='zeitplanzeit'>".$zeile['StartZeit']."</a></td>";
								$IDForLiveEvent = 0;
							}
						}
						}
						else {
						
							echo "<td class ='zeitplanzeit'><a class='zeitplanzeit'>".$zeile['StartZeit']."</a></td>";
							$IDForLiveEvent = 0;
							
						
						}
						
			
			
						
					}
					else {
					
						if(($startzeitUnixtimestamp  >= $aktuellbeginn AND $startzeitUnixtimestamp  <= $aktuellende) OR (file_exists($dat_wbteiln) == true AND empty($zeile['TeilnStaffeln']) == false AND $startzeitUnixtimestamp  <= $aktuellbeginn AND $zeile['TypNr'] === 4 AND $startzeitUnixtimestamp + $DauerLiveEnde > $serverzeitakt) OR (file_exists($dat_wbteiln) == true AND empty($zeile['TeilnStaffeln']) == false AND $startzeitUnixtimestamp  <= $aktuellbeginn AND $zeile['TypNr'] === 3 AND $startzeitUnixtimestamp + $DauerLiveEnde > $serverzeitakt)) {
						
						if($aktuellerWettbewerbAn == 1 && $zeile['WettbewerbTyp'] !== "m") {	
						
							echo "<td class ='zeitplanzeitvorhandenaktuell'><a name='aktuell' class='zeitplanzeitvorhanden'> </a></td>";
							$IDForLiveEvent = 1;
							
						}
						else {
							if($zeile['TypNr'] == 4) {
								echo "<td class ='zeitplanzeitvorhandenaktuell'><a name='aktuell' class='zeitplanzeitvorhanden'> </a></td>";
								$IDForLiveEvent = 1;
							}
							else {
								echo "<td class ='zeitplanzeitvorhanden'><a class='zeitplanzeitvorhanden'> </a></td>";
								$IDForLiveEvent = 0;
							}
						}
						}
						else {
						
							echo "<td class ='zeitplanzeitvorhanden'><a class='zeitplanzeitvorhanden'> </a></td>";
							$IDForLiveEvent = 0;
							
						
						}
					
						}
					
					
					
					}
					
					# Row Event name
						# Case: Combined Event Event
						if($zeile['WettbewerbTyp'] == "md") {
					
							echo "<td class ='zeitplanspalteevent$IDForLiveEvent'><a class='".$classtd."'";
					
							# Type Fileextention
							if($IPCResultListFileExtention == "htm") {
								if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'";}
							}
							else {
								if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='"."_self"."'";}
							}
							echo ">";
					
							echo $zeile['WettbewerbBez']."</a>";
							
							echo "</td>";
				
						}
						# Case: all other events
						else {
					
							echo "<td class ='zeitplanspalteevent$IDForLiveEvent'><a class='".$classtd."'";
							
							# Type file extention
							if($IPCResultListFileExtention == "htm") {
								if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'";}
							}
							else {
								if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='"."_self"."'";}
							}
							echo ">";
					
							echo $zeile['WettbewerbBez']."</a>";

							echo "</td>";
						}

						
					# Row Round
						# Case: Combined Event Event
						if($zeile['WettbewerbTyp'] == "md") {
					
							echo "<td class ='zeitplanspalterunde$IDForLiveEvent'><a class='".$classtd."'";
					
							# Type Fileextention
							if($IPCResultListFileExtention == "htm") {
								if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'";}
							}
							else {
								if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='"."_self"."'";}
							}
							echo ">";
					
							echo $zeile['RundeBez']."</a>";
							
							echo "</td>";
				
						}
						# Case: all other events
						else {
					
							echo "<td class ='zeitplanspalterunde$IDForLiveEvent'><a class='".$classtd."'";
							
							# Type file extention
							if($IPCResultListFileExtention == "htm") {
								if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'";}
							}
							else {
								if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='"."_self"."'";}
							}
							echo ">";
					
							echo $zeile['RundeBez']."</a>";

							echo "</td>";
						}						
					
					# Row Seperator
					print("<td class='seperator'></td>");
					
					
					# Row Lists
					
					# Definition how many rows in table
					$TimetableRowsInTableLinks = 3;
					$widthOneRow = 160 / $TimetableRowsInTableLinks;
					
					echo "<td class='timetableRowLists'>";
					
						echo "<table class='zeitplanspaltelink'><tr>";
						
							for($CounterRows = 1; $CounterRows <= $TimetableRowsInTableLinks; $CounterRows++) {
							
								# Switch by Type
								
								switch($zeile['TypNr']) {
								
								case 1: # Result list
								
									#echo $zeile['WettbewerbTyp'] . "<br>";
								
									
								
									switch($CounterRows) {
								
										case 1:
										
										if($IPCModeON == 1) { # IPC mode on
										
										
										if($zeile['WettbewerbTyp'] == "md") { # Combined Event
											
												
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
											
											
											}
											else { # normal event
											
												$TmpEntrylist = "t". $zeile['COSANr'] . ".htm";
												
												if($zeile['Datei'] !== "" && file_exists($TmpEntrylist)) {
													echo "<td width='$widthOneRow' class ='typ2'><a class='typ2'";
													echo "href='?sub=".$TmpEntrylist."' target='"."_self"."'";
													echo ">";
													echo "<abbr title='".$TypTyp2."'>" . $ListTypAbbrev[2]."</abbr></a>";
													echo"</td>";
												}
												else {
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
												}
												
											}
										
										
										}
										else { # normal mode
										
											if($zeile['WettbewerbTyp'] == "md") { # Combined Event
											
												
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
											
											
											}
											else { # normal event
											
												$TmpEntrylist = "t". $zeile['COSANr'] . ".htm";
												
												if($zeile['Datei'] !== "" && file_exists($TmpEntrylist)) {
													echo "<td width='$widthOneRow' class ='typ2'><a class='typ2'";
													echo "href='?sub=".$TmpEntrylist."' target='"."_self"."'";
													echo ">";
													echo "<abbr title='".$TypTyp2."'>" . $ListTypAbbrev[2]."</abbr></a>";
													echo"</td>";
												}
												else {
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
												}
												
											}
										
										}
										
										
										break;
										
										case 2:
										
										if($IPCModeON == 1) { # IPC mode on
										
										
										if($zeile['WettbewerbTyp'] == "md") { # Combined Event
											
												
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
											
											
											}
											else { # normal event
											
												$TmpStartlist = "s-". $zeile['WettbewerbTyp'] . "-" . $zeile['WettbewerbNr'] . "-" . $zeile['COSANr'] . "-" . $zeile['Riege'] . "-" . $zeile['RoundTypeLetter'] . ".htm";
												
												if($zeile['Datei'] !== "" && file_exists($TmpStartlist)) {
													echo "<td width='$widthOneRow' class ='typ4'><a class='typ4'";
													echo "href='?sub=".$TmpStartlist."' target='"."_self"."'";
													echo ">";
													echo "<abbr title='".$TypTyp4."'>" . $ListTypAbbrev[4]."</abbr></a>";
													echo"</td>";
												}
												else {
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
												}
												
											}
										
										
										}
										else { # normal mode
										
											if($zeile['WettbewerbTyp'] == "md") { # Combined Event
											
												
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
											
											
											}
											else { # normal event
											
												$TmpStartlist = "s". $zeile['COSANr'] . $zeile['RoundTypeLetter'] . ".htm";
												
												if($zeile['Datei'] !== "" && file_exists($TmpStartlist)) {
													echo "<td width='$widthOneRow' class ='typ4'><a class='typ4'";
													echo "href='?sub=".$TmpStartlist."' target='"."_self"."'";
													echo ">";
													echo "<abbr title='".$TypTyp4."'>" . $ListTypAbbrev[4]."</abbr></a>";
													echo"</td>";
												}
												else {
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
												}
												
											}
										
										}
										
										break;
										
										case 3:
											
									
												echo "<td width='$widthOneRow' class ='".$classtd."'><a class='".$classtd."'";
												if($IPCResultListFileExtention == "htm") {
													if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'";}
												}
												else {
													if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='"."_self"."'";}
												}
												echo ">";
					
												echo "<abbr title='".$TypTyp1."'>" . $ListTypAbbrev[$zeile['TypNr']]."</abbr></a>";
												echo"</td>";

									
										break;
										
										default:
											echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
										break;
									}
								
								break;
								
								case 2: # Entry list
								
									if($CounterRows == 1) {
										
										echo "<td width='$widthOneRow' class ='".$classtd."'><a class='".$classtd."'";
										if($IPCResultListFileExtention == "htm") {
											if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'";}
										}
										else {
										if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='"."_self"."'";}
										}
										echo ">";
					
				
										echo "<abbr title='".$TypTyp2."'>" . $ListTypAbbrev[$zeile['TypNr']]."</abbr></a>";
										echo"</td>";
										
									}
									else {
										echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
									}
								
								break;
								
								case 3: # Intermediate Result list
								
								switch($CounterRows) {
								
										case 1:
										
										if($IPCModeON == 1) { # IPC mode on
										
										
										# ... noch zu bearbeiten
										
										
										}
										else { # normal mode
										
											if($zeile['WettbewerbTyp'] == "md") { # Combined Event
											
												
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
											
											
											}
											else { # normal event
											
												$TmpEntrylist = "t". $zeile['COSANr'] . ".htm";
												
												if($zeile['Datei'] !== "" && file_exists($TmpEntrylist)) {
													echo "<td width='$widthOneRow' class ='typ2'><a class='typ2'";
													echo "href='?sub=".$TmpEntrylist."' target='"."_self"."'";
													echo ">";
													echo "<abbr title='".$TypTyp2."'>" . $ListTypAbbrev[2]."</abbr></a>";
													echo"</td>";
												}
												else {
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
												}
												
											}
										
										}
										
										
										break;
										
										case 2:
										
										if($IPCModeON == 1) { # IPC mode on
										
										
										# ... noch zu bearbeiten
										
										
										}
										else { # normal mode
										
											if($zeile['WettbewerbTyp'] == "md") { # Combined Event
											
												
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
											
											
											}
											else { # normal event
											
												$TmpStartlist = "s". $zeile['COSANr'] . $zeile['RoundTypeLetter'] . ".htm";
												
												if($zeile['Datei'] !== "" && file_exists($TmpStartlist)) {
													echo "<td width='$widthOneRow' class ='typ4'><a class='typ4'";
													echo "href='?sub=".$TmpStartlist."' target='"."_self"."'";
													echo ">";
													echo "<abbr title='".$TypTyp4."'>" . $ListTypAbbrev[4]."</abbr></a>";
													echo"</td>";
												}
												else {
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
												}
												
											}
										
										}
										
										break;
										
										case 3:
											
									
												echo "<td width='$widthOneRow' class ='".$classtd."'><a class='".$classtd."'";
												if($IPCResultListFileExtention == "htm") {
													if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'";}
												}
												else {
													if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='"."_self"."'";}
												}
												echo ">";
					
												echo "<abbr title='".$TypTyp3."'>" . $ListTypAbbrev[$zeile['TypNr']]."</abbr></a>";
												echo"</td>";

									
										break;
										
										default:
											echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
										break;
									}
								
								
								
								
								
								break;
								
								case 4: # Start list
								
									switch($CounterRows) {
								
									case 1:
									
										if($IPCModeON == 1) { # IPC mode on
										
										
											if($zeile['WettbewerbTyp'] == "md") { # Combined Event
											
												$TmpFileArray = explode('-', $zeile['Datei']);
											
												$TmpFileEntries4 = "t" .  $Wettbew[$TmpFileArray[2]]['COSANr'] . ".htm";
												
												if($zeile['Datei'] !== "" && file_exists($TmpFileEntries4)) {
													echo "<td width='$widthOneRow' class ='typ2'><a class='typ2'";
													echo "href='?sub=".$TmpFileEntries4."' target='"."_self"."'";
													echo ">";
													echo "<abbr title='".$TypTyp2."'>" . $ListTypAbbrev[2]."</abbr></a>";
													echo"</td>";
												}
												else {
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
												} 
											
											}
											else { # normal event
											
												$TmpFileEntries4 = "t". $zeile['COSANr'] . ".htm";
												
												if($zeile['Datei'] !== "" && file_exists($TmpFileEntries4)) {
													echo "<td width='$widthOneRow' class ='typ2'><a class='typ2'";
													echo "href='?sub=".$TmpFileEntries4."' target='"."_self"."'";
													echo ">";
													echo "<abbr title='".$TypTyp2."'>" . $ListTypAbbrev[2]."</abbr></a>";
													echo"</td>";
												}
												else {
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
												}
												
											}
										
										
										}
										else { # normal mode
										
											if($zeile['WettbewerbTyp'] == "md") { # Combined Event
											
												$TmpFileArray = explode('-', $zeile['Datei']);
											
												$TmpFileEntries4 = "t" .  $Wettbew[$TmpFileArray[2]]['COSANr'] . ".htm";
												
												if($zeile['Datei'] !== "" && file_exists($TmpFileEntries4)) {
													echo "<td width='$widthOneRow' class ='typ2'><a class='typ2'";
													echo "href='?sub=".$TmpFileEntries4."' target='"."_self"."'";
													echo ">";
													echo "<abbr title='".$TypTyp2."'>" . $ListTypAbbrev[2]."</abbr></a>";
													echo"</td>";
												}
												else {
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
												} 
											
											}
											else { # normal event
											
												$TmpFileEntries4 = "t". substr($zeile['Datei'], 1, 5) . ".htm";
												
												if($zeile['Datei'] !== "" && file_exists($TmpFileEntries4)) {
													echo "<td width='$widthOneRow' class ='typ2'><a class='typ2'";
													echo "href='?sub=".$TmpFileEntries4."' target='"."_self"."'";
													echo ">";
													echo "<abbr title='".$TypTyp2."'>" . $ListTypAbbrev[2]."</abbr></a>";
													echo"</td>";
												}
												else {
													echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
												}
												
											}
										
										}
										
						
									break;
								
									case 2:
										echo "<td width='$widthOneRow' class ='".$classtd."'><a class='".$classtd."'";
										if($IPCResultListFileExtention == "htm") {
											if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'";}
										}
										else {
										if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='"."_self"."'";}
										}
										echo ">";
					
				
										echo "<abbr title='".$TypTyp4."'>" . $ListTypAbbrev[$zeile['TypNr']]."</abbr></a>";
										echo"</td>";
									break;

									default:
										echo "<td class='typempty' width='$widthOneRow'>&nbsp;</td>";
									break;
									}
								
								
								
								
								break;
								
								case 5: # ""
								
								break;
								
								case 6: # Qualification by heats
									if($CounterRows == 1) {
										echo "<td class='typempty'>$TypTyp6</td>";
									}
								break;
								
								case 7: # Qualification by semi-finals
									if($CounterRows == 1) {
										echo "<td class='typempty'>$TypTyp7</td>";
									}
								break;
								
								case 8: # Combined event - event finished
									if($CounterRows == 1) {
										echo "<td class='typempty'>$TypTyp8</td>";
									}
								break;
								}
							
							}
						
						echo "</tr></table>";
					
					
					
					
					
					
					/*
					
					
					echo "<td class ='zeitplanspaltetyp'><table class='zeitplantyplink'><tr><td class ='".$classtd."'><a class='".$classtd."'";
					if($IPCResultListFileExtention == "htm") {
						if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'";}
					}
					else {
						if($zeile['Datei'] !== "" && file_exists($zeile['Datei'])) { echo "href='?sub=".$zeile['Datei']."' target='"."_self"."'";}
					}
					echo ">";
					
				
					echo $zeile['TypBez']."</a>";
					echo"</td></tr></table></td>";*/
					
					
					
					# Row Seperator
					print("<td class='seperator'></td>");
					
					
					# Row Participans and Relay teams
					
					
					echo "<td class='timetableRowParticipansAndTeams'>";
					
					# Beim Status Ergebnisliste
					if(file_exists($dat_endli) && $zeile['TypNr'] == 1 && empty($zeile['TeilnStaffeln']) == false) {
				
						if($zeile['Gemischt'] == "v") {
							echo "<abbr title='".$TxtMixedEvent."'>" . "* " . $zeile['TeilnStaffeln'] . "</abbr>";
						}
						else {
							echo $zeile['TeilnStaffeln'];
						}
					
					}
					
					# Beim Status Startliste
					if(file_exists($dat_wklist) && $zeile['TypNr'] == 4 && empty($zeile['TeilnStaffeln']) == false) {
					
						if($zeile['Gemischt'] == "v") {
							echo "<abbr title='".$TxtMixedEvent."'>" . "* " . $zeile['TeilnStaffeln'] . "</abbr>";
						}
						else {
							echo $zeile['TeilnStaffeln'];
						}
					
					}
					
					
					
					# Beim Status Teilnehmer
					if($zeile['WettbewerbTyp'] !== "m") {
						if(empty($zeile['TeilnStaffeln']) == false && ($zeile['TypNr'] == 2 || $zeile['TypNr'] == 5) && file_exists($dat_wbteiln)) {
								echo $zeile['TeilnStaffeln'];
						}
					
						if(empty($zeile['TeilnStaffeln']) == true && ($zeile['TypNr'] == 2 || $zeile['TypNr'] == 5) && file_exists($dat_wbteiln)) {
							echo $TxtNoEntry;
						}
					}
					
					
					
					echo "</td>";
					
					# Row Heats and Groups
					
					
					echo "<td class='timetableRowHeatsAndGroups'>";
					
					# Beim Status Ergebnisliste
					if(file_exists($dat_endli) && $zeile['TypNr'] == 1 && empty($zeile['TeilnStaffeln']) == false) {
							echo $zeile['LaeufeGruppen'];	
					}
					
					# Beim Status Startliste
					if(file_exists($dat_wklist) && $zeile['TypNr'] == 4 && empty($zeile['TeilnStaffeln']) == false) {
						echo $zeile['LaeufeGruppen'];	
					}

					echo "</td>";
					
					
					#Row Seperator
					
					print("<td class='seperator'></td>");
					
					# Row Updated
					
					echo "<td class ='zeitplanspalteaktuell'><a class='zeitplanspalteaktuell'>".date("H:i", $zeile['aktualisiert'])."&nbsp;<br>".date("d.m.", $zeile['aktualisiert'])."</a></td>";
					
					
				
			
				echo "</tr>";
			
				$vorherigeUhrzeit = $zeile['StartZeit'];
			
			}
			}

?>

		</table>
		
		<p class="AnzahlRunden"><a class="AnzahlRunden"><?php echo $ZaehlerRunden." ".$txt_anzahlrunden; ?></a></p>
		<?php if($StellplatzzeitplanAn == 1 && file_exists($dat_stellplatzzeitplan)) {
		echo "<p class='LinkStellplatz'><a class='LinkStellplatz' href='?sub=$dat_stellplatzzeitplan'>$txt_stellplatzzeitplan</a></p>";
		}
		
		
# Create Timetable file for score board

if($TTBoardON == 1 && $_GET['ttboard'] == 1) {

	$TTBoardOutputContent 	= "";
	$TTBoardOutputFileName	= $DayTTBoard[$starttag] . "Z.txt";
	$TTBoardCounter = 0;
	$TTBoardTemplateContent[1] = $txt_zeitplan;
	$TTBoardTemplateContent[2] = $tage[$starttag];
	$TTBoardTemplateContent[3] = $TxtTTBoardPage;
	$TTBoardTimeBefore = "";

	foreach ($Zeitplan as $zeile) { # Go through $Zeitplan
			
		if($zeile['StartTag'] == $starttag) { # Just use Lines for current day
		
			
			
			if($TTBoardCounter == 0) {
			
				$TTBoardTemplateContent[4]++;
			
				foreach($TTBoardHeadline as $TTBoardItemKey => $TTBoardHeadlineLine) {
				
					
				
					$TmpTTBoardHeadline = str_replace($TTBoardTemplate, $TTBoardTemplateContent, $TTBoardHeadlineLine);
					$TmpTTBoardHeadline2 = str_pad($TmpTTBoardHeadline, $TTBoardRows, $TTBoardHeadlineFilling[$TTBoardItemKey], $TTBoardHeadlineOrientation[$TTBoardItemKey]);
				
					$TTBoardOutputContent = $TTBoardOutputContent . $TmpTTBoardHeadline2 . "\r\n";
				
					
						$TTBoardCounter++;
				}
			
			}
			
			if($TTBoardCounter == $TTBoardLines  && $TTBoardHeadlineOnEachPage == 1) {
			
				$TTBoardTemplateContent[4]++;
			
				foreach($TTBoardHeadline as $TTBoardItemKey => $TTBoardHeadlineLine) {
				
					
				
					$TmpTTBoardHeadline = str_replace($TTBoardTemplate, $TTBoardTemplateContent, $TTBoardHeadlineLine);
					$TmpTTBoardHeadline2 = str_pad($TmpTTBoardHeadline, $TTBoardRows, $TTBoardHeadlineFilling[$TTBoardItemKey], $TTBoardHeadlineOrientation[$TTBoardItemKey]);
				
					$TTBoardOutputContent = $TTBoardOutputContent . $TmpTTBoardHeadline2 . "\r\n";
				
					
						
				}
				
				$TTBoardCounter = count($TTBoardHeadline);
				$TTBoardTimeBefore = "";
			
			}
			
			
			
			
			$TTBoardCounter++;
			
			if($TTBoardTimeBefore != $zeile['StartZeit']) {
				$TmpTTBoardStartTime = $zeile['StartZeit'];
			}
			else {
				$TmpTTBoardStartTime = "";
			}
			
			$TTBoardOutputContent = $TTBoardOutputContent .	str_pad($TmpTTBoardStartTime, $TTBoardRowsTime, ' ', STR_PAD_RIGHT) . $TTBoardSeperator[1] . str_pad($Klassen[substr($zeile['COSANr'], 0, 2)]['Abbrev'], $TTBoardRowsClass, ' ', STR_PAD_RIGHT) . $TTBoardSeperator[2] . str_pad($Disziplinen[substr($zeile['COSANr'], 2, 3)*1]['Kurz'], $TTBoardRowsEvent, ' ', STR_PAD_RIGHT) . $TTBoardSeperator[3] . str_pad($RoundTypAbbrev[$zeile['RundeNr']], $TTBoardRowsRound, ' ', STR_PAD_RIGHT) . $TTBoardSeperator[4];

			if($TTBoardOutputParticipants == 1) {
				$TTBoardOutputContent = $TTBoardOutputContent . str_pad($zeile['TeilnStaffeln'], $TTBoardRowsParticipants, ' ', STR_PAD_LEFT);
			}
			
			$TTBoardOutputContent = $TTBoardOutputContent . "\r\n";
			
			$TTBoardTimeBefore = $zeile['StartZeit'];
		
		} # Just use Lines for current day
		
		
		
	} # End Go through $Zeitplan

	
	# Output of file
	
		if(strlen($TTBoardTemplateContent[4]) == 1) {
			$TTBoardTemplateContent[4] = " " . $TTBoardTemplateContent[4];
		}
		
		$TTBoardOutputContent = str_replace($TTBoardTemplateContent[5], $TTBoardTemplateContent[4], $TTBoardOutputContent);
		
	
		$TTBoadFileHandler = fopen($TTBoardOutputFileName,"w");
		fwrite($TTBoadFileHandler, mb_convert_encoding($TTBoardOutputContent, $TTBoardCharsetFile, "auto"));
		fclose($TTBoadFileHandler);
		
		if(file_exists($TTBoardOutputFileName)) {
			echo "<p><a href='".$TTBoardOutputFileName."' type='application/octet-stream'>" . $TxtTTBoardDownload . " - ". $tage[$starttag] . " (" . $TTBoardOutputFileName . ")"."</a></p>";
			
		}
	
	
} # End Create File for score board
		
		
unset($Wettbew);
unset($Endli);
unset($Endli2);
unset($TmpEndli);
unset($EndliWettbewNr);
unset($TmpEndliAnzahlLaeufeGruppen);
unset($EndliAnzahlLaeufeGruppen);
unset($TmpWkList);
unset($WkList);
unset($WKList2);
unset($WbTeiln);
unset($TeilnehmeranzahlWb);
unset($StTeilnehmeranzahlWb);
unset($StWbTeiln);
unset($ZeitplanRunden);
unset($Zeitplan);
unset($ZeitplanRundenEndli);
unset($WettbewerbeOberhalbZeitplan);																			
		
		?>
