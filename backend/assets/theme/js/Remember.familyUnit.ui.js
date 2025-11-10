jsFramework.lib.core.utils.registerNamespace('Remember.familyUnit.ui')
Remember.familyUnit.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
      var id = $(this).attr('familyunitid')
      var active = $(this).attr('active')
        
      swal({
        title: 'Are you sure?',
        text: 'Do you want to activate this Family Unit',
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
                    swal({title: 'Success', text: 'This Family Unit has been activated', type: 'success'},
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
      var id = $(this).attr('familyunitid')
      var active = $(this).attr('active')
       
      swal({
        title: 'Are you sure?',
        text: 'Do you want to deactivate this Family Unit',
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
                    swal({title: 'Success', text: 'This Family Unit has been deactivated', type: 'success'},
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
  




    $('#addfamilyunit').on('click', function () {
        if($('#familyunittext').val() == '')
        {
          $('#ErrorMessageLabel').show();
        }
        else{
            $('#ErrorMessageLabel').hide();
        }
    })
  },
  _onChangeEvents: function () {

  },
  _onLoadEvents: function () {
    var __this = this

    
    $('#sub-form').on('beforeValidate', function (event, messages) {
      $(this).find('.submit').attr('disabled', true)
    })

    $('#sub-form').on('afterValidate', function (event, messages) {
      event.preventDefault()
      var form = $(this)
      $('.overlay').hide()
      if (form.find('.has-error').length) {
        $('.overlay').hide()
        form.find('.submit').attr('disabled', false)
      } else {
        var count = $('#member-count').val()
        var amount = $('#input-amount').val()
        var message = 'Debit all ' + count + ' members with Rs ' + amount + ' ?'
        bootbox.confirm({
          message: message,
          buttons: {
            confirm: {
              label: 'Continue',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancel',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result) {
              bootbox.confirm({
                message: 'Are you sure ?',
                buttons: {
                  confirm: {
                    label: 'Yes',
                    className: 'btn-success'
                  },
                  cancel: {
                    label: 'Cancel',
                    className: 'btn-danger'
                  }
                },
                callback: function (result) {
                  if (result) {
                    $('.overlay').show()
                    $('#sub-form').submit()
                  } else {
                    window.location.reload()
                  }
                }
              })
            } else {
              window.location.reload()
            }
          }
        })
      }
    })
  },
  _onKeyEvents: function () {
    var __this = this
  },

    // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var FamilyunitJS = new Remember.familyUnit.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(FamilyunitJS)
})
