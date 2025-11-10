jsFramework.lib.core.utils.registerNamespace('Remember.committee.ui')
Remember.committee.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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

    var __this = this;
    
     //Committee Type block
    $(document).on('click','#CommiteegroupAddli', function(){
      $('.description').val('');
      $('.tabcontentborder').hide();
      $('#AddCommitteeType').show();
    });
    //Find Member block
    $(document).on('click','#CommitteeAddli', function(){
      $('.tabcontentborder').hide();
      $('#AddMemberDiv').show();
      $('#CommitteeMemberDetailsDiv').html('');
    });

    //Committee Member block
    $(document).on('click','#Committeeli', function(){
      $('.tabcontentborder').hide();
      $('#CommitteeMemberListDiv').show();
      __this._getAllCommitteeTypeList(true);
      __this._getCommitteeMembers();
    });

    //Committee Period block
    $(document).on('click','#CommitteePeriodli', function(){
      $('.tabcontentborder').hide();
      $('#AddCommitteePeriodDiv').show();
      __this._getAllCommitteeTypeList(false);
    });

    //Committee Designation block
    $(document).on('click','#CommiteedesignationAddli', function(){
      $('.tabcontentborder').hide();
      $('#AddCommitteeDesignationDiv').show();
    });

    $(document).on('click','.nav-tabs li', function(){
    	 $('.help-block').removeClass('error-ms');
         $('.help-block').html('');
    })
    //Add committee type 
    $(document).on('click','.add-committee-type', function(){
      var description = $('.description').val().trim();
      var committeeGroupId = $('#committeeGroupId').val();
      if(description == '' || description == undefined){
    	  $('.field-extendedcommitteegroup-description').find('.help-block').css('display', 'block');
        $('.help-block').addClass('error-ms');
        $('.help-block').css('color', '#a94442');
        $('.field-extendedcommitteegroup-description').find('.help-block').html('Committee name cannot be blank.');
      }
      else{
        $('.help-block').hide();
        var ajaxUrl = $('#homeUrl').val() + $('#admin-save-committee-type-Url').val();
        var committeeGroupId = $('#committeeGroupId').val();
        $.post(ajaxUrl, // Ajax Get URL
        {
          '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          committeeGroupId: committeeGroupId,
          description: description
        },
        function (res) {
          if (typeof (res) != 'undefined' && res.status == 'success') {
            $('.description').val('');
            $('#committeeGroupId').val(0);
            __this._getAllCategoryType();
          } 
          else {
            if(res.data != null && res.data != undefined){
              $('.field-extendedcommitteegroup-description').find('.help-block').css('display', 'block');
              $('.help-block').addClass('error-ms');
              $('.help-block').css('color', '#a94442');
              $('.field-extendedcommitteegroup-description').find('.help-block').html(res.data['description']);
            }
            else{
              $('.modal-title').text('Committee');
              $('.content-div').text("An error occured while processing the request.");
              $("#myModal").modal('show');
            }
          }
        });
      }
    });

    //Activate/Deactivate committee type
    $(document).on('click', '.btn-type-active', function(){
      var ajaxUrl = $('#homeUrl').val() + $('#admin-activate-deactivate-committee-type-Url').val();
      var committeeGroupId = $(this).attr('data-committeegroupid');
      var active = $(this).attr('data-active');

      if(active == '1'){
        var text = 'Do you need to deactivate this committee type !';
        active = '0';
        var message = 'This committee type  has been deactivated';
      }
      else{
        var text = 'Do you need to activate this committee type !';
        active = '1';
        var message = 'This committee type  has been activated';
      }

      swal({
        title: 'Are you sure?',
        text: text,
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
              committeeGroupId: committeeGroupId,
              active: active
            }, // Data
            function (res) {
              if (typeof (res) != 'undefined' && res.status == 'success') {
                swal({title: 'Success', text: message, type: 'success'},
                  function () {
                    __this._getAllCategoryType();
                  }
                )
              } 
              else {
                swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'}),
                function () {
                  //reload
                }
              }
            }
          )
        }
      )
    });

    //Edit committee type
    $(document).on('click', '.edit-committee-type', function(){
      var committeGroupId = $(this).attr('data-committeegroupid');
      var description = $(this).attr('data-description');

      $('.description').val(description);
      $('#committeeGroupId').val(committeGroupId);
      $('.update-committee-type').show();
      $('.add-type').hide();
      $('.description').focus();
    });

    //Clear button function
    $(document).on('click','#button-clear', function(){
      $('.update-committee-type').hide();
      $('.add-type').show();
      $('.description').val('');
      $('.help-block').html('');
      $('#committeeGroupId').val(0);
    });

    //Sort order down function
    $(document).on('click', '.commiteedown', function(){
      var groupId = $(this).attr('data-key');
      var currentOrder = $(this).attr('data-order');
      var nextGroupId = $($('#group_'+groupId).next()).attr('data-key');
      var nextOrder = $($('#group_'+groupId).next()).attr('data-order');
      // $('#group_'+groupId).insertAfter($($('#group_'+groupId).next()));
      __this._updateCommitteeTypeOrder(groupId, currentOrder, nextGroupId, nextOrder)
    });

    //Sort order up function
    $(document).on('click', '.commiteeup', function(){
      var groupId = $(this).attr('data-key');
      var currentOrder = $(this).attr('data-order');
      var prevGroupId = $($('#group_'+groupId).prev()).attr('data-key');
      var prevOrder = $($('#group_'+groupId).prev()).attr('data-order');
      // $('#group_'+groupId).insertBefore($($('#group_'+groupId).prev()));
      __this._updateCommitteeTypeOrder(groupId, currentOrder, prevGroupId, prevOrder);
    });

  },
  _onChangeEvents: function () {
    var __this = this
  },
  _onLoadEvents: function () {
    var __this = this
  },
  _onKeyEvents: function () {
    var __this = this
  },

  //Get all category types
  _getAllCategoryType: function () {

    var ajaxUrl = $('#homeUrl').val() + $('#admin-get-committee-type-Url').val();

    $.get(ajaxUrl, // Ajax Get URL
    {
      //data
    },
    function (res) {
      if (typeof (res) != 'undefined' && res.status == 'success') {
        $('#AddCommitteeType').html('');
        $('#AddCommitteeType').append(res.data);
      } 
      else {
        $('.modal-title').text('Committee');
        $('.content-div').text("An error occured while processing the request.");
        $("#myModal").modal('show');
      }
    });
  },

  //Get all category types
  _updateCommitteeTypeOrder: function (groupId, order, oldGroupId, oldOrder) {
    var __this = this

    var ajaxUrl = $('#homeUrl').val() + $('#admin-update-committee-type-order-Url').val();

    $.post(ajaxUrl, // Ajax Get URL
    {
      '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
      currentValue: { groupId: groupId, order: oldOrder },
      oldValue: { oldGroupId: oldGroupId, oldOrder: order}
    },
    function (res) {
      if (typeof (res) != 'undefined' && res.status == 'success') {
       __this._getAllCategoryType();
      } 
      else {
        $('.modal-title').text('Committee');
        $('.content-div').text("An error occured while processing the request.");
        $("#myModal").modal('show');
      }
    });
  },

  //Get committee types
  _getAllCommitteeTypeList: function (isType) {

    var ajaxUrl = $('#homeUrl').val() + $('#admin-get-committee-type-list-Url').val();
    $.get(ajaxUrl, // Ajax Get URL
    {
      
    },
    function (res) {
      if (typeof (res) != 'undefined' && res.status == 'success') {
        if(isType){
          $('#committeeType').html(res.data);
        } else{
          $('#committeeTypeList').html(res.data);
        }
        $('#committeePeriod').html('<option value ="" >Please Select</option>');
        // $('.committeeMembers').html('');
      } 
      else {
        $('.modal-title').text('Committee');
        $('.content-div').text("An error occured while processing the request.");
        $("#myModal").modal('show');
      }
    });
  },

  //Get all search members
  _getCommitteeMembers: function(){ 
    var ajaxUrl = $('#homeUrl').val() + $('#admin-get-committee-members-Url').val();

    $.get(ajaxUrl, // Ajax Get URL
    {
      committeeType: false,
      committeePeriod: false
    },
    function (res) {
      if (typeof (res) != 'undefined' && res.status == 'success') {
        $('.committeeMembers').html('');
        $('.committeeMembers').html(res.data);
      } 
      else {
        $('#CommitteeMemberListErrorDiv').show();
      }
    });
  },

  // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var CommitteeJs = new Remember.committee.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(CommitteeJs)
})