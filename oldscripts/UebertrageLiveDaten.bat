@echo off
REM ===============================================================================
REM Generiere open command:
REM 1) Regulär einloggen per WinScp 
REM 2) Sitzung -> Generiere Sitzungs-URL/code -> Script
REM ------------------------------------------------------------------------------
REM Hinweis: timeout Befehl geht ab Windows 7
REM ===============================================================================

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
