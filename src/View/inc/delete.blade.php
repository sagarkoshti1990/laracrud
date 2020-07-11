<script>
    jQuery(document).ready(function($) {
        // make the delete button work in the first result page
        register_delete_button_action();
    
        // make the delete button work on subsequent result pages
        $('.table.crudTable').on( 'draw.dt',   function () {
            register_delete_button_action();
        });
    
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
                    text: "Are you sure you want to delete?",
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
                                        title: "Item Deleted",
                                        text: "The item has been deleted successfully.",
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
                                        text: result.message,
                                        type: "warning"
                                    });
                                }
                            },
                            error: function(result) {
                                // Show an alert with the result
                                Swal.fire({
                                    title: "Not deleted",
                                    text: "There's been an error. Your item might not have been deleted.",
                                    type: "warning"
                                });
                            }
                        });
                    } else {
                        Swal.fire(
                            "Not deleted",
                            "Nothing happened. Your item is safe.",
                        )
                    }
                })
            });
        }
    });
    </script>