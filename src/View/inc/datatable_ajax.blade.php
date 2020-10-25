<script>
    jQuery(document).ready(function($) {
        // make the delete button work in the first result page
        register_delete_button_action();
    
        // make the delete button work on subsequent result pages
        $('.table.crudTable').on( 'draw.dt',   function () {
            register_delete_button_action();
        });
    
        function register_delete_button_action() {
            $('.content').on("[data-button-type=confirm_ajax]").unbind('click');
            // CRUD Delete
            // ask for confirmation before deleting an item
            $('.content').on('click','[data-button-type=confirm_ajax]',function(e) {
                e.preventDefault();
                var delete_button = $(this);
                var src_url = $(this).attr('src');
                var bsurl = $('body').attr('bsurl');
                Swal.fire({
                    title: $(this).attr('stlc-title'),
                    text: $(this).attr('stlc-text'),
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ trans("stlc.ok") }}',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: $(this).attr('href'),
                            type: $(this).attr('method'),
                            data:{src_ajax:true},
                            success: function(result) {
                                // Show an alert with the result
                                if(result.status == "200") {
                                    Swal.fire('success',result.message,"success");
                                    if(delete_button.parent().prop("tagName").toLowerCase() == 'td'){
                                        delete_button.parentsUntil('tr').parent().remove();
                                    }
                                    if(src_url != "reload") {
                                        window.location.href = bsurl+'/'+src_url;
                                    } else if(src_url == "reload") {
                                        window.location.reload();
                                    }
                                } else {
                                    Swal.fire('Warning',result.message,"warning");
                                }
                            },
                            error: function(result) {
                                if(result.status == "403" && typeof result.responseJSON.data != "undefined") {
                                    var html = "<p>"+result.responseJSON.message+"</p><table class='table'>";
                                    result.responseJSON.data.map((value,index)=>{
                                        html += `<tr><td>${value.key}<td><td>${value.value}<td></tr>`;
                                    });
                                    html += "</table>";
                                    console.log(html);
                                    Swal.fire({
                                        title:'Warning',
                                        text:result.responseJSON.message,
                                        type:"warning",
                                        html:html
                                    });
                                } else {
                                    Swal.fire('Error',result.responseJSON.message,"error");
                                }
                            }
                        });
                    } else {
                        Swal.fire("{{ trans('stlc.nothing_happened') }}");
                    }
                })
            });
        }
    });
</script>