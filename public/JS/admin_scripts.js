document.addEventListener("DOMContentLoaded", function () {
    // Confirm deletion
    const deleteLinks = document.querySelectorAll(".delete");
    deleteLinks.forEach(link => {
        link.addEventListener("click", function (event) {
            if (!confirm("Are you sure you want to delete this item?")) {
                event.preventDefault();
            }
        });
    });

    // Filter courses
    const filterButton = document.getElementById("filter-button");
    if (filterButton) {
        filterButton.addEventListener("click", function () {
            const statusFilter = document.getElementById("status-filter").value;
            const priceFilter = document.getElementById("price-filter").value;
            
            const rows = document.querySelectorAll(".course-row");
            rows.forEach(row => {
                const status = row.getAttribute("data-status");
                const price = row.getAttribute("data-price");
                
                if ((statusFilter === "all" || status === statusFilter) &&
                    (priceFilter === "all" || price === priceFilter)) {
                    row.style.display = "table-row";
                } else {
                    row.style.display = "none";
                }
            });
        });
    }
});
