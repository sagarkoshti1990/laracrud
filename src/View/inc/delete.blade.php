<script>
    jQuery(document).ready(function($) {
        // make the delete button work in the first result page
        register_delete_button_action();
    
        // make the delete button work on subsequent result pages
        $('.table.crudTable').on( 'draw.dt',   function () {
            register_delete_button_action();
        } ).dataTable();
    
        function register_delete_button_action() {
            $('.content').on("[data-button-type=delete]").unbind('click');
            // CRUD Delete
            // ask for confirmation before deleting an item
            $('.content').on('click','[data-button-type=delete]',function(e) {
                
                e.preventDefault();
                var delete_button = $(this);
                var delete_url = $(this).attr('href');
                var src_url = $(this).attr('src');
                var bsurl = $('body').attr('bsurl');
                Swal.fire({
                    title: "Confirmation",
                    text: "{{ trans('crud.delete_confirm') }}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: delete_url,
                            type: 'DELETE',
                            data:{src_ajax:true},
                            success: function(result) {
                                // Show an alert with the result
                                if(result.status == "success") {
                                    Swal.fire({
                                        title: "{{ trans('crud.delete_confirmation_title') }}",
                                        text: "{{ trans('crud.delete_confirmation_message') }}",
                                        type: "success"
                                    });
                                    // delete the row from the table
                                    // delete_button.parentsUntil('tr').parent().remove();
                                    if(src_url != "reload") {
                                        window.location.href = bsurl+'/'+src_url;
                                    } else {
                                        window.location.reload();
                                    }
                                } else {
                                    Swal.fire({
                                        title: result.status,
                                        text: result.massage,
                                        type: "warning"
                                    });
                                }
                            },
                            error: function(result) {
                                // Show an alert with the result
                                Swal.fire({
                                    title: "{{ trans('crud.delete_confirmation_not_title') }}",
                                    text: "{{ trans('crud.delete_confirmation_not_message') }}",
                                    type: "warning"
                                });
                            }
                        });
                    } else {
                        Swal.fire(
                            "{{ trans('crud.delete_confirmation_not_deleted_title') }}",
                            "{{ trans('crud.delete_confirmation_not_deleted_message') }}",
                        )
                    }
                })
                // (new PNotify({
                //     title: "Confirmation",
                //     text: "{{ trans('crud.delete_confirm') }}",
                //     icon: 'glyphicon glyphicon-question-sign',
                //     hide: false,
                //     type: "warning",
                //     confirm: {
                //         confirm: true
                //     },
                //     buttons: {
                //         closer: false,
                //         sticker: false
                //     },
                //     history: {
                //         history: false
                //     },
                //     addclass: 'stack-modal',
                //     stack: {'dir1': 'down', 'dir2': 'right', 'modal': true}
                // })).get().on('pnotify.confirm', function() {
                    
                // }).on('pnotify.cancel', function() {
                //     new PNotify({
                //         title: "{{ trans('crud.delete_confirmation_not_deleted_title') }}",
                //         text: "{{ trans('crud.delete_confirmation_not_deleted_message') }}",
                //         type: "info"
                //     });
                // });
            });
        }
    });
    </script>