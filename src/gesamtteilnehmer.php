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

This program contains (as at 18.06.2013) the following files,
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

Das Programm beinhaltet derzeit (18.06.2013) folgende eigenständig
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

---------------------------------------------------------------------------------
*/ ?>
<?php

### LaIVE - Modul Gesamtteilnehmer (gesamtteilnemer.php) /LaIVE - Module participants lists (gesamtteilnehmer.php)
### Erstellt von / Created by Kilian Wenzel
### Zuletzt geändert: / Last change: 0.12.3.2014-03-17


# Ermitteln, welche Liste verwendet werden soll

if(empty($_GET["list"])) {

	$_GET["list"] = 1;
	
}

if(empty($_GET["wg"])) {

	$_GET["wg"] = 0;
	
}

if(empty($_GET["sort"])) {

	$_GET["sort"] = 3;
	
}


# DBS Modus - Einlesen der Startklassen / IPC Mode - read start classes from file
if($IPCModeON == 1) {
	$DBSTextskl_entrylists = IPCClassesArray();
}

# Enty List Notes / Hinweise zur Teilnehmerliste
if(file_exists("./laive_entrylist_notes.txt")) {
	$EntrylistNotesContent 	= file("./laive_entrylist_notes.txt");
	$EntrylistNotesCount	= count($EntrylistNotesContent);
}


# Unterscheidung nach Listen

switch($_GET["list"]) {

	case 1: #Teilnehmerliste #####################################################################################
	
	$GesamtteilnehmerAnzahl = 0;
	$GesamtmannschaftenAnzahl = 0;
	
	
		#Daten auslesen und aufbereiten
		
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
		
		
		
		$Wettbew[trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3))*1] = array(	'WettbewNr'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3))*1,
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
							'Aktiv'					=>	$TmpWettbewAktiv
							
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
				$WbTeiln3[trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 4, 3)) * 1 ."-".trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS - 1, 5))] = array ( 	'StNr' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS - 1, 5)),
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
							'Riege'			=> trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 28, 2))*1
							
						);
				}
				$WbTeilnAbsolutePositionDS = $WbTeilnAbsolutePositionDS + $WbTeilnLaengeDatensatz;
			}	
		}
		
		
		#Stamm
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
			
				if(trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5)) != "*****") {
				#Wettbewerbe zu den Teilnehmern schreiben
				foreach($WbTeiln3 as $WbTeiln3Zeile) {
					
					
					if($WbTeiln3Zeile['StNr'] == trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5))) {
					
					if($Wettbew[$WbTeiln3Zeile['WettbewNr']]['Aktiv'] != 0) {
					
						# Disziplin-Kurz-Bez. bei eigenen Wettbewerben
						if($Wettbew[$WbTeiln3Zeile['WettbewNr']]['WettbewKurz'] != "") {
							$TmpWettbewKurz = $Wettbew[$WbTeiln3Zeile['WettbewNr']]['WettbewKurz'];
						}
						else {
							$TmpWettbewKurz = $Disziplinen[substr($WbTeiln3Zeile['COSANr'], 2, 3)*1]['Kurz'];
						}
					
						
						$WettbewerbeTeiln[] = array (	'WettbewNr'		=>	$WbTeiln3Zeile['WettbewNr'],
													'COSANr'		=>	$WbTeiln3Zeile['COSANr'],
													'aW'			=>	$WbTeiln3Zeile['aW'],
													'Meldeleistung'	=>	$WbTeiln3Zeile['Meldeleistung'],
													'AK'			=>	$WbTeiln3Zeile['AK'],
													'Staffel'		=>	$WbTeiln3Zeile['Staffel'],
													'Riege'			=>	$WbTeiln3Zeile['Riege'],
													'Nachmeldung'	=>	$WbTeiln3Zeile['Nachmeldung'],
													'Altersklasse'	=>	$Klassen[substr($WbTeiln3Zeile['COSANr'], 0, 2)]['Bez'],
													'Disziplin'	=>	$TmpWettbewKurz);
						}
						}
					
				}
				
				
				$Wertungsgruppe[1] = trim(substr($StammInhalt, $StammAbsolutePosition + 71, 1));
				$Wertungsgruppe[2] = trim(substr($StammInhalt, $StammAbsolutePosition + 72, 1));
				$Wertungsgruppe[3] = trim(substr($StammInhalt, $StammAbsolutePosition + 73, 1));
				$Wertungsgruppe[4] = trim(substr($StammInhalt, $StammAbsolutePosition + 74, 1));
				$Wertungsgruppe[5] = trim(substr($StammInhalt, $StammAbsolutePosition + 75, 1));
				$Wertungsgruppe[6] = trim(substr($StammInhalt, $StammAbsolutePosition + 76, 1));
				$Wertungsgruppe[7] = trim(substr($StammInhalt, $StammAbsolutePosition + 77, 1));
				
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
				
				
				
				if($_GET['wg'] == 0 || $Wertungsgruppe[$_GET['wg']] == 1) {
				$Ausgabe[trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5))] = array(	'StartNr'		=>	trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5)),
																								'Nachname'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 4, 22)),
																								'Vorname'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 27, 16)),
																								'JG'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 44, 4)),
																								'Geschlecht'=> trim(substr($StammInhalt, $StammAbsolutePosition + 48, 1)),
																								'LV'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 50, 3)),
																								'Staffel'	=> $TmpStaffel ,
																								'Verein'	=> $Verein[trim(substr($StammInhalt, $StammAbsolutePosition + 50, 3))."-".trim(substr($StammInhalt, $StammAbsolutePosition + 53, 5))]['VereinBez'],
																								'VereinNr'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 53, 5)),
																								'Wertungsgruppe1'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 71, 1)),
																								'Wertungsgruppe2'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 72, 1)),
																								'Wertungsgruppe3'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 73, 1)),
																								'Wertungsgruppe4'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 74, 1)),
																								'Wertungsgruppe5'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 75, 1)),
																								'Wertungsgruppe6'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 76, 1)),
																								'Wertungsgruppe7'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 77, 1)),
																								'Wettbewerbe'			=> $WettbewerbeTeiln,
																								'IPCClassName'			=> $TmpIPCClass,
																								
																								
																					);
				}
				unset($WettbewerbeTeiln);
				}
				$StammAbsolutePosition = $StammAbsolutePosition + $StammLaengeDatensatz;
			
	}
}
		# Sortieren
		# Array (Zeitplan) sortieren nach Startzeit

foreach ($Ausgabe as $nr => $inhalt) {

	$MStartNr[$nr] = strtolower($inhalt['StartNr']);
	$MNachname[$nr] = strtolower($inhalt['Nachname']);
	$MVorname[$nr] = strtolower($inhalt['Vorname']);
	$MJG[$nr] = strtolower($inhalt['JG']);
	$MGeschlecht[$nr] = strtolower($inhalt['Geschlecht']);
	$MLV[$nr] = strtolower($inhalt['LV']);
	$MStaffel[$nr] = strtolower($inhalt['Staffel']);
	$MVerein[$nr] = strtolower($inhalt['Verein']);
	$MVereinNr[$nr] = strtolower($inhalt['VereinNr']);
	$MWertungsgruppe1[$nr] = strtolower($inhalt['Wertungsgruppe1']);
	$MWertungsgruppe2[$nr] = strtolower($inhalt['Wertungsgruppe2']);
	$MWertungsgruppe3[$nr] = strtolower($inhalt['Wertungsgruppe3']);
	$MWertungsgruppe4[$nr] = strtolower($inhalt['Wertungsgruppe4']);
	$MWertungsgruppe5[$nr] = strtolower($inhalt['Wertungsgruppe5']);
	$MWertungsgruppe6[$nr] = strtolower($inhalt['Wertungsgruppe6']);
	$MWertungsgruppe7[$nr] = strtolower($inhalt['Wertungsgruppe7']);
	$MWettbewerbe[$nr] = $inhalt['Wettbewerbe'];
	$MIPCClassName[$nr] = strtolower($inhalt['IPCClassName']);
	$MSortieren[$nr] = strtolower($inhalt['Staffel'])." ".strtolower($inhalt['Nachname'])." ".strtolower($inhalt['Vorname'])." ".strtolower($inhalt['Verein']);
}
	
	if($StartnummernAn == 1){ array_multisort($MStartNr, SORT_ASC, $Ausgabe); }
	else { array_multisort($MSortieren, SORT_ASC, $Ausgabe); }
				
	
		
		
		#___________________________________________
		#Daten ausgeben
		?>

	<table class="laivemenu">
	<tr>
		
		<td class="linkliste">
			<ul class="secoundmenu">
				<?php 
						
						
						
					
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=0'>" . $TxtLinkSubMenuEntriesList1 . "</a>
							</li>";
						
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=0&amp;sort=$TeilnNachWettbewStandardSort'>" . $TxtLinkSubMenuEntriesList2 . "</a>
							</li>"; 
						
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=gesamtteilnehmer.php&amp;list=3'>" . $TxtLinkSubMenuEntriesList3 . "</a>
							</li>"; 
						
						
						
						?>

			</ul>
		</td>
		<td class="aktualisiert" align="right"><a class="aktualisiert"><?php echo $TxtSubMenuUpdated. " ".date("d.m.y H:i", max(filemtime($dat_stamm), filemtime($dat_wbteiln), filemtime($dat_verein))) ; ?></a></td>
	</tr>
