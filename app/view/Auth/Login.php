<div class="pY-5">
	<form class="form-signin" method="POST">
		<?php $formSecurity->token(); ?>

		<h1 class="h3 mb-3 font-weight-normal text-center">Please sign in</h1>

		<div class="form-group">
			<label for="input-login" class="sr-only">Login</label>
			<input type="text" id="input-login" name="login" class="form-control <?php if ($alerts->has('login')) echo 'is-invalid' ?>" value="<?php if (isset($data['login'])) echo $data['login']; ?>" placeholder="Login" autofocus="">
			<?php if ($alerts->has('login')): ?>
				<?php foreach ($alerts->get('login') as $alert): ?>
					<div class="invalid-feedback"><?php echo $alert['message']; ?></div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<div class="form-group">
			<label for="input-password" class="sr-only">Password</label>
			<input type="password" id="input-password" name="password" class="form-control <?php if ($alerts->has('password')) echo 'is-invalid' ?>" placeholder="Password">
			<?php if ($alerts->has('password')): ?>
				<?php foreach ($alerts->get('password') as $alert): ?>
					<div class="invalid-feedback"><?php echo $alert['message']; ?></div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<!-- Global errors -->
		<?php if ($alerts->has()): ?>
			<?php foreach ($alerts->get() as $alert): ?>
				<div class="alert <?php echo $alert['type_class']; ?>"><?php echo $alert['message']; ?></div>
			<?php endforeach; ?>
		<?php endif; ?>

		<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
		<p class="mt-5 mb-3 text-muted text-center">© BeeJee, 2019</p>
	</form>
</div>