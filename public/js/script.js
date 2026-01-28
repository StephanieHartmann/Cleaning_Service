document.addEventListener("DOMContentLoaded", function() {

    const creatForm = document.querySelector('form[action="create.php]');
    if(creatForm) {
        creatForm.addEventListener("submit", function(event) {
            const name = document.querySelector('input[name="customer_name"]').value;
            const size = document.querySelector('input[name="size_sqm"]').value;

            if (name.trim() === "") {
                alert ("Please enter the customer name.");
                event.preventDefault();
                return;
            }

            if (size < 1) {
                alert("Square meters must be a positive number.");
                event.preventDefault();
                return;
            }
        });
    }

    const workForm = document.querySelector('input[name="hours_worked')?.closest('form');
    if (workForm) {
        workForm.addEventListener("submit", function(event) {
            const hours = document.querySelector('input[name="hours_worked"]').value;

            if (hours <= 0) {
                alert("Hours worked must be greater than 0.");
                event.preventDefault();
            }
        });
    }

    console.log("Cleaning Service App Loaded correctly.");
});