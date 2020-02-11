$( document ).ready(function() {

    if (window.DevelopXTk)
        return;

    window.DevelopXTk= function () {
        this.initEvents();
        this.ajaxPath = '/bitrix/admin/developxtk_ajax.php';
    };

    window.DevelopXTk.prototype = {
        initEvents: function () {
            var $this = this;
            $('.getPointsJs').click(function () {
                var button = $(this);
                var action = button.data('action');
                var gObj = {
                    ACTION: action,
                    AJAX_CALL: 'Y'
                };
                $this.addLog('Загрузка пунктов ' + button.val() + ' - старт');
                $.get($this.ajaxPath, gObj).done(function (hData) {
                    $this.addLog(hData);
                    $this.addLog('Загрузка пунктов ' + button.val() + ' - конец');
                }).fail(function (jqxhr, textStatus, error) {
                    console.error(
                        "Request Failed: " + textStatus + ", " + error
                    );
                });
            });
            $('.clearCacheJs').click(function () {
                var button = $(this);
                var action = button.data('action');
                var gObj = {
                    ACTION: action,
                    AJAX_CALL: 'Y'
                };
                $.get($this.ajaxPath, gObj).done(function (hData) {
                    alert(hData);
                }).fail(function (jqxhr, textStatus, error) {
                    console.error(
                        "Request Failed: " + textStatus + ", " + error
                    );
                });
            });
        },
        addLog: function (text) {
            $('.logJs').prepend(text + '<br>');
        }
    }
});
