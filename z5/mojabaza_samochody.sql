-- Host: localhost    Database: mojabaza
-- ------------------------------------------------------
-- Server version	9.2.0

--
-- Creating new database
--

CREATE DATABASE IF NOT EXISTS mojaBaza;

USE mojaBaza;

--
-- Table structure for table `samochody`
--

DROP TABLE IF EXISTS `samochody`;

CREATE TABLE `samochody` (
  `id` int NOT NULL AUTO_INCREMENT,
  `marka` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `cena` decimal(10,2) NOT NULL,
  `rok` int NOT NULL,
  `opis` text,
  PRIMARY KEY (`id`)
);

--
-- Dumping data for table `samochody`
--

INSERT INTO `samochody` VALUES 
(1,'Toyota','Corolla',45900.00,2015,'Ekonomiczne auto miejskie w dobrym stanie.'),
(2,'Volkswagen','Golf',59900.00,2017,'Auto z niskim przebiegiem, zadbane wnętrze.'),
(3,'BMW','3 Series',105000.00,2019,'Samochód klasy premium z bogatym wyposażeniem.'),
(4,'Audi','A4',99000.00,2018,'Komfortowy sedan, po pierwszym właścicielu.'),
(5,'Ford','Focus',38900.00,2014,'Auto po serwisie, gotowe do jazdy.'),
(6,'Skoda','Octavia',74900.00,2020,'Rodzinny samochód z dużym bagażnikiem.'),
(7,'Honda','Civic',52900.00,2016,'Dynamiczny silnik, niskie spalanie.'),
(8,'Renault','Clio',34900.00,2013,'Małe auto idealne do miasta.'),
(9,'Mercedes-Benz','C-Class',119000.00,2021,'Luksusowe auto z nowoczesnymi systemami bezpieczeństwa.'),
(10,'Kia','Ceed',45900.00,2015,'Bezawaryjny samochód z kompletem dokumentów.');
