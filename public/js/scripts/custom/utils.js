$(function () {
    if (typeof toastr !== "undefined") {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-left",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }
});


// load datatable data module
function loadDataTables(url, keys, search_query, lang_obj) {
    var columns = [];
    var query = '';
    for (i = 0; i < keys.length; i++) {
        searchable = (keys.length - 1 !== i) ? true : false;
        columns[i] = {
            data: keys[i],
            orderable: false,
            searchable: searchable
        };
        columns[i].render = function (data, type, full, meta) {
            return $("<div/>").html(data).text();
        };
    }

    if (lang_obj.keys) {
        lang_obj.keys.forEach((object) => {
            let column = {
                data: object.key,
                orderable: false,
                searchable: object.search,
            };
            if (object.render) {
                column.render = function (data, type, full, meta) {
                    return $("<div/>").html(data).text();
                }
            }
            columns.push(column);
        });
    }

    let options = {
        processing: true,
        serverSide: true,
        ajax: url + search_query,
        language: {
            search: '<span>' + lang_obj.filter + ':</span> _INPUT_',
            searchPlaceholder: lang_obj.filter_type,
            lengthMenu: '<span>' + lang_obj.show + ':</span> _MENU_',
            paginate: {
                'first': lang_obj.first,
                'last': lang_obj.last,
                'next': '&rarr;',
                'previous': '&larr;'
            }
        },
        "initComplete": function (settings, json) {
            $('[data-toggle="tooltip"]').tooltip();
            if (lang_obj.fancy && lang_obj.fancy === true) {
                $('[data-popup="lightbox"]').fancybox({
                    padding: 3,
                    width: 560,
                    height: 340
                });
            }
        },
        "fnDrawCallback": function (setting) {
            $('[data-toggle="tooltip"]').tooltip();
            feather.replace();
            if (lang_obj.fancy && lang_obj.fancy === true) {
                $('[data-popup="lightbox"]').fancybox({
                    padding: 3,
                    width: 560,
                    height: 340
                });
            }
        },
        columns: columns
    };

    if (lang_obj.export && lang_obj.export === true) {
        options.dom = 'lBfrtip';
        options.buttons = [
            {
                extend: 'excel',
                text: '<span class="fa fa-file-excel-o"></span> Excel',
                exportOptions: {
                    modifier: {
                        search: 'applied',
                        order: 'applied'
                    }
                }
            }
        ];
        options.lengthMenu = [
            [10, 25, 50, 75, -1],
            [10, 25, 50, 75, "All"]
        ];
    }

    $('#datatable').DataTable(options);
}

// load datatable data module
function loadDataTablesWithId(url, keys, search_query, lang_obj) {
    var columns = [];
    var query = '';
    for (i = 0; i < keys.length; i++) {
        searchable = (keys.length - 1 !== i) ? true : false;
        columns[i] = {
            data: keys[i],
            orderable: false,
            searchable: searchable
        };
        columns[i].render = function (data, type, full, meta) {
            return $("<div/>").html(data).text();
        };
    }

    $('#' + lang_obj['id']).DataTable({
        processing: true,
        serverSide: true,
        ajax: url + search_query,
        language: {
            search: '<span>' + lang_obj.filter + ':</span> _INPUT_',
            searchPlaceholder: lang_obj.filter_type,
            lengthMenu: '<span>' + lang_obj.show + ':</span> _MENU_',
            paginate: {
                'first': lang_obj.first,
                'last': lang_obj.last,
                'next': '&rarr;',
                'previous': '&larr;'
            }
        },
        "initComplete": function (settings, json) {
            $('[data-toggle="tooltip"]').tooltip();
            if (lang_obj.fancy && lang_obj.fancy === true) {
                $('[data-popup="lightbox"]').fancybox({
                    padding: 3,
                    width: 560,
                    height: 340
                });
            }
        },
        "fnDrawCallback": function (setting) {
            $('[data-toggle="tooltip"]').tooltip();
            if (lang_obj.fancy && lang_obj.fancy === true) {
                $('[data-popup="lightbox"]').fancybox({
                    padding: 3,
                    width: 560,
                    height: 340
                });
            }
        },
        columns: columns
    });
}

// reset form data

function resetForm($form) {
    $form.find('input,input:text,input:password, input:file, select, textarea').val('');

    if (typeof defaultImage !== "undefined") {
        $('span.filename').text(name);
        $('#preview').attr('src', defaultImage);
        $('#preview').parent('a').attr('href', defaultImage);
    }
}

// handle when open add modal
function addModal(lang_obj) {
    $('#add_btn').on('click', function (e) {

        e.preventDefault();
        $('.modal-title').text(lang_obj.title);
        resetForm($('#general-form'));

        if(lang_obj.dropify){
            initDropify();
        }

        if (lang_obj.hiddenName && lang_obj.hiddenValue) {
            $('#general-form input[name=' + lang_obj.hiddenName + ']').val(lang_obj.hiddenValue);
        }


        if (lang_obj.imageSelector) {
            lang_obj.imageSelector.parent('div').find('span.filename').text('');
            let preview = lang_obj.imageSelector.parent('div').parent('div').parent('div').find('#preview');
            preview.attr('src', lang_obj.image);
            preview.parent('a').attr('href', lang_obj.image);
        }

        if (lang_obj.permissions) {
            $('#' + lang_obj.select_selector[0]).select2('val', []);
        } else if (lang_obj.select_selector && lang_obj.select_selector.length > 0) {
            lang_obj.select_selector.forEach((item, index) => {
                $('#' + item).val($('#' + item + ' > option:first-child').val()).trigger('change');
                $('#' + item).select2();
                $('#' + item).attr('disabled', false);
            });
        }
        if (lang_obj.multi_selector && lang_obj.multi_selector.length > 0) {
            lang_obj.multi_selector.forEach((item, index) => {
                $('#' + item).select2('val', '');
            });
        }
        if (lang_obj.hide && lang_obj.dependsOn) {
            const dependsOnValue = $('#' + lang_obj.dependsOn).find('option:selected').val();
            if (!dependsOnValue || dependsOnValue === lang_obj.value) {
                $('#' + lang_obj.hide).hide();
            } else {
                $('#' + lang_obj.hide).show();
            }
        }

        add = true;
        edit = false;
    });
}

// handles onClick event on modal close.
function onClose(addExam = null) {
    $('.close').on('click', function (e) {
        e.preventDefault();
        add = false;
        edit = false;

        if (addExam === true) {
            let type = $('#type').find('option:selected').val();
            if (type === 'level') {
                $('#level_id').find('option:selected').remove();
            } else if (type === 'lesson') {
                $('#lesson_id').find('option:selected').remove();
            }
        }
    });
}

// change status
function changeStatus(item, url, lang_obj, ban) {
    $.loader({
        className: "blue-with-image-2",
        content: ''
    });
    var id = $(item).attr('id');
    var form = new FormData();
    if (lang_obj.status) {
        form.append('status', lang_obj.status);
    }
    form.append('id', id);
    pub_id = id;
    $.ajax({
        url: url,
        method: 'POST',
        data: form,
        processData: false,
        contentType: false,
        headers: {'X-CSRF-TOKEN': csrf_token},
        success: function (response) {
            $.loader('close');
            if (ban === true)
                swal.close();
            toastr['success'](response.message, response.title);

            if (lang_obj.load_page) {
                window.location.href = lang_obj.load_page;
            } else if (lang_obj.load_datatable === undefined || lang_obj.load_datatable !== false) {
                if (lang_obj.datatableId) {
                    $('#' + lang_obj.datatableId).DataTable().ajax.reload(null, false);
                } else {
                    $('#datatable').DataTable().ajax.reload(null, false);
                }
            }

        },
        error: function (response) {
            $.loader('close');
            if (ban === true)
                swal.close();
            toastr['error'](response.responseJSON.message, response.responseJSON.title);
        }
    });
}

// edit data
function update(item, url, lang_obj) {
    var id = $(item).attr('id');
    var form = new FormData();
    form.append('id', id);
    $('.modal-title').text(lang_obj.title);
    pub_id = id;
    $.ajax({
        url: url,
        method: 'POST',
        data: form,
        processData: false,
        contentType: false,
        headers: {'X-CSRF-TOKEN': csrf_token},
        success: function (response) {

            if (response.code == 1) {

                if (lang_obj.image && lang_obj.image == true) {
                    $('#preview').attr('src', response.data.image);
                    $('#preview').parent('a').attr('href', response.data.image);
                }
                $('#general-form input[name=name_ar]').val(response.data.name_ar);
                $('#general-form input[name=name_en]').val(response.data.name_en);
                $('#status').select2('val', response.data.status);
                $('.bs-example-modal-lg').modal('toggle');
                edit = true;
                add = false;
            } else if (response.code == 2) {
                toastr['error'](response.message, response.title); // Wire up an
            }
        },
        error: function () {
            toastr['error'](lang_obj.error_message, lang_obj.error_title);
        }
    });
}

// make ajax request for modals
function sendModalAjaxRequest(form_selector, add_url, edit_url, lang_obj) {
    if (lang_obj.loader && lang_obj.loader === true) {
        $.loader({
            className: "blue-with-image-2",
            content: ''
        });
    }
    var form = new FormData(form_selector);
    if (add)
        url = add_url;
    else if (edit) {
        url = edit_url;
        form.append('id', pub_id);
    }
    $.ajax({
        url: url,
        method: 'POST',
        data: form,
        processData: false,
        contentType: false,
        headers: {'X-CSRF-TOKEN': csrf_token},
        success: function (response) {

            if (lang_obj.add_exam && lang_obj.add_exam === true) {
                let type = $('#type').find('option:selected').val();
                if (type === 'level') {
                    $('#level_id').find('option:selected').remove();
                } else if (type === 'lesson') {
                    $('#lesson_id').find('option:selected').remove();
                }
            }

            if (lang_obj.loader && lang_obj.loader === true) {
                $.loader('close');
            }

            $('.general_modal').modal('toggle');
            toastr['success'](response.message, response.title); // Wire up an
            if (lang_obj.datatableId) {
                $('#' + lang_obj.datatableId).DataTable().ajax.reload(null, false);
            } else {
                $('#datatable').DataTable().ajax.reload(null, false);
            }
            add = false;
            edit = false;
        },
        error: function (response) {
            if (lang_obj.loader && lang_obj.loader === true) {
                $.loader('close');
            }
            if (response.responseJSON.message && response.responseJSON.title) {
                toastr['error'](response.responseJSON.message, response.responseJSON.title);
            } else {
                toastr['error'](lang_obj.error_message, lang_obj.error_title);
            }
        }
    });
}

// search by key
function search(url, keys, search_obj, lang_obj) {
    if (search_obj.load == true)
        loadDataTables(url, keys, '?search_key=' + search_obj.key, lang_obj);
    $('#search').on('submit', function (e) {
        e.preventDefault();
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().destroy();
        }
        loadDataTables(url, keys, '?search_key=' + $('#search input[name=search_key]').val(), lang_obj);
    });
}

