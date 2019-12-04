<?php

/*

UI functions
--------------------------------------

1. UI functions
  1.1 h3();
  1.2 h2()
  1.3 label()
  1.4 submitBtn()
  1.5 button()
  1.6 inputText()
  1.7 inputSpecial()
  1.8 inputRestrict()
  1.9 inputOption()
  1.10 dynamicOption
  1.11 sideLink()
  1.12 sideBtn()
  1.13 accordion()

--------------------------------------

*/

//Required files
if(!defined('inc')) {
   header("Location: ../../login"); //die();
}


/*

1 UI functions

*/

class ui {

    public $data;

    //h3 tag
    public function h3($conn, $data) {

        $r = 1;
        $data = secure($conn, $data);

        if('true' == auth($conn, 1)) {
            echo('<h3>'.$data.'</h3>');
        }
    }

    //h2 tag
    public function h2($conn, $data) {

        $r = 1;
        $data = secure($conn, $data);

        if('true' == auth($conn, 1)) {
            echo('<h2>'.$data.'</h2>');
        }
    }

    //label
    public function label($conn, $data) {

        $r = 1;
        $data = secure($conn, $data);

        if('true' == auth($conn, 1)) {
            echo('<label>'.$data.'</label>');
        }
    }

    //submit button
    public function submitBtn($conn, $value, $class, $name) {

        $r = 1;
        $value = secure($conn, $value);
        $class = secure($conn, $class);
        $name = secure($conn, $name);

        if('true' == auth($conn, 1)) {
            echo('<input type="submit" class="'.$class.'" value="'.$value.'" name="'.$name.'">');
        }
    }

    //red button
    public function button($conn, $value, $class, $name, $type, $event) {

        $r = 1;
        $value = secure($conn, $value);
        $class = secure($conn, $class);
        $name = secure($conn, $name);
        $type = secure($conn, $type);

        if('true' == auth($conn, 1)) {
            echo('<button type="'.$type.'" class="'.$class.'" name="'.$name.'" value="'.$value.'" '.$event.'>'.$name.'</button>');
        }
    }


    //input text
    public function inputText($conn, $value, $name, $type, $placeholder) {

        $r = 1;

        if ('email' != $name) {
            $value = sanitize(encrypt($conn, 1, $value));
            $name = sanitize(encrypt($conn, 1, $name));
            $type = sanitize(encrypt($conn, 1, $type));
            $placeholder = sanitize(encrypt($conn, 1, $placeholder));
        }

        if('readonly' == $name) {
          $readonly = 'style="background-color:#cccccc;" readonly';
        }

        if('true' == auth($conn, 1)) {
            echo('<input type="'.$type.'" value="'.$value.'" name="'.$name.'"  placeholder="'.$placeholder.'" id="'.$name.'" '.$readonly.'>');
        }
    }


    //input text
    public function inputSpecial($conn, $value, $name, $type, $placeholder) {

        $r = 1;

        if ('email' != $name) {
            $value = encrypt($conn, 1, $value);
            $name = sanitize(encrypt($conn, 1, $name));
            $type = sanitize(encrypt($conn, 1, $type));
            $placeholder = sanitize(encrypt($conn, 1, $placeholder));
        }

        if('readonly' == $name) {
          $readonly = 'style="background-color:#cccccc;" readonly';
        }

        if('true' == auth($conn, 1)) {
            echo('<input type="'.$type.'" value="'.$value.'" name="'.$name.'"  placeholder="'.$placeholder.'" id="'.$name.'" '.$readonly.'>');
        }
    }


    //input text
    public function arrayText($conn, $value, $name, $type, $placeholder) {

        $r = 1;

        if ('email' != $name) {
            $value = str_replace(',', ', ',encrypt($conn, 1, $value));
            $name = sanitize(encrypt($conn, 1, $name));
            $type = sanitize(encrypt($conn, 1, $type));
            $placeholder = sanitize(encrypt($conn, 1, $placeholder));
        }

        if('readonly' == $name) {
          $readonly = 'style="background-color:#cccccc;" readonly';
        }

        if('true' == auth($conn, 1)) {
            echo('<input type="'.$type.'" value="'.$value.'" name="'.$name.'"  placeholder="'.$placeholder.'" id="'.$name.'" '.$readonly.'>');
        }
    }

    // input text restricted
    public function inputRestrict($conn, $value, $name, $type) {

        $r = 1;
        $value = sanitize(encrypt($conn, 1, $value));
        $name = sanitize(encrypt($conn, 1, $name));
        $restrict = restrictform($conn, $admin, $username, 1);

        if('true' == auth($conn, 1)) {
            echo('<input type="'.$type.'" value="'.$value.'" name="'.$name.'" id="'.$name.'" '.$restrict.'>');
        }
    }

    //input opntions
    public function inputOption($conn, $value) {

        $r = 1;
        $value = secure($conn, $value);

        if('true' == auth($conn, 1)) {
            echo('<option value="'.$value.'">'.$value.'</option>');
        }
    }


    //dynamic input opntions
    public function dynamicOption($conn, $value) {
      $r = 1;
      $value = sanitize(scandir($value));

        foreach ($value as $directory) {
          if ($directory === '.' or $directory === '..') continue;

            if('true' == auth($conn, 1)) {
                echo('<option value="'.$directory.'">'.str_replace('_', ' ', $directory).'</option>');
            }
        }
    }


    //side links
    public function sideLink($conn, $url, $name, $icon, $class) {

        $r = 1;
        $url = secure($conn, $url);
        $name = secure($conn, $name);
        $icon = secure($conn, $icon);

        if( '' == $class) {
          $class = 'dir';
        }else {
          $class = secure($conn, $class);
        }

        if('true' == auth($conn, 1)) {
            echo('<li class="'.$class.'"><a href="'.$url.'" class="slink"><i class="'.$icon.'"></i>'.$name.'</a></li>');
        }
    }

    //side button
    public function sideBtn($conn, $url, $name, $icon, $class) {

        $r = 1;
        $url = secure($conn, $url);
        $name = secure($conn, $name);
        $icon = secure($conn, $icon);

        if( '' == $class) {
          $class = 'dir';
        }else {
          $class = secure($conn, $class);
        }

        if('true' == auth($conn, 1)) {
            echo('<a class="'.$class.'" href="'.$url.'"><i class="'.$icon.'"></i>'.$name.'</a></li>');
        }
    }

    //accordion
    public function accordion($conn, $data, $name, $icon) {

      $r = 1;
      $data = secure($conn, $data);
      $name = secure($conn, $name);
      $icon = secure($conn, $icon);


      if('true' == auth($conn, 1)) {

        echo'<button class="accordion" onclick="accordion(this);"><i class="'.$icon.'" aria-hidden="true"></i>'.$name.'</button>';
          echo '<div class="panel">';
              if (!ismobile()) {
                  $data;
              }
          echo '</div>';

        }
    }


}

?>