</table>

		<table class="body" cellspacing="0">
			<tr><td class="KopfZ1"><?php echo $Kopfzeile1; ?></td></tr>
			<tr><td class="KopfZ11"><?php echo $Kopfzeile2; ?></td></tr>
			<tr><td class="KopfZ12"><?php echo $Kopfzeile3; ?></td></tr>
			</table>



		<table class="body" cellspacing="0">
		<tr><td class="KopfZ21"><?php echo $txt_gesamtteilnehmerliste; 
		
			switch($_GET['wg']) {
				case 1:
					echo " - ". $wertungsgruppen[1];
				break;
				case 2:
					echo " - ". $wertungsgruppen[2];
				break;
				case 3:
					echo " - ". $wertungsgruppen[3];
				break;
				case 4:
					echo " - ". $wertungsgruppen[4];
				break;
				case 5:
					echo " - ". $wertungsgruppen[5];
				break;
				case 6:
					echo " - ". $wertungsgruppen[6];
				break;
				case 7:
					echo " - ". $wertungsgruppen[17];
				break;
			}
		
		?></td></tr>
		</table>
		<br>
<?php # Entry Notes output
if(file_exists("./laive_entrylist_notes.txt") && $EntrylistNotesCount <> 0) {	
	print("<p class='entrylistnotes'>");
	foreach($EntrylistNotesContent as $EntrylistNotesContentLine) {
		print("<a class='entrylistnotes'>" . $EntrylistNotesContentLine . "</a><br>");
	}
	print("</p>");
}
?>
		
