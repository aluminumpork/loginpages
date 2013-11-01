<!DOCTYPE html>
<html lang="en">
<head>
  {include="common"}
  <title>Login for access</title>
</head>
<body class="login">
<div>
  <header>
    <div><img src="{$asset_path}/{$logo_small_src}" alt="Logo for {$property_name}" /></div>
    <h2>{$property_name}</h2>
    <h3>Login for access</h3>
    <div style="clear: both"></div>
  </header>
  <section>
    <form name="tos" method="POST" action="{$link_login}">
      <label for="password">Please enter your access code</label>
      <input type="text" id="password" name="password" value="" />
      <input type="hidden" name="username" value="authuser" />
      <input type="hidden" name="dst" value="{$link_orig}" />
      <input type="hidden" name="domain" value="" />
      <div>
        <input type="submit" name="login" value="Login" />
      </div>
      <small>You should have been provided with an access code at check in.</small>
    </form>
  </section>
  {include="footer"}
</div>
</body>
</html>
