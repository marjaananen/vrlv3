-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 31.03.2019 klo 09:13
-- Palvelimen versio: 10.1.28-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vrl`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_archives`
--

CREATE TABLE `fuel_archives` (
  `id` int(10) UNSIGNED NOT NULL,
  `ref_id` int(10) UNSIGNED NOT NULL,
  `table_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `version` smallint(5) UNSIGNED NOT NULL,
  `version_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `archived_user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_blocks`
--

CREATE TABLE `fuel_blocks` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `view` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'english',
  `published` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `date_added` datetime DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_categories`
--

CREATE TABLE `fuel_categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `context` varchar(100) NOT NULL DEFAULT '',
  `language` varchar(30) NOT NULL DEFAULT 'english',
  `precedence` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `published` enum('yes','no') NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_logs`
--

CREATE TABLE `fuel_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `entry_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_navigation`
--

CREATE TABLE `fuel_navigation` (
  `id` int(10) UNSIGNED NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The part of the path after the domain name that you want the link to go to (e.g. company/about_us)',
  `group_id` int(5) UNSIGNED NOT NULL DEFAULT '1',
  `nav_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The nav key is a friendly ID that you can use for setting the selected state. If left blank, a default value will be set for you.',
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The name you want to appear in the menu',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Used for creating menu hierarchies. No value means it is a root level menu item',
  `precedence` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The higher the number, the greater the precedence and farther up the list the navigational element will appear',
  `attributes` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Extra attributes that can be used for navigation implementation',
  `selected` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The pattern to match for the active state. Most likely you leave this field blank',
  `hidden` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no' COMMENT 'A hidden value can be used in rendering the menu. In some areas, the menu item may not want to be displayed',
  `language` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'english',
  `published` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes' COMMENT 'Determines whether the item is displayed or not'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_navigation_groups`
--

CREATE TABLE `fuel_navigation_groups` (
  `id` int(3) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `published` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_pages`
--

CREATE TABLE `fuel_pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Add the part of the url after the root of your site (usually after the domain name). For the homepage, just put the word ''home''',
  `layout` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The name of the template to associate with this page',
  `published` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes' COMMENT 'A ''yes'' value will display the page and an ''no'' value will give a 404 error message',
  `cache` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes' COMMENT 'Cache controls whether the page will pull from the database or from a saved file which is more efficient. If a page has content that is dynamic, it''s best to set cache to ''no''',
  `date_added` datetime DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_modified_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_page_variables`
--

CREATE TABLE `fuel_page_variables` (
  `id` int(10) UNSIGNED NOT NULL,
  `page_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `scope` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('string','int','boolean','array') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'string',
  `language` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'english',
  `active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_permissions`
--

CREATE TABLE `fuel_permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'In most cases, this should be the name of the module (e.g. news)',
  `active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_relationships`
--

CREATE TABLE `fuel_relationships` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `candidate_table` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `candidate_key` int(11) NOT NULL,
  `foreign_table` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foreign_key` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_settings`
--

CREATE TABLE `fuel_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `module` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `key` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` longtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_site_variables`
--

CREATE TABLE `fuel_site_variables` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `scope` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'leave blank if you want the variable to be available to all pages',
  `active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_tags`
--

CREATE TABLE `fuel_tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `context` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `language` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'english',
  `precedence` int(11) NOT NULL,
  `published` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Rakenne taululle `fuel_users`
--

CREATE TABLE `fuel_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `reset_key` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `super_admin` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fuel_archives`
--
ALTER TABLE `fuel_archives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fuel_blocks`
--
ALTER TABLE `fuel_blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`,`language`);

--
-- Indexes for table `fuel_categories`
--
ALTER TABLE `fuel_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `fuel_logs`
--
ALTER TABLE `fuel_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fuel_navigation`
--
ALTER TABLE `fuel_navigation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_id_nav_key_language` (`group_id`,`nav_key`,`language`);

--
-- Indexes for table `fuel_navigation_groups`
--
ALTER TABLE `fuel_navigation_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `fuel_pages`
--
ALTER TABLE `fuel_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `location` (`location`),
  ADD KEY `layout` (`layout`);

--
-- Indexes for table `fuel_page_variables`
--
ALTER TABLE `fuel_page_variables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_id` (`page_id`,`name`,`language`);

--
-- Indexes for table `fuel_permissions`
--
ALTER TABLE `fuel_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `fuel_relationships`
--
ALTER TABLE `fuel_relationships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_key` (`candidate_table`,`candidate_key`,`foreign_table`,`foreign_key`),
  ADD KEY `candidate_table` (`candidate_table`,`candidate_key`),
  ADD KEY `foreign_table` (`foreign_table`,`foreign_key`);

--
-- Indexes for table `fuel_settings`
--
ALTER TABLE `fuel_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `module` (`module`,`key`);

--
-- Indexes for table `fuel_site_variables`
--
ALTER TABLE `fuel_site_variables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `fuel_tags`
--
ALTER TABLE `fuel_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `fuel_users`
--
ALTER TABLE `fuel_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fuel_archives`
--
ALTER TABLE `fuel_archives`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_blocks`
--
ALTER TABLE `fuel_blocks`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_categories`
--
ALTER TABLE `fuel_categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_logs`
--
ALTER TABLE `fuel_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_navigation`
--
ALTER TABLE `fuel_navigation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_navigation_groups`
--
ALTER TABLE `fuel_navigation_groups`
  MODIFY `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_pages`
--
ALTER TABLE `fuel_pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_page_variables`
--
ALTER TABLE `fuel_page_variables`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_permissions`
--
ALTER TABLE `fuel_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_relationships`
--
ALTER TABLE `fuel_relationships`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_settings`
--
ALTER TABLE `fuel_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_site_variables`
--
ALTER TABLE `fuel_site_variables`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_tags`
--
ALTER TABLE `fuel_tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_users`
--
ALTER TABLE `fuel_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
