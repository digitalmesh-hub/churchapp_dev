jsFramework.lib.core.utils.registerNamespace('Remember.memberApproval.ui')
Remember.memberApproval.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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

            $("#Next1").on("click", function() {
                $('.nav-tabs a[href="#dependants"]').tab('show');
            });

            $("#Next2").on("click", function() {
                $('.nav-tabs a[href="#settings"]').tab('show');
            });

            $("#btnCancelDepend").on('click', function() {
                $('#txtDependantName').val("");
                $('#txtDependantMobileCountryCode').val("");
                $('#txtDependantMobile').val("");
                $("#txtSpouseMobile").val("");
                $('#DPMartialStatus').val("");
                $("#dependantspousetitleid").val("");
                $("#txtdependantspousename").val("");
                $("#txtdependantspousemobilecountrycode").val("");
                $("#txtdependantspousemobile").val("");
                $("#dependantspousedob").val("");
                $("#dependantweddingdate").val("");
                $("#hdndependantspouseid").val("-1");
                $('#dependantspousediv').addClass('nodisplay');
                $('#btnAddDepend').val("Add");
                $('#dependantId').val("");
                $('#spouseDependantId').val("");
                $('#dependantdob').val("");
                $('#dependantTitleId').val("");
            });

            $('body').on("click", ".infobtn", function() {
                $(this).parent('div').children('div').attr('class', '');
                $(this).parent('div').children('div').attr('class', 'approvalbox');
                $(this).parent('div').children('div').show();
            });


            $('body').on("click", ".approvebtn", function() {
                $(this).parent().parent().parent().children().attr('isapproved', true);
                $(this).parent().parent().hide();
                $(this).parent().parent().parent().children().removeClass('pendinginfo');
            });

            $('body').on("click", ".rejectbtn", function() {
                $(this).parent().parent().parent().children().attr('isapproved', false);
                $(this).parent().parent().hide();
                $(this).parent().parent().parent().children().removeClass('pendinginfo');
            });

            function formatCoordinates(input) {
                return input.replace(/latitude:\s*"?([^",]*)"?,\s*longitude:\s*"?([^",]*)"?/, (match, lat, lon) => {
                    return (lat === "" && lon === "") ? "," : `${lat},${lon}`;
                });
            }

            //Member Save or Update
            $(document).on('click', '.valid', function(e) {
                e.preventDefault();
                var totalModified = $("#totalModified").val();
                var totalApproved = 0;
                var totalRejected = 0;
                var MemberTitle = typeof $("#txtMemberTitle").attr("isapproved") != 'undefined' ? ($("#txtMemberTitle").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberTitle").attr('membertitleid') : $("#MemberTitle").attr('membertitleid')) : $("#txtMemberTitle").attr('membertitleid');
                var MemberTitleDescription = typeof $("#txtMemberTitle").attr("isapproved") != 'undefined' ? ($("#txtMemberTitle").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberTitle").val() : $("#MemberTitle").val()) : $("#txtMemberTitle").val();
                var FirstName = typeof $("#txtFirstName").attr("isapproved") != 'undefined' ? ($("#txtFirstName").attr("isapproved").toLowerCase() == 'true' ? $("#txtFirstName").val() : $("#FirstName").text()) : $("#txtFirstName").val();
                var MiddleName = typeof $("#txtMiddleName").attr("isapproved") != 'undefined' ? ($("#txtMiddleName").attr("isapproved").toLowerCase() == 'true' ? $("#txtMiddleName").val() : $("#MiddleName").text()) : $("#txtMiddleName").val();
                var LastName = typeof $("#txtLastName").attr("isapproved") != 'undefined' ? ($("#txtLastName").attr("isapproved").toLowerCase() == 'true' ? $("#txtLastName").val() : $("#LastName").text()) : $("#txtLastName").val();
                var MemberMobile1 = typeof $("#txtMemberMobile1").attr("isapproved") != 'undefined' ? ($("#txtMemberMobile1").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberMobile1").val() : $("#span_MemberMobile1").text()) : $("#txtMemberMobile1").val();
                var MemberMobile2 = '';
                var varmemberDOB = typeof $("#txtMemberDOB").attr("isapproved") != 'undefined' ? ($("#txtMemberDOB").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberDOB").val() : $("#MemberDOB").text()) : $("#txtMemberDOB").val();
                var MemberEmail = typeof $("#txtMemberEmail").attr("isapproved") != 'undefined' ? ($("#txtMemberEmail").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberEmail").val() : $("#MemberEmail").text()) : $("#txtMemberEmail").val();
                var SpouseTitle = typeof $("#txtSpouseTitle").attr("isapproved") != 'undefined' ? ($("#txtSpouseTitle").attr("isapproved").toLowerCase() == 'true' ? $("#txtSpouseTitle").attr('membertitleid') : $("#SpouseTitle").attr('membertitleid')) : $("#txtSpouseTitle").attr('membertitleid');
                var SpouseTitleDescription = typeof $("#txtSpouseTitle").attr("isapproved") != 'undefined' ? ($("#txtSpouseTitle").attr("isapproved").toLowerCase() == 'true' ? $("#txtSpouseTitle").val() : $("#SpouseTitle").val()) : $("#txtSpouseTitle").val();

                var SpouseFirstName = typeof $("#txtMemberSpouseFirstName").attr("isapproved") != 'undefined' ? ($("#txtMemberSpouseFirstName").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberSpouseFirstName").val() : $("#SpouseFirstName").text()) : $("#txtMemberSpouseFirstName").val();
                var SpouseMiddleName = typeof $("#txtMemberSpouseMiddleName").attr("isapproved") != 'undefined' ? ($("#txtMemberSpouseMiddleName").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberSpouseMiddleName").val() : $("#SpouseMiddleName").text()) : $("#txtMemberSpouseMiddleName").val();
                var SpouseLastName = typeof $("#txtMemberSpouseLastName").attr("isapproved") != 'undefined' ? ($("#txtMemberSpouseLastName").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberSpouseLastName").val() : $("#SpouseLastName").text()) : $("#txtMemberSpouseLastName").val();
                var spouseemail = typeof $("#txtMemberspouseemail").attr("isapproved") != 'undefined' ? ($("#txtMemberspouseemail").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberspouseemail").val() : $("#spouseemail").text()) : $("#txtMemberspouseemail").val();
                var spousemobile1 = typeof $("#txtspousemobile1").attr("isapproved") != 'undefined' ? ($("#txtspousemobile1").attr("isapproved").toLowerCase() == 'true' ? $("#txtspousemobile1").val() : $("#span_spousemobile1").text()) : $("#txtspousemobile1").val();
                var spousemobile2 = '';
                var varspouseDOB = typeof $("#txtSpouseDOB").attr("isapproved") != 'undefined' ? ($("#txtSpouseDOB").attr("isapproved").toLowerCase() == 'true' ? $("#txtSpouseDOB").val() : $("#varspouseDOB").text()) : $("#txtSpouseDOB").val();
                var MemberNo = typeof $("#txtMemberNo").attr("isapproved") != 'undefined' ? ($("#txtMemberNo").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberNo").val() : $("#MemberNo").text()) : $("#txtMemberNo").val();
                var location1 = typeof $("#txtlocation").attr("isapproved") != 'undefined' ? ($("#txtlocation").attr("isapproved").toLowerCase() == 'true' ? $("#txtlocation").val() : $("#location").text()) : $("#txtlocation").val();
                var location = formatCoordinates(location1);
                var varMembersince = typeof $("#txtMembersince").attr("isapproved") != 'undefined' ? ($("#txtMembersince").attr("isapproved").toLowerCase() == 'true' ? $("#txtMembersince").val() : $("#varMembersince").text()) : $("#txtMembersince").val();
                var MemberShip_Type = typeof $("#txtMemberShip_Type").attr("isapproved") != 'undefined' ? ($("#txtMemberShip_Type").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberShip_Type").val() : $("#MemberShip_Type").text()) : $("#txtMemberShip_Type").val();
                var varDOM = typeof $("#txtdom").attr("isapproved") != 'undefined' ? ($("#txtdom").attr("isapproved").toLowerCase() == 'true' ? $("#txtdom").val() : $("#varDOM").text()) : $("#txtdom").val();
                var BusinessAddress1 = typeof $("#txtBusinessAddress1").attr("isapproved") != 'undefined' ? ($("#txtBusinessAddress1").attr("isapproved").toLowerCase() == 'true' ? $("#txtBusinessAddress1").val() : $("#BusinessAddress1").text()) : $("#txtBusinessAddress1").val();
                var BusinessAddress2 = typeof $("#txtBusinessAddress2").attr("isapproved") != 'undefined' ? ($("#txtBusinessAddress2").attr("isapproved").toLowerCase() == 'true' ? $("#txtBusinessAddress2").val() : $("#BusinessAddress2").text()) : $("#txtBusinessAddress2").val();
                var BusinessDistrict = typeof $("#txtBusinessDistrict").attr("isapproved") != 'undefined' ? ($("#txtBusinessDistrict").attr("isapproved").toLowerCase() == 'true' ? $("#txtBusinessDistrict").val() : $("#BusinessDistrict").text()) : $("#txtBusinessDistrict").val();
                var BusinessPincode = typeof $("#txtBusinessPincode").attr("isapproved") != 'undefined' ? ($("#txtBusinessPincode").attr("isapproved").toLowerCase() == 'true' ? $("#txtBusinessPincode").val() : $("#BusinessPincode").text()) : $("#txtBusinessPincode").val();
                var BusinessState = typeof $("#txtBusinessState").attr("isapproved") != 'undefined' ? ($("#txtBusinessState").attr("isapproved").toLowerCase() == 'true' ? $("#txtBusinessState").val() : $("#BusinessState").text()) : $("#txtBusinessState").val();
                var MemberBusinessPhone1 = typeof $("#txtMemberBusinessPhone1").attr("isapproved") != 'undefined' ? ($("#txtMemberBusinessPhone1").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberBusinessPhone1").val() : $("#span_MemberBusinessPhone1").text()) : $("#txtMemberBusinessPhone1").val();
                var MemberBusinessPhone2 = typeof $("#txtMemberBusinessPhone2").attr("isapproved") != 'undefined' ? ($("#txtMemberBusinessPhone2").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberBusinessPhone2").val() : $("#span_MemberBusinessPhone2").text()) : $("#txtMemberBusinessPhone2").val();

                var Residenceaddress1 = typeof $("#txtresidenceaddress1").attr("isapproved") != 'undefined' ? ($("#txtresidenceaddress1").attr("isapproved").toLowerCase() == 'true' ? $("#txtresidenceaddress1").val() : $("#residenceaddress1").text()) : $("#txtresidenceaddress1").val();
                var Residenceaddress2 = typeof $("#txtresidenceaddress2").attr("isapproved") != 'undefined' ? ($("#txtresidenceaddress2").attr("isapproved").toLowerCase() == 'true' ? $("#txtresidenceaddress2").val() : $("#residenceaddress2").text()) : $("#txtresidenceaddress2").val();
                var Residence_pincode = typeof $("#txtresidence_pincode").attr("isapproved") != 'undefined' ? ($("#txtresidence_pincode").attr("isapproved").toLowerCase() == 'true' ? $("#txtresidence_pincode").val() : $("#residence_pincode").text()) : $("#txtresidence_pincode").val();
                var Residencedistrict = typeof $("#txtresidencedistrict").attr("isapproved") != 'undefined' ? ($("#txtresidencedistrict").attr("isapproved").toLowerCase() == 'true' ? $("#txtresidencedistrict").val() : $("#residencedistrict").text()) : $("#txtresidencedistrict").val();

                var Residencestate = typeof $("#txtresidencestate").attr("isapproved") != 'undefined' ? ($("#txtresidencestate").attr("isapproved").toLowerCase() == 'true' ? $("#txtresidencestate").val() : $("#residencestate").text()) : $("#txtresidencestate").val();
                var MemberResidencePhone2 = typeof $("#txtMemberResidencePhone2").attr("isapproved") != 'undefined' ? ($("#txtMemberResidencePhone2").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberResidencePhone2").val() : $("#MemberResidencePhone2").text()) : $("#txtMemberResidencePhone2").val();
                var BusinessEmail = typeof $("#txtbusinessEmail").attr("isapproved") != 'undefined' ? ($("#txtbusinessEmail").attr("isapproved").toLowerCase() == 'true' ? $("#txtbusinessEmail").val() : $("#BusinessEmail").text()) : $("#txtbusinessEmail").val();
                var SpouceNickName = typeof $("#txtSpouseNickName").attr("isapproved") != 'undefined' ? ($("#txtSpouseNickName").attr("isapproved").toLowerCase() == 'true' ? $("#txtSpouseNickName").val() : $("#SpouceNickName").text()) : $("#txtSpouseNickName").val();
                var MemberNickName = typeof $("#txtMemberNickName").attr("isapproved") != 'undefined' ? ($("#txtMemberNickName").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberNickName").val() : $("#MemberNickName").text()) : $("#txtMemberNickName").val();
                var memberpic = $("#tempmemberimage").attr("isapproved") != 'undefined' ? (($("#memberimageinfo").attr("isapproved").toLowerCase() == 'true' || $("#tempmemberimage").attr("isapproved").toLowerCase() == 'true') ? $("#tempmemberimage").attr("src") : $("#memberpic").attr("src")) : $("#tempmemberimage").attr("src");
                var spousepic = $("#tempspouseimage").attr("isapproved") != 'undefined' ? (($("#spouseimageinfo").attr("isapproved").toLowerCase() == 'true' || $("#tempspouseimage").attr("isapproved").toLowerCase() == 'true') ? $("#tempspouseimage").attr("src") : $("#spouseimage").attr("src")) : $("#tempspouseimage").attr("src");

                var MemberMobile1_countrycode = typeof $("#txtMemberMobile1_countrycode").attr("isapproved") != 'undefined' ? ($("#txtMemberMobile1_countrycode").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberMobile1_countrycode").val() : $("#span_Member_Mobile1_Countrycode").text()) : $("#txtMemberMobile1_countrycode").val();
                var SpouseMobile1_countrycode = typeof $("#txtspousemobile1_countrycode").attr("isapproved") != 'undefined' ? ($("#txtspousemobile1_countrycode").attr("isapproved").toLowerCase() == 'true' ? $("#txtspousemobile1_countrycode").val() : $("#span_Spouse_Mobile1_Countrycode").text()) : $("#txtspousemobile1_countrycode").val();
                var MemberBusinessPhone1_countrycode = typeof $("#txtMemberBusinessPhone1_countrycode").attr("isapproved") != 'undefined' ? ($("#txtMemberBusinessPhone1_countrycode").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberBusinessPhone1_countrycode").val() : $("#span_Member_Business_Phone1_Countrycode").text()) : $("#txtMemberBusinessPhone1_countrycode").val();
                var MemberBusinessPhone1_areacode = typeof $("#txtMemberBusinessPhone1_areacode").attr("isapproved") != 'undefined' ? ($("#txtMemberBusinessPhone1_areacode").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberBusinessPhone1_areacode").val() : $("#span_Member_Business_Phone1_Areacode").text()) : $("#txtMemberBusinessPhone1_areacode").val();
                var MemberBusinessPhone2_countrycode = typeof $("#txtMemberBusinessPhone2_countrycode").attr("isapproved") != 'undefined' ? ($("#txtMemberBusinessPhone2_countrycode").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberBusinessPhone2_countrycode").val() : $("#span_Member_Business_Phone2_Countrycode").text()) : $("#txtMemberBusinessPhone2_countrycode").val();

                var HiddenInstitutionType = $("#HiddenInstitutionType").val();
                if (HiddenInstitutionType == 2) {
                    var HomeChurch = typeof $("#txtHomeChurch").attr("isapproved") != 'undefined' ? ($("#txtHomeChurch").attr("isapproved").toLowerCase() == 'true' ? $("#txtHomeChurch").val() : $("#HomeChurch").text()) : $("#txtHomeChurch").val();
                } else {
                    HomeChurch = null;
                }
                var Occupation = typeof $("#txtOccupation").attr("isapproved") != 'undefined' ? ($("#txtOccupation").attr("isapproved").toLowerCase() == 'true' ? $("#txtOccupation").val() : $("#Occupation").text()) : $("#txtOccupation").val();
                var SpouseOccupation = typeof $("#txtSpouseOccupation").attr("isapproved") != 'undefined' ? ($("#txtSpouseOccupation").attr("isapproved").toLowerCase() == 'true' ? $("#txtSpouseOccupation").val() : $("#SpouseOccupation").text()) : $("#txtSpouseOccupation").val();

                var MemberBusinessPhone3 = typeof $("#txtMemberBusinessPhone3").attr("isapproved") != 'undefined' ? ($("#txtMemberBusinessPhone3").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberBusinessPhone3").val() : $("#span_MemberBusinessPhone3").text()) : $("#txtMemberBusinessPhone3").val();
                var MemberBusinessPhone3_countrycode = typeof $("#txtMemberBusinessPhone3_countrycode").attr("isapproved") != 'undefined' ? ($("#txtMemberBusinessPhone3_countrycode").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberBusinessPhone3_countrycode").val() : $("#span_Member_Business_Phone3_Countrycode").text()) : $("#txtMemberBusinessPhone3_countrycode").val();
                var MemberBusinessPhone3_areacode = typeof $("#txtMemberBusinessPhone3_areacode").attr("isapproved") != 'undefined' ? ($("#txtMemberBusinessPhone3_areacode").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberBusinessPhone3_areacode").val() : $("#span_Member_Business_Phone3_Areacode").text()) : $("#txtMemberBusinessPhone3_areacode").val();

                var MemberBloodGroup = typeof $("#txtMemberBloodGroup").attr("isapproved") != 'undefined' ? ($("#txtMemberBloodGroup").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberBloodGroup").val() : $("#MemberBloodGroup").text()) : $("#txtMemberBloodGroup").val();;
                var SpouseBloodGroup = typeof $("#txtSpouseBloodGroup").attr("isapproved") != 'undefined' ? ($("#txtSpouseBloodGroup").attr("isapproved").toLowerCase() == 'true' ? $("#txtSpouseBloodGroup").val() : $("#SpouseBloodGroup").text()) : $("#txtSpouseBloodGroup").val();;

                var TagCloud = typeof $("#txtTagCloud").attr("isapproved") != 'undefined' ? ($("#txtTagCloud").attr("isapproved").toLowerCase() == 'true' ? $("#txtTagCloud").val() : $("#TagCloud").text()) : $("#txtTagCloud").val();
                var MemberResidencePhone1 = typeof $("#txtMemberResidencePhone1").attr("isapproved") != 'undefined' ? ($("#txtMemberResidencePhone1").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberResidencePhone1").val() : $("#span_MemberResidencePhone1").text()) : $("#txtMemberResidencePhone1").val();
                var MemberResidencePhone1CountryCode = typeof $("#txtMemberResidencePhone1_countrycode").attr("isapproved") != 'undefined' ? ($("#txtMemberResidencePhone1_countrycode").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberResidencePhone1_countrycode").val() : $("#span_MemberResidencePhone1CountryCode").text()) : $("#txtMemberResidencePhone1_countrycode").val();
                var MemberResidencePhone1AreaCode = typeof $("#txtMemberResidencePhone1_areacode").attr("isapproved") != 'undefined' ? ($("#txtMemberResidencePhone1_areacode").attr("isapproved").toLowerCase() == 'true' ? $("#txtMemberResidencePhone1_areacode").val() : $("#span_MemberResidencePhone1AreaCode").text()) : $("#txtMemberResidencePhone1_areacode").val();
                var _DependantLst = new Array();
                var Additionalinfo = {
                    MemberID: $("#hdnmemberid").val(),
                    TagCloud: TagCloud,
                };
                var dependantcount = $("#HiddenDependantCount").val();

                if (Number(dependantcount) > 0) {
                    var _DependantLst = new Array();

                    for (var k = 0; k < dependantcount; k++) {
                        var Dependant = {
                            "DependantId": $("#txtDependantName" + k).attr("dependantid"),
                            "DependantTitleId": typeof $("#txtDependantTitle" + k).attr("isapproved") != 'undefined' ? ($("#txtDependantTitle" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtDependantTitle" + k).attr('dependanttitleid') : $("#DependantTitle" + k).attr('dependanttitleid')) : $("#txtDependantTitle" + k).attr('dependanttitleid'),
                            "DependantTitle": typeof $("#txtDependantTitle" + k).attr("isapproved") != 'undefined' ? ($("#txtDependantTitle" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtDependantTitle" + k).val() : "") : $("#txtDependantTitle" + k).val(),
                            "DependantName": typeof $("#txtDependantName" + k).attr("isapproved") != 'undefined' ? ($("#txtDependantName" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtDependantName" + k).val() : $("#DependantName" + k).text()) : $("#txtDependantName" + k).val(),
                            "DependantMobileCountryCode": typeof $("#txtDependantMobileCountryCode" + k).attr("isapproved") != 'undefined' ? ($("#txtDependantMobileCountryCode" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtDependantMobileCountryCode" + k).val() : $("#DependantMobileCountryCode" + k).text()) : $("#txtDependantMobileCountryCode" + k).val(),
                            "DependantMobile": typeof $("#txtDependantMobile" + k).attr("isapproved") != 'undefined' ? ($("#txtDependantMobile" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtDependantMobile" + k).val() : $("#DependantMobile" + k).text()) : $("#txtDependantMobile" + k).val(),
                            "Dob": typeof $("#txtDependantDOB" + k).attr("isapproved") != 'undefined' ? ($("#txtDependantDOB" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtDependantDOB" + k).val() : $("#Dob" + k).text()) : $("#txtDependantDOB" + k).val(),
                            "Relation": typeof $("#txtRelation" + k).attr("isapproved") != 'undefined' ? ($("#txtRelation" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtRelation" + k).val() : $("#Relation" + k).text()) : $("#txtRelation" + k).val(),
                            "IsMarried": typeof $("#txtMartialStatus" + k).attr("isapproved") != 'undefined' ? ($("#txtMartialStatus" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtMartialStatus" + k).val() : $("#MartialStatus" + k).text()) : $("#txtMartialStatus" + k).val(),
                            "SpouseTitleId": typeof $("#txtDependantSpouseTitle" + k).attr("isapproved") != 'undefined' ? ($("#txtDependantSpouseTitle" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtDependantSpouseTitle" + k).attr('dependanspousettitleid') : $("#DependantSpouseTitle" + k).attr('dependanspousettitleid')) : $("#txtDependantSpouseTitle" + k).attr('dependanspousettitleid'),
                            "SpouseTitle": typeof $("#txtDependantSpouseTitle" + k).attr("isapproved") != 'undefined' ? ($("#txtDependantSpouseTitle" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtDependantSpouseTitle" + k).val() : "") : $("#txtDependantSpouseTitle" + k).val(),
                            "DependantSpouseName": typeof $("#txtSpouseName" + k).attr("isapproved") != 'undefined' ? ($("#txtSpouseName" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtSpouseName" + k).val() : $("#SpouseName" + k).text()) : $("#txtSpouseName" + k).val(),
                            "DependantSpouseMobileCountryCode": typeof $("#txtSpouseMobileCountryCode" + k).attr("isapproved") != 'undefined' ? ($("#txtSpouseMobileCountryCode" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtSpouseMobileCountryCode" + k).val() : $("#SpouseMobileCountryCode" + k).text()) : $("#txtSpouseMobileCountryCode" + k).val(),
                            "DependantSpouseMobile": typeof $("#txtSpouseMobile" + k).attr("isapproved") != 'undefined' ? ($("#txtSpouseMobile" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtSpouseMobile" + k).val() : $("#SpouseMobile" + k).text()) : $("#txtSpouseMobile" + k).val(),
                            "DependantSpouseDOB": typeof $("#txtSpouseDOB" + k).attr("isapproved") != 'undefined' ? ($("#txtSpouseDOB" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtSpouseDOB" + k).val() : $("#SpouseDOB" + k).text()) : $("#txtSpouseDOB" + k).val(),
                            "WeddingAnniversary": typeof $("#txtWeddingAnniversary" + k).attr("isapproved") != 'undefined' ? ($("#txtWeddingAnniversary" + k).attr("isapproved").toLowerCase() == 'true' ? $("#txtWeddingAnniversary" + k).val() : $("#WeddingAnniversary" + k).text()) : $("#txtWeddingAnniversary" + k).val(),
                            "Image": $("#txtDependantImage" + k).attr("isapproved") != 'undefined' ? (($("#txtDependantImage" + k).attr("isapproved").toLowerCase() == 'true' || $("#dependantimageinfo" + k).attr("isapproved").toLowerCase() == 'true') ? $("#txtDependantImage" + k).attr("src") : $("#dependantpic" + k).attr("src")) : $("#txtDependantImage" + k).attr("src"),
                            "SpouseImage": $("#txtDependantSpouseImage" + k).attr("isapproved") != 'undefined' ? (($("#txtDependantSpouseImage" + k).attr("isapproved").toLowerCase() == 'true' || $("#dependantspouseimageinfo" + k).attr("isapproved").toLowerCase() == 'true') ? $("#txtDependantSpouseImage" + k).attr("src") : $("#dependantspousepic" + k).attr("src")) : $("#txtDependantSpouseImage" + k).attr("src"),
                        };
                        _DependantLst.push(Dependant);
                    }
                }
                var AllRejected = true;
                var AllAccepted = true;
                if (Number(dependantcount) > 0) {
                    var _MailDependantLst = new Array();

                    for (var k = 0; k < dependantcount; k++) {

                        if (typeof $("#txtDependantImage" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#dependantimageinfo" + k).attr("isapproved");
                            var IsApprovedDependantImage = isapproved == "true" ? true : false
                            if (IsApprovedDependantImage == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsApprovedDependantImage == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsApprovedDependantImage = true;
                        }

                        if (typeof $("#txtDependantSpouseImage" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#dependantspouseimageinfo" + k).attr("isapproved");
                            var IsApprovedDependantSpouseImage = isapproved == "true" ? true : false
                            if (IsApprovedDependantSpouseImage == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsApprovedDependantSpouseImage == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsApprovedDependantSpouseImage = true;
                        }

                        if (typeof $("#txtDependantName" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtDependantName" + k).attr("isapproved");
                            var IsApprovedDependantName = isapproved == "true" ? true : false
                            if (IsApprovedDependantName == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsApprovedDependantName == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsApprovedDependantName = true;
                        }

                        if (typeof $("#txtDependantMobileCountryCode" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtDependantMobileCountryCode" + k).attr("isapproved");
                            var IsApprovedDependantMobileCountryCode = isapproved == "true" ? true : false
                        } else {
                            var IsApprovedDependantMobileCountryCode = true;
                        }

                        if (typeof $("#txtDependantMobile" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtDependantMobile" + k).attr("isapproved");
                            var IsApprovedDependantMobile = isapproved == "true" ? true : false
                            if (IsApprovedDependantMobile == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsApprovedDependantMobile == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsApprovedDependantMobile = true;
                        }
                        if (typeof $("#txtRelation" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtRelation" + k).attr("isapproved");
                            var IsApprovedRelation = isapproved == "true" ? true : false
                            if (IsApprovedRelation == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsApprovedRelation == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsApprovedRelation = true;
                        }
                        if (typeof $("#txtDependantDOB" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtDependantDOB" + k).attr("isapproved");
                            var IsApprovedDob = isapproved == "true" ? true : false
                            if (IsApprovedDob == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else {
                                AllAccepted = false;
                            }
                            if (isapproved == "false") {
                                totalRejected++;
                            }
                        } else {
                            var IsApprovedDob = true;
                        }




                        if (typeof $("#txtDependantTitle" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtDependantTitle" + k).attr("isapproved");
                            var IsApprovedDependantTitle = isapproved == "true" ? true : false
                            if (IsApprovedDependantTitle == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsApprovedDependantTitle == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsApprovedDependantTitle = true;
                        }

                        if (typeof $("#txtMartialStatus" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtMartialStatus" + k).attr("isapproved");
                            var IsApprovedIsMarried = isapproved == "true" ? true : false
                            if (IsApprovedIsMarried == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsApprovedIsMarried == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsApprovedIsMarried = true;
                        }

                        if (typeof $("#txtDependantSpouseTitle" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtDependantSpouseTitle" + k).attr("isapproved");
                            var IsDependantApprovedSpouseTitle = isapproved == "true" ? true : false
                            if (IsDependantApprovedSpouseTitle == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsDependantApprovedSpouseTitle == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsDependantApprovedSpouseTitle = true;
                        }

                        if (typeof $("#txtSpouseName" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtSpouseName" + k).attr("isapproved");
                            var IsDependantApprovedSpouseName = isapproved == "true" ? true : false
                            if (IsDependantApprovedSpouseName == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsDependantApprovedSpouseName == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsDependantApprovedSpouseName = true;
                        }

                        if (typeof $("#txtSpouseMobileCountryCode" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtSpouseMobileCountryCode" + k).attr("isapproved");
                            var IsDependantApprovedSpouseMobileCountryCode = isapproved == "true" ? true : false
                        } else {
                            var IsDependantApprovedSpouseMobileCountryCode = true;
                        }

                        if (typeof $("#txtSpouseMobile" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtSpouseMobile" + k).attr("isapproved");
                            var IsDependantApprovedSpouseMobile = isapproved == "true" ? true : false
                            if (IsDependantApprovedSpouseMobile == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsDependantApprovedSpouseMobile == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsDependantApprovedSpouseMobile = true;
                        }

                        if (typeof $("#txtSpouseDOB" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtSpouseDOB" + k).attr("isapproved");
                            var IsDependantApprovedSpouseDOB = isapproved == "true" ? true : false
                            if (IsDependantApprovedSpouseDOB == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsDependantApprovedSpouseDOB == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsDependantApprovedSpouseDOB = true;
                        }
                        false
                        if (typeof $("#txtWeddingAnniversary" + k).attr("isapproved") != 'undefined') {
                            var isapproved = $("#txtWeddingAnniversary" + k).attr("isapproved");
                            var IsApprovedAnniversary = isapproved == "true" ? true : false
                            if (IsApprovedAnniversary == true) {
                                AllRejected = false;
                                totalApproved++;
                            } else if (isapproved == "false") {
                                totalRejected++;
                            }
                            if (IsApprovedAnniversary == false) {
                                AllAccepted = false;
                            }
                        } else {
                            var IsApprovedAnniversary = true;
                        }
                        var MailDependant = {

                            "TempDependantName": $("#txtDependantName" + k).val(),
                            "IsApprovedDependantName": IsApprovedDependantName,
                            "TempDependantMobileCountryCode": $("#txtDependantMobileCountryCode" + k).val(),
                            "IsApprovedDependantMobileCountryCode": IsApprovedDependantMobileCountryCode,
                            "TempDependantMobile": $("#txtDependantMobile" + k).val(),
                            "IsApprovedDependantMobile": IsApprovedDependantMobile,
                            "TempDob": $("#txtDependantDOB" + k).val(),
                            "IsApprovedDob": IsApprovedDob,
                            "TempRelation": $("#txtRelation" + k).val(),
                            "IsApprovedRelation": IsApprovedRelation,
                            "TempDependantTitle": $("#txtDependantTitle" + k).val(),
                            "IsApprovedDependantTitle": IsApprovedDependantTitle,
                            "TempIsMarried": $("#txtMartialStatus" + k).val(),
                            "IsApprovedIsMarried": IsApprovedIsMarried,
                            "TempDependantSpouseTitle": $("#txtDependantSpouseTitle" + k).val(),
                            "IsDependantApprovedSpouseTitle": IsDependantApprovedSpouseTitle,
                            "TempDependantSpouseName": $("#txtSpouseName" + k).val(),
                            "IsDependantApprovedSpouseName": IsDependantApprovedSpouseName,
                            "TempDependantSpouseMobileCountryCode": $("#txtSpouseMobileCountryCode" + k).val(),
                            "IsDependantApprovedSpouseMobileCountryCode": IsDependantApprovedSpouseMobileCountryCode,
                            "TempDependantSpouseMobile": $("#txtSpouseMobile" + k).val(),
                            "IsDependantApprovedSpouseMobile": IsDependantApprovedSpouseMobile,
                            "TempDependantSpouseDOB": $("#txtSpouseDOB" + k).val(),
                            "IsDependantApprovedSpouseDOB": IsDependantApprovedSpouseDOB,
                            "TempWeddingAnniversary": $("#txtWeddingAnniversary" + k).val(),
                            "IsApprovedAnniversary": IsApprovedAnniversary,
                            "TempMarried": $("#txtMartialStatus" + k).val(),
                        };
                        _MailDependantLst.push(MailDependant);
                    }
                }

                //mail data

                var TempMemberTitle = $("#txtMemberTitle").val();
                if (typeof $("#txtMemberTitle").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberTitle").attr("isapproved");
                    var IsApprovedMemberTitle = isapproved == "true" ? true : false
                    if (IsApprovedMemberTitle == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    } 
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberTitle = true;
                }
                var TempFirstName = $("#txtFirstName").val();
                if (typeof $("#txtFirstName").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtFirstName").attr("isapproved");
                    var IsApprovedFirstName = isapproved == "true" ? true : false
                    if (IsApprovedFirstName == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedFirstName = true;
                }
                var TempMiddleName = $("#txtMiddleName").val();
                if (typeof $("#txtMiddleName").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMiddleName").attr("isapproved");
                    var IsApprovedMiddleName = isapproved == "true" ? true : false
                    if (IsApprovedMiddleName == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMiddleName = true;
                }

                var TempLastName = $("#txtLastName").val();
                if (typeof $("#txtLastName").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtLastName").attr("isapproved");
                    var IsApprovedLastName = isapproved == "true" ? true : false
                    if (IsApprovedLastName == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedLastName = true;
                }
                var TempMemberMobile1 = $("#txtMemberMobile1").val();
                if (typeof $("#txtMemberMobile1").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberMobile1").attr("isapproved");
                    var IsApprovedMemberMobile1 = isapproved == "true" ? true : false
                    if (IsApprovedMemberMobile1 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberMobile1 = true;
                }
                var TempvarmemberDOB = $("#txtMemberDOB").val();

                if (typeof $("#txtMemberDOB").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberDOB").attr("isapproved");
                    var IsApprovedvarmemberDOB = isapproved == "true" ? true : false
                    if (IsApprovedvarmemberDOB == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedvarmemberDOB = true;
                }
                var TempMemberEmail = $("#txtMemberEmail").val();
                if (typeof $("#txtMemberEmail").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberEmail").attr("isapproved");
                    var IsApprovedMemberEmail = isapproved == "true" ? true : false
                    if (IsApprovedMemberEmail == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberEmail = true;
                }
                var TempSpouseTitle = $("#txtSpouseTitle").val();
                if (typeof $("#txtSpouseTitle").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtSpouseTitle").attr("isapproved");
                    var IsApprovedSpouseTitle = isapproved == "true" ? true : false
                    if (IsApprovedSpouseTitle == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedSpouseTitle = true;
                }
                var TempLocation = $("#txtlocation").val();
                if (typeof $("#txtlocation").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtlocation").attr("isapproved");
                   
                    var IsApprovedLocation = isapproved == "true" ? true : false
                    if (IsApprovedLocation == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                
                } else {
                    var IsApprovedLocation = true;
                }
                var TempSpouseFirstName = $("#txtMemberSpouseFirstName").val();

                if (typeof $("#txtMemberSpouseFirstName").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberSpouseFirstName").attr("isapproved");
                    var IsApprovedSpouseFirstName = isapproved == "true" ? true : false
                    if (IsApprovedSpouseFirstName == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedSpouseFirstName = true;
                }
                var TempSpouseMiddleName = $("#txtMemberSpouseMiddleName").val();

                if (typeof $("#txtMemberSpouseMiddleName").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberSpouseMiddleName").attr("isapproved");
                    var IsApprovedSpouseMiddleName = isapproved == "true" ? true : false
                    if (IsApprovedSpouseMiddleName == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedSpouseMiddleName = true;
                }
                var TempSpouseLastName = $("#txtMemberSpouseLastName").val();


                if (typeof $("#txtMemberSpouseLastName").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberSpouseLastName").attr("isapproved");
                    var IsApprovedSpouseLastName = isapproved == "true" ? true : false
                    if (IsApprovedSpouseLastName == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedSpouseLastName = true;
                }

                var Tempspouseemail = $("#txtMemberspouseemail").val();
                if (typeof $("#txtMemberspouseemail").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberspouseemail").attr("isapproved");
                    var IsApprovedspouseemail = isapproved == "true" ? true : false
                    if (IsApprovedspouseemail == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedspouseemail = true;
                }
                var Tempspousemobile1 = $("#txtspousemobile1").val();

                if (typeof $("#txtspousemobile1").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtspousemobile1").attr("isapproved");
                    var IsApprovedspousemobile1 = isapproved == "true" ? true : false
                    if (IsApprovedspousemobile1 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedspousemobile1 = true;
                }

                var TempvarspouseDOB = $("#txtSpouseDOB").val();

                if (typeof $("#txtSpouseDOB").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtSpouseDOB").attr("isapproved");
                    var IsApprovedvarspouseDOB = isapproved == "true" ? true : false
                    if (IsApprovedvarspouseDOB == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedvarspouseDOB = true;
                }
                var TempMemberNo = $("#txtMemberNo").val();

                if (typeof $("#txtMemberNo").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberNo").attr("isapproved");
                    var IsApprovedMemberNo = isapproved == "true" ? true : false
                    if (IsApprovedMemberNo == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberNo = true;
                }
                var TempvarMembersince = $("#txtMembersince").val();

                if (typeof $("#txtMembersince").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMembersince").attr("isapproved");
                    var IsApprovedvarMembersince = isapproved == "true" ? true : false
                    if (IsApprovedvarMembersince == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedvarMembersince = true;
                }
                var TempMemberShip_Type = $("#txtMemberShip_Type").val();

                if (typeof $("#txtMemberShip_Type").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberShip_Type").attr("isapproved");
                    var IsApprovedMemberShip_Type = isapproved == "true" ? true : false
                    if (IsApprovedMemberShip_Type == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberShip_Type = true;
                }
                var TempvarDOM = $("#txtdom").val();

                if (typeof $("#txtdom").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtdom").attr("isapproved");
                    var IsApprovedvarDOM = isapproved == "true" ? true : false
                    if (IsApprovedvarDOM == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedvarDOM = true;
                }
                var TempBusinessAddress1 = $("#txtBusinessAddress1").val();

                if (typeof $("#txtBusinessAddress1").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtBusinessAddress1").attr("isapproved");
                    var IsApprovedBusinessAddress1 = isapproved == "true" ? true : false
                    if (IsApprovedBusinessAddress1 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedBusinessAddress1 = true;
                }
                var TempBusinessAddress2 = $("#txtBusinessAddress2").val();


                if (typeof $("#txtBusinessAddress2").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtBusinessAddress2").attr("isapproved");
                    var IsApprovedBusinessAddress2 = isapproved == "true" ? true : false
                    if (IsApprovedBusinessAddress2 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedBusinessAddress2 = true;
                }
                var TempBusinessDistrict = $("#txtBusinessDistrict").val();


                if (typeof $("#txtBusinessDistrict").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtBusinessDistrict").attr("isapproved");
                    var IsApprovedBusinessDistrict = isapproved == "true" ? true : false
                    if (IsApprovedBusinessDistrict == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedBusinessDistrict = true;
                }
                var TempBusinessPincode = $("#txtBusinessPincode").val();

                if (typeof $("#txtBusinessPincode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtBusinessPincode").attr("isapproved");
                    var IsApprovedBusinessPincode = isapproved == "true" ? true : false
                    if (IsApprovedBusinessPincode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedBusinessPincode = true;
                }
                var TempBusinessState = $("#txtBusinessState").val();

                if (typeof $("#txtBusinessState").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtBusinessState").attr("isapproved");
                    var IsApprovedBusinessState = isapproved == "true" ? true : false
                    if (IsApprovedBusinessState == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedBusinessState = true;
                }
                var TempMemberBusinessPhone1 = $("#txtMemberBusinessPhone1").val();


                if (typeof $("#txtMemberBusinessPhone1").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberBusinessPhone1").attr("isapproved");
                    var IsApprovedMemberBusinessPhone1 = isapproved == "true" ? true : false
                    if (IsApprovedMemberBusinessPhone1 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberBusinessPhone1 = true;
                }
                var TempMemberBusinessPhone2 = $("#txtMemberBusinessPhone2").val();


                if (typeof $("#txtMemberBusinessPhone2").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberBusinessPhone2").attr("isapproved");
                    var IsApprovedMemberBusinessPhone2 = isapproved == "true" ? true : false
                    if (IsApprovedMemberBusinessPhone2 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberBusinessPhone2 = true;
                }

                var TempResidenceaddress1 = $("#txtresidenceaddress1").val();


                if (typeof $("#txtresidenceaddress1").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtresidenceaddress1").attr("isapproved");
                    var IsApprovedResidenceaddress1 = isapproved == "true" ? true : false
                    if (IsApprovedResidenceaddress1 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedResidenceaddress1 = true;
                }
                var TempResidenceaddress2 = $("#txtresidenceaddress2").val();

                if (typeof $("#txtresidenceaddress2").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtresidenceaddress2").attr("isapproved");
                    var IsApprovedResidenceaddress2 = isapproved == "true" ? true : false
                    if (IsApprovedResidenceaddress2 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedResidenceaddress2 = true;
                }
                var TempResidence_pincode = $("#txtresidence_pincode").val();

                if (typeof $("#txtresidence_pincode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtresidence_pincode").attr("isapproved");
                    var IsApprovedResidence_pincode = isapproved == "true" ? true : false
                    if (IsApprovedResidence_pincode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedResidence_pincode = true;
                }
                var TempResidencedistrict = $("#txtresidencedistrict").val();

                if (typeof $("#txtresidencedistrict").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtresidencedistrict").attr("isapproved");
                    var IsApprovedResidencedistrict = isapproved == "true" ? true : false
                    if (IsApprovedResidencedistrict == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedResidencedistrict = true;
                }

                var TempResidencestate = $("#txtresidencestate").val();

                if (typeof $("#txtresidencestate").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtresidencestate").attr("isapproved");
                    var IsApprovedResidencestate = isapproved == "true" ? true : false
                    if (IsApprovedResidencestate == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedResidencestate = true;
                }

                var TempMemberResidencePhone2 = $("#txtMemberResidencePhone2").val();

                if (typeof $("#txtMemberResidencePhone2").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberResidencePhone2").attr("isapproved");
                    var IsApprovedMemberResidencePhone2 = isapproved == "true" ? true : false
                    if (IsApprovedMemberResidencePhone2 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberResidencePhone2 = true;
                }
                var TempBusinessEmail = $("#txtbusinessEmail").val();

                if (typeof $("#txtbusinessEmail").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtbusinessEmail").attr("isapproved");
                    var IsApprovedBusinessEmail = isapproved == "true" ? true : false
                    if (IsApprovedBusinessEmail == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedBusinessEmail = true;
                }
                var TempSpouceNickName = $("#txtSpouseNickName").val();

                if (typeof $("#txtSpouseNickName").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtSpouseNickName").attr("isapproved");
                    var IsApprovedSpouceNickName = isapproved == "true" ? true : false
                    if (IsApprovedSpouceNickName == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedSpouceNickName = true;
                }
                var TempMemberNickName = $("#txtMemberNickName").val();

                if (typeof $("#txtMemberNickName").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberNickName").attr("isapproved");
                    var IsApprovedMemberNickName = isapproved == "true" ? true : false
                    if (IsApprovedMemberNickName == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberNickName = true;
                }
                var Tempmemberpic = $("#tempmemberimage").attr("src");

                if ($("#tempmemberimage").attr("isapproved") != 'undefined') {
                    var isapproved = $("#tempmemberimage").attr("isapproved");
                    var IsApprovedmemberpic = ($("#memberimageinfo").attr("isapproved").toLowerCase() == 'true' || $("#tempmemberimage").attr("isapproved").toLowerCase() == 'true') ? true : false
                    if (IsApprovedmemberpic == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                        if (isapproved == "false") {
                            totalRejected++;
                        }
                    }
                } else {
                    var IsApprovedmemberpic = true;
                }
                var Tempspousepic = $("#tempspouseimage").attr("src");
                if ($("#tempspouseimage").attr("isapproved") != 'undefined') {
                    var isapproved = $("#tempspouseimage").attr("isapproved");
                    var IsApprovedspousepic = ($("#spouseimageinfo").attr("isapproved").toLowerCase() == 'true' || $("#tempspouseimage").attr("isapproved").toLowerCase() == 'true') ? true : false
                    if (IsApprovedspousepic == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                        if (isapproved == "false") {
                            totalRejected++;
                        }
                    }
                } else {
                    var IsApprovedspousepic = true;
                }

                if (HiddenInstitutionType == 2) {
                    var TempHomeChurch = $("#txtHomeChurch").val();

                    if (typeof $("#txtHomeChurch").attr("isapproved") != 'undefined') {
                        var isapproved = $("#txtHomeChurch").attr("isapproved");
                        var IsApprovedHomeChurch = isapproved == "true" ? true : false
                        if (IsApprovedHomeChurch == true) {
                            AllRejected = false;
                            totalApproved++;
                        } else {
                            AllAccepted = false;
                        }
                        if (isapproved == "false") {
                            totalRejected++;
                        }
                    } else {
                        var IsApprovedHomeChurch = true;
                    }
                } else {
                    TempHomeChurch = null;
                    var IsApprovedHomeChurch = null;
                }
                var TempOccupation = $("#txtOccupation").val();

                if (typeof $("#txtOccupation").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtOccupation").attr("isapproved");
                    var IsApprovedOccupation = isapproved == "true" ? true : false
                    if (IsApprovedOccupation == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedOccupation = true;
                }
                var TempSpouseOccupation = $("#txtSpouseOccupation").val();

                if (typeof $("#txtSpouseOccupation").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtSpouseOccupation").attr("isapproved");
                    var IsApprovedSpouseOccupation = isapproved == "true" ? true : false
                    if (IsApprovedSpouseOccupation == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedSpouseOccupation = true;
                }

                var TempMember_Mobile1_Countrycode = $("#txtMemberMobile1_countrycode").val();

                if (typeof $("#txtMemberMobile1_countrycode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberMobile1_countrycode").attr("isapproved");
                    var IsApprovedMember_Mobile1_Countrycode = isapproved == "true" ? true : false
                    if (IsApprovedMember_Mobile1_Countrycode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMember_Mobile1_Countrycode = true;
                }

                var TempSpouse_Mobile1_Countrycode = $("#txtspousemobile1_countrycode").val();

                if (typeof $("#txtspousemobile1_countrycode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtspousemobile1_countrycode").attr("isapproved");
                    var IsApprovedSpouse_Mobile1_Countrycode = isapproved == "true" ? true : false
                    if (IsApprovedSpouse_Mobile1_Countrycode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedSpouse_Mobile1_Countrycode = true;
                }
                var TempMember_Business_Phone1_Countrycode = $("#txtMemberBusinessPhone1_countrycode").val();

                if (typeof $("#txtMemberBusinessPhone1_countrycode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberBusinessPhone1_countrycode").attr("isapproved");
                    var IsApprovedMember_Business_Phone1_Countrycode = isapproved == "true" ? true : false
                    if (IsApprovedMember_Business_Phone1_Countrycode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMember_Business_Phone1_Countrycode = true;
                }
                var TempMember_Business_Phone1_Areacode = $("#txtMemberBusinessPhone1_areacode").val();

                if (typeof $("#txtMemberBusinessPhone1_areacode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberBusinessPhone1_areacode").attr("isapproved");
                    var IsApprovedMember_Business_Phone1_Areacode = isapproved == "true" ? true : false
                    if (IsApprovedMember_Business_Phone1_Areacode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMember_Business_Phone1_Areacode = true;
                }
                var TempMember_Business_Phone2_Countrycode = $("#txtMemberBusinessPhone2_countrycode").val();

                if (typeof $("#txtMemberBusinessPhone2_countrycode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberBusinessPhone2_countrycode").attr("isapproved");
                    var IsApprovedMember_Business_Phone2_Countrycode = isapproved == "true" ? true : false
                    if (IsApprovedMember_Business_Phone2_Countrycode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMember_Business_Phone2_Countrycode = true;
                }

                if (HiddenInstitutionType == 2) {
                    var HomeChurch = $("#txtHomeChurch").val();
                    if (typeof $("#txtHomeChurch").attr("isapproved") != 'undefined') {
                        var isapproved = $("#txtHomeChurch").attr("isapproved");
                        var IsApprovedHomeChurch = isapproved == "true" ? true : false
                        if (IsApprovedHomeChurch == true) {
                            AllRejected = false;
                            totalApproved++;
                        } else {
                            AllAccepted = false;
                        }
                        if (isapproved == "false") {
                            totalRejected++;
                        }
                    } else {
                        var IsApprovedHomeChurch = true;
                    }

                } else {
                    HomeChurch = null;
                }

                var TempMemberBusinessPhone3 = $("#txtMemberBusinessPhone3").val();


                if (typeof $("#txtMemberBusinessPhone3").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberBusinessPhone3").attr("isapproved");
                    var IsApprovedMemberBusinessPhone3 = isapproved == "true" ? true : false
                    if (IsApprovedMemberBusinessPhone3 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberBusinessPhone3 = true;
                }

                var TempMemberBusinessPhone3 = $("#txtMemberBusinessPhone3").val();


                if (typeof $("#txtMemberBusinessPhone3").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberBusinessPhone3").attr("isapproved");
                    var IsApprovedMemberBusinessPhone3 = isapproved == "true" ? true : false
                    if (IsApprovedMemberBusinessPhone3 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberBusinessPhone3 = true;
                }

                var TempMember_Business_Phone3_Countrycode = $("#txtMemberBusinessPhone3_countrycode").val();

                if (typeof $("#txtMemberBusinessPhone3_countrycode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberBusinessPhone3_countrycode").attr("isapproved");
                    var IsApprovedMember_Business_Phone3_Countrycode = isapproved == "true" ? true : false
                    if (IsApprovedMember_Business_Phone3_Countrycode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMember_Business_Phone3_Countrycode = true;
                }
                var TempMember_Business_Phone3_Areacode = $("#txtMemberBusinessPhone3_areacode").val();

                if (typeof $("#txtMemberBusinessPhone3_areacode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberBusinessPhone3_areacode").attr("isapproved");
                    var IsApprovedMember_Business_Phone3_Areacode = isapproved == "true" ? true : false
                    if (IsApprovedMember_Business_Phone3_Areacode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMember_Business_Phone3_Areacode = true;
                }

                var Temp_Member_Blood_Group = $("#txtMemberBloodGroup").val();

                if (typeof $("#txtMemberBloodGroup").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberBloodGroup").attr("isapproved");
                    var IsApproved_Member_Blood_Group = isapproved == "true" ? true : false
                    if (IsApproved_Member_Blood_Group == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApproved_Member_Blood_Group = true;
                }

                var Temp_Spouse_Blood_Group = $("#txtSpouseBloodGroup").val();

                if (typeof $("#txtSpouseBloodGroup").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtSpouseBloodGroup").attr("isapproved");
                    var IsApproved_Spouse_Blood_Group = isapproved == "true" ? true : false
                    if (IsApproved_Spouse_Blood_Group == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApproved_Spouse_Blood_Group = true;
                }

                var TempTagCloud = $("#txtTagCloud").val();

                if (typeof $("#txtTagCloud").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtTagCloud").attr("isapproved");
                    var IsApprovedTagCloud = isapproved == "true" ? true : false
                    if (IsApprovedTagCloud == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedTagCloud = true;
                }

                var TempMemberResidencePhone1 = $("#txtMemberResidencePhone1").val();
                if (typeof $("#txtMemberResidencePhone1").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberResidencePhone1").attr("isapproved");
                    var IsApprovedMemberResidencePhone1 = isapproved == "true" ? true : false
                    if (IsApprovedMemberResidencePhone1 == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberResidencePhone1 = true;
                }

                var TempMemberResidencePhone1AreaCode = $("#txtMemberResidencePhone1_areacode").val();
                if (typeof $("#txtMemberResidencePhone1_areacode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberResidencePhone1_areacode").attr("isapproved");
                    var IsApprovedMemberResidencePhone1AreaCode = isapproved == "true" ? true : false
                    if (IsApprovedMemberResidencePhone1AreaCode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberResidencePhone1AreaCode = true;
                }

                var TempMemberResidencePhone1CountryCode = $("#txtMemberResidencePhone1_countrycode").val();
                if (typeof $("#txtMemberResidencePhone1_countrycode").attr("isapproved") != 'undefined') {
                    var isapproved = $("#txtMemberResidencePhone1_countrycode").attr("isapproved");
                    var IsApprovedMemberResidencePhone1CountryCode = isapproved == "true" ? true : false
                    if (IsApprovedMemberResidencePhone1CountryCode == true) {
                        AllRejected = false;
                        totalApproved++;
                    } else {
                        AllAccepted = false;
                    }
                    if (isapproved == "false") {
                        totalRejected++;
                    }
                } else {
                    var IsApprovedMemberResidencePhone1CountryCode = true;
                }
                var data = {
                    "MemberID": $("#hdnmemberid").val(),
                    "MemberTitle": MemberTitle,
                    "MemberTitleDescription": MemberTitleDescription,
                    "FirstName": FirstName,
                    "MiddleName": MiddleName,
                    "LastName": LastName,
                    "MemberMobile1": MemberMobile1,
                    "MemberMobile2": '',
                    "varmemberDOB": varmemberDOB,
                    "MemberEmail": MemberEmail,

                    "SpouseTitle": SpouseTitle,
                    "SpouseTitleDescription": SpouseTitleDescription,
                    "SpouseFirstName": SpouseFirstName,
                    "SpouseMiddleName": SpouseMiddleName,
                    "SpouseLastName": SpouseLastName,
                    "spouseemail": spouseemail,
                    "spousemobile1": spousemobile1,
                    "spousemobile2": '',
                    "varspouseDOB": varspouseDOB,

                    "MemberNo": MemberNo,
                    "location": location,
                    "varMembersince": varMembersince,
                    "MemberShip_Type": MemberShip_Type,
                    "varDOM": varDOM,
                    "BusinessAddress1": BusinessAddress1,
                    "BusinessAddress2": BusinessAddress2,
                    "BusinessPincode": BusinessPincode,
                    "BusinessDistrict": BusinessDistrict,
                    "BusinessState": BusinessState,
                    "MemberBusinessPhone1": MemberBusinessPhone1,
                    "MemberBusinessPhone2": MemberBusinessPhone2,

                    "Residenceaddress1": Residenceaddress1,
                    "Residenceaddress2": Residenceaddress2,
                    "Residence_pincode": Residence_pincode,
                    "Residencedistrict": Residencedistrict,
                    "Residencestate": Residencestate,
                    "MemberResidencePhone1": MemberResidencePhone1,
                    "MemberResidencePhone2": MemberResidencePhone2,
                    "BusinessEmail": BusinessEmail,
                    "MemberNickName": MemberNickName,
                    "SpouceNickName": SpouceNickName,
                    "HomeChurch": HomeChurch,
                    "Occupation": Occupation,
                    "SpouseOccupation": SpouseOccupation,
                    "DependantLst": _DependantLst,
                    "memberpic": memberpic,
                    "spousepic": spousepic,
                    "Member_Mobile1_Countrycode": MemberMobile1_countrycode,
                    "Spouse_Mobile1_Countrycode": SpouseMobile1_countrycode,
                    "Member_Business_Phone1_Countrycode": MemberBusinessPhone1_countrycode,
                    "Member_Business_Phone1_Areacode": MemberBusinessPhone1_areacode,
                    "Member_Business_Phone2_Countrycode": MemberBusinessPhone2_countrycode,
                    "MemberBusinessPhone3": MemberBusinessPhone3,
                    "Member_Business_Phone3_Countrycode": MemberBusinessPhone3_countrycode,
                    "Member_Business_Phone3_Areacode": MemberBusinessPhone3_areacode,

                    "MemberBloodGroup": MemberBloodGroup,
                    "SpouseBloodGroup": SpouseBloodGroup,
                    "MemberAdditionalInfoViewModel": Additionalinfo,
                    "MemberResidencePhone1": MemberResidencePhone1,
                    "MemberResidencePhone1CountryCode": MemberResidencePhone1CountryCode,
                    "MemberResidencePhone1AreaCode": MemberResidencePhone1AreaCode,
                    "MemberTag": TagCloud
                };

                var maildata = {
                    "MemberID": $("#hdnmemberid").val(),
                    "TempMemberTitle": TempMemberTitle,
                    "IsApprovedMemberTitle": IsApprovedMemberTitle,
                    "TempFirstName": TempFirstName,
                    "TempLocation" : TempLocation,
                    "IsApprovedFirstName": IsApprovedFirstName,
                    "TempMiddleName": TempMiddleName,
                    "IsApprovedMiddleName": IsApprovedMiddleName,
                    "TempLastName": TempLastName,
                    "IsApprovedLastName": IsApprovedLastName,
                    "TempMemberMobile1": TempMemberMobile1,
                    "IsApprovedMemberMobile1": IsApprovedMemberMobile1,
                    "TempvarmemberDOB": TempvarmemberDOB,
                    "IsApprovedvarmemberDOB": IsApprovedvarmemberDOB,
                    "TempMemberEmail": TempMemberEmail,
                    "IsApprovedMemberEmail": IsApprovedMemberEmail,
                    "location": location,
                    "TempSpouseTitle": TempSpouseTitle,
                    "IsApprovedSpouseTitle": IsApprovedSpouseTitle,
                    "TempSpouseFirstName": TempSpouseFirstName,
                    "IsApprovedSpouseFirstName": IsApprovedSpouseFirstName,
                    "IsApprovedLocation": IsApprovedLocation,
                    "TempSpouseMiddleName": TempSpouseMiddleName,
                    "IsApprovedSpouseMiddleName": IsApprovedSpouseMiddleName,
                    "TempSpouseLastName": TempSpouseLastName,
                    "IsApprovedSpouseLastName": IsApprovedSpouseLastName,
                    "Tempspouseemail": Tempspouseemail,
                    "IsApprovedspouseemail": IsApprovedspouseemail,
                    "Tempspousemobile1": Tempspousemobile1,
                    "IsApprovedspousemobile1": IsApprovedspousemobile1,
                    "TempvarspouseDOB": TempvarspouseDOB,
                    "IsApprovedvarspouseDOB": IsApprovedvarspouseDOB,

                    "TempMemberNo": TempMemberNo,
                    "IsApprovedMemberNo": IsApprovedMemberNo,
                    "TempvarMembersince": TempvarMembersince,
                    "IsApprovedvarMembersince": IsApprovedvarMembersince,
                    "TempMemberShip_Type": TempMemberShip_Type,
                    "IsApprovedMemberShip_Type": IsApprovedMemberShip_Type,
                    "TempvarDOM": TempvarDOM,
                    "IsApprovedvarDOM": IsApprovedvarDOM,
                    "TempBusinessAddress1": TempBusinessAddress1,
                    "IsApprovedBusinessAddress1": IsApprovedBusinessAddress1,
                    "TempBusinessAddress2": TempBusinessAddress2,
                    "IsApprovedBusinessAddress2": IsApprovedBusinessAddress2,
                    "TempBusinessPincode": TempBusinessPincode,
                    "IsApprovedBusinessPincode": IsApprovedBusinessPincode,
                    "TempBusinessDistrict": TempBusinessDistrict,
                    "IsApprovedBusinessDistrict": IsApprovedBusinessDistrict,
                    "TempBusinessState": TempBusinessState,
                    "IsApprovedBusinessState": IsApprovedBusinessState,
                    "TempMemberBusinessPhone1": TempMemberBusinessPhone1,
                    "IsApprovedMemberBusinessPhone1": IsApprovedMemberBusinessPhone1,
                    "TempMemberBusinessPhone2": TempMemberBusinessPhone2,
                    "IsApprovedMemberBusinessPhone2": IsApprovedMemberBusinessPhone2,

                    "TempResidenceaddress1": TempResidenceaddress1,
                    "IsApprovedResidenceaddress1": IsApprovedResidenceaddress1,
                    "TempResidenceaddress2": TempResidenceaddress2,
                    "IsApprovedResidenceaddress2": IsApprovedResidenceaddress2,
                    "TempResidence_pincode": TempResidence_pincode,
                    "IsApprovedResidence_pincode": IsApprovedResidence_pincode,
                    "TempResidencedistrict": TempResidencedistrict,
                    "IsApprovedResidencedistrict": IsApprovedResidencedistrict,
                    "TempResidencestate": TempResidencestate,
                    "IsApprovedResidencestate": IsApprovedResidencestate,
                    "TempMemberResidencePhone2": TempMemberResidencePhone2,
                    "TempBusinessEmail": TempBusinessEmail,
                    "IsApprovedBusinessEmail": IsApprovedBusinessEmail,
                    "TempMemberNickName": TempMemberNickName,
                    "IsApprovedMemberNickName": IsApprovedMemberNickName,
                    "TempSpouceNickName": TempSpouceNickName,
                    "IsApprovedSpouceNickName": IsApprovedSpouceNickName,
                    "TempHomeChurch": TempHomeChurch,
                    "IsApprovedHomeChurch": IsApprovedHomeChurch,
                    "TempOccupation": TempOccupation,
                    "IsApprovedOccupation": IsApprovedOccupation,
                    "TempSpouseOccupation": TempSpouseOccupation,
                    "IsApprovedSpouseOccupation": IsApprovedSpouseOccupation,
                    "PendingMailTempDependantList": _MailDependantLst,
                    "Tempmemberpic": Tempmemberpic,
                    "IsApprovedmemberpic": IsApprovedmemberpic,
                    "Tempspousepic": Tempspousepic,
                    "IsApprovedspousepic": IsApprovedspousepic,
                    "AllRejected": AllRejected,
                    "AllAccepted": AllAccepted,
                    "TempMember_Mobile1_Countrycode": TempMember_Mobile1_Countrycode,
                    "IsApprovedMember_Mobile1_Countrycode": IsApprovedMember_Mobile1_Countrycode,
                    "TempSpouse_Mobile1_Countrycode": TempSpouse_Mobile1_Countrycode,
                    "IsApprovedSpouse_Mobile1_Countrycode": IsApprovedSpouse_Mobile1_Countrycode,
                    "TempMember_Business_Phone1_Areacode": TempMember_Business_Phone1_Areacode,
                    "IsApprovedMember_Business_Phone1_Areacode": IsApprovedMember_Business_Phone1_Areacode,
                    "TempMember_Business_Phone1_Countrycode": TempMember_Business_Phone1_Countrycode,
                    "IsApprovedMember_Business_Phone1_Countrycode": IsApprovedMember_Business_Phone1_Countrycode,
                    "TempMember_Business_Phone2_Countrycode": TempMember_Business_Phone2_Countrycode,
                    "IsApprovedMember_Business_Phone2_Countrycode": IsApprovedMember_Business_Phone2_Countrycode,
                    "TempUserType": $("#hdnUserType").val(),
                    "TempMemberBusinessPhone3": TempMemberBusinessPhone3,
                    "IsApprovedMemberBusinessPhone3": IsApprovedMemberBusinessPhone3,
                    "TempMember_Business_Phone3_Areacode": TempMember_Business_Phone3_Areacode,
                    "IsApprovedMember_Business_Phone3_Areacode": IsApprovedMember_Business_Phone3_Areacode,
                    "TempMember_Business_Phone3_Countrycode": TempMember_Business_Phone3_Countrycode,
                    "IsApprovedMember_Business_Phone3_Countrycode": IsApprovedMember_Business_Phone3_Countrycode,
                    "TempMemberResidencePhone1": TempMemberResidencePhone1,
                    "IsApprovedMemberResidencePhone1": IsApprovedMemberResidencePhone1,
                    "TempMemberResidencePhone1AreaCode": TempMemberResidencePhone1AreaCode,
                    "IsApprovedMemberResidencePhone1AreaCode": IsApprovedMemberResidencePhone1AreaCode,
                    "TempMemberResidencePhone1CountryCode": TempMemberResidencePhone1CountryCode,
                    "IsApprovedMemberResidencePhone1CountryCode": IsApprovedMemberResidencePhone1CountryCode,

                    "Temp_Member_Blood_Group": Temp_Member_Blood_Group,
                    "IsApproved_Member_Blood_Group": IsApproved_Member_Blood_Group,
                    "Temp_Spouse_Blood_Group": Temp_Spouse_Blood_Group,
                    "IsApproved_Spouse_Blood_Group": IsApproved_Spouse_Blood_Group,
                    "TempTagCloud": TempTagCloud,
                    "IsApprovedTagCloud": IsApprovedTagCloud,


                };

                var isvalid = true;
                if ((MemberBusinessPhone1_countrycode != "" || MemberBusinessPhone1_areacode != "") && MemberBusinessPhone1 == "") {
                    swal({
                        title: '',
                        text: 'Office Number 1 cannot be blank',
                        type: 'error'
                    });
                    isvalid = false;
                } else if ((MemberBusinessPhone3_countrycode != "" || MemberBusinessPhone3_areacode != "") && MemberBusinessPhone3 == "") {
                    swal({
                        title: '',
                        text: 'Office Number 2 cannot be blank',
                        type: 'error'
                    });
                    isvalid = false;
                } else if ((MemberResidencePhone1CountryCode != "" || MemberResidencePhone1AreaCode != "") && MemberResidencePhone1 == "") {
                    swal({
                        title: '',
                        text: 'Residence Number 1 cannot be blank',
                        type: 'error'
                    });

                    isvalid = false;
                } else {
                    isvalid = true;
                }

                var totalSkipped = totalModified - totalApproved - totalRejected;
                swal({
                    title: 'Confirmation',
                    html:true,
                    text: `
                    <div style="display: flex; justify-content: center;">
    <table style="border: 1px solid black; border-collapse: collapse;">
        <tr>
            <th style="border: 1px solid black; padding: 10px;">Modifications</th>
            <td style="border: 1px solid black; padding: 10px;">${totalModified}</td>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 10px;">Approved</th>
            <td style="border: 1px solid black; padding: 10px;">${totalApproved}</td>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 10px;">Rejections</th>
            <td style="border: 1px solid black; padding: 10px;">${totalRejected}</td>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 10px;">Skipped<sup>**</sup></th>
            <td style="border: 1px solid black; padding: 10px;">${totalSkipped}</td>
        </tr>
    </table>
</div>
<br>
<div style="text-align: center;font-size: small;font-weight: bold;">
    **Kindly note that the ones skipped are also considered rejected, and their details will not be saved. To approve or reject, please click the<span style="display: inline-block;" class="infobtn2"></span> icon on the respective fields and perform the necessary actions.
</div>`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Continue',
                    cancelButtonText: 'Cancel'
                }, function (isConfirmed) {
                    if (!isConfirmed) {
                        console.log('cancelled');
                    } else {
                        // Execute the callback function if confirmed
                        $(".overlay").show();
        
                        if (isvalid == true) {
                            $.ajax({
                                url: $('#homeUrl').val() + $('#store-pending-details').val(),
                                type: "POST",
                                cache: false,
                                async: true,
                                data: {
                                    memberViewModel: data,
                                    pendingDataMailViewModel: maildata,
                                    '_csrf-backend': $("meta[name='csrf-token']").attr('content')
                                },
                                //  contentType: 'application/json',
                                success: function(result) {
                                    if (result.hasError == 0) {
                                        //code after success
                                        var msg = "Member Data Updated Successfully";
                                        swal({
                                            title: '',
                                            text: msg,
                                            type: 'success',
                                        })
                                        window.location.href = $('#homeUrl').val() + 'member/';
                                        $(".overlay").hide();
                                    } else {
                                        $(".overlay").hide();
                                        var msg = result?.message ? result.message : "Member Data Updation Failed";
                                        setTimeout(function() {
                                            swal({
                                                title: 'Error',
                                                text: msg,
                                                type: 'error',
                                            });
                                        }, 100);
                                    }
                                },
                                error: function(er) {
                                    $(".overlay").hide();
                                    swal({
                                        title: 'Member ',
                                        text: 'An error occured while processing the request.',
                                        type: 'error',
                                    })
                                    //                    alert(er);
                                }
                            })
        
                        } else {
                            $(".overlay").hide();
                        }
                    }
                });
            });


            $("#btnAddDepend").on('click', function() {});

            $('#addfamilyunit').on('click', function() {})
        },
        _onChangeEvents: function() {

        },
        _onLoadEvents: function() {
            var __this = this
            $('#test').tagsinput('items')

            $('#sub-form').on('beforeValidate', function(event, messages) {})

            $('#sub-form').on('afterValidate', function(event, messages) {})

            $(document).on("keydown", ".bootstrap-tagsinput", function(e) {
                if (e.keyCode == 13) {

                    return false;
                }

            });

        },
        _onKeyEvents: function() {
            var __this = this
        },



        // funtion for validating controls before submit
        _ValidateBeforeSave: function() {
            var regexEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            var regexName = /^[a-zA-Z]+([ ][a-zA-Z]+)*$/;
            //var regexPhone = /^\d{10}$/;
            var regexPhone = /^\d{6,}$/;
            var regexcountrycode = /^(\+(?:\d{2,3}$))|(\+(?:\d{1})$)|(?:\d{2,3}$)|(?:\d{1}$)/;
            var regexareacode = /^\d{0,5}$/;

            var regexpin = /^.{4,8}$/;
            //var regexOfficePhone = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
            // var regexOfficePhone = /^(?=.*[0-9])[- ()0-9]+$/;
            var regexOfficePhone = /^\d{7,10}$/;
            var boolresult = true;
            var arr = [];
            $("#tags_1_tagsinput span:odd").each(function(index, elem) {
                arr.push($(this).text().trim());
            });

            if ($("#txtFirstName").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Member First Name Cannot be blank !");
                $("#txtFirstName").focus();

                boolresult = false;
            } else if ($("#txtLastName").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Member Last Name Cannot be blank !");
                $("#txtLastName").focus();
                boolresult = false;
            }
            if (arr.toString().length > 100) {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("tag length exceeds !");
                $("#tags_1_tagsinput").focus();
                boolresult = false;
            } else if ($("#txtMemberEmail").val() != "" && !regexEmail.test($("#txtMemberEmail").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Member Email not in proper format !");
                $("#txtMemberEmail").focus();
                boolresult = false;
            } else if ($("#txtMemberspouseemail").val() != "" && !regexEmail.test($("#txtMemberspouseemail").val())) {
                alert("Spouse Email not in proper format !");
                $('.nav-tabs a[href="#member"]').tab('show');
                $("#txtMemberspouseemail").focus();
                boolresult = false;
            } else if ($("#txtMemberspouseemail").val() != "" && ($("#txtMemberspouseemail").val() == $("#txtMemberEmail").val())) {
                alert("Member and Spouse Email cannot be same!");
                $('.nav-tabs a[href="#member"]').tab('show');
                $("#txtMemberspouseemail").focus();
                boolresult = false;
            } else if ($("#txtMemberspouseemail").val() != "" && $("#txtMemberSpouseFirstName").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Spouse First Name Cannot be blank !");
                $("#txtMemberSpouseFirstName").focus();
                boolresult = false;
            } else if ($("#txtMemberspouseemail").val() != "" && !regexName.test($("#txtMemberSpouseFirstName").val())) {

                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Spouse First Name not in proper format !");
                $("#txtMemberSpouseFirstName").focus();
                boolresult = false;
            } else if ($("#txtMemberSpouseFirstName").val() != "" && $("#txtMemberSpouseLastName").val() == "") {

                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Spouse Last Name Cannot be blank !");
                $("#txtMemberSpouseLastName").focus();
                boolresult = false;
            } else if ($("#txtspousemobile1").val() != "" && $("#txtMemberSpouseLastName").val() == "") {

                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Spouse Last Name Cannot be blank !");
                $("#txtMemberSpouseLastName").focus();
                boolresult = false;
            } else if ($("#txtspousemobile1").val() != "" && !regexName.test($("#txtMemberSpouseLastName").val())) {

                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Spouse Last Name not in proper format !");
                $("#txtMemberSpouseLastName").focus();
                boolresult = false;
            } else if ($("#txtMemberBusinessPhone1").val() != "" && !regexOfficePhone.test($("#txtMemberBusinessPhone1").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Office phone Number not in proper format !");
                $("#txtMemberBusinessPhone1").focus();
                boolresult = false;
            } else if ($("#txtMemberBusinessPhone1_countrycode").val() != "" && !regexcountrycode.test($("#txtMemberBusinessPhone1_countrycode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Countrycode not in proper format !");
                $("#txtMemberBusinessPhone1_countrycode").focus();
                boolresult = false;
            } else if ($("#txtMemberBusinessPhone1_areacode").val() != "" && !regexareacode.test($("#txtMemberBusinessPhone1_areacode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Areacode not in proper format !");
                $("#txtMemberBusinessPhone1_areacode").focus();
                boolresult = false;
            } else if ($("#txtbusinessEmail").val() != "" && !regexEmail.test($("#txtbusinessEmail").val())) {
                alert("member Residential Email not in proper format !");
                $('.nav-tabs a[href="#member"]').tab('show');
                $("#txtbusinessEmail").focus();
                boolresult = false;
            } else if ($("#txtMemberBusinessPhone3_countrycode").val() != "" && !regexcountrycode.test($("#txtMemberBusinessPhone3_countrycode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert(" Business Phone 2 Countrycode not in proper format !");
                $("#txtMemberBusinessPhone1_countrycode").focus();
                boolresult = false;
            } else if ($("#txtMemberBusinessPhone3_areacode").val() != "" && $("#txtMemberBusinessPhone3").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Office phone number2 cannot be blank!");
                $("#txtMemberBusinessPhone1").focus();
                boolresult = false;
            } else if ($("#txtMemberBusinessPhone3_countrycode").val() != "" && $("#txtMemberBusinessPhone3").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Office phone number 2 cannot be blank!");
                $("#txtMemberBusinessPhone1").focus();
                boolresult = false;
            } else if ($("#txtMemberBusinessPhone3_areacode").val() != "" && !regexareacode.test($("#txtMemberBusinessPhone3_areacode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                alert("Areacode not in proper format !");
                $("#txtMemberBusinessPhone3_areacode").focus();
                boolresult = false;
            } else if ($(".tempDependantTitle").val() == "") {
                alert("Please select Dependant Title")
                $(this).focus();
                boolresult = false;
            } else if ($(".tempDependantName").val().trim() == "") {
                alert("Dependant Name Cannot be blank !")
                $(this).focus();
                boolresult = false;
            } else if ($(".tempdpmartialstatus").val() == 2) {
                if ($(".tempdependantspousetitleid").val() == "") {
                    alert("Please select dependant Spouse Title")
                    $(this).focus();
                    boolresult = false;
                } else if ($(".tempDependantSpousName").val().trim() == "") {
                    alert("Dependant spouse Name Cannot be blank !")
                    $(this).focus();
                    boolresult = false;
                }
            } else {
                boolresult = true;

            }

            return boolresult;

        },



        // public members
        buildPage: function() {
            this._InitializePageBuilder()
        }

    })
var memberApprovalJS = new Remember.memberApproval.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function() {
    jsFramework.lib.ui.pageBinder.addPageBuilder(memberApprovalJS)
})
