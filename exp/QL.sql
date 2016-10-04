
CREATE TABLE `opening_balance` (
  `id` int(11) NOT NULL,
  `subsidiary` int(11) NOT NULL,
  `name` char(128) NOT NULL,
  `client` int(11) NOT NULL,
  `client_subsidiary` int(11) NOT NULL,
  `folio` char(60) NOT NULL,
  `comment` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  `type_of_currency` int(11) NOT NULL,
  `exchange_rate` double NOT NULL,
  `registred_by` int(11) NOT NULL,
  `registred_on` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_on` datetime NOT NULL,
  `import` double NOT NULL,
  `sub_total` double NOT NULL,
  `tax_iva` double NOT NULL,
  `tax_iva_retained` double NOT NULL,
  `tax_isr` double NOT NULL,
  `tax_ieps` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


INSERT INTO `opening_balance` (`id`, `subsidiary`, `name`, `client`, `client_subsidiary`, `folio`, `comment`, `status`, `type_of_currency`, `exchange_rate`, `registred_by`, `registred_on`, `updated_by`, `updated_on`, `import`, `sub_total`, `tax_iva`, `tax_iva_retained`, `tax_isr`, `tax_ieps`) VALUES
(1, 1, '', 1, 1, '23', '', 6, 1, 0, 1, '2016-03-29 14:33:46', 1, '2016-03-29 15:24:54', 23, 23, 0, 0, 0, 0),
(2, 1, '', 1, 1, 'as', '', 12, 1, 0, 1, '2016-03-29 14:38:36', 1, '2016-03-29 16:27:29', 12, 12, 0, 0, 0, 0);



CREATE TABLE `opening_balance_details` (
  `id` int(11) NOT NULL,
  `opening_balance` int(11) NOT NULL,
  `stockModification` int(1) NOT NULL,
  `quantity` double NOT NULL,
  `article` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `totalSub` double NOT NULL,
  `discount` double NOT NULL,
  `taxIeps` double NOT NULL,
  `taxIva` double NOT NULL,
  `taxIvaRetained` double NOT NULL,
  `taxIsr` double NOT NULL,
  `registred_by` int(11) NOT NULL,
  `registred_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_by` int(11) NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `opening_balance_details` (`id`, `opening_balance`, `stockModification`, `quantity`, `article`, `description`, `price`, `totalSub`, `discount`, `taxIeps`, `taxIva`, `taxIvaRetained`, `taxIsr`, `registred_by`, `registred_on`, `updated_by`, `updated_on`) VALUES
(1, 2, 0, 1, 1, '', 12, 12, 0, 0, 0, 0, 0, 1, '2016-03-29 20:52:07', 1, '2016-03-29 22:27:29'),
(2, 1, 0, 1, 1, '', 23, 23, 0, 0, 0, 0, 0, 1, '2016-03-29 21:24:54', 0, '0000-00-00 00:00:00');


ALTER TABLE `opening_balance`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `opening_balance_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opening_balance` (`opening_balance`),
  ADD KEY `article` (`article`);


ALTER TABLE `opening_balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `opening_balance_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


CREATE TABLE `opening_balance_payments` (
  `id` int(11) NOT NULL,
  `opening_balance` int(11) NOT NULL,
  `method` int(2) NOT NULL,
  `import` double NOT NULL,
  `date` date NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `type_of_currency` int(5) NOT NULL,
  `exchange_rate` double NOT NULL,
  `bank_accounting_ledger_accounts` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `registred_by` int(11) NOT NULL,
  `registred_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_by` int(11) NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `opening_balance_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_opening_balance` (`opening_balance`);


ALTER TABLE `opening_balance_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

  ALTER TABLE `opening_balance` ADD `sub_total_discount` DOUBLE NOT NULL AFTER `sub_total`, ADD `payment` DOUBLE NOT NULL AFTER `sub_total_discount`, ADD `discount` DOUBLE NOT NULL AFTER `payment`;



-- LOCAL --PRODUCCION
ALTER TABLE  `cinepixi_movie` ADD  `pathFile_id` INT NOT NULL AFTER  `category_id` ;
