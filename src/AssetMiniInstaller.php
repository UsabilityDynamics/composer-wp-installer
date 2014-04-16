<?php
////////////////////////////////////////////////////////////////////////////////
// __________ __             ________                   __________              
// \______   \  |__ ______  /  _____/  ____ _____ ______\______   \ _______  ___
//  |     ___/  |  \\____ \/   \  ____/ __ \\__  \\_  __ \    |  _//  _ \  \/  /
//  |    |   |   Y  \  |_> >    \_\  \  ___/ / __ \|  | \/    |   (  <_> >    < 
//  |____|   |___|  /   __/ \______  /\___  >____  /__|  |______  /\____/__/\_ \
//                \/|__|           \/     \/     \/             \/            \/
// -----------------------------------------------------------------------------
//          Designed and Developed by Brad Jones <brad @="bjc.id.au" />         
// -----------------------------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////

namespace Gears\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class AssetMiniInstaller extends LibraryInstaller
{
	/**
	 * Method: install
	 * =========================================================================
	 * This method over loads the parent install method.
	 * We still install assetmini like a normal library.
	 * 
	 * After the parent install method has run we come along and add
	 * the assets dir if it doesn't already exist along with the skel dir.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $repo -
	 * $package - 
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
	{
		// Run the parent installer
		parent::install($repo, $package);
		
		// Create the skel dir path
		$this->skelDir();
		
		// Check for an existing assets dir
		if (!file_exists('assets/') && !is_dir('assets/'))
		{
			// Create the assets dir (if it does not already exist)
			mkdir('assets/');
			
			// Copy in the AssetMini skeleton
			$this->copyr($this->skel, 'assets/');
		}
	}
	
	/**
	 * Method: update
	 * =========================================================================
	 * This method over loads the parent update method.
	 * We still update assetmini like a normal library.
	 * 
	 * After the parent update method has run we come along and update the
	 * min.php and .htaccess files if they exist
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $repo - InstalledRepositoryInterface
	 * $package - PackageInterface
	 * $target - PackageInterface
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
	{
		// Run the parent installer
		parent::update($repo, $initial, $target);
		
		// Create the skel dir path
		$this->skelDir();
		
		// Does our min.php exist in the assets folder
		if (file_exists('assets/min.php'))
		{
			// Make sure it's ours
			$file = file_get_contents('assets/min.php');
			if (strpos($file, '<brad @="bjc.id.au" />'))
			{
				// Okay it's safe to update it
				unlink('assets/min.php');
				copy($this->skel.'/min.php', 'assets/min.php');
			}
		}
		
		// Does our .htaccess exist in the assets folder
		if (file_exists('assets/.htaccess'))
		{
			// Make sure it's ours
			$file = file_get_contents('assets/.htaccess');
			if (strpos($file, '<brad @="bjc.id.au" />'))
			{
				// Okay it's safe to update it
				unlink('assets/.htaccess');
				copy($this->skel.'/min.php', 'assets/.htaccess');
			}
		}
	}
	
	/**
	 * Method: supports
	 * =========================================================================
	 * This is simply saying that we will install
	 * a package of type `gears-assetmini`
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $packageType - The package type
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * boolean
	 */
	public function supports($packageType)
	{
		return (bool)('gears-assetmini' === $packageType);
	}
	
	/**
	 * Method: skelDir
	 * =========================================================================
	 * This is just a little helper to point us to the
	 * skel dir in the `gears\assetmini` package.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	private function skelDir()
	{
		// Create the path to our assetmini skel dir
		$this->skel = $this->vendorDir.'/gears/assetmini/skel';
	}
	
	/**
	 * Method: copyr
	 * =========================================================================
	 * A simple recursive copy function.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $src - The location to the source files and folders to copy
	 * $dst - The destination to the copy the source to.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	private function copyr($src, $dst)
	{
		// Get a list of all the src files
		$iterator = new \RecursiveIteratorIterator
		(
			new \RecursiveDirectoryIterator
			(
				$src,
				\RecursiveDirectoryIterator::SKIP_DOTS
			),
			\RecursiveIteratorIterator::SELF_FIRST
		);
		
		// Loop through each file
		foreach ($iterator as $item)
		{
			// Is it a dir, if so create it
			if ($item->isDir())
			{
				mkdir($dst.DIRECTORY_SEPARATOR.$iterator->getSubPathName());
			}
			else
			{
				copy($item, $dst.DIRECTORY_SEPARATOR.$iterator->getSubPathName());
			}
		}
	}
}