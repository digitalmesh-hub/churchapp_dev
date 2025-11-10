jsFramework.lib.core.utils.registerNamespace("Remember.orders.ui");
Remember.orders.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
.extend({
	init : function(settings) {
		this._super(settings); // call base init
	},

	_InitializePageBuilder : function() {
		var __this = this;
		__this._configureEvents();
	},

	_configureEvents : function() {
		var __this = this;
		__this._basicEvents();
		__this._ajaxEvents();
	},

	_ajaxEvents : function() {

	},

	_basicEvents : function() {
		var __this = this;
		__this._onClickEvents();
		__this._onChangeEvents();
		__this._onLoadEvents();
		__this._onKeyEvents();
	},

	_onChangeEvents : function() {

	},
	_onLoadEvents : function() {
		var __this = this;
	
	},
	_onKeyEvents : function() {
	},
	_onClickEvents: function () {
		var __this = this
		
		$(document).on("click", "#btn-reject-reason", function () {
			 var ajaxUrl = $('#homeUrl').val() + $('#reject-order').val()
	    	 var orderId = $('#orderid').val()
	    	 var note = $('.rejectreason').val().trim();
	    	 if(note != ''){
	    	 	 $('.reject').modal('hide');
	    	 	 $('.rejectreason').val('');
		    	 swal({
			           title: 'Are you sure?',
			           text: 'Are You sure that you need to reject the order ?',
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
			                    orderId:orderId,
			                    note:note
			                  }, // Data
			                  	function (res) {
			                          if (typeof (res) !== 'undefined' && res.status === 'success') {
			                            swal({title: 'Success', text: 'The order has been rejected', type: 'success'},
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
			                        }
			                  
			                  )
			    });
		    }
		    else{
		    	swal({title: 'Warning', text: 'Please describe a reason to reject this order', type: 'warning'}),
                function () {
                  location.reload()
                }
		    }
		})
		
		$(document).on("click", "#btn-confirm", function () {
			var ajaxUrl = $('#homeUrl').val() + $('#update-status').val()
		
	    	 var orderId = $('#orderid').val()
	    	 var orderStatus = $(this).attr('data-status');
	    	 swal({
		           title: 'Are you sure?',
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
		                    orderId:orderId,
		                    orderStatus:orderStatus
		                  }, // Data
		                  	function (res) {
		                          if (typeof (res) !== 'undefined' && res.status === 'success') {
		                            swal({title: 'Success', text: 'The order status has been updated', type: 'success'},
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
		                        }
		                  
		                  )
		    });
		});

		//Cancel
		$(document).on("click", ".btn-cancel", function () {
			$('.rejectreason').val('');
		});
	},
	

	// public members
	buildPage : function() {
		this._InitializePageBuilder();
	}

});
var OrderJS = new Remember.orders.ui.PageBuilder({});
jQuery(document).bind("SETUP_PAGE_BUILDERS_EVENT", function() {
	jsFramework.lib.ui.pageBinder.addPageBuilder(OrderJS);
});
