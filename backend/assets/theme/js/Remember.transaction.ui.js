jsFramework.lib.core.utils.registerNamespace("Remember.transaction.ui");
Remember.transaction.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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

	_onClickEvents : function() {
		var __this=this;
		$(document).on('click', '#search', function(){
			__this._searchResult(this);
		});
		$(document).on('click','.pagerLink',function(){
			var pageVal= $(this).attr('value');
			$('#pagerValue').val(pageVal);
			__this._searchResult(this);   
		});
		$(document).on('click','.firstLink',function(){
			__this._searchResult(this);   
		});
		$(document).on('click','.lastLink',function(){
			__this._searchResult(this);   
		});
		$(document).on('click', '#download', function(){
			__this._downloadResult(this);
		});
		
		
	},
	_onChangeEvents : function() {

	},
	_onLoadEvents : function() {
		var __this = this;
		
		$(document).ready(function(){
			$('#startdate').appendDtpicker
			({
				"dateOnly": true,
				"dateFormat": "YYYY-MM-DD",
				"autodateOnStart": false,
				"closeOnSelected": true,
			});
		
			$('#enddate').appendDtpicker
			({
				"dateOnly": true,
				"dateFormat": "YYYY-MM-DD",
				"autodateOnStart": false,
				"closeOnSelected": true,
			});
			
		});	
		
		__this._searchResult(this);
	},
	_onKeyEvents : function() {
		var __this=this;
		$(document ).on( "keydown", function(event) {
		    if(event.which == 13) {
		    	__this._searchResult(this);   
		    }
		}); 
		 
	},
	_searchResult : function(elem){
			var countVal=$('#count').attr('value');
			var page=$('#pagerValue').val();
			var txnid=$('#txnid').val();
			var memberno=$('#memberno').val();
			var name=$('#name').val();
			var status=$('#status').val();
			var startdate=$('#startdate').val();
			var enddate=$('#enddate').val();
			if(startdate > enddate)
			{
				var message = 'Invalid date range'; 
				$('#alertModal').find('.modal-body p').text(message);
			    $('#alertModal').modal('show')
			}
			else
			{
			var URL=$("#homeUrl").val()+$('#searchUrl').val();
			$.ajax({
				url: URL,
				type: 'post',
				dataType: "html",
				data: 
				{
					'_csrf-backend' : $('meta[name="csrf-token" ]').attr('content'),
					txnid:txnid,
					memberno:memberno,
					status:status,
					name:name,
					startdate:startdate,
					enddate:enddate,
					page:page
				},
			
				success:function(data){
					$('#listview').html(data);
					if(page==1)
						{
						$('#prev').addClass('disabled');
						$('#first').addClass('disabled');
						}
					if(page>=countVal)
						{
						$('#next').addClass('disabled');
						$('#last').addClass('disabled');
						}
					$($("a[value='"+page+"'][name='pagerIndex']")).parent().addClass('active');			
				},
			});
			}
	},


	_downloadResult : function(elem){
		var countVal=$('#count').attr('value');
		var txnid=$('#txnid').val();
		var memberno=$('#memberno').val();
		var name=$('#name').val();
		var status=$('#status').val();
		var startdate=$('#startdate').val();
		var enddate=$('#enddate').val();
		if(this._dateFilter(startdate, enddate))
		{
			var URL=$("#homeUrl").val()+$('#downloadUrl').val();
			var request = {
				txnid:txnid,
				memberno:memberno,
				status:status,
				name:name,
				startdate:startdate,
				enddate:enddate,
			};
			URL=`${URL}?${$.param(request)}`;
			window.location.href = URL
		}
	},
	_dateFilter : function(startDate, endDate, monthLimit = 1) {
		var strtDateSplit = startDate.split("-");
    	var strtDate      = new Date(strtDateSplit[0],strtDateSplit[1]-1,strtDateSplit[2]);
    	var fromDate	  = new Date(strtDateSplit[0],strtDateSplit[1]-1,strtDateSplit[2]);
    	var dStart	      = fromDate.getDate() + '/' + (fromDate.getMonth() + 1) + '/' +fromDate.getFullYear();

    	var lastDate      = new Date(fromDate .setMonth(fromDate .getMonth() + monthLimit));	
		var lDate 		  = lastDate.getDate() + '/' + (lastDate.getMonth() + 1) + '/' + lastDate.getFullYear() ; 		

		var endDateSplit  = endDate.split("-");
		var end_date      = new Date(endDateSplit[0],endDateSplit[1]-1,endDateSplit[2]);
    	var dEnd	      = end_date.getDate() + '/' + (end_date.getMonth() + 1) + '/' +end_date.getFullYear();
		
		if( end_date > lastDate){
			var message = `Date Limit exceed. Please choose date range of ${monthLimit} months.`; 
			$('#alertModal').find('.modal-body p').text(message);
			$('#alertModal').modal('show')
			return false;
		}
		else if ((strtDate > end_date)){
			var message = 'To Date must be greater than From date.'; 
			$('#alertModal').find('.modal-body p').text(message);
			$('#alertModal').modal('show')
			return false;
		}
		else
			return true;
	},

	// public members
	buildPage : function() {
		this._InitializePageBuilder();
	}

});
var TransactionJS = new Remember.transaction.ui.PageBuilder({});
jQuery(document).bind("SETUP_PAGE_BUILDERS_EVENT", function() {
	jsFramework.lib.ui.pageBinder.addPageBuilder(TransactionJS);
});
