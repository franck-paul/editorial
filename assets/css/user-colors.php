<?php
header("Content-type: text/css; charset=UTF-8");

if (!empty($_GET['main_color']))
{
  $main_color = '#'.$_GET['main_color'];
}

?>
a {
  color: <?php echo $main_color; ?>;
}

a:hover {
  border-bottom-color: <?php echo $main_color; ?>;
  color: <?php echo $main_color; ?> !important;
}

header.major > :last-child {
  border-bottom: solid 3px <?php echo $main_color; ?>;
}
input[type="text"]:focus,
input[type="password"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
input[type="search"]:focus,
input[type="url"]:focus,
select:focus,
textarea:focus {
  border-color: <?php echo $main_color; ?>;
  box-shadow: 0 0 0 1px <?php echo $main_color; ?>;
}

input[type="checkbox"]:focus + label:before,
input[type="radio"]:focus + label:before {
  border-color: <?php echo $main_color; ?>;
  box-shadow: 0 0 0 1px <?php echo $main_color; ?>;
}

ul.contact li:before {
  color: <?php echo $main_color; ?>;
}

ul.pagination li > .page.active {
  background-color: <?php echo $main_color; ?>;
}

/* Button */
input[type="submit"],
input[type="reset"],
input[type="button"],
button,
.button {
  box-shadow: inset 0 0 0 2px <?php echo $main_color; ?>;
  color: <?php echo $main_color; ?> !important;
}

input[type="submit"].primary,
input[type="reset"].primary,
input[type="button"].primary,
button.primary,
.button.primary {
  background-color: <?php echo $main_color; ?>;
}

.features article .icon:before {
  color: <?php echo $main_color; ?>;
}

#header {
  border-bottom: solid 5px <?php echo $main_color; ?>;
}

#menu ul a:hover, #menu ul span:hover {
  color: <?php echo $main_color; ?>;
}

#menu ul a.opener:hover:before, #menu ul span.opener:hover:before {
  color: <?php echo $main_color; ?>;
}

.widget {
  border-bottom: 1px solid <?php echo $main_color; ?>;
}

#sidebar .toggle:focus,
a:focus {
  outline: <?php echo $main_color; ?> dotted 1px;
}

#accessconfig button:hover {
  background: none;
  color: <?php echo $main_color; ?> !important;
}
