jsFramework.lib.core.utils.registerNamespace('Remember.designation.ui')
Remember.designation.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    var __this = this
    $(document).on('click', '#btn-activate', function () {
      var ajaxUrl = $('#homeUrl').val() + $(this).attr('url')
      var id = $(this).attr('staffdesignationid')
      var active = $(this).attr('active')
        
      swal({
        title: 'Are you sure?',
        text: 'Do you want to activate this Staff Designation',
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
              
                 if (typeof (res) !== 'undefined' && res.status === 'success') {
                    swal({title: 'Success', text: 'This Staff Designation has been activated', type: 'success'},
                                function () {
                                  location.reload()
                                }
                    )
                  } else {
                    swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'}),
                    function () {
                      location.reload()
                    }
                  }
                })
        })
    })
    $(document).on('click', '#btn-deactivate', function () {
      var ajaxUrl = $('#homeUrl').val() + $(this).attr('url')
      var id = $(this).attr('staffdesignationid')
      var active = $(this).attr('active')
      swal({
        title: 'Are you sure?',
        text: 'Do you want to deactivate this Staff Designation',
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
              id: id
            }, // Data
                function (res) {
            	
                  if (typeof (res) !== 'undefined' && res.status === 'success') {
                    swal({title: 'Success', text: 'This Staff Designation has been deactivated', type: 'success'},
                        function () {
                          location.reload()
                        }
                    )
                     
                  } else {
                    swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'}),
                      function () {
                        location.reload()
                      }
                    // )
                  }
            })
           // location.reload();
        })
    })

  /*  $('#addstaffdesignation').on('click', function () {
        if($('#staffdesignationtext').val() == '')
        {
          $('#ErrorMessageLabel').show();
        }
        else{
            $('#ErrorMessageLabel').hide();
        }
    })*/
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
var DesignationJS = new Remember.designation.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(DesignationJS)
})
