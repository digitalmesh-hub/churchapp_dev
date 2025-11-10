jsFramework.lib.core.utils.registerNamespace('Remember.Event.ui')
Remember.Event.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
            $(document).on('click', '#acknowledgeid', function() {
                var ackvalue = $(this).attr('ackvalue');
                if (ackvalue != 'disabled') {
                    var rsvpid = $(this).attr('data-id');
                    var memberId = $(this).attr('data-memberId');
                    var userId = $(this).attr('data-userId');
                    var eventId = $(this).attr('data-eventId');
                    $('#eventid').val(eventId);
                    $('#rsvpid').val(rsvpid);
                    $('#memberid').val(memberId);
                    $('#userid').val(userId);
                    $('#alertModal').find('.modal-body');
                    $('#alertModal').modal('show');
                    $('#ErrorMessageLabel').hide();
                }
            });
            $(document).on('click', '#rsvplist', function() {
                var rsvpavailable = $(this).attr('rsvpavailable');
                if (rsvpavailable == 0) {
                    $(this).removeAttr("href");

                }
            });
            $(document).on('click', '#modalsendid', function() {
                var noteBody = $('#modaltxtnotebody').val();
                if (noteBody == '') {
                    $('#ErrorMessageLabel').show();
                } else {
                    $('#ErrorMessageLabel').hide();
                    $('#alertModal').modal('show');
                    $.ajax({
                        url: $('#homeUrl').val() + $('#modalsendid').attr('url'),
                        type: "POST",
                        dataType: 'json',
                        async: true,
                        data: {
                            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                            rsvpId: $('#rsvpid').val(),
                            eventId: $('#eventid').val(),
                            memberId: $('#memberid').val(),
                            userId: $('#userid').val(),
                            noteBody: noteBody
                        },
                        success: function(response) {
                            window.location.href = $('#homeUrl').val() + $('#modalsendid').attr('reload-path'),
                                $(".overlay").hide();
                        },
                        error: function(er) {
                            window.location.href = $('#homeUrl').val() + $('#modalsendid').attr('reload-path'),
                                $(".overlay").hide();
                            // alert(er.data.message);   
                        },
                    });
                }
            });

            //Publish event
            $(document).on('click', '#btn-publish', function() {

                if (typeof $(this).attr('disabled') != typeof undefined && $(this).attr("disabled").length) {
                    return false;
                }
                var publish = $(this).attr('publish');
                var title = $(this).attr("event-title");
                var activityDate = $(this).attr("activity-date");
                var activateOnDate = $(this).attr("activity-on");
                var familyUnitId = $(this).attr("familyUnitId");
                var venue = $(this).attr("venue");
                if (activityDate != '' && activityDate != null) {
                    activityDateList = activityDate.split('-');
                    formatActivityDate = new Date(parseInt(activityDateList[0]), parseInt(activityDateList[1]) - 1,
                        parseInt(activityDateList[2]));
                }
                if (activateOnDate != '' && activateOnDate != null) {
                    activityOnDateList = activateOnDate.split('-');
                    formatActivityOnDate = new Date(parseInt(activityOnDateList[0]), parseInt(activityOnDateList[1]) - 1,
                        parseInt(activityDateList[2]));
                }
                if (formatActivityOnDate > new Date()) {
                    swal({
                            title: 'Failed',
                            text: 'Activate On date should not be greater than today',
                            type: 'error'
                        },
                        function() {
                            location.reload()
                        }
                    )
                } else if (formatActivityDate < new Date()) {
                    swal({
                            title: 'Failed',
                            text: 'Event Date and Time should be greater than current Date and Time',
                            type: 'error'
                        },
                        function() {
                            location.reload()
                        }
                    )
                }
                if (false) {
                    console.log("test")
                } else {
                    if (publish == 1) {
                        $(this).removeAttr("href");
                    } else {
                        var ajaxUrl = $('#homeUrl').val() + $(this).attr('url')
                        var id = $(this).attr('event-id')

                        swal({
                                title: 'Are you sure?',
                                text: 'Do you want to Publish this ',
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonClass: 'btn-danger',
                                confirmButtonText: 'Yes',
                                closeOnConfirm: false
                            },
                            function() {
                                $.post(ajaxUrl, // Ajax Post URL
                                    {
                                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                                        id: id,
                                        noteHead: title,
                                        activityDate: activityDate,
                                        venue: venue,
                                        familyUnitId: familyUnitId
                                    }, // Data
                                    function(res) {
                                        if (typeof(res) !== 'undefined' && res.status === 'success') {
                                            swal({
                                                    title: 'Success',
                                                    text: res.data,
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
                            }
                        )
                    }
                }
            });

            //publishes the news
            $(document).on('click', '#btn-news-publish', function() {

                var publish = $(this).attr('publish');
                var title = $(this).attr("event-title");
                var activityDate = $(this).attr("activity-date");
                var familyUnitId = $(this).attr("familyUnitId");

                if (activityDate != '' && activityDate != null) {
                    activityDateList = activityDate.split('-');
                    formatActivityDate = new Date(parseInt(activityDateList[0]), parseInt(activityDateList[1]) - 1,
                        parseInt(activityDateList[2]));
                }

                if (publish == 1) {
                    $(this).removeAttr("href");
                } else {
                    var ajaxUrl = $('#homeUrl').val() + $(this).attr('url')
                    var id = $(this).attr('event-id')
                    swal({
                            title: 'Are you sure?',
                            text: 'Do you want to Publish this',
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
                                    id: id,
                                    noteHead: title,
                                    activityDate: activityDate,
                                    familyUnitId: familyUnitId
                                }, // Data
                                function(res) {
                                    if (typeof(res) !== undefined && res.status === 'success') {
                                        swal({
                                            title: "Success",
                                            text: res.data,
                                            type: "success"
                                        }, function() {
                                            location.reload()
                                        })
                                    } else {
                                        swal("Failed", "Sorry! unable to complete the process", "error");
                                    }
                                })
                        }
                    )
                }

            });
            $(document).on('click', '#btnUpload', function() {
                if ($("#exlfile").val() == '') {
                    $("#uploadErrorMessageEmptyFeild").show();
                    $('#uploadErrorMessage').hide();
                } else {
                    if ($("#exlfile")[0].files[0].size <= 5242880) {
                    var data = new FormData();
                    data.append('img', $("#exlfile")[0].files[0]);
                    var txt = $("#txtnotebody").val();
                    $.ajax({
                        url: $('#homeUrl').val() + $('#btnUpload').attr('url'),
                        type: "POST",
                        processData: false,
                        contentType: false,
                        cache: false,
                        dataType: 'json',
                        async: true,
                        data: data,
                        success: function(response) {
                            //alert(response)
                            if (response.hasError == false) {
                                var link = JSON.stringify(response.url);
                                $(".note-editable").append('</br>.Please Click on the link.</br><a href=' + link + '>' + link + '</a>');
                                $("#txtnotebody").val(txt + '</br>.Please Click on the link.</br><a href=' + link + '>' + link + '</a>');
                                $("#exlfile").val('');
                                $(".overlay").hide();
                            } else {
                                $(".overlay").hide();
                            }
                        },
                        error: function(er) {
                            $(".overlay").hide();
                            swal({
                                title: '',
                                text: 'Unbale to upload file.Please try again',
                                type: 'error',
                            })
                        },
                    });
                } else {
                    $("#exlfile").val('');
                    swal('File size cannot exceed 5MB')
                }
            }

            });
            $(document).on('click', '#btn-event-delete', function() {
                var ajaxUrl = $('#homeUrl').val() + $('#event-delete-url').val()
                var id = $(this).attr('event-id')
                swal({
                        title: 'Are you sure?',
                        text: 'Do you want to delete the event?',
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
                                            text: 'The event deleted successfully',
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
            $(document).on('click', '#btn-news-delete', function() {
                var ajaxUrl = $('#homeUrl').val() + $('#news-delete-url').val()
                var id = $(this).attr('news-id')
                swal({
                        title: 'Are you sure?',
                        text: 'Do you want to delete the record?',
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
                                            text: 'The record deleted successfully',
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

        },
        _onChangeEvents: function() {
            var __this = this;
            
            $(document).on('change', '#extendedevent-activitydate' ,function(e) {
            	var activityDate = $('#extendedevent-activitydate').val();
            	var formattedDate = __this._dateFormat(activityDate);
            	        if(formattedDate != undefined && formattedDate != '') {
            	           $('#expriydate-kvdate').kvDatepicker('setStartDate', formattedDate);
            	           $('#expriydate').val(formattedDate);
            	        }
            });
            $(document).on('change', '#notice-board-activity-date' ,function(e) {
                var noticeActivityDate = $('#notice-board-activity-date').val();
                var formattedDate = __this._dateFormat(noticeActivityDate);
                if(formattedDate != undefined && formattedDate != '') {
                    $('#news-expriy-date-kvdate').kvDatepicker('setStartDate', formattedDate);
                    $('#news-expriy-date').val(formattedDate);
                }
            });

            $(document).on('change', '#rsvpcountdropdown', function(e) {
                this.form.submit()
            })
            $('#exlfile').on('change', function() {
                var FileType = ".jpg,.jpeg,.png,.doc,.docs,.docx,.pdf,.JPG,.JPEG,.PNG,DOCS,DOCX";
                var filename = $("#exlfile")[0].value;
                var errMsg;
                var GetExtension = filename.substring(filename.lastIndexOf('.'));
                if (jQuery.trim(GetExtension).length > 0) {
                    // Check file type is valid or not  
                    if (FileType.toLowerCase().indexOf(GetExtension.toLowerCase()) < 0) {
                        $('#uploadErrorMessage').show();
                        $('#uploadErrorMessageEmptyFeild').hide();
                        $('#exlfile').val('');
                        $('#exlfile').focus();
                    }
                }
            });
        },
        _dateFormat: function(dateString){
            var fullMonth = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];
            var objDate = new Date(dateString);
            var objDate = objDate.setDate(objDate.getDate());

            var date = new Date(objDate);

            // Hours part from the timestamp
            var day = date.getDate();

            // Minutes part from the timestamp
            var month = date.getMonth();

            var year = date.getFullYear();

            // Will display time in 10:30:23 format
            var formattedDate = day + ' ' + fullMonth[month] + ' ' + year;

            return formattedDate;
          },
        _onLoadEvents: function() {
            var __this = this
            $('.drop-select').select2()
            $('.input-description').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript', 'fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph', 'table', 'hr']],
                    ['height', ['height']],
                    ['files', ['picture', 'link']],
                    ['fullscreen'],
                    ['codeview'],
                    ['help']
                ],
                callbacks: {
                    onImageUpload: function(image,editor,editable) {
                        var data = new FormData();
                        data.append('img',image[0]);
                        var sizeKB = image[0]['size'];
                        var valid = 0;
                        message = "";
                        if(sizeKB > 5242880){
                            valid = 1;
                            message = "Image size cannot exceed 2 MB";
                         }
                         if(image[0]['type'] != 'image/jpeg' && image[0]['type'] != 'image/png' && image[0]['type'] != 'image/jpg'){
                            valid = 1;
                            message = "Please select png or jpg image.";
                         }
                        if(valid == 0){
                            $.ajax({
                                url: $('#homeUrl').val() + $('#imageUpload').val(),
                                type: "POST",
                                processData: false,
                                contentType: false,
                                cache: false,
                                dataType: 'json',
                                async: true,
                                data: data,
                                success: function(response) {
                                    //alert(response)
                                    if (response.hasError == false) {
                                        $('.input-description').summernote('insertImage', response.url, function ($image) {
                                                $image.css('width', '100%');
                                        });
                                    } else {
                                        //  window.location.href = $('#homeUrl').val() + $('#btnUpload').attr('url');
                                        $(".overlay").hide();
                                    }
                                },
                                error: function(er) {
                                   swal('Something went wrong! Please try again') 
                                },
                            });
                        } else {
                            swal(message)
                        }   	
                    }
                }
            })
            // $('.note-group-select-from-files').css('display', 'none')
        },
        _onKeyEvents: function() {
            var __this = this
        },
        
        // public members
        buildPage: function() {
            this._InitializePageBuilder()
        }

    })
var EventJS = new Remember.Event.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function() {
    jsFramework.lib.ui.pageBinder.addPageBuilder(EventJS)
})
