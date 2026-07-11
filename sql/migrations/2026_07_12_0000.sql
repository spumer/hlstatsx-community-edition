/*
:? Reason: Introduce a server-wide i18n layer (web/lang/*.php + LanguageService)
:i Info: New option drives which locale __() resolves; missing option or row
         defaults to 'en' in code (web/hlstats.php), so existing installs
         keep rendering English until this row is added and set to another
         locale.
:! Change: Insert new option 'language', default 'en'
*/
INSERT IGNORE INTO `hlstats_Options` (`keyname`, `value`, `opttype`) VALUES ('language', 'en', 1);
