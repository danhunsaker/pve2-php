/**
* @author Frank Linehan
* @date 9/24/2013
*/

/**
* A wrapper class for local storage
*
* @class StoreLocal
*/

function StoreLocal () {
  /**
  * Returns true if local storage is supported by the browser
  *
  * @method supportStorage
  * @return {Bool} 
  */
  function supportStorage() {
    try {
      return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
      return false;
    }
  }

  /**
  * Saves a value to local storage
  *
  * @method save
  * @param {String} a name that is the key in local storage
  * @param {Object} a value that is mapped to the key and saved in local storage
  * @return {Bool} true or false depending on success
  */
  this['save'] = function (name, data) {
    if (!supportStorage()) {return false;}
    try{
      localStorage[name] = JSON.stringify(data);
      return true;
    } catch (e) {
      // console.log('Error saving local [' + name + '] to [' + JSON.stringify(data) + ']: ' + e);
      return false;
    }
  };

  /**
  * Fetches a value from local storage
  *
  * @method get
  * @param {String} the key of a value in local storage
  * @return {Object} the item from local storage
  */
  this['get'] = function (name) {
    if (!supportStorage()) {return false;}
    try{
      return JSON.parse(localStorage[name]);
    } catch (e) {
      // console.log('Error getting local [' + name + ']: ' + e);
      return false;
    }
  };

  /**
  * Deletes a value from local storage
  *
  * @method delete
  * @param {String} the key of the value to delete
  * @return {Bool} true or false depending on success
  */
  this['delete'] = function (name) {
    if (!supportStorage()) {return false;}
    try{
      localStorage.reremoveItem(name);
      return true;
    } catch (e) {
      // console.log('Error deleting local [' + name + ']: ' + e);
      return false;
    }
  };

 /**
  * Clears all values from local storage
  * 
  * DANGER: THIS CLEARS *ALL* VALUES!!!!!!!!!!!!!
  * 
  * @method clear
  * @return {Bool} true or false depending on success
  */
  this['clear'] = function () {
    if (!supportStorage()) {return false;}
    try{
      localStorage.clear();
      return true;
    } catch (e) {
      // console.log('Error clearing local storage: ' + e);
      return false;
    }
  };
}