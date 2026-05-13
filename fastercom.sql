-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 13, 2026 alle 09:57
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fastercom`
--
CREATE DATABASE IF NOT EXISTS `fastercom` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `fastercom`;

-- --------------------------------------------------------

--
-- Struttura della tabella `classi`
--

CREATE TABLE `classi` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `anno` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `classi`
--

INSERT INTO `classi` (`id`, `nome`, `anno`) VALUES
(1, '1A', '2025-2026'),
(2, '2B', '2025-2026'),
(3, '3C', '2025-2026'),
(4, '4D', '2025-2026'),
(5, '5E', '2025-2026');

-- --------------------------------------------------------

--
-- Struttura della tabella `docenti`
--

CREATE TABLE `docenti` (
  `id` int(11) NOT NULL,
  `utente_id` int(11) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `docenti`
--

INSERT INTO `docenti` (`id`, `utente_id`, `nome`, `cognome`) VALUES
(1, 2, 'Marco', 'Rossi'),
(5, 14, 'Mario', 'Bianchi'),
(6, 16, 'Giulia', 'Verdi'),
(7, 17, 'Luca', 'Ferrari');

-- --------------------------------------------------------

--
-- Struttura della tabella `insegnamenti`
--

CREATE TABLE `insegnamenti` (
  `id` int(11) NOT NULL,
  `docente_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `classe_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `insegnamenti`
--

INSERT INTO `insegnamenti` (`id`, `docente_id`, `materia_id`, `classe_id`) VALUES
(1, 1, 1, 1),
(2, 1, 1, 2),
(5, 1, 2, 2),
(4, 1, 2, 3),
(6, 6, 1, 3),
(7, 6, 1, 4),
(8, 7, 4, 3),
(9, 7, 4, 4),
(10, 7, 4, 5);

-- --------------------------------------------------------

--
-- Struttura della tabella `materie`
--

CREATE TABLE `materie` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `materie`
--

INSERT INTO `materie` (`id`, `nome`) VALUES
(2, 'Italiano'),
(1, 'Matematica'),
(4, 'Storia');

-- --------------------------------------------------------

--
-- Struttura della tabella `studenti`
--

CREATE TABLE `studenti` (
  `id` int(11) NOT NULL,
  `utente_id` int(11) DEFAULT NULL,
  `classe_id` int(11) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `data_nascita` date DEFAULT NULL,
  `codice_fiscale` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `studenti`
--

INSERT INTO `studenti` (`id`, `utente_id`, `classe_id`, `nome`, `cognome`, `data_nascita`, `codice_fiscale`) VALUES
(1, 4, 1, 'Mario', 'Verdi', '2010-03-15', 'VRDMRA10C15H501A'),
(2, 5, 1, 'Anna', 'Neri', '2010-07-22', 'NRANNA10L62H501B'),
(3, 6, 2, 'Luca', 'Blu', '2009-11-05', 'BLULCU09S05H501C'),
(4, 7, 2, 'Sara', 'Gialli', '2009-04-18', 'GLLSRA09D58H501D'),
(6, 18, 3, 'Sofia', 'Romano', '2008-09-12', 'RMNSFO08P52H501E'),
(7, 19, 4, 'Davide', 'Conti', '2008-06-30', 'CNTDVD08H30H501F'),
(8, 20, 5, 'Elena', 'Marchetti', '2007-12-01', 'MRCLNE07T41H501G');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `ruolo` enum('studente','docente','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `email`, `password_hash`, `ruolo`) VALUES
(1, 'admin@scuola.it', '$2y$10$EHQvzlG/HxfH3fl6xTY2G.F2bhRBF/HhXthtzpulCUkCLATusymaq', 'admin'),
(2, 'rossi.marco@scuola.it', '$2y$10$EJi0/yCPAH/W2AcFbKU7XuYaqAOyZdYYh9ERonBW7tgCeM9utPtoW', 'docente'),
(4, 'mario.verdi@studenti.it', '$2y$10$f2R5UDRtCU5GneKKMbe3WuOo9piAGWhUH8W8EAlSwFB5rjETZpp0u', 'studente'),
(5, 'anna.neri@studenti.it', '$2y$10$f2R5UDRtCU5GneKKMbe3WuOo9piAGWhUH8W8EAlSwFB5rjETZpp0u', 'studente'),
(6, 'luca.blu@studenti.it', '$2y$10$f2R5UDRtCU5GneKKMbe3WuOo9piAGWhUH8W8EAlSwFB5rjETZpp0u', 'studente'),
(7, 'sara.gialli@studenti.it', '$2y$10$f2R5UDRtCU5GneKKMbe3WuOo9piAGWhUH8W8EAlSwFB5rjETZpp0u', 'studente'),
(14, 'mario.bianchi@scuola.it', '$2y$10$EJi0/yCPAH/W2AcFbKU7XuYaqAOyZdYYh9ERonBW7tgCeM9utPtoW', 'docente'),
(16, 'verdi.giulia@scuola.it', '$2y$10$EJi0/yCPAH/W2AcFbKU7XuYaqAOyZdYYh9ERonBW7tgCeM9utPtoW', 'docente'),
(17, 'ferrari.luca@scuola.it', '$2y$10$EJi0/yCPAH/W2AcFbKU7XuYaqAOyZdYYh9ERonBW7tgCeM9utPtoW', 'docente'),
(18, 'sofia.romano@studenti.it', '$2y$10$f2R5UDRtCU5GneKKMbe3WuOo9piAGWhUH8W8EAlSwFB5rjETZpp0u', 'studente'),
(19, 'davide.conti@studenti.it', '$2y$10$f2R5UDRtCU5GneKKMbe3WuOo9piAGWhUH8W8EAlSwFB5rjETZpp0u', 'studente'),
(20, 'elena.marchetti@studenti.it', '$2y$10$EJi0/yCPAH/W2AcFbKU7XuYaqAOyZdYYh9ERonBW7tgCeM9utPtoW', 'studente');

-- --------------------------------------------------------

--
-- Struttura della tabella `voti`
--

CREATE TABLE `voti` (
  `id` int(11) NOT NULL,
  `insegnamento_id` int(11) NOT NULL,
  `studente_id` int(11) NOT NULL,
  `valore` decimal(3,1) NOT NULL,
  `tipo` enum('scritto','orale','pratico') DEFAULT NULL,
  `data` date NOT NULL,
  `nota` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `voti`
--

INSERT INTO `voti` (`id`, `insegnamento_id`, `studente_id`, `valore`, `tipo`, `data`, `nota`) VALUES
(1, 1, 1, 8.5, 'scritto', '2026-02-10', 'Ottima prova'),
(2, 1, 2, 7.0, 'scritto', '2026-02-10', 'Buona prova'),
(3, 2, 3, 6.5, 'orale', '2026-02-12', NULL),
(4, 2, 4, 9.0, 'orale', '2026-02-12', 'Eccellente'),
(7, 1, 1, 9.0, 'orale', '2026-04-15', 'Ottima esposizione'),
(8, 1, 1, 8.5, 'scritto', '2026-04-29', 'Ottima prova'),
(9, 1, 1, 1.0, 'scritto', '2026-04-29', 'Ottima prova'),
(10, 1, 1, 8.5, 'scritto', '2026-04-29', 'Ottima prova'),
(17, 1, 2, 1.0, 'scritto', '2026-04-24', NULL),
(18, 2, 3, 10.0, 'orale', '2026-04-28', 'molto bavo'),
(19, 1, 1, 10.0, 'scritto', '2026-05-13', 'fai cagare'),
(20, 2, 4, 1.0, 'scritto', '2026-05-13', 'molto bavo'),
(21, 2, 4, 10.0, 'scritto', '2026-05-13', 'fai cagare'),
(22, 1, 2, 10.0, 'orale', '2026-05-13', NULL),
(24, 6, 6, 7.5, 'scritto', '2026-02-20', 'Buona prova'),
(25, 6, 6, 8.0, 'orale', '2026-03-10', 'Ottima partecipazione'),
(26, 8, 6, 6.5, 'scritto', '2026-02-25', NULL),
(27, 8, 6, 9.0, 'orale', '2026-04-03', 'Eccellente esposizione'),
(28, 7, 7, 5.5, 'scritto', '2026-02-18', 'Deve migliorare'),
(29, 7, 7, 7.0, 'scritto', '2026-03-22', 'Miglioramento evidente'),
(30, 9, 7, 8.5, 'orale', '2026-03-05', 'Ottima conoscenza'),
(31, 10, 8, 9.5, 'scritto', '2026-02-28', 'Quasi perfetta'),
(32, 10, 8, 7.0, 'orale', '2026-04-10', NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `classi`
--
ALTER TABLE `classi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`,`anno`);

--
-- Indici per le tabelle `docenti`
--
ALTER TABLE `docenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utente_id` (`utente_id`);

--
-- Indici per le tabelle `insegnamenti`
--
ALTER TABLE `insegnamenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `docente_id` (`docente_id`,`materia_id`,`classe_id`),
  ADD KEY `materia_id` (`materia_id`),
  ADD KEY `classe_id` (`classe_id`);

