(function ($) {
  'use strict';
  $(function () {
    var frame;
    $('#ca-author-image-upload').on('click', function (event) {
      event.preventDefault();
      if (frame) { frame.open(); return; }
      frame = wp.media({
        title: 'Choose an author photo',
        button: { text: 'Use this photo' },
        library: { type: 'image' },
        multiple: false
      });
      frame.on('select', function () {
        var attachment = frame.state().get('selection').first().toJSON();
        var source = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
        $('#ca_author_image_id').val(attachment.id);
        $('#ca-author-image-preview').html($('<img>', { src: source, alt: '' }));
        $('#ca-author-image-remove').prop('disabled', false);
      });
      frame.open();
    });
    $('#ca-author-image-remove').on('click', function (event) {
      event.preventDefault();
      $('#ca_author_image_id').val('');
      $('#ca-author-image-preview').empty();
      $(this).prop('disabled', true);
    });
  });
})(jQuery);
