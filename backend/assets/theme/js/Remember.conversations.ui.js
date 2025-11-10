jsFramework.lib.core.utils.registerNamespace('Remember.conversations.ui.js')
Remember.conversations.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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

    //view button function
    $('.btn-view-conversations').on('click',function(){
      var ajaxUrl = $('#homeUrl').val() + $('#admin-view-conversation-Url').val();
      var topicId = $(this).attr('data-topicid');

      $.get(ajaxUrl, // Ajax Get URL
        {
          topicId: topicId
        },
        function (res) {
          if (typeof (res) != 'undefined' && res.status == 'success') {
            $('.modal-title').text('Conversations');
            $('.content-div').html(res.result);
            $("#myModal").modal('show');
          } 
          else {
            $('.modal-title').text('Conversations');
            $('.content-div').text("An error occured while processing the request.");
            $("#myModal").modal('show');
          }
        }
      )
    });
  },
  _onChangeEvents: function () {
    $('#startdate').on('change',function(e)
    {
        var start_date = $('#startdate').val();
        
        if(start_date != '')
        {
           $('#enddate-kvdate').kvDatepicker('setStartDate', start_date);
        }
    });
  },
  _onLoadEvents: function () {
    var __this = this
  },
  _onKeyEvents: function () {
    var __this = this
  },

    // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var ConversationsJs = new Remember.conversations.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(ConversationsJs)
})