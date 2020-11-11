SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `azubirotation` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `azubirotation`;

CREATE TABLE `abteilungen` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Bezeichnung` varchar(50) NOT NULL,
  `MaxAzubis` int(11) NOT NULL,
  `Farbe` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `accounts` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Username` varchar(30) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `accounts` (`ID`, `Username`, `Password`) VALUES
(1, 'sa', '$2y$10$Z8T5tsr6NRWzPGda6krGqeQk/HRKa8MUvdycYFrkm09GlL4s2JWXW');

CREATE TABLE `ansprechpartner` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Name` varchar(30) NOT NULL,
  `Email` varchar(320) NOT NULL,
  `ID_Abteilung` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ausbildungsberufe` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Bezeichnung` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `auszubildende` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Vorname` varchar(30) NOT NULL,
  `Nachname` varchar(30) NOT NULL,
  `Email` varchar(320) NOT NULL,
  `ID_Ausbildungsberuf` bigint(20) UNSIGNED DEFAULT NULL,
  `Ausbildungsstart` date NOT NULL,
  `Ausbildungsende` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `errors` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `ErrorCode` tinyint(4) NOT NULL,
  `JSON` longtext NOT NULL,
  `Accepted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pläne` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `ID_Auszubildender` bigint(20) UNSIGNED DEFAULT NULL,
  `ID_Ansprechpartner` bigint(20) UNSIGNED DEFAULT NULL,
  `ID_Abteilung` bigint(20) UNSIGNED DEFAULT NULL,
  `Startdatum` date NOT NULL,
  `Enddatum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `settings` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`ID`, `name`, `value`) VALUES
(1, 'allow-registration', 'true');

CREATE TABLE `standardpläne` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `ID_Ausbildungsberuf` bigint(20) UNSIGNED DEFAULT NULL,
  `ID_Abteilung` bigint(20) UNSIGNED DEFAULT NULL,
  `AnzahlWochen` int(11) NOT NULL,
  `Praeferieren` tinyint(1) NOT NULL,
  `Optional` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `termine` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Bezeichnung` varchar(30) NOT NULL,
  `Separat` tinyint(1) NOT NULL,
  `ID_Plan` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `abteilungen`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`);

ALTER TABLE `accounts`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD UNIQUE KEY `Username` (`Username`);

ALTER TABLE `ansprechpartner`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD KEY `ID_Abteilung` (`ID_Abteilung`);

ALTER TABLE `ausbildungsberufe`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`);

ALTER TABLE `auszubildende`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD KEY `ID_Ausbildungsberuf` (`ID_Ausbildungsberuf`);

ALTER TABLE `errors`
  ADD UNIQUE KEY `ID` (`ID`);

ALTER TABLE `pläne`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD KEY `ID_Auszubildender` (`ID_Auszubildender`),
  ADD KEY `ID_Ansprechpartner` (`ID_Ansprechpartner`),
  ADD KEY `ID_Abteilung` (`ID_Abteilung`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `standardpläne`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD KEY `ID_Ausbildungsberuf` (`ID_Ausbildungsberuf`),
  ADD KEY `ID_Abteilung` (`ID_Abteilung`);

ALTER TABLE `termine`
  ADD UNIQUE KEY `ID` (`ID`),
  ADD KEY `termine_ibfk_1` (`ID_Plan`);


ALTER TABLE `abteilungen`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `accounts`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `ansprechpartner`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `ausbildungsberufe`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `auszubildende`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `errors`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `pläne`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `settings`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `standardpläne`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `termine`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `ansprechpartner`
  ADD CONSTRAINT `ansprechpartner_ibfk_1` FOREIGN KEY (`ID_Abteilung`) REFERENCES `abteilungen` (`ID`);

ALTER TABLE `auszubildende`
  ADD CONSTRAINT `auszubildende_ibfk_1` FOREIGN KEY (`ID_Ausbildungsberuf`) REFERENCES `ausbildungsberufe` (`ID`);

ALTER TABLE `pläne`
  ADD CONSTRAINT `pläne_ibfk_1` FOREIGN KEY (`ID_Auszubildender`) REFERENCES `auszubildende` (`ID`),
  ADD CONSTRAINT `pläne_ibfk_2` FOREIGN KEY (`ID_Ansprechpartner`) REFERENCES `ansprechpartner` (`ID`),
  ADD CONSTRAINT `pläne_ibfk_3` FOREIGN KEY (`ID_Abteilung`) REFERENCES `abteilungen` (`ID`);

ALTER TABLE `standardpläne`
  ADD CONSTRAINT `standardpläne_ibfk_1` FOREIGN KEY (`ID_Ausbildungsberuf`) REFERENCES `ausbildungsberufe` (`ID`),
  ADD CONSTRAINT `standardpläne_ibfk_2` FOREIGN KEY (`ID_Abteilung`) REFERENCES `abteilungen` (`ID`);

ALTER TABLE `termine`
  ADD CONSTRAINT `termine_ibfk_1` FOREIGN KEY (`ID_Plan`) REFERENCES `pläne` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
