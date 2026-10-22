@echo off
setlocal

REM Backs up the venue_finder database into the backups folder.
set DB_NAME=venue_finder
set DB_USER=postgres
set DB_HOST=127.0.0.1
set DB_PORT=5432

set BACKUP_DIR=%~dp0..\backups
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

REM File name with the date and time, for example venue_finder_2026-10-21_2355.dump
for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyy-MM-dd_HHmm"') do set STAMP=%%i
set FILE=%BACKUP_DIR%\%DB_NAME%_%STAMP%.dump

REM The password is typed in, so it is never saved in this file.
set /p PGPASSWORD=Enter the password for %DB_USER%: 

pg_dump -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -Fc -f "%FILE%" %DB_NAME%

if errorlevel 1 (
    echo Backup FAILED.
    exit /b 1
)

echo Backup saved to %FILE%
dir "%FILE%"
endlocal