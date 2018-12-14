<?php
//flush rewrite rules when we load this page!
flush_rewrite_rules();

$users = get_users();
?>
<div class="wrap">
	<h2><?php echo esc_html(get_admin_page_title()); ?></h2>

	<div class="mt-4 pl-4 pr-4 d-flex flex-column">
        <div class="mb-4">
            <span>Bulk Action</span>
            <select id="mwpu-bulk-action">
                <option value="inactive">Deactivate</option>
                <option value="active">Activate</option>
            </select>
            <button id="btn-bulk-action" class="btn btn-sm btn-primary disabled" disabled>Submit</button>
            <small><span id="mwpu-quantity">0</span> selected</small>
        </div>
        <table id="mwpu-user-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th class="text-center"><input type="checkbox" class="selectAll" /> </th>
                    <th>ID</th>
                    <th>Name</th>
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
                        <td class="mwpu-display-name"><?php echo $user->display_name ?></td>
                        <td><?php echo $user->user_email ?></td>
                        <td><?php echo implode($user->roles, ",") ?></td>
                        <td class="mwpu-member-status">
                            <?php
                                $status = get_user_meta($user->ID, 'member_status', true);
                                echo ( (empty($status) || $status == 'active') ? 'active' : $status);
                            ?>
                        </td>
                        <td class="mwpu-actions">
                            <a class="btn btn-sm btn-secondary mwpu-action-link mwpu-edit" data-id="<?php echo $user->ID ?>">
                                edit
                            </a>
                            <a class="btn btn-sm btn-warning mwpu-action-link mwpu-change-status" data-id="<?php echo $user->ID ?>">
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
