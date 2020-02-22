<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Option Framework</title>

    <script src="{{asset('option-framework/vendors/jquery-2.1.3.js')}}"></script>
    <link rel="stylesheet" href="{{asset('option-framework/css/style.css')}}">


</head>

<body>
<header class="header_top">
    <div class="name"><i class="fa fa-gears"></i> Option Framework</div>
    <div class="actions">
        <a class="btn_top" href="{{url(config('option-framework.admin_panel_path'))}}">Goto Admin Panel</a>
        <a class="btn_top" href="https://laravelarticle.com/laravel-option-framework"
           title="Laravel Log Reader">Doc</a>
    </div>
</header>
<section class="content">

    <div class="option_wrapper">
    </div>
    <!--option wrapper end-->

    <!-- error modal -->
    <div class="modal fade" id="errorModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Error!</h4>
                </div>
                <div class="modal-body"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <a href="https://laravelarticle.com/laravel-option-framework"
                       title="Laravel Option Framework"
                       target="_blank"
                       class="btn btn-primary">Check Doc</a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>

    <link rel="stylesheet" href="{{asset('option-framework/css/font-awesome.min.css')}}">

    <script src="{{asset('option-framework/vendors/bootstrap.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('option-framework/css/bootstrap.min.css')}}">

    <script src="{{asset('option-framework/vendors/select2/select2.js')}}"></script>
    <link rel="stylesheet" href="{{asset('option-framework/vendors/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('option-framework/vendors/select2/select2-bootstrap.css')}}">

    <script src="{{asset('option-framework/vendors/moment.min.js')}}"></script>
    <script src="{{asset('option-framework/vendors/bootstrap-datetime/bootstrap-datetime.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('option-framework/vendors/bootstrap-datetime/bootstrap-datetime.min.css')}}">

    <script src="{{asset('option-framework/vendors/bootstrap-switch.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('option-framework/vendors/bootstrap-switch/bootstrap-switch.min.css')}}">
    <script src="{{asset('option-framework/vendors/bootstrap-colorpicker.min.js')}}"></script>
    <link rel="stylesheet"
          href="{{asset('option-framework/vendors/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}">

    <script>
        $(document).ready(function () {
            var errorModal = $('#errorModal')
            localStorage.setItem('_active_tab', '')

            $(document).on('click', 'div.list-group > a', function (e) {
                e.preventDefault();
                $(this).siblings('a.active').removeClass("active");
                $(this).addClass("active");
                var index = $(this).index();

                $("div.h_tab_content").removeClass("active");
                $("div.h_tab_content").eq(index).addClass("active");

                localStorage.setItem('_active_tab', index)
            });

            //checkbox
            $(document).bind('content_loaded', function (e, status) {
                $('.multicheck_input').each(function () {
                    var inputbox = $(this)
                    var arr = inputbox.val().split(',')
                    arr.forEach(function () {
                        inputbox.siblings('input[type="checkbox"]').each(function () {
                            if (arr.indexOf($(this).val()) > -1) $(this).prop("checked", true)
                        })
                    })
                });

                $('div.multicheck_box input[type="checkbox"]').change(function () {
                    var inputbox = $(this).siblings('.multicheck_input');
                    var arr = inputbox.val().trim().split(',');
                    var value = $(this).val()

                    if ($(this).is(':checked')) {
                        arr.push(value)
                    } else {
                        var index = arr.indexOf(value);
                        if (index !== -1) arr.splice(index, 1);
                    }
                    inputbox.val(arr.join(',').replace(/^,/, ''))
                });
            })
            //end checkbox

            $(document).bind('content_loaded', function (e, status) {
                $(".bs-switch").bootstrapSwitch({offColor: 'warning'});
                $('.clr_picker').colorpicker()
                $('.datepicker').datetimepicker({format: 'YYYY-MM-DD'});
                $('.timepicker').datetimepicker({format: 'HH:mm'});
                $('.datetimepicker').datetimepicker({'format': 'YYYY-MM-DD HH:mm'});


                function getUrl(el) {
                    var options = $(el).data('options');
                    return '{{$viewPath}}?autocomplete_request&options=' + options;
                }

                $('.autocomplete').select2({
                    initSelection: function (element, callback) {
                        var options = $(element).data('options');
                        var id = $(element).val();

                        if (id !== "") {
                            $.ajax('{{$viewPath}}?autocomplete_request&options=' + options + '&id=' + id, {
                                dataType: "json"
                            }).done(function (data) {
                                callback(data);
                            });
                        }
                    },
                    ajax: {
                        url: function () {
                            var options = $(this.context).data('options');
                            return '{{$viewPath}}?autocomplete_request&options=' + options;
                        },
                        dataType: 'json',
                        type: "GET",
                        quietMillis: 500,
                        data: function (term) {
                            return {q: term};
                        },
                        results: function (data) {
                            return {results: data}
                        }
                    },
                    placeholder: 'Search for user',
                    minimumInputLength: 1,
                });

                $('.select2').select2({
                    theme: 'bootstrap',
                    width: '100%'
                });

                $('.icon_select_box').select2({
                    theme: 'bootstrap',
                    width: '100%',
                    containerCssClass: 'icon_select_box',
                    dropdownCssClass: 'icon_select_box',
                });

                $('.icon_select_box').each(function () {
                    var val = $(this).data('selected')
                    if (val !== '') {
                        $(this).val(val).trigger('change')
                    }
                });


                $(".tag").select2({
                    theme: 'bootstrap',
                    width: '100%',
                    multiple: true,
                    dropdownCssClass: 'hideTagSearch',
                    allowClear: true,
                    tags: true,
                    tokenSeparators: [","]
                });
                tinyMCE.init({
                    selector: "textarea.editor",
                    branding: false,
                    menubar: false,
                    plugins: [
                        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars fullscreen",
                        "media nonbreaking table directionality",
                        "emoticons template paste textcolor colorpicker",
                    ],
                    toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link table fullscreen",
                })
            })

            var optionBox = $('.option_wrapper');

            var init = function () {
                $.get('{{$dataPath}}')
                    .success(function (content) {
                        optionBox.html(content)
                        $(document).trigger('content_loaded')
                    })
                    .error(function (error) {
                        errorModal.find('.modal-body').text(error.responseJSON.message)
                        errorModal.modal('show')
                    })

            };

            init();

            $(document).on('click', '.btnSaveOption', function (event) {
                event.preventDefault();
                var btnSave = $(this)
                var _form = $(this).closest('form');
                var data = _form.serialize();
                var url = _form.attr('action');

                btnSave.text('Saving...').attr('disabled');

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'}
                });

                $.ajax({
                    type: "POST",
                    dataType: 'JSON',
                    url: url,
                    data: data,
                    success: function (data) {
                        btnSave.removeAttr('disabled').text('Save');
                        if (data.success) {
                            btnSave.parent().addClass('well-success')
                            setTimeout(function () {
                                btnSave.parent().removeClass('well-success')
                            }, 1000)
                        }
                    },
                    error: function (error) {
                        btnSave.removeAttr('disabled').text('Save');
                        console.log(error)

                        optionBox.html(error.responseText)
                        $(document).trigger('content_loaded')
                        $('div.list-group > a').eq(localStorage.getItem('_active_tab')).click()

                    }
                }); //end ajax

            })

        });
    </script>
</section>

</body>

</html>