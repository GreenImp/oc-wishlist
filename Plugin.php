<?php namespace GreenImp\Wishlist;

use Event;
use Backend\Facades\Backend;
use System\Classes\PluginBase;

/**
 * Wishlist Plugin Information File
 */
class Plugin extends PluginBase
{
  /**
   * Returns information about this plugin.
   *
   * @return array
   */
  public function pluginDetails(){
    return [
      'name'        => 'greenimp.wishlist::lang.app.name',
      'description' => 'greenimp.wishlist::lang.app.description',
      'author'      => 'GreenImp',
      'icon'        => 'icon-star'
    ];
  }

  public function registerComponents(){
    return [];
  }

  public function registerPermissions(){}

  public function registerNavigation(){
    return [];
  }
}
