jsFramework.lib.core.utils.registerNamespace('Remember.account.ui')
Remember.account.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    $(document).on('click', '#btn-admin-deactivate', function () {
      var ajaxUrl = $('#homeUrl').val() + $('#admin-deactivation-Url').val()
      var id = $(this).attr('data-profile-id')
      swal({
        title: 'Are you sure?',
        text: 'Do you want to deactivate this admin',
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
                    swal({title: 'Success', text: 'The Admin has been deactivated', type: 'success'},
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
    }),
    $(document).on('click', '#extendedusercredentials-editpassword', function() {
      checkbox = $('#extendedusercredentials-editpassword');
        if($(checkbox). prop("checked") == true){
            $('#divChangepassword').show();
        } else {
          $('#extendeduser-userpassword').val(''); 
          $('#extendeduser-userconfirmpassword').val(''); 
          $('#divChangepassword').hide();
        }
    });
    $(document).on('click', '#btn-admin-activate', function () {
      var ajaxUrl = $('#homeUrl').val() + $('#admin-activation-Url').val()
      var id = $(this).attr('data-profile-id')
      swal({
        title: 'Are you sure?',
        text: 'Do you want to activate this admin',
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
                    swal({title: 'Success', text: 'The admin has been activated', type: 'success'},
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
  },
  _onChangeEvents: function () {
    var __this = this
    $(document).on('change', '#institution_name', function () {
      __this._roleCategory()
    })
    $(document).on('change', '#role-category', function () {
      __this._roles()
    })
    $(document).on('change', '#institution-name', function (e) {
      this.form.submit()
    })
  },
  _onLoadEvents: function () {
    var __this = this
    $( document ).ready(function() {
       var $url = $('#account-dep-drop-Url').val()
        if ($url != undefined) {
            __this._roleCategory()
      }
    });
  },
  _onKeyEvents: function () {
    var __this = this
  },

  _roleCategory: function () {
    var __this = this
    var ajaxUrl = $('#homeUrl').val() + $('#account-dep-drop-Url').val()
    var institutionId = $('#institution_name').val()
    var userId = $('#extendedusercredentials-id').val()
    if (institutionId != undefined && (institutionId != '' || institutionId != null)) {
      $.post(ajaxUrl, // Ajax Post URL
        {
          '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          institutionId: institutionId,
          userId: userId,
          type: 'role-category'
        },
                    function (res) {
                      $('select#role-category').html(res)
                      __this._roles()
                    })
    }
  },

  _roles: function () {
    var ajaxUrl = $('#homeUrl').val() + $('#account-dep-drop-Url').val()
    var institutionId = $('#institution_name').val()
    var roleCategoryId = $('#role-category').val()
    var userId = $('#extendedusercredentials-id').val()
    if ((institutionId != '' || institutionId != null) && (roleCategoryId != '' || roleCategoryId != null)) {
      $.post(ajaxUrl, // Ajax Post URL
        {
          '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          institutionId: institutionId,
          roleCategoryId: roleCategoryId,
          userId: userId,
          type: 'role'
        },
                    function (res) {
                      $('select#roles').html(res)
                    })
    }
  },

    // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var AccountJS = new Remember.account.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(AccountJS)
})
