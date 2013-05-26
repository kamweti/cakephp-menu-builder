    CREATE TABLE `menus` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(100) DEFAULT NULL,
      `slug` varchar(100) DEFAULT NULL,
      `status` int(11) DEFAULT '1' COMMENT '1=>active 0=>disabled',
      `created` datetime DEFAULT NULL,
      `deleted` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    );

    CREATE TABLE `menuitems` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `menu_id` int(11) DEFAULT NULL COMMENT 'id of menu',
      `label` varchar(100) DEFAULT NULL,
      `url` varchar(80) DEFAULT NULL,
      `controller` varchar(100) DEFAULT NULL,
      `action` varchar(100) DEFAULT NULL,
      `children` text COMMENT 'menuitem id of menu',
      `created` datetime DEFAULT NULL,
      `modified` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    )
