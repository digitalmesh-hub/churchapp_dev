jsFramework.lib.core.utils.registerNamespace('Remember.affiliatedInstitution.ui.js')
Remember.affiliatedInstitution.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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

            //view button function
            $('.btn-delete-institution').on('click', function() {
                var ajaxUrl = $('#homeUrl').val() + $('#admin-delete-affiliated-institution-Url').val();
                var affiliatedinstitutionid = $(this).attr('data-affiliatedinstitutionid');

                swal({
                        title: 'Are you sure?',
                        text: 'Do you want to delete this institution',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'btn-danger',
                        confirmButtonText: 'Yes',
                        closeOnConfirm: false
                    },
                    function() {
                        $.post(ajaxUrl, // Ajax Get URL
                            {
                                '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                                affiliatedinstitutionid: affiliatedinstitutionid,
                            },
                            function(res) {
                                if (typeof(res) != 'undefined' && res.status == 'success') {
                                    swal({
                                            title: 'Success',
                                            text: 'The institution has been deleted',
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
                                        }
                                    )
                                }
                            }
                        )
                    });
            });


            //Save affiliated institution
            $(document).on('click', '.save-institution', function() {
                var isRotary = parseInt($('#isRotary').val());
                var ruleCountryCode = /^(\+(?:\d{2,3}$))|(\+(?:\d{1})$)|(?:\d{2,3}$)|(?:\d{1}$)/;
                var ruleAreaCode = /^\d{0,5}$/;
                var rulePhone1 = /^\d{10}$/;
                var rulePhone2 = /^\d{7,10}$/;
                if(isRotary == 0){
                    var phone1Code = $('#extendedaffiliatedinstitution-mobilenocountrycode').val().trim();
                    var phone1 = $('#extendedaffiliatedinstitution-phone2').val().trim();
                    var phone2Code = $('#extendedaffiliatedinstitution-phone1_countrycode').val().trim();
                    var phone2AreaCode = $('#extendedaffiliatedinstitution-phone1_areacode').val().trim();
                    var phone2 = $('#extendedaffiliatedinstitution-phone1').val().trim();
                    var phone3Code = $('#extendedaffiliatedinstitution-phone3_countrycode').val().trim();
                    var Phone3AreaCode = $('#extendedaffiliatedinstitution-phone3_areacode').val().trim();
                    var phone3 = $('#extendedaffiliatedinstitution-phone3').val().trim();
                    
                    // if (!ruleCountryCode.test(phone1)) {
                    //   $('.error-phone1').addClass('error-ms');
                    //   $('.error-phone1').show();
                    //   return false;
                    // }
                    if (phone1 != '') {
                        if (!rulePhone1.test(phone1)) {
                            $('.error-phone1').addClass('error-ms');
                            $('.error-phone1').show();
                            return false;
                        }
                    }
                    // else if (!ruleCountryCode.test(phone2Code)) {
                    //   $('.error-phone2').addClass('error-ms');
                    //   $('.error-phone2').show();
                    //   return false;
                    // }
                    if (phone2AreaCode != '') {
                        if (!ruleAreaCode.test(phone2AreaCode)) {
                            $('.error-phone2').addClass('error-ms');
                            $('.error-phone2').show();
                            return false;
                        }
                    } 
                    if (phone2 != '') {
                        if (!rulePhone2.test(phone2)) {
                            $('.error-phone2').addClass('error-ms');
                            $('.error-phone2').show();
                            return false;
                        }
                    } 
                    if (phone3AreaCode != '') {
                        if (!ruleAreaCode.test(phone3AreaCode)) {
                            $('.error-phone3').addClass('error-ms');
                            $('.error-phone3').show();
                            return false;
                        }
                    } 
                    if (phone3 != '') {
                        if (!rulePhone2.test(phone3)) {
                            $('.error-phone3').addClass('error-ms');
                            $('.error-phone3').show();
                            return false;
                        }
                    } else {
                        $('.msg-div').hide();
                        return true;
                    }
                }
                else{
                    var presidentCountryCode = $('#extendedaffiliatedinstitution-presidentmobile_countrycode').val().trim();
                    var presidentMobile = $('#extendedaffiliatedinstitution-presidentmobile').val().trim();
                    var secretaryCountryCode = $('#extendedaffiliatedinstitution-secretarymobile_countrycode').val().trim();
                    var secretaryMobile = $('#extendedaffiliatedinstitution-secretarymobile').val().trim();
                    if (presidentCountryCode != '') {
                        if (!ruleCountryCode.test(presidentCountryCode)) {
                            $('.error-president-mobile').addClass('error-ms');
                            $('.error-president-mobile').show();
                            return false;
                        }
                    }
                    if (presidentMobile != '') {
                        if (!rulePhone1.test(presidentMobile)) {
                            $('.error-president-mobile').addClass('error-ms');
                            $('.error-president-mobile').show();
                            return false;
                        }
                    }
                    if (secretaryCountryCode != '') {
                        if (!ruleCountryCode.test(secretaryCountryCode)) {
                            $('.error-secretary-mobile').addClass('error-ms');
                            $('.error-secretary-mobile').show();
                            return false;
                        }
                    }
                    if (secretaryMobile != '') {
                        if (!rulePhone1.test(secretaryMobile)) {
                            $('.error-secretary-mobile').addClass('error-ms');
                            $('.error-secretary-mobile').show();
                            return false;
                        }
                    }
                    else {
                        $('.msg-div').hide();
                        return true;
                    }
                }
            });
        },
        _onChangeEvents: function() { 
             var __this = this
            $('#country_id').on('change', function(e) {
                var countryId = $('#country_id').val();
                if (countryId != '') {
                    __this._loadCountryCode(countryId)
                }

            });
            //Thumbnail image change function var id = $(this).attr('id');
            $(".thumbnailimage").on("change", function() {
                var imageFile = this.files[0];
                var type = imageFile.type;

                //Image upload and preview
                if (imageFile.type != 'image/png' && imageFile.type != 'image/jpg' && imageFile.type != 'image/jpeg') {
                    if ($("#AffliatedInstitutionImage").attr("src") != $('#base-url').val() + '/theme/images/institution-icon-grey.png') {
                        var location = $('#base-url').val() + "/theme/images/institution-icon-grey.png";
                        $('#AffliatedInstitutionImage').attr("src", location);
                    } else {
                        $("#AffliatedInstitutionImage").replaceWith($("#AffliatedInstitutionImage").clone());
                    }
                } else {
                    var url = window.URL.createObjectURL(imageFile);
                    $('#AffliatedInstitutionImage').attr("src", url);
                }
            });


            //Phone number validation
            $(document).on('keydown', '.number-check', function(e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        },

        _loadCountryCode: function(countryId) {

            var ajaxUrl = $('#homeUrl').val() + $('#admin-get-countryCode-Url').val();
            $.get(ajaxUrl, // Ajax Get URL
                {
                    countryId: countryId
                },
                function(res) {
                    if (typeof(res) != 'undefined' && res.status == 'success') {
                        $('.country-code').val(res.countryCode);
                    } else {
                        $('.modal-title').text('Conversations');
                        $('.content-div').text("An error occured while processing the request.");
                        $("#myModal").modal('show');
                    }
                }
            )

        },
        _onLoadEvents: function() {
            var __this = this
            $(document).ready(function () {
                var countryId = $('#country_id').val();
                if (countryId != undefined && countryId != "") {
                    __this._loadCountryCode(countryId)
                }
            })
        },
        _onKeyEvents: function() {
            var __this = this
        },

        // public members
        buildPage: function() {
            this._InitializePageBuilder()
        }

    })
var AffiliatedInstitutionJs = new Remember.affiliatedInstitution.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function() {
    jsFramework.lib.ui.pageBinder.addPageBuilder(AffiliatedInstitutionJs)
})