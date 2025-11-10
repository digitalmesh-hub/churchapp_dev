jsFramework.lib.core.utils.registerNamespace("Remember.album.ui");
Remember.album.ui.PageBuilder = jsFramework.lib.ui.basePageBuilder
    .extend({
        init: function(settings) {
            this._super(settings); // call base init
        },

        _InitializePageBuilder: function() {
            var __this = this;
            __this._configureEvents();
        },

        _configureEvents: function() {
            var __this = this;
            __this._basicEvents();
            __this._ajaxEvents();
        },

        _ajaxEvents: function() {

        },

        _basicEvents: function() {
            var __this = this;
            __this._onClickEvents();
            __this._onChangeEvents();
            __this._onLoadEvents();
            __this._onKeyEvents();
        },

        _onChangeEvents: function() {

        },
        _onLoadEvents: function() {
            var __this = this;

        },
        _onKeyEvents: function() {},
        _onClickEvents: function() {
            var __this = this

            $(document).on("click", ".addphoto", function() {
                    var eventId = $('#Albumlist option:selected').val()
                    if (eventId == "") {
                        swal("Please choose an event")
                        return false
                    }
                    $('#eventId').val(eventId);
                }),

                $(".delete-album").on("click", function(e) {

                    var ajaxUrl = $('#homeUrl').val() + $('#delete-album').val();
                    var albumId = $(this).attr('data-album-id');
                    swal({
                            title: 'Are you sure?',
                            text: 'Are you sure you want to delete this album ?',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonClass: 'btn-danger',
                            confirmButtonText: 'Yes',
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        },
                        function() {
                            $.post(ajaxUrl, // Ajax Post URL
                                {
                                    '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                                    albumId: albumId,
                                }, // Data
                                function(res) {
                                    if (typeof(res) !== 'undefined' && res.status === 'success') {
                                        swal({
                                                title: 'Success',
                                                text: 'The album deleted',
                                                type: 'success'
                                            },
                                            function() {
                                                location.reload()
                                            }
                                        )
                                    } else {
                                        swal({
                                                title: 'Failed',
                                                text: 'Sorry! unable to complete the process',
                                                type: 'error'
                                            }),
                                            function() {
                                                location.reload()
                                            }
                                    }
                                }

                            )
                        });
                });
            $(".delete-image").on("click", function(e) {

                var ajaxUrl = $('#homeUrl').val() + $('#delete-image').val();
                var imageId = $(this).attr('data-image-id');
                swal({
                        title: 'Are you sure?',
                        text: 'Are you sure you want to delete this image ?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'btn-danger',
                        confirmButtonText: 'Yes',
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    },
                    function() {
                        $.post(ajaxUrl, // Ajax Post URL
                            {
                                '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                                imageId: imageId,
                            }, // Data
                            function(res) {
                                if (typeof(res) !== 'undefined' && res.status === 'success') {
                                    swal({
                                            title: 'Success',
                                            text: 'The image deleted',
                                            type: 'success'
                                        },
                                        function() {
                                            location.reload()
                                        }
                                    )
                                } else {
                                    swal({
                                            title: 'Failed',
                                            text: 'Sorry! unable to complete the process',
                                            type: 'error'
                                        }),
                                        function() {
                                            location.reload()
                                        }
                                }
                            }

                        )
                    });
            });

            $(document).on('click', '#btn-publish', function() {

                var publish = $(this).attr('data-album-id')
                if (publish == 1) {
                    $(this).removeAttr("href");
                } else {
                    var ajaxUrl = $('#homeUrl').val() + $(this).attr('url')
                    var albumId = $(this).attr('album-id')
                    swal({
                            title: 'Are you sure?',
                            text: 'Do you want to publish this album',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonClass: 'btn-danger',
                            confirmButtonText: 'Yes',
                            closeOnConfirm: false
                        },
                        function() {
                            $.post(ajaxUrl, // Ajax Post URL
                                {
                                    '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                                    albumId: albumId
                                }, // Data
                                function(res) {
                                    if (typeof(res) !== 'undefined' && res.status === 'success') {
                                        swal({
                                                title: 'Success',
                                                text: 'Album published successfully',
                                                type: 'success'
                                            },
                                            function() {
                                                location.reload()
                                            }
                                        )
                                    } else {
                                        swal({
                                                title: 'Failed',
                                                text: 'Sorry! unable to complete the process',
                                                type: 'error'
                                            }),
                                            function() {
                                                location.reload()
                                            }
                                    }
                                })
                        })
                }
            });
            $(document).on('click', '.btn-add-pending', function() {
                var numberOfChecked = $('input:checkbox:checked').length;
                var totalCheckboxes = $('input:checkbox').length;

                var message = 'Adding ' + numberOfChecked + ' of ' + totalCheckboxes + ' Photos. Remaining photos will be permanently deleted . Are you sure ?';
                $('#approvepara').text(message)
                $('#approval').modal('show')
            })

            $(document).on('click', '#approvealbum', function() {

                $('#approval').modal('hide')
                var imageId = $('input[type=checkbox]:checked').map(function(_, el) {
                    return $(el).val();
                }).get();
                var albumId = $('#albumId').val();
                $.ajax({
                    url: $('#homeUrl').val() + $('#approve-album').val(),
                    type: "POST",
                    dataType: 'json',
                    async: true,
                    data: {
                        '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                        'albumId': albumId,
                        'imageId': imageId
                    },
                    success: function(res) {
                        if (typeof(res) !== 'undefined' && res.status === 'success') {
                            swal({
                                    title: 'Success',
                                    text: 'Photos approved successfully',
                                    type: 'success'
                                },
                                function() {
                                    window.location = $('#homeUrl').val() + 'album/'
                                }
                            )
                        } else {
                            swal({
                                    title: 'Failed',
                                    text: 'Sorry! unable to complete the process',
                                    type: 'error'
                                }),
                                function() {
                                    location.reload()
                                }
                        }
                    },
                    error: function(er) {
                        location.reload()
                        //alert ('test error')
                    },
                })

            });
            $(document).on('click', '.unpublish', function() {

                var publish = $(this).attr('data-album-id')
                if (publish == 0) {
                    $(this).removeAttr("href");
                } else {
                    var ajaxUrl = $('#homeUrl').val() + $(this).attr('url')
                    var albumId = $(this).attr('album-id')
                    swal({
                            title: 'Are you sure?',
                            text: 'Do you want to unpublish this album',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonClass: 'btn-danger',
                            confirmButtonText: 'Yes',
                            closeOnConfirm: false
                        },
                        function() {
                            $.post(ajaxUrl, // Ajax Post URL
                                {
                                    '_csrf-backend': $("meta[name='csrf-token']").attr('content'),
                                    albumId: albumId
                                }, // Data
                                function(res) {
                                    console.log(res)
                                    if (typeof(res) !== 'undefined' && res.status === 'success') {
                                        swal({
                                                title: 'Success',
                                                text: 'The album has been unpublished successfully',
                                                type: 'success'
                                            },
                                            function() {
                                                location.reload()
                                            }
                                        )
                                    } else {
                                        swal({
                                                title: 'Failed',
                                                text: 'Sorry! unable to complete the process',
                                                type: 'error'
                                            }),
                                            function() {
                                                location.reload()
                                            }
                                    }
                                })
                        })

                }

            });

        },


        // public members
        buildPage: function() {
            this._InitializePageBuilder();
        }

    });
var AlbumJS = new Remember.album.ui.PageBuilder({});
jQuery(document).bind("SETUP_PAGE_BUILDERS_EVENT", function() {
    jsFramework.lib.ui.pageBinder.addPageBuilder(AlbumJS);
});