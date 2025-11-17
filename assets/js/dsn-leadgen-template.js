// Minimal front-end JS for DSN LeadGen Template
// Kept intentionally tiny — available for small DOM tweaks if needed later.

(function(){
  'use strict';

  document.addEventListener('DOMContentLoaded', function(){
    // Ensure custom checkbox/radio visuals toggle correctly across GF markup variants.
    var wrapper = document.getElementById('dsn-leadgen-wrapper');
    if ( ! wrapper ) { return; }

    function findChoiceContainer(el){
      // Look for common GF choice container patterns
      return el.closest('li') || el.closest('.gchoice') || el.closest('.gfield_choice') || el.closest('label') || el.parentElement;
    }

    function initChoices(){
      var inputs = wrapper.querySelectorAll('input[type="checkbox"], input[type="radio"]');
      inputs.forEach(function(input){
        var container = findChoiceContainer(input);
        function update(){
          if ( ! container ) { return; }
          if ( input.type === 'radio' ) {
            if ( input.checked ) {
              // remove checked class from other radios in the same group
              try {
                var groupName = input.name;
                var group = wrapper.querySelectorAll('input[type="radio"][name="' + groupName.replace(/"/g, '\\"') + '"]');
                group.forEach(function(r){
                  var c = findChoiceContainer(r);
                  if ( c && c !== container ) { c.classList.remove('dsn-checked'); }
                });
              } catch (e) {
                // fallback: ignore
              }
              container.classList.add('dsn-checked');
            } else {
              container.classList.remove('dsn-checked');
            }
          } else {
            if ( input.checked ) { container.classList.add('dsn-checked'); } else { container.classList.remove('dsn-checked'); }
          }
        }
        input.addEventListener('change', update);
        // Allow radio buttons to be deselected by clicking the selected option again.
        if ( input.type === 'radio' ) {
          // record state before click
          input.addEventListener('mousedown', function(){
            input.dataset.wasChecked = input.checked ? 'true' : 'false';
          });
          input.addEventListener('click', function(e){
            if ( input.dataset.wasChecked === 'true' ) {
              // if it was already checked, uncheck and fire change
              e.preventDefault();
              input.checked = false;
              var ev = new Event('change', { bubbles: true });
              input.dispatchEvent(ev);
            }
          });
        }
        // Also handle clicks on labels (some GF markup toggles input before change event)
        var label = container && (container.querySelector('label') || container.querySelector('span.choice_label'));
        if ( label ) {
          // ensure visuals update after normal clicks
          label.addEventListener('click', function(){ setTimeout(update, 10); });
          // record state on mousedown so we know if the user clicked the already-selected radio
          label.addEventListener('mousedown', function(){ label.dataset.wasChecked = input.checked ? 'true' : 'false'; });
          // if the user clicks the already-checked radio's label, deselect it
          label.addEventListener('click', function(e){
            if ( input.type === 'radio' && label.dataset.wasChecked === 'true' ) {
              e.preventDefault();
              input.checked = false;
              var ev = new Event('change', { bubbles: true });
              input.dispatchEvent(ev);
            }
          });
        }
        // initialize
        update();
      });
    }

    // Run once on load and also when GF may update the form (basic fallback)
    initChoices();

    // If Gravity Forms updates fields via AJAX, try to re-init after a short delay
    document.addEventListener('gform_post_render', function(){ setTimeout(initChoices, 30); });
  });

})();
