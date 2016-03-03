/* ==========================================================================
 * Example JS component file
 * ========================================================================== */

;(function (window, document, undefined){
  "use strict";

  /**
   * Create our component
   *
   * @type {wishlist}
   */
  window.wishlist  = new function(){
    /**
     * The component scope.
     * Used within functions instead of `this`
     *
     * @type {window.wishlist}
     */
    var lib = this;

    /**
     * Component namespace.
     * Used for things like data attributes for event handlers.
     * It's public (using `this`) so we can use it from other components:
     * `console.log('The example namespace is: ', app.example.namespace);`
     *
     * @type {string}
     */
    this.namespace  = 'wishlist';

    /**
     * List of current wishlist items
     * @type {Array}
     */
    this.items = [];


    /**
     * updates form buttons
     *
     * @param {string} ref
     * @param {boolean} add
       */
    this.updateButtons = function(ref, add){
      if(ref){
        // we have an updated reference - check if adding or removing
        if(add){
          // add the reference to the list
          lib.items.push(ref);
        }else{
          // remove the reference from the list
          var index = lib.items.indexOf(ref);

          if(index >= 0){
            lib.items.splice(index, 1);
          }
        }
      }


      // now show/hide the relevant form buttons
      $('[data-' + lib.namespace + '-item-form]').each(function(i, elm){
        var $container  = $(elm),
            ref         = $container.find(':input[name="item"]').val(),
            $addForm    = $container.find('form[data-request="wishlist::onAddItem"]'),
            $removeForm = $container.find('form[data-request="wishlist::onRemoveItem"]');

        // check if the item is in the wishlist
        if(lib.items && ($.inArray(ref, lib.items) >= 0)){
          // item exists in the list
          // hide the 'add' button
          $addForm.hide();
          // show the 'remove' button
          $removeForm.show();
        }else{
          // item is not in the list
          // show the 'add' button
          $addForm.show();
          // hide the 'remove' button
          $removeForm.hide();
        }
      });
    };

    this.updateButtons();
  };
}(window, window.document));
