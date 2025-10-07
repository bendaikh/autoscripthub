@echo off
REM AutoScriptHub Asset Deployment Script for Windows
REM This script copies theme assets to the public directory for production deployment

echo Starting asset deployment...

REM Copy theme assets
echo Copying theme assets...
xcopy /E /I /Y resources\views\theme public\theme

REM Copy admin template assets
echo Copying admin template assets...
xcopy /E /I /Y resources\views\admin\template public\admin

REM Copy vendor assets (TinyMCE, etc.)
echo Copying vendor assets...
xcopy /E /I /Y vendor public\vendor

REM Copy additional assets
echo Copying additional assets...
copy /Y resources\views\assets\style.css public\assets\

REM Run npm production build
echo Running npm production build...
npm run production

echo Asset deployment completed successfully!
echo All assets are now available in the public directory.
pause
