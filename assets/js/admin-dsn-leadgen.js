/* Admin JS for DSN LeadGen meta box - uses WP media frames */
(function($){
  'use strict';

  var frame;

  function openMediaFrame(target){
    // Always create a fresh media frame so the closure captures the current target
    if ( frame ) {
      frame.close();
    }

    frame = wp.media({
      title: 'Select media',
      button: { text: 'Select' },
      multiple: false
    });

    frame.on('select', function(){
      var attachment = frame.state().get('selection').first().toJSON();
      if ( ! attachment ) { return; }
      // target: logo | image | media
      var prefix = 'dsn_lgp_' + target;
      // set ID and URL inputs
      $('#' + prefix + '_id').val( attachment.id );
      $('#' + prefix + '_url').val( attachment.url ).trigger('change');
      // set preview image
      var preview = $('#' + prefix + '_preview');
      if ( preview.length ) {
        preview.attr('src', attachment.url).show();
      }
      // show remove
      $('.dsn-remove-button[data-target="' + target + '"]').show();
    });

    frame.open();
  }

  $(document).ready(function(){
    // Toggle visibility of image/video sections based on selected media type
    function toggleMediaSections(){
      var selected = $('input[name="dsn_lgp_media_type"]:checked').val() || 'image';
      if ( selected === 'image' ){
        $('.dsn-media-section--image').show();
        $('.dsn-media-section--video').hide();
      } else {
        $('.dsn-media-section--image').hide();
        $('.dsn-media-section--video').show();
      }
    }

    // initial toggle on load
    toggleMediaSections();

    // on change
    $(document).on('change', 'input[name="dsn_lgp_media_type"]', function(){
      toggleMediaSections();
    });

    $(document).on('click', '.dsn-upload-button', function(e){
      e.preventDefault();
      var target = $(this).data('target');
      openMediaFrame(target);
    });

    $(document).on('click', '.dsn-remove-button', function(e){
      e.preventDefault();
      var target = $(this).data('target');
      var prefix = 'dsn_lgp_' + target;
      $('#' + prefix + '_id').val('');
      $('#' + prefix + '_url').val('').trigger('change');
      $('#' + prefix + '_preview').attr('src','').hide();
      $(this).hide();
    });

    // If URL input changes manually, ensure preview updates
    $(document).on('change', 'input[id$="_url"]', function(){
      var id = $(this).attr('id');
      var target = id.replace('_url','');
      var preview = $('#' + target + '_preview');
      var val = $(this).val();
      if ( val ) {
        preview.attr('src', val).show();
        $('.dsn-remove-button[data-target="' + target.replace('dsn_lgp_','') + '"]').show();
      } else {
        preview.attr('src','').hide();
      }
    });
  });

})(jQuery);
