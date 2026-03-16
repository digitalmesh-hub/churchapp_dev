jsFramework.lib.core.utils.registerNamespace('Remember.vicardirectory.ui')
Remember.vicardirectory.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    
    // Deactivate vicar assignment
    $(document).on('click', '.btn-deactivate-vicar', function () {
      var ajaxUrl = $('#homeUrl').val() + 'vicardirectory/deactivate-vicar'
      var id = $(this).data('id')
      
      swal({
        title: 'Are you sure?',
        text: 'Do you want to deactivate this vicar assignment?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        confirmButtonText: 'Yes, deactivate',
        closeOnConfirm: false
      },
      function () {
        $.post(ajaxUrl,
          {
            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
            id: id
          },
          function (res) {
            if (typeof (res) !== 'undefined' && res.status === 'success') {
              swal({
                title: 'Success',
                text: 'Vicar assignment has been deactivated',
                type: 'success'
              },
              function () {
                location.reload()
              })
            } else {
              swal({
                title: 'Failed',
                text: 'Sorry! Unable to deactivate vicar assignment',
                type: 'error'
              })
            }
          })
      })
    })

    // Activate vicar assignment
    $(document).on('click', '.btn-activate-vicar', function () {
      var ajaxUrl = $('#homeUrl').val() + 'vicardirectory/activate-vicar'
      var id = $(this).data('id')
      $.post(ajaxUrl,
        {
          '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          id: id
        },
        function (res) {
          if (typeof (res) !== 'undefined' && res.status === 'success') {
            swal({
              title: 'Success',
              text: 'Vicar assignment has been activated',
              type: 'success'
            },
            function () {
              location.reload()
            })
          } else {
            swal({
              title: 'Failed',
              text: 'Sorry! Unable to activate vicar assignment',
              type: 'error'
            })
          }
        })
    })

    // Activate position
    $(document).on('click', '.btn-activate-position', function () {
      var ajaxUrl = $('#homeUrl').val() + 'vicardirectory/activate-position'
      var id = $(this).data('id')
      
      $.post(ajaxUrl,
        {
          '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          id: id
        },
        function (res) {
          if (typeof (res) !== 'undefined' && res.status === 'success') {
            swal({
              title: 'Success',
              text: 'Position has been activated',
              type: 'success'
            },
            function () {
              location.reload()
            })
          } else {
            swal({
              title: 'Failed',
              text: 'Sorry! Unable to activate position',
              type: 'error'
            })
          }
        })
    })

    // Deactivate position
    $(document).on('click', '.btn-deactivate-position', function () {
      var ajaxUrl = $('#homeUrl').val() + 'vicardirectory/deactivate-position'
      var id = $(this).data('id')
      
      swal({
        title: 'Are you sure?',
        text: 'Do you want to deactivate this position?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        confirmButtonText: 'Yes, deactivate',
        closeOnConfirm: false
      },
      function () {
        $.post(ajaxUrl,
          {
            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
            id: id
          },
          function (res) {
            if (typeof (res) !== 'undefined' && res.status === 'success') {
              swal({
                title: 'Success',
                text: 'Position has been deactivated',
                type: 'success'
              },
              function () {
                location.reload()
              })
            } else {
              swal({
                title: 'Failed',
                text: 'Sorry! Unable to deactivate position',
                type: 'error'
              })
            }
          })
      })
    })
  },

  _onChangeEvents: function () {
    // Change events removed - using server-side filtering now
  },

  _onLoadEvents: function () {
    var __this = this
    
    // Initialize Select2 for searchable dropdowns
    if ($('#position-select').length) {
      $('#position-select').select2({
        placeholder: 'Search and select position...',
        allowClear: true,
        width: '100%'
      })
    }
    
    if ($('#member-select').length) {
      $('#member-select').select2({
        placeholder: 'Search and select member...',
        allowClear: true,
        width: '100%'
      })
    }
  },
  
  _onKeyEvents: function () {
    var __this = this
    
    // Search positions table (for positions management page)
    $(document).on('keyup', '#searchPositions', function() {
      var value = $(this).val().toLowerCase()
      $('#positionsTable tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      })
    })
  },

  // public members
  buildPage: function() {
    this._InitializePageBuilder()
  }
})

var vicardirectoryJS = new Remember.vicardirectory.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function() {
  jsFramework.lib.ui.pageBinder.addPageBuilder(vicardirectoryJS)
})
