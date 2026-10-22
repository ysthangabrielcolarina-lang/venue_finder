@echo off
setlocal

REM Restores a backup file into a NEW database called venue_finder_restore.
REM Usage: scripts\restore.bat backups\venue_finder_2026-10-21_2356.dump

set BACKUP_FILE=%~1
set DB_USER=postgres
set DB_HOST=127.0.0.1
set DB_PORT=5432
set RESTORE_DB=venue_finder_restore

if "%BACKUP_FILE%"=="" (
    echo Usage: scripts\restore.bat path\to\backup.dump
    exit /b 1
)

set /p PGPASSWORD=Enter the password for %DB_USER%: 

REM Start from an empty database so the restore is a clean proof.
dropdb -h %DB_HOST% -p %DB_PORT% -U %DB_USER% --if-exists %RESTORE_DB%
createdb -h %DB_HOST% -p %DB_PORT% -U %DB_USER% %RESTORE_DB%

pg_restore -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -d %RESTORE_DB% "%BACKUP_FILE%"

if errorlevel 1 (
    echo Restore finished with errors. Read the messages above.
    exit /b 1
)

echo Restore finished. Database %RESTORE_DB% was rebuilt from %BACKUP_FILE%
endlocal