const sendMessage = (message) => {
    let botTypingMessageTemplate = $('<div class="messages__item messages__item--typing"><span class="messages__dot"></span><span class="messages__dot"></span><span class="messages__dot"></span></div>');
    let botMessageTemplate = $('<div class="messages__item messages__item--visitor"></div>');
    let inbox = $('#chatbox__messages--container');

    $.ajax({
        type: "POST",
        url: "../../chatbot.php",
        data: {message},
        dataType: "json",

        beforeSend: (xhr) => $(inbox).append(botTypingMessageTemplate),

        success: (response, status, xhr) => {
            $(botTypingMessageTemplate).remove();

            let message = response.message.replace(/\\r\\n/gi, "<br>");

            botMessageTemplate.html(message);
            $(inbox).append(botMessageTemplate);
        },

        error: (xhr, status, error) => {
            $(botTypingMessageTemplate).remove();

            botMessageTemplate.text('An error occured, please try again. ');
            $(inbox).append(botMessageTemplate);
        }
    });
};

// Bind keyup event on chatbox input
$('#chatbox__form').on('input paste', 'select, input', () => {
    let message = $('.chatbox__footer input').val();

    // Check the length of the value of the input
    // if it's empty or just whitespaces
    if(message.length == 0 || !message.trim())
        // Disable the send button
        $('.chatbox__send--footer').prop('disabled', true);
    else
        // Enable the send button
        $('.chatbox__send--footer').removeAttr('disabled');
});

// Bind click event on the send button
$('.chatbox__send--footer').on('click', () => {
    let userMessageTemplate = $('<div class="messages__item messages__item--operator"></div>');
    let inbox = $('#chatbox__messages--container');
    
    let userMessage = $('.chatbox__footer input').val();
    $('.chatbox__footer input').val('');
    // Disable the send button
    $('.chatbox__send--footer').prop('disabled', true);

    userMessageTemplate.text(userMessage);
    $(inbox).append(userMessageTemplate);
    sendMessage(userMessage);
});