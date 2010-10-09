ALTER TABLE `config_menus` ADD COLUMN `Order` INT(11) AFTER `Menu`;

UPDATE `config_menus` SET `Order`=1 WHERE `Index`=1;
UPDATE `config_menus` SET `Order`=2 WHERE `Index`=2;
UPDATE `config_menus` SET `Order`=3 WHERE `Index`=3;
UPDATE `config_menus` SET `Order`=4 WHERE `Index`=4;
UPDATE `config_menus` SET `Order`=5 WHERE `Index`=5;
UPDATE `config_menus` SET `Order`=6 WHERE `Index`=6;
UPDATE `config_menus` SET `Order`=7 WHERE `Index`=7;
UPDATE `config_menus` SET `Order`=8 WHERE `Index`=8;
UPDATE `config_menus` SET `Order`=8 WHERE `Index`=9;

UPDATE `config_menus` SET `Order`=1 WHERE `Index`=10;
UPDATE `config_menus` SET `Order`=2 WHERE `Index`=11;
UPDATE `config_menus` SET `Order`=3 WHERE `Index`=12;
UPDATE `config_menus` SET `Order`=4 WHERE `Index`=13;
UPDATE `config_menus` SET `Order`=5 WHERE `Index`=14;
UPDATE `config_menus` SET `Order`=6 WHERE `Index`=15;
UPDATE `config_menus` SET `Order`=7 WHERE `Index`=16;
UPDATE `config_menus` SET `Order`=8 WHERE `Index`=17;
UPDATE `config_menus` SET `Order`=9 WHERE `Index`=43;
UPDATE `config_menus` SET `Order`=10 WHERE `Index`=44;
UPDATE `config_menus` SET `Order`=12 WHERE `Index`=45;
UPDATE `config_menus` SET `Order`=11 WHERE `Index`=53;

UPDATE `config_menus` SET `Order`=1 WHERE `Index`=25;
UPDATE `config_menus` SET `Order`=2 WHERE `Index`=26;

UPDATE `config_menus` SET `Order`=0 WHERE `Index`=28;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=29;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=30;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=31;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=32;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=33;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=34;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=35;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=36;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=37;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=38;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=39;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=40;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=41;
UPDATE `config_menus` SET `Order`=0 WHERE `Index`=42;