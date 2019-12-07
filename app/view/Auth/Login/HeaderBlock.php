<?php if (isset($user)): ?>
    <a class="btn btn-outline-primary" href="<?php echo $url->get('/auth/logout'); ?>"><span class="text-muted"><?php echo $user->field('login'); ?>:</span> Log out</a>
<?php else: ?>
    <a class="btn btn-outline-primary" href="<?php echo $url->get('/auth'); ?>">Log in</a>
<?php endif; ?>