"use strict";

document.addEventListener("DOMContentLoaded", function () {
  var clipboard = new ClipboardJS('.btn-clipboard');

  clipboard.on('success', function (e) {
    var b = bootstrap.Tooltip.getInstance(e.trigger);
    e.trigger.setAttribute('data-bs-original-title', 'Copied!'),
      b.show(),
      e.trigger.setAttribute('data-bs-original-title', 'Copy to clipboard'),
      e.clearSelection()
  });

  clipboard.on('error', function (e) {
    var b = /mac/i.test(navigator.userAgent) ? "\u2318" : "Ctrl-",
      c = "Press " + b + "C to copy",
      d = bootstrap.Tooltip.getInstance(e.trigger);
    e.trigger.setAttribute("data-bs-original-title", c),
      d.show(),
      e.trigger.setAttribute("data-bs-original-title", "Copy to clipboard");
  });

  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl, { container: 'body' });
  });

  $('.custom-modal-display').on('hidden.bs.modal', function () {
    $('.custom-modal-display').children(".modal-dialog").removeClass("modal-lg modal-sm modal-xl");
    $('.custom-modal-content').empty();
  })
});