<?php
		if($wertungsgruppen[1] != "" || $wertungsgruppen[2] != "" || $wertungsgruppen[3] != "" || $wertungsgruppen[4] != "" || $wertungsgruppen[5] != "" || $wertungsgruppen[6] != "" || $wertungsgruppen[7] != "") {
			echo "<table class='bodynoprint' cellspacing='0'><tr><td class='KopfZ2'>" . $TxtEvaluationGroupsHeadline . "</td></tr>";
			
			echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=0'>".$TxtEvaluationGroupsNoGroup."</a></td></tr>";
			if($wertungsgruppen[1] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=1'>".$wertungsgruppen[1]."</a></td></tr>";}
			if($wertungsgruppen[2] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=2'>".$wertungsgruppen[2]."</a></td></tr>";}
			if($wertungsgruppen[3] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=3'>".$wertungsgruppen[3]."</a></td></tr>";}
			if($wertungsgruppen[4] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=4'>".$wertungsgruppen[4]."</a></td></tr>";}
			if($wertungsgruppen[5] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=5'>".$wertungsgruppen[5]."</a></td></tr>";}
			if($wertungsgruppen[6] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=6'>".$wertungsgruppen[6]."</a></td></tr>";}
			if($wertungsgruppen[7] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=7'>".$wertungsgruppen[7]."</a></td></tr>";}
			echo "</table>";
			echo "<br class='noprint'>";
			
			
			
		}


?>		
		
		
		<table class="sortable">
			<thead>
				<tr>
					<?php if($StartnummernAn == 1) { echo "<th><abbr title='$text_hinweissortierunguebersicht'>$txt_startnummer</abbr></th>";} ?>
					<th><abbr title="<?php echo $text_hinweissortierunguebersicht; ?>"><? echo $txt_name; ?></abbr></th>
					<?php if($IPCModeON == 1) { echo "<th><abbr title='$text_hinweissortierunguebersicht'>$TxtIPCClass</abbr></th>";} ?>
					<th><abbr title="<?php echo $text_hinweissortierunguebersicht; ?>"><? echo $txt_geschlecht; ?></abbr></th>
					<th><abbr title="<?php echo $text_hinweissortierunguebersicht; ?>"><? echo $txt_jahrgang; ?></abbr></th>
					<th><abbr title="<?php echo $text_hinweissortierunguebersicht; ?>"><? echo $txt_lv; ?></abbr></th>
					<th><abbr title="<?php echo $text_hinweissortierunguebersicht; ?>"><? echo $txt_verein; ?></abbr></th>
					<th class="sorttable_nosort"><? echo $txt_gemeldetewettbewerbe; ?></th>
				</tr>
			</thead>
		
<?php
		$Zeilenwechsler = 0;
		foreach($Ausgabe as $AusgabeZeile) {
		
			
		
			#Geschlecht
			switch($AusgabeZeile['Geschlecht']) {
				case 0: # männlich
					$tmpgeschlecht = $TxtAbrrevGenderMale;
					$tmpgeschlechtAK = $TxtAbrrevGenderMan;
				break;
				case 1: # weiblich
					$tmpgeschlecht = $TxtAbrrevGenderFemale;
					$tmpgeschlechtAK = $TxtAbrrevGenderWoman;
				break;
			}
		
			switch($AusgabeZeile['Staffel']) {
			
			case 1:  # Staffel
			
			$GesamtmannschaftenAnzahl++;
			
			echo  "<tr>";
				if($StartnummernAn == 1){ echo "<td class='TeilnStNr'>".$AusgabeZeile['StartNr']."</td>"; }
				
				echo "<td class='TeilnName' sorttable_customkey='".$AusgabeZeile['Staffel']." ".$AusgabeZeile['Verein']."'>".$AusgabeZeile['Verein']." ".$Roemisch[$AusgabeZeile['JG']]."</td>";
				if($IPCModeON == 1){ echo "<td class='TeilnIPCClass'>".$AusgabeZeile['IPCClassName']."</td>"; }
				echo "<td class='TeilnGeschlecht'>".$tmpgeschlecht."</td>";
				echo "<td class='TeilnJG'>".""."</td>";
				
				if($FlagsOn == 1) {print("<td class='TeilnLV'>"."<img src='" .  $PathToFlags . $AusgabeZeile['LV'] . $FileFormatFlags . "' alt='".$AusgabeZeile['LV']."' class='imgflags'>"."</td>");}
					else {echo "<td class='TeilnLV'>".$AusgabeZeile['LV']."</td>";}
				
				
				echo "<td class='TeilnVerein'>"."<a href='?sub=gesamtteilnehmer.php&amp;list=3#".$AusgabeZeile['LV'].$AusgabeZeile['VereinNr']."'>".$AusgabeZeile['Verein']."</a></td>";
				echo "<td class='TeilnGemWettbew'>";
				
					foreach($AusgabeZeile['Wettbewerbe'] as $WettbewerbeZeile) {
					
						if($WettbewerbeZeile['aW'] == 1) {
							$TmpaW = "(".$TxtAbbrevOutOfRanking.")";
						}
						else {
							$TmpaW = "";
						}
						
						
					
						if($WettbewerbeZeile['Nachmeldung'] == 1) {
							$TmpNachmeldung = "<a class='nachmeldung'> - " . $TxtAbbrevLateEntry . "</a>";
						}
						else {
							$TmpNachmeldung = "";
						}
						
						if($WettbewerbeZeile['Meldeleistung'] != "") {
							$TmpMeldeleistung = "(".str_replace(",", $MarkSeperator1, $WettbewerbeZeile['Meldeleistung']).")";
						}
						else {
							$TmpMeldeleistung = "";
						}
						
						if($WettbewerbeZeile['Riege'] != "" && $WettbewerbeZeile['Riege'] != 0) {
							$TmpRiege = "- " . $TxtCombinedEventGroup . " ".$WettbewerbeZeile['Riege'];
						}
						else {
							$TmpRiege = "";
						}
						
						
						
						
					
						echo "<a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=".$_GET['wg']."&amp;sort=$TeilnNachWettbewStandardSort#wb".$WettbewerbeZeile['WettbewNr']."'>".$WettbewerbeZeile['Disziplin']." ".$WettbewerbeZeile['Altersklasse']."</a> ".$TmpRiege." ".$TmpaW." ".$TmpMeldeleistung." ".$TmpNachmeldung."<br>";
					
					}
				
				
				
				echo "</td>";
			echo "</tr>";
			
			
			
			
			
			
			break;
			
			default: # Alle Anderen
			
			$GesamtteilnehmerAnzahl++;
			
		
			echo  "<tr>";
				if($StartnummernAn == 1){ echo "<td class='TeilnStNr'>".$AusgabeZeile['StartNr']."</td>"; }
				
				echo "<td class='TeilnName' sorttable_customkey='".$AusgabeZeile['Staffel']." ".$AusgabeZeile['Nachname']." ".$AusgabeZeile['Vorname']."'>".$AusgabeZeile['Nachname'].", ".$AusgabeZeile['Vorname']."</td>";
				if($IPCModeON == 1){ echo "<td class='TeilnIPCClass'>".$AusgabeZeile['IPCClassName']."</td>"; }
				echo "<td class='TeilnGeschlecht'>".$tmpgeschlecht."</td>";
				echo "<td class='TeilnJG'>".$AusgabeZeile['JG']."</td>";
				
				if($FlagsOn == 1) {print("<td class='TeilnLV'>"."<img src='" .  $PathToFlags . $AusgabeZeile['LV'] . $FileFormatFlags . "' alt='".$AusgabeZeile['LV']."' class='imgflags'>"."</td>");}
					else {echo "<td class='TeilnLV'>".$AusgabeZeile['LV']."</td>";}
				
				
				
				echo "<td class='TeilnVerein'>"."<a href='?sub=gesamtteilnehmer.php&amp;list=3#".$AusgabeZeile['LV'].$AusgabeZeile['VereinNr']."'>".$AusgabeZeile['Verein']."</a></td>";
				echo "<td class='TeilnGemWettbew'>";
				
					foreach($AusgabeZeile['Wettbewerbe'] as $WettbewerbeZeile) {
					
						if($WettbewerbeZeile['aW'] == 1) {
							$TmpaW = "(".$TxtAbbrevOutOfRanking.")";
						}
						else {
							$TmpaW = "";
						}
						
						
					
						if($WettbewerbeZeile['Nachmeldung'] == 1) {
							$TmpNachmeldung = "<a class='nachmeldung'> - ". $TxtAbbrevLateEntry. " </a>";
						}
						else {
							$TmpNachmeldung = "";
						}
						
						if($WettbewerbeZeile['Meldeleistung'] != "") {
							$TmpMeldeleistung = "(".str_replace(",", $MarkSeperator1, $WettbewerbeZeile['Meldeleistung']).")";
						}
						else {
							$TmpMeldeleistung = "";
						}
						if($WettbewerbeZeile['Riege'] != "" && $WettbewerbeZeile['Riege'] != 0) {
							$TmpRiege = "- " . $TxtCombinedEventGroup. " ".$WettbewerbeZeile['Riege'];
						}
						else {
							$TmpRiege = "";
						}
						
						if($Wettbew[$WettbewerbeZeile['WettbewNr']]['WettbewTyp'] == "m") {
						
							if(is_numeric($WettbewerbeZeile['AK'])) {
								$TmpAKMK = $tmpgeschlechtAK.$WettbewerbeZeile['AK'];
							}
							else {
								$TmpAKMK = "";
							}
						}
						else {
							$TmpAKMK = "";
						}
					
						echo "<a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=".$_GET['wg']."&amp;sort=$TeilnNachWettbewStandardSort#wb".$WettbewerbeZeile['WettbewNr']."'>".$WettbewerbeZeile['Disziplin']." ".$WettbewerbeZeile['Altersklasse']."</a> ".$TmpRiege." ".$TmpAKMK." ".$TmpaW." ".$TmpMeldeleistung." ".$TmpNachmeldung."<br>";
					
					}
				
				
				
				echo "</td>";
			echo "</tr>";
			break;
		}
		}
		echo "</table>";
		#echo "<br>";
	
?>
	<p class="AnzahlRunden"><a class="AnzahlRunden">
<?php
															if ($GesamtteilnehmerAnzahl != "" && $GesamtmannschaftenAnzahl != "") {
																$TrennzeichenTeilnStaffeln = " " . $TxtAnd . " ";
															}
															else {
																$TrennzeichenTeilnStaffeln = "";
															}
															
															if($GesamtteilnehmerAnzahl  == 1) {
																$GesamtteilnehmerAnzahlText = $GesamtteilnehmerAnzahl . " ". $TxtParticipant;
															}
															elseif ($GesamtteilnehmerAnzahl  > 1) {
																$GesamtteilnehmerAnzahlText = $GesamtteilnehmerAnzahl . " ". $TxtParticipants;
															}
															else {
																$GesamtteilnehmerAnzahlText = "";
															}
															
															switch($GesamtmannschaftenAnzahl) {
																case 0:
																	$GesamtmannschaftenAnzahlText = "";
																break;
																case 1:
																	$GesamtmannschaftenAnzahlText = $GesamtmannschaftenAnzahl . " ". $TxtRelayTeam;
																break;
																default:
																	$GesamtmannschaftenAnzahlText = $GesamtmannschaftenAnzahl . " ". $TxtRelayTeams;
																break;
															}
															echo $GesamtteilnehmerAnzahlText . $TrennzeichenTeilnStaffeln . $GesamtmannschaftenAnzahlText;
?>
</a></p>



		
<?php
	
	break;
	
	case 2: # Teilnehmerliste nach Wettbewerb ######################################################################
	
	
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
		
		
		
		$Wettbew[trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3))*1] = array(	'WettbewNr'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3))*1,
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
				$WbTeiln3[trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 4, 3)) * 1 ."-".trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS - 1, 5))] = array ( 	'StNr' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS - 1, 5)),
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
							'Riege'			=> trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 28, 2))*1
						);
				}
				$WbTeilnAbsolutePositionDS = $WbTeilnAbsolutePositionDS + $WbTeilnLaengeDatensatz;
			}	
		} # Ende WbTeiln.c01
	
		# Aufbereiten der Ausgabedatei
		
		
		foreach($Wettbew as $WettbewZeile) { # Ausgabedatei
		$TmpAnzahlStaffeln = 0;
				foreach($WbTeiln3 as $Teilnehmer2Zeile) { # Teilnehmer zu Wettbewerben
				
					if($Teilnehmer2Zeile['WettbewNr'] == $WettbewZeile['WettbewNr']) { #1
					
					
					$Wertungsgruppe[1] = $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe1'];
					$Wertungsgruppe[2] = $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe2'];
					$Wertungsgruppe[3] = $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe3'];
					$Wertungsgruppe[4] = $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe4'];
					$Wertungsgruppe[5] = $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe5'];
					$Wertungsgruppe[6] = $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe6'];
					$Wertungsgruppe[7] = $Stamm[$Teilnehmer2Zeile['StNr']]['Wertungsgruppe7'];
				
				
				
				
				
				if($_GET['wg'] == 0 || $Wertungsgruppe[$_GET['wg']] == 1) {
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
																);
																
					# Bei Staffeln
					if ($Teilnehmer2Zeile['Staffel'] == 1) {
						$TmpAnzahlStaffeln++;
					}
					
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
					
					case 4: # nach DBS-Startklassen
					if(count($T2IPCClassName)) {
						array_multisort($T2IPCClassName, SORT_ASC, $TeilnehmerZumWettbewerb);
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
		
		# Header
		echo "<div class='header'>" . $txt_laive . " - ".$Kopfzeile2." ".$DatumUeberschrift . "</div>";
		
		
?>

	<table class="laivemenu">
	<tr>
		
		<td class="linkliste">
			<ul class="secoundmenu">
				<?php 
						
						
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=0'>" . $TxtLinkSubMenuEntriesList1 . "</a>
							</li>";
						
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=0&amp;sort=$TeilnNachWettbewStandardSort'>" . $TxtLinkSubMenuEntriesList2 . "</a>
							</li>"; 
						
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=gesamtteilnehmer.php&amp;list=3'>" . $TxtLinkSubMenuEntriesList3 . "</a>
							</li>"; 
						
						?>

			</ul>
		</td>
		<td class="aktualisiert" align="right"><a class="aktualisiert"><?php echo $TxtSubMenuUpdated. " ".date("d.m.y H:i", max(filemtime($dat_stamm), filemtime($dat_wbteiln), filemtime($dat_verein))) ; ?></a></td>
	</tr>
</table>

		<table class="body" cellspacing="0">
			<tr><td class="KopfZ1"><?php echo $Kopfzeile1; ?></td></tr>
			<tr><td class="KopfZ11"><?php echo $Kopfzeile2; ?></td></tr>
			<tr><td class="KopfZ12"><?php echo $Kopfzeile3; ?></td></tr>
			</table>



		<table class="body" cellspacing="0">
		<tr><td class="KopfZ21"><?php echo $txt_gesamtteilnehmerlistenachwettbewerben; 
		
			switch($_GET['wg']) {
				case 1:
					echo " - ". $wertungsgruppen[1];
				break;
				case 2:
					echo " - ". $wertungsgruppen[2];
				break;
				case 3:
					echo " - ". $wertungsgruppen[3];
				break;
				case 4:
					echo " - ". $wertungsgruppen[4];
				break;
				case 5:
					echo " - ". $wertungsgruppen[5];
				break;
				case 6:
					echo " - ". $wertungsgruppen[6];
				break;
				case 7:
					echo " - ". $wertungsgruppen[17];
				break;
			}
		
		?></td></tr>
		</table>
		<br>
<?php # Entry Notes output
if(file_exists("./laive_entrylist_notes.txt") && $EntrylistNotesCount <> 0) {	
	print("<p class='entrylistnotes'>");
	foreach($EntrylistNotesContent as $EntrylistNotesContentLine) {
		print("<a class='entrylistnotes'>" . $EntrylistNotesContentLine . "</a><br>");
	}
	print("</p>");
}
?>
		<table class="bodynoprint" cellspacing="0"><tr>
			<td class="blGrundSortierung"><?php echo $TxtSortedByHeadline . ":"; ?> </td>
			<td class="blGrundSortierung">
			<?php 
				if($StartnummernAn == 1) {echo "<a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=".$_GET['wg']."&amp;sort=1'>". $TxtSortedByBIB . "</a>";}
			?></td>
			<td class="blGrundSortierung">
			<?php 
				echo "<a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=".$_GET['wg']."&amp;sort=2'>" . $TxtSortedByName . "</a>";
			?></td>
			<td class="blGrundSortierung">
			<?php 
				echo "<a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=".$_GET['wg']."&amp;sort=3'>" . $TxtSortedBySeasonBest . "</a>";
			?></td>
			<td class="blGrundSortierung">
			<?php 
				if($IPCModeON  == 1) {echo "<a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=".$_GET['wg']."&amp;sort=4'>". $TxtSortedByIPCClass . "</a>";}
			?></td>
		</tr></table>
		<br class="noprint">
<?php
		if($wertungsgruppen[1] != "" || $wertungsgruppen[2] != "" || $wertungsgruppen[3] != "" || $wertungsgruppen[4] != "" || $wertungsgruppen[5] != "" || $wertungsgruppen[6] != "" || $wertungsgruppen[7] != "") {
			echo "<table class='bodynoprint' cellspacing='0'><tr><td class='KopfZ2'>" . $TxtEvaluationGroupsHeadline . "</td></tr>";
			
			echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=0&amp;sort=".$_GET['sort']."'>".$TxtEvaluationGroupsNoGroup."</a></td></tr>";
			if($wertungsgruppen[1] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=1&amp;sort=".$_GET['sort']."'>".$wertungsgruppen[1]."</a></td></tr>";}
			if($wertungsgruppen[2] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=2&amp;sort=".$_GET['sort']."'>".$wertungsgruppen[2]."</a></td></tr>";}
			if($wertungsgruppen[3] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=3&amp;sort=".$_GET['sort']."'>".$wertungsgruppen[3]."</a></td></tr>";}
			if($wertungsgruppen[4] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=4&amp;sort=".$_GET['sort']."'>".$wertungsgruppen[4]."</a></td></tr>";}
			if($wertungsgruppen[5] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=5&amp;sort=".$_GET['sort']."'>".$wertungsgruppen[5]."</a></td></tr>";}
			if($wertungsgruppen[6] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=6&amp;sort=".$_GET['sort']."'>".$wertungsgruppen[6]."</a></td></tr>";}
			if($wertungsgruppen[7] != "") { echo "<tr><td class='blGrund'><a href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=7&amp;sort=".$_GET['sort']."'>".$wertungsgruppen[7]."</a></td></tr>";}
			echo "</table>";
			echo "<br class='noprint'>";
			
			
			
		}


?>		
		
		
<?php
			echo "<table class='bodynoprint' cellspacing='0'><tr><td class='KopfZ2'>" . $TxtSummaryOfClasses . "</td></tr></table>";
			
			echo "<table class='bodynoprint' cellspacing='0'>";
			
			$LinklisteCOSNrAKVorher = "";
			
			foreach($Ausgabe2Linkliste as $Ausgabe2LinklisteZeile) {
			
				if($Ausgabe2LinklisteZeile['COSANrAK'] != $LinklisteCOSNrAKVorher) {
					
					echo "<tr>";
					echo "<td class='blGrundLinkAK'>";
					echo "<a href='#ak".$Ausgabe2LinklisteZeile['COSANrAK']."'>".$Ausgabe2LinklisteZeile['AKBez']."</a>:";
					echo "</td>";
					echo "<td class='blGrundLinkDIS'>";
				
				}
					echo "<a href='#wb".$Ausgabe2LinklisteZeile['WettbewNr']."'>".$Ausgabe2LinklisteZeile['COSANrDISBez']."</a> ";
				
				
				
				$LinklisteCOSNrAKVorher = $Ausgabe2LinklisteZeile['COSANrAK'];
			}
			
			echo "</table>";
			
			echo "<br class='noprint'>";
			#echo "<hr>";
			#echo "<br>";
			
			$COSANrAKVorher = "";
			$RiegeVorher = "";
			
			foreach($Ausgabe2 as $Ausgabe2Zeile) {
			
			echo "<div class='holdtogether'>";
			
			$TmpTeilnehmer = 0;
			$TmpMannschaften = 0;
			$TmpTeilnehmerRiege = array();
			
			
				# Wenn AK Vorher anders
				if(count($Ausgabe2Zeile['Teilnehmer']) > 0) {
				if($Ausgabe2Zeile['COSANrAK'] != $COSANrAKVorher) {
				
					echo "<table class='body' cellspacing='0'><tr><td class='AklZ'><a name='ak".$Ausgabe2Zeile['COSANrAK']."'>".$Ausgabe2Zeile['AKBez']."</a></td></tr></table>";

				}
				$COSANrAKVorher = $Ausgabe2Zeile['COSANrAK'];
				}
				
				
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
			
				echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'><a name='wb".$Ausgabe2Zeile['WettbewNr']."'>".$Ausgabe2Zeile['WettbewBez'];
				
				switch($_GET['wg']) {
				case 1:
					echo " - ". $wertungsgruppen[1];
				break;
				case 2:
					echo " - ". $wertungsgruppen[2];
				break;
				case 3:
					echo " - ". $wertungsgruppen[3];
				break;
				case 4:
					echo " - ". $wertungsgruppen[4];
				break;
				case 5:
					echo " - ". $wertungsgruppen[5];
				break;
				case 6:
					echo " - ". $wertungsgruppen[6];
				break;
				case 7:
					echo " - ". $wertungsgruppen[17];
				break;
			}
				echo"</a></td></tr></table>";
				echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>".$TmpVorrunde."</a></td><td CLASS='blEFreiDisTm'><a CLASS='blEFreiDisT'>".$TmpZwischenlaeufe."</a></td><td CLASS='blEFreiDisTr'><a CLASS='blEFreiDisT'>".$TmpFinale."</a></td></tr></table>";
			
				
			
			

				$Zeilenwechsler = 0;
				$Zeilenwechsler2 = 0;
				unset($TmpStaffelteilnehmer);
				$TmpStaffelteilnehmer = $Ausgabe2Zeile['Teilnehmer'];
				foreach($Ausgabe2Zeile['Teilnehmer'] as $TeilnehmerZeile) {
				
				
				if($TeilnehmerZeile['Riege'] != $RiegeVorher && $TeilnehmerZeile['Riege'] != 0 &&  $TeilnehmerZeile['Riege'] != "") {
					echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEStNrw'>&nbsp;</td><td CLASS='blEFreiDisTlriege'><a CLASS='blEFreiDisTriege'>". $TxtCombinedEventGroup . " ".$TeilnehmerZeile['Riege']."</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td><td CLASS='blEFreiDisTl'><a CLASS='blEFreiDisT'>&nbsp;</a></td></tr></table>";
					$Zeilenwechsler = 0;
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
				
					echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
					if($IPCModeON == 1) {echo "<td CLASS='IPCClass$farbe2'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
					if($StartnummernAn == 1) {echo "<td CLASS='blEStNr$farbe2'>".$TmpaW." ".$TeilnehmerZeile['StNr'] ."</td>";} else {echo "<td CLASS='blEStNr$farbe2'>$TmpaW &nbsp;</td>";}
					echo "<td CLASS='blENameAS$farbe2'>".$TeilnehmerZeile['Verein']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</td>";
					echo "<td CLASS='blEJG$farbe2'>"."&nbsp;"."</td>";
					
					if($FlagsOn == 1) {print("<td CLASS='blELv$farbe2'>"."<img src='" .  $PathToFlags . $TeilnehmerZeile['LV'] . $FileFormatFlags . "' alt='".$TeilnehmerZeile['LV']."' class='imgflags'>"."</td>");}
					else {echo "<td CLASS='blELv$farbe2'>".$TeilnehmerZeile['LV']."</td>";}
					
					echo "<td CLASS='blEVerein$farbe2'>".$TeilnehmerZeile['Verein'] ."</td>";
					echo "<td CLASS='blELeist$farbe2'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>";
					echo "<td CLASS='blEPokP$farbe2'>".$TmpNachmeldung." ".""."</td>";
					echo "</tr></table>";
				
				
				}
				
				#
				foreach($TmpStaffelteilnehmer as $TmpStaffelteilnehmerZeile) {
				
					if($TmpStaffelteilnehmerZeile['Staffel'] != 1 && $TmpStaffelteilnehmerZeile['LV'].$TmpStaffelteilnehmerZeile['VereinNr'] == $TeilnehmerZeile['LV'].$TeilnehmerZeile['VereinNr'] && $TmpStaffelteilnehmerZeile['Meldeleistung'] == $TeilnehmerZeile['JG']) {
					
					
					echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
					echo "<td CLASS='blEStNr$farbe2'>&nbsp;</td>";
					if($IPCModeON == 1) {echo "<td CLASS='IPCClass$farbe2'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
					if($StartnummernAn == 1) {echo "<td CLASS='blEStNr$farbe2'>".$TmpStaffelteilnehmerZeile['StNr'] ."</td>";} else {echo "<td CLASS='blEStNr$farbe2'>&nbsp;</td>";}
					echo "<td CLASS='blENameAS$farbe2'>".$TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>";
					echo "<td CLASS='blEJG$farbe2'>".$TmpStaffelteilnehmerZeile['JG']."</td>";
					echo "<td CLASS='blELv$farbe2'>&nbsp;</td>";
					echo "<td CLASS='blEVerein$farbe2'>&nbsp;</td>";
					echo "<td CLASS='blELeist$farbe2'>&nbsp;</td>";
					echo "<td CLASS='blEPokP$farbe2'>&nbsp;</td>";
					echo "</tr></table>";
					
					
					}
					
					if($TmpStaffelteilnehmerZeile['Staffel'] != 1 && $TmpStaffelteilnehmerZeile['LV'].$TmpStaffelteilnehmerZeile['VereinNr'] == $TeilnehmerZeile['LV'].$TeilnehmerZeile['VereinNr'] && $TmpStaffelteilnehmerZeile['Meldeleistung'] == "" && $TeilnehmerZeile['JG'] == 1) {
					
					
					echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
					echo "<td CLASS='blEStNr$farbe2'>&nbsp;</td>";
					if($IPCModeON == 1) {echo "<td CLASS='IPCClass$farbe2'>".$TmpStaffelteilnehmerZeile['IPCClassName'] ."</td>";}
					if($StartnummernAn == 1) {echo "<td CLASS='blEStNr$farbe2'>".$TmpStaffelteilnehmerZeile['StNr'] ."</td>";} else {echo "<td CLASS='blEStNr$farbe2'>&nbsp;</td>";}
					echo "<td CLASS='blENameAS$farbe2'>".$TmpStaffelteilnehmerZeile['Nachname']. ", ".$TmpStaffelteilnehmerZeile['Vorname'] ."</td>";
					echo "<td CLASS='blEJG$farbe2'>".$TmpStaffelteilnehmerZeile['JG']."</td>";
					echo "<td CLASS='blELv$farbe2'>&nbsp;</td>";
					echo "<td CLASS='blEVerein$farbe2'>&nbsp;</td>";
					echo "<td CLASS='blELeist$farbe2'>&nbsp;</td>";
					echo "<td CLASS='blEPokP$farbe2'>&nbsp;</td>";
					echo "</tr></table>";
					
					
					}
					
					
				
				}
				#unset($TmpStaffelteilnehmer);
				
				
				
				
				break;
				
				default: # alle anderen
				
				$TmpTeilnehmer++;
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

		
			
			echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
			if($IPCModeON == 1) {echo "<td CLASS='IPCClass$farbe'>".$TeilnehmerZeile['IPCClassName'] ."</td>";}
			if($StartnummernAn == 1) {echo "<td CLASS='blEStNr$farbe'>".$TmpaW." ".$TeilnehmerZeile['StNr'] ."</td>";} else {echo "<td CLASS='blEStNr$farbe'>$TmpaW &nbsp;</td>";}
			echo "<td CLASS='blENameAS$farbe'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</td>";
			
			echo "<td CLASS='blEJG$farbe'>".$TeilnehmerZeile['JG'] ."</td>";
			
			if($FlagsOn == 1) {print("<td CLASS='blELv$farbe'>"."<img src='" .  $PathToFlags . $TeilnehmerZeile['LV'] . $FileFormatFlags . "' alt='".$TeilnehmerZeile['LV']."' class='imgflags'>"."</td>");}
					else {echo "<td CLASS='blELv$farbe'>".$TeilnehmerZeile['LV']."</td>";}
			
			
			
			echo "<td CLASS='blEVerein$farbe'>".$TeilnehmerZeile['Verein'] ."</td>";
			echo "<td CLASS='blELeist$farbe'>".str_replace(",", $MarkSeperator1, $TeilnehmerZeile['Meldeleistung']) ."</td>";
			echo "<td CLASS='blEPokP$farbe'>".$TmpAKMK." ".$TmpNachmeldung." ".""."</td>";
			echo "</tr></table>";
			
			
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
				
				
					echo "<p class='AnzahlRunden'><a class='AnzahlRunden'>" . $TmpMannschaftenText . "</a></p>";
				break;
				case "m": # mehrkampf
					echo "<p class='AnzahlRunden'><a class='AnzahlRunden'>$TmpTeilnehmer ". $TxtParticipants . " (";
					$u = 0;
					foreach($TmpTeilnehmerRiege as $key => $value) {
					$u++;
						echo $TxtCombinedEventGroup . "  $key: $value " . $TxtAbbrevParticipants;
						if($u < count($TmpTeilnehmerRiege)) {
							echo ", ";
						}
					
					}
					
					echo ")</a></p>";
				break;
				case "l":
				case "t":
				case "w":
				case "h":
					echo "<p class='AnzahlRunden'><a class='AnzahlRunden'>$TmpTeilnehmer " . $TxtParticipants . "</a></p>";
				break;
				}
				#echo "<br>";
				
				
				
				unset($TmpTeilnehmerRiege);
				$RiegeVorher = "";
				$Zeilenwechsler = 0;
				$Zeilenwechsler2 = 0;
			}
				echo "</div>";
			} # End for each Ausgabe2
			
	
	
	break;
	
	
	case 3: # Teilnehmerliste nach Vereinen #########################################################################
	
		$GesamtteilnehmerAnzahl = 0;
		$GesamtmannschaftenAnzahl = 0;
		$VereineAnzahl = 0;
		$AlleTeilnehmer = 0;
		$AlleStaffelln = 0;
	
	
		#Daten auslesen und aufbereiten
		
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
		
		
		
		$Wettbew[trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3))*1] = array(	'WettbewNr'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3))*1,
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
							'Aktiv'					=> 	$TmpWettbewAktiv
							
							
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
				
				#print(trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 98, 3)));
				#print("<hr>");
				
				$WbTeiln3[trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 4, 3)) * 1 ."-".trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS - 1, 5))] = array ( 	'StNr' => trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS - 1, 5)),
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
							'Riege'			=> trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 28, 2))*1,
							'IPCClassEvent' => 	trim(substr($WbTeilnInhalt, $WbTeilnAbsolutePositionDS + 96, 3))
						);
				}
				$WbTeilnAbsolutePositionDS = $WbTeilnAbsolutePositionDS + $WbTeilnLaengeDatensatz;
			}	
		}
		
		#Stamm
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
			
				if(trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5)) != "*****") {
				
				
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
				
				$Stamm[trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5))] = array(	'StartNr'		=>	trim(substr($StammInhalt, $StammAbsolutePosition - 1, 5)),
																								'Nachname'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 4, 22)),
																								'Vorname'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 27, 16)),
																								'JG'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 44, 4)),
																								'Geschlecht'=> trim(substr($StammInhalt, $StammAbsolutePosition + 48, 1)),
																								'LV'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 50, 3)),
																								'Staffel'	=> $TmpStaffel ,
																								'VereinNr'	=> trim(substr($StammInhalt, $StammAbsolutePosition + 53, 5)),
																								'Wertungsgruppe1'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 71, 1)),
																								'Wertungsgruppe2'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 72, 1)),
																								'Wertungsgruppe3'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 73, 1)),
																								'Wertungsgruppe4'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 74, 1)),
																								'Wertungsgruppe5'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 75, 1)),
																								'Wertungsgruppe6'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 76, 1)),
																								'Wertungsgruppe7'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 77, 1)),
																								'IPCClassName'			=> $TmpIPCClass,
																								'IPC_SDMSID'			=> trim(substr($StammInhalt, $StammAbsolutePosition + 80, 6)),
																								'AthletesLicenceID'		=> trim(substr($StammInhalt, $StammAbsolutePosition + 58, 6))
																								
																								
																					);
				
				
				}
				$StammAbsolutePosition = $StammAbsolutePosition + $StammLaengeDatensatz;
			
	}
}

