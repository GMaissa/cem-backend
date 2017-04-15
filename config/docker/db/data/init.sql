CREATE DATABASE vm_dashboard charset utf8;
CREATE DATABASE vm_dashboard_test charset utf8;
GRANT ALL ON vm_dashboard.* to 'symfony'@'%' identified by 'symfony';
GRANT ALL ON vm_dashboard_test.* to 'symfony'@'%' identified by 'symfony';
