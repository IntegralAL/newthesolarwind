Новая версия CMS "TheSolarWind" 2026 года выпуска,
актуальна для PHP 8.1, совместима с PHP 5.6 

Разработчик: Ларионов Андрей Николаевич

Версия продукта: 10.0.0.1 alfa version

Язык продукта: Русский / Английский

Структура проекта:

   Папка
        /app
	        /Controller
		/Models
		/Views
	/cloud
	/resource
	Config.php
	index.php
	README.txt

Создайте базу данных, как определено в файле app\Models\Class_Config.php

и в базе создайте таблицу:

CREATE TABLE `standartpages` (
	`id` BIGINT(19) NOT NULL AUTO_INCREMENT,
	`name_page` CHAR(255) NULL DEFAULT '0' COLLATE 'utf8mb3_general_ci',
	`theme_page` INT(10) NOT NULL DEFAULT '1',
	`context_page` TEXT NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`url_page` CHAR(255) NULL DEFAULT '0' COLLATE 'utf8mb3_general_ci',
	`address_page` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`create_page` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	`creator_page` CHAR(255) NULL DEFAULT 'admin' COLLATE 'utf8mb3_general_ci',
	`text_page` TEXT NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`alt_text1` TEXT NOT NULL COLLATE 'utf8mb3_unicode_ci',
	`alit_text2` TEXT(16383) NOT NULL COLLATE 'utf32_unicode_ci',
	`alt_text3` TEXT NOT NULL COLLATE 'utf8mb3_unicode_ci',
	`alt_text4` TEXT NOT NULL COLLATE 'utf8mb3_unicode_ci',
	`for_del` INT(10) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb3_general_ci'
ENGINE=InnoDB
;
