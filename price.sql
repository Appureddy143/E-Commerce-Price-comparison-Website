-- This SQL script creates the database and all necessary tables.
-- You can run this entire script at once in phpMyAdmin.

-- --------------------------------------------------------
--                  DATABASE CREATION
-- --------------------------------------------------------

-- Create the database if it doesn't already exist to prevent errors.
CREATE DATABASE IF NOT EXISTS `pricecomp_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Select the newly created database to use for the next commands.
USE `pricecomp_db`;

-- --------------------------------------------------------
--                  TABLE CREATION
-- --------------------------------------------------------

-- Set the default time zone to prevent timestamp issues.
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Table structure for table `users`
-- This table will store all user information, including their role.
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT 'default.png',
  `role` varchar(10) NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `history`
-- This table will store the search history for each user.
--
CREATE TABLE `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `search_term` varchar(255) NOT NULL,
  `searched_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
--                  TABLE CONSTRAINTS
-- --------------------------------------------------------

--
-- Constraints for table `history`
-- This links the `history` table to the `users` table.
-- If a user is deleted, all of their search history will also be deleted.
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

COMMIT;


ALTER TABLE `users`
ADD `last_login` TIMESTAMP NULL DEFAULT NULL
AFTER `created_at`;