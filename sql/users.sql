DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  age smallint(3) NOT NULL,
  email varchar(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE = MyISAM AUTO_INCREMENT = 11 DEFAULT CHARSET = utf8;
