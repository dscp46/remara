<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="/assets/brand/logo.svg" type="image/svg+xml" />
    <title><?= (!empty($title)) ? "{$APPNAME} - {$title}" : $APPNAME ?></title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" />
<?php
if( isset($custom_css) && is_array( $custom_css) )
{
	foreach( $custom_css as $css)
	{
?>
    <link href="<?= $css ?>" rel="stylesheet" />
<?php   } // foreach( $custom_css)
} // if( isset && is_array( $custom_css))
else {
?>
    <link href="/assets/css/default.css" rel="stylesheet" />
<?php } ?>
    <meta name="theme-color" content="#712cf9" />
  </head>
  <body>
<?php 
	
echo $this->render('navbar.php');

$selected_view = Base::instance()->get('content') ?? '_undefined_'; 
echo $this->render( file_exists($UI.$selected_view) ? $selected_view : 'default.tpl.php' );
?>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

