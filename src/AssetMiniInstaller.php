<?php namespace Gears\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class AssetMiniInstaller extends LibraryInstaller
{
	public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
	{
		// Run the parent installer
		parent::install($repo, $package);
		
		// Output some debug info
		echo "\n\n".'INSTALLING'."\n\n";
		print_r($repo);
		print_r($package);
	}
	
	public function supports($packageType)
	{
		return (bool)('gears-assetmini' === $packageType);
	}
}