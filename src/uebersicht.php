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
### LaIVE - Modul Übersicht (uebersicht.php) / LaIVE - Module Overview (uebersicht.php)
### Erstellt von / Created by Kilian Wenzel
### Zuletzt geändert: / Last change: 0.9.3.2013-06-19


if($IPCModeON == 1) {
	$Events_SummaryIPC 		= EventsArray();
	$IPCClass_SummaryIPC 	= IPCClassesArray();
	$IPCLists1				= array();
	$IPCLists2				= array();
	
	
}

## Datein auslesen

// Ordnername
$ordner = "."; //auch komplette Pfade möglich ($ordner = "download/files";)
 
// Ordner auslesen und Array in Variable speichern
$alledateien = scandir($ordner); // Sortierung A-Z
// Sortierung Z-A mit scandir($ordner, 1)                               
 
// Schleife um Array "$alledateien" aus scandir Funktion auszugeben
// Einzeldateien werden dabei in der Variabel $datei abgelegt
foreach ($alledateien as $datei) {
 
    // Zusammentragen der Dateiinfo
    $dateiinfo = pathinfo($ordner."/".$datei);
    //Folgende Variablen stehen nach pathinfo zur Verfügung
    // $dateiinfo['filename'] =Dateiname ohne Dateiendung  *erst mit PHP 5.2
    // $dateiinfo['dirname'] = Verzeichnisname
    // $dateiinfo['extension'] = Dateityp -/endung
    // $dateiinfo['basename'] = voller Dateiname mit Dateiendung
	
	$dateiname = $datei;
	$erstesZeichen = substr($dateiname, 0, 1);
	$letztesZeichen = substr($dateiinfo['filename'], -1);
	if(is_numeric($erstesZeichen)) {$erstesZeichen ="e";}
	
	if($dateiname != "index.php" && $dateiname != "sorttable.js" && $dateiname != "stellplatzzeitplan.php" && $dateiname != "startlistenerstellen.php" &&$dateiname != "uebersicht.php" && $dateiname != "zeitplan.php") {
	
	if($IPCModeON != 1) {

		switch ($erstesZeichen) {
		
		case "e":
				# Ergebnisse
				
				if(ctype_alpha($letztesZeichen) == false ) {
				

					# Typ
					$TypNr = 1;
					$TypBez = $TypTyp1;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));
			}
			else {
			
					
					$dateiergebnisse_a		= substr($dateiinfo['filename'], 0, 5)."a.htm";
					$dateiergebnisse_b		= substr($dateiinfo['filename'], 0, 5)."b.htm";
					$dateiergebnisse_c		= substr($dateiinfo['filename'], 0, 5)."c.htm";
					$dateiergebnisse_d		= substr($dateiinfo['filename'], 0, 5)."d.htm";
					$dateiergebnisse_e		= substr($dateiinfo['filename'], 0, 5)."e.htm";
					$dateiergebnisse_f		= substr($dateiinfo['filename'], 0, 5)."f.htm";
					$dateiergebnisse_g		= substr($dateiinfo['filename'], 0, 5)."g.htm";
					$dateiergebnisse_h		= substr($dateiinfo['filename'], 0, 5)."h.htm";
					$dateiergebnisse_i		= substr($dateiinfo['filename'], 0, 5)."i.htm";
					$dateiergebnisse_finale	= substr($dateiinfo['filename'], 0, 5).".htm";

				switch($letztesZeichen) {
				
					case "a":
						
						if(file_exists($dateiergebnisse_b) || file_exists($dateiergebnisse_c) || file_exists($dateiergebnisse_d) || file_exists($dateiergebnisse_e) || file_exists($dateiergebnisse_f) || file_exists($dateiergebnisse_g) || file_exists($dateiergebnisse_h) || file_exists($dateiergebnisse_i) || file_exists($dateiergebnisse_finale)) {
							# nichts
						}
						else {
						
											# Typ
					$TypNr = 3;
					$TypBez = $TypTyp3;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));	
						
						}
					
					break;
				
				
					case "b":
						
						if(file_exists($dateiergebnisse_c) || file_exists($dateiergebnisse_d) || file_exists($dateiergebnisse_e) || file_exists($dateiergebnisse_f) || file_exists($dateiergebnisse_g) || file_exists($dateiergebnisse_h) || file_exists($dateiergebnisse_i) || file_exists($dateiergebnisse_finale)) {
							# nichts
						}
						else {
						
											# Typ
					$TypNr = 3;
					$TypBez = $TypTyp3;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));	
						
						}
					
					break;				
				
				
					case "c":
						
						if(file_exists($dateiergebnisse_d) || file_exists($dateiergebnisse_e) || file_exists($dateiergebnisse_f) || file_exists($dateiergebnisse_g) || file_exists($dateiergebnisse_h) || file_exists($dateiergebnisse_i) || file_exists($dateiergebnisse_finale)) {
							# nichts
						}
						else {
						
											# Typ
					$TypNr = 3;
					$TypBez = $TypTyp3;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));	
						
						}
					
					break;

					case "d":
						
						if(file_exists($dateiergebnisse_e) || file_exists($dateiergebnisse_f) || file_exists($dateiergebnisse_g) || file_exists($dateiergebnisse_h) || file_exists($dateiergebnisse_i) || file_exists($dateiergebnisse_finale)) {
							# nichts
						}
						else {
						
											# Typ
					$TypNr = 3;
					$TypBez = $TypTyp3;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));	
						
						}
					
					break;					

					case "e":
						
						if(file_exists($dateiergebnisse_f) || file_exists($dateiergebnisse_g) || file_exists($dateiergebnisse_h) || file_exists($dateiergebnisse_i) || file_exists($dateiergebnisse_finale)) {
							# nichts
						}
						else {
						
											# Typ
					$TypNr = 3;
					$TypBez = $TypTyp3;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));	
						
						}
					
					break;						
				
					case "f":
						
						if(file_exists($dateiergebnisse_g) || file_exists($dateiergebnisse_h) || file_exists($dateiergebnisse_i) || file_exists($dateiergebnisse_finale)) {
							# nichts
						}
						else {
						
											# Typ
					$TypNr = 3;
					$TypBez = $TypTyp3;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));	
						
						}
					
					break;				

					case "g":
						
						if(file_exists($dateiergebnisse_h) || file_exists($dateiergebnisse_i) || file_exists($dateiergebnisse_finale)) {
							# nichts
						}
						else {
						
											# Typ
					$TypNr = 3;
					$TypBez = $TypTyp3;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));	
						
						}
					
					break;								

					case "h":
						
						if(file_exists($dateiergebnisse_i) || file_exists($dateiergebnisse_finale)) {
							# nichts
						}
						else {
						
											# Typ
					$TypNr = 3;
					$TypBez = $TypTyp3;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));	
						
						}
					
					break;													
					
					case "i":
						
						if(file_exists($dateiergebnisse_finale)) {
							# nichts
						}
						else {
						
											# Typ
					$TypNr = 3;
					$TypBez = $TypTyp3;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));	
						
						}
					
					break;						

					case "z":
						
						if(file_exists($dateiergebnisse_finale)) {
							# nichts
						}
						else {
						
											# Typ
					$TypNr = 3;
					$TypBez = $TypTyp3;
					
					# Klasse
					$KlasseNr = substr($dateiinfo['filename'], 0, 2);
					$KlasseBez = $Klassen[$KlasseNr]['Bez'];
					
					# Disziplin
					$DisziplinNrTmp = substr($dateiinfo['filename'], 2, 3);
					
						if($DisziplinNrTmp == "eig") {
							# Eigener Wettbewerb
							$DisziplinNr = 0; # Nr. eigener Wettbewerb
						}
						else {
							# Standardwettbewerb
							$DisziplinNr = (int)$DisziplinNrTmp;
						}
					
					$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
					# Runde
					
						# Prüfen auf Länge des Dateinamens ---
						# 5 = Finale
						# 6 = Vorlauf, Zwischenlauf, Zeitvorlauf
				
						$TmpStrLen = strlen($dateiinfo['filename']);
			
							if ($TmpStrLen == 5) {
			
								# Wenn 5 Zeichen --> Finale
				
									#Rundentyp
									$RundeNr = 0;
									$RundeBez = $RundeTyp0;
				
							}
							else {
			
								# Wenn 6 Zeichen --> Vorlauf, Zwischenlauf, Zeitvorlauf
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
											case 1:
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case 2:
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case 3:
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											
											case "a":
												$RundeNr = "a";
												$RundeBez = $RundeTypa;
												break;
											case "b":
												$RundeNr = "b";
												$RundeBez = $RundeTypb;
												break;
											case "c":
												$RundeNr = "c";
												$RundeBez = $RundeTypc;
												break;
											case "d":
												$RundeNr = "d";
												$RundeBez = $RundeTypd;
												break;
											case "e":
												$RundeNr = "e";
												$RundeBez = $RundeType;
												break;
											case "f":
												$RundeNr = "f";
												$RundeBez = $RundeTypf;
												break;
											case "g":
												$RundeNr = "g";
												$RundeBez = $RundeTypg;
												break;
											case "h":
												$RundeNr = "h";
												$RundeBez = $RundeTyph;
												break;	
											case "i":
												$RundeNr = "i";
												$RundeBez = $RundeTypi;
												break;	
												
												
										}	
			
							}
				# Ausgabe in Array
				$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));	
						
						}
					
					break;						
				}
			
			}
				

		break;
		
		
		
		
				
		
		
		
			case "t":
			# Teilnehmer
			
			# Prüfen, ob schon Ergebnisse vorhanden sind
				$DateinameErgebnisse = substr($dateiinfo['filename'], 1);
				$DateiErg = $DateinameErgebnisse . ".htm";
				$DateiErg1 = $DateinameErgebnisse . "1.htm";
				$DateiErg2 = $DateinameErgebnisse . "2.htm";
				$DateiErg3 = $DateinameErgebnisse . "3.htm";
				$DateiErga = $DateinameErgebnisse . "a.htm";
				$DateiErgb = $DateinameErgebnisse . "b.htm";
				$DateiErgc = $DateinameErgebnisse . "c.htm";
				$DateiErgd = $DateinameErgebnisse . "d.htm";
				$DateiErge = $DateinameErgebnisse . "e.htm";
				$DateiErgf = $DateinameErgebnisse . "f.htm";
				$DateiErgg = $DateinameErgebnisse . "g.htm";
				$DateiErgh = $DateinameErgebnisse . "h.htm";
				$DateiErgi = $DateinameErgebnisse . "i.htm";
				
				# Prüfen, ob Startliste vorhanden ist
				$DateiStartlistea = "s".$DateinameErgebnisse . "a.htm";
				$DateiStartlisteb = "s".$DateinameErgebnisse . "b.htm";
				$DateiStartlistec = "s".$DateinameErgebnisse . "c.htm";
				$DateiStartlisted = "s".$DateinameErgebnisse . "d.htm";
				$DateiStartlistee = "s".$DateinameErgebnisse . "e.htm";
				$DateiStartlistek = "s".$DateinameErgebnisse . "k.htm";
				$DateiStartlistel = "s".$DateinameErgebnisse . "l.htm";
				$DateiStartlistem = "s".$DateinameErgebnisse . "m.htm";
				$DateiStartlisten = "s".$DateinameErgebnisse . "n.htm";
				$DateiStartlisteq = "s".$DateinameErgebnisse . "q.htm";
				$DateiStartlister = "s".$DateinameErgebnisse . "r.htm";
				$DateiStartlistes = "s".$DateinameErgebnisse . "s.htm";

	
				if(file_exists($DateiStartlistea) || file_exists($DateiStartlisteb) || file_exists($DateiStartlistec) || file_exists($DateiStartlisted) || file_exists($DateiStartlistee) || file_exists($DateiStartlistek) || file_exists($DateiStartlistel) || file_exists($DateiStartlistem) || file_exists($DateiStartlisten) || file_exists($DateiStartlisteq) || file_exists($DateiStartlister) || file_exists($DateiStartlistes) || file_exists($DateiErg) || file_exists($DateiErg1) || file_exists($DateiErg2) || file_exists($DateiErg3) || file_exists($DateiErga) || file_exists($DateiErgb) || file_exists($DateiErgc) || file_exists($DateiErgd) || file_exists($DateiErge) || file_exists($DateiErgf) || file_exists($DateiErgg) || file_exists($DateiErgh) || file_exists($DateiErgi)) {
					# nichts
				}
				else {
			
					# Ausgeben, da keine Ergebnisse vorhanden
			
						$TypNr = 2; #Typ
						$TypBez = $TypTyp2;
			
						#Klasse
						$KlasseNr = substr($dateiinfo['filename'], 1, 2);
						$KlasseBez = $Klassen[$KlasseNr]['Bez'];
				
						#Disziplin
						$DisziplinNrTmp = substr($dateiinfo['filename'], 3, 3);
							if($DisziplinNrTmp == "eig") {
								$DisziplinNr = 0; # Nr. eigener Wettbewerb
							}
							else {
				
								$DisziplinNr = (int)$DisziplinNrTmp;
							}
						$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
						# Runde
						$RundeNr = 99;
						$RundeBez = $RundeTyp99;
			
			
			$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));
			
			
			}
			
			break;
			
			case "s": # Startliste vorhanden
			
			# Prüfen, ob "-" vorhanden ist, um Neues Format auszuschließen
			if(strpos($dateiinfo['filename'],"-")!==false) {
			
				# dann passiert derzeit noch nichts
				
			}
			else {
			
			# Prüfen, ob schon Ergebnisse vorhanden sind    
			
				if(strlen(substr($dateiinfo['filename'], 1)) == 6) {
					if(is_numeric(substr($dateiinfo['filename'], 1, 6))) {
						$DateinameErgebnisse = substr($dateiinfo['filename'], 1, 6);
					}
					else {
						$DateinameErgebnisse = substr($dateiinfo['filename'], 1, 5);
					}
				}
				else {
					$DateinameErgebnisse = substr($dateiinfo['filename'], 1, 5);
				}
				
			
				
				
				$DateiErg = $DateinameErgebnisse . ".htm";
				$DateiErg1 = $DateinameErgebnisse . "1.htm";
				$DateiErg2 = $DateinameErgebnisse . "2.htm";
				$DateiErg3 = $DateinameErgebnisse . "3.htm";
				$DateiErga = $DateinameErgebnisse . "a.htm";
				$DateiErgb = $DateinameErgebnisse . "b.htm";
				$DateiErgc = $DateinameErgebnisse . "c.htm";
				$DateiErgd = $DateinameErgebnisse . "d.htm";
				$DateiErge = $DateinameErgebnisse . "e.htm";
				$DateiErgf = $DateinameErgebnisse . "f.htm";
				$DateiErgg = $DateinameErgebnisse . "g.htm";
				$DateiErgh = $DateinameErgebnisse . "h.htm";
				$DateiErgi = $DateinameErgebnisse . "i.htm";
				
				# Prüfen, ob Startliste vorhanden ist
				$DateiStartlistea = "s".$DateinameErgebnisse . "a.htm";
				$DateiStartlisteb = "s".$DateinameErgebnisse . "b.htm";
				$DateiStartlistec = "s".$DateinameErgebnisse . "c.htm";
				$DateiStartlisted = "s".$DateinameErgebnisse . "d.htm";
				$DateiStartlistee = "s".$DateinameErgebnisse . "e.htm";
				$DateiStartlistek = "s".$DateinameErgebnisse . "k.htm";
				$DateiStartlistel = "s".$DateinameErgebnisse . "l.htm";
				$DateiStartlistem = "s".$DateinameErgebnisse . "m.htm";
				$DateiStartlisten = "s".$DateinameErgebnisse . "n.htm";
				$DateiStartlisteq = "s".$DateinameErgebnisse . "q.htm";
				$DateiStartlister = "s".$DateinameErgebnisse . "r.htm";
				$DateiStartlistes = "s".$DateinameErgebnisse . "s.htm";
			

	
				if(file_exists($DateiErg) || file_exists($DateiErg1) || file_exists($DateiErg2) || file_exists($DateiErg3) || file_exists($DateiErga) || file_exists($DateiErgb) || file_exists($DateiErgc) || file_exists($DateiErgd) || file_exists($DateiErge) || file_exists($DateiErgf) || file_exists($DateiErgg) || file_exists($DateiErgh) || file_exists($DateiErgi)) {
					# nichts
				}
				else {
				
				
				
				
			
					# Ausgeben, da keine Ergebnisse vorhanden
			
						$TypNr = 4; #Typ
						$TypBez = $TypTyp4;
			
						#Klasse
						$KlasseNr = substr($dateiinfo['filename'], 1, 2);
						$KlasseBez = $Klassen[$KlasseNr]['Bez'];
				
						#Disziplin
						$DisziplinNrTmp = substr($dateiinfo['filename'], 3, 3);
							if($DisziplinNrTmp == "eig") {
								$DisziplinNr = 0; # Nr. eigener Wettbewerb
							}
							else {
				
								$DisziplinNr = (int)$DisziplinNrTmp;
							}
						$DisziplinBez = $Disziplinen[$DisziplinNr]['Bez'];
				
				
						# Runde
					
						
				
				
									# Rundentyp
									$TmpSubStrRunde = substr($dateiinfo['filename'], -1, 1);
				
										switch($TmpSubStrRunde) {
				
										
											
											case "a":
												$RundeNr = 1;
												$RundeBez = $RundeTyp1;
												break;
											case "b":
												$RundeNr = 2;
												$RundeBez = $RundeTyp2;
												break;
											case "c":
												$RundeNr = 4;
												$RundeBez = $RundeTyp4;
												break;
											case "d":
												$RundeNr = 6;
												$RundeBez = $RundeTyp6;
												break;
											case "e":
												$RundeNr = 3;
												$RundeBez = $RundeTyp3;
												break;
											case "k":
											case "n":
											case "q":
												$RundeNr = 0;
												$RundeBez = $RundeTyp0;
												break;
											case "r":
												$RundeNr = 4;
												$RundeBez = $RundeTyp4;
												break;
											case "s":
												$RundeNr = 5;
												$RundeBez = $RundeTyp5;
												break;	
											case "m":
												$RundeNr = 8;
												$RundeBez = $RundeTyp8;
												break;	
												
												
										}	
			
							
			
			
			$Ausgabe[] = array(	'KlasseNr'		=>	$KlasseNr,
							'KlasseBez'			=>	$KlasseBez,
							'DisziplinNr'		=>	$DisziplinNr,
							'DisziplinBez'		=>	$DisziplinBez,
							'RundeNr'			=>	$RundeNr,
							'RundeBez'			=>	$RundeBez,
							'TypNr'				=>	$TypNr,
							'TypBez'			=>	$TypBez,
							'Datei'				=>  $datei,
							'Zeit'				=> 	filemtime($datei));
			
			
			}
			}
			break;
			
			
			
		}
	}
	else { # IPC Mode
		
		
		#$AllFilesFile 			= "";

							if(substr($datei, 0, 1) == "t" || substr($datei, 0, 1) == "s" || substr($datei, 0, 1) == "e") {
								switch(substr($datei, 0, 1)) {
									case "t": # Teilnehmerliste / Entry list
										$AllFilesFileListType			= substr($datei, 0, 1);	
										$AllFilesFileCOSAID 			= substr($datei, 1, 5);
										$TmpAllFilesFileEventIDArray	= array_multi_search($AllFilesFileCOSAID, $Events_SummaryIPC, "COSAID");
										$AllFilesFileEventID			= $TmpAllFilesFileEventIDArray[0]['EventID'];
										unset($TmpAllFilesFileEventIDArray);
										
										break;
								
									case "s": # Startliste / start list
										list($AllFilesFileName, $AllFilesFileExtention) = explode(".", $datei);
										list($AllFilesFileListType, $AllFilesFileEventType, $AllFilesFileEventID, $AllFilesFileCOSAID, $AllFilesFileIPCClassID, $AllFilesFileRoundType) = explode("-", $AllFilesFileName);
										break;
								
									case "e": # Ergebnisliste / result list
										list($AllFilesFileName, $AllFilesFileExtention) = explode(".", $datei);
										list($AllFilesFileListType, $AllFilesFileEventID, $AllFilesFileIPCClassID, $AllFilesFileRoundType) = explode("-", $AllFilesFileName);
										$TmpAllFilesFileCOSAIDArray	= array_multi_search($AllFilesFileEventID, $Events_SummaryIPC, "EventID");
										$AllFilesFileCOSAID			= $TmpAllFilesFileCOSAIDArray[0]['COSAID'];
										unset($TmpAllFilesFileCOSAIDArray);
										break;
									default:
										break;
								
								}
								
								
									switch($AllFilesFileListType) {
										case "t": # Teilnehmerliste / Entry list
										
											if(array_key_exists($AllFilesFileEventID."-".$AllFilesFileCOSAID , $IPCLists1) == FALSE) {
											$Ausgabe[] = array(	'KlasseNr'			=>	substr($AllFilesFileCOSAID, 0, 2),
																'KlasseBez'			=>	$Klassen[substr($AllFilesFileCOSAID, 0, 2)]['Bez'],
																'DisziplinNr'		=>	substr($AllFilesFileCOSAID, 2, 3)*1,
																'DisziplinBez'		=>	$Disziplinen[substr($AllFilesFileCOSAID, 2, 3)*1]['Bez'],
																'IPCClassName'		=>	"",
																'RundeNr'			=>	"",
																'RundeBez'			=>	"",
																'TypNr'				=>	2,
																'TypBez'			=>	$TypTyp2,
																'Datei'				=>  $datei,
																'Zeit'				=> 	filemtime($datei)
																);
											
											}
											break;
											
										case "s": # Startliste / start list
											switch($AllFilesFileRoundType) {
												case "a":
													$TmpSubMenuLinkTitle = $RundeTyp1;
													break;
												case "b":
													$TmpSubMenuLinkTitle = $RundeTyp2;
													break;
												case "c":
													$TmpSubMenuLinkTitle = $RundeTyp4;
													break;
												case "d":
													$TmpSubMenuLinkTitle = $RundeTyp6;
													break;
												case "e":
													$TmpSubMenuLinkTitle = $RundeTyp3;
													break;
												case "k":
													$TmpSubMenuLinkTitle = $RundeTyp0;
													break;
												case "l":
													$TmpSubMenuLinkTitle = $RundeTyp7;
													break;
												case "m":
													$TmpSubMenuLinkTitle = $RundeTyp8;
													break;
												case "n":
													$TmpSubMenuLinkTitle = $RundeTyp0;
													break;
												case "q":
													$TmpSubMenuLinkTitle = $RundeTyp0;
													break;
												case "r":
													$TmpSubMenuLinkTitle = $RundeTyp4;
													break;
												case "s":
													$TmpSubMenuLinkTitle = $RundeTyp5;
													break;	
											}
											if(array_key_exists($AllFilesFileEventID."-".$AllFilesFileCOSAID."-".$AllFilesFileIPCClassID."-".$AllFilesFileRoundType , $IPCLists2) == FALSE) {
											$Ausgabe[] = array(	'KlasseNr'			=>	substr($AllFilesFileCOSAID, 0, 2),
																'KlasseBez'			=>	$Klassen[substr($AllFilesFileCOSAID, 0, 2)]['Bez'],
																'DisziplinNr'		=>	substr($AllFilesFileCOSAID, 2, 3)*1,
																'DisziplinBez'		=>	$Disziplinen[substr($AllFilesFileCOSAID, 2, 3)*1]['Bez'],
																'IPCClassName'		=>	$IPCClass_SummaryIPC[$AllFilesFileIPCClassID]['IPCClassName'],
																'RundeNr'			=>	$AllFilesFileRoundType,
																'RundeBez'			=>	$TmpSubMenuLinkTitle,
																'TypNr'				=>	4,
																'TypBez'			=>	$TypTyp4,
																'Datei'				=>  $datei,
																'Zeit'				=> 	filemtime($datei)
																);
											
											$IPCLists1[$AllFilesFileEventID."-".$AllFilesFileCOSAID]	= 1;
											}
											break;
											
										case "e": # Ergebnisliste / result list
										switch($AllFilesFileRoundType) {
												case "a":
													$TmpSubMenuLinkTitle = $RundeTyp1;
													break;
												case "b":
													$TmpSubMenuLinkTitle = $RundeTyp2;
													break;
												case "c":
													$TmpSubMenuLinkTitle = $RundeTyp4;
													break;
												case "d":
													$TmpSubMenuLinkTitle = $RundeTyp6;
													break;
												case "e":
													$TmpSubMenuLinkTitle = $RundeTyp3;
													break;
												case "k":
													$TmpSubMenuLinkTitle = $RundeTyp0;
													break;
												case "l":
													$TmpSubMenuLinkTitle = $RundeTyp7;
													break;
												case "m":
													$TmpSubMenuLinkTitle = $RundeTyp8;
													break;
												case "n":
													$TmpSubMenuLinkTitle = $RundeTyp0;
													break;
												case "q":
													$TmpSubMenuLinkTitle = $RundeTyp0;
													break;
												case "r":
													$TmpSubMenuLinkTitle = $RundeTyp4;
													break;
												case "s":
													$TmpSubMenuLinkTitle = $RundeTyp5;
													break;		
											}
											$Ausgabe[] = array(	'KlasseNr'			=>	substr($AllFilesFileCOSAID, 0, 2),
																'KlasseBez'			=>	$Klassen[substr($AllFilesFileCOSAID, 0, 2)]['Bez'],
																'DisziplinNr'		=>	substr($AllFilesFileCOSAID, 2, 3)*1,
																'DisziplinBez'		=>	$Disziplinen[substr($AllFilesFileCOSAID, 2, 3)*1]['Bez'],
																'IPCClassName'		=>	$IPCClass_SummaryIPC[$AllFilesFileIPCClassID]['IPCClassName'],
																'RundeNr'			=>	$AllFilesFileRoundType,
																'RundeBez'			=>	$TmpSubMenuLinkTitle,
																'TypNr'				=>	1,
																'TypBez'			=>	$TypTyp1,
																'Datei'				=>  $datei,
																'Zeit'				=> 	filemtime($datei)
																);
											
											$IPCLists1[$AllFilesFileEventID."-".$AllFilesFileCOSAID]	= 1;
											$IPCLists2[$AllFilesFileEventID."-".$AllFilesFileCOSAID."-".$AllFilesFileIPCClassID."-".$AllFilesFileRoundType] = 1;
											
											break;
										default:
											break;
									}
								
							}
							
		
	}
	}
}

