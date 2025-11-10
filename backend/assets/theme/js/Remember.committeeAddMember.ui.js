jsFramework.lib.core.utils.registerNamespace('Remember.committeeAddMember.ui')
Remember.committeeAddMember.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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

    //View member details
    $(document).on('click', '.add-member' ,function(e)
    {
      var ajaxUrl = $('#homeUrl').val() + $('#admin-get-committee-member-details-Url').val();
      var memberName = $('.member-name').val().trim();

      memberName = memberName.substr(memberName.indexOf(' ')+1);
      memberName = memberName.split('/');
      var isSpouse = $('.spouse-check').is(':checked');
      var memberId = $('#member-id').val();

      $.get(ajaxUrl, // Ajax Get URL
      {
        memberName:"", 
        isSpouse: isSpouse,
        memberId: memberId 
      },
      function (res) {
        if (typeof (res) != 'undefined' && res.status == 'success') {
          $('#CommitteeMemberDetailsDiv').html(res.data);
        } 
        else {
          $('.modal-title').text('Committee');
          $('.content-div').text("An error occured while processing the request.");
          $("#myModal").modal('show');
        }
      });
    });


    //Save committee
    $(document).on('click', '.saveCommitteeMember', function(){
      var committeeTypeId = $('#committeeTypeId').val();
      var designationType = $('#designationType').val();
      var periodType = $('#periodType').val();

      if(committeeType != '' && designationType != '' && periodType != ''){
        if ($('#MemberMobileLabel').text().trim() != '' || 
          $('#MemberEmailLabel').text().trim() != '') {
            $('#errorDiv').hide();

            var data = {
              "_csrf-backend": $("meta[name='csrf-token']").attr('content'),
              "designationId": $('#designationType').val(),
              "committeePeriodId": $('#periodType').val(),
              "institutionId": $(this).attr('data-institutionid'),
              "memberId": $(this).attr('data-memberid'),
              "userId": $(this).attr('data-userid'),
              "isSpouse": $(this).attr('data-isspouse'),
              "committeeGroupId": $('#committeeTypeId').val()
            };

            var ajaxUrl = $('#admin-save-committee-member-Url').val();
            $.post(ajaxUrl, // Ajax Post URL            
              data,
              function (res) {
                if (typeof (res) != 'undefined' && res.status == 'success') {
                  $('.message').html('');
                  $('.message').removeClass('alert-danger').addClass('alert-success');
                  $('.message').html('<strong>Member  successfully added to committee</strong>');
                  $('#SuccessMessageDiv').show();
                  setTimeout(function() {
                    $('#SuccessMessageDiv').fadeOut();
                  }, 3000);
                  $('.saveCommitteeMember').hide();
                } else if (typeof (res) != 'undefined' && res.status == 'message') {
                  $('#SuccessMessageDiv').show();
                  $('.message').html('');
                  $('.message').removeClass('alert-success').addClass('alert-danger');
                  $('.message').html('<strong>'+res.data+'</strong>');
                  setTimeout(function() {
                    $('#SuccessMessageDiv').fadeOut();
                  }, 3000); 
                } else {
                  $('#SuccessMessageDiv').show();
                  $('.message').html('');
                  $('.message').removeClass('alert-success').addClass('alert-danger');
                  $('.message').html('<strong>An error occured</strong>');
                  setTimeout(function() {
                    $('#SuccessMessageDiv').fadeOut();
                  }, 3000);

                }
              }
            );


        }
        else{
          $('#errorDiv').show();
          $('#errorMessageDiv').html('');
          $('#errorMessageDiv').html('<strong>Invalid contact details. Please contact administrator</strong>');
          setTimeout(function() {
            $('#errorDiv').fadeOut();
          }, 3000);
        }
      }
      else{
        $('#errorDiv').show();
        $('#errorMessageDiv').html('');
        $('#errorMessageDiv').html('<strong>Error!! Please fill in the fields above</strong>');
        setTimeout(function() {
          $('#errorDiv').fadeOut();
        }, 3000);
      }
    });

    $(document).on('click', '.spouse-check', function(e){
      var isSpouse = false;
      var memberName = $('.member-name').val();
      if ($(this).prop('checked') == true){
          isSpouse = true;
      }

      var ajaxUrl = $('#homeUrl').val() + $('#admin-get-member-for-search-Url').val();

      $.post(ajaxUrl, // Ajax Get URL
      {
        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
        isSpouse: isSpouse,
        memberName: ''
      },
      function (res) {
        if (typeof (res) != 'undefined' && res.status == 'success') {
          $( ".member-name" ).autocomplete({source: res.list});
          $('#CommitteeMemberDetailsDiv').html('');
          $('.member-name').val('');
          /*$('#AddMemberDiv').html(res.data);*/
          if (isSpouse == true) {
              $('.spouse-check').prop('checked', true);
          }
        } else {
          $('.modal-title').text('Committee');
          $('.content-div').text("An error occured while processing the request.");
          $("#myModal").modal('show');
        }
      });
    });
  },
  _onChangeEvents: function () {
    var __this = this
    
    //get committee period
    $(document).on('change', '#committeeTypeId' ,function(e)
    {
        var committeeType = $('#committeeTypeId').val();

        if(committeeType != '' && committeeType != undefined){
          __this._getAllCommitteePeriodByType(committeeType);
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
  _getAllCommitteePeriodByType: function (committeeType) {

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
        $('#periodType  ').html(html);
      } 
      else {
        $('.modal-title').text('Committee');
        $('.content-div').text("An error occured while processing the request.");
        $("#myModal").modal('show');
      }
    });
  },

  // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var CommitteeAddMemberJs = new Remember.committeeAddMember.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(CommitteeAddMemberJs)
})