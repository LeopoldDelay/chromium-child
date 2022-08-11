
jQuery(document).ready(function($) {
    var data = {
      action: 'my_action',
      security : MyAjax.security,
      whatever: 1234
    };
    $.post(MyAjax.ajaxurl, data, function(response) {
      alert('Got this from the server: ' + response);
    });
  });