<!--  -->
<?php 
Dana2012.uza4C3R0HP
+52 1 81 2434 8439
TRUNCATE `article`;
TRUNCATE `client`;
TRUNCATE `client_subsidiary`;
TRUNCATE `provider`;
TRUNCATE `purchase`;
TRUNCATE `purchase_details`;
TRUNCATE `sale`;
TRUNCATE `sale_details`;
TRUNCATE `sale_payments`;
TRUNCATE `remission`;
TRUNCATE `remission_details`;
TRUNCATE `remission_payments`;
TRUNCATE `request`;
TRUNCATE `request_details`;
TRUNCATE `ci_sessions`;
TRUNCATE `enterprise`;
TRUNCATE `pac`;
TRUNCATE `series`;
TRUNCATE `shcp_file`;
TRUNCATE `subsidiary`;

ALTER TABLE `publications_hosting_server_link` ADD `publication` INT NOT NULL AFTER `publications_hosting_server_id`;
// magnolia 2908 colonia reymundo almaguer
// CORREGIR 
UPDATE `publications_hosting_server_link` 
LEFT JOIN `publications_hosting_server` 
ON `publications_hosting_server`.`id` = `publications_hosting_server_link`.`publications_hosting_server_id`
SET `publications_hosting_server_link`.`publication` = `publications_hosting_server`.`publication_id` ;

INSERT INTO `_vars_system` (`id`, `category`, `type`, `name`, `value`, `description`, `status`, `registred_by`, `registred_on`, `updated_by`, `updated_on`) VALUES (NULL, 'storage', 'string', 'storage/publication_config', 'pirabook/images/uploads/imgPost/', '', '1', '0', '2015-10-13 04:00:00', '0', '0000-00-00 00:00:00');


-- CAMBIE LA CARPETA DE STORAGE Y ESO IMPLICA CHECAR MUCHOS MODULOS QUE MANEJAN IMAGES 
-- Hacer el catalogo de productos (CATEGORIAS)

<CINEVELO>
-- Falta hacer la relacion de la pelicula con el RECORD
<CINEVELO>
?