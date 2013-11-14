<!DOCTYPE html>
<html lang="en">
<head>
  {include="common"}
  <script src="{$asset_path}/form-redirect.js"></script>
  <title>Terms of service</title>
</head>
<body class="tos" style="display:none">
  <div>
    <header>
      <div><img src="{$asset_path}/{$logo_small_src}" alt="Logo for {$property_name}" /></div>
      <h2>{$property_name}</h2>
      <h3>Terms of Service</h3>
      <div style="clear: both"></div>
    </header>
    <section>
      <div>{include="tos-text"}</div>
      {if condition="$input_access_code == false"}
      <form name="tos" method="POST" action="{$link_login}">
        <input type="hidden" name="password" value="freeaccess" />
      {else}
      <form name="tos" method="GET">
      {/if}
        <input type="hidden" name="username" value="{$access_code_user}" />
        <input type="hidden" name="domain" value="" />
        <div>
          <p>By clicking "I agree", you are hereby agreeing to use this Internet service within the guidelines above.</p>
          <input type="submit" value="I agree" />
        </div>
      </form>
    </section>
    {include="footer"}
  </div>

  {if condition="$input_access_code == true"}
  <script>
    var formParams = {$json_redirect_params};
    ;(function($){
      $(document).ready(function(){
        var timerEnabled = false;
        var timeout = 0;
        {if condition="$use_tos == false"}
          timerEnabled = true;
          timeout = 1;
        {else}
          $('body').show();
        {/if}

        formParams.page = 'login';
        $("form[name='tos']").formRedirect({
          params: formParams,
          useTimer: timerEnabled,
          redirectTimer: timeout
        });
      });
    })(jQuery);
  </script>
  {/if}

</body>
</html>