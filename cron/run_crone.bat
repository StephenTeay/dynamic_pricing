@echo off
cd C:\xampp\htdocs\dynamic\dynamic_pricing
C:\xampp\php\php.exe cron\update_prices.php >> logs\cron.log 2>&1