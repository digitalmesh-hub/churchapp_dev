jsFramework.lib.core.utils.registerNamespace('Remember.memberList.ui')
Remember.memberList.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    $(document).on('click', '#btn-activate', function () {})
    $(document).on('click', '#btn-deactivate', function () {})
  
    $("#approvalli").on("click", function () {
           // $(".overlay").show();
            $("#memberlist").hide();
            $("#memberlist").removeClass("in active");
            $("#pendinglist").addClass("in active");
            $('#pendinglist').show();
            $('#approval').show();

            //$("#memberouterdiv").hide();
            //$("#FeedbackSettings").hide();
            //__this._LoadPendingMembers(0);
        });

    $("#memberlistli").on("click", function () {
      //  $(".overlay").show();
        $('#pendinglist').hide();
        $("#pendinglist").removeClass("in active");
        $("#memberlist").show();
        $("pendinglist").addClass("tab-pane fade");
       // __this._Loadmembers(0);

    });
   
  $(".btn-member-delete").on("click", function (e) {
  	  var ajaxUrl = $('#homeUrl').val() + $('#delete-member').val();
  	  var memberId = $(this).attr('data-member-id');
  	 swal({
         title: 'Are you sure?',
         text: 'Are You sure that you need to Delete member  !',
         type: 'warning',
         showCancelButton: true,
         confirmButtonClass: 'btn-danger',
         confirmButtonText: 'Yes',
         closeOnConfirm: false,
         showLoaderOnConfirm: true
       },
       function () {
        $.post(ajaxUrl, // Ajax Post URL
                {
                  '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                  memberId:memberId,
                }, // Data
                	function (res) {
                        if (typeof (res) !== 'undefined' && res.status === 'success') {
                          swal({title: 'Success', text: 'The member details are deleted', type: 'success'},
                                      function () {
                                        location.reload()
                                      }
                          	)
                        } else {
                          swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'}),
                          function () {
                            location.reload()
                          }
                        }
                      }
                
                )
       });    
  });

    $('#addfamilyunit').on('click', function () {})
  },
  _onChangeEvents: function () {

  },
  _onLoadEvents: function () {
    var __this = this

    $('#pendinglist').hide();
    
    $('#sub-form').on('beforeValidate', function (event, messages) {})

    $('#sub-form').on('afterValidate', function (event, messages) {})
  },
  _onKeyEvents: function () {
    var __this = this
  },

    // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var memberListJS = new Remember.memberList.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(memberListJS)
})
