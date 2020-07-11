<script>
    jQuery(document).ready(function($) {
        // make the restore button work in the first result page
        register_restore_button_action();
    
        // make the restore button work on subsequent result pages
        $('.table.crudTable').on( 'draw.dt',   function () {
            register_restore_button_action();
        } ).dataTable();
    
        function register_restore_button_action() {
            $('.content').on("[data-button-type=restore]").unbind('click');
            // CRUD restore
            // ask for confirmation before deleting an item
            $('.content').on('click',"[data-button-type=restore]",function(e) {
                
                e.preventDefault();
                var restore_button = $(this);
                var restore_url = $(this).attr('href');
    
                (new PNotify({
                    title: "Confirmation",
                    text: "{{ trans('crud.restore_confirm') }}",
                    icon: 'glyphicon glyphicon-question-sign',
                    hide: false,
                    type: "warning",
                    confirm: {
                        confirm: true
                    },
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    history: {
                        history: false
                    },
                    addclass: 'stack-modal',
                    stack: {'dir1': 'down', 'dir2': 'right', 'modal': true}
                })).get().on('pnotify.confirm', function() {
                    $.ajax({
                        url: restore_url,
                        type: 'post',
                        data:{src_ajax:true},
                        success: function(result) {
                            // Show an alert with the result
                            if(result.status == "success") {
                                new PNotify({
                                    title: "{{ trans('crud.restore_confirmation_title') }}",
                                    text: "{{ trans('crud.restore_confirmation_message') }}",
                                    type: "success"
                                });
                                // restore the row from the table
                                // restore_button.parentsUntil('tr').parent().remove();
                                // if(restore_button.attr('data-refresh_page') == "true") {
                                    location.reload();
                                // }
                            } else {
                                new PNotify({
                                    title: result.status,
                                    text: result.message,
                                    type: "warning"
                                });
                            }
                        },
                        error: function(result) {
                            // Show an alert with the result
                            new PNotify({
                                title: "{{ trans('crud.restore_confirmation_not_title') }}",
                                text: "{{ trans('crud.restore_confirmation_not_message') }}",
                                type: "warning"
                            });
                        }
                    });
                }).on('pnotify.cancel', function() {
                    new PNotify({
                        title: "{{ trans('crud.restore_confirmation_not_restored_title') }}",
                        text: "{{ trans('crud.restore_confirmation_not_restored_message') }}",
                        type: "info"
                    });
                });
            });
        }
    });
    </script>