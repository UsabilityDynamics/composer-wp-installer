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
		
		// Do we have a custom path to install to
		if ($this->composer->getPackage())
		{
			$extra = $this->composer->getPackage()->getExtra();
			if (!empty($extra['assetmini-dir']))
			{
				$this->copySkelToAssets($extra['assetmini-dir']);
				return;
			}
		}
		
		// Otherwise just put it in the root
		$this->copySkelToAssets('assets/');
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
		
		// Update any of our files that we find with our newer versions
		if ($this->composer->getPackage())
		{
			$extra = $this->composer->getPackage()->getExtra();
			if (!empty($extra['assetmini-dir']))
			{
				$this->updateFile('min.php', $extra['assetmini-dir'].'/min.php');
				$this->updateFile('.htaccess', $extra['assetmini-dir'].'/.htaccess');
				return;
			}
		}
		
		// Otherwise just look in the root
		$this->updateFile('min.php', 'assets/min.php');
		$this->updateFile('.htaccess', 'assets/.htaccess');
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
	 * Method: copySkelToAssets
	 * =========================================================================
	 * This will check to see if the assets_dir exists and if not we will
	 * create it and copy in the skel dir from the `gears\assetmini` package.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $assets_dir - This is where we are going to copy the skel dir to
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	private function copySkelToAssets($assets_dir)
	{
		// Check for an existing assets dir
		if (!file_exists($assets_dir) && !is_dir($assets_dir))
		{
			// Create the assets dir (if it does not already exist)
			mkdir($assets_dir, 0777, true);
			
			// Copy in the AssetMini skeleton
			$this->copyr($this->getSkelDir(), $assets_dir);
		}
	}
	
	/**
	 * Method: updateFile
	 * =========================================================================
	 * This checks to see if $file_dst exists and that it is one of files.
	 * We do this by confirming our code signature <brad @="bjc.id.au" />
	 * exists in the file. Once we are sure that it is a file that this
	 * package created we overwrite it with a new version from the skel dir.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $file_src - This is the file that exists in the skel dir.
	 * $file_dst - This is the file we are going to try and update.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	private function updateFile($file_src, $file_dst)
	{
		// Does our file exist
		if (file_exists($file_dst))
		{
			// Make sure it's ours
			if (strpos(file_get_contents($file_dst), '<brad @="bjc.id.au" />'))
			{
				// Okay it's safe to update it
				unlink($file_dst);
				copy($this->getSkelDir().'/'.$file_src, $file_dst);
			}
		}
	}
	
	/**
	 * Method: getSkelDir
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
	 * The path to our skeleton dir
	 */
	private function getSkelDir()
	{
		// Return the path to our assetmini skel dir
		return $this->vendorDir.'/gears/assetmini/skel';
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