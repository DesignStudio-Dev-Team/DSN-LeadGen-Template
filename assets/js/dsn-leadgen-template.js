// Minimal front-end JS for DSN LeadGen Template
// Handles custom checkbox/radio visual state for Gravity Forms multi-page forms.

(function(){
  'use strict';

  document.addEventListener('DOMContentLoaded', function(){
    var wrapper = document.getElementById('dsn-leadgen-wrapper');
    if ( ! wrapper ) { return; }

    function findChoiceContainer(el){
      // Walk up to find the best container: gchoice div, li, or label itself
      return el.closest('.gchoice') || el.closest('.gfield_choice') || el.closest('li') || el.closest('label') || el.parentElement;
    }

    function initChoices(){
      // Grab all inputs inside the wrapper fresh each time (GF replaces DOM on page change)
      var inputs = wrapper.querySelectorAll('input[type="checkbox"], input[type="radio"]');
      inputs.forEach(function(input){
        // Skip if already bound (check a custom flag)
        if ( input._dsnBound ) { return; }
        input._dsnBound = true;

        var container = findChoiceContainer(input);

        function update(){
          if ( ! container ) { return; }
          if ( input.type === 'radio' ) {
            if ( input.checked ) {
              // Remove checked class from sibling radios in the same group
              try {
                var groupName = input.name;
                var group = wrapper.querySelectorAll('input[type="radio"][name="' + CSS.escape(groupName) + '"]');
                group.forEach(function(r){
                  var c = findChoiceContainer(r);
                  if ( c && c !== container ) { c.classList.remove('dsn-checked'); }
                });
              } catch (e) { /* ignore */ }
              container.classList.add('dsn-checked');
            } else {
              container.classList.remove('dsn-checked');
            }
          } else {
            if ( input.checked ) {
              container.classList.add('dsn-checked');
            } else {
              container.classList.remove('dsn-checked');
            }
          }
        }

        input.addEventListener('change', update);

        // Allow radio buttons to be deselected by clicking the already-selected option
        if ( input.type === 'radio' ) {
          input.addEventListener('mousedown', function(){
            input.dataset.wasChecked = input.checked ? 'true' : 'false';
          });
          input.addEventListener('click', function(e){
            if ( input.dataset.wasChecked === 'true' ) {
              e.preventDefault();
              input.checked = false;
              input.dispatchEvent(new Event('change', { bubbles: true }));
            }
          });
        }

        // Also fire update when the label is clicked (GF may re-render label text)
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

        // Set initial state
        update();
      });
    }

    // Run on initial load
    initChoices();

    // Re-run whenever GF rerenders (multi-page navigation, conditional logic, etc.)
    // gform_post_render fires on the document when any GF form or page is rendered/updated.
    document.addEventListener('gform_post_render', function(){
      // Reset _dsnBound flags so new inputs in the rerendered page get bound
      var inputs = wrapper.querySelectorAll('input[type="checkbox"], input[type="radio"]');
      inputs.forEach(function(input){ input._dsnBound = false; });
      setTimeout(initChoices, 50);

      // Scroll to the top of the form wrapper so the user always sees step start
      setTimeout(function(){
        var top = wrapper.getBoundingClientRect().top + window.pageYOffset - 20;
        window.scrollTo({ top: top, behavior: 'smooth' });
      }, 80);
    });
  });

})();
