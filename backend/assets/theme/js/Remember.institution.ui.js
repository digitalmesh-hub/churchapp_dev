jsFramework.lib.core.utils.registerNamespace('Remember.institution.ui')
Remember.institution.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
    .extend({
        init: function(settings) {
            this._super(settings) // call base init
        },

        _InitializePageBuilder: function() {
            var __this = this
            __this._configureEvents()
            __this._ConfigureDashBoard()
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
            $(document).on('click', '#btn-institution-deactivate', function() {
                var ajaxUrl = $('#homeUrl').val() + $('#institution-deactivate-Url').val()
                var id = $(this).attr('data-institution-id')
                swal({
                        title: 'Are you sure?',
                        text: 'Do you want to deactivate this institution',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'btn-danger',
                        confirmButtonText: 'Yes',
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    },
                    function() {
                        $.post(ajaxUrl, // Ajax Post URL
                            {
                                '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                                id: id
                            }, // Data
                            function(res) {
                                if (typeof(res) !== 'undefined' && res.status === 'success') {
                                    swal({
                                            title: 'Success',
                                            text: 'This institution has been deactivated',
                                            type: 'success'
                                        },
                                        function() {
                                            location.reload()
                                        })
                                } else {
                                    swal({
                                            title: 'Failed',
                                            text: 'Sorry! unable to complete the process',
                                            type: 'error'
                                        },
                                        function() {
                                            location.reload()
                                        })
                                }
                            })
                    })
            })

            $(document).on('click', '#btn-institution-activate', function() {
                var ajaxUrl = $('#homeUrl').val() + $('#institution-activate-Url').val()
                var id = $(this).attr('data-institution-id')
                swal({
                        title: 'Are you sure?',
                        text: 'Do you want to activate this institution',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'btn-danger',
                        confirmButtonText: 'Yes',
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    },
                    function() {
                        $.post(ajaxUrl, // Ajax Post URL
                            {
                                '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                                id: id
                            }, // Data
                            function(res) {
                                if (typeof(res) !== 'undefined' && res.status === 'success') {
                                    swal({
                                            title: 'Success',
                                            text: 'This institution has been activated',
                                            type: 'success'
                                        },
                                        function() {
                                            location.reload()
                                        }
                                    )
                                } else {
                                    swal({
                                            title: 'Failed',
                                            text: 'Sorry! unable to complete the process',
                                            type: 'error'
                                        }),
                                        function() {
                                            location.reload()
                                        }
                                }
                            })
                    })
            })

            $(document).on('click', '#save-dashboard-item', function(event) {
                event.preventDefault()
                var dashBoardSettingsList = new Array();
                $('.overlay').show()
                var institutionId = $("#hdnInstitutionId").val()
                $(".dbicon").each(function(index) {
                    var privilege = {
                        "institutionid": institutionId,
                        "dashboardid": $(this).children().find("img[id*='ImageURL']").attr('dashboardid'),
                        "sortorder": $(this).attr('order'),
                        "isactive": $(this).attr('activestatus'),
                    };
                    dashBoardSettingsList.push(privilege);
                });
                var ajaxUrl = $('#homeUrl').val() + $('#dashboard-item-url').val()

                $.post(ajaxUrl, // Ajax Post URL
                    {
                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                        'data': dashBoardSettingsList,
                        'institutionId': institutionId
                    }, // Data
                    function(result) {
                        if (result.hasError === false) {
                            swal({
                                    title: 'Success',
                                    text: 'Successfully saved dashboard items',
                                    type: 'success'
                                }),
                                function() {
                                    location.reload()
                                }
                        } else {
                            swal({
                                    title: 'Failed',
                                    text: 'Sorry! unable to complete the process',
                                    type: 'error'
                                }),
                                function() {
                                    location.reload()
                                }
                        }
                    })
            });

            //Save affiliated institution
            $(document).on('click', '.save-institution', function() {
                var phone1Code = $('#extendedinstitution-phone1_countrycode').val().trim();
                var phone1AreaCode = $('#extendedinstitution-phone1_areacode').val().trim();
                var phone1 = $('#extendedinstitution-phone1').val().trim();
                var phone2Code = $('#extendedinstitution-phone2_countrycode').val().trim();
                var phone2 = $('#extendedinstitution-phone2').val().trim();
                // var ruleCountryCode = new RegExp("^(\+(?:\d{2,3}$))|(\+(?:\d{1})$)|(?:\d{2,3}$)|(?:\d{1}$)');
                var ruleAreaCode = /^\d{0,5}$/;
                var rulePhone1 = /^\d{10}$/;
                var rulePhone2 = /^\d{7,10}$/;

                if (phone1AreaCode != '') {
                    if (!ruleAreaCode.test(phone1AreaCode)) {
                        $('.error-phone1').addClass('error-ms');
                        $('.error-phone1').show();
                        return false;
                    }
                } else if (phone1 != '') {
                    if (!rulePhone2.test(phone1)) {
                        $('.error-phone1').addClass('error-ms');
                        $('.error-phone1').show();
                        return false;
                    }
                } else if (phone2 != '') {
                    if (!rulePhone1.test(phone2)) {
                        $('.error-phone2').addClass('error-ms');
                        $('.error-phone2').show();
                        return false;
                    }
                } else {
                    $('.msg-div').hide();
                    return true;
                }
            });

        },
        _onChangeEvents: function() {
            var __this = this
            $('#checkbox-more').on('change', function() {
                if ($('#checkbox-more').is(':checked') === true) {
                    $('#more-text').css('display', 'block')
                } else {
                    $('#more-text').val('')
                    $('#more-text').css('display', 'none')
                }
            })
            $('#checkbox-demo').on('change', function () {
                if ($('#checkbox-demo').is(':checked') === true) {
                    $('#demo-div').css('display', 'block')
                    // Set DatePicker value to today + 1 month
                    var today = new Date();
                    today.setMonth(today.getMonth() + 1); // Adds 1 month
                    var day = today.getDate();
                    var day = (day < 10 ? '0' : '') + day;
                    var monthIndex = today.getMonth(); // 0-11 (0 = January, 1 = February, etc.)
                    var year = today.getFullYear();
                    var monthNames = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    var monthName = monthNames[monthIndex];

                    // Format the date as 'd F Y'
                    var formattedDate = day + ' ' + monthName + ' ' + year;
                    $('#create-demo-expiry').val(formattedDate);
                } else {
                    $('#demo-div').css('display', 'none')
                }
            })

            $('#institution-logo').on('change', function() {
                //EDdocument.getElementById('Imagefile').onchange = function (e) {
                _memberimage = "";
                // Get the first file in the FileList object
                var imageFile = this.files[0]
                var type = imageFile.type
                var defaultLogo = $('#defaultInsitutionLogo').val()
                if (imageFile.type != 'image/png' && imageFile.type != 'image/jpg' && imageFile.type != 'image/jpeg') {
                    swal("Unfortunately this image format is not supported.Please use png or jpeg");
                    if ($("#institutionImage").attr("src") != defaultLogo) {
                        institutionImage.src = defaultLogo
                    }
                    $("#institution-logo").replaceWith($("#institution-logo").clone())
                } else {
                    // get a local URL representation of the image blob
                    var url = window.URL.createObjectURL(imageFile)
                    // Now use your newly created URL!
                    institutionImage.src = url
                }

            })

            $(document).on("change", '#extendedinstitution-countryid', function() {
                if ($(this).val() != '') {
                    var inputData = {
                        "countryId": $(this).val(),
                    };
                    __this._loadCountryCode(inputData)
                }
            })

            //Phone number validation
//            $(document).on('keydown', '.number-check', function(e) {
//                // Allow: backspace, delete, tab, escape, enter and .
//                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
//                    // Allow: Ctrl+A, Command+A
//                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
//                    // Allow: home, end, left, right, down, up
//                    (e.keyCode >= 35 && e.keyCode <= 40)) {
//                    // let it happen, don't do anything
//                    return;
//                }
//                // Ensure that it is a number and stop the keypress
//                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
//                    e.preventDefault();
//                }
//            });
        },

        _loadCountryCode: function(inputData) {

            var ajaxUrl = $('#homeUrl').val() + $('#auto-country-code').val()
            $.post(ajaxUrl, // Ajax Post URL
                {
                    '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                    'inuptData': inputData
                }, // Data
                function(result) {
                    if (result.Success) {
                        $("#extendedinstitution-phone1_countrycode").val(result.telephoneCode)
                        // $("#extendedinstitution-phone1_areacode").val(result.telephoneCode)
                        $("#extendedinstitution-phone2_countrycode").val(result.telephoneCode)
                    }
                })

        },
        _onLoadEvents: function() {
            var __this = this
            $(document).ready(function() {
                if ($('#checkbox-more').is(':checked') === true) {
                    $('#more-text').css('display', 'block')
                } else {
                    $('#more-text').val('')
                    $('#more-text').css('display', 'none')
                }
                if ($('#checkbox-demo').is(':checked') === true) {
                    $('#demo-div').css('display', 'block')
                } else {
                    $('#demo-div').css('display', 'none')
                }
            })
            $(document).ready(function () {
                if ($("#extendedinstitution-countryid").val() != undefined && $("#extendedinstitution-countryid").val() != '') {
                    var inputData = {
                        "countryId": $("#extendedinstitution-countryid").val(),
                    };
                    __this._loadCountryCode(inputData)
                }
            })
            $(document).ready(function() {
                $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
                    localStorage.setItem('activeTab', $(e.target).attr('href'))
                })
                var activeTab = localStorage.getItem('activeTab')
                if (activeTab) {
                    $('#myTab a[href="' + activeTab + '"]').tab('show')
                }
            })
        },
        _onKeyEvents: function() {
            var __this = this
        },
        _ConfigureDashBoard: function() {
            var __this = this;
            $('.dbicon').on("click", function() {
                var id = $(this).attr('id');
                if ($(this).hasClass('iconspanel')) {
                    $(this).removeClass('iconspanel').addClass('iconspanel-disable').attr('activestatus', "0");
                    $('.dbstatus', this).removeClass('iconenable').addClass('icondisable').text('Disabled');
                    if ($("#contentinsidebox1").find(".dashenable").length <= 9) {
                        $('#mob_' + id).addClass('nodisplay');
                        $('#mob_' + id).removeClass('dashenable').addClass('dashdisable');
                        if ($("#contentinsidebox1").find(".dashenable").length == 9) {
                            $('#mob2_' + id).addClass('nodisplay');
                            $('#mob2_' + id).removeClass('dashenable').addClass('dashdisable');
                        }
                        if ($("#contentinsidebox1").find(".dashenable").length < 9) {
                            $('#mob_' + $('#contentinsidebox2 div.dashenable:first').attr('order')).removeClass('nodisplay').removeClass('dashdisable').addClass('dashenable');
                            $('#contentinsidebox2 div.dashenable:first').addClass('nodisplay').removeClass('dashenable').addClass('dashdisable');
                        }
                    } else {
                        $('#mob2_' + id).addClass('nodisplay');
                        $('#mob2_' + id).removeClass('dashenable').addClass('dashdisable');
                    }

                } else if ($(this).hasClass('iconspanel-disable')) {
                    $(this).removeClass('iconspanel-disable').addClass('iconspanel').attr('activestatus', "1");
                    $('.dbstatus', this).removeClass('icondisable').addClass('iconenable').text('Enabled');
                    if ($("#contentinsidebox1").find(".dashenable").length <= 9) {
                        $('#mob_' + id).removeClass('nodisplay');
                        $('#mob_' + id).removeClass('dashdisable').addClass('dashenable');
                        if ($("#contentinsidebox1").find(".dashenable").length > 9) {
                            $('#mob2_' + $('#contentinsidebox1 div.dashenable:last').attr('order')).removeClass('nodisplay').removeClass('dashdisable').addClass('dashenable');
                            $('#contentinsidebox1 div.dashenable:last').addClass('nodisplay').removeClass('dashenable').addClass('dashdisable');
                        }
                    } else {
                        $('#mob2_' + id).removeClass('nodisplay');
                        $('#mob2_' + id).removeClass('dashdisable').addClass('dashenable');
                    }

                }
            });
            $('.nexticon').on("click", function(e) {
                e.stopPropagation();
                var currentid = $(this).attr('currentid');
                var nextid = $(this).attr('nextid');
                var currentstatus = $('#' + currentid).attr('activestatus');
                var nxtstatus = $('#' + nextid).attr('activestatus');
                var currentlabel = $('#lab_' + currentid).text();
                var nxtlabel = $('#lab_' + nextid).text();
                var currentimage = $('#img_' + currentid).html();
                var nextimage = $('#img_' + nextid).html();
                $('#img_' + currentid).html(nextimage);
                $('#img_' + nextid).html(currentimage);
                $('#lab_' + currentid).text(nxtlabel);
                $('#lab_' + nextid).text(currentlabel);
                var currentmobimg = $('#mob_' + currentid).html();
                var nextmobimg = $('#mob_' + nextid).html();
                $('#mob_' + currentid).html(nextmobimg);
                $('#mob_' + nextid).html(currentmobimg);
                var currentmobimg2 = $('#mob2_' + currentid).html();
                var nextmobimg2 = $('#mob2_' + nextid).html();
                $('#mob2_' + currentid).html(nextmobimg2);
                $('#mob2_' + nextid).html(currentmobimg2);
                __this._EnableDisableIcons(nextid, currentstatus);
                __this._EnableDisableIcons(currentid, nxtstatus);
            });
            $('.preicon').on("click", function(e) {
                e.stopPropagation();
                var currentid = $(this).attr('currentid');
                var previd = $(this).attr('previousid');
                var currentstatus = $('#' + currentid).attr('activestatus');
                var prestatus = $('#' + previd).attr('activestatus');
                var currentlabel = $('#lab_' + currentid).text();
                var prelabel = $('#lab_' + previd).text();
                var currentimage = $('#img_' + currentid).html();
                var preimage = $('#img_' + previd).html();
                $('#img_' + currentid).html(preimage);
                $('#img_' + previd).html(currentimage);
                $('#lab_' + currentid).text(prelabel);
                $('#lab_' + previd).text(currentlabel);
                var currentmobimg = $('#mob_' + currentid).html();
                var premobimg = $('#mob_' + previd).html();
                $('#mob_' + currentid).html(premobimg);
                $('#mob_' + previd).html(currentmobimg);
                var currentmobimg2 = $('#mob2_' + currentid).html();
                var premobimg2 = $('#mob2_' + previd).html();
                $('#mob2_' + currentid).html(premobimg2);
                $('#mob2_' + previd).html(currentmobimg2);
                __this._EnableDisableIcons(previd, currentstatus);
                __this._EnableDisableIcons(currentid, prestatus);
            });
            $('#pageleft').on("click", function() {
                $('#contentbox2').addClass('nodisplay');
                $('#contentbox1').removeClass('nodisplay');
            });
            $('#pageright').on("click", function() {
                $('#contentbox1').addClass('nodisplay');
                $('#contentbox2').removeClass('nodisplay');
            });
        },
        _EnableDisableIcons: function(id, status) {
            var __this = this;
            if (status.toString().toLowerCase() == "1") {
                $('#' + id).removeClass('iconspanel-disable').addClass('iconspanel').attr('activestatus', "1");
                $('.dbstatus', $('#' + id)).removeClass('icondisable').addClass('iconenable').text('Enabled');
                if (id < 9) {
                    $('#mob_' + id).removeClass('nodisplay');
                }
            } else if (status.toString().toLowerCase() == "0") {
                $('#' + id).removeClass('iconspanel').addClass('iconspanel-disable').attr('activestatus', "0");
                $('.dbstatus', $('#' + id)).removeClass('iconenable').addClass('icondisable').text('Disabled');
                $('#mob_' + id).addClass('nodisplay');
            }
        },

        // public members
        buildPage: function() {
            this._InitializePageBuilder()
        }

    })
var InstitutionJS = new Remember.institution.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function() {
    jsFramework.lib.ui.pageBinder.addPageBuilder(InstitutionJS)
})