jsFramework.lib.core.utils.registerNamespace('Remember.subscription.ui')
Remember.subscription.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    $('#btn-delete').on('click', function (event, messages) {
      var href = $(this).attr('href')
      event.preventDefault()
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
            window.location.href = href // redirect with stored href
          }
        }
      })
    })
  },
  _onChangeEvents: function () {

  },
  _onLoadEvents: function () {
    var __this = this

    $(document).ready(function () {
      var date = new Date(), y = date.getFullYear(), m = date.getMonth()
      $('.subscripitonTransactionDate').datepicker({
        startDate: new Date(y, m, 1),
        endDate: new Date(y, m + 1, 0),
        format: 'dd-mm-yyyy'

      })
    })
    $('#sub-form').on('beforeValidate', function (event, messages) {
        $(this).find('.submit').attr('disabled', true)
    })


    $('#sub-form').on('beforeSubmit', function (e) {
        var form = $(this)

        var count = $('#member-count').val()
        var amount = $('#input-amount').val()
        var message = 'Debit all ' + count + ' members with Rs ' + amount + ' ?'
        form.find('.submit').attr('disabled', false)
        swal({
            title: 'Are you sure?',
            text: message,
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            confirmButtonText: 'Yes',
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        },function(){
            allowSubmit = true;
            form.submit();
        });
    }).on('submit', function (e) {
        return allowSubmit;
    });


    // $('#sub-form').on('beforeSubmit', function (event, messages) {
    //   event.preventDefault()
      
    //   $('.overlay').hide()
    //   if (form.find('.has-error').length) {
    //     $('.overlay').hide()
    //     form.find('.submit').attr('disabled', false)
    //   } else {
    //     var count = $('#member-count').val()
    //     var amount = $('#input-amount').val()
    //     var message = 'Debit all ' + count + ' members with Rs ' + amount + ' ?'
    //     bootbox.confirm({
    //       message: message,
    //       buttons: {
    //         confirm: {
    //           label: 'Continue',
    //           className: 'btn-success'
    //         },
    //         cancel: {
    //           label: 'Cancel',
    //           className: 'btn-danger'
    //         }
    //       },
    //       callback: function (result) {
    //         if (result) {
    //           bootbox.confirm({
    //             message: 'Are you sure ?',
    //             buttons: {
    //               confirm: {
    //                 label: 'Yes',
    //                 className: 'btn-success'
    //               },
    //               cancel: {
    //                 label: 'Cancel',
    //                 className: 'btn-danger'
    //               }
    //             },
    //             callback: function (result) {
    //               if (result) {
    //                 $('.overlay').show()
    //                 $('#sub-form').submit()
    //               } else {
    //                 window.location.reload()
    //               }
    //             }
    //           })
    //         } else {
    //           window.location.reload()
    //         }
    //       }
    //     })
    //   }
    // })
  },
  _onKeyEvents: function () {
    var __this = this
  },

    // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var SubscriptionJS = new Remember.subscription.ui.PageBuilder({})
var allowSubmit = false;
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(SubscriptionJS)
})