# Array (Ausgabe nach Aktualität sortieren)

foreach ($Ausgabe as $nr => $inhalt) {

	$KlasseNrUe[$nr] = strtolower($inhalt['KlasseNr']);
	$KlasseBezUe[$nr] = strtolower($inhalt['KlasseBez']);
	$DisziplinNrUe[$nr] = strtolower($inhalt['DisziplinNr']);
	$DisziplinBezUe[$nr] = strtolower($inhalt['DisziplinBez']);
	$RundeNrUe[$nr] = strtolower($inhalt['RundeNr']);
	$RundeBezUe[$nr] = strtolower($inhalt['RundeBez']);
	$TypNrUe[$nr] = strtolower($inhalt['TypNr']);
	$TypBezUe[$nr] = strtolower($inhalt['TypBez']);
	$DateiUe[$nr] = strtolower($inhalt['Datei']);
	$ZeitUe[$nr] = strtolower($inhalt['Zeit']);
	$IPCClassNameUe[$nr] = strtolower($inhalt['IPCClassName']);
}

array_multisort($ZeitUe, SORT_DESC, $Ausgabe);




# HTML-Ausgabe

?>
		<table class="body" cellspacing="0">
			<tr><td class="KopfZ1"><?php echo $Kopfzeile1; ?></td></tr>
			<tr><td class="KopfZ11"><?php echo $Kopfzeile2; ?></td></tr>
			<tr><td class="KopfZ12"><?php echo $Kopfzeile3; ?></td></tr>
		</table>
		<table class="body" cellspacing="0">
		<tr><td class="KopfZ21"><? echo $txt_uebersicht; ?></td></tr>
		</table>
		<br>
