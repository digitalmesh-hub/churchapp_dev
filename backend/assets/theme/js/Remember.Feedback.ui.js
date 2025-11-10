jsFramework.lib.core.utils.registerNamespace('Remember.Feedback.ui')
Remember.Feedback.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    __this._basicEvents()
    __this._ajaxEvents()
  },

  _ajaxEvents: function () {

  },

  _basicEvents: function () {
    var __this = this
    __this._onClickEvents()
    __this._onChangeEvents()
    __this._onLoadEvents()
    __this._onKeyEvents()
  },

_onClickEvents: function () {
    var __this = this


    $(document).on("click", ".sortarrow-up", function () {

            var InstitutionFeedbackTypeID = $(this).attr("id");
            var CurrentOrder = $(this).attr("order");
        
            var PreviousOrder = $(this).attr("previousorder");

                 $.ajax({
                    url:    $('#homeUrl').val() + $('.sortarrow-up').attr('url'),
                    type:   "POST",
                    dataType : 'json',
                    async : true,
                    data: { 
                            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                            InstitutionFeedbackTypeID:InstitutionFeedbackTypeID,
                            CurrentOrder:CurrentOrder,
                            PreviousOrder:PreviousOrder,
                            sort:'up',
                    },
                    success: function (result) {
                             location.reload();
                             $(".overlay").hide();  
                                  
                    },   
                    error: function (er) {   
                         location.reload();
                         $(".overlay").hide();  
                    },
            });


        });

        $(document).on("click", ".sortarrow-down", function () {
            var InstitutionFeedbackTypeID = $(this).attr("id");
            var CurrentOrder = $(this).attr("order");
            var nextOrder = $(this).attr("nextorder");
                 $.ajax({
                    url:    $('#homeUrl').val() + $('.sortarrow-up').attr('url'),
                    type:   "POST",
                    dataType : 'json',
                    async : true,
                    data: { 
                            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                            InstitutionFeedbackTypeID:InstitutionFeedbackTypeID,
                            CurrentOrder:CurrentOrder,
                            nextOrder:nextOrder,
                            sort:'down',
                    },
                    success: function (result) {
                             location.reload();
                             $(".overlay").hide();  
                                  
                    },   
                    error: function (er) {     
                         location.reload();
                         $(".overlay").hide();    
                    },
            });


        });
    $(document).on('click', '#btn-activate', function () {
      var ajaxUrl = $('#homeUrl').val() + $(this).attr('url');
      var id = $(this).attr('feedbacktypeid');
    
        
      swal({
        title: 'Are you sure?',
        text: 'Do you want to activate this Feedback Type',
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        confirmButtonText: 'Yes',
        closeOnConfirm: false
      },
        function () {
          $.post(ajaxUrl, // Ajax Post URL
            {
              '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
              id: id
            }, // Data
                function (res) {
              
                 if (typeof (res) !== 'undefined' && res.status === 'success') {
                    swal({title: 'Success', text: 'This Feedback Type has been activated', type: 'success'},
                                function () {
                                  location.reload()
                                })
                  } else {
                    swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'},
                    function () {
                      location.reload()
                    })
                  }
                })
        })
    })
    $(document).on('click', '#btn-deactivate', function () {
      var ajaxUrl = $('#homeUrl').val() + $(this).attr('url');
      var id = $(this).attr('feedbacktypeid');
      swal({
        title: 'Are you sure?',
        text: 'Do you want to deactivate this Feedback Type',
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        confirmButtonText: 'Yes',
        closeOnConfirm: false
      },
        function () {
          $.post(ajaxUrl, // Ajax Post URL
            {
              '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
              id: id
            }, // Data
                function (res) {

                  if (typeof (res) !== 'undefined' && res.status === 'success') {
                    swal({title: 'Success', text: 'This Feedback has been deactivated', type: 'success'},
                        function () {
                          location.reload()
                        }
                        )
                  } else {
                    swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'},
                    function () {
                      location.reload()
                    })
                  }
                })
        })
    })
    $(document).on('click', '#Savefeedbackemail', function (){
        var data = $('#txtfeedbackemail').val();
        if(data != ''){
         $('#ErroraddmailDiv').hide();
         $('#ErrorvalidatemaleDiv').hide();    
        var testEmail = /^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$/;
        if (testEmail.test(data)) {
                 $.ajax({
                    url:    $('#homeUrl').val() + $('#Savefeedbackemail').attr('url'),
                    type:   "POST",
                    dataType : 'json',
                    async : true,
                    data: { 
                            data:data,
                            '_csrf-backend':$("meta[name='csrf-token']").attr('content')
                    },
                    success: function (result) {
                        swal({title: 'Success', text: 'Feedback email added successfully', type: 'success'},
                              function () {
                                location.reload()
                        })
                                  
                    },   
                    error: function (er) {  
                       swal({
                        title: 'Failed',
                        text: 'Something went wrong.Please try again.',
                        type: 'error'
                        })   
                    },
            }); 
        }
        else {
          swal({title: 'Failed', text: 'Please Enter A Valid Email Address', type: 'error'},
                  function () {
                  location.reload()
          })
        }
    }else{

         $('#ErroraddmailDiv').show();
    }
    });
 $(document).on('click', '#Savefeedback', function () {
    var feedbacktype = $('#FeedbackTypeTextbox').val();
    if(feedbacktype != '') {  
    $('#ErrorDiv').hide();
    $.ajax({
            url:    $('#homeUrl').val() + $('#Savefeedback').attr('url'),
            type:   "POST",
            dataType : 'json',
            async : true,
            data: { 
                    feedbacktype:feedbacktype,
                    '_csrf-backend':$("meta[name='csrf-token']").attr('content')
            },
            success: function (response) {  
                 swal({
                        title: 'Success',
                        text: 'Feedback type has been added successfully',
                        type: 'success'
                  },
                  function() {
                      location.reload()
                  })
                   
            },   
            error: function (er) {  
            swal({
                  title: 'Failed',
                  text: 'Something went wrong.Please try again.',
                  type: 'error'
            })
            },
            });
        } else {
            $('#ErrorDiv').show();
        }
    });  
    $(document).on('click', '.feedbackrespondbtn', function () {
        var feedbackid = $(this).attr('feedbackid');
        var data  =  $('#emailcontent_' + feedbackid).val();
        var email =  $('#email_' + feedbackid).val();
        if(data == '' ) {
             $('#ErrorMessageLabel').show();
             $('#ErrorEmailLabel').hide();
        }else if(email == ''){
            $('#ErrorEmailLabel').show();
            $('#ErrorMessageLabel').hide();
        }
        else {
             var testEmail = /^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$/;
             if (testEmail.test(email)) {
                  $('#ErrorEmailvalid').hide();
             $.ajax({
                    url:    $('#homeUrl').val() + $('#RespondButton').attr('url'),
                    type:   "POST",
                    dataType : 'json',
                    async : true,
                    data: { 
                            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                            feedbackid:$('#RespondButton').attr('feedbackid'),
                            userid:$('#RespondButton').attr('userid'),
                            emailid:email,
                            data:data
                    },
                    success: function (response) {
                    	
                        if (typeof(response) !== 'undefined' && response.status === 'success') {
                            swal({
                                    title: 'Success',
                                    text: response.data,
                                    type: 'success'
                                },
                                function() {
                                    location.reload()
                                    $(".overlay").hide();
                                })
                        } else {
                            swal({
                                    title: 'Failed',
                                    text: 'Sorry! unable to complete the process',
                                    type: 'error'
                                },
                                function() {
                                    location.reload()
                                    $(".overlay").hide();
                                })
                        }
                    },   
                    error: function (er) {
                    	swal({
                        title: 'Failed',
                        text: 'Sorry! unable to complete the process',
                        type: 'error'
                    },
                    function() {
                        location.reload()
                        $(".overlay").hide();
                    })
                    },
            });
        }else
        {
            $('#ErrorEmailvalid').show();
        }
    }
    });   
  },
  _onChangeEvents: function () {
       var __this = this;   
  },
  _onLoadEvents: function () {
    var __this = this
      $(document).ready(function () {
        $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'))
        })
        var activeTab = localStorage.getItem('activeTab')
            if (activeTab) {
                $('#myTab a[href="' + activeTab + '"]').tab('show')
            }
        })
  },
  _onKeyEvents: function () {
    var __this = this
  },

    // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }
})
var FeedbackJS = new Remember.Feedback.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(FeedbackJS)
})
