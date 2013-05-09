App = {
    init: function() {
        $('.js').each(function(i, el) {
            var element = $(el);
            element.on(element.data('event'), function(event) {
                var f = new Function('', 'return ' + element.data('handler') + '();');
                f()(event);
            });
        });

        this.Login.init();
    }
};

App.Login = {
    init: function() {
    },
    dialog: function() {
        return function (event) {
            event.preventDefault();
            var element = $(event.target);
            $(element.data('content')).modal('show');
        }
    }
};

$(function() {
    App.init();
});
