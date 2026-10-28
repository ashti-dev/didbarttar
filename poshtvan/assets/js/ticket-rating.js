jQuery(document).ready(function($){
    function showNotice(msg, wrapper)
    {
        let noticeMode = wrapper.attr('notice-mode')
        if(noticeMode == '1') return false
        setTimeout(() => {
            wrapper.attr('notice-mode', '1')
            let tmpValue = wrapper.attr('data-title')
            wrapper.attr('data-title', msg)
            setTimeout(() => {
                wrapper.removeAttr('notice-mode')
                wrapper.attr('data-title', tmpValue)
                wrapper.removeClass('message-mode')
            }, 2000);
        }, 200);
    }

    $(document).on('click', '.ratings[poshtvan-ticket-rating][handle-rating-click="true"] .star-item', function(){
        let el = $(this),
            rateValue = el.attr('rate-value'),
            wrapper = el.closest('[poshtvan-ticket-rating]'),
            ticketID = wrapper.attr('poshtvan-ticket-rating')

        wrapper.addClass('message-mode')
        el.closest('.rating-stars').addClass('loading-mode')
        wrapper.attr('handle-rating-click', 'false')

        $.ajax({
            url: mwtc_rating_data.au,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'poshtvan_ticket_submit_rate',
                nonce: mwtc_rating_data.nonce,
                ticket_id: ticketID,
                rate_value: rateValue
            },
            success: response => {
                if(response.success)
                {
                    wrapper.find('.rating-stars .star-item').removeClass('active-star')
                    wrapper.find('.rating-stars .star-item[rate-value="' + rateValue + '"]').addClass('active-star');
                    el.addClass('item-clicked')
                    setTimeout(() => {
                        el.removeClass('item-clicked')
                    }, 1500);
                }
                showNotice(response.data.msg, wrapper)
            },
            error: err => {
                showNotice(mwtc_rating_data.messages.has_error, wrapper)
            },
            complete: () => {
                setTimeout(() => {
                    el.closest('.rating-stars').removeClass('loading-mode')                        
                    wrapper.attr('handle-rating-click', "true")
                }, 200);
            }
        })
    })
});
