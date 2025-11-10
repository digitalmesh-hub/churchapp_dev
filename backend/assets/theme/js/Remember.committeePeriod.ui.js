jsFramework.lib.core.utils.registerNamespace('Remember.committeePeriod.ui')
Remember.committeePeriod.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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

    //Add committee type 
    $(document).on('click','.add-committee-period', function(){
      var committeeType = $('#committeeTypeList').val();
      var periodFrom = $('.period_from').val().trim();
      var periodTo = $('.period_to').val();

      if(committeeType == '' || committeeType == undefined){
        $('.field-committeeTypeList').find('.help-block').addClass('error-ms');
        $('.field-committeeTypeList').find('.help-block').css('display', 'block');
        $('.field-committeeTypeList').find('.help-block').html('Committee Type Cannot be blank.');
        $('.help-block').css('color', '#a94442');
      }
      else if(periodFrom == '' || periodFrom == undefined){
        $('.field-extendedcommitteeperiod-period_from').find('.help-block').addClass('error-ms');
         $('.field-extendedcommitteeperiod-period_from').find('.help-block').css('display', 'block');
        $('.help-block').css('color', '#a94442');
        $('.field-extendedcommitteeperiod-period_from').find('.help-block').html('Start Date cannot be blank.')

      }
      else if(periodTo == '' || periodTo == undefined){
        $('.field-extendedcommitteeperiod-period_to').find('.help-block').addClass('error-ms');
         $('.field-extendedcommitteeperiod-period_to').find('.help-block').css('display', 'block');
        $('.help-block').css('color', '#a94442');
        $('.field-extendedcommitteeperiod-period_to').find('.help-block').html('End Date cannot be blank.');

      }
      else if(new Date(periodFrom) >= new Date(periodTo)){
        $('.field-extendedcommitteeperiod-period_to').find('.help-block').addClass('error-ms');
         $('.field-extendedcommitteeperiod-period_to').find('.help-block').css('display', 'block');
        $('.help-block').css('color', '#a94442');
        $('.field-extendedcommitteeperiod-period_to').find('.help-block').html('End Date should be greater than Start Date.');
      }
      else{
        $('.help-block').hide();
        var ajaxUrl = $('#homeUrl').val() + $('#admin-save-committee-period-Url').val();
        var committeePeriodId = $('#committee-period-id').val();
        $.post(ajaxUrl, // Ajax Get URL
        {
          '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          committeePeriodId: committeePeriodId,
          committeeType: committeeType,
          periodFrom: periodFrom,
          periodTo: periodTo
        },
        function (res) {
          if (typeof (res) != 'undefined' && res.status == 'success') {
            $('#committeeTypeList').val('');
            $('#committeeTypeList').prop('disabled',false);
            $('#committee-period-id').val(0);
            $('.add-committee-period').show();
            $('.update-committee-period').hide();
            __this. _getAllCategoryPeriod();
          }
          else if (typeof (res) != 'undefined' && res.status == 'invalid') {
            $('#CommitteePeriodErrorDiv').css('display','block')
            $('#CommitteePeriodErrorMessageLabel').html('<strong>'+res.data+'</strong>');
            setTimeout(function () {
              $('#CommitteePeriodErrorDiv').css('display','none');
            },3000);
          } 
          else {
            if(res.data != null && res.data != undefined){
              if(res.data['committeegroupid'] != undefined){
                $('.field-committeeTypeList').find('.help-block').css('display', 'block');
                $('.field-committeeTypeList').find('.help-block').addClass('error-ms');
                $('.help-block').css('color', '#a94442');
                $('.field-committeeTypeList').find('.help-block').html(res.data['committeegroupid']);
              }
              else if(res.data['period_from'] != undefined){
                $('.field-extendedcommitteeperiod-period_from').find('.help-block').addClass('error-ms');
                 $('.field-extendedcommitteeperiod-period_from').find('.help-block').css('display', 'block');
                $('.help-block').css('color', '#a94442');
                $('.field-extendedcommitteeperiod-period_from').find('.help-block').html(res.data['period_from'])

              }
              else if(res.data['period_to'] != undefined){
                $('.field-extendedcommitteeperiod-period_to').find('.help-block').addClass('error-ms');
                $('.field-extendedcommitteeperiod-period_to').find('.help-block').css('display', 'block');
                $('.help-block').css('color', '#a94442');
                $('.field-extendedcommitteeperiod-period_to').find('.help-block').html(res.data['period_to']);

              }
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

    //Edit committee type
    $(document).on('click', '.edit-period', function(){
      var committeePeriodId = $(this).attr('data-period_id');
      var committeeTypeId = $(this).attr('data-committe-type');
      var periodFrom = $(this).attr('data-startDate');
      var periodTo = $(this).attr('data-endDate');

      $('#committeeTypeList').val(committeeTypeId);
      $('#committee-period-id').val(committeePeriodId);
      $('.period_from').val(periodFrom);
      $('.period_to').val(periodTo);
      $('.add-committee-period').hide();
      $('.update-committee-period').show();
      $('#committeeTypeList').prop('disabled', true);
      $('#committeeTypeList').focus();

    });

    //Activate/Deactivate committee type
    $(document).on('click', '.btn-active', function(){
      var ajaxUrl = $('#homeUrl').val() + $('#admin-activate-deactivate-committee-period-Url').val();
      var committeePeriodId = $(this).attr('data-committee_period_id');
      var active = $(this).attr('data-active');

      if(active == '1'){
        var text = 'Do you need to deactivate this committee period';
        active = '0';
        var message = 'This committee period  has been deactivated';
      }
      else{
        var text = 'Do you need to activate this committee period';
        active = '1';
        var message = 'This committee period  has been activated';
      }

      swal({
        title: 'Are you sure?',
        text: text,
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
              committeePeriodId: committeePeriodId,
              active: active
            }, // Data
            function (res) {
              if (typeof (res) != 'undefined' && res.status == 'success') {
                swal({title: 'Success', text: message, type: 'success'},
                  function () {
                    __this. _getAllCategoryPeriod();
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

   

  },
  _onChangeEvents: function () {
    var __this = this
    $(document).on('change', '.period_from' ,function(e)
    {
        var periodFrom = $('.period_from').val();
        var formattedDate = __this._dateFormat(periodFrom);
        if(formattedDate != undefined && formattedDate != '')
        {
           $('#extendedcommitteeperiod-period_to-kvdate').kvDatepicker('setStartDate', formattedDate);
           $('.period_to').val(formattedDate);
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
  _getAllCategoryPeriod: function () {
    var ajaxUrl = $('#homeUrl').val() + $('#admin-get-committee-period-Url').val();
    $.get(ajaxUrl, // Ajax Get URL
    {
      //data
    },
    function (res) {
      if (typeof (res) != 'undefined' && res.status == 'success') {
        /*$('#AddCommitteePeriodDiv').html('');
        $('#AddCommitteePeriodDiv').append(res.data);*/
        $('#fill-data').html(res.data)
      } else {
        $('.modal-title').text('Committee');
        $('.content-div').text("An error occured while processing the request.");
        $("#myModal").modal('show');
      }
    });
  },

  //Get all category types
  _updateCommitteeDesignationOrder: function (designationId, order, oldDesignationId, oldOrder) {
    var __this = this

    var ajaxUrl = $('#homeUrl').val() + $('#admin-update-committee-designation-order-Url').val();

    $.post(ajaxUrl, // Ajax Get URL
    {
      '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
      currentValue: { designationId: designationId, order: oldOrder },
      oldValue: { oldDesignationId: oldDesignationId, oldOrder: order}
    },
    function (res) {
      if (typeof (res) != 'undefined' && res.status == 'success') {
       __this._getAllCategoryDesignation();
      } 
      else {
        $('.modal-title').text('Committee');
        $('.content-div').text("An error occured while processing the request.");
        $("#myModal").modal('show');
      }
    });
  },

  ////Date format
  _dateFormat: function(dateString){
    var fullMonth = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];
    var objDate = new Date(dateString);
    var objDate = objDate.setDate(objDate.getDate() +1);

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


  // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var CommitteePeriodJs = new Remember.committeePeriod.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(CommitteePeriodJs)
})