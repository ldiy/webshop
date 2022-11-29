<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'Users - Admin']);
?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/sidebar.css'); ?>">
<link rel="stylesheet" href="<?php echo url('/resources/css/admin.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">
        <!-- Breadcrumb -->
        <?php template('breadcrumb', ['items' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Admin', 'url' => url('/admin')],
            ['name' => 'Users', 'url' => url('/admin/user')],
        ]]); ?>
        <div class="container d-flex flex-row mt-4">
            <?php template('admin-sidebar'); ?>
            <div class="flex-fill px-4">
                <h2>Users</h2>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">role</th>
                        <th scope="col" class="align-center">Number of orders</th>
                        <th scope="col" class="align-center">Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="">
                            <td><?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?></td>
                            <td><?php echo htmlspecialchars($user->email); ?></td>
                            <td>
                                <select class="form-select" id="role-select" onchange="updateRole(<?php echo $user->id; ?>)">
                                    <?php foreach ($availableRoles as $role): ?>
                                        <option value="<?php echo $role->id; ?>" <?php if ($role->id === $user->role_id) echo 'selected'; ?>><?php echo $role->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="align-center"><?php echo count($user->orders()); ?></td>
                            <td class="align-center">
                                <?php if($user->id !== auth()->user()->id): ?>
                                    <button class="btn btn-danger" onclick="deleteUser(<?php echo $user->id; ?>)"><i class="fa-solid fa-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!--  Footer  -->
    <?php template('footer'); ?>
</div>


<!-- Scripts -->
<?php template('bottomScripts'); ?>
<script>
    function updateRole(userId) {
        const select = document.getElementById('role-select');
        const roleId = select.value;
        const url = '<?php echo url('/admin/user/'); ?>' + userId;
        const data = {
            'role_id': roleId,
        };
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function (response) {
                console.log(response);
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    function deleteUser(userId) {
        const url = '<?php echo url('/admin/user/'); ?>' + userId;
        $.ajax({
            url: url,
            type: 'DELETE',
            success: function (response) {
                location.reload();
            },
            error: function (error) {
                console.log(error);
            }
        });
    }
</script>

</body>
</html>