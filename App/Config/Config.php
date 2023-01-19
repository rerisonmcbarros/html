<?php 

//CONFIGURAÇÃO DE ARQUIVOS E DIRETÓRIOS



define("DOCUMENT_ROOT", "Penedo");

//CONFIGURAÇÃO DE CONEXÃO COM DB

define("DB_CONFIG", [

	"DB_DRIVE" => "mysql",
	"DB_HOST" => "localhost",
	"DB_NAME" => "penedo",
	"DB_PORT" => "3306",
	"DB_USER" => "root",
	"DB_PASSWD" => "",
]);
/*
define("DB_HOST", "localhost");
define("DB_NAME", "users");
define("DB_USER", "root");
define("DB_PASSWD", "password");
define("DB_OPTIONS", [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
*/