jsFramework.lib.core.utils.registerNamespace('Remember.roles.ui')
Remember.roles.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    $(document).on('click', '#btn-category-save', function (e) {
      e.preventDefault()
      var roleGroup = $('#role_group').val()
      if (roleGroup == '' || roleGroup == null) {
        swal({
          title: '',
          text: 'Please select a Role'
        })
      } else {
        if (__this._ValidateBeforeSave()) {
          $('#role-category-form').submit()
        } else {
          swal({
            title: '',
            text: 'Please enter role category'
          })
        }
      }
    })
    $(document).on('click', '#add-new-category', function (e) {
        // ROLE CATEGORY
      var fieldHTML = '<div><li><input type="text" name="field_name[]" class="form-control category" value="" id="categorytextbox" categoryid="0" isdeleted="0" maxlength="100"/><a href="javascript:void(0);" class="remove" title="Remove field"> <span class = "delete-icon"></span></a></li></div>' // New input field html
      if (__this._ValidateBeforeSave()) {
        $('.field_wrapper').append(fieldHTML) // Add field html
      } else {
        swal({
          title: '',
          text: 'Please fill the previous text box'
        })
      }
    })
    $(document).on('click', '.remove', function (e) {
      e.preventDefault()
      var rolecategoryid = $(this).parent().parent('div').children().children().attr('categoryid')
      if (rolecategoryid == 0) {
        $(this).parent().parent('div').remove()// Remove field html
      } else {
        $(this).parent().addClass('nodisplay')
        $(this).parent().children().attr('isdeleted', 1)
      }
    })
    $(document).on('click', '#btn-role-save', function (e) {
      e.preventDefault()
      var roleCategory = $('#role_category').val()
      if (roleCategory == '' || roleCategory == null) {
        swal({
          title: '',
          text: 'Please select a category'
        })
      } else {
        if (__this._ValidateRolesBeforeSave()) {
          $('#role-form').submit()
        } else {
          swal({
            title: '',
            text: 'Please enter role name'
          })
        }
      }
    })
    $(document).on('click', '#add-new-Role', function (e) {
        // ROLES
      var fieldHTML = '<div><li><input type="text" class="form-control roles" roleid="0" isdeleted="0" name="field_name[roledescription][]" value="" id="rolestextbox" maxlength="100"><a href="javascript:void(0);" class="removeroles" title="Remove field"> <span class = "delete-icon"></span></a></li></div>' // New input field html
      if (__this._ValidateRolesBeforeSave()) {
        $('.field_wrapper').append(fieldHTML) // Add field html
      } else {
        swal({
          title: '',
          text: 'Please fill the previous text box'
        })
      }
    })
    $(document).on('click', '.removeroles', function (e) {
      e.preventDefault()
      var roleid = $(this).parent().parent('div').children().children().attr('roleid')
      if (roleid == 0) {
        $(this).parent().parent('div').remove()// Remove field html
      } else {
        $(this).parent().addClass('nodisplay')
        $(this).parent().children().attr('isdeleted', 1)
      }
    })
  },
  _onChangeEvents: function () {
    var __this = this
    $(document).on('change', '#role_group', function () {
      var roleId = $('#role_group').val()
      var institutionId = $('#institutionId').val()
      var ajaxUrl = $('#homeUrl').val() + $('#getSelectedRoleCategory-url').val()
      if (roleId != '' && institutionId != '') {
        $.post(ajaxUrl, // Ajax Post URL
          {
            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
            roleId: roleId,
            institutionId: institutionId
          }, // Data
            function (res) {
              if ((typeof (res) !== undefined && res.status === 'success') && res.data) {
                $('.field_wrapper').html('')
                $('.field_wrapper').append(res.data)
              }
            })
      }
    })

    $(document).on('change', '#role_category', function () {
      __this._selectedRoles()
    })
  },
  _onLoadEvents: function () {
    var __this = this
    $( document ).ready(function() {
         var $url = $('#getSelectedRoles-url').val()
         if($url != undefined){
            __this._selectedRoles()
         }
    });
    
  },
  _onKeyEvents: function () {
    var __this = this
  },

  _ValidateBeforeSave: function () {
    boolresult = true
    $('.category').each(function () {
      if (this.value == '') {
        boolresult = false
      }
    })

    return boolresult
  },
  _ValidateRolesBeforeSave: function () {
    boolresult = true
    $('.roles').each(function () {
      if (this.value == '') {
        boolresult = false
      }
    })

    return boolresult
  },
  _selectedRoles: function () {
    var roleCategoryId = $('#role_category').val()
    var institutionId = $('#institutionId').val()
    var ajaxUrl = $('#homeUrl').val() + $('#getSelectedRoles-url').val()
    if (roleCategoryId != '' && institutionId != '') {
      $.post(ajaxUrl, // Ajax Post URL
        {
          '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          roleCategoryId: roleCategoryId,
          institutionId: institutionId
        }, // Data
            function (res) {
              if ((typeof (res) !== undefined && res.status === 'success') && res.data) {
                $('.field_wrapper').html('')
                $('.field_wrapper').append(res.data)
              }
            })
    }
  },

    // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var RolesJS = new Remember.roles.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(RolesJS)
})
