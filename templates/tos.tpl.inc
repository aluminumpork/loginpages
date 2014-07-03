<!DOCTYPE html>
<html lang="en">
<head>
  {include="common"}
  <script src="{$asset_path}/form-redirect.js"></script>
  <title>Terms of service</title>
</head>
<body class="tos">
  <div>
    <header>
      <div><img src="{$asset_path}/{$logo_small_src}" alt="Logo for {$property_name}" /></div>
      <h2>{$property_name}</h2>
      <h3>Terms of Service</h3>
      <div style="clear: both"></div>
    </header>
    <section>
      <div>{include="tos-text"}</div>
      <form name="tos" method="GET">
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

  <script>
    var formParams = {$json_redirect_params};
    ;(function($){
      $(document).ready(function(){
        $("form[name='tos']").formRedirect({
          params: formParams,
          useTimer: false
        });
      });
    })(jQuery);
  </script>

</body>
</html>