document.addEventListener("DOMContentLoaded", function() {
    
    const form = document.querySelector('form');

    if (form) {
        form.addEventListener('submit', function(event) {
            
            const sizeInput = document.querySelector('input[name="size"]');
            
            if (sizeInput) {
                const sizeValue = parseInt(sizeInput.value);
                
                if (sizeValue <= 0) {
                    alert("Error: The size (sqm) must be a positive number greater than 0.");
                    event.preventDefault();
                    return;
                }
            }
        }); 
    }

});