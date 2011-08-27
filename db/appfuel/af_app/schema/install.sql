create database if not exists af_app default character set utf8;
use af_app;

create user 'af_admin'@'localhost' identified by 'w3bg33k';
create user 'af_web_user'@'localhost' identified by 'cde2Ws4Dc';

grant all privileges on af_app.* 
to 'af_admin'@'localhost' identified by 'w3bg33k';
