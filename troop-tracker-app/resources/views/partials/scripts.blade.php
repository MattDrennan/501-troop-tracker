<script src="https://cdnjs.cloudflare.com/ajax/libs/htmx/2.0.7/htmx.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script type="text/javascript">
  /** HTMX CSRF TOKEN **/
  document.addEventListener('htmx:configRequest', function (event) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    event.detail.headers['X-CSRF-TOKEN'] = token;
  });

  /** DATE PICKER **/
  document.addEventListener('DOMContentLoaded', () => {
    function bindDatePickers() {
      document.querySelectorAll(".date-picker").forEach(function (el) {
        options = {};
        flatpickr(el, options);
      });
    }
    bindDatePickers();
    document.body.addEventListener('htmx:afterSettle', bindDatePickers);
  });

  /** DATETIME PICKER **/
  document.addEventListener('DOMContentLoaded', () => {
    function bindDatePickers() {
      document.querySelectorAll(".datetime-picker").forEach(function (el) {
        options = { enableTime: true, };
        flatpickr(el, options);
      });
    }
    bindDatePickers();
    document.body.addEventListener('htmx:afterSettle', bindDatePickers);
  });


  /** SCOPED BUTTON DISABLE FOR HTMX **/
  document.body.addEventListener('htmx:configRequest', function (event) {
    const form = event.detail.elt;
    if (!form || form.tagName !== 'FORM') return;

    // Find the submit button that was clicked
    const activeElement = document.activeElement;
    const button = form.querySelector('button[data-action="htmx-disable"]');

    // Confirm it's the one that triggered the form
    if (button && (button === activeElement || activeElement.form === form)) {
      button.disabled = true;
      button.dataset.originalText = button.innerHTML;
      button.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Transmitting ...';

      const restore = () => {
        button.disabled = false;
        if (button.dataset.originalText) {
          button.innerHTML = button.dataset.originalText;
          delete button.dataset.originalText;
        }
      };

      document.body.addEventListener('htmx:afterRequest', restore, { once: true });
      document.body.addEventListener('htmx:responseError', restore, { once: true });
    }
  });

  /** SCOPED BUTTON DISABLE FOR HTML **/
  document.addEventListener('DOMContentLoaded', () => {  // Attach listener to every form on the page
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', () => {
        const button = form.querySelector('button[type=submit]');
        if (button) {
          button.disabled = true;
          button.dataset.originalText = button.innerHTML;
          button.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Submitting ...';
        }
      });
    });
  });


  /** FLASH AFTER SWAP **/
  document.body.addEventListener('htmx:afterSwap', function (event) {
    try {
      const flashMessageJson = event.detail.xhr.getResponseHeader('X-Flash-Message');
      if (!flashMessageJson) {
        return;
      }
      const response = JSON.parse(flashMessageJson);

      if (response && response.message && response.type) {
        const messagesContainer = document.getElementById('flash-messages');
        if (!messagesContainer) {
          console.error('Flash message container not found.');
          return; // Important: Stop if container is missing
        }

        // Create the alert div
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert alert-${response.type} alert-dismissible fade show mt-2`;

        // Create the close button
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'btn-close float-end';
        closeButton.setAttribute('data-bs-dismiss', 'alert');
        messageDiv.appendChild(closeButton);

        // Create the strong message element
        const strong = document.createElement('strong');
        strong.textContent = response.message;
        messageDiv.appendChild(strong);

        // Append and fade in
        messagesContainer.appendChild(messageDiv);

        requestAnimationFrame(() => {
          messageDiv.style.transition = 'opacity 0.3s ease-in-out';
          messageDiv.style.opacity = '1';
        });

        // Scroll into view if needed
        const rect = messagesContainer.getBoundingClientRect();
        const isOutOfView = rect.top < 0 || rect.bottom > window.innerHeight;
        if (isOutOfView) {
          messagesContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Fade out and remove after 2 seconds
        setTimeout(() => {
          messageDiv.style.opacity = '0';
          messageDiv.style.transition = 'opacity 0.5s ease-in-out';
          setTimeout(() => {
            messageDiv.remove();
          }, 500);
        }, 2000);
      }
    } catch (e) {
      console.error("Error parsing JSON or displaying flash message:", e);
    }
  });

  /** BIND TYPEAHEADS **/
  function bindTypeaheadInputs() {
    document.querySelectorAll('.typeahead').forEach(input => {
      // Prevent rebinding if already initialized
      if (input.dataset.typeaheadBound === 'true') return;
      input.dataset.typeaheadBound = 'true';

      const searchUrl = input.dataset.searchUrl;
      const targetId = input.id;

      const costumes = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
          url: searchUrl + '?query=%QUERY',
          wildcard: '%QUERY'
        }
      });

      $(input).typeahead({
        minLength: 2,
        highlight: true
      }, {
        name: 'costumes',
        display: 'name',
        source: costumes,
        templates: {
          suggestion: function (data) {
            return `<div class="dropdown-item d-flex align-items-center p-2">
                      <i class="bi bi-person-badge me-2 text-secondary"></i>
                      <span class="text-truncate">${data.name}</span>
                    </div>`;
          }
        }
      }).on('typeahead:select', function (e, selection) {
        document.getElementById(targetId).value = selection.id;
        input.form.dispatchEvent(new Event('submit', { bubbles: true }));
      });
    });
  }

  document.addEventListener('DOMContentLoaded', bindTypeaheadInputs);
  document.body.addEventListener('htmx:afterSettle', bindTypeaheadInputs);


  /** PICKER PICKED **/
  document.addEventListener('DOMContentLoaded', () => {
    function bindModalPickers() {
      // Attach to all modals with the class .picker-modal
      document.querySelectorAll('.modal-picker').forEach(function (modalEl) {
        modalEl.addEventListener('click', function (evt) {
          const record = evt.target.closest("[data-property][data-id][data-name]");
          if (!record) return;

          const property = record.dataset.property;
          const id = record.dataset.id;
          const name = record.dataset.name;

          // Find the matching picker container
          const container = document.getElementById(`picker-container-${property}`);
          if (!container) return;

          // Update hidden input
          const hiddenInput = container.querySelector(`input[type="hidden"][name="${property}"]`);
          if (hiddenInput) hiddenInput.value = id;

          // Update text input
          const textInput = container.querySelector(`input[type="text"][name="picker-${property}"]`);
          if (textInput) textInput.value = name;

          // Close the modal
          const modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();
        });
      });
    }
    bindModalPickers();
    document.body.addEventListener('htmx:afterSettle', bindModalPickers);
  });


</script>