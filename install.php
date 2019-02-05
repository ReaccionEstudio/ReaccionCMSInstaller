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

	// Update 'fos_user.yaml' file
	writeln("Copying fos_user.yaml config file ...");

	$fosUserFilePath 	= $bundleVersion . "/config/packages/fos_user.yaml";
	$fosUserFileContent = file_get_contents($fosUserFilePath);
	$fosUserFileContent = str_replace("%emailAddress%", $senderEmailAddr, $fosUserFileContent);

	$result = file_put_contents("config/packages/fos_user.yaml", $fosUserFileContent);

	if($result)
	{
		writeln("fos_user.yaml config file has been copied correctly.");
	}
	else
	{
		writeln("Error copying fos_user.yaml config file.");
	}

	// Update 'config/packages/framework.yaml' file
	writeln("Checking framework.yaml config file ...");
	$sfFrameworkFilePath = "config/packages/framework.yaml";

	$file = fopen($sfFrameworkFilePath, "r");

	if($file)
	{
		// read symfony project 'config/packages/framework.yaml' file
		$fileHasBeenModified = false;
		$newFileContent = "";
		$frameworkLines = [];
		$addLines = [
			'cache:' => '',
			'templating:' => "\tengines: ['twig', 'php']"
		];

		while(!feof($file))
	  	{
	  		$line = fgets($file);

	  		$frameworkLines[] = $line;
	  		$newFileContent .= $line;
	  	}

		fclose($file);

		// add requried lines
		foreach($addLines as $configKey => $configValue)
		{
			if( ! in_array($configKey, $frameworkLines))
			{
				$fileHasBeenModified = true;

				$newFileContent .= "\n";
				$newFileContent .= "\n" . $ymlSpace . $configKey;

				if( empty($configValue)) continue;

				$newFileContent .= "\n" . $ymlSpace . $configValue;
			}
		}

		// save modified file
		$saveResult = false;

		if($fileHasBeenModified == true)
		{
			writeln("Updating framework.yaml config file ...");
			$saveResult = file_put_contents($sfFrameworkFilePath, $newFileContent);
		}	

		if($saveResult)
		{
			writeln("framework.yaml config file has been saved correctly.");
		}
		else
		{
			writeln("Error saving framework.yaml config.");
		}
	}
	else
	{
		writeln("Unable to open '" . $sfFrameworkFilePath . "' file! " . $e->getMessage());
	}

	// Copy 'package.json' file
	writeln("Copying package.json file ...");

	if(file_exists('package.json'))
	{
		unlink('package.json');
	}

	$copyResult = copy($bundleVersion . "/package.json", 'package.json');

	if($copyResult)
	{
		writeln("File 'package.json' has been copied correctly ...");
	}
	else
	{
		writeln("Error copying package.json file");
	}

	// Copy 'webpack.config.js'
	if(file_exists('webpack.config.js'))
	{
		unlink('webpack.config.js');
	}

	if($installAdminPanel)
	{
		$webpackCopyResult = copy($bundleVersion . "/webpack.config.js_with_panel", "webpack.config.js");
	}
	else
	{
		$webpackCopyResult = copy($bundleVersion . "/webpack.config.js", "webpack.config.js");
	}

	if($webpackCopyResult)
	{
		writeln("File 'webpack.config.js' has been copied correctly.");
	}
	else
	{
		writeln("Error copying 'webpack.config.js' file.");
	}

	

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