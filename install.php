<?php

	echo "\n###################################\n";
	echo "## Reaccion CMS Bundle Installer ##\n";
	echo "###################################\n\n";

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
			echo "\n Bundle version '" . $bundleVersion .  "' is not available.";
			echo "\n Available versions are:\n";

			foreach($availableVersions as $version)
			{
				echo "\n\t- " . $version;
			}

			echo "\n\n";
		}
	} 
	while( ! $bundleVersionIsAvailable );

	// Get email address
	$senderEmailAddr = readline("Enter app sender email address: ");
	$senderEmailAddr = ( ! empty($senderEmailAddr)) ? $senderEmailAddr : "email@host.com";

	// Update 'fos_user.yaml' file
	echo "\nCopying fos_user.yaml config file ...";

	$fosUserFilePath 	= $bundleVersion . "/config/packages/fos_user.yaml";
	$fosUserFileContent = file_get_contents($fosUserFilePath);
	$fosUserFileContent = str_replace("%emailAddress%", $senderEmailAddr, $fosUserFileContent);

	$result = file_put_contents("config/packages/fos_user.yaml", $fosUserFileContent);

	if($result)
	{
		echo "\nfos_user.yaml config file has been copied correctly.";
	}
	else
	{
		echo "\nError copying fos_user.yaml config file.";
	}

	// Update 'config/packages/framework.yaml' file
	echo "\nChecking framework.yaml config file ...";
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
			echo "\nUpdating framework.yaml config file ...";
			$saveResult = file_put_contents($sfFrameworkFilePath, $newFileContent);
		}	

		if($saveResult)
		{
			echo "\nframework.yaml config file has been saved correctly.";
		}
		else
		{
			echo "\nError saving framework.yaml config.";
		}
	}
	else
	{
		echo "\nUnable to open '" . $sfFrameworkFilePath . "' file! " . $e->getMessage();
	}

	echo "\n";

