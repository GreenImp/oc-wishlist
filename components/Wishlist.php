<?php namespace GreenImp\Wishlist\Components;

use Cms\Classes\ComponentBase;
use ApplicationException;

class Wishlist extends ComponentBase
{
  protected $wishlist;
  public $isButton  = false;

  public function componentDetails()
  {
    return [
      'name'        => 'Wishlist',
      'description' => 'Implements a simple/generic wishlist.'
    ];
  }

  public function defineProperties()
  {
    return [
      'max' => [
        'description'       => 'The most amount of items allowed',
        'title'             => 'Max items',
        'default'           => 0,
        'type'              => 'string',
        'validationPattern' => '^[0-9]*$',
        'validationMessage' => 'The Max Items value should be integer.'
      ],
      'isButton'  => [
        'default' => false,
        'type'  => 'checkbox'
      ]
    ];
  }

  public function init(){
    // initialise the Wishlist handler
    $this->wishlist = \GreenImp\Wishlist\Classes\Wishlist::getInstance($this->property('reference'), $this->property('max'));
  }

  public function onRun(){
    $this->addJs('assets/js/wishlist.js');
  }

  public function itemsRaw(){
    return $this->wishlist->getItems();
  }

  /**
   * Returns a list of the current wishlist items
   *
   * @return array
   */
  public function items(){
    return $this->wishlist->getItemObjects();
  }

  public function itemCount(){
    return $this->wishlist->getItemsCount();
  }

  /**
   * Preps the given item for use in a form
   *
   * @param $item
   * @return mixed
   */
  public function prep($item){
    return $this->wishlist->serialize($item);
  }

  public function onAddItem()
  {
    $item = post('item');

    if(!empty($item)){
      $this->wishlist->addItem($item);
    }
  }

  public function onRemoveItem(){
    $this->wishlist->removeItem(post('item'));
  }
}
