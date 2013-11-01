<!DOCTYPE html>
<html lang="en">
<head>
<title>An error has occurred</title>
{include="common"}
</head>
<body class="error">
  <div>
    <header>
      <img src="{$asset_path}/{$logo_small_src}" alt="Logo for {$property_name}" />
      <h2>{$property_name}</h2>
      <h3>Unable to access the Internet</h3>
    </header>
    <section>
      <p>{include="error-text"}</p>
    </section>
    {include="footer"}
  </div>
</body>
</html>