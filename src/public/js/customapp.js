
function isset (variable) {
    if(typeof(variable) != "undefined" && variable !== null) {
        return true;
    }
    return false;
}
function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
if(window.location.hash != "") {
    $('a[href="' + window.location.hash + '"]').click()
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$("select").on("select2:close", function (e) {
    $(this).valid();
});

$.validator.setDefaults({
    ignore: ':disabled',
    errorClass: "error invalid-feedback",
    errorElement: "span",
    highlight: function(element) {
        if(isset($(element).parents('.form-group'))) {
            $(element).parents('.form-group').addClass("has-error");
            $(element).addClass('is-invalid');
        } else {
            $(element).addClass('is-invalid');
        }
    },
    unhighlight: function(element) {
        if(isset($(element).parents('.form-group'))) {
            $(element).parents('.form-group').removeClass("has-error");
            $(element).parents('.form-group').find('label.error').remove();
            $(element).parents('.form-group').find('.help-block').remove();
            $(element).removeClass('is-invalid');
        }
    },
    errorPlacement: function (error, element) {
        $(element).parents('.form-group').find('.help-block').remove();
        if (element.attr("type") == "checkbox") {
            error.insertAfter($(element).parents('.form-group').find(':last'));
        } else {
            error.insertAfter($(element).parents('.form-group').children().last());
        }
    },
    invalidHandler: function(event, validator) {
        var target = $(validator.errorList[0].element).parents('.tab-pane').attr('id');
        $(`[data-target='#${target}']`).click();
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
    // console.log(element.name+" - "+CKEDITOR.instances[element.name].getData());
    var editor = CKEDITOR.instances[element.name];
    var editor1 = CKEDITOR.instances[element.id];
    if (isset(editor) || isset(editor1)) {
        if(isset(editor)) {
            if(editor.getData() != null && editor.getData() != "") {
                // console.log(editor);
                return true;
            }
        } else if(isset(editor1)) {
            if(editor1.getData() != null && editor1.getData() != "") {
                // console.log(editor1);
                return true;
            }
        }
    }
    return false;
}, "This field is required.");

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
    var $from = $(element).parents('form');
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

$(function () {
    $('input[type="checkbox"], input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_square-orange',
        radioClass: 'icheckbox_square-orange',
        increaseArea: '10%' // optional
    });
    $(".alert.alert-danger.alert-dismissable").fadeTo(90000, 500).slideUp(500, function(){
        $(".alert.alert-danger.alert-dismissable").slideUp(500);
    });
});

$('button.btn.f-next-btn.btn-success').on('click', function() {
    $data = $(this).parents('.tab-pane').find(':input');
    if($data.valid()) {
        var classli = $(this).data('target');
        $('a[href="#' + classli + '"]').click();
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
                        row.child("<p class='error'>"+data.massage+"</p>").show();
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

$(':input.f-show-password+.fa').on('click',function(){
    $input = $(this).parent().find(':input.f-show-password');
    if(typeof $input != undefined && $input.attr('type') == 'password') {
        $input.attr('type','text');
        $(this).removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
        $input.attr('type','password');
        $(this).removeClass('fa-eye').addClass('fa-eye-slash');
    }
});
function sweetAlert(title = 'Alert',text = 'alert text',type = 'success') {
	Swal.fire({
		title: title,
		text: text,
		type: type,
		showConfirmButton: false,
		timer: 2500
	})
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
            // console.log(response);
            if(response.status == 'success' || response.statusCode == '200') {
                sweetAlert('Success',response.message);
            } else {
                sweetAlert(response.status,response.message,'error');
            }
        }
    });
}
$(document).ready(function () {
    $('body.hide').fadeIn(1000).removeClass('hide');
});
function ajax_form_notification(form,data,$refresh=true) {
    if(data.status == "validation_error" || data.status == '422') {
        var errors = [];
        if(isset(data.errors)) {
            errors = data.errors;
        } else {
            errors = data.responseJSON.errors;
        }
        $.each(errors,function(index, value){
            if($(form).find(':input[name='+index+']').not('[type="hidden"]').length == 0) {
                $(form).prepend(`<div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>${data.status}</strong> ${value}
                </div>`);
            } else {
                $(form).find(':input[name='+index+']').parents('.form-group').addClass('has-error').append(`<label class="error">${value}</label>`)
            }
        });
    } else if(data.status == "exception_error") {
        $(form).append(`<div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Danger!</strong>${data.errors}
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