(function($) {
  $(function() {
    /**
     * Handles opening the modal
     */
    var $modalTrigger = $( '.pardotmarketing-modal-trigger' );
    $modalTrigger.click(function( e ) {
      e.preventDefault();

      var id = $(this).data('id');
      $('#pardotmarketing-modal-' + id).addClass( 'is-active' );
    });

    $('.pardotmarketing-modal').click(function(){
      $(this).removeClass('is-active');
    });

    $(".pardotmarketing-modal .pardotmarketing-modal-inside").click(function(e) {
        e.stopPropagation();
    });
  });
})(jQuery);