// search by query
function searchByQuery(url, keys, search_obj, lang_obj) {
    if (search_obj.load == true)
        loadDataTables(url, keys, '?' + search_obj.query, lang_obj);
    $('#search').on('submit', function (e) {
        e.preventDefault();
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().destroy();
        }
        loadDataTables(url, keys, '?' + $('#search').serialize(), lang_obj);
    });
}

// ban data
function ban(item, url, lang_obj) {
    swal.fire({
        title: lang_obj.ban_title,
        text: lang_obj.ban_message,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-warning',
        confirmButtonText: lang_obj.inactivate,
        cancelButtonText: lang_obj.cancel,
        closeOnConfirm: false
    }).then((result) => {
        if(result.isConfirmed) {
            if (lang_obj.modal) {
                resetForm(lang_obj.modal.form_id);
                lang_obj.modal.inputs.forEach((input) => {
                    input.name.val(input.value);
                });
                swal.close();
                lang_obj.modal.modal_id.modal('toggle');
            } else if (lang_obj.status && lang_obj.close_modal) {
                resetForm($('#general-form'));
                $('#general-form input[name=id]').val($(item).attr('id'));
                $('#general-form input[name=status]').val(lang_obj.status);
                swal.close();
                lang_obj.close_modal.modal('toggle');
            } else {
                changeStatus(item, url, lang_obj, true);
            }
        }else{
            swal.close();
        }
    });
}


