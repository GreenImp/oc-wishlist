<?php namespace GreenImp\Wishlist\Classes;

use Session;

class Wishlist{
  protected static $instances  = [];

  protected $reference;
  protected $max = 0;

  protected function __construct($reference = 'wishlist', $max = null){
    $this->reference  = $reference;
    $this->max = (is_numeric($max) && ($max > 0)) ? intval($max) : 0;

    if(!Session::has('wishlist.' . $this->reference . '.items')){
      // wishlist doesn't exists - create it
      Session::put('wishlist.' . $this->reference . '.items', []);
    }
  }

  public static function getInstance($reference = 'wishlist', $max = null){
    $instance = null;

    if(isset(self::$instances[$reference])){
      $instance = &self::$instances[$reference];

      if(is_numeric($max) && ($max >= 0)){
        $instance->max  = $max;
      }
    }else{
      self::$instances[$reference]  = new self($reference, $max);

      $instance = &self::$instances[$reference];
    }

    return $instance;
  }

  /**
   * Returns a list of the current wishlist items
   *
   * @return array
   */
  public function getItems(){
    return Session::get('wishlist.' . $this->reference . '.items', []);
  }

  public function getItemObjects(){
    $items = $this->getItems();

    // loop through and load the objects
    foreach($items as &$item){
      if(!is_array($item)){
        list($type, $id) = explode(':', $item);

        // build the item
        $item = [
          'type' => $type,
          'id'   => $id
        ];
      }else{
        $type = $item['type'];
      }

      // get the item's model
      $model = class_exists($type) ? $type::find($item['id']) : null;

      if(!is_null($model)){
        $item['title']  = isset($model->title) ? $model->title : (isset($model->name) ? $model->name : 'N/A');
        $item['url']    = method_exists($model, 'url') ? $model->url() : (isset($model->url) ? $model->url : '#');
      }
    }

    return $items;
  }

  /**
   * Sets the items for the wishlist, replacing any existing items
   *
   * @param array $items
   * @throws ApplicationException
   */
  public function setItems(array $items){
    if($this->canFitItems($items)){
      // loop through and add the new items to the list
      $itemList = [];
      foreach($items as $item){
        // TODO - run item validation
        // only add item if it doesn't already exist
        if(!in_array($item, $itemList)){
          $itemList[] = $item;
        }
      }

      // store the new items
      Session::put('wishlist.' . $this->reference . '.items', $itemList);
    }else{
      throw new ApplicationException(sprintf('Sorry only %s items are allowed.', $this->max));
    }
  }

  /**
   * Adds a list of items to the wishlist
   *
   * @param array $items
   */
  public function addItems(Array $items){
    // merge the new items into the existing
    $items  = array_merge($this->getItems(), $items);

    // save them
    $this->setItems($items);
  }

  /**
   * Add a single item to the wishlist
   *
   * @param $item
   */
  public function addItem($item){
    $this->addItems([$item]);
  }

  /**
   * Removes an item from the wishlist for the
   * provided key
   *
   * @param $key
   * @throws ApplicationException
   */
  public function removeItem($key){
    if(!is_null($key)){
      $items  = $this->getItems();

      $key = array_search($key, $items);
      if(false !== $key){
        unset($items[$key]);
      }

      $this->setItems($items);
    }
  }

  /**
   * Removes a list of items from the wishlist
   *
   * @param array $keys
   */
  public function removeItems(Array $keys){
    foreach($keys as $key){
      $this->removeItem($key);
    }
  }

  /**
   * Empties the wishlist
   */
  public function emptyList(){
    Session::put('wishlist.' . $this->reference . '.items', []);
  }

  /**
   * Returns the number of items currently in the list
   *
   * @return int
   */
  public function getItemsCount(){
    return count(self::getItems());
  }

  /**
   * Checks if there is enough space on the list
   * to add the given items.
   * This only appies if a `max` has been defined
   *
   * @param array $items
   * @return bool
   */
  public function canFitItems(Array $items){
    if($this->max > 0){
      // max defined - check if the item cout plus the new items will go over the limit
      return ($this->getItemsCount() + count($items)) <= $this->max;
    }else{
      // no max defined so items will always fit
      return true;
    }
  }

  public function serialize($var){
    return base64_encode(serialize($var));
  }

  public function unserialize($var){
    return unserialize(base64_decode($var));
  }
}
