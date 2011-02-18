UPDATE config_top_menus SET `Index`='5' WHERE `Index`='4';
UPDATE config_top_menus SET Action='#' WHERE `Index`='1';
INSERT INTO config_top_menus (`Index`, Action, `Name`) VALUES ('4', '#', 'db');