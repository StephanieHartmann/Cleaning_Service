
document.addEventListener("DOMContentLoaded", function() {
    
    const searchInput = document.getElementById("searchInput");
    
    const tableBody = document.querySelector("table tbody");
    const rows = tableBody.getElementsByTagName("tr");

    if (searchInput) {
        searchInput.addEventListener("keyup", function() {
            
            const term = searchInput.value.toLowerCase();

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                
                const textContent = row.textContent.toLowerCase();

                if (textContent.includes(term)) {
                    row.style.display = ""; 
                } else {
                    row.style.display = "none"; 
                }
            }
        });
    }
});