foreach ($Stamm as $nr => $inhalt) {

	$PStartNr[$nr] = strtolower($inhalt['StartNr']);
	$PNachname[$nr] = strtolower($inhalt['Nachname']);
	$PVorname[$nr] = strtolower($inhalt['Vorname']);
	$PJG[$nr] = strtolower($inhalt['JG']);
	$PGeschlecht[$nr] = strtolower($inhalt['Geschlecht']);
	$PLV[$nr] = strtolower($inhalt['LV']);
	$PStaffel[$nr] = strtolower($inhalt['Staffel']);
	$PVerein[$nr] = strtolower($inhalt['Verein']);
	$PWertungsgruppe1[$nr] = strtolower($inhalt['Wertungsgruppe1']);
	$PWertungsgruppe2[$nr] = strtolower($inhalt['Wertungsgruppe2']);
	$PWertungsgruppe3[$nr] = strtolower($inhalt['Wertungsgruppe3']);
	$PWertungsgruppe4[$nr] = strtolower($inhalt['Wertungsgruppe4']);
	$PWertungsgruppe5[$nr] = strtolower($inhalt['Wertungsgruppe5']);
	$PWertungsgruppe6[$nr] = strtolower($inhalt['Wertungsgruppe6']);
	$PWertungsgruppe7[$nr] = strtolower($inhalt['Wertungsgruppe7']);
	$PIPCClassName[$nr] = strtolower($inhalt['IPCClassName']);
	$PIPC_SDMSID[$nr] = strtolower($inhalt['IPC_SDMSID']);
	$PAthletesLicenceID[$nr] = strtolower($inhalt['AthletesLicenceID']);
	$PSortieren[$nr] = strtolower($inhalt['Staffel'])." ".strtolower($inhalt['Nachname'])." ".strtolower($inhalt['Vorname'])." ".strtolower($inhalt['Verein']);
}
	
	if($StartnummernAn == 1){ array_multisort($PStartNr, SORT_ASC, $Stamm); }
	else { array_multisort($PSortieren, SORT_ASC, $Stamm); }


		
		
		
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
				
				#Teilnehmer zu den Vereinen zuordnen
				
				foreach($Stamm as $StammZeile) {
				
					if($StammZeile['LV'] == trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3)) && $StammZeile['VereinNr'] == trim(substr($VereinInhalt, $VereinAbsolutePosition + 2, 5))) {
					
						
						
						foreach($WbTeiln3 as $WbTeiln3Zeile) {
						
							if($WbTeiln3Zeile['StNr'] == $StammZeile['StartNr']) {
							
							if($Wettbew[$WbTeiln3Zeile['WettbewNr']]['Aktiv'] != 0) {
							
							# Disziplin-Kurz-Bez. bei eigenen Wettbewerben
							if($Wettbew[$WbTeiln3Zeile['WettbewNr']]['WettbewKurz'] != "") {
								$TmpWettbewKurz = $Wettbew[$WbTeiln3Zeile['WettbewNr']]['WettbewKurz'];
							}
							else {
								$TmpWettbewKurz = $Disziplinen[substr($WbTeiln3Zeile['COSANr'], 2, 3)*1]['Kurz'];
							}
							
							
							$WettbewerbeProTeilnehmer[] = array(	'WettbewNr'		=>	$WbTeiln3Zeile['WettbewNr'],
													'WettbewTyp'		=>	$Wettbew[$WbTeiln3Zeile['WettbewNr']]['WettbewTyp'],
													'COSANr'		=>	$WbTeiln3Zeile['COSANr'],
													'aW'		=>	$WbTeiln3Zeile['aW'],
													'Meldeleistung'		=>	$WbTeiln3Zeile['Meldeleistung'],
													'AK'		=>	$WbTeiln3Zeile['AK'],
													'Staffel'		=>	$WbTeiln3Zeile['Staffel'],
													'Nachmeldung'		=>	$WbTeiln3Zeile['Nachmeldung'],
													'Riege'		=>	$WbTeiln3Zeile['Riege'],
													'Altersklasse'	=>	$Klassen[substr($WbTeiln3Zeile['COSANr'], 0, 2)]['Bez'],
													'Disziplin'	=>	$TmpWettbewKurz,
													'IPCClassEvent' => $WbTeiln3Zeile['IPCClassEvent']
													);
							#print_r($WettbewerbeProTeilnehmer);
							#print("<hr>");
							unset($TmpWettbewKurz);
							#unset($WbTeiln3Zeile);
							
							}
							}
						
						}
		
						
						$TeilnehmerImVerein[] = array (	'StartNr'			=>	$StammZeile['StartNr'],
														'Nachname'			=>	$StammZeile['Nachname'],
														'Vorname'			=>	$StammZeile['Vorname'],
														'JG'				=>	$StammZeile['JG'],
														'Geschlecht'		=>	$StammZeile['Geschlecht'],
														'Staffel'			=>	$StammZeile['Staffel'],
														'Wertungsgruppe1'	=>	$StammZeile['Wertungsgruppe1'],
														'Wertungsgruppe2'	=>	$StammZeile['Wertungsgruppe2'],
														'Wertungsgruppe3'	=>	$StammZeile['Wertungsgruppe3'],
														'Wertungsgruppe4'	=>	$StammZeile['Wertungsgruppe4'],
														'Wertungsgruppe5'	=>	$StammZeile['Wertungsgruppe5'],
														'Wertungsgruppe6'	=>	$StammZeile['Wertungsgruppe6'],
														'Wertungsgruppe7'	=>	$StammZeile['Wertungsgruppe7'],
														'Wettbewerbe'		=>	$WettbewerbeProTeilnehmer,
														'IPCClassName'	=>	$StammZeile['IPCClassName'],
														'IPC_SDMSID'	=>	$StammZeile['IPC_SDMSID'],
														'AthletesLicenceID'	=>	$StammZeile['AthletesLicenceID']
														);
						unset($WettbewerbeProTeilnehmer);
					
					}
				
				}
				

				#Ausgabe erstellen
				if(count($TeilnehmerImVerein) > 0 ) {
				$AusgabeLinkliste[trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3))."-".trim(substr($VereinInhalt, $VereinAbsolutePosition + 2, 5))] = array(	'LV'		=>	trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3)),
																																						'VereinNr'	=> trim(substr($VereinInhalt, $VereinAbsolutePosition + 2, 5)),
																																						'VereinBez'	=> trim(substr($VereinInhalt, $VereinAbsolutePosition + 7, 30)),
																																						'Sortierung'	=> trim(substr($VereinInhalt, $VereinAbsolutePosition + 62, 25)));
				
				}
				
				$Ausgabe3[trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3))."-".trim(substr($VereinInhalt, $VereinAbsolutePosition + 2, 5))] = array(	'LV'		=>	trim(substr($VereinInhalt, $VereinAbsolutePosition - 1, 3)),
																																						'VereinNr'	=> trim(substr($VereinInhalt, $VereinAbsolutePosition + 2, 5)),
																																						'VereinBez'	=> trim(substr($VereinInhalt, $VereinAbsolutePosition + 7, 30)),
																																						'Sortierung'	=> trim(substr($VereinInhalt, $VereinAbsolutePosition + 62, 25)),
																																						'Teilnehmer'	=> $TeilnehmerImVerein
																																		);
				unset($TeilnehmerImVerein);
				$VereinAbsolutePosition = $VereinAbsolutePosition + $VereinLaengeDatensatz;
			}

		}
	
	
	# Sortieren
		# Array Linkliste Sortieren nach Sortierung

