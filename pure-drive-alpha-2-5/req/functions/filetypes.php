<?php

// ODT files
if ($file_type == 'application/vnd.oasis.opendocument.text') {
    $file_type = 'text/odt';
}

// ODS files
if ($file_type == 'application/vnd.oasis.opendocument.spreadsheet') {
    $file_type = 'text/ods';
}
    
?>
