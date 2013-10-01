

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>SSP Director test</title>

</head>

<body>
<h1>SSP API TESTER</h1>
<?php
  echo('<div>Initializing...</div>');
  include('classes/DirectorPHP.php');
  $director = new Director('local-e99bfa1df7c4df0f09adf61ba84078e2', 'uat.carsguide.com.au/ssp');

  echo('<div>Connected!</div>');

?>
</body>

</html>

