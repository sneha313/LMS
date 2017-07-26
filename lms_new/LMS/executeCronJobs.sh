#!/bin/bash
echo -e "\nSTART DATE - `date \"+DATE: %m/%d/%y TIME: %H:%M:%S\"` \n"
echo -e "\nFilling perdaytransaction table -- START\n"
/usr/bin/php /var/www/lms/FillPerDayTransactions.php
echo -e "\nFilling perdaytransaction table -- END\n"

echo -e "\nFilling inout table -- START\n"
/usr/bin/php /var/www/lms/trackWeeklyAttendance.php --fromDate=2017-01-01
echo -e "\nFilling inout table -- END\n"
echo -e "\nEND DATE - `date \"+DATE: %m/%d/%y TIME: %H:%M:%S\"` \n"
