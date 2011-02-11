UPDATE config_accounts SET SecurityLevel=SecurityLevel+(WebAdmin*1073741824);
ALTER TABLE config_accounts DROP COLUMN WebAdmin;