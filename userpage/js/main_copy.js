$(function(){
    $("#form-total").steps({
        headerTag: "h2",
        bodyTag: "section",
        transitionEffect: "fade",
        enableAllSteps: true,
        stepsOrientation: "vertical",
        autoFocus: true,
        transitionEffectSpeed: 500,
        titleTemplate : '<div class="title">#title#</div>',
        labels: {
            previous : 'Back Step',
            next : '<i class="zmdi zmdi-arrow-right"></i>',
            finish : '<i class="zmdi zmdi-check"></i>',
            current : ''
        },
    });

    
});

document.addEventListener('DOMContentLoaded', function() {
    var workStatusSelect = document.getElementById('work-status');
    var defaultForm = document.getElementById('default-form');
    var employedForm = document.getElementById('employed-form');

    workStatusSelect.addEventListener('change', function() {
        if (workStatusSelect.value === 'Employed') {
            defaultForm.classList.add('hide');
            employedForm.classList.remove('hide');
        } else {
            defaultForm.classList.remove('hide');
            employedForm.classList.add('hide');
        }
    });
});