foreach ($AusgabeLinkliste as $nr => $inhalt) {

	$NLV[$nr] = strtolower($inhalt['LV']);
	$NVereinNr[$nr] = strtolower($inhalt['VereinNr']);
	$NVereinBez[$nr] = strtolower($inhalt['VereinBez']);
	$NSortierung[$nr] = strtolower($inhalt['Sortierung']);
	$NSortierung2[$nr] = strtolower($inhalt['Sortierung'])." ".strtolower($inhalt['VereinBez']);
	
}
	
	array_multisort($NSortierung2, SORT_ASC, $AusgabeLinkliste);
	
	foreach ($Ausgabe3 as $nr => $inhalt) {

	$OLV[$nr] = strtolower($inhalt['LV']);
	$OVereinNr[$nr] = strtolower($inhalt['VereinNr']);
	$OVereinBez[$nr] = strtolower($inhalt['VereinBez']);
	$OSortierung[$nr] = strtolower($inhalt['Sortierung']);
	$OTeilnehmer[$nr] = $inhalt['Teilnehmer'];
	$OSortierung2[$nr] = strtolower($inhalt['Sortierung'])." ".strtolower($inhalt['VereinBez']);
	
}
	
	array_multisort($OSortierung2, SORT_ASC, $Ausgabe3);
	
	
	
	#___________________________________________
		#Daten ausgeben
		?>

	<table class="laivemenu">
	<tr>
		
		<td class="linkliste">
			<ul class="secoundmenu">
				<?php 
						
						
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=gesamtteilnehmer.php&amp;list=1&amp;wg=0'>" . $TxtLinkSubMenuEntriesList1 . "</a>
							</li>";
						
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=0&amp;sort=$TeilnNachWettbewStandardSort'>" . $TxtLinkSubMenuEntriesList2 . "</a>
							</li>"; 
						
						echo "<li class='topmenu'>
								<a class='linkliste' href='?sub=gesamtteilnehmer.php&amp;list=3'>" . $TxtLinkSubMenuEntriesList3 . "</a>
							</li>"; 
						
						?>

			</ul>
		</td>
		<td class="aktualisiert" align="right"><a class="aktualisiert"><?php echo $TxtSubMenuUpdated . " ".date("d.m.y H:i", max(filemtime($dat_stamm), filemtime($dat_wbteiln), filemtime($dat_verein))) ; ?></a></td>
	</tr>
