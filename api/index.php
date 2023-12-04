<?php
  function getValue($key){
    return isset($_GET[$key]) ? $_GET[$key] : 5;
  }

  $numberInput = getValue("numberInput");
  $numberValid = false;
  $typeValid = false;

  // test if the type isn't number;
  if (is_numeric($numberInput) == true) {
    $typeValid = true;
  }

  // test if it's odd number
  if ($numberInput % 2 != 0 && $numberInput >= 3) {
    $numberValid = true;
  } 

  function DrawLine($number)
  {
    $pattern = "";

    for ($i = 0; $i < $number; $i++) {
      $pattern .= "*"; 
    }

    return $pattern;
  }

  function DrawStripedLine($number)
  {
    $pattern = "";

    for ($i = 0; $i < $number; $i++) {
      if ($i % 2 == 0) {
        $pattern .= "*";
      } else {
        $pattern .= "_";
      }
    }
    
    return $pattern;
  }

  function DrawSquare($number)
  {
    $pattern = "";

    for ($i = 0; $i < $number; $i++) {
      for ($j = 0; $j < $number; $j++) {
        $pattern .= "*";
      }
      $pattern .= "<br>";
    }

    return $pattern;
  }

  function DrawParallelogram($number)
  {
    $pattern = "";

    for ($i = 0; $i < $number; $i++) {
      for ($j = $number; $j > $i + 1; $j--) {
        $pattern .= "_";
      }
      for ($j = $number; $j > 0; $j--) {
        $pattern .= "*";
      }
      for ($j = 0; $j <= $i - 1; $j++) {
        $pattern .= "_";
      }
      $pattern .= "<br>";
    }

    return $pattern;
  }

  function DrawTriangle($number)
  {
    $pattern = "";

    for ($i = 0; $i < $number; $i++) {
      for ($j = $number; $j > $i; $j--) {
        $pattern .= "*";
      }
      for ($j = 0; $j <= $i - 1; $j++) {
        $pattern .= "_";
      }
      $pattern .= "<br>";
    }

    return $pattern;
  }

  function DrawReverseTriangle($number)
  {
    $pattern = "";

    for ($i = 0; $i < $number; $i++) {
      for ($j = 0; $j <= $i; $j++) {
        $pattern .= "*";
      }
      for ($j = $number; $j > $i; $j--) {
        $pattern .= "_";
      }
      $pattern .= "<br>";
    }
    
    return $pattern;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shape Drawing</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@2.0.2-alpha/dist/css/materialize.min.css">
</head>
<body>
  <form method="get" style="padding: 10px 0px; text-align: center;" class="col s12">
    <div class="row" style="margin: 30px 0; display: flex; justify-content: center;">
      <div class="input-field outlined" style="padding: 0px 10px; width: 200px;">
        <input type="number" name="numberInput" value='<?php echo $numberInput?>' min="3" placeholder=" " class="validate" />
        <label>Number:</label>
        <?php if($numberValid == false): ?>
          <p style="margin-top: 10px">
            Number must be odd!
          </p>
        <br/>
        <?php endif;  ?>
        <?php if($typeValid == false): ?>
          <p style="margin-top: 10px">
            Input is not a number / empty!
          </p>
        <br/>
        <?php endif; ?>
        <button type="submit" class="btn" style="margin-top: 10px; margin-left: auto; font-weight: bold;">DRAW</button>
      </div>
    </div>
    <?php if($numberValid == true && $typeValid == true): ?>
      <div class="row">
        <div class="col s12 m6 l4" style="padding: 0px 10px">
          <p style="font-weight: bold" class="bold">Line:</p style="font-weight: bold">
          <div style="padding: 20px 10px">
            <?php echo DrawLine($numberInput); ?>
          </div>
        </div>

        <div class="col s12 m6 l4" style="padding: 0px 10px">
          <p style="font-weight: bold">StripedLine:</p style="font-weight: bold">
          <div style="padding: 20px 10px">
            <?php echo DrawStripedLine($numberInput); ?>
          </div>
        </div>

        <div class="col s12 m6 l4" style="padding: 0px 10px">
          <p style="font-weight: bold">Square:</p style="font-weight: bold">
          <div style="padding: 20px 10px">
            <?php echo DrawSquare($numberInput); ?>
          </div>
        </div>

        <div class="col s12 m6 l4" style="padding: 0px 10px">
          <p style="font-weight: bold">Parallelogram:</p style="font-weight: bold">
          <div style="padding: 20px 10px">
            <?php echo DrawParallelogram($numberInput) ?>
          </div>
        </div>

        <div class="col s12 m6 l4" style="padding: 0px 10px">
          <p style="font-weight: bold">Triangle:</p style="font-weight: bold">
          <div style="padding: 20px 10px">
            <?php echo DrawTriangle($numberInput) ?>
          </div>
        </div>

        <div class="col s12 m6 l4" style="padding: 0 10px">
          <p style="font-weight: bold">Reverse Triangle</p style="font-weight: bold">
          <div style="padding: 20px 10px">
            <?php echo DrawReverseTriangle($numberInput) ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </form>
</body>
</html>
