<!DOCTYPE html>
<html lang="en">
<head>
  <title>Please wait while you are redirected...</title>
  {include="common"}
  <link href="{$asset_path}/progress.css" rel="stylesheet" />

  <script src="http://heartcode-canvasloader.googlecode.com/files/heartcode-canvasloader-min-0.9.1.js"></script>
  <script src="{$asset_path}/form-redirect.js"></script>

</head>
<body class="overflow index">
<h1 class="bg">Connecting...</h1>
<div>
  <img src="{$asset_path}/{$logo_large_src}" />
  <section>
    <h2>Welcome to the <wbr />{$property_name}!</h2>
    <h3>Please wait while you are redirected...</h3>
    <progress>Loading</progress>
    <form method="GET" name="redirect">
      <input type="hidden" name="page" value="tos" />
    </form>
  </section>
</div>

<script>
  var formParams = {$json_redirect_params};
  ;(function($){
    $(document).ready(function(){
      $("form[name='redirect']").formRedirect({
        params: formParams
      });
    });
  })(jQuery);
</script>
<script src="{$asset_path}/progress.js"></script>
</body>
</html>