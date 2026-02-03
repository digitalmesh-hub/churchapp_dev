jsFramework.lib.core.utils.registerNamespace('Remember.memberCreate.ui')
Remember.memberCreate.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
            $(".updateDependant").on("click", function(e) {
                var ajaxUrl = $('#homeUrl').val() + $('#dependant-edit-url').val();
                var id = $(this).attr('data-id');
                var memberId = $("#memberId").val();
                $.post(ajaxUrl, // Ajax Post URL
                    {
                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                        id: id,
                        memberId: memberId,
                        async: false,
                    }, // Data
                    function(res) {
                        if (typeof(res) !== 'undefined') {
                            __this._SetEditDependant(res);
                            __this._basicEvents();
                            return false
                        } else {
                            swal({
                                title: 'Member ',
                                text: 'An error occured while processing the request.',
                                type: 'error',
                            })
                        }
                    }
                )
            });

            $(".btn-dependant-delete").on("click", function() {
                var ajaxUrl = $('#homeUrl').val() + $('#dependant-delete-url').val();
                var id = $(this).attr('data-dependant-id');
                var memberId = $("#memberId").val();
                var formType = $("#formType").val();
                swal({
                    title: 'Are you sure?',
                    text: 'Do you want to delete dependent?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn-danger',
                    confirmButtonText: 'Yes',
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function() {
                    $.post(ajaxUrl, // Ajax Post URL
                        {
                            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                            id: id,
                            memberId: memberId,
                            formType: formType,
                            async: false,

                        }, // Data
                        function(res) {
                            if (typeof(res) !== 'undefined') {
                                $('#divdependants').html(res);
                                __this._basicEvents();
                                swal({
                                    title: 'Success',
                                    text: 'Successfully deleted the dependent. ',
                                    type: 'success',
                                })
                                return false
                            } else {
                                swal({
                                    title: 'Member ',
                                    text: 'An error occurred while processing the request.',
                                    type: 'error',
                                })
                            }
                        }
                    );
                });

            });

            $("#btnremovespouse").on("click", function() { /*remove entire spouse details */
                var ajaxUrl = $('#homeUrl').val() + $('#remove-spouse').val()
                var memberId = $("#hdnmemberid").val();
                swal({
                        title: 'Are you sure?',
                        text: 'Do you want to delete spouse?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'btn-danger',
                        confirmButtonText: 'Yes',
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    },
                    function() {
                        $("#SpouseTitle option:selected").attr("selected", null);
                        $("#txtMemberSpouseFirstName").val("");
                        $("#txtMemberSpouseMiddleName").val("");
                        $("#txtMemberSpouseLastName").val("");
                        $("#txtMemberspouseemail").val("");
                        $("#txtspousemobile1").val("");
                        $("#txtSpouseDOB").val("");
                        $("#txtSpouseNickName").val("");
                        $("#txtSpouseBloodgroup").val("");
                        $("#txtspousemobile1_countrycode").val("");
                        $("#txtSpouseOccupation").val("");

                        $.post(ajaxUrl, // Ajax Post URL
                            {
                                '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                                memberId: memberId
                            }, // Data
                            function(res) {
                                if (typeof(res) !== 'undefined' && res.status === 'success') {
                                    swal({
                                            title: 'Success',
                                            text: 'Spouse details removed successfully',
                                            type: 'success'
                                        },
                                        function() {
                                            window.location.href = $('#homeUrl').val() + 'member/';
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
            });

            $("#btnremoveprimarymember").on("click", function() {
                swal({
                        title: 'Are you sure?',
                        text: 'Do you want to delete primary member?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'btn-danger',
                        confirmButtonText: 'Yes',
                        closeOnConfirm: false,
                        showLoaderOnConfirm: false
                    },
                    function() {
                        var memberid = $("#hdnmemberid").val();
                        if (__this._ValidateBeforeDeleteMember() == true) {
                            $("#memberimage").attr("src", $("#spouseimage").attr('src'));
                            $("#spouseimage").attr('src', $('#base-url').val() + "/theme/images/default-user.png");
                            $("#MemberTitle option:selected").attr("selected", null);
                            var spousetitle = $('#SpouseTitle').val();
                            //$("#MemberTitle option[value='" + spousetitle + "']").attr("selected", "selected");
                            $('#MemberTitle').val($('#SpouseTitle').val());
                            var spousefirstname = $("#txtMemberSpouseFirstName").val();
                            var spousemiddlename = $("#txtMemberSpouseMiddleName").val();
                            var spouselastname = $("#txtMemberSpouseLastName").val();
                            var spouse_mobile1_countrycode = $("#txtspousemobile1_countrycode").val();
                            var spousemobilenumber1 = $("#txtspousemobile1").val();
                            var spousedob = $("#txtSpouseDOB").val();
                            var spouseemail = $("#txtMemberspouseemail").val();
                            var spousenickname = $("#txtSpouseNickName").val();
                            var spousebloodgroup = $("#txtSpouseBloodgroup").val();
                            $("#txtFirstName").val(spousefirstname);
                            $("#txtMiddleName").val(spousemiddlename);
                            $("#txtLastName").val(spouselastname);
                            $("#txtMemberMobile1_countrycode").val(spouse_mobile1_countrycode)
                            $("#txtMemberMobile1").val(spousemobilenumber1);
                            $("#txtMemberDOB").val(spousedob);
                            $("#txtOccupation").val($('#txtSpouseOccupation').val());
                            $("#txtMemberEmail").val(spouseemail);
                            $("#txtMemberNickName").val(spousenickname);
                            $("#txtMemberBloodgroup").val(spousebloodgroup);
                            $("#SpouseTitle option:selected").attr("selected", null);
                            $("#txtMemberSpouseFirstName").val("");
                            $('#txtSpouseOccupation').val('');
                            $("#txtMemberSpouseMiddleName").val("");
                            $("#txtMemberSpouseLastName").val("");
                            $("#txtMemberspouseemail").val("");
                            $("#txtspousemobile1_countrycode").val("");
                            $("#txtspousemobile1").val("");
                            $("#txtSpouseDOB").val("");
                            $("#txtSpouseNickName").val("");
                            $("#txtSpouseBloodgroup").val("");
                            $("#txtdom").val("");
                            var data = {
                                "MemberID": $("#hdnmemberid").val(),
                                "MemberTitle": $('#MemberTitle :selected').val(),
                                "FirstName": $("#txtFirstName").val(),
                                "MiddleName": $("#txtMiddleName").val(),
                                "LastName": $("#txtLastName").val(),
                                "Member_Mobile1_Countrycode": $("#txtMemberMobile1_countrycode").val(),
                                "MemberMobile1": $("#txtMemberMobile1").val(),
                                "MemberMobile2": '',
                                "memberpic": $("#memberimage").attr("src"),
                                "varmemberDOB": $("#txtMemberDOB").val(),
                                "MemberEmail": $("#txtMemberEmail").val(),
                                "MemberNickName": $("#txtMemberNickName").val(),
                                "Occupation": $("#txtOccupation").val(),
                                "MemberBloodGroup": $("#txtMemberBloodgroup").val(),
                            };

                            $.post($('#homeUrl').val() + $('#remove-member').val(), // Ajax Post URL
                                {
                                    '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                                    data: data
                                }, // Data
                                function(res) {
                                    if (typeof(res) !== 'undefined' && res.status === 'success') {
                                        swal({
                                                title: 'Success',
                                                text: 'Member details removed successfully',
                                                type: 'success'
                                            },
                                            function() {
                                                window.location.href = $('#homeUrl').val() + 'member/';
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
                                });
                        }
                    }
                )
            });
            $("#btnsentmail").unbind().click(function() {
                $('#btnsentmail').attr('disabled', 'true')
                genratedUrl = $("#txtgeneratedurl").val();
                var memberId = $("#memberId").val();
                var email = $("#txtMemberEmail").val();
                if (email == '') {
                    swal({
                        title: '',
                        text: 'Member email id not available',
                        type: 'error',
                        timer: 2000
                    });
                    $('#btnsentmail').removeAttr("disabled");
                    return false;
                }
                var ajaxUrl = $('#homeUrl').val() + $('#sent-genarated-link').val();
                $.post(ajaxUrl, // Ajax Post URL
                    {
                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                        genratedUrl: genratedUrl,
                        memberId: memberId,
                        async: false,

                    }, // Data
                    function(res) {
                        if (typeof(res) !== 'undefined') {
                            if (res == "success") {
                                swal({
                                    title: '',
                                    text: 'Mail sent successfully',
                                    type: 'success',
                                });
                            } else if (res == "email_error") {
                                swal({
                                    title: '',
                                    text: 'Member email id not available',
                                    type: 'error',
                                    timer: 2000
                                });
                            } else {
                                swal({
                                    title: '',
                                    text: 'Unable to send email',
                                    type: 'error'
                                })
                            }
                        } else {
                            swal({
                                title: '',
                                text: 'Unable to send email',
                                type: 'error'
                            })
                        }
                    }
                );
                $('#btnsentmail').removeAttr("disabled");
                $(".overlay").hide();
            });

            $("#btngenerateurl").on("click", function() {
                var memberId = $("#memberId").val();
                var ajaxUrl = $('#homeUrl').val() + $('#generate-member-url').val();
                $.post(ajaxUrl, // Ajax Post URL
                    {
                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                        memberId: memberId,
                        memberId: memberId,
                        async: false,

                    }, // Data
                    function(res) {
                        if (typeof(res) !== 'undefined') {
                            $("#txtgeneratedurl").val(res);
                            $("#txtgeneratedurl").attr('readonly', 'true');
                            $('#btnsentmail').show();
                            $('#btnsentmail').removeAttr("disabled");
                            //__this._basicEvents();                      
                            return false
                        } else {
                            swal({
                                title: 'Generate URL ',
                                text: 'An error occured while processing the request.',
                                type: 'error',
                            })
                        }
                    }
                );
            });
            //
            $(".btngenerateprofileurl").on("click", function() {
                var memberId = $("#memberId").val();
                var ajaxUrl = $('#homeUrl').val() + $('#get-member-profile-url').val();
                var memberType = $(this).attr("data-membertype");
                // console.log('member Id -> '+memberId);
                // console.log('url -> '+ajaxUrl);
                // console.log('member Type -> '+memberType);
                if(memberType == 'member'){
                    $fullname = (($("#MemberTitle option:selected").text())?$("#MemberTitle option:selected").text()+ ' ':'') + 
                                (($("#txtFirstName").val())?$("#txtFirstName").val()+' ':'') + 
                                (($("#txtMiddleName").val())?$("#txtMiddleName").val()+' ':'') + 
                                (($("#txtLastName").val())?$("#txtLastName").val()+' ':'');
                    $profileImage = $("#memberimage").attr('src');
                }
                else{
                    $fullname = (($("#SpouseTitle option:selected").text())?$("#SpouseTitle option:selected").text()+ ' ':'') + 
                                (($("#txtMemberSpouseFirstName").val())?$("#txtMemberSpouseFirstName").val()+' ':'') + 
                                (($("#txtMemberSpouseMiddleName").val())?$("#txtMemberSpouseMiddleName").val()+' ':'') + 
                                (($("#txtMemberSpouseLastName").val())?$("#txtMemberSpouseLastName").val()+' ':'');
                    $profileImage = $("#spouseimage").attr('src');
                }
                               
                // return false;
                $.post(ajaxUrl, // Ajax Post URL
                    {
                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                        memberId: memberId,
                        memberType: memberType,
                        async: false,
                    }, // Data
                    function(res) {
                        if (typeof(res) !== 'undefined') {
                            $("#memberProfileImage").attr("src",$profileImage);
                            $("#profileMemberName").text($fullname)
                            $("#profileMemberUrl").val(res)
                            $('#exampleModalCenter').modal('show');
                            //__this._basicEvents();                      
                            return false
                        } else {
                            swal({
                                title: 'Get Member Profile URL ',
                                text: 'An error occured while processing the request.',
                                type: 'error',
                            })
                        }
                    }
                );

              
            });

            $(document).on('click', '.valid', function(e) {
                e.preventDefault();
                if (e.keyCode == 13) {
                    return false
                }
                $('.valid').attr('disabled', 'true')

                if (__this._ValidateBeforeSave()) {
                    $('#form').submit();
                } else {
                    $('.valid').removeAttr("disabled")
                }
            });

            $("#btnCancelDepend").on('click', function() {
                $('#txtDependantName').val("");
                $('#DPMartialStatus').val("");
                $("#dependantspousetitleid").val("");
                $("#txtdependantspousename").val("");
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

            $("#btnremovemember").on("click", function() {
                memberimage.src = $('#base-url').val() + "/theme/images/default-user.png";
                $('#memberfile').val("");
                var memberId = $("#memberId").val();
                var ajaxUrl = $('#homeUrl').val() + $('#remove-member-pic').val();
                $.post(ajaxUrl, // Ajax Post URL
                    {
                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                        memberId: memberId,

                    }, // Data
                    function(res) {}
                );
            });

            $("#btnspouseremove").on("click", function() { /*remove image */
                spouseimage.src = $('#base-url').val() + "/theme/images/default-user.png";
                $('#spousefile').val("");
                $("#spousefile").replaceWith($("#spousefile").clone());
                _spouseimage = "removed";

                var memberId = $("#memberId").val();
                var ajaxUrl = $('#homeUrl').val() + $('#remove-member-spousepic').val();
                $.post(ajaxUrl, // Ajax Post URL
                    {
                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                        memberId: memberId,

                    }, // Data
                    function(res) {}
                );
            });


            $("#btnAddDepend").on('click', function() {
                $('#btnAddDepend').attr('disabled', 'true')
                var ajaxUrl = $('#homeUrl').val() + $('#sotre-depentdant-url').val();
                var data = new Object();

                if ($("#extendeddependant-image")[0].length)
                    if ($("#extendeddependant-image")[0].files[0].size > 5242880) {
                        $('#btnAddDepend').removeAttr("disabled");
                        swal({
                            title: 'Member ',
                            text: 'Please upload files less than 5 MB',
                            type: 'error',
                        })

                        return;
                    }
                if ($("#dynamicmodel-spouseimage")[0].length)
                    if ($("#dynamicmodel-spouseimage")[0].files[0].size > 5242880) {
                        $('#btnAddDepend').removeAttr("disabled");
                        swal({
                            title: 'Member ',
                            text: 'Please upload files less than 5 MB',
                            type: 'error',
                        })

                        return;
                    }
                if (__this._ValidateDependantSave()) {
                    //                  //  data.dependantImage = $("#extendeddependant-image")[0].files[0];
                    //                   // data.dependantSpouseImage = $("#dynamicmodel-spouseimage")[0].files[0];
                    //                    
                    //                    var formDetails = new FormData();
                    //                      formDetails.append('dependantImage', $("#extendeddependant-image")[0].files[0]);
                    //                      formDetails.append('dependantSpouseImage', $("#dynamicmodel-spouseimage")[0].files[0]);
                    //                      
                    //                    data.dependantName = $('#txtDependantName').val();
                    //                    data.dependantMartialStatus = $('#DPMartialStatus').val();
                    //                    data.dependantspousetitleid = $("#dependantspousetitleid").val();
                    //                    data.dependantspousename = $("#txtdependantspousename").val();
                    //                    data.dependantspousedob = $("#dependantspousedob").val();
                    //                    data.dependantweddingdate = $("#dependantweddingdate").val();
                    //                    data.dependantId = $("#dependantId").val();
                    //                    data.spouseDependantId = $("#spouseDependantId").val();
                    //                    data.dependantdob = $("#dependantdob").val();
                    //                    data.relation = $("#relation").val();
                    //                    data.dependantTitleId = $("#dependantTitleId").val();
                    //                    data.memberId = $("#memberId").val();
                    //                    data.formType = $("#formType").val();
                    //                    var myData = JSON.stringify(data);
                    var formDetails = new FormData();
                    formDetails.append('dependantImage', $("#extendeddependant-image")[0].files[0]);
                    formDetails.append('dependantSpouseImage', $("#dynamicmodel-spouseimage")[0].files[0]);

                    data['dependantName'] = $('#txtDependantName').val();
                    data['dependantMartialStatus'] = $('#DPMartialStatus').val();
                    data['dependantspousetitleid'] = $("#dependantspousetitleid").val();
                    data['dependantspousename'] = $("#txtdependantspousename").val();
                    data['dependantspousedob'] = $("#dependantspousedob").val();
                    data['dependantweddingdate'] = $("#dependantweddingdate").val();
                    data['dependantId'] = $("#dependantId").val();
                    data['spouseDependantId'] = $("#spouseDependantId").val();
                    data['dependantdob'] = $("#dependantdob").val();
                    data['relation'] = $("#relation").val();
                    data['dependantTitleId'] = $("#dependantTitleId").val();
                    data['dependantMobileCountryCode'] = $("#dependantMobileCountryCode").val();
                    data['dependantMobile'] = $("#dependantMobile").val();
                    data['dependantOccupation'] = $("#txtDependantOccupation").val();
                    data['dependantActive'] = $("#chkDependantActive").is(':checked') ? 1 : 0;
                    data['dependantConfirmed'] = $("#chkDependantConfirmed").is(':checked') ? 1 : 0;
                    data['dependantSpouseMobileCountryCode'] = $("#dependantSpouseMobileCountryCode").val();
                    data['dependantSpouseMobile'] = $("#dependantSpouseMobile").val();
                    data['dependantspouseoccupation'] = $("#txtdependantspouseoccupation").val();
                    data['dependantspouseactive'] = $("#dependantspouseactive").is(':checked') ? 1 : 0;
                    data['dependantspouseconfirmed'] = $("#dependantspouseconfirmed").is(':checked') ? 1 : 0;

                    //data['dependantImage'] = Imagedata;
                    formDetails.append('data', JSON.stringify(data));
                    var memberId = $("#memberId").val();
                    var formType = $("#formType").val();
                    formDetails.append('_csrf-backend', $("meta[name='csrf-token']").attr('content'));
                    formDetails.append('memberId', memberId);
                    formDetails.append('formType', formType);

                    $.ajax({
                        url: ajaxUrl,
                        type: "POST",
                        processData: false,
                        contentType: false,
                        cache: false,
                        async: true,
                        data: formDetails,
                        headers : {'X-CSRF-Token' : yii.getCsrfToken()},
                        success: function(response) {
                            if (typeof(response) !== 'undefined') {
                                $('#divdependants').html(response);
                                __this._basicEvents();
                                swal({
                                    title: 'Success ',
                                    text: 'Data added successfully',
                                    type: 'success',
                                })
                                return false
                            } else {
                                swal({
                                    title: 'Member ',
                                    text: 'An error occurred while processing the request.',
                                    type: 'error'
                                });
                            }
                        },
                        error: function(er) {
                            swal({
                                title: 'Member ',
                                text: 'An error occurred while processing the request.',
                                type: 'error'
                            });
                        }
                    });
                    $('#btnAddDepend').removeAttr("disabled");
                }
                $('#btnAddDepend').removeAttr("disabled");
            });

            $('#addfamilyunit').on('click', function() {})
        },
        _onChangeEvents: function() {
            var __this = this
            $("#DPMartialStatus").on('change', function() {
                if ($('#DPMartialStatus').val() == 2) {
                    $('#dependantspousediv').removeClass('nodisplay');
                } else {
                    if ($('#DPMartialStatus').val() == 1) {
                        $("#dependantspousetitleid").val("");
                        $("#txtdependantspousename").val("");
                        $("#dependantspousedob").val("");
                        $("#dependantweddingdate").val("");
                        $("#hdndependantspouseid").val("-1");
                        $('#dependantspousediv').addClass('nodisplay');
                    }
                }
            });

            $('#txtspousemobile1').on('change', function() {
                __this._spouseDropDownToggle()
                __this._updateSpouseRequiredIndicators()

            });
            $('#txtMemberSpouseFirstName').on('change', function() {
                __this._weddingAnnivesaryToogle()

            });
            $('#SpouseTitle, #txtMemberspouseemail, #txtSpouseDOB, #txtspousemobile1_countrycode').on('change keyup', function() {
                __this._updateSpouseRequiredIndicators()
            });
            $(".tempdpmartialstatus").change(function() {

                var id = $(this).attr("dependantid");

                if ($(this).val() == 2) {
                    $('#dependentspousediv_' + id).removeClass('nodisplay');
                } else {
                    if ($(this).val() == 1) {
                        $("#tempdependantspousetitleid_" + id).val("");
                        $("#tempDependantSpousName_" + id).val("");
                        $("#tempDependantDob_" + id).val("");
                        $("#tempDependantwdate_" + id).val("");
                        //  $("#" + id).attr("dependantid", "-1");
                        $('#dependentspousediv_' + id).addClass('nodisplay');
                    }
                }
            });

            $('#memberfile').on('change', function(e) {
                //EDdocument.getElementById('Imagefile').onchange = function (e) {
                _memberimage = "";
                // Get the first file in the FileList object
                var imageFile = this.files[0];
                var type = imageFile.type;
                if (this.files[0].size > 5242880) {
                    $('#memberfile').val('');
                    swal({
                        title: 'Member ',
                        text: 'Please upload files less than 2MB',
                        type: 'error',
                    });
                    if ($("#memberimage").attr("src") != $('#base-url').val() + "/theme/images/default-user.png") {
                        memberimage.src = $('#base-url').val() + "/theme/images/default-user.png";
                    }
                    return;
                }
                if (imageFile.type != 'image/png' && imageFile.type != 'image/jpg' && imageFile.type != 'image/jpeg') {
                    swal({
                        title: 'Member ',
                        text: 'Please use one of these file types : .png , .jpg  or jpeg ',
                        type: 'error',
                    })
                    if ($("#memberimage").attr("src") != $('#base-url').val() + "/theme/images/default-user.png") {
                        memberimage.src = $('#base-url').val() + "/theme/images/default-user.png";
                    }
                    // $("#memberfile").replaceWith($("#memberfile").clone());
                } else {
                    // get a local URL representation of the image blob
                    var url = window.URL.createObjectURL(imageFile);
                    // Now use your newly created URL!
                    memberimage.src = url;
                }
            });
            $("#spousefile").on("change", function() {
                //EDdocument.getElementById('Imagefile').onchange = function (e) {
                _spouseimage = "";
                // Get the first file in the FileList object
                var imageFile = this.files[0];
                var type = imageFile.type;
                if (this.files[0].size > 5242880) {
                    $("#spousefile").val('');
                    swal({
                        title: 'Member ',
                        text: 'Please upload files less than 2MB',
                        type: 'error',
                    });
                    if ($("#memberimage").attr("src") != $('#base-url').val() + "/theme/images/default-user.png") {
                        spouseimage.src = $('#base-url').val() + "/theme/images/default-user.png";
                    }
                    return;
                }
                if (imageFile.type != 'image/png' && imageFile.type != 'image/jpg' && imageFile.type != 'image/jpeg') {
                    swal({
                        title: 'Member ',
                        text: 'Please use one of these file types : .png , .jpg  or jpeg ',
                        type: 'error',
                    })
                    if ($("#spouseimage").attr("src") != $('#base-url').val() + "/theme/images/default-user.png") {

                        spouseimage.src = $('#base-url').val() + "/theme/images/default-user.png";
                    }
                    // $("#spousefile").replaceWith($("#spousefile").clone());
                } else {
                    // get a local URL representation of the image blob
                    var url = window.URL.createObjectURL(imageFile);
                    // Now use your newly created URL!
                    spouseimage.src = url;
                }

            });
            $(document).on('change', '#role-category', function() {
                var roleCategoryId = $('#role-category').val()
                __this._memberroles(roleCategoryId)
            })
            $(document).on('change', '#spouse-role-category', function() {
                var roleCategoryId = $('#spouse-role-category').val()
                __this._spouseroles(roleCategoryId)
            })

        },
        _memberroles: function(roleCategoryId) {
            var ajaxUrl = $('#homeUrl').val() + $('#member-role-dep-drop-Url').val()
            var memberId = $('#hdnmemberid').val()
            if ((roleCategoryId != '' || roleCategoryId != null)) {
                $.post(ajaxUrl, // Ajax Post URL
                    {
                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                        roleCategoryId: roleCategoryId,
                        memberId: memberId,
                        userType: "M"
                    },
                    function(res) {
                        $('select#member-role').html(res)
                    })
            }
        },
        _spouseroles: function(roleCategoryId) {
            var ajaxUrl = $('#homeUrl').val() + $('#member-role-dep-drop-Url').val()
            var memberId = $('#hdnmemberid').val()
            if ((roleCategoryId != '' || roleCategoryId != null)) {
                $.post(ajaxUrl, // Ajax Post URL
                    {
                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                        roleCategoryId: roleCategoryId,
                        memberId: memberId,
                        userType: "S"
                    },
                    function(res) {
                        $('select#spouse-role').html(res)
                    })
            }
        },
        _onLoadEvents: function() {
            var __this = this
            //$('#tag-cloud-input').tagsinput('items')
            /*$(document).on("keydown", ".bootstrap-tagsinput", function(e) {

                if (e.keyCode == 13) {
                    return
                }

            });*/
            var roleCategoryId = $('#role-category').val()
            __this._memberroles(roleCategoryId)
            var roleCategoryId = $('#spouse-role-category').val()
            __this._spouseroles(roleCategoryId)
            $(document).ready(function() {
                __this._spouseDropDownToggle()
                __this._weddingAnnivesaryToogle()
                __this._updateSpouseRequiredIndicators()

            });

        },
        _onKeyEvents: function() {
            var __this = this;
        
            $(document).on('keypress', '#dependantMobile, #dependantSpouseMobile', function(event) {
                var charCode = event.which ? event.which : event.keyCode;
        
                // Allow numbers (0-9), backspace (8), and delete (46)
                if (charCode < 48 || charCode > 57) {
                    if (charCode !== 8 && charCode !== 46) {
                        event.preventDefault();
                    }
                }
            });
            
            // Ensure pasted input contains only numbers
            $(document).on('input', '#dependantMobile, #dependantSpouseMobile', function() {
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
            });


            $(document).on('keypress', '#dependantMobileCountryCode, #dependantSpouseMobileCountryCode', function(event) {
                var charCode = event.which ? event.which : event.keyCode;
            
                // Allow numbers (0-9), backspace (8), delete (46), and plus (+)
                if ((charCode < 48 || charCode > 57) && charCode !== 8 && charCode !== 46 && charCode !== 43) {
                    event.preventDefault();
                }
            });
            $(document).on('input', '#dependantMobileCountryCode, #dependantSpouseMobileCountryCode', function() {
                let value = $(this).val();
            
                // Allow "+" only at the beginning and remove all other non-numeric characters
                value = value.replace(/[^0-9+]/g, '');
            
                // Ensure "+" appears only at the start
                if (value.includes('+')) {
                    value = '+' + value.replace(/\+/g, '');
                }
            
                $(this).val(value);
            });
        },

        _SetEditDependant: function(data) {
            $('.dependantFrom').html(data);
        },
        // funtion for validating controls before submit
        _ValidateBeforeSave: function() {
            var type = $("#isStaff").val()
            var regexEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            var regexName = /^[a-zA-Z]+([ ][a-zA-Z]+)*$/;
            //var regexPhone = /^\d{10}$/;
            var regexPhone = /^\d{6,}$/;
            var regexcountrycode = /^(\+(?:\d{2,3}$))|(\+(?:\d{1})$)|(?:\d{2,3}$)|(?:\d{1}$)/;
            var regexareacode = /^\d{0,5}$/;
            var regexpin = /^.{4,8}$/;
            //var regexOfficePhone = /^(?=.*[0-9])[- ()0-9]+$/;
            var regexOfficePhone = /^\d{7,10}$/;
            var boolresult = true;
            var latitudePattern = /^-?(90|[0-8]?\d)(\.\d+)?$/;
            var longitudePattern = /^-?(180|1[0-7]\d|[0-9]?\d)(\.\d+)?$/;



            if ($("#MemberTitle").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Please select ' + type + ' title.',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#MemberTitle").focus();
                    }
                )

                boolresult = false;

            } else if ($("#txtFirstName").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: type + ' First Name Cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtFirstName").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtLastName").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: type + ' Last Name Cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtLastName").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberMobile1").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: type + ' Mobile number is mandatory',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberMobile1").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtspousemobile1").val() != "" && $("#txtMemberSpouseFirstName").val().trim() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse First Name cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberSpouseFirstName").focus();
                    }
                )

                boolresult = false;
            } else if ($("#txtMemberEmail").val() != "" && !regexEmail.test($("#txtMemberEmail").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: ' Email not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberEmail").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtbusinessEmail").val() != "" && !regexEmail.test($("#txtbusinessEmail").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Business Email not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtbusinessEmail").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberMobile1").val() != "" && !regexPhone.test($("#txtMemberMobile1").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: type + ' Mobile Number not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberMobile1").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberMobile1").val() != "" && $("#txtMemberMobile1_countrycode").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: type + ' Mobile country code is mandatory',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberMobile1_countrycode").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberMobile1_countrycode").val() != "" && !regexcountrycode.test($("#txtMemberMobile1_countrycode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Countrycode not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberMobile1_countrycode").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtspousemobile1").val() != "" && !regexPhone.test($("#txtspousemobile1").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse Mobile Number not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtspousemobile1").focus();
                    }
                )
                boolresult = false;
            } else if (($("#txtlatitude").val() == "") && ($("#txtlongitude").val() != "")) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Latitde and Longitude are filled together',
                        type: 'error',
                        timer: 2000
                    },
                    function() {
                        $("#txtlatitude").focus();
                    }
                )
                boolresult = false;
            } else if (($("#txtlatitude").val() != "") && ($("#txtlongitude").val() == "")) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Latitde and Longitude are filled together',
                        type: 'error',
                        timer: 2000
                    },
                    function() {
                        $("#txtlongitude").focus();
                    }
                )
                boolresult = false;
            } else if (($("#txtlatitude").val() != "") && !latitudePattern.test($("#txtlatitude").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Enter a valid coordinate: Latitude (-90 to 90), Longitude (-180 to 180).',
                        type: 'error',
                        timer: 2000
                    },
                    function() {
                        $("#txtlatitude").focus();
                    }
                )
                boolresult = false;
            } else if (($("#txtlongitude").val() != "") && !longitudePattern.test($("#txtlongitude").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Enter a valid coordinate: Latitude (-90 to 90), Longitude (-180 to 180).',
                        type: 'error',
                        timer: 2000
                    },
                    function() {
                        $("#txtlongitude").focus();
                    }
                )
                boolresult = false;
            }else if ($("#txtspousemobile1").val() != "" && $("#txtspousemobile1_countrycode").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse Mobile country code is mandatory',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtspousemobile1_countrycode").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberNo").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Membership number is mandatory',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberNo").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberShip_Type").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Membership type is mandatory',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberShip_Type").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtspousemobile1_countrycode").val() != "" && !regexcountrycode.test($("#txtspousemobile1_countrycode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Countrycode not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtspousemobile1_countrycode").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberspouseemail").val() != "" && !regexEmail.test($("#txtMemberspouseemail").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse Email not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberspouseemail").focus();
                    }
                )

                boolresult = false;
            } else if ($("#txtMemberMobile1").val() != "" && ($("#txtspousemobile1").val() == $("#txtMemberMobile1").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: type + ' and Spouse mobile no cannot be same',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtspousemobile1").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberspouseemail").val() != "" && $("#txtMemberSpouseFirstName").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');

                swal({
                        title: '',
                        text: 'Spouse First Name Cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberSpouseFirstName").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberspouseemail").val() != "" && !regexName.test($("#txtMemberSpouseFirstName").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse First Name not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberSpouseFirstName").focus();
                    }
                )
                boolresult = false;
            } else if (($("#txtspousemobile1").val() != "" && $("#txtMemberSpouseFirstName").val() == "")) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse First Name Cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberSpouseFirstName").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberspouseemail").val() != "" && $("#txtMemberSpouseLastName").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse Last Name Cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberSpouseLastName").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberspouseemail").val() != "" && !regexName.test($("#txtMemberSpouseLastName").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse Last Name not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberSpouseLastName").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberNo").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Member no  Cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberNo").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberShip_Type").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Membership type  Cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberShip_Type").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtSpouseDOB").val() != "" && $("#txtMemberSpouseFirstName").val().trim() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse First Name cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtSpouseDOB").focus();
                    }
                )
                boolresult = false;

            }
            /*else if ($("#txtMemberBusinessPhone1_areacode").val() != "" && $("#txtMemberBusinessPhone1").val() == "") {
                            $('.nav-tabs a[href="#member"]').tab('show');
                            swal({
                                    title: '',
                                    text: 'Office phone number 1 cannot be blank',
                                    type: 'error',
                                    //closeOnConfirm: false
                                    timer: 2000
                                },
                                function() {
                                    $("#txtMemberBusinessPhone1").focus();
                                }
                            )
                            boolresult = false;
                        } */
            else if ($("#txtMemberBusinessPhone1_areacode").val() != "" && $("#txtMemberBusinessPhone1").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office phone number 1 cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone1").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberBusinessPhone3_areacode").val() != "" && $("#txtMemberBusinessPhone3").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office phone number 2 cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone3").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberBusinessPhone3_areacode").val() != "" && $("#txtMemberBusinessPhone3").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office phone number 2 cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone3").focus();
                    }
                )
                boolresult = false;
            }
            /*else if ($("#txtMemberBusinessPhone1_countrycode").val() != "" && $("#txtMemberBusinessPhone1").val() == "") {
                            $('.nav-tabs a[href="#member"]').tab('show');
                            swal({
                                    title: '',
                                    text: 'Office phone number 1 cannot be blank',
                                    type: 'error',
                                    //closeOnConfirm: false
                                    timer: 2000
                                },
                                function() {
                                    $("#txtMemberBusinessPhone1").focus();
                                }
                            )
                            boolresult = false;
                        } else if ($("#txtMemberBusinessPhone1").val() != "" && $("#txtMemberBusinessPhone1_areacode").val() == "") {
                            $('.nav-tabs a[href="#member"]').tab('show');
                            swal({
                                    title: '',
                                    text: 'Office phone number 1 areacode cannot be blank',
                                    type: 'error',
                                    //closeOnConfirm: false
                                    timer: 2000
                                },
                                function() {
                                    $("#txtMemberBusinessPhone1").focus();
                                }
                            )
                            boolresult = false;
                        } else if ($("#txtMemberBusinessPhone1").val() != "" && $("#txtMemberBusinessPhone1_countrycode").val() == "") {
                            $('.nav-tabs a[href="#member"]').tab('show');
                            swal({
                                    title: '',
                                    text: 'Office phone number 1 country code cannot be blank',
                                    type: 'error',
                                    //closeOnConfirm: false
                                    timer: 2000
                                },
                                function() {
                                    $("#txtMemberBusinessPhone1").focus();
                                }
                            )
                            boolresult = false;
                        } else if ($("#txtMemberBusinessPhone3").val() != "" && $("#txtMemberBusinessPhone3_areacode").val() == "") {
                            $('.nav-tabs a[href="#member"]').tab('show');
                            swal({
                                    title: '',
                                    text: 'Office phone number 2 areacode cannot be blank',
                                    type: 'error',
                                    //closeOnConfirm: false
                                    timer: 2000
                                },
                                function() {
                                    $("#txtMemberBusinessPhone3").focus();
                                }
                            )
                            boolresult = false;
                        } else if ($("#txtMemberBusinessPhone3").val() != "" && $("#txtMemberBusinessPhone3_countrycode").val() == "") {
                            $('.nav-tabs a[href="#member"]').tab('show');
                            swal({
                                    title: '',
                                    text: 'Office phone number 2 country code cannot be blank',
                                    type: 'error',
                                    //closeOnConfirm: false
                                    timer: 2000
                                },
                                function() {
                                    $("#txtMemberBusinessPhone3").focus();
                                }
                            )
                            boolresult = false;
                        }*/
         /*   else if ($("#txtMemberBusinessPhone1").val() != "" && !regexOfficePhone.test($("#txtMemberBusinessPhone1").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office phone number 1 not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone1").focus();
                    }
                )
                boolresult = false;
            }*/

            else if ($("#txtMemberBusinessPhone1_countrycode").val() != "" && !regexcountrycode.test($("#txtMemberBusinessPhone1_countrycode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office Phone 1 country code not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone1_countrycode").focus();
                    }
                )
                boolresult = false;
            }
           else if ($("#txtMemberBusinessPhone1_areacode").val() != "" && !regexareacode.test($("#txtMemberBusinessPhone1_areacode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office Phone 1 area code not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone1_areacode").focus();
                    }
                )
                boolresult = false;
            }
            else if ($("#txtMemberBusinessPhone3_countrycode").val() != "" && !regexcountrycode.test($("#txtMemberBusinessPhone3_countrycode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office Phone 2 country code not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone1_countrycode").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberBusinessPhone3_areacode").val() != "" && !regexareacode.test($("#txtMemberBusinessPhone3_areacode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office Phone 2 area code not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone3_areacode").focus();
                    }
                )
                boolresult = false;
            }
           /* else if ($("#txtMemberBusinessPhone3").val() != "" && !regexOfficePhone.test($("#txtMemberBusinessPhone3").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office phone number 2 not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone1").focus();
                    }
                )
                boolresult = false;
            } */
            else if ($("#txtbusinessEmail").val() != "" && !regexEmail.test($("#txtbusinessEmail").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Member Residential Email not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtbusinessEmail").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtDependantName").val() != "" && $("#DropDownRelation option:selected").text() != "") {
                $('.nav-tabs a[href="#dependants"]').tab('show');
                swal({
                        title: '',
                        text: 'Please add dependent details !',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtState").focus();
                    }
                )
                boolresult = false;
            } else if ($('#SpouseTitle').val() > 0 && $("#txtMemberSpouseFirstName").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse name Cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        //$("#txtState").focus(); 
                    }
                )
                boolresult = false;
            } else if ($('#SpouseTitle').val() > 0 && $("#txtMemberSpouseLastName").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse Last Name Cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        //$("#txtState").focus(); 
                    }
                )
                boolresult = false;
            } else if ($('#SpouseTitle').val() == "" && $("#txtMemberSpouseFirstName").val() != "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Please select spouse title.',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {

                        $("#SpouseTitle").focus();

                    }
                )
                boolresult = false;
            }
            /* else if ($("#txtMemberBusinessPhone3_areacode").val() != "" && $("#txtMemberBusinessPhone3").val() == "") {
                            $('.nav-tabs a[href="#member"]').tab('show');
                            swal({
                                    title: '',
                                    text: 'Office phone number 2 cannot be blank',
                                    type: 'error',
                                    //closeOnConfirm: false
                                    timer: 2000
                                },
                                function() {
                                    $("#txtMemberBusinessPhone3").focus();
                                }
                            )
                            boolresult = false;
                        } */
            else if ($("#txtMemberBusinessPhone3_countrycode").val() != "" && $("#txtMemberBusinessPhone3").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office phone number 2 cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone3").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberResidencePhone1").val() == "" && $("#txtMemberResidencePhone1_countrycode").val() != "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Residence Land Line Number cannot be empty',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberResidencePhone1").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberResidencePhone1").val() == "" && $("#txtMemberResidencePhone1_areacode").val() != "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Residence Land Line Number cannot be empty',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberResidencePhone1").focus();
                    }
                )
                boolresult = false;
            }
            /*else if ($("#txtMemberResidencePhone1").val() != "" && !regexOfficePhone.test($("#txtMemberResidencePhone1").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Residence Land Line Number not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberResidencePhone1").focus();
                    }
                )
                boolresult = false;
            }
            */
            else if ($("#txtMemberResidencePhone1_areacode").val() != "" && !regexareacode.test($("#txtMemberResidencePhone1_areacode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Residence Land Line Area Code not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberResidencePhone1_areacode").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtMemberResidencePhone1_countrycode").val() != "" && !regexcountrycode.test($("#txtMemberResidencePhone1_countrycode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Residence Land Line Country Code not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberResidencePhone1_countrycode").focus();
                    }
                )
                boolresult = false;
            } else if ($("#StaffDesignation").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Designation is mandatory for a staff',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#StaffDesignation").focus();
                    }
                )

                boolresult = false;
            }
            else if ($("#batch").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                $("").focus();
                swal({
                        title: '',
                        text: 'Batch cannot be blank!',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#batch").focus();
                    })
                boolresult = false;
            }
             else if ($("#role-category").val() == "") {
                $('.nav-tabs a[href="#settings"]').tab('show');
                swal({
                    title: '',
                    text: 'Role category cannot be blank !',
                    type: 'error',
                    timer: 2000
                }, function() {
                    $("#role-category").focus();
                })
                boolresult = false;
            } else if ($("#member-role").val() == "") {
                $('.nav-tabs a[href="#settings"]').tab('show');
                swal({
                    title: '',
                    text: 'Member role cannot be blank !',
                    type: 'error',
                    timer: 2000
                }, function() {
                    $("#member-role").focus();
                })
                boolresult = false;
            } else if ($('#txtspousemobile1').val() !== "") {
                if ($("#spouse-role-category").val() == "") {
                    $('.nav-tabs a[href="#settings"]').tab('show');
                    swal({
                        title: '',
                        text: 'Spouse role category cannot be blank',
                        type: 'error',
                        timer: 2000
                    }, function() {
                        $("#spouse-role-category").focus();
                    })
                    boolresult = false;
                }
                if ($("#spouse-role").val() == "") {
                    $('.nav-tabs a[href="#settings"]').tab('show');
                    swal({
                        title: '',
                        text: 'Spouse Role cannot be blank',
                        type: 'error',
                        timer: 2000
                    }, function() {
                        $("#spouse-role").focus();
                    })
                    boolresult = false;
                }
            } else {
                boolresult = true;
            }
            return boolresult;

        },
        _ValidateBeforeDeleteMember: function() {
            var boolresult = true;
            if ($("#txtMemberSpouseFirstName").val() == "") {
                swal({
                        title: '',
                        text: 'Spouse First Name cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberSpouseFirstName").focus();
                    }
                )

                boolresult = false;
            } else if ($("#txtMemberSpouseLastName").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: "Spouse Last Name cannot be blank",
                        type: 'error',
                        //closeOnConfirm: true
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberSpouseLastName").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtspousemobile1").val() == "") {

                $('.nav-tabs a[href="#member"]').tab('show');

                swal({
                        title: '',
                        text: "Spouse Mobile number is mandatory",
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtspousemobile1").focus();
                    }
                )

                boolresult = false;
            } else {
                boolresult = true;
            }
            return boolresult;
        },

        _spouseDropDownToggle: function() {
            if ($('#txtspousemobile1').val() == null || $('#txtspousemobile1').val() == "" || $('#txtspousemobile1').val() == undefined) {
                $(".spouse-role-category").prop("disabled", true);
                $(".spouse-role").prop("disabled", true);
                $('.spouse-role-category').prop('selectedIndex', 0);
                $('.spouse-role').prop('selectedIndex', 0);
            } else {
                $(".spouse-role-category").prop("disabled", false);
                $(".spouse-role").prop("disabled", false);
            }
        },

        _weddingAnnivesaryToogle:function() {
            if ($('#txtMemberSpouseFirstName').val() == null || $('#txtMemberSpouseFirstName').val() == "" || $('#txtMemberSpouseFirstName').val() == undefined) {
                $('#dom-kvdate').kvDatepicker('update', '');
                $('#date-of-wedding').hide()
            } else {
               $('#date-of-wedding').show()
            }
        },

        _updateSpouseRequiredIndicators: function() {
            // Show asterisks if any of these conditions are true:
            // 1. Spouse mobile number is filled
            // 2. Spouse title is selected
            // 3. Spouse email is filled
            // 4. Spouse DOB is filled
            var spouseMobile = $('#txtspousemobile1').val();
            var spouseTitle = $('#SpouseTitle').val();
            var spouseEmail = $('#txtMemberspouseemail').val();
            var spouseDOB = $('#txtSpouseDOB').val();
            
            if ((spouseMobile && spouseMobile.trim() !== "") || 
                (spouseTitle && spouseTitle !== "") || 
                (spouseEmail && spouseEmail.trim() !== "") ||
                (spouseDOB && spouseDOB.trim() !== "")) {
                $('#spouse-title-required').show();
                $('#spouse-firstname-required').show();
                $('#spouse-lastname-required').show();
            } else {
                $('#spouse-title-required').hide();
                $('#spouse-firstname-required').hide();
                $('#spouse-lastname-required').hide();
            }
            
            // Show country code asterisk if mobile is filled but country code is empty
            /* if ((spouseMobile && spouseMobile.trim() !== "")) {
                $('#spouse-mobile-countrycode-required').show();
            } else {
                $('#spouse-mobile-countrycode-required').hide();
            } */
        },

        _ValidateDependantSave: function() {
            var boolresult = true;
            if ($("#dependantTitleId").val() == "") {
                swal({
                    title: ' ',
                    text: 'Please select Dependent Title',
                    type: 'error',
                })
                $("#dependantTitleId").focus();
                boolresult = false;
            } else if ($("#txtDependantName").val().trim() == "") {

                swal({
                    title: ' ',
                    text: 'Dependent Name Cannot be blank',
                    type: 'error',
                })
                $("#txtDependantName").focus();
                boolresult = false;
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
var memberCreateJS = new Remember.memberCreate.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function() {
    jsFramework.lib.ui.pageBinder.addPageBuilder(memberCreateJS)
})