--
-- Indici per le tabelle `materie`
--
ALTER TABLE `materie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Indici per le tabelle `studenti`
--
ALTER TABLE `studenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utente_id` (`utente_id`),
  ADD UNIQUE KEY `codice_fiscale` (`codice_fiscale`),
  ADD KEY `classe_id` (`classe_id`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indici per le tabelle `voti`
--
ALTER TABLE `voti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `insegnamento_id` (`insegnamento_id`),
  ADD KEY `studente_id` (`studente_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `classi`
--
ALTER TABLE `classi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `docenti`
--
ALTER TABLE `docenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `insegnamenti`
--
ALTER TABLE `insegnamenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `materie`
--
ALTER TABLE `materie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `studenti`
--
ALTER TABLE `studenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT per la tabella `voti`
--
ALTER TABLE `voti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `docenti`
--
ALTER TABLE `docenti`
  ADD CONSTRAINT `docenti_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`) ON DELETE SET NULL;

--
-- Limiti per la tabella `insegnamenti`
--
ALTER TABLE `insegnamenti`
  ADD CONSTRAINT `insegnamenti_ibfk_1` FOREIGN KEY (`docente_id`) REFERENCES `docenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `insegnamenti_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materie` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `insegnamenti_ibfk_3` FOREIGN KEY (`classe_id`) REFERENCES `classi` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `studenti`
--
ALTER TABLE `studenti`
  ADD CONSTRAINT `studenti_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `studenti_ibfk_2` FOREIGN KEY (`classe_id`) REFERENCES `classi` (`id`) ON DELETE SET NULL;

--
-- Limiti per la tabella `voti`
--
ALTER TABLE `voti`
  ADD CONSTRAINT `voti_ibfk_1` FOREIGN KEY (`insegnamento_id`) REFERENCES `insegnamenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `voti_ibfk_2` FOREIGN KEY (`studente_id`) REFERENCES `studenti` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
