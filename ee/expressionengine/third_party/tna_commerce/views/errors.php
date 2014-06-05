<?
    if (isset($errors)) {
        if (count($errors)) { 
            echo '<div class="alert alert-danger lead">';
        } 

        foreach ($errors as $error) {
            echo "<p><strong>Oops, there's a problem!</strong> $error</p>\n";
        }

        if (count($errors)) { 
            echo '</div>';
        }
    }
  
?>