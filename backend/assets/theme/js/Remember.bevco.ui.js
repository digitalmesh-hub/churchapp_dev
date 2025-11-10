jsFramework.lib.core.utils.registerNamespace('Remember.bevco.ui')
Remember.bevco.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    $(document).on('click', '#activate-bevco-cat', function() {
      var ajaxUrl = $(this).attr('data-url')
      swal({
          title: 'Are you sure?',
          text: 'Do you want to make this category available',
          type: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'btn-danger',
          confirmButtonText: 'Yes',
          closeOnConfirm: false,
          showLoaderOnConfirm: true
      },function() {
          $.post(ajaxUrl,{
            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          }, // Data
          function(res) {
            if (typeof(res) !== 'undefined' && res.success) {
              location.reload()
            } else {
                swal({
                  title: 'Failed',
                  text: 'Sorry! unable to complete your request',
                  type: 'error'
                },function() {
                    location.reload()
                })
            }
        })
      })
    });
    $(document).on('click', '#deactivate-bevco-cat', function() {
      var ajaxUrl = $(this).attr('data-url')
      swal({
          title: 'Are you sure?',
          text: 'Do you want to make this category unavailable',
          type: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'btn-danger',
          confirmButtonText: 'Yes',
          closeOnConfirm: false,
          showLoaderOnConfirm: true
      },function() {
          $.post(ajaxUrl,{
            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          }, // Data
          function(res) {
            if (typeof(res) !== 'undefined' && res.success) {
              location.reload()
            } else {
                swal({
                  title: 'Failed',
                  text: 'Sorry! unable to complete your request',
                  type: 'error'
                },function() {
                    location.reload()
                })
            }
        })
      })
    })
    $('#add-settings').on('beforeSubmit', function (e) {
      var $yiiform = $('#add-settings');
      rawData = $yiiform.serializeArray()
      $.ajax({
        type: 'POST',
        url: $yiiform.attr('action'),
        data: rawData,
        headers : {
          'X-CSRF-Token' : $("meta[name='csrf-token']").attr('content')
        }}).done(function(data) {
          if (data.validation) {                
              $yiiform.yiiActiveForm('updateMessages', data.validation, true); 
          } else if(data.success) {
              location.href = $('#homeUrl').val() + 'beverages';
          } else {
              swal ( "Oops" ,  "Something went wrong!" ,  "error" )
          }
        }).fail(function() {
            swal ( "Oops" ,  "Something went wrong!" ,  "error" )
        })
    }).on('submit', function (e) {
        return false;
    });

    $(document).on('click', '.selection-panel', function (e) {
        var data_row = $(this).attr('data-row');
        var $target = $(e.target);
        $('#beveragebookingform-slot').val(data_row).trigger('change.yiiActiveForm');
        $('.slot-panel .tile-choosen').removeClass("tile-choosen");
        $target.closest('.tile').addClass( "tile-choosen" );    
    });
 
    $('#bevco-booking-form').on('beforeSubmit', function (e) {
        rawData = $(this).serializeArray();
        $('#booking-error').empty();
        $('.bevco-booking-error-msg').hide();
        var slot = _.find(rawData, { 'name': 'BeverageBookingForm[slot]' });
        if (_.isEmpty(_.get(slot, 'value'))) {
             swal("Please choose a slot");
            return false;
        }
        $.ajax({
          type: 'POST',
          url: $(this).attr('action'),
          data: rawData,
          headers : {
            'X-CSRF-Token' : $("meta[name='csrf-token']").attr('content')
          }}).done(function(data) {
            if (data.validation) {
                var str = "";
                var validation = data.validation;
                for (var key in validation) {
                   errors = validation[key];
                  if (_.isString(errors))
                        errors = [errors];

                    errors.forEach(function(error) {
                         str += '<li>' + error + '</li>'
                    });
                    $('.bevco-booking-error-msg').show();
                    $('#booking-error').append('<h3>Error:</h3>').append('<ul>' + str + '</ul>');
                    $('html, body').animate({
                      scrollTop: $("#booking-error").offset().top
                    }, 2000);
                }
            } else if(data.success) {
              swal({
                title: 'Success',
                text: 'Booked successfully',
                type: 'success'
              },
              function() {
                  location.href = $('#homeUrl').val() + 'beverages/manage-booking';
              })
            } else {
              var errors = data.errors;
              var str = "";
              if (errors) {
                  if (_.isString(errors))
                      errors = [errors];

                  errors.forEach(function(error) {
                       str += '<li>' + error + '</li>'
                  });
                  $('.bevco-booking-error-msg').show();
                  $('#booking-error').append('<h3>Booking Failed</h3>').append('<ul>' + str + '</ul>');
                  $('html, body').animate({
                    scrollTop: $("#booking-error").offset().top
                  }, 2000);
              }
            }
        }).fail(function() {
            swal ( "Oops" ,  "Something went wrong!" ,  "error" )
        })

    }).on('submit', function (e) {
        return false;
    });

    $('#complete-bevco-order').on('beforeSubmit', function (e) {
        rawData = $(this).serializeArray();
        $.ajax({
          type: 'POST',
          url: $(this).attr('action'),
          data: rawData,
          headers : {
            'X-CSRF-Token' : $("meta[name='csrf-token']").attr('content')
          }}).done(function(data) {
              if (data.validation) {                
                  $(this).yiiActiveForm('updateMessages', data.validation, true); 
              } else if(data.success) {
                  $('#completeOrderModal').modal('hide');
                  swal({
                      title: 'Success',
                      text: 'Order processed successfully',
                      type: 'success'
                  },
                  function() {
                      location.href = $('#homeUrl').val() + 'beverages/manage-booking';
                  })
              } else {
                  $('#completeOrderModal').modal('hide');
                  swal ( "Oops" ,  "Something went wrong!" ,  "error" )
              }
        }).fail(function() {
            $('#completeOrderModal').modal('hide');
            swal ( "Oops" ,  "Something went wrong!" ,  "error" )
        })
    }).on('submit', function (e) {
        return false;
    });

    $(document).on('click', '.slot-unlock', function(e) {
       e.preventDefault();
        var url = $('#homeUrl').val() + 'beverages/slot-unlock';
        var slot = $(this).attr('data-slot-id');
        swal({
          title: 'Are you sure?',
          text: 'Do you want to unlock this slot, by unlocking this slot, it can be used for further bookings according to defined rules',
          type: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'btn-danger',
          confirmButtonText: 'Yes',
          closeOnConfirm: false,
          showLoaderOnConfirm: true
        },function() {
          $.post(url,{
            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
            'slot_id': slot
          },
          function(res) {
            swal.close();
            if (typeof(res) !== 'undefined' && res.success) {
              var order_date = $('#beveragebookingform-order_date').val();
              if(order_date != undefined && order_date != '') {
                  __this.generateSlots(order_date);
              }
            } else {
                swal({
                  title: 'Failed',
                  text: 'Sorry! unable to complete your request',
                  type: 'error'
                },function() {
                    swal.close();
                })
            }
          })
        })
    }); 

    $(document).on('click', '.slot-lock', function(e) {
        e.preventDefault();
        var url = $('#homeUrl').val() + 'beverages/slot-lock';
        var slot = $(this).attr('data-slot-id');
        swal({
          title: 'Are you sure?',
          text: "Do you want to lock this slot.Once you lock this slot it'll be inutile for further bookings until unlocked.",
          type: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'btn-danger',
          confirmButtonText: 'Yes',
          closeOnConfirm: false,
          showLoaderOnConfirm: true
        },function() {
          $.post(url,{
            '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
            'slot_id': slot
          },
          function(res) {
            swal.close();
            if (typeof(res) !== 'undefined' && res.success) {
              var order_date = $('#beveragebookingform-order_date').val();
              if(order_date != undefined && order_date != '') {
                  __this.generateSlots(order_date);
              }
            } else {
                swal({
                  title: 'Failed',
                  text: 'Sorry! unable to complete your request',
                  type: 'error'
                },function() {
                    swal.close();
                })
            }
          })
        })
    });
  },
  _onChangeEvents: function () {
    var __this = this
      $('#beveragebookingform-order_date').on('change',function(e) {
         __this.generateSlots($(this).val());
      });
  },
  generateSlots:function(order_date) {
    var url = $('#homeUrl').val() + 'beverages/get-slots';
    $.get(url, {order_date : order_date}, function (response) {
        $('.slot-panel').html(response);
        $('[data-toggle="popover"]').popover({
            html : true
        });   
    });
  },
  _onLoadEvents: function () {
    var __this = this
    $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeTab');
    if(activeTab){
        $('#myTab a[href="' + activeTab + '"]').tab('show');
    }

    $('#completeOrderModal').on('hidden.bs.modal', function () {
        $('#complete-bevco-order').yiiActiveForm('resetForm');
        $('#extendedbevcoorder-status').val('');
        $('#complete-bevco-order').data('yiiActiveForm').validated = false;
    });
    $( document ).ready(function() {
        var order_date = $('#beveragebookingform-order_date').val();
        if(order_date != undefined && order_date != '') {
            __this.generateSlots(order_date);
        }
    });
    
  },
  _onKeyEvents: function () {
    var __this = this
  },

  // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }

})
var BevcoJS = new Remember.bevco.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(BevcoJS)
})
