(function ($) {
    $.get(url.validation, function(validation) {
        if (validation.status == 'invalid') {
            displayValidatorErrors(validation.violations);
        } else {
            displayValidationSuccess();
        }
    });

    function displayValidatorErrors(items){
        var iconMap = {
            'error': 'ban-circle',
            'warning': 'exclamation-sign',
            'notice': 'info-sign'
        };

        $('#validator').addClass('error-validation').html('<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Validation errors <span class="badge counter">' + items.length + '</span><span class="caret"></a><ul id="validation-list" class="dropdown-menu scrollable-menu"></ul>');

        items.forEach(function(item) {
            $('#validation-list').append(
                '<li class="validation-item" data-node-id="'+ item.nodeId + '">' +
                '<span class="glyphicon glyphicon-' + iconMap[item.severity] + '" aria-hidden="true"></span>' +
                item.message + '</li>'
            );
        });
    }

    function displayValidationSuccess()
    {
        $('#validator').addClass('success-validation').html('<a href="#">Validation errors <span class="badge counter">0</span></a>');
    }

}(jQuery));