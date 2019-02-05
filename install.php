<?php

	writeln("###################################");
	writeln("## Reaccion CMS Bundle Installer ##");
	writeln("###################################");
	br(2);

	$ymlSpace = "    ";
	$excludeFolders = ['.', '..', '.git'];
	$files = scandir(".");
	$availableVersions = [];

	foreach($files as $file)
	{
		if( ! is_dir($file) || in_array($file, $excludeFolders) || ! is_numeric($file) ) continue;
		$availableVersions[] = $file;
	}

	// Get bundle version
	do
	{
		$bundleVersion = readline("Enter which Reaccion CMS version you want to install: ");
		$bundleVersionIsAvailable = in_array($bundleVersion, $availableVersions);

		if( ! $bundleVersionIsAvailable)
		{
			// list available versions
			writeln("Bundle version '" . $bundleVersion .  "' is not available.");
			writeln("Available versions are:\n");

			foreach($availableVersions as $version)
			{
				writeln("\t- " . $version);
			}

			br(2);
		}
	} 
	while( ! $bundleVersionIsAvailable );

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

	// Copy 'fos_user.yaml' file
	copyFileToSfApp($bundleVersion, '/config/packages/fos_user.yaml', 'config/packages/fos_user.yaml', true);

	// Copy 'framework.yaml' file
	copyFileToSfApp($bundleVersion, '/config/packages/framework.yaml', 'config/packages/framework.yaml', true);

	// Copy 'package.json' file
	copyFileToSfApp($bundleVersion, 'package.json', 'package.json', true);

	// Copy 'webpack.config.js'
	if($installAdminPanel)
	{
		copyFileToSfApp($bundleVersion, "/assets/js/app.js", "assets/js/app.js", true);
		copyFileToSfApp($bundleVersion, "/webpack.config.js_with_panel", "webpack.config.js", true);
	}
	else
	{
		copyFileToSfApp($bundleVersion, "/webpack.config.js", "webpack.config.js", true);
	}

	// assets
	copyFileToSfApp($bundleVersion, "/assets/js/front_app.js", "assets/js/front_app.js", true);

	// security.yml
	copyFileToSfApp($bundleVersion, "/config/packages/security.yaml", "config/packages/security.yaml", true);

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

	function copyFileToSfApp($bundleVersion, $sourcePath, $newPath, $delete=false)
	{
		writeln("Copying '" . $sourcePath . "' file ...");

		if(file_exists($sourcePath) && $delete)
		{
			unlink($sourcePath);
		}

		$copyResult = copy($bundleVersion . "/" . $sourcePath, $newPath);

		if($copyResult)
		{
			writeln("File '" . $sourcePath . "' has been copied correctly.");
		}
		else
		{
			writeln("Error copying '" . $sourcePath . "' file.");
		}
	}