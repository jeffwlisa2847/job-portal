@echo off
:: ============================================================
::  SETUP-NEW-XAMPP.bat
::  Run as Administrator to fix XAMPP for the Job Portal
::  Right-click this file -> "Run as administrator"
:: ============================================================

echo.
echo  ==========================================
echo   Job Portal - XAMPP Setup Helper
echo   Run as Administrator!
echo  ==========================================
echo.

echo [Step 1] Killing anything using port 80...
net stop "World Wide Web Publishing Service" 2>nul
net stop W3SVC 2>nul
net stop http /y 2>nul
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":80 "') do (
    taskkill /F /PID %%a 2>nul
)

echo [Step 2] Stopping conflicting MySQL services...
net stop MySQL 2>nul
net stop MySQL80 2>nul

echo [Step 3] Checking what's on port 80 now...
netstat -ano | findstr ":80 "

echo.
echo  ==========================================
echo   Done! Now:
echo   1. Open XAMPP as Administrator
echo   2. Start Apache and MySQL
echo   3. Visit: http://localhost/job-portal/public/test.php
echo  ==========================================
echo.
pause
