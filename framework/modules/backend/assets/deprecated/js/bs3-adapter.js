//
// BS3 adapter
//

$(document).render(function(){
    $('[data-toggle=dropdown]:not([data-bs-toggle])').attr('data-bs-toggle', 'dropdown');
    $('.fade.in:not(.show)').addClass('show');
});
