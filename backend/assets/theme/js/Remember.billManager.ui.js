jsFramework.lib.core.utils.registerNamespace('Remember.billManager.ui')
Remember.billManager.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
          },

          _onClickEvents: function () {
            var __this = this

                // Anchor button click event to show loader
            $('a').bind('click', function () {
              var link = $(this).attr('href')
              if (link.indexOf('#') < 0 && link != '') {
                $('.overlay').show()
              }
            })

            $(document).on('click', '.editBtn', function () {
              __this._enableEdit(this)
            })

            $(document).on('click', '.saveBtn', function () {
              __this._uploadBill(this, 'create')
            })

            $(document).on('click', '.updateBtn', function () {
              __this._uploadBill(this, 'update')
            })

            $(document).on('click', '.paybtn', function () {
              __this._getPaymentData(this)
            })

            $(document).on('click', '.delBtn', function () {
              __this._delConfirm(this)
            })

            $(document).on('click', '.editOpenbalance', function () {
              __this._setOpenBalanceEditable(this)
            })

            $(document).on('click', '.saveOpeningBalance', function () {
              __this._uploadOpenbalance()
            })

            $(document).on('click', '.savePaymentDetails', function () {
              __this._paymentData()
            })

            $(document).on('click', '.cancelPaymentDetails', function () {
              __this._resetPaymentModel()
            })

            $(document).on('click', '.btnMail', function () {
              __this._mailReciept(this)
            })

                /* $(document).on('click', '#statement', function(){
                    __this._viewStatement(this);

                }); */
            $(document).on('click', '.pageNav', function (e) {
              e.preventDefault()
              $('.overlay').hide()
              var urlVal = $(this).attr('url')
              $('.goNav').val(urlVal)
              __this._showMessage()
            })
            $(document).on('click', '.goNav', function () {
              var urlValue = $(this).attr('value')
              window.location = urlValue
            })
          },
          _onChangeEvents: function () {
            var __this = this

            $(document).on('change', '#month', function () {
              __this._getBills()
            })

            $(document).on('change', '#year', function () {
              __this._setMonths()
              __this._getBills()
            })

            $(document).on('change', '.paymentType', function () {
              __this._setPaymentModel()
            })
          },

          _onLoadEvents: function () {
            var __this = this

            $(document).ready(function () {
              $('.chequeDate').datepicker({format: 'yyyy-mm-dd'})
            })

            $(document).ready(function () {
              $('#month').val(new Date().getMonth())
              var monthIndex = $('#month').get(0).selectedIndex

              for (var i = 12; i > monthIndex; i--) {
                $('#month option:eq(' + i + ')').remove()
              }

              __this._getBills()
            })
          },

          _setMonths: function () {
            var year = $('#year').val()
            var months = [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
            var limit = 12
            var currYear = 1900 + new Date().getYear()
            var currMonth = new Date().getMonth()
            if (currYear == year) {
                    // Removes all months > current month,
                    // here +1 is added to consider the default option 'Please select'
              $('#month option:gt(' + (currMonth + 1) + ')').remove()

                    // Selects current month
              $('#month').val(currMonth)
            } else {
              for (i = 2; i <= limit; i++) {
                if ($('#month option:eq(' + i + ')').text() != months[i - 1]) {
                  $('#month').append($('<option>', {value: i - 1, text: months[i - 1]}))
                }
              }
            }
          },

          _enableEdit: function (elem) {
            var row = $(elem).parents('tr')
            var nonEditables = $(row).find('.nonEditables')
            nonEditables.addClass('nodisplay')

            var editables = $(row).find('.editables')
            editables.removeClass('nodisplay')

            var noOfRows = row.find('td').length
            if (noOfRows == 6) {
              $(row).find('td:last').remove()
              $(row).find('td:last').attr('colspan', '2')
            }

            $(elem).addClass('nodisplay')

            var saveButton = $(row).find('.delBtn')
            saveButton.addClass('nodisplay')

            var updateButton = $(row).find('.updateBtn')
            updateButton.removeClass('nodisplay')
          },

          _getBills: function () {
            var __this = this

            var params = __this._getBillParams()

            if (params.month.trim() && params.year.trim()) {
              var URL = $('#homeUrl').val() + $('#monthlyBillUrl').val()
              var json = JSON.stringify(params)

              $.ajax({
                type: 'POST',
                        // async: false,
                url: URL,
                dataType: 'JSON',
                data: {
                  '_csrf-backend':$("meta[name='csrf-token']").attr('content'),
                  json:json
                },
                success: function (data) {
                  if (data.status == 'success') {
                    __this._renderTable(data.bills)
                  } else {
                    __this._renderTable(null)
                  }
                }
              })
            }
          },

          _getPaymentData: function (elem) {
            var __this = this
            var params = {}
            var row = $(elem).parents('tr')
            params.billId = $(row).data('bid')
            params.credit = $(row).find('input[name="credit"]').val()
            params.payTypeBillId = $('#payTypeBillId').val()

            if (params.billId && params.credit) {
              if (params.payTypeBillId != params.billId) {
                var URL = $('#homeUrl').val() + $('#paymentDetailsUrl').val()
                var json = JSON.stringify(params)

                $.ajax({
                  type: 'POST',
                  url: URL,
                  dataType: 'JSON',
                  data: {
                  '_csrf-backend':$("meta[name='csrf-token']").attr('content'),
                  json:json
                  },
                  success: function (data) {
                    if (data.status == 'success') {
                      $('#payTypeBillId').val(params.billId)
                      __this._renderPaymentDetails(data)
                    } else {
                      __this._renderPaymentDetails(null)
                    }
                  }
                })
              } else {
                if ($('#savedpaymentType').val() == 'cheque') {
                  $('#paymentTypeCheque').prop('checked', true)
                  $('#chequeDetails').removeClass('nodisplay')
                  $('#neftDetails').addClass('nodisplay')
                  $('#ChequeNo').val($('#savedChequeNo').val())
                  $('#ChequeDate').val($('#savedChequeDate').val())
                  $('#Bank').val($('#savedBank').val())
                  $('#Branch').val($('#savedBranch').val())
                } else if ($('#savedpaymentType').val() == 'neft') {
                  $('#paymentTypeNeft').prop('checked', true)
                  $('#neftDetails').removeClass('nodisplay')
                  $('#chequeDetails').addClass('nodisplay')
                  $('#NeftNo').val($('#savedNeftNo').val())
                  $('#NeftDate').val($('#savedNeftDate').val())
                  $('#NeftBank').val($('#savedNeftBank').val())
                  $('#NeftBranch').val($('#savedNeftBranch').val())
                } else if ($('#savedpaymentType').val() == 'card') {
                  $('#paymentTypeCard').prop('checked', true)
                  $('#chequeDetails').addClass('nodisplay')
                  $('#neftDetails').addClass('nodisplay')
                  $('#CardNo').val($('#savedCardNo').val())
                  $('#CardBank').val($('#savedCardBank').val())
                } else if ($('#savedpaymentType').val() == 'cash') {
                  $('#paymentTypeCash').prop('checked', true) 
                  $('#cardDetails').addClass('nodisplay')
                  $('#chequeDetails').addClass('nodisplay')
                  $('#neftDetails').addClass('nodisplay')
                } else if ($('#savedpaymentType').val() == 'upi') {
                  $('#paymentTypeUpi').prop('checked', true) 
                  $('#cardDetails').addClass('nodisplay')
                  $('#chequeDetails').addClass('nodisplay')
                  $('#neftDetails').addClass('nodisplay')
                  $('#UpiId').val($('#savedUpiId').val())
                  $('#Upidate').val($('#savedUpiDate').val())
                  $('#TxnId').val($('#savedTxnId').val())
                }
              }
            } else if (params.billId && !params.credit) {
              $('#payTypeBillId').val(params.billId)
              __this._resetPaymentModel()
                    // Pre-fills payment type modal cheque details
              __this._getLastChequeData()
                    // Pre-fills payment type modal neft details
              __this._getLastNeftData()
              __this.getLastCardData()
              __this._getLastUpiData()
            } else if (!params.billId && params.payTypeBillId != '') {
              $('#payTypeBillId').val('')
              __this._resetPaymentModel()
                    // Pre-fills payment type modal cheque details
              __this._getLastChequeData()
                    // Pre-fills payment type modal neft details
              __this._getLastNeftData()
              __this.getLastCardData()
              __this._getLastUpiData()
            } else {
              if ($('#savedpaymentType').val()) {
                if ($('#savedpaymentType').val() == 'cheque') {
                  $('#paymentTypeCheque').prop('checked', true)
                  $('#chequeDetails').removeClass('nodisplay')
                  $('#ChequeNo').val($('#savedChequeNo').val())
                  $('#ChequeDate').val($('#savedChequeDate').val())
                  $('#Bank').val($('#savedBank').val())
                  $('#Branch').val($('#savedBranch').val())
                } else if ($('#savedpaymentType').val() == 'neft') {
                  $('#paymentTypeNeft   ').prop('checked', true)
                  $('#neftDetails').removeClass('nodisplay')
                  $('#NeftNo').val($('#savedNeftNo').val())
                  $('#NeftDate').val($('#savedNeftDate').val())
                  $('#NeftBank').val($('#savedNeftBank').val())
                  $('#NeftBranch').val($('#savedNeftBranch').val())
                } else if ($('#savedpaymentType').val() == 'card') {
                  $('#paymentTypeCard').prop('checked', true)
                  $('#cardDetails').removeClass('nodisplay')
                  $('#CardNo').val($('#savedCardNo').val())
                  $('#CardBank').val($('#savedCardBank').val())
                } else if ($('#savedpaymentType').val() == 'upi') {
                  $('#paymentTypeUpi').prop('checked', true)
                  $('#UpiDetails').removeClass('nodisplay')
                  $('#UpiId').val($('#savedUpiId').val())
                  $('#Upidate').val($('#savedUpiDate').val())
                  $('#TxnId').val($('#savedTxnId').val())
                } else {
                  $('#paymentTypeCash').prop('checked', true)
                  $('#chequeDetails').addClass('nodisplay')
                  $('#neftDetails').addClass('nodisplay')
                  $('#cardDetails').addClass('nodisplay')
                  __this._clearChequeFields(false)
                  __this._clearNeftFields(false)
                  __this._clearUpiFields(true)
                }
              } else {
                $('#payTypeBillId').val('')
                __this._resetPaymentModel()
                        // Pre-fills payment type modal cheque details
                __this._getLastChequeData()
                        // Pre-fills payment type modal neft details
                __this._getLastNeftData()
                __this._getLastCardData()
                __this._getLastUpiData()
                
              }
            }
          },
            /**
             * Gets the last used cheque details and pre-fill it in
             * the payment type modal.
             */
          _getLastChequeData: function () {
            var url = $('#homeUrl').val() + $('#defaultChequeUrl').val()
            $.get(url, {
              memberid: window.location.href.split('?')[1].split('id=')[1].split('&')[0]
            }, function (response) {
              if (response !== false) {
                if (response.paymentType == 'cheque') {
                  $('#Bank').val(response.Bank)
                  $('#Branch').val(response.Branch)
                }
              }
            }
                )
          },

            /**
             * Gets the last used upi details and pre-fill it in
             * the payment type modal.
             */
            _getLastUpiData: function () {
              var url = $('#homeUrl').val() + $('#defaultUpiUrl').val()
              $.get(url, {
                memberid: window.location.href.split('?')[1].split('id=')[1].split('&')[0]
              }, function (response) {
                if (response !== false) {
                  if (response.paymentType == 'upi') {
                    $('#UpiId').val(response.UpiId)
                  }
                }
              }
                  )
            },

            /**
             * Gets the last used Neft details and pre-fill it in
             * the payment type modal.
             */
          _getLastNeftData: function () {
            var url = $('#homeUrl').val() + $('#defaultNeftUrl').val()
            $.get(url, {
              memberid: window.location.href.split('?')[1].split('id=')[1].split('&')[0]
            }, function (response) {
              if (response !== false) {
                if (response.paymentType == 'neft') {
                  $('#NeftBank').val(response.Bank)
                  $('#NeftBranch').val(response.Branch)
                }
              }
            }
                )
          },
            /**
             * Gets the last used Card details and pre-fill it in
             * the payment type modal.
             */
          _getLastCardData: function () {
            var url = $('#homeUrl').val() + $('#defaultNeftUrl').val()
            $.get(url, {
              memberid: window.location.href.split('?')[1].split('id=')[1].split('&')[0]
            }, function (response) {
              if (response !== false) {
                if (response.paymentType == 'card') {
                  $('#CardBank').val(response.Bank)
                }
              }
            }
                )
          },

          _renderPaymentDetails: function (paymentData) {
            var __this = this

            if (paymentData) {
              $('#savedpaymentType').val(paymentData.paymentType)
              if (paymentData.paymentType == 'cheque') {
                $('#paymentTypeCheque').prop('checked', true)
                $('#chequeDetails').removeClass('nodisplay')
                $('#neftDetails').addClass('nodisplay')
                $('#cardDetails').addClass('nodisplay')

                        // clear neft fields if neft choosen
                __this._clearNeftFields(true)
                __this._clearCardFields(true)
                __this._clearUpiFields(true)

                $('#ChequeNo').val(paymentData.chequeNo)
                $('#ChequeDate').val(paymentData.chequeDate)
                $('#Bank').val(paymentData.bank)
                $('#Branch').val(paymentData.branch)

                $('#savedChequeNo').val(paymentData.chequeNo)
                $('#savedChequeDate').val(paymentData.chequeDate)
                $('#savedBank').val(paymentData.bank)
                $('#savedBranch').val(paymentData.branch)
              } else if (paymentData.paymentType == 'neft') {
                $('#paymentTypeNeft').prop('checked', true)
                $('#neftDetails').removeClass('nodisplay')
                $('#chequeDetails').addClass('nodisplay')
                $('#cardDetails').addClass('nodisplay')

                        // clear neft fields if cheque choosen
                __this._clearChequeFields(true)
                __this._clearCardFields(true)
                __this._clearUpiFields(true)

                $('#NeftNo').val(paymentData.neftNo)
                $('#NeftDate').val(paymentData.neftDate)
                $('#NeftBank').val(paymentData.neftBank)
                $('#NeftBranch').val(paymentData.neftBranch)

                $('#savedNeftNo').val(paymentData.neftNo)
                $('#savedNeftDate').val(paymentData.neftDate)
                $('#savedNeftBank').val(paymentData.neftBank)
                $('#savedNeftBranch').val(paymentData.neftBranch)
              } else if (paymentData.paymentType == 'card') {
                $('#paymentTypeCard').prop('checked', true)
                $('#chequeDetails').addClass('nodisplay')
                $('#neftDetails').addClass('nodisplay')
                $('#cardDetails').removeClass('nodisplay')

                __this._clearChequeFields(true)
                __this._clearNeftFields(true)
                __this._clearCardFields(true)
                __this._clearUpiFields(true)

                $('#CardNo').val(paymentData.CardNo)
                $('#CardBank').val(paymentData.CardBank)

                $('#savedCardNo').val(paymentData.CardNo)
                $('#savedCardBank').val(paymentData.CardBank)
              } else if (paymentData.paymentType == 'upi') {
                $('#paymentTypeUpi').prop('checked', true)
                $('#chequeDetails').addClass('nodisplay')
                $('#neftDetails').addClass('nodisplay')
                $('#cardDetails').addClass('nodisplay')
                $('#upiDetails').removeClass('nodisplay')

                __this._clearChequeFields(true)
                __this._clearNeftFields(true)
                __this._clearCardFields(true)

                $('#UpiId').val(paymentData.UpiId)
                $('#UpiDate').val(paymentData.UpiDate)
                $('#TxnId').val(paymentData.TxnId)

                $('#savedUpiId').val(paymentData.UpiId)
                $('#savedUpiDate').val(paymentData.UpiDate)
                $('#savedTxnId').val(paymentData.TxnId)

              } else {
                $('#paymentTypeCash').prop('checked', true)
                $('#chequeDetails').addClass('nodisplay')
                $('#neftDetails').addClass('nodisplay')
                $('#cardDetails').addClass('nodisplay')
                __this._clearChequeFields(true)
                __this._clearNeftFields(true)
                __this._clearCardFields(true)
                __this._clearUpiFields(true)
              }
            } else {
              $('.paymentType').prop('checked', false)
              $('#chequeDetails').addClass('nodisplay')
              $('#neftDetails').addClass('nodisplay')
              $('#cardDetails').addClass('nodisplay')
              $('#upiDetails').addClass('nodisplay')
              __this._clearChequeFields(true)
              __this._clearNeftFields(true)
              __this._clearCardFields(true)
              __this._clearUpiFields(true)
            }
          },

          _clearChequeFields: function (hidden) {
            $('#ChequeNo').val('')
            $('#ChequeDate').val('')
            $('#Bank').val('')
            $('#Branch').val('')
            if (hidden) {
              $('#savedChequeNo').val('')
              $('#savedChequeDate').val('')
              $('#savedBank').val('')
              $('#savedBranch').val('')
            }
          },

          _clearUpiFields: function (hidden) {
            $('#UpiId').val('')
            $('#UpiDate').val('')
            $('#TxnId').val('')
            if (hidden) {
              $('#savedUpiId').val('')
              $('#savedUpiDate').val('')
              $('#savedTxnId').val('')
            }
          },

          _clearNeftFields: function (hidden) {
            $('#NeftNo').val('')
            $('#NeftBank').val('')
            $('#NeftBranch').val('');
            $('#NeftDate').val('')

            if (hidden) {
              $('#savedNeftNo').val('')
              $('#savedNeftBank').val('')
              $('#savedNeftBranch').val('')
              $('#savedNeftDate').val('')
            }
          },
          _clearCardFields: function (hidden) {
            $('#CardNo').val('')
            $('#CardBank').val('')

            if (hidden) {
              $('#savedCardNo').val('')
              $('#savedCardBank').val('')
            }
          },

          _setPaymentModel: function () {
            $radioVaue = $('input[type=radio][name=paymentType]:checked').val()
            if ($radioVaue == 'cash') {
              $('#chequeDetails').addClass('nodisplay')
              $('#neftDetails').addClass('nodisplay')
              $('#cardDetails').addClass('nodisplay')
              $('#upiDetails').addClass('nodisplay')
            } else if ($radioVaue == 'card') {
              $('#cardDetails').removeClass('nodisplay')
              $('#neftDetails').addClass('nodisplay')
              $('#chequeDetails').addClass('nodisplay')
              $('#upiDetails').addClass('nodisplay')
            } else if ($radioVaue == 'neft') {
              $('#neftDetails').removeClass('nodisplay')
              $('#chequeDetails').addClass('nodisplay')
              $('#cardDetails').addClass('nodisplay')
              $('#upiDetails').addClass('nodisplay')
            } else if ($radioVaue == 'cheque') {
              $('#chequeDetails').removeClass('nodisplay')
              $('#neftDetails').addClass('nodisplay')
              $('#cardDetails').addClass('nodisplay')
              $('#upiDetails').addClass('nodisplay')
            } else if ($radioVaue == 'upi') {
              $('#upiDetails').removeClass('nodisplay')
              $('#chequeDetails').addClass('nodisplay')
              $('#neftDetails').addClass('nodisplay')
              $('#cardDetails').addClass('nodisplay')
            }
          },

          _resetPaymentModel: function () {
            var __this = this
            $('.paymentType').prop('checked', false)
            $('#savedpaymentType').val('')
            $('#chequeDetails').addClass('nodisplay')
            $('#neftDetails').addClass('nodisplay')
            $('#cardDetails').addClass('nodisplay')
            $('#upiDetails').addClass('nodisplay')
            __this._clearChequeFields(true)
            __this._clearNeftFields(true)
            __this._clearCardFields(true)
          },

          _uploadBill: function (elem, action) {
            var __this = this
            var params = __this._getBillParams()
            var billData = __this._getBillData(elem)
            var paymentData = __this._getBillPaymentData()
            var data = $.extend({}, params, billData, paymentData, {'actionType': action})

            var validate = __this._validate(data)
            if (!validate.isValid) {
              swal({title: 'Failed', text: validate.message, type: 'error'})
              return false
            }

            var URL = $('#homeUrl').val() + $('#billUploadUrl').val()
            var json = JSON.stringify(data)
            $.ajax({
              type: 'POST',
                    // async: false,
              url: URL,
              dataType: 'JSON',
              data: {
                '_csrf-backend':$("meta[name='csrf-token']").attr('content'),
                json:json
              },
              success: function (data) {
                if (data.status == 'success') {
                  if (data.upload) {
                    var message = 'Successfully saved bill entry'
                    swal({title: 'Success', text: message, type: 'success'})
                  } else {
                    var message = 'Unable to save bill entry. Please try again later'
                    swal({title: 'Failed', text: message, type: 'error'})
                  }
                  __this._renderTable(data.bills)
                } else {
                  __this._renderTable(null)
                }
              }
            })
          },

          _validate: function (data) {
            var __this = this;

            var validate = {}
            validate.isValid = true
            validate.message = ''
            if (!data.transactionDate || !data.description) {
              validate.isValid = false
              validate.message = 'Transaction Date and Description are mandatory fields'
              return validate
            }

            if (!data.debit && !data.credit) {
              validate.isValid = false
              validate.message = 'Please enter debit or credit amount'
              return validate
            }

            if (data.debit && data.credit) {
              validate.isValid = false
              validate.message = 'Enter either credit or debit amount. Both values not allowed'
              return validate
            }

            if (data.debit && !$.isNumeric(data.debit)) {
              validate.isValid = false
              validate.message = 'Debit amount should be numeric value'
              return validate
            }
            if (data.credit && !$.isNumeric(data.credit)) {
              validate.isValid = false
              validate.message = 'Credit amount should be numeric value'
              return validate
            }
            if ((data.credit && data.credit <= 0) || (data.debit && data.debit <= 0)) {
              validate.isValid = false
              validate.message = 'Amount should be greater than zero'
              return validate
            }
            if (data.credit && !data.paymentType) {
              validate.isValid = false
              validate.message = 'Select payment type for credit amount'
              return validate
            }
            if (data.credit) {
              validate = __this._validatePaymentData(data)
            }
            return validate
          },

          _paymentData: function () {
            var __this = this
            var paymentData = __this._getBillPaymentData()
            var validate = __this._validatePaymentData(paymentData)
            if (!validate.isValid) {
              swal({title: 'Failed', text: validate.message, type: 'error'})
              return false
            } else {
              $('#savedpaymentType').val(paymentData.paymentType)
              if (paymentData.paymentType == 'cheque') {
                $('#savedChequeNo').val(paymentData.ChequeNo)
                $('#savedChequeDate').val(paymentData.ChequeDate)
                $('#savedBank').val(paymentData.Bank)
                $('#savedBranch').val(paymentData.Branch)
              } else {
                $('#savedChequeNo').val('')
                $('#savedChequeDate').val('')
                $('#savedBank').val('')
                $('#savedBranch').val('')
              }

              if (paymentData.paymentType == 'neft') {
                $('#savedNeftNo').val(paymentData.NeftNo)
                $('#savedNeftBank').val(paymentData.NeftBank)
                $('#savedNeftBranch').val(paymentData.NeftBranch)
                $('#savedNeftDate').val(paymentData.NeftDate)
              } else {
                $('#savedNeftNo').val('')
                $('#savedNeftBank').val('')
                $('#savedNeftBranch').val('')
                $('#savedNeftDate').val('')
              }
              if (paymentData.paymentType == 'card') {
                $('#savedCardNo').val(paymentData.CardNo)
                $('#savedCardBank').val(paymentData.CardBank)
              } else {
                $('#savedCardNo').val('')
                $('#savedCardBank').val('')
              }
              if (paymentData.paymentType == 'upi') {
                $('#savedUpiId').val(paymentData.UpiId)
                $('#savedUpiDate').val(paymentData.UpiDate)
                $('#savedTxnId').val(paymentData.TxnId)
              } else {
                $('#savedUpiId').val('')
                $('#savedUpiDate').val('')
                $('#savedTxnId').val('')
              }
              $('#paymentmethod').modal('hide')
            }
          },

          _validatePaymentData: function (data) {
            var validate = {}
            validate.isValid = true
            validate.message = ''
            if (!data.paymentType) {
              validate.isValid = false
              validate.message = 'Select payment type for credit amount'
              return validate
            }

            if (data.paymentType == 'cheque' && (!data.ChequeNo || !data.Bank || !data.Branch || !data.ChequeDate)) {
              validate.isValid = false
              validate.message = 'All fields are mandatory for cheque details'
              if (!$('#paymentmethod').hasClass('in')) {
                $('#paymentmethod').modal('show')
              }
              return validate
            } else if (data.paymentType == 'card') {
              if (!data.CardNo) {
                validate.isValid = false
                validate.message = 'Card No cannot be blank'
                if (!$('#paymentmethod').hasClass('in')) {
                  $('#paymentmethod').modal('show')
                }
                return validate
              } else {
                var reg = /[^0-9]/
                if (reg.test(data.CardNo)) {
                  validate.isValid = false
                  validate.message = 'Invalid Card number'
                  if (!$('#paymentmethod').hasClass('in')) {
                    $('#paymentmethod').modal('show')
                  }
                  return validate
                }
              }
            } else if (data.paymentType == 'neft') {
              if (!data.NeftNo) {
                validate.isValid = false
                validate.message = 'NEFT No cannot be blank for Neft Details'
                if (!$('#paymentmethod').hasClass('in')) {
                  $('#paymentmethod').modal('show')
                }
                return validate
              } else {
                var reg = /[^A-Za-z0-9]/
                if (reg.test(data.NeftNo)) {
                  validate.isValid = false
                  validate.message = 'Invalid NEFT number'
                  if (!$('#paymentmethod').hasClass('in')) {
                    $('#paymentmethod').modal('show')
                  }
                  return validate
                }
              }
            } else if (data.paymentType == 'upi') {
              console.log(data.UpiId);
              console.log(data.TxnId);
              if (!data.UpiId) {
                validate.isValid = false
                validate.message = 'UPI Reference Id cannot be blank for UPI Details'
                if (!$('#paymentmethod').hasClass('in')) {
                  $('#paymentmethod').modal('show')
                }
                return validate
              } else if (!data.TxnId) {
                validate.isValid = false
                validate.message = 'UPI Transaction Id cannot be blank for UPI Details'
                if (!$('#paymentmethod').hasClass('in')) {
                  $('#paymentmethod').modal('show')
                }
                return validate
              } 
            }

            return validate
          },

          _delConfirm: function (elem) {
            var __this = this
            bootbox.confirm({
              message: 'Are you sure to delete this bill entry?',
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
                  __this._delBill(elem)
                }
              }
            })
          },

          _delBill: function (elem) {
            var __this = this

            var params = __this._getBillParams()

            var billData = __this._getBillData(elem)

            var data = $.extend({}, params, billData)

            if (data.billId) {
              var URL = $('#homeUrl').val() + $('#billDeleteUrl').val()
              var json = JSON.stringify(data)
              $.ajax({
                type: 'POST',
                        // async: false,
                url: URL,
                dataType: 'JSON',
                data: {
                  '_csrf-backend':$("meta[name='csrf-token']").attr('content'),
                  json:json
                },
                success: function (data) {
                  if (data.status == 'success') {
                    if (data.deleted) {
                      var message = 'Successfully deleted bill entry'
                      swal({
                            title: 'Success',
                            text: message,
                            type: 'success'
                      },
                      function() {
                        __this._renderTable(data.bills)
                      })
                    } else {
                      var message = 'Unable to delete bill entry. Please try again later'
                      swal({
                            title: 'Failed',
                            text: message,
                            type: 'error'
                      },
                      function() {
                        __this._renderTable(data.bills)
                      })
                    }
                    
                  } else {
                    __this._renderTable(null)
                  }
                }
              })
            }
          },

          _renderTable: function (tableData) {
            if (tableData) {
              $('#billsTableDiv').html(tableData)
              $('.transactionDate').datepicker({
                  format: 'yyyy-mm-dd',
                  endDate: new Date()
              })
              $('.chequeDate').datepicker({format: 'yyyy-mm-dd'})
            } else {
              $('#billsTableDiv').html('<div class="inlinerow alert alert-danger">Error occured! please try later</div>')
            }
          },

          _getBillParams: function () {
            var params = {}

            params.memberid = $('#memberid').val()
            params.month = $('#month').val()
            params.year = $('#year').val()

            return params
          },

          _getBillData: function (elem) {
            var billData = {}

            var row = $(elem).parents('tr')

            billData.billId = $(row).data('bid')
            billData.transactionDate = $(row).find('input[name="transactionDate"]').val()
            billData.description = $(row).find('input[name="description"]').val()
            billData.debit = $(row).find('input[name="debit"]').val()
            billData.credit = $(row).find('input[name="credit"]').val()

            return billData
          },

          _getBillPaymentData: function () {
            var BillPaymentData = {}

            BillPaymentData.paymentType = $('input[name=paymentType]:checked').val()
            if (BillPaymentData.paymentType == 'cheque') {
              BillPaymentData.ChequeNo = $('#ChequeNo').val()
              BillPaymentData.ChequeDate = $('#ChequeDate').val()
              BillPaymentData.Bank = $('#Bank').val()
              BillPaymentData.Branch = $('#Branch').val()
            } else if (BillPaymentData.paymentType == 'neft') {
              BillPaymentData.NeftNo = $('#NeftNo').val()
              BillPaymentData.NeftBank = $('#NeftBank').val()
              BillPaymentData.NeftBranch = $('#NeftBranch').val()
              BillPaymentData.NeftDate = $('#NeftDate').val()
            } else if (BillPaymentData.paymentType == 'card') {
              BillPaymentData.CardNo = $('#CardNo').val()
              BillPaymentData.CardBank = $('#CardBank').val()
            } else if (BillPaymentData.paymentType == 'upi') {
              BillPaymentData.UpiId = $('#UpiId').val()
              BillPaymentData.UpiDate = $('#UpiDate').val()
              BillPaymentData.TxnId = $('#TxnId').val()
            } 
            return BillPaymentData
          },

          _setOpenBalanceEditable: function (elem) {
            $('#openingBalance').prop('readonly', false)
            $(elem).addClass('nodisplay')
            $('.saveOpeningBalance').removeClass('nodisplay')
          },

          _uploadOpenbalance: function () {
            var __this = this

            if (!$('#openingBalance').is('[readonly]')) {
              var params = __this._getBillParams()
              params.debit = $('#openingBalance').val()

              if (!$.isNumeric(params.debit)) {
                message: 'Opening balance should be a numerical value',
                swal({title: 'Failed', text: message, type: 'error'})
                return false
              }

              var URL = $('#homeUrl').val() + $('#openBalUploadUrl').val()
              var json = JSON.stringify(params)
              $.ajax({
                type: 'POST',
                        // async: false,
                url: URL,
                dataType: 'JSON',
                data: {
                  '_csrf-backend':$("meta[name='csrf-token']").attr('content'),
                  json:json
                },
                success: function (data) {
                  if (data.status == 'success') {
                    if (data.upload) {
                      var message = 'Successfully saved opening balance'
                      swal({title: 'Success', text: message, type: 'success'})
                    } else {
                      var message = 'Unable to save opening balance. Please try again later'
                      swal({title: 'Failed', text: message, type: 'error'})
                    }
                    __this._renderTable(data.bills)
                  } else {
                    __this._renderTable(null)
                  }
                }
              })
            }
          },

          _mailReciept: function (elem) {
            var __this = this
            var data = {}
            var row = $(elem).parents('tr')
            data.billId = $(row).data('bid')
            data.emailAddress = $('#memberEmail').html()
            if (!data.emailAddress) {
              bootbox.dialog({
                title: 'Enter email address to send receipt',
                message: '<div class="form-group">' +
                        '<input id="emailAddress" type="text" class="form-control" autocomplete="off">' +
                        '</div>',
                buttons: {
                  confirm: {
                    label: 'Send Mail',
                    className: 'btn-success',
                    callback: function () {
                      var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                      if ($('#emailAddress').val()) {
                        if (re.test($('#emailAddress').val())) {
                          data.emailAddress = $('#emailAddress').val()
                          $('.modal').modal('hide')
                          __this._sendMail(data)
                        } else {
                          message: 'Not a valid email address'  
                          swal({title: 'Failed', text: message, type: 'error'})
                          return false
                        }
                      } else {
                        message: 'Enter a valid email address' 
                        swal({title: 'Failed', text: message, type: 'error'})
                        return false
                      }
                    }
                  },
                  cancel: {
                    label: 'Cancel',
                    className: 'btn-danger',
                    callback: function () {
                      return true
                    }
                  }
                }
              })
            } else {
              __this._sendMail(data)
            }
          },

          _sendMail: function (data) {
            if (data.emailAddress) {
              var URL = $('#homeUrl').val() + $('#mailRecieptUrl').val()
              var json = JSON.stringify(data)
              $.ajax({
                type: 'POST',
                        // async: false,
                url: URL,
                dataType: 'JSON',
                data: {
                  '_csrf-backend':$("meta[name='csrf-token']").attr('content'),
                  json:json
                },
                success: function (data) {
                  if (data.status == 'success') {
                    if (!data.mailed) {
                      var message = 'Unable to mail reciept. Please try again later'
                      swal({title: 'Failed', text: message, type: 'error'})
                    } else {
                      var message = 'Successfully mailed receipt to member'
                     swal({title: 'Success', text: message, type: 'success'})
                    }
                  } else {
                    var message = 'Unable to mail reciept. Please try again later'
                    swal({title: 'Failed', text: message, type: 'error'})
                  }
                }
              })
            }
          },
            /* _viewStatement : function(elem){
                var memberid = $(elem).attr('data-id');
                var URL = $("#homeUrl").val() + $('#statementUrl').val();
                $.ajax({
                    url: URL,
                    type: 'post',
                    dataType: 'HTML',
                    data:
                    {
                        memberid:memberid

                    },
                    success:function(data){
                        $(".modal-body").html(data);
                        $('#transactions').modal('show');

                    }
                });
                }, */
          _showMessage: function () {
            $('#alertModal').modal('show')
          },
        // public members
          buildPage: function () {
            this._InitializePageBuilder()
          }

        })
var BillManagerJS = new Remember.billManager.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(BillManagerJS)
})
