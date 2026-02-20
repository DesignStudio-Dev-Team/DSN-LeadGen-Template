// DSN LeadGen Template — front-end JS
// Custom checkbox/radio visuals + scroll-to-form on GF multi-page navigation.

(function($){
  'use strict';

  $(document).ready(function(){
    var wrapper = document.getElementById('dsn-leadgen-wrapper');
    if ( ! wrapper ) { return; }

    function findChoiceContainer(el){
      return el.closest('.gchoice') || el.closest('.gfield_choice') || el.closest('li') || el.closest('label') || el.parentElement;
    }

    function initChoices(){
      var inputs = wrapper.querySelectorAll('input[type="checkbox"], input[type="radio"]');
      inputs.forEach(function(input){
        if ( input._dsnBound ) { return; }
        input._dsnBound = true;

        var container = findChoiceContainer(input);

        function update(){
          if ( ! container ) { return; }
          if ( input.type === 'radio' ) {
            if ( input.checked ) {
              try {
                var group = wrapper.querySelectorAll('input[type="radio"][name="' + CSS.escape(input.name) + '"]');
                group.forEach(function(r){
                  var c = findChoiceContainer(r);
                  if ( c && c !== container ) { c.classList.remove('dsn-checked'); }
                });
              } catch(e){}
              container.classList.add('dsn-checked');
            } else {
              container.classList.remove('dsn-checked');
            }
          } else {
            container.classList[ input.checked ? 'add' : 'remove' ]('dsn-checked');
          }
        }

        input.addEventListener('change', update);

        // Allow radio to be deselected by clicking the already-selected option
        if ( input.type === 'radio' ) {
          input.addEventListener('mousedown', function(){ input.dataset.wasChecked = input.checked ? 'true' : 'false'; });
          input.addEventListener('click', function(e){
            if ( input.dataset.wasChecked === 'true' ) {
              e.preventDefault();
              input.checked = false;
              input.dispatchEvent(new Event('change', { bubbles: true }));
            }
          });
        }

        // Keep visuals in sync when label is clicked too
        var label = container && (container.querySelector('label') || (container.tagName === 'LABEL' ? container : null));
        if ( label ) {
          label.addEventListener('mousedown', function(){ label.dataset.wasChecked = input.checked ? 'true' : 'false'; });
          label.addEventListener('click', function(){
            setTimeout(update, 10);
            if ( input.type === 'radio' && label.dataset.wasChecked === 'true' ) {
              input.checked = false;
              input.dispatchEvent(new Event('change', { bubbles: true }));
            }
          });
        }

        update();
      });
    }

    function scrollToForm(){
      var top = wrapper.getBoundingClientRect().top + window.pageYOffset - 20;
      window.scrollTo({ top: top, behavior: 'smooth' });
    }

    // Run once on initial load
    initChoices();

    // ── AJAX multi-page navigation ───────────────────────────────────────────
    // GF fires gform_post_render via jQuery — MUST use jQuery .on() here,
    // native addEventListener will NOT receive jQuery-triggered events.
    $(document).on('gform_post_render', function(event, formId, currentPage){
      // Re-bind any new inputs injected by GF
      var inputs = wrapper.querySelectorAll('input[type="checkbox"], input[type="radio"]');
      inputs.forEach(function(input){ input._dsnBound = false; });
      setTimeout(initChoices, 50);

      // Scroll to the top of the form on every page transition
      setTimeout(scrollToForm, 100);
    });

    // ── Non-AJAX fallback (full page reload) ─────────────────────────────────
    // Store a flag before form submits so next load knows to scroll.
    $(document).on('click', '.gform_next_button, .gform_previous_button', function(){
      sessionStorage.setItem('dsn_gf_scroll', '1');
    });
    if ( sessionStorage.getItem('dsn_gf_scroll') ) {
      sessionStorage.removeItem('dsn_gf_scroll');
      setTimeout(scrollToForm, 150);
    }

  });

}(jQuery));
