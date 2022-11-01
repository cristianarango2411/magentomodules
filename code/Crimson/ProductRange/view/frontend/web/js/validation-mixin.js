
define(['jquery'], function($) {
    'use strict';
  
    return function(targetWidget) {
      $.validator.addMethod(
        'validate-five-x',
        function(value, element) {
            var lowRange = $('#lowRange').val();
            return parseFloat(lowRange)*5 >= parseFloat(value);
        },
        $.mage.__("the highRange can't be 5x grater than lowRange")
      )
      return targetWidget;
    }
  });