function takeDivScreenShoot(selector, parentSelector, inputName, variable) {
    if (variable && variable !== null) {
        var value = variable.getValue();
        html2canvas($("#" + selector), {
            onrendered: function (canvas) {
                console.log(value)
                $('#' + parentSelector).append('<input type="hidden" name="' + inputName + '" value="'
                    + canvas.toDataURL("image/png") + '" />');
                $('#' + parentSelector).append('<input type="hidden" name="' + selector + '" value="'
                    + value + '" />');
            }
        });
    }
}

// send ajax request
function sendAjaxRequest(form_selector, url, lang_obj) {
    if (lang_obj.loader && lang_obj.loader === true) {
        $.loader({
            className: "blue-with-image-2",
            content: ''
        });
    }
    let form = new FormData(form_selector);
    if (lang_obj.editor && lang_obj.editor === true) {
        console.log(lang_obj.keys);
        let keys = lang_obj.keys.split(',');
        const base = 'content_';
        keys.forEach((key) => {
            form.append(base + key, tinymce.get(base + key).getContent());
        });
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: form,
        processData: false,
        contentType: false,
        headers: {'X-CSRF-TOKEN': csrf_token},
        success: function (response) {

            if (lang_obj.loader && lang_obj.loader === true) {
                $.loader('close');
            }
            toastr['success'](response.message, response.title); // Wire up an
            if (lang_obj.load_datatable && lang_obj.load_datatable == true) {
                $('#datatable').DataTable().ajax.reload(null, false);
            }
            if (lang_obj.close_modal) {
                lang_obj.close_modal.modal('toggle');
            }
            if (lang_obj.load_page) {
                window.location.href = lang_obj.load_page;
            }
            if (lang_obj.update) {
                $('.user-img').find('img').attr('src', response.data.admin_image);
            }
            if (lang_obj.clear){
                resetForm(form_selector);
            }

        },
        error: function (response) {
            if (lang_obj.loader && lang_obj.loader === true) {
                $.loader('close');
            }

            if (parseInt(response.status) === 403) {
                console.log(response);
                toastr['error'](response.responseJSON.message, response.responseJSON.title);
            } else {
                toastr['error'](lang_obj.message, lang_obj.error_title);
            }
        }
    });
}

