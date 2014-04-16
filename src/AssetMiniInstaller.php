<?php namespace Gears\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

class AssetMiniInstaller extends LibraryInstaller
{
	public function install()
	{
		echo "\n\n".'INSTALLING'."\n\n";
	}
	
	public function supports($packageType)
	{
		return (bool)('gears-assetmini' === $packageType);
	}
}