jQuery(document).ready(function($) {
    $('.accordian-head').on('click', function() {
        /**
         * if selected accordian item is not collapsed, collapse
         * else, expand selected accordian item
         * regardless, hide awll accordian items
         */
        if(!$(this).siblings('.accordian-row').hasClass('hidden-row')) {
            $('.accordian-list-item > .accordian-row').addClass('hidden-row');
            $('.accordian-button-toggle').html('+');
            $(this).siblings('.accordian-row').addClass('hidden-row');
            $(this).find('.accordian-button-toggle').html('+');

        } else {
            $('.accordian-list-item > .accordian-row').addClass('hidden-row');
            $('.accordian-button-toggle').html('+');
            $(this).siblings('.accordian-row').removeClass('hidden-row');
            $(this).find('.accordian-button-toggle').html('-');
        }
    });
});

