@echo off
REM ===============================================================================
REM Generiere open command:
REM 1) Regulär einloggen per WinScp 
REM 2) Sitzung -> Generiere Sitzungs-URL/code -> Script
REM ------------------------------------------------------------------------------

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