<?php

	writeln("#######################################");
	writeln("## Reaccion CMS Bundle 0.1 Installer ##");
	writeln("#######################################");
	br(2);

	CONST SYMFONY_PATH = __DIR__ . '/../../';

	// Get email address
	$senderEmailAddr = readline("Enter app sender email address: ");
	$senderEmailAddr = ( ! empty($senderEmailAddr)) ? $senderEmailAddr : "email@host.com";

	// Install Reaccion CMS Admin panel?
	do
	{
		$installAdminPanel = readline("Do you want to install the Reaccion CMS Admin Panel? [Y/N]");
	}
	while( ! in_array(strtolower($installAdminPanel), ['y','n']) );

	writeln("################");
	writeln("## Installing ##");
	writeln("################");
	br();

	// Copy 'services.yaml' file
	copyFileToSfApp(__DIR__ . '/config/services.yaml', SYMFONY_PATH . 'config/services.yaml', true);

	// Copy 'fos_user.yaml' file
	copyFileToSfApp(__DIR__ . '/config/packages/fos_user.yaml', SYMFONY_PATH . 'config/packages/fos_user.yaml', true);

	// Copy 'framework.yaml' file
	copyFileToSfApp(__DIR__ . '/config/packages/framework.yaml', SYMFONY_PATH . 'config/packages/framework.yaml', true);

	// Copy 'package.json' file
	copyFileToSfApp(__DIR__ . '/package.json', SYMFONY_PATH . 'package.json', true);

	// check assets file exists
	if( ! file_exists(SYMFONY_PATH . "/assets"))
	{
		mkdir(SYMFONY_PATH . "/assets/js", 0755, true);
	}

	// Copy 'webpack.config.js'
	if($installAdminPanel)
	{
		copyFileToSfApp(__DIR__ . "/assets/js/app.js", SYMFONY_PATH . "assets/js/app.js", true);
		copyFileToSfApp(__DIR__ . "/webpack.config.js_with_panel", SYMFONY_PATH . "webpack.config.js", true);
	}
	else
	{
		copyFileToSfApp(__DIR__ . "/webpack.config.js", SYMFONY_PATH . "webpack.config.js", true);
	}

	// assets
	copyFileToSfApp(__DIR__ . "/assets/js/front_app.js", SYMFONY_PATH . "assets/js/front_app.js", true);

	// security.yml
	copyFileToSfApp(__DIR__ . "/config/packages/security.yaml", SYMFONY_PATH . "config/packages/security.yaml", true);

	br(2);

	// End installation script

	/**
	 * Functions
	 */
	function writeln(String $message)
	{
		echo "\n" . $message;
	}

	function br(Int $total=1)
	{
		for($i=0;$i<$total;$i++)
		{
			echo "\n";
		}
	}

	function copyFileToSfApp($sourcePath, $newPath, $delete=false)
	{
		writeln("Copying '" . $sourcePath . "' file ...");

		if(file_exists($newPath) && $delete)
		{
			// TODO: copiar los archivos originales en el directorio de la version
			unlink($newPath);
		}

		$copyResult = copy($sourcePath, $newPath);

		if($copyResult)
		{
			writeln("File '" . $sourcePath . "' has been copied correctly.");
		}
		else
		{
			writeln("Error copying '" . $sourcePath . "' file.");
		}
	}