<?php
		echo" <p class='LinkStellplatz'><a class='LinkStellplatz' href='?sub=uebersicht.php&justRL=1'>$TxtLinkJustRL</a></p>";

?>
		<table class="sortable">
			<thead>
				<tr>
					<th><abbr title="<?php echo $text_hinweissortierunguebersicht; ?>"><? echo $txt_klasse; ?></abbr></th>
					<th><abbr title="<?php echo $text_hinweissortierunguebersicht; ?>"><? echo $txt_disziplin; ?></abbr></th>
<?php
					if($IPCModeON == 1) {
						echo "<th><abbr title='".$text_hinweissortierunguebersicht."'>".$txt_IPCClassesName."</abbr></th>";
					}
?>
					<th><abbr title="<?php echo $text_hinweissortierunguebersicht; ?>"><? echo $txt_runde; ?></abbr></th>
					<th><abbr title="<?php echo $text_hinweissortierunguebersicht; ?>"><? #echo $txt_typ; ?></abbr></th>
					<th><abbr title="<?php echo $text_hinweissortierunguebersicht; ?>"><? echo $txt_aktualisiert; ?></abbr></th>
				</tr>
			</thead>
<?php
			
			# Ausgabe jeder einzelnen Wettbewerbszeile
			$ZaehlerRunden = 0;
			foreach ($Ausgabe as $zeile) {
			
			if(!isset($_GET['justRL']) || isset($_GET['justRL']) && $_GET['justRL'] != 1) {
			
			if($zeile['TypNr'] == 1) {
			
				$classtd = "typ1";
			
			}
			else {
			
			
				$classtd = "typ2";
			}
				
			$ZaehlerRunden = $ZaehlerRunden + 1;
			
			echo "<tr>";
				echo "<td class ='zeitplanspalteklasse' sorttable_customkey='".$zeile['KlasseNr']."'><a class='zeitplanspalteklasse'>".$zeile['KlasseBez']."</a></td>";
				echo "<td class ='zeitplanspaltedisziplin' sorttable_customkey='".$zeile['DisziplinNr']."'><a class='zeitplanspaltedisziplin'>".$zeile['DisziplinBez']."</a></td>";
				if($IPCModeON == 1) {
					echo "<td class ='zeitplanspalterunde'><a class='zeitplanspalterunde'>".$zeile['IPCClassName']."</a></td>";
				}
				echo "<td class ='zeitplanspalterunde' sorttable_customkey='".$zeile['RundeNr']."'><a class='zeitplanspalterunde'>".$zeile['RundeBez']."</a></td>";
				if($IPCResultListFileExtention == "htm") {
					echo "<td class ='zeitplanspaltetyp' sorttable_customkey='".$zeile['TypNr']."'><table class='zeitplantyplink'><tr><td class='typ".$zeile['TypNr']."'><a class='typ".$zeile['TypNr']."' href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'>".$zeile['TypBez']."</a></td></tr></table></td>";
				}
				else {
					echo "<td class ='zeitplanspaltetyp' sorttable_customkey='".$zeile['TypNr']."'><table class='zeitplantyplink'><tr><td class='typ".$zeile['TypNr']."'><a class='typ".$zeile['TypNr']."' href='?sub=".$zeile['Datei']."' target='"."_self"."'>".$zeile['TypBez']."</a></td></tr></table></td>";
				}
				echo "<td class ='zeitplanspalteaktuell' sorttable_customkey='".$zeile['Zeit']."'><a class='zeitplanspalteaktuell'>".date("H:i d.m.y", $zeile['Zeit'])."</a></td>";
			echo "</tr>";
			}
			else {
			
			if($zeile['TypNr'] == 1) {
			if($zeile['TypNr'] == 1) {
			
				$classtd = "typ1";
			
			}
			else {
			
			
				$classtd = "typ2";
			}
				
			$ZaehlerRunden = $ZaehlerRunden + 1;
			
			echo "<tr>";
				echo "<td class ='zeitplanspalteklasse' sorttable_customkey='".$zeile['KlasseNr']."'><a class='zeitplanspalteklasse'>".$zeile['KlasseBez']."</a></td>";
				echo "<td class ='zeitplanspaltedisziplin' sorttable_customkey='".$zeile['DisziplinNr']."'><a class='zeitplanspaltedisziplin'>".$zeile['DisziplinBez']."</a></td>";
				if($IPCModeON == 1) {
					echo "<td class ='zeitplanspalterunde'><a class='zeitplanspalterunde'>".$zeile['IPCClassName']."</a></td>";
				}
				echo "<td class ='zeitplanspalterunde' sorttable_customkey='".$zeile['RundeNr']."'><a class='zeitplanspalterunde'>".$zeile['RundeBez']."</a></td>";
				if($IPCResultListFileExtention == "htm") {
					echo "<td class ='zeitplanspaltetyp' sorttable_customkey='".$zeile['TypNr']."'><table class='zeitplantyplink'><tr><td class='typ".$zeile['TypNr']."'><a class='typ".$zeile['TypNr']."' href='?sub=".$zeile['Datei']."' target='".$LinksTargets[$zeile['TypNr']]."'>".$zeile['TypBez']."</a></td></tr></table></td>";
				}
				else {
					echo "<td class ='zeitplanspaltetyp' sorttable_customkey='".$zeile['TypNr']."'><table class='zeitplantyplink'><tr><td class='typ".$zeile['TypNr']."'><a class='typ".$zeile['TypNr']."' href='?sub=".$zeile['Datei']."' target='"."_self"."'>".$zeile['TypBez']."</a></td></tr></table></td>";
				}
				echo "<td class ='zeitplanspalteaktuell' sorttable_customkey='".$zeile['Zeit']."'><a class='zeitplanspalteaktuell'>".date("H:i d.m.y", $zeile['Zeit'])."</a></td>";
			echo "</tr>";
			}
			}
			}
?>
		</table>
		<p class="AnzahlRunden"><a class="AnzahlRunden"><?php if($ZaehlerRunden > 0) {echo $ZaehlerRunden." ".$txt_anzahlrunden;} else {echo $txt_anzahlrunden_keine;} ?></a></p>