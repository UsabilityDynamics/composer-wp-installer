<?php
namespace UsabilityDynamics\ComposerWpInstaller;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Package\CompletePackage;
use Composer\Repository\InstalledRepositoryInterface;

class Package extends CompletePackage
{

  /** These are public variables that won't be overridden by the parent (ever) */
  public $protected = array(
    'name',
    'prettyName',
    'id',
    'version',
    'prettyVersion',
    'replaces',
    'repository',
    'requires',
    'releaseDate'
  );

  /** Ok, this function will clone everything from the parent that is not set in the child */
  function loadFromParent( $parent ){
    /** Ok, loop through each of the parent's attributes, and set them if they're not in public */
    $parent_vars = get_object_vars( $parent );
    foreach( $parent_vars as $key => $value ){
      if( !in_array( $key, $this->protected ) ){
        $this->{$key} = $value;
      }
    }
    /** Return self */
    return $this;
  }

}
