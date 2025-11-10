jsFramework.lib.core.utils.registerNamespace('Remember.committeeDesignation.ui')
Remember.committeeDesignation.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    $(document).on('click','.add-committee-designation', function(){
      var designation = $('.designation').val().trim();

      if(designation == '' || designation == undefined){
        $('.field-extendeddesignation-description').find('.help-block').css('display', 'block');
        $('.field-extendeddesignation-description').find('.help-block').addClass('error-ms');
        $('.help-block').css('color', '#a94442');
        $('.field-extendeddesignation-description').find('.help-block').html('Committee designation Cannot be blank.');
      }
      else{
        $('.help-block').hide();
        var ajaxUrl = $('#homeUrl').val() + $('#admin-save-committee-designation-Url').val();
        var designationId = $('#designationid').val();
        $.post(ajaxUrl, // Ajax Get URL
        {
          '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          designationId: designationId,
          designation: designation
        },
        function (res) {
          if (typeof (res) != 'undefined' && res.status == 'success') {
            $('.designation').val('');
            $('#designationid').val(0);
            __this._getAllCategoryDesignation();
          } 
          else {
            if(res.data != null && res.data != undefined){
              $('.field-extendeddesignation-description').find('.help-block').css('display', 'block');
              $('.field-extendeddesignation-description').find('.help-block').addClass('error-ms');
              $('.help-block').css('color', '#a94442');
              $('.field-extendeddesignation-description').find('.help-block').html(res.data['description']);
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
    $(document).on('click', '.edit-committee-designation', function(){
      var designationid = $(this).attr('data-designationid');
      var description = $(this).attr('data-description');

      $('.designation').val(description);
      $('#designationid').val(designationid);
      $('.add-committee-designation').hide();
      $('.button-designation-update').show();
      $('.designation').focus();
    });

    //Clear button function
    $(document).on('click','.button-clear', function(){
      $(".add-committee-designation").show();
      $('.button-designation-update').hide();
      $('.designation').val('');
      $('.help-block').html('');
      $('#designationid').val(0);
    });

    //Sort order down function
    $(document).on('click', '.designation-down', function(){
      var designationId = $(this).attr('data-key');
      var currentOrder = $(this).attr('data-order');
      var nextDesignationId = $($('#designation_'+designationId).next()).attr('data-key');
      var nextOrder = $($('#designation_'+designationId).next()).attr('data-order');
      // $('#group_'+groupId).insertAfter($($('#group_'+groupId).next()));
      __this._updateCommitteeDesignationOrder(designationId, currentOrder, nextDesignationId, nextOrder)
    });

    //Sort order up function
    $(document).on('click', '.designation-up', function(){
      var designationId = $(this).attr('data-key');
      var currentOrder = $(this).attr('data-order');
      var prevDesignationId = $($('#designation_'+designationId).prev()).attr('data-key');
      var prevOrder = $($('#designation_'+designationId).prev()).attr('data-order');
      // $('#group_'+groupId).insertBefore($($('#group_'+groupId).prev()));
      __this._updateCommitteeDesignationOrder(designationId, currentOrder, prevDesignationId, prevOrder);
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
  _getAllCategoryDesignation: function () {

    var ajaxUrl = $('#homeUrl').val() + $('#admin-get-committee-designation-Url').val();

    $.get(ajaxUrl, // Ajax Get URL
    {
      //data
    },
    function (res) {
      if (typeof (res) != 'undefined' && res.status == 'success') {
        $('#AddCommitteeDesignationDiv').html('');
        $('#AddCommitteeDesignationDiv').append(res.data);
      } 
      else {
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


  // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var CommitteeDesignationJs = new Remember.committeeDesignation.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(CommitteeDesignationJs)
})