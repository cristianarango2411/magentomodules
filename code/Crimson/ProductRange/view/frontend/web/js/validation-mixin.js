
define(['jquery'], function($) {
    'use strict';
  
    return function(targetWidget) {
      $.validator.addMethod(
        'validate-five-x',
        function(value, element) {
            var lowRange = $('#lowRange').val();
            return parseFloat(lowRange)*5 >= parseFloat(value);
        },
        $.mage.__("The highRange can't be 5x greater than lowRange")
      )
      return targetWidget;
    }
  });