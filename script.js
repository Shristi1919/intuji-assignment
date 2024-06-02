
// for start and end date validation and datepicker
$(function() {
    $('#start').datetimepicker({
        format: 'Y-m-d\\TH:i:sP',  // ISO 8601 format
        step: 30  // step for minutes, adjust as needed
    });
    $('#end').datetimepicker({
        format: 'Y-m-d\\TH:i:sP',  
        step: 30  
    });

    $('form').on('submit', function(event) {
        var isValid = true;
        $('.error-message').remove();

        if (!$('#summary').val()) {
            isValid = false;
            $('<div class="error-message">Summary is required.</div>').insertAfter('#summary');
        }

        if (!$('#start').val()) {
            isValid = false;
            $('<div class="error-message">Start date is required.</div>').insertAfter('#start');
        }
        
        if (!$('#end').val()) {
            isValid = false;
            $('<div class="error-message">End date is required.</div>').insertAfter('#end');
        }

        if (!isValid) {
            event.preventDefault();
        }
    });
});






