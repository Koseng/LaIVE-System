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
### LaIVE - Modul Stellplatzzeitplan (stellplatzzeitplan.php) / LaIVE - Modul Final Confirmation timetable (stellplatzzeitplan.php)
### Erstellt von / Created by: Kilian Wenzel
### Zuletzt geändert: / Last change: 0.9.0.2013-06-18

				
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

		
		$Wettbew[] = array(	'WettbewNr'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS - 1, 3)),
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
							'DISBez'			=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 296, 32)),
							'AKBez'				=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 331, 24)),
							'WettbewTyp'		=>	trim(substr($WettbewInhalt, $WettbewAbsolutePositionDS + 41, 1))
		);			
	
		$WettbewAbsolutePositionDS = $WettbewAbsolutePositionDS + $WettbewLaengeDatensatz;
	
	}
	

}

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
	
	$TeilnehmeranzahlWb = array_count_values($WbTeiln);

}

# Datei "WbTeil.c01" verwenden um die Teilnehmerzahlen zu ermitteln --- Staffeln
if(file_exists($dat_wbteiln)) {

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


## Wettbew-Array ausgeben um den Zeitplan zu erstellen

foreach($Wettbew as $WettbewZeile) {

if(empty($WettbewZeile['StellplatzZeit']) == false) {

	# Prüfen, woher der Tag für Stellplatz-Zeit genommen werden muss
	if(empty($WettbewZeile['VorlaufTag']) == false) {
	
		$StellplatzTag = $WettbewZeile['VorlaufTag'];
	}
	else {
		if(empty($WettbewZeile['FinaleTag']) == false) {
		
			$StellplatzTag = $WettbewZeile['FinaleTag'];
		
		}
	}

	
	


# Daten in Variablen
				$WettbewerbNr 			= $WettbewZeile['WettbewNr'];
				$WettbewerbBez 			= $WettbewZeile['WettbewBez'];
				if($WettbewZeile['WettbewTyp'] == "s") {
				$TeilnStaffeln 			= $StTeilnehmeranzahlWb[$WettbewerbNr];
				}
				else {
				$TeilnStaffeln 			= $TeilnehmeranzahlWb[$WettbewerbNr];
				}
				$StellplatzMin			= $WettbewZeile['StellplatzMin'];
				$StellplatzZeit			= $WettbewZeile['StellplatzZeit'];
				$COSANrAK				= $WettbewZeile['COSANrAK'];
				$COSANrDIS				= $WettbewZeile['COSANrDIS'];
				$COSANr					= $COSANrAK.$COSANrDIS;
				$DISBez					= $WettbewZeile['DISBez'];
				$AKBez					= $WettbewZeile['AKBez'];
				$ZeitSortierung			= $StellplatzTag."-".$StellplatzZeit."-".$COSANrAK."-".$COSANrDIS;

				
				$Stellplatz[]	= array(	'WettbewerbNr'		=>	$WettbewerbNr,
											'WettbewerbBez'		=>	$WettbewerbBez,
											'TeilnStaffeln'		=> $TeilnStaffeln,
											'StellplatzMin'		=>	$StellplatzMin,
											'StellplatzZeit'	=>	$StellplatzZeit,
											'StellplatzTag'		=>	$StellplatzTag,
											'COSANrAK'			=>	$COSANrAK,
											'COSANrDIS'			=>	$COSANrDIS,
											'COSANr'			=>	$COSANr,
											'DISBez'			=>	$DISBez,
											'AKBez'				=>	$AKBez,
											'ZeitSortierung'	=>	$ZeitSortierung);
				
}				
} # Master


# Array (Stellplatz) sortieren nach Startzeit


foreach ($Stellplatz as $nr => $inhalt) {

	$TWettbewerbNr[$nr] = strtolower($inhalt['WettbewerbNr']);
	$TWettbewerbBez[$nr] = strtolower($inhalt['WettbewerbBez']);
	$TTeilnStaffeln[$nr] = strtolower($inhalt['TeilnStaffeln']);
	$TStellplatzMin[$nr] = strtolower($inhalt['StellplatzMin']);
	$TStellplatzZeit[$nr] = strtolower($inhalt['StellplatzZeit']);
	$TStellplatzTag[$nr] = strtolower($inhalt['StellplatzTag']);
	$TCOSANr[$nr] = strtolower($inhalt['COSANr']);
	$TCOSANrAK[$nr] = strtolower($inhalt['COSANrAK']);
	$TCOSANrDIS[$nr] = strtolower($inhalt['COSANrDIS']);
	$TDISBez[$nr] = strtolower($inhalt['DISBez']);
	$TAKBez[$nr] = strtolower($inhalt['AKBez']);
	$TZeitSortierung[$nr] = strtolower($inhalt['ZeitSortierung']);

	
}

array_multisort($TZeitSortierung, SORT_ASC, $Stellplatz);
?>

<table CLASS="laivemenu">
	<tr>
		<td class="linkliste"></td>
		<td class="aktualisiert" ><?php echo "<a class='aktualisiert'>$txt_kopf_zeitplanaktualisiert ".date("d.m.y H:i", filemtime($dat_wettbew))."</a>"; ?></td>
	</tr>
</table>
<table class="body" cellspacing="0">
			<tr><td class="KopfZ1"><?php echo $Kopfzeile1; ?></td></tr>
			<tr><td class="KopfZ11"><?php echo $Kopfzeile2; ?></td></tr>
			<tr><td class="KopfZ12"><?php echo $Kopfzeile3; ?></td></tr>
</table>
<table class="body" cellspacing="0">
		<tr><td class="KopfZ21"><?php echo $txt_stellplatzzeitplan; ?></td></tr>
</table>
<br>
<p class="txtstellplatz"><a class="txtstellplatz"><?php echo $txt_stellplatzabgabe; ?></a></p>
<table class= "stellplatz">
	<thead>				
					<tr>			
						<th class="stellplatz"><? echo $txt_zeit; ?></th>
						<th class="stellplatz"><? echo $txt_klasse; ?></th>
						<th class="stellplatz"><? echo $txt_disziplin; ?></th>
						<th class="stellplatz"><?php if(file_exists($dat_wbteiln)){ echo "$txt_meldungen";} ?></th>
						<th class="stellplatz"><? echo $txt_wbnr; ?></th>
					</tr>
			</thead>
<?php
			$vorherigerTag = 0;
			$vorherigeUhrzeit = "";
			
			# Ausgabe jeder einzelnen Wettbewerbszeile
			$ZaehlerRunden = 0;
			foreach ($Stellplatz as $zeile) {
			
			if($zeile['StellplatzMin'] != 1) {
			
			if($zeile['StellplatzTag'] != $vorherigerTag) {
			
				$vorherigerTag = $zeile['StellplatzTag'];
				
				if(count($tage) > 1) {
					$TmpTagStellpl = $tage[$zeile['StellplatzTag']] . " ($txt_tag ".$zeile['StellplatzTag'] . ")";
				}
				else {
					$TmpTagStellpl = $tage[$zeile['StellplatzTag']];
				}
				
				echo "<tr><td colspan='5' class ='stellplatztag'><a class='stellplatztag'>" . $Wochentage[date("w", $TageUnix[$zeile['StellplatzTag']])] . ", " . $TmpTagStellpl . "</a></td></tr>";
			
			
			}
			
					$classtd = "stellplatz";

				$ZaehlerRunden = $ZaehlerRunden + 1;
			
				echo "<tr class ='".$classtd."'>";
			
					
					if($zeile['StellplatzZeit'] != $vorherigeUhrzeit) {
					echo "<td class ='".$classtd."zeit'><a class='".$classtd."zeit'";
					echo ">".$zeile['StellplatzZeit']."</a></td>";
					}
					else 	{
					echo "<td class ='".$classtd."zeitvorhanden'><a class='".$classtd."zeitvorhanden'";
					echo "> </a></td>";
					}
					
					echo "<td class ='".$classtd."'><a class='".$classtd."'";
					echo ">".$zeile['AKBez']."</a></td>";
					
					echo "<td class ='".$classtd."'><a class='".$classtd."'";
					echo ">".$zeile['DISBez']."</a></td>";
					
					echo "<td class ='".$classtd."tn'><a class='".$classtd."tn'>";
					if(file_exists($dat_wbteiln)){
					echo $zeile['TeilnStaffeln'];
					if(empty($zeile['TeilnStaffeln'])) { echo "keine";}
					}
					echo"</a></td>";
					
					echo "<td class ='".$classtd."wbnr'><a class='".$classtd."wbnr'";
					echo ">-".$zeile['WettbewerbNr']."-</a></td>";
				
			
				echo "</tr>";
			
				$vorherigeUhrzeit = $zeile['StellplatzZeit'];
			
			
			}
			}
?>
		
</table>
		
<p class="AnzahlRunden"><a class="AnzahlRunden"><?php echo $ZaehlerRunden." ".$txt_anzahlwettbewerbestellplatz; ?></a></p>