// preview image
function previewImage(selector) {
    selector.change(function (e) {
        var name = this.files[0].name;
        selector.parent('div').find('span.filename').text(name);
        // reader.onload = function () {
        //     var imageInput = document.getElementById(selector.attr('id'));
        //     selector.parent('div').parent('div').parent('div').find('#preview').attr('src', reader.result);
        //     selector.parent('div').parent('div').parent('div').find('#preview').parent('a').attr('href',
        // $('span.filename').text(name);
        var reader = new FileReader();
        reader.onload = function () {
            var output = document.getElementById('preview');
            let preview = selector.parent('div').parent('div').parent('div').find('#preview');
            // output.src = reader.result;
            preview.attr('src', reader.result);
            preview.parent('a').attr('href', reader.result);
            // output.parentNode.href = reader.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    });
}

// validate number
function validateNumber(event) {
    var key = window.event ? event.keyCode : event.which;
    if (event.keyCode === 8 || event.keyCode === 46) {
        return true;
    } else if (key < 48 || key > 57) {
        return false;
    } else {
        return true;
    }
}

// validate letters

function validateLetters(event) {
    var key = window.event ? event.keyCode : event.which;
    console.log(key)
    // var key = event.keyCode; 1571
    return ((key >= 65 && key <= 90) || (key > 96 && key < 123) || (key >= 1571 && key <= 1610) || key == 1569 || key == 8);

};
