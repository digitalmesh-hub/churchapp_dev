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
          
          // Reset dependant selection flags
          $('#selected-dependant-id').val('');
          $('#is-dependant-selected').val('0');
          
          // Load dependants for this member
          __this._loadMemberDependants(memberId);
        } 
        else {
          $('.modal-title').text('Committee');
          $('.content-div').text("An error occured while processing the request.");
          $("#myModal").modal('show');
        }
      });
    });

    // Select dependant for committee
    $(document).on('click', '.select-dependant-for-committee', function(){
      var dependantId = $(this).data('dependantid');
      var dependantName = $(this).data('dependantname');
      var relation = $(this).data('relation');
      
      // Update hidden fields
      $('#selected-dependant-id').val(dependantId);
      $('#is-dependant-selected').val('1');
      
      // Update UI to show selected dependant
      $('#AssignmentTypeLabel').html('<i class="glyphicon glyphicon-user"></i> Assign Dependant: <strong>' + dependantName + '</strong> (' + relation + ')');
      $('#CommitteeAssignmentSection').find('th:first').css('background-color', '#FFF4E6').css('color', '#ff9800');
      
      // Update save button data attributes
      $('.saveCommitteeMember').attr('data-dependantid', dependantId);
      
      // Highlight selected dependant
      $('.dependant-card').removeClass('selected-dependant');
      $(this).closest('.dependant-card').addClass('selected-dependant');
      
      // Show success message
      $('#DependantsListDiv').prepend(
        '<div class="alert alert-success temp-alert" style="margin: 10px;">' +
        '<i class="glyphicon glyphicon-ok"></i> ' +
        '<strong>Selected:</strong> ' + dependantName + ' will be added to the committee. ' +
        '<button type="button" class="btn btn-xs btn-default clear-dependant-selection" style="margin-left: 10px;">' +
        '<i class="glyphicon glyphicon-remove"></i> Clear Selection (Add Member Instead)' +
        '</button>' +
        '</div>'
      );
      
      // Scroll to assignment section
      $('html, body').animate({
        scrollTop: $('#CommitteeAssignmentSection').offset().top - 100
      }, 500);
    });

    // Clear dependant selection
    $(document).on('click', '.clear-dependant-selection', function(){
      $('#selected-dependant-id').val('');
      $('#is-dependant-selected').val('0');
      $('#AssignmentTypeLabel').html('Assign Member To Committe');
      $('#CommitteeAssignmentSection').find('th:first').css('background-color', '#ADF7EE').css('color', '');
      $('.saveCommitteeMember').removeAttr('data-dependantid');
      $('.dependant-card').removeClass('selected-dependant');
      $('.temp-alert').remove();
    });


    //Save committee
    $(document).on('click', '.saveCommitteeMember', function(){
      var committeeTypeId = $('#committeeTypeId').val();
      var designationType = $('#designationType').val();
      var periodType = $('#periodType').val();
      var isDependantSelected = $('#is-dependant-selected').val();
      var dependantId = $('#selected-dependant-id').val();

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
            
            // Add dependant info if selected
            if (isDependantSelected == '1' && dependantId) {
              data.dependantId = dependantId;
            }

            var ajaxUrl = $('#admin-save-committee-member-Url').val();
            $.post(ajaxUrl, // Ajax Post URL            
              data,
              function (res) {
                if (typeof (res) != 'undefined' && res.status == 'success') {
                  $('.message').html('');
                  $('.message').removeClass('alert-danger').addClass('alert-success');
                  var successMsg = isDependantSelected == '1' ? 
                    '<strong>Dependant successfully added to committee</strong>' : 
                    '<strong>Member successfully added to committee</strong>';
                  $('.message').html(successMsg);
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

  // Load member's dependants
  _loadMemberDependants: function (memberId) {
    var ajaxUrl = $('#homeUrl').val() + $('#admin-get-member-dependants-Url').val();
    
    $.get(ajaxUrl, {
      memberId: memberId
    },
    function (res) {
      if (typeof (res) != 'undefined' && res.status == 'success') {
        if (res.count > 0) {
          // Show dependants section
          $('#MemberDependantsSection').show();
          
          var html = '';
          $.each(res.data, function(index, dependant) {
            html += '<div class="dependant-card" style="border: 1px solid #ddd; margin: 10px; padding: 15px; border-radius: 5px; background: #f9f9f9;">';
            html += '<div class="row">';
            html += '<div class="col-md-8">';
            html += '<h4 style="margin-top: 0; color: #333;">';
            if (dependant.title) {
              html += dependant.title + ' ';
            }
            html += '<strong>' + dependant.dependantname + '</strong></h4>';
            html += '<p style="margin: 5px 0;"><strong>Relation:</strong> ' + (dependant.relation || 'Not specified') + '</p>';
            if (dependant.dob) {
              html += '<p style="margin: 5px 0;"><strong>DOB:</strong> ' + dependant.dob + '</p>';
            }
            if (dependant.dependantmobile) {
              html += '<p style="margin: 5px 0;"><strong>Mobile:</strong> ' + dependant.dependantmobile + '</p>';
            }
            html += '</div>';
            html += '<div class="col-md-4 text-right">';
            html += '<button class="btn btn-success btn-lg select-dependant-for-committee" ';
            html += 'data-dependantid="' + dependant.dependantid + '" ';
            html += 'data-dependantname="' + dependant.dependantname + '" ';
            html += 'data-relation="' + (dependant.relation || '') + '" ';
            html += 'style="margin-top: 20px;">';
            html += '<i class="glyphicon glyphicon-plus-sign"></i> Add to Committee';
            html += '</button>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
          });
          
          $('#DependantsListDiv').html(html);
        } else {
          // No dependants, hide section
          $('#MemberDependantsSection').hide();
        }
      } else {
        // Error or no dependants
        $('#MemberDependantsSection').hide();
      }
    });
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