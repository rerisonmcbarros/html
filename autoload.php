<?php

require_once __DIR__.'/App/Config/Config.php';
require_once __DIR__.'/Lib/Core/AplicationLoader.php';


spl_autoload_register( array(new \Lib\Core\AplicationLoader(DOCUMENT_ROOT), 'load') );