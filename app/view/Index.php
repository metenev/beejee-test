<div class="container">
	<!-- Alerts -->
	<div class="alerts">
		<?php if ($alerts->has()): ?>
			<?php foreach ($alerts->get() as $alert): ?>
				<div class="alert <?php echo $alert['type_class']; ?>"><?php echo $alert['message']; ?></div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<h1 class="mb-4">Tasks</h1>
	<div class="toolbar d-flex align-items-center">
		<button class="btn btn-primary" data-toggle="modal" data-target="#task-create-modal">Create</button>
		<div class="sorting ml-auto">
			<form class="form-inline mb-0">
				<input type="hidden" name="page" value="<?php echo $page + 1; ?>" />
				<div class="form-group">
					<label class="my-1 mr-2" for="order-sort-field">Sort by</label>
					<select name="sort" id="order-sort-field" class="form-control">
						<?php foreach ($orderFields as $item): ?>
							<option value="<?php echo $item['key']; ?>"<?php if ($item['key'] == $selectedOrderField) echo 'selected="selected"'; ?>><?php echo $item['name']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<label class="sr-only" for="order-sort-dir">Sort direction</label>
					<select name="dir" id="order-sort-dir" class="form-control">
						<?php foreach ($orderDirections as $item): ?>
							<option value="<?php echo $item['key']; ?>"<?php if ($item['key'] == $selectedOrderDir) echo 'selected="selected"'; ?>><?php echo $item['name']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</form>
		</div>
	</div>
	<div class="tasks mt-3">
		<?php if (!empty($tasks)): ?>
			<?php foreach ($tasks as $task): ?>
				<div class="task card">
					<div class="card-body">
						<h6 class="card-subtitle text-muted mb-2 d-flex w-100 align-items-center">
							<?php echo $task->field('user_name'); ?>
							<span class="badge badge-<?php echo $task->field('status') == 'completed' ? 'success' : 'secondary'; ?> ml-2"><?php echo $task->field('status'); ?></span>
							<?php if ($task->field('edited_by_admin') == 1): ?>
								<small class="text-muted ml-auto font-weight-regular">edited by admin</small>
							<?php endif; ?>
						</h6>
						<small class="card-subtitle d-block text-muted mb-3"><?php echo $task->field('user_email'); ?></small>
						<p class="card-text"><?php echo $task->field('content'); ?></p>
						<?php if (isset($user)): ?>
							<button
								class="btn btn-outline-primary"
								data-toggle="modal"
								data-target="#task-edit-modal"
								data-task-id="<?php echo $task->field('id'); ?>"
								data-task-content="<?php echo $task->field('content'); ?>"
								data-task-user-name="<?php echo $task->field('user_name'); ?>"
								data-task-user-email="<?php echo $task->field('user_email'); ?>"
							>Edit</button>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="d-flex align-items-center justify-content-center pY-5">
				<div class="card">
					<div class="card-body">
						<p class="card-text text-muted">No tasks was created yet.</p>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php if ($pagesCount > 0): ?>
		<nav class="mt-5" aria-label="Pagination">
			<ul class="pagination">

				<?php if ($page > 0): ?>
					<li class="page-item">
						<a class="page-link" href="<?php echo $pagination->uri($page - 1); ?>">&lsaquo;</a>
					</li>
				<?php else: ?>
					<li class="page-item disabled">
						<a class="page-link" href="#">&lsaquo;</a>
					</li>
				<?php endif; ?>

				<?php for ($i = 0; $i < $pagesCount; $i++): ?>
					<li class="page-item<?php if ($i == $page) echo ' active'; ?>">
						<a class="page-link" href="<?php echo $pagination->uri($i); ?>"><?php echo $i + 1; ?></a>
					</li>
				<?php endfor; ?>

				<?php if ($page < ($pagesCount - 1)): ?>
					<li class="page-item">
						<a class="page-link" href="<?php echo $pagination->uri($page + 1); ?>">&rsaquo;</a>
					</li>
				<?php else: ?>
					<li class="page-item disabled">
						<a class="page-link" href="#">&rsaquo;</a>
					</li>
				<?php endif; ?>
			</ul>
		</nav>
	<?php endif; ?>
</div>

<!-- Modals -->

<div id="task-create-modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form action="<?php echo $url->get('/index/create'); ?>" method="POST">
				<?php $formSecurity->token($url->get('/index/create')); ?>

				<div class="modal-header">
					<h5 class="modal-title">Create task</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">✕</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="input-create-task-user-name">User name</label>
						<input name="user_name" id="input-create-task-user-name" type="text" class="form-control" placeholder="Enter your name" autofocus="" />
					</div>
					<div class="form-group">
						<label for="input-create-task-user-email">User email</label>
						<input name="user_email" id="input-create-task-user-email" type="text" class="form-control" placeholder="Enter your email" />
					</div>
					<div class="form-group">
						<label for="input-create-task-content">Content</label>
						<textarea name="content" id="input-create-task-content" class="form-control" placeholder="Type things you need to do..."></textarea>
					</div>
					<div class="alerts"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Create task</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="task-edit-modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form action="<?php echo $url->get('/index/edit'); ?>" method="POST">
				<?php $formSecurity->token($url->get('/index/edit')); ?>

				<input type="hidden" name="id" value="" />

				<div class="modal-header">
					<h5 class="modal-title">Edit task</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">✕</span>
					</button>
				</div>
				<div class="modal-body">
					<h6 class="task-user-name card-subtitle mb-2"><span class="text-muted">User:</span> <span class="value"></span></h6>
					<small class="task-user-email card-subtitle d-block mb-3"><span class="text-muted">E-mail:</span> <span class="value"></span></small>
					<div class="form-group">
						<label for="input-edit-task-content">Content</label>
						<textarea name="content" id="input-edit-task-content" class="form-control" placeholder="Type things you need to do..." autofocus=""></textarea>
					</div>
					<div class="form-group custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="input-edit-task-completed" name="completed" value="1" />
						<label class="custom-control-label" for="input-edit-task-completed">Task is completed</label>
					</div>
					<div class="alerts"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Update task</button>
				</div>
			</form>
		</div>
	</div>
</div>
