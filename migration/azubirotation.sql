-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Sep 2020 um 08:27
-- Server-Version: 10.4.14-MariaDB
-- PHP-Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `azubirotation`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `abteilungen`
--

CREATE TABLE `abteilungen` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Bezeichnung` varchar(50) NOT NULL,
  `MaxAzubis` int(11) NOT NULL,
  `Farbe` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ansprechpartner`
--

CREATE TABLE `ansprechpartner` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Name` varchar(30) NOT NULL,
  `Email` varchar(320) NOT NULL,
  `ID_Abteilung` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ausbildungsberufe`
--

CREATE TABLE `ausbildungsberufe` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Bezeichnung` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auszubildende`
--

CREATE TABLE `auszubildende` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Vorname` varchar(30) NOT NULL,
  `Nachname` varchar(30) NOT NULL,
  `Email` varchar(320) NOT NULL,
  `ID_Ausbildungsberuf` bigint(20) UNSIGNED DEFAULT NULL,
  `Ausbildungsstart` date NOT NULL,
  `Ausbildungsende` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pläne`
--

CREATE TABLE `pläne` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `ID_Auszubildender` bigint(20) UNSIGNED DEFAULT NULL,
  `ID_Ansprechpartner` bigint(20) UNSIGNED DEFAULT NULL,
  `ID_Abteilung` bigint(20) UNSIGNED DEFAULT NULL,
  `Startdatum` date NOT NULL,
  `Enddatum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `standardpläne`
--

CREATE TABLE `standardpläne` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `ID_Ausbildungsberuf` bigint(20) UNSIGNED DEFAULT NULL,
  `ID_Abteilung` bigint(20) UNSIGNED DEFAULT NULL,
  `AnzahlWochen` int(11) NOT NULL,
  `Praeferieren` tinyint(1) NOT NULL,
  `Optional` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `abteilungen`
--
ALTER TABLE `abteilungen`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`);

--
-- Indizes für die Tabelle `ansprechpartner`
--
ALTER TABLE `ansprechpartner`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD KEY `ID_Abteilung` (`ID_Abteilung`);

--
-- Indizes für die Tabelle `ausbildungsberufe`
--
ALTER TABLE `ausbildungsberufe`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`);

--
-- Indizes für die Tabelle `auszubildende`
--
ALTER TABLE `auszubildende`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD KEY `ID_Ausbildungsberuf` (`ID_Ausbildungsberuf`);

--
-- Indizes für die Tabelle `pläne`
--
ALTER TABLE `pläne`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD KEY `ID_Auszubildender` (`ID_Auszubildender`),
  ADD KEY `ID_Ansprechpartner` (`ID_Ansprechpartner`),
  ADD KEY `ID_Abteilung` (`ID_Abteilung`);

--
-- Indizes für die Tabelle `standardpläne`
--
ALTER TABLE `standardpläne`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD KEY `ID_Ausbildungsberuf` (`ID_Ausbildungsberuf`),
  ADD KEY `ID_Abteilung` (`ID_Abteilung`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `abteilungen`
--
ALTER TABLE `abteilungen`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `ansprechpartner`
--
ALTER TABLE `ansprechpartner`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `ausbildungsberufe`
--
ALTER TABLE `ausbildungsberufe`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `auszubildende`
--
ALTER TABLE `auszubildende`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `pläne`
--
ALTER TABLE `pläne`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `standardpläne`
--
ALTER TABLE `standardpläne`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `ansprechpartner`
--
ALTER TABLE `ansprechpartner`
  ADD CONSTRAINT `ansprechpartner_ibfk_1` FOREIGN KEY (`ID_Abteilung`) REFERENCES `abteilungen` (`ID`);

--
-- Constraints der Tabelle `auszubildende`
--
ALTER TABLE `auszubildende`
  ADD CONSTRAINT `auszubildende_ibfk_1` FOREIGN KEY (`ID_Ausbildungsberuf`) REFERENCES `ausbildungsberufe` (`ID`);

--
-- Constraints der Tabelle `pläne`
--
ALTER TABLE `pläne`
  ADD CONSTRAINT `pläne_ibfk_1` FOREIGN KEY (`ID_Auszubildender`) REFERENCES `auszubildende` (`ID`),
  ADD CONSTRAINT `pläne_ibfk_2` FOREIGN KEY (`ID_Ansprechpartner`) REFERENCES `ansprechpartner` (`ID`),
  ADD CONSTRAINT `pläne_ibfk_3` FOREIGN KEY (`ID_Abteilung`) REFERENCES `abteilungen` (`ID`);

--
-- Constraints der Tabelle `standardpläne`
--
ALTER TABLE `standardpläne`
  ADD CONSTRAINT `standardpläne_ibfk_1` FOREIGN KEY (`ID_Ausbildungsberuf`) REFERENCES `ausbildungsberufe` (`ID`),
  ADD CONSTRAINT `standardpläne_ibfk_2` FOREIGN KEY (`ID_Abteilung`) REFERENCES `abteilungen` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
