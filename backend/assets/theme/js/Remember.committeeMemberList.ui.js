jsFramework.lib.core.utils.registerNamespace('Remember.committeeMemberList.ui')
Remember.committeeMemberList.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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

    $(document).on('click', '#search-committee-member', function(){
      $('.help-block').css('display', 'none');
      var committeeType = $('#committeeType').val().trim();
      var committeePeriod = $('#committeePeriod').val().trim();

      if(committeeType == ''){
        $('.field-committeeType').find('.help-block').css('display', 'block');
        $('.help-block').addClass('error-ms');
        $('.help-block').css('color', '#a94442');
        $('.field-committeeType').find('.help-block').html('Committee name cannot be blank.');
      }
      else if(committeePeriod == 'Please Select'){
        $('.field-committeePeriod').find('.help-block').css('display', 'block');
        $('.help-block').addClass('error-ms');
        $('.help-block').css('color', '#a94442');
        $('.field-committeePeriod').find('.help-block').html('Committee period cannot be blank.');
      }
      else{
        $('#CommitteeMemberListErrorDiv').hide();
        __this._getCommitteeMembers(committeeType, committeePeriod);
      }

    });

    $(document).on('click', '.delete-committee-member', function(){
      var committeeId = $(this).attr('data-committeeid');
      var ajaxUrl = $('#homeUrl').val() + $('#admin-delete-committee-member-Url').val();

      swal({
        title: 'Are you sure?',
        text: 'Do you need to delete this member from Committee?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        confirmButtonText: 'Yes',
        closeOnConfirm: false
      },
      function () {
        $.post(ajaxUrl, // Ajax Get URL
        {
          "_csrf-backend": $("meta[name='csrf-token']").attr('content'),
          committeeId: committeeId
        },
        function (res) {
        	console.log(res)
          if (typeof (res) != 'undefined' && res.status == 'success') {
            swal({title: 'Success', text: 'This committee member  has been deleted', type: 'success'},
              function () {
                var committeeType = $('#committeeType').val();
                var committeePeriod = $('#committeePeriod').val();
                __this._getCommitteeMembers(committeeType, committeePeriod);
              }
            )
          } 
          else {
            swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'}),
                function () {
                  //reload
            }
          }
        });
      })
    })

  },
  _onChangeEvents: function () {

    var __this = this

    //get committee period
    $(document).on('change', '#committeeType' ,function(e)
    {
      var committeeType = $('#committeeType').val();
      if(committeeType != '' && committeeType != undefined){
        __this._getAllCommitteePeriod(committeeType);
      }
    });
  },
  _onLoadEvents: function () {
    var __this = this
  },
  _onKeyEvents: function () {
    var __this = this
  },

  //Get all category types
  _getAllCommitteePeriod: function (committeeType) {

    var ajaxUrl = $('#homeUrl').val() + $('#admin-get-period-by-type-Url').val();

    $.get(ajaxUrl, // Ajax Get URL
    {
      committeeType: committeeType
    },
    function (res) {
      if (typeof (res) != 'undefined' && res.status == 'success') {
        html = '<option>Please Select</option>';
        $.each(res.data, function(key, value){
          html += '<option value='+key+'>'+value+'</option>';
        });
        $('#committeePeriod').html(html);
      } 
      else {
        $('.modal-title').text('Committee');
        $('.content-div').text("An error occured while processing the request.");
        $("#myModal").modal('show');
      }
    });
  },

  //Get all search members
  _getCommitteeMembers: function(committeeType, committeePeriod){ 
    var ajaxUrl = $('#homeUrl').val() + $('#admin-get-committee-members-Url').val();

    $.get(ajaxUrl, // Ajax Get URL
    {
      committeeType: committeeType,
      committeePeriod: committeePeriod
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
var CommitteeMemberListJs = new Remember.committeeMemberList.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(CommitteeMemberListJs)
})