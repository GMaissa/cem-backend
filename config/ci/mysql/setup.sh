#!/bin/bash

mysql -u root -e  "CREATE DATABASE vm_dashboard_test charset utf8;"
mysql -u root -e  "GRANT ALL ON vm_dashboard_test.* to 'symfony'@'localhost' identified by 'symfony';"
