//This was found here: http://stackoverflow.com/questions/642136/jeditable-with-jquery-ui-datepicker
$.editable.addInputType('datepicker', {
    element : function(settings, original) {
        var input = $('<input>');
        if (settings.width  != 'none') { input.width(settings.width);  }
        if (settings.height != 'none') { input.height(settings.height); }
        input.attr('autocomplete','off');
        $(this).append(input);
        return(input);
    },
    plugin : function(settings, original) {
        /* Workaround for missing parentNode in IE */
        var form = this;
        settings.onblur = 'ignore';
        $(this).find('input').datepicker({
			dateFormat: 'D, M d',
			onSelect: function(dateText) { $(this).hide(); $(form).trigger("submit"); }
		}).bind('click', function() {
                $(this).datepicker('show');
            return false;
        }).bind('dateSelected', function(e, selectedDate, $td) {
            $(form).submit();
        });
    }
});