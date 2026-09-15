jsFramework.lib.core.utils.registerNamespace('Remember.whatsappSettings.ui')
Remember.whatsappSettings.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
.extend({
    init: function (settings) {
        this._super(settings) // call base init
    },

    _InitializePageBuilder: function () {
        var __this = this
        __this._configureEvents()
    },

    _configureEvents: function () {
        var __this = this
        __this._onClickEvents()
    },

    _onClickEvents: function () {

        $(document).on('click', '#btn-whatsapp-send-test', function () {
            var $btn = $(this)
            var testNumber = $('#whatsapp-test-number').val()
            var eventType = $('#whatsapp-test-event-type').val()

            if (!testNumber) {
                swal({ title: 'WhatsApp', text: 'Enter a recipient number first.', type: 'error' })
                return
            }

            $btn.attr('disabled', 'true')
            var ajaxUrl = $('#homeUrl').val() + $('#whatsapp-send-test-Url').val()
            $.post(ajaxUrl, {
                '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                testNumber: testNumber,
                eventType: eventType
            }, function (res) {
                $btn.removeAttr('disabled')
                swal({
                    title: 'WhatsApp',
                    text: (res && res.message) ? res.message : 'Unexpected response',
                    type: (res && res.status === 'success') ? 'success' : 'error'
                })
            }).fail(function () {
                $btn.removeAttr('disabled')
                swal({ title: 'WhatsApp', text: 'Request failed. Please try again.', type: 'error' })
            })
        })

        $(document).on('click', '#btn-whatsapp-send-now', function () {
            var $btn = $(this)
            $btn.attr('disabled', 'true')
            var ajaxUrl = $('#homeUrl').val() + $('#whatsapp-send-now-Url').val()
            $.post(ajaxUrl, {
                '_csrf-backend': $("meta[name='csrf-token']").attr('content')
            }, function (res) {
                $btn.removeAttr('disabled')
                if (res && res.status === 'success') {
                    var s = res.summary
                    swal({
                        title: 'WhatsApp',
                        text: 'Sent: ' + s.sent + ', Failed: ' + s.failed,
                        type: (s.failed > 0 && s.sent === 0) ? 'warning' : 'success'
                    })
                } else {
                    swal({ title: 'WhatsApp', text: (res && res.message) ? res.message : 'Unexpected response', type: 'error' })
                }
            }).fail(function () {
                $btn.removeAttr('disabled')
                swal({ title: 'WhatsApp', text: 'Request failed. Please try again.', type: 'error' })
            })
        })

        $(document).on('click', '.btn-whatsapp-delete-template', function () {
            var $btn = $(this)
            var id = $btn.data('id')
            var ajaxUrl = $('#homeUrl').val() + $('#whatsapp-delete-template-Url').val()
            $.post(ajaxUrl, {
                '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                id: id
            }, function (res) {
                if (res && res.status === 'success') {
                    $btn.closest('tr').remove()
                } else {
                    swal({ title: 'WhatsApp', text: (res && res.message) ? res.message : 'Failed to delete template.', type: 'error' })
                }
            })
        })

        $(document).on('click', '.btn-whatsapp-resend-message', function () {
            var $btn = $(this)
            var id = $btn.data('id')
            $btn.attr('disabled', 'true')
            var ajaxUrl = $('#homeUrl').val() + $('#whatsapp-resend-message-Url').val()
            $.post(ajaxUrl, {
                '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                id: id
            }, function (res) {
                $btn.removeAttr('disabled')
                swal({
                    title: 'WhatsApp',
                    text: (res && res.message) ? res.message : 'Unexpected response',
                    type: (res && res.status === 'success') ? 'success' : 'error'
                })
                if (res && res.status === 'success') {
                    setTimeout(function () { location.reload() }, 800)
                }
            }).fail(function () {
                $btn.removeAttr('disabled')
                swal({ title: 'WhatsApp', text: 'Request failed. Please try again.', type: 'error' })
            })
        })
    },

    // public members
    buildPage: function () {
        this._InitializePageBuilder()
    }

})
var WhatsappSettingsJs = new Remember.whatsappSettings.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
    jsFramework.lib.ui.pageBinder.addPageBuilder(WhatsappSettingsJs)
})
