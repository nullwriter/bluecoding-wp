(function( $ ) {
    'use strict';

    $( window ).load(function() {

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
        $('#mwpu-user-table').DataTable({
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
    });

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