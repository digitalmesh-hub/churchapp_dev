jsFramework.lib.core.utils.registerNamespace('Remember.title.ui')
Remember.title.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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

    //Activate button function - using event delegation for dynamic content
    $(document).on('click', '.btn-activate', function(){
      var ajaxUrl = $('#homeUrl').val() + $('#admin-title-activation-Url').val();
      var id = $(this).attr('data-titleId');
      swal({
        title: 'Are you sure?',
        text: 'Do you want to activate this title',
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
              id: id
            }, // Data
            function (res) {
              if (typeof (res) != 'undefined' && res.status == 'success') {
                swal({title: 'Success', text: 'This title has been activated', type: 'success'},
                  function () {
                    location.reload()
                  }
                )
              } 
              else {
                swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'}),
                  function () {
                    location.reload()
                  }
              }
            }
          )
        }
      )
    });

    //Deactivate button function - using event delegation for dynamic content
    $(document).on('click', '.btn-deactivate', function(){
      var ajaxUrl = $('#homeUrl').val() + $('#admin-title-deactivation-Url').val();
      var id = $(this).attr('data-titleId');
      
      swal({
        title: 'Are you sure?',
        text: 'Do you want to deactivate this title',
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
              id: id
            }, // Data
            function (res) {
              if (typeof (res) != 'undefined' && res.status == 'success') {
                swal({title: 'Success', text: 'This title has been deactivated', type: 'success'},
                  function () {
                    location.reload()
                  }
)
              } 
              else {
                swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'}),
                function () {
                  location.reload()
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
var TitleJs = new Remember.title.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(TitleJs)
})