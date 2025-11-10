jsFramework.lib.core.utils.registerNamespace('Remember.Prayerrequest.ui')
Remember.Prayerrequest.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
    .extend({
        init: function(settings) {
            this._super(settings) // call base init
        },

        _InitializePageBuilder: function() {
            var __this = this
            __this._configureEvents()
        },

        _configureEvents: function() {
            var __this = this
            __this._basicEvents()
            __this._ajaxEvents()
        },

        _ajaxEvents: function() {

        },

        _basicEvents: function() {
            var __this = this
            __this._onClickEvents()
            __this._onChangeEvents()
            __this._onLoadEvents()
            __this._onKeyEvents()
        },

        _onClickEvents: function() {
            var __this = this

            $(document).on('click', '#btnSavePrayerEmail', function() {
                var data = $('#txtprayeremail').val();
                var testEmail = /^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$/;
                if (testEmail.test(data)) {
                    swal({
                            title: 'Are you sure?',
                            text: 'Do you want to add prayer request email(s)',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonClass: 'btn-danger',
                            confirmButtonText: 'Yes',
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        },
                        function() {
                            $.ajax({
                                url: $('#homeUrl').val() + $('#btnSavePrayerEmail').attr('url'),
                                type: "POST",
                                dataType: 'json',
                                async: true,
                                data: {
                                    data: data
                                },
                                success: function(result) {
                                    $(".overlay").hide();
                                    swal({
                                            title: 'Success',
                                            text: 'Prayer request email added successfully',
                                            type: 'success'
                                        },
                                        function() {
                                            location.reload()
                                        })
                                },
                                error: function(er) {
                                     swal({
                                        title: 'Failed',
                                        text: 'Something went wrong.Please try again.',
                                        type: 'error'
                                        })
                                },
                            });
                        })
                } else {
                    swal({
                            title: 'Failed',
                            text: 'Please Enter A Valid Email Address',
                            type: 'error'
                    })
                }
            });
            $(document).on('click', '#prayerrequestsettingsli', function() {
                $('#prayersettings').show();
                $('#prayers').hide();
                $('#accordion').hide();
            });
            $(document).on('click', '#prayerrequestli', function() {
                $('#prayersettings').hide();
                $('#prayers').show();
                $('#accordion').show();
            });
            $(document).on('click', '.prayermailidclass', function() {
                var prid = $(this).attr('prid')
                var data = $('#reply_' + prid).val();
                var email = $('#emailid_' + prid).val();
                if (data == '') {
                    $('#ErrorMessageLabel').show();
                    $('#ErrorEmailLabel').hide();
                } else if (email == '') {
                    $('#ErrorEmailLabel').show();
                    $('#ErrorMessageLabel').hide();
                } else {
                    var testEmail = /^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$/;
                    if (testEmail.test(email)) {
                        $('#ErrorEmailvalid').hide();
                        $.ajax({
                            url: $('#homeUrl').val() + $('#prayermailid').attr('url'),
                            type: "POST",
                            dataType: 'json',
                            async: true,
                            data: {
                                prid: $('#prayermailid').attr('prid'),
                                userid: $('#prayermailid').attr('userid'),
                                emailid: email,
                                data: data
                            },
                            success: function(res) {

                                 if (typeof(res) !== 'undefined' && res.status === 'success') {
                                    swal({
                                            title: 'Success',
                                            text: 'Response successfully sent',
                                            type: 'success'
                                        },
                                        function() {
                                            window.location.href = $('#homeUrl').val() + $('#prayermailid').attr('reload')
                                        })
                                } else {
                                    swal({
                                            title: '',
                                            text: 'Sending Failed',
                                            type: 'error'
                                        },
                                        function() {
                                            location.reload()
                                        })
                                }  
                            },
                            error: function(er) {
                                swal({
                                    title: '',
                                    text: 'Sending Failed.',
                                    type: 'error'
                                }),
                                function() {
                                     window.location.href = $('#homeUrl').val() + $('#prayermailid').attr('reload')
                                }
                            },
                        });
                    } else {
                        $('#ErrorEmailvalid').show();
                    }
                }
            });
        },
        _onChangeEvents: function() {
            var __this = this;



        },
        _onLoadEvents: function() {
            var __this = this



        },
        _onKeyEvents: function() {
            var __this = this
        },

        // public members
        buildPage: function() {
            this._InitializePageBuilder()
        }
    })
var PrayerrequestJS = new Remember.Prayerrequest.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function() {
    jsFramework.lib.ui.pageBinder.addPageBuilder(PrayerrequestJS)
})