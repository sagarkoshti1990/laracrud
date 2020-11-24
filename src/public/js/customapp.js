
function isset (variable) {
    if(typeof(variable) != "undefined" && variable !== null && typeof variable != "object") {
        return true;
    } else if((typeof variable == "object" && typeof variable.length == "undefined") || (typeof variable == "object" && variable.length != "undefined" && variable.length > 0)) {
        return true;
    }
    return false;
}
function IsJsonString(str) {
    try {
        return JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
if(window.location.hash != "") {
    $('a[href="' + window.location.hash + '"]').trigger('click')
}

$.ajaxSetup({
    headers: {
        Accept: "application/json; charset=utf-8",
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$("select").on("select2:close", function (e) {
    $(this).valid();
});
$("form").on('change', ':input', function(){
    $(this).valid();
});
$.validator.setDefaults({
    ignore: ':disabled',
    errorClass: "error invalid-feedback",
    errorElement: "span",
    highlight: function(element) {
        if(isset($(element).closest('.btn-group').find('a[file_type]'))) {
            $(element).closest('.btn-group').find('a[file_type]').addClass('form-control is-invalid');
        } else if($(element).hasClass('ckeditor_required')) {
            $('#cke_'+element.id).addClass('form-control is-invalid');
        } else if(isset($(element).closest('.form-group'))) {
            $(element).closest('.form-group').find(':input').addClass('is-invalid');
        } else {
            $(element).addClass('is-invalid');
        }
    },
    unhighlight: function(element) {
        if(isset($(element).closest('.btn-group').find('a[file_type]'))) {
            $(element).closest('.btn-group').find('a[file_type]').removeClass('form-control is-invalid');
        } else if($(element).hasClass('ckeditor_required')) {
            $('#cke_'+element.id).removeClass('form-control is-invalid');
        } else if(isset($(element).closest('.form-group'))) {
            $(element).closest('.form-group').find(':input').removeClass('is-invalid');
            $(element).closest('.form-group').find('.error').remove();
        } else {
            $(element).removeClass('is-invalid');
            $(element).parent().find('.error').remove();
        }
    },
    errorPlacement: function (error, element) {
        $(error).css('display','block')
        $(element).closest('.form-group').find('.invalid-feedback').remove();
        if($(element).closest('.form-group').length > 0) {
            error.insertAfter($(element).closest('.form-group').children().last());
        } else if($(element).closest('.input-group').length > 0) {
            error.insertAfter($(element).closest('.input-group').children().last());
        }
    },
    invalidHandler: function(event, validator) {
        var element = validator.errorList[0].element;
        var target = $(element).closest('.tab-pane').attr('id');
        $(`[data-target='#${target}']`).trigger('click');
        var errors = validator.numberOfInvalids();
        if (errors) {
            if(element.type && element.type == 'hidden') {
                $(element).closest('.form-group').find(':input').not(':input[name='+element.name+']').first().focus();
            } else {
                element.focus();
            }
        }
        $(validator.currentForm).find('[type="submit"]').removeAttr('disabled');
    }
});
// jquery custome validate methode
$.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
}, "Only Alphabetical characters");

// jquery custome validate methode first later capital and laterce
$.validator.addMethod("fcandlettersonly", function(value, element) {
    value = element.value = value[0].toUpperCase() + value.slice(1);
    return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
}, "Only Alphabetical characters");

$.validator.addMethod("notalphabet", function(value, element) {
    return this.optional(element) || /^[0-9-.\s]+$/i.test(value);
}, "Alphabet characters not accepted");

$.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^\w+$/i.test(value);
}, "Alphabet, Numbers and Underscores only please");

$.validator.addMethod("capitalnumeric", function(value, element) {
    return this.optional(element) || /[A-Z-0-9\s]+$/i.test(value);
}, "Capital Alphabet and Numbers only please");

$.validator.addMethod("recapnum", function(value, element) {
    $(element).val(value.toUpperCase());
    return this.optional(element) || /^(?=.*[0-9\s])(?=.*[a-zA-Z\s]).*$/i.test(value);
},  "capital alphabet and numbers both at least one please.");

// Older "accept" file extension method. Old docs: http://docs.$.com/Plugins/Validation/Methods/accept
$.validator.addMethod("extension", function(value, element, param) {
	param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
	return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, "Please select a file valid extension.");

$.validator.addMethod("ckeditor_required", function(value, element) {
    var editor = CKEDITOR.instances[element.name];
    var editor1 = CKEDITOR.instances[element.id];
    if (isset(editor) || isset(editor1)) {
        if(isset(editor)) {
            if(editor.getData() != null && editor.getData() != "") {
                return true;
            }
        } else if(isset(editor1)) {
            if(editor1.getData() != null && editor1.getData() != "") {
                return true;
            }
        }
    }
    return false;
}, "This ckediter field is required.");

// jquery custome validate methode
$.validator.addMethod("phone_input", function(value, element) {
    return this.optional(element) || /^\+?\d*$/i.test(value);
}, "phone number not valid");
jQuery.validator.addMethod("unique", function(value, element, params) {
    var prefix = params;
    var selector = jQuery.validator.format("[name!='{0}'][unique='{1}']", element.name, prefix);
    var matches = new Array();
    $(selector).each(function(index, item) {
        if (value == $(item).val()) {
            matches.push(item);
        }
    });
    return (matches.length == 0);
}, "Value is not unique.");

jQuery.validator.classRuleSettings.unique = {
    unique: true
};

$.validator.addMethod("mininput", function(value, element,params) {
    var $from = $(element).closest('form');
    var selector = $from.find("[name='"+$(element).attr('target')+"']").first();
    return (parseInt(value) < parseInt($(selector).val()));
}, function(params, element) {
    var message = $("[for='"+$(element).attr('target')+"']").first().text();
    return 'The field cannot be less than than ' + message.replace('*','');
});

function UpperCase(params) {
    var data = $(params).val();
    var value = data.toUpperCase();
    $(params).val(value);
}

$('button.btn.f-next-btn.btn-success').on('click', function() {
    $data = $(this).closest('.tab-pane').find(':input');
    if($data.valid()) {
        var classli = $(this).data('target');
        $('a[href="#' + classli + '"]').trigger('click');
    }
});
function datatable_details(table,table_data) {
    // var table = $(this).closest('table').datatable();
    $('table tbody').on('click','tr td.details-control',function() {
        var item_id = $(this).next().text();
        var tr = $(this).closest('tr');
        var row = table.row( tr );
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            var bsurl = $('body').attr("bsurl");
            var prefixRoute = table_data['table']['prefix'];
            $.ajax({
                type: "get",
                url: bsurl+"/"+prefixRoute+"/table",
                data:table_data['table'],
                success: function (data) {
                    if(data.statusCode == 200) {
                        row.child(data.html).show();
                        var table = row.child().find('table.table').first();
                        if(typeof table_data['datatable']['url'] != undefined && table_data['datatable']['url'] != "") {
                            table_data['datatable']['url'] = data.route;
                        }
                        table_data['datatable']['filter'][0][1] = item_id;
                        datatable_assined(table,table_data['datatable']);
                    } else {
                        row.child("<p class='error'>"+data.message+"</p>").show();
                    }
                    tr.addClass('shown');
                }
            });
        }
    })
}

function datatable_assined(table,table_data) {
    var dt_table = $(table).DataTable({
        "pageLength": table_data['pageLength'],
        "aaSorting": [],"processing": true,"serverSide": true,"responsive": true,
        "language": {"paginate": {"next":">","previous":"<"}},
        "ajax": {
            "url": table_data['url'],
            "type": "POST",
            'data': table_data
        },
        dom: "<tr><'row small-db-foot'<'col-sm-12'p><'col-sm-1'>>",
    });
}

$(':input.f-show-password+.input-group-append .fa').on('click',function(){
    $input = $(this).closest('.form-group').find(':input.f-show-password');
    if(typeof $input != undefined && $input.attr('type') == 'password') {
        $input.attr('type','text');
        $(this).removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
        $input.attr('type','password');
        $(this).removeClass('fa-eye').addClass('fa-eye-slash');
    }
});
function sweetAlert(title = 'Alert',text = 'alert text',type = 'success',attrData = {}) {
    var data = {title: title, text: text, type: type, showConfirmButton: false,timer: 2500};
    if(typeof attrData == "object") {
        var data = Object.assign(data, attrData);
    }
    
	Swal.fire(data)
}
function  xeditable(nRow) {
    $.fn.editable.defaults.mode = 'inline';
    $(nRow).editable({
        ajaxOptions: {
            type: 'put',
            dataType: 'json'
        },
        emptytext : "Select",
        params:function(params){
            var data = {};
            data[params.name] = params.value;
            data['src_ajax'] = $(this).attr('data-src_ajax');
            data['xeditable'] = "Yes";
            return data;
        },
        success: function(response, newValue) {
            if(response.status == 'success' || response.statusCode == '200') {
                sweetAlert('Success',response.message);
            } else {
                sweetAlert(response.status,response.message,'error');
            }
        }
    });
}
$(function () {
    $('body.hide').fadeIn(1000).removeClass('hide');
    $('.overlay').on('click',function(){
        $('body').find('#f-bg-gallery').remove();
        var type = $(this).attr('type');
        var htmlData = "";
        if(typeof type == "undefined" || type == "image") {
            htmlData = `<div id="f-bg-gallery"
                style="position: fixed;left: 0;right: 0;top: 0;bottom: 0;z-index: 9999;background-color: #000;display: flex;align-items: center;justify-content: center;">
                <img src="${this.src.replace("?s=350X350", "?s=full")}" height="100%" alt="">
                <i class="fa fa-times" style="position:fixed;top:20px;right:20px;font-size:25px;color:#fff;"></i>
            </div>`;
        }
        $('body').prepend(htmlData);
    });

    $('body').on('click','#f-bg-gallery .fa.fa-times',function(){
        $('body').find('#f-bg-gallery').remove();
    });
    
    $(".alert.alert-danger.alert-dismissable").fadeTo(90000, 500).slideUp(500, function(){
        $(".alert.alert-danger.alert-dismissable").slideUp(500);
    });
});
function ajax_form_notification(form,data,$refresh=true) {
    $(form).find('.alert,.error,is-invalid').remove();
    if(data.status == "validation_error" || data.status == '422') {
        var errors = [];
        if(isset(data.errors)) {
            errors = data.errors;
        } else {
            errors = data.responseJSON.errors;
        }
        $.each(errors,function(index, value){
            if($(form).find(':input[name='+index+']').not('[type="hidden"]').length == 0) {
                if($(form).find('.alert').length > 0) {
                    $(form).find('.alert').append(`<li><strong>${data.status}</strong> ${value}</li>`);
                } else {
                    $(form).prepend(`<div class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <li><strong>${data.status}</strong> ${value}</li>
                    </div>`);
                }
            } else {
                $(form).find(':input[name='+index+']').addClass('is-invalid');
                $(form).find(':input[name='+index+']').parent().append(`<span class="error invalid-feedback">${value[0]}</span>`)
            }
        });
        if(typeof Object.keys(errors)[0] != 'undefined') {
            $(form).find(':input[name='+Object.keys(errors)[0]+']').first().focus();
        }
    } else if(data.status == "exception_error") {
        $(form).append(`<div class="alert alert-danger m-3">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>errors! </strong>${data.errors}
        </div>`);
    } else if(data.status == "success" || data.status == "200") {
        sweetAlert(data.status,data.message);
        if($refresh) {
            location.reload();
        }
    } else if($refresh){
        $(form).append($(form).append(`<div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Danger!</strong>${JSON.stringify(data)}
        </div>`));
    }
    $(form).find('[type=submit]').attr('disabled', false);
}
function lodingBtn(btn) {
    if(typeof btn != "undefined" && btn) {
        text = btn.innerText;
        btn.setAttribute('disabled',true);
        btn.innerHTML = `<span class="">Loading </span><i class="fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i>`;
        return text;
    } else {
        console.log('loding btn undefind',btn);
    }
}
function stopLodingBtn(btn,text = "") {
    if(typeof btn != "undefined" && typeof text != "undefined" && btn && text) {
        btn.removeAttribute('disabled');
        btn.innerText = text;
    } else {
        console.log('loding btn',btn,'text',text);
    }
}
function base_url(url) {
    var bsurl = document.body.getAttribute('bsurl');
    if(bsurl) {
        return bsurl+'/'+url;
    } else {
        return "set base url in body tag";
    }
}