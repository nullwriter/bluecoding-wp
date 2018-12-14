<?php
//flush rewrite rules when we load this page!
flush_rewrite_rules();

$users = get_users();
?>
<div class="wrap">
	<h2><?php echo esc_html(get_admin_page_title()); ?></h2>

	<div class="mt-4 pl-4 pr-4">
        <table id="mwpu-user-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                    <tr data-id="<?php echo $user->ID ?>">
                        <td></td>
                        <td><?php echo $user->ID ?></td>
                        <td><?php echo $user->first_name ?></td>
                        <td><?php echo $user->last_name ?></td>
                        <td><?php echo $user->user_email ?></td>
                        <td><?php echo implode($user->roles, ",") ?></td>
                        <td>
                            <?php
                                $status = get_user_meta($user->ID, 'member_status', true);
                                echo ( (empty($status) || $status == 'active') ? 'active' : $status);
                            ?>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-secondary mwpu-action-link" id="mwpu-edit" href>edit</a>
                            <a class="btn btn-sm btn-warning mwpu-action-link" id="mwpu-change-status" href>
	                            <?php
                                    $status = get_user_meta($user->ID, 'member_status', true);
                                    echo ( (empty($status) || $status == 'active') ? 'deactivate' : 'activate');
	                            ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
