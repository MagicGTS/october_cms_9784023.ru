
// Configure bluebird
jQuery(function() {
    Promise.config({
        cancellation: true
    });

    Queue.configure(Promise);
});
