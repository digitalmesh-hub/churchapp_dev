jsFramework.lib.core.utils.registerNamespace('Remember.common.ui')
Remember.common.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
            $(document).ajaxStart(function () {
              $('.overlay').show()
            }).ajaxStop(function () {
              $('.overlay').hide()
            }).ajaxComplete(function () {
              $('.overlay').hide()
            })
          },

          _basicEvents: function () {
            var __this = this
            __this._onClickEvents()
            __this._onChangeEvents()
            __this._onLoadEvents()
          },

          _onClickEvents: function () {
            var __this = this

            $(document).on('click', '.back-btn', function (e) {
              __this._goBack()
            })
           
          },

          _onChangeEvents: function () {
            var __this = this
          },

          _onLoadEvents: function () {
            var __this = this
            __this._hideFlash()
          
            $(document).on('ready pjax:success', function () {
              row = $('#gridTable-filters')
              column = row.children().last()
              column.html('<a href="' + $('#homeUrl').val() + $('#controller').val() + '" class="btn btn-primary" title="Reset">Reset</a>')
            })
            $(document).ready(function () {
              $('.overlay').hide()
            })
            $('#form').on('beforeValidate', function (event, messages) {
              $('.overlay').show()
              $(this).find('.submit').attr('disabled', true)
            })

            $('#form').on('afterValidate', function (event, messages) {
              var form = $(this)
              if (form.find('.has-error').length) {
                $('.overlay').hide()
                $(this).find('.submit').attr('disabled', false)
              }
            })
          },

          _hideFlash: function () {
            setTimeout(function () {
              $('.flash').fadeOut('slow')
            }, 10000)
          },

          _showFlash: function () {
            $('.flash').show()
          },

          _goBack: function () {
            // window.history.back()
            window.history.back(-1)
          },

            /// ///////////////////////
            // public members
          buildPage: function () {
            this._InitializePageBuilder()
          }
         

        })

var CommonJS = new Remember.common.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(CommonJS)
})
