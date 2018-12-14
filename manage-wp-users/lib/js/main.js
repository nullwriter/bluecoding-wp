(function( $ ) {
    'use strict';

    $( window ).load(function() {

        /**
         * Event to change status in bulk
         */
        $('#btn-bulk-action').on('click', function(){
            var selectedIds = [];
            var status = $('#mwpu-bulk-action').val();

            $('tr.selected').each(function(){
                selectedIds.push($(this).data('id'));
            });

            sendBulkAction(selectedIds, status);
        });

        /**
         * Event to update selected for bulk action
         */
        $(document).on('click', 'td.select-checkbox', function(){
            updateSelectedCount();
        });

        /**
         * Event to edit display name for user
         */
        $('.mwpu-edit').on('click', function(){
            var user_id = $(this).data('id');
            var currentNameField = $(this).parent().parent().children('.mwpu-display-name');

            alertify.prompt(
                'Edit Name',
                'Type a new name',
                currentNameField.text(),
                function(evt, value) {
                    var newName = value.trim();

                    if (!newName) {
                        alertify.error('Please type a valid name.');
                    } else {
                        updateMemberDisplayName(user_id, newName, currentNameField);
                    }
                },
                function() {
                    alertify.error('Cancelled')
                }
            );
        });

        /**
         * Event to change single member status
         */
        $('.mwpu-change-status').on('click', function(){
            var user_id = $(this).data('id');
            var button = $(this);
            var memberStatusField = $(this).parent().parent().children('.mwpu-member-status');

            changeMemberStatus(user_id, memberStatusField, button);
        });

        /**
         * Initialize datatable
         */
        var DT = $('#mwpu-user-table').DataTable({
            'columnDefs': [
                {
                    'targets': 0,
                    'orderable': false,
                    'className': 'select-checkbox',
                    'checkboxes': {
                        'selectRow': true
                    }
                },
                {
                    'targets': 6,
                    'orderable': false
                }
            ],
            'select': {
                'style': 'multi',
                'selector': 'td:first-child'
            },
            order: [[ 1, 'asc' ]]
        });

        $(document).on('click', '.selectAll', function(e) {
            if ($(this).is( ":checked" )) {
                DT.rows(  ).select();
            } else {
                DT.rows(  ).deselect();
            }

            updateSelectedCount();
        });
    });

    /**
     * Updates the selected count in the Bulk Action label
     */
    function updateSelectedCount() {
        var quantity = $('tr.selected').length;
        $('#mwpu-quantity').text(quantity);

        if (quantity <= 0) {
            $('#btn-bulk-action').addClass('disabled').prop("disabled",true);
        } else {
            $('#btn-bulk-action').removeClass('disabled').prop("disabled",false);
        }
    }

    /**
     * Ajax to change status in bulk
     *
     * @param selectedIds
     * @param status
     */
    function sendBulkAction(selectedIds, status) {
        $.ajax({
            url : admin_url.ajax_url,
            type : 'post',
            data : {
                'action' : 'changeMemberStatusInBulk',
                'selected_ids[]': selectedIds,
                'status': status
            },
            success : function( response ) {
                response = JSON.parse(response);

                if (response.result) {
                    alertify.success(
                        'Member status has been changed to ' +
                        response.status.toUpperCase() +
                        ' for ' + response.count + ' users.'
                    );

                    for(var i in selectedIds) {
                        var trParent = $('tr[data-id="'+selectedIds[i]+'"]');
                        var memberStatusField = trParent.children('.mwpu-member-status');
                        var button = trParent.children('.mwpu-actions').children('.mwpu-change-status');

                        // Optional: remove selected and update count
                        trParent.removeClass('selected');
                        updateSelectedCount();

                        // Update button and status labels for each user updated
                        updateLabels(response.status, memberStatusField, button);
                    }

                    $('.selectAll').attr('checked', false);

                } else {
                    alertify.error('Something went wrong!');
                }
            },
            error: function (xhr) {
                alertify.error('Something went wrong: '+xhr);
                console.log(xhr);
            }
        });
    }

    /**
     * Ajax to change specific member status
     *
     * @param user_id
     * @param memberStatusField
     * @param button
     */
    function changeMemberStatus(user_id, memberStatusField, button) {
        $.ajax({
            url : admin_url.ajax_url,
            type : 'post',
            data : {
                action : 'changeMemberStatus',
                user_id: user_id
            },
            success : function( response ) {
                response = JSON.parse(response);

                if (response.result) {
                    alertify.success('User status has been updated successfully to: ' + response.status.toUpperCase());
                    updateLabels(response.status, memberStatusField, button);
                } else {
                    alertify.error('Something went wrong!');
                }
            },
            error: function (xhr) {
                alertify.error('Something went wrong: '+xhr);
                console.log(xhr);
            }
        });
    }

    /**
     * Ajax to edit users display name
     * @param user_id
     * @param name
     * @param nameField
     */
    function updateMemberDisplayName(user_id, name, nameField) {
        $.ajax({
            url : admin_url.ajax_url,
            type : 'post',
            data : {
                action : 'updateMemberDisplayName',
                user_id: user_id,
                name: name
            },
            success : function( response ) {
                response = JSON.parse(response);

                if (response.result) {
                    alertify.success('User name has been updated successfully to: ' + response.name);
                    updateLabels(response.name, nameField);
                } else {
                    alertify.error('Something went wrong!');
                }
            },
            error: function (xhr) {
                alertify.error('Something went wrong: '+xhr);
                console.log(xhr);
            }
        });
    }

    /**
     * Updates labels of a field in the list, optionally updates the status button if available as parameter
     *
     * @param newStatus
     * @param statusField
     * @param button
     */
    function updateLabels(newStatus, statusField, button) {

        statusField.text(newStatus);

        // update button label
        if (button) {
            if (newStatus === 'active') {
                button.text('deactivate');
            } else {
                button.text('activate');
            }
        }
    }

})( jQuery );