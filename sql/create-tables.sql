CREATE TABLE Taulu
(
taulun_id INT PRIMARY KEY AUTO_INCREMENT,
taulun_nimi TEXT
);

CREATE TABLE Sarake
(
sarakkeen_id INT PRIMARY KEY AUTO_INCREMENT,
taulun_id INT,
sarakkeen_nimi TEXT,
sarakkeen_tyyppi TEXT
);

CREATE TABLE Kayttaja
(
kayttaja_id INT PRIMARY KEY AUTO_INCREMENT,
kayttajanimi TEXT,
tiiviste TEXT,
suola TEXT
);

CREATE TABLE Oikeudet
(
oikeus_id INT PRIMARY KEY AUTO_INCREMENT,
kayttaja_id INT,
tyyppi INT,
kohde INT,
oikeus INT
);

CREATE TABLE Asetelma
(
asetelman_id INT PRIMARY KEY AUTO_INCREMENT,
asetelman_nimi TEXT,
xml_arkisto TEXT,
xml_yllapito TEXT,
sqllauseke TEXT
);