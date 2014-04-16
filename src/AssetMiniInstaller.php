<?php namespace Gears\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class AssetMiniInstaller extends LibraryInstaller
{
	public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
	{
		parent::install($repo, $package);
		echo "\n\n".'INSTALLING'."\n\n";
	}
	
	public function supports($packageType)
	{
		return (bool)('gears-assetmini' === $packageType);
	}
}