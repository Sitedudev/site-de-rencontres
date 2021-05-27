<link href="<?= URL ?>css/jquery-ui.theme.min.css" rel="stylesheet" type="text/css"/>
<link href="<?= URL ?>css/jquery-ui.structure.min.css" rel="stylesheet" type="text/css"/>
<link href="<?= URL ?>css/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
<link href="<?= URL ?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?= URL ?>css/style.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<?php
if(isset($_SESSION['theme']) && $_SESSION['theme'] <> 0){
	switch($_SESSION['theme']){
		case 1:
			$css_theme = "blue_theme";
		break;
		case 2:
			$css_theme = "dark_theme";
		break;
		case 3:
			$css_theme = "sobre_theme";
		break;
	}
?>
<link href="<?= URL ?>css/<?= $css_theme ?>.css" rel="stylesheet" />
<?php	
}
?>