jsFramework.lib.core.utils.registerNamespace('Remember.statement.ui')
Remember.statement.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    __this._onLoadEvents()
  },
  _onClickEvents: function () {
    var __this = this
    $(document).on('click', '#statement', function () {
            __this._viewStatement(this)
    })
  },
  _viewStatement: function (elem) {
    var memberid = $(elem).attr('data-id')
    var URL = $('#homeUrl').val() + $('#statementUrl').val()
    $.ajax({
      url: URL,
      type: 'post',
      dataType: 'HTML',
      data:
      { 
        '_csrf-backend':$("meta[name='csrf-token']").attr('content'),
        memberid: memberid

      },
      success: function (data) {
        $('.modal-body').html(data)
        $('#transactions').modal('show')
      }
    })
  },
  _showMessage: function () {
        $('#alertModal').modal('show')
  },
    _onLoadEvents: function () {
      var __this = this
        $(document).on('ready pjax:success', function () {
            row = $('#gridTable-filters')
            column = row.children().last()
            column.html('<a href="' + $('#homeUrl').val() + $('#controller').val()+ '/home' + '" class="btn btn-primary" title="Reset">Reset</a>')
          })
        },
    // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var StatementJS = new Remember.statement.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(StatementJS)
})
