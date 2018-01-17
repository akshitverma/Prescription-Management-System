<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset() ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PMS</title>
	<?php echo $this->Html->meta('icon'); ?>

	<script type="text/javascript">var SITE_URL="<?php echo $this->Url->build('/',true);?>"</script>
	<script type="text/javascript">var home_url="<?php echo $this->Url->build('/',true);?>"</script>
	<script type="text/javascript">var current_controller="<?php echo $this->name;?>"</script>
	<script type="text/javascript">var controller ="<?php echo $this->request->params['controller']?>";</script>

	<?php
	echo $this->Html->css([
		'styles/vendors/bootstrap.min.css',
		'bootstrap-select/bootstrap-select.min.css',
		'admin_styles/css/admin_style.css',
		'admin_styles/css/form-builder.min.css',
		'admin_styles/css/form-render.min.css',
		'admin_styles/css/demo.css',
		'admin_styles/css/style.css',
		'lib/jquery.datetimepicker.min.css',
	]);

	// lib
	echo $this->Html->script([
		'lib/jquery.min.js',
		'lib/bootstrap.min.js',
		'lib/jquery.validate.min.js',
		'lib/jquery-ui.js',
		'lib/form_builder/form-builder.min.js',
		'lib/jquery.datetimepicker.min.js',
		'lib/admin_custom.js',
	]);
	?>
	
	<style>
		@media screen and (min-color-index:0) and(-webkit-min-device-pixel-ratio:0)
		{ @media {
			button{
				display:inline-block;
			}
		}}
	</style>
</head>

<body id="app" class="app off-canvas theme-four nav-expand">
<?php
	echo $this->element('header');
	echo $this->element('sidebar');
	echo $this->fetch('content');
	echo $this->element('footer');
?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		initDatePicker();
	});
</script>
</body>
</html>
