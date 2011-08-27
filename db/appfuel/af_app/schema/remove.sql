drop database if exists af_app;

-- this will create a user with a harmless permission if the user does not
-- exist, allowing then to delete the user with an error when the user does
-- not exist.
grant usage on *.* to 'af_admin'@'localhost';
drop user 'af_admin'@'localhost';

grant usage on *.* to 'af_web_user'@'localhost';
drop user 'af_web_user'@'localhost';

