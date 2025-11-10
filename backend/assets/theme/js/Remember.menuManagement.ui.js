jsFramework.lib.core.utils.registerNamespace('Remember.menuManagement.ui')
Remember.menuManagement.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
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
    var __this = this
  },

  _basicEvents: function () {
    var __this = this
    __this._onClickEvents()
    __this._onChangeEvents()
    __this._onLoadEvents()
    __this._onKeyEvents()
  },

  _onClickEvents: function () {
    var __this = this;

    //Activate/Deactivate products
    $(document).on('click', '.avail', function(){
      var ajaxUrl = $('#homeUrl').val() + $('#admin-available-unavailable-Url').val();
      var propertyId = $(this).attr('data-propertyId');
      var isActive = $(this).attr('data-isActive');

      if(isActive == '1'){
        var text = 'Do you need to make the item unavailable ?';
        isActive = '0';
        var message = 'This item successfully marked as unavailable';
      }
      else{
        var text = 'Do you need to make the item available ?';
        isActive = '1';
        var message = 'This item successfully marked as available';
      }

      swal({
        title: 'Are you sure?',
        text: text,
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
              propertyId: propertyId,
              isActive: isActive
            }, // Data
            function (res) {
              if (typeof (res) != 'undefined' && res.status == 'success') {
                swal({title: 'Success', text: message, type: 'success'},
                  function () {
                    location.reload()
                  }
                )
              } 
              else {
                swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'}),
                function () {
                  location.reload()
                }
              }
            }
          )
        }
      )
    });

    //Show popup
    $(document).on('click', '#add-category', function(){
      $('#foodcategory').val('');
      $('#propertyCategoryId').val(0);
      $('#AddCategoryPopup').modal('show');
    });

    //Save Category
    $(document).on('click', '#save-category', function(){
      var categoryName = $('#foodcategory').val().trim();
      if(categoryName == '' || categoryName == undefined){
        $('#ErrorDiv').removeClass('nodisplay');
      }
      else{
        $('#ErrorDiv').addClass('nodisplay');
        var ajaxUrl = $('#homeUrl').val() + $('#admin-save-category-Url').val();
        var propertyCategoryId = $('#propertyCategoryId').val();

        $.post(ajaxUrl, // Ajax Post URL
        {
          '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
          categoryName : categoryName,
          propertyCategoryId: propertyCategoryId,
        }, // Data
        function (res) {
          if (typeof (res) != 'undefined' && res.status == 'success') {
            $('#AddCategoryPopup').modal('hide'); 
            __this._getCategories();
          } 
          else {
            $('.error-msg').css('display','block');
          }
        });
      }
    });

    //Edit food category
    $(document).on('click','.edit-category', function(){
      var propertyCategoryId = $(this).attr('data-propertyCategoryId');
      var category = $(this).attr('data-category');
      $('#propertyCategoryId').val(propertyCategoryId);
      $("#foodcategory").val(category);
      $('#AddCategoryPopup').modal('show');
    })


    //Activate/Deactivate food item
    $(document).on('click', '.catavail', function(){
      var ajaxUrl = $('#homeUrl').val() + $('#admin-available-unavailable-category-Url').val();
      var propertyCategoryId = $(this).attr('data-propertyCategoryId');
      var isActive = $(this).attr('data-isActive');

      if(isActive == '1'){
        var text = 'Do you need to make the item unavailable ?';
        isActive = '0';
        var message = 'This item has been unavailable';
      }
      else{
        var text = 'Do you need to make the item available ?';
        isActive = '1';
        var message = 'This item has been available';
      }

      swal({
        title: 'Are you sure?',
        text: text,
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
              propertyCategoryId: propertyCategoryId,
              isActive: isActive
            }, // Data
            function (res) {
              if (typeof (res) != 'undefined' && res.status == 'success') {
                swal({title: 'Success', text: message, type: 'success'},
                  function () {
                    __this._getCategories();
                  }
                )
              } 
              else {
                swal({title: 'Failed', text: 'Sorry! unable to complete the process', type: 'error'}),
                function () {
                  __this._getCategories();
                }
              }
            }
          )
        }
      )
    });

    //Get Active Products
    $(document).on('click','#foodproducts',function(){
      location.reload();
    })
  },
  _onChangeEvents: function () {
    var __this = this

    //Thumbnail image change function var id = $(this).attr('id');
    $(".thumbnailimage").on("change",  function () {
      var imageFile = this.files[0];
      var type = imageFile.type;

      //Image upload and preview
      if (imageFile.type != 'image/png' && imageFile.type != 'image/jpg' && imageFile.type != 'image/jpeg') {
          if($("#thumbnailpreview").attr("src") != $('#base-url').val()+'/theme/images/propertythumbnail.jpg') {
            var location =$('#base-url').val()+"/theme/images/propertythumbnail.jpg";
            $('#thumbnailpreview').attr("src",location);
          }
          else{
            $("#thumbnailpreview").replaceWith($("#thumbnailpreview").clone());
          }
      }
      else {
        var url = window.URL.createObjectURL(imageFile);
        $('#thumbnailpreview').attr("src",url);
      }
    });

    //Thumbnail image change function 
    $(".image").on("change",  function () {
      var id = $(this).attr('data-imageId');
      var imageFile = this.files[0];
      var type = imageFile.type;

      //Image upload and preview
      if (imageFile.type != 'image/png' && imageFile.type != 'image/jpg' && imageFile.type != 'image/jpeg') {
          if($('#'+id).attr("src") != $('#base-url').val()+'/theme/images/photoupload.jpg') {
            var location =$('#base-url').val()+"/theme/images/photoupload.jpg";
            $('#'+id).attr("src",location);
          }
          else{
            $('#'+id).replaceWith($("#thumbnailpreview").clone());
          }
      }
      else {
        var url = window.URL.createObjectURL(imageFile);
        $('#'+id).attr("src",url);
      }
    });
  },
  _onLoadEvents: function () {
    var __this = this
  },
  _onKeyEvents: function () {
    var __this = this
  },

  //Get all categories
  _getCategories: function () {
    var ajaxUrl = $('#homeUrl').val() + $('#admin-property-categories-Url').val();
    var institutionId = $('#institutionId').val();

    $.get(ajaxUrl, // Ajax Get URL
    {
      institutionId: institutionId,
      isActive: null
    }, // Data
    function (res) {
      if (typeof (res) != 'undefined' && res.status == 'success') {
        $("#categories").html(res.data);
      } 
      else {
        $("#categories").html('');
      }
    });
  },

  // public members
  buildPage: function () {
    this._InitializePageBuilder()
  }
})
var MenuManagementJS = new Remember.menuManagement.ui.PageBuilder({})
jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function () {
  jsFramework.lib.ui.pageBinder.addPageBuilder(MenuManagementJS)
})