<?php

return [

    /*
    |--------------------------------------------------------------------------
    | lara Crud Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    // Forms
    'save_action_save_and_new' => 'Save and new item',
    'save_action_save_and_edit' => 'Save and edit this item',
    'save_action_save_and_back' => 'Save and back',
    'save_action_changed_notification' => 'Default behaviour after saving has been changed.',

    // Create form
    'add'                 => 'Add',
    'public'              => 'Public',
    'quick_add'           => 'Quick Add',
    'back_to_all'         => 'Back to all ',
    'cancel'              => 'Cancel',
    'add_a_new'           => 'Add a New ',
    'import'              => 'Import',

    // Edit form
    'edit'                 => 'Edit',
    'save'                 => 'Save',
    'data_not_found'         => 'Data Not found',

    // Revisions
    'revisions'            => 'Revisions',
    'no_revisions'         => 'No revisions found',
    'created_this'         => 'created this',
    'changed_the'          => 'changed the',
    'restore_this_value'   => 'Restore this value',
    'from'                 => 'from',
    'to'                   => 'to',
    'undo'                 => 'Undo',
    'revision_restored'    => 'Revision successfully restored',
    'guest_user'           => 'Guest User',

    // Translatable models
    'edit_translations' => 'EDIT TRANSLATIONS',
    'language'          => 'Language',

    // CRUD table view
    'all'                       => 'All ',
    'in_the_database'           => 'in the database',
    'list'                      => 'List',
    'actions'                   => 'Actions',
    'preview'                   => 'Preview',
    'clone'                     => 'Clone',
    'delete'                    => 'Delete',
    'deleted'                   => 'Deleted',
    'permanently_delete'        => 'Permanently Deleted',
    'restore'                   => 'Restore',
    'restored'                  => 'Restored',
    'admin'                     => 'Admin',
    'logs'                      => 'Logs',
    'access'                    => 'Access',
    'details'                   => 'Details',
    'information'               => 'Information',
    'dashboard'                 => 'Dashboard',
    'details_row'               => 'This is the details row. Modify as you please.',
    'details_row_loading_error' => 'There was an error loading the details. Please retry.',

    // Confirmation messages and bubbles
    'delete_confirm'       => 'Delete Confirmation?',
    'delete_confirm_text'  => 'Are you sure you want to delete?',

    'restore_confirm'      => 'Restore Confirmation?',
    'restore_confirm_text' => 'Are you sure you want to restore?',

    'permanently_delete_confirm'      => 'Permanently delete Confirmation?',
    'permanently_delete_confirm_text' => 'Are you sure you want to Permanently delete?',

    'nothing_happened'     => 'Nothing happened.',

    // DataTables translation
    'emptyTable'     => 'No data available in table',
    'info'           => 'Showing _START_ to _END_ of _TOTAL_ entries',
    'infoEmpty'      => 'Showing 0 to 0 of 0 entries',
    'infoFiltered'   => '(filtered from _MAX_ total entries)',
    'infoPostFix'    => '',
    'thousands'      => ',',
    'lengthMenu'     => '_MENU_ records per page',
    'loadingRecords' => 'Loading...',
    'processing'     => 'Processing...',
    'search'         => 'Search: ',
    'zeroRecords'    => 'No matching records found',
    'paginate'       => [
        'first'    => 'First',
        'last'     => 'Last',
        'next'     => 'Next',
        'previous' => 'Previous',
    ],
    'aria' => [
        'sortAscending'  => ': activate to sort column ascending',
        'sortDescending' => ': activate to sort column descending',
    ],

    // global crud - errors
    'unauthorized_access' => 'Unauthorized access - you do not have the necessary permissions to see this page.',
    'please_fix' => 'Please fix error.',

    // global crud - success / error notification bubbles
    'insert_success' => 'The item has been added successfully.',
    'update_success' => 'The item has been modified successfully.',
    'delete_success' => 'The item has been deleted successfully.',
    'restore_success' => 'The item has been restored successfully.',
    'delete_dependency_success' => 'Dependency delete first.',

    // CRUD reorder view
    'reorder'                      => 'Reorder',
    'reorder_text'                 => 'Use drag&drop to reorder.',
    'reorder_success_title'        => 'Done',
    'reorder_success_message'      => 'Your order has been saved.',
    'reorder_error_title'          => 'Error',
    'reorder_error_message'        => 'Your order has not been saved.',

    // CRUD yes/no
    'yes' => 'Yes',
    'no' => 'No',
    'ok' => 'Ok',

    // CRUD filters navbar view
    'filters' => 'Filters',
    'toggle_filters' => 'Toggle filters',
    'remove_filters' => 'Remove filters',

    // account
    'my_account' => 'My Account',
    'change_password' => 'Change Password',
    'update_account_info' => 'Update Account Info',

    // Fields
    'none_value' => 'None',
    'browse_uploads' => 'Browse uploads',
    'clear' => 'Clear',
    'page_link' => 'Page link',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Internal link',
    'internal_link_placeholder' => 'Internal slug. Ex: \'admin/page\' (no quotes) for \':url\'',
    'external_link' => 'External link',
    'choose_file' => 'Choose file',

    //Table field
    'table_cant_add' => 'Cannot add new :entity',
    'table_max_reached' => 'Maximum number of :max reached',

    // File manager
    'file_manager' => 'File Manager',
    'file_size_text' => 'file size Maximum 100mb allowed',
    'file_size_titel' => 'file size limit',
];
