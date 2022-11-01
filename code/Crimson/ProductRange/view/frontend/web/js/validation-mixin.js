
define(['jquery'], function($) {
    'use strict';
  
    return function(targetWidget) {
      $.validator.addMethod(
        'validate-five-x',
        function(value, element) {
          return parseInt(value)*10 > 10;
        },
        $.mage.__("the highRange can't be 5x grater than lowRange")
      )
      return targetWidget;
    }
  });