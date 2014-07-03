<!DOCTYPE html>
<html lang="en">
<head>
  {include="common"}
  <title>Please wait... logging you in</title>
  <script src="{$asset_path}/form-redirect.js"></script>
</head>
<body class="login">
<div>
  <header>
    <h3>Please wait while your connection is activated</h3>
    <div style="clear: both"></div>
  </header>
  <form name="tos" method="POST" action="{$link_login}">
    <input type="hidden" name="password" value="{$access_code_pass}" />
    <input type="hidden" name="username" value="{$access_code_user}" />
    <input type="hidden" name="dst" value="{$link_orig}" />
    <input type="hidden" name="domain" value="" />
    <div>
      <p>Click the button below if you are not automatically connected.</p>
      <input type="submit" value="Connect" />
    </div>
  </form>
  <script>
  var formParams = {$json_redirect_params};
  ;(function($){
    $(document).ready(function(){
      $("form[name='tos']").formRedirect({
        params: formParams,
        useTimer: true,
        redirectTimer: 2
      });
    });
  })(jQuery);
  </script>

  {include="footer"}
</div>
</body>
</html>
