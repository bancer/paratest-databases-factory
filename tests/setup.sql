CREATE USER 'pf_user'@'%' IDENTIFIED BY 'password';
GRANT CREATE ON *.* TO 'pf_user'@'%';

CREATE DATABASE pf_test;
GRANT SELECT ON `pf_test`.* TO 'pf_user'@'%';

-- for unit tests:
GRANT DROP ON *.* TO 'pf_user'@'%';