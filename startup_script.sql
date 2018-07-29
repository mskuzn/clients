CREATE USER 'web_rw'@'localhost' IDENTIFIED BY '123qweasd';
CREATE DATABASE clients;

CREATE TABLE clients.clients (id BIGINT NOT NULL AUTO_INCREMENT
					  ,name VARCHAR(40)
					  ,familyname VARCHAR(40)
					  ,fothername VARCHAR(40)
					  ,birthday DATE
					  ,male VARCHAR(40)
					  ,created DATE
					  ,modified DATE
					  ,PRIMARY KEY (id));
									
CREATE TABLE clients.phones (id BIGINT NOT NULL AUTO_INCREMENT
					  ,phone_number BIGINT NOT NULL
					  ,client_id BIGINT NOT NULL
					  ,PRIMARY KEY (id));
									
ALTER TABLE clients.clients AUTO_INCREMENT=1433238;

GRANT ALL PRIVILEGES ON clients . * TO 'web_rw'@'localhost';
FLUSH PRIVILEGES;


ALTER TABLE clients.clients CHARACTER SET = utf8;							
ALTER DATABASE clients DEFAULT CHARACTER SET UTF8 COLLATE utf8_unicode_ci;
ALTER TABLE clients.clients CONVERT TO CHARACTER SET UTF8 COLLATE utf8_unicode_ci;
ALTER TABLE clients.clients DEFAULT CHARACTER SET UTF8 COLLATE utf8_unicode_ci;
									
									



									
select id,familyname,name,fothername,birthday,case male when 'm' then 'мужской' when 'w' then 'женский' end  as male,created,modified,phones from clients cli
	left join (select client_id, GROUP_CONCAT(phone_number SEPARATOR '; ') as phones from phones group by client_id) pho 
			on pho.client_id=cli.id;
	