</table>

		<table class="body" cellspacing="0">
			<tr><td class="KopfZ1"><?php echo $Kopfzeile1; ?></td></tr>
			<tr><td class="KopfZ11"><?php echo $Kopfzeile2; ?></td></tr>
			<tr><td class="KopfZ12"><?php echo $Kopfzeile3; ?></td></tr>
			</table>



		<table class="body" cellspacing="0">
		<tr><td class="KopfZ21"><?php echo $txt_gesamtteilnehmerlistenachverein; ?></td></tr>
		</table>
		<br class="noprint">
<?php # Entry Notes output
if(file_exists("./laive_entrylist_notes.txt") && $EntrylistNotesCount <> 0) {	
	print("<p class='entrylistnotes'>");
	foreach($EntrylistNotesContent as $EntrylistNotesContentLine) {
		print("<a class='entrylistnotes'>" . $EntrylistNotesContentLine . "</a><br>");
	}
	print("</p>");
}
?>
		
		
<?php
		# Athletennummern-Links / Athlete's licence IDs Lins show
		if($AthletesLicenceIDOn == 1) {	
			echo "<p class='LinkStellplatz'><a class='LinkStellplatz' href='?sub=gesamtteilnehmer.php&amp;list=3&amp;AthletesLicenceID=1'>".$TxtAthletesLicenceIDShowHead."</a> / <a class='LinkStellplatz' href='?sub=gesamtteilnehmer.php&amp;list=3&amp;AthletesLicenceID=0'>".$TxtAthletesLicenceIDDontShowHead."</a></p>";
		}


			echo "<table class='bodynoprint' cellspacing='0'><tr><td class='KopfZ2'>".$TxtClubs."</td></tr></table>";
			
			$AnzahlInLinkliste = count($AusgabeLinkliste);
			$AnzahlEineSpalte = ceil($AnzahlInLinkliste / 3);
			$LinkZaehler = 0;
			
			echo "<table class='bodynoprint' cellspacing='0'>";
			if($AnzahlInLinkliste > 2) {
			
				echo "<tr><td class='blGrundLink'>";
				foreach ($AusgabeLinkliste as $AusgabeLinklisteZeile) {
					
					
					if($LinkZaehler == $AnzahlEineSpalte || $LinkZaehler == $AnzahlEineSpalte * 2) {
					
						echo "</td>";
						echo "<td class='blGrundLink'>";
					
					}
					
				echo "<a href='#".$AusgabeLinklisteZeile['LV'].$AusgabeLinklisteZeile['VereinNr']."'>".$AusgabeLinklisteZeile['VereinBez']."</a><br>";
			
			
					if($LinkZaehler == $AnzahlInLinkliste) {
						echo "</td>";
					}
					
					$LinkZaehler++;
				}
				echo "</tr>";
			
			
			}
			else {
			
			foreach ($AusgabeLinkliste as $AusgabeLinklisteZeile) {
			
			
			
			
			
			
				
				echo "<tr><td class='blGrund'><a href='#".$AusgabeLinklisteZeile['LV'].$AusgabeLinklisteZeile['VereinNr']."'>".$AusgabeLinklisteZeile['VereinBez']."</a></td></tr>";
			
			}
			}
			echo "</table>";
			echo "<br class='noprint'>";
			echo "<hr>";
			echo "<br>";
			
			foreach($Ausgabe3 as $Ausgabe3Zeile) {
			
			if(count($Ausgabe3Zeile['Teilnehmer']) > 0 && $Ausgabe3Zeile['VereinNr'] != "") {
			$VereineAnzahl++;
			
			
			
			
			
				echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr><td CLASS='blEWettb'>";
				
				if($FlagsOn == 1) {print("<img src='" .  $PathToFlags . $Ausgabe3Zeile['LV'] . $FileFormatFlags . "' alt='".$Ausgabe3Zeile['LV']."' class='imgflags'>");}
				
				
				echo "<a name='".$Ausgabe3Zeile['LV'].$Ausgabe3Zeile['VereinNr']."'>".$Ausgabe3Zeile['VereinBez']."</a></td><td CLASS='blEWind'></td><td CLASS='blEDatum'>(".$Ausgabe3Zeile['LV']." ".$Ausgabe3Zeile['VereinNr'] .")</td></tr></table>";
				#echo $Ausgabe3Zeile['Sortierung'];
			

				
				
				
				
				
				echo "<table CLASS='body' cellspacing='0' cellpadding='0'>";
			
				echo "<tr>";
				 if($StartnummernAn == 1) { echo "<td CLASS='blEStNru'>$txt_startnummer</td>";} else {echo "<td CLASS='blEStNru'>&nbsp;</td>"; }
					echo "<td CLASS='blENameASu'>$txt_name</td>";
					
					echo "<td CLASS='blELvu'>$txt_geschlecht</td>";
					echo "<td CLASS='blEJGu'>$txt_jahrgang</td>";
					echo "<td CLASS='blEgemwettbewu'><a CLASS='blEgemwettbewu'>$txt_gemeldetewettbewerbe</a></td>";
				echo "</tr>";
			
			echo "</table>";

			$Zeilenwechsler = 0;
			# Teilnehmer ausgeben
			foreach($Ausgabe3Zeile['Teilnehmer'] as $TeilnehmerZeile) {
			
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
		
		
			
			
			switch($TeilnehmerZeile['Staffel']) {
			
				case 1:  # Staffel
				
				$GesamtmannschaftenAnzahl++;
				$AlleStaffeln++;
				
				echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
			
			if($StartnummernAn == 1) {echo "<td CLASS='blEStNrV$farbe'>".$TeilnehmerZeile['StartNr'] ."</td>";} else {echo "<td CLASS='blEStNrV$farbe'>&nbsp;</td>";}
			echo "<td CLASS='blENameASV$farbe'>".$Ausgabe3Zeile['VereinBez']." ". $Roemisch[$TeilnehmerZeile['JG']] ."</td>";
			echo "<td CLASS='blELvV$farbe'>".$tmpgeschlecht ."</td>";
			echo "<td CLASS='blEJGV$farbe'>"." " ."</td>";
			echo "<td class='blEgemwettbew$farbe'>";
			
				echo "<table cellspacing='0' cellpadding='0'>";
				$IPCClassStart = 1;
				# Wettbewerbe ausgeben
				foreach($TeilnehmerZeile['Wettbewerbe'] as $WettbewerbeZeile) {
				
				
				if($WettbewerbeZeile['aW'] == 1) {
							$TmpaW = "(".$TxtAbbrevOutOfRanking.")";
						}
						else {
							$TmpaW = "";
						}
						
						
					
						if($WettbewerbeZeile['Nachmeldung'] == 1) {
							$TmpNachmeldung = "<a class='nachmeldung'>- ".$TxtLateEntry."</a>";
						}
						else {
							$TmpNachmeldung = "";
						}
						
						if($WettbewerbeZeile['Meldeleistung'] != "") {
							$TmpMeldeleistung = str_replace(",", $MarkSeperator1, $WettbewerbeZeile['Meldeleistung']);
						}
						else {
							$TmpMeldeleistung = "";
						}
						
						if(strlen($WettbewerbeZeile['Meldeleistung']) < 3 && strlen($WettbewerbeZeile['Meldeleistung']) != "") {
							$TmpMeldeleistung = $TxtAbbrevRelayTeam." " . $Roemisch[$WettbewerbeZeile['Meldeleistung']];
						}
						
						
						
						if($WettbewerbeZeile['Riege'] != "" && $WettbewerbeZeile['Riege'] != 0) {
							$TmpRiege = $TxtCombinedEventGroup . " ".$WettbewerbeZeile['Riege'];
						}
						else {
							$TmpRiege = "";
						}
						
						echo "<tr><td class='blEWettbew'><a class='blEWettbew' href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=".$_GET['wg']."&amp;sort=$TeilnNachWettbewStandardSort#wb".$WettbewerbeZeile['WettbewNr']."'>". $WettbewerbeZeile['Disziplin']." ".$WettbewerbeZeile['Altersklasse']."</a> ".$TmpaW. " ".$TmpNachmeldung."</td>";
						echo "<td class='blEMeldeleistung'>". $TmpMeldeleistung ."</td>";
						echo "<td class='blERiege'>";
							if($IPCModeON == 1) {
								#echo $DBSTextskl_entrylists[$WettbewerbeZeile['IPCClassEvent']]['IPCClassName'];
								echo "<a class ='IPCClassEvent'>" ."<abbr title='".$WettbewerbeZeile['IPCClassEvent']."'>".$DBSTextskl_entrylists[$WettbewerbeZeile['IPCClassEvent']]['IPCClassName']."</abbr>" . "</a>";
								
							}
						echo $TmpRiege ."</td>";
						echo "</tr>";
				
				
				
				
				} # Wettbewerbe ausgeben
				
				unset($WettbewerbeZeile);
				
				if($IPCModeON == 1 && count($TeilnehmerZeile['Wettbewerbe']) == 0) {
					echo "<tr><td class='blEWettbew'>&nbsp;</td>";
						echo "<td class='blEMeldeleistung'>&nbsp;</td>";
						echo "<td class='blERiege'>";
							if($IPCModeON == 1) {
								#echo "<a class ='IPCClassEvent'>" . $DBSTextskl_entrylists[$WettbewerbeZeile['IPCClassEvent']]['IPCClassName'] . "</a>";
								#echo "<a class ='IPCClassEvent'>" ."<abbr title='".$WettbewerbeZeile['IPCClassEvent']."'>".$DBSTextskl_entrylists[$WettbewerbeZeile['IPCClassEvent']]['IPCClassName']."</abbr>" . "</a>";
							}
						echo "</td>";
						echo "</tr>";
				}
				
				echo "</table>";
			
			echo "</td>";
			
			echo "</tr></table>";
			
			# Athleten-Nr. / Athletes Licence ID
			if(isset($_GET['AthletesLicenceID']) && $_GET['AthletesLicenceID'] == 1 && $TeilnehmerZeile['AthletesLicenceID'] != "") {
				
				if($TeilnehmerZeile['AthletesLicenceID'] > 599999) {
						$TmpTxtLicenceID = $TxtAthletesOnlineID;
				}
				else {
						$TmpTxtLicenceID = $TxtAthletesLicenceID;
				}
			
				echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				echo "<td CLASS='blEStNrV$farbe'>"."&nbsp;" ."</td>";
				echo "<td CLASS='blEWertungsgruppenV$farbe'>";
				echo $TmpTxtLicenceID.": ".  $TeilnehmerZeile['AthletesLicenceID'];
				echo "</td>";
				echo "</tr></table>";
			}
			
			# IPC SDMSID
			if($IPCModeON == 1) {
				echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				echo "<td CLASS='blEStNrV$farbe'>"."&nbsp;" ."</td>";
				echo "<td CLASS='blEIPCCodeV$farbe'>";
				echo $TxtSDMSID.": ".  $TeilnehmerZeile['IPC_SDMSID'];
				echo "</td>";
				echo "<td CLASS='blEIPCStartClass$farbe'>";
				echo $TeilnehmerZeile['IPCClassName'];
				echo "</td>";
				echo "<td CLASS='blEIPCempty$farbe'>&nbsp;</td>";
				echo "</tr></table>";
			}
			
			
			
			
			if($TeilnehmerZeile['Wertungsgruppe1'] == 1 || $TeilnehmerZeile['Wertungsgruppe2'] == 1 || $TeilnehmerZeile['Wertungsgruppe3'] == 1 || $TeilnehmerZeile['Wertungsgruppe4'] == 1 || $TeilnehmerZeile['Wertungsgruppe5'] == 1 || $TeilnehmerZeile['Wertungsgruppe6'] == 1 || $TeilnehmerZeile['Wertungsgruppe7'] == 1) {
			
			echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
			
			echo "<td CLASS='blEStNrV$farbe'>"."&nbsp;" ."</td>";
			echo "<td CLASS='blEWertungsgruppenV$farbe'>";
			
			if($TeilnehmerZeile['Wertungsgruppe1'] == 1) {
				echo $wertungsgruppen[1]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe2'] == 1) {
				echo $wertungsgruppen[2]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe3'] == 1) {
				echo $wertungsgruppen[3]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe4'] == 1) {
				echo $wertungsgruppen[4]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe5'] == 1) {
				echo $wertungsgruppen[5]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe6'] == 1) {
				echo $wertungsgruppen[6]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe7'] == 1) {
				echo $wertungsgruppen[7]."&nbsp;&nbsp;";
			}
			
			echo "</td>";
			echo "</tr></table>";
			
			}			
				
				
				break;
				
				default: # alle anderen
				$GesamtteilnehmerAnzahl++;
				$AlleTeilnehmer++;
	
	
			
		
		
			
			echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
			
			if($StartnummernAn == 1) {echo "<td CLASS='blEStNrV$farbe'>".$TeilnehmerZeile['StartNr'] ."</td>";} else {echo "<td CLASS='blEStNrV$farbe'>&nbsp;</td>";}
			echo "<td CLASS='blENameASV$farbe'>".$TeilnehmerZeile['Nachname'].", ". $TeilnehmerZeile['Vorname'] ."</td>";
			echo "<td CLASS='blELvV$farbe'>".$tmpgeschlecht ."</td>";
			echo "<td CLASS='blEJGV$farbe'>".$TeilnehmerZeile['JG'] ."</td>";
			echo "<td class='blEgemwettbew$farbe'>";
			
				echo "<table cellspacing='0' cellpadding='0'>";
				
				$IPCClassStart = 1;
				
				if(count($TeilnehmerZeile['Wettbewerbe']) > 0) {
				# Wettbewerbe ausgeben
				foreach($TeilnehmerZeile['Wettbewerbe'] as $WettbewerbeZeile) {
				
				
				if($WettbewerbeZeile['aW'] == 1) {
							$TmpaW = "(".$TxtAbbrevOutOfRanking.")";
						}
						else {
							$TmpaW = "";
						}
						
						
					
						if($WettbewerbeZeile['Nachmeldung'] == 1) {
							$TmpNachmeldung = "<a class='nachmeldung'>- ".$TxtLateEntry."</a>";
						}
						else {
							$TmpNachmeldung = "";
						}
						
						if($WettbewerbeZeile['Meldeleistung'] != "") {
							$TmpMeldeleistung = str_replace(",", $MarkSeperator1, $WettbewerbeZeile['Meldeleistung']);
						}
						else {
							$TmpMeldeleistung = "";
						}
						
						if(strlen($WettbewerbeZeile['Meldeleistung']) < 3 && strlen($WettbewerbeZeile['Meldeleistung']) != "") {
							$TmpMeldeleistung = $TxtAbbrevRelayTeam. " " . $Roemisch[$WettbewerbeZeile['Meldeleistung']];
						}
						
						
						
						if($WettbewerbeZeile['Riege'] != "" && $WettbewerbeZeile['Riege'] != 0) {
							$TmpRiege = $TxtCombinedEventGroup . " ".$WettbewerbeZeile['Riege'];
						}
						else {
							$TmpRiege = "";
						}
						
						if($WettbewerbeZeile['WettbewTyp'] == "m") {
						
							if(is_numeric($WettbewerbeZeile['AK'])) {
								$TmpAKMK = " - ". $tmpgeschlechtAK.$WettbewerbeZeile['AK'];
							}
							else {
								$TmpAKMK = "";
							}
						}
						else {
							$TmpAKMK = "";
						}
						
						echo "<tr><td class='blEWettbew'><a class='blEWettbew' href='?sub=gesamtteilnehmer.php&amp;list=2&amp;wg=".$_GET['wg']."&amp;sort=$TeilnNachWettbewStandardSort#wb".$WettbewerbeZeile['WettbewNr']."'>". $WettbewerbeZeile['Disziplin']." ".$WettbewerbeZeile['Altersklasse']."</a> ".$TmpAKMK." ".$TmpaW. " ".$TmpNachmeldung."</td>";
						echo "<td class='blEMeldeleistung'>". $TmpMeldeleistung ."</td>";
						echo "<td class='blERiege'>";
							if($IPCModeON == 1) {
								#echo "<a class ='IPCClassEvent'>" . $DBSTextskl_entrylists[$WettbewerbeZeile['IPCClassEvent']]['IPCClassName'] . "</a>";
								#echo $WettbewerbeZeile['IPCClassEvent'];
								echo "<a class ='IPCClassEvent'>" ."<abbr title='".$WettbewerbeZeile['IPCClassEvent']."'>".$DBSTextskl_entrylists[$WettbewerbeZeile['IPCClassEvent']]['IPCClassName']."</abbr>" . "</a>";
							}
						echo $TmpRiege ."</td>";
						echo "</tr>";
				
				
				
				
				} # Wettbewerbe ausgeben
				}
				
				if($IPCModeON == 1 && count($TeilnehmerZeile['Wettbewerbe']) == 0) {
					echo "<tr><td class='blEWettbew'>&nbsp;</td>";
						echo "<td class='blEMeldeleistung'>&nbsp;</td>";
						echo "<td class='blERiege'>";
							if($IPCModeON == 1) {
								#echo "<a class ='IPCClassEvent'>" . $DBSTextskl_entrylists[$WettbewerbeZeile['IPCClassEvent']]['IPCClassName'] . "</a>";
								#echo "<a class ='IPCClassEvent'>" ."<abbr title='".$WettbewerbeZeile['IPCClassEvent']."'>".$DBSTextskl_entrylists[$WettbewerbeZeile['IPCClassEvent']]['IPCClassName']."</abbr>" . "</a>";
							}
						echo "</td>";
						echo "</tr>";
				}
				
				
				echo "</table>";
			
			echo "</td>";
			
			echo "</tr></table>";
			
			# Athleten-Nr. / Athletes Licence ID
			if(isset($_GET['AthletesLicenceID']) && $_GET['AthletesLicenceID'] == 1 && $TeilnehmerZeile['AthletesLicenceID'] != "") {
				
				if($TeilnehmerZeile['AthletesLicenceID'] > 599999) {
						$TmpTxtLicenceID = $TxtAthletesOnlineID;
				}
				else {
						$TmpTxtLicenceID = $TxtAthletesLicenceID;
				}
			
				echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				echo "<td CLASS='blEStNrV$farbe'>"."&nbsp;" ."</td>";
				echo "<td CLASS='blEWertungsgruppenV$farbe'>";
				echo $TmpTxtLicenceID.": ".  $TeilnehmerZeile['AthletesLicenceID'];
				echo "</td>";
				echo "</tr></table>";
			}
			
			# IPC SDMSID
			if($IPCModeON == 1) {
				echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
				echo "<td CLASS='blEStNrV$farbe'>"."&nbsp;" ."</td>";
				echo "<td CLASS='blEIPCCodeV$farbe'>";
				echo $TxtSDMSID.": ".  $TeilnehmerZeile['IPC_SDMSID'];
				echo "</td>";
				echo "<td CLASS='blEIPCStartClass$farbe'>";
				echo $TeilnehmerZeile['IPCClassName'];
				echo "</td>";
				echo "<td CLASS='blEIPCempty$farbe'>&nbsp;</td>";
				echo "</tr></table>";
			}
			
			if($TeilnehmerZeile['Wertungsgruppe1'] == 1 || $TeilnehmerZeile['Wertungsgruppe2'] == 1 || $TeilnehmerZeile['Wertungsgruppe3'] == 1 || $TeilnehmerZeile['Wertungsgruppe4'] == 1 || $TeilnehmerZeile['Wertungsgruppe5'] == 1 || $TeilnehmerZeile['Wertungsgruppe6'] == 1 || $TeilnehmerZeile['Wertungsgruppe7'] == 1) {
			
			echo "<table CLASS='body' cellspacing='0' cellpadding='0'><tr>";
			
			echo "<td CLASS='blEStNrV$farbe'>"."&nbsp;" ."</td>";
			echo "<td CLASS='blEWertungsgruppenV$farbe'>";
			
			if($TeilnehmerZeile['Wertungsgruppe1'] == 1) {
				echo $wertungsgruppen[1]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe2'] == 1) {
				echo $wertungsgruppen[2]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe3'] == 1) {
				echo $wertungsgruppen[3]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe4'] == 1) {
				echo $wertungsgruppen[4]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe5'] == 1) {
				echo $wertungsgruppen[5]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe6'] == 1) {
				echo $wertungsgruppen[6]."&nbsp;&nbsp;";
			}
			if($TeilnehmerZeile['Wertungsgruppe7'] == 1) {
				echo $wertungsgruppen[7]."&nbsp;&nbsp;";
			}
			
			echo "</td>";
			echo "</tr></table>";
			
			}	
			
			break;
			}
			} # Teilnehmer ausgeben

		
															if ($GesamtteilnehmerAnzahl != "" && $GesamtmannschaftenAnzahl != "") {
																$TrennzeichenTeilnStaffeln = " ".$TxtAnd." ";
															}
															else {
																$TrennzeichenTeilnStaffeln = "";
															}
															
															if($GesamtteilnehmerAnzahl  > 0) {
																$GesamtteilnehmerAnzahlText = $GesamtteilnehmerAnzahl . " ".$TxtParticipants;
															}
															else {
																$GesamtteilnehmerAnzahlText = "";
															}
															
															switch($GesamtmannschaftenAnzahl) {
																case 0:
																	$GesamtmannschaftenAnzahlText = "";
																break;
																case 1:
																	$GesamtmannschaftenAnzahlText = $GesamtmannschaftenAnzahl . " ".$TxtRelayTeam;
																break;
																default:
																	$GesamtmannschaftenAnzahlText = $GesamtmannschaftenAnzahl . " ".$TxtRelayTeams;
																break;
															}
															
	echo "<p class='ParticipantsTeamsByClub'><a class='AnzahlRunden'>" . $GesamtteilnehmerAnzahlText . $TrennzeichenTeilnStaffeln . $GesamtmannschaftenAnzahlText . "</a></p>";
	
	$GesamtteilnehmerAnzahl = 0;
	$GesamtmannschaftenAnzahl = 0;
	
	# Headline for printing
			
			echo "<table class='laivemenuPrint'>";
				echo "<tr class='laivemenuPrint'>";
					echo "<td class='linklistePrint'> </td>";
					echo "<td class='aktualisiertPrint' align='right'><a class='aktualisiertPrint'>" . $TxtSubMenuUpdated . " " . date("d.m.y H:i", max(filemtime($dat_stamm), filemtime($dat_wbteiln), filemtime($dat_verein))) . "</a></td>";
				echo "</tr>";
			echo "</table>";
			
			echo "<table class='bodyPrint' cellspacing='0'>";
			echo "<tr class='KopfPrint'><td class='KopfZ1Print'>" . $Kopfzeile1 . "</td></tr>";
			echo "<tr class='KopfPrint'><td class='KopfZ11Print'>" . $Kopfzeile2 . "</td></tr>";
			echo "<tr class='KopfPrint'><td class='KopfZ12Print'>" . $Kopfzeile3 . "</td></tr>";
			echo "</table>";

			echo "<table class='bodyPrint' cellspacing='0'>";
				echo "<tr class='KopfPrint'><td class='KopfZ21Print'>" . $txt_gesamtteilnehmerlistenachverein . "</td></tr>";
			echo "</table>";
			echo "<hr class='HRPrint'>";
# Entry Notes output
if(file_exists("./laive_entrylist_notes.txt") && $EntrylistNotesCount <> 0) {	
	print("<p class='entrylistnotesPrint'>");
	foreach($EntrylistNotesContent as $EntrylistNotesContentLine) {
		print("<a class='entrylistnotesPrint'>" . $EntrylistNotesContentLine . "</a><br class='BRPrint'>");
	}
	print("</p>");
}

	
	
	
	}
	
	
	} # Vereine, Teilnehmer, Staffeln ausgeben
	
	if ($AlleTeilnehmer > 0) {
		$TxtAlleTeilnehmer = $AlleTeilnehmer . " ".$TxtParticipants;
	}
	else {
		$TxtAlleTeilnehmer = "";
	}
	
	switch($AlleStaffeln) {
		case 0:
			$TxtAlleStaffeln = "";
		break;
		case 1:
			$TxtAlleStaffeln = $AlleStaffeln . " ".$TxtRelayTeam;
		break;
		default:
			$TxtAlleStaffeln = $AlleStaffeln . " ".$TxtRelayTeams;
		break;
	}
	
	if($AlleTeilnehmer > 0 || $AlleStaffeln > 0) {
		$Trenner1 = ", ";
	}
	
	if($AlleTeilnehmer > 0 && $AlleStaffeln > 0) {
		$Trenner2 = ", ";
	}
	
	echo "<hr>";
	echo "<p class='AnzahlRunden'><a class='AnzahlRunden'>" . $VereineAnzahl . " ". $TxtClubs . $Trenner1 . $TxtAlleTeilnehmer . $Trenner2 . $TxtAlleStaffeln . "</a></p>";
	
	break;
	
	
	
} # Master








?>
