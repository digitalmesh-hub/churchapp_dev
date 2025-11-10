  jsFramework.lib.core.utils.registerNamespace('Remember.memberEdit.ui')
  Remember.memberEdit.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
      .extend({
          init: function(settings) {
              this._super(settings) // call base init
          },

          _InitializePageBuilder: function() {
              var __this = this
              __this._configureEvents()

          },

          _configureEvents: function() {
              var __this = this
              __this._basicEvents()
              __this._ajaxEvents()
          },

          _ajaxEvents: function() {

          },

          _basicEvents: function() {
              var __this = this
              __this._onClickEvents()
              __this._onChangeEvents()
              __this._onLoadEvents()
              __this._onKeyEvents()
          },

          _onClickEvents: function() {
              var __this = this
              $(document).on('click', '.valid', function(e) {
                  e.preventDefault();
                  $('.valid').attr('disabled', 'true')
                  if (__this._ValidateBeforeSave()) {
                     $('#form').submit();
                } else {
                   $('.valid').removeAttr("disabled") 
                }   
              });
              $('#addfamilyunit').on('click', function() {})
          },
          _onChangeEvents: function() {
              var __this = this
              $(".tempdpmartialstatus").change(function() {
                  var id = $(this).attr("dependantid");
                  if ($(this).val() == 2) {
                      $('#dependentspousediv_' + id).removeClass('nodisplay');
                  } else {
                      if ($(this).val() == 1) {

                          $("#tempdependantspousetitleid_" + id).val("");
                          $("#tempDependantSpousName_" + id).val("");
                          $("#tempDependantDob_" + id).val("");
                          $("#tempDependantwdate_" + id).val("");
                          //  $("#" + id).attr("dependantid", "-1");

                          $('#dependentspousediv_' + id).addClass('nodisplay');

                      }
                  }
              });

              $('#memberfile').on('change', function() {

                  //EDdocument.getElementById('Imagefile').onchange = function (e) {
                  _memberimage = "";
                  // Get the first file in the FileList object
                  var imageFile = this.files[0];
                  var type = imageFile.type;

                  if (this.files[0].size > 5242880) {
                    $('#memberfile').val('');
                      swal({
                          title: 'Member ',
                          text: 'Please upload files less than 2MB',
                          type: 'error',
                      })
                      return;
                  }
                  if (imageFile.type != 'image/png' && imageFile.type != 'image/jpg' && imageFile.type != 'image/jpeg') {
                      swal({
                          title: 'Warning',
                          text: 'File type not supported, Please use png, jpeg, jpg.',
                          type: 'error',
                      })
                      if ($("#memberimage").attr("src") != $('#base-url').val() + "/theme/images/default-user.png") {
                          memberimage.src = $('#base-url').val() + "/theme/images/default-user.png";
                      }
                  } else {

                      // get a local URL representation of the image blob
                      var url = window.URL.createObjectURL(imageFile);
                      // Now use your newly created URL!
                      memberimage.src = url;
                  }

              });
              $("#btnremovemember").on("click", function() {
                  memberimage.src = $('#base-url').val() + "/theme/images/default-user.png";
                  $('#memberfile').val("");
                  $('#memberImageChecker').val('removed');
                  //   $("#memberfile").replaceWith($("#memberfile").clone());
                  // _memberimage = "removed";
              });
              $("#btnspouseremove").on("click", function() { /*remove image */
                  spouseimage.src = $('#base-url').val() + "/theme/images/default-user.png";
                  $('#spousefile').val("");
                  $('#spouseImageChecker').val('removed');
                  //          $("#spousefile").replaceWith($("#spousefile").clone());
                  //          _spouseimage = "removed";
              });
              $('#txtMemberSpouseFirstName').on('change', function() {
                  __this._weddingAnnivesaryToogle()
              });
              $(".removedependantimage").on("click", function() {
                  var dependantId = $(this).attr("dependantid");
                  $('#dependantimage_' + dependantId).attr('src', $('#base-url').val() + "/theme/images/default-user.png");
                  $('#dependantfile_' + dependantId).val("");
                  $('#dependantpic_' + dependantId).val('removed');
              });

              $(".removedependantspouseimage").on("click", function() {
                  var dependantId = $(this).attr("dependantid");
                  $('#dependantspouseimage_' + dependantId).attr('src', $('#base-url').val() + "/theme/images/default-user.png");
                  $('#dependantspousefile_' + dependantId).val("");
                  $('#dependantspousepic_' + dependantId).val('removed');
              });


              $("#spousefile").on("change", function() {
                  //EDdocument.getElementById('Imagefile').onchange = function (e) {
                  _spouseimage = "";
                  // Get the first file in the FileList object
                  var imageFile = this.files[0];
                  var type = imageFile.type;
                  if (this.files[0].size > 5242880) {
                    $('#spousefile').val('');
                      swal({
                          title: 'Member ',
                          text: 'Please upload files less than 2MB',
                          type: 'error',
                      })
                      return;
                  }
                  if (imageFile.type != 'image/png' && imageFile.type != 'image/jpg' && imageFile.type != 'image/jpeg') {
                      swal({
                          title: 'Warning',
                          text: 'File type not supported, Please use png, jpeg, jpg.',
                          type: 'error',
                      })
                      if ($("#spouseimage").attr("src") != $('#base-url').val() + "/theme/images/default-user.png") {

                          spouseimage.src = $('#base-url').val() + "/theme/images/default-user.png";

                      }
                  } else {
                      // get a local URL representation of the image blob
                      var url = window.URL.createObjectURL(imageFile);
                      // Now use your newly created URL!
                      spouseimage.src = url;
                  }

              });

              $('.depentendImage').on('change', function() {

                  var dependantid = $(this).attr("dependantImageid");

                  var imageFile = this.files[0];
                  var type = imageFile.type;
                  if (this.files[0].size > 5242880) {
                    $('.depentendImage').val('');
                      swal({
                          title: 'Member ',
                          text: 'Please upload files less than 2MB',
                          type: 'error',
                      })
                      return;
                  }
                  if (imageFile.type != 'image/png' && imageFile.type != 'image/jpg' && imageFile.type != 'image/jpeg') {
                      swal({
                          title: 'Warning',
                          text: 'File type not supported, Please use png, jpeg, jpg.',
                          type: 'error',
                      })
                      if ($("#dependantimage_" + dependantid).attr("src") != $('#base-url').val() + "/theme/images/default-user.png") {
                          var url = $('#base-url').val() + "/theme/images/default-user.png";
                          $("#dependantimage_" + dependantid).attr('src', url);
                      }
                      $("#dependantimage_" + dependantid).replaceWith($("#dependantimage_" + dependantid).clone());

                  } else {
                      // get a local URL representation of the image blob
                      var url = window.URL.createObjectURL(imageFile);
                      // Now use your newly created URL!

                      $("#dependantimage_" + dependantid).attr('src', url);
                  }
              });



              $('.dependantspouseimage').on('change', function() {

                  var dependantid = $(this).attr("dependantImageid");
                  var imageFile = this.files[0];
                  var type = imageFile.type;
                  if (this.files[0].size > 5242880) {
                    $('.dependantspouseimage').val('');
                      swal({
                          title: 'Member ',
                          text: 'Please upload files less than 2MB',
                          type: 'error',
                      })
                      return;
                  }
                  if (imageFile.type != 'image/png' && imageFile.type != 'image/jpg' && imageFile.type != 'image/jpeg') {
                      swal({
                          title: 'Warning',
                          text: 'File type not supported, Please use png, jpeg, jpg.',
                          type: 'error',
                      })
                      if ($("#dependantspouseimage_" + dependantid).attr("src") != $('#base-url').val() + "/theme/images/default-user.png") {
                          var url = $('#base-url').val() + "/theme/images/default-user.png";
                          $("#dependantspouseimage_" + dependantid).attr('src', url);
                      }
                      $("#dependantspouseimage_" + dependantid).replaceWith($("#dependantimage_" + dependantid).clone());

                  } else {
                      // get a local URL representation of the image blob
                      var url = window.URL.createObjectURL(imageFile);
                      // Now use your newly created URL!

                      $("#dependantspouseimage_" + dependantid).attr('src', url);


                  }
              });

          },
          _onLoadEvents: function() {
              var __this = this
              $('#test').tagsinput('items')
              $(document).ready(function() {
                __this._weddingAnnivesaryToogle()

            });
          },
          _onKeyEvents: function() {
            var __this = this;
        
            $(document).on('keypress', '#dependantmobile, #dependantspousemobile', function(event) {
                var charCode = event.which ? event.which : event.keyCode;
        
                // Allow numbers (0-9), backspace (8), and delete (46)
                if (charCode < 48 || charCode > 57) {
                    if (charCode !== 8 && charCode !== 46) {
                        event.preventDefault();
                    }
                }
            });
        
            // Ensure pasted input contains only numbers
            $(document).on('input', '#dependantmobile, #dependantspousemobile', function() {
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
            });

            $(document).on('keypress', '#dependantmobilecountrycode, #dependantspousemobilecountrycode', function(event) {
                var charCode = event.which ? event.which : event.keyCode;
            
                // Allow numbers (0-9), backspace (8), delete (46), and plus (+)
                if ((charCode < 48 || charCode > 57) && charCode !== 8 && charCode !== 46 && charCode !== 43) {
                    event.preventDefault();
                }
            });
            $(document).on('input', '#dependantmobilecountrycode, #dependantspousemobilecountrycode', function() {
                let value = $(this).val();
            
                // Allow "+" only at the beginning and remove all other non-numeric characters
                value = value.replace(/[^0-9+]/g, '');
            
                // Ensure "+" appears only at the start
                if (value.includes('+')) {
                    value = '+' + value.replace(/\+/g, '');
                }
            
                $(this).val(value);
            });
          },
          _SetEditDependant: function(data) {
              $('.dependantFrom').html(data);
          },
          // funtion for validating controls before submit
          _ValidateBeforeSave: function() {

              var regexEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
              var regexName = /^[a-zA-Z]+([ ][a-zA-Z]+)*$/;
              //var regexPhone = /^\d{10}$/;
              var regexPhone = /^\d{6,}$/;
              var regexcountrycode = /^(\+(?:\d{2,3}$))|(\+(?:\d{1})$)|(?:\d{2,3}$)|(?:\d{1}$)/;
              var regexareacode = /^\d{0,5}$/;
              var regexpin = /^.{4,8}$/;
              //var regexOfficePhone = /^(?=.*[0-9])[- ()0-9]+$/;
              var regexOfficePhone = /^\d{7,10}$/;
              var boolresult = true;
              var latLongPattern = /^-?\d{1,3}(\.\d+)?°?\s?[NSEW]?$/;
              var latitudePattern = /^-?(90|[0-8]?\d)(\.\d+)?$/;
              var longitudePattern = /^-?(180|1[0-7]\d|[0-9]?\d)(\.\d+)?$/;

              if ($("#MemberTitle").val() == "") {
                  swal({
                      title: '',
                      text: 'Please select Member title.'
                  })
                  $('.nav-tabs a[href="#member"]').tab('show');
                  $("#MemberTitle").focus();
                  boolresult = false;
              } else if ($("#txtFirstName").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Member First Name Cannot be blank !',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtFirstName").focus();
                      })
                  boolresult = false;
              } else if ($("#txtLastName").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Member Last Name Cannot be blank !',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtLastName").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberMobile1").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Member Mobile number is mandatory !',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberMobile1").focus();
                      })
                  boolresult = false;
              } else if (($("#txtlatitude").val() == "") && ($("#txtlongitude").val() != "")) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Latitde and Longitude are filled together',
                        type: 'error',
                        timer: 2000
                    },
                    function() {
                        $("#txtlatitude").focus();
                    }
                )
                boolresult = false;
            } else if (($("#txtlatitude").val() != "") && ($("#txtlongitude").val() == "")) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Latitde and Longitude are filled together',
                        type: 'error',
                        timer: 2000
                    },
                    function() {
                        $("#txtlongitude").focus();
                    }
                )
                boolresult = false;
            } else if (($("#txtlatitude").val() != "") && !latitudePattern.test($("#txtlatitude").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Enter a valid coordinate: Latitude (-90 to 90), Longitude (-180 to 180).',
                        type: 'error',
                        timer: 2000
                    },
                    function() {
                        $("#txtlatitude").focus();
                    }
                )
                boolresult = false;
            } else if (($("#txtlongitude").val() != "") && !longitudePattern.test($("#txtlongitude").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Enter a valid coordinate: Latitude (-90 to 90), Longitude (-180 to 180).',
                        type: 'error',
                        timer: 2000
                    },
                    function() {
                        $("#txtlongitude").focus();
                    }
                )
                boolresult = false;
            } else if ($("#txtspousemobile1").val() != "" && $("#txtMemberSpouseFirstName").val().trim() == "") {
                console.log('spouse mobile:',$("#txtspousemobile1").val());
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse First Name Cannot be blank !',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberSpouseFirstName").focus();
                    }
                );
                boolresult = false;
              } else if ($("#txtMemberEmail").val() != "" && !regexEmail.test($("#txtMemberEmail").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Member Email not in proper format !',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberEmail").focus();
                      })
                  boolresult = false;
              }
              // else if ($("#txtMemberMobile1").val() != "" && !regexPhone.test($("#txtMemberMobile1").val())) {
              //     $('.nav-tabs a[href="#member"]').tab('show');
              //     swal({
              //             title: '',
              //             text: 'Member Mobile Number not in proper format !',
              //             type: 'error',
              //             //closeOnConfirm: false
              //             timer: 2000
              //         },
              //         function() {
              //             $("#txtMemberMobile1").focus();
              //         })
              //     boolresult = false;
              // } 
              // else if ($("#txtMemberMobile1").val() != "" && $("#txtMemberMobile1_countrycode").val() == "") {
              //     $('.nav-tabs a[href="#member"]').tab('show');
              //     swal({
              //             title: '',
              //             text: 'Member Mobile country code is mandatory !',
              //             type: 'error',
              //             //closeOnConfirm: false
              //             timer: 2000
              //         },
              //         function() {
              //             $("#txtMemberMobile1_countrycode").focus();
              //         })
              //     boolresult = false;
              // } 
              // else if ($("#txtMemberMobile1_countrycode").val() != "" && !regexcountrycode.test($("#txtMemberMobile1_countrycode").val())) {
              //     $('.nav-tabs a[href="#member"]').tab('show');
              //     $("#txtMemberMobile1_countrycode").focus();
              //     swal({
              //             title: '',
              //             text: 'Country Code not in proper format !',
              //             type: 'error',
              //             //closeOnConfirm: false
              //             timer: 2000
              //         },
              //         function() {
              //             $("#txtMemberMobile1_countrycode").focus();
              //         })
              //     boolresult = false;
              // } 
              // else if ($("#txtspousemobile1").val() != "" && !regexPhone.test($("#txtspousemobile1").val())) {
              //     $('.nav-tabs a[href="#member"]').tab('show');
              //     swal({
              //             title: '',
              //             text: 'Spouse Mobile Number not in proper format!',
              //             type: 'error',
              //             //closeOnConfirm: false
              //             timer: 2000
              //         },
              //         function() {
              //             $("#txtspousemobile1").focus();
              //         })
              //     boolresult = false;
              // } 
              else if ($("#txtspousemobile1").val() != "" && $("#txtspousemobile1_countrycode").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Spouse Mobile country code is mandatory!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtspousemobile1_countrycode").focus();
                      })
                  boolresult = false;
              } else if ($("#txtspousemobile1_countrycode").val() != "" && !regexcountrycode.test($("#txtspousemobile1_countrycode").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Spouse Country Code not in proper format!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtspousemobile1_countrycode").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberspouseemail").val() != "" && !regexEmail.test($("#txtMemberspouseemail").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Spouse Email not in proper format!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberspouseemail").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberspouseemail").val() != "" && ($("#txtMemberspouseemail").val() == $("#txtMemberEmail").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Member and Spouse Email cannot be same!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberspouseemail").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberMobile1").val() != "" && ($("#txtspousemobile1").val() == $("#txtMemberMobile1").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Member and Spouse Mobile no cannot be same!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtspousemobile1").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberspouseemail").val() != "" && $("#txtMemberSpouseFirstName").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  console.log('spouse txtMemberspouseemail:',$("#txtMemberspouseemail").val());
                  swal({
                          title: '',
                          text: 'Spouse First Name cannot be blank!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberSpouseFirstName").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberspouseemail").val() != "" && !regexName.test($("#txtMemberSpouseFirstName").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Spouse First Name not in proper format!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberSpouseFirstName").focus();
                      })
                  boolresult = false;
              } else if ($("#txtSpouseBloodgroup").val() != "" && $("#txtMemberSpouseFirstName").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  console.log('spouse txtSpouseBloodgroup:',$("#txtSpouseBloodgroup").val());
                  swal({
                          title: '',
                          text: 'Spouse First Name cannot be blank!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberSpouseFirstName").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberspouseemail").val() != "" && $("#txtMemberSpouseLastName").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Spouse Last Name cannot be blank!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberSpouseLastName").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberspouseemail").val() != "" && !regexName.test($("#txtMemberSpouseLastName").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Spouse Last Name not in proper format!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberSpouseLastName").focus();
                      })
                  boolresult = false;
              } else if ($("#spouse_dob").val() != "" && $("#txtMemberSpouseFirstName").val().trim() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Spouse First Name cannot be blank',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#spouse_dob").focus();
                    }
                )
                boolresult = false;

             } else if ($("#txtMemberNo").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  $("").focus();
                  swal({
                          title: '',
                          text: 'Member Number cannot be blank!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberNo").focus();
                      })
                  boolresult = false;
              } else if ($("#batch").val() == "") {
                $('.nav-tabs a[href="#member"]').tab('show');
                $("").focus();
                swal({
                        title: '',
                        text: 'Batch cannot be blank!',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#batch").focus();
                    })
                boolresult = false;
            }
              
              else if ($("#txtMemberShip_Type").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Membership type cannot be blank!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberShip_Type").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberBusinessPhone1_countrycode").val() != "" && !regexcountrycode.test($("#txtMemberBusinessPhone1_countrycode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office Phone 1 country code not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone1_countrycode").focus();
                    }
                )
                boolresult = false;
            }
            else if ($("#txtMemberBusinessPhone1_areacode").val() != "" && !regexareacode.test($("#txtMemberBusinessPhone1_areacode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office Phone 1 area code not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone1_areacode").focus();
                    }
                )
                boolresult = false;
            }
            else if ($("#txtMemberBusinessPhone3_countrycode").val() != "" && !regexcountrycode.test($("#txtMemberBusinessPhone3_countrycode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office Phone 2 country code not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone1_countrycode").focus();
                    }
                )
                boolresult = false;
            }
            else if ($("#txtMemberBusinessPhone3_areacode").val() != "" && !regexareacode.test($("#txtMemberBusinessPhone3_areacode").val())) {
                $('.nav-tabs a[href="#member"]').tab('show');
                swal({
                        title: '',
                        text: 'Office Phone 2 area code not in proper format',
                        type: 'error',
                        //closeOnConfirm: false
                        timer: 2000
                    },
                    function() {
                        $("#txtMemberBusinessPhone3_areacode").focus();
                    }
                )
                boolresult = false;
            }  else if ($("#txtMemberBusinessPhone1_areacode").val() != "" && $("#txtMemberBusinessPhone1").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Office Phone Number 1 cannot be blank!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberBusinessPhone1").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberBusinessPhone1_countrycode").val() != "" && $("#txtMemberBusinessPhone1").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Office Phone Number 1 cannot be blank!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberBusinessPhone1").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberBusinessPhone1").val() != "" && !regexOfficePhone.test($("#txtMemberBusinessPhone1").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Office Phone Number not in proper format!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberBusinessPhone1").focus();
                      })
                  boolresult = false;
              } else if ($("#txtbusinessEmail").val() != "" && !regexEmail.test($("#txtbusinessEmail").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Member Residential Email not in proper format !',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtbusinessEmail").focus();
                      })
                  boolresult = false;
              } else if ($("#txtDependantName").val() != "" && $("#DropDownRelation option:selected").text() != "") {
                  $('.nav-tabs a[href="#dependants"]').tab('show');
                  swal({
                          title: '',
                          text: 'Please add Dependant Details!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtState").focus();
                      })
                  boolresult = false;
              } else if ($('#SpouseTitle').val() > 0 && $("#txtMemberSpouseFirstName").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  console.log('spouse SpouseTitle:',$("#SpouseTitle").val());
                  swal({
                          title: '',
                          text: 'Spouse First Name cannot be blank',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberSpouseFirstName").focus();
                      })
                  boolresult = false;
              } else if ($('#SpouseTitle').val() > 0 && $("#txtMemberSpouseLastName").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Spouse Last Name cannot be blank',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberSpouseLastName").focus();
                      })
                  boolresult = false;
              } else if ($('#SpouseTitle').val() == "" && $("#txtMemberSpouseFirstName").val() != "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Please select Spouse title.',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#SpouseTitle").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberBusinessPhone3_areacode").val() != "" && $("#txtMemberBusinessPhone3").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Office Phone Number 2 cannot be blank!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberBusinessPhone3").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberBusinessPhone3_countrycode").val() != "" && $("#txtMemberBusinessPhone3").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  $("#txtMemberBusinessPhone3").focus();
                  swal({
                          title: '',
                          text: 'Office Phone Number 2 cannot be blank!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberBusinessPhone3").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberResidencePhone1").val() == "" && $("#txtMemberResidencePhone1_countrycode").val() != "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Residence Land Line Number cannot be empty!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberResidencePhone1").focus();
                      })
                  boolresult = false;
              } else if ($("#txtMemberResidencePhone1").val() == "" && $("#txtMemberResidencePhone1_areacode").val() != "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Residence Land Line Number cannot be empty!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberResidencePhone1").focus();
                      })
                  boolresult = false;
              }
              else if ($("#txtMemberResidencePhone1_countrycode").val() != "" && !regexcountrycode.test($("#txtMemberResidencePhone1_countrycode").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Member Residence Land Line Country Code is not in proper format.',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberResidencePhone1_countrycode").focus();
                      })
                  boolresult = false;
              }
              else if ($("#txtMemberResidencePhone1_areacode").val() != "" && !regexareacode.test($("#txtMemberResidencePhone1_areacode").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Member Residence Land Line Area Code is not in proper format!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberResidencePhone1_areacode").focus();
                      })
                  boolresult = false;
              }
              /*else if ($("#txtMemberResidencePhone1").val() != "" && !regexOfficePhone.test($("#txtMemberResidencePhone1").val())) {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  swal({
                          title: '',
                          text: 'Residence Land Line Number not in proper format!',
                          type: 'error',
                          //closeOnConfirm: false
                          timer: 2000
                      },
                      function() {
                          $("#txtMemberResidencePhone1").focus();
                      })
                  boolresult = false;
              } */
              // else if ($("#memberrole").val() == "" || $("#memberrole").val().trim().toLowerCase() == "please select" || $("#memberrole").val() == 0) {
              //     swal({
              //             title: '',
              //             text: 'Member Role cannot be blank!',
              //             type: 'error',
              //             //closeOnConfirm: false
              //             timer: 2000
              //         },
              //         function() {
              //             $("#memberrole").focus();
              //         })
              //     boolresult = false;
              // } 
              // else if ($("#txtspousemobile1").val() != "" && (($("#spouserole").val() == "" || $("#spouserole").val().trim().toLowerCase() == "please select" || $("#spouserole").val() == 0))) {
              //     swal({
              //             title: '',
              //             text: 'Spouse Role cannot be blank!',
              //             type: 'error',
              //             //closeOnConfirm: false
              //             timer: 2000
              //         },
              //         function() {
              //             $("#spouserole").focus();
              //         })
              //     boolresult = false;
              // } 
              else {
                  boolresult = true;
              }

              return boolresult;

          },
           _weddingAnnivesaryToogle:function() {
            if ($('#txtMemberSpouseFirstName').val() == null || $('#txtMemberSpouseFirstName').val() == "" || $('#txtMemberSpouseFirstName').val() == undefined) {
                $('#dom-kvdate').kvDatepicker('update', '');
                $('#date-of-wedding').hide()
            } else {
               $('#date-of-wedding').show()
            }
        },
        _ValidateBeforeDeleteMember: function() {
              var boolresult = true;
              if ($("#txtMemberspouseemail").val() != "" && $("#txtMemberSpouseFirstName").val() == "") {
                  alert("Spouse First Name Cannot be blank !");
                  $("#txtMemberSpouseFirstName").focus();
                  boolresult = false;
              } else if ($("#txtMemberSpouseLastName").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  alert("Spouse Last Name Cannot be blank !");
                  $("#txtMemberSpouseLastName").focus();
                  boolresult = false;
              } else if ($("#txtspousemobile1").val() == "") {
                  $('.nav-tabs a[href="#member"]').tab('show');
                  alert("Member Mobile number is mandatory !");
                  $("#txtspousemobile1").focus();
                  boolresult = false;
              } else {
                  boolresult = true;
              }
              return boolresult;
          },


          // public members
          buildPage: function() {
              this._InitializePageBuilder()
          }

      })
  var memberEditJS = new Remember.memberEdit.ui.PageBuilder({})
  jQuery(document).bind('SETUP_PAGE_BUILDERS_EVENT', function() {
      jsFramework.lib.ui.pageBinder.addPageBuilder(memberEditJS)
  })
