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

    //View member details - now fetches member + spouse + dependants
    $(document).on('click', '.add-member' ,function(e)
    {
      var ajaxUrl = $('#homeUrl').val() + $('#admin-get-committee-member-details-Url').val();
      var memberName = $('.member-name').val().trim();

      memberName = memberName.substr(memberName.indexOf(' ')+1);
      memberName = memberName.split('/');
      var memberId = $('#member-id').val();

      $.get(ajaxUrl, // Ajax Get URL
      {
        memberName:"", 
        memberId: memberId 
      },
      function (res) {
        if (typeof (res) != 'undefined' && res.status == 'success') {
          $('#CommitteeMemberDetailsDiv').html(res.data);
          
          // Reset selection
          __this._clearPersonSelection();
        } 
        else {
          $('.modal-title').text('Committee');
          $('.content-div').text("An error occured while processing the request.");
          $("#myModal").modal('show');
        }
      });
    });

    // Select person (member/spouse/dependant) for committee
    $(document).on('click', '.select-person-for-committee', function(){
      var personType = $(this).data('person-type');
      var memberId = $(this).data('member-id');
      var userId = $(this).data('user-id');
      var institutionId = $(this).data('institution-id');
      var isSpouse = $(this).data('is-spouse');
      var dependantId = $(this).data('dependant-id') || '';
      var personName = $(this).data('person-name');
      
      // Update hidden fields
      $('#selected-person-type').val(personType);
      $('#selected-member-id').val(memberId);
      $('#selected-user-id').val(userId);
      $('#selected-institution-id').val(institutionId);
      $('#selected-is-spouse').val(isSpouse);
      $('#selected-dependant-id').val(dependantId);
      $('#selected-person-name').val(personName);
      
      // Update UI
      var typeLabel = personType.charAt(0).toUpperCase() + personType.slice(1);
      var badgeClass = 'label-primary';
      if (personType === 'spouse') badgeClass = 'label-danger';
      if (personType === 'dependant') badgeClass = 'label-warning';
      
      $('#AssignmentTypeLabel').html(
        '<i class="glyphicon glyphicon-user"></i> Assign ' + typeLabel + ' to Committee: ' +
        '<span class="label ' + badgeClass + '" style="font-size: 14px; margin-left: 10px;">' + 
        personName + '</span>'
      );
      
      $('#SelectedPersonInfo').html(
        '<strong><i class="glyphicon glyphicon-ok-circle"></i> Selected:</strong> ' +
        '<span class="label ' + badgeClass + '">' + typeLabel.toUpperCase() + '</span> ' +
        personName + ' will be added to the committee.'
      );
      
      // Highlight selected card
      $('.person-card').removeClass('selected-person');
      $(this).closest('.person-card').addClass('selected-person');
      
      // Show assignment section
      $('#CommitteeAssignmentSection').slideDown();
      
      // Scroll to assignment section
      $('html, body').animate({
        scrollTop: $('#CommitteeAssignmentSection').offset().top - 50
      }, 500);
    });

    // Cancel selection
    $(document).on('click', '.cancel-selection', function(){
      __this._clearPersonSelection();
    });


    //Save committee
    $(document).on('click', '.saveCommitteeMember', function(){
      var committeeTypeId = $('#committeeTypeId').val();
      var designationType = $('#designationType').val();
      var periodType = $('#periodType').val();
      var personType = $('#selected-person-type').val();
      var memberId = $('#selected-member-id').val();
      var userId = $('#selected-user-id').val();
      var institutionId = $('#selected-institution-id').val();
      var isSpouse = $('#selected-is-spouse').val();
      var dependantId = $('#selected-dependant-id').val();
      var personName = $('#selected-person-name').val();

      if(!personType || !memberId) {
        $('#errorDiv').show();
        $('#errorMessageDiv').html('<strong>Please select a person to add to committee</strong>');
        setTimeout(function() {
          $('#errorDiv').fadeOut();
        }, 3000);
        return;
      }

      if(committeeTypeId != '' && designationType != '' && periodType != ''){
        $('#errorDiv').hide();

        // Ensure userId is a valid value (convert empty string to null or 0)
        if (!userId || userId.trim() === '') {
          userId = '0';
        }

        var data = {
          "_csrf-backend": $("meta[name='csrf-token']").attr('content'),
          "designationId": designationType,
          "committeePeriodId": periodType,
          "institutionId": institutionId,
          "memberId": memberId,
          "userId": userId,
          "isSpouse": isSpouse,
          "committeeGroupId": committeeTypeId
        };
        
        // Add dependant info if selected
        if (personType === 'dependant' && dependantId) {
          data.dependantId = dependantId;
        }

        var ajaxUrl = $('#admin-save-committee-member-Url').val();
        $.post(ajaxUrl, // Ajax Post URL            
          data,
          function (res) {
            if (typeof (res) != 'undefined' && res.status == 'success') {
              $('.message').html('');
              $('.message').removeClass('alert-danger').addClass('alert-success');
              var successMsg = '<strong>' + personName + ' successfully added to committee as ' + 
                             personType + '</strong>';
              $('.message').html(successMsg);
              $('#SuccessMessageDiv').show();
              setTimeout(function() {
                $('#SuccessMessageDiv').fadeOut();
                __this._clearPersonSelection();
                $('#CommitteeMemberDetailsDiv').html('');
                $('.member-name').val('');
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
        $('#errorMessageDiv').html('<strong>Error!! Please fill in the fields above</strong>');
        setTimeout(function() {
          $('#errorDiv').fadeOut();
        }, 3000);
      }
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

  // Clear person selection
  _clearPersonSelection: function () {
    $('#selected-person-type').val('');
    $('#selected-member-id').val('');
    $('#selected-user-id').val('');
    $('#selected-institution-id').val('');
    $('#selected-is-spouse').val('');
    $('#selected-dependant-id').val('');
    $('#selected-person-name').val('');
    
    $('.person-card').removeClass('selected-person');
    $('#CommitteeAssignmentSection').slideUp();
    $('#AssignmentTypeLabel').html('<i class="glyphicon glyphicon-user"></i> Assign to Committee');
    $('#SelectedPersonInfo').html('');
    $('.saveCommitteeMember').show();
    
    // Reset form
    $('#committeeTypeId').val('');
    $('#designationType').val('');
    $('#periodType').html('<option>Please Select</option>');
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