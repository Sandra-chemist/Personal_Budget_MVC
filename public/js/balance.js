const rangeChooser = document.querySelector('select[name="period"]');
const startDate = document.querySelector('input[name="startDate"]');
const startDateLabel = document.querySelector('label[for="startDate"]');
const endDate = document.querySelector('input[name="endDate"]');
const endDateLabel = document.querySelector('label[for="endDate"]');

rangeChooser.addEventListener("click", function(){
    if (rangeChooser.value === 'customPeriod'){
        startDate.classList.remove('hidden');
        startDate.required = true;
        startDateLabel.classList.remove('hidden');
        startDateLabel.required = true;
        endDate.classList.remove('hidden');
        endDate.required = true;
    } else{
        startDate.classList.add('hidden');
        startDate.required = false;
        startDateLabel.classList.add('hidden');
        startDateLabel.required = false;
        endDate.classList.add('hidden');
        endDate.required = false;
    }
});
